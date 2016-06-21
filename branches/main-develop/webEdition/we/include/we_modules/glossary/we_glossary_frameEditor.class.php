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
class we_glossary_frameEditor{

	function buildHeader(we_glossary_frames $weGlossaryFrames, $we_tabs, $titlePre, $titlePost){
		$bodyContent = '<div id="main" ><div id="headrow"><b>' . str_replace(" ", "&nbsp;", $titlePre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . $titlePost . '</b></span></div>' . $we_tabs->getHTML() . '</div>';

		$body = we_html_element::htmlBody(array("onresize" => "weTabs.setFrameSize()", "onload" => "weTabs.setFrameSize()", "id" => "eHeaderBody"), $bodyContent
				//$table->getHtml() .
				//$tabsBody
		);
		$tabsHead = we_tabs::getHeader("
function setTab(tab) {
	top.content.activ_tab=tab;
	//top.content.editor.edbody.we_cmd('switchPage',0);
}");

		return $weGlossaryFrames->getHTMLDocument($body, $tabsHead);
	}

	function buildBody(we_glossary_frames $weGlossaryFrames, $content = ""){

		$hidden = $weGlossaryFrames->View->getCommonHiddens(array(
			'cmd' => we_base_request::_(we_base_request::RAW, 'cmd', ''),
			'cmdid' => we_base_request::_(we_base_request::STRING, 'cmdid', ''),
			'pnt' => 'edbody',
		));

		$form = we_html_element::htmlForm(array(
				'name' => 'we_form',
				'onsubmit' => 'return false',
				), $hidden . $content);

		return $weGlossaryFrames->getHTMLDocument(we_html_element::htmlBody(array(
					'class' => 'weEditorBody',
					'onload' => 'loaded=1;',
					'onunload' => "doUnload()"
					), $form), $weGlossaryFrames->View->getJSProperty());
	}

	function buildFooter(we_glossary_frames $weGlossaryFrames, $content = ""){
		$body = we_html_element::htmlBody(array('id' => 'footerBody'), $content);
		return $weGlossaryFrames->getHTMLDocument($body);
	}

	function Footer(we_glossary_frames $weGlossaryFrames){
		$form = we_html_element::htmlForm(array(), we_html_button::create_button(we_html_button::SAVE, "javascript:top.opener.top.we_cmd('save_exception')", true, 100, 22, '', '', (!permissionhandler::hasPerm('NEW_GLOSSARY') && !permissionhandler::hasPerm('EDIT_GLOSSARY'))));

		return self::buildFooter($weGlossaryFrames, $form);
	}

}
