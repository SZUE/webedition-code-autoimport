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
		if(isset($_REQUEST['we_cmd'][0]) && $_REQUEST['we_cmd'][0] &&
			($folder=  weRequest('int', 'we_cmd',0,1)) &&
			($path = weRequest('file', 'we_cmd', '', 2)) &&
			isset($_REQUEST['we_cmd'][3]) && $_REQUEST['we_cmd'][3] &&
			(strpos($path, $_REQUEST['we_cmd'][0]) === false || strpos($path, $_REQUEST['we_cmd'][0]) > 0)
		){

			$db = $GLOBALS['DB_WE'];
			$db->query('SELECT * FROM ' . NAVIGATION_TABLE . " WHERE Path LIKE '" . $db->escape($path) . "/%' ORDER BY Path");
			$result = $db->getAll();
			$querySet = '';
			$query = '';
			$folders = array($folder);
			$mapedId = array($_REQUEST['we_cmd'][3] => $folder);
			foreach($result as $row){
				$querySet = '(';
				foreach($row as $key => $val){
					switch($key){
						case "ID" :
							$querySet .= "''";
							break;
						case "Path" :
							$path = str_replace($path, $_REQUEST['we_cmd'][0], $val);
							$querySet .= ", '" . $db->escape($path) . "'";
							break;
						case "ParentID" :
							$querySet .= ', ' . intval($mapedId[$val]);
							break;
						default :
							$querySet .= ", '$val'";
					}
				}
				$querySet .= ')';
				if($row['IsFolder']){
					if($query){
						$db->query('INSERT INTO ' . NAVIGATION_TABLE . ' VALUES ' . $query);
					}
					$db->query('INSERT INTO ' . NAVIGATION_TABLE . ' VALUES ' . $querySet);
					$mapedId[$row['ID']] = $db->getInsertId();
					$folders[] = $mapedId[$row['ID']];
					$query = "";
				} else {
					$query .= ($query ? ',' : '') . $querySet;
				}
				$lastInserted = $row['IsFolder'];
			}
			if(!$lastInserted){
				$db->query('INSERT INTO ' . NAVIGATION_TABLE . ' VALUES ' . $query);
			}
			foreach($folders as $folder){
				$newNavi = new we_navigation_navigation($folder);
				$newNavi->save();
			}
			$resp->setData("status", "ok");
			$resp->setData("folders", $folders);
		} else {
			$resp->setData("folders", "");
		}

		return $resp;
	}

}
