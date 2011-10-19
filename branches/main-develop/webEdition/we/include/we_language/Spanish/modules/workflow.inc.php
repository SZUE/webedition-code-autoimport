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
 * Language file: workflow.inc.php
 * Provides language strings.
 * Language: English
 */

$l_modules_workflow = array(
'new_workflow' => "New workflow", // TRANSLATE
'workflow' => "Flujo de Trabajo",

'doc_in_wf_warning' => "El documento se encuentra en el flujo de trabajo",
'message' => "Mensaje",
'in_workflow' => "Documento en el flujo de trabajo",
'decline_workflow' => "Rechazar documento",
'pass_workflow' => "Reenviar documeto",


'no_wf_defined' => "¡Ningún flujo de trabajo ha sido definido para este documento!",

'document' => "Documento",

'del_last_step' => "¡El último paso en serie no puede ser borrado!",
'del_last_task' => "¡El último paso en paralelo no puede ser borrado!",
'save_ok' => "El flujo de trabajo fue salvado!",
'delete_ok' => "El flujo de trabajo fue borrado!",
'delete_nok' => "El flujo de trabajo no puede ser borrado!",

'name' => "Nombre",
'type' => "Tipo",
'type_dir' => "Basado en directorio",
'type_doctype' => "Basado en tipo de documento\categoría",
'type_object' => "Basado en objeto",

'dirs' => "Directorios",
'doctype' => "Tipo de documento",
'categories' => "Categorías",
'classes' => "Clases",

'active' => "El flujo de trabajo is activo",

'step' => "Paso",
'and_or' => "Y/O",
'worktime' => "Worktime (H, 1min=0<b>.</b>016)", // TRANSLATE
'specials' => "Specials",// TRANSLATE
'EmailPath' => "Show the document path in the subject of notifications emails",// TRANSLATE
'LastStepAutoPublish' => "After the last step (next step clicked), publish the document instead of decline ist",// TRANSLATE
'user' => "Usuario",

'edit' => "Editar",
'send_mail' => "Enviar E-Mail",
'select_user' => "Seleccionar usuario",

'and' => " y ",
'or' => " o ",

'waiting_on_approval' => "Esperando por la aprobación de %s",
'status' => "Estatus",
'step_from' => "Paso %s de %s",

'step_time' => "Step time", // TRANSLATE
'step_start' => "Fecha de inicio del paso",
'step_plan' => "Fecha de final",
'step_worktime' => "Tiempo de trabajo planificado",

'current_time' => "Tiempo actual",
'time_elapsed' => "Tiempo transcurrido (h:m:s)",
'time_remained' => "Tiempo restante (h:m:s)",

'todo_subject' => "Tarea del flujo de trabajo",
'todo_next' => "Hay un documento esperando por Ud en el flujo de trabajo",

'go_next' => "Siguiente paso",

'new_step' => "Crear paso en serie adicional",
'new_task' => "Crear paso en paralelo adicional",

'delete_step' => "Borrar paso en serie",
'delete_task' => "Borrar paso en paralelo",

'save_question' => "Todos los documentos que están en el flujo de trabajo serán removidos del mismo!\\nContinuar de todas formas?",
'nothing_to_save' => "Nada para salvar!",
'save_changed_workflow' => "Workflow has been changed.\\nDo you want to save your changes?", // TRANSLATE

'delete_question' => "Todo el data del flujo de trabajo será borrado!\\nContinuar de todas formas?",
'nothing_to_delete' => "Nada para borrar!",

'user_empty' => "No se ha definido ningún Usuario para el paso %s.!",
'folders_empty' => "No se ha definido ningúna carpeta para el flujo de trabajo!",
'objects_empty' => "No se ha definido ningún objeto para el flujo de trabajo!",
'doctype_empty' => "No se ha definido ningún tipo de documento o categoría para el flujo de trabajo!",
'worktime_empty' => "No se ha definido ningún tiempo de trabajo para el paso %s.!",
'name_empty' => "No se ha definido ningún nombre para el flujo de trabajo!",
'cannot_find_active_step' => "¡El paso activo no se puede encontrar!",

'no_perms' => "Sin Permiso",
'plan' => "(plan)", // TRANSLATE

'todo_returned' => "El documento ha sido devuelto desde el flujo de trabajo.",

'description' => "Descripción",
'time' => "Tiempo",

'log_approve_force' => "El usuario ha aprobado el documento forzadamente.",
'log_approve' => "El usuario ha aprobado el documento.",
'log_decline_force' => "El usuario ha cancelado el documento forzadamente.",
'log_decline' => "El usuario ha cancelado el flujo de trabajo del documento.",
'log_doc_finished_force' => "El flujo de trabajo ha finalizado forzadamente.",
'log_doc_finished' => "El flujo de trabajo fue finalizado.",
'log_insert_doc' => "El documento ha sido insertado en el flujo de trabajo.",

'logbook' => "Diario",
'empty_log' => "Vaciar Diario",
'emty_log_question' => "¿Desea UD realmente vaciar el diario?",
'empty_log_ok' => "El diario fue vaciado.",
'log_is_empty' => "El diario está vacío.",

'log_question_all' => "Eliminar todo",
'log_question_time' => "Eliminar las entradas anteriores a:",
'log_question_text' => "Escoger opción:",

'log_remove_doc' => "El documento fue removido del flujo de trabajo",
'action' => "Acción",

'auto_approved' => "El documento fue aprovado automáticamente",
'auto_declined' => "El documento fue rechazado automáticamente",
'auto_published' => "Document has been automatically published.",// TRANSLATE

'doc_deleted' => "El documento fue borrado!",
'ask_before_recover' => "¡El documento/objeto aún está en el flujo de trabajo! ¿Puede UD sacar estos documentos/objetos del Flujo de Trabajo?",

'double_name' => "¡El nombre del flujo de trabajo ya existe!",

'more_information' => "Más información",
'less_information' => "Menos información",

'no_wf_defined_object' => "¡Ningún flujo de trabajo ha sido definido para este objeto!",

'tblFile'=> array(

'messagePath' => "Documento",
'in_workflow_ok' => "¡El documento fue colocado con éxito en el flujo de trabajo!",
'in_workflow_notok' => "¡El documento no pudo ser colocado en el flujo de trabajo!",
'pass_workflow_ok' => "¡El documento fue continado con éxito!",
'pass_workflow_notok' => "¡El documento no puede ser continuado!",
'decline_workflow_ok' => "¡El documento fue devuelto al autor!",
'decline_workflow_notok' => "¡El documento no puede ser devuelto al autor!",
),

	'tblObjectFiles' => array(
	'messagePath' => "Objeto",
	'in_workflow_ok' => "¡El objeto fue colocado con éxito en el flujo de trabajo!",
	'in_workflow_notok' => "¡El objeto no pudo ser colocado en el flujo de trabajo!",
	'pass_workflow_ok' => "¡El objeto fue continuado con éxito!",
	'pass_workflow_notok' => "¡El objeto no puede ser continuado!",
	'decline_workflow_ok' => "¡El objeto fue devuelto al autor!",
	'decline_workflow_notok' => "¡El objeto no puede ser devuelto al autor!",
	),
);
