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

/**
 * class    permissionhandler
 * @desc    This class looks for the needed permissions to perform a single
  action. Actions are normaly submitted via we_cmds. There are two
  ways to use this class.
  a - get all needed permissions for a requested command.
  b - checks, if the user has the right to perform the requested action.
 *
 */
abstract class permissionhandler{

	public static function hasPerm($perm, $or = true){
		if(empty($_SESSION['user']['isWeSession'])){
			return false;
		}
		if((!empty($_SESSION['perms']['ADMINISTRATOR']))){
			return true;
		}
		if(empty($perm)){
			return true;
		}
		$perm = is_array($perm) ? $perm : [$perm];
		$ret = ($or ? false : true);
		foreach($perm as $cur){
			$ret = ($or ?
					$ret || (!empty($_SESSION['perms'][$cur])) :
					$ret && (!empty($_SESSION['perms'][$cur]))
				);
		}
		return $ret;
	}

	/**
	 * permissionhandler::getPermissionsForAction()
	 * @desc    This function looks in the array $knownActions for the needed
	  permissions for an action.
	  It returns either an array of permissions, or "none", when no
	  permission is needed for this action, or the action is not
	  listed.
	 *
	 * @param   requestedAction     string - the action the user wants to do (we_cmd[0])
	 * @param   paramater           string - a parameter to specify the action
	 *
	 * @return  array               of the needed Permissions
	 */
	static function getPermissionsForAction($requestedAction, $parameter){

		/*
		  Here is the Array, which gives the connection between the action the user wants to make
		  and the needed permissions.
		  - first index is the action, the user wants to make
		  - the second is a paramter the action needs.
		  - the last is an array of strings containing all permissions, the user must have
		  each entry of the array reflects a condition, when one condition matches - the action is allowed.
		  - Several AND conditions in one conditionstring are seperated with ","

		  here are two example usages:

		  $knownActions["switch_edit_page"]["we_base_constants::WE_EDITPAGE_PROPERTIES"] = array("CAN_SEE_PROPERTIES");
		  action: switch editPage to property-page - needed right is "CAN_SEE_PROPERTIES"

		  $knownActions["another_action"]["another_para"] = array("Right1","Right2,Right3");
		  action: another_action with another_para - needed right can be: Right1 OR Right2 AND Right3

		 */
		//	The first entries are no we_cmd[0], but sometimes needed.
		$knownActions = ['switch_edit_page' => ['we_base_constants::WE_EDITPAGE_PROPERTIES' => ['CAN_SEE_PROPERTIES'],
				0 => ["CAN_SEE_PROPERTIES"],
				'we_base_constants::WE_EDITPAGE_INFO' => ['CAN_SEE_INFO'],
				2 => ["CAN_SEE_INFO"],
				'we_base_constants::WE_EDITPAGE_VALIDATION' => ['CAN_SEE_VALIDATION'],
				10 => ['CAN_SEE_VALIDATION'],
			],
			//	Is user allowed to work in normal mode or only in SEEM
			"work_mode" => [we_base_constants::MODE_NORMAL => ["CAN_WORK_NORMAL_MODE"]
			],
			"header" => ["with_java" => ["CAN_SEE_MENUE"]
			]
		];

		return (isset($knownActions[$requestedAction][$parameter]) ?
				$knownActions[$requestedAction][$parameter] : 'none');
	}

	/**
	 * permissionhandler::isUserAllowedForAction()
	 * @desc    When a user is allowed to do an action with a certain parameter,
	  true is returned, otherwise false.
	 *
	 * @see     permissionhandler::getPermissionsForAction
	 *
	 * @param   requestedAction     string - the action the user wants to do (we_cmd[0])
	 * @param   paramater           string - a parameter to specify the action
	 *
	 * @return  boolean
	 */
	static function isUserAllowedForAction($requestedAction, $parameter){
		$neededPerm = permissionhandler::getPermissionsForAction($requestedAction, $parameter);
		//  An array is returned, check the rights.
		if(!is_array($neededPerm)){
			//  no permissions are needed for this action
			return true;
		}
		foreach($neededPerm as $val){
			$allowed = true;
			$perms = explode(',', $val);
			foreach($perms as $perm){
				if(!permissionhandler::hasPerm($perm)){
					$allowed = false;
					break;
				}
			}

			if($allowed){
				return true;
			}
		}

		return false;
	}

	static function checkIfRestrictUserIsAllowed($id, $table, we_database_base $DB_WE){
		$row = getHash('SELECT CreatorID=' . $_SESSION['user']['ID'] . ' AS isCreator,RestrictOwners,Owners,OwnersReadOnly FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
		if(!$row || $row['isCreator'] || permissionhandler::hasPerm('ADMINISTRATOR')){ //	Owner or admin or file doesn't exist
			return true;
		}

		if($row['RestrictOwners']){ //	check which user - group has permission
			$userArray = makeArrayFromCSV($row['Owners']);

			$allowedGroup = false;

			//	check if usergroup is allowed
			foreach($_SESSION['user']['groups'] as $nr => $userGroup){
				if(in_array($userGroup, $userArray)){
					$allowedGroup = true;
					break;
				}
			}
			if(!in_array($_SESSION['user']['ID'], $userArray) && !$allowedGroup){ //	user is no allowed user.
				return false;
			}

			//	user belongs to owners of document, check if he has only read access !!!
			if($row['OwnersReadOnly']){
				$arr = we_unserialize($row['OwnersReadOnly']);
				if(is_array($arr)){

					if(!empty($arr[$_SESSION['user']['ID']])){ //	if user is readonly user -> no delete
						return false;
					}
					if(in_array($_SESSION['user']['ID'], $userArray)){ //	user NOT readonly and in restricted -> delete allowed
						return true;
					}

					//	check if group has rights to delete
					foreach($_SESSION['user']['groups'] as $nr => $userGroup){ //	user is directly in first group
						if(!empty($arr[$userGroup])){ //	group not allowed
							return false;
						}
						if(in_array($userGroup, $userArray)){ //	group is NOT readonly and in restricted -> delete allowed
							return true;
						}
					}
				}
			}
		}
		return true;
	}

}
