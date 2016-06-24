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
class we_tool_model extends we_base_model{
	var $ID = 0;
	var $Text;
	var $ParentID = 0;
	var $Path;
	var $IsFolder;
	var $ModelClassName = __CLASS__;
	var $toolName = '';
	var $requiredFields = [];

	function __construct($table){
		parent::__construct($table);
	}

	function saveInSession(){
		$_SESSION['weS'][$this->toolName . '_session'] = $this;
	}

	function clearSessionVars(){
		if(!empty($this->toolName) && isset($_SESSION['weS'][$this->toolName . '_session'])){
			unset($_SESSION['weS'][$this->toolName . '_session']);
		}
	}

	function filenameNotValid($text = ''){
		return false;
	}

	function isRequiredField($fieldname){
		return in_array($fieldname, $this->requiredFields);
	}

	function hasRequiredFields(&$failed){
		foreach($this->requiredFields as $req){
			if(empty($this->$req)){
				$failed[] = $req;
			}
		}
		return empty($failed);
	}

	function setPath(){
		$ppath = f('SELECT Path FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->ParentID) . ';', 'Path', $this->db);
		$this->Path = $ppath . "/" . $this->Text;
	}

	function pathExists($path){
		$this->db->query('SELECT * FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($path) . '" AND ID!=' . intval($this->ID));
		return ($this->db->next_record() ? true : false);
	}

	function isSelf(){
		if($this->ID){
			$count = 0;
			$parentid = $this->ParentID;
			while($parentid != 0){
				if($parentid == $this->ID){
					return true;
				}
				$parentid = f('SELECT ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($parentid), 'ParentID', $this->db);
				$count++;
				if($count == 9999){
					return false;
				}
			}
			return false;
		} else {
			return false;
		}
	}

	function isAllowedForUser(){
		return true;
	}

	function evalPath($id = 0){
		$db_tmp = new DB_WE();
		$path = '';
		if($id == 0){
			$id = $this->ParentID;
			$path = $this->Text;
		}

		$foo = getHash('SELECT Text,ParentID FROM ' . $db_tmp->escape($this->table) . ' WHERE ID=' . intval($id), $db_tmp);
		$path = '/' . (isset($foo['Text']) ? $foo['Text'] : '') . $path;

		$pid = isset($foo['ParentID']) ? $foo['ParentID'] : '';
		while($pid > 0){
			$db_tmp->query('SELECT Text,ParentID FROM ' . $db_tmp->escape($this->table) . ' WHERE ID=' . intval($pid));
			while($db_tmp->next_record()){
				$path = '/' . $db_tmp->f('Text') . $path;
				$pid = $db_tmp->f('ParentID');
			}
		}
		return $path;
	}

	function updateChildPaths($oldpath){
		if($this->IsFolder && $oldpath != '' && $oldpath != '/' && $oldpath != $this->Path){
			$db_tmp = new DB_WE();
			$this->db->query('SELECT ID FROM ' . $db_tmp->escape($this->table) . ' WHERE Path LIKE \'' . $db_tmp->escape($oldpath) . '%\' AND ID!=' . intval($this->ID));
			while($this->db->next_record()){
				$db_tmp->query('UPDATE ' . $db_tmp->escape($this->table) . ' SET Path=\'' . $db_tmp->escape($this->evalPath($this->db->f("ID"))) . '\' WHERE ID=' . intval($this->db->f("ID")));
			}
		}
	}

	function setIsFolder($value){
		$this->IsFolder = $value;
	}

	function deleteChilds(){
		$this->db->query('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->ID));
		while($this->db->next_record()){
			$child = new $this->ModelClassName($this->db->f("ID"));
			$child->delete();
		}
	}

	function delete(){

		if(!$this->ID){
			return false;
		}
		if($this->IsFolder){
			$this->deleteChilds();
		}
		parent::delete();

		return true;
	}

}
