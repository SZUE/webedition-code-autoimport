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
abstract class we_banner_base{ // FIXME: base on we_ModelBase to us registerFileLink()
	protected $db;
	public $persistents = [];
	protected $table = "";
	var $ClassName = __CLASS__;

	protected function __construct(){
		$this->db = new DB_WE();
	}

	public function load(){
		$data = getHash('SELECT * FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($this->ID));
		if($data){
			foreach($data as $key => $value){
				if(isset($this->persistents[$key])){
					$this->{$key} = $value;
				}
			}
		}
	}

	public function save(){
		$sets = [];
		foreach(array_keys($this->persistents) as $val){
			if($val != 'ID'){
				$sets[$val] = $this->$val;
			}
		}
		if($this->ID == 0){
			$this->db->query('INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter($sets));
			# get ID #
			$this->ID = $this->db->getInsertId();
		} else {
			$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter($sets) . ' WHERE ID=' . intval($this->ID));
		}
	}

	public function delete(){
		if(!$this->ID){
			return false;
		}
		$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->ID));
		return true;
	}

}
