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
 * Language file: global.inc.php
 * Provides language strings.
 * Language: English
 */
$l_global = array(
		'new_link' => "Nuevo Vínculo", // It is important to use the GLOBALS ARRAY because in linklists, the file is included in a function.
		'load_menu_info' => "¡Cargando data!<br>Esto puede tomar tiempo cuando se cargan varios elementos de menú ...",
		'text' => "Texto",
		'yes' => "Si",
		'no' => "No", // TRANSLATE
		'checked' => "Chequeado",
		'max_file_size' => "Tamaño máximo de archivo(en bytes)",
		'default' => "Predefinido",
		'values' => "Valores",
		'name' => "Nombre",
		'type' => "Tipo",
		'attributes' => "Atributos",
		'formmailerror' => "La forma no fue propuesta por las siguientes razones:",
		'email_notallfields' => "UD no ha llenado todos los campos requeridos!",
		'email_ban' => "UD no tiene los derechos para usar este script!",
		'email_recipient_invalid' => "La dirección del destinatario no es válida!",
		'email_no_recipient' => "La dirección del destinatario no existe!",
		'email_invalid' => "Su <b>dirección de E-mail</b> no es válida!",
		'captcha_invalid' => "The entered security code is wrong!", // TRANSLATE
		'question' => "Pregunta",
		'warning' => "Advertencía",
		'we_alert' => "Esta función no está disponible en la versión demo de webEdition!",
		'index_table' => "Tabla del índice",
		'cannotconnect' => "No se puede conectar con el servidor de webEdition!",
		'recipients' => "Destinatarios de formas de correos",
		'recipients_txt' => "Por favor, entre todas las direciones de E-mail que recibirán formularios enviados por las funciones de formas de correos (&lt;we:form type=&quot;formmail&quot; ..&gt;). Si UD no entra una direción de E-mail, no podrá enviar formularios usando esta función!",
		'std_mailtext_newObj' => "Un nuevo objeto %s de clase %s ha sido creado!",
		'std_subject_newObj' => "Nuevo objeto",
		'std_subject_newDoc' => "Nuevo documento",
		'std_mailtext_newDoc' => "Un nuevo documento %s ha sido creado!",
		'std_subject_delObj' => "Objeto borrado",
		'std_mailtext_delObj' => "El objeto %s ha sido borrado!",
		'std_subject_delDoc' => "Documento borrado",
		'std_mailtext_delDoc' => "El documento %s ha sido borrado!",
		'we_make_same' => array(
				'text/html' => "Nueva página después de salvar",
				'text/webedition' => "Nueva página después de salvar",
				'objectFile' => "New object after saving",
		),
		'no_entries' => "Ninguna entrada encontrada!",
		'save_temporaryTable' => "Salve nuevamente los documentos temporales",
		'save_mainTable' => "Salve nuevamente la tabla principal de la base de datos",
		'add_workspace' => "Adicionar área de trabajo",
		'folder_not_editable' => "Este directorio no puede ser escogido!",
		'modules' => "Módulos",
		'modules_and_tools' => "Modules and Tools", // TRANSLATE
		'center' => "Centro",
		'jswin' => "Ventana Pop-up",
		'open' => "Abrir",
		'posx' => "Posición x",
		'posy' => "Posición y",
		'status' => "Estatus",
		'scrollbars' => "Barras de<br>desplazamiento",
		'menubar' => "Barra del<br>menú",
		'toolbar' => "Toolbar", // TRANSLATE
		'resizable' => "Cambiable<br>de tamaño",
		'location' => "Ubicación",
		'title' => "Titulo",
		'description' => "Descripción",
		'required_field' => "Campo requerido",
		'from' => "De",
		'to' => "A",
		'search' => "Buscar",
		'in' => "en",
		'we_rebuild_at_save' => "Reconstrucción automática",
		'we_publish_at_save' => "Publicar después de salvar",
		'we_new_doc_after_save' => "New Document after saving", // TRANSLATE
		'we_new_folder_after_save' => "New folder after saving", // TRANSLATE
		'we_new_entry_after_save' => "New entry after saving", // TRANSLATE
		'wrapcheck' => "Empaque",
		'static_docs' => "Documentos estáticos",
		'save_templates_before' => "Salvar plantillas por adelantado",
		'specify_docs' => "Documentos con el siguiente criterio",
		'object_docs' => "Todos los objetos",
		'all_docs' => "Todos los documentos",
		'ask_for_editor' => "Preguntar por el editor",
		'cockpit' => "Cockpit", // TRANSLATE
		'introduction' => "Introducción",
		'doctypes' => "Tipos de documentos",
		'content' => "Contenido",
		'site_not_exist' => "La página no existe!",
		'site_not_published' => "Página aún no publicada!",
		'required' => "Entrada de datos requerida",
		'all_rights_reserved' => "Todos los derechos reservados",
		'width' => "Ancho",
		'height' => "Alto",
		'new_username' => "Nuevo nombre de usuario",
		'username' => "Nombre de usuario",
		'password' => "Contraseña",
		'documents' => "Documentos",
		'templates' => "Plantillas",
		'objects' => "Objects", // TRANSLATE
		'licensed_to' => "Autorizado a",
		'left' => "Izquierda",
		'right' => "Derecha",
		'top' => "Cima",
		'bottom' => "Fondo",
		'topleft' => "Izquierda superior",
		'topright' => "Derecha superior",
		'bottomleft' => "Izquierda inferior",
		'bottomright' => "Derecha inferior",
		'true' => "Si",
		'false' => "No", // TRANSLATE
		'showall' => "Mostrar todos",
		'noborder' => "Sin borde",
		'border' => "Borde",
		'align' => "Aliniación",
		'hspace' => "EspacioH",
		'vspace' => "EspacioV",
		'exactfit' => "Acomodo exacto",
		'select_color' => "Seleccionar color",
		'changeUsername' => "Cambiar nombre de usuario",
		'changePass' => "Cambiar contraseña",
		'oldPass' => "Antigua contraseña",
		'newPass' => "Nueva contraseña",
		'newPass2' => "Repetir nueva contraseña",
		'pass_not_confirmed' => "Las entradas no coinciden!",
		'pass_not_match' => "Antigua contraseña incorrecta!",
		'passwd_not_match' => "Las contraseñas no coinciden!",
		'pass_to_short' => "La contraseña debe tener al menos 4 carácteres!",
		'pass_changed' => "Contraseña exitosamente cambiada!",
		'pass_wrong_chars' => "Las contraseñas solo deben contener carácteres alpha-númericos (a-z, A-Z and 0-9)!",
		'username_wrong_chars' => "Username may only contain alpha-numeric characters (a-z, A-Z and 0-9) and '.', '_' or '-'!", // TRANSLATE
		'all' => "Todos",
		'selected' => "Seleccionado",
		'username_to_short' => "El nombre de usuario debe tener al menos 4 carácteres!",
		'username_changed' => "Nombre de usuario exitosamente cambiado!",
		'published' => "Publicado",
		'help_welcome' => "Bienvenido a la Ayuda de webEdition",
		'edit_file' => "Editar archivo",
		'docs_saved' => "Documentos exitosamente salvados!",
		'preview' => "Vista previa",
		'close' => "Cerrar ventana",
		'loginok' => "<strong>Conexión con el sistema ok! Por favor, espere!</strong><br>webEdition abrirá en una nueva ventana. Si esa ventana no se abre, asegurece de que UD no ha bloqueado las ventanas pop-up en su navegador!",
		'apple' => "&#x2318;", // TRANSLATE
		'shift' => "SHIFT", // TRANSLATE
		'ctrl' => "CTRL", // TRANSLATE
		'required_fields' => "Campos requeridos",
		'no_file_uploaded' => "<p class=\"defaultfont\">Por el momento, ningún documento fue cargado.</p>",
		'openCloseBox' => "Abrir/Cerrar",
		'rebuild' => "Reconstruir",
		'unlocking_document' => "Abriendo documento",
		'variant_field' => "Campo variante",
		'redirect_to_login_failed' => "Please press the following link, if you are not redirected within the next 30 seconds ", // TRANSLATE
		'redirect_to_login_name' => "webEdition login", // TRANSLATE
		'untitled' => "Untitled", // TRANSLATE
		'no_document_opened' => "There is no document opened!", // TRANSLATE
		'credits_team' => "webEdition Team", // TRANSLATE
		'developed_further_by' => "developed further by", // TRANSLATE
		'with' => "with the", // TRANSLATE
		'credits_translators' => "Translations", // TRANSLATE
		'credits_thanks' => "Thanks to", // TRANSLATE
		'unable_to_call_ping' => "Connection to server is lost - RPC: Ping!", // TRANSLATE
		'unable_to_call_setpagenr' => "Connection to server is lost - RPC: setPageNr!", // TRANSLATE
		'nightly-build' => "nightly Build", // TRANSLATE
		'alpha' => "Alpha", // TRANSLATE
		'beta' => "Beta", // TRANSLATE
		'rc' => "RC", // TRANSLATE
		'preview' => "preview", // TRANSLATE
		'release' => "official release", // TRANSLATE
		'categorys' => "Categories", // TRANSLATE
		'navigation' => "Navigation", // TRANSLATE
);
