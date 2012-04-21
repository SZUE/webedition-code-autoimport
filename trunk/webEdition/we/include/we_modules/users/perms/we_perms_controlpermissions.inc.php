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


$perm_group_name="controlpermissions";

$perm_group_title[$perm_group_name] = g_l('perms_controlpermissions',"[perm_group_title]");


$perm_values[$perm_group_name] = array(
	"NEW_GROUP",
	"NEW_USER",
	"SAVE_GROUP",
	"SAVE_USER",
	"DELETE_GROUP",
	"DELETE_USER",
	"PUBLISH",
	"EDIT_SETTINGS_DEF_EXT",
	"EDIT_SETTINGS",
	"EDIT_PASSWD");

//	Here the array of the permission-titles is set.
$perm_titles[$perm_group_name] = array();

foreach($perm_values[$perm_group_name] as $entry){

	$perm_titles[$perm_group_name][$entry] = g_l('perms_'.$perm_group_name,'['.$entry.']');
}

$perm_defaults[$perm_group_name] = array(
	"NEW_GROUP"=>0,
	"NEW_USER"=>0,
	"SAVE_GROUP"=>0,
	"SAVE_USER"=>0,
	"DELETE_GROUP"=>0,
	"DELETE_USER"=>0,
	"PUBLISH"=>0,
	"EDIT_SETTINGS_DEF_EXT"=>0,
	"EDIT_SETTINGS"=>1,
	"EDIT_PASSWD"=>1
	);

