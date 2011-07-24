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

function we_tag_ifNoJavaScript($attribs, $content){
	$foo = attributFehltError($attribs, 'id', 'ifNoJavaScript');
	if ($foo) {
		print($foo);
		return '';
	}
	$id = we_getTagAttribute('id', $attribs);
	$row = getHash('SELECT Path,IsFolder,IsDynamic FROM ' . FILE_TABLE . ' WHERE ID='.abs($id), new DB_WE());
	$url = $row['Path'] . ($row['IsFolder'] ? '/' : '');
	$attr = we_make_attribs($attribs, 'id');
	return '<noscript><meta http-equiv="refresh" content="0;URL=' . $url . '"></noscript>';
}
