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
	private static $treeWidthsJS = '{}';

	function __construct($frameset){
		$this->db = new DB_WE();
		$this->frameset = $frameset;
	}

	public function getHTMLDocumentHeader($charset = ''){
		$charset = ($charset? : $GLOBALS['WE_BACKENDCHARSET']);
		we_html_tools::headerCtCharset('text/html', $charset);
		return we_html_tools::getHtmlTop($this->module, $charset) . STYLESHEET;
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

	function getHTML($what = ''){
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
				return $this->getHTMLEditorFooter();
			case 'cmd':
				return $this->getHTMLCmd();
			case 'tree':
				return $this->getHTMLTree();
			case 'search':
				return $this->getHTMLSearch();
			case 'exit_doc_question':
				return $this->getHTMLExitQuestion();
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

	function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		$this->setTreeWidthFromCookie();

		$extraHead = $this->getJSCmdCode() .
				self::getJSToggleTreeCode($this->module) .
				we_main_headermenu::css() .
				$extraHead;

		$body = we_html_element::htmlBody(array('id' => 'weMainBody', "onload" => 'startTree();'), we_html_element::htmlExIFrame('header', self::getHTMLHeader(
										(isset($this->toolDir) ?
												$this->toolDir . 'conf/we_menu_' . $this->toolName . '.conf.php' :
												WE_INCLUDES_PATH . 'menu/module_menu_' . $this->module . '.inc.php'))) .
						($this->hasIconbar ? we_html_element::htmlIFrame('iconbar', $this->frameset . '&pnt=iconbar' . $extraUrlParams, '', '', '', false) : '') .
						$this->getHTMLResize($extraUrlParams) .
						we_html_element::htmlIFrame('cmd', $this->frameset . '&pnt=cmd' . $extraUrlParams)
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLHeader($_menuFile){
		$menu = include($_menuFile);
		$jmenu = new we_base_menu($menu, 'top.opener.top.load', '');
		$menu = $jmenu->getCode(false);

		return
				we_html_element::htmlDiv(array('class' => 'menuDiv'), $menu) .
				we_html_element::htmlDiv(array('id' => 'moduleMessageConsole'), we_main_headermenu::createMessageConsole('moduleFrame'));
	}

	private function getHTMLResize($extraUrlParams = ''){
		$_incDecTree = '<div id="baumArrows">
	<div class="baumArrow" id="incBaum" ' . ($this->treeWidth <= 30 ? 'style="background-color: grey"' : '') . ' onclick="top.content.incTree();"><i class="fa fa-plus"></i></div>
	<div class="baumArrow" id="decBaum" ' . ($this->treeWidth <= 30 ? 'style="background-color: grey"' : '') . ' onclick="top.content.decTree();"><i class="fa fa-minus"></i></div>
	<div class="baumArrow" onclick="top.content.toggleTree();"><i id="arrowImg" class="fa fa-lg fa-caret-' . ($this->treeWidth <= 30 ? "right" : "left") . '" ></i></div>
</div>';

		$content = we_html_element::htmlDiv(array('id' => 'moduleContent'), we_html_element::htmlDiv(array('id' => 'lframeDiv', 'style' => 'width: ' . $this->treeWidth . 'px;'), we_html_element::htmlDiv(array('id' => 'vtabs'), $_incDecTree) .
								$this->getHTMLLeft()
						) .
						we_html_element::htmlDiv(array('id' => 'right', 'style' => 'left: ' . $this->treeWidth . 'px;'), we_html_element::htmlIFrame('editor', $this->frameset . '&pnt=editor' . $extraUrlParams, ' ', '', '', false)
						)
		);

		return we_html_element::htmlDiv(array('id' => 'resize', 'name' => 'resize', 'class' => ($this->hasIconbar ? 'withIconBar' : ''), 'style' => 'overflow:hidden'), $content);
	}

	function getHTMLLeft(){
		//we load tree in iFrame, because the complete tree JS is based on document.open() and document.write()
		//it makes not much sense, to rewrite trees before abandoning them anyway
		return we_html_element::htmlDiv(array(
					'id' => 'left', 'name' => 'left'), we_html_element::htmlDiv(array('id' => 'treeheader', 'style' => ($this->showTreeHeader ? 'display:block;' : '')), $this->getHTMLTreeheader()) .
						$this->getHTMLTree() .
						($this->showTreeFooter ? we_html_element::htmlDiv(array('id' => 'treefooter', 'class' => 'editfooter'), $this->getHTMLTreefooter()) :
								''
						)
		);
	}

	protected function getHTMLTree($extraHead = ''){
		return we_html_element::htmlDiv(array(
					'id' => 'tree',
					'style' => ($this->showTreeHeader ? 'top: 40px;' : '') . ($this->showTreeFooter ? 'bottom: 40px;' : '')), $extraHead . $this->Tree->getHTMLContruct()
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

	protected function getHTMLEditor($extraUrlParams = '', $extraHead = ''){
		$sid = we_base_request::_(we_base_request::STRING, 'sid');
		$body = we_html_element::htmlBody(array('class' => 'moduleEditor'), we_html_element::htmlIFrame('edheader', $this->frameset . '&pnt=edheader' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, '', 'width: 100%; overflow: hidden', '', false, 'editorHeader') .
						we_html_element::htmlIFrame('edbody', $this->frameset . '&pnt=edbody' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, '', 'border:0px;width:100%;height:100%;', '', true, 'editorBody') .
						we_html_element::htmlIFrame('edfooter', $this->frameset . '&pnt=edfooter' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, '', 'width: 100%; overflow: hidden', '', false, 'editorButtonFrame')
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorHeader(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#F0EFF0"), ""));
		}
	}

	protected function getHTMLEditorBody(){
// to be overridden
	}

	protected function getHTMLEditorFooter($btn_cmd, $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("style" => "background-color:#EFF0EF"), ""));
		}

		$extraHead .= we_html_element::jsElement('
function we_save() {
	top.content.we_cmd("' . $btn_cmd . '");
}');

		$table2 = new we_html_table(array('class' => 'default', 'style' => 'width:300px;'), 1, 2);
		$table2->setRow(0, array('style' => 'vertical-align:middle'));
		$table2->setCol(0, 1, array(), we_html_button::create_button(we_html_button::SAVE, 'javascript:we_save()'));

		return $this->getHTMLDocument(we_html_element::htmlBody(array('id' => 'footerBody'), $table2->getHtml()), $extraHead);
	}

	protected function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody(), $this->Tree->getJSLoadTree());
	}

	function getHTMLSearch(){
		// to be overridden
	}

	function getHTMLBox($content, $headline = "", $width = 100, $height = 50, $w = 25, $vh = 0, $ident = 0, $space = 5, $headline_align = "left", $content_align = "left"){
		$table = new we_html_table(array("width" => $width, "height" => $height, "class" => 'default', 'style' => 'margin-left:' . intval($ident) . 'px;margin-top:' . intval($vh) . 'px;margin-bottom:' . ($w && $headline ? $vh : 0) . 'px;'), 1, 2);

		$table->setCol(0, 0, array("style" => 'vertical-align:middle;text-align:' . $headline_align . ';padding-right:' . $space . 'px;', "class" => "defaultfont lowContrast"), str_replace(" ", "&nbsp;", $headline));
		$table->setCol(0, 1, array("style" => 'vertical-align:middle;text-align:' . $content_align), $content);
		return $table->getHtml();
	}

	protected function getHTMLExitQuestion(){
		if(($dc = we_base_request::_(we_base_request::RAW, 'delayCmd'))){
			$_frame = 'opener.top.content';
			$_yes = $_frame . '.hot=0;' . $_frame . '.we_cmd("module_' . $this->module . '_save");self.close();';
			$_no = $_frame . '.hot=0;' . $_frame . '.we_cmd("' . $dc . '","' . we_base_request::_(we_base_request::INT, 'delayParam') . '");self.close();';
			$_cancel = 'self.close();';

			return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET, '<body class="weEditorBody" onBlur="self.focus()" onload="self.focus()">' .
							we_html_tools::htmlYesNoCancelDialog(g_l('tools', '[exit_doc_question]'), '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', "ja", "nein", "abbrechen", $_yes, $_no, $_cancel) .
							'</body>');
		}
	}

	function setTreeWidthFromCookie(){
		$_tw = isset($_COOKIE["treewidth_modules"]) ? $_COOKIE["treewidth_modules"] : $this->treeDefaultWidth;
		if(!is_numeric($_tw)){
			$_tw = explode(',', trim($_tw, ' ,'));
			$_twArr = array();
			$_twJS = '{';

			foreach($_tw as $_v){
				$entry = explode(':', trim($_v));
				$_twArr[trim($entry[0])] = $entry[1];
				$_twJS .= $entry[0] . ':' . $entry[1] . ',';
			}
			self::$treeWidthsJS = rtrim($_twJS, ',') . '}';
			$this->treeWidth = isset($_twArr[$this->module]) ? $_twArr[$this->module] : $this->treeDefaultWidth;
		} else {
			$this->treeWidth = $_tw;
		}
	}

	static function getJSToggleTreeCode($module){
		//FIXME: throw some of these functions out again and use generic version of main-window functions
		return we_html_element::jsElement('
var sizeTreeJsWidth=' . self::$treeWidthsJS . ';
var currentModule="' . $module . '";
') . we_html_element::jsScript(JS_DIR . 'modules_tree.js');
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
