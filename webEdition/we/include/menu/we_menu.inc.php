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
$we_menu = [
	'file_new' => [// New
		'text' => g_l('javaMenu_global', '[new]'),
	],
	'file_new_wedoc' => [// New > webEdition Document
		'text' => g_l('javaMenu_global', '[webEdition_page]'),
		'parent' => 'file_new',
		'perm' => 'NEW_WEBEDITIONSITE',
	], [// New > webEdition Document > empty page
		'text' => g_l('javaMenu_global', '[empty_page]'),
		'parent' => 'file_new_wedoc',
		'cmd' => 'new_webEditionPage',
		'perm' => 'NO_DOCTYPE',
	], [
		'parent' => 'file_new_wedoc',
		'hide' => !$seeMode,
	],
	'file_new_weobj' => [// New > Object
		'text' => g_l('javaMenu_object', '[object]'),
		'parent' => 'file_new',
		'perm' => 'NEW_OBJECTFILE',
		'hide' => !defined('OBJECT_TABLE')
	],
	'file_new_media' => [
		'text' => g_l('javaMenu_global', '[media]'),
		'parent' => 'file_new',
		'perm' => 'ADMINISTRATOR || NEW_GRAFIK || NEW_FLASH || NEW_SONSTIGE',
	], [// File > Image
		'text' => g_l('javaMenu_global', '[image]'),
		'parent' => 'file_new_media',
		'cmd' => 'new_image',
		'perm' => 'NEW_GRAFIK',
	], [// New > Other > Other (Binary)
		'text' => g_l('javaMenu_global', '[pdf]'),
		'parent' => 'file_new_media',
		'cmd' => 'new_binary_document',
		'perm' => 'NEW_SONSTIGE',
	], [// New > Other > Flash
		'text' => g_l('javaMenu_global', '[flash_movie]'),
		'parent' => 'file_new_media',
		'cmd' => 'new_flash_movie',
		'perm' => 'NEW_FLASH',
	], [// New Other > video
		'text' => g_l('contentTypes', '[' . we_base_ContentTypes::VIDEO . ']'),
		'parent' => 'file_new_media',
		'cmd' => 'new_video_movie',
		'perm' => 'NEW_FLASH',
	], [// New Other > audio
		'text' => g_l('contentTypes', '[' . we_base_ContentTypes::AUDIO . ']'),
		'parent' => 'file_new_media',
		'cmd' => 'new_audio_audio',
		'perm' => 'NEW_SONSTIGE',
	],
	'file_new_other' => [// New > Other
		'text' => g_l('javaMenu_global', '[other]'),
		'parent' => 'file_new',
		'perm' => 'ADMINISTRATOR || NEW_HTML || NEW_JS || NEW_CSS || NEW_TEXT || NEW_HTACCESS || NEW_SONSTIGE',
	], [// New > Other > html
		'text' => g_l('javaMenu_global', '[html_page]'),
		'parent' => 'file_new_other',
		'cmd' => 'new_html_page',
		'perm' => 'NEW_HTML',
	], [// New > Other > Javascript
		'text' => g_l('javaMenu_global', '[javascript]'),
		'parent' => 'file_new_other',
		'cmd' => 'new_javascript',
		'perm' => 'NEW_JS',
	], [// New > Other > CSS
		'text' => g_l('javaMenu_global', '[css_stylesheet]'),
		'parent' => 'file_new_other',
		'cmd' => 'new_css_stylesheet',
		'perm' => 'NEW_CSS',
	], [// New > Other > Text
		'text' => g_l('javaMenu_global', '[text_plain]'),
		'parent' => 'file_new_other',
		'cmd' => 'new_text_plain',
		'perm' => 'NEW_TEXT',
	], [// New > Other > XML
		'text' => g_l('javaMenu_global', '[text_xml]'),
		'parent' => 'file_new_other',
		'cmd' => 'new_text_xml',
		'perm' => 'NEW_TEXT',
	], [// New > Other > htaccess
		'text' => g_l('javaMenu_global', '[htaccess]'),
		'parent' => 'file_new_other',
		'cmd' => 'new_text_htaccess',
		'perm' => 'NEW_HTACCESS',
	], [// New > Other > Other (Binary)
		'text' => g_l('javaMenu_global', '[other_files]'),
		'parent' => 'file_new_other',
		'cmd' => 'new_binary_document',
		'perm' => 'NEW_SONSTIGE',
	], [
		'parent' => 'file_new',
		'hide' => $seeMode
	],
	'file_new_dir' => [// New > Directory
		'text' => g_l('javaMenu_global', '[directory]'),
		'parent' => 'file_new',
		'hide' => $seeMode,
		'perm' => 'NEW_DOC_FOLDER || NEW_TEMP_FOLDER || NEW_OBJECTFILE_FOLDER || NEW_COLLECTION_FOLDER',
	], [// New > Directory > Document
		'text' => g_l('javaMenu_global', '[document_directory]'),
		'parent' => 'file_new_dir',
		'cmd' => 'new_document_folder',
		'perm' => 'NEW_DOC_FOLDER',
		'hide' => $seeMode
	], [// New > Directory > Template
		'text' => g_l('javaMenu_global', '[template_directory]'),
		'parent' => 'file_new_dir',
		'cmd' => 'new_template_folder',
		'perm' => 'NEW_TEMP_FOLDER',
		'hide' => $seeMode
	], [// New > directory > objectfolder
		'text' => g_l('javaMenu_object', '[object_directory]'),
		'parent' => 'file_new_dir',
		'cmd' => 'new_objectfile_nested_folder',
		'perm' => 'NEW_OBJECTFILE_FOLDER',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	], [// New > Directory > Collection
		'text' => g_l('javaMenu_global', '[collection_directory]'),
		'parent' => 'file_new_dir',
		'cmd' => 'new_collection_folder',
		'perm' => 'NEW_COLLECTION_FOLDER',
		'hide' => $seeMode || !we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION)
	], ['parent' => 'file_new',
		'perm' => 'NEW_OBJECT || NEW_TEMPLATE',
		'hide' => $seeMode
	], [// New > Template
		'text' => g_l('javaMenu_global', '[template]'),
		'parent' => 'file_new',
		'cmd' => 'new_template',
		'perm' => 'NEW_TEMPLATE',
		'hide' => $seeMode
	], [// New > Class
		'text' => g_l('javaMenu_object', '[class]'),
		'parent' => 'file_new',
		'cmd' => 'new_object',
		'perm' => 'NEW_OBJECT',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	], [
		'parent' => 'file_new',
		'perm' => 'NEW_COLLECTION',
		'hide' => $seeMode || !we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION)
	], [// File > COLLECTION
		'text' => g_l('javaMenu_global', '[collection]'),
		'parent' => 'file_new',
		'cmd' => 'new_collection',
		'perm' => 'NEW_COLLECTION',
		'hide' => !we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION)
	],
	'file' => [// File
		'text' => g_l('javaMenu_global', '[file]'),
	],
	/* 	$we_menu[1011100]['parent'] = 'file_new'; // separator
	  // New > Wizards
	  'text'=> g_l('javaMenu_global', '[wizards]') . '&hellip;',
	  'parent'=> 'file_new',

	  // New > Wizard > First Steps Wizard
	  'text'=> g_l('javaMenu_global', '[first_steps_wizard]'),
	  'parent'=> 1011200,
	  'cmd'=> 'openFirstStepsWizardMasterTemplate',
	  'perm'=> 'ADMINISTRATOR',

	  $we_menu[1020000]['parent'] = 'file'; // separator
	 */
	'file_open' => [// File > Open
		'text' => g_l('javaMenu_global', '[open]'),
		'parent' => 'file',
	], [// File > Open > Document
		'text' => g_l('javaMenu_global', '[open_document]') . '&hellip;',
		'parent' => 'file_open',
		'cmd' => 'open_document',
		'perm' => 'CAN_SEE_DOCUMENTS',
	], [// File > open > Object
		'text' => g_l('javaMenu_object', '[open_object]') . '&hellip;',
		'parent' => 'file_open',
		'cmd' => 'open_objectFile',
		'perm' => 'CAN_SEE_OBJECTFILES',
		'hide' => !defined('OBJECT_TABLE')
	], [// File > Open > Collection
		'text' => g_l('javaMenu_global', '[collection]') . '&hellip;',
		'parent' => 1030000,
		'cmd' => 'open_collection',
		'perm' => 'CAN_SEE_COLLECTIONS',
		'hide' => !we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION)
	], ['parent' => 'file_open',
		'perm' => 'CAN_SEE_TEMPLATES || CAN_SEE_OBJECTS',
		'hide' => $seeMode
	], [// File > Open > Template
		'text' => g_l('javaMenu_global', '[open_template]') . '&hellip;',
		'parent' => 'file_open',
		'cmd' => 'open_template',
		'perm' => 'CAN_SEE_TEMPLATES',
		'hide' => $seeMode
	], [// File > Open > Class
		'text' => g_l('javaMenu_object', '[open_class]') . '&hellip;',
		'parent' => 'file_open',
		'cmd' => 'open_object',
		'perm' => 'CAN_SEE_OBJECTS',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	],
	'file_delete' => [// File > Delete
		'text' => g_l('javaMenu_global', '[delete]') . ($seeMode ? '&hellip;' : ''),
		'parent' => 'file',
		'cmd' => $seeMode ? 'we_selector_delete' : '',
		'perm' => $seeMode ? 'DELETE_DOCUMENT' : 'DELETE_DOCUMENT || DELETE_OBJECTFILE || DELETE_TEMPLATE || DELETE_OBJECT',
	], [// File > Delete > Documents
		'text' => g_l('javaMenu_global', '[documents]'),
		'parent' => 'file_delete',
		'cmd' => 'delete_documents',
		'perm' => 'DELETE_DOCUMENT',
		'hide' => $seeMode,
	], [// File > Delete > Objects
		'text' => g_l('javaMenu_object', '[objects]'),
		'parent' => 'file_delete',
		'cmd' => 'delete_objectfile',
		'perm' => 'DELETE_OBJECTFILE',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	], [
		'parent' => 'file_delete',
		'perm' => 'CAN_SEE_TEMPLATES || CAN_SEE_OBJECTS',
		'hide' => $seeMode
	], [// File > Delete > Templates
		'text' => g_l('javaMenu_global', '[templates]'),
		'parent' => 'file_delete',
		'cmd' => 'delete_templates',
		'perm' => 'DELETE_TEMPLATE',
		'hide' => $seeMode,
	], [// File > Delete > Classes
		'text' => g_l('javaMenu_object', '[classes]'),
		'parent' => 'file_delete',
		'cmd' => 'delete_object',
		'perm' => 'DELETE_OBJECT',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	], [
		'parent' => 'file_delete',
		'perm' => 'DELETE_COLLECTION',
		'hide' => $seeMode
	], [// File > Delete > Collection
		'text' => g_l('global', '[vfile]'),
		'parent' => 'file_delete',
		'cmd' => 'delete_collections',
		'perm' => 'DELETE_COLLECTION',
		'hide' => $seeMode || !we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION),
	],
	'file_mv' => [// File > Move
		'text' => g_l('javaMenu_global', '[move]'),
		'parent' => 'file',
		'hide' => $seeMode,
		'perm' => 'MOVE_DOCUMENT || MOVE_OBJECTFILE || MOVE_TEMPLATE',
	], [// File > Move > Documents
		'text' => g_l('javaMenu_global', '[documents]'),
		'parent' => 'file_mv',
		'cmd' => 'move_documents',
		'perm' => 'MOVE_DOCUMENT',
		'hide' => $seeMode,
	], [// File > move > objects
		'text' => g_l('javaMenu_object', '[objects]'),
		'parent' => 'file_mv',
		'cmd' => 'move_objectfile',
		'perm' => 'MOVE_OBJECTFILE',
		'hide' => !defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL)
	], [
		'parent' => 'file_mv',
		'perm' => 'MOVE_TEMPLATE',
		'hide' => $seeMode
	],
	[// File > Move > Templates
		'text' => g_l('javaMenu_global', '[templates]'),
		'parent' => 'file_mv',
		'cmd' => 'move_templates',
		'perm' => 'MOVE_TEMPLATE',
		'hide' => $seeMode,
	],
	'file_addcoll' => [// File > add to collection
		'text' => g_l('javaMenu_global', '[add_to_collection]'),
		'parent' => 'file',
		'hide' => $seeMode,
		'perm' => 'SAVE_COLLECTION',
		'hide' => $seeMode || !we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION)
	], [// File > add to collection > documents
		'text' => g_l('javaMenu_global', '[documents]'),
		'parent' => 'file_addcoll',
		'cmd' => 'add_documents_to_collection',
		'perm' => 'SAVE_COLLECTION',
		'hide' => $seeMode || !we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION),
	], [/// File > add to collection > objects
		'text' => g_l('javaMenu_object', '[objects]'),
		'parent' => 'file_addcoll',
		'cmd' => 'add_objectfiles_to_collection',
		'perm' => 'SAVE_COLLECTION',
		'hide' => true, //!defined('OBJECT_TABLE') || ($_SESSION['weS']['we_mode'] != we_base_constants::MODE_NORMAL) || !we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION)
	], [
		'parent' => 'file'
	],
	/*
	  'text' => g_l('javaMenu_glossary', '[glossary_check]'),
	  'parent' => 'file',
	  'cmd' => 'glossary_check',
	  'hide' => !(defined('GLOSSARY_TABLE'))
	  , */
	[// File > Delete Active Document
		'text' => g_l('javaMenu_global', '[delete_active_document]'),
		'parent' => 'file',
		'cmd' => 'delete_single_document_question',
		'perm' => 'DELETE_DOCUMENT || DELETE_OBJECTFILE || DELETE_TEMPLATE || DELETE_OBJECT',
		'hide' => $seeMode
	],
	/* / File > Close
	  'text' => g_l('javaMenu_global', '[close_single_document]'),
	  'parent' => 'file',
	  'cmd' => 'close_document',
	  , */
	[// File > Close All
		'text' => g_l('javaMenu_global', '[close_all_documents]'),
		'parent' => 'file',
		'cmd' => 'close_all_documents',
		'hide' => $seeMode
	], [// File > Close All But this
		'text' => g_l('javaMenu_global', '[close_all_but_active_document]'),
		'parent' => 'file',
		'cmd' => 'close_all_but_active_document',
		'hide' => $seeMode
	], [
		'parent' => 'file'
	], [// File > unpublished pages
		'text' => g_l('javaMenu_global', '[unpublished_pages]') . '&hellip;',
		'parent' => 'file',
		'cmd' => 'openUnpublishedPages',
		'perm' => 'CAN_SEE_DOCUMENTS',
	], [// File > unpublished objects
		'text' => g_l('javaMenu_object', '[unpublished_objects]') . '&hellip;',
		'parent' => 'file',
		'cmd' => 'openUnpublishedObjects',
		'perm' => 'CAN_SEE_OBJECTFILES',
		'hide' => !defined('OBJECT_TABLE')
	], [// File > Search
		'text' => g_l('javaMenu_global', '[search]') . '&hellip;',
		'parent' => 'file',
		'cmd' => 'tool_weSearch_edit',
	], [
		'parent' => 'file',
	],
	'file_imex' => [// File > Import/Export
		'text' => g_l('javaMenu_global', '[import_export]'),
		'parent' => 'file',
		'perm' => 'GENERICXML_EXPORT || CSV_EXPORT || FILE_IMPORT || SITE_IMPORT || GENERICXML_IMPORT || CSV_IMPORT || WXML_IMPORT',
	], [// File > Import/Export > Import
		'text' => g_l('javaMenu_global', '[import]') . '&hellip;',
		'cmd' => 'import',
		'parent' => 'file_imex',
		'perm' => 'FILE_IMPORT || SITE_IMPORT || GENERICXML_IMPORT || CSV_IMPORT || WXML_IMPORT',
	], [// File > Import/Export > Export
		'text' => g_l('javaMenu_global', '[export]') . '&hellip;',
		'cmd' => 'export',
		'parent' => 'file_imex',
		'perm' => 'GENERICXML_EXPORT || CSV_EXPORT',
	],
	'file_backup' => [// File > Backup
		'text' => g_l('javaMenu_global', '[backup]'),
		'parent' => 'file',
		'hide' => $seeMode,
		'perm' => 'BACKUPLOG || IMPORT || EXPORT || EXPORTNODOWNLOAD',
	], [// File > Backup > make
		'text' => g_l('javaMenu_global', '[make_backup]') . '&hellip;',
		'parent' => $seeMode ? 'file' : 'file_backup',
		'cmd' => 'make_backup',
		'perm' => 'EXPORT || EXPORTNODOWNLOAD',
	], [// File > Backup > recover
		'text' => g_l('javaMenu_global', '[recover_backup]') . '&hellip;',
		'parent' => 'file_backup',
		'cmd' => 'recover_backup',
		'perm' => 'IMPORT',
		'hide' => $seeMode
	], [// File > Backup > view Log
		'text' => g_l('javaMenu_global', '[view_backuplog]') . '&hellip;',
		'parent' => $seeMode ? 'file' : 'file_backup',
		'cmd' => 'view_backuplog',
		'perm' => 'BACKUPLOG',
	], [// File > rebuild
		'text' => g_l('javaMenu_global', '[rebuild]') . '&hellip;',
		'parent' => 'file',
		'cmd' => 'rebuild',
		'perm' => 'REBUILD',
	], [// File > Browse server
		'text' => g_l('javaMenu_global', '[browse_server]') . '&hellip;',
		'parent' => 'file',
		'cmd' => 'browse_server',
		'perm' => 'BROWSE_SERVER',
		'hide' => $seeMode,
	], [
		'parent' => 'file',
		'perm' => 'BROWSE_SERVER',
		'hide' => $seeMode,
	], [// File > Quit
		'text' => g_l('javaMenu_global', '[quit]'),
		'parent' => 'file',
		'cmd' => 'dologout',
	],
	'cockpit' => [// Cockpit
		'text' => g_l('global', '[cockpit]'),
		'perm' => 'CAN_SEE_QUICKSTART',
	], [// Cockpit > Display
		'text' => g_l('javaMenu_global', '[display]'),
		'parent' => 'cockpit',
		'cmd' => 'home',
		'perm' => 'CAN_SEE_QUICKSTART',
	],
	'cockpit_new' => [// Cockpit > new Widget
		'text' => g_l('javaMenu_global', '[new_widget]'),
		'parent' => 'cockpit',
		'perm' => 'CAN_SEE_QUICKSTART',
	], [// Cockpit > new Widget > shortcuts
		'text' => g_l('javaMenu_global', '[shortcuts]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'sct'],
		'perm' => 'CAN_SEE_QUICKSTART',
	], [// Cockpit > new Widget > RSS
		'text' => g_l('javaMenu_global', '[rss_reader]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'rss'],
		'perm' => 'CAN_SEE_QUICKSTART',
	], [// Cockpit > new Widget > messaging
		'text' => g_l('javaMenu_global', '[todo_messaging]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'msg'],
		'perm' => 'CAN_SEE_QUICKSTART',
		'hide' => !defined('MESSAGING_SYSTEM')
	], [// Cockpit > new Widget > Shop
		'text' => g_l('javaMenu_global', '[shop_dashboard]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'shp'],
		'perm' => 'CAN_SEE_QUICKSTART || NEW_SHOP_ARTICLE || DELETE_SHOP_ARTICLE || EDIT_SHOP_ORDER || DELETE_SHOP_ORDER || EDIT_SHOP_PREFS',
		'hide' => !defined('SHOP_TABLE')
	], [// Cockpit > new Widget > online users
		'text' => g_l('javaMenu_global', '[users_online]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'usr'],
		'perm' => 'CAN_SEE_QUICKSTART',
	], [// Cockpit > new Widget > lastmodified
		'text' => g_l('javaMenu_global', '[last_modified]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'mfd'],
		'perm' => 'CAN_SEE_QUICKSTART',
	], [// Cockpit > new Widget > unpublished
		'text' => g_l('javaMenu_global', '[unpublished]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'upb'],
		'perm' => 'CAN_SEE_QUICKSTART',
	], [// Cockpit > new Widget > my Documents
		'text' => g_l('javaMenu_global', '[my_documents]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'mdc'],
		'perm' => 'CAN_SEE_QUICKSTART',
	], [// Cockpit > new Widget > Notepad
		'text' => g_l('javaMenu_global', '[notepad]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'pad'],
		'perm' => 'CAN_SEE_QUICKSTART',
	], [
		'text' => g_l('javaMenu_global', '[kv_failedLogins]'),
		'parent' => 'cockpit_new',
		'cmd' => ['new_widget', 'fdl'],
		'perm' => 'EDIT_CUSTOMER || NEW_CUSTOMER',
		'hide' => !defined('CUSTOMER_TABLE') || !permissionhandler::hasPerm('CAN_SEE_QUICKSTART'),
	], [// Cockpit > new Widget > default settings
		'text' => g_l('javaMenu_global', '[default_settings]'),
		'parent' => 'cockpit',
		'cmd' => 'reset_home',
		'perm' => 'CAN_SEE_QUICKSTART',
	],
	'modules' => [
		'text' => g_l('javaMenu_global', '[modules]'),
	],
	'extras' => [
		'text' => g_l('javaMenu_global', '[extras]'),
	], [// Extras > Dokument-Typen
		'text' => g_l('javaMenu_global', '[document_types]') . '&hellip;',
		'parent' => 'extras',
		'cmd' => 'doctypes',
		'perm' => 'EDIT_DOCTYPE',
	], [// Extras > Kategorien
		'text' => g_l('javaMenu_global', '[categories]') . '&hellip;',
		'parent' => 'extras',
		'cmd' => 'editCat',
		'perm' => 'EDIT_KATEGORIE',
	], [// Extras > Thumbnails
		'text' => g_l('javaMenu_global', '[thumbnails]') . '&hellip;',
		'parent' => 'extras',
		'cmd' => 'editThumbs',
		'perm' => 'EDIT_THUMBS',
	], [// Extras > Metadata fields
		'text' => g_l('javaMenu_global', '[metadata]') . '&hellip;',
		'parent' => 'extras',
		'cmd' => 'editMetadataFields',
		'perm' => 'ADMINISTRATOR',
		'hide' => $seeMode
	], [
		'parent' => 'extras',
		'perm' => 'EDIT_DOCTYPE || EDIT_KATEGORIE || EDIT_THUMBS',
	], [// Extras > change password
		'text' => g_l('javaMenu_global', '[change_password]') . '&hellip;',
		'parent' => 'extras',
		'cmd' => 'change_passwd',
		'perm' => 'EDIT_PASSWD',
	], ['parent' => 'extras',
		'perm' => 'EDIT_PASSWD',
	], [// Extras > versioning
		'text' => g_l('javaMenu_global', '[versioning]') . '&hellip;',
		'parent' => 'extras',
		'cmd' => 'versions_wizard',
		'perm' => 'ADMINISTRATOR',
	], [// Extras > versioning-log
		'text' => g_l('javaMenu_global', '[versioning_log]') . '&hellip;',
		'parent' => 'extras',
		'cmd' => 'versioning_log',
		'perm' => 'ADMINISTRATOR',
	], [
		'parent' => 'extras',
		'perm' => 'ADMINISTRATOR',
	], [
		'text' => g_l('javaMenu_global', '[common]') . '&hellip;',
		'parent' => 'settings',
		'cmd' => 'openPreferences',
		'perm' => 'EDIT_SETTINGS',
	], [
		'parent' => 'settings',
		'perm' => 'EDIT_SETTINGS',
	],
	'help' => [
		'text' => g_l('javaMenu_global', '[help]'),
	],
	'online-help' => [
		'text' => g_l('javaMenu_global', '[onlinehelp]'),
		'parent' => 'help',
		'hide' => $seeMode
	], [
		'text' => g_l('javaMenu_global', '[onlinehelp_documentation]') . '&hellip;',
		'parent' => 'online-help',
		'cmd' => 'help_documentation',
		'hide' => $seeMode
	], [
		'text' => g_l('javaMenu_global', '[onlinehelp_tagreference]') . '&hellip;',
		'parent' => 'online-help',
		'cmd' => 'help_tagreference',
		'perm' => 'CAN_SEE_TEMPLATES',
		'hide' => $seeMode
	], [
		'text' => g_l('javaMenu_global', '[onlinehelp_forum]') . '&hellip;',
		'parent' => 'online-help',
		'cmd' => 'help_forum',
		'hide' => $seeMode
	], [
		'text' => g_l('javaMenu_global', '[onlinehelp_bugtracker]') . '&hellip;',
		'parent' => 'online-help',
		'cmd' => 'help_bugtracker',
		'hide' => $seeMode
	], [
		'parent' => 'online-help',
		'hide' => $seeMode
	], [
		'text' => g_l('javaMenu_global', '[onlinehelp_changelog]') . '&hellip;',
		'parent' => 'online-help',
		'cmd' => 'help_changelog',
		'hide' => $seeMode
	], [
		'text' => g_l('javaMenu_global', '[sidebar]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'openSidebar',
		'hide' => !(SIDEBAR_DISABLED == 0)
	], [
		'text' => g_l('javaMenu_global', '[webEdition_online]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'webEdition_online',
	], ['parent' => 'help',
		'perm' => 'ADMINISTRATOR',
	], ['text' => g_l('javaMenu_global', '[update]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'update',
		'perm' => 'ADMINISTRATOR',
	], ['parent' => 'help'
	], ['text' => g_l('javaMenu_global', '[sysinfo]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'sysinfo',
		'perm' => 'ADMINISTRATOR',
	], [
		'text' => g_l('javaMenu_global', '[showerrorlog]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'showerrorlog',
		'perm' => 'ADMINISTRATOR',
	], [
		'text' => g_l('javaMenu_global', '[info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info',
	]
];

$dtq = we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']);
$GLOBALS['DB_WE']->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where'] . ' LIMIT 95');

if($GLOBALS['DB_WE']->num_rows() && permissionhandler::hasPerm('NO_DOCTYPE')){
	$we_menu[] = ['parent' => 'file_new_wedoc']; // separator
}
// New > webEdition Document > Doctypes*
while($GLOBALS['DB_WE']->next_record()){
	$we_menu[] = [
		'text' => str_replace([',', '"', '\'',], [' ', ''], $GLOBALS['DB_WE']->f('DocType')),
		'parent' => 'file_new_wedoc',
		'cmd' => ['new_dtPage', $GLOBALS['DB_WE']->f('ID')],
		'perm' => 'NEW_WEBEDITIONSITE',
	];
}


if(defined('OBJECT_TABLE')){
	// object from which class
	$ac = we_users_util::getAllowedClasses($GLOBALS['DB_WE']);
	if($ac){
		$GLOBALS['DB_WE']->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ' . ($ac ? ' WHERE ID IN(' . implode(',', $ac) . ') ' : '') . 'ORDER BY Text LIMIT 95');

		if($GLOBALS['DB_WE']->num_rows()){
			while($GLOBALS['DB_WE']->next_record()){
				$we_menu[] = [
					'text' => str_replace(['"', '\''], '', $GLOBALS['DB_WE']->f('Text')),
					'parent' => 'file_new_weobj',
					'cmd' => ['new_ClObjectFile', $GLOBALS['DB_WE']->f('ID')],
					'perm' => 'NEW_OBJECTFILE',
				];
			}
		} else {
			$we_menu['file_new_weobj']['hide'] = 1;
		}
	}
}

// order all modules
$allModules = we_base_moduleInfo::getAllModules();
we_base_moduleInfo::orderModuleArray($allModules);

//$moduleList = 'schedpro|';
foreach($allModules as $m){
	if(we_base_moduleInfo::showModuleInMenu($m['name'])){
		$we_menu[] = [
			'text' => $m['text'] . '&hellip;',
			'parent' => 'modules',
			'cmd' => $m['name'] . '_edit_ifthere',
			'perm' => isset($m['perm']) ? $m['perm'] : '',
		];
	}
}
// Extras > Tools > Custom tools
$tools = we_tool_lookup::getAllTools(true, false);

foreach($tools as $tool){
	$we_menu[] = [
		'text' => ($tool['text'] === 'toolfactory' ? g_l('javaMenu_global', '[toolfactory]') : $tool['text']) . '&hellip;',
		'parent' => 'extras',
		'cmd' => 'tool_' . $tool['name'] . '_edit',
		'perm' => $tool['startpermission'],
	];
}

$activeIntModules = we_base_moduleInfo::getIntegratedModules(true);
we_base_moduleInfo::orderModuleArray($activeIntModules);

//add settings
$we_menu[] = ['parent' => 'extras',];
$we_menu['settings'] = [// Extras > Einstellungen
	'text' => g_l('javaMenu_global', '[preferences]'),
	'parent' => 'extras',
];

if($activeIntModules){
	foreach($activeIntModules as $modInfo){
		if($modInfo['hasSettings']){
			$we_menu[] = [
				'text' => $modInfo['text'] . '&hellip;',
				'parent' => 'settings',
				'cmd' => 'edit_settings_' . $modInfo['name'],
				'perm' => isset($modInfo['perm']) ? $modInfo['perm'] : '',
			];
		}
	}
}
return $we_menu;
