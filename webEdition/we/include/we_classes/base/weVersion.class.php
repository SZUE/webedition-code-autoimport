<?php
/**
 * webEdition CMS
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
 * Class weVersion
 *
 * Provides functions for exporting and importing backups.
 */

	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/base/"."weFile.class.php");

	class weVersion{

		var $db;
		var $ClassName="weVersion";
		var $Pseudo="weVersion";

		var $attribute_slots=array();
		var $persistent_slots=array();

		var $ID=0;
		var $Path="";
		var $Data="";
		var $SeqN=0;

		var $linkData=true;

		function weVersion($id=0){
			$this->Pseudo="weVersion";
			$this->persistent_slots=array("ID", "ClassName","Path","Data","SeqN");
			foreach($this->persistent_slots as $slot) $this->$slot="";
			$this->SeqN = 0;
			$this->ClassName="weVersion";
			$this->db=new DB_WE();
			if($id) $this->load($id);
		}

		function load($id,$loadData=true){
			$this->ID=$id;
			$this->db->query("SELECT binaryPath FROM ".VERSIONS_TABLE." WHERE ID='".abs($id)."';");
			if($this->db->next_record()){
				$this->Path=$this->db->f("binaryPath");
				if($this->Path && $loadData){
					return $this->loadFile($_SERVER["DOCUMENT_ROOT"].SITE_DIR.$this->Path);
				}
				return false;
			}
			else return false;
		}

		function loadFile($file){
			$path=stri_replace($_SERVER["DOCUMENT_ROOT"],"",$file);
			$path=stri_replace(SITE_DIR,"",$path);
			$this->Path=$path;
			if($this->linkData)
				return $this->Data=weFile::load($file);
			else
				return true;
		}

		function save($force=true){
			if($this->ID){
				$path=$_SERVER["DOCUMENT_ROOT"].$this->Path;
				if(file_exists($path) && !$force) return false;
				if(!is_dir(dirname($path))) {
					createLocalFolderByPath(dirname($path));
				}
				weFile::save($_SERVER["DOCUMENT_ROOT"].$this->Path,$this->Data,($this->SeqN==0 ? 'wb' : 'ab'));
			}
			else{
				$path=$_SERVER["DOCUMENT_ROOT"].$this->Path;
				if(file_exists($path) && !$force) return false;
				if(!is_dir(dirname($path))){
					createLocalFolderByPath(dirname($path));
				}
				weFile::save($_SERVER["DOCUMENT_ROOT"].$this->Path,$this->Data,($this->SeqN==0 ? 'wb' : 'ab'));
			}
			return true;
		}

		//alias
		function we_save(){
			return $this->save();
		}

	}


