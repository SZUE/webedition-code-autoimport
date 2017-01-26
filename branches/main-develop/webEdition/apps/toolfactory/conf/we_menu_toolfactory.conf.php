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
include_once ('meta.conf.php');

$controller = Zend_Controller_Front::getInstance();
$appName = $controller->getParam('appName');

$we_menu_toolfactory = [
	100 => ['text' => g_l('apps', '[toolfactory][name]'),
		'parent' => 0,
		'perm' => '',
		'enabled' => 1,
	],
	['text' => g_l('apps', '[menu][new][entry]'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_new',
		'perm' => 'NEW_APP_TOOLFACTORY || ADMINISTRATOR',
		'enabled' => 1,
	],
	['text' => g_l('apps', '[menu][delete][all]'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_checkdelete',
		'perm' => 'DELETE_APP_TOOLFACTORY || ADMINISTRATOR',
		'enabled' => 1,
	],
	['text' => g_l('apps', '[menu][generateTGZ]'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_generateTGZ',
		'perm' => 'NEW_APP_TOOLFACTORY || ADMINISTRATOR',
		'enabled' => 1,
	],
	['parent' => 100, // separator
	],
	['text' => g_l('apps', '[menu][close]'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_exit',
		'perm' => '',
		'enabled' => 1,
	],
	3000 => ['text' => g_l('apps', '[menu][help]'),
		'parent' => 0,
		'perm' => '',
		'enabled' => 1,
	],
	['text' => g_l('apps', '[menu][help]') . '&hellip;',
		'parent' => 3000,
		'cmd' => 'app_' . $appName . '_help',
		'perm' => '',
		'enabled' => 1,
	],
	['text' => g_l('apps', '[menu][info]') . '&hellip;',
		'parent' => 3000,
		'cmd' => 'app_' . $appName . '_info',
		'perm' => '',
		'enabled' => 1,
	]
];
