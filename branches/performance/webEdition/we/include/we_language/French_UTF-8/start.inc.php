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

		'phpini_problems' => 'Il y a des problèmes avec les préférences de session dans votre fichier php.ini %s!',
		'tmp_path' => "La variable <b>session.save_path</b> montre sur '%s'. Ce répertoire n'existe pas sur le serveur!",
		'use_cookies' => "La variable <b>session.use_cookies</b> n'est pas definit. S'il vous plaît mettez la sur 1!",
		'cookie_path' => "La variable <b>session.cookie_path</b> a la valeur '%s'. Cette valeur devrait être nomalement '/' !",
		'solution_one' => "Si vous avez votre propre serveur, changer cette valeur s'il vous plaît. Si vous êtes chez un fournisseur, informez-le que la valeur est mal definit!",
		'solution_more' => "Si vous avez votre propre serveur, changer cette valeur s'il vous plaît. Si vous êtes chez un fournisseur, informez-le que la valeur est mal definit!",
		'cannot_start_we' => "webEdition ne peut pas être démarré ",
		'browser_not_supported' => "Votre navigateur n'est pas supporter par webEdition!",
		'browser_supported' => "webEdition support les navigateurs:",
		'browser_ie' => "Internet Explorer", // TRANSLATE
		'browser_ie_version' => "dès la  Version 5.5",
		'browser_firefox' => "Firefox", // TRANSLATE
		'browser_firefox_version' => "dès la Version 1.0",
		'browser_safari' => "Safari", // TRANSLATE
		'browser_safari_version' => "dès la Version 1.1",
		'ignore_browser' => "Si vous voulez tout de même démarré webEdition, cliquez ici ...",
		'no_db_connection' => "The database connection can not be established.", // TRANSLATE
		'cookies_disabled' => "Cookies are deactivated.", // TRANSLATE
);