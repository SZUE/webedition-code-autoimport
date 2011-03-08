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
$l_modules_voting = array(
		'no_perms' => 'You do not have permission to use this option.',
		'delete_alert' => 'Usuń aktualną ankietę/grupę.\\n Jesteś pewien?',
		'result_delete_alert' => 'Delete the current voting results.\\nAre you sure?', // TRANSLATE
		'nothing_to_delete' => 'Brak obiektu do usunięcia!',
		'nothing_to_save' => 'Brak obiektu do zapamiętania',
		'we_filename_notValid' => 'Nieprawidłowa nazwa użytkownika!\\nDopuszczalne są znaki alfanumeryczne, wielkie i małe litery oraz znak podkreślenia, myślnik, kropka i spacja (a-z, A-Z, 0-9, _, -, ., )',
		'menu_new' => 'Nowy',
		'menu_save' => 'Zapisz',
		'menu_delete' => 'Usuń',
		'menu_exit' => 'Zakończ',
		'menu_info' => 'Informacje',
		'menu_help' => 'Pomoc',
		'headline' => 'Imiona i nazwiska',
		'headline_name' => 'Nazwisko',
		'headline_publish_date' => 'Data ustawienia',
		'headline_data' => 'Dane ankiety',
		'publish_date' => 'Data',
		'publish_format' => 'Format', // TRANSLATE

		'published_on' => 'Opublikowano dn.',
		'total_voting' => 'Łączna liczba głosów',
		'reset_scores' => 'Przywróć liczenie punktów',
		'inquiry_question' => 'Pytanie',
		'inquiry_answers' => 'Odpowiedzi',
		'question_empty' => 'Pole pytania jest puste. Wypełnij je!',
		'answer_empty' => 'Jedno lub więcej pól odpowiedzi jest pustych. Podaj odpowiedź/odpowiedzi!',
		'invalid_score' => 'Wartość zliczania punktów musi być liczbą; podaj ponownie!',
		'headline_revote' => 'Głosuj ponownie',
		'headline_help' => 'Pomoc',
		'inquiry' => 'Ankieta',
		'browser_vote' => 'Z tej przeglądarki nie można ponownie głosować przed upływem',
		'one_hour' => '1 godziny',
		'feethteen_minutes' => '15 minut',
		'thirthty_minutes' => '30 minut',
		'one_day' => '1 dnia',
		'never' => '--nigdy--',
		'always' => '--zawsze--',
		'cookie_method' => 'Metoda Cookie',
		'ip_method' => 'Metoda IP',
		'time_after_voting_again' => 'Czas do ponownego głosowania',
		'cookie_method_help' => 'Wykorzystaj tą metodę, jeżeli nie chcesz/nie możesz korzystać z metody IP. Pamiętaj, że użytkownik może wyłączyć Cookies w przeglądarce. Opcja "Metoda Fallback IP" wymaga wykorzystania znaczników we:cookie w szablonie.',
		'ip_method_help' => 'Jeżeli twoja strona jest dostępna tylko z intranetu a Twoi użytkownicy łączą się bez użycia Proxy, użyj tej metody. Pamiętaj, że niektóre serwery dynamicznie przydzielają adresy IP.',
		'time_after_voting_again_help' => 'W celu uniknięcia częstego głosowania przez jednego i tego samego użytkownika, wprowadź okres czasu, który musi upłynąć, zanim z tego komputera będzie można ponownie zagłosować. W przypadku komputerów dostępnych dla wielu użytkowników jest to najrozsądniejsze rozwiązanie. W pozostałych przypadkach wybierz "nigdy".',
		'property' => 'Właściwości',
		'variant' => 'Wersja',
		'voting' => 'Głosowanie',
		'result' => 'Wynik',
		'group' => 'Grupa',
		'name' => 'Nazwa',
		'newFolder' => 'Nowa grupa',
		'save_group_ok' => 'Grupa została zapamiętana.',
		'save_ok' => 'Głosowanie zostało zapamiętane.',
		'path_nok' => 'Niewłaściwa ścieżka!',
		'name_empty' => 'Nazwa nie może być pusta!',
		'name_exists' => 'Nazwa nie istnieje!',
		'wrongtext' => 'Nazwa nie jest prawidłowa!',
		'voting_deleted' => 'Usunięto głosowanie.',
		'group_deleted' => 'Usunięto grupę.',
		'access' => 'Dostęp',
		'limit_access' => 'Ogranicz dostęp',
		'limit_access_text' => 'Dostęp wyłącznie dla następujacych użytkowników',
		'variant_limit' => 'Musi istnieć przynajmniej jedna wersja!',
		'answer_limit' => 'Ankieta musi zawierać przynajmiej dwie odpowiedzi!',
		'valid_txt' => 'Należy aktywować pole wyboru "Aktywne", żeby głosowanie zostało zapamiętane na Twojej stronie, a po upływie ważności zostało wycofane. Ustaw za pomocą menu kontekstowych datę i czas, w których ma upływać głosowanie. Od tego momentu nie będą już przyjmowane żadne nowe głosy.',
		'active_till' => 'Aktywne',
		'valid' => 'Ważność',
		'export' => 'Eksport',
		'export_txt' => 'Eksport danych głosowania do pliku CSV (Comma Separated Values).',
		'csv_download' => "Download CSV file", // TRANSLATE
		'csv_export' => "Plik '%s' został zapisany.",
		'fallback' => 'Metoda Fallback IP',
		'save_user_agent' => 'Zapisz/porównaj dane programu użytkownika',
		'save_changed_voting' => "Voting has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'voting_log' => 'Protokołuj głosowanie w logu',
		'forbid_ip' => 'Zablokuj kolejny adres IP',
		'until' => 'do',
		'options' => 'Opcje',
		'control' => 'Kontrola',
		'data_deleted_info' => 'Dane zostały usunięte!',
		'time' => 'Czas',
		'ip' => 'IP', // TRANSLATE
		'user_agent' => 'Program użytkownika',
		'cookie' => 'Cookie', // TRANSLATE
		'delete_ipdata_question' => 'Chcesz wyczyścić wszystkie zapamietane dane IP. Na pewno?',
		'delete_log_question' => 'Chcesz usunąć wszystkie wpisy do logu głosowania.Na pewno?',
		'delete_ipdata_text' => 'Zapamiętane dane IP zajmują %s bajtów pamięci. Można je usunąć za pomocą przyciska  \'Usuń\'. Pamiętaj, że wszystkie zapisane dane IP głosowania zostaną usunięte a wyniki ?łosowania nie bedą już tak dokładne, ponieważ możliwe jest powtórzenie głosowania.',
		'status' => 'Status', // TRANSLATE
		'log_success' => 'Sukces',
		'log_error' => 'Błąd',
		'log_error_active' => 'Błąd: nieaktywny',
		'log_error_revote' => 'Błąd: nowe głosowanie',
		'log_error_blackip' => 'Błąd: zablokowane IP',
		'log_is_empty' => 'Log jest pusty!',
		'enabled' => 'Włączony',
		'disabled' => 'Wyłączony',
		'log_fallback' => 'Fallback', // TRANSLATE

		'new_ip_add' => 'Proszę podać nowy adres IP!',
		'not_valid_ip' => 'Nieprawidłowy adres IP',
		'not_active' => 'The entered date is in the past!', // TRANSLATE

		'headline_datatype' => 'Type of Inquiry', // TRANSLATE
		'AllowFreeText' => 'Allow free text', // TRANSLATE
		'AllowImages' => 'Allow images', // TRANSLATE
		'AllowSuccessor' => 'redirect to:', // TRANSLATE
		'AllowSuccessors' => 'allow individual redirects', // TRANSLATE
		'csv_charset' => "Export charset", // TRANSLATE
		'imageID_text' => "Image ID", // TRANSLATE
		'successorID_text' => "Successor ID", // TRANSLATE
		'mediaID_text' => "Media-ID", // TRANSLATE
		'AllowMedia' => 'Allow Media such as Audio or video files', // TRANSLATE

		'voting-id' => 'Voting ID', // TRANSLATE
		'voting-session' => 'Voting Session', // TRANSLATE
		'voting-successor' => 'successor', // TRANSLATE
		'voting-additionalfields' => 'add. data', // TRANSLATE
		'answerID' => 'answer ID', // TRANSLATE
		'answerText' => 'answer text', // TRANSLATE

		'userid_method' => 'For logged in Users (customer management), compare to saved customer ID (the log has to be active)', // TRANSLATE
		'IsRequired' => 'This is a required field', // TRANSLATE

		'answer_limit' => 'The inquiry must consist of at least two - in case free text answers are allowd one - answers!', // TRANSLATE
		'folder_path_exists' => "Folder already exists!", // TRANSLATE
);