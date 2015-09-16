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
//class weNavigationFrames extends weToolFramesInterim {

	var $toolDir; //TODO: replace toll/module-wide by $module
	var $toolUrl; //TODO: replace toll/module-wide by $module
	var $_space_size = 120;
	var $_text_size = 75;
	var $_width_size = 520;
	var $Model;
	public $module = 'navigation';
	public $toolName = 'navigation';
	public $Table = NAVIGATION_TABLE;
	public $TreeSource = '';
	protected $treeDefaultWidth = 220;
	protected $treeFooterHeight = 40;

	function __construct(){
		$this->toolUrl = WE_INCLUDES_DIR . 'we_modules/' . $this->module . '/'; //TODO: replace toll/module-wide by $module
		$this->toolDir = $_SERVER['DOCUMENT_ROOT'] . $this->toolUrl; //TODO: replace toll/module-wide by $module

		$_frameset = $this->toolUrl . 'edit_' . $this->toolName . '_frameset.php';
		parent::__construct($_frameset);

		$this->Tree = new we_navigation_tree();
		$this->TreeSource = 'table:' . $this->Table;
		$this->View = new we_navigation_view($_frameset, 'top.content');
		$this->Model = &$this->View->Model;
		$this->setupTree(NAVIGATION_TABLE, 'top.content', 'top.content', 'top.content.cmd');
	}

	function getHTML($what){
		switch($what){
			case 'preview' :
				return $this->getHTMLEditorBody();
			case 'previewIframe' :
				return $this->getHTMLEditorPreviewIframe();
			case 'fields' :
				return $this->getHTMLFieldSelector();
			case 'dyn_preview' :
				return $this->getHTMLDynPreview();
			default :
				return parent::getHTML($what);
		}
	}

	function getHTMLFrameset(){
		$extraHead = $this->getJSCmdCode() .
			$this->Tree->getJSTreeCode() .
			we_html_element::jsElement($this->getJSStart()) .
			we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
			we_main_headermenu::css();

		$tab = we_base_request::_(we_base_request::STRING, 'tab');
		$sid = we_base_request::_(we_base_request::STRING, 'sid', false);
		$extraUrlParams = ($tab !== false ? '&tab=' . $tab : '' ) . ($sid !== false ? '&sid=' . $sid : '');

		return parent::getHTMLFrameset($extraHead, $extraUrlParams);
	}

	protected function getHTMLTreeFooter(){
		return '<div id="infoField" style="margin:5px; display: none;" class="defaultfont"></div>';
	}

	function getHTMLCmd(){
		$pid = we_base_request::_(we_base_request::INT, 'pid');
		if($pid === false){
			exit;
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);
		$_loader = new we_navigation_treeDataSource($this->TreeSource);

		$rootjs = (!$pid ?
				$this->Tree->topFrame . '.treeData.clear();' .
				$this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));' : '');

		$hiddens = we_html_element::htmlHidden(array('name' => 'pnt', 'value' => 'cmd')) .
			we_html_element::htmlHidden(array('name' => 'cmd', 'value' => 'no_cmd'));

		return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => 'white', 'marginwidth' => 10, 'marginheight' => 10, 'leftmargin' => 10, 'topmargin' => 10), we_html_element::htmlForm(array('name' => 'we_form'), $hiddens .
						we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree($_loader->getItems($pid, $offset, $this->Tree->default_segment, '')))
					)
		));
	}

	function getJSCmdCode(){
		return $this->View->getJSTop() . we_html_element::jsElement($this->Tree->getJSMakeNewEntry());
	}

	public function getHTMLDocumentHeader(){
		if(!empty($this->Model->Charset)){
			we_html_tools::headerCtCharset('text/html', $this->Model->Charset);
		}
		return we_html_tools::getHtmlTop($this->module, $this->Model->Charset);
	}

	/**
	 * Frame for tabs
	 *
	 * @return string
	 */
	protected function getHTMLEditorHeader(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(
					we_html_element::htmlBody(
						array(
						'bgcolor' => '#F0EFF0',
						), ''));
		}

		$we_tabs = new we_tabs();

		$we_tabs->addTab(new we_tab('#', g_l('navigation', '[property]'), '((' . $this->topFrame . '.activ_tab==1) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('1');", array("id" => "tab_1")));
		if($this->Model->IsFolder){
			$we_tabs->addTab(new we_tab("#", g_l('navigation', '[content]'), '((' . $this->topFrame . '.activ_tab==2) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('2');", array("id" => "tab_2")));
		}

		if(defined('CUSTOMER_TABLE') && permissionhandler::hasPerm("CAN_EDIT_CUSTOMERFILTER")){
			$we_tabs->addTab(new we_tab("#", g_l('navigation', '[customers]'), '((' . $this->topFrame . '.activ_tab==3) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('3');", array("id" => "tab_3")));
		}

		if($this->Model->IsFolder){
			$we_tabs->addTab(new we_tab("#", g_l('navigation', '[preview]'), '((' . $this->topFrame . '.activ_tab=="preview") ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('preview');", array("id" => "tab_preview")));
		}

		$we_tabs->onResize();
		$tabsHead = $we_tabs->getHeader() .
			we_html_element::jsElement(
				($this->Model->IsFolder == 0 ? '
if(' . $this->View->topFrame . '.activ_tab!=1 && ' . $this->View->topFrame . '.activ_tab!=3) {
	' . $this->View->topFrame . '.activ_tab=1;
}' : ''
				) . '
function mark() {
	var elem = document.getElementById("mark");
	elem.style.display = "inline";

}

function unmark() {
	var elem = document.getElementById("mark");
	elem.style.display = "none";
}

function setTab(tab) {
	switch (tab) {
		case "preview":	// submit the information to preview screen
			parent.edbody.document.we_form.cmd.value="";
			if (' . $this->topFrame . '.activ_tab != tab || (' . $this->topFrame . '.activ_tab=="preview" && tab=="preview")) {
				parent.edbody.document.we_form.pnt.value = "preview";
				parent.edbody.document.we_form.tabnr.value = "preview";
				parent.edbody.submitForm();
			}
		break;

		default: // just toggle content to show
			if (' . $this->topFrame . '.activ_tab!="preview") {
				parent.edbody.toggle("tab"+' . $this->topFrame . '.activ_tab);
				parent.edbody.toggle("tab"+tab);
				' . $this->topFrame . '.activ_tab=tab;
				self.focus();
			} else {

				parent.edbody.document.we_form.pnt.value = "edbody";
				parent.edbody.document.we_form.tabnr.value = tab;
				parent.edbody.submitForm();
			}
		break;
	}
	self.focus();
	' . $this->topFrame . '.activ_tab=tab;
}' . ($this->Model->ID ? '' : $this->topFrame . '.activ_tab=1;')
		);


		$table = new we_html_table(array("style" => 'width:100%;margin-top:3px;', "cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 1);

		$table->setCol(0, 0, array("valign" => "top", "class" => "small"), we_html_tools::getPixel(15, 2) .
			we_html_element::htmlB(
				g_l('navigation', ($this->Model->IsFolder ? '[group]' : '[entry]')) .
				':&nbsp;' . str_replace('&amp;', '&', $this->Model->Text) .
				'<div id="mark" style="display: none;">*</div>' . we_html_tools::getPixel(1, 19)
		));

		$extraJS = 'document.getElementById("tab_"+' . $this->topFrame . '.activ_tab).className="tabActive";';
		$body = we_html_element::htmlBody(
				array(
				"bgcolor" => "white",
				"background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif",
				"marginwidth" => 0,
				"marginheight" => 0,
				"leftmargin" => 0,
				"topmargin" => 0,
				"onload" => "setFrameSize()",
				"onresize" => "setFrameSize()"
				), we_html_element::htmlDiv(array('id' => "main"), we_html_tools::getPixel(100, 3) . we_html_element::htmlDiv(array('id' => 'headrow', 'style' => "margin:0px;"), '&nbsp;' .
						we_html_element::htmlB(g_l('navigation', ($this->Model->IsFolder ? '[group]' : '[entry]')) . ':&nbsp;' .
							str_replace('&amp;', '&', $this->Model->Text) .
							we_html_element::htmlDiv(array('id' => 'mark', 'style' => 'display: none;'), '*'))) .
					we_html_tools::getPixel(100, 3) . $we_tabs->getHTML() . '</div>' . we_html_element::jsElement($extraJS))
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){

		$hiddens = array('cmd' => 'tool_' . $this->toolName . '_edit', 'pnt' => 'edbody', 'vernr' => we_base_request::_(we_base_request::INT, 'vernr', 0));

		if(we_base_request::_(we_base_request::BOOL, "home")){
			$hiddens['cmd'] = 'home';
			$GLOBALS['we_print_not_htmltop'] = true;
			$GLOBALS['we_head_insert'] = $this->View->getJSProperty();
			$GLOBALS['we_body_insert'] = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->getCommonHiddens($hiddens) . we_html_element::htmlHidden(array('name' => 'home', 'value' => '0')));
			$tool = $GLOBALS['tool'] = $this->toolName;
			ob_start();
			include($this->toolDir . 'home.inc.php');
			$out = ob_get_clean();

			return
				we_html_element::jsElement('
								' . $this->topFrame . '.editor.edheader.location="' . $this->frameset . '?pnt=edheader&home=1";
								' . $this->topFrame . '.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter&home=1";
			') . $out;
		}

		$body = we_html_element::htmlBody(array("class" => "weEditorBody", 'onload' => 'loaded=1;'), we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js?' . WE_VERSION) .
				we_html_element::htmlForm(array('name' => 'we_form', 'onsubmit' => 'return false'), $this->getHTMLProperties()
				)
		);

		return $this->getHTMLDocument($body, STYLESHEET . $this->View->getJSProperty());
	}

	function getHTMLGeneral(){
		$_table = new we_html_table(
			array(
			'border' => 0,
			'cellpadding' => 0,
			'cellspacing' => 0,
			'width' => 300,
			'style' => 'margin-top: 5px;'
			), 1, 3);

		$_parentid = (isset($this->Model->Text) && $this->Model->Text && isset($this->Model->ID) && $this->Model->ID ?
				f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->Model->ID), 'ParentID', $this->db) :
				(we_base_request::_(we_base_request::STRING, 'presetFolder') ?
					$this->Model->ParentID :
					(($wq = we_navigation_navigation::getWSQuery()) ?
						f('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE IsFolder=1 ' . $wq . ' ORDER BY Path LIMIT 1', '', $this->db) :
						0)
				));

		$_table->setCol(0, 0, array('class' => 'defaultfont'), g_l('navigation', '[order]') . ':');
		if($this->Model->ID){
			$_table->setColContent(0, 1, //we_html_tools::htmlTextInput('Ordn', '', ($this->Model->Ordn + 1), '', 'disabled="true" readonly style="width: 35px"') .
				we_html_element::htmlHidden(array('name' => 'Ordn', 'value' => $this->Model->Ordn)) .
				we_html_tools::htmlSelect('Position', $this->View->getEditNaviPosition(), 1, $this->Model->Ordn, false, array('onchange' => $this->topFrame . '.we_cmd(\'move_abs\',this.value);'))
			);

			$_num = $this->Model->ID ? f('SELECT COUNT(ID) FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($_parentid)) : 0;

			$_table->setColContent(0, 2, we_html_button::create_button_table(
					array(
					we_html_button::create_button('image:direction_up', 'javascript:' . $this->topFrame . '.we_cmd("move_up");', true, 100, 22, '', '', (($this->Model->Ordn > 0) ? false : true), false),
					we_html_button::create_button('image:direction_down', 'javascript:' . $this->topFrame . '.we_cmd("move_down");', true, 100, 22, '', '', (($this->Model->Ordn < ($_num - 1)) ? false : true), false)
					), 10, array(
					'style' => 'margin-left: 15px'
			)));
		} else {
			$_table->setColContent(0, 1, we_html_element::htmlHidden(array('name' => 'Ordn', 'value' => -1)));
		}
		// name and folder block
		// icen selector block
		$uniqname = 'weIconNaviAttrib';
		$wepos = (weGetCookieVariable("but_weIconNaviAttrib") === 'down' ? 'down' : 'right');

		return array(
			array(
				'headline' => g_l('navigation', '[general]'),
				'html' => we_html_element::htmlHidden(array('name' => 'newone', 'value' => ($this->Model->ID == 0 ? 1 : 0))) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Text', '', strtr($this->Model->Text, array_flip(get_html_translation_table(HTML_SPECIALCHARS))), '', 'style="width: ' . $this->_width_size . 'px;" onchange="' . $this->topFrame . '.mark();"'), g_l('navigation', '[name]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Display', '', $this->Model->Display, '', 'style="width: ' . $this->_width_size . 'px;" onchange="' . $this->topFrame . '.mark();"'), g_l('navigation', '[display]')) .
				$this->getHTMLChooser(g_l('navigation', '[group]'), NAVIGATION_TABLE, 0, 'ParentID', $_parentid, 'ParentPath', 'opener.' . $this->topFrame . '.mark()', we_base_ContentTypes::FOLDER, ($this->Model->IsFolder == 0 && $this->Model->Depended == 1)),
				'space' => $this->_space_size,
				'noline' => 1
			),
			array(
				'headline' => '',
				'html' => $_table->getHtml(),
				'space' => $this->_space_size,
				'noline' => 1
			),
			array(
				'headline' => '',
				'html' => $this->getHTMLChooser(
					g_l('navigation', '[icon]'), FILE_TABLE, 0, 'IconID', $this->Model->IconID, 'IconPath', 'opener.' . $this->topFrame . '.mark()', we_base_ContentTypes::IMAGE, false, true, 'folder,' . we_base_ContentTypes::IMAGE) . we_html_tools::getPixel($this->_width_size, 10) . '<table><tr><td>' . we_html_multiIconBox::getJS() . we_html_multiIconBox::_getButton(
					$uniqname, "weToggleBox('" . $uniqname . "','" . addslashes(g_l('navigation', '[icon_properties_out]')) . "','" . addslashes(
						g_l('navigation', '[icon_properties]')) . "')", $wepos, g_l('global', '[openCloseBox]')) . '</td><td><span style="cursor: pointer;" class="defaultfont" id="text_' . $uniqname . '" onclick="weToggleBox(\'' . $uniqname . '\',\'' . addslashes(
					g_l('navigation', '[icon_properties_out]')) . '\',\'' . addslashes(
					g_l('navigation', '[icon_properties]')) . '\');" >' . g_l('navigation', ($wepos === 'down' ? '[icon_properties_out]' : '[icon_properties]')) . '</span></td></tr></table>',
				'space' => $this->_space_size,
				'noline' => 1
			),
			array(
				'headline' => '',
				'html' => '<div id="table_' . $uniqname . '" style="display: ' . ($wepos === 'down' ? 'block' : 'none') . ';">' . $this->getHTMLImageAttributes() . '</div>',
				'space' => $this->_space_size + 50,
				'noline' => 1
			),
		);
	}

	function getHTMLPropertiesItem(){
		if($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
			$_seltype = array(
				we_navigation_navigation::STPYE_DOCTYPE => g_l('navigation', '[documents]')
			);
			if(defined('OBJECT_TABLE')){
				$_seltype[we_navigation_navigation::STPYE_CLASS] = g_l('navigation', '[objects]');
			}
			$_seltype[we_navigation_navigation::STPYE_CATEGORY] = g_l('navigation', '[categories]');
		} else {
			$_seltype = array(
				we_navigation_navigation::STPYE_DOCLINK => g_l('navigation', '[docLink]'),
				we_navigation_navigation::STYPE_URLLINK => g_l('navigation', '[urlLink]')
			);
			if(defined('OBJECT_TABLE')){
				$_seltype[we_navigation_navigation::STPYE_OBJLINK] = g_l('navigation', '[objLink]');
			}
			$_seltype[we_navigation_navigation::STPYE_CATLINK] = g_l('navigation', '[catLink]');
		}

		$_selection_block = $this->Model->Depended == 1 ? $this->getHTMLDependedProfile() : $this->View->htmlHidden(
				'CategoriesControl', we_base_request::_(we_base_request::INT, 'CategoriesCount', 0)) . $this->View->htmlHidden(
				'SortControl', we_base_request::_(we_base_request::INT, 'SortCount', 0)) . $this->View->htmlHidden(
				'CategoriesCount', (isset($this->Model->Categories) ? count($this->Model->Categories) : 0)) . $this->View->htmlHidden(
				'SortCount', (isset($this->Model->Sort) ? count($this->Model->Sort) : 0)) .
			'<div style="display: block;">' .
			we_html_tools::htmlSelect('Selection', array(
				we_navigation_navigation::SELECTION_DYNAMIC => g_l('navigation', '[dyn_selection]'),
				we_navigation_navigation::SELECTION_STATIC => g_l('navigation', '[stat_selection]')
				), 1, $this->Model->Selection, false, array('onchange' => 'closeAllSelection();toggle(this.value);setPresentation(this.value);setWorkspaces(\'\');' . $this->topFrame . '.mark();setCustomerFilter(this);onSelectionTypeChangeJS(\'' . we_navigation_navigation::STPYE_DOCTYPE . '\');'), 'value', $this->_width_size) . '<br />' . we_html_tools::htmlSelect(
				'SelectionType', $_seltype, 1, $this->Model->SelectionType, false, array('onchange' => 'closeAllType();clearFields();closeAllStats();toggle(this.value);setWorkspaces(this.value);onSelectionTypeChangeJS(this.value);setStaticSelection(this.value);' . $this->topFrame . '.mark();', 'style' => 'width: ' . $this->_width_size . 'px; margin-top: 5px;'), 'value', $this->_width_size) .
			'<div id="dynamic" style="' . ($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC ? 'display: block;' : 'display: none;') . '">' .
			$this->getHTMLDynamic() .
			'</div>
			<div id="static" style="' . ($this->Model->Selection == we_navigation_navigation::SELECTION_STATIC ? 'display: block;' : 'display: none;') . '">

				<div id="staticSelect" style="' . ($this->Model->SelectionType != we_navigation_navigation::STYPE_URLLINK ? 'display: block;' : 'display: none;') . '">' .
			$this->getHTMLStatic() .
			'</div>
				<div id="staticUrl" style="' . (($this->Model->SelectionType == we_navigation_navigation::STPYE_CATLINK || $this->Model->SelectionType == we_navigation_navigation::STYPE_URLLINK) ? 'display: block;' : 'display: none;') . ';margin-top:5px;">' .
			$this->getHTMLLink() .
			'</div>
				<div style="margin-top:5px;">' .
			we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Parameter', 58, $this->Model->Parameter, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size, 0), g_l('navigation', '[parameter]')) . '
				</div>
			</div>
			</div>';

		return array(
			array(
				'headline' => g_l('navigation', '[selection]'),
				'html' => $_selection_block,
				'space' => $this->_space_size,
				'noline' => 1
		));
	}

	function getHTMLPropertiesGroup(){
		$yuiSuggest = & weSuggest::getInstance();

		$rootDirID = 0;
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['LinkID'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['LinkPath'].value");
		$_cmd_doc = "javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $rootDirID . "','',0)";
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['LinkID'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['LinkPath'].value");
		$wecmdenc3 = we_base_request::encCmd("opener." . $this->topFrame . ".we_cmd('populateFolderWs');");
		$_cmd_obj = defined('OBJECT_TABLE') ? "javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "','objectFile',0)" : '';

		$_button_doc = we_html_button::create_button_table(array(
				we_html_button::create_button('select', $_cmd_doc, true, 0, 0, '', '', false),
				we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', 'javascript:openToEdit("' . FILE_TABLE . '",document.we_form.elements["LinkID"].value,"")', true, 100, 22, '', '', false)
				), 2);
		$_button_obj = we_html_button::create_button_table(array(
				we_html_button::create_button('select', $_cmd_obj, true, 0, 0, '', '', false),
				(defined('OBJECT_TABLE') ? we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', 'javascript:openToEdit("' . OBJECT_FILES_TABLE . '",document.we_form.elements["LinkID"].value,"")', true, 100, 22, '', '', false) : '')
				), 2);

		$_buttons = '<div id="docFolderLink" style="display: ' . ((empty($this->Model->FolderSelection) || $this->Model->FolderSelection == we_navigation_navigation::STPYE_DOCLINK) ? 'inline' : 'none') . '">' . $_button_doc . '</div><div id="objFolderLink" style="display: ' . ($this->Model->FolderSelection == we_navigation_navigation::STPYE_OBJLINK ? 'inline' : 'none') . '">' . $_button_obj . '</div>';
		$_path = ($this->Model->LinkID == 0 ?
				'' :
				id_to_path($this->Model->LinkID, ($this->Model->FolderSelection == we_navigation_navigation::STPYE_DOCLINK ? FILE_TABLE : (defined('OBJECT_TABLE') && $this->Model->FolderSelection == we_navigation_navigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE))));

		$_seltype = array(
			we_navigation_navigation::STPYE_DOCLINK => g_l('navigation', '[docLink]'),
			we_navigation_navigation::STYPE_URLLINK => g_l('navigation', '[urlLink]')
		);
		if(defined('OBJECT_TABLE')){
			$_seltype[we_navigation_navigation::STPYE_OBJLINK] = g_l('navigation', '[objLink]');
		}

		$yuiSuggest->setAcId('LinkPath');
		$yuiSuggest->setContentType(
			$this->Model->FolderSelection == we_navigation_navigation::STPYE_DOCLINK ?
				implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH, we_base_ContentTypes::QUICKTIME)) :
				(defined('OBJECT_TABLE') && $this->Model->FolderSelection == we_navigation_navigation::STPYE_OBJLINK ?
					'folder,objectFile' :
					implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH, we_base_ContentTypes::QUICKTIME))
			));
		$yuiSuggest->setInput('LinkPath', $_path, array("onchange" => $this->topFrame . ".mark();"));
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('LinkID', $this->Model->LinkID);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable($this->Model->FolderSelection == we_navigation_navigation::STPYE_DOCLINK ? FILE_TABLE : (defined('OBJECT_TABLE') && $this->Model->FolderSelection == we_navigation_navigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE));
		$yuiSuggest->setWidth($this->_width_size - 190);
		$yuiSuggest->setSelectButton($_buttons);
		$yuiSuggest->setTrashButton(we_html_button::create_button('image:btn_function_trash', 'javascript:document.we_form.elements["LinkID"].value=0;document.we_form.elements["LinkPath"].value="";', true, 27, 22));

		$weAcSelector = $yuiSuggest->getHTML();

		$_selection = we_html_element::jsElement('
	function openToEdit(tab,id,contentType){
	if(id>0){
    if(top.opener && top.opener.top.weEditorFrameController) {
     top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
    } else if(top.opener.top.opener && top.opener.top.opener.top.weEditorFrameController) {
     top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
    } else if(top.opener.top.opener.top.opener && top.opener.top.opener.top.opener.top.weEditorFrameController) {
     top.opener.top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
    }
   }}' . we_html_button::create_state_changer(false)) .
			'<div style="display: block;">' .
			we_html_tools::htmlSelect(
				'FolderSelection', $_seltype, 1, $this->Model->FolderSelection, false, array('onchange' => "onFolderSelectionChangeJS(this.value);setFolderSelection(this.value);" . $this->topFrame . ".mark();", 'style' => 'width: ' . $this->_width_size . 'px; margin-top: 5px;'), 'value', $this->_width_size) . '

		<div id="folderSelectionDiv" style="display: ' . ($this->Model->FolderSelection != we_navigation_navigation::STYPE_URLLINK ? 'block' : 'none') . ';margin-top: 5px;">' . $weAcSelector . '</div>

		</div>
		<div id="folderUrlDiv" style="display: ' . ($this->Model->FolderSelection == we_navigation_navigation::STYPE_URLLINK ? 'block' : 'none') . '; margin-top: 5px;">
			' . we_html_tools::htmlTextInput(
				'FolderUrl', 58, $this->Model->FolderUrl, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size, 0) . '
		</div>' .
			$this->getHTMLWorkspace('object', 0, 'FolderWsID') . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'FolderParameter', 58, $this->Model->FolderParameter, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size, 0) . '<br/>' . we_html_forms::checkboxWithHidden(
					$this->Model->CurrentOnUrlPar, 'CurrentOnUrlPar', g_l('navigation', '[current_on_urlpar]'), false, "defaultfont", $this->topFrame . '.mark();"'), g_l('navigation', '[parameter]'));

		$parts = array(
			array(
				'headline' => g_l('navigation', '[selection]'),
				'html' => $_selection,
				'space' => $this->_space_size,
				'noline' => 1
		));

		if(function_exists('mb_convert_encoding')){
			$parts[] = array(
				'headline' => g_l('navigation', '[charset]'),
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('navigation', '[charset_desc]'), we_html_tools::TYPE_INFO, $this->_width_size) . $this->getHTMLCharsetTable(),
				'space' => $this->_space_size,
				'noline' => 1
			);
		}

		// COPY FOLDER
		//$cmd = 'opener.'.$this->topFrame.'.mark()';
		if($this->Model->isnew){
			$_disabled = true;
			$_disabledNote = " " . g_l('weClass', '[availableAfterSave]');
			$_padding = "15";
		} else {
			$_disabled = false;
			$_disabledNote = "";
			$_padding = "10";
		}

		$cmd = 'opener.we_cmd("copyNaviFolder")';
		$wecmdenc1 = we_base_request::encCmd("document.we_form.CopyFolderID.value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.CopyFolderPath.value");

		$_cmd = "javascript:we_cmd('openNavigationDirselector',document.we_form.elements['CopyFolderID'].value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $cmd . "')";
		$_button_copyFolder = we_html_button::create_button('select', $_cmd, true, 100, 22, '', '', $_disabled);

		$parts[] = array(
			'headline' => g_l('weClass', '[copyFolder]'),
			'html' => we_html_element::jsElement("var selfNaviPath ='" . addslashes(
					$this->Model->Path) . "';\nvar selfNaviId = '" . $this->Model->ID . "';") . "<div style='float:left; margin-right:20px'>" . we_html_tools::htmlAlertAttentionBox(
				g_l('weClass', '[copy_owners_expl]') . $_disabledNote, we_html_tools::TYPE_INFO, ($this->_width_size - 120), true, 0) . "</div>" . "<div style='padding-top:{$_padding}px'>" . $_button_copyFolder . "</div>" . we_html_element::htmlHidden(
				array(
					'name' => 'CopyFolderID', "value" => ''
			)) . we_html_element::htmlHidden(array(
				'name' => 'CopyFolderPath', "value" => ''
			)),
			'space' => $this->_space_size,
			'noline' => 1
		);

		return $parts;
	}

	function getHTMLDependedProfile(){
		if($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
			$_seltype = array(
				we_navigation_navigation::STPYE_DOCTYPE => g_l('navigation', '[documents]')
			);
			if(defined('OBJECT_TABLE')){
				$_seltype[we_navigation_navigation::STPYE_CLASS] = g_l('navigation', '[objects]');
			}
		} else {
			$_seltype = array(
				we_navigation_navigation::STPYE_DOCLINK => g_l('navigation', '[docLink]')
			);
			if(defined('OBJECT_TABLE')){
				$_seltype[we_navigation_navigation::STPYE_OBJLINK] = g_l('navigation', '[objLink]');
			}
		}

		$_table = new we_html_table(
			array(
			'width' => $this->_width_size,
			'cellpadding' => 0,
			'cellspacing' => 2,
			'border' => 0,
			'class' => 'defaultfont'
			), 5, 2);

		$_table->setColContent(0, 0, g_l('navigation', '[stat_selection]'));
		$_table->setColContent(1, 0, g_l('navigation', ($this->Model->SelectionType == we_navigation_navigation::STPYE_CATLINK ? '[catLink]' : ($this->Model->SelectionType == we_navigation_navigation::STPYE_DOCLINK ? '[docLink]' : '[objLink]'))) . ':');
		$_table->setColContent(1, 1, id_to_path($this->Model->LinkID, ($this->Model->SelectionType == we_navigation_navigation::STPYE_CATLINK ? CATEGORY_TABLE : ($this->Model->SelectionType == we_navigation_navigation::STPYE_DOCLINK ? FILE_TABLE : OBJECT_FILES_TABLE))));

		if(!empty($this->Model->Url) && $this->Model->Url != 'http://'){
			$_table->setColContent(2, 0, g_l('navigation', '[linkSelection]') . ':');
			$_table->setColContent(2, 1, $this->Model->Url);
		} elseif(!empty($this->Model->UrlID) && $this->Model->UrlID){
			$_table->setColContent(2, 0, g_l('navigation', '[linkSelection]') . ':');
			$_table->setColContent(2, 1, id_to_path($this->Model->UrlID));
		}

		if($this->Model->SelectionType == we_navigation_navigation::STPYE_CATLINK){
			$_table->setColContent(3, 0, g_l('navigation', '[catParameter]') . ':');
			$_table->setColContent(3, 1, $this->Model->CatParameter);
		}

		if(!empty($this->Model->Parameter)){
			$_table->setColContent(4, 0, g_l('navigation', '[parameter]') . ':');
			$_table->setColContent(4, 1, $this->Model->Parameter);
		}

		return $_table->getHtml();
	}

	function getHTMLDynamic(){
		$dtq = we_docTypes::getDoctypeQuery($this->db);
		$this->db->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
		$docTypes = array(g_l('navigation', '[no_entry]')) + $this->db->getAllFirst(false);

		$classID2Name = $classID2Dir = $classDirs = $classDirsJS = $classHasSubDirsJS = $classPathsJS = array();
		$allowedClasses = we_users_util::getAllowedClasses($this->db);

		$_firstClass = 0;
		if(defined('OBJECT_TABLE') && $allowedClasses){
			$this->db->query('SELECT DISTINCT o.ID,o.Text,o.Path,of.ID AS classDirID FROM ' . OBJECT_TABLE . ' o JOIN ' . OBJECT_FILES_TABLE . ' of ON (o.ID=of.TableID) WHERE of.IsClassFolder=1 AND o.ID IN(' . implode(',', $allowedClasses) . ')');
			while($this->db->next_record()){
				if(!$_firstClass){
					$_firstClass = $this->db->f('ID');
				}
				$classID2Name[$this->db->f('ID')] = $this->db->f('Text');
				$classID2Dir[$this->db->f('classDirID')] = $this->db->f('ID');
				$classDirs[] = $this->db->f('classDirID');
				$classHasSubDirsJS[$this->db->f('ID')] = $this->db->f('ID') . ':false';
				$classDirsJS[] = $this->db->f('ID') . ':' . $this->db->f('classDirID');
				$classPathsJS[] = $this->db->f('ID') . ':"' . $this->db->f('Path') . '"';
			}
			if($classDirs){
				$this->db->query('SELECT ID,ParentID FROM ' . OBJECT_FILES_TABLE . ' WHERE ParentID IN (' . implode(',', $allowedClasses) . ') AND IsFolder=1');
				while($this->db->next_record()){
					$classHasSubDirsJS[$classID2Dir[$this->db->f('ParentID')]] = $classID2Dir[$this->db->f('ParentID')] . ':true';
				}
			}
		}

		/* $_wsid = ($this->Model->SelectionType == we_navigation_navigation::STPYE_OBJLINK && $this->Model->LinkID ?
		  we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID) :
		  array());
		 */

		$_sortVal = isset($this->Model->Sort[0]['field']) ? $this->Model->Sort[0]['field'] : "";
		$_sortOrder = isset($this->Model->Sort[0]['order']) ? $this->Model->Sort[0]['order'] : "ASC";

		$_sortSelect = we_html_tools::htmlSelect(
				"SortOrder", array(
				"ASC" => g_l('navigation', '[ascending]'), "DESC" => g_l('navigation', '[descending]')
				), 1, $_sortOrder, false, array('onchange' => $this->topFrame . '.mark();'), "value", 120);

		return we_html_element::jsElement('
var classDirs = {' . implode(',', $classDirsJS) . '};
var classPaths = {' . implode(',', $classPathsJS) . '};
var hasClassSubDirs = {' . implode(',', $classHasSubDirsJS) . '};') . '

<div style="display: block;">
	<div id="doctype" style="' . ($this->Model->SelectionType == we_navigation_navigation::STPYE_DOCTYPE ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . 'px;margin-top:5px;">' .
			we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect(
					'DocTypeID', $docTypes, 1, $this->Model->DocTypeID, false, array('onchange' => 'clearFields();' . $this->topFrame . '.mark();'), 'value', $this->_width_size), g_l('navigation', '[doctype]')) . '
	</div>
	<div id="classname" style="' . ($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . 'px;margin-top:5px;">' .
			(defined('OBJECT_TABLE') ? we_html_tools::htmlFormElementTable(
					we_html_tools::htmlSelect(
						'ClassID', $classID2Name, 1, $this->Model->ClassID, false, array('onchange' => "clearFields();onSelectionClassChangeJS(this.value);"), 'value', $this->_width_size), g_l('navigation', '[class]')) . $this->getHTMLWorkspace('class', $_firstClass) : '') . '
	</div>
	<div id="fieldChooser" style="' . ($this->Model->SelectionType != we_navigation_navigation::STPYE_CATEGORY ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . 'px;margin-top: 5px;">' .
			$this->getHTMLFieldChooser(g_l('navigation', '[title_field]'), 'TitleField', $this->Model->TitleField, 'putTitleField', $this->Model->SelectionType, ($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS ? $this->Model->ClassID : $this->Model->DocTypeID)) . '
	</div>' .
			$this->getHTMLDirSelector() . '
	<div id="catSort" style="' . ($this->Model->SelectionType != we_navigation_navigation::STPYE_CATEGORY ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . 'px;">' .
			$this->getHTMLCategory() .
			$this->getHTMLFieldChooser(g_l('navigation', '[sort]'), 'SortField', $_sortVal, 'putSortField', $this->Model->SelectionType, ($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS ? $this->Model->ClassID : $this->Model->DocTypeID), $_sortSelect, 120) . '
	</div>
	<div id="dynUrl" style="' . ($this->Model->SelectionType == we_navigation_navigation::STPYE_CATEGORY ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . 'px;">' .
			$this->getHTMLLink('dynamic_') . '
	</div>
	<div style="width: ' . $this->_width_size . 'px;margin-top: 5px;">' .
			we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('dynamic_Parameter', 58, $this->Model->Parameter, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size, 0), g_l('navigation', '[parameter]')) . '
	</div>' .
			$this->getHTMLCount() .
			we_html_button::create_button_table(
				array(
				we_html_button::create_button(
					'preview', 'javascript:' . $this->topFrame . '.we_cmd("dyn_preview");'),
				($this->Model->hasDynChilds() ? we_html_button::create_button(
						'delete_all', 'javascript:' . $this->topFrame . '.we_cmd("depopulate");') : '')
				)
				, 10, array('style' => 'margin-top:20px;'
			)) . '
</div>';
	}

	function getHTMLStatic($disabled = false){
		$seltype = array(
			we_navigation_navigation::STPYE_DOCLINK => g_l('navigation', '[docLink]')
		);
		if(defined('OBJECT_TABLE')){
			$seltype[we_navigation_navigation::STPYE_OBJLINK] = g_l('navigation', '[objLink]');
		}

		$rootDirID = 0;

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['LinkID'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['LinkPath'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.switch_button_state('open_navigation_doc', '', opener.document.we_form.elements['LinkID'].value>0?'enabled':'disabled');");

		$_cmd_doc = "javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "',''," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['LinkID'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['LinkPath'].value");
		$wecmdenc3 = we_base_request::encCmd('opener.' . $this->topFrame . ".we_cmd('populateWorkspaces');opener.switch_button_state('open_navigation_obj', '', opener.document.we_form.elements['LinkID'].value>0?'enabled':'disabled');");
		$_cmd_obj = defined('OBJECT_TABLE') ? "javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "',''," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")" : '';
		$_cmd_cat = "javascript:we_cmd('openCatselector',document.we_form.elements['LinkID'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'LinkID\\'].value','document.we_form.elements[\\'LinkPath\\'].value','opener." . $this->topFrame . ".we_cmd(\"populateText\");opener." . $this->topFrame . ".mark();','','" . $rootDirID . "')";

		$_button_doc = we_html_button::create_button_table(array(
				we_html_button::create_button('select', $_cmd_doc, true, 0, 0, '', '', $disabled),
				we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', 'javascript:openToEdit("' . FILE_TABLE . '",document.we_form.elements["LinkID"].value,"")', true, 100, 22, '', '', $disabled, false, '_navigation_doc')
				), 2);

		$_button_obj = we_html_button::create_button_table(array(
				we_html_button::create_button('select', $_cmd_obj, true, 0, 0, '', '', $disabled),
				(defined('OBJECT_TABLE') ? we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', 'javascript:openToEdit("' . OBJECT_FILES_TABLE . '",document.we_form.elements["LinkID"].value,"")', true, 100, 22, '', '', $disabled, false, '_navigation_obj') : '')
				), 2);
		$_button_cat = we_html_button::create_button('select', $_cmd_cat, true, 0, 0, '', '', $disabled);

		$_buttons = '<div id="docLink" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STPYE_DOCLINK ? 'inline' : 'none') . '">' . $_button_doc . '</div><div id="objLink" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STPYE_OBJLINK ? 'inline' : 'none') . '">' . $_button_obj . '</div><div id="catLink" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STPYE_CATLINK ? 'inline' : 'none') . '">' . $_button_cat . '</div>';
		$_path = ($this->Model->LinkID == 0 ?
				'' :
				id_to_path($this->Model->LinkID, ($this->Model->SelectionType == we_navigation_navigation::STPYE_DOCLINK ? FILE_TABLE : ($this->Model->SelectionType == we_navigation_navigation::STPYE_CATLINK ? CATEGORY_TABLE : (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : ''))))
			);

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("LinkPath", "");
		$yuiSuggest->setContentType(
			$this->Model->SelectionType == we_navigation_navigation::STPYE_DOCLINK ?
				implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH, we_base_ContentTypes::QUICKTIME)) :
				($this->Model->SelectionType === 'folder,objectFile' ? OBJECT_FILES_TABLE : ''));
		$yuiSuggest->setInput('LinkPath', $_path, array(
			"onchange" => $this->topFrame . ".mark();"
		));
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('LinkID', $this->Model->LinkID);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable(
			$this->Model->SelectionType == we_navigation_navigation::STPYE_DOCLINK ? FILE_TABLE : ($this->Model->SelectionType == we_navigation_navigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : CATEGORY_TABLE));
		$yuiSuggest->setWidth($this->_width_size - 150);
		$yuiSuggest->setSelectButton($_buttons);

		return we_html_element::jsElement('
	function openToEdit(tab,id,contentType){
		if(id>0){
    if(top.opener && top.opener.top.weEditorFrameController) {
     top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
    } else if(top.opener.top.opener && top.opener.top.opener.top.weEditorFrameController) {
     top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
    } else if(top.opener.top.opener.top.opener && top.opener.top.opener.top.opener.top.weEditorFrameController) {
     top.opener.top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
    }
		}
   }' . we_html_button::create_state_changer(false)) .
			'<div style="margin-top:5px">' . $yuiSuggest->getHTML() . '</div>' . $this->getHTMLWorkspace();
	}

	function getHTMLTab2(){
		/* if($this->Model->hasDynChilds()) {
		  return $this->getHTMLContentInfo();
		  } */

		$_seltype = array(
			we_navigation_navigation::STPYE_DOCTYPE => g_l('navigation', '[documents]')
		);

		if(defined('OBJECT_TABLE')){
			$_seltype[we_navigation_navigation::STPYE_CLASS] = g_l('navigation', '[objects]');
		}

		$_seltype[we_navigation_navigation::STPYE_CATEGORY] = g_l('navigation', '[categories]');

		$_selection_block = $this->View->htmlHidden('CategoriesControl', we_base_request::_(we_base_request::INT, 'CategoriesCount', 0)) .
			$this->View->htmlHidden('SortControl', we_base_request::_(we_base_request::INT, 'SortCount', 0)) .
			$this->View->htmlHidden('CategoriesCount', (isset($this->Model->Categories) ? count($this->Model->Categories) : '0')) .
			$this->View->htmlHidden('SortCount', (isset($this->Model->Sort) ? count($this->Model->Sort) : '0')) .
			'<div style="display: block;">
				<div style="display:block;">
				' . we_html_tools::htmlSelect(
				'Selection', array(
				we_navigation_navigation::SELECTION_NODYNAMIC => g_l('navigation', '[no_dyn_content]'),
				we_navigation_navigation::SELECTION_DYNAMIC => g_l('navigation', '[dyn_content]')
				), 1, $this->Model->Selection, false, array('style' => 'width: ' . $this->_width_size . 'px;', 'onchange' => 'toggle(\'dynamic\');setPresentation(\'dynamic\');setWorkspaces(\'\');' . $this->topFrame . '.mark();setCustomerFilter(this);onSelectionTypeChangeJS(\'' . we_navigation_navigation::STPYE_DOCTYPE . '\');'), 'value', $this->_width_size) . '
				</div>
				<div id="dynamic" style="' . ($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC ? 'display: block;' : 'display: none;') . ';margin-top:5px">
				' . we_html_tools::htmlSelect(
				'SelectionType', $_seltype, 1, $this->Model->SelectionType, false, array('onchange' => "closeAllType();clearFields();toggle(this.value);setWorkspaces(this.value);onSelectionTypeChangeJS(this.value);setStaticSelection(this.value);" . $this->topFrame . '.mark();'), 'value', $this->_width_size) . $this->getHTMLDynamic() . '</div>
			</div>';

		return array(
			array(
				'headline' => g_l('navigation', '[content]'),
				'html' => $_selection_block,
				'space' => $this->_space_size
		));
	}

	function getHTMLContentInfo(){
		$_sort = array();
		foreach($this->Model->Sort as $_i){
			$_sort[] = $_i['field'] . '&nbsp;(' . g_l('navigation', ($_i['order'] === 'DESC' ? '[descending]' : '[ascending]')) . ')';
		}

		$_table = new we_html_table(
			array(
			'width' => $this->_width_size,
			'cellpadding' => 0,
			'cellspacing' => 0,
			'border' => 0,
			'class' => 'defaultfont'
			), 9, 2);

		if($this->Model->SelectionType == we_navigation_navigation::STPYE_DOCTYPE){

			$_table->setCol(0, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[documents]'));

			if(!empty($this->Model->DocTypeID)){
				$_dt = f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($this->Model->DocTypeID), '', new DB_WE());
				$_table->setCol(1, 0, array(
					'style' => 'font-weight: bold;'
					), g_l('navigation', '[doctype]') . ':');
				$_table->setColContent(1, 1, $_dt);
			}
		} elseif($this->Model->SelectionType == we_navigation_navigation::STPYE_CATEGORY){
			$_table->setCol(0, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[categories]'));
		} else {
			$_table->setCol(0, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[objects]'));
			$_cn = f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->Model->ClassID), 'Text', new DB_WE());
			$_table->setCol(1, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[class]') . ':');
			$_table->setColContent(1, 1, $_cn);

			$_table->setCol(2, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[workspace]') . ':');
			$_table->setColContent(2, 1, id_to_path($this->Model->WorkspaceID));
		}

		if($this->Model->SelectionType != we_navigation_navigation::STPYE_CATEGORY && !empty($this->Model->TitleField)){
			$_table->setCol(3, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[title_field]') . ':');
			$_table->setColContent(3, 1, $this->Model->TitleField);
		}

		$_table->setCol(4, 0, array(
			'style' => 'font-weight: bold;'
			), g_l('navigation', '[dir]') . ':');
		switch($this->Model->SelectionType){
			case we_navigation_navigation::STPYE_DOCTYPE:
				$_table->setColContent(4, 1, id_to_path($this->Model->FolderID, FILE_TABLE));
				break;
			case we_navigation_navigation::STPYE_CATEGORY:
				$_table->setColContent(4, 1, id_to_path($this->Model->FolderID, CATEGORY_TABLE));
				break;
			default:
				if(defined('OBJECT_FILES_TABLE')){
					$_table->setColContent(4, 1, id_to_path($this->Model->FolderID, OBJECT_FILES_TABLE));
				}
		}
		if($this->Model->SelectionType != we_navigation_navigation::STPYE_CATEGORY){
			if($this->Model->Categories){
				$_table->setCol(5, 0, array(
					'style' => 'font-weight: bold;'
					), g_l('navigation', '[categories]') . ':');
				$_table->setColContent(5, 1, implode('<br />', $this->Model->Categories));
			}

			if($_sort){
				$_table->setCol(6, 0, array(
					'style' => 'font-weight: bold;'
					), g_l('navigation', '[sort]') . ':');
				$_table->setColContent(6, 1, implode('<br />', $_sort));
			}
		}

		if($this->Model->Url && $this->Model->Url != 'http://'){
			$_table->setCol(7, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[urlLink]') . ':');
			$_table->setColContent(7, 1, $this->Model->Url);
		}

		if($this->Model->Paramter){
			$_table->setCol(8, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[parameter]') . ':');
			$_table->setColContent(8, 1, $this->Model->Parameter);
		}

		$_table->setCol(8, 0, array(
			'style' => 'font-weight: bold;'
			), g_l('navigation', '[show_count]') . ':');
		$_table->setColContent(8, 1, $this->Model->ShowCount);

		return array(
			array(
				'headline' => g_l('navigation', '[entries]'),
				'html' => we_html_tools::htmlSelect('dynContent', $this->View->getItems($this->Model->ID), 20, '', false, array('style' => 'width: ' . $this->_width_size . 'px; height: 200px;  margin: 0px 0px 5px 0px;')),
				'space' => $this->_space_size,
				'noline' => 0
			),
			array(
				'headline' => '',
				'html' => we_html_button::create_button_table(
					array(
						we_html_button::create_button('preview', 'javascript:' . $this->topFrame . '.we_cmd("dyn_preview");'),
						we_html_button::create_button('refresh', 'javascript:' . $this->topFrame . '.we_cmd("populate");'),
						we_html_button::create_button('delete_all', 'javascript:' . $this->topFrame . '.we_cmd("depopulate");')
				)),
				'space' => $this->_space_size
			),
			array(
				'headline' => g_l('navigation', '[content]'),
				'html' => $_table->getHTML(),
				'space' => $this->_space_size
			),
		);
	}

	function getHTMLEditorPreview(){
		// build the page
		$out = '<table border="0" class="defaultfont" cellpadding="0" cellspacing="0">
		<tr>
			<td><iframe name="preview" style="background: white; border: 1px solid black; width: 640px; height: 150px" src="edit_navigation_frameset.php?pnt=previewIframe"></iframe></td>
		</tr>
		<tr>
			<td height="30"><label for="previewCode">' . g_l('navigation', '[preview_code]') . '</label><br /></td>
		<tr>
			<td>' . we_html_element::htmlTextArea(
				array(
				'name' => 'previewCode',
				'id' => 'previewCode',
				'style' => 'width: 640px; height: 200px;',
				'class' => 'defaultfont'
				), $this->Model->previewCode) . '</td>
		</tr>
		<tr>
			<td height="10"></td>
		<tr>
		<tr>
			<td align="right">' . we_html_button::create_button_table(
				array(
					we_html_button::create_button('refresh', 'javascript: showPreview();'),
					we_html_button::create_button(
						'reset', 'javascript: document.getElementById("previewCode").value = "' . str_replace(array("\r\n", "\n"), '\n', addslashes(we_navigation_navigation::defaultPreviewCode)) . '"; showPreview();')
				)//,we_button::create_button('new_template', 'javascript: '.$this->topFrame.'.we_cmd("create_template");')
			) . '</td>
		</tr>
		</table>';

		return we_html_element::jsElement('
function showPreview() {
	document.we_form.pnt.value="previewIframe";
	submitForm("preview");
}') . $this->View->getCommonHiddens(array(
				'pnt' => 'preview', 'tabnr' => 'preview'
			)) . we_html_tools::htmlDialogLayout($out, g_l('navigation', '[preview]'));
	}

	function getHTMLEditorPreviewIframe(){
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
		$tabNr = we_base_request::_(we_base_request::STRINGC, 'tabnr', 1); //FIXME: due to preview - fix this as a better tab-name; replace 1-3 with consts

		if($this->Model->IsFolder == 0 && $tabNr != 1 && $tabNr != 3){
			$tabNr = 1;
		}

		$out = we_html_element::jsElement('
function onFolderSelectionChangeJS(value) {
	var linktype = value == "' . we_navigation_navigation::STPYE_DOCLINK . '" ? "docLink" : (value == "' . we_navigation_navigation::STPYE_CATLINK . '" ? "catLink" : (value == "' . we_navigation_navigation::STPYE_OBJLINK . '" ? "' . (defined('OBJECT_TABLE') ? 'objLink' : '') . '" : "docLink"));
	YAHOO.autocoml.modifySetById("yuiAcInputLinkPath",{
			table : linktype == "docLink" ? "' . FILE_TABLE . '" : (linktype == "objLink" ? "' . (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE') . '" : (linktype == "catLink" ? "' . CATEGORY_TABLE . '": "")),
			cTypes : linktype == "docLink" ? "folder,' . we_base_ContentTypes::XML . ',' . we_base_ContentTypes::WEDOCUMENT . ',' . we_base_ContentTypes::IMAGE . ',' . we_base_ContentTypes::HTML . ',' . we_base_ContentTypes::APPLICATION . ',' . we_base_ContentTypes::FLASH . ',' . we_base_ContentTypes::QUICKTIME . '" : (linktype == "objLink" ? "folder,objectFile": "")
		}
	);
}

function onSelectionTypeChangeJS(value) {
	if(document.we_form.elements[\'Selection\'].value=="' . we_navigation_navigation::SELECTION_STATIC . '"){
		onFolderSelectionChangeJS(value);
	} else {
		var objects = ' . defined('OBJECT_FILES_TABLE') . ';
		if(objects == 1 && value=="' . we_navigation_navigation::STPYE_CLASS . '"){
			document.we_form.elements[\'ClassID\'].selectedIndex = 0;
			onSelectionClassChangeJS(document.we_form.elements[\'ClassID\'].options[0].value);
		} else{
			YAHOO.autocoml.modifySetById("yuiAcInputFolderPath",{
					table : value == "' . we_navigation_navigation::STPYE_DOCTYPE . '" ? "' . FILE_TABLE . '" : "' . CATEGORY_TABLE . '",
					rootDir : "",
					mayBeEmpty : value == "' . we_navigation_navigation::STPYE_DOCTYPE . '" ? true : false
				}
			);
		}
		YAHOO.autocoml.setValidById("yuiAcInputFolderPath");
	}
}

function onSelectionClassChangeJS(value) {
	YAHOO.autocoml.modifySetById("yuiAcInputFolderPath",{
			table : "trunk_tblObjectFiles",
			rootDir : classPaths[value],
			mayBeEmpty : false
		}
	);
	document.we_form.elements["FolderID"].value=classDirs[value];
	document.we_form.elements["FolderPath"].value=classPaths[value];
	document.we_form.elements["FolderPath"].disabled=!hasClassSubDirs[value];
	' . $this->topFrame . '.we_cmd(\'populateWorkspaces\');
	' . $this->topFrame . '.mark();
}
');

		$hiddens = array(
			'cmd' => '',
			'pnt' => 'edbody',
			'tabnr' => $tabNr,
			'vernr' => we_base_request::_(we_base_request::INT, 'vernr', 0),
			'delayParam' => we_base_request::_(we_base_request::INT, 'delayParam', 0)
		);

		$yuiSuggest = & weSuggest::getInstance();
		if($tabNr === 'preview'){
			$out .= $this->getHTMLEditorPreview();
		} else {
			// Property tab content
			$out .= weSuggest::getYuiJsFiles() .
				we_html_element::htmlDiv(
					array(
					'id' => 'tab1', 'style' => ($tabNr == 1 ? 'display: block;' : 'display: none')
					), $this->View->getCommonHiddens($hiddens) .
					$this->View->htmlHidden(
						'IsFolder', (isset($this->Model->IsFolder) ? $this->Model->IsFolder : 0)) . $this->View->htmlHidden(
						'presetFolder', we_base_request::_(we_base_request::STRING, 'presetFolder', '')) .
					we_html_multiIconBox::getHTML(
						'', '100%', $this->getHTMLGeneral(), 30, '', -1, '', '', false, $preselect) .
					($this->Model->IsFolder ? we_html_multiIconBox::getHTML(
							'', '100%', $this->getHTMLPropertiesGroup(), 30, '', -1, '', '', false, $preselect) : we_html_multiIconBox::getHTML(
							'', '100%', $this->getHTMLPropertiesItem(), 30, '', -1, '', '', false, $preselect)) . (($this->Model->Selection == we_navigation_navigation::SELECTION_STATIC || $this->Model->IsFolder) ? $this->getHTMLAttributes() : '')) . ($this->Model->IsFolder ? (we_html_element::htmlDiv(
						array(
						'id' => 'tab2', 'style' => ($tabNr == 2 ? 'display: block;' : 'display: none')
						), we_html_multiIconBox::getHTML(
							'', '100%', $this->getHTMLTab2(), 30, '', -1, '', '', false, $preselect))) :
					'') . ((defined('CUSTOMER_TABLE')) ? we_html_element::htmlDiv(
						array(
						'id' => 'tab3', 'style' => ($tabNr == 3 ? 'display: block;' : 'display: none')
						), we_html_multiIconBox::getHTML(
							'', '100%', $this->getHTMLTab3(), 30, '', -1, '', '', false, $preselect)) : '');
		}
		$out .= $yuiSuggest->getYuiCss() .
			$yuiSuggest->getYuiJs();
		return $out;
	}

	function htmlTextInput($name, $size = 24, $value = "", $maxlength = "", $attribs = "", $type = "text", $width = 0, $height = 0, $markHot = "", $disabled = false){
		$style = ($width || $height) ? (' style="' . ($width ? ('width: ' . $width . ((strpos($width, "px") || strpos(
					$width, "%")) ? "" : "px") . ';') : '') . ($height ? ('height: ' . $height . ((strpos($height, "px") || strpos(
					$height, "%")) ? "" : "px") . ';') : '') . '"') : '';
		return '<input' . ($markHot ? ' onchange="if(_EditorFrame){_EditorFrame.setEditorIsHot(true);}' . $markHot . '.hot=1;"' : '') . (strstr(
				$attribs, "class=") ? "" : ' class="wetextinput"') . ' type="' . trim($type) . '" name="' . trim($name) . '" size="' . abs(
				$size) . '" value="' . oldHtmlspecialchars($value) . '"' . ($maxlength ? (' maxlength="' . abs(
					$maxlength) . '"') : '') . ($attribs ? " $attribs" : '') . $style . ($disabled ? (' disabled="true"') : '') . ' />';
	}

	function getHTMLFieldChooser($title, $name, $value, $cmd, $type, $selection, $extraField = "", $extraFieldWidth = 0){
		$_disabled = !(($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS && $this->Model->ClassID != 0) || ($this->Model->SelectionType == we_navigation_navigation::STPYE_DOCTYPE && $this->Model->DocTypeID != 0));
		$_cmd = "javascript:var st=document.we_form.SelectionType.options[document.we_form.SelectionType.selectedIndex].value; var s=(st=='" . we_navigation_navigation::STPYE_DOCTYPE . "' ? document.we_form.DocTypeID.options[document.we_form.DocTypeID.selectedIndex].value : document.we_form.ClassID.options[document.we_form.ClassID.selectedIndex].value); we_cmd('openFieldSelector','" . $cmd . "',st,s,0)";
		$_button = we_html_button::create_button('select', $_cmd, true, 100, 22, '', '', $_disabled, false, "_$name");
		if(!$extraField){
			$showValue = stristr($value, "_") ? substr($value, strpos($value, "_") + 1) : $value;
			return we_html_tools::htmlFormElementTable(
					we_html_tools::hidden($name, $value) . $this->htmlTextInput(
						"__" . $name, 58, $showValue, '', 'onchange="setFieldValue(\'' . $name . '\',this); ' . $this->topFrame . '.mark();"', 'text', ($this->_width_size - 120), 0), $title, 'left', 'defaultfont', '', we_html_tools::getPixel(20, 4), $_button);
		} else {
			$showValue = stristr($value, "_") ? substr($value, strpos($value, "_") + 1) : $value;
			return we_html_tools::htmlFormElementTable(
					we_html_tools::hidden($name, $value) . $this->htmlTextInput(
						"__" . $name, 58, $showValue, '', 'onchange="setFieldValue(\'' . $name . '\',this); ' . $this->topFrame . '.mark();"', 'text', ($this->_width_size - 120) - abs($extraFieldWidth) - 8, 0), $title, 'left', 'defaultfont', '', we_html_tools::getPixel(20, 4), $extraField, we_html_tools::getPixel(10, 4), $_button);
		}
	}

	private function getHTMLChooser($title, $table = FILE_TABLE, $rootDirID = 0, $IDName = 'ID', $IDValue = '', $PathName = 'Path', $cmd = '', $filter = we_base_ContentTypes::WEDOCUMENT, $disabled = false, $showtrash = false, $acCTypes = ""){
		if($IDValue == 0){
			$_path = '/';
		} elseif(isset($this->Model->$IDName) && !empty($this->Model->$IDName)){
			$_path = id_to_path($this->Model->$IDName, $table);
		} else {
			$acQuery = new we_selector_query();
			if($IDValue !== ""){
				$acResponse = $acQuery->getItemById($IDValue, $table, array(
					"IsFolder", "Path"
				));
				if($acResponse && $acResponse[0]['Path']){
					$_path = $acResponse[0]['Path'];
				} else {
					// return with errormessage
				}
			} else {
				$_path = "";
			}
		}

		$wecmdenc1 = we_base_request::encCmd("document.we_form.$IDName.value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.$PathName.value");

		if($table == NAVIGATION_TABLE){
			$_cmd = "javascript:we_cmd('openNavigationDirselector',document.we_form.elements['" . $IDName . "'].value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $cmd . "')";
			$_selector = weSuggest::DirSelector;
		} else if($filter == we_base_ContentTypes::FOLDER){
			$_cmd = "javascript:we_cmd('openSelector',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $cmd . "','','" . $rootDirID . "')";
			$_selector = weSuggest::DirSelector;
		} else {
			$wecmdenc1 = we_base_request::encCmd("document.we_form.$IDName.value");
			$wecmdenc2 = we_base_request::encCmd("document.we_form.$PathName.value");
			$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
			$_cmd = "javascript:we_cmd('openDocselector',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "','" . $filter . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";
			$_selector = weSuggest::DocSelector;
		}

		if($_selector == weSuggest::DocSelector){
			$_path = $_path === '/' ? "" : $_path;
			$mayBeEmpty = 1;
		} else {
			$mayBeEmpty = 0;
		}

		if($showtrash){
			$_button = we_html_button::create_button_table(
					array(
					we_html_button::create_button('select', $_cmd, true, 100, 22, '', '', $disabled),
					we_html_button::create_button('image:btn_function_trash', 'javascript:document.we_form.elements["' . $IDName . '"].value=0;document.we_form.elements["' . $PathName . '"].value="/";', true, 27, 22)
					), 10);
			$_width = 157;
		} else {
			$_width = 120;
			$_button = we_html_button::create_button('select', $_cmd, true, 100, 22, '', '', $disabled);
		}

		$yuiSuggest = &weSuggest::getInstance();
		$yuiSuggest->setAcId($PathName);
		$yuiSuggest->setContentType($acCTypes ? : $filter);
		$yuiSuggest->setInput($PathName, $_path, array(
			'onchange' => $this->topFrame . ".mark();"
		));
		$yuiSuggest->setLabel($title);
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty($mayBeEmpty);
		$yuiSuggest->setResult($IDName, $IDValue);
		$yuiSuggest->setSelector($_selector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth($this->_width_size - $_width);
		$yuiSuggest->setSelectButton($_button);

		$weAcSelector = $yuiSuggest->getHTML();
		return (isset($weAcSelector) ?
				$weAcSelector :
				we_html_tools::htmlFormElementTable(
					we_html_tools::htmlTextInput($PathName, 58, $_path, '', 'readonly', 'text', ($this->_width_size - $_width), 0), $title, 'left', 'defaultfont', we_html_element::htmlHidden(array(
						'name' => $IDName, 'value' => $IDValue
					)), we_html_tools::getPixel(20, 4), $_button)
			);
	}

	function getHTMLCategory(){
		$addbut = we_html_button::create_button("add", "javascript:we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener.addCat(top.allPaths);opener." . $this->topFrame . ".mark();')");
		$del_but = addslashes(
			we_html_element::htmlImg(
				array(
					'src' => BUTTONS_DIR . 'btn_function_trash.gif',
					'onclick' => 'javascript:#####placeHolder#####;' . $this->topFrame . '.mark();',
					'style' => 'cursor: pointer; width: 27px;'
		)));

		$variant_js = '
var categories_edit = new multi_edit("categories",document.we_form,0,"' . $del_but . '",' . ($this->_width_size - 10) . ',false);
categories_edit.addVariant();
			document.we_form.CategoriesControl.value = categories_edit.name;';

		if(is_array($this->Model->Categories)){
			foreach($this->Model->Categories as $cat){

				$variant_js .= '
categories_edit.addItem();
categories_edit.setItem(0,(categories_edit.itemCount-1),"' . $cat . '");';
			}
		}

		$variant_js .= 'categories_edit.showVariant(0);';

		$js = we_html_element::jsElement($variant_js);

		$table = new we_html_table(
			array(
			'id' => 'CategoriesBlock',
			'style' => 'display: block;',
			'cellpadding' => 0,
			'cellspacing' => 0,
			'border' => 0
			), 6, 2
		);

		$table->setCol(0, 0, array('colspan' => 2), we_html_tools::getPixel(3, 3));
		$table->setCol(1, 0, array('colspan' => 2, 'class' => 'defaultfont'), g_l('navigation', '[categories]'));
		$table->setCol(2, 0, array('colspan' => 2), we_html_element::htmlDiv(
				array(
					'id' => 'categories',
					'class' => 'blockWrapper',
					'style' => 'width: ' . ($this->_width_size) . 'px; height: 60px; border: #AAAAAA solid 1px;'
				)
			)
		);
		$table->setCol(3, 0, array('colspan' => 2), we_html_tools::getPixel(5, 5));
		$table->setCol(
			4, 0, array('align' => 'left'), we_html_forms::checkboxWithHidden($this->Model->CatAnd, "CatAnd", g_l('navigation', '[catAnd]'))
		);
		$table->setCol(
			4, 1, array('align' => 'right'), we_html_button::create_button_table(
				array(we_html_button::create_button("delete_all", "javascript:removeAllCats()"), $addbut)
			)
		);
		$table->setCol(5, 0, array('colspan' => 2), we_html_tools::getPixel(3, 3));

		return $table->getHtml() . $js . we_html_element::jsElement('
function removeAllCats(){
	' . $this->topFrame . '.mark();
	if(categories_edit.itemCount>0){
		while(categories_edit.itemCount>0){
			categories_edit.delItem(categories_edit.itemCount);
		}
	}
}

function addCat(paths){
	' . $this->topFrame . '.mark();
	var path = paths.split(",");
	var found = false;
	var j = 0;
	for (var i = 0; i < path.length; i++) {
		if(path[i]!="") {
			found = false;
			for(j=0;j<categories_edit.itemCount;j++){
				if(categories_edit.form.elements[categories_edit.name+"_variant0_"+categories_edit.name+"_item"+j].value == path[i]) {
					found = true;
				}
			}
			if(!found) {
				categories_edit.addItem();
				categories_edit.setItem(0,(categories_edit.itemCount-1),path[i]);
			}
		}
	}
	categories_edit.showVariant(0);
}');
	}

	function getHTMLFieldSelector(){
		$_type = we_base_request::_(we_base_request::STRING, 'type'); // doctype || class
		$_selection = we_base_request::_(we_base_request::INT, 'selection'); // templateid or classid
		$_cmd = we_base_request::_(we_base_request::JS, 'cmd'); // js command
		$_multi = we_base_request::_(we_base_request::JS, 'multi'); // js command
		$_js = we_html_button::create_state_changer(false) . '
function setFields() {
	var list = document.we_form.fields.options;

	var fields = new Array();
	for(i=0;i<list.length;i++){
		if(list[i].selected){
			fields.push(list[i].value);
		}
	}
	opener.' . $_cmd . '(fields.join(","));
	self.close();

}

function selectItem() {
	if(document.we_form.fields.selectedIndex>-1){
		switch_button_state("save", "save_enabled", "enabled");
	}
}';

		$__fields = array();

		if($_type == we_navigation_navigation::STPYE_DOCTYPE){

			$_db = new DB_WE();
			$_fields = array();
			$_templates = f('SELECT Templates FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($_selection), '', $_db);
			$_ids = makeArrayFromCSV($_templates);

			foreach($_ids as $_templateID){
				$_template = new we_template();
				$_template->initByID($_templateID, TEMPLATES_TABLE);
				$_fields = array_merge($_fields, $_template->readAllVariantFields(true));
			}
			$__fields = array_keys($_fields);
			$__tmp = array();
			foreach($__fields as $val){
				$__tmp[$val] = $val;
			}
			$__fields = $__tmp;
		} else {
			if(defined('OBJECT_TABLE')){

				$_class = new we_object();
				$_class->initByID($_selection, OBJECT_TABLE);
				$_fields = $_class->getAllVariantFields();
				foreach($_fields as $_key => $val){
					$__fields[$_key] = substr($_key, strpos($_key, "_") + 1);
				}
			}
		}
		$_parts = array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlSelect(
					'fields', $__fields, 20, '', ($_multi ? true : false), array('style' => "width: 300px; height: 200px; margin: 5px 0px 5px 0px;", 'onclick' => "setTimeout('selectItem();',100);")),
				'space' => 0
			)
		);
		$button = we_html_button::position_yes_no_cancel(we_html_button::create_button('save', 'javascript:setFields();', true, 100, 22, '', '', true, false), null, we_html_button::create_button('close', 'javascript:self.close();'));

		we_html_button::create_button_table(array(we_html_button::create_button('save', 'javascript:setFields();', true, 100, 22, '', '', true, false), we_html_button::create_button('close', 'javascript:self.close();')));

		$_body = we_html_element::htmlBody(
				array(
				"class" => "weDialogBody", "onload" => "loaded=1;"
				), we_html_element::htmlForm(
					array(
					"name" => "we_form", "onsubmit" => "return false"
					), we_html_multiIconBox::getHTML(
						'', '100%', $_parts, 30, $button, -1, '', '', false, g_l('navigation', '[select_field_txt]'))));

		return $this->getHTMLDocument($_body, we_html_element::jsElement($_js));
	}

	function getHTMLCount(){
		return '<div style="width: ' . $this->_width_size . 'px; margin-top: 5px">' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'ShowCount', 30, $this->Model->ShowCount, '', 'onBlur="var r=parseInt(this.value);if(isNaN(r)) this.value=' . $this->Model->ShowCount . '; else{ this.value=r; ' . $this->topFrame . '.mark();}"', 'text', $this->_width_size, 0), g_l('navigation', '[show_count]')) . '</div>';
	}

	function getHTMLDirSelector(){
		$rootDirID = 0;

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['FolderID'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['FolderPath'].value");
		$wecmdenc3 = we_base_request::encCmd("opener." . $this->topFrame . ".mark();");
		$_button_doc = we_html_button::create_button('select', "javascript:we_cmd('openDirselector',document.we_form.elements['FolderID'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");
		$_countSubDirs = 1;
		if(defined('OBJECT_FILES_TABLE') && ($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS || $this->Model->SelectionType == we_navigation_navigation::STPYE_OBJLINK)){
			$_classDirID = f('SELECT ' . OBJECT_FILES_TABLE . '.ID AS classDirID FROM ' . OBJECT_TABLE . ' LEFT JOIN ' . OBJECT_FILES_TABLE . ' ON (' . OBJECT_TABLE . '.Path=' . OBJECT_FILES_TABLE . '.Path) WHERE ' . OBJECT_TABLE . '.ID=' . $this->Model->ClassID . '', 'classDirID', $this->db);
			$_countSubDirs = f('SELECT COUNT(ID) as CountSubDirs FROM ' . OBJECT_FILES_TABLE . ' WHERE ParentID=' . $_classDirID . ' AND IsFolder=1', 'CountSubDirs', $this->db);
		}
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['FolderID'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['FolderPath'].value");
		$wecmdenc3 = we_base_request::encCmd("opener." . $this->topFrame . ".mark();");

		$_button_obj = defined('OBJECT_TABLE') ? we_html_button::create_button('select', "javascript:we_cmd('openDirselector',document.we_form.elements['FolderID'].value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','',classDirs[document.we_form.elements['ClassID'].options[document.we_form.elements['ClassID'].selectedIndex].value])", true, 100, 22, "", "", ($_countSubDirs ? false : true), false, "_XFolder") : '';
		$_button_cat = we_html_button::create_button('select', "javascript:we_cmd('openCatselector',document.we_form.elements['FolderID'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'FolderID\\'].value','document.we_form.elements[\\'FolderPath\\'].value','opener." . $this->topFrame . ".mark();','','" . $rootDirID . "')");
		$_buttons = '<div id="docFolder" style="display: ' . (($this->Model->SelectionType == we_navigation_navigation::STPYE_DOCTYPE) ? 'inline' : 'none') . '">' . $_button_doc . '</div><div id="objFolder" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS ? 'inline' : 'none') . '">' . $_button_obj . '</div><div id="catFolder" style="display: ' . ($this->Model->SelectionType == we_navigation_navigation::STPYE_CATEGORY ? 'inline' : 'none') . '">' . $_button_cat . '</div>';

		$_table = $this->Model->SelectionType == we_navigation_navigation::STPYE_DOCTYPE ? FILE_TABLE :
			($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS ? OBJECT_FILES_TABLE :
				($this->Model->SelectionType == we_navigation_navigation::STPYE_CATEGORY ? CATEGORY_TABLE :
					FILE_TABLE));

		$_path = id_to_path($this->Model->FolderID, $_table);
		$_attribs = array("onchange" => $this->topFrame . ".mark();");
		if(!$_countSubDirs){
			$_attribs["disabled"] = "disabled";
		}

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("FolderPath", defined('OBJECT_FILES_TABLE') && $_table == OBJECT_FILES_TABLE ? id_to_path($this->Model->ClassID, OBJECT_FILES_TABLE) : "");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput('FolderPath', $_path, $_attribs);
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('FolderID', $this->Model->FolderID);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setLabel(g_l('navigation', '[dir]'));
		$yuiSuggest->setTable($_table);
		$yuiSuggest->setWidth($this->_width_size - 120);
		$yuiSuggest->setSelectButton($_buttons);

		$weAcSelector = $yuiSuggest->getHTML();

		return we_html_element::htmlDiv(array(
				'style' => 'margin-top:5px;'
				), $weAcSelector);
	}

	function getHTMLDynPreview(){
		$_select = new we_html_select(
			array(
			'size' => 20,
			'style' => 'width: 420px; height: 200; margin: 5px 0px 5px 0px;'
		));

		$_items = $this->Model->getDynamicEntries();

		foreach($_items as $_k => $_item){
			$_txt = id_to_path(
				$_item['id'], ($this->Model->SelectionType == we_navigation_navigation::STPYE_DOCTYPE) ? FILE_TABLE : ($this->Model->SelectionType == we_navigation_navigation::STPYE_CATEGORY ? CATEGORY_TABLE : OBJECT_FILES_TABLE));
			if(!empty($_item['field'])){
				$_opt = we_html_select::getNewOptionGroup(
						array(
							'style' => 'font-weight: bold; font-style: normal; color: darkblue;',
							'label' => $_item['field']
				));
				$_opt->addChild(we_html_select::getNewOption($_k, $_txt));
				$_select->addChild($_opt);
			} else {
				$_select->addOption($_k, $_txt);
			}
		}

		$_parts = array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlFormElementTable(
					$_select->getHtml(), g_l('navigation', ($this->Model->SelectionType == we_navigation_navigation::STPYE_CATEGORY ? '[categories]' : ($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS ? '[objects]' : '[documents]')))),
				'space' => 0
		));

		$_body = we_html_element::htmlBody(
				array(
				"class" => "weDialogBody"
				), we_html_element::htmlForm(
					array(
					'name' => 'we_form', 'onsubmit' => 'return false'
					), we_html_multiIconBox::getHTML(
						'', '100%', $_parts, 30, '<div style="float:right;">' . we_html_button::create_button(
							'close', 'javascript:self.close();') . '</div>', -1, '', '', false, g_l('navigation', '[dyn_selection]'))));

		return $this->getHTMLDocument($_body, '');
	}

	function getHTMLWorkspace($type = 'object', $defClassID = 0, $field = 'WorkspaceID'){
		$_wsid = array();

		if($type === 'class'){

			if($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS && $this->Model->ClassID){
				$_wsid = we_navigation_dynList::getWorkspacesForClass($this->Model->ClassID);
			} elseif($defClassID){
				$_wsid = we_navigation_dynList::getWorkspacesForClass($defClassID);
			}

			return '<div id="objLinkWorkspaceClass" style="display: ' . (($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC) ? 'block' : 'none') . ';margin-top: 5px;">' . we_html_tools::htmlFormElementTable(
					we_html_tools::htmlSelect(
						'WorkspaceIDClass', $_wsid, 1, $this->Model->WorkspaceID, false, array('style' => 'width: ' . $this->_width_size . 'px;', 'onchange' => $this->topFrame . '.mark();'), 'value'), g_l('navigation', '[workspace]')) . '</div>';
		} else {

			if($field === 'WorkspaceID'){
				if($this->Model->SelectionType == we_navigation_navigation::STPYE_OBJLINK && $this->Model->LinkID){
					$_wsid = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);
				}

				return '<div id="objLinkWorkspace" style="display: ' . (($this->Model->SelectionType == we_navigation_navigation::STPYE_OBJLINK && ($this->Model->WorkspaceID > -1)) ? 'block' : 'none') . ';margin-top: 5px;">' . we_html_tools::htmlFormElementTable(
						we_html_tools::htmlSelect(
							'WorkspaceID', $_wsid, 1, $this->Model->WorkspaceID, false, array('style' => 'width: ' . $this->_width_size . 'px;', 'onchange' => $this->topFrame . '.mark();'), 'value'), g_l('navigation', '[workspace]')) . '</div>';
			} else {
				if($this->Model->FolderSelection == we_navigation_navigation::STPYE_OBJLINK && $this->Model->LinkID){
					$_wsid = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);
				}

				return '<div id="objLinkFolderWorkspace" style="display: ' . (($this->Model->FolderSelection == we_navigation_navigation::STPYE_OBJLINK && ($this->Model->FolderWsID > -1)) ? 'block' : 'none') . ';margin-top: 5px;">' . we_html_tools::htmlFormElementTable(
						we_html_tools::htmlSelect(
							'FolderWsID', $_wsid, 1, $this->Model->FolderWsID, false, array('style' => 'width: ' . $this->_width_size . 'px;', 'onchange' => $this->topFrame . '.mark();'), 'value'), g_l('navigation', '[workspace]')) . '</div>';
			}
		}
	}

	function getHTMLLink($prefix = ''){
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $prefix . "UrlID'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $prefix . "UrlIDPath'].value");
		$wecmdenc3 = we_base_request::encCmd("opener." . $this->topFrame . ".mark()");
		$_cmd = "javascript:we_cmd('openDocselector',document.we_form.elements['" . $prefix . "UrlID'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','',0,'" . we_base_ContentTypes::WEDOCUMENT . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";

		$_path = id_to_path($this->Model->UrlID);

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId($prefix . "UrlIDPath");
		$yuiSuggest->setContentType(implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH, we_base_ContentTypes::QUICKTIME)));
		$yuiSuggest->setInput($prefix . 'UrlIDPath', $_path, array(
			"onchange" => $this->topFrame . ".mark();"
		));
		$yuiSuggest->setMaxResults(50);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($prefix . 'UrlID', $this->Model->UrlID);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setWidth($this->_width_size - 120);
		$yuiSuggest->setSelectButton(we_html_button::create_button('select', $_cmd));

		$weAcSelector = $yuiSuggest->getHTML();

		return we_html_element::jsElement('
function ' . $prefix . 'setLinkSelection(value){
		setVisible("' . $prefix . 'intern",(value=="' . we_navigation_navigation::LSELECTION_INTERN . '"));
		setVisible("' . $prefix . 'extern",(value!="' . we_navigation_navigation::LSELECTION_INTERN . '"));
}') . '<div id="' . $prefix . 'LinkSelectionDiv" style="display: ' . (($this->Model->SelectionType == we_navigation_navigation::STPYE_CATLINK || $this->Model->SelectionType == we_navigation_navigation::STPYE_CATEGORY) ? 'block' : 'none') . ';margin-top: 5px;">' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect(
					$prefix . 'LinkSelection', array(
					we_navigation_navigation::LSELECTION_INTERN => g_l('navigation', '[intern]'),
					we_navigation_navigation::LSELECTION_EXTERN => g_l('navigation', '[extern]')
					), 1, $this->Model->LinkSelection, false, array('style' => 'width: ' . $this->_width_size . 'px;', 'onchange' => $prefix . 'setLinkSelection(this.value);' . $this->topFrame . '.mark();'), 'value'), g_l('navigation', '[linkSelection]')) . '</div>
				<div id="' . $prefix . 'intern" style="display: ' . (($this->Model->LinkSelection === we_navigation_navigation::LSELECTION_INTERN && $this->Model->SelectionType != we_navigation_navigation::STYPE_URLLINK) ? 'block' : 'none') . ';margin-top: 5px;">
				' . $weAcSelector . '
				</div>
				<div id="' . $prefix . 'extern" style="display: ' . (($this->Model->LinkSelection === we_navigation_navigation::LSELECTION_EXTERN || $this->Model->SelectionType == we_navigation_navigation::STYPE_URLLINK) ? 'block' : 'none') . ';margin-top: 5px;">' . we_html_tools::htmlTextInput(
				$prefix . 'Url', 58, $this->Model->Url, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size) . '</div>
				<div style="margin-top: 5px;">' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					$prefix . 'CatParameter', 58, $this->Model->CatParameter, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[catParameter]')) . '</div>';
	}

	function getHTMLCharsetTable(){
		$value = ((isset($this->Model->Charset) && $this->Model->Charset) ? $this->Model->Charset : $GLOBALS['WE_BACKENDCHARSET']);

		$charsetHandler = new we_base_charsetHandler();

		$charsets = $charsetHandler->getCharsetsForTagWizzard();
		asort($charsets);
		reset($charsets);

		$table = new we_html_table(array(
			"border" => 0, "cellpadding" => 0, "cellspacing" => 0
			), 1, 3);
		$table->setCol(0, 0, null, we_html_tools::htmlTextInput("Charset", 15, $value, '', '', 'text', 120));
		$table->setCol(0, 1, null, we_html_tools::getPixel(2, 10, 0));
		$table->setCol(0, 2, null, we_html_tools::htmlSelect("CharsetSelect", $charsets, 1, $value, false, array('onblur' => 'document.forms[0].elements[\'Charset\'].value=this.options[this.selectedIndex].value;', 'onchange' => 'document.forms[0].elements[\'Charset\'].value=this.options[this.selectedIndex].value;document.we_form.submit();'), 'value', ($this->_width_size - 122), "defaultfont", false));

		return $table->getHtml();
	}

	function getLangField($name, $value, $title, $width){
		//FIXME: these values should be obtained from global settings
		$input = we_html_tools::htmlTextInput("Attributes[$name]", 15, $value, "", 'onchange=' . $this->topFrame . '.mark(); ', "text", $width - 100);
		$select = '
<select style="width:100px;" class="weSelect" name="' . $name . '_select" size="1" onchange="' . $this->topFrame . '.mark(); this.form.elements[\'Attributes[' . $name . ']\'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;">
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

	function getRevRelSelect($type, $value, $title){
		$input = we_html_tools::htmlTextInput(
				"Attributes[$type]", 15, $value, '', 'onchange=' . $this->topFrame . '.mark(); ', 'text', $this->_width_size - 100);
		$select = '<select name="' . $type . '_sel" class="weSelect" size="1" style="width:100px;" onchange="' . $this->topFrame . '.mark(); this.form.elements[\'Attributes[' . $type . ']\'].value=this.options[this.selectedIndex].text;this.selectedIndex=0;">
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

	function getHTMLAttributes(){
		$_title = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[title]', 30, $this->Model->getAttribute('title'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[title]'));

		$_anchor = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[anchor]', 30, $this->Model->getAttribute('anchor'), '', 'onchange="' . $this->topFrame . '.mark();" onblur="if(this.value&&!new RegExp(\'#?[a-z]+[a-z,0-9,_,:,.,-]*$\',\'i\').test(this.value)){alert(\'' . g_l('linklistEdit', '[anchor_invalid]') . '\');this.focus();}"', 'text', $this->_width_size) . '<br/>' .
				we_html_forms::checkboxWithHidden($this->Model->CurrentOnAnker, 'CurrentOnAnker', g_l('navigation', '[current_on_anker]'), false, "defaultfont", $this->topFrame . '.mark();"'), g_l('navigation', '[anchor]'));

		$_target = we_html_tools::htmlFormElementTable(
				we_html_tools::targetBox('Attributes[target]', 30, ($this->_width_size - 100), '', $this->Model->getAttribute('target'), '' . $this->topFrame . '.mark();', 8, 100), g_l('navigation', '[target]'));

		$_link = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[link_attribute]', 30, $this->Model->getAttribute('link_attribute'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[link_attribute]'));

		$_lang = $this->getLangField('lang', $this->Model->getAttribute('lang'), g_l('navigation', '[link_language]'), $this->_width_size);
		$_hreflang = $this->getLangField('hreflang', $this->Model->getAttribute('hreflang'), g_l('navigation', '[href_language]'), $this->_width_size);

		$_parts = array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('navigation', '[linkprops_desc]'), we_html_tools::TYPE_INFO, $this->_width_size),
				'space' => $this->_space_size,
				'noline' => 1
			),
			array(
				'headline' => g_l('navigation', '[attributes]'),
				'html' => $_title . $_anchor . $_link . $_target,
				'space' => $this->_space_size,
				'noline' => 1
			),
			array(
				'headline' => g_l('navigation', '[language]'),
				'html' => $_lang . $_hreflang,
				'space' => $this->_space_size,
				'noline' => 1
			)
		);

		$_accesskey = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[accesskey]', 30, $this->Model->getAttribute('accesskey'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[accesskey]'));

		$_tabindex = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[tabindex]', 30, $this->Model->getAttribute('tabindex'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[tabindex]'));

		$_parts[] = array(
			'headline' => g_l('navigation', '[keyboard]'),
			'html' => $_accesskey . $_tabindex,
			'space' => $this->_space_size,
			'noline' => 1
		);

		$_relfield = $this->getRevRelSelect('rel', $this->Model->getAttribute('rel'), 'rel');
		$_revfield = $this->getRevRelSelect('rev', $this->Model->getAttribute('rev'), 'rev');

		$_parts[] = array(
			'headline' => g_l('navigation', '[relation]'),
			'html' => $_relfield . $_revfield,
			'space' => $this->_space_size,
			'noline' => 1
		);

		$_input_width = 70;

		$_popup = new we_html_table(array('cellpadding' => 5, 'cellspacing' => 0), 4, 4);

		$_popup->setCol(0, 0, array('colspan' => 2), we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_open'), 'Attributes[popup_open]', g_l('navigation', '[popup_open]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(0, 2, array('colspan' => 2), we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_center'), 'Attributes[popup_center]', g_l('navigation', '[popup_center]'), false, "defaultfont", $this->topFrame . '.mark();"'));

		$_popup->setCol(1, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[popup_xposition]', 5, $this->Model->getAttribute('popup_xposition'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[popup_x]')));
		$_popup->setCol(1, 1, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[popup_yposition]', 5, $this->Model->getAttribute('popup_yposition'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[popup_y]')));
		$_popup->setCol(1, 2, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[popup_width]', 5, $this->Model->getAttribute('popup_width'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[popup_width]')));

		$_popup->setCol(1, 3, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Attributes[popup_height]', 5, $this->Model->getAttribute('popup_height'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[popup_height]')));

		$_popup->setCol(2, 0, array(), we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_status'), 'Attributes[popup_status]', g_l('navigation', '[popup_status]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(2, 1, array(), we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_scrollbars'), 'Attributes[popup_scrollbars]', g_l('navigation', '[popup_scrollbars]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(2, 2, array(), we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_menubar'), 'Attributes[popup_menubar]', g_l('navigation', '[popup_menubar]'), false, "defaultfont", $this->topFrame . '.mark();"'));

		$_popup->setCol(3, 0, array(), we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_resizable'), 'Attributes[popup_resizable]', g_l('navigation', '[popup_resizable]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(3, 1, array(), we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_location'), 'Attributes[popup_location]', g_l('navigation', '[popup_location]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(3, 2, array(), we_html_forms::checkboxWithHidden($this->Model->getAttribute('popup_toolbar'), 'Attributes[popup_toolbar]', g_l('navigation', '[popup_toolbar]'), false, "defaultfont", $this->topFrame . '.mark();"'));

		$_parts[] = array(
			'headline' => g_l('navigation', '[popup]'),
			'html' => $_popup->getHTML(),
			'space' => $this->_space_size,
			'noline' => 1
		);

		$wepos = weGetCookieVariable("but_weNaviAttrib");
		return we_html_multiIconBox::getHTML('weNaviAttrib', '100%', $_parts, 30, '', 0, g_l('navigation', '[more_attributes]'), g_l('navigation', '[less_attributes]'), ($wepos === 'down'));
	}

	function getHTMLImageAttributes(){
		$_input_width = 70;
		$_img_props = new we_html_table(array('cellpadding' => 5, 'cellspacing' => 0, 'border' => 0), 4, 5);

		$_img_props->setCol(0, 0, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[icon_width]', 5, $this->Model->getAttribute('icon_width'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_width]')));
		$_img_props->setCol(0, 1, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[icon_height]', 5, $this->Model->getAttribute('icon_height'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_height]')));
		$_img_props->setCol(0, 2, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[icon_border]', 5, $this->Model->getAttribute('icon_border'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_border]')));
		$_img_props->setCol(0, 3, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[icon_hspace]', 5, $this->Model->getAttribute('icon_hspace'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_hspace]')));
		$_img_props->setCol(0, 4, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('Attributes[icon_vspace]', 5, $this->Model->getAttribute('icon_vspace'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_vspace]')));
		$_img_props->setCol(1, 0, array('colspan' => 5), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect(
					'Attributes[icon_align]', array(
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
					), 1, $this->Model->getAttribute('icon_align'), false, array('style' => 'width: ' . ($this->_width_size - 50) . 'px;')), g_l('navigation', '[icon_align]')));
		$_img_props->setCol(2, 0, array(
			'colspan' => 5
			), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[icon_alt]', 5, $this->Model->getAttribute('icon_alt'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', ($this->_width_size - 50)), g_l('navigation', '[icon_alt]')));
		$_img_props->setCol(3, 0, array(
			'colspan' => 5
			), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[icon_title]', 5, $this->Model->getAttribute('icon_title'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', ($this->_width_size - 50)), g_l('navigation', '[icon_title]')));

		return $_img_props->getHTML();
	}

	function getHTMLTab3(){
		$_space_size = 50;

		$_filter = new we_navigation_customerFilter();
		$_filter->initByNavModel($this->Model);

		$_view = new we_navigation_customerFilterView($_filter, $this->topFrame . '.mark()', $this->_width_size);
		return array(
			array(
				'headline' => '',
				'html' => $_view->getFilterHTML($this->Model->IsFolder == 0 && $this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC),
				'space' => $_space_size,
				'noline' => 1
		));
	}

	protected function getHTMLEditorFooter(){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array(
						"bgcolor" => "#F0EFF0"
						), ""));
		}

		$table2 = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "style" => 'width:400px;margin-top:10px;'), 1, 2);
		$table2->setColContent(0, 0, we_html_button::create_button_table(
				array(
				we_html_button::create_button("save", "javascript:we_save();", true, 100, 22, '', '', (!permissionhandler::hasPerm('EDIT_NAVIGATION')))
				), 10, array(
				'style' => 'margin-left: 15px'
		)));

		$table2->setColContent(0, 1, we_html_forms::checkbox("makeNewDoc", false, "makeNewDoc", g_l('global', ($this->View->Model->IsFolder ? '[we_new_folder_after_save]' : '[we_new_entry_after_save]')), false, "defaultfont", ""));

		return $this->getHTMLDocument(
				we_html_element::jsScript(JS_DIR . "attachKeyListener.js") .
				we_html_element::jsElement('
					function we_save() {
						' . $this->topFrame . '.makeNewDoc = document.we_form.makeNewDoc.checked;
						' . $this->topFrame . '.we_cmd("module_' . $this->toolName . '_save");
					}
					') . we_html_element::htmlBody(
					array(
					"bgcolor" => "white",
					"background" => IMAGE_DIR . "edit/editfooterback.gif",
					"marginwidth" => 0,
					"marginheight" => 0,
					"leftmargin" => 0,
					"topmargin" => 0,
					"onload" => "document.we_form.makeNewDoc.checked=" . $this->topFrame . ".makeNewDoc;"
					), we_html_element::htmlForm(array(), $table2->getHtml())));
	}

	//TODO: function comes from weToolFrames: do we need it in navigation?
	function getPercent($total, $value, $precision = 0){
		$result = ($total ? round(($value * 100) / $total, $precision) : 0);
		return we_util_Strings::formatNumber($result, strtolower($GLOBALS['WE_LANGUAGE']), 2);
	}

	//TODO: probably not used
	/*
	  function formFileChooser($width = '', $IDName = 'ParentID', $IDValue = '/', $cmd = '', $filter = ''){
	  $wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
	  $button = we_button::create_button('select', "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value);");

	  return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, '', 'readonly', 'text', ($this->_width_size - 120), 0), "", "left", "defaultfont", "", we_html_tools::getPixel(20, 4), permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	  }
	 *
	 */
}
