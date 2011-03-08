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
 * Language file: users.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_users = array(
		'user_same' => "The owner cannot be deleted!", // TRANSLATE
		'grant_owners_ok' => "Los dueños fueron cambiados éxitosamente!",
		'grant_owners_notok' => "¡Error al cambiar los dueños!",
		'grant_owners' => "Cambiar dueños",
		'grant_owners_expl' => "Cambiar los dueños de todos los archivos y directorios que residen en el directorio actual al dueño ajustado arriba",
		'make_def_ws' => "Predeterminado",
		'user_saved_ok' => "El usuario '%s' fue salvado exitosamente",
		'group_saved_ok' => "El grupo '%s' fue salvado exitosamente",
		'alias_saved_ok' => "El alias '%s' fue salvado exitosamente",
		'user_saved_nok' => "El usuario '%s' no se puede salvar!",
		'nothing_to_save' => "Nada para salvar!",
		'username_exists' => "El nombre de usuario '%s' ya existe!",
		'username_empty' => "¡El nombre de usuario está vacío!",
		'user_deleted' => "El usuario '%s' fue borrado!",
		'nothing_to_delete' => "Nada para borrar!",
		'delete_last_user' => "Ud está tratando de borrar el último usuario con derechos de administrador. Borrarlo haría el sistema inmanejable! Por lo tanto, no es posible borrarlo.",
		'modify_last_admin' => "Debe haber al menos un administrador.\\n Ud no puede cambiar los derechos del último administrador.",
		'user_path_nok' => "La ruta de acceso no es correcta!",
		'user_data' => "Data del usuario",
		'first_name' => "Nombre",
		'second_name' => "Apellido",
		'username' => "Nombre de usuario",
		'password' => "Contraseña",
		'workspace_specify' => "Especificar área de trabajo",
		'permissions' => "Permisos",
		'user_permissions' => "Permisos del usuario",
		'admin_permissions' => "Permisos del administrador",
		'password_alert' => "La contraseña debe tener por lo menos 4 carácteres",
		'delete_alert_user' => "Toda el data del usuario para el nombre de usuario ' %s ' será borrado.\\n ¿Está UD seguro que desea continuar?",
		'delete_alert_alias' => "Toda el data del alias para el alias ' %s ' será borrado.\\n ¿Está UD seguro que desea continuar?",
		'delete_alert_group' => "Toda el data del grupo y grupo de usuarios para el grupo ' %s ' será borrado.\\n ¿Está UD seguro que desea continuar?",
		'created_by' => "Creado por",
		'changed_by' => "Cambiado por",
		'no_perms' => "UD no tiene ningún permiso para usar esta opción!",
		'publish_specify' => "El usuario puede publicar",
		'work_permissions' => "Permisos de trabajo",
		'control_permissions' => "Permisos de control",
		'log_permissions' => "Permisos de conexión",
		'file_locked' => array(
				FILE_TABLE => "El archivo '%s' es actualmente usado por '%s'!",
				TEMPLATES_TABLE => "La plantilla '%s' es actualmente usada por '%s'!",
		),
		'acces_temp_denied' => "Acceso denegado temporalmente",
		'description' => "Descripción",
		'group_data' => "Data de grupo",
		'group_name' => "Nombre de grupo",
		'group_member' => "Membresía de grupo",
		'group' => "Grupo",
		'address' => "Dirección",
		'houseno' => "Número de casa/apartamento",
		'state' => "Estado",
		'PLZ' => "Código Postal",
		'city' => "Ciudad",
		'country' => "Pais",
		'tel_pre' => "Código telefónico del área",
		'fax_pre' => "Código de fax del área",
		'telephone' => "Teléfono",
		'fax' => "Fax", // TRANSLATE
		'mobile' => "Celular",
		'email' => "E-Mail", // TRANSLATE
		'general_data' => "Data general",
		'workspace_documents' => "Documentos del área de trabajo",
		'workspace_templates' => "Plantillas del área de trabajo",
		'workspace_objects' => "Workspace Objects", // TRANSLATE
		'save_changed_user' => "El usuario fue cambiado.\\nDesea UD salvar sus cambios?",
		'not_able_to_save' => " El data no ha sido salvado por la invalidez del data!",
		'cannot_save_used' => " El status no puede ser cambiado porque está en proceso!",
		'geaendert_von' => "Cambiado por",
		'geaendert_am' => "Cambiado en",
		'angelegt_am' => " Establecido en",
		'angelegt_von' => "Establecido por",
		'status' => "Estatus",
		'value' => " Valor ",
		'gesperrt' => "restringido",
		'freigegeben' => "abrir",
		'gelöscht' => "deleted", // TRANSLATE
		'ohne' => "sin",
		'user' => "Usuario",
		'usertyp' => "Tipo de usuario",
		'search' => "Suche", // TRANSLATE
		'search_result' => "Ergebnis", // TRANSLATE
		'search_for' => "Suche nach", // TRANSLATE
		'inherit' => "Heredar permisos desde el grupo primario",
		'inherit_ws' => "Heredar área de trabajo de documentos desde el grupo primario",
		'inherit_wst' => "Heredar área de trabajo de plantillas desde el grupo primario",
		'inherit_wso' => "Inherit objects workspace from parent group", // TRANSLATE
		'organization' => "Organización",
		'give_org_name' => "Nombre de la organización",
		'can_not_create_org' => "La organización no puede ser creada",
		'org_name_empty' => "El nombre de la organización está vacío",
		'salutation' => "Saludo",
		'sucheleer' => "La palabra de búsqueda está vacía.",
		'alias_data' => "Data del alias",
		'rights_and_workspaces' => "Permisos y<br>áreas de trabajo",
		'workspace_navigations' => "Workspave Navigation", // TRANSLATE
		'inherit_wsn' => "Inherit navigation workspaces from parent group", // TRANSLATE
		'workspace_newsletter' => "Workspace Newsletter", // TRANSLATE
		'inherit_wsnl' => "Inherit newsletter workspaces from parent group", // TRANSLATE

		'delete_user_same' => "Sie k�nnen nicht Ihr eigenes Konto l�schen.",
		'delete_group_user_same' => "Sie k�nnen nicht Ihre eigene Gruppe l�schen.",
		'login_denied' => "Login denied", // TRANSLATE
		'workspaceFieldError' => "ERROR: Invalid workspace entry!", // TRANSLATE
		'noGroupError' => "Error: Invalid entry in field group!", // TRANSLATE
		'CreatorID' => "Created by: ", // TRANSLATE
		'CreateDate' => "Created at: ", // TRANSLATE
		'ModifierID' => "Modified by: ", // TRANSLATE
		'ModifyDate' => "Modified at: ", // TRANSLATE
		'lastPing' => "Last Ping: ", // TRANSLATE
		'lostID' => "ID: ", // TRANSLATE
		'lostID2' => " (deleted)", // TRANSLATE
);
if (defined("OBJECT_TABLE")) {
	$l_modules_users["file_locked"][OBJECT_TABLE] = "La clase '%s' es actualmente usada por '%s'!";
	$l_modules_users["file_locked"][OBJECT_FILES_TABLE] = "El objeto '%s' es actualmente usado por '%s'!";
}
