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
		'text' => g_l('modules_banner', '[new]'),
	),
	array(
		'text' => g_l('modules_banner', '[banner]'),
		'cmd' => 'new_banner',
		'perm' => 'NEW_BANNER || ADMINISTRATOR',
		'parent' => 'new',
	),
	array(
		'text' => g_l('modules_banner', '[bannergroup]'),
		'cmd' => 'new_bannergroup',
		'perm' => 'NEW_BANNER || ADMINISTRATOR',
		'parent' => 'new',
	),
	'banner' => array(
		'text' => g_l('modules_banner', '[banner]'),
	),
	array(
		'text' => g_l('modules_banner', '[save]'),
		'parent' => 'banner',
		'cmd' => 'save_banner',
		'perm' => 'EDIT_BANNER || ADMINISTRATOR',
	),
	array(
		'text' => g_l('modules_banner', '[delete]'),
		'parent' => 'banner',
		'cmd' => 'delete_banner',
		'perm' => 'DELETE_BANNER || ADMINISTRATOR',
	),
	array(
		'parent' => 'banner', // separator
	),
	array(
		'text' => g_l('modules_banner', '[quit]'),
		'parent' => 'banner',
		'cmd' => 'exit_banner',
	),
	'options' => array(
		'text' => g_l('modules_banner', '[options]'),
	),
	array(
		'text' => g_l('modules_banner', '[defaultbanner]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'banner_default',
		'perm' => 'EDIT_BANNER || ADMINISTRATOR',
	),
	array(
		'text' => g_l('modules_banner', '[bannercode]') . '&hellip;',
		'parent' => 'options',
		'cmd' => 'banner_code',
		'perm' => 'EDIT_BANNER || ADMINISTRATOR',
	),
	'help' => array(
		'text' => g_l('modules_banner', '[help]'),
	),
	array(
		'text' => g_l('modules_banner', '[help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
	),
	array(
		'text' => g_l('modules_banner', '[info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	),
);
