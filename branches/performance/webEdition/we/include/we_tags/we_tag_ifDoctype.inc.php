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

function we_tag_ifDoctype($attribs, $content){
	$foo = attributFehltError($attribs, "doctypes", "ifDoctype");
	if ($foo) {
		print($foo);
		return "";
	}
	$match = we_getTagAttribute("doctypes", $attribs);

	$docAttr = we_getTagAttribute("doc", $attribs, "self");

	if ($docAttr == "listview" && isset($GLOBALS['lv'])) {
		$doctype = $GLOBALS['lv']->f('wedoc_DocType');
	} else {
		$doc = we_getDocForTag($docAttr);
		if ($doc->ClassName == "we_template") {
			return false;
		}
		$doctype = $doc->DocType;
	}
	$matchArr = makeArrayFromCSV($match);

	if (isset($doctype) && $doctype != false) {
		foreach ($matchArr as $match) {
			$matchID = f("SELECT ID FROM " . DOC_TYPES_TABLE . " WHERE DocType='".escape_sql_query($match)."'", "ID", new DB_WE());
			if ($matchID == $doctype) {
				return true;
			}
		}
	}
	return false;
}
