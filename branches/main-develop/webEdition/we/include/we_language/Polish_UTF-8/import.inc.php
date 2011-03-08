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
 * Language file: import.inc.php
 * Provides language strings.
 * Language: English
 */
$l_import = array(
		'title' => 'Kreator importu',
		'wxml_import' => 'Import webEdition XML',
		'gxml_import' => 'Import zwykłego XML',
		'csv_import' => 'Import CSV',
		'import' => 'Importuj',
		'none' => '-- żaden --',
		'any' => '-- brak --',
		'source_file' => 'Plik ródłowy',
		'import_dir' => 'Katalog docelowy',
		'select_source_file' => 'Wybierz plik ródłowy.',
		'we_title' => 'Tytuł',
		'we_description' => 'Tekst opisu',
		'we_keywords' => 'Słowa kluczowe',
		'uts' => 'Unix-Timestamp', // TRANSLATE
		'unix_timestamp' => 'Unix-Timestamp liczy sekundy od poczštku epoki Uniksa (01.01.1970).',
		'gts' => 'GMT Timestamp', // TRANSLATE
		'gmt_timestamp' => 'General Mean Time ew. Greenwich Mean Time (w skrócie GMT).',
		'fts' => 'Własny format',
		'format_timestamp' => 'We wskazaniu formatu dopuszcza się następujšce symbole: Y (czterocyfrowe przedstawienie roku: 2004), y (dwucyfrowe przedstawienie roku: 04), m (Miesišc z zerem na poczštku: 01 do 12), n (Miesišc bez zera na poczštku: 1 do 12), d (Dzień miesišca dwucyfrowo z zerem na poczštku: 01 do 31), j (Dzień miesišca bez zera na poczštku: 1 do 31), H (godzina w formacie 24-godzinnym: 00 do 23), G (godzina w formacie 24-godzinnym bez zera na poczštku: 0 do 23), i (minuty: 00 do 59), s (sekundy z zerem na poczštku: 00 do 59)',
		'import_progress' => 'Importuj',
		'prepare_progress' => 'Przygotowanie',
		'finish_progress' => 'Gotowe',
		'finish_import' => 'Import zakończony!',
		'import_file' => 'Import pliku',
		'import_data' => 'Import danych',
		'import_templates' => 'Template import', // TRANSLATE
		'template_import' => 'First Steps Wizard', // TRANSLATE
		'txt_template_import' => 'Import ready example templates and template sets from the webEdition server', // TRANSLATE
		'file_import' => 'Import lokalnych plików',
		'txt_file_import' => 'Importuj jeden lub więcej plików z lokalnego dysku twardego.',
		'site_import' => 'Importuj pliki z serwera',
		'site_import_isp' => 'Importuj obrazki z serwera',
		'txt_site_import_isp' => 'Importuj obrazki z katalogu serwera. Wybierz, które obrazki majš być importowane.',
		'txt_site_import' => 'Importuj pliki z katalogu na serwerze. Wybierz za pomocš ustawienia opcji fitra, czy majš być importowane obrazki, strony HTML, pliki Flash, Javascript, CSS, dokumenty tekstowe lub inne pliki.',
		'txt_wxml_import' => 'Pliki XML webEdition zawierajš informacje o dokumentach, szablonach i obiektach webEdition. Ustaw do którego katalogu importować te dokumenty i obiekty.',
		'txt_gxml_import' => 'Import "flat" XML files, such as those provided by phpMyAdmin. The dataset fields have to be allocated to the webEdition dataset fields. Use this to import XML files exported from webEdition without the export module.', // TRANSLATE
		'txt_csv_import' => 'Import plików CSV (Comma Separated Values) lub opartych na nich formatach tekstowych (np. *.txt). Pola danych przyporzšdkowuje się polom webEdition.',
		'add_expat_support' => 'Interfejs importu XML wymaga rozszerzenia XML expat autorstwa Jamesa Clark. Skompiluj ponownie PHP z rozszerzeniem expat, żeby program mógł wspierać funkcję  importu XML.',
		'xml_file' => 'Plik XML',
		'templates' => 'Szablony',
		'classes' => 'Klasy',
		'predetermined_paths' => 'Domylna cieżka',
		'maintain_paths' => 'Zachowaj cieżki',
		'import_options' => 'Opcje importu',
		'file_collision' => 'Przy istniejšcym pliku',
		'collision_txt' => 'Przy imporcie plików do katalogu, który zawiera plik o identycznej nazwie, dochodzi do konfliktów. Podaj, w jaki sposób Kreator importu powinien traktować takie pliki.',
		'replace' => 'Zastšp',
		'replace_txt' => 'Usunšć istniejšce dane przed zapisaniem Twojego nowego pliku.',
		'rename' => 'Zmień nazwę',
		'rename_txt' => 'Do nazwy plików zostanie dodane jednoznaczne rozszerzenie ID. Wszystkie odnoniki, które wskazujš na ten plik zostanš odpowiednio dopasowane.',
		'skip' => 'Pomiń',
		'skip_txt' => 'Przy pomijaniu danego pliku zostanie zachowany plik istniejšcy.',
		'extra_data' => 'Dodatkowe dane',
		'integrated_data' => 'Importuj zintegrowane dane',
		'integrated_data_txt' => 'Jeżeli wybierzesz tš opcję, to dane zawarte w szablonach lub dokumentach będš importowane.',
		'max_level' => 'do poziomu',
		'import_doctypes' => 'Importuj typy dokumentów',
		'import_categories' => 'Importuj kategorie',
		'invalid_wxml' => 'Można importować tylko te dokumenty XML, któe odpowiadajš Definicji Typu Dokumentu (DTD) webEdition.',
		'valid_wxml' => 'Dokument XML jest dobrze sformatowany i prawidłowy tzn. opdowiada Definicji Typu Dokumentu (DTD) webEdition.',
		'specify_docs' => 'Wybierz dokumenty, które chcesz importować.',
		'specify_objs' => 'Wybierz obiekty, które chcesz importować.',
		'specify_docs_objs' => 'Wybierz, czy chcesz importować dokumenty i/lub obiekty.',
		'no_object_rights' => 'Nie masz uprawnień do importu obiektów.',
		'display_validation' => 'Wywietl walidację XML',
		'xml_validation' => 'Walidacja XML',
		'warning' => 'Ostrzeżenie',
		'attribute' => 'Atrybut',
		'invalid_nodes' => 'Nieprawidłowy węzeł XML w pozycji ',
		'no_attrib_node' => 'Brakujšcy element XML "attrib" w pozycji ',
		'invalid_attributes' => 'Nieprawidłowy atrybut w pozycji ',
		'attrs_incomplete' => 'Lista atrybutów zdefiniowanych jako #required i #fixed jest niekompletna w pozycji ',
		'wrong_attribute' => 'Nazwy atrybutu nie zdefiniowano ani jako #required, ani jako #implied w pozycji ',
		'documents' => 'Dokumenty',
		'objects' => 'Obiekty',
		'fileselect_server' => 'Pobierz plik ródłowy z serwera',
		'fileselect_local' => 'Załaduj plik ródłowy z lokalnego dysku twardego',
		'filesize_local' => 'Ze względu na ograniczenia PHP plik do załadowania nie może być większy niż %s !',
		'xml_mime_type' => 'Nie można zaimportować wybranego pliku. Mime-Typ:',
		'invalid_path' => 'Nieprawidłowa cieżka do pliku ródłowego.',
		'ext_xml' => 'Wybierz plik ródłowy z rozszerzeniem ".xml".',
		'store_docs' => 'Katalog docelowy dokumentów',
		'store_tpls' => 'Katalog docelowy szablonów stron',
		'store_objs' => 'Katalog docelowy obiektów',
		'doctype' => 'Document type',
		'gxml' => 'Zwykły XML',
		'data_import' => 'Importuj dane',
		'documents' => 'Dokumenty',
		'objects' => 'Obiekty',
		'type' => 'Typ',
		'template' => 'Szablony',
		'class' => 'Klasa',
		'categories' => 'Kategorie',
		'isDynamic' => 'Generuj dynamicznie stronę',
		'extension' => 'Rozszerzenie',
		'filetype' => 'Typ pliku',
		'directory' => 'Katalog',
		'select_data_set' => 'Wybierz zestaw danych',
		'select_docType' => 'Wybierz szablon.',
		'file_exists' => 'Wybrany plik ródłowy nie istnieje. Sprawd podanš cieżkę. cieżka: ',
		'file_readable' => 'Wybrany plik rółowy nie ma ustawionych uprawnień do odczytu i nie można go zaimportować.',
		'asgn_rcd_flds' => 'Przyporzšdkuj pola danych',
		'we_flds' => 'Pola webEdition&nbsp;',
		'rcd_flds' => 'Pola rekordów&nbsp;',
		'name' => 'Nazwa',
		'auto' => 'automatycznie',
		'asgnd' => 'przyporzšdkowane',
		'pfx' => 'Prefiks',
		'pfx_doc' => 'Dokument',
		'pfx_obj' => 'Obiekt',
		'rcd_fld' => 'Pole rekordu',
		'import_settings' => 'Ustawienia importu',
		'xml_valid_1' => 'Plik XML jest prawidłowy i zawiera',
		'xml_valid_s2' => 'Elementów. Wybierz elementy, które chcesz importować.',
		'xml_valid_m2' => 'Węzeł potomny XML pierwszego poziomu ma różne nazwy. Wybierz węzły XML oraz liczbę elementów, które chcesz importować.',
		'well_formed' => 'Dokument XML jest dobrze sformatowany.',
		'not_well_formed' => 'Dokument XML nie jest dobrze sformatowany i nie można go zaimportować.',
		'missing_child_node' => 'Dokument XML jest dobrze sformatowany, nie zawiera jednak węzłów XML i dlatego nie można go zaimportować.',
		'select_elements' => 'Wybierz elementy, które chcesz importować.',
		'num_elements' => 'Wybierz liczbę elementów pomiędzy 1 a ',
		'xml_invalid' => '', // TRANSLATE
		'option_select' => 'Wybór..',
		'num_data_sets' => 'Rekordy:',
		'to' => 'do',
		'assign_record_fields' => 'Przyporzšdkuj pola danych',
		'we_fields' => 'Pola webEdition',
		'record_fields' => 'Pola rekordów',
		'record_field' => 'Pole rekordu ',
		'attributes' => 'Atrybut',
		'settings' => 'Ustawienia',
		'field_options' => 'Opcje pola',
		'csv_file' => 'Plik CSV',
		'csv_settings' => 'Ustawienia CSV',
		'xml_settings' => 'Ustawienia XML',
		'file_format' => 'Format pliku',
		'field_delimiter' => 'separator',
		'comma' => ', {Przecinek}',
		'semicolon' => '; {Srednik}',
		'colon' => ': {Dwukropek}',
		'tab' => "\\t {Tab}",
		'space' => '  {Spacja}',
		'text_delimiter' => 'Ogranicznik tekstu',
		'double_quote' => '" {Cudzyslow}',
		'single_quote' => '\' {Cudzyslow prosty}',
		'contains' => 'Pierwszy wiersz zawiera nazwy pól',
		'split_xml' => 'Importuj rekordy wg kolejnoci',
		'wellformed_xml' => 'Sprawdzenie formatowania XML',
		'validate_xml' => 'Walidacja XML',
		'select_csv_file' => 'Wybierz plik ródłowy CSV.',
		'select_seperator' => 'Wybierz separator.',
		'format_date' => 'Format daty',
		'info_sdate' => 'Wybierz format daty dla pola webEdition',
		'info_mdate' => 'Wybierz format daty dla pól webEdition',
		'remark_csv' => 'Można importować pliki CSV (Comma Separated Values) lub oparte na nich formaty tekstowe (np. *.txt). Przy imporcie tych formatów danych można ustawić znak separatora(np. , ; Tab, spacja) oraz ogranicznik tekstu (= znak, który zamyka wpis tekstowy).',
		'remark_xml' => 'Wybierz opcję "Importuj rekordy pojedynczo", żeby można było importować duże pliki w cišgu zdefiniowanego jako limit (Timeout) czasu wykonywania skryptu PHP.<br>Jeżeli nie jese pewien, czy wybrany plik jest plikiem XML webEdition, to możesz przed importem sprawdzić plik pod kštem jego dobrego sformatowania i poprawnoci typu.',
		'import_docs' => "Importuj dokumenty",
		'import_templ' => "Importuj szablony",
		'import_objs' => "Importuj obiekty",
		'import_classes' => "Importuj klasy",
		'import_doctypes' => "Importuj typy dokumentu",
		'import_cats' => "Importuj kategorie",
		'documents_desc' => "Podaj katalog, do którego majš zostać zaimportowane dokumenty. W przypadku gdy wybrano opcję \"Zachowaj cieżki\", to odpowiednie cieżki zostanš ustawione automatycznie, w innym za przypadku, będš one zignorowane.",
		'templates_desc' => "Podaj katalog, do którego majš zostać zaimportowane szablony. W przypadku gdy wybrano opcję \"Zachowaj cieżki\", to odpowiednie cieżki zostanš ustawione automatycznie, w innym za przypadku, będš one zignorowane.",
		'handle_document_options' => 'Dokumenty',
		'handle_template_options' => 'Szablony',
		'handle_object_options' => 'Obiekty',
		'handle_class_options' => 'Klasa',
		'handle_doctype_options' => "Typy dokumentów",
		'handle_category_options' => "Kategorie",
		'log' => 'Details', // TRANSLATE
		'start_import' => 'Rozpoczęcie importu',
		'prepare' => 'Przygotowanie...',
		'update_links' => 'Aktualizacja odnoników...',
		'doctype' => 'Typ dokumentu',
		'category' => 'Kategoria',
		'end_import' => 'Import zakończony',
		'handle_owners_option' => 'Dane użytkownika',
		'txt_owners' => 'Importuj wraz z danymi użytkownika.',
		'handle_owners' => 'Przywróć dane użytkownika',
		'notexist_overwrite' => 'Jeżeli użytkownik nie istnieje, wtedy stosuje się opcję "Nadpisz dane użytkownika" .',
		'owner_overwrite' => 'Nadpisz dane użytkownika',
		'name_collision' => 'Przy identycznych nazwach',
		'item' => 'Przedmiot',
		'backup_file_found' => 'Plik kopii zapasowej. Użyj opcji \"Kopia zapasowa->Przywróć kopię zapasowš\" z menu plik w celu importu danych.',
		'backup_file_found_question' => 'Czy chcesz zamknšć aktualne okno i uruchomić Kreatora importu dla kopii zapasowych?',
		'close' => 'Zamkij',
		'handle_file_options' => 'Pliki',
		'import_files' => 'Importuj pliki',
		'weBinary' => 'Plik',
		'format_unknown' => 'Nieznany format pliku!',
		'customer_import_file_found' => 'Plik importu z modułu Zarzšdzanie klientami. Użyj opcji \"Import/Eksport\" za modułu Zarzšdzanie klientami (PRO) w celu importowania danych.',
		'upload_failed' => 'Nie można załadować danych! Sprawd, czy wielkoć danych nie przekracza %s ',
		'import_navigation' => 'Import navigation', // TRANSLATE
		'weNavigation' => 'Navigation', // TRANSLATE
		'navigation_desc' => 'Select the directory where the navigation will be imported.', // TRANSLATE
		'weNavigationRule' => 'Navigation rule', // TRANSLATE
		'weThumbnail' => 'Thumbnail', // TRANSLATE
		'import_thumbnails' => 'Import thumbnails', // TRANSLATE
		'rebuild' => 'Rebuild', // TRANSLATE
		'rebuild_txt' => 'Automatic rebuild', // TRANSLATE
		'finished_success' => 'The import of the data was successful.', // TRANSLATE

		'encoding_headline' => 'Charset', // TRANSLATE
		'encoding_noway' => 'A conversion  is only possible between ISO-8859-1 and UTF-8 <br/>and with a set default charset (settings dialog)', // TRANSLATE
		'encoding_change' => "Change, from '", // TRANSLATE
		'encoding_XML' => '', // TRANSLATE
		'encoding_to' => "' (XML file) to '", // TRANSLATE
		'encoding_default' => "' (standard)", // TRANSLATE
);