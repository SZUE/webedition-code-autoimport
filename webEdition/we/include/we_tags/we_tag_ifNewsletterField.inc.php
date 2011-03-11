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


function we_tag_ifNewsletterField($attribs,$content) {
	$foo = attributFehltError($attribs, "type", "ifNewsletterField", true);
	if ($foo) {
		print($foo);
		return "";
	}
	$foo = attributFehltError($attribs, "match", "ifNewsletterField");
	if ($foo) {
		print($foo);
		return "";
	}
	$match = we_getTagAttributeTagParser("match",$attribs,'',false,false,true);
	$atts = removeAttribs($attribs,array('match'));
	if (we_tag('newsletterField',$atts, "")==$match) {
		return true; 
	} else {
		return false;
	}
}
