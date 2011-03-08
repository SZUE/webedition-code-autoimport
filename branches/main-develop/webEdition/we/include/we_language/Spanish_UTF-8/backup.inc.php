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
$l_backup = array(
		'save_not_checked' => "Ud no ha escogido donde salvar el archivo de reserva!",
		'wizard_title' => "Asistente de Importación de Reserva",
		'wizard_title_export' => "Asistente de Exportación de Reserva",
		'save_before' => "Durante la importación todo el data existente será borrado! Es recomendable que UD salve su data existente primero.",
		'save_question' => "Desea Ud salvar el data existente?",
		'step1' => "Paso 1/4 - Salvar data existente",
		'step2' => "Paso 2/4 - Seleccionar fuente de importación",
		'step3' => "Paso 3/4 - Importar data salvado",
		'step4' => "Paso 4/4 - Restauración terminada",
		'extern' => "Restaurar archivos y carpetas webEdition externos",
		'settings' => "Restaurar preferencias",
		'rebuild' => "Reconstrucción automática",
		'select_upload_file' => "Cargar importación desde archivo local",
		'select_server_file' => "Escoja en esta lista el archivo de reserva que Ud desea importar.",
		'charset_warning' => "If you encounter problems when restoring a backup, please ensure that the <strong>target system uses the same character set as the source system</strong>. This applies both to the character set of the database (collation) as well as for the character set of the user interface language!", // TRANSLATE
		'defaultcharset_warning' => '<span style="color:ff0000">Attention! The standard charset is not defined.</span> For some server configurations, this can lead to problems while importing backups.!', // TRANSLATE
		'finished_success' => "La importación del data de reserva ha finalizado exitosamente.",
		'finished_fail' => "La importación del data de reserva no ha finalizado exitosamente",
		'question_taketime' => "La exportación puede tomar algún tiempo.",
		'question_wait' => "Por favor, espere!",
		'export_title' => "Exportar",
		'finished' => "Finalizado",
		'extern_files_size' => "Dado que el tamaño máximo del archivo es limitado a %.1f MB (%s byte) por lös ajustes de su base de datos, multiples archivos deben ser creados.",
		'extern_files_question' => "Salvar archivos y carpetas webEdition externos",
		'export_location' => "Specify where you want to save the backup file. If it is stored on the server, you find the file in '/webEdition/we_backup/data/'.", // TRANSLATE
		'export_location_server' => "En el servidor",
		'export_location_send' => "En el disco duro local",
		'can_not_open_file' => "Incapaz de abrir el archivo '%s'.",
		'too_big_file' => "El archivo '%s' no puede ser escrito ya que el tamaño excede el tamaño máximo de archivo.",
		'cannot_save_tmpfile' => "Incapaz de crear archivo temporal. Chequear si Ud tiene permisos de escritura sobre %s",
		'cannot_save_backup' => "Incapaz de salvar archivo de reserva.",
		'cannot_send_backup' => "Incapaz de ejecutar la reserva.",
		'finish' => "The backup was successfully created.", // TRANSLATE
		'finish_error' => " Error: Incapaz de ejecutar la reserva.",
		'finish_warning' => "Advertencia: Reserva completada, sin embargo, algunos archivos pueden no estar completados!",
		'export_step1' => "Paso 1 de 2 - Parámetros de exportación",
		'export_step2' => "Paso 2 de 2 - Exportación completada",
		'unspecified_error' => "Un error desconocido ha ocurrido!",
		'export_users_data' => "Salvar data del usuario",
		'import_users_data' => "Restaurar data del usuario",
		'import_from_server' => "Restaurando data desde el servidor",
		'import_from_local' => "Restaurando desde un archivo local",
		'backup_form' => "Reserva desde",
		'nothing_selected' => "Nada seleccionado!",
		'query_is_too_big' => "La reserva contiene un archivo el cual no podría ser restaurado al exceder el limite de %s bytes!",
		'show_all' => "Show all files", // TRANSLATE
		'import_customer_data' => "Restaurar data del cliente",
		'import_shop_data' => "Restaurar data de compra",
		'export_customer_data' => "Salvar data del cliente",
		'export_shop_data' => "Salvar data de compra",
		'working' => "Trabajando...",
		'preparing_file' => "Preparando archivo para importar...",
		'external_backup' => "Salvando data externo...",
		'import_content' => "Importando contenido",
		'import_files' => "Importando archivos",
		'import_doctypes' => "Restaurar tipos de documentos",
		'import_user_data' => "Restaurar data del usuario",
		'import_templates' => "Importando plantillas",
		'export_content' => "Exportando contenido",
		'export_files' => "Exportando archivos",
		'export_doctypes' => "Salvar tipo de documento",
		'export_user_data' => "Salvar data del usuario",
		'export_templates' => "Exportando plantillas",
		'download_starting' => "La descarga del archivo de reserva ha sido iniciada.<br><br>Si la descarga no se inicia después de 10 segundos,<br>",
		'download' => "Por favor, hacer clic aquí.",
		'download_failed' => "El archivo que Ud solicitó no existe o no le está permitido descargarlo.",
		'extern_backup_question_exp' => "Ud seleccionó la opción 'Salvar archivos y carpetas webEdition externos'. Esta opción podría tomar algún tiempo y puede conducir a algunos errores específicos del sistema. Desea Ud proceder de todas formas?",
		'extern_backup_question_exp_all' => "Ud seleccionó la opción 'Chequear todos'. Eso también chequea la opción 'Salvar archivos y carpetas webEdition externos'. Esta opción podría tomar tiempo y puede conducir a algunos errores específicos del sistema. <br><br>Desea Ud permitir que 'Salvar archivos y carpetas webEdition externos' sea chequeada de todas formas?",
		'extern_backup_question_imp' => "Ud seleccionó la opción 'Restaurar archivos y carpetas webEdition externos'. Esta opción podría tomar tiempo y puede conducir a algunos errores específicos del sistema. Desea Ud proceder de todas formas?",
		'extern_backup_question_imp_all' => "Ud seleccionó la opción 'Chequear todos'. Eso también chequea la opción 'Restaurar archivos y carpetas webEdition externos'. Esta opción podría tomar tiempo y puede conducir a algunos errores específicos del sistema. <br><br>Desea Ud permitir que 'Restaurar archivos y carpetas webEdition externos' sea chequeado de todas formas?",
		'nothing_selected_fromlist' => "Escoja en la lista el archivo de reserva que Ud desea importar para proceder!",
		'export_workflow_data' => "Salvar data del flujo de trabajo",
		'export_todo_data' => "Salvar data de tarea\mensajes",
		'import_workflow_data' => "Restaurar data del flujo de trabajo",
		'import_todo_data' => "Restaurar data de tarea\mensaje",
		'import_check_all' => "Chequear todo",
		'export_check_all' => "Chequear todo",
		'import_shop_dep' => "Ud ha seleccionado la opción 'Restaurar data de compra'. El Módulo Compras necesita el data del cliente y por eso, 'Restaurar data del cliente' ha sido automáticamente seleccionado.",
		'export_shop_dep' => "Ud ha seleccionado la opción 'Salvar data de compra'. El Módulo Compras necesita el data del cliente y por eso, 'Salvar data del cliente' ha sido automáticamente seleccionado.",
		'import_workflow_dep' => "Ud ha seleccionado la opción 'Restaurar data del flujo de trabajo'. El Módulo Flujo de Trabajo necesita los documentos y el data del usuario y por eso, 'Restaurar documentos y plantillas' y 'Restaurar data del usuario' han sido automáticamente seleccionados.",
		'export_workflow_dep' => "Ud ha seleccionado la opción 'Salvar data del flujo de trabajo'. El Módulo Flujo de Trabajo necesita los documentos y el data del usuario y por eso, 'Salvar documentos y plantillas' y 'Salvar data del flujo de trabajo' han sido automáticamente seleccionado.",
		'import_todo_dep' => "Ud ha seleccionado la opción 'Restaurar data de tarea\mensaje'. El Módulo Tarea\Mensaje necesita el data del usuario y por eso, 'Restaurar data del usuario' ha sido automáticamente seleccionado.",
		'export_todo_dep' => "Ud ha seleccionado la opción 'Salvar data de tarea\mensaje'. El Módulo Tarea\Mensaje necesita el data del usuario y por eso, 'Salvar data del usuario' ha sido automáticamente seleccionado.",
		'export_newsletter_data' => "Salvar data del boletín informativo",
		'import_newsletter_data' => "Restaurar data del boletín informativo",
		'export_newsletter_dep' => "Ud ha seleccionado la opción 'Salvar data del boletín informativo'. El Módulo Hoja Informativa necesita los documentos y el data del usuario y por eso, 'Salvar documentos y plantillas' y 'Salvar data del cliente' ha sido automáticamente seleccionado.",
		'import_newsletter_dep' => "Ud ha seleccionado la opción 'Restaurar data del boletín informativo'. El Módulo Hoja Informativa necesita los documentos y el data del usuario y por eso, 'Restaurar documentos y plantillas' y 'Restaurar data del cliente' ha sido automáticamente seleccionado.",
		'warning' => "Advertencia",
		'error' => "Error", // TRANSLATE
		'export_temporary_data' => "Salvar data temporal",
		'import_temporary_data' => "Restaurar data temporal",
		'export_banner_data' => "Salvar data de la pancarta",
		'import_banner_data' => "Restaurar data de la pancarta",
		'export_prefs' => "Preferencias al salvar",
		'import_prefs' => "Preferencias al restaurar",
		'export_links' => "Salvar vínculos",
		'import_links' => "Restaurar vínculos",
		'export_indexes' => "Salvar índices",
		'import_indexes' => "Restaurar índices",
		'filename' => "Nombre de archivo",
		'compress' => "Comprimir",
		'decompress' => "Descomprimir",
		'option' => "Opciones de reserva",
		'filename_compression' => "Aqui puede Ud entrar un nombre para el archivo destino de reserva y habilitar la compresión. El archivo será comprimido usando la compresión gzip y el archivo resultante tendrá la extensión .gz. Esta acción puede tomar unos minutos.<br>Si la reserva no fue exitosa, por favor, trate de cambiar los ajustes.",
		'export_core_data' => "Salvar documentos y plantillas",
		'import_core_data' => "Restaurar documentos y plantillas",
		'export_object_data' => "Salvar objetos and clases",
		'import_object_data' => "Restaurar objetos and clases",
		'export_binary_data' => "Salvar data binario",
		'import_binary_data' => "Restaurar data binario",
		'export_schedule_data' => "Salvar data del planificador",
		'import_schedule_data' => "Restaurar data del planificador",
		'export_settings_data' => "Salvar ajustes",
		'import_settings_data' => "Restaurar ajustes",
		'export_extern_data' => "Salvar archivo/carpetas externos",
		'import_extern_data' => "Restaurar archivo/carpetas externos",
		'export_binary_dep' => "Ud ha seleccionado la opción 'Salvar data binario'. El data binario necesita los documentos y por eso, 'Salvar documentos y plantillas' ha sido automáticamente seleccionado.",
		'import_binary_dep' => "Ud ha seleccionado la opción 'Restaurar data binario'. El data binario necesita el data de los documentos y por eso, 'Restaurar documentos y plantillas' ha sido automáticamente seleccionado.",
		'export_schedule_dep' => "Ud ha seleccionado la opción 'Salvar data del planificador'. El Módulo Planificador necesita los documentos y por eso, 'Salvar documentos y plantillas' ha sido automáticamente seleccionado.",
		'import_schedule_dep' => "Ud ha seleccionado la opción 'Restaurar data del planificador'. El Módulo Planificador necesita el data de los documentos y por eso, 'Restaurar documentos y plantillas' ha sido automáticamente seleccionado.",
		'export_temporary_dep' => "Ud ha seleccionado la opción 'Salvar data temporal'. El data temporal necesita los documentos y por eso, 'Salvar documentos y plantillas' ha sido automáticamente seleccionado.",
		'import_temporary_dep' => "Ud ha seleccionado la opción 'Restaurar data temporal'. El data temporal necesita el data de los documentos y por eso, 'Restaurar documentos y plantillas' ha sido automáticamente seleccionado.",
		'compress_file' => "Comprimir archivo",
		'export_options' => "Seleccionar el data que debe ser salvado.",
		'import_options' => "Seleccionar el data que debe ser restaurado.",
		'extern_exp' => "Esta opción puede tomar algún tiempo y conducir a errores especificos del sistema.",
		'unselect_dep2' => "Ud ha cancelado la selección de '%s'. Las opciones que le siguén serán automáticamente canceladas.",
		'unselect_dep3' => "Esta opción puede ser seleccionada nuevamente.",
		'gzip' => "gzip", // TRANSLATE
		'zip' => "zip", // TRANSLATE
		'bzip' => "bzip", // TRANSLATE
		'none' => "ninguno",
		'cannot_split_file' => "El archivo '%s' no se puede preparar para ser restaurado!",
		'cannot_split_file_ziped' => "El archivo ha sido comprimido con métodos de compresión infundados.",
		'export_banner_dep' => "Ud ha seleccionado la opción 'Salvar data de la pancarta'. El data data de la pancarta necesita los documentos y por eso, 'Salvar documentos y plantillas' ha sido automáticamente seleccionado.",
		'import_banner_dep' => "Ud ha seleccionado la opción 'Restaurar data de la pancarta'. El data data de la pancarta necesita los documentos y por eso, 'Salvar documentos y plantillas' ha sido automáticamente seleccionado.",
		'delold_notice' => "Es recomendable que Ud borre los archivos viejos en su servidor para tener mas espacio libre.<br>Desea Ud continuar?",
		'delold_confirm' => "Todo el data existente será borrado!\\nEstá Ud seguro?",
		'delete_entry' => "Borrar %s",
		'delete_nok' => "Los archivos no pueden ser borrados!",
		'nothing_to_delete' => "No hay nada para borrar!",
		'files_not_deleted' => "Uno o mas archivos no pueden ser borrados! Es posible que estén protegidos contra escritura. Borre los archivos manualmente. Los siguientes archivos son afectados:",
		'delete_old_files' => "Delete old files...", // TRANSLATE

		'export_configuration_data' => "Save configuration", // TRANSLATE
		'import_configuration_data' => "Restore configuration", // TRANSLATE

		'import_export_data' => "Restaurar datos de exportación",
		'export_export_data' => "Guardar datos de exportación",
		'export_versions_data' => "Save version data", // TRANSLATE
		'export_versions_binarys_data' => "Save Version-Binary-Files", // TRANSLATE
		'import_versions_data' => "Restore version data", // TRANSLATE
		'import_versions_binarys_data' => "Restore Version-Binary-Files", // TRANSLATE

		'export_versions_dep' => "You have selected the option 'Save version data'. The version data need the documents, objects and version-binary-files and because of that, 'Save documents and templates', 'Save object and classes' and 'Save Version-Binary-Files' has been automatically selected.", // TRANSLATE
		'import_versions_dep' => "You have selected the option 'Restore version data'. The version data need the documents data, object data an version-binary-files and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore Version-Binary-Files' has been automatically selected.", // TRANSLATE

		'export_versions_binarys_dep' => "You have selected the option 'Save Version-Binary-Files'. The Version-Binary-Files need the documents, objects and version data and because of that, 'Save documents and templates', 'Save object and classes' and 'Save version data' has been automatically selected.", // TRANSLATE
		'import_versions_binarys_dep' => "You have selected the option 'Restore Version-Binary-Files'. The Version-Binary-Files need the documents data, object data an version data and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore version data' has been automatically selected.", // TRANSLATE

		'del_backup_confirm' => "¿Desea eliminar la copia de seguridad seleccionada?",
		'name_notok' => "¡El nombre del fichero no es correcto!",
		'backup_deleted' => "La copia de seguridad %s ha sido eliminada",
		'error_delete' => "¡La copia de seguridad no puede ser eliminada! Intente eliminarla en la carpeta /webEdition/we_backup del servidor FTP.",
		'core_info' => 'Todos los documentos y plantillas.',
		'object_info' => 'Objetos y Clases del Módulo de Base Datos/Objetos.',
		'binary_info' => 'Datos Binarios - Imágenes, PDFs y otros documentos.',
		'user_info' => 'Datos de usuarios y cuentas del Módulo Gestión de Cliente.',
		'customer_info' => 'Datos de clientes y cuentas del Módulo Gestión de Cliente.',
		'shop_info' => 'Pedidos del Módulo Compras.',
		'workflow_info' => 'Datos del Módulo Flujo de Trabajo.',
		'todo_info' => 'Mensajes y tareas del Módulo Tareas/Mensajería.',
		'newsletter_info' => 'Datos del Módulo Boletín Informativo.',
		'banner_info' => 'Banner y estadístiscas del Módulo Pancarta/Estadísticas.',
		'schedule_info' => 'Datos programados del Módulo Planificador.',
		'settings_info' => 'Configuraciones de la aplicación webEdition.',
		'temporary_info' => 'Datos de documentos y objetos no publicados.',
		'export_info' => 'Datos del Módulo Exportación.',
		'glossary_info' => 'Data from the glossary.', // TRANSLATE
		'versions_info' => 'Data from Versioning.', // TRANSLATE
		'versions_binarys_info' => 'This option could take some time and memory because the folder /webEdition/we/versions/ could be very large. It is recommended to save this folder manually.', // TRANSLATE


		'import_voting_data' => "Restaurar datos de la votación",
		'export_voting_data' => "Salvar datos de la votación",
		'voting_info' => 'Datos del módulo de votación.',
		'we_backups' => 'Copias de seguridad de webEdition',
		'other_files' => 'Otros ficheros',
		'filename_info' => 'Entre el nombre del archivo de copia.',
		'backup_log_exp' => 'El diario sera guardado en /webEdition/we_backup/data/lastlog.php',
		'export_backup_log' => 'Crear diario',
		'download_file' => 'Descargar Archivo',
		'import_file_found' => 'El archivo parece un archivo de importación de webEdition. Por favor use la opción \"Importación/Exportación\" del menú \"Archivo\" para importar los datos.',
		'customer_import_file_found' => 'El archivo parece un archivo de importación con datos del cliente. Por favor use la opción \"Importación/Exportación\" del módulo Gestión de Cliente (PRO) para importar los datos.',
		'import_file_found_question' => '¿Desea cerrar ahora la ventana de diálogo actual y comenzar el asistente de importación/exportación?',
		'format_unknown' => '¡El formato del archivo es desconocido!',
		'upload_failed' => 'El archivo no puede ser subido. Por favor verifique si el tamaño del archivo es más grande que %s',
		'file_missing' => '¡El archivo de copia no se encuentra!',
		'recover_option' => 'Opciones de importación',
		'no_resource' => 'Fatal Error: There are not enough resources to finish the backup!', // TRANSLATE
		'error_compressing_backup' => 'An error occured while compressing the backup, so the backup could not be finished!', // TRANSLATE
		'error_timeout' => 'An timeout occured while creating the backup, so the backup could not be finished!', // TRANSLATE

		'export_spellchecker_data' => "Save spellchecker data", // TRANSLATE
		'import_spellchecker_data' => "Restore spellchecker data", // TRANSLATE
		'spellchecker_info' => 'Data for spellchecker: settings, general and personal dictionaries.', // TRANSLATE

		'import_banner_data' => "Restaurar data de la pancarta",
		'export_banner_data' => "Salvar data de la pancarta",
		'export_glossary_data' => "Save glossary data", // TRANSLATE
		'import_glossary_data' => "Restore glossary data", // TRANSLATE

		'protect' => "Protect backup file", // TRANSLATE
		'protect_txt' => "The backup file will be protected from unprivileged download with additional php code. This protection requires additional disk space for import!", // TRANSLATE

		'recover_backup_unsaved_changes' => "Some open files have unsaved changes. Please check these before you continue.", // TRANSLATE
		'file_not_readable' => "The backup file is not readable. Please check the file permissions.", // TRANSLATE

		'tools_import_desc' => "Here you can restore webEdition tools data. Please select the desired tools from the list.", // TRANSLATE
		'tools_export_desc' => "Here you can save webEdition tools data. Please select the desired tools from the list.", // TRANSLATE

		'ftp_hint' => "Attention! Use the Binary mode for the download by FTP if the backup file is zip compressed! A download in ASCII 	mode destroys the file, so that it cannot be recovered!", // TRANSLATE

		'convert_charset' => "Attention! Using this option in an existing site can lead to total loss of all data, please follow the instruction in http://documentation.webedition.org/de/webedition/administration/charset-conversion-of-legacy-sites", // TRANSLATE

		'convert_charset_data' => "While importing the backup, convert the site from ISO to UTF-8", // TRANSLATE
		'view_log' => "Backup-Log", // TRANSLATE
		'view_log_not_found' => "The backup log file was not found! ", // TRANSLATE
		'view_log_no_perm' => "You do not have the needed permissions to view the backup log file! ", // TRANSLATE
);