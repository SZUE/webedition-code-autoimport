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

function we_tag_ifVarSet($attribs, $content){
	$foo = attributFehltError($attribs, "name", "ifVarSet");
	if ($foo) {
		print($foo);
		return "";
	}

	$type = we_getTagAttribute("var", $attribs);
	$type = $type ? $type : we_getTagAttribute("type", $attribs);
	$doc = we_getTagAttribute("doc", $attribs);
	$name = we_getTagAttribute("name", $attribs);
	$formname = we_getTagAttribute("formname", $attribs, "we_global_form");
	$property = we_getTagAttribute("property", $attribs, "", true);
	$shopname = we_getTagAttribute('shopname', $attribs, '');

	return we_isVarSet($name, $type, $doc, $property, $formname, $shopname);
}
