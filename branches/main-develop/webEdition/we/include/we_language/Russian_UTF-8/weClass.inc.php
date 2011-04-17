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
 * Language file: we_class.inc.php
 * Provides language strings.
 * Language: English
 */
$l_weClass = array(
		'ChangePark' => "Данный атрибут можно изменить только для неопубликованного документа!",
		'fieldusers' => "Пользователи",
		'other' => "Другие",
		'use_object' => "Использовать объект",
		'language' => "Language", // TRANSLATE
		'users' => "Владельцы по умолчанию",
		'copytext/css' => "Копировать таблицу стилей CSS",
		'copytext/js' => "Копировать Javascript",
		'copytext/html' => "Копировать страницу HTML",
		'copytext/plain' => "Копировать текстовую страницу",
		'copytext/htaccess' => "Copy .htaccess file", //TRANSLATE
		'copytext/xml' => "Copy XML document", // TRANSLATE
		'copyTemplate' => "Копировать шаблон",
		'copyFolder' => "Копировать директорию",
		'copy_owners_expl' => "Выберите директорию, чье содержимое должно быть скопировано в текущую директорию.",
		'category' => "Категория",
		'folder_saved_ok' => "Директория '%s' успешно сохранена!",
		'response_save_ok' => "Документ '%s' успешно сохранен!",
		'response_publish_ok' => "Документ '%s' успешно опубликован!",
		'response_unpublish_ok' => "Документ '%s' успешно снят с публикации!",
		'response_save_notok' => "Ошибка при сохранении документа '%s'!",
		'response_path_exists' => "Невозможно сохранить документ или директорию %s, так как это местоположение уже занято другим документом или директорией!",
		'width' => "Ширина",
		'height' => "Высота",
		'origwidth' => "o.W.", // TRANSLATE
		'origheight' => "o.H.", // TRANSLATE
		'width_tmp' => "Ширина",
		'height_tmp' => "Высота",
		'percent_width_tmp' => "Ширина в %",
		'percent_height_tmp' => "Высота в %",
		'alt' => "Альтернативный текст",
		'alt_kurz' => "Альт.текст",
		'title' => "Заголовок",
		'use_meta_title' => "Использовать meta заголовок",
		'longdesc_text' => "Файл длинного описания",
		'align' => "Центровка",
		'name' => "Имя",
		'hspace' => "Расстояние по горизонтали",
		'vspace' => "Расстояние по вертикали",
		'border' => "Граница",
		'fields' => "Поля",
		'AutoFolder' => "Автоматическая папка",
		'AutoFilename' => "Имя файла",
		'import_ok' => "Документы успешно импортированы!",
		'function' => "Функция",
		'contenttable' => "Таблица-содержание",
		'quality' => "Качество",
		'salign' => "Расположение Flash ролика",
		'play' => "Воспроизведение (Play)",
		'loop' => "Повтор (Loop)",
		'scale' => "Масштаб",
		'wmode' => "Window mode", // TRANSLATE
		'bgcolor' => "Цвет заднего фона",
		'response_save_noperms_to_create_folders' => "Документ не был сохранен, так как у Вас нет соответствующих полномочий для создания директорий (%s)!",
		'file_on_liveserver' => "Файл уже существует",
		'defaults' => "По умолчанию",
		'attribs' => "Атрибуты",
		'intern' => "Внутренняя",
		'extern' => "Внешняя",
		'linkType' => "Тип ссылки",
		'href' => "Href", // TRANSLATE
		'target' => "Цель",
		'hyperlink' => "Гиперссылка",
		'nolink' => "Ссылка отсутствует",
		'noresize' => "Не менять размер",
		'pixel' => "Pixel", // TRANSLATE
		'percent' => "Процент",
		'new_doc_type' => "Новый тип документов",
		'doctypes' => "Типы документов",
		'subdirectory' => "Поддиректория",
		'subdir' => array(
				SUB_DIR_NO => "-- -- ",
				SUB_DIR_YEAR => "Год",
				SUB_DIR_YEAR_MONTH => "Год/месяц",
				SUB_DIR_YEAR_MONTH_DAY => "Год/месяц/день",
		),
		'doctype_save_ok' => "Тип документа '%s' успешно сохранен!",
		'doctype_save_nok_exist' => "Имя типа документа '%s' уже существует.\\n Выберите другое имя и попробуйте еще раз!",
		'delete_doc_type' => "Удалить '%s'",
		'doctype_delete_prompt' => "Удалить тип документа '%s'! Вы уверены?",
		'doctype_delete_nok' => "Имя типа документа '%s' уже используется!\\n Данный тип документов не может быть удален!",
		'doctype_delete_ok' => "Тип документа '%s' успешно удален!",
		'confirm_ext_change' => "Вы изменили условия генерирования динамической страницы.\\nИзменить расширение на заданное по умолчанию?",
		'newDocTypeName' => 'Введите, пожалуйста, имя для нового типа документов!',
		'no_perms' => 'Вы не уполномочены на проведение данной операции!',
		'workspaces' => "Рабочие пространства",
		'extraWorkspaces' => "Дополнительные рабочие пространства",
		'edit' => "Редактировать",
		'edit_image' => "Image editing", // TRANSLATE
		'workspace' => "Рабочее пространство",
		'information' => "Справка",
		'previeweditmode' => "Preview Editmode", // TRANSLATE
		'preview' => "Предварительный просмотр",
		'no_preview_available' => "No preview available for this file. To view this file please download it first.", // TRANSLATE
		'file_not_saved' => "File wasn't saved yet.", // TRANSLATE
		'download' => "Download", // TRANSLATE
		'validation' => "Проверка",
		'variants' => "Варианты",
		'tab_properties' => "Свойства",
		'metainfos' => "Мета-информация",
		'fields' => "Поля",
		'search' => "Поиск",
		'schedpro' => "Планировщик ПРО",
		'generateTemplate' => "Сгенерировать шаблон",
		'autoplay' => "Autoplay", // TRANSLATE
		'controller' => "Показать контрольную панель",
		'volume' => "Громкость",
		'hidden' => "Скрыто",
		'workspacesFromClass' => "Перенять от класса",
		'image' => "Изображение",
		'thumbnails' => "Иконки",
		'metadata' => "Metadata", // TRANSLATE
		'edit_show' => "Показывать опции изображений",
		'edit_hide' => "Скрыть опции изображений",
		'resize' => "Изменить размер",
		'rotate' => "Вращать изображение",
		'rotate_hint' => "Версия GD library, установленная на сервере, не поддерживает функцию вращения изображения!",
		'crop' => "Crop image", // TRANSLATE
		'quality' => "Качество",
		'quality_hint' => "Введите качество изображения для компрессирования JPEG. <br><br> 10: максимальное качество, занимает наибольший объем памяти <br> 0: минимальное качество занимает наименьший объем памяти",
		'quality_maximum' => "Максимум",
		'quality_high' => "Высокое",
		'quality_medium' => "Среднее",
		'quality_low' => "Низкое",
		'convert' => "Конвертировать",
		'convert_gif' => "Формат GIF",
		'convert_jpg' => "Формат JPEG",
		'convert_png' => "Формат PNG",
		'rotate0' => "без вращения",
		'rotate180' => "вращать на 180&deg;",
		'rotate90l' => "вращать 90&deg; против часовой стрелки",
		'rotate90r' => "вращать 90&deg; по часовой стрелке",
		'change_compression' => "Изменить компрессирование",
		'upload' => "Загрузить",
		'type_not_supported_hint' => "Установленная на сервере версия GD Library не поддерживает %s как формат вывода на экран! Рекомендуется конвертировать изображение в формат, поддерживаемый GD Library.",
		'CSS' => "CSS", // TRANSLATE
		'we_del_workspace_error' => "Данное рабочее пространство не удалено, так как оно задействовано объектами класса!",
		'master_template' => "Master template", // TRANSLATE
		'same_master_template' => "The selected master template cannot be identical with the current template!", // TRANSLATE
		'documents' => "Documents", // TRANSLATE
		'no_documents' => "No document based on this template", // TRANSLATE

		'grant_language' => "Change language", // TRANSLATE
		'grant_language_expl' => "Change the language of all files and directories which reside in the current directory to the setting above.", // TRANSLATE
		'grant_language_ok' => "Language have been successfully changed!", // TRANSLATE
		'grant_language_notok' => "There was an error while changing the language!", // TRANSLATE

		'grant_tid' => "Change display document", // TRANSLATE
		'grant_tid_expl' => "Change the display document of all files and directories which reside in the current directory to the setting above.", // TRANSLATE
		'grant_tid_ok' => "The display document has been successfully changed!", // TRANSLATE
		'grant_tid_notok' => "There was an error while changing the display document", // TRANSLATE

		'notValidFolder' => "The directory chosen is invalid!", // TRANSLATE


		'saveFirstMessage' => "You need to save your changes before executing this command.", // TRANSLATE

		'image_edit_null_not_allowed' => "In the fields Width and Height only numbers greater than 0 are allowed!", // TRANSLATE

		'doctype_changed_question' => "Should the default values for the document type be applied for this document?", // TRANSLATE
		'availableAfterSave' => "The feature is only available after saving the entry.", // TRANSLATE

		'properties' => 'Свойства',
		'path' => 'Путь',
		'adoptToAllInferiorDocuments' => 'adopt for all inferior documents', // TRANSLATE
		'filename' => 'Имя файла',
		'extension' => 'Расширение',
		'dir' => 'Директория',
		'document' => 'Документ',
		'upload_will_replace' => 'При нажатии на кнопку «Загрузить» файл переписывается на новый. Для одновременной загрузки нескольких файлов нужно воспользоваться командой импорта или вернуться на текущую страницу с помощью команды меню «файл».',
		'upload_single_files' => 'ВНИМАНИЕ: возможна загрузка одного файла. Для одновременной загрузки нескольких файлов нужно воспользоваться командой импорта.',
		'none' => '--None--', // TRANSLATE
		'nodoctype' => '--none--', // TRANSLATE
		'doctype' => 'Document type', // TRANSLATE
		'standard_workspace' => 'Default Workspace', // TRANSLATE
		'standard_template' => 'Шаблон',
		'template' => 'Шаблон',
		'no_template' => 'Без шаблона',
		'IsDynamic' => 'Создать динамическую страницу',
		'IsSearchable' => 'Разрешить поиск?',
		'InGlossar' => 'Not allowed for automatic glossary replacement', // TRANSLATE //TRANSLATE
		'metainfo' => 'Мета-теги',
		'Title' => 'Название',
		'Keywords' => 'Ключевые слова',
		'Description' => 'Описание',
		'urlfield0' => 'URL field', // TRANSLATE
		'urlfield1' => 'URL field 1', // TRANSLATE
		'urlfield2' => 'URL field 2', // TRANSLATE
		'urlfield3' => 'URL field 3', // TRANSLATE
		'Charset' => 'Кодировка символов',
		'moreProps' => 'Больше свойств',
		'lessProps' => 'Меньше свойств',
		'owners' => 'Владельцы',
		'maincreator' => 'Основной владелец',
		'otherowners' => 'Доступ для следующих пользователей',
		'onlyOwner' => 'Только основной владелец',
		'limitedAccess' => 'Ограничить доступ',
		'nobody' => 'Никто',
		'everybody' => 'Все пользователи',
		'noWorkspaces' => 'В данном классе не заданы рабочие пространства!',
		'readOnly' => 'Только читать',
		'copyWeDoc' => 'Копировать страницу webEdition',
		'showTagwizard' => 'Показать Мастер тегов',
		'hideTagwizard' => 'Спрятать Мастер тегов',
		'variant_fields' => 'Поля',
		'variant_info' => 'Следующие поля могут относиться к разным вариантам. Выберите поля с разными вариантами.',
		'webUser' => 'Customer', // TRANSLATE
		'docList' => 'Content', // TRANSLATE
		'version' => 'Versions', // TRANSLATE
		'languageLinksDefaults' => 'Default value for the document type in corresponding documents in the other front end languages', // TRANSLATE
		'languageLinks' => 'Link to the corresponding documents/objects in other languages',// TRANSLATE
);
