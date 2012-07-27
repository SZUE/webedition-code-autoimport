<?php
/** Generated language file of webEdition CMS */
$l_backup=array(
	''=>'Achtung! Nach dem Wiederherstellen eines Backups <strong>aus älteren webEdition-Installationen</strong> (vor 6.3.0) sollte dringend eine <strong>Update-Wiederholung</strong> durchgeführt werden!',
	'backup_deleted'=>'Die Backup-Datei %s wurde gelöscht',
	'backup_form'=>'Backup vom',
	'backup_log_exp'=>'Das Logbuch wird in /webEdition/we_backup/data/lastlog.php erstellt',
	'banner_info'=>'Banner und Statistiken des Banner Moduls.',
	'binary_info'=>'Binärdateien von Bildern, PDFs und anderen Dokumenten.',
	'bzip'=>'bzip',
	'cannot_save_backup'=>'Die Backup-Datei kann nicht gespeichert werden.',
	'cannot_save_tmpfile'=>'Temporäre Datei kann nicht angelegt werden! Prüfen Sie bitte, ob Sie die Rechte haben in %s zu schreiben.',
	'cannot_send_backup'=>'Backup kann nicht ausgeführt werden',
	'cannot_split_file'=>'Kann die Datei `%s` nicht zur Wiederherstellung vorbereiten!',
	'cannot_split_file_ziped'=>'Die Datei wurde mit einer nicht unterstützen Komprimierungsmethode komprimiert.',
	'can_not_open_file'=>'Die Datei `%s` kann nicht geöffnet werden.',
	'charset_warning'=>'Sollte es Probleme beim Wiederherstellen eines Backups geben, achten Sie bitte darauf, dass <strong>im Zielsystem derselbe Zeichensatz (Charset) wie im Quellsystem verwendet</strong> wird. Dies gilt sowohl für den Zeichensatz der Datenbank (collation) als auch für den Zeichensatz der verwendeten Oberflächensprache!',
	'compress'=>'Komprimieren',
	'compress_file'=>'Datei komprimieren',
	'convert_charset'=>'Achtung! Beim Nutzung dieser Option in einer bestehenden Site besteht die Gefahr des totalen Datenverlustes. Bitte beachten Sie die Hinweise unter http://documentation.webedition.org/de/webedition/administration/charset-conversion-of-legacy-sites',
	'convert_charset_data'=>'Beim Einspielen des Backups Umstellung der Installation von ISO auf UTF-8',
	'core_info'=>'Alle Vorlagen und Dokumente.',
	'customer_import_file_found'=>'Hier handelt es sich um eine Import-Datei aus der Kundenverwaltung. Nutzen Sie bitte die Option "Import/Export" aus der Kundenverwaltung (PRO) um die Datei zu importieren.',
	'customer_info'=>'Kunden und Zugangsdaten der Kundenverwaltung.',
	'decompress'=>'Dekomprimieren',
	'defaultcharset_warning'=>'<span style="color:ff0000">Achtung! Es ist keinen Standard-Zeichensatz definiert.</span> Dies kann bei bestimmten Server-Konfigurationen zu Problemen beim Wiederherstellen des Backups führen!',
	'delete_entry'=>'Lösche %s',
	'delete_nok'=>'Die Dateien kann nicht gelöscht werden!',
	'delete_old_files'=>'Lösche alte Dateien...',
	'delold_confirm'=>'Sind Sie sicher, dass Sie alle Dateien vom Server löschen möchten?',
	'delold_notice'=>'Auf dem Server vorhandene Dateien löschen (empfohlen)?<br/>
Es werden alle Dateien, die mit webEdition verwaltet werden gelöscht! Dokumente und Vorlagen bleiben in der Datenbank vorhanden. Die Webseite ist erst nach erfolgreichem Rebuild (Dokumente + Vorlagen) wieder funktional.',
	'del_backup_confirm'=>'Möchten Sie ausgewählte Backup-Datei löschen?',
	'download'=>'klicken Sie bitte hier.',
	'download_failed'=>'Die angeforderte Datei existiert entweder nicht oder Sie haben keine Berechtigung, sie herunterzuladen.',
	'download_file'=>'Datei herunterladen',
	'download_starting'=>'Der Download der Backup-Datei wurde gestartet.<br/><br/>Sollte der Download nach 10 Sekunden nicht starten,<br/>',
	'error'=>'Fehler',
	'error_compressing_backup'=>'Bei der Komprimierung ist ein Fehler aufgetreten, das Backup konnte nicht abgeschlossen werden!',
	'error_delete'=>'Backup-Datei konnte nicht gelöscht werden. Bitte löschen Sie die Datei über Ihr FTP-Programm aus dem Ordner /webEdition/we_backup',
	'error_timeout'=>'Bei der Erstellung des Backup ist ein timeout aufgetreten, das Backup konnte nicht abgeschlossen werden!',
	'export_backup_log'=>'Logbuch erstellen',
	'export_banner_data'=>'Bannerdaten sichern',
	'export_banner_dep'=>'Sie haben `Bannerdaten sichern` ausgewählt. Um richtig zu funktionieren, benötigen die Bannerdaten auch die Dokumente. Deswegen wird `Dokumente und Vorlage sichern` automatisch markiert.',
	'export_binary_data'=>'Binarydaten (Bilder, PDFs, ...) sichern',
	'export_binary_dep'=>'Sie haben `Binarydaten sichern` ausgewählt. Um richtig zu funktionieren, benötigen die Binarydaten auch die Dokumente. Deswegen wird `Dokumente und Vorlage sichern` automatisch markiert.',
	'export_check_all'=>'Alle auswählen',
	'export_configuration_data'=>'Konfiguration sichern',
	'export_content'=>'Inhalt sichern',
	'export_core_data'=>'Dokumente und Vorlagen sichern',
	'export_customer_data'=>'Kundendaten sichern',
	'export_doctypes'=>'Dateien sichern',
	'export_export_data'=>'Exportdaten sichern',
	'export_extern_data'=>'webEdition-externe Dateien/Verzeichnisse sichern',
	'export_files'=>'Dateien sichern',
	'export_glossary_data'=>'Glossardaten sichern',
	'export_indexes'=>'Indizes sichern',
	'export_info'=>'Daten des Export Moduls.',
	'export_links'=>'Links sichern',
	'export_location'=>'Bitte wählen Sie aus, wo die Backup-Datei gespeichert werden soll. Wird die Datei auf dem Server gespeichert, finden Sie diese unter `/webEdition/we_backup/data/`.',
	'export_location_send'=>'Auf Ihrer lokalen Festplatte',
	'export_location_server'=>'Auf dem Server',
	'export_newsletter_data'=>'Newsletterdaten sichern',
	'export_newsletter_dep'=>'Sie haben `Newsletterdaten sichern` ausgewählt. Der Newsletter braucht die Dokumente, Objekte und die Kundendaten um richtig zu funktionieren und deswegen wird `Dokumente und Vorlage sichern`, `Objekte und Klasse sichern` und `Kundendaten sichern` automatisch markiert.',
	'export_object_data'=>'Objekte und Klassen sichern',
	'export_options'=>'Wählen Sie die zu sichernden Daten aus.',
	'export_prefs'=>'Einstellungen sichern',
	'export_schedule_data'=>'Zeitplanungsdaten sichern',
	'export_schedule_dep'=>'Sie haben `Zeitplanungsdaten sichern` ausgewählt. Um richtig zu funktionieren, benötigen die Zeitplanungsdaten auch die Dokumente und die Objekte. Deswegen wird `Dokumente und Vorlage sichern` und `Objekte und Klassen sichern` automatisch markiert.',
	'export_settings_data'=>'Einstellungen sichern',
	'export_shop_data'=>'Shopdaten sichern',
	'export_shop_dep'=>'Sie haben `Shopdaten sichern` ausgewählt. Das Shop Modul braucht die Kundendaten um richtig zu funktionieren und deswegen wird `Kundendaten sichern` automatisch markiert.',
	'export_spellchecker_data'=>'Daten der Rechtschreibprüfung sichern',
	'export_step1'=>'Schritt 1/2 - Backup Parameter',
	'export_step2'=>'Schritt 2/2 - Backup beendet',
	'export_templates'=>'Vorlagen sichern',
	'export_temporary_data'=>'Temporäre Dateien sichern',
	'export_temporary_dep'=>'Sie haben `Temporäre Dateien sichern` ausgewählt. Um richtig zu funktionieren, benötigen die Temporäre Dateien auch die Dokumente. Deswegen wird `Dokumente und Vorlage sichern` automatisch markiert.',
	'export_title'=>'Backup erstellen',
	'export_todo_data'=>'Todo/Messaging Daten sichern',
	'export_todo_dep'=>'Sie haben `Todo/Messaging sichern` ausgewählt. Das Todo/Messaging braucht die Benutzerdaten um richtig zu funktionieren und deswegen wird `Benutzerdaten sichern` automatisch markiert.',
	'export_users_data'=>'Benutzerdaten sichern',
	'export_user_data'=>'Benutzerdaten sichern',
	'export_versions_binarys_data'=>'Versions-Binary-Dateien sichern',
	'export_versions_binarys_dep'=>'Sie haben `Versions-Binary-Dateien sichern` ausgewählt. Um richtig zu funktionieren, benötigen die Versionen auch die zugehörigen Dokumente, Objekte und Versionierungsdaten. Deswegen wird `Dokumente und Vorlage sichern`, `Objekte und Klassen sichern` und `Versionierungsdaten sichern` automatisch markiert.',
	'export_versions_data'=>'Versionierungsdaten sichern',
	'export_versions_dep'=>'Sie haben `Versionierungsdaten sichern` ausgewählt. Um richtig zu funktionieren, benötigen die Versionen auch die zugehörigen Dokumente, Objekte und Binärdateien. Deswegen wird `Dokumente und Vorlage sichern`, `Objekte und Klassen sichern` und `Binärdateien sichern` automatisch markiert.',
	'export_voting_data'=>'Votingdaten sichern',
	'export_workflow_data'=>'Workflowdaten sichern',
	'export_workflow_dep'=>'Sie haben `Workflow sichern` ausgewählt. Das Workflow braucht die Dokumente und Benutzerdaten um richtig zu funktionieren und deswegen wird `Dokumente und Vorlage sichern` und `Benutzerdaten sichern` automatisch markiert.',
	'external_backup'=>'Externe Daten sichern...',
	'extern'=>'webEdition-externe Dateien/Verzeichnisse wiederherstellen',
	'extern_backup_question_exp'=>'Sie haben `webEdition-externe Dateien/Verzeichnisse sichern` ausgewählt. Diese Auswahl kann sehr zeitintensiv sein und zu Systemfehlern führen. Trotzdem fortfahren?',
	'extern_backup_question_exp_all'=>'Sie haben `Alle auswählen` ausgewählt. Damit wird automatisch `webEdition-externe Dateien/Verzeichnisse sichern` mit ausgewählt. Dieser Vorgang kann sehr zeitintensiv sein und zu Systemfehlern führen.\n`webEdition-externe Dateien/Verzeichnisse sichern` ausgewählt lassen?',
	'extern_backup_question_imp'=>'Sie haben `webEdition-externe Dateien/Verzeichnisse wiederherstellen` ausgewählt. Diese Auswahl kann sehr zeitintensiv sein und zu Systemfehlern führen. Trotzdem fortfahren?',
	'extern_backup_question_imp_all'=>'Sie haben `Alle auswählen` ausgewählt. Damit wird automatisch `webEdition-externe Dateien/Verzeichnisse wiederherstellen` mit ausgewählt. Dieser Vorgang kann sehr zeitintensiv sein und zu Systemfehlern führen.\n`webEdition-externe Dateien/Verzeichnisse wiederherstellen` ausgewählt lassen?',
	'extern_exp'=>'Achtung! Diese Option ist sehr zeitintensiv und kann zu Systemfehlern führen',
	'extern_files_question'=>'webEdition-externe Dateien/Verzeichnisse sichern',
	'extern_files_size'=>'Dieser Vorgang kann einige Minuten dauern. Es werden unter Umständen mehrere Dateien angelegt, da die Datenbankeinstellung auf eine maximale Dateigröße von %.1f MB (%s Byte) beschränkt ist.',
	'filename'=>'Dateiname',
	'filename_compression'=>'Geben Sie hier der Ziel-Backup-Datei einen Namen. Sie können auch die Dateikompression aktivieren. Die Backup-Datei wird dann mit gzip komprimiert und wird die Dateiendung .gz erhalten. Dieser Vorgang kann einige Minuten dauern!<br/>Wenn das Backup nicht erfolgreich ist, ändern Sie bitte die Einstellungen.',
	'filename_info'=>'Geben Sie hier der Ziel-Backup-Datei einen Namen.',
	'files_not_deleted'=>'Eine oder mehrere der zu löschende Dateien konnten nicht vollständig vom Server gelöscht werden! Möglicherweise sind sie schreibgeschützt. Löschen Sie die Dateien manuell. Folgende Dateien sind davon betroffen:',
	'file_missing'=>'Die Backup-Datei fehlt!',
	'file_not_readable'=>'Die Backup-Datei ist nicht lesbar. Bitte überprüfen Sie die Berechtigungen.',
	'finished'=>'Beendet',
	'finished_fail'=>'Der Import der Backup-Daten wurde nicht erfolgreich beendet.',
	'finished_success'=>'Der Import der Backup-Daten wurde erfolgreich beendet.',
	'finish'=>'Die Backup-Datei wurde erstellt.',
	'finish_error'=>'Fehler: Backup konnte nicht erfolgreich ausgeführt werden',
	'finish_warning'=>'Warnung: Backup wurde ausgeführt, möglicherweise wurden nicht alle Dateien vollständig angelegt',
	'format_unknown'=>'Das Format der Datei ist unbekannt!',
	'ftp_hint'=>'Achtung! Benutzen Sie den Binary-Modus beim Download per FTP, wenn die Backup-Datei mit zip komprimiert ist! Ein Download im ASCII-Modus zerstört die Datei, so dass sie nicht wieder hergestellt werden kann!',
	'glossary_info'=>'Daten des Glossars.',
	'gzip'=>'gzip',
	'import_banner_data'=>'Bannerdaten wiederherstellen',
	'import_banner_dep'=>'Sie haben `Bannerdaten wiederherstellen` ausgewählt. Um richtig zu funktionieren, benötigen die Bannerdaten auch die Dokumente. Deswegen wird `Dokumente und Vorlage wiederherstellen` automatisch markiert.',
	'import_binary_data'=>'Binarydaten (Bilder, PDFs, ...) wiederherstellen',
	'import_binary_dep'=>'Sie haben `Binarydaten wiederherstellen` ausgewählt. Um richtig zu funktionieren, benötigen die Binarydaten auch die Dokumente. Deswegen wird `Dokumente und Vorlage wiederherstellen` automatisch markiert.',
	'import_check_all'=>'Alle auswählen',
	'import_configuration_data'=>'Konfiguration wiederherstellen',
	'import_content'=>'Inhalt wiederherstellen',
	'import_core_data'=>'Dokumente und Vorlagen wiederherstellen',
	'import_customer_data'=>'Kundendaten wiederherstellen',
	'import_doctypes'=>'Dateien wiederherstellen',
	'import_export_data'=>'Exportdaten wiederherstellen',
	'import_extern_data'=>'webEdition-externe Dateien/Verzeichnisse wiederherstellen',
	'import_files'=>'Dateien wiederherstellen',
	'import_file_found'=>'Hier handelt es sich um eine Import-Datei. Nutzen Sie bitte die Option "Import/Export" aus dem Datei-Menü um die Datei zu importieren.',
	'import_file_found_question'=>'Möchten Sie gleich das aktuelle Fenster schließen und einen Import-Wizard für den webEditon XML-Import starten?',
	'import_from_local'=>'Daten aus lokal gesicherter Datei laden',
	'import_from_server'=>'Daten vom Server laden',
	'import_glossary_data'=>'Glossardaten wiederherstellen',
	'import_indexes'=>'Indizes wiederherstellen',
	'import_links'=>'Links wiederherstellen',
	'import_newsletter_data'=>'Newsletterdaten wiederherstellen',
	'import_newsletter_dep'=>'Sie haben `Newsletterdaten wiederherstellen` ausgewählt. Der Newsletter braucht die Dokumente, Objekte und die Kundendaten um richtig zu funktionieren und deswegen wird `Dokumente und Vorlage wiederherstellen`, `Objekte und Klasse wiederherstellen` und `Kundendaten wiederherstellen` automatisch markiert.',
	'import_object_data'=>'Objekte und Klassen wiederherstellen',
	'import_options'=>'Wählen Sie die wiederherzustellenden Daten aus.',
	'import_prefs'=>'Einstellungen wiederherstellen',
	'import_schedule_data'=>'Zeitplanungsdaten wiederherstellen',
	'import_schedule_dep'=>'Sie haben `Zeitplanungsdaten wiederherstellen` ausgewählt. Um richtig zu funktionieren, benötigen die Zeitplanungsdaten auch die Dokumente und die Objekte. Deswegen wird `Dokumente und Vorlage wiederherstellen` und `Objekte und Klassen wiederherstellen` automatisch markiert.',
	'import_settings_data'=>'Einstellungen wiederherstellen',
	'import_shop_data'=>'Shopdaten wiederherstellen',
	'import_shop_dep'=>'Sie haben `Shopdaten wiederherstellen` ausgewählt. Der Shop braucht die Kundendaten um richtig zu funktionieren und deswegen wird `Kundendaten sichern` automatisch markiert.',
	'import_spellchecker_data'=>'Daten der Rechtschreibprüfung wiederherstellen',
	'import_templates'=>'Vorlagen wiederherstellen',
	'import_temporary_data'=>'Temporäre Dateien wiederherstellen',
	'import_temporary_dep'=>'Sie haben `Temporäre Dateien wiederherstellen` ausgewählt. Um richtig zu funktionieren, benötigen die `Temporäre Dateien auch die Dokumente. Deswegen wird `Dokumente und Vorlage wiederherstellen` automatisch markiert.',
	'import_todo_data'=>'Todo/Messaging Daten wiederherstellen',
	'import_todo_dep'=>'Sie haben `Todo/Messaging wiederherstellen` ausgewählt. Das Todo/Mess. braucht die Benutzerdaten um richtig zu funktionieren und deswegen wird `Benutzerdaten wiederherstellen` automatisch markiert.',
	'import_users_data'=>'Benutzerdaten wiederherstellen',
	'import_user_data'=>'Benutzerdaten wiederherstellen',
	'import_versions_binarys_data'=>'Vorhandene Versions-Binary-Dateien wiederherstellen',
	'import_versions_binarys_dep'=>'Sie haben `Versions-Binary-Dateien wiederherstellen` ausgewählt. Um richtig zu funktionieren, benötigen die Versionen auch zugehörigen Dokumente, Objekte und Versionierungsdaten. Deswegen wird `Dokumente und Vorlage wiederherstellen`, `Objekte und Klassen wiederherstellen` und `Versionierungsdaten wiederherstellen` automatisch markiert.',
	'import_versions_data'=>'Versionierungsdaten wiederherstellen',
	'import_versions_dep'=>'Sie haben `Versionierungsdaten wiederherstellen` ausgewählt. Um richtig zu funktionieren, benötigen die Versionen auch zugehörigen Dokumente, Objekte und Binärdateien. Deswegen wird `Dokumente und Vorlage wiederherstellen`, `Objekte und Klassen wiederherstellen` und `Binärdateien wiederherstellen` automatisch markiert.',
	'import_voting_data'=>'Votingdaten wiederherstellen',
	'import_workflow_data'=>'Workflowdaten wiederherstellen',
	'import_workflow_dep'=>'Sie haben `Workflow wiederherstellen` ausgewählt. Das Workflow braucht die Dokumente und Benutzerdaten um richtig zu funktionieren und deswegen wird `Dokumente und Vorlage wiederherstellen` und `Benutzerdaten wiederherstellen` automatisch markiert.',
	'name_notok'=>'Der Dateiname ist nicht korrekt!',
	'newsletter_info'=>'Daten des Newsletter Moduls.',
	'none'=>'kein',
	'nothing_selected'=>'Es wurde nichts ausgewählt!',
	'nothing_selected_fromlist'=>'Bitte wählen Sie eine Backup-Datei aus der Liste!',
	'nothing_to_delete'=>'Es gibt nichts zu löschen!',
	'no_resource'=>'Kritischer Fehler: Nicht genügend freie Ressourcen, um das Backup abzuschließen!',
	'object_info'=>'Objekte und Klassen des DB/Objekt Moduls.',
	'old_backups_warning'=>'Achtung! Nach dem Wiederherstellen eines Backups <strong>aus älteren webEdition-Installationen</strong> (vor 6.3.0) sollte dringend eine <strong>Update-Wiederholung</strong> durchgeführt werden!',
	'option'=>'Backup-Optionen',
	'other_files'=>'Sonstige Datei',
	'preparing_file'=>'Daten fürs Wiederherstellen vorbereiten...',
	'protect'=>'Die Backup-Datei schützen',
	'protect_txt'=>'Um die Backup-Datei von unrechtmäßigem Herunterladen zu schützen, wird zusätzlicher PHP-Code in die Backup-Datei eingefügt und die PHP-Datei-Erweiterung verwendet. Diese Schutz benötigt beim Import zusätzlichen Speicherplatz!',
	'query_is_too_big'=>'Die Backup-Datei enthält eine Datei, welche nicht wiederhergestellt werden konnte, da sie größer als %s bytes ist!',
	'question_taketime'=>'Der Export dauert einige Zeit.',
	'question_wait'=>'Bitte haben Sie etwas Geduld!',
	'rebuild'=>'Automatischer Rebuild',
	'recover_backup_unsaved_changes'=>'Einige geöffnete Dateien haben noch ungespeicherte Änderungen. Bitte überprüfen Sie diese, bevor Sie fortfahren.',
	'recover_option'=>'Wiederherstellen-Optionen',
	'save_before'=>'Während des Wiederherstellens der Backup-Datei werden die vorhandenen Daten gelöscht! Es wird daher empfohlen, die vorhandenen Daten vorher zu speichern.',
	'save_not_checked'=>'Sie haben noch nicht ausgewählt, wohin die Backup-Datei gespeichert werden soll!',
	'save_question'=>'Möchten Sie dies jetzt tun?',
	'schedule_info'=>'Zeitgesteuerte Aktionen des Zeitplaner Moduls.',
	'select_server_file'=>'Wählen Sie die gewünschte Backup-Datei aus.',
	'select_upload_file'=>'Wiederherstellung aus lokaler Datei hochladen',
	'settings'=>'Einstellungen wiederherstellen',
	'settings_info'=>'webEdition Programmeinstellungen.',
	'shop_info'=>'Bestellungen des Shop Moduls.',
	'show_all'=>'Zeige alle Dateien',
	'spellchecker_info'=>'Daten der Rechtschreibprüfung: Einstellungen, allgemeine und persönliche Wörterbücher.',
	'step1'=>'Schritt 1/4 - Vorhandene Daten speichern',
	'step2'=>'Schritt 2/4 - Datenquelle auswählen',
	'step3'=>'Schritt 3/4 - Gesicherte Daten wiederherstellen',
	'step4'=>'Schritt 4/4 - Wiederherstellung beendet',
	'temporary_info'=>'Noch nicht veröffentlichte Dokumente und Objekte bzw. noch nicht veröffentlichte Änderungen.',
	'todo_info'=>'Mitteilungen und Aufgaben des ToDo/Messaging Moduls.',
	'tools_export_desc'=>'Hier können Sie die Daten der webEdition-Tools sichern. Wählen Sie bitte die gewünschte Tools aus.',
	'tools_import_desc'=>'Hier können Sie die Daten der webEdition-Tools wiederhestellen. Wählen Sie bitte die gewünschte Tools aus.',
	'too_big_file'=>'Die Datei `%s` kann nicht gespeichert werden, da die maximale Dateigröße überschritten wurde.',
	'unselect_dep2'=>'Sie haben `%s` abgewählt. Folgende Optionen werden automatisch abgewählt:',
	'unselect_dep3'=>'Sie können trotzdem die nicht selektierten Optionen auswählen.',
	'unspecified_error'=>'Ein unbekannter Fehler ist aufgetreten',
	'upload_failed'=>'Die Datei kann nicht hochgeladen werden! Prüfen Sie bitte ob die Größe der Datei %s überschreitet',
	'user_info'=>'Benutzer und Zugangsdaten der Benutzerverwaltung.',
	'versions_binarys_info'=>'Achtung! Diese Option kann sehr zeit- und speicherintensiv sein da der Ordner /webEdition/we/versions/ unter Umständen sehr groß sein kann. Daher wird empfohlen diesen Ordner manuell zu sichern.',
	'versions_info'=>'Daten der Versionierung.',
	'view_log'=>'Backup-Log',
	'view_log_not_found'=>'Keine Backup-Log-Datei gefunden!',
	'view_log_no_perm'=>'Sie haben nicht die notwendigen Rechte, die Backup-Log-Datei einzusehen!',
	'voting_info'=>'Daten aus dem Voting Modul.',
	'warning'=>'Warnung',
	'we_backups'=>'webEdition Backups',
	'wizard_backup_title'=>'Backup erstellen Wizard',
	'wizard_recover_title'=>'Backup wiederherstellen Wizard',
	'wizard_title'=>'Backup wiederherstellen Wizard',
	'wizard_title_export'=>'Backup Wizard',
	'workflow_info'=>'Daten des Workflow Moduls.',
	'working'=>'In Arbeit...',
	'zip'=>'zip',
);