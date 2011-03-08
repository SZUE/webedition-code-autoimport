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
$l_modules_workflow = array(
		'new_workflow' => "Neuer Workflow",
		'workflow' => "Workflow",
		'doc_in_wf_warning' => "Das Dokument befindet sich gerade im Workflow!",
		'message' => "Mitteilung",
		'in_workflow' => "Dokument in Workflow",
		'decline_workflow' => "Dokument zur&uuml;ckweisen",
		'pass_workflow' => "Dokument weitergeben",
		'no_wf_defined' => "Für dieses Dokument ist kein Workflow definiert!",
		'document' => "Dokument",
		'del_last_step' => "Die letzte serielle Stufe kann nicht gelöscht werden!",
		'del_last_task' => "Die letzte parallele Stufe kann nicht gelöscht werden!",
		'save_ok' => "Der Workflow wurde erfolgreich gespeichert!",
		'delete_ok' => "Der Workflow wurde erfolgreich gelöscht!",
		'delete_nok' => "Der Workflow konnte nicht gelöscht werden!",
		'name' => "Name",
		'type' => "Typ",
		'type_dir' => "Verzeichnisbasiert",
		'type_doctype' => "Dokumenttyp/Kategorie basiert",
		'type_object' => "Objektbasiert",
		'dirs' => "Verzeichnisse",
		'doctype' => "Dokument-Typ",
		'categories' => "Kategorien",
		'classes' => "Klassen",
		'active' => "Workflow ist aktiv",
		'step' => "Stufe",
		'and_or' => "UND&nbsp;/&nbsp;ODER",
		'worktime' => "Arbeitszeit (Std., 1Min=0<b>.</b>016)",
		'specials' => "Sonderbehandlungen",
		'EmailPath' => "Zeige den Dokumentenpfad im Betreff von Benachrichtigungs-E-Mails",
		'LastStepAutoPublish' => "Nach Ablaufen des letzten Schrittes (Nächste Stufe angeklickt), publiziere das Dokument statt es zurückzuweisen",
		'user' => "Benutzer ",
		'edit' => "Bearbeiten",
		'send_mail' => "E-Mail verschicken",
		'select_user' => "Benutzer auswählen",
		'and' => " UND ",
		'or' => " ODER ",
		'waiting_on_approval' => "Warte auf Freigabe von %s",
		'status' => "Status",
		'step_from' => "Stufe %s von %s",
		'step_time' => "Zeit",
		'step_start' => "Startzeit",
		'step_plan' => "Abgelaufen am",
		'step_worktime' => "Geplante Zeit",
		'current_time' => "Aktuelle Zeit",
		'time_elapsed' => "Verbrauchte Zeit (h:m:s)",
		'time_remained' => "&Uuml;brige Zeit (h:m:s)",
		'todo_subject' => "Workflow Aufgabe",
		'todo_next' => "Nächste Workflow-Stufe.",
		'go_next' => "Nächste Stufe",
		'new_step' => "Erzeuge zusätzliche serielle Stufe",
		'new_task' => "Erzeuge zusätzliche parallele Stufe",
		'delete_step' => "Serielle Stufe löschen",
		'delete_task' => "Parallele Stufe löschen",
		'save_question' => "Alle Dokumente, welche sich im Workflow befinden, werden entfernt!\\nTrotzdem fortfahren?",
		'nothing_to_save' => "Es gibt nichts zu speichern!",
		'save_changed_workflow' => "Der Workflow wurde geändert.\\nMöchten Sie Ihre Änderungen speichern?",
		'delete_question' => "Alle Daten des Workflows werden gelöscht!\\nTrotzdem fortfahren?",
		'nothing_to_delete' => "Es gibt nichts zu löschen!",
		'user_empty' => "Für die %s. Stufe wurden keine Benutzer definiert!",
		'folders_empty' => "Für den Workflow wurde kein Verzeichnis definiert!",
		'objects_empty' => "Für den Workflow wurde keine Klasse definiert!",
		'doctype_empty' => "Für den Workflow wurde kein Dokument-Typ oder Kategorie definiert!",
		'worktime_empty' => "Für die %s. Stufe wurd keine Arbeitszeit definiert!",
		'name_empty' => "Für den Workflow wurde noch kein Name definiert!",
		'cannot_find_active_step' => "Kann aktive Stufe nicht finden!",
		'no_perms' => "Keine Berechtigung!",
		'plan' => "(planmäßig)",
		'todo_returned' => "Ein Dokument wurde zurückgewiesen.",
		'description' => "Beschreibung",
		'time' => "Zeit",
		'log_approve_force' => "Das Dokument wurde weitergegeben",
		'log_approve' => "Das Dokument wurde weitergegeben",
		'log_decline_force' => "Das Dokument wurde wegen Zeitüberschreitung zurückgegeben",
		'log_decline' => "Das Dokument wurde zurückgegeben",
		'log_doc_finished_force' => "Der Workflow wurde wegen Zeitüberschreitung beendet",
		'log_doc_finished' => "Der Workflow wurde beendet",
		'log_insert_doc' => "Das Dokument wurde in den Workflow übergeben",
		'logbook' => "Logbuch",
		'empty_log' => "Logbuch leeren",
		'emty_log_question' => "Möchten Sie wirklich das ganze Logbuch leeren?",
		'empty_log_ok' => "Das Logbuch wurde geleert!",
		'log_is_empty' => "Das Logbuch ist leer!",
		'log_question_all' => "Alle Einträge löschen",
		'log_question_time' => "Alle Einträge löschen, welche älter sind als:",
		'log_question_text' => "Bitte wählen Sie:",
		'log_remove_doc' => "Das Dokument wurde vom Workflow entfernt",
		'action' => "Aktion",
		'auto_approved' => "Dokument wurde automatisch weitergegeben",
		'auto_declined' => "Dokument wurde automatisch zurückgewiesen",
		'auto_published' => "Document wurde automatisch veröffentlicht",
		'doc_deleted' => "Dokument wurde gelöscht!",
		'ask_before_recover' => "Es befinden sich noch Dokumente/Objekte im Workflow! Möchten Sie diese Dokumente/Objekte aus dem Workflow entfernen?",
		'double_name' => "Der Workflow Name existiert bereits!",
		'more_information' => "Weitere Informationen",
		'less_information' => "Weniger Informationen",
		'no_wf_defined_object' => "Für dieses Objekt ist kein Workflow definiert!",
		FILE_TABLE => array(
				'messagePath' => "Dokument",
				'in_workflow_ok' => "Das Dokument wurde erfolgreich in den Workflow übergeben!",
				'in_workflow_notok' => "Das Dokument konnte nicht in den Workflow übergeben werden!",
				'pass_workflow_ok' => "Das Dokument wurde erfolgreich weitergegeben!",
				'pass_workflow_notok' => "Das Dokument konnte nicht weitergegeben werden!",
				'decline_workflow_ok' => "Das Dokument wurde an den Autor zurückgegeben!",
				'decline_workflow_notok' => "Das Dokument konnte nicht an den Autor zurückgegeben werden!",
		),
);

if (defined("OBJECT_FILES_TABLE")) {
	$l_modules_workflow[OBJECT_FILES_TABLE] = array(
			'messagePath' => "Objekt",
			'in_workflow_ok' => "Das Objekt wurde erfolgreich in den Workflow übergeben!",
			'in_workflow_notok' => "Das Objekt konnte nicht in den Workflow übergeben werden!",
			'pass_workflow_ok' => "Das Objekt wurde erfolgreich weitergegeben!",
			'pass_workflow_notok' => "Das Objekt konnte nicht weitergegeben werden!",
			'decline_workflow_ok' => "Das Objekt wurde an den Autor zurückgegeben!",
			'decline_workflow_notok' => "Das Objekt konnte nicht an den Autor zurückgegeben werden!",
	);
}
