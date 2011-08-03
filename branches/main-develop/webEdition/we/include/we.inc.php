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
if (isset($_SERVER['SCRIPT_NAME']) && str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == '/we.inc.php') {
	exit();
}

// remove trailing slash
if (isset($_SERVER['DOCUMENT_ROOT'])) {
	$_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
}

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_inc_min.inc.php'); //	New absolute minimum include for any we-file, reduces memory consumption for special usages about 20 MB.
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/we_util.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf_language.inc.php');

//	Insert all config files for all modules.
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_active_integrated_modules.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_installed_modules.inc.php');

// use the following arrays:
// we_available_modules - modules and informations about integrated and none integrated modules
// we_installed_modules - all installed (none integrated) modules
// we_active_integrated_modules - all active integrated modules
// we_active_modules - all active modules integrated and none integrated
// merge we_installed_modules and we_active_integrated_modules to we_active_modules
$_we_active_modules = array_merge($_we_active_integrated_modules, $_we_installed_modules);

foreach ($_we_active_modules as $active) {
	if (file_exists(
									$_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/' . $active . '/we_conf_' . $active . '.inc.php')) {
		include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/' . $active . '/we_conf_' . $active . '.inc.php');
	}
}

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db_tools.inc.php');

if (isset($_we_active_modules) && in_array('shop', $_we_active_modules)) {
	$MNEMONIC_EDITPAGES['11'] = 'variants';
}
if (isset($_we_active_modules) && in_array('customer', $_we_active_modules)) {
	$MNEMONIC_EDITPAGES['14'] = 'customer';
}


if (!defined('NO_SESS')) {
	include_once ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_session.inc.php");
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/tools/weToolLookup.class.php');
	$_tooldefines = weToolLookup::getDefineInclude();
	if (!empty($_tooldefines)) {
		foreach ($_tooldefines as $_tooldefine) {
			@include_once ($_tooldefine);
		}
	}
	$_tooltagdirs = weToolLookup::getTagDirs();
}

define('MULTIEDITOR_AMOUNT', (isset($_SESSION) && isset($_SESSION['we_mode']) && $_SESSION['we_mode'] == 'seem') ? 1 : 16);

if (defined('WE_WEBUSER_LANGUAGE')) {
	$GLOBALS['WE_LANGUAGE'] = WE_WEBUSER_LANGUAGE;
} else{
	$sid = '';
}
//set new sessionID from dw-extension
if ((isset($_SESSION['user']['ID']) && isset($_REQUEST['weSessionId']) && $_REQUEST['weSessionId'] != '' && isset($_REQUEST['cns']) && $_REQUEST['cns'] == 'dw')) {
	$sid = strip_tags($_REQUEST['weSessionId']);
	$sid = htmlspecialchars($sid);
	session_id($sid);
	@session_start();
}
if (!session_id() && !isset($GLOBALS['FROM_WE_SHOW_DOC'])) {
	@session_start();
}
if (isset($_SESSION['prefs']['Language']) && $_SESSION['prefs']['Language'] != '') {
	if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $_SESSION['prefs']['Language'])) {
		$GLOBALS['WE_LANGUAGE'] = $_SESSION['prefs']['Language'];
	} else { //  bugfix #4229
		$GLOBALS['WE_LANGUAGE'] = WE_LANGUAGE;
		$_SESSION['prefs']['Language'] = WE_LANGUAGE;
	}
} else {
	$GLOBALS['WE_LANGUAGE'] = WE_LANGUAGE;
}
if (isset($_SESSION['prefs']['BackendCharset']) && $_SESSION['prefs']['BackendCharset'] != '') {
	$GLOBALS['WE_BACKENDCHARSET'] = $_SESSION['prefs']['BackendCharset'];
} else {
	$GLOBALS['WE_BACKENDCHARSET'] = defined('WE_BACKENDCHARSET')? WE_BACKENDCHARSET : 'UTF-8';
}

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/define_styles.inc.php');
if (isset($_we_active_modules) && in_array('shop', $_we_active_modules)) {
	$MNEMONIC_EDITPAGES['11'] = 'variants';
}
if (isset($_we_active_modules) && in_array('customer', $_we_active_modules)) {
	$MNEMONIC_EDITPAGES['14'] = 'customer';
}


if (!isset($GLOBALS['WE_IS_DYN'])) {
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_browser_check.inc.php');
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_perms.inc.php');
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_available_modules.inc.php');
	//	At last we set the charset, as determined from the choosen language
	define('WE_DEFAULT_TITLE', 'webEdition::');
	define('WE_DEFAULT_HEAD', '<title>' . WE_DEFAULT_TITLE . '</title>' .
					'<meta http-equiv="expires" content="0">' .
					'<meta http-equiv="pragma" content="no-cache">' .
					'<meta http-equiv="content-type" content="text/html; charset=' . $GLOBALS['WE_BACKENDCHARSET'] . '">' .
					'<script type="text/javascript" src="' . JS_DIR . 'we_showMessage.js"></script>' .
					'<script type="text/javascript" src="' . JS_DIR . 'attachKeyListener.js"></script>'
	);

	
	//send header?
	if (isset($_REQUEST['we_cmd'][0])) {
		switch ($_REQUEST['we_cmd'][0]) {
			case 'edit_link':
			case 'edit_linklist':
			case 'show_newsletter':
			case 'save_document':
			case 'load_editor':
				$header = false;
				break;
			case 'reload_editpage':
				$header = (!($_SESSION['EditPageNr'] == WE_EDITPAGE_PREVIEW ||
								$_SESSION['EditPageNr'] == WE_EDITPAGE_CONTENT ||
								$_SESSION['EditPageNr'] == WE_EDITPAGE_PROPERTIES
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
								isset($_SESSION['we_data'][$_REQUEST['we_transaction']]) &&
								$_SESSION['we_data'][$_REQUEST['we_transaction']][0]['Table'] == FILE_TABLE &&
								$_SESSION['EditPageNr'] == WE_EDITPAGE_PREVIEW
								));
				break;
			default:
				$header = true;
		}
	} else if ((isset($show_stylesheet) && $show_stylesheet)) {
		$header = false;
	} else {
		$header = true;
	}

	if ($header) {
		header('Content-Type: text/html; charset=' . g_l('charset', '[charset]'));
		unset($header);
	}

}

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_message_reporting/we_message_reporting.class.php');

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/weModuleInfo.class.php');