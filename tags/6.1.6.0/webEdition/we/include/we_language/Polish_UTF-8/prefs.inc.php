<?php

/**
 * webEdition CMS
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
		'preload' => "Ładowanie ustawień, zaczekaj chwilę ...",
		'preload_wait' => "Ładuję ustawienia",
		/*		 * ***************************************************************************
		 * SAVE
		 * *************************************************************************** */

		'save' => "Zapisano ustawienia, zaczekaj chwilę ...",
		'save_wait' => "Zapisuję ustawienia",
		'saved' => "Ustawienia zostały zapamiętane.",
		'saved_successfully' => "Zapisano ustawienia",
		/*		 * ***************************************************************************
		 * TABS
		 * *************************************************************************** */

		'tab_ui' => "Interfejs",
		'tab_glossary' => "Glossary", // TRANSLATE
		'tab_extensions' => "Rozszerzenia plików",
		'tab_editor' => 'Edytor',
		'tab_formmail' => 'Formmail', // TRANSLATE
		'formmail_recipients' => 'Odbiorca formularza poczty',
		'tab_proxy' => 'Serwer Proxy',
		'tab_advanced' => 'Zaawansowane',
		'tab_system' => 'System', // TRANSLATE
		'tab_seolinks' => 'SEO links', // TRANSLATE
		'tab_error_handling' => 'Obsługa błędów',
		'tab_cockpit' => 'Cockpit', // TRANSLATE
		'tab_cache' => 'Cache', // TRANSLATE
		'tab_language' => 'Languages', // TRANSLATE
		'tab_countries' => 'Countries', // TRANSLATE
		'tab_modules' => 'Moduły',
		'tab_versions' => 'Versioning', // TRANSLATE

		/*		 * ***************************************************************************
		 * USER INTERFACE
		 * *************************************************************************** */
		/**
		 * Countries
		 */
		'countries_information' => "Select the countries, which are available in the customer module, shop-module and so on.", // TRANSLATE
		'countries_headline' => "Country selection", // TRANSLATE
		'countries_country' => "Country", // TRANSLATE
		'countries_top' => "top list", // TRANSLATE
		'countries_show' => "display", // TRANSLATE
		'countries_noshow' => "no display", // TRANSLATE

		/**
		 * LANGUAGE
		 */
		'choose_language' => "Język",
		'language_notice' => "The language change will only take effect everywhere after restarting webEdition.", // TRANSLATE

		/**
		 * CHARSET
		 */
		'default_charset' => "Standard charset", // TRANSLATE


		/**
		 * SEEM
		 */
		'seem' => "seeMode", // TRANSLATE
		'seem_deactivate' => "Wyłącz seeMode",
		'seem_startdocument' => "Dokument startowy - seeMode",
		'seem_start_type_document' => "Document", // TRANSLATE
		'seem_start_type_object' => "Object", // TRANSLATE
		'seem_start_type_cockpit' => "Cockpit", // TRANSLATE
		'question_change_to_seem_start' => "Chcesz zamienić na wybrany dokument?",
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
		'dimension' => "Wielkość okna",
		'maximize' => "Maksymalizuj",
		'specify' => "Ustaw",
		'width' => "Szerokość",
		'height' => "Wysokość",
		'predefined' => "Wymary domyślne",
		'show_predefined' => "Wyświetl wymiary domyślne",
		'hide_predefined' => "Wyłacz wymiary domyślne",
		/**
		 * TREE
		 */
		'tree_title' => "Tytuł drzewa",
		'all' => "Wszystkie",
		/*		 * ***************************************************************************
		 * FILE EXTENSIONS
		 * *************************************************************************** */

		/**
		 * FILE EXTENSIONS
		 */
		'extensions_information' => "Set the default file extensions for static and dynamic pages here.", // TRANSLATE

		'we_extensions' => "Rozszerzenia webEdition",
		'static' => "Strony Statyczne",
		'dynamic' => "Strony dynamiczne",
		'html_extensions' => "Rozszerzenia HTML",
		'html' => "Strony HTML",
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
		'langlink_support' => "Automatic language links",// TRANSLATE
		'langlink_support_backlinks' => "Generate back links automatically",// TRANSLATE

		/*		 * ***************************************************************************
		 * EDITOR
		 * *************************************************************************** */

		/**
		 * EDITOR PLUGIN
		 */
		'editor_plugin' => 'Rozszerzenie edytora',
		'use_it' => "Użyj",
		'start_automatic' => "Uruchom automatycznie",
		'ask_at_start' => 'Zapytaj przy starcie, który edytor<br> ma być używany<br>',
		'must_register' => 'Musisz być zarejestrowany',
		'change_only_in_ie' => 'Ponieważ rozszerzenie edytora działa tylko w systemie Windows w przeglądarkach Internet Explorer, Mozilla Firebird oraz Firefox nie można zmienić tych ustawień.',
		'install_plugin' => 'Żeby można było wykorzystać rozszerzenie edytora w twojej przeglądarce, powinienieś zainstalować Mozilla ActiveX PlugIn.',
		'confirm_install_plugin' => 'Mozilla ActiveX PlugIn umożliwia zintegrowanie kontrolek ActiveX w przeglądarce Mozilla. Po instalacji należy na nowo uruchomić przeglądarkę.\\n\\nPamiętaj: ActiveX może stanowić ryzyko dla bezpieczeństwa!\\n\\nKontynuować instalację?',
		'install_editor_plugin' => 'Żeby używać rozszerzenia edytora w Twojej przeglądarce, musisz go zainstalować.',
		'install_editor_plugin_text' => 'Rozszerzenie edytora dla webEdition zostanie zainstalowane...',
		/**
		 * TEMPLATE EDITOR
		 */
		'editor_information' => "Specify font and size which should be used for the editing of templates, CSS- and JavaScript files within webEdition.<br /><br />These settings are used for the text editor of the abovementioned file types.", // TRANSLATE

		'editor_mode' => 'Editor', // TRANSLATE
		'editor_font' => 'Czcionka w edytorze',
		'editor_fontname' => 'Krój pisma',
		'editor_fontsize' => 'Wielkość',
		'editor_dimension' => 'Wielkość edytora',
		'editor_dimension_normal' => 'Normalna',
		/*		 * ***************************************************************************
		 * FORMMAIL RECIPIENTS
		 * *************************************************************************** */

		/**
		 * FORMMAIL RECIPIENTS
		 */
		'formmail_information' => "Wpisz tutaj wszystkie adresy e-mail, do których mogą być wysyłane formularze za pomocą funkcji Formmail (&lt;we:form type=\"formmail\" ..&gt;) .<br><br>Jeżeli nie wpisano tu żadnych adresów e-mail, to nie można wysyłać formularzy za pomocą funkcji Formmail!",
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

		'useproxy' => "Użyj Serwera Proxy do aktualizacji Live-Update<br>",
		'proxyaddr' => "Adres",
		'proxyport' => "Port", // TRANSLATE
		'proxyuser' => "Nazwa użytkownika",
		'proxypass' => "Hasło",
		/*		 * ***************************************************************************
		 * ADVANCED
		 * *************************************************************************** */

		/**
		 * ATTRIBS
		 */
		'default_php_setting' => "Standardowe ustawienie dla<br>atrybutu <em>php</em> w we:tags",
		/**
		 * INLINEEDIT
		 */
		'inlineedit_default' => "Standardowe ustawienie dla<br>atrybutu <em>inlineedit</em> w<br>&lt;we:textarea&gt;",
		'removefirstparagraph_default' => "Default value for the<br><em>removefirstparagraph</em> attribute in<br>&lt;we:textarea&gt;", // TRANSLATE
		'hidenameattribinweimg_default' => "No output of name=xyz in we:img (HTML 5)", // TRANSLATE
		'hidenameattribinweform_default' => "No output of name=xyz in we:form (XHTML strict)", // TRANSLATE

		/**
		 * SAFARI WYSIWYG
		 */
		'safari_wysiwyg' => "Użyj edytora Wysiwyg<br>Safari (wersja beta)",
		/**
		 * SHOWINPUTS
		 */
		'showinputs_default' => "Standardowe ustawienie dla <br>atrybutu <em>showinputs</em> w <br>&lt;we:img&gt;",
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
		'db_connect' => "Rodzaj połączeń <br>bazą danych",
		'db_set_charset' => "Connection charset", // TRANSLATE
		'db_set_charset_information' => "The connection charset is used for the communication between webEdition and datase server.<br/>If no value is specified, the standard connection charset set in PHP is used.<br/>In the ideal case, the webEdition language (i.e. English_UTF-8), the database collation (i.e. utf8_general_ci), the connection charset (i.e. utf8) and the settings of external tools such as phpMyAdmin (i.e. utf-8) are identical. In this case, one can edit database entries with these external tools without problems.", // TRANSLATE
		'db_set_charset_warning' => "The connection charset should be changed only in a fresh installation of webEdition (without data in the database). Otherwise, all non ASCII characters will be interpreted wrong and may be destroyed.", // TRANSLATE


		/**
		 * HTTP AUTHENTICATION
		 */
		'auth' => "Autentyfikacja HTTP",
		'useauth' => "Serwer stosuje Autentyfikację HTTP<br>w katalogu webEdition<br>",
		'authuser' => "Nazwa użytkownika",
		'authpass' => "Hasło",
		/**
		 * THUMBNAIL DIR
		 */
		'thumbnail_dir' => "Thumbnail directory", // TRANSLATE

		'pagelogger_dir' => "Katalog pageLoggera",
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

		/*		 * ***************************************************************************
		 * ERROR HANDLING
		 * *************************************************************************** */


		'error_no_object_found' => 'Errorpage for not existing objects', // TRANSLATE

		/**
		 * TEMPLATE TAG CHECK
		 */
		'templates' => "Templates", // TRANSLATE
		'disable_template_tag_check' => "Deactivate check for missing,<br />closing we:tags", // TRANSLATE

		/**
		 * ERROR HANDLER
		 */
		'error_use_handler' => "Włącz obsługe błędów webEdition <br>",
		/**
		 * ERROR TYPES
		 */
		'error_types' => "Do obsługiwanego błędu",
		'error_notices' => "Wskazówki",
		'error_deprecated' => "deprecated Notices", //TRANSLATE
		'error_warnings' => "Ostrzeżenia",
		'error_errors' => "Błędy",
		'error_notices_warning' => 'Option for developers! Do not activate on live-systems.', // TRANSLATE

		/**
		 * ERROR DISPLAY
		 */
		'error_displaying' => "Wyświetlanie błędów",
		'error_display' => "Wyświetl błąd",
		'error_log' => "Rejestruj błędy",
		'error_mail' => "Wyślij e-mail z informacją o błędzie",
		'error_mail_address' => "Adresy",
		'error_mail_not_saved' => 'Błedy nie zostaną wysłane na podany przez Ciebie adres, ponieważ adres ten podano błędnie!\n\nZapisano pozostałe ustawienia.',
		/**
		 * DEBUG FRAME
		 */
		'show_expert' => "Wyświetl ustawienia eksperta",
		'hide_expert' => "Ukryj ustawienia eksperta",
		'show_debug_frame' => "Wyświetl Debug-Frame",
		'debug_normal' => "W trybie normalnym",
		'debug_seem' => "W trybie SeeModus",
		'debug_restart' => "Zmiany wymagają ponownego uruchomienia",
		/*		 * ***************************************************************************
		 * MODULES
		 * *************************************************************************** */

		/**
		 * OBJECT MODULE
		 */
		'module_object' => "Moduł DB/Obiekt",
		'tree_count' => "Liczba obiektów do wyświetlenia",
		'tree_count_description' => "Wartość ta podaje maksymalną liczbę wpisów do wyświetlenia w lewym oknie nawigacji.",
		/*		 * ***************************************************************************
		 * BACKUP
		 * *************************************************************************** */
		'backup' => "Backup", // TRANSLATE
		'backup_slow' => "Slow", // TRANSLATE
		'backup_fast' => "Fast", // TRANSLATE
		'performance' => "Here you can set an appropriate performance level. The performance level should be adequate to the server system. If the system has limited resources (memory, timeout etc.) choose a slow level, otherwise choose a fast level.", // TRANSLATE
		'backup_auto' => "Auto", // TRANSLATE

		/*		 * ***************************************************************************
		 * Validation
		 * *************************************************************************** */
		'validation' => 'Walidacja',
		'xhtml_default' => 'Standardowe ustawienie atrybutu <em>xml</em> w we:Tags',
		'xhtml_debug_explanation' => 'Wyszukiwanie błędów w XHTML (Debugging) wspierasz tworząc bezbłedne strony WWW. Opcjonalnie można sprawdzić każde wystąpienie znacznika we:Tags pod kątem ważności a w razie potrzeby usunąć bądź wyswietlić błędne atrybuty. Pamiętaj, że proces ten wymaga trochę czasu i może być używany tylko w trakcie tworzenia nowej strony WWW.',
		'xhtml_debug_headline' => 'XHTML-Debugging',
		'xhtml_debug_html' => 'Włącz Debugging XHTML',
		'xhtml_remove_wrong' => 'Usuń błędne atrybuty',
		'xhtml_show_wrong_headline' => 'Powiadomienie przy błędnych atrybutach',
		'xhtml_show_wrong_html' => 'Włącz',
		'xhtml_show_wrong_text_html' => 'Jako tekst',
		'xhtml_show_wrong_js_html' => 'Jako komunikat JavaScript',
		'xhtml_show_wrong_error_log_html' => 'W logu błędów (PHP)',
		/*		 * ***************************************************************************
		 * max upload size
		 * *************************************************************************** */
		'we_max_upload_size' => "Maksymalna wielkość uploadu w<br>tekstach wskazówek",
		'we_max_upload_size_hint' => "(w MB, 0=automatycznie)",
		/*		 * ***************************************************************************
		 * we_new_folder_mod
		 * *************************************************************************** */
		'we_new_folder_mod' => "Prawa dostępu do <br>nowych katalogów",
		'we_new_folder_mod_hint' => "(Standardowo 755)",
		/*		 * ***************************************************************************
		 * we_doctype_workspace_behavior
		 * *************************************************************************** */

		'we_doctype_workspace_behavior_hint0' => "Standardowy katalog typu dokumentu musi się znajdować wewnątrz obszaru roboczego użytkownika, aby użytkownik mógł zmieniać typ dokumentu.",
		'we_doctype_workspace_behavior_hint1' => "Obszar roboczy użytkownika musi się znajdować wewnątrz ustawionego w typie dokumentu katalogu standadardowego, aby użytkownik mógł zminiać typ dokumentu.",
		'we_doctype_workspace_behavior_1' => "Odwrotnie",
		'we_doctype_workspace_behavior_0' => "Standardowo",
		'we_doctype_workspace_behavior' => "Wybór zachowania typu dokumentu",
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
				'information' => "Here you can activate or deactivate your modules if you do not need them.<br />Deactivated modules improve the overall performance of webEdition. <br />For some modules, you have to restart webEdition to activate.<br />The Shop module requires the Customer module, the Workflow module requires the ToDo-Messaging module.",
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
		'editor_javascript_information' => 'The JavaScript editor is still in beta stadium. Depending on which of the following options you\'ll activate, there might occur errors. Code completion is currently not working in Internet Explorer. For a complete list of known issues please have a look at the <a href="http://qa.webedition.org/tracker/search.php?project_id=107&sticky_issues=on&sortby=last_updated&dir=DESC&hide_status_id=90" target="_blank">webEdition bugtracker</a>.', // TRANSLATE


		'juplod_not_installed' => 'JUpload is not installed!', // TRANSLATE
);