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
class we_users_tree extends weTree{

	function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'users_tree.js');
	}

	function getJSStartTree(){
		$db = new DB_WE();
		if(permissionhandler::hasPerm("NEW_USER") || permissionhandler::hasPerm("NEW_GROUP") || permissionhandler::hasPerm("SAVE_USER") || permissionhandler::hasPerm("SAVE_GROUP") || permissionhandler::hasPerm("DELETE_USER") || permissionhandler::hasPerm("DELETE_GROUP")){
			if(permissionhandler::hasPerm("ADMINISTRATOR")){
				$parent_path = '/';
				$startloc = 0;
			} else {
				$foo = getHash('SELECT Path,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION["user"]["ID"]), $db);
				$parent_path = str_replace("\\", "/", dirname($foo["Path"]));
				$startloc = $foo['ParentID'];
			}
		}

		return '
function startTree(){
	frames={
		"top":' . $this->topFrame . ',
		"cmd":' . $this->cmdFrame . '
	};
	var table="' . USER_TABLE . '";
	treeData.frames=frames;
	treeData.startloc=' . $startloc . ';
	frames.cmd.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&pid=0";
}';
	}

	public static function getItems($ParentId, $Offset = 0, $Segment = 500){
		$items = array();
		$db = new DB_WE();
		if(permissionhandler::hasPerm("NEW_USER") || permissionhandler::hasPerm("NEW_GROUP") || permissionhandler::hasPerm("SAVE_USER") || permissionhandler::hasPerm("SAVE_GROUP") || permissionhandler::hasPerm("DELETE_USER") || permissionhandler::hasPerm("DELETE_GROUP")){

			$parent_path = (permissionhandler::hasPerm("ADMINISTRATOR") ?
					'/' :
					str_replace("\\", "/", dirname(f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION["user"]["ID"]), $db))));

			$db->query('SELECT ID,ParentID,Text,Type,Permissions,LoginDenied FROM ' . USER_TABLE . ' WHERE Path LIKE "' . $db->escape($parent_path) . '%" AND ParentID=' . $ParentId . ' ORDER BY Text ASC');

			while($db->next_record()){
				switch(($type = $db->f('Type'))){
					case we_users_user::TYPE_USER_GROUP:
						$items[] = array(
							'icon' => 'usergroup.gif',
							'id' => $db->f('ID'),
							'parentid' => $db->f("ParentID"),
							'text' => addslashes($db->f("Text")),
							'typ' => 'group',
							'open' => 0,
							'contentType' => 'we/userGroup',
							'table' => USER_TABLE,
							'loaded' => 0,
							'checked' => false,
						);
						break;
					default:
						$p = we_unserialize($db->f("Permissions"));

						$items[] = array(
							'icon' => ($type == we_users_user::TYPE_ALIAS ? 'user_alias.gif' : 'user.gif'),
							'id' => $db->f('ID'),
							'parentid' => $db->f("ParentID"),
							'text' => addslashes($db->f("Text")),
							'typ' => 'item',
							'open' => 0,
							'contentType' => ($db->f("Type") == we_users_user::TYPE_ALIAS ? 'we/alias' : 'we/user'),
							'table' => USER_TABLE,
							'class' => (!empty($p["ADMINISTRATOR"]) ? 'bold ' : '') . ($db->f("LoginDenied") ? 'red' : '')
						);
				}
			}
		}
		return $items;
	}

}
