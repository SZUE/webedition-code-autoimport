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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
//FIXME: send no perms img; but better an invalid picture, than access to unallowed images


$imageId = weRequest('int', 'id', 0);
$imagePath = weRequest('file', 'path', '');
$imageSizeW = weRequest('int', 'size', 0);
$imageSizeH = weRequest('int', 'size2', $imageSizeW);
$extension = weRequest('string', 'extension', '');


if(!$imageId || !$imagePath || !$imageSizeW || !$extension){
	exit();
}

$whiteList = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::IMAGE);

if(!in_array(strtolower($_REQUEST['extension']), $whiteList)){
	exit();
}

$imageExt = substr($_REQUEST['extension'], 1, strlen($_REQUEST['extension']));

$thumbpath = we_base_imageEdit::createPreviewThumb($imagePath, $imageId, $imageSizeW, $imageSizeH, substr($extension, 1));
if(file_exists($_SERVER['DOCUMENT_ROOT'] . $thumbpath)){
	header('Content-type: image/' . $imageExt);
	readfile($_SERVER['DOCUMENT_ROOT'] . $thumbpath);
}
