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
class we_exim_refTable{
	private $Storage = [];
	var $current = 0;
	var $Users = []; // username => id

	function add($object, $extra = []){
		$rd = new we_exim_refData();
		$rd->init($object, $extra);
		if($this->hasPerms($rd)){
			$this->Storage[] = $rd;
		}
	}

	function add2($properties){
		$rd = new we_exim_refData();
		foreach($properties as $k => $v){
			$rd->$k = $v;
		}
		$rd->Table = we_exim_XMLExIm::getTableForCT($rd->ContentType, (isset($rd->Table)) ? $rd->Table : '');
		if($this->hasPerms($rd)){
			$this->Storage[] = $rd;
		}
	}

	function addToUsers($ids){
		foreach($ids as $id){
			$key = basename(id_to_path($id, USER_TABLE));
			if($key){
				$this->Users[$key] = ['user' => $key, 'id' => $id];
			}
		}
	}

	private function hasPerms($rd){
		if($rd->Table){
			switch($rd->Table){
				case DOC_TYPES_TABLE:
				case CATEGORY_TABLE:
				case NAVIGATION_RULE_TABLE:
					$allowed = true;
					break;
				default:
					$q = we_exim_XMLExIm::queryForAllowed($rd->Table);
					$id = f('SELECT ID FROM ' . escape_sql_query($rd->Table) . ' WHERE ID=' . intval($rd->ID) . ' ' . $q);
					$allowed = $id ? true : false;
			}
			switch($rd->Table){
				case FILE_TABLE:
					return $allowed && we_base_permission::hasPerm('CAN_SEE_DOCUMENTS');
				case TEMPLATES_TABLE:
					return $allowed && we_base_permission::hasPerm('CAN_SEE_TEMPLATES');
				case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
					return $allowed && we_base_permission::hasPerm('CAN_SEE_OBJECTS');
				case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					return $allowed && we_base_permission::hasPerm('CAN_SEE_OBJECTFILES');
				case VFILE_TABLE:
					return $allowed && we_base_permission::hasPerm('CAN_SEE_COLLECTIONS');
				case DOC_TYPES_TABLE:
					return $allowed && we_base_permission::hasPerm('EDIT_DOCTYPE');
				case CATEGORY_TABLE:
					return $allowed && we_base_permission::hasPerm('EDIT_KATEGORIE');
				case NAVIGATION_TABLE:
					return $allowed && we_base_permission::hasPerm('EDIT_NAVIGATION');
				case FILELINK_TABLE:
					return true;
			}
		}
		switch($rd->ContentType){
			case 'weBinary':
			case we_base_ContentTypes::NAVIGATIONRULE:
			case 'weThumbnail':
				return true;
			default:
				return false;
		}
	}

	function moveItemsToEnd($ct){
		$regular = $moved = [];
		foreach($this->Storage as $elem){
			if($elem->ContentType == $ct){
				$moved[] = $elem;
			} else {
				$regular[] = $elem;
			}
		}
		$this->Storage = array_merge($regular, $moved);
	}

	function update($object){
		$param = ["ID" => $object->ID,
			"ContentType" => $object->ContentType
			];
		foreach($this->Storage as $k => $ref){
			if($ref->match($param)){
				$this->Storage[$k]->init($object);
			}
		}
	}

	function exists($params){
		foreach($this->Storage as $ref){
			if($ref->match($params)){
				return true;
			}
		}
		return false;
	}

	function setProp($id, $name, $value){
		foreach($this->Storage as $ref){
			if($ref->match($id)){
				$this->$name = $value;
				return true;
			}
		}
		return false;
	}

	function reset(){
		$this->current = 0;
	}

	function getNext(){
		if(isset($this->Storage[$this->current])){
			$id = $this->current;
			$this->current++;
			return $this->Storage[$id];
		}
		$this->reset();
		return null;
	}

	function getLast(){
		if($this->Storage){
			return $this->Storage[count($this->Storage) - 1];
		}
		return null;
	}

	function getRef($param){
		foreach($this->Storage as $ref){
			if($ref->match($param)){
				return $ref;
			}
		}
		return false;
	}

	public function __sleep(){
		return ['Storage'];
	}

	public function getCount(){
		return count($this->Storage);
	}

	function getNewOwnerID($id){
		foreach($this->Users as $user){
			if($user['id'] == $id){
				if(($newid = f('SELECT ID FROM ' . USER_TABLE . ' WHERE Username=\'' . $GLOBALS['DB_WE']->escape($user['user']) . '\''))){
					return $newid;
				}
			}
		}
		return 0;
	}

}
