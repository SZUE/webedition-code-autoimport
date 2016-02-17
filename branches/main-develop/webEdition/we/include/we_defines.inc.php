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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf.inc.php');

define('WEBEDITION_DIR', '/webEdition/');

define('WE_INCLUDES_DIR', WEBEDITION_DIR . 'we/include/');
define('TEMPLATES_DIR', WEBEDITION_DIR . 'we/templates');
define('TEMP_DIR', WEBEDITION_DIR . 'we/tmp/');
define('WE_MODULES_DIR', WE_INCLUDES_DIR . 'we_modules/');
define('CSS_DIR', WEBEDITION_DIR . 'css/');


define('WE_APPS_DIR', WEBEDITION_DIR . 'apps/');
define('SITE_DIR', WEBEDITION_DIR . 'site/');
define('IMAGE_DIR', WEBEDITION_DIR . 'images/');
define('ICON_DIR', IMAGE_DIR . 'icons/');
define('HTML_DIR', WEBEDITION_DIR . 'html/');
define('JS_DIR', WEBEDITION_DIR . 'js/');
define('WE_JS_MODULES_DIR', JS_DIR . 'we_modules/');
define('WE_JS_TINYMCE_DIR', JS_DIR . 'wysiwyg/tinymce/');
define('BACKUP_DIR', WEBEDITION_DIR . 'we_backup/');
define('VERSION_DIR', WEBEDITION_DIR . 'we/versions/');
define('LIB_DIR', WEBEDITION_DIR . 'lib/');
define('TINYMCE_SRC_DIR', LIB_DIR . 'additional/tinymce/');
define('WE_USERS_MODULE_DIR', WE_MODULES_DIR . 'users/');
define('WE_CACHE_DIR', WEBEDITION_DIR . 'we/cache/');

define('EDIT_IMAGE_DIR', IMAGE_DIR . 'edit/');

//all paths
define('WEBEDITION_PATH', $_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR);
define('TEMPLATES_PATH', $_SERVER['DOCUMENT_ROOT'] . TEMPLATES_DIR);
define('TEMP_PATH', $_SERVER['DOCUMENT_ROOT'] . TEMP_DIR);
define('WE_APPS_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_APPS_DIR);
define('WE_INCLUDES_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_INCLUDES_DIR);
define('JS_PATH', $_SERVER['DOCUMENT_ROOT'] . JS_DIR);
define('WE_LIB_PATH', $_SERVER['DOCUMENT_ROOT'] . LIB_DIR);
define('WE_MODULES_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_MODULES_DIR);
define('WE_JS_MODULES_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_JS_MODULES_DIR);
define('WE_USERS_MODULE_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_USERS_MODULE_DIR);
define('BACKUP_PATH', $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR);
define('WE_CACHE_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_CACHE_DIR);

//paths without "DIRS"
define('WE_FRAGMENT_PATH', WEBEDITION_PATH . 'fragments/');
define('ZENDCACHE_PATH', WE_CACHE_PATH); //FIXME: remove

include_once (WE_INCLUDES_PATH . 'we_version.php');

define('CATEGORY_TABLE', TBL_PREFIX . 'tblCategorys');
define('CAPTCHA_TABLE', TBL_PREFIX . 'tblCaptcha');
define('CLEAN_UP_TABLE', TBL_PREFIX . 'tblCleanUp');
define('CONTENT_TABLE', TBL_PREFIX . 'tblContent');
define('DOC_TYPES_TABLE', TBL_PREFIX . 'tblDocTypes');
define('ERROR_LOG_TABLE', TBL_PREFIX . 'tblErrorLog');
define('FAILED_LOGINS_TABLE', TBL_PREFIX . 'tblFailedLogins');
define('FILE_TABLE', TBL_PREFIX . 'tblFile');
define('INDEX_TABLE', TBL_PREFIX . 'tblIndex');
define('LINK_TABLE', TBL_PREFIX . 'tblLink');
define('LANGLINK_TABLE', TBL_PREFIX . 'tblLangLink');
define('PREFS_TABLE', TBL_PREFIX . 'tblPrefs');
define('RECIPIENTS_TABLE', TBL_PREFIX . 'tblRecipients');
define('TEMPLATES_TABLE', TBL_PREFIX . 'tblTemplates');
define('TEMPORARY_DOC_TABLE', TBL_PREFIX . 'tblTemporaryDoc');
define('UPDATE_LOG_TABLE', TBL_PREFIX . 'tblUpdateLog');
define('THUMBNAILS_TABLE', TBL_PREFIX . 'tblthumbnails');
define('VALIDATION_SERVICES_TABLE', TBL_PREFIX . 'tblvalidationservices');
define('HISTORY_TABLE', TBL_PREFIX . 'tblhistory');
define('FORMMAIL_LOG_TABLE', TBL_PREFIX . 'tblformmaillog');
define('FORMMAIL_BLOCK_TABLE', TBL_PREFIX . 'tblformmailblock');
define('METADATA_TABLE', TBL_PREFIX . 'tblMetadata');
define('METAVALUES_TABLE', TBL_PREFIX . 'tblMetaValues');
define('NOTEPAD_TABLE', TBL_PREFIX . 'tblwidgetnotepad');
define('PWDRESET_TABLE', TBL_PREFIX . 'tblPasswordReset');
define('VERSIONS_TABLE', TBL_PREFIX . 'tblversions');
define('VERSIONSLOG_TABLE', TBL_PREFIX . 'tblversionslog');
define('SESSION_TABLE', TBL_PREFIX . 'tblSessions');
define('NAVIGATION_TABLE', TBL_PREFIX . 'tblnavigation');
define('NAVIGATION_RULE_TABLE', TBL_PREFIX . 'tblnavigationrules');
define('USER_TABLE', TBL_PREFIX . 'tblUser');
define('LOCK_TABLE', TBL_PREFIX . 'tblLock');
define('SETTINGS_TABLE', TBL_PREFIX . 'tblSettings');
define('VFILE_TABLE', TBL_PREFIX . 'tblVFile');
define('FILELINK_TABLE', TBL_PREFIX . 'tblFileLink');

//NOTE: you have to register the tables at we.inc!

define('SESSION_NAME', 'WESESSION');

/**
 * Fix the none existing $_SERVER['REQUEST_URI'] on IIS
 */
if(!isset($_SERVER['REQUEST_URI'])){
	if(!isset($_SERVER['HTTP_REQUEST_URI'])){
		$_SERVER['HTTP_REQUEST_URI'] = (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF']) .
			(isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
	}
}

if(!defined('DATETIME_INITIALIZED')){// to prevent additional initialization if set somewhere else, i.e in autoload, this also allows later to make that an settings-item
	if(!date_default_timezone_set(@date_default_timezone_get())){
		date_default_timezone_set('Europe/Berlin');
	}
	define('DATETIME_INITIALIZED', 1);
}

if(!isset($GLOBALS['WE_LANGUAGE'])){
	$GLOBALS['WE_LANGUAGE'] = WE_LANGUAGE;
}
if(!isset($GLOBALS['WE_BACKENDCHARSET'])){
	$GLOBALS['WE_BACKENDCHARSET'] = WE_BACKENDCHARSET;
}