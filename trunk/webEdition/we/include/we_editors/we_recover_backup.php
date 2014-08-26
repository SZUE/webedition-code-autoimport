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

we_html_tools::protect();

$what = we_base_request::_(we_base_request::STRING,'pnt','frameset');
$step = we_base_request::_(we_base_request::INT,'step',1);
$weBackupWizard = new we_backup_wizard(WE_INCLUDES_DIR . 'we_editors/we_recover_backup.php', we_backup_wizard::RECOVER);

//FIXME: delete condition when new uploader is stable
if(!we_fileupload_include::USE_LEGACY_FOR_BACKUP){
	if((!defined('FILE_UPLOAD_USE_LEGACY') || FILE_UPLOAD_USE_LEGACY == false) && ($what == 'cmd' || (($what == 'body' || $what == 'busy')&& $step == 3))){
		$fileUploader = new we_fileupload_include('we_upload_file', 'body', '', 'we_form', '', 'top.body.startImport(true)', '', 400, true, true, 200, '', 'xml, gz, tgz');
		$fileUploader->setAction($weBackupWizard->frameset . '?pnt=cmd&operation_mode=import');
		$fileUploader->setInternalProgress(array('width' => 300));
		$fileUploader->setDimensions(array('width' => 500, 'dragHeight' => 60, 'marginTop' => 5));
		$fileUploader->setFileNameTemp(array('path' => $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/'), we_fileupload_include::USE_FILENAME_FROM_UPLOAD);
		$weBackupWizard->setFileUploader($fileUploader);
	}
}

switch($what){
	case 'frameset':
		print $weBackupWizard->getHTMLFrameset();
		break;
	case 'body':
		print $weBackupWizard->getHTMLStep($step);
		break;
	case 'cmd':
		print $weBackupWizard->getHTMLCmd();
		break;
	case 'busy':
		print $weBackupWizard->getHTMLBusy();
		break;
	case 'extern':
		print $weBackupWizard->getHTMLExtern();
		break;
	case 'checker':
		print $weBackupWizard->getHTMLChecker();
		break;
	default:
		t_e(__FILE__ . ' unknown reference: ' . $what);
}