
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
//TODO in modulesFrames: set module-settings as class properties instead of looping them through as method parameters!

class weModuleFrames{

	var $module;
	var $db;
	var $frameset;
	var $View;
	var $Tree;
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
			case "tree":
				print $this->getHTMLTree();
			case "treeheader":
				print $this->getHTMLTreeHeader();
				break;
			case "treefooter":
				print $this->getHTMLTreeFooter();
				break;
			case "search":
				print $this->getHTMLSearch();
				break;
			case 'exit_doc_question':
				print $this->getHTMLExitQuestion();
				break;
			default:
				t_e(__FILE__ . " unknown reference: $what");
		}
	}

	function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		$extraHead = $this->getJSCmdCode() .
			self::getJSToggleTreeCode($this->module, $this->treeDefaultWidth) .
			we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
			we_main_headermenu::css() .
			$extraHead;
		
		//extraHead extracted from ex-resizeFrame
		$this->setTreeWidthFromCookie();
		$extraHead .= self::getJSToggleTreeCode($this->module, $this->treeDefaultWidth) . 
			$extraHead;

		$body = we_html_element::htmlBody(array('style' => 'background-color: gray; position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;', "onload" => "start();") ,
				we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlExIFrame('header', self::getHTMLHeader(WE_INCLUDES_PATH .'java_menu/modules/module_menu_' . $this->module . '.inc.php', $this->module), 'position: absolute; top: 0px; height: 32px; left: 0px; right: 0px;') .
					($this->hasIconbar ? we_html_element::htmlIFrame('iconbar', $this->frameset . '?pnt=iconbar' . $extraUrlParams, 'position: absolute; top: 32px; left: 0px; right: 0px; height: 40px; overflow: hidden;') : '') . 
					$this->getHTMLResizeDiv($extraUrlParams) .
					we_html_element::htmlIFrame('cmd', $this->frameset . '?pnt=cmd' . $extraUrlParams, 'position: absolute; bottom: 0px; height: 1px; left: 0px; right: 0px; overflow: hidden;')
		));

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLHeader($_menuFile, $_module){
		include($_menuFile);
		require_once(WE_INCLUDES_PATH . "jsMessageConsole/messageConsole.inc.php" );

		$lang_arr = "we_menu_" . $_module;
		$jmenu = new weJavaMenu($$lang_arr, 'top.opener.top.load', '');

		$menu = $jmenu->getCode(false) . $jmenu->getJS();

		$table = new we_html_table(array("width" => "100%", "cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 2);
		$table->setCol(0, 0, array("align" => "left", "valign" => "top"), $menu);
		$table->setCol(0, 1, array("align" => "right", "valign" => "top"), createMessageConsole("moduleFrame"));

		return we_html_element::htmlDiv(array('class' => 'menuDiv'), $table->getHtml());
	}

	function getHTMLResizeDiv($extraUrlParams = ''){//TODO: only customer uses param sid: handle sid with edParams
		$_incDecTree = '
			<img id="incBaum" src="' . BUTTONS_DIR . 'icons/function_plus.gif" width="9" height="12" style="position:absolute;bottom:53px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($this->treeWidth <= 30 ? 'bgcolor:grey;' : '') . '" onClick="top.content.incTree();">
			<img id="decBaum" src="' . BUTTONS_DIR . 'icons/function_minus.gif" width="9" height="12" style="position:absolute;bottom:33px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer; ' . ($this->treeWidth <= 30 ? 'bgcolor:grey;' : '') . '" onClick="top.content.decTree();">
			<img id="arrowImg" src="' . BUTTONS_DIR . 'icons/direction_' . ($this->treeWidth <= 30 ? 'right' : 'left') . '.gif" width="9" height="12" style="position:absolute;bottom:13px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;" onClick="top.content.toggleTree();">
		';

		$content = we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;'),
					we_html_element::htmlDiv(array('id' => 'lframeDiv','style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;width: ' . $this->treeWidth . 'px;'),
						we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; width: ' . weTree::HiddenWidth . 'px; background-image: url(/webEdition/images/v-tabs/background.gif); background-repeat: repeat-y; border-top: 1px solid black;'), $_incDecTree) .
						$this->getHTMLLeftDiv()
					) .
					we_html_element::htmlDiv(array('id' => 'right', 'style' => 'background: #F0EFF0; position: absolute; top: 0px; bottom: 0px; left: ' . $this->treeWidth . 'px; right: 0px; width: auto; border-left: 1px solid black; overflow: auto;'), 
						we_html_element::htmlIFrame('editor', $this->frameset . '?pnt=editor' . $extraUrlParams, 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px; overflow: hidden;')
					)
			);

		$attribs = array('id' => 'resize', 'name' => 'resize', 'style' => 'position: absolute; top:' . ($this->hasIconbar ? 72 : 32) . 'px; bottom: 1px; left: 0px; right: 0px; overflow: hidden;');

		return we_html_element::htmlDiv($attribs, $content);
	}

	function getHTMLLeftDiv($footer = false){//TODO: $loadMainTree entfaellt, sobald trees einheitlich sind
		//we load tree in iFrame, because the complete tree JS is based on document.open() and document.write()
		//it makes not much sense, to rewrite trees before abandoning them anyway

		$attribs = array('id' => 'left', 'name' => 'left', 'style' => 'position: absolute; top: 0px; bottom: 0px; left: ' . weTree::HiddenWidth . 'px; right: 0px;');

		$content = we_html_element::htmlDiv(array('id' => 'treeheader', 'style' => 'overflow:hidden; position: absolute; top: 0px; left: 0px; height: ' . $this->treeHeaderHeight . 'px; width: 100%; ' . ($this->treeHeaderHeight != 1 ? 'background: url(' . IMAGE_DIR . 'backgrounds/header_with_black_line.gif); padding: 5px 0 0 0 ; ' : 'background: #ffffff')), $this->getHTMLTreeheader()) . 
			we_html_element::htmlIFrame('tree', ($this->useMainTree ? WEBEDITION_DIR . 'treeMainModules.php' : $this->frameset . '?pnt=tree'), 'position: absolute; top: ' . $this->treeHeaderHeight . 'px; bottom: ' . $this->treeFooterHeight . 'px; left: 0px; width: 100%;') .
			($this->treeFooterHeight == 0 ? '' : we_html_element::htmlDiv(array('id' => 'treefooter', 'style' => 'position: absolute; bottom: 0px; left: 0px; padding-left: 2px; height: ' . $this->treeFooterHeight . 'px; width: 100%; overflow:hidden; background: url(\'' . IMAGE_DIR . 'edit/editfooterback.gif\')'), $this->getHTMLTreefooter()));

		return we_html_element::htmlDiv($attribs, $content);
	}

	function getHTMLTree(){
		if($this->useMainTree){
			if(isset($_REQUEST['code'])){
				//return('REQUEST[\'code\'] is forbidden!');
			}
			require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

			$Tree = new weMainTree('webEdition.php', 'top', 'top.tree', 'top.load');//IMI: FOR MODULES WE NEED top.tree NOT top.left.tree!!!

			print $Tree->getHTMLContruct('if(top.treeResized){top.treeResized();}');
		}

		return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => '#F3F7FF')));
	}

	function getHTMLTreeheader(){
		return ''; 
		//to be overridden
	}

	function getHTMLTreefooter(){
		return '';
		//to be overridden
	}

	protected function getTreeHeaderHeigt(){
		return $this->treeHeaderHeight;
	}

	protected function getTreeFooterHeigt(){
		return $this->treeFooterHeight;
	}

	function getHTMLEditor($extraUrlParams = ''){

		$frameset = new we_html_frameset(array("framespacing" => 0, "border" => 0, "frameborder" => "no"));

		$frameset->setAttributes(array("rows" => "40,*,40"));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edheader' . $extraUrlParams, 'name' => 'edheader', 'noresize' => null, 'scrolling' => 'no'));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edbody' . $extraUrlParams, 'name' => 'edbody', 'scrolling' => 'auto'));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edfooter' . $extraUrlParams, 'name' => 'edfooter', 'scrolling' => 'no'));

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

	function getHTMLEditorFooter($btn_cmd, $extraHead = ''){
		if(isset($_REQUEST['home'])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "EFf0EF"), ""));
		}

		$extraHead .= we_html_element::jsElement('

			function we_save() {
				top.content.we_cmd("' . $btn_cmd . '");
			}'
		);

		$table1 = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "width" => 300), 1, 1);
		$table1->setCol(0, 0, array("nowrap" => null, "valign" => "top"), we_html_tools::getPixel(1600, 10));

		$table2 = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0, 'width' => 300), 1, 2);
		$table2->setRow(0, array('valign' => 'middle'));
		$table2->setCol(0, 0, array('nowrap' => null), we_html_tools::getPixel(5, 5));
		$table2->setCol(0, 1, array('nowrap' => null), we_button::create_button('save', 'javascript:we_save()'));

		$body = we_html_element::htmlBody(array('bgcolor' => 'white', 'background' => IMAGE_DIR . 'edit/editfooterback.gif', 'marginwidth' => 0, 'marginheight' => 0, 'leftmargin' => 0, 'topmargin' => 0), $table1->getHtml() . $table2->getHtml());

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

	function getHTMLBox($content, $headline = "", $width = 100, $height = 50, $w = 25, $vh = 0, $ident = 0, $space = 5, $headline_align = "left", $content_align = "left"){
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

	function getHTMLExitQuestion(){
		if(isset($_REQUEST['delayCmd']) && isset($_REQUEST['delayParam'])){
			$_frame = 'opener.' . $this->topFrame;
			$_form = $_frame . '.document.we_form';
			$_yes = $_frame . '.hot=0;' . $_frame . '.we_cmd("module_' . $this->module . '_save");self.close();';
			$_no = $_frame . '.hot=0;' . $_frame . '.we_cmd("' . $_REQUEST['delayCmd'] . '","' . $_REQUEST['delayParam'] . '");self.close();';
			$_cancel = 'self.close();';

			return we_html_tools::getHtmlTop() .
				STYLESHEET .
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
		} else{
			$this->treeWidth = $_tw;
		}
	}

	static function getJSToggleTreeCode($module, $treeDefaultWidth){
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