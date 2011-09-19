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
 * Language file: customer.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_customer = array(
		'menu_customer' => "Klant",
		'menu_new' => "Nieuw",
		'menu_save' => "Bewaar",
		'menu_delete' => "Verwijder",
		'menu_exit' => "Eind",
		'menu_info' => "Info", // TRANSLATE
		'menu_help' => "Help", // TRANSLATE

		'menu_admin' => "Administratie",
		'save_changed_customer' => "Customer has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'customer_saved_ok' => "Klant '%s' is succesvol bewaard.",
		'customer_saved_nok' => "Klant '%s' kan niet bewaard worden",
		'nothing_to_save' => "Er is niks om te bewaren",
		'username_exists' => "Gebruikersnaam '%s' bestaat al!",
		'username_empty' => "Gebruikersnaam is nog niet ingevuld!",
		'password_empty' => "Wachtwoord is nog niet ingevuld!",
		'customer_deleted' => "Klant is succesvol verwijderd.",
		'nothing_to_delete' => "Er is niks om te verwijderen!",
		'no_space' => "Spaties zijn niet toegestaan.",
		'customer_data' => "Klant details",
		'first_name' => "Voornaam",
		'second_name' => "Naam",
		'username' => "Gebruikers naam",
		'password' => "Wachtwoord",
		'login' => "Login", // TRANSLATE
		'login_denied' => "Toegang geweigerd",
		'autologin' => "Auto-Login", // TRANSLATE
		'autologin_request' => "requested", // TRANSLATE

		'permissions' => "Rechten",
		'password_alert' => "Het wachtwoord moet minimaal 4 karakters lang zijn.",
		'delete_alert' => "Verwijder alle klant details.\\n Weet u het zeker?",
		'created_by' => "Aangemaakt door",
		'changed_by' => "Gewijzigd door",
		'no_perms' => "U heeft niet de juiste rechten om deze optie te gebruiken.",
		'topic' => "Onderdeel",
		'not_nummer' => "Primaire letter mag geen nummer zijn.",
		'field_not_empty' => "De veld naam moet compleet zijn.",
		'delete_field' => "Weet u zeker dat u dit veld wilt verwijderen? Dit proces kan niet ongedaan gemaakt worden.",
		'display' => "Toon",
		'insert_field' => "Voeg veld in",
//---- new things

		'customer' => "Klant",
		'common' => "Algemeen",
		'all' => "Alle",
		'sort' => "Sorteer",
		'branch' => "Weergave",
		'field_name' => "Naam",
		'field_type' => "Type", // TRANSLATE
		'field_default' => "Standaard",
		'add_mail' => "Voer E-mail in",
		'edit_mail' => "Wijzig E-mail",
		'no_branch' => "Geen weergave geselecteerd!",
		'no_field' => "Geen veld geselecteerd!",
		'field_saved' => "Veld is bewaard.",
		'field_deleted' => "Veld is verwijderd van %s weergave.",
		'del_fild_question' => "Wilt u dit veld verwijderen?",
		'field_admin' => "Veld administratie",
		'sort_admin' => "Sorteer administratie",
		'name' => "Naam",
		'sort_branch' => "Weergave",
		'sort_field' => "Veld",
		'sort_order' => "Volgorde",
		'sort_saved' => "Volgorde is bewaard.",
		'sort_name' => "sorteer",
		'sort_function' => "Functie",
		'no_sort' => "--Geen volgorde--",
		'branch_select' => "Selecteer weergave",
		'fields' => "Velden",
		'add_sort_group' => "Voeg nieuwe groep in",
		'search' => "Zoeken",
		'search_for' => "Zoeken naar",
		'simple_search' => "Eenvoudig zoeken",
		'advanced_search' => "Geadvanceerd zoeken",
		'search_result' => "Resultaat",
		'no_value' => "[-Geen waarde-]",
		'other' => "Andere",
		'cannot_save_property' => "Het '%s' veld is beveiligd en kan niet worden bewaard!",
		'settings' => "Instellingen",
		'Username' => "Gebruikersnaam",
		'Password' => "Wachtwoord",
		'Forname' => "Voornaam",
		'Surname' => "Achternaam",
		'MemeberSince' => "Lid sinds",
		'LastLogin' => "Laaste inlog",
		'LastAccess' => "Laaste toegang",
		'default_date_type' => "Standaard datum formaat",
		'custom_date_format' => "Aanpasbaar datum formaat",
		'default_sort_view' => "Standaard sorteer weergave",
		'unix_ts' => "Unix tijdstempel",
		'mysql_ts' => "MySQL tijdstempel",
		'start_year' => "Start jaar",
		'settings_saved' => "Instellingen zijn bewaard.",
		'settings_not_saved' => "Fout tijdens het bewaren van de instellingen!",
		'data' => "Gegevens",
		'add_field' => "Voeg veld toe",
		'edit_field' => "Wijzig veld",
		'edit_branche' => "Wijzig weergave",
		'not_implemented' => "niet geïmplementeerd",
		'branch_no_edit' => "De omgeving is beveiligd en niet worden gewijzigd!",
		'name_exists' => "Deze naam bestaal al!",
		'import' => "Importeer",
		'export' => "Exporteer",
		'export_title' => "Exporteer wizard",
		'import_title' => "Importeer wizard",
		'export_step1' => "Export formaat",
		'export_step2' => "Selecteer klanten",
		'export_step3' => "Exporteer data",
		'export_step4' => "Exporteren voltooid",
		'import_step1' => "Import formaat",
		'import_step2' => "Importeer data",
		'import_step3' => "Selecteer dataset",
		'import_step4' => "Ken data velden toe",
		'import_step5' => "Export finished",
		'file_format' => "Bestandsformaat",
		'export_to' => "Exporteer naar",
		'export_to_server' => "Server", // TRANSLATE
		'export_to_ftp' => "FTP", // TRANSLATE
		'export_to_local' => "Lokaal",
		'ftp_host' => "Host", // TRANSLATE
		'ftp_username' => "Gebruikersnaam",
		'ftp_password' => "Wachtwoord",
		'filename' => "Bestandsnaam",
		'path' => "Pad",
		'xml_format' => "XML", // TRANSLATE
		'csv_format' => "CSV", // TRANSLATE

		'csv_delimiter' => "Scheidingsteken",
		'csv_enclose' => "Insluiten",
		'csv_escape' => "Uitsluiten",
		'csv_lineend' => "Regel einde",
		'import_charset' => "Import charset", // TRANSLATE
		'csv_null' => "NULL vervanging",
		'csv_fieldnames' => "Eerste rij bevat veldnaam",
		'generic_export' => "Generieke export",
		'gxml_export' => "Generieke-XML export",
		'txt_gxml_export' => "Exporteer naar \"fleet\" XML bestand, zoals bijv. phpMyAdmin deed. De velden uit de data set worden ingedeeld naar de webEdition velden.",
		'csv_export' => "CSV export", // TRANSLATE
		'txt_csv_export' => "Exporteer naar CSV bestand (Comma Separated Values) of een ander geselecteerd tekst formaat (Bijv. *.txt). De velden uit de data set worden ingedeeld naar de webEdition velden.",
		'csv_params' => "CSV bestands instellingen",
		'filter_selection' => "Filter selectie",
		'manual_selection' => "Handmatige selectie",
		'sortname_empty' => "Sorteer naam is leeg!",
		'fieldname_exists' => "De veld naam bestaat al!",
		'treetext_format' => "Menu tekst formaat",
		'we_filename_notValid' => "Ongeldige gebruikersnaam!\\nThe sign / is forbidden.",//TRANSLATE
		'windows' => "Windows formaat",
		'unix' => "UNIX formaat",
		'mac' => "Mac formaat",
		'comma' => ", {komma}",
		'semicolon' => "; {punt komma}",
		'colon' => ": {dubbele punt}",
		'tab' => "\\t {tab}", // TRANSLATE
		'space' => "  {spatie}",
		'double_quote' => "\" {dubbele aanhalingstekens}",
		'single_quote' => "' {enkel aanhalingsteken}",
		'exporting' => "Bezig met exporteren...",
		'cdata' => "Codering",
		'export_xml_cdata' => "Voeg CDATA secties toe",
		'export_xml_entities' => "Vervang entiteiten",
		'export_finished' => "Export voltooid.",
		'server_finished' => "Het export bestand is bewaard op de server.",
		'download_starting' => "Download van het export bestand is gestart.<br><br>Indien de download niet start binnen 10 seconden,<br>",
		'download' => "klik dan hier a.u.b.",
		'download_failed' => "Of het gekozen bestand bestaat niet of u bent niet bevoegd om het te downloaden.",
		'generic_import' => "Generieke import",
		'gxml_import' => "Generieke XML import",
		'txt_gxml_import' => "Importeer \"platte\" XML bestanden, zoals aangeleverd door phpMyAdmin. De dataset velden moeten toegewezen worden aan de klant dataset velden.",
		'csv_import' => "CSV import", // TRANSLATE
		'txt_csv_import' => "Importeer CSV bestanden (Comma Separated Values) of aangepaste tekstformaten (bijv. *.txt). De dataset velden zijn toegekend aan de klant velden.",
		'source_file' => "Bron bestand",
		'server_import' => "Importeer bestand van server",
		'upload_import' => "Importeer bestand van lokale harde schijf.",
		'file_uploaded' => "Het bestand is ge-upload.",
		'num_data_sets' => "Datasets:", // TRANSLATE
		'to' => "naar",
		'well_formed' => "Het XML document is well-formed.",
		'select_elements' => "Kies a.u.b. de te importeren datasets.",
		'xml_valid_1' => "Het XML bestand is geldig en bevat",
		'xml_valid_m2' => "XML child node in het eerste niveau met verschillende namen. Kies a.ub. de XML node en het aantal te importeren elementen.",
		'not_well_formed' => "Het XML document is niet well-formed en kan niet geïmporteerd worden.",
		'missing_child_node' => "Het XML document is well-formed, maar bevat geen XML nodes en kan daardoor niet geïmporteerd worden.",
		'none' => "-- geen --",
		'any' => "-- geen --",
		'we_flds' => "webEdition&nbsp;velden",
		'rcd_flds' => "Dataset&nbsp;velden",
		'attributes' => "Attribuut",
		'we_title' => "Titel",
		'we_description' => "Omschrijving",
		'we_keywords' => "Trefwoorden",
		'pfx' => "Voorvoegsel",
		'pfx_doc' => "Document", // TRANSLATE
		'pfx_obj' => "Object", // TRANSLATE
		'rcd_fld' => "Dataset veld",
		'auto' => "Auto", // TRANSLATE
		'asgnd' => "Toegekend",
		'remark_csv' => "U kunt CSV bestanden (Comma Separated Values) of aangepaste tekst formaten importeren (bijv. *.txt). De veld scheider (bijv. , ; tab, spatie) and tekst scheider (= welke de tekst invoer omvat) kunnen vooraf ingesteld worden bj het importeren van deze bestands formaten.",
		'remark_xml' => "Om de vooraf ingestelde timeout van een PHP-script te omzijlen, selecteer de optie \"Importeer data-sets afzonderlijk\", om grote bestanden te importeren.<br>Als u niet zeker weet of het geselecteerde bestand webEdition XML is of niet, kan het bestand getest worden op geldigheid en syntax.",
		'record_field' => "Dataset veld",
		'missing_filesource' => "Bron bestand is leeg! Selecteer a.u.b. een bron bestand.",
		'importing' => "Bezig met importeren",
		'same_names' => "Zelfde namen",
		'same_rename' => "Hernoem",
		'same_overwrite' => "Overschrijf",
		'same_skip' => "Sla over",
		'rename_customer' => "De klant '%s' is hernoemd naar '%s'",
		'overwrite_customer' => "De klant '%s' is overschreven",
		'skip_customer' => "De klant '%s' is overgeslagen",
		'import_finished_desc' => "%s nieuwe klanten zijn geïmporteerd!",
		'show_log' => " Waarschuwingen",
		'import_step5' => "Importeren voltooid",
		'view' => "Bekijk",
		'registered_user' => "geregisteerde Gebruiker",
		'unregistered_user' => "niet geregisteerde Gebruiker",
		'default_soting_no_del' => "De sortering wordt gebruikt in de instellingen en moet niet verwijderd worden!",
		'we_fieldname_notValid' => "Ongeldige veld naam!\\nGeldige karakters zijn alfa-numeriek, onder en bovenkast, eveneens als de underscore (a-z, A-Z, 0-9, _)",
		'orderTab' => 'Bestellingen van deze klant',
		'default_order' => 'standaard bestelling',
		'ASC' => 'ascending', // TRANSLATE
		'DESC' => 'descending', // TRANSLATE

		'connected_with_customer' => "Connected with customer", // TRANSLATE
		'one_customer' => "Customer", // TRANSLATE
		'sort_edit_fields_explain' => "If a field is apparently not moving, it moves along fields in other branches, not visible here", // TRANSLATE
		'objectTab' => 'Objects of this customer',
		'documentTab' => 'Documents of this customer',
		'NoDocuments' => 'The customer has no documents',
		'NoObjects' => 'The customer has no objects',
		'ID' => 'ID',
		'Filename' => 'Filename',
		'Aenderungsdatum' => 'Modification date',
		'Titel' => 'Title',
);
