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

$l_wysiwyg["window_title"] = "Editar campo '%s'";

$l_wysiwyg["format"] = "Formato";
$l_wysiwyg["fontsize"] = "Tamaño de la fuente";
$l_wysiwyg["fontname"] = "Nombre de la fuente";
$l_wysiwyg["css_style"] = "Estilo CSS";

$l_wysiwyg["normal"] = "Normal"; // TRANSLATE
$l_wysiwyg["h1"] = "Encabezamiento 1";
$l_wysiwyg["h2"] = "Encabezamiento 2";
$l_wysiwyg["h3"] = "Encabezamiento 3";
$l_wysiwyg["h4"] = "Encabezamiento 4";
$l_wysiwyg["h5"] = "Encabezamiento 5";
$l_wysiwyg["h6"] = "Encabezamiento 6";
$l_wysiwyg["pre"] = "Formateado";
$l_wysiwyg["address"] = "Dirección";

$GLOBALS['l_wysiwyg']['spellcheck'] = 'Spellchecking'; // TRANSLATE


/*****************************************************************************
 * CONTEXT MENUS
 *****************************************************************************/

// REMEMBER: context menus cannot display any umlauts!
$l_wysiwyg["cut"] = "Cortar";
$l_wysiwyg["copy"] = "Copiar";
$l_wysiwyg["paste"] = "Pegar";
$l_wysiwyg["insert_row"] = "Insertar fila";
$l_wysiwyg["delete_rows"] = "Borrar filas";
$l_wysiwyg["insert_colmn"] = "Insertar columna";
$l_wysiwyg["delete_colmns"] = "Borrar columnas";
$l_wysiwyg["insert_cell"] = "Insertar celda";
$l_wysiwyg["delete_cells"] = "Borrar celdas";
$l_wysiwyg["merge_cells"] = "Reunir celdas";
$l_wysiwyg["split_cell"] = "Dividir celdas";

/*****************************************************************************
 * ALT-TEXTS FOR BUTTONS
 *****************************************************************************/

$l_wysiwyg["subscript"] = "Subíndice";
$l_wysiwyg["superscript"] = "Superíndice";
$l_wysiwyg["justify_full"] = "Alinear completo";
$l_wysiwyg["strikethrought"] = "Marcar texto";
$l_wysiwyg["removeformat"] = "Remover formato";
$l_wysiwyg["removetags"] = "Remove tags, styles and comments"; // TRANSLATE
$l_wysiwyg["editcell"] = "Editar celda de la tabla";
$l_wysiwyg["edittable"] = "Editar tabla";
$l_wysiwyg["insert_row2"] = "Insertar filas";
$l_wysiwyg["delete_rows2"] = "Borrar filas";
$l_wysiwyg["insert_colmn2"] = "Insertar columna";
$l_wysiwyg["delete_colmns2"] = "Borrar columnas";
$l_wysiwyg["insert_cell2"] = "Insertar celda";
$l_wysiwyg["delete_cells2"] = "Borrar celdas";
$l_wysiwyg["merge_cells2"] = "Reunir celdas";
$l_wysiwyg["split_cell2"] = "Dividir celda";
$l_wysiwyg["insert_edit_table"] = "Insertar/editar tabla";
$l_wysiwyg["insert_edit_image"] = "Insertar/editar imagen";
$l_wysiwyg["edit_style_class"] = "Editar clase (estilo)";
$l_wysiwyg["insert_br"] = "Insertar pausa de línea (SHIFT + RETURN)";
$l_wysiwyg["insert_p"] = "Insertar párrafo";
$l_wysiwyg["edit_sourcecode"] = "Editar original";
$l_wysiwyg["show_details"] = "Mostrar detalles";
$l_wysiwyg["rtf_import"] = "Importar RTF";
$l_wysiwyg["unlink"] = "Remover hipervínculo";
$l_wysiwyg["hyperlink"] = "Insertar/editar hipervínculo";
$l_wysiwyg["back_color"] = "Fondo del carácter";
$l_wysiwyg["fore_color"] = "Color del carácter";
$l_wysiwyg["outdent"] = "Aumentar la sangría";
$l_wysiwyg["indent"] = "Reducir la sangría";
$l_wysiwyg["unordered_list"] = "Activar/desactivar viñetas";
$l_wysiwyg["ordered_list"] = "Activar/desactivar numeración";
$l_wysiwyg["justify_right"] = "Alinear a la derecha";
$l_wysiwyg["justify_center"] = "Centrado";
$l_wysiwyg["justify_left"] = "Alinear a la izquierda";
$l_wysiwyg["underline"] = "Subrayado";
$l_wysiwyg["italic"] = "Cursiva";
$l_wysiwyg["bold"] = "Negrita";
$l_wysiwyg["fullscreen"] = "Abrir editor en modo pantalla completa";
$l_wysiwyg["edit_source"] = "Editar código original";
$l_wysiwyg["fullscreen_editor"] = "Editor de pantalla completa";
$l_wysiwyg["table_props"] = "Propiedades de la tabla";
$l_wysiwyg["insert_table"] = "Insertar tabla";
$l_wysiwyg["edit_stylesheet"] = "Editar hoja de estilo";

/*****************************************************************************
 * THE REST
 *****************************************************************************/

$l_wysiwyg["url"] = "URL"; // TRANSLATE
$l_wysiwyg["image_url"] = "URL de la imagen";
$l_wysiwyg["width"] = "Ancho";
$l_wysiwyg["height"] = "Alto";
$l_wysiwyg["hspace"] = "Espacio horizontal";
$l_wysiwyg["vspace"] = "Espacio vertical";
$l_wysiwyg["border"] = "Borde";
$l_wysiwyg["altText"] = "Texto alternativo";
$l_wysiwyg["alignment"] = "Aliniación";

$l_wysiwyg["external_image"] = "Imagen externa de webEdition";
$l_wysiwyg["internal_image"] = "Imagen interna de webEdition";

$l_wysiwyg["bgcolor"] = "Color de fondo";
$l_wysiwyg["cellspacing"] = "Espaciamiento de la celda";
$l_wysiwyg["cellpadding"] = "Relleno de la celda";
$l_wysiwyg["rows"] = "Filas";
$l_wysiwyg["cols"] = "Columnas";
$l_wysiwyg["edit_table"] = "Editar tabla";
$l_wysiwyg["colspan"] = "Ancho de la columna";
$l_wysiwyg["halignment"] = "Alineación horiz."; // has to be short !!
$l_wysiwyg["valignment"] = "Alineación vert.";  // has to be short !!
$l_wysiwyg["color"] = "Color";
$l_wysiwyg["choosecolor"] = "Escoger color";
$l_wysiwyg["parent_class"] = "Área primaria";
$l_wysiwyg["region_class"] = "Selección solamente";
$l_wysiwyg["edit_classname"] = "Editar nombre de clase de la hoja de estilo";
$l_wysiwyg["emaillink"] = "E-Mail"; // TRANSLATE
$l_wysiwyg["clean_word"] = "Eliminar código MS Word";
$l_wysiwyg["addcaption"] = "Agregar Caption";
$l_wysiwyg["removecaption"] = "Remover Caption";
$l_wysiwyg["anchor"] = "Anclaje";

$l_wysiwyg["edit_hr"] = "Regla horizontal";
$l_wysiwyg["color"] = "color"; // TRANSLATE
$l_wysiwyg["noshade"] = "Sin matiz";
$l_wysiwyg["strikethrough"] = "Rayado";

$l_wysiwyg["nothumb"] = "Sin imagen en miniatura";
$l_wysiwyg["thumbnail"] = "Imagen en miniatura";

$l_wysiwyg["acronym"] = "Acrónimo";
$l_wysiwyg["acronym_title"] = "Editar acrónimo";
$l_wysiwyg["abbr"] = "Abbreviation"; // TRANSLATE
$l_wysiwyg["abbr_title"] = "Edit Abbreviation"; // TRANSLATE
$l_wysiwyg["title"] = "Título";
$l_wysiwyg["language"] = "Idioma";
$l_wysiwyg["language_title"] = "Editar Idioma";
$l_wysiwyg["link_lang"] = "Vínculo";
$l_wysiwyg["href_lang"] = "Página vinculada";
$l_wysiwyg["paragraph"] = "Párrafo";

$l_wysiwyg["summary"] = "Resumen";
$l_wysiwyg["isheader"] = "Es encabezamiento";

$l_wysiwyg["keyboard"] = "Teclado";

$l_wysiwyg["relation"] = "Relación";

$l_wysiwyg["fontsize"] = "Font size"; // TRANSLATE
