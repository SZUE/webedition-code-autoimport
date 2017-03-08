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
$GLOBALS['tabs'] = [
	'ui' => ['fa-tv', ''],
//	'extensions' => 'EDIT_SETTINGS_DEF_EXT',
	'editor' => ['fa-edit', ['ADMINISTRATOR', 'CAN_SEE_TEMPLATES', 'NEW_CSS', 'NEW_JS']],
	'seolinks' => ['fa-search', 'ADMINISTRATOR'],
	'language' => ['fa-language', 'ADMINISTRATOR'],
	'countries' => ['fa-flag-checkered', 'ADMINISTRATOR'],
	'error_handling' => ['fa-bug', 'ADMINISTRATOR'],
//	'validation' => array('fa-check', 'ADMINISTRATOR'),
	'email' => ['fa-envelope-o', 'ADMINISTRATOR'],
	//'message_reporting' => '',
	'recipients' => ['fa-envelope-o', 'FORMMAIL'],
	'versions' => ['fa-history', 'ADMINISTRATOR'],
	'defaultAttribs' => ['fa-tags', 'ADMINISTRATOR'],
	'advanced' => ['fa-wrench', 'ADMINISTRATOR'],
	'system' => ['fa-cogs', 'ADMINISTRATOR'],
	'security' => ['fa-key', 'ADMINISTRATOR'],
	'proxy' => ['fa-bomb', 'ADMINISTRATOR'],
	'modules' => ['fa-cubes', 'ADMINISTRATOR'],
];

$GLOBALS['configs'] = [// Create array for needed configuration variables

	'global' => [//key => comment, default, changed right (default Admin)
// Variables for SEEM
		'WE_SEEM' => ['Enable seeMode', we_base_request::BOOL, 1],
// Variables for LogIn
		'WE_LOGIN_HIDEWESTATUS' => ['Hide if webEdition is Nightly or Alpha or.. Release Version', we_base_request::BOOL, 1],
		'WE_LOGIN_WEWINDOW' => ['Decide how WE opens: 0 allow both, 1 POPUP only, 2 same Window only', we_base_request::INT, 0],
// Variables for thumbnails
		'WE_THUMBNAIL_DIRECTORY' => ['Directory in which to save thumbnails', we_base_request::FILE, '/_thumbnails_'],
// Variables for error handling
		'WE_ERROR_HANDLER' => ['Show errors that occur in webEdition', we_base_request::BOOL, true],
		'WE_ERROR_NOTICES' => ['Handle notices', we_base_request::BOOL, false],
		'WE_ERROR_DEPRECATED' => ['Handle deprecated warnings', we_base_request::BOOL, true],
		'WE_ERROR_WARNINGS' => ['Handle warnings', we_base_request::BOOL, true],
		'WE_ERROR_ERRORS' => ['Handle errors', we_base_request::BOOL, true],
		'WE_ERROR_SHOW' => ['Show errors', we_base_request::BOOL, false],
		'WE_ERROR_LOG' => ['Log errors', we_base_request::BOOL, true],
		'WE_ERROR_MAIL' => ['Mail errors', we_base_request::BOOL, false],
		'WE_ERROR_MAIL_ADDRESS' => ['E-Mail address to which to mail errors', we_base_request::EMAIL, ''],
		'ERROR_DOCUMENT_NO_OBJECTFILE' => ['Document to open when trying to open non-existing object', we_base_request::INT, 0],
		//'DISABLE_TEMPLATE_CODE_CHECK' => array('Disable the check for php-errors in templates', we_base_request::BOOL, false),
		'INLINEEDIT_DEFAULT' => ['Default setting for inlineedit attribute', we_base_request::BOOL, true],
		'IMAGESTARTID_DEFAULT' => ['Default setting for attribute imagestartdir in wetextarea', we_base_request::INT, 0],
		'WE_PHP_DEFAULT' => ['Default setting for php attribute', we_base_request::BOOL, false],
		'COMMANDS_DEFAULT' => ['Default setting for commands attribute', we_base_request::STRING, ''],
		'REMOVEFIRSTPARAGRAPH_DEFAULT' => ['Default setting for removeparagraph attribute', we_base_request::BOOL, false],
		'HIDENAMEATTRIBINWEIMG_DEFAULT' => ['Default setting for hide name attribute in weimg output', we_base_request::BOOL, true],
		'HIDENAMEATTRIBINWEFORM_DEFAULT' => ['Default setting for hide name attribute in weform output', we_base_request::BOOL, false],
		'REPLACEACRONYM' => ['Remove unsupported html5 acronym tag', we_base_request::BOOL, false],
// we_css
		'CSSAPPLYTO_DEFAULT' => ['Default setting for we:css attribute applyto', we_base_request::STRING, 'around'],
// hooks
		'EXECUTE_HOOKS' => ['Default setting for hook execution', we_base_request::BOOL, false],
// xhtml
		'XHTML_DEFAULT' => ['Default setting for xml attribute', we_base_request::BOOL, true],
		/* 'XHTML_DEBUG' => array('Enable XHTML debug', we_base_request::BOOL, false),
		  'XHTML_REMOVE_WRONG' => array('Remove wrong xhtml attributes from we:tags', we_base_request::BOOL, false),
		 */
//system
		'FILE_UPLOAD_MAX_UPLOAD_SIZE' => ['Set the maximum size a file can have', we_base_request::INT, 128],
		'WE_NEW_FOLDER_MOD' => ['File permissions when creating a new directory', we_base_request::INT, 755], //this should be string but deny access by user doesn't make sense
		'WE_DOCTYPE_WORKSPACE_BEHAVIOR' => ['Which Doctypes should be shown for which workspace', we_base_request::BOOL, false],
		'SCHEDULER_TRIGGER' => ['decide how the scheduler works', we_base_request::INT, 1], //postdoc
		'SYSTEM_WE_SESSION' => ['use webedition session handling', we_base_request::BOOL, false],
		'SYSTEM_WE_SESSION_TIME' => ['time after which the session is killed if not active anymore', we_base_request::INT, get_cfg_var('session.gc_maxlifetime') ?: 1440],
		'SYSTEM_WE_SESSION_CRYPT' => ['crypt we session before save', we_base_request::INT, 2],
// accessibility
		'SHOWINPUTS_DEFAULT' => ['Default setting for showinputs attribute', we_base_request::BOOL, true],
		/* 		'WYSIWYG_TYPE' => array('define used wysiwyg editor', we_base_request::STRING, 'tinyMCE'),
		  'WYSIWYG_TYPE_FRONTEND' => array('define used wysiwyg editor in frontend', we_base_request::STRING, 'tinyMCE'), */
		'WE_MAILER' => ['mailer type; possible values are php and smtp', we_base_request::STRING, 'php'],
		'SMTP_SERVER' => ['SMTP_SERVER', we_base_request::STRING, 'localhost'],
		'SMTP_PORT' => ['SMTP server port', we_base_request::INT, 25],
		'SMTP_AUTH' => ['SMTP authentication', we_base_request::BOOL, false],
		'SMTP_USERNAME' => ['SMTP username', we_base_request::STRING, ''],
		'SMTP_PASSWORD' => ['SMTP password', we_base_request::RAW_CHECKED, ''],
		'SMTP_ENCRYPTION' => ['SMTP encryption', we_base_request::STRING, 0],
//formmail stuff
		'FORMMAIL_CONFIRM' => ['Flag if formmail confirm function should be work', we_base_request::BOOL, true], //this is restricted to admin
		'FORMMAIL_VIAWEDOC' => ['Flag if formmail should be send only via a webEdition document', we_base_request::BOOL, false, 'FORMMAIL'],
		'FORMMAIL_LOG' => ['Flag if formmail calls should be logged', we_base_request::BOOL, true, 'FORMMAIL'],
		'FORMMAIL_EMPTYLOG' => ['Time how long formmail calls should be logged', we_base_request::INT, 604800, 'FORMMAIL'],
		'FORMMAIL_BLOCK' => ['Flag if formmail calls should be blocked after a time', we_base_request::BOOL, true, 'FORMMAIL'],
		'FORMMAIL_SPAN' => ['Time span in seconds', we_base_request::INT, 300, 'FORMMAIL'],
		'FORMMAIL_TRIALS' => ['Num of trials sending formmail with same ip address in span', we_base_request::INT, 3, 'FORMMAIL'],
		'FORMMAIL_BLOCKTIME' => ['Time to block ip', we_base_request::INT, 86400, 'FORMMAIL'],
// sidebar stuff
		'SIDEBAR_DISABLED' => ['Sidebar is disabled', we_base_request::BOOL, false],
		'SIDEBAR_SHOW_ON_STARTUP' => ['Show Sidebar on startup', we_base_request::BOOL, true],
		'SIDEBAR_DEFAULT_DOCUMENT' => ['Default document id of the sidebar', we_base_request::INT, 0],
		'SIDEBAR_DEFAULT_WIDTH' => ['Default width of the sidebar', we_base_request::INT, 300],
// extension stuff
		'DEFAULT_STATIC_EXT' => ['Default static extension', we_base_request::STRING, ".html", 'EDIT_SETTINGS_DEF_EXT'],
		'DEFAULT_DYNAMIC_EXT' => ['Default dynamic extension', we_base_request::STRING, ".php", 'EDIT_SETTINGS_DEF_EXT'],
		'DEFAULT_HTML_EXT' => ['Default html extension', we_base_request::STRING, ".html", 'EDIT_SETTINGS_DEF_EXT'],
//naviagtion stuff
		'NAVIGATION_ENTRIES_FROM_DOCUMENT' => ['Flag if new NAV- entries added from Dokument should be items or folders', we_base_request::BOOL, false],
		'NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH' => ['Flag if NAV- rules should be evaluated even after a first match', we_base_request::BOOL, false],
//SEO stuff
		'NAVIGATION_DIRECTORYINDEX_HIDE' => ['Flag if directoy-index files should be hidden in Nav-output', we_base_request::BOOL, false],
		'NAVIGATION_DIRECTORYINDEX_NAMES' => ['Comma seperated list such as index.php,index.html', we_base_request::STRING, 'index.php,index.html'],
		'WYSIWYGLINKS_DIRECTORYINDEX_HIDE' => ['Flag if directoy-index files should be hidden in Wysiwyg-editor output', we_base_request::BOOL, false],
		'TAGLINKS_DIRECTORYINDEX_HIDE' => ['Flag if directoy-index files should be hidden in tag output', we_base_request::BOOL, false],
		'OBJECTSEOURLS_LOWERCASE' => ['Flag if object SEO-URLs save in lower or camelcase', we_base_request::BOOL, false],
		'NAVIGATION_OBJECTSEOURLS' => ['Flag if we_objectID should be hidden from output of navigation', we_base_request::BOOL, false],
		'WYSIWYGLINKS_OBJECTSEOURLS' => ['Flag if we_objectID should be hidden from output of wysiwyg editior', we_base_request::BOOL, false],
		'TAGLINKS_OBJECTSEOURLS' => ['Flag if we_objectID should be hidden from output of tags', we_base_request::BOOL, false],
		'URLENCODE_OBJECTSEOURLS' => ['Flag if seo-urls should be urlencoded', we_base_request::BOOL, false],
		'SUPPRESS404CODE' => ['Flag if 404 not found should be suppressed', we_base_request::BOOL, false],
		'FORCE404REDIRECT' => ['Flag if redirect to 404 instead of include', we_base_request::BOOL, false],
		'SEOINSIDE_HIDEINWEBEDITION' => ['Flag if should be displayed in webEdition ', we_base_request::BOOL, false],
		'SEOINSIDE_HIDEINEDITMODE' => ['Flag if should be displayed in Editmode ', we_base_request::BOOL, false],
//lang link stuff
		'LANGLINK_SUPPORT' => ['Flag if automatic LanguageLinks should be supported ', we_base_request::BOOL, true],
//default charset
		'DEFAULT_CHARSET' => ['Default Charset', we_base_request::STRING, 'UTF-8'],
//countries
		'WE_COUNTRIES_TOP' => ['top countries', we_base_request::STRING, 'DE,AT,CH'],
		'WE_COUNTRIES_SHOWN' => ['other shown countries', we_base_request::STRING, 'BE,DK,FI,FR,GR,IE,IT,LU,NL,PT,SE,ES,GB,EE,LT,MT,PL,SK,SI,CZ,HU,CY'],
		'WE_COUNTRIES_DEFAULT' => ['shown if no coutry was choosen', we_base_request::STRING, ''],
//versions
		'VERSIONING_IMAGE' => ['Versioning status for ContentType image', we_base_request::BOOL, false],
		'VERSIONING_TEXT_HTML' => ['Versioning status for ContentType text/html', we_base_request::BOOL, false],
		'VERSIONING_TEXT_WEBEDITION' => ['Versioning status for ContentType text/webedition', we_base_request::BOOL, true],
		'VERSIONING_TEXT_HTACCESS' => ['Versioning status for ContentType text/htaccess', we_base_request::BOOL, false],
		'VERSIONING_TEXT_WETMPL' => ['Versioning status for ContentType text/weTmpl', we_base_request::BOOL, true],
		'VERSIONING_TEXT_JS' => ['Versioning status for ContentType text/js', we_base_request::BOOL, true],
		'VERSIONING_TEXT_CSS' => ['Versioning status for ContentType text/css', we_base_request::BOOL, true],
		'VERSIONING_TEXT_PLAIN' => ['Versioning status for ContentType text/plain', we_base_request::BOOL, false],
		'VERSIONING_VIDEO' => ['Versioning status for ContentType video/*', we_base_request::BOOL, false],
		'VERSIONING_AUDIO' => ['Versioning status for ContentType audio/*', we_base_request::BOOL, false],
		'VERSIONING_FLASH' => ['Versioning status for ContentType application/x-shockwave-flash', we_base_request::BOOL, false],
		'VERSIONING_SONSTIGE' => ['Versioning status for ContentType application/*', we_base_request::BOOL, false],
		'VERSIONING_TEXT_XML' => ['Versioning status for ContentType text/xml', we_base_request::BOOL, false],
		'VERSIONING_OBJECT' => ['Versioning status for ContentType objectFile', we_base_request::BOOL, false],
		'VERSIONS_TIME_DAYS' => ['Versioning Number of Days', we_base_request::INT, -1],
		'VERSIONS_TIME_WEEKS' => ['Versioning Number of Weeks', we_base_request::INT, -1],
		'VERSIONS_TIME_YEARS' => ['Versioning Number of Years', we_base_request::INT, -1],
		'VERSIONS_ANZAHL' => ['Versioning Number of Versions', we_base_request::INT, 3],
		'VERSIONS_CREATE' => ['Versioning Save version only if publishing', we_base_request::BOOL, false],
		'VERSIONS_CREATE_TMPL' => ['Versioning Save template version only on special request', we_base_request::BOOL, true],
		'VERSIONS_TIME_DAYS_TMPL' => ['Versioning Number of Days', we_base_request::INT, -1],
		'VERSIONS_TIME_WEEKS_TMPL' => ['Versioning Number of Weeks', we_base_request::INT, -1],
		'VERSIONS_TIME_YEARS_TMPL' => ['Versioning Number of Years', we_base_request::INT, -1],
		'VERSIONS_ANZAHL_TMPL' => ['Versioning Number of Versions', we_base_request::INT, 5],
//security
		'SECURITY_DELETE_SESSION' => ['whether to delte a session on logout of a customer', we_base_request::BOOL, true],
		'SECURITY_LIMIT_CUSTOMER_IP' => ['Limit # of failed logins comming from the same IP', we_base_request::INT, 10],
		'SECURITY_LIMIT_CUSTOMER_IP_HOURS' => ['Limit failed logins comming from same IP connections per # hours', we_base_request::INT, 3],
		'SECURITY_LIMIT_CUSTOMER_NAME' => ['Limit # of failed logins with same username', we_base_request::INT, 4],
		'SECURITY_LIMIT_CUSTOMER_NAME_HOURS' => ['Limit failed logins with same usernames per # hours', we_base_request::INT, 1],
		'SECURITY_LIMIT_CUSTOMER_REDIRECT' => ['If limit reached, redirect to page', we_base_request::INT, 0],
		'SECURITY_DELAY_FAILED_LOGIN' => ['Delay a failed login by # seconds', we_base_request::INT, 3],
		'SECURITY_ENCRYPTION_TYPE_PASSWORD' => ['Determines how passwords are handled', we_base_request::INT, (!defined('SECURITY_ENCRYPTION_TYPE_PASSWORD') && defined('CUSTOMER_TABLE') && (f('SELECT COUNT(1)  FROM ' . CUSTOMER_TABLE) > 5) ? we_customer_customer::ENCRYPT_NONE : we_customer_customer::ENCRYPT_HASH)],
		'SECURITY_ENCRYPTION_KEY' => ['This is the encryption key used for password, if set to symmetric mode', we_base_request::STRING, ''],
		'SECURITY_SESSION_PASSWORD' => ['Determine if a userpassword is allowed to be stored in current session', we_base_request::INT, we_customer_customer::STORE_PASSWORD],
		'SECURITY_USER_PASS_REGEX' => ['Regex used to compare user password', we_base_request::STRING, we_users_user::DEFAULT_PASS_REGEX],
		'SECURITY_USER_PASS_DESC' => ['Userfriendly description of how the password should be chosen', we_base_request::STRING, 'Enter a password with at least 6 and at most 20 characters.'],
//internal
		'CONF_SAVED_VERSION' => ['config file version', we_base_request::INT, str_replace(['$Rev$'], '', WE_SVNREV)],
	],
	'user' => [//FIXME: most defaults (currently null) are handled by remember_value! change this!
//key => type,default-val, permission. default true
		'Language' => [we_base_request::STRING, 'Deutsch'],
		'BackendCharset' => [we_base_request::STRING, 'UTF-8'],
		'default_tree_count' => [we_base_request::INT, 0],
		'sizeOpt' => [we_base_request::BOOL, 0],
		'weWidth' => [we_base_request::INT, 0],
		'weHeight' => [we_base_request::INT, 0],
		'cockpit_amount_columns' => [we_base_request::INT, 5],
		'cockpit_amount_last_documents' => [we_base_request::INT, 5],
		'cockpit_dat' => [we_base_request::STRING, ''],
		//all rss feeds set in cockpit
		'cockpit_rss' => [we_base_request::SERIALIZED_KEEP, ''],
		'editorMode' => [we_base_request::STRING, 'codemirror2'],
		'editorCodecompletion' => [we_base_request::STRING, we_serialize(['WE' => 1, 'htmlTag' => 1, 'html5Tag' => 1], SERIALIZE_JSON)],
		'editorCommentFontcolor' => [we_base_request::STRING, null],
		'editorDocuintegration' => [we_base_request::BOOL, true],
		'editorFont' => [we_base_request::BOOL, ''],
		'editorFontname' => [we_base_request::STRING, ''],
		'editorFontsize' => [we_base_request::INT, 0],
		'editorFontcolor' => [we_base_request::STRING, ''],
		'editorHighlightCurrentLine' => [we_base_request::BOOL, true],
		'editorTheme' => [we_base_request::STRING, 'elegant'],
		'editorWeTagFontcolor' => [we_base_request::STRING, ''],
		'editorWeAttributeFontcolor' => [we_base_request::STRING, ''],
		'editorHTMLTagFontcolor' => [we_base_request::STRING, ''],
		'editorHTMLAttributeFontcolor' => [we_base_request::STRING, ''],
		'editorPiTagFontcolor' => [we_base_request::STRING, ''],
		'editorLinenumbers' => [we_base_request::BOOL, true],
		'editorTooltips' => [we_base_request::BOOL, false], //tags
		'editorTooltipsIDs' => [we_base_request::BOOL, false], //IDS
		'editorTooltipFont' => [we_base_request::BOOL, ''],
		'editorTooltipFontname' => [we_base_request::STRING, ''],
		'editorTooltipFontsize' => [we_base_request::INT, 0],
		'editorWrap' => [we_base_request::BOOL, false],
		'message_reporting' => [we_base_request::INT, we_message_reporting::WE_MESSAGE_WARNING | we_message_reporting::WE_MESSAGE_ERROR],
		/* 'xhtml_show_wrong' => array(we_base_request::BOOL, false),
		  'xhtml_show_wrong_text' => array(we_base_request::BOOL, false),
		  'xhtml_show_wrong_js' => array(we_base_request::BOOL, false),
		  'xhtml_show_wrong_error_log' => array(we_base_request::BOOL, false), */
		'specify_jeditor_colors' => [we_base_request::BOOL, false],
		'seem_start_type' => [we_base_request::STRING, 'cockpit', 'CHANGE_START_DOCUMENT'],
		'seem_start_file' => [we_base_request::INT, 0],
		'seem_start_weapp' => [we_base_request::STRING, ''],
		'autostartPlugin' => [we_base_request::INT, 0],
		'DefaultTemplateID' => [we_base_request::INT, 0],
		'editorHeight' => [we_base_request::INT, 0],
		'editorSizeOpt' => [we_base_request::BOOL, false],
		'editorWidth' => [we_base_request::INT, 0],
		'force_glossary_action' => [we_base_request::BOOL, false],
		'force_glossary_check' => [we_base_request::BOOL, false],
		'import_from' => [we_base_request::FILE, ''],
		'openFolders_tblFile' => [we_base_request::INTLIST, ''],
		'openFolders_tblObject' => [we_base_request::INTLIST, ''],
		'openFolders_tblObjectFiles' => [we_base_request::INTLIST, ''],
		'openFolders_tblTemplates' => [we_base_request::INTLIST, ''],
		'promptPlugin' => [we_base_request::BOOL, false],
		'siteImportPrefs' => [we_base_request::STRING, ''],
		'usePlugin' => [we_base_request::BOOL, false],
		'editorShowTab' => [we_base_request::BOOL, true],
		'editorShowSpaces' => [we_base_request::BOOL, true],
		'editorTabSize' => [we_base_request::INT, 2],
		'editorAutoIndent' => [we_base_request::BOOL, true],
		'editorIndentSpaces' => [we_base_request::BOOL, false],
	],
	'conf' => [
		'HTTP_USERNAME' => ['if used password protection to the webEdition directory, the username', we_base_request::STRING, '', true],
		'HTTP_PASSWORD' => ['if used password protection to the webEdition directory, the password', we_base_request::RAW_CHECKED, '', true],
		'DB_CONNECT' => ['Mode how to access the database: mysqli_connect, mysqli_pconnect, deprecated: connect, pconnect', we_base_request::STRING, ''],
		'DB_SET_CHARSET' => ['connection charset to db', we_base_request::STRING, 'utf8'],
		//note these settings are user-settings, not changed by request/frontend
		'DB_HOST' => ['Domain or IP address of the database server', '', 'localhost'],
		'DB_DATABASE' => ['Name of database used by webEdition', '', 'webedition'],
		'DB_USER' => ['Username to access the database', '', 'root', true],
		'DB_PASSWORD' => ['Password to access the database', '', 'root', true],
		'TBL_PREFIX' => ['Prefix of tables in database for this webEdition.', '', ''],
		'DB_CHARSET' => ['Charset of tables in database for this webEdition.', we_base_request::STRING, (defined('DB_CHARSET') ? DB_CHARSET : '')],
		'DB_COLLATION' => ['Collation of tables in database for this webEdition.', '', ''],
		'WE_LANGUAGE' => ['Original language of this version of webEdition, used for login-screen', '', 'English'],
		'WE_BACKENDCHARSET' => ['Original backend charset of this version of webEdition, used for login-screen', '', 'UTF-8'],
	],
	'other' => [
		'formmail_values' => [we_base_request::RAW_CHECKED, '', 'FORMMAIL'],
		'formmail_deleted' => [we_base_request::RAW, '', 'FORMMAIL'],
		'useproxy' => [we_base_request::BOOL, false],
		'proxyhost' => [we_base_request::URL, ''],
		'proxyport' => [we_base_request::INT, 0],
		'proxyuser' => [we_base_request::STRING, ''],
		'proxypass' => [we_base_request::RAW_CHECKED, ''],
		'active_integrated_modules' => [we_base_request::STRING, ''],
		'DB_CONNECT' => [we_base_request::STRING, ''],
		'useauth' => [we_base_request::BOOL, false], //pseudo element
		'locale_default' => [we_base_request::STRING, 'de_DE'],
		'locale_locales' => [we_base_request::STRING, 'de_DE'],
	],
];
