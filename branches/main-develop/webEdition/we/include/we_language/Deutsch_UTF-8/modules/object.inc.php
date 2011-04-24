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
 * Language file: object.inc.php
 *
 * Provides language strings.
 *
 * Language: Deutsch
 */
$l_modules_object = array(
		'attributes' => "Attribute",
		'objectname' => "Objektname",
		'objectpath' => "Objektpfad",
		'objectid' => "Objekt-ID",
		'objecturl' => "Objekt-URL",
		'class' => "Klasse",
		'classname' => "Klassenname",
		'class_id' => "ID der Klasse",
		'default' => "Voreinstellung",
		'checked' => "ausgewählt",
		'name' => "Automatischer Name",
		'seourl' => "SEO-URL", //
		'seourltrigger' => "Voreinstellung Dokument für die Darstellung",
		'allFields' => "allen Feldern",
		'search_term' => "Suchbegriff",
		'search_field' => "Suchfeld",
		'defaultdir' => "Standardverzeichnis",
		'rootdir' => "Arbeitsbereich",
		'no_root_ws_select' => "Das Root Verzeichnis kann nicht als Arbeitbereich ausgewählt werden!",
		'objectFile_field' => "Objekt",
		'multiObjectFile_field' => "Multi Objekt",
		'checkbox_field' => "Checkbox",
		'meta_field' => "Select",
		'input_field' => "Textinput",
		'country_field' => "Land",
		'language_field' => "Sprache",
		'locale_field' => "Locale",
		'int_field' => "Integer",
		'float_field' => "Float",
		'date_field' => "Datum",
		'textarea_field' => "Textarea",
		'img_field' => "Grafik",
		'binary_field' => "Binary Dokument",
		'flashmovie_field' => "Flashmovie",
		'quicktime_field' => "Quicktime",
		'link_field' => "Link",
		'href_field' => "Href",
		'shopVat_field' => "Mehrwertsteuer Feld",
		'multiobject_recursion' => "In einem Multiobjekt kann ein Objekt nicht in sich selbst eingebunden werden!",
		'we_new_doc_after_save' => "Nach Speichern neues Objekt",
		'objectFile_response_save_ok' => "Das Objekt '%s' wurde erfolgreich gespeichert!",
		'objectFile_response_publish_ok' => "Das Objekt '%s' wurde erfolgreich veröffentlicht!",
		'objectFile_response_unpublish_ok' => "Das Objekt '%s' wurde erfolgreich geparkt!",
		'fieldNameNotValid' => "Der Feldname darf nur aus Buchstaben (groß oder klein, aber ohne Umlaute und Sonderzeichen), Zahlen (a-z, A-Z, 0-9) und Unterstrichen bestehen!",
		'fieldNameNotTitleDesc' => "Die Feldnamen Title und Description sind nicht zulässig!",
		'fieldNameEmpty' => "Der Feldname darf nicht leer sein!",
		'length' => "Länge",
		'type' => "Typ",
		'max_objects' => "max. Objekte",
		'no_maximum' => "leer für keine Beschränkung",
		'DefaultOwners' => "Standard User",
		'copyObject' => "Objekt kopieren",
		'copyClass' => "Klasse kopieren",
		'new_field' => "NeuesFeld",
		'behaviour' => "Anzeige",
		'behaviour_all' => "Ein Objekt wird immer angezeigt, wenn keine Arbeitsbereiche ausgewählt wurden!",
		'behaviour_no' => "Ein Objekt wird nicht angezeigt, wenn keine Arbeitsbereiche ausgewählt wurden!",
		'generated_template_for_objectFile' => "Kein passendes Template gefunden.",
		'no_workspace_defined' => "Diesem Objekt wurden noch keine Arbeitsbereiche zugewiesen. Wollen Sie einem Objekt spezielle Arbeitsbereiche zuordnen, müssen Sie diese in der Klasse des Objekts auswählen.",
		'use_thumbnail_preview' => "Vorschau in Objekten als Miniaturansicht",
		'not_published' => "das Objekt ist geparkt",
		'incObject_sameFieldname_start' => "Die inkludierte Klasse hat identischen Feldnamen: ",
		'incObject_sameFieldname_end' => ". Diese werden in Listviews und we:object nicht angezeigt.",
		'value' => array(
				'' => "----",
				'%unique%' => "unique",
				'%d%' => "Tag",
				'%m%' => "Monat (01-12)",
				'%n%' => "Monat (1-12)",
				'%y%' => "Jahr (11)",
				'%Y%' => "Jahr (2011)",
				'%h%' => "Stunden",
				'%ID%' => "ID",
				'Text' => "Text",
		),
		'url' => array(
				'' => "----",
				'%urlunique%' => "unique",
				'%d%' => "Erstellungs-Tag",
				'%m%' => "Erstellungs-Monat (01-12)",
				'%n%' => "Erstellungs-Monat (1-12)",
				'%y%' => "Erstellungs-Jahr (11)",
				'%Y%' => "Erstellungs-Jahr (2011)",
				'%h%' => "Erstellungs-Stunden",
				'%Md%' => "Modifikations-Tag",
				'%Mm%' => "Modifikations-Monat (01-12)",
				'%Mn%' => "Modifikations-Monat (1-12)",
				'%My%' => "Modifikations-Jahr (11)",
				'%MY%' => "Modifikations-Jahr (2011)",
				'%Mh%' => "Modifikations-Stunden",
				'%Fd%' => "Datumsfeld-Tag",
				'%Fm%' => "Datumsfeld-Monat (01-12)",
				'%Fn%' => "Datumsfeld-Monat (1-12)",
				'%Fy%' => "Datumsfeld-Jahr (11)",
				'%FY%' => "Datumsfeld-Jahr (2011)",
				'%Fh%' => "Datumsfeld-Stunden",
				'%ID%' => "ID",
				'Text' => "Text",
				'%Parent%' => "ElternOrdner",
				'%DirSep%' => "Verzeichnisseparator",
				'%PathIncC%' => "Pfad mit Klasse",
				'%PathNoC%' => "Pfad ohne Klasse",
				'%urlfield1%' => "URL-Feld 1",
				'%urlfield2%' => "URL-Feld 2",
				'%urlfield3%' => "URL-Feld 3",
				'%locale%' => "Locale",
				'%language%' => "Sprache",
				'%country%' => "Land",
		),
);