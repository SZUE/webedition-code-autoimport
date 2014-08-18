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
class we_navigation_rule extends weModelBase{
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

	public function __construct($persData = array()){
		parent::__construct(NAVIGATION_RULE_TABLE, null, false);
		if($persData){
			foreach($this->persistent_slots as $val){
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

		$_navigation = new self();
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

	function processVariables(){
		if(($name = we_base_request::_(we_base_request::STRING, 'CategoriesControl')) && ($cnt = we_base_request::_(we_base_request::INT, 'CategoriesCount')) !== false){
			$_categories = array();

			for($i = 0; $i < $cnt; $i++){
				if(($cat = we_base_request::_(we_base_request::STRING, $name . '_variant0_' . $name . '_item' . $i)) !== false){
					$_categories[] = $cat;
				}
			}

			$categoryIds = array();

			foreach($_categories as $cat){
				if(($path = path_to_id($cat, CATEGORY_TABLE))){
					$categoryIds[] = $path;
				}
			}
			$categoryIds = array_unique($categoryIds);

			$this->Categories = ($categoryIds ? ',' . implode(',', $categoryIds) . ',' : '');
		}

		if(is_array($this->persistent_slots)){
			foreach($this->persistent_slots as $val){
				if(($tmp = we_base_request::_(we_base_request::RAW, $val)) !== false){
					$this->$val = $tmp;
				}
			}
		}

		$this->isnew = ($this->ID == 0);
	}
}
