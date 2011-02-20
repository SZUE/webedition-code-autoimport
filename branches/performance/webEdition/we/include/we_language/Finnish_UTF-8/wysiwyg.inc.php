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

$l_wysiwyg["window_title"] = "Muokkaa kenttää '%s'";

$l_wysiwyg["format"] = "Muotoilu";
$l_wysiwyg["fontsize"] = "Fontin koko";
$l_wysiwyg["fontname"] = "Fontin nimi";
$l_wysiwyg["css_style"] = "CSS -tyyli";

$l_wysiwyg["normal"] = "Normaali";
$l_wysiwyg["h1"] = "Otsikko 1";
$l_wysiwyg["h2"] = "Otsikko 2";
$l_wysiwyg["h3"] = "Otsikko 3";
$l_wysiwyg["h4"] = "Otsikko 4";
$l_wysiwyg["h5"] = "Otsikko 5";
$l_wysiwyg["h6"] = "Otsikko 6";
$l_wysiwyg["pre"] = "Muotoiltu";
$l_wysiwyg["address"] = "Osoite";

$GLOBALS['l_wysiwyg']['spellcheck'] = 'Oikoluku';

/*****************************************************************************
 * CONTEXT MENUS
 *****************************************************************************/

// REMEMBER: context menus cannot display any umlauts!
$l_wysiwyg["cut"] = "Leikkaa";
$l_wysiwyg["copy"] = "Kopioi";
$l_wysiwyg["paste"] = "Liitä";
$l_wysiwyg["insert_row"] = "Lisää rivi";
$l_wysiwyg["delete_rows"] = "Poista rivi";
$l_wysiwyg["insert_colmn"] = "Lisää sarake";
$l_wysiwyg["delete_colmns"] = "Poista sarake";
$l_wysiwyg["insert_cell"] = "Lisää solu";
$l_wysiwyg["delete_cells"] = "Poista solu";
$l_wysiwyg["merge_cells"] = "Yhdistä solut";
$l_wysiwyg["split_cell"] = "Jaa solut";

/*****************************************************************************
 * ALT-TEXTS FOR BUTTONS
 *****************************************************************************/

$l_wysiwyg["subscript"] = "Alaindeksoitu";
$l_wysiwyg["superscript"] = "Yläindeksoitu";
$l_wysiwyg["justify_full"] = "Tasaa molemmista reunoista";
$l_wysiwyg["strikethrought"] = "Yliviivaus";
$l_wysiwyg["removeformat"] = "Poista muotoilut";
$l_wysiwyg["removetags"] = "Remove tags, styles and comments"; //TRANSLATE
$l_wysiwyg["editcell"] = "Muokkaa taulukon solua";
$l_wysiwyg["edittable"] = "Muokkaa taulukkoa";
$l_wysiwyg["insert_row2"] = "Lisää rivi";
$l_wysiwyg["delete_rows2"] = "Poista rivejä";
$l_wysiwyg["insert_colmn2"] = "Lisää sarake";
$l_wysiwyg["delete_colmns2"] = "Poista sarakkeita";
$l_wysiwyg["insert_cell2"] = "Lisää solu";
$l_wysiwyg["delete_cells2"] = "Poista soluja";
$l_wysiwyg["merge_cells2"] = "Yhdistä soluja";
$l_wysiwyg["split_cell2"] = "Jaa solu";
$l_wysiwyg["insert_edit_table"] = "Lisää/muokkaa taulukkoa";
$l_wysiwyg["insert_edit_image"] = "Lisää/muokkaa kuva(a)";
$l_wysiwyg["edit_style_class"] = "Muokkaa luokkaa (tyyli)";
$l_wysiwyg["insert_br"] = "Lisää rivinvaihto (SHIFT + RETURN)";
$l_wysiwyg["insert_p"] = "Lisää kappale";
$l_wysiwyg["edit_sourcecode"] = "Muokkaa lähdekoodia";
$l_wysiwyg["show_details"] = "Näytä yksityiskohdat";
$l_wysiwyg["rtf_import"] = "Tuo RTF -tiedosto";
$l_wysiwyg["unlink"] = "Poista hyperlinkki";
$l_wysiwyg["hyperlink"] = "Lisää/muokkaa hyperlinkki(ä)";
$l_wysiwyg["back_color"] = "Taustan väri";
$l_wysiwyg["fore_color"] = "Fontin väri";
$l_wysiwyg["outdent"] = "Poista sisennys";
$l_wysiwyg["indent"] = "Sisennys";
$l_wysiwyg["unordered_list"] = "Numeroimaton lista";
$l_wysiwyg["ordered_list"] = "Numeroitu lista";
$l_wysiwyg["justify_right"] = "Jäsennä oikealle";
$l_wysiwyg["justify_center"] = "Jäsennä keskelle";
$l_wysiwyg["justify_left"] = "Jäsennä vasemmalle";
$l_wysiwyg["underline"] = "Alleviivaa";
$l_wysiwyg["italic"] = "Kursiivi";
$l_wysiwyg["bold"] = "Lihavoi";
$l_wysiwyg["fullscreen"] = "Avaa editori kokoruudussa";
$l_wysiwyg["edit_source"] = "Muokkaa lähdekoodia";
$l_wysiwyg["fullscreen_editor"] = "Kokoruutu-editori";
$l_wysiwyg["table_props"] = "Taulukon ominaisuudet";
$l_wysiwyg["insert_table"] = "Lisää taulukko";
$l_wysiwyg["edit_stylesheet"] = "Muokkaa tyylitiedostoa";

/*****************************************************************************
 * THE REST
 *****************************************************************************/

$l_wysiwyg["url"] = "URL";
$l_wysiwyg["image_url"] = "Kuvan URL";
$l_wysiwyg["width"] = "Leveys";
$l_wysiwyg["height"] = "Korkeus";
$l_wysiwyg["hspace"] = "Väli vaakasuunnassa";
$l_wysiwyg["vspace"] = "Väli pystysuunnassa";
$l_wysiwyg["border"] = "Reunus";
$l_wysiwyg["altText"] = "Vaihtoehtoinen teksti";
$l_wysiwyg["alignment"] = "Paikka";

$l_wysiwyg["external_image"] = "webEditionin ulkoinen kuva";
$l_wysiwyg["internal_image"] = "webEditionin sisäinen kuva";

$l_wysiwyg["bgcolor"] = "Taustan väri";
$l_wysiwyg["cellspacing"] = "Solujen väli";
$l_wysiwyg["cellpadding"] = "Solujen marginaali";
$l_wysiwyg["rows"] = "Rivejä";
$l_wysiwyg["cols"] = "Sarakkeita";
$l_wysiwyg["edit_table"] = "Muokkaa taulukkoa";
$l_wysiwyg["colspan"] = "Sarakkeen väli";
$l_wysiwyg["halignment"] = "Vaakapaikka"; // has to be short !!
$l_wysiwyg["valignment"] = "Pystypaikka";  // has to be short !!
$l_wysiwyg["color"] = "Color";
$l_wysiwyg["choosecolor"] = "Valitse väri";
$l_wysiwyg["parent_class"] = "Vanhemman alue";
$l_wysiwyg["region_class"] = "Vain valinta";
$l_wysiwyg["edit_classname"] = "Muokkaa tyylitiedoston luokkanimeä";
$l_wysiwyg["emaillink"] = "Sähköposti";
$l_wysiwyg["clean_word"] = "Puhdas MS Word koodi";
$l_wysiwyg["addcaption"] = "Lisää otsikko";
$l_wysiwyg["removecaption"] = "Poista otsikko";
$l_wysiwyg["anchor"] = "Ankkuri";

$l_wysiwyg["edit_hr"] = "Vaakaviiva";
$l_wysiwyg["color"] = "väri";
$l_wysiwyg["noshade"] = "Ei varjoa";
$l_wysiwyg["strikethrough"] = "Yliviivattu";

$l_wysiwyg["nothumb"] = "Ei esikatselukuvaa";
$l_wysiwyg["thumbnail"] = "Esikatselukuva";

$l_wysiwyg["acronym"] = "Akronyymi";
$l_wysiwyg["acronym_title"] = "Muokkaa akronyymia";
$l_wysiwyg["abbr"] = "Lyhenne";
$l_wysiwyg["abbr_title"] = "Muokkaa lyhennettä";
$l_wysiwyg["title"] = "Otsikko";
$l_wysiwyg["language"] = "Kieli";
$l_wysiwyg["language_title"] = "Muokkaa kielta";
$l_wysiwyg["link_lang"] = "Linkki";
$l_wysiwyg["href_lang"] = "Linkitetty sivu";
$l_wysiwyg["paragraph"] = "Kappale";

$l_wysiwyg["summary"] = "Yhteenveto";
$l_wysiwyg["isheader"] = "On otsikko";

$l_wysiwyg["keyboard"] = "Näppäimistö";

$l_wysiwyg["relation"] = "Relaatio";

$l_wysiwyg["fontsize"] = "Fonttikoko";
