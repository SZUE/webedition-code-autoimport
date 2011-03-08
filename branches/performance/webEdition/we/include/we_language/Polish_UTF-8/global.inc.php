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
 * Language file: global.inc.php
 * Provides language strings.
 * Language: English
 */
$l_global = array(
		'new_link' => "New Link", // TRANSLATE // It is important to use the GLOBALS ARRAY because in linklists, the file is included in a function.
		'load_menu_info' => "Załaduj dane!<br>Przy wielu wpisach w menu może to długo potrwać ...",
		'text' => "Tekst",
		'yes' => "tak",
		'no' => "nie",
		'checked' => "Aktywny",
		'max_file_size' => "Maks. wielkość pliku (w bajtach)",
		'default' => "Domyślne",
		'values' => "Wartość",
		'name' => "Nazwa",
		'type' => "Typ",
		'attributes' => "Atrybut",
		'formmailerror' => "Formularz nie został wysłany z następującego powodu:",
		'email_notallfields' => "Nie wypełniłeś wszystkich wymaganych pól!",
		'email_ban' => "Nie masz uprawnień do korzystania z tego skryptu!",
		'email_recipient_invalid' => "Adres odbiorcy jest nieprawidłowy!",
		'email_no_recipient' => "Adres odbiorcy nie istnieje!",
		'email_invalid' => "Twój <b>adres e-mail </b> jest nieprawidłowy!",
		'captcha_invalid' => "The entered security code is wrong!", // TRANSLATE
		'question' => "Pytanie",
		'warning' => "Ostrzeżenie",
		'we_alert' => "Ta funkcja nie jest dostępna w wersji demonstracyjnej webEdition!",
		'index_table' => "Tabela indeksu",
		'cannotconnect' => "Nie można było nawiązać połączenia z serwerem webEdition!",
		'recipients' => "Odbiorca formularza pocztowego",
		'recipients_txt' => "Wpisz tutaj wszystkie adresy e-mail, do których może być skierowany formularz za pomocą funkcji formularza pocztowego(&lt;we:form type=&quot;formmail&quot; ..&gt;). Jeżeli nie podanych adresów e-mail, nie można wysyłać formularzy za pomocą formularza poczty!",
		'std_mailtext_newObj' => "Stworzono nowy obiekt %s klasy %s!",
		'std_subject_newObj' => "Nowy obiekt",
		'std_subject_newDoc' => "Nowy dokument",
		'std_mailtext_newDoc' => "Stworzono nowy dokument %s!",
		'std_subject_delObj' => "Obiekt usunięto",
		'std_mailtext_delObj' => "Obiekt %s został usunięty!",
		'std_subject_delDoc' => "Dokument usunięto",
		'std_mailtext_delDoc' => "Dokument %s został usunięty!",
		'we_make_same' => array(
				'text/html' => "Po zapisie nowej strony",
				'text/webedition' => "Po zapisie nowej strony",
				'objectFile' => "New object after saving",
		),
		'no_entries' => "Brak wpisów!",
		'save_temporaryTable' => "Zapisz ponownie tymczasowe dokumenty robocze",
		'save_mainTable' => "Zapisz teraz główną tabelę bazy danych",
		'add_workspace' => "Dodaj obszar roboczy",
		'folder_not_editable' => "Nie można wybrać tego katalogu!",
		'modules' => "Moduł",
		'modules_and_tools' => "Modules and Tools", // TRANSLATE
		'center' => "Centruj",
		'jswin' => "Wyskakujące okno",
		'open' => "Otwórz",
		'posx' => "Pozycja x",
		'posy' => "Pozycja y",
		'status' => "Status", // TRANSLATE
		'scrollbars' => "Scrollbars",
		'menubar' => "Menubar",
		'toolbar' => "Toolbar", // TRANSLATE
		'resizable' => "Resizable", // TRANSLATE
		'location' => "Location", // TRANSLATE
		'title' => "Tytuł",
		'description' => "Opis",
		'required_field' => "Pole obowiązkowe",
		'from' => "od",
		'to' => "do",
		'search' => "Search", // TRANSLATE
		'in' => "in", // TRANSLATE
		'we_rebuild_at_save' => "Automatyczny Rebuild",
		'we_publish_at_save' => "Opublikuj przy zapisaniu",
		'we_new_doc_after_save' => "New Document after saving", // TRANSLATE
		'we_new_folder_after_save' => "New folder after saving", // TRANSLATE
		'we_new_entry_after_save' => "New entry after saving", // TRANSLATE
		'wrapcheck' => "Zawijanie komórki",
		'static_docs' => "Dokumenty statyczne",
		'save_templates_before' => "Zapisz wcześniej szablony",
		'specify_docs' => "Dokumenty o następujących kryteriach",
		'object_docs' => "Wszystkie obiekty",
		'all_docs' => "Wszystkie dokumenty",
		'ask_for_editor' => "Zapytaj o oczekiwany edytor",
		'cockpit' => "Cockpit", // TRANSLATE
		'introduction' => "Wprowadzenie",
		'doctypes' => "Typy dokumentu",
		'content' => "Zawartość",
		'site_not_exist' => "Ta strona nie istnieje!",
		'site_not_published' => "Ta strona nie jest jeszcze opublikowana!",
		'required' => "Wpis obowiązkowy",
		'all_rights_reserved' => "Wszelkie prawa zastrzeżone",
		'width' => "Szerokość",
		'height' => "Wysokość",
		'new_username' => "Nowa nazwa użytkownika",
		'username' => "Nazwa użytkownika",
		'password' => "Hasło",
		'documents' => "Dokumenty",
		'templates' => "Szablony",
		'objects' => "Objects", // TRANSLATE
		'licensed_to' => "Licencjobiorcja",
		'left' => "lewa",
		'right' => "prawa",
		'top' => "góra",
		'bottom' => "dół",
		'topleft' => "góra lewa",
		'topright' => "góra prawa",
		'bottomleft' => "dół lewa",
		'bottomright' => "dół prawa",
		'true' => "tak",
		'false' => "nie",
		'showall' => "Pokaż wszystko",
		'noborder' => "bez marginesu",
		'border' => "Margines",
		'align' => "Wyrównanie",
		'hspace' => "Odstęp poziomy",
		'vspace' => "Odstęp pionowy",
		'exactfit' => "dopasuj",
		'select_color' => "Wybór koloru",
		'changeUsername' => "Zmień nazwę użytkownika",
		'changePass' => "Zmień hasło",
		'oldPass' => "Stare hasło",
		'newPass' => "Nowe hasło",
		'newPass2' => "Powtórzenie nowego hasła",
		'pass_not_confirmed' => "Powtórzenie nowego hasła nie zgadza się z nowym hasłem!",
		'pass_not_match' => "Stare hasło nie zgadza się!",
		'passwd_not_match' => "Hasło nie zgadza się!",
		'pass_to_short' => "Hasło musi się składać z przynajmniej 4 znaków!",
		'pass_changed' => "Hasło zostało zmienione!",
		'pass_wrong_chars' => "Hasło może zawierać tylko litery (a-z oraz A-Z) i cyfry (0-9)!",
		'username_wrong_chars' => "Username may only contain alpha-numeric characters (a-z, A-Z and 0-9) and '.', '_' or '-'!", // TRANSLATE
		'all' => "Wszystkie",
		'selected' => "Wybrane",
		'username_to_short' => "Nazwa użytkownika musi się składać przynajmniej z 4 znaków!",
		'username_changed' => "Nazwa użytkownika została zmieniona!",
		'published' => "Opublikowany",
		'help_welcome' => "Witamy w systemie pomocy webEdition",
		'edit_file' => "Edytuj dane",
		'docs_saved' => "Dokumenty zostały zabezpieczone!",
		'preview' => "Podgląd",
		'close' => "Zamknij okno",
		'loginok' => "<strong>Logowanie powiodło się!</strong><br>webEdition powinien otworzyć się teraz w nowym oknie.<br>Jeżeli tak się nie stało, prawdopodobnie zablokowałeś okna wyskakujące w swojej przeglądarce!",
		'apple' => "&#x2318;", // TRANSLATE
		'shift' => "SHIFT", // TRANSLATE
		'ctrl' => "STRG",
		'required_fields' => "Pola obowiązkowe",
		'no_file_uploaded' => "<p class=\"defaultfont\">Nie załadowano jeszcze żadnego dokumentu.</p>",
		'openCloseBox' => "Otwórz-/Zamknij",
		'rebuild' => "Rebuild", // TRANSLATE
		'unlocking_document' => "Zatwierdź dokument",
		'variant_field' => "Pole wariantów",
		'redirect_to_login_failed' => "Please press the following link, if you are not redirected within the next 30 seconds ", // TRANSLATE
		'redirect_to_login_name' => "webEdition login", // TRANSLATE
		'untitled' => "Untitled", // TRANSLATE
		'no_document_opened' => "There is no document opened!", // TRANSLATE
		'credits_team' => "webEdition Team", // TRANSLATE
		'developed_further_by' => "developed further by", // TRANSLATE
		'with' => "with the", // TRANSLATE
		'credits_translators' => "Translations", // TRANSLATE
		'credits_thanks' => "Thanks to", // TRANSLATE
		'unable_to_call_ping' => "Connection to server is lost - RPC: Ping!", // TRANSLATE
		'unable_to_call_setpagenr' => "Connection to server is lost - RPC: setPageNr!", // TRANSLATE
		'nightly-build' => "nightly Build", // TRANSLATE
		'alpha' => "Alpha", // TRANSLATE
		'beta' => "Beta", // TRANSLATE
		'rc' => "RC", // TRANSLATE
		'preview' => "preview", // TRANSLATE
		'release' => "official release", // TRANSLATE

		'categorys' => "Categories", // TRANSLATE
		'navigation' => "Navigation", // TRANSLATE
);
