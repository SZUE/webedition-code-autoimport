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
	public $Model;

	/**
	 *  Constructor for class 'weVersionsView'
	 */
	public function __construct($model = null){

		$this->db = new DB_WE();
		$this->Model = $model ?: new we_versions_model();
		$this->version = new we_versions_version();
		$this->searchclass = new we_versions_search($this);
	}

	/**
	 * @abstract create javascript-Code for versions-tab
	 * @return string javascript-code
	 */
	public function getJS(){

		//add height of each input row to calculate the scrollContent-height
		$h = 0;
//		$addinputRows = '';
		if($this->Model->mode){
			$h += 37;
			/* 		$addinputRows = '
			  for(i=0;i<newID;i++) {
			  scrollheight = scrollheight + 26;
			  }'; */
		}

		return we_html_element::jsScript(JS_DIR . 'versions_view.js', '', ['id' => 'loadVarVersionView',
				'data-searchClass' => setDynamicVar([
					'scrollHeight' => $h,
					'anzahl' => intval($this->Model->getProperty('currentAnzahl')),
					'searchFields' => we_html_tools::htmlSelect('searchFields[__we_new_id__]', $this->searchclass->getFields(), 1, "", false, [
						'class' => "defaultfont", 'id' => "searchFields[__we_new_id__]", 'onchange' => 'changeit(this.value, __we_new_id__);']),
					'locationFields' => we_html_tools::htmlSelect('location[__we_new_id__]', we_search_search::getLocation(), 1, "", false, ['class' => "defaultfont",
						'disabled' => 'disabled', 'id' => "location[__we_new_id__]"]),
					'locationFieldsDate' => we_html_tools::htmlSelect('location[__we_new_id__]', we_search_search::getLocation('date'), 1, "", false, [
						'class' => "defaultfont", 'disabled' => 'disabled', 'id' => "location[__we_new_id__]"]),
					'locationFieldsText' => we_html_tools::htmlSelect('location[__we_new_id__]', we_search_search::getLocation('text'), 1, "", false, [
						'class' => "defaultfont", 'disabled' => 'disabled', 'id' => "location[__we_new_id__]"]),
					'search' => we_html_tools::htmlSelect('search[__we_new_id__]', $this->searchclass->getModFields(), 1, "", false, ['class' => "defaultfont",
						'style' => "width:190px;", 'id' => "search[__we_new_id__]"]),
					'trash' => we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(__we_row__)"),
					'searchUsers' => we_html_tools::htmlSelect('search[__we_new_id__]', $this->searchclass->getUsers(), 1, "", false, ['class' => "defaultfont",
						'style' => "width:190px;", 'id' => "search[__we_new_id__]"]),
					'searchStats' => we_html_tools::htmlSelect('search[__we_new_id__]', $this->searchclass->getStats(), 1, "", false, ['class' => "defaultfont",
						'style' => "width:190px;", 'id' => "search[__we_new_id__]"])
					]
				),
				'data-doc' => setDynamicVar([
					'ID' => intval($GLOBALS['we_doc']->ID),
					'Table' => $GLOBALS['we_doc']->Table,
					'ClassName' => get_class($GLOBALS['we_doc']),
					'Text' => $GLOBALS['we_doc']->Text,
				]),
				'data-props' => setDynamicVar([
					'transaction' => $GLOBALS['we_transaction'],
					'rows' => (isset($_REQUEST['searchFields']) ? count($_REQUEST['searchFields']) - 1 : 0)
				])
		]);
	}

	/**
	 * @abstract create html-Code for filter-selects
	 * @return string html-Code
	 */
	public function getBodyTop(){
		$currentSearch = $this->Model->getProperty('currentSearch');
		$currentSearchFields = $this->Model->getProperty('currentSearchFields');
		$currentLocation = $this->Model->getProperty('currentLocation');

		$out = '<table class="default" id="defSearch" style="width:550px;margin-left:20px;display:' . ($this->Model->mode ? 'none' : 'block') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('versions', '[weSearch]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:switchSearch(1)", false) . '</td>
	<td style="width:100%"></td>
</tr>
</table>
<table class="default" id="advSearch" style="width:550px;margin-left:20px;display:' . ($this->Model->mode ? 'block' : 'none') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('versions', '[weSearch]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRDOWN, "javascript:switchSearch(0)", false) . '</td>
	<td style="width:100%"></td>
</tr>
</table>
<table id="advSearch2" style="margin-left:20px;display:' . ($this->Model->mode ? 'block' : 'none') . ';">
<tbody id="filterTable">
<tr>
	<td>' . we_class::hiddenTrans() . '</td>
</tr>';

		for($i = 0; $i < count($currentSearchFields); $i++){

			$button = we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(" . $i . ");", '', "", "", "", "", false);
			$search = we_html_tools::htmlSelect("search[" . $i . "]", $this->searchclass->getModFields(), 1, (isset($currentSearch[$i]) ? $currentSearch[$i] : ""), false, [
					'class' => "defaultfont", 'style' => "width:190px;", 'id' => 'search[' . $i . ']']);
			$locationDisabled = '';
			$handle = '';

			if(isset($currentSearchFields[$i])){
				switch($currentSearchFields[$i]){
					case "allModsIn":
						$search = we_html_tools::htmlSelect("search[" . $i . "]", $this->searchclass->getModFields(), 1, (isset($currentSearch[$i]) ? $currentSearch[$i] : ""), false, [
								'class' => "defaultfont", 'style' => "width:190px;", 'id' => 'search[' . $i . ']']);
						$locationDisabled = 'disabled';
						$currentLocation[$i] = 'IS';
						break;
					case "modifierID":
						$search = we_html_tools::htmlSelect("search[" . $i . "]", $this->searchclass->getUsers(), 1, (isset($currentSearch[$i]) ? $currentSearch[$i] : ""), false, [
								'class' => "defaultfont", 'style' => "width:190px;", 'id' => 'search[' . $i . ']']);
						$locationDisabled = 'disabled';
						$currentLocation[$i] = 'IS';
						break;
					case "status":
						$search = we_html_tools::htmlSelect("search[" . $i . "]", $this->searchclass->getStats(), 1, (isset($currentSearch[$i]) ? $currentSearch[$i] : ""), false, [
								'class' => "defaultfont", 'style' => "width:190px;", 'id' => 'search[' . $i . ']']);
						$locationDisabled = 'disabled';
						$currentLocation[$i] = 'IS';
						break;
					case "timestamp":
						$locationDisabled = "";
						$handle = "date";
						$search = we_html_tools::getDateSelector("search[" . $i . "]", "_from" . $i, (isset($currentSearch[$i]) ? $currentSearch[$i] : ""));
				}
			}

			$out .= '
				<tr id="filterRow_' . $i . '">
					<td>' . we_html_tools::htmlSelect("searchFields[" . $i . "]", $this->searchclass->getFields(), 1, (isset($currentSearchFields[$i]) ? $currentSearchFields[$i] : ""), false, [
					'class' => "defaultfont", 'id' => 'searchFields[' . $i . ']', 'onchange' => 'changeit(this.value, ' . $i . ');']) . '</td>
					<td id="td_location[' . $i . ']">' .
				we_html_tools::htmlSelect("location[" . $i . "]", we_search_search::getLocation($handle), 1, (isset($currentLocation[$i]) ? $currentLocation[$i] : ""), false, [
					'class' => "defaultfont", $locationDisabled => $locationDisabled, 'id' => 'location[' . $i . ']']) . '</td>
					<td id="td_search[' . $i . ']">' . $search . '</td>
					<td id="td_delButton[' . $i . ']">' . $button . '</td>
					<td id="td_hiddenLocation[' . $i . ']">' . (!$locationDisabled ? '' : we_html_element::htmlHidden('location[' . $i . ']', $currentLocation[$i])) . '</td>
				</tr>
				';
		}

		$out .= '</tbody></table>
<table class="default" id="advSearch3" style="margin:10px 0px 20px 20px;display:' . ($this->Model->mode ? 'block' : 'none') . ';">
	<tr>
		<td style="width:215px;">' . we_html_button::create_button(we_html_button::ADD, "javascript:newinput();") . '</td>
		<td style="width:155px"></td>
		<td style="width:188px;text-align:right">' . we_html_button::create_button(we_html_button::SEARCH, "javascript:search(true);") . '</td>
		<td></td>
	</tr>
	</table>
	<div style="border-top: 1px solid #AFB0AF;clear:both;"></div>';

		return $out;
	}

	/**
	 * @abstract create html-Code for paging on top
	 * @return string html-Code
	 */
	public function getParameterTop($foundItems){
		$anzahl_all = [10 => 10, 25 => 25, 50 => 50, 100 => 100];

		$order = $this->Model->getProperty('currentOrder');
		$mode = $this->Model->mode;
		$height = $this->Model->height;
		$anzahl = $this->Model->getProperty('currentAnzahl');
		$we_transaction = $this->Model->transaction;

		// FIXME: move to model or init view
		$Text = we_base_request::_(we_base_request::STRING, 'text', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Text : '');
		$ID = we_base_request::_(we_base_request::INT, 'id', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->ID : 0);
		$Path = we_base_request::_(we_base_request::FILE, 'path', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : '/');


		return we_html_element::htmlHiddens(["we_transaction" => $we_transaction,
				"order" => $order,
				"mode" => $mode,
				"height" => $height
			]) .
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
	<td></td>
	<td id="eintraege_pro_seite" style="font-size:12px;width:130px;">' . g_l('versions', '[eintraege_pro_seite]') . ':</td>
	<td class="defaultfont lowContrast" style="width:70px;">' .
			we_html_tools::htmlSelect('anzahl', $anzahl_all, 1, $anzahl, "", ['id' => "anzahl", 'onchange' => 'this.form.elements.searchstart.value=0;search(false);']) . '
	</td>
	<td class="defaultfont" id="eintraege">' . g_l('versions', '[eintraege]') . '</td>
	<td>' . $this->getNextPrev($foundItems) . '</td>
	<td id="print" class="defaultfont"> <a href="javascript:printScreen();">' . g_l('versions', '[printPage]') . '</a></td>
</tr>
</table>';
	}

	/**
	 * @abstract create html-Code for paging on bottom
	 * @return string html-Code
	 */
	public function getParameterBottom($foundItems){
		return '<table class="default" style="margin-top:20px;margin-left:220px;">
<tr id="paging_bottom">
 <td id="bottom">' . $this->getNextPrev($foundItems) . '</td>
</tr>
</table>';
	}

	/**
	 * @abstract generates html for 'previous' / 'next'
	 * @return string html
	 */
	private function getNextPrev($we_search_anzahl){
		$anzahl = $this->Model->getProperty('currentAnzahl');
		$searchstart = $this->Model->getProperty('currentSearchstart');

		$out = '<table class="default"><tr><td id="zurueck">' .
			($searchstart ?
			we_html_button::create_button(we_html_button::BACK, "javascript:back(" . $anzahl . ");") :
			we_html_button::create_button(we_html_button::BACK, "", '', 0, 0, "", "", true)) .
			'</td><td class="defaultfont"><b>' . (($we_search_anzahl) ? $searchstart + 1 : 0) . '-' .
			(($we_search_anzahl - $searchstart) < $anzahl ?
			$we_search_anzahl :
			$searchstart + $anzahl) .
			' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl . '</b></td><td id="weiter">' .
			(($searchstart + $anzahl) < $we_search_anzahl ?
			we_html_button::create_button(we_html_button::NEXT, "javascript:next(" . $anzahl . ");") : //bt_back
			we_html_button::create_button(we_html_button::NEXT, "", '', 0, 0, "", "", true)) .
			'</td><td>';

		$pages = [];
		for($i = 0; $i < ceil($we_search_anzahl / $anzahl); $i++){
			$pages[($i * $anzahl)] = ($i + 1);
		}

		$page = ceil($searchstart / $anzahl) * $anzahl;

		$select = we_html_tools::htmlSelect('page', $pages, 1, $page, false, ['id' => 'pageselect', 'onchange' => 'this.form.elements.searchstart.value=this.value;search(false);']);

		if(!isset($GLOBALS['setInputSearchstart'])){
			if(!defined('searchstart')){
				define('searchstart', true);
				$out .= we_html_element::htmlHidden("searchstart", $searchstart);
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
		// FIXME: move to model or init view
		$id = we_base_request::_(we_base_request::INT, 'id', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->ID : 0);
		$table = we_base_request::_(we_base_request::TABLE, 'table', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Table : FILE_TABLE);

		$order = $this->Model->getProperty('currentOrder');

		$content = [];
		$modificationText = '';

		$where = $this->searchclass->getWhere($this->Model);

		$versions = we_versions_version::loadVersionsOfId($id, $table, $where);
		$resultCount = count($versions);
		$_SESSION['weS']['versions']['foundItems'] = $resultCount;

		if($resultCount > 0){
			$sortierung = explode(' ', $order);

			foreach($versions as $v){
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
				$sort1 = [];
				$sortHow = (isset($sortierung[1]) ? SORT_DESC : SORT_ASC);
				foreach($_Result as $key => $row){
					$sort1[$key] = strtolower($row[$sortText]);
				}

				array_multisort($sort1, $sortHow, $_Result);
			}

			$versions = $_Result;
		}

		for($f = 0; $f < $resultCount; $f++){

			$modificationText = $this->getTextForMod($versions[$f]["modifications"], $versions[$f]["status"]);
			$user = $versions[$f]['modifierID'] ? id_to_path($versions[$f]["modifierID"], USER_TABLE, $this->db) : g_l('versions', '[unknown]');
			$vers = $versions[$f]['version'];
			$disabledReset = ($versions[$f]['active'] == 1);
			if(!we_base_permission::hasPerm("ADMINISTRATOR") && !we_base_permission::hasPerm("RESET_VERSIONS")){
				$disabledReset = true;
			}
			$fromScheduler = ($versions[$f]["fromScheduler"]) ? g_l('versions', '[fromScheduler]') : "";
			$fromImport = ($versions[$f]["fromImport"]) ? g_l('versions', '[fromImport]') : "";
			$resetFromVersion = ($versions[$f]["resetFromVersion"]) ? "--" . g_l('versions', '[resetFromVersion]') . $versions[$f]['resetFromVersion'] . "--" : "";

			$content[] = [
				['dat' => '<nobr>' . $vers . '</nobr>'],
				['dat' => '<nobr>' . we_base_util::shortenPath($user, 30) . '</nobr>'],
				['dat' => '<nobr>' . ($versions[$f]["timestamp"] ? date("d.m.y - H:i:s", $versions[$f]['timestamp']) : "-") . ' </nobr>'],
				['dat' => (($modificationText != '') ? $modificationText : '') .
					($fromScheduler ?: '') .
					($fromImport ?: '') .
					($resetFromVersion ?: '')],
				['dat' => (we_base_permission::hasPerm('ADMINISTRATOR')) ? we_html_forms::checkbox($versions[$f]['ID'], 0, 'deleteVersion', '', false, 'defaultfont', '') : ''],
				['dat' => "<span class='printShow'>" . we_html_button::create_button('reset', "javascript:resetVersion('" . $versions[$f]["ID"] . "','" . $versions[$f]["documentID"] . "','" . $versions[$f]["version"] . "','" . $versions[$f]["documentTable"] . "');", '', 0, 0, "", "", $disabledReset) . "</span>"],
				['dat' => "<span class='printShow'>" . we_html_button::create_button(we_html_button::PREVIEW, "javascript:previewVersion('" . $table . "'," . $id . "," . $versions[$f]["version"] . ");") . "</span>"],
				['dat' => "<span class='printShow'>" .
					(($versions[$f]["ContentType"] == we_base_ContentTypes::WEDOCUMENT || $versions[$f]["ContentType"] == we_base_ContentTypes::HTML || $versions[$f]["ContentType"] === we_base_ContentTypes::OBJECT_FILE) ?
					we_html_forms::checkbox($versions[$f]["ID"], 0, "publishVersion_" . $versions[$f]["ID"], g_l('versions', '[publishIfReset]'), false, "middlefont", "") :
					'') .
					'</span>'],
			];
		}

		return $content;
	}

	/**
	 * @abstract generates headline-titles for columns
	 * @return array with headlines
	 */
	public function makeHeadLines(){
		return [
			['dat' => '<span onclick="setOrder(\'version\');">' . g_l('versions', '[version]') . ' <span id="version" >' . $this->getSortImage('version') . '</span></span>'],
			['dat' => '<span onclick="setOrder(\'modifierID\');">' . g_l('versions', '[user]') . ' <span id="modifierID" >' . $this->getSortImage('modifierID') . '</span></span>'],
			['dat' => '<span onclick="setOrder(\'timestamp\');">' . g_l('versions', '[modTime]') . '</a> <span id="timestamp" >' . $this->getSortImage('timestamp') . '</span></span>'],
			['dat' => g_l('versions', '[modifications]')],
			['dat' => (we_base_permission::hasPerm('ADMINISTRATOR') ? '<div id="deleteButton">' .
				we_html_button::create_button(we_html_button::TRASH, "javascript:deleteVers();") . '</div>' : '') .
				we_html_forms::checkbox(1, 0, "deleteAllVersions", g_l('versions', '[mark]'), false, "middlefont", "checkAll();")],
			['dat' => ''],
			['dat' => ''],
			['dat' => ''],
			['dat' => ''],
		];
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
		$order = $this->Model->getProperty('currentOrder');

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
<table style="width:100%" cellpadding="5">
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
			$this->tabListContent($content) .
			'</div>';
	}

	function tabListContent($content){
		$searchstart = $this->Model->getProperty('currentSearchstart');
		$anzahl = $this->Model->getProperty('currentAnzahl');

		$out = '<table cellpadding="5" style="width:100%" id="contentTable">';

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

		$out = '<table style="width:100%" class="default">
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
