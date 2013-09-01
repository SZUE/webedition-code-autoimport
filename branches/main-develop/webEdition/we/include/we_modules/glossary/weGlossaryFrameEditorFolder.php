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
class weGlossaryFrameEditorFolder extends weGlossaryFrameEditor{

	function Header(&$weGlossaryFrames){

		$we_tabs = new we_tabs();
		$we_tabs->addTab(new we_tab("#", g_l('modules_glossary', '[overview]'), we_tab::ACTIVE, "setTab('1');"));

		$frontendL = getWeFrontendLanguagesForBackend();
		$title = g_l('modules_glossary', '[folder]') . ":&nbsp;";

		$title .= $frontendL[substr($_REQUEST['cmdid'], 0, 5)];

		return self::buildHeader($weGlossaryFrames, $we_tabs, g_l('modules_glossary', '[folder]'), $frontendL[substr($_REQUEST['cmdid'], 0, 5)]);
	}

	function Body(&$weGlossaryFrames){

		$_js = $weGlossaryFrames->topFrame . '.editor.edheader.location="' . $weGlossaryFrames->frameset . '?pnt=edheader&cmd=view_folder&cmdid=' . $_REQUEST['cmdid'] . '";'
			. $weGlossaryFrames->topFrame . '.editor.edfooter.location="' . $weGlossaryFrames->frameset . '?pnt=edfooter&cmd=view_folder&cmdid=' . $_REQUEST['cmdid'] . '"';

		$js = we_html_element::jsElement($_js);

		$out = we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ''), we_multiIconBox::getHTML('', "100%", self::getHTMLOverview($weGlossaryFrames), 30, '', -1, '', '', false));

		$content = $js . $out;

		return self::buildBody($weGlossaryFrames, $content);
	}

	function Footer(&$weGlossaryFrames){

		return self::buildFooter($weGlossaryFrames, "");
	}

	function getHTMLOverview(&$weGlossaryFrames){

		$_list = array(
			we_glossary_glossary::TYPE_ABBREVATION => g_l('modules_glossary', '[abbreviation]'),
			we_glossary_glossary::TYPE_ACRONYM => g_l('modules_glossary', '[acronym]'),
			we_glossary_glossary::TYPE_FOREIGNWORD => g_l('modules_glossary', '[foreignword]'),
			we_glossary_glossary::TYPE_LINK => g_l('modules_glossary', '[link]'),
			we_glossary_glossary::TYPE_TEXTREPLACE => g_l('modules_glossary', '[textreplacement]'),
		);

		$language = substr($_REQUEST['cmdid'], 0, 5);

		$parts = array();


		foreach($_list as $key => $value){

			$query = "SELECT count(1) as items FROM " . GLOSSARY_TABLE . " WHERE Language = '" . $GLOBALS['DB_WE']->escape($language) . "' AND Type = '" . $key . "'";
			$items = f($query, "items", $GLOBALS['DB_WE']);

			$button = we_button::create_button("new_glossary_" . $key, "javascript:top.opener.top.we_cmd('new_glossary_" . $key . "', '" . $_REQUEST['cmdid'] . "');", true, -1, -1, "", "", !we_hasPerm("NEW_GLOSSARY"));

			$content = '<table width="550" border="0" cellpadding="0" cellspacing="0" class="defaultfont">
						<tr>
							<td>
								' . g_l('modules_glossary', '[' . $key . '_description]') . '</td>
						</tr>
						<tr>
							<td>
								' . we_html_tools::getPixel(2, 4) . '</td>
						<tr>
							<td>
								' . g_l('modules_glossary', '[number_of_entries]') . ': ' . $items . '</td>
						</tr>
						<tr>
							<td>
								' . we_html_tools::getPixel(2, 4) . '</td>
						</tr>
						<tr>
							<td align="right">
								' . $button . '</td>
						</tr>
						</table>';

			$headline = '<a href="javascript://" onclick="' . $this->topFrame . '.editor.edbody.location=\'' . $weGlossaryFrames->frameset . '?pnt=edbody&cmd=view_type&cmdid=' . $_REQUEST['cmdid'] . '_' . $key . '&tabnr=\'+' . $weGlossaryFrames->topFrame . '.activ_tab;">' . g_l('modules_glossary', '[' . $key . ']') . '</a>';

			$parts[] = array("headline" => $headline, "html" => $content, "space" => 120);
		}

		return $parts;
	}

}
