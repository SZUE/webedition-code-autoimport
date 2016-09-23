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
ini_set('mysqli.reconnect', 1);
!defined('MYSQL_BOTH') && define('MYSQL_BOTH', MYSQLI_BOTH);
!defined('MYSQL_ASSOC') && define('MYSQL_ASSOC', MYSQLI_ASSOC);
!defined('MYSQL_NUM') && define('MYSQL_NUM', MYSQLI_NUM);

class DB_WE extends we_database_base{

	protected function _free(){
		if(is_object($this->Query_ID)){
//			print_r(debug_backtrace());
			@$this->Query_ID->free();
		}
	}

	protected function _query($Query_String, $unbuffered = false){
		$this->_free();
		$tmp = $this->Link_ID->query($Query_String, ($unbuffered ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT));
		if($tmp === false){
			return 0;
		} else if($tmp === true){
			return -1; //!=0 what is tested.
		}
		return $tmp;
	}

	protected function _setCharset($charset){
		$this->Link_ID->set_charset($charset);
	}

	protected function _seek($pos = 0){
		return (is_object($this->Query_ID)) && $this->Query_ID->data_seek($pos);
	}

	protected function errno(){
		return ($this->Link_ID ? $this->Link_ID->errno : 2006);
	}

	protected function error(){
		return ($this->Link_ID ? $this->Link_ID->error : '');
	}

	protected function info(){
		return ($this->Link_ID ? $this->Link_ID->info : '');
	}

	protected function fetch_array($resultType){
		return (is_object($this->Query_ID)) ?
			$this->Query_ID->fetch_array($resultType) :
			false;
	}

	protected function _affected_rows(){
		return $this->Link_ID->affected_rows;
	}

	public function close(){
		if($this->Link_ID){
			$this->Link_ID->close();
			$this->Link_ID = null;
			$this->Query_ID = null;
		}
	}

	protected function connect($Database = DB_DATABASE, $Host = DB_HOST, $User = DB_USER, $Password = DB_PASSWORD){
		$this->Database = $Database;
		if(!$this->isConnected()){
			switch(DB_CONNECT){
				case 'pconnect'://old mysql if
				case 'mysqli_pconnect':
					$Host = 'p:' . $Host;
				case 'connect'://old mysql if
				case 'mysqli_connect':
					$this->Query_ID = null;
					$this->Link_ID = mysqli_init();
					$this->Link_ID->options(MYSQLI_OPT_CONNECT_TIMEOUT, 60);
					if((!@$this->Link_ID->real_connect($Host, $User, $Password, $Database, null, null, MYSQLI_CLIENT_COMPRESS) &&
						!@$this->Link_ID->real_connect($Host, $User, $Password, $Database, null, null, MYSQLI_CLIENT_COMPRESS) &&
						!@$this->Link_ID->real_connect($Host, $User, $Password, $Database, null, null, MYSQLI_CLIENT_COMPRESS)

						) ||
						//need the @ operator, since can't catch mysqli warning on reconnect pconnection
						$this->Link_ID->connect_error){
						$this->Link_ID = null;
						t_e("mysqli_(p)connect($Host, $User) failed.");
						$this->halt("mysqli_(p)connect($Host, $User) failed.");
						return false;
					}
					break;
				default:
					$this->halt('Error in DB connect');
					exit('Error in DB connect');
			}
		}
		$this->_setup();
		return true;
	}

	public function field_flags($no){
		return (is_object($this->Query_ID) ? $this->Query_ID->fetch_field_direct($no)->flags : '');
	}

	public function field_len($no){
		if(is_object($this->Query_ID)){
			$len = $this->Query_ID->fetch_field_direct($no)->length;
			//fix faulty lenght on text-types with connection in utf-8
			$type = $this->Query_ID->fetch_field_direct($no)->type;
			if(DB_SET_CHARSET === 'utf8' && $type >= 252 && $type <= 254){
				$len/=3;
			}
			return $len;
		} else {
			return 0;
		}
	}

	public function field_name($no){
		return (is_object($this->Query_ID) ? $this->Query_ID->fetch_field_direct($no)->name : '');
	}

	public function field_table($no){
		return (is_object($this->Query_ID) ? $this->Query_ID->fetch_field_direct($no)->table : '');
	}

	public function field_type($no){
		switch(is_object($this->Query_ID) ? $this->Query_ID->fetch_field_direct($no)->type : 0){
			case MYSQLI_TYPE_CHAR:
				return 'tinyint';
			case MYSQLI_TYPE_SHORT:
				return 'smallint';
			case MYSQLI_TYPE_LONG:
				return 'int';
			case MYSQLI_TYPE_INT24:
				return 'mediumint';
			case MYSQLI_TYPE_LONGLONG:
				return 'bigint';
			case MYSQLI_TYPE_FLOAT:
				return 'float';
			case MYSQLI_TYPE_DOUBLE:
				return 'double';
			case MYSQLI_TYPE_NULL:
				return 'null';
			case MYSQLI_TYPE_TIMESTAMP:
				return 'timestamp';
			case MYSQLI_TYPE_DATE:
				return 'date';
			case MYSQLI_TYPE_TIME:
				return 'time';
			case MYSQLI_TYPE_DATETIME:
				return 'datetime';
			case MYSQLI_TYPE_YEAR:
				return 'year';
			case MYSQLI_TYPE_NEWDATE:
				return 'date';
			case MYSQLI_TYPE_BIT:
				return 'bit';
			case MYSQLI_TYPE_NEWDECIMAL:
				return 'decimal';
			case MYSQLI_TYPE_ENUM:
				return 'enum';
			case MYSQLI_TYPE_SET:
				return 'set';
			case MYSQLI_TYPE_TINY_BLOB:
				return 'tinyblob';
			case MYSQLI_TYPE_MEDIUM_BLOB:
				return 'mediumblob';
			case MYSQLI_TYPE_BLOB:
				return 'blob';
			case MYSQLI_TYPE_LONG_BLOB:
				return 'longblob';
			case MYSQLI_TYPE_GEOMETRY:
				return 'geometry';
			//252 is currently mapped to all text and blob types (MySQL 5.0.51a)
			case MYSQLI_TYPE_VAR_STRING:
				return ($this->field_flags($no) & MYSQLI_BINARY_FLAG ?
						'varbinary' :
						'varchar');
			case MYSQLI_TYPE_STRING:
				return ($this->field_flags($no) & MYSQLI_BINARY_FLAG ?
						'binary' :
						'char');
			default:
				return '';
		}
	}

	protected function _getInsertId(){
		return $this->Link_ID->insert_id;
	}

	public function num_fields(){
		return $this->Link_ID->field_count;
	}

	public function num_rows(){
		return is_object($this->Query_ID) ? $this->Query_ID->num_rows : 0;
	}

	public function _getCurrentCharset(){
		$charset = mysqli_get_charset($this->Link_ID);
		return $charset->charset;
	}

	public function _getInfo(){
		$charset = mysqli_get_charset($this->Link_ID);
		return ['type' => DB_CONNECT,
			'protocol' => $this->Link_ID->protocol_version,
			'client' => $this->Link_ID->client_info,
			'host' => $this->Link_ID->host_info,
			'server' => $this->Link_ID->server_info,
			'database' => $this->Database,
			'encoding' => $charset->charset
			];
	}

	protected function ping(){
		return $this->Link_ID->ping();
	}

}
