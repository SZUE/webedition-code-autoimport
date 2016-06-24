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
class we_search_frames extends we_modules_frame{
	const TAB_DOCUMENTS = 1;
	const TAB_TEMPLATES = 2;
	const TAB_MEDIA = 5;
	const TAB_ADVANCED = 3;
	const TAB_PROPERTIES = 4;

	var $Model;
	
	public function __construct(){
		$this->module = 'weSearch';
		parent::__construct(WE_INCLUDES_DIR . 'we_tools/' . $this->module . '/edit_' . $this->module . '_frameset.php?mod=' . $this->module);
		$this->Tree = new we_search_tree($this->frameset, 'top.content', 'top.content', 'top.content.cmd');
		$this->View = new we_search_view($this->frameset, 'top.content');
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::INT, 'pid')) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		$offset = we_base_request::_(we_base_request::INT, 'offset', 0);
		$attr = [];

		if(!empty($_SESSION['weS']['weSearch']['modelidForTree'])){
			$attr['onload'] = "top.content.treeData.selectNode('" . ($_SESSION['weS']['weSearch']['modelidForTree']) . "');";
			unset($_SESSION['weS']['weSearch']['modelidForTree']);
		}

		return $this->getHTMLDocument(
				we_html_element::htmlBody($attr, we_html_element::htmlForm(array(
						'name' => 'we_form'
						), we_html_element::htmlHiddens(array(
							'pnt' => 'cmd',
							'cmd' => 'no_cmd')) .
						we_html_element::jsElement(
							($pid ?
								'' :
								'top.content.treeData.clear();
top.content.treeData.add(top.content.node.prototype.rootEntry(\'' . $pid . '\',\'root\',\'root\'));'
							) . $this->Tree->getJSLoadTree(!$pid, we_search_tree::getItemsFromDB($pid, $offset, $this->Tree->default_segment)))))
		);
	}

	protected function getHTMLEditorHeader($mode = 0){
		$we_tabs = new we_tabs();

		if($this->View->Model->IsFolder == 0){
			$displayEntry = 'inline';
			$displayFolder = 'none';
		} else {
			//folders and entries have different tabs to display
			$displayEntry = 'none';
			$displayFolder = 'inline';
		}

		//tabs for entries
		if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
			$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-file-o"></i> ' . g_l('searchtool', '[documents]'), false, "setTab(" . self::TAB_DOCUMENTS . ");", array('id' => 'tab_1', 'style' => 'display:' . $displayEntry)));
		}
		if($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE && permissionhandler::hasPerm('CAN_SEE_TEMPLATES')){
			$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-file-code-o"></i> ' . g_l('searchtool', '[templates]'), false, "setTab(" . self::TAB_TEMPLATES . ");", array('id' => 'tab_2', 'style' => 'display:' . $displayEntry)));
		}
		if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){// FIXME: add some media related perm
			$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-image"></i> ' . g_l('searchtool', '[media]'), false, "setTab(" . self::TAB_MEDIA . ");", array('id' => 'tab_5', 'style' => 'display:' . $displayEntry)));
		}
		$we_tabs->addTab(new we_tab('<i class="fa fa-lg fa-search-plus"></i> ' . g_l('searchtool', '[advSearch]'), false, "setTab(" . self::TAB_ADVANCED . ");", array('id' => 'tab_3', 'style' => 'display:' . $displayEntry)));

		//tabs for folders
		$we_tabs->addTab(new we_tab(g_l('searchtool', '[properties]'), false, "setTab(" . self::TAB_PROPERTIES . ");", array('id' => 'tab_4', 'style' => 'display:' . $displayFolder)));

		$tabNr = $this->getTab();
		$tabsHead = we_tabs::getHeader('top.content.activ_tab = ' . $tabNr . ';
function setTab(tab) {
	switch (tab) {
		default: // just toggle content to show
			parent.edbody.document.we_form.pnt.value = "edbody";
			parent.edbody.document.we_form.tabnr.value = tab;
			parent.edbody.submitForm();
		break;
	}
	self.focus();
	top.content.activ_tab=tab;
}');

		$Text = we_search_model::getLangText($this->View->Model->Path, $this->View->Model->Text);
		$body = we_html_element::htmlBody(
				array(
				'id' => 'eHeaderBody',
				'onload' => "weTabs.setFrameSize();document.getElementById('tab_'+top.content.activ_tab).className='tabActive';",
				'onresize' => 'weTabs.setFrameSize()'
				), '<div id="main"><div id="headrow">&nbsp;' . we_html_element::htmlB(g_l('searchtool', ($this->View->Model->IsFolder ? '[topDir]' : '[topSuche]')) . ':&nbsp;' .
					$Text . '<div id="mark" style="display: none;">*</div>') . '</div>' .
				$we_tabs->getHTML() .
				'</div>');

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){
		$body = we_html_element::htmlBody(
				array(
				'class' => 'weEditorBody',
				'onkeypress' => 'if(event.keyCode==13 || event.keyCode==3){weSearch.search(true);}',
				'onload' => 'loaded=1;setTimeout(weSearch.init,200);',
				'onresize' => 'weSearch.sizeScrollContent();'
				), we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js') .
				we_html_element::htmlForm(array(
					'name' => 'we_form',
					'onsubmit' => 'return false'
					), $this->getHTMLProperties() .
					we_html_element::htmlHiddens(array(
						'predefined' => $this->View->Model->predefined,
						'savedSearchName' => $this->View->Model->Text
				)))
		);

		$whichSearch = we_search_view::SEARCH_DOCS;
		$tabNr = $this->getTab();

		switch($tabNr){
			case self::TAB_DOCUMENTS:
				$whichSearch = we_search_view::SEARCH_DOCS;
				break;
			case self::TAB_TEMPLATES:
				$whichSearch = we_search_view::SEARCH_TMPL;
				break;
			case self::TAB_ADVANCED:
				$whichSearch = we_search_view::SEARCH_ADV;
				break;
			case self::TAB_MEDIA:
				$whichSearch = we_search_view::SEARCH_MEDIA;
				break;
		}

		return $this->getHTMLDocument($body, we_html_tools::getCalendarFiles() . $this->View->getJSProperty() . $this->View->getSearchJS($whichSearch));
	}

	private function getTab(){
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
		return we_base_request::_(we_base_request::INT, 'tabnr', self::TAB_DOCUMENTS);
	}

	protected function getHTMLEditorFooter($btn_cmd = '', $extraHead = ''){
		$but_table = we_html_button::create_button('save', 'javascript:we_save();', true, 100, 22, '', '', (!permissionhandler::hasPerm('EDIT_NAVIGATION')));

		return $this->getHTMLDocument(we_html_element::jsElement('
function we_save() {
	top.content.we_cmd("tool_' . $this->module . '_save");
}') .
				we_html_element::htmlBody(array('id' => 'footerBody'), we_html_element::htmlForm([], $but_table)));
	}

	private function getHTMLProperties($preselect = ''){
		$tabNr = $this->getTab();

		return $this->View->getCommonHiddens(array(
				'cmd' => '',
				'pnt' => 'edbody',
				'tabnr' => $tabNr,
				'vernr' => we_base_request::_(we_base_request::INT, 'vernr', 0),
				'delayParam' => we_base_request::_(we_base_request::INT, 'delayParam', '')
			)) .
			we_html_element::htmlHidden('newone', ($this->View->Model->ID == 0 ? 1 : 0)) .
			we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ($tabNr == self::TAB_DOCUMENTS ? 'display: block;' : 'display: none')), $tabNr == self::TAB_DOCUMENTS ? $this->getHTMLSearchtool($this->getHTMLTabDocuments()) : '') .
			we_html_element::htmlDiv(array('id' => 'tab2', 'style' => ($tabNr == self::TAB_TEMPLATES ? 'display: block;' : 'display: none')), $tabNr == self::TAB_TEMPLATES ? $this->getHTMLSearchtool($this->getHTMLTabTemplates()) : '') .
			we_html_element::htmlDiv(array('id' => 'tab5', 'style' => ($tabNr == self::TAB_MEDIA ? 'display: block;' : 'display: none')), $tabNr == self::TAB_MEDIA ? $this->getHTMLSearchtool($this->getHTMLTabMedia()) : '') .
			we_html_element::htmlDiv(array('id' => 'tab3', 'style' => ($tabNr == self::TAB_ADVANCED ? 'display: block;' : 'display: none')), $tabNr == self::TAB_ADVANCED ? $this->getHTMLSearchtool($this->getHTMLTabAdvanced()) : '') .
			we_html_element::htmlDiv(array('id' => 'tab4', 'style' => ($tabNr == self::TAB_PROPERTIES ? 'display: block;' : 'display: none')), $this->getHTMLSearchtool($this->getHTMLGeneral()));
	}

	private function getHTMLGeneral(){
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

	private function getHTMLTabDocuments(){
		//parameter: search of the tab (load only search dependent model data in the view)
		$innerSearch = we_search_view::SEARCH_DOCS;

		$searchDirChooser_block = '<div>' . $this->View->getDirSelector($innerSearch) . '</div>';
		$searchField_block = '<div>' . $this->View->getSearchDialog($innerSearch) . '</div>';
		$searchCheckboxes_block = '<div>' . $this->View->getSearchDialogOptions($innerSearch) . '</div>';

		//$this->View->searchProperties($innerSearch);
		$content = $this->View->searchclass->searchProperties($innerSearch, $this->View->Model);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $innerSearch];

		$searchResult_block = '<div>
		<div id="parametersTop_' . $innerSearch . '">' . $this->View->getSearchParameterTop($foundItems, $innerSearch) . '</div>' . $this->View->tblList($content, $headline, $innerSearch) . '<div id="parametersBottom_' . $innerSearch . '">' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
		</div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $searchField_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[suchenIn]'),
				'html' => $searchDirChooser_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[optionen]'),
				'html' => $searchCheckboxes_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => '', 'html' => $searchResult_block, 'space' => we_html_multiIconBox::SPACE_MED
		));
	}

	private function getHTMLTabTemplates(){
		$innerSearch = we_search_view::SEARCH_TMPL;

		$searchDirChooser_block = '<div>' . $this->View->getDirSelector($innerSearch) . '</div>';
		$searchField_block = '<div>' . $this->View->getSearchDialog($innerSearch) . '</div>';
		$searchCheckboxes_block = '<div>' . $this->View->getSearchDialogOptions($innerSearch) . '</div>';
		$content = $this->View->searchclass->searchProperties($innerSearch, $this->View->Model);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItemsTmplSearch'];

		$searchResult_block = '<div>
		<div id="parametersTop_' . $innerSearch . '">' . $this->View->getSearchParameterTop($foundItems, $innerSearch) . '</div>' .
			$this->View->tblList($content, $headline, $innerSearch) . '<div id="parametersBottom_TmplSearch">' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
		</div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $searchField_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[suchenIn]'),
				'html' => $searchDirChooser_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[optionen]'),
				'html' => $searchCheckboxes_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => '', 'html' => $searchResult_block, 'space' => we_html_multiIconBox::SPACE_MED
		));
	}

	private function getHTMLTabMedia(){
		$innerSearch = we_search_view::SEARCH_MEDIA;

		$searchDirChooser_block = '<div>' . $this->View->getDirSelector($innerSearch) . '</div>';
		$searchField_block = '<div>' . $this->View->getSearchDialog($innerSearch) . '</div>';
		$searchCheckboxes_block = '<div>' . $this->View->getSearchDialogOptions($innerSearch) . '</div>';
		$searchCheckboxMediaTyp_block = '<div>' . $this->View->getSearchDialogMediaType($innerSearch) . '</div>';
		$searchFilter_block = '<div>' . $this->View->getSearchDialogFilter($innerSearch) . '</div>';

		$content = $this->View->searchclass->searchProperties($innerSearch, $this->View->Model);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $innerSearch];

		$searchResult_block = '<div>
		<div id=\'parametersTop_' . $innerSearch . '\'>' . $this->View->getSearchParameterTop($foundItems, $innerSearch) . '</div>' . $this->View->tblList($content, $headline, $innerSearch) . '<div id=\'parametersBottom_' . $innerSearch . '\'>' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
		</div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $searchField_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[suchenIn]'),
				'html' => $searchDirChooser_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[optionen]'),
				'html' => $searchCheckboxes_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[anzeigen]'),
				'html' => $searchCheckboxMediaTyp_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => 'Filter', //g_l('searchtool', '[optionen]'),
				'html' => $searchFilter_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => '', 'html' => $searchResult_block, 'space' => we_html_multiIconBox::SPACE_MED
		));
	}

	private function getHTMLTabAdvanced(){
		$innerSearch = 'AdvSearch';
		$searchFields_block = '<div>' . $this->View->getSearchDialogOptionalFields($innerSearch) . '</div>';
		$searchCheckboxes_block = '<div>' . $this->View->getSearchDialogCheckboxesAdvSearch() . '</div>';
		$content = $this->View->searchclass->searchProperties($innerSearch, $this->View->Model);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $innerSearch];

		$searchResult_block = '<div>
      <div id=\'parametersTop_' . $innerSearch . '\'>' . $this->View->getSearchParameterTop($foundItems, $innerSearch) . '</div>' .
			$this->View->tblList($content, $headline, $innerSearch) . '<div id=\'parametersBottom_' . $innerSearch . '\'>' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
      </div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $searchFields_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => g_l('searchtool', '[anzeigen]'),
				'html' => $searchCheckboxes_block,
				'space' => we_html_multiIconBox::SPACE_MED
			),
			array(
				'headline' => '', 'html' => $searchResult_block, 'space' => we_html_multiIconBox::SPACE_MED
		));
	}

	private function getHTMLSearchtool($content){
		//FIXME: why is this different to we_html_multiIconBox.class.php
		$out = '';

		foreach($content as $i => $c){
			$forceRightHeadline = (!empty($c['forceRightHeadline']));
			$headline = (!empty($c['headline'])) ? ('<div  class="weMultiIconBoxHeadline" style="margin-bottom:10px;margin-left:30px;">' . $c["headline"] . '</div>') : "";
			$mainContent = (!empty($c['html'])) ? $c['html'] : '';
			$leftWidth = (empty($c['space'])) ? '' : $c['space'];
			$leftContent = (($leftWidth && (!$forceRightHeadline)) ? $headline : '');
			$rightContent = '<div class="defaultfont">' . ((($leftContent === '') || $forceRightHeadline) ? ($headline . '<div>' . $mainContent . '</div>') : '<div>' . $mainContent . '</div>') . '</div>';

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

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case 'frameset':
				return $this->getHTMLFrameset('', ($tab = we_base_request::_(we_base_request::INT, 'tab')) !== false ? '&tab=' . $tab : '');
			case 'editor':
				return $this->getHTMLEditor(($tab = we_base_request::_(we_base_request::INT, 'tab')) !== false ? '&tab=' . $tab : '');
			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

	protected function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		if(($modelid = we_base_request::_(we_base_request::INT, 'modelid'))){
			$this->Model = new we_search_model();
			$this->Model->load($modelid);
			$this->Model->saveInSession();
			$_SESSION['weS'][$this->toolName]["modelidForTree"] = $modelid;
		} else {
			$this->Model = $this->Model ? : new we_search_model();
		}

		return parent::getHTMLFrameset($this->Tree->getJSTreeCode() . $extraHead, ($modelid ? '&modelid=' . $modelid : '') . $extraUrlParams);
	}

}
