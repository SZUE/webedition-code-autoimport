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

function we_tag_ifWorkspace($attribs, $content){
	$required_path = we_getTagAttribute('path', $attribs, "");
	$docAttr = we_getTagAttribute("doc", $attribs, "self");
	$doc = we_getDocForTag($docAttr);
	$id = we_getTagAttribute('id', $attribs);

	if (!$required_path) {
		$required_path = id_to_path($id);
	}

	if (!$required_path) {
		return false;
	}

	if (substr($required_path, 0, 1) != '/') {
		$required_path = '/' . $required_path;
	}

	return (strpos($doc->Path, $required_path) === 0);
}
