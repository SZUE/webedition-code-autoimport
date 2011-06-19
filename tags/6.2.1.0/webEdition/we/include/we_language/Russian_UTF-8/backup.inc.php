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
$l_backup = array(
		'save_not_checked' => "Вы не выбрали место сохранения резервного файла!",
		'wizard_title' => "Restore Backup Wizard", // TRANSLATE
		'wizard_title_export' => "Backup Export Wizard", // TRANSLATE
		'save_before' => "During import all existing data will be erased! It is recommended that you save your existing data first.", // TRANSLATE
		'save_question' => "Do you want to save your existing data?", // TRANSLATE
		'step1' => "Step 1/4 - Save existing data", // TRANSLATE
		'step2' => "Step 2/4 - Select import source", // TRANSLATE
		'step3' => "Step 3/4 - Import saved data", // TRANSLATE
		'step4' => "Step 4/4 - Restore finished", // TRANSLATE
		'extern' => "Restore webEdition external files and folders", // TRANSLATE
		'settings' => "Restore preferences", // TRANSLATE
		'rebuild' => "Automatic rebuild", // TRANSLATE
		'select_upload_file' => "Upload import from local file", // TRANSLATE
		'select_server_file' => "Choose the backup file you want to import from this list.", // TRANSLATE
		'charset_warning' => "If you encounter problems when restoring a backup, please ensure that the <strong>target system uses the same character set as the source system</strong>. This applies both to the character set of the database (collation) as well as for the character set of the user interface language!", // TRANSLATE
		'defaultcharset_warning' => '<span style="color:ff0000">Attention! The standard charset is not defined.</span> For some server configurations, this can lead to problems while importing backups.!', // TRANSLATE
		'finished_success' => "The import of backup data has finished successfully.",
		'finished_fail' => "The import of backup data has not finished successfully.", // TRANSLATE
		'question_taketime' => "Export can take some time.", // TRANSLATE
		'question_wait' => "Please wait!", // TRANSLATE
		'export_title' => "Export", // TRANSLATE
		'finished' => "Finished", // TRANSLATE
		'extern_files_size' => "Since the maximum file size is limited to %.1f MB (%s byte) by your database settings, multiple files may be created.", // TRANSLATE
		'extern_files_question' => "Save webEdition external files and folders.", // TRANSLATE
		'export_location' => "Specify where you want to save the backup file. If it is stored on the server, you find the file in '/webEdition/we_backup/data/'.", // TRANSLATE
		'export_location_server' => "On server", // TRANSLATE
		'export_location_send' => "On local hard disk", // TRANSLATE
		'can_not_open_file' => "Unable to open file '%s'.", // TRANSLATE
		'too_big_file' => "File '%s' cannot be written as the size exceeds the maximum file size.", // TRANSLATE
		'cannot_save_tmpfile' => "Unable to create temporary file. Chek if you have write premissions over %s", // TRANSLATE
		'cannot_save_backup' => "Unable to save backup file.", // TRANSLATE
		'cannot_send_backup' => "Unable to execute backup.", // TRANSLATE
		'finish' => "The backup was successfully created.", // TRANSLATE
		'finish_error' => "Ошибка: невозможно создать резервный файл",
		'finish_warning' => "Внимание: сохранение резервного файла завершено, но не все файлы сохранены в полном объеме",
		'export_step1' => "Step 1 of 2 - Backup parameters", // TRANSLATE
		'export_step2' => "Step 2 of 2 - Backup complete", // TRANSLATE
		'unspecified_error' => "An unknown error occurred!", // TRANSLATE
		'export_users_data' => "Сохранить данные «Управления пользователями»",
		'import_users_data' => "Восстановить данные «Управления пользователями»",
		'import_from_server' => "Загрузить данные с сервера",
		'import_from_local' => "Загрузить данные файла, сохраненного локально ",
		'backup_form' => "Резервный файл",
		'nothing_selected' => "Ничего не выделено!",
		'query_is_too_big' => "Резервный файл содержит файл, не подлежащий восстановлению, так как он превышает предел %s bytes!",
		'show_all' => "Show all files", // TRANSLATE
		'import_customer_data' => "Восстановить данные «Управления клиентами»",
		'import_shop_data' => "Восстановить данные «Интернет-магазина»",
		'export_customer_data' => "Сохранить данные «Управления клиентами»",
		'export_shop_data' => "Сохранить данные «Интернет-магазина»",
		'working' => "В работе",
		'preparing_file' => "Процесс подготовки к восстановлению",
		'external_backup' => "Сохранение внешних данных и директорий",
		'import_content' => "Importing content", // TRANSLATE
		'import_files' => "Восстановление файлов",
		'import_doctypes' => "Восстановить типы документов",
		'import_user_data' => "Восстановить данные пользователя",
		'import_templates' => "Восстановление шаблонов",
		'export_content' => "Exporting content", // TRANSLATE
		'export_files' => "Сохранение файлов",
		'export_doctypes' => "Сохранить типы документов",
		'export_user_data' => "Сохранить данные пользователя",
		'export_templates' => "Сохранение шаблонов",
		'download_starting' => "Download of the backup file has been started.<br><br>If the download does not start after 10 seconds,<br>", // TRANSLATE
		'download' => "Please click here.", // TRANSLATE
		'download_failed' => "Either the file you requested does not exist or you are not permitted to download it.", // TRANSLATE
		'extern_backup_question_exp' => "You selected the option 'Save webEdition external files and folders'. This option could take some time and may lead to some system-specific errors. Do you want to proceed anyway?", // TRANSLATE
		'extern_backup_question_exp_all' => "You selected the option 'Check all'. That also checks the option 'Save webEdition external files and folders'. This option could take some time and may lead to some system-specific errors. <br><br>Do you want to let 'Save webEdition external files and folders' be checked anyway?", // TRANSLATE
		'extern_backup_question_imp' => "You selected the option 'Restore webEdition external files and folders'. This option could take some time and may lead to some system-specific errors. Do you want to proceed anyway?", // TRANSLATE
		'extern_backup_question_imp_all' => "You selected the option 'Check all'. That also checks the option 'Restore webEdition external files and folders'. This option could take some time and may lead to some system-specific errors. <br><br>Do you want to let 'Restore webEdition external files and folders' be checked anyway?", // TRANSLATE
		'nothing_selected_fromlist' => "Выберите из списка резервный файл!",
		'export_workflow_data' => "Save workflow data", // TRANSLATE
		'export_todo_data' => "Save task/messaging data", // TRANSLATE
		'import_workflow_data' => "Restore workflow data", // TRANSLATE
		'import_todo_data' => "Restore task/messaging data", // TRANSLATE
		'import_check_all' => "Check all", // TRANSLATE
		'export_check_all' => "Check all", // TRANSLATE
		'import_shop_dep' => "You have selected the option 'Restore shop data'. The Shop Module needs the customers data and because of that, 'Restore customers data' has been automatically selected.", // TRANSLATE
		'export_shop_dep' => "You have selected the option 'Save shop data'. The Shop Module needs the customers data and because of that, 'Save customers data' has been automatically selected.", // TRANSLATE
		'import_workflow_dep' => "You have selected the option 'Restore workflow data'. The Workflow Module needs the documents and users data and because of that, 'Restore documents and templates' and 'Restore user data' has been automatically selected.", // TRANSLATE
		'export_workflow_dep' => "You have selected the option 'Save workflow data'. The Workflow Module needs the documents and users data and because of that,  'Save documents and templates' and 'Save workflow data' has been automatically selected.", // TRANSLATE
		'import_todo_dep' => "You have selected the option 'Restore task/messaging data'. The Task/Messaging Module needs the users data and because of that, 'Restore user data' has been automatically selected.", // TRANSLATE
		'export_todo_dep' => "You have selected the option 'Save task/messaging data'. The Task/Messaging Module needs the users data and because of that, 'Save user data' has been automatically selected.", // TRANSLATE
		'export_newsletter_data' => "Сохранить данные листа рассылки",
		'import_newsletter_data' => "Восстановить данные листа рассылки",
		'export_newsletter_dep' => "You have selected the option 'Save newsletter data'. The Newsletter Module needs the documents and users data and because of that, 'Save documents and templates' and 'Save customers data' has been automatically selected.", // TRANSLATE
		'import_newsletter_dep' => "You have selected the option 'Restore newsletter data'. The Newsletter Module needs the documents and users data and because of that,  'Restore documents and templates' and 'Restore customers data' has been automatically selected.", // TRANSLATE
		'warning' => "Warning", // TRANSLATE
		'error' => "Error", // TRANSLATE
		'export_temporary_data' => "Save temporary data", // TRANSLATE
		'import_temporary_data' => "Restore temporary data", // TRANSLATE
		'export_banner_data' => "Save banner data", // TRANSLATE
		'import_banner_data' => "Restore banner data", // TRANSLATE
		'export_prefs' => "Save preferences", // TRANSLATE
		'import_prefs' => "Restore preferences", // TRANSLATE
		'export_links' => "Save links", // TRANSLATE
		'import_links' => "Restore links", // TRANSLATE
		'export_indexes' => "Save indexes", // TRANSLATE
		'import_indexes' => "Restore indexes", // TRANSLATE
		'filename' => "Файл (имя)",
		'compress' => "компрессировать",
		'decompress' => "декомпрессировать",
		'option' => "опции резервного копирования",
		'filename_compression' => "Введите имя резервного файла. Вы также можете активировать команду компрессирования файла. Резервный файл компрессируется с помощью gzip с расширением .gz. Эта операция займет некоторое время!",
		'export_core_data' => "Сохранить документы и шаблоны ",
		'import_core_data' => "Восстановить документы и шаблоны",
		'export_object_data' => "Сохранить объекты и классы",
		'import_object_data' => "Восстановить объекты и классы",
		'export_binary_data' => "Сохранить бинарные файлы (изображения, PDF, ...) ",
		'import_binary_data' => "Восстановить бинарные файлы (изображения, PDF, ...) ",
		'export_schedule_data' => "Сохранить данные планировщика ",
		'import_schedule_data' => "Восстановить данные планировщика ",
		'export_settings_data' => "Сохранить настройки",
		'import_settings_data' => " Восстановить настройки",
		'export_extern_data' => " Сохранить внешние файлы/директории webEdition",
		'import_extern_data' => " Восстановить внешние файлы/директории webEdition",
		'export_binary_dep' => "Вы выбрали опцию: «сохранить бинарные файлы». Для корректного функционирования бинарных файлов требуются соответствующие документы. Опция: «сохранить документы и шаблоны» выбирается автоматически.",
		'import_binary_dep' => "Вы выбрали опцию: «восстановить бинарные файлы». Для корректного функционирования бинарных файлов требуются соответствующие документы. Опция: «восстановить документы и шаблоны» выбирается автоматически.",
		'export_schedule_dep' => "You have selected the option 'Save schedule data'. The Schedule Module needs the documents and objects and because of that, 'Save documents and templates' and 'Save objects and classes' has been automatically selected.", // TRANSLATE
		'import_schedule_dep' => "You have selected the option 'Restore schedule data'. The Schedule Module needs the documents data and objects and because of that, 'Restore documents and templates' and 'Restore objects and classes' has been automatically selected.", // TRANSLATE
		'export_temporary_dep' => "Вы выбрали опцию: «сохранить временные файлы». Для корректного функционирования временных файлов требуются соответствующие документы. Опция: «сохранить документы и шаблоны» выбирается автоматически.",
		'import_temporary_dep' => "Вы выбрали опцию: «восстановить временные файлы». Для корректного функционирования временных файлов требуются соответствующие документы. Опция: «восстановить документы и шаблоны» выбирается автоматически.",
		'compress_file' => "Компрессировать файл",
		'export_options' => "Выберите файлы, предназначенные для сохранения.",
		'import_options' => "Выберите файлы, предназначенные для восстановления.",
		'extern_exp' => "Внимание! Выполнение данной операции займет продолжительное время и может привести к системным ошибкам.",
		'unselect_dep2' => "Вы отменили выбор '%s'. Выбор следующих опций отменяется автоматически:",
		'unselect_dep3' => "У Вас есть возможность повторного выбора опций, выбор которых ранее был отменен.",
		'gzip' => "gzip", // TRANSLATE
		'zip' => "zip", // TRANSLATE
		'bzip' => "bzip", // TRANSLATE
		'none' => "-",
		'cannot_split_file' => "Невозможно подготовить  файл '%s' к импортированию!",
		'cannot_split_file_ziped' => "Файл скомпрессирован методом, не поддерживаемым системой.",
		'export_banner_dep' => "You have selected the option 'Save banner data'. The banner data need the documents and because of that, 'Save documents and templates' has been automatically selected.", // TRANSLATE
		'import_banner_dep' => "You have selected the option 'Restore banner data'. The banner data need the documents data and because of that, 'Restore documents and templates' has been automatically selected.", // TRANSLATE

		'delold_notice' => "Рекомендуется предварительно удалить имеющиеся файлы.<br>Удалить?",
		'delold_confirm' => "Вы уверены, что хотите удалить все файлы с сервера?",
		'delete_entry' => "Удаление %s",
		'delete_nok' => "Этот файл не может быть удален!",
		'nothing_to_delete' => "Нет объекта удаления!",
		'files_not_deleted' => "Один или несколько файлов, предназначенных к удалению, полностью не удалены с сервера! По-видимому, эти файлы с защитой от записи! Их нужно удалить вручную. К ним относятся следующие файлы:",
		'delete_old_files' => "Delete old files...", // TRANSLATE

		'export_configuration_data' => "Сохранить конфигурацию",
		'import_configuration_data' => "Воссоздать конфигурацию",
		'import_export_data' => "Восстановить экспортируемые данные",
		'export_export_data' => "Сохранить экспортированные данные",
		'export_versions_data' => "Save version data", // TRANSLATE
		'export_versions_binarys_data' => "Save Version-Binary-Files", // TRANSLATE
		'import_versions_data' => "Restore version data", // TRANSLATE
		'import_versions_binarys_data' => "Restore Version-Binary-Files", // TRANSLATE

		'export_versions_dep' => "You have selected the option 'Save version data'. The version data need the documents, objects and version-binary-files and because of that, 'Save documents and templates', 'Save object and classes' and 'Save Version-Binary-Files' has been automatically selected.", // TRANSLATE
		'import_versions_dep' => "You have selected the option 'Restore version data'. The version data need the documents data, object data an version-binary-files and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore Version-Binary-Files' has been automatically selected.", // TRANSLATE

		'export_versions_binarys_dep' => "You have selected the option 'Save Version-Binary-Files'. The Version-Binary-Files need the documents, objects and version data and because of that, 'Save documents and templates', 'Save object and classes' and 'Save version data' has been automatically selected.", // TRANSLATE
		'import_versions_binarys_dep' => "You have selected the option 'Restore Version-Binary-Files'. The Version-Binary-Files need the documents data, object data an version data and because of that, 'Restore documents and templates', 'Restore objects and classes and 'Restore version data' has been automatically selected.", // TRANSLATE

		'del_backup_confirm' => "Удалить выбранный резервный файл?",
		'name_notok' => "Имя файла недействительно!",
		'backup_deleted' => "Резервный файл %s удален",
		'error_delete' => "Невозможно удалить резервный файл! Попробуйте его удалить с помощью FTP из директирии /webEdition/we_backup.",
		'core_info' => 'Все документы и шаблоны.',
		'object_info' => 'Объекты и классы модуля базы данных/объекта.',
		'binary_info' => 'Бинарные данные: изображения, PDF и прочие документы.',
		'user_info' => 'Данные пользователя модуля управления пользователыями.',
		'customer_info' => 'Данные о клиенте модуля управления клиентами.',
		'shop_info' => 'Данные по заказам интернет-магазина.',
		'workflow_info' => 'Данные модуля электронного документооборота.',
		'todo_info' => 'Сообщения и задачи модуля задач/сообщений.',
		'newsletter_info' => 'Данные модуля листа рассылки.',
		'banner_info' => 'Статистические данные и данные по баннерам модуля баннера/статистики.',
		'schedule_info' => 'Данные планировщика.',
		'settings_info' => 'Настройки по применению webEdition.',
		'temporary_info' => 'Данные из неопубликованных документов и объектов.',
		'export_info' => 'Данные модуля экспорта.',
		'glossary_info' => 'Data from the glossary.', // TRANSLATE
		'versions_info' => 'Data from Versioning.', // TRANSLATE
		'versions_binarys_info' => 'This option could take some time and memory because the folder /webEdition/we/versions/ could be very large. It is recommended to save this folder manually.', // TRANSLATE


		'import_voting_data' => "Сохранить данные голосования",
		'export_voting_data' => "Восстановить данные голосования",
		'voting_info' => 'Данные модуля голосования.',
		'we_backups' => 'Резервные файлы webEdition',
		'other_files' => 'Другие файлы',
		'filename_info' => 'Введите имя резервного файла.',
		'backup_log_exp' => 'Лог сохранен в /webEdition/we_backup/data/lastlog.php',
		'export_backup_log' => 'Создать лог',
		'download_file' => 'Сохранить файл',
		'import_file_found' => 'Этот файл должен быть импортирован в webEdition. Для импорта файла воспользуйтесь опцией \"Импорта/экспорта\" в пункте меню \"Файл\".',
		'customer_import_file_found' => 'Этот файл должен быть импортирован совместно с данными клиентов. Для импорта файла воспользуйтесь опцией \"Импорта/экспорта\" в  модуле управления клиентами (ПРО).',
		'import_file_found_question' => 'Закрыть данное диалоговое окно и запустить Мастер импорта/экспорта?',
		'format_unknown' => 'Неизвестный формат файла!',
		'upload_failed' => 'Невозможно загрузить файл. Убедитесь в том, что размер файла не превышает %s',
		'file_missing' => 'Не хватает резервного файла!',
		'recover_option' => 'Опции импорта',
		'no_resource' => 'Fatal Error: There are not enough resources to finish the backup!', // TRANSLATE
		'error_compressing_backup' => 'An error occured while compressing the backup, so the backup could not be finished!', // TRANSLATE
		'error_timeout' => 'An timeout occured while creating the backup, so the backup could not be finished!', // TRANSLATE

		'export_spellchecker_data' => "Save spellchecker data", // TRANSLATE
		'import_spellchecker_data' => "Restore spellchecker data", // TRANSLATE
		'spellchecker_info' => 'Data for spellchecker: settings, general and personal dictionaries.', // TRANSLATE

		'import_banner_data' => "Restore banner data", // TRANSLATE
		'export_banner_data' => "Save banner data", // TRANSLATE

		'export_glossary_data' => "Save glossary data", // TRANSLATE
		'import_glossary_data' => "Restore glossary data", // TRANSLATE

		'protect' => "Protect backup file", // TRANSLATE
		'protect_txt' => "The backup file will be protected from unprivileged download with additional php code. This protection requires additional disk space for import!", // TRANSLATE

		'recover_backup_unsaved_changes' => "Some open files have unsaved changes. Please check these before you continue.", // TRANSLATE
		'file_not_readable' => "The backup file is not readable. Please check the file permissions.", // TRANSLATE

		'tools_import_desc' => "Here you can restore webEdition tools data. Please select the desired tools from the list.", // TRANSLATE
		'tools_export_desc' => "Here you can save webEdition tools data. Please select the desired tools from the list.", // TRANSLATE

		'ftp_hint' => "Attention! Use the Binary mode for the download by FTP if the backup file is zip compressed! A download in ASCII 	mode destroys the file, so that it cannot be recovered!", // TRANSLATE

		'convert_charset' => "Attention! Using this option in an existing site can lead to total loss of all data, please follow the instruction in http://documentation.webedition.org/de/webedition/administration/charset-conversion-of-legacy-sites", // TRANSLATE

		'convert_charset_data' => "While importing the backup, convert the site from ISO to UTF-8", // TRANSLATE

		'view_log' => "Backup-Log", // TRANSLATE
		'view_log_not_found' => "The backup log file was not found! ", // TRANSLATE
		'view_log_no_perm' => "You do not have the needed permissions to view the backup log file! ", // TRANSLATE
);