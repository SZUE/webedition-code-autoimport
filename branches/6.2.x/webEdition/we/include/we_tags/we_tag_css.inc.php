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

function we_tag_css($attribs, $content){

	$foo = attributFehltError($attribs, "id", "css");
	if ($foo)
		return $foo;
	$id = we_getTagAttribute("id", $attribs);
	$rel = we_getTagAttribute("rel", $attribs, "stylesheet");

	$row = getHash("SELECT Path,IsFolder,IsDynamic FROM " . FILE_TABLE . " WHERE ID=".abs($id)."", new DB_WE());
	if (count($row)) {
		$url = $row["Path"] . ($row["IsFolder"] ? "/" : "");

		//	remove not needed elements
		$attribs = removeAttribs($attribs, array(
			"id", "rel"
		));

		$attribs["rel"] = $rel;
		$attribs["type"] = "text/css";
		$attribs["href"] = $url;
		

		return getHtmlTag("link", $attribs). "\n";
	}
	return "";
}
