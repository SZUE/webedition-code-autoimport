<?php

/**
 * webEdition CMS
 *
 * $Rev: 9287 $
 * $Author: mokraemer $
 * $Date: 2015-02-11 14:38:50 +0100 (Mi, 11 Feb 2015) $
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
class rpcGetItemsFromDBCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();

		if(!($id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'id'))){
			$resp->setData("error", array("Missing field id"));
		} else {
			
			$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 'table');
			$type = we_base_request::_(we_base_request::STRING, 'we_cmd', 'item', 'type');
			$ct = we_base_request::_(we_base_request::STRING_LIST, 'we_cmd', '', 'ct');t_e('ct', $ct);
			$recursive = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'recursive');
			$resp->setData("itemsArray", $this->getDocsInFolder($id, $table, ($type === 'item'), $recursive, $ct, true));
		}

		return $resp;
	}

	function getDocsInFolder($id, $table, $idToPath = false, $recursive = true, $contentTypes = array(), $checkWs = true, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		$result = $todo = array();
		
		$wspaces = array();
		if(($ws = get_ws($table))){
			$wsPathArray = id_to_path($ws, $table, $DB_WE, false, true);

			foreach($wsPathArray as $path){
				$wspaces[] = " Path LIKE '" . $DB_WE->escape($path) . "/%' OR " . getQueryParents($path);
				while($path != '/' && $path != '\\' && $path){
					$parentpaths[] = $path;
					$path = dirname($path);
				}
			}
		} elseif(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!permissionhandler::hasPerm("ADMINISTRATOR"))){
			$ac = we_users_util::getAllowedClasses($DB_WE);
			foreach($ac as $cid){
				$path = id_to_path($cid, OBJECT_TABLE);
				$wspaces[] = " Path LIKE '" . $DB_WE->escape($path) . "/%' OR Path='" . $DB_WE->escape($path) . "'";
			}
		}
		$wsQuery = ($checkWs && $wspaces ? ' AND (' . implode(' OR ', $wspaces) . ') ' : ' OR RestrictOwners=0 ' );

		$db->query('SELECT ID,Path,ContentType FROM ' . $db->escape($table) . ' WHERE ' . (boolval($idToPath) ? 'ID' : 'ParentID') . '=' . intval($id) . ' AND ((1' . we_users_util::makeOwnersSql() . ') ' . $wsQuery . ')');
		while($db->next_record()){
			if(!$idToPath && $recursive && $db->f('ContentType') === 'folder'){
				$todo[] = $db->f('ID');
			} 

			if((empty($contentTypes) || in_array($db->f('ContentType'), $contentTypes)) && !($db->f('ContentType') === 'folder')){
				$result[] = array('id' => $db->f('ID'), 'path' => $db->f('Path'), 'ct' => $db->f('ContentType'));
			}
		}

		foreach($todo as $id){
			$result = array_merge($result, $this->getDocsInFolder($id, $table, false, true, $contentTypes, false, $db));
		}

		return $result;
	}

}
