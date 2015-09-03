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
abstract class doclistView{

	/**
	 * @abstract create javascript for document list
	 * @return javascript code
	 */
	public static function getSearchJS(){
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0);

		return we_html_element::jsElement('
var ajaxURL = "' . WEBEDITION_DIR . 'rpc/rpc.php";
var rows = ' . (isset($_REQUEST["searchFields"]) ? count($_REQUEST["searchFields"]) - 1 : 0) . ';
var docID="' . $GLOBALS['we_doc']->ID . '";
var transaction="' . $we_transaction . '";
var dirs={
	"IMAGE_DIR":"' . IMAGE_DIR . '",
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

var searchFields = "' . addslashes(we_html_tools::htmlTextInput('search[__we_new_id__]', 24, "", "", " class=\"wetextinput\" id=\"search[__we_new_id__]\" ", "text", 190)) . '";
var trashButton=\'' . we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(__we_new_id__)") . '\';
var searchClassFolder = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('search[__we_new_id__]', $GLOBALS['we_doc']->searchclassFolder->getFieldsStatus(), 1, "", false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[__we_new_id__]")))) . '";

var searchSpeicherat = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('search[__we_new_id__]', $GLOBALS['we_doc']->searchclassFolder->getFieldsSpeicherart(), 1, "", false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[__we_new_id__]")))) . '";

') .
			we_html_element::jsScript(JS_DIR . 'doclistView.js');
	}

	/**
	 * @abstract create search dialog-box
	 * @return html for search dialog box
	 */
	public static function getSearchDialog(){
		$out = '<table class="default" id="defSearch" width="550" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'none' : 'block') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:switchSearch(1)", false) . '</td>
</tr>
</table>
<table class="default" id="advSearch" width="550" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'block' : 'none') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRDOWN, "javascript:switchSearch(0)", false) . '</td>
</tr>
</table>' .
			we_class::hiddenTrans() .
			'<table class="default"  id="advSearch2" border="0" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'block' : 'none') . ';">
<tbody id="filterTable">';

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
			$button = we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(" . $i . ");", true, "", "", "", "", false);

			$handle = "";

			$searchInput = we_html_tools::htmlTextInput("search[" . $i . "]", 30, (isset($GLOBALS['we_doc']->searchclassFolder->search) && is_array($GLOBALS['we_doc']->searchclassFolder->search) && isset($GLOBALS['we_doc']->searchclassFolder->search[$i]) ? $GLOBALS['we_doc']->searchclassFolder->search[$i] : ''), "", " class=\"wetextinput\"  id=\"search[" . $i . "]\" ", "text", 190);

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
					$_cmd = "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . TEMPLATES_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("document.we_form.elements['search[" . $i . "]'].value") . "','','','" . $_rootDirID . "','','" . we_base_ContentTypes::TEMPLATE . "')";
					$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
					$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search[' . $i . ']', 58, $_linkPath, '', 'readonly ', 'text', 190, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('searchParentID[' . $i . ']', ""), $_button);

					$searchInput = $selector;
				}
				if($GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "temp_category"){
					$_linkPath = $GLOBALS['we_doc']->searchclassFolder->search [$i];

					$_rootDirID = 0;

					$_cmd = "javascript:we_cmd('we_selector_category',document.we_form.elements['searchParentID[" . $i . "]'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'searchParentID[" . $i . "]\\'].value','document.we_form.elements[\\'search[" . $i . "]\\'].value','','','" . $_rootDirID . "','','')";
					$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
					$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 190, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('searchParentID[' . $i . ']', ""), $_button);

					$searchInput = $selector;
				}
			}

			$out .= '
<tr id="filterRow_' . $i . '">
	<td>' . we_html_tools::hidden("hidden_searchFields[" . $i . "]", isset($GLOBALS['we_doc']->searchclassFolder->searchFields[$i]) ? $GLOBALS['we_doc']->searchclassFolder->searchFields[$i] : "" ) . we_html_tools::htmlSelect("searchFields[" . $i . "]", $GLOBALS['we_doc']->searchclassFolder->getFields($i, "doclist"), 1, (isset($GLOBALS['we_doc']->searchclassFolder->searchFields) && is_array($GLOBALS['we_doc']->searchclassFolder->searchFields) && isset($GLOBALS['we_doc']->searchclassFolder->searchFields [$i]) ? $GLOBALS['we_doc']->searchclassFolder->searchFields [$i] : ""), false, array('class' => "defaultfont", 'id' => "searchFields[' . $i . ']", 'onchange' => 'changeit(this.value, ' . $i . ');')) . '</td>
	<td id="td_location[' . $i . ']">' . we_html_tools::htmlSelect("location[" . $i . "]", we_search_search::getLocation($handle), 1, (isset($GLOBALS['we_doc']->searchclassFolder->location) && is_array($GLOBALS['we_doc']->searchclassFolder->location) && isset($GLOBALS['we_doc']->searchclassFolder->location [$i]) ? $GLOBALS['we_doc']->searchclassFolder->location [$i] : ""), false, array('class' => "defaultfont", $locationDisabled => $locationDisabled, 'id' => 'location[' . $i . ']')) . '</td>
	<td id="td_search[' . $i . ']">' . $searchInput . '</td>
	<td id="td_delButton[' . $i . ']">' . $button . '</td>
</tr>
        ';
		}

		$out .= '</tbody></table>
<table class="default" id="advSearch3" style="margin-left:20px;margin-top:10px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'block' : 'none') . ';">
	<tr>
		<td width="215">' . we_html_button::create_button(we_html_button::ADD, "javascript:newinput();") . '</td>
		<td width="155"></td>
		<td width="188" style="text-align:right">' . we_html_button::create_button(we_html_button::SEARCH, "javascript:search(true);") . '</td>
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
	public static function searchProperties($table = FILE_TABLE){

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

		$searchFields = we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->searchFields, 'searchFields');
		$searchText = array_map('trim', we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->search, 'search'));
		$location = we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->location, 'location');
		$_order = we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->order, 'order');
		$_view = we_base_request::_(we_base_request::INT, 'we_cmd', $obj->searchclassFolder->setView, 'setView');
		$_searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', $obj->searchclassFolder->searchstart, 'searchstart');
		$_anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', $obj->searchclassFolder->anzahl, 'anzahl');

		$where = '';
		$op = ' AND ';
		$obj->searchclassFolder->settable($table);


		if(!we_search_search::checkRightTempTable() && !we_search_search::checkRightDropTable()){
			echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('searchtool', '[noTempTableRightsDoclist]'), we_message_reporting::WE_MESSAGE_NOTICE));
			return '';
		}
		if($obj->ID){
			$obj->searchclassFolder->createTempTable();

			foreach($searchFields as $i => $searchField){

				$w = "";
				if(isset($searchText[0])){
					$searchString = (isset($searchText[$i]) ? $searchText[$i] : $searchText[0]);
				}
				if(!empty($searchString)){

					switch($searchField){
						default:
						case "Text":
							if(isset($searchField) && isset($location[$i])){
								$where .= $obj->searchclassFolder->searchfor($searchString, $searchField, $location[$i], $table);
							}
						case "Content":
						case "Status":
						case "Speicherart":
						case "CreatorName":
						case "WebUserName":
						case "temp_category":
							break;
					}

					switch($searchField){
						case "Content":
							$w = $obj->searchclassFolder->searchContent($searchString, $table);
							if(!$where){
								$where .= " AND " . ($w ? $w : '0');
							} elseif($w != ""){
								$where .= $op . " " . $w;
							}
							break;

						case 'Title':
							$w = $obj->searchclassFolder->searchInTitle($searchString, $table);
							if(!$where){
								$where = ' AND ' . ($w ? $w : '0');
							} elseif($w != ''){
								$where .= $op . ' ' . $w;
							}
							break;
						case "Status":
						case "Speicherart":
							if($searchString != ""){
								if($table == FILE_TABLE){
									$w = $obj->searchclassFolder->getStatusFiles($searchString, $table);
									$where .= $w;
								}
							}
							break;
						case "CreatorName":
						case "WebUserName":
							if($searchString != ""){
								$w = $obj->searchclassFolder->searchSpecial($searchString, $table, $searchField, $location[$i]);
								$where .= $w;
							}
							break;
						case "temp_category":
							$w = $obj->searchclassFolder->searchCategory($searchString, $table, $searchField);
							$where .= $w;
							break;
					}
				}
			}

			$where .= ' AND ParentID = ' . intval($obj->ID);

			$whereQuery = '1 ' . $where;
			switch($table){
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
			$obj->searchclassFolder->insertInTempTable($whereQuery, $table, $obj->Path . '/');

			$foundItems = $obj->searchclassFolder->countitems($whereQuery, $table);
			$_SESSION['weS']['weSearch']['foundItems'] = $foundItems;

			$obj->searchclassFolder->selectFromTempTable($_searchstart, $_anzahl, $_order);

			while($obj->searchclassFolder->next_record()){
				if(!isset($saveArrayIds[$obj->searchclassFolder->Record ['ContentType']][$obj->searchclassFolder->Record ['ID']])){
					$saveArrayIds[$obj->searchclassFolder->Record ['ContentType']][$obj->searchclassFolder->Record ['ID']] = $obj->searchclassFolder->Record ['ID'];
					$_result[] = array_merge(array('Table' => $table), $obj->searchclassFolder->Record);
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
				$_result[$k]["Description"] = f('SELECT c.Dat FROM (' . FILE_TABLE . ' a LEFT JOIN ' . LINK_TABLE . ' b ON (a.ID=b.DID)) LEFT JOIN ' . CONTENT_TABLE . ' c ON (b.CID=c.ID) WHERE a.ID=' . intval($_result[$k]["ID"]) . ' AND b.Name="Description" AND b.DocumentTable="' . FILE_TABLE . '"', '', $DB_WE);
			} else {
				if(($obj = f('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($_result[$k]["ID"]) . ' AND DocTable="tblFile" AND Active=1', '', $DB_WE))){
					$tempDoc = we_unserialize($obj);
					if(isset($tempDoc[0]['elements']['Description']) && $tempDoc[0]['elements']['Description']['dat']){
						$_result[$k]['Description'] = $tempDoc[0]['elements']['Description']['dat'];
					}
				}
			}
		}

		return self::makeContent($DB_WE, $_result, $_view);
	}

	public static function makeHeadLines($table){
		return array(
			array("dat" => '<a href="javascript:setOrder(\'Text\');">' . g_l('searchtool', '[dateiname]') . '</a> <span id="Text" >' . self::getSortImage('Text') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'SiteTitle\');">' . ($table == TEMPLATES_TABLE ? g_l('weClass', '[path]') : g_l('searchtool', '[seitentitel]') ) . '</a> <span id="SiteTitle" >' . self::getSortImage('SiteTitle') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'CreationDate\');">' . g_l('searchtool', '[created]') . '</a> <span id="CreationDate" >' . self::getSortImage('CreationDate') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'ModDate\');">' . g_l('searchtool', '[modified]') . '</a> <span id="ModDate" >' . self::getSortImage('ModDate') . '</span>'),
		);
	}

	private static function getSortImage($for){
		$order = we_base_request::_(we_base_request::STRING, 'order', $GLOBALS['we_doc']->searchclassFolder->order);

		if(strpos($order, $for) === 0){
			if(strpos($order, 'DESC')){
				return '<i class="fa fa-sort-desc fa-lg"></i>';
			}
			return '<i class="fa fa-sort-asc fa-lg"></i>';
		}
		return '<i class="fa fa-sort fa-lg"></i>';
	}

	private function makeContent(we_database_base $DB_WE, $_result, $view){

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


			if($view == 0){
				switch($showPubCheckbox ? '-1' : $_result[$f]["ContentType"]){
					case we_base_ContentTypes::WEDOCUMENT:
					case we_base_ContentTypes::HTML:
					case we_base_ContentTypes::OBJECT_FILE:
						if(permissionhandler::hasPerm('PUBLISH')){
							$publishCheckbox = we_html_forms::checkbox($_result[$f]["docID"] . "_" . $_result[$f]["docTable"], 0, "publish_docs_doclist", "", false, "middlefont", "");
							break;
						}
					default:
						$publishCheckbox = $showPubCheckbox ? '' : '<span style="width:20px"/>';
				}

				$content[$f] = array(
					array('dat' => $publishCheckbox),
					array('dat' => we_html_element::jsElement('document.write(getTreeIcon("' . $_result[$f]["ContentType"] . '"))')),
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]['docTable'] . '\',\'' . $_result[$f]['docID'] . '\',\'' . $_result[$f]['ContentType'] . '\')" class="' . $fontColor . ' middlefont" title="' . $_result[$f]['Text'] . '"><u>' . we_base_util::shortenPath($_result[$f]['Text'], $we_PathLength)),
					//array("dat" => '<nobr>' . g_l('contentTypes', '[' . $_result[$f]['ContentType'] . ']') . '</nobr>'),
					array("dat" => '<nobr>' . we_base_util::shortenPath($_result[$f]["SiteTitle"], $we_PathLength) . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-") . '</nobr>')
				);
			} else {
				$fs = file_exists($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) : 0;

				if($_result[$f]["ContentType"] == we_base_ContentTypes::IMAGE){
					$smallSize = 64;
					$bigSize = 140;

					if($fs){
						$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]);
						$imageView = '<img src="' . WEBEDITION_DIR . 'thumbnail.php?id=' . $_result[$f]["docID"] . "&size=" . $smallSize . "&path=" . urlencode($_result[$f]["Path"] . "&extension=" . $_result[$f]['Extension']
							) . "' border='0' /></a>";

						$imageViewPopup = '<img src="' . WEBEDITION_DIR . 'thumbnail.php?id=' . $_result[$f]['docID'] . '&size=' . $bigSize . '&path=' . urlencode($_result[$f]['Path']) . '&extension=' . $_result[$f]['Extension'] . '" border="0" /></a>';
					} else {
						$imagesize = array(0, 0);
						$imageView = $imageViewPopup = we_html_element::jsElement('document.write(getTreeIcon("' . we_base_ContentTypes::IMAGE . '"))');
					}
				} else {
					$imagesize = array(0, 0);
					$imageView = $imageViewPopup = we_html_element::jsElement('document.write(getTreeIcon("' . $_result[$f]['ContentType'] . '",false,"' . $_result[$f]['Extension'] . '"))');
				}

				$creator = $_result[$f]['CreatorID'] ? id_to_path($_result[$f]['CreatorID'], USER_TABLE, $DB_WE) : g_l('searchtool', '[nobody]');

				if($_result[$f]['ContentType'] == we_base_ContentTypes::WEDOCUMENT){
					$templateID = ($_result[$f]["Published"] >= $_result[$f]["ModDate"] && $_result[$f]["Published"] ?
							$_result[$f]['TemplateID'] :
							$_result[$f]['temp_template_id']);

					$templateText = g_l('searchtool', '[no_template]');
					if($templateID){
						$DB_WE->query('SELECT ID, Text FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($templateID));
						while($DB_WE->next_record()){
							$templateText = we_base_util::shortenPath($DB_WE->f('Text'), 20) . ' (ID=' . $DB_WE->f('ID') . ')';
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
							$metafields[$_tagName] = we_base_util::shortenPath($DB_WE->f('Dat'), 45);
						}
					}
				}

				$content[$f] = array(
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $imageView . '</a>'),
					array("dat" => we_base_util::shortenPath($_result[$f]["SiteTitle"], 17)),
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]["Text"] . '"><u>' . we_base_util::shortenPath($_result[$f]["Text"], 17) . '</u></a>'),
					array("dat" => '<nobr>' . ($_result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-") . '</nobr>'),
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none;" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $imageViewPopup . '</a>'),
					array("dat" => we_base_file::getHumanFileSize($fs)),
					array("dat" => $imagesize[0] . " x " . $imagesize[1]),
					array("dat" => we_base_util::shortenPath(g_l('contentTypes', '[' . ($_result[$f]['ContentType']) . ']'), 22)),
					array("dat" => '<span class="' . $fontColor . '">' . we_base_util::shortenPath($_result[$f]["Text"], 30) . '</span>'),
					array("dat" => we_base_util::shortenPath($_result[$f]["SiteTitle"], 45)),
					array("dat" => we_base_util::shortenPath($_result[$f]["Description"], 100)),
					array("dat" => $_result[$f]['ContentType']),
					array("dat" => we_base_util::shortenPath($creator, 22)),
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
	public static function getSearchParameterTop($foundItems){
		$anzahl = array(10 => 10, 25 => 25, 50 => 50, 100 => 100);

		$order = we_base_request::_(we_base_request::STRING, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->order : '', 'order');
		$mode = we_base_request::_(we_base_request::BOOL, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->mode : '', 'mode');
		$setView = we_base_request::_(we_base_request::STRING, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->setView : we_search_view::VIEW_LIST, 'setView');
		$_anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->anzahl : '', 'anzahl');
		$id = we_base_request::_(we_base_request::INT, 'id', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->ID : '');
		$table = we_base_request::_(we_base_request::TABLE, 'table', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Table : '');
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', (isset($GLOBALS['we_transaction']) ? $GLOBALS['we_transaction'] : 0));

		return
			we_html_tools::hidden("we_transaction", $we_transaction) .
			we_html_tools::hidden("order", $order) .
			we_html_tools::hidden("todo", "") .
			we_html_tools::hidden("mode", $mode) .
			we_html_tools::hidden("setView", $setView) .
			'<table class="default" style="margin:12px 0px 12px 19px;">
	<tr>
		<td style="font-size:12px;width:125px;">' . g_l('searchtool', '[eintraege_pro_seite]') . ':</td>
		<td class="defaultgray" style="width:60px;">' . we_html_tools::htmlSelect("anzahl", $anzahl, 1, $_anzahl, "", array('onchange' => 'this.form.elements.searchstart.value=0;search(false);')) . '</td>
		<td>' . self::getNextPrev($foundItems) . '</td>
		<td>' . we_html_button::create_button("fa:iconview,fa-lg fa-th", "javascript:setview('" . we_search_view::VIEW_ICONS . "');", true, 40, "", "", "", false) . '</td>
		<td>' . we_html_button::create_button("fa:listview,fa-lg fa-align-justify", "javascript:setview('" . we_search_view::VIEW_LIST . "');", true, 40, "", "", "", false) . '</td>' .
			($id && $table === FILE_TABLE ? we_html_baseElement::getHtmlCode(new we_html_baseElement('td', true, array('style' => 'width:50px;'), we_fileupload_importFiles::getBtnImportFiles($id))) : '') .
			'<td style="width:50px;">' . we_html_button::create_button("fa:btn_new_dir,fa-plus,fa-lg fa-folder", "javascript:top.we_cmd('new_document','" . FILE_TABLE . "','','" . we_base_ContentTypes::FOLDER . "','','" . $id . "')", true, 50, "", "", "", false) . '</td>
	</tr>
</table>';
	}

	public static function getSearchParameterBottom($table, $foundItems){
		switch($table){
			case TEMPLATES_TABLE:
				$publishButton = $publishButtonCheckboxAll = "";
				break;
			default:
				if(permissionhandler::hasPerm('PUBLISH')){
					$publishButtonCheckboxAll = we_html_forms::checkbox(1, 0, "publish_all", "", false, "middlefont", "checkAllPubChecks()");
					$publishButton = we_html_button::create_button(we_html_button::PUBLISH, "javascript:publishDocs();", true, 100, 22, "", "");
				} else {
					$publishButton = $publishButtonCheckboxAll = "";
				}
		}

		return
			'<table class="default" style="margin-top:20px;">
	<tr>
	 <td>' . $publishButtonCheckboxAll . '</td>
	 <td style="font-size:12px;width:125px;">' . $publishButton . '</td>
	 <td class="defaultgray" style="width:60px;height:30px;" id="resetBusy"></td>
	 <td style="width:370px;">' . self::getNextPrev($foundItems, false) . '</td>
	</tr>
</table>';
	}

	/**
	 * @abstract generates html for paging GUI
	 * @return string, html for paging GUI
	 */
	private static function getNextPrev($we_search_anzahl, $isTop = true){
		if(($obj = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'obj'))){
			$anzahl = $_SESSION['weS']['weSearch']['anzahl'];
			$searchstart = $_SESSION['weS']['weSearch']['searchstart'];
		} else {
			$obj = $GLOBALS['we_doc'];
			$anzahl = $obj->searchclassFolder->anzahl;
			$searchstart = $obj->searchclassFolder->searchstart;
		}

		$out = '<table class="default"><tr><td>' .
			($searchstart ?
				we_html_button::create_button(we_html_button::BACK, "javascript:back(" . $anzahl . ");") :
				we_html_button::create_button(we_html_button::BACK, "", true, 100, 22, "", "", true)
			) .
			'</td><td class="defaultfont"><b>' . (($we_search_anzahl) ? $searchstart + 1 : 0) . '-' .
			(($we_search_anzahl - $searchstart) < $anzahl ? $we_search_anzahl : $searchstart + $anzahl) .
			' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl . '</b></td><td>' .
			(($searchstart + $anzahl) < $we_search_anzahl ?
				we_html_button::create_button(we_html_button::NEXT, "javascript:next(" . $anzahl . ");") :
				we_html_button::create_button(we_html_button::NEXT, "", true, 100, 22, "", "", true)
			) .
			'</td><td>';

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
	public static function getHTMLforDoclist($content){
		$out = '<table class="default" style="width:100%;">
<tr><td class="defaultfont">';

		foreach($content as $i => $c){
			$out .= '<div style="margin-left:0px" class="defaultfont">' . (!empty($c["html"]) ? $c["html"] : "") . '</div>';

			if($i < (count($content) - 1)){
				$out .= '<div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;"></div>';
			}
		}

		return $out . '</td></tr></table>';
	}

}
