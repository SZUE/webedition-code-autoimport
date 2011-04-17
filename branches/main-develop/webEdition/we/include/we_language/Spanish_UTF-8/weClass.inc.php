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
 * Language file: we_class.inc.php
 * Provides language strings.
 * Language: English
 */
$l_weClass = array(
		'ChangePark' => "Ud solamente puede cambiar este atributo si el documento no es publicado!",
		'fieldusers' => "Usuario",
		'other' => "Otro",
		'use_object' => "Usar objeto",
		'language' => "Language", // TRANSLATE
		'users' => "Dueños predefinidos",
		'copytext/css' => "Copiar hoja de estilo CSS ",
		'copytext/js' => "Copiar JavaScript",
		'copytext/html' => "Copiar página HTML",
		'copytext/plain' => "Copiar página de texto",
		'copytext/htaccess' => "Copy .htaccess file", //TRANSLATE
		'copytext/xml' => "Copy XML document", // TRANSLATE
		'copyTemplate' => "Copiar plantilla",
		'copyFolder' => "Copiar directorio",
		'copy_owners_expl' => "Seleccione un directorio cuyo contenido será copiado al directorio actual.",
		'category' => "Categoría",
		'folder_saved_ok' => "La carpeta '%s' ha sido exitosamente salvada!",
		'response_save_ok' => "El documento '%s' ha sido exitosamente salvado!",
		'response_publish_ok' => "El documento '%s' ha sido exitosamente publicado!",
		'response_unpublish_ok' => "El documento '%s' ha sido exitosamente des-publicado!",
		'response_save_notok' => "Error mientras se salvava documento '%s'!",
		'response_path_exists' => "El documento o carpeta %s no pudo ser salvado(a) porque otro documento está posicionado en la misma ubicación!",
		'width' => "Ancho",
		'height' => "Alto",
		'origwidth' => "o.A.",
		'origheight' => "o.A.",
		'width_tmp' => "Ancho",
		'height_tmp' => "Alto",
		'percent_width_tmp' => "Ancho en %",
		'percent_height_tmp' => "Alto en %",
		'alt' => "Texto alternativo",
		'alt_kurz' => "Texto alt.",
		'title' => "Título",
		'use_meta_title' => "Use el meta título",
		'longdesc_text' => "Archivo para una descripción larga",
		'align' => "Aliniación",
		'name' => "Nombre",
		'hspace' => "EspacioH",
		'vspace' => "EspacioV",
		'border' => "Borde",
		'fields' => "Campos",
		'AutoFolder' => "Carpeta automática",
		'AutoFilename' => "Nombre de campo",
		'import_ok' => "Documentos exitosamente importados!",
		'function' => "Función",
		'contenttable' => "Tabla de contenido",
		'quality' => "Calidad",
		'salign' => "Alinear a escala",
		'play' => "Ejecutar",
		'loop' => "Ciclo",
		'scale' => "Escala",
		'wmode' => "Window mode", // TRANSLATE
		'bgcolor' => "Color de fondo",
		'response_save_noperms_to_create_folders' => "El documento no pudo ser salvado porque Ud no tiene los derechos necesarios para crear carpetas (%s)!",
		'file_on_liveserver' => "Archivo ya existe",
		'defaults' => "Valores<br>predefinidos",
		'attribs' => "Atributos",
		'intern' => "Interno",
		'extern' => "Externo",
		'linkType' => "Tipo de vínculo",
		'href' => "Href", // TRANSLATE
		'target' => "Destino",
		'hyperlink' => "Hipervínculo",
		'nolink' => "Sin vínculo",
		'noresize' => "Sin cambiar de tamaño",
		'pixel' => "Pixel", // TRANSLATE
		'percent' => "Porciento",
		'new_doc_type' => "Nuevo tipo de documento",
		'doctypes' => "Tipo de documento",
		'subdirectory' => "Subdirectorio",
		'subdir' => array(
				SUB_DIR_NO => "-- ninguno --",
				SUB_DIR_YEAR => "Año",
				SUB_DIR_YEAR_MONTH => "Año/Mes",
				SUB_DIR_YEAR_MONTH_DAY => "Año/Mes/Día",
		),
		'doctype_save_ok' => "El tipo de documento '%s' fue exitosamente salvado!",
		'doctype_save_nok_exist' => "El tipo de documento con el nombre '%s' ya existe.\\n Escoja otro nombre e intentelo nuevamente!",
		'delete_doc_type' => "Borrar '%s'",
		'doctype_delete_prompt' => "Borrar el tipo de documento '%s'! Está UD seguro?",
		'doctype_delete_nok' => "El tipo de documento con el nombre '%s' está en uso!\\n El tipo de documento no puede ser borrado!",
		'doctype_delete_ok' => "El tipo de documento con el nombre '%s fue exitosamente borrado!",
		'confirm_ext_change' => "UD ha cambiado la generación dinámica\\nDesea UD cambiar la extensión a predefinida?",
		'newDocTypeName' => 'Por favor, entre un nombre para el nevo documento!',
		'no_perms' => 'UD no tiene permiso para esta acción!',
		'workspaces' => "Áreas de trabajo",
		'extraWorkspaces' => "Áreas de trabajo extras",
		'edit' => "Editar",
		'edit_image' => "Image editing", // TRANSLATE
		'workspace' => "Áreas de trabajo",
		'information' => "Información",
		'previeweditmode' => "Preview Editmode", // TRANSLATE
		'preview' => "Vista previa",
		'no_preview_available' => "No preview available for this file. To view this file please download it first.", // TRANSLATE
		'file_not_saved' => "File wasn't saved yet.", // TRANSLATE
		'download' => "Download", // TRANSLATE
		'validation' => "Validación",
		'variants' => "Variantes",
		'tab_properties' => "Propiedades",
		'metainfos' => "Meta infos", // TRANSLATE
		'fields' => "Campos",
		'search' => "Buscar",
		'schedpro' => "Planificador PRO",
		'generateTemplate' => "Generar plantilla",
		'autoplay' => "Auto ejecutar",
		'controller' => "Mostrar controlador",
		'volume' => "Volumen",
		'hidden' => "Oculto",
		'workspacesFromClass' => "Adoptar de la clase",
		'image' => "Imagen",
		'thumbnails' => "Imágenes en miniatura",
		'metadata' => "Metadata", // TRANSLATE
		'edit_show' => "Mostrar opciones de la imagen",
		'edit_hide' => "Ocultar opciones de la imagen",
		'resize' => "Cambiar de tamaño",
		'rotate' => "Rotar imagen",
		'rotate_hint' => "La versión de la librería GD  - instalada en este servidor - no soporta la rotación de imagen!",
		'crop' => "Recortar imagen",
		'quality' => "Calidad",
		'quality_hint' => "Ajuste aquí la calidad de la imagen para la compresión JPEG. <br><br> 10: calidad máxima, se requiere mas espacio en disco <br> 0: calidad inferior, menos espacio en disco es requerido",
		'quality_maximum' => "Máxima",
		'quality_high' => "Alto",
		'quality_medium' => "Media",
		'quality_low' => "Baja",
		'convert' => "Convertir",
		'convert_gif' => "Formato GIF",
		'convert_jpg' => "Formato JPEG",
		'convert_png' => "Formato PNG",
		'rotate0' => "Sin rotación",
		'rotate180' => "Rotar 180°;",
		'rotate90l' => "Rotate 90°; ccw",
		'rotate90r' => "Rotate 90°; cw",
		'change_compression' => "Cambiar compresión",
		'upload' => "Cargar",
		'type_not_supported_hint' => "La versión de la librería GD  - instalada en este servidor - no soporta %s para el formato de salida! Por favor, convirta la imagen a un formato compatible!",
		'CSS' => "CSS", // TRANSLATE
		'we_del_workspace_error' => "El espacio de trabajo no podra ser suprimido mientras que es utilizado por los objetos de la clase!",
		'master_template' => "Master template", // TRANSLATE
		'same_master_template' => "The selected master template cannot be identical with the current template!", // TRANSLATE
		'documents' => "Documents", // TRANSLATE
		'no_documents' => "No document based on this template", // TRANSLATE

		'grant_language' => "Change language", // TRANSLATE
		'grant_language_expl' => "Change the language of all files and directories which reside in the current directory to the setting above.", // TRANSLATE
		'grant_language_ok' => "Language have been successfully changed!", // TRANSLATE
		'grant_language_notok' => "There was an error while changing the language!", // TRANSLATE

		'grant_tid' => "Change display document", // TRANSLATE
		'grant_tid_expl' => "Change the display document of all files and directories which reside in the current directory to the setting above.", // TRANSLATE
		'grant_tid_ok' => "The display document has been successfully changed!", // TRANSLATE
		'grant_tid_notok' => "There was an error while changing the display document", // TRANSLATE

		'notValidFolder' => "The directory chosen is invalid!", // TRANSLATE


		'saveFirstMessage' => "You need to save your changes before executing this command.", // TRANSLATE

		'image_edit_null_not_allowed' => "In the fields Width and Height only numbers greater than 0 are allowed!", // TRANSLATE

		'doctype_changed_question' => "Should the default values for the document type be applied for this document?", // TRANSLATE
		'availableAfterSave' => "The feature is only available after saving the entry.", // TRANSLATE

		'properties' => 'Propiedades',
		'path' => 'Ruta de acceso',
		'adoptToAllInferiorDocuments' => 'adopt for all inferior documents', // TRANSLATE
		'filename' => 'Nombre de archivo',
		'extension' => 'Extensión',
		'dir' => 'Directorio',
		'document' => 'Documento',
		'upload_will_replace' => 'Ud puede ahora reemplazar el archivo actual usando el botón "cargar". Si Ud desea cargar otro archivo o múltiples archivos de una vez; por favor, use la función de importación o regrese a esta página usando el menú de archivo.',
		'upload_single_files' => 'ATENCIÓN: Por favor, note que solamente puede cargar archivos únicos usando esta función. Para cargar múltiples archivos de una vez use la función de importación, a la cual puede acceder usando el menú de archivo.',
		'none' => '--Ninguno--',
		'nodoctype' => '--ninguno--',
		'doctype' => 'Tipo de documento',
		'standard_workspace' => 'Default Workspace', // TRANSLATE
		'standard_template' => 'Plantilla',
		'template' => 'Plantilla',
		'no_template' => 'Sin plantilla',
		'IsDynamic' => 'Generar página dinámica',
		'IsSearchable' => 'És buscable?',
		'InGlossar' => 'Not allowed for automatic glossary replacement', // TRANSLATE //TRANSLATE
		'metainfo' => 'Meta rótulos',
		'Title' => 'Titulo',
		'Keywords' => 'Palabras claves',
		'Description' => 'Descripción',
		'urlfield0' => 'URL field', // TRANSLATE
		'urlfield1' => 'URL field 1', // TRANSLATE
		'urlfield2' => 'URL field 2', // TRANSLATE
		'urlfield3' => 'URL field 3', // TRANSLATE
		'Charset' => 'Codificación de carácter',
		'moreProps' => 'Más propiedades',
		'lessProps' => 'Menos propiedades',
		'owners' => 'Dueños',
		'maincreator' => 'Creador principal',
		'otherowners' => 'Conceder acceso a los siguientes usuarios',
		'onlyOwner' => 'Creador solamente',
		'limitedAccess' => 'Restringir acceso',
		'nobody' => 'Nadie',
		'everybody' => 'Todos',
		'noWorkspaces' => 'No hay área de trabajo definida en esta clase!',
		'readOnly' => 'Solo lectura',
		'copyWeDoc' => 'Copiar página webEdition',
		'showTagwizard' => 'Mostrar Asistente de Rótulos',
		'hideTagwizard' => 'Ocultar Asistente de Rótulos',
		'variant_fields' => 'Campos',
		'variant_info' => 'Los Siguientes campos pueden tener diferentes variantes. Por favor seleccione los campos que tienen diferentes variantes.',
		'webUser' => 'Customer', // TRANSLATE
		'docList' => 'Content', // TRANSLATE
		'version' => 'Versions', // TRANSLATE
		'languageLinksDefaults' => 'Default value for the document type in corresponding documents in the other front end languages', // TRANSLATE
		'languageLinks' => 'Link to the corresponding documents/objects in other languages',// TRANSLATE
);
