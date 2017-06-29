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
	'new' => [
		'text' => g_l('javaMenu_users', '[menu_new]'),
		'icon' => 'fa fa-plus-circle',
	],
	['text' => g_l('javaMenu_users', '[menu_user]'),
		'parent' => 'new',
		'cmd' => 'new_user',
		'perm' => 'NEW_USER || ADMINISTRATOR',
	],
	['text' => g_l('javaMenu_users', '[menu_alias]'),
		'parent' => 'new',
		'cmd' => 'new_alias',
		'perm' => 'NEW_USER || ADMINISTRATOR',
	],
	['parent' => 'new',],
	['text' => g_l('javaMenu_users', '[group]'),
		'parent' => 'new',
		'cmd' => 'new_group',
		'perm' => 'NEW_GROUP || ADMINISTRATOR',
	],
	'user' => [
		'text' => g_l('javaMenu_users', '[menu_user]'),
		'icon' => 'fa fa-user'
		],
	['text' => g_l('javaMenu_users', '[menu_save]'),
		'parent' => 'user',
		'cmd' => 'save_user',
		'perm' => 'NEW_GROUP || NEW_USER || SAVE_USER || SAVE_GROUP || ADMINISTRATOR',
	],
	['text' => g_l('javaMenu_users', '[menu_delete]'),
		'parent' => 'user',
		'cmd' => 'delete_user',
		'perm' => 'DELETE_USER || DELETE_GROUP || ADMINISTRATOR',
	],
	['parent' => 'user',],
	['text' => g_l('javaMenu_users', '[menu_exit]'),
		'parent' => 'user',
		'cmd' => 'exit_users',
	],
];
