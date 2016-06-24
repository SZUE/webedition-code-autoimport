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
class we_doclist_view extends we_search_view{

	public function __construct($model = null){
		$this->Model = $model ? : new we_doclist_model();
		$this->searchclass = new we_doclist_search($this);
	}

	/**
	 * @abstract create javascript for document list
	 * @return javascript code
	 */
	public function getSearchJS($whichSearch = ''){
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0);

		return we_html_element::jsScript(JS_DIR . 'we_modules/search/search_view.js') .
			we_html_element::jsElement('
weSearch.conf = {
	whichsearch: "' . we_search_view::SEARCH_DOCLIST . '",
	we_transaction: "' . $this->Model->transaction . '",
	editorBodyFrame : window,
	ajaxURL: WE().consts.dirs.WEBEDITION_DIR+"rpc.php",
	rows: ' . (isset($_REQUEST['searchFields' . we_search_view::SEARCH_DOCLIST]) ? count($_REQUEST['searchFields' . we_search_view::SEARCH_DOCLIST]) - 1 : 0) . ',
	tab: 0,
	modelClassName: "placeholder",
	modelID: "placeholder",
	modelIsFolder: true,
	//showSelects: "placeholder",
	rows: 0,
};
weSearch.elems = {
	btnTrash: \'' . str_replace("'", "\'", we_html_button::create_button(we_html_button::TRASH, "javascript:weSearch.delRow(__we_new_id__)")) . '\',
	btnSelector: \'' . str_replace("'", "\'", we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('__we_selector__', document.we_form.elements['search" . we_search_view::SEARCH_DOCLIST . "ParentID[__we_new_id__]'].value, '__we_sel_table__', 'document.we_form.elements[\\\'search" . we_search_view::SEARCH_DOCLIST . "ParentID[__we_new_id__]\\\'].value', 'document.we_form.elements[\\\'search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]\\\'].value');", true, 60, 22)) . '\',
	fieldSearch: \'' . str_replace("'", "\'", we_html_tools::htmlTextInput('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', 58, '', '', ' __we_read_only__class="wetextinput" id="search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]"', 'text', 170)) . '\',
	selStatus: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->searchclass->getFieldsStatus(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selSpeicherart: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->searchclass->getFieldsSpeicherart(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selLocation: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', we_search_search::getLocation(), 1, "", false, array('class' => "defaultfont", 'style' => 'width:150px;', 'id' => "location" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selLocationDate: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', we_search_search::getLocation('date'), 1, "", false, array('class' => "defaultfont", 'style' => 'width:150px;', 'id' => "location" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selLocationText: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', we_search_search::getLocation('text'), 1, "", false, array('class' => "defaultfont", 'style' => 'width:150px;', 'id' => "location" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selModFields: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->searchclass->getModFields(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	selUsers: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->searchclass->getUsers(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]"))) . '\',
	searchFields: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('searchFields' . we_search_view::SEARCH_DOCLIST . '[__we_new_id__]', $this->searchclass->getFields("__we_new_id__", we_search_view::SEARCH_DOCLIST), 1, "", false, array('class' => "defaultfont", 'id' => "searchFields" . we_search_view::SEARCH_DOCLIST . "[__we_new_id__]", 'onchange' => "weSearch.changeit(this.value, __we_new_id__);"))) . '\'
};

WE().consts.weSearch= {
	SEARCH_DOCS: "' . we_search_view::SEARCH_DOCS . '",
	SEARCH_TMPL: "' . we_search_view::SEARCH_TMPL . '",
	SEARCH_MEDIA: "' . we_search_view::SEARCH_MEDIA . '",
	SEARCH_ADV: "' . we_search_view::SEARCH_ADV . '",
	SEARCH_DOCLIST: "' . we_search_view::SEARCH_DOCLIST . '"
};
WE().consts.g_l.weSearch = {
	publish_docs:"' . g_l('searchtool', '[publish_docs]') . '",
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
	public function getSearchDialog($whichSearch = ''){ // TODO: use parent
		$currentSearchFields = $this->Model->getProperty('currentSearchFields');
		$currentSearch = $this->Model->getProperty('currentSearch');
		$currentLocation = $this->Model->getProperty('currentLocation');

		$out = '<table class="default" id="defSearch" style="width:550px;margin-left:20px;display:' . ($this->Model->mode ? 'none' : 'block') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:weSearch.switchSearch(1)", false) . '</td>
</tr>
</table>
<table class="default" id="advSearch" style="width:550px;margin-left:20px;display:' . ($this->Model->mode ? 'block' : 'none') . ';">
<tr>
	<td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '</td>
	<td>' . we_html_button::create_button(we_html_button::DIRDOWN, "javascript:weSearch.switchSearch(0)", false) . '</td>
</tr>
</table>' .
			we_class::hiddenTrans() .
			'<table class="default"  style="margin-left:20px;display:' . ($this->Model->mode ? 'block' : 'none') . ';">
<tbody id="filterTable' . we_search_view::SEARCH_DOCLIST . '">';

		// we always have at least an empty 'Content' search: it's set in model initialisation
		for($i = 0; $i < $this->Model->height; $i++){
			$button = we_html_button::create_button(we_html_button::TRASH, "javascript:weSearch.delRow(" . $i . ");", true, "", "", "", "", false);
			$handle = "";
			$searchInput = we_html_tools::htmlTextInput('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', 30, (isset($currentSearch[$i]) ? $currentSearch[$i] : ''), "", " class=\"wetextinput\"  id=\"search" . we_search_view::SEARCH_DOCLIST . "[" . $i . "]\" ", "text", 170);

			switch(isset($currentSearchFields[$i]) ? $currentSearchFields[$i] : ''){
				case 'Content':
				case 'Status':
				case 'Speicherart':
				case 'temp_template_id':
				case 'temp_category':
					$locationDisabled = 'disabled';
			}

			if(isset($currentSearchFields[$i])){
				switch($currentSearchFields[$i]){
					case 'Status':
						$searchInput = we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', $this->searchclass->getFieldsStatus(), 1, (isset($currentSearch[$i]) ? $currentSearch[$i] : ''), false, array('class' => 'defaultfont', 'style' => 'width:170px;', 'id' => 'search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']'));
						break;
					case 'Speicherart':
						$searchInput = we_html_tools::htmlSelect('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', $this->searchclass->getFieldsSpeicherart(), 1, (isset($currentSearch[$i]) ? $currentSearch[$i] : ''), false, array('class' => 'defaultfont', 'style' => 'width:170px;', 'id' => 'search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']'));
						break;
					case 'Published':
					case 'CreationDate':
					case 'ModDate':
						$handle = "date";
						$searchInput = we_html_tools::getDateSelector('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', '_from' . $i, (isset($currentSearch[$i]) ? $currentSearch[$i] : ''));
						break;
					case 'MasterTemplateID':
					case 'temp_template_id':
						$linkPath = (isset($currentSearch[$i]) ? $currentSearch[$i] : '');
						$rootDirID = 0;

						$cmd1 = "document.we_form.elements['search" . we_search_view::SEARCH_DOCLIST . "ParentID[" . $i . "]'].value";
						$cmd = "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . TEMPLATES_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("document.we_form.elements['search" . we_search_view::SEARCH_DOCLIST . "[" . $i . "]'].value") . "','','','" . $rootDirID . "','','" . we_base_ContentTypes::TEMPLATE . "')";
						$button = we_html_button::create_button(we_html_button::SELECT, $cmd, true, 60, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', 58, $linkPath, '', 'readonly ', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('search' . we_search_view::SEARCH_DOCLIST . 'ParentID[' . $i . ']', ''), $button);

						$searchInput = $selector;
						break;
					case 'temp_category':
						$linkPath = (isset($currentSearch[$i]) ? $currentSearch[$i] : '');
						$rootDirID = 0;

						$cmd = "javascript:we_cmd('we_selector_category',document.we_form.elements['search" . we_search_view::SEARCH_DOCLIST . "ParentID[" . $i . "]'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'search" . we_search_view::SEARCH_DOCLIST . "ParentID[" . $i . "]\\'].value','document.we_form.elements[\\'search" . we_search_view::SEARCH_DOCLIST . "[" . $i . "]\\'].value','','','" . $rootDirID . "','','')";
						$button = we_html_button::create_button(we_html_button::SELECT, $cmd, true, 60, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', 58, $linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('search' . we_search_view::SEARCH_DOCLIST . 'ParentID[' . $i . ']', ''), $button);

						$searchInput = $selector;
						break;
				}
			}
			$locationValue = isset($currentLocation[$i]) ? $currentLocation[$i] : '';
			$out .= '
<tr id="filterRow_' . $i . '">
	<td>' . we_html_element::htmlHidden('hidden_searchFields' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', isset($currentSearchFields[$i]) ? $currentSearchFields[$i] : '') . we_html_tools::htmlSelect('searchFields' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', $this->searchclass->getFields($i, we_search_view::SEARCH_DOCLIST), 1, (isset($currentSearchFields[$i]) ? $currentSearchFields[$i] : ""), false, array('class' => "defaultfont", 'id' => 'searchFields' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', 'onchange' => 'weSearch.changeit(this.value, ' . $i . ');')) . ' </td>
	<td id="td_location' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']">' . we_html_tools::htmlSelect('location' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', we_search_search::getLocation($handle), 1, (isset($currentLocation[$i]) ? $currentLocation[$i] : ""), false, array('class' => "defaultfont", 'style' => 'width:150px', $locationDisabled => $locationDisabled, 'id' => 'location' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']')) . ' </td>
	<td id="td_search' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']">' . $searchInput . '</td>
	<td id="td_delButton[' . $i . ']">' . $button . '</td>
	<td id="td_hiddenLocation[' . $i . ']">' . (!$locationDisabled ? '' : we_html_element::htmlHidden('location' . we_search_view::SEARCH_DOCLIST . '[' . $i . ']', $locationValue)) . '</td>
</tr>
		';
		}

		$out .= '</tbody></table>
<table class="default" id="advSearch3" style="margin-left:20px;margin-top:10px;display:' . ($this->Model->mode ? 'block' : 'none') . ';">
	<tr>
		<td style="width:215px;">' . we_html_button::create_button(we_html_button::ADD, "javascript:weSearch.newinput();") . '</td>
		<td style="width:155px;"></td>
		<td style="width:188px;text-align:right">' . we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);") . '</td>
		<td></td>
	</tr>
</table>' .
			we_html_element::jsElement('weSearch.calendarSetup(' . $this->Model->height . ');');

		return $out;
	}

	public function makeHeadLines($table){
		return array(
			array('dat' => '<span onclick="weSearch.setOrder(\'Text\', \'' . we_search_view::SEARCH_DOCLIST . '\');">' . g_l('searchtool', '[dateiname]') . ' <span id="Text_' . we_search_view::SEARCH_DOCLIST . '" >' . self::getSortImage('Text', we_search_view::SEARCH_DOCLIST) . '</span></span>'),
			array('dat' => '<span onclick="weSearch.setOrder(\'SiteTitle\', \'' . we_search_view::SEARCH_DOCLIST . '\');">' . ($table == TEMPLATES_TABLE ? g_l('weClass', '[path]') : g_l('searchtool', '[seitentitel]') ) . ' <span id="SiteTitle_' . we_search_view::SEARCH_DOCLIST . '" >' . self::getSortImage('SiteTitle', we_search_view::SEARCH_DOCLIST) . '</span></span>'),
			array('dat' => '<span onclick="javascript:weSearch.setOrder(\'CreationDate\', \'' . we_search_view::SEARCH_DOCLIST . '\');">' . g_l('searchtool', '[created]') . ' <span id="CreationDate_' . we_search_view::SEARCH_DOCLIST . '" >' . self::getSortImage('CreationDate', we_search_view::SEARCH_DOCLIST) . '</span></span>'),
			array('dat' => '<span onclick="javascript:weSearch.setOrder(\'ModDate\', \'' . we_search_view::SEARCH_DOCLIST . '\');">' . g_l('searchtool', '[modified]') . ' <span id="ModDate_' . we_search_view::SEARCH_DOCLIST . '" >' . self::getSortImage('ModDate', we_search_view::SEARCH_DOCLIST) . '</span></span>'),
		);
	}

	public function makeContent(array $result = [], $view = self::VIEW_LIST, $whichSearch = self::SEARCH_DOCS){
		$DB_WE = new DB_WE();
		$currentSetView = $this->Model->getProperty('currentSetView');
		$we_PathLength = 30;

		$resultCount = count($result);
		$content = [];

		for($f = 0; $f < $resultCount; $f++){
			$fontColor = '';
			$showPubCheckbox = true;
			if(isset($result[$f]['Published'])){
				switch($result[$f]['ContentType']){
					case we_base_ContentTypes::HTML:
					case we_base_ContentTypes::WEDOCUMENT:
					case we_base_ContentTypes::OBJECT_FILE:
						$published = ((($result[$f]['Published'] != 0) && ($result[$f]['Published'] < $result[$f]['ModDate'])) ? -1 : $result[$f]['Published']);
						if($published == 0){
							$fontColor = 'notpublished';
							$showPubCheckbox = false;
						} elseif($published == -1){
							$fontColor = 'changed';
							$showPubCheckbox = false;
						}
						break;
					default:
						$published = $result[$f]['Published'];
				}
			} else {
				$published = 1;
			}


			if($currentSetView === self::VIEW_LIST){
				switch($showPubCheckbox ? '-1' : $result[$f]['ContentType']){
					case we_base_ContentTypes::WEDOCUMENT:
					case we_base_ContentTypes::HTML:
					case we_base_ContentTypes::OBJECT_FILE:
						if(permissionhandler::hasPerm('PUBLISH')){
							$publishCheckbox = we_html_forms::checkbox($result[$f]['docID'] . '_' . addTblPrefix($result[$f]['docTable']), 0, 'publish_docs_DoclistSearch', '', false, 'middlefont', '');
							break;
						}
					default:
						$publishCheckbox = $showPubCheckbox ? '' : '<span style="width:20px"/>';
				}

				$content[$f] = array(
					array('dat' => $publishCheckbox),
					array('dat' => '<span class="iconListview"><span class="resultIcon" data-contenttype="' . $result[$f]["ContentType"] . '" data-extension="' . $result[$f]['Extension'] . '"></span></span>'),
					// TODO: set thumb ptah when doctype is image/*
					array('dat' => '<a href="javascript:WE().layout.openToEdit(\'' . addTblPrefix($result[$f]['docTable']) . '\',\'' . $result[$f]['docID'] . '\',\'' . $result[$f]['ContentType'] . '\')" class="' . $fontColor . ' middlefont" title="' . $result[$f]['Text'] . '"><u>' . we_base_util::shortenPath($result[$f]['Text'], $we_PathLength)),
					//array("dat" => '<nobr>' . g_l('contentTypes', '[' . $result[$f]['ContentType'] . ']') . '</nobr>'),
					array('dat' => '<nobr>' . we_base_util::shortenPath($result[$f]["SiteTitle"], $we_PathLength) . '</nobr>'),
					array('dat' => '<nobr>' . ($result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $result[$f]["CreationDate"]) : '-') . '</nobr>'),
					array('dat' => '<nobr>' . ($result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $result[$f]["ModDate"]) : '-') . '</nobr>')
				);
			} else {
				$fs = file_exists($_SERVER['DOCUMENT_ROOT'] . $result[$f]['Path']) ? filesize($_SERVER['DOCUMENT_ROOT'] . $result[$f]['Path']) : 0;

				if($result[$f]['ContentType'] == we_base_ContentTypes::IMAGE){
					$smallSize = 64;
					$bigSize = 140;

					if($fs){
						$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $result[$f]['Path']);

						$url = WEBEDITION_DIR . 'thumbnail.php?id=' . $result[$f]['docID'] . "&size[width]=" . $smallSize . "&path=" . urlencode($result[$f]["Path"]) . "&extension=" . $result[$f]["Extension"];
						$imageView = '<img src="' . $url . '" /></a>';
						$urlPopup = WEBEDITION_DIR . "thumbnail.php?id=" . $result[$f]["docID"] . "&size[width]=" . $bigSize . "&path=" . $result[$f]["Path"] . "&extension=" . $result[$f]["Extension"];
						$imageViewPopup = '<img src="' . $urlPopup . '"/></a>';
					} else {
						$imagesize = array(0, 0);
						$imageView = $imageViewPopup = '<span class="resultIcon" data-contenttype="' . $result[$f]['ContentType'] . '" data-extension="' . $result[$f]['Extension'] . '"></span>';
					}
				} else {
					$imagesize = array(0, 0);
					$imageView = $imageViewPopup = '<span class="iconGridview"><span class="resultIcon" data-contenttype="' . $result[$f]['ContentType'] . '" data-extension="' . $result[$f]['Extension'] . '"></span></span>';
				}

				$creator = $result[$f]['CreatorID'] ? id_to_path($result[$f]['CreatorID'], USER_TABLE, $DB_WE) : g_l('searchtool', '[nobody]');

				if($result[$f]['ContentType'] == we_base_ContentTypes::WEDOCUMENT){
					$templateID = ($result[$f]["Published"] >= $result[$f]["ModDate"] && $result[$f]["Published"] ?
							$result[$f]['TemplateID'] :
							$result[$f]['temp_template_id']);

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

				$defined_fields = we_metadata_metaData::getDefinedMetaDataFields();
				$metafields = [];
				$fieldcount = min(6, count($defined_fields));
				for($i = 0; $i < $fieldcount; $i++){
					$tagName = $defined_fields[$i]["tag"];

					if(we_exim_contentProvider::isBinary($result[$f]["docID"])){
						$DB_WE->query('SELECT l.DID,c.Dat FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DID=' . intval($result[$f]['docID']) . ' AND l.nHash=x\'' . md5($tagName) . '\' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
						$metafields[$tagName] = '';
						while($DB_WE->next_record()){
							$metafields[$tagName] = we_base_util::shortenPath($DB_WE->f('Dat'), 45);
						}
					}
				}

				$content[$f] = array(
					array("dat" => '<a href="javascript:WE().layout.openToEdit(\'' . addTblPrefix($result[$f]["docTable"]) . '\',\'' . $result[$f]["docID"] . '\',\'' . $result[$f]["ContentType"] . '\')" style="text-decoration:none" class="middlefont" title="' . $result[$f]["Text"] . '">' . $imageView . '</a>'),
					array("dat" => we_base_util::shortenPath($result[$f]["SiteTitle"], 17)),
					array("dat" => '<a href="javascript:WE().layout.openToEdit(\'' . addTblPrefix($result[$f]["docTable"]) . '\',\'' . $result[$f]["docID"] . '\',\'' . $result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $result[$f]["Text"] . '"><u>' . we_base_util::shortenPath($result[$f]["Text"], 17) . '</u></a>'),
					array("dat" => '<nobr>' . ($result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $result[$f]["ModDate"]) : "-") . '</nobr>'),
					array("dat" => '<a href="javascript:WE().layout.openToEdit(\'' . addTblPrefix($result[$f]["docTable"]) . '\',\'' . $result[$f]["docID"] . '\',\'' . $result[$f]["ContentType"] . '\')" style="text-decoration:none;" class="middlefont" title="' . $result[$f]["Text"] . '">' . $imageViewPopup . '</a>'),
					array("dat" => we_base_file::getHumanFileSize($fs)),
					array("dat" => $imagesize[0] . " x " . $imagesize[1]),
					array("dat" => we_base_util::shortenPath(g_l('contentTypes', '[' . ($result[$f]['ContentType']) . ']'), 22)),
					array("dat" => '<span class="' . $fontColor . '">' . we_base_util::shortenPath($result[$f]["Text"], 30) . '</span>'),
					array("dat" => we_base_util::shortenPath($result[$f]["SiteTitle"], 45)),
					array("dat" => we_base_util::shortenPath($result[$f]["Description"], 100)),
					array("dat" => $result[$f]['ContentType']),
					array("dat" => we_base_util::shortenPath($creator, 22)),
					array("dat" => $templateText),
					array("dat" => $metafields),
					array("dat" => $result[$f]["docID"]),
				);
			}
		}

		return $content;
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
