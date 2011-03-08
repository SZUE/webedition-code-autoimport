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
$l_modules_voting = array(
		'no_perms' => 'You do not have permission to use this option.',
		'delete_alert' => 'Eliminar la actual votación/grupo.\\n Esta seguro?',
		'result_delete_alert' => 'Delete the current voting results.\\nAre you sure?', // TRANSLATE
		'nothing_to_delete' => 'No hay nada que eliminar!',
		'nothing_to_save' => 'No hay nada que guardar',
		'we_filename_notValid' => 'Nombre de usuario inválido!\\nLos Caracteres válidos son: alfanuméricos, mayúsculas y minúsculas, así como subrayado, guión, punto y espacio en blanco (a-z, A-Z, 0-9, _, -, ., )',
		'menu_new' => 'Nuevo',
		'menu_save' => 'Guardar',
		'menu_delete' => 'Eliminar',
		'menu_exit' => 'Cerrar',
		'menu_info' => 'Información',
		'menu_help' => 'Ayuda',
		'headline' => 'Nombres y Apellidos',
		'headline_name' => 'Nombre',
		'headline_publish_date' => 'Crear Fecha',
		'headline_data' => 'Solicitar Datos',
		'publish_date' => 'Fecha',
		'publish_format' => 'Formato',
		'published_on' => 'Publicado en',
		'total_voting' => 'Votacion Total',
		'reset_scores' => 'Restaurar resultado',
		'inquiry_question' => 'Pregunta',
		'inquiry_answers' => 'Respuestas',
		'question_empty' => 'No se ha entrado pregunta, por favor entre una!',
		'answer_empty' => 'Una o mas respuestas no se han entrado, por favor, entre respuesta(s)',
		'invalid_score' => 'El valor para el resultado debe ser un número, por favor, trate de nuevo!',
		'headline_revote' => 'Control de Revoto',
		'headline_help' => 'Ayuda',
		'inquiry' => 'Pregunta',
		'browser_vote' => 'Un navegador no puede votar otra vez antes de',
		'one_hour' => '1 hora',
		'feethteen_minutes' => '15 min.', // TRANSLATE
		'thirthty_minutes' => '30 min.', // TRANSLATE
		'one_day' => '1 día',
		'never' => '--nunca--',
		'always' => '--siempre--',
		'cookie_method' => 'Por Método de Coookie',
		'ip_method' => 'Por Método de IP',
		'time_after_voting_again' => 'Tiempo antes de votar otra vez',
		'cookie_method_help' => 'Si no puede usar el método de IP seleccione este. Recuerde, algunos usuarios pueden tener las cookies deshabilitadas en sus navegadores.',
		'ip_method_help' => 'Si su sitio web tiene solo acceso a Intranet y sus usuarios no se conectan a través de un proxy, seleccione este método. Considere que algunos servidores asignan direcciones IP dinámicamente.',
		'time_after_voting_again_help' => 'Para evitar que un mismo navegador/IP vote más de una vez en forma rápida y sucesiva, seleccione una intervalo de tiempo apropiado antes del cual no se puede votar desde ese navegador. Si desea que se pueda votar desde un mismo navegador solo una vez, seleccione \"nunca\".',
		'property' => 'Propiedades',
		'variant' => 'Versión',
		'voting' => 'Votación',
		'result' => 'Resultado',
		'group' => 'Grupo',
		'name' => 'Nombre',
		'newFolder' => 'Nuevo Grupo',
		'save_group_ok' => 'El grupo ha sido salvado.',
		'save_ok' => 'La votación ha sido salvada.',
		'path_nok' => 'El camino es incorrecto!',
		'name_empty' => '¡El nombre no debe estar vacío!',
		'name_exists' => '¡El nombre existe!',
		'wrongtext' => '¡El nombre no es válido!',
		'voting_deleted' => 'La votación ha sido exitosamente eliminada.',
		'group_deleted' => 'El grupo ha sido exitosamente eliminado.',
		'access' => 'Acceso',
		'limit_access' => 'Limitar acceso',
		'limit_access_text' => 'Permitir acceso a los siguientes grupos',
		'variant_limit' => 'Al menos una versión debe existir en la encuesta!',
		'answer_limit' => 'La encuesta debe consistir de al menos dos respuestas!',
		'valid_txt' => 'El checkbox "active" ha de ser activado, así que la votación en su página es guardada y "parked" al final de su validez. Determine con los menús desplegables la fecha y la hora a las cuales la votación debe ejecutarse. Ninguna otra votación es aceptada a partir de este momento.',
		'active_till' => 'Activo hasta',
		'valid' => 'Validez',
		'export' => 'Exportar',
		'export_txt' => 'Exportar datos de las votaciones a un fichero CSV (valores separados por coma).',
		'csv_download' => "Descargar archivo CSV",
		'csv_export' => "El archivo '%s' ha sido guardado.",
		'fallback' => 'Método IP para recuperación de información perdida',
		'save_user_agent' => 'Guardar/Comparar datos del agente-usuario',
		'save_changed_voting' => "Voting has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'voting_log' => 'Voto de Protocolo',
		'forbid_ip' => 'Suspender la siguiente dirección de IP',
		'until' => 'hasta',
		'options' => 'Opciones',
		'control' => 'Control', // TRANSLATE
		'data_deleted_info' => '¡El dato ha sido eliminado!',
		'time' => 'Tiempo',
		'ip' => 'IP', // TRANSLATE
		'user_agent' => 'Agente-Usuario',
		'cookie' => 'Cookie', // TRANSLATE
		'delete_ipdata_question' => 'Usted quiere eliminar todos los datos de IP guardados. ¿Está usted seguro?',
		'delete_log_question' => 'Usted quiere eliminar todos los registros de votos entrados. ¿Está usted seguro?',
		'delete_ipdata_text' => 'Los datos de IP guardados ocupan %s Bytes de la memoria. Elimínelos usando el botón \Borrar\'. Por favor considere que toda la información de IP guardada será eliminada y por consiguiente los votos iterativos serán posibles.',
		'status' => 'Estado',
		'log_success' => 'Éxito',
		'log_error' => 'Error', // TRANSLATE
		'log_error_active' => 'Error: no activar',
		'log_error_revote' => 'Error: nuevo voto',
		'log_error_blackip' => 'Error: IP suspendido',
		'log_is_empty' => '¡El diario está vacío!',
		'enabled' => 'Activado',
		'disabled' => 'Desactivado',
		'log_fallback' => 'Recuperar',
		'new_ip_add' => '¡Por favor introduzca la nuev dirección de IP!',
		'not_valid_ip' => 'La dirección de IP no es válida',
		'not_active' => 'The entered date is in the past!', // TRANSLATE

		'headline_datatype' => 'Type of Inquiry', // TRANSLATE
		'AllowFreeText' => 'Allow free text', // TRANSLATE
		'AllowImages' => 'Allow images', // TRANSLATE
		'AllowSuccessor' => 'redirect to:', // TRANSLATE
		'AllowSuccessors' => 'allow individual redirects', // TRANSLATE
		'csv_charset' => "Export charset", // TRANSLATE
		'imageID_text' => "Image ID", // TRANSLATE
		'successorID_text' => "Successor ID", // TRANSLATE
		'mediaID_text' => "Media-ID", // TRANSLATE
		'AllowMedia' => 'Allow Media such as Audio or video files', // TRANSLATE

		'voting-id' => 'Voting ID', // TRANSLATE
		'voting-session' => 'Voting Session', // TRANSLATE
		'voting-successor' => 'successor', // TRANSLATE
		'voting-additionalfields' => 'add. data', // TRANSLATE
		'answerID' => 'answer ID', // TRANSLATE
		'answerText' => 'answer text', // TRANSLATE

		'userid_method' => 'For logged in Users (customer management), compare to saved customer ID (the log has to be active)', // TRANSLATE
		'IsRequired' => 'This is a required field', // TRANSLATE

		'answer_limit' => 'The inquiry must consist of at least two - in case free text answers are allowd one - answers!', // TRANSLATE
		'folder_path_exists' => "Folder already exists!", // TRANSLATE
);