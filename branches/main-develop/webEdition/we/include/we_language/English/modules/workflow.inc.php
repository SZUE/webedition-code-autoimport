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
 * Language file: workflow.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_workflow = array(
		'new_workflow' => "New workflow",
		'workflow' => "Workflow",
		'doc_in_wf_warning' => "The document is in workflow!",
		'message' => "Message",
		'in_workflow' => "Document in workflow",
		'decline_workflow' => "Decline document",
		'pass_workflow' => "Forward document",
		'no_wf_defined' => "No workflow has been defined for this document!",
		'document' => "Document",
		'del_last_step' => "Cannot delete last serial step!",
		'del_last_task' => "Cannot delete last parallel step!",
		'save_ok' => "Workflow is saved.",
		'delete_ok' => "Workflow is deleted.",
		'delete_nok' => "Delete failed!",
		'name' => "Name",
		'type' => "Type",
		'type_dir' => "Directory-based",
		'type_doctype' => "Document type/Category-based",
		'type_object' => "Object-based",
		'dirs' => "Directories",
		'doctype' => "Document type",
		'categories' => "Categories",
		'classes' => "Classes",
		'active' => "Workflow is active.",
		'step' => "Step",
		'and_or' => "AND&nbsp;/&nbsp;OR",
		'worktime' => "Worktime (H, 1min=0<b>.</b>016)", // TRANSLATE
		'specials' => "Specials", // TRANSLATE
		'EmailPath' => "Show the document path in the subject of notifications emails", // TRANSLATE
		'LastStepAutoPublish' => "After the last step (next step clicked), publish the document instead of decline ist", // TRANSLATE
		'user' => "User",
		'edit' => "Edit",
		'send_mail' => "Send mail",
		'select_user' => "Select user",
		'and' => " and ",
		'or' => " or ",
		'waiting_on_approval' => "Waiting for approval from %s.",
		'status' => "Status",
		'step_from' => "Step %s from %s",
		'step_time' => "Step time",
		'step_start' => "Step start date",
		'step_plan' => "End date",
		'step_worktime' => "Planed worktime",
		'current_time' => "Current time",
		'time_elapsed' => "Time elapsed",
		'time_remained' => "Time remaining",
		'todo_subject' => "Workflow task",
		'todo_next' => "There is a document waiting for you in the workflow.",
		'go_next' => "Next step",
		'new_step' => "Create additional serial step.",
		'new_task' => "Create additional parallel step.",
		'delete_step' => "Delete serial step.",
		'delete_task' => "Delete parallel step.",
		'save_question' => "All documents that are in the workflow will be removed from it.\\nAre you sure that you want to do this?",
		'nothing_to_save' => "Nothing to save!",
		'save_changed_workflow' => "Workflow has been changed.\\nDo you want to save your changes?",
		'delete_question' => "All workflow data will be deleated!\\nAre you sure that you want to do this?",
		'nothing_to_delete' => "Nothing to delete!",
		'user_empty' => "No defined users for step %s.",
		'folders_empty' => "Folder is not defined for workflow!",
		'objects_empty' => "Object is not defined for workflow!",
		'doctype_empty' => "Document type or category are not defined for workflow",
		'worktime_empty' => "Worktime is not defined for step %s!",
		'name_empty' => "Name is not defined for workflow!",
		'cannot_find_active_step' => "Cannot find active step!",
		'no_perms' => "No permissions!",
		'plan' => "(plan)",
		'todo_returned' => "The document has been returned from the workflow.",
		'description' => "Description",
		'time' => "Time",
		'log_approve_force' => "User has forcibly approved document.",
		'log_approve' => "User has approved document.",
		'log_decline_force' => "User has forcibly cancelled document workflow.",
		'log_decline' => "User has cancelled document workflow.",
		'log_doc_finished_force' => "Workflow has been forcibly finished.",
		'log_doc_finished' => "Workflow is finished.",
		'log_insert_doc' => "Document has been inserted into wokflow.",
		'logbook' => "Logbook",
		'empty_log' => "Empty logbook",
		'emty_log_question' => "Do you really want to empty the logbook?",
		'empty_log_ok' => "The logbook is now emtpy.",
		'log_is_empty' => "The logbook is emtpy.",
		'log_question_all' => "Clear all",
		'log_question_time' => "Clear older than",
		'log_question_text' => "Choose option:",
		'log_remove_doc' => "Document is removed from workflow.",
		'action' => "Action",
		'auto_approved' => "Document has been automatically approved.",
		'auto_declined' => "Document has been automatically declined.",
		'auto_published' => "Document has been automatically published.", // TRANSLATE

		'doc_deleted' => "Document has been deleted!",
		'ask_before_recover' => "There are still documents/objects in the workflow process! Do you want to remove them from the workflow process?",
		'double_name' => "Workflow name already exists!",
		'more_information' => "More information",
		'less_information' => "Less information",
		'no_wf_defined_object' => "No workflow has been defined for this object!",
		'tblFile' => array(
				'messagePath' => "Document",
				'in_workflow_ok' => "The document was successfully placed in the workflow!",
				'in_workflow_notok' => "The document cannot be placed in the workflow!",
				'pass_workflow_ok' => "The document was successfully passed on!",
				'pass_workflow_notok' => "The document cannot be passed on!",
				'decline_workflow_ok' => "The document was returned to the author!",
				'decline_workflow_notok' => "The document cannot be returned to the author!",
			),	
'tblObjectFiles' => array(
			'in_workflow_ok' => "The object was successfully placed in the workflow!",
			'in_workflow_notok' => "The object cannot be placed in the workflow!",
			'pass_workflow_ok' => "The object was successfully passed on!",
			'pass_workflow_notok' => "The object cannot be passed on!",
			'decline_workflow_ok' => "The object was returned to the author!",
			'decline_workflow_notok' => "The object cannot be returned to the author!",
	),
);
