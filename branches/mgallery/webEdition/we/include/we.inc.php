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
// exit if script called directly
if(isset($_SERVER['SCRIPT_NAME']) && str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(dirname(__FILE__), '', __FILE__)){
	exit();
}

// remove trailing slash
if(isset($_SERVER['DOCUMENT' . '_ROOT'])){ //so zerlegt stehen lassen: Bug #6318
	$_SERVER['DOCUMENT' . '_ROOT'] = rtrim($_SERVER['DOCUMENT' . '_ROOT'], '/');
}

foreach(array('HTTP_USER_AGENT', 'PHP_SELF', 'REQUEST_URI', 'QUERY_STRING', 'REDIRECT_URL') as $cur){
	if(isset($_SERVER[$cur])){
		$_SERVER[$cur] = strtr($_SERVER[$cur], array('<' => '%3C', '>' => '%3E', '"' => '%22', '\'' => '%27', '`' => '%60'));
	}
}

//due to hoster bugs (1&1) we have to ensure servername is the called url. since http-host is not safe, we do some security additions.
if(isset($_SERVER['HTTP_HOST']) && $_SERVER['SERVER_NAME'] != $_SERVER['HTTP_HOST']){
	//some security checks
	if(strlen($_SERVER['HTTP_HOST']) < 256 && strpos($_SERVER['HTTP_HOST'], $_SERVER['SERVER_NAME'])){
		$_SERVER['SERVER_NAME'] = rawurlencode($_SERVER['HTTP_HOST']);
	}
}

// Set PHP flags
@ini_set('allow_url_fopen', '1');
@ini_set('file_uploads', '1');
@ini_set('session.use_trans_sid', '0');
//@ini_set("arg_separator.output","&");
//fix insecure cookies
$cookie = session_get_cookie_params();
session_set_cookie_params($cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure'], true);


//prepare space for we-variables; $_SESSION['weS'] is set in we_session
if(!isset($GLOBALS['we'])){
	$GLOBALS['we'] = array();
}

if(!(defined('SYSTEM_WE_SESSION') && SYSTEM_WE_SESSION) && ini_get('session.gc_probability') != '0' /* && !@opendir(session_save_path()) */){
//	$GLOBALS['FOUND_SESSION_PROBLEM'] = ini_get('session.gc_probability');
	ini_set('session.gc_probability', '0');
	//won't work with apps like phpmyadmin session_save_path($_SERVER['DOCUMENT_ROOT'] . TEMP_DIR);
}

//start autoloader!
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_autoload.inc.php');
//register all used tables.
we_base_request::registerTables(array(
	CATEGORY_TABLE, CAPTCHA_TABLE, CLEAN_UP_TABLE, CONTENT_TABLE, DOC_TYPES_TABLE, ERROR_LOG_TABLE, FAILED_LOGINS_TABLE, FILE_TABLE, INDEX_TABLE, LINK_TABLE, LANGLINK_TABLE, PREFS_TABLE, RECIPIENTS_TABLE, TEMPLATES_TABLE, TEMPORARY_DOC_TABLE, UPDATE_LOG_TABLE, THUMBNAILS_TABLE, VALIDATION_SERVICES_TABLE, HISTORY_TABLE, FORMMAIL_LOG_TABLE, FORMMAIL_BLOCK_TABLE, METADATA_TABLE, NOTEPAD_TABLE, PWDRESET_TABLE, VERSIONS_TABLE, VERSIONSLOG_TABLE, SESSION_TABLE, NAVIGATION_TABLE, NAVIGATION_RULE_TABLE, USER_TABLE, LOCK_TABLE, SETTINGS_TABLE, VFILE_TABLE, FILELINK_TABLE
));

require_once(WE_INCLUDES_PATH . 'we_global.inc.php');
update_mem_limit(32);

we_loadLanguageConfig();


//	Insert all config files for all modules.
include_once(WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php');

// use the following arrays:
// we_available_modules - modules and informations about integrated and none integrated modules
// we_active_integrated_modules - all active integrated modules
//if file corrupted try to load defaults
if(empty($GLOBALS['_we_active_integrated_modules']) || !in_array('users', $GLOBALS['_we_active_integrated_modules'])){
	include_once(WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php.default');
}
//FIXME: don't include all confs!
foreach($GLOBALS['_we_active_integrated_modules'] as $active){
	we_base_moduleInfo::isActive($active);
}

if(!isset($GLOBALS['DB_WE'])){
	$GLOBALS['DB_WE'] = new DB_WE();
}

if(!(defined('NO_SESS') || isset($GLOBALS['FROM_WE_SHOW_DOC']))){
	$GLOBALS['WE_BACKENDCHARSET'] = 'UTF-8'; //Bug 5771 schon in der Session wird ein vorläufiges Backendcharset benötigt
	require_once(WE_INCLUDES_PATH . 'we_session.inc.php');
	$_tooldefines = we_tool_lookup::getDefineInclude();
	if($_tooldefines){
		foreach($_tooldefines as $_tooldefine){
			@include_once($_tooldefine);
		}
	}
}

if(defined('WE_WEBUSER_LANGUAGE')){
	$GLOBALS['WE_LANGUAGE'] = WE_WEBUSER_LANGUAGE;
} else {
	$sid = '';
}


if(!empty($_SESSION['prefs']['Language'])){
	$GLOBALS['WE_LANGUAGE'] = (is_dir(WE_INCLUDES_PATH . 'we_language/' . $_SESSION['prefs']['Language']) ?
			$_SESSION['prefs']['Language'] :
			//  bugfix #4229
			($_SESSION['prefs']['Language'] = WE_LANGUAGE));
} else {
	$GLOBALS['WE_LANGUAGE'] = WE_LANGUAGE;
}

define('SCRIPT_BUTTONS_ONLY', we_html_element::jsScript(JS_DIR . 'weButton.js'));
define('STYLESHEET_BUTTONS_ONLY', we_html_element::cssLink(CSS_DIR . 'we_button.css') . we_html_element::cssLink(LIB_DIR . 'additional/fontawesome/css/font-awesome.min.css'));
define('STYLESHEET_SCRIPT', we_html_element::cssLink(CSS_DIR . 'global.php') .
	STYLESHEET_BUTTONS_ONLY .
	we_html_element::cssLink(CSS_DIR . 'webEdition.css')
);
define('STYLESHEET', STYLESHEET_SCRIPT . SCRIPT_BUTTONS_ONLY);

if(!isset($GLOBALS['WE_IS_DYN'])){ //only true on dynamic frontend pages
	$GLOBALS['WE_BACKENDCHARSET'] = (!empty($_SESSION['prefs']['BackendCharset']) ?
			$_SESSION['prefs']['BackendCharset'] : 'UTF-8');

	//send header?
	switch(isset($_REQUEST['we_cmd']) && !is_array($_REQUEST['we_cmd']) ? we_base_request::_(we_base_request::STRING, 'we_cmd', '__default__') : ''){
		case 'edit_link':
		case 'edit_linklist':
		case 'show_newsletter':
		case 'save_document':
		case 'load_editor':
			$header = false;
			break;
		case 'reload_editpage':
			$header = (!in_array($_SESSION['weS']['EditPageNr'], array(we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_PREVIEW, we_base_constants::WE_EDITPAGE_PROPERTIES)));
			break;
		case 'switch_edit_page':
			$header = (!in_array(we_base_request::_(we_base_request::INT, 'we_cmd', -1, 1), array(we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_PREVIEW, we_base_constants::WE_EDITPAGE_PROPERTIES)));
			break;
		case 'load_editor':
			$trans = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', '__NO_TRANS__');
			$header = (!(isset($_SESSION['weS']['we_data'][$trans]) &&
				$_SESSION['weS']['we_data'][$trans][0]['Table'] == FILE_TABLE &&
				$_SESSION['weS']['EditPageNr'] == we_base_constants::WE_EDITPAGE_PREVIEW
				));
			break;
		case '__default__':
			$header = empty($GLOBALS['show_stylesheet']);
			break;
		default:
			$header = true;
	}

	if($header){
		we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);
	}
	unset($header);
}
