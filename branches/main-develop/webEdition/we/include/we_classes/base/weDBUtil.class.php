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
 * Class weDBUtil
 *
 * Implements db operations
 */

	include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we.inc.php");

	class weDBUtil {

		function isColExist($tab,$col){
			if($tab==''||$col==''){
				return false;
			}
			global $DB_WE;
			$DB_WE->query("SHOW COLUMNS FROM ".$DB_WE->escape($tab)." LIKE '$col';");
			return ($DB_WE->next_record());
		}

		function isTabExist($tab){
			if($tab==''){
				return false;
			}
			global $DB_WE;
			$DB_WE->query("SHOW TABLES LIKE '".$DB_WE->escape($tab)."';");
			return (bool)($DB_WE->num_rows());
		}

		function addTable($tab,$cols,$keys=array()){
			global $DB_WE;

			if(!is_array($cols)) return;
			if(!count($cols)) return;
			$cols_sql=array();
			foreach($cols as $name=>$type){
				$cols_sql[]="`" . $name."` ".$type;
			}
			if(count($keys)) {
				foreach($keys as $key){
			   		$cols_sql[]=$key;
			   	}
			}

			// Charset and Collation
			$charset_collation = "";
			if (defined("DB_CHARSET") && DB_CHARSET != "" && defined("DB_COLLATION") && DB_COLLATION != "") {
				$Charset = DB_CHARSET;
				$Collation = DB_COLLATION;
				$charset_collation = " CHARACTER SET " . $Charset . " COLLATE " . $Collation;

			}

			return $DB_WE->query("CREATE TABLE ".$DB_WE->escape($tab)." (".implode(",",$cols_sql).") ENGINE = MYISAM $charset_collation;") ? true : false;


		}

		function delTable($tab){
			   global $DB_WE;
				$DB_WE->query("DROP TABLE IF EXISTS ".$DB_WE->escape($tab).";");
		}

		function addCol($tab,$col,$typ,$pos=""){
			   global $DB_WE;
			   $DB_WE->query("ALTER TABLE ".$DB_WE->escape($tab)." ADD $col $typ".(($pos!="") ? " ".$pos : "").";");
		}

		function changeColTyp($tab,$col,$newtyp){
			   global $DB_WE;
			   $DB_WE->query("ALTER TABLE ".$DB_WE->escape($tab)." CHANGE $col $col $newtyp;");
		}

		function getColTyp($tab,$col){
			   global $DB_WE;
			   $DB_WE->query("SHOW COLUMNS FROM ".$DB_WE->escape($tab)." LIKE '$col';");
			   if($DB_WE->next_record()) return $DB_WE->f("Type"); else return "";
		}

		function delCol($tab,$col){
			   global $DB_WE;
			   $DB_WE->query("ALTER TABLE ".$DB_WE->escape($tab)." DROP $col;");
		}

		function getTableCreateArray($tab){
			global $DB_WE;
			$DB_WE->query("SHOW CREATE TABLE ".$DB_WE->escape($tab));
			if($DB_WE->next_record()) {
				return explode("\n",$DB_WE->f("Create Table"));
			} else {
				return false;
			}
		}

		function getTableKeyArray($tab){
			global $DB_WE;
			$myarray = array();
			$DB_WE->query("SHOW CREATE TABLE ".$DB_WE->escape($tab));
			if($DB_WE->next_record()) {
				$zw=explode("\n",$DB_WE->f("Create Table"));
				foreach ($zw as $k => $v){
					$vv = trim($v);
					$posP = strpos($vv,'PRIMARY KEY');
					$posU = strpos($vv,'UNIQUE KEY');
					$posK = strpos($vv,'KEY');
					if( ($posP !== false && $posP == 0) || ($posU !== false && $posU == 0) || ($posK !== false && $posK == 0) ){
						$myarray[] = trim(rtrim($v,','));
					}
				}
				return $myarray;
			} else {
				return false;
			}

		}
		function isKeyExist($tab,$key){
			global $DB_WE;
			$DB_WE->query("SHOW CREATE TABLE ".$DB_WE->escape($tab));
			if($DB_WE->next_record()) {
				$zw=explode("\n",$DB_WE->f("Create Table"));
				foreach ($zw as $k => $v){
					if (trim(rtrim($v,','))==$key) return true;
				}
			}
			return false;
		}
		function addKey($tab,$key){
			global $DB_WE;
			$DB_WE->query("ALTER TABLE ".$DB_WE->escape($tab)." ADD ".$key.";");
		}

	}
