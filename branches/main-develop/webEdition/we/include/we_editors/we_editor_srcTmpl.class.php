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
if(!$GLOBALS['we_editmode']){
	exit();
}
require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');

class we_editor_srcTmpl extends we_editor_base{

	private function we_getCSSIds(){
		$tp = new we_tag_tagParser($this->we_doc->getElement('data'));
		$tags = $tp->getTagsWithAttributes();
		$query = ['document' => [], 'template' => [], 'object' => []];

		foreach($tags as $tag){
			if(isset($tag['attribs']['id']) && intval($tag['attribs']['id'])){
				$type = (isset($tag['attribs']['type']) ? $tag['attribs']['type'] : ($tag['name'] === 'object' ? 'object' : 'document'));
				$query[$type][] = intval($tag['attribs']['id']);
			}
		}
		foreach($query as $type => &$ids){
			if(!$ids){
				continue;
			}
			switch($type){
				default:
				case 'document':
					$table = FILE_TABLE;
					break;
				case 'template':
					$table = TEMPLATES_TABLE;
					break;
				case 'object':
					if(!defined('OBJECT_FILES_TABLE')){
						$ids = [];
						continue;
					}
					$table = OBJECT_FILES_TABLE;
			}
			$GLOBALS['DB_WE']->query('SELECT ID,Path FROM ' . $table . ' WHERE ID IN (' . implode(',', array_unique($ids, SORT_NUMERIC)) . ')');
			$ids = $GLOBALS['DB_WE']->getAllFirst(false);
		}

		$ret = '';
		foreach($query as $type => $docs){
			foreach($docs as $id => $path){
				$ret .= '.cm-we' . $type . 'ID-' . $id . ':hover:after {content: "' . $path . '";}';
			}
		}
		return $ret;
	}

	private function we_getCodeMirror2Code(&$options){
		$useCompletion = false;
		$mode = '';
		switch($this->we_doc->ContentType){ // Depending on content type we use different parsers and css files
			case we_base_ContentTypes::CSS:
				$mode = 'text/css';
				break;
			case we_base_ContentTypes::JS:
				$mode = 'text/javascript';
				break;
			case we_base_ContentTypes::TEMPLATE:
				$mode = we_base_ContentTypes::TEMPLATE;
				$useCompletion = true;
				break;
			case we_base_ContentTypes::HTML:
				$mode = (isset($mode) ? $mode : 'application/x-httpd-php');
				$useCompletion = true;
				break;
			case we_base_ContentTypes::XML:
			case we_base_ContentTypes::HTACCESS:
			default://if we don't know, use XML Mode
				$parser_js[] = 'mode/xml/xml.js';
				$parser_js[] = 'addon/edit/matchbrackets.js';
				$parser_js[] = 'addon/hint/show-hint.js';
				$parser_js[] = 'addon/hint/xml-hint.js';
				$mode = 'application/xml';
				break;
		}

		$tmp = we_unserialize($_SESSION['prefs']['editorCodecompletion']);
		$hasCompletion = is_array($tmp) ? array_sum($tmp) : false;
		$settings = http_build_query(['settings' => is_array($tmp) ? $tmp : []]);

		$maineditor = '';
		$editors = we_base_request::_(we_base_request::JSON, 'activeEditors', []);
		if(true || empty($editors['CodeMirror'])){ //load js only if not present in browser
			$maineditor = we_html_element::jsScript(LIB_DIR . 'additional/CodeMirror/lib/codemirror.js');

			$parser_js = [
				'addon/edit/closetag.js',
				'addon/edit/matchbrackets.js',
				'addon/edit/trailingspace.js',
				'addon/fold/brace-fold.js',
				'addon/fold/comment-fold.js',
				'addon/fold/foldcode.js',
				'addon/fold/foldgutter.js',
				'addon/fold/markdown-fold.js',
				'addon/fold/xml-fold.js',
				/* 			'addon/hint/css-hint.js',
				  'addon/hint/html-hint.js',
				  'addon/hint/javascript-hint.js', */
				'addon/hint/show-hint.js',
				'addon/hint/xml-hint.js',
				'addon/mode/overlay.js',
				'addon/search/searchcursor.js',
				'mode/clike/clike.js',
				'mode/css/css.js',
				'mode/htmlmixed/htmlmixed.js',
				'mode/javascript/javascript.js',
				'mode/php/php.js',
				'mode/sass/sass.js',
				'mode/xml/xml.js',
			];

			foreach($parser_js as $js){
				$maineditor .= we_html_element::jsScript(LIB_DIR . 'additional/CodeMirror/' . $js);
			}
			$maineditor .= we_html_element::jsScript(WEBEDITION_DIR . 'editors/template/CodeMirror/mode/webEdition/webEdition.js') .
				we_html_element::jsScript(WEBEDITION_DIR . 'editors/template/CodeMirror/addon/show-invisibles.js') .
				we_html_element::jsScript(WEBEDITION_DIR . 'editors/template/CodeMirror/addon/we-hint.js') .
				we_html_element::jsScript(WEBEDITION_DIR . 'editors/template/CodeMirror/mode/webEdition/cmTags_js.php?' . $settings);
		}


		// CodeMirror will be used; we add css to the page
		$maineditor .= we_html_element::cssLink(LIB_DIR . 'additional/CodeMirror/lib/codemirror.css') .
			we_html_element::cssLink(LIB_DIR . 'additional/CodeMirror/theme/' . $_SESSION['prefs']['editorTheme'] . '.css') .
			we_html_element::cssLink(LIB_DIR . 'additional/CodeMirror/addon/fold/foldgutter.css') .
			we_html_element::cssLink(LIB_DIR . 'additional/CodeMirror/addon/hint/show-hint.css') .
			we_html_element::cssLink(WEBEDITION_DIR . 'editors/template/CodeMirror/mode/webEdition/webEdition.css');

		$options = [//these are the CodeMirror options
			'mode' => $mode,
			'electricChars' => false,
			'theme' => $_SESSION['prefs']['editorTheme'],
			'lineNumbers' => ($_SESSION['prefs']['editorLinenumbers'] ? true : false),
			'gutter' => true,
			'foldGutter' => true,
			'minFoldSize' => 5,
			'gutters' => ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
			'indentWithTabs' => !$_SESSION['prefs']['editorIndentSpaces'],
			'tabSize' => intval($_SESSION['prefs']['editorTabSize']),
			'indentUnit' => intval($_SESSION['prefs']['editorTabSize']),
			'matchBrackets' => true,
			/* 	workTime: 300,
			  workDelay: 800, */
			'dragDrop' => false,
			'height' => intval(($_SESSION['prefs']['editorHeight'] != 0) ? $_SESSION['prefs']['editorHeight'] : 320),
			'lineWrapping' => ($_SESSION['weS']['we_wrapcheck'] ? true : false),
			'autoCloseTags' => ($_SESSION['prefs']['editorDocuintegration'] ? true : false), // use object with indentTags to indent these tags
			'autofocus' => true,
			'smartIndent' => ($_SESSION['prefs']['editorAutoIndent'] ? true : false/* '"Enter": false,' */),
			'closeCharacters' => '()[]{};>,',
			'hasCodeCompletion' => ($hasCompletion && $useCompletion),
			'showTrailingSpace' => $_SESSION['prefs']['editorShowSpaces'],
			'showInvisibles' => $_SESSION['prefs']['editorShowSpaces'],
		];

		$maineditor .= //($hasCompletion && $useCompletion ?		 :		''		) .
			//($_SESSION['prefs']['editorShowSpaces'] ? '' : '') .
			($_SESSION['prefs']['editorTooltips'] ? we_html_element::cssLink(WEBEDITION_DIR . 'editors/template/CodeMirror/mode/webEdition/cmTags_css.php?' . $settings) : '' ) .
			we_html_element::cssElement(
				($this->we_doc->ContentType == we_base_ContentTypes::TEMPLATE && $_SESSION['prefs']['editorTooltipsIDs'] ?
				$this->we_getCSSIds() : '') . '
.weSelfClose:hover:after,
.cm-weSelfClose:hover:after,
.weOpenTag:hover:after,
.cm-weOpenTag:hover:after,
.weTagAttribute:hover:after,
.cm-weTagAttribute:hover:after {
	font-family: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontname'] ? $_SESSION['prefs']['editorTooltipFontname'] : 'sans-serif') . ';
	font-size: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontsize'] ? $_SESSION['prefs']['editorTooltipFontsize'] : '12') . 'px;
}

.CodeMirror,
.CodeMirror.cm-s-ambiance,
.CodeMirror.cm-s-solarized {
	font-family: ' . ($_SESSION['prefs']['editorFont'] && $_SESSION['prefs']['editorFontname'] ? $_SESSION['prefs']['editorFontname'] : 'monospace') . ' !important;
	font-size: ' . ($_SESSION['prefs']['editorFont'] && $_SESSION['prefs']['editorFontsize'] ? $_SESSION['prefs']['editorFontsize'] : '12') . 'px !important;
}

' . ($_SESSION['prefs']['editorShowTab'] ? '' : '
.cm-tab {
background: none;
}')
		);

		return $maineditor;
	}

	private function getInitEditor(){
		switch($_SESSION['prefs']['editorMode']){
			case 'java':
			case 'codemirror2':
				return'initCM();';
			default:
				return 'initDefaultEdior();';
		}
	}

	private function getTagWizzard(){
		// Code Wizard
		$allWeTags = we_wizard_tag::getExistingWeTags();

		$tagGroups = we_wizard_tag::getWeTagGroups($allWeTags);

		$selectedGroup = !empty($this->we_doc->TagWizardSelection) ? $this->we_doc->TagWizardSelection : "alltags";
		$groupselect = '<select class="weSelect" style="width: 250px;" id="weTagGroupSelect" name="we_' . $this->we_doc->Name . '_TagWizardSelection" onchange="selectTagGroup(this.value);">
<optgroup label="' . g_l('weCodeWizard', '[snippets]') . '">
<option value="snippet_standard" ' . ($selectedGroup === 'snippet_standard' ? 'selected' : '') . '>' . g_l('weCodeWizard', '[standard_snippets]') . '</option>
		<option value="snippet_custom" ' . ($selectedGroup === 'snippet_custom' ? 'selected' : '') . '>' . g_l('weCodeWizard', '[custom_snippets]') . '</option>
		</optgroup>
		<optgroup label="we:tags">';

		foreach(array_keys($tagGroups) as $tagGroupName){
			switch($tagGroupName){
				case 'custom_tags':
					$tagGroupName = 'custom';
				case 'custom':
					$groupselect .= '<option value="-1" disabled="disabled">----------</option>';
			}
			$groupselect .= '<option value="' . $tagGroupName . '"' . ($tagGroupName == $selectedGroup ? ' selected="selected"' : '') . '">' . (we_base_moduleInfo::isActive($tagGroupName) ? g_l('javaMenu_moduleInformation', '[' . $tagGroupName . '][text]') : g_l('weTagGroups', '[' . $tagGroupName . ']')) . '</option>';
			if($tagGroupName === 'alltags'){
				$groupselect .= '<option value="-1" disabled="disabled">----------</option>';
			}
		}
		$groupselect .= '</optgroup></select>';

		$tagselect = '<select onkeydown="return openTagWizWithReturn(event?event:window.event)" class="defaultfont" style="width: 250px; height: 100px;" size="7" ondblclick="edit_wetag(this.value);" name="tagSelection" id="tagSelection" onchange="WE().layout.button.enable(document, \'btn_direction_right_applyCode\')">';

		for($i = 0; $i < count($allWeTags); $i++){
			$tagselect .= '
	<option value="' . $allWeTags[$i] . '">' . $allWeTags[$i] . '</option>';
		}

		$tagselect .= '</select>';

		// buttons
		$editTagbut = we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:executeEditButton();", '', 0, 0, "", "", false, false, "_applyCode");
		/*
		  $selectallbut = we_html_button::create_button("selectAll", "javascript:document.getElementById(\"tag_edit_area\").focus(); document.getElementById(\"tag_edit_area\").select();");
		  $prependbut = we_html_button::create_button("prepend", 'javascript:insertAtStart(document.getElementById("tag_edit_area").value);');
		  $appendbut = we_html_button::create_button("append", 'javascript:insertAtEnd(document.getElementById("tag_edit_area").value);');
		  $addCursorPositionbut = we_html_button::create_button("addCursorPosition", 'javascript:addCursorPosition(document.getElementById("tag_edit_area").value);_EditorFrame.setEditorIsHot(true);');
		 */
		$tagWizardHtml = we_wizard_code::getJavascript() . '
<table id="wizardTable" style="width:700px;" class="default defaultfont">
	<tr><td style="padding-bottom:5px;">' . $groupselect . '</td></tr>
	<tr>
		<td id="tagSelectCol" style="width: 250px;">' . $tagselect . we_wizard_code::getSelect() . we_wizard_code::getSelect('custom') . '</td>
		<td id="spacerCol" style="width: 50px;text-align:center">' . $editTagbut . '</td>
		<td id="tagAreaCol" style="width: 100%;text-align:right">' . we_html_element::htmlTextArea(['name' => 'we_' . $this->we_doc->Name . '_TagWizardCode',
				'id' => 'tag_edit_area',
				'style' => 'width:400px; height:100px;' . (($_SESSION["prefs"]["editorFont"] == 1) ? " font-family: " . $_SESSION["prefs"]["editorFontname"] . "; font-size: " . $_SESSION["prefs"]["editorFontsize"] . "px;" : ""),
				'class' => 'defaultfont'
				], $this->we_doc->TagWizardCode) . '</td>
	</tr>
</table>
';
		/* <table id="wizardTableButtons" class="default defaultfont">
		  <tr>
		  <td id="tagSelectColButtons" style="width: 250px;"></td>
		  <td id="spacerColButtons" style="width: 50px;"></td>
		  <td id="tagAreaColButtons" style="width: 100%;text-align:right">
		  <table class="default">
		  <tr>
		  <td style="padding-right:10px;">' . $selectallbut . '</td>
		  <td style="padding-right:10px;">' . $prependbut . '</td>
		  <td style="padding-right:10px;">' . $appendbut . '</td>
		  <td>' . $addCursorPositionbut . '</td>
		  </table>
		  </td>
		  </tr>
		  </table> */
		return
			[$selectedGroup,
				[
					[],
					["headline" => "", "html" => $tagWizardHtml,]
				]
		];
	}

	public function show(){
		if(isset($this->we_doc->elements["Charset"]["dat"])){ //	send charset which might be determined in template
			$this->charset = $this->we_doc->elements["Charset"]["dat"];
		}

		if(!isset($_SESSION['weS']['we_wrapcheck'])){
			$_SESSION['weS']['we_wrapcheck'] = $_SESSION['prefs']['editorWrap'];
		}


		$code = ($this->we_doc instanceof we_htmlDocument ?
			$this->we_doc->getDocumentCode() :
			$this->we_doc->getElement('data')
			);

		$maineditor = '<textarea id="editarea" style="' . (($_SESSION["prefs"]["editorFont"] == 1) ? ' font-family: ' . $_SESSION['prefs']['editorFontname'] . '; font-size: ' . $_SESSION['prefs']['editorFontsize'] . 'px;' : '') .
			'-moz-tab-size:' . $_SESSION['prefs']['editorTabSize'] . '; -o-tab-size:' . $_SESSION['prefs']['editorTabSize'] . '; -webkit-tab-size:' . $_SESSION['prefs']['editorTabSize'] . '; tab-size:' . $_SESSION['prefs']['editorTabSize'] . ';' .
			'" name="we_' . $this->we_doc->Name . '_txt[data]" wrap="' . ($_SESSION['weS']['we_wrapcheck'] ? 'virtual' : 'off') . '" ' .
			((!we_base_browserDetect::isGecko() && !$_SESSION['weS']['we_wrapcheck']) ? '' : '') . ($_SESSION['prefs']['editorMode'] === 'codemirror2' ? '' : (we_base_browserDetect::isIE() || we_base_browserDetect::isOpera() ? 'onkeydown' : 'onkeypress') . '="editorChanged();return wedoKeyDown(this,event);"') . '>'
			. oldHtmlspecialchars($code) . '</textarea>';
		$options = [];
		switch($_SESSION['prefs']['editorMode']){
			case 'java':
			case 'codemirror2': //Syntax-Highlighting
				$maineditor .= $this->we_getCodeMirror2Code($options);
				break;
		}

		$znr = -1;
		$wepos = "";

		if($this->we_doc->ContentType == we_base_ContentTypes::TEMPLATE){
			list($selectedGroup, $parts ) = $this->getTagWizzard();
			$wepos = weGetCookieVariable("but_weTMPLDocEdit");
			$znr = 1;
		}

		return $this->getPage('<div id="bodydiv" style="display:none;position:absolute;top:10px;left:0px;right:0px;bottom:0px;">
<div id="editorDiv" style="margin-left: 20px;margin-right: 20px;">' .
				$maineditor . '
	<table class="default" id="srtable">
	<tr>
		<td style="text-align:left" class="defaultfont">' .
				($_SESSION['prefs']['editorMode'] === 'codemirror2' ? '
	<input type="text" style="width: 10em;float:left;" id="query" onkeydown="cmSearch(event);"/><div style="float:left;">' . we_html_button::create_button(we_html_button::SEARCH, 'javascript:cmSearch(null);') . '</div>
	<input type="text" style="margin-left:2em;width: 10em;float:left;" id="replace" onkeydown="cmReplace(event);"/><div style="float:left;">' . we_html_button::create_button('replace', 'javascript:cmReplace(null);') . '</div>' .
				we_html_forms::checkbox(1, 0, 'caseSens', g_l('weClass', '[caseSensitive]'), false, "defaultfont", '', false, '', 0, 0, '', 'display:inline-block;margin-left:2em;') .
				'</div>' : ''
				) . '
						</td>
						<td style="text-align:right" class="defaultfont">' .
				we_html_forms::checkbox(1, ($_SESSION['weS']['we_wrapcheck'] == 1), 'we_wrapcheck_tmp', g_l('global', '[wrapcheck]'), false, "defaultfont", ($_SESSION['prefs']['editorMode'] === 'codemirror2' ? 'editor.setOption(\'lineWrapping\',this.checked);' : "we_cmd('wrap_on_off',this.checked)"), false, '', 0, 0, '', 'display:inline-block;') .
				($_SESSION['prefs']['editorMode'] === 'codemirror2' ? '<div id="reindentButton" style="display:inline-block;margin-left:10px;margin-top:-3px;">' . we_html_button::create_button('fa:reindent,fa-lg fa-indent', 'javascript:reindent();') . '</div>' : '') .
				'</td></tr></table>
</div>' .
				(isset($parts) ? we_html_multiIconBox::getHTML("weTMPLDocEdit", $parts, 20, "", $znr, g_l('weClass', '[showTagwizard]'), g_l('weClass', '[hideTagwizard]'), ($wepos === 'down'), '', 'sizeEditor();') : '') . '</div>', we_html_element::jsScript(JS_DIR . 'we_srcTmpl.js', '', [
					'id' => 'loadVarSrcTmpl', 'data-doc' => setDynamicVar([
						'docName' => $this->we_doc->Name,
						'docCharSet' => ($this->we_doc->elements['Charset']['dat'] ?: $GLOBALS['WE_BACKENDCHARSET']),
						'editorHighlightCurrentLine' => intval($_SESSION['prefs']['editorHighlightCurrentLine']),
						'CMOptions' => $options,
				])]), [
				'style' => "overflow:hidden;",
				'onload' => (isset($selectedGroup) ? "selectTagGroup('" . $selectedGroup . "');" : '') . $this->getInitEditor(),
				'onunload' => "doUnload();parent.editorScrollPosTop = getScrollPosTop();parent.editorScrollPosLeft = getScrollPosLeft();",
				'onresize' => "sizeEditor();"
		]);
	}

}
