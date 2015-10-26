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
$we_menu_navigation = array(
	100 => array(
		'text' => g_l('navigation', '[navigation]'),
		'parent' => 0,
		'perm' => '',
		'enabled' => 1,
	),
	200 => array(
		'text' => g_l('navigation', '[menu_new]'),
		'parent' => 100,
		'perm' => '',
		'enabled' => 1,
	),
	array(
		'text' => g_l('navigation', '[entry]'),
		'parent' => 200,
		'cmd' => 'module_navigation_new',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => 1,
	),
	array(
		'text' => g_l('navigation', '[group]'),
		'parent' => 200,
		'cmd' => 'module_navigation_new_group',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => 1,
	),
	array(
		'text' => g_l('navigation', '[menu_save]'),
		'parent' => 100,
		'cmd' => 'module_navigation_save',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => 1,
	),
	array(
		'text' => g_l('navigation', '[menu_delete]'),
		'parent' => 100,
		'cmd' => 'module_navigation_delete',
		'perm' => 'DELETE_NAVIGATION || EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => 1,
	),
	array(
		'parent' => 100, // separator
	),
	array(
		'text' => g_l('navigation', '[menu_exit]'),
		'parent' => 100,
		'cmd' => 'exit_navigation',
		'perm' => '',
		'enabled' => 1,
	),
	2000 => array(
		'text' => g_l('navigation', '[menu_options]'),
		'parent' => 0,
		'perm' => 'EDIT_NAVIAGTION_RULES',
		'enabled' => 1,
	),
	array(
		'text' => g_l('navigation', '[menu_highlight_rules]'),
		'parent' => 2000,
		'perm' => 'EDIT_NAVIAGTION_RULES',
		'cmd' => 'module_navigation_rules',
		'enabled' => 1,
	));
if(defined('CUSTOMER_TABLE')){
	$we_menu_navigation[2200] = array(
		'text' => g_l('navigation', '[reset_customer_filter]'),
		'parent' => 2000,
		'perm' => 'ADMINISTRATOR',
		'cmd' => 'module_navigation_reset_customer_filter',
		'enabled' => 1,
	);
}

$we_menu_navigation[3000] = array(
	'text' => g_l('navigation', '[menu_help]'),
	'parent' => 0,
	'perm' => '',
	'enabled' => 1,
);

$we_menu_navigation[3100] = array(
	'text' => g_l('navigation', '[menu_help]') . '&hellip;',
	'parent' => 3000,
	'cmd' => 'help_modules',
	'perm' => '',
	'enabled' => 1,
);

$we_menu_navigation[3200] = array(
	'text' => g_l('navigation', '[menu_info]') . '&hellip;',
	'parent' => 3000,
	'cmd' => 'info_modules',
	'perm' => '',
	'enabled' => 1,
);
