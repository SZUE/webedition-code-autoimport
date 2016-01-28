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
class we_backup_tableAdv extends we_backup_table{

	var $ClassName = __CLASS__;

	public function getColumns(){
		if($this->db->isTabExist($this->table)){
			$this->db->query('SHOW CREATE TABLE ' . $this->table);
			if($this->db->next_record()){
				$zw = explode("\n", $this->db->f("Create Table"));
				$zw[0] = str_replace($this->table, stripTblPrefix($this->table), $zw[0]);
			}
			$this->elements[$this->db->f("Table")] = array('Field' => 'create');
			foreach($zw as $k => $v){
				$this->elements[$this->db->f("Table")]['line' . $k] = $v;
			}
		}
		//$this->fetchNewColumns();
	}

	function save(){
		global $DB_WE;
		if(!(isset($_SESSION['weS']['weBackupVars']['tablekeys']) && is_array($_SESSION['weS']['weBackupVars']['tablekeys']))){
			$_SESSION['weS']['weBackupVars']['tablekeys'] = array();
		}
		if(isset($_SESSION['weS']['weBackupVars']['options']['convert_charset']) && $_SESSION['weS']['weBackupVars']['options']['convert_charset']){
			$doConvert = true;
			$searchArray = array('CHARACTER SET latin1', 'COLLATE latin1_bin', 'COLLATE latin1_danish_ci', 'COLLATE latin1_general_ci', 'COLLATE latin1_general_cs', 'COLLATE latin1_german1_ci', 'COLLATE latin1_german2_ci', 'COLLATE latin1_spanish_ci', 'COLLATE latin1_swedish_ci');
		} else {
			$doConvert = false;
		}
		if($this->db->isTabExist($this->table)){
			$_SESSION['weS']['weBackupVars']['tablekeys'][$this->table] = $this->db->getTableKeyArray($this->table);
			$this->db->delTable($this->table);
		}
		$myarray = $this->elements['create'];
		unset($myarray['Field']);
		foreach($myarray as &$cur){
			if(substr($cur, 0, 6) === 'CREATE'){
				//Regex because of backups <6.2.4
				$cur = preg_replace('/(CREATE *\w* *`?)\w*' . stripTblPrefix($this->table) . '/i', '$1' . $this->table, $cur, 1);
			}
			if($doConvert){
				$cur = str_replace($searchArray, '', $cur);
			}
		}

		//FIXME: this is NOT Save for MySQL Updates!!!!
		array_pop($myarray); //get rid of old Engine statement
		$myarray[] = ' ) ' . we_database_base::getCharsetCollation() . ' ENGINE=MyISAM;';

		$query = implode(' ', $myarray);
		return ($DB_WE->query($query));
	}

}
