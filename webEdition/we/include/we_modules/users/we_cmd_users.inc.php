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
switch($cmd){
	case 'users_edit':
		$_REQUEST['mod'] = 'users';
		$_REQUEST['pnt'] = 'show_frameset';
		return '../../we_showMod.php';
	case 'users_unlock':
		we_users_user::unlockDocuments();
		return true;
	case 'users_add_owner':
	case 'users_del_owner':
	case 'users_del_user':
	case 'users_del_all_owners':
	case 'users_add_user':
		we_editor_functions::processUsersCmd($cmd);
		return true;
	case 'users_changeR':
		echo we_users_util::changeRecursive();
		return true;
	case 'we_users_selector':
		we_selector_file::getSelectorFromRequest();
		return true;
}
