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
 * Language file: alert.inc.php
 * Provides language strings.
 * Language: English
 */
$l_alert = array(
		FILE_TABLE => array(
				'in_wf_warning' => "El documento debe ser salvado antes de poder ser colocado en el flujo de trabajo!\\Desea UD salvar el documento ahora?",
				'not_im_ws' => "El archivo no está situado dentro de su área de trabajo!",
		),
		TEMPLATES_TABLE => array(
				'in_wf_warning' => "La plantilla debe ser salvada antes de poder ser colocada en el flujo de trabajo!\\Desea UD salvar la plantilla ahora?",
				'not_im_ws' => "La plantilla no está situado dentro de su área de trabajo!",
		),
		'folder' => array(
				'not_im_ws' => "La carpeta no está situado dentro de su área de trabajo!",
		),
		'nonew' => array(
				'objectFile' => "Ud no puede crear nuevos objetos!<br>O Ud no tiene permiso o no hay una clase donde una de sus áreas de trabajo sea válida!",
		),
		'wrong_file' => array(
				'image/*' => "El archivo no pudo ser guardado. Este archivo no es una imagen o su espacio web está agotado!",
				'application/x-shockwave-flash' => "El archivo no pudo ser guardado. Este archivo no es una película Flash o no hay espacio suficiente en su disco duro!",
				'video/quicktime' => "El archivo no pudo ser guardado. Este archivo no es una película Quicktime o no hay espacio suficiente en su disco duro!",
				'text/css' => "The file could not be stored. Either it is not a CSS file or your disk space is exhausted!", // TRANSLATE
		),
		'no_views' => array(
				'headline' => '¡Atención!',
				'description' => 'No hay vista disponible para este documento.',
		),
		'navigation' => array(
				'last_document' => 'Ud edita el último documento.',
				'first_document' => 'Ud edita el primer documento.',
				'doc_not_found' => 'No se puede encontrar documento concordante.',
				'no_entry' => 'Ninguna entrada se encontro en la historia.',
				'no_open_document' => 'There is no open document.', // TRANSLATE
		),
		'delete_single' => array(
				'confirm_delete' => 'Borrar este documento?',
				'no_delete' => 'Este documento no puede ser borrado.',
				'return_to_start' => 'El documento fue borrado. \\nAtras al documento de inicio de seeMode.',
		),
		'move_single' => array(
				'return_to_start' => 'The document was moved. \\nBack to seeMode startdocument.', // TRANSLATE
				'no_delete' => 'This document could not be moved', // TRANSLATE
		),
		'notice' => "Notice",
		'warning' => "Warning", // TRANSLATE
		'error' => "Error", // TRANSLATE

		'noRightsToDelete' => "\\'%s\\' cannot be deleted! You do not have permission to perform this action!", // TRANSLATE
		'noRightsToMove' => "\\'%s\\' cannot be moved! You do not have permission to perform this action!", // TRANSLATE
		'delete_recipient' => "UD realmente desea borrar la dirección de E-mail seleccionada?",
		'recipient_exists' => "Esa dirección de E-mail ya existe!",
		'input_name' => "Entre una nueva dirección de E-mail!",
		'input_file_name' => "Enter a filename.", // TRANSLATE
		'max_name_recipient' => "La dirección de E-mail solo debe tener hasta 255 caracteres!",
		'not_entered_recipient' => "Ninguna dirección de E-mail ha sido entrada!",
		'recipient_new_name' => "Cambiar dirección de E-mail!",
		'required_field_alert' => "El campo '%s' es requerido y tiene que ser llenado!",
		'phpError' => "webEdition no se puede iniciar!",
		'3timesLoginError' => "Conexión al sistema fallida '%s' veces! Por favor, espere '%s' minutos e intentelo nuevamente!",
		'popupLoginError' => "La ventana de webEdition no se puede abrir\\n\\nwebEdition puede ser iniciado solamente cuando su navegador no bloquea las ventanas pop-up.",
		'publish_when_not_saved_message' => "El documento aún no ha sido salvado! Desea Ud publicarlo de todas formas?",
		'template_in_use' => "La plantilla está siendo usada y no se puede remover!",
		'no_cookies' => "UD no tiene cookies activados. Por favor, activar los cookies en su navegador!",
		'doctype_hochkomma' => "Nombre inválido!Los carácteres inválidos son ' (apostrofe) y , (coma)!",
		'thumbnail_hochkomma' => "Nombre inválido! Los carácteres inválidos son ' (apostrofe) y , (coma)!",
		'can_not_open_file' => "El archivo '%s' no pudo ser abierto!",
		'no_perms_title' => "Permiso denegado!",
		'no_perms_action' => "You don't have the permission to perform this action.", // TRANSLATE
		'access_denied' => "Acceso denegado!",
		'no_perms' => "Por favor, contacte al dueño (%s) o a un administrador<br>si necesita acceso!",
		'temporaere_no_access' => "Acceso no posible!",
		'temporaere_no_access_text' => "El archivo '%s' está siendo editado por '%s' en este momento.",
		'file_locked_footer' => "This document is edited by \"%s\" at the moment.", // TRANSLATE
		'file_no_save_footer' => "UD no tiene los permisos para salvar este archivo.",
		'login_failed' => "Nombre de usuario y/o contraseña incorrectos!",
		'login_failed_security' => "webEdition no pudo ser iniciado!\\n\\nEl proceso de conexión al sistema fue abortado por razones de seguridad, porque el tiempo maximo para conectarse a webEdition ha sido excedido!\\n\\nPor favor, conectarse nuevamente.",
		'perms_no_permissions' => "A Ud no le está permitido ejecutar esta acción!",
		'no_image' => "El archivo que Ud ha seleccionado no es una imagen!",
		'delete_ok' => "Archivos o directorios exitosamente borrados!",
		'delete_cache_ok' => "Cache successfully deleted!", // TRANSLATE
		'nothing_to_delete' => "No hay nada marcado para ser borrado!",
		'delete' => "Borrar las entradas seleccionadas?\\nDesea Ud continuar?",
		'delete_cache' => "Delete cache for the selected entries?\\nDo you want to continue?", // TRANSLATE
		'delete_folder' => "Borrar el directorio seleccionado?\\nPor favor, note que: Cuando se borra un directorio, todos los  documentos y directorios dentro del mismo son automáticamente borrados!\\nDesea UD continuar?",
		'delete_nok_error' => "El archivo '%s' no puede ser borrado.",
		'delete_nok_file' => "El archivo '%s' no puede ser borrado.\\nEs posible que esté protegido contra escritura.",
		'delete_nok_folder' => "El directorio '%s' no puede ser borrado.\\nEs posible que esté protegido contra escritura.",
		'delete_nok_noexist' => "El archivo '%s' no existe!",
		'noResourceTitle' => "No Item!", // TRANSLATE
		'noResource' => "The document or directory does not exist!", // TRANSLATE
		'move_exit_open_docs_question' => "Before moving all %s must be closed.\\nIf you continue, the following %s will be closed, unsaved changes will not be saved.\\n\\n", // TRANSLATE
		'move_exit_open_docs_continue' => 'Continue?', // TRANSLATE
		'move' => "Move selected entries?\\nDo you want to continue?", // TRANSLATE
		'move_ok' => "Files successfully moved!", // TRANSLATE
		'move_duplicate' => "There are files with the same name in the target directory!\\nThe files cannot be moved.", // TRANSLATE
		'move_nofolder' => "The selected files cannot be moved.\\nIt isn't possible to move directories.", // TRANSLATE
		'move_onlysametype' => "The selected objects cannnot be moved.\\nObjects can only be moved in there own classdirectory.", // TRANSLATE
		'move_no_dir' => "Please choose a target directory!", // TRANSLATE
		'document_move_warning' => "After moving documents it is  necessary to do a rebuild.<br />Would you like to do this now?", // TRANSLATE
		'nothing_to_move' => "There is nothing marked to move!", // TRANSLATE
		'move_of_files_failed' => "One or more files couldn't moved! Please move these files manually.\\nThe following files are affected:\\n%s", // TRANSLATE
		'template_save_warning' => "This template is used by %s published documents. Should they be resaved? Attention: This procedure may take some time if you have many documents!", // TRANSLATE
		'template_save_warning1' => "This template is used by one published document. Should it be resaved?", // TRANSLATE
		'template_save_warning2' => "This template is used by other templates and documents, should they be resaved?", // TRANSLATE
		'thumbnail_exists' => 'Esta imagen en miniatura ya existe!',
		'thumbnail_not_exists' => 'Esta imagen en miniatura no existe!',
		'thumbnail_empty' => "You must enter a name for the new thumbnail!", // TRANSLATE
		'doctype_exists' => "Este tipo de documento ya existe!",
		'doctype_empty' => "UD debe entrar un nombre para el nuevo tipo de documento!",
		'delete_cat' => "Ud realmente desea borrar la categoría seleccionada?",
		'delete_cat_used' => "Esta categoría está en uso y no puede ser borrada!",
		'cat_exists' => "Esta categoría ya existe!",
		'cat_changed' => "La categoría está en uso! Salve nuevamente los documentos que están usando la categoría!\\\\nDebe la categoría ser modificada de todas formas?",
		'max_name_cat' => "El nombre de la categoría debe tener solamente 32 carácteres!",
		'not_entered_cat' => "Ningún nombre de categoría ha sido entrado!",
		'cat_new_name' => "Entre el nuevo nombre para la categoría!",
		'we_backup_import_upload_err' => "Un error ocurrio mientras se cargaba el archivo de reserva! El tamaño maximo del archivo para cargar es %s. Si su archivo de reserva excede este limite, por favor, cargarlo en el directorio webEdition/we_Backup vía FTP y escoger '" . g_l('backup', "[import_from_server]") . "'",
		'rebuild_nodocs' => "Ningún documento se iguala con los atributos seleccionados.",
		'we_name_not_allowed' => "Los terminos 'we' and 'webEdition' son palabras reservadas y no deben ser usadas!",
		'we_filename_empty' => "Ningún nombre ha sido entrado para este documento o directorio!",
		'exit_multi_doc_question' => "Several open documents contain unsaved changes. If you continue all unsaved changes are discarded. Do you want to continue and discard all modifications?", // TRANSLATE
		'exit_doc_question_' . FILE_TABLE => "El documento ha sido cambiado.<BR> Desearía Ud salvar sus cambios?",
		'exit_doc_question_' . TEMPLATES_TABLE => "La plantilla ha sido cambiada.<BR> Desearía Ud salvar sus cambios?",
		'deleteTempl_notok_used' => "Una o más de las plantillas están en uso y no podrían ser borradas",
		'deleteClass_notok_used' => "One or more of the classes are in use and could not be deleted!", // TRANSLATE
		'delete_notok' => "Error mientras se borraba!",
		'nothing_to_save' => "La función de salvar está desactivada por el momento!",
		'nothing_to_publish' => "The publish function is disabled at the moment!", // TRANSLATE
		'we_filename_notValid' => "Invalid filename\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)",
		'empty_image_to_save' => "La imagen seleccionada está vacia..\\nContinuar?",
		'path_exists' => "El archivo o documento %s no puede ser salvado porque otro documento ya ocupa su lugar!",
		'folder_not_empty' => "Uno o más directorios no están completamente vacios y por lo tanto no pueden ser borrados! Borre los archivos manualmente.\\\\n Los siguientes archivos son efectuados:\\n%s",
		'name_nok' => "Los nombres no deben contener carácteres como '<' o '>'!",
		'found_in_workflow' => "Una o más entradas seleccionadas están en el proceso del flujo de trabajo! Desea Ud removerlas del proceso del flujo de trabajo?",
		'import_we_dirs' => "Ud está tratando de importar desde un directorio webEdition!\\\\n Esos directorios son usados y protejidos por webEdition y por lo tanto no pueden ser usados para importar!",
		'no_file_selected' => "Ningún archivo ha sido selecionado para cargar!",
		'browser_crashed' => "La ventana no pudo ser abierta por un error con su navegador!  Por favor, salve su trabajo y reinicie el navegador.",
		'copy_folders_no_id' => "Por favor, salve el directorio actual primero!",
		'copy_folder_not_valid' => "El mismo directorio o uno de los directorios primarios no puede ser copiado!",
		'cockpit_not_activated' => 'The action could not be performed because the cockpit is not activated.', // TRANSLATE
		'cockpit_reset_settings' => 'Are you sure to delete the current Cockpit settings and reset the default settings?', // TRANSLATE
		'save_error_fields_value_not_valid' => 'The highlighted fields contain invalid data.\\nPlease enter valid data.', // TRANSLATE

		'eplugin_exit_doc' => "The document has been edited with extern editor. The connection between webEdition and extern editor will be closed and the content will not be synchronized anymore.\\nDo you want to close the document?", // TRANSLATE

		'delete_workspace_user' => "The directory %s could not be deleted! It is defined as workspace for the following users or groups:\\n%s", // TRANSLATE
		'delete_workspace_user_r' => "The directory %s could not be deleted! Within the directory there are other directories which are defined as workspace for the following users or groups:\\n%s", // TRANSLATE
		'delete_workspace_object' => "The directory %s could not be deleted! It is defined as workspace for the following objects:\\n%s", // TRANSLATE
		'delete_workspace_object_r' => "The directory %s could not be deleted! Within the directory there are other directories which are defined as workspace in the following objects:\\n%s", // TRANSLATE


		'field_contains_incorrect_chars' => "A field (of the type %s) contains invalid characters.", // TRANSLATE
		'field_input_contains_incorrect_length' => "The maximum length of a field of the type \'Text input\' is 255 characters. If you need more characters, use a field of the type \'Textarea\'.", // TRANSLATE
		'field_int_contains_incorrect_length' => "The maximum length of a field of the type \'Integer\' is 10 characters.", // TRANSLATE
		'field_int_value_to_height' => "The maximum value of a field of the type \'Integer\' is 2147483647.", // TRANSLATE


		'we_filename_notValid' => "Nombre de archivo inválido\\\\nLos carácteres válidos son alpha-númericos, mayúsculas y minúsculas, así como subrayados, guión y punto (a-z, A-Z, 0-9, _, -, .)",
		'login_denied_for_user' => "The user cannot login. The user access is disabled.", // TRANSLATE
		'no_perm_to_delete_single_document' => "You have not the needed permissions to delete the active document.", // TRANSLATE

		'confirm' => array(
				'applyWeDocumentCustomerFiltersDocument' => "The document has been moved to a folder with divergent customer account policies. Should the settings of the folder be transmitted to this document?", // TRANSLATE
				'applyWeDocumentCustomerFiltersFolder' => "The directory has been moved to a folder with divergent customers account policies. Should the settings be adopted for this directory and all subelements? ", // TRANSLATE
		),
		'field_in_tab_notvalid_pre' => "The settings could not be saved, because the following fields contain invalid values:", // TRANSLATE
		'field_in_tab_notvalid' => ' - field %s on tab %s', // TRANSLATE
		'field_in_tab_notvalid_post' => 'Correct the fields before saving the settings.', // TRANSLATE
		'discard_changed_data' => 'There are unsaved changes that will be discarded. Are you sure?', // TRANSLATE
);


if (defined("OBJECT_FILES_TABLE")) {
	$l_alert = array_merge($l_alert, array(
			'in_wf_warning' => "El objeto debe ser salvado antes de poder ser colocado en el flujo de trabajo!\\Desea UD salvar el objeto ahora?",
			'in_wf_warning' => "La clase debe ser salvada antes de poder ser colocada en el flujo de trabajo!\\Desea UD salvar la clase ahora?",
			'exit_doc_question_' . OBJECT_TABLE => "La clase ha sido cambiada.<BR> Desearía Ud salvar sus cambios?",
			'exit_doc_question_' . OBJECT_FILES_TABLE => "El objeto ha sido cambiado.<BR> Desearía UD salvar sus cambios?",
					));
}
