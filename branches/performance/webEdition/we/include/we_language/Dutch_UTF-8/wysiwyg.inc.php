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
		'window_title' => "Wijzig veld '%s'",
		'format' => "Opmaak",
		'fontsize' => "Font grootte",
		'fontname' => "Font naam",
		'css_style' => "CSS stijl",
		'normal' => "Normaal",
		'h1' => "Kop 1",
		'h2' => "Kop 2",
		'h3' => "Kop 3",
		'h4' => "Kop 4",
		'h5' => "Kop 5",
		'h6' => "Kop 6",
		'pre' => "Opgemaakt",
		'address' => "Adresseer",
		'spellcheck' => 'Spellingscontrole',
		/*		 * ***************************************************************************
		 * CONTEXT MENUS
		 * *************************************************************************** */

// REMEMBER: context menus cannot display any umlauts!
		'cut' => "Knip",
		'copy' => "Kopieer",
		'paste' => "Plak",
		'insert_row' => "Voeg rij in",
		'delete_rows' => "Verwijder rij",
		'insert_colmn' => "Voeg kolom in",
		'delete_colmns' => "Verwijder kolommen",
		'insert_cell' => "Voeg cel in",
		'delete_cells' => "Verwijder cellen",
		'merge_cells' => "Verenig cellen",
		'split_cell' => "Splits cellen",
		/*		 * ***************************************************************************
		 * ALT-TEXTS FOR BUTTONS
		 * *************************************************************************** */

		'subscript' => "Subschrift",
		'superscript' => "Superschrift",
		'justify_full' => "Sta alles toe",
		'strikethrought' => "Doorhalen",
		'removeformat' => "Verwijder opmaak",
		'removetags' => "Remove tags, styles and comments", //TRANSLATE
		'editcell' => "Wijzig tabelcel",
		'edittable' => "Wijzig tabel",
		'insert_row2' => "Voeg rijen toe",
		'delete_rows2' => "Verwijder rijen",
		'insert_colmn2' => "Voeg kolom toe",
		'delete_colmns2' => "Verwijder kolommen",
		'insert_cell2' => "Voeg cel toe",
		'delete_cells2' => "Verwijder cellen",
		'merge_cells2' => "Verenig cellen",
		'split_cell2' => "Splits cellen",
		'insert_edit_table' => "Voeg toe/wijzig tabel",
		'insert_edit_image' => "Voeg toe/wijzig afbeelding",
		'edit_style_class' => "Wijzig class (stijl)",
		'insert_br' => "Voeg witregel toe (SHIFT + RETURN)",
		'insert_p' => "Voeg paragraaf toe",
		'edit_sourcecode' => "Wijzig code",
		'show_details' => "Toon details",
		'rtf_import' => "Importeer RTF",
		'unlink' => "Verwijder hyperlink",
		'hyperlink' => "Voeg toe/wijzig hyperlink",
		'back_color' => "Achtergrondkleur",
		'fore_color' => "Voorgrondkleur",
		'outdent' => "Spring in",
		'indent' => "Spring uit",
		'unordered_list' => "Ongeordende lijst",
		'ordered_list' => "Geordende lijst",
		'justify_right' => "Lijn rechts uit",
		'justify_center' => "Centreer",
		'justify_left' => "Lijn links uit",
		'underline' => "Onderstreep",
		'italic' => "Cursief",
		'bold' => "Vet",
		'fullscreen' => "Open editor in volledig scherm",
		'edit_source' => "Wijzig broncode",
		'fullscreen_editor' => "Volledige scherm editor",
		'table_props' => "Tabel eigenschappen",
		'insert_table' => "Voeg tabel toe",
		'edit_stylesheet' => "Wijzig stylesheet",
		/*		 * ***************************************************************************
		 * THE REST
		 * *************************************************************************** */

		'url' => "URL", // TRANSLATE
		'image_url' => "Afbeeldings URL",
		'width' => "Breedte",
		'height' => "Hoogte",
		'hspace' => "Horizontale ruimte",
		'vspace' => "Verticale ruimte",
		'border' => "Rand",
		'altText' => "Alternatieve tekst",
		'alignment' => "Uitlijning",
		'external_image' => "webEdition externe afbeelding",
		'internal_image' => "webEdition interne afbeelding",
		'bgcolor' => "Achtergrondkleur",
		'cellspacing' => "Cel spacing",
		'cellpadding' => "Cel padding",
		'rows' => "Rijen",
		'cols' => "Kolommen",
		'edit_table' => "Wijzig tabel",
		'colspan' => "Colspan", // TRANSLATE
		'halignment' => "Horiz. uitlijnen", // has to be short !!
		'valignment' => "Vert. uitlijnen", // has to be short !!
		'color' => "Color",
		'choosecolor' => "Kies kleur",
		'parent_class' => "Hoofdgebied",
		'region_class' => "Alleen selectie",
		'edit_classname' => "Wijzig stylesheet class naam",
		'emaillink' => "E-Mail", // TRANSLATE
		'clean_word' => "Leeg MS Word code",
		'addcaption' => "Voeg onderschrift toe",
		'removecaption' => "Verwijder onderschrift",
		'anchor' => "Anker",
		'edit_hr' => "Horizontale lijn",
		'color' => "kleur",
		'noshade' => "Geen schaduw",
		'strikethrough' => "Haal door",
		'nothumb' => "geen thumbnail",
		'thumbnail' => "Thumbnail", // TRANSLATE

		'acronym' => "Acroniem",
		'acronym_title' => "Wijzig Acroniem",
		'abbr' => "Afkorting",
		'abbr_title' => "Wijzig afkorting",
		'title' => "Titel",
		'language' => "Taal",
		'language_title' => "Wijzig Taal",
		'link_lang' => "Koppeling",
		'href_lang' => "Gekoppelde pagina",
		'paragraph' => "Paragraaf",
		'summary' => "Opsomming",
		'isheader' => "Is Koptekst",
		'keyboard' => "Toetsenbord",
		'relation' => "Relatie",
		'fontsize' => "Font grootte",
				));