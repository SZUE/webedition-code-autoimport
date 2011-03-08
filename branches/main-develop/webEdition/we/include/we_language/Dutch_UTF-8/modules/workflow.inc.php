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
		'new_workflow' => "Nieuwe workflow",
		'workflow' => "Workflow", // TRANSLATE

		'doc_in_wf_warning' => "Het document bevindt zich in de workflow!",
		'message' => "Bericht",
		'in_workflow' => "Document in workflow", // TRANSLATE
		'decline_workflow' => "Document afwijzen",
		'pass_workflow' => "Document doorsturen",
		'no_wf_defined' => "Geen workflow gedefinieerd voor dit document!",
		'document' => "Document", // TRANSLATE

		'del_last_step' => "Kan laatste seriële stap niet verwijderen!",
		'del_last_task' => "Kan laatste parallele stap niet verwijderen!",
		'save_ok' => "Workflow is bewaard.",
		'delete_ok' => "Workflow is verwijderd.",
		'delete_nok' => "Verwijderen mislukt!",
		'name' => "Naam",
		'type' => "Type", // TRANSLATE
		'type_dir' => "Directory-gebaseerd",
		'type_doctype' => "Document type/Categorie-gebaseerd",
		'type_object' => "Object-gebaseerd",
		'dirs' => "Directories", // TRANSLATE
		'doctype' => "Document type", // TRANSLATE
		'categories' => "Categorieën",
		'classes' => "Classen",
		'active' => "Workflow is actief.",
		'step' => "Stap",
		'and_or' => "EN&nbsp;/&nbsp;OF",
		'worktime' => "Worktime (H, 1min=0<b>.</b>016)", // TRANSLATE
		'specials' => "Specials", // TRANSLATE
		'EmailPath' => "Show the document path in the subject of notifications emails", // TRANSLATE
		'LastStepAutoPublish' => "After the last step (next step clicked), publish the document instead of decline ist", // TRANSLATE
		'user' => "Gebruiker",
		'edit' => "Wijzig",
		'send_mail' => "Verstuur mail",
		'select_user' => "Selecteer gebruiker",
		'and' => " en ",
		'or' => " of ",
		'waiting_on_approval' => "Wacht op goedkeuring van %s.",
		'status' => "Status", // TRANSLATE
		'step_from' => "Stap %s van %s",
		'step_time' => "Tijd per stap",
		'step_start' => "Start datum vam stap",
		'step_plan' => "Eind datum",
		'step_worktime' => "Geplande werktijd",
		'current_time' => "Huidige tijd",
		'time_elapsed' => "Tijd verstreken",
		'time_remained' => "Tijd resterend",
		'todo_subject' => "Workflow taak",
		'todo_next' => "Er is een document voor u in de workflow.",
		'go_next' => "Volgende stap",
		'new_step' => "Maak aditionele seriële stap aan.",
		'new_task' => "Maak aditionele parallele stap aan.",
		'delete_step' => "Verwijder seriële stap.",
		'delete_task' => "Verwijder parallele stap.",
		'save_question' => "Alle documenten die zich bevinden in de workflow worden verwijderd uit de workflow.\\nWeet u zeker dat u door wilt gaan?",
		'nothing_to_save' => "Er is niks om te bewaren!",
		'save_changed_workflow' => "Workflow has been changed.\\nDo you want to save your changes?", // TRANSLATE

		'delete_question' => "Alle workflow data wordt verwijderd!\\nWeet u zeker dat u door wilt gaan?",
		'nothing_to_delete' => "Er is niks om te verwijderen!",
		'user_empty' => "Geen gedefinieerde gebruikers voor stap %s.",
		'folders_empty' => "De map is niet gedefinieerd voor de workflow!",
		'objects_empty' => "Het Object is niet gedefinieerd voor workflow!",
		'doctype_empty' => "Document type of categorie zijn niet gedefinieerd voor de workflow",
		'worktime_empty' => "De werktijd is niet gedefinieerd voor stap %s!",
		'name_empty' => "Deanam is niet gedefinieerd voor de workflow!",
		'cannot_find_active_step' => "Kan actieve stap niet vinden!",
		'no_perms' => "Geen toestemming!",
		'plan' => "(plan)", // TRANSLATE

		'todo_returned' => "Het document is terug gekeerd uit het werkschema.",
		'description' => "Omschrijving",
		'time' => "Tijd",
		'log_approve_force' => "Gebruiker heeft document geforceerd goed gekeurd.",
		'log_approve' => "Gebruiker heeft document goed gekeurd.",
		'log_decline_force' => "Gebruiker heeft document workflow geforceerd geannuleerd.",
		'log_decline' => "Gebruiker heeft document workflow geannuleerd.",
		'log_doc_finished_force' => "Workflow is geforceerd gestopt.",
		'log_doc_finished' => "Workflow is voltooid.",
		'log_insert_doc' => "Document is ingevoegd in de wokflow.",
		'logbook' => "Logboek",
		'empty_log' => "Leeg logboek",
		'emty_log_question' => "Weet u zeker dat u het logboek leeg wilt maken?",
		'empty_log_ok' => "Het logboek is nu leeg.",
		'log_is_empty' => "Het logboek is leeg.",
		'log_question_all' => "Leeg alles",
		'log_question_time' => "Leeg ouder dan",
		'log_question_text' => "Kies optie:",
		'log_remove_doc' => "Document is verwijderd uit de workflow.",
		'action' => "Actie",
		'auto_approved' => "Document is automatisch goedgekeurd.",
		'auto_declined' => "Document is automatisch afgewezen.",
		'auto_published' => "Document has been automatically published.", // TRANSLATE

		'doc_deleted' => "Document is verwijderd!",
		'ask_before_recover' => "Er bevinden zich nog steeds documenten/objecten in het werkschema proces! Wilt u ze uit het workflow proces verwijderen?",
		'double_name' => "Workflow naam bestaat al!",
		'more_information' => "Meer informatie",
		'less_information' => "Minder informatie",
		'no_wf_defined_object' => "Er is geen workflow gedefinieerd voor dit object!",
		FILE_TABLE => array(
				'in_workflow_ok' => "Het document is succesvol in de workflow geplaatst!",
				'in_workflow_notok' => "Het document kan niet in de workflow geplaatst worden!",
				'pass_workflow_ok' => "Het document is succesvol door gestuurd!",
				'pass_workflow_notok' => "Het document kan niet doorgestuurd worden!",
				'decline_workflow_ok' => "Het document is door gestuurd naar de auteur!",
				'decline_workflow_notok' => "Het document kan niet door gestuurd worden naar de auteur!",
				'messagePath' => "Document", // TRANSLATE
				));
if (defined("OBJECT_FILES_TABLE")) {
	$l_modules_workflow[OBJECT_FILES_TABLE] = array(
			'messagePath' => "Object", // TRANSLATE
			'in_workflow_ok' => "Het object is succesvol in de workflow geplaatst!",
			'in_workflow_notok' => "Het object kan niet in de workflow geplaatst worden!",
			'pass_workflow_ok' => "Het object is succesvol door gegeven!",
			'pass_workflow_notok' => "Het object kan niet door gegeven worden!",
			'decline_workflow_ok' => "Het object is terug gestuurd naar de auteur!",
			'decline_workflow_notok' => "Het object kan niet terug gestuur worden naar de auteur!",
	);
}
