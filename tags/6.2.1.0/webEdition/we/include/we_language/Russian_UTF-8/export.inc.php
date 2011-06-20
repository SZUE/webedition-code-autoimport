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
 * Language file: export.inc.php
 * Provides language strings.
 * Language: English
 */
$l_export = array(
		'save_changed_export' => "Export has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'auto_selection' => "Automatic selection", // TRANSLATE
		'txt_auto_selection' => "Экспортирует  документы или объекты, выбранные автоматически, согласно типа документа или класса",
		'txt_auto_selection_csv' => "Exports objects automatically according to their class.", // TRANSLATE
		'manual_selection' => "Выбор вручную",
		'txt_manual_selection' => "Экспортирует документы или объекты, выбранные вручную",
		'txt_manual_selection_csv' => "Exports manually selected objects.", // TRANSLATE
		'element' => "Выбор элемента",
		'documents' => "Документы",
		'objects' => "Объекты",
		'step1' => "Выбрать параметры",
		'step2' => "Выбрать элементы для экспорта",
		'step3' => "Выбрать параметры экспорта",
		'step10' => "Экспорт завершен",
		'step99' => "Ошибка при экспортировании",
		'step99_notice' => "Экспорт невозможен",
		'server_finished' => "Экспортированный файл сохранен на сервере",
		'backup_finished' => "Экспорт прошел успешно",
		'download_starting' => "Download of the export file has been started.<br><br>If the download does not start after 10 seconds,<br>", // TRANSLATE
		'download' => "please click here.", // TRANSLATE
		'download_failed' => "Either the file you requested does not exist or you are not permitted to download it.", // TRANSLATE
		'file_format' => "File format", // TRANSLATE
		'export_to' => "Export to", // TRANSLATE
		'export_to_server' => "Server", // TRANSLATE
		'export_to_local' => "Local harddisc", // TRANSLATE
		'cdata' => "Кодировка",
		'export_xml_cdata' => "Добавить области CDATA",
		'export_xml_entities' => "Заменить сущности (entities)",
		'filename' => "File name", // TRANSLATE
		'path' => "Path", // TRANSLATE
		'doctypename' => "Documents of document type", // TRANSLATE
		'classname' => "Objects of class", // TRANSLATE
		'dir' => "Directory", // TRANSLATE
		'categories' => "Categories", // TRANSLATE
		'wizard_title' => "Export Wizard", // TRANSLATE
		'xml_format' => "XML", // TRANSLATE
		'csv_format' => "CSV", // TRANSLATE
		'csv_delimiter' => "Delimiter", // TRANSLATE
		'csv_enclose' => "Enclose character", // TRANSLATE
		'csv_escape' => "Escape character", // TRANSLATE
		'csv_lineend' => "File format", // TRANSLATE
		'csv_null' => "NULL replacement", // TRANSLATE
		'csv_fieldnames' => "Put field names in first row", // TRANSLATE
		'windows' => "Windows format", // TRANSLATE
		'unix' => "UNIX format", // TRANSLATE
		'mac' => "Mac format", // TRANSLATE
		'generic_export' => "Generic export", // TRANSLATE
		'title' => "Export Wizard", // TRANSLATE
		'gxml_export' => "Generic XML export", // TRANSLATE
		'txt_gxml_export' => "Export webEdition documents and objects to a \"flat\" XML file (3 levels).", // TRANSLATE
		'csv_export' => "CSV export", // TRANSLATE
		'txt_csv_export' => "Export webEdition objects to a CSV file (comma separated values).", // TRANSLATE
		'csv_params' => "Settings", // TRANSLATE
		'error' => "Экспорт не выполнен!",
		'error_unknown' => "Неизвестная ошибка!",
		'error_object_module' => "В данный момент не поддерживается экспорт данных в файлы CSV!<br><br>Не инсталлирован модуль «База данных/объект», без которого функция экспорта файлов CSV не работает.",
		'error_nothing_selected_docs' => "Экспорт не выполнен!<br><br>Отсутствуют выделенные документы",
		'error_nothing_selected_objs' => "Экспорт не выполнен!<br><br>Отсутствуют выделенные документы или объекты",
		'error_download_failed' => "Загрузка экспортируемых файлов не состоялась",
		'comma' => ", {comma}", // TRANSLATE
		'semicolon' => "; {semicolon}", // TRANSLATE
		'colon' => ": {colon}", // TRANSLATE
		'tab' => "\\t {tab}", // TRANSLATE
		'space' => "  {space}", // TRANSLATE
		'double_quote' => "\" {double quote}", // TRANSLATE
		'single_quote' => "' {single quote}", // TRANSLATE
		'we_export' => 'Экспорт webEdition',
		'wxml_export' => 'XML экспорт',
		'txt_wxml_export' => 'Экспорт документов, шаблонов, объектов и классов webEdition в соответствии с определением типа документа, заданным в webEdition.',
		'options' => 'Options', // TRANSLATE
		'handle_document_options' => 'Documents', // TRANSLATE
		'handle_template_options' => 'Templates', // TRANSLATE
		'handle_def_templates' => 'Export default templates', // TRANSLATE
		'handle_document_includes' => 'Export included documents', // TRANSLATE
		'handle_document_linked' => 'Export linked documents', // TRANSLATE
		'handle_object_options' => 'Objects', // TRANSLATE
		'handle_def_classes' => 'Export default classes', // TRANSLATE
		'handle_object_includes' => 'Export included objects', // TRANSLATE
		'handle_classes_options' => 'Classes', // TRANSLATE
		'handle_class_defs' => 'Default value', // TRANSLATE
		'handle_object_embeds' => 'Export embedded objects', // TRANSLATE
		'handle_doctype_options' => 'Doctypes/<br>Categorys/<br>Navigation',
		'handle_doctypes' => 'Doctypes', // TRANSLATE
		'handle_categorys' => 'Categorys',
		'export_depth' => 'Export depth', // TRANSLATE
		'to_level' => 'to level', // TRANSLATE
		'select_export' => 'Для того, чтобы экспортировать  запись нужно выделить соответствующее окошко в дереве файлов. Важное примечание: экспортируются все выделенные записи из всех областей, а при экспорте директории – все документы этой директории ',
		'templates' => 'Templates', // TRANSLATE
		'classes' => 'Classes', // TRANSLATE

		'nothing_to_delete' => 'Нет предмета удаления.',
		'nothing_to_save' => 'Нет предмета сохранения!',
		'no_perms' => 'Нет разрешения!',
		'new' => 'Новую',
		'export' => 'экспортную',
		'group' => 'группу',
		'save' => 'сохранить',
		'delete' => 'удалить',
		'quit' => 'Выйти',
		'property' => 'Свойство',
		'name' => 'Имя',
		'save_to' => 'Сохранить (куда):',
		'selection' => 'выделенное',
		'save_ok' => 'Данные экспорта сохранены.',
		'save_group_ok' => 'Группа сохранена.',
		'log' => 'Детальные записи',
		'start_export' => 'Запуск экспорта',
		'prepare' => 'Подготовка к запуску...',
		'doctype' => 'тип документа',
		'category' => 'категория',
		'end_export' => 'Экспорт завершен',
		'newFolder' => "Новая группа",
		'folder_empty' => "Папка  пуста!",
		'folder_path_exists' => "Папка уже существует!",
		'wrongtext' => "Имя недействительно",
		'wrongfilename' => "The filename is not valid!", // TRANSLATE
		'folder_exists' => "Папка уже существует",
		'delete_ok' => 'Данные экспорта удалены.',
		'delete_nok' => 'ОШИБКА: данные экспорта не удалены',
		'delete_group_ok' => 'Группа удалена.',
		'delete_group_nok' => 'ОШИБКА: группа не удалена',
		'delete_question' => 'Удалить данные текущего экспорта?',
		'delete_group_question' => 'Удалить текущую группу?',
		'download_starting2' => 'Запущена загрузка экспортного файла.',
		'download_starting3' => 'Если загрузка не начнется через 10 секунд,',
		'working' => 'в работе',
		'txt_document_options' => 'Шаблоном по умолчанию является шаблон, заданный в свойствах документа. Включенными документами являются внутренние документы, вложенные в экспортированные документ с помощью тегов we:include, we:form, we:url, we:linkToSeeMode, we:a, we:href, we:link, we:css, we:js, а также we:addDelNewsletterEmail. Включенными объектами являются объекты, вложенные в экспортированный документ с помощью тегов we:object и we:form. Документами, связанными ссылками, являются внутренние документы, связанные ссылкой с экспортированным документом с помощью таких тегов HTM  как:  body, a, img, table и td.',
		'txt_object_options' => 'Классом по умолчанию является класс, заданный в свойствах объекта.',
		'txt_exportdeep_options' => 'Глубина экспорта определяет граничный уровень экспорта вложенных документов и объектов. Данное поле должно состоять из цифр!',
		'name_empty' => 'Должно быть заполнено имя!',
		'name_exists' => 'Имя уже существует!',
		'help' => 'Помощь',
		'info' => 'Справка',
		'path_nok' => 'Путь неверный!',
		'must_save' => "Данные экспорта были изменены.\\nПрежде чем экспортировать, Вы должны сохранить данные экспорта!",
		'handle_owners_option' => 'Данные владельцев',
		'handle_owners' => 'Экспорт данных владельцев',
		'txt_owners' => 'Экспорт данных владельцев, связанных ссылкой. ',
		'weBinary' => 'File', // TRANSLATE
		'handle_navigation' => 'Navigation', // TRANSLATE
		'weNavigation' => 'Navigation', // TRANSLATE
		'weNavigationRule' => 'Navigation rule', // TRANSLATE
		'weThumbnail' => 'Thumbnails', // TRANSLATE
		'handle_thumbnails' => 'Thumbnails', // TRANSLATE

		'navigation_hint' => 'Document types, categories and the navigation are exported depending on your select documents and templates. The export of the navigation therefore requires the export of a template with a document based on it in which the navigation is used.', // TRANSLATE
		'title' => 'Мастер экспорта',
		'selection_type' => 'Задайте выбор элемента',
		'auto_selection' => 'Автоматический выбор',
		'txt_auto_selection' => 'Экспорт автоматически выбранных документов и объектов в соответствии с типом документа и классом.',
		'manual_selection' => 'Выбор вручную',
		'txt_manual_selection' => 'Экспорт документов и объектов, выбранных вручную.',
		'element' => 'Выбор элемента',
		'select_elements' => 'Выбрать элементы для экспорта',
		'select_docType' => 'Выберите тип документа или шаблон.',
		'none' => '-- без --',
		'doctype' => 'Тип документа',
		'template' => 'Шаблон',
		'categories' => 'Категории',
		'documents' => 'Документы',
		'objects' => 'Объекты',
		'class' => 'Класс',
		'isDynamic' => 'Генерировать динамическую страницу',
		'extension' => 'Расширение',
		'wexml_export' => 'Экспорт',
		'filename' => 'Имя файла',
		'extra_data' => 'Дополнительные данные',
		'integrated_data' => 'Экспорт вложенных данных',
		'integrated_data_txt' => 'Выберите данную опцию для экспорта данных, вложенных в шаблоны или документы.',
		'max_level' => 'максимальный уровень',
		'export_doctypes' => 'Экспорт типов документов',
		'export_categories' => 'Экспорт категорий',
		'export_location' => 'Назначение экспорта',
		'local_drive' => 'локальный дисковод',
		'server' => 'сервер',
		'export_progress' => 'Экспорт',
		'prepare_progress' => 'в процессе подготовки',
		'finish_progress' => 'завершен',
		'finish_export' => 'Экспорт успешно завершен!',
);