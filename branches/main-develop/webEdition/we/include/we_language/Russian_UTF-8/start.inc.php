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
 * Language file: start.inc.php
 * Provides language strings.
 * Language: English
 */
$l_start = array(
		'we_homepage' => 'http://www.webedition.org/',
		'phpini_problems' => 'Возникли проблемы с настройками сессии (session-settings) в php.ini-file%s!',
		'tmp_path' => "Переменная <b>session.save_path</b> указывает на '%s'. На Вашем сервере данной директории не существует!",
		'use_cookies' => "Переменной <b>session.use_cookies</b> не было присвоено значение. Она должна быть равна 1!",
		'cookie_path' => "Значение переменной <b>session.cookie_path</b> равно '%s'. Ее значение должно быть равным '/'!",
		'solution_one' => "В случае, если Вы пользуетесь собственным сервером, измените, пожалуйста, данное значение. Если Вы пользуетесь услугами провайдера, проинформируйте его о том, что данное значение установлено неверно!",
		'solution_more' => "В случае, если Вы пользуетесь собственным сервером, измените, пожалуйста, данные значения. Если Вы пользуетесь услугами провайдера, проинформируйте его о том, что данные значения установлены неверно!",
		'cannot_start_we' => "Невозможно запустить webEdition",
		'browser_not_supported' => "Ваш браузер не поддерживается системой webEdition!",
		'browser_supported' => "Система webEdition поддерживает следующие браузеры:",
		'browser_ie' => "Internet Explorer", // TRANSLATE
		'browser_ie_version' => "Начиная с 5.5 версии",
		'browser_firefox' => "Firefox", // TRANSLATE
		'browser_firefox_version' => "Начиная с 1.0 версии",
		'browser_safari' => "Safari", // TRANSLATE
		'browser_safari_version' => "Начиная с 1.1 версии",
		'ignore_browser' => "Если Вы все же хотите запустить систему webEdition, нажмите здесь ...",
		'no_db_connection' => "The database connection can not be established.", // TRANSLATE
		'cookies_disabled' => "Cookies are deactivated.", // TRANSLATE
);