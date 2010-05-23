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

if (file_exists($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/conf/we_error_conf.inc.php")) {
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/conf/we_error_conf.inc.php");
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
define("WE_ERROR_MAIL_ADDRESS", "mail@example.com");


// Number of entries per batch
define("BACKUP_STEPS", 80);


// Directory in which to save thumbnails
define("WE_THUMBNAIL_DIRECTORY", "/__we_thumbs__");



// Default setting for inlineedit attribute
define("INLINEEDIT_DEFAULT", 1);


// Default setting for xml attribute
define("XHTML_DEFAULT", 0);


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
define("WE_TRACKER_DIR", "/pageLogger");


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
define("DEFAULT_CHARSET","UTF-8");

// Default setting for hook execution
define("EXECUTE_HOOKS", 0);

// connection charset to db wichtig: klein Leerzeichen nach/vor Komma im define
define("DB_SET_CHARSET","utf8");



//Versioning status for ContentType image/* 
define("VERSIONING_IMAGE", 0);

//Versioning status for ContentType text/html 
define("VERSIONING_TEXT_HTML", 0);

//Versioning status for ContentType text/webedition 
define("VERSIONING_TEXT_WEBEDITION", 0);

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

?>