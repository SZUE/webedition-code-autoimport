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
	var $searchFields = [];

	/**
	 * @var array with operators
	 */
	var $location = [];

	/**
	 * @var array with fields to search for
	 */
	var $search = [];
	private $collectionMetaSearches = [];
	private $usedMedia = [];
	private $usedMediaLinks = [];
	public $founditems = 0;
	public $View;

	public function __construct(we_modules_view $view = null){
		parent::__construct();
		$this->View = $view ?: new we_search_view();
	}

	function searchProperties($whichSearch, $model){
		$DB_WE = new DB_WE();
		$workspaces = $result = $versionsFound = $saveArrayIds = [];
		$_SESSION['weS']['weSearch']['foundItems' . $whichSearch] = 0; // will be obsolete
		$searchFields = $model->getProperty('currentSearchFields');
		$searchText = $model->getProperty('currentSearch');
		$location = $model->getProperty('currentLocation');
		$folderID = $model->getProperty('currentFolderID');
		$order = $model->getProperty('currentOrder');
		$view = $model->getProperty('currentSetView');
		$searchstart = $model->getProperty('currentSearchstart');
		$anzahl = $model->getProperty('currentAnzahl');
		$tables = $model->getProperty('currentSearchTables');
		$searchForField = $model->getProperty('currentSearchForField');
		$searchForContentType = $model->getProperty('currentSearchForContentType');

		$searchText = (is_array($searchText) ?
				array_map('trim', $searchText) :
				[]);

		$tab = we_base_request::_(we_base_request::INT, 'tab', we_base_request::_(we_base_request::INT, 'tabnr', 1)); //init activTab like this

		if(isset($searchText[0]) && substr($searchText[0], 0, 4) === 'exp:'){
			$result = $this->View->searchclassExp->getSearchResults($searchText[0], $tables);
			if($result){
				foreach($result as $k => $v){
					foreach($v as $key => $val){
						switch($key){
							case "Table":
							case 'ID':
								unset($result[$k][$key]);
								$result[$k]['doc' . $key] = $val;
						}
					}
					$result[$k]['SiteTitle'] = "";
				}
				$_SESSION['weS']['weSearch']['foundItems' . $whichSearch] = count($result);
			}
		} elseif(
				($model->IsFolder != 1 && ( ($whichSearch === we_search_view::SEARCH_DOCS && $tab === 1) || ($whichSearch === we_search_view::SEARCH_TMPL && $tab === 2) || ($whichSearch === we_search_view::SEARCH_ADV && $tab === 3)) || ($whichSearch === we_search_view::SEARCH_MEDIA && $tab === 5) ) ||
				(we_base_request::_(we_base_request::INT, 'cmdid')) ||
				(($view = we_base_request::_(we_base_request::STRING, 'view')) === "GetSearchResult" || $view === "GetMouseOverDivs")
		){

			$this->createTempTable();
			$opAND = ($whichSearch === we_search_view::SEARCH_ADV || $whichSearch === we_search_view::SEARCH_MEDIA);

			foreach($tables as $table){
				$this->settable($table);

				if(!defined('OBJECT_TABLE') || (defined('OBJECT_TABLE') && $table != OBJECT_TABLE)){
					$workspaces = get_ws($table, true);
				}

				$where = $this->getSearchString($table, $tables, $opAND, $searchFields, $whichSearch, $searchForField, $searchText, $location, $searchForContentType, $DB_WE, $view);

				if($where){

					if(isset($folderID) && ($folderID != '' && $folderID != 0)){
						// FIXME: search for Text shoukd come without AND!!
						$where[] = we_search_search::ofFolderAndChildsOnly($folderID, $table);
					}

					if($table === VERSIONS_TABLE){
						$workspacesTblFile = get_ws(FILE_TABLE, true);
						if(defined('OBJECT_FILES_TABLE')){
							$workspacesObjFile = get_ws(OBJECT_FILES_TABLE, true);
						}
					}

					if($workspaces){
						$where[] = we_search_search::ofFolderAndChildsOnly($workspaces, $table);
					}

					$whereQuery = $where;

					//query for restrict users for FILE_TABLE, VERSIONS_TABLE AND OBJECT_FILES_TABLE
					$restrictUserQuery = '(WETABLE.RestrictOwners IN(0,' . intval($_SESSION['user']["ID"]) . ') OR FIND_IN_SET(' . intval($_SESSION['user']["ID"]) . ',WETABLE.Owners))';

					switch($table){
						case FILE_TABLE:
							$whereQuery[] = $restrictUserQuery;
							break;

						case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
							$whereQuery[] = $restrictUserQuery;
							break;

						case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
							$whereQuery[] = '(o.RestrictUsers=0 OR o.CreatorID=' . intval($_SESSION['user']["ID"]) . ' OR FIND_IN_SET(' . intval($_SESSION['user']["ID"]) . ',o.Users)) ';
							break;
						case VERSIONS_TABLE:
							$_SESSION['weS']['weSearch']['onlyObjects'] = true;
							$_SESSION['weS']['weSearch']['onlyDocs'] = true;
							$_SESSION['weS']['weSearch']['ObjectsAndDocs'] = true;
							$_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere'] = ' AND (WETABLE.RestrictOwners=0 OR WETABLE.CreatorID=' . intval($_SESSION['user']["ID"]) . ' OR FIND_IN_SET(' . intval($_SESSION['user']["ID"]) . ',WETABLE.Owners))';
							$_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere'] = ' AND (WETABLE.RestrictOwners=0 OR WETABLE.CreatorID=' . intval($_SESSION['user']["ID"]) . ' OR FIND_IN_SET(' . intval($_SESSION['user']["ID"]) . ',WETABLE.Owners))';
							if(!empty($workspacesTblFile)){
								$_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere'] .= (implode(' AND ', $where) . ' AND ' . self::ofFolderAndChildsOnly($workspacesTblFile[0], $table));
							}
							if(isset($workspacesObjFile) && !empty($workspacesObjFile)){
								$_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere'] .= (implode(' AND ', $where) . ' AND ' . self::ofFolderAndChildsOnly($workspacesObjFile[0], $table));
							}

							/* 	if(!$isCheckedFileTable && $isCheckedObjFileTable){
							  $_SESSION['weS']['weSearch']['onlyDocs'] = false;
							  $whereQuery .= ' AND ' . escape_sql_query($table) . '.documentTable="' . OBJECT_FILES_TABLE . '" ';
							  $_SESSION['weS']['weSearch']['ObjectsAndDocs'] = false;
							  }
							  if($isCheckedFileTable && !$isCheckedObjFileTable){
							  $_SESSION['weS']['weSearch']['onlyObjects'] = false;
							  $whereQuery .= ' AND ' . escape_sql_query($table) . '.documentTable="' . FILE_TABLE . '" ';
							  $_SESSION['weS']['weSearch']['ObjectsAndDocs'] = false;
							  } */
							break;
					}
					$whereQuery = implode(' AND ', array_filter($whereQuery));
					$this->setwhere($whereQuery);
					$this->insertInTempTable($whereQuery, $table);

					// when MediaSearch add attrib_alt, attrib_title, IsUsed to SEARCH_TEMP_TABLE
					if($whichSearch === we_search_view::SEARCH_MEDIA){
						$this->insertMediaAttribsToTempTable();
					}
				}
			}

			$this->selectFromTempTable($searchstart, $anzahl, $order);

			while($this->next_record()){
				if(!empty($this->Record['VersionID'])){

					$versionsFound[] = [
						$this->Record['ContentType'],
						$this->Record['docID'],
						$this->Record['VersionID']
					];
				}
				if(!isset($saveArrayIds[$this->Record['ContentType']][$this->Record['docID']])){
					$saveArrayIds[$this->Record['ContentType']][$this->Record['docID']] = $this->Record['docID'];

					$result[] = array_merge(['Table' => $table], ['foundInVersions' => ""], $this->Record);
				}
			}

			foreach($versionsFound as $k => $v){
				foreach($result as $key => $val){
					if(isset($result[$key]['foundInVersions']) && isset($result[$key]['docID']) && $result[$key]['docID'] == $v[1] && isset($result[$key]['ContentType']) && $result[$key]['ContentType'] == $v[0]){
						if($result[$key]['foundInVersions'] != ""){
							$result[$key]['foundInVersions'] .= ",";
						}
						$result[$key]['foundInVersions'] .= $v[2];
					}
				}

				$this->selectFromTempTable($searchstart, $anzahl, $order);
				while($this->next_record()){
					if(!isset($saveArrayIds[$this->Record['ContentType']][$this->Record['docID']])){
						$saveArrayIds[$this->Record['ContentType']][$this->Record['docID']] = $this->Record['docID'];
						$result[] = array_merge(['Table' => $table], $this->Record);
					}
				}
			}

			$_SESSION['weS']['weSearch']['foundItems' . $whichSearch] = $this->founditems = $this->getResultCount();
		}

		if($_SESSION['weS']['weSearch']['foundItems' . $whichSearch] == 0){
			return [];
		}

		foreach($result as $k => $v){
			$result[$k]["Description"] = '';

			switch(addTblPrefix($result[$k]['docTable'])){
				case FILE_TABLE:
					if($result[$k]['Published'] >= $result[$k]['ModDate'] && $result[$k]['Published'] != 0){
						$result[$k]['Description'] = f('SELECT c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DID=' . intval($result[$k]["docID"]) . ' AND l.nHash=x\'' . md5('Description') . '\' AND l.DocumentTable="' . FILE_TABLE . '"', '', $DB_WE);
						break;
					}
					$tempDoc = f('SELECT DocumentObject  FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID =' . intval($result[$k]["docID"]) . ' AND docTable="tblFile" AND Active = 1', '', $DB_WE);
					if(!empty($tempDoc)){
						$tempDoc = we_unserialize($tempDoc);
						if(isset($tempDoc[0]['elements']['Description']) && $tempDoc[0]['elements']['Description']['dat'] != ''){
							$result[$k]["Description"] = $tempDoc[0]['elements']['Description']['dat'];
						}
					}
					break;
				default:
					$result[$k]['Description'] = '';
			}
		}

		return $this->View->makeContent($result, $view, $whichSearch);
	}

	function getModFields(){
		$modFields = [];
		$versions = new we_versions_version();
		foreach(array_keys($versions->modFields) as $k){
			if($k != 'status'){
				$modFields[$k] = $k;
			}
		}

		return $modFields;
	}

	function getUsers(){
		$db = new DB_WE();
		return $db->getAllFirstq('SELECT ID, username FROM ' . USER_TABLE, false);
	}

	function getFields($row = 0, $whichSearch = ''){
		$tableFields = ['ID' => g_l('searchtool', '[ID]'),
			'Text' => g_l('searchtool', '[text]'),
			'Path' => g_l('searchtool', '[Path]'),
			'ParentIDDoc' => g_l('searchtool', '[ParentIDDoc]'),
			'ParentIDObj' => g_l('searchtool', '[ParentIDObj]'),
			'ParentIDTmpl' => g_l('searchtool', '[ParentIDTmpl]'),
			'temp_template_id' => g_l('searchtool', '[temp_template_id]'),
			'MasterTemplateID' => g_l('searchtool', '[MasterTemplateID]'),
			'ContentType' => g_l('searchtool', '[ContentType]'),
			'HasReferenceToID' => g_l('searchtool', '[HasReferenceToID]'),
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
			//'allModsIn' => g_l('versions', '[allModsIn]'),
			'modifierID' => g_l('versions', '[modUser]')
		];

		if($whichSearch === we_search_view::SEARCH_MEDIA){
			$tableFields = array_merge($this->getFieldsMeta(true), $tableFields);
			unset($tableFields['Text']);
			unset($tableFields['ParentIDObj']);
			unset($tableFields['ParentIDTmpl']);
			unset($tableFields['temp_template_id']);
			unset($tableFields['MasterTemplateID']);
			//unset($tableFields['ContentType']);
			unset($tableFields['WebUserID']);
			unset($tableFields['WebUserName']);
			unset($tableFields['Content']);
			unset($tableFields['Status']);
			unset($tableFields['Speicherart']);
			unset($tableFields['Published']);
			unset($tableFields['HasReferenceToID']);
		} elseif($whichSearch === we_search_view::SEARCH_DOCLIST){
			unset($tableFields['Path']);
			unset($tableFields['ParentIDDoc']);
			unset($tableFields['ParentIDObj']);
			unset($tableFields['ParentIDTmpl']);
			unset($tableFields['MasterTemplateID']);
			unset($tableFields['HasReferenceToID']);
		}

		if(!we_base_permission::hasPerm('CAN_SEE_DOCUMENTS')){
			unset($tableFields['ParentIDDoc']);
		}

		if(!defined('OBJECT_FILES_TABLE')){
			unset($tableFields['ParentIDObj']);
		}

		if(!we_base_permission::hasPerm('CAN_SEE_OBJECTFILES')){
			unset($tableFields['ParentIDObj']);
		}

		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
			unset($tableFields['ParentIDTmpl']);
		}

		if(!we_base_permission::hasPerm('CAN_SEE_TEMPLATES')){
			unset($tableFields['ParentIDTmpl']);
			unset($tableFields['temp_template_id']);
			unset($tableFields['MasterTemplateID']);
		}

		return $tableFields;
	}

	function getFieldsMeta($usePrefix = false, $getTypes = false){
		$db = new DB_WE();
		$db->query('SELECT tag,type FROM ' . METADATA_TABLE);
		$ret = [($usePrefix ? 'meta__' : '') . 'Title' => ($getTypes ? 'text' : g_l('searchtool', '[metadata][field]')),
			($usePrefix ? 'meta__' : '') . 'Description' => ($getTypes ? 'text' : g_l('searchtool', '[metadata][description]')),
			($usePrefix ? 'meta__' : '') . 'Keywords' => ($getTypes ? 'text' : g_l('searchtool', '[metadata][keyword]')),
		];
		while($db->next_record()){
			$ret[($usePrefix ? 'meta__' : '') . $db->f('tag')] = $getTypes ? $db->f('type') : 'Metadaten: ' . $db->f('tag');
		}

		return $ret;
	}

	function getFieldsStatus(){
		return ['jeder' => g_l('searchtool', '[jeder]'),
			'geparkt' => g_l('searchtool', '[geparkt]'),
			'veroeffentlicht' => g_l('searchtool', '[veroeffentlicht]'),
			'geaendert' => g_l('searchtool', '[geaendert]'),
			'veroeff_geaendert' => g_l('searchtool', '[veroeff_geaendert]'),
			'geparkt_geaendert' => g_l('searchtool', '[geparkt_geaendert]'),
			'deleted' => g_l('searchtool', '[deleted]')
		];
	}

	function getFieldsSpeicherart(){
		return ['jeder' => g_l('searchtool', '[jeder]'),
			'dynamisch' => g_l('searchtool', '[dynamisch]'),
			'statisch' => g_l('searchtool', '[statisch]')
		];
	}

	static function getLocation($whichField = '', $select = '', $size = 1){
		switch($whichField){
			default:
				return ['IS' => g_l('searchtool', '[IS]'),
					'CONTAIN' => g_l('searchtool', '[CONTAIN]'),
					'START' => g_l('searchtool', '[START]'),
					'END' => g_l('searchtool', '[END]'),
					'LO' => g_l('searchtool', '[<]'),
					'LEQ' => g_l('searchtool', '[<=]'),
					'HEQ' => g_l('searchtool', '[>=]'),
					'HI' => g_l('searchtool', '[>]'),
					'IN' => g_l('searchtool', '[IN]'),
				];
			case 'text':
				return ['IS' => g_l('searchtool', '[IS]'),
					'CONTAIN' => g_l('searchtool', '[CONTAIN]'),
					'START' => g_l('searchtool', '[START]'),
					'END' => g_l('searchtool', '[END]'),
					'IN' => g_l('searchtool', '[IN]'),
				];
			case 'date':
				return ['IS' => g_l('searchtool', '[IS]'),
					'LO' => g_l('searchtool', '[<]'),
					'LEQ' => g_l('searchtool', '[<=]'),
					'HEQ' => g_l('searchtool', '[>=]'),
					'HI' => g_l('searchtool', '[>]'),
					'IN' => g_l('searchtool', '[IN]'),
				];
			case 'meta':
				return ['IS' => g_l('searchtool', '[IS]')];
		}
	}

	public function getUsedMedia(){
		return $this->usedMedia;
	}

	public function getUsedMediaLinks(){
		return $this->usedMediaLinks;
	}

	function getDoctypes(){
		$db = new DB_WE();

		$dtq = we_docTypes::getDoctypeQuery($db);
		$db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		return $db->getAllFirst(false);
	}

	private function searchInTitle($keyword, $table){
		$db2 = new DB_WE();
		//first check published documents
		$titles = [];

		//check unpublished documents
		$db2->query('SELECT DocumentID, DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE docTable="tblFile" AND Active=1 AND DocumentObject LIKE "%' . $db2->escape(trim($keyword)) . '%"');
		while($db2->next_record()){
			$tempDoc = we_unserialize($db2->f('DocumentObject'));
			if(!empty($tempDoc[0]['elements']['Title'])){
				$keyword = strtr($keyword, ['\_' => '_', '\%' => '%']);
				if(stristr($tempDoc[0]['elements']['Title']['dat'], $keyword)){
					$titles[] = $db2->f('DocumentID');
				}
			}
		}

		return ' (WETABLE.ID IN (SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.nHash=x\'' . md5("Title") . '\' AND c.Dat LIKE "%' . $db2->escape(trim($keyword)) . '%" AND l.DocumentTable="' . stripTblPrefix($table) . '") ' . ($titles ? ' OR WETABLE.ID IN (' . implode(',', $titles) . ')' : '') . ' )';
	}

	function searchCategory($keyword, $table){
		if($table == TEMPLATES_TABLE){
			return ' 0 ';
		}
		$db = new DB_WE();
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
		$res = $res2 = [];

		$db->query($query);

		switch($table){
			default:
				while($db->next_record()){
					$res[$db->f('ID')] = $db->f($field);
				}
				break;
			case FILE_TABLE:
				while($db->next_record()){
					$res[$db->f('ID')] = ($db->f($field) ?: $db->f($field2));
				}
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				//search in public objects first and write them in the array
				while($db->next_record()){
					$res[$db->f('ID')] = $db->f($field);
				}
				//search in unpublic objects and write them in the array
				$query2 = 'SELECT DocumentObject  FROM ' . TEMPORARY_DOC_TABLE . ' WHERE docTable="tblObjectFiles" AND Active=1';
				$db->query($query2);
				while($db->next_record()){
					$tempObj = we_unserialize($db->f('DocumentObject'));
					if(!empty($tempObj[0]['Category'])){
						if(!array_key_exists($tempObj[0]['ID'], $res)){
							$res[$tempObj[0]['ID']] = $tempObj[0]['Category'];
						}
					}
				}
				break;
		}

		foreach($res as $k => $v){
			$res2[$k] = array_filter(explode(',', $v));
		}

		$whereIn = [];

		$keyword = path_to_id($keyword, CATEGORY_TABLE);

		foreach($res2 as $k => $v){
			foreach($v as $v2){
				//look if the value is numeric
				if(preg_match('=^[0-9]+$=i', $v2)){
					if($v2 == $keyword){
						$whereIn[] = intval($k);
					}
				}
			}
		}

		return ($whereIn ? 'WETABLE.ID IN(' . implode(',', $whereIn) . ')' : ' 0 ');
	}

	function searchSpecial($keyword, $searchFields, $searchlocation){
		$db = new DB_WE();
		switch($searchFields){
			case 'CreatorName':
				$table = USER_TABLE;
				$field = 'username';
				$fieldFileTable = 'CreatorID';
				break;
			case 'WebUserName':
				$table = CUSTOMER_TABLE;
				$field = 'Username';
				$fieldFileTable = 'WebUserID';
				break;
		}

		if(isset($searchlocation)){
			switch($searchlocation){
				case 'END' :
					$searching = ' LIKE "%' . $db->escape($keyword) . '" ';
					break;
				case 'START' :
					$searching = ' LIKE "' . $db->escape($keyword) . '%" ';
					break;
				case 'IS' :
					$searching = " = '" . $db->escape($keyword) . "' ";
					break;
				case 'IN':
					$searching = ' IN ("' . implode('","', array_map('trim', explode(',', $keyword))) . '") ';
					break;
				case 'LO' :
					$searching = ' < "' . $db->escape($keyword) . '" ';
					break;
				case 'LEQ' :
					$searching = ' <= "' . $db->escape($keyword) . '" ';
					break;
				case 'HI' :
					$searching = ' > "' . $db->escape($keyword) . '" ';
					break;
				case 'HEQ' :
					$searching = ' >= "' . $db->escape($keyword) . '" ';
					break;
				default :
					$searching = ' LIKE "%' . $db->escape($keyword) . '%" ';
					break;
			}
		}

		$userIDs = $db->getAllq('SELECT ID FROM ' . $db->escape($table) . ' WHERE ' . $field . ' ' . $searching, true);

		return ($userIDs ? $fieldFileTable . ' IN (' . implode(',', $userIDs) . ')' : ' 0 ');
	}

	function addToSearchInMeta($search, $field, $location){
		$this->collectionMetaSearches[] = [$search, $field, $location];
	}

	function searchInAllMetas($keyword, $table = ''){
		if($table !== FILE_TABLE){// FIXME: actually no meta search on Versions or unpublished docs!!
			return;
		}
		$db = new DB_WE();
		$names = [];
		foreach($this->getFieldsMeta() as $k => $v){
			if($v[0] && $v[1]){
				$names[] = $k;
			}
		}
		if(empty($names)){
			return;
		}
		$n = array_map('md5', $names);
		$db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND (l.Name IN (x\'' . implode('\',x\'', $n) . '\') AND c.Dat LIKE "%' . $db->escape($keyword) . '%")');
		$IDs = $db->getAll(true);

		return $IDs ? 'WETABLE.ID IN (' . implode(',', $IDs) . ')' : '0';
	}

	private function searchInMeta($keyword, $searchField, $searchlocation, $table, $db){
		if($table !== FILE_TABLE){// FIXME: actually no meta search on Versions or unpublished docs!!
			return '';
		}
		$reverse = false;
		if(isset($searchlocation)){
			switch($searchlocation){
				case 'END' :
					$searching = ' LIKE "%' . $db->escape($keyword) . '" ';
					break;
				case 'START' :
					$searching = ' LIKE "' . $db->escape($keyword) . '%" ';
					break;
				case 'IS' :
					$reverse = $keyword === '##EMPTY##' ?: false;
					$searching = " = '" . $db->escape($keyword) . "' ";
					break;
				case 'IN':
					$searching = ' IN ("' . implode('","', array_map('trim', explode(',', $keyword))) . '") ';
					break;
				case 'LO' :
					$searching = ' < "' . $db->escape($keyword) . '" ';
					break;
				case 'LEQ' :
					$searching = ' <= "' . $db->escape($keyword) . '" ';
					break;
				case 'HI' :
					$searching = ' > "' . $db->escape($keyword) . '" ';
					break;
				case 'HEQ' :
					$searching = ' >= "' . $db->escape($keyword) . '" ';
					break;
				default :
					$searching = ' LIKE "%' . $db->escape(trim($keyword)) . '%" ';
					break;
			}
		}

		$db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.nHash=x\'' . md5($searchField) . '\' ' . ($reverse ? '' : 'AND c.Dat ' . $searching) . ' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
		$IDs = $db->getAll(true);

		return $IDs ? 'WETABLE.ID ' . ($reverse ? 'NOT' : '') . ' IN (' . implode(',', $IDs) . ')' : ' 0 ';
	}

	protected function getStatusFiles($status, $table){//IMI: IMPORTANT: veröffentlichungsstatus grenzt die contenttypes auf djenigen ein, die solch einen status haben!!
		// also kann auch beim verlinkungsstatus auf media-docs eingegremzt werden
		switch($status){
			case "jeder" :
				$ret = 'WETABLE.ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '")';
				break;
			case "geparkt" :
				$ret = ($table == VERSIONS_TABLE ?
						'v.status="unpublished"' :
						'(WETABLE.Published=0 AND WETABLE.ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '"))');
				break;
			case "veroeffentlicht" :
				$ret = ($table == VERSIONS_TABLE ?
						'v.status="published"' :
						'(WETABLE.Published >=WETABLE.ModDate AND WETABLE.Published !=0 AND WETABLE.ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '"))');
				break;
			case "geaendert" :
				$ret = ($table == VERSIONS_TABLE ?
						'v.status="saved"' :
						'(WETABLE.Published<WETABLE.ModDate AND WETABLE.Published!=0 AND WETABLE.ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '"))');
				break;
			case "veroeff_geaendert" :
				$ret = '((WETABLE.Published>=WETABLE.ModDate OR WETABLE.Published < WETABLE.ModDate AND WETABLE.Published !=0) AND WETABLE.ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '") )';
				break;
			case "geparkt_geaendert" :
				$ret = ($table === VERSIONS_TABLE ?
						'v.status!="published"' :
						'((WETABLE.Published=0 OR WETABLE.Published< WETABLE.ModDate) AND WETABLE.ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '") )');
				break;
			case "dynamisch" :
				$ret = ($table !== FILE_TABLE && $table !== VERSIONS_TABLE ? '' :
						'(WETABLE.IsDynamic=1 AND WETABLE.ContentType="' . we_base_ContentTypes::WEDOCUMENT . '")');
				break;
			case "statisch" :
				$ret = ($table !== FILE_TABLE && $table !== VERSIONS_TABLE ? '' :
						'(WETABLE.IsDynamic=0 AND WETABLE.ContentType="' . we_base_ContentTypes::WEDOCUMENT . '")');
				break;
			case "deleted" :
				$ret = ($table !== VERSIONS_TABLE ? '' :
						'v.status="deleted"' );
				break;
			case "default":
				$ret = 1;
		}

		return $ret;
	}

	private function searchModifier($text){
		return ($text ? 'WETABLE.modifierID=' . intval($text) : '');
	}

	private function searchModFields($text, $db){
		$where = [];
		$versions = new we_versions_version();

		$modConst[] = $versions->modFields[$text]['const'];

		if($modConst){
			$modifications = $ids = $myIds = [];
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
					$myIds[] = $val;
				}
				$arr = [];
				if($myIds[0]){
					//more then one field
					$mtof = false;
					foreach($myIds as $k => $v){
						if($k != 0){
							$mtof = true;
							foreach($v as $key => $val){
								if(!in_array($val, $myIds[0])){
									unset($myIds[0][$val]);
								} else {
									$arr[] = $val;
								}
							}
						}
					}
					$where[] = ($mtof ?
							'WETABLE.ID IN (' . implode(',', $arr) . ') ' :
							($myIds[0] ?
							'WETABLE.ID IN (' . implode(',', $myIds[0]) . ') ' :
							' 0 '));
				}
			}
		}

		return implode(' AND ', $where);
	}

	function searchContent($keyword, $table){
		$db = new DB_WE();
		switch($table){
			case FILE_TABLE:
				$ws = we_selector_file::getWsQuery(FILE_TABLE, false);
				$db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) JOIN ' . FILE_TABLE . ' f ON (l.DID=f.ID) WHERE c.Dat LIKE "%' . $this->db->escape(trim($keyword)) . '%" AND l.nHash!=x\'' . md5("completeData") . '\' AND l.DocumentTable="' . $db->escape(stripTblPrefix(FILE_TABLE)) . '"' . $ws);
				$contents = $db->getAll(true);

				$db->query('SELECT t.DocumentID FROM ' . TEMPORARY_DOC_TABLE . ' t JOIN ' . FILE_TABLE . ' f ON (t.DocumentID=f.ID) WHERE t.DocumentObject LIKE "%' . $db->escape(trim($keyword)) . '%" AND t.docTable="' . $this->db->escape(stripTblPrefix($table)) . '" AND Active=1' . $ws);
				$contents = array_unique(array_merge($contents, $db->getAll(true)));

				return ($contents ? ' WETABLE.ID IN(' . implode(',', $contents) . ')' : '');
			case TEMPLATES_TABLE:
				$db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE c.Dat LIKE "%' . $this->db->escape(trim($keyword)) . '%" AND l.nHash=x\'' . md5("data") . '\' AND l.DocumentTable="' . $db->escape(stripTblPrefix(TEMPLATES_TABLE)) . '"');
				$contents = $db->getAll(true);

				return ($contents ? ' WETABLE.ID IN(' . implode(',', $contents) . ')' : '');
			case VERSIONS_TABLE:
				$ws = we_selector_file::getWsQuery(FILE_TABLE, false);
				;
				//FIXME: versions are searched even if the field is not checked!
				$contents = [];

				$db->query('SELECT ID,documentElements FROM ' . VERSIONS_TABLE . ' WHERE documentElements!=""' . $ws);
				while($db->next_record()){
					$elements = we_unserialize((substr_compare($db->f('documentElements'), 'a%3A', 0, 4) == 0 ?
							html_entity_decode(urldecode($db->f('documentElements')), ENT_QUOTES) :
							gzuncompress($db->f('documentElements')))
					);

					if(is_array($elements)){
						foreach($elements as $k => $v){
							switch($k){
								case 'Title':
								case 'Charset':
									break;
								default:
									if(isset($v['dat']) &&
											stristr((is_array($v['dat']) ? we_serialize($v['dat'], SERIALIZE_PHP) : $v['dat']), $keyword)){
										$contents[] = $db->f('ID');
									}
							}
						}
					}
				}

				return ($contents ? ' WETABLE.ID IN (' . implode(',', $contents) . ')' : '');
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$ws = we_selector_file::getWsQuery(OBJECT_FILES_TABLE, false);
				$Ids = $regs = [];

				$classes = $db->getAllq('SELECT ID FROM ' . OBJECT_TABLE, true);

				//published objects
				foreach($classes as $i){
					$obj_table = OBJECT_X_TABLE . intval($i);
					$tableInfo = $db->metadata($obj_table);
					$fields = [];
					for($c = 0; $c < count($tableInfo); $c++){
						if(preg_match('/(.+?)_(.*)/', $tableInfo[$c]['name'], $regs)){
							if($regs[1] != 'OF' && $regs[1] != 'variant'){
								$fields[] = ['name' => $tableInfo[$c]['name'],
									'type' => $regs[1],
									'length' => $tableInfo[$c]['len']
								];
							}
						}
					}
					if(!$fields){
						continue;
					}
					$where = [];
					foreach($fields as $v){
						$where[] = 'o.' . $v['name'] . ' LIKE "%' . $db->escape(trim($keyword)) . '%"';
					}

					$db->query('SELECT o.OF_ID FROM ' . $db->escape($obj_table) . ' o JOIN ' . OBJECT_FILES_TABLE . ' of WHERE ' . implode(' OR ', $where) . $ws);
					$Ids = array_merge($Ids, $db->getAll(true));
				}
				//only saved objects
				$db->query('SELECT t.DocumentID FROM ' . TEMPORARY_DOC_TABLE . ' t JOIN ' . OBJECT_FILES_TABLE . ' of ON (of.ID=t.DocumentID) WHERE t.DocumentObject LIKE "%' . $db->escape(trim($keyword)) . '%" AND t.docTable="tblObjectFiles" AND t.Active=1' . $ws);
				$Ids = array_merge($Ids, $db->getAll(true));

				return ($Ids ? ' WETABLE.ID IN (' . implode(',', $Ids) . ')' : '');
		}

		return '';
	}

	function searchMediaLinks($useState = 0, $holdAllLinks = true, $inIDs = '', $returnQuery = false){
		$db = new DB_WE();
		$useState = intval($useState);
		$this->usedMedia = $this->usedMediaLinks = $tmpMediaLinks = $groups = $paths = [];

		$fields = $holdAllLinks ? 'fl.ID,fl.DocumentTable,fl.remObj,fl.isTemp,l.Name AS element' : 'DISTINCT fl.remObj';
		$db->query('SELECT ' . $fields . ' FROM ' . FILELINK_TABLE . ' fl ' . ($holdAllLinks ? ' LEFT JOIN ' . LINK_TABLE . ' l ON l.nHash=fl.nHash AND l.DocumentTable=fl.DocumentTable AND l.DID=fl.remObj' : '') . ' WHERE fl.type="media" AND fl.remTable="' . stripTblPrefix(FILE_TABLE) . '" ' . ($inIDs ? 'AND fl.remObj IN (' . trim($db->escape($inIDs), ',') . ')' : '') . ' AND fl.position=0');

		if($holdAllLinks){
			$types = [FILE_TABLE => g_l('global', '[documents]'),
				TEMPLATES_TABLE => g_l('global', '[templates]'),
				defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE' => g_l('global', '[objects]'),
				defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE' => g_l('searchtool', '[classes]'),
				defined('VFILE_TABLE') ? VFILE_TABLE : 'VFILE_TABLE' => g_l('global', '[vfile]'),
				CATEGORY_TABLE => g_l('global', '[categorys]'),
				defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE' => g_l('javaMenu_moduleInformation', '[newsletter][text]'),
				defined('BANNER_TABLE') ? BANNER_TABLE : 'BANNER_TABLE' => g_l('javaMenu_moduleInformation', '[banner][text]'),
				defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE' => g_l('javaMenu_moduleInformation', '[customer][text]'),
				defined('GLOSSARY_TABLE') ? GLOSSARY_TABLE : 'GLOSSARY_TABLE' => g_l('javaMenu_moduleInformation', '[glossary][text]'),
				NAVIGATION_TABLE => g_l('javaMenu_moduleInformation', '[navigation][text]'),
			];

			while($db->next_record()){
				$rec = $db->getRecord();
				$tmpMediaLinks[$rec['remObj']][] = [$rec['ID'], $rec['DocumentTable'], $rec['isTemp'], $rec['element']];
				$groups[$rec['DocumentTable']][] = $rec['ID'];
				$this->usedMedia[] = $rec['remObj'];
			}

			// get some more information about referencing objects
			$accessible = $paths = $isModified = $isUnpublished = $ct = $onclick = $type = $mod = $isTmpPossible = []; // TODO: make these arrays elements of one array
			foreach($groups as $k => $v){// FIXME: ct is obslete?
				switch(addTblPrefix($k)){
					case FILE_TABLE:
					case defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE';
						$db->query('SELECT ID,Path,ModDate,Published,ContentType FROM ' . addTblPrefix($k) . ' WHERE ID IN (' . implode(',', array_unique($v)) . ')' . we_selector_file::getWsQuery(addTblPrefix($k)));
						while($db->next_record()){
							$accessible[$k][$db->f('ID')] = true;
							$paths[$k][$db->f('ID')] = $db->f('Path');
							$isModified[$k][$db->f('ID')] = $db->f('Published') > 0 && $db->f('ModDate') > $db->f('Published');
							$isUnpublished[$k][$db->f('ID')] = $db->f('Published') == 0;
							$ct[$k][$db->f('ID')] = $db->f('ContentType');
							$onclick[$k][$db->f('ID')] = 'WE().layout.openToEdit(\'' . addTblPrefix($k) . '\',\'' . $db->f('ID') . '\',\'' . $db->f('ContentType') . '\')';
							$type[$k][$db->f('ID')] = 'we_doc';
							$mod[$k][$db->f('ID')] = '';
							$isTmpPossible[$k][$db->f('ID')] = true;
						}
						break;
					case TEMPLATES_TABLE:
					case defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE':
						$db->query('SELECT ID,Path,ContentType FROM ' . addTblPrefix($k) . ' WHERE ID IN (' . implode(',', array_unique($v)) . ')' . we_selector_file::getWsQuery(addTblPrefix($k)));
						while($db->next_record()){
							$accessible[$k][$db->f('ID')] = true;
							$paths[$k][$db->f('ID')] = $db->f('Path');
							$ct[$k][$db->f('ID')] = $db->f('ContentType');
							$onclick[$k][$db->f('ID')] = 'WE().layout.openToEdit(\'' . addTblPrefix($k) . '\',\'' . $db->f('ID') . '\',\'' . $db->f('ContentType') . '\')';
							$type[$k][$db->f('ID')] = 'we_doc';
							$mod[$k][$db->f('ID')] = '';
							$isTmpPossible[$k][$db->f('ID')] = false;
						}
						break;
					case defined('VFILE_TABLE') ? VFILE_TABLE : 'VFILE_TABLE':
						if(we_base_permission::hasPerm('CAN_SEE_COLLECTIONS')){
							$db->query('SELECT ID,Path FROM ' . addTblPrefix($k) . ' WHERE ID IN (' . implode(',', array_unique($v)) . ')'); //no ws fo collections
							while($db->next_record()){
								$accessible[$k][$db->f('ID')] = true;
								$paths[$k][$db->f('ID')] = $db->f('Path');
								$ct[$k][$db->f('ID')] = we_base_ContentTypes::COLLECTION;
								$onclick[$k][$db->f('ID')] = 'WE().layout.openToEdit(\'' . addTblPrefix($k) . '\',\'' . $db->f('ID') . '\',\'' . we_base_ContentTypes::COLLECTION . '\')';
								$type[$k][$db->f('ID')] = 'we_doc';
								$mod[$k][$db->f('ID')] = '';
								$isTmpPossible[$k][$db->f('ID')] = false;
							}
						}
						break;
					case defined('BANNER_TABLE') ? BANNER_TABLE : 'BANNER_TABLE':
					case defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE':
					case defined('GLOSSARY_TABLE') ? GLOSSARY_TABLE : 'GLOSSARY_TABLE':
					case defined('NAVIGATION_TABLE') ? NAVIGATION_TABLE : 'NAVIGATION_TABLE':
					case defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE':
						$paths[$k] = id_to_path($v, addTblPrefix($k), null, true);
						$modules = [defined('BANNER_TABLE') ? BANNER_TABLE : 'BANNER_TABLE' => 'banner',
							defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE' => 'customer',
							defined('GLOSSARY_TABLE') ? GLOSSARY_TABLE : 'GLOSSARY_TABLE' => 'glossary',
							defined('NAVIGATION_TABLE') ? NAVIGATION_TABLE : 'NAVIGATION_TABLE' => 'navigation',
							defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE' => 'newsletter'
						];
						foreach($paths[$k] as $key => $v){
							$accessible[$k][$key] = true;
							$onclick[$k][$key] = 'weSearch.openModule(\'' . $modules[addTblPrefix($k)] . '\',' . $key . ')';
							$type[$k][$key] = 'module';
							$mod[$k][$key] = $modules[addTblPrefix($k)];
							$isTmpPossible[$k][$key] = false;
						}
						break;
					case CATEGORY_TABLE:
						if(we_base_permission::hasPerm('EDIT_KATEGORIE')){
							$paths[$k] = id_to_path($v, addTblPrefix($k), null, true);
							foreach($paths[$k] as $key => $v){
								$accessible[$k][$key] = true;
								$onclick[$k][$key] = 'weSearch.openCategory(' . $key . ')';
								$type[$k][$key] = 'cat';
								$mod[$k][$key] = '';
								$isTmpPossible[$k][$key] = false;
							}
						}
				}
			}

			foreach($tmpMediaLinks as $m_id => $v){
				$this->usedMediaLinks['accessible']['mediaID_' . $m_id] = [];
				$this->usedMediaLinks['notaccessible']['mediaID_' . $m_id] = [];
				foreach($v as $val){// FIXME: table, ct are obsolete when onclick works
					if(!isset($this->usedMediaLinks['accessible']['mediaID_' . $m_id][$types[addTblPrefix($val[1])]][$val[0] . $val[3]])){
						if(isset($accessible[$val[1]][$val[0]])){
							$this->usedMediaLinks['accessible']['mediaID_' . $m_id][$types[addTblPrefix($val[1])]][$val[0] . $val[3]] = ['exists' => isset($accessible[$val[1]][$val[0]]),
								'referencedIn' => intval($val[2]) === 0 ? 'main' : 'temp',
								'isTempPossible' => $isTmpPossible[$val[1]][$val[0]],
								'id' => $val[0],
								'element' => $val[3],
								'type' => isset($type[$val[1]][$val[0]]) ? $type[$val[1]][$val[0]] : '',
								'table' => addTblPrefix($val[1]),
								'ct' => isset($ct[$val[1]][$val[0]]) ? $ct[$val[1]][$val[0]] : '',
								'mod' => $mod[$val[1]][$val[0]],
								'onclick' => isset($onclick[$val[1]][$val[0]]) ? $onclick[$val[1]][$val[0]] : 'alert(\'not implemented yet: ' . $val[1] . '\')',
								'path' => isset($paths[$val[1]][$val[0]]) ? $paths[$val[1]][$val[0]] : '',
								'isModified' => isset($isModified[$val[1]][$val[0]]) ? $isModified[$val[1]][$val[0]] : false,
								'isUnpublished' => isset($isUnpublished[$val[1]][$val[0]]) ? $isUnpublished[$val[1]][$val[0]] : false
							];
						} else {
							$this->usedMediaLinks['notaccessible']['mediaID_' . $m_id][$types[addTblPrefix($val[1])]][$val[0] . $val[3]] = true;
						}
					} else {
						$this->usedMediaLinks['accessible']['mediaID_' . $m_id][$types[addTblPrefix($val[1])]][$val[0] . $val[3]]['referencedIn'] = 'both';
					}
				}

				foreach($types as $type){
					if(!empty($this->usedMediaLinks['accessible']['mediaID_' . $m_id][$type]) || !empty($this->usedMediaLinks['notaccessible']['mediaID_' . $m_id][$type])){
						$this->usedMediaLinks['groups']['mediaID_' . $m_id][] = $type;
					}
				}
			}
		} else {
			$this->usedMedia = $db->getAll(true);
		}

		if(!$useState || !$returnQuery){
			return;
		}

		return $this->usedMedia ? ('WETABLE.ID ' . ($useState === 2 ? 'NOT ' : ' ') . 'IN(' . implode(',', $this->usedMedia) . ')') : ($useState === 2 ? '' : ' 0 ');
	}

	function searchHasReferenceToId($id, $table){
		if(!id || !table){
			return 0;
		}

		$db = new DB_WE();
		$db->query('SELECT DISTINCT ID FROM ' . FILELINK_TABLE . ' WHERE type="media" AND DocumentTable="' . $db->escape(stripTblPrefix($table)) . '" AND remObj=' . intval($id));

		return ($ids = $db->getAll(true)) ? ' ID IN (' . implode(',', array_unique($ids)) . ') ' : 0;
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

		$this->db->query('SELECT *,ABS(' . $sortierung[0] . ') AS Nr, (' . $sortierung[0] . " REGEXP '^[0-9]') AS isNr FROM " . SEARCHRESULT_TABLE . ' WHERE UID=' . $_SESSION['user']['ID'] . ' ORDER BY IsFolder DESC, isNr ' . $sortIsNr . ',Nr ' . $sortNr . ',' . $sortierung[0] . ' ' . $sortNr . ', ' . $order . '  LIMIT ' . $searchstart . ',' . $anzahl);
	}

//FIXME path is only implemented for filetable
	protected function insertInTempTable($where = '', $table = '', $path = ''){
		$this->table = ($table ?: ($this->table ?: ''));
		if(!$this->table){
			return;
		}

		$this->where = '1 ' . ($where ? (((substr(trim($where), 0, 4) !== 'AND ') ? 'AND ' : ' ') . trim($where)) : ($this->where ? 'AND ' . $this->where : ''));

		switch($this->table){
			case FILE_TABLE:
				$tmpTableWhere = '';
				if($path){
					$this->where .= ' AND Path LIKE "%' . $this->db->escape($path) . '%" ';
					$tmpTableWhere = '(SELECT ID FROM ' . FILE_TABLE . ' WHERE Path LIKE "' . $this->db->escape($path) . '%" )';
				}
				$where = str_replace('WETABLE.', 'f.', $this->where);

				$this->db->query('INSERT INTO ' . SEARCHRESULT_TABLE . ' (UID,docID,docTable,Text,Path,ParentID,IsFolder,IsProtected,temp_template_id,TemplateID,ContentType,CreationDate,CreatorID,ModDate,Published,Extension) SELECT ' . $_SESSION['user']['ID'] . ',ID,"' . stripTblPrefix(FILE_TABLE) . '",Text,Path,ParentID,IsFolder,IsProtected,temp_template_id,TemplateID,ContentType,CreationDate,CreatorID,ModDate,Published,Extension FROM `' . FILE_TABLE . '` f WHERE ' . $where);

				//first check published documents
				$this->db->query('UPDATE ' . SEARCHRESULT_TABLE . ' sr JOIN `' . LINK_TABLE . '` l ON (sr.docID=l.DID AND sr.docTable=l.DocumentTable) JOIN `' . CONTENT_TABLE . '` c ON (l.CID=c.ID) ' . ($path ? '' : ' JOIN ' . FILE_TABLE . ' f ON f.ID= l.DID') .
						' SET sr.SiteTitle=c.Dat' .
						' WHERE sr.UID=' . $_SESSION['user']['ID'] . ' AND l.nHash=x\'' . md5("Title") . '\' AND l.DocumentTable!="' . stripTblPrefix(TEMPLATES_TABLE) . '"' . ($path ? ' AND l.DID IN ' . $tmpTableWhere : ' AND ' . $where));

				//check unpublished documents
				$titles = [];
				$this->db->query('SELECT td.DocumentID, td.DocumentObject FROM `' . TEMPORARY_DOC_TABLE . '` td ' . ($path ? '' : ' JOIN ' . FILE_TABLE . ' f ON f.ID=td.DocumentID') . ' WHERE td.docTable="tblFile" AND td.Active=1 ' . ($path ? ' AND td.DocumentID IN ' . $tmpTableWhere : ' AND ' . $where));
				while($this->db->next_record()){
					$tempDoc = we_unserialize($this->db->f('DocumentObject'));
					if(!empty($tempDoc[0]['elements']['Title'])){
						$titles[$this->db->f('DocumentID')] = $tempDoc[0]['elements']['Title']['dat'];
					}
				}
				foreach($titles as $k => $v){
					$this->db->query('UPDATE ' . SEARCHRESULT_TABLE . ' SET SiteTitle="' . $this->db->escape($v) . '" WHERE UID=' . $_SESSION['user']['ID'] . ' AND docID=' . intval($k) . ' AND docTable="' . stripTblPrefix(FILE_TABLE) . '" LIMIT 1');
				}
				break;

			case VERSIONS_TABLE:
				if($_SESSION['weS']['weSearch']['onlyDocs'] || $_SESSION['weS']['weSearch']['ObjectsAndDocs']){
					$this->db->query('INSERT INTO ' . SEARCHRESULT_TABLE . ' (UID,docID,docTable,Text,Path,ParentID,TemplateID,ContentType,CreationDate,CreatorID,Extension,TableID,VersionID) SELECT ' . $_SESSION['user']['ID'] . ',v.documentID,v.documentTable,v.Text,v.Path,v.ParentID,v.TemplateID,v.ContentType,v.timestamp,v.modifierID,v.Extension,v.TableID,v.ID FROM ' . VERSIONS_TABLE . ' v LEFT JOIN ' . FILE_TABLE . ' f ON v.documentID=f.ID WHERE ' . str_replace('WETABLE.', (stristr($this->where, "status='deleted'") ? 'f.' : 'v.'), $this->where . ' ' . $_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere']));
				}
				if(defined('OBJECT_FILES_TABLE') && ($_SESSION['weS']['weSearch']['onlyObjects'] || $_SESSION['weS']['weSearch']['ObjectsAndDocs'])){
					$this->db->query('INSERT INTO ' . SEARCHRESULT_TABLE . ' (UID,docID,docTable,Text,Path,ParentID,TemplateID,ContentType,CreationDate,CreatorID,Extension,TableID,VersionID) SELECT ' . $_SESSION['user']['ID'] . ',v.documentID,v.documentTable,v.Text,v.Path,v.ParentID,v.TemplateID,v.ContentType,v.timestamp,v.modifierID,v.Extension,v.TableID,v.ID FROM ' . VERSIONS_TABLE . ' v LEFT JOIN ' . OBJECT_FILES_TABLE . ' of ON v.documentID=of.ID WHERE ' . str_replace('WETABLE.', (stristr($this->where, "status='deleted'") ? 'of.' : 'v.'), $this->where . ' ' . $_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere']));
				}
				unset($_SESSION['weS']['weSearch']['onlyObjects'], $_SESSION['weS']['weSearch']['onlyDocs'], $_SESSION['weS']['weSearch']['ObjectsAndDocs'], $_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere'], $_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere']);
				break;

			case TEMPLATES_TABLE:
				$this->db->query('INSERT INTO ' . SEARCHRESULT_TABLE . ' (UID,docID,docTable,Text,Path,ParentID,IsFolder,ContentType,SiteTitle,CreationDate,CreatorID,ModDate,Extension) SELECT ' . $_SESSION['user']['ID'] . ',ID,"' . stripTblPrefix(TEMPLATES_TABLE) . '",Text,Path,ParentID,IsFolder,ContentType,Path,CreationDate,CreatorID,ModDate,Extension FROM `' . TEMPLATES_TABLE . '` t WHERE ' . str_replace('WETABLE.', 't.', $this->where));
				break;

			case VFILE_TABLE:
				$this->db->query('INSERT INTO ' . SEARCHRESULT_TABLE . ' (UID,docID,docTable,Text,Path,ParentID,IsFolder,ContentType,CreationDate,CreatorID,ModDate,remTable,remCT,remClass) SELECT ' . $_SESSION['user']['ID'] . ',ID,"' . stripTblPrefix(VFILE_TABLE) . '",Text,Path,ParentID,IsFolder,ContentType,CreationDate,CreatorID,ModDate,remTable,remCT,remClass FROM `' . VFILE_TABLE . '` v WHERE ' . str_replace('WETABLE.', 'v.', $this->where));
				break;

			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$this->db->query('INSERT INTO ' . SEARCHRESULT_TABLE . ' (UID,docID,docTable,Text,Path,ParentID,IsFolder,ContentType,CreationDate,CreatorID,ModDate,Published,TableID) SELECT ' . $_SESSION['user']['ID'] . ',ID,"' . stripTblPrefix(OBJECT_FILES_TABLE) . '",Text,Path,ParentID,IsFolder,ContentType,CreationDate,CreatorID,ModDate,Published,TableID FROM ' . OBJECT_FILES_TABLE . ' of WHERE ' . str_replace('WETABLE.', 'of.', $this->where));
				break;

			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$this->db->query('INSERT INTO ' . SEARCHRESULT_TABLE . ' (UID,docID,docTable,Text,Path,ParentID,IsFolder,ContentType,CreationDate,CreatorID,ModDate) SELECT ' . $_SESSION['user']['ID'] . ',ID,"' . stripTblPrefix(OBJECT_TABLE) . '",Text,Path,ParentID,IsFolder,ContentType,CreationDate,CreatorID,ModDate FROM `' . OBJECT_TABLE . '` WHERE ' . str_replace('WETABLE.', 'o.', $this->where));
				break;
		}
	}

	function insertMediaAttribsToTempTable(){
		if(!f('SELECT 1 FROM ' . SEARCHRESULT_TABLE . ' WHERE UID=' . $_SESSION['user']['ID'] . ' AND docTable="' . stripTblPrefix(FILE_TABLE) . '"')){
			return;
		}
		$this->db->query('SELECT l.DID, c.Dat FROM `' . LINK_TABLE . '` l JOIN `' . CONTENT_TABLE . '` c ON (l.CID=c.ID) JOIN ' . SEARCHRESULT_TABLE . ' t ON t.docID=l.DID WHERE t.UID=' . $_SESSION['user']['ID'] . ' AND t.docTable="' . stripTblPrefix(FILE_TABLE) . '" AND l.nHash=x\'' . md5("title") . '\' AND l.Type="attrib" AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
		$titles = $this->db->getAll();
		if(is_array($titles) && $titles){
			foreach($titles as $k => $v){
				if($v['Dat']){
					$this->db->query('UPDATE ' . SEARCHRESULT_TABLE . ' SET media_title="' . $this->db->escape($v['Dat']) . '" WHERE UID=' . $_SESSION['user']['ID'] . ' AND docID=' . intval($v['DID']) . ' AND docTable="' . stripTblPrefix(FILE_TABLE) . '" LIMIT 1');
				}
			}
		}

		$this->db->query('SELECT l.DID, c.Dat FROM `' . LINK_TABLE . '` l JOIN `' . CONTENT_TABLE . '` c ON (l.CID=c.ID) JOIN ' . SEARCHRESULT_TABLE . ' t ON t.docID=l.DID WHERE t.UID=' . $_SESSION['user']['ID'] . ' AND t.docTable="' . stripTblPrefix(FILE_TABLE) . '"  AND l.nHash=x\'' . md5("alt") . '\' AND l.Type="attrib" AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
		$alts = $this->db->getAll();
		if(is_array($alts) && $alts){
			foreach($alts as $v){
				if($v['Dat']){
					$this->db->query('UPDATE ' . SEARCHRESULT_TABLE . ' SET media_alt="' . $this->db->escape($v['Dat']) . '" WHERE UID=' . $_SESSION['user']['ID'] . '  AND docID=' . intval($v['DID']) . ' AND docTable="' . stripTblPrefix(FILE_TABLE) . '" LIMIT 1');
				}
			}
		}

		/*
		  $startTime = microtime(true);
		  $this->db->query('SELECT l.DID, c.Dat FROM `' . LINK_TABLE . '` l JOIN `' . CONTENT_TABLE . '` c ON (l.CID=c.ID) JOIN '.SEARCHRESULT_TABLE.' t ON t.docID=l.DID WHERE t.UID=' . $_SESSION['user']['ID'] .' AND t.docTable="' . stripTblPrefix(FILE_TABLE) . '"  AND l.nHash=x\''.md5("filesize").'\' AND l.Type="attrib" AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
		  $filesizes = $this->db->getAll();
		  if(is_array($filesizes) && $filesizes){
		  foreach($filesizes as $v){
		  if($v['Dat'] != ""){
		  $this->db->query('UPDATE '.SEARCHRESULT_TABLE.' SET media_filesize="' . $this->db->escape($v['Dat']) . '" WHERE UID=' . $_SESSION['user']['ID'] .' AND docID=' . intval($v['DID']) . ' AND docTable="' . stripTblPrefix(FILE_TABLE) . '" LIMIT 1');
		  }
		  }
		  }
		  t_e('time used compute filesizes', $firstTime, (microtime(true) - $startTime));
		 *
		 */

		// FIXME: attrib filesize is buggy so use filesize() to get size:
		// as soon as attrib is fixed we can use the above code (although using filesize seems faster than above JOINs)
		//$startTime = microtime(true);
		$this->db->query('SELECT docID,Path FROM ' . SEARCHRESULT_TABLE . ' WHERE UID=' . $_SESSION['user']['ID']);
		$docs = $this->db->getAll();
		if(is_array($docs) && $docs){
			foreach($docs as $v){
				if($v['Path']){
					$this->db->query('UPDATE ' . SEARCHRESULT_TABLE . ' SET media_filesize="' . (is_file($_SERVER['DOCUMENT_ROOT'] . $v['Path']) ? intval(filesize($_SERVER['DOCUMENT_ROOT'] . $v['Path'])) : 0) . '" WHERE UID=' . $_SESSION['user']['ID'] . ' AND docID=' . intval($v['docID']) . ' AND docTable="' . stripTblPrefix(FILE_TABLE) . '" LIMIT 1');
				}
			}
		}
		//FIXME: remove this query - only temporary, searchMediaLinks should use the join as well
		$this->db->query('SELECT docID FROM ' . SEARCHRESULT_TABLE . ' WHERE UID=' . $_SESSION['user']['ID'] . ' AND docTable="' . stripTblPrefix(FILE_TABLE) . '"');
		$IDs = implode(',', $this->db->getAll(true));
		$this->searchMediaLinks(0, true, $IDs); // we write $this->usedMediaLinks here, where we allready have final list of found media
		if($this->usedMedia){
			foreach($this->usedMedia as $v){
				$this->db->query('UPDATE ' . SEARCHRESULT_TABLE . ' SET `IsUsed`=1 WHERE UID=' . $_SESSION['user']['ID'] . ' AND docID=' . intval($v) . ' AND docTable="' . stripTblPrefix(FILE_TABLE) . '" LIMIT 1');
			}
		}
	}

	function createTempTable(){
		if(we_base_request::_(we_base_request::BOOL, 'we_cmd', true, 'newSearch')){
			$this->db->query('DELETE FROM ' . SEARCHRESULT_TABLE . ' WHERE UID=' . $_SESSION['user']['ID']);
		}
	}

	function searchfor($searchname, $searchfield, $searchlocation, $tablename, $rows = -1, $start = 0, $order = '', $desc = 0){
		$operator = ' AND ';
		$this->table = $tablename;
		$sql = [];

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
					$arr = array_filter(explode(',', $searchname), function($var){
						return is_numeric($var);
					});
					if(empty($arr)){
						return '0';
					}
					$searchname = implode(',', $arr);
				}
				break;
		}
		if(empty($searchname)){
			return '';
		}

//change some field names
		if($tablename == VERSIONS_TABLE){
			switch($searchfield){
				case 'ID' :
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

		$tableInfo = $GLOBALS['DB_WE']->metadata($this->table, we_database_base::META_NAME);
		//filter fields for each table
		foreach($tableInfo as $cur){
			if($searchfield != $cur){
				continue;
			}
			$searchfield = 'WETABLE.' . $cur;

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
				$sql[] = $this->sqlwhere($searchfield, $searching);
			} elseif(($cur === 'MasterTemplateID' && $this->table === TEMPLATES_TABLE) || ($cur === 'temp_template_id' && $this->table == FILE_TABLE) || ($cur === 'TemplateID' && $this->table === VERSIONS_TABLE)){
				$searchname = path_to_id($searchname, TEMPLATES_TABLE);
				$searching = " = '" . $this->db->escape($searchname) . "' ";

				if(($cur === 'temp_template_id' && $this->table == FILE_TABLE) || ($cur === 'TemplateID' && $this->table == VERSIONS_TABLE)){
					if($this->table == FILE_TABLE || $this->table == VERSIONS_TABLE){
						$sql[] = $this->sqlwhere('WETABLE.TemplateID', $searching);
					}
				} else {
					$sql[] = $this->sqlwhere($searchfield, $searching);
				}
			} elseif($cur == 'Published' || $cur == 'CreationDate' || $cur == 'ModDate'){
				if($cur == 'Published' && $this->table == FILE_TABLE || $this->table == OBJECT_FILES_TABLE || $cur != 'Published'){
					if($this->table == VERSIONS_TABLE && $cur == 'CreationDate' || $cur == 'ModDate'){
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
								$sql[] = $this->sqlwhere($searchfield, ' BETWEEN ' . $timestampStart . ' AND ' . $timestampEnd . ' ');
								break;
							case 'LO':
								$sql[] = $this->sqlwhere($searchfield, ' < "' . $timestampStart . '" ');
								break;
							case 'LEQ':
								$sql[] = $this->sqlwhere($searchfield, ' <= "' . $timestampEnd . '" ');
								break;
							case 'HI':
								$sql[] = $this->sqlwhere($searchfield, ' > "' . $timestampEnd . '" ');
								break;
							case 'HEQ':
								$sql[] = $this->sqlwhere($searchfield, ' >= "' . $timestampStart . '" ');
								break;
						}
					}
				}
			} elseif(isset($searchlocation)){
				switch($searchlocation){
					case 'END':
						$sql[] = $this->sqlwhere($searchfield, ' LIKE "%' . $this->db->escape($searchname) . '" ');
						break;
					case 'START':
						$sql[] = $this->sqlwhere($searchfield, ' LIKE "' . $this->db->escape($searchname) . '%" ');
						break;
					case 'IN':
						$searchname = strtr($searchname, ['\_' => '_', '\%' => '%']);
						$sql[] = $this->sqlwhere($searchfield, ' IN ("' . implode('","', array_map('trim', explode(',', $searchname))) . '") ');
						break;
					case 'IS':
						$searchname = strtr($searchname, ['\_' => '_', '\%' => '%']);
						$sql[] = $this->sqlwhere($searchfield, '="' . $this->db->escape($searchname) . '" ');
						break;
					case 'LO':
						$sql[] = $this->sqlwhere($searchfield, ' < "' . $this->db->escape($searchname) . '" ');
						break;
					case 'LEQ':
						$sql[] = $this->sqlwhere($searchfield, ' <= "' . $this->db->escape($searchname) . '" ');
						break;
					case 'HI':
						$sql[] = $this->sqlwhere($searchfield, ' > "' . $this->db->escape($searchname) . '" ');
						break;
					case 'HEQ':
						$sql[] = $this->sqlwhere($searchfield, ' >= "' . $this->db->escape($searchname) . '" ');
						break;
					default :
						$sql[] = $this->sqlwhere($searchfield, ' LIKE "%' . $this->db->escape($searchname) . '%" ');
						break;
				}
			}
			//found col, return
			return implode(' AND ', $sql);
		}

		return '';
	}

	private static function ofFolderAndChildsOnly($folderID, $table){//move this to view class; or verse visa
		$DB_WE = new DB_WE();
		$_SESSION['weS']['weSearch']['countChilds'] = [];
		//fix #2940
		if(is_array($folderID)){
			foreach($folderID as $k){
				$childsOfFolderId = self::getChildsOfParentId($k, $table, $DB_WE);
				$ids = implode(',', $childsOfFolderId);
			}
			return 'WETABLE.ParentID IN (' . $ids . ')';
		}
		$childsOfFolderId = self::getChildsOfParentId($folderID, $table, $DB_WE);

		return 'WETABLE.ParentID IN (' . implode(',', $childsOfFolderId) . ')';
	}

	private static function getChildsOfParentId($folderID, $table, we_database_base $DB_WE){
		if($table === VERSIONS_TABLE){ //we don't have parents & folders
			return $_SESSION['weS']['weSearch']['countChilds'];
		}

		$DB_WE->query('SELECT ID FROM `' . $DB_WE->escape($table) . '` WHERE ParentID=' . intval($folderID) . ' AND IsFolder=1');
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

	/* static function checkRightTempTable(){
	  $db = new DB_WE();
	  $db->addTable('test_SEARCH_TEMP_TABLE', array('test' => 'VARCHAR(1) NOT NULL'), [], 'MEMORY', true);

	  $db->next_record();

	  $return = (stristr($db->Error, 'Access denied') ? false : true);

	  $db->delTable('test_SEARCH_TEMP_TABLE', true);

	  return $return;
	  } */

	/* static function checkRightDropTable(){
	  $db = new DB_WE();
	  $db->addTable('test_SEARCH_TEMP_TABLE', array('test' => 'VARCHAR(1) NOT NULL'), [], 'MEMORY');
	  $db->delTable('test_SEARCH_TEMP_TABLE');
	  return (stristr($db->Error, 'command denied') ? false : true);
	  } */

	function getResultCount(){
		return f('SELECT COUNT(1) FROM ' . SEARCHRESULT_TABLE . ' WHERE UID=' . $_SESSION['user']['ID'], '', $this->db);
	}

	static function cleanOldEntries(){
		$GLOBALS['DB_WE']->query('DELETE FROM ' . SEARCHRESULT_TABLE . ' WHERE UID IN (SELECT ID FROM ' . USER_TABLE . ' WHERE Ping IS NULL OR (Ping+INTERVAL ' . we_base_constants::PING_TOLERANZ . ' second)<NOW() )');
	}

	private function getSearchString($table, $tables, $opAND, $searchFields, $whichSearch, $searchForField, $searchText, $location, $searchForContentType, $DB_WE, $view){
		$where = [];
		$where_OR = [];
		foreach($searchFields as $i => $curField){
			$done = false;
			$searchString = ($whichSearch === we_search_view::SEARCH_MEDIA && substr($curField, 0, 6) === 'meta__' && $searchText[$i] === '' && $location[$i] === 'IS') ? '##EMPTY##' : $searchText[$i];

			if(!empty($searchString)){
				if(!in_array($curField, ['Status', 'Speicherart', 'HasReferenceToID'])){
					$searchString = strtr($searchString, ['\\' => '\\\\', '_' => '\_', '%' => '\%']);
				}

				if($table === FILE_TABLE && $whichSearch === we_search_view::SEARCH_MEDIA){
					$done = true;
					switch($curField){
						case 'keyword':
							foreach((array_filter($searchForField, function($var){
								return ($var == 1);
							})) as $field => $v){
								switch($field){
									case "title": // IMPORTANT: in media search options are generally AND-linked, but not "search in Title, Text, Meta!
										$where_OR [] = ($this->searchInTitle($searchString, $table) ?: '');
										break;
									case "text":
										$where_OR [] = 'WETABLE.`Text` LIKE "%' . $DB_WE->escape(trim($searchString)) . '%" ';
										break;
									case "meta":
										$where_OR [] = ($this->searchInAllMetas($searchString, $table) ?: '');
										break;
								}
							}
							break;
						case 'ContentType':
							$contentTypes = [];
							foreach($searchForContentType as $type => $v){
								if($v){
									switch($type){
										case 'image':
											$contentTypes[] = we_base_ContentTypes::IMAGE;
											break;
										case 'video':
											$contentTypes[] = we_base_ContentTypes::VIDEO;
											$contentTypes[] = we_base_ContentTypes::FLASH;
											break;
										case 'audio':
											$contentTypes[] = we_base_ContentTypes::AUDIO;
											break;
										case 'other':
											$contentTypes[] = we_base_ContentTypes::APPLICATION;
											break;
									}
								}
							}
							$contentTypes = $contentTypes ?:
									[we_base_ContentTypes::IMAGE, we_base_ContentTypes::VIDEO, we_base_ContentTypes::FLASH, we_base_ContentTypes::AUDIO, we_base_ContentTypes::APPLICATION];
							$where[] = 'WETABLE.ContentType IN ("' . implode('","', $contentTypes) . '")';
							break;
						case 'IsUsed':
							$where[] = $this->searchMediaLinks($searchString, $view !== we_search_view::VIEW_ICONS, '', true);
							break;
						case 'IsProtected':
							switch($searchString){
								case 1:
									$where[] = 'WETABLE.IsProtected=1';
									break;
								case 2:
									$where[] = 'WETABLE.IsProtected=0';
									break;
							}
							break;
						default:
							$done = false;
							if(substr($curField, 0, 6) === 'meta__'){
								$where[] = $this->searchInMeta($searchString, substr($curField, 6), $location[$i], $table, $DB_WE);
								$done = true;
							}
					}
				}

				if(!$done){
					switch($curField){
						case 'Content':
							$objectTable = defined('OBJECT_TABLE') ? OBJECT_TABLE : '';
							if($objectTable == '' || $table != $objectTable){
								$w = $this->searchContent($searchString, $table);
								if($w === ''){
									$where[] = ' 0 ';
								} elseif($opAND){
									$where[] = $w;
								} else {
									$where_OR[] = $w;
								}
							}
							break;
						case 'modifierID':
							$w = $this->searchModifier($searchString);
							if($w === ''){
								$where[] = ' 0 ';
							} elseif($opAND){
								$where[] = $w;
							} else {
								$where_OR[] = $w;
							}

							break;
						case 'allModsIn':
							if($table == VERSIONS_TABLE){
								$where[] = $this->searchModFields($searchString, $DB_WE);
							}
							break;
						case 'Title':
							$w = $this->searchInTitle($searchString, $table);
							if($w === ''){
								$where[] = ' 0 ';
							} elseif($opAND){
								$where[] = $w;
							} else {
								$where_OR[] = $w;
							}
							break;
						case 'Status':
						case 'Speicherart':
							switch($table){
								case VERSIONS_TABLE:
									$where[] = $this->getStatusFiles($searchString, $table);

									$docTableChecked = (in_array(FILE_TABLE, $tables)) ? true : false;
									$objTableChecked = (defined('OBJECT_FILES_TABLE') && (in_array(OBJECT_FILES_TABLE, $tables))) ? true : false;
									if($objTableChecked && $docTableChecked){
										$where[] = '(v.documentTable IN ("' . stripTblPrefix(FILE_TABLE) . '","' . stripTblPrefix(OBJECT_FILES_TABLE) . '")) ';
									} elseif($docTableChecked){
										$where[] = 'v.documentTable="' . stripTblPrefix(FILE_TABLE) . '" ';
									} elseif($objTableChecked){
										$where[] = 'v.documentTable="' . stripTblPrefix(OBJECT_FILES_TABLE) . '" ';
									}
									break;
								case FILE_TABLE:
								case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
									$w = $this->getStatusFiles($searchString, $table);
									if($w === ''){
										$where[] = ' 0 ';
									} elseif($opAND){
										$where[] = $w;
									} else {
										$where_OR[] = $w;
									}

									break 2;
								default:
									break 2;
							}

							break;
						case 'CreatorName':
						case 'WebUserName':
							if(isset($curField) && isset($location[$i])){
								$w = $this->searchSpecial($searchString, $curField, $location[$i]);
								if($w === ''){
									$where[] = ' 0 ';
								} elseif($opAND){
									$where[] = $w;
								} else {
									$where_OR[] = $w;
								}
							}
							break;
						case 'temp_category':
							$w = $this->searchCategory($searchString, $table, $curField);
							if($w === ''){
								$where[] = ' 0 ';
							} elseif($opAND){
								$where[] = $w;
							} else {
								$where_OR[] = $w;
							}

							break;
						case 'HasReferenceToID':
							$where[] = $searchString ? (($searchId = path_to_id($searchString)) ? $this->searchHasReferenceToId($searchId, $table) : 0) : '';
							break;
						default:
							//if($whichSearch != "AdvSearch"){
							$w = $this->searchfor($searchString, $curField, $location[$i], $table);
							if($w === ''){
								$where[] = ' 0 ';
							} elseif($opAND){
								$where[] = $w;
							} else {
								$where_OR[] = $w;
							}
						//}
					}
				}
			}
		}
		$where_OR = array_filter($where_OR);
		if($where_OR){
			$where[] = '(' . implode(' OR ', $where_OR) . ')';
		}

		return array_filter($where);
	}

	public static function getJSLangConsts(){
		return 'WE().consts.g_l.weSearch = {
	buttonSelectValue: "' . g_l('button', '[select][value]') . '",
	confirmDel:"' . g_l('searchtool', '[confirmDel]') . '",
	nameForSearch:"' . g_l('searchtool', '[nameForSearch]') . '",
	no_perms:"' . we_message_reporting::prepareMsgForJS(g_l('tools', '[no_perms]')) . '",
	nothingCheckedAdv: \'' . g_l('searchtool', '[nothingCheckedAdv]') . '\',
	nothingCheckedTmplDoc: \'' . g_l('searchtool', '[nothingCheckedTmplDoc]') . '\',
	nothing_to_delete:"' . we_message_reporting::prepareMsgForJS(g_l('tools', '[nothing_to_delete]')) . '",
	nothing_to_save:"' . we_message_reporting::prepareMsgForJS(g_l('tools', '[nothing_to_save]')) . '",
	predefinedSearchdelete:"' . we_message_reporting::prepareMsgForJS(g_l('searchtool', '[predefinedSearchdelete]')) . '",
	predefinedSearchmodify:"' . we_message_reporting::prepareMsgForJS(g_l('searchtool', '[predefinedSearchmodify]')) . '",
	publish_docs:"' . g_l('searchtool', '[publish_docs]') . '",
	resetVersionsSearchtool:"' . g_l('versions', '[resetVersionsSearchtool]') . '",
	searchtool__notChecked: "' . g_l('searchtool', '[notChecked]') . '",
	searchtool__publishOK: "' . g_l('searchtool', '[publishOK]') . '",
	versionsNotChecked: "' . g_l('versions', '[notChecked]') . '",
	versionsResetAllVersionsOK: "' . g_l('versions', '[resetAllVersionsOK]') . '",
};';
	}

}
