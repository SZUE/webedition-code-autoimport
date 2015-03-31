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
class we_search_search extends we_search_base{
	//for doclist!
	/**
	 * @var integer: number of searchfield-rows
	 */
	var $height;

	/**
	 * @var string: default order of the result columns
	 */
	var $order = 'Text';

	/**
	 * @var string: default number of rows of the result columns
	 */
	var $anzahl = 10;

	/**
	 * @var string: mode if the searchfields are displayed or not, default = 0 (not displayed)
	 */
	var $mode = 0;

	/**
	 * @var string: set view, either iconview (1) or listview (0, default)
	 */
	var $setView = we_search_view::VIEW_LIST;

	/**
	 * @var array with fields to search in
	 */
	var $searchFields = array();

	/**
	 * @var array with operators
	 */
	var $location = array();

	/**
	 * @var array with fields to search for
	 */
	var $search = array();
	private $collectionMetaSearches = array();
	private $usedMedia = array();
	private $usedMediaLinks = array();

	/**
	 * @abstract get data from fields, used in the doclistsearch
	 */
	function initSearchData(){
		$view = we_base_request::_(we_base_request::STRING, 'setView');
		if(isset($GLOBALS['we_doc'])){
			$obj = $GLOBALS['we_doc'];
			$obj->searchclassFolder->searchstart = we_base_request::_(we_base_request::INT, 'searchstart', $obj->searchclassFolder->searchstart);
			$obj->searchclassFolder->mode = we_base_request::_(we_base_request::BOOL, 'mode', $obj->searchclassFolder->mode);
			$obj->searchclassFolder->order = we_base_request::_(we_base_request::STRING, 'order', $obj->searchclassFolder->order);
			$obj->searchclassFolder->anzahl = we_base_request::_(we_base_request::INT, 'anzahl', $obj->searchclassFolder->anzahl);
			$obj->searchclassFolder->location = we_base_request::_(we_base_request::STRING, 'location', $obj->searchclassFolder->location);
			$obj->searchclassFolder->search = we_base_request::_(we_base_request::STRING, 'search', $obj->searchclassFolder->search);

			if($view !== false){
				$this->db->query('UPDATE ' . FILE_TABLE . ' SET viewType="' . $this->db->escape($view) . '" WHERE ID=' . intval($obj->ID));
				$obj->searchclassFolder->setView = $view;
			} else {
				$obj->searchclassFolder->setView = f('SELECT viewType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($obj->ID));
			}

			$searchFields = we_base_request::_(we_base_request::STRING, 'searchFields');
			if($searchFields){
				$obj->searchclassFolder->searchFields = $searchFields;
				$obj->searchclassFolder->height = count($searchFields);
			} else {
				$obj->searchclassFolder->height = (we_base_request::_(we_base_request::INT, 'searchstart') !== false ? 0 : 1);
			}
		} elseif($view !== false && ($id = we_base_request::_(we_base_request::INT, 'id')) !== false){
			$this->db->query('UPDATE ' . FILE_TABLE . ' SET viewType="' . $this->db->escape($view) . '" WHERE ID=' . $id);
		}
	}

	function getModFields(){
		$modFields = array();
		$versions = new we_versions_version();
		foreach($versions->modFields as $k => $v){
			if($k != 'status'){
				$modFields[$k] = $k;
			}
		}

		return $modFields;
	}

	function getUsers(){

		$_db = new DB_WE();
		$vals = array();

		$_db->query('SELECT ID, Text FROM ' . USER_TABLE);
		while($_db->next_record()){
			$v = $_db->f('ID');
			$t = $_db->f('Text');
			$vals[$v] = $t;
		}

		return $vals;
	}

	function getFields($row = 0, $whichSearch = ''){

		$tableFields = array(
			'ID' => g_l('searchtool', '[ID]'),
			'Text' => g_l('searchtool', '[text]'),
			'Path' => g_l('searchtool', '[Path]'),
			'ParentIDDoc' => g_l('searchtool', '[ParentIDDoc]'),
			'ParentIDObj' => g_l('searchtool', '[ParentIDObj]'),
			'ParentIDTmpl' => g_l('searchtool', '[ParentIDTmpl]'),
			'temp_template_id' => g_l('searchtool', '[temp_template_id]'),
			'MasterTemplateID' => g_l('searchtool', '[MasterTemplateID]'),
			'ContentType' => g_l('searchtool', '[ContentType]'),
			//'temp_doc_type' => g_l('searchtool', '[temp_doc_type]'),
			'temp_category' => g_l('searchtool', '[temp_category]'),
			'CreatorID' => g_l('searchtool', '[CreatorID]'),
			'CreatorName' => g_l('searchtool', '[CreatorName]'),
			'WebUserID' => g_l('searchtool', '[WebUserID]'),
			'WebUserName' => g_l('searchtool', '[WebUserName]'),
			'Content' => g_l('searchtool', '[Content]'),
			'Status' => g_l('searchtool', '[Status]'),
			'Speicherart' => g_l('searchtool', '[Speicherart]'),
			'Published' => g_l('searchtool', '[Published]'),
			'CreationDate' => g_l('searchtool', '[CreationDate]'),
			'ModDate' => g_l('searchtool', '[ModDate]'),
			'allModsIn' => g_l('versions', '[allModsIn]'),
			'modifierID' => g_l('versions', '[modUser]')
		);

		if($whichSearch === we_search_view::SEARCH_MEDIA){
			$tableFields = array_merge($this->getFieldsMeta(true), $tableFields);

			unset($tableFields['Text']);
			unset($tableFields['ParentIDObj']);
			unset($tableFields['ParentIDTmpl']);
			unset($tableFields['temp_template_id']);
			unset($tableFields['MasterTemplateID']);
			unset($tableFields['ContentType']);
			unset($tableFields['WebUserID']);
			unset($tableFields['WebUserName']);
			unset($tableFields['Content']);
			unset($tableFields['Status']);
			unset($tableFields['Speicherart']);
			unset($tableFields['Published']);
		}

		if($whichSearch === 'doclist'){
			unset($tableFields['Path']);
			unset($tableFields['ParentIDDoc']);
			unset($tableFields['ParentIDObj']);
			unset($tableFields['ParentIDTmpl']);
			unset($tableFields['MasterTemplateID']);
		}

		if(!permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
			unset($tableFields['ParentIDDoc']);
		}

		if(!defined('OBJECT_FILES_TABLE')){
			unset($tableFields['ParentIDObj']);
		}

		if(!permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
			unset($tableFields['ParentIDObj']);
		}

		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
			unset($tableFields['ParentIDTmpl']);
		}

		if(!permissionhandler::hasPerm('CAN_SEE_TEMPLATES')){
			unset($tableFields['ParentIDTmpl']);
			unset($tableFields['temp_template_id']);
			unset($tableFields['MasterTemplateID']);
		}

		return $tableFields;
	}

	function getFieldsMeta($usePrefix = false, $getTypes = false){
		$_db = new DB_WE();
		$_db->query('SELECT tag,type FROM ' . METADATA_TABLE);
		$ret = array(
			($usePrefix ? 'meta__' : '') . 'Title' => ($getTypes ? 'text' : 'Metadaten: Titel'), // FIXME: G_L()
			($usePrefix ? 'meta__' : '') . 'Description' => ($getTypes ? 'text' : 'Metadaten: Beschreibung'), // FIXME: G_L()
			($usePrefix ? 'meta__' : '') . 'Keywords' => ($getTypes ? 'text' : 'Metadaten: Schlüsselwörter'), // FIXME: G_L()
		);
		while($_db->next_record()){
			$ret[($usePrefix ? 'meta__' : '') . $_db->f('tag')] = $getTypes ? $_db->f('type') : 'Metadaten: ' . $_db->f('tag');
		}

		return $ret;
	}

	function getFieldsStatus(){

		$fields = array(
			'jeder' => g_l('searchtool', '[jeder]'),
			'geparkt' => g_l('searchtool', '[geparkt]'),
			'veroeffentlicht' => g_l('searchtool', '[veroeffentlicht]'),
			'geaendert' => g_l('searchtool', '[geaendert]'),
			'veroeff_geaendert' => g_l('searchtool', '[veroeff_geaendert]'),
			'geparkt_geaendert' => g_l('searchtool', '[geparkt_geaendert]'),
			'deleted' => g_l('searchtool', '[deleted]')
		);

		return $fields;
	}

	function getFieldsSpeicherart(){
		return array(
			'jeder' => g_l('searchtool', '[jeder]'),
			'dynamisch' => g_l('searchtool', '[dynamisch]'),
			'statisch' => g_l('searchtool', '[statisch]')
		);
	}

	static function getLocation($whichField = ''){
		switch($whichField){
			default:
				return array(
					'IS' => g_l('searchtool', '[IS]'),
					'CONTAIN' => g_l('searchtool', '[CONTAIN]'),
					'START' => g_l('searchtool', '[START]'),
					'END' => g_l('searchtool', '[END]'),
					'<' => g_l('searchtool', '[<]'),
					'<=' => g_l('searchtool', '[<=]'),
					'>=' => g_l('searchtool', '[>=]'),
					'>' => g_l('searchtool', '[>]'),
					'IN' => g_l('searchtool', '[IN]'),
				);
			case 'date':
				return array(
					'<' => g_l('searchtool', '[<]'),
					'<=' => g_l('searchtool', '[<=]'),
					'>=' => g_l('searchtool', '[>=]'),
					'>' => g_l('searchtool', '[>]')
				);
			case 'meta':
				return array('IS' => g_l('searchtool', '[IS]'));
		}
	}

	public function getUsedMedia(){
		return $this->usedMedia;
	}

	public function getUsedMediaLinks(){
		return $this->usedMediaLinks;
	}

	function getDoctypes(){
		$_db = new DB_WE();

		$_db->query('SELECT ID,DocType FROM ' . DOC_TYPES_TABLE . ' ' . we_docTypes::getDoctypeQuery($_db));
		return $_db->getAllFirst(false);
	}

	function searchInTitle($keyword, $table){
		$_db2 = new DB_WE();
		//first check published documents
		$_db2->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.Name="Title" AND c.Dat LIKE "%' . $_db2->escape(trim($keyword)) . '%" AND l.DocumentTable="' . stripTblPrefix($table) . '"');
		$titles = $_db2->getAll(true);

		//check unpublished documents
		$_db2->query('SELECT DocumentID, DocumentObject  FROM ' . TEMPORARY_DOC_TABLE . " WHERE DocTable = 'tblFile' AND Active = 1 AND DocumentObject LIKE '%" . $_db2->escape(trim($keyword)) . "%'");
		while($_db2->next_record()){
			$tempDoc = unserialize($_db2->f('DocumentObject'));
			if(isset($tempDoc[0]['elements']['Title']) && $tempDoc[0]['elements']['Title']['dat'] != ''){
				$keyword = str_replace(array('\_', '\%'), array('_', '%'), $keyword);
				if(stristr($tempDoc[0]['elements']['Title']['dat'], $keyword)){
					$titles[] = $_db2->f('DocumentID');
				}
			}
		}

		return ($titles ? ' ' . $table . '.ID IN (' . implode(',', $titles) . ') ' : '');
	}

	function searchCategory($keyword, $table){
		if($table == TEMPLATES_TABLE){
			return ' 0 ';
		}
		$_db = new DB_WE();
		switch($table){
			case FILE_TABLE:
				$field = 'temp_category';
				$field2 = 'Category';
				$query = 'SELECT ID, ' . $field . ', ' . $field2 . '  FROM ' . $table . ' WHERE ((' . $field2 . ' != NULL OR ' . $field2 . " != '') AND Published >= ModDate AND Published !=0) OR (Published < ModDate AND (" . $field . " != NULL OR " . $field . " != '')) ";
				break;
			case VERSIONS_TABLE:
				$field = 'Category';
				$query = 'SELECT ID,' . $field . '  FROM ' . $table . ' WHERE ' . $field . ' != NULL OR ' . $field . " != '' ";
				break;
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$field = 'DefaultCategory';
				$query = 'SELECT ID,' . $field . '  FROM ' . $table . ' WHERE ' . $field . ' != NULL OR ' . $field . " != '' ";
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$field = 'Category';
				$query = 'SELECT ID,' . $field . '  FROM ' . $table . ' WHERE ' . $field . ' != NULL OR ' . $field . " != '' AND Published >= ModDate AND Published !=0";
				break;
		}
		$res = array();
		$res2 = array();

		$_db->query($query);

		switch($table){
			default:
				while($_db->next_record()){
					$res[$_db->f('ID')] = $_db->f($field);
				}
				break;
			case FILE_TABLE:
				while($_db->next_record()){
					$res[$_db->f('ID')] = ($_db->f($field) ? : $_db->f($field2));
				}
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				//search in public objects first and write them in the array
				while($_db->next_record()){
					$res[$_db->f('ID')] = $_db->f($field);
				}
				//search in unpublic objects and write them in the array
				$query2 = 'SELECT DocumentObject  FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocTable = "tblObjectFiles" AND Active=1';
				$_db->query($query2);
				while($_db->next_record()){
					$tempObj = unserialize($_db->f('DocumentObject'));
					if(isset($tempObj[0]['Category']) && $tempObj[0]['Category'] != ''){
						if(!array_key_exists($tempObj[0]['ID'], $res)){
							$res[$tempObj[0]['ID']] = $tempObj[0]['Category'];
						}
					}
				}
				break;
		}

		foreach($res as $k => $v){
			$res2[$k] = makeArrayFromCSV($v);
		}

		$where = '';
		$i = 0;

		$keyword = path_to_id($keyword, CATEGORY_TABLE);

		foreach($res2 as $k => $v){
			foreach($v as $v2){
				//look if the value is numeric
				if(preg_match('=^[0-9]+$=i', $v2)){
					if($v2 == $keyword){
						$where .= ($i > 0 ? ' OR ' : ' AND (') . ' ' . $_db->escape($table) . '.ID=' . intval($k);
						$i++;
					}
				}
			}
		}

		$where .= ($where ? ' )' : ' 0 ');
		return $where;
	}

	function searchSpecial($keyword, $searchFields, $searchlocation){
		$userIDs = array();
		$_db = new DB_WE();
		switch($searchFields){
			case 'CreatorName':
				$_table = USER_TABLE;
				$field = 'Text';
				$fieldFileTable = 'CreatorID';
				break;
			case 'WebUserName':
				$_table = CUSTOMER_TABLE;
				$field = 'Username';
				$fieldFileTable = 'WebUserID';
				break;
		}

		if(isset($searchlocation)){
			switch($searchlocation){
				case 'END' :
					$searching = " LIKE '%" . $_db->escape($keyword) . "' ";
					break;
				case 'START' :
					$searching = " LIKE '" . $_db->escape($keyword) . "%' ";
					break;
				case 'IS' :
					$searching = " = '" . $_db->escape($keyword) . "' ";
					break;
				case 'IN':
					$searching = ' IN ("' . implode('","', array_map('trim', explode(',', $keyword))) . '") ';
					break;
				case '<' :
				case '<=' :
				case '>' :
				case '>=' :
					$searching = ' ' . $searchlocation . " '" . $_db->escape($keyword) . "' ";
					break;
				default :
					$searching = " LIKE '%" . $_db->escape($keyword) . "%' ";
					break;
			}
		}

		$_db->query('SELECT ID FROM ' . $_db->escape($_table) . ' WHERE ' . $field . ' ' . $searching);
		while($_db->next_record()){
			$userIDs[] = ($_db->f('ID'));
		}

		$i = 0;
		if(!$userIDs){
			return '0';
		}

		$where = '';
		foreach($userIDs as $id){
			$where .= ($i > 0 ? ' OR ' : ' (') . $fieldFileTable . '=' . intval($id) . ' ';
			$i++;
		}
		$where .= ')';

		return $where;
	}

	function addToSearchInMeta($search, $field, $location){
		$this->collectionMetaSearches[] = array($search, $field, $location);
	}

	function searchInAllMetas($keyword){
		$_db = new DB_WE();
		$where = '(';
		$c = 0;

		foreach($this->getFieldsMeta() as $k => $v){
			if($v[0] && $v[1]){
				$where .= ($c !== 0 ? 'OR ' : '') . '(l.Name="' . $k . '" AND c.Dat LIKE "%' . $_db->escape($keyword) . '%") ';
				$c++;
			}
		}
		if($c === 0){
			return;
		}
		$where .= ')';

		$_db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE ' . $where . ' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND ' . $where);
		$IDs = $_db->getAll(true);

		return $IDs ? 'ID IN (' . implode(',', $IDs) . ')' : '';
	}

	function searchInMeta($keyword, $searchField, $searchlocation = 'LIKE', $table = ''){
		if($table !== FILE_TABLE){// FIXME: actually no meta search on Versions or unpublished docs!!
			return;
		}
		$_db = new DB_WE();

		$reverse = false;
		if(isset($searchlocation)){
			switch($searchlocation){
				case 'END' :
					$searching = " LIKE '%" . $_db->escape($keyword) . "' ";
					break;
				case 'START' :
					$searching = " LIKE '" . $_db->escape($keyword) . "%' ";
					break;
				case 'IS' :
					$reverse = $keyword === '#EMPTY#' ? : false;
					$searching = " = '" . $_db->escape($keyword) . "' ";
					break;
				case 'IN':
					$searching = ' IN ("' . implode('","', array_map('trim', explode(',', $keyword))) . '") ';
					break;
				case '<' :
				case '<=' :
				case '>' :
				case '>=' :
					$searching = ' ' . $searchlocation . " '" . $_db->escape($keyword) . "' ";
					break;
				default :
					$searching = " LIKE '%" . $_db->escape(trim($keyword)) . "%' ";
					break;
			}
		}

		$_db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.Name="' . $searchField . '" ' . ($reverse ? '' : 'AND c.Dat ' . $searching . '') . ' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
		$IDs = $_db->getAll(true);

		return $IDs ? 'AND ID ' . ($reverse ? 'NOT' : '') . ' IN (' . implode(',', $IDs) . ')' : 'AND 0';
	}

	/*
	function searchIsAttributeNotEmpty($id, $attribute, $table = FILE_TABLE){
		$_db->query('SELECT c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.CID=' . intval($id) . ' AND l.Name="' . $attribute . '" AND l.DocumentTable="' . stripTblPrefix($table) . '"');
		
	}
	*/

	function getStatusFiles($status, $table){//IMI: IMPORTANT: veröffentlichungsstatus grenzt die contenttypes auf djenigen ein, die solch einen status haben!!
		// also kann auch beim verlinkungsstatus auf media-docs eingegremzt werden
		switch($status){
			case "jeder" :
				return "AND (" . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::WEDOCUMENT . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::HTML . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::OBJECT_FILE . "')";

			case "geparkt" :
				return ($table == VERSIONS_TABLE ?
						"AND v.status='unpublished'" :
						"AND ((" . $this->db->escape($table) . ".Published=0) AND (" . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::WEDOCUMENT . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::HTML . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::OBJECT_FILE . "'))");

			case "veroeffentlicht" :
				return ($table == VERSIONS_TABLE ?
						"AND v.status='published'" :
						"AND ((" . $this->db->escape($table) . ".Published >= " . $this->db->escape($table) . ".ModDate AND " . $this->db->escape($table) . ".Published !=0) AND (" . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::WEDOCUMENT . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::HTML . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::OBJECT_FILE . "'))");
			case "geaendert" :
				return ($table == VERSIONS_TABLE ?
						"AND v.status='saved'" :
						"AND ((" . $this->db->escape($table) . ".Published < " . $this->db->escape($table) . ".ModDate AND " . $this->db->escape($table) . ".Published !=0) AND (" . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::WEDOCUMENT . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::HTML . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::OBJECT_FILE . "'))");
			case "veroeff_geaendert" :
				return "AND ((" . $this->db->escape($table) . ".Published >= " . $this->db->escape($table) . ".ModDate OR " . $this->db->escape($table) . ".Published < " . $this->db->escape($table) . ".ModDate AND " . $this->db->escape($table) . ".Published !=0) AND (" . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::WEDOCUMENT . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::HTML . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::OBJECT_FILE . "'))";

			case "geparkt_geaendert" :
				return ($table === VERSIONS_TABLE ?
						"AND v.status!='published'" :
						"AND ((" . $this->db->escape($table) . ".Published=0 OR " . $this->db->escape($table) . ".Published < " . $this->db->escape($table) . ".ModDate) AND (" . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::WEDOCUMENT . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::HTML . "' OR " . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::OBJECT_FILE . "'))");
			case "dynamisch" :
				return ($table !== FILE_TABLE && $table !== VERSIONS_TABLE ? '' :
						"AND ((" . $this->db->escape($table) . ".IsDynamic=1) AND (" . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::WEDOCUMENT . "'))");
			case "statisch" :
				return ($table !== FILE_TABLE && $table !== VERSIONS_TABLE ? '' :
						"AND ((" . $this->db->escape($table) . ".IsDynamic=0) AND (" . $this->db->escape($table) . ".ContentType='" . we_base_ContentTypes::WEDOCUMENT . "'))");
			case "deleted" :
				return ($table === VERSIONS_TABLE ? "AND v.status='deleted' " : '');
		}

		return '';
	}

	function searchModifier($text, $table){
		return ($text ? ' AND ' . $this->db->escape($table) . '.modifierID = ' . intval($text) : '');
	}

	function searchModFields($text, $table){
		$where = "";
		$db = new DB_WE();
		$versions = new we_versions_version();

		$modConst[] = $versions->modFields[$text]['const'];

		if($modConst){
			$modifications = $ids = $_ids = array();
			$db->query('SELECT ID, modifications FROM ' . VERSIONS_TABLE . ' WHERE modifications!=""');

			while($db->next_record()){
				$modifications[$db->f('ID')] = makeArrayFromCSV($db->f('modifications'));
			}
			$m = 0;
			foreach($modConst as $k => $v){
				foreach($modifications as $key => $val){
					if(in_array($v, $modifications[$key])){
						$ids[$m][] = $key;
					}
				}
				$m++;
			}

			if($ids){
				foreach($ids as $key => $val){
					$_ids[] = $val;
				}
				$arr = array();
				if($_ids[0]){
					//more then one field
					$mtof = false;
					foreach($_ids as $k => $v){
						if($k != 0){
							$mtof = true;
							foreach($v as $key => $val){
								if(!in_array($val, $_ids[0])){
									unset($_ids[0][$val]);
								} else {
									$arr[] = $val;
								}
							}
						}
					}
					$where .= ' AND ' . ($mtof ?
							$table . '.ID IN (' . implode(',', $arr) . ') ' :
							($_ids[0] ?
								$table . '.ID IN (' . implode(',', $_ids[0]) . ') ' :
								' 0'));
				}
			}
		}

		return $where;
	}

	function searchContent($keyword, $table){
		$_db = new DB_WE();
		switch($table){
			case FILE_TABLE:
				$_db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE c.Dat LIKE "%' . $this->db->escape(trim($keyword)) . '%" AND l.Name!="completeData" AND l.DocumentTable="' . $_db->escape(stripTblPrefix(FILE_TABLE)) . '"');
				$contents = $_db->getAll(true);

				$_db->query('SELECT DocumentID FROM ' . TEMPORARY_DOC_TABLE . " WHERE DocumentObject LIKE '%" . $_db->escape(trim($keyword)) . "%' AND DocTable='" . $this->db->escape(stripTblPrefix($table)) . "' AND Active = 1");
				$contents = array_unique(array_merge($contents, $_db->getAll(true)));

				return ($contents ? ' ' . $table . '.ID IN (' . implode(',', $contents) . ')' : '');
			case TEMPLATES_TABLE:
				$_db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE c.Dat LIKE "%' . $this->db->escape(trim($keyword)) . '%" AND l.Name="data" AND l.DocumentTable="' . $_db->escape(stripTblPrefix(TEMPLATES_TABLE)) . '"');
				$contents = $_db->getAll(true);

				return ($contents ? ' ' . $table . '.ID IN (' . implode(',', $contents) . ')' : '');
			case VERSIONS_TABLE:
				//FIXME: versions are searched even if the field is not checked!
				$contents = array();

				$_db->query('SELECT ID,documentElements  FROM ' . VERSIONS_TABLE . ' WHERE documentElements!=""');
				while($_db->next_record()){
					$elements = unserialize((substr_compare($_db->f('documentElements'), 'a%3A', 0, 4) == 0 ?
							html_entity_decode(urldecode($_db->f('documentElements')), ENT_QUOTES) :
							gzuncompress($_db->f('documentElements')))
					);

					if(is_array($elements)){
						foreach($elements as $k => $v){
							switch($k){
								case 'Title':
								case 'Charset':
									break;
								default:
									if(isset($v['dat']) &&
										stristr((is_array($v['dat']) ? serialize($v['dat']) : $v['dat']), $keyword)){
										$contents[] = $_db->f('ID');
									}
							}
						}
					}
				}

				return ($contents ? '  ' . $table . '.ID IN (' . implode(',', $contents) . ')' : '');
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$Ids = $regs = array();

				$_db->query('SELECT ID FROM ' . OBJECT_TABLE);
				$_classes = $_db->getAll(true);

				//published objects
				foreach($_classes as $i){
					$_obj_table = OBJECT_X_TABLE . intval($i);
					//$_obj_table = strtolower($_obj_table);
					$tableInfo = $_db->metadata($_obj_table);
					$fields = array();
					for($c = 0; $c < count($tableInfo); $c++){
						if(preg_match('/(.+?)_(.*)/', $tableInfo[$c]['name'], $regs)){
							if($regs[1] != 'OF' && $regs[1] != 'variant'){
								$fields[] = array(
									'name' => $tableInfo[$c]['name'],
									'type' => $regs[1],
									'length' => $tableInfo[$c]['len']
								);
							}
						}
					}
					if(!$fields){
						continue;
					}
					$where = array();
					foreach($fields as $v){
						$where[] = $v['name'] . ' LIKE "%' . $_db->escape(trim($keyword)) . '%"';
					}

					$_db->query('SELECT OF_ID FROM ' . $_db->escape($_obj_table) . ' WHERE ' . implode(' OR ', $where));
					$Ids = array_merge($Ids, $_db->getAll(true));
				}
				//only saved objects
				$_db->query('SELECT DocumentID FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentObject LIKE "%' . $_db->escape(trim($keyword)) . '%" AND DocTable="tblObjectFiles" AND Active=1');
				$Ids = array_merge($Ids, $_db->getAll(true));

				return ($Ids ? '  ' . OBJECT_FILES_TABLE . '.ID IN (' . implode(',', $Ids) . ')' : '');
		}

		return '';
	}

	function searchMediaLinks($useState = 0, $holdAllLinks = true, $inIDs = ''){
		$db = new DB_WE();
		$useState = intval($useState);
		$this->usedMedia = $this->usedMediaLinks = $tmpMediaLInks = $groups = $paths = array();

		$fields = $holdAllLinks ? 'ID,DocumentTable,remObj' : 'DISTINCT remObj';
		$db->query('SELECT ' . $fields . ' FROM ' . FILELINK_TABLE . ' WHERE type="media" AND remTable="' . stripTblPrefix(FILE_TABLE) . '" ' . ($inIDs ? 'AND remObj IN (' . trim($db->escape($inIDs), ',') . ')' : '') . ' AND position=0');

		if($holdAllLinks){
			$types = array(
				FILE_TABLE => 'documents',
				TEMPLATES_TABLE => 'templates',
				OBJECT_FILES_TABLE => 'objects',
				OBJECT_TABLE => 'classes',
				CATEGORY_TABLE => 'categorys'
			);

			while($db->next_record()){
				$rec = $db->getRecord();
				$tmpMediaLInks[$rec['remObj']][] = array($rec['ID'],$rec['DocumentTable']);
				$groups[$rec['DocumentTable']][] = $rec['ID'];
				$this->usedMedia[] = $rec['remObj'];
			}
			foreach($groups as $k => $v){
				$paths[$k] = id_to_path($v, addTblPrefix($k), null, false, true);
			}

			foreach($tmpMediaLInks as $m_id => $v){
				foreach($v as $val){
					$this->usedMediaLinks['mediaID_' . $m_id][$types[addTblPrefix($val[1])]][] = array('id' => $val[0], 'table' => addTblPrefix($val[1]), 'path' => $paths[$val[1]][$val[0]]);
				}
			}
		} else {
			$this->usedMedia = $db->getAll(true);
		}

		if(!$useState){
			return;
		}

		return $this->usedMedia ? (' AND ' . FILE_TABLE . '.ID ' . ($useState === 2 ? 'NOT ' : ' ') . 'IN(' . implode(',', $this->usedMedia) . ')') : ($useState === 2 ? '' : ' AND 0');
	}

	function selectFromTempTable($searchstart, $anzahl, $order){
		$sortIsNr = 'DESC';
		$sortNr = '';
		$sortierung = explode(' ', $order);
		if(isset($sortierung[1])){
			$sortIsNr = '';
			$sortNr = 'DESC';
		}
		if(!$sortierung[0]){
			return;
		}

		$this->db->query('SELECT *,ABS(' . $sortierung[0] . ') AS Nr, (' . $sortierung[0] . " REGEXP '^[0-9]') AS isNr FROM SEARCH_TEMP_TABLE  ORDER BY IsFolder DESC, isNr " . $sortIsNr . ',Nr ' . $sortNr . ',' . $sortierung[0] . ' ' . $sortNr . ', ' . $order . '  LIMIT ' . $searchstart . ',' . $anzahl);
	}

//FIXME path is only implemented for filetable
	function insertInTempTable($where = '', $table = '', $path = ''){
		$this->table = ($table ? : ($this->table ? : ''));

		if(!$this->table){
			return;
		}

		$this->where = ($where ? : ($this->where ? : '1 '));

		switch($this->table){
			case FILE_TABLE:
				$tmpTableWhere = '';
				if($path){
					$this->where .= ' AND Path LIKE "' . $this->db->escape($path) . '%" ';
					$tmpTableWhere = ' AND DocumentID IN (SELECT ID FROM ' . FILE_TABLE . ' WHERE Path LIKE "' . $this->db->escape($path) . '%" )';
				}
				$this->db->query('INSERT INTO SEARCH_TEMP_TABLE SELECT "",ID,"' . FILE_TABLE . '",Text,Path,ParentID,IsFolder,IsProtected,temp_template_id,TemplateID,ContentType,"",CreationDate,CreatorID,ModDate,Published,Extension,"","","","","" FROM `' . FILE_TABLE . '` WHERE ' . $this->where);

				//first check published documents
				$this->db->query('SELECT l.DID,c.Dat FROM `' . LINK_TABLE . '` l JOIN `' . CONTENT_TABLE . '` c ON (l.CID=c.ID) WHERE l.Name="Title" AND l.DocumentTable!="' . stripTblPrefix(TEMPLATES_TABLE) . '"');
				$titles = $this->db->getAllFirst(false);

				//check unpublished documents
				$this->db->query('SELECT DocumentID, DocumentObject  FROM `' . TEMPORARY_DOC_TABLE . '` WHERE DocTable="tblFile" AND Active=1 ' . $tmpTableWhere);
				while($this->db->next_record()){
					$tempDoc = unserialize($this->db->f('DocumentObject'));
					if(isset($tempDoc[0]['elements']['Title'])){
						$titles[$this->db->f('DocumentID')] = $tempDoc[0]['elements']['Title']['dat'];
					}
				}
				if(is_array($titles) && $titles){
					foreach($titles as $k => $v){
						if($v != ""){
							$this->db->query('UPDATE SEARCH_TEMP_TABLE  SET `SiteTitle`="' . $this->db->escape($v) . '" WHERE docID=' . intval($k) . ' AND DocTable="' . FILE_TABLE . '" LIMIT 1');
						}
					}
				}
				break;

			case VERSIONS_TABLE:
				if($_SESSION['weS']['weSearch']['onlyDocs'] || $_SESSION['weS']['weSearch']['ObjectsAndDocs']){
					$query = "INSERT INTO  SEARCH_TEMP_TABLE SELECT '',v.documentID,v.documentTable,v.Text,v.Path,v.ParentID,'','',v.TemplateID,v.ContentType,'',v.timestamp,v.modifierID,'','',v.Extension,v.TableID,v.ID,'','','' " .
						'FROM ' . VERSIONS_TABLE . ' v LEFT JOIN ' . FILE_TABLE . ' f ON v.documentID=f.ID WHERE ' . $this->where . ' ' . $_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere'];
					if(stristr($query, "v.status='deleted'")){
						$query = str_replace(FILE_TABLE . ".", "v.", $query);
					}
					$this->db->query($query);
				}
				if(defined('OBJECT_FILES_TABLE') && ($_SESSION['weS']['weSearch']['onlyObjects'] || $_SESSION['weS']['weSearch']['ObjectsAndDocs'])){
					$query = "INSERT INTO SEARCH_TEMP_TABLE SELECT '',v.documentID,v.documentTable,v.Text,v.Path,v.ParentID,'','',v.TemplateID,v.ContentType,'',v.timestamp,v.modifierID,'','',v.Extension,v.TableID,v.ID,'','','' "
						. 'FROM ' . VERSIONS_TABLE . ' v LEFT JOIN ' . OBJECT_FILES_TABLE . ' f ON v.documentID=f.ID WHERE ' . $this->where . " " . $_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere'];
					if(stristr($query, "v.status='deleted'")){
						$query = str_replace(OBJECT_FILES_TABLE . ".", "v.", $query);
					}
					$this->db->query($query);
				}
				unset($_SESSION['weS']['weSearch']['onlyObjects'], $_SESSION['weS']['weSearch']['onlyDocs'], $_SESSION['weS']['weSearch']['ObjectsAndDocs'], $_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere'], $_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere']);
				break;

			case TEMPLATES_TABLE:
				$this->db->query("INSERT INTO SEARCH_TEMP_TABLE  SELECT '',ID,'" . TEMPLATES_TABLE . "',Text,Path,ParentID,IsFolder,'','',ContentType,Path,CreationDate,CreatorID,ModDate,'',Extension,'','','','','' FROM `" . TEMPLATES_TABLE . "` WHERE " . $this->where);
				break;

			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$this->db->query("INSERT INTO SEARCH_TEMP_TABLE SELECT '',ID,'" . OBJECT_FILES_TABLE . "',Text,Path,ParentID,IsFolder,'','',ContentType,'',CreationDate,CreatorID,ModDate,Published,'',TableID,'','','','' FROM `" . OBJECT_FILES_TABLE . "` WHERE " . $this->where);
				break;

			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$this->db->query("INSERT INTO SEARCH_TEMP_TABLE SELECT '',ID,'" . OBJECT_TABLE . "',Text,Path,ParentID,IsFolder,'','',ContentType,'',CreationDate,CreatorID,ModDate,'','','','','','','' FROM `" . OBJECT_TABLE . "` WHERE " . $this->where);
				break;
		}
	}
	
	function insertMediaAttribsToTempTable(){
		$this->db->query('SELECT docID FROM SEARCH_TEMP_TABLE');
		$IDs = implode(',', $this->db->getAll(true));

		$this->db->query('SELECT l.DID FROM `' . LINK_TABLE . '` l JOIN `' . CONTENT_TABLE . '` c ON (l.CID=c.ID) WHERE l.DID IN (' . $IDs . ') AND l.Name="title" AND l.Type="attrib" AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
		$titles = $this->db->getAll(true);
		if(is_array($titles) && $titles){
			foreach($titles as $k => $v){
				if($v != ""){
					$this->db->query('UPDATE SEARCH_TEMP_TABLE SET `IsTitleSet`=1 WHERE docID=' . intval($k) . ' AND DocTable="' . FILE_TABLE . '" LIMIT 1');
				}
			}
		}

		$this->db->query('SELECT l.DID FROM `' . LINK_TABLE . '` l JOIN `' . CONTENT_TABLE . '` c ON (l.CID=c.ID) WHERE l.DID IN (' . $IDs . ') AND l.Name="alt" AND l.Type="attrib" AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
		$alts = $this->db->getAll(true);
		if(is_array($alts) && $alts){
			foreach($alts as $v){
				$this->db->query('UPDATE SEARCH_TEMP_TABLE SET `IsAltSet`=1 WHERE docID=' . intval($v) . ' AND DocTable="' . FILE_TABLE . '" LIMIT 1');
			}
		}

		$this->searchMediaLinks(0, true, $IDs); // we write $this->usedMediaLinks here, where we allready have final list of found media
		if($this->usedMedia){
			foreach($this->usedMedia as $v){
				$this->db->query('UPDATE SEARCH_TEMP_TABLE SET `IsUsed`=1 WHERE docID=' . intval($v) . ' AND DocTable="' . FILE_TABLE . '" LIMIT 1');
			}
		}
	}

	function createTempTable(){
		$this->db->query('DROP TABLE IF EXISTS SEARCH_TEMP_TABLE');

		if(self::checkRightTempTable() && self::checkRightDropTable()){
			$this->db->query('CREATE TEMPORARY TABLE SEARCH_TEMP_TABLE (
ID BIGINT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
docID BIGINT( 20 ) NOT NULL ,
docTable VARCHAR( 32 ) NOT NULL ,
Text VARCHAR( 255 ) NOT NULL ,
Path VARCHAR( 255 ) NOT NULL ,
ParentID BIGINT( 20 ) NOT NULL ,
IsFolder TINYINT( 1 ) NOT NULL ,
IsProtected TINYINT( 1 ) NOT NULL ,
temp_template_id INT( 11 ) NOT NULL ,
TemplateID INT( 11 ) NOT NULL ,
ContentType VARCHAR( 32 ) NOT NULL ,
SiteTitle VARCHAR( 255 ) NOT NULL ,
CreationDate INT( 11 ) NOT NULL ,
CreatorID BIGINT( 20 ) NOT NULL ,
ModDate INT( 11 ) NOT NULL ,
Published INT( 11 ) NOT NULL ,
Extension VARCHAR( 16 ) NOT NULL ,
TableID INT( 11 ) NOT NULL,
VersionID BIGINT( 20 ) NOT NULL,
IsAltSet TINYINT( 1 ) NOT NULL ,
IsTitleSet TINYINT( 1 ) NOT NULL ,
IsUsed TINYINT( 1 ) NOT NULL ,
UNIQUE KEY k (docID,docTable)
) ENGINE = MEMORY' . we_database_base::getCharsetCollation());
		}
	}

	function searchfor($searchname, $searchfield, $searchlocation, $tablename){
		$operator = ' AND ';
		$this->table = $tablename;
		$sql = '';
		$tableInfo = $GLOBALS['DB_WE']->metadata($this->table);

		$whatParentID = '';
		switch($searchfield){
			case 'ParentIDDoc':
			case 'ParentIDObj':
			case 'ParentIDTmpl':
				$whatParentID = $searchfield;
				$searchfield = 'ParentID';
				break;
			case 'ID':
			case 'CreatorID':
			case 'WebUserID':
				if(!is_numeric($searchname)){
					return ' AND 0';
				}
				break;
		}

		//filter fields for each table
		foreach($tableInfo as $cur){
			if($tablename == VERSIONS_TABLE){
				switch($searchfield){
					case 'ID' :
						$cur['name'] = 'documentID';
						$searchfield = 'documentID';
						break;
					case 'temp_template_id' :
						$searchfield = 'TemplateID';
						break;
					case 'ModDate' :
						$searchfield = 'timestamp';
						break;
				}
			}

			if($searchfield == $cur['name']){
				$searchfield = $tablename . '.' . $cur['name'];

				if(isset($searchname) && $searchname != ''){
					if(($whatParentID === 'ParentIDDoc' && ($this->table == FILE_TABLE || $this->table == VERSIONS_TABLE)) || ($whatParentID === 'ParentIDObj' && ($this->table == OBJECT_FILES_TABLE || $this->table == VERSIONS_TABLE)) || ($whatParentID === 'ParentIDTmpl' && $this->table == TEMPLATES_TABLE)){
						if($this->table == VERSIONS_TABLE){
							if($whatParentID === 'ParentIDDoc'){
								$this->table = FILE_TABLE;
							}
							if(defined('OBJECT_FILES_TABLE') && $whatParentID === 'ParentIDObj'){
								$this->table = OBJECT_FILES_TABLE;
							}
						}
						$searchname = path_to_id($searchname, $this->table);
						$searching = " = '" . $this->db->escape($searchname) . "' ";
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
					} elseif(($searchfield == TEMPLATES_TABLE . '.MasterTemplateID' && $this->table == TEMPLATES_TABLE) || ($searchfield == FILE_TABLE . '.temp_template_id' && $this->table == FILE_TABLE) || ($searchfield == VERSIONS_TABLE . '.TemplateID' && $this->table == VERSIONS_TABLE)){
						$searchname = path_to_id($searchname, TEMPLATES_TABLE);
						$searching = " = '" . $this->db->escape($searchname) . "' ";

						if(($searchfield === 'temp_template_id' && $this->table == FILE_TABLE) || ($searchfield === 'TemplateID' && $this->table == VERSIONS_TABLE)){
							if($this->table == FILE_TABLE){
								$sql .= $this->sqlwhere($tablename . '.TemplateID', $searching, $operator . '( (Published >= ModDate AND Published !=0 AND ') .
									$this->sqlwhere($searchfield, $searching, ' ) OR (Published < ModDate AND ') .
									'))';
							} elseif($this->table == VERSIONS_TABLE){
								$sql .= $this->sqlwhere($tablename . '.TemplateID', $searching, $operator . ' ');
							}
						} else {
							$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						}
					} elseif(stristr($searchfield, '.Published') || stristr($searchfield, '.CreationDate') || stristr($searchfield, '.ModDate')){
						if((stristr($searchfield, '.Published') && $this->table == FILE_TABLE || $this->table == OBJECT_FILES_TABLE) || !stristr($searchfield, '.Published')){
							if($this->table == VERSIONS_TABLE && (stristr($searchfield, '.CreationDate') || stristr($searchfield, '.ModDate'))){
								$searchfield = $this->table . '.timestamp';
							}
							$date = explode('.', $searchname);
							$day = $date[0];
							$month = $date[1];
							$year = $date[2];
							$timestampStart = mktime(0, 0, 0, $month, $day, $year);
							$timestampEnd = mktime(23, 59, 59, $month, $day, $year);

							if(isset($searchlocation)){
								switch($searchlocation){
									case 'IS':
										$searching = ' BETWEEN ' . $timestampStart . ' AND ' . $timestampEnd . ' ';
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
									case '<':
										$searching = ' ' . $searchlocation . " '" . $timestampStart . "' ";
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
									case '<=':
										$searching = ' ' . $searchlocation . " '" . $timestampEnd . "' ";
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
									case '>':
										$searching = ' ' . $searchlocation . " '" . $timestampEnd . "' ";
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
									case '>=':
										$searching = ' ' . $searchlocation . " '" . $timestampStart . "' ";
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
								}
							}
						}
					} elseif(isset($searchlocation)){
						switch($searchlocation){
							case 'END':
								$searching = " LIKE '%" . $this->db->escape($searchname) . "' ";
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
							case 'START':
								$searching = " LIKE '" . $this->db->escape($searchname) . "%' ";
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
							case 'IN':
								$searching = ' IN ("' . implode('","', array_map('trim', explode(',', $searchname))) . '") ';
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
							case 'IS':
								$searching = "='" . $this->db->escape($searchname) . "' ";
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
							case '<':
							case '<=':
							case '>':
							case '>=':
								$searching = ' ' . $searchlocation . " '" . $this->db->escape($searchname) . "' ";
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
							default :
								$searching = " LIKE '%" . $this->db->escape($searchname) . "%' ";
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
						}
					}
				}
			}
		}

		return $sql;
	}

	static function ofFolderAndChildsOnly($folderID, $table){//move this to view class; or verse visa
		$DB_WE = new DB_WE();
		$_SESSION['weS']['weSearch']['countChilds'] = array();
		$childsOfFolderId = array();
		//fix #2940
		if(is_array($folderID)){
			foreach($folderID as $k){
				$childsOfFolderId = self::getChildsOfParentId($k, $table, $DB_WE);
				$ids = implode(',', $childsOfFolderId);
			}
			return ' AND ' . $table . '.ParentID IN (' . $ids . ')';
		}
		$childsOfFolderId = self::getChildsOfParentId($folderID, $table, $DB_WE);
		$ids = implode(',', $childsOfFolderId);

		return ' AND ' . $table . '.ParentID IN (' . $ids . ')';
	}

	private static function getChildsOfParentId($folderID, $table, we_database_base $DB_WE){
		if($table === VERSIONS_TABLE){ //we don't have parents & folders
			return $_SESSION['weS']['weSearch']['countChilds'];
		}

		$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($folderID) . ' AND IsFolder=1');
		$ids = $DB_WE->getAll(true);
		$_SESSION['weS']['weSearch']['countChilds'] = array_merge($_SESSION['weS']['weSearch']['countChilds'], $ids);

		foreach($ids as $id){
			self::getChildsOfParentId($id, $table, $DB_WE);
		}

		$_SESSION['weS']['weSearch']['countChilds'][] = $folderID;
		// doppelte Eintr�ge aus array entfernen
		$_SESSION['weS']['weSearch']['countChilds'] = array_values(
			array_unique($_SESSION['weS']['weSearch']['countChilds']));

		return $_SESSION['weS']['weSearch']['countChilds'];
	}

	static function checkRightTempTable(){
		$db = new DB_WE();
		$db->query('CREATE TEMPORARY TABLE test_SEARCH_TEMP_TABLE (
				`test` VARCHAR(1) NOT NULL
				) ENGINE=MEMORY' . we_database_base::getCharsetCollation());

		$db->next_record();

		$return = (stristr($db->Error, 'Access denied') ? false : true);

		$db->query('DROP TABLE IF EXISTS test_SEARCH_TEMP_TABLE');

		return $return;
	}

	static function checkRightDropTable(){
		$db = new DB_WE();

		$db->query('CREATE TABLE IF NOT EXISTS test_SEARCH_TEMP_TABLE (
				`test` VARCHAR( 1 ) NOT NULL
				) ENGINE=MEMORY' . we_database_base::getCharsetCollation());
		$db->next_record();

		$db->query('DROP TABLE IF EXISTS test_SEARCH_TEMP_TABLE');

		return (stristr($db->Error, 'command denied') ? false : true);
	}

	function getResultCount(){
		return f('SELECT COUNT(1) AS Count FROM SEARCH_TEMP_TABLE', '', $this->db);
	}

}
