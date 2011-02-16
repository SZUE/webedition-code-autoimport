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
 * Language file: wysiwyg.inc.php
 * Provides language strings.
 * Language: English
 */
include_once(dirname(__FILE__)."/wysiwyg_js.inc.php");

$l_wysiwyg["window_title"] = "Edytuj pole '%s'";

$l_wysiwyg["format"] = "Styl";
$l_wysiwyg["fontsize"] = "Wilekość czcionki";
$l_wysiwyg["fontname"] = "Nazwa czcionki";
$l_wysiwyg["css_style"] = "Styl CSS";

$l_wysiwyg["normal"] = "Normal"; // TRANSLATE
$l_wysiwyg["h1"] = "Nagłówek 1";
$l_wysiwyg["h2"] = "Nagłówek 2";
$l_wysiwyg["h3"] = "Nagłówek 3";
$l_wysiwyg["h4"] = "Nagłówek 4";
$l_wysiwyg["h5"] = "Nagłówek 5";
$l_wysiwyg["h6"] = "Nagłówek 6";
$l_wysiwyg["pre"] = "Sformatowano";
$l_wysiwyg["address"] = "Adres";

$GLOBALS['l_wysiwyg']['spellcheck'] = 'Spellchecking'; // TRANSLATE


/*****************************************************************************
 * CONTEXT MENUS
 *****************************************************************************/

// REMEMBER: context menus cannot display any umlauts!
$l_wysiwyg["cut"] = "Cut"; // TRANSLATE
$l_wysiwyg["copy"] = "Kopiuj";
$l_wysiwyg["paste"] = "Wklej";
$l_wysiwyg["insert_row"] = "Wstaw wiersz";
$l_wysiwyg["delete_rows"] = "Kasuj wiersze";
$l_wysiwyg["insert_colmn"] = "Wstaw kolumnę";
$l_wysiwyg["delete_colmns"] = "Kasuj kolumny";
$l_wysiwyg["insert_cell"] = "Wstaw komórkę";
$l_wysiwyg["delete_cells"] = "Kasuj komórki";
$l_wysiwyg["merge_cells"] = "Scal komórki";
$l_wysiwyg["split_cell"] = "Podziel komórki";

/*****************************************************************************
 * ALT-TEXTS FOR BUTTONS
 *****************************************************************************/

$l_wysiwyg["subscript"] = "Index dolny";
$l_wysiwyg["superscript"] = "Index górny";
$l_wysiwyg["justify_full"] = "Wyjustuj";
$l_wysiwyg["strikethrought"] = "Przekreślenie";
$l_wysiwyg["removeformat"] = "Kasuj formatowanie";
$l_wysiwyg["removetags"] = "Remove tags, styles and comments"; // TRANSLATE
$l_wysiwyg["editcell"] = "Edytuj komórkę tabeli";
$l_wysiwyg["edittable"] = "Edytuj tabelę";
$l_wysiwyg["insert_row2"] = "Wstaw wiersz";
$l_wysiwyg["delete_rows2"] = "Kasuj wiersze";
$l_wysiwyg["insert_colmn2"] = "Wstaw kolumnę";
$l_wysiwyg["delete_colmns2"] = "Kasuj kolumny";
$l_wysiwyg["insert_cell2"] = "Wstaw komórkę";
$l_wysiwyg["delete_cells2"] = "Kasuj komórki";
$l_wysiwyg["merge_cells2"] = "Scal komórki";
$l_wysiwyg["split_cell2"] = "Podziel komórki";
$l_wysiwyg["insert_edit_table"] = "Tabela wklej/edytuj";
$l_wysiwyg["insert_edit_image"] = "Grafika wklej/edytuj";
$l_wysiwyg["edit_style_class"] = "Edytuj klasę (Style)";
$l_wysiwyg["insert_br"] = "Wstaw przełamanie linii (SHIFT + RETURN)";
$l_wysiwyg["insert_p"] = "Wstaw paragraf";
$l_wysiwyg["edit_sourcecode"] = "Edytuj kod źródłowy";
$l_wysiwyg["show_details"] = "Pokaż szczegóły";
$l_wysiwyg["rtf_import"] = "Importuj RTF";
$l_wysiwyg["unlink"] = "Usuń Hyperlink";
$l_wysiwyg["hyperlink"] = "Hyperlink wklej/edytuj";
$l_wysiwyg["back_color"] = "Kolor tła";
$l_wysiwyg["fore_color"] = "Kolor panelu";
$l_wysiwyg["outdent"] = "Usuń wcięcie";
$l_wysiwyg["indent"] = "Wcięcie";
$l_wysiwyg["unordered_list"] = "Nieuporządkowana lista";
$l_wysiwyg["ordered_list"] = "Uporządkowana lista";
$l_wysiwyg["justify_right"] = "Wyrównaj do prawej";
$l_wysiwyg["justify_center"] = "Centruj";
$l_wysiwyg["justify_left"] = "Wyrównaj do lewej";
$l_wysiwyg["underline"] = "Podkreślenie";
$l_wysiwyg["italic"] = "Kursywa";
$l_wysiwyg["bold"] = "Wytłuść";
$l_wysiwyg["fullscreen"] = "Otwórz edytor w trybie Fullscreen";
$l_wysiwyg["edit_source"] = "Edytuj kod źródłowy";
$l_wysiwyg["fullscreen_editor"] = "Fullscreen edytor";
$l_wysiwyg["table_props"] = "Edytuj tabelę";
$l_wysiwyg["insert_table"] = "Wstaw tabelę";
$l_wysiwyg["edit_stylesheet"] = "Edytuj klasy Stylesheet";

/*****************************************************************************
 * THE REST
 *****************************************************************************/

$l_wysiwyg["url"] = "URL"; // TRANSLATE
$l_wysiwyg["image_url"] = "Obrazek URL";
$l_wysiwyg["width"] = "Szerokość";
$l_wysiwyg["height"] = "Wysokość";
$l_wysiwyg["hspace"] = "Odstęp poziomy";
$l_wysiwyg["vspace"] = "Odstęp pionowy";
$l_wysiwyg["border"] = "Krawędź";
$l_wysiwyg["altText"] = "Tekst alternatywny";
$l_wysiwyg["alignment"] = "Wyrównanie";

$l_wysiwyg["external_image"] = "webEdition-zew. grafika";
$l_wysiwyg["internal_image"] = "webEdition-wew. grafika";

$l_wysiwyg["bgcolor"] = "Kolor tła";
$l_wysiwyg["cellspacing"] = "Odległość komórek";
$l_wysiwyg["cellpadding"] = "Margines w komórkach tabeli";
$l_wysiwyg["rows"] = "Wiersze";
$l_wysiwyg["cols"] = "Kolumny";
$l_wysiwyg["edit_table"] = "Edytuj tabelę";
$l_wysiwyg["colspan"] = "Rozpiętość";
$l_wysiwyg["halignment"] = "Wyrównanie horyz."; // has to be short !!
$l_wysiwyg["valignment"] = "Wyrównanie wert.";  // has to be short !!
$l_wysiwyg["color"] = "Color";
$l_wysiwyg["choosecolor"] = "Wybierz kolor";
$l_wysiwyg["parent_class"] = "Klasa bazowa";
$l_wysiwyg["region_class"] = "Klasa potomna";
$l_wysiwyg["edit_classname"] = "Edytuj klasę Stylesheet";
$l_wysiwyg["emaillink"] = "E-mail";
$l_wysiwyg["clean_word"] = "Czyść kod MS Word";
$l_wysiwyg["addcaption"] = "Dodaj podpis";
$l_wysiwyg["removecaption"] = "Usuń podpis";
$l_wysiwyg["anchor"] = "Odnośnik";

$l_wysiwyg["edit_hr"] = "Pozioma linia";
$l_wysiwyg["color"] = "Kolor";
$l_wysiwyg["noshade"] = "Bez cieniowania";
$l_wysiwyg["strikethrough"] = "Przekreśl";

$l_wysiwyg["nothumb"] = "Brak miniatury";
$l_wysiwyg["thumbnail"] = "Widok miniatury";

$l_wysiwyg["acronym"] = "Skrót";
$l_wysiwyg["acronym_title"] = "Edytuj skrót";
$l_wysiwyg["abbr"] = "Abbreviation"; // TRANSLATE
$l_wysiwyg["abbr_title"] = "Edit Abbreviation"; // TRANSLATE
$l_wysiwyg["title"] = "Tytuł";
$l_wysiwyg["language"] = "Język";
$l_wysiwyg["language_title"] = "Edytuj język";
$l_wysiwyg["link_lang"] = "Link"; // TRANSLATE
$l_wysiwyg["href_lang"] = "Zlinkowane strony";
$l_wysiwyg["paragraph"] = "Paragraf";

$l_wysiwyg["summary"] = "Zestawienie";
$l_wysiwyg["isheader"] = "Jest podpis";

$l_wysiwyg["keyboard"] = "Klawiatura";

$l_wysiwyg["relation"] = "Powiązania";

$l_wysiwyg["fontsize"] = "Font size"; // TRANSLATE
