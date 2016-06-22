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
we_html_tools::protect();
$cmd2 = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 2);
switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)){
	case we_base_ContentTypes::IMAGE:
		$we_doc = new we_imageDocument();
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
		$contenttype = $we_doc->getElement("type");
		break;
	case we_base_ContentTypes::VIDEO:
		$we_doc = new we_document_video();
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
		$contenttype = 'video/' . str_replace('.', '', $we_doc->Extension);
		break;
	case we_base_ContentTypes::AUDIO:
		$we_doc = new we_document_audio();
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
		$contenttype = 'audio/' . str_replace('.', '', $we_doc->Extension);
		break;
	case we_base_ContentTypes::FLASH:
		$we_doc = new we_flashDocument();
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
		$contenttype = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
		break;
	case we_base_ContentTypes::APPLICATION:
		$we_doc = new we_otherDocument();
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$cmd2]);
		switch($we_doc->Extension){
			case ".zip":
				$contenttype = "application/zip";
				break;
			case ".doc":
				$contenttype = "application/msword";
				break;
			case ".xls":
				$contenttype = "application/vnd.ms-excel";
				break;
			case ".ppt":
				$contenttype = "application/vnd.ms-powerpoint";
				break;
			case ".pdf":
				$contenttype = "application/pdf";
				break;
			case ".sit":
				$contenttype = "application/x-stuffit";
				break;
			case ".hqx":
				$contenttype = "application/mac-binhex40";
				break;
			default:
				$contenttype = "application/octet-stream";
		}
		break;
	default:
		die('unsupported request');
}
header("Content-disposition: filename=" . $we_doc->Text);
header("Content-Type: $contenttype");
header("Pragma: no-cache");
header("Expires: 0");

$dataPath = $we_doc->getElement("data");
if(($tid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3))){ // create thumbnail
	if(we_base_imageEdit::gd_version()){
		$thumbObj = new we_thumbnail();
		$thumbObj->initByThumbID($tid, $we_doc->ID, $we_doc->Filename, $we_doc->Path, $we_doc->Extension, $we_doc->getElement("origwidth"), $we_doc->getElement("origheight"), $we_doc->getDocument());
		$thumbObj->getThumb($out);
		unset($thumbObj);
		echo $out;
		exit();
	}
}
session_write_close();
readfile($dataPath);
exit();
