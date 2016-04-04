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
class we_search_view extends we_modules_view{
	const VIEW_LIST = 'list';
	const VIEW_ICONS = 'icons';
	const SEARCH_DOCS = 'DocSearch';
	const SEARCH_MEDIA = 'MediaSearch';
	const SEARCH_TMPL = 'TmplSearch';
	const SEARCH_ADV = 'AdvSearch';
	const SEARCH_DOCLIST = 'DoclistSearch';
	const SEARCH_VERSION = 'VersionSearch';

	var $toolName;
	var $db;
	var $frameset;
	var $topFrame;
	var $editorBodyFrame;
	var $editorHeaderFrame;
	var $icon_pattern = '';
	var $page = 1;
	public $searchclass;
	var $searchclassExp;
	private $searchMediaOptFieldIndex = 0;
	public $rpcCmd = ''; // make setter and set private

	//private $view = self::VIEW_LIST;

	public function __construct($frameset = ''){
		$topframe = 'top.content';
		parent::__construct($frameset, $topframe);
		$this->editorBodyFrame = $this->topFrame . '.editor.edbody';
		$this->editorHeaderFrame = $this->topFrame . '.editor.edheader';
		$this->Model = !isset($_SESSION['weS'][$this->toolName . '_session']) ? new we_search_model() :
			$_SESSION['weS'][$this->toolName . '_session'];
		//$this->Model = new we_search_model();
		$this->yuiSuggest = & weSuggest::getInstance();
		$this->searchclassExp = new we_search_exp();
		$this->searchclass = new we_search_search($this);
	}

	function getJSTop(){
		return we_html_element::jsElement('
WE().consts.g_l.weSearch = {
	publish_docs:"' . g_l('searchtool', '[publish_docs]') . '",
	noTempTableRightsSearch: "' . g_l('searchtool', '[noTempTableRightsSearch]') . '",
	nothingCheckedAdv: \'' . g_l('searchtool', '[nothingCheckedAdv]') . '\',
	nothingCheckedTmplDoc: \'' . g_l('searchtool', '[nothingCheckedTmplDoc]') . '\',
	buttonSelectValue: "' . g_l('button', '[select][value]') . '",
	versionsResetAllVersionsOK: "' . g_l('versions', '[resetAllVersionsOK]') . '",
	versionsNotChecked: "' . g_l('versions', '[notChecked]') . '",
	searchtool__notChecked: "' . g_l('searchtool', '[notChecked]') . '",
	searchtool__publishOK: "' . g_l('searchtool', '[publishOK]') . '",
	predefinedSearchmodify:"' . we_message_reporting::prepareMsgForJS(g_l('searchtool', '[predefinedSearchmodify]')) . '",
	predefinedSearchdelete:"' . we_message_reporting::prepareMsgForJS(g_l('searchtool', '[predefinedSearchdelete]')) . '",
	nothing_to_save:"' . we_message_reporting::prepareMsgForJS(g_l('tools', '[nothing_to_save]')) . '",
	nothing_to_delete:"' . we_message_reporting::prepareMsgForJS(g_l('tools', '[nothing_to_delete]')) . '",
	no_perms:"' . we_message_reporting::prepareMsgForJS(g_l('tools', '[no_perms]')) . '",
	confirmDel:"' . g_l('searchtool', '[confirmDel]') . '",
	nameForSearch:"' . g_l('searchtool', '[nameForSearch]') . '",
};') .
			we_html_element::jsScript(JS_DIR . 'we_modules/search/search_view2.js');
	}

	function processCommands(){
		$cmdid = we_base_request::_(we_base_request::INT, 'cmdid');
		switch(($cmd = we_base_request::_(we_base_request::STRING, 'cmd', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)))){
			case 'tool_weSearch_new' :
			case 'tool_weSearch_new_forDocuments' :
			case 'tool_weSearch_new_forTemplates' :
			case 'tool_weSearch_new_forObjects' :
			case 'tool_weSearch_new_forClasses' :
			case 'tool_weSearch_new_forMedia' :
			case 'tool_weSearch_new_advSearch' :
				//case 'tool_weSearch_new_group' :
				$this->Model = new we_search_model($cmdid);
				switch($cmd){
					case 'tool_weSearch_new_forDocuments' :
					case 'tool_weSearch_new_forMedia' :
						$tables = 1; // TODO: use constants
						break;
					case 'tool_weSearch_new_forTemplates' :
						$tables = 2;
						break;
					case 'tool_weSearch_new_forObjects' :
						$tables = 3;
						break;
					case 'tool_weSearch_new_forClasses' :
						$tables = 4;
						break;
					case 'tool_weSearch_new_forVersions' :
						$tables = 5;
						break;
					case 'tool_weSearch_new_forMedia' :
						$tables = 1;
						break;
					case 'tool_weSearch_new_advSearch' :
					case 'tool_weSearch_new' :
						$tables = 0;
				}

				$tab = we_base_request::_(we_base_request::INT, 'tab', we_base_request::_(we_base_request::INT, 'tabnr', 1));
				$keyword = we_base_request::_(we_base_request::STRING, 'keyword');
				$this->Model->setPredefinedSearch($tab, $keyword, $tables);
				$this->Model->prepareModelForSearch();
				$this->Model->setIsFolder(/* $cmd == 'tool_weSearch_new_group' ? 1 : */ 0);

				echo we_html_element::jsElement('if(' . $this->topFrame . '.editor){' .
					$this->editorHeaderFrame . '.location="' . $this->frameset . '&pnt=edheader' .
					($tab !== 0 ? '&tab=' . $tab : '') .
					'&text=' . urlencode($this->Model->Text) . '";' .
					$this->topFrame . '.editor.edfooter.location="' . $this->frameset . '&pnt=edfooter";
}');
				break;

			case 'tool_weSearch_edit' : // get model from db
				$this->Model = new we_search_model($cmdid);
				$this->Model->prepareModelForSearch();

				if(!$this->Model->isAllowedForUser()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('tools', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					$this->Model = new we_search_model();
					$_REQUEST['home'] = true;
					break;
				}
				echo we_html_element::jsElement(
					$this->editorHeaderFrame . '.location="' . $this->frameset . '&pnt=edheader' .
					($cmdid !== false ? '&cmdid=' . $cmdid : '') . '&text=' .
					urlencode($this->Model->Text) . '";' .
					$this->topFrame . '.editor.edfooter.location="' . $this->frameset . '&pnt=edfooter";
if(top.content.treeData){
 top.content.treeData.unselectNode();
 top.content.treeData.selectNode("' . $this->Model->ID . '");
}');
				break;

			case 'tool_weSearch_save' :
				$this->Model->Text = we_base_request::_(we_base_request::STRING, 'savedSearchName', $this->Model->Text);
				if(strlen($this->Model->Text) > 30){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('searchtool', '[nameTooLong]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if(stristr($this->Model->Text, "'") || stristr($this->Model->Text, '"')){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('searchtool', '[no_hochkomma]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->Model->filenameNotValid($this->Model->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('tools', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				$this->Model->activTab = we_base_request::_(we_base_request::INT, 'tabnr', 1); // TODO: have activeTab always active (initByHttp)!!

				if(!trim($this->Model->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('tools', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				$oldpath = $this->Model->Path;
				// set the path and check it
				$this->Model->setPath();
				if($this->Model->pathExists($this->Model->Path)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('tools', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if($this->Model->isSelf()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('tools', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				$newone = $this->Model->ID == 0;

				if($this->Model->save()){
					$this->Model->updateChildPaths($oldpath);

					$js = we_html_element::jsElement(($newone ?
								$this->topFrame . '.treeData.makeNewEntry({id:' . $this->Model->ID . ',parentid:' . $this->Model->ParentID . ',text:\'' . addslashes($this->Model->Text) . '\',open:0,contenttype:\'' . ($this->Model->IsFolder ? 'folder' : 'we/search') . '\',table:\'' . SUCHE_TABLE . '\',published:0});' :
								$this->topFrame . '.treeData.updateEntry({id:' . $this->Model->ID . ',text:\'' . $this->Model->Text . '\',parentid:' . $this->Model->ParentID . ',order:0,tooltip:' . $this->Model->ID . '});') .
							$this->editorHeaderFrame . '.location.reload();' .
							we_message_reporting::getShowMessageCall(g_l('searchtool', ($this->Model->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) .
							$this->topFrame . '.hot=0;'
					);

					if(we_base_request::_(we_base_request::BOOL, 'delayCmd')){
						$js .= we_html_element::jsElement($this->topFrame . '.we_cmd("' . we_base_request::_(we_base_request::STRING, 'delayCmd') . '"' . (($dp = we_base_request::_(we_base_request::RAW, 'delayParam')) ? ',"' . $dp . '"' : '') . ');'
						);
						$_REQUEST['delayCmd'] = '';
						$_REQUEST['delayParam'] = '';
					}
				} else {
					$js = we_html_element::jsElement($js .
							$this->editorHeaderFrame . '.location.reload();' .
							we_message_reporting::getShowMessageCall(g_l('searchtool', ($this->Model->IsFolder == 1 ? '[save_group_failed]' : '[save_failed]')), we_message_reporting::WE_MESSAGE_ERROR) .
							$this->topFrame . '.hot=0;'
					);
				}

				echo $js;

				break;
			case 'tool_weSearch_delete' :
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();');
				if($this->Model->delete()){
					echo we_html_element::jsElement(
						$this->topFrame . '.treeData.deleteEntry("' . $this->Model->ID . '");
setTimeout(top.we_showMessage,500,"' . g_l('tools', ($this->Model->IsFolder == 1 ? '[group_deleted]' : '[item_deleted]')) . '", WE().consts.message.WE_MESSAGE_NOTICE, window);' .
						$this->topFrame . '.we_cmd("tool_weSearch_edit");'
					);
					$this->Model = new we_search_model();
					$_REQUEST['pnt'] = 'edbody';
				}
				break;

			default :
		}

		$_SESSION['weS'][$this->toolName . '_session'] = $this->Model;
	}

	function getSearchJS($whichSearch = ''){
		switch($whichSearch){
			case "AdvSearch":
				$h = 140;
				//add height of each input row to calculate the scrollContent-height
				$addinputRows = 'for(i=1;i<newID;i++) {
        //scrollheight = scrollheight + 28;
       }';
				break;
			default:
				$h = 170;
				$addinputRows = "";
		}
		// FIXME: take actualTab from model
		$tab = we_base_request::_(we_base_request::INT, 'tab', we_base_request::_(we_base_request::INT, 'tabnr', 1));

		$showSelects = '';
		return we_html_element::jsScript(JS_DIR . 'we_modules/search/search_view.js') .
			we_html_element::jsElement('
weSearch.conf = {
	whichsearch: "' . $whichSearch . '",
	editorBodyFrame : ' . $this->editorBodyFrame . ',
	ajaxURL: WE().consts.dirs.WEBEDITION_DIR+"rpc.php",
	tab: "' . $tab . '",
	modelClassName: "' . $this->Model->ModelClassName . '",
	modelID: "' . $this->Model->ID . '",
	modelIsFolder: ' . ($this->Model->IsFolder ? 1 : 0) . ',
	showSelects: ' . ($showSelects ? 1 : 0) . ',
	rows: ' . ($whichSearch == self::SEARCH_MEDIA ? $this->searchMediaOptFieldIndex : (count($this->Model->getProperty('currentSearchFields')) - ($whichSearch == self::SEARCH_ADV ? 1 : 0))) . ',
	//rows: ' . (count($this->Model->getProperty('currentSearchFields')) - ($whichSearch == self::SEARCH_ADV ? 1 : 0) /* : ($whichSearch == self::SEARCH_MEDIA ? $this->searchMediaOptFieldIndex : (count($this->Model->getProperty('currentSearchFields')) - ($whichSearch == self::SEARCH_ADV ? 1 : 0))) */) . ',
	we_transaction: "' . $GLOBALS["we_transaction"] . '",
	checkRightTempTable: ' . (we_search_search::checkRightTempTable() ? 1 : 0) . ',
	checkRightDropTable: ' . (we_search_search::checkRightDropTable() ? 1 : 0) . '
};
weSearch.elems = {
	btnTrash: \'' . str_replace("'", "\'", we_html_button::create_button(we_html_button::TRASH, "javascript:weSearch.delRow(__we_new_id__)")) . '\',
	btnSelector: \'' . str_replace("'", "\'", we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('__we_selector__', document.we_form.elements['search" . $whichSearch . "ParentID[__we_new_id__]'].value, '__we_sel_table__', 'document.we_form.elements[\\\'search" . $whichSearch . "ParentID[__we_new_id__]\\\'].value', 'document.we_form.elements[\\\'search" . $whichSearch . "[__we_new_id__]\\\'].value', '', 0, '', '__we_content_types__');", true, 60, 22)) . '\',
	fieldSearch: \'' . str_replace("'", "\'", we_html_tools::htmlTextInput('search' . $whichSearch . '[__we_new_id__]', 58, '', '', ' __we_read_only__class="wetextinput" id="search' . $whichSearch . '[__we_new_id__]"', 'text', 170)) . '\',
	selStatus: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . $whichSearch . '[__we_new_id__]', $this->searchclass->getFieldsStatus(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . $whichSearch . "[__we_new_id__]"))) . '\',
	selSpeicherart: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . $whichSearch . '[__we_new_id__]', $this->searchclass->getFieldsSpeicherart(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . $whichSearch . "[__we_new_id__]"))) . '\',
	selLocation: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . $whichSearch . '[__we_new_id__]', we_search_search::getLocation(), 1, "", false, array('class' => "defaultfont", 'style' => 'width:150px', 'id' => "location" . $whichSearch . "[__we_new_id__]"))) . '\',
	selLocationDate: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . $whichSearch . '[__we_new_id__]', we_search_search::getLocation('date'), 1, "", false, array('class' => "defaultfont", 'style' => 'width:150px', 'id' => "location" . $whichSearch . "[__we_new_id__]"))) . '\',
	selLocationText: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . $whichSearch . '[__we_new_id__]', we_search_search::getLocation('text'), 1, "", false, array('class' => "defaultfont", 'style' => 'width:150px', 'id' => "location" . $whichSearch . "[__we_new_id__]"))) . '\',
	selModFields: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . $whichSearch . '[__we_new_id__]', $this->searchclass->getModFields(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . $whichSearch . "[__we_new_id__]"))) . '\',
	selUsers: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . $whichSearch . '[__we_new_id__]', $this->searchclass->getUsers(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . $whichSearch . "[__we_new_id__]"))) . '\',
	searchFields: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('searchFields' . $whichSearch . '[__we_new_id__]', $this->searchclass->getFields("__we_new_id__", $whichSearch), 1, "", false, array('class' => "defaultfont", 'id' => "searchFields" . $whichSearch . "[__we_new_id__]", 'onchange' => "weSearch.changeit(this.value, __we_new_id__);"))) . '\'
};

WE().consts.weSearch= {
	SEARCH_DOCS: "' . self::SEARCH_DOCS . '",
	SEARCH_TMPL: "' . self::SEARCH_TMPL . '",
	SEARCH_MEDIA: "' . self::SEARCH_MEDIA . '",
	SEARCH_ADV: "' . self::SEARCH_ADV . '",
	MEDIA_CONTENTTYPES_CSV: "' . we_base_ContentTypes::APPLICATION . "," . we_base_ContentTypes::AUDIO . "," . we_base_ContentTypes::FLASH . "," . we_base_ContentTypes::IMAGE . "," . we_base_ContentTypes::QUICKTIME . "," . we_base_ContentTypes::VIDEO . '"
};
');
	}

	function getNextPrev($we_search_anzahl, $whichSearch, $isTop = true, $dataOnly = false){
		$currentOrder = $this->Model->getProperty('currentOrder');
		$currentSetView = $this->Model->getProperty('currentSetView');
		$currentSearchstart = $this->Model->getProperty('currentSearchstart');
		$currentAnzahl = $this->Model->getProperty('currentAnzahl') ? : 1;

		$disableBack = $currentSearchstart ? false : true;
		$disableNext = ($currentSearchstart + $currentAnzahl) >= $we_search_anzahl;

		$text = (($we_search_anzahl) ? $currentSearchstart + 1 : 0) . '-' .
			(($we_search_anzahl - $currentSearchstart) < $currentAnzahl ? $we_search_anzahl : $currentSearchstart + $currentAnzahl) .
			' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl;

		$pages = array();
		if($currentAnzahl){
			for($i = 0; $i < ceil($we_search_anzahl / $currentAnzahl); $i++){
				$pages[($i * $currentAnzahl)] = ($i + 1);
			}
		}
		$page = ($currentAnzahl ? ceil($currentSearchstart / $currentAnzahl) * $currentAnzahl : 0);

		if($dataOnly){
			return we_html_element::htmlSpan(array(
					'class' => 'nextPrevData',
					'style' => "display:none",
					'data-setView' => $currentSetView,
					'data-mode' => $this->Model->mode, // IMI DO: set mode to curr
					'data-order' => $currentOrder,
					'data-searchstart' => $currentSearchstart,
					'data-number' => $currentAnzahl,
					'data-disableBack' => $disableBack ? 'true' : 'false',
					'data-disableNext' => $disableNext ? 'true' : 'false',
					'data-text' => $text,
					'data-pageValue' => implode(',', array_keys($pages)),
					'data-pageText' => implode(',', array_values($pages)),
					'data-page' => $page
			));
		}

		$btnBack = we_html_button::create_button(we_html_button::BACK, 'javascript:weSearch.back();', true, 100, 22, '', '', $disableBack, true, '', false, '', 'btnSearchBack');
		$btnNext = we_html_button::create_button(we_html_button::NEXT, 'javascript:weSearch.next();', true, 100, 22, '', '', $disableNext, true, '', false, '', 'btnSearchNext');
		$select = we_html_tools::htmlSelect('page', $pages, 1, $page, false, array('onchange' => "this.form.elements.searchstart" . $whichSearch . ".value = this.value; weSearch.search(false);"), 'value', 0, 'selectSearchPages');

		$tbl = new we_html_table(array(), 1, 4);
		$tbl->setCol(0, 0, array(), $btnBack);
		$tbl->setCol(0, 1, array('class' => 'defaultfont'), we_html_element::htmlSpan(array('class' => 'spanSearchText bold'), $text));
		$tbl->setCol(0, 2, array(), $btnNext);
		$tbl->setCol(0, 3, array(), $select);

		return $tbl->getHtml();
	}

	function getSortImage($for, $whichSearch){
		$currentOrder = $this->Model->getProperty('currentOrder');
		if($currentOrder){
			if(strpos($currentOrder, $for) === 0){
				if(strpos($currentOrder, 'DESC')){
					return '<i class="fa fa-sort-desc fa-lg"></i>';
				}
				return '<i class="fa fa-sort-asc fa-lg"></i>';
			}
			return '';
		}
		return '<i class="fa fa-sort fa-lg"></i>';
	}

	function getSearchDialogOptions($whichSearch){
		$_table = new we_html_table(array('style' => 'width:500px',), 2, 3);
		$row = 0;
		$currentSearchForField = $this->Model->getProperty('currentSearchForField');

		switch($whichSearch){
			case self::SEARCH_DOCS :
				$_table->setCol($row, 0, array('style' => 'width:200px;'), we_html_forms::checkboxWithHidden(empty($currentSearchForField['text']) ? false : true, 'searchForText' . $whichSearch, g_l('searchtool', '[onlyFilename]'), false, 'defaultfont', ''));
				$_table->setCol($row++, 1, array(), we_html_forms::checkboxWithHidden(empty($currentSearchForField['title']) ? false : true, 'searchForTitle' . $whichSearch, g_l('searchtool', '[onlyTitle]'), false, 'defaultfont', ''));
				$_table->setCol($row, 0, array(), we_html_forms::checkboxWithHidden(empty($currentSearchForField['content']) ? false : true, 'searchForContent' . $whichSearch, g_l('searchtool', '[Content]'), false, 'defaultfont', ''));
				break;
			case self::SEARCH_TMPL :
				$_table->setCol($row++, 0, array(), we_html_forms::checkboxWithHidden(empty($currentSearchForField['text']) ? false : true, 'searchForText' . $whichSearch, g_l('searchtool', '[onlyFilename]'), false, 'defaultfont', ''));
				$_table->setCol($row, 0, array(), we_html_forms::checkboxWithHidden(empty($currentSearchForField['content']) ? false : true, 'searchForContent' . $whichSearch, g_l('searchtool', '[Content]'), false, 'defaultfont', ''));
				break;
			case self::SEARCH_MEDIA :
				$_table->setCol($row, 0, array('style' => 'width:200px;'), we_html_forms::checkboxWithHidden(empty($currentSearchForField['text']) ? false : true, 'searchForText' . $whichSearch, g_l('searchtool', '[onlyFilename]'), false, 'defaultfont', ''));
				$_table->setCol($row++, 1, array(), we_html_forms::checkboxWithHidden(empty($currentSearchForField['title']) ? false : true, 'searchForTitle' . $whichSearch, g_l('searchtool', '[onlyTitle]'), false, 'defaultfont', ''));
				$_table->setCol($row, 0, array(), we_html_forms::checkboxWithHidden(empty($currentSearchForField['meta']) ? false : true, 'searchForMeta' . $whichSearch, g_l('searchtool', '[onlyMetadata]'), false, 'defaultfont', ''));

				return $_table->getHtml();
		}
		$_table->setCol($row++, 2, array('style' => 'text-align:right'), we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);"));

		return $_table->getHtml();
	}

	function getSearchDialogMediaType($whichSearch){
		$_table = new we_html_table(array('style' => 'width:400px',), 2, 2);
		$currentSearchForContentType = $this->Model->getProperty('currentSearchForContentType');

		switch($whichSearch){
			case self::SEARCH_MEDIA :
				$n = 1;
				$_table->setCol(0, 0, array('style' => 'width:200px;'), we_html_element::htmlHiddens(array(
						'searchFields' . $whichSearch . '[' . $n . ']' => 'ContentType',
						'search' . $whichSearch . '[' . $n . ']' => 1,
						'location' . $whichSearch . '[' . $n++ . ']' => 'IN')) .
					we_html_forms::checkboxWithHidden($currentSearchForContentType['image'] ? true : false, 'searchForImage' . $whichSearch, g_l('contentTypes', '[image/*]'), false, 'defaultfont withSpace', ''));
				$_table->setCol(0, 1, array(), we_html_forms::checkboxWithHidden($currentSearchForContentType['audio'] ? true : false, 'searchForAudio' . $whichSearch, g_l('contentTypes', '[audio/*]'), false, 'defaultfont', ''));
				$_table->setCol(1, 1, array(), we_html_forms::checkboxWithHidden($currentSearchForContentType['video'] ? true : false, 'searchForVideo' . $whichSearch, g_l('contentTypes', '[video/*]'), false, 'defaultfont', ''));
				$_table->setCol(1, 0, array(), we_html_forms::checkboxWithHidden($currentSearchForContentType['other'] ? true : false, 'searchForOther' . $whichSearch, g_l('contentTypes', '[media/*]'), false, 'defaultfont', '', false));
				break;
			default:
				return;
		}

		return $_table->getHtml();
	}

	function getSearchDialogFilter($whichSearch){
		$_table = new we_html_table(array(
			'width' => 400,
			), 4, 4);

		$currentSearch = $this->Model->getProperty('currentSearch');

		switch($whichSearch){
			case self::SEARCH_MEDIA :
				$n = 2;
				$_table->setCol(0, 0, array(), g_l('searchtool', '[usage_state]') . ': ');
				$_table->setCol(0, 1, array('colspan' => 2), we_html_element::htmlHiddens(array(
						'searchFields' . $whichSearch . '[' . $n . ']' => 'IsUsed',
						'location' . $whichSearch . '[' . $n . ']' => 'IS')) .
					we_html_tools::htmlSelect('search' . $whichSearch . '[' . $n . ']', array(
						0 => g_l('searchtool', '[all]'),
						1 => g_l('searchtool', '[only_unsed]'),
						2 => g_l('searchtool', '[only_unused]')), 1, isset($currentSearch[$n]) ? $currentSearch[$n] : '', false, array(), 'value', 228));
				$_table->setCol(0, 3, array(), we_html_tools::htmlAlertAttentionBox(g_l('searchtool', '[media_do_rebuild]'), we_html_tools::TYPE_HELP, false));

				$_table->setCol(1, 0, array(), g_l('searchtool', '[protection]') . ': ');
				$_table->setCol(1, 1, array('colspan' => 2), we_html_element::htmlHiddens(array(
						'searchFields' . $whichSearch . '[' . ++$n . ']' => 'IsProtected',
						'location' . $whichSearch . '[' . $n . ']' => 'IS')) .
					we_html_tools::htmlSelect('search' . $whichSearch . '[' . $n . ']', array(
						0 => g_l('searchtool', '[all]'),
						1 => g_l('searchtool', '[only_protected]'),
						2 => g_l('searchtool', '[only_unprotected]')), 1, isset($currentSearch[$n]) ? $currentSearch[$n] : '', false, array(), 'value', 228));

				$this->searchMediaOptFieldIndex = ++$n; // FIXME: do we need this (or can we handle it as simple param?)
				break;
			default:
				return;
		}
		$_table->setCol(2, 0, array('colspan' => 4), $this->getSearchDialogOptionalFields($whichSearch));
		$_table->setCol(3, 3, array(), we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);"));

		return $_table->getHtml();
	}

	function getSearchDialogCheckboxesAdvSearch(){
		/* => if we need this move it to model init
		  if(($table = we_base_request::_(we_base_request::TABLE, 'table'))){
		  $search_tables_advSearch = $table;
		  $this->Model->search_tables_advSearch[$search_tables_advSearch] = 1;
		  }
		 *
		 */

		$_table = new we_html_table(array('style' => 'width:550px',), 4, 3);
		$currentSearchTables = $this->Model->getProperty('currentSearchTables');

		if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
			$_table->setCol(0, 0, array(), we_html_forms::checkboxWithHidden(in_array(FILE_TABLE, $currentSearchTables), 'search_tables_advSearch[' . FILE_TABLE . ']', g_l('searchtool', '[documents]'), false, 'defaultfont', ''));
		}

		if(permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
			$_table->setCol(1, 0, array(), we_html_forms::checkboxWithHidden(in_array(TEMPLATES_TABLE, $currentSearchTables), 'search_tables_advSearch[' . TEMPLATES_TABLE . ']', g_l('searchtool', '[templates]'), false, 'defaultfont', ''));
		}

		if(defined('OBJECT_TABLE')){
			if(permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
				$_table->setCol(0, 1, array(), we_html_forms::checkboxWithHidden(in_array(OBJECT_FILES_TABLE, $currentSearchTables), 'search_tables_advSearch[' . OBJECT_FILES_TABLE . ']', g_l('searchtool', '[objects]'), false, 'defaultfont', ''));
			}
			if(permissionhandler::hasPerm('CAN_SEE_OBJECTS') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
				$_table->setCol(1, 1, array(), we_html_forms::checkboxWithHidden(in_array(OBJECT_TABLE, $currentSearchTables), 'search_tables_advSearch[' . OBJECT_TABLE . ']', g_l('searchtool', '[classes]'), false, 'defaultfont', ''));
			}
		}

		if(permissionhandler::hasPerm('SEE_VERSIONS')){
			$_table->setCol(0, 2, array(), we_html_forms::checkboxWithHidden(in_array(VERSIONS_TABLE, $currentSearchTables), 'search_tables_advSearch[' . VERSIONS_TABLE . ']', g_l('versions', '[versions]'), false, 'defaultfont', ''));
		}

		$_table->setCol(1, 2, array('style' => 'text-align:right'), we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);"));

		return $_table->getHtml();
	}

	function getSearchDialog($whichSearch = ''){
		$searchInput = '';
		$currentSearch = $this->Model->getProperty('currentSearch');

		switch($whichSearch){
			case self::SEARCH_TMPL :
				$searchTables = 'search_tables_' . $whichSearch . '[' . TEMPLATES_TABLE . ']';
				break;
			case self::SEARCH_MEDIA :
				$searchInput .= we_html_element::htmlHidden('searchFields' . $whichSearch . '[0]', 'keyword');
			// no break
			case self::SEARCH_DOCS :
				$searchTables = 'search_tables_' . $whichSearch . '[' . FILE_TABLE . ']';
				break;
		}
		$searchInput .= we_html_tools::htmlTextInput('search' . $whichSearch . '[0]', 30, (isset($currentSearch[0]) ? $currentSearch[0] : ''), '', '', 'search', $whichSearch == self::SEARCH_MEDIA ? 348 : 380);

		return '<div id="mouseOverDivs_' . $whichSearch . '"></div><table class="default">
<tbody>
<tr>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
</tr>
<tr>
 <td style="padding-right:20px;">' . $searchInput . ($whichSearch == self::SEARCH_MEDIA ? ' ' . we_html_tools::htmlAlertAttentionBox("Ohne Suchbegriff werden alle Medien-Dokumente ausgegeben.", we_html_tools::TYPE_HELP, false) : '') . '</td>
 <td>' . we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);") . '</td>
 <td>' . we_html_tools::hidden('location' . $whichSearch . '[0]', 'CONTAIN') . '</td>
 <td>' . we_html_tools::hidden($searchTables, 1) . '</td>
 <td></td>
</tr>' . (false && $whichSearch == self::SEARCH_MEDIA ?
				'<tr><td colspan="5"></td></tr>' :
				'') . '
</tbody></table>';
	}

	function makeHeadLines($whichSearch){
		return $whichSearch !== self::SEARCH_MEDIA ?
			array(
			array("dat" => '<a href="javascript:weSearch.setOrder(\'Text\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[dateiname]') . '</a> <span id="Text_' . $whichSearch . '" >' . $this->getSortImage('Text', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'SiteTitle\',\'' . $whichSearch . '\');">' . ($whichSearch === 'TmplSearch' ? g_l('weClass', '[path]') : g_l('searchtool', '[seitentitel]')) . '</a> <span id="SiteTitle_' . $whichSearch . '" >' . $this->getSortImage('SiteTitle', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'CreationDate\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[created]') . '</a> <span id="CreationDate_' . $whichSearch . '" >' . $this->getSortImage('CreationDate', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'ModDate\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[modified]') . '</a> <span id="ModDate_' . $whichSearch . '" >' . $this->getSortImage('ModDate', $whichSearch) . '</span>')
			) :
			array(
			array("dat" => '<a href="javascript:weSearch.setOrder(\'Text\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[dateiname]') . '</a> <span id="Text_' . $whichSearch . '" >' . $this->getSortImage('Text', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'media_filesize\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[groesse]') . '</a> <span id="media_filesize_' . $whichSearch . '" >' . $this->getSortImage('media_filesize', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'IsUsed\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[Status]') . '</a> <span id="IsUsed_' . $whichSearch . '" >' . $this->getSortImage('IsUsed', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'media_alt\',\'' . $whichSearch . '\');">alt</a> <span id="media_alt_' . $whichSearch . '" >' . $this->getSortImage('media_alt', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'media_title\',\'' . $whichSearch . '\');">title</a> <span id="media_title_' . $whichSearch . '" >' . $this->getSortImage('media_title', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'CreationDate\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[created]') . '</a> <span id="CreationDate_' . $whichSearch . '" >' . $this->getSortImage('CreationDate', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'ModDate\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[modified]') . '</a> <span id="ModDate_' . $whichSearch . '" >' . $this->getSortImage('ModDate', $whichSearch) . '</span>'),
			array("dat" => '')
		);
	}

	public function makeContent(array $_result = array(), $view = self::VIEW_LIST, $whichSearch = self::SEARCH_DOCS){
		$DB_WE = new DB_WE();

		$content = array();
		$resultCount = count($_result);

		for($f = 0; $f < $resultCount; $f++){
			$fontColor = '';
			$showPubCheckbox = true;
			switch($_result[$f]["ContentType"]){
				case we_base_ContentTypes::HTML:
				case we_base_ContentTypes::WEDOCUMENT:
				case we_base_ContentTypes::OBJECT_FILE:
					$published = ((($_result[$f]['Published'] != 0) && ($_result[$f]['Published'] < $_result[$f]['ModDate'])) ? -1 : $_result[$f]['Published']);
					if($published == 0){
						$fontColor = 'notpublished';
						$showPubCheckbox = false;
					} elseif($published == -1){
						$fontColor = 'changed';
						$showPubCheckbox = false;
					}
					break;
				default:
					$published = (isset($_result[$f]["Published"]) ? $_result[$f]["Published"] : 1);
			}

			//$ext = isset($_result[$f]['Extension']) ? $_result[$f]['Extension'] : "";
			$foundInVersions = isset($_result[$f]["foundInVersions"]) ? makeArrayFromCSV($_result[$f]["foundInVersions"]) : "";

			if(!$view || $view == self::VIEW_LIST){
				if(is_array($foundInVersions) && !empty($foundInVersions)){

					rsort($foundInVersions);
					foreach($foundInVersions as $k){

						$resetDisabled = false;
						if(!permissionhandler::hasPerm('RESET_VERSIONS')){
							$resetDisabled = true;
						}

						list($ID, $timestamp, $version, $active) = getHash('SELECT ID,timestamp,version,active FROM ' . VERSIONS_TABLE . ' WHERE ID=' . intval($k), $DB_WE, MYSQL_NUM)? : array(0, 0, 0, 0);

						$previewButton = we_html_button::create_button(we_html_button::PREVIEW, "javascript:weSearch.previewVersion('" . $ID . "');");

						$fileExists = f('SELECT 1 FROM ' . escape_sql_query(addTblPrefix($_result[$f]['docTable'])) . ' WHERE ID=' . intval($_result[$f]['docID']), '', $DB_WE);

						if($active && $fileExists){
							$resetDisabled = true;
						}

						$classNotExistsText = '';
						//if class doesn't exists it's not possible to reset object-version!
						if($_result[$f]['ContentType'] === we_base_ContentTypes::OBJECT_FILE){

							if(!f('SELECT 1 FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($_result[$f]["TableID"]), '', $DB_WE)){
								$resetDisabled = true;
								$classNotExistsText = '(' . g_l('versions', '[objClassNotExists]') . ')';
							}
						}

						$content[] = array(
							array("version" => array($k => "")),
							array("version" => array($k => "<span style='margin-left:5px;'>" . g_l('versions', '[version]') . " " . $version . "</span> <br/><span style='font-weight:100;color:red;margin-left:10px;'>" . $classNotExistsText . "</span>")),
							array("version" => array($k => "<div style='margin-bottom:5px;margin-left:5px;float:left;'>" .
									we_html_forms::radiobutton($ID, 0, "resetVersion[" . $_result[$f]["ID"] . "_" . $_result[$f]["Table"] . "]", "", false, "defaultfont", "", $resetDisabled) . "</div><div style='float:left;margin-left:30px;'>" . $previewButton . "</div>")),
							array("version" => array($k => "<span style='margin-left:5px;'>" . date("d.m.Y", $timestamp) . "</span>")),
							array("version" => array($k => "")),
							array("version" => array($k => "<div style='margin-left:5px;'>" .
									(($_result[$f]["ContentType"] == we_base_ContentTypes::WEDOCUMENT || $_result[$f]["ContentType"] == we_base_ContentTypes::HTML || $_result[$f]["ContentType"] === we_base_ContentTypes::OBJECT_FILE) ?
										we_html_forms::checkbox($ID, 0, "publishVersion_" . $ID, g_l('versions', '[publishIfReset]'), false, "middlefont", "") :
										"") .
									"</div>")),
						);
					}
				}
//Checkbox
				if($whichSearch !== self::SEARCH_MEDIA){
					switch($_result[$f]['ContentType']){
						case we_base_ContentTypes::WEDOCUMENT:
						case we_base_ContentTypes::HTML:
						case 'objectFile':
							$actionCheckbox = (!$showPubCheckbox ?
									(permissionhandler::hasPerm('PUBLISH') && f('SELECT 1 FROM ' . escape_sql_query(addTblPrefix($_result[$f]['docTable'])) . ' WHERE ID=' . intval($_result[$f]['docID']), '', $DB_WE)) ?
										we_html_forms::checkbox($_result[$f]['docID'] . '_' . addTblPrefix($_result[$f]['docTable']), 0, 'publish_docs_' . $whichSearch, '', false, 'middlefont', '') :
										'' :
									'');
							break;
						default:
							$actionCheckbox = '';
					}
				} else {
					switch($_result[$f]["ContentType"]){
						case we_base_ContentTypes::IMAGE:
						case we_base_ContentTypes::AUDIO:
						case we_base_ContentTypes::VIDEO:
						case we_base_ContentTypes::QUICKTIME:
						case we_base_ContentTypes::FLASH:
						case we_base_ContentTypes::APPLICATION:
							$actionCheckbox = '';
							if($_result[$f]["IsProtected"]){
								$actionCheckbox = we_html_element::htmlSpan(array('class' => 'wealertIcon', 'style' => 'margin-left:4px;', 'title' => g_l('searchtool', '[image_protected]')));
							} else if(!in_array($_result[$f]['docID'], $this->searchclass->getUsedMedia())){
								$actionCheckbox = permissionhandler::hasPerm('DELETE_DOCUMENT') && f('SELECT 1 FROM ' . escape_sql_query(addTblPrefix($_result[$f]["docTable"])) . ' WHERE ID=' . intval($_result[$f]['docID']), '', $DB_WE) ?
									we_html_forms::checkbox($_result[$f]['docID'] . '_' . addTblPrefix($_result[$f]['docTable']), 0, 'delete_docs_' . $whichSearch, '', false, 'middlefont', '') : '';
							}
							break;
						default:
							$actionCheckbox = '';
					}
				}

				$_result[$f]['size'] = file_exists($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) : 0;
				$_result[$f]['fileSize'] = we_base_file::getHumanFileSize($_result[$f]['size']);
				$iconHTML = $this->getHtmlIconThmubnail($_result[$f], 64, 140, $view, $whichSearch);
				$standardStyle = 'height:12px;padding-top:6px;font-size:11px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;';
				$content[] = $whichSearch !== self::SEARCH_MEDIA ?
					array(
					array("dat" => $actionCheckbox),
					array("dat" => '<span class="iconListview">' . $iconHTML['imageView'] . '</span>'),
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . addTblPrefix($_result[$f]['docTable']) . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]['Path'] . ' (ID: ' . $_result[$f]['docID'] . ')"><u>' . $_result[$f]["Text"]),
					array("dat" => ($whichSearch === 'TmplSearch' ? str_replace('/' . $_result[$f]["Text"], '', $_result[$f]["Path"]) : $_result[$f]["SiteTitle"])),
					array("dat" => isset($_result[$f]["VersionID"]) && $_result[$f]['VersionID'] ?
							"-" :
							($_result[$f]["CreationDate"] ?
								date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) :
								"-")),
					array("dat" => ($_result[$f]["ModDate"] ?
							date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) :
							"-")),
					) :
					array(
					array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'vertical-align:top;"', 'dat' => array(
							array('elem' => 'table', '' => '', 'dat' => array(
									array('elem' => 'row', 'dat' => array(
											array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'padding-top:10px;"', 'dat' => $actionCheckbox),
										)
									),
									array('elem' => 'row', 'attribs' => '', 'dat' => array(
											array('elem' => 'td', 'attribs' => '', 'dat' => '&nbsp;'),
										)
									)
								)
							)
						)),
					array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'vertical-align:top;"', 'dat' => '<a href="javascript:weSearch.openToEdit(\'' . addTblPrefix($_result[$f]['docTable']) . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]['Path'] . '"><span class="iconListview">' . $iconHTML['imageView'] . '</span>'),
					array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'vertical-align:top;"', 'dat' => array(
							array('elem' => 'table', '' => '', 'dat' => array(
									array('elem' => 'row', 'dat' => array(
											array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . '" class="bold"', 'dat' => '<a href="javascript:weSearch.openToEdit(\'' . addTblPrefix($_result[$f]['docTable']) . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]['Path'] . ' (ID: ' . $_result[$f]['docID'] . ')"><u>' . $_result[$f]["Text"] . '</u></a>'),
											array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'width:75px;text-align:left"', 'dat' => ($_result[$f]['IsUsed'] ? we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:weSearch.toggleAdditionalContent(this, " . $_result[$f]['docID'] . ")", true, 21, 22, "", "", false, false, '__' . $_result[$f]['docID'], false, 'Verwendet in:') : '')),
											array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'width:70px;text-align:left"', 'dat' => $_result[$f]['fileSize']),
											array('elem' => 'td', 'attribs' => ($_result[$f]['IsUsed'] ? 'title="Dokument wird benutzt." onclick="weSearch.showAdditional(' . $_result[$f]['docID'] . ')" style="cursor:pointer;width:45px;text-align:left;' . $standardStyle . 'height:auto;"' : 'title="Dokument wird nicht benutzt!" style="width:45px;text-align:left;' . $standardStyle . '"'), 'dat' => '<i class="fa fa-lg fa-circle" style="color:' . ($_result[$f]['IsUsed'] ? 'green' : 'yellow') . ';"></i>'),
											array('elem' => 'td', 'attribs' => 'title="' . ($_result[$f]['media_alt'] ? : 'Alt-Attribut nicht gesetzt" ') . '" style="width:45px;text-align:left;' . $standardStyle . '"', 'dat' => '<i class="fa fa-lg fa-circle" style="color:' . ($_result[$f]['media_alt'] ? 'green' : 'red') . ';"></i>'),
											array('elem' => 'td', 'attribs' => 'title="' . ($_result[$f]['media_title'] ? : 'Title-Attribut nicht gesetzt" ') . '" style="width:45px;text-align:left;' . $standardStyle . '"', 'dat' => '<i class="fa fa-lg fa-circle" style="color:' . ($_result[$f]['media_title'] ? 'green' : 'red') . ';"></i>'),
											array('elem' => 'td', 'attribs' => 'style="width:90px;' . $standardStyle . '"', 'dat' => $_result[$f]['CreationDate'] ? date(g_l('searchtool', '[date_format]'), $_result[$f]['CreationDate']) : '-'),
											array('elem' => 'td', 'attribs' => 'style="width:90px;' . $standardStyle . '"', 'dat' => $_result[$f]['ModDate'] ? date(g_l('searchtool', '[date_format]'), $_result[$f]['ModDate']) : '-'),
											array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'width:30px;text-align:left"', 'dat' => we_html_button::create_button(we_html_button::EDIT, "javascript:weSearch.openToEdit('" . FILE_TABLE . "'," . $_result[$f]["docID"] . ",'" . $_result[$f]["ContentType"] . "');", true, 27, 22)),
										)),
									array('elem' => 'row', 'dat' => array(
											array('elem' => 'td', 'attribs' => 'id="infoTable_' . $_result[$f]["docID"] . '" style="display:none;width:100%;text-align:left;"' . $standardStyle . 'height:auto;overflow:visible;" colspan="7"', 'dat' => $this->makeAdditionalContentMedia($_result[$f])),
										))
								), 'colgroup' => '</colgroup>
	<col style="text-align:left;"/>
	<col style="width:48px;text-align:left;"/>
	<col style="width:80px;text-align:left;"/>
	<col style="width:45px;text-align:left;"/>
	<col style="width:45px;text-align:left;"/>
	<col style="width:45px;text-align:left;"/>
	<col style="width:90px;text-align:left;"/>
	<col style="width:90px;text-align:left;"/>
	<col style="width:60px;text-align:left;"/>
</colgroup>'
							)
						)),
				);
			} else {
				$_result[$f]['size'] = file_exists($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) : 0;
				$_result[$f]['fileSize'] = we_base_file::getHumanFileSize($_result[$f]['size']);
				$iconHTML = $this->getHtmlIconThmubnail($_result[$f], 64, $whichSearch === self::SEARCH_MEDIA ? 180 : 140, $view, $whichSearch);
				$creator = $_result[$f]["CreatorID"] ? id_to_path($_result[$f]["CreatorID"], USER_TABLE, $DB_WE) : g_l('searchtool', '[nobody]');

				if($_result[$f]["ContentType"] == we_base_ContentTypes::WEDOCUMENT && $_result[$f]["Table"] != VERSIONS_TABLE){
					$templateID = ($_result[$f]["Published"] >= $_result[$f]["ModDate"] && $_result[$f]["Published"] != 0 ?
							$_result[$f]["TemplateID"] :
							$_result[$f]["temp_template_id"]);

					$templateText = g_l('searchtool', '[no_template]');
					if($templateID){
						$DB_WE->query('SELECT ID,Text FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($templateID));
						while($DB_WE->next_record()){
							$templateText = we_base_util::shortenPath($DB_WE->f('Text'), 20) . ' (ID=' . $DB_WE->f('ID') . ')';
						}
					}
				} else {
					$templateText = '';
				}

				$_defined_fields = we_metadata_metaData::getDefinedMetaDataFields();
				$metafields = array();
				$_fieldcount = min(count($_defined_fields), 6);
				for($i = 0; $i < $_fieldcount; $i++){
					$_tagName = $_defined_fields[$i]["tag"];

					if(we_exim_contentProvider::isBinary($_result[$f]["docID"])){
						$DB_WE->query('SELECT a.ID,c.Dat FROM (' . FILE_TABLE . ' a LEFT JOIN ' . LINK_TABLE . ' b ON (a.ID=b.DID)) LEFT JOIN ' . CONTENT_TABLE . " c ON (b.CID=c.ID) WHERE b.DID=" . intval($_result[$f]["docID"]) . ' AND b.Name="' . escape_sql_query($_tagName) . '" AND b.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
						$metafields[$_tagName] = '';
						while($DB_WE->next_record()){
							$metafields[$_tagName] = we_base_util::shortenPath($DB_WE->f('Dat'), 45);
						}
					}
				}

				$content[] = array(
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . addTblPrefix($_result[$f]["docTable"]) . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none" class="middlefont" title="' . $_result[$f]["Text"] . '"><span class="iconGridview">' . $iconHTML['imageView'] . '<span></a>'),
					array("dat" => we_base_util::shortenPath($_result[$f]["SiteTitle"], 17)),
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . addTblPrefix($_result[$f]["docTable"]) . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . ' middlefont" title="' . ($whichSearch === self::SEARCH_MEDIA ? $_result[$f]["Path"] : $_result[$f]["Text"]) . '"><u>' . we_base_util::shortenPath($_result[$f]["Text"], 20) . '</u></a>'),
					array("dat" => '<nobr>' . ($_result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-") . '</nobr>'),
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . addTblPrefix($_result[$f]["docTable"]) . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none;" class="middlefont" title="' . $_result[$f]["Text"] . '"><span class="iconGridview">' . $iconHTML['imageViewPopup'] . '</span></a>'),
					array("dat" => $_result[$f]['fileSize']),
					array("dat" => $iconHTML['sizeX'] . " x " . $iconHTML['sizeY']),
					array("dat" => we_base_util::shortenPath(g_l('contentTypes', '[' . $_result[$f]['ContentType'] . ']'), 22)),
					array("dat" => '<span class="' . $fontColor . '">' . we_base_util::shortenPath($_result[$f]["Text"], 30) . '</span>'),
					array("dat" => we_base_util::shortenPath($_result[$f]["SiteTitle"], 45)),
					array("dat" => we_base_util::shortenPath($_result[$f]["Description"], 100)),
					array("dat" => $_result[$f]['ContentType']),
					array("dat" => we_base_util::shortenPath($creator, 22)),
					array("dat" => $templateText),
					array("dat" => $metafields),
					array("dat" => $_result[$f]['docID']),
					array("dat" => (isset($_result[$f]['media_alt']) ? $_result[$f]['media_alt'] : '')),
					array("dat" => (isset($_result[$f]['media_title']) ? $_result[$f]['media_title'] : '')),
					array("dat" => (isset($_result[$f]['isProtected']) ? $_result[$f]['isProtected'] : '')),
					array("dat" => (isset($_result[$f]['isUsed']) ? $_result[$f]['isUsed'] : '')),
				);
			}
		}

		return $content;
	}

	function makeAdditionalContentMedia($result){
		$usedMediaLinks = $this->searchclass->getUsedMediaLinks();
		$mediaLinks = $this->searchclass->getUsedMediaLinks();
		$accessibles = isset($mediaLinks['accessible']['mediaID_' . $result['docID']]) ? $mediaLinks['accessible']['mediaID_' . $result['docID']] : array();
		$notaccessibles = isset($mediaLinks['notaccessible']['mediaID_' . $result['docID']]) ? $mediaLinks['notaccessible']['mediaID_' . $result['docID']] : array();
		$groups = isset($mediaLinks['groups']['mediaID_' . $result['docID']]) ? $mediaLinks['groups']['mediaID_' . $result['docID']] : array();

		if(!empty($groups)){
			$out = '<table style="font-weight:normal; background-color:#eeeeee;width:480px"><tr><td colspan="2" style="padding:4px 0 0 6px;"><strong>' . g_l('searchtool', '[mediaRef][title]') . ':</stong></td></tr>'; // FIXME: G_L()
			foreach($groups as $group){
				$numNotaccessible = isset($notaccessibles[$group]) && is_array($notaccessibles[$group]) ? count($notaccessibles[$group]) : 0;
				$numAccessibles = isset($accessibles[$group]) && is_array($accessibles[$group]) ? count($accessibles[$group]) : 0;
				$out .= '<tr><td style="padding:4px 0 0 6px;"><em>' . $group . ' (' . ($numNotaccessible + $numAccessibles) . ($numNotaccessible ? ', davon ' . $numNotaccessible . ' ' . g_l('weClass', '[medialinks_unaccessible]') : '') . '):</em></td></tr>';

				$references = isset($accessibles[$group]) && is_array($accessibles[$group]) ? $accessibles[$group] : array();
				$limit = 20;
				$c = 0;

				foreach($references as $reference){
					if($c++ >= $limit){
						$out .= '<tr><td style="padding-left:26px;width:410px;">[ + ' . ($numAccessibles - $limit) . ' ' . g_l('weClass', '[medialinks_more]') . ' ]</td></tr>';
						break;
					}

					$color = 'black';
					$makeLink = true;
					// FIXME: establishing document state is buggy
					/*
					  switch($reference['referencedIn']){
					  case 'temp':
					  case 'both':
					  if($reference['isUnpublished']){
					  $color = 'red';
					  } else {
					  $color = '#3366cc';
					  }
					  break;
					  case 'main':
					  if($reference['isModified']){
					  $color = 'gray';
					  $makeLink = false;
					  } else if($reference['isUnpublished']){
					  $color = 'red';
					  }
					  }
					 *
					 */
					$element = preg_replace('|NN[0-9]\]+$|', 'NN', $reference['element']);
					$out .= '<tr style="background-color:white;">' .
						($makeLink ? '
							<td style="padding:8px 0 6px 26px;width:410px;"><a href="javascript:' . $reference['onclick'] . '" title="ID ' . $reference["id"] . ': ' . $reference['path'] . ($element ? ', in: ' . $reference['element'] : '') . '"><span style="color:' . $color . ';"><u>' . $reference['path'] . '</u></span></a>' . ($element ? '<br>' . 'in: ' . $element : '') . '</span></td>
							<td style="padding:6px 0 0 0">' . we_html_button::create_button(we_html_button::EDIT, "javascript:weSearch.openToEdit('" . $reference['table'] . "'," . $reference["id"] . ",'');", true, 27, 22) . '</td>' :
							'<td style="padding:8px 0 6px 26px;width:410px;"><span style="color:' . $color . ';">' . $reference['path'] . '</span></td>
							<td style="padding:6px 0 0 0">' . we_html_button::create_button(we_html_button::EDIT, '', true, 27, 22, '', '', true, false, '', false, g_l('searchtool', '[linkPublishedOnly]')) . '</td>') .
						'</tr>';
				}
			}
			$out .= '</table>';

			return $out;
		}
	}

	function getHtmlIconThmubnail($file, $smallSize = 64, $bigSize = 140, $view = 'list', $whichsearch = ''){
		$urlPopup = $url = '';
		if(!($view !== self::VIEW_ICONS && $whichsearch !== self::SEARCH_MEDIA) && $file["ContentType"] == we_base_ContentTypes::IMAGE){
			if($file["size"] > 0){
				$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $file["Path"]);
				$url = WEBEDITION_DIR . 'thumbnail.php?id=' . $file["docID"] . "&size[width]=" . $smallSize . "&path=" . urlencode($file["Path"]) . "&extension=" . $file["Extension"];
				$imageView = '<img src="' . $url . '" style="max-width:' . $smallSize . 'px;max-height:' . $smallSize . '"/></a>';

				$urlPopup = WEBEDITION_DIR . "thumbnail.php?id=" . $file["docID"] . "&size[width]=" . $bigSize . "&path=" . $file["Path"] . "&extension=" . $file["Extension"];
				$imageViewPopup = '<img src="' . $urlPopup . '" style="max-width:' . $smallSize . 'px;max-height:' . $smallSize . '"/></a>';
			} else {
				$imagesize = array(0, 0);
				$imageView = $imageViewPopup = '<span class="resultIcon" data-contenttype="' . $file["ContentType"] . '" data-extension="' . $file['Extension'] . '"></span>';
			}
		} else {
			$imagesize = array(0, 0);
			$imageView = $imageViewPopup = '<span class="resultIcon" data-contenttype="' . $file["ContentType"] . '" data-extension="' . $file['Extension'] . '"></span>';
		}

		return array('imageView' => $imageView, 'imageViewPopup' => $imageViewPopup, 'sizeX' => $imagesize[0], 'sizeY' => $imagesize[1], 'url' => $url, 'urlPopup' => $urlPopup);
	}

	function getSearchParameterTop($foundItems, $whichSearch){
		$currentOrder = $this->Model->getProperty('currentOrder');
		$currentSetView = $this->Model->getProperty('currentSetView');
		$currentSearchstart = $this->Model->getProperty('currentSearchstart');
		$currentAnzahl = $this->Model->getProperty('currentAnzahl');
		$currentFolderID = $this->Model->getProperty('currentFolderID');
		$currentSearchTables = $this->Model->getProperty('currentSearchTables');

		$select = we_html_tools::htmlSelect('anzahl' . $whichSearch, array(10 => 10, 25 => 25, 50 => 50, 100 => 100), 1, $currentAnzahl, false, array('onchange' => "this.form.elements['searchstart" . $whichSearch . "'].value=0;weSearch.search(false);"), 'value', 0, 'selectSearchNumber');

		$tbl = new we_html_table(array('class' => 'default', 'style' => 'margin:12px 0px 12px 19px;'), 1, 7);
		$tbl->setCol(0, ($c = 0), array('style' => 'font-size:12px; width:125px;'), g_l('searchtool', '[eintraege_pro_seite]') . ':');
		$tbl->setCol(0, ++$c, array('class' => 'defaultfont lowContrast', 'style' => 'width:60px;'), $select);
		$tbl->setCol(0, ++$c, array(), $this->getNextPrev($foundItems, $whichSearch));
		$tbl->setCol(0, ++$c, array(), we_html_button::create_button('fa:iconview,fa-lg fa-th', "javascript:weSearch.setView('" . we_search_view::VIEW_ICONS . "');", true, 40, '', '', '', false));
		$tbl->setCol(0, ++$c, array(), we_html_button::create_button('fa:listview,fa-lg fa-align-justify', "javascript:weSearch.setView('" . we_search_view::VIEW_LIST . "');", true, 40, '', '', '', false));

		$moreHiddens = '';
		if($whichSearch === self::SEARCH_DOCLIST){
			if($currentFolderID && $currentSearchTables[0] === FILE_TABLE){
				$tbl->setCol(0, ++$c, array('style' => 'width:50px;'), we_fileupload_ui_importer::getBtnImportFiles($currentFolderID));
			}
			$btnNewdir = we_html_button::create_button('fa:btn_new_dir,fa-plus,fa-lg fa-folder', "javascript:top.we_cmd('new_document','" . FILE_TABLE . "','','" . we_base_ContentTypes::FOLDER . "','','" . $currentFolderID . "')", true, 50, '', '', '', false);
			$tbl->setCol(0, ++$c, array('style' => 'width:50px;'), $btnNewdir);

			$moreHiddens = we_html_element::htmlHiddens(array(
					'we_transaction' => $this->Model->transaction,
			));
		}

		return we_html_element::htmlHiddens(array(
				'setView' . $whichSearch => $currentSetView,
				'Order' . $whichSearch => $currentOrder,
				'mode' => $_mode = $this->Model->mode,
				'searchstart' . $whichSearch => $currentSearchstart,
				'position' => '',
				'do' => '',
			)) . $moreHiddens . $tbl->getHtml();
	}

	function getSearchParameterBottom($foundItems, $whichSearch, $table = FILE_TABLE){
		$resetButton = (permissionhandler::hasPerm('RESET_VERSIONS') && $whichSearch === self::SEARCH_ADV ?
				we_html_button::create_button('reset', "javascript:.weSearch.resetVersions();", true, 100, 22, "", "") :
				'');

		$actionButton = $actionButtonCheckboxAll = '';
		switch($whichSearch){
			case self::SEARCH_ADV:
			case self::SEARCH_DOCS:
			case self::SEARCH_DOCLIST:
				if(permissionhandler::hasPerm('PUBLISH') && !($whichSearch === self::SEARCH_DOCLIST && $table === TEMPLATES_TABLE)){
					$actionButtonCheckboxAll = we_html_forms::checkbox(1, 0, "action_all_" . $whichSearch, "", false, "middlefont", "weSearch.checkAllActionChecks('" . $whichSearch . "')");
					$actionButton = we_html_button::create_button(we_html_button::PUBLISH, "javascript:weSearch.publishDocs('" . $whichSearch . "');", true, 100, 22, "", "");
					break;
				}
				$actionButton = $actionButtonCheckboxAll = '';
				break;
			case self::SEARCH_MEDIA:
				$actionButtonCheckboxAll = we_html_forms::checkbox(1, 0, "action_all_" . $whichSearch, "", false, "middlefont", "weSearch.checkAllActionChecks('" . $whichSearch . "')");
				$actionButton = we_html_button::create_button(we_html_button::DELETE, "javascript:weSearch.deleteMediaDocs('" . $whichSearch . "');", true, 100, 22, "", "");
				break;
		}

		$tbl = new we_html_table(array('class' => 'default', 'style' => 'margin-top:10px;'), 2, 4);
		$tbl->setCol(($k = 0), ($c = 0), array('style' => 'font-size:12px;width:12px;'), $actionButtonCheckboxAll);
		$tbl->setCol($k, ++$c, array('style' => 'font-size:12px;width:125px;'), $actionButton);
		$tbl->setCol($k, ++$c, array('class' => 'defaultfont lowContrast', 'style' => 'width:48px;height:30px;', 'id' => 'resetBusy' . $whichSearch), '');
		if(false && $resetButton){
			$tbl->setCol($k++, ++$c, array('style' => 'font-size:12px;'), $resetButton);
			$c = 0;
		}
		if(false && $whichSearch !== self::SEARCH_DOCLIST){
			$tbl->setCol(++$k, ($c = 0));
			$tbl->setCol($k, ++$c);
			$tbl->setCol($k, ++$c);
		}
		$tbl->setCol($k, ++$c, array(), $this::getNextPrev($foundItems, $whichSearch, false));

		return $tbl->getHtml();
	}

	// FIXME: will be  obsolete as soon as getSearchDialogOptionalFields() works properly
	function getSearchDialogOptionalFields($whichSearch){
		if($whichSearch !== self::SEARCH_ADV && $whichSearch !== self::SEARCH_MEDIA){
			return;
		}

		$currentSearchFields = $this->Model->getProperty('currentSearchFields');
		$currentSearch = $this->Model->getProperty('currentSearch');
		$currentLocation = $this->Model->getProperty('currentLocation');
		$this->searchclass->height = count($currentSearchFields);

		$out = '<div ' . ($whichSearch === self::SEARCH_MEDIA ? '' : 'style="margin-left:123px;"') . '>
	<div id="mouseOverDivs_' . $whichSearch . '"></div><table>
<tbody id="filterTable' . $whichSearch . '">
<tr>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
</tr>';

		for($i = ($whichSearch === self::SEARCH_MEDIA ? $this->searchMediaOptFieldIndex : 0); $i < $this->searchclass->height; $i++){
			$button = we_html_button::create_button(we_html_button::TRASH, 'javascript:weSearch.delRow(' . $i . ');', true, '', '', '', '', false);
			$locationDisabled = $handle = '';
			$searchInput = we_html_tools::htmlTextInput('search' . $whichSearch . '[' . $i . ']', 30, (isset($currentSearch[$i]) ? $currentSearch[$i] : ''), "", " class=\"wetextinput\"  id=\"search" . $whichSearch . "[" . $i . "]\" ", "search", 170);

			if(isset($currentSearchFields[$i])){
				switch($currentSearchFields[$i]){
					case 'ParentIDDoc':
					case 'ParentIDObj':
					case 'ParentIDTmpl':
					case 'Content':
					case 'Status':
					case 'Speicherart':
					case 'MasterTemplateID':
					case 'HasReferenceToID':
					case 'temp_template_id':
					case 'temp_category':
						$locationDisabled = 'disabled';
				}

				switch($currentSearchFields[$i]){
					case 'meta__Title':
					case 'meta__Description':
					case 'meta__Keywords':
					case 'meta__Autor':
					case 'meta__MIME':
					case 'ID':
					case 'Path':
					case 'CreatorName':
						break;
					case 'allModsIn':
						$searchInput = we_html_tools::htmlSelect('search' . $whichSearch . '[' . $i . ']', $this->searchclass->getModFields(), 1, (isset($currentSearch[$i]) ? $currentSearch[$i] : ''), false, array('class' => 'defaultfont', 'style' => 'width:170px;', 'id' => 'search' . $whichSearch . '[' . $i . ']'));
						break;
					case 'Status':
						$searchInput = we_html_tools::htmlSelect('search' . $whichSearch . '[' . $i . ']', $this->searchclass->getFieldsStatus(), 1, (isset($currentSearch[$i]) ? $currentSearch[$i] : ''), false, array('class' => 'defaultfont', 'style' => 'width:170px;', 'id' => 'search' . $whichSearch . '[' . $i . ']'));
						break;
					case 'Speicherart':
						$searchInput = we_html_tools::htmlSelect('search' . $whichSearch . '[' . $i . ']', $this->searchclass->getFieldsSpeicherart(), 1, (isset($currentSearch[$i]) ? $currentSearch[$i] : ''), false, array('class' => 'defaultfont', 'style' => 'width:170px;', 'id' => 'search' . $whichSearch . '[' . $i . ']'));
						break;
					case 'Published':
					case 'CreationDate':
					case 'ModDate':
						$handle = 'date';
						$searchInput = we_html_tools::getDateSelector('search' . $whichSearch . '[' . $i . ']', '_from' . $i, (isset($currentSearch[$i]) ? $currentSearch[$i] : ''), 170, 'multiicon');
						break;
					case 'ParentIDDoc':
					case 'ParentIDObj':
					case 'ParentIDTmpl':
						$_linkPath = (isset($currentSearch[$i]) ? $currentSearch[$i] : '');

						$_rootDirID = 0;
						$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['search" . $whichSearch . "ParentID[" . $i . "]'].value");
						$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['search" . $whichSearch . "[" . $i . "]'].value");
						$_cmd = "javascript:we_cmd('we_selector_directory',document.we_form.elements['search" . $whichSearch . "ParentID[" . $i . "]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $_rootDirID . "','','')";
						$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 60, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search' . $whichSearch . '[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('search' . $whichSearch . 'ParentID[' . $i . ']', ""), $_button);

						$searchInput = $selector;
						break;
					case 'HasReferenceToID':
						$_linkPath = (isset($currentSearch[$i]) ? $currentSearch[$i] : '');

						$_rootDirID = 0;
						$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['search" . $whichSearch . "ParentID[" . $i . "]'].value");
						$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['search" . $whichSearch . "[" . $i . "]'].value");

						$_button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['search" . $whichSearch . "ParentID[" . $i . "]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','','" . we_base_ContentTypes::IMAGE . "')", true, 60, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search' . $whichSearch . '[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('search' . $whichSearch . 'ParentID[' . $i . ']', ''), $_button);

						$searchInput = $selector;
						break;
					case 'MasterTemplateID':
					case 'temp_template_id':
						$_linkPath = (isset($currentSearch[$i]) ? $currentSearch[$i] : '');

						$_rootDirID = 0;
						$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['search" . $whichSearch . "ParentID[" . $i . "]'].value");
						$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['search" . $whichSearch . "[" . $i . "]'].value");

						$_button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['search" . $whichSearch . "ParentID[" . $i . "]'].value,'" . TEMPLATES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $_rootDirID . "','','" . we_base_ContentTypes::TEMPLATE . "')", true, 60, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search' . $whichSearch . '[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('search' . $whichSearch . 'ParentID[' . $i . ']', ''), $_button);

						$searchInput = $selector;
						break;
					case 'temp_category':
						$_linkPath = (isset($currentSearch[$i]) ? $currentSearch[$i] : '');
						$_rootDirID = 0;
						$_button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_category',document.we_form.elements['search" . $whichSearch . "ParentID[" . $i . "]'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'search" . $whichSearch . "ParentID[" . $i . "]\\'].value','document.we_form.elements[\\'search" . $whichSearch . "[" . $i . "]\\'].value','','','" . $_rootDirID . "','','')", true, 60, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search' . $whichSearch . '[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('search' . $whichSearch . 'ParentID[' . $i . ']', ''), $_button);

						$searchInput = $selector;
				}
			}

			$locationValue = (isset($currentLocation[$i]) ? $currentLocation[$i] : '');
			$out .= '<tr id="filterRow_' . $i . '">
	<td>' . we_html_tools::hidden('hidden_searchFields' . $whichSearch . '[' . $i . ']', isset($currentSearchFields[$i]) ? $currentSearchFields[$i] : '') .
				we_html_tools::htmlSelect('searchFields' . $whichSearch . '[' . $i . ']', $this->searchclass->getFields($i, $whichSearch), 1, (isset($currentSearchFields[$i]) ? $currentSearchFields[$i] : ''), false, array('class' => "defaultfont", 'id' => 'searchFields' . $whichSearch . '[' . $i . ']', 'onchange' => 'weSearch.changeit(this.value, ' . $i . ');')) .
				'</td>
	<td id="td_location' . $whichSearch . '[' . $i . ']">' . we_html_tools::htmlSelect('location' . $whichSearch . '[' . $i . ']', we_search_search::getLocation($handle), 1, $locationValue, false, array('class' => "defaultfont", 'style' => 'width:150px', $locationDisabled => $locationDisabled, 'id' => 'location' . $whichSearch . '[' . $i . ']')) . '</td>
	<td id="td_search' . $whichSearch . '[' . $i . ']">' . $searchInput . '</td>
	<td id="td_delButton[' . $i . ']">' . $button . '</td>
	<td id="td_hiddenLocation[' . $i . ']">' . (!$locationDisabled ? '' : we_html_element::htmlHidden('location' . $whichSearch . '[' . $i . ']', $locationValue)) . '</td>
	</tr>';
		}

		$out .= '</tbody></table>' .
			'<table>
<tr>
<td>' . we_html_button::create_button(we_html_button::ADD, "javascript:weSearch.newinput();") . '</td>
<td colspan="8" style="text-align:right"></td>
</tr>
</table></div>' .
			we_html_element::jsElement("weSearch.calendarSetup(" . $this->searchclass->height . ");");

		return $out;
	}

	function tblList($content, $headline, $whichSearch){
		$whichSearch = $whichSearch === 'doclist' ? self::SEARCH_DOCLIST : $whichSearch;
		$class = "middlefont";
		$currentSetView = $this->Model->getProperty('currentSetView');

		$anz = count($headline);
		$out = '<table style="table-layout:fixed;white-space:nowrap;width:100%;padding:0 0 0 0;margin:0 0 0 0;background-color:#fff;border-bottom:1px solid #D1D1D1;" >' .
			($whichSearch !== self::SEARCH_MEDIA ? '<colgroup>
<col style="width:30px;text-align:center;"/>
<col style="width:90px;text-align:left;"/>
<col style="width:28%;text-align:left;"/>
<col style="width:36%;text-align:left;"/>
<col style="width:15%;text-align:left;"/>
<col id="headerLast" style="width:18%;text-align:left;"/>
</colgroup>' : '<colgroup>
<col style="width:35px;text-align:center;"/>
<col style="width:80px;text-align:center;"/>
<col style="text-align:left;"/>
<col style="width:70px;text-align:left;"/>
<col style="width:55px;text-align:left;"/>
<col style="width:42px;text-align:left;"/>
<col style="width:50px;text-align:left;"/>
<col style="width:90px;text-align:left;"/>
<col style="width:90px;text-align:left;"/>
<col id="headerLast" style="width:80px;text-align:left;"/>
</colgroup>'
			) .
			'<tr style="height:20px;">
	<td>&nbsp;</td>
	<td>&nbsp;</td>';

		for($f = 0; $f < $anz; $f++){
			$out .= '<td  class="' . $class . '">' . $headline[$f]["dat"] . '</td>';
		}

		$out .= '</tr></table>' .
			//FIXME: realize with tbody?
			'<div class="largeicons" id="scrollContent_' . $whichSearch . '">' .
			$this->tabListContent($currentSetView, $content, $class, $whichSearch) .
			'</div>';

		return $out;
	}

	public function tabListContent($view = self::VIEW_LIST, $content = "", $class = "", $whichSearch = ""){
		$x = count($content);
		switch($view){
			default:
			case self::VIEW_LIST:
				$out = '<table style="border-spacing: 0 10px;table-layout:fixed;white-space:nowrap;border:0px;width:100%;padding:0 0 0 0;margin:0 0 0 0;">';
				$out .= $whichSearch !== self::SEARCH_MEDIA ? '
<colgroup>
<col style="width:30px;text-align:center;"/>
<col style="width:90px;text-align:left;"/>
<col style="width:28%;text-align:left;"/>
<col style="width:36%;text-align:left;"/>
<col style="width:15%;text-align:left;"/>
<col style="width:18%;text-align:left;"/>
</colgroup>' : '
<colgroup>
<col style="width:40px;text-align:center;"/>
<col style="width:80px;text-align:left;"/>
<col style="text-align:left;"/>
</colgroup>
';

				for($m = 0; $m < $x; $m++){
					$out .= '<tr>' . ($whichSearch === self::SEARCH_MEDIA ? self::tblListRowMedia($content[$m]) : self::tblListRow($content[$m])) . '</tr>';
				}
				$out .= '</tbody></table>' . '<div id="movethemaway" style="display:block"></div>';
				break;
			case self::VIEW_ICONS:
				$out = '<table class="default" style="width:100%"><tr><td style="text-align:center">';

				for($m = 0; $m < $x; $m++){
					$out .= $whichSearch !== self::SEARCH_MEDIA ? ('<div style="float:left;width:180px;height:100px;margin:20px 0px 0px 20px;z-index:1;">' .
						self::tblListRowIconView($content[$m], $class, $m, $whichSearch) . // FIXME: take this or tblListRowIconView()?
						'</div>') :
						('<div style="float:left;width:200px;height:200px;margin:20px 0px 0px 20px;z-index:1;">' .
						self::tblListRowMediaIconView($content[$m], $class, $m, $whichSearch) .
						'</div>');
				}

				$out .= '</td></tr></table>' . '<div id="movethemaway" style="display:block">' . self::makeMouseOverDivs($x, $content, $whichSearch) . '</div>';
			//we_html_element::jsElement("document.getElementById('mouseOverDivs_" . $whichSearch . "').innerHTML = '" . addslashes(self::makeMouseOverDivs($x, $content, $whichSearch)) . "';");
		}

		return $out .= $this->getNextPrev($this->searchclass->founditems, $whichSearch, true, true);
	}

	static function makeMouseOverDivs($x, $content, $whichSearch){
		$allDivs = '';

		for($n = 0; $n < $x; $n++){
			$outDivs = '<div class="largeicons" style="position:absolute;left:-9999px;width:400px;text-align:left;z-index:10000;visibility:visible;border:1px solid #bab9ba; border-radius:20px;background-color:#EDEDED;" class="middlefont" id="ImgDetails_' . $n . '_' . $whichSearch . '">
			<div style="margin-left:18px;margin-right:18px;height:22px;padding-top:3px;" class="weDocListSearchHeadlineDivs">' . $content[$n][10]["dat"] . '</div>
			<div style="width:100%;border-top:1px solid #DDDDDD;">
				<div style="padding:15px;display: inline-block;">' . $content[$n][5]["dat"] . '</div>

					<table style="font-size:10px;margin-left:150px;width:200px;display:inline-table;">
					<tr>
					<td colspan="2" style="font-size:12px;padding-bottom:2em;">' . $content[$n][9]["dat"] . '</td></tr>
					<tr><td style="vertical-align:top">' . g_l('searchtool', '[idDiv]') . ': </td><td>' . $content[$n][16]["dat"] . '</td></tr>
					<tr><td style="vertical-align:top">' . g_l('searchtool', '[dateityp]') . ': </td><td>' . $content[$n][8]["dat"] . '</td></tr>';
			switch($content[$n][12]["dat"]){
				case we_base_ContentTypes::IMAGE:
					if($whichSearch === self::SEARCH_MEDIA){
						$outDivs .= '<tr><td style="vertical-align:top">' . g_l('searchtool', '[protection]') . ': </td><td>' . ($content[$n][19]["dat"] ? g_l('global', '[true]') : g_l('global', '[false]')) . '</td></tr>';
						$outDivs .= '<tr><td style="vertical-align:top">' . g_l('weClass', '[isUsed]') . ': </td><td>' . ($content[$n][20]["dat"] ? g_l('global', '[true]') : g_l('global', '[false]')) . '</td></tr>';
					}
					$outDivs .= '<tr><td style="vertical-align:top">' . g_l('searchtool', '[groesse]') . ': </td><td>' . $content[$n][6]["dat"] . '</td></tr>';
					$outDivs .= '<tr><td style="vertical-align:top">' . g_l('searchtool', '[aufloesung]') . ': </td><td>' . $content[$n][7]["dat"] . '</td></tr>';
					if($whichSearch === self::SEARCH_MEDIA){
						$outDivs .= '<tr><td style="vertical-align:top">' . g_l('weClass', '[collection][attr_alt]') . ': </td><td>' . $content[$n][17]["dat"] . '</td></tr>';
						$outDivs .= '<tr><td style="vertical-align:top">' . g_l('weClass', '[collection][attr_title]') . ': </td><td>' . $content[$n][18]["dat"] . '</td></tr>';
					}
					break;
				case we_base_ContentTypes::AUDIO:
				case we_base_ContentTypes::VIDEO:
				case we_base_ContentTypes::FLASH:
				case we_base_ContentTypes::QUICKTIME:
				case we_base_ContentTypes::APPLICATION:
					if($whichSearch === self::SEARCH_MEDIA){
						$outDivs .= '<tr><td style="vertical-align:top">' . g_l('searchtool', '[protection]') . ': </td><td>' . ($content[$n][19]["dat"] ? g_l('global', '[true]') : g_l('global', '[false]')) . '</td></tr>';
						$outDivs .= '<tr><td style="vertical-align:top">' . g_l('weClass', '[isUsed]') . ': </td><td>' . ($content[$n][20]["dat"] ? g_l('global', '[true]') : g_l('global', '[false]')) . '</td></tr>';
					}
					$outDivs .= '<tr><td style="vertical-align:top">' . g_l('searchtool', '[groesse]') . ': </td><td>' . $content[$n][6]["dat"] . '</td></tr>';
					break;
				case we_base_ContentTypes::WEDOCUMENT:
					$outDivs .= '<tr><td style="vertical-align:top">' . g_l('searchtool', '[template]') . ': ' . '</td>
							<td>' . $content[$n][14]["dat"] . '</td></tr>';
					break;
			}
			$outDivs .= '<tr><td style="vertical-align:top">' . g_l('searchtool', '[creator]') . ': </td><td>' . $content[$n][13]["dat"] . '</td></tr>
					<tr><td style="vertical-align:top">' . g_l('searchtool', '[created]') . ': </td><td>' . $content[$n][3]["dat"] . '</td></tr>
					<tr><td style="vertical-align:top">' . g_l('searchtool', '[modified]') . ': </td><td>' . $content[$n][4]["dat"] . '</td></tr></table>

				<div style="padding:0px 0px 6px 15px;width:360px;">';
			if($content[$n][11]['dat']){
				$outDivs .= '<span style="font-size:10px;">' . g_l('searchtool', '[beschreibung]') . ': ' . we_base_util::shortenPath($content[$n][11]["dat"], 150) . '</span>';
			}
			$outDivs .= '</div>
			</div>';
			if($content[$n][15]["dat"]){
				$outDivs .= '
					<div style="height:20px;overflow:hidden;border-top:1px solid #DDDDDD;margin-top:15px;padding:5px 0px 0px 15px;">' . g_l('searchtool', '[metafelder]') . ':</div>
						<div style="background-color:#FFF;margin:10px 10px 10px 15px;">
						<table style="font-size:10px;">';
				foreach($content[$n][15]["dat"] as $k => $v){
					$outDivs .= '<tr><td>' . we_base_util::shortenPath($k, 90) . ':' . '</td><td>' . we_base_util::shortenPath($v, 90) . '</td></tr>';
				}
				$outDivs .= '</table>
					</div>';
			}

			$outDivs .= '
				</div>';
			$allDivs .= $outDivs;
		}

		return str_replace("\n", '', $allDivs);
	}

	private static function tblListRow($content, $class = "middlefont", $bgColor = ""){
		$anz = count($content);
		if(isset($content[0]["version"])){
			$anz = count($content) - 1;
		}

		$out = '';
		for($f = 0; $f < $anz; $f++){
			$out .= '<td ' . ($f < 2 ? '' : ' class="middlefont bold" style="height:30px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;"') . '>' . ((!empty($content[$f]["dat"])) ? $content[$f]["dat"] : "&nbsp;") . '</td>';
		}

		if(isset($content[0]["version"])){
			foreach(array_keys($content[0]["version"]) as $k){
				$out .= '</tr><tr><td style="width:20px;"></td>';
				for($y = 0; $y < $anz; $y++){
					$out .= '<td class="middlefont bold" style="' . ($f == 0 ? "width:30px;" : '') . '">' .
						$content[$y]["version"][$k] .
						'</td>';
				}

				$out .= '</tr><tr><td style="width:20px;"></td>';
				for($y = 0; $y < $anz; $y++){
					$out .= '<td class="middlefont bold" style="' . ($f == 0 ? "width:30px;" : '') . '">' . ($y == 2 ?
							$content[5]["version"][$k] . '<br/>' :
							''
						) . '</td>';
				}
			}
		}

		return $out;
	}

	private static function tblListRowMedia($content){
		$out = '';
		for($i = 0; $i < count($content); $i++){
			switch($content[$i]['elem']){
				case 'td':
					$out .= '<td ' . ($content[$i]['attribs'] ? $content[$i]['attribs'] : '') . '>' .
						(!isset($content[$i]['dat']) || !$content[$i]['dat'] ? '&nbsp;' : (!is_array($content[$i]["dat"]) ? $content[$i]["dat"] : self::tblListRowMedia($content[$i]["dat"]))) .
						'</td>';
					break;
				case 'table':
					// FIXME :this whole colgroup-stuff in dynamically built tables is absurde! throw out or generate dynamically too!
					$out .=!isset($content[$i]['dat']) || !is_array($content[$i]['dat']) ? '&nbsp;' : ('<table style="table-layout:fixed;white-space:nowrap;width:100%;padding:0 0 0 0;margin:0 0 0 0;background-color:#fff;" >' .
						(!empty($content[$i]['colgroup']) ? $content[$i]['colgroup'] : '') .
						self::tblListRowMedia($content[$i]["dat"]) .
						'</table>');
					break;
				case 'row':
					$out .= '<tr style="width:100%" ' . (isset($content[$i]['attribs']) ? $content[$i]['attribs'] : '') . '>' .
						(!isset($content[$i]['dat']) || !is_array($content[$i]['dat']) ? '<td>&nbsp;</td>' : self::tblListRowMedia($content[$i]["dat"])) .
						'</tr>';
					break;
			}
		}

		return $out;
	}

	private static function tblListRowIconView($content, $class, $i, $whichSearch){
		return '<table style="width:100%" class="default ' . $class . '">
<tr>
	<td style="width:75px;vertical-align:top;text-align:center" onmouseover="weSearch.showImageDetails(\'ImgDetails_' . $i . '_' . $whichSearch . '\',1)" onmouseout="weSearch.hideImageDetails(\'ImgDetails_' . $i . '_' . $whichSearch . '\')">' .
			((!empty($content[0]["dat"])) ? $content[0]["dat"] : "&nbsp;") . '</td>
		<td style="width:105px;vertical-align:top;line-height:20px;">
		<div style="padding-bottom:2em;">' . ((!empty($content[2]["dat"])) ? $content[2]["dat"] : "&nbsp;") . '</div>
		<span>' . ((!empty($content[1]["dat"])) ? $content[1]["dat"] : "&nbsp;") . '</span></td>
</tr></table>';
	}

	private static function tblListRowMediaIconView($content, $class, $i, $whichSearch){
		return '<table style="width:100%" class="default ' . $class . '">
<tr>
	<td style="width:100%;vertical-align:top;text-align:center" onmouseover="weSearch.showImageDetails(\'ImgDetails_' . $i . '_' . $whichSearch . '\',1)" onmouseout="weSearch.hideImageDetails(\'ImgDetails_' . $i . '_' . $whichSearch . '\')">' .
			((!empty($content[5]["dat"])) ? $content[5]["dat"] : "&nbsp;") .
			'</td>
</tr>
<tr>
		<td style="width:100%;vertical-align:top;line-height:20px;text-align:center">
		<span>' . ((!empty($content[2]["dat"])) ? $content[2]["dat"] : "&nbsp;") . '</span>
</tr></table>';
	}

	function getDirSelector($whichSearch){
		$yuiSuggest = & weSuggest::getInstance();
		switch($whichSearch){
			case self::SEARCH_DOCS :
				$nameFolderID = "folderIDDoc";
				$nameFolderPath = "folderPathDoc";
				$table = FILE_TABLE;
				$ACname = "docu";
				break;
			case self::SEARCH_MEDIA :
				$nameFolderID = "folderIDMedia";
				$nameFolderPath = "folderPathMedia";
				$table = FILE_TABLE;
				$ACname = "docu";
				break;
			case self::SEARCH_TMPL :
				$nameFolderID = "folderIDTmpl";
				$nameFolderPath = "folderPathTmpl";
				$table = TEMPLATES_TABLE;
				$ACname = "Tmpl";
				break;
		}
		$yuiSuggest->setWidth(380);
		$currentFolderID = $this->Model->getProperty('currentFolderID');
		$_path = id_to_path($currentFolderID, $table, $this->db);


		$yuiSuggest->setAcId($ACname);
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($nameFolderPath, $_path);
		$yuiSuggest->setLabel("");
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($nameFolderID, $nameFolderPath);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable($table);
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $nameFolderID . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $nameFolderPath . "'].value");
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements['" . $nameFolderID . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "')"));

		return
			weSuggest::getYuiFiles() .
			$yuiSuggest->getHTML() .
			$yuiSuggest->getYuiJs();
	}

	function getCommonHiddens($cmds = array()){
		return we_html_element::htmlHiddens(array(
				'cmd' => (isset($cmds['cmd']) ? $cmds['cmd'] : ''),
				'cmdid' => (isset($cmds['cmdid']) ? $cmds['cmdid'] : ''),
				'pnt' => (isset($cmds['pnt']) ? $cmds['pnt'] : ''),
				'tabnr' => (isset($cmds['tabnr']) ? $cmds['tabnr'] : ''),
				'vernr' => (isset($cmds['vernr']) ? $cmds['vernr'] : 0),
				'delayCmd' => (isset($cmds['delayCmd']) ? $cmds['delayCmd'] : ''),
				'delayParam' => (isset($cmds['delayParam']) ? $cmds['delayParam'] : '')
		));
	}

	function getJSProperty(){
		return we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
			we_html_element::jsElement('
var loaded=0;
function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(this, url,"we_docselector",-1,-1,WE().consts.size.docSelect.width,WE().consts.size.docSelect.height,true,true,true,true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(this, url,"we_selector",-1,-1,WE().consts.size.windowSelect.width,WE().consts.size.windowSelect.height,true,true,true,true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(this, url,"we_selector",-1,-1,WE().consts.size.windowDirSelect.width,WE().consts.size.windowDirSelect.height,true,true,true,true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(this, url,"we_catselector",-1,-1,WE().consts.size.catSelect.width,WE().consts.size.catSelect.height,true,true,true,true);
			break;
		case "openweSearchDirselector":
			url = WE().consts.dirs.WEBEDITION_DIR+"apps/weSearch/we_weSearchDirSelect.php?";
			for(var i = 0; i < args.length; i++){
				url += "we_cmd[]="+encodeURI(args[i]);
				if(i < (args.length - 1)){
				url += "&";
				}
			}
			new (WE().util.jsWindow)(this, url,"we_weSearch_dirselector",-1,-1,600,400,true,true,true);
			break;
		default:
			top.content.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}
function submitForm(target,action,method) {
	var f = self.document.we_form;
	f.target = (target?target:"edbody");
	f.action = (action?action:"' . $this->frameset . '");
	f.method = (method?method:"post");
	f.submit();
}');
	}

	function processVariables(){
		if(isset($_SESSION['weS'][$this->toolName . '_session'])){
			$this->Model = $_SESSION['weS'][$this->toolName . '_session'];
		}

		$tmp = '';
		$modelVars = array_merge(array(// some vars are not persistent in db but must be written to session anyway
			'searchstartDocSearch',
			'searchstartTmplSearch',
			'searchstartMediaSearch',
			'searchstartAdvSearch'), (is_array($this->Model->persistent_slots) ? $this->Model->persistent_slots : array()));

		$doInitModelByHttp = false;
		foreach($modelVars as $val){
			if(we_base_request::_(we_base_request::RAW, $val, we_base_request::NOT_VALID) !== we_base_request::NOT_VALID){
				$doInitModelByHttp = true;
				break;
			}
		}

		if($doInitModelByHttp && ($tab = we_base_request::_(we_base_request::INT, 'tab', we_base_request::_(we_base_request::INT, 'tabnr', 1)))){
			switch($tab){
				case 1:
					$whichSearch = self::SEARCH_DOCS;
					break;
				case 2:
					$whichSearch = self::SEARCH_TMPL;
					break;
				case 3:
					$whichSearch = self::SEARCH_ADV;
					break;
				case 5:
					$whichSearch = self::SEARCH_MEDIA;
					break;
				default:
					$whichSearch = false;
			}

			$this->Model->initByHttp($whichSearch, false);
		}
	}

}
