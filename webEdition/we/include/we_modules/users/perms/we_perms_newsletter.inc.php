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
$perm_group_name = 'newsletter';
$perm_group_title = g_l('perms_newsletter', '[perm_group_title]');
$perm_defaults = ['NEW_NEWSLETTER' => 0,
	'DELETE_NEWSLETTER' => 0,
	'EDIT_NEWSLETTER' => 0,
	'SEND_NEWSLETTER' => 0,
	'SEND_TEST_EMAIL' => 0,
	'NEWSLETTER_SETTINGS' => 0,
	'NEWSLETTER_FILES' => 0
];
return [$perm_group_name, $perm_group_title, $perm_defaults];
