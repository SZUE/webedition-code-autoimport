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
function we_tag_js($attribs){
	if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		return $foo;
	}
	$id = weTag_getAttribute('id', $attribs, 0, we_base_request::INT);
	$row = getHash('SELECT Path,IsFolder,IsDynamic,Published FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id));

	if(!$row){
		return '';
	}
	$url = $row['Path'] . ($row['IsFolder'] ? '/' : '');

	$attribs['type'] = 'text/javascript';
	$attribs['src'] = $url . '?m=' . $row['Published'];

	//	prepare $attribs for output:
	return getHtmlTag('script', removeAttribs($attribs, array('id')), '', true) . "\n";
}
