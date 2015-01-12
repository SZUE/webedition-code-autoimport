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
	public $module = "workflow";
	protected $useMainTree = false;

	function __construct(){
		parent::__construct(WE_WORKFLOW_MODULE_DIR . "edit_workflow_frameset.php");
		$this->View = new we_workflow_view();
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
			default:
				return parent::getHTML($what);
		}
	}

	function getHTMLFrameset(){
		$extraHead = $this->getJSTreeCode() . $this->getJSCmdCode();
		return parent::getHTMLFrameset($extraHead);
	}

	protected function getDoClick(){
		return "function doClick(id,ct,table){
	if(ct=='folder'){
		top.content.we_cmd('workflow_edit',id,ct,table);
	}else if(ct=='file'){
		top.content.we_cmd('show_document',id,ct,table);
	}
}";
	}

	function getJSTreeCode(){
		$out = '
		function loadData(){
			menuDaten.clear();';

		$out.="startloc=0;";
		$this->db->query('SELECT ID FROM ' . WORKFLOW_TABLE . ' ORDER BY Text ASC');
		$ids = $this->db->getAll(true);
		foreach($ids as $id){
			$this->View->workflowDef = new we_workflow_workflow($id);
			$out.="  menuDaten.add(new dirEntry('folder','" . $this->View->workflowDef->ID . "','0','" . oldHtmlspecialchars(addslashes($this->View->workflowDef->Text)) . "',false,'folder','workflowDef','" . $this->View->workflowDef->Status . "'));";

			foreach($this->View->workflowDef->documents as $v){
				$out.="  menuDaten.add(new urlEntry('" . $v["Icon"] . "','" . $v["ID"] . "','" . $this->View->workflowDef->ID . "','" . oldHtmlspecialchars(addslashes($v["Text"])) . "','file','" . FILE_TABLE . "',1));";
			}
		}

		$out.='}';
		echo
		we_html_element::jsScript(JS_DIR . 'images.js') .
		we_html_element::jsScript(JS_DIR . 'windows.js') .
		we_html_element::jsScript(JS_DIR . 'tree.js') .
		we_html_element::cssLink(CSS_DIR . 'tree.css') .
		// TODO: move shared code for (some of the) modules-tree (not based on weTree!!) to new weModulesTree.class
		we_html_element::jsElement('
var table="' . USER_TABLE . '";
var tree_icon_dir="' . TREE_ICON_DIR . '";
var tree_img_dir="' . TREE_IMAGE_DIR . '";
var we_dir="' . WEBEDITION_DIR . '";'
				 . parent::getTree_g_l()
				) .
		we_html_element::jsScript(JS_DIR . 'workflow_tree.js') .
		we_html_element::jsElement($out);
	}

	function getJSCmdCode(){
		echo $this->View->getJSTopCode();
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#F0EFF0"), ""));
		}

		$page = we_base_request::_(we_base_request::INT, "page", 0);
		$text = we_base_request::_(we_base_request::RAW, 'txt', g_l('modules_workflow', '[new_workflow]'));

		$we_tabs = new we_tabs();

		if($mode == 0){
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][properties]'), we_tab::NORMAL, "setTab(0);", array("id" => "tab_0")));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][overview]'), we_tab::NORMAL, "setTab(1);", array("id" => "tab_1")));
		} else {
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[editor][information]'), we_tab::ACTIVE, "//", array("id" => "tab_0")));
		}

		$tab_header = $we_tabs->getHeader();
		$textPre = g_l('modules_workflow', ($mode == 1 ? '[document]' : '[workflow]'));
		$textPost = '/' . $text;

		$extraHead = we_html_element::jsElement('
function setTab(tab){
	switch(tab){
		case 0:
			top.content.editor.edbody.we_cmd("switchPage",0);
			break;
		case 1:
			top.content.editor.edbody.we_cmd("switchPage",1);
			break;
	}
}

top.content.hloaded=1;') .
			$tab_header;

		$mainDiv = we_html_element::htmlDiv(array('id' => 'main'), we_html_tools::getPixel(100, 3) .
				we_html_element::htmlDiv(array('style' => 'margin:0px;padding-left:10px;', 'id' => 'headrow'), we_html_element::htmlNobr(
						we_html_element::htmlB(oldHtmlspecialchars($textPre) . ':&nbsp;') .
						we_html_element::htmlSpan(array('id' => 'h_path', 'class' => 'header_small'), '<b id="titlePath">' . oldHtmlspecialchars($textPost) . '</b>')
				)) .
				we_html_tools::getPixel(100, 3) .
				$we_tabs->getHTML()
		);

		$body = we_html_element::htmlBody(array(
				'onresize' => 'setFrameSize()',
				'onload' => 'setFrameSize()',
				'bgcolor' => 'white',
				'background' => IMAGE_DIR . 'backgrounds/header_with_black_line.gif',
				'marginwidth' => 0,
				'marginheight' => 0,
				'leftmargin' => 0,
				'topmargin' => 0,
				), $mainDiv .
				we_html_element::jsElement('document.getElementById("tab_' . $page . '").className="tabActive";')
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorFooter($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#EFF0EF"), ""));
		}

		$extraHead = we_html_element::jsElement('
function setStatusCheck(){
	var a=document.we_form.status_workflow;
	var b;
	if(top.content.editor.edbody.loaded) b=top.content.editor.edbody.getStatusContol();
	else setTimeout("setStatusCheck()",100);

	if(b==1) a.checked=true;
	else a.checked=false;
}
function we_save() {
	top.content.we_cmd("save_workflow");
}');

		$table1 = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "width" => 300), 1, 1);
		$table1->setCol(0, 0, array("nowrap" => null, "valign" => "top"), we_html_tools::getPixel(1, 10));

		$table2 = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0, 'width' => 300), 1, 3);
		//$table2->setRow(0, array('valign' => 'middle'));
		$table2->setCol(0, 0, array('nowrap' => null), we_html_tools::getPixel(15, 5));
		$table2->setCol(0, 1, array('nowrap' => null), we_html_button::create_button('save', 'javascript:we_save()'));
		$table2->setCol(0, 2, array('nowrap' => null, 'class' => 'defaultfont'), $this->View->getStatusHTML());

		$body = we_html_element::htmlBody(array(
				'bgcolor' => 'white',
				'background' => IMAGE_DIR . 'edit/editfooterback.gif',
				'style' => 'margin: 0px 0px 0px 0px;',
				'onload' => ($mode == 0 ? 'setStatusCheck()' : '')
				), we_html_element::htmlForm($attribs = array(), $table1->getHtml() . $table2->getHtml())
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLLog($docID, $type = 0){
		$extraHead = we_html_element::jsElement('self.focus();');
		$body = we_html_element::htmlBody(array('class' => 'weDialogBody'), we_workflow_view::getLogForDocument($docID, $type));

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLCmd(){
		$form = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->htmlHidden("wcmd", "") . $this->View->htmlHidden("wopt", ""));
		$body = we_html_element::htmlBody(array(), $form);

		return $this->getHTMLDocument($body, $this->View->getCmdJS());
	}

	function getHTMLLogQuestion(){
		$form = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->getLogQuestion());
		$body = we_html_element::htmlBody(array(), $form);

		return $this->getHTMLDocument($body);
	}

}
