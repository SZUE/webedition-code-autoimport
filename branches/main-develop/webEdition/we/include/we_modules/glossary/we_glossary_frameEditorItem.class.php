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
abstract class we_glossary_frameEditorItem extends we_glossary_frameEditor{

	public static function Header(we_glossary_frames $weGlossaryFrames){

		$we_tabs = new we_tabs();
		$we_tabs->addTab('', we_base_constants::WE_ICON_PROPERTIES, true, 1, ['title' => g_l('modules_glossary', '[property]')]);

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

		return self::buildHeader($weGlossaryFrames, $we_tabs, $title, ($weGlossaryFrames->View->Glossary->ID ? oldHtmlspecialchars($weGlossaryFrames->View->Glossary->Text) : g_l('modules_glossary', '[menu_new]')) . '<div id="mark"><i class="fa fa-asterisk modified"></i></div>');
	}

	public static function Body(we_glossary_frames $weGlossaryFrames){
		$tabNr = we_base_request::_(we_base_request::INT, 'tabnr', 1);
		$tabNr = ($weGlossaryFrames->View->Glossary->IsFolder && $tabNr != 1) ? 1 : $tabNr;

		$out = we_html_element::htmlDiv(['id' => 'tab1', 'style' => ($tabNr == 1 ? '' : 'display: none')], we_html_multiIconBox::getHTML('weMultibox', self::getHTMLTabProperties($weGlossaryFrames->View->Glossary), 0, '', 2, g_l('modules_glossary', '[show_extended_linkoptions]'), g_l('modules_glossary', '[hide_extended_linkoptions]'), false));


		$head = we_html_element::jsScript(JS_DIR . 'multiIconBox.js') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'glossary/we_glossary_frameEditorItem.js');

		$weGlossaryFrames->jsCmd->addCmd('loadHeaderFooter', $weGlossaryFrames->View->Glossary->Type, ($weGlossaryFrames->View->Glossary->Type === "link" ? ($weGlossaryFrames->View->Glossary->getAttribute('mode') ?: "intern") : ''), ($weGlossaryFrames->View->Glossary->getAttribute('mode') === "category" ? ($weGlossaryFrames->View->Glossary->getAttribute('modeCategory') ?: "intern") : ''));

		return self::buildBody($weGlossaryFrames, $out, $head);
	}

	public static function Footer(we_glossary_frames $weGlossaryFrames){
		$SaveButton = we_html_button::create_button(we_html_button::SAVE, "javascript:if(top.publishWhenSave==1){top.content.editor.edbody.document.getElementById('Published').value=1;};top.content.we_cmd('save_glossary');", '', 0, 0, '', '', (!we_base_permission::hasPerm('NEW_GLOSSARY') && !we_base_permission::hasPerm('EDIT_GLOSSARY')));
		$UnpublishButton = we_html_button::create_button('deactivate', "javascript:top.content.editor.edbody.document.getElementById('Published').value=0;top.opener.top.we_cmd('save_glossary')", '', 0, 0, '', '', (!we_base_permission::hasPerm('NEW_GLOSSARY') && !we_base_permission::hasPerm('EDIT_GLOSSARY')));

		$NewEntry = we_html_forms::checkbox(1, false, "makeNewEntry", g_l('modules_glossary', '[new_item_after_saving]'), false, "defaultfont", "top.makeNewEntryCheck = (this.checked) ? 1 : 0", false);
		$PublishWhenSaved = we_html_forms::checkbox(1, false, "publishWhenSave", g_l('modules_glossary', '[publish_when_saved]'), false, "defaultfont", "top.publishWhenSave = (this.checked) ? 1 : 0", false);

		$ShowUnpublish = ($weGlossaryFrames->View->Glossary->ID == 0 || $weGlossaryFrames->View->Glossary->Published > 0);

		$col = 0;
		$table2 = new we_html_table(['class' => 'default'], 1, 7);
		$table2->setRow(0, ['style' => 'vertical-align:middle;']);
		if($ShowUnpublish){
			$table2->setColContent(0, $col++, $UnpublishButton);
		}
		$table2->setColContent(0, $col++, $SaveButton);
		if(!$ShowUnpublish){
			$table2->setColContent(0, $col++, $PublishWhenSaved);
		}
		$table2->setColContent(0, $col++, $NewEntry);
		if($weGlossaryFrames->View->Glossary->ID){
			if(we_base_permission::hasPerm(['DELETE_GLOSSARY'])){
				$table2->setColContent(0, 2, we_html_button::create_button(we_html_button::TRASH, "javascript:top.we_cmd('delete_glossary');"));
			}
		}
		$weGlossaryFrames->jsCmd->addCmd('checkSaveNew');

		return self::buildFooter($weGlossaryFrames, we_html_element::htmlForm([], $table2->getHtml()));
	}

	private static function getHTMLTabProperties(we_glossary_glossary $glossary){
		$types = [we_glossary_glossary::TYPE_ACRONYM => g_l('modules_glossary', '[acronym]'),
			we_glossary_glossary::TYPE_ABBREVATION => g_l('modules_glossary', '[abbreviation]'),
			we_glossary_glossary::TYPE_FOREIGNWORD => g_l('modules_glossary', '[foreignword]'),
			we_glossary_glossary::TYPE_LINK => g_l('modules_glossary', '[link]'),
			we_glossary_glossary::TYPE_TEXTREPLACE => g_l('modules_glossary', '[textreplacement]'),
		];

		$hidden = we_html_element::htmlHidden('newone', ($glossary->ID == 0 ? 1 : 0)) .
			we_html_element::htmlHidden('Published', $glossary->ID == 0 ? 1 : ($glossary->Published > 0 ? 1 : 0), 'Published');


		$language = ($glossary->Language ?: $GLOBALS['weDefaultFrontendLanguage']);

		$content = $hidden . '<table class="default">
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[folder]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlSelect("Language", getWeFrontendLanguagesForBackend(), 1, $language, false, ['onchange' => "top.content.setHot();"], "value", 520) . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[type]') . '</td></tr>
	<tr><td>' . we_html_tools::htmlSelect("Type", $types, 1, $glossary->Type, false, ['onchange' => "top.content.setHot();showType(this.value);"], "value", 520) . '</td></tr>
	<tr><td class="defaultfont">' . we_html_forms::checkboxWithHidden((bool) $glossary->Fullword, 'Fullword', g_l('modules_glossary', '[Fullword]'), false, 'defaultfont', 'top.content.setHot();') . '</td></tr>
</table>';
		$parts = [["headline" => g_l('modules_glossary', '[path]'),
			"html" => $content,
			'space' => we_html_multiIconBox::SPACE_ICON,
			'icon' => we_html_multiIconBox::PROP_PATH
			],
			["headline" => g_l('modules_glossary', '[selection]'),
				"html" => self::getHTMLAbbreviation($glossary) .
				self::getHTMLAcronym($glossary) .
				self::getHTMLForeignWord($glossary) .
				self::getHTMLLink($glossary) .
				self::getHTMLTextReplacement($glossary),
				'space' => we_html_multiIconBox::SPACE_ICON,
				'icon'=>we_html_multiIconBox::PROP_ATTRIB,
				'noline' => 1,
			]
		];

		return array_merge($parts, self::getHTMLLinkAttributes($glossary));
	}

	private static function getHTMLAbbreviation(we_glossary_glossary $glossary){
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

	private static function getHTMLAcronym(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_ACRONYM){
			$text = html_entity_decode($glossary->Text);
			$title = html_entity_decode($glossary->Title);
			$language = $glossary->getAttribute('lang');
		} else {
			$text = $title = $language = "";
		}

		return '<div id="type_acronym" style="display: none;">
<table class="default">
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[acronym]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("acronym[Text]", 24, $text, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[announced_word]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("acronym[Title]", 24, $title, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td>' . self::getLangField("acronym[Attributes][lang]", $language, g_l('modules_glossary', '[language]'), 520) . '</td></tr>
</table>
</div>';
	}

	private static function getHTMLForeignWord(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_FOREIGNWORD){
			$text = html_entity_decode($glossary->Text);
			$language = $glossary->getAttribute('lang');
		} else {
			$text = $language = "";
		}

		return '<div id="type_foreignword" style="display: none;"><table class="default">
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[foreignword]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("foreignword[Text]", 24, $text, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td>' . self::getLangField("foreignword[Attributes][lang]", $language, g_l('modules_glossary', '[language]'), 520) . '</td></tr>
</table></div>';
	}

	private static function getHTMLTextReplacement(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_TEXTREPLACE){
			$text = html_entity_decode($glossary->Text, null, $GLOBALS["WE_BACKENDCHARSET"]);
			$title = html_entity_decode($glossary->Title, null, $GLOBALS["WE_BACKENDCHARSET"]);
		} else {
			$title = $text = "";
		}

		return '<div id="type_textreplacement" style="display: none;"><table class="default">
<tr><td class="defaultfont">' . g_l('modules_glossary', '[textreplacement]') . '</td></tr>
<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("textreplacement[Text]", 24, $text, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
<tr><td class="defaultfont">' . g_l('modules_glossary', '[textreplacement_Text]') . '</td></tr>
<tr><td>' . we_html_tools::htmlTextInput("textreplacement[Title]", 24, $title, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
</table></div>';
	}

	private static function getHTMLLink(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_LINK){
			$text = html_entity_decode($glossary->Text);
			$mode = $glossary->getAttribute('mode');
		} else {
			$text = $mode = "";
		}

		return
			'<div id="type_link" style="display: none;">
<table class="default">
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[link]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlTextInput("link[Text]", 24, $text, 255, 'onchange="setHot();"', "text", 520) . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlSelect("link[Attributes][mode]", ['intern' => g_l('modules_glossary', '[link_intern]'),
				'extern' => g_l('modules_glossary', '[link_extern]'),
				'object' => g_l('modules_glossary', '[link_object]'),
				'category' => g_l('modules_glossary', '[link_category]'),
				], 1, $mode, false, ['onchange' => "setHot();showLinkMode(this.value);"], "value", 520) . '</td></tr>
</table>' .
			self::getHTMLIntern($glossary) .
			self::getHTMLExtern($glossary) .
			self::getHTMLObject($glossary) .
			self::getHTMLCategory($glossary) .
			'</div>';
	}

	private static function getHTMLIntern(we_glossary_glossary $glossary){
		$cmd = "javascript:we_cmd('we_selector_document',document.we_form.elements['link[Attributes][InternLinkID]'].value,'" . FILE_TABLE . "','link[Attributes][InternLinkID]','link[Attributes][InternLinkPath]','','','0')";

		if($glossary->Type === "link" && $glossary->getAttribute('mode') === "intern"){
			//$linkPath = $glossary->getAttribute('InternLinkPath');
			$linkID = $glossary->getAttribute('InternLinkID');
			$linkPath = id_to_path($linkID);
			$glossary->setAttribute('InternLinkPath', $linkPath);
			$internParameter = $glossary->getAttribute('InternParameter');
		} else {
			$linkPath = $linkID = $internParameter = "";
		}
		$weSuggest = &weSuggest::getInstance();
		$weSuggest->setAcId('docPath');
		$weSuggest->setContentType([we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::JS, we_base_ContentTypes::CSS,
			we_base_ContentTypes::APPLICATION]);
		$weSuggest->setInput('link[Attributes][InternLinkPath]', $linkPath);
		$weSuggest->setMaxResults(10);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, $cmd, '', 0, 0, '', '', false));
		$weSuggest->setResult('link[Attributes][InternLinkID]', $linkID);
		$weSuggest->setSelector(weSuggest::DocSelector);
		$weSuggest->setTable(FILE_TABLE);
		$weSuggest->setWidth(400);

		return '<div id="mode_intern" style="display: none;">'
			. '<table class="default">
	<tr><td style="padding:2px 0px;">' . $weSuggest->getHTML() . '</td></tr>
	<tr><td class="defaultfont">' . g_l('modules_glossary', '[parameter]') . '</td></tr>
	<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][InternParameter]', 58, $internParameter, '', 'onchange="setHot();"', 'text', 520, 0) . '</td></tr>
</table></div>';
	}

	private static function getHTMLExtern(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_LINK && $glossary->getAttribute('mode') === "extern"){
			$url = $glossary->getAttribute('ExternUrl');
			$parameter = $glossary->getAttribute('ExternParameter');
		} else {
			$url = we_base_link::EMPTY_EXT;
			$parameter = "";
		}

		return '<div id="mode_extern" style="display: none;">
	<table class="default">
		<tr><td style="padding:2px 0px;">' . we_html_tools::htmlTextInput('link[Attributes][ExternUrl]', 58, $url, '', 'onchange="setHot();"', 'text', 520) . '</td></tr>
		<tr><td class="defaultfont">' . g_l('modules_glossary', '[parameter]') . '</td></tr>
		<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][ExternParameter]', 58, $parameter, '', 'onchange="setHot();"', 'text', 520, 0) . '</td></tr>
	</table>
</div>';
	}

	private static function getHTMLObject(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_LINK && $glossary->getAttribute('mode') === "object"){
			$linkPath = $glossary->getAttribute('ObjectLinkPath');
			$linkID = $glossary->getAttribute('ObjectLinkID');
			$workspaceID = $glossary->getAttribute('ObjectWorkspaceID');
			$parameter = $glossary->getAttribute('ObjectParameter');
		} else {
			$linkPath = $linkID = $workspaceID = $parameter = "";
		}

		$cmd = defined('OBJECT_TABLE') ? "javascript:we_cmd('we_selector_document',document.we_form.elements['link[Attributes][ObjectLinkID]'].value,'" . OBJECT_FILES_TABLE . "','link[Attributes][ObjectLinkID]','link[Attributes][ObjectLinkPath]','populateWorkspaces','','0','objectFile'," . (we_base_permission::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")" : '';

		$weSuggest = &weSuggest::getInstance();
		$weSuggest->setAcId('objPathLink');
		$weSuggest->setContentType("folder," . we_base_ContentTypes::OBJECT_FILE);
		$weSuggest->setInput('link[Attributes][ObjectLinkPath]', $linkPath);
		$weSuggest->setMaxResults(10);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, $cmd, '', 0, 0, '', '', false));
		$weSuggest->setResult('link[Attributes][ObjectLinkID]', $linkID);
		$weSuggest->setSelector(weSuggest::DocSelector);
		$weSuggest->setTable(OBJECT_FILES_TABLE);
		$weSuggest->setWidth(400);

		$wsid = ($glossary->getAttribute('ObjectLinkID') ? we_navigation_dynList::getWorkspacesForObject($glossary->getAttribute('ObjectLinkID')) : []);

		return '<div id="mode_object" style="display: none;">
	<table class="default">
			<tr><td style="padding:2px 0px;">' . $weSuggest->getHTML() . '</td></tr>
	</table>
	<div id="ObjectWorkspaceID" style="display: block;">
		<table class="default">
			<tr><td class="defaultfont">' . g_l('modules_glossary', '[workspace]') . '</td></tr>
			<tr><td>' . we_html_tools::htmlSelect('link[Attributes][ObjectWorkspaceID]', $wsid, 0, $workspaceID, false, ['style' => "width:520px; border: #AAAAAA solid 1px;",
				'onchange' => "setHot();"], 'value') . '</td></tr>
		</table>
	</div>
	<table class="default">
		<tr><td class="defaultfont" style="padding-top:2px;">' . g_l('modules_glossary', '[parameter]') . '</td></tr>
		<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][ObjectParameter]', 58, $parameter, '', 'onchange="setHot();"', 'text', 520, 0) . '</td></tr>
	</table></div>';
	}

	private static function getHTMLCategory(we_glossary_glossary $glossary){
		if($glossary->Type == we_glossary_glossary::TYPE_LINK && $glossary->getAttribute('mode') === "category"){
			$linkPath = $glossary->getAttribute('CategoryLinkPath');
			$linkID = $glossary->getAttribute('CategoryLinkID');
			$internLinkPath = $glossary->getAttribute('CategoryInternLinkPath');
			$internLinkID = $glossary->getAttribute('CategoryInternLinkID');
			$modeCategory = $glossary->getAttribute('modeCategory');
			$url = $glossary->getAttribute('CategoryUrl');
			$catParameter = $glossary->getAttribute('CategoryCatParameter');
			$parameter = $glossary->getAttribute('CategoryParameter');
		} else {
			$linkPath = "/";
			$linkID = 0;
			$internLinkPath = "";
			$internLinkID = "";
			$modeCategory = 0;
			$url = "http://";
			$catParameter = "";
			$parameter = "";
		}

		$cmd = "javascript:we_cmd('we_selector_category',document.we_form.elements['link[Attributes][CategoryLinkID]'].value,'" . CATEGORY_TABLE . "','link[Attributes][CategoryLinkID]','link[Attributes][CategoryLinkPath]','setHot','','0')";

		$selector1 = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][CategoryLinkPath]', 58, $linkPath, '', 'onchange="setHot();" readonly', 'text', 400, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('link[Attributes][CategoryLinkID]', $linkID), we_html_button::create_button(we_html_button::SELECT, $cmd, '', 0, 0, '', '', false));


		$cmd = "javascript:we_cmd('we_selector_document',document.we_form.elements['link[Attributes][CategoryInternLinkID]'].value,'" . FILE_TABLE . "','link[Attributes][CategoryInternLinkID]','link[Attributes][CategoryInternLinkPath]','','','0')";

		$selector2 = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][CategoryInternLinkPath]', 58, $internLinkPath, '', 'onchange="setHot();" readonly', 'text', 400, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden('link[Attributes][CategoryInternLinkID]', $internLinkID), we_html_button::create_button(we_html_button::SELECT, $cmd, '', 0, 0, '', '', false)
		);

		return '<div id="mode_category" style="display: none;">
<table class="default">
		<tr><td style="padding:2px 0px;">' . $selector1 . '</td></tr>
		<tr><td class="defaultfont">' . g_l('modules_glossary', '[link_selection]') . '</td></tr>
		<tr><td style="padding-bottom:2px;">' . we_html_tools::htmlSelect("link[Attributes][modeCategory]", ['intern' => g_l('modules_glossary', '[link_intern]'),
				'extern' => g_l('modules_glossary', '[link_extern]'),
				], 1, $modeCategory, false, ['onchange' => "setHot();showLinkModeCategory(this.value);"], "value", 520) . '</td></tr>
	</table>
	<div id="mode_category_intern" style="display: none;">
	<table class="default">
		<tr><td>' . $selector2 . '</td></tr>
	</table>
	</div>
	<div id="mode_category_extern" style="display: none;">
		<table class="default">
			<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][CategoryUrl]', 58, $url, '', 'onchange="setHot();"', 'text', 520) . '</td></tr>
		</table>
	</div>
	<table class="default">
		<tr><td class="defaultfont" style="padding:2px 0px;">' . g_l('modules_glossary', '[parameter_name]') . '</td></tr>
		<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][CategoryCatParameter]', 58, $catParameter, '', 'onchange="setHot();"', 'text', 520) . '</td></tr>
		<tr><td class="defaultfont">' . g_l('modules_glossary', '[parameter]') . '</td></tr>
		<tr><td>' . we_html_tools::htmlTextInput('link[Attributes][CategoryParameter]', 58, $parameter, '', 'onchange="setHot();"', 'text', 520, 0) . '</td></tr>
	</table></div>';
	}

	// ---> Helper Methods

	private static function getLangField($name, $value, $title, $width){
		$name = md5($name);
		$langs = array_keys(getWELangs());
		array_unshift($langs, '');
		$options = array_combine($langs, $langs);

		$input = we_html_tools::htmlTextInput($name, 15, $value, "", '', "text");

		$select = we_html_tools::htmlSelect($name . '_sel', $options, 1, "", false, ['onchange' => "setHot();this.form.elements['" . $name . "'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;"], 'value');

		return we_html_tools::htmlFormElementTable($input, $title, "left", "defaultfont", $select);
	}

	private static function getRevRel($name, $value, $title, $width){
		$name = md5($name);
		$options = ['' => '',
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
		];
		$size = 1;
		$multiple = false;
		$compare = "value";

		$input = we_html_tools::htmlTextInput($name, 15, $value, "", '', "text");

		$select = we_html_tools::htmlSelect($name . '_sel', $options, $size, "", $multiple, ['onchange' => "setHot();this.form.elements['" . $name . "'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;"], $compare);

		return we_html_tools::htmlFormElementTable($input, $title, "left", "defaultfont", $select);
	}

	private function getHTMLLinkAttributes(we_glossary_glossary $glossary){
		$input_width = 70;
		$popup = new we_html_table(['class' => 'withSpace'], 4, 4);
		$popup->setCol(0, 0, ['colspan' => 2], we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_open'), 'link[Attributes][popup_open]', g_l('modules_glossary', '[popup_open]')));
		$popup->setCol(0, 2, ['colspan' => 2], we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_center'), 'link[Attributes][popup_center]', g_l('modules_glossary', '[popup_center]')));

		$popup->setCol(1, 0, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][popup_xposition]', 5, $glossary->getAttribute('popup_xposition'), '', 'onchange="setHot();"', 'text', $input_width), g_l('modules_glossary', '[popup_x]')));
		$popup->setCol(1, 1, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][popup_yposition]', 5, $glossary->getAttribute('popup_yposition'), '', 'onchange="setHot();"', 'text', $input_width), g_l('modules_glossary', '[popup_y]')));
		$popup->setCol(1, 2, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][popup_width]', 5, $glossary->getAttribute('popup_width'), '', 'onchange="setHot();"', 'text', $input_width), g_l('modules_glossary', '[popup_width]')));

		$popup->setCol(1, 3, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][popup_height]', 5, $glossary->getAttribute('popup_height'), '', 'onchange="setHot();"', 'text', $input_width), g_l('modules_glossary', '[popup_height]')));


		$popup->setCol(2, 0, [], we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_status'), 'link[Attributes][popup_status]', g_l('modules_glossary', '[popup_status]')));
		$popup->setCol(2, 1, [], we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_scrollbars'), 'link[Attributes][popup_scrollbars]', g_l('modules_glossary', '[popup_scrollbars]')));
		$popup->setCol(2, 2, [], we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_menubar'), 'link[Attributes][popup_menubar]', g_l('modules_glossary', '[popup_menubar]')));

		$popup->setCol(3, 0, [], we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_resizable'), 'link[Attributes][popup_resizable]', g_l('modules_glossary', '[popup_resizable]')));
		$popup->setCol(3, 1, [], we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_location'), 'link[Attributes][popup_location]', g_l('modules_glossary', '[popup_location]')));
		$popup->setCol(3, 2, [], we_html_forms::checkboxWithHidden($glossary->getAttribute('popup_toolbar'), 'link[Attributes][popup_toolbar]', g_l('modules_glossary', '[popup_toolbar]')));


		return [['headline' => '',
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_glossary', '[linkprops_desc]'), we_html_tools::TYPE_INFO, 520),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
			],
			['headline' => g_l('modules_glossary', '[attributes]'),
				'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Title]', 30, $glossary->Title, '', 'onchange="setHot();"', 'text', 520), g_l('modules_glossary', '[title]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][anchor]', 30, $glossary->getAttribute('anchor'), '', 'onchange="setHot();" onblur="if(this.value&&!new RegExp(\'#?[a-z]+[a-z0-9_:.-=]*$\',\'i\').test(this.value)){top.we_showMessage(\'' . g_l('linklistEdit', '[anchor_invalid]') . '\', WE().consts.message.WE_MESSAGE_ERROR);this.focus();}"', 'text', 520), g_l('modules_glossary', '[anchor]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][attribute]', 30, $glossary->getAttribute('attribute'), '', 'onchange="setHot();"', 'text', 520), g_l('modules_glossary', '[link_attribute]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::targetBox('link[Attributes][target]', 30, (520 - 100), '', $glossary->getAttribute('target'), 'setHot();', 8, 100), g_l('modules_glossary', '[target]')),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			],
			['headline' => g_l('modules_glossary', '[language]'),
				'html' => self::getLangField('link[Attributes][lang]', $glossary->getAttribute('lang'), g_l('modules_glossary', '[link_language]'), 520) .
				self::getLangField('link[Attributes][hreflang]', $glossary->getAttribute('hreflang'), g_l('modules_glossary', '[href_language]'), 520),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			],
			['headline' => g_l('modules_glossary', '[keyboard]'),
				'html' => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][accesskey]', 30, $glossary->getAttribute('accesskey'), '', 'onchange="setHot();"', 'text', 520), g_l('modules_glossary', '[accesskey]')) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('link[Attributes][tabindex]', 30, $glossary->getAttribute('tabindex'), '', 'onchange="setHot();"', 'text', 520), g_l('modules_glossary', '[tabindex]')),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			],
			['headline' => g_l('modules_glossary', '[relation]'),
				'html' => self::getRevRel('link[Attributes][rel]', $glossary->getAttribute('rel'), 'rel', 520) .
				self::getRevRel('link[Attributes][rev]', $glossary->getAttribute('rev'), 'rev', 520),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			],
			['headline' => g_l('modules_glossary', '[popup]'),
				'html' => $popup->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			]
		];
	}

}
