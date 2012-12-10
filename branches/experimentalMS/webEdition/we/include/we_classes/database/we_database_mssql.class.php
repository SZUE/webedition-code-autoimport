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
class DB_WE extends we_database_base{

	private $conType = '';

	protected function ping(){
		//return @mysql_ping($this->Link_ID);
		return true;
	}

	/* public: connection management */

	protected function connect($Database = DB_DATABASE, $Host = DB_HOST, $User = DB_USER, $Password = DB_PASSWORD){
		/* establish connection, select database */
		if(!$this->isConnected()){
			switch(DB_CONNECT){
				case 'msconnect':
					$this->Link_ID = @mssql_connect($Host, $User, $Password);
					if(!$this->Link_ID){
						$this->halt("msconnect($Host, $User, $Database) failed.");
						return false;
					}
					$this->conType = 'msconnect';
					break;
				default:
					$this->halt('Error in DB connect MSSQL');
					exit('Error in DB connect');
			}
			if(!@mssql_select_db($Database, $this->Link_ID) &&
				!@mssql_select_db($Database, $this->Link_ID) &&
				!@mssql_select_db($Database, $this->Link_ID) &&
				!@mssql_select_db($Database, $this->Link_ID)){
				$this->halt('cannot use database ' . $this->Database);
				return false;
			}
			if($this->Link_ID){
				$this->_setup();
			}
		}
		return ($this->Link_ID > 0);
	}

	protected function _setCharset($charset){
		//@ mysql_set_charset($charset);  bereits beim connect
	}

	/* public: discard the query result */

	protected function _free(){
		@mssql_free_result($this->Query_ID);
	}

	protected function _query($Query_String, $unbuffered = false){//unbuffered wird wohl nicht unterstützt
		//$in=array('NOW()');
		//$out=array('Getdate()');	
		//$Query_String=str_replace($in,$out,$Query_String);
		if(strpos($Query_String,'INSERT')===0){	
			$Query_String= //'SET NOCOUNT ON;'."\n".
				$Query_String."\n".
				'SELECT @@IDENTITY as LAST_INSERT_ID;'//."\n".
				//'SET NOCOUNT OFF;';
				;
			
			$RR=@mssql_query($Query_String, $this->Link_ID);
			if($RR){
				$row = mssql_fetch_array($RR);
				if(isset($row['LAST_INSERT_ID'])){
					$this->LAST_INSERT_ID = $row['LAST_INSERT_ID'];
				} else {
					$this->LAST_INSERT_ID=0;	
				}
				return $RR;
			} else {
				return false;
			}
		} else {
			$Query_String=str_replace('`','',$Query_String);
			$zw= @mssql_query($Query_String, $this->Link_ID);
			return $zw;
		}
	}

	public function close(){
		if($this->Link_ID){
			@mssql_close($this->Link_ID);
		}
		$this->Link_ID = 0;
	}

	protected function fetch_array($resultType){
		return @mssql_fetch_array($this->Query_ID, $resultType);
	}

	/* public: position in result set */

	protected function _seek($pos = 0){
		return @mssql_data_seek($this->Query_ID, $pos);
	}

	/* public: evaluate the result (size, width) */

	public function affected_rows(){
		return @mssql_rows_affected($this->Link_ID);
	}

	public function num_rows(){
		return @mssql_num_rows($this->Query_ID);
	}

	public function num_fields(){
		if(isset($this->tableInfo)){
			return count($this->tableInfo);
		} 
		return @mssql_num_fields($this->Query_ID);
	}

	public function field_name($no){
		if(isset($this->tableInfo) && isset($this->tableInfo[$no]) ){
			return $this->tableInfo[$no]['Field'];
		}
		return @mssql_field_name($this->Query_ID, $no);
	}

	public function field_type($no){
		if(isset($this->tableInfo) && isset($this->tableInfo[$no]) ){
			return $this->tableInfo[$no]['Type'];
		}
		return @mssql_field_type($this->Query_ID, $no);
	}

	public function field_table($no){
		//return @mysql_field_table($this->Query_ID, $no);
	}

	public function field_len($no){
		if(isset($this->tableInfo) && isset($this->tableInfo[$no]) ){
			return $this->tableInfo[$no]['Len'];
		}
		return mssql_field_length($this->Query_ID, $no);
	}

	public function field_flags($no){
		//return @mysql_field_flags($this->Query_ID, $no);
	}

	public function getInsertId(){
		//return mysql_insert_id($this->Link_ID);
		return $this->LAST_INSERT_ID;
	}

	public function getCurrentCharset(){
		//return mysql_client_encoding();
	}

	public function getInfo(){
		/*
		return '<table class="defaultfont"><tr><td>type:</td><td>' . $this->conType .
			'</td></tr><tr><td>protocol:</td><td>' . mysql_get_proto_info() .
			'</td></tr><tr><td>client:</td><td>' . mysql_get_client_info() .
			'</td></tr><tr><td>host:</td><td>' . mysql_get_host_info() .
			'</td></tr><tr><td>server:</td><td>' . mysql_get_server_info() .
			'</td></tr><tr><td>encoding:</td><td>' . mysql_client_encoding() . '</td></tr></table>';
		*/
	}

	protected function errno(){
		//return $this->Link_ID ? mysql_errno($this->Link_ID) : 2006;
	}

	protected function error(){
		//return $this->Link_ID ? mysql_error($this->Link_ID) : 'no Link to DB';
		return mssql_get_last_message();
	}

	protected function info(){
		//return $this->Link_ID ? mysql_info($this->Link_ID) : 'no Link to DB';
	}
	
	function getInfos($table){
		$this->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$this->escape($table)."';");
		$mydata=array(); 
		while($this->next_record()) {
			$myrow=array(); 
			$myrow['Field']=$this->f("COLUMN_NAME");
			$myrow['Null']=$this->f("IS_NULLABLE");
			if($this->f("DATA_TYPE")=='varchar'){
				$myrow['Type']=$this->f("DATA_TYPE").'('.$this->f("CHARACTER_MAXIMUM_LENGTH").')';
				$myrow['Len']=$this->f("CHARACTER_MAXIMUM_LENGTH");
			} else {
				$myrow['Type']=$this->f("DATA_TYPE");
				if($myrow['Type']=='text'){
					$myrow['Len']=$this->f("CHARACTER_MAXIMUM_LENGTH");
				} else {
					$myrow['Len']='';
				}
			}
			$myrow['Default']=str_replace(array("('","')",'(NULL)'),'',$this->f("COLUMN_DEFAULT"));
			$myrow['Position']=$this->f("ORDINAL_POSITION")-1;
			$mydata[$myrow['Position']]=$myrow;
		}
		$this->tableInfo=$mydata;
		return true;
	}

}
