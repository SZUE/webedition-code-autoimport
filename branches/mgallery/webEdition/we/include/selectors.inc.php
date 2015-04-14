<?php
/**
 * webEdition CMS
 *
 * $Rev: 9071 $
 * $Author: mokraemer $
 * $Date: 2015-01-20 15:27:44 +0100 (Di, 20. Jan 2015) $
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
$class = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . $class . '&';

switch($class){
	case 'we_navigation_dirSelector':
		$fs = new we_navigation_dirSelector(we_base_request::_(we_base_request::INT, 'id', we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1)), we_base_request::_(we_base_request::CMD, 'we_cmd', isset($JSIDName) ? $JSIDName : '', 2), we_base_request::_(we_base_request::CMD, 'we_cmd', isset($JSTextName) ? $JSTextName : '', 3), we_base_request::_(we_base_request::CMD, 'we_cmd', isset($JSCommand) ? $JSCommand : '', 4), we_base_request::_(we_base_request::RAW, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::RAW, 'we_FolderText', ''));
		break;
	case 'we_banner_dirSelector':
		if(($cmd1 = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1)) !== false){
			$_REQUEST['id'] = $cmd1;
			$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
		}

		$fs = new we_banner_dirSelector(we_base_request::_(we_base_request::INT, "id", 0), isset($JSIDName) ? $JSIDName : '', isset($JSTextName) ? $JSTextName : '', isset($JSCommand) ? $JSCommand : '', we_base_request::_(we_base_request::STRING, "order", ''), we_base_request::_(we_base_request::INT, "we_editDirID", 0), we_base_request::_(we_base_request::RAW, "we_FolderText", ''));
		break;
	case 'we_banner_selector':
		if(($cmd1 = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1)) !== false){
			$_REQUEST['id'] = $cmd1;
			$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
		}

		$fs = new we_banner_selector(we_base_request::_(we_base_request::INT, "id", 0), isset($JSIDName) ? $JSIDName : '', isset($JSTextName) ? $JSTextName : '', isset($JSCommand) ? $JSCommand : '', we_base_request::_(we_base_request::RAW, "order", ''));
		break;

	case 'we_newsletter_dirSelector':
		if(($cmd1 = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1)) !== false){
			$id = $cmd1;
			$JSIDName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2));
			$JSTextName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3));
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
			$sessionID = 0;
			$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 6);
			$filter = we_base_request::_(we_base_request::RAW, 'we_cmd', 7, '');
			$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8);
		}

		$fs = new we_newsletter_dirSelector(isset($id) ? $id : we_base_request::_(we_base_request::INT, "id", 0), isset($JSIDName) ? $JSIDName : '', isset($JSTextName) ? $JSTextName : '', isset($JSCommand) ? $JSCommand : '', we_base_request::_(we_base_request::RAW, "order", ''), 0, we_base_request::_(we_base_request::RAW, "we_editDirID", 0), we_base_request::_(we_base_request::RAW, "we_FolderText", ''), isset($rootDirID) ? $rootDirID : we_base_request::_(we_base_request::RAW, "rootDirID", 0), isset($multiple) ? $multiple : we_base_request::_(we_base_request::RAW, "multiple")
		);
		break;

	case 'we_export_dirSelector':
		if(($id = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1)) !== false){
			$_REQUEST['id'] = $id;
			$JSIDName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2));
			$JSTextName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3));
			$JSCommand = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4));
		}

		$fs = new we_export_dirSelector(we_base_request::_(we_base_request::INT, "id", 0), isset($JSIDName) ? $JSIDName : '', isset($JSTextName) ? $JSTextName : '', isset($JSCommand) ? $JSCommand : '', we_base_request::_(we_base_request::RAW, "order", ''), we_base_request::_(we_base_request::INT, "we_editDirID", ''), we_base_request::_(we_base_request::STRING, "we_FolderText", ''));
		break;

	case 'we_users_selector':
		we_html_tools::protect(array("NEW_USER", "NEW_GROUP", "SAVE_USER", "SAVE_GROUP", "DELETE_USER", "DELETE_GROUP"));
		if(($idname = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 1))){
			$_REQUEST['id'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4);
			$_REQUEST['table'] = USER_TABLE;

			$JSIDName = $idname;
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
			$_REQUEST['rootDirID'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
			$_REQUEST['filter'] = we_base_request::_(we_base_request::RAW, 'we_cmd', 0, 3);
			$_REQUEST['multiple'] = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8);
		}

		$fs = new we_users_selector(we_base_request::_(we_base_request::INT, "id", 0), we_base_request::_(we_base_request::TABLE, 'table', USER_TABLE), isset($JSIDName) ? $JSIDName : '', isset($JSTextName) ? $JSTextName : '', isset($JSCommand) ? $JSCommand : '', we_base_request::_(we_base_request::RAW, "order", ""), we_base_request::_(we_base_request::INT, "rootDirID", 0), we_base_request::_(we_base_request::STRING, "filter", ""), we_base_request::_(we_base_request::BOOL, "multiple"));
		break;
}
$fs->printHTML(we_base_request::_(we_base_request::INT, 'what', we_selector_file::FRAMESET));
