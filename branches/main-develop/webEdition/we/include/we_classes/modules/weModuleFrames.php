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
 * @package    webEdition_modules
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weModuleFrames {

	var $module;
	var $db;
	var $frameset;
	var $View;
	var $Tree;
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;

	protected $treeDefaultWidth = 200;
	protected $treeWidth = 0;
	protected $treeHeaderHeight = 0;
	protected $treeFooterHeight = 40;
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

	function getHTMLDocument($body, $extraHead = ''){
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_tools::getHtmlInnerHead($this->module) . STYLESHEET . $extraHead
				) . $body
		);
	}

	function getJSCmdCode(){
		return we_html_element::jsElement('function we_cmd(){}');
	}

	function getHTML($what = ''){
		switch($what){
			case "frameset":
				print $this->getHTMLFrameset();
				break;
			case "iconbar":
				print $this->getHTMLIconbar();
				break;
			case "resize":
				print $this->getHTMLResize();
				break;
			case "left":
				print $this->getHTMLLeft();
				break;
			case "right":
				print $this->getHTMLRight();
				break;
			case "editor":
				print $this->getHTMLEditor();
				break;
			case "edheader":
				print $this->getHTMLEditorHeader();
				break;
			case "edbody":
				print $this->getHTMLEditorBody();
				break;
			case "edfooter":
				print $this->getHTMLEditorFooter();
				break;
			case "cmd":
				print $this->getHTMLCmd();
				break;
			case "treeheader":
				print $this->getHTMLTreeHeader();
				break;
			case "treefooter":
				print $this->getHTMLTreeFooter();
				break;
			case "search":
				print $this->getHTMLSearch();
				break;
			default:
				t_e(__FILE__ . " unknown reference: $what");
		}
	}

	function getHTMLFrameset($extraHead = '', $iconbar = false){
		$extraHead = $this->getJSCmdCode() .
			self::getJSToggleTreeCode($this->module, $this->treeDefaultWidth) .
			we_html_element::jsScript(JS_DIR . 'we_showMessage.js') . 
			we_main_headermenu::css() .
			$extraHead;

		$body = we_html_element::htmlBody(array('style' => 'background-color: gray; position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;', "onload" => "start();") ,
				we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlExIFrame('header', self::getHTMLHeader(WE_INCLUDES_PATH .'java_menu/modules/module_menu_' . $this->module . '.inc.php', $this->module), 'position: absolute; top: 0px; height: 32px; left: 0px; right: 0px;') .
					($iconbar ? we_html_element::htmlIFrame('iconbar', $this->frameset . '?pnt=iconbar', 'position: absolute; top: 32px; left: 0px; right: 0px; height: 40px; overflow: hidden;') : '') . 
					we_html_element::htmlIFrame('resize', $this->frameset . '?pnt=resize', 'position: absolute; top:' . ($iconbar ? 72 : 32) . 'px; bottom: 1px; left: 0px; right: 0px; overflow: hidden;') . 
					we_html_element::htmlIFrame('cmd', $this->frameset . '?pnt=cmd', 'position: absolute; bottom: 0px; height: 1px; left: 0px; right: 0px; overflow: hidden;')
				));

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLHeader($_menuFile, $_module){

		//Include the menu.
		include($_menuFile);
		include_once(WE_INCLUDES_PATH . "jsMessageConsole/messageConsole.inc.php" );

		$lang_arr = "we_menu_" . $_module;
		$jmenu = new weJavaMenu($$lang_arr, 'top.opener.top.load', '');

		$menu = $jmenu->getCode(false) . $jmenu->getJS();

		$table = new we_html_table(array("width" => "100%", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"), 1, 2);
		$table->setCol(0, 0, array("align" => "left", "valign" => "top"), $menu);
		$table->setCol(0, 1, array("align" => "right", "valign" => "top"), createMessageConsole("moduleFrame"));

		return we_html_element::htmlDiv(array('class' => 'menuDiv'), $table->getHtml());
	}

	function getHTMLResize($extraHead = '', $editorParams = ''){//TODO: only customer uses param sid: handle sid with edParams
		$this->setTreeWidthFromCookie();
		$extraHead = self::getJSToggleTreeCode($this->module, $this->treeDefaultWidth) . 
			$extraHead;

		$_incDecTree = '
			<img id="incBaum" src="' . BUTTONS_DIR . 'icons/function_plus.gif" width="9" height="12" style="position:absolute;bottom:53px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($this->treeWidth <= 30 ? 'bgcolor:grey;' : '') . '" onClick="top.content.resize.incTree();">
			<img id="decBaum" src="' . BUTTONS_DIR . 'icons/function_minus.gif" width="9" height="12" style="position:absolute;bottom:33px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($this->treeWidth <= 30 ? 'bgcolor:grey;' : '') . '" onClick="top.content.resize.decTree();">
			<img id="arrowImg" src="' . BUTTONS_DIR . 'icons/direction_' . ($this->treeWidth <= 30 ? 'right' : 'left') . '.gif" width="9" height="12" style="position:absolute;bottom:13px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;" onClick="top.content.resize.toggleTree();">
		';

		$body = we_html_element::htmlBody(array('style' => 'background-color:#bfbfbf;'), 
				we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;'),
					we_html_element::htmlDiv(array('id' => 'lframeDiv','style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;width: ' . $this->treeWidth . 'px;'),
						we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; width: ' . weTree::HiddenWidth . 'px; background-image: url(/webEdition/images/v-tabs/background.gif); background-repeat: repeat-y; border-top: 1px solid black;'), $_incDecTree) .
						we_html_element::htmlIFrame('left', $this->frameset . '?pnt=left', 'position: absolute; top: 0px; bottom: 0px; left: ' . weTree::HiddenWidth . 'px; right: 0px;')
					) .
					we_html_element::htmlIFrame('right', $this->frameset . '?pnt=right' . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : '') . $editorParams, 'position: absolute; top: 0px; bottom: 0px; left: ' . $this->treeWidth . 'px; right: 0px; width:auto; border-left: 1px solid black; overflow: hidden;')
			));

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLLeft($loadMainTree = true, $header = false, $footer = false, $footerId = 'treefooter'){//TODO: $loadMainTree entfaellt, sobald trees einheitlich sind
		$headerHeight = $this->getTreeHeaderHeigt();
		$footerHeight = $this->getTreeFooterHeigt();
		
		$body = we_html_element::htmlBody(array(), 
				($header ? we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; left: 0px; height: ' . $headerHeight . 'px; width: 100%; background-color: #00ff00;'), '') : 
					we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; left: 0px; height: 1px; width: 100%; background-color: #ffffff;'), '')) . 
				we_html_element::htmlIFrame('tree', ($loadMainTree ? WEBEDITION_DIR . 'treeMain.php' : HTML_DIR . 'white.html'), 'position: absolute; top: ' . ($header ? $headerHeight : 1) . 'px; bottom: ' . ($footer ? $footerHeight : 0) . 'px; left: 0px; width: 100%;') .
				($footer ? we_html_element::htmlIFrame($footerId, $this->frameset . '?pnt=' . $footerId, 'position: absolute; bottom: 0px; left: 0px; height: ' . $footerHeight . 'px; width: 100%;') : '')
			);

		return $this->getHTMLDocument($body);
	}

	protected function getTreeHeaderHeigt(){
		return $this->treeHeaderHeight;
	}
	
	protected function getTreeFooterHeigt(){
		return $this->treeFooterHeight;
	}

	function getHTMLRight($extraHead = '', $editorParams = ''){
		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$frameset->setAttributes(array("cols" => "*"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=editor" . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : '') . $editorParams, "name" => "editor", "noresize" => null, "scrolling" => "no"));
		$noframeset = new we_baseElement("noframes");
		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLEditor(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));

		$frameset->setAttributes(array("rows" => "40,*,40"));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edheader', 'name' => 'edheader', 'noresize' => null, 'scrolling' => 'no'));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edbody', 'name' => 'edbody', 'scrolling' => 'auto'));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edfooter', 'name' => 'edfooter', 'scrolling' => 'no'));

		// set and return html code
		$body = $frameset->getHtml();

		return $this->getHTMLDocument($body);
	}

	function getHTMLEditorHeader(){
		// to be overridden
	}

	function getHTMLEditorBody(){
		return $this->View->getProperties();
	}

	function getHTMLEditorFooter($btn_cmd){
		if(isset($_REQUEST['home'])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "EFf0EF"), ""));
		}
		
		$extraHead = we_html_element::jsElement(
			'function we_save() {
				top.content.we_cmd("' . $btn_cmd . '");
			}'
		);

		$table1 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "300"), 1, 1);
		$table1->setCol(0, 0, array("nowrap" => null, "valign" => "top"), we_html_tools::getPixel(1600, 10));

		$table2 = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0', 'width' => '300'), 1, 2);
		$table2->setRow(0, array('valign' => 'middle'));
		$table2->setCol(0, 0, array('nowrap' => null), we_html_tools::getPixel(5, 5));
		$table2->setCol(0, 1, array('nowrap' => null), we_button::create_button('save', 'javascript:we_save()'));
		
		$body = we_html_element::htmlBody(array('bgcolor' => 'white', 'background' => IMAGE_DIR . 'edit/editfooterback.gif', 'marginwidth' => '0', 'marginheight' => '0', 'leftmargin' => '0', 'topmargin' => '0'), $table1->getHtml() . $table2->getHtml());

		return $this->getHTMLDocument($body, $extraHead);

	}

	function getHTMLCmd(){
		// set and return html code
		$head = $this->Tree->getJSLoadTree();
		$body = we_html_element::htmlBody();

		return $this->getHTMLDocument($body, $head);
	}

	function getHTMLSearch(){
		// to be overridden
	}

	function getHTMLBox($content, $headline = "", $width = "100", $height = "50", $w = "25", $vh = "0", $ident = "0", $space = "5", $headline_align = "left", $content_align = "left"){
		$headline = str_replace(" ", "&nbsp;", $headline);
		if($ident){
			$pix1 = we_html_tools::getPixel($ident, $vh);
		}
		if($w){
			if(!$vh){
				$vh = 1;
			}
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
		$table->setCol(1, 1, array("valign" => "middle", "class" => "defaultgray", "align" => $headline_align), $headline);
		$table->setCol(1, 2, array(), $pix3);
		$table->setCol(1, 3, array("valign" => "middle", "align" => $content_align), $content);
		if($w && $headline != ""){
			$table->setCol(2, 1, array("valign" => "top"), $pix2);
		}
		return $table->getHtml();
	}

	function setTreeWidthFromCookie(){
		$_tw = isset($_COOKIE["treewidth_modules"]) ? $_COOKIE["treewidth_modules"] : $this->treeDefaultWidth;
		if(!is_numeric($_tw)){
			$_tw = explode(',', trim($_tw,' ,'));
			$_twArr = array();
			$_twJS = '{';

			foreach($_tw as $_v){
				$entry = explode(':', trim($_v));
				$_twArr[trim($entry[0])] = $entry[1];
				$_twJS .= $entry[0] . ':' . $entry[1] . ',';
			}
			self::$treeWidthsJS = rtrim($_twJS,',') . '}';
			$this->treeWidth = isset($_twArr[$this->module]) ? $_twArr[$this->module] : $this->treeDefaultWidth;
		} else{
			$this->treeWidth = $_tw;
		}
	}

	static function getJSToggleTreeCode($module,$treeDefaultWidth){
		//FIXME: throw some of these functions out again and use generic version of main-window functions

		return we_html_element::jsElement('
			var oldTreeWidth = ' . $treeDefaultWidth . ';

			function toggleTree(){
				var tDiv = self.document.getElementById("leftDiv");
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
					setTreeWidth('. weTree::HiddenWidth .');
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
				self.document.getElementById("rightDiv").style.left = w + "px";
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
				if(module == "users" || module == "messaging"){
					doc.cookie = "treewidth_" + module + "=" + escape(value) +
						((expires == null) ? "" : "; expires=" + expires.toGMTString()) +
						((path == null)    ? "" : "; path=" + path) +
						((domain == null)  ? "" : "; domain=" + domain);
				} else{
					moduleVals[module] = value;//console.log(moduleVals);
					var val = "";
					for(var param in moduleVals){
						val += val ? "," + param + ":" + moduleVals[param] : param + " : " + moduleVals[param];
					}
					doc.cookie = "treewidth_modules" + "=" + val +
						((expires == null) ? "" : "; expires=" + expires.toGMTString()) +
						((path == null)    ? "" : "; path=" + path) +
						((domain == null)  ? "" : "; domain=" + domain);
				}
			}
	');
	}

}
