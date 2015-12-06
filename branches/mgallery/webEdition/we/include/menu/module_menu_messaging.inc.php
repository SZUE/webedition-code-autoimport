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
	'file' => array(
		'text' => g_l('javaMenu_messaging', '[file]'),
	),
	'new' => array(
		'text' => g_l('javaMenu_messaging', '[new]'),
		'parent' => 'file',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[message]') . '&hellip;',
		'cmd' => 'messaging_new_message',
		'parent' => 'new',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[todo]') . '&hellip;',
		'cmd' => 'messaging_new_todo',
		'parent' => 'new',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[folder]'),
		'cmd' => 'messaging_new_folder',
		'parent' => 'new',
	),
	'delete' => array(
		'text' => g_l('javaMenu_messaging', '[delete]'),
		'parent' => 'file',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[folder]'),
		'cmd' => 'messaging_delete_mode_on',
		'parent' => 'delete',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[quit]'),
		'cmd' => 'messaging_exit',
		'parent' => 'file',
	),
	'edit' => array(
		'text' => g_l('javaMenu_messaging', '[edit]'),
	),
	array(
		'text' => g_l('javaMenu_messaging', '[folder]'),
		'cmd' => 'messaging_edit_folder',
		'parent' => 'edit',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[settings]') . '&hellip;',
		'cmd' => 'messaging_settings',
		'parent' => 'edit',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[copy]'),
		'cmd' => 'messaging_copy',
		'parent' => 'edit',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[cut]'),
		'cmd' => 'messaging_cut',
		'parent' => 'edit',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[paste]'),
		'cmd' => 'messaging_paste',
		'parent' => 'edit',
	),
	'help' => array(
		'text' => g_l('javaMenu_messaging', '[help]'),
	),
	array(
		'text' => g_l('javaMenu_messaging', '[help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	)
);
