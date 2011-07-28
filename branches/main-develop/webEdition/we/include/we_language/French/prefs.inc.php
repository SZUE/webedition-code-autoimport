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
		'preload' => "Chargement des préférences en cours, un moment s'il vous plaît ...",
		'preload_wait' => "Chargement des préférences",
		/*		 * ***************************************************************************
		 * SAVE
		 * *************************************************************************** */

		'save' => "Enregistrement des préférences en cours, un moment s'il vous plaît ...",
		'save_wait' => "Enregistrement des préférence",
		'saved' => "Les préférences ont été enregistré avec succès.",
		'saved_successfully' => "Préférences enregistrés",
		/*		 * ***************************************************************************
		 * TABS
		 * *************************************************************************** */

		'tab_ui' => "Surface",
		'tab_glossary' => "Glossary", // TRANSLATE
		'tab_extensions' => "Extensions de fichier",
		'tab_editor' => 'Editeur',
		'tab_formmail' => 'Formmail', // TRANSLATE
		'formmail_recipients' => 'Destinataire-Formmail',
		'tab_proxy' => 'Server-Proxy',
		'tab_advanced' => 'Avancé',
		'tab_system' => 'Système',
		'tab_seolinks' => 'SEO links', // TRANSLATE
		'tab_error_handling' => 'Traitement des Erreurs',
		'tab_cockpit' => 'Cockpit', // TRANSLATE
		'tab_cache' => 'Cache', // TRANSLATE
		'tab_language' => 'Languages', // TRANSLATE
		'tab_countries' => 'Countries', // TRANSLATE
		'tab_modules' => 'Modules', // TRANSLATE
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
		'seem_deactivate' => "désactiver  le seeMode ",
		'seem_startdocument' => "Page d'accueil du seeMode ",
		'seem_start_type_document' => "Document", // TRANSLATE
		'seem_start_type_object' => "Object", // TRANSLATE
		'seem_start_type_cockpit' => "Cockpit", // TRANSLATE
		'question_change_to_seem_start' => "Voulez-vous changer au document choisi?",
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
		'dimension' => "Taille de la fenêtre",
		'maximize' => "Maximaliser",
		'specify' => "Spécifier",
		'width' => "Largeur",
		'height' => "Hauteur",
		'predefined' => "Tailles préréglées",
		'show_predefined' => "Afficher les tailles préréglées",
		'hide_predefined' => "Cacher les tailles préréglées",
		/**
		 * TREE
		 */
		'tree_title' => "Menu d'abre",
		'all' => "Tous",
		/*		 * ***************************************************************************
		 * FILE EXTENSIONS
		 * *************************************************************************** */

		/**
		 * FILE EXTENSIONS
		 */
		'extensions_information' => "Set the default file extensions for static and dynamic pages here.", // TRANSLATE

		'we_extensions' => "Extension-webEdition",
		'static' => "Sites statiques",
		'dynamic' => "Sites dynamiques",
		'html_extensions' => "Extension-HTML",
		'html' => "Site-HTML",
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
		'editor_plugin' => 'PlugIn-Editeur',
		'use_it' => "Utiliser",
		'start_automatic' => "Démarrer automatiquement",
		'ask_at_start' => 'En démarrant demander,<br>quel editeur doit<br>être utilisé',
		'must_register' => 'Doit être enregistré',
		'change_only_in_ie' => 'Comme le PlugIn Editor fonctionne seulement sous Windows dans le Internet Explorer, Mozilla, Firebird et Firefox ces préférences ne sont pas modifiables.',
		'install_plugin' => 'Pour que vous puissiez utiliser le Plugin-Editeur avec votre Navigateur, il est nécéssaire d\'installer le PlugIn ActiveX pour Mozilla.',
		'confirm_install_plugin' => 'Le PlugIn ActiveX pour Mozilla , permet d\'intégrer des Controles ActiveX dans le navigateur Mozilla. Le navigateur doit être redémarré après l\'installation .\\n\\nConsidérez: ActiveX peut-être un risque pour la sécurité!\\n\\nContinuer avec l\'installation?',
		'install_editor_plugin' => 'Pour que vous puissiez utilisé le PlugIn dans votre navigateur, vous deviez l\'installer d\'abord.',
		'install_editor_plugin_text' => 'Le Plugin-Editeur de webEdition est installé...',
		/**
		 * TEMPLATE EDITOR
		 */
		'editor_information' => "Specify font and size which should be used for the editing of templates, CSS- and JavaScript files within webEdition.<br /><br />These settings are used for the text editor of the abovementioned file types.", // TRANSLATE

		'editor_mode' => 'Éditeur',
		'editor_font' => 'Police dans l\'editeur',
		'editor_fontname' => 'Type de Police',
		'editor_fontsize' => 'Taille',
		'editor_dimension' => 'Taille de l\'editeur',
		'editor_dimension_normal' => 'Normal',
		/*		 * ***************************************************************************
		 * FORMMAIL RECIPIENTS
		 * *************************************************************************** */

		/**
		 * FORMMAIL RECIPIENTS
		 */
		'formmail_information' => "Saisissez ici tous les adresses e-mail, aux quelles des formulaires avec la fonction-formmail  (&lt;we:form type=\"formmail\" ..&gt;) sont être envoyés.<br><br>Si aucune adresse e-mail est saisie ici, il n'est pas possible d'envoyer des formulaires avec la fonction-Formmail!",
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

		'useproxy' => "Utiliser un Server-Proxy pour<br>la mise à jour en direct",
		'proxyaddr' => "Adresse",
		'proxyport' => "Port", // TRANSLATE
		'proxyuser' => "Nom d'utilisateur",
		'proxypass' => "Mot de passe",
		/*		 * ***************************************************************************
		 * ADVANCED
		 * *************************************************************************** */

		/**
		 * ATTRIBS
		 */
		'default_php_setting' => "Préférences standard pour l'attribut-<br><em>php</em> dans les we:tags",
		/**
		 * INLINEEDIT
		 */
		'inlineedit_default' => "Préférences standard pour<br>l'attribut-<em>inlineedit</em> dans la <br>&lt;we:textarea&gt;",
		'removefirstparagraph_default' => "Default value for the<br><em>removefirstparagraph</em> attribute in<br>&lt;we:textarea&gt;", // TRANSLATE
		'hidenameattribinweimg_default' => "No output of name=xyz in we:img (HTML 5)", // TRANSLATE
		'hidenameattribinweform_default' => "No output of name=xyz in we:form (XHTML strict)", // TRANSLATE

		/**
		 * SAFARI WYSIWYG
		 */
		'safari_wysiwyg' => "Emploi l´editeur WYSIWYG (vérsion beta)",
		'wysiwyg_type' => "Préférences standard pour l'editeur de <em>textarea</em>",

		/**
		 * SHOWINPUTS
		 */
		'showinputs_default' => "Préférences standard pour l'attribut<br><em>showinputs</em> dans <br>&lt;we:img&gt;",
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
		'db_connect' => "Type de connexion-<br>de base de données",
		'db_set_charset' => "Connection charset", // TRANSLATE
		'db_set_charset_information' => "The connection charset is used for the communication between webEdition and datase server.<br/>If no value is specified, the standard connection charset set in PHP is used.<br/>In the ideal case, the webEdition language (i.e. English_UTF-8), the database collation (i.e. utf8_general_ci), the connection charset (i.e. utf8) and the settings of external tools such as phpMyAdmin (i.e. utf-8) are identical. In this case, one can edit database entries with these external tools without problems.", // TRANSLATE
		'db_set_charset_warning' => "The connection charset should be changed only in a fresh installation of webEdition (without data in the database). Otherwise, all non ASCII characters will be interpreted wrong and may be destroyed.", // TRANSLATE


		/**
		 * HTTP AUTHENTICATION
		 */
		'auth' => "Authentification HTTP",
		'useauth' => "Le serveur utilise <br>l'authentification HTTP dans <br>le répertoire webEdition",
		'authuser' => "Nom d'utilisateur",
		'authpass' => "Mot de passe",
		/**
		 * THUMBNAIL DIR
		 */
		'thumbnail_dir' => "Thumbnail directory", // TRANSLATE

		'pagelogger_dir' => "Répertoire de pageLogger",
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
		'error_use_handler' => "Activer le traitement des<br>erreurs de webEdition ",
		/**
		 * ERROR TYPES
		 */
		'error_types' => "Erreurs à traiter",
		'error_notices' => "Renseignements",
		'error_deprecated' => "deprecated Notices", //TRANSLATE
		'error_warnings' => "Avertissements",
		'error_errors' => "Erreurs",
		'error_notices_warning' => 'We recommend to aktivate the option -Log errors- on all systems; the option -Show errors- should be activated only during development.',//TRANSLATE

		/**
		 * ERROR DISPLAY
		 */
		'error_displaying' => "Affichage d'erreur",
		'error_display' => "Afficher les erreurs",
		'error_log' => "Protocoler les erreurs",
		'error_mail' => "Envoyer les erreurs par e-mail",
		'error_mail_address' => "Adresse",
		'error_mail_not_saved' => 'Les erreurs ne vont pas être envoyé à l\'adresse insérere parce que l\'adresse est défectueux!\n\nLes autres préférences ont été enregistrées avec succès.',
		/**
		 * DEBUG FRAME
		 */
		'show_expert' => "Afficher les préférences d'expert",
		'hide_expert' => "Cacher les préférences d'expert",
		'show_debug_frame' => "afficher le Debug-Frame ",
		'debug_normal' => "Dans mode normal",
		'debug_seem' => "Dans le SeeMode",
		'debug_restart' => "Les changement demandent un nouveau démarrage",
		/*		 * ***************************************************************************
		 * MODULES
		 * *************************************************************************** */

		/**
		 * OBJECT MODULE
		 */
		'module_object' => "Module de base de données-/ Objects",
		'tree_count' => "Nombre des objects à afficher",
		'tree_count_description' => "Cette valeure définit le nombre maximal des entrées affichées dans la navigation gauche.",
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
		'validation' => 'Validation', // TRANSLATE
		'xhtml_default' => 'Préférences standard pour l\'attribut <em>xml</em> dans les we:Tags',
		'xhtml_debug_explanation' => 'Le Débogage-XHTML vous aide à créer des site-web valide. Optionel chaque édition d\'un we:Tag peut être vérifié sur sa validité 	et si besoin sur des attributs défectueux. Considérez s\'il vous plaît que ce processus nécessite du temps et il considerable d\'effectuer cette option seulement quand vous créez un nouveau site.',
		'xhtml_debug_headline' => 'Débogage-XHTML',
		'xhtml_debug_html' => 'Activer le Débogage-XHTML ',
		'xhtml_remove_wrong' => 'Enlever les attributs défectueux',
		'xhtml_show_wrong_headline' => 'Notification en cas d\'attributs défectueux',
		'xhtml_show_wrong_html' => 'Activer',
		'xhtml_show_wrong_text_html' => 'Comme texte',
		'xhtml_show_wrong_js_html' => 'Comme Message-JavaScript',
		'xhtml_show_wrong_error_log_html' => 'Dans le Error-Log (PHP)',
		/*		 * ***************************************************************************
		 * max upload size
		 * *************************************************************************** */
		'we_max_upload_size' => "Taille maximale de téléchargement<br>dans les textes de notification",
		'we_max_upload_size_hint' => "(en Mega Octet, 0=automatique)",
		/*		 * ***************************************************************************
		 * we_new_folder_mod
		 * *************************************************************************** */
		'we_new_folder_mod' => "Droits d'accès pour des<br>nouveauxnew répertoires.",
		'we_new_folder_mod_hint' => "(stander est 755)",
		/*		 * ***************************************************************************
		 * we_doctype_workspace_behavior
		 * *************************************************************************** */

		'we_doctype_workspace_behavior_hint0' => "Le répertoire standard d'un type-de-document doit être dans l'éspace de travail de l'utilisateur, pour que l'utilisateur puisse choisir le type-de-document.",
		'we_doctype_workspace_behavior_hint1' => "L'éspace de travail doit être dans le répertoire standard de l'utilisateur, pour que l'utilisateur puisse  choisir le type-de-document.",
		'we_doctype_workspace_behavior_1' => "Inverse", // TRANSLATE
		'we_doctype_workspace_behavior_0' => "Standard", // TRANSLATE
		'we_doctype_workspace_behavior' => "Comportement du choix du type-de-document",
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
		'editor_javascript2' => 'CodeMirror2 (alpha)',
		'editor_javascript_information' => 'The JavaScript editor is still in beta stadium. Depending on which of the following options you\'ll activate, there might occur errors. Code completion is currently not working in Internet Explorer. For a complete list of known issues please have a look at the <a href="http://qa.webedition.org/tracker/search.php?project_id=107&sticky_issues=on&sortby=last_updated&dir=DESC&hide_status_id=90" target="_blank">webEdition bugtracker</a>.', // TRANSLATE


		'juplod_not_installed' => 'JUpload is not installed!', // TRANSLATE
);