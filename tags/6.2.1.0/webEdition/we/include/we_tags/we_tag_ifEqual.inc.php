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


function we_tag_ifEqual($attribs, $content){
	$foo = attributFehltError($attribs, "name", "ifEqual");
	if ($foo) {
		print($foo);
		return "";
	}
	$name = we_getTagAttribute("name", $attribs);
	$eqname = we_getTagAttribute("eqname", $attribs);
	$value = we_getTagAttribute("value", $attribs);

	if (!$eqname) {
		$foo = attributFehltError($attribs, "value", "ifEqual");
		if ($foo) {
			print($foo);
			return "";
		}
		return ($GLOBALS["we_doc"]->getElement($name) == $value);
	}

	$foo = attributFehltError($attribs, "eqname", "ifEqual");
	if ($foo) {
		print($foo);
		return "";
	}
	if ($GLOBALS["we_doc"]->getElement($name) && $GLOBALS["WE_MAIN_DOC"]->getElement($eqname)) {
		return ($GLOBALS["we_doc"]->getElement($name) == $GLOBALS["WE_MAIN_DOC"]->getElement($eqname));
	} else {
		if (isset($GLOBALS[$eqname])) {
			return $GLOBALS[$eqname] == $GLOBALS["we_doc"]->getElement($name);
		} else {
			return false;
		}
	}
}
