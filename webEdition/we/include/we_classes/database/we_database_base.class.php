<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package database
 * @internal
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/* * this is the abstract super class for DB connections */

if(!defined('DB_DATABASE')){
	require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf.inc.php');
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db_tools.inc.php');
}

abstract class we_database_base{
	private static $pool = []; //fixme: don't repool temporary tables - they require the same connection
	protected static $conCount = 0;
	protected static $linkCount = 0;
	//states if we have lost connection and try again
	private $retry = false;
	/* link handles */
	protected $Link_ID = 0;
	/* query handles */
	protected $Query_ID = 0;
	private $Insert_ID = 0;
	private $Affected_Rows = 0;
	/*	 * true, if first query failed due to some server conditions */

	/** result array */
	public $Record = [];

	/** current row number */
	public $Row = 0;

	/** current error number */
	public $Errno = 0;
	/*	 * current Error text */
	public $Error = "";

	/* true, if a temporary table was created */
	private $hasTempTable = false;

	/** public: connection parameters */
	protected $Database = DB_DATABASE;
	private static $Trigger_cnt = 0;

	/** Connects to the database, which this is done by the constructor
	 *
	 */
	abstract protected function connect($Database = DB_DATABASE, $Host = DB_HOST, $User = DB_USER, $Password = DB_PASSWORD);

	/** internal query
	 * @param $Query_String string the sql statement
	 * @param $unbuffered bool if this query is executed buffered or unbuffered
	 * @return int a query_id
	 */
	abstract protected function _query($Query_String, $unbuffered = false);

	/** set charset for the session
	 */
	abstract protected function _setCharset($charset);

	/** internal free function */
	abstract protected function _free();

	/** internal get last error
	 * @return string error
	 */
	abstract protected function error();

	/** internal get last errorno
	 * @return int number
	 */
	abstract protected function errno();

	/** get info of latest executed query
	 * @return String
	 */
	abstract protected function info();

	/** internal seek to a specific position in result-set
	 * @return bool if seek was successfull
	 */
	abstract protected function _seek($pos = 0);

	/** internal fetch the data from DB
	 * @param $resultType int (MYSQL_BOTH,MYSQL_ASSOC,MYSQL_NUM)
	 * @return array depending on resulttype
	 */
	abstract protected function fetch_array($resultType);

	/** internal check if Database is still responding
	 * @return true if DB is still present
	 */
	abstract protected function ping();

	/** get the last inserted ID
	 * @return int last generated id of the insert-statement
	 */
	abstract protected function _getInsertId();

	/** get the no of rows in the resultset
	 * @return int row count
	 */
	abstract public function num_rows();

	/** get the count of fields in the result-set
	 * @return int no of fields
	 */
	abstract public function num_fields();

	/** get the name of the field no
	 * @param $no int field no
	 * @return string orignal name of the field
	 */
	abstract public function field_name($no);

	/** get the type of the field no
	 * @param $no int field no
	 * @return string type of the field
	 */
	abstract public function field_type($no);

	/** get the name of the table field no is located
	 * @param $no int field no
	 * @return string orignal table location of the field
	 */
	abstract public function field_table($no);

	/** get the lenght of the field no
	 * @param $no int field no
	 * @return int length inside the tabledef. of the field
	 */
	abstract public function field_len($no);

	/** get the flags of the field no
	 * @param $no int field no
	 * @return int field flags as int
	 */
	abstract public function field_flags($no);

	/** close the DB connection
	 *
	 */
	abstract public function close();

	/** get the no of rows that were affected by update/delete/replace
	 * @return int count of rows
	 */
	abstract protected function _affected_rows();

	/** get Information about the used driver etc.
	 * @return string containing all information
	 */
	abstract public function _getInfo();

	/*	 * returns the charset of the current connection */

	abstract public function _getCurrentCharset();

	/** Constructor, establishes the connection to the DB
	 *
	 */
	public function __construct(){
		//make lazy connections, only the first one is executed instantly
		if(!self::$linkCount){
			$this->_connect();
			//make the first connection reuseable
			$this->repool();
		}
		self::$conCount++;
	}

	/**
	 * Fill connection pool
	 * @internal
	 */
	public function __destruct(){
		$this->free();
	}

	/**
	 * @internal
	 */
	private function repool(){
		if($this->Link_ID && $this->Database == DB_DATABASE && !$this->hasTempTable){
			self::$pool[] = $this->Link_ID;
			$this->Link_ID = 0;
		}
	}

	/**
	 * called on serialize of this class, closes db connection
	 * @internal
	 * @return type empty array
	 */
	public function __sleep(){
		$this->close();
		return [];
	}

	/**
	 * called if this class is unserialized
	 * @internal
	 */
	public function __wakeup(){
		$this->_connect();
	}

	/**
	 * internal connect which uses Connection Pools
	 * @return type
	 */
	protected function _connect(){
		$this->Link_ID = array_pop(self::$pool);
		if(!$this->isConnected(false)){
			self::$linkCount++;
			$this->connect();
		}
		return $this->Link_ID;
	}

	/**
	 * Only for debug
	 */
	public static function showStat(){
		echo 'tried connections: ' . self::$conCount . '<br/>' . 'real connections: ' . self::$linkCount;
	}

	/**
	 * call this function to make sure the connection is setup correctly
	 */
	protected function _setup(){
// deactivate MySQL strict mode; don't use query function (error logging)
		$this->_query('SET SESSION sql_mode="ERROR_FOR_DIVISION_BY_ZERO"'); //NO_ZERO_DATE,NO_ZERO_IN_DATE,
		if(defined('DB_SET_CHARSET') && DB_SET_CHARSET != ''){
			$this->_setCharset(DB_SET_CHARSET);
		}
	}

	/**
	 * This function is a replacement for mysql_real_escape_string, which sends the string to mysql to escape it
	 * @param array/string $inp value to escape for sql-query
	 * @return array/string
	 */
	public function escape($inp){
		return escape_sql_query($inp);
	}

	/** check the connection
	 *
	 * @return bool true, if the DB is connected
	 */
	protected function isConnected($withPing = true){
		return ($this->Link_ID) && ($withPing ? $this->ping() : true);
	}

	/**
	 * make an sql-Query to the DB
	 * @param string $Query_String the sql-query
	 * @param bool $allowUnion this parameter is deprecated; it determines if the query is allowed to have unions
	 * @return bool true, if the query was successfull
	 */
	public function query($Query_String, $allowUnion = false, $unbuffered = false){
		if(!$Query_String){
			return true;
		}
		if(self::$Trigger_cnt){
			$time = microtime(true);
		}
		if(!$this->retry){
			$this->Errno = 0;
			$this->Error = '';
			$this->Row = 0;
		}
# New query, discard previous result.
		if($this->Query_ID){
			$this->free();
		}
		/* No empty queries, please, since PHP4 chokes on them. */
		if(!$this->isConnected(false) && !$this->_connect()){
			return false;
		}

		$repool = false;
// check for union This is the fastest check
// if union is found in query, then take a closer look
		if(!$allowUnion && stristr($Query_String, 'union') || stristr($Query_String, '/*!')){

			$queryToCheck = str_replace(array('\\\\'/* escape for mysql connection */, '\\"', "\\'", '\\\`'), array('', '', '', ''), $Query_String);

			$quotes = array('\'' => false, '"' => false, '`' => false, '/*' => false, '#' => false);

			$queryWithoutStrings = '';

			for($i = 0; $i < strlen($queryToCheck); $i++){
				$char = $queryToCheck[$i];
				$active=array_filter($quotes);
				$active = !empty($active);
				switch($char){
					case '/':
						if(!$active && $queryToCheck[$i + 1] === '*'){
							if($queryToCheck[$i + 2] === '!'){/* mysql specific code */
								if(defined('ERROR_LOG_TABLE')){
									t_e('error', 'No MySQL specific syntax allowed!', $Query_String);
								}
								//be quiet, no need to give more information
								return;
							} else {
								$quotes['/*'] = true;
								$i++;
								continue;
							}
						}
						break;
					case '*':
						if($quotes['/*'] && $queryToCheck[$i + 1] === '/'){//no nested comments
							$quotes['/*'] = false;
							$i++;
							continue;
						}
						break;
					case "\n":
						if($active && $quotes['#']){
							$quotes['#'] = false;
						}
					case '-':
						if(!$active && $queryToCheck[$i + 1] === '-'){
							$quotes['#'] = true;
							$active = true;
							$i++;
							continue;
						}
						break;
					case '#':
						if(!$active){
							$quotes['#'] = true;
							$active = true;
							continue;
						}
						break;
					case '"':
					case '`':
					case '\'':
						if(($active && $quotes[$char]) || !$active){//if active close only corresponding pair
							$quotes[$char] = !$quotes[$char];
							$active = true;
						}
						break;
				}
				if(!$active){
					$queryWithoutStrings .= $char;
				}
			}

			if(!$allowUnion && stristr($queryWithoutStrings, 'union') || stristr($queryWithoutStrings, '/*') || stristr($queryWithoutStrings, '*/')){
				if(defined('ERROR_LOG_TABLE')){
					t_e('error', 'Attempt to execute union statement/injection', $Query_String);
				}
				//be quiet, no need to give more information
				return;
			}
		}
		static $date = 0;
		$date = $date ? $date : date('Y-m-d');

		$this->Insert_ID = 0;
		$this->Affected_Rows = 0;
		$isSelect = stripos($Query_String, 'select') === 0;
		//FIX for current MySQL Versions which do not cache queries with dates
		if($isSelect){
			$Query_String = str_replace(array('CURDATE()', 'CURRENT_DATE()'), '"' . $date . '"', $Query_String);
		}

		$this->Query_ID = $this->_query($Query_String, $unbuffered);
		$this->Errno = $this->errno();
		$this->Error = $this->error();

		if(!$this->Query_ID && preg_match('/alter\stable|drop\stable/i', $Query_String)){
			$this->_query('FLUSH TABLES');
			$this->hasTempTable = false;
			$repool = true;
		} elseif(preg_match('/insert\s|delete\s|update\s|replace\s/i', $Query_String)){
			$this->Insert_ID = $this->_getInsertId();
			$this->Affected_Rows = $this->_affected_rows();
			//don't kill query cache if we add errors
			if(!strpos($Query_String, defined('ERROR_LOG_TABLE') ? ERROR_LOG_TABLE : 'ERROR_LOG_TABLE') || (stripos($Query_String, 'DELETE') === 0)){
// delete getHash DB Cache
				$this->getHash();
			}
			$repool = true;
		} elseif(preg_match('/CREATE TEMPORARY/i', $Query_String)){
			//we have to keep this DB connection with that temp table, since tables are connection dependend
			$this->hasTempTable = true;
			$repool = false;
		}
		$matches = [];
		if(preg_match('/^[[:space:]]*alter[[:space:]]*table[[:space:]]*(`?([[:alpha:]]|[[:punct:]])+`?)[[:space:]]*(add|change|modify|drop)/i', $Query_String, $matches)){
			$this->_query('ANALYZE TABLE `' . $matches[1] . '`');
			$repool = true;
		}
		$this->Row = 0;
		if(self::$Trigger_cnt && (defined('ERROR_LOG_TABLE') && strpos($Query_String, ERROR_LOG_TABLE) === false || !defined('ERROR_LOG_TABLE'))){
			--self::$Trigger_cnt;
			$time = microtime(true) - $time;
			$tmp = array(
				'time' => $time,
				'trigger' => self::$Trigger_cnt,
				'errno' => $this->Errno,
				'error' => $this->Error,
				'affected' => $this->_affected_rows(),
				'rows' => $this->num_rows(),
				'explain' => array()
			);
			if($isSelect){
				$this->Query_ID = $this->_query('EXPLAIN ' . $Query_String);

				while($this->next_record(MYSQL_ASSOC)){
					if(empty($tmp['explain'])){
						$tmp['explain'][] = implode(' | ', array_keys($this->Record));
					}
					$tmp['explain'][] = implode(' | ', $this->Record);
				}
				$this->Row = 0;
				!$this->isConnected(false) && !$this->_connect();
				$this->Query_ID = $this->_query($Query_String, $unbuffered);
			}
			t_e($Query_String, $tmp);
		}

		if(!$this->Query_ID){
			switch($this->Errno){
				case 2006://SERVER_GONE_ERROR
				case 2013://SERVER_LOST
					if(!$this->retry){
						$this->retry = true;
						$this->Link_ID = 0;
						$tmp = $this->query($Query_String, $allowUnion, $unbuffered);
						$this->retry = false;
						return $tmp;
					}
				case 1062://ignore as error - duplicate entry
					return false;
				case 0:// ignore this
					return true;
				default:
					$this->free();
					trigger_error('MYSQL-ERROR' . "\nFehler: " . $this->Errno . "\nDetail: " . $this->Error . "\nInfo:" . $this->info() . "\nQuery: " . $Query_String, E_USER_WARNING);
					if(defined('WE_SQL_DEBUG') && WE_SQL_DEBUG == 1){
						error_log('MYSQL-ERROR - Fehler: ' . $this->Errno . ' Detail: ' . $this->Error . ' Query: ' . $Query_String);
					}
			}
		}

		if($repool){
			$this->repool();
		}

//(bool) entfernt um Kompatibilität mit alten weDevEdge Beispiel herzustellen
		return $this->Query_ID;
	}

	/* discard the query result */

	public function free(){
		$this->_free();
		$this->hasTempTable = false;
		$this->repool();
		$this->Query_ID = 0;
		$this->Record = [];
	}

	/** shorthand notation for num_rows */
	public function nf(){
		return $this->num_fields();
	}

	/** shorthand for print num_rows
	 */
	public function np(){
		echo $this->num_rows();
	}

	/**
	 * get a value from a field, queried by a prequel query+next_record
	 * @param mixed $Name name/number of the field, depending on query-type
	 * @return mixed returns the value or '' if not present
	 */
	public function f($Name){
		return isset($this->Record[$Name]) ? $this->Record[$Name] : '';
	}

	/**
	 * directly print the value from a field, queried by a prequel query+next_record
	 * @param type $Name name/number of the field, depending on query-type
	 *
	 */
	public function p($Name){
		echo $this->Record[$Name];
	}

	/**
	 * get an array with all field names from a previous query
	 * @return array in order of the query, all fields are added with their name, having one name per row
	 */
	public function fieldNames(){
		$res = [];
		if(!($this->Query_ID)){
			return $res;
		}
		$count = $this->num_fields();
		for($i = 0; $i < $count; $i++){
			$res[$i] = $this->field_name($i);
		}
		return $res;
	}

	/**
	 * get all tables named (like)
	 * @param string $like if given, make a like query, without any %
	 * @return array all tables (named like $like)
	 */
	public function table_names($like = ''){
		$this->query('SHOW TABLES' . (($like != '') ? ' LIKE "' . $like . '"' : ''));
		$return = [];
		while($this->next_record()){
			$return[] = array(
				"table_name" => $this->f(0),
				"tablespace_name" => $this->Database,
				"database" => $this->Database);
		}
		return $return;
	}

	/** walk result set
	 * @param $resultType int
	 * @return bool true, if rows was successfully fetched
	 */
	public function next_record($resultType = MYSQL_BOTH){
		if(!($this->Query_ID)){
			$this->halt("next_record called with no query pending.");
			return false;
		}
		$this->Record = $this->fetch_array($resultType);
		$this->Row++;
		$this->Errno = $this->errno();
		$this->Error = $this->error();

		$stat = is_array($this->Record);
		if(!$stat){
			$this->repool();
		}
		return $stat;
	}

	/** get result at positionset
	 * @param $pos int position in the result set
	 * @param $resultType int
	 * @return bool true, if rows was successfully fetched
	 */
	public function record($pos = 0, $resultType = MYSQL_BOTH){
		if(!$this->seek($pos)){
			return false;
		}

		return $this->next_record($resultType);
	}

	/** set the position in result set
	 * @param $pos int seek to pos in result set
	 * @return bool true, if seek was successfull
	 */
	public function seek($pos = 0){
		if(!$this->Query_ID){
			$this->halt('seek called with no query pending.');
			return false;
		}
		if($this->_seek($pos)){
			$this->Row = $pos;
			return true;
		} else {
			$this->halt("seek($pos) failed: result has " . $this->num_rows() . " rows");

			/* half assed attempt to save the day,
			 * but do not consider this documented or even
			 * desireable behaviour.
			 */
			$this->_seek($this->num_rows());
			$this->Row = $this->num_rows();
			return false;
		}
	}

	/**
	 * Get complete result as array
	 * @return array
	 */
	public function getAll($single = false, $resultType = MYSQL_ASSOC){
		$ret = [];
		while($this->next_record($resultType)){
			$ret[] = ($single ? current($this->Record) : $this->Record);
		}
		return $ret;
	}

	public function getAllFirst($useArray = true, $resultType = MYSQL_NUM){
		$ret = [];
		while($this->next_record($resultType)){
			$ret[array_shift($this->Record)] = ($useArray ? $this->Record : current($this->Record));
		}
		return $ret;
	}

	public function getAllq($query, $single = false, $resultType = MYSQL_ASSOC){
		$this->query($query);
		return $this->getAll($single, $resultType);
	}

	public function getAllFirstq($query, $useArray = true, $resultType = MYSQL_NUM){
		$this->query($query);
		return $this->getAllFirst($useArray, $resultType);
	}

	/**
	 * is a handy setter, for executing `a`="\"b\"" set from an assoc array
	 * @param type $arr
	 * @param forValues set to true if used in VALUES (...)
	 */
	static function arraySetter(array $arr, $imp = ',', $forValues = false){
		$ret = [];
		foreach($arr as $key => $val){
			if($key === ''){
				continue;
			}
			$escape = !(is_bool($val));
			if(is_array($val) && sql_function($val)){
				$val = $val['val'];
				$escape = false;
			} elseif(is_object($val) || is_array($val)){
				t_e('warning', 'data error: db-field cannot contain objects / arrays', 'Key: ' . $key, $arr);
			}

			$val = (is_bool($val) ? intval($val) : $val);
			//we must escape int-values since the value might be an enum element
			$ret[] = ($forValues ? '' : '`' . $key . '`=' ) . ($escape ? '"' . escape_sql_query($val) . '"' : $val);
		}
		return ($forValues ? '(' : '') . implode($imp, $ret) . ($forValues ? ')' : '');
	}

	/* public: return table metadata */

	public function metadata($table = '', $full = false){
		$res = [];

		/*
		 * Due to compatibility problems with Table we changed the behavior
		 * of metadata();
		 * depending on $full, metadata returns the following values:
		 *
		 * - full is false (default):
		 * $result[]:
		 *   [0]["table"]  table name
		 *   [0]["name"]   field name
		 *   [0]["type"]   field type
		 *   [0]["len"]    field length
		 *   [0]["flags"]  field flags
		 *
		 * - full is true
		 * $result[]:
		 *   ["num_fields"] number of metadata records
		 *   [0]["table"]  table name
		 *   [0]["name"]   field name
		 *   [0]["type"]   field type
		 *   [0]["len"]    field length
		 *   [0]["flags"]  field flags
		 *   ["meta"][field name]  index of field named "field name"
		 *   The last one is used, if you have a field name, but no index.
		 *   Test:  if (isset($result['meta']['myfield'])) {
		 */

// if no $table specified, assume that we are working with a query
// result
		if($table){
			if(!$this->query('SELECT * FROM `' . $table . '` LIMIT 1')){
				$this->halt('Metadata query failed.');
			}
		} elseif(!($this->Query_ID)){
			$this->halt('No query specified.');
		}
		$count = $this->num_fields();
		if(!$count){
			trigger_error('MYSQL-ERROR' . "\n" . 'Fehler: Metadata-Query on table ' . $table . ' failed' . "\n", E_USER_WARNING);
		}

		for($i = 0; $i < $count; $i++){
			$res[$i] = array(
				'table' => $this->field_table($i),
				'name' => $this->field_name($i),
				'type' => $this->field_type($i),
				'len' => $this->field_len($i),
				'flags' => $this->field_flags($i),
			);
		}
		if($full){
			$res["num_fields"] = $count;
			for($i = 0; $i < $count; $i++){
				$res["meta"][$res[$i]["name"]] = $i;
			}
		}

		$this->free();
		return $res;
	}

	/*	 * checks if this DB connection with this user is allowed to lock a table */

	public function hasLock(){
		static $lock = -1;
		if(is_bool($lock)){
			return $lock;
		}
//lock table
		$this->lock(VALIDATION_SERVICES_TABLE, 'read');
//if lock unavailable this will generate an error 1044 - access denied

		$lock = ($this->errno() == 0);
		$this->unlock();
		return $lock;
	}

	/**
	 * @param $table string,array specify the tables to lock; use numeric array to lock all tables with mode; use named array with [table]=mode to lock specific modes
	 * @param $mode string name the locking mode
	 * @return bool true, on success
	 */
	public function lock($table, $mode = 'write'){
		if(!$this->isConnected() && !$this->_connect()){
			return false;
		}
		if(is_array($table)){
			$query = [];
			foreach($table as $key => $value){
				$query[] = (is_numeric($key) ?
						$value . ' ' . $mode :
						$key . ' ' . $value);
			}
			$query = implode(',', $query);
		} else {
			$query = $table . ' ' . $mode;
		}
//always lock Errlog-Table
		if(strpos($query, ERROR_LOG_TABLE) === FALSE){
			$query.=',' . ERROR_LOG_TABLE . ' write';
		}

		return $this->_query('lock tables ' . $query);
	}

	/** Unlock all locked tables
	 * @return bool true, on success
	 */
	public function unlock(){
		if(!$this->isConnected() && !$this->_connect()){
			return false;
		}
		return $this->_query('unlock tables');
	}

	/**
	 * get full query result
	 * @return array
	 */
	public function getRecord(){
		return $this->Record;
	}

	public function getInsertId(){
		return $this->Insert_ID;
	}

	public function affected_rows(){
		return $this->Affected_Rows;
	}

	/** print the message and stop further execution
	 * @param string $msg message to be printed
	 */
	protected function halt($msg){
		$this->Error = $this->error();
		$this->Errno = $this->errno();
	}

	/**
	 * Check if a colum exists in the specified table
	 * @param type $tab table to check
	 * @param type $col colum to check
	 * @return boolean true if exists
	 */
	public function isColExist($tab, $col){
		if(!$tab || !$col){
			return false;
		}
		$col = trim($col, '`');
		return (bool) count($this->getHash('SHOW COLUMNS FROM ' . $this->escape($tab) . ' LIKE "' . $col . '"'));
	}

	/**
	 * check if a particular table exists
	 * @param type $tab table name
	 * @return boolean true if exists
	 */
	public function isTabExist($tab){
		if(!$tab){
			return false;
		}
		$this->query('SHOW TABLES LIKE "' . $this->escape($tab) . '"');
		return ($this->next_record());
	}

	/**
	 * @param type $tab
	 * @param type $cols
	 * @param array $keys
	 * @return type
	 */
	public function addTable($tab, array $cols, array $keys = [], $engine = 'MYISAM', $temporary = false){
		if(!is_array($cols) || empty($cols)){
			t_e('create table needs an array');
			return;
		}
		if($engine === 'MYISAM'){
			$defaultEngine = f('show variables LIKE "default_storage_engine"', 'Value');
			$engine = (in_array(strtolower($defaultEngine), array('myisam', 'aria')) ? $defaultEngine : 'myisam');
		}
		$cols_sql = [];
		foreach($cols as $name => $type){
			$cols_sql[] = "`" . $name . "` " . $type;
		}
		if($keys){
			foreach($keys as $key){
				$cols_sql[] = $key;
			}
		}

		return $this->query('CREATE ' . ($temporary ? 'TEMPORARY' : '') . ' TABLE ' . $this->escape($tab) . ' (' . implode(',', $cols_sql) . ') ENGINE=' . $engine . ' ' . self::getCharsetCollation());
	}

	/**
	 * delete an table, checks if it exists.
	 * @param type $tab
	 */
	public function delTable($tab, $temporary = false){
		$this->query('DROP ' . ($temporary ? 'TEMPORARY' : '') . ' TABLE IF EXISTS ' . $this->escape($tab));
	}

	public function addCol($tab, $col, $typ, $pos = ''){
		$col = trim($col, '`');
		if($this->isColExist($tab, $col)){
			return false;
		}
		return $this->query('ALTER TABLE ' . $this->escape($tab) . ' ADD `' . $this->escape($col) . '` ' . $typ . (($pos != '') ? ' ' . $pos : ''));
	}

	public function changeColType($tab, $col, $newtyp){
		$col = trim($col, '`');
		if(!$this->isColExist($tab, $col)){
			return false;
		}

		return $this->query('ALTER TABLE ' . $this->escape($tab) . ' CHANGE `' . $col . '` `' . $col . '` ' . $newtyp);
	}

	public function getColTyp($tab, $col){
		return f('SHOW COLUMNS FROM ' . $this->escape($tab) . ' LIKE "' . $col . '"', 'Type', $this);
	}

	/**
	 * delete a column from a table
	 * @param type $tab
	 * @param type $col
	 * @return type
	 */
	public function delCol($tab, $col){
		if(!$this->isColExist($tab, $col)){
			return;
		}
		$this->query('ALTER TABLE ' . $this->escape($tab) . ' DROP `' . trim($col, '`') . '`');
	}

	function getTableCreateArray($tab){
		$this->query('SHOW CREATE TABLE ' . $this->escape($tab));
		return ($this->next_record()) ?
			explode("\n", $this->f("Create Table")) :
			false;
	}

	public function getTableKeyArray($tab){
		$myarray = [];
		$zw = $this->getTableCreateArray($tab);
		if(!$zw){
			return false;
		}
		foreach($zw as $v){
			$vv = trim($v);
			$posP = strpos($vv, 'PRIMARY KEY');
			$posU = strpos($vv, 'UNIQUE KEY');
			$posK = strpos($vv, 'KEY');
			if(($posP !== false && $posP == 0) || ($posU !== false && $posU == 0) || ($posK !== false && $posK == 0)){
				$myarray[] = trim(rtrim($v, ','));
			}
		}
		return $myarray;
	}

	public function getPrimaryKeys($tab, array $create = []){
		$zw = $create ? : $this->getTableCreateArray($tab);
		if(!$zw){
			return false;
		}
		$matches = $mm = [];
		foreach($zw as $v){
			if(preg_match('|PRIMARY KEY\s*\((.*)\)|', $v, $matches)){//be greedy
				$matches = explode(',', $matches[1]);
				foreach($matches as &$m){
					preg_match('|`?([^\(`]*)(\(\d+\))?|', $m, $mm);
					$m = trim($mm[1]);
				}
				return $matches;
			}
		}
		return false;
	}

	/**
	 * checks if a key with a full key definition exists
	 * @param type $tab table to check
	 * @param string $key name
	 * @return boolean true if exists
	 */
	public function isKeyExistAtAll($tab, $key){
		$zw = $this->getTableKeyArray($tab);
		if($zw){
			foreach($zw as $v){
				if(preg_match('|.*KEY *`?' . $key . '`? \(|', $v)){
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * check if a key matches the EXACT definition given by $key
	 * @param string $tab tablename
	 * @param string $key full key definition what is used in a create statement
	 * @return boolean true, if the exact definition is met, false otherwise
	 */
	public function isKeyExist($tab, $name, array $definition, $type = ''){
		$zw = $this->getTableKeyArray($tab);
		if($zw){
			$definition = array_map('preg_quote', $definition);
			foreach($zw as $v){
				if(preg_match('|' . ($type ? $type : '.*KEY') . ' *`?' . preg_quote($name) . '`? \(`?' . implode('`?, ?`?', $definition) . '`?\)|', $v)){
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * add a key/index to a table
	 * @param string $tab tablename
	 * @param string $fullKey full key definition what is used in a create statement
	 */
	public function addKey($tab, $fullKey){
		$this->query('ALTER TABLE ' . $this->escape($tab) . ' ADD ' . $fullKey);
	}

	/**
	 * delete a key/index from a table
	 * @param string $tab tablename
	 * @param string $keyname ONLY the keyname is wanted here
	 */
	public function delKey($tab, $keyname){
		$this->query('ALTER TABLE ' . $this->escape($tab) . ' DROP ' . ($keyname === 'PRIMARY' ? 'PRIMARY KEY' : 'INDEX `' . $keyname . '`'));
	}

	/**
	 * rename a Col to a new Name
	 * @param string $tab tablename
	 * @param string $oldcol old col-name
	 * @param string $newcol new col-name
	 */
	public function renameCol($tab, $oldcol, $newcol){
		if($oldcol == $newcol){
			return;
		}
		$this->query('ALTER TABLE ' . $this->escape($tab) . ' CHANGE `' . $oldcol . '` `' . $newcol . '`');
	}

	/**
	 * move a column to a new position inside the table
	 * @param string $tab tablename
	 * @param string $colName the name of the col to move
	 * @param string $newPos the new position (possible: FIRST, colname)
	 */
	public function moveCol($tab, $colName, $newPos){
		//get the old col def, use for alter table.
		$zw = $this->getTableCreateArray($tab);
		if(!$zw){
			return false;
		}
		$colName = '`' . trim($colName, '`') . '`';
		unset($zw[0]);
		$found = false;
		foreach($zw as $def){
			if(strpos($def, $colName) !== FALSE){
				$found = $def;
				break;
			}
		}
		if($found){
			return $this->query('ALTER TABLE ' . $tab . ' MODIFY ' . $found . ' ' . ($newPos === 'FIRST' ? 'FIRST' : 'AFTER `' . trim($newPos, '`') . '`'));
		}
		return false;
	}

	public static function getCharset(){
		return defined('DB_CHARSET') ? DB_CHARSET : '';
	}

	public static function getCollation(){
		return defined('DB_COLLATION') ? DB_COLLATION : '';
	}

	public static function getCharsetCollation(){
		$Charset = self::getCharset();
		$Collation = self::getCollation();
		return ($Charset && $Collation ? ' CHARACTER SET ' . $Charset . ' COLLATE ' . $Collation : '');
	}

	public static function hasDB(){
		return self::$linkCount > 0;
	}

	public static function t_e_query($cnt = 1){
		self::$Trigger_cnt = $cnt;
	}

	public function getInfo($html = true){
		if(!$this->isConnected() && !$this->_connect()){
			return false;
		}

		$data = $this->_getInfo($html);
		$ret = '';
		$this->repool();
		if($html){
			foreach($data as $key => $val){
				$ret.='<tr><td>' . $key . ':</td><td>' . $val . '</td></tr>';
			}
			$ret = '<table class="defaultfont">' . $ret . '</table>';
		} else {
			foreach($data as $key => $val){
				$ret.=$key . ': ' . $val . "\n";
			}
		}
		return $ret;
	}

	public function getCurrentCharset(){
		if(!$this->isConnected() && !$this->_connect()){
			return false;
		}
		$ret = $this->_getCurrentCharset();
		$this->repool();
		return $ret;
	}

	/**
	 * @internal
	 * @param type $nodots
	 * @return string
	 */
	public static function getMysqlVer(/* $nodots = true */){
		$DB_WE = new DB_WE();
		list($res) = explode('-', f('SELECT VERSION()', '', $DB_WE)? : f('SHOW VARIABLES LIKE "version"', 'Value', $DB_WE));
		return $res;
	}

	/**
	 * get the maximum size a db query could be
	 * @return int
	 */
	public function getMaxAllowedPacket(){
		return f('SHOW VARIABLES LIKE "max_allowed_packet"', 'Value', $this);
	}

	/**
	 * get a line from db & give this line as an array
	 * @param type $query
	 * @param type $resultType
	 * @return array
	 */
	public function getHash($query = '', $resultType = MYSQL_ASSOC){
		static $cache = [];
		if(!$query){
			$cache = [];
			return $cache;
		}
		$hash = $resultType . md5($query, true);
		if(isset($cache[$hash])){
			return $cache[$hash];
		}
		$this->query($query);
		$data = ($this->next_record($resultType) ? $this->Record : []);
		if($data){
			$cache[$hash] = $data;
		}
		$this->free();
		return $data;
	}

}
