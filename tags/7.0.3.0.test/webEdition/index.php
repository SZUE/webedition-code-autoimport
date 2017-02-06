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
/* * ***************************************************************************
 * INITIALIZATION
 * *************************************************************************** */

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

//Check some critical PHP Setings #7243
//FIXME: implement class sysinfo.class, for not analysing the same php settings twice (here and in sysinfo.php)
if(permissionhandler::hasPerm('ADMINISTRATOR')){
	$suhosinMsg = (extension_loaded('suhosin') && !in_array(ini_get('suhosin.simulation'), array(1, 'on', 'yes', 'true', true))) ? 'suhosin=on\n' : '';

	$maxInputMsg = (!ini_get('max_input_vars') ? 'max_input_vars = 1000 (PHP default value)' :
			(ini_get('max_input_vars') < 2000 ? 'max_input_vars = ' . ini_get('max_input_vars') : ''));
	$maxInputMsg .= $maxInputMsg ? ': >= 2000 is recommended' : '';

	$criticalPhpMsg = trim($maxInputMsg . $suhosinMsg);
	if($criticalPhpMsg){
		t_e('Critical PHP Settings found', $criticalPhpMsg);
	}
}

if(!defined('CONF_SAVED_VERSION') || (defined('CONF_SAVED_VERSION') && (intval(WE_SVNREV) > intval(CONF_SAVED_VERSION)))){
	define('WE_VERSION_UPDATE', 1);
	//resave config file(s)
	we_base_preferences::check_global_config(true);
	we_base_file::delete(WE_CACHE_PATH . 'newwe_version.json');
}
we_base_file::checkAndMakeFolder($_SERVER['DOCUMENT_ROOT'] . WE_THUMBNAIL_DIRECTORY);

define('LOGIN_DENIED', 4);
define('LOGIN_OK', 2);
define('LOGIN_CREDENTIALS_INVALID', 1);
define('LOGIN_UNKNOWN', 0);

function getValueLoginMode($val){
	$mode = isset($_COOKIE['we_mode']) ? $_COOKIE['we_mode'] : we_base_constants::MODE_NORMAL;
	switch($val){
		case we_base_constants::MODE_SEE :
			return ($mode == we_base_constants::MODE_SEE) ? ' checked="checked"' : '';
		case we_base_constants::MODE_NORMAL :// start normal mode
			return ($mode != we_base_constants::MODE_SEE) ? ' checked="checked"' : '';
		case 'popup':
			return (!isset($_COOKIE['we_popup']) || $_COOKIE['we_popup'] == 1);
	}
}

function printHeader($login, $status = 200, $js = ''){
	header('Expires: ' . gmdate('D, d.m.Y H:i:s') . ' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Pragma: no-cache');
	we_html_tools::setHttpCode($status);

	echo we_html_tools::getHtmlTop('webEdition', '', '', '', '', false) .
	STYLESHEET .
	we_html_element::jsScript(JS_DIR . 'windows.js') .
	we_html_element::cssLink(CSS_DIR . 'loginScreen.css') .
	we_html_element::jsElement(we_message_reporting::jsString());

	if($login != LOGIN_OK){
		echo we_html_element::linkElement(array('rel' => 'home', 'href' => WEBEDITION_DIR)) .
		we_html_element::linkElement(array('rel' => 'author', 'href' => g_l('start', '[we_homepage]')));
	}

	echo we_html_element::linkElement(array('rel' => 'SHORTCUT ICON', 'href' => IMAGE_DIR . 'webedition.ico')) .
	we_html_element::jsElement('
	isLoginScreen = true;
	cookieBackup = document.cookie;
	document.cookie = "cookie=yep";
	cookieOk = document.cookie.indexOf("cookie=yep") > -1;
	document.cookie = cookieBackup;

	if (!cookieOk) {
		' . we_message_reporting::getShowMessageCall(g_l('alert', '[no_cookies]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
	var messageSettings = ' . (we_message_reporting::WE_MESSAGE_ERROR + we_message_reporting::WE_MESSAGE_WARNING + we_message_reporting::WE_MESSAGE_NOTICE) . ';

/**
 * setting is built like the unix file system privileges with the 3 options
 * see notices, see warnings, see errors
 *
 * 1 => see Errors
 * 2 => see Warnings
 * 4 => see Notices
 *
 * @param message string
 * @param prio integer one of the values 1,2,4
 * @param win object reference to the calling window
 */
function showMessage(message, prio, win){

	if (!win) {
		win = this.window;
	}
	if (!prio) { // default is error, to avoid missing messages
		prio = ' . we_message_reporting::WE_MESSAGE_ERROR . ';
	}

	if (prio & messageSettings) { // show it, if you should

		// the used vars are in file JS_DIR . "weJsStrings.php";
		switch (prio) {

			// Notice
			case ' . we_message_reporting::WE_MESSAGE_NOTICE . ':
				win.alert(message_reporting.notice + ":\n" + message);
				break;

			// Warning
			case ' . we_message_reporting::WE_MESSAGE_WARNING . ':
				win.alert(message_reporting.warning + ":\n" + message);
				break;

			// Error
			case ' . we_message_reporting::WE_MESSAGE_ERROR . ':
				win.alert(message_reporting.error + ":\n" + message);
				break;
		}
	}
}' .
		$js) .
	'</head>';
}

/* * ***************************************************************************
 * CLEAN Temporary Data left over from last logout  bug #4240
 * *************************************************************************** */
$removePaths = array(
	WEBEDITION_PATH . 'we/include/we_modules/navigation/cache', //old navi-cache
	$_SERVER['DOCUMENT_ROOT'] . '/OnlineInstaller',
	$_SERVER['DOCUMENT_ROOT'] . '/OnlineInstaller.php',
	WEBEDITION_PATH . 'we/zendcache',
	WEBEDITION_PATH . 'preview',
);

foreach($removePaths as $path){
	if(is_dir($path)){
		we_base_file::deleteLocalFolder($path, true);
	}
}

we_base_file::cleanTempFiles(true);
we_base_file::cleanWECache();
we_navigation_cache::clean();
we_captcha_captcha::cleanup($GLOBALS['DB_WE']);
we_search_search::cleanOldEntries();
we_base_preferences::writeDefaultLanguageConfig();

//clean Error-Log-Table
$GLOBALS['DB_WE']->query('DELETE FROM ' . ERROR_LOG_TABLE . ' WHERE `Date`<(NOW() - INTERVAL ' . we_base_constants::ERROR_LOG_HOLDTIME . ' DAY)');
$cnt = f('SELECT COUNT(1) FROM ' . ERROR_LOG_TABLE);

if($cnt > we_base_constants::ERROR_LOG_MAX_ITEM_COUNT){
	$GLOBALS['DB_WE']->query('DELETE FROM ' . ERROR_LOG_TABLE . ' WHERE 1 ORDER BY Date LIMIT ' . ($cnt - we_base_constants::ERROR_LOG_MAX_ITEM_THRESH));
}

//CHECK FOR FAILED LOGIN ATTEMPTS
$GLOBALS['DB_WE']->query('DELETE FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblUser" AND LoginDate<(NOW() - INTERVAL ' . we_base_constants::LOGIN_FAILED_HOLDTIME . ' DAY)');

$count = f('SELECT COUNT(1) FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblUser" AND IP="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '" AND LoginDate>(NOW() - INTERVAL ' . intval(we_base_constants::LOGIN_FAILED_TIME) . ' MINUTE)');

if($count >= we_base_constants::LOGIN_FAILED_NR){
	echo we_html_tools::getHtmlTop('webEdition ') .
	we_html_element::jsScript(JS_DIR . 'windows.js') .
	we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[3timesLoginError]'), we_base_constants::LOGIN_FAILED_NR, we_base_constants::LOGIN_FAILED_TIME), we_message_reporting::WE_MESSAGE_ERROR)) .
	'</html>';
	exit();
}

/* * ***************************************************************************
 * SWITCH MODE
 * *************************************************************************** */
//set denied as default
$login = LOGIN_DENIED;
if(isset($GLOBALS['userLoginDenied'])){
	$login = LOGIN_DENIED;
} else if(isset($_SESSION['user']['Username']) && isset($_POST['WE_LOGIN_password']) && isset($_POST['WE_LOGIN_username'])){
	$login = LOGIN_OK;
	if(($mode = we_base_request::_(we_base_request::STRING, 'mode'))){
		setcookie('we_mode', $mode, time() + 2592000); //	Cookie remembers the last selected mode, it will expire in one Month !
	}
	setcookie('we_popup', we_base_request::_(we_base_request::BOOL, 'popup'), time() + 2592000);
} else if(isset($_POST['WE_LOGIN_password']) && isset($_POST['WE_LOGIN_username'])){
	$login = LOGIN_CREDENTIALS_INVALID;
} else {
	$login = LOGIN_UNKNOWN;
	//old incompatible browser
}

function getError($reason, $cookie = false){
	$error_count = 0;
	$tmp = ini_get('session.save_path');

	$error = we_html_element::htmlB($reason) .
		(!(is_dir($tmp) || (is_link($tmp) && is_dir(readlink($tmp)))) ?
			( ++$error_count . ' - ' . sprintf(g_l('start', '[tmp_path]'), ini_get('session.save_path')) . we_html_element::htmlBr()) :
			'') .
		(!ini_get('session.use_cookies') ?
			( ++$error_count . ' - ' . g_l('start', '[use_cookies]') . we_html_element::htmlBr()) :
			'') .
		(ini_get('session.cookie_path') != '/' ?
			( ++$error_count . ' - ' . sprintf(g_l('start', '[cookie_path]'), ini_get('session.cookie_path')) . we_html_element::htmlBr()) :
			'') .
		($cookie && $error_count == 0 ?
			( ++$error_count . ' - ' . g_l('start', '[login_session_terminated]') . we_html_element::htmlBr()) :
			'') .
		we_html_element::htmlBr() . g_l('start', ($error_count == 1 ? '[solution_one]' : '[solution_more]'));

	$layout = new we_html_table(array('style' => 'width: 100%; height: 75%;'), 1, 1);
	$layout->setCol(0, 0, array('style' => 'text-align:center;vertical-align:middle'), we_html_tools::htmlMessageBox(500, 250, we_html_element::htmlP(array('class' => 'defaultfont'), $error), g_l('alert', '[phpError]')));
	return $layout;
}

/* * ***************************************************************************
 * CHECK FOR PROBLEMS
 * *************************************************************************** */

if(we_base_request::_(we_base_request::STRING, 'checkLogin') && !$_COOKIE){
	$layout = getError(g_l('start', '[cookies_disabled]'));

	printHeader($login, 400);
	echo we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $layout->getHtml()) . '</html>';
} else if(!we_database_base::hasDB() || $GLOBALS['DB_WE']->Error === 'No database selected'){
	$layout = getError(g_l('start', '[no_db_connection]'));

	printHeader($login, 503);
	echo we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $layout->getHtml()) . '</html>';
} /* don't check for browsers anymore */ else {

	/*	 * ***************************************************************************
	 * GENERATE LOGIN
	 * *************************************************************************** */

	$hidden_values = we_html_element::htmlHiddens(array(
			'checkLogin' => session_id(),
			'indexDate' => date('d.m.Y, H:i:s')));

	/*	 * ***********************************************************************
	 * BUILD DIALOG
	 * *********************************************************************** */

	$GLOBALS['loginpage'] = ($login == LOGIN_OK) ? false : true;

	$dialogtable = '<noscript style="color:#fff;">Please activate Javascript!' . we_html_element::htmlBr() . we_html_element::htmlBr() . '</noscript>
' . include(WE_INCLUDES_PATH . 'we_editors/we_info.inc.php');

	/*	 * ***********************************************************************
	 * GENERATE NEEDED JAVASCRIPTS
	 * *********************************************************************** */
	$headerjs = '';
	switch($login){
		case LOGIN_OK:
			$httpCode = 200;
			$body_javascript = '';

			//	Here the mode - SEEM or normal is saved in the SESSION!
			//	Perhaps this must move to another place later.
			//	Later we must check permissions as well!
			if(we_base_request::_(we_base_request::STRING, 'mode', we_base_constants::MODE_NORMAL) == we_base_constants::MODE_NORMAL){
				if(permissionhandler::isUserAllowedForAction('work_mode', we_base_constants::MODE_NORMAL)){
					$_SESSION['weS']['we_mode'] = we_base_constants::MODE_NORMAL;
				} else {
					$body_javascript = we_message_reporting::getShowMessageCall(g_l('SEEM', '[only_seem_mode_allowed]'), we_message_reporting::WE_MESSAGE_ERROR);
					$_SESSION['weS']['we_mode'] = we_base_constants::MODE_SEE;
				}
			} else {
				$_SESSION['weS']['we_mode'] = we_base_request::_(we_base_request::STRING, 'mode');
			}

			if((WE_LOGIN_WEWINDOW == 2 || WE_LOGIN_WEWINDOW == 0 && (!we_base_request::_(we_base_request::BOOL, 'popup')))){
				if($body_javascript){
					$body_javascript.='top.location="' . WEBEDITION_DIR . 'webEdition.php"';
				} else {
					$httpCode = 303;
					header('Location: ' . WEBEDITION_DIR . 'webEdition.php');
					$body_javascript = 'alert("automatic redirect disabled");';
				}
				break;
			}
			$headerjs = 'function open_we() {
var aw=' . (empty($_SESSION['prefs']['weWidth']) ? 8000 : $_SESSION['prefs']['weWidth']) . ';
var ah=' . (empty($_SESSION['prefs']['weHeight']) ? 6000 : $_SESSION['prefs']['weHeight']) . ';
win = new jsWindow(top.window, "' . WEBEDITION_DIR . "webEdition.php?h='+ah+'&w='+aw, '" . md5(uniqid(__FILE__, true)) . '", "mainwindow",-1, -1, aw, ah, true, true, true, true, "' . g_l('alert', '[popupLoginError]') . '", "' . WEBEDITION_DIR . 'index.php"); }';

			break;
		case LOGIN_CREDENTIALS_INVALID:
			we_users_user::logLoginFailed('tblUser', we_base_request::_(we_base_request::STRING, 'WE_LOGIN_username'));
			//make it harder to guess salt/password
			usleep(1100000 + rand(0, 1000000));
			//CHECK FOR FAILED LOGIN ATTEMPTS
			$cnt = f('SELECT COUNT(1) FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblUser" AND IP="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '" AND LoginDate>(NOW() - INTERVAL ' . intval(we_base_constants::LOGIN_FAILED_TIME) . ' MINUTE)');

			$body_javascript = ($cnt >= we_base_constants::LOGIN_FAILED_NR ?
					we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[3timesLoginError]'), we_base_constants::LOGIN_FAILED_NR, we_base_constants::LOGIN_FAILED_TIME), we_message_reporting::WE_MESSAGE_ERROR) :
					we_message_reporting::getShowMessageCall(g_l('alert', '[login_failed]'), we_message_reporting::WE_MESSAGE_ERROR));
			break;
		case 3:
			$body_javascript = we_message_reporting::getShowMessageCall(g_l('alert', '[login_failed_security]'), we_message_reporting::WE_MESSAGE_ERROR) . "document.location='" . WEBEDITION_DIR . "index.php';";
			break;
		case LOGIN_DENIED:
			$body_javascript = we_message_reporting::getShowMessageCall(g_l('alert', '[login_denied_for_user]'), we_message_reporting::WE_MESSAGE_ERROR);
			break;
		default:
			$httpCode = 200;
			break;
	}


	$layout = /* we_html_element::htmlDiv(array('style' => 'float: left;height: 50%;width: 1px;')) . we_html_element::htmlDiv(array('style' => 'clear:left;position:relative;top:-25%;'), */we_html_element::htmlForm(array("action" => WEBEDITION_DIR . 'index.php', 'method' => 'post', 'name' => 'loginForm'), $hidden_values . $dialogtable)/* ) */ .
		we_html_element::htmlDiv(array('id' => 'picCopy'), 'Copyright &copy; nw7.eu / Fotolia.com');

	printHeader($login, (isset($httpCode) ? $httpCode : 401), $headerjs);
	echo we_html_element::htmlBody(array('id' => 'loginScreen', "onload" => (($login == LOGIN_OK) ? "open_we();" : "document.loginForm.WE_LOGIN_username.focus();document.loginForm.WE_LOGIN_username.select();")), $layout . ((isset($body_javascript)) ? we_html_element::jsElement($body_javascript) : '')) . '</html>';
}
session_write_close();
flush();
if(function_exists('fastcgi_finish_request')){
	fastcgi_finish_request();
}
ignore_user_abort(true);
if(!file_exists(WE_CACHE_PATH . 'newwe_version.json')){
	we_base_file::save(WE_CACHE_PATH . 'newwe_version.json', getHTTP('https://update.webedition.org', '/server/we/latest.php?vers=' .
		WE_VERSION .
		'&supp=' . WE_VERSION_SUPP .
		'&branch=' . WE_VERSION_BRANCH .
			(WE_VERSION_SUPP != "release" ? '&beta=true' : '')));
	we_base_file::insertIntoCleanUp(WE_CACHE_DIR . 'newwe_version.json', 7 * 86400);
}