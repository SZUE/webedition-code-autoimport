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
//TODO: do we need the other navigation cmds here?

switch($cmd){
	case 'navigation_edit':
		$_REQUEST['mod'] = 'navigation';
		$_REQUEST['pnt'] = 'show_frameset';
		return '../../we_showMod.php';
	case 'we_navigation_dirSelector':
		we_selector_file::getSelectorFromRequest();
		return true;
	case 'module_navigation_do_reset_customer_filter':
		we_navigation_navigation::reset_customer_filter();
		return true;
}
