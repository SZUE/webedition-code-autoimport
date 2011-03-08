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
		'window_title' => "Edit field '%s'",
		'format' => "Format",
		'fontsize' => "Font size",
		'fontname' => "Font name",
		'css_style' => "CSS style",
		'normal' => "Normal (without)",
		'h1' => "Heading 1",
		'h2' => "Heading 2",
		'h3' => "Heading 3",
		'h4' => "Heading 4",
		'h5' => "Heading 5",
		'h6' => "Heading 6",
		'pre' => "Formatted",
		'address' => "Address",
		'spellcheck' => 'Spellchecking',
		/*		 * ***************************************************************************
		 * CONTEXT MENUS
		 * *************************************************************************** */

// REMEMBER: context menus cannot display any umlauts!
		'cut' => "Cut",
		'copy' => "Copy",
		'paste' => "Paste",
		'insert_row' => "Insert row",
		'delete_rows' => "Delete rows",
		'insert_colmn' => "Insert column",
		'delete_colmns' => "Delete columns",
		'insert_cell' => "Insert cell",
		'delete_cells' => "Delete cells",
		'merge_cells' => "Merge cells",
		'split_cell' => "Split cells",
		/*		 * ***************************************************************************
		 * ALT-TEXTS FOR BUTTONS
		 * *************************************************************************** */

		'subscript' => "Subscript",
		'superscript' => "Superscript",
		'justify_full' => "Justify full",
		'strikethrought' => "Strike through",
		'removeformat' => "Remove format",
		'removetags' => "Remove tags, styles and comments",
		'editcell' => "Edit table cell",
		'edittable' => "Edit table",
		'insert_row2' => "Insert rows",
		'delete_rows2' => "Delete rows",
		'insert_colmn2' => "Insert column",
		'delete_colmns2' => "Delete columns",
		'insert_cell2' => "Insert cell",
		'delete_cells2' => "Delete cells",
		'merge_cells2' => "Merge cells",
		'split_cell2' => "Split cell",
		'insert_edit_table' => "Insert/edit table",
		'insert_edit_image' => "Insert/edit image",
		'edit_style_class' => "Edit class (style)",
		'insert_br' => "Insert line break (SHIFT + RETURN)",
		'insert_p' => "Insert paragraph",
		'edit_sourcecode' => "Edit source",
		'show_details' => "Show details",
		'rtf_import' => "Import RTF",
		'unlink' => "Remove hyperlink",
		'hyperlink' => "Insert/edit hyperlink",
		'back_color' => "Background color",
		'fore_color' => "Foreground color",
		'outdent' => "Outdent",
		'indent' => "Indent",
		'unordered_list' => "Unordered list",
		'ordered_list' => "Ordered list",
		'justify_right' => "Justify right",
		'justify_center' => "Justify center",
		'justify_left' => "Justify left",
		'underline' => "Underline",
		'italic' => "Italic",
		'bold' => "Bold",
		'fullscreen' => "Open editor in full-screen mode",
		'edit_source' => "Edit source code",
		'fullscreen_editor' => "Full-screen editor",
		'table_props' => "Table properties",
		'insert_table' => "Insert table",
		'edit_stylesheet' => "Edit style sheet",
		/*		 * ***************************************************************************
		 * THE REST
		 * *************************************************************************** */

		'url' => "URL",
		'image_url' => "Image URL",
		'width' => "Width",
		'height' => "Height",
		'hspace' => "Horizontal space",
		'vspace' => "Vertical space",
		'border' => "Border",
		'altText' => "Alternative text",
		'alignment' => "Alignment",
		'external_image' => "webEdition external image",
		'internal_image' => "webEdition internal image",
		'bgcolor' => "Background color",
		'cellspacing' => "Cell spacing",
		'cellpadding' => "Cell padding",
		'rows' => "Rows",
		'cols' => "Columns",
		'edit_table' => "Edit table",
		'colspan' => "Colspan",
		'halignment' => "Horiz. alignment", // has to be short !!
		'valignment' => "Vert. alignment", // has to be short !!
		'color' => "Color",
		'choosecolor' => "Choose color",
		'parent_class' => "Parent area",
		'region_class' => "Selection only",
		'edit_classname' => "Edit style sheet class name",
		'emaillink' => "E-Mail",
		'clean_word' => "Clean MS Word code",
		'addcaption' => "Add caption",
		'removecaption' => "Remove caption",
		'anchor' => "Anchor",
		'edit_hr' => "Horizontal rule",
		'color' => "color",
		'noshade' => "No shading",
		'strikethrough' => "Strike out",
		'nothumb' => "no thumbnail",
		'thumbnail' => "Thumbnail",
		'acronym' => "Acronym",
		'acronym_title' => "Edit Acronym",
		'abbr' => "Abbreviation",
		'abbr_title' => "Edit Abbreviation",
		'title' => "Title",
		'language' => "Language",
		'language_title' => "Edit Language",
		'link_lang' => "Link",
		'href_lang' => "Linked page",
		'paragraph' => "Paragraph",
		'summary' => "Summary",
		'isheader' => "Is heading",
		'keyboard' => "Keyboard",
		'relation' => "Relation",
		'fontsize' => "Font size",
				));