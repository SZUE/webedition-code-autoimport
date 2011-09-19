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
 * Language file: customer.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_customer = array(
		'menu_customer' => "Cliente",
		'menu_new' => "Nuevo",
		'menu_save' => "Salvar",
		'menu_delete' => "Borrar",
		'menu_exit' => "Finalizar",
		'menu_info' => "Info", // TRANSLATE
		'menu_help' => "Ayuda",
		'menu_admin' => "Administración",
		'save_changed_customer' => "Customer has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'customer_saved_ok' => "El cliente '%s' fue salvado exitosamente",
		'customer_saved_nok' => "El cliente '%s' no puede ser salvado!",
		'nothing_to_save' => "Nada para salvar!",
		'username_exists' => "El nombre de usuario '%s' ya existe!",
		'username_empty' => "El nombre de usuario no ha sido llenado!",
		'password_empty' => "La contraseña no ha sido llenada!",
		'customer_deleted' => "El cliente fue borrado exitosamente!",
		'nothing_to_delete' => "Nada para borrar!",
		'no_space' => "No está permitido usar espacios",
		'customer_data' => "Data del cliente",
		'first_name' => "Nombre",
		'second_name' => "Apellido",
		'username' => "Nombre de usuario",
		'password' => "Contraseña",
		'login' => "Login", // TRANSLATE
		'login_denied' => "Access denied", // TRANSLATE
		'autologin' => "Auto-Login", // TRANSLATE
		'autologin_request' => "requested", // TRANSLATE

		'permissions' => "Permisos",
		'password_alert' => "La contraseña debe tener al menos 4 carácteres",
		'delete_alert' => "Borrar todo el data del cliente?\\n Está UD seguro?",
		'created_by' => "Creado por",
		'changed_by' => "Cambiado por",
		'no_perms' => "UD no tiene permiso para usar esta opción!",
		'topic' => "Tópico",
		'not_nummer' => "Las letras iniciales no pueden ser números.",
		'field_not_empty' => "El campo del nombre debe ser llenado.",
		'delete_field' => "¿Está UD seguro que quiere borrar este campo? Este proceso no es reversible.",
		'display' => "Mostrar",
		'insert_field' => "Insertar campo",
//---- new things

		'customer' => "Cliente",
		'common' => "General", // TRANSLATE
		'all' => "Todo",
		'sort' => "Clasificar",
		'branch' => "Vista",
		'field_name' => "Nombre",
		'field_type' => "Tipo",
		'field_default' => "Predeterminado",
		'add_mail' => "Insertar E-Mail", // CHECK
// changed from: "Insert E-mail"
// changed to  : "Insert email"

		'edit_mail' => "Editar E-Mail", // CHECK
// changed from: "Edit E-mail"
// changed to  : "Edit email"


		'no_branch' => "Ninguna vista ha sido seleccionada!",
		'no_field' => "Ningún campo ha sido seleccionado!",
		'field_saved' => "El campo fue salvado",
		'field_deleted' => "El campo %s fue borrado de la vista %s ",
		'del_fild_question' => "Desea UD borrar este campo?",
		'field_admin' => "Administración de campos",
		'sort_admin' => "Administración de clasificación",
		'name' => "Nombre",
		'sort_branch' => "Vista",
		'sort_field' => "Campo",
		'sort_order' => "Orden",
		'sort_saved' => "La clasificación fue salvada",
		'sort_name' => "Clasificación",
		'sort_function' => "Función",
		'no_sort' => "--Sin clasificación--",
		'branch_select' => "Seleccionar vista",
		'fields' => "Campos",
		'add_sort_group' => "Insertar nuevo grupo",
		'search' => "Buscar",
		'search_for' => "Buscar en",
		'simple_search' => "Búsqueda simple",
		'advanced_search' => "Búsqueda avanzada",
		'search_result' => "Resultados",
		'no_value' => "[-Sin valor-]",
		'other' => "Otros",
		'cannot_save_property' => "El campo '%s' está protegido y no puede ser salvado!",
		'settings' => "Preferencias",
		'Username' => "Nombre de usuario",
		'Password' => "Contraseña",
		'Forname' => "Nombre",
		'Surname' => "Apellido",
		'MemeberSince' => "Miembro desde",
		'LastLogin' => "Última conexión",
		'LastAccess' => "Último acceso",
		'default_date_type' => "Formato de fecha predeterminado",
		'custom_date_format' => "Formato de fecha personalizado",
		'default_sort_view' => "Vista de clasificación predeterminada",
		'unix_ts' => "Marca horária Unix",
		'mysql_ts' => "Marca horária MySQL",
		'start_year' => "Iniciar Año",
		'settings_saved' => "Las preferencias fueron salvadas",
		'settings_not_saved' => "Las preferencias no fueron salvadas",
		'data' => "Data", // TRANSLATE

		'add_field' => "Adicionar campo",
		'edit_field' => "Editar campo",
		'edit_branche' => "Editar vista",
		'not_implemented' => "Sin implementar",
		'branch_no_edit' => "La vista está protegida y no puede ser cambiada",
		'name_exists' => "El nombre ya existe!",
		'import' => "Importar",
		'export' => "Exportar",
		'export_title' => "Asistente de Exportación",
		'import_title' => "Asistente de Importación",
		'export_step1' => "Exportar formato",
		'export_step2' => "Seleccionar cliente",
		'export_step3' => "Exportar data",
		'export_step4' => "Exportación finalizada",
		'import_step1' => "Importar formato",
		'import_step2' => "Import data", // TRANSLATE
		'import_step3' => "Seleccionar registro",
		'import_step4' => "Asignar campos de data",
		'import_step5' => "Export finished",
		'file_format' => "Formato de archivo",
		'export_to' => "Exportar a",
		'export_to_server' => "Servidor",
		'export_to_ftp' => "FTP", // TRANSLATE
		'export_to_local' => "Local", // TRANSLATE

		'ftp_host' => "Host", // TRANSLATE
		'ftp_username' => "Nombre de usuario",
		'ftp_password' => "Contraseña",
		'filename' => "Nombre de archivo",
		'path' => "Ruta",
		'xml_format' => "XML", // TRANSLATE
		'csv_format' => "CSV", // TRANSLATE

		'csv_delimiter' => "Delimtador",
		'csv_enclose' => "Adjuntar",
		'csv_escape' => "Escape", // TRANSLATE
		'csv_lineend' => "Final de línea",
		'import_charset' => "Import charset", // TRANSLATE //
		'csv_null' => "Reemplazo Nulo",
		'csv_fieldnames' => "La primera fila contiene los nombres de campo",
		'generic_export' => "Exportación genérica",
		'gxml_export' => "Exportación genérica XML",
		'txt_gxml_export' => "Exportar a \"flat\" archivos XML, cuando ellos son generados, por ejemplo, por phpMyAdmin. Los campos de registro serán cartografiados a los campos de webEdition.",
		'csv_export' => "Exportar CSV",
		'txt_csv_export' => "Exportar a archivos CSV (Valores Separados por Comas) u otros formatos de texto seleccionados (z. B. *.txt). Los campos de registro serán cartografiado a los campos de webEdition.",
		'csv_params' => "Preferencias del data de CSV",
		'filter_selection' => "Selección por filtros",
		'manual_selection' => "Selección manual",
		'sortname_empty' => "El nombre de clasificación está vacío!",
		'fieldname_exists' => "El nombre de campo ya existe!",
		'treetext_format' => "Formato de texto del menú",
		'we_filename_notValid' => "El nombre de usuario no es válido!\\nThe sign / is forbidden.",//TRANSLATE
		'windows' => "Formato Windows",
		'unix' => "Formato UNIX",
		'mac' => "Formato Mac",
		'comma' => ", {Coma}",
		'semicolon' => "; {Punto y coma}",
		'colon' => ": {Dos puntos}",
		'tab' => "\\t {Tabulador}",
		'space' => "  {espacio}",
		'double_quote' => "\" {Comillas}",
		'single_quote' => "' {Comilla simple}",
		'exporting' => "Exportando...",
		'cdata' => "Codificando",
		'export_xml_cdata' => "Adicionar secciones CDATA",
		'export_xml_entities' => "Reemplazar entidades",
		'export_finished' => "La exportación ha finalizado.",
		'server_finished' => "El archivo exportado ha sido salvado en el servidor",
		'download_starting' => "La descarga del archivo exportado se ha iniciado.<br><br>Si la descarga no se inicia en 10 segundos,<br>",
		'download' => "por favor, hacer clic aquí.",
		'download_failed' => "El archivo solicitado o no existe o UD no tiene ningún permiso para descargarlo",
		'generic_import' => "Importación genérica",
		'gxml_import' => "Importación genérica XML",
		'txt_gxml_import' => "Importar \"flat\" archivos XML, como aquellos suministrados por phpMyAdmin. Los campos de registro deben ser destinados a los campos de registro de los clientes..",
		'csv_import' => "Importar CSV",
		'txt_csv_import' => "Importar archivos CSV (Valores Separados por Comas) o formatos de texto modificados (z. B. *.txt).Los campos de registro son asignados los campos de clientes..",
		'source_file' => "Archivo original",
		'server_import' => "Import file from server", // TRANSLATE
		'upload_import' => "Import file from the local harddrive.", // TRANSLATE
		'file_uploaded' => "El archivo fue cargado.",
		'num_data_sets' => "Registros:",
		'to' => "a",
		'well_formed' => "El documento XML está bien formado.",
		'select_elements' => "Por favor, seleccione los registros a importar,.",
		'xml_valid_1' => "El archivo XML es válido y contiene",
		'xml_valid_m2' => "Nódulo XML en el primer nivel con nombres diferentes. Por favor, escoja el nódulo XML y el número de elementos a importar.",
		'not_well_formed' => "El documento XML no está bien formado y no puede importarse.",
		'missing_child_node' => "El documento de XML está bien formado, sin embargo no contiene nódulos XML y por consiguiente no puede importarse.",
		'none' => "-- ningúno --",
		'any' => "-- ninguno --",
		'we_flds' => "Campos de webEdition",
		'rcd_flds' => "Campos de Registro",
		'attributes' => "Atributo",
		'we_title' => "Titulo",
		'we_description' => "Descripción",
		'we_keywords' => "Palabras claves",
		'pfx' => "Prefijo",
		'pfx_doc' => "Documento",
		'pfx_obj' => "Objeto",
		'rcd_fld' => "Campo de registro",
		'auto' => "Automático",
		'asgnd' => "Asignado",
		'remark_csv' => "Ud puede importar archivos CSV (Valores Separados por Comas) o formatos de texto modificados (z. B. *.txt). El delimitador de campo (z. B. , ; tabulador, espacio) y el delimitador de texto (= el cual encapsula las entradas de texto) pueden ser preajustadas en la importación de estos formatos de archivos.",
		'remark_xml' => "Para evitar la pausa predefinida de un script PHP, seleccione la opción\"Importar registros por separado\",para importar archivos extensos .<br>Si Ud no está seguro de que el archivo seleccionado sea un archivo XML de webEdition o no, este puede ser comprobado por validez y sintaxis.",
		'record_field' => "Campo de registro",
		'missing_filesource' => "¡Ningún archivo original fue seleccionado! Por favor seleccione un archivo original.",
		'importing' => "Importando",
		'same_names' => "Nombres iguales",
		'same_rename' => "Renombrar",
		'same_overwrite' => "Sobrescribir",
		'same_skip' => "Saltar",
		'rename_customer' => "El cliente '%s' fue renombrado como '%s'",
		'overwrite_customer' => "El cliente '%s' fue sobrescrito",
		'skip_customer' => "El cliente '%s' fue saltado",
		'import_finished_desc' => "%s nuevos clientes fueron importados!",
		'show_log' => "Avisos",
		'import_step5' => "Importación finalizada",
		'view' => "Vista",
		'registered_user' => "Usuario registrado",
		'unregistered_user' => "Usuario no registrado",
		'default_soting_no_del' => "The sort is used in settings and must not be deleted!", // TRANSLATE
		'we_fieldname_notValid' => "Nombre de archivo inválido!\\nLos espacios en blanco no son carácteres válidos.",
		'orderTab' => 'Órdenes de este cliente',
		'default_order' => 'Orden previa',
		'ASC' => 'ascending', // TRANSLATE
		'DESC' => 'descending', // TRANSLATE

		'connected_with_customer' => "Connected with customer", // TRANSLATE
		'one_customer' => "Customer", // TRANSLATE

		'sort_edit_fields_explain' => "If a field is apparently not moving, it moves along fields in other branches, not visible here", // TRANSLATE
		'objectTab' => 'Objects of this customer',
		'documentTab' => 'Documents of this customer',
		'NoDocuments' => 'The customer has no documents',
		'NoObjects' => 'The customer has no objects',
		'ID' => 'ID',
		'Filename' => 'Filename',
		'Aenderungsdatum' => 'Modification date',
		'Titel' => 'Title',
);
