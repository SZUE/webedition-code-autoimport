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
class rpcCopyNavigationFolderCmd extends we_rpc_cmd{

	function execute(){
		$resp = new we_rpc_response();
		$targetFolder = we_base_request::_(we_base_request::FILE, 'we_cmd', false, 0);
		$sourceFolderID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3);

		if($targetFolder &&
			($targetFolderID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1)) &&
			($sourceFolder = we_base_request::_(we_base_request::FILE, 'we_cmd', '', 2)) &&
			$sourceFolderID &&
			(strpos($sourceFolder, $targetFolder) === false || strpos($sourceFolder, $targetFolder) > 0)
		){

			$db = $GLOBALS['DB_WE'];
			$sourceFolder = f('SELECT Path FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . $sourceFolderID);
			$result = $db->getAllq('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE "' . $db->escape($sourceFolder) . '/%" ORDER BY IsFolder DESC, Path ASC');

			$folders = array($targetFolderID);
			$mapedId = array($sourceFolderID => array($targetFolderID, $targetFolder));
			$itemsQuery = array();

			foreach($result as $row){
				$querySet = array();
				foreach($row as $key => $val){
					switch($key){
						case 'ID' :
							$querySet[] = 'DEFAULT';
							break;
						case 'Path' :
							$oldparent = $row['ParentID'];
							if(!isset($mapedId[$oldparent])){
								t_e('Parentid not found');
							}
							$newPath = $mapedId[$oldparent][1] . '/' . $row['Text'];
							$querySet[] = '"' . $db->escape($newPath) . '"';
							/* SELECT n.ID,n.Path,CONCAT(COALESCE(np.Path,""),"/",n.Text) FROM `tblnavigation` n left join tblnavigation np ON n.ParentID=np.ID WHERE n.Path!=CONCAT(COALESCE(np.Path,""),"/",n.Text) */
							break;
						case 'ParentID' :
							if(!isset($mapedId[$val])){
								t_e('Parentid not found');
							}
							$querySet [] = (isset($mapedId[$val]) ? intval($mapedId[$val][0]) : 0);
							break;
						default :
							$querySet [] = '"' . $db->escape($val) . '"';
					}
				}
				$querySet = '(' . implode(',', $querySet) . ')';
				if($row['IsFolder']){
					$db->query('INSERT INTO ' . NAVIGATION_TABLE . ' VALUES ' . $querySet);
					$mapedId[$row['ID']] = array($db->getInsertId(), $newPath);
					$folders[] = $mapedId[$row['ID']][0];
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
