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

function we_tag_ifCat($attribs, $content){

	$categories = we_getTagAttribute("categories", $attribs);
	$category = we_getTagAttribute("category", $attribs);

	if (strlen($categories) == 0 && strlen($category) == 0) {
		$foo = attributFehltError($attribs, "categories", "ifCat");
		if ($foo) {
			print($foo);
			return "";
		}
	}

	$parent = we_getTagAttribute("parent", $attribs, "", true);

	$docAttr = we_getTagAttribute("doc", $attribs, "self");

	$match = $categories ? $categories : $category;
	$db = new DB_WE();
	$matchArray = makeArrayFromCSV($match);

	if ($docAttr == 'listview' && isset($GLOBALS['lv'])) {
		$DocCatsPaths = id_to_path($GLOBALS['lv']->f('wedoc_Category'), CATEGORY_TABLE, $db, true, false, $parent);
	} else {
		$doc = we_getDocForTag($docAttr);
		$DocCatsPaths = id_to_path($doc->Category, CATEGORY_TABLE, $db, true, false, $parent);
	}

	foreach ($matchArray as $match) {

		if (substr($match, 0, 1) != "/") {
			$match = "/" . $match;
		}
		if ($parent) {
			if (strpos($DocCatsPaths, ',' . $match . ',') !== false || strpos($DocCatsPaths, ',' . $match . '/') !== false) {
				return true;
			}
		} else {
			if (!(strpos($DocCatsPaths, "," . $match . ",") === false)) {
				return true;
			}
		}
	}
	return false;
}
