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
		'text' => g_l('modules_voting', '[menu_new]'),
		'icon' => 'fa fa-plus-circle',
	],
	['text' => g_l('modules_voting', '[voting]'),
		'parent' => 'new',
		'cmd' => 'new_voting',
		'perm' => 'NEW_VOTING || ADMINISTRATOR',
	],
	['text' => g_l('modules_voting', '[group]'),
		'parent' => 'new',
		'cmd' => 'new_voting_group',
		'perm' => 'NEW_VOTING || ADMINISTRATOR',
	],
	'voting' => [
		'text' => g_l('modules_voting', '[voting]'),
		'icon' => 'fa fa-thumbs-up'
	],
	['text' => g_l('modules_voting', '[menu_save]'),
		'parent' => 'voting',
		'cmd' => 'save_voting',
		'perm' => 'EDIT_VOTING || NEW_VOTING || ADMINISTRATOR',
	],
	['text' => g_l('modules_voting', '[menu_delete]'),
		'parent' => 'voting',
		'cmd' => 'delete_voting',
		'perm' => 'DELETE_VOTING || ADMINISTRATOR',
	],
	['parent' => 'voting',],
	['text' => g_l('modules_voting', '[menu_exit]'),
		'parent' => 'voting',
		'cmd' => 'exit_voting',
	],
	'help' => [
		'text' => g_l('modules_voting', '[menu_help]'),
		'icon' => 'fa fa-question-circle'],
	['text' => g_l('modules_voting', '[menu_help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
	],
	['text' => g_l('modules_voting', '[menu_info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	]
];
