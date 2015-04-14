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

	function buildHeader($weGlossaryFrames, $we_tabs, $titlePre, $titlePost){
		$we_tabs->onResize();
		$tabsHead = $we_tabs->getHeader();
		$bodyContent = '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . str_replace(" ", "&nbsp;", $titlePre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . $titlePost . '</b></span></nobr></div>' . we_html_tools::getPixel(100, 3) . $we_tabs->getHTML() . '</div>';

		$body = we_html_element::htmlBody(array("onresize" => "setFrameSize()", "onload" => "setFrameSize()", "bgcolor" => "#C8D8EC", "background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif", "marginwidth" => 0, "marginheight" => 0, "leftmargin" => 0, "topmargin" => 0), $bodyContent
				//$table->getHtml() .
				//$tabsBody
		);
		$_js = "
			function setTab(tab) {
				" . $this->topFrame . ".activ_tab=tab;
				//top.content.editor.edbody.we_cmd('switchPage',0);
			}
			top.content.hloaded = 1;\n";

		$js = we_html_element::jsElement($_js);

		return $weGlossaryFrames->getHTMLDocument($body, $tabsHead . $js);
	}

	function buildBody($weGlossaryFrames, $content = ""){

		$_hidden = array(
			'cmd' => we_base_request::_(we_base_request::RAW, 'cmd', ''),
			'cmdid' => we_base_request::_(we_base_request::STRING, 'cmdid', ''),
			'pnt' => 'edbody',
		);

		$_form = array(
			'name' => 'we_form',
			'onsubmit' => 'return false',
		);

		$hidden = $weGlossaryFrames->View->getCommonHiddens($_hidden);

		$form = we_html_element::htmlForm($_form, $hidden . $content);

		$_body = array(
			'class' => 'weEditorBody',
			'onload' => 'loaded=1;',
			'onunload' => "doUnload()"
		);

		$body = we_html_element::htmlBody($_body, $form);

		return $weGlossaryFrames->getHTMLDocument($body, $weGlossaryFrames->View->getJSProperty());
	}

	function buildFooter($weGlossaryFrames, $content = ""){

		$_body = array(
			'bgcolor' => 'white',
			'background' => IMAGE_DIR . 'edit/editfooterback.gif',
			'marginwidth' => 0,
			'marginheight' => 0,
			'leftmargin' => 0,
			'topmargin' => 0,
		);

		$body = we_html_element::htmlBody($_body, $content);

		return $weGlossaryFrames->getHTMLDocument($body);
	}

}
