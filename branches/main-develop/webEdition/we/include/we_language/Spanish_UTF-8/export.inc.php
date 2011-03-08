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
 * Language file: export.inc.php
 * Provides language strings.
 * Language: English
 */
$l_export = array(
		'save_changed_export' => "Export has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'auto_selection' => "Automatic selection", // TRANSLATE
		'txt_auto_selection' => "Exportar documentos u objetos automáticamente de acuerdo al tipo de documento o clase.",
		'txt_auto_selection_csv' => "Exports objects automatically according to their class.", // TRANSLATE
		'manual_selection' => "Selección manual",
		'txt_manual_selection' => "Exportar manualmente los documentos u objetos seleccionados.",
		'txt_manual_selection_csv' => "Exports manually selected objects.", // TRANSLATE
		'element' => "Selección de elementos",
		'documents' => "Documentos",
		'objects' => "Objetos",
		'step1' => "Selecionar parámetros",
		'step2' => "Seleccionar articulos para exportar",
		'step3' => "Seleccionar parámetros de exportación ",
		'step10' => "Exportación terminada",
		'step99' => "Error mientras se exportaba",
		'step99_notice' => "Exportación no posible",
		'server_finished' => "El archivo exportado ha sido salvado en el servidor.",
		'backup_finished' => "La exportación ha sido exitosa.",
		'download_starting' => "La descarga del archivo exportado ha sido iniciado.<br><br>Si la descarga no se inicia después de 10 segundos,<br>",
		'download' => "por favor, dar clic aquí.",
		'download_failed' => "El archivo que UD solicitó no existe o a UD no le está permitido descargarlo.",
		'file_format' => "Formato de archivo",
		'export_to' => "Exportar a",
		'export_to_server' => "Servidor",
		'export_to_local' => "Disco duro local",
		'cdata' => "Codificando",
		'export_xml_cdata' => "Adicionar secciones CDATA",
		'export_xml_entities' => "Reemplazar entidades",
		'filename' => "Nombre archivo",
		'path' => "Ruta de acceso",
		'doctypename' => "Documentos del tipo de documento",
		'classname' => "Clase de objetos",
		'dir' => "Directorio",
		'categories' => "Categorías",
		'wizard_title' => "Asistente de Exportación",
		'xml_format' => "XML", // TRANSLATE
		'csv_format' => "CSV", // TRANSLATE
		'csv_delimiter' => "Delimitador",
		'csv_enclose' => "Adjuntar carácter",
		'csv_escape' => "Evadir carácter",
		'csv_lineend' => "Formato de archivo",
		'csv_null' => "Reemplazo Nulo",
		'csv_fieldnames' => "Colocar nombre de campos en primera fila",
		'windows' => "Formato de Windows",
		'unix' => "Formato UNIX",
		'mac' => "Formato Mac",
		'generic_export' => "Exportación genérica",
		'title' => "Asistente de Exportación",
		'gxml_export' => "Exportación genérica de XML",
		'txt_gxml_export' => "Exportar documentos y objetos webEdition a un archivo XML \"plano\" (3 niveles).",
		'csv_export' => "Exportación de CSV",
		'txt_csv_export' => "Exportar objetos webEdition a un archivo CSV (Valores Separados por Comas).",
		'csv_params' => "Ajustes",
		'error' => "El proceso de exportación no fue exitoso!",
		'error_unknown' => "Un error desconocido ocurrio!",
		'error_object_module' => "La exportación de documentos a archivos CSV no es actualmente sostenida!<br><br>Dado que el Módulo Base de datos/Objeto no está instalado, la exportación de archivos CSV no está disponible.",
		'error_nothing_selected_docs' => "La exportación no ha sido ejecutada!<br><br>Ningún documento fue selecionado.",
		'error_nothing_selected_objs' => "La exportación no ha sido ejecutada!<br><br>Ningún documento u objeto fue selecionado.",
		'error_download_failed' => "Descarga del archivo exportado fallida.",
		'comma' => ", {coma}",
		'semicolon' => "; {punto y coma}",
		'colon' => ": {dos puntos}",
		'tab' => "\\t {tabulador}",
		'space' => "  {espacio}",
		'double_quote' => "\" {comillas dobles}",
		'single_quote' => "' {comilla}",
		'we_export' => 'Exportación de webEdition',
		'wxml_export' => 'Exportación de XML webEdition',
		'txt_wxml_export' => 'Exportación de documentos, plantillas, objetos y clases webEdition, correspondiendo a la DTD (definición de tipo de documento) específica de webEdition.',
		'options' => 'Options', // TRANSLATE
		'handle_document_options' => 'Documents', // TRANSLATE
		'handle_template_options' => 'Templates', // TRANSLATE
		'handle_def_templates' => 'Export default templates', // TRANSLATE
		'handle_document_includes' => 'Export included documents', // TRANSLATE
		'handle_document_linked' => 'Export linked documents', // TRANSLATE
		'handle_object_options' => 'Objects', // TRANSLATE
		'handle_def_classes' => 'Export default classes', // TRANSLATE
		'handle_object_includes' => 'Export included objects', // TRANSLATE
		'handle_classes_options' => 'Classes', // TRANSLATE
		'handle_class_defs' => 'Default value', // TRANSLATE
		'handle_object_embeds' => 'Export embedded objects', // TRANSLATE
		'handle_doctype_options' => 'Doctypes/<br>Categorys/<br>Navigation',
		'handle_doctypes' => 'Doctypes', // TRANSLATE
		'handle_categorys' => 'Categorys',
		'export_depth' => 'Export depth', // TRANSLATE
		'to_level' => 'to level', // TRANSLATE
		'select_export' => 'Para exportar una entrada, por favor seleccione la casilla apropiada en el árbol. Nota importante: Todas las entradas seleccionadas en todas las ramas serán exportadas y si se exporta un directorio, todos los documentos en ese directorio serán exportados también!',
		'templates' => 'Templates', // TRANSLATE
		'classes' => 'Classes', // TRANSLATE

		'nothing_to_delete' => 'No existe nada para eliminar.',
		'nothing_to_save' => '¡No existe nada para salvar!',
		'no_perms' => 'No posee permisos!',
		'new' => 'Nuevo',
		'export' => 'Exportar',
		'group' => 'Agrupar',
		'save' => 'Guardar',
		'delete' => 'Eliminar',
		'quit' => 'Salir',
		'property' => 'Propiedad',
		'name' => 'Nombre',
		'save_to' => 'Guardar en:',
		'selection' => 'Selección',
		'save_ok' => 'La exportación ha sido guardada.',
		'save_group_ok' => 'El grupo ha sido guardado.',
		'log' => 'Detalles',
		'start_export' => 'Iniciar exportación',
		'prepare' => 'Preparar exportación...',
		'doctype' => 'Tipo de documento',
		'category' => 'Categoría',
		'end_export' => 'Exportación terminada',
		'newFolder' => "Nuevo grupo",
		'folder_empty' => "¡La carpeta está vacía!",
		'folder_path_exists' => "¡La carpeta ya existe!",
		'wrongtext' => "Nombre no válido",
		'wrongfilename' => "The filename is not valid!", // TRANSLATE
		'folder_exists' => "¡La carpeta ya existe!",
		'delete_ok' => 'La exportación ha sido eliminada.',
		'delete_nok' => 'ERROR: La exportación no ha sido eliminada',
		'delete_group_ok' => 'El grupo ha sido eliminado.',
		'delete_group_nok' => 'ERROR: El grupo no ha sido eliminado',
		'delete_question' => '¿Desea eliminar la exportación actual?',
		'delete_group_question' => '¿Desea eliminar el grupo actual?',
		'download_starting2' => 'La descarga de la exportación ha sido iniciada.',
		'download_starting3' => 'Si la descarga no se inicia despues de 10 seconds,',
		'working' => 'Trabajando',
		'txt_document_options' => 'La plantilla predeterminada es la que está definida en las propiedades del documento. Los documentos incluidos son documentos internos que se incluyen en el documento de exportación con las etiquetas we:include, we:form, we:url, we:linkToSeeMode, we:a, we:href, we:link, we:css, we:js and we:addDelNewsletterEmail. Los objetos incluidos son aquellos que seincluyen en el documento de exportación con las etiquetas we:object and we:form. Los documentos vinculados son documentos internos que se vinculan al el documento de exportación con las etiquetas HTML: body, a, img, table and td.',
		'txt_object_options' => 'La clase predeterminada está definida en las propiedades del objeto.',
		'txt_exportdeep_options' => 'La profundidad de exportación define el nivel para la exportación de los documentos incluidos. ¡El valor debe ser un número!',
		'name_empty' => '¡El campo del nombre no puede estar vacío!',
		'name_exists' => 'El nombre ya existe!',
		'help' => 'Ayuda',
		'info' => 'Información',
		'path_nok' => '¡El camino no es correcto!',
		'must_save' => 'La exportación ha sido modificada.\\n¡Debe salvar los datos de exportación antes de poder hacer la exportación!',
		'handle_owners_option' => 'Datos propietarios',
		'handle_owners' => 'Exportar datos propietarios',
		'txt_owners' => 'Exportar datos propietarios vinculados.',
		'weBinary' => 'File', // TRANSLATE
		'handle_navigation' => 'Navigation', // TRANSLATE
		'weNavigation' => 'Navigation', // TRANSLATE
		'weNavigationRule' => 'Navigation rule', // TRANSLATE
		'weThumbnail' => 'Thumbnails', // TRANSLATE
		'handle_thumbnails' => 'Thumbnails', // TRANSLATE

		'navigation_hint' => 'To export the navigation entries, the template on which the navigation is displayed has also to be exported!',
		'title' => 'Export Wizard', // TRANSLATE
		'selection_type' => 'Determine element selection', // TRANSLATE
		'auto_selection' => 'Automatic selection', // TRANSLATE
		'txt_auto_selection' => 'Exports automatically - according to the doctype or class - selected documents or objects.', // TRANSLATE
		'manual_selection' => 'Manual selection', // TRANSLATE
		'txt_manual_selection' => 'Exports manually selected documents or objects.', // TRANSLATE
		'element' => 'Element selection', // TRANSLATE
		'select_elements' => 'Select elements for export', // TRANSLATE
		'select_docType' => 'Please, choose a doctype or a template.', // TRANSLATE
		'none' => '-- none --', // TRANSLATE
		'doctype' => 'Doctype', // TRANSLATE
		'template' => 'Template', // TRANSLATE
		'categories' => 'Categories', // TRANSLATE
		'documents' => 'Documents', // TRANSLATE
		'objects' => 'Objects', // TRANSLATE
		'class' => 'Class', // TRANSLATE
		'isDynamic' => 'Generate page dynamically', // TRANSLATE
		'extension' => 'Extension', // TRANSLATE
		'wexml_export' => 'weXML Export', // TRANSLATE
		'filename' => 'File name', // TRANSLATE
		'extra_data' => 'Extra data', // TRANSLATE
		'integrated_data' => 'Export included data', // TRANSLATE
		'integrated_data_txt' => 'Choose this option to export the the data included by the templates or documents.', // TRANSLATE
		'max_level' => 'to level', // TRANSLATE
		'export_doctypes' => 'Export doctypes', // TRANSLATE
		'export_categories' => 'Export categories', // TRANSLATE
		'export_location' => 'Export to', // TRANSLATE
		'local_drive' => 'Local drive', // TRANSLATE
		'server' => 'Server', // TRANSLATE
		'export_progress' => 'Exporting', // TRANSLATE
		'prepare_progress' => 'Preparing', // TRANSLATE
		'finish_progress' => 'Finished', // TRANSLATE
		'finish_export' => 'The export was successful!', // TRANSLATE
);