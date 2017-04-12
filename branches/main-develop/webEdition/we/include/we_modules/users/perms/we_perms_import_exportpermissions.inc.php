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
$perm_group_name = 'importExportpermissions';
$perm_group_title = g_l('perms_' . $perm_group_name, '[perm_group_title]');
$perm_defaults = ['EXPORT' => 0,
	'EXPORTNODOWNLOAD' => 0,
	'IMPORT' => 0,
	'BACKUPLOG' => 0,
	'FILE_IMPORT' => 1,
	'SITE_IMPORT' => 1,
	'WXML_IMPORT' => 0,
	'GENERICXML_IMPORT' => 0,
	'CSV_IMPORT' => 0,
	'NEW_EXPORT' => 0,
	'DELETE_EXPORT' => 0,
	'EDIT_EXPORT' => 0,
	'MAKE_EXPORT' => 0,
	'GENERICXML_EXPORT' => 1,
	'CSV_EXPORT' => 1
];
return [$perm_group_name, $perm_group_title, $perm_defaults];
