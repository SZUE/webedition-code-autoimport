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
		'window_title' => "Muokkaa kenttää '%s'",
		'format' => "Muotoilu",
		'fontsize' => "Fontin koko",
		'fontname' => "Fontin nimi",
		'css_style' => "CSS -tyyli",
		'normal' => "Normaali",
		'h1' => "Otsikko 1",
		'h2' => "Otsikko 2",
		'h3' => "Otsikko 3",
		'h4' => "Otsikko 4",
		'h5' => "Otsikko 5",
		'h6' => "Otsikko 6",
		'pre' => "Muotoiltu",
		'address' => "Osoite",
		'spellcheck' => 'Oikoluku',
		/*		 * ***************************************************************************
		 * CONTEXT MENUS
		 * *************************************************************************** */

// REMEMBER: context menus cannot display any umlauts!
		'cut' => "Leikkaa",
		'copy' => "Kopioi",
		'paste' => "Liitä",
		'insert_row' => "Lisää rivi",
		'delete_rows' => "Poista rivi",
		'insert_colmn' => "Lisää sarake",
		'delete_colmns' => "Poista sarake",
		'insert_cell' => "Lisää solu",
		'delete_cells' => "Poista solu",
		'merge_cells' => "Yhdistä solut",
		'split_cell' => "Jaa solut",
		/*		 * ***************************************************************************
		 * ALT-TEXTS FOR BUTTONS
		 * *************************************************************************** */

		'subscript' => "Alaindeksoitu",
		'superscript' => "Yläindeksoitu",
		'justify_full' => "Tasaa molemmista reunoista",
		'strikethrought' => "Yliviivaus",
		'removeformat' => "Poista muotoilut",
		'removetags' => "Remove tags, styles and comments", //TRANSLATE
		'editcell' => "Muokkaa taulukon solua",
		'edittable' => "Muokkaa taulukkoa",
		'insert_row2' => "Lisää rivi",
		'delete_rows2' => "Poista rivejä",
		'insert_colmn2' => "Lisää sarake",
		'delete_colmns2' => "Poista sarakkeita",
		'insert_cell2' => "Lisää solu",
		'delete_cells2' => "Poista soluja",
		'merge_cells2' => "Yhdistä soluja",
		'split_cell2' => "Jaa solu",
		'insert_edit_table' => "Lisää/muokkaa taulukkoa",
		'insert_edit_image' => "Lisää/muokkaa kuva(a)",
		'edit_style_class' => "Muokkaa luokkaa (tyyli)",
		'insert_br' => "Lisää rivinvaihto (SHIFT + RETURN)",
		'insert_p' => "Lisää kappale",
		'edit_sourcecode' => "Muokkaa lähdekoodia",
		'show_details' => "Näytä yksityiskohdat",
		'rtf_import' => "Tuo RTF -tiedosto",
		'unlink' => "Poista hyperlinkki",
		'hyperlink' => "Lisää/muokkaa hyperlinkki(ä)",
		'back_color' => "Taustan väri",
		'fore_color' => "Fontin väri",
		'outdent' => "Poista sisennys",
		'indent' => "Sisennys",
		'unordered_list' => "Numeroimaton lista",
		'ordered_list' => "Numeroitu lista",
		'justify_right' => "Jäsennä oikealle",
		'justify_center' => "Jäsennä keskelle",
		'justify_left' => "Jäsennä vasemmalle",
		'underline' => "Alleviivaa",
		'italic' => "Kursiivi",
		'bold' => "Lihavoi",
		'fullscreen' => "Avaa editori kokoruudussa",
		'edit_source' => "Muokkaa lähdekoodia",
		'fullscreen_editor' => "Kokoruutu-editori",
		'table_props' => "Taulukon ominaisuudet",
		'insert_table' => "Lisää taulukko",
		'edit_stylesheet' => "Muokkaa tyylitiedostoa",
		/*		 * ***************************************************************************
		 * THE REST
		 * *************************************************************************** */

		'url' => "URL",
		'image_url' => "Kuvan URL",
		'width' => "Leveys",
		'height' => "Korkeus",
		'hspace' => "Väli vaakasuunnassa",
		'vspace' => "Väli pystysuunnassa",
		'border' => "Reunus",
		'altText' => "Vaihtoehtoinen teksti",
		'alignment' => "Paikka",
		'external_image' => "webEditionin ulkoinen kuva",
		'internal_image' => "webEditionin sisäinen kuva",
		'bgcolor' => "Taustan väri",
		'cellspacing' => "Solujen väli",
		'cellpadding' => "Solujen marginaali",
		'rows' => "Rivejä",
		'cols' => "Sarakkeita",
		'edit_table' => "Muokkaa taulukkoa",
		'colspan' => "Sarakkeen väli",
		'halignment' => "Vaakapaikka", // has to be short !!
		'valignment' => "Pystypaikka", // has to be short !!
		'color' => "Color",
		'choosecolor' => "Valitse väri",
		'parent_class' => "Vanhemman alue",
		'region_class' => "Vain valinta",
		'edit_classname' => "Muokkaa tyylitiedoston luokkanimeä",
		'emaillink' => "Sähköposti",
		'clean_word' => "Puhdas MS Word koodi",
		'addcaption' => "Lisää otsikko",
		'removecaption' => "Poista otsikko",
		'anchor' => "Ankkuri",
		'edit_hr' => "Vaakaviiva",
		'color' => "väri",
		'noshade' => "Ei varjoa",
		'strikethrough' => "Yliviivattu",
		'nothumb' => "Ei esikatselukuvaa",
		'thumbnail' => "Esikatselukuva",
		'acronym' => "Akronyymi",
		'acronym_title' => "Muokkaa akronyymia",
		'abbr' => "Lyhenne",
		'abbr_title' => "Muokkaa lyhennettä",
		'title' => "Otsikko",
		'language' => "Kieli",
		'language_title' => "Muokkaa kielta",
		'link_lang' => "Linkki",
		'href_lang' => "Linkitetty sivu",
		'paragraph' => "Kappale",
		'summary' => "Yhteenveto",
		'isheader' => "On otsikko",
		'keyboard' => "Näppäimistö",
		'relation' => "Relaatio",
		'fontsize' => "Fonttikoko",
				));