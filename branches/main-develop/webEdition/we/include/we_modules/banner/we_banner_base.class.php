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

/**
 * Document Definition base class
 */
abstract class we_banner_base{

	protected $db;
	protected $persistents = array();
	protected $table = "";
	var $ClassName = __CLASS__;

	protected function __construct(){
		$this->db = new DB_WE();
	}

	public function load(){
		$tableInfo = $this->db->metadata($this->table);
		$this->db->query('SELECT * FROM ' . $this->table . ' WHERE ID=' . intval($this->ID));
		if($this->db->next_record()){
			foreach($tableInfo as $cur){
				$fieldName = $cur["name"];
				if(in_array($fieldName, $this->persistents)){
					$foo = $this->db->f($fieldName);
					$this->{$fieldName} = $foo;
				}
			}
		}
	}

	public function save(){
		$sets = array();
		foreach($this->persistents as $val){
			if($val != 'ID'){
				$sets[$val] = $this->$val;
			}
		}
		if($this->ID == 0){
			$this->db->query('INSERT INTO ' . $this->table . ' SET ' . we_database_base::arraySetter($sets));
			# get ID #
			$this->ID = $this->db->getInsertId();
		} else {
			$this->db->query('UPDATE ' . $this->table . ' SET ' . we_database_base::arraySetter($sets) . ' WHERE ID=' . $this->ID);
		}
	}

	public function delete(){
		if(!$this->ID){
			return false;
		}
		$this->db->query('DELETE FROM ' . $this->table . ' WHERE ID=' . intval($this->ID));
		return true;
	}

}
