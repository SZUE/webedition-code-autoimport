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
//NOTE: there is no need to add any variables to we_conf_global.inc.php.default anymore.
$GLOBALS['tabs'] = array(
	'ui' => '',
	'extensions' => 'EDIT_SETTINGS_DEF_EXT',
	'editor' => '',
	'proxy' => 'ADMINISTRATOR',
	'defaultAttribs' => 'ADMINISTRATOR',
	'advanced' => 'ADMINISTRATOR',
	'system' => 'ADMINISTRATOR',
	'security' => 'ADMINISTRATOR',
	'seolinks' => 'ADMINISTRATOR',
	'modules' => 'ADMINISTRATOR',
	'language' => 'ADMINISTRATOR',
	'countries' => 'ADMINISTRATOR',
	'error_handling' => 'ADMINISTRATOR',
//	'backup' => 'ADMINISTRATOR',
	'validation' => 'ADMINISTRATOR',
	'email' => 'ADMINISTRATOR',
	'message_reporting' => '',
	'recipients' => 'FORMMAIL',
	'versions' => 'ADMINISTRATOR',
);

$GLOBALS['configs'] = array(
// Create array for needed configuration variables
	'global' => array(
//key => comment, default, changed right (default Admin)
// Variables for SEEM
		'WE_SEEM' => array('Enable seeMode', we_base_request::BOOL, 1),
// Variables for LogIn
		'WE_LOGIN_HIDEWESTATUS' => array('Hide if webEdition is Nightly or Alpha or.. Release Version', we_base_request::BOOL, 1),
		'WE_LOGIN_WEWINDOW' => array('Decide how WE opens: 0 allow both, 1 POPUP only, 2 same Window only', we_base_request::INT, 0),
// Variables for thumbnails
		'WE_THUMBNAIL_DIRECTORY' => array('Directory in which to save thumbnails', we_base_request::FILE, '/_thumbnails_'),
// Variables for error handling
		'WE_ERROR_HANDLER' => array('Show errors that occur in webEdition', we_base_request::BOOL, true),
		'WE_ERROR_NOTICES' => array('Handle notices', we_base_request::BOOL, false),
		'WE_ERROR_DEPRECATED' => array('Handle deprecated warnings', we_base_request::BOOL, true),
		'WE_ERROR_WARNINGS' => array('Handle warnings', we_base_request::BOOL, true),
		'WE_ERROR_ERRORS' => array('Handle errors', we_base_request::BOOL, true),
		'WE_ERROR_SHOW' => array('Show errors', we_base_request::BOOL, false),
		'WE_ERROR_LOG' => array('Log errors', we_base_request::BOOL, true),
		'WE_ERROR_MAIL' => array('Mail errors', we_base_request::BOOL, false),
		'WE_ERROR_MAIL_ADDRESS' => array('E-Mail address to which to mail errors', we_base_request::EMAIL, ''),
		'ERROR_DOCUMENT_NO_OBJECTFILE' => array('Document to open when trying to open non-existing object', we_base_request::INT, 0),
		'DISABLE_TEMPLATE_CODE_CHECK' => array('Disable the check for php-errors in templates', we_base_request::BOOL, false),
		'INLINEEDIT_DEFAULT' => array('Default setting for inlineedit attribute', we_base_request::BOOL, true),
		'IMAGESTARTID_DEFAULT' => array('Default setting for attribute imagestartdir in wetextarea', we_base_request::INT, 0),
		'WE_PHP_DEFAULT' => array('Default setting for php attribute', we_base_request::BOOL, false),
		'COMMANDS_DEFAULT' => array('Default setting for commands attribute', we_base_request::STRING, ''),
		'REMOVEFIRSTPARAGRAPH_DEFAULT' => array('Default setting for removeparagraph attribute', we_base_request::BOOL, false),
		'HIDENAMEATTRIBINWEIMG_DEFAULT' => array('Default setting for hide name attribute in weimg output', we_base_request::BOOL, true),
		'HIDENAMEATTRIBINWEFORM_DEFAULT' => array('Default setting for hide name attribute in weform output', we_base_request::BOOL, false),
		'REPLACEACRONYM' => array('Remove unsupported html5 acronym tag', we_base_request::BOOL, false),
// we_css
		'CSSAPPLYTO_DEFAULT' => array('Default setting for we:css attribute applyto', we_base_request::STRING, 'around'),
// hooks
		'EXECUTE_HOOKS' => array('Default setting for hook execution', we_base_request::BOOL, false),
// xhtml
		'XHTML_DEFAULT' => array('Default setting for xml attribute', we_base_request::BOOL, true),
		'XHTML_DEBUG' => array('Enable XHTML debug', we_base_request::BOOL, false),
		'XHTML_REMOVE_WRONG' => array('Remove wrong xhtml attributes from we:tags', we_base_request::BOOL, false),
//system
		'FILE_UPLOAD_MAX_UPLOAD_SIZE' => array('Set the maximum size a file can have', we_base_request::INT, 128),
		'WE_NEW_FOLDER_MOD' => array('File permissions when creating a new directory', we_base_request::INT, 755), //this should be string but deny access by user doesn't make sense
		'WE_DOCTYPE_WORKSPACE_BEHAVIOR' => array('Which Doctypes should be shown for which workspace', we_base_request::BOOL, false),
		'SCHEDULER_TRIGGER' => array('decide how the scheduler works', we_base_request::INT, 1), //postdoc
		'SYSTEM_WE_SESSION' => array('use webedition session handling', we_base_request::BOOL, false),
		'SYSTEM_WE_SESSION_TIME' => array('time after which the session is killed if not active anymore', we_base_request::INT, get_cfg_var('session.gc_maxlifetime')? : 1440),
		'SYSTEM_WE_SESSION_CRYPT' => array('crypt we session before save', we_base_request::INT, 2),
// accessibility
		'SHOWINPUTS_DEFAULT' => array('Default setting for showinputs attribute', we_base_request::BOOL, true),
		/* 		'WYSIWYG_TYPE' => array('define used wysiwyg editor', we_base_request::STRING, 'tinyMCE'),
		  'WYSIWYG_TYPE_FRONTEND' => array('define used wysiwyg editor in frontend', we_base_request::STRING, 'tinyMCE'), */
		'WE_MAILER' => array('mailer type; possible values are php and smtp', we_base_request::STRING, 'php'),
		'SMTP_SERVER' => array('SMTP_SERVER', we_base_request::STRING, 'localhost'),
		'SMTP_PORT' => array('SMTP server port', we_base_request::INT, 25),
		'SMTP_AUTH' => array('SMTP authentication', we_base_request::BOOL, false),
		'SMTP_USERNAME' => array('SMTP username', we_base_request::STRING, ''),
		'SMTP_PASSWORD' => array('SMTP password', we_base_request::RAW_CHECKED, ''),
		'SMTP_ENCRYPTION' => array('SMTP encryption', we_base_request::STRING, 0),
//formmail stuff
		'FORMMAIL_CONFIRM' => array('Flag if formmail confirm function should be work', we_base_request::BOOL, true), //this is restricted to admin
		'FORMMAIL_VIAWEDOC' => array('Flag if formmail should be send only via a webEdition document', we_base_request::BOOL, false, 'FORMMAIL'),
		'FORMMAIL_LOG' => array('Flag if formmail calls should be logged', we_base_request::BOOL, true, 'FORMMAIL'),
		'FORMMAIL_EMPTYLOG' => array('Time how long formmail calls should be logged', we_base_request::INT, 604800, 'FORMMAIL'),
		'FORMMAIL_BLOCK' => array('Flag if formmail calls should be blocked after a time', we_base_request::BOOL, true, 'FORMMAIL'),
		'FORMMAIL_SPAN' => array('Time span in seconds', we_base_request::INT, 300, 'FORMMAIL'),
		'FORMMAIL_TRIALS' => array('Num of trials sending formmail with same ip address in span', we_base_request::INT, 3, 'FORMMAIL'),
		'FORMMAIL_BLOCKTIME' => array('Time to block ip', we_base_request::INT, 86400, 'FORMMAIL'),
// sidebar stuff
		'SIDEBAR_DISABLED' => array('Sidebar is disabled', we_base_request::BOOL, true),
		'SIDEBAR_SHOW_ON_STARTUP' => array('Show Sidebar on startup', we_base_request::BOOL, true),
		'SIDEBAR_DEFAULT_DOCUMENT' => array('Default document id of the sidebar', we_base_request::INT, 0),
		'SIDEBAR_DEFAULT_WIDTH' => array('Default width of the sidebar', we_base_request::INT, 300),
// extension stuff
		'DEFAULT_STATIC_EXT' => array('Default static extension', we_base_request::STRING, ".html", 'EDIT_SETTINGS_DEF_EXT'),
		'DEFAULT_DYNAMIC_EXT' => array('Default dynamic extension', we_base_request::STRING, ".php", 'EDIT_SETTINGS_DEF_EXT'),
		'DEFAULT_HTML_EXT' => array('Default html extension', we_base_request::STRING, ".html", 'EDIT_SETTINGS_DEF_EXT'),
//naviagtion stuff
		'NAVIGATION_ENTRIES_FROM_DOCUMENT' => array('Flag if new NAV- entries added from Dokument should be items or folders', we_base_request::BOOL, false),
		'NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH' => array('Flag if NAV- rules should be evaluated even after a first match', we_base_request::BOOL, false),
//SEO stuff
		'NAVIGATION_DIRECTORYINDEX_HIDE' => array('Flag if directoy-index files should be hidden in Nav-output', we_base_request::BOOL, false),
		'NAVIGATION_DIRECTORYINDEX_NAMES' => array('Comma seperated list such as index.php,index.html', we_base_request::STRING, 'index.php,index.html'),
		'WYSIWYGLINKS_DIRECTORYINDEX_HIDE' => array('Flag if directoy-index files should be hidden in Wysiwyg-editor output', we_base_request::BOOL, false),
		'TAGLINKS_DIRECTORYINDEX_HIDE' => array('Flag if directoy-index files should be hidden in tag output', we_base_request::BOOL, false),
		'OBJECTSEOURLS_LOWERCASE' => array('Flag if object SEO-URLs save in lower or camelcase', we_base_request::BOOL, false),
		'NAVIGATION_OBJECTSEOURLS' => array('Flag if we_objectID should be hidden from output of navigation', we_base_request::BOOL, false),
		'WYSIWYGLINKS_OBJECTSEOURLS' => array('Flag if we_objectID should be hidden from output of wysiwyg editior', we_base_request::BOOL, false),
		'TAGLINKS_OBJECTSEOURLS' => array('Flag if we_objectID should be hidden from output of tags', we_base_request::BOOL, false),
		'URLENCODE_OBJECTSEOURLS' => array('Flag if seo-urls should be urlencoded', we_base_request::BOOL, false),
		'SUPPRESS404CODE' => array('Flag if 404 not found should be suppressd', we_base_request::BOOL, false),
		'FORCE404REDIRECT' => array('Flag if redirect to 404 instead of include', we_base_request::BOOL, false),
		'SEOINSIDE_HIDEINWEBEDITION' => array('Flag if should be displayed in webEdition ', we_base_request::BOOL, false),
		'SEOINSIDE_HIDEINEDITMODE' => array('Flag if should be displayed in Editmode ', we_base_request::BOOL, false),
//lang link stuff
		'LANGLINK_SUPPORT' => array('Flag if automatic LanguageLinks should be supported ', we_base_request::BOOL, true),
//default charset
		'DEFAULT_CHARSET' => array('Default Charset', we_base_request::STRING, 'UTF-8'),
//countries
		'WE_COUNTRIES_TOP' => array('top countries', we_base_request::STRING, 'DE,AT,CH'),
		'WE_COUNTRIES_SHOWN' => array('other shown countries', we_base_request::STRING, 'BE,DK,FI,FR,GR,IE,IT,LU,NL,PT,SE,ES,GB,EE,LT,MT,PL,SK,SI,CZ,HU,CY'),
		'WE_COUNTRIES_DEFAULT' => array('shown if no coutry was choosen', we_base_request::STRING, ''),
//versions
		'VERSIONING_IMAGE' => array('Versioning status for ContentType image', we_base_request::BOOL, false),
		'VERSIONING_TEXT_HTML' => array('Versioning status for ContentType text/html', we_base_request::BOOL, false),
		'VERSIONING_TEXT_WEBEDITION' => array('Versioning status for ContentType text/webedition', we_base_request::BOOL, true),
		'VERSIONING_TEXT_HTACCESS' => array('Versioning status for ContentType text/htaccess', we_base_request::BOOL, false),
		'VERSIONING_TEXT_WETMPL' => array('Versioning status for ContentType text/weTmpl', we_base_request::BOOL, true),
		'VERSIONING_TEXT_JS' => array('Versioning status for ContentType text/js', we_base_request::BOOL, true),
		'VERSIONING_TEXT_CSS' => array('Versioning status for ContentType text/css', we_base_request::BOOL, true),
		'VERSIONING_TEXT_PLAIN' => array('Versioning status for ContentType text/plain', we_base_request::BOOL, false),
		'VERSIONING_VIDEO' => array('Versioning status for ContentType video/*', we_base_request::BOOL, false),
		'VERSIONING_AUDIO' => array('Versioning status for ContentType audio/*', we_base_request::BOOL, false),
		'VERSIONING_FLASH' => array('Versioning status for ContentType application/x-shockwave-flash', we_base_request::BOOL, false),
		'VERSIONING_QUICKTIME' => array('Versioning status for ContentType video/quicktime', we_base_request::BOOL, false),
		'VERSIONING_SONSTIGE' => array('Versioning status for ContentType application/*', we_base_request::BOOL, false),
		'VERSIONING_TEXT_XML' => array('Versioning status for ContentType text/xml', we_base_request::BOOL, false),
		'VERSIONING_OBJECT' => array('Versioning status for ContentType objectFile', we_base_request::BOOL, false),
		'VERSIONS_TIME_DAYS' => array('Versioning Number of Days', we_base_request::INT, -1),
		'VERSIONS_TIME_WEEKS' => array('Versioning Number of Weeks', we_base_request::INT, -1),
		'VERSIONS_TIME_YEARS' => array('Versioning Number of Years', we_base_request::INT, -1),
		'VERSIONS_ANZAHL' => array('Versioning Number of Versions', we_base_request::INT, 3),
		'VERSIONS_CREATE' => array('Versioning Save version only if publishing', we_base_request::BOOL, false),
		'VERSIONS_CREATE_TMPL' => array('Versioning Save template version only on special request', we_base_request::BOOL, true),
		'VERSIONS_TIME_DAYS_TMPL' => array('Versioning Number of Days', we_base_request::INT, -1),
		'VERSIONS_TIME_WEEKS_TMPL' => array('Versioning Number of Weeks', we_base_request::INT, -1),
		'VERSIONS_TIME_YEARS_TMPL' => array('Versioning Number of Years', we_base_request::INT, -1),
		'VERSIONS_ANZAHL_TMPL' => array('Versioning Number of Versions', we_base_request::INT, 5),
//security
		'SECURITY_DELETE_SESSION' => array('whether to delte a session on logout of a customer', we_base_request::BOOL, true),
		'SECURITY_LIMIT_CUSTOMER_IP' => array('Limit # of failed logins comming from the same IP', we_base_request::INT, 10),
		'SECURITY_LIMIT_CUSTOMER_IP_HOURS' => array('Limit failed logins comming from same IP connections per # hours', we_base_request::INT, 3),
		'SECURITY_LIMIT_CUSTOMER_NAME' => array('Limit # of failed logins with same username', we_base_request::INT, 4),
		'SECURITY_LIMIT_CUSTOMER_NAME_HOURS' => array('Limit failed logins with same usernames per # hours', we_base_request::INT, 1),
		'SECURITY_LIMIT_CUSTOMER_REDIRECT' => array('If limit reached, redirect to page', we_base_request::INT, 0),
		'SECURITY_DELAY_FAILED_LOGIN' => array('Delay a failed login by # seconds', we_base_request::INT, 3),
		'SECURITY_ENCRYPTION_TYPE_PASSWORD' => array('Determines how passwords are handled', we_base_request::INT, (!defined('SECURITY_ENCRYPTION_TYPE_PASSWORD') && defined('CUSTOMER_TABLE') && (f('SELECT COUNT(1)  FROM ' . CUSTOMER_TABLE) > 5) ? we_customer_customer::ENCRYPT_NONE : we_customer_customer::ENCRYPT_HASH)),
		'SECURITY_ENCRYPTION_KEY' => array('This is the encryption key used for password, if set to symmetric mode', we_base_request::STRING, ''),
		'SECURITY_SESSION_PASSWORD' => array('Determine if a userpassword is allowed to be stored in current session', we_base_request::INT, we_customer_customer::STORE_PASSWORD),
//internal
		'CONF_SAVED_VERSION' => array('config file version', we_base_request::INT, str_replace(array('$Rev$'), '', WE_SVNREV)),
	),
	'user' => array(//FIXME: most defaults (currently null) are handled by remember_value! change this!
//key => type,default-val, permission. default true
		'Language' => array(we_base_request::STRING, 'Deutsch'),
		'BackendCharset' => array(we_base_request::STRING, 'UTF-8'),
		'default_tree_count' => array(we_base_request::INT, 0),
		'sizeOpt' => array(we_base_request::BOOL, 0),
		'weWidth' => array(we_base_request::INT, 0),
		'weHeight' => array(we_base_request::INT, 0),
		'cockpit_amount_columns' => array(we_base_request::INT, 5),
		'cockpit_amount_last_documents' => array(we_base_request::INT, 5),
		'cockpit_dat' => array(we_base_request::STRING, ''),
		//all rss feeds set in cockpit
		'cockpit_rss' => array(we_base_request::STRING, ''),
		//current url for rss feed
		'cockpit_rss_feed_url' => array(we_base_request::STRING, ''),
		'editorMode' => array(we_base_request::STRING, 'codemirror2'),
		'editorCodecompletion' => array(we_base_request::STRING, we_serialize(array('WE' => 1, 'htmlTag' => 1, 'html5Tag' => 1), 'json')),
		'editorCommentFontcolor' => array(we_base_request::STRING, null),
		'editorDocuintegration' => array(we_base_request::BOOL, true),
		'editorFont' => array(we_base_request::BOOL, ''),
		'editorFontname' => array(we_base_request::STRING, ''),
		'editorFontsize' => array(we_base_request::INT, 0),
		'editorFontcolor' => array(we_base_request::STRING, ''),
		'editorHighlightCurrentLine' => array(we_base_request::BOOL, true),
		'editorTheme' => array(we_base_request::STRING, 'elegant'),
		'editorWeTagFontcolor' => array(we_base_request::STRING, ''),
		'editorWeAttributeFontcolor' => array(we_base_request::STRING, ''),
		'editorHTMLTagFontcolor' => array(we_base_request::STRING, ''),
		'editorHTMLAttributeFontcolor' => array(we_base_request::STRING, ''),
		'editorPiTagFontcolor' => array(we_base_request::STRING, ''),
		'editorLinenumbers' => array(we_base_request::BOOL, true),
		'editorTooltips' => array(we_base_request::BOOL, false), //tags
		'editorTooltipsIDs' => array(we_base_request::BOOL, false), //IDS
		'editorTooltipFont' => array(we_base_request::BOOL, ''),
		'editorTooltipFontname' => array(we_base_request::STRING, ''),
		'editorTooltipFontsize' => array(we_base_request::INT, 0),
		'editorWrap' => array(we_base_request::BOOL, false),
		'message_reporting' => array(we_base_request::INT, 7),
		'xhtml_show_wrong' => array(we_base_request::BOOL, false),
		'xhtml_show_wrong_text' => array(we_base_request::BOOL, false),
		'xhtml_show_wrong_js' => array(we_base_request::BOOL, false),
		'xhtml_show_wrong_error_log' => array(we_base_request::BOOL, false),
		'specify_jeditor_colors' => array(we_base_request::BOOL, false),
		'seem_start_type' => array(we_base_request::STRING, 'cockpit', 'CHANGE_START_DOCUMENT'),
		'seem_start_file' => array(we_base_request::INT, 0),
		'seem_start_weapp' => array(we_base_request::STRING, ''),
		'autostartPlugin' => array(we_base_request::INT, 0),
		'DefaultTemplateID' => array(we_base_request::INT, 0),
		'editorHeight' => array(we_base_request::INT, 0),
		'editorSizeOpt' => array(we_base_request::BOOL, false),
		'editorWidth' => array(we_base_request::INT, 0),
		'force_glossary_action' => array(we_base_request::BOOL, false),
		'force_glossary_check' => array(we_base_request::BOOL, false),
		'import_from' => array(we_base_request::FILE, ''),
		'openFolders_tblFile' => array(we_base_request::INTLIST, ''),
		'openFolders_tblObject' => array(we_base_request::INTLIST, ''),
		'openFolders_tblObjectFiles' => array(we_base_request::INTLIST, ''),
		'openFolders_tblTemplates' => array(we_base_request::INTLIST, ''),
		'promptPlugin' => array(we_base_request::BOOL, false),
		'siteImportPrefs' => array(we_base_request::STRING, ''),
		'usePlugin' => array(we_base_request::BOOL, false),
		'editorShowTab' => array(we_base_request::BOOL, true),
		'editorTabSize' => array(we_base_request::INT, 2),
		'editorAutoIndent' => array(we_base_request::BOOL, true),
	),
	'conf' => array(
		//description,request-type if any, default, encode
		'HTTP_USERNAME' => array('if used password protection to the webEdition directory, the username', we_base_request::STRING, '', true),
		'HTTP_PASSWORD' => array('if used password protection to the webEdition directory, the password', we_base_request::RAW_CHECKED, '', true),
		'DB_CONNECT' => array('Mode how to access the database: mysqli_connect, mysqli_pconnect, deprecated: connect, pconnect', we_base_request::STRING, ''),
		'DB_SET_CHARSET' => array('connection charset to db', we_base_request::STRING, 'utf8'),
		//note these settings are user-settings, not changed by request/frontend
		'DB_HOST' => array('Domain or IP address of the database server', '', 'localhost'),
		'DB_DATABASE' => array('Name of database used by webEdition', '', 'webedition'),
		'DB_USER' => array('Username to access the database', '', 'root', true),
		'DB_PASSWORD' => array('Password to access the database', '', 'root', true),
		'TBL_PREFIX' => array('Prefix of tables in database for this webEdition.', '', ''),
		'DB_CHARSET' => array('Charset of tables in database for this webEdition.', we_base_request::STRING, (defined('DB_CHARSET') ? DB_CHARSET : '')),
		'DB_COLLATION' => array('Collation of tables in database for this webEdition.', '', ''),
		'WE_LANGUAGE' => array('Original language of this version of webEdition, used for login-screen', '', 'English'),
		'WE_BACKENDCHARSET' => array('Original backend charset of this version of webEdition, used for login-screen', '', 'UTF-8'),
	),
	'other' => array(
		'formmail_values' => array(we_base_request::RAW_CHECKED, '', 'FORMMAIL'),
		'formmail_deleted' => array(we_base_request::RAW, '', 'FORMMAIL'),
		'useproxy' => array(we_base_request::BOOL, false),
		'proxyhost' => array(we_base_request::URL, ''),
		'proxyport' => array(we_base_request::INT, 0),
		'proxyuser' => array(we_base_request::STRING, ''),
		'proxypass' => array(we_base_request::RAW_CHECKED, ''),
		'active_integrated_modules' => array(we_base_request::STRING, ''),
		'DB_CONNECT' => array(we_base_request::STRING, ''),
		'useauth' => array(we_base_request::BOOL, false), //pseudo element
		'locale_default' => array(we_base_request::STRING, 'de_DE'),
		'locale_locales' => array(we_base_request::STRING, 'de_DE'),
	),
);
