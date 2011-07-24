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

function we_tag_icon($attribs, $content){
	if (($foo=attributFehltError($attribs, 'id', 'icon'))) return $foo;
	$xml = we_getTagAttribute('xml', $attribs, '');
	$id = we_getTagAttribute('id', $attribs);
	$row = getHash('SELECT Path,IsFolder,IsDynamic FROM ' . FILE_TABLE . ' WHERE ID='.abs($id), new DB_WE());
	if (count($row)) {
		$url = $row['Path'] . ($row['IsFolder'] ? '/' : '');
		return getHtmlTag('link', array('rel' => 'shortcut icon', 'href' => $url, 'xml' => $xml));
	}
	return '';
}
