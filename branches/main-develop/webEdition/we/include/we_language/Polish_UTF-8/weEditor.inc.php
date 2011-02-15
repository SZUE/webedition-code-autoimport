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
 * Language file: we_editor.inc.php
 * Provides language strings.
 * Language: English
 */
$l_weEditor["doubble_field_alert"] = "The field '%s' already exists! A field name must be unique!"; // TRANSLATE
$l_weEditor["variantNameInvalid"] = "The name of an article variant can not be empty!"; // TRANSLATE

$l_weEditor["folder_save_nok_parent_same"] = "Wybrany katalog nadrzędny leży wewnštrz aktualnego katalogu! Wybierz inny katalog i spróbuj jeszcze raz!";
$l_weEditor["pfolder_notsave"] = "Nie można zapisać katalogu w wybranym katalogu!";
$l_weEditor["required_field_alert"] = "Pole '%s' jest obowišzkowe i należy je wypełnić!";

$l_weEditor["category"]["response_save_ok"] = "Udało się zapisać kategorię '%s'!";
$l_weEditor["category"]["response_save_notok"] = "Błšd zapisu kategorii '%s'!";
$l_weEditor["category"]["response_path_exists"] = "Nie udało się zapisać kategorii '%s', ponieważ w tym miejscu znajduje się już inna kategoria!";
$l_weEditor["category"]["we_filename_notValid"] = "Podana nazwa jest nieprawidłowa!\\nDopuszczalne sš wszystkie znaki poza \\\", ' / < > i \\\\";
$l_weEditor["category"]["filename_empty"]       = "Nazwa nie może być pusta";
$l_weEditor["category"]["name_komma"] = "Podana nazwa jest nieprawidłowa!\\nPrzecinki sš niedozwolone";

$l_weEditor["text/webedition"]["response_save_ok"] = "Udało się zapisać stronę webEdition '%s'!";
$l_weEditor["text/webedition"]["response_publish_ok"] = "Opublikowano stronę webEdition '%s'!";
$l_weEditor["text/webedition"]["response_publish_notok"] = "Błšd w trakcie publikowania strony webEdition '%s'!";
$l_weEditor["text/webedition"]["response_unpublish_ok"] = "Udało się wycofać stronę webEdition '%s'!";
$l_weEditor["text/webedition"]["response_unpublish_notok"] = "Błšd w trakcie wycofywania strony webEdition '%s'!";
$l_weEditor["text/webedition"]["response_not_published"] = "Nie wycofano strony webEdition '%s'!";
$l_weEditor["text/webedition"]["response_save_notok"] = "Błšd zapisu strony webEdition '%s'!";
$l_weEditor["text/webedition"]["response_path_exists"] = "Nie udało się zapisać strony webEdition '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["text/webedition"]["filename_empty"] = "Nie wprowadzono nazwy pliku!";
$l_weEditor["text/webedition"]["we_filename_notValid"] = "Wprowadzona nazwa pliku jest nieprawidłowa!\\nDozwolone znaki to litery od a do z (wielkie lub małe) , cyfry, znak podkrelenia (_), minus (-) oraz kropka (.).";
$l_weEditor["text/webedition"]["we_filename_notAllowed"] = "Wprowadzona nazwa pliku jest niedozwolona!";
$l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"] = "Nie można było zapisać pliku, ponieważ nie masz wystarczajšcych uprawnień do zakładania nowych katalogów (%s) !";
$l_weEditor["text/webedition"]["autoschedule"] = "Strona webEdition zostanie automatycznie opublikowana dn. %s !";

$l_weEditor["text/html"]["response_save_ok"] = "Udało się zapisać stronę HTML '%s'!";
$l_weEditor["text/html"]["response_publish_ok"] = "Udało się opublikować stronę HTML '%s'!";
$l_weEditor["text/html"]["response_publish_notok"] = "Błšd w trakcie publikowania strony HTML'%s'!";
$l_weEditor["text/html"]["response_unpublish_ok"] = "Udało się wycofać stronę HTML '%s'!";
$l_weEditor["text/html"]["response_unpublish_notok"] = "Błšd w trakcie wycofywania strony HTML '%s'!";
$l_weEditor["text/html"]["response_not_published"] = "Nie opublikowano strony HTML '%s'!";
$l_weEditor["text/html"]["response_save_notok"] = "Błšd zapisu strony HTML '%s'!";
$l_weEditor["text/html"]["response_path_exists"] = "Nie udało się zapisać strony '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["text/html"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/html"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/html"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/html"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/html"]["autoschedule"] = "The HTML page will be published automatically on %s.";

$l_weEditor["text/weTmpl"]["response_save_ok"] = "Udało się zapisać szablon '%s'!";
$l_weEditor["text/weTmpl"]["response_publish_ok"] = "Udało się opublikować szablon '%s'!";
$l_weEditor["text/weTmpl"]["response_unpublish_ok"] = "Udało się wycofać szablon '%s'!";
$l_weEditor["text/weTmpl"]["response_save_notok"] = "Błšd zapisu szablonu '%s'!";
$l_weEditor["text/weTmpl"]["response_path_exists"] = "Nie udało się zapisać szablonu '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["text/weTmpl"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/weTmpl"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/weTmpl"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/weTmpl"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/weTmpl"]["no_template_save"] = "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.";

$l_weEditor["text/css"]["response_save_ok"] = "Udało się zapisać arkusz stylu CSS '%s'!";
$l_weEditor["text/css"]["response_publish_ok"] = "Udało się opublikować arkusz stylu CSS '%s' !";
$l_weEditor["text/css"]["response_unpublish_ok"] = "Udało się wycofać arkusz stylu '%s'!";
$l_weEditor["text/css"]["response_save_notok"] = "Błšd zapisu arkusza stylu CSS '%s'!";
$l_weEditor["text/css"]["response_path_exists"] = "Nie udało się zapisać arkusza stylu CSS '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["text/css"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/css"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/css"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/css"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/js"]["response_save_ok"] = "The JavaScript '%s' has been successfully saved!";
$l_weEditor["text/js"]["response_publish_ok"] = "Udało się opublikować plik Javascript '%s'!";
$l_weEditor["text/js"]["response_unpublish_ok"] = "Udało się wycofać plik Javascript '%s'!";
$l_weEditor["text/js"]["response_save_notok"] = "Błšd zapisu pliku Javascripts '%s'!";
$l_weEditor["text/js"]["response_path_exists"] = "Nie udało się zapisać pliku Javascript '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["text/js"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/js"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/js"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/js"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/plain"]["response_save_ok"] = "The text file '%s' has been successfully saved!";
$l_weEditor["text/plain"]["response_publish_ok"] = "Udało się opublikować pliku tekstowego '%s'!";
$l_weEditor["text/plain"]["response_unpublish_ok"] = "Udało się wycofać plik tekstowy '%s'!";
$l_weEditor["text/plain"]["response_save_notok"] = "Błšd zapisu pliku tekstowego '%s'!";
$l_weEditor["text/plain"]["response_path_exists"] = "Nie udało się zapisać pliku tekstowego '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["text/plain"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/plain"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/plain"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/plain"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/htaccess"]["response_save_ok"] = "The file '%s' has been successfully saved!"; //TRANSLATE
$l_weEditor["text/htaccess"]["response_publish_ok"] = "The file '%s' has been successfully published!"; //TRANSLATE
$l_weEditor["text/htaccess"]["response_unpublish_ok"] = "The file '%s' has been successfully unpublished!"; //TRANSLATE
$l_weEditor["text/htaccess"]["response_save_notok"] = "Error while saving the file '%s'!"; //TRANSLATE
$l_weEditor["text/htaccess"]["response_path_exists"] = "The file '%s' could not be saved because another document or directory is positioned at the same location!"; //TRANSLATE
$l_weEditor["text/htaccess"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/htaccess"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/htaccess"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/htaccess"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/xml"]["response_save_ok"] = "The XML file '%s' has been successfully saved!";
$l_weEditor["text/xml"]["response_publish_ok"] = "The XML file '%s' has been successfully published!"; // TRANSLATE
$l_weEditor["text/xml"]["response_unpublish_ok"] = "The XML file '%s' has been successfully unpublished!"; // TRANSLATE
$l_weEditor["text/xml"]["response_save_notok"] = "Error while saving XML file '%s'!"; // TRANSLATE
$l_weEditor["text/xml"]["response_path_exists"] = "The XML file '%s' could not be saved because another document or directory is positioned at the same location!"; // TRANSLATE
$l_weEditor["text/xml"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/xml"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/xml"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/xml"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["folder"]["response_save_ok"] = "The directory '%s' has been successfully saved!";
$l_weEditor["folder"]["response_publish_ok"] = "Udało się opublikować katalog '%s'!";
$l_weEditor["folder"]["response_unpublish_ok"] = "Udało się wycofać katalog '%s'!";
$l_weEditor["folder"]["response_save_notok"] = "Błšd zapisu katalogu '%s'!";
$l_weEditor["folder"]["response_path_exists"] = "Nie udało się zapisać katalogu '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["folder"]["filename_empty"] = "Nie wprowadzono jeszcze nazwy dla katalogu!";
$l_weEditor["folder"]["we_filename_notValid"] = "Wprowadzona nazwa katalogu jest nieprawidłowa!\\nDozwolone znaki to litery od a do z (wielkie lub małe) , cyfry, znak podkrelenia (_), minus (-) oraz kropka (.).";
$l_weEditor["folder"]["we_filename_notAllowed"] = "Wprowadzona nazwa katalogu jest niedozwolona!";
$l_weEditor["folder"]["response_save_noperms_to_create_folders"] = "Nie można było zapisać katalogu, ponieważ nie posiadasz wystarczajšcych uprawnień do zakładania nowych katalogów (%s)!";

$l_weEditor["image/*"]["response_save_ok"] = "Udało się zapisać obrazek '%s'!";
$l_weEditor["image/*"]["response_publish_ok"] = "Udało się opublikować obrazek '%s'";
$l_weEditor["image/*"]["response_unpublish_ok"] = "Udało się wycofać obrazek '%s'";
$l_weEditor["image/*"]["response_save_notok"] = "Błšd zapisu obrazka '%s'!";
$l_weEditor["image/*"]["response_path_exists"] = "Nie udało się zapisać obrazka '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["image/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["image/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["image/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["image/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["application/*"]["response_save_ok"] = "The document '%s' has been successfully saved!";
$l_weEditor["application/*"]["response_publish_ok"] = "Udało się opublikować plik '%s'!";
$l_weEditor["application/*"]["response_unpublish_ok"] = "Udało się wycofać plik '%s'!";
$l_weEditor["application/*"]["response_save_notok"] = "Błšd zapisu pliku '%s'!";
$l_weEditor["application/*"]["response_path_exists"] = "Nie udało się zapisać pliku '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["application/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["application/*"]["we_description_missing"] = "Please enter a desription in the 'Desription' field!";
$l_weEditor["application/*"]["response_save_wrongExtension"] =  "Błšd zapisu '%s' \\nRozszerzenie nazwy pliku '%s' jest niedozwolonoe dla innych plików!\\nWprowad w tym celu stronę HTML!";

$l_weEditor["application/x-shockwave-flash"]["response_save_ok"] = "Udało się zapisać animację Flash '%s'!";
$l_weEditor["application/x-shockwave-flash"]["response_publish_ok"] = "Udało się opublikować animację Flash '%s'!";
$l_weEditor["application/x-shockwave-flash"]["response_unpublish_ok"] = "Udało się wycofać animację Flashe '%s'!";
$l_weEditor["application/x-shockwave-flash"]["response_save_notok"] = "Błšd zapisu animacji Flash '%s'!";
$l_weEditor["application/x-shockwave-flash"]["response_path_exists"] = "Nie udało się zapisać animacji Flash '%s' '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["application/x-shockwave-flash"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/x-shockwave-flash"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["video/quicktime"]["response_save_ok"] = "The Quicktime movie '%s' has been successfully saved!";
$l_weEditor["video/quicktime"]["response_publish_ok"] = "Udało się opublikować film Quicktime '%s'!";
$l_weEditor["video/quicktime"]["response_unpublish_ok"] = "Udało się wycofać film Quicktime '%s'!";
$l_weEditor["video/quicktime"]["response_save_notok"] = "Błšd zapisu filmu Quicktime '%s'!";
$l_weEditor["video/quicktime"]["response_path_exists"] = "Nie udało się zapisać filmu Quicktime '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!";
$l_weEditor["video/quicktime"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["video/quicktime"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["video/quicktime"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["video/quicktime"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

/*****************************************************************************
 * PLEASE DON'T TOUCH THE NEXT LINES
 * UNLESS YOU KNOW EXACTLY WHAT YOU ARE DOING!
 *****************************************************************************/

$_language_directory = $_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/modules";
$_directory = dir($_language_directory);

while (false !== ($entry = $_directory->read())) {
	if (strstr($entry, '_we_editor')) {
		include($_language_directory."/".$entry);
	}
}
