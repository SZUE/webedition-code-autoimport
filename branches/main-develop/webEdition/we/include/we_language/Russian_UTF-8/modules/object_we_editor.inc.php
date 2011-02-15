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
 * Language file: object_we_editor.inc.php
 * Provides language strings.
 * Language: English
 */

$l_weEditor["object"]["response_save_ok"] = "Класс '%s' успешно сохранен!";
$l_weEditor["object"]["response_publish_ok"] = "Класс '%s' успешно опубликован!";
$l_weEditor["object"]["response_unpublish_ok"] = "Класс '%s' успешно снят с публикации!";
$l_weEditor["object"]["response_save_notok"] = "Ошибка при сохранении класса '%s'!";
$l_weEditor["object"]["response_path_exists"] = "Класс '%s' не сохранен по указанному пути, так как это местоположение занято другим документом или директорией!";
$l_weEditor["object"]["filename_empty"] = "Не введено имя для данного класса!";
$l_weEditor["object"]["we_filename_notValid"] = "Invalid class name or automatic name\\nValid characters are alpha-numeric, upper and lower case, as well as underscore, hyphen and dot (a-z, A-Z, 0-9, _, -, .)"; // TRANSLATE
$l_weEditor["object"]["we_filename_notAllowed"] = "Введенное имя класса недопустимо!";
$l_weEditor["object"]["response_save_noperms_to_create_folders"] = "Класс не сохранен, так как у Вас нет соответствующих полномочий на создание новых директорий (%s)!";

$l_weEditor["objectFile"]["response_save_ok"] = "Объект '%s' успешно сохранен!";
$l_weEditor["objectFile"]["response_publish_ok"] = "Объект '%s' успешно опубликован!";
$l_weEditor["objectFile"]["response_publish_notok"] = "Ошибка при публикации объекта '%s'!";
$l_weEditor["objectFile"]["response_unpublish_ok"] = "Объект '%s' успешно снят с публикации!";
$l_weEditor["objectFile"]["response_unpublish_notok"] = "Ошибка при снятии с публикации объекта '%s'!";
$l_weEditor["objectFile"]["response_not_published"] = "Объект '%s' не опубликован!";
$l_weEditor["objectFile"]["response_save_notok"] = "Ошибка при сохранении объекта '%s'!";
$l_weEditor["objectFile"]["response_path_exists"] = "Объект '%s' не сохранен по указанному пути, так как это местоположение занято другим документом или директорией!";
$l_weEditor["objectFile"]["we_objecturl_exists"] = "The object '%s' could not be saved, because another object with the same URL already exists";//TRANSLATE
$l_weEditor["objectFile"]["filename_empty"] = "Не введено имя для данного объекта!";
$l_weEditor["objectFile"]["we_filename_notValid"] = "Недействительное имя объекта\\nДопустимыми являются большие и малые буквы латинского алфавита, цифры, тире, нижняя черта и точка (a-z, A-Z, 0-9, _, -, .)";
$l_weEditor["objectFile"]["we_filename_notAllowed"] = "Введенное имя объекта недопустимо!";
$l_weEditor["objectFile"]["response_save_noperms_to_create_folders"] = "Объект не сохранен, так как у Вас нет соответствующих полномочий на создание новых директорий (%s)!";
$l_weEditor["objectFile"]["autoschedule"] = "Дата автоматической публикации объекта %s";
?>