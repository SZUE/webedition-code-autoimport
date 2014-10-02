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
$filename = WEBEDITION_PATH . we_base_request::_(we_base_request::FILE, 'file');
if(strpos($filename, WE_INCLUDES_PATH) !== false){
	//nobody should read inside include directory
	return;
}
if(file_exists($filename)){
	$isCompressed = we_base_file::isCompressed($filename);
	if(function_exists('finfo_open')){
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimetype = finfo_buffer($finfo, we_base_file::loadPart($filename, 0, 8192, $isCompressed));
	} else {
		$mimetype = '';
		if(function_exists('getimagesizefromstring')){
			$mysize = getimagesizefromstring(we_base_file::load($filename, 0, 8192, $isCompressed));
			if(isset($mysize['mime'])){
				$mimetype = $mysize['mime'];
			}
		}
	}
	if($mimetype){
		switch($mimetype){
			case 'text/plain': //let the browser decide
			case 'application/x-empty':
				break;
			default:
				header('Content-Type: ' . $mimetype);
		}
	}

	if($isCompressed){
		readgzfile($filename);
	} else {
		readfile($filename);
	}
}