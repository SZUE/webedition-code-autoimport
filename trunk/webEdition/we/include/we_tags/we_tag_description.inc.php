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
function we_tag_description($attribs, $content){
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true);
	$max = weTag_getAttribute('max', $attribs, 0);
	$attribs = removeAttribs($attribs, array(
		'htmlspecialchars', 'max'
	));

	$descr = isset($GLOBALS['DESCRIPTION']) && $GLOBALS['DESCRIPTION'] ? $GLOBALS['DESCRIPTION'] : '';
	if(!$descr && $content){
		ob_start();
		eval('?>' . $content);
		$descr = ob_get_contents();
		ob_end_clean();
	}
	$attribs["name"] = "description";
	$descr = $htmlspecialchars ? oldHtmlspecialchars(strip_tags($descr)) : strip_tags($descr);
	$attribs["content"] = $max ? cutText($descr, $max, true) : $descr;
	return getHtmlTag("meta", $attribs) . "\n";
}
