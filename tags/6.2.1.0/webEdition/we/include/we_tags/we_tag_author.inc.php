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

function we_tag_author($attribs, $content){
	// attributes
	$type = we_getTagAttribute("type", $attribs);
	$creator = we_getTagAttribute("creator", $attribs, '', true);
	$docAttr = we_getTagAttribute("doc", $attribs);

	$doc = we_getDocForTag($docAttr, true);

	$foo = getHash(
			"SELECT Username,First,Second FROM " . USER_TABLE . " WHERE ID='" . abs($creator ? $doc->CreatorID : $doc->ModifierID) . "'",
			new DB_WE());

	switch ($type) {
		case "name" :
			$out = trim(($foo["First"] ? ($foo["First"] . " ") : "") . $foo["Second"]);
			if (!$out) {
				$out = $foo["Username"];
			}
			return $out;
		case "initials" :
			$out = trim(
					($foo["First"] ? substr($foo["First"], 0, 1) : "") . ($foo["Second"] ? substr(
							$foo["Second"],
							0,
							1) : ""));
			if (!$out) {
				$out = $foo["Username"];
			}
			return $out;
		default :
			return $foo["Username"];
	}
}
