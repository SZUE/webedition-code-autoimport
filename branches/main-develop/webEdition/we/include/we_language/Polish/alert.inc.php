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
 * Language file: alert.inc.php
 * Provides language strings.
 * Language: English
 */
$l_alert = array(
		FILE_TABLE => array(
				'in_wf_warning' => "Przed dołączeniem dokumentu do Workflow, należy go zapisać!\\nCzy zapisać dokument?",
				'not_im_ws' => "Plik nie znajduje się w twoim obszarze roboczym!",
		),
		TEMPLATES_TABLE => array(
				'in_wf_warning' => "Przed dołączeniem szablonu do Workflow, należy go zapisać!\\nCzy zapisać teraz szablon?",
				'not_im_ws' => "Szablon nie znajduje się w twoim obszarze roboczym!",
		),
		'folder' => array(
				'not_im_ws' => "Ten katalog nie znajduje się w twoim obszarze roboczym!",
		),
		'nonew' => array(
				'objectFile' => "Nie możesz stworzyć obiektów, gdyż albo brakuje ci uprawnień<br>albo nie istnieje klasa, w której ważny jest jeden z Twoich obszarów pracy!",
		),
		'notice' => "Notice",
		'warning' => "Warning", // TRANSLATE
		'error' => "Error", // TRANSLATE

		'noRightsToDelete' => "\\'%s\\' cannot be deleted! You do not have permission to perform this action!", // TRANSLATE
		'noRightsToMove' => "\\'%s\\' cannot be moved! You do not have permission to perform this action!", // TRANSLATE
		'delete_recipient' => "Czy naprawdę chcesz usunąć wybrany adres e-mail?",
		'recipient_exists' => "Adres e-mail już istnieje!",
		'input_name' => "Wprowadź nowy adres e-mail!",
		'input_file_name' => "Enter a filename.", // TRANSLATE
		'max_name_recipient' => "Adres e-mail może składać się najwyżej z 255 znaków!",
		'not_entered_recipient' => "Nie wprowadzono adresu e-mail!",
		'recipient_new_name' => "Zmiana adresu e-mail!",
		'required_field_alert' => "Pole '%s' jest polem obowiązkowym i należy je wypełnić!",
		'phpError' => "Nie można uruchomić webEdition",
		'3timesLoginError' => "Logowanie %sx nie powiodło się! Odczekaj %s minut i spróbuj ponownie!",
		'popupLoginError' => "Nie można otworzyć okna webEdition!\\n\\n Uruchomienie webEdition jest możliwe tylko wtedy, gdy twoja przeglądarka zezwala na otwieranie okien wyskakujących.",
		'publish_when_not_saved_message' => "Nie zapisano dokumentu! Czy mimo to chcesz go opublikować?",
		'template_in_use' => "Szablon jest używany i dlatego nie można go usunąć!",
		'no_cookies' => "Nie aktywowano Cookies. Aktywuj Cookies w swojej przeglądarce, żeby program webEdition zadziałał prawidłowo!",
		'doctype_hochkomma' => "Nazwa typu dokumentu nie może zawierać ' (apostrofu) ani , (przecinka)!",
		'thumbnail_hochkomma' => "Nazwa widoku miniatur nie może zawierać ' (apostrofu) ani , (przecinka)!",
		'can_not_open_file' => "Nie udało się otworzyć pliku %s !",
		'no_perms_title' => "Brak uprawnień",
		'no_perms_action' => "You don't have the permission to perform this action.", // TRANSLATE
		'access_denied' => "Odmowa dostępu!",
		'no_perms' => "Jeżeli potrzebujesz praw dostępu, zwróć się do właściciela (%s)<br>lub administratora!",
		'temporaere_no_access' => "Chwilowy brak dostępu",
		'temporaere_no_access_text' => "Plik (%s) jest chwilowo edytowany przez użytkownika '%s' .",
		'file_locked_footer' => "This document is edited by \"%s\" at the moment.", // TRANSLATE
		'file_no_save_footer' => "Nie masz uprawnień wymaganych do zapisania tego pliku.",
		'login_failed' => "Błędna nazwa użytkownika i/lub hasło!",
		'login_failed_security' => "Nie można uruchomić webEdition!\\n\\nZe względów bezpieczeństwa logowanie zostało przerwane, ponieważ przekroczono maksymalny czas logowania!\\n\\nZaloguj się ponownie.",
		'perms_no_permissions' => "Brak uprawnień do wykonania tej operacji!\\nZaloguj się ponownie!",
		'no_image' => "Wybrany plik nie jest obrazkiem!",
		'delete_ok' => "Pliki lub katalogi usunięto!",
		'delete_cache_ok' => "Cache successfully deleted!", // TRANSLATE
		'nothing_to_delete' => "Nie wybrano niczego do skasowania!",
		'delete' => "Usunąć wybrane wpisy?\\n/Na pewno?",
		'delete_cache' => "Delete cache for the selected entries?\\nDo you want to continue?", // TRANSLATE
		'delete_folder' => "Usunąć wybrany katalog?\\nPamiętaj,że wtedy wraz z nim zostaną usunięte automatycznie zawarte w katalogu pliki i katalogi!\\nNa pewno?",
		'delete_nok_error' => "Nie można usunąć pliku '%s'.",
		'delete_nok_file' => "Nie można usunąć pliku '%s'.\\nMożliwe, że plik jest chroniony przed zapisem.",
		'delete_nok_folder' => "Nie można usunąć katalogu '%s'.\\nMożliwe, że katalog jest chroniony przed zapisem.",
		'delete_nok_noexist' => "Plik '%s' nie istnieje!",
		'noResourceTitle' => "No Item!", // TRANSLATE
		'noResource' => "The document or directory does not exist!", // TRANSLATE
		'move_exit_open_docs_question' => "Before documents of a table can be moved, all documents of this table must be closed. All not saved changes will be lost during this process. The following document will be closed:\\n\\n",
		'move_exit_open_docs_continue' => 'Continue?', // TRANSLATE
		'move' => "Move selected entries?\\nDo you want to continue?", // TRANSLATE
		'move_ok' => "Files successfully moved!", // TRANSLATE
		'move_duplicate' => "There are files with the same name in the target directory!\\nThe files cannot be moved.", // TRANSLATE
		'move_nofolder' => "The selected files cannot be moved.\\nIt isn't possible to move directories.", // TRANSLATE
		'move_onlysametype' => "The selected objects cannnot be moved.\\nObjects can only be moved in there own classdirectory.", // TRANSLATE
		'move_no_dir' => "Please choose a target directory!", // TRANSLATE
		'document_move_warning' => "After moving documents it is  necessary to do a rebuild.<br />Would you like to do this now?", // TRANSLATE
		'nothing_to_move' => "There is nothing marked to move!", // TRANSLATE
		'move_of_files_failed' => "One or more files couldn't moved! Please move these files manually.\\nThe following files are affected:\\n%s", // TRANSLATE
		'template_save_warning' => "This template is used by %s published documents. Should they be resaved? Attention: This procedure may take some time if you have many documents!", // TRANSLATE
		'template_save_warning1' => "This template is used by one published document. Should it be resaved?", // TRANSLATE
		'template_save_warning2' => "This template is used by other templates and documents, should they be resaved?", // TRANSLATE
		'thumbnail_exists' => "Widok miniatur już istnieje!",
		'thumbnail_not_exists' => "Widok miniatur nie istnieje!",
		'thumbnail_empty' => "You must enter a name for the new thumbnail!", // TRANSLATE
		'doctype_exists' => "Typ dokumentu juź istnieje!",
		'doctype_empty' => "Nie podano nazwy!",
		'delete_cat' => "Czy napewno chcesz usunąć wybraną kategorię?",
		'delete_cat_used' => "Kategoria jest już używana i dlatego nie można jej usunąć!",
		'cat_exists' => "Kategoria już istnieje!",
		'cat_changed' => "Kategoria jest już używana! Jeżeli pokazano ją w dokumentach, należy zapisać te dokumenty na nowo!\\nCzy mimo to zmienić kategorię?",
		'max_name_cat' => "Nazwa kategorii może składać się najwyżej z 32 znaków!",
		'not_entered_cat' => "Nie wprowadzono nazwy kategorii!",
		'cat_new_name' => "Wprowadź nową nazwę kategorii!",
		'we_backup_import_upload_err' => "Wystąpił błąd przy ładowaniu pliku kopii zapasowej! /Maksymalna dozwolona wielkość pliku do załadowania wynosi %s. Jeżeli twój plik kopii zapasowej jest większy, skopiuj go na serwer za pomocą FTP do katalogu webEdition/we_backup a następnie wybierz '" . g_l('backup', "[import_from_server]") . "'!",
		'rebuild_nodocs' => "Brak dokumentów spełniających wymagane kryteria!",
		'we_name_not_allowed' => "Nazwy 'we' oraz 'webEdition' są używane przez sam program webEdition i dlatego nie wolno ich stosować!",
		'we_filename_empty' => "Nie wprowadzono nazwy pliku dla tego dokumentu bądź katalogu!",
		'exit_multi_doc_question' => "Several open documents contain unsaved changes. If you continue all unsaved changes are discarded. Do you want to continue and discard all modifications?", // TRANSLATE
		'exit_doc_question_' . FILE_TABLE => "Dokument został zmieniony.<br>Czy zapisać zmiany?",
		'exit_doc_question_' . TEMPLATES_TABLE => "Szablon został zmieniony.<br>Zapisać zmiany?",
		'deleteTempl_notok_used' => "Nie można wykonać operacji, ponieważ przynajmniej jeden z szablonów, które mają być usunięte, jest używany!",
		'deleteClass_notok_used' => "One or more of the classes are in use and could not be deleted!", // TRANSLATE
		'delete_notok' => "Wystąpił błąd przy usuwaniu!",
		'nothing_to_save' => "Nie można wykonać w tej chwili operacji zapisu!",
		'nothing_to_publish' => "The publish function is disabled at the moment!", // TRANSLATE
		'we_filename_notValid' => "Invalid filename\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)",
		'empty_image_to_save' => "Wybrany obrazek jest pusty.\\nKontynuować mimo to?",
		'path_exists' => "Nie można zapisać dokumentu bądź katalogu %s , ponieważ w tym miejscu znajduje się już inny dokument!",
		'folder_not_empty' => "Ponieważ co najmniej jeden z katalogów do skasowania nie był pusty, nie można było go całkiem usunąć z serwera! Usuń plik ręcznie.\\nDotyczy to następujących katalogów:\\n%s",
		'name_nok' => "Nazwy nie mogą zawierać znaków '<' i '>'!",
		'found_in_workflow' => "Co najmniej jeden z wpisów do skasowania znajduje się w tej chwili w Workflow! Czy chcesz usunąć te wpisy z Workflow?",
		'import_we_dirs' => "Próbujesz importu z katalogu zarządzanego przez webEdition!\\nTe katalogi są chronione i z tego powodu nie można z nich importować!",
		'image/*' => "Nie można dodać pliku. Albo nie jest to obrazek, albo wyczerpało się miejsce na dysku (Webspace)!",
		'application/x-shockwave-flash' => "Nie można dodać pliku. Albo nie jest to animacja Flash-Movie albo wyczerpało się miejsce na dysku twardym!",
		'video/quicktime' => "Nie można dodać pliku. Albo nie jest to plik Quicktime-Movie albo wyczerpało się miejsce na dysku twardym!",
		'text/css' => "The file could not be stored. Either it is not a CSS file or your disk space is exhausted!", // TRANSLATE
		'no_file_selected' => "Nie wybrano plików do załadowania!",
		'browser_crashed' => "Nie można otworzyć okna, ponieważ przeglądarka spowodowała błąd!  Zapisz swoją pracę i uruchom ponownie przeglądarkę.",
		'copy_folders_no_id' => "Należy najpierw zapisać aktualny katalog!",
		'copy_folder_not_valid' => "Nie można skopiować tego samego katalogu lub jednego z katalogów nadrzędnych!",
		'headline' => 'Attention', // TRANSLATE
		'description' => 'Dla tego dokumentu widok nie jest dostępny.',
		'last_document' => 'You edit the last document.', // TRANSLATE
		'first_document' => 'Znajdujesz się przy pierwszym dokumencie.',
		'doc_not_found' => 'Could not find matching document.', // TRANSLATE
		'no_entry' => 'No entry found in history.', // TRANSLATE
		'no_open_document' => 'There is no open document.', // TRANSLATE
		'confirm_delete' => 'Delete this document?', // TRANSLATE
		'no_delete' => 'This document could not be deleted.', // TRANSLATE
		'return_to_start' => 'Plik został skasowany.\\nPowrót do seeMode dokumentu startowego.',
		'return_to_start' => 'The document was moved. \\nBack to seeMode startdocument.', // TRANSLATE
		'no_delete' => 'This document could not be moved', // TRANSLATE
		'cockpit_not_activated' => 'The action could not be performed because the cockpit is not activated.', // TRANSLATE
		'cockpit_reset_settings' => 'Are you sure to delete the current Cockpit settings and reset the default settings?', // TRANSLATE
		'save_error_fields_value_not_valid' => 'The highlighted fields contain invalid data.\\nPlease enter valid data.', // TRANSLATE

		'eplugin_exit_doc' => "The document has been edited with extern editor. The connection between webEdition and extern editor will be closed and the content will not be synchronized anymore.\\nDo you want to close the document?", // TRANSLATE

		'delete_workspace_user' => "The directory %s could not be deleted! It is defined as workspace for the following users or groups:\\n%s", // TRANSLATE
		'delete_workspace_user_r' => "The directory %s could not be deleted! Within the directory there are other directories which are defined as workspace for the following users or groups:\\n%s", // TRANSLATE
		'delete_workspace_object' => "The directory %s could not be deleted! It is defined as workspace for the following objects:\\n%s", // TRANSLATE
		'delete_workspace_object_r' => "The directory %s could not be deleted! Within the directory there are other directories which are defined as workspace in the following objects:\\n%s", // TRANSLATE


		'field_contains_incorrect_chars' => "A field (of the type %s) contains invalid characters.", // TRANSLATE
		'field_input_contains_incorrect_length' => "The maximum length of a field of the type \'Text input\' is 255 characters. If you need more characters, use a field of the type \'Textarea\'.", // TRANSLATE
		'field_int_contains_incorrect_length' => "The maximum length of a field of the type \'Integer\' is 10 characters.", // TRANSLATE
		'field_int_value_to_height' => "The maximum value of a field of the type \'Integer\' is 2147483647.", // TRANSLATE


		'we_filename_notValid' => "Wprowadzona nazwa pliku jest nieprawidłowa!\\nDopuszczalne znaki to litery od a do z (wielkie i małe) , cyfry, znak podkreślenia (_), minus (-) oraz kropka (.).",
		'login_denied_for_user' => "The user cannot login. The user access is disabled.", // TRANSLATE
		'no_perm_to_delete_single_document' => "You have not the needed permissions to delete the active document.", // TRANSLATE

		'confirm' => array(
				'applyWeDocumentCustomerFiltersDocument' => "The document has been moved to a folder with divergent customer account policies. Should the settings of the folder be transmitted to this document?", // TRANSLATE
				'applyWeDocumentCustomerFiltersFolder' => "The directory has been moved to a folder with divergent customers account policies. Should the settings be adopted for this directory and all subelements? ", // TRANSLATE
		),
		'field_in_tab_notvalid_pre' => "The settings could not be saved, because the following fields contain invalid values:", // TRANSLATE
		'field_in_tab_notvalid' => ' - field %s on tab %s', // TRANSLATE
		'field_in_tab_notvalid_post' => 'Correct the fields before saving the settings.', // TRANSLATE
		'discard_changed_data' => 'There are unsaved changes that will be discarded. Are you sure?', // TRANSLATE
);


if (defined("OBJECT_FILES_TABLE")) {
	$l_alert = array_merge($l_alert, array(
			'in_wf_warning' => "Przed dołączeniem obiektu do Workflow, należy go zapisać!\\nCzy zapisać teraz dokument?",
			'in_wf_warning' => "Przed dołączeniem klasy do Workflow, należy ją zapisać!\\nCzy zapisać teraz klasę?",
			'exit_doc_question_' . OBJECT_TABLE => "Klasa została zmieniona.<br>Zapisać zmiany?",
			'exit_doc_question_' . OBJECT_FILES_TABLE => "Obiekt został zmieniony.<br>Zapisać zmiany?",
					));
}
