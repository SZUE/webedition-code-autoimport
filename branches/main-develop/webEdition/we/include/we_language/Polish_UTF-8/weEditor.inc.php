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
 * Language file: we_editor.inc.php
 * Provides language strings.
 * Language: English
 */
$l__tmp = array(
		'filename_empty' => "Nie wprowadzono nazwy pliku!",
		'we_filename_notValid' => "Wprowadzona nazwa pliku jest nieprawidłowa!\\nDozwolone znaki to litery od a do z (wielkie lub małe) , cyfry, znak podkrelenia (_), minus (-) oraz kropka (.).",
		'we_filename_notAllowed' => "Wprowadzona nazwa pliku jest niedozwolona!",
		'response_save_noperms_to_create_folders' => "Nie można było zapisać pliku, ponieważ nie masz wystarczajšcych uprawnień do zakładania nowych katalogów (%s) !",
);
$l_weEditor = array(
		'doubble_field_alert' => "The field '%s' already exists! A field name must be unique!", // TRANSLATE
		'variantNameInvalid' => "The name of an article variant can not be empty!", // TRANSLATE

		'folder_save_nok_parent_same' => "Wybrany katalog nadrzędny leży wewnštrz aktualnego katalogu! Wybierz inny katalog i spróbuj jeszcze raz!",
		'pfolder_notsave' => "Nie można zapisać katalogu w wybranym katalogu!",
		'required_field_alert' => "Pole '%s' jest obowišzkowe i należy je wypełnić!",
		'category' => array(
				'response_save_ok' => "Udało się zapisać kategorię '%s'!",
				'response_save_notok' => "Błšd zapisu kategorii '%s'!",
				'response_path_exists' => "Nie udało się zapisać kategorii '%s', ponieważ w tym miejscu znajduje się już inna kategoria!",
				'we_filename_notValid' => "Podana nazwa jest nieprawidłowa!\\nDopuszczalne sš wszystkie znaki poza \\\", ' / < > i \\\\",
				'filename_empty' => "Nazwa nie może być pusta",
				'name_komma' => "Podana nazwa jest nieprawidłowa!\\nPrzecinki sš niedozwolone",
		),
		'text/webedition' => array_merge($l__tmp, array(
				'response_save_ok' => "Udało się zapisać stronę webEdition '%s'!",
				'response_publish_ok' => "Opublikowano stronę webEdition '%s'!",
				'response_publish_notok' => "Błšd w trakcie publikowania strony webEdition '%s'!",
				'response_unpublish_ok' => "Udało się wycofać stronę webEdition '%s'!",
				'response_unpublish_notok' => "Błšd w trakcie wycofywania strony webEdition '%s'!",
				'response_not_published' => "Nie wycofano strony webEdition '%s'!",
				'response_save_notok' => "Błšd zapisu strony webEdition '%s'!",
				'response_path_exists' => "Nie udało się zapisać strony webEdition '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
				'autoschedule' => "Strona webEdition zostanie automatycznie opublikowana dn. %s !",
		)),
		'text/html' => array_merge($l__tmp, array(
				'response_save_ok' => "Udało się zapisać stronę HTML '%s'!",
				'response_publish_ok' => "Udało się opublikować stronę HTML '%s'!",
				'response_publish_notok' => "Błšd w trakcie publikowania strony HTML'%s'!",
				'response_unpublish_ok' => "Udało się wycofać stronę HTML '%s'!",
				'response_unpublish_notok' => "Błšd w trakcie wycofywania strony HTML '%s'!",
				'response_not_published' => "Nie opublikowano strony HTML '%s'!",
				'response_save_notok' => "Błšd zapisu strony HTML '%s'!",
				'response_path_exists' => "Nie udało się zapisać strony '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
		)),
		'text/weTmpl' => array_merge($l__tmp, array(
				'response_save_ok' => "Udało się zapisać szablon '%s'!",
				'response_publish_ok' => "Udało się opublikować szablon '%s'!",
				'response_unpublish_ok' => "Udało się wycofać szablon '%s'!",
				'response_save_notok' => "Błšd zapisu szablonu '%s'!",
				'response_path_exists' => "Nie udało się zapisać szablonu '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
				'no_template_save' => "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.",
		)),
		'text/css' => array_merge($l__tmp, array(
				'response_save_ok' => "Udało się zapisać arkusz stylu CSS '%s'!",
				'response_publish_ok' => "Udało się opublikować arkusz stylu CSS '%s' !",
				'response_unpublish_ok' => "Udało się wycofać arkusz stylu '%s'!",
				'response_save_notok' => "Błšd zapisu arkusza stylu CSS '%s'!",
				'response_path_exists' => "Nie udało się zapisać arkusza stylu CSS '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
		)),
		'text/js' => array_merge($l__tmp, array(
				'response_save_ok' => "The JavaScript '%s' has been successfully saved!",
				'response_publish_ok' => "Udało się opublikować plik Javascript '%s'!",
				'response_unpublish_ok' => "Udało się wycofać plik Javascript '%s'!",
				'response_save_notok' => "Błšd zapisu pliku Javascripts '%s'!",
				'response_path_exists' => "Nie udało się zapisać pliku Javascript '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
		)),
		'text/plain' => array_merge($l__tmp, array(
				'response_save_ok' => "The text file '%s' has been successfully saved!",
				'response_publish_ok' => "Udało się opublikować pliku tekstowego '%s'!",
				'response_unpublish_ok' => "Udało się wycofać plik tekstowy '%s'!",
				'response_save_notok' => "Błšd zapisu pliku tekstowego '%s'!",
				'response_path_exists' => "Nie udało się zapisać pliku tekstowego '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
		)),
		'text/htaccess' => array_merge($l__tmp, array(
				'response_save_ok' => "The file '%s' has been successfully saved!", //TRANSLATE
				'response_publish_ok' => "The file '%s' has been successfully published!", //TRANSLATE
				'response_unpublish_ok' => "The file '%s' has been successfully unpublished!", //TRANSLATE
				'response_save_notok' => "Error while saving the file '%s'!", //TRANSLATE
				'response_path_exists' => "The file '%s' could not be saved because another document or directory is positioned at the same location!", //TRANSLATE
		)),
		'text/xml' => array_merge($l__tmp, array(
				'response_save_ok' => "The XML file '%s' has been successfully saved!",
				'response_publish_ok' => "The XML file '%s' has been successfully published!", // TRANSLATE
				'response_unpublish_ok' => "The XML file '%s' has been successfully unpublished!", // TRANSLATE
				'response_save_notok' => "Error while saving XML file '%s'!", // TRANSLATE
				'response_path_exists' => "The XML file '%s' could not be saved because another document or directory is positioned at the same location!", // TRANSLATE
		)),
		'folder' => array(
				'response_save_ok' => "The directory '%s' has been successfully saved!",
				'response_publish_ok' => "Udało się opublikować katalog '%s'!",
				'response_unpublish_ok' => "Udało się wycofać katalog '%s'!",
				'response_save_notok' => "Błšd zapisu katalogu '%s'!",
				'response_path_exists' => "Nie udało się zapisać katalogu '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
				'filename_empty' => "Nie wprowadzono jeszcze nazwy dla katalogu!",
				'we_filename_notValid' => "Wprowadzona nazwa katalogu jest nieprawidłowa!\\nDozwolone znaki to litery od a do z (wielkie lub małe) , cyfry, znak podkrelenia (_), minus (-) oraz kropka (.).",
				'we_filename_notAllowed' => "Wprowadzona nazwa katalogu jest niedozwolona!",
				'response_save_noperms_to_create_folders' => "Nie można było zapisać katalogu, ponieważ nie posiadasz wystarczajšcych uprawnień do zakładania nowych katalogów (%s)!",
		),
		'image/*' => array_merge($l__tmp, array(
				'response_save_ok' => "Udało się zapisać obrazek '%s'!",
				'response_publish_ok' => "Udało się opublikować obrazek '%s'",
				'response_unpublish_ok' => "Udało się wycofać obrazek '%s'",
				'response_save_notok' => "Błšd zapisu obrazka '%s'!",
				'response_path_exists' => "Nie udało się zapisać obrazka '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
		)),
		'application/*' => array_merge($l__tmp, array(
				'response_save_ok' => "The document '%s' has been successfully saved!",
				'response_publish_ok' => "Udało się opublikować plik '%s'!",
				'response_unpublish_ok' => "Udało się wycofać plik '%s'!",
				'response_save_notok' => "Błšd zapisu pliku '%s'!",
				'response_path_exists' => "Nie udało się zapisać pliku '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
				'we_description_missing' => "Please enter a desription in the 'Desription' field!",
				'response_save_wrongExtension' => "Błšd zapisu '%s' \\nRozszerzenie nazwy pliku '%s' jest niedozwolonoe dla innych plików!\\nWprowad w tym celu stronę HTML!",
		)),
		'application/x-shockwave-flash' => array_merge($l__tmp, array(
				'response_save_ok' => "Udało się zapisać animację Flash '%s'!",
				'response_publish_ok' => "Udało się opublikować animację Flash '%s'!",
				'response_unpublish_ok' => "Udało się wycofać animację Flashe '%s'!",
				'response_save_notok' => "Błšd zapisu animacji Flash '%s'!",
				'response_path_exists' => "Nie udało się zapisać animacji Flash '%s' '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
		)),
		'video/quicktime' => array_merge($l__tmp, array(
				'response_save_ok' => "The Quicktime movie '%s' has been successfully saved!",
				'response_publish_ok' => "Udało się opublikować film Quicktime '%s'!",
				'response_unpublish_ok' => "Udało się wycofać film Quicktime '%s'!",
				'response_save_notok' => "Błšd zapisu filmu Quicktime '%s'!",
				'response_path_exists' => "Nie udało się zapisać filmu Quicktime '%s', ponieważ w tym miejscu znajduje się już plik lub katalog!",
		)),
);

/* * ***************************************************************************
 * PLEASE DON'T TOUCH THE NEXT LINES
 * UNLESS YOU KNOW EXACTLY WHAT YOU ARE DOING!
 * *************************************************************************** */

$_language_directory = dirname(__FILE__) . "/modules";
$_directory = dir($_language_directory);

while (false !== ($entry = $_directory->read())) {
	if (strstr($entry, '_we_editor')) {
		include($_language_directory . "/" . $entry);
	}
}
