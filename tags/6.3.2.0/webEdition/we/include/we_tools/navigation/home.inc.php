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

$createNavigation = we_button::create_button(
		'new_item', "javascript:we_cmd('tool_navigation_new');", true, -1, -1, "", "", !we_hasPerm('EDIT_NAVIGATION'));
$createNavigationGroup = we_button::create_button(
		'new_folder', "javascript:we_cmd('tool_navigation_new_group');", true, -1, -1, "", "", !we_hasPerm('EDIT_NAVIGATION'));

$content = $createNavigation . we_html_tools::getPixel(2, 14) . $createNavigationGroup;
$tool = "navigation";
$title = g_l('navigation', '[navigation]');

include ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tools/home.inc.php');
