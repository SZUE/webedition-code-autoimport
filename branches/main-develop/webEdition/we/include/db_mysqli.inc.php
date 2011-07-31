<?php

/**
 * webEdition CMS
 *
 * $Rev: 2798 $
 * $Author: mokraemer $
 * $Date: 2011-04-23 04:25:33 +0200 (Sa, 23. Apr 2011) $
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

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db.inc.php');

class DB_WE extends DB_WE_abstract {

	protected function _free() {
		if(is_object($this->Query_ID)){
//			print_r(debug_backtrace());
			$this->Query_ID->free();
		}
		$this->Query_ID = 0;
	}

	protected function _query($Query_String, $unbuffered=false) {
		$this->_free();
		$tmp=@$this->Link_ID->query($Query_String, ($unbuffered? MYSQLI_USE_RESULT:MYSQLI_STORE_RESULT));
		if($tmp===false){
			return 0;
		}else if($tmp===true){
			return -1; //!=0 what is tested.
		}
		return $tmp;
	}

	protected function _setCharset($charset){
		@$this->Link_ID->set_charset($charset);
	}
	protected function _seek($pos=0) {
		return @$this->Query_ID->data_seek($pos);
	}

	protected function errno() {
		return $this->Link_ID->errno;
	}

	protected function error() {
		return $this->Link_ID->error;
	}

	protected function fetch_array($resultType) {
		return @$this->Query_ID->fetch_array($resultType);
	}

	public function getAll($resultType){
		return @$this->Query_ID->fetch_all($resultType);
	}
	
	public function affected_rows() {
		return $this->Link_ID->mysqli_affected_rows;
	}

	public function close() {
		if($this->Link_ID){
			$this->Link_ID->close();
		}
	}

	protected function connect($Database = DB_DATABASE, $Host = DB_HOST, $User = DB_USER, $Password = DB_PASSWORD) {
		if (!$this->isConnected()) {
			switch(DB_CONNECT){
				case 'mysqli_pconnect':
					$Host='p:'.$Host;
				case 'mysqli_connect':
					$this->Link_ID = new mysqli($Host,$User,$Password,$Database);
					if (mysqli_connect_error()){
						$this->Link_ID=0;
						$this->halt("mysqli_(p)connect($Host, $User) failed.");
						return false;
					}
			}
		}
		return true;
	}

	public function field_flags($no) {
		return ($this->Query_ID ? $this->Query_ID->fetch_field_direct($no)->flags:'');
	}

	public function field_len($no) {
		return ($this->Query_ID ? $this->Query_ID->fetch_field_direct($no)->length:0);
	}

	public function field_name($no) {
		return ($this->Query_ID ? $this->Query_ID->fetch_field_direct($no)->orgname:'');
	}

	public function field_table($no) {
		return ($this->Query_ID ? $this->Query_ID->fetch_field_direct($no)->orgtable:'');
	}

	public function field_type($no) {
		return ($this->Query_ID ? $this->Query_ID->fetch_field_direct($no)->type:'');
	}

	public function getInsertId() {
		return $this->Link_ID->insert_id;
	}

	public function num_fields() {
		return $this->Link_ID->field_count;
	}

	public function num_rows() {
		return $this->Query_ID->num_rows;
	}

	public function getInfo(){
		return 'type: '.DB_CONNECT.
						'<br/>protocol: '.$this->Link_ID->protocol_version.
						'<br/>client: '.$this->Link_ID->client_info.
						'<br/>host: '.$this->Link_ID->host_info.
						'<br/>server: '.$this->Link_ID->server_info;
	}

	protected function ping() {
		return $this->Link_ID->ping();
	}
}
