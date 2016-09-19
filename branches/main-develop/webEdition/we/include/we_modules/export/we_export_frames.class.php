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
we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT);

class we_export_frames extends we_modules_frame{
	var $SelectionTree;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->treeDefaultWidth = 220;
		$this->module = "export";

		$this->Tree = new we_export_treeMain($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->SelectionTree = new we_export_tree($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->View = new we_export_view($frameset);
	}

	public function getHTMLDocumentHeader($what = '', $mode = ''){
		if($what != "cmd" && $what != "load" && !we_base_request::_(we_base_request::FILE, "exportfile")){
			return parent::getHTMLDocumentHeader();
		}
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case "load":
				return $this->getHTMLCmd();
			case 'frameset':
				$this->View->export->clearSessionVars();
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode());
			case "treeheader":
			case "treefooter":
				return '';
			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return parent::getHTMLEditorHeader(0);
		}

		$we_tabs = new we_tabs();
		$we_tabs->addTab(we_base_constants::WE_ICON_PROPERTIES, false, "setTab(1);", ["id" => "tab_1", 'title' => g_l('export', '[property]')]);
		if($this->View->export->IsFolder == 0){
			$we_tabs->addTab(g_l('export', '[options]'), false, "setTab(2);", ["id" => "tab_2"]);
			$we_tabs->addTab(g_l('export', '[log]'), false, "setTab(3);", ["id" => "tab_3"]);
		}

		$tabsHead = we_tabs::getHeader('
function setTab(tab) {
	parent.edbody.toggle("tab"+top.content.activ_tab);
	parent.edbody.toggle("tab"+tab);
	top.content.activ_tab=tab;
}' .
				($this->View->export->ID ? '' : 'top.content.activ_tab=1;') .
				($this->View->export->IsFolder == 1 ? 'top.content.activ_tab=1;' : ''));

		$table = new we_html_table(["style" => 'width:100%;margin-top:3px', 'class' => 'default'], 1, 1);

		$table->setCol(0, 0, ['class' => "small", 'style' => 'vertical-align:top;padding-left:15px;'], we_html_element::htmlB(g_l('export', '[export]') . ':&nbsp;' . $this->View->export->Text));
		$text = !empty($this->View->export->Path) ? $this->View->export->Path : "/" . $this->View->export->Text;
		$extraJS = 'document.getElementById("tab_"+top.content.activ_tab).className="tabActive";';

		//TODO: we have the following body in several modules!
		$body = we_html_element::htmlBody(array('onresize' => 'weTabs.setFrameSize()', 'onload' => 'weTabs.setFrameSize()', 'id' => 'eHeaderBody'), we_html_element::htmlDiv(array(
					'id' => 'main'), we_html_element::htmlDiv(array('id' => 'headrow'), we_html_element::htmlNobr(
							we_html_element::htmlB(str_replace(" ", "&nbsp;", g_l('export', '[export]')) . ':&nbsp;') .
							we_html_element::htmlSpan(array('id' => 'h_path', 'class' => 'header_small'), '<b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b>'
							)
						)
					) .
					$we_tabs->getHTML()
				) .
				we_html_element::jsElement($extraJS)
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){
		$hiddens = array('cmd' => 'export_edit', 'pnt' => 'edbody');

		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->View->getHomeScreen();
		}
		$yuiSuggest = & weSuggest::getInstance();
		//FIXME: folder don't have a tree to start.
		$body = we_html_element::htmlBody(['class' => 'weEditorBody', "onload" => "loaded=1;if(window.startTree){startTree();start();}", "onunload" => "WE().util.jsWindow.prototype.closeAll(window);"], weSuggest::getYuiFiles() . we_html_element::htmlForm([
					'name' => 'we_form'], $this->View->getCommonHiddens($hiddens) . $this->getHTMLProperties()) . $yuiSuggest->getYuiJs()
		);
		return $this->getHTMLDocument($body, $this->View->getJSProperty());
	}

	protected function getHTMLEditorFooter(array $btn_cmd = [], $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return parent::getHTMLEditorFooter([]);
		}

		$col = 0;
		$table2 = new we_html_table(['style' => 'margin-top:10px;', 'class' => 'default', "width" => 210], 1, 5);
		$table2->setRow(0, ['style' => 'vertical-align:middle;']);
		$table2->setCol(0, $col++, [], we_html_button::create_button(we_html_button::SAVE, "javascript:top.content.we_cmd('save_export');"));

		if($this->View->export->IsFolder == 0){
			$table2->setCol(0, $col++, [], we_html_button::create_button('export', "javascript:top.content.we_cmd('start_export')", true, 100, 22, '', '', !permissionhandler::hasPerm("MAKE_EXPORT"))
			);
		}

		$table2->setCol(0, $col++, array('style' => 'width:290px;'));

		$js = we_html_element::jsElement('
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

		$table2->setCol(0, 4, ["id" => "progress", "style" => "display: none"], $progressbar->getHtml());

		return $this->getHTMLDocument(
				we_html_element::htmlBody(['id' => 'footerBody'], we_html_element::htmlForm([], $table2->getHtml())
				), (isset($progressbar) ? $progressbar->getJSCode() : "") . $js
		);
	}

	function getHTMLProperties($preselect = ""){// TODO: move to weExportView
		$this->SelectionTree->init($this->frameset, 'top.content.editor.edbody', 'top.content.editor.edbody', $this->cmdFrame);

		$tabNr = we_base_request::_(we_base_request::INT, "tabnr", 1);

		return we_html_element::jsElement('
function toggle(id){
	var elem = document.getElementById(id);
	if(elem.style.display == "none") elem.style.display = "";
	else elem.style.display = "none";
}

function clearLog(){
	top.content.editor.edbody.document.getElementById("log").innerHTML = "";
}

function addLog(text){
	top.content.editor.edbody.document.getElementById("log").innerHTML+= text+"<br/>";
	top.content.editor.edbody.document.getElementById("log").scrollTop = 50000;
}
') .
			we_html_element::htmlDiv(['id' => 'tab1', 'style' => ($tabNr == 1 ? '' : 'display: none')], we_html_multiIconBox::getHTML('', $this->getHTMLTab1(), 30, '', -1, '', '', false, $preselect)) .
			we_html_element::htmlDiv(['id' => 'tab2', 'style' => ($tabNr == 2 ? '' : 'display: none')], we_html_multiIconBox::getHTML('', $this->getHTMLTab2(), 30, '', -1, '', '', false, $preselect)) .
			we_html_element::htmlDiv(['id' => 'tab3', 'style' => ($tabNr == 3 ? '' : 'display: none')], we_html_multiIconBox::getHTML('', $this->getHTMLTab3(), 30, '', -1, '', '', false, $preselect));
	}

	function getHTMLTab1(){
		$parts = [
				[
				'headline' => g_l('export', '[property]'),
				'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Text", '', $this->View->export->Text, '', 'style="width: 520px;" id="yuiAcInputPathName" onchange="top.content.setHot();" onblur="parent.edheader.weTabs.setTitlePath(this.value);" onchange="top.content.hot=1;"'), g_l('export', '[name]')) . '<br/>' .
				$this->getHTMLDirChooser(),
				'space' => we_html_multiIconBox::SPACE_MED]
		];

		if($this->View->export->IsFolder == 1){
			return $parts;
		}

		$parts[] = [
			'headline' => g_l('export', '[export_to]'),
			'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Filename", 75, $this->View->export->Filename, '', 'style="width: 520px;" onchange="top.content.hot=1;"'), g_l('export', '[filename]')),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		];

		$table = new we_html_table(['class' => 'default withSpace'], 2, 1);
		$table->setColContent(0, 0, we_html_tools::htmlSelect('ExportTo', ['local' => g_l('export', '[export_to_local]'), "server" => g_l('export', '[export_to_server]')], 1, $this->View->export->ExportTo, false, [
				'onchange' => 'toggle(\'save_to\');top.content.hot=1;'], 'value', 520));
		$table->setCol(1, 0, ["id" => "save_to", "style" => ($this->View->export->ExportTo === 'server' ? 'display:block' : 'display: none')], we_html_tools::htmlFormElementTable($this->formFileChooser(400, "ServerPath", $this->View->export->ServerPath, "", "folder"), g_l('export', '[save_to]')));


		$parts[] = ["headline" => "",
			"html" => $table->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED
		];

		$js = we_html_element::jsElement('
function formFileChooser() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

switch (args[0]) {
		case "browse_server":
			new (WE().util.jsWindow)(window, url,"server_selector",-1,-1,660,330,true,false,true);
		break;
	}
}
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

		$table = new we_html_table(['class' => 'default'], 4, 1);

		$seltype = ['doctype' => g_l('export', '[doctypename]')];
		if(defined('OBJECT_TABLE')){
			$seltype['classname'] = g_l('export', '[classname]');
		}

		$table->setCol(0, 0, ['style' => 'padding-bottom:5px;'], we_html_tools::htmlSelect('SelectionType', $seltype, 1, $this->View->export->SelectionType, false, ['onchange' => "closeAllType();toggle(this.value);top.content.hot=1;"], 'value', 520));
		$table->setCol(1, 0, ["id" => "doctype", "style" => ($this->View->export->SelectionType === 'doctype' ? 'display:block' : 'display: none')], we_html_tools::htmlSelect('DocType', $docTypes, 1, $this->View->export->DocType, false, [
				'onchange' => 'top.content.hot=1;'], 'value', 520) .
			we_html_tools::htmlFormElementTable($this->formWeChooser(FILE_TABLE, 400, 0, 'Folder', $this->View->export->Folder, 'FolderPath', $FolderPath), g_l('export', '[dir]'))
		);
		if(defined('OBJECT_TABLE')){
			$table->setCol(2, 0, ["id" => "classname", "style" => ($this->View->export->SelectionType === "classname" ? "display:block" : "display: none")], we_html_tools::htmlSelect('ClassName', $classNames, 1, $this->View->export->ClassName, false, [
					'onchange' => 'top.content.hot=1;'], 'value', 520)
			);
		}

		$table->setColContent(3, 0, $this->getHTMLCategory());

		$selectionTypeHtml = $table->getHTML();

		$table = new we_html_table(['class' => 'default'], 3, 1);
		$table->setCol(0, 0, ['style' => 'padding-bottom:5px;'], we_html_tools::htmlSelect('Selection', ['auto' => g_l('export', '[auto_selection]'), "manual" => g_l('export', '[manual_selection]')], 1, $this->View->export->Selection, false, [
				'onchange' => 'closeAllSelection();toggle(this.value);closeAllType();toggle(\'doctype\');top.content.hot=1;'], 'value', 520));
		$table->setCol(1, 0, ['id' => 'auto', 'style' => ($this->View->export->Selection === 'auto' ? 'display:block' : 'display: none')], we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_auto_selection]'), we_html_tools::TYPE_INFO, 520) .
			$selectionTypeHtml
		);

		$table->setCol(2, 0, ['id' => 'manual', "style" => ($this->View->export->Selection === 'manual' ? "display:block" : "display: none")], we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_manual_selection]') . " " . g_l('export', '[select_export]'), we_html_tools::TYPE_INFO, 520) .
			$this->SelectionTree->getHTMLMultiExplorer(520, 200)
		);

		$parts[] = array(
			"headline" => g_l('export', '[selection]'),
			"html" => $js . $table->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED
		);

		return $parts;
	}

	private function getHTMLTab2(){
		$formattable = new we_html_table([], 5, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden($this->View->export->HandleDefTemplates, "HandleDefTemplates", g_l('export', '[handle_def_templates]'), false, 'defaultfont', 'top.content.hot=1;'));
		$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleDocIncludes ? true : false), "HandleDocIncludes", g_l('export', '[handle_document_includes]'), false, 'defaultfont', 'top.content.hot=1;'));
		if(defined('OBJECT_TABLE')){
			$formattable->setCol(2, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleObjIncludes ? true : false), "HandleObjIncludes", g_l('export', '[handle_object_includes]'), false, 'defaultfont', 'top.content.hot=1;'));
		}
		$formattable->setCol(3, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleDocLinked ? true : false), "HandleDocLinked", g_l('export', '[handle_document_linked]'), false, 'defaultfont', 'top.content.hot=1;'));
		$formattable->setCol(4, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleThumbnails ? true : false), "HandleThumbnails", g_l('export', '[handle_thumbnails]'), false, 'defaultfont', 'top.content.hot=1;'));

		$parts = [["headline" => g_l('export', '[handle_document_options]') . we_html_element::htmlBr() . g_l('export', '[handle_template_options]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_document_options]'), we_html_tools::TYPE_INFO, 520, true, 60) . $formattable->getHtml(),
				'space' => we_html_multiIconBox::SPACE_MED]
		];

		if(defined('OBJECT_TABLE')){
			$formattable = new we_html_table([], 3, 1);
			$formattable->setCol(0, 0, ["colspan" => 2], we_html_forms::checkboxWithHidden(($this->View->export->HandleDefClasses ? true : false), "HandleDefClasses", g_l('export', '[handle_def_classes]'), false, 'defaultfont', 'top.content.hot=1;'));
			$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleObjEmbeds ? true : false), "HandleObjEmbeds", g_l('export', '[handle_object_embeds]'), false, 'defaultfont', 'top.content.hot=1;'));
			$parts[] = array(
				"headline" => g_l('export', '[handle_object_options]') . we_html_element::htmlBr() . g_l('export', '[handle_classes_options]'),
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_object_options]'), we_html_tools::TYPE_INFO, 520, true, 60) . $formattable->getHtml(),
				'space' => we_html_multiIconBox::SPACE_MED
			);
		}

		$formattable = new we_html_table([], 3, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleDoctypes ? true : false), "HandleDoctypes", g_l('export', '[handle_doctypes]'), false, 'defaultfont', 'top.content.hot=1;'));
		$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleCategorys ? true : false), "HandleCategorys", g_l('export', '[handle_categorys]'), false, 'defaultfont', 'top.content.hot=1;'));
		$formattable->setCol(2, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleNavigation ? true : false), "HandleNavigation", g_l('export', '[handle_navigation]'), false, 'defaultfont', 'top.content.hot=1;', false, g_l('export', '[navigation_hint]'), we_html_tools::TYPE_HELP, false));

		$parts[] = ["headline" => g_l('export', '[handle_doctype_options]'),
			"html" => $formattable->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED
			];

		$parts[] = ["headline" => g_l('export', '[export_depth]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_exportdeep_options]'), we_html_tools::TYPE_INFO, 520) . '<br/>' . we_html_element::htmlLabel([
				'style' => 'padding-right:5px;'], g_l('export', '[to_level]')) . we_html_tools::htmlTextInput("ExportDepth", 10, $this->View->export->ExportDepth, "", "onBlur=\"var r=parseInt(this.value);if(isNaN(r)) this.value=" . $this->View->export->ExportDepth . "; else{ this.value=r; top.content.hot=1;}\"", "text", 50),
			'space' => we_html_multiIconBox::SPACE_MED
			];

		$formattable = new we_html_table([], 1, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleOwners ? true : false), "HandleOwners", g_l('export', '[handle_owners]'), false, 'defaultfont', 'top.content.hot=1;'));

		$parts[] = ["headline" => g_l('export', '[handle_owners_option]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_owners]'), we_html_tools::TYPE_INFO, 520) . $formattable->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED
			];


		return $parts;
	}

	private function getHTMLTab3(){
		return [
				[
				'headline' => '',
				'html' => we_html_element::htmlDiv(['class' => 'blockWrapper', 'style' => 'width: 650px; height: 400px; border:1px #dce6f2 solid;', 'id' => 'log'], ''),
			]
		];
	}

	private function getHTMLDirChooser(){
		$path = id_to_path($this->View->export->ParentID, EXPORT_TABLE);

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:top.content.setHot();we_cmd('we_export_dirSelector',document.we_form.elements.ParentID.value,'ParentID','ParentPath','" . we_base_request::encCmd("top.hot=1;") . "')");

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("PathGroup");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput("ParentPath", $path, array('onchange' => 'top.content.hot=1;'));
		$yuiSuggest->setLabel(g_l('export', '[group]'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult("ParentID", $this->View->export->ParentID);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable(EXPORT_TABLE);
		$yuiSuggest->setWidth(400);
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}

	private function getLoadCode(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) !== false){
			return we_html_element::jsElement("self.location=WE().consts.dirs.WEBEDITION_DIR+'we_cmd.php?we_cmd[0]=loadTree&we_cmd[1]=" . we_base_request::_(we_base_request::TABLE, "tab") . "&we_cmd[2]=" . $pid . "&we_cmd[3]=" . we_base_request::_(we_base_request::INTLIST, "openFolders", "") . "&we_cmd[4]=top.content.editor.edbody&we_cmd[5]=top.content.editor.edbody&we_cmd[6]=top.content.cmd'");
		}
		return '';
	}

	private function getMainLoadCode(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) !== false){

			$js = ($pid ? '' :
				$this->Tree->topFrame . '.treeData.clear();' .
				$this->Tree->topFrame . '.treeData.add(' . $this->Tree->topFrame . '.node.prototype.rootEntry(\'' . we_base_request::_(we_base_request::STRING, "pid") . '\',\'root\',\'root\'));');

			return we_html_element::jsElement($js . $this->Tree->getJSLoadTree($pid, we_export_treeMain::getItemsFromDB($pid)));
		}
		return '';
	}

	private function getDoExportCode(){
		if(!permissionhandler::hasPerm("MAKE_EXPORT")){
			return we_message_reporting::jsMessagePush(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
		}


		$progress_update = '';
		$exports = 0;
		if(!isset($_SESSION['weS']['ExImRefTable'])){

			if($this->View->export->Selection === 'manual'){
				$finalDocs = makeArrayFromCSV($this->View->export->selDocs);
				$finalTempl = makeArrayFromCSV($this->View->export->selTempl);
				$finalObjs = makeArrayFromCSV($this->View->export->selObjs);
				$finalClasses = makeArrayFromCSV($this->View->export->selClasses);
			} else {
				$finalDocs = [];
				$finalTempl = [];
				$finalObjs = [];
				$finalClasses = [];
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

			$all = $xmlExIm->RefTable->getCount();
			$hiddens = we_html_element::htmlHiddens(array(
					"pnt" => "cmd",
					"all" => $all,
					"cmd" => "do_export"));

			return we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "document.we_form.submit()"), we_html_element::htmlForm(array(
							'name' => 'we_form', "method" => "post", "action" => $this->frameset), $hiddens)
					)
			);
		}
		if($_SESSION['weS']['ExImPrepare']){
			$xmlExIm = new we_export_preparer();

			$xmlExIm->loadPerserves();
			$xmlExIm->prepareExport();
			$all = $xmlExIm->RefTable->getCount() - 1;
			$xmlExIm->prepare = ($all > $xmlExIm->RefTable->current) && ($xmlExIm->RefTable->current != 0);



			if(!$xmlExIm->prepare){
				$progress_update = we_html_element::jsElement('
if (top.content.editor.edbody.addLog) top.content.editor.edbody.addLog("' . addslashes(we_html_element::htmlB(g_l('export', '[start_export]') . ' - ' . date("d.m.Y H:i:s"))) . '");
if (top.content.editor.edbody.addLog) top.content.editor.edbody.addLog("' . addslashes(we_html_element::htmlB(g_l('export', '[prepare]'))) . '");
if (top.content.editor.edfooter.doProgress){
	top.content.editor.edfooter.doProgress(0);
}
if(top.content.editor.edfooter.setProgressText){
	top.content.editor.edfooter.setProgressText("current_description","' . g_l('export', '[working]') . '");
}
if(top.content.editor.edbody.addLog){
	top.content.editor.edbody.addLog("' . addslashes(we_html_element::htmlB(g_l('export', '[export]'))) . '");
}');
				//FIXME: set export type in getHeader
				we_base_file::save($this->View->export->ExportFilename, we_exim_XMLExIm::getHeader(), "wb");
				if($this->View->export->HandleOwners){
					we_base_file::save($this->View->export->ExportFilename, we_exim_XMLExport::exportInfoMap($xmlExIm->RefTable->Users), "ab");
				}

				$xmlExIm->RefTable->reset();
			} else {
				$percent = round(max(min(($all ? (($xmlExIm->RefTable->current / $all) * 100) : 0), 100), 0), 2);

				$progress_update = we_html_element::jsElement('
									if (top.content.editor.edfooter.doProgress) top.content.editor.edfooter.doProgress("' . $percent . '");
									if(top.content.editor.edfooter.setProgressText) top.content.editor.edfooter.setProgressText("current_description","' . g_l('export', '[prepare]') . '");
							');
			}

			$xmlExIm->savePerserves();

			$hiddens = we_html_element::htmlHiddens(array(
					"pnt" => "cmd",
					"all" => $all,
					"cmd" => "do_export"));

			return we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "document.we_form.submit()"), we_html_element::htmlForm(array(
							'name' => 'we_form', "method" => "post", "action" => $this->frameset), $hiddens) . $progress_update
					)
			);
		}
		$xmlExIm = new we_exim_XMLExport();
		$xmlExIm->loadPerserves();
		$exports = 0;

		$all = $xmlExIm->RefTable->getCount();

		$ref = $xmlExIm->RefTable->getNext();

		if($ref->ID && $ref->ContentType){
			$table = $this->db->escape($ref->Table);
			$exists = ($ref->ContentType === 'weBinary') || f('SELECT 1 FROM ' . $table . ' WHERE ID=' . intval($ref->ID), '', $this->db);

			if($exists){
				$xmlExIm->export($ref->ID, $ref->ContentType, $this->View->export->ExportFilename);
				$exports = $xmlExIm->RefTable->current;

				switch($ref->ContentType){
					case 'weBinary':
						$progress_update .= "\n" .
							we_html_element::jsElement('
											if (top.content.editor.edbody.addLog) top.content.editor.edbody.addLog("' . addslashes(we_html_element::htmlB(g_l('export', '[weBinary]'))) . '&nbsp;&nbsp;' . $ref->ID . '");
										') . "\n";
						$proceed = false;
						break;
					case 'doctype':
						$path = f('SELECT DocType FROM ' . $table . ' WHERE ID=' . intval($ref->ID), '', $this->db);
						$proceed = true;
						break;
					case we_base_ContentTypes::NAVIGATIONRULE:
						$path = f('SELECT NavigationName FROM ' . $table . ' WHERE ID=' . intval($ref->ID), '', $this->db);
						$proceed = true;
						break;
					case 'weThumbnail':
						$path = f('SELECT Name FROM ' . $table . ' WHERE ID=' . intval($ref->ID), '', $this->db);
						$proceed = true;
						break;

					default:
						$path = id_to_path($ref->ID, $table);
						$proceed = true;
						break;
				}
				if($proceed){
					$progress_text = we_html_element::htmlB(g_l('contentTypes', '[' . $ref->ContentType . ']', true) !== false ? g_l('contentTypes', '[' . $ref->ContentType . ']') : (g_l('export', '[' . $ref->ContentType . ']', true) !== false ? g_l('export', '[' . $ref->ContentType . ']') : '')) . '&nbsp;&nbsp;' . $path;

					if(strlen($path) > 75){
						$progress_text = addslashes(substr($progress_text, 0, 65) . '<abbr title="' . $path . '">...</abbr>' . substr($progress_text, -10));
					}

					$progress_update .= we_html_element::jsElement('
if (top.content.editor.edbody.addLog){
	top.content.editor.edbody.addLog("' . addslashes($progress_text) . '");
}');
				}
			}
		}

		$percent = round(max(min(($all ? intval(($exports / $all) * 100) : 0), 100), 0), 2);
		$progress_update .= we_html_element::jsElement('
if (top.content.editor.edfooter.doProgress){
	top.content.editor.edfooter.doProgress(' . $percent . ');
}');
		$_SESSION['weS']['ExImCurrentRef'] = $xmlExIm->RefTable->current;

		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "cmd",
				"all" => $all,
				"cmd" => "do_export"));

		if($all > $exports){
			return we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(["bgcolor" => "#ffffff", "style" => 'margin:5px', "onload" => "document.we_form.submit()"], we_html_element::htmlForm([
							'name' => 'we_form', "method" => "post", "action" => $this->frameset], $hiddens) . $progress_update
					)
			);
		}
		if(is_writable($this->View->export->ExportFilename)){
			we_base_file::save($this->View->export->ExportFilename, we_exim_XMLExIm::getFooter(), "ab");
		}
		$progress_update .= we_html_element::jsElement('
function showEndStatus(){
	' . we_message_reporting::getShowMessageCall(g_l('export', '[server_finished]'), we_message_reporting::WE_MESSAGE_NOTICE) . ';
}

if (top.content.editor.edfooter.doProgress){
	top.content.editor.edfooter.doProgress(100);
}
if (top.content.editor.edbody.addLog){
	top.content.editor.edbody.addLog("' . addslashes(we_html_element::htmlB(g_l('export', '[end_export]') . ' - ' . date("d.m.Y H:i:s"))) . '");
}' .
				($this->View->export->ExportTo === 'local' ?
				'top.content.editor.edbody.addLog(\'' .
				we_html_element::htmlSpan(['class' => 'defaultfont'], addslashes(g_l('export', '[backup_finished]')) . "<br/>" .
					g_l('export', '[download_starting2]') . "<br/><br/>" .
					g_l('export', '[download_starting3]') . "<br/>" .
					we_html_element::htmlB(we_html_element::htmlA(array("href" => WEBEDITION_DIR . 'we_showMod.php?mod=export&pnt=cmd&cmd=upload&exportfile=' . urlencode($this->View->export->ExportFilename),
							'download' => $this->View->export->ExportFilename), g_l('export', '[download]'))) . "<br/><br/>"
				) .
				'\');' :
				''
				)
		);

		$out = we_html_tools::getHtmlTop('', '', '', $progress_update, we_html_element::htmlBody(
					array(
						'style' => 'margin:5px;',
						"onload" => ($this->View->export->ExportTo === 'local' ?
						($this->cmdFrame . ".location=WE().consts.dirs.WEBEDITION_DIR+'we_showMod.php?mod=export&pnt=cmd&cmd=upload&exportfile=" . urlencode($this->View->export->ExportFilename) . "';") :
						'showEndStatus();') .
						"top.content.editor.edfooter.hideProgress();"
					)
				), null
		);
		$xmlExIm->unsetPerserves();
		return $out;
	}

	private function getUploadCode(){
		if(($filename = we_base_request::_(we_base_request::FILE, "exportfile"))){
			$filename = basename(urldecode($filename));

			if(file_exists(TEMP_PATH . $filename) // Does file exist?
				&& !preg_match('%p?html?%i', $filename) && stripos($filename, "inc") === false && !preg_match('%php3?%i', $filename)){ // Security check
				session_write_close();
				$size = filesize(TEMP_PATH . $filename);

				header("Pragma: public");
				header("Expires: 0");
				header("Cache-control: private, max-age=0, must-revalidate");

				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . trim(htmlentities($filename)) . '"');
				header("Content-Description: " . trim(htmlentities($filename)));
				header("Content-Length: " . $size);

				readfile(TEMP_PATH . $filename);
			} else {
				header("Location: " . WEBEDITION_DIR . 'we_showMod.php?mod=export&pnt=cmd&cmd=upload_failed');
			}
		} else {
			header("Location: " . WEBEDITION_DIR . 'we_showMod.php?mod=export&pnt=cmd&cmd=error=upload_failed');
		}
		exit();
	}

	protected function getHTMLCmd(){
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
				return we_message_reporting::jsMessagePush(g_l('export', '[error_download_failed]'), we_message_reporting::WE_MESSAGE_ERROR);
			default:
				return '';
		}
	}

	private function formWeChooser($table = FILE_TABLE, $width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = ''){
		$Pathvalue = ($Pathvalue ?: f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), "", $this->db));

		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:top.content.setHot();we_cmd('we_selector_directory',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $IDName . "','" . $Pathname . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");
		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('SelPath');
		$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$yuiSuggest->setInput($Pathname, $Pathvalue, array('onchange' => 'top.content.hot=1;'));
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
				if(($cat = we_base_request::_(we_base_request::INTLISTA, "cat", []))){
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
							unset($arr[$k]);
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
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:top.content.setHot(); we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','fillIDs();opener.top.content.editor.edbody.we_cmd(\\'add_cat\\',top.allIDs.join(\\',\\'));')");

		$cats = new we_chooser_multiDir(520, $this->View->export->Categorys, "del_cat", $delallbut . $addbut, "", '"we/category"', CATEGORY_TABLE);

		if(!permissionhandler::hasPerm("EDIT_KATEGORIE")){
			$cats->isEditable = false;
		}
		return $hiddens . we_html_tools::htmlFormElementTable($cats->get(), g_l('export', '[categories]'), "left", "defaultfont");
	}

}
