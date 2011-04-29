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

function we_tag_title($attribs, $content){
	$htmlspecialchars = we_getTagAttribute('htmlspecialchars', $attribs, '', true);
	$prefix=we_getTagAttribute('prefix', $attribs, '');
	$suffix=we_getTagAttribute('suffix', $attribs, '');
	$delimiter=we_getTagAttribute('delimiter', $attribs, '');

	$attribs = removeAttribs($attribs, array('htmlspecialchars','prefix','suffix','delimiter'));
	if ($GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PROPERTIES && $GLOBALS['we_doc']->InWebEdition) { //	normally meta tags are edited on property page
		return '<?php	$GLOBALS["meta"]["Title"]["default"] = "' . str_replace('"', '\"', $content) . '"; ?>';
	} else {
		$title = ($GLOBALS['TITLE'] ? $GLOBALS['TITLE'] : $content);
		$title = ($prefix!=''?$prefix.($title!=''?$delimiter:''):'').$title.($suffix!=''?($title!=''?$delimiter:($prefix!=''?$delimter:'')).$suffix:'');
		return getHtmlTag('title',$attribs,$htmlspecialchars ? htmlspecialchars(strip_tags($title)) : strip_tags($title),true) . "\n";
	}
}
