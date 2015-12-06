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
	'newsletter' => array(
		'text' => g_l('modules_newsletter', '[newsletter][text]'),
	),
	'new' => array(
		'text' => g_l('modules_newsletter', '[new]'),
		'parent' => 'newsletter',
	),
	array(
		'text' => g_l('modules_newsletter', '[newsletter][text]'),
		'cmd' => 'new_newsletter',
		'perm' => 'NEW_NEWSLETTER || ADMINISTRATOR',
		'parent' => 'new',
	),
	array(
		'text' => g_l('modules_newsletter', '[group]'),
		'cmd' => 'new_newsletter_group',
		'perm' => 'NEW_NEWSLETTER || ADMINISTRATOR',
		'parent' => 'new',
	),
	array(
		'text' => g_l('modules_newsletter', '[save]'),
		'parent' => 'newsletter',
		'cmd' => 'save_newsletter',
		'perm' => 'NEW_NEWSLETTER || EDIT_NEWSLETTER || ADMINISTRATOR',
	),
	array(
		'text' => g_l('modules_newsletter', '[delete]'),
		'parent' => 'newsletter',
		'cmd' => 'delete_newsletter',
		'perm' => 'DELETE_NEWSLETTER || ADMINISTRATOR',
	),
	array(
		'parent' => 'newsletter', // separator
	),
	array(
		'text' => g_l('modules_newsletter', '[send]') . '&hellip;',
		'parent' => 'newsletter',
		'cmd' => 'send_newsletter',
		'perm' => 'SEND_NEWSLETTER || ADMINISTRATOR',
	),
	array(
		'parent' => 'newsletter', // separator
	),
	array(
		'text' => g_l('modules_newsletter', '[quit]'),
		'parent' => 'newsletter',
		'cmd' => 'exit_newsletter',
	),
	'options' => array(
		'text' => g_l('modules_newsletter', '[options]'),
	),
	array(
		'text' => g_l('modules_newsletter', '[domain_check]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'domain_check',
	),
	array(
		'text' => g_l('modules_newsletter', '[lists_overview_menu]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'print_lists',
	),
	array(
		'text' => g_l('modules_newsletter', '[show_log]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'show_log',
	),
	array(
		'parent' => 'options', // separator
	),
	array(
		'text' => g_l('modules_newsletter', '[newsletter_test]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'test_newsletter',
	),
	array(
		'text' => g_l('modules_newsletter', '[preview]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'preview_newsletter',
	),
	array(
		'text' => g_l('modules_newsletter', '[send_test]'),
		'parent' => 'options',
		'cmd' => 'send_test',
		'perm' => 'SEND_TEST_EMAIL || ADMINISTRATOR',
	),
	array(
		'text' => g_l('modules_newsletter', '[search_email]'),
		'parent' => 'options',
		'cmd' => 'search_email',
	),
	array(
		'parent' => 'options', // separator
	),
	array(
		'text' => g_l('modules_newsletter', '[edit_file]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'edit_file',
	),
	array(
		'text' => g_l('modules_newsletter', '[black_list]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'black_list',
	),
	array(
		'text' => g_l('modules_newsletter', '[clear_log]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'clear_log',
		'perm' => 'NEWSLETTER_SETTINGS || ADMINISTRATOR',
	),
	array(
		'text' => g_l('modules_newsletter', '[settings]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'newsletter_settings',
		'perm' => 'NEWSLETTER_SETTINGS || ADMINISTRATOR',
	),
	'help' => array(
		'text' => g_l('modules_newsletter', '[help]'),
	),
	array(
		'text' => g_l('modules_newsletter', '[help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
		'enableadd' => 1,
	),
	array(
		'text' => g_l('modules_newsletter', '[info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	)
);
