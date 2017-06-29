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
abstract class we_tool_frames extends we_modules_frame{

	var $Table;
	var $TreeSource = 'table:';
	var $toolName;
	var $toolClassName;
	var $toolDir;
	var $toolUrl;
	var $Model;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->showTreeFooter = false;
		$this->treeWidth = 200;
	}

	protected function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		$class = we_tool_lookup::getModelClassName($this->toolName);
		$this->Model = $this->Model ?: new $class();

		if(($modelid = we_base_request::_(we_base_request::INT, 'modelid'))){
			$this->Model = new $class();
			$this->Model->load($modelid);
			$this->Model->saveInSession();
			$_SESSION['weS'][$this->toolName]["modelidForTree"] = $modelid;
		}

		return parent::getHTMLFrameset($this->Tree->getJSTreeCode() . $extraHead, ($modelid ? '&modelid=' . $modelid : '') . $extraUrlParams);
	}

	/**
	 * Frame for tubs
	 *
	 * @return string
	 */
	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return parent::getHTMLEditorHeader(0);
		}

		$we_tabs = new we_gui_tabs();
		$we_tabs->addTab(g_l('tools', '[properties]'),'', false, "1", ["id" => "tab_1"]);

		$body = we_html_element::htmlBody(["id" => "eHeaderBody", "onload" => ($this->Model->ID ? '' : 'top.content.activ_tab=1;') . "document.getElementById('tab_'+top.content.activ_tab).className='tabActive';setFrameSize()",
					"onresize" => "setFrameSize()"], '<div id="main" ><div id="headrow">&nbsp;' . we_html_element::htmlB(g_l('tools', ($this->Model->IsFolder ? '[group]' : '[entry]')) . ':&nbsp;' . str_replace('&amp;', '&', $this->Model->Text) . '<div id="mark"><i class="fa fa-asterisk modified"></i></div>') . '</div>' .
						$we_tabs->getHTML() .
						'</div>'
		);

		return $this->getHTMLDocument($body, we_html_element::cssLink(CSS_DIR . 'we_tab.css') . we_html_element::jsScript(WE_JS_MODULES_DIR . 'we_tool_frames_tab.js'));
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}

		$body = we_html_element::htmlBody(['class' => 'weEditorBody', 'onload' => 'loaded=1;'], we_html_element::htmlForm(['name' => 'we_form', 'onsubmit' => 'return false'], $this->getHTMLProperties()
						)
		);

		return $this->getHTMLDocument($body, $this->View->getJSProperty());
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case 'edfooter':
				return $this->getHTMLEditorFooter([
							we_html_button::SAVE => [[], 'tool_' . $this->toolName . '_save']
								], we_html_element::cssLink(CSS_DIR . 'tools_home.css'));
			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

	function getHTMLGeneral(){
		return [['headline' => g_l('tools', '[general]'),
		'html' => we_html_element::htmlHidden('newone', ($this->Model->ID == 0 ? 1 : 0)) .
		we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Text', '', $this->Model->Text, '', 'style="width: 520px;" onchange="top.content.mark();"'), g_l('tools', '[name]')) .
		$this->getHTMLChooser(g_l('tools', '[group]'), $this->Table, 0, 'ParentID', $this->Model->ParentID, 'ParentPath', 'opener.top.content.mark()', ''),
		'space' => we_html_multiIconBox::SPACE_MED,
		'noline' => 1
			]
		];
	}

	function getHTMLProperties($preselect = ''){
		$tabNr = we_base_request::_(we_base_request::INT, 'tabnr', 1);

		$hiddens = ['cmd' => '',
			'pnt' => 'edbody',
			'tabnr' => $tabNr,
		];

		return $this->View->getCommonHiddens($hiddens) .
				we_html_multiIconBox::getHTML('', $this->getHTMLGeneral(), 30);
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::STRING, "pid")) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);
		$class = $this->toolClassName . 'TreeDataSource';

		$loader = new $class($this->TreeSource);
		$this->jsCmd->addCmd('loadTree', ['clear' => !$pid, 'items' => $loader->getItems($pid, $offset, $this->Tree->default_segment, '')]);

		return $this->getHTMLDocument(we_html_element::htmlBody([], we_html_element::htmlForm(['name' => 'we_form'], we_html_element::htmlHiddens(['pnt' => 'cmd',
											'cmd' => 'no_cmd'])
								)
		));
	}

	/* protected function getHTMLExitQuestion(){
	  if(($dc = we_base_request::_(we_base_request::STRING, 'delayCmd'))){
	  $yes = 'opener.top.content.hot=false;opener.top.content.we_cmd(\'tool_' . $this->toolName . '_save\');self.close();';
	  $no = 'opener.top.content.hot=false;opener.top.content.we_cmd(\'' . implode("','", $dc) . '\');self.close();';
	  $cancel = 'self.close();';

	  return we_html_tools::getHtmlTop('', '', '', '', '<body class="weEditorBody" onblur="self.focus()" onload="self.focus()">' .
	  we_html_tools::htmlYesNoCancelDialog(g_l('tools', '[exit_doc_question]'), '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', "ja", "nein", "abbrechen", $yes, $no, $cancel) .
	  '</body>');
	  }
	  } */

	function getHTMLDocument($body, $head = ''){
		return we_html_tools::getHtmlTop('', '', '', ' ' . $this->jsCmd->getCmds() . $head, $body);
	}

	private function getHTMLChooser($title, $table = FILE_TABLE, $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $PathName = 'Path', $cmd = '', $filter = we_base_ContentTypes::WEDOCUMENT, $disabled = false, $showtrash = false){
		$path = id_to_path($this->Model->$IDName, $table);
		$cmd = "javascript:we_cmd('open" . $this->toolName . "Dirselector',document.we_form.elements['" . $IDName . "'].value,'document.we_form." . $IDName . ".value','document.we_form." . $PathName . ".value','" . $cmd . "')";

		if($showtrash){
			$button = we_html_button::create_button(we_html_button::SELECT, $cmd, '', 0, 0, '', '', $disabled) .
					we_html_button::create_button(we_html_button::TRASH, 'javascript:document.we_form.elements["' . $IDName . '"].value=0;document.we_form.elements["' . $PathName . '"].value="/";');
			$width = 157;
		} else {
			$button = we_html_button::create_button(we_html_button::SELECT, $cmd, '', 0, 0, '', '', $disabled);
			$width = 120;
		}

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($PathName, 58, $path, '', 'readonly', 'text', (520 - $width), 0), $title, 'left', 'defaultfont', we_html_element::htmlHidden($IDName, $IDValue), $button);
	}

}
