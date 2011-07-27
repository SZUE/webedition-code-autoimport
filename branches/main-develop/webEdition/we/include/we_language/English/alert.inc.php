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
 * Language file: alert.inc.php
 * Provides language strings.
 * Language: English
 */
$l_alert = array(
		FILE_TABLE => array(
				'in_wf_warning' => "The document has to be saved before it can be put in the workflow!\\nDo you want to save the document right now?",
				'not_im_ws' => "The file is not located inside your workspace!",
		),
		TEMPLATES_TABLE => array(
				'in_wf_warning' => "The template has to be saved before it can be put in the workflow!\\nDo you want to save the template right now?",
				'not_im_ws' => "The template is not located inside your workspace!",
		),
		'folder' => array(
				'not_im_ws' => "The folder is not located inside your workspace!",
		),
		'nonew' => array(
				'objectFile' => "You are not allowed to create new objects!<br>Either you have no permission or there is no class where one of your workspaces is valid!",
		),
		'wrong_file' => array(
				'image/*' => "The file could not be stored. Either it is not an image or your webspace is exhausted!",
				'application/x-shockwave-flash' => "The file could not be stored. Either it is not a Flash movie or your disk space is exhausted!",
				'video/quicktime' => "The file could not be stored. Either it is not a Quicktime movie or your disk space is exhausted!",
				'text/css' => "The file could not be stored. Either it is not a CSS file or your disk space is exhausted!",
		),
		'no_views' => array(
				'headline' => 'Attention',
				'description' => 'There is no view for this document available.',
		),
		'navigation' => array(
				'last_document' => 'You edit the last document.',
				'first_document' => 'You edit the first document.',
				'doc_not_found' => 'Could not find matching document.',
				'no_entry' => 'No entry found in history.',
				'no_open_document' => 'There is no open document.',
		),
		'delete_single' => array(
				'confirm_delete' => 'Delete this document?',
				'no_delete' => 'This document could not be deleted.',
				'return_to_start' => 'The document was deleted. \\nBack to seeMode startdocument.',
		),
		'move_single' => array(
				'return_to_start' => 'The document was moved. \\nBack to seeMode startdocument.',
				'no_delete' => 'This document could not be moved',
		),
		'notice' => "Notice",
		'warning' => "Warning",
		'error' => "Error",
		'noRightsToDelete' => "\\'%s\\' cannot be deleted! You do not have permission to perform this action!",
		'noRightsToMove' => "\\'%s\\' cannot be moved! You do not have permission to perform this action!",
		'delete_recipient' => "Do you really want to delete the selected email address?",
		'recipient_exists' => "That email address already exists!",
		'input_name' => "Enter a new email address!",
		'input_file_name' => "Enter a filename.",
		'max_name_recipient' => "An email address may only be 255 characters long!",
		'not_entered_recipient' => "No email address has been entered!",
		'recipient_new_name' => "Change email address!",
		'required_field_alert' => "The field '%s' is required and has to be filled!",
		'phpError' => "webEdition cannot be started!",
		'3timesLoginError' => "Login failed %s times! Please wait %s minutes and try again!",
		'popupLoginError' => "The webEdition window could not be opened!\\n\\nwebEdition can be started only when your browser does not block pop-up windows.",
		'publish_when_not_saved_message' => "The document has not yet been saved! Do you want to publish it anyway?",
		'template_in_use' => "The template is in use and cannot be removed!",
		'no_cookies' => "You have not activated cookies. Please activate cookies in your browser!",
		'doctype_hochkomma' => "Invalid name! Invalid characters are ' (apostrophe) , (comma) and \" (quote)!",
		'thumbnail_hochkomma' => "Invalid name! Invalid characters are ' (apostrophe) and , (comma)!",
		'can_not_open_file' => "The file %s could not be opened!",
		'no_perms_title' => "Permission denied!",
		'no_perms_action' => "You don't have the permission to perform this action.",
		'access_denied' => "Access denied!",
		'no_perms' => "Please contact the owner (%s) or an administrator<br>if you need access!",
		'temporaere_no_access' => "Access not possible!",
		'temporaere_no_access_text' => "The file \"%s\" is being edited by \"%s\" at the moment.",
		'file_locked_footer' => "This document is edited by \"%s\" at the moment.",
		'file_no_save_footer' => "You don't have the permissions to save this file.",
		'login_failed' => "Wrong user name and/or password!",
		'login_failed_security' => "webEdition could not be started!\\n\\nFor security reasons the login process was aborted, because the maximum time to log into webEdition has been exceeded!\\n\\nPlease login again.",
		'perms_no_permissions' => "You are not permitted to perform this action!",
		'no_image' => "The file you have selected is not an image!",
		'delete_ok' => "Files or directories successfully deleted!",
		'delete_cache_ok' => "Cache successfully deleted!",
		'nothing_to_delete' => "There is nothing marked for deletion!",
		'delete' => "Delete selected entries?\\nDo you want to continue?",
		'delete_cache' => "Delete cache for the selected entries?\\nDo you want to continue?",
		'delete_folder' => "Delete selected directory?\\nPlease note: When deleting a directory, all documents and directories within it are also automatically erased!\\nDo you want to continue?",
		'delete_nok_error' => "The file '%s' cannot be deleted.",
		'delete_nok_file' => "The file '%s' cannot be deleted.\\nIt is possibly write protected. ",
		'delete_nok_folder' => "The directory '%s' cannot be deleted.\\nIt is possible that it is write-protected.",
		'delete_nok_noexist' => "File '%s' does not exist!",
		'noResourceTitle' => "No Item!",
		'noResource' => "The document or directory does not exist!",
		'move_exit_open_docs_question' => "Before moving all %s must be closed.\\nIf you continue, the following %s will be closed, unsaved changes will not be saved.\\n\\n",
		'move_exit_open_docs_continue' => 'Continue?',
		'move' => "Move selected entries?\\nDo you want to continue?",
		'move_ok' => "Files successfully moved!",
		'move_duplicate' => "There are files with the same name in the target directory!\\nThe files cannot be moved.",
		'move_nofolder' => "The selected files cannot be moved.\\nIt isn't possible to move directories.",
		'move_onlysametype' => "The selected objects cannnot be moved.\\nObjects can only be moved in there own classdirectory.",
		'move_no_dir' => "Please choose a target directory!",
		'document_move_warning' => "After moving documents it is  necessary to do a rebuild.<br />Would you like to do this now?",
		'nothing_to_move' => "There is nothing marked to move!",
		'move_of_files_failed' => "One or more files couldn't moved! Please move these files manually.\\nThe following files are affected:\\n%s",
		'template_save_warning' => "This template is used by %s published documents. Should they be resaved? Attention: This procedure may take some time if you have many documents!",
		'template_save_warning1' => "This template is used by one published document. Should it be resaved?",
		'template_save_warning2' => "This template is used by other templates and documents, should they be resaved?",
		'thumbnail_exists' => 'This thumbnail already exists!',
		'thumbnail_not_exists' => 'This thumbnail does not exist!',
		'thumbnail_empty' => "You must enter a name for the new thumbnail!",
		'doctype_exists' => "This document type already exists!",
		'doctype_empty' => "You must enter a name for the new document type!",
		'delete_cat' => "Do you really want to delete the selected category?",
		'delete_cat_used' => "This category is in use and cannot be deleted!",
		'cat_exists' => "That category already exists!",
		'cat_changed' => "The category is in use! Resave the documents which are using the category!\\nShould the category be modified anyway?",
		'max_name_cat' => "A category name may only be 32 characters long!",
		'not_entered_cat' => "No category name has been entered!",
		'cat_new_name' => "Enter the new name for the category!",
		'we_backup_import_upload_err' => "An error occured while uploading the backup file! The maximum file size for uploads is %s. If your backup file exceeds this limit, please upload it into the directory webEdition/we_Backup via FTP and choose '" . g_l('backup', "[import_from_server]") . "'",
		'rebuild_nodocs' => "No documents match the selected attributes.",
		'we_name_not_allowed' => "The terms 'we' and 'webEdition' are reserved words and may not be used!",
		'we_filename_empty' => "No name has been entered for this document or directory!",
		'exit_multi_doc_question' => "Several open documents contain unsaved changes. If you continue all unsaved changes are discarded. Do you want to continue and discard all modifications?",
		'exit_doc_question_' . FILE_TABLE => "The document has been changed.<BR> Would you like to save your changes?",
		'exit_doc_question_' . TEMPLATES_TABLE => "The template has been changed.<BR> Would you like to save your changes?",
		'deleteTempl_notok_used' => "One or more of the templates are in use and could not be deleted!",
		'deleteClass_notok_used' => "One or more of the classes are in use and could not be deleted!",
		'delete_notok' => "Error while deleting!",
		'nothing_to_save' => "The save function is disabled at the moment!",
		'nothing_to_publish' => "The publish function is disabled at the moment!",
		'we_filename_notValid' => "Invalid filename\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)",
		'empty_image_to_save' => "The selected image is empty.\\n Continue?",
		'path_exists' => "The file or document %s cannot be saved because another document is already in its place!",
		'folder_not_empty' => "One or more directories are not completely empty and hence could not be erased! Erase the files by hand.\\n The following files are effected:\\n%s",
		'name_nok' => "The names must not contain characters like '<' or '>'!",
		'found_in_workflow' => "One or more selected entries are in the workflow process! Do you want to remove them from the workflow process?",
		'import_we_dirs' => "You are trying to import from a webEdition directory!\\n Those directories are used and protected by webEdition and therefore cannot be used for import!",
		'no_file_selected' => "No file has been choosen for upload!",
		'browser_crashed' => "The window could not be opened because of an error with your browser!  Please save your work and restart the browser.",
		'copy_folders_no_id' => "Please save the current directory first!",
		'copy_folder_not_valid' => "The same directory or one of the parent directories can not be copied!",
		'cockpit_not_activated' => 'The action could not be performed because the cockpit is not activated.',
		'cockpit_reset_settings' => 'Are you sure to delete the current Cockpit settings and reset the default settings?',
		'save_error_fields_value_not_valid' => 'The highlighted fields contain invalid data.\\nPlease enter valid data.',
		'eplugin_exit_doc' => "The document has been edited with extern editor. The connection between webEdition and extern editor will be closed and the content will not be synchronized anymore.\\nDo you want to close the document?",
		'delete_workspace_user' => "The directory %s could not be deleted! It is defined as workspace for the following users or groups:\\n%s",
		'delete_workspace_user_r' => "The directory %s could not be deleted! Within the directory there are other directories which are defined as workspace for the following users or groups:\\n%s",
		'delete_workspace_object' => "The directory %s could not be deleted! It is defined as workspace for the following objects:\\n%s",
		'delete_workspace_object_r' => "The directory %s could not be deleted! Within the directory there are other directories which are defined as workspace in the following objects:\\n%s",
		'field_contains_incorrect_chars' => "A field (of the type %s) contains invalid characters.",
		'field_input_contains_incorrect_length' => "The maximum length of a field of the type \'Text input\' is 255 characters. If you need more characters, use a field of the type \'Textarea\'.",
		'field_int_contains_incorrect_length' => "The maximum length of a field of the type \'Integer\' is 10 characters.",
		'field_int_value_to_height' => "The maximum value of a field of the type \'Integer\' is 2147483647.",
		'we_filename_notValid' => "Invalid file name\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)",
		'login_denied_for_user' => "The user cannot login. The user access is disabled.",
		'no_perm_to_delete_single_document' => "You have not the needed permissions to delete the active document.",
		'confirm' => array(
				'applyWeDocumentCustomerFiltersDocument' => "The document has been moved to a folder with divergent customer account policies. Should the settings of the folder be transmitted to this document?",
				'applyWeDocumentCustomerFiltersFolder' => "The directory has been moved to a folder with divergent customers account policies. Should the settings be adopted for this directory and all subelements? ",
		),
		'field_in_tab_notvalid_pre' => "The settings could not be saved, because the following fields contain invalid values:",
		'field_in_tab_notvalid' => ' - field %s on tab %s',
		'field_in_tab_notvalid_post' => 'Correct the fields before saving the settings.',
		'discard_changed_data' => 'There are unsaved changes that will be discarded. Are you sure?',
);


if (defined("OBJECT_FILES_TABLE")) {
	$l_alert = array_merge($l_alert, array(
			'in_wf_warning' => "The object has to be saved before it can be put in the workflow!\\nDo you want to save the document right now?",
			'in_wf_warning' => "The class has to be saved before it can be put in the workflow!\\nDo you want to save the class right now?",
			'exit_doc_question_' . OBJECT_TABLE => "The class has been changed.<BR> Would you like to save your changes?",
			'exit_doc_question_' . OBJECT_FILES_TABLE => "The object has been changed.<BR> Would you like to save your changes?",
					));
}
