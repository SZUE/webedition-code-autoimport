<?php
/**
 * webEdition CMS
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


function we_tag_ifNotHtmlMail($attribs, $content) {
	if ((isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"]) || (isset($GLOBALS['we_doc']->InWebEdition) && $GLOBALS['we_doc']->InWebEdition)) {
		return true;
	}

	if (isset($GLOBALS["WE_HTMLMAIL"])) {
		if (!$GLOBALS["WE_HTMLMAIL"]) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

?>