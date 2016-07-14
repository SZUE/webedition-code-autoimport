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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_navigation_rule extends we_base_model{
	var $Table = NAVIGATION_RULE_TABLE;
	var $ContentType = 'weNavigationRule';
	var $ClassName = __CLASS__;
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
		'ID' => we_base_request::INT,
		'NavigationName' => we_base_request::STRING,
		'NavigationID' => we_base_request::INT,
		'SelectionType' => we_base_request::STRING,
		'FolderID' => we_base_request::INT,
		'DoctypeID' => we_base_request::INT,
		'ClassID' => we_base_request::INT,
		'Categories' => we_base_request::INTLIST,
		'WorkspaceID' => we_base_request::INT
	);

	public function __construct($persData = array()){
		parent::__construct(NAVIGATION_RULE_TABLE, null, false, true);
		if($persData){
			foreach(array_keys($this->persistent_slots) as $val){
				if(isset($persData[$val])){
					$this->$val = $persData[$val];
				}
			}
		}
	}

	function initByID($ruleId){
		return parent::load(intval($ruleId));
	}

	static function getWeNavigationRule($navigationName, $navigationId, $selectionType, $folderId, $doctype, $classId, $categories, $workspaceId, $href = '', $selfCurrent = true){

		$navigation = new self();
		$navigation->NavigationName = $navigationName;
		$navigation->NavigationID = $navigationId;
		$navigation->SelectionType = $selectionType;
		$navigation->FolderID = $folderId;
		$navigation->DoctypeID = $doctype;
		$navigation->ClassID = $classId;
		$navigation->Categories = $categories;
		$navigation->WorkspaceID = $workspaceId;

		$navigation->Href = $href;
		$navigation->SelfCurrent = $selfCurrent;

		return $navigation;
	}

	function we_load($id){
		$this->load($id);
	}

	function we_save(){
		parent::save($this->ID ? false : true);
	}

	function processVariables(){
		if(($name = we_base_request::_(we_base_request::STRING, 'CategoriesControl')) && ($cnt = we_base_request::_(we_base_request::INT, 'CategoriesCount')) !== false){
			$categories = array();

			for($i = 0; $i < $cnt; $i++){
				if(($cat = we_base_request::_(we_base_request::STRING, $name . '_variant0_' . $name . '_item' . $i)) !== false){
					$categories[] = $cat;
				}
			}

			$this->Categories = path_to_id($categories, CATEGORY_TABLE, $GLOBALS['DB_WE']);
		}

		if(is_array($this->persistent_slots)){
			foreach($this->persistent_slots as $key => $type){
				if(($tmp = we_base_request::_($type, $key)) !== false){
					$this->$key = $tmp;
				}
			}
		}

		$this->isnew = ($this->ID == 0);
	}

}
