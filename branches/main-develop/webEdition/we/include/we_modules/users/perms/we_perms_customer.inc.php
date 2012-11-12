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
$perm_group_name = "customer";

$perm_group_title[$perm_group_name] = g_l('perms_customer', "[perm_group_title]");

$perm_values[$perm_group_name] = array(
	'NEW_CUSTOMER',
	'DELETE_CUSTOMER',
	'EDIT_CUSTOMER',
	'SHOW_CUSTOMER_ADMIN',
	'CUSTOMER_PASSWORD_VISIBLE',
	'CUSTOMER_AUTOLOGINID_VISIBLE',
	'CAN_EDIT_CUSTOMERFILTER',
	'CAN_CHANGE_DOCS_CUSTOMER');

//	Here the array of the permission-titles is set.
$perm_titles[$perm_group_name] = array();

for($i = 0; $i < count($perm_values[$perm_group_name]); $i++){

	$perm_titles[$perm_group_name][$perm_values[$perm_group_name][$i]] = g_l('perms_' . $perm_group_name, '[' . $perm_values[$perm_group_name][$i] . ']');
}

$perm_defaults[$perm_group_name] = array(
	'NEW_CUSTOMER' => 0,
	'DELETE_CUSTOMER' => 0,
	'EDIT_CUSTOMER' => 0,
	'SHOW_CUSTOMER_ADMIN' => 0,
	'CUSTOMER_PASSWORD_VISIBLE' => 0,
	'CUSTOMER_AUTOLOGINID_VISIBLE' => 0,
	'CAN_EDIT_CUSTOMERFILTER' => 1,
	'CAN_CHANGE_DOCS_CUSTOMER' => 1);
