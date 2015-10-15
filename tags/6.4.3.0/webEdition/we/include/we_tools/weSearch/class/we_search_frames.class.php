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
		$this->toolUrl = WE_INCLUDES_DIR . 'we_tools/' . $this->toolName . '/';
		$this->toolDir = $_SERVER['DOCUMENT_ROOT'] . $this->toolUrl;

		$_frameset = $this->toolUrl . 'edit_' . $this->toolName . '_frameset.php';
		parent::__construct($_frameset);
		$this->Table = SUCHE_TABLE;

		$this->TreeSource = 'table:' . $this->Table;

		$this->Tree = new we_search_tree();

		$this->View = new we_search_view($_frameset, 'top.content');
		$this->Model = &$this->View->Model;
		$this->setupTree(SUCHE_TABLE, 'top.content', 'top.content.resize.left.tree', 'top.content.cmd');
	}

	function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::INT, 'pid')) === false){
			exit();
		}

		$offset = we_base_request::_(we_base_request::INT, 'offset', 0);
		$_loader = new we_search_treeDataSource($this->TreeSource);

		$rootjs = (!$pid ?
						$this->Tree->topFrame . '.treeData.clear();' .
						$this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));' :
						'');


		$hiddens = we_html_element::htmlHidden(array('name' => 'pnt', 'value' => 'cmd')) .
				we_html_element::htmlHidden(array('name' => 'cmd', 'value' => 'no_cmd'));

		$out = we_html_element::htmlBody(
						array(
					'bgcolor' => 'white',
					'marginwidth' => 10,
					'marginheight' => 10,
					'leftmargin' => 10,
					'topmargin' => 10
						), we_html_element::htmlForm(
								array(
							'name' => 'we_form'
								), $hiddens . we_html_element::jsElement(
										$rootjs . $this->Tree->getJSLoadTree(
												$_loader->getItems($pid, $offset, $this->Tree->default_segment, '')))));

		if(isset($_SESSION['weS']['weSearch']['modelidForTree'])){
			$out .= we_html_element::jsElement(
							'' . $this->topFrame . '.treeData.selectnode("' . ($_SESSION['weS']['weSearch']["modelidForTree"]) . '");');
			unset($_SESSION['weS']['weSearch']['modelidForTree']);
		}

		return $this->getHTMLDocument($out);
	}

	protected function getHTMLEditorHeader(){

		$we_tabs = new we_tabs();

		//folders and entries have different tabs to display
		$displayEntry = 'none';
		$displayFolder = 'inline';

		if($this->Model->IsFolder == 0){
			$displayEntry = 'inline';
			$displayFolder = 'none';
		}

		//tabs for entries
		if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
			$we_tabs->addTab(new we_tab(
					'#', g_l('searchtool', '[documents]'), '((' . $this->topFrame . '.activ_tab==1) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('1');", array(
				'id' => 'tab_1', 'style' => "display:$displayEntry"
			)));
		}
		if($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE && permissionhandler::hasPerm('CAN_SEE_TEMPLATES')){
			$we_tabs->addTab(new we_tab(
					'#', g_l('searchtool', '[templates]'), '((' . $this->topFrame . '.activ_tab==2) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('2');", array(
				'id' => 'tab_2', 'style' => "display:$displayEntry"
			)));
		}
		$we_tabs->addTab(new we_tab(
				'#', g_l('searchtool', '[advSearch]'), '((' . $this->topFrame . '.activ_tab==3) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('3');", array(
			'id' => 'tab_3', 'style' => "display:$displayEntry"
		)));

		//tabs for folders
		$we_tabs->addTab(new we_tab(
				'#', g_l('searchtool', '[properties]'), '((' . $this->topFrame . '.activ_tab==4) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('4');", array(
			'id' => 'tab_4', 'style' => "display:$displayFolder"
		)));

		$we_tabs->onResize();

		$tabsHead = $we_tabs->getHeader();

		$activeTabJS = '';

		$tabNr = $this->getTab();

		$activeTabJS .= $this->topFrame . '.activ_tab = ' . $tabNr . ';';

		$tabsHead .= we_html_element::jsElement($activeTabJS . '

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
          ' . $this->topFrame . '.activ_tab=tab;

        }');


		$setActiveTabJS = 'document.getElementById("tab_"+' . $this->topFrame . '.activ_tab).className="tabActive";';

		$Text = we_search_model::getLangText($this->Model->Path, $this->Model->Text);

		$body = we_html_element::htmlBody(
						array(
					'bgcolor' => '#C8D8EC',
					'background' => IMAGE_DIR . 'backgrounds/header_with_black_line.gif',
					'style' => 'margin:0px 0px 0px 0px',
					'onload' => 'setFrameSize()',
					'onresize' => 'setFrameSize()'
						), '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;" id="headrow">&nbsp;' . we_html_element::htmlB(
								g_l('searchtool', ($this->Model->IsFolder ? '[topDir]' : '[topSuche]')) . ':&nbsp;' .
								$Text . '<div id="mark" style="display: none;">*</div>') . '</div>' . we_html_tools::getPixel(
								100, 3) . $we_tabs->getHTML() . '</div>' . we_html_element::jsElement($setActiveTabJS));

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){
		$body = we_html_element::htmlBody(
						array(
					'class' => 'weEditorBody',
					'onkeypress' => 'javascript:if(event.keyCode==\'13\' || event.keyCode==\'3\') search(true);',
					'onload' => 'loaded=1;setTimeout(\'init()\',200);',
					'onresize' => 'sizeScrollContent();'
						), we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js') .
						we_html_element::htmlForm(
								array(
							'name' => 'we_form', 'onsubmit' => 'return false'
								), $this->getHTMLProperties() . we_html_element::htmlHidden(
										array(
											'name' => 'predefined', 'value' => $this->Model->predefined
								)) . we_html_element::htmlHidden(
										array(
											'name' => 'savedSearchName', 'value' => $this->Model->Text
		))));

		$whichSearch = 'DocSearch';

		$tabNr = $this->getTab();

		switch($tabNr){
			case 1 :
				$whichSearch = 'DocSearch';
				break;
			case 2 :
				$whichSearch = 'TmplSearch';
				break;
			case 3 :
				$whichSearch = 'AdvSearch';
				break;
		}

		$head = we_html_element::linkElement(
						array(
							'rel' => 'stylesheet',
							'type' => 'text/css',
							'href' => JS_DIR . 'jscalendar/skins/aqua/theme.css',
							'title' => 'Aqua'
				)) . we_html_element::jsScript(JS_DIR . 'jscalendar/calendar.js') .
				we_html_element::jsScript(WE_INCLUDES_DIR . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/calendar.js') .
				we_html_element::jsScript(JS_DIR . 'jscalendar/calendar-setup.js');


		return $this->getHTMLDocument(
						$body, $head . STYLESHEET . $this->View->getJSProperty() . $this->View->getSearchJS($whichSearch));
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
			return $this->Model->activTab;
		}
		return we_base_request::_(we_base_request::INT, 'tabnr', 1);
	}

	protected function getHTMLEditorFooter(){
		$_but_table = we_html_button::create_button_table(
						array(
					we_html_button::create_button(
							'save', 'javascript:we_save();', true, 100, 22, '', '', (!permissionhandler::hasPerm('EDIT_NAVIGATION')))
						), 10, array(
					'style' => 'margin-left: 15px;margin-top:10px;'
		));

		return $this->getHTMLDocument(
						we_html_element::jsScript(JS_DIR . 'attachKeyListener.js') .
						we_html_element::jsElement('
          function we_save() {
            ' . $this->topFrame . '.we_cmd("tool_' . $this->toolName . '_save");
          }
          ') . we_html_element::htmlBody(
								array(
							'bgcolor' => 'white',
							'background' => IMAGE_DIR . 'edit/editfooterback.gif',
							'marginwidth' => 0,
							'marginheight' => 0,
							'leftmargin' => 0,
							'topmargin' => 0
								), we_html_element::htmlForm(array(), $_but_table)));
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
				we_html_element::htmlHidden(array(
					'name' => 'newone', 'value' => ($this->Model->ID == 0 ? 1 : 0)
				)) .
				we_html_element::htmlDiv(array(
					'id' => 'tab1', 'style' => ($tabNr == 1 ? 'display: block;' : 'display: none')
						), $this->getHTMLSearchtool($this->getHTMLTabDocuments())) .
				we_html_element::htmlDiv(array(
					'id' => 'tab2', 'style' => ($tabNr == 2 ? 'display: block;' : 'display: none')
						), $this->getHTMLSearchtool($this->getHTMLTabTemplates())) .
				we_html_element::htmlDiv(array(
					'id' => 'tab3', 'style' => ($tabNr == 3 ? 'display: block;' : 'display: none')
						), $this->getHTMLSearchtool($this->getHTMLTabAdvanced())) .
				we_html_element::htmlDiv(array(
					'id' => 'tab4', 'style' => ($tabNr == 4 ? 'display: block;' : 'display: none')
						), $this->getHTMLSearchtool($this->getHTMLGeneral()));
	}

	function getHTMLGeneral(){
		$disabled = true;

		$this->Model->Text = we_search_model::getLangText($this->Model->Path, $this->Model->Text);

		return array(
			array(
				'headline' => g_l('searchtool', '[general]'),
				'html' => we_html_tools::htmlFormElementTable(
						we_html_tools::htmlTextInput('Text', '', $this->Model->Text, '', 'style="width: ' . $this->_width_size . 'px" );"', '', '', '', '', $disabled), g_l('searchtool', '[dir]')),
				'space' => $this->_space_size,
				'noline' => 1
		));
	}

	function getHTMLTabDocuments(){
		//parameter: search of the tab (load only search dependent model data in the view)
		$innerSearch = 'DocSearch';

		$_searchDirChooser_block = '<div>' . $this->View->getDirSelector($innerSearch) . '</div>';
		$_searchField_block = '<div>' . $this->View->getSearchDialog($innerSearch) . '</div>';
		$_searchCheckboxes_block = '<div>' . $this->View->getSearchDialogCheckboxes($innerSearch) . '</div>';

		$content = $this->View->searchProperties($innerSearch);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $innerSearch . ''];

		$_searchResult_block = '<div>
      <div id=\'parametersTop_' . $innerSearch . '\'>' . $this->View->getSearchParameterTop($foundItems, $innerSearch) . '</div>' . $this->View->tblList($content, $headline, $innerSearch) . '<div id=\'parametersBottom_' . $innerSearch . '\'>' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
      </div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[suchenIn]'),
				'html' => $_searchDirChooser_block,
				'space' => $this->_space_size
			),
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $_searchField_block,
				'space' => $this->_space_size
			),
			array(
				'headline' => g_l('searchtool', '[optionen]'),
				'html' => $_searchCheckboxes_block,
				'space' => $this->_space_size
			),
			array(
				'headline' => '', 'html' => $_searchResult_block, 'space' => $this->_space_size
		));
	}

	function getHTMLTabTemplates(){
		$_searchDirChooser_block = '<div>' . $this->View->getDirSelector('TmplSearch') . '</div>';
		$_searchField_block = '<div>' . $this->View->getSearchDialog('TmplSearch') . '</div>';
		$_searchCheckboxes_block = '<div>' . $this->View->getSearchDialogCheckboxes('TmplSearch') . '</div>';
		$content = $this->View->searchProperties('TmplSearch');
		$headline = $this->View->makeHeadLines('TmplSearch');
		$foundItems = $_SESSION['weS']['weSearch']['foundItemsTmplSearch'];

		$_searchResult_block = '<div>
      <div id="parametersTop_TmplSearch">' . $this->View->getSearchParameterTop(
						$foundItems, 'TmplSearch') . '</div>' . $this->View->tblList($content, $headline, 'TmplSearch') . '<div id="parametersBottom_TmplSearch">' . $this->View->getSearchParameterBottom($foundItems, 'TmplSearch') . '</div>
      </div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[suchenIn]'),
				'html' => $_searchDirChooser_block,
				'space' => $this->_space_size
			),
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $_searchField_block,
				'space' => $this->_space_size
			),
			array(
				'headline' => g_l('searchtool', '[optionen]'),
				'html' => $_searchCheckboxes_block,
				'space' => $this->_space_size
			),
			array(
				'headline' => '', 'html' => $_searchResult_block, 'space' => $this->_space_size
		));
	}

	function getHTMLTabAdvanced(){
		$innerSearch = 'AdvSearch';
		$_searchFields_block = '<div>' . $this->View->getSearchDialogAdvSearch() . '</div>';
		$_searchCheckboxes_block = '<div>' . $this->View->getSearchDialogCheckboxesAdvSearch() . '</div>';
		$content = $this->View->searchProperties($innerSearch);
		$headline = $this->View->makeHeadLines($innerSearch);
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $innerSearch . ''];

		$_searchResult_block = '<div>
      <div id=\'parametersTop_' . $innerSearch . '\'>' . $this->View->getSearchParameterTop(
						$foundItems, $innerSearch) . '</div>' . $this->View->tblList($content, $headline, $innerSearch) . '<div id=\'parametersBottom_' . $innerSearch . '\'>' . $this->View->getSearchParameterBottom($foundItems, $innerSearch) . '</div>
      </div>';

		return array(
			array(
				'headline' => g_l('searchtool', '[text]'),
				'html' => $_searchFields_block,
				'space' => $this->_space_size
			),
			array(
				'headline' => g_l('searchtool', '[anzeigen]'),
				'html' => $_searchCheckboxes_block,
				'space' => $this->_space_size
			),
			array(
				'headline' => '', 'html' => $_searchResult_block, 'space' => $this->_space_size
		));
	}

	function getHTMLSearchtool($content){
		$out = '';

		foreach($content as $i => $c){
			$_forceRightHeadline = (isset($c['forceRightHeadline']) && $c['forceRightHeadline']);
			$icon = (isset($c['icon']) && $c['icon'] ?
							'<img src="' . ICON_DIR . $c["icon"] . '" width="64" height="64" alt="" style="margin-left:20px;" />' :
							"");
			$headline = (isset($c['headline']) && $c['headline']) ? ('<div  class="weMultiIconBoxHeadline" style="margin-bottom:10px;margin-left:30px;">' . $c["headline"] . '</div>') : "";
			$mainContent = (isset($c['html']) && $c['html']) ? $c['html'] : '';
			$leftWidth = (isset($c['space']) && $c['space']) ? abs($c['space']) : 0;
			$leftContent = $icon ? : (($leftWidth && (!$_forceRightHeadline)) ? $headline : '');
			$rightContent = '<div class="defaultfont">' . ((($icon && $headline) || ($leftContent === '') || $_forceRightHeadline) ? ($headline . '<div>' . $mainContent . '</div>') : '<div>' . $mainContent . '</div>') . '</div>';

			if($leftContent || $leftWidth && $leftContent != ''){
				if((!$leftContent) && $leftWidth){
					$leftContent = '&nbsp;';
				}
				$out .= '<div style="float:left;width:' . $leftWidth . 'px">' . $leftContent . '</div>';
			}

			$out .= $rightContent .
					'<div style="clear:both;' . ($i < (count($content) - 1) && (!isset($c['noline'])) ?
							'border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;' :
							'margin:10px 0;'
					) . '"></div>';
		}

		return $out;
	}

}
