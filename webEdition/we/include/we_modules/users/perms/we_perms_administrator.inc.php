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


$perm_group_name="administrator";

$perm_group_title[$perm_group_name] = g_l('perms_administrator',"[perm_group_title]");

$perm_values[$perm_group_name]=array("ADMINISTRATOR");

//	Here the array of the permission-titles is set.
//	$perm_titles[$perm_group_name]["NAME OF PERMISSION"] = g_l('perms_'.$perm_group_name,'['."NAME OF PERMISSION".']')
$perm_titles[$perm_group_name] = array();

for($i = 0; $i < count($perm_values[$perm_group_name]); $i++){

	$perm_titles[$perm_group_name][$perm_values[$perm_group_name][$i]] = g_l('perms_'.$perm_group_name,'['.$perm_values[$perm_group_name][$i].']');

}

$perm_defaults[$perm_group_name] = array("ADMINISTRATOR"=>0);

