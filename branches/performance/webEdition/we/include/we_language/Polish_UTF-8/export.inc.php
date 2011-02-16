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
 * Language file: export.inc.php
 * Provides language strings.
 * Language: English
 */
$l_export = array(
		'save_changed_export' => "Export has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'auto_selection' => "Automatic selection", // TRANSLATE
		'txt_auto_selection' => "Eksportuje automatycznie - wg typu lub klasy dokumentu - wybrane dokumenty lub obiekty.",
		'txt_auto_selection_csv' => "Exports objects automatically according to their class.", // TRANSLATE
		'manual_selection' => "Wybór ręczny",
		'txt_manual_selection' => "Eksportuje ręcznie wybrane dokumenty lub obiekty",
		'txt_manual_selection_csv' => "Exports manually selected objects.", // TRANSLATE
		'element' => "Wybór elementów",
		'documents' => "Dokumenty",
		'objects' => "Obiekty",
		'step1' => "Określ wybór elementów",
		'step2' => "Wybierz elementy do wyeksportowania",
		'step3' => "Generic Export",
		'step10' => "Zakończ eksport",
		'step99' => "Błąd w trakcie eksportowania",
		'step99_notice' => "Eksport jest nie możliwy",
		'server_finished' => "Plik eksportu został zapisany na serwerze.",
		'backup_finished' => "Eksport został zakończony.",
		'download_starting' => "Rozpoczęto pobieranie pliku eksportu.<br><br>Jeżeli pobieranie pliku nie rozpocznie się w ciągu 10 sekund,<br>",
		'download' => "kliknij tutaj.",
		'download_failed' => "Żądany plik albo nie istnieje albo nie masz uprawnień do pobrania go.",
		'file_format' => "Format pliku",
		'export_to' => "Eksport do",
		'export_to_server' => "Serwer",
		'export_to_local' => "Lokalny dysk twardy",
		'cdata' => "Kodowanie",
		'export_xml_cdata' => "Dodaj wycinki CDATA",
		'export_xml_entities' => "Zastąp encje",
		'filename' => "Nazwa pliku",
		'path' => "Ścieżka",
		'doctypename' => "Dokumenty danego typu",
		'classname' => "Obiekty klasy",
		'dir' => "Katalog",
		'categories' => "Kategorie",
		'wizard_title' => "Kreator eksportu",
		'xml_format' => "XML", // TRANSLATE
		'csv_format' => "CSV", // TRANSLATE
		'csv_delimiter' => "Separator",
		'csv_enclose' => "Ogranicznik tekstu",
		'csv_escape' => "Znak ESC",
		'csv_lineend' => "Format pliku",
		'csv_null' => "Zastąpienie NULL",
		'csv_fieldnames' => "Pierwszy wiersz zawiera nazwy pól",
		'windows' => "Format Windows",
		'unix' => "Format UNIX",
		'mac' => "Format Mac",
		'generic_export' => "Generic Export",
		'title' => "Kreator eksportu",
		'gxml_export' => "Generic XML Export",
		'txt_gxml_export' => "Eksport dokumentów i obiektów webEdition do  \"płaskiego\" pliku XML (3 poziomy).",
		'csv_export' => "Eksport do CSV",
		'txt_csv_export' => "Eksport obiektów CSV do pliku CSV (Comma Separated Values).",
		'csv_params' => "Ustawienia",
		'error' => "Eksport przebiegł z problemami!",
		'error_unknown' => "Wystąpił nieznany błąd!",
		'error_object_module' => "Eksport danych do plików CSV nie jest obecnie wspierany!<br><br>Ponieważ nie zainstalowano modułu DB/Obiekt, eksport do plików CSV nie jest dostępny.",
		'error_nothing_selected_docs' => "Eksport nie został wykonany!<br><br>Nie wybrano dokumentów.",
		'error_nothing_selected_objs' => "Eksport nie został wykonany!<br><br>Nie wybrano dokumentów ani obiektów",
		'error_download_failed' => "Nie można pobrać pliku eksportu.",
		'comma' => ", {Przecinek}",
		'semicolon' => "; {Srednik}",
		'colon' => ": {Dwukropek}",
		'tab' => "\\t {Tab}",
		'space' => "  {Spacja}",
		'double_quote' => "\" {Czudzyslow}",
		'single_quote' => "' {Cudzyslow prosty}",
		'we_export' => 'Eksport wE',
		'wxml_export' => 'Eksport XML webEdition',
		'txt_wxml_export' => 'Eksport dokumentów, szablonów, obiektów i klas webEdition, zgodnie ze specyficzną DTD (Definicja Typu Dokumentu).',
		'options' => 'Options', // TRANSLATE
		'handle_document_options' => 'Documents', // TRANSLATE
		'handle_template_options' => 'Templates', // TRANSLATE
		'handle_def_templates' => 'Export default templates', // TRANSLATE
		'handle_document_includes' => 'Export included documents', // TRANSLATE
		'handle_document_linked' => 'Export linked documents', // TRANSLATE
		'handle_object_options' => 'Objects', // TRANSLATE
		'handle_def_classes' => 'Export default classes', // TRANSLATE
		'handle_object_includes' => 'Export included objects', // TRANSLATE
		'handle_classes_options' => 'Classes', // TRANSLATE
		'handle_class_defs' => 'Default value', // TRANSLATE
		'handle_object_embeds' => 'Export embedded objects', // TRANSLATE
		'handle_doctype_options' => 'Doctypes/<br>Categorys/<br>Navigation',
		'handle_doctypes' => 'Doctypes', // TRANSLATE
		'handle_categorys' => 'Categorys',
		'export_depth' => 'Export depth', // TRANSLATE
		'to_level' => 'to level', // TRANSLATE
		'select_export' => 'To export an entry, please mark the referring check box in the tree. Important note: All marked items from all branches will be exported and if you export a directory all documents in this directory will be exported as well!', // TRANSLATE
		'templates' => 'Templates', // TRANSLATE
		'classes' => 'Classes', // TRANSLATE

		'nothing_to_delete' => "Nie ma nic do usunięcia.",
		'nothing_to_save' => 'Nie ma nic do zapisania!',
		'no_perms' => 'Nie masz uprawnień!',
		'new' => 'Nowy',
		'export' => 'Eksport',
		'group' => 'Grupa',
		'save' => 'Zapisz',
		'delete' => 'Usuń',
		'quit' => 'Zakończ',
		'property' => 'Właściwości',
		'name' => 'Nazwa',
		'save_to' => 'Zapisz jako:',
		'selection' => 'Wybór',
		'save_ok' => 'Eksport został zapamiętany.',
		'save_group_ok' => 'Grupa została zapisana.',
		'log' => 'Szczegóły',
		'start_export' => 'Rozpocznij eksport',
		'prepare' => 'Przygotowanie eksportu...',
		'doctype' => 'Typ dokumentu',
		'category' => 'Kategoria',
		'end_export' => 'Eksport zakończony',
		'newFolder' => "Nowa grupa",
		'folder_empty' => "Grupa jest pusta",
		'folder_path_exists' => "Grupa już istnieje!",
		'wrongtext' => "Nazwa jest nieprawidłowa!",
		'wrongfilename' => "The filename is not valid!", // TRANSLATE
		'folder_exists' => "Grupa już istnieje!",
		'delete_ok' => 'Eksport został usunięty.',
		'delete_nok' => 'BŁĄD: Nie usunięto eksportu',
		'delete_group_ok' => 'Grupa została usunięta.',
		'delete_group_nok' => 'BŁĄD: Grupa nie została usunięta',
		'delete_question' => 'Czy chcesz usunąć aktualny eksport?',
		'delete_group_question' => 'Czy chcesz usunąć aktualną grupę?',
		'download_starting2' => 'Pobieranie plików eksportu rozpoczęło się.',
		'download_starting3' => 'Jeżeli pobieranie nie rozpocznie się po 10 sekundach,',
		'working' => 'Praca',
		'txt_document_options' => 'Standardowy szablon to szablon zdefiniowany we właściwościach dokumentu. Zawarte dokumenty to wewnętrzne dokumenty powiązane za pomocą znaczników we:include, we:form, we:url, we:linkToSeeMode, we:a, we:href, we:link, we:css, we:js, we:addDelNewsletterEmail z eksportowanym dokumentem. Zawarte obiekty to obiekty które zostały powiązane za pomocą we:object i we:form z eksportowanym dokumentem. Połączone dokumenty to dokumenty wewnętrzne połączone z eksportowanym dokumentem za pomocą znaczników HTML body, a, img, table, td .',
		'txt_object_options' => 'Standardowa klasa to klasa która została zdefiniowana we własnościach dokumentu.',
		'txt_exportdeep_options' => 'Głębokość eksportu to głębokość toDo której będą eksportowane zawarte dokumenty względnie obiekty. Pole musi zawierać liczbę!',
		'name_empty' => 'Nazwa nie może być pusta!',
		'name_exists' => 'Nazwa już istnieje!',
		'help' => 'Pomoc',
		'info' => 'Informacje',
		'path_nok' => 'Ścieżka jest nieprawidłowa!',
		'must_save' => 'Eksport został zmieniony.\\nZanim będziesz mógł dokonać eksportu, powinieneś zapisać dane eksportu!',
		'handle_owners_option' => 'Dane właściciela',
		'handle_owners' => 'Eksport danych właściciela',
		'txt_owners' => 'Eksportuj wraz z załączonymi danymi użytkownika.',
		'weBinary' => 'File', // TRANSLATE
		'handle_navigation' => 'Navigation', // TRANSLATE
		'weNavigation' => 'Navigation', // TRANSLATE
		'weNavigationRule' => 'Navigation rule', // TRANSLATE
		'weThumbnail' => 'Thumbnails', // TRANSLATE
		'handle_thumbnails' => 'Thumbnails', // TRANSLATE

		'navigation_hint' => 'Document types, categories and the navigation are exported depending on your select documents and templates. The export of the navigation therefore requires the export of a template with a document based on it in which the navigation is used.', // TRANSLATE
		'title' => 'Eksport Wizard',
		'selection_type' => 'Zatwierdź wybór elementów',
		'auto_selection' => 'Automatyczny wybór',
		'txt_auto_selection' => 'Eksportuj automatycznie - według typu dokumentu lub klasy - wybrane dokumenty lub obiekty.',
		'manual_selection' => 'Manualny wybór',
		'txt_manual_selection' => 'Eksportuj manualnie wybrane dokumenty lub obiekty.',
		'element' => 'Wybór elementów',
		'select_elements' => 'Wybierz elementy do importu',
		'select_docType' => 'Wybierz typ dokumentu lub szablonu.',
		'none' => '-- żadne --',
		'doctype' => 'Typ dokumentu',
		'template' => 'Szablon',
		'categories' => 'Kategorie',
		'documents' => 'Dokumenty',
		'objects' => 'Obiekty',
		'class' => 'Klasy',
		'isDynamic' => 'Generuj dynamicznie stronę',
		'extension' => 'Rozszerzenie',
		'wexml_export' => 'Eksport weXML',
		'filename' => 'Nazwa pliku',
		'extra_data' => 'Dodatkowe dane',
		'integrated_data' => 'Eksportuj powiązane pliki',
		'integrated_data_txt' => 'Wybierz tą opcję, jeżeli dane z szablonów lub dokumentów mają zostać eksportowane.',
		'max_level' => 'do końca',
		'export_doctypes' => 'Eksportuj typy dokumentów',
		'export_categories' => 'Eksportuj kategorie',
		'export_location' => 'Eksportuj do',
		'local_drive' => 'Lokalny dysk twardy',
		'server' => 'Serwer',
		'export_progress' => 'Eksportuj',
		'prepare_progress' => 'Przygotowanie',
		'finish_progress' => 'Gotowe',
		'finish_export' => 'Eksport zakończony pomyślnie!',
);