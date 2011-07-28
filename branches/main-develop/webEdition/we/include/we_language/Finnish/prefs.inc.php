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
$l_prefs = array(
		/*		 * ***************************************************************************
		 * PRELOAD
		 * *************************************************************************** */

		'preload' => "Ladataan asetuksia, hetkinen ...",
		'preload_wait' => "Ladataan asetuksia",
		/*		 * ***************************************************************************
		 * SAVE
		 * *************************************************************************** */

		'save' => "Tallennetaan asetuksia, hetkinen ...",
		'save_wait' => "Tallennetaan asetuksia",
		'saved' => "Asetukset on tallennettu.",
		'saved_successfully' => "Asetukset tallennettu",
		/*		 * ***************************************************************************
		 * TABS
		 * *************************************************************************** */

		'tab_ui' => "Käyttäjärajapinta",
		'tab_glossary' => "Sanasto",
		'tab_extensions' => "Tiedoston pääte",
		'tab_editor' => 'Editori',
		'tab_formmail' => 'Formmail',
		'formmail_recipients' => 'Formmail -vastaanottajat',
		'tab_proxy' => 'Proxy -palvelin',
		'tab_advanced' => 'Lisäasetukset',
		'tab_system' => 'Järjestelmä',
		'tab_seolinks' => 'SEO links', // TRANSLATE
		'tab_error_handling' => 'Virhekäsittely',
		'tab_cockpit' => 'Pika-aloitus',
		'tab_cache' => 'Välimuisti',
		'tab_language' => 'Kielet',
		'tab_countries' => 'Countries', // TRANSLATE
		'tab_modules' => 'Moduulit',
		'tab_versions' => 'Versiointi',
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
		'seem' => "Helppokäyttötila",
		'seem_deactivate' => "Disabloi helppokäyttötila",
		'seem_startdocument' => "Helppokäyttötilan aloitussivu",
		'seem_start_type_document' => "Dokumentti",
		'seem_start_type_object' => "Objekti",
		'seem_start_type_cockpit' => "Pika-aloitus",
		'question_change_to_seem_start' => "Haluatko siirtyä valittuun dokumenttiin?",
		/**
		 * Sidebar
		 */
		'sidebar' => "Sivupalkki",
		'sidebar_deactivate' => "poista käytöstä",
		'sidebar_show_on_startup' => "näytä alussa",
		'sidebar_width' => "Leveys pikseleissä",
		'sidebar_document' => "Dokumentti",
		/**
		 * WINDOW DIMENSION
		 */
		'dimension' => "Ikkunan koko",
		'maximize' => "Maksimoi",
		'specify' => "Määritä",
		'width' => "Leveys",
		'height' => "Korkeus",
		'predefined' => "Esiasetetut koot",
		'show_predefined' => "Näytä esiasetetut koot",
		'hide_predefined' => "Piilota esiasetetut koot",
		/**
		 * TREE
		 */
		'tree_title' => "Puunäkymä",
		'all' => "Kaikki",
		/*		 * ***************************************************************************
		 * FILE EXTENSIONS
		 * *************************************************************************** */

		/**
		 * FILE EXTENSIONS
		 */
		'extensions_information' => "Määritä staattisten ja dynaamisten dokumenttien oletuspäätteet täältä.",
		'we_extensions' => "webEdition -sivujen pääte",
		'static' => "Staattiset sivut",
		'dynamic' => "Dynaamiset sivut",
		'html_extensions' => "HTML -sivujen pääte",
		'html' => "HTML -sivut",
		/*		 * ***************************************************************************
		 * Glossary
		 * *************************************************************************** */

		'glossary_publishing' => "Tarkista ennen julkaisua",
		'force_glossary_check' => "Pakota sanastotarkistus",
		'force_glossary_action' => "Pakota toiminta",
		/*		 * ***************************************************************************
		 * COCKPIT
		 * *************************************************************************** */

		/**
		 * Cockpit
		 */
		'cockpit_amount_columns' => "Sarakkeita pika-aloituksessa ",
		/*		 * ***************************************************************************
		 * CACHING
		 * *************************************************************************** */

		/**
		 * Cache Type
		 */
		'cache_information' => "Aseta uusien sivupohjien oletusarvot kentille \"Välimuistin tyyppi\" ja \"Välimuistin elinikä\".<br /><br />Huomioi että nämä ovat vain esivalinnat.",
		'cache_navigation_information' => "Aseta oletusarvot &lt;we:navigation&gt; tageille. Tämä arvo voidaan ylikirjoittaa \"cachelifetime\" attribuutilla &lt;we:navigation&gt; tagissa.",
		'cache_presettings' => "Oletusarvot",
		'cache_type' => "Välimuistin tyyppi",
		'cache_type_none' => "Välimuisti pois käytöstä",
		'cache_type_full' => "Täysi välimuisti",
		'cache_type_document' => "Dokumentin välimuisti",
		'cache_type_wetag' => "we:Tagien välimuisti",
		/**
		 * Cache Life Time
		 */
		'cache_lifetime' => "Välimuistin elinikä sekunneissa",
		'cache_lifetimes' => array(
				0 => "",
				60 => "1 minuutti",
				300 => "5 minuuttia",
				600 => "10 minuuttia",
				1800 => "30 minuuttia",
				3600 => "1 tunti",
				21600 => "6 tuntia",
				43200 => "12 tuntia",
				86400 => "1 vuorokausi",
		),
		'delete_cache_after' => 'Tyhjennä välimuisti jälkeen',
		'delete_cache_add' => 'lisäyksen jälkeen',
		'delete_cache_edit' => 'muutoksen jälkeen',
		'delete_cache_delete' => 'poiston jälkeen',
		'cache_navigation' => 'Oletusasetus',
		'default_cache_lifetime' => 'Välimuistin oletuselinikä',
		/*		 * ***************************************************************************
		 * LOCALES // LANGUAGES
		 * *************************************************************************** */

		/**
		 * Languages
		 */
		'locale_information' => "Lisää kaikki kielet joilla haluat tarjota web-sivuja.<br /><br />Tätä asetusta käytetään sanastotarkastuksessa ja oikoluvussa.",
		'locale_languages' => "Kieli",
		'locale_countries' => "Maa",
		'locale_add' => "Lisää kieli",
		'cannot_delete_default_language' => "Oletuskieltä ei voi poistaa.",
		'language_already_exists' => "Tämä kieli on jo olemassa",
		'language_country_missing' => "Please select also a country", // TRANSLATE
		'add_dictionary_question' => "Haluatko päivittää sanaston tälle kielelle?",
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
		'editor_plugin' => 'Editori PlugIn',
		'use_it' => "Käytä",
		'start_automatic' => "Käynnistä automaattisesti",
		'ask_at_start' => 'Kysy alussa<br>mitä editoria käytetään',
		'must_register' => 'Vaatii rekisteröinnin',
		'change_only_in_ie' => 'Näitä asetuksia ei voi muuttaa. Editor -plugIn toimii ainoastaan windows-version Internet Explorer, Mozilla, Firebird -ja Firefox selaimissa.',
		'install_plugin' => 'Jotta Editor PlugIn -laajennusta voidaan käyttää Mozillan ActiveX Plugin on asennettava.',
		'confirm_install_plugin' => 'Mozillan ActiveX PlugIn sallii Mozillan käyttävän ActiveX -laajennuksia. Asennuksen jälkeen selain on suljettava ja avattava uudelleen.\\n\\nHuomautus: ActiveX voi olla turvallisuusriski!\\n\\nJatketaanko asennusta?',
		'install_editor_plugin' => 'Käyttääksesi webEdition -järjestelmän editori plugin laajennusta sinun on asennettava se.',
		'install_editor_plugin_text' => 'Asennetaan webEdition editori plugin laajennusta...',
		/**
		 * TEMPLATE EDITOR
		 */
		'editor_information' => "Määritä fonttikoko jota haluat käyttää sivupohjien, CSS-tiedostojen ja JavaScript-tiedostojen muokkaamiseen webEditionissa.<br /><br />Asetusta käytetään kaikille yllämainituille tiedostotyypeille.",
		'editor_mode' => 'Editori',
		'editor_font' => 'Editorin kirjasin',
		'editor_fontname' => 'Kirjasimen nimi',
		'editor_fontsize' => 'Koko',
		'editor_dimension' => 'Editorin koko',
		'editor_dimension_normal' => 'Oletus',
		/*		 * ***************************************************************************
		 * FORMMAIL RECIPIENTS
		 * *************************************************************************** */

		/**
		 * FORMMAIL RECIPIENTS
		 */
		'formmail_information' => "Syötä kaikki sähköpostiosoitteet, jotka voivat vastaanottaa formmail -funktion lähettämiä lomakkeita (&lt;we:form type=\"formmail\" ..&gt;).<br><br>Jos et syötä sähköpostiosoitetta, et voi lähettää lomakkeita käyttmällä formmail -funktiota!",
		'formmail_log' => "Formmailin loki",
		'log_is_empty' => "Loki on tyhjä!",
		'ip_address' => "IP osoite",
		'blocked_until' => "Estetty asti",
		'unblock' => "Pura esto",
		'clear_log_question' => "Halutako varmasti tyhjentää lokin?",
		'clear_block_entry_question' => "Haluatko varmasti purkaa eston IP-osoitteilta: %s ?",
		'forever' => "Aina",
		'yes' => "kyllä",
		'no' => "ei",
		'on' => "päällä",
		'off' => "pois",
		'formmailConfirm' => "Formmail varmistustoiminto",
		'logFormmailRequests' => "Formmail pyynnöt lokiin",
		'deleteEntriesOlder' => "Poista vanhemmat merkinnät",
		'blockFormmail' => "Rajoita formmail pyyntöjä",
		'formmailSpan' => "Aikavälillä",
		'formmailTrials' => "Pyyntöjä sallittu",
		'blockFor' => "Estä ajaksi",
		'formmailViaWeDoc' => "Call formmail via webEdition-Dokument.",
		'never' => "ei koskaan",
		'1_day' => "1 päivä",
		'more_days' => "%s päivää",
		'1_week' => "1 viikko",
		'more_weeks' => "%s viikkoa",
		'1_year' => "1 vuosi",
		'more_years' => "%s vuotta",
		'1_minute' => "1 minuutti",
		'more_minutes' => "%s minuuttia",
		'1_hour' => "1 tunti",
		'more_hours' => "%s tuntia",
		'ever' => "aina",
		/*		 * ***************************************************************************
		 * PROXY SERVER
		 * *************************************************************************** */

		/**
		 * PROXY SERVER
		 */
		'proxy_information' => "Jos palvelimesi käyttää Proxy-palvelinta, määrittele sen asetukset täällä.",
		'useproxy' => "Käytä proxy-palvelinta<br>Live-päivityksessä",
		'proxyaddr' => "Osoite",
		'proxyport' => "Portti",
		'proxyuser' => "Käyttäjänimi",
		'proxypass' => "Salasana",
		/*		 * ***************************************************************************
		 * ADVANCED
		 * *************************************************************************** */

		/**
		 * ATTRIBS
		 */
		'default_php_setting' => "Oletusasetukset <br><em>php</em>-määreille we:tageissa",
		/**
		 * INLINEEDIT
		 */
		'inlineedit_default' => "Oletusarvo<br><em>inlineedit</em> määreelle<br>&lt;we:textarea&gt; -tagissa",
		'removefirstparagraph_default' => "Default value for the<br><em>removefirstparagraph</em> attribute in<br>&lt;we:textarea&gt;", // TRANSLATE
		'hidenameattribinweimg_default' => "No output of name=xyz in we:img (HTML 5)", // TRANSLATE
		'hidenameattribinweform_default' => "No output of name=xyz in we:form (XHTML strict)", // TRANSLATE

		/**
		 * SAFARI WYSIWYG
		 */
		'safari_wysiwyg' => "Käytä Safari Wysiwyg<br>editoria (beta versio)",
		'wysiwyg_type' => "Select editor for textareas", //TRANSLATE

		/**
		 * SHOWINPUTS
		 */
		'showinputs_default' => "Tagin &lt;we:img&gt; oletusarvo määreelle <br><em>showinputs</em>",
		/**
		 * NAVIGATION
		 */
		'navigation_entries_from_document' => "Create new navigation entries from the document as", // TRANSLATE
		'navigation_entries_from_document_item' => "item", // TRANSLATE
		'navigation_entries_from_document_folder' => "folder", // TRANSLATE
		'navigation_rules_continue' => "Continue to evaluate navigation rules after a first match", // TRANSLATE
		'general_directoryindex_hide' => "Hide DirectoryIndex- file names", // TRANSLATE
		'general_directoryindex_hide_description' => "For the tags <we:link>, <we:linkslist>, <we:listview> you can use the attribute 'hidedirindex'", // TRANSLATE
		'navigation_directoryindex_hide' => "in the navigation output", // TRANSLATE
		'wysiwyglinks_directoryindex_hide' => "in links from the WYSIWYG editor", // TRANSLATE
		'objectlinks_directoryindex_hide' => "in links to objects", // TRANSLATE
		'urlencode_objectseourls' => "URLencode the SEO-urls",// TRANSLATE
		'suppress404code' => "suppress 404 not found",// TRANSLATE
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
		'db_connect' => "Tietokannan<br>yhteystyyppi",
		'db_set_charset' => "Yhteyden merkistö",
		'db_set_charset_information' => "The connection charset is used for the communication between webEdition and datase server.<br/>If no value is specified, the standard connection charset set in PHP is used.<br/>In the ideal case, the webEdition language (i.e. English_UTF-8), the database collation (i.e. utf8_general_ci), the connection charset (i.e. utf8) and the settings of external tools such as phpMyAdmin (i.e. utf-8) are identical. In this case, one can edit database entries with these external tools without problems.", // TRANSLATE
		'db_set_charset_warning' => "The connection charset should be changed only in a fresh installation of webEdition (without data in the database). Otherwise, all non ASCII characters will be interpreted wrong and may be destroyed.", // TRANSLATE

		/**
		 * HTTP AUTHENTICATION
		 */
		'auth' => "HTTP autentikointi",
		'useauth' => "Palvelin käyttää HTTP<br>autentikointia webEdition<br>hakemistossa",
		'authuser' => "Käyttäjänimi",
		'authpass' => "Salasana",
		/**
		 * THUMBNAIL DIR
		 */
		'thumbnail_dir' => "Esikatselukuvien hakemisto",
		'pagelogger_dir' => "pageLogger hakemisto",
		/**
		 * HOOKS //TRANS
		 */
		'hooks' => "\"Koukut\"",
		'hooks_information' => "\"Koukkujen\" käyttö mahdollistaa mielivaltaisen PHP-koodin suorittamisen webEditionissa tallennuksen, julkaisun, julkaisun poiston sekä minkä tahansa sisältötyypin poiston yhteydessä.<br />
	Lisää tietoa löytyy Online-dokumentaatiosta.<br /><br />Salli koukkujen käyttö?",
		/**
		 * Backward compatibility
		 */
		'backwardcompatibility' => "Backward compatibility", //TRANSLATE
		'backwardcompatibility_tagloading' => "Load all 'old' we_tag functions", //TRANSLATE
		'backwardcompatibility_tagloading_message' => "Only necessary if in custom_tags or in PHP code inside templates we_tags are called in the form we_tag_tagname().<br/> Recommended call: we_tag<br/>('tagname',&#36;attribs,&#36;content)", //TRANSLATE


		/*		 * ***************************************************************************
		 * ERROR HANDLING
		 * *************************************************************************** */


		'error_no_object_found' => 'Virhesivu objekteille joita ei ole',
		/**
		 * TEMPLATE TAG CHECK
		 */
		'templates' => "Sivupohjat",
		'disable_template_tag_check' => "Poista puuttuvien,<br />we:lopetustagien tarkistus",
		/**
		 * ERROR HANDLER
		 */
		'error_use_handler' => "Käytä webEdition -järjestelmän <br>virheenkäsittelyä",
		/**
		 * ERROR TYPES
		 */
		'error_types' => "Käsittele virhetyypit",
		'error_notices' => "Ilmoitukset",
		'error_deprecated' => "deprecated Notices", //TRANSLATE
		'error_warnings' => "Varoitukset",
		'error_errors' => "Virheet",
		'error_notices_warning' => 'We recommend to aktivate the option -Log errors- on all systems; the option -Show errors- should be activated only during development.',//TRANSLATE
		/**
		 * ERROR DISPLAY
		 */
		'error_displaying' => "Virheiden käsittelytapa",
		'error_display' => "Näytä virheet",
		'error_log' => "Kirjaa virheet Lokikirjaan",
		'error_mail' => "Lähetä virheet sähköpostilla",
		'error_mail_address' => "Osoite",
		'error_mail_not_saved' => 'Virheitä ei lähetetä annettuun sähköpostiosoitteeseen, koska osoite on virheellinen!\n\nJäljelläolevat asetukset on tallennettu.',
		/**
		 * DEBUG FRAME
		 */
		'show_expert' => "Näytä asiantuntija-asetukset",
		'hide_expert' => "Piilota asiantuntija-asetukset",
		'show_debug_frame' => "Näytä seuranta -kehys",
		'debug_normal' => "Normaalitilassa",
		'debug_seem' => "Helppokäyttötilassa",
		'debug_restart' => "Muutokset vaativat uudelleenkirjautumisen",
		/*		 * ***************************************************************************
		 * MODULES
		 * *************************************************************************** */

		/**
		 * OBJECT MODULE
		 */
		'module_object' => "Tietokanta/Objektimoduuli",
		'tree_count' => "Näytettävien objektien määrä",
		'tree_count_description' => "Tämä arvo määrittää näytettävien objektien maksimimäärän vasemmassa tiedostolistassa.",
		/*		 * ***************************************************************************
		 * BACKUP
		 * *************************************************************************** */
		'backup' => "Varmuuskopio",
		'backup_slow' => "Hidas",
		'backup_fast' => "Nopea",
		'performance' => "Tässä voit määrittää suoritustason. Suoritustaso riippuu palvelinjärjestelmästä. Jos järjestelmässä on rajattu määrä resursseja (muisti, aikakatkaisu jne...) valitse hidas taso, muussa tapauksessa nopea taso.",
		'backup_auto' => "Automaattinen",
		/*		 * ***************************************************************************
		 * Validation
		 * *************************************************************************** */
		'validation' => 'Validointi',
		'xhtml_default' => 'Tagien <em>xml</em> määreen oletusarvo',
		'xhtml_debug_explanation' => 'XHTML -debug tila tukee kehitystä validille xhtml -muotoiselle www-sivulle. Jokaisen we:tagin validiteetti tarkistetaan ja väärät määreet näytetään tai poistetaan. Huomioi: Tämä toiminto voi kestää, siksi debug tilaa kannattaa käyttää ainoastaan kehitystyön aikana.',
		'xhtml_debug_headline' => 'XHTML debugtila',
		'xhtml_debug_html' => 'Aktivoi XHTML debugtila',
		'xhtml_remove_wrong' => 'Poista virheelliset määreet',
		'xhtml_show_wrong_headline' => 'Ilmoita virheellisistä määreistä',
		'xhtml_show_wrong_html' => 'Aktivoi',
		'xhtml_show_wrong_text_html' => 'Tekstinä',
		'xhtml_show_wrong_js_html' => 'Javascript -varoituksena',
		'xhtml_show_wrong_error_log_html' => 'error logissa (PHP)',
		/*		 * ***************************************************************************
		 * max upload size
		 * *************************************************************************** */
		'we_max_upload_size' => "Maksimi latauskoko,<br>joka näytetään vihjeissä",
		'we_max_upload_size_hint' => "(MB, 0=automaattinen)",
		/*		 * ***************************************************************************
		 * we_new_folder_mod
		 * *************************************************************************** */
		'we_new_folder_mod' => "Oikeudet<br>uusille hakemistoille",
		'we_new_folder_mod_hint' => "(oletus on 755)",
		/*		 * ***************************************************************************
		 * we_doctype_workspace_behavior
		 * *************************************************************************** */

		'we_doctype_workspace_behavior_hint0' => "Dokumentin oletushakemiston täytyy sijaita käyttäjän työtilassa, jotta käyttäjä voi valita dokumentin tyypin.",
		'we_doctype_workspace_behavior_hint1' => "Käyttäjän työtilan on ssijaittava käyttäjälle määritetyn dokumenttityypin oletushakemistossa, jotta dokumenttia voidaan muokata.",
		'we_doctype_workspace_behavior_1' => "Käänteinen",
		'we_doctype_workspace_behavior_0' => "Standardi",
		'we_doctype_workspace_behavior' => "Dokumenttityypin käyttäytyminen",
		/*		 * ***************************************************************************
		 * jupload
		 * *************************************************************************** */

		'use_jupload' => 'Käytä javaa tiedostojen lähetyksessä',
		/*		 * ***************************************************************************
		 * message_reporting
		 * *************************************************************************** */
		'message_reporting' => array(
				'information' => "Voit määrittää alla olevilla laatikoilla haluatko saada ilmoituksen webEditionin tapahtumista.",
				'headline' => "Ilmoitukset",
				'show_notices' => "Näytä huomautukset",
				'show_warnings' => "Näytä varoitukset",
				'show_errors' => "Näytä virheet",
		),
		/*		 * ***************************************************************************
		 * Module Activation
		 * *************************************************************************** */
		'module_activation' => array(
				'information' => "Täällä voit aktivoida ja deaktivoida moduuleja tarpeesi mukaan.<br />Deaktivoidut moduulit voivat parantaa webEditionin yleistä suorituskykyä.",
				'headline' => "Moduulien aktivointi",
		),
		/*		 * ***************************************************************************
		 * Email settings
		 * *************************************************************************** */

		'mailer_information' => "Säädä lähettääkö webEdition sähköpostit PHP:n mail()-funktiolla vai erillisellä SMTP-palvelimella.<br /><br />SMTP-palvelinta käytettäessä, viestien spämmiksi tulkitsemisen riski laskee.",
		'mailer_type' => "Mailerin tyyppi",
		'mailer_php' => "Käytä php mail() funktiota",
		'mailer_smtp' => "Käytä SMTP palvelinta",
		'email' => "E-Mail",
		'tab_email' => "E-Mail",
		'smtp_auth' => "Autentikaatio",
		'smtp_server' => "SMTP palvelin",
		'smtp_port' => "SMTP portti",
		'smtp_username' => "Käyttäjätunnus",
		'smtp_password' => "Salasana",
		'smtp_halo' => "SMTP helo",
		'smtp_timeout' => "SMTP aikakatkaisu",
		'smtp_encryption' => "encrypted transport", // TRANSLATE
		'smtp_encryption_none' => "no", // TRANSLATE
		'smtp_encryption_ssl' => "SSL", // TRANSLATE
		'smtp_encryption_tls' => "TLS", // TRANSLATE


		/*		 * ***************************************************************************
		 * Versions settings
		 * *************************************************************************** */

		'versioning' => "Versiointi",
		'version_all' => "kaikki",
		'versioning_activate_text' => "Valitse mille sisältötyypeille versiointi aktivoidaan.",
		'versioning_time_text' => "Määrittelemällä aikajakson voit valita mistä vanhemmat versiot poistetaan automaattisesti.",
		'versioning_time' => "Aikajakso",
		'versioning_anzahl_text' => "Luotavien versioiden määrä jokaisesta dokumentista tai objektista.",
		'versioning_anzahl' => "Numero",
		'versioning_wizard_text' => "Poista tai palauta versioita avaamalla Versio-Velho.",
		'versioning_wizard' => "Avaa Versio-Velho",
		'ContentType' => "Sisältötyyppi",
		'versioning_create_text' => "Määrittele mitkä tapahtumat luovat uusia versioita. Joko ainoastaan julkaistaessa tai myös tallentaessa, poistaessa, poistaessa julkaisusta sekä tuonnissa.",
		'versioning_create' => "Luo Versio",
		'versions_create_publishing' => "vain julkaistaessa",
		'versions_create_always' => "aina",
		'versioning_templates_text' => "Define special values for the <b>versioning of templates</b>", // TRANSLATE
		'versions_create_tmpl_publishing' => "only using special button", // TRANSLATE
		'versions_create_tmpl_always' => "always", // TRANSLATE


		'use_jeditor' => "Käytä",
		'editor_font_colors' => 'Määrittele fontin värit',
		'editor_normal_font_color' => 'Oletus',
		'editor_we_tag_font_color' => 'webEdition tagit',
		'editor_we_attribute_font_color' => 'webEdition määreet',
		'editor_html_tag_font_color' => 'HTML tagit',
		'editor_html_attribute_font_color' => 'HTML määreet',
		'editor_pi_tag_font_color' => 'PHP koodi',
		'editor_comment_font_color' => 'Kommentit',
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


		'juplod_not_installed' => 'JUpload ei ole asennettu!',
);
