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

function we_tag_registeredUser($attribs, $content){

	$id = we_getTagAttribute("id", $attribs);
	$show = we_getTagAttribute("show", $attribs);
	$docAttr = we_getTagAttribute("doc", $attribs);

	if (ereg("^field:(.+)$", $id, $regs)) {
		$doc = we_getDocForTag($docAttr);
		$field = $regs[1];
		if (strlen($field))
			$id = $doc->getElement($field);
	}
	if ($id) {
		$db = new DB_WE();
		$h = getHash("SELECT * FROM " . CUSTOMER_TABLE . " WHERE id='$id'", $db);
		if ($show) {
			preg_match_all("|%([^ ]+) ?|i", $show, $foo, PREG_SET_ORDER);
			for ($i = 0; $i < sizeof($foo); $i++) {
				$show = str_replace("%" . $foo[$i][1], $h[$foo[$i][1]], $show);
			}
			return $show;
		} else {
			return $h["Username"];
		}
	}
	return "";
}
