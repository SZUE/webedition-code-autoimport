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
 * Language file: we_editor.inc.php
 * Provides language strings.
 * Language: Deutsch
 */
$l_weEditor["doubble_field_alert"] = "Das Feld '%s' gibt es schon! Bitte beachten Sie, daß Feldnamen nur einmal vorkommen dürfen!";
$l_weEditor["variantNameInvalid"] = "Der Name einer Artikel-Variante darf nicht leer sein!";

$l_weEditor["folder_save_nok_parent_same"] = "Das ausgewählte Eltern-Verzeichnis liegt innerhalb des aktuellen Verzeichnisses! Bitte wählen Sie ein anderes Verzeichnis aus und versuchen Sie es noch einmal!";
$l_weEditor["pfolder_notsave"] = "Das Verzeichnis darf im ausgewählten Verzeichnis nicht gespeichert werden!";
$l_weEditor["required_field_alert"] = "Das Feld '%s' ist ein Pflichtfeld und muß ausgefüllt sein!";

$l_weEditor["category"]["response_save_ok"] = "Die Kategorie '%s' wurde erfolgreich gespeichert!";
$l_weEditor["category"]["response_save_notok"] = "Fehler beim Speichern der Kategorie '%s'!";
$l_weEditor["category"]["response_path_exists"] = "Die Kategorie '%s' konnte nicht gespeichert werden, da es bereits eine andere Kategorie an dieser Stelle gibt!";
$l_weEditor["category"]["we_filename_notValid"] = 'Der eingegebene Name ist nicht gültig!\nErlaubt sind alle Zeichen außer ", \' / < > und \\\\';
$l_weEditor["category"]["name_komma"] = "Der eingegebene Name ist nicht gültig!\\nKommas sind nicht erlaubt";
$l_weEditor["category"]["filename_empty"]       = "Der Name darf nicht leer sein";

$l_weEditor["text/webedition"]["response_save_ok"] = "Die webEdition-Seite '%s' wurde erfolgreich gespeichert!";
$l_weEditor["text/webedition"]["response_publish_ok"] = "Die webEdition-Seite '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["text/webedition"]["response_publish_notok"] = "Fehler beim Veröffentlichen der webEdition-Seite '%s'!";
$l_weEditor["text/webedition"]["response_unpublish_ok"] = "Die webEdition-Seite '%s' wurde erfolgreich geparkt!";
$l_weEditor["text/webedition"]["response_unpublish_notok"] = "Fehler beim Parken der webEdition-Seite '%s'!";
$l_weEditor["text/webedition"]["response_not_published"] = "Die webEdition-Seite '%s' ist nicht veröffentlicht!";
$l_weEditor["text/webedition"]["response_save_notok"] = "Fehler beim Speichern der webEdition-Seite '%s'!";
$l_weEditor["text/webedition"]["response_path_exists"] = "Die webEdition-Seite '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["text/webedition"]["filename_empty"] = "Sie haben noch keinen Dateinamen eingegeben!";
$l_weEditor["text/webedition"]["we_filename_notValid"] = "Der eingegebene Dateiname ist nicht gültig!\\nErlaubte Zeichen sind Buchstaben von a bis z (Groß- oder Kleinschreibung), Zahlen, Unterstrich (_), Minus (-) und Punkt (.).";
$l_weEditor["text/webedition"]["we_filename_notAllowed"] = "Der eingegebene Dateiname ist nicht erlaubt!";
$l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"] = "Die Datei konnte nicht gespeichert werden, da Sie nicht die notwendigen Rechte besitzen, um neue Verzeichnisse (%s) anzulegen!";
$l_weEditor["text/webedition"]["autoschedule"] = "Die webEdition-Seite wird am %s automatisch veröffentlicht!";

$l_weEditor["text/html"]["response_save_ok"] = "Die HTML-Datei '%s' wurde erfolgreich gespeichert!";
$l_weEditor["text/html"]["response_publish_ok"] = "Die HTML-Datei '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["text/html"]["response_publish_notok"] = "Fehler beim Veröffentlichen der HTML-Datei '%s'!";
$l_weEditor["text/html"]["response_unpublish_ok"] = "Die HTML-Datei '%s' wurde erfolgreich geparkt!";
$l_weEditor["text/html"]["response_unpublish_notok"] = "Fehler beim Parken der HTML-Datei '%s'!";
$l_weEditor["text/html"]["response_not_published"] = "Die HTML-Datei '%s' ist nicht veröffentlicht!";
$l_weEditor["text/html"]["response_save_notok"] = "Fehler beim Speichern der HTML-Datei '%s'!";
$l_weEditor["text/html"]["response_path_exists"] = "Die HTML-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["text/html"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/html"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/html"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/html"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/html"]["autoschedule"] = "Die HTML-Datei wird am %s automatisch veröffentlicht!";

$l_weEditor["text/weTmpl"]["response_save_ok"] = "Die Vorlage '%s' wurde erfolgreich gespeichert!";
$l_weEditor["text/weTmpl"]["response_publish_ok"] = "Die Vorlage '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["text/weTmpl"]["response_unpublish_ok"] = "Die Vorlage '%s' wurde erfolgreich geparkt!";
$l_weEditor["text/weTmpl"]["response_save_notok"] = "Fehler beim Speichern der Vorlage '%s'!";
$l_weEditor["text/weTmpl"]["response_path_exists"] = "Die Vorlage '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["text/weTmpl"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/weTmpl"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/weTmpl"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/weTmpl"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/weTmpl"]["no_template_save"] = "In " . "der " . "D" . "e" . "mo " . "Version " . "können " . "keine " . "Vorlagen " . "gesichert " . "werden.";

$l_weEditor["text/css"]["response_save_ok"] = "Die CSS-Datei '%s' wurde erfolgreich gespeichert!";
$l_weEditor["text/css"]["response_publish_ok"] = "Die CSS-Datei '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["text/css"]["response_unpublish_ok"] = "Die CSS-Datei '%s' wurde erfolgreich geparkt!";
$l_weEditor["text/css"]["response_save_notok"] = "Fehler beim Speichern der CSS-Datei '%s'!";
$l_weEditor["text/css"]["response_path_exists"] = "Die CSS-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["text/css"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/css"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/css"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/css"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/js"]["response_save_ok"] = "Die Javascript-Datei '%s' wurde erfolgreich gespeichert!";
$l_weEditor["text/js"]["response_publish_ok"] = "Die Javascript-Datei '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["text/js"]["response_unpublish_ok"] = "Die Javascript-Datei '%s' wurde erfolgreich geparkt!";
$l_weEditor["text/js"]["response_save_notok"] = "Fehler beim Speichern desr Javascript-Datei '%s'!";
$l_weEditor["text/js"]["response_path_exists"] = "Die Javascript-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["text/js"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/js"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/js"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/js"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/plain"]["response_save_ok"] = "Die Text-Datei '%s' wurde erfolgreich gespeichert!";
$l_weEditor["text/plain"]["response_publish_ok"] = "Die Text-Datei '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["text/plain"]["response_unpublish_ok"] = "Die Text-Datei '%s' wurde erfolgreich geparkt!";
$l_weEditor["text/plain"]["response_save_notok"] = "Fehler beim Speichern der Text-Datei '%s'!";
$l_weEditor["text/plain"]["response_path_exists"] = "Die Text-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["text/plain"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/plain"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/plain"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/plain"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/htaccess"]["response_save_ok"] = "Die Datei '%s' wurde erfolgreich gespeichert!";
$l_weEditor["text/htaccess"]["response_publish_ok"] = "Die Datei '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["text/htaccess"]["response_unpublish_ok"] = "Die Datei '%s' wurde erfolgreich geparkt!";
$l_weEditor["text/htaccess"]["response_save_notok"] = "Fehler beim Speichern der Datei '%s'!";
$l_weEditor["text/htaccess"]["response_path_exists"] = "Die Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["text/htaccess"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/htaccess"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/htaccess"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/htaccess"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/xml"]["response_save_ok"] = "Die XML-Datei '%s' wurde erfolgreich gespeichert!";
$l_weEditor["text/xml"]["response_publish_ok"] = "Die XML-Datei '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["text/xml"]["response_unpublish_ok"] = "Die XML-Datei '%s' wurde erfolgreich geparkt!";
$l_weEditor["text/xml"]["response_save_notok"] = "Fehler beim Speichern der XML-Datei '%s'!";
$l_weEditor["text/xml"]["response_path_exists"] = "Die XML-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["text/xml"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/xml"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/xml"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/xml"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["folder"]["response_save_ok"] = "Das Verzeichnis '%s' wurde erfolgreich gespeichert!";
$l_weEditor["folder"]["response_publish_ok"] = "Das Verzeichnis '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["folder"]["response_unpublish_ok"] = "Das Verzeichnis '%s' wurde erfolgreich geparkt!";
$l_weEditor["folder"]["response_save_notok"] = "Fehler beim Speichern des Verzeichnisses '%s'!";
$l_weEditor["folder"]["response_path_exists"] = "Das Verzeichnis '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["folder"]["filename_empty"] = "Sie haben noch keinen Namen für das Verzeichnis eingegeben!";
$l_weEditor["folder"]["we_filename_notValid"] = "Der eingegebene Name für das Verzeichnis ist nicht gültig!\\nErlaubte Zeichen sind Buchstaben von a bis z (Groß- oder Kleinschreibung), Zahlen, Unterstrich (_), Minus (-) und Punkt (.).";
$l_weEditor["folder"]["we_filename_notAllowed"] = "Der eingegebene Name für das Verzeichnis ist nicht erlaubt!";
$l_weEditor["folder"]["response_save_noperms_to_create_folders"] = "Das Verzeichnis konnte nicht gespeichert werden, da Sie nicht die notwendigen Rechte besitzen, um neue Verzeichnisse (%s) anzulegen!";

$l_weEditor["image/*"]["response_save_ok"] = "Die Grafik '%s' wurde erfolgreich gespeichert";
$l_weEditor["image/*"]["response_publish_ok"] = "Die Grafik '%s' wurde erfolgreich veröffentlicht";
$l_weEditor["image/*"]["response_unpublish_ok"] = "Die Grafik '%s' wurde erfolgreich geparkt";
$l_weEditor["image/*"]["response_save_notok"] = "Fehler beim Speichern der Grafik '%s'!";
$l_weEditor["image/*"]["response_path_exists"] = "Die Grafik '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["image/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["image/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["image/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["image/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["application/*"]["response_save_ok"] = "Die Datei '%s' wurde erfolgreich gespeichert!";
$l_weEditor["application/*"]["response_publish_ok"] = "Die Datei '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["application/*"]["response_unpublish_ok"] = "Die Datei '%s' wurde erfolgreich geparkt!";
$l_weEditor["application/*"]["response_save_notok"] = "Fehler beim Speichern der Datei '%s'!";
$l_weEditor["application/*"]["response_path_exists"] = "Die Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["application/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["application/*"]["we_description_missing"] = "Bitte geben Sie eine Beschreibung im Feld 'Beschreibung' an!";
$l_weEditor["application/*"]["response_save_wrongExtension"] = "Fehler beim Speichern von '%s' \\nDie Dateierweiterung '%s' ist bei sonstigen Dateien nicht erlaubt!\\nBitte legen Sie dafür eine HTML-Datei an!";

$l_weEditor["application/x-shockwave-flash"]["response_save_ok"] = "Die Flash-Datei '%s' wurde erfolgreich gespeichert!";
$l_weEditor["application/x-shockwave-flash"]["response_publish_ok"] = "Die Flash-Datei '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["application/x-shockwave-flash"]["response_unpublish_ok"] = "Die Flash-Datei '%s' wurde erfolgreich geparkt!";
$l_weEditor["application/x-shockwave-flash"]["response_save_notok"] = "Fehler beim Speichern der Flash-Datei '%s'!";
$l_weEditor["application/x-shockwave-flash"]["response_path_exists"] = "Die Flash-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["application/x-shockwave-flash"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/x-shockwave-flash"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["video/quicktime"]["response_save_ok"] = "Die Quicktime-Datei '%s' wurde erfolgreich gespeichert!";
$l_weEditor["video/quicktime"]["response_publish_ok"] = "Die Quicktime-Datei '%s' wurde erfolgreich veröffentlicht!";
$l_weEditor["video/quicktime"]["response_unpublish_ok"] = "Die Quicktime-Datei '%s' wurde erfolgreich geparkt!";
$l_weEditor["video/quicktime"]["response_save_notok"] = "Fehler beim Speichern der Quicktime-Datei '%s'!";
$l_weEditor["video/quicktime"]["response_path_exists"] = "Die Quicktime-Datei '%s' konnte nicht gespeichert werden, da es bereits eine andere Datei oder ein anderes Verzeichnis an dieser Stelle gibt!";
$l_weEditor["video/quicktime"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["video/quicktime"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["video/quicktime"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["video/quicktime"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

/*****************************************************************************
 * PLEASE DON'T TOUCH THE NEXT LINES
 * UNLESS YOU KNOW EXACTLY WHAT YOU ARE DOING!
 *****************************************************************************/

$_language_directory = $_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/modules";
$_directory = dir($_language_directory);

while (false !== ($entry = $_directory->read())) {
	if (strstr($entry, '_we_editor')) {
		include($_language_directory."/".$entry);
	}
}
