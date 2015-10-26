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
abstract class we_versions_logBase{
	public $db;
	public $table;
	public $userID;
	public $timestamp;
	public $persistent_slots = array();

	function __construct($_table){

		$this->db = new DB_WE();
		$this->userID = $_SESSION['user']['ID'];
		$this->timestamp = time();
		$this->table = $_table;
		$this->loadPresistents();
	}

	function loadPresistents(){

		$tableInfo = $this->db->metadata($this->table);
		foreach($tableInfo as $t){
			$columnName = $t["name"];
			$this->persistent_slots[] = $columnName;
			if(!isset($this->$columnName)){
				$this->$columnName = "";
			}
		}
	}

	function load(){

		$content = array();
		$tableInfo = $this->db->metadata($this->table);
		$this->db->query('SELECT ID,timestamp,typ,userID FROM ' . $this->db->escape($this->table) . ' ORDER BY timestamp DESC');
		$m = 0;
		while($this->db->next_record()){
			for($i = 0; $i < count($tableInfo); $i++){
				$columnName = $tableInfo[$i]["name"];
				if(in_array($columnName, $this->persistent_slots)){
					$content[$m][$columnName] = $this->db->f($columnName);
				}
			}
			$m++;
		}

		return $content;
	}

	function saveLog(){
		$set = array();

		foreach($this->persistent_slots as $val){
			if(isset($this->$val)){
				$set[$val] = $this->$val;
			}
		}

		if($set){
			$this->db->query('INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter($set));
		}
	}

}
