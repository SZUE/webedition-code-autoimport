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
		'preload' => "Bezig met laden van voorkeuren, even geduld a.u.b. ...",
		'preload_wait' => "Bezig met laden van voorkeuren",
		/*		 * ***************************************************************************
		 * SAVE
		 * *************************************************************************** */

		'save' => "Bezig met bewaren van voorkeuren, even geduld a.u.b. ...",
		'save_wait' => "Bezig met bewaren van voorkeuren",
		'saved' => "Voorkeuren zijn succesvol bewaard.",
		'saved_successfully' => "Voorkeuren zijn bewaard",
		/*		 * ***************************************************************************
		 * TABS
		 * *************************************************************************** */

		'tab_ui' => "Gebruikers-interface",
		'tab_glossary' => "Verklarende woordenlijst",
		'tab_extensions' => "Bestands-extensies",
		'tab_editor' => 'Editor', // TRANSLATE
		'tab_formmail' => 'Mailformulier',
		'formmail_recipients' => 'Mailformulier ontvangers',
		'tab_proxy' => 'Proxy-Server',
		'tab_advanced' => 'Geavanceerd',
		'tab_system' => 'Systeem',
		'tab_seolinks' => 'SEO links', // TRANSLATE
		'tab_error_handling' => 'Fout afhandeling',
		'tab_cockpit' => 'Cockpit', // TRANSLATE
		'tab_cache' => 'Cache', // TRANSLATE
		'tab_language' => 'Talen',
		'tab_countries' => 'Countries', // TRANSLATE
		'tab_modules' => 'Modules', // TRANSLATE
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
		'choose_language' => "Taal",
		'language_notice' => "Het wisselen van taal wordt pas zichtbaar na het opnieuw inloggen in webEdition.",
		/**
		 * CHARSET
		 */
		'default_charset' => "Standard charset", // TRANSLATE

		/**
		 * SEEM
		 */
		'seem' => "seeModus",
		'seem_deactivate' => "Deactiveer seeMode",
		'seem_startdocument' => "seeMode startdocument",
		'seem_start_type_document' => "Document", // TRANSLATE
		'seem_start_type_object' => "Object", // TRANSLATE
		'seem_start_type_cockpit' => "Cockpit", // TRANSLATE
		'question_change_to_seem_start' => "Wilt u het geselecteerde document wijzigen?",
		/**
		 * Sidebar
		 */
		'sidebar' => "Sidebar", // TRANSLATE
		'sidebar_deactivate' => "deactivateer",
		'sidebar_show_on_startup' => "toon bij opstarten",
		'sidebar_width' => "Breedte in pixels",
		'sidebar_document' => "Document", // TRANSLATE


		/**
		 * WINDOW DIMENSION
		 */
		'dimension' => "Schermdimensie",
		'maximize' => "Maximaliseer",
		'specify' => "Specificeer",
		'width' => "Breedte",
		'height' => "Hoogte",
		'predefined' => "Vooraf bepaalde dimensies",
		'show_predefined' => "Toon vooraf bepaalde dimensies",
		'hide_predefined' => "Verberg vooraf bepaalde dimensies",
		/**
		 * TREE
		 */
		'tree_title' => "Boomstructuur",
		'all' => "Alles",
		/*		 * ***************************************************************************
		 * FILE EXTENSIONS
		 * *************************************************************************** */

		/**
		 * FILE EXTENSIONS
		 */
		'extensions_information' => "Stel hier de standaard extensie voor statische en dynamische pagina's in.",
		'we_extensions' => "webEdition extensies",
		'static' => "Statische pagina's",
		'dynamic' => "Dynamische pagina's",
		'html_extensions' => "HTML extensies",
		'html' => "HTML pagina's",
		/*		 * ***************************************************************************
		 * Glossary
		 * *************************************************************************** */

		'glossary_publishing' => "Controleer voor publiceren",
		'force_glossary_check' => "Forceer controle verklarende woordenlijst",
		'force_glossary_action' => "Forceer actie",
		/*		 * ***************************************************************************
		 * COCKPIT
		 * *************************************************************************** */

		/**
		 * Cockpit
		 */
		'cockpit_amount_columns' => "Kolommen in de cockpit ",
		/*		 * ***************************************************************************
		 * CACHING
		 * *************************************************************************** */

		/**
		 * Cache Type
		 */
		'cache_information' => "Stel vooraf de waarden van de velden \"Caching Type\" en \"Cache levensduur in seconden\" voor nieuwe sjablonen hier in.<br /><br />Let er wel op dat deze instellingen alleen de voorkeur zijn voor de velden.",
		'cache_navigation_information' => "Voer hier de standaard waarden in voor de &lt;we:navigation&gt;. De waarde kan overcshreven worden door het attribuut \"cachelifetime\" van de &lt;we:navigation&gt; tag.",
		'cache_presettings' => "Vooraf instellen",
		'cache_type' => "Caching Type", // TRANSLATE
		'cache_type_none' => "Caching uitgeschakeld",
		'cache_type_full' => "Volledige cache",
		'cache_type_document' => "Document cache", // TRANSLATE
		'cache_type_wetag' => "we:Tag cache", // TRANSLATE

		/**
		 * Cache Life Time
		 */
		'cache_lifetime' => "Cache levensduur in seconden",
		'cache_lifetimes' => array(
				0 => "",
				60 => "1 minuut",
				300 => "5 minuten",
				600 => "10 minuten",
				1800 => "30 minuten",
				3600 => "1 uur",
				21600 => "6 uur",
				43200 => "12 uur",
				86400 => "1 dag",
		),
		'delete_cache_after' => 'Cache legen na',
		'delete_cache_add' => 'Nieuwe invoer toevoegen',
		'delete_cache_edit' => 'Invoer wijzigen',
		'delete_cache_delete' => 'Invoer verwijderen',
		'cache_navigation' => 'Standaard instellingen',
		'default_cache_lifetime' => 'Standaard cache levensduur',
		/*		 * ***************************************************************************
		 * LOCALES // LANGUAGES
		 * *************************************************************************** */

		/**
		 * Languages
		 */
		'locale_information' => "Voeg alle talen toe die u wilt gebruiken.<br /><br />Deze voorkeur wordt gebruikt voor de woordenlijst controle en de spellingscontrole.",
		'locale_languages' => "Taal",
		'locale_countries' => "Land",
		'locale_add' => "Voeg taal toe",
		'cannot_delete_default_language' => "De standaard taal kon niet verwijderd worden.",
		'language_already_exists' => "Deze taal bestaat al",
		'language_country_missing' => "Please select also a country", // TRANSLATE
		'add_dictionary_question' => "Wilt u het woordenboek voor deze taal uploaden?",
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
		'editor_plugin' => 'Editor PlugIn', // TRANSLATE
		'use_it' => "Gebruik",
		'start_automatic' => "Start automatisch",
		'ask_at_start' => 'Vraag bij opstart welke<br>editor er gebruikt moet worden',
		'must_register' => 'Moet registreerd zijn',
		'change_only_in_ie' => 'Deze instellingen kunnen niet gewijzigd worden. De Editor PlugIn werkt alleen met de Windows versie van Internet Explorer, Mozilla, Firebird evenals Firefox.',
		'install_plugin' => 'Om de Editor PlugIn te kunnen gebruiken moet de Mozilla ActiveX PlugIn geïnstalleerd zijn.',
		'confirm_install_plugin' => 'De Mozilla ActiveX PlugIn maakt het mogelijk om ActiveX controls te draaien in Mozilla browsers. Na de installatie moet u uw browser herstarten.\\n\\nLet op: ActiveX kan een veiligheids risico met zich meebrengen!\\n\\nVerder gaan met installeren?',
		'install_editor_plugin' => 'Om gebruik te kunnen maken van de webEdition Editor PlugIn, moet deze geïnstalleerd zijn.',
		'install_editor_plugin_text' => 'De webEdition Editor Plugin wordt geïnstalleerd...',
		/**
		 * TEMPLATE EDITOR
		 */
		'editor_information' => "Specificeer lettertype en grootte die gebruikt moet worden bij het wijzigen van sjablonen, CSS- en JavaScript bestanden binnen webEdition.<br /><br />Deze instellingen worden gebruikt voor de tekst editor van de bovengenoemde bestands types.",
		'editor_mode' => 'Editor',
		'editor_font' => 'Lettertype in editor',
		'editor_fontname' => 'Fontnaam',
		'editor_fontsize' => 'Grootte',
		'editor_dimension' => 'Editor dimensie',
		'editor_dimension_normal' => 'Standaard',
		/*		 * ***************************************************************************
		 * FORMMAIL RECIPIENTS
		 * *************************************************************************** */

		/**
		 * FORMMAIL RECIPIENTS
		 */
		'formmail_information' => "Voer a.u.b. alle E-mail adressen in, welke formulieren moeten ontvangen verstuurd door de formmail functie (&lt;we:form type=\"formmail\" ..&gt;).<br><br>Als u geen E-Mail adres invoert, kunt u geen formulieren verzenden met de formmail functie!",
		'formmail_log' => "Formmail log", // TRANSLATE
		'log_is_empty' => "De log is leeg!",
		'ip_address' => "IP adres",
		'blocked_until' => "Geblokkeerd tot",
		'unblock' => "Deblokkeer",
		'clear_log_question' => "Weet u zeker dat u de log wilt wissen?",
		'clear_block_entry_question' => "Weet u zeker dat u de IP %s wilt deblokkeren?",
		'forever' => "Altijd",
		'yes' => "ja",
		'no' => "nee",
		'on' => "aan",
		'off' => "uit",
		'formmailConfirm' => "Formmail bevestigings functie",
		'logFormmailRequests' => "Log formmail aanvragen",
		'deleteEntriesOlder' => "Verwijder invoeren ouder dan",
		'blockFormmail' => "Beperk formmail aanvragen",
		'formmailSpan' => "Binnen een tijdspanne van",
		'formmailTrials' => "Aanvragen toegestaan",
		'blockFor' => "Blokkeer voor",
		'formmailViaWeDoc' => "Call formmail via webEdition-Dokument.", // TRANSLATE
		'never' => "nooit",
		'1_day' => "1 dag",
		'more_days' => "%s dagen",
		'1_week' => "1 week", // TRANSLATE
		'more_weeks' => "%s weken",
		'1_year' => "1 year", // TRANSLATE
		'more_years' => "%s years", // TRANSLATE
		'1_minute' => "1 minuut",
		'more_minutes' => "%s minuten",
		'1_hour' => "1 uur",
		'more_hours' => "%s uren",
		'ever' => "altijd",
		/*		 * ***************************************************************************
		 * PROXY SERVER
		 * *************************************************************************** */

		/**
		 * PROXY SERVER
		 */
		'proxy_information' => "Specificeer hier uw Proxy instellingen voor uw server, indien uw server een proxy gebruikt voor verbinding met het internet.",
		'useproxy' => "Gebruik proxy-server voor<br>Live-Update",
		'proxyaddr' => "Adres",
		'proxyport' => "Poort",
		'proxyuser' => "Gebruikersnaam",
		'proxypass' => "Wachtwoord",
		/*		 * ***************************************************************************
		 * ADVANCED
		 * *************************************************************************** */

		/**
		 * ATTRIBS
		 */
		'default_php_setting' => "Standaard instellingen voor<br><em>php</em>-attribuut in we:tags",
		/**
		 * INLINEEDIT
		 */
		'inlineedit_default' => "Standaard waarde voor het<br><em>inlineedit</em> attribuut in<br>&lt;we:textarea&gt;",
		'removefirstparagraph_default' => "Default value for the<br><em>removefirstparagraph</em> attribute in<br>&lt;we:textarea&gt;", // TRANSLATE
		'hidenameattribinweimg_default' => "No output of name=xyz in we:img (HTML 5)", // TRANSLATE
		'hidenameattribinweform_default' => "No output of name=xyz in we:form (XHTML strict)", // TRANSLATE

		/**
		 * SAFARI WYSIWYG
		 */
		'safari_wysiwyg' => "Gebruik Safari Wysiwyg<br>editor (beta versie)",
		'wysiwyg_type' => "Select editor for textareas", //translate
		/**
		 * SHOWINPUTS
		 */
		'showinputs_default' => "Standaard waarde voor de<br><em>showinputs</em> attribuut in<br>&lt;we:img&gt;",
		/**
		 * NAVIGATION
		 */
		'navigation_entries_from_document' => "Create new navigation entries from the document as", // TRANSLATE
		'navigation_entries_from_document_item' => "item", // TRANSLATE
		'navigation_entries_from_document_folder' => "folder", // TRANSLATE
		'navigation_rules_continue' => "Continue to evaluate navigation rules after a first match", // TRANSLATE
		'general_directoryindex_hide' => "Hide DirectoryIndex- file names", // TRANSLATE
		'general_directoryindex_hide_description' => "For the tags <we:link>, <we:linklist>, <we:listview> you can use the attribute 'hidedirindex'", // TRANSLATE
		'urlencode_objectseourls' => "URLencode the SEO-urls",// TRANSLATE
		'suppress404code' => "suppress 404 not found",// TRANSLATE
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
		'db_connect' => "Type database<br>connecties", // TRANSLATE
		'db_set_charset' => "Connection charset", // TRANSLATE
		'db_set_charset_information' => "The connection charset is used for the communication between webEdition and datase server.<br/>If no value is specified, the standard connection charset set in PHP is used.<br/>In the ideal case, the webEdition language (i.e. English_UTF-8), the database collation (i.e. utf8_general_ci), the connection charset (i.e. utf8) and the settings of external tools such as phpMyAdmin (i.e. utf-8) are identical. In this case, one can edit database entries with these external tools without problems.", // TRANSLATE
		'db_set_charset_warning' => "The connection charset should be changed only in a fresh installation of webEdition (without data in the database). Otherwise, all non ASCII characters will be interpreted wrong and may be destroyed.", // TRANSLATE


		/**
		 * HTTP AUTHENTICATION
		 */
		'auth' => "HTTP authenticatie",
		'useauth' => "Server gebruikt HTTP<br>authenticatie in de webEdition<br>directory",
		'authuser' => "Gebruikersnaam",
		'authpass' => "Wachtwoord",
		/**
		 * THUMBNAIL DIR
		 */
		'thumbnail_dir' => "Thumbnail directorie",
		'pagelogger_dir' => "pageLogger directorie",
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


		'error_no_object_found' => 'Foutmeldingspagina voor niet bestaande objecten',
		/**
		 * TEMPLATE TAG CHECK
		 */
		'templates' => "Sjablonen",
		'disable_template_tag_check' => "Deactivateer controle voor ontbrekende,<br />sluit we:tags",
		/**
		 * ERROR HANDLER
		 */
		'error_use_handler' => "Gebruik de webEdition foutbehandelaar",
		/**
		 * ERROR TYPES
		 */
		'error_types' => "Behandel deze fouten",
		'error_notices' => "Notities",
		'error_deprecated' => "deprecated Notities", //TRANSLATE
		'error_warnings' => "Waarschuwingen",
		'error_errors' => "Fouten",
		'error_notices_warning' => 'We recommend to aktivate the option -Log errors- on all systems; the option -Show errors- should be activated only during development.',//TRANSLATE
		/**
		 * ERROR DISPLAY
		 */
		'error_displaying' => "Weergave van fouten",
		'error_display' => "Toon fouten",
		'error_log' => "Log fouten",
		'error_mail' => "Verzend een mail",
		'error_mail_address' => "Adres",
		'error_mail_not_saved' => 'Fouten worden niet verstuurd naar het aangegeven e-mail adres omdat het adres niet correct is!\n\nDe overige voorkeuren zijn succesvol bewaard.',
		/**
		 * DEBUG FRAME
		 */
		'show_expert' => "Toon expert instellingen",
		'hide_expert' => "Verberg expert instellingen",
		'show_debug_frame' => "Toon debug frame",
		'debug_normal' => "In normale modus",
		'debug_seem' => "In seeModus",
		'debug_restart' => "Veranderingen vereisen een herstart",
		/*		 * ***************************************************************************
		 * MODULES
		 * *************************************************************************** */

		/**
		 * OBJECT MODULE
		 */
		'module_object' => "Database/Object module",
		'tree_count' => "Aantal getoonde objecten",
		'tree_count_description' => "Deze waarde defineert het maximum aantal onderdelen getoond in de linker navigatie.",
		/*		 * ***************************************************************************
		 * BACKUP
		 * *************************************************************************** */
		'backup' => "Backup", // TRANSLATE
		'backup_slow' => "Langzaam",
		'backup_fast' => "Snel",
		'performance' => "Hier kunt u een gewenst prestatie niveau instellen. Het prestatie niveau moet bij het server systeem passen. Als het systeem beperkte hulpmiddelen bevat (geheugen, timeout etc.) kies dan een langzaam niveau, zoniet kies dan een snel niveau.",
		'backup_auto' => "Auto", // TRANSLATE

		/*		 * ***************************************************************************
		 * Validation
		 * *************************************************************************** */
		'validation' => 'Validatie',
		'xhtml_default' => 'Standaard waarde voor het attribuut <em>xml</em> in we:Tags',
		'xhtml_debug_explanation' => 'De XHTML debugging ondersteunt u in het ontwikkelen van een geldige xhtml web-site. De output van elke we:Tag wordt gecontroleerd op geldigheid en verkeerd geplaatste attributen kunnen verplaatst of verwijderd worden. Let op: Dit kan enige tijd duren. Daarom zou u xhtml debugging alleen moeten activeren tijdens het ontwikkelen van uw web-site.',
		'xhtml_debug_headline' => 'XHTML debugging', // TRANSLATE
		'xhtml_debug_html' => 'Activeer XHTML debugging',
		'xhtml_remove_wrong' => 'Verwijder ongeldige attributen',
		'xhtml_show_wrong_headline' => 'Notificatie van ongeldige attributen',
		'xhtml_show_wrong_html' => 'Activateer',
		'xhtml_show_wrong_text_html' => 'Als tekst',
		'xhtml_show_wrong_js_html' => 'Als JavaScript-Melding',
		'xhtml_show_wrong_error_log_html' => 'In de fouten log (PHP)',
		/*		 * ***************************************************************************
		 * max upload size
		 * *************************************************************************** */
		'we_max_upload_size' => "Max Upload Grootte<br>weergave in hints",
		'we_max_upload_size_hint' => "(in MByte, 0=automatisch)",
		/*		 * ***************************************************************************
		 * we_new_folder_mod
		 * *************************************************************************** */
		'we_new_folder_mod' => "Toegangsrechten voor <br>nieuwe directories",
		'we_new_folder_mod_hint' => "(standaard is 755)",
		/*		 * ***************************************************************************
		 * we_doctype_workspace_behavior
		 * *************************************************************************** */

		'we_doctype_workspace_behavior_hint0' => "De standaard directory van een document type moet zich bevinden binnen het werkgebied van de gebruiker, waardoor het corresponderende document type geselecteerd kan worden.",
		'we_doctype_workspace_behavior_hint1' => "Het werkgebied van de gebruiker moet zich bevinden binnen de standaard directory gedefinieerd in het document type zodat de gebruiker een document type kan selecteren.",
		'we_doctype_workspace_behavior_1' => "Omgekeerd",
		'we_doctype_workspace_behavior_0' => "Standaard",
		'we_doctype_workspace_behavior' => "Werking van de document type selectie",
		/*		 * ***************************************************************************
		 * jupload
		 * *************************************************************************** */

		'use_jupload' => 'Gebruik java upload',
		/*		 * ***************************************************************************
		 * message_reporting
		 * *************************************************************************** */
		'message_reporting' => array(
				'information' => "U kunt hier instellen of u een melding wilt ontvangen voor webEdition handelingen als bewaren, publiceren of verwijderen.",
				'headline' => "Notificaties",
				'show_notices' => "Toon meldingen",
				'show_warnings' => "Toon waarschuwingen",
				'show_errors' => "Toon fouten",
		),
		/*		 * ***************************************************************************
		 * Module Activation
		 * *************************************************************************** */
		'module_activation' => array(
				'information' => "Hier kunt u uw modules activeren of deactiveren als u ze niet nodig heeft.<br />Het deactiveren van modules verbetert de prestaties van webEdition.",
				'headline' => "Module activatie",
		),
		/*		 * ***************************************************************************
		 * Email settings
		 * *************************************************************************** */

		'mailer_information' => "Stel in of webEdition emails moet verwerken via de geïntegreerde PHP functie of dat er gebruikt gemaakt moet worden van een aparte SMTP server.<br /><br />Indien u gebruik maakt van een SMTP mail server, is de kans kleiner dat berichten als \"Spam\" worden gezien.",
		'mailer_type' => "Mailer type", // TRANSLATE
		'mailer_php' => "Gebruik php mail() functie",
		'mailer_smtp' => "Gebruik SMTP server",
		'email' => "Email",
		'tab_email' => "Email",
		'smtp_auth' => "Authenticatie",
		'smtp_server' => "SMTP server", // TRANSLATE
		'smtp_port' => "SMTP poort",
		'smtp_username' => "Gebruikersnaam",
		'smtp_password' => "Wachtwoord",
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
