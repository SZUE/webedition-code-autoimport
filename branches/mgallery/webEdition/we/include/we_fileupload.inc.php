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
if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) != 'do_upload_file'){
	exit();
}

$cmd1 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);

switch($cmd1){
	case 'binaryDoc' :
		t_e('instance of we_fileupload');
		$contentType = we_base_request::_(we_base_request::STRING, 'we_doc_ct', '');
		$ext = we_base_request::_(we_base_request::STRING, 'we_doc_ext', '');
		$fileUpload = new we_fileupload_ui_editor($contentType, $ext, 'horizontal');
		$fileUpload->processFileRequest();
		break;
	case 'tag' :
		//Prepared for userInput type img
		/*
		$fileUpload = new we_fileupload_tag('we_File');
		$fileUpload->processFileRequest();
		 * 
		 */
	default:
		//do nothing
}
