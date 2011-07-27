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
 * Language: English
 */
include_once(dirname(__FILE__) . "/wysiwyg_js.inc.php");

$l_wysiwyg = array_merge($l_wysiwyg, array(
		'window_title' => "Edytuj pole '%s'",
		'format' => "Styl",
		'fontsize' => "Wilekość czcionki",
		'fontname' => "Nazwa czcionki",
		'css_style' => "Styl CSS",
		'normal' => "Normal", // TRANSLATE
		'h1' => "Nagłówek 1",
		'h2' => "Nagłówek 2",
		'h3' => "Nagłówek 3",
		'h4' => "Nagłówek 4",
		'h5' => "Nagłówek 5",
		'h6' => "Nagłówek 6",
		'pre' => "Sformatowano",
		'address' => "Adres",
		'spellcheck' => 'Spellchecking', // TRANSLATE


		/*		 * ***************************************************************************
		 * CONTEXT MENUS
		 * *************************************************************************** */

// REMEMBER: context menus cannot display any umlauts!
		'cut' => "Cut", // TRANSLATE
		'copy' => "Kopiuj",
		'paste' => "Wklej",
		'insert_row' => "Wstaw wiersz",
		'delete_rows' => "Kasuj wiersze",
		'insert_colmn' => "Wstaw kolumnę",
		'delete_colmns' => "Kasuj kolumny",
		'insert_cell' => "Wstaw komórkę",
		'delete_cells' => "Kasuj komórki",
		'merge_cells' => "Scal komórki",
		'split_cell' => "Podziel komórki",
		/*		 * ***************************************************************************
		 * ALT-TEXTS FOR BUTTONS
		 * *************************************************************************** */

		'subscript' => "Index dolny",
		'superscript' => "Index górny",
		'justify_full' => "Wyjustuj",
		'strikethrought' => "Przekreślenie",
		'removeformat' => "Kasuj formatowanie",
		'removetags' => "Remove tags, styles and comments", // TRANSLATE
		'editcell' => "Edytuj komórkę tabeli",
		'edittable' => "Edytuj tabelę",
		'insert_row2' => "Wstaw wiersz",
		'delete_rows2' => "Kasuj wiersze",
		'insert_colmn2' => "Wstaw kolumnę",
		'delete_colmns2' => "Kasuj kolumny",
		'insert_cell2' => "Wstaw komórkę",
		'delete_cells2' => "Kasuj komórki",
		'merge_cells2' => "Scal komórki",
		'split_cell2' => "Podziel komórki",
		'insert_edit_table' => "Tabela wklej/edytuj",
		'insert_edit_image' => "Grafika wklej/edytuj",
		'edit_style_class' => "Edytuj klasę (Style)",
		'insert_br' => "Wstaw przełamanie linii (SHIFT + RETURN)",
		'insert_p' => "Wstaw paragraf",
		'edit_sourcecode' => "Edytuj kod źródłowy",
		'show_details' => "Pokaż szczegóły",
		'rtf_import' => "Importuj RTF",
		'unlink' => "Usuń Hyperlink",
		'hyperlink' => "Hyperlink wklej/edytuj",
		'back_color' => "Kolor tła",
		'fore_color' => "Kolor panelu",
		'outdent' => "Usuń wcięcie",
		'indent' => "Wcięcie",
		'unordered_list' => "Nieuporządkowana lista",
		'ordered_list' => "Uporządkowana lista",
		'justify_right' => "Wyrównaj do prawej",
		'justify_center' => "Centruj",
		'justify_left' => "Wyrównaj do lewej",
		'underline' => "Podkreślenie",
		'italic' => "Kursywa",
		'bold' => "Wytłuść",
		'fullscreen' => "Otwórz edytor w trybie Fullscreen",
		'edit_source' => "Edytuj kod źródłowy",
		'fullscreen_editor' => "Fullscreen edytor",
		'table_props' => "Edytuj tabelę",
		'insert_table' => "Wstaw tabelę",
		'edit_stylesheet' => "Edytuj klasy Stylesheet",
		/*		 * ***************************************************************************
		 * THE REST
		 * *************************************************************************** */

		'url' => "URL", // TRANSLATE
		'image_url' => "Obrazek URL",
		'width' => "Szerokość",
		'height' => "Wysokość",
		'hspace' => "Odstęp poziomy",
		'vspace' => "Odstęp pionowy",
		'border' => "Krawędź",
		'altText' => "Tekst alternatywny",
		'alignment' => "Wyrównanie",
		'external_image' => "webEdition-zew. grafika",
		'internal_image' => "webEdition-wew. grafika",
		'bgcolor' => "Kolor tła",
		'cellspacing' => "Odległość komórek",
		'cellpadding' => "Margines w komórkach tabeli",
		'rows' => "Wiersze",
		'cols' => "Kolumny",
		'edit_table' => "Edytuj tabelę",
		'colspan' => "Rozpiętość",
		'halignment' => "Wyrównanie horyz.", // has to be short !!
		'valignment' => "Wyrównanie wert.", // has to be short !!
		'color' => "Color",
		'choosecolor' => "Wybierz kolor",
		'parent_class' => "Klasa bazowa",
		'region_class' => "Klasa potomna",
		'edit_classname' => "Edytuj klasę Stylesheet",
		'emaillink' => "E-mail",
		'clean_word' => "Czyść kod MS Word",
		'addcaption' => "Dodaj podpis",
		'removecaption' => "Usuń podpis",
		'anchor' => "Odnośnik",
		'edit_hr' => "Pozioma linia",
		'color' => "Kolor",
		'noshade' => "Bez cieniowania",
		'strikethrough' => "Przekreśl",
		'nothumb' => "Brak miniatury",
		'thumbnail' => "Widok miniatury",
		'acronym' => "Skrót",
		'acronym_title' => "Edytuj skrót",
		'abbr' => "Abbreviation", // TRANSLATE
		'abbr_title' => "Edit Abbreviation", // TRANSLATE
		'title' => "Tytuł",
		'language' => "Język",
		'language_title' => "Edytuj język",
		'link_lang' => "Link", // TRANSLATE
		'href_lang' => "Zlinkowane strony",
		'paragraph' => "Paragraf",
		'summary' => "Zestawienie",
		'isheader' => "Jest podpis",
		'keyboard' => "Klawiatura",
		'relation' => "Powiązania",
		'fontsize' => "Font size", // TRANSLATE
				));