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
 * Language file: newsletter.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_newsletter = array(
		'save_changed_newsletter' => "Newsletter has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'Enter_Path' => "Please enter a path beginning with the DOCUMENT_ROOT!", // TRANSLATE
		'title_or_salutation' => "Angielski format zwrotu (bez tytułu)",
		'global_mailing_list' => "Standardowa lista mailingowa (Plik CSV)",
		'new_newsletter' => "Nowy Newsletter",
		'newsletter' => "Newsletter", // TRANSLATE
		'new' => "Nowy",
		'save' => "Zapisz",
		'delete' => "Usuń",
		'quit' => "Zamknij",
		'help' => "Pomoc",
		'info' => "Informacje",
		'options' => "Opcje",
		'send_test' => "Wyślij wiadomość testową",
		'domain_check' => "Sprawdzenie domeny",
		'send' => "Wyślij",
		'preview' => "Podgląd",
		'settings' => "Ustawienia",
		'show_log' => "Show logbook", // TRANSLATE
		'mailing_list' => "Lista mailingowa %s",
		'customers' => "Klienci",
		'emails' => "E-maile", // CHECK
// changed from: "E-mails"
// changed to  : "emails"

		'newsletter_content' => "Treść newslettera",
		'type_doc' => "Dokumenty",
		'type_object' => "Obiekty",
		'type_file' => "Plik",
		'type_text' => "Tekst",
		'attchments' => "Załącznik",
		'name' => "Nazwa",
		'no_perms' => "Brak uprawnień",
		'nothing_to_delete' => "Brak obiektu do kasowania.",
		'documents' => "Dokumenty",
		'save_ok' => "Zapamiętano Newsletter.",
		'message_description' => "Ustal zawartość newslettera",
		'sender' => "Nadawca",
		'reply' => "Adres zwrotny",
		'reply_same' => "Taki jak adres nadawcy",
		'block_type' => "Typ bloku",
		'block_document' => "Dokument",
		'block_document_field' => "Pole dokumentu",
		'block_object' => "Obiekt",
		'block_object_field' => "Pole obiektu",
		'block_file' => "Plik",
		'block_html' => "HTML", // TRANSLATE
		'block_plain' => "Tylko Tekst",
		'block_newsletter' => "Newsletter", // TRANSLATE
		'block_attachment' => "Załącznik",
		'block_lists' => "Listy mailingowe",
		'block_all' => "----   Wszystkie ----",
		'block_template' => "Szablony",
		'block_url' => "URL", // TRANSLATE
		'use_default' => "Użyj standardowego szablonu",
		'subject' => "Temat",
		'delete_question' => "Czy chcesz usunąć aktualny newsletter?",
		'delete_group_question' => "Do you want to delete the current group?", // TRANSLATE
		'delete_ok' => "Newsletter został usunięty.",
		'delete_nok' => "BŁĄD: Nie usunięto newslettera",
		'test_email' => "Wiadomość testowa", // CHECK
// changed from: "Test E-mail"
// changed to  : "Test email"

		'test_email_question' => "Wiadomość testowa zostanie wysłana na Twój adres e-mail %s !\\n Czy chcesz kontynuować?", // CHECK
// changed from: "This will send a test E-mail to your test E-mail account %s!\\n Do you want to proceed?"
// changed to  : "This will send a test email to your test email account %s!\\n Do you want to proceed?"

		'test_mail_sent' => "Wiadomość testowa została wysłana na Twój adres e-mail %s.", // CHECK
// changed from: "The test E-mail has been sent to the test E-mail account %s."
// changed to  : "The test email has been sent to the test email account %s."

		'malformed_mail_group' => "Lista mailingowa %s zawiera nieprawidłowy adres e-mail '%s'!\\nNewsletter nie został zapisany!", // CHECK
// changed from: "Mailing list %s has malformed E-mail '%s'!\\nThe newsletter has not been saved!"
// changed to  : "Mailing list %s has malformed email '%s'!\\nThe newsletter has not been saved!"

		'malformed_mail_sender' => "Nieprawidłowy adres e-mail nadawcy '%s'!\\nNewsletter nie został zapisany!", // CHECK
// changed from: "The senders E-mail address '%s' is malformed!\\nThe newsletter has not been saved!"
// changed to  : "The senders email address '%s' is malformed!\\nThe newsletter has not been saved!"

		'malformed_mail_reply' => "Nieprawidłowy adres zwrotny '%s'!\\nNewsletter nie został zapisany!", // CHECK
// changed from: "The reply E-mail address '%s' is malformed!\\nThe newsletter has not been saved!"
// changed to  : "The reply email address '%s' is malformed!\\nThe newsletter has not been saved!"

		'malformed_mail_test' => "Nieprawidłowy adres wiadomości testowej '%s'!\\nNewsletter nie został zapisany!", // CHECK
// changed from: "The test E-mail address '%s' is malformed!\\nThe newsletter has not been saved!"
// changed to  : "The test email address '%s' is malformed!\\nThe newsletter has not been saved!"

		'send_question' => "Czy chcesz wysłać ten newsletter do odbiorców listy mailingowej?",
		'send_test_question' => "To jest test - nie wysłano newslettera.\\n      Potwierdź, żeby kontynuować.",
		'domain_ok' => "Sprawdzono domenę %s.",
		'domain_nok' => "Nie można sprawdzić domeny %s.",
		'email_malformed' => "Nieważny adres e-mail %s.", // CHECK
// changed from: "The E-mail address %s is malformed."
// changed to  : "The email address %s is malformed."

		'domain_check_list' => "Kontrola domeny dla listy mailingowej  %s",
		'domain_check_begins' => "Rozpoczęcie kontroli domeny",
		'domain_check_ends' => "Koniec kontroli domeny",
		'newsletter_type_0' => "Dokument",
		'newsletter_type_1' => "Pole dokumentu",
		'newsletter_type_2' => "Obiekt",
		'newsletter_type_3' => "Pole obiektu",
		'newsletter_type_4' => "Plik",
		'newsletter_type_5' => "Tekst",
		'newsletter_type_6' => "Załącznik",
		'newsletter_type_7' => "URL", // TRANSLATE
		'all_list' => "-- Wszystkie listy --",
		'newsletter_test' => "Test", // TRANSLATE
		'send_to_list' => "Wyślij na listę mailingową %s.",
		'campaign_starts' => "Kampania newsletterowa rozpoczęła się...",
		'campaign_ends' => "Koniec kampanii.",
		'test_no_mail' => "Testy - nie wysłano żadnych e-maili...", // CHECK
// changed from: "Testing - no E-mail s will be sent..."
// changed to  : "Testing - no emails will be sent..."

		'sending' => "Wyślij...",
		'mail_not_sent' => "Nie wysłano wiadomości e-mail '%s'.", // CHECK
// changed from: " E-mail '%s' cannot be sent."
// changed to  : " email '%s' cannot be sent."

		'filter' => "Filtr",
		'send_all' => "Wyślij do wszystkich",
		'lists_overview_menu' => "Przegląd list",
		'lists_overview' => "Przegląd list",
		'copy' => "Kopiuj",
		'copy_newsletter' => "Kopiuj newsletter",
		'continue_camp' => "Ostatnia kampania newsletterowa nie została jeszcze całkowicie ukończona!<br>Można kontynuować ostatnią kampanie.<br>Czy chcesz teraz kontynuować ostanią kampanię?",
		'reject_malformed' => "Nie wysyłać wiadomości e-mail, jeżeli adres jest nieważny.", // CHECK
// changed from: "Do not send E-mail if address is malformed."
// changed to  : "Do not send email if address is malformed."

		'reject_not_verified' => "Nie wysyłać wiadomości e-mail, jeżeli nie można zweryfikować wiadomości e-mail.", // CHECK
// changed from: "Do not send E-mail if address cannot be verified."
// changed to  : "Do not send email if address cannot be verified."

		'send_step' => "Liczba wiadomości e-mail na jeden proces wysyłania", // CHECK
// changed from: "Number of E-mails per load"
// changed to  : "Number of emails per load"

		'test_account' => "Testowe konto e-mail",
		'log_sending' => "Sporządź wpis do logu, jeżeli wysłano e-maile", // CHECK
// changed from: "Create a logbook entry when sending E-mail."
// changed to  : "Create a logbook entry when sending email."

		'default_sender' => "Standardowy nadawca",
		'default_reply' => "Standardowy adres zwrotny",
		'default_htmlmail' => "Das Standardowym formatem wiadomosci w-mail jest HTML", // CHECK
// changed from: "The default E-mail format is HTML."
// changed to  : "The default email format is HTML."

		'isEmbedImages' => "Embed images", // TRANSLATE
		'ask_to_preserve' => "Ostatnia kampania newsletterowa nie została jeszcze całkowicie ukończona!<br>Jeżeli teraz zapiszesz newslettera,nie możesz kontynuować ostatniej kampanii!<br>Czy chcesz kontynować?",
		'log_save_newsletter' => "Newsletter został zapisany.",
		'log_start_send' => "Rozpocznij kampanię newsletterową.",
		'log_end_send' => "Zakończono kampanię newsletterową.",
		'log_continue_send' => "Kontynuacja kampanii newsletterowej...",
		'log_campaign_reset' => "Parametry kampanii newsletterowej zostąły przywrócone.",
		'mail_sent' => "Newsletter został wysłany do %s.",
		'must_save' => "Newsletter został zmieniony.\\nZanim go wyślesz, musiz go zapisać!",
		'email_exists' => "Adres e-mail już istnieje!", // CHECK
// changed from: "The E-mail address already exists!"
// changed to  : "The email address already exists!"

		'email_max_len' => "Adres e-mail nie może być dłuższy niż 255 znaków!", // CHECK
// changed from: "The E-mail address cannot exceed 255 charachters!"
// changed to  : "The email address cannot exceed 255 charachters!"

		'no_email' => "Nie wybrano adresu e-mail!", // CHECK
// changed from: " E-mail address missing!"
// changed to  : " email address missing!"

		'email_new' => "Podaj adres e-mail!", // CHECK
// changed from: "Please provide an E-mail address!"
// changed to  : "Please provide an email address!"

		'email_delete' => "Czy chcesz usunąć wybrany adres e-mail??", // CHECK
// changed from: "Do you want to delete the selected E-mail addresses?"
// changed to  : "Do you want to delete the selected email addresses?"

		'email_delete_all' => "Czy chesz usunąć wszystkie adresy e-mail?", // CHECK
// changed from: "Do you want to delete all E-mail addresses?"
// changed to  : "Do you want to delete all email addresses?"

		'email_edit' => "Adres e-mail został zmieniony!", // CHECK
// changed from: "E-mail address changed!"
// changed to  : "email address changed!"

		'nothing_to_save' => "Brak obiektu do zapisania!",
		'csv_delimiter' => "Separator",
		'csv_col' => "Kolumna e-mail", // CHECK
// changed from: " E-mail col."
// changed to  : " email col."

		'csv_hmcol' => "Kolumna HTML",
		'csv_salutationcol' => "Kolumna zwrotu powitalnego",
		'csv_titlecol' => "Kolumna tytułu",
		'csv_firstnamecol' => "Kolumna imienia",
		'csv_lastnamecol' => "Kolumna nazwiska",
		'csv_export' => "Plik '%s' zostął zapamiętany.",
		'customer_email_field' => "Pole e-mail klienta", // CHECK
// changed from: "Cust. E-mail field"
// changed to  : "Cust. email field"

		'customer_html_field' => "Pole HTML klienta",
		'customer_salutation_field' => "Pole zwrotu powitalnego klienta",
		'customer_title_field' => "Pole tytułu klienta",
		'customer_firstname_field' => "Pole imienia klienta",
		'customer_lastname_field' => "Pole nazwiska klienta",
		'csv_html_explain' => "(0 - brak kolumny HTML)",
		'csv_salutation_explain' => "(0 - brak kolumny zwrotu powitalnego)",
		'csv_title_explain' => "(0 - brak kolumny tytułu)",
		'csv_firstname_explain' => "(0 - brak kolumny imienia)",
		'csv_lastname_explain' => "(0 - brak kolumny nazwiska)",
		'email' => "E-mail", // CHECK
// changed from: " E-mail "
// changed to  : " email "

		'lastname' => "Nazwisko",
		'firstname' => "Imię",
		'salutation' => "Tytuł powitalny",
		'title' => "Tytuł",
		'female_salutation' => "Forma żeńska",
		'male_salutation' => "Forma męska",
		'edit_htmlmail' => "Odbierz wiadomość HTML", // CHECK
// changed from: "Receive HTML E-mail "
// changed to  : "Receive HTML email "

		'htmlmail_check' => "HTML", // TRANSLATE
		'double_name' => "Nazwa newslettera już istnieje.",
		'cannot_preview' => "Nie można wyświetlić podglądu newslettera.",
		'empty_name' => "Nazwa nie może być pusta!",
		'edit_email' => "Edytuj adres e-mail", // CHECK
// changed from: "Edit E-mail address"
// changed to  : "Edit email address"

		'add_email' => "Dodaj adres e-mail", // CHECK
// changed from: "Add E-mail address"
// changed to  : "Add email address"

		'none' => "-- brak --",
		'must_save_preview' => "Zmieniono adres newslettera.\\nZanim zostanie wyświetlony podgląd, musisz go zapisać!",
		'black_list' => "Czarna lista",
		'email_is_black' => "Email %s znajduje się na czarnej liście!", // CHECK
// changed from: " E-mail is on the balck list!"
// changed to  : " email is on the balck list!"

		'upload_nok' => "Nie można pobrać pliku.",
		'csv_download' => "Download CSV file", // TRANSLATE
		'csv_upload' => "Upload CSV file", // TRANSLATE
		'finished' => "Koniec",
		'cannot_open' => "Nie można otworzyć pliku",
		'search_email' => "Szukam adresu e-mail...", // CHECK
// changed from: "Search E-mail..."
// changed to  : "Search email..."

		'search_text' => "Wprowadź adres e-mail", // CHECK
// changed from: "Enter E-mail please"
// changed to  : "Enter email please"

		'search_finished' => "Wyszukiwanie zakończone.\\nZnaleziono: %s",
		'email_double' => "Adres e-mail %s już istnieje!", // CHECK
// changed from: "The E-mail address %s already exists!"
// changed to  : "The email address %s already exists!"

		'error' => "BŁĄD",
		'warning' => "OSTRZEŻENIE",
		'file_email' => "Pliki CSV",
		'edit_file' => "Edytuj pliki CSV",
		'show' => "Pokaż",
		'no_file_selected' => "Nie wybrano pliku",
		'file_is_empty' => "The CSV file is empty", // TRANSLATE
		'file_all_ok' => "The CSV file has no invalid entries", // TRANSLATE
		'del_email_file' => "Usunąć adres e-mail '%s'?", // CHECK
// changed from: "Delete E-mail '%s'?"
// changed to  : "Delete email '%s'?"

		'email_missing' => "Missing E-mail address", // CHECK
// changed from: "Missing E-mail address"
// changed to  : "Missing email address"

		'yes' => "Tak",
		'no' => "Nie",
		'select_file' => "Wybór plików",
		'clear_log' => "Wyczyść log",
		'clearlog_note' => "Czy chcesz wyczyścić cały log?",
		'log_is_clear' => "Log został wyczyszczony.",
		'property' => "Właściwości",
		'edit' => "Edycja",
		'details' => "Szczegóły",
		'path' => "Ścieżka",
		'dir' => "Katalog",
		'block' => "Blok %s",
		'new_newsletter_group' => "Nowa grupa",
		'group' => "Grupa",
		'path_nok' => "Nieprawidłowa ścieżka!",
		'save_group_ok' => "Grupa została zapisana.",
		'delete_group_ok' => "Grupa została usunięta.",
		'delete_group_nok' => "BŁĄD: Grupa nie została usunięta",
		'path_not_valid' => "Nieprawidłowa ścieżka",
		'no_subject' => "Nie podano tematu. Czy wiadomośc ma zostać mimo to wysłana?",
		'mail_failed' => "Wiadomość '%s' nie może zostać wysłana. Możliwa przyczyna to błędna konfiguracja serwera!", // CHECK
// changed from: " E-mail '%s' cannot be sent. A possible cause is an incorrect server configuration."
// changed to  : " email '%s' cannot be sent. A possible cause is an incorrect server configuration."

		'reject_save_malformed' => "Nie zapisuj newslettera w przypadku, gdy adres e-mail jest nieprawidłowy.",
		'rfc_email_check' => "Validate conform to rfc 3696.<br>WARNIGN: This validation can take heavy influence on the speed of your server.", // TRANSLATE
		'use_https_refer' => "Użyj HTTPS do odniesień",
		'use_base_href' => "Use &lt;base href=... in head", // TRANSLATE
		'we_filename_notValid' => "Wpisana nazwa jest nieprawidłowa!\\nDozwolone znaki to litery od a do z (wielkie lub małe) , cyfry, znak podkreślenia (_), minus (-), kropka (.) i spacja ( ).",
		'send_wait' => "Czas oczekiwania do następnego procesu wysyłania<br> (w ms)",
		'send_images' => "Dodaj obrazki jako załączniki do e-maila",
		'prepare_newsletter' => "Przygotowanie wysyłki...",
		'use_port_check' => "Użyj portu do odniesień",
		'use_port' => "Port", // TRANSLATE
		'sum_group' => "Adres(y) e-mail na liście %s", // CHECK
// changed from: "E-mail address(es) in liste %s"
// changed to  : "email address(es) in liste %s"

		'sum_all' => "Adres(y) e-mail wszystkich list", // CHECK
// changed from: "E-mail adress(es) all list(s)"
// changed to  : "email adress(es) all list(s)"

		'retry' => "Powtórz",
		'charset' => "Kodowanie znaków",
		'additional_clp' => "Additional reply address (option -f)", // TRANSLATE
		'html_preview' => "show HTML preview", // TRANSLATE
		'status' => "Status", // TRANSLATE
		'statusAll' => "all entries", // TRANSLATE
		'statusInvalid' => "invalid entries", // TRANSLATE
		'invalid_email' => "The email is not valid.", // TRANSLATE
		'blockFieldError' => "ERROR: 'Invalid value in Block %s, Field %s!", // TRANSLATE
		'operator' => array(
				'startWith' => "starts with", // TRANSLATE
				'endsWith' => "ends with", // TRANSLATE
				'contains' => "contains", // TRANSLATE
		),
		'logic' => array(
				'and' => "and", // TRANSLATE
				'or' => "or", // TRANSLATE
		),
		'default' => array(
				'female' => "Mrs.", // TRANSLATE
				'male' => "Mr.", // TRANSLATE
		),
		'no_newsletter_selected' => "No newsletter selected. Please open the newsletter first.", // TRANSLATE
);