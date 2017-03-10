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
class we_base_model{
	var $db;
	var $table = '';
	var $persistent_slots = [];
	var $keys = ['ID'];
	var $isnew = true;
	protected $MediaLinks = [];
	protected $isAdvanced = false;
	protected $binFields = [];

	/**
	 * Default Constructor
	 */
	public function __construct($table, we_database_base $db = null, $load = true, $isAdvanced = false){
		$this->db = ($db ?: new DB_WE());
		$this->table = $table;
		$this->isAdvanced = $isAdvanced;
		if($load){
			$this->loadPresistents();
		}
	}

	function loadPresistents(){//fixme: set datatype from db
		$this->persistent_slots = [];
		$tableInfo = $this->db->metadata($this->table);
		foreach($tableInfo as $info){
			$fname = $info['name'];
			$this->persistent_slots[] = $fname;
			switch($info["type"]){
				case 'tinyblob':
				case 'mediumblob':
				case 'blob':
				case 'longblob':
				case 'varbinary':
				case 'binary':
					$this->binFields[] = $fname;
			}
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
			$isAdvanced |= $this->isAdvanced || !is_numeric(key($this->persistent_slots));

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
		$sets = [];
		if($force_new){
			$this->isnew = true;
		}
		switch($this->table){
			case CONTENT_TABLE:
				if($this->nHash){
					$this->nHash = md5($this->Name, true);
				}
				break;
		}

		foreach($this->persistent_slots as $key => $val){
			$val = ($isAdvanced || $this->isAdvanced ? $key : $val);

			if(isset($this->{$val})){
				$sets[$val] = is_array($this->{$val}) ?
					(empty($this->{$val}) ? '' : we_serialize($this->{$val}, ($jsonSer ? SERIALIZE_JSON : SERIALIZE_PHP))) :
					(in_array($val, $this->binFields) ?
					sql_function('x\'' . bin2hex($this->{$val}) . '\'') :
					$this->{$val});
			}
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

	protected function registerMediaLinks(){ // FIXME: use this for categorys and newsletter too
		$this->MediaLinks = array_filter($this->MediaLinks, function($v){
			return $v && is_numeric($v);
		});

		// filter MediaLinks by media contenttype
		$verifiedIDs = [];
		if(!empty($this->MediaLinks)){
			$whereType = 'AND ContentType IN ("' . we_base_ContentTypes::APPLICATION . '","' . we_base_ContentTypes::FLASH . '","' . we_base_ContentTypes::IMAGE . '","' . we_base_ContentTypes::VIDEO . '")';
			$this->db->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE ID IN (' . implode(',', array_unique(array_values($this->MediaLinks))) . ') ' . $whereType);
			while($this->db->next_record()){
				$verifiedIDs[] = $this->db->f('ID');
			}
		}
		$this->MediaLinks = array_intersect($this->MediaLinks, $verifiedIDs);

		if(empty($this->MediaLinks)){
			return true;
		}

		foreach($this->MediaLinks as $element => $remObj){
			$this->db->query('REPLACE INTO ' . FILELINK_TABLE . ' SET ' . we_database_base::arraySetter(['ID' => $this->ID,
					'DocumentTable' => stripTblPrefix($this->table),
					'type' => 'media',
					'remObj' => $remObj,
					'remTable' => stripTblPrefix(FILE_TABLE),
					'nHash' => sql_function(is_numeric($element) ? 'NULL' : 'x\'' . md5($element) . '\''),
					'position' => 0,
					'isTemp' => 0
			]));
		}
	}

	function unregisterMediaLinks(){
		$this->db->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . $this->db->escape(stripTblPrefix($this->table)) . '"  AND type="media"');
	}

	function getKeyWhere(){
		$wheres = [];
		foreach($this->keys as $f){
			$wheres[] = '`' . $f . '`="' . escape_sql_query($this->$f) . '"';
		}
		return implode(' AND ', $wheres);
	}

	function isKeyDefined(){
		foreach($this->keys as $prim){
			if(!property_exists($this, $prim)){
				return false;
			}
		}
		return true;
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
