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
 * Class weTableItem
 *
 * Provides functions for exporting and importing table rows.
 */

	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/"."modules/weModelBase.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/base/weDBUtil.class.php");

	class weTableItem extends weModelBase{

		var $Pseudo="weTableItem";
		var $attribute_slots=array();
		function weTableItem($table){
			if(weDBUtil::isTabExist($table)) weModelBase::weModelBase($table);
			else{
				$this->db=new DB_WE();
				$this->table=$table;
			}
			$this->attribute_slots["table"]=weTableItem::rmTablePrefix($table);
			$this->setKeys($this->getTableKey($this->table));
		}

		function load($id){
			weModelBase::load($id);
			// remove binary content
			if($this->table==CONTENT_TABLE && weContentProvider::IsBinary($id)) $this->Dat="";
		}


		function getTableKey($table){
			$table = strtolower($table);
			include($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_exim/backup/weTableKeys.inc.php');
			if(in_array($table,array_keys($tableKeys))) {
				return $tableKeys[$table];
			} else {
				return array('ID');
			}

		}

		function rmTablePrefix($tabname) {
			$len=strlen(TBL_PREFIX);
			if(substr($tabname,0,$len)==TBL_PREFIX) return strtolower(substr_replace($tabname,"",0,$len));
		}

        function save($force_new=false){
            // create table if doesn't exists;
            /*if(!weDBUtil::isTabExist($this->table)){
            	$keys=array();
            	$cols=array();
            	foreach ($this->persistent_slots as $value) {
            		$cols[$value]="VARCHAR(255) NOT NULL DEFAULT ''";
            		if($value=="ID") $keys[]="PRIMARY KEY (".$value.")";
            	}
            	if(count($cols)) weDBUtil::addTable($this->table,$cols,$keys);
            }*/
            weModelBase::save($force_new);
        }
		function getFieldType($fieldname){
			if(preg_match('/(.+?)_(.*)/',$fieldname,$regs)){
				return $regs[1];
			}
			return "";
		}
		function isObjectXTable($tablename){
			if(preg_match('/(.+?)_(.*)/',$tablename,$regs)){
				if (isset($regs[1]) && $regs[1]."_" == OBJECT_X_TABLE) return true;
			}
			return false;
		}

		function doConvertCharset($was){ //dies konvertiert die Daten, die binary im backup waren
			$tables = array();
			$tables[CONTENT_TABLE] = array('Dat');

			if(defined("OBJECT_TABLE")) {
				$tables[OBJECT_FILES_TABLE] = array('Category');

			}
			if(defined("SHOP_TABLE")) {
				$tables[ANZEIGE_PREFS_TABLE] = array('strDateiname','strFelder');
				$tables[SHOP_TABLE] = array('strSerial','strSerialOrder');
			}
			if (array_key_exists($this->table , $tables)){
				if ( in_array($was,$tables[$this->table]) ) {return true;}
			}
			return false;

		}
		function doCorrectExactCharsetString($was){
			$tables = array();
			$table = $this->table;
			$tables[NAVIGATION_TABLE] = array('Charset');
			if(defined("OBJECT_TABLE")) {
				$tables[OBJECT_FILES_TABLE] = array('Charset');
				$tables[OBJECT_X_TABLE] = array('OF_Charset');
				if ($this->isObjectXTable($table)) {
					$table=OBJECT_X_TABLE;
					$was = $this->getFieldType($was);
				}
			}
			if(defined("NEWSLETTER_TABLE")) {
				$tables[NEWSLETTER_TABLE] = array('Charset');
			}


			if (array_key_exists($table, $tables)){
				if ( in_array($was,$tables[$table]) ) {return true;}
			}
			return false;

		}
		function doCorrectSerializedLenghtValues($was){
			$tables = array();
			$table = $this->table;
			$tables[NAVIGATION_TABLE] = array('Attributes');
			$tables[CATEGORY_TABLE] = array('Catfields');
			if(defined("OBJECT_TABLE")) {
				$tables[OBJECT_TABLE] = array('dDefaultValues'); //DefaultValues bewusst entfernt
				$tables[OBJECT_X_TABLE] = array('link','variant'); //href nicht da ser str in ser str
				if ($this->isObjectXTable($table)) {
					$table=OBJECT_X_TABLE;
					$was = $this->getFieldType($was);
				}
			}
			if(defined("VOTING_TABLE")) {
				$tables[VOTING_TABLE] = array('QASet','QASetAdditions','Scores','LogData');
			}


			if (array_key_exists($table , $tables)){
				if ( in_array($was,$tables[$table]) ) {return true;}
			}
			return false;
		}
		function doPrepareCorrectSerializedLenghtValues($was){
			$tables = array();
			$tables[CATEGORY_TABLE] = array('Catfields');
			$table = $this->table;
			if (array_key_exists($table , $tables)){
				if ( in_array($was,$tables[$table]) ) {return true;}
			}
			return false;
		}
		function doCorrectSerializedExactCharsetString($was){
			$tables = array();
			if(defined("OBJECT_TABLE")) {
				$tables[OBJECT_TABLE] = array('DefaultValues');

			}

			if (array_key_exists($this->table , $tables)){
				if ( in_array($was,$tables[$this->table]) ) {return true;}
			}
			return false;

		}

		function convertCharsetEncoding($fromC,$toC){
			foreach($this as $key => &$val){
				if ($this->doConvertCharset($key)){
					$mydata = $val;
					if(isSerialized($mydata)){ //mainly for tblcontent, where serialized data is mixed with others, but stored in backup as binary
						$mydataUS = unserialize($mydata);
						if (is_array($mydataUS)){
							foreach ($mydataUS as &$ad){
								if (is_array($ad)){
									foreach ($ad as &$add){

										if (is_array($add)){
											foreach ($add as &$addd){
												$addd = convertCharsetEncoding($fromC,$toC,$addd);
												$addd = convertExactCharsetString($fromC,$toC,$addd);
												$addd = convertCharsetString($fromC,$toC,$addd);
											}
										} else {

											$add = convertCharsetEncoding($fromC,$toC,$add);
											$add = convertExactCharsetString($fromC,$toC,$add);
											$add = convertCharsetString($fromC,$toC,$add);
										}
									}
								} else {
									$ad = convertCharsetEncoding($fromC,$toC,$ad);
									$ad = convertExactCharsetString($fromC,$toC,$ad);
									$ad = convertCharsetString($fromC,$toC,$ad);
								}
							}
							$val = serialize($mydataUS);
						}
					} else {
						$val = convertCharsetEncoding($fromC,$toC,$mydata);
						$val = convertExactCharsetString($fromC,$toC,$val);
						$val = convertCharsetString($fromC,$toC,$val);
					}
				}
				if ($this->doCorrectExactCharsetString($key)){
					$val = convertExactCharsetString($fromC,$toC,$val);
				}
				if ($this->doCorrectSerializedLenghtValues($key))	{
					if ($this->doPrepareCorrectSerializedLenghtValues($key))	{
						$val = convertCharsetEncoding($fromC,$toC,$val);
					}
					$val =  correctSerDataISOtoUTF($val);
				}

				if ($this->doCorrectSerializedExactCharsetString($key))	{
					$mydata = $val;
					$mydataUS = @unserialize($mydata);
					if (is_array($mydataUS)){
						foreach ($mydataUS as &$ad){
							if (isset($ad['Charset']) && isset($ad['Charset']['dat'])){
								$ad['Charset']['dat']=convertExactCharsetString($fromC,$toC,$ad['Charset']['dat']);//tblObject
							}
						}
					}
					$val = @serialize($mydataUS);
				}
			}
		}
	}
