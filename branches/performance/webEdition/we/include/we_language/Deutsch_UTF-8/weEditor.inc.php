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
 * Language file: we_editor.inc.php
 * Provides language strings.
 * Language: Deutsch
 */
$l__tmp = array(
		'filename_empty' => "Sie haben noch keinen Dateinamen eingegeben!",
		'we_filename_notValid' => "Der eingegebene Dateiname ist nicht gültig!\\nErlaubte Zeichen sind Buchstaben von a bis z (Groß- oder Kleinschreibung), Zahlen, Unterstrich (_), Minus (-) und Punkt (.).",
		'we_filename_notAllowed' => "Der eingegebene Dateiname ist nicht erlaubt!",
		'response_save_noperms_to_create_folders' => "Die Datei konnte nicht gespeichert werden, da Sie nicht die notwendigen Rechte besitzen, um neue Verzeichnisse (%s) anzulegen!",
);
$l_weEditor = array(
		'doubble_field_alert' => "Das Feld '%s' gibt es schon! Bitte beachten Sie, daß Feldnamen nur einmal vorkommen dürfen!",
		'variantNameInvalid' => "Der Name einer Artikel-Variante darf nicht leer sein!",
		'folder_save_nok_parent_same' => "Das ausgewählte Eltern-Verzeichnis liegt innerhalb des aktuellen Verzeichnisses! Bitte wählen Sie ein anderes Verzeichnis aus und versuchen Sie es noch einmal!",
		'pfolder_notsave' => "Das Verzeichnis darf im ausgewählten Verzeichnis nicht gespeichert werden!",
		'required_field_alert' => "Das Feld '%s' ist ein Pflichtfeld und muß ausgefüllt sein!",
		'category' => array(
				'response_save_ok' => "Die Kategorie '%s' wurde erfolgreich gespeichert!",
				'response_save_notok' => "Fehler beim Speichern der Kategorie '%s'!",
				'response_path_exists' => "Die Kategorie '%s' konnte nicht gespeichert werden, da es bereits eine andere Kategorie an dieser Stelle gibt!",
				'we_filename_notValid' => 'Der eingegebene Name ist nicht gültig!\nErlaubt sind alle Zeichen außer ", \' / < > und \\\\',
				'name_komma' => "Der eingegebene Name ist nicht gültig!\\nKommas sind nicht erlaubt",
				'filename_empty' => "Der Name darf nicht leer sein",
		),
		'text/webedition' => array_merge($l__tmp, array(
				'response_save_ok' => "Die webEdition-Seite '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die webEdition-Seite '%s' wurde erfolgreich veröffentlicht!",
				'response_publish_notok' => "Fehler beim Veröffentlichen der webEdition-Seite '%s'!",
				'response_unpublish_ok' => "Die webEdition-Seite '%s' wurde erfolgreich geparkt!",
				'response_unpublish_notok' => "Fehler beim Parken der webEdition-Seite '%s'!",
				'response_not_published' => "Die webEdition-Seite '%s' ist nicht veröffentlicht!",
				'response_save_notok' => "Fehler beim Speichern der webEdition-Seite '%s'!",
				'response_path_exists' => "Die webEdition-Seite '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
				'autoschedule' => "Die webEdition-Seite wird am %s automatisch veröffentlicht!",
		)),
		'text/html' => array_merge($l__tmp, array(
				'response_save_ok' => "Die HTML-Datei '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die HTML-Datei '%s' wurde erfolgreich veröffentlicht!",
				'response_publish_notok' => "Fehler beim Veröffentlichen der HTML-Datei '%s'!",
				'response_unpublish_ok' => "Die HTML-Datei '%s' wurde erfolgreich geparkt!",
				'response_unpublish_notok' => "Fehler beim Parken der HTML-Datei '%s'!",
				'response_not_published' => "Die HTML-Datei '%s' ist nicht veröffentlicht!",
				'response_save_notok' => "Fehler beim Speichern der HTML-Datei '%s'!",
				'response_path_exists' => "Die HTML-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
				'autoschedule' => "Die HTML-Datei wird am %s automatisch veröffentlicht!",
		)),
		'text/weTmpl' => array_merge($l__tmp, array(
				'response_save_ok' => "Die Vorlage '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die Vorlage '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Die Vorlage '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern der Vorlage '%s'!",
				'response_path_exists' => "Die Vorlage '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
				'no_template_save' => "In " . "der " . "D" . "e" . "mo " . "Version " . "können " . "keine " . "Vorlagen " . "gesichert " . "werden.",
		)),
		'text/css' => array_merge($l__tmp, array(
				'response_save_ok' => "Die CSS-Datei '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die CSS-Datei '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Die CSS-Datei '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern der CSS-Datei '%s'!",
				'response_path_exists' => "Die CSS-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
		)),
		'text/js' => array_merge($l__tmp, array(
				'response_save_ok' => "Die Javascript-Datei '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die Javascript-Datei '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Die Javascript-Datei '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern desr Javascript-Datei '%s'!",
				'response_path_exists' => "Die Javascript-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
		)),
		'text/plain' => array_merge($l__tmp, array(
				'response_save_ok' => "Die Text-Datei '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die Text-Datei '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Die Text-Datei '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern der Text-Datei '%s'!",
				'response_path_exists' => "Die Text-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
		)),
		'text/htaccess' => array_merge($l__tmp, array(
				'response_save_ok' => "Die Datei '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die Datei '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Die Datei '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern der Datei '%s'!",
				'response_path_exists' => "Die Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
		)),
		'text/xml' => array_merge($l__tmp, array(
				'response_save_ok' => "Die XML-Datei '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die XML-Datei '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Die XML-Datei '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern der XML-Datei '%s'!",
				'response_path_exists' => "Die XML-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
		)),
		'folder' => array(
				'response_save_ok' => "Das Verzeichnis '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Das Verzeichnis '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Das Verzeichnis '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern des Verzeichnisses '%s'!",
				'response_path_exists' => "Das Verzeichnis '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
				'filename_empty' => "Sie haben noch keinen Namen für das Verzeichnis eingegeben!",
				'we_filename_notValid' => "Der eingegebene Name für das Verzeichnis ist nicht gültig!\\nErlaubte Zeichen sind Buchstaben von a bis z (Groß- oder Kleinschreibung), Zahlen, Unterstrich (_), Minus (-) und Punkt (.).",
				'we_filename_notAllowed' => "Der eingegebene Name für das Verzeichnis ist nicht erlaubt!",
				'response_save_noperms_to_create_folders' => "Das Verzeichnis konnte nicht gespeichert werden, da Sie nicht die notwendigen Rechte besitzen, um neue Verzeichnisse (%s) anzulegen!",
		),
		'image/*' => array_merge($l__tmp, array(
				'response_save_ok' => "Die Grafik '%s' wurde erfolgreich gespeichert",
				'response_publish_ok' => "Die Grafik '%s' wurde erfolgreich veröffentlicht",
				'response_unpublish_ok' => "Die Grafik '%s' wurde erfolgreich geparkt",
				'response_save_notok' => "Fehler beim Speichern der Grafik '%s'!",
				'response_path_exists' => "Die Grafik '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
		)),
		'application/*' => array_merge($l__tmp, array(
				'response_save_ok' => "Die Datei '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die Datei '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Die Datei '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern der Datei '%s'!",
				'response_path_exists' => "Die Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
				'we_description_missing' => "Bitte geben Sie eine Beschreibung im Feld 'Beschreibung' an!",
				'response_save_wrongExtension' => "Fehler beim Speichern von '%s' \\nDie Dateierweiterung '%s' ist bei sonstigen Dateien nicht erlaubt!\\nBitte legen Sie dafür eine HTML-Datei an!",
		)),
		'application/x-shockwave-flash' => array_merge($l__tmp, array(
				'response_save_ok' => "Die Flash-Datei '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die Flash-Datei '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Die Flash-Datei '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern der Flash-Datei '%s'!",
				'response_path_exists' => "Die Flash-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
		)),
		'video/quicktime' => array_merge($l__tmp, array(
				'response_save_ok' => "Die Quicktime-Datei '%s' wurde erfolgreich gespeichert!",
				'response_publish_ok' => "Die Quicktime-Datei '%s' wurde erfolgreich veröffentlicht!",
				'response_unpublish_ok' => "Die Quicktime-Datei '%s' wurde erfolgreich geparkt!",
				'response_save_notok' => "Fehler beim Speichern der Quicktime-Datei '%s'!",
				'response_path_exists' => "Die Quicktime-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!",
		)),
);


/* * ***************************************************************************
 * PLEASE DON'T TOUCH THE NEXT LINES
 * UNLESS YOU KNOW EXACTLY WHAT YOU ARE DOING!
 * *************************************************************************** */

$_language_directory = dirname(__FILE__) . "/modules";
$_directory = dir($_language_directory);

while (false !== ($entry = $_directory->read())) {
	if (strstr($entry, '_we_editor')) {
		include($_language_directory . "/" . $entry);
	}
}
