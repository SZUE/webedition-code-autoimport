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
	'new' => array(
		'text' => g_l('javaMenu_users', '[menu_new]'),
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_user]'),
		'parent' => 'new',
		'cmd' => 'new_user',
		'perm' => 'NEW_USER || ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_alias]'),
		'parent' => 'new',
		'cmd' => 'new_alias',
		'perm' => 'NEW_USER || ADMINISTRATOR',
	),
	array(
		'parent' => 'new', // separator
	),
	array(
		'text' => g_l('javaMenu_users', '[group]'),
		'parent' => 'new',
		'cmd' => 'new_group',
		'perm' => 'NEW_GROUP || ADMINISTRATOR',
	),
	'user' => array(
		'text' => g_l('javaMenu_users', '[menu_user]'),
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_save]'),
		'parent' => 'user',
		'cmd' => 'save_user',
		'perm' => 'NEW_GROUP || NEW_USER || SAVE_USER || SAVE_GROUP || ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_delete]'),
		'parent' => 'user',
		'cmd' => 'delete_user',
		'perm' => 'DELETE_USER || DELETE_GROUP || ADMINISTRATOR',
	),
	array(
		'parent' => 'user', // separator
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_exit]'),
		'parent' => 'user',
		'cmd' => 'exit_users',
	),
	'help' => array(
		'text' => g_l('javaMenu_users', '[menu_help]'),
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_info]') . '&hellip;',
		'parent' => '001500',
		'cmd' => 'info_modules',
	),
);
