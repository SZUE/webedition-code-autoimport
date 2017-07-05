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
return [
	we_base_moduleInfo::USERS => [
		'name' => we_base_moduleInfo::USERS,
		'perm' => 'NEW_USER || NEW_GROUP || SAVE_USER || SAVE_GROUP || DELETE_USER || DELETE_GROUP || ADMINISTRATOR',
		'text' => g_l('javaMenu_moduleInformation', '[users][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[users][text_short]'),
		'icon' => 'fa-user',
		'inModuleMenu' => true,
		'alwaysActive' => true,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => ''
	],
	we_base_moduleInfo::CUSTOMER => [
		'name' => we_base_moduleInfo::CUSTOMER,
		'perm' => 'SHOW_CUSTOMER_ADMIN || DELETE_CUSTOMER || EDIT_CUSTOMER || NEW_CUSTOMER || ADMINISTRATOR',
		'text' => g_l('javaMenu_moduleInformation', '[customer][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[customer][text_short]'),
		'icon' => 'fa-users',
		'inModuleMenu' => true,
		'alwaysActive' => false,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => 'shop',
		'tables' => ['CUSTOMER_TABLE'],
	],
	we_base_moduleInfo::NAVIGATION => [
		'name' => we_base_moduleInfo::NAVIGATION,
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'text' => g_l('javaMenu_global', '[navigation]'),
		'text_short' => g_l('javaMenu_global', '[navigation]'),
		'icon' => 'fa-compass',
		'inModuleMenu' => true,
		'alwaysActive' => true,
		'hasSettings' => '',
		'dependson' => '',
		'childmodule' => '',
		'tables' => [],
	],
	we_base_moduleInfo::COLLECTION => [
		'name' => we_base_moduleInfo::COLLECTION,
		'perm' => 'CAN_SEE_COLLECTIONS || DELETE_COLLECTIONS || EDIT_COLLECTIONS || NEW_COLLECTIONS || ADMINISTRATOR',
		'text' => g_l('javaMenu_moduleInformation', '[collection][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[collection][text_short]'),
		'icon' => '',
		'inModuleMenu' => false,
		'alwaysActive' => false,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['VFILE_TABLE'],
	],
	we_base_moduleInfo::SHOP => [
		'name' => we_base_moduleInfo::SHOP,
		'text' => g_l('javaMenu_moduleInformation', '[shop][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[shop][text_short]'),
		'perm' => 'NEW_SHOP_ARTICLE || DELETE_SHOP_ARTICLE || EDIT_SHOP_ORDER || DELETE_SHOP_ORDER || EDIT_SHOP_PREFS || ADMINISTRATOR',
		'icon' => 'fa-shopping-cart',
		'inModuleMenu' => true,
		'alwaysActive' => false,
		'hasSettings' => true,
		'dependson' => 'customer',
		'childmodule' => '',
		'tables' => ['SHOP_TABLE', 'SHOP_ORDER_DATES_TABLE', 'SHOP_ORDER_DOCUMENT_TABLE', 'SHOP_ORDER_ITEM_TABLE', 'SHOP_ORDER_TABLE']
	],
	we_base_moduleInfo::SCHEDULER => ['name' => we_base_moduleInfo::SCHEDULER,
		'text' => g_l('javaMenu_moduleInformation', '[schedule][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[schedule][text_short]'),
		'icon' => '',
		'inModuleMenu' => false,
		'alwaysActive' => false,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['SCHEDULE_TABLE'],
	],
	we_base_moduleInfo::EDITOR => [
		'name' => we_base_moduleInfo::EDITOR,
		'text' => g_l('javaMenu_moduleInformation', '[editor][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[editor][text_short]'),
		'perm' => '',
		'icon' => '',
		'inModuleMenu' => false,
		'alwaysActive' => true,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => '',
		'tables' => []
	],
	we_base_moduleInfo::OBJECT => [
		'name' => we_base_moduleInfo::OBJECT,
		'text' => g_l('javaMenu_moduleInformation', '[object][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[object][text_short]'),
		'icon' => '',
		'inModuleMenu' => false,
		'alwaysActive' => false,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['OBJECT_FILES_TABLE', 'OBJECT_TABLE']
	],
	we_base_moduleInfo::WORKFLOW => [
		'name' => we_base_moduleInfo::WORKFLOW,
		'text' => g_l('javaMenu_moduleInformation', '[workflow][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[workflow][text_short]'),
		'perm' => 'NEW_WORKFLOW || DELETE_WORKFLOW || EDIT_WORKFLOW || EMPTY_LOG || ADMINISTRATOR',
		'icon' => 'fa-gears',
		'alwaysActive' => false,
		'inModuleMenu' => true,
		'hasSettings' => false,
		'childmodule' => ''
	],
	we_base_moduleInfo::NEWSLETTER => [
		'name' => we_base_moduleInfo::NEWSLETTER,
		'text' => g_l('javaMenu_moduleInformation', '[newsletter][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[newsletter][text_short]'),
		'perm' => 'NEW_NEWSLETTER || DELETE_NEWSLETTER || EDIT_NEWSLETTER || SEND_NEWSLETTER || SEND_TEST_EMAIL || ADMINISTRATOR',
		'icon' => 'fa-newspaper-o',
		'inModuleMenu' => true,
		'alwaysActive' => false,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['NEWSLETTER_BLOCK_TABLE', 'NEWSLETTER_CONFIRM_TABLE', 'NEWSLETTER_GROUP_TABLE', 'NEWSLETTER_LOG_TABLE', 'NEWSLETTER_TABLE']
	],
	we_base_moduleInfo::BANNER => [
		'name' => we_base_moduleInfo::BANNER,
		'text' => g_l('javaMenu_moduleInformation', '[banner][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[banner][text_short]'),
		'perm' => 'NEW_BANNER || DELETE_BANNER || EDIT_BANNER || ADMINISTRATOR',
		'icon' => 'fa-flag',
		'inModuleMenu' => true,
		'alwaysActive' => false,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['BANNER_CLICKS_TABLE', 'BANNER_TABLE', 'BANNER_VIEWS_TABLE']
	],
	we_base_moduleInfo::EXPORT => [
		'name' => we_base_moduleInfo::EXPORT,
		'text' => g_l('javaMenu_moduleInformation', '[export][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[export][text_short]'),
		'perm' => 'NEW_EXPORT || DELETE_EXPORT || EDIT_EXPORT || MAKE_EXPORT || ADMINISTRATOR',
		'icon' => 'fa-download',
		'inModuleMenu' => true,
		'alwaysActive' => true,
		'hasSettings' => false,
		'inModuleWindow' => true,
		'dependson' => '',
		'childmodule' => '',
		'tables' => []
	],
	we_base_moduleInfo::VOTING => [
		'name' => we_base_moduleInfo::VOTING,
		'text' => g_l('javaMenu_moduleInformation', '[voting][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[voting][text_short]'),
		'perm' => 'NEW_VOTING || DELETE_VOTING || EDIT_VOTING || ADMINISTRATOR',
		'icon' => 'fa-thumbs-up',
		'inModuleMenu' => true,
		'alwaysActive' => false,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['VOTING_LOG_TABLE', 'VOTING_TABLE']
	],
	/* 'spellchecker' => [
	  'name' => 'spellchecker',
	  'text' => g_l('javaMenu_moduleInformation', '[spellchecker][text]'),
	  'text_short' => g_l('javaMenu_moduleInformation', '[spellchecker][text_short]'),
	  'perm' => 'SPELLCHECKER_ADMIN || ADMINISTRATOR',
	  'icon' => '',
	  'inModuleMenu' => false,
	  'alwaysActive' => false,
	  'hasSettings' => true,
	  'dependson' => '',
	  'childmodule' => ''
	  ], */
	we_base_moduleInfo::GLOSSARY => [
		'name' => we_base_moduleInfo::GLOSSARY,
		'text' => g_l('javaMenu_moduleInformation', '[glossary][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[glossary][text_short]'),
		'perm' => 'NEW_GLOSSARY || DELETE_GLOSSARY || EDIT_GLOSSARY || ADMINISTRATOR',
		'icon' => 'fa-commenting',
		'inModuleMenu' => true,
		'alwaysActive' => false,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['GLOSSARY_TABLE']
	],
	we_base_moduleInfo::SEARCH => [
		'name' => we_base_moduleInfo::SEARCH,
		'text' => g_l('searchtool', '[weSearch]'),
		'text_short' => g_l('searchtool', '[weSearch]'),
		'perm' => '',
		'icon' => 'fa-search',
		'inModuleMenu' => false,
		'inModuleWindow' => true,
		'alwaysActive' => true,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['SEARCH_TABLE']
	],
	we_base_moduleInfo::THUMB => [
		'name' => we_base_moduleInfo::THUMB,
		'text' => g_l('javaMenu_global', '[thumbnails]'),
		'text_short' => g_l('javaMenu_global', '[thumbnails]'),
		'perm' => 'ADMINISTRATOR',
		'icon' => 'fa-image',
		'inModuleMenu' => true,
		'inModuleWindow' => true,
		'alwaysActive' => true,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['THUMBNAILS_TABLE']
	],
	we_base_moduleInfo::DOCTYPE => [
		'name' => we_base_moduleInfo::DOCTYPE,
		'text' => g_l('javaMenu_global', '[document_types]'),
		'text_short' => g_l('javaMenu_global', '[document_types]'),
		'perm' => 'EDIT_DOCTYPE',
		'icon' => 'fa-flask',
		'inModuleMenu' => true,
		'inModuleWindow' => true,
		'alwaysActive' => true,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => '',
		'tables' => ['DOC_TYPES_TABLE']
	],
];
