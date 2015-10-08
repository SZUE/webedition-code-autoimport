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
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;
	protected $treeHeaderHeight = 1;
	protected $treeFooterHeight = 0;
	protected $treeDefaultWidth = 200;
	protected $treeWidth = 0;
	protected $hasIconbar = false;
	private static $treeWidthsJS = '{}';

	function __construct($frameset){
		$this->db = new DB_WE();
		$this->frameset = $frameset;
		$this->Tree = new weTree();
	}

	function setFrames($topFrame, $treeFrame, $cmdFrame){
		$this->topFrame = $topFrame;
		$this->treeFrame = $treeFrame;
		$this->cmdFrame = $cmdFrame;
	}

	function getJSStart(){
		return 'startTree();';
	}

	public function getHTMLDocumentHeader($charset = ''){
		$charset = ($charset? : $GLOBALS['WE_BACKENDCHARSET']);
		we_html_tools::headerCtCharset('text/html', $charset);
		return we_html_tools::getHtmlTop($this->module, $charset) . STYLESHEET;
	}

	function getHTMLDocument($body, $extraHead = ''){
		return $this->getHTMLDocumentHeader() .
			$extraHead .
			we_html_element::jsScript(LIB_DIR . 'additional/yui/yahoo-min.js') .
			we_html_element::jsScript(LIB_DIR . 'additional/yui/event-min.js') .
			we_html_element::jsScript(LIB_DIR . 'additional/yui/connection-min.js') .
			'</head>' . $body . '</html>';
	}

	static function getTree_g_l(){//FIXME:remove
	}

	function getJSCmdCode(){
		return we_html_element::jsElement('function we_cmd(){}');
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
			case 'treeheader':
				return $this->getHTMLTreeHeader();
			case 'search':
				return $this->getHTMLSearch();
			case 'exit_doc_question':
				return $this->getHTMLExitQuestion();
			default:
				t_e(__FILE__ . ' unknown reference: ' . $what);
				return '';
		}
	}

	function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		$this->setTreeWidthFromCookie();

		$extraHead = $this->getJSCmdCode() .
			self::getJSToggleTreeCode($this->module) .
			we_html_element::jsScript(JS_DIR . 'global.js') .
			we_main_headermenu::css() .
			$extraHead;

		$body = we_html_element::htmlBody(array('id' => 'weMainBody', "onload" => $this->getJSStart()), we_html_element::htmlExIFrame('header', self::getHTMLHeader(WE_INCLUDES_PATH . 'menu/module_menu_' . $this->module . '.inc.php', $this->module)) .
				($this->hasIconbar ? we_html_element::htmlIFrame('iconbar', $this->frameset . '?pnt=iconbar' . $extraUrlParams, 'position: absolute; top: 32px; left: 0px; right: 0px; height: 40px; overflow: hidden;', '', '', false) : '') .
				$this->getHTMLResize($extraUrlParams) .
				we_html_element::htmlIFrame('cmd', $this->frameset . '?pnt=cmd' . $extraUrlParams)
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLHeader($_menuFile, $_module){
		include($_menuFile);

		$lang_arr = "we_menu_" . $_module;
		$jmenu = new we_base_menu($$lang_arr, 'top.opener.top.load', '');

		$menu = $jmenu->getCode(false) . $jmenu->getJS();

		return
			we_html_element::htmlDiv(array('class' => 'menuDiv'), $menu) .
			we_html_element::htmlDiv(array('style' => 'width:5em;position: absolute;top: 0px;right: 0px;'), we_main_headermenu::createMessageConsole('moduleFrame'));
	}

	function getHTMLResize($extraUrlParams = ''){//TODO: only customer uses param sid: handle sid with extraUrlParams
		$_incDecTree = '<div id="baumArrows">
	<div class="baumArrow" id="incBaum" ' . ($this->treeWidth <= 30 ? 'style="background-color: grey"' : '') . ' onclick="top.content.incTree();"><i class="fa fa-plus"></i></div>
	<div class="baumArrow" id="decBaum" ' . ($this->treeWidth <= 30 ? 'style="background-color: grey"' : '') . ' onclick="top.content.decTree();"><i class="fa fa-minus"></i></div>
	<div class="baumArrow" onclick="top.content.toggleTree();"><i id="arrowImg" class="fa fa-lg fa-caret-' . ($this->treeWidth <= 30 ? "right" : "left") . '" ></i></div>
</div>
</div>';

		$content = we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;'), we_html_element::htmlDiv(array('id' => 'lframeDiv', 'style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;width: ' . $this->treeWidth . 'px;'), we_html_element::htmlDiv(array('style' => 'width: ' . (weTree::HiddenWidth - 1) . 'px;border-right:1px solid #767676;', 'id' => 'vtabs'), $_incDecTree) .
					$this->getHTMLLeft()
				) .
				we_html_element::htmlDiv(array('id' => 'right', 'style' => 'background-color: #F0EFF0; position: absolute; top: 0px; bottom: 0px; left: ' . $this->treeWidth . 'px; right: 0px; width: auto; border-left: 1px solid black; overflow: auto;'), we_html_element::htmlIFrame('editor', $this->frameset . '?pnt=editor' . $extraUrlParams, 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; overflow: hidden;', '', '', false)
				)
		);

		$attribs = array('id' => 'resize', 'name' => 'resize', 'class' => ($this->hasIconbar ? 'withIconBar' : ''), 'style' => 'overflow:hidden');

		return we_html_element::htmlDiv($attribs, $content);
	}

	function getHTMLLeft(){
		//we load tree in iFrame, because the complete tree JS is based on document.open() and document.write()
		//it makes not much sense, to rewrite trees before abandoning them anyway
		return we_html_element::htmlDiv(array(
				'id' => 'left', 'name' => 'left', 'style' => 'position: absolute; top: 0px; bottom: 0px; left: ' . weTree::HiddenWidth . 'px; right: 0px;'
				), we_html_element::htmlDiv(array(
					'id' => 'treeheader', 'style' => 'overflow:hidden; position: absolute; top: 0px; left: 0px; height: ' . ($this->treeHeaderHeight > 1 ? $this->treeHeaderHeight - 6/* padding+border */ : 1) . 'px; width: 100%; ' . ($this->treeHeaderHeight != 1 ? 'padding: 5px 0px 0px 0px; ' : 'background: #ffffff')
					), $this->getHTMLTreeheader()) .
				$this->getHTMLTree() .
				($this->treeFooterHeight == 0 ? '' : we_html_element::htmlDiv(array(
						'id' => 'treefooter', 'class' => 'editfooter', 'style' => 'position: absolute; bottom: 0px; left: 0px; padding-left: 2px; height: ' . $this->treeFooterHeight . 'px; width: 100%; overflow:hidden;'
						), $this->getHTMLTreefooter())
				)
		);
	}

	protected function getHTMLTree($extraHead = ''){
		return we_html_element::htmlDiv(array(
				'id' => 'tree',
				'style' => 'overflow:scroll;position: absolute; top: ' . $this->treeHeaderHeight . 'px; bottom: ' . $this->treeFooterHeight . 'px; left: 0px; width: 100%; background: #F3F7FF',
				'link' => '#000000',
				'alink' => '#000000',
				'vlink' => '#000000',
				'marginwidth' => 0,
				'marginheight' => 4,
				'leftmargin' => 0,
				'topmargin' => 4), $extraHead . $this->Tree->getHTMLContruct('if(top.treeResized){top.treeResized();}')
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
		$body = we_html_element::htmlBody(array('style' => 'position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;'), we_html_element::htmlIFrame('edheader', $this->frameset . '?pnt=edheader' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, 'position: absolute; top: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;', 'width: 100%; overflow: hidden', '', '', false) .
				we_html_element::htmlIFrame('edbody', $this->frameset . '?pnt=edbody' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, 'position: absolute; top: 40px; bottom: 40px; left: 0px; right: 0px;', 'border:0px;width:100%;height:100%;') .
				we_html_element::htmlIFrame('edfooter', $this->frameset . '?pnt=edfooter' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, 'position: absolute; bottom: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;', 'width: 100%; overflow: hidden', '', '', false)
		);

		return $this->getHTMLDocument($body, $extraHead);
	}

	protected function getHTMLEditorHeader(){
		// to be overridden
	}

	protected function getHTMLEditorBody(){
		return $this->View->getProperties();
	}

	protected function getHTMLEditorFooter($btn_cmd, $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("style" => "background-color:#EFf0EF"), ""));
		}

		$extraHead .= we_html_element::jsElement('
function we_save() {
	top.content.we_cmd("' . $btn_cmd . '");
}');

		$table2 = new we_html_table(array('class' => 'default', 'style' => 'width:300px;'), 1, 2);
		$table2->setRow(0, array('style' => 'vertical-align:middle'));
		$table2->setCol(0, 1, array('nowrap' => null), we_html_button::create_button(we_html_button::SAVE, 'javascript:we_save()'));

		return $this->getHTMLDocument(we_html_element::htmlBody(array('id' => 'footerBody'), $table2->getHtml()), $extraHead);
	}

	function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody(), $this->Tree->getJSLoadTree());
	}

	function getHTMLSearch(){
		// to be overridden
	}

	function getHTMLBox($content, $headline = "", $width = 100, $height = 50, $w = 25, $vh = 0, $ident = 0, $space = 5, $headline_align = "left", $content_align = "left"){
		$table = new we_html_table(array("width" => $width, "height" => $height, "class" => 'default', 'style' => 'margin-left:' . intval($ident) . 'px;margin-top:' . intval($vh) . 'px;margin-bottom:' . ($w && $headline ? $vh : 0) . 'px;'), 1, 2);

		$table->setCol(0, 0, array("style" => 'vertical-align:middle;text-align:' . $headline_align . ';padding-right:' . $space . 'px;', "class" => "defaultgray"), str_replace(" ", "&nbsp;", $headline));
		$table->setCol(0, 1, array("style" => 'vertical-align:middle;text-align:' . $content_align), $content);
		return $table->getHtml();
	}

	protected function getHTMLExitQuestion(){
		if(($dc = we_base_request::_(we_base_request::RAW, 'delayCmd'))){
			$_frame = 'opener.' . $this->topFrame;
			$_yes = $_frame . '.hot=0;' . $_frame . '.we_cmd("module_' . $this->module . '_save");self.close();';
			$_no = $_frame . '.hot=0;' . $_frame . '.we_cmd("' . $dc . '","' . we_base_request::_(we_base_request::INT, 'delayParam') . '");self.close();';
			$_cancel = 'self.close();';

			return we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET, '<body class="weEditorBody" onBlur="self.focus()" onload="self.focus()">' .
					we_html_tools::htmlYesNoCancelDialog(g_l('tools', '[exit_doc_question]'), IMAGE_DIR . "alert.gif", "ja", "nein", "abbrechen", $_yes, $_no, $_cancel) .
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
		$this->View->processVariables();
		$this->View->processCommands();
	}

}
