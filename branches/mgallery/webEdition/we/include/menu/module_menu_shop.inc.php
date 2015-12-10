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
// file
$years = we_shop_shop::getAllOrderYears();

return array(
	'shop' => array(
		'text' => g_l('javaMenu_shop', '[menu_user]'),
	),
	'jahr' => array(
		'text' => g_l('javaMenu_shop', '[year]'),
		'parent' => 'shop',
	),
	180000 => array(
		'parent' => 'shop'
	), // separator
	array(
		'text' => g_l('javaMenu_shop', '[menu_exit]'),
		'parent' => 'shop',
		'cmd' => 'exit_shop',
	),
// edit
	'edit' => array(
		'text' => g_l('javaMenu_shop', '[shop_edit]'),
		'perm' => 'edit_shop',
	),
	array(
		'text' => g_l('javaMenu_shop', '[shop_pref]') . '&hellip;',
		'parent' => 'edit',
		'cmd' => 'pref_shop',
		'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	),
	array(
		'parent' => 'edit'
	), // separator
	array(
		'text' => g_l('javaMenu_shop', '[shop_status]') . '&hellip;',
		'parent' => 'edit',
		'cmd' => 'edit_shop_status',
		'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_shop', '[country_vat]') . '&hellip;',
		'parent' => 'edit',
		'cmd' => 'edit_shop_vat_country',
		'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_shop', '[edit_categories]') . '&hellip;',
		'parent' => 'edit',
		'cmd' => 'edit_shop_categories',
		'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_shop', '[edit_vats]') . '&hellip;',
		'parent' => 'edit',
		'cmd' => 'edit_shop_vats',
		'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	),
	array(
		'text' => g_l('modules_shop', '[shipping][shipping_package]') . '&hellip;',
		'parent' => 'edit',
		'cmd' => 'edit_shop_shipping',
		'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	),
	array(
		'text' => g_l('modules_shop', '[shipping][payment_provider]') . '&hellip;',
		'parent' => 'edit',
		'cmd' => 'payment_val',
		'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	),
	array('parent' => 'edit'), // separator
	array(
		'text' => g_l('modules_shop', '[shipping][revenue_view]'),
		'parent' => 'edit',
		'cmd' => 'revenue_view',
		'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	),
	array('parent' => 'edit'), // separator
	'order' => array(
		'text' => g_l('javaMenu_shop', '[order]'),
		'parent' => 'edit',
	),
	array(
		'text' => g_l('javaMenu_shop', '[add_article_to_order]'),
		'parent' => 'order',
		'cmd' => 'new_article',
		'perm' => 'NEW_SHOP_ARTICLE || ADMINISTRATOR',
	),
	array(
		'text' => g_l('javaMenu_shop', '[delete_order]'),
		'parent' => 'order',
		'cmd' => 'delete_shop',
		'perm' => 'DELETE_SHOP_ARTICLE || ADMINISTRATOR',
	),
	'help' => array(
		'text' => g_l('javaMenu_shop', '[menu_help]'),
		'perm' => 'SHOW_HELP',
	),
	array(
		'text' => g_l('javaMenu_shop', '[menu_help]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'help_modules',
		'perm' => 'SHOW_HELP',
	),
	array(
		'text' => g_l('javaMenu_shop', '[menu_info]') . '&hellip;',
		'parent' => 'help',
		'cmd' => 'info_modules',
	),
);

foreach($years as $cur){
	$we_menu_shop[] = array(
		'text' => $cur,
		'parent' => 'jahr',
		'cmd' => 'year' . $cur,
	);
}

return $we_menu_shop;