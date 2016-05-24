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
require_once(WE_INCLUDES_PATH . 'we_tools/weSearch/conf/define.conf.php');

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
	public $founditems = 0;
	public $View;

	public function __construct($view = null){
		parent::__construct();
		$this->View = $view ? : new we_search_view();
	}

	function searchProperties($whichSearch, $model){
		$DB_WE = new DB_WE();
		$workspaces = $_result = $versionsFound = $saveArrayIds = array();
		$_SESSION['weS']['weSearch']['foundItems' . $whichSearch] = 0; // will be obsolete
		$searchFields = $model->getProperty('currentSearchFields');
		$searchText = $model->getProperty('currentSearch');
		$location = $model->getProperty('currentLocation');
		$folderID = $model->getProperty('currentFolderID');
		$_order = $model->getProperty('currentOrder');
		$_view = $model->getProperty('currentSetView');
		$_searchstart = $model->getProperty('currentSearchstart');
		$_anzahl = $model->getProperty('currentAnzahl');
		$_tables = $model->getProperty('currentSearchTables');
		$searchForField = $model->getProperty('currentSearchForField');
		$searchForContentType = $model->getProperty('currentSearchForContentType');

		$searchText = (is_array($searchText) ?
				array_map('trim', $searchText) :
				array());

		$tab = we_base_request::_(we_base_request::INT, 'tab', we_base_request::_(we_base_request::INT, 'tabnr', 1)); //init activTab like this

		if(isset($searchText[0]) && substr($searchText[0], 0, 4) === 'exp:'){
			$_result = $this->View->searchclassExp->getSearchResults($searchText[0], $_tables);
			if($_result){
				foreach($_result as $k => $v){
					foreach($v as $key => $val){
						switch($key){
							case "Table":
							case 'ID':
								unset($_result[$k][$key]);
								$_result[$k]['doc' . $key] = $val;
						}
					}
					$_result[$k]['SiteTitle'] = "";
				}
				$_SESSION['weS']['weSearch']['foundItems' . $whichSearch] = count($_result);
			}
		} elseif(
			($model->IsFolder != 1 && ( ($whichSearch === we_search_view::SEARCH_DOCS && $tab === 1) || ($whichSearch === we_search_view::SEARCH_TMPL && $tab === 2) || ($whichSearch === we_search_view::SEARCH_ADV && $tab === 3)) || ($whichSearch === we_search_view::SEARCH_MEDIA && $tab === 5) ) ||
			(we_base_request::_(we_base_request::INT, 'cmdid')) ||
			(($view = we_base_request::_(we_base_request::STRING, 'view')) === "GetSearchResult" || $view === "GetMouseOverDivs")
		){

			$this->createTempTable();
			$op = ($whichSearch === we_search_view::SEARCH_ADV || $whichSearch === we_search_view::SEARCH_MEDIA ? ' AND ' : ' OR ');

			foreach($_tables as $_table){
				$where = '';
				$where_OR = array();
				$this->settable($_table);

				if(!defined('OBJECT_TABLE') || (defined('OBJECT_TABLE') && $_table != OBJECT_TABLE)){
					$workspaces = get_ws($_table, true);
				}

				for($i = 0; $i < count($searchFields); $i++){
					$w = '';
					$done = false;
					$searchString = ($whichSearch === we_search_view::SEARCH_MEDIA && substr($searchFields[$i], 0, 6) === 'meta__' && $searchText[$i] === '' && $location[$i] === 'IS') ? '##EMPTY##' : $searchText[$i];

					if(!empty($searchString)){
						if(!in_array($searchFields[$i], array('Status', 'Speicherart', 'HasReferenceToID'))){
							$searchString = str_replace(array('\\', '_', '%'), array('\\\\', '\_', '\%'), $searchString);
						}

						if($_table === FILE_TABLE && $whichSearch === we_search_view::SEARCH_MEDIA){
							$done = true;
							switch($searchFields[$i]){
								case 'keyword':
									foreach((array_filter($searchForField, function($var){
										return ($var == 1);
									})) as $field => $v){
										switch($field){
											case "title": // IMPORTANT: in media search options are generally AND-linked, but not "search in Title, Text, Meta!
												$where_OR [] = ($this->searchInTitle($searchString, $_table)? : '');
												break;
											case "text":
												$where_OR [] = 'WETABLE.`Text` LIKE "%' . $DB_WE->escape(trim($searchString)) . '%" ';
												break;
											case "meta":
												$where_OR [] = ($this->searchInAllMetas($searchString, $_table) ? : '');
												break;
										}
									}
									break;
								case 'ContentType':
									$contentTypes = array();
									foreach($searchForContentType as $type => $v){
										if($v){
											switch($type){
												case 'image':
													$contentTypes[] = "'" . we_base_ContentTypes::IMAGE . "'";
													break;
												case 'video':
													$contentTypes[] = "'" . we_base_ContentTypes::VIDEO . "'";
													$contentTypes[] = "'" . we_base_ContentTypes::QUICKTIME . "'";
													$contentTypes[] = "'" . we_base_ContentTypes::FLASH . "'";
													break;
												case 'audio':
													$contentTypes[] = "'" . we_base_ContentTypes::AUDIO . "'";
													break;
												case 'other':
													$contentTypes[] = "'" . we_base_ContentTypes::APPLICATION . "'";
													break;
											}
										}
									}
									$contentTypes = $contentTypes ? implode(',', $contentTypes) :
										"'" . we_base_ContentTypes::IMAGE . "','" . we_base_ContentTypes::VIDEO . "','" . we_base_ContentTypes::QUICKTIME . "','" . we_base_ContentTypes::FLASH . "','" . we_base_ContentTypes::AUDIO . "','" . we_base_ContentTypes::APPLICATION . "'";
									$where .= ($where ? ' AND ' : '') . 'WETABLE.ContentType IN (' . $contentTypes . ')';
									break;
								case 'IsUsed':
									$where .= $this->searchMediaLinks($searchString, $_view !== we_search_view::VIEW_ICONS, '', true);
									break;
								case 'IsProtected':
									switch($searchString){
										case 1:
											$where .= ' AND WETABLE.IsProtected=1 ';
											break;
										case 2:
											$where .= ' AND WETABLE.IsProtected=0 ';
											break;
									}
									break;
								default:
									$done = false;
									if(substr($searchFields[$i], 0, 6) === 'meta__'){
										$where .= $this->searchInMeta($searchString, substr($searchFields[$i], 6), $location[$i], $_table);
										$done = true;
									}
							}
						}

						if(!$done){
							switch($searchFields[$i]){
								case 'Content':
									$objectTable = defined('OBJECT_TABLE') ? OBJECT_TABLE : '';
									if($objectTable == '' || $_table != $objectTable){
										$w = $this->searchContent($searchString, $_table);
										if($where == '' && $w == ''){
											$where .= ' AND 0 ';
										} elseif($where == '' && $w != ''){
											$where .= ' AND ' . $w;
										} elseif($w != ''){
											$where .= $op . ' ' . $w;
										}
									}
									break;
								case 'modifierID':
									$where .= $this->searchModifier($searchString);
									break;
								case 'allModsIn':
									if($_table == VERSIONS_TABLE){
										$where .= $this->searchModFields($searchString);
									}
									break;
								case 'Title':
									$w = $this->searchInTitle($searchString, $_table);
									if($where == '' && $w == ''){
										$where .= ' AND 0 ';
									} elseif($where == '' && $w != ''){
										$where .= ' AND ' . $w;
									} elseif($w != ''){
										$where .= $op . ' ' . $w;
									}
									break;
								case 'Status':
								case 'Speicherart':
									switch($_table){
										case VERSIONS_TABLE:
											$w = $this->getStatusFiles($searchString, $_table);

											$docTableChecked = (in_array(FILE_TABLE, $_tables)) ? true : false;
											$objTableChecked = (defined('OBJECT_FILES_TABLE') && (in_array(OBJECT_FILES_TABLE, $_tables))) ? true : false;
											if($objTableChecked && $docTableChecked){
												$w .= ' AND (v.documentTable IN ("' . stripTblPrefix(FILE_TABLE) . '","' . stripTblPrefix(OBJECT_FILES_TABLE) . '")) ';
											} elseif($docTableChecked){
												$w .= ' AND v.documentTable="' . stripTblPrefix(FILE_TABLE) . '" ';
											} elseif($objTableChecked){
												$w .= ' AND v.documentTable="' . stripTblPrefix(OBJECT_FILES_TABLE) . '" ';
											}
											break;
										case FILE_TABLE:
										case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
											$w = $this->getStatusFiles($searchString, $_table);
									}
									$where .= ($where ? ' AND ' : '') . $w;
									break;
								case 'CreatorName':
								case 'WebUserName':
									if(isset($searchFields[$i]) && isset($location[$i])){
										$where .= $this->searchSpecial($searchString, $searchFields[$i], $location[$i]);
									}
									break;
								case 'temp_category':
									$where .= $this->searchCategory($searchString, $_table, $searchFields[$i]);
									break;
								case 'HasReferenceToID':
									$where .=!$searchString ? 1 : (($searchId = path_to_id($searchString)) ? $this->searchHasReferenceToId($searchId, $_table) : 0);
									break;
								default:
									//if($whichSearch != "AdvSearch"){
									$where .= ($where ? '' : 1 ) . $this->searchfor($searchString, $searchFields[$i], $location[$i], $_table);
								//}
							}
						}
					}
				}
				$where_OR = array_filter($where_OR);
				if($where || $where_OR){

					if(isset($folderID) && ($folderID != '' && $folderID != 0)){
						// FIXME: search for Text shoukd come without AND!!
						$where = ' AND (' . ($whichSearch === we_search_view::SEARCH_DOCS || $whichSearch === we_search_view::SEARCH_TMPL ? '1 ' : '') . $where . ')' . we_search_search::ofFolderAndChildsOnly($folderID, $_table);
					}

					if($_table === VERSIONS_TABLE){
						$workspacesTblFile = get_ws(FILE_TABLE, true);
						if(defined('OBJECT_FILES_TABLE')){
							$workspacesObjFile = get_ws(OBJECT_FILES_TABLE, true);
						}
					}

					if($workspaces){
						$where = ' (' . $where . ')' . we_search_search::ofFolderAndChildsOnly($workspaces, $_table);
					}

					$whereQuery = $where;

					//query for restrict users for FILE_TABLE, VERSIONS_TABLE AND OBJECT_FILES_TABLE
					$restrictUserQuery = ' AND (WETABLE.RestrictOwners IN(0,' . intval($_SESSION["user"]["ID"]) . ') OR FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',WETABLE.Owners))';

					switch($_table){
						case FILE_TABLE:
							if($where_OR){
								$whereQuery .= ' AND (' . implode(' OR ', $where_OR) . ') ';
							}
							$whereQuery .= $restrictUserQuery;
							break;

						case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
							$whereQuery .= $restrictUserQuery;
							break;

						case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
							$whereQuery .= ' AND (o.RestrictUsers=0 OR o.CreatorID=' . intval($_SESSION["user"]["ID"]) . ' OR FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',o.Users)) ';
							break;
						case VERSIONS_TABLE:
							$_SESSION['weS']['weSearch']['onlyObjects'] = true;
							$_SESSION['weS']['weSearch']['onlyDocs'] = true;
							$_SESSION['weS']['weSearch']['ObjectsAndDocs'] = true;
							$_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere'] = ' AND (WETABLE.RestrictOwners=0 OR WETABLE.CreatorID=' . intval($_SESSION["user"]["ID"]) . ' OR FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',WETABLE.Owners))';
							$_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere'] = ' AND (WETABLE.RestrictOwners=0 OR WETABLE.CreatorID=' . intval($_SESSION["user"]["ID"]) . ' OR FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',WETABLE.Owners))';
							if(!empty($workspacesTblFile)){
								$_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere'] .= ($where = ' ' . we_search_search::ofFolderAndChildsOnly($workspacesTblFile[0], $_table));
							}
							if(isset($workspacesObjFile) && !empty($workspacesObjFile)){
								$_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere'] .= ($where = " " . we_search_search::ofFolderAndChildsOnly($workspacesObjFile[0], $_table));
							}

							/* 	if(!$isCheckedFileTable && $isCheckedObjFileTable){
							  $_SESSION['weS']['weSearch']['onlyDocs'] = false;
							  $whereQuery .= ' AND ' . escape_sql_query($_table) . '.documentTable="' . OBJECT_FILES_TABLE . '" ';
							  $_SESSION['weS']['weSearch']['ObjectsAndDocs'] = false;
							  }
							  if($isCheckedFileTable && !$isCheckedObjFileTable){
							  $_SESSION['weS']['weSearch']['onlyObjects'] = false;
							  $whereQuery .= ' AND ' . escape_sql_query($_table) . '.documentTable="' . FILE_TABLE . '" ';
							  $_SESSION['weS']['weSearch']['ObjectsAndDocs'] = false;
							  } */
							break;
					}

					$this->setwhere($whereQuery);
					$this->insertInTempTable($whereQuery, $_table);

					// when MediaSearch add attrib_alt, attrib_title, IsUsed to SEARCH_TEMP_TABLE
					if($whichSearch === we_search_view::SEARCH_MEDIA){
						$this->insertMediaAttribsToTempTable();
					}
				}
			}

			$this->selectFromTempTable($_searchstart, $_anzahl, $_order);

			while($this->next_record()){
				if(!empty($this->Record['VersionID'])){

					$versionsFound[] = array(
						$this->Record['ContentType'],
						$this->Record['docID'],
						$this->Record['VersionID']
					);
				}
				if(!isset($saveArrayIds[$this->Record['ContentType']][$this->Record['docID']])){
					$saveArrayIds[$this->Record['ContentType']][$this->Record['docID']] = $this->Record['docID'];

					$_result[] = array_merge(array('Table' => $_table), array('foundInVersions' => ""), $this->Record);
				}
			}

			foreach($versionsFound as $k => $v){
				foreach($_result as $key => $val){
					if(isset($_result[$key]['foundInVersions']) && isset($_result[$key]['docID']) && $_result[$key]['docID'] == $v[1] && isset($_result[$key]['ContentType']) && $_result[$key]['ContentType'] == $v[0]){
						if($_result[$key]['foundInVersions'] != ""){
							$_result[$key]['foundInVersions'] .= ",";
						}
						$_result[$key]['foundInVersions'] .= $v[2];
					}
				}

				$this->selectFromTempTable($_searchstart, $_anzahl, $_order);
				while($this->next_record()){
					if(!isset($saveArrayIds[$this->Record['ContentType']][$this->Record['docID']])){
						$saveArrayIds[$this->Record['ContentType']][$this->Record['docID']] = $this->Record['docID'];
						$_result[] = array_merge(array(
							'Table' => $_table
							), $this->Record);
					}
				}
			}

			$_SESSION['weS']['weSearch']['foundItems' . $whichSearch] = $this->founditems = $this->getResultCount();
		}

		if($_SESSION['weS']['weSearch']['foundItems' . $whichSearch] == 0){
			return array();
		}

		foreach($_result as $k => $v){
			$_result[$k]["Description"] = '';

			switch(addTblPrefix($_result[$k]['docTable'])){
				case FILE_TABLE:
					if($_result[$k]['Published'] >= $_result[$k]['ModDate'] && $_result[$k]['Published'] != 0){
						$DB_WE->query('SELECT l.DID, c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DID=' . intval($_result[$k]["docID"]) . ' AND l.nHash=x\'' . md5('Description') . '\' AND l.DocumentTable="' . FILE_TABLE . '"');
						while($DB_WE->next_record()){
							$_result[$k]['Description'] = $DB_WE->f('Dat');
						}
					} else {
						$tempDoc = f('SELECT DocumentObject  FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID =' . intval($_result[$k]["docID"]) . ' AND docTable="tblFile" AND Active = 1', '', $DB_WE);
						if(!empty($tempDoc)){
							$tempDoc = we_unserialize($tempDoc);
							if(isset($tempDoc[0]['elements']['Description']) && $tempDoc[0]['elements']['Description']['dat'] != ''){
								$_result[$k]["Description"] = $tempDoc[0]['elements']['Description']['dat'];
							}
						}
					}
					break;
				default:
					$_result[$k]['Description'] = '';
			}
		}

		return $this->View->makeContent($_result, $_view, $whichSearch);
	}

	function getModFields(){
		$modFields = array();
		$versions = new we_versions_version();
		foreach(array_keys($versions->modFields) as $k){
			if($k != 'status'){
				$modFields[$k] = $k;
			}
		}

		return $modFields;
	}

	function getUsers(){
		$_db = new DB_WE();
		return $_db->getAllFirstq('SELECT ID, Text FROM ' . USER_TABLE, false);
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
		);

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
			($usePrefix ? 'meta__' : '') . 'Title' => ($getTypes ? 'text' : g_l('searchtool', '[metadata][field]')),
			($usePrefix ? 'meta__' : '') . 'Description' => ($getTypes ? 'text' : g_l('searchtool', '[metadata][description]')),
			($usePrefix ? 'meta__' : '') . 'Keywords' => ($getTypes ? 'text' : g_l('searchtool', '[metadata][keyword]')),
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

	static function getLocation($whichField = '', $select = '', $size = 1){
		switch($whichField){
			default:
				return array(
					'IS' => g_l('searchtool', '[IS]'),
					'CONTAIN' => g_l('searchtool', '[CONTAIN]'),
					'START' => g_l('searchtool', '[START]'),
					'END' => g_l('searchtool', '[END]'),
					'LO' => g_l('searchtool', '[<]'),
					'LEQ' => g_l('searchtool', '[<=]'),
					'HEQ' => g_l('searchtool', '[>=]'),
					'HI' => g_l('searchtool', '[>]'),
					'IN' => g_l('searchtool', '[IN]'),
				);
			case 'text':
				return array(
					'IS' => g_l('searchtool', '[IS]'),
					'CONTAIN' => g_l('searchtool', '[CONTAIN]'),
					'START' => g_l('searchtool', '[START]'),
					'END' => g_l('searchtool', '[END]'),
					'IN' => g_l('searchtool', '[IN]'),
				);
			case 'date':
				return array(
					'IS' => g_l('searchtool', '[IS]'),
					'LO' => g_l('searchtool', '[<]'),
					'LEQ' => g_l('searchtool', '[<=]'),
					'HEQ' => g_l('searchtool', '[>=]'),
					'HI' => g_l('searchtool', '[>]'),
					'IN' => g_l('searchtool', '[IN]'),
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

		$dtq = we_docTypes::getDoctypeQuery($_db);
		$_db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		return $_db->getAllFirst(false);
	}

	private function searchInTitle($keyword, $table){
		$_db2 = new DB_WE();
		//first check published documents
		$titles = array();

		//check unpublished documents
		$_db2->query('SELECT DocumentID, DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE docTable="tblFile" AND Active=1 AND DocumentObject LIKE "%' . $_db2->escape(trim($keyword)) . '%"');
		while($_db2->next_record()){
			$tempDoc = we_unserialize($_db2->f('DocumentObject'));
			if(!empty($tempDoc[0]['elements']['Title'])){
				$keyword = str_replace(array('\_', '\%'), array('_', '%'), $keyword);
				if(stristr($tempDoc[0]['elements']['Title']['dat'], $keyword)){
					$titles[] = $_db2->f('DocumentID');
				}
			}
		}

		return ' (WETABLE.ID IN (SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.nHash=x\'' . md5("Title") . '\' AND c.Dat LIKE "%' . $_db2->escape(trim($keyword)) . '%" AND l.DocumentTable="' . stripTblPrefix($table) . '") ' . ($titles ? ' OR WETABLE.ID IN (' . implode(',', $titles) . ')' : '') . ' )';
	}

	function searchCategory($keyword, $table){
		if($table == TEMPLATES_TABLE){
			return 'AND 0 ';
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
		$res = $res2 = array();

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
				$query2 = 'SELECT DocumentObject  FROM ' . TEMPORARY_DOC_TABLE . ' WHERE docTable="tblObjectFiles" AND Active=1';
				$_db->query($query2);
				while($_db->next_record()){
					$tempObj = we_unserialize($_db->f('DocumentObject'));
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

		$where = $whereIn = array();

		$keyword = path_to_id($keyword, CATEGORY_TABLE);

		foreach($res2 as $k => $v){
			foreach($v as $v2){
				//look if the value is numeric
				if(preg_match('=^[0-9]+$=i', $v2)){
					if($v2 == $keyword){
						$where[] = 'WETABLE.ID=' . intval($k);
						$whereIn[] = intval($k);
					}
				}
			}
		}

		// TODO: make IN for other tables too (must check first)
		if($table == FILE_TABLE){
			return ' AND ' . ($whereIn ? 'WETABLE.ID IN(' . implode(',', $whereIn) . ')' : ' 0 ');
		}
		return ' AND ' . ($where ? '(' . implode(' OR ', $where) . ')' : ' 0 ');
	}

	function searchSpecial($keyword, $searchFields, $searchlocation){
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
					$searching = ' LIKE "%' . $_db->escape($keyword) . '" ';
					break;
				case 'START' :
					$searching = ' LIKE "' . $_db->escape($keyword) . '%" ';
					break;
				case 'IS' :
					$searching = " = '" . $_db->escape($keyword) . "' ";
					break;
				case 'IN':
					$searching = ' IN ("' . implode('","', array_map('trim', explode(',', $keyword))) . '") ';
					break;
				case 'LO' :
					$searching = ' < "' . $_db->escape($keyword) . '" ';
					break;
				case 'LEQ' :
					$searching = ' <= "' . $_db->escape($keyword) . '" ';
					break;
				case 'HI' :
					$searching = ' > "' . $_db->escape($keyword) . '" ';
					break;
				case 'HEQ' :
					$searching = ' >= "' . $_db->escape($keyword) . '" ';
					break;
				default :
					$searching = ' LIKE "%' . $_db->escape($keyword) . '%" ';
					break;
			}
		}

		$userIDs = $_db->getAllq('SELECT ID FROM ' . $_db->escape($_table) . ' WHERE ' . $field . ' ' . $searching, true);

		return ' AND ' . ($userIDs ? $fieldFileTable . ' IN (' . implode(',', $userIDs) . ')' : ' 0 ');
	}

	function addToSearchInMeta($search, $field, $location){
		$this->collectionMetaSearches[] = array($search, $field, $location);
	}

	function searchInAllMetas($keyword, $table = ''){
		if($table !== FILE_TABLE){// FIXME: actually no meta search on Versions or unpublished docs!!
			return;
		}
		$_db = new DB_WE();
		$names = array();
		foreach($this->getFieldsMeta() as $k => $v){
			if($v[0] && $v[1]){
				$names[] = $k;
			}
		}
		if(empty($names)){
			return;
		}
		$n = array_map('md5', $names);
		$_db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND (l.Name IN (x\'' . implode('\',x\'', $n) . '\') AND c.Dat LIKE "%' . $_db->escape($keyword) . '%")');
		$IDs = $_db->getAll(true);

		return $IDs ? 'WETABLE.ID IN (' . implode(',', $IDs) . ')' : '0';
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
					$searching = ' LIKE "%' . $_db->escape($keyword) . '" ';
					break;
				case 'START' :
					$searching = ' LIKE "' . $_db->escape($keyword) . '%" ';
					break;
				case 'IS' :
					$reverse = $keyword === '##EMPTY##' ? : false;
					$searching = " = '" . $_db->escape($keyword) . "' ";
					break;
				case 'IN':
					$searching = ' IN ("' . implode('","', array_map('trim', explode(',', $keyword))) . '") ';
					break;
				case 'LO' :
					$searching = ' < "' . $_db->escape($keyword) . '" ';
					break;
				case 'LEQ' :
					$searching = ' <= "' . $_db->escape($keyword) . '" ';
					break;
				case 'HI' :
					$searching = ' > "' . $_db->escape($keyword) . '" ';
					break;
				case 'HEQ' :
					$searching = ' >= "' . $_db->escape($keyword) . '" ';
					break;
				default :
					$searching = ' LIKE "%' . $_db->escape(trim($keyword)) . '%" ';
					break;
			}
		}

		$_db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.nHash=x\'' . md5($searchField) . '\' ' . ($reverse ? '' : 'AND c.Dat ' . $searching) . ' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
		$IDs = $_db->getAll(true);

		return $IDs ? 'AND WETABLE.ID ' . ($reverse ? 'NOT' : '') . ' IN (' . implode(',', $IDs) . ')' : 'AND 0';
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
		return ($text ? ' AND WETABLE.modifierID = ' . intval($text) : '');
	}

	private function searchModFields($text){
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
							'WETABLE.ID IN (' . implode(',', $arr) . ') ' :
							($_ids[0] ?
								'WETABLE.ID IN (' . implode(',', $_ids[0]) . ') ' :
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
				$_db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE c.Dat LIKE "%' . $this->db->escape(trim($keyword)) . '%" AND l.nHash!=x\'' . md5("completeData") . '\' AND l.DocumentTable="' . $_db->escape(stripTblPrefix(FILE_TABLE)) . '"');
				$contents = $_db->getAll(true);

				$_db->query('SELECT DocumentID FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentObject LIKE "%' . $_db->escape(trim($keyword)) . '%" AND docTable="' . $this->db->escape(stripTblPrefix($table)) . '" AND Active=1');
				$contents = array_unique(array_merge($contents, $_db->getAll(true)));

				return ($contents ? ' WETABLE.ID IN(' . implode(',', $contents) . ')' : '');
			case TEMPLATES_TABLE:
				$_db->query('SELECT l.DID FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE c.Dat LIKE "%' . $this->db->escape(trim($keyword)) . '%" AND l.nHash=x\'' . md5("data") . '\' AND l.DocumentTable="' . $_db->escape(stripTblPrefix(TEMPLATES_TABLE)) . '"');
				$contents = $_db->getAll(true);

				return ($contents ? ' WETABLE.ID IN(' . implode(',', $contents) . ')' : '');
			case VERSIONS_TABLE:
				//FIXME: versions are searched even if the field is not checked!
				$contents = array();

				$_db->query('SELECT ID,documentElements  FROM ' . VERSIONS_TABLE . ' WHERE documentElements!=""');
				while($_db->next_record()){
					$elements = we_unserialize((substr_compare($_db->f('documentElements'), 'a%3A', 0, 4) == 0 ?
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
										stristr((is_array($v['dat']) ? we_serialize($v['dat'], SERIALIZE_PHP) : $v['dat']), $keyword)){
										$contents[] = $_db->f('ID');
									}
							}
						}
					}
				}

				return ($contents ? ' WETABLE.ID IN (' . implode(',', $contents) . ')' : '');
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
				$_db->query('SELECT DocumentID FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentObject LIKE "%' . $_db->escape(trim($keyword)) . '%" AND docTable="tblObjectFiles" AND Active=1');
				$Ids = array_merge($Ids, $_db->getAll(true));

				return ($Ids ? ' WETABLE.ID IN (' . implode(',', $Ids) . ')' : '');
		}

		return '';
	}

	function searchMediaLinks($useState = 0, $holdAllLinks = true, $inIDs = '', $returnQuery = false){
		$db = new DB_WE();
		$useState = intval($useState);
		$this->usedMedia = $this->usedMediaLinks = $tmpMediaLinks = $groups = $paths = array();

		$fields = $holdAllLinks ? 'ID,DocumentTable,remObj,isTemp,element' : 'DISTINCT remObj';
		$db->query('SELECT ' . $fields . ' FROM ' . FILELINK_TABLE . ' WHERE type="media" AND remTable="' . stripTblPrefix(FILE_TABLE) . '" ' . ($inIDs ? 'AND remObj IN (' . trim($db->escape($inIDs), ',') . ')' : '') . ' AND position=0');

		if($holdAllLinks){
			$types = array(
				FILE_TABLE => g_l('global', '[documents]'),
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
			);

			while($db->next_record()){
				$rec = $db->getRecord();
				$tmpMediaLinks[$rec['remObj']][] = array($rec['ID'], $rec['DocumentTable'], $rec['isTemp'], $rec['element']);
				$groups[$rec['DocumentTable']][] = $rec['ID'];
				$this->usedMedia[] = $rec['remObj'];
			}

			// get some more information about referencing objects
			$accessible = $paths = $isModified = $isUnpublished = $ct = $onclick = $type = $mod = $isTmpPossible = array(); // TODO: make these arrays elements of one array
			foreach($groups as $k => $v){// FIXME: ct is obslete?
				switch(addTblPrefix($k)){
					case FILE_TABLE:
					case defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE';
						$db->query('SELECT ID,Path,ModDate,Published,ContentType FROM ' . addTblPrefix($k) . ' WHERE ID IN (' . implode(',', array_unique($v)) . ')' . getWsQueryForSelector(addTblPrefix($k)));
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
						$db->query('SELECT ID,Path,ContentType FROM ' . addTblPrefix($k) . ' WHERE ID IN (' . implode(',', array_unique($v)) . ')' . getWsQueryForSelector(addTblPrefix($k)));
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
						if(permissionhandler::hasPerm('CAN_SEE_COLLECTIONS')){
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
						$modules = array(
							defined('BANNER_TABLE') ? BANNER_TABLE : 'BANNER_TABLE' => 'banner',
							defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE' => 'customer',
							defined('GLOSSARY_TABLE') ? GLOSSARY_TABLE : 'GLOSSARY_TABLE' => 'glossary',
							defined('NAVIGATION_TABLE') ? NAVIGATION_TABLE : 'NAVIGATION_TABLE' => 'navigation',
							defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE' => 'newsletter'
						);
						foreach($paths[$k] as $key => $v){
							$accessible[$k][$key] = true;
							$onclick[$k][$key] = 'weSearch.openModule(\'' . $modules[addTblPrefix($k)] . '\',' . $key . ')';
							$type[$k][$key] = 'module';
							$mod[$k][$key] = $modules[addTblPrefix($k)];
							$isTmpPossible[$k][$key] = false;
						}
						break;
					case CATEGORY_TABLE:
						if(permissionhandler::hasPerm('EDIT_KATEGORIE')){
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
				$this->usedMediaLinks['accessible']['mediaID_' . $m_id] = array();
				$this->usedMediaLinks['notaccessible']['mediaID_' . $m_id] = array();
				foreach($v as $val){// FIXME: table, ct are obsolete when onclick works
					if(!isset($this->usedMediaLinks['accessible']['mediaID_' . $m_id][$types[addTblPrefix($val[1])]][$val[0] . $val[3]])){
						if(isset($accessible[$val[1]][$val[0]])){
							$this->usedMediaLinks['accessible']['mediaID_' . $m_id][$types[addTblPrefix($val[1])]][$val[0] . $val[3]] = array(
								'exists' => isset($accessible[$val[1]][$val[0]]),
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
							);
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

		return $this->usedMedia ? (' AND WETABLE.ID ' . ($useState === 2 ? 'NOT ' : ' ') . 'IN(' . implode(',', $this->usedMedia) . ')') : ($useState === 2 ? '' : ' AND 0');
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
		$this->table = ($table ? : ($this->table ? : ''));
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
				$titles = array();
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
				$this->db->query('INSERT INTO ' . SEARCHRESULT_TABLE . ' (UID,docID,docTable,Text,Path,ParentID,IsFolder,ContentType,CreationDate,CreatorID,ModDate,Published,TableID) SELECT ' . $_SESSION['user']['ID'] . ',ID,"' . stripTblPrefix(OBJECT_FILES_TABLE) . '",Text,Path,ParentID,IsFolder,ContentType,CreationDate,CreatorID,ModDate,Published,TableID FROM `' . OBJECT_FILES_TABLE . '` of WHERE ' . str_replace('WETABLE.', 'of.', $this->where));
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
					$arr = array_filter(explode(',', $searchname), function($var){
						return is_numeric($var);
					});
					if(empty($arr)){
						return '0';
					} else {
						$searchname = implode(',', $arr);
					}
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

		//filter fields for each table
		foreach($tableInfo as $cur){
			if($searchfield != $cur['name']){
				continue;
			}
			$searchfield = 'WETABLE.' . $cur['name'];

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
			} elseif(($cur['name'] === 'MasterTemplateID' && $this->table === TEMPLATES_TABLE) || ($cur['name'] === 'temp_template_id' && $this->table == FILE_TABLE) || ($cur['name'] === 'TemplateID' && $this->table === VERSIONS_TABLE)){
				$searchname = path_to_id($searchname, TEMPLATES_TABLE);
				$searching = " = '" . $this->db->escape($searchname) . "' ";

				if(($cur['name'] === 'temp_template_id' && $this->table == FILE_TABLE) || ($cur['name'] === 'TemplateID' && $this->table == VERSIONS_TABLE)){
					if($this->table == FILE_TABLE){
						$sql .= $this->sqlwhere('WETABLE.TemplateID', $searching, $operator . '( (Published >= ModDate AND Published !=0 AND ') .
							$this->sqlwhere($searchfield, $searching, ' ) OR (Published < ModDate AND ') .
							'))';
					} elseif($this->table == VERSIONS_TABLE){
						$sql .= $this->sqlwhere('WETABLE.TemplateID', $searching, $operator . ' ');
					}
				} else {
					$sql .= $this->sqlwhere($searchfield, $searching, $operator);
				}
			} elseif($cur['name'] == 'Published' || $cur['name'] == 'CreationDate' || $cur['name'] == 'ModDate'){
				if($cur['name'] == 'Published' && $this->table == FILE_TABLE || $this->table == OBJECT_FILES_TABLE || $cur['name'] != 'Published'){
					if($this->table == VERSIONS_TABLE && $cur['name'] == 'CreationDate' || $cur['name'] == 'ModDate'){
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
							case 'LO':
								$searching = ' < "' . $timestampStart . '" ';
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
							case 'LEQ':
								$searching = ' <= "' . $timestampEnd . '" ';
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
							case 'HI':
								$searching = ' > "' . $timestampEnd . '" ';
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
							case 'HEQ':
								$searching = ' >= "' . $timestampStart . '" ';
								$sql .= $this->sqlwhere($searchfield, $searching, $operator);
								break;
						}
					}
				}
			} elseif(isset($searchlocation)){
				switch($searchlocation){
					case 'END':
						$searching = ' LIKE "%' . $this->db->escape($searchname) . '" ';
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						break;
					case 'START':
						$searching = ' LIKE "' . $this->db->escape($searchname) . '%" ';
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						break;
					case 'IN':
						$searchname = str_replace(array('\_', '\%'), array('_', '%'), $searchname);
						$searching = ' IN ("' . implode('","', array_map('trim', explode(',', $searchname))) . '") ';
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						break;
					case 'IS':
						$searchname = str_replace(array('\_', '\%'), array('_', '%'), $searchname);
						$searching = '="' . $this->db->escape($searchname) . '" ';
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						break;
					case 'LO':
						$searching = ' < "' . $this->db->escape($searchname) . '" ';
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						break;
					case 'LEQ':
						$searching = ' <= "' . $this->db->escape($searchname) . '" ';
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						break;
					case 'HI':
						$searching = ' > "' . $this->db->escape($searchname) . '" ';
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						break;
					case 'HEQ':
						$searching = ' >= "' . $this->db->escape($searchname) . '" ';
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						break;
					default :
						$searching = ' LIKE "%' . $this->db->escape($searchname) . '%" ';
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						break;
				}
			}
			//found col, return
			return $sql;
		}

		return '';
	}

	static function ofFolderAndChildsOnly($folderID, $table){//move this to view class; or verse visa
		$DB_WE = new DB_WE();
		$_SESSION['weS']['weSearch']['countChilds'] = array();
		//fix #2940
		if(is_array($folderID)){
			foreach($folderID as $k){
				$childsOfFolderId = self::getChildsOfParentId($k, $table, $DB_WE);
				$ids = implode(',', $childsOfFolderId);
			}
			return ' AND WETABLE.ParentID IN (' . $ids . ')';
		}
		$childsOfFolderId = self::getChildsOfParentId($folderID, $table, $DB_WE);

		return ' AND WETABLE.ParentID IN (' . implode(',', $childsOfFolderId) . ')';
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
	  $db->addTable('test_SEARCH_TEMP_TABLE', array('test' => 'VARCHAR(1) NOT NULL'), array(), 'MEMORY', true);

	  $db->next_record();

	  $return = (stristr($db->Error, 'Access denied') ? false : true);

	  $db->delTable('test_SEARCH_TEMP_TABLE', true);

	  return $return;
	  } */

	/* static function checkRightDropTable(){
	  $db = new DB_WE();
	  $db->addTable('test_SEARCH_TEMP_TABLE', array('test' => 'VARCHAR(1) NOT NULL'), array(), 'MEMORY');
	  $db->delTable('test_SEARCH_TEMP_TABLE');
	  return (stristr($db->Error, 'command denied') ? false : true);
	  } */

	function getResultCount(){
		return f('SELECT COUNT(1) FROM ' . SEARCHRESULT_TABLE . ' WHERE UID=' . $_SESSION['user']['ID'], '', $this->db);
	}

	static function cleanOldEntries(){
		$GLOBALS['DB_WE']->query('DELETE FROM ' . SEARCHRESULT_TABLE . ' WHERE UID IN (SELECT ID FROM ' . USER_TABLE . ' WHERE (Ping+INTERVAL ' . we_base_constants::PING_TOLERANZ . ' second)<NOW() )');
	}

}
