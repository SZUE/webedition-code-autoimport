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
$l_modules_voting = array(
		'no_perms' => 'You do not have permission to use this option.',
		'delete_alert' => 'Удалить текущее голосование/группу.\\n Вы уверены?',
		'result_delete_alert' => 'Delete the current voting results.\\nAre you sure?', // TRANSLATE
		'nothing_to_delete' => 'Нет предмета удаления!',
		'nothing_to_save' => 'Нет предмета сохранения',
		'we_filename_notValid' => 'Недействительное имя пользователя!\\nДействительными символами являются большие и малые буквы латинского алфавита, цифры, нижняя черта, тире, точка и пробел (a-z, A-Z, 0-9, _, -, ., )',
		'menu_new' => 'Новое',
		'menu_save' => 'Сохранить',
		'menu_delete' => 'Удалить',
		'menu_exit' => 'Закрыть',
		'menu_info' => 'Справка',
		'menu_help' => 'Помощь',
		'headline' => 'Имя и фамилия',
		'headline_name' => 'Имя',
		'headline_publish_date' => 'Создать дату',
		'headline_data' => 'Данные вопросника',
		'publish_date' => 'Дата',
		'publish_format' => 'Формат',
		'published_on' => 'Дата опубликования',
		'total_voting' => 'Общее голосование',
		'reset_scores' => 'Сброс голосов',
		'inquiry_question' => 'Вопрос',
		'inquiry_answers' => 'Ответы',
		'question_empty' => 'Поле для вопроса не заполнено, введите пожалуйста вопрос!',
		'answer_empty' => 'Не заполнено одно или несколько полей для ответов, введите пожалуйста ответ!',
		'invalid_score' => 'Результат голосования должен иметь численное значение, попробуйте еще раз!',
		'headline_revote' => 'Повторное голосование',
		'headline_help' => 'Помощь',
		'inquiry' => 'Вопросник',
		'browser_vote' => 'Данный браузер может снова голосовать не ранее, чем через',
		'one_hour' => 'час',
		'feethteen_minutes' => '15 мин.',
		'thirthty_minutes' => '30 мин.',
		'one_day' => 'день',
		'never' => '--никогда--',
		'always' => '--всегда--',
		'cookie_method' => 'Методом маркёров',
		'ip_method' => 'Методом IP',
		'time_after_voting_again' => 'Время до следующего голосования',
		'cookie_method_help' => 'В случае, если Вы не можете воспользоваться методом IP, выберите данный метод. Примите во внимание то, что некоторые пользователи могли заблокировать маркёры (cookies) в браузерах.',
		'ip_method_help' => 'В случае, если Ваш веб-сайт работает в сети Intranet и пользователи не могут соединиться с помощью прокси сервера, следует выбрать данный метод. При этом нужно принять во внимание, что некоторые серверы распределяют динамические адреса IP.',
		'time_after_voting_again_help' => 'Во избежание многократного голосования одним браузером/IP за короткие промежутки времени, следует установить нужный промежуток времени, по прошествии которого такой браузер сможет повторно принимать участие в голосовании. Для однократного голосования браузера следует выбрать --никогда--.',
		'property' => 'Свойства',
		'variant' => 'Версия',
		'voting' => 'Голосование',
		'result' => 'Результат',
		'group' => 'Группа',
		'name' => 'Имя',
		'newFolder' => 'Новая группа',
		'save_group_ok' => 'Группа сохранена',
		'save_ok' => 'Голосование сохранено',
		'path_nok' => 'Путь неверен!',
		'name_empty' => 'Поле имени не должно быть пустым!',
		'name_exists' => 'Имя уже существует!',
		'wrongtext' => ' Имя недействительно!',
		'voting_deleted' => 'Голосование успешно удалено',
		'group_deleted' => 'Группа успешно удалена',
		'access' => 'Доступ',
		'limit_access' => 'Ограниченный доступ',
		'limit_access_text' => 'Разрешить доступ следующим пользователям',
		'variant_limit' => 'В вопроснике должна быть указана по меньшей мере одна версия!',
		'answer_limit' => 'Вопросник должен содержать по крайней мере два ответа!',
		'valid_txt' => 'Для  сохранения результатов голосования на Вашей странице, а также для «парковки», по истечении срока действия голосования, должно быть активировано окошко «действует». С помощью выпадающего меню определяется срок (дата и время), до которого проводится голосование. По истечении выбранной Вами даты участие в данном голосовании не засчитывается.',
		'active_till' => 'Действует до',
		'valid' => 'Действие',
		'export' => 'Экспорт',
		'export_txt' => 'Экспортировать данные голосования как файл CSV (comma separated values).',
		'csv_download' => "Загрузить файл CSV",
		'csv_export' => "Файл '%s' сохранён",
		'fallback' => 'Метод Fallback IP',
		'save_user_agent' => 'Сохранить/сравнить данные пользователя-агента',
		'save_changed_voting' => "Voting has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'voting_log' => 'Вести протокол голосования',
		'forbid_ip' => 'Заблокировать следующие адреса IP',
		'until' => 'до',
		'options' => 'опции',
		'control' => 'контроль',
		'data_deleted_info' => 'Данные удалены!',
		'time' => 'Время',
		'ip' => 'IP', // TRANSLATE
		'user_agent' => 'Пользователь-агент',
		'cookie' => 'Cookie', // TRANSLATE
		'delete_ipdata_question' => 'Вы собираетесь удалить все сохраненные данные IP. Вы уверены?',
		'delete_log_question' => 'Вы собираетесь удалить все сохраненные лог-записи голосования.  Вы уверены?',
		'delete_ipdata_text' => 'Сохраненные данные IP занимают %s байтов памяти. Вы можете их удалить с помощью кнопки \Удалить\'. Примите во внимание, что удаление сохраненных данных IP может вызвать неточность результатов голосования, так как в этом случае возможно многократное участие в голосовании.',
		'status' => 'Состояние',
		'log_success' => 'Успех',
		'log_error' => 'Ошибка',
		'log_error_active' => 'Ошибка: не активен',
		'log_error_revote' => 'Ошибка: новое голосование',
		'log_error_blackip' => 'Ошибка: IP заблокирован',
		'log_is_empty' => 'Книга лог-записей не заполнена!',
		'enabled' => 'Активировано',
		'disabled' => 'Не активировано',
		'log_fallback' => 'Fallback', // TRANSLATE

		'new_ip_add' => 'Введите, пожалуйста, новый адрес IP!',
		'not_valid_ip' => 'Адрес IP недействителен!',
		'not_active' => 'The entered date is in the past!', // TRANSLATE

		'headline_datatype' => 'Type of Inquiry', // TRANSLATE
		'AllowFreeText' => 'Allow free text', // TRANSLATE
		'AllowImages' => 'Allow images', // TRANSLATE
		'AllowSuccessor' => 'redirect to:', // TRANSLATE
		'AllowSuccessors' => 'allow individual redirects', // TRANSLATE
		'csv_charset' => "Export charset", // TRANSLATE
		'imageID_text' => "Image ID", // TRANSLATE
		'successorID_text' => "Successor ID", // TRANSLATE
		'mediaID_text' => "Media-ID", // TRANSLATE
		'AllowMedia' => 'Allow Media such as Audio or video files', // TRANSLATE

		'voting-id' => 'Voting ID', // TRANSLATE
		'voting-session' => 'Voting Session', // TRANSLATE
		'voting-successor' => 'successor', // TRANSLATE
		'voting-additionalfields' => 'add. data', // TRANSLATE
		'answerID' => 'answer ID', // TRANSLATE
		'answerText' => 'answer text', // TRANSLATE

		'userid_method' => 'For logged in Users (customer management), compare to saved customer ID (the log has to be active)', // TRANSLATE
		'IsRequired' => 'This is a required field', // TRANSLATE

		'answer_limit' => 'The inquiry must consist of at least two - in case free text answers are allowd one - answers!', // TRANSLATE
		'folder_path_exists' => "Folder already exists!", // TRANSLATE
);