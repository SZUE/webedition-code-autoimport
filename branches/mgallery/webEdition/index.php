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
	//resave config file(s)
	we_base_preferences::check_global_config(true);
}
we_base_file::checkAndMakeFolder($_SERVER['DOCUMENT_ROOT'] . WE_THUMBNAIL_DIRECTORY);

define('LOGIN_DENIED', 4);
define('LOGIN_OK', 2);
define('LOGIN_CREDENTIALS_INVALID', 1);
define('LOGIN_UNKNOWN', 0);


$ignore_browser = we_base_request::_(we_base_request::BOOL, 'ignore_browser', false);

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

function printHeader($login, $status = 200){
	header('Expires: ' . gmdate('D, d.m.Y H:i:s') . ' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Pragma: no-cache');
	we_html_tools::setHttpCode($status);

	echo we_html_tools::getHtmlTop('webEdition') . STYLESHEET .
	we_html_element::jsScript(JS_DIR . 'windows.js') .
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
}') .
	'</head>';
}

function cleanWEZendCache(){
	if(file_exists(WE_CACHE_PATH . 'clean')){
		if(!is_writeable(WE_CACHE_PATH)){
			t_e('cachedir ' . WE_CACHE_PATH . ' is not writeable expect errors, undefined behaviour');
			return;
		}
		$cache = getWEZendCache();
		$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
//remove file
		unlink(WE_CACHE_PATH . 'clean');
	}
}

/* * ***************************************************************************
 * CLEAN Temporary Data left over from last logout  bug #4240
 * *************************************************************************** */
$removePaths = array(
	WEBEDITION_PATH . 'we/include/we_modules/navigation/cache', //old navi-cache
	$_SERVER['DOCUMENT_ROOT'] . '/OnlineInstaller',
	$_SERVER['DOCUMENT_ROOT'] . '/OnlineInstaller.php',
	WEBEDITION_PATH . 'we/zendcache', //old specific zend cache dir
);

foreach($removePaths as $path){
	if(is_dir($path)){
		we_base_file::deleteLocalFolder($path, true);
	}
}

we_base_file::cleanTempFiles(true);
cleanWEZendCache();
we_navigation_cache::clean();
we_captcha_captcha::cleanup($GLOBALS['DB_WE']);

//clean Error-Log-Table
$GLOBALS['DB_WE']->query('DELETE FROM ' . ERROR_LOG_TABLE . ' WHERE `Date` < DATE_SUB(NOW(), INTERVAL ' . we_base_constants::ERROR_LOG_HOLDTIME . ' DAY)');
$cnt = f('SELECT COUNT(1) FROM ' . ERROR_LOG_TABLE);

if($cnt > we_base_constants::ERROR_LOG_MAX_ITEM_COUNT){
	$GLOBALS['DB_WE']->query('DELETE  FROM ' . ERROR_LOG_TABLE . ' WHERE 1 ORDER BY Date LIMIT ' . ($cnt - we_base_constants::ERROR_LOG_MAX_ITEM_THRESH));
}

//CHECK FOR FAILED LOGIN ATTEMPTS
$GLOBALS['DB_WE']->query('DELETE FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblUser" AND LoginDate < DATE_SUB(NOW(), INTERVAL ' . we_base_constants::LOGIN_FAILED_HOLDTIME . ' DAY)');

$count = f('SELECT COUNT(1) FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblUser" AND IP="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '" AND LoginDate > DATE_SUB(NOW(), INTERVAL ' . intval(we_base_constants::LOGIN_FAILED_TIME) . ' MINUTE)');

if($count >= we_base_constants::LOGIN_FAILED_NR){
	echo we_html_tools::getHtmlTop('webEdition ') .
	we_html_element::jsElement(
		we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[3timesLoginError]'), we_base_constants::LOGIN_FAILED_NR, we_base_constants::LOGIN_FAILED_TIME), we_message_reporting::WE_MESSAGE_ERROR)
	) .
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
		setcookie('we_mode', $mode, time() + 2592000); //	Cookie remembers the last selected mode, it will expire in one Month !!!
	}
	setcookie('we_popup', we_base_request::_(we_base_request::BOOL, 'popup'), time() + 2592000);
} else if(isset($_POST['WE_LOGIN_password']) && isset($_POST['WE_LOGIN_username'])){
	$login = LOGIN_CREDENTIALS_INVALID;
} else {
	$login = LOGIN_UNKNOWN;
	if($ignore_browser){
		setcookie('ignore_browser', 'true', time() + 2592000); //	Cookie remembers that the incompatible mode has been selected, it will expire in one Month !!!
	}
}

function getError($reason, $cookie = false){
	$_error = we_html_element::htmlB($reason);
	$_error_count = 0;
	$tmp = ini_get('session.save_path');

	if(!(is_dir($tmp) || (is_link($tmp) && is_dir(readlink($tmp))))){
		$_error .= ++$_error_count . ' - ' . sprintf(g_l('start', '[tmp_path]'), ini_get('session.save_path')) . we_html_element::htmlBr();
	}

	if(!ini_get('session.use_cookies')){
		$_error .= ++$_error_count . ' - ' . g_l('start', '[use_cookies]') . we_html_element::htmlBr();
	}

	if(ini_get('session.cookie_path') != '/'){
		$_error .= ++$_error_count . ' - ' . sprintf(g_l('start', '[cookie_path]'), ini_get('session.cookie_path')) . we_html_element::htmlBr();
	}

	if($cookie && $_error_count == 0){
		$_error .= ++$_error_count . ' - ' . g_l('start', '[login_session_terminated]') . we_html_element::htmlBr();
	}

	$_error .= we_html_element::htmlBr() . g_l('start', ($_error_count == 1 ? '[solution_one]' : '[solution_more]'));

	$_layout = new we_html_table(array('style' => 'width: 100%; height: 75%;'), 1, 1);
	$_layout->setCol(0, 0, array('align' => 'center', 'valign' => 'middle'), we_html_element::htmlCenter(we_html_tools::htmlMessageBox(500, 250, we_html_element::htmlP(array('class' => 'defaultfont'), $_error), g_l('alert', '[phpError]'))));
	return $_layout;
}

/* * ***************************************************************************
 * CHECK FOR PROBLEMS
 * *************************************************************************** */

if(we_base_request::_(we_base_request::STRING, 'checkLogin') && !$_COOKIE){
	$_layout = getError(g_l('start', '[cookies_disabled]'));

	printHeader($login, 400);
	echo we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} else if(!we_database_base::hasDB() || $GLOBALS['DB_WE']->Error === 'No database selected'){
	$_layout = getError(g_l('start', '[no_db_connection]'));

	printHeader($login, 503);
	echo we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} /* don't check for browsers
  elseif(!$ignore_browser && !we_base_browserDetect::isSupported()){
  $supportedBrowserCnt = (we_base_browserDetect::isMAC() ? 3 : (we_base_browserDetect::isUNIX() ? 2 : 4));

  $_browser_table = new we_html_table(array('width' => '100%'), 12, $supportedBrowserCnt);

  $_browser_table->setCol(1, 0, array('align' => 'center', 'class' => 'defaultfont', 'colspan' => $supportedBrowserCnt), we_html_element::htmlB(g_l('start', '[browser_not_supported]')));
  $_browser_table->setCol(3, 0, array('align' => 'center', 'class' => 'defaultfont', 'colspan' => $supportedBrowserCnt), g_l('start', '[browser_supported]'));

  switch(we_base_browserDetect::inst()->getSystem()){
  case we_base_browserDetect::SYS_MAC:
  $_browser_table->setCol(5, 0, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_opera.png', 'width' => 80, 'height' => 80, 'border' => 0))));
  $_browser_table->setCol(5, 1, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.apple.com/safari/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_safari.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
  $_browser_table->setCol(5, 2, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_firefox.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
  $_browser_table->setCol(7, 0, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), g_l('start', '[browser_opera]'))));
  $_browser_table->setCol(7, 1, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.apple.com/safari/', 'target' => '_blank'), g_l('start', '[browser_safari]'))));
  $_browser_table->setCol(7, 2, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), g_l('start', '[browser_firefox]'))));

  $_browser_table->setCol(9, 0, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_safari_version]'));
  $_browser_table->setCol(9, 1, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_firefox_version]'));
  break;
  case we_base_browserDetect::SYS_UNIX:
  $_browser_table->setCol(5, 0, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_opera.png', 'width' => 80, 'height' => 80, 'border' => 0))));
  $_browser_table->setCol(5, 1, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_firefox.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
  $_browser_table->setCol(7, 0, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), g_l('start', '[browser_opera]'))));
  $_browser_table->setCol(7, 1, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), g_l('start', '[browser_firefox]'))));
  $_browser_table->setCol(9, 0, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_opera_version]'));
  $_browser_table->setCol(9, 1, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_firefox_version]'));
  break;
  default:
  $_browser_table->setCol(5, 0, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.microsoft.com/windows/ie/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_ie.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
  $_browser_table->setCol(5, 2, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_firefox.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
  $_browser_table->setCol(5, 3, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.apple.com/safari/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_safari.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
  $_browser_table->setCol(7, 0, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.microsoft.com/windows/ie/', 'target' => '_blank'), g_l('start', '[browser_ie]'))));
  $_browser_table->setCol(7, 1, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), g_l('start', '[browser_opera]'))));
  $_browser_table->setCol(7, 2, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), g_l('start', '[browser_firefox]'))));
  $_browser_table->setCol(7, 3, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.apple.com/safari/', 'target' => '_blank'), g_l('start', '[browser_safari]'))));
  $_browser_table->setCol(9, 0, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_ie_version]'));
  $_browser_table->setCol(9, 1, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_opera_version]'));
  $_browser_table->setCol(9, 2, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_firefox_version]'));
  $_browser_table->setCol(9, 3, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_safari_version]'));
  }


  $_browser_table->setCol(11, 0, array('align' => 'center', 'class' => 'defaultfont', 'colspan' => $supportedBrowserCnt), we_html_element::htmlA(array('href' => WEBEDITION_DIR . 'index.php?ignore_browser=1'), g_l('start', '[ignore_browser]')));

  $_layout = new we_html_table(array('style' => 'width: 100%; height: 75%;'), 1, 1);

  $_layout->setCol(0, 0, array('align' => 'center', 'valign' => 'middle'), we_html_element::htmlCenter(we_html_tools::htmlMessageBox(500, 380, $_browser_table->getHtml(), g_l('start', '[cannot_start_we]'))));

  printHeader($login, 400);
  echo we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
  } */ else {

	/*	 * ***************************************************************************
	 * GENERATE LOGIN
	 * *************************************************************************** */

	$_hidden_values = we_html_element::htmlHiddens(array(
			'checkLogin' => session_id(),
			'indexDate' => date('d.m.Y, H:i:s')));

	if($ignore_browser){
		$_hidden_values .= we_html_element::htmlHidden('ignore_browser', 'true');
	}

	/*	 * ***********************************************************************
	 * BUILD DIALOG
	 * *********************************************************************** */

	$GLOBALS['loginpage'] = ($login == LOGIN_OK) ? false : true;
	include(WE_INCLUDES_PATH . 'we_editors/we_info.inc.php');

	$dialogtable = '<noscript style="color:#fff;">Please activate Javascript!' . we_html_element::htmlBr() . we_html_element::htmlBr() . '</noscript>
' . $_loginTable;

	/*	 * ***********************************************************************
	 * GENERATE NEEDED JAVASCRIPTS
	 * *********************************************************************** */

	switch($login){
		case LOGIN_OK:
			$httpCode = 200;
			$_body_javascript = '';

			//	Here the mode - SEEM or normal is saved in the SESSION!!!
			//	Perhaps this must move to another place later.
			//	Later we must check permissions as well!
			if(we_base_request::_(we_base_request::STRING, 'mode', we_base_constants::MODE_NORMAL) == we_base_constants::MODE_NORMAL){
				if(permissionhandler::isUserAllowedForAction('work_mode', we_base_constants::MODE_NORMAL)){
					$_SESSION['weS']['we_mode'] = we_base_constants::MODE_NORMAL;
				} else {
					$_body_javascript = we_message_reporting::getShowMessageCall(g_l('SEEM', '[only_seem_mode_allowed]'), we_message_reporting::WE_MESSAGE_ERROR);
					$_SESSION['weS']['we_mode'] = we_base_constants::MODE_SEE;
				}
			} else {
				$_SESSION['weS']['we_mode'] = we_base_request::_(we_base_request::STRING, 'mode');
			}

			if((WE_LOGIN_WEWINDOW == 2 || WE_LOGIN_WEWINDOW == 0 && (!we_base_request::_(we_base_request::BOOL, 'popup')))){
				if($_body_javascript){
					$_body_javascript.='top.location="' . WEBEDITION_DIR . 'webEdition.php"';
				} else {
					$httpCode = 303;
					header('Location: ' . WEBEDITION_DIR . 'webEdition.php');
					$_body_javascript = 'alert("automatic redirect disabled");';
				}
			} else {
				$_body_javascript .= 'function open_we() {
			var aw=' . (!empty($_SESSION['prefs']['weWidth']) ? $_SESSION['prefs']['weWidth'] : 8000) . ';
			var ah=' . (!empty($_SESSION['prefs']['weHeight']) ? $_SESSION['prefs']['weHeight'] : 6000) . ';
			win = new jsWindow("' . WEBEDITION_DIR . "webEdition.php?h='+ah+'&w='+aw+'&browser='+((document.all) ? 'ie' : 'nn'), '" . md5(uniqid(__FILE__, true)) . '", -1, -1, aw, ah, true, true, true, true, "' . g_l('alert', '[popupLoginError]') . '", "' . WEBEDITION_DIR . 'index.php"); }';
			}
			break;
		case LOGIN_CREDENTIALS_INVALID:
			we_users_user::logLoginFailed('tblUser', we_base_request::_(we_base_request::STRING, 'WE_LOGIN_username'));
			//make it harder to guess salt/password
			usleep(1100000 + rand(0, 1000000));
			//CHECK FOR FAILED LOGIN ATTEMPTS
			$cnt = f('SELECT COUNT(1) FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblUser" AND IP="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '" AND LoginDate > DATE_SUB(NOW(), INTERVAL ' . intval(we_base_constants::LOGIN_FAILED_TIME) . ' MINUTE)');

			$_body_javascript = ($cnt >= we_base_constants::LOGIN_FAILED_NR ?
					we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[3timesLoginError]'), we_base_constants::LOGIN_FAILED_NR, we_base_constants::LOGIN_FAILED_TIME), we_message_reporting::WE_MESSAGE_ERROR) :
					we_message_reporting::getShowMessageCall(g_l('alert', '[login_failed]'), we_message_reporting::WE_MESSAGE_ERROR));
			break;
		case 3:
			$_body_javascript = we_message_reporting::getShowMessageCall(g_l('alert', '[login_failed_security]'), we_message_reporting::WE_MESSAGE_ERROR) . "document.location='" . WEBEDITION_DIR . "index.php" . (($ignore_browser || (isset($_COOKIE["ignore_browser"]) && $_COOKIE["ignore_browser"] === "true")) ? "&ignore_browser=" . (isset($_COOKIE["ignore_browser"]) ? $_COOKIE["ignore_browser"] : ($ignore_browser ? 1 : 0)) : "") . "';";
			break;
		case LOGIN_DENIED:
			$_body_javascript = we_message_reporting::getShowMessageCall(g_l('alert', '[login_denied_for_user]'), we_message_reporting::WE_MESSAGE_ERROR);
			break;
		default:
			$httpCode = 200;
			break;
	}


	$_layout = we_html_element::htmlDiv(array('style' => 'float: left;height: 50%;width: 1px;')) . we_html_element::htmlDiv(array('style' => 'clear:left;position:relative;top:-25%;'), we_html_element::htmlForm(array("action" => WEBEDITION_DIR . 'index.php', 'method' => 'post', 'name' => 'loginForm'), $_hidden_values . $dialogtable));

	printHeader($login, (isset($httpCode) ? $httpCode : 401));
	echo we_html_element::htmlBody(array('id' => 'loginScreen', "onload" => (($login == LOGIN_OK) ? "open_we();" : "document.loginForm.WE_LOGIN_username.focus();document.loginForm.WE_LOGIN_username.select();")), $_layout . ((isset($_body_javascript)) ? we_html_element::jsElement($_body_javascript) : '')) . '</html>';
}
