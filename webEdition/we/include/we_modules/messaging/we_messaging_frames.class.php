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
	var $frameset;
	public $transaction;
	public $weTransaction;
	protected $messaging;
	public $viewclass;

	const TYPE_MESSAGE = 1;
	const TYPE_TODO = 2;

	public function __construct($framset, $viewclass, $reqTransaction, $weTransaction){
		parent::__construct($framset);
		$this->module = "messaging";
		$this->treeDefaultWidth = 204;

		$this->hasIconbar = true;
		$this->transaction = $reqTransaction;
		$this->weTransaction = &$weTransaction;
		$this->viewclass = $viewclass;
		$this->View = new we_messaging_view($framset, $this->transaction, $this->weTransaction);
		$this->Tree = new we_messaging_tree($this->frameset, "top.content", "top.content", "top.content.cmd", $this->weTransaction);
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			default:
				return parent::getHTML($what, $mode, $step);
			case "msg_fv_headers":
				return $this->getHTMLFvHeaders();
			case 'iconbar':
				return $this->getHTMLIconbar();
			case 'tree':
				return $this->getHTMLTree(we_html_element::jsScript(JS_DIR . 'tree.js') .
						we_html_element::jsScript(JS_DIR . 'messaging_tree.js'));
		}
		exit();
	}

	protected function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		$this->transaction = $this->weTransaction;

		$this->messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$this->transaction]);
		$this->messaging->set_login_data($_SESSION['user']["ID"], $_SESSION['user']["Username"]);

		if(!$this->messaging->check_folders()){
			if(!we_messaging_messaging::createFolders($_SESSION['user']["ID"])){
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

		$extraHead = we_html_element::jsScript(WE_JS_MODULES_DIR . 'messaging/messaging_std.js') .
			$this->Tree->getMsgJSTreeCode($this->messaging) .
			we_html_element::jsElement('startTree();');

		return parent::getHTMLFrameset($extraHead, '&we_transaction=' . $this->transaction);
	}

	private function getHTMLIconbar(){
		$iconbar = new we_messaging_iconbar($this);
		return $iconbar->getHTML();
	}

	protected function getHTMLCmd(){
		$head = $this->View->processCommands();

		if(($pid = we_base_request::_(we_base_request::INT, 'pnt')) !== false){
			$offset = we_base_request::_(we_base_request::INT, "offset", 0);

			$tree = we_html_element::jsElement(
					($pid ? '' :
						'top.content.treeData.clear();
top.content.treeData.add(top.content.node.prototype.rootEntry(' . $pid . ',\'root\',\'root\'));'
					) .
					$this->Tree->getJSLoadTree(!$pid, we_messaging_tree::getItems($pid, 0, $this->Tree->default_segment, $this->View->getMessaging()))
			);
		} else {
			$tree = '';
		}
		return $this->getHTMLDocument(we_html_element::htmlBody(), $head . $tree);
	}

	protected function getHTMLEditor($extraUrlParams = '', $extraHead = ''){
		$body = we_html_element::htmlBody(array('class' => 'moduleEditor'), we_html_element::htmlIFrame('edheader', WEBEDITION_DIR . 'we_showMod.php?mod=messaging&pnt=edheader&we_transaction=' . $this->transaction, 'position: absolute; top: 0px; left: 0px; right: 0px; height: 35px; overflow: hidden;', 'width: 100%; overflow: hidden', '', '', false) .
				we_html_element::htmlIFrame('edbody', WEBEDITION_DIR . 'we_showMod.php?mod=messaging&pnt=edbody&we_transaction=' . $this->transaction, 'position: absolute; top: 35px; bottom: 0px; left: 0px; right: 0px;', 'border:0px;width:100%;height:100%;')
		);

		return $this->getHTMLDocument($body);
	}

	protected function getHTMLEditorHeader($mode = 0){
		$extraHead = we_html_element::jsElement('
WE().consts.dirs.WE_MESSAGING_MODULE_DIR="' . WE_MESSAGING_MODULE_DIR . '";
WE().util.loadConsts("g_l.messaging");
function doSearch() {
	top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&we_transaction=' . $this->transaction . '&pnt=cmd&mcmd=search_messages&searchterm=" + document.we_messaging_search.messaging_search_keyword.value;
}

function launchAdvanced() {
	new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR+"messaging_search_advanced.php?we_transaction=' . $this->transaction . '","messaging_search_advanced",-1,-1,300,240,true,false,true,false);
}

function clearSearch() {
	document.we_messaging_search.messaging_search_keyword.value = "";
	doSearch();
}
');

		$searchlabel = $this->viewclass === 'todo' ? '[search_todos]' : '[search_messages]';
		$hidden = we_html_element::htmlHidden('we_transaction', $this->transaction);
		$table = new we_html_table(array('style' => 'margin: 4px 0px 0px 7px;', 'border' => 0), 1, 2);

		$table->setCol(0, 0, array('class' => 'defaultfont', 'style' => 'padding-left:10px;'), g_l('modules_messaging', $searchlabel) .
			we_html_tools::htmlTextInput('messaging_search_keyword', 15, we_base_request::_(we_base_request::STRING, 'messaging_search_keyword', ''), 15));

		$buttons = we_html_button::create_button(we_html_button::SEARCH, "javascript:doSearch();") .
			we_html_button::create_button('advanced', "javascript:launchAdvanced()") .
			we_html_button::create_button('reset_search', "javascript:clearSearch();");

		$table->setCol(0, 1, ['class' => 'defaultfont'], $buttons);
		$form = we_html_element::htmlForm(
				array('name' => 'we_messaging_search', 'action' => WEBEDITION_DIR . 'we_showMod.php?mod=messaging&we_transaction=' . $this->transaction . '&pnt=edheader&viewclass=' . $this->viewclass, 'onSubmit' => 'return doSearch()'), $hidden . $table->getHtml()
		);

		return $this->getHTMLDocument(we_html_element::htmlBody(['style' => 'border-top:1px solid black;'], we_html_element::htmlNobr($form)), $extraHead);
	}

	protected function getHTMLEditorBody(){
		$content = we_html_element::htmlIFrame('messaging_fv_headers', WEBEDITION_DIR . 'we_showMod.php?mod=messaging&we_transaction=' . $this->transaction . '&pnt=msg_fv_headers', 'position:absolute;top:0px;height:26px;left:0px;right:0px;', '', '', false) .
			we_html_element::htmlIFrame('x', 'about:blank', 'display:none', '', '', false) . //FIXME: is this command window??
			we_html_element::htmlDiv(array('style' => 'position:absolute;top:26px;bottom:26px;left:0px;right:0px;'), we_html_element::htmlIFrame('messaging_messages_overview', 'about:blank', 'position:absolute;top:0px;height:160px;left:0px;right:0px;border-bottom:1px solid black;', '', '', true) .
				we_html_element::htmlIFrame('messaging_msg_view', 'about:blank', 'position:absolute;top:160px;bottom:0px;left:0px;right:0px;', '', '', true)
		);

		return $this->getHTMLDocument(we_html_element::htmlBody(array('onload' => "top.content.cb_incstate();"), $content));
	}

	function getHTMLFvHeaders(){
		$t = $this->transaction != 'no_request' ? $this->transaction : $this->weTransaction;
		$this->transaction = (preg_match('|^([a-f0-9]){32}$|i', $t) ? $t : 0);

		$extraHead = we_html_element::jsElement('
			function doSort(sortitem) {
				entrstr = "";
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&mcmd=show_folder_content&sort=" + sortitem + entrstr + "&we_transaction=' . $this->transaction . '";
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
			'style' => 'margin: 5px 0px 0px 0px',
			'class' => 'default',
			'width' => '100%'), 1, count($colsArray) + 1);

		$table->setCol(0, 0, array('width' => 18), '');
		for($i = 0; $i < count($colsArray); $i++){
			$table->setCol(0, $i + 1, array('class' => 'tableHeader defaultfont', 'width' => $colsArray[$i][0]), '<a href="javascript:doSort(\'' . $colsArray[$i][1] . '\');">' . g_l('modules_messaging', $colsArray[$i][2]) .
				'&nbsp;' . (we_base_request::_(we_base_request::STRING, "si") == $colsArray[$i][1] ? self::sort_arrow(we_base_request::_(we_base_request::STRING, 'so'), "") : '') . '</a>'
			);
		}

		return $this->getHTMLDocument(we_html_element::htmlBody($attribs = array('id' => 'eHeaderBody'), $table->getHTML()), $extraHead);
	}

	protected function getHTMLEditorFooter(array $btn_cmd = [], $extraHead = ''){

	}

	//some utility functions
	public static function sort_arrow($order, $href = ''){
		$dir = ($order == 'asc' ? 'up' : 'down');

		// Check if we have to create a form or href
		return $href ? '<a href="' . $href . '"><i class="fa fa-lg fa-' . $dir . '"></i></a>' :
			'<i class="fa fa-lg fa-' . $dir . '"></i>';
	}

}
