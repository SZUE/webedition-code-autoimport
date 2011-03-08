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
		'window_title' => "Editar campo '%s'",
		'format' => "Formato",
		'fontsize' => "Tamaño de la fuente",
		'fontname' => "Nombre de la fuente",
		'css_style' => "Estilo CSS",
		'normal' => "Normal", // TRANSLATE
		'h1' => "Encabezamiento 1",
		'h2' => "Encabezamiento 2",
		'h3' => "Encabezamiento 3",
		'h4' => "Encabezamiento 4",
		'h5' => "Encabezamiento 5",
		'h6' => "Encabezamiento 6",
		'pre' => "Formateado",
		'address' => "Dirección",
		'spellcheck' => 'Spellchecking', // TRANSLATE


		/*		 * ***************************************************************************
		 * CONTEXT MENUS
		 * *************************************************************************** */

// REMEMBER: context menus cannot display any umlauts!
		'cut' => "Cortar",
		'copy' => "Copiar",
		'paste' => "Pegar",
		'insert_row' => "Insertar fila",
		'delete_rows' => "Borrar filas",
		'insert_colmn' => "Insertar columna",
		'delete_colmns' => "Borrar columnas",
		'insert_cell' => "Insertar celda",
		'delete_cells' => "Borrar celdas",
		'merge_cells' => "Reunir celdas",
		'split_cell' => "Dividir celdas",
		/*		 * ***************************************************************************
		 * ALT-TEXTS FOR BUTTONS
		 * *************************************************************************** */

		'subscript' => "Subíndice",
		'superscript' => "Superíndice",
		'justify_full' => "Alinear completo",
		'strikethrought' => "Marcar texto",
		'removeformat' => "Remover formato",
		'removetags' => "Remove tags, styles and comments", // TRANSLATE
		'editcell' => "Editar celda de la tabla",
		'edittable' => "Editar tabla",
		'insert_row2' => "Insertar filas",
		'delete_rows2' => "Borrar filas",
		'insert_colmn2' => "Insertar columna",
		'delete_colmns2' => "Borrar columnas",
		'insert_cell2' => "Insertar celda",
		'delete_cells2' => "Borrar celdas",
		'merge_cells2' => "Reunir celdas",
		'split_cell2' => "Dividir celda",
		'insert_edit_table' => "Insertar/editar tabla",
		'insert_edit_image' => "Insertar/editar imagen",
		'edit_style_class' => "Editar clase (estilo)",
		'insert_br' => "Insertar pausa de línea (SHIFT + RETURN)",
		'insert_p' => "Insertar párrafo",
		'edit_sourcecode' => "Editar original",
		'show_details' => "Mostrar detalles",
		'rtf_import' => "Importar RTF",
		'unlink' => "Remover hipervínculo",
		'hyperlink' => "Insertar/editar hipervínculo",
		'back_color' => "Fondo del carácter",
		'fore_color' => "Color del carácter",
		'outdent' => "Aumentar la sangría",
		'indent' => "Reducir la sangría",
		'unordered_list' => "Activar/desactivar viñetas",
		'ordered_list' => "Activar/desactivar numeración",
		'justify_right' => "Alinear a la derecha",
		'justify_center' => "Centrado",
		'justify_left' => "Alinear a la izquierda",
		'underline' => "Subrayado",
		'italic' => "Cursiva",
		'bold' => "Negrita",
		'fullscreen' => "Abrir editor en modo pantalla completa",
		'edit_source' => "Editar código original",
		'fullscreen_editor' => "Editor de pantalla completa",
		'table_props' => "Propiedades de la tabla",
		'insert_table' => "Insertar tabla",
		'edit_stylesheet' => "Editar hoja de estilo",
		/*		 * ***************************************************************************
		 * THE REST
		 * *************************************************************************** */

		'url' => "URL", // TRANSLATE
		'image_url' => "URL de la imagen",
		'width' => "Ancho",
		'height' => "Alto",
		'hspace' => "Espacio horizontal",
		'vspace' => "Espacio vertical",
		'border' => "Borde",
		'altText' => "Texto alternativo",
		'alignment' => "Aliniación",
		'external_image' => "Imagen externa de webEdition",
		'internal_image' => "Imagen interna de webEdition",
		'bgcolor' => "Color de fondo",
		'cellspacing' => "Espaciamiento de la celda",
		'cellpadding' => "Relleno de la celda",
		'rows' => "Filas",
		'cols' => "Columnas",
		'edit_table' => "Editar tabla",
		'colspan' => "Ancho de la columna",
		'halignment' => "Alineación horiz.", // has to be short !!
		'valignment' => "Alineación vert.", // has to be short !!
		'color' => "Color",
		'choosecolor' => "Escoger color",
		'parent_class' => "Área primaria",
		'region_class' => "Selección solamente",
		'edit_classname' => "Editar nombre de clase de la hoja de estilo",
		'emaillink' => "E-Mail", // TRANSLATE
		'clean_word' => "Eliminar código MS Word",
		'addcaption' => "Agregar Caption",
		'removecaption' => "Remover Caption",
		'anchor' => "Anclaje",
		'edit_hr' => "Regla horizontal",
		'color' => "color", // TRANSLATE
		'noshade' => "Sin matiz",
		'strikethrough' => "Rayado",
		'nothumb' => "Sin imagen en miniatura",
		'thumbnail' => "Imagen en miniatura",
		'acronym' => "Acrónimo",
		'acronym_title' => "Editar acrónimo",
		'abbr' => "Abbreviation", // TRANSLATE
		'abbr_title' => "Edit Abbreviation", // TRANSLATE
		'title' => "Título",
		'language' => "Idioma",
		'language_title' => "Editar Idioma",
		'link_lang' => "Vínculo",
		'href_lang' => "Página vinculada",
		'paragraph' => "Párrafo",
		'summary' => "Resumen",
		'isheader' => "Es encabezamiento",
		'keyboard' => "Teclado",
		'relation' => "Relación",
		'fontsize' => "Font size", // TRANSLATE
				));