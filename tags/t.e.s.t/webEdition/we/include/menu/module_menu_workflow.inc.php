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
$we_menu_workflow = array(
	'workflow' => array(
		'text' => g_l('javaMenu_workflow', '[workflow]'),
	),
	array(
		'text' => g_l('javaMenu_workflow', '[new]'),
		'cmd' => 'new_workflow',
		'perm' => 'NEW_WORKFLOW || ADMINISTRATOR',
		'parent' => 'workflow',
	),
	array(
		'text' => g_l('javaMenu_workflow', '[save]'),
		'parent' => 'workflow',
		'cmd' => 'save_workflow',
		'perm' => 'EDIT_WORKFLOW || ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_workflow', '[delete]'),
		'parent' => 'workflow',
		'cmd' => 'delete_workflow',
		'perm' => 'DELETE_WORKFLOW || ADMINISTRATOR',
	),
	array(
		'parent' => 'workflow', // separator
	),
	/*
	  array(
	  'text'=> g_l('javaMenu_workflow','[reload]'),
	  'parent'=> 'workflow',
	  'cmd'=> 'reload_workflow',
	  'enabled'=> '0',
	  ),
	  $we_menu_workflow['000880']['parent'] = 'workflow'; // separator
	 */
	array(
		'text' => g_l('javaMenu_workflow', '[empty_log]') . '&hellip;',
		'parent' => 'workflow',
		'cmd' => 'empty_log',
		'perm' => 'EMPTY_LOG || ADMINISTRATOR',
	),
	array(
		'parent' => 'workflow', // separator
	),
	array(
		'text' => g_l('javaMenu_workflow', '[quit]'),
		'parent' => 'workflow',
		'cmd' => 'exit_workflow',
	),
	'help' => array(
		'text' => g_l('javaMenu_workflow', '[help]'),
	),
	array(
		'text' => g_l('javaMenu_workflow', '[help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
	),
	array(
		'text' => g_l('javaMenu_workflow', '[info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	)
);
