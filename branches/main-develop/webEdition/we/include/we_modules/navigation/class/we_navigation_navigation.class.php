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

/**
 * General Definition of WebEdition Navigation
 *
 */
class we_navigation_navigation extends we_base_model{

	const SELECTION_STATIC = 'static';
	const SELECTION_DYNAMIC = 'dynamic';
	const SELECTION_NODYNAMIC = 'nodynamic';
	const STYPE_URLLINK = 'urlLink';
	const STYPE_DOCLINK = 'docLink';
	const STYPE_DOCTYPE = 'doctype';
	const STYPE_OBJLINK = 'objLink';
	const STYPE_CLASS = 'classname';
	const STYPE_CATLINK = 'catLink';
	const STYPE_CATEGORY = 'category';
	const LSELECTION_INTERN = 'intern';
	const LSELECTION_EXTERN = 'extern';
	const defaultPreviewCode = '<we:navigation navigationname="default" parentid="@###PARENTID###@" />

<we:navigationEntry type="folder" navigationname="default">
  <li><we:navigationField name="text" />
    <we:ifHasEntries><ul><we:navigationEntries /></ul></we:ifHasEntries>
  </li>
</we:navigationEntry>

<we:navigationEntry type="item" navigationname="default">
  <li><a href="<we:navigationField name="href" />"><we:navigationField name="text" /></a></li>
</we:navigationEntry>

<ul>
<we:navigationWrite navigationname="default" />
</ul>';

//properties
	var $ID = 0;
	var $Text = '';
	var $Display = '';
	var $Name = '';
	var $ParentID = 0;
	var $TitleField = '';
	var $IconID = '';
	var $IsFolder = 0;
	var $Path = '/';
	var $Published = 1;
	var $Selection = self::SELECTION_STATIC;
	var $SelectionType = self::STYPE_DOCLINK;
	var $FolderID = 0;
	var $DocTypeID = 0;
	var $ClassID = 0;
	var $Categories = array();
	var $CatAnd = 1;
	var $CategoryIDs = '';
	var $Sort = array();
	var $ShowCount = 5;
	var $LinkID = 0;
	var $CurrentOnUrlPar = 0;
	var $CurrentOnAnker = 0;
	var $Ordn = 0;
	var $Depended = 0;
	var $WorkspaceID = -1;
	var $CatParameter = 'catid';
	var $Parameter = '';
	var $LinkSelection = self::LSELECTION_INTERN;
	var $Url = 'http://';
	var $UrlID = 0;
	var $Charset = '';
	var $previewCode = '';
	var $ClassName = __CLASS__;
	var $ContentType = 'weNavigation';
	var $Attributes = array();
	var $FolderSelection = self::STYPE_DOCLINK;
	var $FolderParameter = '';
	var $FolderWsID = -1;
	var $FolderUrl = 'http://';
	var $Table = NAVIGATION_TABLE;
	var $LimitAccess = 0;
	var $AllCustomers = 1;
	var $ApplyFilter = 0;
	var $Customers = array();
	var $CustomerFilter = array();
	var $BlackList = array();
	var $WhiteList = array();
	var $UseDocumentFilter = true;
	var $serializedFields = array('Sort', 'Attributes', 'CustomerFilter');

	/**
	 * Default Constructor
	 * Can load or create new navigation depends of parameter
	 */
	public function __construct($navigationID = 0, we_database_base $db = null){
		parent::__construct(NAVIGATION_TABLE, $db, false);
		$this->persistent_slots = array(
			'ID' => we_base_request::INT,
			'ParentID' => we_base_request::INT,
			'Path' => we_base_request::STRING,
			'Published' => we_base_request::BOOL,
			'Text' => we_base_request::STRING,
			'Display' => we_base_request::RAW_CHECKED, //note: it is desired to have test<sup>a</sup>
			'ContentType' => we_base_request::STRING,
			'IsFolder' => we_base_request::BOOL,
			'TitleField' => we_base_request::STRING,
			'IconID' => we_base_request::INT,
			'Selection' => we_base_request::STRING,
			'LinkID' => we_base_request::INT,
			'CurrentOnUrlPar' => we_base_request::BOOL,
			'CurrentOnAnker' => we_base_request::BOOL,
			'SelectionType' => we_base_request::STRING,
			'FolderID' => we_base_request::INT,
			'DocTypeID' => we_base_request::INT,
			'ClassID' => we_base_request::INT,
			'Categories' => we_base_request::STRING,
			'CatAnd' => we_base_request::BOOL,
			'Sort' => we_base_request::STRING,
			'ShowCount' => we_base_request::INT,
			'Ordn' => we_base_request::INT,
			'Depended' => we_base_request::BOOL,
			'WorkspaceID' => we_base_request::INT,
			'CatParameter' => we_base_request::RAW,
			'Parameter' => we_base_request::RAW,
			'LinkSelection' => we_base_request::RAW,
			'Url' => we_base_request::URL,
			'UrlID' => we_base_request::INT,
			'Charset' => we_base_request::STRING,
			'Attributes' => we_base_request::RAW,
			'FolderSelection' => we_base_request::STRING,
			'FolderWsID' => we_base_request::INT,
			'FolderParameter' => we_base_request::RAW,
			'FolderUrl' => we_base_request::URL,
			'LimitAccess' => we_base_request::INT,
			'AllCustomers' => we_base_request::INT,
			'ApplyFilter' => we_base_request::INT,
			'Customers' => we_base_request::INTLIST,
			'CustomerFilter' => we_base_request::RAW,
			'BlackList' => we_base_request::INTLIST,
			'WhiteList' => we_base_request::INTLIST,
			'UseDocumentFilter' => we_base_request::BOOL,
		);


		if(($ws = get_ws(NAVIGATION_TABLE))){
			list($this->ParentID) = makeArrayFromCSV($ws);
		}

		if($navigationID){
			$this->ID = $navigationID;
			$this->load($navigationID);
		}

		$this->previewCode = str_replace('@###PARENTID###@', $this->ID, self::defaultPreviewCode);
		$this->Charset = DEFAULT_CHARSET;
	}

	function load($id = 0, $isAdvanced = false){
		if(parent::load($id, true)){
			$this->CategoryIDs = $this->Categories;

			if(!is_array($this->Categories)){
				$this->Categories = makeArrayFromCSV($this->Categories);
			}
			$this->Categories = $this->convertToPaths($this->Categories, CATEGORY_TABLE);

			$this->Sort = we_unserialize($this->Sort);

			if(!$this->IsFolder){
				$this->Charset = $this->findCharset($this->ParentID);
			}
			$this->Attributes = we_unserialize($this->Attributes);

			if(defined('CUSTOMER_TABLE')){
				if(!is_array($this->Customers)){
					$this->Customers = explode(',', $this->Customers);
				}
				if(!is_array($this->BlackList)){
					$this->BlackList = explode(',', $this->BlackList);
				}
				if(!is_array($this->WhiteList)){
					$this->WhiteList = explode(',', $this->WhiteList);
				}

				$this->CustomerFilter = we_unserialize($this->CustomerFilter);
			}
		}
		$this->ContentType = 'weNavigation';
	}

	private function _getFilterOfDocument(){
		switch(($this->IsFolder ? $this->FolderSelection : $this->SelectionType)){
			case self::STYPE_OBJLINK:
				$table = OBJECT_FILES_TABLE;
				$id = $this->LinkID;
				break;
			case self::STYPE_DOCLINK:
				$table = FILE_TABLE;
				$id = $this->LinkID;
				break;
			default:
				$id = 0;
				$table = "";
		}

		$this->LimitAccess = 0;

		if($id && $table){
			$docFilter = we_customer_documentFilter::getFilterByIdAndTable($id, $table);
			if($docFilter){
//quick hack
				$mode = $docFilter->getMode();
				$cust = $docFilter->getSpecificCustomers();
				we_navigation_customerFilter::translateModeToNavModel($mode, $this);
				$this->Customers = ($mode == we_customer_abstractFilter::SPECIFIC && empty($cust) ? array(-1) : $cust);
				$this->CustomerFilter = $docFilter->getFilter();
				$this->BlackList = $docFilter->getBlackList();
				$this->WhiteList = $docFilter->getWhiteList();
			}
		}
	}

	function save($order = true, $rebuild = false, $jsonSer = false){
		if(defined('CUSTOMER_TABLE') && $this->UseDocumentFilter){
			$this->_getFilterOfDocument();
		}

		$this->Text = self::encodeSpecChars($this->Text);
		$paths = $this->Categories;
		$this->Categories = path_to_id($this->Categories, CATEGORY_TABLE, $this->db);

		$this->setPath();

		if($order){
			$ord_count = f('SELECT COUNT(1) FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ParentID), '', $this->db);
			if($this->ID == 0){
				$this->Ordn = $ord_count;
			} else {
				if($this->Ordn > ($ord_count - 1)){
					$this->Ordn = $ord_count;
				}
				$oldPid = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->ID), '', $this->db);
			}
		}

		if(!$this->IsFolder){
			$charset = $this->Charset;
			$this->Charset = '';
		}

		if(defined('CUSTOMER_TABLE') && $this->LimitAccess){
			$save = array($this->Customers, $this->BlackList, $this->WhiteList);
			$this->WhiteList = implode(',', $this->WhiteList);
			$this->BlackList = implode(',', $this->BlackList);
			$this->Customers = implode(',', $this->Customers);
		} else {
			$save = array(array(), array(), array());
			$this->Customers = $this->WhiteList = $this->BlackList = $this->CustomerFilter = '';
		}
		if(is_array($this->Attributes)){
			$this->Attributes = array_filter($this->Attributes);
		}
		if(($res = parent::save(false, true, true))){
			$this->registerMediaLinks();
		}

		if($order && isset($oldPid) && $oldPid != $this->ParentID){
// the entry has been moved
			$this->reorder($oldPid);
			$this->reorder($this->ParentID);
			$this->previewCode = str_replace('@###PARENTID###@', $this->ID, self::defaultPreviewCode);
		}
		$this->Categories = $paths;

		if(!$this->IsFolder){
			$this->Charset = $charset;
		}

		if(defined('CUSTOMER_TABLE')){
			list($this->Customers, $this->BlackList, $this->WhiteList) = $save;
		}
		$this->Name = $this->Text;
		if(!$rebuild){
			we_navigation_cache::delNavigationTree($this->ID);
			if(isset($oldPid) && $oldPid != $this->ParentID){
				we_navigation_cache::delNavigationTree($this->ParentID);
				we_navigation_cache::delNavigationTree($oldPid);
			}
		}
		return true;
	}

	function registerMediaLinks(){
		$this->unregisterMediaLinks();
		if($this->IconID){
			$this->MediaLinks[] = $this->IconID;
		}
		if($this->SelectionType === 'docLink' && $this->LinkID){
			$this->MediaLinks[] = $this->LinkID;
		}

		parent::registerMediaLinks();
	}

	function convertToPaths($ids, $table){
		if(!is_array($ids)){
			return array();
		}
		$ids = array_unique($ids);
		$paths = array();
		foreach($ids as $id){
			$paths[] = id_to_path($id, $table);
		}
		return $paths;
	}

	function delete(){
		if(!$this->ID){
			return false;
		}
		if($this->IsFolder){
			$this->deleteChilds();
		}
		parent::delete();
		$this->unregisterMediaLinks();

		we_navigation_cache::delNavigationTree($this->ParentID);

		return true;
	}

	function deleteChilds(){
		$this->db->query('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID));
		while($this->db->next_record()){
			$child = new we_navigation_navigation($this->db->f("ID"));
			$child->delete();
		}
	}

	function deleteStaticChilds(){
		$this->db->query('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' AND Selection="' . self::SELECTION_STATIC . '" ');
		while($this->db->next_record()){
			$child = new we_navigation_navigation($this->db->f("ID"));
			$child->delete();
		}
	}

	function clearSessionVars(){
		if(isset($_SESSION['weS']['navigation_session'])){
			unset($_SESSION['weS']['navigation_session']);
		}
	}

	function filenameNotValid($text){
		return (strpos($text, '/') !== false);
	}

	function alnumNotValid($text){
		return !(ctype_alnum($text));
	}

	function setPath(){
		$ppath = f('SELECT Path FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->ParentID), 'Path', $this->db);
		$this->Path = $ppath . '/' . $this->Text;
	}

	function pathExists($path){
		return f('SELECT 1 FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($path) . '" AND ID!=' . intval($this->ID) . ' LIMIT 1', '', $this->db);
	}

	function isSelf(){
		if(!$this->ID){
			return false;
		}
		$count = 0;
		$parentid = $this->ParentID;
		while($parentid != 0){
			if($parentid == $this->ID){
				return true;
			}
			$parentid = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($parentid), '', $this->db);
			$count++;
			if($count == 9999){
				return false;
			}
		}
		return false;
	}

	function isAllowedForUser(){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
//checkWS
		return f('SELECT 1 FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->ParentID) . ' ' . self::getWSQuery(), '', $this->db);
	}

	function evalPath($id = 0){
		$db_tmp = new DB_WE();
		$path = '';
		if($id == 0){
			$id = $this->ParentID;
			$path = $this->Text;
		}

		$foo = getHash('SELECT Text,ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($id), $db_tmp);
		$path = '/' . (isset($foo['Text']) ? $foo['Text'] : '') . $path;

		$pid = isset($foo['ParentID']) ? $foo['ParentID'] : '';
		while($pid > 0){
			$db_tmp->query('SELECT Text,ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($pid));
			while($db_tmp->next_record()){
				$path = '/' . $db_tmp->f('Text') . $path;
				$pid = $db_tmp->f('ParentID');
			}
		}
		return $path;
	}

	function saveField($name, $serialize = false){
		$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter(array(
					$name => ($serialize ? we_serialize($this->$name) : $this->$name)
				)) . ' WHERE ID=' . intval($this->ID));
		return $this->db->affected_rows();
	}

	function getDynamicEntries(){
		if($this->Selection == self::SELECTION_DYNAMIC){
			switch($this->SelectionType){
				case self::STYPE_DOCTYPE:
					return we_navigation_dynList::getDocuments($this->DocTypeID, $this->FolderID, $this->Categories, $this->CatAnd ? 'AND' : 'OR', $this->Sort, $this->ShowCount, $this->TitleField);
				case self::STYPE_CATEGORY:
					return we_navigation_dynList::getCatgories($this->FolderID, $this->ShowCount);
				default:
					return $this->ClassID > 0 ?
							we_navigation_dynList::getObjects($this->ClassID, $this->FolderID, $this->Categories, $this->CatAnd ? 'AND' : 'OR', $this->Sort, $this->ShowCount, $this->TitleField) :
							array();
			}
		}
	}

	function getChilds(){
		$items = array();

		$this->db->query('SELECT ID,Path,Text,Ordn FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' ORDER BY Ordn');

		while($this->db->next_record()){
			$items[] = array(
				'id' => $this->db->f('ID'),
				'path' => $this->db->f('Path'),
				'text' => $this->db->f('Text'),
				'ordn' => $this->db->f('Ordn')
			);
		}

		return $items;
	}

	private function getDynamicChilds(){
		$this->db->query('SELECT ID,Ordn FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' AND IsFolder=0 AND Depended=1 ORDER BY Ordn');
		return $this->db->getAll();
	}

	function populateGroup($items){
		$info = $this->getDynamicEntries();
		$new_items = array();

		foreach($info as $k => $item){

			$navigation = new we_navigation_navigation();

			$navigation->ParentID = $this->ID;
			$navigation->Selection = self::SELECTION_STATIC;
			$navigation->SelectionType = ($this->SelectionType == self::STYPE_DOCTYPE ? self::STYPE_DOCLINK : ($this->SelectionType == self::STYPE_CATEGORY ? self::STYPE_CATLINK : self::STYPE_OBJLINK));
			$navigation->LinkID = $item['id'];
			$navigation->Ordn = isset($items[$k]) ? $items[$k]['Ordn'] : $k;
			$navigation->Depended = 1;
			$navigation->Text = $item['field'] ? : $item['text'];
			$navigation->IconID = $this->IconID;
			$navigation->Url = $this->Url;
			$navigation->UrlID = $this->UrlID;
			$navigation->CatParameter = $this->CatParameter;
			$navigation->LinkSelection = $this->LinkSelection;
			$navigation->Parameter = $this->Parameter;
			$navigation->WorkspaceID = $this->WorkspaceID;

			$navigation->save(false);

			$new_items[] = array(
				'id' => $navigation->ID,
				'text' => $navigation->Text
			);
		}

// delete old items??


		return $new_items;
	}

	function depopulateGroup(){
		$items = $this->getDynamicChilds();
		foreach($items as $id){
			$navigation = new we_navigation_navigation($id['ID']);
			if($navigation->delete()){

			}
		}
		return $items;
	}

	function hasDynChilds(){
		return ($this->ID ?
						f('SELECT 1 FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' AND Depended=1 LIMIT 1', '', $this->db) :
						false);
	}

	function hasAnyChilds(){
		return ($this->ID ?
						f('SELECT 1 FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' LIMIT 1', '', $this->db) :
						false);
	}

	function hasIndependentChilds(){
		return ($this->ID ?
						f('SELECT 1 FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' AND Depended=0 LIMIT 1', '', $this->db) :
						false);
	}

	function getDynamicPreview(array $sitem, $rules = false){
		$items = array();

		foreach($sitem as $item){
			if($item['ParentID'] == $this->ID){
				$nav = new we_navigation_navigation(0, $this->db);
				$nav->initByRawData($item);
				list($table, $linkid) = $nav->getTableIdForItem();
				if($nav->IsFolder || $nav->Selection != self::SELECTION_DYNAMIC){
					$items[] = array(
						'id' => $nav->ID,
						'name' => $nav->Text,
						'text' => (isset($nav->Display) && !empty($nav->Display)) ? $nav->Display : $nav->Text,
						'display' => (isset($nav->Display) && !empty($nav->Display)) ? $nav->Display : "",
						'docid' => $linkid,
						'table' => $table,
						'href' => $nav->getHref(),
						'type' => $nav->IsFolder ? we_base_ContentTypes::FOLDER : 'item',
						'parentid' => $nav->ParentID,
						'workspaceid' => $nav->WorkspaceID,
						'icon' => we_navigation_items::id2path($nav->IconID),
						'attributes' => $nav->Attributes,
						'customers' => we_navigation_items::getCustomerData($nav),
						'currentonurlpar' => $nav->CurrentOnUrlPar,
						'currentonanker' => $nav->CurrentOnAnker,
						'currentoncat' => $nav->SelectionType === self::STYPE_CATLINK ? 1 : 0,
						'catparam' => $nav->CatParameter,
						'limitaccess' => $nav->LimitAccess,
						'depended' => $nav->Depended
					);
				}

				if(!$nav->IsFolder && $nav->Selection == self::SELECTION_DYNAMIC){
					$dyn_items = $nav->getDynamicEntries();
					foreach($dyn_items as $dyn){

						$href = $nav->getHref($dyn['id']);
						$items[] = array(
							'id' => $nav->ID . '_' . $dyn['id'],
							'name' => $dyn['field'] ? : $dyn['text'],
							'text' => $dyn['field'] ? : $dyn['text'],
							'display' => isset($dyn['display']) ? $dyn['display'] : '',
							'docid' => $dyn['id'],
							'table' => (($nav->SelectionType == self::STYPE_CLASS || $nav->SelectionType == self::STYPE_OBJLINK) ? OBJECT_FILES_TABLE : FILE_TABLE),
							'href' => $href,
							'type' => 'item',
							'parentid' => $nav->ParentID,
							'workspaceid' => $nav->WorkspaceID,
							'icon' => we_navigation_items::id2path($nav->IconID),
							'attributes' => $nav->Attributes,
							'customers' => we_navigation_items::getCustomerData($nav),
							'currentonurlpar' => $nav->CurrentOnUrlPar,
							'currentonanker' => $nav->CurrentOnAnker,
							'limitaccess' => $nav->LimitAccess,
							'depended' => 2,
							'currentoncat' => 0,
							'catparam' => '',
						);

						if($rules){
							$items[(count($items) - 1)]['currentRule'] = we_navigation_rule::getWeNavigationRule(
											'defined_' . ($dyn['field'] ? : $dyn['text']), $nav->ID, $nav->SelectionType, $nav->FolderID, $nav->DocTypeID, $nav->ClassID, $nav->CategoryIDs, $nav->WorkspaceID, $href, false);
						}
					}
				}

				if($nav->IsFolder){
					$items = array_merge($items, $nav->getDynamicPreview($sitem, $rules));
				}
			}
		}

		return $items;
	}

	public function reorderAbs($newPos){
		if(!$this->ID || $this->Ordn == $newPos){
			return false;
		}
		if($newPos == -1){//last entry
			$this->Ordn = 99999;
			$this->saveField('Ordn');
			$this->reorder($this->ParentID);
		} else {
//check position
			if($newPos < 0 || $newPos > $max = f('SELECT MAX(Ordn) FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ParentID), '', $this->db)){
				return false;
			}

			if($newPos < $this->Ordn){
				$this->db->query('UPDATE ' . NAVIGATION_TABLE . ' SET Ordn=Ordn+1 WHERE (Ordn BETWEEN ' . $newPos . ' AND ' . $this->Ordn . ') AND ParentID=' . $this->ParentID . ' AND ID!=' . $this->ID);
			} else {//$newPos>Ordn
				$this->db->query('UPDATE ' . NAVIGATION_TABLE . ' SET Ordn=Ordn-1 WHERE (Ordn BETWEEN ' . $this->Ordn . ' AND ' . $newPos . ') AND ParentID=' . $this->ParentID . ' AND ID!=' . $this->ID);
			}

			$this->Ordn = $newPos;
			$this->saveField('Ordn');
			$this->reorder($this->ParentID);
		}
		$this->Ordn = f('SELECT Ordn FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->ID), '', $this->db);
		return true;
	}

	public function reorder($pid){// FIXME: set private again in 6.5
		$this->db->query('SET @count:=-1');
		$this->db->query('UPDATE ' . NAVIGATION_TABLE . ' SET Ordn=(@count:=@count+1) WHERE ParentID=' . intval($pid) . ' ORDER BY Ordn');
	}

	function reorderUp(){
		if(!($this->ID && $this->Ordn > 0)){
			return false;
		}
		$this->db->query('UPDATE ' . NAVIGATION_TABLE . ' SET Ordn=' . intval($this->Ordn) . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Ordn=' . intval( --$this->Ordn));
		$this->saveField('Ordn');
		$this->reorder($this->ParentID);
		return true;
	}

	function reorderDown(){
		if(!$this->ID){
			return false;
		}
		$num = f('SELECT COUNT(1) FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ParentID), '', $this->db);
		if($this->Ordn < ($num - 1)){
			$this->db->query('UPDATE ' . NAVIGATION_TABLE . ' SET Ordn=' . intval($this->Ordn) . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Ordn=' . intval( ++$this->Ordn));
			$this->saveField('Ordn');
			$this->reorder($this->ParentID);
			return true;
		}

		return false;
	}

	function getTableIdForItem(){
		if($this->IsFolder){
			switch($this->FolderSelection){
				case self::STYPE_URLLINK:
					return array('', 0);
				case self::STYPE_OBJLINK:
					return array(OBJECT_FILES_TABLE, $this->LinkID);
				default:
					return array(FILE_TABLE, $this->LinkID);
			}
		}

		switch($this->SelectionType){
			case self::STYPE_URLLINK:
				return array('', 0);
			case self::STYPE_CATEGORY:
			case self::STYPE_CATLINK:
				if($this->LinkSelection === self::LSELECTION_EXTERN){
					return array('', 0);
				}
				return array(FILE_TABLE, $this->UrlID);
			case self::STYPE_CLASS:
			case self::STYPE_OBJLINK:
				return array(OBJECT_FILES_TABLE, $this->LinkID);
			case self::STYPE_DOCLINK:
				return array(FILE_TABLE, $this->LinkID);
		}
	}

	function getHref($id = 0){
		if($this->IsFolder){
			$path = '';
//FIXME: remove eval
			eval('$param = "' . addslashes(preg_replace('%\\$%', '$this->', $this->FolderParameter)) . '";');
			switch($this->FolderSelection){
				case self::STYPE_URLLINK:
					$path = $this->FolderUrl;
					break;
				default:
					$objecturl = '';
					if($this->FolderSelection == self::STYPE_OBJLINK){
						if(NAVIGATION_OBJECTSEOURLS){
							$db = new DB_WE();
							$objectdaten = getHash('SELECT Url,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->LinkID) . ' LIMIT 1', $db);
							$objecturl = empty($objectdaten['Url']) ? '' : $objectdaten['Url'];
							$objecttriggerid = empty($objectdaten['TriggerID']) ? 0 : $objectdaten['TriggerID'];
							if(!$objecturl){
								$param = 'we_objectID=' . $this->LinkID . ($param ? '&' : '') . $param;
							}
						} else {
							$param = 'we_objectID=' . $this->LinkID . ($param ? '&' : '') . $param;
						}
						$id = ($objecttriggerid ? : we_navigation_dynList::getFirstDynDocument($this->FolderWsID));
					} else {
						$id = $this->LinkID;
					}
					$p = we_navigation_items::id2path($id);
					$path = ($p === '/' ? '' : $p);
					if(NAVIGATION_OBJECTSEOURLS && $objecturl != ''){
						$path_parts = pathinfo($path);
//FIXME: can we use seoIndexHide($path_parts['basename'])??
						$path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' .
								(NAVIGATION_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
										'' : $path_parts['filename'] . '/'
								) . $objecturl;
					}
					break;
			}
		} else {
			$id = ($id ? : $this->LinkID);
			$path = '';
//FIXME: remove eval
			eval('$param = "' . addslashes(preg_replace('%\\$%', '$this->', $this->Parameter)) . '";');

			switch($this->SelectionType){
				case self::STYPE_URLLINK:
					$path = $this->Url;
					break;
				case self::STYPE_CATEGORY:
				case self::STYPE_CATLINK:
					$path = $this->LinkSelection === self::LSELECTION_EXTERN ? $this->Url : we_navigation_items::id2path($this->UrlID);
					if(!empty($this->CatParameter)){
						$param = $this->CatParameter . '=' . $id . (!empty($param) ? '&' : '') . $param;
					}
					break;
				default:
					if($this->SelectionType == self::STYPE_CLASS || $this->SelectionType == self::STYPE_OBJLINK){
						$objecturl = '';
						if(NAVIGATION_OBJECTSEOURLS){
							$db = new DB_WE();
							$objectdaten = getHash('SELECT  Url,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id) . ' LIMIT 1', $db);
							if(isset($objectdaten['Url'])){
								$objecturl = $objectdaten['Url'];
								$objecttriggerid = $objectdaten['TriggerID'];
							} else {
								$objecturl = '';
								$objecttriggerid = '';
							}
							if(!$objecturl){
								$param = 'we_objectID=' . $id . ($param ? '&' : '') . $param;
							}
						} else {
							$param = 'we_objectID=' . $id . ($param ? '&' : '') . $param;
							$objecttriggerid = '';
						}
						$id = ($objecttriggerid ? : we_navigation_dynList::getFirstDynDocument($this->WorkspaceID, $db));
					}

					$p = we_navigation_items::id2path($id);
					$path = ($p === '/' ? '' : $p);
					if(NAVIGATION_OBJECTSEOURLS && !empty($objecturl)){
						$path_parts = pathinfo($path);
//FIXME: can we use seoIndexHide($path_parts['basename'])??
						$path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . (
								(NAVIGATION_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))) ?
										'' : $path_parts['filename'] . '/'
								) . $objecturl;
					}
			}
		}

		if(!is_array($this->Attributes)){
			$this->Attributes = array_filter(we_unserialize($this->Attributes));
		}
		$path = str_replace(' ', '%20', trim($path)) .
				($param ? ((strpos($path, '?') === false ? '?' : '&amp;') . $param) : '');

//leave this, because of strpos
		$path .= (($this->CurrentOnAnker && !empty($this->Attributes['anchor'])) ? ( (strpos($path, '?') === false ? '?' : '&amp;') . 'we_anchor=' . $this->Attributes['anchor']) : '') .
				((!empty($this->Attributes['anchor']) ) ? ('#' . $this->Attributes['anchor']) : '');

		$path = str_replace(array('&amp;', '&'), array('&', '&amp;'), $path);

//FIXME: can we use seoIndexHide($path_parts['basename'])??
		if(NAVIGATION_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES && $this->LinkSelection != self::LSELECTION_EXTERN && $this->SelectionType != self::STYPE_URLLINK){ //Fix #8353
			$dirindexnames = array_map('trim', explode(',', '/' . str_replace(',', ',/', NAVIGATION_DIRECTORYINDEX_NAMES)));
			return str_replace($dirindexnames, '/', $path);
		}

		return $path;
	}

	function findCharset($pid){
		$count = 0;
		$db = new DB_WE();
		while($pid && (++$count < 100)){
			$hash = getHash('SELECT ParentID,Charset FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($pid), $db);
			if(!empty($hash['Charset'])){
				return $hash['Charset'];
			}
			if(empty($hash['ParentID'])){
				return '';
			}
			$pid = $hash['ParentID'];
		}

		return '';
	}

	function we_load($id){
		parent::load($id, true);
		$this->ContentType = 'weNavigation';
	}

	function we_save(){
		return $this->save();
	}

	function setAttribute($name, $value){
		$this->Attributes[$name] = $value;
	}

	function getAttribute($name){
		return isset($this->Attributes[$name]) ? $this->Attributes[$name] : null;
	}

	function initByRawData($data){
		foreach($data as $key => $value){
			if(!is_numeric($key)){
				$this->$key = in_array($key, $this->serializedFields) ? we_unserialize($value) : $value;
			}
		}
	}

	public static function encodeSpecChars($string){
		$open = '!@###';
		$close = '!###@';
		$amp = '!!##@';

		$string = preg_replace(array(
			'|<br(\/)?>|',
			'|<(\/)?b>|',
			'|<(\/)?i>|',
			'|&([^;]+);|',
				), array(
			$open . 'br${1}' . $close,
			$open . '${1}b' . $close,
			$open . '${1}i' . $close,
			$amp . '${1};',
				), $string);

		return str_replace(array(
			$open,
			$close,
			$amp,
				), array(
			'<',
			'>',
			'&',
				), oldHtmlspecialchars($string));
	}

	public static function getNavCondition($id, $table){
		$linkType = ($table == OBJECT_FILES_TABLE ? self::STYPE_OBJLINK : self::STYPE_DOCLINK);
		return ' ((IsFolder=1 AND FolderSelection="' . escape_sql_query($linkType) . '") OR (IsFolder=0 AND SelectionType="' . escape_sql_query($linkType) . '")) AND LinkID=' . intval($id) . ' ';
	}

	public static function getWSQuery(){
		if(permissionhandler::hasPerm('ADMINISTRATOR') || !($ws = get_ws(NAVIGATION_TABLE))){
			return '';
		}
// #5836: Use function get_ws()
		$ids = explode(',', $ws);
		$wrkNavi = id_to_path($ids, NAVIGATION_TABLE, null, true);

		$condition = array();
		foreach($wrkNavi as $nav){
			$condition[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($nav) . '/%"';
		}
		return ' AND (ID IN(' . implode(',', $ids) . ') OR (' . implode(' OR ', $condition) . '))';
	}

}
