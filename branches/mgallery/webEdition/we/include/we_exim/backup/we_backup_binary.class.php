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
 * Class we_backup_binary
 *
 * Provides functions for exporting and importing backups.
 */
class we_backup_binary{
	var $db;
	var $ClassName = __CLASS__;
	var $attribute_slots = array();
	var $persistent_slots = array('ID', 'ClassName', 'Path', 'Data', 'SeqN');
	var $ID = 0;
	var $Path = "";
	var $Data = "";
	var $SeqN = 0;
	var $linkData = true;

	function __construct($id = 0){
		$this->db = new DB_WE();
		if($id){
			$this->load($id);
		}
	}

	function load($id, $loadData = true){
		if($id){
			$this->ID = $id;
			$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), '', $this->db);
			if($path){
				$this->Path = $path;
				if($this->Path && $loadData){
					return $this->loadFile($this->Path);
				}
			}
		}
		return false;
	}

	function loadFile($file){
		$path = str_replace(array($_SERVER['DOCUMENT_ROOT'], SITE_DIR), '', $file);
		$this->Path = $path;
		return ($this->linkData ? $this->Data = we_base_file::load($file) : true);
	}

	function save($force = true){
		$path = $_SERVER['DOCUMENT_ROOT'] . ($this->ID ? SITE_DIR : '') . $this->Path;
		if(file_exists($path) && !$force){
			return false;
		}
		if(!is_dir(dirname($path))){
			we_base_file::createLocalFolderByPath(dirname($path));
		}
		we_base_file::save($path, $this->Data, ($this->SeqN == 0 ? 'wb' : 'ab'));
		return true;
	}

	//alias
	function we_save(){
		return $this->save();
	}

	public function getFilesize(){
		$path = $_SERVER['DOCUMENT_ROOT'] . $this->Path;
		if(!file_exists($path)){
			$path = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $this->Path;
		}
		return file_exists($path) ? filesize($path) : 0;
	}

	public function getLogString($prefix = ''){
		return $prefix . $this->table . $this->ID . ':' . $this->Path;
	}

}
