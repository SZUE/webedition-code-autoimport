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

class we_modules_frame{

	var $module;
	var $db;
	var $frameset;
	var $View;
	var $Tree = null;
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;
	protected $useMainTree = true;
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

	public function setupTree($table, $topFrame, $treeFrame, $cmdFrame){
		$this->setFrames($topFrame, $treeFrame, $cmdFrame);
		$this->Tree->init($this->frameset, $topFrame, $treeFrame, $cmdFrame);
	}

	function getJSStart(){
		return ($this->Tree->initialized ? 'function start(){startTree();}' : 'function start(){}');
	}

	public function getHTMLDocumentHeader(){
		we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);
		return we_html_tools::getHtmlTop($this->module) . STYLESHEET;
	}

	function getHTMLDocument($body, $extraHead = ''){
		/*
		  return we_html_element::htmlDocType() . we_html_element::htmlHtml(
		  we_html_element::htmlHead(we_html_tools::getHtmlInnerHead($this->module) .
		  STYLESHEET . $extraHead) . $body
		  );
		 *
		 */
		//this is not nice, but it works for the moment...
		return STYLESHEET . $extraHead .
				we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
				we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
				we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js') .
				'</head>' . $body . '</html>';
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
			case 'treefooter':
				return $this->getHTMLTreeFooter();
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
				self::getJSToggleTreeCode($this->module, $this->treeDefaultWidth) .
				we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
				we_main_headermenu::css() .
				$extraHead;

		$body = we_html_element::htmlBody(array('style' => 'background-color: gray; position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;', "onload" => "start();"), we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
								, we_html_element::htmlExIFrame('header', self::getHTMLHeader(WE_INCLUDES_PATH . 'menu/module_menu_' . $this->module . '.inc.php', $this->module), 'position: absolute; top: 0px; height: 32px; left: 0px; right: 0px;') .
								($this->hasIconbar ? we_html_element::htmlIFrame('iconbar', $this->frameset . '?pnt=iconbar' . $extraUrlParams, 'position: absolute; top: 32px; left: 0px; right: 0px; height: 40px; overflow: hidden;') : '') .
								$this->getHTMLResize($extraUrlParams) .
								we_html_element::htmlIFrame('cmd', $this->frameset . '?pnt=cmd' . $extraUrlParams, 'position: absolute; bottom: 0px; height: 1px; left: 0px; right: 0px; overflow: hidden;')
		));

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLHeader($_menuFile, $_module){
		include($_menuFile);

		$lang_arr = "we_menu_" . $_module;
		$jmenu = new we_base_menu(${$lang_arr}, 'top.opener.top.load', '');

		$menu = $jmenu->getCode(false) . $jmenu->getJS();

		$table = new we_html_table(array("style" => "width:100%;background-color:#efefef;background-image: url(/webEdition/images/menu/background.gif); background-repeat: repeat-x;", "cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 2);
		$table->setCol(0, 0, array("align" => "left", "valign" => "top"), we_html_element::htmlDiv(array('class' => 'menuDiv'), $menu));
		$table->setCol(0, 1, array("align" => "right", "valign" => "top", 'style' => 'width:5em;'), we_main_headermenu::createMessageConsole('moduleFrame'));

		return $table->getHtml();
	}

	function getHTMLResize($extraUrlParams = ''){//TODO: only customer uses param sid: handle sid with extraUrlParams
		$_incDecTree = '<img id="incBaum" src="' . BUTTONS_DIR . 'icons/function_plus.gif" width="9" height="12" style="position:absolute;bottom:53px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($this->treeWidth <= 30 ? 'bgcolor:grey;' : '') . '" onclick="top.content.incTree();">
			<img id="decBaum" src="' . BUTTONS_DIR . 'icons/function_minus.gif" width="9" height="12" style="position:absolute;bottom:33px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($this->treeWidth <= 30 ? 'bgcolor:grey;' : '') . '" onclick="top.content.decTree();">
			<img id="arrowImg" src="' . BUTTONS_DIR . 'icons/direction_' . ($this->treeWidth <= 30 ? 'right' : 'left') . '.gif" width="9" height="12" style="position:absolute;bottom:13px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;" onclick="top.content.toggleTree();">
		';

		$content = we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;'), we_html_element::htmlDiv(array('id' => 'lframeDiv', 'style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;width: ' . $this->treeWidth . 'px;'), we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; width: ' . weTree::HiddenWidth . 'px; background-image: url(' . IMAGE_DIR . 'v-tabs/background.gif); background-repeat: repeat-y; border-top: 1px solid black;'), $_incDecTree) .
								$this->getHTMLLeft()
						) .
						we_html_element::htmlDiv(array('id' => 'right', 'style' => 'background: #F0EFF0; position: absolute; top: 0px; bottom: 0px; left: ' . $this->treeWidth . 'px; right: 0px; width: auto; border-left: 1px solid black; overflow: auto;'), we_html_element::htmlIFrame('editor', $this->frameset . '?pnt=editor' . $extraUrlParams, 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; overflow: hidden;')
						)
		);

		$attribs = array('id' => 'resize', 'name' => 'resize', 'style' => 'position: absolute; top:' . ($this->hasIconbar ? 72 : 32) . 'px; bottom: 1px; left: 0px; right: 0px; overflow: hidden;');

		return we_html_element::htmlDiv($attribs, $content);
	}

	function getHTMLLeft(){
		//we load tree in iFrame, because the complete tree JS is based on document.open() and document.write()
		//it makes not much sense, to rewrite trees before abandoning them anyway

		$attribs = array('id' => 'left', 'name' => 'left', 'style' => 'position: absolute; top: 0px; bottom: 0px; left: ' . weTree::HiddenWidth . 'px; right: 0px;');

		$content = we_html_element::htmlDiv(array('id' => 'treeheader', 'style' => 'overflow:hidden; position: absolute; top: 0px; left: 0px; height: ' . $this->treeHeaderHeight . 'px; width: 100%; ' . ($this->treeHeaderHeight != 1 ? 'background: url(' . IMAGE_DIR . 'backgrounds/header_with_black_line.gif); padding: 5px 0 0 0 ; ' : 'background: #ffffff')), $this->getHTMLTreeheader()) .
				($this->useMainTree ? $this->getHTMLTree() :
						we_html_element::htmlIFrame('tree', $this->frameset . '?pnt=tree', 'position: absolute; top: ' . $this->treeHeaderHeight . 'px; bottom: ' . $this->treeFooterHeight . 'px; left: 0px; width: 100%;')) .
				($this->treeFooterHeight == 0 ? '' : we_html_element::htmlDiv(array('id' => 'treefooter', 'style' => 'position: absolute; bottom: 0px; left: 0px; padding-left: 2px; height: ' . $this->treeFooterHeight . 'px; width: 100%; overflow:hidden; background: url(\'' . IMAGE_DIR . 'edit/editfooterback.gif\')'), $this->getHTMLTreefooter()));

		return we_html_element::htmlDiv($attribs, $content);
	}

	//TODO: we do not abandon the two tree types because trees will be re-implemented anyway
	protected function getHTMLTree(){
		if($this->useMainTree){
			$Tree = new weMainTree('webEdition.php', 'top', 'top', 'top.load'); //IMI: FOR MODULES WE NEED top.tree NOT top.left.tree!!!

			return we_html_element::htmlDiv(array(
						'id' => 'tree',
						'style' => 'overflow:scroll;position: absolute; top: ' . $this->treeHeaderHeight . 'px; bottom: ' . $this->treeFooterHeight . 'px; left: 0px; width: 100%; background: #F3F7FF',
						'link' => '#000000',
						'alink' => '#000000',
						'vlink' => '#000000',
						'marginwidth' => 0,
						'marginheight' => 4,
						'leftmargin' => 0,
						'topmargin' => 4), $Tree->getHTMLContructX('if(top.treeResized){top.treeResized();}')
			);
		}

		return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => '#F3F7FF')));
	}

	protected function getHTMLTreeheader(){
		return '';
		//to be overridden
	}

	protected function getHTMLTreefooter(){
		return '';
		//to be overridden
	}

	protected function getHTMLEditor($extraUrlParams = '', $extraHead = ''){
		$sid = we_base_request::_(we_base_request::STRING, 'sid');
		$body = we_html_element::htmlBody(array('style' => 'position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;'), we_html_element::htmlIFrame('edheader', $this->frameset . '?pnt=edheader' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, 'position: absolute; top: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;', 'width: 100%; overflow: hidden') .
						we_html_element::htmlIFrame('edbody', $this->frameset . '?pnt=edbody' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, 'position: absolute; top: 40px; bottom: 40px; left: 0px; right: 0px; overflow: auto;', 'border:0px;width:100%;height:100%;overflow: auto;') .
						we_html_element::htmlIFrame('edfooter', $this->frameset . '?pnt=edfooter' . ($sid !== false ? '&sid=' . $sid : '&home=1') . $extraUrlParams, 'position: absolute; bottom: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;', 'width: 100%; overflow: hidden')
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
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#EFf0EF"), ""));
		}

		$extraHead .= we_html_element::jsElement('
function we_save() {
	top.content.we_cmd("' . $btn_cmd . '");
}');

		$table2 = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0, 'style' => 'width:300px;margin-top:10px;'), 1, 2);
		$table2->setRow(0, array('valign' => 'middle'));
		$table2->setCol(0, 0, array('nowrap' => null), we_html_tools::getPixel(5, 5));
		$table2->setCol(0, 1, array('nowrap' => null), we_html_button::create_button('save', 'javascript:we_save()'));

		$body = we_html_element::htmlBody(array('bgcolor' => 'white', 'background' => IMAGE_DIR . 'edit/editfooterback.gif', 'marginwidth' => 0, 'marginheight' => 0, 'leftmargin' => 0, 'topmargin' => 0), $table2->getHtml());

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody(), $this->Tree->getJSLoadTree());
	}

	function getHTMLSearch(){
		// to be overridden
	}

	function getHTMLBox($content, $headline = "", $width = 100, $height = 50, $w = 25, $vh = 0, $ident = 0, $space = 5, $headline_align = "left", $content_align = "left"){
		if($ident){
			$pix1 = we_html_tools::getPixel($ident, $vh);
		}
		if($w){
			$vh = $vh? : 1;
			$pix2 = we_html_tools::getPixel($w, $vh);
		}

		$pix3 = we_html_tools::getPixel($space, 1);

		$table = new we_html_table(array("width" => $width, "height" => $height, "cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 4);

		if($ident){
			$table->setCol(0, 0, array("valign" => "top"), $pix1);
		}
		if($w){
			$table->setCol(0, 1, array("valign" => "top"), $pix2);
		}
		$table->setCol(1, 1, array("valign" => "middle", "class" => "defaultgray", "align" => $headline_align), str_replace(" ", "&nbsp;", $headline));
		$table->setCol(1, 2, array(), $pix3);
		$table->setCol(1, 3, array("valign" => "middle", "align" => $content_align), $content);
		if($w && $headline){
			$table->setCol(2, 1, array("valign" => "top"), $pix2);
		}
		return $table->getHtml();
	}

	protected function getHTMLExitQuestion(){
		if(($dc = we_base_request::_(we_base_request::RAW, 'delayCmd'))){
			$_frame = 'opener.' . $this->topFrame;
			$_yes = $_frame . '.hot=0;' . $_frame . '.we_cmd("module_' . $this->module . '_save");self.close();';
			$_no = $_frame . '.hot=0;' . $_frame . '.we_cmd("' . $dc . '","' . we_base_request::_(we_base_request::INT, 'delayParam') . '");self.close();';
			$_cancel = 'self.close();';

			return STYLESHEET .
					'</head>
			<body class="weEditorBody" onBlur="self.focus()" onload="self.focus()">' .
					we_html_tools::htmlYesNoCancelDialog(g_l('tools', '[exit_doc_question]'), IMAGE_DIR . "alert.gif", "ja", "nein", "abbrechen", $_yes, $_no, $_cancel) .
					'</body>
			</html>';
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

	static function getJSToggleTreeCode($module, $treeDefaultWidth){
		//FIXME: throw some of these functions out again and use generic version of main-window functions

		return we_html_element::jsElement('
var oldTreeWidth = ' . $treeDefaultWidth . ';

function toggleTree(){
	var tDiv = self.document.getElementById("left");
	var w = getTreeWidth();

	if(tDiv.style.display == "none"){
		oldTreeWidth = (oldTreeWidth < ' . weTree::MinWidthModules . ' ? ' . $treeDefaultWidth . ' : oldTreeWidth);
		setTreeWidth(oldTreeWidth);
		tDiv.style.display = "block";
		setTreeArrow("left");
		storeTreeWidth(oldTreeWidth);
	} else{
		tDiv.style.display = "none";
		oldTreeWidth = w;
		setTreeWidth(' . weTree::HiddenWidth . ');
		setTreeArrow("right");
	}
}

function setTreeArrow(direction) {
	try{
		self.document.getElementById("arrowImg").src = "' . BUTTONS_DIR . 'icons/direction_" + direction + ".gif";
		if(direction == "right"){
			self.document.getElementById("incBaum").style.backgroundColor = "gray";
			self.document.getElementById("decBaum").style.backgroundColor = "gray";
		}else{
			self.document.getElementById("incBaum").style.backgroundColor = "";
			self.document.getElementById("decBaum").style.backgroundColor = "";
		}
	} catch(e) {
		// Nothing
	}
}

function getTreeWidth() {
	var w = self.document.getElementById("lframeDiv").style.width;
	return w.substr(0, w.length-2);
}

function setTreeWidth(w) {
	self.document.getElementById("lframeDiv").style.width = w + "px";
	self.document.getElementById("right").style.left = w + "px";
	if(w > ' . weTree::HiddenWidth . '){
		storeTreeWidth(w);
	}
}

function storeTreeWidth(w) {
	var ablauf = new Date();
	var newTime = ablauf.getTime() + 30758400000;
	ablauf.setTime(newTime);
	weSetCookie("' . $module . '", w, ablauf, "/");
}

function incTree(){
	var w = parseInt(getTreeWidth());
	if((w > ' . weTree::MinWidthModules . ') && (w < ' . weTree::MaxWidthModules . ')){
		w += ' . weTree::StepWidth . ';
		setTreeWidth(w);
	}
	if(w >= ' . weTree::MaxWidthModules . '){
		w = ' . weTree::MaxWidthModules . ';
		self.document.getElementById("incBaum").style.backgroundColor = "grey";
	}
}

function decTree(){
	var w = parseInt(getTreeWidth());
	w -= ' . weTree::StepWidth . ';
	if(w > ' . weTree::MinWidthModules . '){
		setTreeWidth(w);
		self.document.getElementById("incBaum").style.backgroundColor = "";
	}
	if(w <= ' . weTree::MinWidthModules . ' && ((w + ' . weTree::StepWidth . ') >= ' . weTree::MinWidthModules . ')){
		toggleTree();
	}
}

function weSetCookie(module, value, expires, path, domain){
	var moduleVals = ' . self::$treeWidthsJS . ';
	var doc = self.document;
	moduleVals[module] = value;
	var val = "";
	for(var param in moduleVals){
		val += val ? "," + param + ":" + moduleVals[param] : param + " : " + moduleVals[param];
	}
	doc.cookie = "treewidth_modules" + "=" + val +
		((expires == null) ? "" : "; expires=" + expires.toGMTString()) +
		((path == null)    ? "" : "; path=" + path) +
		((domain == null)  ? "" : "; domain=" + domain);
}');
	}

	/* process vars & commands
	 */

	public function process(){
		$this->View->processVariables();
		$this->View->processCommands();
	}

}
