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
 * Language file: users.inc.php
 * Provides language strings.
 * Language: Deutsch
 */
$l_modules_users = array(
		'user_same' => "Der eigene Benutzer kann nicht gelöscht werden!",
		'grant_owners_ok' => "Die Besitzer wurden erfolgreich übertragen!",
		'grant_owners_notok' => "Es gab einen Fehler beim Übertragen der Besitzer!",
		'grant_owners' => "Besitzer übertragen",
		'grant_owners_expl' => "Übertragen Sie die oben eingestellten Besitzer und Benutzer auf alle Dateien und Verzeichnisse, welche sich in diesem Verzeichnis befinden.",
		'make_def_ws' => "Standard",
		'user_saved_ok' => "Der Benutzer '%s' wurde erfolgreich gespeichert",
		'group_saved_ok' => "Die Gruppe '%s' wurde erfolgreich gespeichert",
		'alias_saved_ok' => "Das Alias '%s' wurde erfolgreich gespeichert",
		'user_saved_nok' => "Der Benutzer '%s' kann nicht gespeichert werden!",
		'nothing_to_save' => "Es gibt nichts zu speichern!",
		'username_exists' => "Der Benutzername '%s' existiert schon!",
		'username_empty' => "Der Benutzername ist nicht ausgefüllt!",
		'user_deleted' => "Der Benutzer '%s' wurde erfolgreich gelöscht!",
		'nothing_to_delete' => "Es gibt nichts zu löschen!",
		'delete_last_user' => "Zur Verwaltung wird mindestens ein Administrator benötigt. Sie können den letzten Administrator nicht löschen.",
		'modify_last_admin' => "Zur Verwaltung wird mindestens ein Administrator benötigt. Sie können die Rechte des letzten Administrators nicht ändern.",
		'user_path_nok' => "Der Pfad ist nicht korrekt!",
		'user_data' => "Benutzerdaten",
		'first_name' => "Vorname",
		'second_name' => "Nachname",
		'username' => "Benutzername",
		'password' => "Kennwort",
		'workspace_specify' => "Arbeitsbereich spezifizieren",
		'permissions' => "Rechte",
		'user_permissions' => "Redakteur",
		'admin_permissions' => "Administrator",
		'password_alert' => "Das Kennwort muß mindestens 4 Zeichen lang sein",
		'delete_alert_user' => "Alle Benutzerdaten für den Benutzernamen '%s' werden gelöscht.\\nSind Sie sicher?",
		'delete_alert_alias' => "Alle Aliasdaten für das Alias '%s' werden gelöscht.\\nSind Sie sicher?",
		'delete_alert_group' => "Alle Gruppendaten und Gruppenbenutzer für die Gruppe '%s' werden gelöscht.\\nSind Sie sicher?",
		'created_by' => "Erstellt von",
		'changed_by' => "Geändert von",
		'no_perms' => "Sie haben keine Berechtigung, diese Option zu benutzen!",
		'publish_specify' => "Benutzer darf veröffentlichen",
		'work_permissions' => "Arbeitsrechte",
		'control_permissions' => "Kontrollrechte",
		'log_permissions' => "Logrechte",
		'file_locked' => array(
				FILE_TABLE => "Die Datei '%s' wird gerade von Benutzer '%s' bearbeitet!",
				TEMPLATES_TABLE => "Die Vorlage '%s' wird gerade von Benutzer '%s' bearbeitet!",
		),
		'acces_temp_denied' => "Zugriff zur Zeit nicht möglich",
		'description' => "Beschreibung",
		'group_data' => "Gruppendaten",
		'group_name' => "Gruppenname",
		'group_member' => "Gruppenmitgliedschaft",
		'group' => "Gruppe",
		'address' => "Adresse",
		'houseno' => "Hausnummer",
		'state' => "Bundesland",
		'PLZ' => "Postleitzahl",
		'city' => "Stadt",
		'country' => "Land",
		'tel_pre' => "Telefon Vorwahl",
		'fax_pre' => "Fax Vorwahl",
		'telephone' => "Telefon",
		'fax' => "Fax",
		'mobile' => "Handy",
		'email' => "E-Mail",
		'general_data' => "Allgemeine Daten",
		'workspace_documents' => "Arbeitsbereich Dokumente",
		'workspace_templates' => "Arbeitsbereich Vorlagen",
		'workspace_objects' => "Arbeitsbereich Objekte",
		'save_changed_user' => "Der Benutzer wurde geändert.\\nMöchten Sie Ihre Änderungen speichern?",
		'not_able_to_save' => " Daten wurden nicht gespeichert, da sie ungültig sind!",
		'cannot_save_used' => " Status kann nicht geändert werden, da gerade in Bearbeitung!",
		'geaendert_von' => "Geändert  von",
		'geaendert_am' => "Geändert  am",
		'angelegt_am' => "Angelegt  am",
		'angelegt_von' => "Angelegt  von",
		'status' => "Status",
		'value' => " Wert ",
		'gesperrt' => "gesperrt",
		'freigegeben' => "freigegeben",
		'gelöscht' => "gelöscht",
		'ohne' => "Ohne",
		'user' => "Benutzer",
		'usertyp' => "Benutzer-Typ",
		'search' => "Suche",
		'search_result' => "Ergebnis",
		'search_for' => "Suche nach",
		'inherit' => "Übernehme Rechte von Elterngruppe",
		'inherit_ws' => "Übernehme Dokument-Arbeitsbereiche der Elterngruppe",
		'inherit_wst' => "Übernehme Vorlagen-Arbeitsbereiche der Elterngruppe",
		'inherit_wso' => "Übernehme Objekt-Arbeitsbereiche der Elterngruppe",
		'organization' => "Organisation",
		'give_org_name' => "Der Name der Organisation",
		'can_not_create_org' => "Die Organisation kann nicht erstellt worden",
		'org_name_empty' => "Der Name der Organisation ist leer",
		'salutation' => "Anrede",
		'sucheleer' => "Es wurde kein Suchwort angegeben.",
		'alias_data' => "Alias Daten",
		'rights_and_workspaces' => "Rechte und<br>Arbeitsbereiche",
		'workspace_navigations' => "Arbeitsbereich Navigation",
		'inherit_wsn' => "Übernehme Navigations-Arbeitsbereiche der Elterngruppe",
		'workspace_newsletter' => "Arbeitsbereich Newsletter",
		'inherit_wsnl' => "Übernehme Newsletter-Arbeitsbereiche der Elterngruppe",
		'delete_user_same' => "Sie können nicht Ihr eigenes Konto löschen.",
		'delete_group_user_same' => "Sie können nicht Ihre eigene Gruppe löschen.",
		'login_denied' => "Login gesperrt",
		'workspaceFieldError' => "FEHLER: Falscher Wert für einen Arbeitsbereich!",
		'noGroupError' => "FEHLER: Gruppe existiert nicht!",
		'CreatorID' => "Erstellt durch: ",
		'CreateDate' => "Erstellt am: ",
		'ModifierID' => "Geändert durch: ",
		'ModifyDate' => "Geändert am: ",
		'lastPing' => "Letzter Ping: ",
		'lostID' => "ID: ",
		'lostID2' => " (gelöscht)",
);

if (defined("OBJECT_TABLE")) {
	$l_modules_users["file_locked"][OBJECT_TABLE] = "Die Klasse '%s' wird gerade von Benutzer '%s' bearbeitet!";
	$l_modules_users["file_locked"][OBJECT_FILES_TABLE] = "Das Objekt '%s' wird gerade von Benutzer '%s' bearbeitet!";
}
