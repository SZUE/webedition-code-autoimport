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
 * Language file: import.inc.php
 * Provides language strings.
 * Language: English
 */
$l_import = array(
		'title' => 'Import Wizard', // TRANSLATE
		'wxml_import' => 'webEdition XML import', // TRANSLATE
		'gxml_import' => 'Generic XML import', // TRANSLATE
		'csv_import' => 'CSV import', // TRANSLATE
		'import' => 'Importing', // TRANSLATE
		'none' => '-- none --', // TRANSLATE
		'any' => '-- none --', // TRANSLATE
		'source_file' => 'Source file', // TRANSLATE
		'import_dir' => 'Target directory', // TRANSLATE
		'select_source_file' => 'Please choose a source file.', // TRANSLATE
		'we_title' => 'Title', // TRANSLATE
		'we_description' => 'Description', // TRANSLATE
		'we_keywords' => 'Keywords', // TRANSLATE
		'uts' => 'Unix-Timestamp', // TRANSLATE
		'unix_timestamp' => 'The unix time stamp is a way to track time as a running total of seconds. This count starts at the Unix Epoch on January 1st, 1970.', // TRANSLATE
		'gts' => 'GMT Timestamp', // TRANSLATE
		'gmt_timestamp' => 'General Mean Time ie. Greenwich Mean Time (GMT).', // TRANSLATE
		'fts' => 'Specified format', // TRANSLATE
		'format_timestamp' => 'The following characters are recognized in the format parameter string: Y (a full numeric representation of a year, 4 digits), y (a two digit representation of a year), m (numeric representation of a month, with leading zeros), n (numeric representation of a month, without leading zeros), d (day of the month, 2 digits with leading zeros), j (day of the month without leading zeros), H (24-hour format of an hour with leading zeros), G (24-hour format of an hour without leading zeros), i (minutes with leading zeros), s (seconds, with leading zeros)', // TRANSLATE
		'import_progress' => 'Importing', // TRANSLATE
		'prepare_progress' => 'Preparing', // TRANSLATE
		'finish_progress' => 'Finished', // TRANSLATE
		'finish_import' => 'The Import was successful!', // TRANSLATE
		'import_file' => 'File import', // TRANSLATE
		'import_data' => 'Data import', // TRANSLATE
		'import_templates' => 'Template import', // TRANSLATE
		'template_import' => 'First Steps Wizard', // TRANSLATE
		'txt_template_import' => 'Import ready example templates and template sets from the webEdition server', // TRANSLATE
		'file_import' => 'Import local files', // TRANSLATE
		'txt_file_import' => 'Import one or more files from the local hard drive.', // TRANSLATE
		'site_import' => 'Import files from server', // TRANSLATE
		'site_import_isp' => 'Import graphics from server', // TRANSLATE
		'txt_site_import_isp' => 'Импорт графики с корневого каталога сервера. Установите фильтры по отбору графики для импорта.',
		'txt_site_import' => 'Import files from the root-directory of the server. Set filter options to choose if images, HTML pages, Flash, JavaScript, or CSS files, plain-text documents, or other types of files are to be imported.', // TRANSLATE
		'txt_wxml_import' => 'webEdition XML files contain information about webEdition documents, templates or objects. Choose a directory to which the files are to be imported.', // TRANSLATE
		'txt_gxml_import' => 'Import "flat" XML files, such as those provided by phpMyAdmin. The dataset fields have to be allocated to the webEdition dataset fields. Use this to import XML files exported from webEdition without the export module.', // TRANSLATE
		'txt_csv_import' => 'Import CSV files (Comma Separated Values) or modified textformats (e. g. *.txt). The dataset fields are assigned to the webEdition fields.', // TRANSLATE
		'add_expat_support' => 'In order to implement support for the XML expat parser, you will need to recompile PHP to add support for this library to your PHP build. The expat extension, created by James Clark, can be found at http://www.jclark.com/xml/.', // TRANSLATE
		'xml_file' => 'Файл-XML',
		'templates' => 'Шаблоны',
		'classes' => 'Классы',
		'predetermined_paths' => 'Предустановленные пути',
		'maintain_paths' => 'Оставить пути без изменений',
		'import_options' => 'Опции импорта',
		'file_collision' => 'Если файл уже существует',
		'collision_txt' => 'При импорте файлов в директорию, содержащую файл с таким же именем, возможны конфликты данных. Вы можете задать соответствующие параметры для Мастера импорта.',
		'replace' => 'Заменить',
		'replace_txt' => 'Удалить уже имеющийся файл и заменить новым файлом.',
		'rename' => 'Переименовать',
		'rename_txt' => 'Имени файла назначается один номер ID. Все ссылки, указывающие на такой файл, соответствуют его ID.',
		'skip' => 'Пропустить',
		'skip_txt' => 'При пропуске текущего файла сохраняется файл, записанный ранее.',
		'extra_data' => 'Данные экстра',
		'integrated_data' => 'Импортировать включенные данные',
		'integrated_data_txt' => 'При выборе данной опции импортируются данные/документы, включенные в шаблоны.',
		'max_level' => 'максимальный уровень',
		'import_doctypes' => 'Импортировать типы документов',
		'import_categories' => 'Импортировать категории',
		'invalid_wxml' => 'Возможен импорт только файлов XML, соответствующих определению типа документа.',
		'valid_wxml' => 'The XML document is well-formed and valid.  It applies to the webEdition document type definition (DTD).', // TRANSLATE
		'specify_docs' => 'Please choose the documents to import.', // TRANSLATE
		'specify_objs' => 'Please choose the objects to import.', // TRANSLATE
		'specify_docs_objs' => 'Please choose whether to import documents and objects.', // TRANSLATE
		'no_object_rights' => 'You do not have authorization to import objects.', // TRANSLATE
		'display_validation' => 'Display XML validation', // TRANSLATE
		'xml_validation' => 'XML validation', // TRANSLATE
		'warning' => 'Warning', // TRANSLATE
		'attribute' => 'Attribute', // TRANSLATE
		'invalid_nodes' => 'Invalid XML node at position ', // TRANSLATE
		'no_attrib_node' => 'No XML element "attrib" at position ', // TRANSLATE
		'invalid_attributes' => 'Invalid attributes at position ', // TRANSLATE
		'attrs_incomplete' => 'The list of #required and #fixed attributes is incomplete at position ', // TRANSLATE
		'wrong_attribute' => 'The attribute name is neither defined as #required nor #implied at position ', // TRANSLATE
		'documents' => 'Documents', // TRANSLATE
		'objects' => 'Objects', // TRANSLATE
		'fileselect_server' => 'Load file from server', // TRANSLATE
		'fileselect_local' => 'Upload file from local hard disc', // TRANSLATE
		'filesize_local' => 'Because of restrictions within PHP, the file that you wish to upload cannot exceed %s.', // TRANSLATE
		'xml_mime_type' => 'The selected file cannot be imported. Mime-type:', // TRANSLATE
		'invalid_path' => 'The path of the source file is invalid.', // TRANSLATE
		'ext_xml' => 'Please select a source file with the extension ".xml".', // TRANSLATE
		'store_docs' => 'Target directory documents', // TRANSLATE
		'store_tpls' => 'Target directory templates', // TRANSLATE
		'store_objs' => 'Target directory objects', // TRANSLATE
		'doctype' => 'Document type',
		'gxml' => 'Generic XML', // TRANSLATE
		'data_import' => 'Import data', // TRANSLATE
		'documents' => 'Documents', // TRANSLATE
		'objects' => 'Objects', // TRANSLATE
		'type' => 'Type', // TRANSLATE
		'template' => 'Template', // TRANSLATE
		'class' => 'Class', // TRANSLATE
		'categories' => 'Categories', // TRANSLATE
		'isDynamic' => 'Generate page dynamically', // TRANSLATE
		'extension' => 'Extension', // TRANSLATE
		'filetype' => 'Filetype', // TRANSLATE
		'directory' => 'Directory', // TRANSLATE
		'select_data_set' => 'Select dataset', // TRANSLATE
		'select_docType' => 'Please choose a template.', // TRANSLATE
		'file_exists' => 'The selected source file does not exist. Please check the given file path. Path: ', // TRANSLATE
		'file_readable' => 'The selected source file is not readable and thereby cannot be imported.', // TRANSLATE
		'asgn_rcd_flds' => 'Assign data fields', // TRANSLATE
		'we_flds' => 'webEdition&nbsp;fields', // TRANSLATE
		'rcd_flds' => 'Dataset&nbsp;fields', // TRANSLATE
		'name' => 'Name', // TRANSLATE
		'auto' => 'Automatic', // TRANSLATE
		'asgnd' => 'Assigned', // TRANSLATE
		'pfx' => 'Prefix', // TRANSLATE
		'pfx_doc' => 'Document', // TRANSLATE
		'pfx_obj' => 'Object', // TRANSLATE
		'rcd_fld' => 'Dataset field', // TRANSLATE
		'import_settings' => 'Import settings', // TRANSLATE
		'xml_valid_1' => 'The XML file is valid and contains', // TRANSLATE
		'xml_valid_s2' => 'elements. Please select the elements to import.', // TRANSLATE
		'xml_valid_m2' => 'XML child node in the first level with different names. Please choose the XML node and the number of elements to import.', // TRANSLATE
		'well_formed' => 'The XML document is well-formed.', // TRANSLATE
		'not_well_formed' => 'The XML document is not well-formed and cannot be imported.', // TRANSLATE
		'missing_child_node' => 'The XML document is well-formed, but contains no XML nodes and can therefore not be imported.', // TRANSLATE
		'select_elements' => 'Please choose the datasets to import.', // TRANSLATE
		'num_elements' => 'Please choose the number of datasets from 1 to ', // TRANSLATE
		'xml_invalid' => '', // TRANSLATE
		'option_select' => 'Selection..', // TRANSLATE
		'num_data_sets' => 'Datasets:', // TRANSLATE
		'to' => 'to', // TRANSLATE
		'assign_record_fields' => 'Assign data fields', // TRANSLATE
		'we_fields' => 'webEdition fields', // TRANSLATE
		'record_fields' => 'Dataset fields', // TRANSLATE
		'record_field' => 'Dataset field ', // TRANSLATE
		'attributes' => 'Attributes', // TRANSLATE
		'settings' => 'Settings', // TRANSLATE
		'field_options' => 'Field options', // TRANSLATE
		'csv_file' => 'CSV file', // TRANSLATE
		'csv_settings' => 'CSV settings', // TRANSLATE
		'xml_settings' => 'XML settings', // TRANSLATE
		'file_format' => 'File format', // TRANSLATE
		'field_delimiter' => 'Separator', // TRANSLATE
		'comma' => ', {comma}', // TRANSLATE
		'semicolon' => '; {semicolon}', // TRANSLATE
		'colon' => ': {colon}', // TRANSLATE
		'tab' => "\\t {tab}", // TRANSLATE
		'space' => '  {space}', // TRANSLATE
		'text_delimiter' => 'Text separator', // TRANSLATE
		'double_quote' => '" {double quote}', // TRANSLATE
		'single_quote' => '\' {single quote}', // TRANSLATE
		'contains' => 'First line contains field name', // TRANSLATE
		'split_xml' => 'Import datasets sequential', // TRANSLATE
		'wellformed_xml' => 'Validation for well-formed XML', // TRANSLATE
		'validate_xml' => 'XML validiation', // TRANSLATE
		'select_csv_file' => 'Please choose a CSV source file.', // TRANSLATE
		'select_seperator' => 'Please, select a seperator.', // TRANSLATE
		'format_date' => 'Date format', // TRANSLATE
		'info_sdate' => 'Select the date format for the webEdition field', // TRANSLATE
		'info_mdate' => 'Select the date format for the webEdition fields', // TRANSLATE
		'remark_csv' => 'You are able to import CSV files (Comma Separated Values) or modified text formats (e. g. *.txt). The field delimiter (e. g. , ; tab, space) and text delimiter (= which encapsulates the text inputs) can be preset at the import of these file formats.', // TRANSLATE
		'remark_xml' => 'To avoid the predefined timeout of a PHP-script, select the option "Import data-sets separately", to import large files.<br>If you are unsure whether the selected file is webEdition XML or not, the file can be tested for validity and syntax.', // TRANSLATE

		'import_docs' => "Импорт документов ",
		'import_templ' => "Импорт шаблонов ",
		'import_objs' => "Импорт объектов ",
		'import_classes' => "Импорт классов ",
		'import_doctypes' => "Импорт типов документов ",
		'import_cats' => "Импорт категорий ",
		'documents_desc' => "Введите, пожалуйста, директорию назначения для импортируемых документов. В случае если отмечена опция \"Оставить пути без изменений\", соответствующий путь воссоздается автоматически, в противном случае путь игнорируется. ",
		'templates_desc' => " Введите, пожалуйста, директорию назначения для импортируемых объектов. В случае если отмечена опция \"Оставить пути без изменений\", соответствующий путь воссоздается автоматически, в противном случае путь игнорируется. ",
		'handle_document_options' => 'Документы',
		'handle_template_options' => 'Шаблоны',
		'handle_object_options' => 'Объекты',
		'handle_class_options' => 'Классы',
		'handle_doctype_options' => "Типы документов",
		'handle_category_options' => "Категории",
		'log' => 'Детальные записи',
		'start_import' => 'Запуск импорта',
		'prepare' => 'Подготовка...',
		'update_links' => 'Обновление ссылок...',
		'doctype' => 'тип документа',
		'category' => 'Категория',
		'end_import' => 'Импорт завершен',
		'handle_owners_option' => 'Данные владельцев',
		'txt_owners' => 'Импорт данных владельцев, связанных ссылками.',
		'handle_owners' => 'Восстановить данные владельцев',
		'notexist_overwrite' => 'При отсутствии владельца переписать данные владельца',
		'owner_overwrite' => 'Переписать данные владельца',
		'name_collision' => 'Противоречие имени',
		'item' => 'Единица',
		'backup_file_found' => 'Файл относится к резервным файлам webEdition. Для импорта данных воспользуйтесь опцией \"Backup\" в пункте меню \"Файл\".',
		'backup_file_found_question' => 'Закрыть данное диалоговое окно и начать запуск Мастера backup?',
		'close' => 'Закрыть',
		'handle_file_options' => 'Файлы',
		'import_files' => 'Импорт файлов',
		'weBinary' => 'Файл',
		'format_unknown' => 'Формат файла неизвестен!',
		'customer_import_file_found' => 'Файл относится к файлам импорта с данными клиентов. Для импорта данных воспользуйтесь опцией \"Импорт/экспорт\" модуля управления клиентами (ПРО).',
		'upload_failed' => 'Невозможно загрузить файл. Убедитесь в том, что размер файла не превышает %s',
		'import_navigation' => 'Import navigation', // TRANSLATE
		'weNavigation' => 'Navigation', // TRANSLATE
		'navigation_desc' => 'Select the directory where the navigation will be imported.', // TRANSLATE
		'weNavigationRule' => 'Navigation rule', // TRANSLATE
		'weThumbnail' => 'Thumbnail', // TRANSLATE
		'import_thumbnails' => 'Import thumbnails', // TRANSLATE
		'rebuild' => 'Rebuild', // TRANSLATE
		'rebuild_txt' => 'Automatic rebuild', // TRANSLATE
		'finished_success' => 'The import of the data has finished successfully.',
		'encoding_headline' => 'Charset', // TRANSLATE
		'encoding_noway' => 'A conversion  is only possible between ISO-8859-1 and UTF-8 <br/>and with a set default charset (settings dialog)', // TRANSLATE
		'encoding_change' => "Change, from '", // TRANSLATE
		'encoding_XML' => '', // TRANSLATE
		'encoding_to' => "' (XML file) to '", // TRANSLATE
		'encoding_default' => "' (standard)", // TRANSLATE
);