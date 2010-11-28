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

	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/navigation/class/weNavigationItems.class.php');

function we_tag_navigation($attribs, $content = ''){
	$parentid = we_getTagAttribute("parentid", $attribs, -1);
	$id = we_getTagAttribute("id", $attribs, 0);
	$name = we_getTagAttribute("navigationname", $attribs, "default");

	if (isset($GLOBALS['initNavigationFromSession']) && $GLOBALS['initNavigationFromSession']) {

		$GLOBALS['we_navigation'][$name] = new weNavigationItems();
		$GLOBALS['we_navigation'][$name]->initByNavigationObject($parentid == -1 ? true : false);

	} else {

		$GLOBALS['we_navigation'][$name] = new weNavigationItems();

		if (!$GLOBALS['we_navigation'][$name]->initFromCache(($id ? $id : ($parentid != -1 ? $parentid : 0)), false)) {

			$GLOBALS['we_navigation'][$name]->initById(
					$id ? $id : ($parentid != -1 ? $parentid : 0),
					false,
					$id ? true : ($parentid != -1 ? false : true));
		}

	}
}
