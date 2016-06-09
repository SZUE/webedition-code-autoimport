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
class we_search_frames extends we_tool_frames{

	function __construct(){
		$this->toolName = 'weSearch';
		$this->module = 'weSearch';
		$this->toolUrl = WE_INCLUDES_DIR . 'we_tools/' . $this->module . '/';
		$this->toolDir = $_SERVER['DOCUMENT_ROOT'] . $this->toolUrl;

		$_frameset = $this->toolUrl . 'edit_' . $this->module . '_frameset.php?mod=' . $this->module;
		parent::__construct($_frameset);
		$this->Table = SUCHE_TABLE;
		$this->TreeSource = 'table:' . $this->Table;
		$this->Tree = new we_search_tree($this->frameset, 'top.content', 'top.content', 'top.content.cmd');

		$this->View = new we_search_view($_frameset, 'top.content');
		//$this->Model = &$this->View->Model;
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::INT, 'pid')) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		$offset = we_base_request::_(we_base_request::INT, 'offset', 0);

		$rootjs = (!$pid ?
				'top.content.treeData.clear();
top.content.treeData.add(top.content.node.prototype.rootEntry(\'' . $pid . '\',\'root\',\'root\'));' :
				'');


		$hiddens = we_html_element::htmlHiddens(array(
				'pnt' => 'cmd',
				'cmd' => 'no_cmd'));

		$out = we_html_element::htmlBody(array(), we_html_element::htmlForm(array(
					'name' => 'we_form'
					), $hiddens .
					we_html_element::jsElement($rootjs .
						$this->Tree->getJSLoadTree(!$pid, we_search_tree::getItemsFromDB($pid, $offset, $this->Tree->default_segment)))));

		if(isset($_SESSION['weS']['weSearch']['modelidForTree'])){
			$out .= we_html_element::jsElement('top.content.treeData.selectNode("' . ($_SESSION['weS']['weSearch']["modelidForTree"]) . '");');
			unset($_SESSION['weS']['weSearch']['modelidForTree']);
		}

		return $this->getHTMLDocument($out);
	}

	function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		return parent::getHTMLFrameset(($tab = we_base_request::_(we_base_request::INT, 'tab')) ? '&tab=' . $tab : '');
	}

	protected function getHTMLEditor($extraUrlParams = '', $extraHead = ''){
		return parent::getHTMLEditor(($tab = we_base_request::_(we_base_request::INT, 'tab')) ? '&tab=' . $tab : '');
	}

	protected function getHTMLEditorHeader(){
		$we_tabs = new we_tabs();

		//folders and entries have different tabs to display
		$displayEntry = 'none';
		$displayFolder = 'inline';

		if($this->View->Model->IsFolder == 0){
			$displayEntry = 'inline';
			$displayFolder = 'none';
		}

		//tabs for entries
		if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
			$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-file-o"></i> ' . g_l('searchtool', '[documents]'), '((top.content.activ_tab==1) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('1');", array(
				'id' => 'tab_1', 'style' => "display:$displayEntry"
			)));
		}
		if($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE && permissionhandler::hasPerm('CAN_SEE_TEMPLATES')){
			$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-file-code-o"></i> ' . g_l('searchtool', '[templates]'), '((top.content.activ_tab==2) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('2');", array(
				'id' => 'tab_2', 'style' => "display:$displayEntry"
			)));
		}
		if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){// FIXME: add some media related perm
			$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-image"></i> ' . g_l('searchtool', '[media]'), '((top.content.activ_tab==5) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('5');", array(
				'id' => 'tab_5', 'style' => "display:$displayEntry"
			)));
		}
		$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-search-plus"></i> ' . g_l('searchtool', '[advSearch]'), '((top.content.activ_tab==3) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('3');", array(
			'id' => 'tab_3', 'style' => "display:$displayEntry"
		)));

		//tabs for folders
		$we_tabs->addTab(new we_tab(g_l('searchtool', '[properties]'), '((top.content.activ_tab==4) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('4');", array(
			'id' => 'tab_4', 'style' => "display:$displayFolder"
		)));

		$tabNr = $this->getTab();
		$activeTabJS = 'top.content.activ_tab = ' . $tabNr . ';';
		$tabsHead = we_tabs::getHeader($activeTabJS . '
function setTab(tab) {
	switch (tab) {
		// Add new tab handlers here
		default: // just toggle content to show
			parent.edbody.document.we_form.pnt.value = "edbody";
			parent.edbody.document.we_form.tabnr.value = tab;
			parent.edbody.submitForm();
		break;
	}
	self.focus();
	top.content.activ_tab=tab;
}
		');


		$setActiveTabJS = 'document.getElementById("tab_"+top.content.activ_tab).className="tabActive";';
		$Text = we_search_model::getLangText($this->View->Model->Path, $this->View->Model->Text);
		$body = we_html_element::htmlBody(
				array(
				'id' => 'eHeaderBody',
				'onload' => 'weTabs.setFrameSize()',
				'onresize' => 'weTabs.setFrameSize()'
				), '<div id="main"><div id="headrow">&nbsp;' . we_html_element::htmlB(g_l('searchtool', ($this->View->Model->IsFolder ? '[topDir]' : '[topSuche]')) . ':&nbsp;' .
					$Text . '<div id="mark" style="display: none;">*</div>') . '</div>' .
				$we_tabs->getHTML() .
				'</div>' .
				we_html_element::jsElement($setActiveTabJS));

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){
		$body = we_html_element::htmlBody(
				array(
				'class' => 'weEditorBody',
				'onkeypress' => 'javascript:if(event.keyCode==13 || event.keyCode==3){weSearch.search(true);}',
				'onload' => 'loaded=1;setTimeout(weSearch.init,200);',
				'onresize' => 'weSearch.sizeScrollContent();'
				), we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js') .
				we_html_element::htmlForm(array(
					'name' => 'we_form', 'onsubmit' => 'return false'
					), $this->getHTMLProperties() . we_html_element::htmlHiddens(array(
						'predefined' => $this->View->Model->predefined,
						'savedSearchName' => $this->View->Model->Text
				)))
		);

		$whichSearch = we_search_view::SEARCH_DOCS;
		$tabNr = $this->getTab();

		switch($tabNr){
			case 1 :
				$whichSearch = we_search_view::SEARCH_DOCS;
				break;
			case 2 :
				$whichSearch = we_search_view::SEARCH_TMPL;
				break;
			case 3 :
				$whichSearch = we_search_view::SEARCH_ADV;
				break;
			case 5 :
				$whichSearch = we_search_view::SEARCH_MEDIA;
				break;
		}

		return $this->getHTMLDocument($body, we_html_tools::getCalendarFiles() . $this->View->getJSProperty() . $this->View->getSearchJS($whichSearch));
	}

	function getTab(){
		$cmdid = we_base_request::_(we_base_request::INT, 'cmdid', '');
		if($cmdid != ''){
			$_REQUEST['searchstartAdvSearch'] = 0;
		}
		if(($tab = we_base_request::_(we_base_request::INT, 'tab'))){
			return $tab;
		}
		if($cmdid != ''){
			return $this->View->Model->activTab;
		}
		return we_base_request::_(we_base_request::INT, 'tabnr', 1);
	}

	protected function getHTMLEditorFooter($btn_cmd = '', $extraHead = ''){
		$_but_table = we_html_button::create_button('save', 'javascript:we_save();', true, 100, 22, '', '', (!permissionhandler::hasPerm('EDIT_NAVIGATION')));

		return $this->getHTMLDocument(we_html_element::jsElement('
function we_save() {
	top.content.we_cmd("tool_' . $this->module . '_save");
}') . we_html_element::htmlBody(
					array('id' => 'footerBody'), we_html_element::htmlForm(array(), $_but_table)));
	}

	function getHTMLProperties($preselect = ''){
		$tabNr = $this->getTab();

		$hiddens = array(
			'cmd' => '',
			'pnt' => 'edbody',
			'tabnr' => $tabNr,
			'vernr' => we_base_request::_(we_base_request::INT, 'vernr', 0),
			'delayParam' => we_base_request::_(we_base_request::INT, 'delayParam', '')
		);

		return $this->View->getCommonHiddens($hiddens) .
			we_html_element::htmlHidden('newone', ($this->View->Model->ID == 0 ? 1 : 0)) .
			we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ($tabNr == 1 ? 'display: block;' : 'display: none')), $tabNr == 1 ? $this->getHTMLSearchtool($this->getHTMLTabDocuments()) : '') .
			we_html_element::htmlDiv(array('id' => 'tab2', 'style' => ($tabNr == 2 ? 'display: block;' : 'display: none')), $tabNr == 2 ? $this->getHTMLSearchtool($this->getHTMLTabTemplates()) : '') .
			we_html_element::htmlDiv(array('id' => 'tab5', 'style' => ($tabNr == 5 ? 'display: block;' : 'display: none')), $tabNr == 5 ? $this->getHTMLSearchtool($this->getHTMLTabMedia()) : '') .
			we_html_element::htmlDiv(array('id' => 'tab3', 'style' => ($tabNr == 3 ? 'display: block;' : 'display: none')), $tabNr == 3 ? $this->getHTMLSearchtool($this->getHTMLTabAdvanced()) : '') .
			we_html_element::htmlDiv(array('id' => 'tab4', 'style' => ($tabNr == 4 ? 'display: block;' : 'display: none')), $this->getHTMLSearchtool($this->getHTMLGeneral()));
	}

	function getHTMLGeneral(){
		$disabled = true;
		$this->View->Model->Text = we_search_model::getLangText($this->View->Model->Path, $this->View->Model->Text);

		return array(
			array(
				'headline' => g_l('searchtool', '[general]'),
				'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Text', '', $this->View->Model->Text, '', 'style="width: 520px" );"', '', '', '', '', $disabled), g_l('searchtool', '[dir]')),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
		));
	}

	function getHTMLTabDocuments(){
		//parameter: search of the tab (load only search dependent model data in the view)
		$innerSearch = we_search_view::SEARCH_DOCS;

		$_searchDirChooser_block = '<div>' . $this->View->getDirSelector($innerSearch) . '</div>';
		$_searchField_block = '<div>' . $this->View->getSearchDialog($innerSearch) . '</div>';
		$_searchCheckboxes_block = '<div>' . $this->View->getSearchDialogOptions($innerSearch) . '</div>';

		//$this->View->searchProperties($innerSearch);
		$content = $this->View->searchclass->searchProperties($innerSearch, $this->View->Model);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $innerSearch];

		$_searchResult_block = '<div>
		<div id="parametersTop_' . $innerSearch . '">' . $this->View->getSearchParameterTop($foundItems, $innerSearch) . '</div>' . $this->View->tblList($content, $headline, $innerSearch) . '<div id="parametersBottom_' . $innerSearch . '">' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
		</div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $_searchField_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[suchenIn]'),
				'html' => $_searchDirChooser_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[optionen]'),
				'html' => $_searchCheckboxes_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => '', 'html' => $_searchResult_block, 'space' => we_html_multiIconBox::SPACE_MED
		));
	}

	function getHTMLTabTemplates(){
		$innerSearch = we_search_view::SEARCH_TMPL;

		$_searchDirChooser_block = '<div>' . $this->View->getDirSelector($innerSearch) . '</div>';
		$_searchField_block = '<div>' . $this->View->getSearchDialog($innerSearch) . '</div>';
		$_searchCheckboxes_block = '<div>' . $this->View->getSearchDialogOptions($innerSearch) . '</div>';
		$content = $this->View->searchclass->searchProperties($innerSearch, $this->View->Model);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItemsTmplSearch'];

		$_searchResult_block = '<div>
		<div id="parametersTop_' . $innerSearch . '">' . $this->View->getSearchParameterTop($foundItems, $innerSearch) . '</div>' .
			$this->View->tblList($content, $headline, $innerSearch) . '<div id="parametersBottom_TmplSearch">' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
		</div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $_searchField_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[suchenIn]'),
				'html' => $_searchDirChooser_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[optionen]'),
				'html' => $_searchCheckboxes_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => '', 'html' => $_searchResult_block, 'space' => we_html_multiIconBox::SPACE_MED
		));
	}

	function getHTMLTabMedia(){
		$innerSearch = we_search_view::SEARCH_MEDIA;

		$_searchDirChooser_block = '<div>' . $this->View->getDirSelector($innerSearch) . '</div>';
		$_searchField_block = '<div>' . $this->View->getSearchDialog($innerSearch) . '</div>';
		$_searchCheckboxes_block = '<div>' . $this->View->getSearchDialogOptions($innerSearch) . '</div>';
		$_searchCheckboxMediaTyp_block = '<div>' . $this->View->getSearchDialogMediaType($innerSearch) . '</div>';
		$_searchFilter_block = '<div>' . $this->View->getSearchDialogFilter($innerSearch) . '</div>';

		$content = $this->View->searchclass->searchProperties($innerSearch, $this->View->Model);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $innerSearch];

		$_searchResult_block = '<div>
		<div id=\'parametersTop_' . $innerSearch . '\'>' . $this->View->getSearchParameterTop($foundItems, $innerSearch) . '</div>' . $this->View->tblList($content, $headline, $innerSearch) . '<div id=\'parametersBottom_' . $innerSearch . '\'>' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
		</div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $_searchField_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[suchenIn]'),
				'html' => $_searchDirChooser_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[optionen]'),
				'html' => $_searchCheckboxes_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[anzeigen]'),
				'html' => $_searchCheckboxMediaTyp_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => 'Filter', //g_l('searchtool', '[optionen]'),
				'html' => $_searchFilter_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => '', 'html' => $_searchResult_block, 'space' => we_html_multiIconBox::SPACE_MED
		));
	}

	function getHTMLTabAdvanced(){
		$innerSearch = 'AdvSearch';
		$_searchFields_block = '<div>' . $this->View->getSearchDialogOptionalFields($innerSearch) . '</div>';
		$_searchCheckboxes_block = '<div>' . $this->View->getSearchDialogCheckboxesAdvSearch() . '</div>';
		$content = $this->View->searchclass->searchProperties($innerSearch, $this->View->Model);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $innerSearch];

		$_searchResult_block = '<div>
      <div id=\'parametersTop_' . $innerSearch . '\'>' . $this->View->getSearchParameterTop($foundItems, $innerSearch) . '</div>' .
			$this->View->tblList($content, $headline, $innerSearch) . '<div id=\'parametersBottom_' . $innerSearch . '\'>' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
      </div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $_searchFields_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[anzeigen]'),
				'html' => $_searchCheckboxes_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => '', 'html' => $_searchResult_block, 'space' => we_html_multiIconBox::SPACE_MED
		));
	}

	function getHTMLSearchtool($content){
		//FIXME: why is this different to we_html_multiIconBox.class.php
		$out = '';

		foreach($content as $i => $c){
			$_forceRightHeadline = (!empty($c['forceRightHeadline']));
			$headline = (!empty($c['headline'])) ? ('<div  class="weMultiIconBoxHeadline" style="margin-bottom:10px;margin-left:30px;">' . $c["headline"] . '</div>') : "";
			$mainContent = (!empty($c['html'])) ? $c['html'] : '';
			$leftWidth = (empty($c['space'])) ? '' : $c['space'];
			$leftContent = (($leftWidth && (!$_forceRightHeadline)) ? $headline : '');
			$rightContent = '<div class="defaultfont">' . ((($leftContent === '') || $_forceRightHeadline) ? ($headline . '<div>' . $mainContent . '</div>') : '<div>' . $mainContent . '</div>') . '</div>';

			if($leftContent || $leftWidth && $leftContent != ''){
				if((!$leftContent) && $leftWidth){
					$leftContent = '&nbsp;';
				}
				$out .= '<div style="float:left" class="multiiconleft largeicons leftSpace-' . $leftWidth . '">' . $leftContent . '</div>';
			}

			$out .= $rightContent .
				'<div style="clear:both;' . ($i < (count($content) - 1) && (!isset($c['noline'])) ?
					'border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;' :
					'margin:10px 0;'
				) . '"></div>';
		}

		return '<div class="multiIcon">' . $out . '</div>';
	}

}
