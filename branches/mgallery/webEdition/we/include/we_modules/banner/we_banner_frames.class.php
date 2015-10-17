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

	var $edit_cmd = "banner_edit";
	protected $treeDefaultWidth = 224;

	function __construct($frameset){
		parent::__construct($frameset);
		$this->View = new we_banner_view();
		$this->Tree = new we_banner_tree($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->module = 'banner';
	}

	function getHTML($what = '', $mode = ''){
		switch($what){
			case "edheader":
				return $this->getHTMLEditorHeader($mode);
			case "edfooter":
				return $this->getHTMLEditorFooter($mode);
			default:
				return parent::getHTML($what);
		}
	}

	function getHTMLFrameset(){
		return parent::getHTMLFrameset($this->Tree->getJSTreeCode());
	}

	function getJSCmdCode(){
		return $this->View->getJSTopCode();
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => '#F0EFF0'), ''));
		}

		$isFolder = we_base_request::_(we_base_request::BOOL, "isFolder");

		$page = we_base_request::_(we_base_request::INT, "page", 0);

		$headline1 = g_l('modules_banner', $isFolder ? '[group]' : '[banner]');
		$text = we_base_request::_(we_base_request::STRING, "txt", g_l('modules_banner', ($isFolder ? '[newbannergroup]' : '[newbanner]')));

		$we_tabs = new we_tabs();

		if($isFolder){
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][properties]'), we_tab::ACTIVE, "setTab(0);"));
		} else {
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][properties]'), ($page == 0 ? we_tab::ACTIVE : we_tab::NORMAL), "setTab(0);"));
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][placement]'), ($page == 1 ? we_tab::ACTIVE : we_tab::NORMAL), "setTab(1);"));
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][statistics]'), ($page == 2 ? we_tab::ACTIVE : we_tab::NORMAL), "setTab(2);"));
		}

		$tab_head = we_tabs::getHeader();

		$extraHead = $tab_head .
				we_html_element::jsElement('
function setTab(tab){
	switch(tab){
		case ' . we_banner_banner::PAGE_PROPERTY . ':
		case ' . we_banner_banner::PAGE_PLACEMENT . ':
		case ' . we_banner_banner::PAGE_STATISTICS . ':
			top.content.editor.edbody.we_cmd("switchPage",tab);
			break;
	}
}');

		//TODO: we have the following body in several modules!
		$body = we_html_element::htmlBody(array('onresize' => 'weTabs.setFrameSize()', 'onload' => 'weTabs.setFrameSize()', 'id' => 'eHeaderBody'), we_html_element::htmlDiv(array('id' => 'main'), we_html_element::htmlDiv(array('id' => 'headrow'), we_html_element::htmlNobr(
												we_html_element::htmlB(str_replace(" ", "&nbsp;", $headline1) . ':&nbsp;') .
												we_html_element::htmlSpan(array('id' => 'h_path', 'class' => 'header_small'), '<b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b>'
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
		return $this->getHTMLDocument('<body></body>');
	}

	protected function getHTMLEditorFooter($mode = 0){
		return parent::getHTMLEditorFooter('save_banner', we_html_element::jsScript(WE_JS_BANNER_MODULE_DIR . 'banner_footer.js'));
	}

	function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::RAW, "pid")) === false){
			exit;
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);

		$rootjs = "";
		if(!$pid){
			$rootjs.=
					$this->Tree->topFrame . '.treeData.clear();' .
					$this->Tree->topFrame . '.treeData.add(' . $this->Tree->topFrame . '.node.prototype.rootEntry(\'' . $pid . '\',\'root\',\'root\'));';
		}
		$hiddens = we_html_element::htmlHiddens(array(
					"pnt" => "cmd",
					"cmd" => "no_cmd"));

		return $this->getHTMLDocument(
						we_html_element::htmlBody(array(), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
										we_html_element::jsElement($rootjs .
												$this->Tree->getJSLoadTree(!$pid, we_banner_tree::getItems($pid, $offset, $this->Tree->default_segment))
										)
								)
						)
		);
	}

	function getHTMLDCheck(){
		return $this->getHTMLDocument(we_html_element::htmlBody(array('onload' => 'self.focus();'), $this->View->getHTMLDCheck()));
	}



}
