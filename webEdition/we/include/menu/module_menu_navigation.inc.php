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
	'navigation' => array(
		'text' => g_l('navigation', '[navigation]'),

	),
	'new' => array(
		'text' => g_l('navigation', '[menu_new]'),
		'parent' => 'navigation',

	),
	array(
		'text' => g_l('navigation', '[entry]'),
		'parent' => 'new',
		'cmd' => 'module_navigation_new',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
	),
	array(
		'text' => g_l('navigation', '[group]'),
		'parent' => 'new',
		'cmd' => 'module_navigation_new_group',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
	),
	array(
		'text' => g_l('navigation', '[menu_save]'),
		'parent' => 'navigation',
		'cmd' => 'module_navigation_save',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
	),
	array(
		'text' => g_l('navigation', '[menu_delete]'),
		'parent' => 'navigation',
		'cmd' => 'module_navigation_delete',
		'perm' => 'DELETE_NAVIGATION || EDIT_NAVIGATION || ADMINISTRATOR',
	),
	array(
		'parent' => 'navigation', // separator
	),
	array(
		'text' => g_l('navigation', '[menu_exit]'),
		'parent' => 'navigation',
		'cmd' => 'exit_navigation',

	),
	'options' => array(
		'text' => g_l('navigation', '[menu_options]'),
		'perm' => 'EDIT_NAVIAGTION_RULES',
	),
	array(
		'text' => g_l('navigation', '[menu_highlight_rules]'),
		'parent' => 'options',
		'perm' => 'EDIT_NAVIAGTION_RULES',
		'cmd' => 'module_navigation_rules',
	),
	array(
		'text' => g_l('navigation', '[reset_customer_filter]'),
		'parent' => 'options',
		'perm' => 'ADMINISTRATOR',
		'cmd' => 'module_navigation_reset_customer_filter',
		'hide' => !defined('CUSTOMER_TABLE')
	),
	array(
		'text' => g_l('navigation', '[menu_help]'),

	),
	array(
		'text' => g_l('navigation', '[menu_help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',

	),
	array(
		'text' => g_l('navigation', '[menu_info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',

	)
);
