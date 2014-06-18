<?php

/**
 * Part2: make demo-version after online-installation
 * @see /we4/includes/replaceCode/replaceCode.inc.php
 */
// add licensee to we_conf.inc.php
$replaceCode['we_conf_demo']['path']['4900'] = '/webEdition/we/include/conf/we_conf.inc%s';
$replaceCode['we_conf_demo']['replace']['4900'] = <<< weConfDemoSaveCodeBoundary
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

$replaceCode['we_conf_global_demo']['path']['4900'] = '/webEdition/we/include/conf/we_conf_global.inc%s';
$replaceCode['we_conf_global_demo']['replace']['4900'] = <<< weConfGlobalDemoSaveCodeBoundary
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

// add version and uid
$replaceCode['we_version_demo']['path']['4900'] = '/webEdition/we/include/we_version%s';
$replaceCode['we_version_demo']['replace']['4900'] = '<?php
define("WE_VERSION", "%s");
define("WE_VERSION_SUPP", "%s");
define("WE_ZFVERSION","%s");
define("WE_SVNREV","%s");
define("WE_VERSION_SUPP_VERSION","%s");
define("WE_VERSION_BRANCH","%s");
define("WE_VERSION_NAME","%s");

?>';
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
$replaceCode['we_proxysettings']['path']['4900'] = '/webEdition/liveUpdate/includes/proxysettings.inc%s';
$replaceCode['we_proxysettings']['replace']['4900'] = '<?php
	define("WE_PROXYHOST", "%s");
	define("WE_PROXYPORT", "%s");
	define("WE_PROXYUSER", "%s");
	define("WE_PROXYPASSWORD", "%s");
?>';

// enable demo pop-up webEdition.php
$replaceCode['webEdition_demo']['path']['4900'] = '/webEdition/webEdition%s';
$replaceCode['webEdition_demo']['needle']['4900'] = 'var we_demo = false;';
$replaceCode['webEdition_demo']['replace']['4900'] = 'var we_demo = true;';

// change menu entries
$replaceCode['menu1_demo']['path']['4900'] = '/webEdition/we/include/java_menu/we_menu.inc%s';
$replaceCode['menu1_demo']['needle']['4900'] = '\$we_menu\["5050000"\]\["text"\] = \$l_javaMenu\["update"\]';
$replaceCode['menu1_demo']['replace']['4900'] = '$we_menu["5050000"]["text"] = $l_javaMenu["register"]';

$replaceCode['menu2_demo']['path']['4900'] = '/webEdition/we/include/java_menu/we_menu.inc%s';
$replaceCode['menu2_demo']['needle']['4900'] = '\$we_menu\["3060000"\]\["text"\] = \$l_javaMenu\["module_installation"\]';
$replaceCode['menu2_demo']['replace']['4900'] = '$we_menu["3060000"]["text"] = $l_javaMenu["register"]';

// template savecode
$replaceCode['templateSaveCode_demo']['path']['4900'] = '/webEdition/we/include/we_editors/we_editor.inc%s';
$replaceCode['templateSaveCode_demo']['needle']['4900'] = '####TEMPLATE_SAVE_CODE2_START###.*####TEMPLATE_SAVE_CODE2_END###'; // ! IMPORTANT
$replaceCode['templateSaveCode_demo']['replace']['4900'] = '#save template2';

// insert tblPrefs
$replaceCode['insert_tblPrefs']['path']['4900'] = '';

// new with webedition feed and deleted feeds:
//$replaceCode['insert_tblPrefs']['replace']['4900'] = 'INSERT INTO %s'.'tblPrefs VALUES (1,0,\'1\',\'\',0,\'.html\',\'.php\',\'.html\',0,0,0,0,0,0,\'%s\',\'\',\'\',0,0,\'cockpit\',1,900,700,0,0,\'none\',-1,0,20,0,0,0,0,\'\',\'\',5,1,\'%s\',\'a:3:{i:0;a:2:{i:0;a:4:{i:0;s:3:\"pad\";i:1;s:4:\"blue\";i:2;i:1;i:3;s:18:\"U29uc3RpZ2Vz,30020\";}i:1;a:4:{i:0;s:3:\"mfd\";i:1;s:5:\"green\";i:2;i:1;i:3;s:12:\"1111;0;5;00;\";}}i:1;a:2:{i:0;a:4:{i:0;s:3:\"rss\";i:1;s:6:\"yellow\";i:2;i:1;i:3;s:98:\"aHR0cDovL3d3dy53ZWJlZGl0aW9uLmRlL2RlL1ByZXNzZS9QcmVzc2VtZWxkdW5nZW4vcnNzMi54bWw=,111000,0,110000,1\";}i:1;a:4:{i:0;s:3:\"sct\";i:1;s:3:\"red\";i:2;i:1;i:3;s:124:\"open_document,new_document,new_template,new_directory,unpublished_pages;unpublished_objects,new_object,new_class,preferences\";}}i:2;a:15:{i:0;a:2:{i:0;s:20:\"d2ViRWRpdGlvbiBOZXdz\";i:1;s:80:\"aHR0cDovL3d3dy53ZWJlZGl0aW9uLmRlL2RlL1ByZXNzZS9QcmVzc2VtZWxkdW5nZW4vcnNzMi54bWw=\";}i:1;a:2:{i:0;s:16:\"Rk9DVVMtT25saW5l\";i:1;s:60:\"aHR0cDovL2ZvY3VzLm1zbi5kZS9mb2wvWE1ML3Jzc19mb2xuZXdzLnhtbA==\";}i:2;a:2:{i:0;s:12:\"U2xhc2hkb3Q=\";i:1;s:56:\"aHR0cDovL3Jzcy5zbGFzaGRvdC5vcmcvU2xhc2hkb3Qvc2xhc2hkb3Q=\";}i:3;a:2:{i:0;s:24:\"aGVpc2Ugb25saW5lIE5ld3M=\";i:1;s:56:\"aHR0cDovL3d3dy5oZWlzZS5kZS9uZXdzdGlja2VyL2hlaXNlLnJkZg==\";}i:4;a:2:{i:0;s:20:\"dGFnZXNzY2hhdS5kZQ==\";i:1;s:68:\"aHR0cDovL3d3dy50YWdlc3NjaGF1LmRlL3htbC90YWdlc3NjaGF1LW1lbGR1bmdlbi8=\";}i:8;a:2:{i:0;s:12:\"RkFaLk5FVA==\";i:1;s:64:\"aHR0cDovL3d3dy5mYXoubmV0L3MvUnViL1RwbH5FcGFydG5lcn5TUnNzXy54bWw=\";}i:9;a:2:{i:0;s:20:\"RmlsbXN0YXJ0cy5kZQ==\";i:1;s:60:\"aHR0cDovL3d3dy5maWxtc3RhcnRzLmRlL3htbC9maWxtc3RhcnRzLnhtbA==\";}i:10;a:2:{i:0;s:20:\"TkVUWkVJVFVORy5ERQ==\";i:1;s:76:\"aHR0cDovL3d3dy5uZXR6ZWl0dW5nLmRlL2V4cG9ydC9uZXdzL3Jzcy90aXRlbHNlaXRlLnhtbA==\";}i:11;a:2:{i:0;s:28:\"aHR0cDovL3d3dy5zcGllZ2VsLmRl\";i:1;s:52:\"aHR0cDovL3d3dy5zcGllZ2VsLmRlL3NjaGxhZ3plaWxlbi9yc3Mv\";}i:12;a:2:{i:0;s:8:\"R0VPLmRl\";i:1;s:48:\"aHR0cDovL3d3dy5nZW8uZGUvcnNzL0dFTy9pbmRleC54bWw=\";}i:13;a:2:{i:0;s:44:\"MTAwMGUgU3By/GNoZSAoU3BydWNoIGRlcyBUYWdlcyk=\";i:1;s:96:\"aHR0cDovL3d3dy5ob21lcGFnZXNlcnZpY2Uudm9zc3dlYi5pbmZvL2F1c3dhaGwvc3BydWNoL3Jzcy9oZXV0ZS9yc3MueG1s\";}i:14;a:2:{i:0;s:32:\"QnVuZGVzcmVnaWVydW5nIEFrdHVlbGw=\";i:1;s:56:\"aHR0cDovL3d3dy5idW5kZXNyZWdpZXJ1bmcuZGUvYWt0dWVsbC5yc3M=\";}i:15;a:2:{i:0;s:20:\"QW53YWx0cy1UaXBwcw==\";i:1;s:60:\"aHR0cDovL3d3dy5hbndhbHRzc3VjaGRpZW5zdC5kZS9yc3MvcnNzLnhtbA==\";}i:18;a:2:{i:0;s:12:\"Q0hJUC5ERQ==\";i:1;s:44:\"aHR0cDovL3d3dy5jaGlwLmRlL3Jzc19uZXdzLnhtbA==\";}i:19;a:2:{i:0;s:12:\"U3Rlcm4uZGU=\";i:1;s:64:\"aHR0cDovL3d3dy5zdGVybi5kZS9zdGFuZGFyZC9yc3MucGhwP2NoYW5uZWw9YWxs\";}}}\',3,7,0,0)';
//$replaceCode['insert_tblPrefs']['replace']['4900'] = 'INSERT INTO %s'.'tblPrefs VALUES (1,0,\'1\',\'\',0,\'.html\',\'.php\',\'.html\',0,0,0,0,0,0,\'%s\',\'\',\'\',0,0,\'cockpit\',1,900,700,0,0,\'none\',-1,0,20,0,0,0,0,\'\',\'\',5,1,\'%s\',\'a:3:{i:0;a:2:{i:0;a:4:{i:0;s:3:\"pad\";i:1;s:4:\"blue\";i:2;i:1;i:3;s:18:\"U29uc3RpZ2Vz,30020\";}i:1;a:4:{i:0;s:3:\"mfd\";i:1;s:5:\"green\";i:2;i:1;i:3;s:12:\"1111;0;5;00;\";}}i:1;a:2:{i:0;a:4:{i:0;s:3:\"rss\";i:1;s:6:\"yellow\";i:2;i:1;i:3;s:98:\"aHR0cDovL3d3dy53ZWJlZGl0aW9uLmRlL2RlL1ByZXNzZS9QcmVzc2VtZWxkdW5nZW4vcnNzMi54bWw=,111000,0,110000,1\";}i:1;a:4:{i:0;s:3:\"sct\";i:1;s:3:\"red\";i:2;i:1;i:3;s:124:\"open_document,new_document,new_template,new_directory,unpublished_pages;unpublished_objects,new_object,new_class,preferences\";}}i:2;a:15:{i:0;a:2:{i:0;s:20:\"d2ViRWRpdGlvbiBOZXdz\";i:1;s:80:\"aHR0cDovL3d3dy53ZWJlZGl0aW9uLmRlL2RlL1ByZXNzZS9QcmVzc2VtZWxkdW5nZW4vcnNzMi54bWw=\";}i:1;a:2:{i:0;s:16:\"Rk9DVVMtT25saW5l\";i:1;s:60:\"aHR0cDovL2ZvY3VzLm1zbi5kZS9mb2wvWE1ML3Jzc19mb2xuZXdzLnhtbA==\";}i:2;a:2:{i:0;s:12:\"U2xhc2hkb3Q=\";i:1;s:56:\"aHR0cDovL3Jzcy5zbGFzaGRvdC5vcmcvU2xhc2hkb3Qvc2xhc2hkb3Q=\";}i:3;a:2:{i:0;s:24:\"aGVpc2Ugb25saW5lIE5ld3M=\";i:1;s:56:\"aHR0cDovL3d3dy5oZWlzZS5kZS9uZXdzdGlja2VyL2hlaXNlLnJkZg==\";}i:4;a:2:{i:0;s:20:\"dGFnZXNzY2hhdS5kZQ==\";i:1;s:68:\"aHR0cDovL3d3dy50YWdlc3NjaGF1LmRlL3htbC90YWdlc3NjaGF1LW1lbGR1bmdlbi8=\";}i:8;a:2:{i:0;s:12:\"RkFaLk5FVA==\";i:1;s:64:\"aHR0cDovL3d3dy5mYXoubmV0L3MvUnViL1RwbH5FcGFydG5lcn5TUnNzXy54bWw=\";}i:9;a:2:{i:0;s:20:\"RmlsbXN0YXJ0cy5kZQ==\";i:1;s:60:\"aHR0cDovL3d3dy5maWxtc3RhcnRzLmRlL3htbC9maWxtc3RhcnRzLnhtbA==\";}i:10;a:2:{i:0;s:20:\"TkVUWkVJVFVORy5ERQ==\";i:1;s:76:\"aHR0cDovL3d3dy5uZXR6ZWl0dW5nLmRlL2V4cG9ydC9uZXdzL3Jzcy90aXRlbHNlaXRlLnhtbA==\";}i:11;a:2:{i:0;s:28:\"aHR0cDovL3d3dy5zcGllZ2VsLmRl\";i:1;s:52:\"aHR0cDovL3d3dy5zcGllZ2VsLmRlL3NjaGxhZ3plaWxlbi9yc3Mv\";}i:12;a:2:{i:0;s:8:\"R0VPLmRl\";i:1;s:48:\"aHR0cDovL3d3dy5nZW8uZGUvcnNzL0dFTy9pbmRleC54bWw=\";}i:13;a:2:{i:0;s:44:\"MTAwMGUgU3By/GNoZSAoU3BydWNoIGRlcyBUYWdlcyk=\";i:1;s:96:\"aHR0cDovL3d3dy5ob21lcGFnZXNlcnZpY2Uudm9zc3dlYi5pbmZvL2F1c3dhaGwvc3BydWNoL3Jzcy9oZXV0ZS9yc3MueG1s\";}i:14;a:2:{i:0;s:32:\"QnVuZGVzcmVnaWVydW5nIEFrdHVlbGw=\";i:1;s:56:\"aHR0cDovL3d3dy5idW5kZXNyZWdpZXJ1bmcuZGUvYWt0dWVsbC5yc3M=\";}i:15;a:2:{i:0;s:20:\"QW53YWx0cy1UaXBwcw==\";i:1;s:60:\"aHR0cDovL3d3dy5hbndhbHRzc3VjaGRpZW5zdC5kZS9yc3MvcnNzLnhtbA==\";}i:18;a:2:{i:0;s:12:\"Q0hJUC5ERQ==\";i:1;s:44:\"aHR0cDovL3d3dy5jaGlwLmRlL3Jzc19uZXdzLnhtbA==\";}i:19;a:2:{i:0;s:12:\"U3Rlcm4uZGU=\";i:1;s:64:\"aHR0cDovL3d3dy5zdGVybi5kZS9zdGFuZGFyZC9yc3MucGhwP2NoYW5uZWw9YWxs\";}}}\',3,7,0,0,\'\',\'\',\'\',\'\',\'\',\'\',\'\',1,0)';

$replaceCode['insert_tblPrefs']['replace']['4900'] = 'UPDATE %s'.'tblPrefs set Language = \'%s\' where userID = \'1\'';
$replaceCode['insert_tblPrefs']['replace'][LANGUAGELIMIT] = 'UPDATE %s'.'tblPrefs set Language = \'%s\',BackendCharset = \'%s\'  where userID = \'1\'';

// old with webedition feed:
//$replaceCode['insert_tblPrefs']['replace']['4900'] = 'INSERT INTO %s'.'tblPrefs VALUES (1,0,\'1\',\'\',0,\'.html\',\'.php\',\'.html\',0,0,0,0,0,0,\'%s\',\'\',\'\',0,0,\'cockpit\',1,900,700,0,0,\'none\',-1,0,20,0,0,0,0,\'\',\'\',5,1,\'%s\',\'a:3:{i:0;a:2:{i:0;a:4:{i:0;s:3:\"pad\";i:1;s:4:\"blue\";i:2;i:1;i:3;s:18:\"U29uc3RpZ2Vz,30020\";}i:1;a:4:{i:0;s:3:\"mfd\";i:1;s:5:\"green\";i:2;i:1;i:3;s:12:\"1111;0;5;00;\";}}i:1;a:2:{i:0;a:4:{i:0;s:3:\"rss\";i:1;s:6:\"yellow\";i:2;i:1;i:3;s:98:\"aHR0cDovL3d3dy53ZWJlZGl0aW9uLmRlL2RlL1ByZXNzZS9QcmVzc2VtZWxkdW5nZW4vcnNzMi54bWw=,111000,0,110000,1\";}i:1;a:4:{i:0;s:3:\"sct\";i:1;s:3:\"red\";i:2;i:1;i:3;s:124:\"open_document,new_document,new_template,new_directory,unpublished_pages;unpublished_objects,new_object,new_class,preferences\";}}i:2;a:20:{i:0;a:2:{i:0;s:20:\"d2ViRWRpdGlvbiBOZXdz\";i:1;s:80:\"aHR0cDovL3d3dy53ZWJlZGl0aW9uLmRlL2RlL1ByZXNzZS9QcmVzc2VtZWxkdW5nZW4vcnNzMi54bWw=\";}i:1;a:2:{i:0;s:16:\"Rk9DVVMtT25saW5l\";i:1;s:60:\"aHR0cDovL2ZvY3VzLm1zbi5kZS9mb2wvWE1ML3Jzc19mb2xuZXdzLnhtbA==\";}i:2;a:2:{i:0;s:12:\"U2xhc2hkb3Q=\";i:1;s:56:\"aHR0cDovL3Jzcy5zbGFzaGRvdC5vcmcvU2xhc2hkb3Qvc2xhc2hkb3Q=\";}i:3;a:2:{i:0;s:24:\"aGVpc2Ugb25saW5lIE5ld3M=\";i:1;s:56:\"aHR0cDovL3d3dy5oZWlzZS5kZS9uZXdzdGlja2VyL2hlaXNlLnJkZg==\";}i:4;a:2:{i:0;s:20:\"dGFnZXNzY2hhdS5kZQ==\";i:1;s:68:\"aHR0cDovL3d3dy50YWdlc3NjaGF1LmRlL3htbC90YWdlc3NjaGF1LW1lbGR1bmdlbi8=\";}i:5;a:2:{i:0;s:12:\"U0FUVklTSU9O\";i:1;s:52:\"aHR0cDovL3d3dy5zYXR2aXNpb24ub3JnL25ld3MvcnNzLnhtbA==\";}i:6;a:2:{i:0;s:20:\"QmFzZWwtSUkuaW5mbw==\";i:1;s:52:\"aHR0cDovL3d3dy5iYXNlbC1paS5pbmZvL0Jhc2VsLUlJLnBocA==\";}i:7;a:2:{i:0;s:52:\"LrAuTGlxdWlkIE1vdGlvbiBXZWItICYgR3JhZmlrZGVzaWdusC6w\";i:1;s:52:\"aHR0cDovL3d3dy5saXF1aWQtbW90aW9uLmRlL3Jzcy9yc3MueG1s\";}i:8;a:2:{i:0;s:12:\"RkFaLk5FVA==\";i:1;s:64:\"aHR0cDovL3d3dy5mYXoubmV0L3MvUnViL1RwbH5FcGFydG5lcn5TUnNzXy54bWw=\";}i:9;a:2:{i:0;s:20:\"RmlsbXN0YXJ0cy5kZQ==\";i:1;s:60:\"aHR0cDovL3d3dy5maWxtc3RhcnRzLmRlL3htbC9maWxtc3RhcnRzLnhtbA==\";}i:10;a:2:{i:0;s:20:\"TkVUWkVJVFVORy5ERQ==\";i:1;s:76:\"aHR0cDovL3d3dy5uZXR6ZWl0dW5nLmRlL2V4cG9ydC9uZXdzL3Jzcy90aXRlbHNlaXRlLnhtbA==\";}i:11;a:2:{i:0;s:28:\"aHR0cDovL3d3dy5zcGllZ2VsLmRl\";i:1;s:52:\"aHR0cDovL3d3dy5zcGllZ2VsLmRlL3NjaGxhZ3plaWxlbi9yc3Mv\";}i:12;a:2:{i:0;s:8:\"R0VPLmRl\";i:1;s:48:\"aHR0cDovL3d3dy5nZW8uZGUvcnNzL0dFTy9pbmRleC54bWw=\";}i:13;a:2:{i:0;s:44:\"MTAwMGUgU3By/GNoZSAoU3BydWNoIGRlcyBUYWdlcyk=\";i:1;s:96:\"aHR0cDovL3d3dy5ob21lcGFnZXNlcnZpY2Uudm9zc3dlYi5pbmZvL2F1c3dhaGwvc3BydWNoL3Jzcy9oZXV0ZS9yc3MueG1s\";}i:14;a:2:{i:0;s:32:\"QnVuZGVzcmVnaWVydW5nIEFrdHVlbGw=\";i:1;s:56:\"aHR0cDovL3d3dy5idW5kZXNyZWdpZXJ1bmcuZGUvYWt0dWVsbC5yc3M=\";}i:15;a:2:{i:0;s:20:\"QW53YWx0cy1UaXBwcw==\";i:1;s:60:\"aHR0cDovL3d3dy5hbndhbHRzc3VjaGRpZW5zdC5kZS9yc3MvcnNzLnhtbA==\";}i:16;a:2:{i:0;s:56:\"UHJvbW9NYXN0ZXJzIEludGVybmV0IE1hcmtldGluZyBSU1MgQmxvZw==\";i:1;s:56:\"aHR0cDovL3d3dy5wcm9tb21hc3RlcnMuYXQvcnNzL2luZGV4LnhtbA==\";}i:17;a:2:{i:0;s:20:\"U1dSMyBSREYtRmVlZA==\";i:1;s:40:\"aHR0cDovL3d3dy5zd3IzLmRlL3JkZi1mZWVkLw==\";}i:18;a:2:{i:0;s:12:\"Q0hJUC5ERQ==\";i:1;s:44:\"aHR0cDovL3d3dy5jaGlwLmRlL3Jzc19uZXdzLnhtbA==\";}i:19;a:2:{i:0;s:12:\"U3Rlcm4uZGU=\";i:1;s:64:\"aHR0cDovL3d3dy5zdGVybi5kZS9zdGFuZGFyZC9yc3MucGhwP2NoYW5uZWw9YWxs\";}}}\',3,7,0,0)';
// old with living-e feed: 
//$replaceCode['insert_tblPrefs']['replace']['4900'] = 'INSERT INTO %s'.'tblPrefs VALUES (1,0,\'1\',\'\',0,\'.html\',\'.php\',\'.html\',0,0,0,0,0,0,\'%s\',\'\',\'\',0,0,\'cockpit\',1,900,700,0,0,\'none\',-1,0,20,0,0,0,0,\'\',\'\',5,1,\'%s\',\'a:3:{i:0;a:2:{i:0;a:4:{i:0;s:3:\"pad\";i:1;s:4:\"blue\";i:2;i:1;i:3;s:18:\"U29uc3RpZ2Vz,30020\";}i:1;a:4:{i:0;s:3:\"mfd\";i:1;s:5:\"green\";i:2;i:1;i:3;s:12:\"1111;0;5;00;\";}}i:1;a:2:{i:0;a:4:{i:0;s:3:\"rss\";i:1;s:6:\"yellow\";i:2;i:1;i:3;s:106:\"aHR0cDovL3d3dy5saXZpbmctZS5kZS9kZS9wcmVzc2V6ZW50cnVtL3ByLW1pdHRlaWx1bmdlbi9yc3MyLnhtbA==,111000,0,110000,1\";}i:1;a:4:{i:0;s:3:\"sct\";i:1;s:3:\"red\";i:2;i:1;i:3;s:124:\"open_document,new_document,new_template,new_directory,unpublished_pages;unpublished_objects,new_object,new_class,preferences\";}}i:2;a:20:{i:0;a:2:{i:0;s:16:\"bGl2aW5nLWUgQUc=\";i:1;s:88:\"aHR0cDovL3d3dy5saXZpbmctZS5kZS9kZS9wcmVzc2V6ZW50cnVtL3ByLW1pdHRlaWx1bmdlbi9yc3MyLnhtbA==\";}i:1;a:2:{i:0;s:16:\"Rk9DVVMtT25saW5l\";i:1;s:60:\"aHR0cDovL2ZvY3VzLm1zbi5kZS9mb2wvWE1ML3Jzc19mb2xuZXdzLnhtbA==\";}i:2;a:2:{i:0;s:12:\"U2xhc2hkb3Q=\";i:1;s:56:\"aHR0cDovL3Jzcy5zbGFzaGRvdC5vcmcvU2xhc2hkb3Qvc2xhc2hkb3Q=\";}i:3;a:2:{i:0;s:24:\"aGVpc2Ugb25saW5lIE5ld3M=\";i:1;s:56:\"aHR0cDovL3d3dy5oZWlzZS5kZS9uZXdzdGlja2VyL2hlaXNlLnJkZg==\";}i:4;a:2:{i:0;s:20:\"dGFnZXNzY2hhdS5kZQ==\";i:1;s:68:\"aHR0cDovL3d3dy50YWdlc3NjaGF1LmRlL3htbC90YWdlc3NjaGF1LW1lbGR1bmdlbi8=\";}i:5;a:2:{i:0;s:12:\"U0FUVklTSU9O\";i:1;s:52:\"aHR0cDovL3d3dy5zYXR2aXNpb24ub3JnL25ld3MvcnNzLnhtbA==\";}i:6;a:2:{i:0;s:20:\"QmFzZWwtSUkuaW5mbw==\";i:1;s:52:\"aHR0cDovL3d3dy5iYXNlbC1paS5pbmZvL0Jhc2VsLUlJLnBocA==\";}i:7;a:2:{i:0;s:52:\"LrAuTGlxdWlkIE1vdGlvbiBXZWItICYgR3JhZmlrZGVzaWdusC6w\";i:1;s:52:\"aHR0cDovL3d3dy5saXF1aWQtbW90aW9uLmRlL3Jzcy9yc3MueG1s\";}i:8;a:2:{i:0;s:12:\"RkFaLk5FVA==\";i:1;s:64:\"aHR0cDovL3d3dy5mYXoubmV0L3MvUnViL1RwbH5FcGFydG5lcn5TUnNzXy54bWw=\";}i:9;a:2:{i:0;s:20:\"RmlsbXN0YXJ0cy5kZQ==\";i:1;s:60:\"aHR0cDovL3d3dy5maWxtc3RhcnRzLmRlL3htbC9maWxtc3RhcnRzLnhtbA==\";}i:10;a:2:{i:0;s:20:\"TkVUWkVJVFVORy5ERQ==\";i:1;s:76:\"aHR0cDovL3d3dy5uZXR6ZWl0dW5nLmRlL2V4cG9ydC9uZXdzL3Jzcy90aXRlbHNlaXRlLnhtbA==\";}i:11;a:2:{i:0;s:28:\"aHR0cDovL3d3dy5zcGllZ2VsLmRl\";i:1;s:52:\"aHR0cDovL3d3dy5zcGllZ2VsLmRlL3NjaGxhZ3plaWxlbi9yc3Mv\";}i:12;a:2:{i:0;s:8:\"R0VPLmRl\";i:1;s:48:\"aHR0cDovL3d3dy5nZW8uZGUvcnNzL0dFTy9pbmRleC54bWw=\";}i:13;a:2:{i:0;s:44:\"MTAwMGUgU3By/GNoZSAoU3BydWNoIGRlcyBUYWdlcyk=\";i:1;s:96:\"aHR0cDovL3d3dy5ob21lcGFnZXNlcnZpY2Uudm9zc3dlYi5pbmZvL2F1c3dhaGwvc3BydWNoL3Jzcy9oZXV0ZS9yc3MueG1s\";}i:14;a:2:{i:0;s:32:\"QnVuZGVzcmVnaWVydW5nIEFrdHVlbGw=\";i:1;s:56:\"aHR0cDovL3d3dy5idW5kZXNyZWdpZXJ1bmcuZGUvYWt0dWVsbC5yc3M=\";}i:15;a:2:{i:0;s:20:\"QW53YWx0cy1UaXBwcw==\";i:1;s:60:\"aHR0cDovL3d3dy5hbndhbHRzc3VjaGRpZW5zdC5kZS9yc3MvcnNzLnhtbA==\";}i:16;a:2:{i:0;s:56:\"UHJvbW9NYXN0ZXJzIEludGVybmV0IE1hcmtldGluZyBSU1MgQmxvZw==\";i:1;s:56:\"aHR0cDovL3d3dy5wcm9tb21hc3RlcnMuYXQvcnNzL2luZGV4LnhtbA==\";}i:17;a:2:{i:0;s:20:\"U1dSMyBSREYtRmVlZA==\";i:1;s:40:\"aHR0cDovL3d3dy5zd3IzLmRlL3JkZi1mZWVkLw==\";}i:18;a:2:{i:0;s:12:\"Q0hJUC5ERQ==\";i:1;s:44:\"aHR0cDovL3d3dy5jaGlwLmRlL3Jzc19uZXdzLnhtbA==\";}i:19;a:2:{i:0;s:12:\"U3Rlcm4uZGU=\";i:1;s:64:\"aHR0cDovL3d3dy5zdGVybi5kZS9zdGFuZGFyZC9yc3MucGhwP2NoYW5uZWw9YWxs\";}}}\',3,7,0,0)';

// insert tblUser
$replaceCode['insert_tblUser']['path']['4900'] = '';
//$replaceCode['insert_tblUser']['replace']['4900'] = 'INSERT INTO %s'.'tblUser (ID, Text, Path, Icon, First, username, passwd, Permissions, CreatorID) VALUES (1, \'Administrator\', \'/Administrator\', \'user.gif\', \'Administrator\', \'%s\', MD5(\'%s\'), \'a:1:{s:13:\"ADMINISTRATOR\";i:1;}\', 1)';
$replaceCode['insert_tblUser']['replace']['4900'] = 'UPDATE %s'.'tblUser set Text = \'%s\', username = \'%s\', passwd = MD5(\'%s\'), UseSalt = \'0\' where ID = \'1\';';
?>