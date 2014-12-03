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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_ifWorkspace($attribs){
	$required_path = weTag_getAttribute('path', $attribs, array(), we_base_request::FILELISTA);
	$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);
	$doc = we_getDocForTag($docAttr);
	$id = weTag_getAttribute('id', $attribs, array(), we_base_request::INTLISTA);

	if(!$required_path){
		$required_path = id_to_path($id, FILE_TABLE, $GLOBALS['DB_WE'], false, true);
	}

	if(!$required_path){
		return false;
	}

	foreach($required_path as $path){
		if(strpos($doc->Path, $path) === 0){
			return true;
		}
	}

	return false;
}
