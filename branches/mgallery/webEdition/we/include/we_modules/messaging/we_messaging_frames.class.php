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
class we_messaging_frames extends we_modules_frame{
	var $db;
	var $View;
	var $frameset;
	public $transaction;
	public $weTransaction;
	protected $messaging;
	public $viewclass;
	public $module = "messaging";
	protected $hasIconbar = true;
	protected $useMainTree = false;
	protected $treeDefaultWidth = 204;

	const TYPE_MESSAGE = 1;
	const TYPE_TODO = 2;

	public function __construct($viewclass, $reqTransaction, &$weTransaction){
		parent::__construct(WE_MESSAGING_MODULE_DIR . "edit_messaging_frameset.php");

		$this->transaction = $reqTransaction;
		$this->weTransaction = &$weTransaction;
		$this->viewclass = $viewclass;
		$this->View = new we_messaging_view(WE_MESSAGING_MODULE_DIR . "edit_messaging_frameset.php", "top.content", $this->transaction, $this->weTransaction);
//		$this->Tree = new we_messaging_tree($this->frameset, "top.content", "top.content", "top.content.cmd");
	}

	function getHTML($what){
		switch($what){
			default:
				return parent::getHTML($what);
			case "msg_fv_headers":
				return $this->getHTMLFvHeaders();
			case 'iconbar':
				return $this->getHTMLIconbar();
		}
		exit();
	}

	function getJSCmdCode(){
		return $this->View->getJSTop_tmp();
	}

	function getJSTreeCode(){ //TODO: move to new class weUsersTree (extends weModulesTree)
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';
		if(($param = we_base_request::_(we_base_request::INT, 'msg_param'))){
			switch($param){
				case self::TYPE_TODO:
					$f = $this->messaging->get_inbox_folder('we_todo');
					break;
				case self::TYPE_MESSAGE:
					$f = $this->messaging->get_inbox_folder('we_message');
					break;
			}
		}

		$jsinit = '
var tree_icon_dir="' . TREE_ICON_DIR . '";
var tree_img_dir="' . TREE_IMAGE_DIR . '";
var we_dir="' . WEBEDITION_DIR . '";
var messaging_module_dir="' . WE_MESSAGING_MODULE_DIR . '";

parent.document.title = "' . $title . '";
we_transaction = "' . $this->transaction . '";
var we_frameset="' . $this->frameset . '";'
			. parent::getTree_g_l() . '
var table="' . MESSAGES_TABLE . '";
var save_changed_folder="' . g_l('modules_messaging', '[save_changed_folder]') . '";
';

		$jsOut = '
function cb_incstate() {
		loaded = true;
		loadData();
		' . (isset($f) ?
				'r_tree_open(' . $f['ID'] . ');
we_cmd("show_folder_content", ' . $f['ID'] . ');' :
				'drawEintraege();'
			) . '
}

function translate(inp){
	if(inp.substring(0,12).toLowerCase() == "messages - ("){
		return "' . g_l('modules_messaging', '[Mitteilungen]') . ' - ("+inp.substring(12,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "task - ("){
		return "' . g_l('modules_messaging', '[ToDo]') . ' - ("+inp.substring(8,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "todo - ("){
		return "' . g_l('modules_messaging', '[ToDo]') . ' - ("+inp.substring(8,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "done - ("){
		return "' . g_l('modules_messaging', '[Erledigt]') . ' - ("+inp.substring(8,inp.length);
	}else if(inp.substring(0,12).toLowerCase() == "rejected - ("){
		return "' . g_l('modules_messaging', '[Zurueckgewiesen]') . ' - ("+inp.substring(12,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "sent - ("){
		return "' . g_l('modules_messaging', '[Gesendet]') . ' - ("+inp.substring(8,inp.length);
	}else{
		return inp;
	}

}

function loadData() {
	treeData.clear();
		';

		$jsOut .= '
	startloc=0;
	treeData.add(new self.rootEntry("0","root","root"));
		';

		foreach($this->messaging->available_folders as $folder){
			switch($folder['obj_type']){
				case we_messaging_proto::FOLDER_INBOX:
					$iconbasename = $folder['ClassName'] === 'we_todo' ? 'todo_in_folder' : 'msg_in_folder';
					$folder['Name'] = g_l('modules_messaging', $folder['ClassName'] === 'we_todo' ? '[ToDo]' : '[Mitteilungen]');
					break;
				case we_messaging_proto::FOLDER_SENT:
					$iconbasename = 'msg_sent_folder';
					$folder['Name'] = g_l('modules_messaging', '[Gesendet]');
					break;
				case we_messaging_proto::FOLDER_DONE:
					$iconbasename = 'todo_done_folder';
					$folder['Name'] = g_l('modules_messaging', '[Erledigt]');
					break;
				case we_messaging_proto::FOLDER_REJECT:
					$iconbasename = 'todo_reject_folder';
					$folder['Name'] = g_l('modules_messaging', '[Zurueckgewiesen]');
					break;
				default:
					$iconbasename = $folder['ClassName'] === 'we_todo' ? 'todo_folder' : 'msg_folder';
					break;
			}
			$jsOut .= '
	treeData.add(' .
				(($sf_cnt = $this->messaging->get_subfolder_count($folder['ID'])) >= 0 ?
					'new dirEntry("",' . $folder['ID'] . ',' . $folder['ParentID'] . ',"' . $folder['Name'] . ' - (' . $this->messaging->get_message_count($folder['ID'], '') . ')",false,"folder","' . MESSAGES_TABLE . '", ' . $sf_cnt . ', "' . $iconbasename . '", "' . $folder['view_class'] . '")
				' :
					'new urlEntry("",' . $folder['ID'] . ',' . $folder['ParentID'] . ',"' . $folder['Name'] . ' - (' . $this->messaging->get_message_count($folder['ID'], '') . ')","folder","' . MESSAGES_TABLE . '", "' . $iconbasename . '", "' . $folder['view_class'] . '")
				') . ');';
		}
		$jsOut .= '
}

		';
		return we_html_element::cssLink(CSS_DIR . 'tree.css') .
			we_html_element::jsElement($jsinit) .
			we_html_element::jsScript(JS_DIR . 'tree.js', 'self.focus();') .
			we_html_element::jsScript(JS_DIR . 'messaging_tree.js') .
			we_html_element::jsElement($jsOut);
	}

	protected function getHTMLTree($extraHead = ''){
		return parent::getHTMLTree(
				we_html_element::jsScript(JS_DIR . 'tree.js') .
				we_html_element::jsScript(JS_DIR . 'messaging_tree.js'));
	}

	function getHTMLFrameset(){
		$this->transaction = $this->weTransaction;

		$this->messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$this->transaction]);
		$this->messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);

		if(!$this->messaging->check_folders()){
			if(!we_messaging_messaging::createFolders($_SESSION["user"]["ID"])){
				$extraHead .= we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[cant_create_folders]'), we_message_reporting::WE_MESSAGE_ERROR));
			}
		}

		$this->messaging->init($_SESSION['weS']['we_data'][$this->transaction]);
		$this->messaging->add_msgobj('we_message', 0);
		$this->messaging->add_msgobj('we_todo', 0);
		$this->messaging->add_msgobj('we_msg_email', 0);
		$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);

		//TODO: move to a better place: jsTop()
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		$extraHead = $this->getJSCmdCode() .
			we_html_element::jsScript(JS_DIR . 'we_modules/messaging/messaging_std.js') .
			we_html_element::jsScript(JS_DIR . 'we_modules/messaging/messaging_hl.js') .
			$this->getJSTreeCode() .
			we_html_element::jsElement($this->getJSStart());

		return parent::getHTMLFrameset($extraHead, '&we_transaction=' . $this->transaction);
	}

	private function getHTMLIconbar(){
		$iconbar = new we_messaging_iconbar($this);
		return $iconbar->getHTML();
	}

	function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody(array(), ''), $this->View->processCommands());
	}

	function getHTMLSearch(){

	}

	protected function getHTMLEditor(){
		$body = we_html_element::htmlBody(array('style' => 'position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;'), we_html_element::htmlIFrame('edheader', $this->frameset . '?pnt=edheader&we_transaction=' . $this->transaction, 'position: absolute; top: 0px; left: 0px; right: 0px; height: 35px; overflow: hidden;', 'width: 100%; overflow: hidden', '', '', false) .
				we_html_element::htmlIFrame('edbody', $this->frameset . '?pnt=edbody&we_transaction=' . $this->transaction, 'position: absolute; top: 35px; bottom: 0px; left: 0px; right: 0px; overflow: auto;', 'border:0px;width:100%;height:100%;overflow: auto;')
		);

		return $this->getHTMLDocument($body);
	}

	protected function getHTMLEditorHeader(){
		$extraHead = we_html_element::jsElement('
			function doSearch() {
				top.content.cmd.location = "' . $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=cmd&mcmd=search_messages&searchterm=" + document.we_messaging_search.messaging_search_keyword.value;
			}

			function launchAdvanced() {
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'messaging_search_advanced.php?we_transaction=' . $this->transaction . '","messaging_search_advanced",-1,-1,300,240,true,false,true,false);
			}

			function clearSearch() {
				document.we_messaging_search.messaging_search_keyword.value = "";
				doSearch();
			}
		') . we_html_element::jsScript(JS_DIR . 'windows.js');

		$searchlabel = $this->viewclass === 'todo' ? '[search_todos]' : '[search_messages]';
		$hidden = we_html_tools::hidden('we_transaction', $this->transaction);
		$table = new we_html_table(array('style' => 'margin: 4px 0px 0px 7px;', 'border' => 0), 1, 2);

		$table->setCol(0, 0, array('class' => 'defaultfont'), g_l('modules_messaging', $searchlabel) .
			we_html_tools::getPixel(10, 1) .
			we_html_tools::htmlTextInput('messaging_search_keyword', 15, we_base_request::_(we_base_request::RAW, 'messaging_search_keyword', ''), 15) .
			we_html_tools::getPixel(10, 1)
		);

		$buttons = we_html_button::create_button_table(array(
				we_html_button::create_button(we_html_button::SEARCH, "javascript:doSearch();"),
				we_html_button::create_button("advanced", "javascript:launchAdvanced()", true),
				we_html_button::create_button("reset_search", "javascript:clearSearch();")), 10);

		$table->setCol(0, 1, array('class' => 'defaultfont'), $buttons);
		$form = we_html_element::htmlForm(
				array('name' => 'we_messaging_search', 'action' => $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=edheader&viewclass=' . $this->viewclass, 'onSubmit' => 'return doSearch()'), $hidden . $table->getHtml()
		);

		return $this->getHTMLDocument(we_html_element::htmlBody(array('style' => 'background-color: white;border-top:1px solid black;'), we_html_element::htmlNobr($form)), $extraHead);
	}

	protected function getHTMLEditorBody(){
		$content = we_html_element::htmlIFrame('messaging_fv_headers', $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=msg_fv_headers', 'position:absolute;top:0px;height:26px;left:0px;right:0px;', '', '', false) .
			we_html_element::htmlIFrame('x', 'about:blank', 'display:none', '', '', false) . //FIXME: is this command window??
			we_html_element::htmlDiv(array('style' => 'position:absolute;top:26px;bottom:26px;left:0px;right:0px;'), we_html_element::htmlIFrame('messaging_messages_overview', HTML_DIR . 'white.html', 'position:absolute;top:0px;height:160px;left:0px;right:0px;border-bottom:1px solid black;', '', '', true) .
				we_html_element::htmlIFrame('messaging_msg_view', HTML_DIR . 'white.html', 'position:absolute;top:160px;bottom:0px;left:0px;right:0px;', '', '', true)
		);

		return $this->getHTMLDocument(we_html_element::htmlBody(array('onload' => "top.content.cb_incstate();"), $content));
	}

	function getHTMLFvHeaders(){

		$this->transaction = $this->transaction != 'no_request' ? $this->transaction : $this->weTransaction;
		$this->transaction = (preg_match('|^([a-f0-9]){32}$|i', $this->transaction) ? $this->transaction : 0);

		$extraHead = we_html_element::jsElement('
			function doSort(sortitem) {
				entrstr = "";
				top.content.cmd.location = "' . $this->frameset . '?pnt=cmd&mcmd=show_folder_content&sort=" + sortitem + entrstr + "&we_transaction=' . $this->transaction . '";
			}');

		$colsArray = we_base_request::_(we_base_request::STRING, "viewclass") != "todo" ? array(
			array(200, 'subject', '[subject]'),
			array(170, 'date', '[date]'),
			array(120, 'sender', '[from]'),
			array(70, 'isread', '[is_read]'),
			) : array(
			array(200, 'subject', '[subject]'),
			array(170, 'deadline', '[deadline]'),
			array(120, 'priority', '[priority]'),
			array(70, 'status', '[status]'),
		);

		$table = new we_html_table(array(
			'style' => 'margin: 5px 0 0 0px',
			'border' => 0,
			'cellpadding' => 0,
			'cellspacing' => 0,
			'width' => '100%'), 1, count($colsArray) + 1);

		$table->setCol(0, 0, array('width' => 18), '');
		for($i = 0; $i < count($colsArray); $i++){
			$table->setCol(0, $i + 1, array('class' => 'tableHeader defaultfont', 'width' => $colsArray[$i][0]), '<a href="javascript:doSort(\'' . $colsArray[$i][1] . '\');">' . g_l('modules_messaging', $colsArray[$i][2]) .
				'&nbsp;' . (we_base_request::_(we_base_request::STRING, "si") == $colsArray[$i][1] ? self::sort_arrow(we_base_request::_(we_base_request::STRING, 'so'), "") : we_html_tools::getPixel(1, 1)) . '</a>'
			);
		}

		return $this->getHTMLDocument(we_html_element::htmlBody($attribs = array('id' => 'eHeaderBody'), $table->getHTML()), $extraHead);
	}

	protected function getHTMLEditorFooter(){

	}

	//some utility functions
	public static function sort_arrow($order, $href=''){
		$dir = ($order == 'asc' ? 'up' : 'down');

		// Check if we have to create a form or href
		return $href ? '<a href="' . $href . '"><i class="fa fa-lg fa-' . $dir . '"></i></a>' :
			'<i class="fa fa-lg fa-' . $dir . '"></i>';
	}

	function getJSStart(){
		return 'function start(){}'; //tree is started somewhere else!
	}

}
