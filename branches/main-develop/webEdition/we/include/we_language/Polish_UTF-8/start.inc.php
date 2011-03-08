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
		'we_homepage' => 'http://www.webedition.org/', // TRANSLATE

		'phpini_problems' => 'Są problemy z ustawieniami sesji dla pliku php.ini %s!',
		'tmp_path' => "Zmienna <b>session.save_path</b> wskazuje '%s'. Taki katalog nie istnieje na serwerze!",
		'use_cookies' => "Zmienna <b>session.use_cookies</b> nie jest ustawiona. Proszę ustawić wartość na 1!",
		'cookie_path' => "Zmienna <b>session.cookie_path</b> ma wartość '%s'. Wartość ta powina wynosić normalnie '/'!",
		'solution_one' => "W przypadku korzystania z własnego serwera, należy zmienić tą wartość. W przypadku korzystania z usług providera, należy go poinformować o błędnych ustawieniach wartości!",
		'solution_more' => "W przypadku korzystania z własnego serwera, należy zmienić te wartości. W przypadku korzystania z usług providera, należy go poinformować o błędnych ustawieniach wartości!",
		'cannot_start_we' => "webEdition nie może zostać uruchomiony",
		'browser_not_supported' => "Twoja przeglądarka nie jest obsługiwana przez webEdition!",
		'browser_supported' => "webEdition obsługuje następujące przeglądarki:",
		'browser_ie' => "Internet Explorer", // TRANSLATE
		'browser_ie_version' => "od wersji 5.5",
		'browser_firefox' => "Firefox", // TRANSLATE
		'browser_firefox_version' => "od wersji 1.0",
		'browser_safari' => "Safari", // TRANSLATE
		'browser_safari_version' => "od wersji 1.1",
		'ignore_browser' => "Jeżeli pomimo to chcesz uruchomić webEdition kliknij tutaj ...",
		'no_db_connection' => "The database connection can not be established.", // TRANSLATE
		'cookies_disabled' => "Cookies are deactivated.", // TRANSLATE
);