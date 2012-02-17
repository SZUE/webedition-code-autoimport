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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * Document Definition base class
 */
class weBannerBase{

	var $uid;
	var $db;
	var $persistents = array();
	var $table = "";
	var $ClassName;

	function weBannerBase(){
		$this->uid = "ba_" . md5(uniqid(rand()));
		$this->db = new DB_WE();
		$this->ClassName = "weBannerBase";
	}

	function load(){
		$tableInfo = $this->db->metadata($this->table);
		$this->db->query("SELECT * FROM " . $this->table . " WHERE ID=" . intval($this->ID));
		if($this->db->next_record())
			for($i = 0; $i < sizeof($tableInfo); $i++){
				$fieldName = $tableInfo[$i]["name"];
				if(in_array($fieldName, $this->persistents)){
					$foo = $this->db->f($fieldName);
					$this->{$fieldName} = $foo;
				}
			}
	}

	function save(){
		$sets = array();
		$wheres = array();
		foreach($this->persistents as $key => $val){
			if($val == "ID"){
				eval('$wheres[]="' . $val . '=\'".$this->' . $val . '."\'";');
			}
			eval('$sets[]="' . $val . '=\'".$this->' . $val . '."\'";');
		}
		$where = implode(",", $wheres);
		$set = implode(",", $sets);
		if($this->ID == 0){

			$query = 'INSERT INTO ' . $this->table . ' SET ' . $set;
			$this->db->query($query);
			# get ID #
			$this->db->query("SELECT LAST_INSERT_ID()");
			$this->db->next_record();
			$this->ID = $this->db->f(0);
		} else{
			$query = 'UPDATE ' . $this->table . ' SET ' . $set . ' WHERE ' . $where;
			$this->db->query($query);
		}
	}

	function delete(){
		if($this->ID){
			$this->db->query('DELETE FROM ' . $this->table . ' WHERE ID=' . intval($this->ID));
			return true;
		}
		else
			return false;
	}

}
