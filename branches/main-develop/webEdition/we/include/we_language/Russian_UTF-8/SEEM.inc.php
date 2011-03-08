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
 * Language file: SEEM.inc.php
 * Provides language strings.
 * Language: English
 */
$l_SEEM = array(
		'ext_doc_selected' => "Вы нажали на ссылку, указывающую на документ, который, по-видимому, не управляется системой webEdition. Продолжить?",
		'ext_document_on_other_server_selected' => "Вы нажали на ссылку, которая указывает на документ другого веб-сервера. \\nДокумент откроется в новом окне браузера. Продолжить?",
		'ext_form_target_other_server' => "Вы хотите отправить форму на другой веб-сервер. \\nПри этом откроется новое окно браузера. Продолжить?",
		'ext_form_target_we_server' => "Эта форма отправляет данные документу, который не управляется системой webEdition. \\nПродолжить?",
		'ext_doc' => "Текущий документ: <b>%s</b> <u>не</u> относится к странице, обслуживаемой системой webEdition",
		'ext_doc_not_found' => "Указанная страница <b>%s</b> не найдена",
		'ext_doc_tmp' => "При открытии указанного документа/страницы в системе webEdition произошел сбой. Чтобы выйти на требуемый документ, воспользуйтесь, пожалуйста, привычной навигацией сайта.",
		'info_ext_doc' => "Ссылка не относится к webEdition",
		'info_doc_with_parameter' => "Ссылка с параметром",
		'link_does_not_work' => "Данная ссылка деактивирована в режиме предварительного просмотра. \nДля навигации по странице используйте, пожалуйста, панель главного меню.",
		'info_link_does_not_work' => "Деактивировано",
		'open_link_in_SEEM_edit_include' => "Вы собираетесь изменить содержание главного окна webEdition. При этом текущее окно закроется. Продолжить?",
//  Used in we_info.inc.php
		'start_mode' => "Режим",
		'start_mode_normal' => "стандартный",
		'start_mode_seem' => "суперлегкий",
//	When starting webedition in SEEMode
		'start_with_SEEM_no_startdocument' => "Для Вас не задан действующий стартовый документ.\nСтартовый документ задается администратором.",
		'only_seem_mode_allowed' => "У Вас нет полномочий для запуска webEdition в стандартном режиме.\\nЗапускается суперлегкий режим.",
//	Workspace - the SEEM startdocument
		'workspace_seem_startdocument' => "Стартовый документ<br>суперлегкого<br>режима",
//	Desired document is locked by another user
		'try_doc_again' => "Попробуйте еще раз",
//	no permission to work with document
		'no_permission_to_work_with_document' => "У Вас нет полномочий редактировать данный документ",
//	started seem with no startdocument - can select startdocument.
		'question_change_startdocument' => "Для Вас не задан действующий стартовый документ.\\nЗадать стартовый документ в диалоге настроек?",
//	started seem with no startdocument - can select startdocument.
		'no_permission_to_edit_document' => "У Вас нет полномочий редактировать данный документ",
		'confirm' => array(
				'change_to_preview' => "Перейти в режим предварительного просмотра?",
		),
		'alert' => array(
				'changed_include' => "Вложенный файл был изменен. Перезагружается главное окно.",
				'close_include' => "This file is no webEdition document. The include window is closed.", // TRANSLATE
		),
);