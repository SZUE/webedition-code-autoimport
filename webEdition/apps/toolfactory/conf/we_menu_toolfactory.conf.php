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
$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', 'toolfactory');


include_once ('meta.conf.php');

$controller = Zend_Controller_Front::getInstance();
$appName = $controller->getParam('appName');

$we_menu_toolfactory = [100 => ['text' => $translate->_('toolfactory'),
		'parent' => 0,
		'perm' => '',
		'enabled' => 1,
	],
	['text' => $translate->_('New Entry'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_new',
		'perm' => 'NEW_APP_TOOLFACTORY || ADMINISTRATOR',
		'enabled' => 1,
	],
	['text' => $translate->_('Delete Entry/Group.'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_checkdelete',
		'perm' => 'DELETE_APP_TOOLFACTORY || ADMINISTRATOR',
		'enabled' => 1,
	],
	['text' => $translate->_('Generate TGZ-File from App'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_generateTGZ',
		'perm' => 'NEW_APP_TOOLFACTORY || ADMINISTRATOR',
		'enabled' => 1,
	],
	['parent' => 100, // separator
	],
	['text' => $translate->_('Close'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_exit',
		'perm' => '',
		'enabled' => 1,
	],
	3000 => ['text' => $translate->_('Help'),
		'parent' => 0,
		'perm' => '',
		'enabled' => 1,
	],
	['text' => $translate->_('Help') . '&hellip;',
		'parent' => 3000,
		'cmd' => 'app_' . $appName . '_help',
		'perm' => '',
		'enabled' => 1,
	],
	['text' => $translate->_('Info') . '&hellip;',
		'parent' => 3000,
		'cmd' => 'app_' . $appName . '_info',
		'perm' => '',
		'enabled' => 1,
	]
];
