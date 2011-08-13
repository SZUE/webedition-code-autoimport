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
 * Language file: prefs.inc.php
 * Provides language strings.
 * Language: English
 */
/* * ***************************************************************************
 * PRELOAD
 * *************************************************************************** */
$l_prefs = array(
		'preload' => "Cargando preferencias, un momento ...",
		'preload_wait' => "Cargando preferencias",
		/*		 * ***************************************************************************
		 * SAVE
		 * *************************************************************************** */

		'save' => "Salvando preferencias, un momento ...",
		'save_wait' => "Salvando preferencias",
		'saved' => "Las preferencias han sido salvadas exitosamente.",
		'saved_successfully' => "Preferencias salvadas",
		/*		 * ***************************************************************************
		 * TABS
		 * *************************************************************************** */

		'tab_ui' => "Interfaz del usuario",
		'tab_glossary' => "Glossary", // TRANSLATE
		'tab_extensions' => "Extensiones de archivo",
		'tab_editor' => 'Editor', // TRANSLATE
		'tab_formmail' => 'Formas de correos',
		'formmail_recipients' => 'Destinatarios de formas de correos',
		'tab_proxy' => 'Servidor proxy',
		'tab_advanced' => 'Advanzada',
		'tab_system' => 'Sistema',
		'tab_seolinks' => 'SEO links', // TRANSLATE
		'tab_error_handling' => 'Manejo de error',
		'tab_cockpit' => 'Cockpit', // TRANSLATE
		'tab_cache' => 'Cache', // TRANSLATE
		'tab_language' => 'Languages', // TRANSLATE
		'tab_countries' => 'Countries', // TRANSLATE
		'tab_modules' => 'Módulos',
		'tab_versions' => 'Versioning', // TRANSLATE

		/*		 * ***************************************************************************
		 * USER INTERFACE
		 * *************************************************************************** */
		/**
		 * Countries
		 */
		'countries_information' => "Select the countries, which are available in the customer module, shop-module and so on.  The default value (Code '--') - if filled - will be shown on top of the list, possible values are i.e. 'please choose' or '--'.", // TRANSLATE
		'countries_headline' => "Country selection", // TRANSLATE
		'countries_default' => "Default value",
		'countries_country' => "Country", // TRANSLATE
		'countries_top' => "top list", // TRANSLATE
		'countries_show' => "display", // TRANSLATE
		'countries_noshow' => "no display", // TRANSLATE

		/**
		 * LANGUAGE
		 */
		'choose_language' => "Backend language",// TRANSLATE
		'language_notice' => "The backend language/charset change will only take effect everywhere after restarting webEdition.",// TRANSLATE
		'choose_backendcharset' => "Backend charset",// TRANSLATE
		/**
		 * CHARSET
		 */
		'default_charset' => "Standard frontend charset",// TRANSLATE


		/**
		 * SEEM
		 */
		'seem' => "seeMode", // TRANSLATE
		'seem_deactivate' => "Desactivar seeMode",
		'seem_startdocument' => "Documento inicio de seeMode",
		'seem_start_type_document' => "Document", // TRANSLATE
		'seem_start_type_object' => "Object", // TRANSLATE
		'seem_start_type_cockpit' => "Cockpit", // TRANSLATE
		'question_change_to_seem_start' => "Desea Ud cambiar el documento seleccionado?",
		/**
		 * Sidebar
		 */
		'sidebar' => "Sidebar", // TRANSLATE
		'sidebar_deactivate' => "deactivate", // TRANSLATE
		'sidebar_show_on_startup' => "show on startup", // TRANSLATE
		'sidebar_width' => "Width in pixel", // TRANSLATE
		'sidebar_document' => "Document", // TRANSLATE


		/**
		 * WINDOW DIMENSION
		 */
		'dimension' => "Dimensiones de ventana",
		'maximize' => "Máximizar",
		'specify' => "Especificar",
		'width' => "Ancho",
		'height' => "Alto",
		'predefined' => "Dimensiones predefinidas",
		'show_predefined' => "Mostrar dimensiones predefinidas",
		'hide_predefined' => "Ocultar dimensiones predefinidas",
		/**
		 * TREE
		 */
		'tree_title' => "Menú en árbol",
		'all' => "Todos",
		/*		 * ***************************************************************************
		 * FILE EXTENSIONS
		 * *************************************************************************** */

		/**
		 * FILE EXTENSIONS
		 */
		'extensions_information' => "Set the default file extensions for static and dynamic pages here.", // TRANSLATE

		'we_extensions' => "Extensiones webEdition ",
		'static' => "Páginas estáticas",
		'dynamic' => "Páginas dinámicas",
		'html_extensions' => "Extensiones HTML",
		'html' => "Páginas HTML",
		/*		 * ***************************************************************************
		 * Glossary
		 * *************************************************************************** */

		'glossary_publishing' => "Check before publishing", // TRANSLATE
		'force_glossary_check' => "Force glossary check", // TRANSLATE
		'force_glossary_action' => "Force action", // TRANSLATE

		/*		 * ***************************************************************************
		 * COCKPIT
		 * *************************************************************************** */

		/**
		 * Cockpit
		 */
		'cockpit_amount_columns' => "Columns in the cockpit ", // TRANSLATE


		/*		 * ***************************************************************************
		 * CACHING
		 * *************************************************************************** */

		/**
		 * Cache Type
		 */
		'cache_information' => "Set the preset values of the fields \"Caching Type\" and \"Cache lifetime in seconds\" for new templates here.<br /><br />Please note that these setting are only the presets of the fields.", // TRANSLATE
		'cache_navigation_information' => "Enter the defaults for the &lt;we:navigation&gt; tag here. This value can be overwritten by the attribute \"cachelifetime\" of the &lt;we:navigation&gt; tag.", // TRANSLATE

		'cache_presettings' => "Presetting", // TRANSLATE
		'cache_type' => "Caching Type", // TRANSLATE
		'cache_type_none' => "Caching deactivated", // TRANSLATE
		'cache_type_full' => "Full cache", // TRANSLATE
		'cache_type_document' => "Document cache", // TRANSLATE
		'cache_type_wetag' => "we:Tag cache", // TRANSLATE

		/**
		 * Cache Life Time
		 */
		'cache_lifetime' => "Cache lifetime in seconds", // TRANSLATE

		'cache_lifetimes' => array(
				0 => "",
				60 => "1 minute", // TRANSLATE
				300 => "5 minutes", // TRANSLATE
				600 => "10 minutes", // TRANSLATE
				1800 => "30 minutes", // TRANSLATE
				3600 => "1 hour", // TRANSLATE
				21600 => "6 hours", // TRANSLATE
				43200 => "12 hours", // TRANSLATE
				86400 => "1 day", // TRANSLATE
		),
		'delete_cache_after' => 'Clear cache after', // TRANSLATE
		'delete_cache_add' => 'adding a new entry', // TRANSLATE
		'delete_cache_edit' => 'changing a entry', // TRANSLATE
		'delete_cache_delete' => 'deleting a entry', // TRANSLATE
		'cache_navigation' => 'Default setting', // TRANSLATE
		'default_cache_lifetime' => 'Default cache lifetime', // TRANSLATE


		/*		 * ***************************************************************************
		 * LOCALES // LANGUAGES
		 * *************************************************************************** */

		/**
		 * Languages
		 */
		'locale_information' => "Add all languages for which you would provide a web page.<br /><br />This preference will be used for the glossary check and the spellchecking.", // TRANSLATE

		'locale_languages' => "Language", // TRANSLATE
		'locale_countries' => "Country", // TRANSLATE
		'locale_add' => "Add language", // TRANSLATE
		'cannot_delete_default_language' => "The default language cannot be deleted.", // TRANSLATE
		'language_already_exists' => "This language already exists", // TRANSLATE
		'language_country_missing' => "Please select also a country", // TRANSLATE
		'add_dictionary_question' => "Would you like to upload the dictionary for this language?", // TRANSLATE
		'langlink_headline' => "Support for setting links between different languages",
		'langlink_information' => "With this option, you can set the links to corresponding language versions of documents/objects in the backend and open/create etc. these documents/oobjects.<br/>For the frontend you can display these links in a listview type=languagelink.<br/><br/>For folders, you can define a <b>document</b> in each language, which is used if for a document within the folder no corresponding document in the other language is set.",
		'langlink_support' => "active",
		'langlink_support_backlinks' => "Generate back links automatically",// TRANSLATE
		'langlink_support_backlinks_information' => "Back links can be generated automatically for documents/objects (not folders). The other document should not be open in an editor tab!",
		'langlink_support_recursive' => "Generate language links recursive",// TRANSLATE
		'langlink_support_recursive_information' => "Setting of langauge links can be done recursively for documents/objects (but not folders). This sets all possible links and tries to establish the language-circle as fast as possible. The other documents should not be open in an editor tab!",

		/*		 * ***************************************************************************
		 * EDITOR
		 * *************************************************************************** */

		/**
		 * EDITOR PLUGIN
		 */
		'editor_plugin' => 'PlugIn del Editor',
		'use_it' => "Usarlo",
		'start_automatic' => "Iniciar automáticamente",
		'ask_at_start' => 'Preguntar al iniciar cuál<br>editor será usado',
		'must_register' => 'Debe estar registrado',
		'change_only_in_ie' => 'Estos ajustes no pueden ser cambiados. El PlugIn del Editor opera solamente con la versión Windows del Internet Explorer, Mozilla, Firebird, así como también Firefox.',
		'install_plugin' => 'Para poder usar el PlugIn del Editor, el Mozilla ActiveX PlugIn debe de estar instalado.',
		'confirm_install_plugin' => 'El Mozilla ActiveX PlugIn le permite correr los controles ActiveX en el navegador Mozilla . Después de la instalación, Ud debe reiniciar su navegador.\\n\\nNota: ActiveX puede ser un riesgo de seguridad!\\n\\nContinuar la instalación?',
		'install_editor_plugin' => 'Para poder usar el Plugin del editor de webEdition, este debe estar instalado.',
		'install_editor_plugin_text' => 'El Plugin del editor de webEdition será instalado...',
		/**
		 * TEMPLATE EDITOR
		 */
		'editor_information' => "Specify font and size which should be used for the editing of templates, CSS- and JavaScript files within webEdition.<br /><br />These settings are used for the text editor of the abovementioned file types.", // TRANSLATE

		'editor_mode' => 'Editor', // TRANSLATE
		'editor_font' => 'Tipo de letra en el editor',
		'editor_fontname' => 'Tipo de letra',
		'editor_fontsize' => 'Tamaño de fuente',
		'editor_dimension' => 'Dimensión del Editor',
		'editor_dimension_normal' => 'Predeterminado',
		/*		 * ***************************************************************************
		 * FORMMAIL RECIPIENTS
		 * *************************************************************************** */

		/**
		 * FORMMAIL RECIPIENTS
		 */
		'formmail_information' => "Por favor, entre todas las direcciones de E-Mail que recibirán los formularios enviadas por la función formas de correos (&lt;we:form type=\"formmail\" ..&gt;).<br><br>Si UD no entra una dirección de E-Mail, no podrá mandar formas usando la función de formas de correos!", // CHECK
// changed from: "Please enter all E-Mail addresses, which should receive forms sent by the formmail function (&lt;we:form type=\"formmail\" ..&gt;).<br><br>If you do not enter an E-Mail address, you cannot send forms using the formmail function!"
// changed to  : "Please enter all email addresses, which should receive forms sent by the formmail function (&lt;we:form type=\"formmail\" ..&gt;).<br><br>If you do not enter an email address, you cannot send forms using the formmail function!"


		'formmail_log' => "Formmail log", // TRANSLATE
		'log_is_empty' => "The log is empty!", // TRANSLATE
		'ip_address' => "IP address", // TRANSLATE
		'blocked_until' => "Blocked until", // TRANSLATE
		'unblock' => "Unblock", // TRANSLATE
		'clear_log_question' => "Do you really want to clear the log?", // TRANSLATE
		'clear_block_entry_question' => "Do you really want to unblock the IP %s ?", // TRANSLATE
		'forever' => "Always", // TRANSLATE
		'yes' => "yes", // TRANSLATE
		'no' => "no", // TRANSLATE
		'on' => "on", // TRANSLATE
		'off' => "off", // TRANSLATE
		'formmailConfirm' => "Formmail confirmation function", // TRANSLATE
		'logFormmailRequests' => "Log formmail requests", // TRANSLATE
		'deleteEntriesOlder' => "Delete entries older than", // TRANSLATE
		'blockFormmail' => "Limit formmail requests", // TRANSLATE
		'formmailSpan' => "Within the span of time", // TRANSLATE
		'formmailTrials' => "Requests allowed", // TRANSLATE
		'blockFor' => "Block for", // TRANSLATE
		'formmailViaWeDoc' => "Call formmail via webEdition-Dokument.", // TRANSLATE
		'never' => "never", // TRANSLATE
		'1_day' => "1 day", // TRANSLATE
		'more_days' => "%s days", // TRANSLATE
		'1_week' => "1 week", // TRANSLATE
		'more_weeks' => "%s weeks", // TRANSLATE
		'1_year' => "1 year", // TRANSLATE
		'more_years' => "%s years", // TRANSLATE
		'1_minute' => "1 minute", // TRANSLATE
		'more_minutes' => "%s minutes", // TRANSLATE
		'1_hour' => "1 hour", // TRANSLATE
		'more_hours' => "%s hours", // TRANSLATE
		'ever' => "always", // TRANSLATE





		/*		 * ***************************************************************************
		 * PROXY SERVER
		 * *************************************************************************** */

		/**
		 * PROXY SERVER
		 */
		'proxy_information' => "Specify your Proxy settings for your server here, if your server uses a proxy for the connection with the Internet.", // TRANSLATE

		'useproxy' => "Usar servidor proxy para la<br>Actualización en vivo",
		'proxyaddr' => "Dirección",
		'proxyport' => "Puerto",
		'proxyuser' => "Nombre de usuario",
		'proxypass' => "Contraseña",
		/*		 * ***************************************************************************
		 * ADVANCED
		 * *************************************************************************** */

		/**
		 * ATTRIBS
		 */
		'default_php_setting' => "Ajustes predefinidos para<br><em>php</em>-el atributo en we:tags",
		/**
		 * INLINEEDIT
		 */
		'inlineedit_default' => "Valor por defecto para el atributo <br><em>inlineedit</em> en <br>&lt;we:textarea&gt;",
		'removefirstparagraph_default' => "Default value for the<br><em>removefirstparagraph</em> attribute in<br>&lt;we:textarea&gt;", // TRANSLATE
		'hidenameattribinweimg_default' => "No output of name=xyz in we:img (HTML 5)", // TRANSLATE
		'hidenameattribinweform_default' => "No output of name=xyz in we:form (XHTML strict)", // TRANSLATE

		/**
		 * SAFARI WYSIWYG
		 */
		'safari_wysiwyg' => "Use el Wysiwyg editor<br>de Safari (versión beta)",
		'wysiwyg_type' => "Select editor for textareas", //TRANSLATE
		/**
		 * SHOWINPUTS
		 */
		'showinputs_default' => "Valor por defecto para el atributo<br><em>showinputs</em> en<br>&lt;we:img&gt;",
		/**
		 * NAVIGATION
		 */
		'navigation_entries_from_document' => "Create new navigation entries from the document as", // TRANSLATE
		'navigation_entries_from_document_item' => "item", // TRANSLATE
		'navigation_entries_from_document_folder' => "folder", // TRANSLATE
		'navigation_rules_continue' => "Continue to evaluate navigation rules after a first match", // TRANSLATE
		'general_directoryindex_hide' => "Hide DirectoryIndex- file names", // TRANSLATE
		'general_directoryindex_hide_description' => "For the tags <we:link>, <we:linklist>, <we:listview> you can use the attribute 'hidedirindex'", // TRANSLATE
		'navigation_directoryindex_hide' => "in the navigation output", // TRANSLATE
		'wysiwyglinks_directoryindex_hide' => "in links from the WYSIWYG editor", // TRANSLATE
		'objectlinks_directoryindex_hide' => "in links to objects", // TRANSLATE
		'navigation_directoryindex_description' => "After a change, a rebuild is required (i.e. navigation cache, objects ...)", // TRANSLATE
		'navigation_directoryindex_names' => "DirectoryIndex file names (comma separated, incl. file extensions, i.e. 'index.php,index.html'", // TRANSLATE
		'general_objectseourls' => "Generate object SEO urls ", // TRANSLATE
		'navigation_objectseourls' => "in the navigation output", // TRANSLATE
		'wysiwyglinks_objectseourls' => "in links from the WYSIWYG editor", // TRANSLATE
		'general_objectseourls_description' => "For the tags <we:link>, <we:linklist>, <we:listview>, <we:object> you can use the attribute 'objectseourls'", // TRANSLATE
		'taglinks_directoryindex_hide' => "preset value for tags", // TRANSLATE
		'taglinks_objectseourls' => "preset value for tags", // TRANSLATE
		'general_seoinside' => "Usage within webEdition ", // TRANSLATE
		'general_seoinside_description' => "If DirectoryIndex- file names and object SEO urls are used within webEdition, webEdition can not identify internal links and clicks on these links do not open the editor. With the following options, you can decide if they are are used in editmode and in the preview.", // TRANSLATE
		'seoinside_hideinwebedition' => "Hide in preview", // TRANSLATE
		'seoinside_hideineditmode' => "Hide in editmode", // TRANSLATE

		'navigation' => "Navigation", // TRANSLATE


		/**
		 * DATABASE
		 */
		'db_connect' => "Tipo de conexiones de<br> base de datos",
		'db_set_charset' => "Connection charset", // TRANSLATE
		'db_set_charset_information' => "The connection charset is used for the communication between webEdition and datase server.<br/>If no value is specified, the standard connection charset set in PHP is used.<br/>In the ideal case, the webEdition language (i.e. English_UTF-8), the database collation (i.e. utf8_general_ci), the connection charset (i.e. utf8) and the settings of external tools such as phpMyAdmin (i.e. utf-8) are identical. In this case, one can edit database entries with these external tools without problems.", // TRANSLATE
		'db_set_charset_warning' => "The connection charset should be changed only in a fresh installation of webEdition (without data in the database). Otherwise, all non ASCII characters will be interpreted wrong and may be destroyed.", // TRANSLATE


		/**
		 * HTTP AUTHENTICATION
		 */
		'auth' => "Autentificación HTTP",
		'useauth' => "El servidor usa autentificación<br>HTTP en el directorio<br>webEdition",
		'authuser' => "Nombre de usuario",
		'authpass' => "Contraseña",
		/**
		 * THUMBNAIL DIR
		 */
		'thumbnail_dir' => "Directorio de imágenes en miniatura",
		'pagelogger_dir' => "Directorio pageLogger",
		/**
		 * HOOKS
		 */
		'hooks' => "Hooks", // TRANSLATE
		'hooks_information' => "The use of hooks allows for the execution of arbitrary any PHP code during storing, publishing, unpublishing and deleting of any content type in webEdition.<br/>
	Further information can be found in the online documentation.<br/><br/>Allow execution of hooks?", //TRANSLATE

		/**
		 * Backward compatibility
		 */
		'backwardcompatibility' => "Backward compatibility", //TRANSLATE
		'backwardcompatibility_tagloading' => "Load all 'old' we_tag functions", //TRANSLATE
		'backwardcompatibility_tagloading_message' => "Only necessary if in custom_tags or in PHP code inside templates we_tags are called in the form we_tag_tagname().<br/> Recommended call: we_tag<br/>('tagname',&#36;attribs,&#36;content)", //TRANSLATE
		'urlencode_objectseourls' => "URLencode the SEO-urls", // TRANSLATE
	 'suppress404code' => "suppress 404 not found",// TRANSLATE

		/*		 * ***************************************************************************
		 * ERROR HANDLING
		 * *************************************************************************** */


		'error_no_object_found' => 'Errorpage for not existing objects', // TRANSLATE

		/**
		 * TEMPLATE TAG CHECK
		 */
		'templates' => "Templates", // TRANSLATE
		'disable_template_tag_check' => "Deactivate check for missing,<br />closing we:tags", // TRANSLATE
		'disable_template_code_check' => "Deactivate check for invalid<br />code (php)",

		/**
		 * ERROR HANDLER
		 */
		'error_use_handler' => "Usar el proceso de tratamiento<br>de errores de webEdition",
		/**
		 * ERROR TYPES
		 */
		'error_types' => "Tratar estos errores",
		'error_notices' => "Avisos",
		'error_deprecated' => "deprecated Notices", //TRANSLATE
		'error_warnings' => "Advertencias",
		'error_errors' => "Errores",
		'error_notices_warning' => 'We recommend to aktivate the option -Log errors- on all systems; the option -Show errors- should be activated only during development.',//TRANSLATE

		/**
		 * ERROR DISPLAY
		 */
		'error_displaying' => "Desplegando errores",
		'error_display' => "Mostrar errores",
		'error_log' => "Reportar errores",
		'error_mail' => "Enviar un correos",
		'error_mail_address' => "Direción",
		'error_mail_not_saved' => 'Los errores no serán enviados a la dirección de E-mail dada porque la dirección no es correcta!\n\nLa preferencias restantes han sido salvadas exitosamente.',
		/**
		 * DEBUG FRAME
		 */
		'show_expert' => "Mostrar ajustes del experto",
		'hide_expert' => "Ocultar ajustes del experto",
		'show_debug_frame' => "Mostrar marco del depurador",
		'debug_normal' => "En modo normal",
		'debug_seem' => "En seeMode",
		'debug_restart' => "Los cambios requieren de un reinicio",
		/*		 * ***************************************************************************
		 * MODULES
		 * *************************************************************************** */

		/**
		 * OBJECT MODULE
		 */
		'module_object' => "Módulo Base de datos/Objeto",
		'tree_count' => "Número de objetos mostrados",
		'tree_count_description' => "Este valor define el número máximo de objetos que son mostrados en la navegación a la izquierda.",
		/*		 * ***************************************************************************
		 * BACKUP
		 * *************************************************************************** */
		'backup' => "Reserva",
		'backup_slow' => "Lento",
		'backup_fast' => "Rápido",
		'performance' => "Aqui Ud puede ajustar un nivel apropiado de ejecución. El nivel de ejecución debe ser adecuado al sistema de su servidor. Si su sistema tiene recursos limitados (memoria, pausas, etc) Ud debe escoger un nivel menor sino puede escoger un nivel mayor.",
		'backup_auto' => "Auto", // TRANSLATE

		/*		 * ***************************************************************************
		 * Validation
		 * *************************************************************************** */
		'validation' => 'Validación',
		'xhtml_default' => 'Valor por defecto para el atributo <em>xml</em> en we:Tags',
		'xhtml_debug_explanation' => '´La depuración de XHTML soportará su desarrollo de un sitio web xhtml válido. La salida de cada we:Tag será chequeada para su validez y los atributos mal colocados pueden ser mostrados o removidos. Por favor notar: Esta acción puede tardar algún tiempo. En consecuencia usted debe activar la depuración xhtml solo durante el desarrollo de su sitio web.', // CHECK
// changed from: 'The XHTML debugging will support your development of a xhtml valid web-site. The output of every we:Tag will be checked for validity and misplaced attribs can be displayed or removed. Please note: This action can take some time. Therefore you should only activate xhtml debugging during the development of your web-site.'
// changed to  : 'The XHTML debugging will support your development of a xhtml valid web-site. The output of every we:Tag will be checked for validity and misplaced attributes can be displayed or removed. Please note: This action can take some time. Therefore you should only activate xhtml debugging during the development of your web-site.'

		'xhtml_debug_headline' => 'Depuración XHTML',
		'xhtml_debug_html' => 'Activar depuración XHTML',
		'xhtml_remove_wrong' => 'Remover atributos no válidos',
		'xhtml_show_wrong_headline' => 'Notificación de atributos no válidos',
		'xhtml_show_wrong_html' => 'Activar',
		'xhtml_show_wrong_text_html' => 'Como texto',
		'xhtml_show_wrong_js_html' => 'Como Alerta-JavaScript',
		'xhtml_show_wrong_error_log_html' => 'En el log (PHP) de error',
		/*		 * ***************************************************************************
		 * max upload size
		 * *************************************************************************** */
		'we_max_upload_size' => "El tamaño máximo de subida<br>mostrándose en señales",
		'we_max_upload_size_hint' => "(en MByte, 0=automatic)",
		/*		 * ***************************************************************************
		 * we_new_folder_mod
		 * *************************************************************************** */
		'we_new_folder_mod' => "Derechos de acceso<br>nuevos directorios",
		'we_new_folder_mod_hint' => "(valor predeterminado es 755)",
		/*		 * ***************************************************************************
		 * we_doctype_workspace_behavior
		 * *************************************************************************** */

		'we_doctype_workspace_behavior_hint0' => "El directorio por defecto de un tipo de documento ha de estar localizado dentro del área de trabajo del usuario, de esta manera puede seleccionar el tipo de documento correspondiente.",
		'we_doctype_workspace_behavior_hint1' => "El área de trabajo del usuario ha de estar localizada dentro del directorio por defecto definido en el tipo de documento por el usuario pudiendo de esta manera seleccionar el tipo de documento.",
		'we_doctype_workspace_behavior_1' => "Inverso",
		'we_doctype_workspace_behavior_0' => "Standard", // TRANSLATE
		'we_doctype_workspace_behavior' => "Comportamiento de la selección del tipo de documento",
		/*		 * ***************************************************************************
		 * jupload
		 * *************************************************************************** */

		'use_jupload' => 'Use java upload', // TRANSLATE

		/*		 * ***************************************************************************
		 * message_reporting
		 * *************************************************************************** */
		'message_reporting' => array(
				'information' => "You can decide on the respective check boxes whether you like to receive a notice for webEdition operations as for example saving, publishing or deleting.", // TRANSLATE

				'headline' => "Notifications", // TRANSLATE
				'show_notices' => "Show Notices", // TRANSLATE
				'show_warnings' => "Show Warnings", // TRANSLATE
				'show_errors' => "Show Errors", // TRANSLATE
		),
		/*		 * ***************************************************************************
		 * Module Activation
		 * *************************************************************************** */
		'module_activation' => array(
				'information' => "Here you can activate or deactivate your modules if you do not need them.<br />Deactivated modules improve the overall performance of webEdition.<br />For some modules, you have to restart webEdition to activate.<br />The Shop module requires the Customer module, the Workflow module requires the ToDo-Messaging module.", // TRANSLATE

				'headline' => "Module activation", // TRANSLATE
		),
		/*		 * ***************************************************************************
		 * Email settings
		 * *************************************************************************** */

		'mailer_information' => "Adjust whether webEditionin should dispatch emails via the integrated PHP function or a seperate SMTP server should be used.<br /><br />When using a SMTP mail server, the risk that messages are classified by the receiver as a \"Spam\" is lowered.", // TRANSLATE

		'mailer_type' => "Mailer type", // TRANSLATE
		'mailer_php' => "Use php mail() function", // TRANSLATE
		'mailer_smtp' => "Use SMTP server", // TRANSLATE
		'email' => "E-Mail", // TRANSLATE
		'tab_email' => "E-Mail", // TRANSLATE
		'smtp_auth' => "Authentication", // TRANSLATE
		'smtp_server' => "SMTP server", // TRANSLATE
		'smtp_port' => "SMTP port", // TRANSLATE
		'smtp_username' => "User name", // TRANSLATE
		'smtp_password' => "Password", // TRANSLATE
		'smtp_halo' => "SMTP halo", // TRANSLATE
		'smtp_timeout' => "SMTP timeout", // TRANSLATE
		'smtp_encryption' => "encrypted transport", // TRANSLATE
		'smtp_encryption_none' => "no", // TRANSLATE
		'smtp_encryption_ssl' => "SSL", // TRANSLATE
		'smtp_encryption_tls' => "TLS", // TRANSLATE


		/*		 * ***************************************************************************
		 * Versions settings
		 * *************************************************************************** */

		'versioning' => "Versioning", // TRANSLATE
		'version_all' => "all", // TRANSLATE
		'versioning_activate_text' => "Activate versioning for some or all content types.", // TRANSLATE
		'versioning_time_text' => "If you specify a time period, only versions are saved which are created in this time until today. Older versions will be deleted.", // TRANSLATE
		'versioning_time' => "Time period", // TRANSLATE
		'versioning_anzahl_text' => "Number of versions which will be created for each document or object.", // TRANSLATE
		'versioning_anzahl' => "Number", // TRANSLATE
		'versioning_wizard_text' => "Open the Version-Wizard to delete or reset versions.", // TRANSLATE
		'versioning_wizard' => "Open Versions-Wizard", // TRANSLATE
		'ContentType' => "Content Type", // TRANSLATE
		'versioning_create_text' => "Determine which actions provoke new versions. Either if you publish or if you save, unpublish, delete or import files, too.", // TRANSLATE
		'versioning_create' => "Create Version", // TRANSLATE
		'versions_create_publishing' => "only when publishing", // TRANSLATE
		'versions_create_always' => "always", // TRANSLATE
		'versioning_templates_text' => "Define special values for the <b>versioning of templates</b>", // TRANSLATE
		'versions_create_tmpl_publishing' => "only using special button", // TRANSLATE
		'versions_create_tmpl_always' => "always", // TRANSLATE


		'use_jeditor' => "Use", // TRANSLATE
		'editor_font_colors' => 'Specify font colors', // TRANSLATE
		'editor_normal_font_color' => 'Default', // TRANSLATE
		'editor_we_tag_font_color' => 'webEdition tags', // TRANSLATE
		'editor_we_attribute_font_color' => 'webEdition attributes', // TRANSLATE
		'editor_html_tag_font_color' => 'HTML tags', // TRANSLATE
		'editor_html_attribute_font_color' => 'HTML attributes', // TRANSLATE
		'editor_pi_tag_font_color' => 'PHP code', // TRANSLATE
		'editor_comment_font_color' => 'Comments', // TRANSLATE
		'editor_highlight_colors' => 'Highlighting colors', // TRANSLATE
		'editor_linenumbers' => 'Line numbers', // TRANSLATE
		'editor_completion' => 'Code Completion', // TRANSLATE
		'editor_tooltips' => 'Tooltips on we:tags', // TRANSLATE
		'editor_docuclick' => 'Docu integration', // TRANSLATE
		'editor_enable' => 'Enable', // TRANSLATE
		'editor_plaintext' => 'Plain textarea', // TRANSLATE
		'editor_java' => 'Java editor', // TRANSLATE
		'editor_javascript' => 'JavaScript editor (beta)', // TRANSLATE
		'editor_javascript2' => 'CodeMirror2 (alpha)',
		'editor_javascript_information' => 'The JavaScript editor is still in beta stadium. Depending on which of the following options you\'ll activate, there might occur errors. Code completion is currently not working in Internet Explorer. For a complete list of known issues please have a look at the <a href="http://qa.webedition.org/tracker/search.php?project_id=107&sticky_issues=on&sortby=last_updated&dir=DESC&hide_status_id=90" target="_blank">webEdition bugtracker</a>.', // TRANSLATE


		'juplod_not_installed' => 'JUpload is not installed!', // TRANSLATE
);
