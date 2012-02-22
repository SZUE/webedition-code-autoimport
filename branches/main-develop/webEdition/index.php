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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/* * ***************************************************************************
 * INITIALIZATION
 * *************************************************************************** */

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf.inc.php');

/* * ***************************************************************************
 * INCLUDES
 * *************************************************************************** */

if(!file_exists($_SERVER['DOCUMENT_ROOT'] .'/webEdition/we/include/conf/we_conf_language.inc.php')){
	require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_global.inc.php');
	we_loadLanguageConfig();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$ignore_browser = isset($_REQUEST['ignore_browser']) && ($_REQUEST['ignore_browser'] === 'true');

/* * ***************************************************************************
 * FUNCTIONS
 * *************************************************************************** */

function getValueLoginMode($val){
	$mode = isset($_COOKIE['we_mode']) ? $_COOKIE['we_mode'] : 'normal';
	switch($val){
		case 'seem' :
			return ($mode == 'seem') ? ' checked="checked"' : '';
		case 'normal' :// start normal mode
			return ($mode != 'seem') ? ' checked="checked"' : '';
		case 'popup':
			return (!isset($_COOKIE['we_popup']) || $_COOKIE['we_popup'] == 1);
	}
}

function printHeader($login){
	/*	 * ***************************************************************************
	 * CREATE HEADER
	 * *************************************************************************** */
	we_html_tools::htmlTop('webEdition');
	print STYLESHEET .
		we_html_element::cssElement('html, body {height:100%;}');

	print we_html_element::jsScript(JS_DIR . 'windows.js');
	print we_html_element::jsScript(JS_DIR . 'weJsStrings.php');

	if($login != 2){
		print we_html_element::linkElement(array('rel' => 'home', 'href' => '/webEdition/'));
		print we_html_element::linkElement(array('rel' => 'author', 'href' => g_l('start', '[we_homepage]')));
	}

	print we_html_element::linkElement(array('rel' => 'SHORTCUT ICON', 'href' => '/webEdition/images/webedition.ico'));

	$_head_javascript = 'cookieBackup = document.cookie;
	document.cookie = "cookie=yep";
	cookieOk = document.cookie.indexOf("cookie=yep") > -1;
	document.cookie = cookieBackup;

	if (!cookieOk) {
		' . we_message_reporting::getShowMessageCall(g_l('alert', "[no_cookies]"), we_message_reporting::WE_MESSAGE_ERROR) . '
	}';

	$_head_javascript .= 'var messageSettings = ' . (we_message_reporting::WE_MESSAGE_ERROR + we_message_reporting::WE_MESSAGE_WARNING + we_message_reporting::WE_MESSAGE_NOTICE) . ';

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
		win = window;
	}
	if (!prio) { // default is error, to avoid missing messages
		prio = 4;
	}

	if (prio & messageSettings) { // show it, if you should

		// the used vars are in file JS_DIR . "weJsStrings.php";
		switch (prio) {

			// Notice
			case 1:
				win.alert(we_string_message_reporting_notice + ":\n" + message);
				break;

			// Warning
			case 2:
				win.alert(we_string_message_reporting_warning + ":\n" + message);
				break;

			// Error
			case 4:
				win.alert(we_string_message_reporting_error + ":\n" + message);
				break;
		}
	}
}
';

	print we_html_element::jsElement($_head_javascript);

	print '</head>';
}

/* * ***************************************************************************
 * CLEAN Temporary Data left over from last logout  bug #4240
 * *************************************************************************** */
if(is_dir($_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . 'we/cache')){
	we_util_File::deleteLocalFolder($_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . 'we/cache', true);
}

cleanTempFiles(true);
cleanWEZendCache();
we_updater::fixInconsistentTables();

//clean Error-Log-Table
$GLOBALS['DB_WE']->query('DELETE FROM ' . ERROR_LOG_TABLE . ' WHERE `Date` < DATE_SUB(NOW(), INTERVAL ' . ERROR_LOG_HOLDTIME . ' DAY)');


/* * ***************************************************************************
 * CHECK FOR FAILED LOGIN ATTEMPTS
 * *************************************************************************** */

$GLOBALS['DB_WE']->query('DELETE FROM ' . FAILED_LOGINS_TABLE . ' WHERE LoginDate < DATE_SUB(NOW(), INTERVAL ' . LOGIN_FAILED_HOLDTIME . ' DAY)');

$count = f('SELECT COUNT(1) AS count FROM ' . FAILED_LOGINS_TABLE . ' WHERE IP="' . addslashes($_SERVER['REMOTE_ADDR']) . '" AND LoginDate > DATE_SUB(NOW(), INTERVAL ' . intval(LOGIN_FAILED_TIME) . ' MINUTE)', 'count', $DB_WE);

if($count >= LOGIN_FAILED_NR){
	we_html_tools::htmlTop('webEdition ');
	print we_html_element::jsElement(
			we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[3timesLoginError]'), LOGIN_FAILED_NR, LOGIN_FAILED_TIME), we_message_reporting::WE_MESSAGE_ERROR)
		);
	print '</html>';
	exit();
}

/* * ***************************************************************************
 * SWITCH MODE
 * *************************************************************************** */
//set denied as default
$login = 4;
if(isset($GLOBALS['userLoginDenied'])){
	$login = 4;
} else if(isset($_SESSION['user']['Username']) && isset($_POST['password']) && isset($_POST['username'])){
	$login = 2;
	setcookie('we_mode', $_REQUEST['mode'], time() + 2592000); //	Cookie remembers the last selected mode, it will expire in one Month !!!
	setcookie('we_popup', (isset($_REQUEST['popup']) ? 1 : 0), time() + 2592000);
} else if(isset($_POST['password']) && isset($_POST['username'])){
	$login = 1;
} else{
	$login = 0;
	if($ignore_browser){
		setcookie('ignore_browser', 'true', time() + 2592000); //	Cookie remembers that the incompatible mode has been selected, it will expire in one Month !!!
	}
}


/* * ***************************************************************************
 * CHECK FOR PROBLEMS
 * *************************************************************************** */

if(isset($_POST['checkLogin']) && !count($_COOKIE)){
	$_error = we_html_element::htmlB(g_l('start', '[cookies_disabled]'));

	$_error_count = 0;
	$tmp = ini_get('session.save_path');

	if(!(is_dir($tmp) && file_exists($tmp))){
		$_error .= $_error_count++ . ' - ' . sprintf(g_l('start', '[tmp_path]'), ini_get('session.save_path')) . we_html_element::htmlBr();
	}

	if(!ini_get('session.use_cookies')){
		$_error .= $_error_count++ . ' - ' . g_l('start', '[use_cookies]') . we_html_element::htmlBr();
	}

	if(ini_get('session.cookie_path') != '/'){
		$_error .= $_error_count++ . ' - ' . sprintf(g_l('start', '[cookie_path]'), ini_get('session.cookie_path')) . we_html_element::htmlBr();
	}

	if($_error_count == 1){
		$_error .= we_html_element::htmlBr() . g_l('start', '[solution_one]');
	} else if($_error_count > 1){
		$_error .= we_html_element::htmlBr() . g_l('start', '[solution_more]');
	}

	$_layout = new we_html_table(array('style' => 'width: 100%; height: 75%;'), 1, 1);

	$_layout->setCol(0, 0, array('align' => 'center', 'valign' => 'middle'), we_html_element::htmlCenter(we_html_tools::htmlMessageBox(500, 250, we_html_element::htmlP(array('class' => 'defaultfont'), $_error), g_l('alert', '[phpError]'))));

	printHeader($login);
	print we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} else if(!$GLOBALS['DB_WE']->isConnected() || $GLOBALS['DB_WE']->Error == 'No database selected'){
	$_error = we_html_element::htmlB(g_l('start', '[no_db_connection]'));

	$_error_count = 0;
	$tmp = ini_get('session.save_path');

	if(!(is_dir($tmp) && file_exists($tmp))){
		$_error .= $_error_count++ . ' - ' . sprintf(g_l('start', '[tmp_path]'), ini_get('session.save_path')) . we_html_element::htmlBr();
	}

	if(!ini_get('session.use_cookies')){
		$_error .= $_error_count++ . ' - ' . g_l('start', '[use_cookies]') . we_html_element::htmlBr();
	}

	if(ini_get('session.cookie_path') != '/'){
		$_error .= $_error_count++ . ' - ' . sprintf(g_l('start', '[cookie_path]'), ini_get('session.cookie_path')) . we_html_element::htmlBr();
	}

	if($_error_count == 1){
		$_error .= we_html_element::htmlBr() . g_l('start', '[solution_one]');
	} else if($_error_count > 1){
		$_error .= we_html_element::htmlBr() . g_l('start', '[solution_more]');
	}

	$_layout = new we_html_table(array('style' => 'width: 100%; height: 75%;'), 1, 1);

	$_layout->setCol(0, 0, array('align' => 'center', 'valign' => 'middle'), we_html_element::htmlCenter(we_html_tools::htmlMessageBox(500, 250, we_html_element::htmlP(array('class' => 'defaultfont'), $_error), g_l('alert', '[phpError]'))));

	printHeader($login);
	print we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} else if(isset($_POST['checkLogin']) && $_POST['checkLogin'] != session_id()){
	$_error = we_html_element::htmlB(sprintf(g_l('start', '[phpini_problems]'), (ini_get('cfg_file_path') ? ' (' . ini_get('cfg_file_path') . ')' : '')) . we_html_element::htmlBr() . we_html_element::htmlBr());

	$_error_count = 0;
	$tmp = ini_get('session.save_path');

	if(!(is_dir($tmp) && file_exists($tmp))){
		$_error .= $_error_count++ . ' - ' . sprintf(g_l('start', '[tmp_path]'), ini_get('session.save_path')) . we_html_element::htmlBr();
	}

	if(!ini_get('session.use_cookies')){
		$_error .= $_error_count++ . ' - ' . g_l('start', '[use_cookies]') . we_html_element::htmlBr();
	}

	if(ini_get('session.cookie_path') != '/'){
		$_error .= $_error_count++ . ' - ' . sprintf(g_l('start', '[cookie_path]'), ini_get('session.cookie_path')) . we_html_element::htmlBr();
	}

	if($_error_count == 1){
		$_error .= we_html_element::htmlBr() . g_l('start', '[solution_one]');
	} else if($_error_count > 1){
		$_error .= we_html_element::htmlBr() . g_l('start', '[solution_more]');
	}

	$_layout = new we_html_table(array('style' => 'width: 100%; height: 75%;'), 1, 1);

	$_layout->setCol(0, 0, array('align' => 'center', 'valign' => 'middle'), we_html_element::htmlCenter(we_html_tools::htmlMessageBox(500, 250, we_html_element::htmlP(array('class' => 'defaultfont'), $_error), g_l('alert', '[phpError]'))));

	printHeader($login);
	print we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} else if(!$ignore_browser && !we_base_browserDetect::isSupported()){

	/*	 * *******************************************************************
	 * CHECK BROWSER
	 * ******************************************************************* */

	$supportedBrowserCnt = (we_base_browserDetect::isMAC() ? 3 : (we_base_browserDetect::isUNIX() ? 2 : 4));

	$_browser_table = new we_html_table(array('cellspacing' => 0, 'cellpadding' => 0, 'border' => 0, 'width' => '100%'), 12, $supportedBrowserCnt);

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
			$_browser_table->setCol(5, 1, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_opera.png', 'width' => 80, 'height' => 80, 'border' => 0))));
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


	$_browser_table->setCol(0, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 20));
	$_browser_table->setCol(2, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 50));
	$_browser_table->setCol(4, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 30));
	$_browser_table->setCol(6, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 10));
	$_browser_table->setCol(8, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 5));
	$_browser_table->setCol(10, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 50));

	$_browser_table->setCol(11, 0, array('align' => 'center', 'class' => 'defaultfont', 'colspan' => $supportedBrowserCnt), we_html_element::htmlA(array('href' => WEBEDITION_DIR . 'index.php?ignore_browser=true'), g_l('start', '[ignore_browser]')));

	$_layout = new we_html_table(array('style' => 'width: 100%; height: 75%;'), 1, 1);

	$_layout->setCol(0, 0, array('align' => 'center', 'valign' => 'middle'), we_html_element::htmlCenter(we_html_tools::htmlMessageBox(500, 380, $_browser_table->getHtml(), g_l('start', '[cannot_start_we]'))));

	printHeader($login);
	print we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} else{

	/*	 * ***************************************************************************
	 * GENERATE LOGIN
	 * *************************************************************************** */

	$_hidden_values = we_html_element::htmlHidden(array('name' => 'checkLogin', 'value' => session_id()));

	if($ignore_browser){
		$_hidden_values .= we_html_element::htmlHidden(array('name' => 'ignore_browser', 'value' => 'true'));
	}




	/*	 * ***********************************************************************
	 * BUILD DIALOG
	 * *********************************************************************** */

	$GLOBALS['loginpage'] = ($login == 2) ? false : true;
	include($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_templates/we_info.inc.php');

	$dialogtable = '<noscript style="color:#fff;">Please activate Javascript!' . we_html_element::htmlBr() . we_html_element::htmlBr() . '</noscript>
<table cellpadding="0" cellspacing="0" border="0" style="margin-left: auto; margin-right: auto;text-align:left;">
	<tr>
		<td style="background-color:#386AAB;"></td>
		<td rowspan="2">' . $_loginTable . '</td>
		<td valign="top" style="background-image:url(/webEdition/images/login/right.jpg);background-repeat:repeat-y;">' . we_html_element::htmlImg(array('src' => '/webEdition/images/login/top_r.jpg')) . '</td>

	</tr>
	<tr>
		<td  valign="bottom" style="background-color:#386AAB;"></td>

		<td valign="bottom" style="height:296px;background-image:url(/webEdition/images/login/right.jpg);background-repeat:repeat-y;">' . we_html_element::htmlImg(array('src' => '/webEdition/images/login/bottom_r.jpg')) . '</td>

	</tr>
	<tr>
		<td></td>
		<td style="background-image:url(/webEdition/images/login/bottom.jpg);background-repeat:repeat-x;">' . we_html_element::htmlImg(array('src' => '/webEdition/images/login/bottom_l.jpg')) . '</td>
		<td>' . we_html_element::htmlImg(array('src' => '/webEdition/images/login/bottom_r2.jpg')) . '</td>
	</tr>

</table>';



	//	PHP-Table
	$_contenttable = 432;
	$_layoutLeft = 14;
	$_layoutLeft2 = 3;
	$_layoutMiddle = 406;
	$_layoutRight1 = 12;
	$_layoutRight2 = 10;
	$_layoutRight = ($_layoutRight1 + $_layoutRight2);

	$_layouttable = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0', 'width' => 440), 4, 5);

	$_layouttable->setCol(0, 0, null, we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/top_left2.gif', 'width' => $_layoutLeft2, 'height' => 21)));
	$_layouttable->setCol(0, 1, null, we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/top_left.gif', 'width' => $_layoutLeft, 'height' => 21)));
	$_layouttable->setCol(0, 2, array('background' => IMAGE_DIR . 'info/top.gif', 'width' => $_layoutMiddle, 'class' => 'small', 'align' => 'right'), '&nbsp;');
	$_layouttable->setCol(0, 3, array('colspan' => 2, 'width' => $_layoutRight), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/top_right.gif', 'width' => $_layoutRight, 'height' => 21)));

	//	Here is table to log in
	$GLOBALS['loginpage'] = ($login == 2) ? false : true;

	include($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_templates/we_info.inc.php');

	$_layouttable->setCol(1, 0, array('background' => IMAGE_DIR . 'info/left2.gif'), we_html_tools::getPixel($_layoutLeft2, 1));
	$_layouttable->setCol(1, 1, array('colspan' => 3, 'width' => $_contenttable), $_loginTable);
	$_layouttable->setCol(1, 4, array('width' => $_layoutRight2, 'background' => IMAGE_DIR . 'info/right.gif'), we_html_tools::getPixel($_layoutRight2, 1));

	$_layouttable->setCol(2, 0, array('width' => $_layoutLeft2), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/bottom_left2.gif', 'width' => $_layoutLeft2, 'height' => 16)));
	$_layouttable->setCol(2, 1, null, we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/bottom_left.gif', 'width' => $_layoutLeft, 'height' => 16)));
	$_layouttable->setCol(2, 2, array('background' => IMAGE_DIR . 'info/bottom.gif'), we_html_tools::getPixel(1, 16));
	$_layouttable->setCol(2, 3, array('colspan' => 2, 'width' => $_layoutRight), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/bottom_right.gif', 'width' => $_layoutRight, 'height' => 16)));

	$_layouttable->setCol(3, 0, null, we_html_tools::getPixel($_layoutLeft2, 1));
	$_layouttable->setCol(3, 1, null, we_html_tools::getPixel($_layoutLeft, 1));
	$_layouttable->setCol(3, 2, null, we_html_tools::getPixel($_layoutMiddle, 1));
	$_layouttable->setCol(3, 3, null, we_html_tools::getPixel($_layoutRight1, 1));
	$_layouttable->setCol(3, 4, null, we_html_tools::getPixel($_layoutRight2, 1));

	/*	 * ***********************************************************************
	 * GENERATE NEEDED JAVASCRIPTS
	 * *********************************************************************** */

	switch($login){
		case 2:
			$_body_javascript = '';

			//	Here the mode - SEEM or normal is saved in the SESSION!!!
			//	Perhaps this must move to another place later.
			//	Later we must check permissions as well!
			if($_REQUEST['mode'] == 'normal'){
				if(permissionhandler::isUserAllowedForAction('work_mode', 'normal')){
					$_SESSION['we_mode'] = $_REQUEST['mode'];
				} else{
					$_body_javascript .= we_message_reporting::getShowMessageCall(g_l('SEEM', '[only_seem_mode_allowed]'), we_message_reporting::WE_MESSAGE_ERROR);
					$_SESSION['we_mode'] = 'seem';
				}
			} else{
				$_SESSION['we_mode'] = $_REQUEST['mode'];
			}

			$_body_javascript .= 'function open_we() {';

			if(isset($_SESSION['prefs']['weWidth']) && $_SESSION['prefs']['weWidth'] > 0){
				$_body_javascript .= 'var aw=' . $_SESSION['prefs']['weWidth'] . ';';
			} else{
				$_body_javascript .= 'var aw=8000;';
			}

			if(isset($_SESSION['prefs']['weHeight']) && $_SESSION['prefs']['weHeight'] > 0){
				$_body_javascript .= 'var ah=' . $_SESSION['prefs']['weHeight'] . ';';
			} else{
				$_body_javascript .= 'var ah=6000;';
			}

			$_body_javascript .= "win = new jsWindow('" . WEBEDITION_DIR . "webEdition.php?h='+ah+'&w='+aw+'&browser='+((document.all) ? 'ie' : 'nn'), '" . md5(uniqid(rand())) . "', -1, -1, aw, ah, true, true, true, true, '" . g_l('alert', "[popupLoginError]") . "', '/webEdition/index.php'); }";
			if(!isset($_REQUEST['popup'])){
				header('HTTP/1.1 303 See Other');
				header('Location: ' . WEBEDITION_DIR . 'webEdition.php');
			}
			break;
		case 1:
			$DB_WE->query('INSERT INTO ' . FAILED_LOGINS_TABLE . ' SET UserTable="tblUser", Username="' . $_POST['username'] . '", IP="' . $_SERVER['REMOTE_ADDR'] . '"');

			/*			 * ***************************************************************************
			 * CHECK FOR FAILED LOGIN ATTEMPTS
			 * *************************************************************************** */
			$cnt = f('SELECT COUNT(1) AS count FROM ' . FAILED_LOGINS_TABLE . ' WHERE IP="' . addslashes($_SERVER['REMOTE_ADDR']) . '" AND LoginDate > DATE_SUB(NOW(), INTERVAL ' . intval(LOGIN_FAILED_TIME) . ' MINUTE)', 'count', $DB_WE);

			if($cnt >= LOGIN_FAILED_NR){
				$_body_javascript = we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[3timesLoginError]"), LOGIN_FAILED_NR, LOGIN_FAILED_TIME), we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				$_body_javascript = we_message_reporting::getShowMessageCall(g_l('alert', "[login_failed]"), we_message_reporting::WE_MESSAGE_ERROR);
			}
			break;
		case 3:
			$_body_javascript = we_message_reporting::getShowMessageCall(g_l('alert', "[login_failed_security]"), we_message_reporting::WE_MESSAGE_ERROR) . "document.location = '/webEdition/index.php" . (($ignore_browser || (isset($_COOKIE["ignore_browser"]) && $_COOKIE["ignore_browser"] == "true")) ? "&ignore_browser=" . (isset($_COOKIE["ignore_browser"]) ? $_COOKIE["ignore_browser"] : ($ignore_browser ? "true" : "false")) : "") . "';";
			break;
		case 4:
			$_body_javascript = we_message_reporting::getShowMessageCall(g_l('alert', "[login_denied_for_user]"), we_message_reporting::WE_MESSAGE_ERROR);
			break;
		default:
	}


	$_layout = we_html_element::htmlDiv(array('style' => 'float: left;height: 50%;width: 1px;')) . we_html_element::htmlDiv(array('style' => 'clear:left;position:relative;top:-25%;'), we_html_element::htmlForm(array("action" => WEBEDITION_DIR . 'index.php', 'method' => 'post', 'name' => 'loginForm'), $_hidden_values . $dialogtable));

	printHeader($login);
	print we_html_element::htmlBody(array('style' => 'background-color:#386AAB; height:100%;', "onload" => (($login == 2) ? "open_we();" : "document.loginForm.username.focus();document.loginForm.username.select();")), $_layout . ((isset($_body_javascript)) ? we_html_element::jsElement($_body_javascript) : '')) . '</html>';
}