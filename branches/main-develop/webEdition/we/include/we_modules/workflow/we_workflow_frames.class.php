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
		$this->Tree = new we_workflow_tree($this->jsCmd);
		$this->View = new we_workflow_view();
	}

	function getHTML($what = '', $mode = 0, $type = 0){
		switch($what){
			case "edheader":
				return $this->getHTMLEditorHeader($mode);
			case "edfooter":
				return $this->getHTMLEditorFooter([$mode]);
			case "qlog":
				return $this->getHTMLLogQuestion();
			case "log":
				return $this->getHTMLLog($mode, $type);
			case 'edit':
				return $this->getHTMLEditorBody();
			case 'frameset':
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode() . $this->View->getJSTop());
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
			$we_tabs->addTab('', we_base_constants::WE_ICON_PROPERTIES, false, self::TAB_PROPERTIES, ['id' => 'tab_' . self::TAB_PROPERTIES, 'title' => g_l('tabs', '[module][properties]')]);
			$we_tabs->addTab('', we_base_constants::WE_ICON_CONTENT, false, self::TAB_OVERVIEW, ['id' => 'tab_' . self::TAB_OVERVIEW, 'title' => g_l('tabs', '[module][overview]')]);
		} else {
			$we_tabs->addTab('', we_base_constants::WE_ICON_INFO, true, self::TAB_PROPERTIES, ['id' => "tab_" . self::TAB_PROPERTIES, 'title' => g_l('tabs', '[editor][information]')]);
		}

		$textPre = g_l('modules_workflow', ($mode == 1 ? '[document]' : '[workflow]'));
		$textPost = '/' . $text;

		$extraHead = we_html_element::cssLink(CSS_DIR . 'we_tab.css') .
			we_html_element::jsScript(JS_DIR . 'initTabs.js') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'workflow/workflow_top.js');

		$body = we_html_element::htmlBody(['onresize' => 'weTabs.setFrameSize()',
				'onload' => "weTabs.setFrameSize();document.getElementById('tab_" . $page . "').className='tabActive';",
				'id' => 'eHeaderBody',
				], we_html_element::htmlDiv(
					['id' => 'main'], we_html_element::htmlDiv(
						['id' => 'headrow'], we_html_element::htmlNobr(
							we_html_element::htmlB(oldHtmlspecialchars($textPre) . ':&nbsp;') .
							we_html_element::htmlSpan(['id' => 'h_path', 'class' => 'header_small'], '<b id="titlePath">' . oldHtmlspecialchars($textPost) . '</b>')
					)) .
					$we_tabs->getHTML()
				)
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorFooter(array $mode = [0], $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return parent::getHTMLEditorFooter([]);
		}

		$table2 = new we_html_table(['class' => 'default', 'width' => 300], 1, 2);
		$table2->setColContent(0, 0, we_html_button::create_button(we_html_button::SAVE, "javascript:top.content.we_cmd('save_workflow');"));
		$table2->setCol(0, 1, ['class' => 'defaultfont'], $this->View->getStatusHTML());

		$body = we_html_element::htmlBody([
				'id' => 'footerBody',
				'onload' => ($mode == 0 ? 'setStatusCheck()' : '')
				], we_html_element::htmlForm($attribs = [], $table2->getHtml())
		);

		return $this->getHTMLDocument($body, we_html_element::jsScript(WE_JS_MODULES_DIR . 'workflow/workflow_frames.js'));
	}

	private function getHTMLLog($docID, $type = 0){
		return $this->getHTMLDocument(
				we_html_element::htmlBody(['class' => 'weDialogBody', 'onload' => 'self.focus();'], we_workflow_view::getLogForDocument($docID, $type))
		);
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::RAW, "pid")) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody(), $this->jsCmd->getCmds() . (empty($GLOBALS['extraJS']) ? '' : $GLOBALS['extraJS']));
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);
		$this->jsCmd->addCmd('loadTree', ['clear' => !$pid, 'items' => we_workflow_tree::getItems($pid, $offset, $this->Tree->default_segment)]);

		return $this->getHTMLDocument(
				we_html_element::htmlBody([], we_html_element::htmlForm(['name' => 'we_form'], we_html_element::htmlHiddens([
							'wcmd' => '',
							'wopt' => ''])
					)
				), we_html_element::jsScript(WE_JS_MODULES_DIR . 'workflow/workflow_frames.js')
		);
	}

	private function getHTMLLogQuestion(){
		$form = we_html_element::htmlForm(['name' => 'we_form'], $this->View->getLogQuestion());
		$body = we_html_element::htmlBody(['onload' => 'self.focus();'], $form);

		return $this->getHTMLDocument($body, we_html_element::jsScript(WE_JS_MODULES_DIR . 'workflow/workflow_frames.js'));
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}

		return $this->getHTMLDocument($this->View->getProperties($this->jsCmd), $this->View->getJSProperty());
	}

}
