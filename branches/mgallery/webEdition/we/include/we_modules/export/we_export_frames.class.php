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
//TEST: was it ok to abandon treefooter?
we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT);

class we_export_frames extends we_modules_frame{
	var $SelectionTree;
	var $editorBodyFrame;
	var $_space_size = 130;
	var $_width_size = 535;
	protected $treeDefaultWidth = 220;
	public $module = "export";

	function __construct(){
		parent::__construct(WE_EXPORT_MODULE_DIR . "edit_export_frameset.php");
		$this->Tree = new we_export_treeMain($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->SelectionTree = new we_export_tree($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->View = new we_export_view(WE_EXPORT_MODULE_DIR . "edit_export_frameset.php", "top.content");
		$this->setFrames("top.content", "top.content", "top.content.cmd");
		$this->editorBodyFrame = $this->topFrame . '.editor.edbody';
	}

	public function getHTMLDocumentHeader($what = '', $mode = ''){
		if($what != "cmd" && $what != "load" && !we_base_request::_(we_base_request::FILE, "exportfile")){
			return parent::getHTMLDocumentHeader();
		}
	}

	function getHTML($what){
		switch($what){
			case "load":
				return $this->getHTMLCmd();
			case "treeheader":
				return '';
			case "treefooter":
				return '';
			default:
				return parent::getHTML($what);
		}
	}

	function getHTMLFrameset(){
		$this->View->export->clearSessionVars();
		$extraHead = $this->Tree->getJSTreeCode();

		return parent::getHTMLFrameset($extraHead);
	}

	function getJSCmdCode(){
		return $this->View->getJSTop();
	}

	protected function getHTMLEditorHeader(){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#F0EFF0"), ""));
		}

		$we_tabs = new we_tabs();
		$we_tabs->addTab(new we_tab(g_l('export', '[property]'), '((' . $this->topFrame . '.activ_tab==1) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('1');", array("id" => "tab_1")));
		if($this->View->export->IsFolder == 0){
			$we_tabs->addTab(new we_tab(g_l('export', '[options]'), '((' . $this->topFrame . '.activ_tab==2) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('2');", array("id" => "tab_2")));
			$we_tabs->addTab(new we_tab(g_l('export', '[log]'), '((' . $this->topFrame . '.activ_tab==3) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('3');", array("id" => "tab_3")));
		}

		$tabsHead = $we_tabs->getHeader() .
			we_html_element::jsElement('
function setTab(tab) {
	parent.edbody.toggle("tab"+' . $this->topFrame . '.activ_tab);
	parent.edbody.toggle("tab"+tab);
	' . $this->topFrame . '.activ_tab=tab;
}' .
				($this->View->export->ID ? '' : $this->topFrame . '.activ_tab=1;') .
				($this->View->export->IsFolder == 1 ? $this->topFrame . '.activ_tab=1;' : ''));

		$table = new we_html_table(array("style" => 'width:100%;margin-top:3px', 'class' => 'default'), 1, 1);

		$table->setCol(0, 0, array("class" => "small", 'style' => 'vertical-align:top;padding-left:15px;'), we_html_element::htmlB(g_l('export', '[export]') . ':&nbsp;' . $this->View->export->Text)
		);
		$text = !empty($this->View->export->Path) ? $this->View->export->Path : "/" . $this->View->export->Text;
		$extraJS = 'document.getElementById("tab_"+top.content.activ_tab).className="tabActive";';

		//TODO: we have the following body in several modules!
		$body = we_html_element::htmlBody(array('onresize' => 'setFrameSize()', 'onload' => 'setFrameSize()', 'id' => 'eHeaderBody'), we_html_element::htmlDiv(array('id' => 'main'), we_html_element::htmlDiv(array('id' => 'headrow'), we_html_element::htmlNobr(
							we_html_element::htmlB(str_replace(" ", "&nbsp;", g_l('export', '[export]')) . ':&nbsp;') .
							we_html_element::htmlSpan(array('id' => 'h_path', 'class' => 'header_small'), '<b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b>'
							)
						)
					) .
					$we_tabs->getHTML()
				) .
				we_html_element::jsElement($extraJS)
		);


		return $this->getHTMLDocument($body, we_html_element::jsScript(JS_DIR . 'we_tabs/we_tabs.js') . $tabsHead);
	}

	protected function getHTMLEditorBody(){

		$hiddens = array('cmd' => 'export_edit', 'pnt' => 'edbody');

		if(we_base_request::_(we_base_request::BOOL, "home")){
			$hiddens["cmd"] = "home";
			$GLOBALS["we_print_not_htmltop"] = true;
			$GLOBALS["we_head_insert"] = $this->View->getJSProperty();
			$GLOBALS["we_body_insert"] = we_html_element::htmlForm(array("name" => "we_form"), $this->View->getCommonHiddens($hiddens) . we_html_element::htmlHidden("home", 0)
			);
			$GLOBALS["mod"] = "export";
			ob_start();
			include(WE_MODULES_PATH . 'home.inc.php');
			return ob_get_clean();
		}
		$yuiSuggest = & weSuggest::getInstance();
		$body = we_html_element::htmlBody(array("class" => "weEditorBody", "onload" => "loaded=1;" . $this->getJSStart(), "onunload" => "doUnload()"), weSuggest::getYuiFiles() . we_html_element::htmlForm(array("name" => "we_form"), $this->View->getCommonHiddens($hiddens) . $this->getHTMLProperties()) . $yuiSuggest->getYuiJs()
		);
		return $this->getHTMLDocument($body, $this->View->getJSProperty());
	}

	protected function getHTMLEditorFooter(){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#EFF0EF"), ""));
		}

		$col = 0;
		$table2 = new we_html_table(array('style' => 'margin-top:10px;', 'class' => 'default', "width" => 210), 1, 5);
		$table2->setRow(0, array('style' => 'vertical-align:middle;'));
		$table2->setCol(0, $col++, array("nowrap" => null), we_html_button::create_button(we_html_button::SAVE, "javascript:we_save()"));

		if($this->View->export->IsFolder == 0){
			$table2->setCol(0, $col++, array("nowrap" => null), we_html_button::create_button("export", "javascript:top.content.we_cmd('start_export')", true, 100, 22, '', '', !permissionhandler::hasPerm("MAKE_EXPORT"))
			);
		}

		$table2->setCol(0, $col++, array("nowrap" => null), we_html_tools::getPixel(290, 5));

		$js = we_html_element::jsElement('
function we_save() {
	top.content.we_cmd("save_export");

}
function doProgress(progress) {
	var elem = document.getElementById("progress");
	if(elem.style.display == "none") elem.style.display = "";
	setProgress(progress);
}

function hideProgress() {
	var elem = document.getElementById("progress");
	if(elem.style.display != "none") elem.style.display = "none";
}
		');

		$text = we_base_request::_(we_base_request::STRING, "current_description", g_l('export', '[working]'));
		$progress = we_base_request::_(we_base_request::INT, "percent", 0);

		$progressbar = new we_progressBar($progress);
		$progressbar->setStudLen(200);
		$progressbar->addText($text, 0, "current_description");

		$table2->setCol(0, 4, array("id" => "progress", "style" => "display: none", "nowrap" => null), $progressbar->getHtml());

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array("bgcolor" => "white", "class" => "editfooter", "marginwidth" => 15, "marginheight" => 0, "leftmargin" => 15, "topmargin" => 0), we_html_element::htmlForm(array(), $table2->getHtml())
				), (isset($progressbar) ? $progressbar->getJSCode() : "") . $js
		);
	}

	function getHTMLProperties($preselect = ""){// TODO: move to weExportView
		$this->SelectionTree->init($this->frameset, $this->editorBodyFrame, $this->editorBodyFrame, $this->cmdFrame);

		$tabNr = we_base_request::_(we_base_request::INT, "tabnr", 1);

		return we_html_element::jsElement('
var log_counter=0;
function toggle(id){
	var elem = document.getElementById(id);
	if(elem.style.display == "none") elem.style.display = "";
	else elem.style.display = "none";
}

function clearLog(){
	' . $this->editorBodyFrame . '.document.getElementById("log").innerHTML = "";
}

function addLog(text){
	' . $this->editorBodyFrame . '.document.getElementById("log").innerHTML+= text;
	' . $this->editorBodyFrame . '.document.getElementById("log").scrollTop = 50000;
}
') .
			we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ($tabNr == 1 ? '' : 'display: none')), we_html_multiIconBox::getHTML('', $this->getHTMLTab1(), 30, '', -1, '', '', false, $preselect)) .
			we_html_element::htmlDiv(array('id' => 'tab2', 'style' => ($tabNr == 2 ? '' : 'display: none')), we_html_multiIconBox::getHTML('', $this->getHTMLTab2(), 30, '', -1, '', '', false, $preselect)) .
			we_html_element::htmlDiv(array('id' => 'tab3', 'style' => ($tabNr == 3 ? '' : 'display: none')), we_html_multiIconBox::getHTML('', $this->getHTMLTab3(), 30, '', -1, '', '', false, $preselect));
	}

	function getHTMLTab1(){
		$parts = array(
			array(
				"headline" => g_l('export', '[property]'),
				"html" => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Text", '', $this->View->export->Text, '', 'style="width: ' . $this->_width_size . 'px;" id="yuiAcInputPathName" onchange="top.content.setHot();" onblur="parent.edheader.setPathName(this.value); parent.edheader.setTitlePath()" onchange="' . $this->topFrame . '.hot=1;"'), g_l('export', '[name]')) . '<br/>' .
				$this->getHTMLDirChooser(),
				"space" => $this->_space_size)
		);

		if($this->View->export->IsFolder == 1){
			return $parts;
		}

		$parts[] = array(
			"headline" => g_l('export', '[export_to]'),
			"html" => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Filename", 75, $this->View->export->Filename, '', 'style="width: ' . $this->_width_size . 'px;" onchange="' . $this->topFrame . '.hot=1;"'), g_l('export', '[filename]')),
			"space" => $this->_space_size,
			"noline" => 1
		);

		$table = new we_html_table(array('class' => 'default withSpace'), 2, 1);
		$table->setColContent(0, 0, we_html_tools::htmlSelect('ExportTo', array('local' => g_l('export', '[export_to_local]'), "server" => g_l('export', '[export_to_server]')), 1, $this->View->export->ExportTo, false, array('onchange' => 'toggle(\'save_to\');' . $this->topFrame . '.hot=1;'), 'value', $this->_width_size));
		$table->setCol(1, 0, array("id" => "save_to", "style" => ($this->View->export->ExportTo === 'server' ? 'display:block' : 'display: none')), we_html_tools::htmlFormElementTable($this->formFileChooser(($this->_width_size - 120), "ServerPath", $this->View->export->ServerPath, "", "folder"), g_l('export', '[save_to]')));


		$parts[] = array(
			"headline" => "",
			"html" => $table->getHtml(),
			"space" => $this->_space_size
		);

		$js = we_html_element::jsElement('
function closeAllSelection(){
	var elem = document.getElementById("auto");
	elem.style.display = "none";
	elem = document.getElementById("manual");
	elem.style.display = "none";
}
function closeAllType(){
	var elem = document.getElementById("doctype");
	elem.style.display = "none";
	' . (defined('OBJECT_TABLE') ? '
	elem = document.getElementById("classname");
	elem.style.display = "none";' : '') . '
}');

		$dtq = we_docTypes::getDoctypeQuery($this->db);
		$this->db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		$docTypes = $this->db->getAllFirst(false);

		if(defined('OBJECT_TABLE')){
			$this->db->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ORDER BY Text');
			$classNames = $this->db->getAllFirst(false);
		}

		$FolderPath = $this->View->export->Folder ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->View->export->Folder), '', $this->db) : "/";

		$table = new we_html_table(array('class' => 'default'), 4, 1);

		$seltype = array('doctype' => g_l('export', '[doctypename]'));
		if(defined('OBJECT_TABLE')){
			$seltype['classname'] = g_l('export', '[classname]');
		}

		$table->setCol(0, 0, array('style' => 'padding-bottom:5px;'), we_html_tools::htmlSelect('SelectionType', $seltype, 1, $this->View->export->SelectionType, false, array('onchange' => "closeAllType();toggle(this.value);' . $this->topFrame . '.hot=1;"), 'value', $this->_width_size));
		$table->setCol(1, 0, array("id" => "doctype", "style" => ($this->View->export->SelectionType === 'doctype' ? 'display:block' : 'display: none')), we_html_tools::htmlSelect('DocType', $docTypes, 1, $this->View->export->DocType, false, array('onchange' => $this->topFrame . '.hot=1;'), 'value', $this->_width_size) .
			we_html_tools::htmlFormElementTable($this->formWeChooser(FILE_TABLE, ($this->_width_size - 120), 0, 'Folder', $this->View->export->Folder, 'FolderPath', $FolderPath), g_l('export', '[dir]'))
		);
		if(defined('OBJECT_TABLE')){
			$table->setCol(2, 0, array("id" => "classname", "style" => ($this->View->export->SelectionType === "classname" ? "display:block" : "display: none")), we_html_tools::htmlSelect('ClassName', $classNames, 1, $this->View->export->ClassName, false, array('onchange' => $this->topFrame . '.hot=1;'), 'value', $this->_width_size)
			);
		}

		$table->setColContent(3, 0, $this->getHTMLCategory());

		$selectionTypeHtml = $table->getHTML();

		$table = new we_html_table(array('class' => 'default'), 3, 1);
		$table->setCol(0, 0, array('style' => 'padding-bottom:5px;'), we_html_tools::htmlSelect('Selection', array('auto' => g_l('export', '[auto_selection]'), "manual" => g_l('export', '[manual_selection]')), 1, $this->View->export->Selection, false, array('onchange' => 'closeAllSelection();toggle(this.value);closeAllType();toggle(\'doctype\');' . $this->topFrame . '.hot=1;'), 'value', $this->_width_size));
		$table->setCol(1, 0, array('id' => 'auto', 'style' => ($this->View->export->Selection === 'auto' ? 'display:block' : 'display: none')), we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_auto_selection]'), we_html_tools::TYPE_INFO, $this->_width_size) .
			$selectionTypeHtml
		);

		$table->setCol(2, 0, array('id' => 'manual', "style" => ($this->View->export->Selection === 'manual' ? "display:block" : "display: none")), we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_manual_selection]') . " " . g_l('export', '[select_export]'), we_html_tools::TYPE_INFO, $this->_width_size) .
			$this->SelectionTree->getHTMLMultiExplorer($this->_width_size, 200)
		);

		$parts[] = array(
			"headline" => g_l('export', '[selection]'),
			"html" => $js . $table->getHtml(),
			"space" => $this->_space_size
		);

		return $parts;
	}

	function getHTMLTab2(){
		$formattable = new we_html_table(array(), 5, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden($this->View->export->HandleDefTemplates, "HandleDefTemplates", g_l('export', '[handle_def_templates]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleDocIncludes ? true : false), "HandleDocIncludes", g_l('export', '[handle_document_includes]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		if(defined('OBJECT_TABLE')){
			$formattable->setCol(2, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleObjIncludes ? true : false), "HandleObjIncludes", g_l('export', '[handle_object_includes]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		}
		$formattable->setCol(3, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleDocLinked ? true : false), "HandleDocLinked", g_l('export', '[handle_document_linked]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		$formattable->setCol(4, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleThumbnails ? true : false), "HandleThumbnails", g_l('export', '[handle_thumbnails]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));

		$parts = array(
			array(
				"headline" => g_l('export', '[handle_document_options]') . we_html_element::htmlBr() . g_l('export', '[handle_template_options]'),
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_document_options]'), we_html_tools::TYPE_INFO, $this->_width_size, true, 70) . $formattable->getHtml(),
				"space" => $this->_space_size)
		);

		if(defined('OBJECT_TABLE')){
			$formattable = new we_html_table(array(), 3, 1);
			$formattable->setCol(0, 0, array("colspan" => 2), we_html_forms::checkboxWithHidden(($this->View->export->HandleDefClasses ? true : false), "HandleDefClasses", g_l('export', '[handle_def_classes]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));
			$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleObjEmbeds ? true : false), "HandleObjEmbeds", g_l('export', '[handle_object_embeds]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));
			$parts[] = array(
				"headline" => g_l('export', '[handle_object_options]') . we_html_element::htmlBr() . g_l('export', '[handle_classes_options]'),
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_object_options]'), we_html_tools::TYPE_INFO, $this->_width_size, true, 70) . $formattable->getHtml(),
				"space" => $this->_space_size
			);
		}

		$formattable = new we_html_table(array(), 3, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleDoctypes ? true : false), "HandleDoctypes", g_l('export', '[handle_doctypes]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleCategorys ? true : false), "HandleCategorys", g_l('export', '[handle_categorys]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		$formattable->setCol(2, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleNavigation ? true : false), "HandleNavigation", g_l('export', '[handle_navigation]'), false, 'defaultfont', $this->topFrame . '.hot=1;', false, g_l('export', '[navigation_hint]'), 1, 509));

		$parts[] = array(
			"headline" => g_l('export', '[handle_doctype_options]'),
			"html" => $formattable->getHtml(),
			"space" => $this->_space_size
		);

		$parts[] = array(
			"headline" => g_l('export', '[export_depth]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_exportdeep_options]'), we_html_tools::TYPE_INFO, $this->_width_size) . '<br/>' . we_html_element::htmlLabel(array('style' => 'padding-right:5px;'), g_l('export', '[to_level]')) . we_html_tools::htmlTextInput("ExportDepth", 10, $this->View->export->ExportDepth, "", "onBlur=\"var r=parseInt(this.value);if(isNaN(r)) this.value=" . $this->View->export->ExportDepth . "; else{ this.value=r; " . $this->topFrame . ".hot=1;}\"", "text", 50),
			"space" => $this->_space_size
		);

		$formattable = new we_html_table(array(), 1, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleOwners ? true : false), "HandleOwners", g_l('export', '[handle_owners]'), false, 'defaultfont', $this->topFrame . '.hot=1;'));

		$parts[] = array(
			"headline" => g_l('export', '[handle_owners_option]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_owners]'), we_html_tools::TYPE_INFO, $this->_width_size) . $formattable->getHtml(),
			"space" => $this->_space_size
		);


		return $parts;
	}

	function getHTMLTab3(){
		return array(
			array(
				"headline" => '',
				"html" => we_html_element::htmlDiv(array('class' => 'blockWrapper', 'style' => 'width: 650px; height: 400px; border:1px #dce6f2 solid;', 'id' => 'log'), ''),
				"space" => 0)
		);
	}

	function getHTMLDirChooser(){
		$path = id_to_path($this->View->export->ParentID, EXPORT_TABLE);
		$cmd1 = "document.we_form.elements.ParentID.value";

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:top.content.setHot();we_cmd('we_export_dirSelector'," . $cmd1 . ",'" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("document.we_form.elements.ParentPath.value") . "','" . we_base_request::encCmd("top.hot=1;") . "')");

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("PathGroup");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput("ParentPath", $path, array("onchange" => $this->topFrame . '.hot=1;'));
		$yuiSuggest->setLabel(g_l('export', '[group]'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult("ParentID", $this->View->export->ParentID);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable(EXPORT_TABLE);
		$yuiSuggest->setWidth($this->_width_size - 120);
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}

	private function getLoadCode(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) !== false){
			return we_html_element::jsElement("self.location='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=loadTree&we_cmd[1]=" . we_base_request::_(we_base_request::TABLE, "tab") . "&we_cmd[2]=" . $pid . "&we_cmd[3]=" . we_base_request::_(we_base_request::INTLIST, "openFolders", "") . "&we_cmd[4]=" . $this->editorBodyFrame . "'");
		}
		return '';
	}

	private function getMainLoadCode(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) !== false){

			$js = ($pid ? '' :
					$this->Tree->topFrame . '.treeData.clear();' .
					$this->Tree->topFrame . '.treeData.add(' . $this->Tree->topFrame . '.rootEntry(\'' . we_base_request::_(we_base_request::STRING, "pid") . '\',\'root\',\'root\'));');

			return we_html_element::jsElement($js . $this->Tree->getJSLoadTree(we_export_treeMain::getItemsFromDB($pid)));
		}
		return '';
	}

	private function getDoExportCode(){
		if(!permissionhandler::hasPerm("MAKE_EXPORT")){
			return we_html_element::jsElement(
					we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
			);
		}


		$_progress_update = '';
		$exports = 0;
		if(!isset($_SESSION['weS']['ExImRefTable'])){

			if($this->View->export->Selection === 'manual'){
				$finalDocs = makeArrayFromCSV($this->View->export->selDocs);
				$finalTempl = makeArrayFromCSV($this->View->export->selTempl);
				$finalObjs = makeArrayFromCSV($this->View->export->selObjs);
				$finalClasses = makeArrayFromCSV($this->View->export->selClasses);
			} else {
				$finalDocs = array();
				$finalTempl = array();
				$finalObjs = array();
				$finalClasses = array();
			}
			$xmlExIm = new we_exim_XMLExport();
			$xmlExIm->getSelectedItems($this->View->export->Selection, we_import_functions::TYPE_WE_XML, "", $this->View->export->SelectionType, $this->View->export->DocType, $this->View->export->ClassName, $this->View->export->Categorys, $this->View->export->Folder, $finalDocs, $finalTempl, $finalObjs, $finalClasses);



			$xmlExIm->setOptions(array(
				"handle_def_templates" => $this->View->export->HandleDefTemplates,
				"handle_doctypes" => $this->View->export->HandleDoctypes,
				"handle_categorys" => $this->View->export->HandleCategorys,
				"handle_def_classes" => $this->View->export->HandleDefClasses,
				"handle_document_includes" => $this->View->export->HandleDocIncludes,
				"handle_document_linked" => $this->View->export->HandleDocLinked,
				"handle_object_includes" => $this->View->export->HandleObjIncludes,
				"handle_object_embeds" => $this->View->export->HandleObjEmbeds,
				"handle_class_defs" => $this->View->export->HandleDefClasses,
				"handle_owners" => $this->View->export->HandleOwners,
				"export_depth" => $this->View->export->ExportDepth,
				"handle_documents" => 1,
				"handle_templates" => 1,
				"handle_classes" => 1,
				"handle_objects" => 1,
				"handle_navigation" => $this->View->export->HandleNavigation,
				"handle_thumbnails" => $this->View->export->HandleThumbnails
			));

			$xmlExIm->RefTable->reset();
			$xmlExIm->savePerserves();

			$all = $xmlExIm->RefTable->getLastCount();
			$hiddens = we_html_element::htmlHiddens(array(
					"pnt" => "cmd",
					"all" => $all,
					"cmd" => "do_export"));

			return we_html_element::htmlDocType() . we_html_element::htmlHtml(
					we_html_element::htmlHead('') .
					we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "action" => $this->frameset), $hiddens)
					)
			);
		}
		if($_SESSION['weS']['ExImPrepare']){
			$xmlExIm = new we_export_preparer();

			$xmlExIm->loadPerserves();
			$xmlExIm->prepareExport();
			$all = count($xmlExIm->RefTable->Storage) - 1;
			$xmlExIm->prepare = ($all > $xmlExIm->RefTable->current) && ($xmlExIm->RefTable->current != 0);



			if(!$xmlExIm->prepare){
				$_progress_update = we_html_element::jsElement('
										if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(10, 10) . we_html_element::htmlB(g_l('export', '[start_export]') . ' - ' . date("d.m.Y H:i:s"))) . '<br/><br/>");
										if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(20, 5) . we_html_element::htmlB(g_l('export', '[prepare]'))) . '<br/>");
										if (' . $this->topFrame . '.editor.edfooter.doProgress) ' . $this->topFrame . '.editor.edfooter.doProgress(0);
										if(' . $this->topFrame . '.editor.edfooter.setProgressText) ' . $this->topFrame . '.editor.edfooter.setProgressText("current_description","' . g_l('export', '[working]') . '");
										if(' . $this->editorBodyFrame . '.addLog){
										' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(20, 5) . we_html_element::htmlB(g_l('export', '[export]'))) . '<br/>");
									}
								');
				//FIXME: set export type in getHeader
				we_base_file::save($this->View->export->ExportFilename, we_exim_XMLExIm::getHeader(), "wb");
				if($this->View->export->HandleOwners){
					we_base_file::save($this->View->export->ExportFilename, we_exim_XMLExport::exportInfoMap($xmlExIm->RefTable->Users), "ab");
				}

				$xmlExIm->RefTable->reset();
			} else {
				$percent = max(min(($all ? (($xmlExIm->RefTable->current / $all) * 100) : 0), 100), 0);

				$_progress_update = we_html_element::jsElement('
									if (' . $this->topFrame . '.editor.edfooter.doProgress) ' . $this->topFrame . '.editor.edfooter.doProgress("' . $percent . '");
									if(' . $this->topFrame . '.editor.edfooter.setProgressText) ' . $this->topFrame . '.editor.edfooter.setProgressText("current_description","' . g_l('export', '[prepare]') . '");
							');
			}

			$xmlExIm->savePerserves();

			$hiddens = we_html_element::htmlHiddens(array(
					"pnt" => "cmd",
					"all" => $all,
					"cmd" => "do_export"));

			return we_html_element::htmlDocType() . we_html_element::htmlHtml(
					we_html_element::htmlHead('') .
					we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "action" => $this->frameset), $hiddens) . $_progress_update
					)
			);
		}
		$xmlExIm = new we_exim_XMLExport();
		$xmlExIm->loadPerserves();
		$exports = 0;

		$all = count($xmlExIm->RefTable->Storage);

		$ref = $xmlExIm->RefTable->getNext();

		if($ref->ID && $ref->ContentType){
			$table = $this->db->escape($ref->Table);
			$exists = ($ref->ContentType === 'weBinary') || f('SELECT 1 FROM ' . $table . ' WHERE ID=' . intval($ref->ID), '', $this->db);

			if($exists){
				$xmlExIm->export($ref->ID, $ref->ContentType, $this->View->export->ExportFilename);
				$exports = $xmlExIm->RefTable->current;

				switch($ref->ContentType){
					case 'weBinary':
						$_progress_update .= "\n" .
							we_html_element::jsElement('
											if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(50, 5) . we_html_element::htmlB(g_l('export', '[weBinary]'))) . '&nbsp;&nbsp;' . $ref->ID . '<br/>");
										') . "\n";
						$proceed = false;
						break;
					case 'doctype':
						$_path = f('SELECT DocType FROM ' . $table . ' WHERE ID = ' . intval($ref->ID), '', $this->db);
						$proceed = true;
						break;
					case 'weNavigationRule':
						$_path = f('SELECT NavigationName FROM ' . $table . ' WHERE ID = ' . intval($ref->ID), '', $this->db);
						$proceed = true;
						break;
					case 'weThumbnail':
						$_path = f('SELECT Name FROM ' . $table . ' WHERE ID = ' . intval($ref->ID), '', $this->db);
						$proceed = true;
						break;

					default:
						$_path = id_to_path($ref->ID, $table);
						$proceed = true;
						break;
				}
				if($proceed){
					$_progress_text = we_html_element::htmlB(g_l('contentTypes', '[' . $ref->ContentType . ']', true) !== false ? g_l('contentTypes', '[' . $ref->ContentType . ']') : (g_l('export', '[' . $ref->ContentType . ']', true) !== false ? g_l('export', '[' . $ref->ContentType . ']') : '')) . '&nbsp;&nbsp;' . $_path;

					if(strlen($_path) > 75){
						$_progress_text = addslashes(substr($_progress_text, 0, 65) . '<abbr title="' . $_path . '">...</abbr>' . substr($_progress_text, -10));
					}

					$_progress_update .= "\n" .
						we_html_element::jsElement('
											if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(50, 5) . $_progress_text) . '<br/>");
										');
				}
			}
		}

		$percent = max(min(($all ? intval(($exports / $all) * 100) : 0), 100), 0);
		$_progress_update .= "\n" .
			we_html_element::jsElement('
									if (' . $this->topFrame . '.editor.edfooter.doProgress) ' . $this->topFrame . '.editor.edfooter.doProgress(' . $percent . ');
						') . "\n";
		$_SESSION['weS']['ExImCurrentRef'] = $xmlExIm->RefTable->current;

		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "cmd",
				"all" => $all,
				"cmd" => "do_export"));


		if($all > $exports){
			return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET, we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "action" => $this->frameset), $hiddens) . $_progress_update
					)
			);
		}
		if(is_writable($this->View->export->ExportFilename)){
			we_base_file::save($this->View->export->ExportFilename, we_exim_XMLExIm::getFooter(), "ab");
		}
		$_progress_update .= "\n" .
			we_html_element::jsElement('
									if (' . $this->topFrame . '.editor.edfooter.doProgress) ' . $this->topFrame . '.editor.edfooter.doProgress(100);
									if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("<br/>' . addslashes(we_html_tools::getPixel(10, 10) . we_html_element::htmlB(g_l('export', '[end_export]') . ' - ' . date("d.m.Y H:i:s"))) . '<br/><br/>");
							') . "\n" .
			($this->View->export->ExportTo === 'local' ?
				we_html_element::jsElement($this->editorBodyFrame . '.addLog(\'' .
					we_html_element::htmlSpan(array("class" => "defaultfont"), addslashes(we_html_tools::getPixel(10, 1) . g_l('export', '[backup_finished]')) . "<br/>" .
						addslashes(we_html_tools::getPixel(10, 1)) . g_l('export', '[download_starting2]') . "<br/><br/>" .
						addslashes(we_html_tools::getPixel(10, 1)) . g_l('export', '[download_starting3]') . "<br/>" .
						addslashes(we_html_tools::getPixel(10, 1)) . we_html_element::htmlB(we_html_element::htmlA(array("href" => $this->frameset . "?pnt=cmd&cmd=upload&exportfile=" . urlencode($this->View->export->ExportFilename), 'download' => $this->View->export->ExportFilename), g_l('export', '[download]'))) . "<br/><br/>"
					) .
					'\');') :
				''
			);

		$out = we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head . $_progress_update .
					we_html_element::jsElement('function showEndStatus(){' . we_message_reporting::getShowMessageCall(g_l('export', '[server_finished]'), we_message_reporting::WE_MESSAGE_NOTICE) . ';}')
				) .
				we_html_element::htmlBody(
					array(
						"bgcolor" => "#ffffff",
						"marginwidth" => 5,
						"marginheight" => 5,
						"leftmargin" => 5,
						"topmargin" => 5,
						"onload" =>
						($this->View->export->ExportTo === 'local' ?
							($this->cmdFrame . ".location='" . $this->frameset . "?pnt=cmd&cmd=upload&exportfile=" . urlencode($this->View->export->ExportFilename) . "';") :
							'showEndStatus();') .
						$this->topFrame . ".editor.edfooter.hideProgress();"
					)
				), null
		);
		$xmlExIm->unsetPerserves();
		return $out;
	}

	private function getUploadCode(){
		if(($_filename = we_base_request::_(we_base_request::FILE, "exportfile"))){
			$_filename = basename(urldecode($_filename));

			if(file_exists(TEMP_PATH . $_filename) // Does file exist?
				&& !preg_match('%p?html?%i', $_filename) && stripos($_filename, "inc") === false && !preg_match('%php3?%i', $_filename)){ // Security check
				session_write_close();
				$_size = filesize(TEMP_PATH . $_filename);

				header("Pragma: public");
				header("Expires: 0");
				header("Cache-control: private, max-age=0, must-revalidate");

				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . trim(htmlentities($_filename)) . '"');
				header("Content-Description: " . trim(htmlentities($_filename)));
				header("Content-Length: " . $_size);

				readfile(TEMP_PATH . $_filename);
			} else {
				header("Location: " . $this->frameset . "?pnt=cmd&cmd=upload_failed");
			}
		} else {
			header("Location: " . $this->frameset . "?pnt=cmd&cmd=error=upload_failed");
		}
		exit();
	}

	function getHTMLCmd(){
		switch(we_base_request::_(we_base_request::STRING, "cmd")){
			case "load":
				return $this->getLoadCode();
			case "mainload":
				return $this->getMainLoadCode();
			case "do_export":
				return $this->getDoExportCode();
			case 'upload':
				$this->getUploadCode();
				exit();

			case 'upload_failed':
				return we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('export', '[error_download_failed]'), we_message_reporting::WE_MESSAGE_ERROR)
				);
			default:
				return '';
		}
	}

	/* creates the FileChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	private function formFileChooser($width = "", $IDName = "ParentID", $IDValue = "/", $cmd = "", $filter = ""){
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:top.content.setHot();formFileChooser('browse_server','" . $wecmdenc1 . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value);");

		return we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
				function formFileChooser() {
					var args = "";
					var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if (i < (arguments.length - 1)){ url += "&"; }}
					switch (arguments[0]) {
						case "browse_server":
							new jsWindow(url,"server_selector",-1,-1,660,330,true,false,true);
						break;
					}
				}
		') . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 42, $IDValue, "", ' readonly onchange="' . $this->topFrame . '.hot=1;"', "text", $width, 0), "", "left", "defaultfont", "", permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	private function formWeChooser($table = FILE_TABLE, $width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = ''){
		$Pathvalue = ($Pathvalue ? : f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), "", $this->db));

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:top.content.setHot();we_cmd('we_selector_directory',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");
		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('SelPath');
		$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$yuiSuggest->setInput($Pathname, $Pathvalue, array("onchange" => $this->topFrame . '.hot=1;'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($IDName, $IDValue);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable(FILE_TABLE);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}

	function getHTMLCategory(){
		switch(we_base_request::_(we_base_request::STRING, "cmd")){
			case 'add_cat':
				$arr = makeArrayFromCSV($this->View->export->Categorys);
				if(($cat = we_base_request::_(we_base_request::INTLISTA, "cat", array()))){
					foreach($cat as $id){
						if(strlen($id) && (!in_array($id, $arr))){
							$arr[] = $id;
						}
					}
					$this->View->export->Categorys = implode(',', $arr);
				}
				break;
			case 'del_cat':
				$arr = makeArrayFromCSV($this->View->export->Categorys);
				if(($cat = we_base_request::_(we_base_request::INT, "cat"))){
					foreach($arr as $k => $v){
						if($v == $cat){
							array_splice($arr, $k, 1);
						}
					}
					$this->View->export->Categorys = implode(',', $arr);
				}
				break;
			case 'del_all_cats':
				$this->View->export->Categorys = '';
				break;
			default:
		}



		$hiddens = we_html_element::htmlHiddens(array(
				"Categorys" => $this->View->export->Categorys,
				"cat" => we_base_request::_(we_base_request::RAW, 'cat', "")));


		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:top.content.setHot(); we_cmd('del_all_cats')", true, 0, 0, "", "", (isset($this->View->export->Categorys) ? false : true));
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:top.content.setHot(); we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','fillIDs();opener." . $this->editorBodyFrame . ".we_cmd(\\'add_cat\\',top.allIDs);')");

		$cats = new we_chooser_multiDir($this->_width_size, $this->View->export->Categorys, "del_cat", $delallbut . $addbut, "", '"we/category"', CATEGORY_TABLE);

		if(!permissionhandler::hasPerm("EDIT_KATEGORIE")){
			$cats->isEditable = false;
		}
		return $hiddens . we_html_tools::htmlFormElementTable($cats->get(), g_l('export', '[categories]'), "left", "defaultfont");
	}

}
