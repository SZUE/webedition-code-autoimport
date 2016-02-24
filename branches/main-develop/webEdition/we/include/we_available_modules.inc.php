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

return array(
	'users' => array(
		'name' => 'users',
		'perm' => 'NEW_USER || NEW_GROUP || SAVE_USER || SAVE_GROUP || DELETE_USER || DELETE_GROUP || ADMINISTRATOR',
		'text' => g_l('javaMenu_moduleInformation', '[users][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[users][text_short]'),
		'icon' => 'fa-user',
		'inModuleMenu' => true,
		'integrated' => true,
		'alwaysActive' => true,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => ''
	),
	'customer' => array(
		'name' => 'customer',
		'perm' => 'SHOW_CUSTOMER_ADMIN || DELETE_CUSTOMER || EDIT_CUSTOMER || NEW_CUSTOMER || ADMINISTRATOR',
		'text' => g_l('javaMenu_moduleInformation', '[customer][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[customer][text_short]'),
		'icon' => 'fa-users',
		'inModuleMenu' => true,
		'integrated' => true,
		'alwaysActive' => false,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => 'shop'
	),
	'navigation' => array(
		'name' => 'navigation',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'text' => g_l('javaMenu_global', '[navigation]'),
		'text_short' => g_l('javaMenu_global', '[navigation]'),
		'icon' => 'fa-compass',
		'inModuleMenu' => true,
		'integrated' => true,
		'alwaysActive' => true,
		'hasSettings' => '',
		'dependson' => '',
		'childmodule' => ''
	),
	'collection' => array(
		'name' => 'collection',
		'perm' => 'CAN_SEE_COLLECTIONS || DELETE_COLLECTIONS || EDIT_COLLECTIONS || NEW_COLLECTIONS || ADMINISTRATOR',
		'text' => g_l('javaMenu_moduleInformation', '[collection][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[collection][text_short]'),
		'icon' => '',
		'inModuleMenu' => false,
		'integrated' => true,
		'alwaysActive' => false,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => ''
	),
	'shop' => array(
		'name' => 'shop',
		'text' => g_l('javaMenu_moduleInformation', '[shop][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[shop][text_short]'),
		'perm' => 'NEW_SHOP_ARTICLE || DELETE_SHOP_ARTICLE || EDIT_SHOP_ORDER || DELETE_SHOP_ORDER || EDIT_SHOP_PREFS || ADMINISTRATOR',
		'icon' => 'fa-shopping-cart',
		'inModuleMenu' => true,
		'integrated' => true,
		'hasSettings' => true,
		'dependson' => 'customer',
		'childmodule' => ''
	),
	'schedule' => array(
		'name' => 'schedule',
		'text' => g_l('javaMenu_moduleInformation', '[schedule][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[schedule][text_short]'),
		'icon' => '',
		'inModuleMenu' => false,
		'integrated' => true,
		'alwaysActive' => false,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => ''
	),
	'editor' => array(
		'name' => 'editor',
		'text' => g_l('javaMenu_moduleInformation', '[editor][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[editor][text_short]'),
		'perm' => '',
		'icon' => '',
		'inModuleMenu' => false,
		'integrated' => true,
		'alwaysActive' => true,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => ''
	),
	'object' => array(
		'name' => 'object',
		'text' => g_l('javaMenu_moduleInformation', '[object][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[object][text_short]'),
		'icon' => '',
		'inModuleMenu' => false,
		'integrated' => true,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => ''
	),
	'messaging' => array(
		'name' => 'messaging',
		'text' => g_l('javaMenu_moduleInformation', '[messaging][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[messaging][text_short]'),
		'perm' => '',
		'icon' => 'fa-envelope',
		'inModuleMenu' => true,
		'integrated' => true,
		'hasSettings' => false, //the only setting was not used
		'dependson' => '',
		'childmodule' => 'workflow'
	),
	'workflow' => array(
		'name' => 'workflow',
		'text' => g_l('javaMenu_moduleInformation', '[workflow][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[workflow][text_short]'),
		'perm' => 'NEW_WORKFLOW || DELETE_WORKFLOW || EDIT_WORKFLOW || EMPTY_LOG || ADMINISTRATOR',
		'icon' => 'fa-gears',
		'inModuleMenu' => true,
		'integrated' => true,
		'hasSettings' => false,
		'dependson' => 'messaging',
		'childmodule' => ''
	),
	'newsletter' => array(
		'name' => 'newsletter',
		'text' => g_l('javaMenu_moduleInformation', '[newsletter][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[newsletter][text_short]'),
		'perm' => 'NEW_NEWSLETTER || DELETE_NEWSLETTER || EDIT_NEWSLETTER || SEND_NEWSLETTER || SEND_TEST_EMAIL || ADMINISTRATOR',
		'icon' => 'fa-newspaper-o',
		'inModuleMenu' => true,
		'integrated' => true,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => ''
	),
	'banner' => array(
		'name' => 'banner',
		'text' => g_l('javaMenu_moduleInformation', '[banner][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[banner][text_short]'),
		'perm' => 'NEW_BANNER || DELETE_BANNER || EDIT_BANNER || ADMINISTRATOR',
		'icon' => 'fa-flag',
		'inModuleMenu' => true,
		'integrated' => true,
		'alwaysActive' => false,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => ''
	),
	'export' => array(
		'name' => 'export',
		'text' => g_l('javaMenu_moduleInformation', '[export][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[export][text_short]'),
		'perm' => 'NEW_EXPORT || DELETE_EXPORT || EDIT_EXPORT || MAKE_EXPORT || ADMINISTRATOR',
		'icon' => 'fa-download',
		'inModuleMenu' => true,
		'integrated' => true,
		'alwaysActive' => true,
		'hasSettings' => false,
		'inModuleWindow' => true,
		'dependson' => '',
		'childmodule' => ''
	),
	'voting' => array(
		'name' => 'voting',
		'text' => g_l('javaMenu_moduleInformation', '[voting][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[voting][text_short]'),
		'perm' => 'NEW_VOTING || DELETE_VOTING || EDIT_VOTING || ADMINISTRATOR',
		'icon' => 'fa-thumbs-up',
		'inModuleMenu' => true,
		'integrated' => true,
		'alwaysActive' => false,
		'hasSettings' => false,
		'dependson' => '',
		'childmodule' => ''
	),
	/*'spellchecker' => array(
		'name' => 'spellchecker',
		'text' => g_l('javaMenu_moduleInformation', '[spellchecker][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[spellchecker][text_short]'),
		'perm' => 'SPELLCHECKER_ADMIN || ADMINISTRATOR',
		'icon' => '',
		'inModuleMenu' => false,
		'integrated' => true,
		'alwaysActive' => false,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => ''
	),*/
	'glossary' => array(
		'name' => 'glossary',
		'text' => g_l('javaMenu_moduleInformation', '[glossary][text]'),
		'text_short' => g_l('javaMenu_moduleInformation', '[glossary][text_short]'),
		'perm' => 'NEW_GLOSSARY || DELETE_GLOSSARY || EDIT_GLOSSARY || ADMINISTRATOR',
		'icon' => 'fa-commenting',
		'inModuleMenu' => true,
		'integrated' => true,
		'alwaysActive' => false,
		'hasSettings' => true,
		'dependson' => '',
		'childmodule' => ''
	),
);
