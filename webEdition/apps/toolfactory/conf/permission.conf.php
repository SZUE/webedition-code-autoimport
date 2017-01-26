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
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/core/autoload.inc.php');

$perm_group_name = "toolfactory";
$perm_group_title[$perm_group_name] = g_l('apps', '[toolfactory][name]');

$perm_values[$perm_group_name] = ['USE_APP_TOOLFACTORY', 'NEW_APP_TOOLFACTORY', 'DELETE_APP_TOOLFACTORY', 'EDIT_APP_TOOLFACTORY', 'PUBLISH_APP_TOOLFACTORY', 'GENTOC_APP_TOOLFACTORY'];

$perm_titles[$perm_group_name] = [
	'USE_APP_TOOLFACTORY' => g_l('apps', '[toolfactory][perm][use]'),
	'NEW_APP_TOOLFACTORY' => g_l('apps', '[toolfactory][perm][create]'),
	'DELETE_APP_TOOLFACTORY' => g_l('apps', '[toolfactory][perm][delete]'),
	'EDIT_APP_TOOLFACTORY' => g_l('apps', '[toolfactory][perm][edit]'),
	'PUBLISH_APP_TOOLFACTORY' => g_l('apps', '[toolfactory][perm][publish]'),
	'GENTOC_APP_TOOLFACTORY' => g_l('apps', '[toolfactory][perm][regenerateTOC]'),
];

$perm_defaults[$perm_group_name] = [
	'USE_APP_TOOLFACTORY' => 1,
	'NEW_APP_TOOLFACTORY' => 1,
	'DELETE_APP_TOOLFACTORY' => 0,
	'EDIT_APP_TOOLFACTORY' => 0,
	'PUBLISH_APP_TOOLFACTORY' => 0,
	'GENTOC_APP_TOOLFACTORY' => 0
];
