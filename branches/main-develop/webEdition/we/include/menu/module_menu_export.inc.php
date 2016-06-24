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
	'export' => [
		'text' => g_l('export', '[export]'),
	],
	'new' => [
		'text' => g_l('export', '[new]'),
		'parent' => 'export',
	],
	[
		'text' => g_l('export', '[export]'),
		'cmd' => 'new_export',
		'perm' => 'NEW_EXPORT || ADMINISTRATOR',
		'parent' => 'new',
	],
	[
		'text' => g_l('export', '[group]'),
		'cmd' => 'new_export_group',
		'perm' => 'NEW_EXPORT || ADMINISTRATOR',
		'parent' => 'new',
	],
	[
		'text' => g_l('export', '[save]'),
		'parent' => 'export',
		'cmd' => 'save_export',
		'perm' => 'NEW_EXPORT || EDIT_EXPORT || ADMINISTRATOR',
	],
	[
		'text' => g_l('export', '[delete]'),
		'parent' => 'export',
		'cmd' => 'delete_export',
		'perm' => 'DELETE_EXPORT || ADMINISTRATOR',
	],
	['parent' => 'export',],
	[
		'text' => g_l('export', '[quit]'),
		'parent' => 'export',
		'cmd' => 'exit_export',
	],
	'help' => [
		'text' => g_l('export', '[help]'),
	],
	[
		'text' => g_l('export', '[help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
	],
	[
		'text' => g_l('export', '[info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	],
];
