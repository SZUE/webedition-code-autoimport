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
require (WE_INCLUDES_PATH . 'we_tools/weSearch/conf/meta.conf.php');

$we_menu_weSearch = array(
	'search' => array(
		'text' => g_l('searchtool', '[menu_suche]'),
	),
	'new' => array(
		'text' => g_l('searchtool', '[menu_new]'),
		'parent' => 'search',
	),
	array(
		'text' => g_l('searchtool', '[forDocuments]'),
		'parent' => 'new',
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_forDocuments',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'hide' => !permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')
	),
	array(
		'text' => g_l('searchtool', '[forTemplates]'),
		'parent' => 'new',
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_forTemplates',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'hide' => !($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE && permissionhandler::hasPerm('CAN_SEE_TEMPLATES'))
	),
	array(
		'text' => g_l('searchtool', '[forObjects]'),
		'parent' => 'new',
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_forObjects',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'hide' => (defined('OBJECT_FILES_TABLE') && defined('OBJECT_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES'))
	),
	array(
		'text' => g_l('searchtool', '[menu_advSearch]'),
		'parent' => 'new',
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_advSearch',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
	),
	array(
		'text' => g_l('searchtool', '[menu_save]'),
		'parent' => 'search',
		'cmd' => 'tool_' . $metaInfo['name'] . '_save',
	),
	array(
		'text' => g_l('searchtool', '[menu_delete]'),
		'parent' => 'search',
		'cmd' => 'tool_' . $metaInfo['name'] . '_delete',
	),
	array('parent' => 'search'), // separator
	array(
		'text' => g_l('searchtool', '[menu_exit]'),
		'parent' => 'search',
		'cmd' => 'tool_' . $metaInfo['name'] . '_exit',
	),
	'help' => array(
		'text' => g_l('searchtool', '[menu_help]'),
	),
	array(
		'text' => g_l('searchtool', '[menu_help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_tools',
	),
	array(
		'text' => g_l('searchtool', '[menu_info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_tools',
	),
);
