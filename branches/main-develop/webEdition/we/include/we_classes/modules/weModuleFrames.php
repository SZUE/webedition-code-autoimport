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
	var $Tree;
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;

	protected $treeDefaultWidth = 200;
	protected $treeWidth = 0;
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

	function getHTMLFrameset(){
		print $this->getJSCmdCode() .
			self::getJSToggleTreeCode($this->module, $this->treeDefaultWidth) .
			$this->Tree->getJSTreeCode() .
			we_html_element::jsElement($this->getJSStart()) .
			we_html_element::jsScript(JS_DIR . 'we_showMessage.js');

		print we_html_element::htmlBody(array('style' => 'background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;', "onload" => "start();")
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlExIFrame('header', self::getHTMLHeader(WE_INCLUDES_PATH .'java_menu/modules/module_menu_' . $this->module . '.inc.php', $this->module), 'position:absolute;top:0px;height:32px;left:0px;right:0px;') .
					we_html_element::htmlIFrame('resize', $this->frameset . '?pnt=resize', 'position:absolute;top:32px;bottom:1px;left:0px;right:0px;overflow: hidden;') .
					we_html_element::htmlIFrame('cmd', $this->frameset . '?pnt=cmd', 'position:absolute;bottom:0px;height:1px;left:0px;right:0px;overflow: hidden;')
				));
	}

	//TODO: this method is called statically and should therefore be declared static. for this, we must first make weToolFrames->getHtmlHeader static too
	//Btw: as soon as Apps got css-menues too, weToolFrames->getHtmlHeader is obsolete
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

		return we_main_headermenu::css() .
			we_html_element::htmlDiv(array('style' => 'background-color:#efefef;background-image: url(' . IMAGE_DIR . 'java_menu/background.gif); background-repeat:repeat;margin:0px;'), $table->getHtml());
	}

	function getHTMLResize(){
		$this->setTreeWidthFromCookie();
		print self::getJSToggleTreeCode($this->module, $this->treeDefaultWidth);

		$_incDecTree = '
			<img id="incBaum" src="' . BUTTONS_DIR . 'icons/function_plus.gif" width="9" height="12" style="position:absolute;bottom:53px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($this->treeWidth <= 30 ? 'bgcolor:grey;' : '') . '" onClick="top.content.resize.incTree();">
			<img id="decBaum" src="' . BUTTONS_DIR . 'icons/function_minus.gif" width="9" height="12" style="position:absolute;bottom:33px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($this->treeWidth <= 30 ? 'bgcolor:grey;' : '') . '" onClick="top.content.resize.decTree();">
			<img id="arrowImg" src="' . BUTTONS_DIR . 'icons/direction_' . ($this->treeWidth <= 30 ? 'right' : 'left') . '.gif" width="9" height="12" style="position:absolute;bottom:13px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;" onClick="top.content.resize.toggleTree();">
		';

		print we_html_element::htmlBody(array('style' => 'background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px'),
			we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;'),
				we_html_element::htmlDiv(array('id' => 'lframeDiv','style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;width: ' . $this->treeWidth . 'px;'),
					we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; width: ' . weTree::HiddenWidth . 'px; background-image: url(/webEdition/images/v-tabs/background.gif); background-repeat: repeat-y; border-top: 1px solid black;'), $_incDecTree) .
					we_html_element::htmlIFrame('left', $this->frameset . '?pnt=left', 'position: absolute; top: 0px; bottom: 0px; left: ' . weTree::HiddenWidth . 'px; right: 0px;')
				) .
				we_html_element::htmlIFrame('right', $this->frameset . '?pnt=right' . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), 'position: absolute; top: 0px; bottom: 0px; left: ' . $this->treeWidth . 'px; right: 0px; width:auto; border-left: 1px solid black; overflow: hidden;')
			)
		);
	}

	function getHTMLLeft(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => "1,*"));
		$frameset->addFrame(array("src" => HTML_DIR . "whiteWithTopLine.html", "name" => "treeheader", "noresize" => null, "scrolling" => "no"));
		$frameset->addFrame(array("src" => WEBEDITION_DIR . "treeMain.php", "name" => "tree", "noresize" => null, "scrolling" => "auto"));

		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLRight(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$frameset->setAttributes(array("cols" => "*"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=editor" . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "editor", "noresize" => null, "scrolling" => "no"));
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

	function getHTMLCmd(){
		// set and return html code
		$head = $this->Tree->getJSLoadTree();
		$body = we_html_element::htmlBody();

		return $this->getHTMLDocument($body, $head);
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
		$_tw = isset($_COOKIE["treewidth_modules"]) ? $_COOKIE["treewidth_modules"] : $this->treeDefaultWidth;t_e("def",$this->treeDefaultWidth);
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
		$leftDiv = $module == "users" ? "user_leftDiv" : ($module == "messaging" ? "messaging_treeDiv" : "leftDiv");
		$rightDiv = $module == "users" ? "user_rightDiv" : ($module == "messaging" ? "messaging_rightDiv" : "rightDiv");

		return we_html_element::jsElement('
			var oldTreeWidth = ' . $treeDefaultWidth . ';

			function toggleTree(){
				var tDiv = self.document.getElementById("' . $leftDiv . '");
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
				self.document.getElementById("' . $rightDiv . '").style.left = w + "px";
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
