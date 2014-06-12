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
function we_tag_title($attribs, $content){
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true);
	$prefix = weTag_getAttribute('prefix', $attribs);
	$suffix = weTag_getAttribute('suffix', $attribs);
	$delimiter = weTag_getAttribute('delimiter', $attribs);

	$attribs = removeAttribs($attribs, array('htmlspecialchars', 'prefix', 'suffix', 'delimiter'));
	$title = isset($GLOBALS['TITLE']) && $GLOBALS['TITLE'] ? $GLOBALS['TITLE'] : '';
	if(!$title && $content){
		ob_start();
		//FIXME:eval
		eval('?>' . $content);
		$title = ob_get_contents();
		ob_end_clean();
	}
	$title = ($prefix ? $prefix . ($title ? $delimiter : '') : '') . $title . ($suffix ? ($title ? $delimiter : ($prefix ? $delimiter : '')) . $suffix : '');
	return getHtmlTag('title', $attribs, $htmlspecialchars ? oldHtmlspecialchars(strip_tags($title)) : strip_tags($title), true) . "\n";
}
