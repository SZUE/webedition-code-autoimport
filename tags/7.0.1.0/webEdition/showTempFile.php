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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
session_write_close();

$filename = realpath(WEBEDITION_PATH . we_base_request::_(we_base_request::FILE, 'file'));
$keepBin = we_base_request::_(we_base_request::BOOL, 'binary');
if(strpos($filename, realpath(WE_INCLUDES_PATH)) !== false){
	//nobody should read inside include directory
	return;
}

if(!file_exists($filename)){
	return;
}
$isCompressed = $keepBin ? false : we_base_file::isCompressed($filename);
$allfile = explode('.', $filename);

if(!$keepBin && ($mimetype = we_base_util::getMimeType(end($allfile), $filename, we_base_util::MIME_BY_HEAD_THEN_EXTENSION, true))){
	switch($mimetype){
		case 'text/plain': //let the browser decide
		case 'application/x-empty':
			break;
		default:
			header('Content-Type: ' . $mimetype . (($charset = we_base_request::_(we_base_request::STRING, 'charset')) ? '; charset=' . $charset : ''));
	}
} else {
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="' . ($keepBin ? $filename : basename($filename, '.gz')) . '"');
	$isCompressed = false;
}

if($isCompressed){
	readgzfile($filename);
} else {
	readfile($filename);
}
