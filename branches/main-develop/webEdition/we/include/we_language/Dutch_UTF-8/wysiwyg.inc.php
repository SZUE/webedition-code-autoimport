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

$l_wysiwyg["window_title"] = "Wijzig veld '%s'";

$l_wysiwyg["format"] = "Opmaak";
$l_wysiwyg["fontsize"] = "Font grootte";
$l_wysiwyg["fontname"] = "Font naam";
$l_wysiwyg["css_style"] = "CSS stijl";

$l_wysiwyg["normal"] = "Normaal";
$l_wysiwyg["h1"] = "Kop 1";
$l_wysiwyg["h2"] = "Kop 2";
$l_wysiwyg["h3"] = "Kop 3";
$l_wysiwyg["h4"] = "Kop 4";
$l_wysiwyg["h5"] = "Kop 5";
$l_wysiwyg["h6"] = "Kop 6";
$l_wysiwyg["pre"] = "Opgemaakt";
$l_wysiwyg["address"] = "Adresseer";

$GLOBALS['l_wysiwyg']['spellcheck'] = 'Spellingscontrole';
/*****************************************************************************
 * CONTEXT MENUS
 *****************************************************************************/

// REMEMBER: context menus cannot display any umlauts!
$l_wysiwyg["cut"] = "Knip";
$l_wysiwyg["copy"] = "Kopieer";
$l_wysiwyg["paste"] = "Plak";
$l_wysiwyg["insert_row"] = "Voeg rij in";
$l_wysiwyg["delete_rows"] = "Verwijder rij";
$l_wysiwyg["insert_colmn"] = "Voeg kolom in";
$l_wysiwyg["delete_colmns"] = "Verwijder kolommen";
$l_wysiwyg["insert_cell"] = "Voeg cel in";
$l_wysiwyg["delete_cells"] = "Verwijder cellen";
$l_wysiwyg["merge_cells"] = "Verenig cellen";
$l_wysiwyg["split_cell"] = "Splits cellen";

/*****************************************************************************
 * ALT-TEXTS FOR BUTTONS
 *****************************************************************************/

$l_wysiwyg["subscript"] = "Subschrift";
$l_wysiwyg["superscript"] = "Superschrift";
$l_wysiwyg["justify_full"] = "Sta alles toe";
$l_wysiwyg["strikethrought"] = "Doorhalen";
$l_wysiwyg["removeformat"] = "Verwijder opmaak";
$l_wysiwyg["removetags"] = "Remove tags, styles and comments"; //TRANSLATE
$l_wysiwyg["editcell"] = "Wijzig tabelcel";
$l_wysiwyg["edittable"] = "Wijzig tabel";
$l_wysiwyg["insert_row2"] = "Voeg rijen toe";
$l_wysiwyg["delete_rows2"] = "Verwijder rijen";
$l_wysiwyg["insert_colmn2"] = "Voeg kolom toe";
$l_wysiwyg["delete_colmns2"] = "Verwijder kolommen";
$l_wysiwyg["insert_cell2"] = "Voeg cel toe";
$l_wysiwyg["delete_cells2"] = "Verwijder cellen";
$l_wysiwyg["merge_cells2"] = "Verenig cellen";
$l_wysiwyg["split_cell2"] = "Splits cellen";
$l_wysiwyg["insert_edit_table"] = "Voeg toe/wijzig tabel";
$l_wysiwyg["insert_edit_image"] = "Voeg toe/wijzig afbeelding";
$l_wysiwyg["edit_style_class"] = "Wijzig class (stijl)";
$l_wysiwyg["insert_br"] = "Voeg witregel toe (SHIFT + RETURN)";
$l_wysiwyg["insert_p"] = "Voeg paragraaf toe";
$l_wysiwyg["edit_sourcecode"] = "Wijzig code";
$l_wysiwyg["show_details"] = "Toon details";
$l_wysiwyg["rtf_import"] = "Importeer RTF";
$l_wysiwyg["unlink"] = "Verwijder hyperlink";
$l_wysiwyg["hyperlink"] = "Voeg toe/wijzig hyperlink";
$l_wysiwyg["back_color"] = "Achtergrondkleur";
$l_wysiwyg["fore_color"] = "Voorgrondkleur";
$l_wysiwyg["outdent"] = "Spring in";
$l_wysiwyg["indent"] = "Spring uit";
$l_wysiwyg["unordered_list"] = "Ongeordende lijst";
$l_wysiwyg["ordered_list"] = "Geordende lijst";
$l_wysiwyg["justify_right"] = "Lijn rechts uit";
$l_wysiwyg["justify_center"] = "Centreer";
$l_wysiwyg["justify_left"] = "Lijn links uit";
$l_wysiwyg["underline"] = "Onderstreep";
$l_wysiwyg["italic"] = "Cursief";
$l_wysiwyg["bold"] = "Vet";
$l_wysiwyg["fullscreen"] = "Open editor in volledig scherm";
$l_wysiwyg["edit_source"] = "Wijzig broncode";
$l_wysiwyg["fullscreen_editor"] = "Volledige scherm editor";
$l_wysiwyg["table_props"] = "Tabel eigenschappen";
$l_wysiwyg["insert_table"] = "Voeg tabel toe";
$l_wysiwyg["edit_stylesheet"] = "Wijzig stylesheet";

/*****************************************************************************
 * THE REST
 *****************************************************************************/

$l_wysiwyg["url"] = "URL"; // TRANSLATE
$l_wysiwyg["image_url"] = "Afbeeldings URL";
$l_wysiwyg["width"] = "Breedte";
$l_wysiwyg["height"] = "Hoogte";
$l_wysiwyg["hspace"] = "Horizontale ruimte";
$l_wysiwyg["vspace"] = "Verticale ruimte";
$l_wysiwyg["border"] = "Rand";
$l_wysiwyg["altText"] = "Alternatieve tekst";
$l_wysiwyg["alignment"] = "Uitlijning";

$l_wysiwyg["external_image"] = "webEdition externe afbeelding";
$l_wysiwyg["internal_image"] = "webEdition interne afbeelding";

$l_wysiwyg["bgcolor"] = "Achtergrondkleur";
$l_wysiwyg["cellspacing"] = "Cel spacing";
$l_wysiwyg["cellpadding"] = "Cel padding";
$l_wysiwyg["rows"] = "Rijen";
$l_wysiwyg["cols"] = "Kolommen";
$l_wysiwyg["edit_table"] = "Wijzig tabel";
$l_wysiwyg["colspan"] = "Colspan"; // TRANSLATE
$l_wysiwyg["halignment"] = "Horiz. uitlijnen"; // has to be short !!
$l_wysiwyg["valignment"] = "Vert. uitlijnen";  // has to be short !!
$l_wysiwyg["color"] = "Color";
$l_wysiwyg["choosecolor"] = "Kies kleur";
$l_wysiwyg["parent_class"] = "Hoofdgebied";
$l_wysiwyg["region_class"] = "Alleen selectie";
$l_wysiwyg["edit_classname"] = "Wijzig stylesheet class naam";
$l_wysiwyg["emaillink"] = "E-Mail"; // TRANSLATE
$l_wysiwyg["clean_word"] = "Leeg MS Word code";
$l_wysiwyg["addcaption"] = "Voeg onderschrift toe";
$l_wysiwyg["removecaption"] = "Verwijder onderschrift";
$l_wysiwyg["anchor"] = "Anker";

$l_wysiwyg["edit_hr"] = "Horizontale lijn";
$l_wysiwyg["color"] = "kleur";
$l_wysiwyg["noshade"] = "Geen schaduw";
$l_wysiwyg["strikethrough"] = "Haal door";

$l_wysiwyg["nothumb"] = "geen thumbnail";
$l_wysiwyg["thumbnail"] = "Thumbnail"; // TRANSLATE

$l_wysiwyg["acronym"] = "Acroniem";
$l_wysiwyg["acronym_title"] = "Wijzig Acroniem";
$l_wysiwyg["abbr"] = "Afkorting";
$l_wysiwyg["abbr_title"] = "Wijzig afkorting";
$l_wysiwyg["title"] = "Titel";
$l_wysiwyg["language"] = "Taal";
$l_wysiwyg["language_title"] = "Wijzig Taal";
$l_wysiwyg["link_lang"] = "Koppeling";
$l_wysiwyg["href_lang"] = "Gekoppelde pagina";
$l_wysiwyg["paragraph"] = "Paragraaf";

$l_wysiwyg["summary"] = "Opsomming";
$l_wysiwyg["isheader"] = "Is Koptekst";

$l_wysiwyg["keyboard"] = "Toetsenbord";

$l_wysiwyg["relation"] = "Relatie";

$l_wysiwyg["fontsize"] = "Font grootte";
