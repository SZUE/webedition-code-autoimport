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
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/modules/weModelBase.php');

class weNavigationRule extends weModelBase{

	var $table = NAVIGATION_RULE_TABLE;
	var $Table = NAVIGATION_RULE_TABLE;
	var $ContentType = 'weNavigationRule';
	var $ClassName = __CLASS__;
	var $db;
	var $ID;
	var $NavigationName;
	var $NavigationID;
	var $SelectionType;
	var $FolderID;
	var $DoctypeID;
	var $ClassID;
	var $Categories;
	var $WorkspaceID;
	var $Href;
	var $SelfCurrent;
	var $persistent_slots = array(
		'ID',
		'NavigationName',
		'NavigationID',
		'SelectionType',
		'FolderID',
		'DoctypeID',
		'ClassID',
		'Categories',
		'WorkspaceID'
	);

	function weNavigationRule(){
		$this->db = new DB_WE();
	}

	function initByID($ruleId){

		parent::load(intval($ruleId));
	}

	function getWeNavigationRule($navigationName, $navigationId, $selectionType, $folderId, $doctype, $classId, $categories, $workspaceId, $href = '', $selfCurrent = true){

		$_navigation = new weNavigationRule();
		$_navigation->NavigationName = $navigationName;
		$_navigation->NavigationID = $navigationId;
		$_navigation->SelectionType = $selectionType;
		$_navigation->FolderID = $folderId;
		$_navigation->DoctypeID = $doctype;
		$_navigation->ClassID = $classId;
		$_navigation->Categories = $categories;
		$_navigation->WorkspaceID = $workspaceId;

		$_navigation->Href = $href;
		$_navigation->SelfCurrent = $selfCurrent;

		return $_navigation;
	}

	function we_load($id){
		$this->load($id);
	}

	function we_save(){
		parent::save($this->ID ? false : true);
	}

	// beide folgenden f�r Bug #4142
	function deleteDB(){
		unset($this->db);
	}

	function renewDB(){
		$this->db = new DB_WE();
	}

}