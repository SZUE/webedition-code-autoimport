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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
define('LIVEUPDATE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/');

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_error_handler.inc.php');
if(function_exists('we_error_setHandleAll')){
	we_error_setHandleAll();
}
we_error_handler();

if(!isset($_COOKIE[SESSION_NAME]) && isset($_COOKIE['PHPSESSID'])){
	session_name('PHPSESSID');
	session_id($_COOKIE['PHPSESSID']);
	unset($_REQUEST['PHPSESSID'], $_GET['PHPSESSID'], $_POST['PHPSESSID']);
//note due to session upgrade: in session are serialized classes so an autoloader is needed before starting the session
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/core/autoload.inc.php');
	session_start();
	if(isset($_SESSION['user']['isWeSession']) && $_SESSION['user']['isWeSession']){//use this session&rename if we have a good we session found
		setcookie('PHPSESSID', '', time() - 3600);
		//rename session
		session_name(SESSION_NAME);
	}
	session_write_close();
}

if(isset($_REQUEST[SESSION_NAME])){
	session_name(SESSION_NAME);
	session_id($_REQUEST[SESSION_NAME]);
	unset($_REQUEST[SESSION_NAME], $_GET[SESSION_NAME], $_POST[SESSION_NAME]);
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateHttp.class.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateResponse.class.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateFrames.class.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateFunctions.class.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateTemplates.class.php');

require_once(LIVEUPDATE_DIR . 'conf/mapping.inc.php');
require_once(LIVEUPDATE_DIR . 'conf/conf.inc.php');
require_once(LIVEUPDATE_DIR . 'includes/define.inc.php');
include_once(LIVEUPDATE_LANGUAGE_DIR . 'liveUpdate.inc.php');

if(is_readable(LIVEUPDATE_DIR . 'updateClient/liveUpdateFunctionsServer.class.php')){
	require_once(LIVEUPDATE_DIR . 'updateClient/liveUpdateFunctionsServer.class.php');
}
if(is_readable(LIVEUPDATE_DIR . 'updateClient/liveUpdateResponseServer.class.php')){
	require_once(LIVEUPDATE_DIR . 'updateClient/liveUpdateResponseServer.class.php');
}
if(is_readable(LIVEUPDATE_DIR . 'updateClient/liveUpdateServer.class.php')){
	require_once(LIVEUPDATE_DIR . 'updateClient/liveUpdateServer.class.php');
}
