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
abstract class we_tool_frames extends we_modules_frame{
	var $Table;
	var $TreeSource = 'table:';
	var $toolName;
	var $toolClassName;
	var $toolDir;
	var $toolUrl;
	var $_space_size = 120;
	var $_width_size = 520;
	var $Model;

	function __construct($frameset){
		parent::__construct($frameset);
		$this->treeFooterHeight = 40;
		$this->treeWidth = 200;
	}

	function getHTML($what){
		switch($what){
			case 'frameset':
				return $this->getHTMLFrameset();
			case 'header':
				return $this->getHTMLHeader();
			case 'resize':
				return $this->getHTMLResize();
			case 'editor':
				return $this->getHTMLEditor();
			case 'edheader':
				return $this->getHTMLEditorHeader();
			case 'edbody':
				return $this->getHTMLEditorBody();
			case 'edfooter':
				return $this->getHTMLEditorFooter();
			case 'cmd':
				return $this->getHTMLCmd();
			case 'treeheader':
				return $this->getHTMLTreeHeader();
			case 'treefooter':
				return $this->getHTMLTreeFooter();
			case 'exit_doc_question':
				return $this->getHTMLExitQuestion();
			default:
				t_e(__FILE__ . " unknown reference: $what");
				return '';
		}
	}

	//TODO: call parent after if(){}
	function getHTMLFrameset(){
		$this->setTreeWidthFromCookie();

		$this->Model->clearSessionVars();

		if(($modelid = we_base_request::_(we_base_request::INT, 'modelid'))){
			$_class = we_tool_lookup::getModelClassName($this->toolName);
			$this->Model = new $_class();
			$this->Model->load($modelid);
			$this->Model->saveInSession();
			$_SESSION['weS'][$this->toolName]["modelidForTree"] = $modelid;
		}

		$js = $this->getJSCmdCode() .
			$this->Tree->getJSTreeCode() .
			self::getJSToggleTreeCode($this->toolName) .
			we_main_headermenu::css();

		$body = we_html_element::htmlBody(array('id' => 'weMainBody', "onload" => $this->getJSStart())
				, we_html_element::htmlExIFrame('header', parent::getHTMLHeader($this->toolDir . 'conf/we_menu_' . $this->toolName . '.conf.php', $this->toolName)) .
				$this->getHTMLResize() .
				/* we_html_element::htmlIFrame('resize', $this->frameset . '?pnt=resize' . (($tab = we_base_request::_(we_base_request::INT, 'tab')) ? '&tab=' . $tab : '') . ($modelid ? '&modelid=' . $modelid : '') . (($sid = we_base_request::_(we_base_request::INT, 'sid')) ? '&sid=' . $sid : ''), 'overflow: hidden;', '', '', false) . */
				we_html_element::htmlIFrame('cmd', $this->frameset . '?pnt=cmd' . ($modelid ? '&modelid=' . $modelid : ''))
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getJSCmdCode(){
		return $this->View->getJSTop();
	}

	/**
	 * Top frame with menu
	 *
	 * @return string
	 */
	function getHTMLHeader(){
		//	Include the menu.
		include($this->toolDir . 'conf/we_menu_' . $this->toolName . '.conf.php');

		$lang_arr = 'we_menu_' . $this->toolName;
		$jmenu = new we_base_menu($$lang_arr, $this->topFrame . '.cmd');
		$menu = $jmenu->getCode();

		$table = new we_html_table(array("width" => "100%", "class" => 'default'), 1, 2);
		$table->setCol(0, 0, array('style' => 'text-align:left;vertical-align:top'), $menu);
		$table->setCol(0, 1, array('style' => 'text-align:right;vertical-align:top;'), we_main_headermenu::createMessageConsole('toolFrame'));

		$body = we_html_element::htmlBody(array('id' => 'toolMenu'), $table->getHtml());

		return $this->getHTMLDocument($body);
	}

	/**
	 * Frame for tubs
	 *
	 * @return string
	 */
	protected function getHTMLEditorHeader(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => '#F0EFF0'), ''));
		}

		$we_tabs = new we_tabs();
		$we_tabs->addTab(new we_tab(g_l('tools', '[properties]'), '((' . $this->topFrame . '.activ_tab==1) ? ' . we_tab::ACTIVE . ': ' . we_tab::NORMAL . ')', "setTab('1');", array("id" => "tab_1")));

		$tabsHead = $we_tabs->getHeader() .
			we_html_element::jsElement('
function mark() {
	var elem = document.getElementById("mark");
	elem.style.display = "inline";

}

function unmark() {
	var elem = document.getElementById("mark");
	elem.style.display = "none";
}

function setTab(tab) {
	switch (tab) {

		// Add new tab handlers here

		default: // just toggle content to show
				parent.edbody.document.we_form.pnt.value = "edbody";
				parent.edbody.document.we_form.tabnr.value = tab;
				parent.edbody.submitForm();
		break;
	}
	self.focus();
	' . $this->topFrame . '.activ_tab=tab;
}

' . ($this->Model->ID ? '' : $this->topFrame . '.activ_tab=1;'));

		/* $table = new we_html_table(array("width" => '100%', "class" => 'default'), 3, 1);

		  $table->setCol(1, 0, array("class" => "small",'style'=>'p'), we_html_element::htmlB(g_l('tools', ($this->Model->IsFolder ? '[group]' : '[entry]')) . ':&nbsp;' . str_replace('&amp;', '&', $this->Model->Text) . '<div id="mark" style="display: none;">*</div>')); */

		$extraJS = 'document.getElementById("tab_"+' . $this->topFrame . '.activ_tab).className="tabActive";';
		$body = we_html_element::htmlBody(array("id" => "eHeaderBody", "onload" => "setFrameSize()", "onresize" => "setFrameSize()"), '<div id="main" ><div id="headrow">&nbsp;' . we_html_element::htmlB(g_l('tools', ($this->Model->IsFolder ? '[group]' : '[entry]')) . ':&nbsp;' . str_replace('&amp;', '&', $this->Model->Text) . '<div id="mark" style="display: none;">*</div>') . '</div>' .
				$we_tabs->getHTML() .
				'</div>' . we_html_element::jsElement($extraJS)
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){

		$hiddens = array('cmd' => 'tool_' . $this->toolName . '_edit', 'pnt' => 'edbody', 'vernr' => we_base_request::_(we_base_request::INT, 'vernr', 0));

		if(we_base_request::_(we_base_request::BOOL, "home")){
			$hiddens['cmd'] = 'home';
			$GLOBALS['we_print_not_htmltop'] = true;
			$GLOBALS['we_head_insert'] = $this->View->getJSProperty();
			$GLOBALS['we_body_insert'] = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->getCommonHiddens($hiddens) . we_html_element::htmlHidden('home', 0));
			$tool = $GLOBALS['tool'] = $this->toolName;
			ob_start();
			include($this->toolDir . 'home.inc.php');
			$out = ob_get_clean();
			return
				/* we_html_element::jsElement('
				  ' . $this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&home=1";
				  ' . $this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter&home=1";
				  ') . */ $out;
		}

		$body = we_html_element::htmlBody(array("class" => "weEditorBody", 'onload' => 'loaded=1;'), we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js?' . WE_VERSION) .
				we_html_element::htmlForm(array('name' => 'we_form', 'onsubmit' => 'return false'), $this->getHTMLProperties()
				)
		);

		return $this->getHTMLDocument($body, $this->View->getJSProperty());
	}

	protected function getHTMLEditorFooter(){

		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#F0EFF0"), ""));
		}

		$_but_table = we_html_button::create_button_table(array(
				we_html_button::create_button(we_html_button::SAVE, "javascript:we_save();", true, 100, 22, '', '', (!permissionhandler::hasPerm('EDIT_NAVIGATION')))
				), array('style' => 'margin-left: 15px;margin-top:10px;')
		);

		return $this->getHTMLDocument(
				we_html_element::jsElement('
function we_save() {
	' . $this->topFrame . '.we_cmd("tool_' . $this->toolName . '_save");
}') .
				we_html_element::htmlBody(array("id" => "footerBody"), we_html_element::htmlForm(array(), $_but_table)
				)
		);
	}

	function getPercent($total, $value, $precision = 0){
		$result = ($total ? round(($value * 100) / $total, $precision) : 0);
		return we_base_util::formatNumber($result, strtolower($GLOBALS['WE_LANGUAGE']), 2);
	}

	function getHTMLPropertiesItem(){
		return array();
	}

	function getHTMLPropertiesGroup(){
		return array();
	}

	function getHTMLGeneral(){
		return array(array(
				'headline' => g_l('tools', '[general]'),
				'html' => we_html_element::htmlHidden('newone', ($this->Model->ID == 0 ? 1 : 0)) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Text', '', $this->Model->Text, '', 'style="width: ' . $this->_width_size . 'px;" onchange="' . $this->topFrame . '.mark();"'), g_l('tools', '[name]')) .
				$this->getHTMLChooser(g_l('tools', '[group]'), $this->Table, 0, 'ParentID', $this->Model->ParentID, 'ParentPath', 'opener.' . $this->topFrame . '.mark()', ''),
				'space' => $this->_space_size,
				'noline' => 1
			)
		);
	}

	function getHTMLProperties($preselect = ''){
		$tabNr = we_base_request::_(we_base_request::INT, 'tabnr', 1);

		$hiddens = array('cmd' => '',
			'pnt' => 'edbody',
			'tabnr' => $tabNr,
			'vernr' => we_base_request::_(we_base_request::INT, 'vernr', 0),
			'delayParam' => we_base_request::_(we_base_request::INT, 'delayParam', '')
		);

		return $this->View->getCommonHiddens($hiddens) .
			we_html_multiIconBox::getHTML('', $this->getHTMLGeneral(), 30);
	}

	protected function getHTMLTreeFooter(){
		return '<div id="infoField" class="defaultfont"></div>';
	}

	function getHTMLCmd(){
		$pid = we_base_request::_(we_base_request::STRING, "pid");
		if($pid === false){
			exit;
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);
		$_class = $this->toolClassName . 'TreeDataSource';

		$_loader = new $_class($this->TreeSource);

		$rootjs = (!$pid ?
				$this->Tree->topFrame . '.treeData.clear();' .
				$this->Tree->topFrame . '.treeData.add(' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));' : '');

		$hiddens = we_html_element::htmlHiddens(array(
				'pnt' => 'cmd',
				'cmd' => 'no_cmd'));

		return $this->getHTMLDocument(we_html_element::htmlBody(array(), we_html_element::htmlForm(array('name' => 'we_form'), $hiddens .
						we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree($_loader->getItems($pid, $offset, $this->Tree->default_segment, '')))
					)
		));
	}

	function formFileChooser($width = '', $IDName = 'ParentID', $IDValue = '/', $cmd = '', $filter = ''){
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value);");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, '', 'readonly', 'text', ($this->_width_size - 120), 0), "", "left", "defaultfont", "", permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	protected function getHTMLExitQuestion(){
		if(($dp = we_base_request::_(we_base_request::INT, 'delayParam'))){

			$_frame = 'opener.' . $this->topFrame;
//			$_form = $_frame . '.document.we_form';

			$_yes = $_frame . '.hot=0;' . $_frame . '.we_cmd("tool_' . $this->toolName . '_save");self.close();';
			$_no = $_frame . '.hot=0;' . $_frame . '.we_cmd("' . we_base_request::_(we_base_request::RAW, 'delayCmd') . '","' . $dp . '");self.close();';
			$_cancel = 'self.close();';

			return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET, '<body class="weEditorBody" onBlur="self.focus()" onload="self.focus()">' .
					we_html_tools::htmlYesNoCancelDialog(g_l('tools', '[exit_doc_question]'), IMAGE_DIR . "alert.gif", "ja", "nein", "abbrechen", $_yes, $_no, $_cancel) .
					'</body>');
		}
	}

	function getHTMLDocument($body, $head = ''){
		return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET . $head, $body);
	}

	private function getHTMLChooser($title, $table = FILE_TABLE, $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $PathName = 'Path', $cmd = '', $filter = we_base_ContentTypes::WEDOCUMENT, $disabled = false, $showtrash = false){
		$_path = id_to_path($this->Model->$IDName, $table);
		$_cmd = "javascript:we_cmd('open" . $this->toolName . "Dirselector',document.we_form.elements['" . $IDName . "'].value,'document.we_form." . $IDName . ".value','document.we_form." . $PathName . ".value','" . $cmd . "')";

		if($showtrash){
			$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 100, 22, '', '', $disabled) .
				we_html_button::create_button(we_html_button::TRASH, 'javascript:document.we_form.elements["' . $IDName . '"].value=0;document.we_form.elements["' . $PathName . '"].value="/";', true, 27, 22);
			$_width = 157;
		} else {
			$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 100, 22, '', '', $disabled);
			$_width = 120;
		}

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($PathName, 58, $_path, '', 'readonly', 'text', ($this->_width_size - $_width), 0), $title, 'left', 'defaultfont', we_html_element::htmlHidden($IDName, $IDValue), $_button);
	}

}
