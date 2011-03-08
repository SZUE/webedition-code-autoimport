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
 * Language file: newsletter.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_newsletter = array(
		'save_changed_newsletter' => "Newsletter has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'Enter_Path' => "Por favor, entre una ruta de acceso comenzando con el DOCUMENTO-RAÍZ",
		'title_or_salutation' => "Formato de título en inglés (sin saludo)",
		'global_mailing_list' => "Lista de correos predefinida (archivo CSV)",
		'new_newsletter' => "Nuevo boletín informativo",
		'newsletter' => "Boletín Informativo",
		'new' => "Nuevo",
		'save' => "Salvar",
		'delete' => "Borrar",
		'quit' => "Salir",
		'help' => "Ayuda",
		'info' => "Info", // TRANSLATE
		'options' => "Opciones",
		'send_test' => "Enviar E-Mail de prueba", // CHECK
// changed from: "Send test E-mail"
// changed to  : "Send test email"

		'domain_check' => "Chequear Dominio",
		'send' => "Enviar",
		'preview' => "Vista previa",
		'settings' => "Preferencias",
		'show_log' => "Show logbook", // TRANSLATE
		'mailing_list' => "Lista de Correos %s",
		'customers' => "Clientes",
		'emails' => "E-Mails", // CHECK
// changed from: "E-mails"
// changed to  : "emails"

		'newsletter_content' => "Contenido del boletín informativo",
		'type_doc' => "Documentos",
		'type_object' => "Objetos",
		'type_file' => "Archivo",
		'type_text' => "Texto",
		'attchments' => "Adjuntos",
		'name' => "Nombre",
		'no_perms' => "Sin permisos",
		'nothing_to_delete' => "Nada para borrar.",
		'documents' => "Documentos",
		'save_ok' => "El boletín informativo fue salvado.",
		'message_description' => "Definir contenido del boletín informativo",
		'sender' => "Remitente",
		'reply' => "Responder a",
		'reply_same' => "Igual que el remitente",
		'block_type' => "Tipo de bloque",
		'block_document' => "Documento",
		'block_document_field' => "Campo de documento",
		'block_object' => "Objeto",
		'block_object_field' => "Campo de objeto",
		'block_file' => "Archivo",
		'block_html' => "HTML", // TRANSLATE
		'block_plain' => "Texto simple",
		'block_newsletter' => "Boletín Informativo",
		'block_attachment' => "Adjunto",
		'block_lists' => "Listas de correos",
		'block_all' => "----   todos   ----",
		'block_template' => "Plantilla",
		'block_url' => "URL", // TRANSLATE
		'use_default' => "Usar plantilla predefinida",
		'subject' => "Asunto",
		'delete_question' => "Desea Ud borrar el boletín informativo actual?",
		'delete_group_question' => "Do you want to delete the current group?", // TRANSLATE
		'delete_ok' => "El boletín informativo fue borrado.",
		'delete_nok' => "Error: El boletín informativo no fue borrado",
		'test_email' => "E-Mail de prueba", // CHECK
// changed from: "Test E-mail"
// changed to  : "Test email"

		'test_email_question' => "Esto enviará un E-mail de prueba a su cuenta de E-mail de prueba.\\nDesea Ud continuar?", // CHECK
// changed from: "This will send a test E-mail to your test E-mail account %s!\\n Do you want to proceed?"
// changed to  : "This will send a test email to your test email account %s!\\n Do you want to proceed?"

		'test_mail_sent' => "El E-mail de prueba fue enviado a la cuenta de E-mail de prueba %s", // CHECK
// changed from: "The test E-mail has been sent to the test E-mail account %s."
// changed to  : "The test email has been sent to the test email account %s."

		'malformed_mail_group' => "La lista de correos contiene E-mail %s no válido!\\nEl boletín informativo no fue salvado!", // CHECK
// changed from: "Mailing list %s has malformed E-mail '%s'!\\nThe newsletter has not been saved!"
// changed to  : "Mailing list %s has malformed email '%s'!\\nThe newsletter has not been saved!"

		'malformed_mail_sender' => "La dirección de E-mail del remitente no es válida!\\nEl boletín informativo no fue salvado!", // CHECK
// changed from: "The senders E-mail address '%s' is malformed!\\nThe newsletter has not been saved!"
// changed to  : "The senders email address '%s' is malformed!\\nThe newsletter has not been saved!"

		'malformed_mail_reply' => "La dirección de E-mail para la respuesta no es válida\\nEl boletín informativo no fue salvado!", // CHECK
// changed from: "The reply E-mail address '%s' is malformed!\\nThe newsletter has not been saved!"
// changed to  : "The reply email address '%s' is malformed!\\nThe newsletter has not been saved!"

		'malformed_mail_test' => "La dirección de E-mail de prueba %s no es válida!\\nEl boletín informativo no fue salvado!", // CHECK
// changed from: "The test E-mail address '%s' is malformed!\\nThe newsletter has not been saved!"
// changed to  : "The test email address '%s' is malformed!\\nThe newsletter has not been saved!"

		'send_question' => "Desea enviar este boletín informativo a la lista de correos?",
		'send_test_question' => "Esto es una prueba y ningún boletín informativo será enviado.\\nConfirmar para continuar",
		'domain_ok' => "El dominio %s fue verificado.",
		'domain_nok' => "El dominio %s no puede ser verificado.",
		'email_malformed' => "La dirección de E-Mail %s no es válida.", // CHECK
// changed from: "The E-mail address %s is malformed."
// changed to  : "The email address %s is malformed."

		'domain_check_list' => "Chequeo de dominio para la lista de correos %s",
		'domain_check_begins' => "El chequeo de dominio ha sido iniciado",
		'domain_check_ends' => "El chequeo de dominio ha finalizado",
		'newsletter_type_0' => "Basado en documento",
		'newsletter_type_1' => "Basado en campo de documento",
		'newsletter_type_2' => "Basado en objeto",
		'newsletter_type_3' => "Basado en campo de objeto",
		'newsletter_type_4' => "Basado en archivo",
		'newsletter_type_5' => "Texto",
		'newsletter_type_6' => "Adjunto",
		'newsletter_type_7' => "URL", // TRANSLATE
		'all_list' => "-- Todas las listas --",
		'newsletter_test' => "Prueba",
		'send_to_list' => "Enviar a la lista de correos %s.",
		'campaign_starts' => "La campaña del boletín informativo se ha iniciado...",
		'campaign_ends' => "La campaña del boletín informativo ha finalizado.",
		'test_no_mail' => "Prueba - ningún E-Mail será enviado...", // CHECK
// changed from: "Testing - no E-mail s will be sent..."
// changed to  : "Testing - no emails will be sent..."

		'sending' => "Enviando...",
		'mail_not_sent' => "El E-Mail '%s' no puede ser enviado.", // CHECK
// changed from: " E-mail '%s' cannot be sent."
// changed to  : " email '%s' cannot be sent."

		'filter' => "Filtros",
		'send_all' => "Enviar a todos",
		'lists_overview_menu' => "Vista general de las listas",
		'lists_overview' => "Vista general de las listas",
		'copy' => "Copiar",
		'copy_newsletter' => "Copiar boletín<br>informativo",
		'continue_camp' => "La campaña previa del boletín informativo no fue completada!<br>La campaña previa puede ser continuada.<br>Desea UD continuar con la campaña previa?",
		'reject_malformed' => "No enviar E-Mail si la dirección no es válida.", // CHECK
// changed from: "Do not send E-mail if address is malformed."
// changed to  : "Do not send email if address is malformed."

		'reject_not_verified' => "No enviar E-Mail si la dirección no se puede verificar.", // CHECK
// changed from: "Do not send E-mail if address cannot be verified."
// changed to  : "Do not send email if address cannot be verified."

		'send_step' => "Número de E-Mails por carga", // CHECK
// changed from: "Number of E-mails per load"
// changed to  : "Number of emails per load"

		'test_account' => "Cuenta de prueba",
		'log_sending' => "Crear entradas al diario cuando el E-Mail es enviado", // CHECK
// changed from: "Create a logbook entry when sending E-mail."
// changed to  : "Create a logbook entry when sending email."

		'default_sender' => "Remitente predefinido",
		'default_reply' => "Respuesta predefinida",
		'default_htmlmail' => "El formato predefinido del E-Mail es HTML", // CHECK
// changed from: "The default E-mail format is HTML."
// changed to  : "The default email format is HTML."

		'isEmbedImages' => "Embed images", // TRANSLATE
		'ask_to_preserve' => "La campaña previa del boletín informativo no fue completada!<br>Si UD salva la hoja informativa ahora, no podrá continuar con la campaña!<br>Desea UD continuar?",
		'log_save_newsletter' => "La hoja informnativa ha sido salvada.",
		'log_start_send' => "Inicie la campaña del boletín informativo.",
		'log_end_send' => "La campaña del boletín informativo ha finalizado exitosamente.",
		'log_continue_send' => "La campaña del boletín informativo continua...",
		'log_campaign_reset' => "Los parámetros de la campaña del boletín informativo fueron reajustados.",
		'mail_sent' => "El boletín informativo fue enviado a %s .",
		'must_save' => "El boletín informativo fue cambiado.\\nDebe de salvar los cambios antes de ser enviada!",
		'email_exists' => "La dirección de E-Mail ya existe!", // CHECK
// changed from: "The E-mail address already exists!"
// changed to  : "The email address already exists!"

		'email_max_len' => "La dirección de E-Mail no puede tener mas de 255 carácteres!", // CHECK
// changed from: "The E-mail address cannot exceed 255 charachters!"
// changed to  : "The email address cannot exceed 255 charachters!"

		'no_email' => "Ninguna dirección de E-Mail seleccionada!", // CHECK
// changed from: " E-mail address missing!"
// changed to  : " email address missing!"

		'email_new' => "Por favor, suministre una dirección de E-Mail!", // CHECK
// changed from: "Please provide an E-mail address!"
// changed to  : "Please provide an email address!"

		'email_delete' => "Desea Ud borrar las direcciónes de E-Mail seleccionados?", // CHECK
// changed from: "Do you want to delete the selected E-mail addresses?"
// changed to  : "Do you want to delete the selected email addresses?"

		'email_delete_all' => "Desea Ud borrar todas las direcciónes de E-Mail?", // CHECK
// changed from: "Do you want to delete all E-mail addresses?"
// changed to  : "Do you want to delete all email addresses?"

		'email_edit' => "La dirección de E-Mail fue cambiada!", // CHECK
// changed from: "E-mail address changed!"
// changed to  : "email address changed!"

		'nothing_to_save' => "Nada para salvar!",
		'csv_delimiter' => "Delimitador",
		'csv_col' => "Columna de E-Mail", // CHECK
// changed from: " E-mail col."
// changed to  : " email col."

		'csv_hmcol' => "Columna de HTML",
		'csv_salutationcol' => "Columna de Saludos",
		'csv_titlecol' => "Columna de Titulo",
		'csv_firstnamecol' => "Columna de Nombre",
		'csv_lastnamecol' => "Columna de Apellido",
		'csv_export' => "El archivo '%s' fue salvado.",
		'customer_email_field' => "Campo de E-Mail del cliente", // CHECK
// changed from: "Cust. E-mail field"
// changed to  : "Cust. email field"

		'customer_html_field' => "Campo de HTML del cliente",
		'customer_salutation_field' => "Campo de Saludo del cliente",
		'customer_title_field' => "Campo de Titulo del cliente",
		'customer_firstname_field' => "Campo de Nombre del cliente",
		'customer_lastname_field' => "Campo de Apellido del cliente",
		'csv_html_explain' => "(0 - Ninguna columna de HTML)",
		'csv_salutation_explain' => "(0 - Ninguna columna de Saludo)",
		'csv_title_explain' => "(0 - Ninguna columna de Titulo)",
		'csv_firstname_explain' => "(0 - Ninguna columna de Nombre)",
		'csv_lastname_explain' => "(0 - Ninguna columna de Apellido)",
		'email' => "E-Mail", // CHECK
// changed from: " E-mail "
// changed to  : " email "

		'lastname' => "Apellido",
		'firstname' => "Nombre",
		'salutation' => "Saludo",
		'title' => "Titulo",
		'female_salutation' => "Saludo femenino",
		'male_salutation' => "Saludo masculino",
		'edit_htmlmail' => "E-Mail HTML", // CHECK
// changed from: "Receive HTML E-mail "
// changed to  : "Receive HTML email "

		'htmlmail_check' => "HTML", // TRANSLATE
		'double_name' => "El nombre del boletín informativo ya existe",
		'cannot_preview' => "La vista previa del boletín informativo no puede ser mostrada!",
		'empty_name' => "¡El nombre no puede estar vacío!",
		'edit_email' => "Editar dirección de E-Mail", // CHECK
// changed from: "Edit E-mail address"
// changed to  : "Edit email address"

		'add_email' => "Adicionar dirección de E-Mail", // CHECK
// changed from: "Add E-mail address"
// changed to  : "Add email address"

		'none' => "-- Ninguna --",
		'must_save_preview' => "El boletín informativo ha sido cambiado.\\nDebe ser salvada antes de poder ver la vista previa!",
		'black_list' => "Lista Negra",
		'email_is_black' => "El E-Mail %s está en la lista negra!", // CHECK
// changed from: " E-mail is on the balck list!"
// changed to  : " email is on the balck list!"

		'upload_nok' => "El archivo no puede ser cargado.",
		'csv_download' => "Descargar archivo CSV",
		'csv_upload' => "Cargar archivo CSV",
		'finished' => "Finalizado",
		'cannot_open' => "El archivo no se puede abrir",
		'search_email' => "Buscar E-Mail...", // CHECK
// changed from: "Search E-mail..."
// changed to  : "Search email..."

		'search_text' => "Por favor, entre E-Mail", // CHECK
// changed from: "Enter E-mail please"
// changed to  : "Enter email please"

		'search_finished' => "Búsqueda finalizada.\\nEncontrado: %s",
		'email_double' => "La dirección de E-Mail %s ya existe!", // CHECK
// changed from: "The E-mail address %s already exists!"
// changed to  : "The email address %s already exists!"

		'error' => "ERROR", // TRANSLATE
		'warning' => "AVISO",
		'file_email' => "Archivos CSV",
		'edit_file' => "Editar archivos CSV",
		'show' => "Mostrar",
		'no_file_selected' => "Ningún archivo seleccionado",
		'file_is_empty' => "The CSV file is empty", // TRANSLATE
		'file_all_ok' => "The CSV file has no invalid entries", // TRANSLATE
		'del_email_file' => "Borrar E-Mail '%s'?", // CHECK
// changed from: "Delete E-mail '%s'?"
// changed to  : "Delete email '%s'?"

		'email_missing' => "Missing E-mail address", // CHECK
// changed from: "Missing E-mail address"
// changed to  : "Missing email address"

		'yes' => "Si",
		'no' => "No", // TRANSLATE
		'select_file' => "Seleccionar archivo",
		'clear_log' => "Vaciar diario",
		'clearlog_note' => "Le gustaría vaciar el diario completo?",
		'log_is_clear' => "El diario fue vaciado.",
		'property' => "Propiedades",
		'edit' => "Editar",
		'details' => "Detalles",
		'path' => "Ruta",
		'dir' => "Directorio",
		'block' => "Bloque %s",
		'new_newsletter_group' => "Nuevo Grupo",
		'group' => "Grupo",
		'path_nok' => "La ruta de acceso es incorrecta!",
		'save_group_ok' => "El grupo de hojas informativas fue salvado.",
		'delete_group_ok' => "El grupo de hojas informativas fue borrado.",
		'delete_group_nok' => "ERROR: el grupo de hojas informativas no ha sido borrado",
		'path_not_valid' => "La ruta de acceso no es válida",
		'no_subject' => "El campo del asunto está vacío. Desea enviar el boletín informativo de todas formas?",
		'mail_failed' => "El E-Mail '%s' no puede enviarse. ¡Una posible causa es la configuración incorrecta del servidor!", // CHECK
// changed from: " E-mail '%s' cannot be sent. A possible cause is an incorrect server configuration."
// changed to  : " email '%s' cannot be sent. A possible cause is an incorrect server configuration."

		'reject_save_malformed' => "No salvar el boletín informativo si la dirección de E-Mail es inválida.",
		'rfc_email_check' => "Validate conform to rfc 3696.<br>WARNIGN: This validation can take heavy influence on the speed of your server.", // TRANSLATE
		'use_https_refer' => "Use HTTPS para la referencia",
		'use_base_href' => "Use &lt;base href=... in head", // TRANSLATE
		'we_filename_notValid' => "El nombre entrado no es válido!\\nLos carácteres permitidos son alpha-númericos, mayúsculas y minúsculas, subrayado (_), guión (-), punto (.) y espacios ()(a-z, A-Z, 0-9, _, -, ., ).",
		'send_wait' => "Esperar un periodo para la próxima carga (en ms)",
		'send_images' => "Enviar imágenes",
		'prepare_newsletter' => "Preparación...",
		'use_port_check' => "Use puerto para la referencia",
		'use_port' => "Puerto",
		'sum_group' => "Dirección(es) de E-mail en la lista %s", // CHECK
// changed from: "E-mail address(es) in liste %s"
// changed to  : "email address(es) in liste %s"

		'sum_all' => "Dirección(es) de E-mail en todas las listas", // CHECK
// changed from: "E-mail adress(es) all list(s)"
// changed to  : "email adress(es) all list(s)"

		'retry' => "Intentarlo nuevamente",
		'charset' => "Charset", // TRANSLATE
		'additional_clp' => "Additional reply address (option -f)", // TRANSLATE
		'html_preview' => "show HTML preview", // TRANSLATE
		'status' => "Status", // TRANSLATE
		'statusAll' => "all entries", // TRANSLATE
		'statusInvalid' => "invalid entries", // TRANSLATE
		'invalid_email' => "The email is not valid.", // TRANSLATE
		'blockFieldError' => "Invalid value in Block %s, Field %s!",
		'operator' => array(
				'startWith' => "starts with", // TRANSLATE
				'endsWith' => "ends with", // TRANSLATE
				'contains' => "contains", // TRANSLATE
		),
		'logic' => array(
				'and' => "and", // TRANSLATE
				'or' => "or", // TRANSLATE
		),
		'default' => array(
				'female' => "Mrs.", // TRANSLATE
				'male' => "Mr.", // TRANSLATE
		),
		'no_newsletter_selected' => "No newsletter selected. Please open the newsletter first.", // TRANSLATE
);