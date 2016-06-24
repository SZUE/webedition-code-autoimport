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
 * Class weVersion
 *
 * Provides functions for exporting and importing backups.
 */
class we_backup_version{

	var $db;
	var $ClassName = __CLASS__;
	var $attribute_slots = [];
	var $persistent_slots = array("ID", "ClassName", "Path", "Data", "SeqN");
	var $ID = 0;
	var $Path = '';
	var $Data = '';
	var $SeqN = 0;
	var $linkData = true;

	public function __construct($id = 0){
		foreach($this->persistent_slots as $slot){
			$this->$slot = "";
		}
		$this->SeqN = 0;
		$this->db = new DB_WE();
		if($id){
			$this->load($id);
		}
	}

	function load($id, $loadData = true){
		$this->ID = $id;
		$this->Path = f('SELECT binaryPath FROM ' . VERSIONS_TABLE . ' WHERE ID=' . intval($id), '', $this->db);
		return ($this->Path && $loadData ? $this->loadFile($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $this->Path) : false);
	}

	function loadFile($file){
		$this->Path = stri_replace(array($_SERVER['DOCUMENT_ROOT'], SITE_DIR), '', $file);
		return ($this->linkData ?
				$this->Data = we_base_file::load($file, 'rb', 8192, we_base_file::isCompressed($file)) :
				true);
	}

	function save($force = true){
		if($this->ID){
			$path = $_SERVER['DOCUMENT_ROOT'] . $this->Path;
			if(file_exists($path) && !$force){
				return false;
			}
			if(!is_dir(dirname($path))){
				we_base_file::createLocalFolderByPath(dirname($path));
			}
			we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $this->Path, $this->Data, ($this->SeqN == 0 ? 'wb' : 'ab'));
		} else {
			$path = $_SERVER['DOCUMENT_ROOT'] . $this->Path;
			if(file_exists($path) && !$force){
				return false;
			}
			if(!is_dir(dirname($path))){
				we_base_file::createLocalFolderByPath(dirname($path));
			}
			we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $this->Path, $this->Data, ($this->SeqN == 0 ? 'wb' : 'ab'));
		}
		return true;
	}

	//alias
	function we_save(){
		return $this->save();
	}

}
