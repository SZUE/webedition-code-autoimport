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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db_tools.inc.php');

/**this is the abstract super class for DB connections */
abstract class DB_WE_abstract {
	private $retry=false;
	/**"yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)*/
	private $Halt_On_Error = 'no';
	
	/**Set to 1 for automatic mysql_free_result() */
	private $Auto_Free = 0; 
	/* link handles */
	protected $Link_ID = 0;
	/* query handles */
	protected $Query_ID = 0;
	/**true, if first query failed due to some server conditions*/

	/** result array*/
	public $Record = array();
	/** current row number */
	public $Row=0;
	/** current error number*/
	public $Errno = 0;
	/**current Error text*/
	public $Error = "";
	/** public: connection parameters */
	public $Database = DB_DATABASE;
	/** 1 to enable Debug messages - static to be enabled from outside for all next queries */
	public static $Debug = 0;

	/** Connects to the database, which this is done by the constructor
	 * 
	 */
	abstract protected function connect($Database, $Host, $User, $Password);
	/** internal query
	 * @param $Query_String string the sql statement
	 * @param $unbuffered bool if this query is executed buffered or unbuffered
	 * @return int a query_id
	 */
	abstract protected function _query($Query_String,$unbuffered);
	/** internal free function*/
	abstract protected function _free();
	/** internal get last error
	 * @return string error
	 */
	abstract protected function error();
	/** internal get last errorno
	 * @return int number
	 */
	abstract protected function errno();
	/** internal seek to a specific position in result-set
	 * @return bool if seek was successfull
	 */
	abstract protected function _seek($pos);
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
	abstract function getInsertId();
	/** get the no of rows in the resultset
	 * @return int row count
	 */
	abstract function num_rows();
	/** get the count of fields in the result-set
	 * @return int no of fields
	 */
	abstract function num_fields();
	/** get the name of the field no
	 * @param $no int field no
	 * @return string orignal name of the field
	 */
	abstract function field_name($no);
	/** get the type of the field no
	 * @param $no int field no
	 * @return string type of the field
	 */
	abstract function field_type($no);
	/** get the name of the table field no is located
	 * @param $no int field no
	 * @return string orignal table location of the field
	 */
	abstract function field_table($no);
	/** get the lenght of the field no
	 * @param $no int field no
	 * @return int length inside the tabledef. of the field
	 */
	abstract function field_len($no);
	/** get the flags of the field no
	 * @param $no int field no
	 * @return int field flags as int
	 */
	abstract public function field_flags($no);
	//abstract function lock($table, $mode = 'write');
	//abstract function unlock();
	/** close the DB connection
	 * 
	 */
	abstract public function close();
	/** get the no of rows that were affected by update/delete/replace ...
	 * @return int count of rows
	 */
	abstract public function affected_rows();
	
	/** get Information about the used driver etc.
	 * @return string containing all information
	 */
	abstract public function getInfo();

	/** Constructor, establishes the connection to the DB
	 * 
	 */
	public function __construct(){
		if($this->connect()){
			// deactivate MySQL strict mode #185
			$this->query('SET SESSION sql_mode=""');
			if (defined('DB_SET_CHARSET') && DB_SET_CHARSET != '') {
				$this->query('SET NAMES "' . DB_SET_CHARSET . '"');
			}
		}
	}

	/**
	 *This function is a replacement for mysql_real_escape_string, which sends the string to mysql to escape it
	 * @deprecated NOTE: this function will be removed; in future there will be a function for prepared statements
	 * @param array/string $inp value to escape for sql-query
	 * @return array/string
	 */
	function escape($inp){
		return escape_sql_query($inp);
	}
	
	/** check the connection
	 *
	 * @return bool true, if the DB is connected
	 */
	function isConnected() {
		return ($this->Link_ID) && $this->ping();
	}

	/**
	 * make an sql-Query to the DB
	 * @param string $Query_String the sql-query
	 * @param bool $allowUnion this parameter is deprecated; it determines if the query is allowed to have unions
	 * @return bool true, if the query was successfull 
	 */
	function query($Query_String, $allowUnion=false, $unbuffered=false) {
		/* No empty queries, please, since PHP4 chokes on them. */
		if ($Query_String == ''){
		/* The empty query string is passed on from the constructor,
		 * when calling the class without a query, e.g. in situations
		 * like these: '$db = new DB_Sql_Subclass;'
		 */
			return false;
		}
		if (!$this->connect())
			return false;
		/* we already complained in connect() about that. */

		// check for union This is the fastest check
		// if union is found in query, then take a closer look
		if ($allowUnion == false && stristr($Query_String,'union')) {
			if (preg_match('/[\s\(`=\)\/]union[\s\(`\/]/i', $Query_String)) {
				$queryToCheck = str_replace("\\\"", '', $Query_String);
				$queryToCheck = str_replace("\\'", '', $queryToCheck);

				$singleQuote = false;
				$doubleQuote = false;

				$queryWithoutStrings = '';

				for ($i = 0; $i < strlen($queryToCheck); $i++) {
					$char = $queryToCheck[$i];
					if ($char == '"' && $doubleQuote == false && $singleQuote == false) {
						$doubleQuote = true;
					} else if ($char == '\'' && $doubleQuote == false && $singleQuote == false) {
						$singleQuote = true;
					} else if ($char == '"' && $doubleQuote == true) {
						$doubleQuote = false;
					} else if ($char == '\'' && $singleQuote == true) {
						$singleQuote = false;
					}
					if ($doubleQuote == false && $singleQuote == false && $char !== '\'' && $char !== '"') {
						$queryWithoutStrings .= $char;
					}
				}

				if (preg_match('/[\s\(`"\'\\/)]union[\s\(`\/]/i', $queryWithoutStrings)) {
					exit('Bad SQL statement! For security reasons, the UNION operator is not allowed within SQL statements per default! You need to set the second parameter of the query function to true if you want to use the UNION operator!');
				}
			}
		}

		# New query, discard previous result.
		if ($this->Query_ID){
			$this->free();
		}
		if (self::$Debug){
			printf("Debug: query = %s<br/>\n", $Query_String);
		}
		$this->Query_ID = $this->_query($Query_String);

		if (!$this->Query_ID && preg_match('/alter table|drop table/i', $Query_String)) {
			$this->_query('FLUSH TABLES');
			$this->Query_ID = $this->_query($Query_String);
		} else
		if (preg_match('/insert |update|replace /i', $Query_String)) {
			// delete getHash DB Cache
			getHash('',$this);
		}
		$this->Errno=0;
		$this->Error='';
		$this->Row = 0;
		if (!$this->Query_ID) {
			$this->Errno = $this->errno();
			$this->Error = $this->error();

			switch($this->Errno){
				case 2006://SERVER_GONE_ERROR
				case 2013://SERVER_LOST
					if(!$this->retry){
						$this->retry=true;
						$this->Link_ID=0;
						$tmp=$this->query($Query_String, $allowUnion);
						$this->retry=false;
						return $tmp;
					}
				default:
					trigger_error('MYSQL-ERROR'."\n".'Fehler: ' . $this->Errno ."\n". 'Detail: ' . $this->Error ."\n". 'Query: ' . $Query_String . "\n", E_USER_WARNING);
					if (defined('WE_SQL_DEBUG') && WE_SQL_DEBUG == 1) {
						error_log('MYSQL-ERROR - Fehler: ' . $this->Errno . ' Detail: ' . $this->Error . ' Query: ' . $Query_String);
					}
					$this->halt('Invalid SQL: ' . $Query_String);
			}

		}

		# Will return nada if it fails. That's fine.
		return (bool)$this->Query_ID;
	}

	/* discard the query result */
	public function free() {
		$this->_free();
		$this->Query_ID = 0;
	}

	/** shorthand notation for num_rows*/
	public function nf() {
		return $this->num_rows();
	}

	/** shorthand for print num_rows
	 */
	public function np() {
		print $this->num_rows();
	}

	/**
	 * get a value from a field, queried by a prequel query+next_record
	 * @param mixed $Name name/number of the field, depending on query-type
	 * @return mixed returns the value or '' if not present
	 */
	public function f($Name) {
		return isset($this->Record[$Name]) ? $this->Record[$Name] : "";
	}

	/**
	 * directly print the value from a field, queried by a prequel query+next_record
	 * @param type $Name name/number of the field, depending on query-type
	 * 
	 */
	public function p($Name) {
		print $this->Record[$Name];
	}

	/**
	 * get an array with all field names from a previous query
	 * @return array in order of the query, all fields are added with their name, having one name per row
	 */
	function fieldNames(){
		$res = array();
		if(!($this->Query_ID)){
			return $res;
		}
		$count = $this->num_fields();
		for ($i = 0; $i < $count; $i++) {
			$res[$i] = $this->field_name($i);
		}
		return $res;
	}

	/**
	 * get all tables named (like)
	 * @param string $like if given, make a like query, without any %
	 * @return array all tables (named like $like)
	 */
	function table_names($like = '') {
		$this->query('SHOW TABLES' . (($like != '') ? ' LIKE "' . $like . '"' : ''));
		$return = array();
		while ($this->next_record()) {
			$return[]["table_name"] = $this->f(0);
			$return[]["tablespace_name"] = $this->Database;
			$return[]["database"] = $this->Database;
		}
		return $return;
	}
	
	/** walk result set 
	 * @param $resultType int
	 * @return bool true, if rows was successfully fetched
	 */
	public function next_record($resultType = MYSQL_BOTH) {

		if (!($this->Query_ID)) {
			$this->halt("next_record called with no query pending.");
			return false;
		}
		$this->Record = $this->fetch_array($resultType);
		$this->Row ++;
		$this->Errno = $this->errno();
		$this->Error = $this->error();

		$stat = is_array($this->Record);
		if (!$stat && $this->Auto_Free) {
			$this->free();
		}
		return $stat;
	}

	/** get result at positionset 
	 * @param $pos int position in the result set
	 * @param $resultType int
	 * @return bool true, if rows was successfully fetched
	 */
	public function record($pos = 0, $resultType = MYSQL_BOTH) {
		if (!$this->seek($pos)) {
			return false;
		}

		return $this->next_record($resultType);
		}
	
	/** set the position in result set 
	 * @param $pos int seek to pos in result set
	 * @return bool true, if seek was successfull
	 */
	public function seek($pos = 0) {
		if (!$this->Query_ID) {
			$this->halt("seek called with no query pending.");
			return false;
		}
		if ($this->_seek($pos)){
			$this->Row = $pos;
			return true;
		}else {
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
	public function getAll($resultType = MYSQL_ASSOC) {
		$ret = array();
		while ($this->next_record($resultType)) {
			$ret[] = $this->Record;
		}
		return $ret;
	}

	/* public: return table metadata */
	public function metadata($table = '', $full = false) {
		$res = array();

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
		*   Test:  if (isset($result['meta']['myfield'])) { ...
		*/

		// if no $table specified, assume that we are working with a query
		// result
		if ($table) {
			if ($this->query('SELECT * FROM `'.$table.'` WHERE FALSE;'))
				$this->halt("Metadata query failed.");
		} else {
			if (!($this->Query_ID))
				$this->halt("No query specified.");
		}
		$count = $this->num_fields();

			for ($i = 0; $i < $count; $i++) {
				$res[$i]["table"] = $this->field_table($i);
				$res[$i]["name"] = $this->field_name($i);
				$res[$i]["type"] = $this->field_type($i);
				$res[$i]["len"] = $this->field_len($i);
				$res[$i]["flags"] = $this->field_flags($i);
			}
		if ($full) {
			$res["num_fields"] = $count;
			for ($i = 0; $i < $count; $i++) {
				$res["meta"][$res[$i]["name"]] = $i;
			}
		}

		$this->free();
		return $res;
	}

	/** eventually print the message and stop further execution, depending on "Halt_On_Error"
	 * @param string $msg message to be printed
	 */
	protected function halt($msg) {
		$this->Error = $this->error();
		$this->Errno = $this->errno();
		switch($this->Halt_On_Error){
			case 'no':
				return;
			case 'report':
				$this->haltmsg($msg);
				return;
			default:
				$this->haltmsg($msg);
				die("Session halted.");
		}
	}

	/**
	 * print a message with the query error
	 * @param string $msg message to be printed
	 */
	protected function haltmsg($msg) {
		printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
		printf("<b>MySQL Error</b>: %s (%s)<br>\n", $this->Errno, $this->Error);
	}
}

// Database wrapper class of webEdition
if (!defined('DB_CONNECT')){
	define('DB_CONNECT','-1');
}
switch(DB_CONNECT){
	case 'connect':
	case 'pconnect':
		include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/db_mysql.inc.php');
		break;
	case 'mysqli_connect':
	case 'mysqli_pconnect':
		include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/db_mysqli.inc.php');
		if(!defined('MYSQL_BOTH')) define('MYSQL_BOTH',MYSQLI_BOTH);
		if(!defined('MYSQL_ASSOC')) define('MYSQL_ASSOC',MYSQLI_ASSOC);
		if(!defined('MYSQL_NUM')) define('MYSQL_NUM',MYSQLI_NUM);
		break;
	default:
		echo 'unknown DB connection type "'.DB_CONNECT."\"\n";
		die('unknown DB connection type');
}


/* Create a global instance of the concrete DB-Class (mysql/mysqli) */

if(!isset($GLOBALS['DB_WE'])){
	$GLOBALS['DB_WE'] = new DB_WE();
}