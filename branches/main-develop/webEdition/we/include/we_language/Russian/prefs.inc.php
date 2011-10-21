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
		'preload' => "Загружаются настройки, подождите, пожалуйста",
		'preload_wait' => "Загрузка настроек",
		/*		 * ***************************************************************************
		 * SAVE
		 * *************************************************************************** */

		'save' => "Сохраняются настройки, подождите, пожалуйста",
		'save_wait' => "Сохранение настроек",
		'saved' => "Сохранение настроек прошло успешно",
		'saved_successfully' => "Настройки сохранены",
		/*		 * ***************************************************************************
		 * TABS
		 * *************************************************************************** */

		'tab_ui' => "Пользовательский интерфейс",
		'tab_glossary' => "Glossary", // TRANSLATE
		'tab_extensions' => "Расширения файлов",
		'tab_editor' => 'Редактор',
		'tab_formmail' => 'Formmail', // TRANSLATE
		'formmail_recipients' => 'Получатели Formmail',
		'tab_proxy' => 'Proxy-сервер',
		'tab_advanced' => 'Специальные настройки',
		'tab_system' => 'Система',
		'tab_seolinks' => 'SEO links', // TRANSLATE
		'tab_error_handling' => 'Ошибки',
		'tab_cockpit' => 'Cockpit', // TRANSLATE
		'tab_cache' => 'Cache', // TRANSLATE
		'tab_language' => 'Languages', // TRANSLATE
		'tab_countries' => 'Countries', // TRANSLATE
		'tab_modules' => 'Модули',
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
		'seem' => "Суперлегкий режим",
		'seem_deactivate' => "Деактивировать режим",
		'seem_startdocument' => "Стартовый документ режима",
		'seem_start_type_document' => "Document", // TRANSLATE
		'seem_start_type_object' => "Object", // TRANSLATE
		'seem_start_type_cockpit' => "Cockpit", // TRANSLATE
		'seem_start_type_weapp' => "WE-App",
		'question_change_to_seem_start' => "Перейти к выбранному документу?",
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
		'dimension' => "Размер окна",
		'maximize' => "Максимизировать",
		'specify' => "Установить",
		'width' => "Ширина",
		'height' => "Высота",
		'predefined' => "Заданные размеры",
		'show_predefined' => "Показать заданные размеры",
		'hide_predefined' => "Скрыть заданные размеры",
		/**
		 * TREE
		 */
		'tree_title' => "Меню дерева",
		'all' => "Все",
		/*		 * ***************************************************************************
		 * FILE EXTENSIONS
		 * *************************************************************************** */

		/**
		 * FILE EXTENSIONS
		 */
		'extensions_information' => "Set the default file extensions for static and dynamic pages here.", // TRANSLATE

		'we_extensions' => "Расширения webEdition",
		'static' => "Статические страницы",
		'dynamic' => "Динамические страницы",
		'html_extensions' => "Расширения HTML",
		'html' => "Страницы HTML",
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
		'add_dictionary_question' => "Would you like to upload the dictionary for this language?",// TRANSLATE
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
		'editor_plugin' => 'Редактор-плагин',
		'use_it' => "Применить",
		'start_automatic' => "Автоматический запуск",
		'ask_at_start' => 'При запуске спрашивать<br>какой редактор применить',
		'must_register' => 'Должен быть зарегистрирован',
		'change_only_in_ie' => 'Данные настройки нельзя изменить. Редактор-плагин работает с Internet Explorer только версии Windows.',
		'install_plugin' => 'Использование редактора-плагин в Вашем браузере зависит от наличия установленного плагина Mozilla ActiveX.',
		'confirm_install_plugin' => 'Плагин Mozilla ActiveX позволяет интеграцию ActiveX Controls в браузере Mozilla. После инсталляции плагина нужно заново запустить браузер.\\n\\nПримите во внимание: ActiveX может представлять угрозу безопасности!\\n\\nПродолжить инсталляцию?',
		'install_editor_plugin' => 'Вначале нужно инсталлировать модуль редактора-плагин webEdition.',
		'install_editor_plugin_text' => 'Редактор-плагин в процессе инсталляции...',
		/**
		 * TEMPLATE EDITOR
		 */
		'editor_information' => "Specify font and size which should be used for the editing of templates, CSS- and JavaScript files within webEdition.<br /><br />These settings are used for the text editor of the abovementioned file types.", // TRANSLATE

		'editor_mode' => 'редактор',
		'editor_font' => 'Шрифт в редакторе',
		'editor_fontname' => 'Название шрифта',
		'editor_fontsize' => 'Размер шрифта',
		'editor_dimension' => 'Размер редактора',
		'editor_dimension_normal' => 'По умолчанию',
		/*		 * ***************************************************************************
		 * FORMMAIL RECIPIENTS
		 * *************************************************************************** */

		/**
		 * FORMMAIL RECIPIENTS
		 */
		'formmail_information' => "Введите, пожалуйста, адреса электронной почты всех получателей форм, рассылаемых с помощью функции formmail (&lt;we:form&nbsp;type=\"formmail\"&nbsp;..&gt;).<br><br>Если адрес email не введен, рассылкой форм с использованием функции formmail воспользоваться нельзя!",
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

		'useproxy' => "Для онлайн обновления<br>использовать proxy-сервер",
		'proxyaddr' => "Адрес",
		'proxyport' => "Порт",
		'proxyuser' => "Имя пользователя",
		'proxypass' => "Пароль",
		/*		 * ***************************************************************************
		 * ADVANCED
		 * *************************************************************************** */

		/**
		 * ATTRIBS
		 */
		'default_php_setting' => "Настройки по умолчанию для<br><em>php</em>-атрибута в we:tags",
		/**
		 * INLINEEDIT
		 */
		'inlineedit_default' => "Значение по умолчанию <br><em>inlineedit</em> атрибута в<br>&lt;we:textarea&gt;",
		'removefirstparagraph_default' => "Default value for the<br><em>removefirstparagraph</em> attribute in<br>&lt;we:textarea&gt;", // TRANSLATE
		'hidenameattribinweimg_default' => "No output of name=xyz in we:img (HTML 5)", // TRANSLATE
		'hidenameattribinweform_default' => "No output of name=xyz in we:form (XHTML strict)", // TRANSLATE

		/**
		 * SAFARI WYSIWYG
		 */
		'safari_wysiwyg' => "Воспользуйтесь редактором<br>Wysiwyg (beta-версии) Safari",
		'wysiwyg_type' => "Select editor for textareas", //TRANSLATE

		/**
		 * SHOWINPUTS
		 */
		'showinputs_default' => "Значение по умолчанию <br><em>showinputs</em> атрибута в<br>&lt;we:img&gt;",
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
		'db_connect' => "Тип соединений<br>базы данных",
		'db_set_charset' => "Connection charset", // TRANSLATE
		'db_set_charset_information' => "The connection charset is used for the communication between webEdition and datase server.<br/>If no value is specified, the standard connection charset set in PHP is used.<br/>In the ideal case, the webEdition language (i.e. English_UTF-8), the database collation (i.e. utf8_general_ci), the connection charset (i.e. utf8) and the settings of external tools such as phpMyAdmin (i.e. utf-8) are identical. In this case, one can edit database entries with these external tools without problems.", // TRANSLATE
		'db_set_charset_warning' => "The connection charset should be changed only in a fresh installation of webEdition (without data in the database). Otherwise, all non ASCII characters will be interpreted wrong and may be destroyed.", // TRANSLATE


		/**
		 * HTTP AUTHENTICATION
		 */
		'auth' => "Аутентификация HTTP",
		'useauth' => "Сервер использует<br>аутентификацию HTTP<br>в директории webEdition",
		'authuser' => "Имя пользователя",
		'authpass' => "Пароль",
		/**
		 * THUMBNAIL DIR
		 */
		'thumbnail_dir' => "Thumbnail directory", // TRANSLATE

		'pagelogger_dir' => "директория pageLogger",
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
		'error_use_handler' => "Активация устранения ошибок",
		/**
		 * ERROR TYPES
		 */
		'error_types' => "Ошибки для устранения",
		'error_notices' => "Примечания",
		'error_deprecated' => "deprecated Notices", //TRANSLATE
		'error_warnings' => "Предостережения",
		'error_errors' => "Ошибки",
		'error_notices_warning' => 'We recommend to aktivate the option -Log errors- on all systems; the option -Show errors- should be activated only during development.',//TRANSLATE

		/**
		 * ERROR DISPLAY
		 */
		'error_displaying' => "Вывод ошибок на экран",
		'error_display' => "Показывать ошибки",
		'error_log' => "Сделать запись об ошибках",
		'error_mail' => "Отправить сообщение",
		'error_mail_address' => "Адрес",
		'error_mail_not_saved' => 'Адрес введен неправильно: ошибки не будут отправлены по этому адресу!\n\nОстальные настройки успешно сохранены.',
		/**
		 * DEBUG FRAME
		 */
		'show_expert' => "Показывать профессиональные настройки",
		'hide_expert' => "Скрыть профессиональные настройки",
		'show_debug_frame' => "Показывать debug frame",
		'debug_normal' => "В обычном режиме",
		'debug_seem' => "Суперлегкое редактирование",
		'debug_restart' => "При изменениях: перезапуск",
		/*		 * ***************************************************************************
		 * MODULES
		 * *************************************************************************** */

		/**
		 * OBJECT MODULE
		 */
		'module_object' => "База данных/объект",
		'tree_count' => "Количество выводимых на экран объектов",
		'tree_count_description' => "Данная величина задает максимальное количество объектов, выводимых на экран в  навигации слева",
		/*		 * ***************************************************************************
		 * BACKUP
		 * *************************************************************************** */
		'backup' => "Backup", // TRANSLATE
		'backup_slow' => "Slow", // TRANSLATE
		'backup_fast' => "Fast", // TRANSLATE
		'performance' => "Here you can set an appropriate performance level. The performance level should be adequate to the server system. If the system has limited resources (memory, timeout etc.) choose a slow level, otherwise choose a fast level.", // TRANSLATE
		'backup_auto' => "автоматический",
		/*		 * ***************************************************************************
		 * Validation
		 * *************************************************************************** */
		'validation' => 'Проверка',
		'xhtml_default' => 'Значение по умолчанию для данного атрибута <em>xml</em> в тегах we:Tags',
		'xhtml_debug_explanation' => 'Приложение по удалению неисправностей для xhtml помогает в разработке веб-сайта, который должен характеризоваться как «xhtml valid». Теги we:Tag проверяются на действительность, неверные атрибуты при этом высвечиваются или удаляются. Примите во внимание: операция по удалению неисправностей занимает некоторое время. Рекомендуется активировать данное приложение по удалению неисправностей только при разработке веб-сайта.',
		'xhtml_debug_headline' => 'Удаление неисправностей XHTML',
		'xhtml_debug_html' => 'Активировать удаление неисправностей XHTML',
		'xhtml_remove_wrong' => 'Удалить неверные атрибуты',
		'xhtml_show_wrong_headline' => 'Оповещение при наличии неверных атрибутов',
		'xhtml_show_wrong_html' => 'Активировать',
		'xhtml_show_wrong_text_html' => 'Как текст',
		'xhtml_show_wrong_js_html' => 'Как JavaScript-Alert',
		'xhtml_show_wrong_error_log_html' => 'Запись ошибок (PHP)',
		/*		 * ***************************************************************************
		 * max upload size
		 * *************************************************************************** */
		'we_max_upload_size' => "Максимально возможный объем <br>отображаемый в подсказке",
		'we_max_upload_size_hint' => "(в MByte, 0=automatic)",
		/*		 * ***************************************************************************
		 * we_new_folder_mod
		 * *************************************************************************** */
		'we_new_folder_mod' => "Права доступа к<br>новым директориям",
		'we_new_folder_mod_hint' => "(по умолчанию 755)",
		/*		 * ***************************************************************************
		 * we_doctype_workspace_behavior
		 * *************************************************************************** */

		'we_doctype_workspace_behavior_hint0' => "Директория по умолчанию данного типа документа должна быть расположена в рабочей области пользователя, для предоставления ему возможности выбора соответствующего типа документа.",
		'we_doctype_workspace_behavior_hint1' => "Рабочая область данного пользователя должна быть расположена в директории по умолчанию, заданной в типе документа пользователя, имеющего право выбора типа документа.",
		'we_doctype_workspace_behavior_1' => "инверсное ",
		'we_doctype_workspace_behavior_0' => "стандартное",
		'we_doctype_workspace_behavior' => "Поведение выбранного типа документа",
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

	 'urlencode_objectseourls' => "URLencode the SEO-urls",// TRANSLATE
	 'suppress404code' => "suppress 404 not found",// TRANSLATE

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
