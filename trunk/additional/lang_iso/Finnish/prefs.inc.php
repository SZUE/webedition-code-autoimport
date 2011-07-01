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

/*****************************************************************************
 * PRELOAD
 *****************************************************************************/

$l_prefs["preload"] = "Ladataan asetuksia, hetkinen ...";
$l_prefs["preload_wait"] = "Ladataan asetuksia";

/*****************************************************************************
 * SAVE
 *****************************************************************************/

$l_prefs["save"] = "Tallennetaan asetuksia, hetkinen ...";
$l_prefs["save_wait"] = "Tallennetaan asetuksia";

$l_prefs["saved"] = "Asetukset on tallennettu.";
$l_prefs["saved_successfully"] = "Asetukset tallennettu";

/*****************************************************************************
 * TABS
 *****************************************************************************/

$l_prefs["tab_ui"] = "K‰ytt‰j‰rajapinta";
$l_prefs["tab_glossary"] = "Sanasto";
$l_prefs["tab_extensions"] = "Tiedoston p‰‰te";
$l_prefs["tab_editor"] = 'Editori';
$l_prefs["tab_formmail"] = 'Formmail';
$l_prefs["formmail_recipients"] = 'Formmail -vastaanottajat';
$l_prefs["tab_proxy"] = 'Proxy -palvelin';
$l_prefs["tab_advanced"] = 'Lis‰asetukset';
$l_prefs["tab_system"] = 'J‰rjestelm‰';
$l_prefs["tab_seolinks"] = 'SEO links';// TRANSLATE
$l_prefs["tab_error_handling"] = 'Virhek‰sittely';
$l_prefs["tab_cockpit"] = 'Pika-aloitus';
$l_prefs["tab_cache"] = 'V‰limuisti';
$l_prefs["tab_language"] = 'Kielet';
$l_prefs["tab_countries"] = 'Countries';// TRANSLATE
$l_prefs["tab_modules"] = 'Moduulit';
$l_prefs["tab_versions"] = 'Versiointi';

/*****************************************************************************
 * USER INTERFACE
 *****************************************************************************/
	/**
	 * Countries
	 */
	$l_prefs["countries_information"]= "Select the countries, which are available in the customer module, shop-module and so on.";// TRANSLATE
	$l_prefs["countries_headline"] = "Country selection";// TRANSLATE
	$l_prefs["countries_country"] = "Country";// TRANSLATE
    $l_prefs["countries_top"] = "top list";// TRANSLATE
    $l_prefs["countries_show"] = "display";// TRANSLATE
    $l_prefs["countries_noshow"] = "no display";// TRANSLATE

	/**
	 * LANGUAGE
	 */

	$l_prefs["choose_language"] = "Kieli";
	$l_prefs["language_notice"] = "Kielenmuutos tulee voimaan webEditionin uudelleenk‰ynnistyksen j‰lkeen.";

	/**
	 * CHARSET
	 */

	$l_prefs["default_charset"] = "Standard charset"; // TRANSLATE

	/**
	 * SEEM
	 */
	$l_prefs["seem"] = "Helppok‰yttˆtila";
	$l_prefs["seem_deactivate"] = "Disabloi helppok‰yttˆtila";
	$l_prefs["seem_startdocument"] = "Helppok‰yttˆtilan aloitussivu";
	$l_prefs["seem_start_type_document"] = "Dokumentti";
	$l_prefs["seem_start_type_object"] = "Objekti";
	$l_prefs["seem_start_type_cockpit"] = "Pika-aloitus";
	$l_prefs["question_change_to_seem_start"] = "Haluatko siirty‰ valittuun dokumenttiin?";


	/**
	 * Sidebar
	 */
	$l_prefs["sidebar"] = "Sivupalkki";
	$l_prefs["sidebar_deactivate"] = "poista k‰ytˆst‰";
	$l_prefs["sidebar_show_on_startup"] = "n‰yt‰ alussa";
	$l_prefs["sidebar_width"] = "Leveys pikseleiss‰";
	$l_prefs["sidebar_document"] = "Dokumentti";


	/**
	 * WINDOW DIMENSION
	 */

	$l_prefs["dimension"] = "Ikkunan koko";
	$l_prefs["maximize"] = "Maksimoi";
	$l_prefs["specify"] = "M‰‰rit‰";
	$l_prefs["width"] = "Leveys";
	$l_prefs["height"] = "Korkeus";
	$l_prefs["predefined"] = "Esiasetetut koot";
	$l_prefs["show_predefined"] = "N‰yt‰ esiasetetut koot";
	$l_prefs["hide_predefined"] = "Piilota esiasetetut koot";

	/**
	 * TREE
	 */

	$l_prefs["tree_title"] = "Puun‰kym‰";
	$l_prefs["all"] = "Kaikki";
/*****************************************************************************
 * FILE EXTENSIONS
 *****************************************************************************/

	/**
	 * FILE EXTENSIONS
	 */
	$l_prefs["extensions_information"] = "M‰‰rit‰ staattisten ja dynaamisten dokumenttien oletusp‰‰tteet t‰‰lt‰.";
	
	$l_prefs["we_extensions"] = "webEdition -sivujen p‰‰te";
	$l_prefs["static"] = "Staattiset sivut";
	$l_prefs["dynamic"] = "Dynaamiset sivut";
	$l_prefs["html_extensions"] = "HTML -sivujen p‰‰te";
	$l_prefs["html"] = "HTML -sivut";
	
/*****************************************************************************
 * Glossary
 *****************************************************************************/

	$l_prefs["glossary_publishing"] = "Tarkista ennen julkaisua";
	$l_prefs["force_glossary_check"] = "Pakota sanastotarkistus";
	$l_prefs["force_glossary_action"] = "Pakota toiminta";

/*****************************************************************************
 * COCKPIT
 *****************************************************************************/

	/**
	 * Cockpit
	 */

	$l_prefs["cockpit_amount_columns"] = "Sarakkeita pika-aloituksessa ";


/*****************************************************************************
 * CACHING
 *****************************************************************************/

	/**
	 * Cache Type
	 */
	$l_prefs["cache_information"] = "Aseta uusien sivupohjien oletusarvot kentille \"V‰limuistin tyyppi\" ja \"V‰limuistin elinik‰\".<br /><br />Huomioi ett‰ n‰m‰ ovat vain esivalinnat.";
	$l_prefs["cache_navigation_information"] = "Aseta oletusarvot &lt;we:navigation&gt; tageille. T‰m‰ arvo voidaan ylikirjoittaa \"cachelifetime\" attribuutilla &lt;we:navigation&gt; tagissa.";
	
	$l_prefs["cache_presettings"] = "Oletusarvot";
	$l_prefs["cache_type"] = "V‰limuistin tyyppi";
	$l_prefs["cache_type_none"] = "V‰limuisti pois k‰ytˆst‰";
	$l_prefs["cache_type_full"] = "T‰ysi v‰limuisti";
	$l_prefs["cache_type_document"] = "Dokumentin v‰limuisti";
	$l_prefs["cache_type_wetag"] = "we:Tagien v‰limuisti";

	/**
	 * Cache Life Time
	 */
	$l_prefs["cache_lifetime"] = "V‰limuistin elinik‰ sekunneissa";

	$l_prefs['cache_lifetimes'] = array();
	$l_prefs['cache_lifetimes'][0] = "";
	$l_prefs['cache_lifetimes'][60] = "1 minuutti";
	$l_prefs['cache_lifetimes'][300] = "5 minuuttia";
	$l_prefs['cache_lifetimes'][600] = "10 minuuttia";
	$l_prefs['cache_lifetimes'][1800] = "30 minuuttia";
	$l_prefs['cache_lifetimes'][3600] = "1 tunti";
	$l_prefs['cache_lifetimes'][21600] = "6 tuntia";
	$l_prefs['cache_lifetimes'][43200] = "12 tuntia";
	$l_prefs['cache_lifetimes'][86400] = "1 vuorokausi";

	$l_prefs['delete_cache_after'] = 'Tyhjenn‰ v‰limuisti j‰lkeen';
	$l_prefs['delete_cache_add'] = 'lis‰yksen j‰lkeen';
	$l_prefs['delete_cache_edit'] = 'muutoksen j‰lkeen';
	$l_prefs['delete_cache_delete'] = 'poiston j‰lkeen';
	$l_prefs['cache_navigation'] = 'Oletusasetus';
	$l_prefs['default_cache_lifetime'] = 'V‰limuistin oletuselinik‰';


/*****************************************************************************
 * LOCALES // LANGUAGES
 *****************************************************************************/

	/**
	 * Languages
	 */
	$l_prefs["locale_information"] = "Lis‰‰ kaikki kielet joilla haluat tarjota web-sivuja.<br /><br />T‰t‰ asetusta k‰ytet‰‰n sanastotarkastuksessa ja oikoluvussa.";

	$l_prefs["locale_languages"] = "Kieli";
	$l_prefs["locale_countries"] = "Maa";
	$l_prefs["locale_add"] = "Lis‰‰ kieli";
	$l_prefs['cannot_delete_default_language'] = "Oletuskielt‰ ei voi poistaa.";
	$l_prefs["language_already_exists"] = "T‰m‰ kieli on jo olemassa";
	$l_prefs["language_country_missing"] = "Please select also a country"; // TRANSLATE
	$l_prefs["add_dictionary_question"] = "Haluatko p‰ivitt‰‰ sanaston t‰lle kielelle?";
	$l_prefs["langlink_headline"]= "Support for setting links between different languages";
	$l_prefs["langlink_information"]= "With this option, you can set the links to corresponding language versions of documents/objects in the backend and open/create etc. these documents/oobjects.<br/>For the frontend you can display these links in a listview type=languagelink.<br/><br/>For folders, you can define a <b>document</b> in each language, which is used if for a document within the folder no corresponding document in the other language is set.";
	$l_prefs["langlink_support"] = "active";
	$l_prefs["langlink_support_backlinks"] = "Generate back links automatically";// TRANSLATE
	$l_prefs["langlink_support_backlinks_information"] = "Back links can be generated automatically for documents/objects (not folders). The other document should not be open in an editor tab!";
	$l_prefs["langlink_support_recursive"] = "Generate language links recursive";// TRANSLATE
	$l_prefs["langlink_support_recursive_information"] = "Setting of langauge links can be done recursively for documents/objects (but not folders). This sets all possible links and tries to establish the language-circle as fast as possible. The other documents should not be open in an editor tab!";

/*****************************************************************************
 * EDITOR
 *****************************************************************************/

	/**
	 * EDITOR PLUGIN
	 */

	$l_prefs["editor_plugin"] = 'Editori PlugIn';
	$l_prefs["use_it"] = "K‰yt‰";
	$l_prefs["start_automatic"] = "K‰ynnist‰ automaattisesti";
	$l_prefs["ask_at_start"] = 'Kysy alussa<br>mit‰ editoria k‰ytet‰‰n';
	$l_prefs["must_register"] = 'Vaatii rekisterˆinnin';
	$l_prefs["change_only_in_ie"] = 'N‰it‰ asetuksia ei voi muuttaa. Editor -plugIn toimii ainoastaan windows-version Internet Explorer, Mozilla, Firebird -ja Firefox selaimissa.';
	$l_prefs["install_plugin"] = 'Jotta Editor PlugIn -laajennusta voidaan k‰ytt‰‰ Mozillan ActiveX Plugin on asennettava.';
	$l_prefs["confirm_install_plugin"] = 'Mozillan ActiveX PlugIn sallii Mozillan k‰ytt‰v‰n ActiveX -laajennuksia. Asennuksen j‰lkeen selain on suljettava ja avattava uudelleen.\\n\\nHuomautus: ActiveX voi olla turvallisuusriski!\\n\\nJatketaanko asennusta?';

	$l_prefs["install_editor_plugin"] = 'K‰ytt‰‰ksesi webEdition -j‰rjestelm‰n editori plugin laajennusta sinun on asennettava se.';
	$l_prefs["install_editor_plugin_text"]= 'Asennetaan webEdition editori plugin laajennusta...';

	/**
	 * TEMPLATE EDITOR
	 */
	
	$l_prefs["editor_information"] = "M‰‰rit‰ fonttikoko jota haluat k‰ytt‰‰ sivupohjien, CSS-tiedostojen ja JavaScript-tiedostojen muokkaamiseen webEditionissa.<br /><br />Asetusta k‰ytet‰‰n kaikille yll‰mainituille tiedostotyypeille.";
	
	$l_prefs["editor_mode"] = 'Editori';
	$l_prefs["editor_font"] = 'Editorin kirjasin';
	$l_prefs["editor_fontname"] = 'Kirjasimen nimi';
	$l_prefs["editor_fontsize"] = 'Koko';
	$l_prefs["editor_dimension"] = 'Editorin koko';
	$l_prefs["editor_dimension_normal"] = 'Oletus';

/*****************************************************************************
 * FORMMAIL RECIPIENTS
 *****************************************************************************/

	/**
	 * FORMMAIL RECIPIENTS
	 */

	$l_prefs["formmail_information"] = "Syˆt‰ kaikki s‰hkˆpostiosoitteet, jotka voivat vastaanottaa formmail -funktion l‰hett‰mi‰ lomakkeita (&lt;we:form type=\"formmail\" ..&gt;).<br><br>Jos et syˆt‰ s‰hkˆpostiosoitetta, et voi l‰hett‰‰ lomakkeita k‰yttm‰ll‰ formmail -funktiota!";

	$l_prefs["formmail_log"] = "Formmailin loki";
	$l_prefs['log_is_empty'] = "Loki on tyhj‰!";
	$l_prefs['ip_address'] = "IP osoite";
	$l_prefs['blocked_until'] = "Estetty asti";
	$l_prefs['unblock'] = "Pura esto";
	$l_prefs['clear_log_question'] = "Halutako varmasti tyhjent‰‰ lokin?";
	$l_prefs['clear_block_entry_question'] = "Haluatko varmasti purkaa eston IP-osoitteilta: %s ?";
	$l_prefs["forever"] = "Aina";
	$l_prefs["yes"] = "kyll‰";
	$l_prefs["no"] = "ei";
	$l_prefs["on"] = "p‰‰ll‰";
	$l_prefs["off"] = "pois";
	$l_prefs["formmailConfirm"] = "Formmail varmistustoiminto";
	$l_prefs["logFormmailRequests"] = "Formmail pyynnˆt lokiin";
	$l_prefs["deleteEntriesOlder"] = "Poista vanhemmat merkinn‰t";
	$l_prefs["blockFormmail"] = "Rajoita formmail pyyntˆj‰";
	$l_prefs["formmailSpan"] = "Aikav‰lill‰";
	$l_prefs["formmailTrials"] = "Pyyntˆj‰ sallittu";
	$l_prefs["blockFor"] = "Est‰ ajaksi";
	$l_prefs["formmailViaWeDoc"] = "Call formmail via webEdition-Dokument.";
	$l_prefs["never"] = "ei koskaan";
	$l_prefs["1_day"] = "1 p‰iv‰";
	$l_prefs["more_days"] = "%s p‰iv‰‰";
	$l_prefs["1_week"] = "1 viikko";
	$l_prefs["more_weeks"] = "%s viikkoa";
	$l_prefs["1_year"] = "1 vuosi";
	$l_prefs["more_years"] = "%s vuotta";
	$l_prefs["1_minute"] = "1 minuutti";
	$l_prefs["more_minutes"] = "%s minuuttia";
	$l_prefs["1_hour"] = "1 tunti";
	$l_prefs["more_hours"] = "%s tuntia";
	$l_prefs["ever"] = "aina";





/*****************************************************************************
 * PROXY SERVER
 *****************************************************************************/

	/**
	 * PROXY SERVER
	 */

	$l_prefs["proxy_information"] = "Jos palvelimesi k‰ytt‰‰ Proxy-palvelinta, m‰‰rittele sen asetukset t‰‰ll‰.";
	
	$l_prefs["useproxy"] = "K‰yt‰ proxy-palvelinta<br>Live-p‰ivityksess‰";
	$l_prefs["proxyaddr"] = "Osoite";
	$l_prefs["proxyport"] = "Portti";
	$l_prefs["proxyuser"] = "K‰ytt‰j‰nimi";
	$l_prefs["proxypass"] = "Salasana";

/*****************************************************************************
 * ADVANCED
 *****************************************************************************/

	/**
	 * ATTRIBS
	 */

	$l_prefs["default_php_setting"] = "Oletusasetukset <br><em>php</em>-m‰‰reille we:tageissa";

	/**
	 * INLINEEDIT
	 */

	 $l_prefs["inlineedit_default"] = "Oletusarvo<br><em>inlineedit</em> m‰‰reelle<br>&lt;we:textarea&gt; -tagissa";
	 $l_prefs["removefirstparagraph_default"] = "Default value for the<br><em>removefirstparagraph</em> attribute in<br>&lt;we:textarea&gt;";// TRANSLATE
	 $l_prefs["hidenameattribinweimg_default"] = "No output of name=xyz in we:img (HTML 5)";// TRANSLATE
	 $l_prefs["hidenameattribinweform_default"] = "No output of name=xyz in we:form (XHTML strict)";// TRANSLATE

	/**
	 * SAFARI WYSIWYG
	 */
	 $l_prefs["safari_wysiwyg"] = "K‰yt‰ Safari Wysiwyg<br>editoria (beta versio)";

	/**
	 * SHOWINPUTS
	 */
	 $l_prefs["showinputs_default"] = "Tagin &lt;we:img&gt; oletusarvo m‰‰reelle <br><em>showinputs</em>";

	/**
	 * NAVIGATION
	 */
	 $l_prefs["navigation_entries_from_document"] = "Create new navigation entries from the document as"; // TRANSLATE
	 $l_prefs["navigation_entries_from_document_item"] = "item"; // TRANSLATE
	 $l_prefs["navigation_entries_from_document_folder"] = "folder"; // TRANSLATE
	 $l_prefs["navigation_rules_continue"] = "Continue to evaluate navigation rules after a first match";// TRANSLATE
	 $l_prefs["general_directoryindex_hide"] = "Hide DirectoryIndex- file names";// TRANSLATE
	 $l_prefs["general_directoryindex_hide_description"] = "For the tags <we:link>, <we:linklist>, <we:listview> you can use the attribute 'hidedirindex'";// TRANSLATE
	 $l_prefs["navigation_directoryindex_hide"] = "in the navigation output";// TRANSLATE
 	 $l_prefs["wysiwyglinks_directoryindex_hide"] = "in links from the WYSIWYG editor";// TRANSLATE
	 $l_prefs["objectlinks_directoryindex_hide"] = "in links to objects";// TRANSLATE
	 $l_prefs["navigation_directoryindex_description"] = "After a change, a rebuild is required (i.e. navigation cache, objects ...)";// TRANSLATE
	 $l_prefs["navigation_directoryindex_names"] = "DirectoryIndex file names (comma separated, incl. file extensions, i.e. 'index.php,index.html'";// TRANSLATE
	 $l_prefs["general_objectseourls"] = "Generate object SEO urls ";// TRANSLATE
	 $l_prefs["navigation_objectseourls"] = "in the navigation output";// TRANSLATE
	 $l_prefs["wysiwyglinks_objectseourls"] = "in links from the WYSIWYG editor";// TRANSLATE
	 $l_prefs["general_objectseourls_description"] = "For the tags <we:link>, <we:linklist>, <we:listview>, <we:object> you can use the attribute 'objectseourls'";// TRANSLATE
	 $l_prefs["taglinks_directoryindex_hide"] = "preset value for tags";// TRANSLATE
	 $l_prefs["taglinks_objectseourls"] = "preset value for tags";// TRANSLATE
	 $l_prefs["urlencode_objectseourls"] = "URLencode the SEO-urls";// TRANSLATE
	 $l_prefs["suppress404code"] = "suppress 404 not found";
	 $l_prefs["general_seoinside"] = "Usage within webEdition ";// TRANSLATE
	 $l_prefs["general_seoinside_description"] = "If DirectoryIndex- file names and object SEO urls are used within webEdition, webEdition can not identify internal links and clicks on these links do not open the editor. With the following options, you can decide if they are are used in editmode and in the preview.";// TRANSLATE
	 $l_prefs["seoinside_hideinwebedition"] = "Hide in preview";// TRANSLATE
	 $l_prefs["seoinside_hideineditmode"] = "Hide in editmode";// TRANSLATE

	 $l_prefs["navigation"] ="Navigation";// TRANSLATE


	/**
	 * DATABASE
	 */

	$l_prefs["db_connect"] = "Tietokannan<br>yhteystyyppi";
	$l_prefs["db_set_charset"] = "Yhteyden merkistˆ";
	$l_prefs["db_set_charset_information"] = "The connection charset is used for the communication between webEdition and datase server.<br/>If no value is specified, the standard connection charset set in PHP is used.<br/>In the ideal case, the webEdition language (i.e. English_UTF-8), the database collation (i.e. utf8_general_ci), the connection charset (i.e. utf8) and the settings of external tools such as phpMyAdmin (i.e. utf-8) are identical. In this case, one can edit database entries with these external tools without problems.";// TRANSLATE
	$l_prefs["db_set_charset_warning"] = "The connection charset should be changed only in a fresh installation of webEdition (without data in the database). Otherwise, all non ASCII characters will be interpreted wrong and may be destroyed.";// TRANSLATE

	/**
	 * HTTP AUTHENTICATION
	 */

	$l_prefs["auth"] = "HTTP autentikointi";
	$l_prefs["useauth"] = "Palvelin k‰ytt‰‰ HTTP<br>autentikointia webEdition<br>hakemistossa";
	$l_prefs["authuser"] = "K‰ytt‰j‰nimi";
	$l_prefs["authpass"] = "Salasana";

	/**
 	* THUMBNAIL DIR
 	*/
	$l_prefs["thumbnail_dir"] = "Esikatselukuvien hakemisto";

	$l_prefs["pagelogger_dir"] = "pageLogger hakemisto";

	/**
	 * HOOKS //TRANS
	 */
	$l_prefs["hooks"] = "\"Koukut\"";
	$l_prefs["hooks_information"] = "\"Koukkujen\" k‰yttˆ mahdollistaa mielivaltaisen PHP-koodin suorittamisen webEditionissa tallennuksen, julkaisun, julkaisun poiston sek‰ mink‰ tahansa sis‰ltˆtyypin poiston yhteydess‰.<br />
	Lis‰‰ tietoa lˆytyy Online-dokumentaatiosta.<br /><br />Salli koukkujen k‰yttˆ?"; 

	/**
	 * Backward compatibility
	 */
	$l_prefs["backwardcompatibility"] = "Backward compatibility";//TRANSLATE
	$l_prefs["backwardcompatibility_tagloading"] = "Load all 'old' we_tag functions";//TRANSLATE
	$l_prefs["backwardcompatibility_tagloading_message"] = "Only necessary if in custom_tags or in PHP code inside templates we_tags are called in the form we_tag_tagname().<br/> Recommended call: we_tag<br/>('tagname',&#36;attribs,&#36;content)";//TRANSLATE


/*****************************************************************************
 * ERROR HANDLING
 *****************************************************************************/


	$l_prefs['error_no_object_found'] = 'Virhesivu objekteille joita ei ole';

	/**
	 * TEMPLATE TAG CHECK
	 */

	$l_prefs["templates"] = "Sivupohjat";
	$l_prefs["disable_template_tag_check"] = "Poista puuttuvien,<br />we:lopetustagien tarkistus";

	/**
	 * ERROR HANDLER
	 */

	$l_prefs["error_use_handler"] = "K‰yt‰ webEdition -j‰rjestelm‰n <br>virheenk‰sittely‰";

	/**
	 * ERROR TYPES
	 */

	$l_prefs["error_types"] = "K‰sittele virhetyypit";
	$l_prefs["error_notices"] = "Ilmoitukset";
	$l_prefs["error_deprecated"] = "deprecated Notices";//TRANSLATE
	$l_prefs["error_warnings"] = "Varoitukset";
	$l_prefs["error_errors"] = "Virheet";

	$l_prefs["error_notices_warning"] = 'We recommend to aktivate the option -Log errors- on all systems; the option -Show errors- should be activated only during development.';//TRANSLATE

	/**
	 * ERROR DISPLAY
	 */

	$l_prefs["error_displaying"] = "Virheiden k‰sittelytapa";
	$l_prefs["error_display"] = "N‰yt‰ virheet";
	$l_prefs["error_log"] = "Kirjaa virheet Lokikirjaan";
	$l_prefs["error_mail"] = "L‰het‰ virheet s‰hkˆpostilla";
	$l_prefs["error_mail_address"] = "Osoite";
	$l_prefs["error_mail_not_saved"] = 'Virheit‰ ei l‰hetet‰ annettuun s‰hkˆpostiosoitteeseen, koska osoite on virheellinen!\n\nJ‰ljell‰olevat asetukset on tallennettu.';

	/**
	 * DEBUG FRAME
	 */

	$l_prefs["show_expert"] = "N‰yt‰ asiantuntija-asetukset";
	$l_prefs["hide_expert"] = "Piilota asiantuntija-asetukset";
	$l_prefs["show_debug_frame"] = "N‰yt‰ seuranta -kehys";
	$l_prefs["debug_normal"] = "Normaalitilassa";
	$l_prefs["debug_seem"] = "Helppok‰yttˆtilassa";
	$l_prefs["debug_restart"] = "Muutokset vaativat uudelleenkirjautumisen";

/*****************************************************************************
 * MODULES
 *****************************************************************************/

	/**
	 * OBJECT MODULE
	 */

	$l_prefs["module_object"] = "Tietokanta/Objektimoduuli";
	$l_prefs["tree_count"] = "N‰ytett‰vien objektien m‰‰r‰";
	$l_prefs["tree_count_description"] = "T‰m‰ arvo m‰‰ritt‰‰ n‰ytett‰vien objektien maksimim‰‰r‰n vasemmassa tiedostolistassa.";

/*****************************************************************************
 * BACKUP
 *****************************************************************************/
	$l_prefs["backup"] = "Varmuuskopio";
	$l_prefs["backup_slow"] = "Hidas";
	$l_prefs["backup_fast"] = "Nopea";
	$l_prefs["performance"] = "T‰ss‰ voit m‰‰ritt‰‰ suoritustason. Suoritustaso riippuu palvelinj‰rjestelm‰st‰. Jos j‰rjestelm‰ss‰ on rajattu m‰‰r‰ resursseja (muisti, aikakatkaisu jne...) valitse hidas taso, muussa tapauksessa nopea taso.";
	$l_prefs["backup_auto"]="Automaattinen";

/*****************************************************************************
 * Validation
 *****************************************************************************/
	$l_prefs['validation']='Validointi';
	$l_prefs['xhtml_default'] = 'Tagien <em>xml</em> m‰‰reen oletusarvo';
	$l_prefs['xhtml_debug_explanation'] = 'XHTML -debug tila tukee kehityst‰ validille xhtml -muotoiselle www-sivulle. Jokaisen we:tagin validiteetti tarkistetaan ja v‰‰r‰t m‰‰reet n‰ytet‰‰n tai poistetaan. Huomioi: T‰m‰ toiminto voi kest‰‰, siksi debug tilaa kannattaa k‰ytt‰‰ ainoastaan kehitystyˆn aikana.';
	$l_prefs['xhtml_debug_headline'] = 'XHTML debugtila';
	$l_prefs['xhtml_debug_html'] = 'Aktivoi XHTML debugtila';
	$l_prefs['xhtml_remove_wrong'] = 'Poista virheelliset m‰‰reet';
	$l_prefs['xhtml_show_wrong_headline'] = 'Ilmoita virheellisist‰ m‰‰reist‰';
	$l_prefs['xhtml_show_wrong_html'] = 'Aktivoi';
	$l_prefs['xhtml_show_wrong_text_html'] = 'Tekstin‰';
	$l_prefs['xhtml_show_wrong_js_html'] = 'Javascript -varoituksena';
	$l_prefs['xhtml_show_wrong_error_log_html'] = 'error logissa (PHP)';


/*****************************************************************************
 * max upload size
 *****************************************************************************/
	$l_prefs["we_max_upload_size"]="Maksimi latauskoko,<br>joka n‰ytet‰‰n vihjeiss‰";
	$l_prefs["we_max_upload_size_hint"]="(MB, 0=automaattinen)";

/*****************************************************************************
 * we_new_folder_mod
 *****************************************************************************/
	$l_prefs["we_new_folder_mod"]="Oikeudet<br>uusille hakemistoille";
	$l_prefs["we_new_folder_mod_hint"]="(oletus on 755)";

/*****************************************************************************
* we_doctype_workspace_behavior
*****************************************************************************/

   $l_prefs["we_doctype_workspace_behavior_hint0"] = "Dokumentin oletushakemiston t‰ytyy sijaita k‰ytt‰j‰n tyˆtilassa, jotta k‰ytt‰j‰ voi valita dokumentin tyypin.";
   $l_prefs["we_doctype_workspace_behavior_hint1"] = "K‰ytt‰j‰n tyˆtilan on ssijaittava k‰ytt‰j‰lle m‰‰ritetyn dokumenttityypin oletushakemistossa, jotta dokumenttia voidaan muokata.";
   $l_prefs["we_doctype_workspace_behavior_1"] = "K‰‰nteinen";
   $l_prefs["we_doctype_workspace_behavior_0"] = "Standardi";
   $l_prefs["we_doctype_workspace_behavior"] = "Dokumenttityypin k‰ytt‰ytyminen";


/*****************************************************************************
 * jupload
 *****************************************************************************/

	$l_prefs['use_jupload'] = 'K‰yt‰ javaa tiedostojen l‰hetyksess‰';

/*****************************************************************************
 * message_reporting
 *****************************************************************************/
	$l_prefs["message_reporting"]["information"] = "Voit m‰‰ritt‰‰ alla olevilla laatikoilla haluatko saada ilmoituksen webEditionin tapahtumista.";
	
	$l_prefs["message_reporting"]["headline"] = "Ilmoitukset";
	$l_prefs["message_reporting"]["show_notices"] = "N‰yt‰ huomautukset";
	$l_prefs["message_reporting"]["show_warnings"] = "N‰yt‰ varoitukset";
	$l_prefs["message_reporting"]["show_errors"] = "N‰yt‰ virheet";


/*****************************************************************************
 * Module Activation
 *****************************************************************************/
	$l_prefs["module_activation"]["information"] = "T‰‰ll‰ voit aktivoida ja deaktivoida moduuleja tarpeesi mukaan.<br />Deaktivoidut moduulit voivat parantaa webEditionin yleist‰ suorituskyky‰.";
	
	$l_prefs["module_activation"]["headline"] = "Moduulien aktivointi";

/*****************************************************************************
 * Email settings
 *****************************************************************************/
	
	$l_prefs["mailer_information"] = "S‰‰d‰ l‰hett‰‰kˆ webEdition s‰hkˆpostit PHP:n mail()-funktiolla vai erillisell‰ SMTP-palvelimella.<br /><br />SMTP-palvelinta k‰ytett‰ess‰, viestien sp‰mmiksi tulkitsemisen riski laskee.";
	
	$l_prefs["mailer_type"] = "Mailerin tyyppi";
	$l_prefs["mailer_php"] = "K‰yt‰ php mail() funktiota";
	$l_prefs["mailer_smtp"] = "K‰yt‰ SMTP palvelinta";
	$l_prefs["email"] = "E-Mail";
	$l_prefs["tab_email"] = "E-Mail";
	$l_prefs["smtp_auth"] = "Autentikaatio";
	$l_prefs["smtp_server"] = "SMTP palvelin";
	$l_prefs["smtp_port"] = "SMTP portti";
	$l_prefs["smtp_username"] = "K‰ytt‰j‰tunnus";
	$l_prefs["smtp_password"] = "Salasana";
	$l_prefs["smtp_halo"] = "SMTP helo";
	$l_prefs["smtp_timeout"] = "SMTP aikakatkaisu";
	$l_prefs["smtp_encryption"] = "encrypted transport";// TRANSLATE
	$l_prefs["smtp_encryption_none"] = "no";// TRANSLATE
	$l_prefs["smtp_encryption_ssl"] = "SSL";// TRANSLATE
	$l_prefs["smtp_encryption_tls"] = "TLS";// TRANSLATE


/*****************************************************************************
 * Versions settings
 *****************************************************************************/

	$l_prefs["versioning"] = "Versiointi";
	$l_prefs["version_all"] = "kaikki";
	$l_prefs["versioning_activate_text"] = "Valitse mille sis‰ltˆtyypeille versiointi aktivoidaan.";
	$l_prefs["versioning_time_text"] = "M‰‰rittelem‰ll‰ aikajakson voit valita mist‰ vanhemmat versiot poistetaan automaattisesti.";
	$l_prefs["versioning_time"] = "Aikajakso";
	$l_prefs["versioning_anzahl_text"] = "Luotavien versioiden m‰‰r‰ jokaisesta dokumentista tai objektista.";
	$l_prefs["versioning_anzahl"] = "Numero";
	$l_prefs["versioning_wizard_text"] = "Poista tai palauta versioita avaamalla Versio-Velho.";
	$l_prefs["versioning_wizard"] = "Avaa Versio-Velho";
	$l_prefs["ContentType"] = "Sis‰ltˆtyyppi";
	$l_prefs["versioning_create_text"] = "M‰‰rittele mitk‰ tapahtumat luovat uusia versioita. Joko ainoastaan julkaistaessa tai myˆs tallentaessa, poistaessa, poistaessa julkaisusta sek‰ tuonnissa.";
	$l_prefs["versioning_create"] = "Luo Versio";
	$l_prefs["versions_create_publishing"] = "vain julkaistaessa";
	$l_prefs["versions_create_always"] = "aina";
	$l_prefs["versioning_templates_text"] = "Define special values for the <b>versioning of templates</b>";// TRANSLATE
	$l_prefs["versions_create_tmpl_publishing"] = "only using special button";// TRANSLATE
	$l_prefs["versions_create_tmpl_always"] = "always";// TRANSLATE

	
	$l_prefs['use_jeditor'] = "K‰yt‰";
	$l_prefs["editor_font_colors"] = 'M‰‰rittele fontin v‰rit';
	$l_prefs["editor_normal_font_color"] = 'Oletus';
	$l_prefs["editor_we_tag_font_color"] = 'webEdition tagit';
	$l_prefs["editor_we_attribute_font_color"] = 'webEdition m‰‰reet';
	$l_prefs["editor_html_tag_font_color"] = 'HTML tagit';
	$l_prefs["editor_html_attribute_font_color"] = 'HTML m‰‰reet';
	$l_prefs["editor_pi_tag_font_color"] = 'PHP koodi';
	$l_prefs["editor_comment_font_color"] = 'Kommentit';
	$l_prefs['editor_highlight_colors'] = 'Highlighting colors';// TRANSLATE
	$l_prefs['editor_linenumbers'] = 'Line numbers';// TRANSLATE
	$l_prefs['editor_completion'] = 'Code Completion';// TRANSLATE
	$l_prefs['editor_tooltips'] = 'Tooltips on we:tags';// TRANSLATE
	$l_prefs['editor_docuclick'] = 'Docu integration';// TRANSLATE
	$l_prefs['editor_enable'] = 'Enable';// TRANSLATE
	$l_prefs['editor_plaintext'] = 'Plain textarea';// TRANSLATE
	$l_prefs['editor_java'] = 'Java editor';// TRANSLATE
	$l_prefs['editor_javascript'] = 'JavaScript editor (beta)';// TRANSLATE
	$l_prefs['editor_javascript_information'] = 'The JavaScript editor is still in beta stadium. Depending on which of the following options you\'ll activate, there might occur errors. Code completion is currently not working in Internet Explorer. For a complete list of known issues please have a look at the <a href="http://qa.webedition.org/tracker/search.php?project_id=107&sticky_issues=on&sortby=last_updated&dir=DESC&hide_status_id=90" target="_blank">webEdition bugtracker</a>.';// TRANSLATE
	
	
	$l_prefs["juplod_not_installed"] = 'JUpload ei ole asennettu!';
	

?>