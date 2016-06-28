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
	var $Model;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->showTreeFooter = false;
		$this->treeWidth = 200;
	}

	protected function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		$class = we_tool_lookup::getModelClassName($this->toolName);
		$this->Model = $this->Model ? : new $class();
		//$this->Model->clearSessionVars(); // why should we clear here?

		if(($modelid = we_base_request::_(we_base_request::INT, 'modelid'))){
			$this->Model = new $class();
			$this->Model->load($modelid);
			$this->Model->saveInSession();
			$_SESSION['weS'][$this->toolName]["modelidForTree"] = $modelid;
		}

		return parent::getHTMLFrameset($this->Tree->getJSTreeCode() . $extraHead, ($modelid ? '&modelid=' . $modelid : '') . $extraUrlParams);
	}

	/**
	 * Frame for tubs
	 *
	 * @return string
	 */
	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return parent::getHTMLEditorHeader(0);
		}

		$we_tabs = new we_tabs();
		$we_tabs->addTab(new we_tab(g_l('tools', '[properties]'), false, "setTab(1);", array("id" => "tab_1")));

		$tabsHead = we_tabs::getHeader('
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
	top.content.activ_tab=tab;
}

' . ($this->Model->ID ? '' : 'top.content.activ_tab=1;'));

		/* $table = new we_html_table(array("width" => '100%', "class" => 'default'), 3, 1);

		  $table->setCol(1, 0, array("class" => "small",'style'=>'p'), we_html_element::htmlB(g_l('tools', ($this->Model->IsFolder ? '[group]' : '[entry]')) . ':&nbsp;' . str_replace('&amp;', '&', $this->Model->Text) . '<div id="mark" style="display: none;">*</div>')); */

		$extraJS = 'document.getElementById("tab_"+top.content.activ_tab).className="tabActive";';
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
			$GLOBALS['we_head_insert'] = $this->View->getJSProperty();
			$GLOBALS['we_body_insert'] = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->getCommonHiddens($hiddens) . we_html_element::htmlHidden('home', 0));
			$tool = $GLOBALS['tool'] = $this->toolName;
			ob_start();
			include($this->toolDir . 'home.inc.php');
			return ob_get_clean();
		}

		$body = we_html_element::htmlBody(array('class' => 'weEditorBody', 'onload' => 'loaded=1;'), we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js?' . WE_VERSION) .
				we_html_element::htmlForm(array('name' => 'we_form', 'onsubmit' => 'return false'), $this->getHTMLProperties()
				)
		);

		return $this->getHTMLDocument($body, $this->View->getJSProperty());
	}

	protected function getHTMLEditorFooter($btn_cmd = '', $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array('class' => 'home'), ''), we_html_element::cssLink(CSS_DIR . 'tools_home.css'));
		}

		$but_table = we_html_element::htmlSpan(array('style' => 'margin-left: 15px;margin-top:10px;'), we_html_button::create_button(we_html_button::SAVE, "javascript:we_save();", true, 100, 22, '', ''));

		return $this->getHTMLDocument(we_html_element::jsElement('
function we_save() {
	top.content.we_cmd("tool_' . $this->toolName . '_save");
}') .
				we_html_element::htmlBody(array("id" => "footerBody"), we_html_element::htmlForm(array(), $but_table)
				)
		);
	}

	function getHTMLGeneral(){
		return array(array(
				'headline' => g_l('tools', '[general]'),
				'html' => we_html_element::htmlHidden('newone', ($this->Model->ID == 0 ? 1 : 0)) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Text', '', $this->Model->Text, '', 'style="width: 520px;" onchange="top.content.mark();"'), g_l('tools', '[name]')) .
				$this->getHTMLChooser(g_l('tools', '[group]'), $this->Table, 0, 'ParentID', $this->Model->ParentID, 'ParentPath', 'opener.top.content.mark()', ''),
				'space' => we_html_multiIconBox::SPACE_MED,
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

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::STRING, "pid")) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);
		$class = $this->toolClassName . 'TreeDataSource';

		$loader = new $class($this->TreeSource);

		return $this->getHTMLDocument(we_html_element::htmlBody(array(), we_html_element::htmlForm(array('name' => 'we_form'), we_html_element::htmlHiddens(array(
							'pnt' => 'cmd',
							'cmd' => 'no_cmd'))
					)
				), we_html_element::jsElement(
					($pid ?
						'' :
						'top.content.treeData.clear();
top.content.treeData.add(top.content.node.prototype.rootEntry(\'' . $pid . '\',\'root\',\'root\'));'
					) . $this->Tree->getJSLoadTree(!$pid, $loader->getItems($pid, $offset, $this->Tree->default_segment, ''))));
	}

	protected function getHTMLExitQuestion(){
		if(($dp = we_base_request::_(we_base_request::INT, 'delayParam'))){
			$yes = 'opener.top.content.hot=0;opener.top.content.we_cmd("tool_' . $this->toolName . '_save");self.close();';
			$no = 'opener.top.content.hot=0;opener.top.content.we_cmd("' . we_base_request::_(we_base_request::RAW, 'delayCmd') . '","' . $dp . '");self.close();';
			$cancel = 'self.close();';

			return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET, '<body class="weEditorBody" onBlur="self.focus()" onload="self.focus()">' .
					we_html_tools::htmlYesNoCancelDialog(g_l('tools', '[exit_doc_question]'), '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', "ja", "nein", "abbrechen", $yes, $no, $cancel) .
					'</body>');
		}
	}

	function getHTMLDocument($body, $head = ''){
		return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET . (empty($GLOBALS['extraJS']) ? '' : $GLOBALS['extraJS']) . $head, $body);
	}

	private function getHTMLChooser($title, $table = FILE_TABLE, $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $PathName = 'Path', $cmd = '', $filter = we_base_ContentTypes::WEDOCUMENT, $disabled = false, $showtrash = false){
		$path = id_to_path($this->Model->$IDName, $table);
		$cmd = "javascript:we_cmd('open" . $this->toolName . "Dirselector',document.we_form.elements['" . $IDName . "'].value,'document.we_form." . $IDName . ".value','document.we_form." . $PathName . ".value','" . $cmd . "')";

		if($showtrash){
			$button = we_html_button::create_button(we_html_button::SELECT, $cmd, true, 100, 22, '', '', $disabled) .
				we_html_button::create_button(we_html_button::TRASH, 'javascript:document.we_form.elements["' . $IDName . '"].value=0;document.we_form.elements["' . $PathName . '"].value="/";', true, 27, 22);
			$width = 157;
		} else {
			$button = we_html_button::create_button(we_html_button::SELECT, $cmd, true, 100, 22, '', '', $disabled);
			$width = 120;
		}

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($PathName, 58, $path, '', 'readonly', 'text', (520 - $width), 0), $title, 'left', 'defaultfont', we_html_element::htmlHidden($IDName, $IDValue), $button);
	}

}
