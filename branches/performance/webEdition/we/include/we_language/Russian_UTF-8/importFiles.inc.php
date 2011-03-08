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
		'destination_dir' => "Директория назначения",
		'file' => "Файл",
		'sameName_expl' => "Определите действия системы webEdition в случае, если имя файла уже существует",
		'sameName_overwrite' => "Переписать существующий файл",
		'sameName_rename' => "Переименовать новый файл",
		'sameName_nothing' => "Не импортировать данный файл",
		'sameName_headline' => "Что делать в случае,<br> если файл уже существует?",
		'step1' => "Импорт локальных файлов - шаг 1 из 2",
		'step2' => "Импорт локальных файлов - шаг 2 из 2",
		'step3' => "Import local files - Step 3 of 3", // TRANSLATE
		'import_expl' => "Нажатием на кнопку, находящуюся рядом с окном ввода, можно выбрать файл на жестком диске. После выбора появляется новое окно ввода, в котором можно выбрать следующий файл. Примите во внимание то, что в связи с ограничениями PHP максимальный размер файла составляет %s.",
		'import_expl_jupload' => "With the click on the button you can select more then one file from your harddrive. Alternatively the files can be selected per 'Drag and Drop' from the file manager.  Please note that the maximum filesize of  %s is not to be exceeded because of restrictions by PHP!<br><br>Click on \"Next\", to start the import.",
		'error' => "Ошибка импортирования!\\n\\nНе импортированы следующие файлы:\\n%s",
		'finished' => "Импорт успешно завершен!",
		'import_file' => "Импорт файла %s",
		'no_perms' => "Ошибка: нет разрешения",
		'move_file_error' => "Ошибка: move_uploaded_file()",
		'read_file_error' => "Ошибка:  fread()",
		'php_error' => "Ошибка: upload_max_filesize()",
		'same_name' => "Ошибка: файл существует",
		'save_error' => "Ошибка при сохранении",
		'publish_error' => "Ошибка при опубликовании",
		'root_dir_1' => "Вы задали корневой каталог веб-сервера как исходную директорию. Вы уверены, что хотите импортировать все содержимое корневого каталога ?",
		'root_dir_2' => "Вы задали корневой каталог веб-сервера как директорию назначения. Вы уверены, что хотите импортировать непосредственно в корневой каталог?",
		'root_dir_3' => "Вы задали корневой каталог веб-сервера как исходную директорию и директорию назначения одновременно. Вы уверены, что хотите импортировать содержимое корневого каталога непосредственно в корневой каталог?",
		'thumbnails' => "Иконки",
		'make_thumbs' => "Создать<br>иконки",
		'image_options_open' => "Показывать функции графики",
		'image_options_close' => "Скрыть функции графики",
		'add_description_nogdlib' => "Для корректной работы функций графики на Вашем сервере должна быть установлена GD Library!",
		'noFiles' => "No files exist in the specified source directory which correspond with the given import settings!", // TRANSLATE
		'emptyDir' => "The source directory is empty!", // TRANSLATE

		'metadata' => "Meta data", // TRANSLATE
		'import_metadata' => "Import meta data from file", // TRANSLATE
);