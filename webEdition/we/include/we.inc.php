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
// exit if script called directly
if(isset($_SERVER['SCRIPT_NAME']) && str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(dirname(__FILE__), '', __FILE__)){
	exit();
}

// remove trailing slash
if(isset($_SERVER['DOCUMENT' . '_ROOT'])){ //so zerlegt stehen lassen: Bug #6318
	$_SERVER['DOCUMENT' . '_ROOT'] = rtrim($_SERVER['DOCUMENT' . '_ROOT'], '/');
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
//FIXME: how to handle secure connections - do we allow session upgrades?
session_set_cookie_params($cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure'], true);


//prepare space for we-variables; $_SESSION['weS'] is set in we_session
if(!isset($GLOBALS['we'])){
	$GLOBALS['we'] = array();
}
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_defines.inc.php');
if(ini_get('session.gc_probability') == '1' && !@opendir(session_save_path())){
	$GLOBALS['FOUND_SESSION_PROBLEM'] = session_save_path();
	//ini_set('session.gc_probability', '0');
	session_save_path($_SERVER['DOCUMENT_ROOT'] . TEMP_DIR);
}
//start autoloader!
require_once ($_SERVER['DOCUMENT_ROOT'] . LIB_DIR . 'we/core/autoload.php');

require_once (WE_INCLUDES_PATH . 'we_global.inc.php');
update_mem_limit(32);

include_once (WE_INCLUDES_PATH . 'conf/we_conf_language.inc.php');

//	Insert all config files for all modules.
include_once (WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php');

// use the following arrays:
// we_available_modules - modules and informations about integrated and none integrated modules
// we_active_integrated_modules - all active integrated modules
//if file corrupted try to load defaults
if(empty($GLOBALS['_we_active_integrated_modules']) || !in_array('users', $GLOBALS['_we_active_integrated_modules'])){
	include_once (WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php.default');
}
//$GLOBALS['_we_active_integrated_modules'][] = 'navigation';//TODO: remove when navigation is completely implemented as a module

foreach($GLOBALS['_we_active_integrated_modules'] as $active){
	if(file_exists(WE_MODULES_PATH . $active . '/we_conf_' . $active . '.inc.php')){
		require_once (WE_MODULES_PATH . $active . '/we_conf_' . $active . '.inc.php');
	}
}

if(!isset($GLOBALS['DB_WE'])){
	$GLOBALS['DB_WE'] = new DB_WE();
}

if(!defined('NO_SESS') && !isset($GLOBALS['FROM_WE_SHOW_DOC'])){
	$GLOBALS['WE_BACKENDCHARSET'] = 'UTF-8'; //Bug 5771 schon in der Session wird ein vorläufiges Backendcharset benötigt
	require_once (WE_INCLUDES_PATH . 'we_session.inc.php');
	$_tooldefines = we_tool_lookup::getDefineInclude();
	if(!empty($_tooldefines)){
		foreach($_tooldefines as $_tooldefine){
			@include_once ($_tooldefine);
		}
	}
}

if(defined('WE_WEBUSER_LANGUAGE')){
	$GLOBALS['WE_LANGUAGE'] = WE_WEBUSER_LANGUAGE;
} else {
	$sid = '';
}


if(isset($_SESSION['prefs']['Language']) && !empty($_SESSION['prefs']['Language'])){
	$GLOBALS['WE_LANGUAGE'] = (is_dir(WE_INCLUDES_PATH . 'we_language/' . $_SESSION['prefs']['Language']) ?
			$_SESSION['prefs']['Language'] :
			//  bugfix #4229
			($_SESSION['prefs']['Language'] = WE_LANGUAGE));
} else {
	$GLOBALS['WE_LANGUAGE'] = WE_LANGUAGE;
}

if(!isset($GLOBALS['WE_IS_DYN'])){ //only true on dynamic frontend pages
	$GLOBALS['WE_BACKENDCHARSET'] = (isset($_SESSION['prefs']['BackendCharset']) && $_SESSION['prefs']['BackendCharset'] != '' ?
			$_SESSION['prefs']['BackendCharset'] : 'UTF-8');

	include_once (WE_INCLUDES_PATH . 'define_styles.inc.php');
	//FIXME: remove
	include_once (WE_INCLUDES_PATH . 'we_available_modules.inc.php');
	//FIXME: needed by liveupdate, calls old protect directly remove in 6.4
	require_once (WE_INCLUDES_PATH . 'we_perms.inc.php');


	//send header?
	if(isset($_REQUEST['we_cmd'][0])){
		switch($_REQUEST['we_cmd'][0]){
			case 'edit_link':
			case 'edit_linklist':
			case 'show_newsletter':
			case 'save_document':
			case 'load_editor':
				$header = false;
				break;
			case 'reload_editpage':
				$header = (!($_SESSION['weS']['EditPageNr'] == WE_EDITPAGE_PREVIEW ||
					$_SESSION['weS']['EditPageNr'] == WE_EDITPAGE_CONTENT ||
					$_SESSION['weS']['EditPageNr'] == WE_EDITPAGE_PROPERTIES
					));
				break;
			case 'switch_edit_page':
				$header = (!($_REQUEST['we_cmd'][1] == WE_EDITPAGE_CONTENT ||
					$_REQUEST['we_cmd'][1] == WE_EDITPAGE_PREVIEW ||
					$_REQUEST['we_cmd'][1] == WE_EDITPAGE_PROPERTIES
					));
				break;
			case 'load_editor':
				$header = (!(isset($_REQUEST['we_transaction']) &&
					isset($_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]) &&
					$_SESSION['weS']['we_data'][$_REQUEST['we_transaction']][0]['Table'] == FILE_TABLE &&
					$_SESSION['weS']['EditPageNr'] == WE_EDITPAGE_PREVIEW
					));
				break;
			default:
				$header = true;
		}
	} else {
		$header = !((isset($GLOBALS['show_stylesheet']) && $GLOBALS['show_stylesheet']));
	}

	if($header){
		we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);
	}
	unset($header);
}