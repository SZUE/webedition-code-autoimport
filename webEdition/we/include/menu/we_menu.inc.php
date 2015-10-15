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
$seeMode = !(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL);
$we_menu = array(
	1000000 => array(// File
		'text' => g_l('javaMenu_global', '[file]'),
		'parent' => 0,
	),
	1010000 => array(// File > New
		'text' => g_l('javaMenu_global', '[new]'),
		'parent' => 1000000,
	),
	1010100 => array(// File > New > webEdition Document
		'text' => g_l('javaMenu_global', '[webEdition_page]'),
		'parent' => 1010000,
		'perm' => 'NEW_WEBEDITIONSITE || ADMINISTRATOR',
	),
	array(// File > New > webEdition Document > empty page
		'text' => g_l('javaMenu_global', '[empty_page]'),
		'parent' => 1010100,
		'cmd' => 'new_webEditionPage',
		'perm' => 'NO_DOCTYPE || ADMINISTRATOR',
	),
	1010198 => array(// separator
		'parent' => 1010100,
		'hide' => !$seeMode,
	),
	1010200 => array(// File > new > Object
		'text' => g_l('javaMenu_object', '[object]'),
		'parent' => 1010000,
		'perm' => 'NEW_OBJECTFILE || ADMINISTRATOR',
		'enabled' => 0,
		'hide' => !defined('OBJECT_TABLE')
	),
	array(// File > New > Others (Import)
		'text' => g_l('javaMenu_global', '[other]'),
		'parent' => 1010000,
		'cmd' => 'openFirstStepsWizardDetailTemplates',
		'perm' => 'NO_DOCTYPE && ADMINISTRATOR',
		'hide' => !$seeMode,
	),
	array(// File > Image
		'text' => g_l('javaMenu_global', '[image]'),
		'parent' => 1010000,
		'cmd' => 'new_image',
		'perm' => 'NEW_GRAFIK || ADMINISTRATOR',
	),
	1010300 => array(// File > New > Other
		'text' => g_l('javaMenu_global', '[other]'),
		'parent' => 1010000,
		'perm' => 'ADMINISTRATOR || NEW_HTML || NEW_FLASH || NEW_QUICKTIME || NEW_JS || NEW_CSS || NEW_TEXT || NEW_HTACCESS || NEW_SONSTIGE',
	),
	array(// File > New > Other > html
		'text' => g_l('javaMenu_global', '[html_page]'),
		'parent' => 1010300,
		'cmd' => 'new_html_page',
		'perm' => 'NEW_HTML || ADMINISTRATOR',
	),
	array(// File > New > Other > Flash
		'text' => g_l('javaMenu_global', '[flash_movie]'),
		'parent' => 1010300,
		'cmd' => 'new_flash_movie',
		'perm' => 'NEW_FLASH || ADMINISTRATOR',
	),
	array(// File > New Other > quicktime
		'text' => g_l('javaMenu_global', '[quicktime_movie]'),
		'parent' => 1010300,
		'cmd' => 'new_quicktime_movie',
		'perm' => 'NEW_QUICKTIME || ADMINISTRATOR',
	),
	array(// File > New Other > video
		'text' => g_l('contentTypes', '[' . we_base_ContentTypes::VIDEO . ']'),
		'parent' => 1010300,
		'cmd' => 'new_video_movie',
		'perm' => 'NEW_FLASH || ADMINISTRATOR',
	),
	array(// File > New Other > audio
		'text' => g_l('contentTypes', '[' . we_base_ContentTypes::AUDIO . ']'),
		'parent' => 1010300,
		'cmd' => 'new_audio_audio',
		'perm' => 'NEW_SONSTIGE || ADMINISTRATOR',
	),
	array(// File > New > Other > Javascript
		'text' => g_l('javaMenu_global', '[javascript]'),
		'parent' => 1010300,
		'cmd' => 'new_javascript',
		'perm' => 'NEW_JS || ADMINISTRATOR',
	),
	array(// File > New > Other > CSS
		'text' => g_l('javaMenu_global', '[css_stylesheet]'),
		'parent' => 1010300,
		'cmd' => 'new_css_stylesheet',
		'perm' => 'NEW_CSS || ADMINISTRATOR',
	),
	array(// File > New > Other > Text
		'text' => g_l('javaMenu_global', '[text_plain]'),
		'parent' => 1010300,
		'cmd' => 'new_text_plain',
		'perm' => 'NEW_TEXT || ADMINISTRATOR',
	),
	array(// File > New > Other > XML
		'text' => g_l('javaMenu_global', '[text_xml]'),
		'parent' => 1010300,
		'cmd' => 'new_text_xml',
		'perm' => 'NEW_TEXT || ADMINISTRATOR',
	),
	array(// File > New > Other > htaccess
		'text' => g_l('javaMenu_global', '[htaccess]'),
		'parent' => 1010300,
		'cmd' => 'new_text_htaccess',
		'perm' => 'NEW_HTACCESS || ADMINISTRATOR',
	),
	array(// File > New > Other > Other (Binary)
		'text' => g_l('javaMenu_global', '[other_files]'),
		'parent' => 1010300,
		'cmd' => 'new_binary_document',
		'perm' => 'NEW_SONSTIGE || ADMINISTRATOR',
	),
	array(// separator
		'parent' => 1010000,
		'hide' => $seeMode
	),
	1011000 => array(// File > New > Directory
		'text' => g_l('javaMenu_global', '[directory]'),
		'parent' => 1010000,
		'hide' => $seeMode
	),
	array(// File > New > Directory > Document
		'text' => g_l('javaMenu_global', '[document_directory]'),
		'parent' => 1011000,
		'cmd' => 'new_document_folder',
		'perm' => 'NEW_DOC_FOLDER || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// File > New > Directory > Template
		'text' => g_l('javaMenu_global', '[template_directory]'),
		'parent' => 1011000,
		'cmd' => 'new_template_folder',
		'perm' => 'NEW_TEMP_FOLDER || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// File > new > directory > objectfolder
		'text' => g_l('javaMenu_object', '[object_directory]'),
		'parent' => 1011000,
		'cmd' => 'new_objectfile_nested_folder',
		'perm' => 'NEW_OBJECTFILE_FOLDER || ADMINISTRATOR',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	),
	array(// separator
		'parent' => 1010000,
		'perm' => 'NEW_OBJECT || NEW_TEMPLATE || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// File > New > Template
		'text' => g_l('javaMenu_global', '[template]'),
		'parent' => 1010000,
		'cmd' => 'new_template',
		'perm' => 'NEW_TEMPLATE || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// File > new > Class
		'text' => g_l('javaMenu_object', '[class]'),
		'parent' => 1010000,
		'cmd' => 'new_object',
		'perm' => 'NEW_OBJECT || ADMINISTRATOR',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	),
	/* 	$we_menu[1011100]['parent'] = 1010000; // separator
	  // File > New > Wizards
	  'text'=> g_l('javaMenu_global', '[wizards]') . '&hellip;',
	  'parent'=> 1010000,
	  'enabled'=> 1,

	  // File > New > Wizard > First Steps Wizard
	  'text'=> g_l('javaMenu_global', '[first_steps_wizard]'),
	  'parent'=> 1011200,
	  'cmd'=> 'openFirstStepsWizardMasterTemplate',
	  'perm'=> 'ADMINISTRATOR',
	  'enabled'=> 1,

	  $we_menu[1020000]['parent'] = 1000000; // separator
	 */
	1030000 => array(// File > Open
		'text' => g_l('javaMenu_global', '[open]'),
		'parent' => 1000000,
	),
	array(// File > Open > Document
		'text' => g_l('javaMenu_global', '[open_document]') . '&hellip;',
		'parent' => 1030000,
		'cmd' => 'open_document',
		'perm' => 'CAN_SEE_DOCUMENTS || ADMINISTRATOR',
	),
	array(// File > open > Object
		'text' => g_l('javaMenu_object', '[open_object]') . '&hellip;',
		'parent' => 1030000,
		'cmd' => 'open_objectFile',
		'perm' => 'CAN_SEE_OBJECTFILES || ADMINISTRATOR',
		'hide' => !defined('OBJECT_TABLE')
	),
	array(// separator
		'parent' => 1030000,
		'perm' => 'CAN_SEE_TEMPLATES || CAN_SEE_OBJECTS || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// File > Open > Template
		'text' => g_l('javaMenu_global', '[open_template]') . '&hellip;',
		'parent' => 1030000,
		'cmd' => 'open_template',
		'perm' => 'CAN_SEE_TEMPLATES || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// File > Open > Class
		'text' => g_l('javaMenu_object', '[open_class]') . '&hellip;',
		'parent' => 1030000,
		'cmd' => 'open_object',
		'perm' => 'CAN_SEE_OBJECTS || ADMINISTRATOR',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	),
	1080000 => array(// File > Delete
		'text' => g_l('javaMenu_global', '[delete]') . ($seeMode ? '&hellip;' : ''),
		'parent' => 1000000,
		'cmd' => $seeMode ? 'openDelSelector' : '',
		'perm' => $seeMode ? 'DELETE_DOCUMENT || ADMINISTRATOR' : 'DELETE_DOCUMENT || DELETE_OBJECTFILE || DELETE_TEMPLATE || DELETE_OBJECT || ADMINISTRATOR',
	),
	array(// File > Delete > Documents
		'text' => g_l('javaMenu_global', '[documents]'),
		'parent' => 1080000,
		'cmd' => 'delete_documents',
		'perm' => 'DELETE_DOCUMENT || ADMINISTRATOR',
		'hide' => $seeMode,
	),
	array(// File > Delete > Objects
		'text' => g_l('javaMenu_object', '[objects]'),
		'parent' => 1080000,
		'cmd' => 'delete_objectfile',
		'perm' => 'DELETE_OBJECTFILE || ADMINISTRATOR',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	),
	array(// separator
		'parent' => 1080000,
		'perm' => 'CAN_SEE_TEMPLATES || CAN_SEE_OBJECTS || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// File > Delete > Templates
		'text' => g_l('javaMenu_global', '[templates]'),
		'parent' => 1080000,
		'cmd' => 'delete_templates',
		'perm' => 'DELETE_TEMPLATE || ADMINISTRATOR',
		'hide' => $seeMode,
	),
	array(// File > Delete > Classes
		'text' => g_l('javaMenu_object', '[classes]'),
		'parent' => 1080000,
		'cmd' => 'delete_object',
		'perm' => 'DELETE_OBJECT || ADMINISTRATOR',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	),
	1090000 => array(// File > Move
		'text' => g_l('javaMenu_global', '[move]'),
		'parent' => 1000000,
		'hide' => $seeMode,
		'perm' => 'MOVE_DOCUMENT || MOVE_OBJECTFILE || MOVE_TEMPLATE || ADMINISTRATOR',
	),
// File > Move > Documents
	1090100 => array(
		'text' => g_l('javaMenu_global', '[documents]'),
		'parent' => 1090000,
		'cmd' => 'move_documents',
		'perm' => 'MOVE_DOCUMENT || ADMINISTRATOR',
		'hide' => $seeMode,
	),
	array(// File > move > objects
		'text' => g_l('javaMenu_object', '[objects]'),
		'parent' => 1090000,
		'cmd' => 'move_objectfile',
		'perm' => 'MOVE_OBJECTFILE || ADMINISTRATOR',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	),
	array(// separator
		'parent' => 1090000,
		'perm' => 'MOVE_TEMPLATE || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// File > Move > Templates
		'text' => g_l('javaMenu_global', '[templates]'),
		'parent' => 1090000,
		'cmd' => 'move_templates',
		'perm' => 'MOVE_TEMPLATE || ADMINISTRATOR',
		'hide' => $seeMode,
	),
	array(// separator
		'parent' => 1000000
	),
	/* array(// File > Save
	  'text' => g_l('javaMenu_global', '[save]'),
	  'parent' => 1000000,
	  'cmd' => 'trigger_save_document',
	  'perm' => 'SAVE_DOCUMENT_TEMPLATE || ADMINISTRATOR',

	  ),
	  array(// File > Publish
	  'text' => g_l('javaMenu_global', '[publish]'),
	  'parent' => 1000000,
	  'cmd' => 'trigger_publish_document',
	  'perm' => 'PUBLISH || ADMINISTRATOR',

	  ), */
	array(
		'text' => g_l('javaMenu_glossary', '[glossary_check]'),
		'parent' => 1000000,
		'cmd' => 'glossary_check',
		'perm' => '',
		'hide' => !(defined('GLOSSARY_TABLE'))
	),
	array(// File > Delete Active Document
		'text' => g_l('javaMenu_global', '[delete_active_document]'),
		'parent' => 1000000,
		'cmd' => 'delete_single_document_question',
		'perm' => 'DELETE_DOCUMENT || DELETE_OBJECTFILE || DELETE_TEMPLATE || DELETE_OBJECT || ADMINISTRATOR',
		'hide' => $seeMode
	),
	/* array(// File > Close
	  'text' => g_l('javaMenu_global', '[close_single_document]'),
	  'parent' => 1000000,
	  'cmd' => 'close_document',
	  'perm' => '',

	  ), */
	array(// File > Close All
		'text' => g_l('javaMenu_global', '[close_all_documents]'),
		'parent' => 1000000,
		'cmd' => 'close_all_documents',
		'perm' => '',
		'hide' => $seeMode
	),
	array(// File > Close All But this
		'text' => g_l('javaMenu_global', '[close_all_but_active_document]'),
		'parent' => 1000000,
		'cmd' => 'close_all_but_active_document',
		'perm' => '',
		'hide' => $seeMode
	),
	array(// separator
		'parent' => 1000000
	),
	array(// File > unpublished pages
		'text' => g_l('javaMenu_global', '[unpublished_pages]') . '&hellip;',
		'parent' => 1000000,
		'cmd' => 'openUnpublishedPages',
		'perm' => 'CAN_SEE_DOCUMENTS || ADMINISTRATOR',
	),
	array(// File > unpublished objects
		'text' => g_l('javaMenu_object', '[unpublished_objects]') . '&hellip;',
		'parent' => 1000000,
		'cmd' => 'openUnpublishedObjects',
		'perm' => 'CAN_SEE_OBJECTFILES || ADMINISTRATOR',
		'hide' => !defined('OBJECT_TABLE')
	),
	array(// File > Search
		'text' => g_l('javaMenu_global', '[search]') . '&hellip;',
		'parent' => 1000000,
		'cmd' => 'tool_weSearch_edit',
		'perm' => '',
	),
	array(// separator
		'parent' => 1000000,
	),
	1150000 => array(// File > Import/Export
		'text' => g_l('javaMenu_global', '[import_export]'),
		'parent' => 1000000,
		'perm' => 'GENERICXML_EXPORT || CSV_EXPORT || FILE_IMPORT || SITE_IMPORT || GENERICXML_IMPORT || CSV_IMPORT || WXML_IMPORT || ADMINISTRATOR',
	),
	array(// File > Import/Export > Import
		'text' => g_l('javaMenu_global', '[import]') . '&hellip;',
		'cmd' => 'import',
		'parent' => 1150000,
		'perm' => 'FILE_IMPORT || SITE_IMPORT || GENERICXML_IMPORT || CSV_IMPORT || WXML_IMPORT || ADMINISTRATOR',
	),
	array(// File > Import/Export > Export
		'text' => g_l('javaMenu_global', '[export]') . '&hellip;',
		'cmd' => 'export',
		'parent' => 1150000,
		'perm' => 'GENERICXML_EXPORT || CSV_EXPORT || ADMINISTRATOR',
	),
	1160000 => array(// File > Backup
		'text' => g_l('javaMenu_global', '[backup]'),
		'parent' => 1000000,
		'hide' => $seeMode,
		'perm' => 'BACKUPLOG || IMPORT || EXPORT || EXPORTNODOWNLOAD || ADMINISTRATOR',
	),
	array(// File > Backup > make
		'text' => g_l('javaMenu_global', '[make_backup]') . '&hellip;',
		'parent' => $seeMode ? 1000000 : 1160000,
		'cmd' => 'make_backup',
		'perm' => 'EXPORT || EXPORTNODOWNLOAD || ADMINISTRATOR',
	),
	array(// File > Backup > recover
		'text' => g_l('javaMenu_global', '[recover_backup]') . '&hellip;',
		'parent' => 1160000,
		'cmd' => 'recover_backup',
		'perm' => 'IMPORT || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// File > Backup > view Log
		'text' => g_l('javaMenu_global', '[view_backuplog]') . '&hellip;',
		'parent' => $seeMode ? 1000000 : 1160000,
		'cmd' => 'view_backuplog',
		'perm' => 'BACKUPLOG || ADMINISTRATOR',
	),
	array(// File > rebuild
		'text' => g_l('javaMenu_global', '[rebuild]') . '&hellip;',
		'parent' => 1000000,
		'cmd' => 'rebuild',
		'perm' => 'REBUILD || ADMINISTRATOR',
	),
	array(// File > Browse server
		'text' => g_l('javaMenu_global', '[browse_server]') . '&hellip;',
		'parent' => 1000000,
		'cmd' => 'browse_server',
		'perm' => 'BROWSE_SERVER || ADMINISTRATOR',
		'hide' => $seeMode,
	),
	array(// separator
		'parent' => 1000000,
		'perm' => 'BROWSE_SERVER || ADMINISTRATOR',
		'hide' => $seeMode,
	),
	array(// File > Quit
		'text' => g_l('javaMenu_global', '[quit]'),
		'parent' => 1000000,
		'cmd' => 'dologout',
	),
	2000000 => array(// Cockpit
		'text' => g_l('global', '[cockpit]'),
		'parent' => 0,
		'perm' => 'CAN_SEE_QUICKSTART',
	),
// Cockpit > Display
	array(
		'text' => g_l('javaMenu_global', '[display]'),
		'parent' => 2000000,
		'cmd' => 'home',
		'perm' => 'CAN_SEE_QUICKSTART',
	),
// Cockpit > new Widget
	2020000 => array(
		'text' => g_l('javaMenu_global', '[new_widget]'),
		'parent' => 2000000,
		'perm' => 'CAN_SEE_QUICKSTART',
	),
// Cockpit > new Widget > shortcuts
	array(
		'text' => g_l('javaMenu_global', '[shortcuts]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_sct',
		'perm' => 'CAN_SEE_QUICKSTART',
	),
// Cockpit > new Widget > RSS
	array(
		'text' => g_l('javaMenu_global', '[rss_reader]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_rss',
		'perm' => 'CAN_SEE_QUICKSTART',
	),
	array(// Cockpit > new Widget > messaging
		'text' => g_l('javaMenu_global', '[todo_messaging]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_msg',
		'perm' => 'CAN_SEE_QUICKSTART',
		'hide' => !defined('MESSAGING_SYSTEM')
	),
	array(// Cockpit > new Widget > Shop
		'text' => g_l('javaMenu_global', '[shop_dashboard]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_shp',
		'perm' => 'CAN_SEE_QUICKSTART || NEW_SHOP_ARTICLE || DELETE_SHOP_ARTICLE || EDIT_SHOP_ORDER || DELETE_SHOP_ORDER || EDIT_SHOP_PREFS',
		'hide' => !defined('SHOP_TABLE')
	),
	array(// Cockpit > new Widget > online users
		'text' => g_l('javaMenu_global', '[users_online]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_usr',
		'perm' => 'CAN_SEE_QUICKSTART',
	),
	array(// Cockpit > new Widget > lastmodified
		'text' => g_l('javaMenu_global', '[last_modified]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_mfd',
		'perm' => 'CAN_SEE_QUICKSTART',
	),
	array(// Cockpit > new Widget > unpublished
		'text' => g_l('javaMenu_global', '[unpublished]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_upb',
		'perm' => 'CAN_SEE_QUICKSTART',
	),
	array(// Cockpit > new Widget > my Documents
		'text' => g_l('javaMenu_global', '[my_documents]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_mdc',
		'perm' => 'CAN_SEE_QUICKSTART',
	),
	array(// Cockpit > new Widget > Notepad
		'text' => g_l('javaMenu_global', '[notepad]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_pad',
		'perm' => 'CAN_SEE_QUICKSTART',
	),
	array(
		'text' => g_l('javaMenu_global', '[kv_failedLogins]'),
		'parent' => 2020000,
		'cmd' => 'new_widget_fdl',
		'perm' => 'EDIT_CUSTOMER || NEW_CUSTOMER',
		'enabled' => permissionhandler::hasPerm('CAN_SEE_QUICKSTART'),
		'hide' => !defined('CUSTOMER_TABLE'),
	),
	array(// Cockpit > new Widget > default settings
		'text' => g_l('javaMenu_global', '[default_settings]'),
		'parent' => 2000000,
		'cmd' => 'reset_home',
		'perm' => 'CAN_SEE_QUICKSTART',
	),
	3000000 => array(// Modules
		'text' => g_l('javaMenu_global', '[modules]'),
		'parent' => 0,
	),
	4000000 => array(// Extras
		'text' => g_l('javaMenu_global', '[extras]'),
		'parent' => 0,
	),
	array(// Extras > Dokument-Typen
		'text' => g_l('javaMenu_global', '[document_types]') . '&hellip;',
		'parent' => 4000000,
		'cmd' => 'doctypes',
		'perm' => 'EDIT_DOCTYPE || ADMINISTRATOR',
	),
	array(// Extras > Kategorien
		'text' => g_l('javaMenu_global', '[categories]') . '&hellip;',
		'parent' => 4000000,
		'cmd' => 'editCat',
		'perm' => 'EDIT_KATEGORIE || ADMINISTRATOR',
	),
	array(// Extras > Thumbnails
		'text' => g_l('javaMenu_global', '[thumbnails]') . '&hellip;',
		'parent' => 4000000,
		'cmd' => 'editThumbs',
		'perm' => 'EDIT_THUMBS || ADMINISTRATOR',
	),
	array(// Extras > Metadata fields
		'text' => g_l('javaMenu_global', '[metadata]') . '&hellip;',
		'parent' => 4000000,
		'cmd' => 'editMetadataFields',
		'perm' => 'ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(// separator
		'parent' => 4000000,
		'perm' => 'EDIT_DOCTYPE || EDIT_KATEGORIE || EDIT_THUMBS || ADMINISTRATOR',
	),
	array(// Extras > change password
		'text' => g_l('javaMenu_global', '[change_password]') . '&hellip;',
		'parent' => 4000000,
		'cmd' => 'change_passwd',
		'perm' => 'EDIT_PASSWD || ADMINISTRATOR',
	),
	array(// separator
		'parent' => 4000000,
		'perm' => 'EDIT_PASSWD || ADMINISTRATOR',
	),
	array(// Extras > versioning
		'text' => g_l('javaMenu_global', '[versioning]') . '&hellip;',
		'parent' => 4000000,
		'cmd' => 'versions_wizard',
		'perm' => 'ADMINISTRATOR',
	),
	array(// Extras > versioning-log
		'text' => g_l('javaMenu_global', '[versioning_log]') . '&hellip;',
		'parent' => 4000000,
		'cmd' => 'versioning_log',
		'perm' => 'ADMINISTRATOR',
	),
	array(// separator
		'parent' => 4000000,
		'perm' => 'ADMINISTRATOR',
	),
	4179999 => array(// separator
		'parent' => 4000000,
	),
	4180000 => array(// Extras > Einstellungen
		'text' => g_l('javaMenu_global', '[preferences]'),
		'parent' => 4000000,
	),
	array(
		'text' => g_l('javaMenu_global', '[common]') . '&hellip;',
		'parent' => 4180000,
		'cmd' => 'openPreferences',
		'perm' => 'EDIT_SETTINGS || ADMINISTRATOR',
	),
	array(// separator
		'parent' => 4180000,
		'perm' => 'EDIT_SETTINGS || ADMINISTRATOR',
	),
	5000000 => array(// Help
		'text' => g_l('javaMenu_global', '[help]'),
		'parent' => 0,
	),
	5010000 => array(
		'text' => g_l('javaMenu_global', '[onlinehelp]'),
		'parent' => 5000000,
		'hide' => $seeMode
	),
	array(
		'text' => g_l('javaMenu_global', '[onlinehelp]') . '&hellip;',
		'parent' => $seeMode ? 5000000 : 5010000,
		'cmd' => 'help',
		'perm' => '',
	),
	array(// separator
		'parent' => 5010000,
		'hide' => $seeMode
	),
	array(
		'text' => g_l('javaMenu_global', '[onlinehelp_documentation]') . '&hellip;',
		'parent' => 5010000,
		'cmd' => 'help_documentation',
		'perm' => '',
		'hide' => $seeMode
	),
	array(
		'text' => g_l('javaMenu_global', '[onlinehelp_tagreference]') . '&hellip;',
		'parent' => 5010000,
		'cmd' => 'help_tagreference',
		'perm' => 'CAN_SEE_TEMPLATES || ADMINISTRATOR',
		'hide' => $seeMode
	),
	array(
		'text' => g_l('javaMenu_global', '[onlinehelp_forum]') . '&hellip;',
		'parent' => 5010000,
		'cmd' => 'help_forum',
		'perm' => '',
		'hide' => $seeMode
	),
	array(
		'text' => g_l('javaMenu_global', '[onlinehelp_bugtracker]') . '&hellip;',
		'parent' => 5010000,
		'cmd' => 'help_bugtracker',
		'perm' => '',
		'hide' => $seeMode
	),
	array(// separator
		'parent' => 5010000,
		'hide' => $seeMode
	),
	array(
		'text' => g_l('javaMenu_global', '[onlinehelp_changelog]') . '&hellip;',
		'parent' => 5010000,
		'cmd' => 'help_changelog',
		'perm' => '',
		'hide' => $seeMode
	),
	array(
		'text' => g_l('javaMenu_global', '[sidebar]') . '&hellip;',
		'parent' => 5000000,
		'cmd' => 'openSidebar',
		'perm' => '',
		'hide' => !(SIDEBAR_DISABLED == 0)
	),
	array(
		'text' => g_l('javaMenu_global', '[webEdition_online]') . '&hellip;',
		'parent' => 5000000,
		'cmd' => 'webEdition_online',
		'perm' => '',
	),
	array(// separator
		'parent' => 5000000,
		'perm' => 'ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_global', '[update]') . '&hellip;',
		'parent' => 5000000,
		'cmd' => 'update',
		'perm' => 'ADMINISTRATOR',
	),
	array(// separator
		'parent' => 5000000
	),
	array(
		'text' => g_l('javaMenu_global', '[sysinfo]') . '&hellip;',
		'parent' => 5000000,
		'cmd' => 'sysinfo',
		'perm' => 'ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_global', '[showerrorlog]') . '&hellip;',
		'parent' => 5000000,
		'cmd' => 'showerrorlog',
		'perm' => 'ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_global', '[info]') . '&hellip;',
		'parent' => 5000000,
		'cmd' => 'info',
		'perm' => '',
	)
);

$dtq = we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']);
$GLOBALS['DB_WE']->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);
$pre = '1010102_';
$nr = 0;
if($GLOBALS['DB_WE']->num_rows() && permissionhandler::hasPerm('NO_DOCTYPE')){
	$we_menu[$pre . sprintf('%09d', $nr++)] = array('parent' => 1010100); // separator
}
// File > New > webEdition Document > Doctypes*
while($GLOBALS['DB_WE']->next_record()){
	$we_menu[$pre . sprintf('%09d', $nr++)] = array(
		'text' => str_replace(array(',', '"', '\'',), array(' ', ''), $GLOBALS['DB_WE']->f('DocType')),
		'parent' => 1010100,
		'cmd' => 'new_dtPage' . $GLOBALS['DB_WE']->f('ID'),
		'perm' => 'NEW_WEBEDITIONSITE || ADMINISTRATOR',
	);
}


if(defined('OBJECT_TABLE')){
	// object from which class
	$ac = we_users_util::getAllowedClasses($GLOBALS['DB_WE']);
	if($ac){
		$GLOBALS['DB_WE']->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ' . ($ac ? ' WHERE ID IN(' . implode(',', $ac) . ') ' : '') . 'ORDER BY Text');
		$pre = '1010200_';
		$nr = 0;
		while($GLOBALS['DB_WE']->next_record()){
			$we_menu[$pre . sprintf('%09d', $nr++)] = array(
				'text' => str_replace(array('"', '\''), '', $GLOBALS['DB_WE']->f('Text')),
				'parent' => 1010200,
				'cmd' => 'new_ClObjectFile' . $GLOBALS['DB_WE']->f('ID'),
				'perm' => 'NEW_OBJECTFILE || ADMINISTRATOR',
			);
		}
		if($nr){
			$we_menu[1010200]['enabled'] = 1;
		}
	}
}

// order all modules
$allModules = we_base_moduleInfo::getAllModules();
we_base_moduleInfo::orderModuleArray($allModules);

//$moduleList = 'schedpro|';
$pre = '3000000_';
$nr = 0;
foreach($allModules as $m){
	if(we_base_moduleInfo::showModuleInMenu($m['name'])){
		// workarround (old module names) for not installed Modules WIndow
		/* 	if($m['name'] === 'customer'){
		  $moduleList .= 'customerpro|';
		  }
		  $moduleList .= $m['name'] . '|'; */
		$we_menu[$pre . sprintf('%09d', $nr++)] = array(
			'text' => $m['text'] . '&hellip;',
			'parent' => 3000000,
			'cmd' => $m['name'] . '_edit_ifthere',
			'perm' => isset($m['perm']) ? $m['perm'] : '',
		);
	}
}
// Extras > Tools > Custom tools
$_tools = we_tool_lookup::getAllTools(true, false);

foreach($_tools as $_k => $_tool){
	$we_menu[4040000 + $_k] = array(
		'text' => $_tool['text'] . '&hellip;',
		'parent' => 4000000,
		'cmd' => 'tool_' . $_tool['name'] . '_edit',
		'perm' => $_tool['startpermission'] . ' || ADMINISTRATOR',
	);
}

$_activeIntModules = we_base_moduleInfo::getIntegratedModules(true);
we_base_moduleInfo::orderModuleArray($_activeIntModules);

if($_activeIntModules){
	$z = 4184100;
	foreach($_activeIntModules as $modInfo){
		if($modInfo['hasSettings']){
			$we_menu[$z++] = array(
				'text' => $modInfo['text'] . '&hellip;',
				'parent' => 4180000,
				'cmd' => 'edit_settings_' . $modInfo['name'],
				'perm' => isset($modInfo['perm']) ? $modInfo['perm'] : '',
			);
		}
	}
}
ksort($we_menu); //TODO: SORT_NATURAL requires PHP 5.4
return $we_menu;
