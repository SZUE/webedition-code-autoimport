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
		'message' => "Message", // TRANSLATE
		'in_workflow' => "Document dans le Gestion de Flux",
		'decline_workflow' => "Repousser le document",
		'pass_workflow' => "Transmettre le document",
		'no_wf_defined' => "Aucun Gestion de Flux a été défini pour ce document!",
		'document' => "Document", // TRANSLATE

		'del_last_step' => "La dernière étape sérielle ne peut pas être supprimée!",
		'del_last_task' => "La dernière étape parallèle ne peut pas être supprimée!",
		'save_ok' => "Le Gestion de Flux a été enregistré avec succès!",
		'delete_ok' => "Le Gestion de Flux a été supprimé avec succès!",
		'delete_nok' => "Le Gestion de Flux n'a pas pu être supprimé!",
		'name' => "Nom",
		'type' => "Type", // TRANSLATE
		'type_dir' => "À la base de Répertoires",
		'type_doctype' => "À la base de Types-de-Document/Categories",
		'type_object' => "À la base d'Objects",
		'dirs' => "Répertoires",
		'doctype' => "Type-de-Document",
		'categories' => "Categories", // TRANSLATE
		'classes' => "Classes", // TRANSLATE

		'active' => "Gestion de Flux est active",
		'step' => "Étape",
		'and_or' => "ET&nbsp;/&nbsp;OU",
		'worktime' => "Worktime (H, 1min=0<b>.</b>016)", // TRANSLATE
		'specials' => "Specials", // TRANSLATE
		'EmailPath' => "Show the document path in the subject of notifications emails", // TRANSLATE
		'LastStepAutoPublish' => "After the last step (next step clicked), publish the document instead of decline ist", // TRANSLATE
		'user' => "Utilisateur ",
		'edit' => "Éditer",
		'send_mail' => "Envoyer un E-Mail",
		'select_user' => "Select user", // TRANSLATE

		'and' => " and ", // TRANSLATE
		'or' => " or ", // TRANSLATE

		'waiting_on_approval' => "En attente de l'autorisation de %s",
		'status' => "État",
		'step_from' => "Étape %s de %s",
		'step_time' => "Step time", // TRANSLATE
		'step_start' => "Temps de démarrage",
		'step_plan' => "Périmé le",
		'step_worktime' => "Temps prévu",
		'current_time' => "Temps actuel",
		'time_elapsed' => "Temps écoulé (h:m:s)",
		'time_remained' => "Temps restant (h:m:s)",
		'todo_subject' => "Tache de Gestion de Flux",
		'todo_next' => "La prochaine d'étape de Gestion.",
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

		'todo_returned' => "Un document a été rejeté.",
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
		'emty_log_question' => "°Êtes-vous sûr de vider le jornal complèment?",
		'empty_log_ok' => "The logbook is now emtpy.", // TRANSLATE
		'log_is_empty' => "The logbook is emtpy.", // TRANSLATE

		'log_question_all' => "Clear all", // TRANSLATE
		'log_question_time' => "Clear older than", // TRANSLATE
		'log_question_text' => "Choose option:", // TRANSLATE

		'log_remove_doc' => "Document is removed from workflow.", // TRANSLATE
		'action' => "Action", // TRANSLATE

		'auto_approved' => "Document has been automatically approved.", // TRANSLATE
		'auto_declined' => "Document has been automatically declined.", // TRANSLATE
		'auto_published' => "Document has been automatically published.", // TRANSLATE

		'doc_deleted' => "Document has been deleted!", // TRANSLATE
		'ask_before_recover' => "There are still documents/objects in the workflow process! Do you want to remove them from the workflow process?", // TRANSLATE

		'double_name' => "Workflow name already exists!", // TRANSLATE

		'more_information' => "Informations supplémentaire",
		'less_information' => "Moins Informations",
		'no_wf_defined_object' => "Aucun Gestion de Flux a été défini pour cet object!",
		'tblFile' => array(
				'messagePath' => "Document", // TRANSLATE
				'in_workflow_ok' => "Le document a été placé dans le Gestion de Flux avec succès!",
				'in_workflow_notok' => "Le document n'a pas pu être placé dans le Gestion de Flux!",
				'pass_workflow_ok' => "Le document a été transmis avec succès!",
				'pass_workflow_notok' => "Le document n'a pas pu être transmis!",
				'decline_workflow_ok' => "Le document a été repoussé à l'auteur!",
				'decline_workflow_notok' => "Le docuemtn n'a pas pu être repoussé à l'auteur!",
				),
'tblObjectFiles' => array(
			'messagePath' => "Object", // TRANSLATE
			'in_workflow_ok' => "L'object a été placé dans le Gestion de Flux avec succès!",
			'in_workflow_notok' => "L'object n'a pas pu être placé dans le Gestion de Flux!",
			'pass_workflow_ok' => "L'object a été transmis avec succès!",
			'pass_workflow_notok' => "L'object n'a pas pu être transmis!",
			'decline_workflow_ok' => "L'object a été repoussé à l'auteur!",
			'decline_workflow_notok' => "L'object n'a pas pu être repoussé à l'auteur!",
	),
);
