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
//TODO in modulesFrames: set module-settings as class properties instead of looping them through as method parameters!

abstract class we_modules_frame{
	var $module;
	var $db;
	var $frameset;
	var $View;
	var $Tree = null;
	var $topFrame = "top.content";
	var $treeFrame = "top.content";
	var $cmdFrame = "top.content.cmd";
	protected $showTreeHeader = false;
	protected $showTreeFooter = false;
	protected $treeDefaultWidth = 200;
	protected $treeWidth = 0;
	protected $hasIconbar = false;

	function __construct($frameset){
		$this->db = new DB_WE();
		$this->frameset = $frameset;
	}

	public function getHTMLDocumentHeader($charset = ''){
		$charset = ($charset ?: $GLOBALS['WE_BACKENDCHARSET']);
		we_html_tools::headerCtCharset('text/html', $charset);
		return we_html_tools::getHtmlTop($this->module, $charset);
	}

	function getHTMLDocument($body, $extraHead = ''){
		return $this->getHTMLDocumentHeader() .
			$extraHead .
			(empty($GLOBALS['extraJS']) ? '' : $GLOBALS['extraJS']) .
			YAHOO_FILES .
			'</head>' . $body . '</html>';
	}

	function getJSCmdCode(){
		return $this->View->getJSTop();
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case 'frameset':
				return $this->getHTMLFrameset();
			case 'editor':
				return $this->getHTMLEditor();
			case 'edheader':
				return $this->getHTMLEditorHeader();
			case 'edbody':
				return $this->getHTMLEditorBody();
			case 'edfooter':
				return $this->getHTMLEditorFooter([]);
			case 'cmd':
				return $this->getHTMLCmd();
			case 'tree':
				return $this->getHTMLTree();
			case 'search':
				return $this->getHTMLSearch();
			case 'exit_doc_question':
				return $this->getHTMLExitQuestion();
			case 'treeheader':
				return $this->getHTMLTreeHeader();
			default:
				$ret = (empty($GLOBALS['extraJS']) ?
					'' :
					$this->getHTMLDocument('<body></body>', $GLOBALS['extraJS'])
					);
				unset($GLOBALS['extraJS']);
				t_e(__FILE__ . ' unknown reference: ' . $what, ($ret ? 'generated emergency document' : ''));
				return $ret;
		}
	}

	protected function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		$this->setTreeWidthFromCookie();

		$extraHead = $this->getJSCmdCode() .
			//FIXME: throw some of these functions out again and use generic version of main-window functions
			we_html_element::jsScript(JS_DIR . 'modules_tree.js') .
			we_main_headermenu::css() .
			we_base_menu::getJS() .
			$extraHead;

		$body = we_html_element::htmlBody(['id' => 'weMainBody', "onload" => 'startTree();'], we_html_element::htmlExIFrame('header', self::getHTMLHeader(
						(isset($this->toolDir) ?
						$this->toolDir . 'conf/we_menu_' . $this->toolName . '.conf.php' :
						WE_INCLUDES_PATH . 'menu/module_menu_' . $this->module . '.inc.php'))) .
				($this->hasIconbar ? we_html_element::htmlIFrame('iconbar', $this->frameset . '&pnt=iconbar' . $extraUrlParams, '', '', '', false) : '') .
				$this->getHTMLResize($extraUrlParams) .
				we_html_element::htmlIFrame('cmd', $this->frameset . '&pnt=cmd' . $extraUrlParams)
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLHeader($menuFile){
		$inc = include($menuFile);
		$jmenu = new we_base_menu($inc, 'top.opener.top.load', '');
		$menu = $jmenu->getHTML();

		return we_html_element::htmlDiv(['class' => 'menuDiv'], $menu .
				we_html_element::htmlDiv(['id' => 'moduleMessageConsole'], we_main_headermenu::createMessageConsole('moduleFrame', false))
			) . we_html_element::jsElement(we_main_headermenu::createMessageConsole('moduleFrame', true));
	}

	private function getHTMLResize($extraUrlParams = ''){
		$incDecTree = '<div id="baumArrows">
	<div class="baumArrow" id="incBaum" ' . ($this->treeWidth <= 30 ? 'style="background-color: grey"' : '') . ' onclick="top.content.incTree(\'' . $this->module . '\');"><i class="fa fa-plus"></i></div>
	<div class="baumArrow" id="decBaum" ' . ($this->treeWidth <= 30 ? 'style="background-color: grey"' : '') . ' onclick="top.content.decTree(\'' . $this->module . '\');"><i class="fa fa-minus"></i></div>
	<div class="baumArrow" onclick="top.content.toggleTree(\'' . $this->module . '\');"><i id="arrowImg" class="fa fa-lg fa-caret-' . ($this->treeWidth <= 30 ? "right" : "left") . '" ></i></div>
</div>';

		$content = we_html_element::htmlDiv(['id' => 'moduleContent'], we_html_element::htmlDiv(['id' => 'lframeDiv', 'style' => 'width: ' . $this->treeWidth . 'px;'], we_html_element::htmlDiv([
						'id' => 'vtabs'], $incDecTree) .
					$this->getHTMLLeft()
				) .
				we_html_element::htmlDiv(['id' => 'right', 'style' => 'left: ' . $this->treeWidth . 'px;'], we_html_element::htmlIFrame('editor', $this->frameset . '&pnt=editor' . $extraUrlParams, ' ', '', '', false)
				)
		);

		return we_html_element::htmlDiv(['id' => 'resize', 'name' => 'resize', 'class' => ($this->hasIconbar ? 'withIconBar' : ''), 'style' => 'overflow:hidden'], $content);
	}

	protected function getHTMLLeft(){
		//we load tree in iFrame, because the complete tree JS is based on document.open() and document.write()
		//it makes not much sense, to rewrite trees before abandoning them anyway
		return we_html_element::htmlDiv(['id' => 'left', 'name' => 'left'], we_html_element::htmlDiv(['id' => 'treeheader', 'style' => ($this->showTreeHeader ? 'display:block;' : '')], $this->getHTMLTreeheader()) .
				$this->getHTMLTree() .
				($this->showTreeFooter ? we_html_element::htmlDiv(['id' => 'treefooter', 'class' => 'editfooter'], $this->getHTMLTreefooter()) :
				''
				)
		);
	}

	protected function getHTMLTree($extraHead = ''){
		return we_html_element::htmlDiv([
				'id' => 'tree',
				'class' => ($this->showTreeHeader ? ' withHeader' : '') . ($this->showTreeFooter ? ' withFooter' : '')
				], $extraHead . $this->Tree->getHTMLConstruct()
		);
	}

	protected function getHTMLTreeHeader(){
		return '';
		//to be overridden
	}

	protected function getHTMLTreeFooter(){
		return '';
		//to be overridden
	}

	protected function getHTMLSearchTreeFooter(){
		$hiddens = we_html_element::htmlHiddens([
				'pnt' => 'cmd',
				'cmd' => 'show_search']);

		$table = $hiddens .
			we_html_tools::htmlTextInput("keyword", 10, '', '', 'placeholder="' . g_l('buttons_modules_message', '[search][alt]') . '"', "text", "150px") .
			we_html_button::create_button(we_html_button::SEARCH, "javascript:submitForm('cmd', '', '', 'we_form_treefooter')");

		return we_html_element::jsElement($this->View->getJSSubmitFunction('cmd')) .
			we_html_element::htmlDiv(['id' => 'search', 'style' => 'display:block'], we_html_element::htmlForm(['name' => 'we_form_treefooter', 'target' => 'cmd'], $table));
	}

	protected function getHTMLEditor($extraUrlParams = '', $extraHead = ''){
		$sid = we_base_request::_(we_base_request::STRING, 'sid');
		$body = we_html_element::htmlBody(['class' => 'moduleEditor'], we_html_element::htmlIFrame('edheader', $this->frameset . '&pnt=edheader' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, '', 'width: 100%; overflow: hidden', '', false, 'editorHeader') .
				we_html_element::htmlIFrame('edbody', $this->frameset . '&pnt=edbody' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, '', 'border:0px;width:100%;height:100%;', '', true, 'editorBody') .
				we_html_element::htmlIFrame('edfooter', $this->frameset . '&pnt=edfooter' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, '', 'width: 100%; overflow: hidden', '', false, 'editorButtonFrame')
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(['class' => 'home'], ''), we_html_element::cssLink(CSS_DIR . 'tools_home.css'));
		}
	}

	protected function getHTMLEditorBody(){
// to be overridden
	}

	protected function getHTMLEditorFooter(array $btn_cmd, $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(["style" => "background-color:#EFF0EF"], ""), $extraHead);
		}

		$table2 = new we_html_table(['class' => 'default'], 1, count($btn_cmd));
		$table2->setRow(0, ['style' => 'vertical-align:middle']);
		$pos = 0;
		foreach($btn_cmd as $but => $cur){
			list($right, $cmd) = $cur;
			if(empty($right) || permissionhandler::hasPerm($right)){
				$table2->setColContent(0, $pos++, we_html_button::create_button($but, "javascript:top.content.we_cmd('" . $cmd . "')"));
			}
		}
		return $this->getHTMLDocument(we_html_element::htmlBody(['id' => 'footerBody'], $table2->getHtml()), $extraHead);
	}

	protected function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody(), $this->Tree->getJSLoadTree());
	}

	function getHTMLSearch(){
		// to be overridden
	}

	protected function getHTMLExitQuestion(){
		if(($dc = we_base_request::_(we_base_request::RAW, 'delayCmd'))){
			$yes = 'opener.top.content.hot=0;opener.top.content.we_cmd("module_' . $this->module . '_save");self.close();';
			$no = 'opener.top.content.hot=0;opener.top.content.we_cmd("' . $dc . '","' . we_base_request::_(we_base_request::INT, 'delayParam') . '");self.close();';
			$cancel = 'self.close();';

			return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', '', '<body class="weEditorBody" onBlur="self.focus()" onload="self.focus()">' .
					we_html_tools::htmlYesNoCancelDialog(g_l('tools', '[exit_doc_question]'), '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', "ja", "nein", "abbrechen", $yes, $no, $cancel) .
					'</body>');
		}
	}

	private function setTreeWidthFromCookie(){
		if(isset($_COOKIE["treewidth_modules"])){
			$tw = we_unserialize($_COOKIE["treewidth_modules"]);
			$this->treeWidth = empty($tw[$this->module]) ? $this->treeDefaultWidth : $tw[$this->module];
		} else {
			$this->treeWidth = $this->treeDefaultWidth;
		}
	}

	protected function formFileChooser($width = '', $IDName = 'ParentID', $IDValue = '/', $cmd = '', $filter = ''){
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server','" . $IDName . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value);");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, '', 'readonly', 'text', 400, 0), "", "left", "defaultfont", "", permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	/* process vars & commands
	 */

	public function process(){
		ob_start();
		$this->View->processVariables();
		$this->View->processCommands();
		$GLOBALS['extraJS'] = ob_get_clean();
	}

}
