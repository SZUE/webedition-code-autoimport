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
class we_navigation_navigation extends weModelBase{
	const SELECTION_STATIC = 'static';
	const SELECTION_DYNAMIC = 'dynamic';
	const SELECTION_NODYNAMIC = 'nodynamic';
	const STYPE_URLLINK = 'urlLink';
	const STPYE_DOCLINK = 'docLink';
	const STPYE_DOCTYPE = 'doctype';
	const STPYE_OBJLINK = 'objLink';
	const STPYE_CLASS = 'classname';
	const STPYE_CATLINK = 'catLink';
	const STPYE_CATEGORY = 'category';
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
	var $SelectionType = self::STPYE_DOCLINK;
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
	var $LinkSelection = 'intern';
	var $Url = 'http://';
	var $UrlID = 0;
	var $Charset = '';
	var $previewCode = '';
	var $ClassName = __CLASS__;
	var $ContentType = 'weNavigation';
	var $Attributes = array();
	var $FolderSelection = self::STPYE_DOCLINK;
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
	function __construct($navigationID = 0){
		parent::__construct(NAVIGATION_TABLE, null, false);
		$this->persistent_slots = array(
			'ID' => we_base_request::INT,
			'ParentID' => we_base_request::INT,
			'Path' => we_base_request::STRING,
			'Published' => we_base_request::BOOL,
			'Text' => we_base_request::STRING,
			'Display' => we_base_request::STRING,
			'ContentType' => we_base_request::STRING,
			'Icon' => we_base_request::STRING,
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
			'Customers' => we_base_request::RAW,
			'CustomerFilter' => we_base_request::RAW,
			'BlackList' => we_base_request::RAW,
			'WhiteList' => we_base_request::RAW,
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

	function load($id = 0){
		if(parent::load($id,true)){
			$this->CategoryIDs = $this->Categories;

			if(!is_array($this->Categories)){
				$this->Categories = makeArrayFromCSV($this->Categories);
			}
			$this->Categories = $this->convertToPaths($this->Categories, CATEGORY_TABLE);

			$this->Sort = $this->Sort ? @unserialize($this->Sort) : '';

			if(!$this->IsFolder){
				$this->Charset = $this->findCharset($this->ParentID);
			}
			$this->Attributes = @unserialize($this->Attributes);
			if(!is_array($this->Attributes)){
				$this->Attributes = array();
			}

			if(defined('CUSTOMER_TABLE')){
				if(!is_array($this->Customers)){
					$this->Customers = makeArrayFromCSV($this->Customers);
				}
				if(!is_array($this->BlackList)){
					$this->BlackList = makeArrayFromCSV($this->BlackList);
				}
				if(!is_array($this->WhiteList)){
					$this->WhiteList = makeArrayFromCSV($this->WhiteList);
				}

				$this->CustomerFilter = @unserialize($this->CustomerFilter);
				if(!is_array($this->CustomerFilter)){
					$this->CustomerFilter = array();
				}
			}
		}
		$this->ContentType = 'weNavigation';
	}

	private function _getFilterOfDocument(){
		switch(($this->IsFolder ? $this->FolderSelection : $this->SelectionType)){
			case self::STPYE_OBJLINK:
				$_table = OBJECT_FILES_TABLE;
				$_id = $this->LinkID;
				break;
			case self::STPYE_DOCLINK:
				$_table = FILE_TABLE;
				$_id = $this->LinkID;
				break;
			default:
				$_id = 0;
				$_table = "";
		}

		$this->LimitAccess = 0;

		if($_id && $_table){
			$_docFilter = we_customer_documentFilter::getFilterByIdAndTable($_id, $_table);
			if($_docFilter){
				we_navigation_customerFilter::translateModeToNavModel($_docFilter->getMode(), $this);
				$this->Customers = $_docFilter->getSpecificCustomers();
				$this->CustomerFilter = $_docFilter->getFilter();
				$this->BlackList = $_docFilter->getBlackList();
				$this->WhiteList = $_docFilter->getWhiteList();
			}
		}
	}

	function save($order = true, $rebuild = false){
		if(defined('CUSTOMER_TABLE') && $this->UseDocumentFilter){
			$this->_getFilterOfDocument();
		}

		$this->Text = self::encodeSpecChars($this->Text);
		$this->Icon = ($this->IsFolder == 1 ? we_base_ContentTypes::FOLDER_ICON : we_base_ContentTypes::FILE_ICON);

		$_paths = $this->Categories;
		$this->Categories = makeCSVFromArray(weConvertToIds($this->Categories, CATEGORY_TABLE), true);

		$_preSort = $this->Sort;
		if(is_array($this->Sort)){
			$this->Sort = serialize($this->Sort);
		}
		$this->setPath();

		if($order){
			$_ord_count = f('SELECT COUNT(ID) as OrdCount FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ParentID) . ';', 'OrdCount', $this->db);
			if($this->ID == 0){
				$this->Ordn = $_ord_count;
			} else {
				if($this->Ordn > ($_ord_count - 1)){
					$this->Ordn = $_ord_count;
				}
				$_oldPid = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->ID), 'ParentID', $this->db);
			}
		}

		if($this->IsFolder == 0){
			$_charset = $this->Charset;
			$this->Charset = '';
		}
		$_preAttrib = $this->Attributes;
		if(is_array($this->Attributes)){
			$this->Attributes = serialize($this->Attributes);
		}

		if(defined('CUSTOMER_TABLE') && $this->LimitAccess){
			$_cus_paths = $this->Customers;
			$_bl_paths = $this->BlackList;
			$_wl_paths = $this->WhiteList;
			$this->WhiteList = makeCSVFromArray($this->WhiteList, true);
			$this->BlackList = makeCSVFromArray($this->BlackList, true);
			$this->Customers = makeCSVFromArray($this->Customers, true);
			$this->CustomerFilter = serialize($this->CustomerFilter);
		} else {
			$_cus_paths = array();
			$_bl_paths = array();
			$_wl_paths = array();
			$this->Customers = '';
			$this->WhiteList = '';
			$this->BlackList = '';
			$this->CustomerFilter = '';
		}

		parent::save(false, true);

		if($order && isset($_oldPid) && $_oldPid != $this->ParentID){
			// the entry has been moved
			$this->reorder($_oldPid);
			$this->reorder($this->ParentID);
			$this->previewCode = str_replace('@###PARENTID###@', $this->ID, self::defaultPreviewCode);
		}
		$this->Categories = $_paths;
		$this->Sort = $_preSort;

		if($this->IsFolder == 0){
			$this->Charset = $_charset;
		}

		$this->Attributes = $_preAttrib;

		if(defined('CUSTOMER_TABLE')){
			$this->Customers = $_cus_paths;
			$this->WhiteList = $_wl_paths;
			$this->BlackList = $_bl_paths;
			$this->CustomerFilter = unserialize($this->CustomerFilter);
		}
		$this->Name = $this->Text;
		if(!$rebuild){
			we_navigation_cache::delNavigationTree($this->ID);
			if(isset($_oldPid) && $_oldPid != $this->ParentID){
				we_navigation_cache::delNavigationTree($this->ParentID);
				we_navigation_cache::delNavigationTree($_oldPid);
			}
		}
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
		$_count = 0;
		$_parentid = $this->ParentID;
		while($_parentid != 0){
			if($_parentid == $this->ID){
				return true;
			}
			$_parentid = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($_parentid), '', $this->db);
			$_count++;
			if($_count == 9999){
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
		$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . $this->db->escape($name) . '="' . $this->db->escape(($serialize ? serialize($this->$name) : $this->$name)) . '" WHERE ID=' . intval($this->ID));
		return $this->db->affected_rows();
	}

	function getDynamicEntries(){
		if($this->Selection == self::SELECTION_DYNAMIC){
			switch($this->SelectionType){
				case self::STPYE_DOCTYPE:
					return we_navigation_dynList::getDocuments($this->DocTypeID, $this->FolderID, $this->Categories, $this->CatAnd ? 'AND' : 'OR', $this->Sort, $this->ShowCount, $this->TitleField);
				case self::STPYE_CATEGORY:
					return we_navigation_dynList::getCatgories($this->FolderID, $this->ShowCount);
				default:
					return $this->ClassID > 0 ?
						we_navigation_dynList::getObjects($this->ClassID, $this->FolderID, $this->Categories, $this->CatAnd ? 'AND' : 'OR', $this->Sort, $this->ShowCount, $this->TitleField) :
						array();
			}
		}
	}

	function getChilds(){
		$_items = array();

		$this->db->query('SELECT ID,Path,Text,Ordn FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' ORDER BY Ordn;');

		while($this->db->next_record()){
			$_items[] = array(
				'id' => $this->db->f('ID'),
				'path' => $this->db->f('Path'),
				'text' => $this->db->f('Text'),
				'ordn' => $this->db->f('Ordn')
			);
		}

		return $_items;
	}

	function getDynamicChilds(){
		$_items = array();

		$this->db->query('SELECT ID,Ordn FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' AND IsFolder=0 AND Depended=1 ORDER BY Ordn;');

		while($this->db->next_record()){
			$_items[] = array(
				'id' => $this->db->f('ID'),
				'ordn' => $this->db->f('Ordn')
			);
		}

		return $_items;
	}

	function populateGroup($_items){

		$_info = $this->getDynamicEntries();

		$_new_items = array();

		foreach($_info as $_k => $_item){

			$_navigation = new we_navigation_navigation();

			$_navigation->ParentID = $this->ID;
			$_navigation->Selection = self::SELECTION_STATIC;

			$_navigation->SelectionType = ($this->SelectionType == self::STPYE_DOCTYPE ? self::STPYE_DOCLINK : ($this->SelectionType == self::STPYE_CATEGORY ? self::STPYE_CATLINK : self::STPYE_OBJLINK));
			$_navigation->LinkID = $_item['id'];
			$_navigation->Ordn = isset($_items[$_k]) ? $_items[$_k]['ordn'] : $_k;
			$_navigation->Depended = 1;
			$_navigation->Text = $_item['field'] ? $_item['field'] : $_item['text'];
			$_navigation->IconID = $this->IconID;

			$_navigation->Url = $this->Url;
			$_navigation->UrlID = $this->UrlID;
			$_navigation->CatParameter = $this->CatParameter;
			$_navigation->LinkSelection = $this->LinkSelection;
			$_navigation->Parameter = $this->Parameter;
			$_navigation->WorkspaceID = $this->WorkspaceID;

			$_navigation->save(false);

			$_new_items[] = array(
				'id' => $_navigation->ID,
				'text' => $_navigation->Text
			);
		}

		// delete old items??


		return $_new_items;
	}

	function depopulateGroup(){

		$_items = $this->getDynamicChilds();
		foreach($_items as $_id){
			$_navigation = new we_navigation_navigation($_id['id']);
			if($_navigation->delete()){

			}
		}
		return $_items;
	}

	function hasDynChilds(){
		if(!$this->ID){
			return false;
		}
		return f('SELECT 1 FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' AND Depended=1 LIMIT 1', '', $this->db);
	}

	function hasAnyChilds(){
		if(!$this->ID){
			return false;
		}
		return f('SELECT 1 FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' LIMIT 1', '', $this->db);
	}

	function hasIndependentChilds(){
		if(!$this->ID){
			return false;
		}
		return f('SELECT 1 FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ID) . ' AND Depended=0 LIMIT 1', '', $this->db);
	}

	function getDynamicPreview(&$storage, $rules = false){
		$_items = array();

		foreach($storage['items'] as $item){
			if($item['ParentID'] == $this->ID){
				$_nav = new we_navigation_navigation();
				$_nav->initByRawData($item);
				if($_nav->IsFolder || $_nav->Selection != self::SELECTION_DYNAMIC){
					$_items[] = array(
						'id' => $_nav->ID,
						//'text'=>str_replace('&amp;','&',$_nav->Text),
						'name' => $_nav->Text,
						'text' => (isset($_nav->Display) && !empty($_nav->Display)) ? $_nav->Display : $_nav->Text,
						'display' => (isset($_nav->Display) && !empty($_nav->Display)) ? $_nav->Display : "",
						'docid' => $_nav->LinkID,
						'table' => $_nav->IsFolder ? ($_nav->FolderSelection == self::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE) : (($_nav->SelectionType == self::STPYE_CLASS || $_nav->SelectionType == self::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE)),
						'href' => $_nav->getHref($storage['ids']),
						'type' => $_nav->IsFolder ? we_base_ContentTypes::FOLDER : 'item',
						'parentid' => $_nav->ParentID,
						'workspaceid' => $_nav->WorkspaceID,
						'icon' => isset($storage['ids'][$_nav->IconID]) ? $storage['ids'][$_nav->IconID] : id_to_path($_nav->IconID),
						'attributes' => $_nav->Attributes,
						'customers' => we_navigation_items::getCustomerData($_nav),
						'currentonurlpar' => $_nav->CurrentOnUrlPar,
						'currentonanker' => $_nav->CurrentOnAnker,
						'limitaccess' => $_nav->LimitAccess,
						'depended' => $_nav->Depended
					);
				}

				if($_nav->IsFolder == 0 && $_nav->Selection == self::SELECTION_DYNAMIC){
					$_dyn_items = $_nav->getDynamicEntries();
					foreach($_dyn_items as $_dyn){

						$_href = $_nav->getHref($storage['ids'], $_dyn['id']);
						$_items[] = array(
							'id' => $_nav->ID . '_' . $_dyn['id'],
							//'text'=>str_replace('&amp;','&',!empty($_dyn['field']) ? $_dyn['field'] : $_dyn['text']),
							'name' => $_dyn['field'] ? $_dyn['field'] : $_dyn['text'],
							'text' => $_dyn['field'] ? $_dyn['field'] : $_dyn['text'],
							'display' => isset($_dyn['display']) ? $_dyn['display'] : '',
							'docid' => $_dyn['id'],
							'table' => (($_nav->SelectionType == self::STPYE_CLASS || $_nav->SelectionType == self::STPYE_OBJLINK) ? OBJECT_FILES_TABLE : FILE_TABLE),
							'href' => $_href,
							'type' => 'item',
							'parentid' => $_nav->ParentID,
							'workspaceid' => $_nav->WorkspaceID,
							'icon' => isset($storage['ids'][$_nav->IconID]) ? $storage['ids'][$_nav->IconID] : id_to_path($_nav->IconID),
							'attributes' => $_nav->Attributes,
							'customers' => we_navigation_items::getCustomerData($_nav),
							'currentonurlpar' => $_nav->CurrentOnUrlPar,
							'currentonanker' => $_nav->CurrentOnAnker,
							'limitaccess' => $_nav->LimitAccess,
							'depended' => 2,
						);

						if($rules){
							$_items[(count($_items) - 1)]['currentRule'] = we_navigation_rule::getWeNavigationRule(
									'defined_' . ($_dyn['field'] ? $_dyn['field'] : $_dyn['text']), $_nav->ID, $_nav->SelectionType, $_nav->FolderID, $_nav->DocTypeID, $_nav->ClassID, $_nav->CategoryIDs, $_nav->WorkspaceID, $_href, false);
						}
					}
				}

				if($_nav->IsFolder){
					$_items = array_merge($_items, $_nav->getDynamicPreview($storage, $rules));
				}
			}
		}

		return $_items;
	}

	function reorder($pid){
		$count = 0;
		$this->db->query('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($pid) . ' ORDER BY Ordn');
		$ids = $this->db->getAll(true);
		foreach($ids as $id){
			$this->db->query('UPDATE ' . NAVIGATION_TABLE . ' SET Ordn=' . ($count++) . ' WHERE ID=' . $id);
		}
	}

	function reorderUp(){
		if($this->ID && $this->Ordn > 0){
			$_parentid = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->ID), 'ParentID', $this->db);
			$this->db->query('UPDATE ' . NAVIGATION_TABLE . ' SET Ordn=' . abs($this->Ordn) . ' WHERE ParentID=' . intval($_parentid) . ' AND Ordn=' . abs($this->Ordn - 1));
			$this->Ordn--;
			$this->saveField('Ordn');
			$this->reorder($this->ParentID);
			return true;
		}
		return false;
	}

	function reorderDown(){
		if($this->ID){
			$_parentid = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->ID), '', $this->db);
			$_num = f('SELECT COUNT(ID) as OrdCount FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($_parentid), '', $this->db);
			if($this->Ordn < ($_num - 1)){
				$this->db->query('UPDATE ' . NAVIGATION_TABLE . ' SET Ordn=' . abs($this->Ordn) . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Ordn=' . abs($this->Ordn + 1));
				$this->Ordn++;
				$this->saveField('Ordn');
				$this->reorder($this->ParentID);
				return true;
			}
		}
		return false;
	}

	function getHref(&$storage, $id = 0){
		if($this->IsFolder){
			$_path = '';
			//FIXME: remove eval
			eval('$_param = "' . addslashes(preg_replace('%\\$%', '$this->', $this->FolderParameter)) . '";');
			switch($this->FolderSelection){
				case self::STYPE_URLLINK:
					$_path = $this->FolderUrl;
					break;
				default:
					$objecturl = '';
					if($this->FolderSelection == self::STPYE_OBJLINK){
						if(NAVIGATION_OBJECTSEOURLS){
							$_db = new DB_WE();
							$objectdaten = getHash('SELECT  Url,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->LinkID) . ' LIMIT 1', $_db);
							$objecturl = isset($objectdaten['Url']) ? $objectdaten['Url'] : '';
							$objecttriggerid = isset($objectdaten['TriggerID']) ? $objectdaten['TriggerID'] : 0;
							if(!$objecturl){
								$_param = 'we_objectID=' . $this->LinkID . ($_param ? '&' : '') . $_param;
							}
						} else {
							$_param = 'we_objectID=' . $this->LinkID . ($_param ? '&' : '') . $_param;
						}
						$_id = ($objecttriggerid ? $objecttriggerid : we_navigation_dynList::getFirstDynDocument($this->FolderWsID));
					} else {
						$_id = $this->LinkID;
					}
					$_path = isset($storage[$_id]) ? $storage[$_id] : id_to_path($_id, FILE_TABLE);
					$_path = ($_path === '/' ? '' : $_path);
					if(NAVIGATION_OBJECTSEOURLS && $objecturl != ''){
						$path_parts = pathinfo($_path);
						$_path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' .
							(NAVIGATION_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
								'' : $path_parts['filename'] . '/'
							) . $objecturl;
					}
					break;
			}
		} else {
			$_id = ($id ? $id : $this->LinkID);
			$_path = '';
			//FIXME: remove eval
			eval('$_param = "' . addslashes(preg_replace('%\\$%', '$this->', $this->Parameter)) . '";');

			switch($this->SelectionType){
				case self::STYPE_URLLINK:
					$_path = $this->Url;
					break;
				case self::STPYE_CATEGORY:
				case self::STPYE_CATLINK:
					$_path = $this->LinkSelection === 'extern' ? $this->Url : ($_path = isset($storage[$this->UrlID]) ? $storage[$this->UrlID] : id_to_path($this->UrlID, FILE_TABLE));
					if(!empty($this->CatParameter)){
						$_param = $this->CatParameter . '=' . $_id . (!empty($_param) ? '&' : '') . $_param;
					}
					break;
				default:
					if($this->SelectionType == self::STPYE_CLASS || $this->SelectionType == self::STPYE_OBJLINK){
						$objecturl = '';
						if(NAVIGATION_OBJECTSEOURLS){
							$_db = new DB_WE();
							$objectdaten = getHash('SELECT  Url,TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($_id) . ' LIMIT 1', $_db);
							if(isset($objectdaten['Url'])){
								$objecturl = $objectdaten['Url'];
								$objecttriggerid = $objectdaten['TriggerID'];
							} else {
								$objecturl = '';
								$objecttriggerid = '';
							}
							if(!$objecturl){
								$_param = 'we_objectID=' . $_id . ($_param ? '&' : '') . $_param;
							}
						} else {
							$_param = 'we_objectID=' . $_id . ($_param ? '&' : '') . $_param;
							$objecttriggerid = '';
						}
						$_id = ($objecttriggerid ? $objecttriggerid : we_navigation_dynList::getFirstDynDocument($this->WorkspaceID));
					}

					$_path = isset($storage[$_id]) ? $storage[$_id] : id_to_path($_id, FILE_TABLE);
					$_path = ($_path == '/' ? '' : $_path);
					if(NAVIGATION_OBJECTSEOURLS && isset($objecturl) && $objecturl != ''){
						$path_parts = pathinfo($_path);
						$_path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . (
							(NAVIGATION_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))) ?
								'' : $path_parts['filename'] . '/'
							) . $objecturl;
					}
			}
		}

		if(!is_array($this->Attributes)){
			$this->Attributes = unserialize($this->Attributes);
		}
		$_path = str_replace(' ', '%20', trim($_path)) .
			($_param ? ((strpos($_path, '?') === false ? '?' : '&amp;') . $_param) : '');

		//leave this, because of strpos
		$_path .= (($this->CurrentOnAnker && isset($this->Attributes['anchor']) && !empty($this->Attributes['anchor'])) ? ( (strpos($_path, '?') === false ? '?' : '&amp;') . 'we_anchor=' . $this->Attributes['anchor']) : '') .
			((isset($this->Attributes['anchor']) && !empty($this->Attributes['anchor'])) ? ('#' . $this->Attributes['anchor']) : '');

		$_path = str_replace(array('&amp;', '&'), array('&', '&amp;'), $_path);

		if(NAVIGATION_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES && $this->LinkSelection != self::LSELECTION_EXTERN && $this->SelectionType != self::STYPE_URLLINK){ //Fix #8353
			$dirindexnames = array_map('trim', explode(',', '/' . str_replace(',', ',/', NAVIGATION_DIRECTORYINDEX_NAMES)));
			return str_replace($dirindexnames, '/', $_path);
		}

		return $_path;
	}

	function setOrdn($num){
		$_db = new DB_WE();
		if($this->ID){
			$_db->query('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Ordn>=' . abs($num) . ' ORDER BY Ordn');
			while($_db->next_record()){
				$this->db->query('UPDATE ' . NAVIGATION_TABLE . ' SET Ordn=' . abs($_db->f('Ordn') + 1) . ' WHERE ID=' . intval($_db->f('ID')));
			}
			$this->Ordn = $num;
			$this->saveField('Ordn');
		}
		$this->reorder($this->ParentID);
	}

	function findCharset($pid){
		$_charset = '';
		$_count = 0;
		if($pid == ''){
			$pid = 0;
		}
		$_db = new DB_WE();
		while(!$_charset){
			$_hash = getHash('SELECT ParentID,Charset FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($pid), $_db);
			if(isset($_hash['ParentID'])){
				if(isset($_hash['Charset']) && !empty($_hash['Charset'])){
					$_charset = $_hash['Charset'];
					break;
				}
				$pid = $_hash['ParentID'];
				//prevent deadlocks
				if($_count > 10000){
					break;
				}
				$_count++;
			} else {
				break;
			}
		}

		return $_charset;
	}

	function we_load($id){
		parent::load($id);
		$this->ContentType = 'weNavigation';
	}

	function we_save(){
		$this->save();
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
				$this->$key = in_array($key, $this->serializedFields) ? @unserialize($value) : $value;
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
			$open . 'br\\1' . $close,
			$open . '\\1b' . $close,
			$open . '\\1i' . $close,
			$amp . '\\1;',
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
		$linkType = ($table == OBJECT_FILES_TABLE ? self::STPYE_OBJLINK : self::STPYE_DOCLINK);
		return ' ((IsFolder=1 AND FolderSelection="' . escape_sql_query($linkType) . '") OR (IsFolder=0 AND SelectionType="' . escape_sql_query($linkType) . '")) AND LinkID=' . intval($id) . ' ';
	}

	public static function getWSQuery(){
		if(permissionhandler::hasPerm('ADMINISTRATOR') || !($ws = get_ws(NAVIGATION_TABLE))){
			return '';
		}
		// #5836: Use function get_ws()
		$_wrkNavi = makeArrayFromCSV($ws);

		$_condition = array();
		foreach($_wrkNavi as $_value){
			$_condition[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape(id_to_path($_value, NAVIGATION_TABLE)) . '/%"';
		}
		return ' AND (ID IN(' . implode(',', $_wrkNavi) . ') OR (' . implode(' OR ', $_condition) . '))';
	}

}
