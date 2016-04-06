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
$class = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . $class . '&';

switch($class){
	case 'we_navigation_dirSelector':
		$fs = new we_navigation_dirSelector(we_base_request::_(we_base_request::INT, 'id', we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1)), we_base_request::_(we_base_request::CMD, 'we_cmd', isset($JSIDName) ? $JSIDName : '', 2), we_base_request::_(we_base_request::CMD, 'we_cmd', isset($JSTextName) ? $JSTextName : '', 3), we_base_request::_(we_base_request::CMD, 'we_cmd', isset($JSCommand) ? $JSCommand : '', 4), we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''));
		break;
	case 'we_banner_dirSelector':
		if(($id = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1)) !== false){
			$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
		} else {
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$JSIDName = $JSTextName = $JSCommand = '';
		}

		$fs = new we_banner_dirSelector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''));
		break;
	case 'we_banner_selector':
		if(($id = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1)) !== false){
			$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
		} else {
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$JSIDName = $JSTextName = $JSCommand = '';
		}

		$fs = new we_banner_selector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''));
		break;
	case 'we_newsletter_dirSelector':
		if(($cmd = we_base_request::_(we_base_request::CMD, 'we_cmd', false, 4)) !== false){
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1);
			$JSIDName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2));
			$JSTextName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3));
			$JSCommand = $cmd;
			$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 6);
			$filter = we_base_request::_(we_base_request::RAW, 'we_cmd', 7, '');
			$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8);
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
			$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
		}

		$fs = new we_newsletter_dirSelector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), 0, we_base_request::_(we_base_request::STRING, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''), $rootDirID, $multiple);
		break;

	case 'we_export_dirSelector':
		if(($cmd = we_base_request::_(we_base_request::CMD, 'we_cmd', false, 4)) !== false){
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
			$JSIDName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2));
			$JSTextName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3));
			$JSCommand = stripslashes($cmd);
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
		}

		$fs = new we_export_dirSelector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', ''), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''));
		break;

	case 'we_users_selector':
		we_html_tools::protect(array('NEW_USER', 'NEW_GROUP', 'SAVE_USER', 'SAVE_GROUP', 'DELETE_USER', 'DELETE_GROUP'));
		if(($idname = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 1))){
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4);
			$JSIDName = $idname;
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
			$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
			$filter = we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 3);
			$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8);
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
			$filter = we_base_request::_(we_base_request::STRING, 'filter', '');
			$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
		}

		$fs = new we_users_selector($id, USER_TABLE, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), $rootDirID, $filter, $multiple);
		break;
	case 'we_voting_dirSelector':
		if(($cmd = we_base_request::_(we_base_request::CMD, 'we_cmd', false, 2)) !== false){
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
			$JSIDName = $cmd;
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
		}

		$fs = new we_voting_dirSelector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''));
		break;

	case 'we_selector_image':
		if(($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', false, 2)) !== false){
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
			$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
			$startID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 6);
			$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
			$open_doc = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 9);
			$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 10);
			$canSelectDir = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 11);
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$table = we_base_request::_(we_base_request::TABLE, 'table', (defined('FILE_TABLE') ? FILE_TABLE : 'FF'));
			$startID = we_base_request::_(we_base_request::INT, 'startID');
			$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
			$open_doc = we_base_request::_(we_base_request::BOOL, 'open_doc');
			$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
			$canSelectDir = we_base_request::_(we_base_request::BOOL, 'canSelectDir');
		}
		$fs = new we_selector_image($id, $table, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), 0, we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''), $rootDirID, $open_doc ? ($table == (defined('FILE_TABLE') ? FILE_TABLE : 'FF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_FILES') : ($table == (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_OBJECTS') : false)) : false, $multiple, $canSelectDir, $startID);
		break;
	case 'we_customer_selector':
		if(($JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3))){
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
			$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
			$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', '', 9);
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
			$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
			$id = we_base_request::_(we_base_request::STRING, 'id', 0);
		}
		$fs = new we_customer_selector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), $rootDirID, '', $multiple);
		break;
	case 'we_selector_category':
		$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
		$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
		$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
		if($JSCommand || ($JSIDName && $JSTextName)){
			$noChoose = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8);
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
			$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$noChoose = we_base_request::_(we_base_request::BOOL, 'noChoose');
			$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
		}
		$fs = new we_selector_category($id, CATEGORY_TABLE, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editCatID', 0), we_base_request::_(we_base_request::STRING, 'we_EntryText', ''), $rootDirID, $noChoose);
		break;
	case 'we_selector_document':
		if(($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2))){
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
			$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
			$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
			$filter = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 8);
			$open_doc = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 9);
			$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 10);
			$canSelectDir = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 11);
			if($filter === we_base_ContentTypes::IMAGE){
				t_e('notice', 'called incorrect selector');
			}
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$table = we_base_request::_(we_base_request::TABLE, 'table', (defined('FILE_TABLE') ? FILE_TABLE : 'FF'));
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
			$filter = we_base_request::_(we_base_request::STRING, 'filter', '');
			$open_doc = we_base_request::_(we_base_request::BOOL, 'open_doc');
			$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
			$canSelectDir = we_base_request::_(we_base_request::BOOL, 'canSelectDir');
		}
		$fs = new we_selector_document($id, $table, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), 0, we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''), $filter, $rootDirID, $open_doc ? ($table == (defined('FILE_TABLE') ? FILE_TABLE : 'FF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_FILES') : ($table == (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_OBJECTS') : false)) : false, $multiple, $canSelectDir);
		break;
	case 'we_selector_directory':
		if(($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2))){
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
			$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
			$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
			$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', '', 9);
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$table = we_base_request::_(we_base_request::TABLE, 'table', FILE_TABLE);
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
			$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
		}

		$fs = new we_selector_directory($id, $table, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), 0, we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''), $rootDirID, $multiple);
		break;
	case 'we_selector_delete':
		if(($id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1))){
			$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);
		} else {
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$table = we_base_request::_(we_base_request::TABLE, 'table', FILE_TABLE);
		}
		$fs = new we_selector_delete($id, $table);
		break;
	case 'we_selector_file':
		if(($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2))){
			$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
			$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
			$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
			$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
			$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
			$filter = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 8);
			$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', '', 9);
		} else {
			$JSIDName = $JSTextName = $JSCommand = '';
			$id = we_base_request::_(we_base_request::INT, 'id', 0);
			$table = we_base_request::_(we_base_request::TABLE, 'table', FILE_TABLE);
			$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
			$filter = we_base_request::_(we_base_request::RAW, 'filter', '');
			$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
		}

		$fs = new we_selector_file($id, $table, $JSIDName, $JSTextName, $JSCommand, isset($order) ? $order : we_base_request::_(we_base_request::STRING, 'order', ''), $rootDirID, $multiple, $filter);
		break;
	default:
		t_e('selector ' . $class . ' not found');
		return'';
}
$fs->printHTML(we_base_request::_(we_base_request::INT, 'what', we_selector_file::FRAMESET));
