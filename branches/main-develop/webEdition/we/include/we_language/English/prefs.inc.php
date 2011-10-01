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
		'preload' => "Loading preferences, one moment ...",
		'preload_wait' => "Loading preferences",
		/*		 * ***************************************************************************
		 * SAVE
		 * *************************************************************************** */

		'save' => "Saving preferences, one moment ...",
		'save_wait' => "Saving preferences",
		'saved' => "Preferences have been saved successfully.",
		'saved_successfully' => "Preferences saved",
		/*		 * ***************************************************************************
		 * TABS
		 * *************************************************************************** */

		'tab_ui' => "User interface",
		'tab_glossary' => "Glossary",
		'tab_extensions' => "File extensions",
		'tab_editor' => 'Editor',
		'tab_formmail' => 'Formmail',
		'formmail_recipients' => 'Formmail recipients',
		'tab_proxy' => 'Proxy Server',
		'tab_advanced' => 'Advanced',
		'tab_system' => 'System',
		'tab_seolinks' => 'SEO links', // TRANSLATE
		'tab_error_handling' => 'Error handling',
		'tab_cockpit' => 'Cockpit',
		'tab_cache' => 'Cache',
		'tab_language' => 'Languages',
		'tab_countries' => 'Countries', // TRANSLATE
		'tab_modules' => 'Modules',
		'tab_versions' => 'Versioning',
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
		'choose_language' => "Backend language",
		'language_notice' => "The backend language/charset change will only take effect everywhere after restarting webEdition.",
		'choose_backendcharset' => "Backend charset",
		/**
		 * CHARSET
		 */
		'default_charset' => "Standard frontend charset",
		/**
		 * SEEM
		 */
		'seem' => "seeMode",
		'seem_deactivate' => "deactivate",
		'seem_startdocument' => "Home",
		'seem_start_type_document' => "Document",
		'seem_start_type_object' => "Object",
		'seem_start_type_cockpit' => "Cockpit",
		'seem_start_type_weapp' => "WE-App",
		'question_change_to_seem_start' => "Do you want to change to the selected document?",
		/**
		 * Sidebar
		 */
		'sidebar' => "Sidebar",
		'sidebar_deactivate' => "deactivate",
		'sidebar_show_on_startup' => "show on startup",
		'sidebar_width' => "Width in pixel",
		'sidebar_document' => "Document",
		/**
		 * WINDOW DIMENSION
		 */
		'dimension' => "Window dimension",
		'maximize' => "Maximize",
		'specify' => "Specify",
		'width' => "Width",
		'height' => "Height",
		'predefined' => "Predefined dimensions",
		'show_predefined' => "Show predefined dimensions",
		'hide_predefined' => "Hide predefined dimensions",
		/**
		 * TREE
		 */
		'tree_title' => "Treemenu",
		'all' => "All",
		/*		 * ***************************************************************************
		 * FILE EXTENSIONS
		 * *************************************************************************** */

		/**
		 * FILE EXTENSIONS
		 */
		'extensions_information' => "Set the default file extensions for static and dynamic pages here.",
		'we_extensions' => "webEdition extensions",
		'static' => "Static pages",
		'dynamic' => "Dynamic pages",
		'html_extensions' => "HTML extensions",
		'html' => "HTML pages",
		/*		 * ***************************************************************************
		 * Glossary
		 * *************************************************************************** */

		'glossary_publishing' => "Check before publishing",
		'force_glossary_check' => "Force glossary check",
		'force_glossary_action' => "Force action",
		/*		 * ***************************************************************************
		 * COCKPIT
		 * *************************************************************************** */

		/**
		 * Cockpit
		 */
		'cockpit_amount_columns' => "Columns in the cockpit ",
		/*		 * ***************************************************************************
		 * CACHING
		 * *************************************************************************** */

		/**
		 * Cache Type
		 */
		'cache_information' => "Set the preset values of the fields \"Caching Type\" and \"Cache lifetime in seconds\" for new templates here.<br /><br />Please note that these setting are only the presets of the fields.",
		'cache_navigation_information' => "Enter the defaults for the &lt;we:navigation&gt; tag here. This value can be overwritten by the attribute \"cachelifetime\" of the &lt;we:navigation&gt; tag.",
		'cache_presettings' => "Presetting",
		'cache_type' => "Caching Type",
		'cache_type_none' => "Caching deactivated",
		'cache_type_full' => "Full cache",
		'cache_type_document' => "Document cache",
		'cache_type_wetag' => "we:Tag cache",
		/**
		 * Cache Life Time
		 */
		'cache_lifetime' => "Cache lifetime in seconds",
		'cache_lifetimes' => array(
				0 => "",
				60 => "1 minute",
				300 => "5 minutes",
				600 => "10 minutes",
				1800 => "30 minutes",
				3600 => "1 hour",
				21600 => "6 hours",
				43200 => "12 hours",
				86400 => "1 day",
		),
		'delete_cache_after' => 'Clear cache after',
		'delete_cache_add' => 'adding a new entry',
		'delete_cache_edit' => 'changing a entry',
		'delete_cache_delete' => 'deleting a entry',
		'cache_navigation' => 'Default setting',
		'default_cache_lifetime' => 'Default cache lifetime',
		/*		 * ***************************************************************************
		 * LOCALES // LANGUAGES
		 * *************************************************************************** */

		/**
		 * Languages
		 */
		'locale_information' => "Add all languages for which you would provide a web page.<br /><br />This preference will be used for the glossary check and the spellchecking.",
		'locale_languages' => "Language",
		'locale_countries' => "Country",
		'locale_add' => "Add language",
		'cannot_delete_default_language' => "The default language cannot be deleted.",
		'language_already_exists' => "This language already exists",
		'language_country_missing' => "Please select also a country",
		'add_dictionary_question' => "Would you like to upload the dictionary for this language?",
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
		'editor_plugin' => 'Editor PlugIn',
		'use_it' => "Use it",
		'start_automatic' => "Start automatically",
		'ask_at_start' => 'Ask on start which<br>editor to be used',
		'must_register' => 'Must be registered',
		'change_only_in_ie' => 'These settings cannot be changed. The Editor PlugIn operates only with the Windows version of Internet Explorer, Mozilla, Firebird as well as Firefox.',
		'install_plugin' => 'To be able to use the Editor PlugIn the Mozilla ActiveX PlugIn must be installed.',
		'confirm_install_plugin' => 'The Mozilla ActiveX PlugIn allows to run ActiveX controls in Mozilla browsers. After the installation you must restart your browser.\\n\\nNote: ActiveX can be a security risk!\\n\\nContinue installation?',
		'install_editor_plugin' => 'To be able to use the webEdition Editor PlugIn, it must be installed.',
		'install_editor_plugin_text' => 'The webEdition Editor Plugin will be installed...',
		/**
		 * TEMPLATE EDITOR
		 */
		'editor_information' => "Specify font and size which should be used for the editing of templates, CSS- and JavaScript files within webEdition.<br /><br />These settings are used for the text editor of the abovementioned file types.",
		'editor_mode' => 'Editor',
		'editor_font' => 'Font in editor',
		'editor_fontname' => 'Fontname',
		'editor_fontsize' => 'Size',
		'editor_dimension' => 'Editor dimension',
		'editor_dimension_normal' => 'Default',
		/*		 * ***************************************************************************
		 * FORMMAIL RECIPIENTS
		 * *************************************************************************** */

		/**
		 * FORMMAIL RECIPIENTS
		 */
		'formmail_information' => "Please enter all email addresses, which should receive forms sent by the formmail function (&lt;we:form type=\"formmail\" ..&gt;).<br><br>If you do not enter an email address, you cannot send forms using the formmail function!",
		'formmail_log' => "Formmail log",
		'log_is_empty' => "The log is empty!",
		'ip_address' => "IP address",
		'blocked_until' => "Blocked until",
		'unblock' => "Unblock",
		'clear_log_question' => "Do you really want to clear the log?",
		'clear_block_entry_question' => "Do you really want to unblock the IP %s ?",
		'forever' => "Always",
		'yes' => "yes",
		'no' => "no",
		'on' => "on",
		'off' => "off",
		'formmailConfirm' => "Formmail confirmation function",
		'logFormmailRequests' => "Log formmail requests",
		'deleteEntriesOlder' => "Delete entries older than",
		'blockFormmail' => "Limit formmail requests",
		'formmailSpan' => "Within the span of time",
		'formmailTrials' => "Requests allowed",
		'blockFor' => "Block for",
		'formmailViaWeDoc' => "Call formmail via webEdition-Dokument.",
		'never' => "never",
		'1_day' => "1 day",
		'more_days' => "%s days",
		'1_week' => "1 week",
		'more_weeks' => "%s weeks",
		'1_year' => "1 year",
		'more_years' => "%s years",
		'1_minute' => "1 minute",
		'more_minutes' => "%s minutes",
		'1_hour' => "1 hour",
		'more_hours' => "%s hours",
		'ever' => "always",
		/*		 * ***************************************************************************
		 * PROXY SERVER
		 * *************************************************************************** */

		/**
		 * PROXY SERVER
		 */
		'proxy_information' => "Specify your Proxy settings for your server here, if your server uses a proxy for the connection with the Internet.",
		'useproxy' => "Use proxy server for<br>live update",
		'proxyaddr' => "Address",
		'proxyport' => "Port",
		'proxyuser' => "User name",
		'proxypass' => "Password",
		/*		 * ***************************************************************************
		 * ADVANCED
		 * *************************************************************************** */

		/**
		 * ATTRIBS
		 */
		'default_php_setting' => "Default settings for<br><em>php</em>-attribut in we:tags",
		/**
		 * INLINEEDIT
		 */
		'inlineedit_default' => "Default value for the<br><em>inlineedit</em> attribute in<br>&lt;we:textarea&gt;",
		'removefirstparagraph_default' => "Default value for the<br><em>removefirstparagraph</em> attribute in<br>&lt;we:textarea&gt;",
		'hidenameattribinweimg_default' => "No output of name=xyz in we:img (HTML 5)",
		'hidenameattribinweform_default' => "No output of name=xyz in we:form (XHTML strict)",
		/**
		 * SAFARI WYSIWYG
		 */
		'safari_wysiwyg' => "Use Safari Wysiwyg<br>editor (beta version)",
		'wysiwyg_type' => "Select editor for textareas",
		/**
		 * SHOWINPUTS
		 */
		'showinputs_default' => "Default value for the<br><em>showinputs</em> attribute in<br>&lt;we:img&gt;",
		/**
		 * NAVIGATION
		 */
		'navigation_entries_from_document' => "Create new navigation entries from the document as",
		'navigation_entries_from_document_item' => "item",
		'navigation_entries_from_document_folder' => "folder",
		'navigation_rules_continue' => "Continue to evaluate navigation rules after a first match",
		'general_directoryindex_hide' => "Hide DirectoryIndex- file names", // TRANSLATE
		'general_directoryindex_hide_description' => "For the tags <we:link>, <we:linklist>, <we:listview> you can use the attribute 'hidedirindex'", // TRANSLATE
		'navigation_directoryindex_hide' => "in the navigation output", // TRANSLATE
		'wysiwyglinks_directoryindex_hide' => "in links from the WYSIWYG editor", // TRANSLATE
		'objectlinks_directoryindex_hide' => "in links to objects", // TRANSLATE
		'navigation_directoryindex_description' => "After a change, a rebuild is required (i.e. navigation cache, objects ...)", // TRANSLATE
		'navigation_directoryindex_names' => "DirectoryIndex file names (comma separated, incl. file extensions, i.e. 'index.php,index.html'", // TRANSLATE
		'general_objectseourls' => "Generate object SEO urls ", // TRANSLATE
		'urlencode_objectseourls' => "URLencode the SEO-urls",// TRANSLATE
		'suppress404code' => "suppress 404 not found",// TRANSLATE
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
		'db_connect' => "Type of database<br>connections",
		'db_set_charset' => "Connection charset",
		'db_set_charset_information' => "The connection charset is used for the communication between webEdition and datase server.<br/>If no value is specified, the standard connection charset set in PHP is used.<br/>In the ideal case, the webEdition language (i.e. English_UTF-8), the database collation (i.e. utf8_general_ci), the connection charset (i.e. utf8) and the settings of external tools such as phpMyAdmin (i.e. utf-8) are identical. In this case, one can edit database entries with these external tools without problems.",
		'db_set_charset_warning' => "The connection charset should be changed only in a fresh installation of webEdition (without data in the database). Otherwise, all non ASCII characters will be interpreted wrong and may be destroyed.",
		/**
		 * HTTP AUTHENTICATION
		 */
		'auth' => "HTTP authentication",
		'useauth' => "Server uses HTTP<br>authentication in the webEdition<br>directory",
		'authuser' => "User name",
		'authpass' => "Password",
		/**
		 * THUMBNAIL DIR
		 */
		'thumbnail_dir' => "Thumbnail directory",
		'pagelogger_dir' => "pageLogger directory",
		/**
		 * HOOKS
		 */
		'hooks' => "Hooks", //TRANSLATE
		'hooks_information' => "The use of hooks allows for the execution of arbitrary any PHP code during storing, publishing, unpublishing and deleting of any content type in webEdition.<br/>
	Further information can be found in the online documentation.<br/><br/>Allow execution of hooks?",
		/**
		 * Backward compatibility
		 */
		'backwardcompatibility' => "Backward compatibility",
		'backwardcompatibility_tagloading' => "Load all 'old' we_tag functions",
		'backwardcompatibility_tagloading_message' => "Only necessary if in custom_tags or in PHP code inside templates we_tags are called in the form we_tag_tagname().<br/> Recommended call: we_tag<br/>('tagname',&#36;attribs,&#36;content)",
		/*		 * ***************************************************************************
		 * ERROR HANDLING
		 * *************************************************************************** */


		'error_no_object_found' => 'Errorpage for not existing objects',
		/**
		 * TEMPLATE TAG CHECK
		 */
		'templates' => "Templates",
		'disable_template_tag_check' => "Deactivate check for missing,<br />closing we:tags",
		'disable_template_code_check' => "Deactivate check for invalid<br />code (php)",
		/**
		 * ERROR HANDLER
		 */
		'error_use_handler' => "Use webEdition error handler",
		/**
		 * ERROR TYPES
		 */
		'error_types' => "Handle these errors",
		'error_notices' => "Notices",
		'error_deprecated' => "deprecated Notices",
		'error_warnings' => "Warnings",
		'error_errors' => "Errors",
		'error_notices_warning' => 'We recommend to aktivate the option -Log errors- on all systems; the option -Show errors- should be activated only during development.',//TRANSLATE
		/**
		 * ERROR DISPLAY
		 */
		'error_displaying' => "Displaying of errors",
		'error_display' => "Show errors",
		'error_log' => "Log errors",
		'error_mail' => "Send a mail",
		'error_mail_address' => "Address",
		'error_mail_not_saved' => 'Errors won\'t be sent to the given e-mail address due to the address is not correct!\n\nThe remaining preferences have been saved successfully.',
		/**
		 * DEBUG FRAME
		 */
		'show_expert' => "Show expert settings",
		'hide_expert' => "Hide expert settings",
		'show_debug_frame' => "Show debug frame",
		'debug_normal' => "In normal mode",
		'debug_seem' => "In seeMode",
		'debug_restart' => "Changes require a restart",
		/*		 * ***************************************************************************
		 * MODULES
		 * *************************************************************************** */

		/**
		 * OBJECT MODULE
		 */
		'module_object' => "DB/Object module",
		'tree_count' => "Number of displayed objects",
		'tree_count_description' => "This value defines the maximum number of items being displayed in the left navigation.",
		/*		 * ***************************************************************************
		 * BACKUP
		 * *************************************************************************** */
		'backup' => "Backup",
		'backup_slow' => "Slow",
		'backup_fast' => "Fast",
		'performance' => "Here you can set an appropriate performance level. The performance level should be adequate to the server system. If the system has limited resources (memory, timeout etc.) choose a slow level, otherwise choose a fast level.",
		'backup_auto' => "Auto",
		/*		 * ***************************************************************************
		 * Validation
		 * *************************************************************************** */
		'validation' => 'Validation',
		'xhtml_default' => 'Default value for the attribute <em>xml</em> in we:Tags',
		'xhtml_debug_explanation' => 'The XHTML debugging will support your development of a xhtml valid web-site. The output of every we:Tag will be checked for validity and misplaced attributes can be displayed or removed. Please note: This action can take some time. Therefore you should only activate xhtml debugging during the development of your web-site.',
		'xhtml_debug_headline' => 'XHTML debugging',
		'xhtml_debug_html' => 'Activate XHTML debugging',
		'xhtml_remove_wrong' => 'Remove invalid attributes',
		'xhtml_show_wrong_headline' => 'Notification of invalid attributes',
		'xhtml_show_wrong_html' => 'Activate',
		'xhtml_show_wrong_text_html' => 'As text',
		'xhtml_show_wrong_js_html' => 'As JavaScript-Alert',
		'xhtml_show_wrong_error_log_html' => 'In the error log (PHP)',
		/*		 * ***************************************************************************
		 * max upload size
		 * *************************************************************************** */
		'we_max_upload_size' => "Max Upload Size<br>displaying in hints",
		'we_max_upload_size_hint' => "(in MByte, 0=automatic)",
		/*		 * ***************************************************************************
		 * we_new_folder_mod
		 * *************************************************************************** */
		'we_new_folder_mod' => "Access rights for<br>new directories",
		'we_new_folder_mod_hint' => "(default is 755)",
		/*		 * ***************************************************************************
		 * we_doctype_workspace_behavior
		 * *************************************************************************** */

		'we_doctype_workspace_behavior_hint0' => "The default directory of a document type has to be located within the work area of the user, thus being able to select the corresponding document type.",
		'we_doctype_workspace_behavior_hint1' => "The user's work area hast to be located within the default directory defined in the document type for the user being able to select the document type.",
		'we_doctype_workspace_behavior_1' => "Inverse",
		'we_doctype_workspace_behavior_0' => "Standard",
		'we_doctype_workspace_behavior' => "Behaviour of the document type selection",
		/*		 * ***************************************************************************
		 * jupload
		 * *************************************************************************** */

		'use_jupload' => 'Use java upload',
		/*		 * ***************************************************************************
		 * message_reporting
		 * *************************************************************************** */
		'message_reporting' => array(
				'information' => "You can decide on the respective check boxes whether you like to receive a notice for webEdition operations as for example saving, publishing or deleting.",
				'headline' => "Notifications",
				'show_notices' => "Show Notices",
				'show_warnings' => "Show Warnings",
				'show_errors' => "Show Errors",
		),
		/*		 * ***************************************************************************
		 * Module Activation
		 * *************************************************************************** */
		'module_activation' => array(
				'information' => "Here you can activate or deactivate your modules if you do not need them.<br />Deactivated modules improve the overall performance of webEdition.<br />For some modules, you have to restart webEdition to activate.<br />The Shop module requires the Customer module, the Workflow module requires the ToDo-Messaging module.",
				'headline' => "Module activation",
		),
		/*		 * ***************************************************************************
		 * Email settings
		 * *************************************************************************** */

		'mailer_information' => "Adjust whether webEditionin should dispatch emails via the integrated PHP function or a seperate SMTP server should be used.<br /><br />When using a SMTP mail server, the risk that messages are classified by the receiver as a \"Spam\" is lowered.",
		'mailer_type' => "Mailer type",
		'mailer_php' => "Use php mail() function",
		'mailer_smtp' => "Use SMTP server",
		'email' => "E-Mail",
		'tab_email' => "E-Mail",
		'smtp_auth' => "Authentication",
		'smtp_server' => "SMTP server",
		'smtp_port' => "SMTP port",
		'smtp_username' => "User name",
		'smtp_password' => "Password",
		'smtp_halo' => "SMTP halo",
		'smtp_timeout' => "SMTP timeout",
		'smtp_encryption' => "encrypted transport",
		'smtp_encryption_none' => "no",
		'smtp_encryption_ssl' => "SSL",
		'smtp_encryption_tls' => "TLS",
		/*		 * ***************************************************************************
		 * Versions settings
		 * *************************************************************************** */

		'versioning' => "Versioning",
		'version_all' => "all",
		'versioning_activate_text' => "Activate versioning for some or all content types.",
		'versioning_time_text' => "If you specify a time period, only versions are saved which are created in this time until today. Older versions will be deleted.",
		'versioning_time' => "Time period",
		'versioning_anzahl_text' => "Number of versions which will be created for each document or object.",
		'versioning_anzahl' => "Number",
		'versioning_wizard_text' => "Open the Version-Wizard to delete or reset versions.",
		'versioning_wizard' => "Open Versions-Wizard",
		'ContentType' => "Content Type",
		'versioning_create_text' => "Determine which actions provoke new versions. Either if you publish or if you save, unpublish, delete or import files, too.",
		'versioning_create' => "Create Version",
		'versions_create_publishing' => "only when publishing",
		'versions_create_always' => "always",
		'versioning_templates_text' => "Define special values for the <b>versioning of templates</b>",
		'versions_create_tmpl_publishing' => "only using special button",
		'versions_create_tmpl_always' => "always",
		'use_jeditor' => "Use",
		'editor_font_colors' => 'Specify font colors',
		'editor_normal_font_color' => 'Default',
		'editor_we_tag_font_color' => 'webEdition tags',
		'editor_we_attribute_font_color' => 'webEdition attributes',
		'editor_html_tag_font_color' => 'HTML tags',
		'editor_html_attribute_font_color' => 'HTML attributes',
		'editor_pi_tag_font_color' => 'PHP code',
		'editor_comment_font_color' => 'Comments',
		'editor_highlight_colors' => 'Highlighting colors',
		'editor_linenumbers' => 'Line numbers',
		'editor_completion' => 'Code Completion',
		'editor_tooltips' => 'Tooltips on we:tags',
		'editor_docuclick' => 'Docu integration',
		'editor_enable' => 'Enable',
		'editor_plaintext' => 'Plain textarea',
		'editor_java' => 'Java editor',
		'editor_javascript' => 'JavaScript editor (beta)',
		'editor_javascript2' => 'CodeMirror2 (alpha)',
		'editor_javascript_information' => 'The JavaScript editor is still in beta stadium. Depending on which of the following options you\'ll activate, there might occur errors. Code completion is currently not working in Internet Explorer. For a complete list of known issues please have a look at the <a href="http://qa.webedition.org/tracker/search.php?project_id=107&sticky_issues=on&sortby=last_updated&dir=DESC&hide_status_id=90" target="_blank">webEdition bugtracker</a>.',
		'juplod_not_installed' => 'JUpload is not installed!',
);
