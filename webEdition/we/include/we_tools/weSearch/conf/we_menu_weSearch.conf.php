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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require (WE_INCLUDES_PATH . 'we_tools/weSearch/conf/meta.conf.php');

$we_menu_weSearch = array(
	100 => array(
		'text' => g_l('searchtool', '[menu_suche]'),
		'parent' => 0,
		'perm' => '',
		'enabled' => 1,
	),
	200 => array(
		'text' => g_l('searchtool', '[menu_new]'),
		'parent' => 100,
		'perm' => '',
		'enabled' => 1,
	)
);
if(we_hasPerm('CAN_SEE_DOCUMENTS')){
	$we_menu_weSearch[300] = array(
		'text' => g_l('searchtool', '[forDocuments]'),
		'parent' => 200,
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_forDocuments',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => 1,
	);
}
if($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE && we_hasPerm('CAN_SEE_TEMPLATES')){
	$we_menu_weSearch[400] = array(
		'text' => g_l('searchtool', '[forTemplates]'),
		'parent' => 200,
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_forTemplates',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => 1,
	);
}
if(defined('OBJECT_FILES_TABLE') && defined('OBJECT_TABLE') && we_hasPerm('CAN_SEE_OBJECTFILES')){
	$we_menu_weSearch[500] = array(
		'text' => g_l('searchtool', '[forObjects]'),
		'parent' => 200,
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_forObjects',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => 1,
	);
}
$we_menu_weSearch[600] = array(
	'text' => g_l('searchtool', '[menu_advSearch]'),
	'parent' => 200,
	'cmd' => 'tool_' . $metaInfo['name'] . '_new_advSearch',
	'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
	'enabled' => 1,
);

//'text'=> g_l('searchtool','[menu_new_group]'),
//'parent'=> 200,
//'cmd'=> 'tool_' . $metaInfo['name'] . '_new_group',
//'perm'=> 'ADMINISTRATOR',
//'enabled'=> 1,


$we_menu_weSearch[800] = array(
	'text' => g_l('searchtool', '[menu_save]'),
	'parent' => 100,
	'cmd' => 'tool_' . $metaInfo['name'] . '_save',
	'perm' => '',
	'enabled' => 1,
);

$we_menu_weSearch[900] = array(
	'text' => g_l('searchtool', '[menu_delete]'),
	'parent' => 100,
	'cmd' => 'tool_' . $metaInfo['name'] . '_delete',
	'perm' => '',
	'enabled' => 1,
);

$we_menu_weSearch[950] = array('parent' => 100); // separator


$we_menu_weSearch[1000] = array(
	'text' => g_l('searchtool', '[menu_exit]'),
	'parent' => 100,
	'cmd' => 'tool_' . $metaInfo['name'] . '_exit',
	'perm' => '',
	'enabled' => 1,
);

$we_menu_weSearch[3000] = array(
	'text' => g_l('searchtool', '[menu_help]'),
	'parent' => 0,
	'perm' => '',
	'enabled' => 1,
);

$we_menu_weSearch[3100] = array(
	'text' => g_l('searchtool', '[menu_help]') . '&hellip;',
	'parent' => 3000,
	'cmd' => 'help_tools',
	'perm' => '',
	'enabled' => 1,
);

$we_menu_weSearch[3200] = array(
	'text' => g_l('searchtool', '[menu_info]') . '&hellip;',
	'parent' => 3000,
	'cmd' => 'info_tools',
	'perm' => '',
	'enabled' => 1,
);