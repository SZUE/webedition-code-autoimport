<?php

/**
 * Part2: make demo-version after online-installation
 * @see /we4/includes/replaceCode/replaceCode.inc.php
 */
// add licensee to we_conf.inc.php
$replaceCode['we_conf_demo']['path']['3900'] = '/webEdition/we/include/conf/we_conf.inc%s';
$replaceCode['we_conf_demo']['replace']['3900'] = <<< weConfDemoSaveCodeBoundary
<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | Copyright (c) webEdition Software GmbH                               |
// +----------------------------------------------------------------------+
//

/**
 * Configuration file for webEdition
 * =================================
 *
 * Must be adjusted to the current environment!
 *
 * NOTE:
 * =====
 * Edit this file ONLY if you know exactly what you are doing!
 */

/*****************************************************************************
 * SERVER SETTINGS
 *****************************************************************************/

/**
 * When adding a password protection to the webEdition directory uncomment the
 * following lines and adjust the given values.
 *
 * For example "myUsername" should be changed to "whatever" if "whatever"
 * would be the username to access the directory.
 */

//define("HTTP_USERNAME", "myUsername");
//define("HTTP_PASSWORD", "myPassword");

if (isset(\$_SERVER["HTTP_HOST"])) {
	if (ereg("(.*):(.*)", \$_SERVER["HTTP_HOST"], \$regs)) {
		\$SERVER_NAME = \$regs[1];
		\$SERVER_PORT = \$regs[2];
	} else {
		\$SERVER_NAME = \$_SERVER["HTTP_HOST"];
	}
}

if (isset(\$SERVER_PORT) && \$SERVER_PORT != 80) {
	define("HTTP_PORT", \$SERVER_PORT);
}

define("SERVER_NAME", \$SERVER_NAME);

/*****************************************************************************
 * DATABASE SETTINGS
 *****************************************************************************/

// Domain or IP address of the database server
define("DB_HOST","%s");

// Name of database being used by webEdition
define("DB_DATABASE","%s");

// Username to access the database
define("DB_USER","%s");

// Password to access the database
define("DB_PASSWORD","%s");

// Mode how to access the database
//
// "connect":  This mode lets webEdition establishing a connection to the
//             database server that will be closed as soon as the execution of
//             a script ends.
// "pconnect": Using this mode webEdition will first, when connecting to the
//             database, try to find a (persistent) link that's already open
//             with the same host. Second, the connection to the database server
//             will not be closed when execution of a script ends. Instead, the
//             link will remain open for future use.

// Don't change this line!!!
define("DB_CONNECT","%s");

// Prefix of tables in database for this webEdition..
define("TBL_PREFIX","%s");

// Database wrapper class of webEdition
include_once(\$_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."db_mysql.inc.php");



/*****************************************************************************
 * GLOBAL WEBEDITION SETTINGS
 *****************************************************************************/

// Name of licensee
define("WE_LIZENZ","%s");

// Path to the templates directory
define("TEMPLATE_DIR",\$_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/templates");

// Path to the temporary files directory
define("TMP_DIR",\$_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/tmp");

// Original language of this version of webEdition, used for login-screen
define("WE_LANGUAGE","%s");

if (!isset(\$GLOBALS["WE_LANGUAGE"])) {
	\$GLOBALS["WE_LANGUAGE"] = WE_LANGUAGE;
}

define('LIVEUPDATE_INSTALLED_WITH_CONTENT', true);

?>
weConfDemoSaveCodeBoundary;

$replaceCode['we_conf_global_demo']['path']['3900'] = '/webEdition/we/include/conf/we_conf_global.inc%s';
$replaceCode['we_conf_global_demo']['replace']['3900'] = <<< weConfGlobalDemoSaveCodeBoundary
<?php

// +----------------------------------------------------------------------+
// | webEdition                                                           |
// +----------------------------------------------------------------------+
// | PHP version 4.1.0 or greater                                         |
// +----------------------------------------------------------------------+
// | Copyright (c) 2000 - 2007 webEdition Software GmbH                   |
// +----------------------------------------------------------------------+
//


/**
 * Configuration file for webEdition
 * =================================
 *
 * Holds the globals settings of webEdition.
 *
 * NOTE:
 * =====
 * Edit this file ONLY if you know exactly what you are doing!
 */

if (file_exists(\$_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/conf/we_error_conf.inc.php")) {
	include_once(\$_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/conf/we_error_conf.inc.php");
}

/*****************************************************************************
 * SEEMODE SETTINGS
 *****************************************************************************/

// Enable seeMode
define("WE_SEEM", 1);


/*****************************************************************************
 * ERROR HANDLING SETTINGS
 *****************************************************************************/

// Show errors that occur in webEdition
define("WE_ERROR_HANDLER", 0);

// Handle notices
define("WE_ERROR_NOTICES", 0);

// Handle warnings
define("WE_ERROR_WARNINGS", 0);

// Handle errors
define("WE_ERROR_ERRORS", 0);

// Show errors
define("WE_ERROR_SHOW", 0);

// Log errors
define("WE_ERROR_LOG", 0);

// Mail errors
define("WE_ERROR_MAIL", 0);

// E-Mail address to which to mail errors
define("WE_ERROR_MAIL_ADDRESS", "mail@somedomain.com");

// Number of entries per batch
define("BACKUP_STEPS", 7);

// Directory in which to save thumbnails
define("WE_THUMBNAIL_DIRECTORY", "/__we_thumbs__");


// Default setting for inlineedit attribute
define("INLINEEDIT_DEFAULT", 0);

// Default setting for xml attribute
define("XHTML_DEFAULT", 1);

// Enable XHTML debug
define("XHTML_DEBUG", "false");

// Remove wrong xhtml attributes from we:tags
define("XHTML_REMOVE_WRONG", "false");

//
define("WE_MAX_UPLOAD_SIZE", 0);

//
define("WE_DOCTYPE_WORKSPACE_BEHAVIOR", 0);

// Default setting for showinputs attribute
define("SHOWINPUTS_DEFAULT", 1);

// File permissions when creating a new directory
define("WE_NEW_FOLDER_MOD", 755);

// Directory in which pageLogger is installed
define("WE_TRACKER_DIR", "");

// Flag if beta wysiwyg for safari should be used
define("SAFARI_WYSIWYG", 1);

// Document to open when trying to open non-existing object
define("ERROR_DOCUMENT_NO_OBJECTFILE", 0);

//Cache Type
define("WE_CACHE_TYPE", "none");

//Cache Life Time
define("WE_CACHE_LIFETIME", 0);

// Disable the check for missing close tags in templates
define("DISABLE_TEMPLATE_TAG_CHECK", 0);

// Flag if formmail calls should be logged
define("FORMMAIL_LOG", 1);

// Time in seconds
define("FORMMAIL_SPAN", 60);

// Num of trials sending formmail with same ip address in span
define("FORMMAIL_TRIALS", 3);

// Flag if formmail calls should be blocked after a time
define("FORMMAIL_BLOCK", 1);

// Time to block ip
define("FORMMAIL_BLOCKTIME", 86400);

// Time how long formmail calls should be logged
define("FORMMAIL_EMPTYLOG", 604800);

?>
weConfGlobalDemoSaveCodeBoundary;

// add version and uid
$replaceCode['we_version_demo']['path']['3900'] = '/webEdition/we/include/we_version%s';
$replaceCode['we_version_demo']['replace']['3900'] = '<?php
define("WE_VERSION", "%s");
%s
?>';


// Proxysettings
$replaceCode['we_proxysettings']['path']['3900'] = '/webEdition/liveUpdate/includes/proxysettings.inc%s';
$replaceCode['we_proxysettings']['replace']['3900'] = '<?php
	define("WE_PROXYHOST", "%s");
	define("WE_PROXYPORT", "%s");
	define("WE_PROXYUSER", "%s");
	define("WE_PROXYPASSWORD", "%s");
?>';

// enable demo pop-up webEdition.php
$replaceCode['webEdition_demo']['path']['3900'] = '/webEdition/webEdition%s';
$replaceCode['webEdition_demo']['needle']['3900'] = 'var we_demo = false;';
$replaceCode['webEdition_demo']['replace']['3900'] = 'var we_demo = true;';

// change menu entries
$replaceCode['menu1_demo']['path']['3900'] = '/webEdition/we/include/java_menu/we_menu.inc%s';
$replaceCode['menu1_demo']['needle']['3900'] = '\$we_menu\["004400"\]\["text"\] = \$l_javaMenu\["update"\]';
$replaceCode['menu1_demo']['replace']['3900'] = '$we_menu["004400"]["text"] = $l_javaMenu["register"]';

$replaceCode['menu2_demo']['path']['3900'] = '/webEdition/we/include/java_menu/we_menu.inc%s';
$replaceCode['menu2_demo']['needle']['3900'] = '\$we_menu\["004399"\]\["text"\] = \$l_javaMenu\["module_installation"\]';
$replaceCode['menu2_demo']['replace']['3900'] = '$we_menu["004399"]["text"] = $l_javaMenu["register"]';

// template savecode
$replaceCode['templateSaveCode_demo']['path']['3900'] = '/webEdition/we/include/we_editors/we_editor.inc%s';
$replaceCode['templateSaveCode_demo']['needle']['3900'] = '####TEMPLATE_SAVE_CODE2_START###.*####TEMPLATE_SAVE_CODE2_END###'; // ! IMPORTANT
$replaceCode['templateSaveCode_demo']['replace']['3900'] = '#save template2';

$replaceCode['we_conf_content']['path']['3900'] = '/webEdition/we/include/conf/we_conf.inc%s';
$replaceCode['we_conf_content']['needle']['3900'] = 'define\(\'LIVEUPDATE_INSTALLED_WITH_CONTENT\', .*\);';
$replaceCode['we_conf_content']['replace']['3900'] = 'define(\'LIVEUPDATE_INSTALLED_WITH_CONTENT\', %s);';

?>