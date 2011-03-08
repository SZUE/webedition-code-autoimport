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
 * Language file: global.inc.php
 * Provides language strings.
 * Language: English
 */
$l_global = array(
		'categorys' => "Категории",
		'navigation' => "Navigation", // TRANSLATE
		'new_link' => "Новая ссылка", // It is important to use the GLOBALS ARRAY because in linklists, the file is included in a function.
		'load_menu_info' => "Загрузка данных!<br>Загрузка нескольких элементов меню займет некоторое время",
		'text' => "Текст",
		'yes' => "да",
		'no' => "нет",
		'checked' => "В действии",
		'max_file_size' => "Максимальный размер файла (в байтах)",
		'default' => "По умолчанию",
		'values' => "Значения",
		'name' => "Имя",
		'type' => "Тип",
		'attributes' => "Атрибуты",
		'formmailerror' => "Форма не отправлена по следующим причинам:",
		'email_notallfields' => "Вы не заполнили все поля обязательные к заполнению!",
		'email_ban' => "Вы не уполномочены использовать данный Script!",
		'email_recipient_invalid' => "Адрес получателя введен неверно!",
		'email_no_recipient' => "Адреса получателя не существует!",
		'email_invalid' => "Ваш электронный <b>адрес</b> недействителен!",
		'captcha_invalid' => "The entered security code is wrong!", // TRANSLATE
		'question' => "Вопрос",
		'warning' => "Внимание",
		'we_alert' => "Данная функция не входит в демо-версию системы webEdition!",
		'index_table' => "Таблица индексов",
		'cannotconnect' => "Нет соединения с сервером webEdition!",
		'recipients' => "Получатели писем Formmail",
		'recipients_txt' => "Введите, пожалуйста, все электронные адреса для рассылки форм с помощью функции Formmail (&lt;we:form type=&quot;formmail&quot; ..&gt;). Если адрес рассылки не введен, невозможно воспользоваться функцией рассылки Formmail!",
		'std_mailtext_newObj' => "Создан новый объект %s класса %s!",
		'std_subject_newObj' => "Новый объект",
		'std_subject_newDoc' => "Новый документ",
		'std_mailtext_newDoc' => "Создан новый документ %s!",
		'std_subject_delObj' => "Объект удален",
		'std_mailtext_delObj' => "Объект %s удален!",
		'std_subject_delDoc' => "Документ удален",
		'std_mailtext_delDoc' => "Документ %s удален!",
		'we_make_same' => array(
				'text/html' => "После сохранения новая страница",
				'text/webedition' => "После сохранения новая страница",
				'objectFile' => "New object after saving",
		),
		'no_entries' => "Данные не найдены!",
		'save_temporaryTable' => "Пересохранить временные документы",
		'save_mainTable' => "Пересохранить главную таблицу базы данных",
		'add_workspace' => "Добавить рабочее пространство",
		'folder_not_editable' => "Данная директория не может быть выбрана!",
		'modules' => "Модули",
		'modules_and_tools' => "Modules and Tools", // TRANSLATE
		'center' => "Центровка",
		'jswin' => "Окно Popup",
		'open' => "Открыть",
		'posx' => "Положение x",
		'posy' => "Положение y",
		'status' => "Status", // TRANSLATE
		'scrollbars' => "Scrollbars",
		'menubar' => "Menubar",
		'toolbar' => "Toolbar", // TRANSLATE
		'resizable' => "Resizable", // TRANSLATE
		'location' => "Location", // TRANSLATE
		'title' => "Титул/звание",
		'description' => "Описание",
		'required_field' => "Обязательное к заполнению поле",
		'from' => "из",
		'to' => "до",
		'search' => "Поиск",
		'in' => "в",
		'we_rebuild_at_save' => "Перестроить (rebuild)",
		'we_publish_at_save' => "После сохранения опубликовать",
		'we_new_doc_after_save' => "New Document after saving", // TRANSLATE
		'we_new_folder_after_save' => "New folder after saving", // TRANSLATE
		'we_new_entry_after_save' => "New entry after saving", // TRANSLATE
		'wrapcheck' => "Обрыв строки (Wrapping)",
		'static_docs' => "Статические документы",
		'save_templates_before' => "Предварительно пересохранить шаблоны",
		'specify_docs' => "Документы со следующими критериями:",
		'object_docs' => "Все объекты",
		'all_docs' => "Все документы",
		'ask_for_editor' => "Предварительно запросить редактор",
		'cockpit' => "Cockpit", // TRANSLATE
		'introduction' => "Введение",
		'doctypes' => "Типы документов",
		'content' => "Содержимое",
		'site_not_exist' => "Страница не существует!",
		'site_not_published' => "Страница еще не опубликована!",
		'required' => "Введите данные",
		'all_rights_reserved' => "Все права защищены",
		'width' => "Ширина",
		'height' => "Высота",
		'new_username' => "Новое имя пользователя",
		'username' => "Имя пользователя",
		'password' => "Пароль",
		'documents' => "Документы",
		'templates' => "Шаблоны",
		'objects' => "Objects", // TRANSLATE
		'licensed_to' => "Владелец лицензии",
		'left' => "по левой стороне",
		'right' => "по правой стороне",
		'top' => "по верхней стороне",
		'bottom' => "по нижней стороне",
		'topleft' => "по левому верхнему углу",
		'topright' => "по правому верхнему углу",
		'bottomleft' => "по левому нижнему углу",
		'bottomright' => "по правому верхнему углу",
		'true' => "Да",
		'false' => "Нет",
		'showall' => "Показать все",
		'noborder' => "Без границ",
		'border' => "Граница",
		'align' => "Центровка",
		'hspace' => "Горизонталь",
		'vspace' => "Вертикаль",
		'exactfit' => "Exactfit",
		'select_color' => "Выберите цвет",
		'changeUsername' => "Изменить имя пользователя",
		'changePass' => "Изменить пароль",
		'oldPass' => "Старый пароль",
		'newPass' => "Новый пароль",
		'newPass2' => "Повторите новый пароль",
		'pass_not_confirmed' => "Повторно введенный пароль не соответствует новому паролю, веденному ранее!",
		'pass_not_match' => "Старый пароль введен неверно!",
		'passwd_not_match' => "Пароль введен неверно!",
		'pass_to_short' => "Пароль должен содержать не менее 4 символов!",
		'pass_changed' => "Пароль успешно изменен!",
		'pass_wrong_chars' => "Пароли должны содержать только буквы латинского алфавита и цифры (a-z, A-Z и 0-9)!",
		'username_wrong_chars' => "Username may only contain alpha-numeric characters (a-z, A-Z and 0-9) and '.', '_' or '-'!", // TRANSLATE
		'all' => "Все",
		'selected' => "выделены",
		'username_to_short' => "Имя пользователя должно содержать не менее 4 символов!",
		'username_changed' => "Имя пользователя успешно изменено!",
		'published' => "Опубликовано",
		'help_welcome' => "Добро пожаловать в службу помощи webEdition!",
		'edit_file' => "Редактировать файл",
		'docs_saved' => "Документы успешно сохранены!",
		'preview' => "Предварительный просмотр",
		'close' => "Закрыть окно",
		'loginok' => "<strong>Login ok! Пожалуйста, подождите!</strong><br>webEdition откроется в новом окне. В случае, если этого не произошло, убедитесь в том, что Вы не заблокировали окна pop-up в Вашем браузере!",
		'apple' => "&#x2318;", // TRANSLATE
		'shift' => "SHIFT", // TRANSLATE
		'ctrl' => "CTRL", // TRANSLATE
		'required_fields' => "Поля, обязательные к заполнению",
		'no_file_uploaded' => "<p class=\"defaultfont\">На данный момент документ еще не загружен.</p>",
		'openCloseBox' => "Открыть/Закрыть",
		'rebuild' => "Перестроить",
		'unlocking_document' => "дать доступ к документу",
		'variant_field' => "Поле варианта",
		'redirect_to_login_failed' => "Please press the following link, if you are not redirected within the next 30 seconds ", // TRANSLATE
		'redirect_to_login_name' => "webEdition login", // TRANSLATE
		'untitled' => "Untitled", // TRANSLATE
		'no_document_opened' => "There is no document opened!", // TRANSLATE
		'credits_team' => "webEdition Team", // TRANSLATE
		'developed_further_by' => "developed further by", // TRANSLATE
		'with' => "with the", // TRANSLATE
		'credits_translators' => "Translations", // TRANSLATE
		'credits_thanks' => "Thanks to", // TRANSLATE
		'unable_to_call_ping' => "Connection to server is lost - RPC: Ping!", // TRANSLATE
		'unable_to_call_setpagenr' => "Connection to server is lost - RPC: setPageNr!", // TRANSLATE
		'nightly-build' => "nightly Build", // TRANSLATE
		'alpha' => "Alpha", // TRANSLATE
		'beta' => "Beta", // TRANSLATE
		'rc' => "RC", // TRANSLATE
		'preview' => "preview", // TRANSLATE
		'release' => "official release", // TRANSLATE
);
