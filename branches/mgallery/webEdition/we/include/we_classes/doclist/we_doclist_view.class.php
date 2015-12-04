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

class we_doclist_view extends we_search_view{
	private $search;
	public $Model;


	public function __construct($search){
		$this->search = $search;
		$this->Model = $search->getModel();
		
		/*
		$this->docID = $docID;
		$this->Model = new we_search_model();
		$this->searchclass = new we_search_search();
		$this->db = new DB_WE();
		 * 
		 */
	}


	/**
	 * @abstract create javascript for document list
	 * @return javascript code
	 */
	public function getSearchJS(){
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0);

		return we_html_element::jsScript(JS_DIR . 'we_modules/search/search_view.js') . 
				we_html_element::jsElement('
WE().consts.dirs.IMAGE_DIR="' . IMAGE_DIR . '";
weSearch.conf = {
	whichsearch: "' . we_search_view::SEARCH_DOCLIST . '",
	we_transaction: "' . $this->Model->transaction . '",
	editorBodyFrame : window,
	ajaxURL: WE().consts.dirs.WEBEDITION_DIR+"rpc/rpc.php",
	rows: ' . (isset($_REQUEST['searchFields' . we_search_view::SEARCH_DOCLIST]) ? count($_REQUEST['searchFields' . we_search_view::SEARCH_DOCLIST]) - 1 : 0) . ',
	tab: 0,
	modelClassName: "placeholder",
	modelID: "placeholder",
modelIsFolder: true,
	//showSelects: "placeholder",
	rows: 0,
	checkRightTempTable: ' . (we_search_search::checkRightTempTable() ? 1 : 0) . ',
	checkRightDropTable: ' . (we_search_search::checkRightDropTable() ? 1 : 0) . '
};
weSearch.elems = {
	btnTrash: \'' . str_replace("'", "\'", we_html_button::create_button(we_html_button::TRASH, "javascript:weSearch.delRow(__we_new_id__)")) . '\',
	btnSelector: \'' . str_replace("'", "\'", we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('__we_selector__', document.we_form.elements['search" . we_search_view::SEARCH_DOCLIST . "ParentID[__we_new_id__]'].value, '__we_sel_table__', 'document.we_form.elements[\\\'search" . we_search_view::SEARCH_DOCLIST . "ParentID[__we_new_id__]\\\'].value', 'document.we_form.elements[\\\'search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]\\\'].value');")) . '\',
	fieldSearch: \'' . str_replace("'", "\'", we_html_tools::htmlTextInput('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', 58, '', '', ' __we_read_only__class="wetextinput" id="search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]"', 'text', 170)) . '\',
	selStatus: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->search->getFieldsStatus(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selSpeicherart: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->search->getFieldsSpeicherart(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selLocation: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', we_search_search::getLocation(), 1, "", false, array('class' => "defaultfont", 'id' => "location" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selLocationDate: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', we_search_search::getLocation('date'), 1, "", false, array('class' => "defaultfont", 'id' => "location" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selLocationText: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', we_search_search::getLocation('text'), 1, "", false, array('class' => "defaultfont", 'id' => "location" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selModFields: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->search->getModFields(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selUsers: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->search->getUsers(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	searchFields: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('searchFields' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->search->getFields("__we_new_id__", we_search_view::SEARCH_DOCLIST), 1, "", false, array('class' => "defaultfont", 'id' => "searchFields" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]", 'onchange' => "weSearch.changeit(this.value, __we_new_id__);"))) . '\'
};

WE().consts.weSearch= {
	SEARCH_DOCS: "' . we_search_view::SEARCH_DOCS . '",
	SEARCH_TMPL: "' . we_search_view::SEARCH_TMPL . '",
	SEARCH_MEDIA: "' . we_search_view::SEARCH_MEDIA . '",
	SEARCH_ADV: "' . we_search_view::SEARCH_ADV . '",
	SEARCH_DOCLIST: "' . we_search_view::SEARCH_DOCLIST . '"
};
WE().consts.g_l.weSearch = {
	noTempTableRightsSearch: "' . g_l('searchtool', '[noTempTableRightsSearch]') . '",
	nothingCheckedAdv: \'' . g_l('searchtool', '[nothingCheckedAdv]') . '\',
	nothingCheckedTmplDoc: \'' . g_l('searchtool', '[nothingCheckedTmplDoc]') . '\',
	buttonSelectValue: "' . g_l('button', '[select][value]') . '",
	versionsResetAllVersionsOK: "' . g_l('versions', '[resetAllVersionsOK]') . '",
	versionsNotChecked: "' . g_l('versions', '[notChecked]') . '",
	searchtool__notChecked: "' . g_l('searchtool', '[notChecked]') . '",
	searchtool__publishOK: "' . g_l('searchtool', '[publishOK]') . '"
};

');
	}

	/**
	 * @abstract create search dialog-box
	 * @return html for search dialog box
	 */
	public function getSearchDialog(){
		$out = '<table class="default" id="defSearch" width="550" style="margin-left:20px;display:' . ($this->Model->mode ? 'none' : 'block') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:weSearch.switchSearch(1)", false) . '</td>
</tr>
</table>
<table class="default" id="advSearch" width="550" style="margin-left:20px;display:' . ($this->Model->mode ? 'block' : 'none') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRDOWN, "javascript:weSearch.switchSearch(0)", false) . '</td>
</tr>
</table>' .
			we_class::hiddenTrans() .
			'<table class="default"  id="filterTable' . we_search_view::SEARCH_DOCLIST . '" border="0" style="margin-left:20px;display:' . ($this->Model->mode ? 'block' : 'none') . ';">
<tbody id="filterTable' . we_search_view::SEARCH_DOCLIST . '">';
		
		$searchfields = $this->Model->searchFields;
		$location = $this->Model->location;
		$search = $this->Model->search;

		// we always have at least an empty 'Content' search: it's set in model initialisation
		for($i = 0; $i < $this->Model->height; $i++){
			$button = we_html_button::create_button(we_html_button::TRASH, "javascript:weSearch.delRow(" . $i . ");", true, "", "", "", "", false);
			$handle = "";
			$searchInput = we_html_tools::htmlTextInput('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', 30, (isset($this->Model->search) && is_array($this->Model->search) && isset($this->Model->search[$i]) ? $this->Model->search[$i] : ''), "", " class=\"wetextinput\"  id=\"search" . we_search_view::SEARCH_DOCLIST . "[" . $i . "]\" ", "text", 170);

			switch(isset($this->Model->searchFields[$i]) ? $this->Model->searchFields[$i] : ''){
				case C:
				case 'Status':
				case 'Speicherart':
				case 'temp_template_id':
				case 'temp_category':
					$locationDisabled = 'disabled';
			}

			if(isset($this->Model->searchFields[$i])){
				switch($this->Model->searchFields[$i]){
					case 'Status':
						$searchInput = we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', $this->search->getFieldsStatus(), 1, (isset($this->Model->search) && is_array($this->Model->search) && isset($this->Model->search[$i]) ? $this->Model->search[$i] : ""), false, array('class' => 'defaultfont', 'style' => 'width:170px;', 'id' => 'search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']'));
						break;
					case 'Speicherart':
						$searchInput = we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', $this->search->getFieldsSpeicherart(), 1, (isset($this->Model->search) && is_array($this->Model->search) && isset($this->Model->search[$i]) ? $this->Model->search[$i] : ""), false, array('class' => 'defaultfont', 'style' => 'width:170px;', 'id' => 'search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']'));
						break;
					case 'Published':
					case 'CreationDate':
					case 'ModDate':
						$handle = "date";
						$searchInput = we_html_tools::getDateSelector('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', '_from' . $i, $this->Model->search[$i]);
						break;
					case 'MasterTemplateID':
					case 'temp_template_id':
						$_linkPath = $this->Model->search[$i];
						$_rootDirID = 0;

						$cmd1 = "document.we_form.elements['search" . we_search_view::SEARCH_DOCLIST . "ParentID[" . $i . "]'].value";
						$_cmd = "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . TEMPLATES_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("document.we_form.elements['search" . we_search_view::SEARCH_DOCLIST . "[" . $i . "]'].value") . "','','','" . $_rootDirID . "','','" . we_base_ContentTypes::TEMPLATE . "')";
						$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', 58, $_linkPath, '', 'readonly ', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('search' . we_search_view::SEARCH_DOCLIST . 'ParentID[' . $i . ']', ""), $_button);

						$searchInput = $selector;
						break;
					case 'temp_category':
						$_linkPath = $this->Model->search[$i];
						$_rootDirID = 0;

						$_cmd = "javascript:we_cmd('we_selector_category',document.we_form.elements['search" . we_search_view::SEARCH_DOCLIST . "ParentID[" . $i . "]'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'search" . we_search_view::SEARCH_DOCLIST . "ParentID[" . $i . "]\\'].value','document.we_form.elements[\\'search" . we_search_view::SEARCH_DOCLIST . "[" . $i . "]\\'].value','','','" . $_rootDirID . "','','')";
						$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('search' . we_search_view::SEARCH_DOCLIST . 'ParentID[' . $i . ']', ""), $_button);

						$searchInput = $selector;
						break;
				}
			}

			$out .= '
<tr id="filterRow_' . $i . '">
	<td>' . we_html_tools::hidden('hidden_searchFields' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', isset($this->Model->searchFields[$i]) ? $this->Model->searchFields[$i] : "" ) . we_html_tools::htmlSelect('searchFields' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', $this->search->getFields($i, we_search_view::SEARCH_DOCLIST), 1, (isset($this->Model->searchFields) && is_array($this->Model->searchFields) && isset($this->Model->searchFields[$i]) ? $this->Model->searchFields[$i] : ""), false, array('class' => "defaultfont", 'id' => 'searchFields' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', 'onchange' => 'weSearch.changeit(this.value, ' . $i . ');')) . '</td>
	<td id="td_location' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']">' . we_html_tools::htmlSelect('location' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', we_search_search::getLocation($handle), 1, (isset($this->Model->location) && is_array($this->Model->location) && isset($this->Model->location[$i]) ? $this->Model->location[$i] : ""), false, array('class' => "defaultfont", $locationDisabled => $locationDisabled, 'id' => 'location' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']')) . '</td>
	<td id="td_search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']">' . $searchInput . '</td>
	<td id="td_delButton[' . $i . ']">' . $button . '</td>
</tr>
		';
		}

		$out .= '</tbody></table>
<table class="default" id="advSearch3" style="margin-left:20px;margin-top:10px;display:' . ($this->Model->mode ? 'block' : 'none') . ';">
	<tr>
		<td width="215">' . we_html_button::create_button(we_html_button::ADD, "javascript:weSearch.newinput();") . '</td>
		<td width="155"></td>
		<td width="188" style="text-align:right">' . we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);") . '</td>
		<td></td>
	</tr>
</table>' .
			we_html_element::jsElement('weSearch.calendarSetup(' . $this->Model->height . ');');

		return $out;
	}

	public function makeHeadLines($table){
		return array(
			array("dat" => '<a href="javascript:weSearch.setOrder(\'Text\', \'' . we_search_view::SEARCH_DOCLIST . '\');">' . g_l('searchtool', '[dateiname]') . '</a> <span id="Text_' . we_search_view::SEARCH_DOCLIST . '" >' . self::getSortImage('Text') . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'SiteTitle\', \'' . we_search_view::SEARCH_DOCLIST . '\');">' . ($table == TEMPLATES_TABLE ? g_l('weClass', '[path]') : g_l('searchtool', '[seitentitel]') ) . '</a> <span id="SiteTitle_' . we_search_view::SEARCH_DOCLIST . '" >' . self::getSortImage('SiteTitle') . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'CreationDate\', \'' . we_search_view::SEARCH_DOCLIST . '\');">' . g_l('searchtool', '[created]') . '</a> <span id="CreationDate_' . we_search_view::SEARCH_DOCLIST . '" >' . self::getSortImage('CreationDate') . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'ModDate\', \'' . we_search_view::SEARCH_DOCLIST . '\');">' . g_l('searchtool', '[modified]') . '</a> <span id="ModDate_' . we_search_view::SEARCH_DOCLIST . '" >' . self::getSortImage('ModDate') . '</span>'),
		);
	}

	public function getSortImage($for){
		$order = we_base_request::_(we_base_request::STRING, 'order', $this->Model->order);

		if(strpos($order, $for) === 0){
			if(strpos($order, 'DESC')){
				return '<i class="fa fa-sort-desc fa-lg"></i>';
			}
			return '<i class="fa fa-sort-asc fa-lg"></i>';
		}
		return '<i class="fa fa-sort fa-lg"></i>';
	}

	public function makeContent($_result){
		$DB_WE = new DB_WE();
		$view = $this->Model->setView;
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


			if($view === self::VIEW_LIST){
				switch($showPubCheckbox ? '-1' : $_result[$f]["ContentType"]){
					case we_base_ContentTypes::WEDOCUMENT:
					case we_base_ContentTypes::HTML:
					case we_base_ContentTypes::OBJECT_FILE:
						if(permissionhandler::hasPerm('PUBLISH')){
							$publishCheckbox = we_html_forms::checkbox($_result[$f]["docID"] . "_" . $_result[$f]["docTable"], 0, "publish_docs_DoclistSearch", "", false, "middlefont", "");
							break;
						}
					default:
						$publishCheckbox = $showPubCheckbox ? '' : '<span style="width:20px"/>';
				}

				$content[$f] = array(
					array('dat' => $publishCheckbox),
					array('dat' => '<span class="resultIcon" data-contenttype="' . $_result[$f]["ContentType"] . '" data-extension="' . $_result[$f]['Extension'] . '"></span>'),
					// TODO: set thumb ptah when doctype is image/*
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]['docTable'] . '\',\'' . $_result[$f]['docID'] . '\',\'' . $_result[$f]['ContentType'] . '\')" class="' . $fontColor . ' middlefont" title="' . $_result[$f]['Text'] . '"><u>' . we_base_util::shortenPath($_result[$f]['Text'], $we_PathLength)),
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

						$url = WEBEDITION_DIR . 'thumbnail.php?id=' . $_result[$f]["docID"] . "&size=" . $smallSize . "&path=" . urlencode($_result[$f]["Path"]) . "&extension=" . $_result[$f]["Extension"];
						$imageView = '<img src="' . $url . '" border="0" /></a>';
						$urlPopup = WEBEDITION_DIR . "thumbnail.php?id=" . $_result[$f]["docID"] . "&size=" . $bigSize . "&path=" . $_result[$f]["Path"] . "&extension=" . $_result[$f]["Extension"];
						$imageViewPopup = '<img src="' . $urlPopup . '" border="0" /></a>';
						
						//$imageView = '<img src="' . WEBEDITION_DIR . 'thumbnail.php?id=' . $_result[$f]["docID"] . "&size=" . $smallSize . "&path=" . urlencode($_result[$f]["Path"]) . "&extension=" . $_result[$f]['Extension'] . "' border='0' /></a>";
						//$imageViewPopup = '<img src="' . WEBEDITION_DIR . 'thumbnail.php?id=' . $_result[$f]['docID'] . '&size=' . $bigSize . '&path=' . urlencode($_result[$f]['Path']) . '&extension=' . $_result[$f]['Extension'] . '" border="0" /></a>';
					} else {
						$imagesize = array(0, 0);
						$imageView = $imageViewPopup = '<span class="resultIcon" data-contenttype="' . $_result[$f]['ContentType'] . '" data-extension="' . $_result[$f]['Extension'] . '"></span>';
					}
				} else {
					$imagesize = array(0, 0);
					$imageView = $imageViewPopup = '<span class="resultIcon" data-contenttype="' . $_result[$f]['ContentType'] . '" data-extension="' . $_result[$f]['Extension'] . '"></span>';
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
				$_fieldcount = min(6, count($_defined_fields));
				for($i = 0; $i < $_fieldcount; $i++){
					$_tagName = $_defined_fields[$i]["tag"];

					if(we_exim_contentProvider::isBinary($_result[$f]["docID"])){
						$DB_WE->query('SELECT l.DID,c.Dat FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DID=' . intval($_result[$f]['docID']) . ' AND l.nHash=x\'' . md5($_tagName) . '\' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
						$metafields[$_tagName] = "";
						while($DB_WE->next_record()){
							$metafields[$_tagName] = we_base_util::shortenPath($DB_WE->f('Dat'), 45);
						}
					}
				}

				$content[$f] = array(
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $imageView . '</a>'),
					array("dat" => we_base_util::shortenPath($_result[$f]["SiteTitle"], 17)),
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]["Text"] . '"><u>' . we_base_util::shortenPath($_result[$f]["Text"], 17) . '</u></a>'),
					array("dat" => '<nobr>' . ($_result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-") . '</nobr>'),
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none;" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $imageViewPopup . '</a>'),
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
	public function getSearchParameterTop($foundItems){
		$options = array(10 => 10, 25 => 25, 50 => 50, 100 => 100);
		$mode = we_base_request::_(we_base_request::BOOL, 'we_cmd', isset($GLOBALS['we_doc']) ? $this->Model->mode : '', 'mode');

		return
			we_html_tools::hidden('we_transaction', $this->Model->transaction) .
			we_html_tools::hidden('Order' . we_search_view::SEARCH_DOCLIST, $this->Model->order) .
			//we_html_tools::hidden("todo", "") .
			we_html_tools::hidden('mode', $this->Model->mode) .
			we_html_tools::hidden('setView' . we_search_view::SEARCH_DOCLIST, $this->Model->setView) .
			'<table class="default" style="margin:12px 0px 12px 19px;">
	<tr>
		<td style="font-size:12px;width:125px;">' . g_l('searchtool', '[eintraege_pro_seite]') . ':</td>
		<td class="defaultgray" style="width:60px;">' . we_html_tools::htmlSelect("anzahlDoclistSearch", $options, 1, $this->Model->anzahl, "", array('onchange' => "this.form.elements['searchstartDoclistSearch'].value=0;weSearch.search(false);")) . '</td>
		<td>' . self::getNextPrev($foundItems) . '</td>
		<td>' . we_html_button::create_button("fa:iconview,fa-lg fa-th", "javascript:weSearch.setView('" . we_search_view::VIEW_ICONS . "');", true, 40, "", "", "", false) . '</td>
		<td>' . we_html_button::create_button("fa:listview,fa-lg fa-align-justify", "javascript:weSearch.setView('" . we_search_view::VIEW_LIST . "');", true, 40, "", "", "", false) . '</td>' .
			($this->Model->folderID && $this->Model->searchTable === FILE_TABLE ? we_html_baseElement::getHtmlCode(new we_html_baseElement('td', true, array('style' => 'width:50px;'), we_fileupload_ui_importer::getBtnImportFiles($this->Model->folderID))) : '') .
			'<td style="width:50px;">' . we_html_button::create_button("fa:btn_new_dir,fa-plus,fa-lg fa-folder", "javascript:top.we_cmd('new_document','" . FILE_TABLE . "','','" . we_base_ContentTypes::FOLDER . "','','" . $this->Model->folderID . "')", true, 50, "", "", "", false) . '</td>
	</tr>
</table>';
	}

	public function getSearchParameterBottom($table, $foundItems){
		switch($table){
			case TEMPLATES_TABLE:
				$publishButton = $publishButtonCheckboxAll = "";
				break;
			default:
				if(permissionhandler::hasPerm('PUBLISH')){
					$publishButtonCheckboxAll = we_html_forms::checkbox(1, 0, "action_all_" . $this->Model->whichSearch, "", false, "middlefont", "weSearch.checkAllActionChecks()");
					$publishButton = we_html_button::create_button(we_html_button::PUBLISH, "javascript:weSearch.publishDocs('" . $this->Model->whichSearch . "');", true, 100, 22, "", "");
				} else {
					$publishButton = $publishButtonCheckboxAll = "";
				}
		}

		return
			'<table class="default" style="margin-top:20px;">
	<tr>
	 <td>' . $publishButtonCheckboxAll . '</td>
	 <td style="font-size:12px;width:125px;">' . $publishButton . '</td>
	 <td class="defaultgray" style="width:60px;height:30px;" id="resetBusyDoclistSearch"></td>
	 <td style="width:370px;">' . self::getNextPrev($foundItems, false) . '</td>
	</tr>
</table>';
	}

	/**
	 * @abstract generates html for paging GUI
	 * @return string, html for paging GUI
	 */
	public function getNextPrev($we_search_anzahl, $isTop = true){
		/*
		if(($obj = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'obj'))){ // TODO: do we need session data other than from model?
			//$anzahl = $_SESSION['weS']['weSearch']['anzahl'];
			//$searchstart = $_SESSION['weS']['weSearch']['searchstart'];
			
			$anzahl = $this->Model->anzahl;
			$searchstart = $this->Model->searchstart;
		} else {
			$obj = $GLOBALS['we_doc'];
			$anzahl = $this->Model->anzahl;
			$searchstart = $this->Model->searchstart;
		}
		*/
		$anzahl = $this->Model->anzahl;
		$searchstart = $this->Model->searchstart;

		$out = '<table class="default"><tr><td>' .
			($searchstart ?
				we_html_button::create_button(we_html_button::BACK, 'javascript:weSearch.back(' . $anzahl . ');') :
				we_html_button::create_button(we_html_button::BACK, '', true, 100, 22, "", "", true)
			) .
			'</td><td class="defaultfont"><b>' . (($we_search_anzahl) ? $searchstart + 1 : 0) . '-' .
			(($we_search_anzahl - $searchstart) < $anzahl ? $we_search_anzahl : $searchstart + $anzahl) .
			' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl . '</b></td><td>' .
			(($searchstart + $anzahl) < $we_search_anzahl ?
				we_html_button::create_button(we_html_button::NEXT, 'javascript:weSearch.next(' . $anzahl . ');') :
				we_html_button::create_button(we_html_button::NEXT, '', true, 100, 22, '', '', true)
			) .
			'</td><td>';

		$pages = array();
		if($anzahl){
			for($i = 0; $i < ceil($we_search_anzahl / $anzahl); $i++){
				$pages[($i * $anzahl)] = ($i + 1);
			}
		}

		$page = ($anzahl ? ceil($searchstart / $anzahl) * $anzahl : 0);

		$select = we_html_tools::htmlSelect('page', $pages, 1, $page, false, array('onchange' => "this.form.elements.searchstartDoclistSearch.value = this.value; weSearch.search(false);"));

		if(!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'setInputSearchstart') && !defined('searchstart') && $isTop){
			define('searchstart', true);
			$out .= we_html_tools::hidden('searchstartDoclistSearch', $searchstart);
		}

		return $out . $select .
			'</td></tr></table>';
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
