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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * Class weTableItem
 *
 * Provides functions for exporting and importing table rows.
 */
class we_backup_tableItem extends weModelBase{

	var $Pseudo = 'we_backup_tableItem';
	var $attribute_slots = array();

	function __construct($table){
		if($GLOBALS['DB_WE']->isTabExist($table)){
			parent::__construct($table);
		} else {
			$this->db = new DB_WE();
			$this->table = $table;
		}
		$this->attribute_slots['table'] = stripTblPrefix($table);
		$this->setKeys(self::getTableKey($this->table));
	}

	function load(array $ids){
		foreach($ids as $key => $val){
			$this->$key = $val;
		}
		parent::load();
	}

	static function getTableKey($table){
		static $cache = array();
		if(isset($cache[$table])){
			return $cache[$table];
		}
		//read Primary key from installed default files
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/sqldumps/' . stripTblPrefix($table) . '.sql')){
			$lines = we_util_File::loadLines($_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/sqldumps/' . stripTblPrefix($table) . '.sql', 2, 999);
			$cache[$table] = $GLOBALS['DB_WE']->getPrimaryKeys($table, $lines);
		}
		if(!isset($cache[$table]) || !$cache[$table]){//fallback, or for external tables
			$cache[$table] = $GLOBALS['DB_WE']->getPrimaryKeys($table);
		}
		return $cache[$table];
	}

	function getFieldType($fieldname){
		return (preg_match('/(.+?)_(.*)/', $fieldname, $regs) ? $regs[1] : '');
	}

	function isObjectXTable($tablename){
		$regs = array();
		return (preg_match('/(.+?)_(.*)/', $tablename, $regs) && isset($regs[1]) && $regs[1] . '_' == OBJECT_X_TABLE);
	}

	function doConvertCharset($was){ //dies konvertiert die Daten, die binary im backup waren
		$tables = array();
		$tables[CONTENT_TABLE] = array('Dat');

		if(defined("OBJECT_TABLE")){
			$tables[OBJECT_FILES_TABLE] = array('Category');
		}
		if(defined("SHOP_TABLE")){
			$tables[ANZEIGE_PREFS_TABLE] = array('strDateiname', 'strFelder');
			$tables[SHOP_TABLE] = array('strSerial', 'strSerialOrder');
		}
		return (array_key_exists($this->table, $tables) && in_array($was, $tables[$this->table]));
	}

	function doCorrectExactCharsetString($was){
		$tables = array();
		$table = $this->table;
		$tables[NAVIGATION_TABLE] = array('Charset');
		if(defined("OBJECT_TABLE")){
			$tables[OBJECT_FILES_TABLE] = array('Charset');
			$tables[OBJECT_X_TABLE] = array('OF_Charset');
			if($this->isObjectXTable($table)){
				$table = OBJECT_X_TABLE;
				$was = $this->getFieldType($was);
			}
		}
		if(defined("NEWSLETTER_TABLE")){
			$tables[NEWSLETTER_TABLE] = array('Charset');
		}

		return (array_key_exists($table, $tables) && in_array($was, $tables[$table]));
	}

	function doCorrectSerializedLenghtValues($was){
		$tables = array();
		$table = $this->table;
		$tables[NAVIGATION_TABLE] = array('Attributes');
		$tables[CATEGORY_TABLE] = array('Catfields');
		if(defined('OBJECT_TABLE')){
			$tables[OBJECT_TABLE] = array('dDefaultValues'); //DefaultValues bewusst entfernt
			$tables[OBJECT_X_TABLE] = array('link', 'variant'); //href nicht da ser str in ser str
			if($this->isObjectXTable($table)){
				$table = OBJECT_X_TABLE;
				$was = $this->getFieldType($was);
			}
		}
		if(defined('VOTING_TABLE')){
			$tables[VOTING_TABLE] = array('QASet', 'QASetAdditions', 'Scores', 'LogData');
		}

		return (array_key_exists($table, $tables) && in_array($was, $tables[$table]));
	}

	function doPrepareCorrectSerializedLenghtValues($was){
		$tables = array();
		$tables[CATEGORY_TABLE] = array('Catfields');
		$table = $this->table;
		return (array_key_exists($table, $tables) && in_array($was, $tables[$table]));
	}

	function doCorrectSerializedExactCharsetString($was){
		$tables = array();
		if(defined("OBJECT_TABLE")){
			$tables[OBJECT_TABLE] = array('DefaultValues');
		}

		return (array_key_exists($this->table, $tables) && in_array($was, $tables[$this->table]));
	}

	static function convertSCharsetEncoding($fromC, $toC, $string){
		if($fromC != '' && $toC != ''){
			if(function_exists('iconv')){
				return iconv($fromC, $toC . '//TRANSLATE', $string);
			} elseif($fromC == 'UTF-8' && $toC == 'ISO-8859-1'){
				return utf8_decode($string);
			} elseif($fromC == 'ISO-8859-1' && $toC == 'UTF-8'){
				return utf8_encode($string);
			}
		}
		return $string;
	}

	function convertCharsetEncoding($fromC, $toC){
		foreach($this as $key => &$val){
			if($this->doConvertCharset($key)){
				$mydata = $val;
				if(weXMLImport::isSerialized($mydata)){ //mainly for tblcontent, where serialized data is mixed with others, but stored in backup as binary
					$mydataUS = unserialize($mydata);
					if(is_array($mydataUS)){
						foreach($mydataUS as &$ad){
							if(is_array($ad)){
								foreach($ad as &$add){
									if(is_array($add)){
										foreach($add as &$addd){
											$addd = self::convertSCharsetEncoding($fromC, $toC, $addd);
											$addd = self::convertExactCharsetString($fromC, $toC, $addd);
											$addd = self::convertCharsetString($fromC, $toC, $addd);
										}
									} else {
										$add = self::convertSCharsetEncoding($fromC, $toC, $add);
										$add = self::convertExactCharsetString($fromC, $toC, $add);
										$add = self::convertCharsetString($fromC, $toC, $add);
									}
								}
							} else {
								$ad = self::convertSCharsetEncoding($fromC, $toC, $ad);
								$ad = self::convertExactCharsetString($fromC, $toC, $ad);
								$ad = self::convertCharsetString($fromC, $toC, $ad);
							}
						}
						$val = serialize($mydataUS);
					}
				} else {
					$val = self::convertSCharsetEncoding($fromC, $toC, $mydata);
					$val = self::convertExactCharsetString($fromC, $toC, $val);
					$val = self::convertCharsetString($fromC, $toC, $val);
				}
			}
			if($this->doCorrectExactCharsetString($key)){
				$val = self::convertExactCharsetString($fromC, $toC, $val);
			}
			if($this->doCorrectSerializedLenghtValues($key)){
				if($this->doPrepareCorrectSerializedLenghtValues($key)){
					$val = self::convertSCharsetEncoding($fromC, $toC, $val);
				}
				$val = self::correctSerDataISOtoUTF($val);
			}

			if($this->doCorrectSerializedExactCharsetString($key)){
				$mydata = $val;
				$mydataUS = @unserialize($mydata);
				if(is_array($mydataUS)){
					foreach($mydataUS as &$ad){
						if(isset($ad['Charset']) && isset($ad['Charset']['dat'])){
							$ad['Charset']['dat'] = self::convertExactCharsetString($fromC, $toC, $ad['Charset']['dat']); //tblObject
						}
					}
				}
				$val = @serialize($mydataUS);
			}
		}
	}

	static function convertCharsetString($fromC, $toC, $string){
		return str_replace($fromC, $toC, $string);
	}

	static function convertExactCharsetString($fromC, $toC, $string){
		return ($string == $fromC ? $toC : $string);
	}

//FIXME: remove
	static function serialize_fix_callback($match){
		return 's:' . strlen($match[1]) . ':"' . $match[1] . '";';
	}

//FIXME: remove
	static function correctSerDataISOtoUTF($serial_str){
		return preg_replace_callback('|s:\d+:"(.*?)";|s', 'we_backup_tableItem::serialize_fix_callback', $serial_str);
	}

}
