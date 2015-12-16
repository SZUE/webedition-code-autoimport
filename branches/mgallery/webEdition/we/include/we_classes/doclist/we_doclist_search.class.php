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
	public $View;
	protected $whichSearch;

	public function __construct($view = null) {
		parent::__construct($view ? : new we_doclist_view());
		$this->whichSearch = we_search_view::SEARCH_DOCLIST;
	}

	public function searchProperties($model, $table = ''){ // FIXME: handle model in like in other searches
		$DB_WE = new DB_WE();
		$foundItems = 0;
		$_result = $saveArrayIds = $currentSearch = array();
		$_SESSION['weS']['weSearch']['foundItems'] = 0;

		$currentSearchFields = $model->getProperty('currentSearchFields');
		$currentSearch = $model->getProperty('currentSearch');
		$table = $table ? : (($t = $model->getProperty('currentSearchTables')) ? $t[0]: FILE_TABLE);
		$currentLocation = $model->getProperty('currentLocation');
		$currentOrder = $model->getProperty('currentOrder');
		//$_view = $model->getProperty('currentSetView');
		$currentSearchstart = $model->getProperty('currentSearchstart');
		$currentAnzahl= $model->getProperty('currentAnzahl');
		$currentFolderID = $model->getProperty('currentFolderID');

		$where = array();
		$this->settable($table);


		if(!we_search_search::checkRightTempTable() && !we_search_search::checkRightDropTable()){
			echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('searchtool', '[noTempTableRightsDoclist]'), we_message_reporting::WE_MESSAGE_NOTICE));
			return '';
		}
		if($currentFolderID){
			$this->createTempTable();

			foreach($currentSearchFields as $i => $searchField){

				$w = "";
				if(isset($currentSearch[0])){
					$searchString = (isset($currentSearch[$i]) ? $currentSearch[$i] : $currentSearch[0]);
				}
				if(!empty($searchString)){

					switch($searchField){
						default:
						case 'Text':
							if(isset($searchField) && isset($currentLocation[$i])){
								$w = $this->searchfor($searchString, $searchField, $currentLocation[$i], $table);
								$where[] = ($w ? $w : 'AND 0');
							}
						case 'Content':
						case 'Title':
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
							$where[] = 'AND' . ($w ? $w : '0');
							break;
						
						case 'Title':
							break;
							/*
							$w = $this->searchInTitle($searchString, $table);
							$where[] = ($w ? $w : '0');
							 * 
							 */
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
								$w = $this->searchSpecial($searchString, $searchField, $currentLocation[$i]);
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

			$where[] = 'AND ParentID=' . intval($currentFolderID);
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
			$whereQuery = '1 ' . implode(' ', $where);t_e('where arr', $where);
			//we_database_base::t_e_query(5);
			$this->setwhere($whereQuery);
			$this->insertInTempTable($whereQuery, $table, id_to_path($currentFolderID) . '/');

			$foundItems = $this->countitems($whereQuery, $table);
			$_SESSION['weS']['weSearch']['foundItems'] = $this->founditems = $foundItems;

			$this->selectFromTempTable($currentSearchstart, $currentAnzahl, $currentOrder);

			while($this->next_record()){
				if(!isset($saveArrayIds[$this->Record['ContentType']][$this->Record['ID']])){
					$saveArrayIds[$this->Record['ContentType']][$this->Record['ID']] = $this->Record['ID'];
					$_result[] = array_merge(array('Table' => $table), $this->Record);
				}
			}
		}

		if(!$this->founditems){
			return array();
		}
		$this->db->query('DROP TABLE IF EXISTS SEARCH_TEMP_TABLE');

		foreach($_result as $k => $v){
			$_result[$k]['Description'] = '';
			if($_result[$k]['Table'] == FILE_TABLE && $_result[$k]['Published'] >= $_result[$k]['ModDate'] && $_result[$k]['Published'] != 0){
				$_result[$k]['Description'] = f('SELECT c.Dat FROM (' . FILE_TABLE . ' f LEFT JOIN ' . LINK_TABLE . ' l ON (f.ID=l.DID)) LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE f.ID=' . intval($_result[$k]["ID"]) . ' AND l.nHash=x\'' . md5("Description") . '\' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"', '', $DB_WE);
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
	}
}