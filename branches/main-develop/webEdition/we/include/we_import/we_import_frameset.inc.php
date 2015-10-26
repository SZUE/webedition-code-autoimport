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

$wizard = new we_import_wizard();

$what = we_base_request::_(we_base_request::STRING, "pnt", 'wizframeset');
$type = we_base_request::_(we_base_request::STRING, "type", '');
$step = we_base_request::_(we_base_request::INT, "step", 0);
$mode = we_base_request::_(we_base_request::INT, "mode", 0);

if($type && ($step == 1) && $what === 'wizbody'){
	$acceptedMime = $acceptedExt = array();
	switch($type){
		case we_import_functions::TYPE_GENERIC_XML:
			$name = 'uploaded_xml_file';
			$acceptedMime = array('text/xml');
			$acceptedExt = array('xml');
			$genericFileNameTemp = TEMP_DIR . 'we_xml_' . we_fileupload::REPLACE_BY_UNIQUEID . '.xml';
			break;
		case we_import_functions::TYPE_WE_XML:
			$name = 'uploaded_xml_file';
			$acceptedMime = array('text/xml');
			$acceptedExt = array('xml');
			$genericFileNameTemp = TEMP_DIR . we_fileupload::REPLACE_BY_UNIQUEID . '_w.xml';
			break;
		case we_import_functions::TYPE_CSV:
			$name = 'uploaded_csv_file';
			$acceptedExt = array('csv', 'txt');
			$genericFileNameTemp = TEMP_DIR . 'we_csv_' . we_fileupload::REPLACE_BY_UNIQUEID . '.csv';
			break;
		default:
			break;
	}

	$wizard->fileUploader = new we_fileupload_ui_base($name);
	$wizard->fileUploader->setTypeCondition('accepted', $acceptedMime, $acceptedExt);
	$wizard->fileUploader->setCallback('top.wizbody.handle_eventNext()');
	$wizard->fileUploader->setExternalUiElements(array('contentName' => 'wizbody', 'btnUploadName' => 'next_btn'));
	$wizard->fileUploader->setFileSelectOnclick("self.document.we_form.elements['v[rdofloc]'][1].checked=true;");
	$wizard->fileUploader->setInternalProgress(array('isInternalProgress' => true, 'width' => 200));
	$wizard->fileUploader->setGenericFileName($genericFileNameTemp);
	$wizard->fileUploader->setDimensions(array('width' => 410, 'marginTop' => 12));
}

echo $wizard->getHTML($what, $type, $step, $mode);
