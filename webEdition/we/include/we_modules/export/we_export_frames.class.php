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
	protected $exportVars = []; // temporary

	const TAB_PROPERTIES = 1;
	const TAB_OPTIONS = 2;
	const TAB_LOG = 3;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->module = "export";

		$this->Tree = new we_export_treeMain($this->jsCmd);
		$this->SelectionTree = new we_export_tree($this->jsCmd);
		$this->View = new we_export_view();
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

		$we_tabs = new we_gui_tabs();
		$we_tabs->addTab('', we_base_constants::WE_ICON_PROPERTIES, false, self::TAB_PROPERTIES, ["id" => "tab_1", 'title' => g_l('export', '[property]')]);
		if($this->View->export->IsFolder == 0){
			$we_tabs->addTab(g_l('export', '[options]'), '', false, self::TAB_OPTIONS, ["id" => "tab_2"]);
			$we_tabs->addTab(g_l('export', '[log]'), '', false, self::TAB_LOG, ["id" => "tab_3"]);
		}

		$tabsHead = we_html_element::cssLink(CSS_DIR . 'we_tab.css') .
			we_html_element::jsScript(JS_DIR . 'initTabs.js') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . '/export/export_top.js');

		if(!$this->View->export->ID || $this->View->export->IsFolder){
			$this->jsCmd->addCmd('setTab', 1);
		}


		$table = new we_html_table(['style' => 'width:100%;margin-top:3px', 'class' => 'default'], 1, 1);

		$table->setCol(0, 0, ['class' => "small", 'style' => 'vertical-align:top;padding-left:15px;'], we_html_element::htmlB(g_l('export', '[export]') . ':&nbsp;' . $this->View->export->Text));
		$text = !empty($this->View->export->Path) ? $this->View->export->Path : "/" . $this->View->export->Text;

		//TODO: we have the following body in several modules!
		$body = we_html_element::htmlBody(['onresize' => 'weTabs.setFrameSize()', 'onload' => 'loaded();', 'id' => 'eHeaderBody'], we_html_element::htmlDiv([
					'id' => 'main'], we_html_element::htmlDiv(['id' => 'headrow'], we_html_element::htmlB(str_replace(" ", "&nbsp;", g_l('export', '[export]')) . ':&nbsp;') .
						we_html_element::htmlSpan(['id' => 'h_path', 'class' => 'header_small'], '<b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b>'
						)
					) .
					$we_tabs->getHTML()
				)
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){
		$hiddens = ['cmd' => 'export_edit', 'pnt' => 'edbody'];

		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->View->getHomeScreen();
		}

		$weSuggest = & we_gui_suggest::getInstance();
		//FIXME: folder don't have a tree to start.
		$body = we_html_element::htmlBody(['class' => 'weEditorBody', "onload" => "doOnload()", "onunload" => "doUnload();"], we_html_element::htmlForm([
					'name' => 'we_form'], $this->View->getCommonHiddens($hiddens) . $this->getHTMLProperties())
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
		$table2->setColContent(0, $col++, we_html_button::create_button(we_html_button::SAVE, "javascript:top.content.we_cmd('save_export');"));

		if($this->View->export->IsFolder == 0){
			$table2->setColContent(0, $col++, we_html_button::create_button(we_html_button::EXPORT, "javascript:top.content.we_cmd('start_export')", '', 0, 0, '', '', !we_base_permission::hasPerm('MAKE_EXPORT'))
			);
		}

		$table2->setColContent(0, $col++, we_html_button::create_button(we_html_button::DELETE, "javascript:top.content.we_cmd('delete_export')", '', 0, 0, '', '', !we_base_permission::hasPerm('DELETE_EXPORT')));

		$text = we_base_request::_(we_base_request::STRING, "current_description", g_l('export', '[working]'));
		$progress = we_base_request::_(we_base_request::INT, "percent", 0);

		$progressbar = new we_gui_progressBar($progress, 200);
		$progressbar->addText($text, we_gui_progressBar::TOP, "current_description");

		$table2->setCol(0, $col++, ["id" => "progress"], $progressbar->getHtml('', 'display: none'));

		return $this->getHTMLDocument(
				we_html_element::htmlBody(['id' => 'footerBody'], we_html_element::htmlForm([], $table2->getHtml())
				), (isset($progressbar) ? we_gui_progressBar::getJSCode() : "")
		);
	}

	function getHTMLProperties($preselect = ""){
		$tabNr = we_base_request::_(we_base_request::INT, "tabnr", 1);

		return we_html_element::htmlDiv(['id' => 'tab1', 'style' => ($tabNr == self::TAB_PROPERTIES ? '' : 'display: none')], we_html_multiIconBox::getHTML('', $this->getHTMLTab1(), 30, '', -1, '', '', false, $preselect)) .
			we_html_element::htmlDiv(['id' => 'tab2', 'style' => ($tabNr == self::TAB_OPTIONS ? '' : 'display: none')], $this->getHTMLTab2($preselect)) .
			we_html_element::htmlDiv(['id' => 'tab3', 'style' => ($tabNr == self::TAB_LOG ? '' : 'display: none')], we_html_multiIconBox::getHTML('', $this->getHTMLTab3(), 30, '', -1, '', '', false, $preselect));
	}

	private function getHTMLTab1(){
		$parts = [
			[
				'headline' => g_l('export', '[property]'),
				'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Text_visible", '', str_replace($this->View->export->Extension, '', $this->View->export->Text), '', 'style="width: 490px;" id="yuiAcInputPathName" onchange="we_cmd(\'setHot\');" onblur="we_cmd(\'setHeaderTitlePath\', this.value);"') . '<span class="exportExtension"> ' . $this->View->export->Extension . '</span>', g_l('export', '[name]')) .
				we_html_element::htmlHiddens(['Extension' => $this->View->export->Extension,
					'Text' => str_replace($this->View->export->Extension, '', $this->View->export->Text)]) .
				'<br/>' . $this->getHTMLDirChooser(),
				'space' => we_html_multiIconBox::SPACE_MED]
		];

		if($this->View->export->IsFolder == 1){
			return $parts;
		}

		$permittedExportTypes = self::getPermittedExportTypes();
		$exportTypes = array_filter([
			we_exim_ExIm::TYPE_WE => (in_array(we_exim_ExIm::TYPE_WE, $permittedExportTypes) ? g_l('export', '[wxml_export]') : false),
			we_exim_ExIm::TYPE_XML => (in_array(we_exim_ExIm::TYPE_XML, $permittedExportTypes) ? g_l('export', '[gxml_export]') : false),
			we_exim_ExIm::TYPE_CSV => (in_array(we_exim_ExIm::TYPE_CSV, $permittedExportTypes) ? g_l('export', '[csv_export]') : false),
		]);

		$parts[] = [
			'headline' => g_l('export', '[export_to]'),
			'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect('ExportType', $exportTypes, 1, $this->View->export->ExportType, false, ['onchange' => "we_cmd('switch_type', this);"], 'value', 520), g_l('export', '[file_format]')
			),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		];

		$parts[] = [
			'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Filename", 75, $this->View->export->Filename, '', 'style="width: 490px;" onchange="we_cmd(\'setHot\');""') . '<span class="exportExtension"> ' . $this->View->export->Extension . '</span>', g_l('export', '[filename]')),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		];

		$table = new we_html_table(['class' => 'default withSpace'], 3, 1);
		$table->setColContent(0, 0, we_html_tools::htmlSelect('ExportTo', ['local' => g_l('export', '[export_to_local]'), "server" => g_l('export', '[export_to_server]')], 1, $this->View->export->ExportTo, false, [
				'onchange' => 'we_cmd(\'toggle\', \'save_to\');'], 'value', 520));
		$table->setCol(1, 0, ["id" => "save_to", 'style' => ($this->View->export->ExportTo === 'server' ? 'display:block' : 'display: none')], we_html_tools::htmlFormElementTable($this->formFileChooser(400, "ServerPath", $this->View->export->ServerPath, "", we_base_ContentTypes::FOLDER), g_l('export', '[save_to]')));


		$parts[] = ['headline' => '',
			'html' => $table->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED
		];

		$dtq = we_docTypes::getDoctypeQuery($this->db);
		$this->db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		$docTypes = $this->db->getAllFirst(false);

		if(defined('OBJECT_TABLE')){
			$this->db->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ORDER BY Text');
			$classNames = $this->db->getAllFirst(false);
		}

		$FolderPath = $this->View->export->Folder ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->View->export->Folder), '', $this->db) : "/";

		$table = new we_html_table(['class' => 'default'], 5, 1);

		$this->View->export->SelectionType = $this->View->export->ExportType === we_exim_ExIm::TYPE_CSV && !we_exim_Export::ENABLE_DOCUMENTS2CSV ? 'classname' : $this->View->export->SelectionType;

		$selectorAttribs = ['name' => 'SelectionType',
			'onchange' => "toggleSelectionType(this.value);",
			'style' => 'width:520px;',
			'disabled' => $this->View->export->ExportType === we_exim_ExIm::TYPE_CSV && !we_exim_Export::ENABLE_DOCUMENTS2CSV ? 'disabled' : false
		];

		$selectionType = new we_html_select(array_filter($selectorAttribs));
		$selectionType->addOption(FILE_TABLE, g_l('export', '[documents]'));
		$selectionType->addOption('doctype', g_l('export', '[doctypename]'));
		if(defined('OBJECT_TABLE')){
			$selectionType->addOption('classname', g_l('export', '[classname]'), ($this->View->export->ExportType === we_exim_ExIm::TYPE_CSV && !we_exim_Export::ENABLE_DOCUMENTS2CSV ? [
					'selected' => 'selected'] : []));
		}
		$selectionType->selectOption($this->View->export->SelectionType);
		$table->setCol(0, 0, ['style' => 'padding-bottom:5px;'], $selectionType->getHtml());

		$table->setCol(1, 0, ['class' => 'selectionTypes doctype', 'style' => ($this->View->export->SelectionType === 'doctype' ? 'display:block' : 'display: none')], we_html_tools::htmlSelect('DocType', $docTypes, 1, $this->View->export->DocType, false, [
				'onchange' => "we_cmd('setHot');"], 'value', 520)
		);

		$table->setCol(2, 0, ['class' => 'selectionTypes doctype document', 'style' => ($this->View->export->SelectionType !== 'classname' ? 'display:block' : 'display: none')], we_html_tools::htmlFormElementTable($this->formWeChooser(FILE_TABLE, 400, 0, 'Folder', $this->View->export->Folder, 'FolderPath', $FolderPath), g_l('export', '[dir]'))
		);

		if(defined('OBJECT_TABLE')){
			$table->setCol(3, 0, ['class' => 'selectionTypes classname', 'style' => ($this->View->export->SelectionType === "classname" ? "display:block" : "display: none")], we_html_tools::htmlSelect('ClassName', $classNames, 1, $this->View->export->ClassName, false, [
					'onchange' => "we_cmd('setHot');"], 'value', 520)
			);
		}

		$table->setColContent(4, 0, $this->getHTMLCategory());
		$selectionTypeHtml = $table->getHTML();

		$table = new we_html_table(['class' => 'default'], 3, 1);
		$table->setCol(0, 0, ['style' => 'padding-bottom:5px;'], we_html_tools::htmlSelect('Selection', ['auto' => g_l('export', '[auto_selection]'), "manual" => g_l('export', '[manual_selection]')], 1, $this->View->export->Selection, false, [
				'onchange' => 'we_cmd(\'toggle_selection\', this.value);'], 'value', 520));
		$table->setCol(1, 0, ['id' => 'auto', 'style' => ($this->View->export->Selection === 'auto' ? 'display:block' : 'display: none')], we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_auto_selection]'), we_html_tools::TYPE_INFO, 520) .
			$selectionTypeHtml
		);

		switch($this->View->export->ExportType){
			case we_exim_ExIm::TYPE_CSV:
				$selected = !we_exim_Export::ENABLE_DOCUMENTS2CSV ? OBJECT_FILES_TABLE : addTblPrefix($this->View->export->XMLTable);
				break;
			case we_exim_ExIm::TYPE_XML:
				$selected = addTblPrefix($this->View->export->XMLTable);
				break;
			default:
				$selected = FILE_TABLE;
		}

		$table->setCol(2, 0, ['id' => 'manual', 'style' => ($this->View->export->Selection === 'manual' ? "display:block" : "display: none")], we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_manual_selection]') . " " . g_l('export', '[select_export]'), we_html_tools::TYPE_INFO, 520) .
			$this->SelectionTree->getHTMLMultiExplorer(520, 200, true, $selected, $this->View->export->ExportType) . we_html_element::htmlHidden('XMLTable', $this->View->export->XMLTable ?: stripTblPrefix(FILE_TABLE))
		);

		$parts[] = ["headline" => g_l('export', '[selection]'),
			"html" => $table->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED
		];

		return $parts;
	}

	private function getHTMLTab2($preselect = ''){
		$optionsWE = we_html_multiIconBox::getHTML('', $this->getHTMLOptionsWE(), 30, '', -1, '', '', false, $preselect);
		$optionsXML = we_html_multiIconBox::getHTML('', $this->getHTMLOptionsXML(), 30, '', -1, '', '', false, $preselect);
		$optionsCSV = we_html_multiIconBox::getHTML('', $this->getHTMLOptionsCSV(), 30, '', -1, '', '', false, $preselect);

		return we_html_element::htmlDiv(['id' => 'optionsWXML', 'class' => 'exportOptions', 'style' => 'display: ' . ($this->View->export->ExportType === we_exim_ExIm::TYPE_WE ? 'block' : 'none') . ';'], $optionsWE) .
			we_html_element::htmlDiv(['id' => 'optionsGXML', 'class' => 'exportOptions', 'style' => 'display: ' . ($this->View->export->ExportType === we_exim_ExIm::TYPE_XML ? 'block' : 'none') . ';'], $optionsXML) .
			we_html_element::htmlDiv(['id' => 'optionsCSV', 'class' => 'exportOptions', 'style' => 'display: ' . ($this->View->export->ExportType === we_exim_ExIm::TYPE_CSV ? 'block' : 'none') . ';'], $optionsCSV);
	}

	private function getHTMLOptionsWE(){
		$formattable = new we_html_table([], 5, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden($this->View->export->HandleDefTemplates, "HandleDefTemplates", g_l('export', '[handle_def_templates]'), false, 'defaultfont', "we_cmd('setHot');"));
		$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleDocIncludes ? true : false), "HandleDocIncludes", g_l('export', '[handle_document_includes]'), false, 'defaultfont', "we_cmd('setHot');"));
		if(defined('OBJECT_TABLE')){
			$formattable->setCol(2, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleObjIncludes ? true : false), "HandleObjIncludes", g_l('export', '[handle_object_includes]'), false, 'defaultfont', "we_cmd('setHot');"));
		}
		$formattable->setCol(3, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleDocLinked ? true : false), "HandleDocLinked", g_l('export', '[handle_document_linked]'), false, 'defaultfont', "we_cmd('setHot');"));
		$formattable->setCol(4, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleThumbnails ? true : false), "HandleThumbnails", g_l('export', '[handle_thumbnails]'), false, 'defaultfont', "we_cmd('setHot');"));

		$parts = [["headline" => g_l('export', '[handle_document_options]') . we_html_element::htmlBr() . g_l('export', '[handle_template_options]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_document_options]'), we_html_tools::TYPE_INFO, 520, true, 60) . $formattable->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED],
		];

		if(defined('OBJECT_TABLE')){
			$formattable = new we_html_table([], 3, 1);
			$formattable->setCol(0, 0, ["colspan" => 2], we_html_forms::checkboxWithHidden(($this->View->export->HandleDefClasses ? true : false), "HandleDefClasses", g_l('export', '[handle_def_classes]'), false, 'defaultfont', "we_cmd('setHot');"));
			$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleObjEmbeds ? true : false), "HandleObjEmbeds", g_l('export', '[handle_object_embeds]'), false, 'defaultfont', "we_cmd('setHot');"));
			$parts[] = ["headline" => g_l('export', '[handle_object_options]') . we_html_element::htmlBr() . g_l('export', '[handle_classes_options]'),
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_object_options]'), we_html_tools::TYPE_INFO, 520, true, 60) . $formattable->getHtml(),
				'space' => we_html_multiIconBox::SPACE_MED
			];
		}

		$formattable = new we_html_table([], 3, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleDoctypes ? true : false), "HandleDoctypes", g_l('export', '[handle_doctypes]'), false, 'defaultfont', "we_cmd('setHot');"));
		$formattable->setCol(1, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleCategorys ? true : false), "HandleCategorys", g_l('export', '[handle_categorys]'), false, 'defaultfont', "we_cmd('setHot');"));
		$formattable->setCol(2, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleNavigation ? true : false), "HandleNavigation", g_l('export', '[handle_navigation]'), false, 'defaultfont', "we_cmd('setHot');", false, g_l('export', '[navigation_hint]'), we_html_tools::TYPE_HELP, false));

		$parts[] = ["headline" => g_l('export', '[handle_doctype_options]'),
			"html" => $formattable->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED
		];

		$parts[] = ["headline" => g_l('export', '[export_depth]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_exportdeep_options]'), we_html_tools::TYPE_INFO, 520) . '<br/>' . we_html_element::htmlLabel([
				'style' => 'padding-right:5px;'], g_l('export', '[to_level]')) . we_html_tools::htmlTextInput("ExportDepth", 10, $this->View->export->ExportDepth, "", 'onBlur="we_cmd(\'setExportDepth\', this.value, ' . intval($this->View->export->ExportDepth) . ')"', "text", 50),
			'space' => we_html_multiIconBox::SPACE_MED
		];

		$formattable = new we_html_table([], 1, 1);
		$formattable->setCol(0, 0, null, we_html_forms::checkboxWithHidden(($this->View->export->HandleOwners ? true : false), "HandleOwners", g_l('export', '[handle_owners]'), false, 'defaultfont', "we_cmd('setHot');"));

		$parts[] = ["headline" => g_l('export', '[handle_owners_option]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_owners]'), we_html_tools::TYPE_INFO, 520) . $formattable->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED
		];

		return $parts;
	}

	private function getHTMLOptionsXML(){
		$table = new we_html_table(['class' => 'default withSpace'], 2, 1);
		$table->setColContent(0, 0, we_html_forms::radiobutton(1, (intval($this->View->export->XMLCdata) === 1), 'XMLCdata', g_l('export', '[export_xml_cdata]'), true, 'defaultfont'));
		$table->setColContent(1, 0, we_html_forms::radiobutton(0, (intval($this->View->export->XMLCdata) === 0), 'XMLCdata', g_l('export', '[export_xml_entities]'), true, 'defaultfont'));

		return [['headline' => g_l('export', '[cdata]'),
			'html' => $table->getHtml(),
			'space' => we_html_multiIconBox::SPACE_MED
		]];
	}

	private function getHTMLOptionsCSV(){
		$fileformattable = new we_html_table([], 4, 1);
		$file_encoding = new we_html_select(['name' => 'CSVLineend', 'class' => 'weSelect', 'style' => 'width: 254px;']);
		$file_encoding->addOption("windows", g_l('export', '[windows]'));
		$file_encoding->addOption("unix", g_l('export', '[unix]'));
		$file_encoding->addOption("mac", g_l('export', '[mac]'));
		$file_encoding->selectOption($this->View->export->CSVLineend);
		$fileformattable->setCol(0, 0, ['class' => 'defaultfont'], g_l('export', '[csv_lineend]') . '<br/>' . $file_encoding->getHtml());
		$fileformattable->setColContent(1, 0, $this->getHTMLChooser('CSVDelimiter', ($this->View->export->CSVDelimiter ?: 'comma'), ['semicolon' => g_l('export', '[semicolon]'),
				'comma' => g_l('export', '[comma]'),
				'colon' => g_l('export', '[colon]'),
				'tab' => g_l('export', '[tab]'),
				'space' => g_l('export', '[space]')
				], g_l('export', '[csv_delimiter]')));
		$fileformattable->setColContent(2, 0, $this->getHTMLChooser("CSVEnclose", $this->View->export->CSVEnclose, ["doublequote" => g_l('export', '[double_quote]'),
				"singlequote" => g_l('export', '[single_quote]')
				], g_l('export', '[csv_enclose]')));
		$fileformattable->setColContent(3, 0, we_html_forms::checkboxWithHidden(($this->View->export->CSVFieldnames ? true : false), 'CSVFieldnames', g_l('export', '[csv_fieldnames]'), false, 'defaultfont'));

		return [['headline' => g_l('export', '[csv_params]'), 'html' => $fileformattable->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED]];
	}

	private function getHTMLTab3(){
		return [
			[
				'headline' => '',
				'html' => we_html_element::htmlDiv(['class' => 'blockWrapper', 'style' => 'padding:12px 0 0 12px;width: 650px; height: 400px; border:1px #dce6f2 solid;', 'id' => 'log'], ''),
			]
		];
	}

	private function getHTMLDirChooser(){
		$path = id_to_path($this->View->export->ParentID, EXPORT_TABLE);

		$weSuggest = & we_gui_suggest::getInstance();
		$weSuggest->setAcId("PathGroup");
		$weSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$weSuggest->setInput("ParentPath", $path, [], false, true);
		$weSuggest->setLabel(g_l('export', '[group]'));
		$weSuggest->setMaxResults(10);
		$weSuggest->setResult("ParentID", $this->View->export->ParentID);
		$weSuggest->setSelector(we_gui_suggest::DirSelector);
		$weSuggest->setTable(EXPORT_TABLE);
		$weSuggest->setWidth(400);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_export_dirSelector',document.we_form.elements.ParentID.value,'ParentID','ParentPath','setHot')"));

		return $weSuggest->getHTML();
	}

	private function getHTMLChooser($name, $value, $values, $title){
		$input_size = 5;

		$select = new we_html_select(['name' => $name . '_select', 'class' => 'weSelect', 'onchange' => "we_cmd('inputChooser_syncChoice', this, '" . $name . "')",
			'style' => 'width:200;']);
		$select->addOption('', '');
		foreach($values as $k => $v){
			$select->addOption(oldHtmlspecialchars($k), oldHtmlspecialchars($v));
		}

		$table = new we_html_table(['class' => 'default', "width" => 250], 1, 2);

		$table->setColContent(0, 0, we_html_tools::htmlTextInput($name, $input_size, $value) . '  ');
		$table->setColContent(0, 1, $select->getHtml());

		return we_html_tools::htmlFormElementTable($table->getHtml(), $title);
	}

	private function getLoadCode(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) !== false){

			$table = $this->View->export->ExportType === we_exim_ExIm::TYPE_CSV ? OBJECT_FILES_TABLE : we_base_request::_(we_base_request::TABLE, "tab");
			$this->jsCmd->addCmd('location', ['doc' => 'document', 'loc' => WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=loadTree&we_cmd[1]=' . $table . '&we_cmd[2]=' . $pid . '&we_cmd[3]=' . we_base_request::_(we_base_request::INTLIST, "openFolders", "")]);

			return $this->getHTMLDocument(we_html_element::htmlBody());
		}
		return '';
	}

	private function getMainLoadCode(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) !== false){
			$this->jsCmd->addCmd('loadTree', ['clear' => !$pid, 'items' => we_export_treeMain::getItems($pid)]);

			return $this->getHTMLDocument(we_html_element::htmlBody());
		}
		return '';
	}

	public static function getPermittedExportTypes(){
		return array_filter([
			(we_base_permission::hasPerm(['NEW_EXPORT', 'DELETE_EXPORT', 'EDIT_EXPORT', 'MAKE_EXPORT']) ? we_exim_ExIm::TYPE_WE : false),
			(we_base_permission::hasPerm(['GENERICXML_EXPORT']) ? we_exim_ExIm::TYPE_XML : false),
			(we_base_permission::hasPerm(['CSV_EXPORT']) ? we_exim_ExIm::TYPE_CSV : false)
		]);
	}

	private function getDoExportCode(){
		if(!in_array($this->View->export->ExportType, self::getPermittedExportTypes())){
			$jsCmd = new we_base_jsCmd();
			$jsCmd->addMsg(g_l('export', '[no_perms]'), we_base_util::WE_MESSAGE_ERROR);
			return we_html_tools::getHtmlTop('', '', '', $jsCmd->getCmds(), we_html_element::htmlBody());
		}

		$progress_update = [
			'log' => [],
			'text' => '',
			'percent' => 0
		];

		$bodyContent = we_html_element::htmlForm(['name' => 'we_form',
				"method" => "post",
				"action" => $this->frameset], we_html_element::htmlHiddens(['pnt' => 'cmd', 'cmd' => 'do_export'])
		);

		switch($this->View->export->ExportType){
			case we_exim_ExIm::TYPE_CSV:
				$exporter = new we_exim_ExportCSV();
				break;
			case we_exim_ExIm::TYPE_XML:
				$exporter = new we_exim_ExportXML();
				break;
			default:
				$exporter = new we_exim_ExportWE();
		}
		$exporter->loadPreserves();

		switch($exporter->getNextTask()){
			case we_exim_Export::EXPORT_PRE_PROCESS:
				$exporter->exportPreprocess($this->View->export);

				$progress_update['log'][] = we_html_element::htmlB(g_l('export', '[start_export]') . ' - ' . date("d.m.Y H:i:s"));
				$progress_update['log'][] = we_html_element::htmlB(g_l('export', '[prepare]') . '<br/>');
				$progress_update['log'][] = we_html_element::htmlB(g_l('export', '[export]'));
				$progress_update['percent'] = 0;
				$progress_update['text'] = g_l('export', '[working]');

				$this->jsCmd->addCmd('updateLog', $progress_update);
				$this->jsCmd->addCmd('submitCmdForm');
				break;
			case we_exim_Export::EXPORT_PROCESS_NEXT:
				$lastExported = $exporter->exportNext();

				if($lastExported['success']){
					switch($lastExported['contentType']){
						case 'weBinary':
							$progress_update['log'][] = we_html_element::htmlB(g_l('export', '[weBinary]')) . '&nbsp;&nbsp;' . $lastExported['id'];
							$proceed = false;
							break;
						case 'doctype':
							$path = f('SELECT DocType FROM ' . $lastExported['table'] . ' WHERE ID=' . intval($lastExported['id']), '', $this->db);
							$proceed = true;
							break;
						case we_base_ContentTypes::NAVIGATIONRULE:
							$path = f('SELECT NavigationName FROM ' . $lastExported['table'] . ' WHERE ID=' . intval($lastExported['id']), '', $this->db);
							$proceed = true;
							break;
						case 'weThumbnail':
							$path = f('SELECT Name FROM ' . $lastExported['table'] . ' WHERE ID=' . intval($lastExported['id']), '', $this->db);
							$proceed = true;
							break;
						default:
							$path = id_to_path($lastExported['id'], $lastExported['table']);
							$proceed = true;
							break;
					}

					if($proceed){
						$progress_text = we_html_element::htmlB(g_l('contentTypes', '[' . $lastExported['contentType'] . ']', true) !== false ? g_l('contentTypes', '[' . $lastExported['contentType'] . ']') : (g_l('export', '[' . $lastExported['contentType'] . ']', true) !== false ? g_l('export', '[' . $lastExported['contentType'] . ']') : '')) . '&nbsp;&nbsp;' . $path;
						if(strlen($path) > 75){
							$progress_text = addslashes(substr($progress_text, 0, 65) . '<abbr title="' . $path . '">...</abbr>' . substr($progress_text, -10));
						}
						$progress_update['log'][] = $progress_text;
					}

					$percent = round(max(min(($lastExported['total'] ? intval(($lastExported['current'] / $lastExported['total']) * 100) : 0), 100), 0), 2);
					$progress_update['percent'] = $percent;

					$this->jsCmd->addCmd('updateLog', $progress_update);
					$this->jsCmd->addCmd('submitCmdForm');
				}
				break;
			case we_exim_Export::EXPORT_POST_PROCESS:
				$exporter->exportPostprocess();

				if($this->View->export->ExportTo === 'local'){
					$progress_update['log'][] = we_html_element::htmlB(we_html_element::htmlSpan(['class' => 'defaultfont'], '<br/>' . g_l('export', '[backup_finished]') . ' - ' . date("d.m.Y H:i:s") . "<br/>" .
								g_l('export', '[download_starting2]') . "<br/><br/>" .
								g_l('export', '[download_starting3]') . "<br/>" .
								we_html_element::htmlB(we_html_element::htmlA(["href" => WEBEDITION_DIR . 'we_showMod.php?mod=export&pnt=cmd&cmd=upload&exportfile=' . urlencode($this->View->export->ExportFilename),
										'download' => $this->View->export->ExportFilename], g_l('export', '[download]'))) . "<br/><br/>"
					));
				} else {
					$progress_update['log'][] = we_html_element::htmlB('<br/>' . g_l('export', '[end_export]') . ' - ' . date("d.m.Y H:i:s"));
				}
				$progress_update['percent'] = 100;

				$this->jsCmd->addCmd('updateLog', $progress_update);
				if($this->View->export->ExportTo === 'local'){
					$this->jsCmd->addCmd('startDownload', $this->View->export->ExportFilename);
				}
				$this->jsCmd->addCmd('setStatusEnd');

				$bodyContent = '';
				break;
		}

		return we_html_tools::getHtmlTop('', '', '', $this->jsCmd->getCmds(), we_html_element::htmlBody(['onload' => $bodyOnload], $bodyContent));
	}

	private function getUploadCode(){
		if(($filename = we_base_request::_(we_base_request::FILE, "exportfile"))){
			$filename = basename(urldecode($filename));

			if(file_exists(TEMP_PATH . $filename) // Does file exist?
				&& !preg_match('%p?html?%i', $filename) && stripos($filename, "inc") === false && !preg_match('%php%i', $filename)){ // Security check
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
				$jsCmd = new we_base_jsCmd();
				$jsCmd->addMsg(g_l('export', '[error_download_failed]'), we_base_util::WE_MESSAGE_ERROR);
				return we_html_tools::getHtmlTop('', '', '', $jsCmd->getCmds(), we_html_element::htmlBody());
			default:
				return '';
		}
	}

	private function formWeChooser($table = FILE_TABLE, $width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/'){
		$Pathvalue = ($Pathvalue ?: f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), "", $this->db));

		$weSuggest = & we_gui_suggest::getInstance();
		$weSuggest->setAcId('SelPath');
		$weSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$weSuggest->setInput($Pathname, $Pathvalue, [], false, true);
		$weSuggest->setMaxResults(10);
		$weSuggest->setResult($IDName, $IDValue);
		$weSuggest->setSelector(we_gui_suggest::DirSelector);
		$weSuggest->setTable(FILE_TABLE);
		$weSuggest->setWidth($width);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $IDName . "','" . $Pathname . "','','','" . $rootDirID . "')"));

		return $weSuggest->getHTML();
	}

	private function getHTMLCategory(){
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



		$hiddens = we_html_element::htmlHiddens(["Categorys" => $this->View->export->Categorys,
				"cat" => we_base_request::_(we_base_request::STRING, 'cat', "")]);


		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_cats')", '', 0, 0, "", "", (isset($this->View->export->Categorys) ? false : true));
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','add_cat')");

		$cats = new we_chooser_multiDir(520, $this->View->export->Categorys, "del_cat", $delallbut . $addbut, "", '"we/category"', CATEGORY_TABLE);

		if(!we_base_permission::hasPerm("EDIT_KATEGORIE")){
			$cats->isEditable = false;
		}
		return $hiddens . we_html_tools::htmlFormElementTable($cats->get($this->jsCmd), g_l('export', '[categories]'), "left", "defaultfont");
	}

}
