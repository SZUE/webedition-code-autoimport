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

	var $Model;
	var $toolName;
	var $db;
	var $frameset;
	var $topFrame;
	var $editorBodyFrame;
	var $editorHeaderFrame;
	var $editorFooterFrame;
	var $icon_pattern = '';
	var $page = 1;
	var $searchclass;
	var $searchclassExp;
	private $searchMediaOptFieldIndex = 0;

	//private $view = self::VIEW_LIST;

	public function __construct($frameset = '', $topframe = 'top'){
		parent::__construct($frameset, $topframe);
		$this->editorBodyFrame = $this->topFrame . '.editor.edbody';
		$this->editorHeaderFrame = $this->topFrame . '.editor.edheader';
		$this->editorFooterFrame = $this->topFrame . '.editor.edfooter';

		$this->toolName = 'weSearch';
		$this->Model = new we_search_model();
		$this->yuiSuggest = & weSuggest::getInstance();
		$this->searchclass = new we_search_search();
		$this->searchclassExp = new we_search_exp();
	}

	function getJSTop(){
		return we_html_element::jsElement('
var activ_tab = 1;
var hot = 0;

function we_cmd() {
 var args = [];
 var url = WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?";
	 for(var i = 0; i < arguments.length; i++){
					 args.push(arguments[i]);

	 url += "we_cmd["+i+"]="+encodeURI(arguments[i]);
	 if(i < (arguments.length - 1)){
		url += "&";
	}
	 }

 if(' . $this->topFrame . '.hot){
	 switch(args[0]){
	 case "tool_weSearch_edit":
	 case "tool_weSearch_new":
	 case "tool_weSearch_new_group":
	 case "tool_weSearch_exit":
	' . $this->editorBodyFrame . '.document.we_form.delayCmd.value = args[0];
	' . $this->editorBodyFrame . '.document.we_form.delayParam.value = args[1];
	args[0] = "exit_doc_question";
	}
 }
 switch (args[0]) {
	case "tool_weSearch_edit":
	 if(' . $this->editorBodyFrame . '.loaded) {
		' . $this->editorBodyFrame . '.document.we_form.cmd.value = args[0];
		' . $this->editorBodyFrame . '.document.we_form.cmdid.value=args[1];
		' . $this->editorBodyFrame . '.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
		' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
		' . $this->editorBodyFrame . '.submitForm();
	 } else {
		setTimeout(function(){we_cmd("tool_weSearch_edit",\'+args[1]+\');}, 10);
	 }
	break;
	case "tool_weSearch_new":
	case "tool_weSearch_new_group":
	 if(' . $this->editorBodyFrame . '.loaded) {
		' . $this->topFrame . '.hot = 0;
		' . $this->editorBodyFrame . '.document.we_form.cmd.value = args[0];
		' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
		' . $this->editorBodyFrame . '.document.we_form.tabnr.value = 1;
		' . $this->editorBodyFrame . '.submitForm();
	 } else {
		setTimeout(function(){we_cmd("tool_weSearch_new");}, 10);
	 }
	 if(treeData){
		treeData.unselectNode();
	 }
	break;

	case "tool_weSearch_exit":
	 top.close();
	break;
	case "exit_doc_question":
	 url = "' . $this->frameset . '?pnt=exit_doc_question&delayCmd="+' . $this->editorBodyFrame . '.document.getElementsByName("delayCmd")[0].value+"&delayParam="+' . $this->editorBodyFrame . '.document.getElementsByName("delayParam")[0].value;
	 new (WE().util.jsWindow)(window, url,"we_exit_doc_question",-1,-1,380,130,true,false,true);
	break;
	case "tool_weSearch_save":
	if(' . $this->editorBodyFrame . '.document.we_form.predefined.value==1) {
	 ' . we_message_reporting::getShowMessageCall(
					g_l('searchtool', '[predefinedSearchmodify]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	 break;
	}else if (' . $this->editorBodyFrame . '.loaded) {
	 if(' . $this->editorBodyFrame . '.document.we_form.newone.value==1) {
		var name = prompt("' . g_l('searchtool', '[nameForSearch]') . '", "");
		if (name == null) {
		 break;
		} else {
		 ' . $this->editorBodyFrame . '.document.we_form.savedSearchName.value=name;
		}
	 }
	 ' . $this->editorBodyFrame . '.document.we_form.cmd.value=arguments[0];
 //' . $this->editorBodyFrame . '.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
	 ' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
	 ' . $this->editorBodyFrame . '.submitForm();
	}else {
	 ' . we_message_reporting::getShowMessageCall(g_l('tools', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}

	break;

 case "tool_weSearch_delete":
	if(' . $this->editorBodyFrame . '.document.we_form.predefined.value==1) {' .
				we_message_reporting::getShowMessageCall(g_l('searchtool', '[predefinedSearchdelete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	 return;
	}
	if(' . $this->topFrame . '.editor.edbody.document.we_form.newone.value==1){
	 ' . we_message_reporting::getShowMessageCall(g_l('tools', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	 return;
	}
	' . (!permissionhandler::hasPerm("DELETE_" . strtoupper($this->toolName)) ? (
					we_message_reporting::getShowMessageCall(g_l('tools', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)) : ('
			if (' . $this->topFrame . '.editor.edbody.loaded) {

			 if (confirm("' . g_l('searchtool', '[confirmDel]') . '")) {
				' . $this->topFrame . '.editor.edbody.document.we_form.cmd.value=arguments[0];
				' . $this->topFrame . '.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
				' . $this->editorHeaderFrame . '.location="' . $this->frameset . '?home=0&pnt=edheader";
				' . $this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?home=0&pnt=edfooter";
				' . $this->topFrame . '.editor.edbody.submitForm();

			 }
			} else {' .
					we_message_reporting::getShowMessageCall(g_l('tools', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
		')) . '
				break;

 case "tool_weSearch_new_forDocuments":
	 if (' . $this->editorBodyFrame . '.loaded) {
		 ' . $this->topFrame . '.hot = 0;
		 ' . $this->editorBodyFrame . '.document.we_form.cmd.value=arguments[0];
		 ' . $this->topFrame . '.activ_tab=1;
		 ' . $this->editorBodyFrame . '.document.we_form.tabnr.value=1;
		 ' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
		 ' . $this->editorBodyFrame . '.submitForm();
	 } else {
		 setTimeout(function(){we_cmd("tool_weSearch_new_forDocuments");}, 10);
	 }
	 if(treeData){
		 treeData.unselectNode();
	 }
	 break;

 case "tool_weSearch_new_forTemplates":
	 if (' . $this->editorBodyFrame . '.loaded) {
		 ' . $this->topFrame . '.hot = 0;
		 ' . $this->topFrame . '.activ_tab=2;
		 ' . $this->editorBodyFrame . '.document.we_form.cmd.value=arguments[0];
		 ' . $this->editorBodyFrame . '.document.we_form.tabnr.value=2;
		 ' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
		 ' . $this->editorBodyFrame . '.submitForm();
	 } else {
		 setTimeout(function(){we_cmd("tool_weSearch_new_forTemplates");}, 10);
	}
	 if(treeData){
	 treeData.unselectNode();
	 }
	 break;

 case "tool_weSearch_new_forObjects":
	if (' . $this->editorBodyFrame . '.loaded) {
	 ' . $this->topFrame . '.hot = 0;
	 ' . $this->topFrame . '.activ_tab=3;
			' . $this->editorBodyFrame . '.document.we_form.cmd.value=arguments[0];
				 ' . $this->editorBodyFrame . '.document.we_form.tabnr.value=3;
				 ' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
				 ' . $this->editorBodyFrame . '.submitForm();
		 } else {
	 setTimeout(function(){we_cmd("tool_weSearch_new_forObjects");}, 10);
	}
		 if(treeData){
	 treeData.unselectNode();
	}
		 break;


			 case "tool_weSearch_new_advSearch":
	if (' . $this->editorBodyFrame . '.loaded) {
	 ' . $this->topFrame . '.hot = 0;
	 ' . $this->topFrame . '.activ_tab=3;
			' . $this->editorBodyFrame . '.document.we_form.cmd.value=arguments[0];
				 ' . $this->editorBodyFrame . '.document.we_form.tabnr.value=3;
				 ' . $this->editorBodyFrame . '.document.we_form.pnt.value="edbody";
				 ' . $this->editorBodyFrame . '.submitForm();
		 } else {
	 setTimeout(function(){we_cmd("tool_weSearch_new_advSearch");}, 10);
	}
	 if(treeData){
		 treeData.unselectNode();
	 }
	 break;
	default:
	 top.opener.top.we_cmd.apply(this, args);

 }
}

function mark() {
 hot=1;
 ' . $this->editorHeaderFrame . '.mark();
}');
	}

	function processCommands(){
		$cmdid = we_base_request::_(we_base_request::INT, 'cmdid');
		switch(($cmd = we_base_request::_(we_base_request::STRING, 'cmd', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)))){
			case 'tool_weSearch_new' :
			case 'tool_weSearch_new_forDocuments' :
			case 'tool_weSearch_new_forTemplates' :
			case 'tool_weSearch_new_forObjects' :
			case 'tool_weSearch_new_advSearch' :
				//case 'tool_weSearch_new_group' :
				$this->Model = new we_search_model();
				$this->Model->setIsFolder(/* $cmd == 'tool_weSearch_new_group' ? 1 : */ 0);
				$tab = we_base_request::_(we_base_request::INT, 'tabnr');

				echo we_html_element::jsElement(
					$this->editorHeaderFrame . '.location="' . $this->frameset . '?pnt=edheader' .
					($tab !== false ? '&tab=' . $tab : '') .
					'&text=' . urlencode($this->Model->Text) . '";' .
					$this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";');
				break;

			case 'tool_weSearch_edit' :
				$this->Model = new we_search_model($cmdid);
				if(!$this->Model->isAllowedForUser()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('tools', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					$this->Model = new we_search_model();
					$_REQUEST['home'] = true;
					break;
				}
				echo we_html_element::jsElement(
					$this->editorHeaderFrame . '.location="' . $this->frameset . '?pnt=edheader' .
					($cmdid !== false ? '&cmdid=' . $cmdid : '') . '&text=' .
					urlencode($this->Model->Text) . '";' .
					$this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
        if(' . $this->topFrame . '.treeData){
         ' . $this->topFrame . '.treeData.unselectNode();
         ' . $this->topFrame . '.treeData.selectNode("' . $this->Model->ID . '");
        }
     ');
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

				$this->Model->activTab = we_base_request::_(we_base_request::INT, 'tabnr', 1);

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
							we_message_reporting::getShowMessageCall(
								g_l('searchtool', ($this->Model->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) .
							$this->topFrame . '.hot=0;'
					);

					if(we_base_request::_(we_base_request::BOOL, 'delayCmd')){
						$js .= we_html_element::jsElement(
								$this->topFrame . '.we_cmd("' . we_base_request::_(we_base_request::STRING, 'delayCmd') . '"' . (($dp = we_base_request::_(we_base_request::RAW, 'delayParam')) ? ',"' . $dp . '"' : '') . ');'
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
        setTimeout(function(){' . we_message_reporting::getShowMessageCall(
							g_l('tools', ($this->Model->IsFolder == 1 ? '[group_deleted]' : '[item_deleted]')), we_message_reporting::WE_MESSAGE_NOTICE) . '},500);' .
						$this->topFrame . '.we_cmd("tool_weSearch_edit");'
					);
					$this->Model = new we_search_model();
					$_REQUEST['pnt'] = 'edbody';
				}
				break;

			default :
		}

		$_SESSION["weSearch_session"] = $this->Model;
	}

	function getSearchJS($whichSearch){
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

		switch($whichSearch){
			case self::SEARCH_DOCS :
				$anzahl = $this->Model->anzahlDocSearch;
				break;
			case self::SEARCH_TMPL :
				$anzahl = $this->Model->anzahlTmplSearch;
				break;
			case self::SEARCH_MEDIA :
				$anzahl = $this->Model->anzahlMediaSearch;
				break;
			case self::SEARCH_ADV :
				$anzahl = $this->Model->anzahlAdvSearch;
				break;
			default:
				$anzahl = 0;
		}

		$tab = we_base_request::_(we_base_request::INT, 'tab', we_base_request::_(we_base_request::INT, 'tabnr', 1));

		$showSelects = '';
		return we_html_element::jsScript(JS_DIR . 'we_modules/search/search_view.js') .
			we_html_element::jsElement('
WE().consts.weSearch= {
	SEARCH_DOCS: "' . self::SEARCH_DOCS . '",
	SEARCH_TMPL: "' . self::SEARCH_TMPL . '",
	SEARCH_MEDIA: "' . self::SEARCH_MEDIA . '",
	SEARCH_ADV: "' . self::SEARCH_ADV . '"
};
weSearch.conf = {
	whichsearch: "' . $whichSearch . '",
	editorBodyFrame : ' . $this->editorBodyFrame . ',
	ajaxURL: WE().consts.dirs.WEBEDITION_DIR+"rpc/rpc.php",
	tab: "' . $tab . '",
	modelClassName: "' . $this->Model->ModelClassName . '",
	modelID: "' . $this->Model->ID . '",
	modelIsFolder: ' . ($this->Model->IsFolder ? 1 : 0) . ',
	showSelects: ' . ($showSelects ? 1 : 0) . ',
	rows: ' . ((isset($_REQUEST["searchFields" . $whichSearch]) ? count($_REQUEST["searchFields" . $whichSearch]) - ($whichSearch == self::SEARCH_ADV ? 1 : 0) : ($whichSearch == self::SEARCH_MEDIA ? $this->searchMediaOptFieldIndex : 0))) . ',
	we_transaction: "' . $GLOBALS["we_transaction"] . '",
	checkRightTempTable: ' . (we_search_search::checkRightTempTable() ? 1 : 0) . ',
	checkRightDropTable: ' . (we_search_search::checkRightDropTable() ? 1 : 0) . '
};
weSearch.elems = {
	btnTrash: \'' . str_replace("'", "\'", we_html_button::create_button(we_html_button::TRASH, "javascript:weSearch.delRow(__we_new_id__)")) . '\',
	btnSelector: \'' . str_replace("'", "\'", we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('__we_selector__', document.we_form.elements['search" . $whichSearch . "ParentID[__we_new_id__]'].value, '__we_sel_table__', 'document.we_form.elements[\\\'search" . $whichSearch . "ParentID[__we_new_id__]\\\'].value', 'document.we_form.elements[\\\'search" . $whichSearch . "[__we_new_id__]\\\'].value');")) . '\',
	fieldSearch: \'' . str_replace("'", "\'", we_html_tools::htmlTextInput('search' . $whichSearch . '[__we_new_id__]', 58, '', '', ' __we_read_only__class="wetextinput" id="search' . $whichSearch . '[__we_new_id__]"', 'text', 170)) . '\',
	selStatus: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . $whichSearch . '[__we_new_id__]', $this->searchclass->getFieldsStatus(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . $whichSearch . "[__we_new_id__]"))) . '\',
	selSpeicherart: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . $whichSearch . '[__we_new_id__]', $this->searchclass->getFieldsSpeicherart(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . $whichSearch . "[__we_new_id__]"))) . '\',
	selLocation: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('location' . $whichSearch . '[__we_new_id__]', we_search_search::getLocation(), 1, "", false, array('class' => "defaultfont", 'id' => "location" . $whichSearch . "[__we_new_id__]"))) . '\',
	selModFields: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . $whichSearch . '[__we_new_id__]', $this->searchclass->getModFields(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . $whichSearch . "[__we_new_id__]"))) . '\',
	selUsers: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('search' . $whichSearch . '[__we_new_id__]', $this->searchclass->getUsers(), 1, "", false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => "search" . $whichSearch . "[__we_new_id__]"))) . '\',
	pixel: \'' . str_replace("'", "\'", we_html_tools::getPixel(5, 4)) . '\',
	searchFields: \'' . str_replace("'", "\'", we_html_tools::htmlSelect('searchFields' . $whichSearch . '[__we_new_id__]', $this->searchclass->getFields("__we_new_id__", $whichSearch), 1, "", false, array('class' => "defaultfont", 'id' => "searchFields" . $whichSearch . "[__we_new_id__]", 'onchange' => "weSearch.changeit(this.value, __we_new_id__);"))) . '\'
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

	function getNextPrev($we_search_anzahl, $whichSearch, $isTop = true){
		$anzahl = 1;
		$searchstart = 0;

		if(isset($GLOBALS['we_cmd_obj'])){
			$anzahl = $_SESSION['weS']['weSearch']['anzahl' . $whichSearch];
			$searchstart = $_SESSION['weS']['weSearch']['searchstart' . $whichSearch];
		} else {
			switch($whichSearch){
				case self::SEARCH_DOCS :
					$anzahl = $this->Model->anzahlDocSearch;
					$searchstart = $this->Model->searchstartDocSearch;
					break;
				case self::SEARCH_TMPL :
					$anzahl = $this->Model->anzahlTmplSearch;
					$searchstart = $this->Model->searchstartTmplSearch;
					break;
				case self::SEARCH_MEDIA :
					$anzahl = $this->Model->anzahlMediaSearch;
					$searchstart = $this->Model->searchstartMediaSearch;
					break;
				case self::SEARCH_ADV :
					$anzahl = $this->Model->anzahlAdvSearch;
					$searchstart = $this->Model->searchstartAdvSearch;
					break;
			}
			if($this->Model->IsFolder){
				$anzahl = 1;
			}
		}

		$out = '<table class="default"><tr><td>' .
			($searchstart ?
				we_html_button::create_button(we_html_button::BACK, "javascript:weSearch.back(" . $anzahl . ");") :
				we_html_button::create_button(we_html_button::BACK, "", true, 100, 22, "", "", true)
			) .
			'</td><td width="10"></td><td class="defaultfont"><b>' . (($we_search_anzahl) ? $searchstart + 1 : 0) . '-' .
			(($we_search_anzahl - $searchstart) < $anzahl ?
				$we_search_anzahl :
				$searchstart + $anzahl
			) .
			' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl . '</b></td><td width="10"></td><td>' .
			(($searchstart + $anzahl) < $we_search_anzahl ?
				//bt_back
				we_html_button::create_button(we_html_button::NEXT, "javascript:weSearch.next(" . $anzahl . ");") :
				we_html_button::create_button(we_html_button::NEXT, "", true, 100, 22, "", "", true)
			) .
			'</td><td width="10"></td><td>';

		$pages = array();
		if($anzahl){
			for($i = 0; $i < ceil($we_search_anzahl / $anzahl); $i++){
				$pages[($i * $anzahl)] = ($i + 1);
			}
		}

		$page = $anzahl ? ceil($searchstart / $anzahl) * $anzahl : 0;

		$select = we_html_tools::htmlSelect("page", $pages, 1, $page, false, array("onchange" => "this.form.elements['searchstart" . $whichSearch . "'].value = this.value;search(false);"));
		if(!isset($GLOBALS['setInputSearchstart']) && !defined('searchstart' . $whichSearch) && $isTop){
			define('searchstart' . $whichSearch, true);
			$out .= we_html_tools::hidden("searchstart" . $whichSearch, $searchstart);
		}
		$out .= $select .
			'</td></tr></table>';
		return $out;
	}

	function getSortImage($for, $whichSearch){
		$ord = we_base_request::_(we_base_request::STRING, 'Order' . $whichSearch);
		if($ord){
			if(strpos($ord, $for) === 0){
				if(strpos($ord, 'DESC')){
					return '<i class="fa fa-sort-desc fa-lg"></i>';
				}
				return '<i class="fa fa-sort-asc fa-lg"></i>';
			}
		}
		return '<i class="fa fa-sort fa-lg"></i>';
	}

	function getSearchDialogOptions($whichSearch){

		$_table = new we_html_table(array('style' => 'width:500px',), 3, 2);
		$row = 0;
		switch($whichSearch){
			case self::SEARCH_DOCS :
				$_table->setCol($row++, 0, array(), we_html_forms::checkboxWithHidden($this->Model->searchForTextDocSearch ? true : false, "searchForTextDocSearch", g_l('searchtool', '[onlyFilename]'), false, 'defaultfont', ''));
				$_table->setCol($row++, 0, array(), we_html_forms::checkboxWithHidden($this->Model->searchForTitleDocSearch ? true : false, "searchForTitleDocSearch", g_l('searchtool', '[onlyTitle]'), false, 'defaultfont', ''));
				$_table->setCol($row++, 0, array(), we_html_forms::checkboxWithHidden($this->Model->searchForContentDocSearch ? true : false, "searchForContentDocSearch", g_l('searchtool', '[Content]'), false, 'defaultfont', ''));
				break;
			case self::SEARCH_TMPL :
				$_table->setCol($row++, 0, array(), we_html_forms::checkboxWithHidden($this->Model->searchForTextTmplSearch ? true : false, "searchForTextTmplSearch", g_l('searchtool', '[onlyFilename]'), false, 'defaultfont', ''));
				$_table->setCol($row++, 0, array(), we_html_forms::checkboxWithHidden($this->Model->searchForContentTmplSearch ? true : false, "searchForContentTmplSearch", g_l('searchtool', '[Content]'), false, 'defaultfont', ''));
				break;
			case self::SEARCH_MEDIA :
				//$_table->setCol($row++, 0, array('style' => 'padding-top: 10px'), we_html_tools::htmlAlertAttentionBox('Ohne Suchbegriff werden alle Medien-Dokumente ausgegeben.', we_html_tools::TYPE_INFO, 440));

				$_table->setCol($row++, 0, array(), we_html_forms::checkboxWithHidden($this->Model->searchForTextMediaSearch ? true : false, "searchForTextMediaSearch", g_l('searchtool', '[onlyFilename]'), false, 'defaultfont', ''));
				$_table->setCol($row++, 0, array(), we_html_forms::checkboxWithHidden($this->Model->searchForTitleMediaSearch ? true : false, "searchForTitleMediaSearch", g_l('searchtool', '[onlyTitle]'), false, 'defaultfont', ''));
				$_table->setCol($row++, 0, array(), we_html_forms::checkboxWithHidden($this->Model->searchForMetaMediaSearch ? true : false, "searchForMetaMediaSearch", 'In Metadaten', false, 'defaultfont', '')); //FIXME: G_L()
				//$_table->setCol($row++, 1, array('style' => 'text-align:right'), we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);"));

				return $_table->getHtml();
		}
		$_table->setCol($row, 0, array('style' => 'padding-right:20px;'), we_html_tools::getPixel(380, 10));
		$_table->setCol($row++, 1, array('style' => 'text-align:right'), we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);"));

		return $_table->getHtml();
	}

	function getSearchDialogMediaType($whichSearch){
		$_table = new we_html_table(array('style' => 'width:400px',), 7, 2);
		switch($whichSearch){
			case self::SEARCH_MEDIA :
				/*
				 * FIXME: add meta tags using advsearch gui elements! (they are AND-connected)
				 */
				$n = 1;
				$_table->setCol(0, 0, array(), we_html_element::htmlHiddens(array(
						'searchFieldsMediaSearch[' . $n . ']' => 'ContentType',
						'searchMediaSearch[' . $n . ']' => 1,
						'locationMediaSearch[' . $n++ . ']' => 'IN')) .
					we_html_forms::checkboxWithHidden($this->Model->searchForImageMediaSearch ? true : false, "searchForImageMediaSearch", 'Bilder', false, 'defaultfont withSpace', ''));
				$_table->setCol(0, 1, array(), we_html_forms::checkboxWithHidden($this->Model->searchForAudioMediaSearch ? true : false, "searchForAudioMediaSearch", 'Audio', false, 'defaultfont', ''));
				$_table->setCol(1, 1, array(), we_html_forms::checkboxWithHidden($this->Model->searchForVideoMediaSearch ? true : false, "searchForVideoMediaSearch", 'Video', false, 'defaultfont', ''));
				$_table->setCol(1, 0, array(), we_html_forms::checkboxWithHidden($this->Model->searchForPdfMediaSearch ? true : false, "searchForOtherMediaSearch", 'Sonstige Medien-Dateien', false, 'defaultfont', '', false));


				break;
			default:
				return;
		}

		return $_table->getHtml();
	}

	function getSearchDialogFilter($whichSearch){
		$_table = new we_html_table(
			array(
			'width' => 400,
			), 4, 4);

		switch($whichSearch){
			case self::SEARCH_MEDIA :
				$n = 2;

				$_table->setCol(0, 0, array(), g_l('searchtool', '[usage_state]') . ': ');
				$_table->setCol(0, 1, array('colspan' => 2), we_html_element::htmlHiddens(array(
						'searchFieldsMediaSearch[' . $n . ']' => 'IsUsed',
						'locationMediaSearch[' . $n . ']' => 'IS')) .
					we_html_tools::htmlSelect('searchMediaSearch[' . $n . ']', array(
						0 => g_l('searchtool', '[all]'),
						1 => g_l('searchtool', '[only_unsed]'),
						2 => g_l('searchtool', '[only_unused]')), 1, isset($this->Model->searchMediaSearch[$n]) ? $this->Model->searchMediaSearch[$n++] : '', false, array(), 'value', 220));

				$_table->setCol(1, 0, array(), g_l('searchtool', '[protection]') . ': ');
				$_table->setCol(1, 1, array('colspan' => 2), we_html_element::htmlHiddens(array(
						'searchFieldsMediaSearch[' . $n . ']' => 'IsProtected',
						'locationMediaSearch[' . $n . ']' => 'IS')) .
					we_html_tools::htmlSelect('searchMediaSearch[' . $n . ']', array(
						0 => g_l('searchtool', '[all]'),
						1 => g_l('searchtool', '[only_protected]'),
						2 => g_l('searchtool', '[only_unprotected]')), 1, isset($this->Model->searchMediaSearch[$n]) ? $this->Model->searchMediaSearch[$n++] : '', false, array(), 'value', 220));

				$this->searchMediaOptFieldIndex = $n;
				break;
			default:
				return;
		}
		$_table->setCol(2, 0, array('colspan' => 4), $this->getSearchDialogOptFields($whichSearch));
		$_table->setCol(3, 3, array(), we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);"));

		return $_table->getHtml();
	}

	function getSearchDialogCheckboxesAdvSearch(){
		if(!is_array($this->Model->search_tables_advSearch)){
			$this->Model->search_tables_advSearch = we_unserialize($this->Model->search_tables_advSearch);
			if(is_array($this->Model->search_tables_advSearch)){
				//tablenames are hardcoded in the tblsearchtool, get the real tablenames if they have a prefix
				foreach($this->Model->search_tables_advSearch as $k => $v){
					switch($k){
						case "tblFile":
							unset($this->Model->search_tables_advSearch[$k]);
							$this->Model->search_tables_advSearch[FILE_TABLE] = $v;
							break;
						case "tblTemplates":
							unset($this->Model->search_tables_advSearch[$k]);
							$this->Model->search_tables_advSearch[TEMPLATES_TABLE] = $v;
							break;
						case "tblObjectFiles":
							if(defined('OBJECT_FILES_TABLE')){
								unset($this->Model->search_tables_advSearch[$k]);
								$this->Model->search_tables_advSearch[OBJECT_FILES_TABLE] = $v;
							}
							break;
						case "tblObject":
							if(defined('OBJECT_TABLE')){
								unset($this->Model->search_tables_advSearch[$k]);
								$this->Model->search_tables_advSearch[OBJECT_TABLE] = $v;
							}
							break;
						case "tblversions":
							unset($this->Model->search_tables_advSearch[$k]);
							$this->Model->search_tables_advSearch[VERSIONS_TABLE] = $v;
					}
				}
			}
		}

		if(($table = we_base_request::_(we_base_request::TABLE, 'table'))){
			$search_tables_advSearch = $table;
			$this->Model->search_tables_advSearch[$search_tables_advSearch] = 1;
		}

		if(!isset($this->Model->search_tables_advSearch[FILE_TABLE])){
			$this->Model->search_tables_advSearch[FILE_TABLE] = 1;
		}

		if(!isset($this->Model->search_tables_advSearch[VERSIONS_TABLE])){
			$this->Model->search_tables_advSearch[VERSIONS_TABLE] = 0;
		}

		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
			$this->Model->search_tables_advSearch[TEMPLATES_TABLE] = 0;
		} elseif(!isset($this->Model->search_tables_advSearch[TEMPLATES_TABLE])){
			$this->Model->search_tables_advSearch[TEMPLATES_TABLE] = 0;
		}

		if(defined('OBJECT_FILES_TABLE') && !isset($this->Model->search_tables_advSearch[OBJECT_FILES_TABLE])){
			$this->Model->search_tables_advSearch[OBJECT_FILES_TABLE] = 1;
		}

		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
			$this->Model->search_tables_advSearch[OBJECT_TABLE] = 0;
		} elseif(defined('OBJECT_TABLE') && !isset($this->Model->search_tables_advSearch[OBJECT_TABLE])){
			$this->Model->search_tables_advSearch[OBJECT_TABLE] = 0;
		}

		if(!permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
			$this->Model->search_tables_advSearch[FILE_TABLE] = 0;
		}

		if(!permissionhandler::hasPerm('SEE_VERSIONS')){
			$this->Model->search_tables_advSearch[VERSIONS_TABLE] = 0;
		}

		if(!permissionhandler::hasPerm('CAN_SEE_TEMPLATES')){
			$this->Model->search_tables_advSearch[TEMPLATES_TABLE] = 0;
		}

		if(!permissionhandler::hasPerm('CAN_SEE_OBJECTFILES') && defined('OBJECT_FILES_TABLE')){
			$this->Model->search_tables_advSearch[OBJECT_FILES_TABLE] = 0;
		}

		if(!permissionhandler::hasPerm('CAN_SEE_OBJECTS') && defined('OBJECT_TABLE')){
			$this->Model->search_tables_advSearch[OBJECT_TABLE] = 0;
		}

		if(we_base_request::_(we_base_request::STRING, 'cmd') === "tool_weSearch_new_forObjects"){
			$this->Model->search_tables_advSearch[FILE_TABLE] = 0;
			$this->Model->search_tables_advSearch[VERSIONS_TABLE] = 0;
		}

		if(isset($_SESSION['weS']['weSearch']["checkWhich"])){
			switch($_SESSION['weS']['weSearch']["checkWhich"]){
				case 1:
					$this->Model->search_tables_advSearch[FILE_TABLE] = 1;
					$this->Model->search_tables_advSearch[VERSIONS_TABLE] = 0;
					$this->Model->search_tables_advSearch[OBJECT_FILES_TABLE] = 0;
					break;
				case 2:
					$this->Model->search_tables_advSearch[FILE_TABLE] = 0;
					$this->Model->search_tables_advSearch[VERSIONS_TABLE] = 0;
					$this->Model->search_tables_advSearch[OBJECT_FILES_TABLE] = 0;
					$this->Model->search_tables_advSearch[TEMPLATES_TABLE] = 1;
					$this->Model->search_tables_advSearch[OBJECT_TABLE] = 0;
					break;
				case 3:
					$this->Model->search_tables_advSearch[FILE_TABLE] = 0;
					$this->Model->search_tables_advSearch[VERSIONS_TABLE] = 0;
					$this->Model->search_tables_advSearch[OBJECT_FILES_TABLE] = 1;
					break;
				case 4:
					$this->Model->search_tables_advSearch[FILE_TABLE] = 0;
					$this->Model->search_tables_advSearch[VERSIONS_TABLE] = 0;
					$this->Model->search_tables_advSearch[OBJECT_FILES_TABLE] = 0;
					$this->Model->search_tables_advSearch[TEMPLATES_TABLE] = 0;
					$this->Model->search_tables_advSearch[OBJECT_TABLE] = 1;
			}
			unset($_SESSION['weS']['weSearch']["checkWhich"]);
		}

		$_table = new we_html_table(array('style' => 'width:550px',), 4, 3);

		if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
			$_table->setCol(0, 0, array(), we_html_forms::checkboxWithHidden($this->Model->search_tables_advSearch[FILE_TABLE] ? true : false, 'search_tables_advSearch[' . FILE_TABLE . ']', g_l('searchtool', '[documents]'), false, 'defaultfont', ''));
		}

		if(permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
			$_table->setCol(1, 0, array(), we_html_forms::checkboxWithHidden($this->Model->search_tables_advSearch[TEMPLATES_TABLE] ? true : false, 'search_tables_advSearch[' . TEMPLATES_TABLE . ']', g_l('searchtool', '[templates]'), false, 'defaultfont', ''));
		}

		if(defined('OBJECT_TABLE')){
			if(permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
				$_table->setCol(0, 1, array(), we_html_forms::checkboxWithHidden($this->Model->search_tables_advSearch[OBJECT_FILES_TABLE] ? true : false, 'search_tables_advSearch[' . OBJECT_FILES_TABLE . ']', g_l('searchtool', '[objects]'), false, 'defaultfont', ''));
			}
			if(permissionhandler::hasPerm('CAN_SEE_OBJECTS') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE){
				$_table->setCol(1, 1, array(), we_html_forms::checkboxWithHidden($this->Model->search_tables_advSearch[OBJECT_TABLE] ? true : false, 'search_tables_advSearch[' . OBJECT_TABLE . ']', g_l('searchtool', '[classes]'), false, 'defaultfont', ''));
			}
		}

		if(permissionhandler::hasPerm('SEE_VERSIONS')){
			$_table->setCol(0, 2, array(), we_html_forms::checkboxWithHidden($this->Model->search_tables_advSearch[VERSIONS_TABLE] ? true : false, 'search_tables_advSearch[' . VERSIONS_TABLE . ']', g_l('versions', '[versions]'), false, 'defaultfont', ''));
		}

		$_table->setCol(2, 2, array('style' => 'text-align:right'), we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);"));

		return $_table->getHtml();
	}

	function getSearchDialog($whichSearch){

		switch($whichSearch){
			case self::SEARCH_DOCS :
				$this->Model->locationDocSearch = (($op = we_base_request::_(we_base_request::STRING, "locationDocSearch")) ?
						$op :
						array("CONTAIN"));

				$this->Model->searchFieldsDocSearch = array();
				$locationName = "locationDocSearch[0]";
				$searchTextName = "searchDocSearch[0]";
				$searchTables = "search_tables_docSearch[" . FILE_TABLE . "]";

				if($this->Model->searchForTextDocSearch){
					$this->Model->searchFieldsDocSearch[] = "Text";
				}
				if($this->Model->searchForTitleDocSearch){
					$this->Model->searchFieldsDocSearch[] = "Title";
				}
				if($this->Model->searchForContentDocSearch){
					$this->Model->searchFieldsDocSearch[] = "Content";
				}
				if(!empty($_SESSION['weS']['weSearch']['keyword']) && we_base_request::_(we_base_request::INT, 'tab') == 1){
					$this->Model->searchDocSearch[0] = ($_SESSION['weS']['weSearch']['keyword']);
					if($GLOBALS['WE_BACKENDCHARSET'] === "UTF-8"){
						$this->Model->searchDocSearch[0] = utf8_encode($this->Model->searchDocSearch[0]);
					}
					unset($_SESSION['weS']['weSearch']['keyword']);
				}
				if(!is_array($this->Model->searchDocSearch)){
					$this->Model->searchDocSearch = we_unserialize($this->Model->searchDocSearch);
				}
				$searchInput = we_html_tools::htmlTextInput($searchTextName, 30, (isset($this->Model->searchDocSearch) && is_array($this->Model->searchDocSearch) && isset($this->Model->searchDocSearch[0]) ? $this->Model->searchDocSearch[0] : ''), "", "", "search", 380);
				break;
			case self::SEARCH_TMPL :
				$this->Model->locationTmplSearch = (($op = we_base_request::_(we_base_request::STRING, "locationTmplSearch")) ?
						$op :
						array("CONTAIN"));

				$this->Model->searchFieldsTmplSearch = array();
				$locationName = "locationTmplSearch[0]";
				$searchTextName = "searchTmplSearch[0]";
				$searchTables = "search_tables_TmplSearch[" . TEMPLATES_TABLE . "]";

				if($this->Model->searchForTextTmplSearch){
					$this->Model->searchFieldsTmplSearch[] = "Text";
				}
				if($this->Model->searchForContentTmplSearch){
					$this->Model->searchFieldsTmplSearch[] = "Content";
				}
				if((!empty($_SESSION['weS']['weSearch']['keyword'])) && we_base_request::_(we_base_request::INT, "tab") == 2){
					$this->Model->searchTmplSearch[0] = $_SESSION['weS']['weSearch']["keyword"];
					if($GLOBALS['WE_BACKENDCHARSET'] === "UTF-8"){
						$this->Model->searchTmplSearch[0] = utf8_encode($this->Model->searchTmplSearch[0]);
					}
					unset($_SESSION['weS']['weSearch']["keyword"]);
				}
				if(!is_array($this->Model->searchTmplSearch)){
					$this->Model->searchTmplSearch = we_unserialize($this->Model->searchTmplSearch);
				}
				$searchInput = we_html_tools::htmlTextInput($searchTextName, 30, (isset($this->Model->searchTmplSearch) && is_array($this->Model->searchTmplSearch) && isset($this->Model->searchTmplSearch[0]) ? $this->Model->searchTmplSearch[0] : ''), "", "", "search", 380);
				break;

			case self::SEARCH_MEDIA :
				$this->Model->locationMediaSearch = (($op = we_base_request::_(we_base_request::STRING, 'locationMediaSearch')) ?
						$op :
						array('CONTAIN'));

				$this->Model->searchFieldsMediaSearch = array();
				$locationName = "locationMediaSearch[0]";
				$searchTextName = "searchMediaSearch[0]";
				$searchFieldName = "searchFieldsMediaSearch[0]";

				$searchTables = "search_tables_MediaSearch[" . FILE_TABLE . "]";

				if($this->Model->searchForTextMediaSearch){
					$this->Model->searchFieldsMediaSearch[] = "Text";
				}
				if($this->Model->searchForTitleMediaSearch){
					$this->Model->searchFieldsMediaSearch[] = "Title";
				}
				if($this->Model->searchForMetaMediaSearch){
					$this->Model->searchFieldsMediaSearch[] = "Meta";
				}

				if((!empty($_SESSION['weS']['weSearch']["keyword"])) && we_base_request::_(we_base_request::INT, "tab") == 1){
					$this->Model->searchMediaSearch[0] = ($_SESSION['weS']['weSearch']["keyword"]);
					if($GLOBALS['WE_BACKENDCHARSET'] === "UTF-8"){
						$this->Model->searchMediaSearch[0] = utf8_encode($this->Model->searchMediaSearch[0]);
					}
					unset($_SESSION['weS']['weSearch']["keyword"]);
				}

				if(!is_array($this->Model->searchMediaSearch)){
					$this->Model->searchMediaSearch = we_unserialize($this->Model->searchMediaSearch);
				}

				$searchInput = we_html_element::htmlHidden($searchFieldName, 'keyword') .
					we_html_tools::htmlTextInput($searchTextName, 30, (isset($this->Model->searchMediaSearch) && is_array($this->Model->searchMediaSearch) && isset($this->Model->searchMediaSearch[0]) ? $this->Model->searchMediaSearch[0] : ''), "", "", "search", 380);
				break;
		}

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
 <td style="padding-right:20px;">' . $searchInput . '</td>
 <td>' . we_html_button::create_button(we_html_button::SEARCH, "javascript:weSearch.search(true);") . '</td>
 <td>' . we_html_tools::hidden($locationName, 'CONTAIN') . '</td>
 <td>' . we_html_tools::hidden($searchTables, 1) . '</td>
 <td></td>
</tr>' . ( $whichSearch == self::SEARCH_MEDIA ?
				'<tr><td colspan="5">' . we_html_tools::htmlAlertAttentionBox("Ohne Suchbegriff werden alle Medien-Dokumente ausgegeben.", we_html_tools::TYPE_INFO, 380) . '</td></tr>' :
				'') . '
</tbody></table>';
	}

	function searchProperties($whichSearch){
		$DB_WE = new DB_WE();
		$workspaces = $_result = $versionsFound = $saveArrayIds = $_tables = $searchText = array();
		$_SESSION['weS']['weSearch']['foundItems' . $whichSearch] = 0;
		$request = we_base_request::_(we_base_request::RAW, 'we_cmd'); //FIXME: due to search for <"
		if(isset($GLOBALS['we_cmd_obj'])){
			$obj = $GLOBALS['we_cmd_obj'];

			foreach($request as $k => $v){
				if(stristr($k, 'searchFields' . $whichSearch . '[') && !stristr($k, 'hidden_')){
					$_REQUEST['we_cmd']['searchFields' . $whichSearch][] = $v;
				}
				if(stristr($k, 'location' . $whichSearch . '[')){
					$_REQUEST['we_cmd']['location' . $whichSearch][] = $v;
				}
				if(stristr($k, 'search' . $whichSearch . '[')){
					$_REQUEST['we_cmd']['search' . $whichSearch][] = $v;
				}
			}

			switch($whichSearch){
				case self::SEARCH_DOCS:
					$_tables[0] = FILE_TABLE;
					$folderID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'folderIDDoc');
					foreach(we_base_request::_(we_base_request::RAW, 'we_cmd') as $k => $v){
						if(is_string($v) && $v == 1){
							switch($k){
								case 'searchForTextDocSearch':
									$_REQUEST['we_cmd']['searchFields' . $whichSearch][] = 'Text';
									break;
								case 'searchForTitleDocSearch':
									$_REQUEST['we_cmd']['searchFields' . $whichSearch][] = 'Title';
									break;
								case 'searchForContentDocSearch':
									$_REQUEST['we_cmd']['searchFields' . $whichSearch][] = 'Content';
									break;
							}
						}
					}
					break;
				case self::SEARCH_TMPL:
					$_tables[0] = TEMPLATES_TABLE;
					$folderID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'folderIDTmpl');
					foreach(we_base_request::_(we_base_request::RAW, 'we_cmd') as $k => $v){
						if(is_string($v) && $v == 1){
							switch($k){
								case 'searchForTextTmplSearch':
									$_REQUEST['we_cmd']['searchFields' . $whichSearch][] = 'Text';
									break;
								case 'searchForContentTmplSearch':
									$_REQUEST['we_cmd']['searchFields' . $whichSearch][] = 'Content';
									break;
							}
						}
					}
					break;
				case self::SEARCH_MEDIA:
					$_tables[0] = FILE_TABLE;
					$folderID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'folderIDMedia');
					$searchForContentTypesMediaSearch = '';

					foreach(we_base_request::_(we_base_request::RAW, 'we_cmd') as $k => $v){
						if(is_string($v) && $v == 1){
							switch($k){
								case 'searchForTextMediaSearch':
								case 'searchForTitleMediaSearch':
								case 'searchForMetaMediaSearch':
									$_REQUEST['we_cmd']['searchFields' . $whichSearch][] = $k === 'searchForTextMediaSearch' ? 'Text' :
										($k === 'searchForTitleMediaSearch' ? 'Title' : 'Meta');
									$_REQUEST['we_cmd']['search' . $whichSearch][] = $_REQUEST['we_cmd']['search' . $whichSearch][0];
									$_REQUEST['we_cmd']['location' . $whichSearch][] = 'CONTAIN';
									break;
								case 'searchForImageMediaSearch':
									$searchForContentTypesMediaSearch .= "'" . we_base_ContentTypes::IMAGE . "',";
									break;
								case 'searchForVideoMediaSearch':
									$searchForContentTypesMediaSearch .= "'" . we_base_ContentTypes::VIDEO . "','" . we_base_ContentTypes::QUICKTIME . "'" . "'" . we_base_ContentTypes::FLASH . "',";
									break;
								case 'searchForAudioMediaSearch':
									$searchForContentTypesMediaSearch .= "'" . we_base_ContentTypes::AUDIO . "',";
									break;
								//case 'searchForPdfMediaSearch':
								case 'searchForOtherMediaSearch':
									$searchForContentTypesMediaSearch .= "'" . we_base_ContentTypes::APPLICATION . "',";
									//$searchForContentTypesMediaSearch .= "'#PDF#',";
									break;
							}
						}
					}
					foreach(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'searchFields' . $whichSearch) as $k => $v){
						switch($v){
							case 'ContentType':
								if(!$cts = trim($searchForContentTypesMediaSearch, ',')){
									$cts = "'" . we_base_ContentTypes::IMAGE . "','" . we_base_ContentTypes::VIDEO . "','" . we_base_ContentTypes::QUICKTIME . "','" . we_base_ContentTypes::FLASH . "','" . we_base_ContentTypes::AUDIO . "','" . we_base_ContentTypes::APPLICATION . "'"; //"','#PDF#'";
								}
								$_REQUEST['we_cmd']['search' . $whichSearch][$k] = $cts;
								break;
							default:
								if(strpos($v, 'meta__') === 0 && $_REQUEST['we_cmd']['search' . $whichSearch][$k] === '' && $_REQUEST['we_cmd']['location' . $whichSearch][$k] === 'IS'){
									$_REQUEST['we_cmd']['search' . $whichSearch][$k] = "#EMPTY#";
								}
						}
					}
					break;
				default:
					foreach(we_base_request::_(we_base_request::RAW, 'we_cmd') as $k => $v){
						if($v){
							switch($k){
								case 'search_tables_advSearch[' . FILE_TABLE:
									$_tables[] = FILE_TABLE;
									break;
								case 'search_tables_advSearch[' . (defined('TEMPLATES_TABLE') ? TEMPLATES_TABLE : 'TEMPLATES_TABLE'):
									$_tables[] = TEMPLATES_TABLE;
									break;
								case 'search_tables_advSearch[' . (defined('VERSIONS_TABLE') ? VERSIONS_TABLE : 'VERSIONS_TABLE'):
									$_tables[] = VERSIONS_TABLE;
									break;
								case 'search_tables_advSearch[' . (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
									$_tables[] = OBJECT_FILES_TABLE;
									break;
								case 'search_tables_advSearch[' . (defined('OBJECT_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_TABLE'):
									$_tables[] = OBJECT_TABLE;
									break;
							}
						}
					}

					break;
			}

			$searchFields = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'searchFields' . $whichSearch);
			$location = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'location' . $whichSearch);
			$searchText = we_base_request::_(we_base_request::RAW, 'we_cmd', '', 'search' . $whichSearch); //allow to search for tags
			$_order = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'Order' . $whichSearch);
			$_view = we_base_request::_(we_base_request::STRING, 'we_cmd', self::VIEW_LIST, 'setView' . $whichSearch);

			$_searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'searchstart' . $whichSearch);
			$_anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'anzahl' . $whichSearch);
		} else {
			$obj = $this->Model;

			switch($whichSearch){
				case self::SEARCH_DOCS:
					$obj->searchstartDocSearch = we_base_request::_(we_base_request::STRING, "searchstartDocSearch", $obj->searchstartDocSearch);
					$_table = FILE_TABLE;
					$_tables[0] = $_table;
					$searchFields = $obj->searchFieldsDocSearch;
					$searchText = $obj->searchDocSearch;
					$location = $obj->locationDocSearch;
					$folderID = $obj->folderIDDoc;
					$_order = $obj->OrderDocSearch;
					$_view = $obj->setViewDocSearch;
					$_searchstart = $obj->searchstartDocSearch;
					$_anzahl = $obj->anzahlDocSearch;
					break;
				case self::SEARCH_TMPL:
					$obj->searchstartTmplSearch = we_base_request::_(we_base_request::INT, "searchstartTmplSearch", $obj->searchstartTmplSearch);
					$_table = TEMPLATES_TABLE;
					$_tables[0] = $_table;
					$searchFields = $obj->searchFieldsTmplSearch;
					$searchText = $obj->searchTmplSearch;
					$location = $obj->locationTmplSearch;
					$folderID = $obj->folderIDTmpl;
					$_order = $obj->OrderTmplSearch;
					$_view = $obj->setViewTmplSearch;
					$_searchstart = $obj->searchstartTmplSearch;
					$_anzahl = $obj->anzahlTmplSearch;
					break;
				case self::SEARCH_MEDIA:
					$obj->searchstartMediaSearch = we_base_request::_(we_base_request::STRING, "searchstartMediaSearch", $obj->searchstartMediaSearch);
					$_table = FILE_TABLE;
					$_tables[0] = $_table;
					$searchFields = $obj->searchFieldsMediaSearch;
					$searchText = $obj->searchMediaSearch;
					$location = $obj->locationMediaSearch;
					$folderID = $obj->folderIDMedia;
					$_order = $obj->OrderMediaSearch;
					$_view = $obj->setViewMediaSearch;
					$_searchstart = $obj->searchstartMediaSearch;
					$_anzahl = $obj->anzahlMediaSearch;
					break;
				case self::SEARCH_ADV:
					$obj->searchstartAdvSearch = we_base_request::_(we_base_request::INT, "searchstartAdvSearch", $obj->searchstartAdvSearch);
					if(!($obj->searchFieldsAdvSearch)){
						$obj->searchFieldsAdvSearch = array("ID");
					}
					if(!($obj->locationAdvSearch)){
						$obj->locationAdvSearch = array("CONTAIN");
					}
					$searchFields = $obj->searchFieldsAdvSearch;
					$searchText = $obj->searchAdvSearch;
					$location = $obj->locationAdvSearch;
					$folderID = 0;
					$_order = $obj->OrderAdvSearch;
					$_view = $obj->setViewAdvSearch;
					$_searchstart = $obj->searchstartAdvSearch;
					$_anzahl = $obj->anzahlAdvSearch;

					$_tables = array_keys(array_filter($obj->search_tables_advSearch));
					break;
			}
			if(stripos($GLOBALS['WE_LANGUAGE'], '_UTF-8') !== false){ //was #3849
				foreach($searchText as &$cur){
					$cur = utf8_decode($cur);
				}
			}
		}
		if(isset($searchText) && is_array($searchText)){
			array_map('trim', $searchText);
		} else {
			$searchText = array();
		}

		$tab = we_base_request::_(we_base_request::INT, 'tab', we_base_request::_(we_base_request::INT, 'tabnr', 1));

		if(isset($searchText[0]) && substr($searchText[0], 0, 4) === 'exp:'){

			$_result = $this->searchclassExp->getSearchResults($searchText[0], $_tables);
			if($_result){
				foreach($_result as $k => $v){
					foreach($v as $key => $val){
						switch($key){
							case "Table":
							case 'ID':
								unset($_result[$k][$key]);
								$_result[$k]['doc' . $key] = $val;
						}
					}
					$_result[$k]['SiteTitle'] = "";
				}
				$_SESSION['weS']['weSearch']['foundItems' . $whichSearch] = count($_result);
			}
		} elseif(
			($obj->IsFolder != 1 && ( ($whichSearch === self::SEARCH_DOCS && $tab === 1) || ($whichSearch === self::SEARCH_TMPL && $tab === 2) || ($whichSearch === self::SEARCH_ADV && $tab === 3)) || ($whichSearch === self::SEARCH_MEDIA && $tab === 5) ) ||
			(we_base_request::_(we_base_request::INT, 'cmdid')) ||
			(($view = we_base_request::_(we_base_request::STRING, 'view')) === "GetSearchResult" || $view === "GetMouseOverDivs")
		){

			if(!we_search_search::checkRightTempTable() && !we_search_search::checkRightDropTable()){
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('searchtool', '[noTempTableRightsSearch]'), we_message_reporting::WE_MESSAGE_NOTICE));
				return;
			}
			$this->searchclass->createTempTable();
			$op = ($whichSearch === self::SEARCH_ADV || $whichSearch === self::SEARCH_MEDIA ? ' AND ' : ' OR ');

			foreach($_tables as $_table){
				$where = '';
				$where_OR = '';
				$this->searchclass->settable($_table);

				if(!defined('OBJECT_TABLE') || (defined('OBJECT_TABLE') && $_table != OBJECT_TABLE)){
					$workspaces = get_ws($_table, true);
				}

				for($i = 0; $i < count($searchFields); $i++){
					$w = '';
					$done = false;
					if(isset($searchText[0])){
						$searchString = (($whichSearch === self::SEARCH_ADV || $whichSearch === self::SEARCH_MEDIA) && isset($searchText[$i]) ?
								($GLOBALS['WE_BACKENDCHARSET'] === "UTF-8" ? utf8_encode($searchText[$i]) : $searchText[$i]) :
								($GLOBALS['WE_BACKENDCHARSET'] === "UTF-8" ? utf8_encode($searchText[0]) : $searchText[0]));
					}
					if(!empty($searchString)){
						if($searchFields[$i] != "Status" && $searchFields[$i] != "Speicherart"){
							$searchString = str_replace(array('\\', '_', '%'), array('\\\\', '\_', '\%'), $searchString);
						}

						if($_table === FILE_TABLE && $whichSearch === self::SEARCH_MEDIA){
							$done = true;
							switch($searchFields[$i]){
								case "Title": // IMPORTANT: in media search options are generally AND-linked, but not "search in Title, Text, Meta!
									$where_OR .= ($where_OR ? 'OR ' : '') . $this->searchclass->searchInTitle($searchString, $_table);
									break;
								case "Text":
									$where_OR .= ($where_OR ? 'OR ' : '') . $_table . '.`Text` LIKE "%' . $DB_WE->escape(trim($searchString)) . '%" ';
									break;
								case "Meta":
									$where_OR .= ($where_OR ? 'OR ' : '') . $this->searchclass->searchInAllMetas($searchString);
									break;
								case 'ContentType':
									/*
									  if(strpos($searchString, "'#PDF#'") !== false){
									  $searchString = str_replace(array("',#PDF'", "'#PDF#'"), '', $searchString);
									  $where .= ' AND (' . $_table . '.ContentType IN (' . trim($searchString, ',') . ') OR ' . $_table . '.Extension = ".pdf") ';
									  } else {
									 *
									 */
									$where .= ' AND ' . $_table . '.ContentType IN (' . $searchString . ')';
									/*
									  }
									 *
									 */
									break;
								case 'IsUsed':
									$where .= $this->searchclass->searchMediaLinks($searchString, $_view !== self::VIEW_ICONS);
									break;
								case 'IsProtected':
									switch($searchString){
										case 1:
											$where .= ' AND ' . $_table . '.IsProtected=1 ';
											break;
										case 2:
											$where .= ' AND ' . $_table . '.IsProtected=0 ';
											break;
									}
									break;
								default:
									$done = false;
							}
							if(substr($searchFields[$i], 0, 6) === 'meta__'){
								$where .= $this->searchclass->searchInMeta($searchString, substr($searchFields[$i], 6), $location[$i], $_table);
								$done = true;
							}
						}

						if($whichSearch === self::SEARCH_ADV && isset($location[$i])){
							switch($searchFields[$i]){
								case "Content":
								case "Status":
								case "Speicherart":
								case "CreatorName":
								case "WebUserName":
								case "temp_category":
									break;
								default:
									$where .= $this->searchclass->searchfor($searchString, $searchFields[$i], $location[$i], $_table);
							}
						}

						if(!$done){
							switch($searchFields[$i]){
								case 'Content':
									$objectTable = defined('OBJECT_TABLE') ? OBJECT_TABLE : '';
									if($objectTable != "" && $_table == $objectTable){

									} else {
										$w = $this->searchclass->searchContent($searchString, $_table);
										if($where == '' && $w == ''){
											$where .= ' AND 0 ';
										} elseif($where == '' && $w != ''){
											$where .= ' AND ' . $w;
										} elseif($w != ''){
											$where .= $op . ' ' . $w;
										}
									}
									break;

								case 'modifierID':
									if($_table == VERSIONS_TABLE){
										$w .= $this->searchclass->searchModifier($searchString, $_table);
										$where .= $w;
									}
									break;

								case 'allModsIn':
									if($_table == VERSIONS_TABLE){
										$w .= $this->searchclass->searchModFields($searchString, $_table);
										$where .= $w;
									}
									break;

								case 'Title':
									$w = $this->searchclass->searchInTitle($searchString, $_table);

									if($where == '' && $w == ''){
										$where .= ' AND 0 ';
									} elseif($where == '' && $w != ''){
										$where .= ' AND ' . $w;
									} elseif($w != ''){
										$where .= $op . ' ' . $w;
									}
									break;
								case 'Status':
								case 'Speicherart':
									if($_table == FILE_TABLE || $_table == VERSIONS_TABLE || $_table == OBJECT_FILES_TABLE){
										$w = $this->searchclass->getStatusFiles($searchString, $_table);
										if($_table == VERSIONS_TABLE){
											$docTableChecked = (in_array(FILE_TABLE, $_tables)) ? true : false;
											$objTableChecked = (defined('OBJECT_FILES_TABLE') && (in_array(OBJECT_FILES_TABLE, $_tables))) ? true : false;
											if($objTableChecked && $docTableChecked){
												$w .= ' AND (v.documentTable="' . FILE_TABLE . '" OR documentTable="' . OBJECT_FILES_TABLE . '") ';
											} elseif($docTableChecked){
												$w .= ' AND v.documentTable="' . FILE_TABLE . '" ';
											} elseif($objTableChecked){
												$w .= ' AND v.documentTable="' . OBJECT_FILES_TABLE . '" ';
											}
										}
										$where .= $w;
									}
									break;
								case 'CreatorName':
								case 'WebUserName':
									if(isset($searchFields[$i]) && isset($location[$i])){
										$w = $this->searchclass->searchSpecial($searchString, $searchFields[$i], $location[$i]);
										$where .= ' AND ' . $w;
									}
									break;
								case 'temp_category':
									$w = $this->searchclass->searchCategory($searchString, $_table, $searchFields[$i]);
									$where .= $w;
									break;
								default:
									if($whichSearch != "AdvSearch"){
										$where .= $this->searchclass->searchfor($searchString, $searchFields[$i], $location[$i], $_table);
									}
							}
						}
					}
				}

				if($where || $where_OR){

					if(isset($folderID) && ($folderID != '' && $folderID != 0)){
						$where = ' AND (' . $where . ')' . we_search_search::ofFolderAndChildsOnly($folderID, $_table);
					}

					if($_table == VERSIONS_TABLE){
						$workspacesTblFile = get_ws(FILE_TABLE, true);
						if(defined('OBJECT_FILES_TABLE')){
							$workspacesObjFile = get_ws(OBJECT_FILES_TABLE, true);
						}
					}

					if($workspaces){
						$where = ' (' . $where . ')' . we_search_search::ofFolderAndChildsOnly($workspaces, $_table);
					}

					$whereQuery = $where;

					//query for restrict users for FILE_TABLE, VERSIONS_TABLE AND OBJECT_FILES_TABLE
					$restrictUserQuery = ' AND ((' . escape_sql_query($_table) . '.RestrictOwners=0 OR ' . escape_sql_query($_table) . '.RestrictOwners= ' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',' . escape_sql_query($_table) . '.Owners)))';

					switch($_table){
						case FILE_TABLE:
							if($where_OR){
								$whereQuery .= ' AND (' . $where_OR . ') ';
							}
							$whereQuery .= $restrictUserQuery;
							break;

						case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : -1):
							$whereQuery .= $restrictUserQuery;
							break;

						case (defined('OBJECT_TABLE') ? OBJECT_TABLE : -2):
							$whereQuery .= ' AND ((' . $this->db->escape($_table) . '.RestrictUsers=0 OR ' . $this->db->escape($_table) . '.RestrictUsers=' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',' . $this->db->escape($_table) . '.Users))) ';
							break;
						case VERSIONS_TABLE:
							if(isset($GLOBALS['we_cmd_obj'])){
								$isCheckedFileTable = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'search_tables_advSearch', FILE_TABLE);
								$isCheckedObjFileTable = (defined('OBJECT_FILES_TABLE')) ? we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'search_tables_advSearch', OBJECT_FILES_TABLE) : 1;
							} else {
								$isCheckedFileTable = $this->Model->search_tables_advSearch[FILE_TABLE];
								$isCheckedObjFileTable = (defined('OBJECT_FILES_TABLE')) ? $this->Model->search_tables_advSearch[OBJECT_FILES_TABLE] : 1;
							}
							$_SESSION['weS']['weSearch']['onlyObjects'] = true;
							$_SESSION['weS']['weSearch']['onlyDocs'] = true;
							$_SESSION['weS']['weSearch']['ObjectsAndDocs'] = true;
							$_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere'] = ' AND ((' . OBJECT_FILES_TABLE . '.RestrictOwners=0 OR ' . OBJECT_FILES_TABLE . '.RestrictOwners= ' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',' . OBJECT_FILES_TABLE . '.Owners)))';
							$_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere'] = ' AND ((' . FILE_TABLE . '.RestrictOwners=0 OR ' . FILE_TABLE . '.RestrictOwners= ' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',' . FILE_TABLE . '.Owners)))';
							if(!empty($workspacesTblFile)){
								$_SESSION['weS']['weSearch']['onlyDocsRestrUsersWhere'] .= $where = ' ' . we_search_search::ofFolderAndChildsOnly($workspacesTblFile[0], $_table);
							}
							if(isset($workspacesObjFile) && !empty($workspacesObjFile)){
								$_SESSION['weS']['weSearch']['onlyObjectsRestrUsersWhere'] .= $where = " " . we_search_search::ofFolderAndChildsOnly($workspacesObjFile[0], $_table);
							}

							if(!$isCheckedFileTable && $isCheckedObjFileTable){
								$_SESSION['weS']['weSearch']['onlyDocs'] = false;
								$whereQuery .= ' AND ' . escape_sql_query($_table) . '.documentTable="' . OBJECT_FILES_TABLE . '" ';
								$_SESSION['weS']['weSearch']['ObjectsAndDocs'] = false;
							}
							if($isCheckedFileTable && !$isCheckedObjFileTable){
								$_SESSION['weS']['weSearch']['onlyObjects'] = false;
								$whereQuery .= ' AND ' . escape_sql_query($_table) . ".documentTable='" . FILE_TABLE . "' ";
								$_SESSION['weS']['weSearch']['ObjectsAndDocs'] = false;
							}
							break;
					}

					$this->searchclass->setwhere($whereQuery);
					$this->searchclass->insertInTempTable($whereQuery, $_table);

					// when MediaSearch add attrib_alt, attrib_title, IsUsed to SEARCH_TEMP_TABLE
					if(self::SEARCH_MEDIA){
						$this->searchclass->insertMediaAttribsToTempTable();
						//SELECT id,alt,title FROM SEARCH_TEMP_TABLE JOIN tblLink JOIN tblContent ON bla WHERE alt  OR title...
					}
				}
			}

			$this->searchclass->selectFromTempTable($_searchstart, $_anzahl, $_order);

			while($this->searchclass->next_record()){
				if(!empty($this->searchclass->Record['VersionID'])){

					$versionsFound[] = array(
						$this->searchclass->Record['ContentType'],
						$this->searchclass->Record['docID'],
						$this->searchclass->Record['VersionID']
					);
				}
				if(!isset($saveArrayIds[$this->searchclass->Record['ContentType']][$this->searchclass->Record['docID']])){
					$saveArrayIds[$this->searchclass->Record['ContentType']][$this->searchclass->Record['docID']] = $this->searchclass->Record['docID'];

					$_result[] = array_merge(array('Table' => $_table), array('foundInVersions' => ""), $this->searchclass->Record);
				}
			}

			foreach($versionsFound as $k => $v){
				foreach($_result as $key => $val){
					if(isset($_result[$key]['foundInVersions']) && isset($_result[$key]['docID']) && $_result[$key]['docID'] == $v[1] && isset(
							$_result[$key]['ContentType']) && $_result[$key]['ContentType'] == $v[0]){
						if($_result[$key]['foundInVersions'] != ""){
							$_result[$key]['foundInVersions'] .= ",";
						}
						$_result[$key]['foundInVersions'] .= $v[2];
					}
				}

				$this->searchclass->selectFromTempTable($_searchstart, $_anzahl, $_order);
				while($this->searchclass->next_record()){
					if(!isset(
							$saveArrayIds[$this->searchclass->Record['ContentType']][$this->searchclass->Record['docID']])){
						$saveArrayIds[$this->searchclass->Record['ContentType']][$this->searchclass->Record['docID']] = $this->searchclass->Record['docID'];
						$_result[] = array_merge(array(
							'Table' => $_table
							), $this->searchclass->Record);
					}
				}
			}
			$_SESSION['weS']['weSearch']['foundItems' . $whichSearch] = $this->searchclass->getResultCount();
		}

		if($_SESSION['weS']['weSearch']['foundItems' . $whichSearch] == 0){
			return array();
		}

		foreach($_result as $k => $v){
			$_result[$k]["Description"] = '';
			if($_result[$k]['docTable'] == FILE_TABLE && $_result[$k]['Published'] >= $_result[$k]['ModDate'] && $_result[$k]['Published'] != 0){
				$DB_WE->query('SELECT l.DID, c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DID=' . intval($_result[$k]["docID"]) . ' AND l.Name="Description" AND l.DocumentTable="' . FILE_TABLE . '"');
				while($DB_WE->next_record()){
					$_result[$k]["Description"] = $DB_WE->f('Dat');
				}
			} elseif($_result[$k]['docTable'] == FILE_TABLE){
				$tempDoc = f('SELECT DocumentObject  FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID =' . intval($_result[$k]["docID"]) . ' AND DocTable = "tblFile" AND Active = 1', 'DocumentObject', $DB_WE);
				if(!empty($tempDoc)){
					$tempDoc = we_unserialize($tempDoc);
					if(isset($tempDoc[0]['elements']['Description']) && $tempDoc[0]['elements']['Description']['dat'] != ''){
						$_result[$k]["Description"] = $tempDoc[0]['elements']['Description']['dat'];
					}
				}
			} else {
				$_result[$k]['Description'] = '';
			}
		}

		return $this->makeContent($_result, $_view, $whichSearch);
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
			array("dat" => '<a href="javascript:weSearch.setOrder(\'FileSize\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[groesse]') . '</a> <span id="FileSize_' . $whichSearch . '" >' . $this->getSortImage('fileSize', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'Status\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[Status]') . '</a> <span id="Status_' . $whichSearch . '" >' . $this->getSortImage('status', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'Alt\',\'' . $whichSearch . '\');">alt</a> <span id="Alt_' . $whichSearch . '" >' . $this->getSortImage('alt', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'Title\',\'' . $whichSearch . '\');">title</a> <span id="Title_' . $whichSearch . '" >' . $this->getSortImage('title', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'CreationDate\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[created]') . '</a> <span id="CreationDate_' . $whichSearch . '" >' . $this->getSortImage('CreationDate', $whichSearch) . '</span>'),
			array("dat" => '<a href="javascript:weSearch.setOrder(\'ModDate\',\'' . $whichSearch . '\');">' . g_l('searchtool', '[modified]') . '</a> <span id="ModDate_' . $whichSearch . '" >' . $this->getSortImage('ModDate', $whichSearch) . '</span>'),
			array("dat" => '')
		);
	}

	private function makeContent($_result = array(), $view = self::VIEW_LIST, $whichSearch = self::SEARCH_DOCS){
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
					$published = (isset($_result[$f]["Published"]) ? $_result[$f]["Published"] : 1);
			}

			$ext = isset($_result[$f]["Extension"]) ? $_result[$f]["Extension"] : "";
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

						$fileExists = f('SELECT 1 FROM ' . escape_sql_query($_result[$f]["docTable"]) . ' WHERE ID=' . intval($_result[$f]["docID"]), '', $DB_WE);

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
					switch($_result[$f]["ContentType"]){
						case we_base_ContentTypes::WEDOCUMENT:
						case we_base_ContentTypes::HTML:
						case "objectFile":
							$actionCheckbox = (!$showPubCheckbox ? (permissionhandler::hasPerm(
										'PUBLISH') && f('SELECT 1 FROM ' . escape_sql_query($_result[$f]["docTable"]) . ' WHERE ID=' . intval($_result[$f]["docID"]), '', $DB_WE)) ? we_html_forms::checkbox(
											$_result[$f]["docID"] . "_" . $_result[$f]["docTable"], 0, "publish_docs_" . $whichSearch, "", false, "middlefont", "") : we_html_tools::getPixel(20, 10) : '');
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
								$actionCheckbox = ' <img title="Dokument ist geschützt und kann nicht gelöscht werden!" src="' . IMAGE_DIR . 'alert_tiny.gif" border="0" />';
							} else if(!in_array($_result[$f]["docID"], $this->searchclass->getUsedMedia())){
								$actionCheckbox = permissionhandler::hasPerm('DELETE_DOCUMENT') && f('SELECT 1 FROM ' . escape_sql_query($_result[$f]["docTable"]) . ' WHERE ID=' . intval($_result[$f]["docID"]), '', $DB_WE) ?
									we_html_forms::checkbox($_result[$f]["docID"] . "_" . $_result[$f]["docTable"], 0, "delete_docs_" . $whichSearch, "", false, "middlefont", "") : we_html_tools::getPixel(20, 10);
							}
							break;
						default:
							$actionCheckbox = '';
					}
				}

				$_result[$f]['size'] = file_exists($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) : 0;
				$_result[$f]['fileSize'] = we_base_file::getHumanFileSize($_result[$f]['size']);
				$iconHTML = $this->getHtmlIconThmubnail($_result[$f]);
				$standardStyle = 'height:12px;padding-top:6px;font-size:11px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;';
				$content[] = $whichSearch !== self::SEARCH_MEDIA ?
					array(
					array("dat" => we_html_tools::getPixel(20, 1) . $actionCheckbox),
					array("dat" => we_html_element::jsElement('document.write(WE().util.getTreeIcon("' . $_result[$f]["ContentType"] . '"))')),
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]['docTable'] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]['Path'] . '"><u>' . $_result[$f]["Text"]),
					array("dat" => ($whichSearch === 'TmplSearch' ? str_replace('/' . $_result[$f]["Text"], '', $_result[$f]["Path"]) : $_result[$f]["SiteTitle"])),
					array("dat" => isset($_result[$f]["VersionID"]) && $_result[$f]['VersionID'] ? "-" : ($_result[$f]["CreationDate"] ? date(
									g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-")),
					array("dat" => ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-")),
					) :
					array(
					array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'vertical-align:top;"', 'dat' => array(
							array('elem' => 'table', '' => '', 'dat' => array(
									array('elem' => 'row', 'dat' => array(
											array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'padding-top:10px;"', 'dat' => $actionCheckbox),
										)),
									array('elem' => 'row', 'attribs' => '', 'dat' => array(
											array('elem' => 'td', 'attribs' => '', 'dat' => '&nbsp;'),
										))
								)
							)
						)),
					array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'vertical-align:top;"', 'dat' => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]['docTable'] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]['Path'] . '">' . $iconHTML['imageView']),
					array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'vertical-align:top;"', 'dat' => array(
							array('elem' => 'table', '' => '', 'dat' => array(
									array('elem' => 'row', 'dat' => array(
											array('elem' => 'td', 'attribs' => 'style="' . $standardStyle . 'font-weight:bold;"', 'dat' => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]['docTable'] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]['Path'] . ' (ID: ' . $_result[$f]['docID'] . ')"><u>' . $_result[$f]["Text"] . '</u></a>'),
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
											<col style="width:40px;text-align:left;"/>
											<col style="width:70px;text-align:left;"/>
											<col style="width:45px;text-align:left;"/>
											<col style="width:45px;text-align:left;"/>
											<col style="width:45px;text-align:left;"/>
											<col style="width:90px;text-align:left;"/>
											<col style="width:90px;text-align:left;"/>
											<col style="width:50px;text-align:left;"/>
											</colgroup>'
							)
						)),
				);
			} else {
				$_result[$f]['size'] = file_exists($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) : 0;
				$_result[$f]['fileSize'] = we_base_file::getHumanFileSize($_result[$f]['size']);
				$iconHTML = $this->getHtmlIconThmubnail($_result[$f], 64, $whichSearch === self::SEARCH_MEDIA ? 180 : 140);
				$creator = $_result[$f]["CreatorID"] ? id_to_path($_result[$f]["CreatorID"], USER_TABLE, $DB_WE) : g_l('searchtool', '[nobody]');

				if($_result[$f]["ContentType"] == we_base_ContentTypes::WEDOCUMENT && $_result[$f]["Table"] != VERSIONS_TABLE){
					$templateID = ($_result[$f]["Published"] >= $_result[$f]["ModDate"] && $_result[$f]["Published"] != 0 ?
							$_result[$f]["TemplateID"] :
							$_result[$f]["temp_template_id"]);

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
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $iconHTML['imageView'] . '</a>'),
					array("dat" => we_base_util::shortenPath($_result[$f]["SiteTitle"], 17)),
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . ' middlefont" title="' . ($whichSearch === self::SEARCH_MEDIA ? $_result[$f]["Path"] : $_result[$f]["Text"]) . '"><u>' . we_base_util::shortenPath($_result[$f]["Text"], 20) . '</u></a>'),
					array("dat" => '<nobr>' . ($_result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-") . '</nobr>'),
					array("dat" => '<a href="javascript:weSearch.openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none;" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $iconHTML['imageViewPopup'] . '</a>'),
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
					array("dat" => $_result[$f]["docID"]),
				);
			}
		}

		return $content;
	}

	function makeAdditionalContentMedia($result){
		$usedMediaLinks = $this->searchclass->getUsedMediaLinks();

		if(!empty($usedMediaLinks['mediaID_' . $result['docID']])){
			$out = '<table style="font-weight:normal; background-color:#fafafa;width:480px"><tr><td colspan="2" style="padding:4px 0 0 6px;"><strong>Dieses Medien-Dokument wird an folgenden Stellen referenziert:</stong></td></tr>'; // FIXME: G_L()
			foreach($usedMediaLinks['mediaID_' . $result['docID']] as $type => $links){
				$out .= '<tr><td style="padding:4px 0 0 6px;"><em>' . $type . ':</em></td></tr>';
				foreach($links as $link){
					$color = 'black';
					$makeLink = true;
					switch($link['referencedIn']){
						case 'temp':
						case 'both':
							if($link['isUnpublished']){
								$color = 'red';
							} else {
								$color = '#3366cc';
							}
							break;
						case 'main':
							if($link['isModified']){
								$color = 'gray';
								$makeLink = false;
							} else if($link['isUnpublished']){
								$color = 'red';
							}
					}
					$out .= '<tr>' .
						($makeLink ? '
							<td style="padding-left:26px;width:410px;"><a href="javascript:' . $link['onclick'] . '" title="' . $link['path'] . ' (' . $link["id"] . ')"><span style="color:' . $color . ';"><u>' . $link['path'] . '</u></span></a></td>
							<td>' . we_html_button::create_button(we_html_button::EDIT, "javascript:weSearch.openToEdit('" . $link['table'] . "'," . $link["id"] . ",'');", true, 27, 22) . '</td>' :
							'<td style="padding-left:26px;width:410px;"><span style="color:' . $color . ';">' . $link['path'] . '</span></td>
							<td>' . we_html_button::create_button(we_html_button::EDIT, '', true, 27, 22, '', '', true, false, '', false, 'Der Link wurde bei einer unveröffentlichten Änderung entfernt: Er existiert nur noch in der veröffentlichten Version!') . '</td>') .
						'</tr>';
				}
			}
			$out .= '</table>';

			return $out;
		}
	}

	function getHtmlIconThmubnail($file, $smallSize = 64, $bigSize = 140){
		$urlPopup = $url = '';
		if($file["ContentType"] == we_base_ContentTypes::IMAGE){
			if($file["size"] > 0){
				$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $file["Path"]);
				$url = WEBEDITION_DIR . 'thumbnail.php?id=' . $file["docID"] . "&size=" . $smallSize . "&path=" . urlencode($file["Path"]) . "&extension=" . $file["Extension"]
				;
				$imageView = "<img src='" . $url . "' border='0' /></a>";

				$urlPopup = WEBEDITION_DIR . "thumbnail.php?id=" . $file["docID"] . "&size=" . $bigSize . "&path=" . $file["Path"] . "&extension=" . $file["Extension"];
				$imageViewPopup = "<img src='" . $urlPopup . "' border='0' /></a>";
			} else {
				$imagesize = array(0, 0);
				$imageView = $imageViewPopup = we_html_element::jsElement('document.write(WE().util.getTreeIcon("' . we_base_ContentTypes::IMAGE . '"))');
			}
		} else {
			$imagesize = array(0, 0);
			$imageView = $imageViewPopup = we_html_element::jsElement('document.write(WE().util.getTreeIcon("' . $file["ContentType"] . '",false,"' . $file['Extension'] . '"))');
		}

		return array('imageView' => $imageView, 'imageViewPopup' => $imageViewPopup, 'sizeX' => $imagesize[0], 'sizeY' => $imagesize[1], 'url' => $url, 'urlPopup' => $urlPopup);
	}

	function getSearchParameterTop($foundItems, $whichSearch){
		switch(isset($GLOBALS['we_cmd_obj']) ? 'we_cmd_obj' : $whichSearch){
			case 'we_cmd_obj':
				$_view = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'setView' . $whichSearch);
				$view = "setView" . $whichSearch;
				$_order = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'Order' . $whichSearch);
				$order = "Order" . $whichSearch;
				$_anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', '', 'anzahl' . $whichSearch);
				$anzahl = "anzahl" . $whichSearch;
				$searchstart = "searchstart" . $whichSearch;
				break;
			case self::SEARCH_DOCS :
				$_view = $this->Model->setViewDocSearch;
				$view = "setViewDocSearch";
				$_order = $this->Model->OrderDocSearch;
				$order = "OrderDocSearch";
				$_anzahl = $this->Model->anzahlDocSearch;
				$anzahl = "anzahlDocSearch";
				$searchstart = "searchstartDocSearch";
				break;
			case self::SEARCH_TMPL :
				$_view = $this->Model->setViewTmplSearch;
				$view = "setViewTmplSearch";
				$_order = $this->Model->OrderTmplSearch;
				$order = "OrderTmplSearch";
				$_anzahl = $this->Model->anzahlTmplSearch;
				$anzahl = "anzahlTmplSearch";
				$searchstart = "searchstartTmplSearch";
				break;
			case self::SEARCH_MEDIA :
				$_view = $this->Model->setViewMediaSearch;
				$view = "setViewMediaSearch";
				$_order = $this->Model->OrderMediaSearch;
				$order = "OrderMediaSearch";
				$_anzahl = $this->Model->anzahlMediaSearch;
				$anzahl = "anzahlMediaSearch";
				$searchstart = "searchstartMediaSearch";
				break;
			case self::SEARCH_ADV :
				$_view = $this->Model->setViewAdvSearch;
				$view = "setViewAdvSearch";
				$_order = $this->Model->OrderAdvSearch;
				$order = "OrderAdvSearch";
				$_anzahl = $this->Model->anzahlAdvSearch;
				$anzahl = "anzahlAdvSearch";
				$searchstart = "searchstartAdvSearch";
				break;
		}

		$values = array(10 => 10, 25 => 25, 50 => 50, 100 => 100);

		return we_html_element::htmlHiddens(array(
				$view => $_view,
				"position" => '',
				$order => $_order,
				"do" => ''
			)) . '
<table class="default">
<tr>
 <td>' . we_html_tools::getPixel(30, 12) . '</td>
 <td style="font-size:12px;width:125px;">' . g_l('searchtool', '[eintraege_pro_seite]') . ':</td>
 <td class="defaultgray" style="width:60px;">
 ' . we_html_tools::htmlSelect($anzahl, $values, 1, $_anzahl, "", array('onchange' => 'this.form.elements["' . $searchstart . '"].value=0;search(false);')) . '</td>
 <td style="width:400px;">' . $this->getNextPrev($foundItems, $whichSearch) . '</td>
 <td style="width:35px;">' . we_html_button::create_button("fa:iconview,fa-lg fa-th", "javascript:weSearch.setView('" . self::VIEW_ICONS . "');", true, "", "", "", "", false) . '</td>
 <td>' . we_html_button::create_button("fa:listview,fa-lg fa-align-justify", "javascript:weSearch.setView('" . self::VIEW_LIST . "');", true, "", "", "", "", false) . '</td>
</tr>
<tr>
	<td colspan="12">' . we_html_tools::getPixel(1, 12) . '</td>
</tr>
</table>';
	}

	function getSearchParameterBottom($foundItems, $whichSearch){
		$resetButton = (permissionhandler::hasPerm('RESET_VERSIONS') && $whichSearch === "AdvSearch" ?
				we_html_button::create_button("reset", "javascript:.weSearch.resetVersions();", true, 100, 22, "", "") :
				'');

		switch($whichSearch){
			case self::SEARCH_ADV:
			case self::SEARCH_DOCS:
				if(permissionhandler::hasPerm('PUBLISH')){
					$actionButtonCheckboxAll = we_html_forms::checkbox(1, 0, "action_all_" . $whichSearch, "", false, "middlefont", "weSearch.checkAllActionChecks('" . $whichSearch . "')");
					$actionButton = we_html_button::create_button(we_html_button::PUBLISH, "javascript:weSearch.publishDocs('" . $whichSearch . "');", true, 100, 22, "", "");
					$publishButtonCheckboxAll = we_html_forms::checkbox(1, 0, "publish_all_" . $whichSearch, "", false, "middlefont", "weSearch.checkAllPubChecks('" . $whichSearch . "')");
					$publishButton = we_html_button::create_button(we_html_button::PUBLISH, "javascript:weSearch.publishDocs('" . $whichSearch . "');", true, 100, 22, "", "");
					break;
				}
				$actionButton = $actionButtonCheckboxAll = $publishButton = $publishButtonCheckboxAll = "";
				break;
			case self::SEARCH_MEDIA:
				$actionButtonCheckboxAll = we_html_forms::checkbox(1, 0, "action_all_" . $whichSearch, "", false, "middlefont", "weSearch.checkAllActionChecks('" . $whichSearch . "')");
				$actionButton = we_html_button::create_button(we_html_button::DELETE, "javascript:weSearch.deleteDocs('" . $whichSearch . "');", true, 100, 22, "", "");
				$publishButton = $publishButtonCheckboxAll = "";
				break;
			default:
				$actionButton = $actionButtonCheckboxAll = $publishButton = $publishButtonCheckboxAll = "";
		}


		return '<table class="default" style="margin-top:10px;">
<tr>
	 <td style="padding-bottom:10px;">' . $actionButtonCheckboxAll . '</td>
	 <td style="font-size:12px;width:140px;">' . $actionButton . '</td>
	 <td style="width:60px;" id="resetBusy' . $whichSearch . '"></td>
	 <td style="width:400px;">' . $resetButton . '</td>
</tr>
<tr>
	<td>' . we_html_tools::getPixel(19, 12) . '</td>
	<td style="font-size:12px;width:140px;">' . we_html_tools::getPixel(30, 12) . '</td>
	<td class="defaultgray" style="width:60px;">' . we_html_tools::getPixel(30, 12) . '</td>
	<td style="width:400px;">' . $this->getNextPrev($foundItems, $whichSearch, false) . '</td>
</tr>
</table>';
	}

	// FIXME: is obsolete as soon as getSearchDialogOptionalFields() works properly
	function getSearchDialogAdvSearch(){
		if((!empty($_SESSION['weS']['weSearch']["keyword"])) && (we_base_request::_(we_base_request::INT, "tab") == 3)){
			$this->Model->searchAdvSearch[0] = $_SESSION['weS']['weSearch']["keyword"];
			if($GLOBALS['WE_BACKENDCHARSET'] === "UTF-8"){
				$this->Model->searchAdvSearch[0] = utf8_encode($this->Model->searchAdvSearch[0]);
			}
			unset($_SESSION['weS']['weSearch']["keyword"]);
		}

		$this->searchclass->height = count($this->Model->searchFieldsAdvSearch);

		$cmd = we_base_request::_(we_base_request::STRING, 'cmd');
		$cmdid = we_base_request::_(we_base_request::INT, 'cmdid');

		if(isset($_REQUEST["searchFieldsAdvSearch"])){
			if($cmdid !== false){
				if($cmdid != ""){
					$this->searchclass->height = count($this->Model->searchFieldsAdvSearch);
				} elseif($cmd != "" && $cmd != "tool_weSearch_save"){
					$this->searchclass->height = 1;
				}
			} else {
				$this->searchclass->height = count($_REQUEST["searchFieldsAdvSearch"]);
			}
		} else {
			if($cmdid !== false){
				if($cmdid){
					$this->searchclass->height = count($this->Model->searchFieldsAdvSearch);
				} elseif(!$cmd){
					$this->searchclass->height = 0;
				} elseif($cmd != "tool_weSearch_save"){
					$this->searchclass->height = 1;
				}
			} else {
				$this->searchclass->height = (isset($this->Model->searchFieldsAdvSearch[0]) ?
						count($this->Model->searchFieldsAdvSearch) : 1);
			}
		}
		//if own search was saved without fields
		if(!$this->Model->searchFieldsAdvSearch && !$this->Model->predefined){
			$this->searchclass->height = 0;
		}

		$out = '<div style="margin-left:123px;"><div id="mouseOverDivs_AdvSearch"></div><table>
<tbody id="filterTableAdvSearch">
<tr>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
</tr>';

		$locationAdvSearch = we_base_request::_(we_base_request::STRING, 'locationAdvSearch');
		$this->Model->locationAdvSearch = ($locationAdvSearch && is_array($locationAdvSearch) ?
				$locationAdvSearch :
				is_array($this->Model->locationAdvSearch) ? array_values($this->Model->locationAdvSearch) :
					array());

		$this->Model->searchAdvSearch = is_array($this->Model->searchAdvSearch) ?
			array_values($this->Model->searchAdvSearch) :
			array();

		$this->Model->searchFieldsAdvSearch = is_array($this->Model->searchFieldsAdvSearch) ?
			array_values($this->Model->searchFieldsAdvSearch) :
			array();

		for($i = 0; $i < $this->searchclass->height; $i++){
			$button = we_html_button::create_button(we_html_button::TRASH, 'javascript:weSearch.delRow(' . $i . ');', true, '', '', '', '', false);

			$locationDisabled = $handle = '';

			$searchInput = we_html_tools::htmlTextInput('searchAdvSearch[' . $i . ']', 30, ( isset($this->Model->searchAdvSearch) && is_array($this->Model->searchAdvSearch) && isset($this->Model->searchAdvSearch[$i]) ? $this->Model->searchAdvSearch[$i] : ''), "", " class=\"wetextinput\"  id=\"searchAdvSearch[" . $i . "]\" ", "search", 170);

			if(isset($this->Model->searchFieldsAdvSearch[$i])){
				switch($this->Model->searchFieldsAdvSearch[$i]){
					case "ParentIDDoc":
					case "ParentIDObj":
					case "ParentIDTmpl":
					case "Content":
					case "Status":
					case "Speicherart":
					case "MasterTemplateID":
					case "temp_template_id":
					case "temp_category":
						$locationDisabled = "disabled";
				}

				switch($this->Model->searchFieldsAdvSearch[$i]){
					case "allModsIn":
						$searchInput = we_html_tools::htmlSelect("searchAdvSearch[" . $i . "]", $this->searchclass->getModFields(), 1, ( isset($this->Model->searchAdvSearch) && is_array($this->Model->searchAdvSearch) && isset($this->Model->searchAdvSearch[$i]) ? $this->Model->searchAdvSearch[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => 'searchAdvSearch[' . $i . ']'));
						break;

					case "Status":
						$searchInput = we_html_tools::htmlSelect("searchAdvSearch[" . $i . "]", $this->searchclass->getFieldsStatus(), 1, (isset($this->Model->searchAdvSearch) && is_array($this->Model->searchAdvSearch) && isset($this->Model->searchAdvSearch[$i]) ? $this->Model->searchAdvSearch[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => 'searchAdvSearch[' . $i . ']'));
						break;

					case "Speicherart":
						$searchInput = we_html_tools::htmlSelect("searchAdvSearch[" . $i . "]", $this->searchclass->getFieldsSpeicherart(), 1, (isset($this->Model->searchAdvSearch) && is_array($this->Model->searchAdvSearch) && isset($this->Model->searchAdvSearch[$i]) ? $this->Model->searchAdvSearch[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => 'searchAdvSearch[' . $i . ']'));
						break;

					case "Published":
					case "CreationDate":
					case "ModDate":
						$handle = "date";
						$searchInput = we_html_tools::getDateSelector("searchAdvSearch[" . $i . "]", "_from" . $i, $this->Model->searchAdvSearch[$i]);
						break;

					case "ParentIDDoc":
					case "ParentIDObj":
					case "ParentIDTmpl":
						$_linkPath = $this->Model->searchAdvSearch[$i];

						$_rootDirID = 0;
						$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['searchAdvSearchParentID[" . $i . "]'].value");
						$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['searchAdvSearch[" . $i . "]'].value");
						$_cmd = "javascript:we_cmd('we_selector_directory',document.we_form.elements['searchAdvSearchParentID[" . $i . "]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $_rootDirID . "','','')";
						$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(
								we_html_tools::htmlTextInput('searchAdvSearch[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('searchAdvSearchParentID[' . $i . ']', ""), we_html_tools::getPixel(5, 4), $_button);

						$searchInput = $selector;
						break;
					case "MasterTemplateID":
					case "temp_template_id":
						$_linkPath = $this->Model->searchAdvSearch[$i];

						$_rootDirID = 0;
						$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['searchAdvSearchParentID[" . $i . "]'].value");
						$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['searchAdvSearch[" . $i . "]'].value");

						$_cmd = "javascript:we_cmd('we_selector_document',document.we_form.elements['searchAdvSearchParentID[" . $i . "]'].value,'" . TEMPLATES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $_rootDirID . "','','" . we_base_ContentTypes::TEMPLATE . "')";
						$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(
								we_html_tools::htmlTextInput(
									'searchAdvSearch[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden(
									array(
										'name' => 'searchAdvSearchParentID[' . $i . ']', "value" => ""
								)), we_html_tools::getPixel(5, 4), $_button);

						$searchInput = $selector;
						break;
					case "temp_category":
						$_linkPath = $this->Model->searchAdvSearch[$i];
						$_rootDirID = 0;

						$_cmd = "javascript:we_cmd('we_selector_category',document.we_form.elements['searchAdvSearchParentID[" . $i . "]'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'searchAdvSearchParentID[" . $i . "]\\'].value','document.we_form.elements[\\'searchAdvSearch[" . $i . "]\\'].value','','','" . $_rootDirID . "','','')";
						$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(
								we_html_tools::htmlTextInput(
									'searchAdvSearch[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden(
									array(
										'name' => 'searchAdvSearchParentID[' . $i . ']', "value" => ""
								)), we_html_tools::getPixel(5, 4), $_button);

						$searchInput = $selector;
				}
			}

			$out .= '<tr id="filterRow_' . $i . '">
	<td>' . we_html_tools::hidden("hidden_searchFieldsAdvSearch[" . $i . "]", isset($this->Model->searchFieldsAdvSearch[$i]) ? $this->Model->searchFieldsAdvSearch[$i] : "") .
				we_html_tools::htmlSelect("searchFieldsAdvSearch[" . $i . "]", $this->searchclass->getFields($i, 'AdvSearch'), 1, (isset($this->Model->searchFieldsAdvSearch) && is_array($this->Model->searchFieldsAdvSearch) && isset($this->Model->searchFieldsAdvSearch[$i]) ? $this->Model->searchFieldsAdvSearch[$i] : ""), false, array('class' => "defaultfont", 'id' => 'searchFieldsAdvSearch[' . $i . ']', 'onchange' => 'weSearch.changeit(this.value, ' . $i . ');')) .
				'</td>
	<td id="td_locationAdvSearch[' . $i . ']">' . we_html_tools::htmlSelect("locationAdvSearch[" . $i . "]", we_search_search::getLocation($handle), 1, (isset($this->Model->locationAdvSearch) && is_array($this->Model->locationAdvSearch) && isset($this->Model->locationAdvSearch[$i]) ? $this->Model->locationAdvSearch[$i] : ""), false, array('class' => "defaultfont", $locationDisabled => $locationDisabled, 'id' => 'locationAdvSearch[' . $i . ']')) . '</td>
	<td id="td_searchAdvSearch[' . $i . ']">' . $searchInput . '</td>
	<td id="td_delButton[' . $i . ']">' . $button . '</td>
	</tr>';
		}

		$out .= '</tbody></table>' .
			'<table>
<tr>
<td>' . we_html_button::create_button(we_html_button::ADD, "javascript:weSearch.newinputAdvSearch();") . '</td>
<td>' . we_html_tools::getPixel(10, 10) . '</td>
<td colspan="7" style="text-align:right"></td>
</tr>
</table></div>' .
			we_html_element::jsElement("weSearch.calendarSetup(" . $this->searchclass->height . ");");

		return $out;
	}

	function getSearchDialogOptFields($whichSearch){
		if($whichSearch !== self::SEARCH_ADV && $whichSearch !== self::SEARCH_MEDIA){
			return;
		}

		$searchWhichSearch = $whichSearch === self::SEARCH_ADV ? "searchAdvSearch" : "searchMediaSearch";
		$searchFieldsWhichSearch = $whichSearch === self::SEARCH_ADV ? "searchFieldsAdvSearch" : "searchFieldsMediaSearch";
		$locationWhichSearch = $whichSearch === self::SEARCH_ADV ? "locationAdvSearch" : "locationMediaSearch";

		if((!empty($_SESSION['weS']['weSearch']['keyword'])) && (we_base_request::_(we_base_request::INT, 'tab') === ($whichSearch === self::SEARCH_ADV ? 3 : (self::SEARCH_MEDIA ? 5 : -1)))){
			$this->Model->$searchWhichSearch[0] = $_SESSION['weS']['weSearch']['keyword'];
			if($GLOBALS['WE_BACKENDCHARSET'] === "UTF-8"){
				$this->Model->$searchWhichSearch[0] = utf8_encode($this->Model->$searchWhichSearch[0]);
			}
			unset($_SESSION['weS']['weSearch']["keyword"]);
		}

		$this->searchclass->height = count($this->Model->$searchFieldsWhichSearch);

		$cmd = we_base_request::_(we_base_request::STRING, 'cmd');
		$cmdid = we_base_request::_(we_base_request::INT, 'cmdid');

		if(isset($_REQUEST[$searchFieldsWhichSearch])){
			if($cmdid !== false){
				if($cmdid != ""){
					$this->searchclass->height = count($this->Model->$searchFieldsWhichSearch);
				} elseif($cmd != "" && $cmd != "tool_weSearch_save"){
					$this->searchclass->height = 1;
				}
			} else {
				$this->searchclass->height = count($_REQUEST[$searchFieldsWhichSearch]);
			}
		} else {
			if($cmdid !== false){
				if($cmdid){
					$this->searchclass->height = count($this->Model->$searchFieldsWhichSearch);
				} elseif(!$cmd){
					$this->searchclass->height = 0;
				} elseif($cmd != "tool_weSearch_save"){
					$this->searchclass->height = 1;
				}
			} else {
				$this->searchclass->height = (isset($this->Model->$searchFieldsWhichSearch[0]) ?
						count($this->Model->$searchFieldsWhichSearch) : 1);
			}
		}
		//if own search was saved without fields
		if(!$this->Model->$searchFieldsWhichSearch && !$this->Model->predefined){
			$this->searchclass->height = 0;
		}

		$out = '<div ' . ($whichSearch === self::SEARCH_MEDIA ? '' : 'style="margin-left:123px;"') . '><div id="mouseOverDivs_' . $whichSearch . '"></div><table>
<tbody id="filterTable' . $whichSearch . '">
<tr>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
 <td></td>
</tr>';

		$$locationWhichSearch = we_base_request::_(we_base_request::STRING, $locationWhichSearch);
		$this->Model->$locationWhichSearch = ($locationWhichSearch && is_array($locationWhichSearch) ?
				$$locationWhichSearch :
				is_array($this->Model->$locationWhichSearch) ? array_values($this->Model->$locationWhichSearch) :
					array());

		$this->Model->$searchWhichSearch = is_array($this->Model->$searchWhichSearch) ?
			array_values($this->Model->$searchWhichSearch) :
			array();

		$this->Model->$searchFieldsWhichSearch = is_array($this->Model->$searchFieldsWhichSearch) ?
			array_values($this->Model->$searchFieldsWhichSearch) :
			array();

		for($i = ($whichSearch === self::SEARCH_MEDIA ? $this->searchMediaOptFieldIndex : 0); $i < $this->searchclass->height; $i++){
			$button = we_html_button::create_button(we_html_button::TRASH, 'javascript:weSearch.delRow(' . $i . ');', true, '', '', '', '', false);

			$locationDisabled = $handle = '';

			$searchInput = we_html_tools::htmlTextInput($searchWhichSearch . '[' . $i . ']', 30, ( isset($this->Model->$searchWhichSearch) && is_array($this->Model->$searchWhichSearch) && isset($this->Model->$searchWhichSearch[$i]) ? $this->Model->$searchWhichSearch[$i] : ''), "", " class=\"wetextinput\"  id=\"' . $searchWhichSearch . '[" . $i . "]\" ", "search", 170);

			if(isset($this->Model->$searchFieldsWhichSearch[$i])){
				switch($this->Model->$searchFieldsWhichSearch[$i]){
					case "ParentIDDoc":
					case "ParentIDObj":
					case "ParentIDTmpl":
					case "Content":
					case "Status":
					case "Speicherart":
					case "MasterTemplateID":
					case "temp_template_id":
					case "temp_category":
						$locationDisabled = "disabled";
				}

				switch($this->Model->$searchFieldsWhichSearch[$i]){
					case "allModsIn":
						$searchInput = we_html_tools::htmlSelect($searchWhichSearch . "[" . $i . "]", $this->searchclass->getModFields(), 1, ( isset($this->Model->$searchWhichSearch) && is_array($this->Model->$searchWhichSearch) && isset($this->Model->$searchWhichSearch[$i]) ? $this->Model->$searchWhichSearch[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => $searchWhichSearch . '[' . $i . ']'));
						break;

					case "Status":
						$searchInput = we_html_tools::htmlSelect($searchWhichSearch . "[" . $i . "]", $this->searchclass->getFieldsStatus(), 1, (isset($this->Model->$searchWhichSearch) && is_array($this->Model->$searchWhichSearch) && isset($this->Model->$searchWhichSearch[$i]) ? $this->Model->$searchWhichSearch[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => $searchWhichSearch . '[' . $i . ']'));
						break;

					case "Speicherart":
						$searchInput = we_html_tools::htmlSelect($searchWhichSearch . "[" . $i . "]", $this->searchclass->getFieldsSpeicherart(), 1, (isset($this->Model->$searchWhichSearch) && is_array($this->Model->$searchWhichSearch) && isset($this->Model->$searchWhichSearch[$i]) ? $this->Model->$searchWhichSearch[$i] : ""), false, array('class' => "defaultfont", 'style' => "width:170px;", 'id' => $searchWhichSearch . '[' . $i . ']'));
						break;

					case "Published":
					case "CreationDate":
					case "ModDate":
						$handle = "date";
						$searchInput = we_html_tools::getDateSelector($searchWhichSearch . "[" . $i . "]", "_from" . $i, $this->Model->$searchWhichSearch[$i]);
						break;

					case "ParentIDDoc":
					case "ParentIDObj":
					case "ParentIDTmpl":
						$_linkPath = $this->Model->$searchWhichSearch[$i];

						$_rootDirID = 0;
						$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $searchWhichSearch . "ParentID[" . $i . "]'].value");
						$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $searchWhichSearch . "[" . $i . "]'].value");
						$_cmd = "javascript:we_cmd('we_selector_directory',document.we_form.elements['" . $searchWhichSearch . "ParentID[" . $i . "]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $_rootDirID . "','','')";
						$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(
								we_html_tools::htmlTextInput($searchWhichSearch . '[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden($searchWhichSearch . 'ParentID[' . $i . ']', ""), we_html_tools::getPixel(5, 4), $_button);

						$searchInput = $selector;
						break;
					case "MasterTemplateID":
					case "temp_template_id":
						$_linkPath = $this->Model->$searchWhichSearch[$i];

						$_rootDirID = 0;
						$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $searchWhichSearch . "ParentID[" . $i . "]'].value");
						$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $searchWhichSearch . "[" . $i . "]'].value");

						$_cmd = "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $searchWhichSearch . "ParentID[" . $i . "]'].value,'" . TEMPLATES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $_rootDirID . "','','" . we_base_ContentTypes::TEMPLATE . "')";
						$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(
								we_html_tools::htmlTextInput(
									$searchWhichSearch . '[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden(
									array(
										'name' => $searchWhichSearch . 'ParentID[' . $i . ']', "value" => ""
								)), we_html_tools::getPixel(5, 4), $_button);

						$searchInput = $selector;
						break;
					case "temp_category":
						$_linkPath = $this->Model->$searchWhichSearch[$i];
						$_rootDirID = 0;

						$_cmd = "javascript:we_cmd('we_selector_category',document.we_form.elements['" . $searchWhichSearch . "ParentID[" . $i . "]'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'" . $searchWhichSearch . "ParentID[" . $i . "]\\'].value','document.we_form.elements[\\'" . $searchWhichSearch . "[" . $i . "]\\'].value','','','" . $_rootDirID . "','','')";
						$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 70, 22, '', '', false);
						$selector = we_html_tools::htmlFormElementTable(
								we_html_tools::htmlTextInput(
									$searchWhichSearch . '[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 170, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden(
									array(
										'name' => $searchWhichSearch . 'ParentID[' . $i . ']', "value" => ""
								)), we_html_tools::getPixel(5, 4), $_button);

						$searchInput = $selector;
				}
			}

			$out .= '<tr id="filterRow_' . $i . '">
     <td>' . we_html_tools::hidden("hidden_" . $searchFieldsWhichSearch . "[" . $i . "]", isset($this->Model->$searchFieldsWhichSearch[$i]) ? $this->Model->$searchFieldsWhichSearch[$i] : "") .
				we_html_tools::htmlSelect($searchFieldsWhichSearch . "[" . $i . "]", $this->searchclass->getFields($i, $whichSearch), 1, (isset($this->Model->$searchFieldsWhichSearch) && is_array($this->Model->$searchFieldsWhichSearch) && isset($this->Model->$searchFieldsWhichSearch[$i]) ? $this->Model->$searchFieldsWhichSearch[$i] : ""), false, array('class' => "defaultfont", 'id' => $searchFieldsWhichSearch . '[' . $i . ']', 'onchange' => 'weSearch.changeit(this.value, ' . $i . ');')) .
				'</td>
     <td id="td_' . $locationWhichSearch . '[' . $i . ']">' . we_html_tools::htmlSelect($locationWhichSearch . "[" . $i . "]", we_search_search::getLocation($handle), 1, (isset($this->Model->$locationWhichSearch) && is_array($this->Model->$locationWhichSearch) && isset($this->Model->$locationWhichSearch[$i]) ? $this->Model->$locationWhichSearch[$i] : ""), false, array('class' => "defaultfont", $locationDisabled => $locationDisabled, 'id' => $locationWhichSearch . '[' . $i . ']')) . '</td>
     <td id="td_' . $searchWhichSearch . '[' . $i . ']">' . $searchInput . '</td>
     <td id="td_delButton[' . $i . ']">' . $button . '</td>
    </tr>';
		}

		$out .= '</tbody></table>' .
			'<table>
<tr>
 <td>' . we_html_button::create_button(we_html_button::ADD, "javascript:weSearch.newinputAdvSearch();") . '</td>
 <td>' . we_html_tools::getPixel(10, 10) . '</td>
 <td colspan="7" style="text-align:right"></td>
</tr>
</table></div>' .
			we_html_element::jsElement("weSearch.calendarSetup(" . $this->searchclass->height . ");");

		return $out;
	}

	function tblList($content, $headline, $whichSearch){
		$class = "middlefont";
		$view = self::VIEW_LIST;

		switch($whichSearch){
			case self::SEARCH_DOCS :
				$view = $this->Model->setViewDocSearch;
				break;
			case self::SEARCH_TMPL :
				$view = $this->Model->setViewTmplSearch;
				break;
			case self::SEARCH_DOCS :
				$view = $this->Model->setViewMediaSearch;
				break;
			case self::SEARCH_ADV :
				$view = $this->Model->setViewAdvSearch;
				break;
			// for doclistsearch
			case "doclist" :
				$view = $GLOBALS['we_doc']->searchclassFolder->setView;
		}

		$anz = count($headline);
		$out = '<table style="table-layout:fixed;white-space:nowrap;width:100%;padding:0 0 0 0;margin:0 0 0 0;background-color:#fff;border-bottom:1px solid #D1D1D1;" >' .
			($whichSearch !== self::SEARCH_MEDIA ? '<colgroup>
<col style="width:30px;text-align:center;"/>
<col style="width:2%;text-align:left;"/>
<col style="width:28%;text-align:left;"/>
<col style="width:36%;text-align:left;"/>
<col style="width:15%;text-align:left;"/>
<col style="width:18%;text-align:left;"/>
</colgroup>' :
				'<colgroup>
<col style="width:30px;text-align:center;"/>
<col style="width:80px;text-align:center;"/>
<col style="text-align:left;"/>
<col style="width:70px;text-align:left;"/>
<col style="width:45px;text-align:left;"/>
<col style="width:45px;text-align:left;"/>
<col style="width:45px;text-align:left;"/>
<col style="width:90px;text-align:left;"/>
<col style="width:90px;text-align:left;"/>
<col style="width:40px;text-align:left;"/>
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
			'<div class="largeicons" id="scrollContent_' . $whichSearch . '" style="overflow-y:auto;background-color:#fff;width:100%;height:100%;">' .
			$this->tabListContent($view, $content, $class, $whichSearch) .
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
<col style="width:2%;text-align:left;"/>
<col style="width:28%;text-align:left;"/>
<col style="width:36%;text-align:left;"/>
<col style="width:15%;text-align:left;"/>
<col style="width:18%;text-align:left;"/>
</colgroup>' : '
<colgroup>
<col style="width:30px;text-align:center;"/>
<col style="width:80px;text-align:left;"/>
<col style="text-align:left;"/>
</colgroup>
';

				for($m = 0; $m < $x; $m++){
					$out .= '<tr>' . ($whichSearch === 'doclist' ? self::tblListRow($content[$m]) :
							($whichSearch === self::SEARCH_MEDIA ? self::tblListRowMedia($content[$m]) : self::tblListRow($content[$m]))) . '</tr>';
				}
				return $out . '</tbody></table>';
			case self::VIEW_ICONS:
				$out = '<table class="default" width="100%"><tr><td style="text-align:center">';

				for($m = 0; $m < $x; $m++){
					$out .= $whichSearch !== self::SEARCH_MEDIA ? ('<div style="float:left;width:180px;height:100px;margin:20px 0px 0px 20px;z-index:1;">' .
						self::tblListRowIconView($content[$m], $class, $m, $whichSearch)
						. '</div>') :
						('<div style="float:left;width:200px;height:200px;margin:20px 0px 0px 20px;z-index:1;">' .
						self::tblListRowMediaIconView($content[$m], $class, $m, $whichSearch)
						. '</div>');
				}

				$out .= '</td></tr></table>' .
					we_html_element::jsElement("document.getElementById('mouseOverDivs_" . $whichSearch . "').innerHTML = '" . addslashes(self::makeMouseOverDivs($x, $content, $whichSearch)) . "';");

				return $out;
		}
	}

	static function makeMouseOverDivs($x, $content, $whichSearch){
		$allDivs = '';

		for($n = 0; $n < $x; $n++){
			$outDivs = '<div style="position:absolute;left:-9999px;width:400px;text-align:left;z-index:10000;visibility:visible;border:1px solid #bab9ba; border-radius:20px;background-color:#EDEDED;" class="middlefont" id="ImgDetails_' . $n . '_' . $whichSearch . '">
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
					$outDivs .= '<tr><td style="vertical-align:top">' . g_l('searchtool', '[aufloesung]') . ': </td><td>' . $content[$n][7]["dat"] . '</td></tr>';
				//no break;
				case we_base_ContentTypes::APPLICATION:
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
			if($content[$n][11]["dat"]){
				$outDivs .= '<table class="default" style="font-size:10px;"><tr><td style="vertical-align:top">' . g_l('searchtool', '[beschreibung]') . ':</td><td>' . we_html_tools::getPixel(
						15, 5) . '</td><td>' .
					we_base_util::shortenPath($content[$n][11]["dat"], 150) .
					'</td></tr></table>';
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
				</div>
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
			$out .= '<td ' . ($f < 2 ? '' : 'style="font-weight:bold;height:30px;font-size:11px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;"') . '>' . ((!empty($content[$f]["dat"])) ? $content[$f]["dat"] : "&nbsp;") . '</td>';
		}

		if(isset($content[0]["version"])){
			foreach(array_keys($content[0]["version"]) as $k){
				$out .= '</tr><tr><td style="width:20px;">' . we_html_tools::getPixel(20, 10) . '</td>';
				for($y = 0; $y < $anz; $y++){
					$out .= '<td style="font-weight:bold;font-size:11px;' . ($f == 0 ? "width:30px;" : '') . '">' .
						we_html_tools::getPixel(5, 10) .
						$content[$y]["version"][$k] .
						'</td>';
				}

				$out .= '</tr><tr><td style="width:20px;">' . we_html_tools::getPixel(20, 10) . '</td>';
				for($y = 0; $y < $anz; $y++){
					$out .= '<td style="font-weight:bold;font-size:11px;' . ($f == 0 ? "width:30px;" : '') . '">' . ($y == 2 ?
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
		return '<table width="100%" class="default ' . $class . '">
<tr>
	<td width="75" style="vertical-align:top;text-align:center" onmouseover="showImageDetails(\'ImgDetails_' . $i . '_' . $whichSearch . '\',1)" onmouseout="hideImageDetails(\'ImgDetails_' . $i . '_' . $whichSearch . '\')">' .
			((!empty($content[0]["dat"])) ? $content[0]["dat"] : "&nbsp;") . '</td>
		<td width="105" style="vertical-align:top;line-height:20px;">
		<div style="padding-bottom:2em;">' . ((!empty($content[2]["dat"])) ? $content[2]["dat"] : "&nbsp;") . '</div>
		<span>' . ((!empty($content[1]["dat"])) ? $content[1]["dat"] : "&nbsp;") . '</span></td>
</tr></table>';
	}

	private static function tblListRowMediaIconView($content, $class, $i, $whichSearch){
		return '<table width="100%" class="default ' . $class . '">
<tr>
	<td width="100%" style="vertical-align:top;text-align:center" onmouseover="showImageDetails(\'ImgDetails_' . $i . '_' . $whichSearch . '\',1)" onmouseout="hideImageDetails(\'ImgDetails_' . $i . '_' . $whichSearch . '\')">' .
			((!empty($content[5]["dat"])) ? $content[5]["dat"] : "&nbsp;") .
			'</td>
</tr>
<tr>
		<td width="100%" style="vertical-align:top;line-height:20px;text-align:center">
		<span>' . ((!empty($content[2]["dat"])) ? $content[2]["dat"] : "&nbsp;") . '</span>
</tr></table>';
	}

	function getDirSelector($whichSearch){
		$yuiSuggest = & weSuggest::getInstance();
		switch($whichSearch){
			case self::SEARCH_DOCS :
				$folderID = "folderIDDoc";
				$folderPath = "folderPathDoc";
				$table = FILE_TABLE;
				$pathID = $this->Model->folderIDDoc;
				$ACname = "docu";
				$yuiSuggest->setWidth(380);
				break;
			case self::SEARCH_MEDIA :
				$folderID = "folderIDMedia";
				$folderPath = "folderPathMedia";
				$table = FILE_TABLE;
				$pathID = $this->Model->folderIDMedia;
				$ACname = "docu";
				$yuiSuggest->setWidth(380);
				break;
			case self::SEARCH_TMPL :
				$folderID = "folderIDTmpl";
				$folderPath = "folderPathTmpl";
				$table = TEMPLATES_TABLE;
				$pathID = $this->Model->folderIDTmpl;
				$ACname = "Tmpl";
				$yuiSuggest->setWidth(380);
				break;
		}

		$_path = id_to_path($pathID, $table, $this->db);


		$yuiSuggest->setAcId($ACname);
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($folderPath, $_path);
		$yuiSuggest->setLabel("");
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($folderID, $pathID);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable($table);
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $folderID . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $folderPath . "'].value");
		$yuiSuggest->setSelectButton(
			we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements['" . $folderID . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "')"));

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

	function getPropertyJSAdditional(){
		return '';
	}

	function getJSProperty(){
		return we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
			we_html_element::jsElement('
var loaded=0;
function we_cmd() {
	var args = "";
	var url = WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(window, url,"we_docselector",-1,-1,WE().consts.size.docSelect.width,WE().consts.size.docSelect.height,true,true,true,true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(window, url,"we_selector",-1,-1,WE().consts.size.windowSelect.width,WE().consts.size.windowSelect.height,true,true,true,true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(window, url,"we_selector",-1,-1,WE().consts.size.windowDirSelect.width,WE().consts.size.windowDirSelect.height,true,true,true,true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(window, url,"we_catselector",-1,-1,WE().consts.size.catSelect.width,WE().consts.size.catSelect.height,true,true,true,true);
			break;
		case "openweSearchDirselector":
			url = WE().consts.dirs.WEBEDITION_DIR+"apps/weSearch/we_weSearchDirSelect.php?";
			for(var i = 0; i < arguments.length; i++){
				url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }
			}
			new (WE().util.jsWindow)(window, url,"we_weSearch_dirselector",-1,-1,600,400,true,true,true);
			break;
			' . $this->getPropertyJSAdditional() . '
		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			' . $this->topFrame . '.we_cmd.apply(this, args);
	}
}
function submitForm() {
	var f = self.document.we_form;
	f.target = (arguments[0]?arguments[0]:"edbody");
	f.action = (arguments[1]?arguments[1]:"' . $this->frameset . '");
	f.method = (arguments[2]?arguments[2]:"post");
	f.submit();
}');
	}

	function getJSSubmitFunction(){
		return '';
	}

	function processVariables(){
		if(isset($_SESSION['weS'][$this->toolName . '_session'])){
			$this->Model = $_SESSION['weS'][$this->toolName . '_session'];
		}

		if(is_array($this->Model->persistent_slots)){
			foreach($this->Model->persistent_slots as $val){
				if(($tmp = we_base_request::_(we_base_request::STRING, $val))){
					$this->Model->$val = $tmp;
				}
			}
		}
	}

}
