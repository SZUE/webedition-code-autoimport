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
	$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, we_base_request::BOOL);
	$prefix = weTag_getAttribute('prefix', $attribs, '', we_base_request::RAW_CHECKED);
	$suffix = weTag_getAttribute('suffix', $attribs, '', we_base_request::RAW_CHECKED);
	$delimiter = weTag_getAttribute('delimiter', $attribs, '', we_base_request::RAW_CHECKED);

	$attribs = removeAttribs($attribs, array('htmlspecialchars', 'prefix', 'suffix', 'delimiter'));
	$title = !empty($GLOBALS['TITLE']) ? $GLOBALS['TITLE'] : '';
	if(!$title && $content){
		ob_start();
		//FIXME:eval
		eval('?>' . $content);
		$title = ob_get_clean();
	}

	if(!empty($GLOBALS['we_editmode'])){
		//set meta data & exit
		$GLOBALS['meta']['Title']['default'] = $title;
		return;
	}

	$title = ($prefix ? $prefix . ($title ? $delimiter : '') : '') . $title . ($suffix ? ($title ? $delimiter : ($prefix ? $delimiter : '')) . $suffix : '');
	return getHtmlTag('title', $attribs, $htmlspecialchars ? oldHtmlspecialchars(strip_tags($title)) : strip_tags($title), true) . "\n";
}
