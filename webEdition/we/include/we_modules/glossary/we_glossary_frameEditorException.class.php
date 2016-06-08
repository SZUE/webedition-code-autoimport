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
class we_glossary_frameEditorException extends we_glossary_frameEditor{

	function Header(we_glossary_frames $weGlossaryFrames){
		$cmdid = substr(we_base_request::_(we_base_request::STRING, 'cmdid'), 0, 5);
		$we_tabs = new we_tabs();
		$we_tabs->addTab(new we_tab(g_l('modules_glossary', '[exception]'), we_tab::ACTIVE, "setTab('1');"));

		$frontendL = getWeFrontendLanguagesForBackend();

		return self::buildHeader($weGlossaryFrames, $we_tabs, g_l('modules_glossary', '[exception]'), (isset($frontendL[$cmdid]) ? $frontendL[$cmdid] : "-"));
	}

	function Body(we_glossary_frames $weGlossaryFrames){
		$tabNr = we_base_request::_(we_base_request::INT, 'tabnr', 1);
		$tabNr = ($weGlossaryFrames->View->Glossary->IsFolder && $tabNr != 1) ? 1 : $tabNr;
		$cmdid = we_base_request::_(we_base_request::STRING, 'cmdid');

		return self::buildBody($weGlossaryFrames, we_html_element::jsElement('top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=glossary&pnt=edheader&cmd=glossary_view_exception&cmdid=' . $cmdid . '";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=glossary&pnt=edfooter&cmd=glossary_view_exception&cmdid=' . $cmdid . '"') . we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ($tabNr == 1 ? '' : 'display: none')), we_html_multiIconBox::getHTML('weMultibox', self::getHTMLTabProperties(), 30)));
	}

	private function getHTMLTabProperties(){
		$language = substr(we_base_request::_(we_base_request::STRING, 'cmdid'), 0, 5);

		$content = '<table class="default">
	<tr><td style="padding-bottom:4px;">' . we_html_tools::htmlAlertAttentionBox(g_l('modules_glossary', '[hint_exception]'), we_html_tools::TYPE_INFO, 520, true, 0) . '</td></tr>
	<tr><td>' . we_html_element::htmlTextarea(array('name' => 'Exception', 'cols' => 60, 'rows' => 20, 'style' => 'width:520px;'), implode("", we_glossary_glossary::getException($language))) . '</td></tr>
</table>';

		return array(
			array(
				"headline" => g_l('modules_glossary', '[exception]'),
				"html" => $content,
				'space' => we_html_multiIconBox::SPACE_MED
		));
	}

}
