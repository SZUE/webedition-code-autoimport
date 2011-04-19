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

require_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

protect();

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/lib/we/core/autoload.php");

$filename = $_SERVER['DOCUMENT_ROOT'].$_GET['file'];
$mimetype='';
if (file_exists ($filename)){
	if(function_exists('mime_content_type')){
		$mimetype = mime_content_type($filename);
    } else {
		if(function_exists('getimagesize')){
			$mysize=getimagesize($filename);
			if(isset($mysize['mime'])){
				$mimetype=$mysize['mime'];
			}
		}
	}
	if ($mimetype){
		header('Content-Type: '.$mimetype);
	}
	ob_clean();
    flush();

	readfile($filename);
}
