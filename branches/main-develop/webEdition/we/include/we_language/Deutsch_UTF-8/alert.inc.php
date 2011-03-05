<?php

/**
 * webEdition CMS
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
 * Language: Deutsch
 */
// Workarround for bug 1292

$l_alert = array(
		'notice' => "Hinweis",
		'warning' => "Warnung",
		'error' => "Fehler",
		'noRightsToDelete' => "Fehler beim Löschen von \\'%s\\'! Sie haben nicht die erforderlichen Rechte!",
		'noRightsToMove' => "Fehler beim Verschieben von \\'%s\\'! Sie haben nicht die erforderlichen Rechte!",
		'in_wf_warning' => "Bevor das Dokument in den Workflow gegeben werden kann, muß es gespeichert werden!\\nSoll das Dokument jetzt gespeichert werden?",
		'in_wf_warning' => "Bevor das Template in den Workflow gegeben werden kann, muß es gespeichert werden!\\nSoll das Template jetzt gespeichert werden?",
		'not_im_ws' => "Die Datei befindet sich nicht in Ihrem Arbeitsbereich!",
		'not_im_ws' => "Dieses Verzeichnis befindet sich nicht in Ihrem Arbeitsbereich!",
		'not_im_ws' => "Die Vorlage befindet sich nicht in Ihrem Arbeitsbereich!",
		'required_field_alert' => "Das Feld '%s' ist ein Pflichtfeld und muß ausgefüllt sein!",
		'phpError' => "webEdition kann nicht gestartet werden",
		'3timesLoginError' => "Der LogIn ist %sx fehlgeschlagen! Bitte warten Sie %s Minuten und versuchen Sie es noch einmal!",
		'popupLoginError' => "Das webEdition Fenster konnte nicht geöffnet werden!\\n\\nwebEdition kann nur gestartet werden, wenn ihr Browser keine Pop-Ups unterdrückt.",
		'publish_when_not_saved_message' => "Das Dokument ist noch nicht gespeichert! Möchten Sie trotzdem veröffentlichen?",
		'template_in_use' => "Die Vorlage wird benutzt und kann daher nicht entfernt werden!",
		'no_cookies' => "Sie haben Cookies nicht aktiviert. Bitte aktivieren Sie in Ihrem Browser Cookies, damit webEdition funktioniert!",
		'doctype_hochkomma' => "Der Name eines Dokument-Typs darf kein ' (Hochkomma) kein , (Komma) und kein \" (Anführungszeichen) enthalten!",
		'thumbnail_hochkomma' => "Der Name einer Miniaturansicht darf kein ' (Hochkomma) und kein , (Komma) enthalten!",
		'can_not_open_file' => "Die Datei %s konnte nicht geöffnet werden!",
		'no_perms_title' => "Keine Berechtigung",
		'no_perms_action' => "Sie haben keine Berechtigung für diese Aktion.",
		'access_denied' => "Zugriff verweigert!",
		'no_perms' => "Bitte wenden Sie sich an den Eigentümer (%s)<br>oder einen Administrator, wenn Sie Zugriff benötigen!",
		'temporaere_no_access' => "Zugriff zur Zeit nicht möglich",
		'temporaere_no_access_text' => "Die Datei (%s) wird gerade vom Benutzer '%s' bearbeitet.",
		'file_locked_footer' => "Das Dokument wird gerade vom Benutzer \"%s\" bearbeitet.",
		'file_no_save_footer' => "Sie haben nicht die erforderlichen Rechte, dieses Dokument zu speichern.",
		'login_failed' => "Benutzername und/oder Kennwort falsch!",
		'login_failed_security' => "webEdition konnte nicht gestartet werden!\\n\\nAus Sicherheitsgründen wurde der Loginvorgang abgebrochen, da die maximale Anmeldezeit überschritten wurde!\\n\\nBitte melden Sie sich neu an.",
		'perms_no_permissions' => "Sie haben keine Berechtigung für diese Aktion!\\nBitte melden Sie sich neu an!",
		'no_image' => "Bei der ausgewählten Datei handelt es sich nicht um eine Grafik!",
		'delete_ok' => "Dateien bzw. Verzeichnisse erfolgreich gelöscht!",
		'delete_cache_ok' => "Cache erfolgreich gelöscht!",
		'nothing_to_delete' => "Es wurde nichts zum Löschen ausgewählt!",
		'objectFile' => "Sie dürfen keine neuen Objekte erstellen, da Ihnen entweder die Rechte fehlen<br>oder es keine Klasse gibt, in welcher einer Ihrer Arbeitsbereiche gültig ist!",
		'delete' => "Ausgewählte Einträge löschen?\\nSind Sie sicher?",
		'delete_cache' => "Cache zu den ausgewählten Einträgen löschen?\\nSind Sie sicher?",
		'delete_folder' => "Ausgewähltes Verzeichnis löschen?\\nBedenken Sie, dass bei einem Verzeichnis alle darin enthaltenen Dokumente und Verzeichnisse automatisch mit gelöscht werden!\\nSind Sie sicher?",
		'delete_nok_error' => "Die Datei '%s' kann nicht gelöscht werden.",
		'delete_nok_file' => "Die Datei '%s' kann nicht gelöscht werden.\\nMöglicherweise ist die Datei schreibgeschützt.",
		'delete_nok_folder' => "Das Verzeichnis '%s' kann nicht gelöscht werden.\\nMöglicherweise ist das Verzeichnis schreibgeschützt.",
		'delete_nok_noexist' => "Diese Datei '%s' existiert nicht!",
		'noResourceTitle' => "Kein Dokument bzw. Verzeichnis!",
		'noResource' => "Das Dokument bzw. Verzeichnis existiert nicht!",
		'move_exit_open_docs_question' => "Vor dem Verschieben müssen alle %s geschlossen werden.\\nWenn Sie fortfahren, werden die folgenden %s geschlossen, ungespeicherte Änderungen gehen dabei verloren.\\n\\n",
		'move_exit_open_docs_continue' => "Fortfahren?",
		'move' => "Ausgewählte Einträge verschieben?\\nSind Sie sicher?",
		'move_ok' => "Dateien wurden erfolgreich verschoben!",
		'move_duplicate' => "Gleichnamige Dateien im Zielverzeichnis vorhanden.\\nDie Dateien konnten nicht verschoben werden.",
		'move_nofolder' => "Die ausgewählten Dateien konnten nicht verschoben werden.\\nEs können keine Verzeichnisse verschoben werden.",
		'move_onlysametype' => "Die ausgewählten Dateien konnten nicht verschoben werden.\\nObjekte können nur innerhalb des eigenen Klassenverzeichnisses verschoben werden.",
		'move_no_dir' => "Es wurde kein Zielverzeichnis ausgewählt.",
		'nothing_to_move' => "Es wurde nichts zum Verschieben ausgewählt.",
		'document_move_warning' => "Nach dem verschieben von Dokumenten ist ein Rebuild erforderlich.<br />Möchten Sie diesen jetzt durchführen?",
		'move_of_files_failed' => "Eine oder mehrere der zu verschiebenden Dateien konnten nicht verschoben werden! Verschieben Sie diese Dateien manuell.\\n Folgende Dateien sind davon betroffen:\\n%s",
		'template_save_warning' => "Diese Vorlage wird von %s veröffentlichten Dokumenten benutzt. Sollen diese Dokumente neu gespeichert werden?<br>ACHTUNG bei vielen Dokumenten kann das sehr lange dauern!",
		'template_save_warning1' => "Diese Vorlage wird von einem veröffentlichten Dokument benutzt. Soll dieses Dokument neu gespeichert werden?",
		'template_save_warning2' => "Diese Vorlage wird von anderen Vorlagen und Dokumenten benutzt. Sollen diese neu gespeichert werden?",
		'thumbnail_exists' => "Diese Miniaturansicht ist bereits vorhanden!",
		'thumbnail_not_exists' => "Diese Miniaturansicht ist nicht vorhanden!",
		'thumbnail_empty' => "Sie haben noch keinen Namen für die Miniaturansicht eingegeben!",
		'doctype_exists' => "Dieser Dokument-Typ ist bereits vorhanden!",
		'doctype_empty' => "Sie haben noch keinen Namen eingegeben!",
		'delete_cat' => "Möchten Sie die ausgewählte Kategorie wirklich löschen?",
		'delete_cat_used' => "Diese Kategorie wird schon benutzt und kann daher nicht gelöscht werden!",
		'cat_exists' => "Die Kategorie ist bereits vorhanden!",
		'cat_changed' => "Diese Kategorie wird schon benutzt! Wenn die Kategorie in Dokumenten angezeigt wird, dann müssen Sie diese Dokumente neu speichern!\\nSoll die Kategorie trotzdem geändert werden?",
		'max_name_cat' => "Der Name der Kategorie darf maximal 32 Zeichen lang sein!",
		'not_entered_cat' => "Sie haben keinen Namen der Kategorie eingegeben!",
		'cat_new_name' => "Bitte geben Sie den neuen Namen der Kategorie ein!",
		'delete_recipient' => "Möchten Sie die ausgewählte E-Mail-Adresse wirklich löschen?",
		'recipient_exists' => "Die E-Mail-Adresse ist bereits vorhanden!",
		'input_name' => "Bitte geben Sie eine neue E-Mail-Adresse ein!",
		'input_file_name' => "Bitte geben Sie einen Dateinamen an.",
		'max_name_recipient' => "Die E-Mail-Adresse darf maximal 255 Zeichen lang sein!",
		'not_entered_recipient' => "Sie haben keine E-Mail-Adresse eingegeben!",
		'recipient_new_name' => "E-Mail-Adresse ändern!",
		'we_backup_import_upload_err' => "Es gab einen Fehler beim Hochladen der Backup-Datei! Die maximal erlaubte Dateigrösse für Uploads beträgt %s. Wenn Ihre Backup-Datei grösser ist, dann kopieren Sie diese per FTP in das Verzeichnis webEdition/we_backup und wählen '" . g_l('backup', "[import_from_server]") . "'!",
		'rebuild_nodocs' => "Es gibt keine Dokumente, welche den ausgewählten Kriterien entsprechen!",
		'we_name_not_allowed' => "Die Namen 'we' und 'webEdition' werden von webEdition selbst benutzt und dürfen deshalb nicht verwendet werden!",
		'we_filename_empty' => "Sie haben noch keinen Dateinamen für dieses Dokument bzw. Verzeichnis eingegeben!",
		'exit_multi_doc_question' => "Einige geöffnete Dokumente enthalten noch ungespeicherte Änderungen. Wenn Sie fortfahren, werden diese Änderungen verworfen. Wollen Sie fortfahren und alle ungespeicherten Änderungen verwerfen?",
		'exit_doc_question_' . FILE_TABLE => "Es scheint, als ob Sie das Dokument verändert haben.<br>Möchten Sie Ihre &Auml;nderungen speichern?",
		'exit_doc_question_' . TEMPLATES_TABLE => "Es scheint, als ob Sie die Vorlage verändert haben.<br>Möchten Sie Ihre &Auml;nderungen speichern?",
		'deleteTempl_notok_used' => "Die Aktion konnte nicht ausgeführt werden, da eine oder mehrere zu löschende Vorlagen schon benutzt werden!",
		'deleteClass_notok_used' => "Die Aktion konnte nicht ausgeführt werden, da eine oder mehrere zu löschende Klassen schon benutzt werden!",
		'delete_notok' => "Es gab einen Fehler beim Löschen!",
		'nothing_to_save' => "Ein Speichern ist im Moment nicht möglich!!",
		'nothing_to_publish' => "Ein Veröffentlichen ist im Moment nicht möglich!",
		'we_filename_notValid' => "Der eingegebene Dateiname ist nicht gültig!\\nErlaubte Zeichen sind Buchstaben von a bis z (Groß- oder Kleinschreibung), Zahlen, Unterstrich (_), Minus (-) und Punkt (.).",
		'empty_image_to_save' => "Gewählte Grafik ist leer.\\nTrotzdem weitermachen?",
		'path_exists' => "Das Dokument bzw. Verzeichnis %s konnte nicht gespeichert werden, da es bereits ein anderes Dokument an dieser Stelle gibt!",
		'folder_not_empty' => "Da eines oder mehrere der zu löschende Verzeichnisse nicht ganz leer waren, konnten Sie nicht vollständig vom Server gelöscht werden! Löschen Sie die Dateien manuell.\\n Folgende Verzeichnisse sind davon betroffen:\\n%s",
		'name_nok' => "Namen dürfen die Zeichen '<', '>' und '/' nicht erhalten!",
		'found_in_workflow' => "Ein oder mehrere zu löschende Einträge befinden sich zur Zeit im Workflow! Möchten Sie diese Einträge aus dem Workflow entfernen?",
		'import_we_dirs' => "Sie versuchen einen Import von einem, mit webEdition verwaltetem Verzeichnis zu machen!\\nDiese Verzeichnisse sind geschützt und deswegen es ist nicht möglich von ihnen zu importieren!",
		'image/*' => "Die Datei konnte nicht angelegt werden. Entweder handelt es sich nicht um eine Grafik oder es steht nicht ausreichend Speicherplatz (Webspace) zur Verfügung!",
		'application/x-shockwave-flash' => "Die Datei konnte nicht angelegt werden. Entweder handelt es sich um keinen Flash-Datei oder ihr Speicherplatz (Festplatte) ist erschöpft!",
		'video/quicktime' => "Die Datei konnte nicht angelegt werden. Entweder handelt es sich um keinen Quicktime-Datei oder ihr Speicherplatz (Festplatte) ist erschöpft!",
		'text/css' => "Die Datei konnte nicht angelegt werden. Entweder handelt es sich um keine CSS-Datei oder ihr Speicherplatz (Festplatte) ist erschöpft!",
		'no_file_selected' => "Sie haben keine Datei zum Hochladen ausgewählt!",
		'browser_crashed' => "Das Fenster konnte nicht geöffnet werden, da Ihr Browser einen Fehler verursacht hat!  Bitte sichern Sie Ihre Arbeit und starten Ihren Browser neu.",
		'copy_folders_no_id' => "Das aktuelle Verzeichnis muß zuerst gespeichert werden!",
		'copy_folder_not_valid' => "Das gleiche Verzeichnis oder eins der Elternverzeichnisse kann nicht kopiert werden!",
		'headline' => 'Achtung',
		'description' => 'Für dieses Dokument ist keine Ansicht verfügbar.',
		'last_document' => 'Sie befinden sich auf dem letzten Dokument.',
		'first_document' => 'Sie befinden sich auf dem ersten Dokument.',
		'doc_not_found' => 'Could not find matching document.',
		'no_entry' => 'Kein Eintrag in der History vorhanden.',
		'no_open_document' => 'Es ist kein Dokument geöffnet.',
		'confirm_delete' => 'Soll dieses Dokument wirklich gelöscht werden?',
		'no_delete' => 'Die Datei konnte nicht gelöscht werden.',
		'return_to_start' => 'Die Datei wurde erfolgreich gelöscht.\\nZurück zum seeMode Startdokument.',
		'return_to_start' => 'Die Datei wurde erfolgreich verschoben.\\nZurück zum seeMode Startdokument.',
		'no_delete' => 'Die Datei konnte nicht verschoben werden.',
		'cockpit_not_activated' => 'Die Aktion konnte nicht ausgeführt werden, da das Cockpit nicht aktiviert ist.',
		'cockpit_reset_settings' => 'Sollen die aktuellen Cockpit-Einstellungen gelöscht und die Standard-Einstellungen wiederhergestellt werden?',
		'save_error_fields_value_not_valid' => 'Die markierten Felder enthalten keine gültigen Werte.\\nBitte tragen Sie gültige Werte ein.',
		'eplugin_exit_doc' => "Die Verbindung zwischen webEdition und dem externen Editor wird beim Schließen des Dokuments getrennt und die Inhalte werden nicht mehr synchronisiert.\\nMöchten Sie das Dokument schließen?",
		'delete_workspace_user' => "Das Verzeichnis %s kann nicht gelöscht werden! Es ist als Arbeitsbereich für folgende Benutzer bzw. Gruppen definiert:\\n%s",
		'delete_workspace_user_r' => "Das Verzeichnis %s kann nicht gelöscht werden! Innerhalb des Verzeichnisses befinden sich weitere Verzeichnisse, welche als Arbeitsbereich für folgende Benutzer bzw. Gruppen definiert sind:\\n%s",
		'delete_workspace_object' => "Das Verzeichnis %s kann nicht gelöscht werden. Es ist als Arbeitsbereich in folgenden Objekten definiert:\\n%s",
		'delete_workspace_object_r' => "Das Verzeichnis %s kann nicht gelöscht werden. Innerhalb des Verzeichnisses befinden sich weitere Verzeichnisse, welche als Arbeitsbereich in folgenden Objekten definiert sind:\\n%s",
		'field_contains_incorrect_chars' => "Ein Feld (vom Typ %s) enthält ungültige Zeichen.",
		'field_input_contains_incorrect_length' => "Die maximale Länge eines Feldes vom Typ \'Textinput\' beträgt 255 Zeichen. Wenn Sie mehr Zeichen benötigen, dann verwenden Sie ein Feld vom Typ \'Textarea\'.",
		'field_int_contains_incorrect_length' => "Die maximale Länge eines Feldes vom Typ \'Integer\' beträgt 10 Zeichen.",
		'field_int_value_to_height' => "Der maximale Wert eines Feldes vom Typ \'Integer\' ist 2147483647.",
		'we_filename_notValid' => "Der Dateiname der hochzuladenden Datei ist nicht gültig!\\nErlaubte Zeichen sind Buchstaben von a bis z (Groß- oder Kleinschreibung), Zahlen, Unterstrich (_), Minus (-) und Punkt (.).",
		'login_denied_for_user' => "Der Benutzer kann sich nicht anmelden. Sein Zugang wurde gesperrt",
		'no_perm_to_delete_single_document' => "Sie verfügen nicht über die benötigten Rechte, um das aktive Dokument zu löschen",
		'applyWeDocumentCustomerFiltersDocument' => "Das Dokument wurde in einen Ordner mit abweichenden Zugriffsrechten für Kunden verschoben. Sollen die Einstellungen des Ordners auf dieses Dokument übertragen werden?",
		'applyWeDocumentCustomerFiltersFolder' => "Das Verzeichnis wurde in einen Ordner mit abweichenden Zugriffsrechten für Kunden verschoben. Sollen die Einstellungen für dieses Verzeichnis und alle Unterelemente übertragen werden? ",
		'error_fields_value_not_valid' => 'Eingabe-Felder enthalten ungültige Werte!',
		'field_in_tab_notvalid_pre' => "Die Einstellungen konnten nicht gespeichert werden, da folgende Felder ungültige Werte enthalten:",
		'field_in_tab_notvalid' => ' - Feld %s im Tab %s',
		'field_in_tab_notvalid_post' => 'Bitte korrigieren Sie die Felder und speichern Sie die Einstellungen erneut.',
		'discard_changed_data' => 'Es gibt nicht gespeicherte Änderungen, die verloren gehen. Sind sie sicher?',
);


if (defined("OBJECT_FILES_TABLE")) {
	$l_alert = array_merge($l_alert, array(
			'in_wf_warning' => "Bevor das Objekt in den Workflow gegeben werden kann, muß es gespeichert werden!\\nSoll das Dokument jetzt gespeichert werden?",
			'in_wf_warning' => "Bevor die Klasse in den Workflow gegeben werden kann, muß sie gespeichert werden!\\nSoll die Klasse jetzt gespeichert werden?",
			'exit_doc_question_' . OBJECT_TABLE => "Es scheint, als ob Sie die Klasse verändert haben.<br>Möchten Sie Ihre &Auml;nderungen speichern?",
			'exit_doc_question_' . OBJECT_FILES_TABLE => "Es scheint, als ob Sie das Objekt verändert haben.<br>Möchten Sie Ihre &Auml;nderungen speichern?",
					));
}