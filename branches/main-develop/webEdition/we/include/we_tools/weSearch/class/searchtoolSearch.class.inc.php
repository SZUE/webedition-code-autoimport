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

class searchtoolsearch extends we_search{

	//for doclist!
	/**
	 * @var integer: number of searchfield-rows
	 */
	var $height;

	/**
	 * @var string: default order of the result columns
	 */
	var $order = "Text";

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
	var $setView = 0;

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

	/**
	 * @abstract get data from fields, used in the doclistsearch
	 */
	function initSearchData(){
		if(isset($GLOBALS['we_doc'])){
			$obj = $GLOBALS['we_doc'];

			if(isset($_REQUEST["searchstart"])){
				$obj->searchclassFolder->searchstart = ($_REQUEST["searchstart"]);
			}
			if(isset($_REQUEST["setView"])){
				$this->db->query("UPDATE " . FILE_TABLE . " SET listview=" . intval($_REQUEST['setView']) . " WHERE ID=" . intval($obj->ID));
				$obj->searchclassFolder->setView = ($_REQUEST["setView"]);
			} else{
				$obj->searchclassFolder->setView = f(
					"SELECT listview FROM " . FILE_TABLE . " WHERE ID=" . intval($obj->ID), "listview", $GLOBALS['DB_WE']);
			}
			if(isset($_REQUEST["mode"])){
				$obj->searchclassFolder->mode = ($_REQUEST["mode"]);
			}
			if(isset($_REQUEST["order"])){
				$obj->searchclassFolder->order = ($_REQUEST["order"]);
			}
			if(isset($_REQUEST["anzahl"])){
				$obj->searchclassFolder->anzahl = ($_REQUEST["anzahl"]);
			}
			if(isset($_REQUEST["searchFields"])){
				$obj->searchclassFolder->searchFields = ($_REQUEST["searchFields"]);
				$obj->searchclassFolder->height = count($_REQUEST["searchFields"]);
			} elseif(!isset($_REQUEST["searchFields"]) && isset($_REQUEST["searchstart"])){
				$obj->searchclassFolder->height = 0;
			} elseif(!isset($_REQUEST["searchFields"]) && !isset($_REQUEST["searchstart"])){
				$obj->searchclassFolder->height = 1;
			} else{
				$obj->searchclassFolder->height = 1;
			}
			if(isset($_REQUEST["location"])){
				$obj->searchclassFolder->location = ($_REQUEST["location"]);
			}
			if(isset($_REQUEST["search"])){
				$obj->searchclassFolder->search = ($_REQUEST["search"]);
			}
		} else{
			if(isset($_REQUEST['we_cmd']["setView"]) && isset($_REQUEST["id"])){
				$this->db->query(
					"UPDATE " . FILE_TABLE . " SET listview=" . intval($_REQUEST['we_cmd']["setView"]) . " WHERE ID=" . intval($_REQUEST["id"]));
			}
		}
	}

	function getModFields(){

		$modFields = array();
		$versions = new weVersions();
		foreach($versions->modFields as $k => $v){
			if($k != "status"){
				$modFields[$k] = $k;
			}
		}

		return $modFields;
	}

	function getUsers(){

		$_db = new DB_WE();
		$vals = array();

		$_db->query("SELECT ID, Text FROM " . USER_TABLE);
		while($_db->next_record()) {
			$v = $_db->f("ID");
			$t = $_db->f("Text");
			$vals[$v] = $t;
		}

		return $vals;
	}

	function getFields($row, $whichSearch = ""){

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
			'temp_doc_type' => g_l('searchtool', '[temp_doc_type]'),
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


		if($whichSearch == "doclist"){
			unset($tableFields["Path"]);
			unset($tableFields["ParentIDDoc"]);
			unset($tableFields["ParentIDObj"]);
			unset($tableFields["ParentIDTmpl"]);
			unset($tableFields["MasterTemplateID"]);
		}

		if(!we_hasPerm('CAN_SEE_DOCUMENTS')){
			unset($tableFields["ParentIDDoc"]);
		}

		if(!defined('OBJECT_FILES_TABLE')){
			unset($tableFields["ParentIDObj"]);
		}

		if(!we_hasPerm('CAN_SEE_OBJECTFILES')){
			unset($tableFields["ParentIDObj"]);
		}

		if($_SESSION["we_mode"] == "seem"){
			unset($tableFields["ParentIDTmpl"]);
		}

		if(!we_hasPerm('CAN_SEE_TEMPLATES')){
			unset($tableFields["ParentIDTmpl"]);
			unset($tableFields["temp_template_id"]);
			unset($tableFields["MasterTemplateID"]);
		}

		return $tableFields;
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
		$fields = array(
			'jeder' => g_l('searchtool', '[jeder]'),
			'dynamisch' => g_l('searchtool', '[dynamisch]'),
			'statisch' => g_l('searchtool', '[statisch]')
		);

		return $fields;
	}

	function getLocation($whichField = ""){
		$locations = array(
			'CONTAIN' => g_l('searchtool', '[CONTAIN]'),
			'IS' => g_l('searchtool', '[IS]'),
			'START' => g_l('searchtool', '[START]'),
			'END' => g_l('searchtool', '[END]'),
			'<' => g_l('searchtool', '[<]'),
			'<=' => g_l('searchtool', '[<=]'),
			'>=' => g_l('searchtool', '[>=]'),
			'>' => g_l('searchtool', '[>]')
		);

		if($whichField == "date"){
			unset($locations["CONTAIN"]);
			unset($locations["START"]);
			unset($locations["END"]);
		}

		return $locations;
	}

	function getDoctypes(){
		$_db = new DB_WE();

		$q = getDoctypeQuery($_db);
		$vals = array();

		$_db->query("SELECT * FROM " . DOC_TYPES_TABLE . " $q");
		while($_db->next_record()) {
			$v = $_db->f("ID");
			$t = $_db->f("DocType");
			$vals[$v] = $t;
		}

		return $vals;
	}

	function searchInTitle($keyword, $table){

		$titles = array();
		$_db2 = new DB_WE();
		//first check published documents
		$query = "SELECT a.DID FROM " . LINK_TABLE . " a LEFT JOIN " . CONTENT_TABLE . " b on (a.CID = b.ID) WHERE a.Name='Title' AND b.Dat LIKE '%" . $_db2->escape(
				trim($keyword)) . "%' AND NOT a.DocumentTable!='" . TEMPLATES_TABLE . "'";
		$_db2->query($query);
		while($_db2->next_record()) {
			$titles[] = $_db2->f('DID');
		}
		//check unpublished documents
		$query2 = "SELECT DocumentID, DocumentObject  FROM " . TEMPORARY_DOC_TABLE . " WHERE DocTable = 'tblFile' AND Active = 1 AND DocumentObject LIKE '%" . $_db2->escape(
				trim($keyword)) . "%'";
		$_db2->query($query2);
		while($_db2->next_record()) {
			$tempDoc = unserialize($_db2->f('DocumentObject'));
			if(isset($tempDoc[0]['elements']['Title']) && $tempDoc[0]['elements']['Title']['dat'] != ""){
				$keyword = str_replace(array("\_", "\%"), array("_", "%"), $keyword);
				if(stristr($tempDoc[0]['elements']['Title']['dat'], $keyword)){
					$titles[] = $_db2->f('DocumentID');
				}
			}
		}

		$where = "";
		if(!empty($titles)){
			$where = " " . $table . ".ID IN (" . makeCSVFromArray($titles) . ")";
			return $where;
		}

		return $where;
	}

	function searchCategory($keyword, $table){
		if($table != TEMPLATES_TABLE){
			$userIDs = array();
			$_db = new DB_WE();
			$_db2 = new DB_WE();
			switch($table){
				case FILE_TABLE:
					$field = "temp_category";
					$field2 = "Category";
					$query = "SELECT ID, " . $field . ", " . $field2 . "  FROM " . $table . " WHERE ((" . $field2 . " != NULL OR " . $field2 . " != '') AND Published >= ModDate AND Published !=0) OR (Published < ModDate AND (" . $field . " != NULL OR " . $field . " != '')) ";
					break;
				case VERSIONS_TABLE:
					$field = "Category";
					$query = "SELECT ID," . $field . "  FROM " . $table . " WHERE " . $field . " != NULL OR " . $field . " != '' ";
					break;
				case (defined("OBJECT_TABLE") ? OBJECT_TABLE : -1):
					$field = "DefaultCategory";
					$query = "SELECT ID," . $field . "  FROM " . $table . " WHERE " . $field . " != NULL OR " . $field . " != '' ";
					break;
				case (defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE : -2):
					$field = "Category";
					$query = "SELECT ID," . $field . "  FROM " . $table . " WHERE " . $field . " != NULL OR " . $field . " != '' AND Published >= ModDate AND Published !=0";
					break;
			}
			$res = array();
			$res2 = array();

			$_db->query($query);

			if($table == FILE_TABLE){
				while($_db->next_record()) {
					if($_db->f($field) == ""){
						$res[$_db->f('ID')] = $_db->f($field2);
					} else{
						$res[$_db->f('ID')] = $_db->f($field);
					}
				}
			} elseif(defined("OBJECT_TABLE") && $table == OBJECT_TABLE){
				while($_db->next_record()) {
					$res[$_db->f('ID')] = $_db->f($field);
				}
			} elseif(defined("OBJECT_FILES_TABLE")){
				//search in public objects first and write them in the array
				while($_db->next_record()) {
					$res[$_db->f('ID')] = $_db->f($field);
				}
				//search in unpublic objects and write them in the array
				$query2 = "SELECT DocumentObject  FROM " . TEMPORARY_DOC_TABLE . " WHERE DocTable = 'tblObjectFiles' AND Active = 1";
				$_db2->query($query2);
				while($_db2->next_record()) {
					$tempObj = unserialize($_db2->f('DocumentObject'));
					if(isset($tempObj[0]["Category"]) && $tempObj[0]["Category"] != ""){
						if(!array_key_exists($tempObj[0]["ID"], $res)){
							$res[$tempObj[0]["ID"]] = $tempObj[0]["Category"];
						}
					}
				}
			}

			foreach($res as $k => $v){
				$res2[$k] = makeArrayFromCSV($v);
			}

			$where = "";
			$i = 0;

			$keyword = path_to_id($keyword, CATEGORY_TABLE);

			foreach($res2 as $k => $v){
				foreach($v as $v2){
					//look if the value is numeric
					if(preg_match("=^[0-9]+$=i", $v2)){
						if($v2 == $keyword){
							if($i > 0){
								$where .= " OR ";
							} else{
								$where .= " AND (";
							}
							$where .= " " . $_db->escape($table) . ".ID = " . intval($k);
							$i++;
						}
					}
				}
			}

			if($where != ""){
				$where .= " )";
			} else{
				$where .= " 0 ";
			}
			return $where;
		} else{
			return " 0 ";
		}
	}

	function searchSpecial($keyword, $searchFields, $searchlocation){
		$userIDs = array();
		$_db = new DB_WE();
		if($searchFields == "CreatorName"){
			$_table = USER_TABLE;
			$field = "Text";
			$fieldFileTable = "CreatorID";
		} elseif($searchFields == "WebUserName"){
			$_table = CUSTOMER_TABLE;
			$field = "Username";
			$fieldFileTable = "WebUserID";
		}

		$query = "SELECT ID FROM " . $_db->escape($_table) . " WHERE " . $field . " ";

		if(isset($searchlocation)){
			switch($searchlocation){
				case "END" :
					$searching = " LIKE '%" . $_db->escape($keyword) . "' ";
					break;
				case "START" :
					$searching = " LIKE '" . $_db->escape($keyword) . "%' ";
					break;
				case "IS" :
					$searching = " = '" . $_db->escape($keyword) . "' ";
					break;
				case "<" :
				case "<=" :
				case ">" :
				case ">=" :
					$searching = " " . $searchlocation . " '" . $_db->escape($keyword) . "' ";
					break;
				default :
					$searching = " LIKE '%" . $_db->escape($keyword) . "%' ";
					break;
			}
		}

		$query .= $searching;

		$_db->query($query);
		while($_db->next_record()) {
			$userIDs[] = ($_db->f('ID'));
		}

		$i = 0;
		$where = "";
		if(!empty($userIDs)){
			foreach($userIDs as $id){
				if($i > 0){
					$where .= " OR ";
				} else{
					$where .= " (";
				}

				$where .= $fieldFileTable . " = " . intval($id) . ' ';

				$i++;
			}

			$where .= ")";
		} else{
			$where .= "0";
		}

		return $where;
	}

	function getStatusFiles($status, $table){

		$where = "";

		switch($status){
			case "jeder" :
				$where .= "AND (" . escape_sql_query($table) . ".ContentType='text/webedition' OR " . escape_sql_query($table) . ".ContentType='text/html' OR " . escape_sql_query($table) . ".ContentType='objectFile')";
				break;

			case "geparkt" :
				if($table == VERSIONS_TABLE){
					$where .= "AND " . escape_sql_query($table) . ".status='unpublished'";
				} else{
					$where .= "AND ((" . escape_sql_query($table) . ".Published=0) AND (" . escape_sql_query($table) . ".ContentType='text/webedition' OR " . escape_sql_query($table) . ".ContentType='text/html' OR " . escape_sql_query($table) . ".ContentType='objectFile'))";
				}
				break;

			case "veroeffentlicht" :
				if($table == VERSIONS_TABLE){
					$where .= "AND " . escape_sql_query($table) . ".status='published'";
				} else{
					$where .= "AND ((" . escape_sql_query($table) . ".Published >= " . escape_sql_query($table) . ".ModDate AND " . escape_sql_query($table) . ".Published !=0) AND (" . escape_sql_query($table) . ".ContentType='text/webedition' OR " . escape_sql_query($table) . ".ContentType='text/html' OR " . escape_sql_query($table) . ".ContentType='objectFile'))";
				}
				break;
			case "geaendert" :
				if($table == VERSIONS_TABLE){
					$where .= "AND " . escape_sql_query($table) . ".status='saved'";
				} else{
					$where .= "AND ((" . escape_sql_query($table) . ".Published < " . escape_sql_query($table) . ".ModDate AND " . escape_sql_query($table) . ".Published !=0) AND (" . escape_sql_query($table) . ".ContentType='text/webedition' OR " . escape_sql_query($table) . ".ContentType='text/html' OR " . escape_sql_query($table) . ".ContentType='objectFile'))";
				}
				break;
			case "veroeff_geaendert" :
				$where .= "AND ((" . escape_sql_query($table) . ".Published >= " . escape_sql_query($table) . ".ModDate OR " . escape_sql_query($table) . ".Published < " . escape_sql_query($table) . ".ModDate AND " . escape_sql_query($table) . ".Published !=0) AND (" . escape_sql_query($table) . ".ContentType='text/webedition' OR " . escape_sql_query($table) . ".ContentType='text/html' OR " . escape_sql_query($table) . ".ContentType='objectFile'))";
				break;

			case "geparkt_geaendert" :
				if($table == VERSIONS_TABLE){
					$where .= "AND " . escape_sql_query($table) . ".status!='published'";
				} else{
					$where .= "AND ((" . escape_sql_query($table) . ".Published=0 OR " . escape_sql_query($table) . ".Published < " . escape_sql_query($table) . ".ModDate) AND (" . escape_sql_query($table) . ".ContentType='text/webedition' OR " . escape_sql_query($table) . ".ContentType='text/html' OR " . escape_sql_query($table) . ".ContentType='objectFile'))";
				}
				break;
			case "dynamisch" :
				if($table != FILE_TABLE && $table != VERSIONS_TABLE){
					return;
				}
				$where .= "AND ((" . escape_sql_query($table) . ".IsDynamic=1) AND (" . escape_sql_query($table) . ".ContentType='text/webedition'))";
				break;
			case "statisch" :
				if($table != FILE_TABLE && $table != VERSIONS_TABLE){
					return;
				}
				$where .= "AND ((" . escape_sql_query($table) . ".IsDynamic=0) AND (" . escape_sql_query($table) . ".ContentType='text/webedition'))";
				break;
			case "deleted" :
				if($table == VERSIONS_TABLE){
					$where .= "AND " . escape_sql_query($table) . ".status='deleted' ";
				}
				break;
		}

		return $where;
	}

	function searchModifier($text, $table){

		$where = "";
		if($text != ""){
			$where .= " AND " . escape_sql_query($table) . ".modifierID = '" . abs($text) . "'";
		}
		return $where;
	}

	function searchModFields($text, $table){

		$where = "";
		$db = new DB_WE();
		$versions = new weVersions();

		$modConst[] = $versions->modFields[$text]['const'];

		if(!empty($modConst)){
			$modifications = array();
			$ids = array();
			$_ids = array();
			$query = "SELECT ID, modifications FROM " . VERSIONS_TABLE . " WHERE modifications != '' ";
			$db->query($query);

			while($db->next_record()) {
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

			if(!empty($ids)){
				foreach($ids as $key => $val){
					$_ids[] = $val;
				}
				$arr = array();
				if(!empty($_ids[0])){
					//more then one field
					$mtof = false;
					foreach($_ids as $k => $v){
						if($k != 0){
							$mtof = true;
							foreach($v as $key => $val){
								if(!in_array($val, $_ids[0])){
									unset($_ids[0][$val]);
								} else{
									$arr[] = $val;
								}
							}
						}
					}
					if($mtof){
						$where .= " AND " . $table . ".ID IN (" . makeCSVFromArray($arr) . ") ";
					} elseif(!empty($_ids[0])){
						$where .= " AND " . $table . ".ID IN (" . makeCSVFromArray($_ids[0]) . ") ";
					} else{
						$where .= " AND 0";
					}
				}
			}
		}

		return $where;
	}

	function getTblName($table){

		return stripTblPrefix($table);
	}

	function searchContent($keyword, $table){

		$_db = new DB_WE();
		$_db2 = new DB_WE();
		$contents = array();
		$w = "";

		if($table == FILE_TABLE || $table == TEMPLATES_TABLE){

			$query = "SELECT a.Name, b.Dat, a.DID FROM " . LINK_TABLE . " a LEFT JOIN " . CONTENT_TABLE . " b on (a.CID = b.ID) WHERE b.Dat LIKE '%" . escape_sql_query(
					trim($keyword)) . "%' AND a.Name!='completeData' AND a.DocumentTable='" . escape_sql_query(stripTblPrefix($table)) . "'";
			$_db2->query($query);
			while($_db2->next_record()) {
				$contents[] = $_db2->f('DID');
			}

			if($table == FILE_TABLE){
				$query2 = "SELECT DocumentID, DocumentObject  FROM " . TEMPORARY_DOC_TABLE . " WHERE DocumentObject LIKE '%" . escape_sql_query(
						trim($keyword)) . "%' AND DocTable = '" . escape_sql_query(stripTblPrefix($table)) . "' AND Active = 1";
				$_db2->query($query2);
				while($_db2->next_record()) {
					$contents[] = $_db2->f('DocumentID');
				}
			}

			if(!empty($contents)){
				$w = "  " . $table . ".ID IN (" . makeCSVFromArray($contents) . ")";
			} else{
				$w = "";
			}
		} elseif($table == VERSIONS_TABLE){

			$query2 = "SELECT ID, documentElements  FROM " . VERSIONS_TABLE . " ";
			$_db2->query($query2);
			while($_db2->next_record()) {
				$tempDoc[0]['elements'] = unserialize(
					html_entity_decode(urldecode($_db2->f('documentElements')), ENT_QUOTES));

				if(isset($tempDoc[0]['elements'])){
					foreach($tempDoc[0]['elements'] as $k => $v){
						if($k != "Title" && $k != "Charset" && isset($tempDoc[0]['elements'][$k]['dat']) && stristr(
								$tempDoc[0]['elements'][$k]['dat'], $keyword)){
							$contents[] = $_db2->f('ID');
						}
					}
				}
			}

			if(!empty($contents)){
				$w = "  " . $table . ".ID IN (" . makeCSVFromArray($contents) . ")";
			} else{
				$w = "";
			}
		} elseif(defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE){

			$_classes = array();
			$Ids = array();
			$regs = array();

			$_db->query("SELECT ID FROM " . OBJECT_TABLE);
			while($_db->next_record()) {
				$_classes[] = $_db->f('ID');
			}

			//published objects
			for($i = 1; $i <= count($_classes); $i++){
				$_obj_table = OBJECT_X_TABLE . $i;
				//$_obj_table = strtolower($_obj_table);
				$tableInfo = $_db->metadata($_obj_table);
				$fields = array();
				for($c = 0; $c < count($tableInfo); $c++){
					if(preg_match('/(.+?)_(.*)/', $tableInfo[$c]["name"], $regs)){
						if($regs[1] != "OF" && $regs[1] != "variant"){
							$fields[] = array(
								"name" => $tableInfo[$c]["name"],
								"type" => $regs[1],
								"length" => $tableInfo[$c]["len"]
							);
						}
					}
				}
				if(count($fields) == 0){
					continue;
				}
				$field = array();
				foreach($fields as $k => $v){
					$field[] = $v['name'];
				}
				$where = '';
				foreach($field as $k => $v){
					if($k != 0){
						$where .= " OR ";
					}
					$where .= $v . " LIKE '%" . escape_sql_query(trim($keyword)) . "%' ";
				}

				$_db->query('SELECT ' . escape_sql_query($_obj_table) . '.OF_ID FROM ' . escape_sql_query($_obj_table) . ' WHERE ' . $where);
				while($_db->next_record()) {
					$Ids[] = $_db->f('OF_ID');
				}
			}
			//only saved objects
			$query2 = "SELECT DocumentID, DocumentObject  FROM " . TEMPORARY_DOC_TABLE . " WHERE DocumentObject LIKE '%" . escape_sql_query(
					trim($keyword)) . "%' AND DocTable = 'tblObjectFiles' AND Active = 1";
			$_db2->query($query2);
			while($_db2->next_record()) {
				$Ids[] = $_db2->f('DocumentID');
			}

			if(!empty($Ids)){
				$w = "  " . OBJECT_FILES_TABLE . ".ID IN (" . makeCSVFromArray($Ids) . ")";
			} else{
				$w = "";
			}
		}

		return $w;
	}

	function getDefaultTableName($name){
		switch($name){
			case FILE_TABLE :
				return 'tblFile';
			case TEMPLATES_TABLE :
				return 'tblTemplates';
			case OBJECT_TABLE :
				return 'tbObject';
			case OBJECT_FILES_TABLE :
				return 'tblObjectFile';
			case DOC_TYPES_TABLE :
				return 'tblDocTypes';
			case CATEGORY_TABLE :
				return 'tblCategory';
		}

		return $name;
	}

	function selectFromTempTable($searchstart, $anzahl, $order){
		$sortIsNr = "DESC";
		$sortNr = "";
		$sortierung = explode(" ", $order);
		if(isset($sortierung[1])){
			$sortIsNr = "";
			$sortNr = "DESC";
		}

		$query = "SELECT SEARCH_TEMP_TABLE.*,LOWER(" . $sortierung[0] . ") AS lowtext, abs(" . $sortierung[0] . ") as Nr, (" . $sortierung[0] . " REGEXP '^[0-9]') as isNr  FROM SEARCH_TEMP_TABLE  ORDER BY IsFolder DESC, isNr " . $sortIsNr . ",Nr " . $sortNr . ",lowtext " . $sortNr . ", " . $order . "  limit " . $searchstart . "," . $anzahl . " ";
		$this->db->query($query);
	}

//FIXME path is only implemented for filetable
	function insertInTempTable($where = "", $table = "", $path=""){
		$this->table = (empty($table)) ? ((empty($this->table)) ? "" : $this->table) : $table;

		if(empty($this->table)){
			return;
		}

		$this->where = (empty($where)) ? ((empty($this->where)) ? " WHERE 1 " : " WHERE " . $this->where) : " WHERE " . $where;

		switch($this->table){

			case FILE_TABLE:
				$tmpTableWhere = '';
				if($path){
					$this->where .= ' AND Path LIKE "' . $this->db->escape($path) . '%" ';
					$tmpTableWhere = ' AND DocumentID IN (SELECT ID FROM ' . FILE_TABLE . ' WHERE Path LIKE "' . $this->db->escape($path) . '%" )';
				}
				$query = "INSERT INTO  SEARCH_TEMP_TABLE SELECT '',ID,'" . FILE_TABLE . "',Text,Path,ParentID,IsFolder,temp_template_id,TemplateID,ContentType,'',CreationDate,CreatorID,ModDate,Published,Extension,'','' FROM `" . FILE_TABLE . "` " . $this->where . " ";
				$this->db->query($query);

				$titles = array();
				//first check published documents
				$query = "SELECT a.Name, b.Dat, a.DID FROM `" . LINK_TABLE . "` a LEFT JOIN `" . CONTENT_TABLE . "` b on (a.CID = b.ID) WHERE a.Name='Title' AND NOT a.DocumentTable='" . TEMPLATES_TABLE . "'";
				$this->db->query($query);
				while($this->db->next_record()) {
					$titles[$this->db->f('DID')] = $this->db->f('Dat');
				}
				//check unpublished documents
				$query2 = 'SELECT DocumentID, DocumentObject  FROM `' . TEMPORARY_DOC_TABLE . '` WHERE DocTable = "tblFile" AND Active = 1 ' . $tmpTableWhere;
				$this->db->query($query2);
				while($this->db->next_record()) {
					$tempDoc = unserialize($this->db->f('DocumentObject'));
					if(isset($tempDoc[0]['elements']['Title'])){
						$titles[$this->db->f('DocumentID')] = $tempDoc[0]['elements']['Title']['dat'];
					}
				}
				if(is_array($titles) && !empty($titles)){
					foreach($titles as $k => $v){
						if($v != ""){
							$query3 = "UPDATE SEARCH_TEMP_TABLE  SET `SiteTitle` = '" . $this->db->escape($v) . "' WHERE docID = " . intval($k) . " AND DocTable = '" . FILE_TABLE . "' LIMIT 1 ";
							$this->db->query($query3);
						}
					}
				}
				break;

			case VERSIONS_TABLE:
				if($_SESSION['weSearch']['onlyDocs'] || $_SESSION['weSearch']['ObjectsAndDocs']){
					$query = "INSERT INTO  SEARCH_TEMP_TABLE SELECT ''," . VERSIONS_TABLE . ".documentID," . VERSIONS_TABLE . ".documentTable," . VERSIONS_TABLE . ".Text," . VERSIONS_TABLE . ".Path," . VERSIONS_TABLE . ".ParentID,'',''," . VERSIONS_TABLE . ".TemplateID," . VERSIONS_TABLE . ".ContentType,''," . VERSIONS_TABLE . ".timestamp," . VERSIONS_TABLE . ".modifierID,'',''," . VERSIONS_TABLE . ".Extension," . VERSIONS_TABLE . ".TableID," . VERSIONS_TABLE . ".ID FROM " . VERSIONS_TABLE . " LEFT JOIN " . FILE_TABLE . " ON " . VERSIONS_TABLE . ".documentID = " . FILE_TABLE . ".ID " . $this->where . " " . $_SESSION['weSearch']['onlyDocsRestrUsersWhere'] . " ";
					if(stristr($query, VERSIONS_TABLE . ".status='deleted'")){
						$query = str_replace(FILE_TABLE . ".", VERSIONS_TABLE . ".", $query);
					}
					$this->db->query($query);
				}
				if(defined("OBJECT_FILES_TABLE")){
					if($_SESSION['weSearch']['onlyObjects'] || $_SESSION['weSearch']['ObjectsAndDocs']){
						$query = "INSERT INTO SEARCH_TEMP_TABLE SELECT ''," . VERSIONS_TABLE . ".documentID," . VERSIONS_TABLE . ".documentTable," . VERSIONS_TABLE . ".Text," . VERSIONS_TABLE . ".Path," . VERSIONS_TABLE . ".ParentID,'',''," . VERSIONS_TABLE . ".TemplateID," . VERSIONS_TABLE . ".ContentType,''," . VERSIONS_TABLE . ".timestamp," . VERSIONS_TABLE . ".modifierID,'',''," . VERSIONS_TABLE . ".Extension," . VERSIONS_TABLE . ".TableID," . VERSIONS_TABLE . ".ID FROM " . VERSIONS_TABLE . " LEFT JOIN " . OBJECT_FILES_TABLE . " ON " . VERSIONS_TABLE . ".documentID = " . OBJECT_FILES_TABLE . ".ID " . $this->where . " " . $_SESSION['weSearch']['onlyObjectsRestrUsersWhere'] . " ";
						if(stristr($query, VERSIONS_TABLE . ".status='deleted'")){
							$query = str_replace(OBJECT_FILES_TABLE . ".", VERSIONS_TABLE . ".", $query);
						}
						$this->db->query($query);
					}
				}
				unset($_SESSION['weSearch']['onlyObjects']);
				unset($_SESSION['weSearch']['onlyDocs']);
				unset($_SESSION['weSearch']['ObjectsAndDocs']);
				unset($_SESSION['weSearch']['onlyObjectsRestrUsersWhere']);
				unset($_SESSION['weSearch']['onlyDocsRestrUsersWhere']);
				break;

			case TEMPLATES_TABLE:
				$query = "INSERT INTO SEARCH_TEMP_TABLE  SELECT '',ID,'" . TEMPLATES_TABLE . "',Text,Path,ParentID,IsFolder,'','',ContentType,'',CreationDate,CreatorID,ModDate,'',Extension,'','' FROM `" . TEMPLATES_TABLE . "` " . $this->where . "  ";
				$this->db->query($query);
				break;

			case (defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE : -4):
				$query = "INSERT INTO SEARCH_TEMP_TABLE SELECT '',ID,'" . OBJECT_FILES_TABLE . "',Text,Path,ParentID,IsFolder,'','',ContentType,'',CreationDate,CreatorID,ModDate,Published,'',TableID,'' FROM `" . OBJECT_FILES_TABLE . "` " . $this->where . " ";
				$this->db->query($query);
				break;

			case (defined("OBJECT_TABLE") ? OBJECT_TABLE : -5):
				$query = "INSERT INTO SEARCH_TEMP_TABLE SELECT '',ID,'" . OBJECT_TABLE . "',Text,Path,ParentID,IsFolder,'','',ContentType,'',CreationDate,CreatorID,ModDate,'','','','' FROM `" . OBJECT_TABLE . "` " . $this->where . "  ";
				$this->db->query($query);
				break;
		}
	}

	function getTableType(){

		$tableType = "MEMORY";
		if(getMysqlVer() < 4100){
			$tableType = "MYISAM";
		}
		return $tableType;
	}

	function createTempTable(){

		$q = "DROP TABLE IF EXISTS SEARCH_TEMP_TABLE ";
		$this->db->query($q);

		$tableType = searchtoolsearch::getTableType();

		$charset_collation = "";
		if(defined("DB_CHARSET") && DB_CHARSET != "" && defined("DB_COLLATION") && DB_COLLATION != ""){
			$Charset = DB_CHARSET;
			$Collation = DB_COLLATION;
			$charset_collation = " CHARACTER SET " . $Charset . " COLLATE " . $Collation;
		}

		$tempTableTrue = ($this->checkRightTempTable() == "0") ? "TEMPORARY" : "";

		if($this->checkRightDropTable() == "1" && $tempTableTrue == ""){

		} else{

			$q = "
				 CREATE " . $tempTableTrue . " TABLE SEARCH_TEMP_TABLE (
				`ID` BIGINT( 20 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`docID` BIGINT( 20 ) NOT NULL ,
				`docTable` VARCHAR( 32 ) NOT NULL ,
				`Text` VARCHAR( 255 ) NOT NULL ,
				`Path` VARCHAR( 255 ) NOT NULL ,
				`ParentID` BIGINT( 20 ) NOT NULL ,
				`IsFolder` TINYINT( 1 ) NOT NULL ,
				`temp_template_id` INT( 11 ) NOT NULL ,
				`TemplateID` INT( 11 ) NOT NULL ,
				`ContentType` VARCHAR( 32 ) NOT NULL ,
				`SiteTitle` VARCHAR( 255 ) NOT NULL ,
				`CreationDate` INT( 11 ) NOT NULL ,
				`CreatorID` BIGINT( 20 ) NOT NULL ,
				`ModDate` INT( 11 ) NOT NULL ,
				`Published` INT( 11 ) NOT NULL ,
				`Extension` VARCHAR( 16 ) NOT NULL ,
				`TableID` INT( 11 ) NOT NULL,
				`VersionID` BIGINT( 20 ) NOT NULL
				) ENGINE = " . $tableType . $charset_collation . "
			";

			$this->db->query($q);
		}
	}

	function searchfor($searchname, $searchfield, $searchlocation, $tablename){
		$operator = " AND ";
		$this->table = $tablename;
		$sql = "";
		$tableInfo = $GLOBALS['DB_WE']->metadata($this->table);

		$whatParentID = "";

		if($searchfield == "ParentIDDoc" || $searchfield == "ParentIDObj" || $searchfield == "ParentIDTmpl"){
			$whatParentID = $searchfield;
			$searchfield = "ParentID";
		}

		if($searchfield == "ID" || $searchfield == "CreatorID" || $searchfield == "WebUserID"){
			if(!is_numeric($searchname)){
				return " AND 0";
			}
		}

		//filter fields for each table
		for($y = 0; $y < sizeof($tableInfo); $y++){

			if($tablename == VERSIONS_TABLE){

				switch($searchfield){

					case "ID" :
						$tableInfo[$y]["name"] = "documentID";
						$searchfield = "documentID";
						break;
					case "temp_template_id" :
						$searchfield = "TemplateID";
						break;
					case "temp_doc_type" :
						$searchfield = "DocType";
						break;
					case "ModDate" :
						$searchfield = "timestamp";
						break;
				}
			}

			if($searchfield == $tableInfo[$y]["name"]){
				$searchfield = $tablename . "." . $tableInfo[$y]["name"];

				if(isset($searchname) && $searchname != "")
					if(($whatParentID == "ParentIDDoc" && ($this->table == FILE_TABLE || $this->table == VERSIONS_TABLE)) || ($whatParentID == "ParentIDObj" && ($this->table == OBJECT_FILES_TABLE || $this->table == VERSIONS_TABLE)) || ($whatParentID == "ParentIDTmpl" && $this->table == TEMPLATES_TABLE)){
						if($this->table == VERSIONS_TABLE){
							if($whatParentID == "ParentIDDoc"){
								$this->table = FILE_TABLE;
							}
							if(defined("OBJECT_FILES_TABLE") && $whatParentID == "ParentIDObj"){
								$this->table = OBJECT_FILES_TABLE;
							}
						}
						$searchname = path_to_id($searchname, $this->table);
						$searching = " = '" . addslashes($searchname) . "' ";
						$sql .= $this->sqlwhere($searchfield, $searching, $operator);
					} elseif(($searchfield == TEMPLATES_TABLE . ".MasterTemplateID" && $this->table == TEMPLATES_TABLE) || ($searchfield == FILE_TABLE . ".temp_template_id" && $this->table == FILE_TABLE) || ($searchfield == VERSIONS_TABLE . ".TemplateID" && $this->table == VERSIONS_TABLE)){
						$searchname = path_to_id($searchname, TEMPLATES_TABLE);
						$searching = " = '" . addslashes($searchname) . "' ";

						if(($searchfield == "temp_template_id" && $this->table == FILE_TABLE) || ($searchfield == "TemplateID" && $this->table == VERSIONS_TABLE)){
							if($this->table == FILE_TABLE){
								$sql .= $this->sqlwhere(
									$tablename . ".TemplateID", $searching, $operator . "( (Published >= ModDate AND Published !=0 AND ");
								$sql .= $this->sqlwhere(
									$searchfield, $searching, " ) OR (Published < ModDate AND ");
								$sql .= "))";
							} elseif($this->table == VERSIONS_TABLE){
								$sql .= $this->sqlwhere($tablename . ".TemplateID", $searching, $operator . " ");

								$sql .= "";
							}
						} else{
							$sql .= $this->sqlwhere($searchfield, $searching, $operator);
						}
					} elseif($searchfield == "temp_doc_type" && $this->table == FILE_TABLE){
						$searching = " = '" . addslashes($searchname) . "' ";

						$sql .= $this->sqlwhere(
							$tablename . ".DocType", $searching, $operator . "( (Published >= ModDate AND Published !=0 AND ");
						$sql .= $this->sqlwhere($searchfield, $searching, " ) OR (Published < ModDate AND ");
						$sql .= "))";
					} elseif(stristr($searchfield, ".Published") || stristr($searchfield, ".CreationDate") || stristr(
							$searchfield, ".ModDate")){
						if((stristr($searchfield, ".Published") && $this->table == FILE_TABLE || $this->table == OBJECT_FILES_TABLE) || !stristr(
								$searchfield, ".Published")){
							if($this->table == VERSIONS_TABLE && (stristr($searchfield, ".CreationDate") || stristr(
									$searchfield, ".ModDate"))){
								$searchfield = $this->table . ".timestamp";
							}
							$date = explode(".", $searchname);
							$day = $date[0];
							$month = $date[1];
							$year = $date[2];
							$timestampStart = mktime(0, 0, 0, $month, $day, $year);
							$timestampEnd = mktime(23, 59, 59, $month, $day, $year);

							if(isset($searchlocation)){
								switch($searchlocation){
									case "IS" :
										$searching = " BETWEEN " . $timestampStart . " AND " . $timestampEnd . " ";
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
									case "<" :
										$searching = " " . $searchlocation . " '" . $timestampStart . "' ";
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
									case "<=" :
										$searching = " " . $searchlocation . " '" . $timestampEnd . "' ";
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
									case ">" :
										$searching = " " . $searchlocation . " '" . $timestampEnd . "' ";
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
									case ">=" :
										$searching = " " . $searchlocation . " '" . $timestampStart . "' ";
										$sql .= $this->sqlwhere($searchfield, $searching, $operator);
										break;
								}
							}
						}
					} else{
						if(isset($searchlocation)){
							switch($searchlocation){
								case "END" :
									$searching = " LIKE '%" . addslashes($searchname) . "' ";
									$sql .= $this->sqlwhere($searchfield, $searching, $operator);
									break;
								case "START" :
									$searching = " LIKE '" . addslashes($searchname) . "%' ";
									$sql .= $this->sqlwhere($searchfield, $searching, $operator);
									//$sql .= " �".$val["field"]."� LIKE �".$val["search"]."%� ";
									break;
								case "IS" :
									$searching = " = '" . addslashes($searchname) . "' ";
									$sql .= $this->sqlwhere($searchfield, $searching, $operator);
									break;
								case "<" :
								case "<=" :
								case ">" :
								case ">=" :
									$searching = " " . $searchlocation . " '" . addslashes($searchname) . "' ";
									$sql .= $this->sqlwhere($searchfield, $searching, $operator);
									break;
								default :
									$searching = " LIKE '%" . addslashes($searchname) . "%' ";
									$sql .= $this->sqlwhere($searchfield, $searching, $operator);
									break;
							}
						}
					}
			}
		}

		return $sql;
	}

	function ofFolderAndChildsOnly($folderID, $table){

		$_SESSION["weSearch"]["countChilds"] = array();
		$childsOfFolderId = array();
		//fix #2940
		if(is_array($folderID)){
			if(!empty($folderID)){
				foreach($folderID as $k){
					$childsOfFolderId = $this->getChildsOfParentId($k, $table);
					$ids = makeCSVFromArray($childsOfFolderId);
				}
				return ' AND ' . $table . '.ParentID IN (' . $ids . ')';
			}
		} else{
			$childsOfFolderId = $this->getChildsOfParentId($folderID, $table);
			$ids = makeCSVFromArray($childsOfFolderId);

			return ' AND ' . $table . '.ParentID IN (' . $ids . ')';
		}
	}

	function getChildsOfParentId($folderID, $table){
		$DB_WE = new DB_WE();

		$query = "SELECT ID FROM `" . escape_sql_query($table) . "` WHERE ParentID=" . intval($folderID) . " AND IsFolder=1 ";
		$DB_WE->query($query);
		while($DB_WE->next_record()) {
			$_SESSION["weSearch"]["countChilds"][] = $DB_WE->f("ID");
			$this->getChildsOfParentId($DB_WE->f("ID"), $table);
		}

		$_SESSION["weSearch"]["countChilds"][] = $folderID;
		// doppelte Eintr�ge aus array entfernen
		$_SESSION["weSearch"]["countChilds"] = array_values(
			array_unique($_SESSION["weSearch"]["countChilds"]));

		return $_SESSION["weSearch"]["countChilds"];
	}

	function ofFolderOnly($folderID){
		return ' AND ParentID = ' . intval($folderID);
	}

	function checkRightTempTable(){
		$db = new DB_WE();
		$tableType = searchtoolsearch::getTableType();
		$charset_collation = "";
		if(defined("DB_CHARSET") && DB_CHARSET != "" && defined("DB_COLLATION") && DB_COLLATION != ""){
			$Charset = DB_CHARSET;
			$Collation = DB_COLLATION;
			$charset_collation = " CHARACTER SET " . $Charset . " COLLATE " . $Collation;
		}
		$rights = "
			   CREATE TEMPORARY TABLE test_SEARCH_TEMP_TABLE (
				`test` VARCHAR( 1 ) NOT NULL
				) ENGINE=" . $tableType . $charset_collation . "
		";
		$db->query($rights);
		$db->next_record();

		//		if(isset($db->Record[0]) && (stristr(($db->Record[0]),"CREATE TEMPORARY TABLES")!="" || stristr(($db->Record[0]),"ALL PRIVILEGES")!="")) {
		//			return "0";
		//		}
		$return = "0";

		if(stristr($db->Error, "Access denied")){
			$return = "1";
		}

		$q = "DROP TABLE IF EXISTS test_SEARCH_TEMP_TABLE";
		$db->query($q);

		return $return;
	}

	function checkRightDropTable(){
		$db = new DB_WE();

		$charset_collation = "";
		if(defined("DB_CHARSET") && DB_CHARSET != "" && defined("DB_COLLATION") && DB_COLLATION != ""){
			$Charset = DB_CHARSET;
			$Collation = DB_COLLATION;
			$charset_collation = " CHARACTER SET " . $Charset . " COLLATE " . $Collation;
		}

		$tableType = searchtoolsearch::getTableType();


		$rights = "
			   CREATE TABLE IF NOT EXISTS test_SEARCH_TEMP_TABLE (
				`test` VARCHAR( 1 ) NOT NULL
				) ENGINE=" . $tableType . $charset_collation;
		$db->query($rights);
		$db->next_record();

		//		if(isset($db->Record[0]) && (stristr(($db->Record[0]),"CREATE TEMPORARY TABLES")!="" || stristr(($db->Record[0]),"ALL PRIVILEGES")!="")) {
		//			return "0";
		//		}
		$return = "0";

		$db->query('DROP TABLE IF EXISTS test_SEARCH_TEMP_TABLE');

		if(stristr($db->Error, "command denied")){
			$return = "1";
		}

		return $return;
	}

	function getResultCount(){
		return f('SELECT COUNT(1) AS Count FROM SEARCH_TEMP_TABLE','Count',$this->db);
	}

}