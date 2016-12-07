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
abstract class we_glossary_frameEditor{

	protected static function buildHeader(we_glossary_frames $weGlossaryFrames, $we_tabs, $titlePre, $titlePost){
		$bodyContent = '<div id="main" ><div id="headrow"><b>' . str_replace(" ", "&nbsp;", $titlePre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . $titlePost . '</b></span></div>' . $we_tabs->getHTML() . '</div>';

		$body = we_html_element::htmlBody(["onresize" => "weTabs.setFrameSize()", "onload" => "weTabs.setFrameSize()", "id" => "eHeaderBody"], $bodyContent
				//$table->getHtml() .
				//$tabsBody
		);
		$tabsHead = we_html_element::cssLink(CSS_DIR . 'we_tab.css') .
			we_html_element::jsScript(JS_DIR . 'initTabs.js') .
			we_html_element::jsScript(WE_JS_MODULES_DIR.'glossary/glossary_header.js');

		return $weGlossaryFrames->getHTMLDocument($body, $tabsHead);
	}

	protected static function buildBody(we_glossary_frames $weGlossaryFrames, $content = ""){

		$hidden = $weGlossaryFrames->View->getCommonHiddens(['cmd' => we_base_request::_(we_base_request::RAW, 'cmd', ''),
			'cmdid' => we_base_request::_(we_base_request::STRING, 'cmdid', ''),
			'pnt' => 'edbody',
			]);

		$form = we_html_element::htmlForm(['name' => 'we_form',
				'onsubmit' => 'return false',
				], $hidden . $content);

		return $weGlossaryFrames->getHTMLDocument(we_html_element::htmlBody(['class' => 'weEditorBody',
					'onload' => 'loaded=1;',
					'onunload' => "doUnload()"
					], $form), $weGlossaryFrames->View->getJSProperty());
	}

	protected static function buildFooter(we_glossary_frames $weGlossaryFrames, $content = ""){
		$body = we_html_element::htmlBody(['id' => 'footerBody'], $content);
		return $weGlossaryFrames->getHTMLDocument($body);
	}

	public static function Footer(we_glossary_frames $weGlossaryFrames){
		$form = we_html_element::htmlForm([], we_html_button::create_button(we_html_button::SAVE, "javascript:top.opener.top.we_cmd('save_exception')", '', 0, 0, '', '', (!permissionhandler::hasPerm('NEW_GLOSSARY') && !permissionhandler::hasPerm('EDIT_GLOSSARY'))));
		return self::buildFooter($weGlossaryFrames, $form);
	}

}
