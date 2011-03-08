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
 * Language file: wysiwyg.inc.php
 * Provides language strings.
 * Language: Deutsch
 */
include_once(dirname(__FILE__) . "/wysiwyg_js.inc.php");

/* * ***************************************************************************
 * CONTEXT MENUS
 * *************************************************************************** */
$l_wysiwyg = array_merge($l_wysiwyg, array(
		'cut' => "Ausschneiden",
		'copy' => "Kopieren",
		'paste' => "Einfügen",
		'insert_row' => "Zeile einfügen",
		'delete_rows' => "Zeilen löschen",
		'insert_colmn' => "Spalte einfügen",
		'delete_colmns' => "Spalten löschen",
		'insert_cell' => "Zelle einfügen",
		'delete_cells' => "Zellen löschen",
		'merge_cells' => "Zellen verbinden",
		'split_cell' => "Zellen teilen",
		/*		 * ***************************************************************************
		 * ALT-TEXTS FOR BUTTONS
		 * *************************************************************************** */

		'subscript' => "Tiefgestellt",
		'superscript' => "Hochgestellt",
		'justify_full' => "Blocksatz",
		'strikethrought' => "Durchgestrichen",
		'removeformat' => "Formatierung löschen",
		'removetags' => "Tags, Styles und Kommentare löschen",
		'inserttable' => "Tabelle einfügen",
		'editcell' => "Tabellenzelle bearbeiten",
		'edittable' => "Tabelle bearbeiten",
		'insert_row2' => "Zeile einfügen",
		'delete_rows2' => "Zeilen löschen",
		'insert_colmn2' => "Spalte einfügen",
		'delete_colmns2' => "Spalten löschen",
		'insert_cell2' => "Zelle einfügen",
		'delete_cells2' => "Zellen löschen",
		'merge_cells2' => "Zellen verbinden",
		'split_cell2' => "Zellen teilen",
		'insert_edit_table' => "Tabelle einfügen/bearbeiten",
		'insert_edit_image' => "Grafik einfügen/bearbeiten",
		'edit_style_class' => "Klasse bearbeiten (Style)",
		'insert_br' => "Zeilenumbruch einfügen (SHIFT + RETURN)",
		'insert_p' => "Absatz einfügen",
		'edit_sourcecode' => "Quellcode bearbeiten",
		'show_details' => "Zeige Details",
		'rtf_import' => "RTF importieren",
		'unlink' => "Hyperlink entfernen",
		'hyperlink' => "Hyperlink einfügen/bearbeiten",
		'back_color' => "Hintergrundfarbe",
		'fore_color' => "Vordergrundfarbe",
		'outdent' => "Ausrücken",
		'indent' => "Einrücken",
		'unordered_list' => "Ungeordnete Liste",
		'ordered_list' => "Geordnete Liste",
		'justify_right' => "Rechts ausrichten",
		'justify_center' => "Zentrieren",
		'justify_left' => "Links ausrichten",
		'underline' => "Unterstrichen",
		'italic' => "Kursiv",
		'bold' => "Fett",
		'fullscreen' => "Editor im Fullscreen-Modus öffnen",
		'edit_source' => "Quellcode bearbeiten",
		'fullscreen_editor' => "Fullscreen Editor",
		'table_props' => "Tabelle bearbeiten",
		'edit_stylesheet' => "Stylesheet Klassen bearbeiten",
		/*		 * ***************************************************************************
		 * THE REST
		 * *************************************************************************** */

		'url' => "URL",
		'image_url' => "Bild URL",
		'width' => "Breite",
		'height' => "Höhe",
		'hspace' => "Horizontaler Abstand",
		'vspace' => "Vertikaler Abstand",
		'border' => "Rand",
		'altText' => "Alternativer Text",
		'alignment' => "Ausrichtung",
		'external_image' => "webEdition-externe Grafik",
		'internal_image' => "webEdition-interne Grafik",
		'bgcolor' => "Hintergrundfarbe",
		'cellspacing' => "Zellenabstand",
		'cellpadding' => "Innenabstand",
		'rows' => "Zeilen",
		'cols' => "Spalten",
		'colspan' => "Spannweite",
		'halignment' => "horiz. Ausrichtung",
		'valignment' => "vert. Ausrichtung",
		'color' => "Farbe",
		'choosecolor' => "Farbe auswählen",
		'parent_class' => "Eltern-Bereich",
		'region_class' => "Nur Auswahl",
		'edit_classname' => "Stylesheet Klasse bearbeiten",
		'emaillink' => "E-Mail",
		'clean_word' => "MS Word Code säubern",
		'addcaption' => "Beschriftung hinzufügen",
		'removecaption' => "Beschriftung entfernen",
		'anchor' => "Anker",
		'edit_hr' => "Horizontale Linie",
		'color' => "Farbe",
		'noshade' => "Ohne Schattierung",
		'strikethrough' => "Durchstreichen",
		'nothumb' => "keine Miniaturansicht",
		'thumbnail' => "Miniaturansicht",
		'acronym' => "Akronym",
		'acronym_title' => "Akronym bearbeiten",
		'abbr' => "Abkürzung",
		'abbr_title' => "Abkürzung bearbeiten",
		'title' => "Titel",
		'language' => "Sprache",
		'language_title' => "Sprache bearbeiten",
		'link_lang' => "Link",
		'href_lang' => "Verlinkte Seite ",
		'paragraph' => "Absatz",
		'summary' => "Zusammenfassung",
		'isheader' => "Ist Überschrift",
		'keyboard' => "Tastatur",
		'relation' => "Beziehung",
		'fontsize' => "Schriftgröße",
		'window_title' => "Feld '%s' bearbeiten",
		'format' => "Schriftstil",
		'fontsize' => "Schriftgröße",
		'fontname' => "Schriftname",
		'css_style' => "CSS Style",
		'normal' => "Normal (ohne)",
		'h1' => "Überschrift 1",
		'h2' => "Überschrift 2",
		'h3' => "Überschrift 3",
		'h4' => "Überschrift 4",
		'h5' => "Überschrift 5",
		'h6' => "Überschrift 6",
		'pre' => "Formatiert",
		'address' => "Adresse",
		'spellcheck' => 'Rechtschreibprüfung',
				));