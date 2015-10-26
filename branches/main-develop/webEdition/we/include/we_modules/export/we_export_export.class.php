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
 * General Definition of WebEdition Export
 *
 */
we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT);

class we_export_export extends weModelBase{
	//properties
	var $ID;
	var $Text;
	var $ParentID;
	var $IsFolder;
	var $Path;
	var $ExportTo; // local | server
	var $ServerPath;
	var $Filename;
	var $Selection = 'auto'; // auto | manual
	var $SelectionType = 'doctype'; // doctype | classname
	var $DocType;
	var $Folder;
	var $ClassName;
	var $Categorys;
	var $selDocs;
	var $selTempl;
	var $selObjs;
	var $selClasses;
	var $HandleDefTemplates;
	var $HandleDocIncludes;
	var $HandleObjIncludes;
	var $HandleDocLinked;
	var $HandleDefClasses;
	var $HandleObjEmbeds;
	var $HandleDoctypes;
	var $HandleCategorys;
	var $HandleOwners;
	var $HandleNavigation;
	var $HandleThumbnails;
	var $ExportDepth;
	var $Log = array();
	var $ExportFilename;
	var $protected = array('ID', 'ParentID', 'IsFolder', 'Path', 'Text');

	/**
	 * Default Constructor
	 * Can load or create new Newsletter depends of parameter
	 */
	function __construct($exportID = 0){
		parent::__construct(EXPORT_TABLE);
		$this->setDefaults();
		if($exportID){
			$this->ID = $exportID;
			$this->load($exportID);
		}
		// clear expiered stuff
		$this->selDocs = $this->clearExpired($this->selDocs, FILE_TABLE);
		$this->selTempl = $this->clearExpired($this->selTempl, TEMPLATES_TABLE);
		if(defined('OBJECT_TABLE')){
			$this->selObjs = $this->clearExpired($this->selObjs, OBJECT_FILES_TABLE);
			$this->selClasses = $this->clearExpired($this->selClasses, OBJECT_TABLE);
		} else {
			$this->selObjs = '';
			$this->selClasses = '';
		}
	}

	function clearExpired($ids, $table, $idfield = 'ID'){
		$idsarr = makeArrayFromCSV($ids);
		$new = array();
		$db = new DB_WE();
		foreach($idsarr as $id){
			if(f('SELECT ' . $db->escape($idfield) . ' FROM ' . $db->escape($table) . ' WHERE ' . $db->escape($idfield) . '=\'' . (is_numeric($id) ? $id : $db->escape($id)) . '\'', $idfield, $db)){
				$new[] = $id;
			}
		}
		return implode(',', $new);
	}

	function save($force_new = false){
		$sets = array();
		$wheres = array();
		foreach($this->persistent_slots as $val){
			//if(!in_array($val,$this->keys))
			if(isset($this->{$val})){
				$sets[] = '`' . $this->db->escape($val) . '`="' . $this->db->escape($this->{$val}) . '"';
			}
		}
		$where = $this->getKeyWhere();
		$set = implode(",", $sets);

		$this->table = $this->db->escape($this->table);
		if(!$this->ID || $force_new){

			$ret = $this->db->query('REPLACE INTO ' . $this->table . ' SET ' . $set);
			if($ret){
				# get ID #
				$this->ID = $this->db->getInsertId();
			}
			return $ret;
		} else {
			return $this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . $set . ' WHERE ' . $where);
		}

		return false;
	}

	function delete(){
		//if (!$this->ID) return false;
		if($this->IsFolder){
			$this->deleteChilds();
		}
		parent::delete();
		return true;
	}

	/*	 * *******************************
	 * delete childs from database
	 *
	 * ******************************** */

	function deleteChilds(){
		$this->db->query("SELECT ID FROM " . EXPORT_TABLE . ' WHERE ParentID=' . intval($this->ID));
		while($this->db->next_record()){
			$child = new we_export_export($this->db->f("ID"));
			$child->delete();
		}
	}

	function clearSessionVars(){
		if(isset($_SESSION['weS']['export_session'])){
			unset($_SESSION['weS']['export_session']);
		}
		if(isset($_SESSION['weS']['exportVars_session'])){
			unset($_SESSION['weS']['exportVars_session']);
		}
	}

	function filenameNotValid($text){
		return preg_match('%[^a-z0-9äöü\._\@\ \-]%i', $text);
	}

	function exportToFilenameValid($filename){
		return (preg_match('%p?html?%i', $filename) || stripos($filename, 'inc') !== false || preg_match('%php3?%i', $filename));
	}

	function setDefaults(){
		$this->ParentID = 0;
		$this->Text = "weExport_" . time();
		$this->Selection = 'auto';
		$this->SelectionType = 'doctype';
		$this->Filename = $this->Text . ".xml";
		$this->ExportDepth = 5;

		$this->HandleDefTemplates = 0;
		$this->HandleDocIncludes = 0;
		$this->HandleObjIncludes = 0;
		$this->HandleDocLinked = 0;
		$this->HandleDefClasses = 0;
		$this->HandleObjEmbeds = 0;
		$this->HandleDoctypes = 0;
		$this->HandleCategorys = 0;
		$this->HandleOwners = 0;
		$this->HandleNavigation = 0;
	}

	function setPath(){
		$ppath = f('SELECT Path FROM ' . EXPORT_TABLE . ' WHERE ID=' . intval($this->ParentID) . ';', 'Path', $this->db);
		$this->Path = $ppath . "/" . $this->Text;
	}

	function pathExists($path){
		return f('SELECT 1 FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($path) . '" AND ID!=' . intval($this->ID) . ' LIMIT 1', '', $this->db);
	}

	function isSelf(){
		return strpos(we_base_file::clearPath(dirname($this->Path) . '/'), '/' . $this->Text . '/') !== false;
	}

	function evalPath($id = 0){
		$db_tmp = new DB_WE();
		$path = "";
		if($id == 0){
			$id = $this->ParentID;
			$path = $this->Text;
		}

		$foo = getHash('SELECT Text,ParentID FROM ' . EXPORT_TABLE . ' WHERE ID=' . intval($id), $db_tmp);
		$path = "/" . (isset($foo["Text"]) ? $foo["Text"] : "") . $path;

		$pid = isset($foo["ParentID"]) ? $foo["ParentID"] : "";
		while($pid > 0){
			$db_tmp->query('SELECT Text,ParentID FROM ' . EXPORT_TABLE . ' WHERE ID=' . intval($pid));
			while($db_tmp->next_record()){
				$path = "/" . $db_tmp->f("Text") . $path;
				$pid = $db_tmp->f("ParentID");
			}
		}
		return $path;
	}

}
