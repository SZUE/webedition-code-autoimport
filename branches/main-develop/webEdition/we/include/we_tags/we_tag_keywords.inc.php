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
function we_tag_keywords($attribs, $content){
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true);
	$attribs = removeAttribs($attribs, array(
		'htmlspecialchars'
	));

	$keys = isset($GLOBALS['KEYWORDS']) && $GLOBALS['KEYWORDS'] ? $GLOBALS['KEYWORDS'] : '';
	if(!$keys && $content){
		ob_start();
		eval('?>' . $content);
		$keys = ob_get_contents();
		ob_end_clean();
	}
	$attribs["name"] = "keywords";
	$attribs["content"] = $htmlspecialchars ? oldHtmlspecialchars(strip_tags($keys)) : strip_tags($keys);
	return getHtmlTag("meta", $attribs) . "\n";
}
