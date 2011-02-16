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
$l_weEditor["doubble_field_alert"] = "Поле '%s' уже существует! Имя поля не должно повторяться!";
$l_weEditor["variantNameInvalid"] = "The name of an article variant can not be empty!"; // TRANSLATE

$l_weEditor["folder_save_nok_parent_same"] = "Выбранная родительская директория идентична текущей директории! Выберите, пожалуйста, другую директорию и попробуйте еще раз!";
$l_weEditor["pfolder_notsave"] = "Данная директория не может быть сохранена в выбранной директории!";
$l_weEditor["required_field_alert"] = "Требуется заполнить поле '%s'!";

$l_weEditor["category"]["response_save_ok"] = "Категория '%s' успешно сохранена!";
$l_weEditor["category"]["response_save_notok"] = "Ошибка при сохранении категория '%s'!";
$l_weEditor["category"]["response_path_exists"] = "Категория '%s' не могла быть сохранена, так как другая категория занимает это местоположение!";
$l_weEditor["category"]["we_filename_notValid"] = "Имя недействительно! Символы \\n\", \\' / < > и \\\\ не допускаются!";
$l_weEditor["category"]["filename_empty"]       = "The file name cannot be empty."; // TRANSLATE
$l_weEditor["category"]["name_komma"] = "Недействительное имя! Запятая недопустима!";

$l_weEditor["text/webedition"]["response_save_ok"] = "Страница webEdition '%s' успешно сохранена!";
$l_weEditor["text/webedition"]["response_publish_ok"] = "Страница webEdition '%s' успешно опубликована!";
$l_weEditor["text/webedition"]["response_publish_notok"] = "Ошибка при опубликовании страницы webEdition '%s'!";
$l_weEditor["text/webedition"]["response_unpublish_ok"] = "Страница webEdition '%s' успешно снята с публикации!";
$l_weEditor["text/webedition"]["response_unpublish_notok"] = "Ошибка при снятии с публикации страницы webEdition '%s'!";
$l_weEditor["text/webedition"]["response_not_published"] = "Страница webEdition '%s' не опубликована!";
$l_weEditor["text/webedition"]["response_save_notok"] = "Ошибка при сохранении страницы webEdition '%s'!";
$l_weEditor["text/webedition"]["response_path_exists"] = "Страница webEdition '%s' не могла быть сохранена, так как другой документ или директория занимает это местоположение!";
$l_weEditor["text/webedition"]["filename_empty"] = "Для этого документа не введено имя!";
$l_weEditor["text/webedition"]["we_filename_notValid"] = "Недействительное имя файла\\nДопустимыми являются большие и малые буквы латинского алфавита, цифры, дефис, нижняя черта и точка (a-z, A-Z, 0-9, _, -, .).";
$l_weEditor["text/webedition"]["we_filename_notAllowed"] = "Введенное имя файла недействительно!";
$l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"] = "Документ не сохранен, так как у Вас нет необходимых полномочий на создание директорий (%s)!";
$l_weEditor["text/webedition"]["autoschedule"] = "Страница webEdition будет автоматически опубликована %s!";

$l_weEditor["text/html"]["response_save_ok"] = "Страница HTML '%s' успешно сохранена!";
$l_weEditor["text/html"]["response_publish_ok"] = "Страница HTML '%s' успешно опубликована!";
$l_weEditor["text/html"]["response_publish_notok"] = "Ошибка при опубликовании страницы HTML '%s'!";
$l_weEditor["text/html"]["response_unpublish_ok"] = "Страница HTML '%s' успешно снята с публикации!";
$l_weEditor["text/html"]["response_unpublish_notok"] = "Ошибка при снятии с публикации страницы HTML '%s'!";
$l_weEditor["text/html"]["response_not_published"] = "Страница HTML '%s' не опубликована!";
$l_weEditor["text/html"]["response_save_notok"] = "Ошибка при сохранении страницы HTML '%s'!";
$l_weEditor["text/html"]["response_path_exists"] = "Страница HTML '%s' не сохранена, так как другой документ или директория занимает это местоположение!";
$l_weEditor["text/html"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/html"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/html"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/html"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/html"]["autoschedule"] = "The HTML page will be published automatically on %s.";

$l_weEditor["text/weTmpl"]["response_save_ok"] = "Шаблон '%s' успешно сохранен!";
$l_weEditor["text/weTmpl"]["response_publish_ok"] = "Шаблон '%s' успешно опубликован!";
$l_weEditor["text/weTmpl"]["response_unpublish_ok"] = "Шаблон '%s' успешно снят с публикации!";
$l_weEditor["text/weTmpl"]["response_save_notok"] = "Ошибка при сохранении шаблона '%s'!";
$l_weEditor["text/weTmpl"]["response_path_exists"] = "Шаблон '%s' не сохранен, так как другой документ или директория занимает это местоположение!";
$l_weEditor["text/weTmpl"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/weTmpl"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/weTmpl"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/weTmpl"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["text/weTmpl"]["no_template_save"] = "Templates " . "can " . "not " . "saved " . "in the " . "de" . "mo" . " of" . " webEdition.";

$l_weEditor["text/css"]["response_save_ok"] = "Таблица стилей '%s' успешно сохранена!";
$l_weEditor["text/css"]["response_publish_ok"] = "Таблица стилей '%s' успешно опубликована!";
$l_weEditor["text/css"]["response_unpublish_ok"] = "Таблица стилей '%s' успешно снята с публикации!";
$l_weEditor["text/css"]["response_save_notok"] = "Ошибка при сохранении стилевого оформления '%s'!";
$l_weEditor["text/css"]["response_path_exists"] = "Таблица стилей '%s' не сохранена, так как другой документ или директория занимает это местоположение!";
$l_weEditor["text/css"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/css"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/css"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/css"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/js"]["response_save_ok"] = "The JavaScript '%s' has been successfully saved!";
$l_weEditor["text/js"]["response_publish_ok"] = "JavaScript '%s' успешно опубликован!";
$l_weEditor["text/js"]["response_unpublish_ok"] = "JavaScript '%s' успешно снят с публикации!";
$l_weEditor["text/js"]["response_save_notok"] = "Ошибка при сохранении JavaScript '%s'!";
$l_weEditor["text/js"]["response_path_exists"] = "JavaScript '%s' не сохранен, так как другой документ или директория занимает это местоположение!";
$l_weEditor["text/js"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["text/js"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["text/js"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["text/js"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["text/plain"]["response_save_ok"] = "The text file '%s' has been successfully saved!";
$l_weEditor["text/plain"]["response_publish_ok"] = "Текстовый файл '%s' успешно опубликован!";
$l_weEditor["text/plain"]["response_unpublish_ok"] = "Текстовый файл'%s' успешно снят с публикации!";
$l_weEditor["text/plain"]["response_save_notok"] = "Ошибка при сохранении текстового файла '%s'!";
$l_weEditor["text/plain"]["response_path_exists"] = "Текстовый файл '%s' не сохранен, так как другой документ или директория занимает это местоположение!";
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
$l_weEditor["folder"]["response_publish_ok"] = "Директория '%s' успешно опубликована!";
$l_weEditor["folder"]["response_unpublish_ok"] = "Директория '%s' успешно снята с публикации!";
$l_weEditor["folder"]["response_save_notok"] = "Ошибка при сохранении директории '%s'!";
$l_weEditor["folder"]["response_path_exists"] = "Директория '%s' не сохранена, так как другой документ или директория занимает это местоположение!";
$l_weEditor["folder"]["filename_empty"] = "Данной директории не присвоено имя!";
$l_weEditor["folder"]["we_filename_notValid"] = "Недействительное имя папки/директории\\nДопустимыми являются большие и малые буквы латинского алфавита, цифры, дефис, нижняя черта и точка (a-z, A-Z, 0-9, _, -, .).";
$l_weEditor["folder"]["we_filename_notAllowed"] = "Введенное имя директории недействительно!";
$l_weEditor["folder"]["response_save_noperms_to_create_folders"] = "Директория не сохранена, так как у Вас нет необходимых полномочий на создание директорий (%s)!";

$l_weEditor["image/*"]["response_save_ok"] = "Графический образ '%s' успешно сохранен!";
$l_weEditor["image/*"]["response_publish_ok"] = "Графический образ '%s' успешно опубликован!";
$l_weEditor["image/*"]["response_unpublish_ok"] = "Графический образ '%s' успешно снят с публикации!";
$l_weEditor["image/*"]["response_save_notok"] = "Ошибка при сохранении графического образа '%s'!";
$l_weEditor["image/*"]["response_path_exists"] = "Графический образ '%s' не сохранен, так как другой документ или директория занимает это местоположение!";
$l_weEditor["image/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["image/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["image/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["image/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["application/*"]["response_save_ok"] = "The document '%s' has been successfully saved!";
$l_weEditor["application/*"]["response_publish_ok"] = "Документ '%s' успешно опубликован!";
$l_weEditor["application/*"]["response_unpublish_ok"] = "Документ '%s' успешно снят с публикации!";
$l_weEditor["application/*"]["response_save_notok"] = "Ошибка при сохранении  документа '%s'!";
$l_weEditor["application/*"]["response_path_exists"] = "Документ '%s' не сохранен, так как другой документ или директория занимает это местоположение!";
$l_weEditor["application/*"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/*"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/*"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/*"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];
$l_weEditor["application/*"]["we_description_missing"] = "Please enter a desription in the 'Desription' field!";
$l_weEditor["application/*"]["response_save_wrongExtension"] =  "Ошибка при сохранении '%s'\\nРасширение '%s' является недопустимым для других файлов.\\nС этой целью нужно открыть новую страницу HTML!";

$l_weEditor["application/x-shockwave-flash"]["response_save_ok"] = "Анимация flashmovie '%s' успешно сохранена!";
$l_weEditor["application/x-shockwave-flash"]["response_publish_ok"] = "Анимация flashmovie '%s' успешно опубликована!";
$l_weEditor["application/x-shockwave-flash"]["response_unpublish_ok"] = "Анимация flashmovie '%s' успешно снята с публикации!";
$l_weEditor["application/x-shockwave-flash"]["response_save_notok"] = "Ошибка при сохранении анимации flashmovie '%s'!";
$l_weEditor["application/x-shockwave-flash"]["response_path_exists"] = "Анимация flashmovie '%s' не сохранена, так как другой документ или директория занимает это местоположение!";
$l_weEditor["application/x-shockwave-flash"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["application/x-shockwave-flash"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["application/x-shockwave-flash"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

$l_weEditor["video/quicktime"]["response_save_ok"] = "The Quicktime movie '%s' has been successfully saved!";
$l_weEditor["video/quicktime"]["response_publish_ok"] = "Фильм quicktime '%s' успешно опубликован!";
$l_weEditor["video/quicktime"]["response_unpublish_ok"] = "Фильм quicktime '%s'успешно снят с публикации!";
$l_weEditor["video/quicktime"]["response_save_notok"] = "Ошибка при сохранении фильма quicktime '%s'!";
$l_weEditor["video/quicktime"]["response_path_exists"] = "Фильм quicktime '%s' не сохранен, так как другой документ или директория занимает это местоположение!";
$l_weEditor["video/quicktime"]["filename_empty"] = $l_weEditor["text/webedition"]["filename_empty"];
$l_weEditor["video/quicktime"]["we_filename_notValid"] = $l_weEditor["text/webedition"]["we_filename_notValid"];
$l_weEditor["video/quicktime"]["we_filename_notAllowed"] = $l_weEditor["text/webedition"]["we_filename_notAllowed"];
$l_weEditor["video/quicktime"]["response_save_noperms_to_create_folders"] = $l_weEditor["text/webedition"]["response_save_noperms_to_create_folders"];

/*****************************************************************************
 * PLEASE DON'T TOUCH THE NEXT LINES
 * UNLESS YOU KNOW EXACTLY WHAT YOU ARE DOING!
 *****************************************************************************/

$_language_directory = dirname(__FILE__)."/modules";
$_directory = dir($_language_directory);

while (false !== ($entry = $_directory->read())) {
	if (strstr($entry, '_we_editor')) {
		include($_language_directory."/".$entry);
	}
}
