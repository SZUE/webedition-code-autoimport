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

$l_wysiwyg["window_title"] = "Editer Champ '%s'";

$l_wysiwyg["format"] = "Style de police";
$l_wysiwyg["fontsize"] = "Taille de police";
$l_wysiwyg["fontname"] = "Nom de police";
$l_wysiwyg["css_style"] = "Style-CSS";

$l_wysiwyg["normal"] = "Normal"; // TRANSLATE
$l_wysiwyg["h1"] = "L'en-tête 1";
$l_wysiwyg["h2"] = "L'en-tête 2";
$l_wysiwyg["h3"] = "L'en-tête 3";
$l_wysiwyg["h4"] = "L'en-tête 4";
$l_wysiwyg["h5"] = "L'en-tête 5";
$l_wysiwyg["h6"] = "L'en-tête 6";
$l_wysiwyg["pre"] = "Formatert";
$l_wysiwyg["address"] = "Adresse";

$GLOBALS['l_wysiwyg']['spellcheck'] = 'Spellchecking'; // TRANSLATE

/*****************************************************************************
 * CONTEXT MENUS
 *****************************************************************************/

// REMEMBER: context menus cannot display any umlauts!
$l_wysiwyg["cut"] = "Cut"; // TRANSLATE
$l_wysiwyg["copy"] = "Copier";
$l_wysiwyg["paste"] = "Insérer";
$l_wysiwyg["insert_row"] = "Insérer des lignes";
$l_wysiwyg["delete_rows"] = "Supprimer des lignes";
$l_wysiwyg["insert_colmn"] = "Insérer des colonnes";
$l_wysiwyg["delete_colmns"] = "Supprimer des colonnes";
$l_wysiwyg["insert_cell"] = "Insérer des cellules";
$l_wysiwyg["delete_cells"] = "Supprimer des cellules";
$l_wysiwyg["merge_cells"] = "Fusionner des cellules";
$l_wysiwyg["split_cell"] = "Séparer des cellules";

/*****************************************************************************
 * ALT-TEXTS FOR BUTTONS
 *****************************************************************************/

$l_wysiwyg["subscript"] = "Indice";
$l_wysiwyg["superscript"] = "Exposant";
$l_wysiwyg["justify_full"] = "Justifié";
$l_wysiwyg["strikethrought"] = "Barré";
$l_wysiwyg["removeformat"] = "Supprimer le formatage";
$l_wysiwyg["removetags"] = "Remove tags, styles and comments"; // TRANSLATE
$l_wysiwyg["editcell"] = "Éditer une cellule de tableau";
$l_wysiwyg["edittable"] = "Éditer un tableau";
$l_wysiwyg["insert_row2"] = "Insérer des lignes";
$l_wysiwyg["delete_rows2"] = "Supprimer des lignes";
$l_wysiwyg["insert_colmn2"] = "Insérer des colonnes";
$l_wysiwyg["delete_colmns2"] = "Supprimer des colonnes";
$l_wysiwyg["insert_cell2"] = "Insérer des cellules";
$l_wysiwyg["delete_cells2"] = "Supprimer des cellules";
$l_wysiwyg["merge_cells2"] = "Fusionner des cellules";
$l_wysiwyg["split_cell2"] = "Séparer des cellules";
$l_wysiwyg["insert_edit_table"] = "Insérer/éditer un tableau";
$l_wysiwyg["insert_edit_image"] = "Insérer/éditer une graphique";
$l_wysiwyg["edit_style_class"] = "Éditer une classe (Style)";
$l_wysiwyg["insert_br"] = "Insérer un passage à ligne (SHIFT + RETURN)";
$l_wysiwyg["insert_p"] = "Insérer un paragraphe";
$l_wysiwyg["edit_sourcecode"] = "Éditer le code source";
$l_wysiwyg["show_details"] = "Afficher les détailes";
$l_wysiwyg["rtf_import"] = "Import-RTF";
$l_wysiwyg["unlink"] = "Enlever le lien";
$l_wysiwyg["hyperlink"] = "Éditer/";
$l_wysiwyg["back_color"] = "Couleur de l'arrière-plan";
$l_wysiwyg["fore_color"] = "Couleur du l'avant-plan";
$l_wysiwyg["outdent"] = "Réduire le retrait";
$l_wysiwyg["indent"] = "Augmenter le retrait";
$l_wysiwyg["unordered_list"] = "Liste énumérative";
$l_wysiwyg["ordered_list"] = "Liste numérotée";
$l_wysiwyg["justify_right"] = "Aligner à droite";
$l_wysiwyg["justify_center"] = "Justifié";
$l_wysiwyg["justify_left"] = "Aligner à gauche";
$l_wysiwyg["underline"] = "Soulignage";
$l_wysiwyg["italic"] = "italic";
$l_wysiwyg["bold"] = "Gras";
$l_wysiwyg["fullscreen"] = "Démarrer l'editeur en plein écran";
$l_wysiwyg["edit_source"] = "Éditer le code source";
$l_wysiwyg["fullscreen_editor"] = "Editeur en plein écran";
$l_wysiwyg["table_props"] = "Éditer le Tableau";
$l_wysiwyg["insert_table"] = "Insérer un tableaux";
$l_wysiwyg["edit_stylesheet"] = "Éditer les classes de feuille de style";

/*****************************************************************************
 * THE REST
 *****************************************************************************/

$l_wysiwyg["url"] = "URL"; // TRANSLATE
$l_wysiwyg["image_url"] = "URL de l'image";
$l_wysiwyg["width"] = "Largeur";
$l_wysiwyg["height"] = "Hateur";
$l_wysiwyg["hspace"] = "Distance horizontale";
$l_wysiwyg["vspace"] = "Distance verticale";
$l_wysiwyg["border"] = "Bordure";
$l_wysiwyg["altText"] = "Texte alternative";
$l_wysiwyg["alignment"] = "Alignement";

$l_wysiwyg["external_image"] = "Graphique-webEdition-externe";
$l_wysiwyg["internal_image"] = "Grafique-webEdition-interne";

$l_wysiwyg["bgcolor"] = "Coleur de l'arrière plan";
$l_wysiwyg["cellspacing"] = "Espace entre cellules";
$l_wysiwyg["cellpadding"] = "Espace intérieur de la cellule";
$l_wysiwyg["rows"] = "Lignes";
$l_wysiwyg["cols"] = "Colonne";
$l_wysiwyg["edit_table"] = "Éditer le tableaux";
$l_wysiwyg["colspan"] = "Envergure";
$l_wysiwyg["halignment"] = "Alignement horiz."; // has to be short !!
$l_wysiwyg["valignment"] = "Alignement vert.";  // has to be short !!
$l_wysiwyg["color"] = "Color";
$l_wysiwyg["choosecolor"] = "Choisir une couleur";
$l_wysiwyg["parent_class"] = "Domaine-parentale";
$l_wysiwyg["region_class"] = "Seulement la selection";
$l_wysiwyg["edit_classname"] = "Éditer une classe de style de CSS";
$l_wysiwyg["emaillink"] = "E-Mail"; // TRANSLATE
$l_wysiwyg["clean_word"] = "Nettoyer le Code de MS Word";
$l_wysiwyg["addcaption"] = "Ajouter un étiquetage";
$l_wysiwyg["removecaption"] = "Enlever l'étiquetage";
$l_wysiwyg["anchor"] = "Ancre";

$l_wysiwyg["edit_hr"] = "ligne horizontale";
$l_wysiwyg["color"] = "Couleur";
$l_wysiwyg["noshade"] = "sans ombre";
$l_wysiwyg["strikethrough"] = "Barré";

$l_wysiwyg["nothumb"] = "Aucune imagette";
$l_wysiwyg["thumbnail"] = "Imagettes";

$l_wysiwyg["acronym"] = "Abréviation";
$l_wysiwyg["acronym_title"] = "Éditer l'abréviation";
$l_wysiwyg["abbr"] = "Abbreviation"; // TRANSLATE
$l_wysiwyg["abbr_title"] = "Edit Abbreviation"; // TRANSLATE
$l_wysiwyg["title"] = "Titre";
$l_wysiwyg["language"] = "Langue";
$l_wysiwyg["language_title"] = "Éditer la langue";
$l_wysiwyg["link_lang"] = "Lien";
$l_wysiwyg["href_lang"] = "Site liées ";
$l_wysiwyg["paragraph"] = "Paragraphe";

$l_wysiwyg["summary"] = "Résumé";
$l_wysiwyg["isheader"] = "Est l'en-tête";

$l_wysiwyg["keyboard"] = "Clavier";

$l_wysiwyg["relation"] = "Rélation";

$l_wysiwyg["fontsize"] = "Font size"; // TRANSLATE
