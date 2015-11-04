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
class we_glossary_frameEditorItem extends we_glossary_frameEditor{

	function Header($weGlossaryFrames){

		$we_tabs = new we_tabs();
		$we_tabs->addTab(new we_tab(g_l('modules_glossary', '[property]'), we_tab::ACTIVE, "setTab('1');"));

		switch($weGlossaryFrames->View->Glossary->Type){
			case we_glossary_glossary::TYPE_ABBREVATION:
				$title = g_l('modules_glossary', '[abbreviation]');
				break;
			case we_glossary_glossary::TYPE_ACRONYM:
				$title = g_l('modules_glossary', '[acronym]');
				break;
			case we_glossary_glossary::TYPE_FOREIGNWORD:
				$title = g_l('modules_glossary', '[foreignword]');
				break;
			case we_glossary_glossary::TYPE_LINK:
				$title = g_l('modules_glossary', '[link]');
				break;
			case we_glossary_glossary::TYPE_TEXTREPLACE:
				$title = g_l('modules_glossary', '[textreplacement]');
				break;
		}

		return self::buildHeader($weGlossaryFrames, $we_tabs, $title, ($weGlossaryFrames->View->Glossary->ID ? oldHtmlspecialchars($weGlossaryFrames->View->Glossary->Text) : g_l('modules_glossary', '[menu_new]')) . '<div id="mark" style="display: none;">*</div>');
	}

	function Body($weGlossaryFrames){
		$tabNr = we_base_request::_(we_base_request::INT, 'tabnr', 1);
		$tabNr = ($weGlossaryFrames->View->Glossary->IsFolder && $tabNr != 1) ? 1 : $tabNr;
		$yuiSuggest = &weSuggest::getInstance();

		$out = weSuggest::getYuiFiles() .
			we_html_element::jsElement('
var table = "' . GLOSSARY_TABLE . '";

function toggle(id){
	var elem = document.getElementById(id);
	if(elem.style.display == "none") elem.style.display = "block";
	else elem.style.display = "none";
}

function setVisible(id,visible){
	var elem = document.getElementById(id);
	if(visible==true) elem.style.display = "block";
	else elem.style.display = "none";
}

function showType(type) {
	document.getElementById("type_abbreviation").style.display = "none";
	document.getElementById("type_acronym").style.display = "none";
	document.getElementById("type_foreignword").style.display = "none";
	document.getElementById("type_link").style.display = "none";
	document.getElementById("type_textreplacement").style.display = "none";
	document.getElementById("type_" + type).style.display = "block";
	document.we_form.cmd.value = "edit_" + type;
	if(type == "link") {
		document.getElementsByClassName("btn_direction_weMultibox_table")[0].style.display = "inline";
		document.getElementById("text_weMultibox").style.display = "inline";
		document.getElementById("div_weMultibox_2").style.display = "block";
		document.getElementById("div_weMultibox_3").style.display = "block";
		document.getElementById("div_weMultibox_4").style.display = "block";
		document.getElementById("div_weMultibox_5").style.display = "block";
		document.getElementById("div_weMultibox_6").style.display = "block";
		document.getElementById("div_weMultibox_7").style.display = "block";
		showLinkMode("intern");
	} else {
		document.getElementsByClassName("btn_direction_weMultibox_table")[0].style.display = "none";
		document.getElementById("text_weMultibox").style.display = "none";
		document.getElementById("div_weMultibox_2").style.display = "none";
		document.getElementById("div_weMultibox_3").style.display = "none";
		document.getElementById("div_weMultibox_4").style.display = "none";
		document.getElementById("div_weMultibox_5").style.display = "none";
		document.getElementById("div_weMultibox_6").style.display = "none";
		document.getElementById("div_weMultibox_7").style.display = "none";
	}
}

function showLinkMode(mode) {
	document.getElementById("mode_intern").style.display = "none";
	document.getElementById("mode_extern").style.display = "none";
	document.getElementById("mode_object").style.display = "none";
	document.getElementById("mode_category").style.display = "none";
	document.getElementById("mode_" + mode).style.display = "block";
	if(mode == "category") {
		showLinkModeCategory("intern");
	}
}

function showLinkModeCategory(mode) {
	document.getElementById("mode_category_intern").style.display = "none";
	document.getElementById("mode_category_extern").style.display = "none";
	document.getElementById("mode_category_" + mode).style.display = "block";
}

function setHot() {
	' . $weGlossaryFrames->topFrame . '.editor.edheader.document.getElementById("mark").style.display = "inline";
	top.hot=1;
}

function setDisplay(id, display) {
	document.getElementById(id).style.display = display;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var arguments = args;

	switch (args[0]) {
		case "populateWorkspaces":
			document.we_form.cmd.value=args[0];
			document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
			document.we_form.pnt.value="cmd";
			submitForm("cmd");
			break;
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(this, url,"we_docselector",-1,-1,WE().consts.size.docSelect.width,WE().consts.size.docSelect.height,true,true,true,true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(this, url,"we_selector",-1,-1,WE().consts.size.windowSelect.width,WE().consts.size.windowSelect.height,true,true,true,true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(this, url,"we_selector",-1,-1,WE().consts.size.windowDirSelect.width,WE().consts.size.windowDirSelect.height,true,true,true,true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(this, url,"we_catselector",-1,-1,WE().consts.size.catSelect.width,WE().consts.size.catSelect.height,true,true,true,true);
			break;
		default:
			' . $this->topFrame . '.we_cmd.apply(this, arguments);

	}
}

function submitForm(target,action,method) {
	var f = self.document.we_form;

	f.target = (target?target:"edbody");
	f.action = (action?action:"' . $weGlossaryFrames->frameset . '");
	f.method = (method?method:"post");
	f.submit();
}' .
				$weGlossaryFrames->topFrame . '.editor.edheader.location="' . $weGlossaryFrames->frameset . '&pnt=edheader";' .
				$weGlossaryFrames->topFrame . '.editor.edfooter.location="' . $weGlossaryFrames->frameset . '&pnt=edfooter"') .
			we_html_multiIconBox::getJs() .
			we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ($tabNr == 1 ? '' : 'display: none')), we_html_multiIconBox::getHTML('weMultibox', self::getHTMLTabProperties($weGlossaryFrames->View->Glossary), 30, '', 2, g_l('modules_glossary', '[show_extended_linkoptions]'), g_l('modules_glossary', '[hide_extended_linkoptions]'), false)) .
			we_html_element::jsElement(
				'showType("' . $weGlossaryFrames->View->Glossary->Type . '");' .
				($weGlossaryFrames->View->Glossary->Type === "link" ?
					'showLinkMode("' . ($weGlossaryFrames->View->Glossary->getAttribute('mode') ? : "intern") . '");' :
					'') .
				($weGlossaryFrames->View->Glossary->getAttribute('mode') === "category" ?
					'showLinkModeCategory("' . ($weGlossaryFrames->View->Glossary->getAttribute('modeCategory') ? : "intern") . '");' :
					'')
			) . $yuiSuggest->getYuiJs();


		return self::buildBody($weGlossaryFrames, $out);
	}

	function Footer($weGlossaryFrames){
		$SaveButton = we_html_button::create_button(we_html_button::SAVE, "javascript:if(top.publishWhenSave==1){top.content.editor.edbody.document.getElementById('Published').value=1;};we_save();", true, 100, 22, '', '', (!permissionhandler::hasPerm('NEW_GLOSSARY') && !permissionhandler::hasPerm('EDIT_GLOSSARY')));
		$UnpublishButton = we_html_button::create_button("deactivate", "javascript:top.content.editor.edbody.document.getElementById('Published').value=0;top.opener.top.we_cmd('save_glossary')", true, 100, 22, '', '', (!permissionhandler::hasPerm('NEW_GLOSSARY') && !permissionhandler::hasPerm('EDIT_GLOSSARY')));

		$NewEntry = we_html_forms::checkbox(1, false, "makeNewEntry", g_l('modules_glossary', '[new_item_after_saving]'), false, "defaultfont", "top.makeNewEntryCheck = (this.checked) ? 1 : 0", false);
		$PublishWhenSaved = we_html_forms::checkbox(1, false, "publishWhenSave", g_l('modules_glossary', '[publish_when_saved]'), false, "defaultfont", "top.publishWhenSave = (this.checked) ? 1 : 0", false);

		$ShowUnpublish = $weGlossaryFrames->View->Glossary->ID == 0 ? true : ($weGlossaryFrames->View->Glossary->Published > 0 ? true : false);

		$col = 0;
		$table2 = new we_html_table(array('class' => 'default'), 1, 6);
		$table2->setRow(0, array('style' => 'vertical-align:middle;'));
		if($ShowUnpublish){
			$table2->setCol(0, $col++, array("nowrap" => null), $UnpublishButton);
		}
		$table2->setCol(0, $col++, array("nowrap" => null), $SaveButton);
		if(!$ShowUnpublish){
			$table2->setCol(0, $col++, array("nowrap" => null), $PublishWhenSaved);
		}
		$table2->setCol(0, $col++, array("nowrap" => null), $NewEntry);

		$js = we_html_element::jsElement('
if(top.makeNewEntryCheck==1) {
	document.getElementById("makeNewEntry").checked = true;
}
if(top.publishWhenSave==1 && document.getElementById("publishWhenSave")) {
	document.getElementById("publishWhenSave").checked = true;
}
function we_save() {
	top.content.we_cmd("save_glossary");
}');

		$form = we_html_element::htmlForm(array(), $table2->getHtml() . $js);

		return self::buildFooter($weGlossaryFrames, $form);
	}

	function getHTMLTabProperties(we_glossary_glossary $glossary){
		$_types = array(
			we_glossary_glossary::TYPE_ACRONYM => g_l('modules_glossary', '[acronym]'),
			we_glossary_glossary::TYPE_ABBREVATION => g_l('modules_glossary', '[abbreviation]'),
			we_glossary_glossary::TYPE_FOREIGNWORD => g_l('modules_glossary', '[foreignword]'),
			we_glossary_glossary::TYPE_LINK => g_l('modules_glossary', '[link]'),
			we_glossary_glossary::TYPE_TEXTREPLACE => g_l('modules_glossary', '[textreplacement]'),
		);

		$hidden = we_html_element::htmlHidden('newone', ($glossary->ID == 0 ? 1 : 0)) .
			we_html_element::htmlHidden('Published', $glossary->ID == 0 ? 1 : ($glossary->Published > 0 ? 1 : 0), 'Published');


		$language = ($glossary->Language ? : $GLOBALS['weDefaultFrontendLanguage']);

		$content = $hidden . '<table class="default">
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[folder]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlSelect("Language", getWeFrontendLanguagesForBackend(), 1, $language, false, array("onchange" => "top.content.setHot();"), "value", 520) . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[type]') . '</td></tr>
	<tr><td>' . we_html_tools::htmlSelect("Type", $_types, 1, $glossary->Type, false, array("onchange" => "top.content.setHot();showType(this.value);"), "value", 520) . '</td></tr>
	<tr><td class="defaultfont">' . we_html_forms::checkboxWithHidden((bool) $glossary->Fullword, 'Fullword', g_l('modules_glossary', '[Fullword]'), false, 'defaultfont', 'top.content.setHot();') . '</td></tr>
</table>';
		$parts = array(
			array(
				"headline" => g_l('modules_glossary', '[path]'),
				"html" => $content,
				"space" => 120
			),
			array(
				"headline" => g_l('modules_glossary', '[selection]'),
				"html" => self::getHTMLAbbreviation($glossary) .
				self::getHTMLAcronym($glossary) .
				self::getHTMLForeignWord($glossary) .
				self::getHTMLLink($glossary) .
				self::getHTMLTextReplacement($glossary),
				"space" => 120,
				'noline' => 1,
			)
		);

		return array_merge($parts, self::getHTMLLinkAttributes($glossary));
	}

	function getHTMLAbbreviation(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_ABBREVATION){
			$text = html_entity_decode($glossary->Text);
			$title = html_entity_decode($glossary->Title);
			$language = $glossary->getAttribute('lang');
		} else {
			$text = $title = $language = "";
		}


		return '<div id="type_abbreviation" style="display: block;">'
			. '<table class="default">
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[abbreviation]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("abbreviation[Text]", 24, $text, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[announced_word]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("abbreviation[Title]", 24, $title, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td>' . self::getLangField("abbreviation[Attributes][lang]", $language, g_l('modules_glossary', '[language]'), 520) . '</td></tr>
</table>
</div>';
	}

	function getHTMLAcronym(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_ACRONYM){
			$_text = html_entity_decode($glossary->Text);
			$_title = html_entity_decode($glossary->Title);
			$_language = $glossary->getAttribute('lang');
		} else {
			$_text = $_title = $_language = "";
		}

		return '<div id="type_acronym" style="display: none;">
<table class="default">
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[acronym]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("acronym[Text]", 24, $_text, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[announced_word]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("acronym[Title]", 24, $_title, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td>' . self::getLangField("acronym[Attributes][lang]", $_language, g_l('modules_glossary', '[language]'), 520) . '</td></tr>
</table>
</div>';
	}

	function getHTMLForeignWord(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_FOREIGNWORD){
			$_text = html_entity_decode($glossary->Text);
			$_language = $glossary->getAttribute('lang');
		} else {
			$_text = $_language = "";
		}

		return '<div id="type_foreignword" style="display: none;"><table class="default">
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[foreignword]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("foreignword[Text]", 24, $_text, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td>' . self::getLangField("foreignword[Attributes][lang]", $_language, g_l('modules_glossary', '[language]'), 520) . '</td></tr>
</table></div>';
	}

	function getHTMLTextReplacement(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_TEXTREPLACE){
			$_text = html_entity_decode($glossary->Text, null, $GLOBALS["WE_BACKENDCHARSET"]);
			$_title = html_entity_decode($glossary->Title, null, $GLOBALS["WE_BACKENDCHARSET"]);
		} else {
			$_title = $_text = "";
		}

		return '<div id="type_textreplacement" style="display: none;"><table class="default">
<tr><td class="defaultfont">' . g_l('modules_glossary', '[textreplacement]') . '</td></tr>
<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("textreplacement[Text]", 24, $_text, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
<tr><td class="defaultfont">' . g_l('modules_glossary', '[textreplacement_Text]') . '</td></tr>
<tr><td>' . we_html_tools::htmlTextInput("textreplacement[Title]", 24, $_title, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
</table></div>';
	}

	function getHTMLLink(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_LINK){
			$_text = html_entity_decode($glossary->Text);
			$_mode = $glossary->getAttribute('mode');
		} else {
			$_text = $_mode = "";
		}

		return
			'<div id="type_link" style="display: none;">
<table class="default">
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[link]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("link[Text]", 24, $_text, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlSelect("link[Attributes][mode]", array(
				'intern' => g_l('modules_glossary', '[link_intern]'),
				'extern' => g_l('modules_glossary', '[link_extern]'),
				'object' => g_l('modules_glossary', '[link_object]'),
				'category' => g_l('modules_glossary', '[link_category]'),
				), 1, $_mode, false, array("onchange" => "setHot();showLinkMode(this.value);"), "value", 520) . '</td></tr>
</table>' .
			self::getHTMLIntern($glossary) .
			self::getHTMLExtern($glossary) .
			self::getHTMLObject($glossary) .
			self::getHTMLCategory($glossary) .
			'</div>';
	}

	function getHTMLIntern(we_glossary_glossary $glossary){
		$_rootDirID = 0;
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['link[Attributes][InternLinkID]'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['link[Attributes][InternLinkPath]'].value");
		$_cmd = "javascript:we_cmd('we_selector_document',document.we_form.elements['link[Attributes][InternLinkID]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $_rootDirID . "')";
		$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 100, 22, '', '', false);

		if($glossary->Type === "link" && $glossary->getAttribute('mode') === "intern"){
			//$_linkPath = $glossary->getAttribute('InternLinkPath');
			$_linkID = $glossary->getAttribute('InternLinkID');
			$_linkPath = id_to_path($_linkID);
			$glossary->setAttribute('InternLinkPath', $_linkPath);
			$_internParameter = $glossary->getAttribute('InternParameter');
		} else {
			$_linkPath = $_linkID = $_internParameter = "";
		}
		$yuiSuggest = &weSuggest::getInstance();
		$yuiSuggest->setAcId('docPath');
		$yuiSuggest->setContentType(implode(',', array(we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::JS, we_base_ContentTypes::CSS, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::QUICKTIME)));
		$yuiSuggest->setInput('link[Attributes][InternLinkPath]', $_linkPath);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setSelectButton($_button);
		$yuiSuggest->setResult('link[Attributes][InternLinkID]', $_linkID);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable(FILE_TABLE);
		$yuiSuggest->setWidth(400);

		return '<div id="mode_intern" style="display: none;">'
			. '<table class="default">
	<tr><td style="padding:2px 0px;">' . $yuiSuggest->getHTML() . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[parameter]') . '</td></tr>
	<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][InternParameter]', 58, $_internParameter, '', 'onchange="setHot();"', 'text', 520, 0) . '</td></tr>
</table></div>';
	}

	function getHTMLExtern(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_LINK && $glossary->getAttribute('mode') === "extern"){
			$_url = $glossary->getAttribute('ExternUrl');
			$_parameter = $glossary->getAttribute('ExternParameter');
		} else {
			$_url = we_base_link::EMPTY_EXT;
			$_parameter = "";
		}

		return '<div id="mode_extern" style="display: none;">
	<table class="default">
		<tr><td style="padding:2px 0px;">' . we_html_tools::htmlTextInput('link[Attributes][ExternUrl]', 58, $_url, '', 'onchange="setHot();"', 'text', 520) . '</td></tr>
		<tr><td class="defaultfont">' . g_l('modules_glossary', '[parameter]') . '</td></tr>
		<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][ExternParameter]', 58, $_parameter, '', 'onchange="setHot();"', 'text', 520, 0) . '</td></tr>
	</table>
</div>';
	}

	function getHTMLObject(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_LINK && $glossary->getAttribute('mode') === "object"){
			$_linkPath = $glossary->getAttribute('ObjectLinkPath');
			$_linkID = $glossary->getAttribute('ObjectLinkID');
			$_workspaceID = $glossary->getAttribute('ObjectWorkspaceID');
			$_parameter = $glossary->getAttribute('ObjectParameter');
		} else {
			$_linkPath = $_linkID = $_workspaceID = $_parameter = "";
		}

		$_rootDirID = 0;
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['link[Attributes][ObjectLinkID]'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['link[Attributes][ObjectLinkPath]'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.we_cmd('populateWorkspaces');");
		$_cmd = defined('OBJECT_TABLE') ? "javascript:we_cmd('we_selector_document',document.we_form.elements['link[Attributes][ObjectLinkID]'].value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $_rootDirID . "','objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")" : '';
		$_button = we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 100, 22, '', '', false);

		$yuiSuggest = &weSuggest::getInstance();
		$yuiSuggest->setAcId('objPathLink');
		$yuiSuggest->setContentType("folder," . we_base_ContentTypes::OBJECT_FILE);
		$yuiSuggest->setInput('link[Attributes][ObjectLinkPath]', $_linkPath);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setSelectButton($_button);
		$yuiSuggest->setResult('link[Attributes][ObjectLinkID]', $_linkID);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable(OBJECT_FILES_TABLE);
		$yuiSuggest->setWidth(400);

		$_wsid = ($this->View->Glossary->getAttribute('ObjectLinkID') ? we_navigation_dynList::getWorkspacesForObject($this->View->Glossary->getAttribute('ObjectLinkID')) : array());

		return '<div id="mode_object" style="display: none;">
	<table class="default">
			<tr><td style="padding:2px 0px;">' . $yuiSuggest->getHTML() . '</td></tr>
	</table>
	<div id="ObjectWorkspaceID" style="display: block;">
		<table class="default">
			<tr><td class="defaultfont">' . g_l('modules_glossary', '[workspace]') . '</td></tr>
			<tr><td>' . we_html_tools::htmlSelect('link[Attributes][ObjectWorkspaceID]', $_wsid, 0, $_workspaceID, false, array('style' => "width:520px; border: #AAAAAA solid 1px;", 'onchange' => "setHot();"), 'value') . '</td></tr>
		</table>
	</div>
	<table class="default">
		<tr><td class="defaultfont" style="padding-top:2px;">' . g_l('modules_glossary', '[parameter]') . '</td></tr>
		<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][ObjectParameter]', 58, $_parameter, '', 'onchange="setHot();"', 'text', 520, 0) . '</td></tr>
	</table></div>';
	}

	function getHTMLCategory(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_LINK && $glossary->getAttribute('mode') === "category"){
			$_linkPath = $glossary->getAttribute('CategoryLinkPath');
			$_linkID = $glossary->getAttribute('CategoryLinkID');
			$_internLinkPath = $glossary->getAttribute('CategoryInternLinkPath');
			$_internLinkID = $glossary->getAttribute('CategoryInternLinkID');
			$_modeCategory = $glossary->getAttribute('modeCategory');
			$_url = $glossary->getAttribute('CategoryUrl');
			$_catParameter = $glossary->getAttribute('CategoryCatParameter');
			$_parameter = $glossary->getAttribute('CategoryParameter');
		} else {
			$_linkPath = "/";
			$_linkID = 0;
			$_internLinkPath = "";
			$_internLinkID = "";
			$_modeCategory = 0;
			$_url = "http://";
			$_catParameter = "";
			$_parameter = "";
		}


		$_rootDirID = 0;
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['link[Attributes][CategoryLinkID]'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['link[Attributes][CategoryLinkPath]'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.setHot();");

		$_cmd = "javascript:we_cmd('we_selector_category',document.we_form.elements['link[Attributes][CategoryLinkID]'].value,'" . CATEGORY_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $_rootDirID . "')";

		$selector1 = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][CategoryLinkPath]', 58, $_linkPath, '', 'onchange="setHot();" readonly', 'text', 400, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('link[Attributes][CategoryLinkID]', $_linkID), we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 100, 22, '', '', false));

		$_rootDirID = 0;
		$cmd1 = "document.we_form.elements['link[Attributes][CategoryInternLinkID]'].value";
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['link[Attributes][CategoryInternLinkPath]'].value");
		$_cmd = "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','','','" . $_rootDirID . "')";

		$selector2 = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][CategoryInternLinkPath]', 58, $_internLinkPath, '', 'onchange="setHot();" readonly', 'text', 400, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('link[Attributes][CategoryInternLinkID]', $_internLinkID), we_html_button::create_button(we_html_button::SELECT, $_cmd, true, 100, 22, '', '', false)
		);

		return '<div id="mode_category" style="display: none;">
<table class="default">
		<tr><td style="padding:2px 0px;">' . $selector1 . '</td></tr>
		<tr><td class="defaultfont">' . g_l('modules_glossary', '[link_selection]') . '</td></tr>
		<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlSelect("link[Attributes][modeCategory]", array(
				'intern' => g_l('modules_glossary', '[link_intern]'),
				'extern' => g_l('modules_glossary', '[link_extern]'),
				), 1, $_modeCategory, false, array('onchange' => "setHot();showLinkModeCategory(this.value);"), "value", 520) . '</td></tr>
	</table>
	<div id="mode_category_intern" style="display: none;">
	<table class="default">
		<tr><td>' . $selector2 . '</td></tr>
	</table>
	</div>
	<div id="mode_category_extern" style="display: none;">
		<table class="default">
			<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][CategoryUrl]', 58, $_url, '', 'onchange="setHot();"', 'text', 520) . '</td></tr>
		</table>
	</div>
	<table class="default">
		<tr><td class="defaultfont" style="padding:2px 0px;">' . g_l('modules_glossary', '[parameter_name]') . '</td></tr>
		<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][CategoryCatParameter]', 58, $_catParameter, '', 'onchange="setHot();"', 'text', 520) . '</td></tr>
		<tr><td class="defaultfont">' . g_l('modules_glossary', '[parameter]') . '</td></tr>
		<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][CategoryParameter]', 58, $_parameter, '', 'onchange="setHot();"', 'text', 520, 0) . '</td></tr>
	</table></div>';
	}

	// ---> Helper Methods



	function getLangField($name, $value, $title, $width){

		$_name = md5($name);
		//FIXME: these values should be obtained from global settings
		$_options = array(
			'' => '',
			'de' => 'de',
			'en' => 'en',
			'es' => 'es',
			'fi' => 'fi',
			'ru' => 'ru',
			'nl' => 'nl',
			'pl' => 'pl',
		);
		$_width = 100;

		$input = we_html_tools::htmlTextInput($name, 15, $value, "", '', "text", ($width - $_width));

		$select = we_html_tools::htmlSelect($_name, $_options, 1, "", false, array("onchange" => "setHot();this.form.elements['" . $name . "'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;"), 'value', $_width);

		return we_html_tools::htmlFormElementTable($input, $title, "left", "defaultfont", $select);
	}

	function getRevRel($name, $value, $title, $width){

		$_name = md5($name);
		$_options = array(
			'' => '',
			'contents' => 'contents',
			'chapter' => 'chapter',
			'section' => 'section',
			'subsection' => 'subsection',
			'index' => 'index',
			'glossary' => 'glossary',
			'appendix' => 'appendix',
			'copyright' => 'copyright',
			'next' => 'next',
			'appendix' => 'appendix',
			'prev' => 'prev',
			'start' => 'start',
			'help' => 'help',
			'bookmark' => 'bookmark',
			'alternate' => 'alternate',
			'nofollow' => 'nofollow',
		);
		$_size = 1;
		$_multiple = false;
		$_compare = "value";
		$_width = 100;

		$input = we_html_tools::htmlTextInput($name, 15, $value, "", '', "text", ($width - $_width));

		$select = we_html_tools::htmlSelect($_name, $_options, $_size, "", $_multiple, array("onchange" => "setHot();this.form.elements['" . $name . "'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;"), $_compare, $_width);

		return we_html_tools::htmlFormElementTable($input, $title, "left", "defaultfont", $select);
	}

	private function getHTMLLinkAttributes(we_glossary_glossary $glossary){
		$_parts = array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_glossary', '[linkprops_desc]'), we_html_tools::TYPE_INFO, 520),
				'space' => 120,
				'noline' => 1
			),
			array(
				'headline' => g_l('modules_glossary', '[attributes]'),
				'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Title]', 30, $glossary->Title, '', 'onchange="setHot();"', 'text', 520), g_l('modules_glossary', '[title]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][anchor]', 30, $glossary->getAttribute('anchor'), '', 'onchange="setHot();" onblur="if(this.value&&!new RegExp(\'#?[a-z]+[a-z,0-9,_,:,.,-]*$\',\'i\').test(this.value)){alert(\'' . g_l('linklistEdit', '[anchor_invalid]') . '\');this.focus();}"', 'text', 520), g_l('modules_glossary', '[anchor]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][attribute]', 30, $glossary->getAttribute('attribute'), '', 'onchange="setHot();"', 'text', 520), g_l('modules_glossary', '[link_attribute]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::targetBox('link[Attributes][target]', 30, (520 - 100), '', $glossary->getAttribute('target'), 'setHot();', 8, 100), g_l('modules_glossary', '[target]')),
				'space' => 120,
				'noline' => 1
			),
			array(
				'headline' => g_l('modules_glossary', '[language]'),
				'html' => self::getLangField('link[Attributes][lang]', $glossary->getAttribute('lang'), g_l('modules_glossary', '[link_language]'), 520) .
				self::getLangField('link[Attributes][hreflang]', $glossary->getAttribute('hreflang'), g_l('modules_glossary', '[href_language]'), 520),
				'space' => 120,
				'noline' => 1
			),
			array(
				'headline' => g_l('modules_glossary', '[keyboard]'),
				'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][accesskey]', 30, $glossary->getAttribute('accesskey'), '', 'onchange="setHot();"', 'text', 520), g_l('modules_glossary', '[accesskey]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][tabindex]', 30, $glossary->getAttribute('tabindex'), '', 'onchange="setHot();"', 'text', 520), g_l('modules_glossary', '[tabindex]')),
				'space' => 120,
				'noline' => 1
			),
			array(
				'headline' => g_l('modules_glossary', '[relation]'),
				'html' => self::getRevRel('link[Attributes][rel]', $glossary->getAttribute('rel'), 'rel', 520) .
				self::getRevRel('link[Attributes][rev]', $glossary->getAttribute('rev'), 'rev', 520),
				'space' => 120,
				'noline' => 1
			),
		);

		$_input_width = 70;
		$_popup = new we_html_table(array('cellpadding' => 5), 4, 4);
		$_popup->setCol(0, 0, array('colspan' => 2), we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_open'), 'link[Attributes][popup_open]', g_l('modules_glossary', '[popup_open]')));
		$_popup->setCol(0, 2, array('colspan' => 2), we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_center'), 'link[Attributes][popup_center]', g_l('modules_glossary', '[popup_center]')));

		$_popup->setCol(1, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][popup_xposition]', 5, $glossary->getAttribute('popup_xposition'), '', 'onchange="setHot();"', 'text', $_input_width), g_l('modules_glossary', '[popup_x]')));
		$_popup->setCol(1, 1, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][popup_yposition]', 5, $glossary->getAttribute('popup_yposition'), '', 'onchange="setHot();"', 'text', $_input_width), g_l('modules_glossary', '[popup_y]')));
		$_popup->setCol(1, 2, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][popup_width]', 5, $glossary->getAttribute('popup_width'), '', 'onchange="setHot();"', 'text', $_input_width), g_l('modules_glossary', '[popup_width]')));

		$_popup->setCol(1, 3, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][popup_height]', 5, $glossary->getAttribute('popup_height'), '', 'onchange="setHot();"', 'text', $_input_width), g_l('modules_glossary', '[popup_height]')));


		$_popup->setCol(2, 0, array(), we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_status'), 'link[Attributes][popup_status]', g_l('modules_glossary', '[popup_status]')));
		$_popup->setCol(2, 1, array(), we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_scrollbars'), 'link[Attributes][popup_scrollbars]', g_l('modules_glossary', '[popup_scrollbars]')));
		$_popup->setCol(2, 2, array(), we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_menubar'), 'link[Attributes][popup_menubar]', g_l('modules_glossary', '[popup_menubar]')));

		$_popup->setCol(3, 0, array(), we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_resizable'), 'link[Attributes][popup_resizable]', g_l('modules_glossary', '[popup_resizable]')));
		$_popup->setCol(3, 1, array(), we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_location'), 'link[Attributes][popup_location]', g_l('modules_glossary', '[popup_location]')));
		$_popup->setCol(3, 2, array(), we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_toolbar'), 'link[Attributes][popup_toolbar]', g_l('modules_glossary', '[popup_toolbar]')));


		$_parts[] = array(
			'headline' => g_l('modules_glossary', '[popup]'),
			'html' => $_popup->getHTML(),
			'space' => 120,
			'noline' => 1
		);

		return $_parts;
	}

}
