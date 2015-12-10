<?php
/**
 * webEdition CMS
 *
 * $Rev: 10845 $
 * $Author: lukasimhof $
 * $Date: 2015-12-01 00:00:40 +0100 (Di, 01 Dez 2015) $
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

class we_doclist_search extends we_search_search{
	public $Model;
	public $View;
	protected $whichSearch;

	public function __construct($view = null) {
		parent::__construct($view ? : new we_doclist_view());
		//$this->Model = &$this->View->Model;
		$this->Model = $this->View->Model;
		$this->whichSearch = we_search_view::SEARCH_DOCLIST;
	}

	public function getModel(){
		return $this->Model;
	}

	public function searchProperties($table = ''){
		$DB_WE = new DB_WE();
		$foundItems = 0;
		$_result = $saveArrayIds = $searchText = array();
		$_SESSION['weS']['weSearch']['foundItems'] = 0;

		$searchFields = $this->Model->searchFields;
		$searchText = $this->Model->search;
		$table = $table ? : ($this->Model->searchTable ? : FILE_TABLE);
		$location = $this->Model->location;
		$_order = $this->Model->OrderDoclistSearch;
		//$_view = $this->Model->setViewDoclistSearch;
		$_searchstart = $this->Model->searchstartDoclistSearch;
		$_anzahl= $this->Model->anzahlDoclistSearch;

		$where = array();
		$this->settable($table);


		if(!we_search_search::checkRightTempTable() && !we_search_search::checkRightDropTable()){
			echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('searchtool', '[noTempTableRightsDoclist]'), we_message_reporting::WE_MESSAGE_NOTICE));
			return '';
		}
		if($this->Model->folderID){
			$this->createTempTable();

			foreach($searchFields as $i => $searchField){

				$w = "";
				if(isset($searchText[0])){
					$searchString = (isset($searchText[$i]) ? $searchText[$i] : $searchText[0]);
				}
				if(!empty($searchString)){

					switch($searchField){
						default:
						case 'Text':
							if(isset($searchField) && isset($location[$i])){
								$where[] = $this->searchfor($searchString, $searchField, $location[$i], $table);
							}
						case 'Content':
						case 'Status':
						case 'Speicherart':
						case 'CreatorName':
						case 'WebUserName':
						case 'temp_category':
							break;
					}

					switch($searchField){
						case 'Content':
							$w = $this->searchContent($searchString, $table);
							$where[] = ($w ? $w : '0');
							break;

						case 'Title':
							$w = $this->searchInTitle($searchString, $table);
							$where[] = ($w ? $w : '0');

							break;
						case "Status":
						case "Speicherart":
							if($searchString != ""){
								if($table === FILE_TABLE){
									$w = $this->getStatusFiles($searchString, $table);
									$where[] = $w;
								}
							}
							break;
						case 'CreatorName':
						case 'WebUserName':
							if($searchString != ""){
								$w = $this->searchSpecial($searchString, $searchField, $location[$i]);
								$where[] = $w;
							}
							break;
						case 'temp_category':
							$w = $this->searchCategory($searchString, $table, $searchField);
							$where[] = $w;
							break;
					}
				}
			}

			$where[] = 'AND ParentID=' . intval($this->Model->folderID);
			switch($table){
				case FILE_TABLE:
					$where[] = 'AND (RestrictOwners IN (0,' . intval($_SESSION['user']['ID']) . ') OR FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Owners))';
					break;
				case TEMPLATES_TABLE:
					//$where[] = 'AND (RestrictUsers IN (0,' . intval($_SESSION['user']['ID']) . ') OR FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Users))';
					break;
				case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					$where[] = 'AND (RestrictOwners IN (0,' . intval($_SESSION['user']['ID']) . ') OR FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Owners))';
					break;
				case (defined('OBJECT_TABLE') ? OBJECT_TABLE : OBJECT_TABLE):
					$where[] = 'AND (RestrictUsers IN (0,' . intval($_SESSION['user']['ID']) . ') OR FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Users))';
					break;
			}
			$whereQuery = '1 ' . implode(' ', $where);
			//we_database_base::t_e_query(5);
			$this->setwhere($whereQuery);
			$this->insertInTempTable($whereQuery, $table, id_to_path($this->Model->folderID) . '/');

			$foundItems = $this->countitems($whereQuery, $table);
			$_SESSION['weS']['weSearch']['foundItems'] = $this->founditems = $foundItems;

			$this->selectFromTempTable($_searchstart, $_anzahl, $_order);

			while($this->next_record()){
				if(!isset($saveArrayIds[$this->Record ['ContentType']][$this->Record ['ID']])){
					$saveArrayIds[$this->Record ['ContentType']][$this->Record ['ID']] = $this->Record ['ID'];
					$_result[] = array_merge(array('Table' => $table), $this->Record);
				}
			}
		}

		if(!$_SESSION['weS']['weSearch']['foundItems']){
			return array();
		}
		$DB_WE->query('DROP TABLE IF EXISTS SEARCH_TEMP_TABLE');

		foreach($_result as $k => $v){
			$_result[$k]["Description"] = "";
			if($_result[$k]["Table"] == FILE_TABLE && $_result[$k]['Published'] >= $_result[$k]['ModDate'] && $_result[$k]['Published'] != 0){
				$_result[$k]["Description"] = f('SELECT c.Dat FROM (' . FILE_TABLE . ' f LEFT JOIN ' . LINK_TABLE . ' l ON (f.ID=l.DID)) LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE f.ID=' . intval($_result[$k]["ID"]) . ' AND l.nHash=x\'' . md5("Description") . '\' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"', '', $DB_WE);
			} else {
				if(($obj = f('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($_result[$k]["ID"]) . ' AND DocTable="tblFile" AND Active=1', '', $DB_WE))){
					$tempDoc = we_unserialize($obj);
					if(isset($tempDoc[0]['elements']['Description']) && $tempDoc[0]['elements']['Description']['dat']){
						$_result[$k]['Description'] = $tempDoc[0]['elements']['Description']['dat'];
					}
				}
			}
		}
		return $_result;
		//return self::makeContent($DB_WE, $_result, $_view);
	}
}