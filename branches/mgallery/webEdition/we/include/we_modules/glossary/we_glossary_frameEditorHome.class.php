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

		function Footer($weGlossaryFrames){

		$_body = array(
			'bgcolor' => '#EFF0EF',
		);

		return $weGlossaryFrames->getHTMLDocument(we_html_element::htmlBody($_body, ""));
	}

}
