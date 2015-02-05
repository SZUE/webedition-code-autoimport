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
 * @abstract class making the view for the document list
 */
class doclistView{

	/**
	 * @abstract create javascript for document list
	 * @return javascript code
	 */
	function getSearchJS(){
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0);

		return we_html_element::jsElement('
var ajaxURL = "' . WEBEDITION_DIR . 'rpc/rpc.php";
var rows = ' . (isset($_REQUEST["searchFields"]) ? count($_REQUEST["searchFields"]) - 1 : 0) . ';
var docID="' . $GLOBALS['we_doc']->ID . '";
var transaction="' . $we_transaction . '";
var dirs={
	"IMAGE_DIR":"' . IMAGE_DIR . '",
	"BUTTONS_DIR":"' . BUTTONS_DIR . '"
};
var tables={
	"CATEGORY_TABLE":"' . CATEGORY_TABLE . '",
	"TEMPLATES_TABLE":"' . TEMPLATES_TABLE . '"
};
var searchclassFolderMode=' . intval($GLOBALS['we_doc']->searchclassFolder->mode) . ';
var g_l={
	"publish_docs":"' . g_l('searchtool', '[publish_docs]') . '",
	"notChecked": "' . we_message_reporting::prepareMsgForJS(g_l('searchtool', '[notChecked]')) . '",
	"publishOK":"' . we_message_reporting::prepareMsgForJS(g_l('searchtool', '[publishOK]')) . '",
	"noTempTableRightsDoclist":"' . we_message_reporting::prepareMsgForJS(g_l('searchtool', '[noTempTableRightsDoclist]')) . '",
	"select_value":"' . g_l('button', '[select][value]') . '"
};
var canNotMakeTemp=' . intval(!we_search_search::checkRightTempTable() && !we_search_search::checkRightDropTable()) . ';
var searchFields = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('searchFields[__we_new_id__]', $GLOBALS['we_doc']->searchclassFolder->getFields("__we_new_id__", "doclist"), 1, "", false, array('class' => "defaultfont", 'id' => "searchFields[__we_new_id__]", 'onchange' => "changeit(this.value, __we_new_id__);")))) . '";
var locationFields = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('location[__we_new_id__]', we_search_search::getLocation(), 1, "", false, array('class' => "defaultfont", 'id' => "location[__we_new_id__]")))) . '";
var locationDateFields = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('location[__we_new_id__]', we_search_search::getLocation("date"), 1, "", false, array('class' => "defaultfont", 'id' => "location[__we_new_id__]")))) . '";

var search = "' . addslashes(we_html_tools::htmlTextInput('search[__we_new_id__]', 24, "", "", " class=\"wetextinput\" id=\"search[__we_new_id__]\" ", "text", 190)) . '";
var trashButton=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow(__we_new_id__)") . '\';
var searchClassFolder = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('search[__we_new_id__]', $GLOBALS['we_doc']->searchclassFolder->getFieldsStatus(), 1, "", false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[__we_new_id__]")))) . '";

var searchSpeicherat = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('search[__we_new_id__]', $GLOBALS['we_doc']->searchclassFolder->getFieldsSpeicherart(), 1, "", false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[__we_new_id__]")))) . '";

') .
				we_html_element::jsScript(JS_DIR . 'doclistView.js');
	}

	/**
	 * @abstract create search dialog-box
	 * @return html for search dialog box
	 */
	function getSearchDialog(){
		$out = '<table cellpadding="0" cellspacing="0" id="defSearch" border="0" width="550" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'none' : 'block') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td>' . we_html_tools::getPixel(40, 2) . '' . we_html_button::create_button("image:btn_direction_right", "javascript:switchSearch(1)", false) . '</td>
	<td width="100%">' . we_html_tools::getPixel(10, 2) . '</td>
</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" id="advSearch" width="550" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'block' : 'none') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td>' . we_html_tools::getPixel(40, 2) . '' . we_html_button::create_button("image:btn_direction_down", "javascript:switchSearch(0)", false) . '</td>
	<td width="100%">' . we_html_tools::getPixel(10, 2) . '</td>
</tr>
</table>
<table cellpadding="2" cellspacing="0"  id="advSearch2" border="0" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'block' : 'none') . ';">
<tbody id="filterTable">
<tr>
	<td>' . we_class::hiddenTrans() . '</td>
</tr>';

		$r = $r2 = $r3 = array();
		if(isset($GLOBALS['we_doc']->searchclassFolder->search) && is_array($GLOBALS['we_doc']->searchclassFolder->search)){
			foreach($GLOBALS['we_doc']->searchclassFolder->search as $k => $v){
				$r[] = $GLOBALS['we_doc']->searchclassFolder->search [$k];
			}
		}
		if(isset($GLOBALS['we_doc']->searchclassFolder->searchFields) && is_array($GLOBALS['we_doc']->searchclassFolder->search)){
			foreach($GLOBALS['we_doc']->searchclassFolder->searchFields as $k => $v){
				$r2[] = $GLOBALS['we_doc']->searchclassFolder->searchFields [$k];
			}
		}
		if(($loc = we_base_request::_(we_base_request::STRING, 'location'))){
			foreach($_REQUEST['searchFields'] as $k => $v){
				$r3[] = (isset($loc[$k]) ? $loc[$k] : "disabled");
			}
		}

		$GLOBALS['we_doc']->searchclassFolder->search = $r;
		$GLOBALS['we_doc']->searchclassFolder->searchFields = $r2;
		$GLOBALS['we_doc']->searchclassFolder->location = $r3;

		for($i = 0; $i < $GLOBALS['we_doc']->searchclassFolder->height; $i++){
			$button = we_html_button::create_button("image:btn_function_trash", "javascript:delRow(" . $i . ");", true, "", "", "", "", false);

			$handle = "";

			$searchInput = we_html_tools::htmlTextInput("search[" . $i . "]", 30, (isset($GLOBALS['we_doc']->searchclassFolder->search) && is_array($GLOBALS['we_doc']->searchclassFolder->search) && isset($GLOBALS['we_doc']->searchclassFolder->search[$i]) ? $GLOBALS['we_doc']->searchclassFolder->search[$i] : ''), "", " class=\"wetextinput\"  id=\"search['.$i.']\" ", "text", 190);

			switch(isset($GLOBALS['we_doc']->searchclassFolder->searchFields[$i]) ? $GLOBALS['we_doc']->searchclassFolder->searchFields[$i] : ''){
				case "Content":
				case "Status":
				case "Speicherart":
				case "temp_template_id":
				case "temp_category":
					$locationDisabled = 'disabled';
					break;
				default:
					$locationDisabled = '';
			}

			if(isset($GLOBALS['we_doc']->searchclassFolder->searchFields[$i])){
				if($GLOBALS['we_doc']->searchclassFolder->searchFields[$i] === "Status"){
					$searchInput = we_html_tools::htmlSelect("search[" . $i . "]", $GLOBALS['we_doc']->searchclassFolder->getFieldsStatus(), 1, (isset($GLOBALS['we_doc']->searchclassFolder->search) && is_array($GLOBALS['we_doc']->searchclassFolder->search) && isset($GLOBALS['we_doc']->searchclassFolder->search [$i]) ? $GLOBALS['we_doc']->searchclassFolder->search [$i] : ""), false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[' . $i . ']"));
				}
				if($GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "Speicherart"){
					$searchInput = we_html_tools::htmlSelect("search[" . $i . "]", $GLOBALS['we_doc']->searchclassFolder->getFieldsSpeicherart(), 1, (isset($GLOBALS['we_doc']->searchclassFolder->search) && is_array($GLOBALS['we_doc']->searchclassFolder->search) && isset($GLOBALS['we_doc']->searchclassFolder->search [$i]) ? $GLOBALS['we_doc']->searchclassFolder->search [$i] : ""), false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[' . $i . ']"));
				}
				if($GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "Published" || $GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "CreationDate" || $GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "ModDate"){
					$handle = "date";
					$searchInput = we_html_tools::getDateSelector("search[" . $i . "]", "_from" . $i, $GLOBALS['we_doc']->searchclassFolder->search [$i]);
				}

				if($GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "MasterTemplateID" || $GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "temp_template_id"){
					$_linkPath = $GLOBALS['we_doc']->searchclassFolder->search [$i];
					$_rootDirID = 0;

					$cmd1 = "document.we_form.elements['searchParentID[" . $i . "]'].value";
					$_cmd = "javascript:we_cmd('openDocselector'," . $cmd1 . ",'" . TEMPLATES_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("document.we_form.elements['search[" . $i . "]'].value") . "','','','" . $_rootDirID . "','','" . we_base_ContentTypes::TEMPLATE . "')";
					$_button = we_html_button::create_button('select', $_cmd, true, 70, 22, '', '', false);
					$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search[' . $i . ']', 58, $_linkPath, '', 'readonly ', 'text', 190, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden(array('name' => 'searchParentID[' . $i . ']', "value" => "")), we_html_tools::getPixel(5, 4), $_button);

					$searchInput = $selector;
				}
				if($GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "temp_category"){
					$_linkPath = $GLOBALS['we_doc']->searchclassFolder->search [$i];

					$_rootDirID = 0;

					$_cmd = "javascript:we_cmd('openCatselector',document.we_form.elements['searchParentID[" . $i . "]'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'searchParentID[" . $i . "]\\'].value','document.we_form.elements[\\'search[" . $i . "]\\'].value','','','" . $_rootDirID . "','','')";
					$_button = we_html_button::create_button('select', $_cmd, true, 70, 22, '', '', false);
					$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 190, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden(array('name' => 'searchParentID[' . $i . ']', "value" => "")), we_html_tools::getPixel(5, 4), $_button);

					$searchInput = $selector;
				}
			}

			$out .= '
        <tr id="filterRow_' . $i . '">
          <td>' . we_html_tools::hidden("hidden_searchFields[" . $i . "]", isset($GLOBALS['we_doc']->searchclassFolder->searchFields[$i]) ? $GLOBALS['we_doc']->searchclassFolder->searchFields[$i] : "" ) . '' . we_html_tools::htmlSelect("searchFields[" . $i . "]", $GLOBALS['we_doc']->searchclassFolder->getFields($i, "doclist"), 1, (isset($GLOBALS['we_doc']->searchclassFolder->searchFields) && is_array($GLOBALS['we_doc']->searchclassFolder->searchFields) && isset($GLOBALS['we_doc']->searchclassFolder->searchFields [$i]) ? $GLOBALS['we_doc']->searchclassFolder->searchFields [$i] : ""), false, array('class' => "defaultfont", 'id' => "searchFields[' . $i . ']", 'onchange' => "changeit(this.value, ' . $i . ');")) . '</td>
          <td id="td_location[' . $i . ']">' . we_html_tools::htmlSelect("location[" . $i . "]", we_search_search::getLocation($handle), 1, (isset($GLOBALS['we_doc']->searchclassFolder->location) && is_array($GLOBALS['we_doc']->searchclassFolder->location) && isset($GLOBALS['we_doc']->searchclassFolder->location [$i]) ? $GLOBALS['we_doc']->searchclassFolder->location [$i] : ""), false, array('class' => "defaultfont", $locationDisabled => $locationDisabled, 'id' => "location[' . $i . ']")) . '</td>
          <td id="td_search[' . $i . ']">' . $searchInput . '</td>
          <td id="td_delButton[' . $i . ']">' . $button . '</td>
        </tr>
        ';
		}

		$out .= '</tbody></table>
<table cellpadding="0" cellspacing="0" id="advSearch3" border="0" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'block' : 'none') . ';">
	<tr>
		<td colspan="4">' . we_html_tools::getPixel(20, 10) . '</td>
	</tr>
	<tr>
		<td width="215">' . we_html_button::create_button("add", "javascript:newinput();") . '</td>
		<td width="155"></td>
		<td width="188" align="right">' . we_html_button::create_button("search", "javascript:search(true);") . '</td>
		<td></td>
	</tr>
</table>' .
				we_html_element::jsElement('calendarSetup(' . $GLOBALS['we_doc']->searchclassFolder->height . ');');

		return $out;
	}

	/**
	 * @abstract executes the search and writes the result into arrays
	 * @return array with search results
	 */
	function searchProperties(){

		$DB_WE = new DB_WE();
		$foundItems = 0;
		$content = $_result = $saveArrayIds = $searchText = array();
		$_SESSION['weS']['weSearch']['foundItems'] = 0;

		foreach($_REQUEST['we_cmd'] as $k => $v){
			if(stristr($k, 'searchFields[') && !stristr($k, 'hidden_')){
				$_REQUEST['we_cmd']['searchFields'][] = $v;
			}
			if(stristr($k, 'location[')){
				$_REQUEST['we_cmd']['location'][] = $v;
			}
			if(stristr($k, 'search[')){
				$_REQUEST['we_cmd']['search'][] = $v;
			}
		}

		$obj = (isset($GLOBALS['we_cmd_obj']) && is_object($GLOBALS['we_cmd_obj']) ?
						$GLOBALS['we_cmd_obj'] :
						$GLOBALS['we_doc']);

		$obj->searchclassFolder->searchstart = we_base_request::_(we_base_request::INT, "searchstart", 0);

		$_table = FILE_TABLE;
		$searchFields = we_base_request::_(we_base_request::STRING, 'searchFields', $obj->searchclassFolder->searchFields);
		$searchText = array_map('trim', we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->search, 'search'));
		$location = we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->location, 'location');
		$_order = we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->order, 'order');
		$_view = we_base_request::_(we_base_request::INT, 'we_cmd', $obj->searchclassFolder->setView, 'setView');
		$_searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', $obj->searchclassFolder->searchstart, 'searchstart');
		$_anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', $obj->searchclassFolder->anzahl, 'anzahl');

		$where = '';
		$op = ' AND ';
		$obj->searchclassFolder->settable($_table);


		if(!we_search_search::checkRightTempTable() && !we_search_search::checkRightDropTable()){
			echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('searchtool', '[noTempTableRightsDoclist]'), we_message_reporting::WE_MESSAGE_NOTICE));
			return '';
		}
		if($obj->ID != 0){
			$obj->searchclassFolder->createTempTable();

			for($i = 0; $i < count($searchFields); $i++){

				$w = "";
				if(isset($searchText[0])){
					$searchString = (isset($searchText[$i]) ? $searchText[$i] : $searchText[0]);
				}
				if(isset($searchString) && $searchString){

					switch($searchFields[$i]){
						default:
						case "Text":
							if(isset($searchFields[$i]) && isset($location[$i])){
								$where .= $obj->searchclassFolder->searchfor($searchString, $searchFields[$i], $location[$i], $_table);
							}
						case "Content":
						case "Status":
						case "Speicherart":
						case "CreatorName":
						case "WebUserName":
						case "temp_category":
							break;
					}

					switch($searchFields[$i]){
						case "Content":
							$w = $obj->searchclassFolder->searchContent($searchString, $_table);
							if(!$where){
								$where .= " AND " . ($w ? $w : '0');
							} elseif($w != ""){
								$where .= $op . " " . $w;
							}
							break;

						case 'Title':
							$w = $obj->searchclassFolder->searchInTitle($searchString, $_table);
							if(!$where){
								$where = ' AND ' . ($w ? $w : '0');
							} elseif($w != ''){
								$where .= $op . ' ' . $w;
							}
							break;
						case "Status":
						case "Speicherart":
							if($searchString != ""){
								if($_table == FILE_TABLE){
									$w = $obj->searchclassFolder->getStatusFiles($searchString, $_table);
									$where .= $w;
								}
							}
							break;
						case "CreatorName":
						case "WebUserName":
							if($searchString != ""){
								$w = $obj->searchclassFolder->searchSpecial($searchString, $_table, $searchFields[$i], $location[$i]);
								$where .= $w;
							}
							break;
						case "temp_category":
							$w = $obj->searchclassFolder->searchCategory($searchString, $_table, $searchFields[$i]);
							$where .= $w;
							break;
					}
				}
			}

			$where .= ' AND ParentID = ' . intval($obj->ID);

			$whereQuery = '1 ' . $where;
			switch($_table){
				case FILE_TABLE:
					$whereQuery .= ' AND ((RestrictOwners=0 OR RestrictOwners=' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Owners)))';
					break;
				case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					$whereQuery .= ' AND ((RestrictOwners=0 OR RestrictOwners=' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Owners)))';
					break;
				case (defined('OBJECT_TABLE') ? OBJECT_TABLE : OBJECT_TABLE):
					$whereQuery .= 'AND ((RestrictUsers=0 OR RestrictUsers= ' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Users))) ';
					break;
			}

			$obj->searchclassFolder->setwhere($whereQuery);
			$obj->searchclassFolder->insertInTempTable($whereQuery, $_table, $obj->Path . '/');

			$foundItems = $obj->searchclassFolder->countitems($whereQuery, $_table);
			$_SESSION['weS']['weSearch']['foundItems'] = $foundItems;

			$obj->searchclassFolder->selectFromTempTable($_searchstart, $_anzahl, $_order);

			while($obj->searchclassFolder->next_record()){
				if(!isset($saveArrayIds[$obj->searchclassFolder->Record ['ContentType']][$obj->searchclassFolder->Record ['ID']])){
					$saveArrayIds[$obj->searchclassFolder->Record ['ContentType']][$obj->searchclassFolder->Record ['ID']] = $obj->searchclassFolder->Record ['ID'];
					$_result[] = array_merge(array('Table' => $_table), $obj->searchclassFolder->Record);
				}
			}
		}

		if($_SESSION['weS']['weSearch']['foundItems'] > 0){
			$_db2 = new DB_WE();
			$_db2->query('DROP TABLE IF EXISTS SEARCH_TEMP_TABLE');

			foreach($_result as $k => $v){
				$_result[$k]["Description"] = "";
				if($_result[$k]["Table"] == FILE_TABLE && $_result[$k]['Published'] >= $_result[$k]['ModDate'] && $_result[$k]['Published'] != 0){
					$DB_WE->query('SELECT a.ID, c.Dat FROM (' . FILE_TABLE . ' a LEFT JOIN ' . LINK_TABLE . ' b ON (a.ID=b.DID)) LEFT JOIN ' . CONTENT_TABLE . ' c ON (b.CID=c.ID) WHERE a.ID=' . intval($_result[$k]["ID"]) . ' AND b.Name="Description" AND b.DocumentTable="' . FILE_TABLE . '"');
					while($DB_WE->next_record()){
						$_result[$k]["Description"] = $DB_WE->f('Dat');
					}
				} else {
					$_db2->query('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($_result[$k]["ID"]) . ' AND DocTable="tblFile" AND Active=1');
					while($_db2->next_record()){
						$tempDoc = unserialize($_db2->f('DocumentObject'));
						if(isset($tempDoc[0]['elements']['Description']) && $tempDoc[0]['elements']['Description']['dat'] != ""){
							$_result[$k]["Description"] = $tempDoc[0]['elements']['Description']['dat'];
						}
					}
				}
			}


			$content = $this->makeContent($_result, $_view);
		}

		return $content;
	}

	function makeHeadLines(){
		return array(
			array("dat" => '<a href="javascript:setOrder(\'Text\');">' . g_l('searchtool', '[dateiname]') . '</a> <span id="Text" >' . $this->getSortImage('Text') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'SiteTitle\');">' . g_l('searchtool', '[seitentitel]') . '</a> <span id="SiteTitle" >' . $this->getSortImage('SiteTitle') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'CreationDate\');">' . g_l('searchtool', '[created]') . '</a> <span id="CreationDate" >' . $this->getSortImage('CreationDate') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'ModDate\');">' . g_l('searchtool', '[modified]') . '</a> <span id="ModDate" >' . $this->getSortImage('ModDate') . '</span>'),
		);
	}

	function getSortImage($for){
		$order = we_base_request::_(we_base_request::RAW, 'order', $GLOBALS['we_doc']->searchclassFolder->order);

		if(strpos($order, $for) === 0){
			if(strpos($order, 'DESC')){
				return '<img border="0" width="11" height="8" src="' . IMAGE_DIR . 'arrow_sort_desc.gif" />';
			}
			return '<img border="0" width="11" height="8" src="' . IMAGE_DIR . 'arrow_sort_asc.gif" />';
		}
		return we_html_tools::getPixel(11, 8);
	}

	function makeContent($_result, $view){
		$DB_WE = new DB_WE();

		$we_PathLength = 30;

		$resultCount = count($_result);
		$content = array();

		for($f = 0; $f < $resultCount; $f++){
			$fontColor = '';
			$showPubCheckbox = true;
			if(isset($_result[$f]["Published"])){
				switch($_result[$f]["ContentType"]){
					case we_base_ContentTypes::HTML:
					case we_base_ContentTypes::WEDOCUMENT:
					case we_base_ContentTypes::OBJECT_FILE:
						$published = ((($_result[$f]["Published"] != 0) && ($_result[$f]["Published"] < $_result[$f]["ModDate"])) ? -1 : $_result[$f]["Published"]);
						if($published == 0){
							$fontColor = 'notpublished';
							$showPubCheckbox = false;
						} elseif($published == -1){
							$fontColor = 'changed';
							$showPubCheckbox = false;
						}
						break;
					default:
						$published = $_result[$f]["Published"];
				}
			} else {
				$published = 1;
			}

			$ext = isset($_result[$f]["Extension"]) ? $_result[$f]["Extension"] : "";
			$Icon = we_base_ContentTypes::inst()->getIcon($_result[$f]["ContentType"], we_base_ContentTypes::FILE_ICON, $ext);

			if($view == 0){
				$publishCheckbox = (!$showPubCheckbox) ? (($_result[$f]["ContentType"] == we_base_ContentTypes::WEDOCUMENT || $_result[$f]["ContentType"] == we_base_ContentTypes::HTML || $_result[$f]["ContentType"] === we_base_ContentTypes::OBJECT_FILE) && permissionhandler::hasPerm('PUBLISH')) ? we_html_forms::checkbox($_result[$f]["docID"] . "_" . $_result[$f]["docTable"], 0, "publish_docs_doclist", "", false, "middlefont", "") : we_html_tools::getPixel(20, 10) : '';

				$content[$f] = array(
					array("dat" => $publishCheckbox),
					array("dat" => '<img src="' . TREE_ICON_DIR . $Icon . '" border="0" width="16" height="18" />'),
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]['docTable'] . '\',\'' . $_result[$f]['docID'] . '\',\'' . $_result[$f]['ContentType'] . '\')" class="' . $fontColor . ' middlefont" title="' . $_result[$f]['Text'] . '"><u>' . we_util_Strings::shortenPath($_result[$f]['Text'], $we_PathLength)),
					array("dat" => '<nobr>' . g_l('contentTypes', '[' . $_result[$f]['ContentType'] . ']') . '</nobr>'),
					array("dat" => '<nobr>' . we_util_Strings::shortenPath($_result[$f]["SiteTitle"], $we_PathLength) . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-") . '</nobr>')
				);
			} else {
				$fs = file_exists($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) : 0;
				$filesize = we_base_file::getHumanFileSize($fs);

				if($_result[$f]["ContentType"] == we_base_ContentTypes::IMAGE){
					$smallSize = 64;
					$bigSize = 140;

					if($fs > 0){
						$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]);
						$imageView = "<img src='" . (file_exists($thumbpath = WE_THUMB_PREVIEW_DIR . $_result[$f]["docID"] . '_' . $smallSize . '_' . $smallSize . strtolower($_result[$f]['Extension'])) ?
										$thumbpath :
										WEBEDITION_DIR . 'thumbnail.php?id=' . $_result[$f]["docID"] . "&size=" . $smallSize . "&path=" . urlencode($_result[$f]["Path"]) . "&extension=" . $_result[$f]["Extension"]
								) . "' border='0' /></a>";

						$imageViewPopup = "<img src='" . (file_exists($thumbpathPopup = WE_THUMB_PREVIEW_DIR . $_result[$f]["docID"] . '_' . $bigSize . '_' . $bigSize . strtolower($_result[$f]["Extension"])) ?
										$thumbpathPopup :
										WEBEDITION_DIR . "thumbnail.php?id=" . $_result[$f]["docID"] . "&size=" . $bigSize . "&path=" . urlencode($_result[$f]["Path"]) . "&extension=" . $_result[$f]["Extension"]
								) . "' border='0' /></a>";
					} else {
						$imagesize = array(0, 0);
						$thumbpath = ICON_DIR . 'doclist/' . we_base_ContentTypes::IMAGE_ICON;
						$imageView = "<img src='" . $thumbpath . "' border='0' />";
						$imageViewPopup = "<img src='" . $thumbpath . "' border='0' />";
					}
				} else {
					$imagesize = array(0, 0);
					$imageView = '<img src="' . ICON_DIR . 'doclist/' . $Icon . '" border="0" width="64" height="64" />';
					$imageViewPopup = '<img src="' . ICON_DIR . 'doclist/' . $Icon . '" border="0" width="64" height="64" />';
				}

				$creator = $_result[$f]["CreatorID"] ? id_to_path($_result[$f]["CreatorID"], USER_TABLE, $DB_WE) : g_l('searchtool', '[nobody]');

				if($_result[$f]["ContentType"] == we_base_ContentTypes::WEDOCUMENT){
					$templateID = ($_result[$f]["Published"] >= $_result[$f]["ModDate"] && $_result[$f]["Published"] ?
									$_result[$f]["TemplateID"] :
									$_result[$f]["temp_template_id"]);

					$templateText = g_l('searchtool', '[no_template]');
					if($templateID){
						$DB_WE->query('SELECT ID, Text FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($templateID));
						while($DB_WE->next_record()){
							$templateText = we_util_Strings::shortenPath($DB_WE->f('Text'), 20) . " (ID=" . $DB_WE->f('ID') . ")";
						}
					}
				} else {
					$templateText = '';
				}

				$_defined_fields = we_metadata_metaData::getDefinedMetaDataFields();
				$metafields = array();
				$_fieldcount = count($_defined_fields);
				if($_fieldcount > 6){
					$_fieldcount = 6;
				}
				for($i = 0; $i < $_fieldcount; $i++){
					$_tagName = $_defined_fields[$i]["tag"];

					if(we_exim_contentProvider::isBinary($_result[$f]["docID"])){
						$DB_WE->query("SELECT a.ID, c.Dat FROM (" . FILE_TABLE . " a LEFT JOIN " . LINK_TABLE . " b ON (a.ID=b.DID)) LEFT JOIN " . CONTENT_TABLE . " c ON (b.CID=c.ID) WHERE b.DID=" . intval($_result[$f]["docID"]) . " AND b.Name='" . $DB_WE->escape($_tagName) . "' AND b.DocumentTable='" . FILE_TABLE . "'");
						$metafields[$_tagName] = "";
						while($DB_WE->next_record()){
							$metafields[$_tagName] = we_util_Strings::shortenPath($DB_WE->f('Dat'), 45);
						}
					}
				}

				$content[$f] = array(
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $imageView . '</a>'),
					array("dat" => we_util_Strings::shortenPath($_result[$f]["SiteTitle"], 17)),
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]["Text"] . '"><u>' . we_util_Strings::shortenPath($_result[$f]["Text"], 17) . '</u></a>'),
					array("dat" => '<nobr>' . ($_result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-") . '</nobr>'),
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none;" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $imageViewPopup . '</a>'),
					array("dat" => $filesize),
					array("dat" => $imagesize[0] . " x " . $imagesize[1]),
					array("dat" => we_util_Strings::shortenPath(g_l('contentTypes', '[' . ($_result[$f]['ContentType']) . ']'), 22)),
					array("dat" => '<span class="' . $fontColor . '">' . we_util_Strings::shortenPath($_result[$f]["Text"], 30) . '</span>'),
					array("dat" => we_util_Strings::shortenPath($_result[$f]["SiteTitle"], 45)),
					array("dat" => we_util_Strings::shortenPath($_result[$f]["Description"], 100)),
					array("dat" => $_result[$f]['ContentType']),
					array("dat" => we_util_Strings::shortenPath($creator, 22)),
					array("dat" => $templateText),
					array("dat" => $metafields),
					array("dat" => $_result[$f]["docID"]),
				);
			}
		}

		return $content;
	}

	/**
	 * @abstract generates html for search result
	 * @return string, html search result
	 */
	function getSearchParameterTop($foundItems){
		$anzahl = array(10 => 10, 25 => 25, 50 => 50, 100 => 100);

		$order = we_base_request::_(we_base_request::STRING, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->order : '', 'order');
		$mode = we_base_request::_(we_base_request::BOOL, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->mode : '', 'mode');
		$setView = we_base_request::_(we_base_request::STRING, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->setView : we_search_view::VIEW_LIST, 'setView');
		$_anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->anzahl : '', 'anzahl');
		$id = we_base_request::_(we_base_request::INT, 'id', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->ID : '');
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', (isset($GLOBALS['we_transaction']) ? $GLOBALS['we_transaction'] : 0));

		return
				we_html_tools::hidden("we_transaction", $we_transaction) .
				we_html_tools::hidden("order", $order) .
				we_html_tools::hidden("todo", "") .
				we_html_tools::hidden("mode", $mode) .
				we_html_tools::hidden("setView", $setView) .
				'<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>' . we_html_tools::getPixel(19, 12) . '</td>
		<td style="font-size:12px;width:125px;">' . g_l('searchtool', '[eintraege_pro_seite]') . ':</td>
		<td class="defaultgray" style="width:60px;">' . we_html_tools::htmlSelect("anzahl", $anzahl, 1, $_anzahl, "", array('onchange' => 'this.form.elements.searchstart.value=0;search(false);')) . '</td>
		<td>' . $this->getNextPrev($foundItems) . '</td>
		<td>' . we_html_tools::getPixel(10, 12) . '</td>
		<td style="width:50px;">' . we_html_button::create_button("image:btn_new_dir", "javascript:top.we_cmd('new_document','" . FILE_TABLE . "','','" . we_base_ContentTypes::FOLDER . "','','" . $id . "')", true, 40, "", "", "", false) . '</td>
		<td>' . we_html_button::create_button("image:iconview", "javascript:setview('" . we_search_view::VIEW_ICONS . "');", true, 40, "", "", "", false) . '</td>
		<td>' . we_html_button::create_button("image:listview", "javascript:setview('" . we_search_view::VIEW_LIST . "');", true, 40, "", "", "", false) . '</td>
	</tr>
	<tr><td colspan="12">' . we_html_tools::getPixel(1, 12) . '</td></tr>
</table>';
	}

	function getSearchParameterBottom($foundItems){
		if(permissionhandler::hasPerm('PUBLISH')){
			$publishButtonCheckboxAll = we_html_forms::checkbox(1, 0, "publish_all", "", false, "middlefont", "checkAllPubChecks()");
			$publishButton = we_html_button::create_button("publish", "javascript:publishDocs();", true, 100, 22, "", "");
		} else {
			$publishButton = $publishButtonCheckboxAll = "";
		}

		return
				'<table border="0" cellpadding="0" cellspacing="0" style="margin-top:20px;">
	<tr>
	 <td>' . $publishButtonCheckboxAll . '</td>
	 <td style="font-size:12px;width:125px;">' . $publishButton . '</td>
	 <td class="defaultgray" style="width:60px;" id="resetBusy">' . we_html_tools::getPixel(30, 12) . '</td>
	 <td style="width:370px;">' . $this->getNextPrev($foundItems, false) . '</td>
	</tr>
</table>';
	}

	/**
	 * @abstract generates html for paging GUI
	 * @return string, html for paging GUI
	 */
	function getNextPrev($we_search_anzahl, $isTop = true){
		if(($obj = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'obj'))){
			$anzahl = $_SESSION['weS']['weSearch']['anzahl'];
			$searchstart = $_SESSION['weS']['weSearch']['searchstart'];
		} else {
			$obj = $GLOBALS['we_doc'];
			$anzahl = $obj->searchclassFolder->anzahl;
			$searchstart = $obj->searchclassFolder->searchstart;
		}

		$out = '<table cellpadding="0" cellspacing="0" border="0"><tr><td>' .
				($searchstart ?
						we_html_button::create_button("back", "javascript:back(" . $anzahl . ");") :
						we_html_button::create_button("back", "", true, 100, 22, "", "", true)
				) .
				'</td><td>' . we_html_tools::getPixel(10, 2) . '</td>
        <td class="defaultfont"><b>' . (($we_search_anzahl) ? $searchstart + 1 : 0) . '-' .
				(($we_search_anzahl - $searchstart) < $anzahl ? $we_search_anzahl : $searchstart + $anzahl) .
				' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl . '</b></td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>' .
				(($searchstart + $anzahl) < $we_search_anzahl ?
						we_html_button::create_button("next", "javascript:next(" . $anzahl . ");") :
						we_html_button::create_button("next", "", true, 100, 22, "", "", true)
				) .
				'</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>';

		$pages = array();
		if($anzahl){
			for($i = 0; $i < ceil($we_search_anzahl / $anzahl); $i++){
				$pages[($i * $anzahl)] = ($i + 1);
			}
		}

		$page = ($anzahl ? ceil($searchstart / $anzahl) * $anzahl : 0);

		$select = we_html_tools::htmlSelect("page", $pages, 1, $page, false, array("onchange" => "this.form.elements.searchstart.value = this.value; search(false);"));

		if(!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'setInputSearchstart') && !defined('searchstart') && $isTop){
			define("searchstart", true);
			$out .= we_html_tools::hidden("searchstart", $searchstart);
		}

		$out .= $select .
				'</td></tr></table>';

		return $out;
	}

	/**
	 * @abstract writes the complete html code
	 * @return string, html
	 */
	function getHTMLforDoclist($content){
		$marginLeft = "0";

		$out = '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%;">
<tr><td class="defaultfont">';

		foreach($content as $i => $c){
			$_forceRightHeadline = (isset($c["forceRightHeadline"]) && $c["forceRightHeadline"]);
			$icon = (isset($c["icon"]) && $c["icon"]) ? ('<img src="' . ICON_DIR . $c["icon"] . '" width="64" height="64" alt="" style="margin-left:20px;" />') : "";
			$headline = (isset($c["headline"]) && $c["headline"]) ? ('<div class="weMultiIconBoxHeadline" style="margin-bottom:10px;">' . $c["headline"] . '</div>') : "";
			$mainContent = (isset($c["html"]) && $c["html"]) ? $c["html"] : "";
			$leftWidth = (isset($c["space"]) && $c["space"]) ? abs($c["space"]) : 0;
			$leftContent = $icon ? : (($leftWidth && (!$_forceRightHeadline)) ? $headline : "");
			$rightContent = '<div class="defaultfont">' . ((($icon && $headline) || ($leftContent === "") || $_forceRightHeadline) ? ($headline . '<div>' . $mainContent . '</div>') : '<div>' . $mainContent . '</div>') . '</div>';

			$out .= '<div style="margin-left:' . $marginLeft . 'px" >';

			if($leftContent || $leftWidth){
				if((!$leftContent) && $leftWidth){
					$leftContent = "&nbsp;";
				}
				$out .= '<div style="float:left;width:' . $leftWidth . 'px">' . $leftContent . '</div>';
			}

			$out .= $rightContent .
					'</div>' . ((we_base_browserDetect::isIE()) ? we_html_element::htmlBr() : '');

			if($i < (count($content) - 1) && (!isset($c["noline"]))){
				$out .= '<div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;"></div>';
			}
		}

		return $out . '</td></tr></table>';
	}

}
