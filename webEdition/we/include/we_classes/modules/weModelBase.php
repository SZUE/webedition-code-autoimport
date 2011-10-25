<?php
/**
 * webEdition CMS
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
 * @package    webEdition_modules
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

/**
 * Definition of webEdition Base Model
 *
 */
class weModelBase {

	var $db;
	var $table = '';
	var $persistent_slots = array();
	var $keys = array("ID");
	var $isnew = true;

	/**
	 * Default Constructor
	 */
	function weModelBase($table) {
		$this->db = new DB_WE();
		$this->table = $table;
		$this->loadPresistents();
	}

	function loadPresistents() {
		$this->persistent_slots = array();
		$tableInfo = $this->db->metadata($this->table);
		for ($i = 0; $i < sizeof($tableInfo); $i++) {
			$fname = $tableInfo[$i]["name"];
			$this->persistent_slots[] = $fname;
			if (!isset($this->$fname))
				$this->$fname = "";
		}
	}

	/**
	 * Load entry from database
	 */
	function load($id="0") {
		$ids = explode(",", $id);
		foreach ($ids as $k => $v) {
			eval('$this->' . $this->keys[$k] . '="' . escape_sql_query($v) . '";');
		}

		if ($this->isKeyDefined()) {
			$tableInfo = $this->db->metadata($this->table);
			$this->db->query("SELECT * FROM " . $this->table . " WHERE " . $this->getKeyWhere() . ";");
			if ($this->db->next_record()) {
				for ($i = 0; $i < sizeof($tableInfo); $i++) {
					$fieldName = $tableInfo[$i]["name"];
					if (in_array($fieldName, $this->persistent_slots)) {
						$foo = $this->db->f($fieldName);
						eval('$this->' . $fieldName . '=$foo;');
					}
				}
				$this->isnew = false;
				return true;
			}else
				return false;
		}else {
			return false;
		}
	}

	/**
	 * save entry in database
	 */
	function save($force_new=false) {
		$sets = array();
		$wheres = array();
		if ($force_new)
			$this->isnew = true;
		foreach ($this->persistent_slots as $key => $val) {
			//if(!in_array($val,$this->keys))
			eval('if(isset($this->' . $val . ')) $sets[]="' . $val . '=\'".escape_sql_query($this->' . $val . ')."\'";');
		}
		$where = $this->getKeyWhere();
		$set = implode(",", $sets);

		if ($this->isKeyDefined() && $this->isnew) {
			$query = 'REPLACE INTO ' . $this->db->escape($this->table) . ' SET ' . $set;

			$this->db->query($query);
			# get ID #
			$this->ID = $this->db->getInsertId();
			$this->isnew = false;
			return true;
		}
		else if ($this->isKeyDefined()) {
			$query = 'UPDATE ' . $this->db->escape($this->table) . ' SET ' . $set . ' WHERE ' . $where;
			$this->db->query($query);
			return true;
		}

		return false;
	}

	/**
	 * delete entry from database
	 */
	function delete() {
		if (!$this->isKeyDefined()) {
			return false;
		}
		$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ' . $this->getKeyWhere() . ';');
		return true;
	}

	function getKeyWhere() {
		$wheres = array();
		foreach ($this->keys as $f) {
			eval('$wheres[]="' . $f . '=\'".escape_sql_query($this->' . $f . ')."\'";');
		}
		return implode(" AND ", $wheres);
	}

	function isKeyDefined() {
		$defined = true;
		foreach ($this->keys as $prim)
			eval('if(!isset($this->' . $prim . ')) $defined=false;');
		return $defined;
	}

	function setKeys($keys) {
		$this->keys = $keys;
	}

}
