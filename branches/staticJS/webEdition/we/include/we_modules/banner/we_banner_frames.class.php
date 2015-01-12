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
	protected $useMainTree = false;
	protected $treeDefaultWidth = 224;

	function __construct($frameset){
		parent::__construct($frameset);
		$this->View = new we_banner_view();
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
		$extraHead = $this->getJSTreeCode();
		return parent::getHTMLFrameset($extraHead);
	}

	protected function getHTMLEditor(){
		return parent::getHTMLEditor('&home=1');
	}

	protected function getDoClick(){
		return "function doClick(id,ct,table){
	top.content.we_cmd('" . $this->edit_cmd . "',id,ct,table);
}";
	}

	function getJSTreeCode(){//TODO: move (as in all modules...) to some future moduleTree class
		//start of code from ex class weModuleBannerFrames
		$startloc = 0;

		$out = '
		function loadData(){
			menuDaten.clear();
			startloc=' . $startloc . ';';

		$this->db->query('SELECT ID,ParentID,Path,Text,Icon,IsFolder,ABS(text) as Nr, (text REGEXP "^[0-9]") as isNr FROM ' . BANNER_TABLE . ' ORDER BY isNr DESC,Nr,Text');
		while($this->db->next_record()){
			$ID = $this->db->f("ID");
			$ParentID = $this->db->f("ParentID");
			$Path = $this->db->f("Path");
			$Text = addslashes($this->db->f("Text"));
			$Icon = $this->db->f("Icon");
			$IsFolder = $this->db->f("IsFolder");

			$out.=($IsFolder ?
							"  menuDaten.add(new dirEntry('" . $Icon . "'," . $ID . "," . $ParentID . ",'" . $Text . "',0,'folder','" . BANNER_TABLE . "',1));" :
							"  menuDaten.add(new urlEntry('" . $Icon . "'," . $ID . "," . $ParentID . ",'" . $Text . "','file','" . BANNER_TABLE . "',1));");
		}

		$out.='}';
		echo we_html_element::cssLink(CSS_DIR . 'tree.css') .
		we_html_element::jsScript(JS_DIR . 'images.js') .
		we_html_element::jsScript(JS_DIR . 'tree.js') .
		we_html_element::jsScript(JS_DIR . 'windows.js') .
		we_html_element::jsElement('
var table="' . BANNER_TABLE . '";
var tree_icon_dir="' . TREE_ICON_DIR . '";
var tree_img_dir="' . TREE_IMAGE_DIR . '";
var we_dir="' . WEBEDITION_DIR . '";'
				. parent::getTree_g_l()
		) .
		we_html_element::jsScript(JS_DIR . 'banner_tree.js') .
		we_html_element::jsElement($out);
	}

	function getJSCmdCode(){
		echo $this->View->getJSTopCode();
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
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][properties]'), we_tab::ACTIVE, "setTab(0);"));
		} else {
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][properties]'), ($page == 0 ? we_tab::ACTIVE : we_tab::NORMAL), "setTab(0);"));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][placement]'), ($page == 1 ? we_tab::ACTIVE : we_tab::NORMAL), "setTab(1);"));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][statistics]'), ($page == 2 ? we_tab::ACTIVE : we_tab::NORMAL), "setTab(2);"));
		}

		$tab_head = $we_tabs->getHeader();

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
				}
				top.content.hloaded=1;
			');

		//TODO: we have the following body in several modules!
		$body = we_html_element::htmlBody(array('onresize' => 'setFrameSize()', 'onload' => 'setFrameSize()', 'bgcolor' => 'white', 'background' => IMAGE_DIR . 'backgrounds/header_with_black_line.gif'), we_html_element::htmlDiv(array('id' => 'main'), we_html_tools::getPixel(100, 3) .
								we_html_element::htmlDiv(array('style' => 'margin:0px;padding-left:10px;', 'id' => 'headrow'), we_html_element::htmlNobr(
												we_html_element::htmlB(str_replace(" ", "&nbsp;", $headline1) . ':&nbsp;') .
												we_html_element::htmlSpan(array('id' => 'h_path', 'class' => 'header_small'), '<b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b>'
												)
										)
								) .
								we_html_tools::getPixel(100, 3) .
								$we_tabs->getHTML()
						)
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorFooter($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => '#F0EFF0'), ''));
		}

		echo we_html_tools::getHtmlTop() .
		STYLESHEET;

		$this->View->getJSFooterCode();

		$extraHead = $this->View->getJSFooterCode() . we_html_element::jsElement('
			function sprintf(){
				if (!arguments || arguments.length < 1) return;

				var argum = arguments[0];
				var regex = /([^%]*)%(%|d|s)(.*)/;
				var arr = new Array();
				var iterator = 0;
				var matches = 0;

				while (arr=regex.exec(argum)){
					var left = arr[1];
					var type = arr[2];
					var right = arr[3];

					matches++;
					iterator++;

					var replace = arguments[iterator];

					if (type=="d") replace = parseInt(param) ? parseInt(param) : 0;
					else if (type=="s") replace = arguments[iterator];
					argum = left + replace + right;
				}
				return argum;
			}

			function we_save() {
				var acLoopCount=0;
				var acIsRunning = false;
				if(!!top.content.editor.edbody.YAHOO && !!top.content.editor.edbody.YAHOO.autocoml){
					while(acLoopCount<20 && top.content.editor.edbody.YAHOO.autocoml.isRunnigProcess()){
						acLoopCount++;
						acIsRunning = true;
						setTimeout("we_save()",100);
					}
					if(!acIsRunning) {
						if(top.content.editor.edbody.YAHOO.autocoml.isValid()) {
							_we_save();
						} else {
							' . we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
					}
				} else {
					_we_save();
				}
			}
		');

		return parent::getHTMLEditorFooter('save_banner', $extraHead);
	}

	function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody(array(), we_html_element::htmlForm(array(), $this->View->htmlHidden("ncmd", "") .
										$this->View->htmlHidden("nopt", "")
								)
						), $this->View->getJSCmd());
	}

	function getHTMLDCheck(){
		return $this->getHTMLDocument(we_html_element::htmlBody(array(), $this->View->getHTMLDCheck()), we_html_element::jsElement('self.focus();'));
	}

}
