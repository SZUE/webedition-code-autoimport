<?php
             
/**
 * webEdition CMS
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


include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/modules/perms/shop.inc.php");

$perm_group_name="shop";
$perm_group_title[$perm_group_name] = $l_perm["shop"]["perm_group_title"];
 
 
$perm_values[$perm_group_name] = array(
	"NEW_SHOP_ARTICLE",
	"DELETE_SHOP_ARTICLE",
	"EDIT_SHOP_ORDER",
	"DELETE_SHOP_ORDER",
	"EDIT_SHOP_PREFS",
	"CAN_EDIT_VARIANTS"
	);
 
//	Here the array of the permission-titles is set.
//	$perm_titles[$perm_group_name]["NAME OF PERMISSION"] = $l_perm[$perm_group_name]["NAME OF PERMISSION"]
$perm_titles[$perm_group_name] = array();

for($i = 0; $i < sizeof($perm_values[$perm_group_name]); $i++){

	$perm_titles[$perm_group_name][$perm_values[$perm_group_name][$i]] = $l_perm[$perm_group_name][$perm_values[$perm_group_name][$i]];
}
 		
 
$perm_defaults[$perm_group_name] = array(
 	"NEW_SHOP_ARTICLE" => 0,
 	"DELETE_SHOP_ARTICLE" => 0,
 	"EDIT_SHOP_ORDER" => 0,
 	"DELETE_SHOP_ORDER" => 0,
 	"EDIT_SHOP_PREFS" => 0,
 	"CAN_EDIT_VARIANTS" => 1
 	);
?>