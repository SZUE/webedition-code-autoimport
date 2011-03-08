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
		'window_title' => "Editer Champ '%s'",
		'format' => "Style de police",
		'fontsize' => "Taille de police",
		'fontname' => "Nom de police",
		'css_style' => "Style-CSS",
		'normal' => "Normal", // TRANSLATE
		'h1' => "L'en-tête 1",
		'h2' => "L'en-tête 2",
		'h3' => "L'en-tête 3",
		'h4' => "L'en-tête 4",
		'h5' => "L'en-tête 5",
		'h6' => "L'en-tête 6",
		'pre' => "Formatert",
		'address' => "Adresse",
		'spellcheck' => 'Spellchecking', // TRANSLATE

		/*		 * ***************************************************************************
		 * CONTEXT MENUS
		 * *************************************************************************** */

// REMEMBER: context menus cannot display any umlauts!
		'cut' => "Cut", // TRANSLATE
		'copy' => "Copier",
		'paste' => "Insérer",
		'insert_row' => "Insérer des lignes",
		'delete_rows' => "Supprimer des lignes",
		'insert_colmn' => "Insérer des colonnes",
		'delete_colmns' => "Supprimer des colonnes",
		'insert_cell' => "Insérer des cellules",
		'delete_cells' => "Supprimer des cellules",
		'merge_cells' => "Fusionner des cellules",
		'split_cell' => "Séparer des cellules",
		/*		 * ***************************************************************************
		 * ALT-TEXTS FOR BUTTONS
		 * *************************************************************************** */

		'subscript' => "Indice",
		'superscript' => "Exposant",
		'justify_full' => "Justifié",
		'strikethrought' => "Barré",
		'removeformat' => "Supprimer le formatage",
		'removetags' => "Remove tags, styles and comments", // TRANSLATE
		'editcell' => "Éditer une cellule de tableau",
		'edittable' => "Éditer un tableau",
		'insert_row2' => "Insérer des lignes",
		'delete_rows2' => "Supprimer des lignes",
		'insert_colmn2' => "Insérer des colonnes",
		'delete_colmns2' => "Supprimer des colonnes",
		'insert_cell2' => "Insérer des cellules",
		'delete_cells2' => "Supprimer des cellules",
		'merge_cells2' => "Fusionner des cellules",
		'split_cell2' => "Séparer des cellules",
		'insert_edit_table' => "Insérer/éditer un tableau",
		'insert_edit_image' => "Insérer/éditer une graphique",
		'edit_style_class' => "Éditer une classe (Style)",
		'insert_br' => "Insérer un passage à ligne (SHIFT + RETURN)",
		'insert_p' => "Insérer un paragraphe",
		'edit_sourcecode' => "Éditer le code source",
		'show_details' => "Afficher les détailes",
		'rtf_import' => "Import-RTF",
		'unlink' => "Enlever le lien",
		'hyperlink' => "Éditer/",
		'back_color' => "Couleur de l'arrière-plan",
		'fore_color' => "Couleur du l'avant-plan",
		'outdent' => "Réduire le retrait",
		'indent' => "Augmenter le retrait",
		'unordered_list' => "Liste énumérative",
		'ordered_list' => "Liste numérotée",
		'justify_right' => "Aligner à droite",
		'justify_center' => "Justifié",
		'justify_left' => "Aligner à gauche",
		'underline' => "Soulignage",
		'italic' => "italic",
		'bold' => "Gras",
		'fullscreen' => "Démarrer l'editeur en plein écran",
		'edit_source' => "Éditer le code source",
		'fullscreen_editor' => "Editeur en plein écran",
		'table_props' => "Éditer le Tableau",
		'insert_table' => "Insérer un tableaux",
		'edit_stylesheet' => "Éditer les classes de feuille de style",
		/*		 * ***************************************************************************
		 * THE REST
		 * *************************************************************************** */

		'url' => "URL", // TRANSLATE
		'image_url' => "URL de l'image",
		'width' => "Largeur",
		'height' => "Hateur",
		'hspace' => "Distance horizontale",
		'vspace' => "Distance verticale",
		'border' => "Bordure",
		'altText' => "Texte alternative",
		'alignment' => "Alignement",
		'external_image' => "Graphique-webEdition-externe",
		'internal_image' => "Grafique-webEdition-interne",
		'bgcolor' => "Coleur de l'arrière plan",
		'cellspacing' => "Espace entre cellules",
		'cellpadding' => "Espace intérieur de la cellule",
		'rows' => "Lignes",
		'cols' => "Colonne",
		'edit_table' => "Éditer le tableaux",
		'colspan' => "Envergure",
		'halignment' => "Alignement horiz.", // has to be short !!
		'valignment' => "Alignement vert.", // has to be short !!
		'color' => "Color",
		'choosecolor' => "Choisir une couleur",
		'parent_class' => "Domaine-parentale",
		'region_class' => "Seulement la selection",
		'edit_classname' => "Éditer une classe de style de CSS",
		'emaillink' => "E-Mail", // TRANSLATE
		'clean_word' => "Nettoyer le Code de MS Word",
		'addcaption' => "Ajouter un étiquetage",
		'removecaption' => "Enlever l'étiquetage",
		'anchor' => "Ancre",
		'edit_hr' => "ligne horizontale",
		'color' => "Couleur",
		'noshade' => "sans ombre",
		'strikethrough' => "Barré",
		'nothumb' => "Aucune imagette",
		'thumbnail' => "Imagettes",
		'acronym' => "Abréviation",
		'acronym_title' => "Éditer l'abréviation",
		'abbr' => "Abbreviation", // TRANSLATE
		'abbr_title' => "Edit Abbreviation", // TRANSLATE
		'title' => "Titre",
		'language' => "Langue",
		'language_title' => "Éditer la langue",
		'link_lang' => "Lien",
		'href_lang' => "Site liées ",
		'paragraph' => "Paragraphe",
		'summary' => "Résumé",
		'isheader' => "Est l'en-tête",
		'keyboard' => "Clavier",
		'relation' => "Rélation",
		'fontsize' => "Font size", // TRANSLATE
				));