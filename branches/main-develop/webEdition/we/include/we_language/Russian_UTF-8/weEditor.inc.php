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
		'filename_empty' => "Для этого документа не введено имя!",
		'we_filename_notValid' => "Недействительное имя файла\\nДопустимыми являются большие и малые буквы латинского алфавита, цифры, дефис, нижняя черта и точка (a-z, A-Z, 0-9, _, -, .).",
		'we_filename_notAllowed' => "Введенное имя файла недействительно!",
		'response_save_noperms_to_create_folders' => "Документ не сохранен, так как у Вас нет необходимых полномочий на создание директорий (%s)!",
);
$l_weEditor = array(
		'doubble_field_alert' => "Поле '%s' уже существует! Имя поля не должно повторяться!",
		'variantNameInvalid' => "The name of an article variant can not be empty!", // TRANSLATE

		'folder_save_nok_parent_same' => "Выбранная родительская директория идентична текущей директории! Выберите, пожалуйста, другую директорию и попробуйте еще раз!",
		'pfolder_notsave' => "Данная директория не может быть сохранена в выбранной директории!",
		'required_field_alert' => "Требуется заполнить поле '%s'!",
		'category' => array(
				'response_save_ok' => "Категория '%s' успешно сохранена!",
				'response_save_notok' => "Ошибка при сохранении категория '%s'!",
				'response_path_exists' => "Категория '%s' не могла быть сохранена, так как другая категория занимает это местоположение!",
				'we_filename_notValid' => "Имя недействительно! Символы \\n\", \\' / < > и \\\\ не допускаются!",
				'filename_empty' => "The file name cannot be empty.", // TRANSLATE
				'name_komma' => "Недействительное имя! Запятая недопустима!",
		),
		'text/webedition' => array_merge($l__tmp, array(
				'response_save_ok' => "Страница webEdition '%s' успешно сохранена!",
				'response_publish_ok' => "Страница webEdition '%s' успешно опубликована!",
				'response_publish_notok' => "Ошибка при опубликовании страницы webEdition '%s'!",
				'response_unpublish_ok' => "Страница webEdition '%s' успешно снята с публикации!",
				'response_unpublish_notok' => "Ошибка при снятии с публикации страницы webEdition '%s'!",
				'response_not_published' => "Страница webEdition '%s' не опубликована!",
				'response_save_notok' => "Ошибка при сохранении страницы webEdition '%s'!",
				'response_path_exists' => "Страница webEdition '%s' не могла быть сохранена, так как другой документ или директория занимает это местоположение!",
				'autoschedule' => "Страница webEdition будет автоматически опубликована %s!",
		)),
		'text/html' => array_merge($l__tmp, array(
				'response_save_ok' => "Страница HTML '%s' успешно сохранена!",
				'response_publish_ok' => "Страница HTML '%s' успешно опубликована!",
				'response_publish_notok' => "Ошибка при опубликовании страницы HTML '%s'!",
				'response_unpublish_ok' => "Страница HTML '%s' успешно снята с публикации!",
				'response_unpublish_notok' => "Ошибка при снятии с публикации страницы HTML '%s'!",
				'response_not_published' => "Страница HTML '%s' не опубликована!",
				'response_save_notok' => "Ошибка при сохранении страницы HTML '%s'!",
				'response_path_exists' => "Страница HTML '%s' не сохранена, так как другой документ или директория занимает это местоположение!",
				'autoschedule' => "The HTML page will be published automatically on %s.",
		)),
		'text/weTmpl' => array_merge($l__tmp, array(
				'response_save_ok' => "Шаблон '%s' успешно сохранен!",
				'response_publish_ok' => "Шаблон '%s' успешно опубликован!",
				'response_unpublish_ok' => "Шаблон '%s' успешно снят с публикации!",
				'response_save_notok' => "Ошибка при сохранении шаблона '%s'!",
				'response_path_exists' => "Шаблон '%s' не сохранен, так как другой документ или директория занимает это местоположение!",
				'no_template_save' => "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.",
		)),
		'text/css' => array_merge($l__tmp, array(
				'response_save_ok' => "Таблица стилей '%s' успешно сохранена!",
				'response_publish_ok' => "Таблица стилей '%s' успешно опубликована!",
				'response_unpublish_ok' => "Таблица стилей '%s' успешно снята с публикации!",
				'response_save_notok' => "Ошибка при сохранении стилевого оформления '%s'!",
				'response_path_exists' => "Таблица стилей '%s' не сохранена, так как другой документ или директория занимает это местоположение!",
		)),
		'text/js' => array_merge($l__tmp, array(
				'response_save_ok' => "The JavaScript '%s' has been successfully saved!",
				'response_publish_ok' => "JavaScript '%s' успешно опубликован!",
				'response_unpublish_ok' => "JavaScript '%s' успешно снят с публикации!",
				'response_save_notok' => "Ошибка при сохранении JavaScript '%s'!",
				'response_path_exists' => "JavaScript '%s' не сохранен, так как другой документ или директория занимает это местоположение!",
		)),
		'text/plain' => array_merge($l__tmp, array(
				'response_save_ok' => "The text file '%s' has been successfully saved!",
				'response_publish_ok' => "Текстовый файл '%s' успешно опубликован!",
				'response_unpublish_ok' => "Текстовый файл'%s' успешно снят с публикации!",
				'response_save_notok' => "Ошибка при сохранении текстового файла '%s'!",
				'response_path_exists' => "Текстовый файл '%s' не сохранен, так как другой документ или директория занимает это местоположение!",
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
				'response_publish_ok' => "Директория '%s' успешно опубликована!",
				'response_unpublish_ok' => "Директория '%s' успешно снята с публикации!",
				'response_save_notok' => "Ошибка при сохранении директории '%s'!",
				'response_path_exists' => "Директория '%s' не сохранена, так как другой документ или директория занимает это местоположение!",
				'filename_empty' => "Данной директории не присвоено имя!",
				'we_filename_notValid' => "Недействительное имя папки/директории\\nДопустимыми являются большие и малые буквы латинского алфавита, цифры, дефис, нижняя черта и точка (a-z, A-Z, 0-9, _, -, .).",
				'we_filename_notAllowed' => "Введенное имя директории недействительно!",
				'response_save_noperms_to_create_folders' => "Директория не сохранена, так как у Вас нет необходимых полномочий на создание директорий (%s)!",
		),
		'image/*' => array_merge($l__tmp, array(
				'response_save_ok' => "Графический образ '%s' успешно сохранен!",
				'response_publish_ok' => "Графический образ '%s' успешно опубликован!",
				'response_unpublish_ok' => "Графический образ '%s' успешно снят с публикации!",
				'response_save_notok' => "Ошибка при сохранении графического образа '%s'!",
				'response_path_exists' => "Графический образ '%s' не сохранен, так как другой документ или директория занимает это местоположение!",
		)),
		'application/*' => array_merge($l__tmp, array(
				'response_save_ok' => "The document '%s' has been successfully saved!",
				'response_publish_ok' => "Документ '%s' успешно опубликован!",
				'response_unpublish_ok' => "Документ '%s' успешно снят с публикации!",
				'response_save_notok' => "Ошибка при сохранении  документа '%s'!",
				'response_path_exists' => "Документ '%s' не сохранен, так как другой документ или директория занимает это местоположение!",
				'we_description_missing' => "Please enter a desription in the 'Desription' field!",
				'response_save_wrongExtension' => "Ошибка при сохранении '%s'\\nРасширение '%s' является недопустимым для других файлов.\\nС этой целью нужно открыть новую страницу HTML!",
		)),
		'application/x-shockwave-flash' => array_merge($l__tmp, array(
				'response_save_ok' => "Анимация flashmovie '%s' успешно сохранена!",
				'response_publish_ok' => "Анимация flashmovie '%s' успешно опубликована!",
				'response_unpublish_ok' => "Анимация flashmovie '%s' успешно снята с публикации!",
				'response_save_notok' => "Ошибка при сохранении анимации flashmovie '%s'!",
				'response_path_exists' => "Анимация flashmovie '%s' не сохранена, так как другой документ или директория занимает это местоположение!",
		)),
		'video/quicktime' => array_merge($l__tmp, array(
				'response_save_ok' => "The Quicktime movie '%s' has been successfully saved!",
				'response_publish_ok' => "Фильм quicktime '%s' успешно опубликован!",
				'response_unpublish_ok' => "Фильм quicktime '%s'успешно снят с публикации!",
				'response_save_notok' => "Ошибка при сохранении фильма quicktime '%s'!",
				'response_path_exists' => "Фильм quicktime '%s' не сохранен, так как другой документ или директория занимает это местоположение!",
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
