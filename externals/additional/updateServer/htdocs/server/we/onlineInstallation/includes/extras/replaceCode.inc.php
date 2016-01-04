<?php

/**
 * Part2: make demo-version after online-installation
 * @see /we4/includes/replaceCode/replaceCode.inc.php
 */

$replaceCode['we_conf_demo']['path'][4900] = '/webEdition/we/include/conf/we_conf.inc%s';
$replaceCode['we_conf_demo']['replace'][4900] = <<< weConfDemoSaveCodeBoundary
<?php

/**
 * webEdition CMS
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
	\$matches = parse_url(\$_SERVER["HTTP_HOST"]);
	if(isset(\$matches["port"]) && !empty(\$matches["port"])) {
		\$SERVER_NAME = \$matches["host"];
		\$SERVER_PORT = \$matches["port"];
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
define("DB_HOST",'%s');

// Name of database being used by webEdition
define("DB_DATABASE",'%s');

// Username to access the database
define("DB_USER",base64_decode('%s'));

// Password to access the database
define("DB_PASSWORD",base64_decode('%s'));

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
define("DB_CONNECT",'%s');

// Prefix of tables in database for this webEdition.
define("TBL_PREFIX",'%s');

// Charset of tables in database for this webEdition.
define("DB_CHARSET",'%s');

// Collation of tables in database for this webEdition.
define("DB_COLLATION",'%s');

// Database wrapper class of webEdition
//include_once(\$_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."db_mysql.inc.php");



/*****************************************************************************
 * GLOBAL WEBEDITION SETTINGS
 *****************************************************************************/

// Name of licensee
define("WE_LIZENZ",'%s');

// Path to the templates directory
define("TEMPLATE_DIR",\$_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/templates");

// Path to the temporary files directory
define("TMP_DIR",\$_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/tmp");

// Original language of this version of webEdition, used for login-screen
define("WE_LANGUAGE",'%s');

// Original backend charset of this version of webEdition, used for login-screen
define("WE_BACKENDCHARSET",'%s');

if (!isset(\$GLOBALS["WE_LANGUAGE"])) {
	\$GLOBALS["WE_LANGUAGE"] = WE_LANGUAGE;
}

if (!isset(\$GLOBALS["WE_LANGUAGE"])) {
	\$GLOBALS["WE_BACKENDCHARSET"] = WE_BACKENDCHARSET;
}

// PHP 5.3 date init #4353
if (!date_default_timezone_set(@date_default_timezone_get())){
	date_default_timezone_set('Europe/Berlin');
}
define("DATETIME_INITIALIZED",'1'); // to prevent additional initialization in we_defines und autoload, this allows later to make that an settings-item

//define ("WE_SQL_DEBUG", 1);
define('LIVEUPDATE_INSTALLED_WITH_CONTENT', true);

?>
weConfDemoSaveCodeBoundary;

$replaceCode['we_conf_demo']['replace'][6391] = <<< weConfDemoSaveCodeBoundary
<?php

/**
 * webEdition CMS
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

// Domain or IP address of the database server
define("DB_HOST",'%s');

// Name of database being used by webEdition
define("DB_DATABASE",'%s');

// Username to access the database
define("DB_USER",base64_decode('%s'));

// Password to access the database
define("DB_PASSWORD",base64_decode('%s'));

// Don't change this line!!!
define("DB_CONNECT",'%s');

// Prefix of tables in database for this webEdition.
define("TBL_PREFIX",'%s');

// Charset of tables in database for this webEdition.
define("DB_CHARSET",'%s');

// Collation of tables in database for this webEdition.
define("DB_COLLATION",'%s');

// Name of licensee
define("WE_LIZENZ",'%s');

// Original language of this version of webEdition, used for login-screen
define("WE_LANGUAGE",'%s');

// Original backend charset of this version of webEdition, used for login-screen
define("WE_BACKENDCHARSET",'%s');

?>
weConfDemoSaveCodeBoundary;

$replaceCode['we_conf_global_demo']['path'][4900] = '/webEdition/we/include/conf/we_conf_global.inc%s';
$replaceCode['we_conf_global_demo']['replace'][4900] = <<< weConfGlobalDemoSaveCodeBoundary
<?php

/**
 * webEdition CMS
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
define("WE_ERROR_HANDLER", 1);

// Handle notices
define("WE_ERROR_NOTICES", 1);

// Handle warnings
define("WE_ERROR_WARNINGS", 1);

// Handle deprecated warnings
define("WE_ERROR_DEPRECATED", 0);

// Handle errors
define("WE_ERROR_ERRORS", 1);

// Show errors
define("WE_ERROR_SHOW", 0);

// Log errors
define("WE_ERROR_LOG", 1);

// Mail errors
define("WE_ERROR_MAIL", 0);

// E-Mail address to which to mail errors
define("WE_ERROR_MAIL_ADDRESS", "mail@example.com");

// Number of entries per batch
define("BACKUP_STEPS", 80);

// Directory in which to save thumbnails
define("WE_THUMBNAIL_DIRECTORY", "/__we_thumbs__");


// Default setting for inlineedit attribute
define("INLINEEDIT_DEFAULT", 0);

// Default setting for attribute imagestartdir in wetextarea
define("IMAGESTARTID_DEFAULT", 0);

// Default setting for removeparagraph attribute
define("REMOVEFIRSTPARAGRAPH_DEFAULT", 0);

// Default setting for commands attribute
define("COMMANDS_DEFAULT", '');

// Default setting for hide name attribute in weimg output
define("HIDENAMEATTRIBINWEIMG_DEFAULT", 0);

// Default setting for hide name attribute in weform output
define("HIDENAMEATTRIBINWEFORM_DEFAULT", 0);

// Default setting for tag inclusion
define("INCLUDE_ALL_WE_TAGS", 0);

// Default setting for xml attribute
define("XHTML_DEFAULT", 1);

// Enable XHTML debug
define("XHTML_DEBUG", 0);

// Remove wrong xhtml attributes from we:tags
define("XHTML_REMOVE_WRONG", 0);

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


// Flag if formmail confirm function should be work
define("FORMMAIL_CONFIRM", 1);


//mailer type; possible values are php and smtp
define("WE_MAILER", "php");


//SMTP server address
define("SMTP_SERVER", "localhost");


//SMTP server port
define("SMTP_PORT", 25);


//SMTP authentication
define("SMTP_AUTH", 0);


//SMTP halo string
define("SMTP_HALO", "");


//SMTP timeout
define("SMTP_TIMEOUT", 30);


//SMTP username
define("SMTP_USERNAME", "");


//SMTP password
define("SMTP_PASSWORD", "");

//SMTP encryption
define("SMTP_ENCRYPTION", 0);

// Sidebar is disabled
define("SIDEBAR_DISABLED", 0);

// Default document id of the sidebar
define("SIDEBAR_DEFAULT_DOCUMENT", 0);

// Default width of the sidebar
define("SIDEBAR_DEFAULT_WIDTH", 300);

// Default setting for php attribute
define("WE_PHP_DEFAULT", 0);


// Show Sidebar on startup
define("SIDEBAR_SHOW_ON_STARTUP", 0);

// Default static extension
define("DEFAULT_STATIC_EXT", ".html");

// Default dynamic extension
define("DEFAULT_DYNAMIC_EXT", ".php");

// Default html extension
define("DEFAULT_HTML_EXT", ".html");

// Flag if formmail should be send only via a webEdition document
define("FORMMAIL_VIAWEDOC", 0);

// Flag if new NAV- entries added from Dokument should be items or folders
define("NAVIGATION_ENTRIES_FROM_DOCUMENT", "0");

// Default Charset wichtig: klein Leerzeichen nach/vor Komma im define, f�r Tarball-Setup exakt so stehen lassen
define("DEFAULT_CHARSET","%s");

// Default setting for hook execution
define("EXECUTE_HOOKS", 0);

// connection charset to db wichtig: klein Leerzeichen nach/vor Komma im define
define("DB_SET_CHARSET","%s");

//Versioning status for ContentType image/*
define("VERSIONING_IMAGE", 0);

//Versioning status for ContentType text/html
define("VERSIONING_TEXT_HTML", 0);

//Versioning status for ContentType text/weTmpl
define("VERSIONING_TEXT_WETMPL", 1);

//Versioning status for ContentType text/webedition
define("VERSIONING_TEXT_WEBEDITION", 1);

//Versioning status for ContentType text/htaccess
define("VERSIONING_TEXT_HTACCESS", 0);

//Versioning status for ContentType text/js
define("VERSIONING_TEXT_JS", 0);

//Versioning status for ContentType text/css
define("VERSIONING_TEXT_CSS", 0);

//Versioning status for ContentType text/plain
define("VERSIONING_TEXT_PLAIN", 0);

//Versioning status for ContentType application/x-shockwave-flash
define("VERSIONING_FLASH", 0);

//Versioning status for ContentType video/quicktime
define("VERSIONING_QUICKTIME", 0);

//Versioning status for ContentType application/*
define("VERSIONING_SONSTIGE", 0);

//Versioning status for ContentType text/xml
define("VERSIONING_TEXT_XML", 0);

//Versioning status for ContentType objectFile
define("VERSIONING_OBJECT", 0);

//Versioning Number of Days
define("VERSIONS_TIME_DAYS", -1);

//Versioning Number of Weeks
define("VERSIONS_TIME_WEEKS", -1);

//Versioning Number of Years
define("VERSIONS_TIME_YEARS", -1);

//Versioning Number of Versions
define("VERSIONS_ANZAHL", "");

//Versioning Save version only if publishing
define("VERSIONS_CREATE", 0);

//Versioning Save template version only on special request
define("VERSIONS_CREATE_TMPL", 1);

//Versioning Number of Days
define("VERSIONS_TIME_DAYS_TMPL", -1);

//Versioning Number of Weeks
define("VERSIONS_TIME_WEEKS_TMPL", -1);

//Versioning Number of Years
define("VERSIONS_TIME_YEARS_TMPL", -1);

//Versioning Number of Versions
define("VERSIONS_ANZAHL_TMPL", "5");

// Flag if automatic LanguageLinks should be supported
define("LANGLINK_SUPPORT", 1);

// Flag if automatic backlinks should be generated
define("LANGLINK_SUPPORT_BACKLINKS", 1);


// top countries
define("WE_COUNTRIES_TOP", "DE,AT,CH");

// other shown countries
define("WE_COUNTRIES_SHOWN", "BE,DK,EE,FI,FR,GR,IE,IT,LT,LU,MT,NL,PL,PT,SE,SK,SI,ES,CZ,HU,GB,CY");

// Flag if NAV- rules should be evaluated even after a first match
define("NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH", 0);

// Flag if directoy-index files should be hidden in Nav-output
define("NAVIGATION_DIRECTORYINDEX_HIDE", 1);

// Comma seperated list such as index.php,index.html
define("NAVIGATION_DIRECTORYINDEX_NAMES", "index.php");

// Flag if directoy-index files should be hidden in Wysiwyg-editor output
define("WYSIWYGLINKS_DIRECTORYINDEX_HIDE", 1);

// Flag if directoy-index files should be hidden in tag output
define("TAGLINKS_DIRECTORYINDEX_HIDE", 1);

// Flag if we_objectID should be hidden from output of navigation
define("NAVIGATION_OBJECTSEOURLS", 1);

// Flag if we_objectID should be hidden from output of wysiwyg editior
define("WYSIWYGLINKS_OBJECTSEOURLS", 1);

// Flag if we_objectID should be hidden from output of tags
define("TAGLINKS_OBJECTSEOURLS", 1);

// Flag if seo-urls should be urlencoded
define("URLENCODE_OBJECTSEOURLS", 0);

// Flag if 404 not found should be suppressd
define("SUPPRESS404CODE", 1);

// Flag if should be displayed in webEdition
define("SEOINSIDE_HIDEINWEBEDITION", 0);

// Flag if should be displayed in Editmode
define("SEOINSIDE_HIDEINEDITMODE", 1);

?>
weConfGlobalDemoSaveCodeBoundary;
$replaceCode['we_conf_global_demo']['replace'][6380] = <<< weConfDemoSaveCodeBoundary
<?php

/**
 * webEdition CMS configuration file
 * NOTE: this file is regenerated, so any extra contents will be overwritten
 */

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

//Default Charset
define('DEFAULT_CHARSET', "UTF-8");
?>
weConfDemoSaveCodeBoundary;

// add version and uid
$replaceCode['we_version_demo']['path'][4900] = '/webEdition/we/include/we_version%s';
$replaceCode['we_version_demo']['replace'][4900] = '<?php
define("WE_VERSION", "%s");
define("WE_VERSION_SUPP", "%s");
define("WE_ZFVERSION","%s");
define("WE_SVNREV","%s");
define("WE_VERSION_SUPP_VERSION","%s");
define("WE_VERSION_BRANCH","%s");
define("WE_VERSION_NAME","%s");

?>';
$replaceCode['we_activeModules']['path'][6380] = '/webEdition/we/include/conf/we_active_integrated_modules.inc%s';
$replaceCode['we_activeModules']['replace'][6380] = '<?php
/**
 * webEdition CMS configuration file
 * NOTE: this file is regenerated, so any extra contents will be overwritten
 */

$GLOBALS[\'_we_active_integrated_modules\'] = array();';

$replaceCode['we_activeModules']['path'][LANGUAGELIMIT] = '/webEdition/we/include/conf/we_active_integrated_modules.inc%s';
$replaceCode['we_activeModules']['replace'][LANGUAGELIMIT] = '<?php
$_we_active_integrated_modules = array();

$_we_active_integrated_modules[] = "users";
$_we_active_integrated_modules[] = "customer";
$_we_active_integrated_modules[] = "schedule";
$_we_active_integrated_modules[] = "shop";
$_we_active_integrated_modules[] = "editor";
$_we_active_integrated_modules[] = "object";
$_we_active_integrated_modules[] = "messaging";
$_we_active_integrated_modules[] = "workflow";
$_we_active_integrated_modules[] = "newsletter";
$_we_active_integrated_modules[] = "banner";
$_we_active_integrated_modules[] = "export";
$_we_active_integrated_modules[] = "voting";
$_we_active_integrated_modules[] = "spellchecker";
$_we_active_integrated_modules[] = "glossary";

?>';


// Proxysettings
$replaceCode['we_proxysettings']['path'][4900] = '/webEdition/liveUpdate/includes/proxysettings.inc%s';
$replaceCode['we_proxysettings']['replace'][4900] = '<?php
	define("WE_PROXYHOST", "%s");
	define("WE_PROXYPORT", "%s");
	define("WE_PROXYUSER", "%s");
	define("WE_PROXYPASSWORD", "%s");
?>';

// enable demo pop-up webEdition.php
$replaceCode['webEdition_demo']['path'][4900] = '/webEdition/webEdition%s';
$replaceCode['webEdition_demo']['needle'][4900] = 'var we_demo = false;';
$replaceCode['webEdition_demo']['replace'][4900] = 'var we_demo = true;';

// template savecode
$replaceCode['templateSaveCode_demo']['path'][4900] = '/webEdition/we/include/we_editors/we_editor.inc%s';
$replaceCode['templateSaveCode_demo']['needle'][4900] = '####TEMPLATE_SAVE_CODE2_START###.*####TEMPLATE_SAVE_CODE2_END###'; // ! IMPORTANT
$replaceCode['templateSaveCode_demo']['replace'][4900] = '#save template2';

// insert tblPrefs
$replaceCode['insert_tblPrefs']['path'][4900] = '';

$replaceCode['insert_tblPrefs']['replace'][4900] = 'UPDATE %s'.'tblPrefs set Language = \'%s\' where userID = \'1\'';
$replaceCode['insert_tblPrefs']['replace'][LANGUAGELIMIT] = 'UPDATE %s'.'tblPrefs set Language = \'%s\',BackendCharset = \'%s\'  where userID = \'1\'';


// insert tblUser
$replaceCode['insert_tblUser']['path'][4900] = '';
$replaceCode['insert_tblUser']['needle'][4900]='';
$replaceCode['insert_tblUser']['replace'][4900] = 'UPDATE %s'.'tblUser set Text=\'%s\', username=\'%s\', passwd=MD5(\'%s\'), UseSalt=0 where ID=1';

$replaceCode['insert_tblUser']['path'][6490] = '/webEdition/we/include/we_modules/users/we_users_user.class.php';
$replaceCode['insert_tblUser']['needle'][6490]='$_SESSION["le_login_pass"]=we_users_user::makeSaltedPassword($_SESSION["le_login_pass"]);';
$replaceCode['insert_tblUser']['replace'][6490] = 'UPDATE %s'.'tblUser set Text=\'%s\', username=\'%s\', passwd=\'%s\', UseSalt=2 where ID=1';
