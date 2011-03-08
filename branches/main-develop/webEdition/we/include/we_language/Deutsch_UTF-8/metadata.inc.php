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
 * Language file: metadata.inc.php
 * Provides language strings.
 * Language: Deutsch
 */
/* * ***************************************************************************
 * DOCUMENT TAB
 * *************************************************************************** */
$l_metadata = array(
		'filesize' => "Dateigröße",
		'supported_types' => "Metadatenformate",
		'none' => "keine",
		'filetype' => "Dateityp",
		/*		 * ***************************************************************************
		 * METADATA FIELD MAPPING
		 * *************************************************************************** */

		'headline' => "Metadatenfelder",
		'tagname' => "Feldname",
		'type' => "Typ",
		'dummy' => "dummy",
		'save' => "Einstellungen werden gespeichert, einen Moment ...",
		'save_wait' => "Speichere Einstellungen",
		'saved' => "Die Einstellungen wurden erfolgreich gespeichert.",
		'saved_successfully' => "Einstellungen gespeichert",
		'properties' => "Eigenschaften",
		'fields_hint' => "Definieren Sie hier zusätzliche Felder für Metadaten. In der Originaldatei bereits hinterlegte Daten (Exif, IPTC) können beim Import automatisch übernommen werden. Geben Sie dazu ein oder mehrere einzulesende Felder im Eingabefeld &quot;importiere von&quot; im Format &quot;[Typ]/[Feldname]&quot; an. Beispiel: &quot;exif/copyright,iptc/copyright&quot;. Mehrere Felder können kommasepariert angegeben werden. Der Import durchsucht alle angegebenen Felder bis zum ersten mit Daten gefüllten Feld.<br><br>Weitere Informationen zum Exif-Standard finden Sie auf der <a target=\"_blank\" href=\"http://www.exif.org\">Exif Homepage</a>. Informationen über IPTC finden Sie auf der <a target=\"_blank\" href=\"http://www.iptc.org/IIM\">IPTC homepage</a>.",
		'import_from' => "Importiere von",
		'fields' => "Felder",
		'add' => "hinzufügen",
		/*		 * ***************************************************************************
		 * UPLOAD
		 * *************************************************************************** */

		'import_metadata_at_upload' => "Vorhandene Metadaten importieren",
		/*		 * ***************************************************************************
		 * ERROR MESSAGES
		 * *************************************************************************** */

		'error_meta_field_empty_msg' => "Der Feldname der %s1. Zeile darf nicht leer sein!",
		'meta_field_wrong_chars_messsage' => "Der Feldname '%s1' ist nicht gültig! Er darf nur aus Buchstaben (groß oder klein, aber ohne Umlaute und Sonderzeichen), Zahlen (a-z, A-Z, 0-9) und Unterstrichen bestehen!",
		'meta_field_wrong_name_messsage' => "Der Feldname '%s1' ist nicht gültig, da er von webEdition intern benutzt wird! Folgende Namen sind nicht zulässig:%s2",
		'file_size_0' => "Die Dateigröße ist 0 Byte, bitte laden Sie vor dem Speichern ein Dokument auf den Server",
		/*		 * ***************************************************************************
		 * INFO TAB
		 * *************************************************************************** */

		'info_exif_data' => "Exif Daten",
		'info_iptc_data' => "IPTC Daten",
		'no_exif_data' => "Keine Exif Daten vorhanden",
		'no_iptc_data' => "Keine IPTC Daten vorhanden",
		'no_exif_installed' => "Die PHP Exif Erweiterung ist nicht installiert!",
		'no_metadata_supported' => "webEdition unterstützt für diesen Dateityp keine Metadatenformate.",
);