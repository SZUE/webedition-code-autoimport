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
class we_navigation_frames extends we_modules_frame{
	var $Model;
	public $Table = NAVIGATION_TABLE;

	const TAB_PROPERTIES = '1'; //make sure to keep 1
	const TAB_CONTENT = '2';
	const TAB_CUSTOMER = '3';
	const TAB_PREVIEW = 'preview';

	function __construct($frameset){
		$frameset = WEBEDITION_DIR . 'we_showMod.php?mod=navigation';
		parent::__construct($frameset);
		$this->module = 'navigation';
		$this->treeDefaultWidth = 220;
		$this->showTreeFooter = true;

		$this->Tree = new we_navigation_tree($frameset, 'top.content', 'top.content', 'top.content.cmd');
		$this->View = new we_navigation_view($frameset);
		$this->Model = &$this->View->Model;
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case 'preview' :
				return $this->getHTMLEditorBody();
			case 'previewIframe' :
				return $this->getHTMLEditorPreviewIframe();
			case 'fields' :
				return $this->getHTMLFieldSelector();
			case 'dyn_preview' :
				return $this->getHTMLDynPreview();
			case 'frameset':
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode(), (($tab = we_base_request::_(we_base_request::STRING, 'tab')) !== false ? '&tab=' . $tab : '' ) . (($sid = we_base_request::_(we_base_request::STRING, 'sid', false)) !== false ? '&sid=' . $sid : ''));
			default :
				return parent::getHTML($what, $mode, $step);
		}
	}

	function getHTMLSearch(){
		$keyword = we_base_request::_(we_base_request::RAW, 'keyword', "");
		$arr = explode(' ', strToLower($keyword));
		$DB_WE = $GLOBALS['DB_WE'];
		$aWsQuery = [];

		if(($ws = get_ws(NAVIGATION_TABLE, true))){
			$wsPathArray = id_to_path($ws, NAVIGATION_TABLE, $DB_WE, true);
			foreach($wsPathArray as $path){
				$aWsQuery[] = ' Path LIKE "' . $DB_WE->escape($path) . '/%" OR ' . we_tool_treeDataSource::getQueryParents($path);
			}
		}

		$condition = ($aWsQuery ? '(' . implode(' OR ', $aWsQuery) . ')' : '');
		foreach($arr as $value){
			$value = $DB_WE->escape($value);
			$condition .= ($condition ? ' AND ' : '') .
				'(Path LIKE "%' . $value . '%" OR Text LIKE "%' . $value . '%" OR Display LIKE "%' . $value . '%")';
		}

		$DB_WE->query('SELECT ID,Path FROM ' . NAVIGATION_TABLE . ($condition ? ' WHERE ' . $condition : '') . ' ORDER BY Path');

		$select = '<div style="background-color:white;width:520px;height:220px;"/>';
		if($DB_WE->num_rows()){
			$select = '<select name="search_results" size="20" style="width:520px;height:220px;" ondblclick="top.opener.top.we_cmd(\'module_navigation_edit\',document.we_form.search_results.value); top.close();">';
			while($DB_WE->next_record(MYSQL_NUM)){
				$select .= '<option value="' . $DB_WE->f(0) . '">' . $DB_WE->f(1) . '</option>';
			}
			$select .= '</select>';
		}

		$buttons = we_html_button::position_yes_no_cancel(
				we_html_button::create_button(we_html_button::EDIT, "javascript:top.opener.top.we_cmd('module_navigation_edit',document.we_form.search_results.value); if(document.we_form.search_results.value){top.close()}"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
		);

		$content = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('keyword', 24, $keyword, '', '', 'text', 485), g_l('modules_users', '[search_for]'), 'left', 'defaultfont', we_html_button::create_button(we_html_button::SEARCH, "javascript:document.we_form.submit();")
			) . '<div style="height:20px;"></div>' .
			we_html_tools::htmlFormElementTable($select, g_l('modules_users', '[search_result]'));

		return $this->getHTMLDocument(we_html_element::htmlBody(['class' => 'weEditorBody', 'style' => 'margin:10px 20px;'], we_html_element::htmlForm(['name' => 'we_form',
						'method' => 'post'], we_html_element::htmlHiddens([
							'mod' => 'navigation',
							'pnt' => 'search']) .
						we_html_tools::htmlDialogLayout($content, g_l('modules_users', '[search]'), $buttons))
		));
	}

	protected function getHTMLTreeFooter(){
		return '<div id="infoField" class="defaultfont" style="display:none;"></div>' . $this->getHTMLSearchTreeFooter();
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::INT, 'pid')) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);

		return $this->getHTMLDocument(we_html_element::htmlBody([], we_html_element::htmlForm(['name' => 'we_form'], we_html_element::htmlHiddens([
							'pnt' => 'cmd',
							'cmd' => 'no_cmd'])
					)
				), we_base_jsCmd::singleCmd('loadTree', ['pid' => intval($pid), 'items' => we_navigation_tree::getItems($pid, $offset, $this->Tree->default_segment)])
		);
	}

	public function getHTMLDocumentHeader($charset = ''){
		return parent::getHTMLDocumentHeader($this->Model->Charset);
	}

	/**
	 * Frame for tabs
	 *
	 * @return string
	 */
	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return parent::getHTMLEditorHeader(0);
		}

		$we_tabs = new we_tabs();

		$we_tabs->addTab(we_base_constants::WE_ICON_PROPERTIES, false, 'setTab(' . self::TAB_PROPERTIES . ');', ['id' => 'tab_' . self::TAB_PROPERTIES, 'title' => g_l('navigation', '[property]')]);
		if($this->Model->IsFolder && permissionhandler::hasPerm('EDIT_DYNAMIC_NAVIGATION')){
			$we_tabs->addTab(we_base_constants::WE_ICON_CONTENT, false, 'setTab(' . self::TAB_CONTENT . ');', ['id' => 'tab_' . self::TAB_CONTENT, 'title' => g_l('navigation', '[content]')]);
		}

		if(defined('CUSTOMER_TABLE') && permissionhandler::hasPerm("CAN_EDIT_CUSTOMERFILTER")){
			$we_tabs->addTab(we_base_constants::WE_ICON_CUSTOMER_FILTER, false, 'setTab(' . self::TAB_CUSTOMER . ');', ['id' => 'tab_' . self::TAB_CUSTOMER, 'title' => g_l('navigation', '[customers]')]);
		}

		if($this->Model->IsFolder){
			$we_tabs->addTab(we_base_constants::WE_ICON_PREVIEW, false, "setTab('" . self::TAB_PREVIEW . "');", ['id' => 'tab_' . self::TAB_PREVIEW, 'title' => g_l('navigation', '[preview]')]);
		}

		$tabsHead = we_tabs::getHeader(
				($this->Model->ID ? '' : 'top.content.activ_tab=' . self::TAB_PROPERTIES . ';') .
				($this->Model->IsFolder == 0 ? '
if(top.content.activ_tab!=' . self::TAB_PROPERTIES . ' && top.content.activ_tab!=' . self::TAB_CUSTOMER . ') {
	top.content.activ_tab=' . self::TAB_PROPERTIES . ';
}' : ''
				) . '
function setTab(tab) {
	switch (tab) {
		case "' . self::TAB_PREVIEW . '":	// submit the information to preview screen
			parent.edbody.document.we_form.cmd.value="";
			if (top.content.activ_tab != tab || (top.content.activ_tab=="' . self::TAB_PREVIEW . '" && tab=="' . self::TAB_PREVIEW . '")) {
				parent.edbody.document.we_form.pnt.value = "' . self::TAB_PREVIEW . '";
				parent.edbody.document.we_form.tabnr.value = "' . self::TAB_PREVIEW . '";
				parent.edbody.submitForm();
			}
		break;

		default: // just toggle content to show
			if (top.content.activ_tab!="' . self::TAB_PREVIEW . '") {
				parent.edbody.toggle("tab"+top.content.activ_tab);
				parent.edbody.toggle("tab"+tab);
				top.content.activ_tab=tab;
				self.focus();
			} else {
				parent.edbody.document.we_form.pnt.value = "edbody";
				parent.edbody.document.we_form.tabnr.value = tab;
				parent.edbody.submitForm();
			}
		break;
	}
	self.focus();
	top.content.activ_tab=tab;
}');

		return $this->getHTMLDocument(we_html_element::htmlBody([
					"id" => "eHeaderBody",
					"onload" => "initNavHeader();",
					"onresize" => "weTabs.setFrameSize()"
					], we_html_element::htmlDiv(['id' => "main"], we_html_element::htmlDiv(['id' => 'headrow'], '&nbsp;' .
							we_html_element::htmlB(g_l('navigation', ($this->Model->IsFolder ? '[group]' : '[entry]')) . ':&nbsp;' .
								str_replace('&amp;', '&', $this->Model->Text) .
								we_html_element::htmlDiv(['id' => 'mark', 'style' => 'display: none;'], '*'))) .
						$we_tabs->getHTML() . '</div>')
				), $tabsHead . we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_frame.js'));
	}

	protected function getHTMLEditorBody(){
		$hiddens = ['cmd' => 'tool_' . $this->module . '_edit', 'pnt' => 'edbody', 'vernr' => we_base_request::_(we_base_request::INT, 'vernr', 0)];

		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->View->getHomeScreen();
		}

		return $this->getHTMLDocument(we_html_element::htmlBody(['class' => 'weEditorBody', 'onload' => 'loaded=1;'], we_html_element::htmlForm(['name' => 'we_form', 'onsubmit' => 'return false'], $this->getHTMLProperties())), $this->View->getJSProperty() .
				we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_frame.js'));
	}

	private function getHTMLGeneral(){
		$table = new we_html_table(['class' => 'default', 'style' => 'margin-top: 5px;'], 1, 3);

		$parentid = (!empty($this->Model->Text) && !empty($this->Model->ID) ?
			f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->Model->ID), '', $this->db) :
			(we_base_request::_(we_base_request::STRING, 'presetFolder') ?
			$this->Model->ParentID :
			(($wq = we_navigation_navigation::getWSQuery()) ?
			f('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE IsFolder=1 ' . $wq . ' ORDER BY Path LIMIT 1', '', $this->db) :
			0)
			));

		$table->setCol(0, 0, ['class' => 'defaultfont'], g_l('navigation', '[order]') . ':');
		if($this->Model->ID){
			$table->setColContent(0, 1, //we_html_tools::htmlTextInput('Ordn', '', ($this->Model->Ordn + 1), '', 'disabled="true" readonly style="width: 35px"') .
												 we_html_element::htmlHidden('Ordn', ($this->Model->Ordn)) .
				we_html_tools::htmlSelect('Position', $this->View->getEditNaviPosition(), 1, $this->Model->Ordn, false, ['onchange' => 'top.content.we_cmd(\'move_abs\',this.value);'])
			);

			$num = $this->Model->ID ? f('SELECT COUNT(ID) FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($parentid)) : 0;

			$table->setColContent(0, 2, we_html_element::htmlSpan(['style' => 'margin-left: 15px'], we_html_button::create_button(we_html_button::DIRUP, 'javascript:top.content.we_cmd("move_up");', '', 0, 0, '', '', (($this->Model->Ordn > 0) ? false : true), false) .
					we_html_button::create_button(we_html_button::DIRDOWN, 'javascript:top.content.we_cmd("move_down");', '', 0, 0, '', '', (($this->Model->Ordn < ($num - 1)) ? false : true), false)));
		} else {
			$table->setColContent(0, 1, we_html_element::htmlHiddens(['name' => 'Ordn', 'value' => -1]));
		}
		// name and folder block
		// icen selector block
		$uniqname = 'weIconNaviAttrib';
		$wepos = (weGetCookieVariable("but_weIconNaviAttrib") === 'down' ? 'down' : 'right');

		return [
				[
				'headline' => g_l('navigation', '[general]'),
				'html' => we_html_element::htmlHidden('newone', ($this->Model->ID == 0 ? 1 : 0)) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Text', '', strtr($this->Model->Text, array_flip(get_html_translation_table(HTML_SPECIALCHARS))), '', 'style="width: 520px;" onchange="top.content.mark();"'), g_l('navigation', '[name]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Display', '', $this->Model->Display, '', 'style="width: 520px;" onchange="top.content.mark();"'), g_l('navigation', '[display]')) .
				$this->getHTMLChooser(g_l('navigation', '[group]'), NAVIGATION_TABLE, 0, 'ParentID', $parentid, 'ParentPath', 'setHot', we_base_ContentTypes::FOLDER, ($this->Model->IsFolder == 0 && $this->Model->Depended == 1)),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			], [
				'headline' => '',
				'html' => $table->getHtml(),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			], [
				'headline' => '',
				'html' => $this->getHTMLChooser(g_l('navigation', '[icon]'), FILE_TABLE, 0, 'IconID', $this->Model->IconID, 'IconPath', 'setHot', we_base_ContentTypes::IMAGE, false, true, 'folder,' . we_base_ContentTypes::IMAGE) . '<table><tr><td>' . we_html_multiIconBox::getJS() .
				we_html_multiIconBox::_getButton($uniqname, "weToggleBox('" . $uniqname . "','" . addslashes(g_l('navigation', '[icon_properties_out]')) . "','" . addslashes(
						g_l('navigation', '[icon_properties]')) . "')", $wepos, g_l('global', '[openCloseBox]')) . '</td><td><span style="cursor: pointer;" class="defaultfont" id="text_' . $uniqname . '" onclick="weToggleBox(\'' . $uniqname . '\',\'' . addslashes(g_l('navigation', '[icon_properties_out]')) . '\',\'' . addslashes(g_l('navigation', '[icon_properties]')) . '\');" >' . g_l('navigation', ($wepos === 'down' ? '[icon_properties_out]' : '[icon_properties]')) . '</span></td></tr></table>',
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			], [
				'headline' => '',
				'html' => '<div id="table_' . $uniqname . '" style="display: ' . ($wepos === 'down' ? 'block' : 'none') . ';">' . $this->getHTMLImageAttributes() . '</div>',
				'space' => we_html_multiIconBox::SPACE_MED2,
				'noline' => 1
			],
		];
	}

	private function getHTMLPropertiesItem(){
		switch($this->Model->Selection){
			case we_navigation_navigation::SELECTION_DYNAMIC:
				$seltype = [we_navigation_navigation::DYN_DOCTYPE => g_l('navigation', '[documents]')];
				if(defined('OBJECT_TABLE')){
					$seltype[we_navigation_navigation::DYN_CLASS] = g_l('navigation', '[objects]');
				}
				$seltype[we_navigation_navigation::DYN_CATEGORY] = g_l('navigation', '[categories]');
				break;
			default:
				$seltype = [
					we_navigation_navigation::STYPE_DOCLINK => g_l('navigation', '[docLink]'),
					we_navigation_navigation::STYPE_URLLINK => g_l('navigation', '[urlLink]')
				];
				if(defined('OBJECT_TABLE')){
					$seltype[we_navigation_navigation::STYPE_OBJLINK] = g_l('navigation', '[objLink]');
				}
				$seltype[we_navigation_navigation::STYPE_CATLINK] = g_l('navigation', '[catLink]');
		}

		$selection_block = $this->Model->Depended == 1 ? $this->getHTMLDependedProfile() :
			we_html_element::htmlHiddens([
				'CategoriesControl' => we_base_request::_(we_base_request::INT, 'CategoriesCount', 0),
				'SortControl' => we_base_request::_(we_base_request::INT, 'SortCount', 0),
				'CategoriesCount' => (isset($this->Model->Categories) ? count($this->Model->Categories) : 0),
				'SortCount' => (isset($this->Model->Sort) ? count($this->Model->Sort) : 0)]) .
			'<div style="display: block;">' .
			(!$this->Model->IsFolder ?
			(permissionhandler::hasPerm('EDIT_DYNAMIC_NAVIGATION') ?
			we_html_tools::htmlSelect('Selection', [
				we_navigation_navigation::SELECTION_DYNAMIC => g_l('navigation', '[dyn_selection]'),
				we_navigation_navigation::SELECTION_STATIC => g_l('navigation', '[stat_selection]')
				], 1, $this->Model->Selection, false, ['onchange' => 'closeAllSelection();toggle(this.value);setWorkspaces(\'\');top.content.mark();setCustomerFilter(this);onSelectionTypeChangeJS(\'' . we_navigation_navigation::DYN_DOCTYPE . '\');'], 'value', 520) . '<br />' .
			we_html_tools::htmlSelect('SelectionType', $seltype, 1, $this->Model->SelectionType, false, ['onchange' => 'closeAllType();clearFields();closeAllStats();toggle(this.value);setWorkspaces(this.value);onSelectionTypeChangeJS(this.value);setStaticSelection(this.value);top.content.mark();',
				'style' => 'width: 520px; margin-top: 5px;'], 'value', 520) :
			we_html_element::htmlHiddens([
				'Selection' => $this->Model->Selection,
				'SelectionType' => $this->Model->SelectionType
			]) ) :
			'') .
			'<div id="dynamic" style="' . ($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC ? 'display: block;' : 'display: none;') . '">' . $this->getHTMLDynamic() . '</div>' .
			'<div id="static" style="' . ($this->Model->Selection == we_navigation_navigation::SELECTION_STATIC ? 'display: block;' : 'display: none;') . '">
				<div id="staticSelect" style="' . ($this->Model->SelectionType != we_navigation_navigation::STYPE_URLLINK ? 'display: block;' : 'display: none;') . '">' . $this->getHTMLStatic() . '</div>
				<div id="staticUrl" style="' . (($this->Model->SelectionType == we_navigation_navigation::STYPE_CATLINK || $this->Model->SelectionType == we_navigation_navigation::STYPE_URLLINK) ? 'display: block;' : 'display: none;') . ';margin-top:5px;">' . $this->getHTMLLink() . '</div>
				<div style="margin-top:5px;">' . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Parameter', 58, $this->Model->Parameter, '', 'onchange="top.content.mark();"', 'text', 520, 0), g_l('navigation', '[parameter]')) . '</div>
			</div>
			</div>';

		return [
				[
				'headline' => g_l('navigation', '[selection]'),
				'html' => $selection_block,
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
		]];
	}

	private function getHTMLPropertiesGroup(){
		$yuiSuggest = & weSuggest::getInstance();

		$cmd1 = "document.we_form.elements.LinkID.value";
		$cmd_doc = "javascript:we_cmd('we_selector_document',document.we_form.elements.LinkID.value,'" . FILE_TABLE . "','LinkID','LinkPath','','','0','',0)";

		$cmd_obj = defined('OBJECT_TABLE') ? "javascript:we_cmd('we_selector_document',document.we_form.elements.LinkID.value,'" . OBJECT_FILES_TABLE . "','LinkID','LinkPath','populateFolderWs','','0','objectFile',0)" : '';

		$button_doc = we_html_button::create_button(we_html_button::SELECT, $cmd_doc, '', 0, 0, '', '', false) .
			we_html_button::create_button(we_html_button::VIEW, 'javascript:WE().layout.openToEdit("' . FILE_TABLE . '",' . $cmd1 . ',"")', '', 0, 0, '', '', false);
		$button_obj = we_html_button::create_button(we_html_button::SELECT, $cmd_obj, '', 0, 0, '', '', false) .
			(defined('OBJECT_TABLE') ? we_html_button::create_button(we_html_button::VIEW, 'javascript:WE().layout.openToEdit("' . OBJECT_FILES_TABLE . '",' . $cmd1 . ',"")', '', 0, 0, '', '', false) : '');

		$buttons = '<div id="docFolderLink" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ? 'inline' : 'none') . '">' . $button_doc . '</div><div id="objFolderLink" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK ? 'inline' : 'none') . '">' . $button_obj . '</div>';
		$path = ($this->Model->LinkID == 0 ?
			'' :
			id_to_path($this->Model->LinkID, ($this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ? FILE_TABLE : (defined('OBJECT_TABLE') && $this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE))));

		$seltype = [
			we_navigation_navigation::STYPE_DOCLINK => g_l('navigation', '[docLink]'),
			we_navigation_navigation::STYPE_URLLINK => g_l('navigation', '[urlLink]')
		];
		if(defined('OBJECT_TABLE')){
			$seltype[we_navigation_navigation::STYPE_OBJLINK] = g_l('navigation', '[objLink]');
		}

		$yuiSuggest->setAcId('LinkPath');
		$yuiSuggest->setContentType(
			$this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ?
				implode(',', [we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML,
					we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH]) :
				(defined('OBJECT_TABLE') && $this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK ?
					'folder,objectFile' :
					implode(',', [we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML,
						we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH])
			));
		$yuiSuggest->setInput('LinkPath', $path, ['onchange' => 'top.content.mark();']);
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('LinkID', $this->Model->LinkID);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable($this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ? FILE_TABLE : (defined('OBJECT_TABLE') && $this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE));
		$yuiSuggest->setWidth(330);
		$yuiSuggest->setSelectButton($buttons);
		$yuiSuggest->setTrashButton(we_html_button::create_button(we_html_button::TRASH, 'javascript:document.we_form.elements.LinkID.value=0;document.we_form.elements.LinkPath.value="";', '', 0, 0));

		$weAcSelector = $yuiSuggest->getHTML();

		$selection = '<div style="display: block;">' .
			we_html_tools::htmlSelect('SelectionType', $seltype, 1, $this->Model->SelectionType, false, ['onchange' => "onFolderSelectionChangeJS(this.value);setFolderSelection(this.value);top.content.mark();",
				'style' => 'width: 520px; margin-top: 5px;'], 'value', 520) . '

		<div id="folderSelectionDiv" style="display: ' . ($this->Model->SelectionType != we_navigation_navigation::STYPE_URLLINK ? 'block' : 'none') . ';margin-top: 5px;">' . $weAcSelector . '</div>

		</div>
		<div id="folderUrlDiv" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STYPE_URLLINK ? 'block' : 'none') . '; margin-top: 5px;">
			' . we_html_tools::htmlTextInput('Url', 58, $this->Model->Url, '', 'onchange="top.content.mark();"', 'text', 520, 0) . '
		</div>' .
			$this->getHTMLWorkspace('object', 0, 'WorkspaceID') . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Parameter', 58, $this->Model->Parameter, '', 'onchange="top.content.mark();"', 'text', 520, 0) . '<br/>' .
				we_html_forms::checkboxWithHidden($this->Model->CurrentOnUrlPar, 'CurrentOnUrlPar', g_l('navigation', '[current_on_urlpar]'), false, "defaultfont", 'top.content.mark();"'), g_l('navigation', '[parameter]'));

		$parts = [
				[
				'headline' => g_l('navigation', '[selection]'),
				'html' => $selection,
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
		]];

		if(function_exists('mb_convert_encoding')){
			$parts[] = [
				'headline' => g_l('navigation', '[charset]'),
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('navigation', '[charset_desc]'), we_html_tools::TYPE_INFO, 520) . $this->getHTMLCharsetTable(),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			];
		}

		// COPY FOLDER
		$disabled = ($this->Model->isnew ? ' ' . g_l('weClass', '[availableAfterSave]') : '');

		$cmd = "javascript:we_cmd('we_navigation_dirSelector',document.we_form.CopyFolderID.value,'CopyFolderID','CopyFolderPath','opener.we_cmd(\"copyNaviFolder\")')";
		$button_copyFolder = we_html_button::create_button(we_html_button::SELECT, $cmd, '', 0, 0, '', '', !empty($disabled));

		$parts[] = [
			'headline' => g_l('weClass', '[copyFolder]'),
			'html' => we_html_element::jsElement("var selfNaviPath='" . addslashes($this->Model->Path) . "';
var selfNaviId = '" . $this->Model->ID . "';") .
			"<div style='float:left; margin-right:20px'>" .
			we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[copy_owners_expl]') . $disabled, we_html_tools::TYPE_INFO, 400, true, 0) . "</div>" . "<div style='padding-top:15px'>" . $button_copyFolder . "</div>" . we_html_element::htmlHiddens(
				[
					'CopyFolderID' => '',
					'CopyFolderPath' => ''
			]),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		];

		return $parts;
	}

	private function getHTMLDependedProfile(){
		switch($this->Model->Selection){
			case we_navigation_navigation::SELECTION_DYNAMIC:
				$seltype = [we_navigation_navigation::DYN_DOCTYPE => g_l('navigation', '[documents]')];
				if(defined('OBJECT_TABLE')){
					$seltype[we_navigation_navigation::DYN_CLASS] = g_l('navigation', '[objects]');
				}
				break;
			default:
				$seltype = [we_navigation_navigation::STYPE_DOCLINK => g_l('navigation', '[docLink]')];
				if(defined('OBJECT_TABLE')){
					$seltype[we_navigation_navigation::STYPE_OBJLINK] = g_l('navigation', '[objLink]');
				}
		}

		$table = new we_html_table(['width' => 520, 'class' => 'default defaultfont'], 5, 2);

		$table->setColContent(0, 0, g_l('navigation', '[stat_selection]'));
		$table->setColContent(1, 0, g_l('navigation', ($this->Model->SelectionType == we_navigation_navigation::STYPE_CATLINK ? '[catLink]' : ($this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ? '[docLink]' : '[objLink]'))) . ':');
		$table->setColContent(1, 1, id_to_path($this->Model->LinkID, ($this->Model->SelectionType == we_navigation_navigation::STYPE_CATLINK ? CATEGORY_TABLE : ($this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ? FILE_TABLE : OBJECT_FILES_TABLE))));

		if(!empty($this->Model->Url) && $this->Model->Url != 'http://'){
			$table->setColContent(2, 0, g_l('navigation', '[linkSelection]') . ':');
			$table->setColContent(2, 1, $this->Model->Url);
		} elseif(!empty($this->Model->UrlID) && $this->Model->UrlID){
			$table->setColContent(2, 0, g_l('navigation', '[linkSelection]') . ':');
			$table->setColContent(2, 1, id_to_path($this->Model->UrlID));
		}

		if($this->Model->SelectionType == we_navigation_navigation::STYPE_CATLINK){
			$table->setColContent(3, 0, g_l('navigation', '[catParameter]') . ':');
			$table->setColContent(3, 1, $this->Model->CatParameter);
		}

		if(!empty($this->Model->Parameter)){
			$table->setColContent(4, 0, g_l('navigation', '[parameter]') . ':');
			$table->setColContent(4, 1, $this->Model->Parameter);
		}

		return $table->getHtml();
	}

	private function getHTMLDynamic(){
		$dtq = we_docTypes::getDoctypeQuery($this->db);
		$this->db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		$docTypes = [g_l('navigation', '[no_entry]')] + $this->db->getAllFirst(false);

		$classID2Name = $classID2Dir = $classDirs = $classDirsJS = $classHasSubDirsJS = $classPathsJS = [];
		$allowedClasses = we_users_util::getAllowedClasses($this->db);

		$firstClass = 0;
		if(defined('OBJECT_TABLE') && $allowedClasses){
			$this->db->query('SELECT DISTINCT o.ID,o.Text,o.Path,of.ID AS classDirID FROM ' . OBJECT_TABLE . ' o JOIN ' . OBJECT_FILES_TABLE . ' of ON (o.ID=of.TableID) WHERE of.IsClassFolder=1 AND o.ID IN(' . implode(',', $allowedClasses) . ')');
			while($this->db->next_record()){
				if(!$firstClass){
					$firstClass = $this->db->f('ID');
				}
				$classID2Name[$this->db->f('ID')] = $this->db->f('Text');
				$classID2Dir[$this->db->f('classDirID')] = $this->db->f('ID');
				$classDirs[] = $this->db->f('classDirID');
				$classHasSubDirsJS[$this->db->f('ID')] = $this->db->f('ID') . ':false';
				$classDirsJS[] = $this->db->f('ID') . ':' . $this->db->f('classDirID');
				$classPathsJS[] = $this->db->f('ID') . ':"' . $this->db->f('Path') . '"';
			}
			asort($classID2Name);
			if($classDirs){
				$this->db->query('SELECT ID,ParentID FROM ' . OBJECT_FILES_TABLE . ' WHERE ParentID IN (' . implode(',', $allowedClasses) . ') AND IsFolder=1');
				while($this->db->next_record()){
					$classHasSubDirsJS[$classID2Dir[$this->db->f('ParentID')]] = intval($classID2Dir[$this->db->f('ParentID')]) . ':true';
				}
			}
		}

		/* $wsid = ($this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK && $this->Model->LinkID ?
		  we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID) :
		  []);
		 */

		$sortVal = isset($this->Model->Sort[0]['field']) ? $this->Model->Sort[0]['field'] : '';
		$sortOrder = isset($this->Model->Sort[0]['order']) ? $this->Model->Sort[0]['order'] : 'ASC';

		$sortSelect = we_html_tools::htmlSelect("SortOrder", ["ASC" => g_l('navigation', '[ascending]'), 'DESC' => g_l('navigation', '[descending]')], 1, $sortOrder, false, [
				'onchange' => 'top.content.mark();'], "value", 120);

		return we_html_element::jsElement('
var classDirs = {' . implode(',', $classDirsJS) . '};
var classPaths = {' . implode(',', $classPathsJS) . '};
var hasClassSubDirs = {' . implode(',', $classHasSubDirsJS) . '};') . '

<div style="display: block;">
	<div id="doctype" style="' . ($this->Model->DynamicSelection == we_navigation_navigation::DYN_DOCTYPE ? 'display: block' : 'display: none') . '; width: 520px;margin-top:5px;">' .
			we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect('DocTypeID', $docTypes, 1, $this->Model->DocTypeID, false, ['onchange' => 'clearFields();top.content.mark();'], 'value', 520), g_l('navigation', '[doctype]')) . '
	</div>
	<div id="classname" style="' . ($this->Model->DynamicSelection == we_navigation_navigation::DYN_CLASS ? 'display: block' : 'display: none') . '; width: 520px;margin-top:5px;">' .
			(defined('OBJECT_TABLE') ? we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect('ClassID', $classID2Name, 1, $this->Model->ClassID, false, ['onchange' => "clearFields();onSelectionClassChangeJS(this.value);"], 'value', 520), g_l('navigation', '[class]')) . $this->getHTMLWorkspace('class', $firstClass) : '') . '
	</div>
	<div id="fieldChooser" style="' . ($this->Model->DynamicSelection != we_navigation_navigation::DYN_CATEGORY ? 'display: block' : 'display: none') . '; width: 520px;margin-top: 5px;">' .
			$this->getHTMLFieldChooser(g_l('navigation', '[title_field]'), 'TitleField', $this->Model->TitleField, 'putTitleField', $this->Model->DynamicSelection) . '
	</div>' .
			$this->getHTMLDirSelector() . '
	<div id="catSort" style="' . ($this->Model->DynamicSelection != we_navigation_navigation::DYN_CATEGORY ? 'display: block' : 'display: none') . '; width: 520px;">' .
			$this->getHTMLCategory() .
			$this->getHTMLFieldChooser(g_l('navigation', '[sort]'), 'SortField', $sortVal, 'putSortField', $this->Model->DynamicSelection, $sortSelect, 120) . '
	</div>
	<div id="dynUrl" style="' . ($this->Model->DynamicSelection == we_navigation_navigation::DYN_CATEGORY ? 'display: block' : 'display: none') . '; width: 520px;">' .
			$this->getHTMLLink('dynamic_') . '
	</div>
	<div style="width: 520px;margin-top: 5px;">' .
			we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('dynamic_Parameter', 58, $this->Model->Parameter, '', 'onchange="top.content.mark();"', 'text', 520, 0), g_l('navigation', '[parameter]')) . '
	</div>' .
			$this->getHTMLCount() .
			we_html_element::htmlSpan(['style' => 'margin-top:20px;'], we_html_button::create_button('preview', 'javascript:top.content.we_cmd("dyn_preview");') . ($this->Model->hasDynChilds() ? we_html_button::create_button('delete_all', 'javascript:top.content.we_cmd("depopulate");') : '')) . '
</div>';
	}

	private function getHTMLStatic($disabled = false){
		$seltype = [we_navigation_navigation::STYPE_DOCLINK => g_l('navigation', '[docLink]')];
		if(defined('OBJECT_TABLE')){
			$seltype[we_navigation_navigation::STYPE_OBJLINK] = g_l('navigation', '[objLink]');
		}

		$cmd_doc = "javascript:we_cmd('we_selector_document',document.we_form.elements.LinkID.value,'" . FILE_TABLE . "','LinkID','LinkPath','enable_open_navigation_doc','','0',''," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";

		$cmd_obj = defined('OBJECT_TABLE') ? "javascript:we_cmd('we_selector_document',document.we_form.elements.LinkID.value,'" . OBJECT_FILES_TABLE . "LinkID','LinkPath','populateWorkspaces','','0',''," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")" : '';
		$cmd_cat = "javascript:we_cmd('we_selector_category',document.we_form.elements.LinkID.value,'" . CATEGORY_TABLE . "','document.we_form.elements.LinkID.value','document.we_form.elements.LinkPath.value','populateText','','0')";

		$button_doc = we_html_button::create_button(we_html_button::SELECT, $cmd_doc, '', 0, 0, '', '', $disabled) .
			we_html_button::create_button(we_html_button::VIEW, 'javascript:WE().layout.openToEdit("' . FILE_TABLE . '",document.we_form.elements.LinkID.value,"")', '', 0, 0, '', '', $disabled, false, '_navigation_doc');

		$button_obj = we_html_button::create_button(we_html_button::SELECT, $cmd_obj, '', 0, 0, '', '', $disabled) .
			(defined('OBJECT_TABLE') ? we_html_button::create_button(we_html_button::VIEW, 'javascript:WE().layout.openToEdit("' . OBJECT_FILES_TABLE . '",document.we_form.elements.LinkID.value,"")', '', 0, 0, '', '', $disabled, false, '_navigation_obj') : '');
		$button_cat = we_html_button::create_button(we_html_button::SELECT, $cmd_cat, '', 0, 0, '', '', $disabled);

		$buttons = '<div id="docLink" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ? 'inline' : 'none') . '">' . $button_doc . '</div><div id="objLink" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK ? 'inline' : 'none') . '">' . $button_obj . '</div><div id="catLink" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STYPE_CATLINK ? 'inline' : 'none') . '">' . $button_cat . '</div>';
		$path = ($this->Model->LinkID == 0 ?
			'' :
			id_to_path($this->Model->LinkID, ($this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ? FILE_TABLE : ($this->Model->SelectionType == we_navigation_navigation::STYPE_CATLINK ? CATEGORY_TABLE : (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : ''))))
			);

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("LinkPath", "");
		$yuiSuggest->setContentType(
			$this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ?
				implode(',', [we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML,
					we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH]) :
				($this->Model->SelectionType === we_navigation_navigation::STYPE_OBJLINK ? we_base_ContentTypes::OBJECT_FILE : ''));
		$yuiSuggest->setInput('LinkPath', $path, ['onchange' => "top.content.mark();"]);
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('LinkID', $this->Model->LinkID);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable($this->Model->SelectionType == we_navigation_navigation::STYPE_DOCLINK ? FILE_TABLE : ($this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK ? OBJECT_FILES_TABLE : CATEGORY_TABLE));
		$yuiSuggest->setWidth(370);
		$yuiSuggest->setSelectButton($buttons);

		return '<div style="margin-top:5px">' . $yuiSuggest->getHTML() . '</div>' . $this->getHTMLWorkspace();
	}

	private function getHTMLTab2(){
		/* if($this->Model->hasDynChilds()) {
		  return $this->getHTMLContentInfo();
		  } */

		$seltype = [we_navigation_navigation::DYN_DOCTYPE => g_l('navigation', '[documents]')];

		if(defined('OBJECT_TABLE')){
			$seltype[we_navigation_navigation::DYN_CLASS] = g_l('navigation', '[objects]');
		}

		$seltype[we_navigation_navigation::DYN_CATEGORY] = g_l('navigation', '[categories]');

		$selection_block = we_html_element::htmlHiddens([
				'CategoriesControl' => we_base_request::_(we_base_request::INT, 'CategoriesCount', 0),
				'SortControl' => we_base_request::_(we_base_request::INT, 'SortCount', 0),
				'CategoriesCount' => (isset($this->Model->Categories) ? count($this->Model->Categories) : '0'),
				'SortCount' => (isset($this->Model->Sort) ? count($this->Model->Sort) : '0')]) . '
<div style="display: block;">
	<div style="display:block;">' .
			we_html_tools::htmlSelect('Selection', [
				we_navigation_navigation::SELECTION_NODYNAMIC => g_l('navigation', '[no_dyn_content]'),
				we_navigation_navigation::SELECTION_DYNAMIC => g_l('navigation', '[dyn_content]')
				], 1, $this->Model->Selection, false, ['style' => 'width: 520px;', 'onchange' => 'toggle(\'dynamic\');setWorkspaces(\'\');top.content.mark();setCustomerFilter(this);onSelectionTypeChangeJS(\'' . we_navigation_navigation::DYN_DOCTYPE . '\');'], 'value', 520) . '
	</div>
	<div id="dynamic" style="' . ($this->Model->Selection === we_navigation_navigation::SELECTION_DYNAMIC ? 'display: block;' : 'display: none;') . ';margin-top:5px">' .
			we_html_tools::htmlSelect('DynamicSelection', $seltype, 1, $this->Model->DynamicSelection, false, ['onchange' => "closeAllType();clearFields();toggle(this.value);setWorkspaces(this.value);onSelectionTypeChangeJS(this.value);setStaticSelection(this.value);" . 'top.content.mark();'], 'value', 520) .
			$this->getHTMLDynamic() . '
	</div>
</div>';

		return [
				['headline' => g_l('navigation', '[content]'),
				'html' => $selection_block,
				'space' => we_html_multiIconBox::SPACE_MED
		]];
	}

	private function getHTMLContentInfo(){
		$sort = [];
		foreach($this->Model->Sort as $i){
			$sort[] = $i['field'] . '&nbsp;(' . g_l('navigation', ($i['order'] === 'DESC' ? '[descending]' : '[ascending]')) . ')';
		}

		$table = new we_html_table(['width' => 520, 'class' => 'default defaultfont'], 9, 2);

		switch($this->Model->DynamicSelection){
			case we_navigation_navigation::DYN_DOCTYPE:

				$table->setCol(0, 0, ['class' => 'bold'], g_l('navigation', '[documents]'));

				if(!empty($this->Model->DocTypeID)){
					$dt = f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.ID=' . intval($this->Model->DocTypeID));
					$table->setCol(1, 0, ['class' => 'bold'], g_l('navigation', '[doctype]') . ':');
					$table->setColContent(1, 1, $dt);
				}
				break;
			case we_navigation_navigation::DYN_CATEGORY:
				$table->setCol(0, 0, ['class' => 'bold'], g_l('navigation', '[categories]'));
				break;
			default:
				$table->setCol(0, 0, ['class' => 'bold'], g_l('navigation', '[objects]'));
				$cn = f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->Model->ClassID));
				$table->setCol(1, 0, ['class' => 'bold'], g_l('navigation', '[class]') . ':');
				$table->setColContent(1, 1, $cn);


				$table->setCol(2, 0, ['class' => 'bold'], g_l('navigation', '[workspace]') . ':');
				$table->setColContent(2, 1, id_to_path($this->Model->WorkspaceID));
		}

		if($this->Model->DynamicSelection != we_navigation_navigation::DYN_CATEGORY && !empty($this->Model->TitleField)){
			$table->setCol(3, 0, ['class' => 'bold'], g_l('navigation', '[title_field]') . ':');
			$table->setColContent(3, 1, $this->Model->TitleField);
		}

		$table->setCol(4, 0, ['class' => 'bold'], g_l('navigation', '[dir]') . ':');
		switch($this->Model->DynamicSelection){
			case we_navigation_navigation::DYN_DOCTYPE:
				$table->setColContent(4, 1, id_to_path($this->Model->FolderID, FILE_TABLE));
				break;
			case we_navigation_navigation::DYN_CATEGORY:
				$table->setColContent(4, 1, id_to_path($this->Model->FolderID, CATEGORY_TABLE));
				break;
			default:
				if(defined('OBJECT_FILES_TABLE')){
					$table->setColContent(4, 1, id_to_path($this->Model->FolderID, OBJECT_FILES_TABLE));
				}
		}
		if($this->Model->DynamicSelection != we_navigation_navigation::DYN_CATEGORY){
			if($this->Model->Categories){
				$table->setCol(5, 0, ['class' => 'bold'], g_l('navigation', '[categories]') . ':');
				$table->setColContent(5, 1, implode('<br />', $this->Model->Categories));
			}

			if($sort){
				$table->setCol(6, 0, ['class' => 'bold'], g_l('navigation', '[sort]') . ':');
				$table->setColContent(6, 1, implode('<br />', $sort));
			}
		}

		if($this->Model->Url && $this->Model->Url != 'http://'){
			$table->setCol(7, 0, ['class' => 'bold'], g_l('navigation', '[urlLink]') . ':');
			$table->setColContent(7, 1, $this->Model->Url);
		}

		if($this->Model->Paramter){
			$table->setCol(8, 0, ['class' => 'bold'], g_l('navigation', '[parameter]') . ':');
			$table->setColContent(8, 1, $this->Model->Parameter);
		}

		$table->setCol(8, 0, ['class' => 'bold'], g_l('navigation', '[show_count]') . ':');
		$table->setColContent(8, 1, $this->Model->ShowCount);

		return [
				[
				'headline' => g_l('navigation', '[entries]'),
				'html' => we_html_tools::htmlSelect('dynContent', $this->View->getItems($this->Model->ID), 20, '', false, ['style' => 'width:520px; height: 200px;  margin: 0px 0px 5px 0px;']),
				'space' => we_html_multiIconBox::SPACE_MED,
			],
				[
				'headline' => '',
				'html' =>
				we_html_button::create_button(we_html_button::PREVIEW, 'javascript:top.content.we_cmd("dyn_preview");') .
				we_html_button::create_button(we_html_button::REFRESH, 'javascript:top.content.we_cmd("populate");') .
				we_html_button::create_button(we_html_button::DELETE_ALL, 'javascript:top.content.we_cmd("depopulate");'),
				'space' => we_html_multiIconBox::SPACE_MED
			],
				[
				'headline' => g_l('navigation', '[content]'),
				'html' => $table->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED
			],
		];
	}

	private function getHTMLEditorPreview(){
		// build the page
		$out = '<table class="defaultfont" class="default">
<tr>
	<td><iframe name="preview" style="background: white; border: 1px solid black; width: 640px; height: 150px" src="' . WEBEDITION_DIR . 'we_showMod.php?mod=navigation&pnt=previewIframe"></iframe></td>
</tr>
<tr>
	<td height="30"><label for="previewCode">' . g_l('navigation', '[preview_code]') . '</label><br /></td>
<tr>
	<td>' . we_html_element::htmlTextArea(
				[
				'name' => 'previewCode',
				'id' => 'previewCode',
				'style' => 'width: 640px; height: 200px;',
				'class' => 'defaultfont'
				], $this->Model->previewCode) . '</td>
</tr>
<tr>
	<td height="10"></td>
<tr>
<tr>
	<td style="text-align:right">' .
			we_html_button::create_button(we_html_button::REFRESH, 'javascript: showPreview();') .
			we_html_button::create_button('reset', 'javascript: document.getElementById("previewCode").value = "' . str_replace(["\r\n", "\n"], '\n', addslashes(we_navigation_navigation::defaultPreviewCode)) . '"; showPreview();')
			//,we_button::create_button('new_template', 'javascript: top.content.we_cmd("create_template");')
			. '</td>
</tr>
</table>';

		return we_html_element::jsElement('
function showPreview() {
	document.we_form.pnt.value="previewIframe";
	submitForm("preview");
}') . $this->View->getCommonHiddens(['pnt' => 'preview', 'tabnr' => 'preview']) . we_html_tools::htmlDialogLayout($out, g_l('navigation', '[preview]'));
	}

	private function getHTMLEditorPreviewIframe(){
		require_once (WE_INCLUDES_PATH . 'we_tag.inc.php');

		$templateCode = $this->Model->previewCode;

		// initialize a document (only for caching needed)
		$GLOBALS['we_doc'] = new we_webEditionDocument();

		$tp = new we_tag_tagParser($templateCode);
		$tp->parseTags($templateCode);
//FIXME:eval
		eval('?>' . $templateCode);
	}

	function getHTMLProperties($preselect = ''){
		$tabNr = we_base_request::_(we_base_request::STRING, 'tabnr', self::TAB_PROPERTIES); //FIXME: due to preview - fix this as a better tab-name; replace 1-3 with consts

		if($this->Model->IsFolder == 0 && $tabNr != self::TAB_PROPERTIES && $tabNr != self::TAB_CUSTOMER){
			$tabNr = self::TAB_PROPERTIES;
		}

		$hiddens = [
			'cmd' => '',
			'pnt' => 'edbody',
			'tabnr' => $tabNr,
			'vernr' => we_base_request::_(we_base_request::INT, 'vernr', 0),
		];

		$yuiSuggest = & weSuggest::getInstance();
		switch($tabNr){
			case self::TAB_PREVIEW:
				return $this->getHTMLEditorPreview();
			default:
				// Property tab content
				$out = weSuggest::getYuiFiles() .
					we_html_element::htmlDiv(
						['id' => 'tab1',
						'style' => ($tabNr == self::TAB_PROPERTIES ? 'display: block;' : 'display: none')
						], $this->View->getCommonHiddens($hiddens) .
						we_html_element::htmlHiddens([
							'IsFolder' => (isset($this->Model->IsFolder) ? $this->Model->IsFolder : 0),
							'presetFolder' => we_base_request::_(we_base_request::STRING, 'presetFolder', '')]) .
						we_html_multiIconBox::getHTML('', $this->getHTMLGeneral(), 30, '', -1, '', '', false, $preselect) .
						($this->Model->IsFolder ?
						we_html_multiIconBox::getHTML('', $this->getHTMLPropertiesGroup(), 30, '', -1, '', '', false, $preselect) :
						we_html_multiIconBox::getHTML('', $this->getHTMLPropertiesItem(), 30, '', -1, '', '', false, $preselect)
						) .
						(($this->Model->Selection == we_navigation_navigation::SELECTION_STATIC || $this->Model->IsFolder) ?
						$this->getHTMLAttributes() :
						''
						)
					) . ($this->Model->IsFolder && permissionhandler::hasPerm('EDIT_DYNAMIC_NAVIGATION') ?
					we_html_element::htmlDiv(['id' => 'tab' . self::TAB_CONTENT, 'style' => ($tabNr == self::TAB_CONTENT ? 'display: block;' : 'display: none')], we_html_multiIconBox::getHTML('', $this->getHTMLTab2(), 30, '', -1, '', '', false, $preselect)) :
					''
					) . ((defined('CUSTOMER_TABLE')) ?
					we_html_element::htmlDiv(['id' => 'tab' . self::TAB_CUSTOMER, 'style' => ($tabNr == self::TAB_CUSTOMER ? 'display: block;' : 'display: none')], we_html_multiIconBox::getHTML('', $this->getHTMLTab3(), 30, '', -1, '', '', false, $preselect)) :
					''
					);
		}
		$out .= $yuiSuggest->getYuiJs();
		return $out;
	}

	/* function htmlTextInput($name, $size = 24, $value = "", $maxlength = "", $attribs = "", $type = "text", $width = 0, $height = 0, $markHot = "", $disabled = false){
	  $style = ($width || $height) ? (' style="' . ($width ? ('width: ' . $width . ((strpos($width, "px") || strpos(
	  $width, "%")) ? "" : "px") . ';') : '') . ($height ? ('height: ' . $height . ((strpos($height, "px") || strpos(
	  $height, "%")) ? "" : "px") . ';') : '') . '"') : '';
	  return '<input' . ($markHot ? ' onchange="if(_EditorFrame){_EditorFrame.setEditorIsHot(true);}' . $markHot . '.hot=1;"' : '') . (strstr(
	  $attribs, "class=") ? "" : ' class="wetextinput"') . ' type="' . trim($type) . '" name="' . trim($name) . '" size="' . abs(
	  $size) . '" value="' . oldHtmlspecialchars($value) . '"' . ($maxlength ? (' maxlength="' . abs(
	  $maxlength) . '"') : '') . ($attribs ? " $attribs" : '') . $style . ($disabled ? (' disabled="true"') : '') . ' />';
	  } */

	private function getHTMLFieldChooser($title, $name, $value, $cmd, $type, $extraField = '', $extraFieldWidth = 0){
		$disabled = !(($this->Model->DynamicSelection == we_navigation_navigation::DYN_CLASS && $this->Model->ClassID != 0) || ($this->Model->DynamicSelection == we_navigation_navigation::DYN_DOCTYPE && $this->Model->DocTypeID != 0));
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:fieldChooserBut('" . $cmd . "');", '', 0, 0, '', '', $disabled, false, '_' . $name);
		if(!$extraField){
			$showValue = ($type === we_navigation_navigation::DYN_CLASS && stristr($value, '_')) ? substr($value, strpos($value, '_') + 1) : $value;
			return we_html_tools::htmlFormElementTable(
					we_html_element::htmlHidden($name, $value) . we_html_tools::htmlTextInput(
						"__" . $name, 58, $showValue, '', 'onchange="setFieldValue(\'' . $name . '\',this); top.content.mark();"', 'text', 400, 0), $title, 'left', 'defaultfont', '', $button);
		}
		$showValue = stristr($value, '_') ? substr($value, strpos($value, '_') + 1) : $value;
		return we_html_tools::htmlFormElementTable(
				we_html_element::htmlHidden($name, $value) .
				we_html_tools::htmlTextInput('__' . $name, 58, $showValue, '', 'onchange="setFieldValue(\'' . $name . '\',this); top.content.mark();"', 'text', 400 - abs($extraFieldWidth) - 8, 0), $title, 'left', 'defaultfont', '', $extraField, $button);
	}

	private function getHTMLChooser($title, $table = FILE_TABLE, $rootDirID = 0, $IDName = 'ID', $IDValue = '', $PathName = 'Path', $cmd = '', $filter = we_base_ContentTypes::WEDOCUMENT, $disabled = false, $showtrash = false, $acCTypes = ""){
		if($IDValue == 0){
			$path = '/';
		} elseif(isset($this->Model->$IDName) && !empty($this->Model->$IDName)){
			$path = id_to_path($this->Model->$IDName, $table);
		} else {
			$acQuery = new we_selector_query();
			if($IDValue !== ""){
				$acResponse = $acQuery->getItemById($IDValue, $table, ['IsFolder', 'Path']);
				if($acResponse && $acResponse[0]['Path']){
					$path = $acResponse[0]['Path'];
				} else {
					// return with errormessage
				}
			} else {
				$path = "";
			}
		}

		if($table == NAVIGATION_TABLE){
			$cmd = "javascript:we_cmd('we_navigation_dirSelector',document.we_form.$IDName.value,'" . $IDName . "','" . $PathName . "','" . $cmd . "')";
			$selector = weSuggest::DirSelector;
		} else if($filter == we_base_ContentTypes::FOLDER){
			$cmd = "javascript:we_cmd('we_selector_file',document.we_form.$IDName.value,'" . $table . "','" . $IDName . "','" . $PathName . "','" . $cmd . "','','" . $rootDirID . "')";
			$selector = weSuggest::DirSelector;
		} else {
			$cmd = "javascript:we_cmd('" . ($filter == we_base_ContentTypes::IMAGE ? 'we_selector_image' : 'we_selector_document') . "',document.we_form.$IDName.value,'" . $table . "','" . $IDName . "','" . $PathName . "','" . $cmd . "','','" . $rootDirID . "','" . $filter . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";
			$selector = weSuggest::DocSelector;
		}

		if($selector == weSuggest::DocSelector){
			$path = $path === '/' ? '' : $path;
			$mayBeEmpty = 1;
		} else {
			$mayBeEmpty = 0;
		}

		if($showtrash){
			$button = we_html_button::create_button(we_html_button::SELECT, $cmd, '', 0, 0, '', '', $disabled) .
				we_html_button::create_button(we_html_button::TRASH, 'javascript:document.we_form.elements["' . $IDName . '"].value=0;document.we_form.elements["' . $PathName . '"].value="/";', '', 0, 0);
			$width = 157;
		} else {
			$width = 120;
			$button = we_html_button::create_button(we_html_button::SELECT, $cmd, '', 0, 0, '', '', $disabled);
		}

		$yuiSuggest = &weSuggest::getInstance();
		$yuiSuggest->setAcId($PathName);
		$yuiSuggest->setContentType($acCTypes ?: $filter);
		$yuiSuggest->setInput($PathName, $path, ['onchange' => 'top.content.mark();']);
		$yuiSuggest->setLabel($title);
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty($mayBeEmpty);
		$yuiSuggest->setResult($IDName, $IDValue);
		$yuiSuggest->setSelector($selector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(520 - $width);
		$yuiSuggest->setSelectButton($button);

		$weAcSelector = $yuiSuggest->getHTML();
		return (isset($weAcSelector) ?
			$weAcSelector :
			we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput($PathName, 58, $path, '', 'readonly', 'text', (520 - $width), 0), $title, 'left', 'defaultfont', we_html_element::htmlHidden($IDName, $IDValue), $button)
			);
	}

	private function getHTMLCategory(){
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category','','" . CATEGORY_TABLE . "','','','opener.addCat(top.fileSelect.data.allPaths);opener.top.content.mark();')");
		$del_but = addslashes(we_html_button::create_button(we_html_button::TRASH, 'javascript:#####placeHolder#####;top.content.mark();'));

		$variant_js = 'categoriesEdit(' . 510 . ', [' . (!empty($this->Model->Categories) ? '"' . implode('","', $this->Model->Categories) . '"' : '') . '], "' . $del_but . '");';

		$table = new we_html_table([
			'id' => 'CategoriesBlock',
			'style' => 'display: block;',
			'class' => 'default withSpace'
			], 3, 2
		);

		$table->setCol(0, 0, ['colspan' => 2, 'class' => 'defaultfont'], g_l('navigation', '[categories]'));
		$table->setCol(1, 0, ['colspan' => 2], we_html_element::htmlDiv([
				'id' => 'categories',
				'class' => 'blockWrapper',
				'style' => 'width: 520px; height: 60px; border: #AAAAAA solid 1px;'
				]
			)
		);
		$table->setCol(2, 0, ['style' => 'text-align:left'], we_html_forms::checkboxWithHidden($this->Model->CatAnd, "CatAnd", g_l('navigation', '[catAnd]')));
		$table->setCol(2, 1, ['style' => 'text-align:right'], we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:removeAllCats()") . $addbut);

		return $table->getHtml() . we_html_element::jsElement($variant_js);
	}

	private function getHTMLFieldSelector(){
		$type = we_base_request::_(we_base_request::STRING, 'type'); // doctype || class
		$selection = we_base_request::_(we_base_request::INT, 'selection'); // doctype or classid
		$cmd = we_base_request::_(we_base_request::JS, 'cmd'); // js command
		$multi = we_base_request::_(we_base_request::JS, 'multi'); // js command

		$fields = [];
		switch($type){
			case we_navigation_navigation::DYN_DOCTYPE:
				$templates = f('SELECT Templates FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($selection));
				$ids = makeArrayFromCSV($templates);

				foreach($ids as $templateID){
					$template = new we_template();
					$template->initByID($templateID, TEMPLATES_TABLE);
					$fields = array_merge($fields, array_keys($template->readAllVariantFields(true)));
				}
				$f = array_unique($fields);
				$fields = array_combine($f, $f);
				break;
			default:
				if(defined('OBJECT_TABLE')){

					$class = new we_object();
					$class->initByID($selection, OBJECT_TABLE);
					$found = array_keys($class->getAllVariantFields());
					foreach($found as $key){
						$fields[$key] = substr($key, strpos($key, "_") + 1);
					}
				}
		}
		asort($fields);
		$parts = [
				[
				'headline' => '',
				'html' => we_html_tools::htmlSelect('fields', $fields, 20, '', ($multi ? true : false), ['style' => "width: 300px; height: 200px; margin: 5px 0px 5px 0px;",
					'onclick' => "setTimeout(selectItem,100);"]),
			]
		];
		$button = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, "javascript:setFields('" . $cmd . "');", '', 0, 0, '', '', true, false), null, we_html_button::create_button(we_html_button::CLOSE, 'javascript:self.close();'));

		$body = we_html_element::htmlBody(
				['class' => "weDialogBody", "onload" => "loaded=1;"], we_html_element::htmlForm(['name' => 'we_form', "onsubmit" => "return false"], we_html_multiIconBox::getHTML('', $parts, 30, $button, -1, '', '', false, g_l('navigation', '[select_field_txt]'))));

		return $this->getHTMLDocument($body, we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_frame.js'));
	}

	private function getHTMLCount(){
		return '<div style="width: 520px; margin-top: 5px">' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'ShowCount', 30, $this->Model->ShowCount, '', 'onBlur="var r=parseInt(this.value);if(isNaN(r)) this.value=' . $this->Model->ShowCount . '; else{ this.value=r; top.content.mark();}"', 'text', 520, 0), g_l('navigation', '[show_count]')) . '</div>';
	}

	private function getHTMLDirSelector(){
		$table = $this->Model->DynamicSelection == we_navigation_navigation::DYN_DOCTYPE ? FILE_TABLE :
			($this->Model->DynamicSelection == we_navigation_navigation::DYN_CLASS ? OBJECT_FILES_TABLE :
			($this->Model->DynamicSelection == we_navigation_navigation::DYN_CATEGORY ? CATEGORY_TABLE :
			FILE_TABLE));

		$rootDirID = (($ws = get_ws($table, true)) ? reset($ws) : 0);

		$button_doc = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.FolderID.value,'" . FILE_TABLE . "','FolderID','FolderPath','setHot','','" . $rootDirID . "')");
		$countSubDirs = 1;
		if(defined('OBJECT_FILES_TABLE') && ($this->Model->DynamicSelection == we_navigation_navigation::DYN_CLASS || $this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK)){
			$classDirID = f('SELECT of.ID FROM ' . OBJECT_TABLE . ' o LEFT JOIN ' . OBJECT_FILES_TABLE . ' of ON o.Path=of.Path WHERE o.ID=' . $this->Model->ClassID, '', $this->db);
			$countSubDirs = f('SELECT COUNT(1) FROM ' . OBJECT_FILES_TABLE . ' WHERE ParentID=' . $classDirID . ' AND IsFolder=1', '', $this->db);
		}

		$button_obj = defined('OBJECT_TABLE') ? we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements.FolderID.value,'" . OBJECT_FILES_TABLE . "','FolderID','FolderPath','setHot','',classDirs[document.we_form.elements.ClassID.options[document.we_form.elements.ClassID.selectedIndex].value])", '', 0, 0, "", "", ($countSubDirs ? false : true), false, "_XFolder") : '';
		$button_cat = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_category',document.we_form.elements.FolderID.value,'" . CATEGORY_TABLE . "','document.we_form.elements.FolderID.value','document.we_form.elements.FolderPath.value','opener.top.content.mark();','','" . $rootDirID . "')");
		$buttons = '<div id="docFolder" style="display: ' . (($this->Model->DynamicSelection == we_navigation_navigation::DYN_DOCTYPE) ? 'inline' : 'none') . '">' . $button_doc . '</div><div id="objFolder" style="display: ' . ($this->Model->DynamicSelection == we_navigation_navigation::DYN_CLASS ? 'inline' : 'none') . '">' . $button_obj . '</div><div id="catFolder" style="display: ' . ($this->Model->DynamicSelection == we_navigation_navigation::DYN_CATEGORY ? 'inline' : 'none') . '">' . $button_cat . '</div>';


		$path = id_to_path($this->Model->FolderID ?: $rootDirID, $table);
		$attribs = ['onchange' => 'top.content.mark();'];
		if(!$countSubDirs){
			$attribs['disabled'] = "disabled";
		}

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("FolderPath", defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE ? id_to_path($this->Model->ClassID, OBJECT_FILES_TABLE) : "");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput('FolderPath', $path, $attribs);
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('FolderID', $this->Model->FolderID ?: $rootDirID);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setLabel(g_l('navigation', '[dir]'));
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(400);
		$yuiSuggest->setSelectButton($buttons);

		$weAcSelector = $yuiSuggest->getHTML();

		return we_html_element::htmlDiv(['style' => 'margin-top:5px;'], $weAcSelector);
	}

	private function getHTMLDynPreview(){
		$select = new we_html_select(['size' => 20, 'style' => 'width: 420px; height: 200; margin: 5px 0px 5px 0px;']);

		$items = $this->Model->getDynamicEntries();

		foreach($items as $k => $item){
			$txt = id_to_path(
				$item['id'], ($this->Model->DynamicSelection == we_navigation_navigation::DYN_DOCTYPE) ? FILE_TABLE : ($this->Model->DynamicSelection == we_navigation_navigation::DYN_CATEGORY ? CATEGORY_TABLE : OBJECT_FILES_TABLE));
			if($item['field']){
				$opt = we_html_select::getNewOptionGroup([
						'class' => 'bold',
						'style' => 'font-style: normal; color: darkblue;',
						'label' => $item['field']
				]);
				$opt->addChild(we_html_select::getNewOption($k, $txt));
				$select->addChild($opt);
			} else {
				$select->addOption($k, $txt);
			}
		}

		$parts = [
				[
				'headline' => '',
				'html' => we_html_tools::htmlFormElementTable(
					$select->getHtml(), g_l('navigation', ($this->Model->DynamicSelection == we_navigation_navigation::DYN_CATEGORY ? '[categories]' : ($this->Model->DynamicSelection == we_navigation_navigation::DYN_CLASS ? '[objects]' : '[documents]')))),
		]];

		$body = we_html_element::htmlBody(['class' => "weDialogBody"], we_html_element::htmlForm([
					'name' => 'we_form', 'onsubmit' => 'return false'
					], we_html_multiIconBox::getHTML('', $parts, 30, '<div style="float:right;">' .
						we_html_button::create_button('close', 'javascript:self.close();') .
						'</div>', -1, '', '', false, g_l('navigation', '[dyn_selection]'))));

		return $this->getHTMLDocument($body, '');
	}

	private function getHTMLWorkspace($type = 'object', $defClassID = 0, $field = 'WorkspaceID'){
		if($type === 'class'){
			$wsid = ($this->Model->DynamicSelection == we_navigation_navigation::DYN_CLASS && $this->Model->ClassID ?
				we_navigation_dynList::getWorkspacesForClass($this->Model->ClassID) :
				($defClassID ?
				we_navigation_dynList::getWorkspacesForClass($defClassID) :
				[]
				));

			return '<div id="objLinkWorkspaceClass" style="display: ' . (($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC) ? 'block' : 'none') . ';margin-top: 5px;">' .
				we_html_tools::htmlFormElementTable(
					we_html_tools::htmlSelect('WorkspaceIDClass', $wsid, 1, $this->Model->WorkspaceID, false, ['style' => 'width: 520px;', 'onchange' => 'top.content.mark();'], 'value'), g_l('navigation', '[workspace]')
				) . '</div>';
		}

		if($this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK && $this->Model->LinkID){
			$wsid = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);
		} else {
			$wsid = [];
		}

		if($field === 'WorkspaceID'){
			return '<div id="objLinkWorkspace" style="display: ' . (($this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK && ($this->Model->WorkspaceID > -1)) ? 'block' : 'none') . ';margin-top: 5px;">' .
				we_html_tools::htmlFormElementTable(
					we_html_tools::htmlSelect('WorkspaceID', $wsid, 1, $this->Model->WorkspaceID, false, ['style' => 'width: 520px;', 'onchange' => 'top.content.mark();'], 'value'), g_l('navigation', '[workspace]')) .
				'</div>';
		}

		return '<div id="objLinkFolderWorkspace" style="display: ' . (($this->Model->SelectionType == we_navigation_navigation::STYPE_OBJLINK && ($this->Model->WorkspaceID > -1)) ? 'block' : 'none') . ';margin-top: 5px;">' .
			we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect('WorkspaceID', $wsid, 1, $this->Model->WorkspaceID, false, ['style' => 'width: 520px;', 'onchange' => 'top.content.mark();'], 'value'), g_l('navigation', '[workspace]')) .
			'</div>';
	}

	private function getHTMLLink($prefix = ''){
		$cmd = "javascript:we_cmd('we_selector_document',document.we_form.elements." . $prefix . "UrlID.value,'" . FILE_TABLE . "','" . $prefix . "UrlID','" . $prefix . "UrlIDPath','setHot','',0,'" . we_base_ContentTypes::WEDOCUMENT . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";

		$path = id_to_path($this->Model->UrlID);

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId($prefix . 'UrlIDPath');
		$yuiSuggest->setContentType([we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML,
			we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH]);
		$yuiSuggest->setInput($prefix . 'UrlIDPath', $path, ['onchange' => 'top.content.mark();']);
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($prefix . 'UrlID', $this->Model->UrlID);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setWidth(400);
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, $cmd));

		$weAcSelector = $yuiSuggest->getHTML();

		return '<div id="' . $prefix . 'LinkSelectionDiv" style="display: ' . (($this->Model->SelectionType == we_navigation_navigation::STYPE_CATLINK || $this->Model->DynamicSelection == we_navigation_navigation::DYN_CATEGORY) ? 'block' : 'none') . ';margin-top: 5px;">' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect($prefix . 'LinkSelection', [
					we_navigation_navigation::LSELECTION_INTERN => g_l('navigation', '[intern]'),
					we_navigation_navigation::LSELECTION_EXTERN => g_l('navigation', '[extern]')
					], 1, $this->Model->LinkSelection, false, ['style' => 'width: 520px;', 'onchange' => "setLinkSelection('" . $prefix . "',this.value);top.content.mark();"], 'value'), g_l('navigation', '[linkSelection]')) . '</div>
				<div id="' . $prefix . 'intern" style="display: ' . (($this->Model->LinkSelection === we_navigation_navigation::LSELECTION_INTERN && $this->Model->SelectionType != we_navigation_navigation::STYPE_URLLINK) ? 'block' : 'none') . ';margin-top: 5px;">
				' . $weAcSelector . '
				</div>
				<div id="' . $prefix . 'extern" style="display: ' . (($this->Model->LinkSelection === we_navigation_navigation::LSELECTION_EXTERN || $this->Model->SelectionType == we_navigation_navigation::STYPE_URLLINK) ? 'block' : 'none') . ';margin-top: 5px;">' . we_html_tools::htmlTextInput(
				$prefix . 'Url', 58, $this->Model->Url, '', 'onchange="top.content.mark();"', 'text', 520) . '</div>
				<div style="margin-top: 5px;">' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput($prefix . 'CatParameter', 58, $this->Model->CatParameter, '', 'onchange="top.content.mark();"', 'text', 520), g_l('navigation', '[catParameter]')) . '</div>';
	}

	private function getHTMLCharsetTable(){
		$value = (!empty($this->Model->Charset) ? $this->Model->Charset : $GLOBALS['WE_BACKENDCHARSET']);

		$charsetHandler = new we_base_charsetHandler();

		$charsets = $charsetHandler->getCharsetsForTagWizzard();
		asort($charsets);
		reset($charsets);

		$table = new we_html_table(['class' => 'default'], 1, 2);
		$table->setCol(0, 0, null, we_html_tools::htmlTextInput("Charset", 15, $value, '', '', 'text', 120));
		$table->setCol(0, 1, null, we_html_tools::htmlSelect("CharsetSelect", $charsets, 1, $value, false, ['onblur' => 'document.forms[0].elements.Charset.value=this.options[this.selectedIndex].value;',
				'onchange' => 'document.forms[0].elements.Charset.value=this.options[this.selectedIndex].value;document.we_form.submit();'], 'value', 400, "defaultfont", false));

		return $table->getHtml();
	}

	private function getLangField($name, $value, $title, $width){
		//FIXME: these values should be obtained from global settings
		$input = we_html_tools::htmlTextInput("Attributes[$name]", 15, $value, "", 'onchange=top.content.mark(); ', "text", $width - 100);
		$select = '
<select style="width:100px;" class="weSelect" name="' . $name . '_select" onchange="top.content.mark(); this.form.elements[\'Attributes[' . $name . ']\'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;">
	<option value=""></option>
	<option value="en">en</option>
	<option value="de">de</option>
	<option value="es">es</option>
	<option value="fi">fi</option>
	<option value="ru">ru</option>
	<option value="fr">fr</option>
	<option value="nl">nl</option>
	<option value="pl">pl</option>
</select>';
		return we_html_tools::htmlFormElementTable($input, $title, "left", "defaultfont", $select);
	}

	private function getRevRelSelect($type, $value, $title){
		$input = we_html_tools::htmlTextInput(
				"Attributes[$type]", 15, $value, '', 'onchange=top.content.mark(); ', 'text', 400);
		$select = '<select name="' . $type . '_sel" class="weSelect" style="width:100px;" onchange="top.content.mark(); this.form.elements[\'Attributes[' . $type . ']\'].value=this.options[this.selectedIndex].text;this.selectedIndex=0;">
	<option></option>
	<option>contents</option>
	<option>chapter</option>
	<option>section</option>
	<option>subsection</option>
	<option>index</option>
	<option>glossary</option>
	<option>appendix</option>
	<option>copyright</option>
	<option>next</option>
	<option>prev</option>
	<option>start</option>
	<option>help</option>
	<option>bookmark</option>
	<option>alternate</option>
	<option>nofollow</option>
</select>';
		return we_html_tools::htmlFormElementTable($input, $title, "left", "defaultfont", $select);
	}

	private function getHTMLAttributes(){
		$title = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[title]', 30, $this->Model->getAttribute('title'), '', 'onchange="top.content.mark();"', 'text', 520), g_l('navigation', '[title]'));

		$anchor = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[anchor]', 30, $this->Model->getAttribute('anchor'), '', 'onchange="top.content.mark();" onblur="if(this.value&&!new RegExp(\'#?[a-z]+[a-z0-9_:.-=]*$\',\'i\').test(this.value)){alert(\'' . g_l('linklistEdit', '[anchor_invalid]') . '\');}"', 'text', 520) . '<br/>' .
				we_html_forms::checkboxWithHidden($this->Model->CurrentOnAnker, 'CurrentOnAnker', g_l('navigation', '[current_on_anker]'), false, "defaultfont", 'top.content.mark();"'), g_l('navigation', '[anchor]'));

		$target = we_html_tools::htmlFormElementTable(
				we_html_tools::targetBox('Attributes[target]', 30, 400, '', $this->Model->getAttribute('target'), 'top.content.mark();', 8, 100), g_l('navigation', '[target]'));

		$link = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[link_attribute]', 30, $this->Model->getAttribute('link_attribute'), '', 'onchange="top.content.mark();"', 'text', 520), g_l('navigation', '[link_attribute]'));

		$lang = $this->getLangField('lang', $this->Model->getAttribute('lang'), g_l('navigation', '[link_language]'), 520);
		$hreflang = $this->getLangField('hreflang', $this->Model->getAttribute('hreflang'), g_l('navigation', '[href_language]'), 520);

		$parts = [
				[
				'headline' => '',
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('navigation', '[linkprops_desc]'), we_html_tools::TYPE_INFO, 520),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			], [
				'headline' => g_l('navigation', '[attributes]'),
				'html' => $title . $anchor . $link . $target,
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			], [
				'headline' => g_l('navigation', '[language]'),
				'html' => $lang . $hreflang,
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			]
		];

		$accesskey = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[accesskey]', 30, $this->Model->getAttribute('accesskey'), '', 'onchange="top.content.mark();"', 'text', 520), g_l('navigation', '[accesskey]'));

		$tabindex = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[tabindex]', 30, $this->Model->getAttribute('tabindex'), '', 'onchange="top.content.mark();"', 'text', 520), g_l('navigation', '[tabindex]'));

		$parts[] = [
			'headline' => g_l('navigation', '[keyboard]'),
			'html' => $accesskey . $tabindex,
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		];

		$relfield = $this->getRevRelSelect('rel', $this->Model->getAttribute('rel'), 'rel');
		$revfield = $this->getRevRelSelect('rev', $this->Model->getAttribute('rev'), 'rev');

		$parts[] = [
			'headline' => g_l('navigation', '[relation]'),
			'html' => $relfield . $revfield,
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		];

		$input_width = 70;

		$popup = new we_html_table(['class' => 'withSpace'], 4, 4);

		$popup->setCol(0, 0, ['colspan' => 2], we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_open'), 'Attributes[popup_open]', g_l('navigation', '[popup_open]'), false, "defaultfont", 'top.content.mark();"'));
		$popup->setCol(0, 2, ['colspan' => 2], we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_center'), 'Attributes[popup_center]', g_l('navigation', '[popup_center]'), false, "defaultfont", 'top.content.mark();"'));

		$popup->setCol(1, 0, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[popup_xposition]', 5, $this->Model->getAttribute('popup_xposition'), '', 'onchange="top.content.mark();"', 'text', $input_width), g_l('navigation', '[popup_x]')));
		$popup->setCol(1, 1, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[popup_yposition]', 5, $this->Model->getAttribute('popup_yposition'), '', 'onchange="top.content.mark();"', 'text', $input_width), g_l('navigation', '[popup_y]')));
		$popup->setCol(1, 2, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[popup_width]', 5, $this->Model->getAttribute('popup_width'), '', 'onchange="top.content.mark();"', 'text', $input_width), g_l('navigation', '[popup_width]')));

		$popup->setCol(1, 3, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[popup_height]', 5, $this->Model->getAttribute('popup_height'), '', 'onchange="top.content.mark();"', 'text', $input_width), g_l('navigation', '[popup_height]')));

		$popup->setCol(2, 0, [], we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_status'), 'Attributes[popup_status]', g_l('navigation', '[popup_status]'), false, "defaultfont", 'top.content.mark();"'));
		$popup->setCol(2, 1, [], we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_scrollbars'), 'Attributes[popup_scrollbars]', g_l('navigation', '[popup_scrollbars]'), false, "defaultfont", 'top.content.mark();"'));
		$popup->setCol(2, 2, [], we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_menubar'), 'Attributes[popup_menubar]', g_l('navigation', '[popup_menubar]'), false, "defaultfont", 'top.content.mark();"'));

		$popup->setCol(3, 0, [], we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_resizable'), 'Attributes[popup_resizable]', g_l('navigation', '[popup_resizable]'), false, "defaultfont", 'top.content.mark();"'));
		$popup->setCol(3, 1, [], we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_location'), 'Attributes[popup_location]', g_l('navigation', '[popup_location]'), false, "defaultfont", 'top.content.mark();"'));
		$popup->setCol(3, 2, [], we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_toolbar'), 'Attributes[popup_toolbar]', g_l('navigation', '[popup_toolbar]'), false, "defaultfont", 'top.content.mark();"'));

		$parts[] = [
			'headline' => g_l('navigation', '[popup]'),
			'html' => $popup->getHTML(),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		];

		$wepos = weGetCookieVariable("but_weNaviAttrib");
		return we_html_multiIconBox::getHTML('weNaviAttrib', $parts, 30, '', 0, g_l('navigation', '[more_attributes]'), g_l('navigation', '[less_attributes]'), ($wepos === 'down'));
	}

	private function getHTMLImageAttributes(){
		$input_width = 70;
		$img_props = new we_html_table(['class' => 'withSpace'], 4, 5);

		$img_props->setColContent(0, 0, we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[icon_width]', 5, $this->Model->getAttribute('icon_width'), '', 'onchange="top.content.mark();"', 'text', $input_width), g_l('navigation', '[icon_width]')));
		$img_props->setColContent(0, 1, we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[icon_height]', 5, $this->Model->getAttribute('icon_height'), '', 'onchange="top.content.mark();"', 'text', $input_width), g_l('navigation', '[icon_height]')));
		$img_props->setColContent(0, 2, we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[icon_border]', 5, $this->Model->getAttribute('icon_border'), '', 'onchange="top.content.mark();"', 'text', $input_width), g_l('navigation', '[icon_border]')));
		$img_props->setColContent(0, 3, we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[icon_hspace]', 5, $this->Model->getAttribute('icon_hspace'), '', 'onchange="top.content.mark();"', 'text', $input_width), g_l('navigation', '[icon_hspace]')));
		$img_props->setColContent(0, 4, we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[icon_vspace]', 5, $this->Model->getAttribute('icon_vspace'), '', 'onchange="top.content.mark();"', 'text', $input_width), g_l('navigation', '[icon_vspace]')));
		$img_props->setCol(1, 0, ['colspan' => 5], we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect(
					'Attributes[icon_align]', [
					'' => 'Default',
					'top' => 'Top',
					'middle' => 'Middle',
					'bottom' => 'Bottom',
					'left' => 'left',
					'right' => 'Right',
					'texttop' => 'Text Top',
					'absmiddle' => 'Abd Middle',
					'baseline' => 'Baseline',
					'absbottom' => 'Abs Bottom'
					], 1, $this->Model->getAttribute('icon_align'), false, ['style' => 'width: 470px;']), g_l('navigation', '[icon_align]')));
		$img_props->setCol(2, 0, ['colspan' => 5], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[icon_alt]', 5, $this->Model->getAttribute('icon_alt'), '', 'onchange="top.content.mark();"', 'text', 470), g_l('navigation', '[icon_alt]')));
		$img_props->setCol(3, 0, ['colspan' => 5], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[icon_title]', 5, $this->Model->getAttribute('icon_title'), '', 'onchange="top.content.mark();"', 'text', 470), g_l('navigation', '[icon_title]')));

		return $img_props->getHTML();
	}

	private function getHTMLTab3(){
		$filter = new we_navigation_customerFilter();
		$filter->initByNavModel($this->Model);

		$view = new we_navigation_customerFilterView($filter, 520);
		return [
				[
				'headline' => '',
				'html' => $view->getFilterHTML($this->Model->IsFolder == 0 && $this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
		]];
	}

	protected function getHTMLEditorFooter(array $btn_cmd = [], $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return parent::getHTMLEditorFooter([]);
		}

		$table2 = new we_html_table(['class' => 'default'], 1, 3);
		$table2->setColContent(0, 0, we_html_element::htmlSpan(['style' => 'margin-left: 15px'], we_html_button::create_button(we_html_button::SAVE, "javascript:top.content.makeNewDoc=document.we_form.makeNewDoc.checked;top.content.we_cmd('module_navigation_save');", '', 0, 0, '', '', (!permissionhandler::hasPerm('EDIT_NAVIGATION')))));
		$table2->setColContent(0, 1, we_html_forms::checkbox("makeNewDoc", false, "makeNewDoc", g_l('global', ($this->View->Model->IsFolder ? '[we_new_folder_after_save]' : '[we_new_entry_after_save]')), false, "defaultfont", ""));
		if($this->Model->ID && permissionhandler::hasPerm(['DELETE_NAVIGATION', 'EDIT_NAVIGATION'])){
			$table2->setColContent(0, 2, we_html_button::create_button(we_html_button::TRASH, "javascript:top.we_cmd('module_navigation_delete');"));
		}

		return $this->getHTMLDocument(
				we_html_element::htmlBody([
					'id' => 'footerBody',
					'onload' => 'document.we_form.makeNewDoc.checked=top.content.makeNewDoc;'
					], we_html_element::htmlForm([], $table2->getHtml())));
	}

}
