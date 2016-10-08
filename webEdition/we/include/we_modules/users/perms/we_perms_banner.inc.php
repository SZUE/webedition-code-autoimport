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
$perm_group_name = 'banner';
$perm_group_title[$perm_group_name] = g_l('perms_banner', '[perm_group_title]');

$perm_defaults[$perm_group_name] = ['NEW_BANNER' => 1,
	'DELETE_BANNER' => 0,
	'EDIT_BANNER' => 0];

$perm_values[$perm_group_name] = array_keys($perm_defaults[$perm_group_name]);

//	Here the array of the permission-titles is set.
$perm_titles[$perm_group_name] = [];

foreach($perm_values[$perm_group_name] as $cur){
	$perm_titles[$perm_group_name][$cur] = g_l('perms_' . $perm_group_name, '[' . $cur . ']');
}