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

	var $Storage = array();
	var $current = 0;
	var $Users = array(); // username => id

	function add($object, $extra = array()){
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
		/* 			if($handle_owners){
		  if(isset($properties['Table'])) $table = $properties['Table'];
		  else $table = weXMLExIm::getTableForCT($properties['ContentType']);
		  $db = new DB_WE();
		  $metadata = $db->metadata($table);
		  $tables = array(FILE_TABLE);
		  if(defined('OBJECT_TABLE')){
		  $tables[] = OBJECT_FILES_TABLE;
		  $tables[] = OBJECT_TABLE;
		  }
		  if(in_array($table,$tables)){
		  $fields = getHash('SELECT CreatorID,Owners FROM '.$table.' WHERE ID=\''.$properties['ID'].'\'',$db);
		  $ids = array($fields['CreatorID']);
		  $ids = array_merge($ids,makeArrayFromCSV($fields['Owners']));
		  $this->addToUsers($ids);
		  }

		  } */
		$rd->Table = we_exim_XMLExIm::getTableForCT($rd->ContentType, (isset($rd->Table)) ? $rd->Table : '');
		if($this->hasPerms($rd)){
			$this->Storage[] = $rd;
		}
	}

	function addToUsers($ids){
		foreach($ids as $id){
			$key = basename(id_to_path($id, USER_TABLE));
			if($key){
				$this->Users[$key] = array('user' => $key, 'id' => $id);
			}
		}
	}

	function hasPerms($rd){
		if($rd->Table){
			$allowed = true;
			if($rd->Table != DOC_TYPES_TABLE && $rd->Table != CATEGORY_TABLE){
				$q = we_exim_XMLExIm::queryForAllowed($rd->Table);
				$id = f('SELECT ID FROM ' . escape_sql_query($rd->Table) . ' WHERE ID=' . intval($rd->ID) . ' ' . $q, 'ID', new DB_WE());
				$allowed = $id ? true : false;
			}
			switch($rd->Table){
				case FILE_TABLE:
					return $allowed && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS');
				case TEMPLATES_TABLE:
					return $allowed && permissionhandler::hasPerm('CAN_SEE_TEMPLATES');
				case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
					return $allowed && permissionhandler::hasPerm('CAN_SEE_OBJECTS');
				case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					return $allowed && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES');
				case VFILE_TABLE:
					return $allowed && permissionhandler::hasPerm('CAN_SEE_COLLECTIONS');
				case DOC_TYPES_TABLE:
					return $allowed && permissionhandler::hasPerm('EDIT_DOCTYPE');
				case CATEGORY_TABLE:
					return $allowed && permissionhandler::hasPerm('EDIT_KATEGORIE');
				case NAVIGATION_TABLE:
					return $allowed && permissionhandler::hasPerm('EDIT_NAVIGATION');
			}
		}
		switch($rd->ContentType){
			case 'weBinary':
			case 'weNavigationRule':
			case 'weThumbnail':
				return true;
			default:
				return false;
		}
	}

	function moveItemsToEnd($ct){
		$regular = array();
		$moved = array();
		for($i = 0; $i < count($this->Storage); $i++){
			if($this->Storage[$i]->ContentType == $ct){
				$moved[] = $this->Storage[$i];
			} else {
				$regular[] = $this->Storage[$i];
			}
		}
		$this->Storage = array_merge($regular, $moved);
	}

	function update($object){
		$param = array(
			"ID" => $object->ID,
			"ContentType" => $object->ContentType
		);
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

	function getLastCount(){
		return count($this->Storage);
	}

	function getRef($param){
		foreach($this->Storage as $ref){
			if($ref->match($param)){
				return $ref;
			}
		}
		return false;
	}

	function RefTable2Array($full = true){
		$out = array();
		foreach($this->Storage as $ref){
			$item = array();
			$vars = array_keys(get_object_vars($ref));
			foreach($vars as $prop){
				if($full || $prop != 'elements'){
					$item[$prop] = $ref->$prop;
				}
			}
			$out[] = $item;
		}

		return $out;
	}

	function Array2RefTable($RefArray, $update = false){
		if(!$update){
			$this->Storage = array();
		}
		foreach($RefArray as $ref){
			$data = new we_exim_refData();
			foreach($ref as $k => $v){
				$data->$k = $v;
			}
			$this->Storage[] = $data;
		}
	}

	function getNewOwnerID($id){
		$db = new DB_WE();
		foreach($this->Users as $user){
			if($user['id'] == $id){
				$newid = f('SELECT ID FROM ' . USER_TABLE . ' WHERE Username=\'' . $db->escape($user['user']) . '\'', 'ID', $db);

				if($newid){
					return $newid;
				}
			}
		}
		return 0;
	}

}
