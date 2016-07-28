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
	'new' => ['text' => g_l('javaMenu_messaging', '[new]'),],
	['text' => g_l('javaMenu_messaging', '[message]') . '&hellip;',
		'cmd' => 'messaging_new_message',
		'parent' => 'new',
	],
	['text' => g_l('javaMenu_messaging', '[todo]') . '&hellip;',
		'cmd' => 'messaging_new_todo',
		'parent' => 'new',
	],
	['text' => g_l('javaMenu_messaging', '[folder]'),
		'cmd' => 'messaging_new_folder',
		'parent' => 'new',
	],
	'file' => ['text' => g_l('javaMenu_messaging', '[file]'),],
	'delete' => ['text' => g_l('javaMenu_messaging', '[delete]'),
		'parent' => 'file',
	],
	['text' => g_l('javaMenu_messaging', '[folder]'),
		'cmd' => 'messaging_delete_mode_on',
		'parent' => 'delete',
	],
	['text' => g_l('javaMenu_messaging', '[quit]'),
		'cmd' => 'messaging_exit',
		'parent' => 'file',
	],
	'edit' => ['text' => g_l('javaMenu_messaging', '[edit]'),],
	['text' => g_l('javaMenu_messaging', '[folder]'),
		'cmd' => 'messaging_edit_folder',
		'parent' => 'edit',
	],
	['text' => g_l('javaMenu_messaging', '[copy]'),
		'cmd' => 'messaging_copy',
		'parent' => 'edit',
	],
	['text' => g_l('javaMenu_messaging', '[cut]'),
		'cmd' => 'messaging_cut',
		'parent' => 'edit',
	],
	['text' => g_l('javaMenu_messaging', '[paste]'),
		'cmd' => 'messaging_paste',
		'parent' => 'edit',
	],
	'help' => ['text' => g_l('javaMenu_messaging', '[help]'),],
	['text' => g_l('javaMenu_messaging', '[help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
	],
	['text' => g_l('javaMenu_messaging', '[info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	]
];
