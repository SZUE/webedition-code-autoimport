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

function we_tag_ifHasChildren($attribs, $content){

	if (isset($GLOBALS["lv"])) {
		if (abs($GLOBALS["lv"]->f("ID")) > 0) {
			return abs(
					f(
							"SELECT COUNT(ID) AS ID FROM " . CATEGORY_TABLE . " WHERE ParentID='" . abs(
									$GLOBALS["lv"]->f("ID")) . "'",
							"ID",
							new DB_WE())) > 0;
		}
	}
	return false;
}
