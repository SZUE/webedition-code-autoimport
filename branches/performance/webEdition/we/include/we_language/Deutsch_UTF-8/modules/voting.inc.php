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
 * Language file: voting.inc.php
 * Provides language strings.
 * Language: German
 */
$l_modules_voting = array(
		'no_perms' => 'Sie haben nicht die Berechtigung, diese Option auszuwählen.',
		'delete_alert' => 'Aktuelles Voting/Gruppe löschen.\\n Sind Sie sich sicher?',
		'result_delete_alert' => 'Die aktuellen Voting-Ergebnisse werden gelöscht.\\nSind Sie sich sicher?',
		'nothing_to_delete' => 'Nichts zu löschen!',
		'nothing_to_save' => 'Nichts zu speichern',
		'we_filename_notValid' => 'Kein korrekter Benutzername!\\nZugelassen sind alphanumerische Zeichen, Groß- und Kleinschreibung, ebenso wie Unterstrich, Bindestrich, Punkt und Leerzeichen (a-z, A-Z, 0-9, _, -, ., )',
		'menu_new' => 'Neu',
		'menu_save' => 'Speichern',
		'menu_delete' => 'Löschen',
		'menu_exit' => 'Schließen',
		'menu_info' => 'Info',
		'menu_help' => 'Hilfe',
		'headline' => 'Namen und Nachnamen',
		'headline_name' => 'Name',
		'headline_publish_date' => 'Datum der Einstellung',
		'headline_data' => 'Befragungsdaten',
		'publish_date' => 'Datum',
		'publish_format' => 'Format',
		'published_on' => 'Veröffentlicht am',
		'total_voting' => 'Gesamtstimmen',
		'reset_scores' => 'Punktezählung zurücksetzen',
		'inquiry_question' => 'Frage',
		'inquiry_answers' => 'Antworten',
		'question_empty' => 'Das Fragefeld ist leer. Bitte ausfüllen!',
		'answer_empty' => 'Eines oder mehrere Antwortfelder sind leer. Bitte Antwort(en) eingeben!',
		'invalid_score' => 'Die Wert der Punktezählung muss eine Nummer sein; bitte neu eingeben!',
		'headline_revote' => 'Neu abstimmen',
		'headline_help' => 'Hilfe',
		'inquiry' => 'Befragung',
		'browser_vote' => 'Dieser Browser kann nicht neu abstimmen vor ablauf von',
		'one_hour' => '1 Stunde',
		'feethteen_minutes' => '15 Minuten',
		'thirthty_minutes' => '30 Minuten',
		'one_day' => '1 Tag',
		'never' => '--nie--',
		'always' => '--immer--',
		'cookie_method' => 'Durch Cookie Methode',
		'ip_method' => 'Durch IP Methode',
		'time_after_voting_again' => 'Zeit bis zur Neuabstimmung',
		'cookie_method_help' => 'Nutzen Sie diese Methode, wenn Sie die IP Methode nicht nutzen können/wollen. Bedenken Sie bitte, dass Benutzer Cookies im Browser deaktivieren können. Die "Fallback IP-Methode" Option benötigt die Nutzung des we:cookie Tags in der Vorlage.',
		'ip_method_help' => 'Sollte Ihre Website nur Intranet Zugang haben und Ihre Benutzer verbinden sich nicht über einen Proxy, benutzen Sie diese Methode. Bedenken Sie, dass manche Server dynamische IP-Adressen vergeben.',
		'time_after_voting_again_help' => 'Um zu vermeiden, dass einundderselbe Benutzer häufiger abstimmt, geben Sie hier eine Zeitspanne ein, die vergehen muss, bevor von diesem Computer wieder abgestimmt werden kann. Bei Computern, die für mehrere Benutzer zugänglich sind, ist dies der sinnvollste Weg. Ansonsten wählen Sie "nie".',
		'property' => 'Eigenschaften',
		'variant' => 'Version',
		'voting' => 'Voting',
		'result' => 'Ergebnis',
		'group' => 'Gruppe',
		'name' => 'Name',
		'newFolder' => 'Neue Gruppe',
		'save_group_ok' => 'Gruppe wurde gespeichert.',
		'save_ok' => 'Voting wurde gespeichert.',
		'path_nok' => 'Der Pfad ist nicht korrekt!',
		'name_empty' => 'Der Name darf nicht leer sein!',
		'name_exists' => 'Der Name existiert bereits!',
		'wrongtext' => 'Der Name ist nicht gültig!\\nErlaubte Zeichen sind Buchstaben von a bis z (Groß- oder Kleinschreibung), Zahlen, Unterstrich (_), Minus (-), Punkt (.), Leerzeichen ( ) und Klammeraffen (@).',
		'voting_deleted' => 'Das Voting wurde erfolgreich gelöscht.',
		'group_deleted' => 'Die Gruppe wurde erfolgreich gelöscht.',
		'access' => 'Zugriff',
		'limit_access' => 'Zugriff einschränken',
		'limit_access_text' => 'Zugriff nur für folgende Benutzer',
		'variant_limit' => 'Es muss mindestenst eine Version geben!',
		'valid_txt' => 'Die Checkbox "Aktiv" muss aktiviert sein, damit das Voting auf Ihrer Seite gespeichert und nach Ablauf der Gültigkeit "geparkt" wird. Legen Sie mit den Dropdownmenüs das Datum und die Uhrzeit fest, zu welchem das Voting ablaufen soll. Es werden ab diesem Zeitpunkt keine Stimmen mehr angenommen.',
		'active_till' => 'Aktiv',
		'valid' => 'Gültigkeit',
		'export' => 'Export',
		'export_txt' => 'Export von Voting Daten in eine CSV-Datei (Comma Separated Values).',
		'csv_download' => "CSV-Datei herunterladen",
		'csv_export' => "Die Datei '%s' wurde gespeichert.",
		'fallback' => 'Fallback IP-Methode',
		'save_user_agent' => 'Daten des Benutzer-Agents speichern/vergleichen',
		'save_changed_voting' => "Das Voting wurde geändert.\\nMöchten Sie Ihre Änderungen speichern?",
		'voting_log' => 'Voting protokollieren',
		'forbid_ip' => 'Folgende IP-Adresse sperren',
		'until' => 'bis',
		'options' => 'Optionen',
		'control' => 'Kontrolle',
		'data_deleted_info' => 'Die Daten wurden gelöscht!',
		'time' => 'Zeit',
		'ip' => 'IP',
		'user_agent' => 'Benutzer-Agent',
		'cookie' => 'Cookie',
		'delete_ipdata_question' => 'Sie möchten alle gespeicherten IP-Daten löchen. Sind Sie sicher?',
		'delete_log_question' => 'Sie möchten alle Einträge des Voting Logbuches löchen. Sind Sie sicher?',
		'delete_ipdata_text' => 'Die gespeicherten IP-Daten belegen %s Bytes des Speichers. Sie können sie mit dem \'Löschen\' Knopf löschen. Bitte achten Sie darauf, dass alle gespeichrten IP-Daten des Votings gelöscht wurden und die Voting-Ergebnise werden nicht mehr präzise sind, weil wiederholte Abstimmungen möglich werden.',
		'status' => 'Status',
		'log_success' => 'Erfolg',
		'log_error' => 'Fehler',
		'log_error_active' => 'Fehler: nicht aktiv',
		'log_error_revote' => 'Fehler: Versuch einer erneuten Abstimmung',
		'log_error_blackip' => 'Fehler: gesperrte IP',
		'log_is_empty' => 'Das Logbuch ist leer!',
		'enabled' => 'Aktiviert',
		'disabled' => 'Deaktiviert',
		'log_fallback' => 'Fallback',
		'new_ip_add' => 'Bitte geben Sie die neue IP-Adresse ein!',
		'not_valid_ip' => 'Die IP-Adresse ist nicht gültig',
		'not_active' => 'Das eingegebene Gültigkeitsdatum liegt in der Vergangenheit!',
		'headline_datatype' => 'Befragungsart',
		'AllowFreeText' => 'Erlaube freie Texteingabe',
		'AllowImages' => 'Erlaube Bilder',
		'AllowSuccessor' => 'Genereller Redirect nach:',
		'AllowSuccessors' => 'Erlaube individuelle Redirects',
		'answer_limit' => 'Die Befragung muss mindestens zwei - bei erlaubter Freitexteingabe eine - Antwort(en) enthalten!',
		'csv_charset' => "Export-Zeichensatz",
		'imageID_text' => "Bild-ID",
		'successorID_text' => "Nachfolger-ID",
		'mediaID_text' => "Media-ID",
		'AllowMedia' => 'Erlaube Media-Dateien wie Audio- und Video-Dateien',
		'answerID' => 'AntwortID',
		'answerText' => 'Antworttext',
		'userid_method' => 'Für eingelogte Nutzer (Kundenverwaltung) mit der gespeicherten <br />Kunden-ID (das Logbuch muss aktiv sein) vergleichen',
		'IsRequired' => 'Mache dies zu einem Pflichtfeld',
		'voting-id' => 'Voting-ID',
		'voting-session' => 'Voting-Session',
		'voting-successor' => 'Nachfolger',
		'voting-additionalfields' => 'Zusatzdaten',
		'log_error_required' => 'Fehler: Dies ist ein Pflichtfeld',
		'folder_path_exists' => "Diese Gruppe existiert schon!",
);