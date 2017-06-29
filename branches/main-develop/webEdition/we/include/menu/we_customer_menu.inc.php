<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software, you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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
	'new' => [
		'text' => g_l('modules_customer', '[menu_new]'),
		'perm' => 'NEW_CUSTOMER || ADMINISTRATOR',
		'icon' => 'fa fa-plus-circle',
	],
	[
		'text' => g_l('modules_customer', '[menu_customer]'),
		'parent' => 'new',
		'cmd' => 'new_customer',
		'perm' => 'NEW_CUSTOMER || ADMINISTRATOR',
	],
	'customer' => [
		'text' => g_l('modules_customer', '[menu_customer]'),
		'icon' => 'fa fa-users'
	],
	[
		'text' => g_l('modules_customer', '[menu_save]'),
		'parent' => 'customer',
		'cmd' => 'save_customer',
		'perm' => 'EDIT_CUSTOMER || NEW_CUSTOMER || ADMINISTRATOR',
	],
	[
		'text' => g_l('modules_customer', '[menu_delete]'),
		'parent' => 'customer',
		'cmd' => 'delete_customer',
		'perm' => 'DELETE_CUSTOMER || ADMINISTRATOR',
	],
	['parent' => 'customer',], // separator
	'admin' => [
		'text' => g_l('modules_customer', '[menu_admin]'),
		'parent' => 'customer',
	],
	[
		'text' => g_l('modules_customer', '[field_admin]') . '&hellip;',
		'parent' => 'admin',
		'cmd' => 'show_admin',
		'perm' => 'SHOW_CUSTOMER_ADMIN || ADMINISTRATOR',
	],
	[
		'text' => g_l('modules_customer', '[sort_admin]') . '&hellip;',
		'parent' => 'admin',
		'cmd' => 'show_sort_admin',
		'perm' => 'SHOW_CUSTOMER_ADMIN || ADMINISTRATOR',
	],
	['parent' => 'customer',], // separator
	[
		'text' => g_l('modules_customer', '[import]') . '&hellip;',
		'parent' => 'customer',
		'cmd' => 'import_customer',
		'perm' => 'SHOW_CUSTOMER_ADMIN || ADMINISTRATOR',
	],
	[
		'text' => g_l('modules_customer', '[export]') . '&hellip;',
		'parent' => 'customer',
		'cmd' => 'export_customer',
		'perm' => 'SHOW_CUSTOMER_ADMIN || ADMINISTRATOR',
	],
	['parent' => 'customer',], // separator
	[
		'text' => g_l('modules_customer', '[search]') . '&hellip;',
		'parent' => 'customer',
		'cmd' => 'show_search',
	],
	[
		'text' => g_l('modules_customer', '[settings]') . '&hellip;',
		'parent' => 'customer',
		'cmd' => 'show_customer_settings',
	],
	['parent' => 'customer',], // separator
	[
		'text' => g_l('modules_customer', '[menu_exit]'),
		'parent' => 'customer',
		'cmd' => 'exit_customer',
	],
];
