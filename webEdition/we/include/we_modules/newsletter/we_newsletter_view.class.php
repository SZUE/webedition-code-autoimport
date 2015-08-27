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
/* the parent class of storagable webEdition classes */

class we_newsletter_view extends we_modules_view{
	const MAILS_ALL = 0;
	const MAILS_CUSTOMER = 1;
	const MAILS_EMAILS = 2;
	const MAILS_FILE = 3;

	var $db;
	// settings array; format settings[setting_name]=settings_value
	var $settings = array();
	//default newsletter
	var $newsletter;
	//wat page is currentlly displayed 0-properties(default);1-overview;
	var $page = 0;
	var $get_import = 0;
	var $hiddens = array('ID');
	var $customers_fields;
	var $frameset;
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;
	protected $jsonOnly = false;
	protected $show_import_box = -1;
	protected $show_export_box = -1;

	public function __construct(){
		parent::__construct(WE_NEWSLETTER_MODULE_DIR . 'edit_newsletter_frameset.php', '');

		$this->newsletter = new we_newsletter_newsletter();

		$this->settings = self::getSettings();
		//FIXME: add types for settings

		if(defined('CUSTOMER_TABLE')){
			$this->customers_fields = array();
			$this->db->query('SHOW FIELDS FROM ' . CUSTOMER_TABLE);
			while($this->db->next_record()){
				$this->customers_fields[] = $this->db->f('Field');
			}
		}
		$this->newsletter->Text = g_l('modules_newsletter', '[new_newsletter]');
		$this->newsletter->Sender = $this->settings['default_sender'];
		$this->newsletter->Reply = $this->settings['default_reply'];
		$this->newsletter->Test = $this->settings['test_account'];
		$this->newsletter->isEmbedImages = $this->settings['isEmbedImages'];
	}

	function setFrames($topFrame, $treeFrame, $cmdFrame){
		$this->topFrame = $topFrame;
		$this->treeFrame = $treeFrame;
		$this->cmdFrame = $cmdFrame;
	}

	function getHiddens($predefs = array()){
		return $this->htmlHidden('ncmd', (isset($predefs['ncmd']) ? $predefs['ncmd'] : 'new_newsletter')) .
			$this->htmlHidden('we_cmd[0]', 'show_newsletter') .
			$this->htmlHidden('nid', (isset($predefs['nid']) ? $predefs['nid'] : $this->newsletter->ID)) .
			$this->htmlHidden('pnt', (isset($predefs['pnt']) ? $predefs['pnt'] : we_base_request::_(we_base_request::RAW, 'pnt'))) .
			$this->htmlHidden('page', (isset($predefs['page']) ? $predefs['page'] : $this->page)) .
			$this->htmlHidden('gview', (isset($predefs['gview']) ? $predefs['gview'] : 0)) .
			$this->htmlHidden('hm', (isset($predefs['hm']) ? $predefs['hm'] : 0)) .
			$this->htmlHidden('ask', (isset($predefs['ask']) ? $predefs['ask'] : 1)) .
			$this->htmlHidden('test', (isset($predefs['test']) ? $predefs['test'] : 0));
	}

	function newsletterHiddens(){
		$out = '';
		foreach($this->hiddens as $val){
			$out .= $this->htmlHidden($val, (isset($this->newsletter->persistents[$val]) ?
					$this->newsletter->$val : $this->$val)
			);
		}

		return $out;
	}

	function getHiddensProperty(){
		$out = '';
		$counter = 0;
		$val = '';

		foreach($this->newsletter->groups as $group){

			foreach(array_keys($group->persistents) as $per){
				$val = $group->$per;
				$out .= $this->htmlHidden('group' . $counter . '_' . $per, $val);
			}

			$counter++;
		}

		$out .= $this->htmlHidden('groups', $counter) .
			$this->htmlHidden('Step', $this->newsletter->Step) .
			$this->htmlHidden('Offset', $this->newsletter->Offset) .
			$this->htmlHidden('IsFolder', $this->newsletter->IsFolder);
		return $out;
	}

	function getHiddensPropertyPage(){
		return $this->htmlHidden('Text', $this->newsletter->Text) .
			$this->htmlHidden('Subject', $this->newsletter->Subject) .
			$this->htmlHidden('ParentID', $this->newsletter->ParentID) .
			$this->htmlHidden('Sender', $this->newsletter->Sender) .
			$this->htmlHidden('Reply', $this->newsletter->Reply) .
			$this->htmlHidden('Test', $this->newsletter->Test) .
			$this->htmlHidden('Charset', $this->newsletter->Charset) .
			$this->htmlHidden('isEmbedImages', $this->newsletter->isEmbedImages);
	}

	function getHiddensMailingPage(){
		$out = '';

		$fields_names = array('fieldname', 'operator', 'fieldvalue', 'logic', 'hours', 'minutes');
		foreach($this->newsletter->groups as $g => $group){
			$filter = $group->getFilter();
			if($filter){
				$out.=$this->htmlHidden('filter_' . $g, count($filter));

				foreach($filter as $k => $v){
					foreach($fields_names as $field){
						if(isset($v[$field])){
							$out.=$this->htmlHidden('filter_' . $field . '_' . $g . '_' . $k, $v[$field]);
						}
					}
				}
			}
		}

		return $out;
	}

	function getHiddensContentPage(){
		$out = '';
		$counter = 0;

		foreach($this->newsletter->blocks as $bk => $bv){

			foreach(array_keys($this->newsletter->blocks[$bk]->persistents) as $per){
				$out .= $this->htmlHidden('block' . $counter . '_' . $per, $bv->$per);
			}

			$counter++;
		}

		$out .= $this->htmlHidden('blocks', $counter);

		return $out;
	}

	/* creates the DocumentChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDocChooser($width = '', $rootDirID = 0, $Pathname = 'ParentPath', $Pathvalue = '/', $IDName = 'ParentID', $IDValue = 0, $cmd = ''){
		$Pathvalue = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($IDValue), 'Path', $this->db);

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['" . $IDName . "'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "',''," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, '', ' readonly', 'text', $width, 0), '', 'left', 'defaultfont', $this->htmlHidden($IDName, $IDValue), we_html_tools::getPixel(20, 4), $button);
	}

	/* creates the FileChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formFileChooser($width = '', $IDName = 'ParentID', $IDValue = '/', $cmd = '', $filter = '', $acObject = null, $contentType = ''){
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$button = we_html_button::create_button('select', "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value);");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, '', 'readonly', 'text', $width, 0), '', 'left', 'defaultfont', '', we_html_tools::getPixel(20, 4), permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES') ? $button : '');
	}

	function formWeChooser($table = FILE_TABLE, $width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = '', $open_doc = '', $acObject = null, $contentType = ''){
		if(!$Pathvalue){
			$Pathvalue = f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), 'Path', $this->db);
		}

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "','','" . $open_doc . "')");
		if(is_object($acObject)){

			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId($IDName);
			$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::TEMPLATE);
			$yuiSuggest->setInput($Pathname, $Pathvalue);
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult($IDName, $IDValue);
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setTable($table);
			$yuiSuggest->setWidth($width);
			$yuiSuggest->setSelectButton($button);
			return $yuiSuggest->getHTML();
		} else {
			return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, '', 'top.content.hot=1; readonly', 'text', $width, 0), '', 'left', 'defaultfont', $this->htmlHidden($IDName, $IDValue), we_html_tools::getPixel(20, 4), $button);
		}
	}

	function formWeDocChooser($table = FILE_TABLE, $width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = '', $filter = we_base_ContentTypes::WEDOCUMENT, $acObject = null){
		if(!$Pathvalue){
			$Pathvalue = f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), 'Path', $this->db);
		}

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));

		$button = we_html_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "','" . $filter . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")");
		if(is_object($acObject)){

			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId($IDName);
			$yuiSuggest->setContentType(implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH, we_base_ContentTypes::QUICKTIME)));
			$yuiSuggest->setInput($Pathname, $Pathvalue);
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult($IDName, $IDValue);
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setTable($table);
			$yuiSuggest->setWidth($width);
			$yuiSuggest->setSelectButton($button);
			return $yuiSuggest->getHTML();
		} else {
			return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", 'top.content.hot=1; readonly', "text", $width, 0), "", "left", "defaultfont", $this->htmlHidden($IDName, $IDValue), we_html_tools::getPixel(20, 4), $button);
		}
	}

	function formNewsletterDirChooser($width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = '', $acObject = null){
		$table = NEWSLETTER_TABLE;
		if(!$Pathvalue){
			$Pathvalue = f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), 'Path', $this->db);
		}

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));

		$button = we_html_button::create_button('select', "javascript:we_cmd('openNewsletterDirselector',document.we_form.elements['" . $IDName . "'].value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");
		if(is_object($acObject)){
			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId('PathGroup');
			$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$yuiSuggest->setInput($Pathname, str_replace('\\', '/', $Pathvalue));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult($IDName, $IDValue);
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setTable(NEWSLETTER_TABLE);
			$yuiSuggest->setWidth($width);
			$yuiSuggest->setSelectButton($button);

			return $yuiSuggest->getHTML();
		} else {

			return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, '', 'top.content.hot=1; readonly id="yuiAcInputPathGroup"', "text", $width, 0), "", "left", "defaultfont", $this->htmlHidden($IDName, $IDValue), we_html_tools::getPixel(20, 4), $button
			);
		}
	}

	function getFields($id, $table){
		$ClassName = f('SELECT ClassName FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($id), 'ClassName', $this->db);
		$foo = array();

		if($ClassName){
			$ent = new $ClassName();
			$ent->initByID($id, $table);
			$tmp = array_keys($ent->elements);

			foreach($tmp as $v){
				$foo[$v] = $v;
			}
		}

		return $foo;
	}

	function getJSTopCode(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		return we_html_element::jsElement('
var get_focus = 1;
var hot = 0;

function setHot() {
	hot = "1";
}

function usetHot() {
	hot = "0";
}

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

parent.document.title = "' . $title . '";

/**
	* Menu command controler
	*/
function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	if(hot == "1" && arguments[0] != "save_newsletter") {
		if(confirm("' . g_l('modules_newsletter', '[save_changed_newsletter]') . '")) {
			arguments[0] = "save_newsletter";
		} else {
			top.content.usetHot();
		}
	}
	switch (arguments[0]) {
		case "exit_newsletter":
			if(hot != "1") {
				eval(\'top.opener.top.we_cmd("exit_modules")\');
			}
			break;

		case "new_newsletter":
			if(top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = arguments[0];
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout("we_cmd(\"new_newsletter\");", 10);
			}
			break;

		case "new_newsletter_group":
			if(top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = arguments[0];
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout("we_cmd(\"new_newsletter_group\");", 10);
			}
			break;

		case "delete_newsletter":
			if(top.content.editor.edbody.document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				' . ((!permissionhandler::hasPerm("DELETE_NEWSLETTER")) ? (
					we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					) : ('
						if (top.content.editor.edbody.loaded) {
							var delQuestion = top.content.editor.edbody.document.we_form.IsFolder.value == 1 ? "' . g_l('modules_newsletter', '[delete_group_question]') . '" : "' . g_l('modules_newsletter', '[delete_question]') . '";
							if (!confirm(delQuestion)) {
								return;
							}
						} else {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
						' . $this->topFrame . '.editor.edheader.location="' . $this->frameset . '?home=1&pnt=edheader";
						' . $this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?home=1&pnt=edfooter";
						top.content.editor.edbody.document.we_form.ncmd.value=arguments[0];
						top.content.editor.edbody.submitForm();
				')) . '
			}
			break;

		case "save_newsletter":
			if(top.content.editor.edbody.document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				' . ((!permissionhandler::hasPerm("EDIT_NEWSLETTER") && !permissionhandler::hasPerm("NEW_NEWSLETTER")) ? (
					we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					) : ('
						if (top.content.editor.edbody.loaded) {
							if (!top.content.editor.edbody.checkData()) {
								return;
							}
							top.content.editor.edbody.document.we_form.ncmd.value=arguments[0];
							top.content.editor.edbody.submitForm();

						} else {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
				')) . '
				top.content.usetHot();
			}
			break;

		case "newsletter_edit":
			top.content.hot=0;
			top.content.editor.edbody.document.we_form.ncmd.value=arguments[0];
			top.content.editor.edbody.document.we_form.nid.value=arguments[1];
			top.content.editor.edbody.submitForm();
			break;

		case "send_test":
			if(top.content.editor.edbody.document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if(top.content.editor.edbody.document.we_form.IsFolder.value==1) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				top.content.editor.edbody.we_cmd("send_test");
			}
			break;

		case "empty_log":
			if(top.content.editor.edbody.document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if(top.content.editor.edbody.document.we_form.IsFolder.value==1) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				new jsWindow("' . $this->frameset . '?pnt=qlog","log_question",-1,-1,330,230,true,false,true);
			}
			break;

		case "preview_newsletter":
			if(top.content.editor.edbody.document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if(top.content.editor.edbody.document.we_form.IsFolder.value==1) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				top.content.editor.edbody.we_cmd("popPreview");
			}
			break;

		case "send_newsletter":
			if(top.content.editor.edbody.document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if(top.content.editor.edbody.document.we_form.IsFolder.value==1) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				top.content.editor.edbody.we_cmd("popSend");
			}
			break;

		case "test_newsletter":
			if(top.content.editor.edbody.document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if(top.content.editor.edbody.document.we_form.IsFolder.value==1) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				top.content.editor.edbody.we_cmd("popSend","1");
			}
			break;

		case "domain_check":
		case "show_log":
		case "print_lists":
		case "search_email":
		case "clear_log":
			if(top.content.editor.edbody.document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if(top.content.editor.edbody.document.we_form.IsFolder.value==1) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				top.content.editor.edbody.we_cmd(arguments[0]);
			}
			break;

		case "newsletter_settings":
		case "black_list":
		case "edit_file":
			top.content.editor.edbody.we_cmd(arguments[0]);
			break;

		case "home":
			top.content.editor.location="' . $this->frameset . '?pnt=editor";
			break;

		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
			}
			eval("top.opener.top.we_cmd(" + args + ")");
	}
}');
	}

	function getJSFooterCode(){
		return we_html_element::jsElement('
function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	switch (arguments[0]) {
		case "empty_log":
			break;

		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
			}
			eval("parent.edbody.we_cmd(" + args + ")");
	}
}');
	}

	function getJSCmd(){
		return we_html_element::jsElement('
function submitForm() {
	var f = self.document.we_form;

	f.target = "cmd";
	f.method = "post";
	f.submit();
}');
	}

	function getJSProperty(){
		$_mailCheck = (isset($this->settings['reject_save_malformed']) && $this->settings['reject_save_malformed'] ?
				"we.validate.email(email);" :
				"true");

		return
			parent::getJSProperty() .
			we_html_element::jsScript(JS_DIR . "libs/we/weValidate.js") .
			we_html_element::jsElement('
function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

/**
	* Newsletter command controler
	*/
function we_cmd() {

	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	if (arguments[0] != "switchPage") {
		self.setScrollTo();
	}

	switch (arguments[0]) {
		case "browse_users":
			new jsWindow(url,"browse_users",-1,-1,500,300,true,false,true);
			break;

		case "browse_server":
			new jsWindow(url,"browse_server",-1,-1,840,400,true,false,true);
			break;

		case "openImgselector":
		case "openDocselector":
			new jsWindow(url,"we_docselector",-1,-1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ',true,true,true,true);
			break;

		case "openSelector":
			new jsWindow(url,"we_selector",-1,-1,' . we_selector_file::WINDOW_SELECTOR_WIDTH . ',' . we_selector_file::WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
			break;

		case "openNewsletterDirselector":
			url = "' . WE_MODULES_DIR . 'newsletter/we_dirfs.php?";
			for(var i = 0; i < arguments.length; i++){
				url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }
			}
			new jsWindow(url,"we_newsletter_dirselector",-1,-1,600,400,true,true,true);
			break;

		case "add_customer":
			document.we_form.ngroup.value=arguments[2];

		case "del_customer":
			document.we_form.ncmd.value=arguments[0];
			document.we_form.ncustomer.value=arguments[1];
			top.content.hot=1;
			submitForm();
			break;

		case "del_all_customers":
		case "del_all_files":
			top.content.hot=1;
			document.we_form.ncmd.value=arguments[0];
			document.we_form.ngroup.value=arguments[1];
			submitForm();
			break;

		case "add_file":
			document.we_form.ngroup.value=arguments[2];
		case "del_file":
			document.we_form.ncmd.value=arguments[0];
			document.we_form.nfile.value=arguments[1];
			top.content.hot=1;
			submitForm();
			break;

		case "switchPage":
			document.we_form.ncmd.value=arguments[0];
			document.we_form.page.value=arguments[1];
			submitForm();
			break;

		case "set_import":
		case "reset_import":
		case "set_export":
		case "reset_export":
			document.we_form.ncmd.value=arguments[0];
			document.we_form.ngroup.value=arguments[1];
			submitForm();
			break;

		case "addBlock":
		case "delBlock":
			document.we_form.ncmd.value=arguments[0];
			document.we_form.blockid.value=arguments[1];
			top.content.hot=1;
			submitForm();
			break;

		case "addGroup":
		case "delGroup":
			document.we_form.ncmd.value=arguments[0];
			document.we_form.ngroup.value=arguments[1];
			top.content.hot=1;
			submitForm();
			break;

		case "popPreview":
			if(document.we_form.ncmd.value=="home") return;
			if (top.content.hot!=0) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[must_save_preview]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				document.we_form.elements["we_cmd[0]"].value="preview_newsletter";
				document.we_form.gview.value=parent.edfooter.document.we_form.gview.value;
				document.we_form.hm.value=parent.edfooter.document.we_form.hm.value;
				popAndSubmit("newsletter_preview","preview",800,800)
			}
			break;

		case "popSend":
			if(document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if (top.content.hot!=0) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[must_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if(document.we_form.IsFolder.value==1) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {

				if (arguments[1]) {
					message_text="' . g_l('modules_newsletter', '[send_test_question]') . '";
				} else {
					message_text="' . g_l('modules_newsletter', '[send_question]') . '";
				}

				if (confirm(message_text)) {
						document.we_form.ncmd.value=arguments[0];
						if(arguments[1]) document.we_form.test.value=arguments[1];
						submitForm();
				}
			}
			break;

		case "send_test":
			if(document.we_form.ncmd.value=="home") {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if (top.content.hot!=0) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[must_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else if(document.we_form.IsFolder.value==1) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			} else {
				if (confirm("' . sprintf(g_l('modules_newsletter', '[test_email_question]'), $this->newsletter->Test) . '")) {
					document.we_form.ncmd.value=arguments[0];
					document.we_form.gview.value=parent.edfooter.document.we_form.gview.value;
					document.we_form.hm.value=parent.edfooter.document.we_form.hm.value;
					submitForm();
				}
			}
			break;
		case "print_lists":
		case "domain_check":
		case "show_log":
			if(document.we_form.ncmd.value!="home")
				popAndSubmit(arguments[0],arguments[0],650,650);
			break;
		case "newsletter_settings":
			new jsWindow("' . $this->frameset . '?pnt="+arguments[0],arguments[0],-1,-1,600,750,true,true,true,true);
			break;

		case "black_list":
			new jsWindow("' . $this->frameset . '?pnt="+arguments[0],arguments[0],-1,-1,560,460,true,true,true,true);
			break;

		case "edit_file":
			if (arguments[1]){
				new jsWindow("' . $this->frameset . '?pnt="+arguments[0]+"&art="+arguments[1],arguments[0],-1,-1,950,640,true,true,true,true);
			} else {
				new jsWindow("' . $this->frameset . '?pnt="+arguments[0],arguments[0],-1,-1,950,640,true,true,true,true);
			}
			break;

		case "reload_table":
		case "copy_newsletter":
			top.content.hot=1;
			document.we_form.ncmd.value=arguments[0];
			submitForm();
			break;

		case "add_filter":
		case "del_filter":
		case "del_all_filters":
			top.content.hot=1;
			document.we_form.ncmd.value=arguments[0];
			document.we_form.ngroup.value=arguments[1];
			submitForm();
			break;

		case "switch_sendall":
			document.we_form.ncmd.value=arguments[0];
			top.content.hot=1;
			eval("if(document.we_form.sendallcheck_"+arguments[1]+".checked) document.we_form.group"+arguments[1]+"_SendAll.value=1; else document.we_form.group"+arguments[1]+"_SendAll.value=0;");
			submitForm();
			break;

		case "save_settings":
			document.we_form.ncmd.value=arguments[0];
			submitForm("newsletter_settings");
			break;

		case "import_csv":
		case "export_csv":
			document.we_form.ncmd.value=arguments[0];
			submitForm();
			break;

		case "do_upload_csv":
			document.we_form.ncmd.value=arguments[0];
			submitForm("upload_csv");
			break;

		case "do_upload_black":
			document.we_form.ncmd.value=arguments[0];
			submitForm("upload_black");
			break;

		case "upload_csv":
		case "upload_black":
			new jsWindow("' . $this->frameset . '?pnt="+arguments[0]+"&grp="+arguments[1],arguments[0],-1,-1,450,270,true,true,true,true);
			break;

		case "add_email":
			var email=document.we_form.group=arguments[1];
			new jsWindow("' . $this->frameset . '?pnt=eemail&grp="+arguments[1],"edit_email",-1,-1,450,270,true,true,true,true);
			break;

		case "edit_email":
			eval("var p=document.we_form.we_recipient"+arguments[1]+";");

			if (p.selectedIndex < 0) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return;
			}

			eval("var dest=document.we_form.group"+arguments[1]+"_Emails;");

			var str=dest.value;

			var arr = str.split("\n");

			var str2=arr[p.selectedIndex];
			var arr2=str2.split(",");
			var eid=p.selectedIndex;
			var email=p.options[p.selectedIndex].text;
			var htmlmail=arr2[1];
			var salutation=arr2[2];
			var title=arr2[3];
			var firstname=arr2[4];
			var lastname=arr2[5];

			salutation=encodeURIComponent(salutation.replace("+","[:plus:]"));
			title=encodeURIComponent(title.replace("+","[:plus:]"));
			firstname=encodeURIComponent(firstname.replace("+","[:plus:]"));
			lastname=encodeURIComponent(lastname.replace("+","[:plus:]"));
			email = encodeURIComponent(email);
			new jsWindow("' . $this->frameset . '?pnt=eemail&grp="+arguments[1]+"&etyp=1&eid="+eid+"&email="+email+"&htmlmail="+htmlmail+"&salutation="+salutation+"&title="+title+"&firstname="+firstname+"&lastname="+lastname,"edit_email",-1,-1,450,270,true,true,true,true);
			break;

		case "save_black":
		case "import_black":
		case "export_black":
			document.we_form.ncmd.value=arguments[0];
			PopulateVar(document.we_form.blacklist_sel,document.we_form.black_list);
			submitForm("black_list");
			break;
		case "search_email":
			if(document.we_form.ncmd.value=="home") return;
			var searchname=prompt("' . g_l('modules_newsletter', '[search_text]') . '","");

			if (searchname != null) {
				searchEmail(searchname);
			}

			break;
		case "clear_log":
			new jsWindow("' . $this->frameset . '?pnt="+arguments[0],arguments[0],-1,-1,450,300,true,true,true,true);
			break;

		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
			}
			eval("top.content.we_cmd("+args+")");
	}
}

function submitForm() {

	if (self.weWysiwygSetHiddenText) {
		weWysiwygSetHiddenText();
	}

	var f = self.document.we_form;

	f.target = (arguments[0]?arguments[0]:"edbody");
	f.action = (arguments[1]?arguments[1]:"' . $this->frameset . '");
	f.method = (arguments[2]?arguments[2]:"post");

	f.submit();
}

function popAndSubmit(wname, pnt, width, height) {

	old = document.we_form.pnt.value;
	document.we_form.pnt.value=pnt;

	new jsWindow("about:blank",wname,-1,-1,width,height,true,true,true,true);


	' . (((we_base_browserDetect::isMAC()) && (we_base_browserDetect::isIE())) ? '
				setTimeout("submitForm(\'"+wname+"\');", 250);
				setTimeout("document.we_form.pnt.value=old;", 350);
	' : '

		submitForm(wname);
		document.we_form.pnt.value=old;
	') . '

}

function doScrollTo() {
	if (parent.scrollToVal) {
		window.scrollTo(0, parent.scrollToVal);
		parent.scrollToVal = 0;
	}
}

function setScrollTo() {
	parent.scrollToVal = ' . ((we_base_browserDetect::isIE()) ? 'document.body.scrollTop' : 'pageYOffset') . ';
}

function switchRadio(a, b) {
	a.value = 1;
	a.checked = true;
	b.value = 0;
	b.checked = false;

	if (arguments[3]) {
		c = arguments[3];
		c.value = 0;
		c.checked = false;
	}
}

function clickCheck(a) {
	if (a.checked) {
		a.value = 1;
	} else {
		a.value=0;
	}
}

function getStatusContol() {
	return document.we_form.' . (isset($this->uid) ? $this->uid : "") . '_Status.value;
}

function getNumOfDocs() {
	return 0;
}

function sprintf() {
	if (!arguments || arguments.length < 1) {
		return;
	}

	var argum = arguments[0];
	var regex = /([^%]*)%(%|d|s)(.*)/;
	var arr = new Array();
	var iterator = 0;
	var matches = 0;

	while (arr = regex.exec(argum)) {
		var left = arr[1];
		var type = arr[2];
		var right = arr[3];

		matches++;
		iterator++;

		var replace = arguments[iterator];

		if (type=="d") {
			replace = parseInt(param) ? parseInt(param) : 0;
		} else if (type=="s") {
			replace = arguments[iterator];
		}

		argum = left + replace + right;
	}

	return argum;
}

function checkData() {
	if (document.we_form.Text.value == "") {
		' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[empty_name]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		return false;
	}

	return true;
}

function markEMails() {

}
function add(group, newRecipient, htmlmail, salutation, title, firstname, lastname) {
	var p = document.forms[0].elements["we_recipient"+group];

	if (newRecipient != null) {

		if (newRecipient.length > 0) {

			if (newRecipient.length > 255 ) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_max_len]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return;
			}

			if (!inSelectBox(document.forms[0].elements["we_recipient"+group], newRecipient)) {
				if(isValidEmail(newRecipient)) optionClassName = "markValid";
				else optionClassName = "markNotValid";
				addElement(document.forms[0].elements["we_recipient"+group],"#",newRecipient,true,optionClassName);
				addEmail(group,newRecipient,htmlmail,salutation,title,firstname,lastname);
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_exists]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
		} else {
			' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		}
		//set_state_edit_delete_recipient("we_recipient"+group);
	}
}

function isValidEmail(email){
	email = email.toLowerCase();
	return ' . ($_mailCheck) . ';
	//return (email.match(/^([[:space:]_:\+\.0-9a-z-]+[\<]{1})?[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,6}(\>)?$/) ? true : false);
}

function deleteit(group) {
	var p=document.forms[0].elements["we_recipient"+group];

	if (p.selectedIndex >= 0) {
		if (confirm("' . g_l('modules_newsletter', '[email_delete]') . '")) {
			delEmail(group,p.selectedIndex);
			p.options[p.selectedIndex] = null;
		}
	} else {
		' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
	//set_state_edit_delete_recipient("we_recipient"+group);
}

function deleteall(group) {
	var p = document.forms[0].elements["we_recipient"+group];

	if (confirm("' . g_l('modules_newsletter', '[email_delete_all]') . '")) {
		delallEmails(group);
		we_cmd("switchPage",1);
	}
	//set_state_edit_delete_recipient("we_recipient"+group);
}

function in_array(n, h) {
	for (var i = 0; i < h.length; i++) {

		if (h[i] == n) {
			return true;
		}
	}

	return false;
}

function editIt(group, index, editRecipient, htmlmail, salutation, title, firstname, lastname) {
	var p = document.forms[0].elements["we_recipient"+group];

	if (index >= 0 && editRecipient != null) {

		if (editRecipient != "") {

			if (editRecipient.length > 255 ) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_max_len]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return;
			}
			if(isValidEmail(editRecipient)) optionClassName = "markValid";
			else optionClassName = "markNotValid";
			p.options[index].text = editRecipient;
			p.options[index].className = optionClassName;
			editEmail(group,index,editRecipient,htmlmail,salutation,title,firstname,lastname);
		} else {
			' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		}
	}
}

function PopulateVar(p, dest) {
	var arr = new Array();

	for (i = 0; i < p.length; i++) {
		arr[i] = p.options[i].text;
	}

	dest.value=arr.join();
}

function PopulateMultipleVar(p, dest){
	var arr = new Array();
	c = 0;

	for (i = 0; i < p.length; i++) {
		if (p.options[i].selected) {
			c++;
			arr[c] = p.options[i].value;
		}
	}

	dest.value=arr.join();
}

function addEmail(group, email, html, salutation, title, firstname, lastname) {
	var dest = document.forms[0].elements["group"+group+"_Emails"]
	var str = dest.value;

	var arr = ( str.length > 0?str.split("\n"):new Array());

	arr[arr.length] = email+","+html+","+salutation+","+title+","+firstname+","+lastname;

	dest.value = arr.join("\n");

	top.content.hot=1;
}

function editEmail(group, id, email, html, salutation, title, firstname, lastname) {
	var dest = document.forms[0].elements["group"+group+"_Emails"]
	var str = dest.value;

	var arr = str.split("\n");

	arr[id] = email+","+html+","+salutation+","+title+","+firstname+","+lastname;

	dest.value = arr.join("\n");

	top.content.hot = 1;
}

function mysplice(arr, id) {
	var newarr = new Array();

	for (i = 0; i < arr.lenght; i++) {
		if (i!=id) {
			newarr[newarr.lenght] = arr[id];
		}
	}
	return newarr;
}

function delEmail(group,id) {
	var dest = document.forms[0].elements["group"+group+"_Emails"]
	var str = dest.value;
	var arr = str.split("\n");

	arr.splice(id, 1);
	dest.value = arr.join("\n");
	top.content.hot=1;
}

function delallEmails(group) {
	var dest = document.forms[0].elements["group"+group+"_Emails"]

	dest.value = "";
	top.content.hot = 1;
}

function inSelectBox(p, val) {
	for (var i = 0; i < p.options.length; i++) {

		if (p.options[i].text == val) {
			return true;
		}
	}
	return false;
}

function addElement(p,value, text, sel, optionClassName) {
	var i = p.length;

	p.options[i] =  new Option(text,value);
	p.options[i].className = optionClassName;

	if (sel) {
		p.selectedIndex = i;
	}
}

function getGroupsNum() {
	return document.we_form.groups.value;
}

function searchEmail(searchname) {
	var f = document.we_form;
	var c;
	var hit = 0;

	if(document.we_form.page.value==1){
		for (i = 0; i < f.groups.value; i++) {
			c = f.elements["we_recipient" + i];
			c.selectedIndex = -1;

			for (j = 0; j < c.length; j++) {
				if (c.options[j].text == searchname) {
					c.selectedIndex = j;
					hit++;
				}
			}
		}
		msg = sprintf("' . g_l('modules_newsletter', '[search_finished]') . '",hit);
		' . we_message_reporting::getShowMessageCall("msg", we_message_reporting::WE_MESSAGE_NOTICE, true) . '
	}

}

function set_state_edit_delete_recipient(control) {
		var p = document.forms[0].elements[control];
		var i = p.length;

		if (i == 0) {
			switch_button_state("edit", "edit_enabled", "disabled");
			switch_button_state("delete", "delete_enabled", "disabled");
			switch_button_state("delete_all", "delete_all_enabled", "disabled");
			//edit_enabled = switch_button_state("edit", "edit_enabled", "disabled");
			//delete_enabled = switch_button_state("delete", "delete_enabled", "disabled");
			//delete_all_enabled = switch_button_state("delete_all", "delete_all_enabled", "disabled");

		} else {
			switch_button_state("edit", "edit_enabled", "enabled");
			switch_button_state("delete", "delete_enabled", "enabled");
			switch_button_state("delete_all", "delete_all_enabled", "enabled");
			//edit_enabled = switch_button_state("edit", "edit_enabled", "enabled");
			//delete_enabled = switch_button_state("delete", "delete_enabled", "enabled");
			//delete_all_enabled = switch_button_state("delete_all", "delete_all_enabled", "enabled");
		}
}');
		//$js.=we_button::create_state_changer();
	}

	function processCommands(){
		$ncmd = we_base_request::_(we_base_request::STRING, "ncmd");
		switch($ncmd){
			case "new_newsletter":
				$this->newsletter = new we_newsletter_newsletter();
				$this->newsletter->Text = g_l('modules_newsletter', '[new_newsletter]');
				$this->newsletter->Sender = $this->settings["default_sender"];
				$this->newsletter->Reply = $this->settings["default_reply"];
				$this->newsletter->Test = $this->settings["test_account"];
				$this->newsletter->isEmbedImages = $this->settings['isEmbedImages'];

				echo we_html_element::jsElement('
							top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader' . (($page = we_base_request::_(we_base_request::INT, "page")) !== false ? "&page=" . $page : "") . '";
							top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
					');
				break;
			case "new_newsletter_group":
				$this->page = 0;
				$this->newsletter = new we_newsletter_newsletter();
				$this->newsletter->IsFolder = "1";
				$this->newsletter->Text = g_l('modules_newsletter', '[new_newsletter_group]');
				echo we_html_element::jsElement('
							top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&group=1";
							top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter&group=1";
					');
				break;
			case "add_customer":
				$ngroup = we_base_request::_(we_base_request::STRING, 'ngroup');
				if($ngroup !== false){
					$arr = makeArrayFromCSV($this->newsletter->groups[$ngroup]->Customers);
					if(($ncust = we_base_request::_(we_base_request::STRING, "ncustomer") ) !== false){

						$ids = makeArrayFromCSV($ncust);
						foreach($ids as $id){
							if($id && (!in_array($id, $arr))){
								$arr[] = $id;
							}
						}

						$this->newsletter->groups[$ngroup]->Customers = makeCSVFromArray($arr, true);
					}
				}
				break;

			case "del_customer":
				$arr = array();
				$ngroup = we_base_request::_(we_base_request::STRING, 'ngroup');
				if($ngroup !== false){
					$arr = makeArrayFromCSV($this->newsletter->groups[$ngroup]->Customers);

					if(($ncust = we_base_request::_(we_base_request::STRING, "ncustomer") ) !== false){
						foreach($arr as $k => $v){
							if($v == $ncust){
								unset($arr[$k]);
							}
						}
						$this->newsletter->groups[$ngroup]->Customers = makeCSVFromArray($arr, true);
					}
				}
				break;

			case "add_file":
				$arr = array();
				if(($ngroup = we_base_request::_(we_base_request::STRING, 'ngroup')) !== false){
					$arr = explode(',', $this->newsletter->groups[$ngroup]->Extern);
					if(($nfile = we_base_request::_(we_base_request::FILE, "nfile")) !== false){
						$_sd = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
						$arr[] = str_replace($_sd, (substr($_sd, -1) === '/' ? '/' : ''), $nfile);
						$this->newsletter->groups[$ngroup]->Extern = implode(',', $arr);
					}
				}
				break;

			case "del_file":
				$arr = array();
				if(($ngroup = we_base_request::_(we_base_request::STRING, 'ngroup')) !== false){
					$arr = explode(',', $this->newsletter->groups[$ngroup]->Extern);
					if(($nfile = we_base_request::_(we_base_request::FILE, "nfile")) !== false){
						$pos = array_search($nfile, $arr);
						if($pos !== false){
							unset($arr[$pos]);
						}
						$this->newsletter->groups[$ngroup]->Extern = implode(',', $arr);
					}
				}
				break;
			case "del_all_files":
				if(($ngroup = we_base_request::_(we_base_request::STRING, 'ngroup')) !== false){
					$this->newsletter->groups[$ngroup]->Extern = '';
				}
				break;
			case "del_all_customers":
				if(($ngroup = we_base_request::_(we_base_request::STRING, 'ngroup')) !== false){
					$this->newsletter->groups[$ngroup]->Customers = '';
				}
				break;

			case "reload":
				echo we_html_element::jsElement('
							top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&page=' . $this->page . '&txt=' . urlencode($this->newsletter->Text) . ($this->newsletter->IsFolder ? '&group=1' : '') . '";
							top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter' . ($this->newsletter->IsFolder ? '&group=1' : '') . '";
					');
				break;

			case "newsletter_edit":
				if(($nid = we_base_request::_(we_base_request::INT, 'nid'))){
					$this->newsletter = new we_newsletter_newsletter($nid);
				}
				if($this->newsletter->IsFolder){
					$this->page = 0;
				}
				$_REQUEST["ncmd"] = "reload";
				$this->processCommands();
				break;

			case 'switchPage':
				$this->page = we_base_request::_(we_base_request::INT, "page", $this->page);
				break;

			case 'save_newsletter':
				$nid = we_base_request::_(we_base_request::INT, 'nid');
				if($nid === false){
					break;
				}
				$weAcQuery = new we_selector_query();
				$newone = false;

				if($this->newsletter->filenameNotValid()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR));
					return;
				}

				if($this->newsletter->ParentID > 0){
					$weAcResult = $weAcQuery->getItemById($this->newsletter->ParentID, NEWSLETTER_TABLE, array("IsFolder"), false);
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
						return;
					}
				}

				if(isset($_REQUEST['blocks'])){
					for($i = 0; $i < $_REQUEST['blocks']; $i++){
						switch(we_base_request::_(we_base_request::INT, 'block' . $i . '_Type')){
							case we_newsletter_block::DOCUMENT:
							case we_newsletter_block::DOCUMENT_FIELD:
								$acTable = FILE_TABLE;
								$acErrorField = g_l('modules_newsletter', '[block_document]');
								break;
							case we_newsletter_block::OBJECT:
							case we_newsletter_block::OBJECT_FIELD:
								$acTable = OBJECT_FILES_TABLE;
								$acErrorField = g_l('modules_newsletter', '[block_object]');
								break;
							default:
								$acTable = '';
								$acErrorField = '';
						}
						if($acTable){
							$weAcResult = $weAcQuery->getItemById(we_base_request::_(we_base_request::INT, 'block' . $i . '_LinkID'), $acTable, array('IsFolder'));

							if(!is_array($weAcResult) || count($weAcResult) < 1 || $weAcResult[0]['IsFolder'] == 1){
								echo we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[blockFieldError]'), ($i + 1), $acErrorField), we_message_reporting::WE_MESSAGE_ERROR)
								);
								return;
							}
							if(($field = we_base_request::_(we_base_request::INT, 'block' . $i . '_Field'))){
								$weAcResult = $weAcQuery->getItemById($field, TEMPLATES_TABLE, array("IsFolder"));
								if(!is_array($weAcResult) || !$weAcResult || $weAcResult[0]['IsFolder'] == 1){
									echo we_html_element::jsElement(
										we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[blockFieldError]'), $i, g_l('modules_newsletter', '[block_template]')), we_message_reporting::WE_MESSAGE_ERROR)
									);
									return;
								}
							}
						}
					}
				}

				if(!$this->newsletter->ID){
					$newone = true;
				}

				if(!$newone && we_base_request::_(we_base_request::BOOL, 'ask')){
					$h = getHash('SELECT Step,Offset FROM ' . NEWSLETTER_TABLE . ' WHERE ID=' . intval($this->newsletter->ID), $this->db);

					if($h['Step'] != 0 || $h['Offset'] != 0){
						echo we_html_element::jsScript(JS_DIR . 'windows.js') .
						we_html_element::jsElement('
										self.focus();
										top.content.get_focus=0;
										new jsWindow("' . $this->frameset . '?pnt=qsave1","save_question",-1,-1,350,200,true,true,true,false);
									');
						break;
					}
				}

				if(!$this->newsletter->Sender){
					$this->newsletter->Sender = $this->settings["default_sender"];
				}

				if(!$this->newsletter->Reply){
					$this->newsletter->Reply = $this->settings["default_reply"];
				}

				if(!$this->newsletter->Test){
					$this->newsletter->Test = $this->settings["test_account"];
				}
				if(!$this->newsletter->isEmbedImages){
					$this->newsletter->isEmbedImages = $this->settings["isEmbedImages"];
				}

				$double = intval(f('SELECT COUNT(1) AS Count FROM ' . NEWSLETTER_TABLE . " WHERE Path='" . $this->db->escape($this->newsletter->Path) . "'" . ($newone ? '' : ' AND ID<>' . $this->newsletter->ID), 'Count', $this->db));

				if(!permissionhandler::hasPerm("EDIT_NEWSLETTER") && !permissionhandler::hasPerm("NEW_NEWSLETTER")){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return;
				}
				if($newone && !permissionhandler::hasPerm("NEW_NEWSLETTER")){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return;
				}
				if(!$newone && !permissionhandler::hasPerm("EDIT_NEWSLETTER")){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return;
				}

				if($double){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[double_name]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return;
				}

				$message = "";

				$ret = $this->newsletter->save($message, (isset($this->settings["reject_save_malformed"]) ? $this->settings["reject_save_malformed"] : true));
				switch($ret){
					default:
						$jsmess = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[malformed_mail_group]'), $ret, $message), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case we_newsletter_newsletter::MALFORMED_SENDER:
						$jsmess = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[malformed_mail_sender]'), $message), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case we_newsletter_newsletter::MALFORMED_REPLY:
						$jsmess = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[malformed_mail_reply]'), $message), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case we_newsletter_newsletter::MALFORMED_TEST:
						$jsmess = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[malformed_mail_test]'), $message), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case we_newsletter_newsletter::SAVE_PATH_NOK:
						$jsmess = we_message_reporting::getShowMessageCall($message, we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case 0:
						$jsmess = ($newone ?
								$this->topFrame . '.makeNewEntry(\'' . $this->newsletter->Icon . '\',\'' . $this->newsletter->ID . '\',\'' . $this->newsletter->ParentID . '\',\'' . $this->newsletter->Text . '\',0,\'' . ($this->newsletter->IsFolder ? we_base_ContentTypes::FOLDER : 'item') . '\',\'' . NEWSLETTER_TABLE . '\');' :
								$this->topFrame . '.updateEntry("' . $this->newsletter->ID . '","' . $this->newsletter->Text . '","' . $this->newsletter->ParentID . '");') .
							$this->topFrame . '.drawTree();' .
							we_message_reporting::getShowMessageCall(g_l('modules_newsletter', ($this->newsletter->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) .
							'top.content.hot=0;';
						break;
				}
				echo we_html_element::jsElement($jsmess);

				break;

			case "delete_newsletter":
				$nid = we_base_request::_(we_base_request::INT, "nid");
				if($nid !== false){
					if(!$nid){
						echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[delete_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
						return;
					}
					if(!permissionhandler::hasPerm("DELETE_NEWSLETTER")){
						echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
						return;
					} else {
						$this->newsletter = new we_newsletter_newsletter($nid);

						if($this->newsletter->delete()){
							$this->newsletter = new we_newsletter_newsletter();
							echo we_html_element::jsElement('
										top.content.deleteEntry(' . $nid . ',"file");
										setTimeout(\'' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', (we_base_request::_(we_base_request::BOOL, "IsFolder") ? '[delete_group_ok]' : '[delete_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) . '\',500);
								');
							$_REQUEST['home'] = 1;
							$_REQUEST['pnt'] = 'edbody';
						} else {
							echo we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_newsletter', (we_base_request::_(we_base_request::BOOL, "IsFolder") ? '[delete_group_nok]' : '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR)
							);
						}
					}
				} else {
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_newsletter', (we_base_request::_(we_base_request::BOOL, "IsFolder") ? '[delete_group_nok]' : '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR)
					);
				}
				break;

			case "reload_table":
				$this->page = 1;
				break;

			case "set_import":
				$this->show_import_box = we_base_request::_(we_base_request::STRING, "ngroup");
				break;

			case "set_export":
				$this->show_export_box = we_base_request::_(we_base_request::STRING, "ngroup");
				break;

			case "reset_import":
				$this->show_import_box = -1;
				break;

			case "reset_export":
				$this->show_export_box = -1;
				break;

			case "addBlock":
				if(($bid = we_base_request::_(we_base_request::INT, "blockid")) !== false){
					$this->newsletter->addBlock($bid + 1);
				}
				break;

			case "delBlock":
				if(($bid = we_base_request::_(we_base_request::INT, "blockid")) !== false){
					$this->newsletter->removeBlock($bid);
				}
				break;

			case "addGroup":
				$this->newsletter->addGroup();
				echo we_html_element::jsElement('
var edf=top.content.editor.edfooter;
edf.document.we_form.gview.length = 0;
edf.populateGroups();');
				break;

			case "delGroup":
				if(($ngroup = we_base_request::_(we_base_request::STRING, "ngroup")) !== false){
					$this->newsletter->removeGroup($ngroup);
					echo we_html_element::jsElement('
var edf=top.content.editor.edfooter;
edf.document.we_form.gview.length = 0;
edf.populateGroups();');
				}
				break;

			case "send_test":
				if(!permissionhandler::hasPerm("SEND_TEST_EMAIL")){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return;
				}
				$this->sendTestMail(we_base_request::_(we_base_request::INT, "gview", 0), we_base_request::_(we_base_request::BOOL, "hm"));
				echo we_html_element::jsElement(
					we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[test_mail_sent]'), $this->newsletter->Test), we_message_reporting::WE_MESSAGE_NOTICE)
				);
				break;

			case "add_filter":
				$this->newsletter->groups[we_base_request::_(we_base_request::STRING, "ngroup")]->addFilter($this->customers_fields[0]);
				break;

			case "del_filter":
				$this->newsletter->groups[we_base_request::_(we_base_request::STRING, "ngroup")]->delFilter();
				break;

			case "del_all_filters":
				$this->newsletter->groups[we_base_request::_(we_base_request::STRING, "ngroup")]->delallFilter();
				break;

			case "copy_newsletter":
				if(($cid = we_base_request::_(we_base_request::INT, "copyid"))){
					$id = $this->newsletter->ID;
					$this->newsletter = new we_newsletter_newsletter($cid);
					$this->newsletter->ID = $id;
					$this->newsletter->Text.="_" . g_l('modules_newsletter', '[copy]');
				}
				break;

			case 'save_settings':
				foreach($this->settings as $k => $v){
					$this->settings[$k] = we_base_request::_(we_base_request::RAW, $k, 0);
				}
				$this->saveSettings();
				break;

			case 'import_csv':
				if(($importno = we_base_request::_(we_base_request::INT, 'csv_import')) !== false){
					$filepath = we_base_request::_(we_base_request::FILE, 'csv_file' . $importno);
					$delimiter = we_base_request::_(we_base_request::RAW_CHECKED, 'csv_delimiter' . $importno);
					$col = max(0, we_base_request::_(we_base_request::INT, 'csv_col' . $importno, 1) - 1);

					$imports = array(
						'hmcol' => array(),
						'salutationcol' => array(),
						'titlecol' => array(),
						'firstnamecol' => array(),
						'lastnamecol' => array(),
					);
					foreach($imports as $key => &$vals){
						$vals['val'] = we_base_request::_(we_base_request::INT, 'csv_' . $key . $importno, 0);
						$vals['import'] = ($vals['val'] > 0);
						if($vals['val']){
							$vals['val'] --;
						}
					}

					if(strpos($filepath, '..') !== false){
						echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
					} else {
						$row = array();
						$control = array();
						$fh = @fopen($_SERVER['DOCUMENT_ROOT'] . $filepath, 'rb');
						$mailListArray = array();
						if($fh){
							$_mailListArray = explode("\n", $this->newsletter->groups[$importno]->Emails);
							foreach($_mailListArray as $line){
								$mailListArray[] = substr($line, 0, strpos($line, ','));
							}
							unset($_mailListArray);
							while($dat = fgetcsv($fh, 1000, $delimiter)){
								if(!isset($control[$dat[$col]])){
									$_alldat = implode('', $dat);
									if(str_replace(' ', '', $_alldat) === ""){
										continue;
									}
									$mailrecip = (str_replace(' ', '', $dat[$col]) === '') ? '--- ' . g_l('modules_newsletter', '[email_missing]') . ' ---' : $dat[$col];
									if(!empty($mailrecip) && !in_array($mailrecip, $mailListArray)){
										$row[] = $mailrecip . ',' .
											( ($imports['hmcol']['import'] && isset($dat[$imports['hmcol']['val']])) ? $dat[$imports['hmcol']['val']] : '') . "," .
											( ($imports['salutationcol']['import'] && isset($dat[$imports['salutationcol']['val']])) ? $dat[$imports['salutationcol']['val']] : "") . "," .
											( ($imports['titlecol']['import'] && isset($dat[$imports['titlecol']['val']])) ? $dat[$imports['titlecol']['val']] : "") . "," .
											( ($imports['firstnamecol']['import'] && isset($dat[$imports['firstnamecol']['val']])) ? $dat[$imports['firstnamecol']['val']] : "") . "," .
											( ($imports['lastnamecol']['import'] && isset($dat[$imports['lastnamecol']['val']])) ? $dat[$imports['lastnamecol']['val']] : "");
										$control[$dat[$col]] = 1;
									}
								}
							}
							fclose($fh);
							$this->newsletter->groups[$importno]->Emails.=($this->newsletter->groups[$importno]->Emails ? "\n" : '') . implode("\n", $row);
						} else {
							echo we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						}
					}
				}
				break;

			case "export_csv":
				if(($exportno = we_base_request::_(we_base_request::INT, "csv_export")) !== false){
					$fname = rtim(we_base_request::_(we_base_request::FILE, "csv_dir" . $exportno), '/') . "/emails_export_" . time() . ".csv";

					we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $fname, $this->newsletter->groups[$exportno]->Emails);
					echo we_html_element::jsScript(JS_DIR . "windows.js") .
					we_html_element::jsElement('
							new jsWindow("' . $this->frameset . '?pnt=export_csv_mes&lnk=' . $fname . '","edit_email",-1,-1,440,250,true,true,true,true);
						');
				}
				break;

			case "save_black":
				$this->saveSetting("black_list", $this->settings["black_list"]);
				echo we_html_element::jsElement('self.close();');
				break;

			case "do_upload_csv":
			case "do_upload_black":
				$group = we_base_request::_(we_base_request::INT, "group", 0);
				$weFileupload = new we_fileupload_include('we_File');
				if(!$weFileupload->processFileRequest()){
					//ajax resonse allready written: return here to send response only
					$this->jsonOnly = true;

					return;
				}

				//set header we avoided when sending JSON only
				we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);
				echo we_html_tools::getHtmlTop('newsletter') . STYLESHEET;

				//we have finished upload or we are in fallback mode
				$tempName = str_replace($_SERVER['DOCUMENT_ROOT'], "", $weFileupload->getFileNameTemp());
				if(!$tempName && isset($_FILES["we_File"]) && $_FILES["we_File"]["size"]){
					//fallback or legacy mode
					$we_File = $_FILES["we_File"];
					$tempName = TEMP_PATH . we_base_file::getUniqueId();

					if(!move_uploaded_file($we_File["tmp_name"], $tempName)){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[upload_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
						return;
					}
					$tempName = str_replace($_SERVER['DOCUMENT_ROOT'], "", $tempName);
				}

				//print next command
				echo we_html_element::jsElement($ncmd === 'do_upload_csv' ? '
opener.document.we_form.csv_file' . $group . '.value="' . $tempName . '";
opener.we_cmd("import_csv");
self.close();' : '
opener.document.we_form.csv_file.value="' . $tempName . '";
opener.document.we_form.sib.value=0;
opener.we_cmd("import_black");
self.close();');
				break;

			case "save_email_file":
				$csv_file = we_base_request::_(we_base_request::FILE, "csv_file", '');
				$nrid = we_base_request::_(we_base_request::INT, "nrid", '');
				$email = we_base_request::_(we_base_request::EMAIL, "email", '');
				$htmlmail = we_base_request::_(we_base_request::BOOL, "htmlmail", '');
				$salutation = we_base_request::_(we_base_request::STRING, "salutation", '');
				$title = we_base_request::_(we_base_request::STRING, "title", '');
				$firstname = we_base_request::_(we_base_request::STRING, "firstname", '');
				$lastname = we_base_request::_(we_base_request::STRING, "lastname", '');

				$emails = ($csv_file ? we_newsletter_newsletter::getEmailsFromExtern($csv_file) : array());

				$emails[$nrid] = array($email, $htmlmail, $salutation, $title, $firstname, $lastname);
				$emails_out = "";
				foreach($emails as $email){
					$emails_out.=makeCSVFromArray(array_slice($email, 0, 6)) . "\n";
				}

				if($csv_file){
					we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $csv_file, $emails_out);
				}

				break;

			case "delete_email_file":
				$nrid = we_base_request::_(we_base_request::INT, "nrid", '');
				$csv_file = we_base_request::_(we_base_request::FILE, "csv_file", '');
				$emails = ($csv_file ? we_newsletter_newsletter::getEmailsFromExtern($csv_file, 2) : array());

				if($nrid){
					unset($emails[$nrid]);
					$emails_out = '';
					foreach($emails as $email){
						$emails_out.=makeCSVFromArray($email) . "\n";
					}

					if($csv_file){
						we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $csv_file, $emails_out);
					}
				}
				break;
			case "popSend":

				echo we_html_element::jsScript(JS_DIR . "windows.js") .
				we_html_element::jsElement(
					((!trim($this->newsletter->Subject)) ? 'if(confirm("' . g_l('modules_newsletter', '[no_subject]') . '")){' : '') . '
							url ="' . $this->frameset . '?pnt=send&nid=' . $this->newsletter->ID . (we_base_request::_(we_base_request::BOOL, "test") ? '&test=1' : '') . '";
							new jsWindow(url,"newsletter_send",-1,-1,600,400,true,true,true,false);
						' . (!(trim($this->newsletter->Subject)) ? '}' : '')
				);
				break;
			default:
		}
	}

	function processVariables(){
		if(($uid = we_base_request::_(we_base_request::STRING, 'wname'))){
			$this->uid = $uid;
		}

		foreach($this->newsletter->persistents as $val => $type){
			$this->newsletter->$val = we_base_request::_($type, $val, $this->newsletter->$val);
		}

		if($this->newsletter->ParentID){
			$this->newsletter->Path = f('SELECT Path FROM ' . NEWSLETTER_TABLE . ' WHERE ID=' . $this->newsletter->ParentID, '', $this->db) . '/' . $this->newsletter->Text;
		} elseif(!$this->newsletter->filenameNotValid($this->newsletter->Text)){
			$this->newsletter->Path = '/' . $this->newsletter->Text;
		}

		$this->page = we_base_request::_(we_base_request::INT, 'page', $this->page);
		if(($groups = we_base_request::_(we_base_request::INT, 'groups')) !== false){

			$this->newsletter->groups = array();

			if($groups == 0){
				$this->newsletter->addGroup();
			}

			for($i = 0; $i < $groups; $i++){
				$this->newsletter->addGroup();
			}

			$fields_names = array('fieldname', 'operator', 'fieldvalue', 'logic', 'hours', 'minutes');

			foreach($this->newsletter->groups as $gkey => &$gval){
				// persistens
				$gval->NewsletterID = $this->newsletter->ID;

				foreach($gval->persistents as $per => $type){
					$varname = 'group' . $gkey . '_' . $per;
					$gval->$per = we_base_request::_($type, $varname, $gval->$per);
				}

				// Filter
				$count = (isset($_REQUEST['filter_' . $gkey]) ? $_REQUEST['filter_' . $gkey] ++ : 0);
				if($count){
					for($i = 0; $i < $count; $i++){
						$new = array();

						foreach($fields_names as $field){
							$varname = 'filter_' . $field . '_' . $gkey . '_' . $i;

							if(($tmp = we_base_request::_(we_base_request::RAW_CHECKED, $varname)) !== false){
								$new[$field] = $tmp;
							}
						}

						if($new){
							$gval->appendFilter($new);
							$gval->preserveFilter();
						}
					}
				}
			}
			unset($gval);
		}
		if(($blocks = we_base_request::_(we_base_request::INT, 'blocks')) !== false){

			$this->newsletter->blocks = array();

			if($blocks == 0){
				$this->newsletter->addBlock();
			}

			for($i = 0; $i < $blocks; $i++){
				$this->newsletter->addBlock();
			}

			foreach($this->newsletter->blocks as $skey => &$sval){
				$sval->NewsletterID = $this->newsletter->ID;

				foreach($sval->persistents as $per => $type){
					$varname = 'block' . $skey . '_' . $per;
					$sval->$per = we_base_request::_($type, $varname, $sval->$per);
				}
			}
			unset($gval);
		}
	}

	function getTime($seconds){
		$min = floor($seconds / 60);
		$ret = array(
			"hour" => floor($min / 60),
			"min" => $min,
			"sec" => $seconds - ($min * 60)
		);
		$ret["min"] -= ($ret["hour"] * 60);
		return $ret;
	}

	public function isJsonOnly(){
		return $this->jsonOnly;
	}

	/**
	 * Newsletter printing functions
	 */
	private function initDocByObject($we_objectID){

		$we_obj = new we_objectFile();
		$we_obj->initByID($we_objectID, OBJECT_FILES_TABLE);

		$we_doc = $this->initDoc();
		$we_doc->elements = $we_obj->elements;
		$we_doc->Templates = $we_obj->Templates;
		$we_doc->ExtraTemplates = $we_obj->ExtraTemplates;
		$we_doc->TableID = $we_obj->TableID;
		$we_doc->CreatorID = $we_obj->CreatorID;
		$we_doc->ModifierID = $we_obj->ModifierID;
		$we_doc->RestrictOwners = $we_obj->RestrictOwners;
		$we_doc->Owners = $we_obj->Owners;
		$we_doc->OwnersReadOnly = $we_obj->OwnersReadOnly;
		$we_doc->Category = $we_obj->Category;
		$we_doc->OF_ID = $we_obj->ID;

		return $we_doc;
	}

	private function initDoc($id = 0){
		$we_doc = new we_webEditionDocument();

		if($id){
			$we_doc->initByID($id);
		}
		return $we_doc;
	}

	function we_includeEntity(&$we_doc, $tmpid){//FIXME: unused
		if($tmpid != "" && $tmpid != 0){
			$path = id_to_path($tmpid, TEMPLATES_TABLE);
		}

		$path = ($path ? TEMPLATES_PATH . $path : $we_doc->TemplatePath);

		if(file_exists($path)){
			include($path);
		} else {
			echo STYLESHEET .
			'<div class="defaultgray"><center>' . g_l('modules_newsletter', '[cannot_preview]') . '</center></div>';
		}
	}

	function getContent($pblk = 0, $gview = 0, $hm = 0, $salutation = '', $title = '', $firstname = '', $lastname = '', $customerid = 0){
		if(!isset($this->newsletter->blocks[$pblk])){
			return '';
		}
		$block = $this->newsletter->blocks[$pblk];
		$groups = makeArrayFromCSV($block->Groups);
		if(!(in_array($gview, $groups) || $gview == 0)){
			return '';
		}

		$content = $GLOBALS['we_doc'] = '';

		$GLOBALS['WE_MAIL'] = we_newsletter_base::EMAIL_REPLACE_TEXT;
		$GLOBALS['WE_HTMLMAIL'] = $hm;
		$GLOBALS['WE_TITLE'] = $title;
		$GLOBALS['WE_SALUTATION'] = $salutation;
		$GLOBALS['WE_FIRSTNAME'] = $firstname;
		$GLOBALS['WE_LASTNAME'] = $lastname;
		$GLOBALS['WE_CUSTOMERID'] = $customerid;
		$patterns = array();

		switch($block->Type){
			case we_newsletter_block::DOCUMENT:
				if($block->Field != "" && $block->Field != 0){
					$path = TEMPLATES_PATH . preg_replace('/\.tmpl$/i', '.php', id_to_path($block->Field, TEMPLATES_TABLE));
				} else if($block->LinkID){
					$p = f('SELECT t.Path FROM ' . FILE_TABLE . ' f LEFT JOIN ' . TEMPLATES_TABLE . ' t ON f.TemplateID=t.ID WHERE f.ID=' . intval($block->LinkID), "", $this->db);
					$path = TEMPLATES_PATH . preg_replace('/\.tmpl$/i', '.php', $p);
				} else {
					$path = "";
				}
				if($block->LinkID && $path){
					$content = ($block->LinkID > 0) && we_base_file::isWeFile($block->LinkID, FILE_TABLE, $this->db) ? we_getDocumentByID($block->LinkID, $path, $this->db) : 'No such File';
				}
				break;
			case we_newsletter_block::DOCUMENT_FIELD:
				if($block->LinkID){
					$we_doc = $this->initDoc($block->LinkID);
					$content = $we_doc->getElement($block->Field);
				}
				break;
			case we_newsletter_block::OBJECT:
				$path = ($block->Field != "" && $block->Field ?
						TEMPLATES_PATH . preg_replace('/\.tmpl$/i', '.php', id_to_path($block->Field, TEMPLATES_TABLE)) : '');

				if($block->LinkID && $path){
					$content = we_getObjectFileByID($block->LinkID, $path);
				}

				break;
			case we_newsletter_block::OBJECT_FIELD:
				if($block->LinkID){
					$we_doc = $this->initDocByObject($block->LinkID);
					$content = $we_doc->getElement($block->Field);
				}
				break;
			case we_newsletter_block::TEXT:
				$blockHtml = $block->Html ? preg_replace(array(
						'/(href=")(\\\\*&quot;)*(.+?)(\\\\*&quot;)*(")/',
						'/(src=")(\\\\*&quot;)*(.+?)(\\\\*&quot;)*(")/'), '$1$3$5', stripslashes($block->Html)) : '';

				if($hm){
					$content = $blockHtml ?
						$blockHtml :
						strtr($block->Source, array(
							"\r\n" => "\n",
							"\r" => "\n",
							'&' => '&amp;',
							'<' => '&lt;',
							'>' => '&gt;',
							"\n" => '<br/>',
							"\t" => '&nbsp;&nbsp;&nbsp;',
					));
					break;
				}
				$content = ($block->Source ?
						$block->Source :
						str_ireplace(array('&nbsp;', '&lt;', "&gt;", "&quot;", "&amp;",), array(' ', "<", ">", '"', "&",), preg_replace("|&nbsp;(&nbsp;)+|i", "\t", trim(strip_tags(preg_replace("|<br\s*/?\s*>|i", "\n", $blockHtml))))));
				//TODO: we should preserve img- and link-pathes: "text text linktext (path) text"

				break;
			case we_newsletter_block::FILE:
				$content = we_base_file::load($_SERVER['DOCUMENT_ROOT'] . $block->Field);
				if(!$content){
					echo g_l('modules_newsletter', '[cannot_open]') . ": " . $_SERVER['DOCUMENT_ROOT'] . $block->Field;
				}
				break;
			case we_newsletter_block::URL:
				if($block->Field){
					if(substr(trim($block->Field), 0, 4) != "http"){
						$block->Field = "http://" . $block->Field;
					}

					$url = parse_url($block->Field);
					$content = getHTTP($url["host"], (isset($url["path"]) ? $url["path"] : ""), "", defined('HTTP_USERNAME') ? HTTP_USERNAME : "", defined('HTTP_PASSWORD') ? HTTP_PASSWORD : "");

					$trenner = '[ |\n|\t|\r]*';
					$patterns[] = "/<(img" . $trenner . "[^>]+src" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ? \\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie";
					$patterns[] = "/<(link" . $trenner . "[^>]+href" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ? \\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie";
					$match = array();

					foreach($patterns as $pattern){
						if(preg_match_all($pattern, $content, $match)){
							$unique = array_unique($match[2]);
							foreach($unique as $name){
								$src = parse_url($name);

								if(!isset($src["host"])){

									if(isset($src["path"])){
										$path = (dirname($src["path"]) ?
												dirname($src["path"]) . "/" :
												(isset($url["path"]) ?
													dirname($url["path"]) . "/" :
													''));
									}
									$newname = $url["scheme"] . "://" . preg_replace("|/+|", "/", $url["host"] . "/" . $path . basename($name));
									$content = str_replace($name, $newname, $content);
								}
							}
						}
					}
				}
				break;
			case we_newsletter_block::ATTACHMENT:
				break;
		}


		$port = (isset($this->settings["use_port"]) && $this->settings["use_port"]) ? ':' . $this->settings["use_port"] : '';
		$protocol = (isset($this->settings["use_https_refer"]) && $this->settings["use_https_refer"] ? 'https://' : 'http://');

		if($hm){
			if($block->Type != we_newsletter_block::URL){
				$spacer = '[ |\n|\t|\r]*';

				we_document::parseInternalLinks($content, 0);

				$urlReplace = we_folder::getUrlReplacements($this->db, false, true);
				if($urlReplace){
					$content = preg_replace('-(["\'])//-', '$1' . $protocol, preg_replace($urlReplace, array_keys($urlReplace), $content));
				}
				$content = preg_replace(array(
					'-(<[^>]+src' . $spacer . '=' . $spacer . '[\'"]?)(/)-i',
					'-(<[^>]+href' . $spacer . '=' . $spacer . '[\'"]?)(/)-i',
					'-(<[^>]+background' . $spacer . '=' . $spacer . '[\'"]?)(/)-i',
					'-(background' . $spacer . ':' . $spacer . '[^url]*url' . $spacer . '\\([\'"]?)(/)-i',
					'+(background-image' . $spacer . ':' . $spacer . '[^url]*url' . $spacer . '\\([\'"]?)(/)+i',
					), array(
					'${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					'${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					'${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					'${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					'${1}' . $protocol . $_SERVER['SERVER_NAME'] . $port . '${2}',
					), $content);
			}
		} else {
			$urlReplace = we_folder::getUrlReplacements($this->db, true, true);
			if($urlReplace){
				$content = str_replace('//', $protocol, preg_replace($urlReplace, array_keys($urlReplace), $content));
			}
			$newplain = preg_replace(array('|<br */? *>|', '|<title>.*</title>|i',), "\n", $content);
			if($block->Type != we_newsletter_block::TEXT){
				$newplain = strip_tags($newplain);
			}
			$newplain = preg_replace("|&nbsp;(&nbsp;)+|i", "\t", $newplain);
			$content = $newplain = str_ireplace(array('&nbsp;', '&lt;', '&gt;', '&quot;', '&amp;',), array(' ', '<', '>', '"', '&'), $newplain);
		}

		return $content;
	}

	function getBlockContents(){
		$content = array();
		$keys = array_keys($this->newsletter->blocks);
		foreach($keys as $kblock){
			$blockid = $kblock + 1;

			$content[] = array(
				'plain' => array(
					'defaultC' => $this->getContent($blockid, 0, 0, '', '', '', '', '###CUSTOMERID###'),
					'femaleC' => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'maleC' => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'title_firstname_lastnameC' => $this->getContent($blockid, 0, 0, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'title_lastnameC' => $this->getContent($blockid, 0, 0, '', '###TITLE###', '', '###LASTNAME###', '###CUSTOMERID###'),
					'firstname_lastnameC' => $this->getContent($blockid, 0, 0, '', '', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'firstnameC' => $this->getContent($blockid, 0, 0, '', '', '###FIRSTNAME###', '', '###CUSTOMERID###'),
					'lastnameC' => $this->getContent($blockid, 0, 0, '', '', '', '###LASTNAME###', '###CUSTOMERID###'),
					'default' => $this->getContent($blockid, 0, 0, '', '', '', '', ''),
					'female' => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'male' => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'title_firstname_lastname' => $this->getContent($blockid, 0, 0, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'title_lastname' => $this->getContent($blockid, 0, 0, '', '###TITLE###', '', '###LASTNAME###', ''),
					'firstname_lastname' => $this->getContent($blockid, 0, 0, '', '', '###FIRSTNAME###', '###LASTNAME###', ''),
					'firstname' => $this->getContent($blockid, 0, 0, '', '', '###FIRSTNAME###', '', ''),
					'lastname' => $this->getContent($blockid, 0, 0, '', '', '', '###LASTNAME###', ''),
				),
				'html' => array(
					'defaultC' => $this->getContent($blockid, 0, 1, '', '', '', '', '###CUSTOMERID###'),
					'femaleC' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'maleC' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'title_firstname_lastnameC' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '', '###LASTNAME###', '###CUSTOMERID###'),
					'title_lastnameC' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'firstname_lastnameC' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
					'firstnameC' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '', '###CUSTOMERID###'),
					'lastnameC' => $this->getContent($blockid, 0, 1, '', '', '', '###LASTNAME###', '###CUSTOMERID###'),
					'default' => $this->getContent($blockid, 0, 1, '', '', '', '', ''),
					'female' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'male' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'title_firstname_lastname' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '', '###LASTNAME###', ''),
					'title_lastname' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
					'firstname_lastname' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '###LASTNAME###', ''),
					'firstname' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '', ''),
					'lastname' => $this->getContent($blockid, 0, 1, '', '', '', '###LASTNAME###', ''),
			));
		}
		return $content;
	}

	function getGroupBlocks($group){
		$content = array();
		$count = count($this->newsletter->blocks);
		if($group == 0){
			for($i = 0; $i < $count; $i++){
				$content[] = $i;
			}
		} else {
			foreach($this->newsletter->blocks as $kblock => $block){
				if(strpos($block->Groups, "," . $group . ",") !== false){
					$content[] = $kblock;
				}
			}
		}
		return $content;
	}

	function getGroupsForEmail($email){
		$ret = array();

		if(is_array($this->newsletter->groups)){
			$keys = array_keys($this->newsletter->groups);
			foreach($keys as $gk){
				$emails = $this->getEmails($gk + 1, 0, 1);

				if(in_array($email, $emails)){
					$ret[] = $gk + 1;
				}
			}
		}

		return $ret;
	}

	function getAttachments($group){
		$atts = array();
		$dbtmp = new DB_WE();
		$this->db->query('SELECT LinkID FROM ' . NEWSLETTER_BLOCK_TABLE . ' WHERE NewsletterID=' . $this->newsletter->ID . ' AND Type=' . we_newsletter_block::ATTACHMENT . ($group ? ' AND FIND_IN_SET("' . $this->db->escape($group) . '",Groups)' : ''));

		while($this->db->next_record()){
			if($this->db->f("LinkID")){
				$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . $this->db->f("LinkID"), '', $dbtmp);

				if($path){
					$atts[] = $_SERVER['DOCUMENT_ROOT'] . $path;
				}
			}
		}
		return $atts;
	}

	function sendTestMail($group, $hm){
		$plain = "";
		$content = "";
		$inlines = array();

		$ret = $this->cacheNewsletter($this->newsletter->ID, false);
		$blocks = $this->getGroupBlocks($group);
		foreach($blocks as $i){
			if($hm){
				$block = $this->getFromCache($ret["blockcache"] . "_h_" . $i);
				$inlines = array_merge($inlines, $block["inlines"]);
				$content.=$block["default"];
				$block = $this->getFromCache($ret["blockcache"] . "_p_" . $i);
				$plain.=$block["default"];
			} else {
				$block = $this->getFromCache($ret["blockcache"] . "_p_" . $i);
				$content.=$block["default"];
				$plain.=$block["default"];
			}
		}

		$atts = $this->getAttachments($group);
		//$_clean = $this->getCleanMail($this->newsletter->Reply);
		$phpmail = new we_util_Mailer($this->newsletter->Test, $this->newsletter->Subject, $this->newsletter->Sender, $this->newsletter->Reply, $this->newsletter->isEmbedImages);
		if(!$this->settings["use_base_href"]){
			$phpmail->setIsUseBaseHref($this->settings["use_base_href"]);
		}
		$phpmail->setCharSet($this->newsletter->Charset ? : $GLOBALS['WE_BACKENDCHARSET']);
		if($hm){
			$phpmail->addHTMLPart($content);
		}
		$phpmail->addTextPart(trim($plain));
		foreach($atts as $att){
			$phpmail->doaddAttachment($att);
		}
		$phpmail->buildMessage();
		$phpmail->Send();

		$cc = 0;
		while(true){
			if(file_exists(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_p_" . $cc)){
				we_base_file::delete(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_p_" . $cc);
			} else {
				break;
			}

			//if(file_exists(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"]."_h_".$cc)) weFile::delete(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"]."_h_".$cc);
			if(file_exists(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_h_" . $cc)){
				$_buffer = unserialize(we_base_file::load(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_h_" . $cc));
				if(is_array($_buffer) && isset($_buffer['inlines'])){
					foreach($_buffer['inlines'] as $_fn){
						if(file_exists($_fn)){
							we_base_file::delete($_fn);
						}
					}
				}
				we_base_file::delete(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_h_" . $cc);
			} else {
				break;
			}
			$cc++;
		}
		foreach($inlines as $ins){
			we_base_file::delete($ins);
		}
	}

	function getFilterSQL($filter){
		$filterSQL = $filter["fieldname"];
		if($filter["fieldname"] === 'MemberSince' || $filter["fieldname"] === 'LastLogin' || $filter["fieldname"] === 'LastAccess'){
			if(stristr($filter['fieldvalue'], '.')){
				$date = explode(".", $filter['fieldvalue']);
				$day = $date[0];
				$month = $date[1];
				$year = $date[2];
				$hour = $filter['hours'];
				$minute = $filter['minutes'];
				$filter['fieldvalue'] = mktime($hour, $minute, 0, $month, $day, $year);
			}
		}

		switch($filter["operator"]){
			case we_newsletter_newsletter::OP_EQ:
				return $filterSQL . " = '" . $filter["fieldvalue"] . "'";
			case we_newsletter_newsletter::OP_NEQ:
				return $filterSQL . " <> '" . $filter["fieldvalue"] . "'";
			case we_newsletter_newsletter::OP_LE:
				return $filterSQL . " < '" . $filter["fieldvalue"] . "'";
			case we_newsletter_newsletter::OP_LEQ:
				return $filterSQL . " <= '" . $filter["fieldvalue"] . "'";
			case we_newsletter_newsletter::OP_GE:
				return $filterSQL . " > '" . $filter["fieldvalue"] . "'";
			case we_newsletter_newsletter::OP_GEQ:
				return $filterSQL . " >= '" . $filter["fieldvalue"] . "'";
			case we_newsletter_newsletter::OP_LIKE:
				return $filterSQL . " LIKE '" . $filter["fieldvalue"] . "'";
			case we_newsletter_newsletter::OP_CONTAINS:
				return $filterSQL . " LIKE '%" . $filter["fieldvalue"] . "%'";
			case we_newsletter_newsletter::OP_STARTS:
				return $filterSQL . " LIKE '" . $filter["fieldvalue"] . "%'";
			case we_newsletter_newsletter::OP_ENDS:
				return $filterSQL . " LIKE '%" . $filter["fieldvalue"] . "'";
		}
		return $filterSQL;
	}

	function getEmails($group, $select = 0, $emails_only = 0){

		update_time_limit(0);
		update_mem_limit(128);

		$extern = ($select == self::MAILS_ALL || $select == self::MAILS_FILE) ? we_newsletter_base::getEmailsFromExtern($this->newsletter->groups[$group - 1]->Extern, $emails_only, $group, $this->getGroupBlocks($group)) : array();

		if($select == self::MAILS_FILE){
			return $extern;
		}

		$list = ($select == self::MAILS_ALL || $select == self::MAILS_EMAILS) ? we_newsletter_base::getEmailsFromList($this->newsletter->groups[$group - 1]->Emails, $emails_only, $group, $this->getGroupBlocks($group)) : array();

		if($select == self::MAILS_EMAILS){
			return $list;
		}

		$customer_mail = $customers = array();

		if(defined('CUSTOMER_TABLE')){
			$filterarr = array();
			$filtera = $this->newsletter->groups[$group - 1]->getFilter();
			if($filtera){
				foreach($filtera as $k => $filter){
					$filterarr[] = ($k ? (' ' . $filter['logic'] . ' ') : ' ') . $this->getFilterSQL($filter);
				}
			}

			$filtersql = implode(' ', $filterarr);

			$customers = ($this->newsletter->groups[$group - 1]->SendAll ?
					'SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE ' . ($filtersql !== '' ? $filtersql : 1) :
					implode(',', array_map('intval', explode(',', $this->newsletter->groups[$group - 1]->Customers))));


			$_default_html = f('SELECT pref_value FROM ' . NEWSLETTER_PREFS_TABLE . ' WHERE pref_name="default_htmlmail";', 'pref_value', $this->db);
			$selectX = $this->settings['customer_email_field'] .
				($emails_only ? '' :
					',' . $this->settings['customer_html_field'] . ',' .
					$this->settings['customer_salutation_field'] . ',' .
					$this->settings['customer_title_field'] . ',' .
					$this->settings['customer_firstname_field'] . ',' .
					$this->settings['customer_lastname_field']
				);
			$this->db->query('SELECT ID,' . $selectX . ' FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $customers . ')' . ($filtersql ? ' AND (' . $filtersql . ')' : ''));
			while($this->db->next_record()){
				if($this->db->f($this->settings["customer_email_field"])){
					$email = trim($this->db->f($this->settings["customer_email_field"]));
					if($emails_only){
						$customer_mail[] = $email;
					} else {
						$htmlmail = ($this->settings["customer_html_field"] != 'ID' && trim($this->db->f($this->settings["customer_html_field"])) != '') ? trim($this->db->f($this->settings["customer_html_field"])) : $_default_html;
						$salutation = $this->settings["customer_salutation_field"] != 'ID' ? $this->db->f($this->settings["customer_salutation_field"]) : '';
						$title = $this->settings["customer_title_field"] != 'ID' ? $this->db->f($this->settings["customer_title_field"]) : '';
						$firstname = $this->db->f($this->settings["customer_firstname_field"]);
						$lastname = $this->db->f($this->settings["customer_lastname_field"]);

						// damd: Parmeter $customer (Kunden ID in der Kundenverwaltung) und Flag dass es sich um Daten aus der Kundenverwaltung handelt angehängt
						$customer = $this->db->f('ID');
						$customer_mail[] = array($email, $htmlmail, $salutation, $title, $firstname, $lastname, $group, $this->getGroupBlocks($group), $customer, 'customer');
					}
				}
			}
			if($select == self::MAILS_CUSTOMER){
				return $customer_mail;
			}
		}
		return array_merge($customer_mail, $list, $extern);
	}

	function getEmailsNum(){
		$out = 0;
		$count = count($this->newsletter->groups);
		for($i = 0; $i < $count; $i++){
			$out+=count($this->getEmails($i + 1, 0, 1));
		}
		return $out;
	}

	/**
	 * Static function - Settings
	 */
	static function getSettings(){
		$db = new DB_WE();
		$_domainName = str_replace("www.", "", $_SERVER['SERVER_NAME']);
		$ret = array(
			'black_list' => '',
			'customer_email_field' => 'Kontakt_Email',
			'customer_firstname_field' => 'Forename',
			'customer_html_field' => 'ID',
			'customer_lastname_field' => 'Surname',
			'customer_salutation_field' => '',
			'customer_title_field' => '',
			'default_htmlmail' => 0,
			'isEmbedImages' => 0,
			'default_reply' => 'replay@' . $_domainName,
			'default_sender' => 'mailer@' . $_domainName,
			we_newsletter_newsletter::FEMALE_SALUTATION_FIELD => g_l('modules_newsletter', '[default][female]'),
			'global_mailing_list' => '',
			'log_sending' => 1,
			we_newsletter_newsletter::MALE_SALUTATION_FIELD => g_l('modules_newsletter', '[default][male]'),
			'reject_malformed' => 1,
			'reject_not_verified' => 1,
			'send_step' => 20,
			'send_wait' => 0,
			'test_account' => 'test@' . $_domainName,
			'title_or_salutation' => 0,
			'use_port' => 0,
			'use_https_refer' => 0,
			'use_base_href' => 1
		);

		$db->query('SELECT pref_name,pref_value FROM ' . NEWSLETTER_PREFS_TABLE);
		while($db->next_record()){
			$ret[$db->f("pref_name")] = $db->f("pref_value");
		}
		//make sure blacklist is correct
		$ret['black_list'] = implode(',', array_map('trim', explode(',', $ret['black_list'])));

		return $ret;
	}

	function putSetting($name, $value){
		$db = new DB_WE();
		$db->query('INSERT IGNORE INTO ' . NEWSLETTER_PREFS_TABLE . ' SET ' . we_database_base::arraySetter(array('pref_name' => $name, pref_value => $value)));
	}

	function saveSettings(){
		$db = new DB_WE();
		// WORKARROUND BUG NR 7450
		foreach($this->settings as $key => $value){
			if($key != 'black_list'){
				$db->query('REPLACE INTO ' . NEWSLETTER_PREFS_TABLE . ' SET ' . we_database_base::arraySetter(array('pref_name' => $key, 'pref_value' => $value)));
			}
		}
	}

	function saveSetting($name, $value){
		$db = new DB_WE();
		$db->query('REPLACE INTO ' . NEWSLETTER_PREFS_TABLE . ' SET ' . we_database_base::arraySetter(array('pref_name' => $name, 'pref_value' => $value)));
	}

	function getBlackList(){
		return array();
	}

	function isBlack($email){
		static $black = 0;
		if(!$black){
			//remove whitespaces
			$black = explode(',', strtolower($this->settings['black_list']));
			foreach($black as &$b){
				$b = trim($b, " \t\n\r\n"); //intentionally duplicate \n!
			}
		}
		return in_array(trim(strtolower($email), " \t\n\r\n"), $black);
	}

	/**
	 * Write newsletter and mailing lists temp files
	 *
	 * @param Integer $nid
	 * @param Boolean $cachemails
	 * @return Array
	 */
	function cacheNewsletter($nid = 0, $cachemails = true){

		$ret = array();
		if($nid)
			$this->newsletter = new we_newsletter_newsletter($nid);

		if($cachemails){
			// BEGIN cache emails groups
			$emailcache = we_base_file::getUniqueId();
			$groupcount = count($this->newsletter->groups) + 1;

			$ret["emailcache"] = $emailcache;
			$buffer = array();

			for($groupid = 1; $groupid < $groupcount; $groupid++){
				$tmp = $this->getEmails($groupid);
				$tcount = count($tmp);
				for($t = 0; $t < $tcount; $t++){
					if(isset($tmp[$t][0]) && isset($tmp[$t][7]) && count($tmp[$t][7])){
						$index = strtolower($tmp[$t][0]);
						if(isset($buffer[$index])){
							if(!in_array($tmp[$t][6], explode(",", $buffer[$index][6]))){
								$buffer[$index][6].="," . $tmp[$t][6];
							}
							$buffer[$index][7] = array_merge($buffer[$index][7], $tmp[$t][7]);
						} else {
							$buffer[$index] = $tmp[$t];
						}
					}
				}
			}


			$cc = 0;
			foreach($buffer as $k => $one){
				$buffer[$cc] = $one;
				unset($buffer[$k]);
				$cc++;
			}

			$ret["ecount"] = count($buffer);

			$groups = 0;
			$tmp = array();
			$go = true;
			$offset = 0;


			while($go){
				$tmp = array_slice($buffer, $offset, $this->settings["send_step"]);
				if(!empty($tmp)){
					$offset+=$this->settings["send_step"];
					$groups++;
					$this->saveToCache(serialize($tmp), $emailcache . "_$groups");
				} else {
					$go = false;
				}
			}

			$ret["gcount"] = $groups + 1;
		}

		// END cache emails groups
		// BEGIN cache newlsetter blocks
		$blockcache = we_base_file::getUniqueId();
		$blockcount = count($this->newsletter->blocks);

		$ret["blockcache"] = $blockcache;

		for($blockid = 0; $blockid < $blockcount; $blockid++){

			$this->saveToCache(serialize(array(
				"defaultC" => $this->getContent($blockid, 0, 0, "", "", "", "", "###CUSTOMERID###"),
				"femaleC" => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###"),
				"maleC" => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###"),
				"title_firstname_lastnameC" => $this->getContent($blockid, 0, 0, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###"),
				"title_lastnameC" => $this->getContent($blockid, 0, 0, "", "###TITLE###", "", "###LASTNAME###", "###CUSTOMERID###"),
				"firstname_lastnameC" => $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###"),
				"firstnameC" => $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "", "###CUSTOMERID###"),
				"lastnameC" => $this->getContent($blockid, 0, 0, "", "", "", "###LASTNAME###", "###CUSTOMERID###"),
				"default" => $this->getContent($blockid, 0, 0, "", "", "", "", ""),
				"female" => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", ""),
				"male" => $this->getContent($blockid, 0, 0, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", ""),
				"title_firstname_lastname" => $this->getContent($blockid, 0, 0, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", ""),
				"title_lastname" => $this->getContent($blockid, 0, 0, "", "###TITLE###", "", "###LASTNAME###", ""),
				"firstname_lastname" => $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "###LASTNAME###", ""),
				"firstname" => $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "", ""),
				"lastname" => $this->getContent($blockid, 0, 0, "", "", "", "###LASTNAME###", ""),
				)), $blockcache . "_p_" . $blockid);

			$this->saveToCache(serialize(array(
				'defaultC' => $this->getContent($blockid, 0, 1, '', '', '', '', '###CUSTOMERID###'),
				'femaleC' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
				'maleC' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
				'title_firstname_lastnameC' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
				'title_lastnameC' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '', '###LASTNAME###', '###CUSTOMERID###'),
				'firstname_lastnameC' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###'),
				'firstnameC' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '', '###CUSTOMERID###'),
				'lastnameC' => $this->getContent($blockid, 0, 1, '', '', '', '###LASTNAME###', '###CUSTOMERID###'),
				'default' => $this->getContent($blockid, 0, 1, '', '', '', '', ''),
				'female' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
				'male' => $this->getContent($blockid, 0, 1, $this->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
				'title_firstname_lastname' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '###FIRSTNAME###', '###LASTNAME###', ''),
				'title_lastname' => $this->getContent($blockid, 0, 1, '', '###TITLE###', '', '###LASTNAME###', ''),
				'firstname_lastname' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '###LASTNAME###', ''),
				'firstname' => $this->getContent($blockid, 0, 1, '', '', '###FIRSTNAME###', '', ''),
				'lastname' => $this->getContent($blockid, 0, 1, '', '', '', '###LASTNAME###', ''),
				'inlines' => ($this->newsletter->blocks[$blockid]->Pack ? $this->cacheInlines($buffer) : array()),
				)), $blockcache . '_h_' . $blockid);
		}
		// END cache newlsetter blocks

		return $ret;
	}

	function cacheInlines(&$buffer){

		$trenner = '[ |\n|\t|\r]*';
		$patterns = array(
			"/<(img" . $trenner . "[^>]+src" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ?\\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie",
			"/<(body" . $trenner . "[^>]+background" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ?\\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie",
			"/<(table" . $trenner . "[^>]+background" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ?\\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie",
			"/<(td" . $trenner . "[^>]+background" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\"> ?\\\]*)([^\"\' \\\\>]*)(" . $trenner . "[^>]*)>/sie",
			"/background" . $trenner . ":" . $trenner . "([^url]*url" . $trenner . "\([\"|\'|\\\\])?(.[^\)|^\"|^\'|^\\\\]+)([\"|\'|\\\\])?/sie",
			"/background-image" . $trenner . ":" . $trenner . "([^url]*url" . $trenner . "\([\"|\'|\\\\])?(.[^\)|^\"|^\'|^\\\\]+)([\"|\'|\\\\])?/sie",
		);

		$match = array();
		$inlines = array();

		foreach($buffer as $v){
			foreach($patterns as $pattern){
				if(preg_match_all($pattern, $v, $match)){
					foreach($match[2] as $name){
						if(!in_array($name, array_keys($inlines))){
							$newname = WE_NEWSLETTER_CACHE_DIR . we_base_file::getUniqueID();
							$inlines[$name] = $newname;

							$fcontent = we_base_file::load($name);
							$fcontent = chunk_split(base64_encode($fcontent), 76, "\n");
							we_base_file::save($newname, $fcontent);
						}
					}
				}
			}
		}
		return $inlines;
	}

	function getFromCache($cache){
		$cache = WE_NEWSLETTER_CACHE_DIR . basename($cache);
		$buffer = we_base_file::load($cache);
		return ($buffer ? unserialize($buffer) : array());
	}

	function getCleanMail($mail){
		$_match = array();
		$_pattern = '|[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,6}|i';
		if(preg_match($_pattern, $mail, $_match)){
			return ($_match[0]);
		}
		return '';
	}

	function saveToCache($content, $filename){
		if(!is_dir(WE_NEWSLETTER_CACHE_DIR)){
			we_base_file::createLocalFolder(WE_NEWSLETTER_CACHE_DIR);
		}

		$filename = WE_NEWSLETTER_CACHE_DIR . basename($filename);
		return we_base_file::save($filename, $content);
	}

	public function getShowImportBox(){
		return $this->show_import_box;
	}

	public function getShowExportBox(){
		return $this->show_export_box;
	}

}
