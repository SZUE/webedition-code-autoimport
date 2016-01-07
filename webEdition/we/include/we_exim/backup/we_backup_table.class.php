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
 * Class weTable
 *
 * Provides functions for loading and saving db tables.
 */
class we_backup_table{
	var $ClassName = __CLASS__;
	var $db;
	var $table = "";
	var $elements;
	var $persistent_slots = array();
	var $attribute_slots = array();

	public function __construct($table, $force_columns = false){
		$this->db = new DB_WE();
		$this->table = $table;
		$this->elements = array();

		$this->attribute_slots["name"] = stripTblPrefix($table);

		$update_table = true;

		if(defined('OBJECT_X_TABLE') && !$force_columns){
			if(strtolower(substr($table, 0, 10)) == strtolower(stripTblPrefix(OBJECT_X_TABLE))){
				$update_table = false;
			}
		}

		if(defined('CUSTOMER_TABLE') && !$force_columns && strtolower($table) == strtolower(CUSTOMER_TABLE)){
			$update_table = false;
		}

		if($update_table){
			$this->getColumns();
		}
	}

	public function getColumns(){
		if($this->db->isTabExist($this->table)){
			$this->db->query("SHOW COLUMNS FROM $this->table;");
			while($this->db->next_record()){
				$this->elements[$this->db->f("Field")] = array(
					"Field" => $this->db->f("Field"),
					"Type" => $this->db->f("Type"),
					"Null" => $this->db->f("Null"),
					"Key" => $this->db->f("Key"),
					"Default" => $this->db->f("Default"),
					"Extra" => $this->db->f("Extra")
				);
			}
		}

		$this->fetchNewColumns();
	}

	function save(){
		if(!(isset($_SESSION['weS']['weBackupVars']['tablekeys']) && is_array($_SESSION['weS']['weBackupVars']['tablekeys']))){
			$_SESSION['weS']['weBackupVars']['tablekeys'] = array();
		}
		$_SESSION['weS']['weBackupVars']['tablekeys'][$this->table] = $this->db->getTableKeyArray($this->table);
		$this->db->delTable($this->table);
		$cols = $keys = array();

		foreach($this->elements as $element){

			$_defalut_for_type = stripos($element["Type"], 'int') !== false || stripos($element["Type"], 'double') !== false || stripos($element["Type"], 'float') !== false ? 0 : "''";

			$_default_value = ("DEFAULT " . ((!empty($element["Default"])) ? ("'" . $element["Default"] . "'") : ((isset($element["Null"]) && $element["Null"] === "YES") ? "NULL" : $_defalut_for_type)));

			$cols[$element["Field"]] = $element["Type"] . " " . ((isset($element["Null"]) && $element["Null"] === "YES") ? "NULL " : "NOT NULL ") . ((isset($element["Extra"]) && strtolower($element["Extra"]) != "auto_increment") ? $_default_value : "") . " " . ((isset($element["Extra"])) ? $element["Extra"] : '');

			if(!empty($element["Key"]) && $element["Key"] === "PRI"){
				$keys[] = "PRIMARY KEY (" . $element["Field"] . ")";
			}
		}

		if($cols){
			return $this->db->addTable($this->table, $cols, $keys);
		}

		return false;
	}

	// add new fields to the table before import
	function fetchNewColumns(){
		// fix for bannerclicks table - primary key has been added
		if(defined('BANNER_CLICKS_TABLE') && $this->table == BANNER_CLICKS_TABLE){
			if(!isset($this->elements['clickid'])){
				$this->elements['clickid'] = array(
					'Field' => 'clickid',
					'Type' => 'BIGINT',
					'Null' => 'NO',
					'Key' => 'PRI',
					'Default' => '',
					'Extra' => 'auto_increment'
				);
			}
		}
		// fix for bannerviews table - primary key has been added
		if(defined('BANNER_VIEWS_TABLE') && $this->table == BANNER_VIEWS_TABLE){
			if(!isset($this->elements['viewid'])){
				$this->elements['viewid'] = array(
					'Field' => 'viewid',
					'Type' => 'BIGINT',
					'Null' => 'NO',
					'Key' => 'PRI',
					'Default' => '',
					'Extra' => 'auto_increment'
				);
			}
		}
	}

	public function getLogString($prefix = ''){
		return $prefix . $this->table;
	}

}
