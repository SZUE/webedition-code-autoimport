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
abstract class we_glossary_frameEditorFolder extends we_glossary_frameEditor{

	public static function Header($weGlossaryFrames){
		$we_tabs = new we_tabs();
		$we_tabs->addTab(g_l('modules_glossary', '[overview]'), '', true, 1);
		$frontendL = getWeFrontendLanguagesForBackend();

		return self::buildHeader($weGlossaryFrames, $we_tabs, g_l('modules_glossary', '[folder]'), $frontendL[substr(we_base_request::_(we_base_request::STRING, 'cmdid'), 0, 5)]);
	}

	public static function Body($weGlossaryFrames){
		$weGlossaryFrames->jsCmd->addCmd('loadHeaderFooter', 'folder', we_base_request::_(we_base_request::STRING, 'cmdid'));
		return self::buildBody($weGlossaryFrames, we_html_element::htmlDiv(['id' => 'tab1', 'style' => ''], we_html_multiIconBox::getHTML('', self::getHTMLOverview($weGlossaryFrames), 30)), we_html_element::jsScript(WE_JS_MODULES_DIR . 'glossary/we_glossary_frameEditorType.js'));
	}

	public static function Footer($weGlossaryFrames){
		return self::buildFooter($weGlossaryFrames, "");
	}

	private static function getHTMLOverview($weGlossaryFrames){
		$cmdid = we_base_request::_(we_base_request::STRING, 'cmdid');
		$list = [
			we_glossary_glossary::TYPE_ABBREVATION,
			we_glossary_glossary::TYPE_ACRONYM,
			we_glossary_glossary::TYPE_FOREIGNWORD,
			we_glossary_glossary::TYPE_LINK,
			we_glossary_glossary::TYPE_TEXTREPLACE,
		];

		$language = $GLOBALS['DB_WE']->escape(substr(we_base_request::_(we_base_request::STRING, 'cmdid'), 0, 5));

		$parts = [];

		foreach($list as $key){
			$items = f('SELECT COUNT(1) FROM ' . GLOSSARY_TABLE . ' WHERE Language="' . $language . '" AND Type="' . $key . '"');
//FIXME createbuttontable?
			$button = we_html_button::create_button('new_glossary_' . $key, "javascript:top.opener.top.we_cmd('new_glossary_" . $key . "', '" . $cmdid . "');", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_GLOSSARY"));

			$parts[] = ["headline" => '<a href="javascript://" onclick="top.content.editor.edbody.location=\'' . $weGlossaryFrames->frameset . '&pnt=edbody&cmd=glossary_view_type&cmdid=' . $cmdid . '_' . $key . '&tabnr=\'+top.content.activ_tab;">' . g_l('modules_glossary', '[' . $key . ']') . '</a>',
				"html" => '<table style="width:550px;" class="default defaultfont">
	<tr><td style="padding-bottom:2px;">' . g_l('modules_glossary', '[' . $key . '_description]') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . g_l('modules_glossary', '[number_of_entries]') . ': ' . $items . '</td></tr>
	<tr><td style="text-align:right">' . $button . '</td></tr>
</table>',
				'space' => we_html_multiIconBox::SPACE_MED];
		}

		return $parts;
	}

}
