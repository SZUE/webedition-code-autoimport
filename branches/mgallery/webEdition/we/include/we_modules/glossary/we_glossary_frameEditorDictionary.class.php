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
class we_glossary_frameEditorDictionary extends we_glossary_frameEditor{

	function Header($weGlossaryFrames){
		$cmdid = substr(we_base_request::_(we_base_request::STRING, 'cmdid'), 0, 5);

		$we_tabs = new we_tabs();
		$we_tabs->addTab(new we_tab(g_l('modules_glossary', '[dictionary]'), we_tab::ACTIVE, "setTab('1');"));

		$frontendL = getWeFrontendLanguagesForBackend();
		$title = g_l('modules_glossary', '[dictionary]') . ":&nbsp;" . $frontendL[$cmdid];

		return self::buildHeader($weGlossaryFrames, $we_tabs, g_l('modules_glossary', '[dictionary]'), $frontendL[$cmdid]);
	}

	function Body($weGlossaryFrames){
		$cmdid = we_base_request::_(we_base_request::STRING, 'cmdid');
		$tabNr = we_base_request::_(we_base_request::INT, 'tabnr', 1);
		$tabNr = ($weGlossaryFrames->View->Glossary->IsFolder && $tabNr != 1) ? 1 : $tabNr;

		return self::buildBody($weGlossaryFrames, we_html_element::jsElement($weGlossaryFrames->topFrame . '.editor.edheader.location="' . $weGlossaryFrames->frameset . '?pnt=edheader&cmd=view_dictionary&cmdid=' . $cmdid . '";' .
					$weGlossaryFrames->topFrame . '.editor.edfooter.location="' . $weGlossaryFrames->frameset . '?pnt=edfooter&cmd=view_dictionary&cmdid=' . $cmdid . '"') .
				we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ($tabNr == 1 ? '' : 'display: none')), we_html_multiIconBox::getHTML('weMultibox', "100%", self::getHTMLTabProperties($weGlossaryFrames), 30, '', -1, '', '', false))
		);
	}

	function Footer($weGlossaryFrames){
		$table2 = new we_html_table(array(
			'border' => 0,
			'cellpadding' => 0,
			'cellspacing' => 0,
			), 1, 2);
		$table2->setRow(0, array("valign" => "middle"));
		$table2->setCol(0, 0, array("nowrap" => null), we_html_tools::getPixel(10, 20));
		$table2->setCol(0, 1, array("nowrap" => null), we_html_button::create_button(we_html_button::SAVE, "javascript:top.opener.top.we_cmd('save_dictionary')", true, 100, 22, '', '', (!permissionhandler::hasPerm('NEW_GLOSSARY') && !permissionhandler::hasPerm('EDIT_GLOSSARY'))));

		$form = we_html_element::htmlForm(array(), $table2->getHtml());

		return self::buildFooter($weGlossaryFrames, $form);
	}

	function getHTMLTabProperties($weGlossaryFrames){
		$language = substr(we_base_request::_(we_base_request::STRING, 'cmdid'), 0, 5);

		$content = '<table border="0" cellpadding="0" cellspacing="0">
					<tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('modules_glossary', '[hint_dictionary]'), we_html_tools::TYPE_INFO, 520, true, 0) . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
					<tr><td>' . we_html_element::htmlTextarea(array('name' => 'Dictionary', 'cols' => 60, 'rows' => 20, 'style' => 'width:520px;'), implode("\n", we_glossary_glossary::getDictionary($language))) . '</td></tr>
				</table>';

		return array(
			array(
				"headline" => g_l('modules_glossary', '[dictionary]'),
				"html" => $content,
				"space" => 120
			)
		);
	}

}
