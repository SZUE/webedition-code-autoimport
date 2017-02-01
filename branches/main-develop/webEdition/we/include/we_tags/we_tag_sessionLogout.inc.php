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

function we_tag_sessionLogout(array $attribs, $content){
	if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		return $foo;
	}
	$id = weTag_getAttribute('id', $attribs, '', we_base_request::STRING);
	$id = ($id === 'self') ? $GLOBALS['WE_MAIN_DOC']->ID : $id;
	$row = getHash('SELECT Path,IsFolder FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id));

	$url = ($row ? $row['Path'] . ($row['IsFolder'] ? '/' : '') : '');
	$attribs = removeAttribs($attribs, ['id', '_name_orig']); //	not html - valid
	$attribs['href'] = $url . '?we_webUser_logout=1';

	return getHtmlTag('a', $attribs, $content);
}
