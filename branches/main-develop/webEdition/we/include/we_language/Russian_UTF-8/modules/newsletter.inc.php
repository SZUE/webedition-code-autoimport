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
 * Language file: newsletter.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_newsletter = array(
		'save_changed_newsletter' => "Newsletter has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'Enter_Path' => "Введите путь, который начинается с DOCUMENT_ROOT!",
		'title_or_salutation' => "Английский титул/степень (не обращение)",
		'global_mailing_list' => "Список адресов рассылки по умолчанию (файл CSV)",
		'new_newsletter' => "Новый лист рассылки",
		'newsletter' => "Лист рассылки",
		'new' => "Новый",
		'save' => "Сохранить",
		'delete' => "Удалить",
		'quit' => "Выйти",
		'help' => "Помощь",
		'info' => "Справка",
		'options' => "Опции",
		'send_test' => "Отправить пробное письмо",
		'domain_check' => "Проверить домен",
		'send' => "Отправить",
		'preview' => "Предварительный просмотр",
		'settings' => "Настройки",
		'show_log' => "Показать книгу записей",
		'mailing_list' => "Список для рассылки %s",
		'customers' => "Клиенты",
		'emails' => "Emails",
		'newsletter_content' => "Содержание листа рассылки",
		'type_doc' => "Документы",
		'type_object' => "Объекты",
		'type_file' => "Файл",
		'type_text' => "Текст",
		'attchments' => "Приложения",
		'name' => "Имя",
		'no_perms' => "Нет полномочий",
		'nothing_to_delete' => "Отсутствует предмет удаления",
		'documents' => "Документы",
		'save_ok' => "Лист рассылки сохранен",
		'message_description' => "Задать содержимое листу рассылки",
		'sender' => "Отправитель",
		'reply' => "Ответить (кому)",
		'reply_same' => "как адрес отправителя",
		'block_type' => "Тип блока",
		'block_document' => "Документ",
		'block_document_field' => "Поле документа",
		'block_object' => "Объект",
		'block_object_field' => "Поле объекта",
		'block_file' => "Файл",
		'block_html' => "HTML", // TRANSLATE
		'block_plain' => "Только текст",
		'block_newsletter' => "Лист рассылки",
		'block_attachment' => "Приложение",
		'block_lists' => "Списки адресов рассылки",
		'block_all' => "----   Все   ----",
		'block_template' => "Шаблон",
		'block_url' => "URL", // TRANSLATE
		'use_default' => "Использовать шаблон по умолчанию",
		'subject' => "Тема",
		'delete_question' => "Удалить текущий лист рассылки?",
		'delete_group_question' => "Do you want to delete the current group?", // TRANSLATE
		'delete_ok' => "Лист рассылки удален",
		'delete_nok' => "ОШИБКА: лист рассылки не был удален!",
		'test_email' => "Пробное письмо",
		'test_email_question' => "Пробное письмо будет отправлено на тестовый электронный адрес %s!\\n Продолжить?",
		'test_mail_sent' => "Пробное письмо отправлено на тестовый электронный адрес %s",
		'malformed_mail_group' => "Список адресов рассылки %s содержит недействительный адрес '%s'!\\nЛист рассылки не сохранен!",
		'malformed_mail_sender' => "Адрес отправителя '%s' недействительный!\\nЛист рассылки не сохранен!",
		'malformed_mail_reply' => "Обратный адрес '%s' недействительный!\\nЛист рассылки не сохранен!",
		'malformed_mail_test' => "Тестовый адрес '%s' недействительный!\\nЛист рассылки не сохранен!",
		'send_question' => "Отправить лист рассылки по адресам рассылок?",
		'send_test_question' => "Это тест (без отправки листа рассылки)\\nПодтвердите для того, чтобы продолжить",
		'domain_ok' => "Домен %s проверен",
		'domain_nok' => "Домен %s невозможно проверить",
		'email_malformed' => "Адрес %s недействителен",
		'domain_check_list' => "Проверка доменов по списку адресов рассылки %s",
		'domain_check_begins' => "Проверка доменов началась",
		'domain_check_ends' => "Проверка доменов завершена",
		'newsletter_type_0' => "Документ",
		'newsletter_type_1' => "Поле документа",
		'newsletter_type_2' => "Объект",
		'newsletter_type_3' => "Поле объекта",
		'newsletter_type_4' => "Файл",
		'newsletter_type_5' => "Текст",
		'newsletter_type_6' => "Приложение",
		'newsletter_type_7' => "URL", // TRANSLATE
		'all_list' => "-- Все списки рассылок --",
		'newsletter_test' => "Тест",
		'send_to_list' => "Отправить по адресам рассылки списка %s.",
		'campaign_starts' => "Кампания по рассылке началась...",
		'campaign_ends' => "Кампания по рассылке завершена",
		'test_no_mail' => "Тестирование - без рассылки писем...",
		'sending' => "Разослать...",
		'mail_not_sent' => "Письмо '%s' не может быть отправлено.",
		'filter' => "Фильтр",
		'send_all' => "Отправить всем",
		'lists_overview_menu' => "Обзор списков рассылки",
		'lists_overview' => "Обзор списков рассылки",
		'copy' => "Копировать",
		'copy_newsletter' => "Копировать лист рассылки",
		'continue_camp' => "Предыдущая кампания по рассылке листа рассылки не завершена!<br>Есть возможность продолжить предыдущую кампанию.<br>Продолжить предыдущую кампанию по рассылке?",
		'reject_malformed' => "Письмо не отправлять в случае, если адрес недействителен",
		'reject_not_verified' => "Письмо не отправлять в случае, если невозможно проверить адрес",
		'send_step' => "Количество писем в одной рассылке",
		'test_account' => "Тестовый адрес",
		'log_sending' => "Вносить запись в журнал при отправке письма",
		'default_sender' => "Отправитель по умолчанию",
		'default_reply' => "Обратный адрес по умолчанию",
		'default_htmlmail' => "Формат письма HTML по умолчанию",
		'isEmbedImages' => "Embed images", // TRANSLATE
		'ask_to_preserve' => "Предыдущая кампания по рассылке листа рассылки окончательно не завершена!<br>В случае, если Вы сейчас сохраните данный лист рассылки, Вы не сможете завершить предыдущую кампанию по рассылке!<br>Продолжить?",
		'log_save_newsletter' => "Лист рассылки сохранен",
		'log_start_send' => "Начать кампанию по рассылке",
		'log_end_send' => "Кампания по рассылке успешно завершена",
		'log_continue_send' => "Кампания по рассылке в действии...",
		'log_campaign_reset' => "Параметры кампании по рассылке были изменены",
		'mail_sent' => "Лист рассылки отправлен по адресу %s.",
		'must_save' => "Лист рассылки изменен.\\nПеред отправкой Вы должны сохранить изменения!",
		'email_exists' => "Такой адрес уже существует!",
		'email_max_len' => "Адрес email не должен превышать 255 символов!",
		'no_email' => "Адрес email не выбран!",
		'email_new' => "Введите, пожалуйста, email адрес!",
		'email_delete' => "Удалить выделенные email адреса?",
		'email_delete_all' => "Удалить все email адреса?",
		'email_edit' => "Адрес email изменен!",
		'nothing_to_save' => "Нет предмета сохранения!",
		'csv_delimiter' => "Разделительный знак",
		'csv_col' => "Колонка email",
		'csv_hmcol' => "Колонка HTML",
		'csv_salutationcol' => "Колонка обращения",
		'csv_titlecol' => "Колонка титула, звания",
		'csv_firstnamecol' => "Колонка имени",
		'csv_lastnamecol' => "Колонка фамилии",
		'csv_export' => "Файл '%s' сохранен",
		'customer_email_field' => "Поле еmail клиента",
		'customer_html_field' => "Поле HTML клиента",
		'customer_salutation_field' => "Поле обращения к клиенту",
		'customer_title_field' => "Поле титула/степени клиента",
		'customer_firstname_field' => "Поле имени клиента",
		'customer_lastname_field' => "Поле фамилии клиента",
		'csv_html_explain' => "(0 - без колонки HTML)",
		'csv_salutation_explain' => "(0 - без колонки обращения)",
		'csv_title_explain' => "(0 - без колонки титула/степени)",
		'csv_firstname_explain' => "(0 - без колонки имени)",
		'csv_lastname_explain' => "(0 - без колонки фамилии)",
		'email' => "Email",
		'lastname' => "Фамилия",
		'firstname' => "Имя",
		'salutation' => "Обращение",
		'title' => "Титул",
		'female_salutation' => "Обращение к женщине",
		'male_salutation' => "Обращение к мужчине",
		'edit_htmlmail' => "HTML email",
		'htmlmail_check' => "HTML", // TRANSLATE
		'double_name' => "Название листа рассылки уже существует.",
		'cannot_preview' => "Предварительный просмотр листа рассылки невозможен",
		'empty_name' => "Не заполнено имя!",
		'edit_email' => "Редактировать адреса email",
		'add_email' => "Добавить адрес email",
		'none' => "-- Отсутствуют --",
		'must_save_preview' => "Лист рассылки изменен.\\nПеред его предварительным просмотром нужно сохранить изменения!",
		'black_list' => "Черный список",
		'email_is_black' => "Адрес еmail в черном списке!",
		'upload_nok' => "Невозможно загрузить файл.",
		'csv_download' => "Загрузить csv файл",
		'csv_upload' => "Загрузить csv",
		'finished' => "Завершено",
		'cannot_open' => "Невозможно раскрыть файл!",
		'search_email' => "Поиск адреса еmail...",
		'search_text' => "Введите, пожалуйста, адрес email",
		'search_finished' => "Поиск завершен\\nНайдено: %s",
		'email_double' => "Адрес email %s уже существует!",
		'error' => "ОШИБКА",
		'warning' => "ВНИМАНИЕ",
		'file_email' => "CSV files", // TRANSLATE
		'edit_file' => "Edit CSV file", // TRANSLATE
		'show' => "Show", // TRANSLATE
		'no_file_selected' => "No file selected!", // TRANSLATE
		'file_is_empty' => "The CSV file is empty", // TRANSLATE
		'file_all_ok' => "The CSV file has no invalid entries", // TRANSLATE
		'del_email_file' => "Delete E-mail '%s'?",
		'email_missing' => "Missing E-mail address",
		'yes' => "Yes", // TRANSLATE
		'no' => "No", // TRANSLATE
		'select_file' => "Select file", // TRANSLATE
		'clear_log' => "Clear logbook", // TRANSLATE
		'clearlog_note' => "Do you really want to clear all entries from the logbook?", // TRANSLATE
		'log_is_clear' => "Logbook is cleared.", // TRANSLATE
		'property' => "Properties", // TRANSLATE
		'edit' => "Edit", // TRANSLATE
		'details' => "Details", // TRANSLATE
		'path' => "Path", // TRANSLATE
		'dir' => "Directory", // TRANSLATE
		'block' => "Block %s", // TRANSLATE
		'new_newsletter_group' => "New group", // TRANSLATE
		'group' => "Group", // TRANSLATE
		'path_nok' => "Путь неверный!",
		'save_group_ok' => "Группа листов рассылки сохранена",
		'delete_group_ok' => "Группа листов рассылки удалена",
		'delete_group_nok' => "ОШИБКА: Группа листов рассылки не удалена!",
		'path_not_valid' => "The path is not valid", // TRANSLATE
		'no_subject' => "The subject field is empty. Do you really want to send the newsletter?", // TRANSLATE
		'mail_failed' => " E-mail '%s' cannot be sent. A possible cause is an incorrect server configuration.",
		'reject_save_malformed' => "Do not save newsletter if address is malformed.", // TRANSLATE
		'rfc_email_check' => "Validate conform to rfc 3696.<br>WARNIGN: This validation can take heavy influence on the speed of your server.", // TRANSLATE
		'use_https_refer' => "Use HTTPS for reference", // TRANSLATE
		'use_base_href' => "Use &lt;base href=... in head", // TRANSLATE
		'we_filename_notValid' => "Введенное имя недействительно!\\nДопустимыми символами являются буквы от a до z (большие и малые), числа, нижняя черта (_), тире (-), точка (.) и пробел ( ).",
		'send_wait' => "Wait period to next load (ms)", // TRANSLATE
		'send_images' => "Send images", // TRANSLATE
		'prepare_newsletter' => "Preparation...", // TRANSLATE
		'use_port_check' => "Использовать порт для перенаправления",
		'use_port' => "порт",
		'sum_group' => "адрес(а) E-Mail в списке %s",
		'sum_all' => "адреса E-Mail всех списков",
		'retry' => "повторить",
		'charset' => "Кодировка символов",
		'additional_clp' => "Additional reply address (option -f)", // TRANSLATE
		'html_preview' => "show HTML preview", // TRANSLATE
		'status' => "Status", // TRANSLATE
		'statusAll' => "all entries", // TRANSLATE
		'statusInvalid' => "invalid entries", // TRANSLATE
		'invalid_email' => "The email is not valid.", // TRANSLATE
		'blockFieldError' => "Invalid value in Block %s, Field %s!",
		'operator' => array(
				'startWith' => "starts with", // TRANSLATE
				'endsWith' => "ends with", // TRANSLATE
				'contains' => "contains", // TRANSLATE
		),
		'logic' => array(
				'and' => "and", // TRANSLATE
				'or' => "or", // TRANSLATE
		),
		'default' => array(
				'female' => "Mrs.", // TRANSLATE
				'male' => "Mr.", // TRANSLATE
		),
		'no_newsletter_selected' => "No newsletter selected. Please open the newsletter first.", // TRANSLATE
);