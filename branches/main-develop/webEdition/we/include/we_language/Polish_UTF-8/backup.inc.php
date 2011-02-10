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
$l_backup=array(
'save_not_checked' => "Nie wybrałeś jeszcze, gdzie mają zostać zapisane pliki kopii zapasowej!",
'wizard_title' => "Kreator przywracania kopii zapasowej",
'wizard_title_export' => "Kreator kopii zapasowej",
'save_before' => "W trakcie przywracania pliku kopii zapasowej istniejące dane zostaną skasowane! Zaleca się zapisać wcześniej istniejące dane.",
'save_question' => "Czy chcesz to uczynić teraz?",
'step1' => "Krok 1/4 - Zapisz istniejące dane",
'step2' => "Krok 2/4 - Wybierz źródło danych",
'step3' => "Krok 3/4 - Przywróć zabezpieczone dane",
'step4' => "Krok 4/4 - Przywracanie zakończone",
'extern' => "Przywracanie zewnętrznych danych/katalogów webEdition",
'settings' => "Przywracanie ustawień",
'rebuild' => "Automatyczny Rebuild",
'select_upload_file' => "Załaduj z lokalnego pliku",
'select_server_file' => "Wybierz plik kopii zapasowej.",
'charset_warning' => "If you encounter problems when restoring a backup, please ensure that the <strong>target system uses the same character set as the source system</strong>. This applies both to the character set of the database (collation) as well as for the character set of the user interface language!", // TRANSLATE
'defaultcharset_warning' => '<span style="color:ff0000">Attention! The standard charset is not defined.</span> For some server configurations, this can lead to problems while importing backups.!',// TRANSLATE
'finished_success' => "Import zapasowej kopii danych został ukończony.",
'finished_fail' => "Import zapasowej kopii danych nie został ukończony.",
'question_taketime' => "Eksport potrwa trochę czasu.",
'question_wait' => "Prosimy o cierpliwość!",
'export_title' => "Sporządzanie kopii zapasowej",
'finished' => "Koniec",
'extern_files_size' => "Ten proces może potrwać kilka minut. Zostanie wprowadzonych wiele plików, ponieważ maksymalna wielkość pliku jest ograniczona do %.1f MB (%s bajtów).",
'extern_files_question' => "Zabezpiecz zewnętrzne pliki/katalogiwebEdition",
'export_location' => "Wybierz miejsce, gdzie ma zostać zapisana kopia zapasowa. Jeżeli plik ma zostać zapisany na serwerze, znajdziesz go pod adresem '/webEdition/we_backup/data/'.",
'export_location_server' => "Na serwerze",
'export_location_send' => "Na lokalnym dysku twardym",
'can_not_open_file' => "Nie można otworzyć pliku '%s' .",
'too_big_file' => "Nie można zapisać pliku '%s', ponieważ przekroczył on maksymalną wielkość.",
'cannot_save_tmpfile' => " Nie można zapisać pliku tymczasowego! Sprawdź, czy masz wystarczające uprawnienia do zapisu w %s.",
'cannot_save_backup' => "Nie można zapisać pliku kopii zapasowej.",
'cannot_send_backup' => " Nie można wykonać kopii zapasowej",
'finish' => "The backup was successfully created.", // TRANSLATE
'finish_error' => " Błąd: Nie można było wykonać kopii zapasowej",
'finish_warning' => "Ostrzeżenie: Wykonano kopię zapasową, możliwe, że nie wprowadzono do kopii wszystkich plików",
'export_step1' => "Krok  1/2 - Parametry kopii zapasowej",
'export_step2' => "Krok 2/2 - Zakończono sporządzanie kopii zapasowej",
'unspecified_error' => "Wystąpił nieznany błąd",
'export_users_data' => "Zabezpiecz dane użytkowników",
'import_users_data' => "Przywróć dane użytkowników",
'import_from_server' => "Pobierz dane z serwera",
'import_from_local' => "pobierz dane z lokalnego dysku",
'backup_form' => "Kopia zapasowa z dn.",
'nothing_selected' => "Nic nie wybrano!",
'query_is_too_big' => "Plik kopii zapasowej zawiera plik, którego nie można było odtworzyć, ponieważ jest ma rozmiar większy niż %s bajtów!",
'show_all' => "Pokaż wszytkie pliki",
'import_customer_data' => "Przywróć dane klientów",
'import_shop_data' => "Przywróć dane sklepu",
'export_customer_data' => "Zabezpiecz dane klientów",
'export_shop_data' => "Zabezpiecz dane sklepu",
'working' => "Pracuję...",
'preparing_file' => "Przygotować dane do przywrócenia...",
'external_backup' => "Zabezpiecz dane zewnętrzne...",
'import_content' => "Zabezpiecz zawartość",
'import_files' => "Przywróć pliki",
'import_doctypes' => "Przywróć pliki",
'import_user_data' => "Przywróć dane użytkowników",
'import_templates' => "Przywróć szablony",
'export_content' => "Zabezpiecz zawartość",
'export_files' => "Zabezpiecz dane",
'export_doctypes' => "Zabezpiecz pliki",
'export_user_data' => "zabezpiecz dane użytkowników",
'export_templates' => "Zabezpiecz szablony",
'download_starting' => "Rozpoczęto pobieranie pliku kopii bezpieczeństwa.<br><br>Jeżeli download nie rozpocznie się w ciągu 10 sekund,<br>",
'download' => "kliknij tutaj.",
'download_failed' => "Wymagane dane albo nie istnieją albo nie masz uprawnień do ich pobrania.",
'extern_backup_question_exp' => "Wybrano 'Zabezpiecz zewnętrzne pliki/katalogi webEdition'. Ten wybór może być bardzo czasochłonny i prowadzić do błędów systemowych. Kontynuować mimo to?",
'extern_backup_question_exp_all' => "Wybrano 'Wybierz wszystko'. Tym samym wybrano automatycznie także 'Zabezpiecz zewnętrzne pliki/katalogi webEdition'. Ten proces może być bardzo czasochłonny i prowadzić do błędów systemowych.\\nZezwolić na wybór 'Zabezpiecz zewnętrzne pliki/katalogi webEdition'?",
'extern_backup_question_imp' => "Wybrano 'Przywróć zewnętrzne pliki/katalogi webEdition'. Ten wybór może być bardzo czasochłonny i prowadzić do błędów systemowych. Kontynuować mimo to?",
'extern_backup_question_imp_all' => "Wybrano 'Wybierz wszystko'. Tym samym wybrano automatycznie także 'Przywróć zewnętrzne pliki/katalogi webEdition'. Ten proces może być bardzo czasochłonny i prowadzić do błędów systemowych.\\nZezwolić na wybór 'Przywróć zewnętrzne pliki/katalogi webEdition'?",
'nothing_selected_fromlist' => "Wybierz plik kopii zapasowej z listy!",
'export_workflow_data' => "Zabezpiecz dane Workflow",
'export_todo_data' => "Zabezpiecz dane Zadania/Powiadamianie",
'import_workflow_data' => "Przywróć dane Workflow",
'import_todo_data' => "Przywróć dane Zadania/Powiadamianie",
'import_check_all' => "Wybierz wszystko",
'export_check_all' => "Wybierz wszystko",
'import_shop_dep' => "Wybrano 'Przywróć dane sklepu'. Sklep potrzebuje danych klientów do prawidłowego funkcjonowania i dlatego automatycznie zaznaczono'Zabezpiecz dane klientów'.",
'export_shop_dep' => "Wybrano 'Zabezpiecz dane sklepu'. Moduł Sklep potrzebuje danych klientów do prawidłowego funkcjonowania i dlatego automatycznie zaznaczono'Zabezpiecz dane klientów'.",
'import_workflow_dep' => "Wybrano 'Przywróć Workflow'. Workflow potrzebuje dokumentów i danych użytkowników do prawidłowego funkcjonowania i z tego powodu zaznaczono automatycznie 'Przywróć dokumenty i szablony' oraz 'Przywróć dane użytkowników'.",
'export_workflow_dep' => "Wybrano 'Zabezpiecz Workflow'. Workflow potrzebuje dokumentów i danych użytkowników do prawidłowego funkcjonowania i dlatego zaznaczono automatycznie 'Zabezpiecz dokumenty i szablony' oraz 'Zabezpiecz dane użytkowników'.",
'import_todo_dep' => "Wybrano 'Przywróć Zadania/Powiadamianie '. Moduł Zadania/Powiadamianie potrzebuje danych użytkowników do prawidłowego funkcjonowania i dlatego automatycznie zaznaczono 'Przywróć dane użytkownika'.",
'export_todo_dep' => "Wybrano 'Zabezpiecz Zadania/Powiadamianie '. Moduł Zadania/Powiadamianie potrzebuje danych użytkowników do prawidłowego funkcjonowania i dlatego automatycznie zaznaczono 'Zabezpiecz dane użytkownika'.",
'export_newsletter_data' => "Zabezpiecz dane Newslettera",
'import_newsletter_data' => "Przywróć dane Newslettera",
'export_newsletter_dep' => "Wybrano 'Zabezpiecz dane Newslettera'. Newsletter potrzebuje dokumentów, obiektów, klas oraz danych klientów do prawidłowego funkcjonowania i dlatego automatycznie zaznaczono 'Zabezpiecz dokumenty i szablony', 'Zabezpiecz obiekty i klasy' oraz 'Zabezpiecz dane klientów'.",
'import_newsletter_dep' => "Wybrano 'Przywróć dane Newslettera'. Newsletter potrzebuje dokumentów, obiektów, klas oraz danych klientów do prawidłowego funkcjonowania i dlatego automatycznie zaznaczono 'Przywróć dokumenty i szablony', 'Przywróć obiekty i klasy' oraz 'Przywróć dane klientów'.",
'warning' => "Ostrzeżenie",
'error' => "Błąd",
'export_temporary_data' => "Zabezpiecz dane tymczasowe",
'import_temporary_data' => "Przywróć dane tymczasowe",
'export_banner_data' => "Zabezpiecz dane Bannerów",
'import_banner_data' => "Przywróć dane Bannerów",
'export_prefs' => "Zabezpiecz ustawienia",
'import_prefs' => "Przywróć ustawienia",
'export_links' => "Zabezpiecz odnośniki",
'import_links' => "Przywróć odnośniki",
'export_indexes' => "Zabezpiecz indeksy",
'import_indexes' => "Przywróć indeksy",
'filename' => "Nazwa plików",
'compress' => "Kompresuj",
'decompress' => "Dekompresuj",
'option' => "Opcje kopii bezpieczeństwa",
'filename_compression' => "Nadaj nazwę docelowemu plikowi kopii bezpieczeństwa. Możesz także włączyć kompresję pliku. Plik kopii bezpieczeństwa zostanie skompresowany programem gzip i otrzyma rozszerzenie .gz. Operacja ta może potrwać kilka minut!<br>Jeżeli nie powiodło się utworzenie kopii bezpieczeństwa, zmień ustawienia.",
'export_core_data' => "Zabezpiecz dokumenty i szablony",
'import_core_data' => "Przywróć dokumenty i szablony",
'export_object_data' => "Zabezpiecz obiekty i klasy",
'import_object_data' => "Przywróć obiekty i klasy",
'export_binary_data' => "Zabezpiecz dane binarne (obrazki, PDFy, ...) ",
'import_binary_data' => "Przywróć dane binarne (obrazki, PDFy, ...) ",
'export_schedule_data' => "Zabezpiecz dane Harmonogramu",
'import_schedule_data' => "Przywróć dane Harmonogramu",
'export_settings_data' => "Zabezpiecz ustawienia",
'import_settings_data' => "Przywróć ustawienia",
'export_extern_data' => "Zabezpiecz zewnętrzne dane/katalogi webEdition",
'import_extern_data' => "Przywróć zewnętrzne dane/katalogi webEdition",
'export_binary_dep' => "Wybrano 'Zabezpiecz dane binarne'. Do prawidłowego funkcjonowania dane binarne potrzebują także dokumentów. Dlatego automatycznie zaznaczono 'Zabezpiecz dokumenty i szablony'.",
'import_binary_dep' => "Wybrano 'Przywróć dane binarne'. Do prawidłowego funkcjonowania dane binarne potrzebują także dokumentów. Dlatego automatycznie zaznaczono 'Przywróć dokumenty i szablony'.",
'export_schedule_dep' => "You have selected the option 'Save schedule data'. The Schedule Module needs the documents and objects and because of that, 'Save documents and templates' and 'Save objects and classes' has been automatically selected.", // TRANSLATE
'import_schedule_dep' => "You have selected the option 'Restore schedule data'. The Schedule Module needs the documents data and objects and because of that, 'Restore documents and templates' and 'Restore objects and classes' has been automatically selected.", // TRANSLATE
'export_temporary_dep' => "Wybrano 'Zabezpiecz pliki tymczasowe'. Do prawidłowego funkcjonowania pliki tymczasowe potrzebują także dokumentów. Dlatego automatycznie zaznaczono 'Zabezpiecz dokumenty i szablony'.",
'import_temporary_dep' => "Wybrano 'Przywróć pliki tymczasowe'. Do prawidłowego funkcjonowania pliki tymczasowe potrzebują także dokumentów. Dlatego automatycznie zaznaczono 'Przywróć dokumenty i szablony'.",
'compress_file' => "Kommpresuj dane",
'export_options' => "Wybierz dane do zabezpieczenia.",
'import_options' => "Wybierz dane do odtworzenia.",
'extern_exp' => "Uwaga! Ta opcja jest bardzo czasochłonna i może prowadzić do błędów systemowych",
'unselect_dep2' => "Wybrano '%s'. Następujące opcje zostaną wybrane automatycznie:",
'unselect_dep3' => "Mimo to można wybrać niezaznaczone opcje.",
'gzip' => "gzip", // TRANSLATE
'zip' => "zip", // TRANSLATE
'bzip' => "bzip", // TRANSLATE
'none' => "brak",
'cannot_split_file' => "Nie można przygotować pliku '%s' do odtworzenia!",
'cannot_split_file_ziped' => "Plik został skompresowany metodą, która nie jest obsługiwana.",
'export_banner_dep' => "You have selected the option 'Save banner data'. The banner data need the documents and because of that, 'Save documents and templates' has been automatically selected.", // TRANSLATE
'import_banner_dep' => "You have selected the option 'Restore banner data'. The banner data need the documents data and because of that, 'Restore documents and templates' has been automatically selected.", // TRANSLATE

'delold_notice' => "Zaleca się uprzednie usunięcie istniejących danych.<br>Czy czcesz to uczynić teraz?",
'delold_confirm' => "Na pewno chcesz usunąć wszystkie pliki z serwera?",
'delete_entry' => "Usuń %s",
'delete_nok' => "Nie można usunąć plików!",
'nothing_to_delete' => "Nie ma nic do usunięcia!",

'files_not_deleted' => "Nie udało się całkowicie usunąć jednego lub więcej plików przeznaczonych do usunięcia z serwera! Możliwe, że są one zabezpieczone przed zapisem. Usuń te pliki ręcznie. Dotyczy to następujących plików:",

'delete_old_files' =>"Usuń stare pliki...",

'export_configuration_data' =>"Zabezpiecz konfigurację",
'import_configuration_data' =>"Przywróć konfigurację",

'import_export_data' => "Przywróć dane do eksportu",
'export_export_data' => "Zabezpiecz dane do eksportu",

'export_versions_data' => "Save version data", // TRANSLATE
'export_versions_binarys_data' => "Save Version-Binary-Files", // TRANSLATE
'import_versions_data' => "Restore version data", // TRANSLATE
'import_versions_binarys_data' => "Restore Version-Binary-Files", // TRANSLATE

'export_versions_dep' => "You have selected the option 'Save version data'. The version data need the documents, objects and version-binary-files and because of that, 'Save documents and templates', 'Save object and classes' and 'Save Version-Binary-Files' has been automatically selected.", // TRANSLATE
'import_versions_dep' => "You have selected the option 'Restore version data'. The version data need the documents data, object data an version-binary-files and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore Version-Binary-Files' has been automatically selected.", // TRANSLATE

'export_versions_binarys_dep' => "You have selected the option 'Save Version-Binary-Files'. The Version-Binary-Files need the documents, objects and version data and because of that, 'Save documents and templates', 'Save object and classes' and 'Save version data' has been automatically selected.", // TRANSLATE
'import_versions_binarys_dep' => "You have selected the option 'Restore Version-Binary-Files'. The Version-Binary-Files need the documents data, object data an version data and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore version data' has been automatically selected.", // TRANSLATE

'del_backup_confirm' => "Czy chcesz usunąć wybrany plik kopi bezpieczeństwa?",
'name_notok' => "Nazwa pliku jest nieprawidłowa!",
'backup_deleted' => "Plik kopii zapasowej %s został usunięty",
'error_delete' => "Plik kopii zapasowej nie mógł być usunięty. Proszę usunąć dane za pomocą programu do FTP z katalogu /webEdition/we_backup",

'core_info' => 'Wszystkie szablony i dokumenty.',
'object_info' => 'Obiekty i klasy z modułu DB/Obiekt.',
'binary_info' => 'Dane binarne obrazków, PDFów i innych dokumentów.',
'user_info' => 'Użytkownicy i dane dotyczące dostępu z zarządzania użytkownikami.',
'customer_info' => 'Klienci i dane dotyczące dostępu z zarządzania klientami.',
'shop_info' => 'Zamówienia z modułu Shop.',
'workflow_info' => 'Dane modułu Workflow.',
'todo_info' => 'Wiadomości i zadania z modułu Zadania/Powiadamianie.',

'newsletter_info' => 'Dane z modułu Newsletter',
'banner_info' => 'Bannery i statystyki z modułu Bannery/Statystyki.',
'schedule_info' => 'Działania sterowane czasem z modułu Harmonogram.',

'settings_info' => 'Ustawienia programu webEdition.',
'temporary_info' => 'Nie opublikowane jeszcze dokumenty i obiekty ewentualnie jeszcze nie opublikowane zmiany',
'export_info' => 'Dane z modułu eksportu',
'glossary_info' => 'Data from the glossary.', // TRANSLATE
'versions_info' => 'Data from Versioning.', // TRANSLATE
'versions_binarys_info' => 'This option could take some time and memory because the folder /webEdition/we/versions/ could be very large. It is recommended to save this folder manually.', // TRANSLATE


'import_voting_data' => "Przywróć dane Voting",
'export_voting_data' => "Zabezpiecz dane Voting",
'voting_info' => 'Dane z modułu Voting.',

'we_backups' => 'Kopie bezpieczeństwa webEdition',
'other_files' => 'Pozostałe pliki',

'filename_info' => 'Nadaj nazwę docelowemu plikowi kopii bezpieczeństwa',
'backup_log_exp' => 'Utworzono log w /webEdition/we_backup/data/lastlog.php',
'export_backup_log' => 'Utwórz log',

'download_file' => 'Pobierz plik',

'import_file_found' => 'To jest plik importu. Skorzystaj z opcji \"Import/Eksport\" z menu Plik, w celu importowania danych.',
'customer_import_file_found' => 'To jest plik importu z zarządzania klientami. Skorzystaj z opcji  \"Import/Eksport\" z zarządzania klientami (PRO) w celu importowania pliku',
'import_file_found_question' => 'Czy chcesz zamknąć zaraz aktualne okno i uruchomić kreatora importu dla importu XML webEditon?',
'format_unknown' => 'Nieznany format danych!',
'upload_failed' => 'Nie można załadować pliku! Sprawdź czy wielkość pliku nie przekroczyła %s',
'file_missing' => 'Brak pliku kopii zapasowej!',
'recover_option' => 'Opcje przywracania',

'no_resource' => 'Fatal Error: There are not enough resources to finish the backup!', // TRANSLATE
'error_compressing_backup' => 'An error occured while compressing the backup, so the backup could not be finished!', // TRANSLATE
'error_timeout' => 'An timeout occured while creating the backup, so the backup could not be finished!', // TRANSLATE

'export_spellchecker_data' => "Save spellchecker data", // TRANSLATE
'import_spellchecker_data' => "Restore spellchecker data", // TRANSLATE
'spellchecker_info' => 'Data for spellchecker: settings, general and personal dictionaries.', // TRANSLATE

'import_banner_data' => "Przywróć dane Bannerów",
'export_banner_data' => "Zabezpiecz dane Bannerów",

'export_glossary_data' => "Save glossary data", // TRANSLATE
'import_glossary_data' => "Restore glossary data", // TRANSLATE

'protect' => "Protect backup file", // TRANSLATE
'protect_txt' => "The backup file will be protected from unprivileged download with additional php code. This protection requieres additional disk space for import!",

'recover_backup_unsaved_changes' => "Some open files have unsaved changes. Please check these before you continue.", // TRANSLATE
'file_not_readable' => "The backup file is not readable. Please check the file permissions.", // TRANSLATE

'tools_import_desc' => "Here you can restore webEdition tools data. Please select the desired tools from the list.", // TRANSLATE
'tools_export_desc' => "Here you can save webEdition tools data. Please select the desired tools from the list.", // TRANSLATE

'ftp_hint' => "Attention! Use the Binary mode for the download by FTP if the backup file is zip compressed! A download in ASCII 	mode destroys the file, so that it cannot be recovered!", // TRANSLATE

'convert_charset' => "Attention! Using this option in an existing site can lead to total loss of all data, please follow the instruction in http://documentation.webedition.org/de/webedition/administration/charset-conversion-of-legacy-sites", // TRANSLATE

'convert_charset_data' => "While importing the backup, convert the site from ISO to UTF-8", // TRANSLATE

'view_log' => "Backup-Log",// TRANSLATE
'view_log_not_found' => "The backup log file was not found! ",// TRANSLATE
'view_log_no_perm' => "You do not have the needed permissions to view the backup log file! ",// TRANSLATE

);