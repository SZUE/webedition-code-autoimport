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
 * Definition of webEdition Base Model
 *
 */
class weModelBase{
	var $db;
	var $table = '';
	var $persistent_slots = array();
	var $keys = array('ID');
	var $isnew = true;
	protected $MediaLinks = array();
	protected $isAdvanced = false;

	/**
	 * Default Constructor
	 */
	public function __construct($table, we_database_base $db = null, $load = true, $isAdvanced = false){
		$this->db = ($db ? : new DB_WE());
		$this->table = $table;
		$this->isAdvanced = $isAdvanced;
		if($load){
			$this->loadPresistents();
		}
	}

	function loadPresistents(){//fixme: set datatype from db
		$this->persistent_slots = array();
		$tableInfo = $this->db->metadata($this->table);
		foreach($tableInfo as $info){
			$fname = $info["name"];
			$this->persistent_slots[] = $fname;
			if(!isset($this->$fname)){
				$this->$fname = "";
			}
		}
	}

	/**
	 * Load entry from database
	 */
	function load($id = 0, $isAdvanced = false){
		if($id){
			$this->ID = $id;
		}
		if($this->isKeyDefined()){
			$isAdvanced|=$this->isAdvanced || !is_numeric(key($this->persistent_slots));
			//if($id){
			//	$this->ID = $id;
			//}
			//#6338: Kode vor den if-Block geschoben
			//$tableInfo = $this->db->metadata($this->table);

			if(($data = getHash('SELECT * FROM `' . $this->table . '` WHERE ' . $this->getKeyWhere(), $this->db, MYSQL_ASSOC))){
				foreach($data as $fieldName => $value){
					if(($isAdvanced ? isset($this->persistent_slots[$fieldName]) : in_array($fieldName, $this->persistent_slots))){
						$this->{$fieldName} = $value;
					}
				}
				$this->isnew = false;
				return true;
			}
		}
		return false;
	}

	/**
	 * save entry in database
	 */
	function save($force_new = false, $isAdvanced = false, $jsonSer = false){
		$sets = array();
		if($force_new){
			$this->isnew = true;
		}
		foreach($this->persistent_slots as $key => $val){
			$val = ($isAdvanced || $this->isAdvanced ? $key : $val);

			if(isset($this->{$val})){
				$sets[$val] = is_array($this->{$val}) ? we_serialize($this->{$val}, ($jsonSer ? 'json' : 'serialize')) : $this->{$val};
			}
		}
		if($this->table == LINK_TABLE && empty($this->nHash)){
			$this->nHash = md5($this->Name);
		}
		$where = $this->getKeyWhere();
		$set = we_database_base::arraySetter($sets);

		if($this->isKeyDefined()){
			if($this->isnew){
				$ret = $this->db->query('REPLACE INTO ' . $this->db->escape($this->table) . ' SET ' . $set, false, true);
				# get ID #
				if($ret){
					$this->ID = $this->db->getInsertId();
					$this->isnew = false;
				}
				return $ret;
			}
			return $this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . $set . ' WHERE ' . $where);
		}

		return false;
	}

	/**
	 * delete entry from database
	 */
	function delete(){
		if(!$this->isKeyDefined()){
			return false;
		}
		$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ' . $this->getKeyWhere());
		return true;
	}

	function registerMediaLinks(){ // FIXME: use this for categorys and newsletter too
		$c = count($this->MediaLinks);
		for($i = 0; $i < $c; $i++){
			if(!$this->MediaLinks[$i] || !is_numeric($this->MediaLinks[$i])){
				unset($this->MediaLinks[$i]);
			}
		}

		if(!empty($this->MediaLinks)){
			$whereType = 'AND ContentType IN ("' . we_base_ContentTypes::APPLICATION . '","' . we_base_ContentTypes::FLASH . '","' . we_base_ContentTypes::IMAGE . '","' . we_base_ContentTypes::QUICKTIME . '","' . we_base_ContentTypes::VIDEO . '")';
			$this->db->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE ID IN (' . implode(',', array_unique($this->MediaLinks)) . ') ' . $whereType);
			$this->MediaLinks = array();
			while($this->db->next_record()){
				$this->MediaLinks[] = $this->db->f('ID');
			}
		}

		foreach(array_unique($this->MediaLinks) as $remObj){
			$this->db->query('REPLACE INTO ' . FILELINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'ID' => $this->ID,
					'DocumentTable' => stripTblPrefix($this->table),
					'type' => 'media',
					'remObj' => $remObj,
					'remTable' => stripTblPrefix(FILE_TABLE),
					'position' => 0,
					'isTemp' => 0
			)));
		}
	}

	function unregisterMediaLinks(){
		$this->db->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . $this->db->escape(stripTblPrefix($this->table)) . '"  AND type="media"');
	}

	function getKeyWhere(){
		$wheres = array();
		foreach($this->keys as $f){
			$wheres[] = '`' . $f . '`="' . escape_sql_query($this->$f) . '"';
		}
		return implode(' AND ', $wheres);
	}

	function isKeyDefined(){
		$defined = true;
		foreach($this->keys as $prim){
			if(!isset($this->$prim)){
				$defined = false;
			}
		}
		return $defined;
	}

	function setKeys($keys){
		$this->keys = $keys;
	}

	public function getLogString($prefix = ''){
		return $prefix . $this->table;
	}

	public function __sleep(){
		$tmp = get_object_vars($this);
		unset($tmp['db']);
		return array_keys($tmp);
	}

	public function __wakeup(){
		$this->db = new DB_WE();
	}

}
