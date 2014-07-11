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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$wizard = new we_import_wizard();

we_html_tools::protect();

$what = we_base_request::_(we_base_request::STRING,"pnt",'wizframeset');
$type = we_base_request::_(we_base_request::STRING,"type",'');
$step = we_base_request::_(we_base_request::INT,"step", 0);
$mode = we_base_request::_(we_base_request::INT,"mode",0);

//FIXME: delete condition and else branch when new uploader is stable
if(!we_fileupload_include::USE_LEGACY_FOR_WEIMPORT){
	if($type && (!defined('FILE_UPLOAD_USE_LEGACY') || FILE_UPLOAD_USE_LEGACY == false) &&
			((($step == 1 || $step == 2) && $what == 'wizbody') || ($what == 'wizbusy'))){

		switch($type){
			case we_import_functions::TYPE_GENERIC_XML:
				$fileNameTempGxml = array('prefix' => 'we_xml_', 'postfix' => '.xml', 'path' => TEMP_DIR, 'missingDocRoot' => we_fileupload_include::MISSING_DOC_ROOT);
			case we_import_functions::TYPE_WE_XML: 
				$name = 'uploaded_xml_file';
				$acceptedMime = 'text/xml';
				$acceptedExt = 'xml';
				$fileNameTemp = array('prefix' => '', 'postfix' => '_w.xml', 'path' => TEMP_DIR, 'missingDocRoot' => we_fileupload_include::MISSING_DOC_ROOT);
				break;
			case we_import_functions::TYPE_CSV:
				$name = 'uploaded_csv_file';
				$acceptedMime = '';
				$acceptedExt = 'csv';
				$fileNameTemp = array('prefix' => 'we_csv_', 'postfix' => '.csv', 'path' => TEMP_DIR, 'missingDocRoot' => we_fileupload_include::MISSING_DOC_ROOT);
				break;
			default:
				break;
		}

		$wizard->fileUploader = new we_fileupload_include($name, 'top.wizbody', "self.document.forms['we_form'].elements['v[rdofloc]'][1].checked=true;", 330, true, true, $acceptedMime, $acceptedExt, '', '', array(), -1);
		$wizard->fileUploader->setAction($wizard->path . '?pnt=wizbody&step=1&type=' . $type);
		$wizard->fileUploader->setFileNameTemp($type == we_import_functions::TYPE_GENERIC_XML ? $fileNameTempGxml : $fileNameTemp);
	}
}

switch($what){
	case "wizframeset":
		print $wizard->getWizFrameset();
		break;
	case "wizbody":
		print $wizard->getWizBody($type, $step, $mode);
		break;
	case "wizbusy":
		print $wizard->getWizBusy();
		break;
	case "wizcmd":
		print $wizard->getWizCmd();
		break;
}