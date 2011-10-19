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
		'new_workflow' => "New workflow", // TRANSLATE
		'workflow' => "Workflow", // TRANSLATE

		'doc_in_wf_warning' => "The document is in workflow!", // TRANSLATE
		'message' => "Wiadomość",
		'in_workflow' => "Dokument w opracowaniu",
		'decline_workflow' => "Odrzuć dokument",
		'pass_workflow' => "Przekaż dokument dalej",
		'no_wf_defined' => "Dla tego dokumentu nie zdefiniowano operacji!",
		'document' => "Document", // TRANSLATE

		'del_last_step' => "Nie można było usunąć ostatniego poziomu szeregowego!",
		'del_last_task' => "Nie można było usunąć ostatniego poziomu równoległego!",
		'save_ok' => "Nie można było zapisać pomyślnie operacji!",
		'delete_ok' => "Operacja została zapisana pomyślnie!",
		'delete_nok' => "Nie można było usunąć operacji!",
		'name' => "Nazwa",
		'type' => "Typ",
		'type_dir' => "Skatalogowany",
		'type_doctype' => "DocumentType/skategoryzowany",
		'type_object' => "Zobiektyzowany",
		'dirs' => "Katalogi",
		'doctype' => "Typ dokumentu",
		'categories' => "Kategorie",
		'classes' => "Klasy",
		'active' => "Workflow jest aktywny",
		'step' => "Poziom",
		'and_or' => "I&nbsp;/&nbsp;LUB",
		'worktime' => "Worktime (H, 1min=0<b>.</b>016)", // TRANSLATE
		'specials' => "Specials", // TRANSLATE
		'EmailPath' => "Show the document path in the subject of notifications emails", // TRANSLATE
		'LastStepAutoPublish' => "After the last step (next step clicked), publish the document instead of decline ist", // TRANSLATE
		'user' => "Nazwa użytkownika ",
		'edit' => "Edytuj",
		'send_mail' => "Wyślij email",
		'select_user' => "Select user", // TRANSLATE

		'and' => " and ", // TRANSLATE
		'or' => " or ", // TRANSLATE

		'waiting_on_approval' => "Czekam na zezwolenie z  %s",
		'status' => "Status", // TRANSLATE
		'step_from' => "Poziom %s z %s",
		'step_time' => "Step time", // TRANSLATE
		'step_start' => "Godzina startu",
		'step_plan' => "Upłynęło w dniu",
		'step_worktime' => "Planowany czas",
		'current_time' => "Aktualny czas",
		'time_elapsed' => "Czas zużyty (h:m:s)",
		'time_remained' => "Czas pozostały (h:m:s)",
		'todo_subject' => "Zadanie operacji",
		'todo_next' => "Następny poziom operacji.",
		'go_next' => "Next step", // TRANSLATE

		'new_step' => "Create additional serial step.", // TRANSLATE
		'new_task' => "Create additional parallel step.", // TRANSLATE

		'delete_step' => "Delete serial step.", // TRANSLATE
		'delete_task' => "Delete parallel step.", // TRANSLATE

		'save_question' => "All documents that are in the workflow will be removed from it.\\nAre you sure that you want to do this?", // TRANSLATE
		'nothing_to_save' => "Nothing to save!", // TRANSLATE
		'save_changed_workflow' => "Workflow has been changed.\\nDo you want to save your changes?", // TRANSLATE

		'delete_question' => "All workflow data will be deleated!\\nAre you sure that you want to do this?", // TRANSLATE
		'nothing_to_delete' => "Nothing to delete!", // TRANSLATE

		'user_empty' => "No defined users for step %s.", // TRANSLATE
		'folders_empty' => "Folder is not defined for workflow!", // TRANSLATE
		'objects_empty' => "Object is not defined for workflow!", // TRANSLATE
		'doctype_empty' => "Document type or category are not defined for workflow", // TRANSLATE
		'worktime_empty' => "Worktime is not defined for step %s!", // TRANSLATE
		'name_empty' => "Name is not defined for workflow!", // TRANSLATE
		'cannot_find_active_step' => "Cannot find active step!", // TRANSLATE

		'no_perms' => "No permissions!", // TRANSLATE
		'plan' => "(plan)", // TRANSLATE

		'todo_returned' => "Jeden dokument został odrzucony.",
		'description' => "Description", // TRANSLATE
		'time' => "Time", // TRANSLATE

		'log_approve_force' => "User has forcibly approved document.", // TRANSLATE
		'log_approve' => "User has approved document.", // TRANSLATE
		'log_decline_force' => "User has forcibly cancelled document workflow.", // TRANSLATE
		'log_decline' => "User has cancelled document workflow.", // TRANSLATE
		'log_doc_finished_force' => "Workflow has been forcibly finished.", // TRANSLATE
		'log_doc_finished' => "Workflow is finished.", // TRANSLATE
		'log_insert_doc' => "Document has been inserted into wokflow.", // TRANSLATE

		'logbook' => "Logbook", // TRANSLATE
		'empty_log' => "Empty logbook", // TRANSLATE
		'emty_log_question' => "Czy rzeczywiście chcesz wyczyścić całą książkę logów?",
		'empty_log_ok' => "The logbook is now emtpy.", // TRANSLATE
		'log_is_empty' => "The logbook is emtpy.", // TRANSLATE

		'log_question_all' => "Clear all", // TRANSLATE
		'log_question_time' => "Clear older than", // TRANSLATE
		'log_question_text' => "Choose option:", // TRANSLATE

		'log_remove_doc' => "Document is removed from workflow.", // TRANSLATE
		'action' => "Action", // TRANSLATE

		'auto_approved' => "Document has been automatically approved.", // TRANSLATE
		'auto_declined' => "Document has been automatically declined.", // TRANSLATE
		'auto_published' => "Dokument has been automatically published.", // TRANSLATE

		'doc_deleted' => "Document has been deleted!", // TRANSLATE
		'ask_before_recover' => "There are still documents/objects in the workflow process! Do you want to remove them from the workflow process?", // TRANSLATE

		'double_name' => "Workflow name already exists!", // TRANSLATE

		'more_information' => "Pozostałe informacje",
		'less_information' => "Mniej informacji",
		'no_wf_defined_object' => "Dla tego obiektu nie został zdefiniowany żaden workflow!",
		'tblFile' => array(
				'messagePath' => "Dokument",
				'in_workflow_ok' => "Dokument został pomyślnie przekazany do opracowania!",
				'in_workflow_notok' => "Nie można było przekazać dokumentu do opracowania!",
				'pass_workflow_ok' => "Dokument został pomyślnie przekazany dalej!",
				'pass_workflow_notok' => "Nie można było przekazać dalej dokumentu!",
				'decline_workflow_ok' => "Dokument został pomyślnie przekazany autorowi!",
				'decline_workflow_notok' => "Nie można było zwrócić dokumentu autorowi!",
				),
	'tblObjectFiles' => array(
			'messagePath' => "Obiekt",
			'in_workflow_ok' => "Obiekt został pomyślnie przekazany do opracowania!",
			'in_workflow_notok' => "Nie można było przekazać obiektu do opracowania!",
			'pass_workflow_ok' => "Obiekt został pomyślnie przekazany dalej!",
			'pass_workflow_notok' => "Nie można było przekazać dalej obiektu!",
			'decline_workflow_ok' => "Obiekt został zwrócony autorowi!",
			'decline_workflow_notok' => "Nie można było zwrócić obiektu autorowi!",
	),
);
