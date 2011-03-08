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
 * Language file: import.inc.php
 * Provides language strings.
 * Language: English
 */
$l_import = array(
		'title' => 'Asistente de Importación',
		'wxml_import' => 'Importación de XML webEdition',
		'gxml_import' => 'Importación de XML Genérico',
		'csv_import' => 'Importación de CSV',
		'import' => 'Importando',
		'none' => '-- none --', // TRANSLATE
		'any' => '-- none --', // TRANSLATE
		'source_file' => 'Archivo original',
		'import_dir' => 'Directorio objetivo',
		'select_source_file' => 'Por favor escoja el archivo original.',
		'we_title' => 'Titulo',
		'we_description' => 'Descripción',
		'we_keywords' => 'Palabras claves',
		'uts' => 'Marca horária Unix',
		'unix_timestamp' => 'La marca horária unix es una forma de seguir el tiempo como una marcha total segundos. Este conteo se inicia en el Unix Epoch, 1st de Enero, 1970.',
		'gts' => 'Marca horária GMT',
		'gmt_timestamp' => 'General Mean Time ie. Greenwich Mean Time (GMT).', // TRANSLATE
		'fts' => 'Formato especificado',
		'format_timestamp' => 'Los siguientes carácteres son reconocidos en la cadena de parámetros de formato: Y (una representación númerica completa de un año, 4 dígitos), y (una represenatación de un año de dos dígitos), m (representación númerica de un mes, encabezada por ceros), n (representación númerica de un mes, no encabezada por ceros), d (día del mes, 2 dígitos encabezados por ceros), j (día del mes no encabezado por ceros), H (formato de 24 horas de una hora, encabezado por ceros), G (formato de 24 horas de una hora no encabezado por ceros), i (minutos encabezados por ceros), s (segundos encabezados por ceros)',
		'import_progress' => 'Importando',
		'prepare_progress' => 'Preparando',
		'finish_progress' => 'Terminado',
		'finish_import' => 'La importación fue exitosa!',
		'import_file' => 'Importación de archivos',
		'import_data' => 'Importación de data',
		'import_templates' => 'Template import', // TRANSLATE
		'template_import' => 'First Steps Wizard', // TRANSLATE
		'txt_template_import' => 'Import ready example templates and template sets from the webEdition server', // TRANSLATE
		'file_import' => 'Importando archivos locales',
		'txt_file_import' => 'Importar uno o más archivos desde su disco duro local.', // CHECK
// changed from: 'Import one or more files from the local harddrive.'
// changed to  : 'Import one or more files from the local hard drive.'

		'site_import' => 'Importar archivos desde el servidor',
		'site_import_isp' => 'Importando gráficos desde el servidor',
		'txt_site_import_isp' => 'Importando gráficos desde el directorio raíz del servidor. Configure las opciones del filtro para escoger cuales gráficos serán importados.',
		'txt_site_import' => 'Importar archivos desde el directorio raíz del servidor. Ajuste las opciones de filtro para escoger si los gráficos, las páginas HTML, Flash, JavaScript, archivos CSS, documentos de texto simple, u otro tipo de archivo serán importados.', // CHECK
// changed from: 'Import files from the root-directory of the server. Set filter options to choose if graphics, HTML pages, Flash, JavaScript, or CSS files, plain-text documents, or other types of files are to be imported.'
// changed to  : 'Import files from the root-directory of the server. Set filter options to choose if images, HTML pages, Flash, JavaScript, or CSS files, plain-text documents, or other types of files are to be imported.'

		'txt_wxml_import' => 'Los archivos XML de webEdition contienen información acerca de documentos, plantillas u objetos webEdition. Escoja un directorio al cual los archivos serán importados.',
		'txt_gxml_import' => 'Import "flat" XML files, such as those provided by phpMyAdmin. The dataset fields have to be allocated to the webEdition dataset fields. Use this to import XML files exported from webEdition without the export module.', // TRANSLATE
		'txt_csv_import' => 'Importar archivos CSV (Valores Separados por Comas) o formatos de texto modificados (por ejemplo *.txt). Los campos de conjuntos de datos son ubicados a los campos de webEdition.',
		'add_expat_support' => 'Para implementar el apoyo del programa analizador sintáctico del expat XML, Ud necesitará recopilar PHP para adicionar apoyo a esta librería para su forma PHP. La extensión expat , creada por James Clark, puede ser encontrada en http://www.jclark.com/xml/.',
		'xml_file' => 'Archivo XML',
		'templates' => 'Plantillas',
		'classes' => 'Clases',
		'predetermined_paths' => 'Ajustes de la ruta de acceso',
		'maintain_paths' => 'Mantener las rutas de acceso',
		'import_options' => 'Importar opciones',
		'file_collision' => 'Colisión de archivos',
		'collision_txt' => 'Cuando Ud importa un archivo a una carpeta que contiene un archivo con el mismo nombre, una colisión de nombres de archivo ocurre. Ud puede especificar como el asistente de importación debe manejar los archivos nuevos y existentes.',
		'replace' => 'Reemplazar',
		'replace_txt' => 'Borrar el archivo existente y reemplazarlo con un archivo nuevo.',
		'rename' => 'Renombrar',
		'rename_txt' => 'Asignar un nombre único al archivo nuevo. Todos los vínculos serán ajustados al nuevo nombre de archivo.',
		'skip' => 'Saltar',
		'skip_txt' => 'Saltar el archivo actual y dejar ambas copias en su ubicación original.',
		'extra_data' => 'Data extra',
		'integrated_data' => 'Importar data integrado ',
		'integrated_data_txt' => 'Seleccione esta opción para importar data integrado por plantillas y documentos.',
		'max_level' => 'a nivelar',
		'import_doctypes' => 'Importar tipos de documentos',
		'import_categories' => 'Importar categorías',
		'invalid_wxml' => 'El documento XML está bien formado pero no es valido. No se aplica a la definición de tipo de documento webEdition (DTD).',
		'valid_wxml' => 'El documento XML está bien formado y es valido. Se aplica a la definición de tipo de documento webEdition (DTD).',
		'specify_docs' => 'Por favor, escoja los documentos a importar.',
		'specify_objs' => 'Por favor, escoja los objetos a importar.',
		'specify_docs_objs' => 'Por favor, escoja si importar documentos y objetos.',
		'no_object_rights' => 'Ud no tiene autorización para importar objetos.',
		'display_validation' => 'Mostrar validación de XML',
		'xml_validation' => 'Validación de XML',
		'warning' => 'Advertencia',
		'attribute' => 'Atributo',
		'invalid_nodes' => 'Nódulo XML invalido en posición',
		'no_attrib_node' => 'No hay elemento XML "atributo" en posición',
		'invalid_attributes' => 'Atributos invalidos en posición',
		'attrs_incomplete' => 'La lista de atributos #requeridos y #reparados está incompleta en posición',
		'wrong_attribute' => 'El nombre del atributo no está definido como #requerido ni como #implícito en posición ',
		'documents' => 'Documentos',
		'objects' => 'Objetos',
		'fileselect_server' => 'Cargar archivo desde el servidor',
		'fileselect_local' => 'Cargar archivo desde su disco duro local',
		'filesize_local' => 'Por las restrinciones dentro de PHP, el archivo que UD desea cargar no puede exceder 0.999MB.',
		'xml_mime_type' => 'El archivo seleccionado no puede ser importado. Tipo MIME',
		'invalid_path' => 'La ruta de acceso del archivo original es invalido.',
		'ext_xml' => 'Por favor, seleccione un archivo original con la extensión ".xml".',
		'store_docs' => 'Documentos del directorio objetivo',
		'store_tpls' => 'Plantillas del directorio objetivo',
		'store_objs' => 'Objectos del directorio objetivo',
		'doctype' => 'Document type',
		'gxml' => 'XML genérico',
		'data_import' => 'Importar data',
		'documents' => 'Documentos',
		'objects' => 'Objetos',
		'type' => 'Tipo',
		'template' => 'Plantilla',
		'class' => 'Clase',
		'categories' => 'Categoría',
		'isDynamic' => 'Generar la página dinámicamente',
		'extension' => 'Extensión',
		'filetype' => 'Tipo de archivo',
		'directory' => 'Directorio',
		'select_data_set' => 'Seleccione conjunto de datos',
		'select_docType' => 'Por favor, escoja una plantilla.',
		'file_exists' => 'El archivo original seleccionado no existe. Por favor, chequear la ruta de acceso del archivo dado. Ruta: ',
		'file_readable' => 'El archivo original seleccionado es no leíble y por lo tanto no puede ser importado.',
		'asgn_rcd_flds' => 'Asignar campos de data',
		'we_flds' => 'Campos de webEdition',
		'rcd_flds' => 'Campos de conjunto de datos',
		'name' => 'Nombre',
		'auto' => 'Automático',
		'asgnd' => 'Asignado',
		'pfx' => 'Prefijo',
		'pfx_doc' => 'Documento',
		'pfx_obj' => 'Objeto',
		'rcd_fld' => 'Campo de conjunto de datos',
		'import_settings' => 'Importar ajustes',
		'xml_valid_1' => 'El archivo XML es valido y contiene',
		'xml_valid_s2' => 'elementos. Por favor, seleccione el elemento a importar.',
		'xml_valid_m2' => 'Nódulo hijo XML en el primer nivel con nombres diferentes. Por favor, escoja el nódulo XML y el número de elementos a importar.',
		'well_formed' => 'El documento XML está bien formado.',
		'not_well_formed' => 'El documento XML no está bien formado y no puede ser importado.',
		'missing_child_node' => 'El documento XML está bien formado, pero no contiene nódulos XML y por lo tanto puede no ser importada.',
		'select_elements' => 'Por favor, escoja los conjuntos de datos a importar.',
		'num_elements' => 'Por favor, escoja el múmero de conjuntos de datos desde 1 a ',
		'xml_invalid' => '', // TRANSLATE
		'option_select' => 'Selección..',
		'num_data_sets' => 'Conjunto de datos:',
		'to' => 'a',
		'assign_record_fields' => 'Asignar campos de data',
		'we_fields' => 'Campos webEdition',
		'record_fields' => 'Campos de conjuntos de datos',
		'record_field' => 'Campo de conjunto de datos ',
		'attributes' => 'Atributos',
		'settings' => 'Ajustes',
		'field_options' => 'Opciones de campo',
		'csv_file' => 'Archivo CSV',
		'csv_settings' => 'Ajustes de CSV',
		'xml_settings' => 'Ajustes de XML',
		'file_format' => 'Formato de archivo',
		'field_delimiter' => 'Separador',
		'comma' => ', {coma}',
		'semicolon' => '; {punto y coma}',
		'colon' => ': {dos puntos}',
		'tab' => "\\t {tabulador}",
		'space' => '  {área}',
		'text_delimiter' => 'Separador de texto',
		'double_quote' => '" {comillas}',
		'single_quote' => '\' {comilla}',
		'contains' => 'La primera línea contiene el nombre del campo',
		'split_xml' => 'Importación secuencial de conjuntos de datos',
		'wellformed_xml' => 'Validación para XML bien formados',
		'validate_xml' => 'Validación de XML',
		'select_csv_file' => 'Por favor, escoja un archivo original CSV.',
		'select_seperator' => 'Por favor, seleccione un separador.',
		'format_date' => 'Formato de fecha',
		'info_sdate' => 'Seleccione el formato de fecha para el campo webEdition',
		'info_mdate' => 'Seleccione el formato de fecha para los campos webEdition',
		'remark_csv' => 'Ud es capaz de importar archivos CSV (Valores Separados por Comas) o modificar formatos de texto  (por ejemplo *.txt). El delimitador de campo (por ejemplo , ; tabulador, área) y el delimitador de texto (= el cual encapsula las entradas de texto) pueden ser preajustados en la importación de estos formatos de archivo.',
		'remark_xml' => 'Para evitar la pausa predefinida de un PHP-script, seleccione la opción "Importar conjuntos de datos separadamente", para importar archvios extensos.<br>Si Ud está inseguro de si el archivo seleccionado es un XML de webEdition o no, el archivo puede ser comprobado por validez y sintaxis.',
		'import_docs' => "Import documents", // TRANSLATE
		'import_templ' => "Import templates", // TRANSLATE
		'import_objs' => "Import objects", // TRANSLATE
		'import_classes' => "Import classes", // TRANSLATE
		'import_doctypes' => "Import DocTypes", // TRANSLATE
		'import_cats' => "Import categorys",
		'documents_desc' => "Select the directory where the documents will be imported. If the option \"Mantener las rutas de acceso\" is checked, the documents paths will be restored, otherwise the documents paths will be ignored.", // TRANSLATE
		'templates_desc' => "Select the directory where the templates will be imported. If the option \"Mantener las rutas de acceso\" is checked, the template paths will be restored, otherwise the template paths will be ignored.", // TRANSLATE
		'handle_document_options' => 'Documentos',
		'handle_template_options' => 'Plantillas',
		'handle_object_options' => 'Objectos',
		'handle_class_options' => 'Clases',
		'handle_doctype_options' => "Tipos de documentos",
		'handle_category_options' => "Categoría",
		'log' => 'Detalles',
		'start_import' => 'Comenzar importación',
		'prepare' => 'Preparar...',
		'update_links' => 'Actualizar vínculos...',
		'doctype' => 'Tipos de documentos',
		'category' => 'Categoría',
		'end_import' => 'Importación terminada',
		'handle_owners_option' => 'Datos  propietarios',
		'txt_owners' => 'Importar datos propietarios vinculados.',
		'handle_owners' => 'Restaurar datos propietarios',
		'notexist_overwrite' => 'Si el usuario no existe, la opción "Sobrescribir datos propietarios" será aplicada',
		'owner_overwrite' => 'Sobrescribir datos propietarios',
		'name_collision' => 'Colisión de nombre',
		'item' => 'Artículo',
		'backup_file_found' => 'El archivo parece un archivo de copia webEdition. Por favor use la opción \"Reserva\" del menú \"Archivo\" para importar los datos.',
		'backup_file_found_question' => '¿Desea cerrar la ventana de diálogo actual y comenzar el asistente de copia?',
		'close' => 'Cerrar',
		'handle_file_options' => 'Archivos',
		'import_files' => 'Importar archivos',
		'weBinary' => 'Archivo',
		'format_unknown' => '¡El formato del archivo es desconocido!',
		'customer_import_file_found' => 'El archivo parece un archivo de importación con los datos del cliente. Por favor use la opción \"Import/Export\" del módulo cliente (PRO) para importar los datos.',
		'upload_failed' => 'El fichero no puede ser subido. Por favor verifique si el tamaño es más grande que %s',
		'import_navigation' => 'Import navigation', // TRANSLATE
		'weNavigation' => 'Navigation', // TRANSLATE
		'navigation_desc' => 'Select the directory where the navigation will be imported.', // TRANSLATE
		'weNavigationRule' => 'Navigation rule', // TRANSLATE
		'weThumbnail' => 'Thumbnail', // TRANSLATE
		'import_thumbnails' => 'Import thumbnails', // TRANSLATE
		'rebuild' => 'Rebuild', // TRANSLATE
		'rebuild_txt' => 'Automatic rebuild', // TRANSLATE
		'finished_success' => 'The import of the data has finished successfully.',
		'encoding_headline' => 'Charset', // TRANSLATE
		'encoding_noway' => 'A conversion  is only possible between ISO-8859-1 and UTF-8 <br/>and with a set default charset (settings dialog)', // TRANSLATE
		'encoding_change' => "Change, from '", // TRANSLATE
		'encoding_XML' => '', // TRANSLATE
		'encoding_to' => "' (XML file) to '", // TRANSLATE
		'encoding_default' => "' (standard)", // TRANSLATE
);