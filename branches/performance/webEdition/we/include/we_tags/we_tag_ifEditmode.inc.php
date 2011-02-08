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

function we_tag_ifEditmode($attribs, $content){
	$doc = we_getTagAttribute('doc', $attribs);
	switch ($doc) {
		case 'self' :
			return $GLOBALS['WE_MAIN_DOC'] == $GLOBALS['we_doc'] && $GLOBALS["we_editmode"];
		default :
			return $GLOBALS['we_editmode']|| $GLOBALS['WE_MAIN_EDITMODE']/* || (isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem')*/;
	}
}
