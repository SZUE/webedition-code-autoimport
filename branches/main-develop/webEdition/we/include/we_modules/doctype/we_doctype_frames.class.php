<?php

/**
 * webEdition CMS
 *
 * $Rev: 13767 $
 * $Author: mokraemer $
 * $Date: 2017-05-19 14:16:12 +0200 (Fr, 19. Mai 2017) $
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
class we_doctype_frames extends we_modules_frame{
	const TAB_PROPERTIES = 1;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->module = 'doctype';
		$this->Tree = new we_doctype_tree($this->jsCmd);
		$this->View = new we_doctype_view();
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case 'frameset':
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode());
			case "edfooter":
				return $this->getHTMLEditorFooter([
						we_html_button::SAVE => [['EDIT_DOCTYPE'], 'save_docType'],
						we_html_button::DELETE => [['EDIT_DOCTYPE'], 'confirmDeleteDocType']
				]);

			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return parent::getHTMLEditorHeader(0);
		}

		$we_tabs = new we_gui_tabs();

		$we_tabs->addTab('', we_base_constants::WE_ICON_PROPERTIES, false, self::TAB_PROPERTIES, ["id" => "tab_" . self::TAB_PROPERTIES, 'title' => g_l('modules_voting', '[property]')]);

		$this->jsCmd->addCmd('setTab', self::TAB_PROPERTIES);
		$tabsHead = we_html_element::cssLink(CSS_DIR . 'we_tab.css') .
			we_html_element::jsScript(JS_DIR . 'initTabs.js') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . '/doctype/doctype_top.js');


		$body = we_html_element::htmlBody([
				"onresize" => "weTabs.setFrameSize()",
				"onload" => "weTabs.setFrameSize();document.getElementById('tab_" . self::TAB_PROPERTIES . "').className='tabActive';",
				"id" => "eHeaderBody"
				], '<div id="main"><div id="headrow"><b>' . g_l('weClass', '[doctypes]') . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $this->View->Model->DocType) . '</b></span></div>' .
				$we_tabs->getHTML() .
				'</div>'
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->View->getHomeScreen();
		}

		return $this->getHTMLDocument(we_html_element::htmlBody(['class' => 'weEditorBody', "onload" => "loaded=1;", "onunload" => "doUnload()"], we_html_element::htmlForm([
						'name' => 'we_form', "onsubmit" => "return false"], $this->View->getCommonHiddens(['cmd' => 'doctype_edit', 'pnt' => 'edbody']) . $this->getHTMLProperties($this->jsCmd))), $this->View->getJSProperty() . $this->jsCmd->getCmds());
	}

	function getHTMLTab1(we_base_jsCmd $jsCmd){
		return [
			["headline" => g_l('weClass', '[name]'),
				"html" => $this->View->Model->formName(),
				'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('global', '[templates]'),
				"html" => $this->View->Model->formDocTypeTemplates($jsCmd),
				'space' => we_html_multiIconBox::SPACE_MED
			],
			["headline" => g_l('weClass', '[defaults]'),
				"html" => $this->View->Model->formDocTypeDefaults($jsCmd),
				'space' => we_html_multiIconBox::SPACE_MED
			]
		];
	}

	function getHTMLProperties(we_base_jsCmd $jsCmd){// TODO: move to View
		$t = we_base_request::_(we_base_request::INT, 'tabnr', 1);
		return we_html_element::jsScript(JS_DIR . 'utils/multi_editMulti.js') .
			we_html_element::htmlDiv(['id' => 'tab1'], we_html_multiIconBox::getHTML('', $this->getHTMLTab1($jsCmd), 0, '', -1, '', '', false, $preselect));
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody(), $this->jsCmd->getCmds());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);
		$this->jsCmd->addCmd('loadTree', ['clear' => !$pid, 'items' => we_doctype_tree::getItems($pid, $offset, $this->Tree->default_segment)]);

		return $this->getHTMLDocument(
				we_html_element::htmlBody([], we_html_element::htmlForm(
						['name' => 'we_form'], we_html_element::htmlHiddens([
							"pnt" => "cmd",
							"cmd" => "no_cmd"]
						)
					)
		));
	}

}
