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
function we_tag_ifSearch($attribs){
	$name = weTag_getAttribute('name', $attribs, '0');
	$set = weTag_getAttribute('set', $attribs, true, true);

	if($set){
		return isset($_REQUEST['we_lv_search_' . $name]);
	} else{
		return isset($_REQUEST['we_lv_search_' . $name]) && strlen(
				str_replace(array('"' . '\\"'), '', (isset($_REQUEST['we_lv_search_' . $name]) ? trim($_REQUEST['we_lv_search_' . $name]) : ''))
		);
	}
}
