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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weToolFrames extends weModuleFrames{

	var $View;
	var $Table;
	var $TreeSource = 'table:';
	var $toolName;
	var $toolClassName;
	var $toolDir;
	var $toolUrl;
	var $_space_size = 120;
	var $_text_size = 75;
	var $_width_size = 520;
	var $_def_box_style = 'float:left;';
	var $_margin_bottom = '5px';
	var $_margin_top = '5px';
	var $_margin_left = '30';
	var $Model;

	function getHTML($what){
		switch($what){
			case 'frameset':
				print $this->getHTMLFrameset();
				break;
			case 'header':
				print $this->getHTMLHeader();
				break;
			case 'resize':
				print $this->getHTMLResize();
				break;
			case 'left':
				print $this->getHTMLLeft();
				break;
			case 'right':
				print $this->getHTMLRight();
				break;
			case 'editor':
				print $this->getHTMLEditor();
				break;
			case 'edheader':
				print $this->getHTMLEditorHeader();
				break;
			case 'edbody':
				print $this->getHTMLEditorBody();
				break;
			case 'edfooter':
				print $this->getHTMLEditorFooter();
				break;
			case 'cmd':
				print $this->getHTMLCmd();
				break;
			case 'treeheader':
				print $this->getHTMLTreeHeader();
				break;
			case 'treefooter':
				print $this->getHTMLTreeFooter();
				break;
			case 'treeconst':
				print $this->Tree->getHTMLContruct();
				break;
			case 'exit_doc_question':
				print $this->getHTMLExitQuestion();
				break;
			default:
				t_e(__FILE__ . " unknown reference: $what");
		}
	}

	function getHTMLFrameset(){

		$this->Model->clearSessionVars();

		if(isset($_REQUEST['modelid'])){
			$_class = weToolLookup::getModelClassName($this->toolName);
			$this->Model = new $_class();
			$this->Model->load($_REQUEST['modelid']);
			$this->Model->saveInSession();
			$_SESSION[$this->toolName]["modelidForTree"] = $_REQUEST['modelid'];
		}


		$js = $this->getJSCmdCode();
		$js.=$this->Tree->getJSTreeCode();
		$js.=we_html_element::jsElement($this->getJSStart());
		$js.=we_html_element::jsScript(JS_DIR . 'we_showMessage.js');

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => ((isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0) ? "32,*,100" : "32,*,0" ), "onLoad" => "start();"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=header", "name" => "header", "scrolling" => "no", "noresize" => null));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=resize" . (isset($_REQUEST['tab']) ? '&tab=' . $_REQUEST['tab'] : '') . (isset($_REQUEST['modelid']) ? '&modelid=' . $_REQUEST['modelid'] : '') . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "resize", "scrolling" => "no"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=cmd" . (isset($_REQUEST['modelid']) ? '&modelid=' . $_REQUEST['modelid'] : ''), "name" => "cmd", "scrolling" => "no", "noresize" => null));

		// set and return html code
		$head = $js;
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body, $head);
	}

	function getHTMLResize(){

		if((we_base_browserDetect::isGecko()) || (we_base_browserDetect::isOpera())){
			$frameset = new we_html_frameset(array("cols" => "200,*", "border" => "1", "id" => "resizeframeid"));
		} else{
			$frameset = new we_html_frameset(array("cols" => "200,*", "border" => "0", "frameborder" => "0", "framespacing" => "0", "id" => "resizeframeid"));
		}
		if(we_base_browserDetect::isIE()){
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=left" . (isset($_REQUEST['modelid']) ? '&modelid=' . $_REQUEST['modelid'] : ''), "name" => "left", "scrolling" => "no", "frameborder" => "no"));
		} else{
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=left" . (isset($_REQUEST['modelid']) ? '&modelid=' . $_REQUEST['modelid'] : ''), "name" => "left", "scrolling" => "no"));
		}
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=right" . (isset($_REQUEST['tab']) ? '&tab=' . $_REQUEST['tab'] : '') . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "right"));

		$noframeset = new we_baseElement("noframes");

		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLRight(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		if((we_base_browserDetect::isGecko()) || we_base_browserDetect::isOpera()){
			$frameset->setAttributes(array("cols" => "*"));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=editor" . (isset($_REQUEST['tab']) ? '&tab=' . $_REQUEST['tab'] : '') . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "editor", "noresize" => null, "scrolling" => "no"));
		} else if(we_base_browserDetect::isSafari()){
			$frameset->setAttributes(array("cols" => "1,*"));
			$frameset->addFrame(array("src" => HTML_DIR . "safariResize.html", "name" => "separator", "noresize" => null, "scrolling" => "no"));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=editor" . (isset($_REQUEST['tab']) ? '&tab=' . $_REQUEST['tab'] : '') . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "editor", "noresize" => null, "scrolling" => "no"));
		} else{
			$frameset->setAttributes(array("cols" => "2,*"));
			$frameset->addFrame(array("src" => HTML_DIR . "ieResize.html", "name" => "separator", "noresize" => null, "scrolling" => "no"));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=editor" . (isset($_REQUEST['tab']) ? '&tab=' . $_REQUEST['tab'] : '') . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "editor", "noresize" => null, "scrolling" => "no"));
		}
		$noframeset = new we_baseElement("noframes");
		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLEditor(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => "40,*,40"));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . (isset($_REQUEST['tab']) ? '&tab=' . $_REQUEST['tab'] : '') . '&pnt=edheader', 'name' => 'edheader', 'noresize' => null, 'scrolling' => 'no'));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . (isset($_REQUEST['tab']) ? '&tab=' . $_REQUEST['tab'] : '') . '&pnt=edbody', 'name' => 'edbody', 'scrolling' => 'auto'));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edfooter', 'name' => 'edfooter', 'scrolling' => 'no'));

		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getJSCmdCode(){
		return $this->View->getJSTop() .
			we_html_element::jsElement($this->Tree->getJSMakeNewEntry()
		);
	}

	/**
	 * Top frame with menu
	 *
	 * @return string
	 */
	function getHTMLHeader(){
		//	Include the menu.
		include($this->toolDir . 'conf/we_menu_' . $this->toolName . '.conf.php');
		include_once(WE_INCLUDES_PATH . "jsMessageConsole/messageConsole.inc.php" );

		$lang_arr = 'we_menu_' . $this->toolName;
		$jmenu = new weJavaMenu($$lang_arr, $this->topFrame . '.cmd', 350, 30);

		$menu = '';
		ob_start();
		$jmenu->printMenu('cmd');
		$menu = ob_get_contents();
		ob_end_clean();

		$table = new we_html_table(array("width" => "100%", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"), 1, 2);
		$table->setCol(0, 0, array("align" => "left", "valign" => "top"), $menu);
		$table->setCol(0, 1, array("align" => "right", "valign" => "top"), createMessageConsole("toolFrame"));

		$body = we_html_element::htmlBody(array('style' => 'background-color:#efefef;background-image: url(' . IMAGE_DIR . 'java_menu/background.gif); background-repeat:repeat;margin:0px;'), $table->getHtml()
		);

		return $this->getHTMLDocument($body);
	}

	/**
	 * Frame for tubs
	 *
	 * @return string
	 */
	function getHTMLEditorHeader(){
		if(isset($_REQUEST['home'])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => '#F0EFF0'), ''));
		}



		$we_tabs = new we_tabs();

		$we_tabs->addTab(new we_tab('#', g_l('tools', '[properties]'), '((' . $this->topFrame . '.activ_tab==1) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('1');", array("id" => "tab_1")));

		$we_tabs->onResize();
		$tabsHead = $we_tabs->getHeader();
		$tabsBody = $we_tabs->getJS();

		$js = '';


		$js.=we_html_element::jsElement('

				function mark() {
					var elem = document.getElementById("mark");
					elem.style.display = "inline";

				}

				function unmark() {
					var elem = document.getElementById("mark");
					elem.style.display = "none";
				}

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
				}

				' . ($this->Model->ID ? '' : $this->topFrame . '.activ_tab=1;') . '
		');

		$tabsHead .= $js;

		$table = new we_html_table(array("width" => "3000", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"), 3, 1);

		$table->setCol(0, 0, array(), we_html_tools::getPixel(1, 3));

		$table->setCol(1, 0, array("valign" => "top", "class" => "small"), we_html_tools::getPixel(15, 2) .
			we_html_element::htmlB(
				($this->Model->IsFolder ? g_l('tools', '[group]') : g_l('tools', '[entry]')) . ':&nbsp;' . str_replace('&amp;', '&', $this->Model->Text) . '<div id="mark" style="display: none;">*</div>' .
				we_html_tools::getPixel(1600, 19)
			)
		);

		$extraJS = 'document.getElementById("tab_"+' . $this->topFrame . '.activ_tab).className="tabActive";';
		$body = we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0", "onload" => "setFrameSize()", "onresize" => "setFrameSize()"), '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;" id="headrow">&nbsp;' . we_html_element::htmlB(($this->Model->IsFolder ? g_l('tools', '[group]') : g_l('tools', '[entry]')) . ':&nbsp;' . str_replace('&amp;', '&', $this->Model->Text) . '<div id="mark" style="display: none;">*</div>') . '</div>' . we_html_tools::getPixel(100, 3) .
				$we_tabs->getHTML() .
				'</div>' . we_html_element::jsElement($extraJS)
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	function getHTMLEditorBody(){

		$hiddens = array('cmd' => 'tool_' . $this->toolName . '_edit', 'pnt' => 'edbody', 'vernr' => (isset($_REQUEST['vernr']) ? $_REQUEST['vernr'] : 0));

		if(isset($_REQUEST["home"]) && $_REQUEST["home"]){
			$hiddens['cmd'] = 'home';
			$GLOBALS['we_print_not_htmltop'] = true;
			$GLOBALS['we_head_insert'] = $this->View->getJSProperty();
			$GLOBALS['we_body_insert'] = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->getCommonHiddens($hiddens) . we_html_element::htmlHidden(array('name' => 'home', 'value' => '0'))
			);
			$tool = $GLOBALS['tool'] = $this->toolName;
			ob_start();
			include($this->toolDir . 'home.inc.php');
			$out = ob_get_contents();
			ob_end_clean();
			return
				we_html_element::jsElement('
								' . $this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&home=1";
								' . $this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter&home=1";
			') . $out;
		}

		$body = we_html_element::htmlBody(array("class" => "weEditorBody", 'onLoad' => 'loaded=1;'), we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js?' . WE_VERSION) .
				we_html_element::htmlForm(array('name' => 'we_form', 'onsubmit' => 'return false'), $this->getHTMLProperties()
				)
		);

		return $this->getHTMLDocument($body, STYLESHEET . $this->View->getJSProperty());
	}

	function getHTMLEditorFooter(){

		if(isset($_REQUEST["home"])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#F0EFF0"), ""));
		}

		$table1 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "3000"), 1, 1);
		$table1->setCol(0, 0, array("nowrap" => null, "valign" => "top"), we_html_tools::getPixel(1600, 10));


		$_but_table = we_button::create_button_table(array(
				we_button::create_button("save", "javascript:we_save();", true, 100, 22, '', '', (!we_hasPerm('EDIT_NAVIGATION')))
				), 10, array('style' => 'margin-left: 15px')
		);

		return $this->getHTMLDocument(
				we_html_element::jsScript(JS_DIR . "attachKeyListener.js")
				.
				we_html_element::jsElement('
					function we_save() {
						' . $this->topFrame . '.we_cmd("tool_' . $this->toolName . '_save");
					}
					')
				.
				we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "edit/editfooterback.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0"), we_html_element::htmlForm(array(), $table1->getHtml() .
						$_but_table)
				)
		);
	}

	function getPercent($total, $value, $precision = 0){

		if($total){
			$result = round(($value * 100) / $total, $precision);
		} else{
			$result = 0;
		}

		return we_util_Strings::formatNumber($result, strtolower($GLOBALS['WE_LANGUAGE']), 2);
	}

	function getHTMLPropertiesItem(){
		$parts = array();


		return $parts;
	}

	function getHTMLPropertiesGroup(){
		$parts = array();


		return $parts;
	}

	function getHTMLGeneral(){
		$parts = array();

		array_push($parts, array(
			'headline' => g_l('tools', '[general]'),
			'html' => we_html_element::htmlHidden(array('name' => 'newone', 'value' => ($this->Model->ID == 0 ? 1 : 0))) .
			we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Text', '', $this->Model->Text, '', 'style="width: ' . $this->_width_size . '" onChange="' . $this->topFrame . '.mark();"'), g_l('tools', '[name]')) .
			$this->getHTMLChooser(g_l('tools', '[group]'), $this->Table, 0, 'ParentID', $this->Model->ParentID, 'ParentPath', 'opener.' . $this->topFrame . '.mark()', ''),
			'space' => $this->_space_size,
			'noline' => 1
			)
		);

		return $parts;
	}

	function getHTMLProperties($preselect = ''){
		$tabNr = isset($_REQUEST['tabnr']) ? ($_REQUEST['tabnr']) : 1;

		$out = '';

		$hiddens = array('cmd' => '',
			'pnt' => 'edbody',
			'tabnr' => $tabNr,
			'vernr' => (isset($_REQUEST['vernr']) ? $_REQUEST['vernr'] : 0),
			'delayParam' => (isset($_REQUEST['delayParam']) ? $_REQUEST['delayParam'] : '')
		);

		$out = $this->View->getCommonHiddens($hiddens) .
			we_multiIconBox::getHTML('', '100%', $this->getHTMLGeneral(), 30);

		return $out;
	}

	function getHTMLLeft(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => "1,*,40"));
		$frameset->addFrame(array("src" => HTML_DIR . "white.html", "name" => "treeheader", "noresize" => null, "scrolling" => "no"));

		$frameset->addFrame(array("src" => $this->frameset . "?pnt=treeconst" . (isset($_REQUEST['modelid']) ? '&modelid=' . $_REQUEST['modelid'] : ''), "name" => "tree", "noresize" => null, "scrolling" => "auto"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=treefooter", "name" => "treefooter", "noresize" => null, "scrolling" => "no"));

		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLTreeHeader(){
		return "";
	}

	function getHTMLTreeFooter(){

		$body = we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "edit/editfooterback.gif", "marginwidth" => "5", "marginheight" => "0", "leftmargin" => "5", "topmargin" => "0"), '<div id="infoField" style="margin:5px; display: none;" class="defaultfont"></div>'
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLCmd(){
		$out = "";

		if(isset($_REQUEST["pid"])){
			$pid = $_REQUEST["pid"];
		}
		else
			exit;

		if(isset($_REQUEST["offset"])){
			$offset = $_REQUEST["offset"];
		}
		else
			$offset = 0;

		$_class = $this->toolClassName . 'TreeDataSource';
		include_once( $this->toolDir . 'class/' . $_class . '.class.php');

		$_loader = new $_class($this->TreeSource);

		$rootjs = '';
		if(!$pid){
			$rootjs.='
			' . $this->Tree->topFrame . '.treeData.clear();
			' . $this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));
			';
		}

		$hiddens = we_html_element::htmlHidden(array('name' => 'pnt', 'value' => 'cmd')) .
			we_html_element::htmlHidden(array('name' => 'cmd', 'value' => 'no_cmd'));

		$out.=we_html_element::htmlBody(array('bgcolor' => 'white', 'marginwidth' => '10', 'marginheight' => '10', 'leftmargin' => '10', 'topmargin' => '10'), we_html_element::htmlForm(array('name' => 'we_form'), $hiddens .
					we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree($_loader->getItems($pid, $offset, $this->Tree->default_segment, '')))
				)
		);

		return $this->getHTMLDocument($out);
	}

	function formFileChooser($width = '', $IDName = 'ParentID', $IDValue = '/', $cmd = '', $filter = ''){
		//javascript:we_cmd('browse_server','document.we_form.elements[\\'$IDName\\'].value','$filter',document.we_form.elements['$IDName'].value);
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc4 = '';
		$button = we_button::create_button('select', "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','$filter',document.we_form.elements['$IDName'].value);");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, '', 'readonly', 'text', ($this->_width_size - 120), 0), "", "left", "defaultfont", "", we_html_tools::getPixel(20, 4), we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	function getHTMLExitQuestion(){
		if(isset($_REQUEST['delayCmd']) && isset($_REQUEST['delayParam'])){

			$_frame = 'opener.' . $this->topFrame;
			$_form = $_frame . '.document.we_form';

			$_yes = $_frame . '.hot=0;' . $_frame . '.we_cmd("tool_' . $this->toolName . '_save");self.close();';
			$_no = $_frame . '.hot=0;' . $_frame . '.we_cmd("' . $_REQUEST['delayCmd'] . '","' . $_REQUEST['delayParam'] . '");self.close();';
			$_cancel = 'self.close();';

			return we_html_tools::getHtmlTop() .
				STYLESHEET . '
			</head>

			<body class="weEditorBody" onBlur="self.focus()" onload="self.focus()">
					' . we_html_tools::htmlYesNoCancelDialog(g_l('tools', '[exit_doc_question]'), IMAGE_DIR . "alert.gif", "ja", "nein", "abbrechen", $_yes, $_no, $_cancel) . '
			</body>
			</html>';
		}
	}

	function getHTMLDocument($body, $head = ""){

		$head = we_html_tools::getHtmlInnerHead() . STYLESHEET .
			we_html_element::jsScript(JS_DIR . 'attachKeyListener.js') .
			$head;

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				$body
		);
	}

	function getHTMLChooser($title, $table = FILE_TABLE, $rootDirID = 0, $IDName = 'ID', $IDValue = '0', $PathName = 'Path', $cmd = '', $filter = 'text/webedition', $disabled = false, $showtrash = false){
		$_path = id_to_path($this->Model->$IDName, $table);

		$_cmd = "javascript:we_cmd('open" . $this->toolName . "Dirselector',document.we_form.elements['" . $IDName . "'].value,'document.we_form." . $IDName . ".value','document.we_form." . $PathName . ".value','" . $cmd . "')";

		if($showtrash){
			$_button = we_button::create_button_table(array(
					we_button::create_button('select', $_cmd, true, 100, 22, '', '', $disabled),
					we_button::create_button('image:btn_function_trash', 'javascript:document.we_form.elements["' . $IDName . '"].value=0;document.we_form.elements["' . $PathName . '"].value="/";', true, 27, 22)
					), 10);
			$_width = 157;
		} else{
			$_button = we_button::create_button('select', $_cmd, true, 100, 22, '', '', $disabled);
			$_width = 120;
		}

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($PathName, 58, $_path, '', 'readonly', 'text', ($this->_width_size - $_width), 0), $title, 'left', 'defaultfont', we_html_element::htmlHidden(array('name' => $IDName, 'value' => $IDValue)), we_html_tools::getPixel(20, 4), $_button
		);
	}

}