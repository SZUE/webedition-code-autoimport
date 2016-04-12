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
class we_glossary_frameEditorHome{

	function Header($weGlossaryFrames){

		$_body = array(
			'bgcolor' => '#F0EFF0',
		);

		$body = we_html_element::htmlBody($_body, "");

		return $weGlossaryFrames->getHTMLDocument($body);
	}

	function Body($weGlossaryFrames){

		$_hidden = array(
			'cmd' => 'home',
			'pnt' => 'edbody',
			'name' => 'home',
			'value' => 0,
		);

		$_form = array(
			'name' => 'we_form',
		);

		$GLOBALS["we_print_not_htmltop"] = true;
		$GLOBALS["we_head_insert"] = $weGlossaryFrames->View->getJSProperty();
		$GLOBALS["we_body_insert"] = we_html_element::htmlForm($_form, $weGlossaryFrames->View->getCommonHiddens($_hidden));
		$GLOBALS["mod"] = "glossary";

		ob_start();
		include(WE_MODULES_PATH . 'home.inc.php');
		$out = ob_get_clean();

		$content = we_html_element::jsElement($weGlossaryFrames->topFrame . '.editor.edheader.location="' . $weGlossaryFrames->frameset . '?pnt=edheader&home=1";' .
				$weGlossaryFrames->topFrame . '.editor.edfooter.location="' . $weGlossaryFrames->frameset . '?pnt=edfooter&home=1";') .
			$out;

		$_body = array(
			'bgcolor' => '#F0EFF0',
			'marginwidth' => 15,
			'marginheight' => 15,
			'leftmargin' => 15,
			'topmargin' => 15,
			'onload' => 'loaded=1;',
		);

		return $weGlossaryFrames->getHTMLDocument(we_html_element::htmlBody($_body, $content), "");
	}

	function Footer($weGlossaryFrames){

		$_body = array(
			'bgcolor' => '#EFF0EF',
		);

		return $weGlossaryFrames->getHTMLDocument(we_html_element::htmlBody($_body, ""));
	}

}
