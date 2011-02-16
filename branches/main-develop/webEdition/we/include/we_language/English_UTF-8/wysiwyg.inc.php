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

$l_wysiwyg["window_title"] = "Edit field '%s'";

$l_wysiwyg["format"] = "Format";
$l_wysiwyg["fontsize"] = "Font size";
$l_wysiwyg["fontname"] = "Font name";
$l_wysiwyg["css_style"] = "CSS style";

$l_wysiwyg["normal"] = "Normal (without)";
$l_wysiwyg["h1"] = "Heading 1";
$l_wysiwyg["h2"] = "Heading 2";
$l_wysiwyg["h3"] = "Heading 3";
$l_wysiwyg["h4"] = "Heading 4";
$l_wysiwyg["h5"] = "Heading 5";
$l_wysiwyg["h6"] = "Heading 6";
$l_wysiwyg["pre"] = "Formatted";
$l_wysiwyg["address"] = "Address";

$GLOBALS['l_wysiwyg']['spellcheck'] = 'Spellchecking';

/*****************************************************************************
 * CONTEXT MENUS
 *****************************************************************************/

// REMEMBER: context menus cannot display any umlauts!
$l_wysiwyg["cut"] = "Cut";
$l_wysiwyg["copy"] = "Copy";
$l_wysiwyg["paste"] = "Paste";
$l_wysiwyg["insert_row"] = "Insert row";
$l_wysiwyg["delete_rows"] = "Delete rows";
$l_wysiwyg["insert_colmn"] = "Insert column";
$l_wysiwyg["delete_colmns"] = "Delete columns";
$l_wysiwyg["insert_cell"] = "Insert cell";
$l_wysiwyg["delete_cells"] = "Delete cells";
$l_wysiwyg["merge_cells"] = "Merge cells";
$l_wysiwyg["split_cell"] = "Split cells";

/*****************************************************************************
 * ALT-TEXTS FOR BUTTONS
 *****************************************************************************/

$l_wysiwyg["subscript"] = "Subscript";
$l_wysiwyg["superscript"] = "Superscript";
$l_wysiwyg["justify_full"] = "Justify full";
$l_wysiwyg["strikethrought"] = "Strike through";
$l_wysiwyg["removeformat"] = "Remove format";
$l_wysiwyg["removetags"] = "Remove tags, styles and comments";
$l_wysiwyg["editcell"] = "Edit table cell";
$l_wysiwyg["edittable"] = "Edit table";
$l_wysiwyg["insert_row2"] = "Insert rows";
$l_wysiwyg["delete_rows2"] = "Delete rows";
$l_wysiwyg["insert_colmn2"] = "Insert column";
$l_wysiwyg["delete_colmns2"] = "Delete columns";
$l_wysiwyg["insert_cell2"] = "Insert cell";
$l_wysiwyg["delete_cells2"] = "Delete cells";
$l_wysiwyg["merge_cells2"] = "Merge cells";
$l_wysiwyg["split_cell2"] = "Split cell";
$l_wysiwyg["insert_edit_table"] = "Insert/edit table";
$l_wysiwyg["insert_edit_image"] = "Insert/edit image";
$l_wysiwyg["edit_style_class"] = "Edit class (style)";
$l_wysiwyg["insert_br"] = "Insert line break (SHIFT + RETURN)";
$l_wysiwyg["insert_p"] = "Insert paragraph";
$l_wysiwyg["edit_sourcecode"] = "Edit source";
$l_wysiwyg["show_details"] = "Show details";
$l_wysiwyg["rtf_import"] = "Import RTF";
$l_wysiwyg["unlink"] = "Remove hyperlink";
$l_wysiwyg["hyperlink"] = "Insert/edit hyperlink";
$l_wysiwyg["back_color"] = "Background color";
$l_wysiwyg["fore_color"] = "Foreground color";
$l_wysiwyg["outdent"] = "Outdent";
$l_wysiwyg["indent"] = "Indent";
$l_wysiwyg["unordered_list"] = "Unordered list";
$l_wysiwyg["ordered_list"] = "Ordered list";
$l_wysiwyg["justify_right"] = "Justify right";
$l_wysiwyg["justify_center"] = "Justify center";
$l_wysiwyg["justify_left"] = "Justify left";
$l_wysiwyg["underline"] = "Underline";
$l_wysiwyg["italic"] = "Italic";
$l_wysiwyg["bold"] = "Bold";
$l_wysiwyg["fullscreen"] = "Open editor in full-screen mode";
$l_wysiwyg["edit_source"] = "Edit source code";
$l_wysiwyg["fullscreen_editor"] = "Full-screen editor";
$l_wysiwyg["table_props"] = "Table properties";
$l_wysiwyg["insert_table"] = "Insert table";
$l_wysiwyg["edit_stylesheet"] = "Edit style sheet";

/*****************************************************************************
 * THE REST
 *****************************************************************************/

$l_wysiwyg["url"] = "URL";
$l_wysiwyg["image_url"] = "Image URL";
$l_wysiwyg["width"] = "Width";
$l_wysiwyg["height"] = "Height";
$l_wysiwyg["hspace"] = "Horizontal space";
$l_wysiwyg["vspace"] = "Vertical space";
$l_wysiwyg["border"] = "Border";
$l_wysiwyg["altText"] = "Alternative text";
$l_wysiwyg["alignment"] = "Alignment";

$l_wysiwyg["external_image"] = "webEdition external image";
$l_wysiwyg["internal_image"] = "webEdition internal image";

$l_wysiwyg["bgcolor"] = "Background color";
$l_wysiwyg["cellspacing"] = "Cell spacing";
$l_wysiwyg["cellpadding"] = "Cell padding";
$l_wysiwyg["rows"] = "Rows";
$l_wysiwyg["cols"] = "Columns";
$l_wysiwyg["edit_table"] = "Edit table";
$l_wysiwyg["colspan"] = "Colspan";
$l_wysiwyg["halignment"] = "Horiz. alignment"; // has to be short !!
$l_wysiwyg["valignment"] = "Vert. alignment";  // has to be short !!
$l_wysiwyg["color"] = "Color";
$l_wysiwyg["choosecolor"] = "Choose color";
$l_wysiwyg["parent_class"] = "Parent area";
$l_wysiwyg["region_class"] = "Selection only";
$l_wysiwyg["edit_classname"] = "Edit style sheet class name";
$l_wysiwyg["emaillink"] = "E-Mail";
$l_wysiwyg["clean_word"] = "Clean MS Word code";
$l_wysiwyg["addcaption"] = "Add caption";
$l_wysiwyg["removecaption"] = "Remove caption";
$l_wysiwyg["anchor"] = "Anchor";

$l_wysiwyg["edit_hr"] = "Horizontal rule";
$l_wysiwyg["color"] = "color";
$l_wysiwyg["noshade"] = "No shading";
$l_wysiwyg["strikethrough"] = "Strike out";

$l_wysiwyg["nothumb"] = "no thumbnail";
$l_wysiwyg["thumbnail"] = "Thumbnail";

$l_wysiwyg["acronym"] = "Acronym";
$l_wysiwyg["acronym_title"] = "Edit Acronym";
$l_wysiwyg["abbr"] = "Abbreviation";
$l_wysiwyg["abbr_title"] = "Edit Abbreviation";
$l_wysiwyg["title"] = "Title";
$l_wysiwyg["language"] = "Language";
$l_wysiwyg["language_title"] = "Edit Language";
$l_wysiwyg["link_lang"] = "Link";
$l_wysiwyg["href_lang"] = "Linked page";
$l_wysiwyg["paragraph"] = "Paragraph";

$l_wysiwyg["summary"] = "Summary";
$l_wysiwyg["isheader"] = "Is heading";

$l_wysiwyg["keyboard"] = "Keyboard";

$l_wysiwyg["relation"] = "Relation";

$l_wysiwyg["fontsize"] = "Font size";
