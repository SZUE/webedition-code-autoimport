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
abstract class we_users_util {

	private static function getGroupList($id){
		if(!$id){
			return array();
		}
		$ret = array();
		$db_tmp = new DB_WE();
		$db_tmp->query('SELECT ID,username WHERE ParentID=' . intval($id) . ' AND Type=1');
		while($db_tmp->next_record()){
			$ret[$db_tmp->f("ID")] = $db_tmp->f("username");
			$section = self::getGroupList($db_tmp->f("ID"));
			$ret = array_merge($ret, $section);
		}
		return $ret;
	}

	private static function getUserTree($id){
		$ret = array();
		$db_tmp = new DB_WE();
		$db_tmp->query('SELECT ID,username,Type WHERE ParentID=' . intval($id));
		while($db_tmp->next_record()){
			$ret[$db_tmp->f("ID")] = array(
				"name" => $db_tmp->f("username"),
				"ParentID" => $id,
				"Type" => $db_tmp->f("Type")
			);
			$section = self::getUserTree($db_tmp->f("ID"));
			$ret = array_merge($ret, $section);
		}
		return $ret;
	}

	static function isUserInUsers($uid, $users, we_database_base $db = null){ // $users can be a csv string or an array
		if(permissionhandler::hasPerm("ADMINISTRATOR")){
			return true;
		}
		if(!is_array($users)){
			$users = makeArrayFromCSV($users);
		}

		if(in_array($uid, $users)){
			return true;
		}
		$db = $db ? $db : new DB_WE();

		$aliases = self::getAliases($uid, $db);
		foreach($aliases as $aid){
			if(in_array($aid, $users)){
				return true;
			}
		}

		foreach($users as $user){
			$isGroup = f('SELECT IsFolder FROM ' . USER_TABLE . ' WHERE ID=' . intval($user), "", $db);
			if($isGroup){
				if(self::isUserInGroup($uid, $user, $db)){
					return true;
				}
				foreach($aliases as $aid){
					if(self::isUserInGroup($aid, $user, $db)){
						return true;
					}
				}
			}
		}

		return false;
	}

	static function isUserInGroup($uid, $groupID, we_database_base $db = null){
		$db = $db ? $db : new DB_WE();
		$pid = f('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($uid), "ParentID", $db);
		if($pid == $groupID){
			return true;
		} else if($pid != 0){
			return self::isUserInGroup($pid, $groupID);
		}
		return false;
	}

	static function addAllUsersAndGroups($uid, &$arr){
		$db = new DB_WE();
		$db->query('SELECT ID,IsFolder FROM ' . USER_TABLE . ' WHERE ParentID=' . intval($uid));
		while($db->next_record()){
			$arr[] = $db->f("ID");
			if($db->f("IsFolder")){
				self::addAllUsersAndGroups($db->f("ID"), $arr);
			}
		}
	}

	static function removeNonAsociative(&$array){
		if(!is_array($array)){
			return $array;
		}

		reset($array);

		while(list($k) = each($array)){
			if((string) (int) $k == $k){
				unset($array[$k]);
			}
		}

		return $array;
	}

	static function getUsersForDocWorkspace(we_database_base $db, $id, $wsField = "workSpace"){
		$ids = (is_array($id) ? $id : array($id));

		$where = array();
		foreach($ids as $id){
			$where[] = $wsField . ' LIKE "%,' . $id . ',%"';
		}

		$db->query('SELECT ID,username FROM ' . USER_TABLE . ' WHERE ' . implode(' OR ', $where));
		return $db->getAllFirst(false);
	}

	static function getAliases($id, we_database_base $db){
		$db->query('SELECT ID FROM ' . USER_TABLE . ' WHERE Alias=' . intval($id));
		return $db->getAll(true);
	}

	public static function isOwner($csvOwners){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		$ownersArray = makeArrayFromCSV($csvOwners);
		return (in_array($_SESSION['user']['ID'], $ownersArray)) || self::isUserInUsers($_SESSION['user']['ID'], $csvOwners);
	}

	public static function userIsOwnerCreatorOfParentDir($folderID, $tab){
		if(($tab != FILE_TABLE && $tab != OBJECT_FILES_TABLE) ||
			(permissionhandler::hasPerm('ADMINISTRATOR') || ($folderID == 0))){
			return true;
		}
		$db = new DB_WE();
		$tmp = getHash('SELECT RestrictOwners,Owners,CreatorID FROM ' . $tab . ' WHERE ID=' . intval($folderID), $db);
		if(!$tmp){
			return true;
		}
		if($tmp['RestrictOwners']){
			$ownersArr = explode(',', $tmp['Owners']);
			foreach($ownersArr as $uid){
				we_users_util::addAllUsersAndGroups($uid, $ownersArr);
			}
			$ownersArr[] = $tmp['CreatorID'];
			$ownersArr = array_unique($ownersArr);
			return (in_array($_SESSION['user']['ID'], $ownersArr));
		}
		$pid = f('SELECT ParentID FROM ' . $tab . ' WHERE ID=' . intval($folderID), '', $db);
		return self::userIsOwnerCreatorOfParentDir($pid, $tab);
	}

	public static function canEditModule($modName){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		$data = we_base_moduleInfo::getModuleData($modName);
		return we_base_menu::isEnabled($data['perm']);
	}

	public static function makeOwnersSql($useCreatorID = true){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return '';
		}
		$aliases = self::getAliases($_SESSION['user']['ID'], $GLOBALS['DB_WE']);
		$aliases[] = $_SESSION['user']['ID'];
		$q = array();
		if($useCreatorID){
			$q[] = 'CreatorID IN ("' . implode('","', $aliases) . '")';
		}
		foreach($aliases as $id){
			$q [] = 'Owners LIKE "%,' . intval($id) . ',%"';
		}
		$groups = array($_SESSION['user']['ID']);
		we_getParentIDs(USER_TABLE, $_SESSION['user']['ID'], $groups, $GLOBALS['DB_WE']);
		foreach($aliases as $id){
			we_getParentIDs(USER_TABLE, $id, $groups, $GLOBALS['DB_WE']);
		}

		foreach($groups as $id){
			$q[] = "Owners LIKE '%," . intval($id) . ",%'";
		}
		return ' AND ( RestrictOwners=0 OR (RestrictOwners=1 AND (' . implode(' OR ', $q) . '))) ';
	}

	public static function getAllowedClasses(we_database_base $db = null){
		if(!defined('OBJECT_FILES_TABLE')){
			return '';
		}
		$db = ($db ? $db : new DB_WE());
		$out = array();
		$ws = get_ws();
		$ofWs = get_ws(OBJECT_FILES_TABLE);
		$ofWsArray = makeArrayFromCSV(id_to_path($ofWs, OBJECT_FILES_TABLE));
		if(intval($ofWs) == 0){
			$ofWs = 0;
		}
		if(intval($ws) == 0){
			$ws = 0;
		}
		$db->query('SELECT ID,Workspaces,Path FROM ' . OBJECT_TABLE . ' WHERE IsFolder=0');

		while($db->next_record()){
			$path = $db->f('Path');
			if(!$ws || permissionhandler::hasPerm('ADMINISTRATOR') || (!$db->f('Workspaces')) || in_workspace($db->f('Workspaces'), $ws, FILE_TABLE, null, true)){
				$path2 = $path . '/';
				if(!$ofWs || permissionhandler::hasPerm('ADMINISTRATOR')){
					$out[] = $db->f('ID');
				} else {

// object Workspace check (New since Version 4.x)
					foreach($ofWsArray as $w){
						if($w == $db->f('Path') || (strlen($w) >= strlen($path2) && substr($w, 0, strlen($path2)) == ($path2))){
							$out[] = $db->f('ID');
							break;
						}
					}
				}
			}
		}

		return $out;
	}

}
