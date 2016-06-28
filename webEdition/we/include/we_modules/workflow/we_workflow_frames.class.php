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
class we_workflow_frames extends we_modules_frame{
	const TAB_PROPERTIES = 0;
	const TAB_OVERVIEW = 1;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->module = "workflow";
		$this->Tree = new we_workflow_tree($frameset, "top.content", "top.content", "top.content.cmd");
		$this->View = new we_workflow_view($frameset);
	}

	function getHTML($what = '', $mode = 0, $type = 0){
		switch($what){
			case "edheader":
				return $this->getHTMLEditorHeader($mode);
			case "edfooter":
				return $this->getHTMLEditorFooter($mode);
			case "qlog":
				return $this->getHTMLLogQuestion();
			case "log":
				return $this->getHTMLLog($mode, $type);
			case 'edit':
				return $this->getHTMLEditorBody();
			case 'frameset':
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode() . $this->getJSCmdCode());
			default:
				return parent::getHTML($what, $mode, $type);
		}
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return parent::getHTMLEditorHeader(0);
		}

		$page = we_base_request::_(we_base_request::INT, 'page', 0);
		$text = we_base_request::_(we_base_request::RAW, 'txt', g_l('modules_workflow', '[new_workflow]'));

		$we_tabs = new we_tabs();

		if($mode == 0){
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][properties]'), false, "setTab(" . self::TAB_PROPERTIES . ");", array("id" => "tab_0")));
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][overview]'), false, "setTab(" . self::TAB_OVERVIEW . ");", array("id" => "tab_1")));
		} else {
			$we_tabs->addTab(new we_tab(g_l('tabs', '[editor][information]'), true, "//", array("id" => "tab_0")));
		}

		$textPre = g_l('modules_workflow', ($mode == 1 ? '[document]' : '[workflow]'));
		$textPost = '/' . $text;

		$extraHead = we_tabs::getHeader('
function setTab(tab){
	switch(tab){
		case ' . self::TAB_PROPERTIES . ':
			top.content.editor.edbody.we_cmd("switchPage",' . self::TAB_PROPERTIES . ');
			break;
		case ' . self::TAB_OVERVIEW . ':
			top.content.editor.edbody.we_cmd("switchPage",' . self::TAB_OVERVIEW . ');
			break;
	}
}');

		$mainDiv = we_html_element::htmlDiv(array('id' => 'main'), we_html_element::htmlDiv(array('id' => 'headrow'), we_html_element::htmlNobr(
						we_html_element::htmlB(oldHtmlspecialchars($textPre) . ':&nbsp;') .
						we_html_element::htmlSpan(array('id' => 'h_path', 'class' => 'header_small'), '<b id="titlePath">' . oldHtmlspecialchars($textPost) . '</b>')
				)) .
				$we_tabs->getHTML()
		);

		$body = we_html_element::htmlBody(array(
				'onresize' => 'weTabs.setFrameSize()',
				'onload' => 'weTabs.setFrameSize()',
				'id' => 'eHeaderBody',
				), $mainDiv .
				we_html_element::jsElement('document.getElementById("tab_' . $page . '").className="tabActive";')
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorFooter($mode = 0, $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return parent::getHTMLEditorFooter('');
		}

		$extraHead = we_html_element::jsElement('
function setStatusCheck(){
	var a=document.we_form.status_workflow;
	var b;
	if(top.content.editor.edbody.loaded){
		b=top.content.editor.edbody.getStatusContol();
	}else{
		setTimeout(setStatusCheck,100);
	}

	if(b==1) a.checked=true;
	else a.checked=false;
}
function we_save() {
	top.content.we_cmd("save_workflow");
}');

		$table2 = new we_html_table(array('class' => 'default', 'width' => 300), 1, 2);
		$table2->setCol(0, 0, [], we_html_button::create_button(we_html_button::SAVE, 'javascript:we_save()'));
		$table2->setCol(0, 1, array('class' => 'defaultfont'), $this->View->getStatusHTML());

		$body = we_html_element::htmlBody(array(
				'id' => 'footerBody',
				'onload' => ($mode == 0 ? 'setStatusCheck()' : '')
				), we_html_element::htmlForm($attribs = [], $table2->getHtml())
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLLog($docID, $type = 0){
		return $this->getHTMLDocument(
				we_html_element::htmlBody(array('class' => 'weDialogBody', 'onload' => 'self.focus();'), we_workflow_view::getLogForDocument($docID, $type))
		);
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::RAW, "pid")) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);

		return $this->getHTMLDocument(
				we_html_element::htmlBody([], we_html_element::htmlForm(array("name" => "we_form"), we_html_element::htmlHiddens(array(
							'wcmd' => '',
							'wopt' => ''))
					)
				), we_html_element::jsElement('
function submitForm(){
	var f = self.document.we_form;
	f.target = "cmd";
	f.method = "post";
	f.submit();
}' .
					($pid ?
						'' :
						'top.content.treeData.clear();
top.content.treeData.add(top.content.node.prototype.rootEntry(\'' . $pid . '\',\'root\',\'root\'));') .
					$this->Tree->getJSLoadTree(!$pid, we_workflow_tree::getItems($pid, $offset, $this->Tree->default_segment))
				)
		);
	}

	function getHTMLLogQuestion(){
		$form = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->getLogQuestion());
		$body = we_html_element::htmlBody([], $form);

		return $this->getHTMLDocument($body);
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}
		return $this->getHTMLDocument($this->View->getProperties(), STYLESHEET . $this->View->getJSProperty());
	}

}
