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
class we_tree_users extends we_tree_base{

	protected function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'users_tree.js');
	}

	function getJSStartTree(){
		if(we_base_permission::hasPerm("NEW_USER") || we_base_permission::hasPerm("NEW_GROUP") || we_base_permission::hasPerm("SAVE_USER") || we_base_permission::hasPerm("SAVE_GROUP") || we_base_permission::hasPerm("DELETE_USER") || we_base_permission::hasPerm("DELETE_GROUP")){
			$startloc = (we_base_permission::hasPerm("ADMINISTRATOR") ?
				0 :
				f('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION['user']["ID"]))
				);
		}

		return 'treeData.startloc=' . $startloc . ';' .
			parent::getJSStartTree();
	}

	public static function getItems($ParentId, $Offset = 0, $Segment = 500, $sort = false){
		$items = [];
		$db = new DB_WE();
		if(we_base_permission::hasPerm(['NEW_USER', 'NEW_GROUP', 'SAVE_USER', 'SAVE_GROUP', 'DELETE_USER', 'DELETE_GROUP'])){

			$parent_path = (we_base_permission::hasPerm("ADMINISTRATOR") ?
				'/' :
				str_replace("\\", "/", dirname(f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION['user']['ID']), '', $db))));

			$db->query('SELECT ID,ParentID,username,Type,Permissions,LoginDenied FROM ' . USER_TABLE . ' WHERE Path LIKE "' . $db->escape($parent_path) . '%" AND ParentID=' . $ParentId . ' ORDER BY username ASC');

			while($db->next_record()){
				switch(($type = $db->f('Type'))){
					case we_users_user::TYPE_USER_GROUP:
						$items[] = [
							'id' => intval($db->f('ID')),
							'parentid' => intval($db->f('ParentID')),
							'text' => addslashes($db->f('username')),
							'typ' => 'group',
							'open' => 0,
							'contenttype' => 'we/userGroup',
							'table' => USER_TABLE,
							'loaded' => 0,
							'checked' => false,
						];
						break;
					default:
						$p = we_unserialize($db->f('Permissions'));

						$items[] = [
							'id' => intval($db->f('ID')),
							'parentid' => intval($db->f('ParentID')),
							'text' => addslashes($db->f('username')),
							'typ' => 'item',
							'open' => 0,
							'contenttype' => ($db->f('Type') == we_users_user::TYPE_ALIAS ? 'we/alias' : 'we/user'),
							'table' => USER_TABLE,
							'class' => (!empty($p['ADMINISTRATOR']) ? 'bold ' : '') . ($db->f('LoginDenied') ? 'red' : '')
						];
				}
			}
		}
		return $items;
	}

}
