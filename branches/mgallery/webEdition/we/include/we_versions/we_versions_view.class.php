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
class we_versions_view{
	public $db;
	public $version;
	public $searchclass;

	/**
	 *  Constructor for class 'weVersionsView'
	 */
	public function __construct(){

		$this->db = new DB_WE();
		$this->version = new we_versions_version();
		$this->searchclass = new we_versions_search();
		$this->searchclass->initData();
	}

	/**
	 * @abstract create javascript-Code for versions-tab
	 * @return string javascript-code
	 */
	public function getJS(){

		//add height of each input row to calculate the scrollContent-height
		$h = 0;
//		$addinputRows = '';
		if($this->searchclass->mode){
			$h += 37;
			/* 		$addinputRows = '
			  for(i=0;i<newID;i++) {
			  scrollheight = scrollheight + 26;
			  }'; */
		}

		return we_html_element::jsElement('
var g_l={
	resetVersions:"' . g_l('versions', '[resetVersions]') . '",
	mark:"' . g_l('versions', '[mark]') . '",
	notMark:"' . g_l('versions', '[notMark]') . '",
	deleteVersions:"' . g_l('versions', '[deleteVersions]') . '",
	notChecked: "' . we_message_reporting::prepareMsgForJS(g_l('versions', '[notChecked]')) . '",
};
var rows = ' . (isset($_REQUEST['searchFields']) ? count($_REQUEST['searchFields']) - 1 : 0) . ';
var transaction="' . $GLOBALS['we_transaction'] . '";
var doc={
	ID:' . intval($GLOBALS['we_doc']->ID) . ',
	Table:"' . $GLOBALS['we_doc']->Table . '",
	ClassName:"' . get_class($GLOBALS['we_doc']) . '",
	Text:"' . $GLOBALS['we_doc']->Text . '",
};

var searchClass={
	scrollHeight:' . $h . ',
	anzahl:' . intval($this->searchclass->anzahl) . ',
	searchFields: "' . str_replace("\n", '\n', addslashes(we_html_tools::htmlSelect('searchFields[__we_new_id__]', $this->searchclass->getFields(), 1, "", false, array('class' => "defaultfont", 'id' => "searchFields[__we_new_id__]", 'onchange' => 'changeit(this.value, __we_new_id__);')))) . '",
	locationFields:"' . str_replace("\n", '\n', addslashes(we_html_tools::htmlSelect('location[__we_new_id__]', we_search_search::getLocation(), 1, "", false, array('class' => "defaultfont", 'disabled' => 'disabled', 'id' => "location[__we_new_id__]")))) . '",
	search:"' . str_replace("\n", '\n', addslashes(we_html_tools::htmlSelect('search[__we_new_id__]', $this->searchclass->getModFields(), 1, "", false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[__we_new_id__]")))) . '",
	trash:\'' . we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(__we_row__)") . '\',
	searchUsers:"' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('search[__we_new_id__]', $this->searchclass->getUsers(), 1, "", false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[__we_new_id__]")))) . '",
	searchStats:"' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('search[__we_new_id__]', $this->searchclass->getStats(), 1, "", false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[__we_new_id__]")))) . '",
};') . we_html_element::jsScript(JS_DIR . 'versions_view.js');
	}

	/**
	 * @abstract create html-Code for filter-selects
	 * @return string html-Code
	 */
	public function getBodyTop(){

		$out = '<table class="default" id="defSearch" width="550" style="margin-left:20px;display:' . ($this->searchclass->mode ? 'none' : 'block') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('versions', '[weSearch]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:switchSearch(1)", false) . '</td>
	<td width="100%"></td>
</tr>
</table>
<table class="default" id="advSearch" width="550" style="margin-left:20px;display:' . ($this->searchclass->mode ? 'block' : 'none') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('versions', '[weSearch]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRDOWN, "javascript:switchSearch(0)", false) . '</td>
	<td width="100%"></td>
</tr>
</table>
<table id="advSearch2" border="0" style="margin-left:20px;display:' . ($this->searchclass->mode ? 'block' : 'none') . ';">
<tbody id="filterTable">
<tr>
	<td>' . we_class::hiddenTrans() . '</td>
</tr>';

		$r = $r2 = $r3 = array();

		if(isset($this->searchclass->search) && is_array($this->searchclass->search)){
			foreach($this->searchclass->search as $k => $v){
				$r[] = $this->searchclass->search[$k];
			}
		}
		if(isset($this->searchclass->searchFields) && is_array($this->searchclass->search)){
			foreach($this->searchclass->searchFields as $k => $v){
				$r2[] = $this->searchclass->searchFields[$k];
			}
		}
		if(isset($_REQUEST['location']) && is_array($_REQUEST['location'])){
			foreach($_REQUEST['searchFields'] as $k => $v){
				$r3[] = we_base_request::_(we_base_request::STRING, 'location', "disabled", $k);
			}
		}

		$this->searchclass->search = $r;
		$this->searchclass->searchFields = $r2;
		$this->searchclass->location = $r3;

		for($i = 0; $i < $this->searchclass->height; $i++){

			$button = we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(" . $i . ");", true, "", "", "", "", false);

			$search = we_html_tools::htmlSelect(
					"search[" . $i . "]", $this->searchclass->getModFields(), 1, (isset($this->searchclass->search) && is_array($this->searchclass->search) && isset($this->searchclass->search[$i]) ? $this->searchclass->search[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => 'search[' . $i . ']'));

			$locationDisabled = "disabled";
			$handle = "";

			if(isset($this->searchclass->searchFields[$i])){

				switch($this->searchclass->searchFields[$i]){
					case "allModsIn":
						$search = we_html_tools::htmlSelect(
								"search[" . $i . "]", $this->searchclass->getModFields(), 1, (isset($this->searchclass->search) && is_array($this->searchclass->search) && isset($this->searchclass->search[$i]) ? $this->searchclass->search[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => 'search[' . $i . ']'));
						break;
					case "modifierID":
						$search = we_html_tools::htmlSelect(
								"search[" . $i . "]", $this->searchclass->getUsers(), 1, (isset($this->searchclass->search) && is_array($this->searchclass->search) && isset($this->searchclass->search[$i]) ? $this->searchclass->search[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => 'search[' . $i . ']'));
						break;
					case "status":
						$search = we_html_tools::htmlSelect(
								"search[" . $i . "]", $this->searchclass->getStats(), 1, (isset($this->searchclass->search) && is_array($this->searchclass->search) && isset($this->searchclass->search[$i]) ? $this->searchclass->search[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => 'search[' . $i . ']'));
						break;
					case "timestamp":
						$locationDisabled = "";
						$handle = "date";
						$search = we_html_tools::getDateSelector("search[" . $i . "]", "_from" . $i, $this->searchclass->search[$i]);
				}
			}

			$out .= '
				<tr id="filterRow_' . $i . '">
					<td>' . we_html_tools::htmlSelect(
					"searchFields[" . $i . "]", $this->searchclass->getFields(), 1, (isset($this->searchclass->searchFields) && is_array($this->searchclass->searchFields) && isset($this->searchclass->searchFields[$i]) ? $this->searchclass->searchFields[$i] : ""), false, array('class' => "defaultfont", 'id' => 'searchFields[' . $i . ']', 'onchange' => 'changeit(this.value, ' . $i . ');')) . '</td>
					<td id="td_location[' . $i . ']">' . we_html_tools::htmlSelect(
					"location[" . $i . "]", we_search_search::getLocation($handle), 1, (isset($this->searchclass->location) && is_array($this->searchclass->location) && isset($this->searchclass->location[$i]) ? $this->searchclass->location[$i] : ""), false, array('class' => "defaultfont", $locationDisabled => $locationDisabled, 'id' => 'location[' . $i . ']')) . '</td>
					<td id="td_search[' . $i . ']">' . $search . '</td>
					<td id="td_delButton[' . $i . ']">' . $button . '</td>
				</tr>
				';
		}

		$out .= '</tbody></table>
<table class="default" id="advSearch3" style="margin:10px 0px 20px 20px;display:' . ($this->searchclass->mode ? 'block' : 'none') . ';">
	<tr>
		<td width="215">' . we_html_button::create_button(we_html_button::ADD, "javascript:newinput();") . '</td>
		<td width="155"></td>
		<td width="188" style="text-align:right">' . we_html_button::create_button(we_html_button::SEARCH, "javascript:search(true);") . '</td>
		<td></td>
	</tr>
	</table>
	<div style="border-top: 1px solid #AFB0AF;clear:both;"></div>' .
			we_html_element::jsElement("calendarSetup(" . $this->searchclass->height . ");");

		return $out;
	}

	/**
	 * @abstract create html-Code for paging on top
	 * @return string html-Code
	 */
	public function getParameterTop($foundItems){
		$anzahl_all = array(
			10 => 10, 25 => 25, 50 => 50, 100 => 100
		);

		$order = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->searchclass->order, 'order');
		$mode = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchclass->mode, 'mode');
		$height = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchclass->height, 'height');
		$_anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchclass->anzahl, 'anzahl');
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', $GLOBALS['we_transaction'], 'we_transaction');
		$Text = we_base_request::_(we_base_request::STRING, 'text', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Text : '');
		$ID = we_base_request::_(we_base_request::INT, 'id', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->ID : 0);
		$Path = we_base_request::_(we_base_request::FILE, 'path', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : '/');


		return we_html_tools::hidden("we_transaction", $we_transaction) .
			we_html_tools::hidden("order", $order) .
			we_html_tools::hidden("mode", $mode) .
			we_html_tools::hidden("height", $height) .
			'<table class="default" style="margin-top:20px;margin-bottom:12px;">
<tr id="beschreibung_print" class="defaultfont">
	<td>
	<strong>' . g_l('versions', '[versions]') . ':</strong><br/>
	<br/><strong>' . g_l('versions', '[Text]') . ':</strong> ' . $Text . '
	<br/><strong>' . g_l('versions', '[documentID]') . ':</strong> ' . $ID . '
	<br/><strong>' . g_l('versions', '[path]') . ':</strong> ' . $Path . '
	</td>
</tr>
 <tr>
	<td>' . we_html_tools::getPixel(19, 12) . '</td>
	<td id="eintraege_pro_seite" style="font-size:12px;width:130px;">' . g_l('versions', '[eintraege_pro_seite]') . ':</td>
	<td class="defaultgray" style="width:70px;">' .
			we_html_tools::htmlSelect('anzahl', $anzahl_all, 1, $_anzahl, "", array('id' => "anzahl", 'onchange' => 'this.form.elements.searchstart.value=0;search(false);')) . '
	</td>
	<td class="defaultfont" id="eintraege">' . g_l('versions', '[eintraege]') . '</td>
	<td>' . $this->getNextPrev($foundItems) . '</td>
	<td id="print" class="defaultfont">' . we_html_tools::getPixel(10, 12) . '<a href="javascript:printScreen();">' . g_l('versions', '[printPage]') . '</a></td>
</tr>
</table>';
	}

	/**
	 * @abstract create html-Code for paging on bottom
	 * @return string html-Code
	 */
	public function getParameterBottom($foundItems){
		return '<table class="default" style="margin-top:20px;">
<tr id="paging_bottom">
 <td>' . we_html_tools::getPixel(19, 12) . '</td>
 <td style="font-size:12px;width:130px;">' . we_html_tools::getPixel(30, 12) . '</td>
 <td class="defaultgray" style="width:70px;">' . we_html_tools::getPixel(30, 12) . '</td>
 <td style="width:370px;" id="bottom">' . $this->getNextPrev($foundItems) . '</td>
</tr>
</table>';
	}

	/**
	 * @abstract generates html for 'previous' / 'next'
	 * @return string html
	 */
	private function getNextPrev($we_search_anzahl){

		if(isset($GLOBALS['we_cmd_obj'])){
			$anzahl = $_SESSION['weS']['versions']['anzahl'];
			$searchstart = $_SESSION['weS']['versions']['searchstart'];
		} else {
			$anzahl = $this->searchclass->anzahl;
			$searchstart = $this->searchclass->searchstart;
		}

		$out = '<table class="default"><tr><td id="zurueck">' .
			($searchstart ?
				we_html_button::create_button(we_html_button::BACK, "javascript:back(" . $anzahl . ");") :
				we_html_button::create_button(we_html_button::BACK, "", true, 100, 22, "", "", true)) .
			'</td><td>' . we_html_tools::getPixel(10, 2) . '</td>
				<td class="defaultfont"><b>' . (($we_search_anzahl) ? $searchstart + 1 : 0) . '-' .
			(($we_search_anzahl - $searchstart) < $anzahl ?
				$we_search_anzahl :
				$searchstart + $anzahl) .
			' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl . '</b></td><td>' . we_html_tools::getPixel(10, 2) . '</td><td id="weiter">' .
			(($searchstart + $anzahl) < $we_search_anzahl ?
				we_html_button::create_button(we_html_button::NEXT, "javascript:next(" . $anzahl . ");") : //bt_back
				we_html_button::create_button(we_html_button::NEXT, "", true, 100, 22, "", "", true)) .
			'</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>';

		$pages = array();
		for($i = 0; $i < ceil($we_search_anzahl / $anzahl); $i++){
			$pages[($i * $anzahl)] = ($i + 1);
		}

		$page = ceil($searchstart / $anzahl) * $anzahl;

		$select = we_html_tools::htmlSelect('page', $pages, 1, $page, false, array('id' => 'pageselect', 'onchange' => 'this.form.elements.searchstart.value=this.value;search(false);'));

		if(!isset($GLOBALS['setInputSearchstart'])){
			if(!defined('searchstart')){
				define('searchstart', true);
				$out .= we_html_tools::hidden("searchstart", $searchstart);
			}
		}

		$out .= $select .
			'</td></tr></table>';

		return $out;
	}

	/**
	 * @abstract generates content for versions found
	 * @return array with content
	 */
	public function getVersionsOfDoc(){

		$id = we_base_request::_(we_base_request::INT, 'id', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->ID : 0);
		$table = we_base_request::_(we_base_request::TABLE, 'table', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Table : FILE_TABLE);
		$_order = we_base_request::_(we_base_request::RAW, 'we_cmd', $this->searchclass->order, 'order');

		$content = array();
		$modificationText = '';

		$where = $this->searchclass->getWhere();
		$_versions = $this->version->loadVersionsOfId($id, $table, $where);
		$resultCount = count($_versions);
		$_SESSION['weS']['versions']['foundItems'] = $resultCount;

		if($resultCount > 0){
			$sortierung = explode(' ', $_order);

			foreach($_versions as $v){
				$_Result[] = $v;
			}

			if($sortierung[0] === "modifierID"){
				$desc = isset($sortierung[1]);

				usort($_Result, function($a, $b) use ($desc){
					return $desc ?
						strnatcasecmp($b['modifierID'], $a['modifierID']) :
						strnatcasecmp($a['modifierID'], $b['modifierID']);
				});
			} else {
				$sortText = $sortierung[0];
				$sortHow = SORT_ASC;
				$sort1 = array();
				if(isset($sortierung[1])){
					$sortHow = SORT_DESC;
				}
				foreach($_Result as $key => $row){
					$sort1[$key] = strtolower($row[$sortText]);
				}

				array_multisort($sort1, $sortHow, $_Result);
			}

			$_versions = $_Result;
		}

		for($f = 0; $f < $resultCount; $f++){

			$modificationText = $this->getTextForMod($_versions[$f]["modifications"], $_versions[$f]["status"]);
			$user = $_versions[$f]["modifierID"] ? id_to_path($_versions[$f]["modifierID"], USER_TABLE, $this->db) : g_l('versions', '[unknown]');
			$vers = $_versions[$f]["version"];
			$disabledReset = ($_versions[$f]["active"] == 1) ? true : false;
			if(!permissionhandler::hasPerm("ADMINISTRATOR") && !permissionhandler::hasPerm("RESET_VERSIONS")){
				$disabledReset = true;
			}
			$fromScheduler = ($_versions[$f]["fromScheduler"]) ? g_l('versions', '[fromScheduler]') : "";
			$fromImport = ($_versions[$f]["fromImport"]) ? g_l('versions', '[fromImport]') : "";
			$resetFromVersion = ($_versions[$f]["resetFromVersion"]) ? "--" . g_l('versions', '[resetFromVersion]') . $_versions[$f]["resetFromVersion"] . "--" : "";

			$content[] = array(
				array("dat" => '<nobr>' . $vers . '</nobr>'),
				array("dat" => '<nobr>' . we_base_util::shortenPath($user, 30) . '</nobr>'),
				array("dat" => '<nobr>' . ($_versions[$f]["timestamp"] ? date("d.m.y - H:i:s", $_versions[$f]["timestamp"]) : "-") . ' </nobr>'),
				array("dat" => (($modificationText != '') ? $modificationText : '') .
					($fromScheduler ? : '') .
					($fromImport ? : '') .
					($resetFromVersion ? : '')),
				array("dat" => (permissionhandler::hasPerm("ADMINISTRATOR")) ? we_html_forms::checkbox($_versions[$f]["ID"], 0, "deleteVersion", "", false, "defaultfont", "") : ""),
				array("dat" => "<span class='printShow'>" . we_html_button::create_button("reset", "javascript:resetVersion('" . $_versions[$f]["ID"] . "','" . $_versions[$f]["documentID"] . "','" . $_versions[$f]["version"] . "','" . $_versions[$f]["documentTable"] . "');", true, 100, 22, "", "", $disabledReset) . "</span>"),
				array("dat" => "<span class='printShow'>" . we_html_button::create_button(we_html_button::PREVIEW, "javascript:previewVersion('" . $_versions[$f]["ID"] . "');") . "</span>"),
				array("dat" => "<span class='printShow'>" .
					(($_versions[$f]["ContentType"] == we_base_ContentTypes::WEDOCUMENT || $_versions[$f]["ContentType"] == we_base_ContentTypes::HTML || $_versions[$f]["ContentType"] === we_base_ContentTypes::OBJECT_FILE) ?
						we_html_forms::checkbox($_versions[$f]["ID"], 0, "publishVersion_" . $_versions[$f]["ID"], g_l('versions', '[publishIfReset]'), false, "middlefont", "") :
						'') .
					'</span>'),
			);
		}

		return $content;
	}

	/**
	 * @abstract generates headline-titles for columns
	 * @return array with headlines
	 */
	public function makeHeadLines(){
		return array(
			array("dat" => '<a href="javascript:setOrder(\'version\');">' . g_l('versions', '[version]') . '</a> <span id="version" >' . $this->getSortImage('version') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'modifierID\');">' . g_l('versions', '[user]') . '</a> <span id="modifierID" >' . $this->getSortImage('modifierID') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'timestamp\');">' . g_l('versions', '[modTime]') . '</a> <span id="timestamp" >' . $this->getSortImage('timestamp') . '</span>'),
			array("dat" => g_l('versions', '[modifications]')),
			array("dat" => (permissionhandler::hasPerm("ADMINISTRATOR") ? '<div style="margin:0px 0px 5px 0px;" id="deleteButton">' . we_html_button::create_button(
						we_html_button::TRASH, "javascript:deleteVers();") . '</div>' : '') .
				we_html_forms::checkbox(1, 0, "deleteAllVersions", g_l('versions', '[mark]'), false, "middlefont", "checkAll();")),
			array("dat" => ''),
			array("dat" => ''),
			array("dat" => ''),
			array("dat" => ''),
		);
	}

	/**
	 * @abstract generate html list for modifications
	 * @return string
	 */
	private function getTextForMod($modString, $status){
		$statusTxt = ($status === "published" ? "<div style='color:#ff0000;'>" . g_l('versions', '[' . $status . ']') . "</div>" : '');

		if($modString == ""){
			return $statusTxt;
		}

		$out = '<div>';

		$modifications = makeArrayFromCSV($modString);
		$m = 0;
		foreach($modifications as $v){
			foreach($this->version->modFields as $key => $val){
				if($v == $val){
					$out .= "<strong>- " . g_l('versions', '[' . $key . ']') . '</strong><br/>';
				}
			}
			$m++;
		}

		$out .= "<span style='color:#ff0000;'>" . $statusTxt . '</span>
			</div>';

		return $out;
	}

	/**
	 * @abstract generate html for SortImage
	 * @return string
	 */
	private function getSortImage($for){
		$order = we_base_request::_(we_base_request::STRING, 'order', $this->searchclass->order);

		if(strpos($order, $for) === 0){
			if(strpos($order, 'DESC')){
				return '<i class="fa fa-sort-desc fa-lg"></i>';
			}
			return '<i class="fa fa-sort-asc fa-lg"></i>';
		}

		return '<i class="fa fa-sort fa-lg"></i>';
	}

	/**
	 * @abstract generate html for version list
	 * @return string
	 */
	public function tblList($content, $headline){
		//$anz = count($headline) - 1;
		return '
<table border="0" style="background-color:#fff;" width="100%" cellpadding="5">
<tr>
	<td style="vertical-align:top;width:15px;border-bottom:1px solid #AFB0AF;"></td>
	<td style="vertical-align:top;width:110px;border-bottom:1px solid #AFB0AF;" class="middlefont">' . $headline[0]["dat"] . '</td>
	<td style="vertical-align:top;width:15em;border-bottom:1px solid #AFB0AF;" class="middlefont">' . $headline[1]["dat"] . '</td>
	<td style="vertical-align:top;width:120px;border-bottom:1px solid #AFB0AF;" class="middlefont">' . $headline[2]["dat"] . '</td>
	<td style="vertical-align:top;width:120px;border-bottom:1px solid #AFB0AF;" class="middlefont">' . $headline[4]["dat"] . '</td>
	<td style="vertical-align:top;width:auto;border-bottom:1px solid #AFB0AF;" class="middlefont">' . $headline[3]["dat"] . '</td>
</tr>
</table>
<div id="scrollContent" style="background-color:#fff;width:100%">' .
			$this->tabListContent($this->searchclass->searchstart, $this->searchclass->anzahl, $content) .
			'</div>';
	}

	function tabListContent($searchstart, $anzahl, $content){
		$out = '<table cellpadding="5" width="100%" id="contentTable">';

		$anz = count($content);
		$x = $searchstart + $anzahl;

		if($x > $anz){
			$x = $x - ($x - $anz);
		}
		for($m = $searchstart; $m < $x; $m++){
			$out .= '<tr>' . self::tblListRow($content[$m]) . '</tr>';
		}

		$out .= '</tbody></table>';

		return $out;
	}

	private static function tblListRow($content){
		//$anz = count($content) - 1;
		return '<td style="vertical-align:top;width:15px;"></td>
<td style="vertical-align:top;width:110px;height:30px;" class="middlefont">' . ((!empty($content[0]["dat"])) ? $content[0]["dat"] : "&nbsp;") . '</td>
<td style="vertical-align:top;width:15em;" class="middlefont">' . ((!empty($content[1]["dat"])) ? $content[1]["dat"] : "&nbsp;") . '</td>
<td style="vertical-align:top;width:120px;" class="middlefont">' . ((!empty($content[2]["dat"])) ? $content[2]["dat"] : "&nbsp;") . '</td>
<td style="vertical-align:top;width:120px;" class="middlefont">' . ((!empty($content[4]["dat"])) ? $content[4]["dat"] : "&nbsp;") . '</td>
<td rowspan="2" style="vertical-align:top;line-height:20px;width:auto;border-bottom:1px solid #D1D1D1;" class="middlefont">' . ((!empty($content[3]["dat"])) ? $content[3]["dat"] : "&nbsp;") . '</td>
</tr>
<tr>
<td style="width:15px;border-bottom:1px solid #D1D1D1;"></td>
<td colspan="2" style="vertical-align:top;width:220px;border-bottom:1px solid #D1D1D1;" class="middlefont">' . ((!empty($content[5]["dat"]) ) ? $content[5]["dat"] : "&nbsp;") . ((!empty($content[7]["dat"])) ? $content[7]["dat"] : "&nbsp;") . '</td>
<td style="vertical-align:top;width:120px;border-bottom:1px solid #D1D1D1;" class="middlefont">' . ((!empty($content[6]["dat"])) ? $content[6]["dat"] : "&nbsp;") . '</td>
<td style="vertical-align:top;width:120px;border-bottom:1px solid #D1D1D1;" class="middlefont"></td>
<td style="vertical-align:top;width:auto;border-bottom:1px solid #D1D1D1;" class="middlefont"></td>';
	}

	public function getHTMLforVersions($content){
		$uniqname = md5(uniqid(__FUNCTION__, true));

		$out = '<table width="100%" class="default">
				<tr>
				<td class="defaultfont">';

		foreach($content as $i => $c){

			$mainContent = (!empty($c["html"])) ? $c["html"] : "";

			$rightContent = '<div class="defaultfont">' . $mainContent . '</div>';

			$out .= '<div style="margin-left:0px" id="div_' . $uniqname . '_' . $i . '">' .
				$rightContent .
				'</div>';
		}

		$out .= '</td></tr></table>';

		return $out;
	}

}
