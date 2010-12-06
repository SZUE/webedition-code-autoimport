<?php
/**
 * webEdition CMS
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


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_db.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_db_tools.inc.php");
include_once( WE_CUSTOMER_MODULE_DIR ."we_listview_onlinemonitor.class.php");



class we_onlinemonitortag{

	var $DB_WE;
	var $class = "";
	var $id = 0;
	var $ClassName = "we_onlinemonitortag";
	var $object = "";
	var $avail = false;

	function we_onlinemonitortag($id=0, $condition=""){
		$this->DB_WE = new DB_WE;
		$this->id = $id;

		$unique = md5(uniqid(rand()));

		if($this->id){
			$this->object = new we_listview_onlinemonitor($unique, 1, 0, "", 0, "(ID='".abs($this->id)."')" .  ($condition ? " AND $condition" : ""));
			if($this->object->next_record()){
				$this->avail = true;
			}
		}
 	}

	function f($key){
		if($this->id){
			return $this->object->f($key);
		}else{
			return "";
		}
	}

}

?>