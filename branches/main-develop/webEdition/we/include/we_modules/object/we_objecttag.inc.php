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


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_db.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_db_tools.inc.php");
include_once( WE_OBJECT_MODULE_DIR ."we_listview_object.class.php");



class we_objecttag{

	var $DB_WE;
	var $class = "";
	var $id = 0;
	var $triggerID = 0;
	var $ClassName = "we_objecttag";
	var $object = "";
	var $avail = false;
	var $hidedirindex = false;
	var $objectseourls=false;

	function we_objecttag($class="", $id=0, $triggerID=0, $searchable=true, $condition="",$hidedirindex=false,$objectseourls=false){
		$this->DB_WE = new DB_WE;
		$this->id = $id;
		if(!$this->id && isset($_REQUEST['we_objectID']) && $_REQUEST['we_objectID']){
			!$this->id=$_REQUEST['we_objectID'];
		}
		$this->class = $class;
		$this->hidedirindex = $hidedirindex;
		$this->objectseourls = $objectseourls;

		$this->triggerID = $triggerID;
		$unique = md5(uniqid(rand()));

		if($this->id){
			$foo = getHash("SELECT TableID,ObjectID FROM ".OBJECT_FILES_TABLE." WHERE ID='".abs($this->id)."'",$this->DB_WE);
			if(count($foo) > 0 ){
				$this->object = new we_listview_object($unique,1,0,"",0,$foo["TableID"],"","","(" . OBJECT_X_TABLE.$foo["TableID"].".ID='".abs($foo["ObjectID"])."')" .  ($condition ? " AND $condition" : ""),$this->triggerID,"","",$searchable,"", "", "", "", '', '', '', 0,"", "", "",'',$hidedirindex,$objectseourls);
				if($this->object->next_record()){
					$this->avail = true;
				}
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
