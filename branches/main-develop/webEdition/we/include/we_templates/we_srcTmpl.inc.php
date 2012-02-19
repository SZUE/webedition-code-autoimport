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
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tag.inc.php');

$parts = array();

if(isset($we_doc->elements["Charset"]["dat"])){ //	send charset which might be determined in template
	we_html_tools::headerCtCharset('text/html',$we_doc->elements["Charset"]["dat"]);
}

if($GLOBALS['we_editmode']){
	we_html_tools::htmlTop('', isset($we_doc->elements["Charset"]["dat"]) ? $we_doc->elements["Charset"]["dat"] : '');
	echo we_html_element::jsScript(JS_DIR . 'windows.js');
	include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_editors/we_editor_script.inc.php");
	print STYLESHEET;

	$_useJavaEditor = ($_SESSION['prefs']['editorMode'] == 'java');
	?>
	<script  type="text/javascript">

		var weIsTextEditor = true;
		var wizardHeight={
			"open" : 305,
			"closed" : 140
		}


		function sizeEditor() { // to be fixed (on 12.12.11)
			var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
			var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
			w = Math.max(w,350);
			var editorWidth = w - <?php echo ($_SESSION['prefs']['editorMode'] == 'codemirror2' ? 60 : 37); ?>;
			var wizardOpen = weGetCookieVariable("but_weTMPLDocEdit") == "right";

			var editarea = document.getElementById("editarea");

			var wizardTable = document.getElementById("wizardTable");
			var tagAreaCol = document.getElementById("tagAreaCol");
			var tagSelectCol = document.getElementById("tagSelectCol");
			var spacerCol = document.getElementById("spacerCol");
			var tag_edit_area = document.getElementById("tag_edit_area");

			if (editarea) {
				editarea.style.width=editorWidth + "px";
				if(editarea.nextSibling!=undefined && editarea.nextSibling.style)
					editarea.nextSibling.style.width=editorWidth + "px";
			}

			if (document.weEditorApplet) {
				document.weEditorApplet.width = editorWidth;

			}

			if(window.editor && window.editor.frame) {
				if(window.editor.frame.nextSibling!=undefined) {
					editorWidth-=window.editor.frame.nextSibling.offsetWidth;
					document.getElementById("reindentButton").style.marginRight= (window.editor.frame.nextSibling.offsetWidth-3) + "px";
				}
				window.editor.frame.style.width = editorWidth + "px";
			}

			if (h) { // h must be set (h!=0), if several documents are opened very fast -> editors are not loaded then => h = 0



				if (wizardTable != null) {

					var editorHeight = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open));

					if (editarea) {
						editarea.style.height= (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
						if(editarea.nextSibling!=undefined && editarea.nextSibling.style)
							editarea.nextSibling.style.height= (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
					}

					if(window.editor && window.editor.frame) {
						window.editor.frame.style.height = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
					}

					if (document.weEditorApplet && typeof(document.weEditorApplet.setSize) != "undefined") {
						document.weEditorApplet.height = editorHeight;
						document.weEditorApplet.setSize(editorWidth,editorHeight);

					}


					wizardTable.style.width=editorWidth+"px";
					//wizardTableButtons.style.width=editorWidth+"px"; // causes problems with codemirror2
					tagAreaCol.style.width=(editorWidth-300)+"px";
					tag_edit_area.style.width=(editorWidth-300)+"px";
					tagSelectCol.style.width = "250px";
					spacerCol.style.width = "50px";

				} else {
					if (editarea) {
						editarea.style.height = (h - wizardHeight.closed) + "px";
						if(editarea.nextSibling!=undefined && editarea.nextSibling.style)
							editarea.nextSibling.style.height = (h - wizardHeight.closed) + "px";
					}

					if(window.editor && window.editor.frame) {
						window.editor.frame.style.height = (h - wizardHeight.closed) + "px";
					}

					if (document.weEditorApplet && typeof(document.weEditorApplet.setSize) != "undefined") {
						document.weEditorApplet.height = h - wizardHeight.closed;
						document.weEditorApplet.setSize(editorWidth,h - wizardHeight.closed);
					}
				}
			}
			window.scroll(0,0);

		}
		var editor=null;

		function initEditor() {
	<?php
	if($_SESSION['prefs']['editorMode'] == 'codemirror2'){
		echo 'document.getElementById("bodydiv").style.display="block";
		editor = CodeMirror.fromTextArea(document.getElementById("editarea"), CMoptions);
		sizeEditor();

				return;';
	}
	?>

					if (document.weEditorApplet) {
						if (top.weEditorWasLoaded && document.weEditorApplet && typeof(document.weEditorApplet.setCode) != "undefined") {
							document.getElementById("weEditorApplet").style.left="0";
							document.weEditorApplet.setCode(document.forms['we_form'].elements["<?php print 'we_' . $we_doc->Name . '_txt[data]'; ?>"].value);
							document.weEditorApplet.initUndoManager();

							sizeEditor();
							checkAndSetHot();
						} else {
							setTimeout(initEditor, 1000);
						}
					} else {
						sizeEditor();
						window.setTimeout('scrollToPosition();',50);
					}
					document.getElementById("bodydiv").style.display="block";
				}

				function toggleTagWizard() {
					var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
					w = Math.max(w,350);
					var editorWidth = w - 37;
					var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
					var wizardOpen = weGetCookieVariable("but_weTMPLDocEdit") == "down";
					if (document.weEditorApplet) {
						var editorHeight = h- (wizardOpen ? wizardHeight.closed : wizardHeight.open);
						document.weEditorApplet.height = editorHeight;
						document.weEditorApplet.setSize(editorWidth,editorHeight);
					} else {

						var editarea = document.getElementById("editarea");
						editarea.style.height= (h- (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
						if(editarea.nextSibling!=undefined && editarea.nextSibling.style)
							editarea.nextSibling.style.height= (h- (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";

						if(window.editor && window.editor.frame) {
							window.editor.frame.style.height = (h- (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
						}
					}

				}

				// ################ Java Editor specific Functions

				function weEditorSetHiddenText() {
					if (document.weEditorApplet && typeof(document.weEditorApplet.getCode) != "undefined") {
						if (document.weEditorApplet.isHot()) {
							_EditorFrame.setEditorIsHot(true);
							document.weEditorApplet.setHot(false);
						}
						document.forms['we_form'].elements["<?php print 'we_' . $we_doc->Name . '_txt[data]'; ?>"].value = document.weEditorApplet.getCode();
					}
				}


				function checkAndSetHot() {
					if (document.weEditorApplet && typeof(document.weEditorApplet.isHot) != "undefined") {
						if (document.weEditorApplet.isHot()) {
							_EditorFrame.setEditorIsHot(true);
						} else {
							setTimeout("checkAndSetHot()", 1000);
						}
					}
				}

				function setCode() {
					if (document.weEditorApplet && typeof(document.weEditorApplet.setCode) != "undefined") {
						document.weEditorApplet.setCode(document.forms['we_form'].elements["<?php print 'we_' . $we_doc->Name . '_txt[data]'; ?>"].value);
					}
				}

				// ################## Textarea specific functions #############

				function getScrollPosTop () {
					var elem = document.getElementById("editarea");
					if (elem) {
						return elem.scrollTop;
					}
					return 0;

				}

				function getScrollPosLeft () {
					var elem = document.getElementById("editarea");
					if (elem) {
						return elem.scrollLeft;
					}
					return 0;
				}

				function scrollToPosition () {
					var elem = document.getElementById("editarea");
					if (elem) {
						elem.scrollTop=parent.editorScrollPosTop;
						elem.scrollLeft=parent.editorScrollPosLeft;
					}
				}

				function wedoKeyDown(ta,keycode){

					if (keycode == 9) { // TAB
						if (ta.setSelectionRange) {
							var selectionStart = ta.selectionStart;
							var selectionEnd = ta.selectionEnd;
							ta.value = ta.value.substring(0, selectionStart)
								+ "\t"
								+ ta.value.substring(selectionEnd);
							ta.focus();
							ta.setSelectionRange(selectionEnd+1, selectionEnd+1);
							ta.focus();
							return false;

						} else if (document.selection) {
							var selection = document.selection;
							var range = selection.createRange();
							range.text = "\t";
							return false;
						}
					}

					return true;
				}
				// ############ EDITOR PLUGIN ################

				function setSource(source){
					document.forms['we_form'].elements['we_<?php print $we_doc->Name; ?>_txt[data]'].value=source;
					// for Applet
					setCode(source);
				}

				function getSource(){
					if (document.weEditorApplet && typeof(document.weEditorApplet.getCode) != "undefined") {
						return document.weEditorApplet.getCode();
					} else {
						return document.forms['we_form'].elements['we_<?php print $we_doc->Name; ?>_txt[data]'].value;
					}
				}

				function getCharset(){
					return "<?php print !empty($we_doc->elements['Charset']['dat']) ? $we_doc->elements['Charset']['dat'] : $GLOBALS['WE_BACKENDCHARSET']; ?>";
				}

				// ############ CodeMirror Functions ################

				function reindent() { // reindents code of CodeMirror
					if(editor.selection().length)
						editor.reindentSelection();
					else
						editor.reindent();
				}

				function reindent2() { // reindents code of CodeMirror2
					if(editor.somethingSelected()){
						start=editor.getCursor(true).line;
						end=editor.getCursor(false).line;
					}else{
						start=0;
						end=editor.lineCount();
					}
					for(i=start;i<end;++i){
						editor.indentLine(i);
					}
				}
				var lastPos = null, lastQuery = null, marked = [];
				function unmark() {
					for (var i = 0; i < marked.length; ++i) marked[i].clear();
					marked.length = 0;
				}
				function search(text) {
					unmark();
					if (!text) return;
					for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
						marked.push(editor.markText(cursor.from(), cursor.to(), "searched"));

					if (lastQuery != text) lastPos = null;
					var cursor = editor.getSearchCursor(text, lastPos || editor.getCursor());
					if (!cursor.findNext()) {
						cursor = editor.getSearchCursor(text);
						if (!cursor.findNext()) return;
					}
					editor.setSelection(cursor.from(), cursor.to());
					lastQuery = text; lastPos = cursor.to();
				}

				function myReplace(text, replaceby) {
					if(!text|| !replaceby) return;
					if(editor.getSelection()!=text){
						search(text);
					}
					if(editor.getSelection()!=text){
						return;
					}
					editor.replaceSelection(replaceby);
					search(text);
				}
	</script>
	</head>
	<body class="weEditorBody" style="overflow:hidden;" onLoad="setTimeout('initEditor()',200);" onUnload="doUnload(); parent.editorScrollPosTop = getScrollPosTop(); parent.editorScrollPosLeft = getScrollPosLeft();" onResize="sizeEditor();"><?php //'       ?>
		<form name="we_form" method="post" onsubmit="return false;" style="margin:0px;"><?php
		$we_doc->pHiddenTrans();
	}

	function we_getJavaEditorCode($code){
		global $we_doc;
		$maineditor = '<input type="hidden" name="we_' . $we_doc->Name . '_txt[data]" value="' . htmlspecialchars($code) . '" />
    <applet id="weEditorApplet" style="position:relative;right:-3000px;" name="weEditorApplet" code="Editor.class" archive="editor.jar" width="3000" height="3000" MAYSCRIPT SCRIPTABLE codebase="' . getServerUrl(true) . '/webEdition/editors/template/editor"/>
    <param name="phpext" value=".php"/>
	<param name="serverUrl" value="' . getServerUrl(true) . '"/>
	<param name="editorPath" value="webEdition/editors/template/editor"/>';



		if($_SESSION["prefs"]["editorFont"] == 1){
			// translate html font names into java font names
			switch($_SESSION["prefs"]["editorFontname"]){
				case "mono":
					$fontname = 'monospaced';
					break;
				case "sans-serif":
					$fontname = 'sansserif';
					break;
				default:
					$fontname = $_SESSION['prefs']['editorFontname'];
					break;
			}
			$maineditor .= '<param name="fontName" value="' . $fontname . '"/>';
			$maineditor .= '<param name="fontSize" value="' . $_SESSION["prefs"]["editorFontsize"] . '"/>';
		}

		if($_SESSION["prefs"]["specify_jeditor_colors"] == 1){
			$maineditor .= '<param name="normalColor" value="' . $_SESSION["prefs"]["editorFontcolor"] . '">';
			$maineditor .= '<param name="weTagColor" value="' . $_SESSION["prefs"]["editorWeTagFontcolor"] . '">';
			$maineditor .= '<param name="weAttributeColor" value="' . $_SESSION["prefs"]["editorWeAttributeFontcolor"] . '">';
			$maineditor .= '<param name="HTMLTagColor" value="' . $_SESSION["prefs"]["editorHTMLTagFontcolor"] . '">';
			$maineditor .= '<param name="HTMLAttributeColor" value="' . $_SESSION["prefs"]["editorHTMLAttributeFontcolor"] . '">';
			$maineditor .= '<param name="piColor" value="' . $_SESSION["prefs"]["editorPiTagFontcolor"] . '">';
			$maineditor .= '<param name="commentColor" value="' . $_SESSION["prefs"]["editorCommentFontcolor"] . '">';
		}

		$maineditor .= '</applet>';
		return $maineditor;
	}

	function we_get_CM_css(){
		return
			'<style type="text/css">
		.CodeMirror-line-numbers {
			padding-top: 6px;
			padding-right: 5px;
			text-align: right;
		}
		#tagDescriptionDiv {
			color: black;
			background: white;
			position: absolute;
			width: 400px;
			padding: 5px 8px;
			z-index: 1000;
			font-family: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontname'] ? $_SESSION['prefs']['editorTooltipFontname'] : 'Tahoma') . ';
			font-size: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontsize'] ? $_SESSION['prefs']['editorTooltipFontsize'] : '12') . 'px;
			border: outset 1px;
			box-shadow: 0 2px 2px rgba(0,0,0,0.3);
			-moz-box-shadow: 0 2px 2px rgba(0,0,0,0.3);
			-webkit-box-shadow: 0 2px 2px rgba(0,0,0,0.3);
			border-radius: 3px;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
		}
	</style>';
	}

	function we_getCodeMirrorCode($code){
		global $we_doc;
		$maineditor = '';
		$parser_js = array();
		$parser_css = array();
		$useCSCC = false;
		switch($we_doc->ContentType){ // Depending on content type we use different parsers and css files
			case 'text/css':
				$parser_js[] = 'parsecss.js';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/css/csscolors.css';
				break;
			case 'text/js':
				$parser_js[] = 'tokenizejavascript.js';
				$parser_js[] = 'parsejavascript.js';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/css/jscolors.css';
				break;
			case 'text/weTmpl':
				$useCSCC = we_base_browserDetect::isIE() ? false : true; //tag completion doesn't work in IE yet
				$parser_js[] = 'parsexml.js';
				$parser_js[] = 'parsecss.js';
				$parser_js[] = 'tokenizejavascript.js';
				$parser_js[] = 'parsejavascript.js';
				$parser_js[] = '../contrib/php/js/tokenizephp.js';
				$parser_js[] = '../contrib/php/js/parsephp.js';
				$parser_js[] = '../contrib/php/js/parsephphtmlmixed.js';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/css/xmlcolors.css';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/css/jscolors.css';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/css/csscolors.css';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/contrib/php/css/phpcolors.css';
				break;
			case 'text/html':
				$parser_js[] = 'parsexml.js';
				$parser_js[] = 'parsecss.js';
				$parser_js[] = 'tokenizejavascript.js';
				$parser_js[] = 'parsejavascript.js';
				$parser_js[] = '../contrib/php/js/tokenizephp.js';
				$parser_js[] = '../contrib/php/js/parsephp.js';
				$parser_js[] = '../contrib/php/js/parsephphtmlmixed.js';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/css/xmlcolors.css';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/css/jscolors.css';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/css/csscolors.css';
				break;
			case 'text/xml':
				$parser_js[] = 'parsexml.js';
				$parser_css[] = '/webEdition/editors/template/CodeMirror/css/xmlcolors.css';
				break;
			default:
				//don't use CodeMirror
				return '';
		}
		$parser_css[] = '/webEdition/editors/template/CodeMirror/contrib/webEdition/css/webEdition.css';
		if(count($parser_js) > 0){ // CodeMirror will be used
			$maineditor = we_get_CM_css() . we_html_element::jsScript('/webEdition/editors/template/CodeMirror/js/codemirror.js');
			if($useCSCC && $_SESSION['prefs']['editorCodecompletion']){ //if we use tag completion we need additional files
				$maineditor.=
					we_html_element::jsScript('/webEdition/editors/template/CodeMirror/contrib/cscc/js/cscc.js') .
					we_html_element::jsScript('/webEdition/editors/template/CodeMirror/contrib/cscc/js/cscc-parse-xml.js') .
					we_html_element::jsScript('/webEdition/editors/template/CodeMirror/contrib/cscc/js/cscc-parse-css.js') .
					we_html_element::jsScript('/webEdition/editors/template/CodeMirror/contrib/cscc/js/cscc-sense.js') .
					'
							<script type="text/javascript">
								if(top.we_tags==undefined) { //this is our tag cache
									document.write("<scr"+"ipt src=\"/webEdition/editors/template/CodeMirror/contrib/webEdition/js/vocabulary.js.php\" type=\"text/javascript\"></sc"+"ript>");
								};
							</script>
						';
			}
			$maineditor.='
						<script type="text/javascript">
							var getDescriptionDiv=function() {
								var ed=(typeof cscc!="undefined"?cscc.editor:window.editor); //depending on the use of CSCC the editor object will be different locations
								var wrap = ed.wrapping;
								var doc = wrap.ownerDocument;
								var tagDescriptionDiv = doc.getElementById("tagDescriptionDiv");
								if(!tagDescriptionDiv) { //if our div is not yet in the DOM, we create it
									var tagDescriptionDiv = doc.createElement("div");
									tagDescriptionDiv.setAttribute("id", "tagDescriptionDiv");
									if(tagDescriptionDiv.addEventListener) {
										tagDescriptionDiv.addEventListener("mouseover", hideDescription, false);
									}
									else {
										tagDescriptionDiv.attachEvent("onmouseover", hideDescription);
									}
									wrap.appendChild(tagDescriptionDiv);
								}
								return tagDescriptionDiv;
							};
							var hideDescription=function(){
								var ed=(typeof cscc!="undefined"?cscc.editor:window.editor); //depending on the use of CSCC the editor object will be different locations
								var wrap = ed.wrapping;
								var doc = wrap.ownerDocument;
								var tagDescriptionDiv = getDescriptionDiv();
								tagDescriptionDiv.style.display="none";
							};
							var XgetComputedStyle = function(el, s) { // cross browser getComputedStyle()
								var computedStyle;
								if(typeof el.currentStyle!="undefined") {
									computedStyle = el.currentStyle;
								}
								else {
									computedStyle = document.defaultView.getComputedStyle(el, null);
								}
								return computedStyle[s];
							};
							var CMoptions = { //these are the CodeMirror options
								tabMode: "spaces",
								height: "' . (($_SESSION["prefs"]["editorHeight"] != 0) ? $_SESSION["prefs"]["editorHeight"] : "320") . '",
								textWrapping:' . ((isset($_SESSION["we_wrapcheck"]) && $_SESSION["we_wrapcheck"]) ? 'true' : 'false') . ',
								parserfile: ["' . (implode('", "', $parser_js)) . '"],
								stylesheet: ["' . (implode('", "', $parser_css)) . '"],
								path: "/webEdition/editors/template/CodeMirror/js/",
								autoMatchParens: false,
								' . ($useCSCC && $we_doc->ContentType == 'text/weTmpl' && $_SESSION['prefs']['editorCodecompletion'] ? 'cursorActivity: cscc.cursorActivity,' : '') . '
								undoDelay: 200,
								lineNumbers: ' . ($_SESSION['prefs']['editorLinenumbers'] ? 'true' : 'false') . ',
								initCallback: function() {
									window.setTimeout(function(){ //without timeout this will raise an exception in firefox
										if (document.addEventListener) {
											editor.frame.contentWindow.document.addEventListener( "keydown", top.dealWithKeyboardShortCut, true );
										} else if(document.attachEvent) {
											editor.frame.contentWindow.document.attachEvent( "onkeydown", top.dealWithKeyboardShortCut );
										}
										editor.focus();
										editor.frame.style.border="1px solid grey";

										var editorFrame=editor.frame.contentWindow.document.getElementsByTagName("body")[0];
										var originalTextArea=document.getElementById("editarea");
										var lineNumbers=editor.frame.nextSibling

										//we adapt font styles from original <textarea> to CodeMirror
										editorFrame.style.fontSize=XgetComputedStyle(originalTextArea,"fontSize");
										editorFrame.style.fontFamily=XgetComputedStyle(originalTextArea,"fontFamily");
										editorFrame.style.lineHeight=XgetComputedStyle(originalTextArea,"lineHeight");
										editorFrame.style.marginTop="5px";

										//we adapt font styles from orignal <textarea> to the line numbers of CodeMirror.
										if(lineNumbers!=undefined) { //line numbers might be disabled
											lineNumbers.style.fontSize=XgetComputedStyle(originalTextArea,"fontSize");
											lineNumbers.style.fontFamily=XgetComputedStyle(originalTextArea,"fontFamily");
											lineNumbers.style.lineHeight=XgetComputedStyle(originalTextArea,"lineHeight");
										}

										sizeEditor();
										var showDescription=function(e) { //this function will display a tooltip with the tags description. will be called by onmousemove
											var ed=(typeof cscc!="undefined"?cscc.editor:window.editor); //depending on the use of CSCC the editor object will be different locations
											if(typeof ed=="undefined" || !ed)
												return
											var wrap = ed.wrapping;
											var doc = wrap.ownerDocument;
											var tagDescriptionDiv = getDescriptionDiv();
											if(top.currentHoveredTag===undefined) { //no tag is currently hoverd -> hide description
												hideDescription();
												return;
											}
											var tag=top.currentHoveredTag.innerHTML.replace(/\s/,"").replace(/&nbsp;/,"");
											if((top.we_tags === undefined) || (top.we_tags[tag]===undefined)) { //unkown tag -> hide description
												hideDescription();
												return;
											}
											//at this point we have a a description for our currently hovered tag. so we calculate of the mouse and display it
											tagDescriptionDiv.innerHTML=top.we_tags[tag].desc;
											x = (e.pageX ? e.pageX : window.event.x) + tagDescriptionDiv.scrollLeft - editor.frame.contentWindow.document.body.scrollLeft;
											y = (e.pageY ? e.pageY : window.event.y) + tagDescriptionDiv.scrollTop - editor.frame.contentWindow.document.body.scrollTop;
											if(x>0 && y>0) {
												if(window.innerWidth-x<468) {
													x+=(window.innerWidth-(e.pageX ? e.pageX : window.event.x)-468);
												}
												tagDescriptionDiv.style.left = (x + 25) + "px";
												tagDescriptionDiv.style.top   = (y + 15) + "px";
											}
											tagDescriptionDiv.style.display="block";
										};

										if(typeof(cscc) != "undefined" && typeof(cscc) != "false") { //tag completion is beeing used
											var hideCscc=function() {
												cscc.hide();
											}
										}
					';
			if($useCSCC && $we_doc->ContentType == 'text/weTmpl'){
				$maineditor.='
										if(window.addEventListener) {
											editor.frame.contentWindow.document.addEventListener("mousemove", showDescription, false);
											editor.frame.contentWindow.document.addEventListener("click", hideCscc, false);
										}
										else {
											editor.frame.contentWindow.document.attachEvent("onmousemove", showDescription);
											editor.frame.contentWindow.document.attachEvent("onclick", hideCscc);
										}
									';
			}
			$maineditor.='
									},500);
								},
								onChange: function(){
									updateEditor();
								}
								';
			if($useCSCC){
				$maineditor.='
							,activeTokens: function(span, token) {
								if(token.style == "xml-tagname" && !span.className.match(/we-tagname/) && token.content.substring(0,3)=="we:" ) { //this is our hook to colorize we:tags
									span.className += " we-tagname";
									var clickTag=function(){
										hideDescription();
										we_cmd("open_tagreference",token.content.substring(3));
									};
									var mouseOverTag=function() {
										top.currentHoveredTag=span;
									}
									var mouseOutTag=function() {
										top.currentHoveredTag=undefined;
									}
									if(window.addEventListener) {
										' . ($_SESSION['prefs']['editorDocuintegration'] ? 'span.addEventListener("dblclick", clickTag, false);' : '') . '
										' . ($_SESSION['prefs']['editorTooltips'] ? 'span.addEventListener("mouseover", mouseOverTag, false);span.addEventListener("mouseout", mouseOutTag, false);' : '') . '
									}
									else {
										' . ($_SESSION['prefs']['editorDocuintegration'] ? 'span.attachEvent("ondblclick", clickTag);' : '') . '
										' . ($_SESSION['prefs']['editorTooltips'] ? 'span.attachEvent("onmouseover", mouseOverTag);span.attachEvent("onmouseout", mouseOutTag);' : '') . '
									}
								}
							},
							cursorActivity: function(el) { //this is our hook for focusing on the right item inside the tag-generator
								try {
									if(el===null || el.className==undefined)
										return;
									while(!el.className.match(/we-tagname/)) {
										if(el.innerHTML=="&gt;" || el.innerHTML=="&lt;" || el.innerHTML=="/&gt;")
											return;
										el=el.previousSibling;
									}
									var currentTag=el.innerHTML.substring(3).replace(/\s/,"");
									for(var i=0;i<document.getElementById("weTagGroupSelect").options.length;i++) {
										if(document.getElementById("weTagGroupSelect").options[i].value=="alltags") {
											document.getElementById("weTagGroupSelect").options[i].selected="selected";
											selectTagGroup("alltags");
											for(var j=0;i<document.getElementById("tagSelection").options.length;j++) {
												if(document.getElementById("tagSelection").options[j].value==currentTag) {
													document.getElementById("tagSelection").options[j].selected="selected";
													break;
												}
											}
											break;
										}
									}
								}catch(e){};
							}
						';
			}
			$maineditor.='
							};
							var updateEditor=function(){ //this wil save content from CoeMirror to our original <textarea>.
								var currentTemplateCode=editor.getCode();
								if(window.orignalTemplateContent!=currentTemplateCode) {
									window.orignalTemplateContent=currentTemplateCode;
									document.getElementById("editarea").value=currentTemplateCode;
									_EditorFrame.setEditorIsHot(true);
								}
							}
							window.orignalTemplateContent=document.getElementById("editarea").value; //this is our reference of the original content to compare with current content
						</script>
					';
			if($useCSCC && $_SESSION['prefs']['editorCodecompletion']){ //initiation depends on the use of code completion
				$maineditor.=
					'<script type="text/JavaScript">
				cscc.init("editarea");
				var editor=cscc.editor;
			</script>';
			} else{
				$maineditor.=
					'<script type="text/JavaScript">
				var editor = CodeMirror.fromTextArea("editarea", CMoptions);
			</script>';
			}
		}
		return $maineditor;
	}

	function we_getCodeMirror2Tags(){
		$ret = '';
		$allWeTags = weTagWizard::getExistingWeTags();
		foreach($allWeTags as $tagName){
			$GLOBALS['TagRefURLName'] = strtolower($tagName);
			if(isset($weTag)){
				unset($weTag);
			}
			$weTag = weTagData::getTagData($tagName);
			$ret.='.cm-weTag_' . $tagName . ':hover:after {content: "' . str_replace('"', '\'', html_entity_decode($weTag->getDescription(),null,$GLOBALS['WE_BACKENDCHARSET'])) . '";}' . "\n";
		}
		return $ret;
	}

	function we_getCodeMirror2Code($code){
		$maineditor = '';
		$parser_js = array();
		$parser_css = array('theme/default.css');
		$toolTip=false;
		switch($GLOBALS['we_doc']->ContentType){ // Depending on content type we use different parsers and css files
			case 'text/css':
				$parser_js[] = 'mode/css/css.js';
				$mode = 'text/css';
				break;
			case 'text/js':
				$parser_js[] = 'mode/javascript/javascript.js';
				$mode = 'text/javascript';
				break;
			case 'text/weTmpl':
				$parser_js[] = 'lib/util/overlay.js';
				$parser_js[] = 'mode/webEdition/webEdition.js';
				$toolTip = $_SESSION['prefs']['editorTooltips'];
				$mode = 'text/weTmpl';
			case 'text/html':
				$parser_js[] = 'mode/xml/xml.js';
				$parser_js[] = 'mode/javascript/javascript.js';
				$parser_js[] = 'mode/css/css.js';
				$parser_js[] = 'mode/htmlmixed/htmlmixed.js';
				$parser_js[] = 'mode/clike/clike.js';
				$parser_js[] = 'mode/php/php.js';
				$parser_css[]= 'mode/clike/clike.css';
				$mode = (isset($mode) ? $mode : 'application/x-httpd-php');
				break;
			case 'text/xml':
				$parser_js[] = 'mode/xml/xml.js';
				$mode = 'application/xml';
				break;
			default:
				//don't use CodeMirror
				return '';
		}

		$parser_css[] = 'mode/webEdition/webEdition.css';

		if(count($parser_js) > 0){ // CodeMirror will be used
			$parser_js[]='lib/util/searchcursor.js';
			$maineditor = we_html_element::cssLink(WEBEDITION_DIR . 'editors/template/CodeMirror2/lib/codemirror.css').
				we_html_element::jsScript(WEBEDITION_DIR . 'editors/template/CodeMirror2/lib/codemirror.js');
			foreach($parser_css as $css){
				$maineditor.=we_html_element::cssLink(WEBEDITION_DIR . 'editors/template/CodeMirror2/' . $css);
			}
			foreach($parser_js as $js){
				$maineditor.=we_html_element::jsScript(WEBEDITION_DIR . 'editors/template/CodeMirror2/' . $js);
			}

			$maineditor.='
		<style type="text/css">' . ($toolTip?we_getCodeMirror2Tags():'') . '
			.weSelfClose:hover:after, .cm-weSelfClose:hover:after, .weOpenTag:hover:after, .cm-weOpenTag:hover:after, .weTagAttribute:hover:after, .cm-weTagAttribute:hover:after {
				font-family: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontname'] ? $_SESSION['prefs']['editorTooltipFontname'] : 'Tahoma') . ';
				font-size: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontsize'] ? $_SESSION['prefs']['editorTooltipFontsize'] : '12') . 'px;
				line-height: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontsize'] ? $_SESSION['prefs']['editorTooltipFontsize'] : '12') . 'px;
			}
			.searched {background: yellow;}
			.activeline {background: #f0fcff !important;}
			.CodeMirror{
			color: black;
			background: white;
			padding: 5px 8px;
			z-index: 1000;
			font-family: ' . ($_SESSION['prefs']['editorFont'] && $_SESSION['prefs']['editorFontname'] ? $_SESSION['prefs']['editorFontname'] : 'Tahoma') . ';
			font-size: ' . ($_SESSION['prefs']['editorFont'] && $_SESSION['prefs']['editorFontsize'] ? $_SESSION['prefs']['editorFontsize'] : '12') . 'px;
			border: outset 1px;
			box-shadow: 0 2px 2px rgba(0,0,0,0.3);
			-moz-box-shadow: 0 2px 2px rgba(0,0,0,0.3);
			-webkit-box-shadow: 0 2px 2px rgba(0,0,0,0.3);
			border-radius: 3px;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
			}
	span.c-like-keyword {
		color: #000;
		font-weight: bold;
	}

</style>
		<script type="text/javascript">
			var getDescriptionDiv=function() {
				var ed=window.editor;
				var wrap = ed.wrapping;
				var doc = wrap.ownerDocument;
				var tagDescriptionDiv = doc.getElementById("tagDescriptionDiv");
				if(!tagDescriptionDiv) { //if our div is not yet in the DOM, we create it
					var tagDescriptionDiv = doc.createElement("div");
					tagDescriptionDiv.setAttribute("id", "tagDescriptionDiv");
					if(tagDescriptionDiv.addEventListener) {
						tagDescriptionDiv.addEventListener("mouseover", hideDescription, false);
					} else {
						tagDescriptionDiv.attachEvent("onmouseover", hideDescription);
					}
					wrap.appendChild(tagDescriptionDiv);
				}
				return tagDescriptionDiv;
			};
			var hideDescription=function(){
				var ed=(typeof cscc!="undefined"?cscc.editor:window.editor); //depending on the use of CSCC the editor object will be different locations
				var wrap = ed.wrapping;
				var doc = wrap.ownerDocument;
				var tagDescriptionDiv = getDescriptionDiv();
				tagDescriptionDiv.style.display="none";
			};
			var XgetComputedStyle = function(el, s) { // cross browser getComputedStyle()
				var computedStyle;
				if(typeof el.currentStyle!="undefined") {
					computedStyle = el.currentStyle;
				}else {
					computedStyle = document.defaultView.getComputedStyle(el, null);
				}
				return computedStyle[s];
			};

			var CMoptions = { //these are the CodeMirror options
				mode: "' . $mode . '",
				tabMode: "classic",
				enterMode: "indent",
				electricChars: true,
				lineNumbers: ' . ($_SESSION['prefs']['editorLinenumbers'] ? 'true' : 'false') . ',
				gutter: false,
				indentWithTabs: true,
				matchBrackets: true,
				workTime: 300,
				workDelay: 800,
				height: "' . (($_SESSION["prefs"]["editorHeight"] != 0) ? $_SESSION["prefs"]["editorHeight"] : "320") . '",
				textWrapping:' . ((isset($_SESSION["we_wrapcheck"]) && $_SESSION["we_wrapcheck"]) ? 'true' : 'false') . ',
				 initCallback: function() {
					window.setTimeout(function(){ //without timeout this will raise an exception in firefox
						if (document.addEventListener) {
							editor.frame.contentWindow.document.addEventListener( "keydown", top.dealWithKeyboardShortCut, true );
						} else if(document.attachEvent) {
							editor.frame.contentWindow.document.attachEvent( "onkeydown", top.dealWithKeyboardShortCut );
						}
						editor.focus();
						editor.frame.style.border="1px solid gray";

						var editorFrame=editor.frame.contentWindow.document.getElementsByTagName("body")[0];
						var originalTextArea=document.getElementById("editarea");
						var lineNumbers=editor.frame.nextSibling

						//we adapt font styles from original <textarea> to CodeMirror
						editorFrame.style.fontSize=XgetComputedStyle(originalTextArea,"fontSize");
						editorFrame.style.fontFamily=XgetComputedStyle(originalTextArea,"fontFamily");
						editorFrame.style.lineHeight=XgetComputedStyle(originalTextArea,"lineHeight");
						editorFrame.style.marginTop="5px";

						//we adapt font styles from orignal <textarea> to the line numbers of CodeMirror.
						if(lineNumbers!=undefined) { //line numbers might be disabled
							lineNumbers.style.fontSize=XgetComputedStyle(originalTextArea,"fontSize");
							lineNumbers.style.fontFamily=XgetComputedStyle(originalTextArea,"fontFamily");
							lineNumbers.style.lineHeight=XgetComputedStyle(originalTextArea,"lineHeight");
						}

						sizeEditor();
						var showDescription=function(e) { //this function will display a tooltip with the tags description. will be called by onmousemove
							var ed=(typeof cscc!="undefined"?cscc.editor:window.editor); //depending on the use of CSCC the editor object will be different locations
							if(typeof ed=="undefined" || !ed)
								return
							var wrap = ed.wrapping;
							var doc = wrap.ownerDocument;
							var tagDescriptionDiv = getDescriptionDiv();
							if(top.currentHoveredTag===undefined) { //no tag is currently hoverd -> hide description
								hideDescription();
								return;
							}
							var tag=top.currentHoveredTag.innerHTML.replace(/\s/,"").replace(/&nbsp;/,"");
							if((top.we_tags === undefined) || (top.we_tags[tag]===undefined)) { //unkown tag -> hide description
								hideDescription();
								return;
							}
							//at this point we have a a description for our currently hovered tag. so we calculate of the mouse and display it
							tagDescriptionDiv.innerHTML=top.we_tags[tag].desc;
							x = (e.pageX ? e.pageX : window.event.x) + tagDescriptionDiv.scrollLeft - editor.frame.contentWindow.document.body.scrollLeft;
							y = (e.pageY ? e.pageY : window.event.y) + tagDescriptionDiv.scrollTop - editor.frame.contentWindow.document.body.scrollTop;
							if(x>0 && y>0) {
								if(window.innerWidth-x<468) {
									x+=(window.innerWidth-(e.pageX ? e.pageX : window.event.x)-468);
								}
								tagDescriptionDiv.style.left = (x + 25) + "px";
								tagDescriptionDiv.style.top   = (y + 15) + "px";
							}
							tagDescriptionDiv.style.display="block";
						};


					},500);
				},
				onChange: function(){
					updateEditor();
				}
};
			var updateEditor=function(){ //this wil save content from CodeMirror2 to our original <textarea>.
				var currentTemplateCode=editor.getValue().replace(/\r/g,"\n");
				if(window.orignalTemplateContent!=currentTemplateCode) {
					window.orignalTemplateContent=currentTemplateCode;
					document.getElementById("editarea").value=currentTemplateCode;
					_EditorFrame.setEditorIsHot(true);
				}
			}
			window.orignalTemplateContent=document.getElementById("editarea").value.replace(/\r/g,""); //this is our reference of the original content to compare with current content
</script>';
		}
		return $maineditor;
	}

	if($GLOBALS['we_editmode']){
		$wrap = (isset($_SESSION["we_wrapcheck"]) && $_SESSION["we_wrapcheck"]) ? "virtual" : "off";

		$code = $we_doc->getElement("data");

		if($we_doc->ClassName == "we_htmlDocument"){
			$code = $we_doc->getDocumentCode();
		}

		$maineditor = '<table border="0" cellpadding="0" cellspacing="0" width="95%"><tr><td>';

		$vers = '';
		if($_useJavaEditor){
			$maineditor .= we_getJavaEditorCode($code);
		} else{
			$maineditor .= '<textarea id="editarea" style="width: 100%; height: ' . (($_SESSION["prefs"]["editorHeight"] != 0) ? $_SESSION["prefs"]["editorHeight"] : "320") . 'px;' . (($_SESSION["prefs"]["editorFont"] == 1) ? " font-family: " . $_SESSION["prefs"]["editorFontname"] . "; font-size: " . $_SESSION["prefs"]["editorFontsize"] . "px;" : "") . '" id="data" name="we_' . $we_doc->Name . '_txt[data]" wrap="' . $wrap . '" ' . (($GLOBALS['BROWSER'] == "NN6" && (!isset($_SESSION["we_wrapcheck"]) || !$_SESSION["we_wrapcheck"] )) ? '' : ' rows="20" cols="80"') . ' onChange="_EditorFrame.setEditorIsHot(true);" ' . (we_base_browserDetect::isIE() ? 'onkeydown="return wedoKeyDown(this,event.keyCode);"' : 'onkeypress="return wedoKeyDown(this,event.keyCode);"') . '>'
				. htmlspecialchars($code) . '</textarea>';
			if($_SESSION['prefs']['editorMode'] == 'codemirror' || $_SESSION['prefs']['editorMode'] == 'codemirror2'){ //Syntax-Highlighting
				$vers = ($_SESSION['prefs']['editorMode'] == 'codemirror' ? '' : 2);
				$maineditor .= ($vers == 2 ? we_getCodeMirror2Code($code) : we_getCodeMirrorCode($code));
			}
		}
		$maineditor .= '</td>
         </tr>
         <tr>
            <td align="left">';
		$maineditor .= we_html_tools::getPixel(2, 10) . '<br><table cellpadding="0" cellspacing="0" border="0" width="100%">
	    <tr>';

		$maineditor .= ($vers == 2 ? '
<td align="left" class="defaultfont">
<input type="text" style="width: 10em;float:left;" id="query"/><div style="float:left;">' . we_button::create_button("search", 'javascript:search(document.getElementById("query").value);') . '</div>
<input type="text" style="margin-left:2em;width: 10em;float:left;" id="replace"/><div style="float:left;">' . we_button::create_button("replace", 'javascript:myReplace(document.getElementById("query").value,document.getElementById("replace").value);') . '</div>' .
				'</td>' : '') .
			'<td align="right" class="defaultfont">' .
			(substr($_SESSION['prefs']['editorMode'], 0, 10) == 'codemirror' ? '



<div id="reindentButton" style="float:right;margin-left:10px;margin-top:-3px;">' . we_button::create_button("reindent", 'javascript:reindent' . $vers . '();') . '</div>' : '') .
			($_useJavaEditor ? "" : we_forms::checkbox("1", ( isset($_SESSION["we_wrapcheck"]) && $_SESSION["we_wrapcheck"] == "1"), "we_wrapcheck_tmp", g_l('global', '[wrapcheck]'), false, "defaultfont", "we_cmd('wrap_on_off',this.checked)")) . '</td>	</tr>
        </table></td></tr></table>
';
		$znr = -1;
		$wepos = "";
		array_push($parts, array("headline" => "", "html" => $maineditor, "space" => 0));
	} else{
		print $maineditor;
	}

	if($GLOBALS['we_editmode']){
		if($we_doc->ContentType == "text/weTmpl"){
			// Code Wizard
			$CodeWizard = new weCodeWizard();
			$allWeTags = weTagWizard::getExistingWeTags();

			$tagGroups = weTagWizard::getWeTagGroups($allWeTags);

			$groupselect = '<select class="weSelect" style="width: 250px;" id="weTagGroupSelect" name="we_' . $we_doc->Name . '_TagWizardSelection" onchange="selectTagGroup(this.value);">';
			$groupJs = "tagGroups = new Array();\n";

			$selectedGroup = isset($we_doc->TagWizardSelection) && !empty($we_doc->TagWizardSelection) ? $we_doc->TagWizardSelection : "alltags";
			$groupselect .= '<optgroup label="' . g_l('weCodeWizard', '[snippets]') . '">';
			$groupselect .= '<option value="snippet_standard" ' . ($selectedGroup == "snippet_standard" ? "selected" : "") . '>' . g_l('weCodeWizard', '[standard_snippets]') . '</option>';
			$groupselect .= '<option value="snippet_custom" ' . ($selectedGroup == "snippet_custom" ? "selected" : "") . '>' . g_l('weCodeWizard', '[custom_snippets]') . '</option>';
			$groupselect .= '</optgroup>';
			$groupselect .= '<optgroup label="we:tags">';

			foreach($tagGroups as $tagGroupName => $tags){

				if($tagGroupName == 'custom'){
					$groupselect .= '<option value="-1" disabled="disabled">----------</option>';
				}
				$groupselect .= '<option value="' . $tagGroupName . '"' . ($tagGroupName == $selectedGroup ? ' selected="selected"' : '') . '">' . (in_array($tagGroupName, $GLOBALS['_we_active_integrated_modules']) ? g_l('javaMenu_moduleInformation', '[' . $tagGroupName . '][text]') : g_l('weTagGroups', '[' . $tagGroupName . ']')) . '</option>';
				if($tagGroupName == 'alltags'){
					$groupselect .= '<option value="-1" disabled="disabled">----------</option>';
				}
				$groupJs .= "tagGroups['" . $tagGroupName . "'] = new Array('" . implode("', '", $tags) . "');\n";
			}
			$groupselect .= '</optgroup>';
			$groupselect .= '</select>';

			$tagselect = '<select onkeydown="evt=event?event:window.event; return openTagWizWithReturn(evt)" class="defaultfont" style="width: 250px; height: 100px;" size="7" ondblclick="edit_wetag(this.value);" name="tagSelection" id="tagSelection" onChange="weButton.enable(\'btn_direction_right_applyCode\')">';

			for($i = 0; $i < sizeof($allWeTags); $i++){
				$tagselect .= '
	<option value="' . $allWeTags[$i] . '">' . $allWeTags[$i] . '</option>';
			}


			$tagselect .= '
</select>';

			// buttons
			$editTagbut = we_button::create_button("image:btn_direction_right", "javascript:executeEditButton();", true, 100, 22, "", "", false, false, "_applyCode");
			$selectallbut = we_button::create_button("selectAll", "javascript:document.getElementById(\"tag_edit_area\").focus(); document.getElementById(\"tag_edit_area\").select();");
			$prependbut = we_button::create_button("prepend", 'javascript:insertAtStart(document.getElementById("tag_edit_area").value);');
			$appendbut = we_button::create_button("append", 'javascript:insertAtEnd(document.getElementById("tag_edit_area").value);');
			$addCursorPositionbut = we_button::create_button("addCursorPosition", 'javascript:addCursorPosition(document.getElementById("tag_edit_area").value);_EditorFrame.setEditorIsHot(true);');

			$tagWizardHtml = $CodeWizard->getJavascript();
			$tagWizardHtml .= '
		<script type="text/javascript">
			function executeEditButton() {
				if(document.getElementById(\'weTagGroupSelect\').value == \'snippet_custom\') {
					YUIdoAjax(document.getElementById(\'codesnippet_custom\').value);

				} else if(document.getElementById(\'weTagGroupSelect\').value == \'snippet_standard\') {
					YUIdoAjax(document.getElementById(\'codesnippet_standard\').value);

				} else {
					var _sel=document.getElementById(\'tagSelection\');
					if(_sel.selectedIndex > -1) {
						edit_wetag(_sel.value);
					}
				}
		 	}

		 	function openTagWizardPrompt( _wrongTag ) {


		 		var _prompttext = "' . g_l('weTagWizard', '[insert_tagname]') . '";
		 		if ( _wrongTag ) {
		 			_prompttext = "' . sprintf(g_l('weTagWizard', '[insert_tagname_not_exist]'), '\"" + _wrongTag + "\"') . '\n\n" + _prompttext;
		 		}

		 		var _tagName = prompt(_prompttext);
		 		var _tagExists = false;

		 		if ( typeof(_tagName) == "string") {

			 		for ( i=0; i < tagGroups["alltags"].length && !_tagExists; i++ ) {
			 			if ( tagGroups["alltags"][i] == _tagName ) {
			 				_tagExists = true;

			 			}
			 		}

			 		if ( _tagExists ) {
			 			edit_wetag(_tagName, 1);

			 		} else {
			 			openTagWizardPrompt( _tagName );

			 		}
			 	}
		 	}

			function edit_wetag(tagname, insertAtCursor) {
				if (!insertAtCursor) {
					insertAtCursor = 0;
				}
				we_cmd("open_tag_wizzard", tagname, insertAtCursor);

			}

			function insertAtStart(tagText) {
				if (document.weEditorApplet && typeof(document.weEditorApplet.insertAtStart) != "undefined") {
					document.weEditorApplet.insertAtStart(tagText);
				} else if(window.editor && window.editor.frame) {
					window.editor.insertIntoLine(window.editor.firstLine(), 0, tagText + "\n");
				} else {
				 	document.we_form["we_' . $we_doc->Name . '_txt[data]"].value = tagText + "\n" + document.we_form["we_' . $we_doc->Name . '_txt[data]"].value;
				}
				_EditorFrame.setEditorIsHot(true);
			}

			function insertAtEnd(tagText) {
				if (document.weEditorApplet && typeof(document.weEditorApplet.insertAtEnd) != "undefined") {
					document.weEditorApplet.insertAtEnd(tagText);
				} else if(window.editor && window.editor.frame) {
					window.editor.insertIntoLine(window.editor.lastLine(), "end", "\n" + tagText);
				} else {
					document.we_form["we_' . $we_doc->Name . '_txt[data]"].value += "\n" + tagText;
				}
				_EditorFrame.setEditorIsHot(true);

			}

			function addCursorPosition ( tagText ) {

				if (document.weEditorApplet && typeof(document.weEditorApplet.replaceSelection) != "undefined") {
					document.weEditorApplet.replaceSelection(tagText);
				} else if(window.editor && window.editor.frame) {
					window.editor.replaceSelection(tagText);
				} else {

					var weForm = document.we_form["we_' . $we_doc->Name . '_txt[data]"];
					if(document.selection)
					    {
					        weForm.focus();
					        document.selection.createRange().text=tagText;
					        document.selection.createRange().select();
					    }
					else if (weForm.selectionStart || weForm.selectionStart == "0")
						{
							intStart = weForm.selectionStart;
							intEnd = weForm.selectionEnd;
							weForm.value = (weForm.value).substring(0, intStart) + tagText + (weForm.value).substring(intEnd, weForm.value.length);
						    window.setTimeout("scrollToPosition();",50);
							weForm.focus();
						    weForm.selectionStart = eval(intStart+tagText.length);
						    weForm.selectionEnd = eval(intStart+tagText.length);
						}
					else
						{
							weForm.value += tagText;
						}
				}
			}

			function selectTagGroup(groupname) {

				if(groupname == "snippet_custom") {
					document.getElementById(\'codesnippet_standard\').style.display = \'none\';
					document.getElementById(\'tagSelection\').style.display = \'none\';
					document.getElementById(\'codesnippet_custom\').style.display = \'block\';

				} else if(groupname == "snippet_standard") {
					document.getElementById(\'codesnippet_custom\').style.display = \'none\';
					document.getElementById(\'tagSelection\').style.display = \'none\';
					document.getElementById(\'codesnippet_standard\').style.display = \'block\';

				} else if (groupname != "-1") {
					document.getElementById(\'codesnippet_custom\').style.display = \'none\';
					document.getElementById(\'codesnippet_standard\').style.display = \'none\';
					document.getElementById(\'tagSelection\').style.display = \'block\';
					elem = document.getElementById("tagSelection");

					for(var i=(elem.options.length-1); i>=0;i--) {
						elem.options[i] = null;
					}

					for (var i=0; i<tagGroups[groupname].length; i++) {
						elem.options[i] = new Option(tagGroups[groupname][i],tagGroups[groupname][i]);
					}
				}
			}

			' . $groupJs . '
			function openTagWizWithReturn (Ereignis) {
				if (!Ereignis)
				Ereignis = window.event;
				if (Ereignis.which) {
				Tastencode = Ereignis.which;
				} else if (Ereignis.keyCode) {
				Tastencode = Ereignis.keyCode;
				}
				if (Tastencode==13) edit_wetag(document.getElementById("tagSelection").value);
				//return false;
			}
		</script>
		<table id="wizardTable" style="width: 700px;" class="defaultfont" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="right">' . $groupselect . '</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(5, 5) . '</td>
		</tr>
		<tr>
			<td id="tagSelectCol" style="width: 250px;">' . $tagselect . $CodeWizard->getSelect() . $CodeWizard->getSelect('custom') . '</td>
			<td id="spacerCol" style="width: 50px;" align="center">' . $editTagbut . '</td>
			<td id="tagAreaCol" style="width: 100%;" align="right">' . we_html_element::htmlTextArea(array('name' => 'we_' . $we_doc->Name . '_TagWizardCode', 'id' => 'tag_edit_area', 'style' => 'width:400px; height:100px;' . (($_SESSION["prefs"]["editorFont"] == 1) ? " font-family: " . $_SESSION["prefs"]["editorFontname"] . "; font-size: " . $_SESSION["prefs"]["editorFontsize"] . "px;" : ""), 'class' => 'defaultfont'), $we_doc->TagWizardCode) . '</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(5, 5) . '</td>
		</tr>
	</table>
	<table id="wizardTableButtons" class="defaultfont" border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td id="tagSelectColButtons" style="width: 250px;"></td>
			<td id="spacerColButtons" style="width: 50px;"></td>
			<td id="tagAreaColButtons" style="width: 100%;" align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td style="padding-right:10px;">' . $selectallbut . '</td>
					<td style="padding-right:10px;">' . $prependbut . '</td>
					<td style="padding-right:10px;">' . $appendbut . '</td>
					<td>' . $addCursorPositionbut . '</td>
				</table>
			</td>
		</tr>
	</table>';

			array_push($parts, array("headline" => "", "html" => $tagWizardHtml, "space" => 0));
			$wepos = weGetCookieVariable("but_weTMPLDocEdit");
			$znr = 1;
		}
		print we_multiIconBox::getJS();
		print '<div id="bodydiv" style="display:none;">' . we_multiIconBox::getHTML("weTMPLDocEdit", "100%", $parts, 20, "", $znr, g_l('weClass', "[showTagwizard]"), g_l('weClass', "[hideTagwizard]"), ($wepos == "down"), "", 'toggleTagWizard();') . '</div>';
	?></body>

	<?php
	if(isset($selectedGroup)){
		echo "<script type='text/javascript'>
	selectTagGroup('$selectedGroup');
	</script>";
	}
	?>
	</html>
<?php
}