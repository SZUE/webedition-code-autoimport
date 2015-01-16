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
class rpcCopyNavigationFolderCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();
		$cmd0 = we_base_request::_(we_base_request::FILE, 'we_cmd', false, 0);
		$pathid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3);
		if($cmd0 &&
				($folder = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1)) &&
				($path = we_base_request::_(we_base_request::FILE, 'we_cmd', '', 2)) &&
				$pathid &&
				(strpos($path, $cmd0) === false || strpos($path, $cmd0) > 0)
		){

			$db = $GLOBALS['DB_WE'];
			$path = f('SELECT Path FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . $pathid);
			$db->query('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE "' . $db->escape($path) . '/%" ORDER BY IsFolder DESC, Path');
			$result = $db->getAll();

			$folders = array($folder);
			$mapedId = array($pathid => $folder);
			$itemsQuery = array();

			foreach($result as $row){
				$querySet = array();
				foreach($row as $key => $val){
					switch($key){
						case "ID" :
							$querySet[] = 'DEFAULT';
							break;
						case "Path" :
							$path = str_replace($path, $cmd0, $val);
							$querySet[] = '"' . $db->escape($path) . '"';
							break;
						case "ParentID" :
							$querySet [] = (isset($mapedId[$val]) ? intval($mapedId[$val]) : 0);
							break;
						default :
							$querySet [] = '"' . $db->escape($val) . '"';
					}
				}
				$querySet = '(' . implode(',', $querySet) . ')';
				if($row['IsFolder']){
					$db->query('INSERT INTO ' . NAVIGATION_TABLE . ' VALUES ' . $querySet);
					$mapedId[$row['ID']] = $db->getInsertId();
					$folders[] = $mapedId[$row['ID']];
				} else {
					$itemsQuery[] = $querySet;
				}
			}
			if($itemsQuery){
				$db->query('INSERT INTO ' . NAVIGATION_TABLE . ' VALUES ' . implode(',', $itemsQuery));
			}
			foreach($folders as $folder){
				$newNavi = new we_navigation_navigation($folder);
				$newNavi->save(false, true);
			}
			$resp->setData('status', 'ok');
			$resp->setData('folders', $folders);
		} else {
			$resp->setData('folders', "");
		}

		return $resp;
	}

}
