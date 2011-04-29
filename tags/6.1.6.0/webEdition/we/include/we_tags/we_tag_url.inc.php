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

function we_tag_url($attribs, $content){
	$foo = attributFehltError($attribs, "id", "url");
	if ($foo)
		return $foo;
	static $urls = array();
	$id = we_getTagAttribute("id", $attribs);
	if (isset($urls[$id])) { // do only work you have never done before
		return $urls[$id];
	}
	if ($id == '0') {
		$url = "/";
	} else {
	    if ($id=='self' || $id=='top'){
			$doc = we_getDocForTag($id, true); // check if we should use the top document or the  included document
			$testid = $doc->ID;
		} else {$testid = $id;}
		$row = getHash("SELECT Path,IsFolder,IsDynamic FROM " . FILE_TABLE . " WHERE ID=".abs($testid)."", new DB_WE());
		$url = isset($row["Path"]) ? ($row["Path"] . ($row["IsFolder"] ? "/" : "")) : "";
	}
	$urls[$id] = $url;
	return $url;

}
