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
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/**
 * Language file: SEEM.inc.php
 * Provides language strings.
 * Language: English
 */
$l_SEEM = array(
		'ext_doc_selected' => "You have selected a link which points to a document that is not administered by webEdition. Continue?",
		'ext_document_on_other_server_selected' => "You have chosen a link which points to a document on another Web server.\\nThis will open in a new browser window. Continue?",
		'ext_form_target_other_server' => "You are about to submit a form to another Web server.\\nThis will open in a new window. Continue? ",
		'ext_form_target_we_server' => "The form will send data to a document, which is not not administered by webEdition.\\nContinue?",
		'ext_doc' => "The current document: <b>%s</b> is <u>not</u> editable with webEdition.",
		'ext_doc_not_found' => "Could not find the selected page <b>%s</b>.",
		'ext_doc_tmp' => "This document was not opened correctly by webEdition. Please use the normal navigation of the website to reach your desired document.",
		'info_ext_doc' => "No webEdition link",
		'info_doc_with_parameter' => "Link with parameter",
		'link_does_not_work' => "This link is deactivated in the preview mode. Please use the main navigation to move on the page.",
		'info_link_does_not_work' => "Deactivated.",
		'open_link_in_SEEM_edit_include' => "You are about to change the content of the webEdition main window. This window will be closed. Continue?",
//  Used in we_info.inc.php
		'start_mode' => "Mode",
		'start_mode_normal' => "Normal",
		'start_mode_seem' => "seeMode",
//	When starting webedition in SEEMode
		'start_with_SEEM_no_startdocument' => "You have not the required permissions to open the Cockpit. Your administrator can assign a valid start document in the user settings to you.",
		'only_seem_mode_allowed' => "You do not have the required permissions to start webEdition in normal mode.\\nStarting seeMode instead ...",
//	Workspace - the SEEM startdocument
		'workspace_seem_startdocument' => "Start document<br>for seeMode",
//	Desired document is locked by another user
		'try_doc_again' => "Try again",
//	no permission to work with document
		'no_permission_to_work_with_document' => "You do not have permission to edit this document.",
//	started seem with no startdocument - can select startdocument.
		'question_change_startdocument' => "You have not the required permissions to open the Cockpit. Do you want to select a valid start document in the preferences dialogue now?",
//	started seem with no startdocument - can select startdocument.
		'no_permission_to_edit_document' => "You do not have permission to edit this document.",
		'confirm' => array(
				'change_to_preview' => "Do you want switch back to preview?",
		),
		'alert' => array(
				'changed_include' => "An included file has been changed. Main document is reloaded.",
				'close_include' => "This file is no webEdition document. The include window is closed.",
		),
);
