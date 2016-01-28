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
switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '')){
	case 'module_navigation_rules':
		$toolInclude = 'navigation/edit_navigation_rules_frameset.php';
		break;
	case 'module_navigation_edit':
		$toolInclude = 'show_frameset.php';
		break;
	case 'module_navigation_edit_navi':
		$toolInclude = 'navigation/weNaviEditor.php';
		break;
	case 'module_navigation_do_reset_customer_filter':
		$toolInclude = 'navigation/reset_customerFilter.php';
		break;
}

if(isset($toolInclude)){
	include(WE_INCLUDES_PATH . 'we_modules/' . $toolInclude);
}
