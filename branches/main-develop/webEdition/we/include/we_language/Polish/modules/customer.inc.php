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
		'menu_customer' => "Klienci",
		'menu_new' => "Nowy",
		'menu_save' => "Zapisz",
		'menu_delete' => "Usuń",
		'menu_exit' => "Zakończ",
		'menu_info' => "Informacja",
		'menu_help' => "Pomoc",
		'menu_admin' => "Administracja",
		'save_changed_customer' => "Customer has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'customer_saved_ok' => "Zapisano dane klienta '%s'",
		'customer_saved_nok' => "Nie udało się zapisac danych klienta '%s'!",
		'nothing_to_save' => "Brak danych do zapisania!",
		'username_exists' => "Nazwa klienta '%s' już istnieje!",
		'username_empty' => "Nie wpisano nazwy klienta!",
		'password_empty' => "Nie wpisano hasła (Password)!",
		'customer_deleted' => "Usunięto dane klienta!",
		'nothing_to_delete' => "Brak danych do skasowania!",
		'no_space' => "Nie wolno używać spacji",
		'customer_data' => "Dane klienta",
		'first_name' => "Imię",
		'second_name' => "Nazwisko",
		'username' => "Nazwa klienta",
		'password' => "Hasło",
		'login' => "Login", // TRANSLATE
		'login_denied' => "Access denied", // TRANSLATE
		'autologin' => "Auto-Login", // TRANSLATE
		'autologin_request' => "requested", // TRANSLATE

		'permissions' => "Uprawnienia",
		'password_alert' => "Hasło musi się składać z przynajmniej 4 znaków",
		'delete_alert' => "Usunąć wybrane dane klienta?\\n Na pewno?",
		'created_by' => "Sporządził",
		'changed_by' => "Zmienił",
		'no_perms' => "Brak uprawnień do korzystania z tej opcji!",
		'topic' => "Topic", // TRANSLATE

		'not_nummer' => "Initial letter cannot be a number.", // TRANSLATE
		'field_not_empty' => "The field name must be completed.", // TRANSLATE
		'delete_field' => "Are you sure that you want to delete this field? This process cannot be reversed.", // TRANSLATE

		'display' => "Wyświetl",
		'insert_field' => "Insert field", // TRANSLATE
//---- new things

		'customer' => "Customer", // TRANSLATE
		'common' => "General", // TRANSLATE
		'all' => "All", // TRANSLATE
		'sort' => "Sort", // TRANSLATE
		'branch' => "View", // TRANSLATE

		'field_name' => "Name", // TRANSLATE
		'field_type' => "Type", // TRANSLATE
		'field_default' => "Default", // TRANSLATE
		'add_mail' => "Insert E-mail",
		'edit_mail' => "Edit E-mail", // CHECK
// changed from: "Edit E-mail"
// changed to  : "Edit email"


		'no_branch' => "No view has been selected!", // TRANSLATE
		'no_field' => "No field has been selected!", // TRANSLATE

		'field_saved' => "Field is saved.", // TRANSLATE
		'field_deleted' => "Field is deleted from %s view.", // TRANSLATE
		'del_fild_question' => "Do you want to delete the field?", // TRANSLATE

		'field_admin' => "Fields administration", // TRANSLATE
		'sort_admin' => "Sort administration", // TRANSLATE

		'name' => "Name", // TRANSLATE
		'sort_branch' => "View", // TRANSLATE
		'sort_field' => "Field", // TRANSLATE
		'sort_order' => "Order", // TRANSLATE
		'sort_saved' => "Sort is saved.", // TRANSLATE
		'sort_name' => "sort", // TRANSLATE
		'sort_function' => "Function", // TRANSLATE
		'no_sort' => "--No Sort--", // TRANSLATE

		'branch_select' => "Select view", // TRANSLATE
		'fields' => "Fields", // TRANSLATE

		'add_sort_group' => "Insert new group", // TRANSLATE
		'search' => "Search", // TRANSLATE
		'search_for' => "Search for", // TRANSLATE
		'simple_search' => "Simple search", // TRANSLATE
		'advanced_search' => "Advanced search", // TRANSLATE
		'search_result' => "Result", // TRANSLATE

		'no_value' => "[-No value-]", // TRANSLATE
		'other' => "Other", // TRANSLATE

		'cannot_save_property' => "The '%s' field is protected and cannot be saved!", // TRANSLATE

		'settings' => "Settings", // TRANSLATE

		'Username' => "Username", // TRANSLATE
		'Password' => "Password", // TRANSLATE
		'Forname' => "First name", // TRANSLATE
		'Surname' => "Last name", // TRANSLATE
		'MemeberSince' => "Member since", // TRANSLATE
		'LastLogin' => "Last login", // TRANSLATE
		'LastAccess' => "Last access", // TRANSLATE

		'default_date_type' => "Default date format", // TRANSLATE
		'custom_date_format' => "Custom date format", // TRANSLATE
		'default_sort_view' => "Default sort view", // TRANSLATE

		'unix_ts' => "Unix timestamp", // TRANSLATE
		'mysql_ts' => "MySQL timestamp", // TRANSLATE
		'start_year' => "Start year", // TRANSLATE

		'settings_saved' => "Settings have been saved.", // TRANSLATE
		'settings_not_saved' => "Failed to save settings!", // TRANSLATE

		'data' => "Data", // TRANSLATE

		'add_field' => "Add field", // TRANSLATE
		'edit_field' => "Edit field", // TRANSLATE

		'edit_branche' => "Edit view", // TRANSLATE
		'not_implemented' => "not implemented", // TRANSLATE
		'branch_no_edit' => "The area is protected and cannot be changed!", // TRANSLATE
		'name_exists' => "That name already exists!", // TRANSLATE

		'import' => "Import", // TRANSLATE
		'export' => "Export", // TRANSLATE

		'export_title' => "Export wizard", // TRANSLATE
		'import_title' => "Import wizard", // TRANSLATE

		'export_step1' => "Format eksportu",
		'export_step2' => "Wybór klienta",
		'export_step3' => "Dane do eksportu",
		'export_step4' => "Zakończono eksport",
		'import_step1' => "Format importu",
		'import_step2' => "Dane z importu",
		'import_step3' => "Wybór zestawu danych",
		'import_step4' => "Przyporządkowanie pól danych",
		'import_step5' => "Export finished",
		'file_format' => "File format", // TRANSLATE
		'export_to' => "Export to", // TRANSLATE

		'export_to_server' => "Server", // TRANSLATE
		'export_to_ftp' => "FTP", // TRANSLATE
		'export_to_local' => "Local", // TRANSLATE

		'ftp_host' => "Host", // TRANSLATE
		'ftp_username' => "User name", // TRANSLATE
		'ftp_password' => "Password", // TRANSLATE

		'filename' => "File name", // TRANSLATE
		'path' => "Path", // TRANSLATE

		'xml_format' => "XML", // TRANSLATE
		'csv_format' => "CSV", // TRANSLATE

		'csv_delimiter' => "Delimiter", // TRANSLATE
		'csv_enclose' => "Enclose", // TRANSLATE
		'csv_escape' => "Escape", // TRANSLATE
		'csv_lineend' => "Line end", // TRANSLATE
		'import_charset' => "Import charset", // TRANSLATE //
		'csv_null' => "NULL replacment", // TRANSLATE
		'csv_fieldnames' => "First row contains fileds name", // TRANSLATE

		'generic_export' => "Generic export", // TRANSLATE
		'gxml_export' => "Generic-XML export", // TRANSLATE
		'txt_gxml_export' => "Export to \"fleet\" XML file, as e.g phpMyAdmin did. The fields from data set will be mapped to the webEdition fields.", // TRANSLATE
		'csv_export' => "CSV export", // TRANSLATE
		'txt_csv_export' => "Export to CSV file (Comma Separated Values) or other selected text format (z. B. *.txt). The fields from data set will be mapped to the webEdition fields.", // TRANSLATE
		'csv_params' => "CSV file settings", // TRANSLATE

		'filter_selection' => "Filter selection", // TRANSLATE
		'manual_selection' => "Manuel selection", // TRANSLATE
		'sortname_empty' => "Sort name is empty!", // TRANSLATE
		'fieldname_exists' => "The field name already exists!", // TRANSLATE
		'treetext_format' => "Menu text format", // TRANSLATE
		'we_filename_notValid' => "Podana nazwa klienta jest nieprawidłowa!\\nThe sign / is forbidden.",//TRANSLATE
		'windows' => "Windows format", // TRANSLATE
		'unix' => "UNIX format", // TRANSLATE
		'mac' => "Mac format", // TRANSLATE

		'comma' => ", {comma}", // TRANSLATE
		'semicolon' => "; {semicolon}", // TRANSLATE
		'colon' => ": {colon}", // TRANSLATE
		'tab' => "\\t {tab}", // TRANSLATE
		'space' => "  {space}", // TRANSLATE
		'double_quote' => "\" {double quote}", // TRANSLATE
		'single_quote' => "' {single quote}", // TRANSLATE

		'exporting' => "Exporting...", // TRANSLATE
		'cdata' => "Kodowanie",
		'export_xml_cdata' => "Add CDATA sections", // TRANSLATE
		'export_xml_entities' => "Replace entities", // TRANSLATE

		'export_finished' => "Eksport zakończono z powodzeniem.",
		'server_finished' => "Dane do kepsortu zostały zapisane na serwerze.",
		'download_starting' => "Download of the export file has been started.<br><br>If the download does not start after 10 seconds,<br>", // TRANSLATE
		'download' => "please click here.", // TRANSLATE
		'download_failed' => "Either the file you requested does not exist or you are not permitted to download it.", // TRANSLATE

		'generic_import' => "Generic import", // TRANSLATE
		'gxml_import' => "Generic XML import", // TRANSLATE
		'txt_gxml_import' => "Import \"flat\" XML files, such as those provided by phpMyAdmin. The dataset fields have to be allocated to the customer dataset fields.", // TRANSLATE
		'csv_import' => "CSV import", // TRANSLATE
		'txt_csv_import' => "Import CSV files (Comma Separated Values) or modified textformats (e. g. *.txt). The dataset fields are assigned to the customer fields.", // TRANSLATE
		'source_file' => "Source file", // TRANSLATE

		'server_import' => "Import file from server", // TRANSLATE
		'upload_import' => "Import file from the local harddrive.", // TRANSLATE
		'file_uploaded' => "Załadowano plik.",
		'num_data_sets' => "Rekordy:",
		'to' => "do",
		'well_formed' => "The XML document is well-formed.", // TRANSLATE
		'select_elements' => "Please choose the datasets to import.", // TRANSLATE
		'xml_valid_1' => "The XML file is valid and contains", // TRANSLATE
		'xml_valid_m2' => "węzły potomne XML w pierwszem wymiarze o różnych nazwach. Wybierz węzły XML i liczbę elementów, które chcesz importować.",
		'not_well_formed' => "The XML document is not well-formed and cannot be imported.", // TRANSLATE
		'missing_child_node' => "The XML document is well-formed, but contains no XML nodes and can therefore not be imported.", // TRANSLATE

		'none' => "-- none --", // TRANSLATE
		'any' => "-- none --", // TRANSLATE
		'we_flds' => "webEdition&nbsp;fields", // TRANSLATE
		'rcd_flds' => "Dataset&nbsp;fields", // TRANSLATE
		'attributes' => "Attribute", // TRANSLATE
		'we_title' => "Title", // TRANSLATE
		'we_description' => "Description", // TRANSLATE
		'we_keywords' => "Keywords", // TRANSLATE

		'pfx' => "Prefix", // TRANSLATE
		'pfx_doc' => "Document", // TRANSLATE
		'pfx_obj' => "Object", // TRANSLATE
		'rcd_fld' => "Dataset field", // TRANSLATE
		'auto' => "Auto", // TRANSLATE
		'asgnd' => "Assigned", // TRANSLATE

		'remark_csv' => "You are able to import CSV files (Comma Separated Values) or modified text formats (e. g. *.txt). The field delimiter (e. g. , ; tab, space) and text delimiter (= which encapsulates the text inputs) can be preset at the import of these file formats.", // TRANSLATE
		'remark_xml' => "To avoid the predefined timeout of a PHP-script, select the option \"Import data-sets separately\", to import large files.<br>If you are unsure whether the selected file is webEdition XML or not, the file can be tested for validity and syntax.", // TRANSLATE

		'record_field' => "Dataset field", // TRANSLATE
		'missing_filesource' => "Source file is empty! Please select a sorce file.", // TRANSLATE
		'importing' => "Importing", // TRANSLATE
		'same_names' => "Same names", // TRANSLATE
		'same_rename' => "Rename", // TRANSLATE
		'same_overwrite' => "Overwrite", // TRANSLATE
		'same_skip' => "Skip", // TRANSLATE

		'rename_customer' => "The customer '%s' has been renamed to '%s'", // TRANSLATE
		'overwrite_customer' => "The customer '%s' has been overwritten", // TRANSLATE
		'skip_customer' => "The customer '%s' has been skipped", // TRANSLATE

		'import_finished_desc' => "%s new customers have been imported!", // TRANSLATE
		'show_log' => " Warnings", // TRANSLATE
		'import_step5' => "Eksport zakończono",
		'view' => "Widok",
		'registered_user' => "Zarejestrowany użytkownik",
		'unregistered_user' => "Niezarejestrowany użytkownik",
		'default_soting_no_del' => "Wybrany porządek sortowania jest używany w ustawieniach i nie można go usunąć!",
		'we_fieldname_notValid' => "Podana nazwa pola jest nieprawidłowa!\\nDopuszczalne znaki to litery od a do z (wielkie i małe) , cyfry oraz znak podkreślenia (_)",
		'orderTab' => 'Orders of this customer', // TRANSLATE
		'default_order' => 'Zadany porządek',
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
