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
class we_banner_frames extends we_modules_frame{

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->module = 'banner';
		$this->treeDefaultWidth = 224;

		$this->View = new we_banner_view($frameset);
		$this->Tree = new we_banner_tree($this->frameset, $this->topFrame, $this->treeFrame, $this->cmdFrame);
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case "edheader":
				return $this->getHTMLEditorHeader($mode);
			case "edfooter":
				return $this->getHTMLEditorFooter([
						we_html_button::SAVE => [['EDIT_BANNER'], 'save_banner']
						], we_html_element::jsScript(WE_JS_MODULES_DIR . 'banner/banner_footer.js'));
			case 'frameset':
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode());
			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return parent::getHTMLEditorHeader(0);
		}

		$isFolder = we_base_request::_(we_base_request::BOOL, "isFolder");
		$page = we_base_request::_(we_base_request::INT, "page", 0);
		$headline1 = g_l('modules_banner', $isFolder ? '[group]' : '[banner]');
		$text = we_base_request::_(we_base_request::STRING, "txt", g_l('modules_banner', ($isFolder ? '[newbannergroup]' : '[newbanner]')));

		$we_tabs = new we_tabs();

		$we_tabs->addTab(we_base_constants::WE_ICON_PROPERTIES, ($isFolder || $page == we_banner_banner::PAGE_PROPERTY), "setTab(" . we_banner_banner::PAGE_PROPERTY . ");", [
			'title' => g_l('tabs', '[module][properties]')]);
		if(!$isFolder){
			$we_tabs->addTab(g_l('tabs', '[module][placement]'), ($page == we_banner_banner::PAGE_PLACEMENT), "setTab(" . we_banner_banner::PAGE_PLACEMENT . ");");
			$we_tabs->addTab('<i class="fa fa-lg fa-pie-chart"></i>', ($page == we_banner_banner::PAGE_STATISTICS), "setTab(" . we_banner_banner::PAGE_STATISTICS . ");", [
				'title' => g_l('tabs', '[module][statistics]')]);
		}

		$extraHead = we_tabs::getHeader('
function setTab(tab){
	top.content.editor.edbody.we_cmd("switchPage",tab);
}');

		//TODO: we have the following body in several modules!
		$body = we_html_element::htmlBody(['onresize' => 'weTabs.setFrameSize()', 'onload' => 'weTabs.setFrameSize()', 'id' => 'eHeaderBody'], we_html_element::htmlDiv([
					'id' => 'main'], we_html_element::htmlDiv(['id' => 'headrow'], we_html_element::htmlNobr(
							we_html_element::htmlB(str_replace(" ", "&nbsp;", $headline1) . ':&nbsp;') .
							we_html_element::htmlSpan(['id' => 'h_path', 'class' => 'header_small'], '<b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b>'
							)
						)
					) .
					$we_tabs->getHTML()
				)
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}
		return $this->getHTMLDocument($this->View->getProperties());
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::RAW, "pid")) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);

		return $this->getHTMLDocument(
				we_html_element::htmlBody([], we_html_element::htmlForm(['name' => 'we_form'], we_html_element::htmlHiddens(["pnt" => "cmd",
							"cmd" => "no_cmd"])
					)
				), we_base_jsCmd::singleCmd('loadTree', ['pid' => intval($pid), 'items' => we_banner_tree::getItems($pid, $offset, $this->Tree->default_segment)])
		);
	}

	function getHTMLDCheck(){
		return $this->getHTMLDocument(we_html_element::htmlBody(['onload' => 'self.focus();'], $this->View->getHTMLDCheck()));
	}

}
