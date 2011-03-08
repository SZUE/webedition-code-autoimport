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
 * Language file: import_files.inc.php
 * Provides language strings.
 * Language: English
 */
$l_importFiles = array(
		'destination_dir' => "Destination directory", // TRANSLATE
		'file' => "Plik",
		'sameName_expl' => "Określ zachowanie webEdition w przypadku wystąpienia takiej samej nazwy.",
		'sameName_overwrite' => "Istniejący plik nadpisać",
		'sameName_rename' => "Zapisać pod inna nazwą",
		'sameName_nothing' => "Nie importuj pliku",
		'sameName_headline' => "Co zrobić<br>przy takiej samej nazwie?",
		'step1' => "Importownie lokalnych plików - krok 1 z 2",
		'step2' => "Importownie lokalnych plików - krok 2 z 2",
		'step3' => "Import local files - Step 3 of 3", // TRANSLATE
		'import_expl' => "Poprzez kliknięcie przycisku obok pola wprowadzenia można wybrać plik z dysku. Po wyborze jednego ukarze się kolejne okno w którym można wybrać następne pliki. Należy uważać, aby maksymalna wielkość pliku nie przekroczyła %s!<br><br>Kliknij na \"Dalej\", aby rozpocząć importowanie.",
		'import_expl_jupload' => "With the click on the button you can select more then one file from your harddrive. Alternatively the files can be selected per 'Drag and Drop' from the file manager.  Please note that the maximum filesize of  %s is not to be exceeded because of restrictions by PHP!<br><br>Click on \"Next\", to start the import.",
		'error' => "Wystąpił błąd podczas importu!\\n\\nNastępujący plik nie mógł zostać zaimportowany:\\n%s",
		'finished' => "Pomyślnie zakończono importowanie pliku!",
		'import_file' => "Importuj plik %s",
		'no_perms' => "Błąd: Brak uprawnień",
		'move_file_error' => "Błąd: move_uploaded_file()",
		'read_file_error' => "Błąd: fread()",
		'php_error' => "Błąd: upload_max_filesize()",
		'same_name' => "Błąd: Plik już istnieje",
		'save_error' => "Błąd przy zapisie",
		'publish_error' => "Błąd przy publikowaniu",
		'root_dir_1' => "Jako folder źródłowy podałeś katalog Root serwera webowego. Jesteś pewien, że chcesz zaimportować całą zawartość katalogu Root?",
		'root_dir_2' => "Jako folder docelowy podałeś katalog Root serwera webowego. Jesteś pewien, że chcesz zaimportować wszystko bezpośrednio do katalogu Root?",
		'root_dir_3' => "Jako folder źródłowy i docelowy podałeś katalog Root. Jesteś pewien, że chcesz zaimportować całą zawartość katalogu Root z powrotem do katalogu Root?",
		'thumbnails' => "Widok miniatury",
		'make_thumbs' => "Utwórz<br>miniaturę",
		'image_options_open' => "Wyświetl funkcje grafiki",
		'image_options_close' => "Wyłącz funkcje grafiki",
		'add_description_nogdlib' => "Aby działały funkcje grafiki musi zostać zainstalowana GD Library na serwerze!",
		'noFiles' => "No files exist in the specified source directory which correspond with the given import settings!", // TRANSLATE
		'emptyDir' => "The source directory is empty!", // TRANSLATE

		'metadata' => "Meta data", // TRANSLATE
		'import_metadata' => "Import meta data from file", // TRANSLATE
);