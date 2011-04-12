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

function we_tag_ifSearch($attribs, $content){
	$name = we_getTagAttribute('name', $attribs, '0');
	$set = we_getTagAttribute('set', $attribs, 1, true);

	if ($set) {
		return isset($_REQUEST['we_lv_search_' . $name]);
	} else {
		return isset($_REQUEST['we_lv_search_' . $name]) && strlen(
				str_replace(
						'"',
						'',
						str_replace(
								'\\"',
								'',
								(isset($_REQUEST['we_lv_search_' . $name]) ? trim(
										$_REQUEST['we_lv_search_' . $name]) : ''))));
	}
}
