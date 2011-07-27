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


	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_html_tools.inc.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/base/"."weDBUtil.class.php");

	class we_updater{

	function updateTables(){
		global $DB_WE;
		$db2 = new DB_WE();
		$tables = $DB_WE->table_names();
		$hasOwnertable=false;
		foreach($tables as $t){
			// old Version of small User Module
			if($t["table_name"] == "tblOwner"){
				$hasOwnertable = true;
				break;
			}
		}
		if(!$this->isColExist(FILE_TABLE,"CreatorID")) $this->addCol(FILE_TABLE,"CreatorID","BIGINT DEFAULT '0' NOT NULL");
		if(!$this->isColExist(FILE_TABLE,"ModifierID")) $this->addCol(FILE_TABLE,"ModifierID","BIGINT DEFAULT '0' NOT NULL");
		if(!$this->isColExist(FILE_TABLE,"WebUserID")) $this->addCol(FILE_TABLE,"WebUserID","BIGINT DEFAULT '0' NOT NULL");
		if($hasOwnertable){
			$DB_WE->query("SELECT * FROM tblOwner");
			while($DB_WE->next_record()){
				$table = $DB_WE->f("DocumentTable");
				if($table==TEMPLATES_TABLE || $table == FILE_TABLE){
					$id = $DB_WE->f("fileID");
					if($table && $id){
						$Owners = ($DB_WE->f("OwnerID") && ($DB_WE->f("OwnerID") != $DB_WE->f("CreatorID"))) ? (",".$DB_WE->f("OwnerID").",") : "";
						$CreatorID = $DB_WE->f("CreatorID") ? $DB_WE->f("CreatorID") : $_SESSION["user"]["ID"];
						$ModifierID = $DB_WE->f("ModifierID") ? $DB_WE->f("ModifierID") : $_SESSION["user"]["ID"];
						$db2->query("UPDATE ".$db2->escape($table)." SET CreatorID='".abs($CreatorID)."' , ModifierID='".abs($ModifierID)."' , Owners='".$db2->escape($Owners)."' WHERE ID='".abs($id)."'");
						$db2->query("DELETE FROM tblOwner WHERE fileID='".abs($id)."'");
						@set_time_limit(30);
					}
				}
			}
			$DB_WE->query("DROP TABLE tblOwner");
		}
		//$DB_WE->query("ALTER TABLE " . INDEX_TABLE . " DROP PRIMARY KEY");

		if(!$this->isColExist(INDEX_TABLE,'Language')) $this->addCol(INDEX_TABLE,'Language',"varchar(5) default NULL");


		if(!$this->isColExist(FILE_TABLE,"Owners")) $this->addCol(FILE_TABLE,"Owners","VARCHAR(255)  DEFAULT ''");
		if(!$this->isColExist(FILE_TABLE,"RestrictOwners")) $this->addCol(FILE_TABLE,"RestrictOwners","TINYINT(1)  DEFAULT ''");
		if(!$this->isColExist(FILE_TABLE,"OwnersReadOnly")) $this->addCol(FILE_TABLE,"OwnersReadOnly","TEXT DEFAULT ''");

		if($this->isColExist(FILE_TABLE,"IsFolder")) $this->changeColTyp(FILE_TABLE,"IsFolder","tinyint(1) NOT NULL default '0'");
		if($this->isColExist(FILE_TABLE,"IsDynamic")) $this->changeColTyp(FILE_TABLE,"IsDynamic","tinyint(1) NOT NULL default '0'");
		if($this->isColExist(FILE_TABLE,"DocType")) $this->changeColTyp(FILE_TABLE,"IsFolder","varchar(64) NOT NULL default ''");

		if(!$this->isColExist(CATEGORY_TABLE,"IsFolder")) $this->addCol(CATEGORY_TABLE,"IsFolder","TINYINT(1) DEFAULT 0");
		if(!$this->isColExist(CATEGORY_TABLE,"ParentID")) $this->addCol(CATEGORY_TABLE,"ParentID","BIGINT(20) DEFAULT 0");
		if(!$this->isColExist(CATEGORY_TABLE,"Text")) $this->addCol(CATEGORY_TABLE,"Text","VARCHAR(64) DEFAULT ''");
		if(!$this->isColExist(CATEGORY_TABLE,"Path")) $this->addCol(CATEGORY_TABLE,"Path","VARCHAR(255)  DEFAULT ''");
		if(!$this->isColExist(CATEGORY_TABLE,"Icon")) $this->addCol(CATEGORY_TABLE,"Icon","VARCHAR(64) DEFAULT 'cat.gif'");
		$DB_WE->query("SELECT * FROM " . CATEGORY_TABLE);
		while($DB_WE->next_record()){
			if(($DB_WE->f("Text")==""))
				$db2->query("UPDATE " . CATEGORY_TABLE . " SET Text='".$db2->escape($DB_WE->f("Category"))."' WHERE ID='".abs($DB_WE->f("ID"))."'");
			if(($DB_WE->f("Path")==""))
				$db2->query("UPDATE " . CATEGORY_TABLE . " SET Path='/".$db2->escape($DB_WE->f("Category"))."' WHERE ID='".abs($DB_WE->f("ID"))."'");
		}

		if(!$this->isColExist(PREFS_TABLE,"seem_start_file")) $this->addCol(PREFS_TABLE,"seem_start_file","INT");
		if(!$this->isColExist(PREFS_TABLE,"seem_start_type")) $this->addCol(PREFS_TABLE,"seem_start_type","VARCHAR(10) DEFAULT ''");
		if(!$this->isColExist(PREFS_TABLE,"phpOnOff")) $this->addCol(PREFS_TABLE,"phpOnOff","TINYINT(1) DEFAULT '0' NOT NULL");
		if(!$this->isColExist(PREFS_TABLE,"editorSizeOpt")) $this->addCol(PREFS_TABLE,"editorSizeOpt","TINYINT( 1 ) DEFAULT '0' NOT NULL");
		if(!$this->isColExist(PREFS_TABLE,"editorWidth")) $this->addCol(PREFS_TABLE,"editorWidth","INT( 11 ) DEFAULT '0' NOT NULL");
		if(!$this->isColExist(PREFS_TABLE,"editorHeight")) $this->addCol(PREFS_TABLE,"editorHeight","INT( 11 ) DEFAULT '0' NOT NULL");
		if(!$this->isColExist(PREFS_TABLE,"debug_normal")) $this->addCol(PREFS_TABLE,"debug_normal","TINYINT( 1 ) DEFAULT '0' NOT NULL");
		if(!$this->isColExist(PREFS_TABLE,"debug_seem")) $this->addCol(PREFS_TABLE,"debug_seem","TINYINT( 1 ) DEFAULT '0' NOT NULL");

		if(!$this->isColExist(PREFS_TABLE,"xhtml_show_wrong")) $this->addCol(PREFS_TABLE,"xhtml_show_wrong","TINYINT(1) DEFAULT '0' NOT NULL");
  		if(!$this->isColExist(PREFS_TABLE,"xhtml_show_wrong_text")) $this->addCol(PREFS_TABLE,"xhtml_show_wrong_text","TINYINT(2) DEFAULT '0' NOT NULL");
  		if(!$this->isColExist(PREFS_TABLE,"xhtml_show_wrong_js")) $this->addCol(PREFS_TABLE,"xhtml_show_wrong_js","TINYINT(2) DEFAULT '0' NOT NULL");
  		if(!$this->isColExist(PREFS_TABLE,"xhtml_show_wrong_error_log")) $this->addCol(PREFS_TABLE,"xhtml_show_wrong_error_log","TINYINT(2) DEFAULT '0' NOT NULL");
  		if(!$this->isColExist(PREFS_TABLE,"default_tree_count")) $this->addCol(PREFS_TABLE,"default_tree_count","INT(11) DEFAULT '0' NOT NULL");

		if(!$this->isColExist(PREFS_TABLE,"editorMode")) $this->addCol(PREFS_TABLE,"editorMode","  varchar(64) NOT NULL DEFAULT 'textarea'",' AFTER  specify_jeditor_colors ');
		if(!$this->isColExist(PREFS_TABLE,"editorLinenumbers")) $this->addCol(PREFS_TABLE,"editorLinenumbers"," tinyint(1) NOT NULL default '1'",' AFTER editorMode ');
		if(!$this->isColExist(PREFS_TABLE,"editorCodecompletion")) $this->addCol(PREFS_TABLE,"editorCodecompletion"," tinyint(1) NOT NULL default '0'",' AFTER editorLinenumbers ');
		if(!$this->isColExist(PREFS_TABLE,"editorTooltips")) $this->addCol(PREFS_TABLE,"editorTooltips"," tinyint(1) NOT NULL default '1'",' AFTER editorCodecompletion ');
		if(!$this->isColExist(PREFS_TABLE,"editorTooltipFont")) $this->addCol(PREFS_TABLE,"editorTooltipFont"," tinyint(1) NOT NULL default '0'",' AFTER editorTooltips ');
		if(!$this->isColExist(PREFS_TABLE,"editorTooltipFontname")) $this->addCol(PREFS_TABLE,"editorTooltipFontname","  varchar(255) NOT NULL default 'none'",' AFTER editorTooltipFont ');
		if(!$this->isColExist(PREFS_TABLE,"editorTooltipFontsize")) $this->addCol(PREFS_TABLE,"editorTooltipFontsize"," int(2) NOT NULL default '-1'",' AFTER editorTooltipFontname ');
		if(!$this->isColExist(PREFS_TABLE,"editorDocuintegration")) $this->addCol(PREFS_TABLE,"editorDocuintegration"," tinyint(1) NOT NULL default '1'",' AFTER editorTooltipFontsize ');

		if($this->isColExist(DOC_TYPES_TABLE,"DocType")) $this->changeColTyp(DOC_TYPES_TABLE,"DocType"," varchar(64) NOT NULL default '' ");

		if($this->isColExist(ERROR_LOG_TABLE,"ID")) $this->changeColTyp(ERROR_LOG_TABLE,"ID","int(11) NOT NULL auto_increment");
		if(!$this->isColExist(ERROR_LOG_TABLE,"Type")) $this->addCol(ERROR_LOG_TABLE,"Type"," enum('Error','Warning','Parse error','Notice','Core error','Core warning','Compile error','Compile warning','User error','User warning','User notice','Deprecated notice','User deprecated notice','unknown Error') NOT NULL ",' AFTER ID ');
		if(!$this->isColExist(ERROR_LOG_TABLE,"Function")) $this->addCol(ERROR_LOG_TABLE,"Function"," varchar(255) NOT NULL default ''",' AFTER Type ');
		if(!$this->isColExist(ERROR_LOG_TABLE,"File")) $this->addCol(ERROR_LOG_TABLE,"File"," varchar(255) NOT NULL default ''",' AFTER Function ');
		if(!$this->isColExist(ERROR_LOG_TABLE,"Line")) $this->addCol(ERROR_LOG_TABLE,"Line"," int(11) NOT NULL",' AFTER File ');
		if(!$this->isColExist(ERROR_LOG_TABLE,"Backtrace")) $this->addCol(ERROR_LOG_TABLE,"Backtrace","text NOT NULL",' AFTER Text ');
		if($this->isColExist(ERROR_LOG_TABLE,"Date")) $this->changeColTyp(ERROR_LOG_TABLE,"Date","timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

		if($this->isColExist(FAILED_LOGINS_TABLE,"ID")) $this->changeColTyp(FAILED_LOGINS_TABLE,"ID","bigint(20) NOT NULL AUTO_INCREMENT");
		if($this->isColExist(FAILED_LOGINS_TABLE,"IP")) $this->changeColTyp(FAILED_LOGINS_TABLE,"IP"," varchar(40) NOT NULL");
		if($this->isColExist(FAILED_LOGINS_TABLE,"LoginDate")) $this->changeColTyp(FAILED_LOGINS_TABLE,"LoginDate"," timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

		if($this->isColExist(LINK_TABLE,"DocumentTable")) $this->changeColTyp(LINK_TABLE,"DocumentTable"," enum('tblFile','tblTemplates') NOT NULL ");
	}



	function convertPerms(){
	  global $DB_WE;
	  if($this->isColExist(USER_TABLE,"Permissions") && $this->getColTyp(USER_TABLE,"Permissions")!="text") $this->changeColTyp(USER_TABLE,"Permissions","TEXT");
	  else return;
	  $db_tmp=new DB_WE();
	  $DB_WE->query("SELECT ID,username,Permissions from " . USER_TABLE);
	  while($DB_WE->next_record()){
		$perms_slot=array();
		$pstr=$DB_WE->f("Permissions");
		$perms_slot["ADMINISTRATOR"]=$pstr["0"];
		$perms_slot["PUBLISH"]=$pstr["1"];
		if(count($perms_slot)>0){
		   $db_tmp->query("UPDATE " . USER_TABLE . " SET Permissions='".$db_tmp->escape(serialize($perms_slot))."' WHERE ID=".abs($DB_WE->f("ID")));
		}
	  }
	}

	function fix_path(){
		$db = new DB_WE();
		$db2 = new DB_WE();
		$db->query("SELECT ID,username,ParentID FROM " . USER_TABLE);
		while($db->next_record()){
			@set_time_limit(30);
			$id = $db->f("ID");
			$pid = $db->f("ParentID");
			$path = "/".$db->f("username");
			while($pid > 0){
			$db2->query("SELECT username,ParentID FROM " . USER_TABLE . " WHERE ID='".abs($pid)."'");
			if($db2->next_record()){
				$path = "/".$db2->f("username").$path;
				$pid = $db2->f("ParentID");
			}
			else $pid=0;
			}
				$db2->query("UPDATE " . USER_TABLE . " SET Path='".$db2->escape($path)."' WHERE ID='".abs($id)."'");
		}
	}

	function fix_icon(){
		$db = new DB_WE();
		$db2 = new DB_WE();
		$db->query("SELECT ID,Type FROM " . USER_TABLE);
		while($db->next_record()){
					@set_time_limit(30);
			$id = $db->f("ID");
			switch($db->f("Type")) {
			case 2:
				$icon="user_alias.gif";
				break;
			case 1:
				$icon="usergroup.gif";
				break;
			default:
				$icon="user.gif";
			}
			$db2->query("UPDATE " . USER_TABLE . " SET Icon='".$db2->escape($icon)."' WHERE ID='".abs($id)."'");
		}
	}

	function fix_icon_small(){
		$db = new DB_WE();
		$db2 = new DB_WE();
		$db->query("SELECT ID,IsFolder FROM " . USER_TABLE);
		while($db->next_record()){
					@set_time_limit(30);
			$id = $db->f("ID");
			if($db->f("IsFolder")==1) $icon="usergroup.gif";
			else $icon="user.gif";
					$db2->query("UPDATE " . USER_TABLE . " SET Icon='".$db2->escape($icon)."' WHERE ID='".abs($id)."'");
		}
	}

	function fix_text(){
		$db = new DB_WE();
		$db2 = new DB_WE();
		$db->query("SELECT ID,username FROM " . USER_TABLE);
		while($db->next_record()){
			@set_time_limit(30);
			$id = $db->f("ID");
			$text = $db->f("username");
			$db2->query("UPDATE " . USER_TABLE . " SET Text='".$db2->escape($text)."' WHERE ID='".abs($id)."'");
		}
	}

	function isColExist($tab,$col){
			global $DB_WE;
			$DB_WE->query("SHOW COLUMNS FROM ".$DB_WE->escape($tab)." LIKE '".$DB_WE->escape($col)."';");
			if($DB_WE->next_record()) return true; else return false;
	}

	function hasIndex($tab,$index){
		$GLOBALS['DB_WE']->query('SHOW INDEX FROM '.$GLOBALS['DB_WE']->escape($tab).' WHERE Key_name = "'.$index.'"');
		return $GLOBALS['DB_WE']->next_record();
	}

	function updateUnindexedCols($tab,$col){
	  global $DB_WE;
	  $DB_WE->query("SHOW COLUMNS FROM ".$DB_WE->escape($tab)." LIKE '".$DB_WE->escape($col)."';");
	  $query=array();
	  while($DB_WE->next_record()) {
		  if($DB_WE->f('Key')==''){
			  $query[]='ADD INDEX ('.$DB_WE->f('Field').')';
		  }
	  }
	  if(count($query)>0){
		  $DB_WE->query('ALTER TABLE '.$DB_WE->escape($tab).' '.implode(', ',$query));
	  }
	}

	function isTabExist($tab){
	  global $DB_WE;
	  $DB_WE->query("SHOW TABLES LIKE '".$DB_WE->escape($tab)."';");
	  if($DB_WE->next_record()) return true; else return false;
	}
	 
	function addTable($tab,$cols,$keys=array()){
	   global $DB_WE;

	   if(!is_array($cols)) return;
	   if(!count($cols)) return;
	   $cols_sql=array();
	   $key_sql=array();
	   foreach($cols as $name=>$type){
			$cols_sql[]=$name." ".$type;
	   }
	   foreach($keys as $name=>$type){
			$key_sql[]=$name." ".$type;
	   }
	   $sql_array=array_merge($cols_sql,$key_sql);

	   $DB_WE->query("CREATE TABLE ".$DB_WE->escape($tab)." (".implode(",",$sql_array).")");
	}

	function addCol($tab,$col,$typ,$pos=""){
	   global $DB_WE;
	   $DB_WE->query("ALTER TABLE ".$DB_WE->escape($tab)." ADD ".$col." ".$typ." ".(($pos!="") ? " ".$pos : "").";");
	}

	function addIndex($tab,$name,$def){
		$GLOBALS['DB_WE']->query('ALTER TABLE '.$GLOBALS['DB_WE']->escape($tab).' ADD INDEX '.$name.' ('.$def.')');
	}

	function changeColTyp($tab,$col,$newtyp){
		global $DB_WE;
		$DB_WE->query("ALTER TABLE ".$DB_WE->escape($tab)." CHANGE ".$col." ".$col." ".$newtyp.";");
	}

	function changeColName($tab,$oldcol,$newcol){
		global $DB_WE;
		$DB_WE->query("ALTER TABLE ".$DB_WE->escape($tab)." CHANGE ".$oldcol." ".$newcol.";");
	}

	function getColTyp($tab,$col){
		global $DB_WE;
		$DB_WE->query("SHOW COLUMNS FROM ".$DB_WE->escape($tab)." LIKE '".$col."';");
		if($DB_WE->next_record()) return $DB_WE->f("Type"); else return "";
	}

	function delCol($tab,$col){
		global $DB_WE;
		$DB_WE->query("ALTER TABLE ".$DB_WE->escape($tab)." DROP ".$col.";");
	}

	function updateUsers(){
		global $DB_WE;
		$db123=new DB_WE();
		if(!$this->isTabExist(USER_TABLE)) return;
		$this->convertPerms();
		if(!$this->isColExist(USER_TABLE,"Path")) $this->addCol(USER_TABLE,"Path","VARCHAR(255)  DEFAULT ''","AFTER ID");
		if(!$this->isColExist(USER_TABLE,"ParentID")) $this->addCol(USER_TABLE,"ParentID","BIGINT(20) DEFAULT '0' NOT NULL","AFTER ID");

		if(!$this->isColExist(USER_TABLE,"Icon")) $this->addCol(USER_TABLE,"Icon","VARCHAR(64)  DEFAULT ''","AFTER Permissions");
		if(!$this->isColExist(USER_TABLE,"IsFolder")) $this->addCol(USER_TABLE,"IsFolder","TINYINT(1) DEFAULT '0' NOT NULL","AFTER Permissions");
		if(!$this->isColExist(USER_TABLE,"Text")) $this->addCol(USER_TABLE,"Text","VARCHAR(255)  DEFAULT ''","AFTER Permissions");

		if($this->isColExist(USER_TABLE,"First")) $this->changeColTyp(USER_TABLE,"First","VARCHAR(255)");
		if($this->isColExist(USER_TABLE,"Second")) $this->changeColTyp(USER_TABLE,"Second","VARCHAR(255)");
		if($this->isColExist(USER_TABLE,"username")) $this->changeColTyp(USER_TABLE,"username","VARCHAR(255) NOT NULL");
		if($this->isColExist(USER_TABLE,"workSpace")) $this->changeColTyp(USER_TABLE,"workSpace","VARCHAR(255)");


		$this->fix_path();
		$this->fix_text();
		$this->fix_icon_small();

		if(!$this->isColExist(USER_TABLE,"Salutation")) $this->addCol(USER_TABLE,"Salutation","VARCHAR(32) DEFAULT ''","AFTER ParentID");
		if(!$this->isColExist(USER_TABLE,"Type")) $this->addCol(USER_TABLE,"Type","TINYINT(4) DEFAULT '0' NOT NULL","AFTER ParentID");
		if(!$this->isColExist(USER_TABLE,"Address")) $this->addCol(USER_TABLE,"Address","VARCHAR(255) DEFAULT ''","AFTER Second");
		if(!$this->isColExist(USER_TABLE,"HouseNo")) $this->addCol(USER_TABLE,"HouseNo","VARCHAR(32) DEFAULT ''","AFTER Address");
		if(!$this->isColExist(USER_TABLE,"PLZ")) $this->addCol(USER_TABLE,"PLZ","VARCHAR(32) DEFAULT ''","AFTER HouseNo");
		if(!$this->isColExist(USER_TABLE,"City")) $this->addCol(USER_TABLE,"City","VARCHAR(255) DEFAULT ''","AFTER PLZ");
		if(!$this->isColExist(USER_TABLE,"State")) $this->addCol(USER_TABLE,"State","VARCHAR(255) DEFAULT ''","AFTER City");
		if(!$this->isColExist(USER_TABLE,"Country")) $this->addCol(USER_TABLE,"Country","VARCHAR(255) DEFAULT ''","AFTER State");
		if(!$this->isColExist(USER_TABLE,"Tel_preselection")) $this->addCol(USER_TABLE,"Tel_preselection","VARCHAR(32) DEFAULT ''","AFTER Country");
		if(!$this->isColExist(USER_TABLE,"Telephone")) $this->addCol(USER_TABLE,"Telephone","VARCHAR(64) DEFAULT ''","AFTER Tel_preselection");
		if(!$this->isColExist(USER_TABLE,"Fax_preselection")) $this->addCol(USER_TABLE,"Fax_preselection","VARCHAR(32) DEFAULT ''","AFTER Telephone");
		if(!$this->isColExist(USER_TABLE,"Fax")) $this->addCol(USER_TABLE,"Fax","VARCHAR(64) DEFAULT ''","AFTER Fax_preselection");
		if(!$this->isColExist(USER_TABLE,"Handy")) $this->addCol(USER_TABLE,"Handy","VARCHAR(64) DEFAULT ''","AFTER Fax");
		if(!$this->isColExist(USER_TABLE,"Email")) $this->addCol(USER_TABLE,"Email","VARCHAR(255) DEFAULT ''","AFTER Handy");
		if(!$this->isColExist(USER_TABLE,"Description")) $this->addCol(USER_TABLE,"Description","TEXT DEFAULT ''","AFTER Email");
		if(!$this->isColExist(USER_TABLE,"workSpaceTmp")) $this->addCol(USER_TABLE,"workSpaceTmp","VARCHAR(255) DEFAULT ''","AFTER workSpace");
		if(!$this->isColExist(USER_TABLE,"workSpaceDef")) $this->addCol(USER_TABLE,"workSpaceDef","VARCHAR(255) DEFAULT ''","AFTER workSpaceTmp");
		if(!$this->isColExist(USER_TABLE,"ParentPerms")) $this->addCol(USER_TABLE,"ParentPerms","TINYINT DEFAULT '0' NOT NULL","AFTER passwd");
		if(!$this->isColExist(USER_TABLE,"ParentWs")) $this->addCol(USER_TABLE,"ParentWs","TINYINT DEFAULT '0' NOT NULL","AFTER workSpaceDef");
		if(!$this->isColExist(USER_TABLE,"ParentWst")) $this->addCol(USER_TABLE,"ParentWst","TINYINT DEFAULT '0' NOT NULL","AFTER ParentWs");
		if(!$this->isColExist(USER_TABLE,"Alias")) $this->addCol(USER_TABLE,"Alias","BIGINT DEFAULT '0' NOT NULL");

		if(!$this->isColExist(USER_TABLE,"workSpaceObj")) $this->addCol(USER_TABLE,"workSpaceObj","VARCHAR(255) DEFAULT ''","AFTER workSpace");
		if(!$this->isColExist(USER_TABLE,"ParentWso")) $this->addCol(USER_TABLE,"ParentWso","TINYINT DEFAULT '0' NOT NULL","AFTER workSpaceDef");
		if(!$this->isColExist(USER_TABLE,"workSpaceNav")) $this->addCol(USER_TABLE,"workSpaceNav","VARCHAR(255) DEFAULT ''","AFTER workSpace");
		if(!$this->isColExist(USER_TABLE,"ParentWsn")) $this->addCol(USER_TABLE,"ParentWsn","TINYINT DEFAULT '0' NOT NULL","AFTER workSpaceDef");
		if(!$this->isColExist(USER_TABLE,"workSpaceNwl")) $this->addCol(USER_TABLE,"workSpaceNwl","VARCHAR(255) DEFAULT ''","AFTER workSpace");
		if(!$this->isColExist(USER_TABLE,"ParentWsnl")) $this->addCol(USER_TABLE,"ParentWsnl","TINYINT DEFAULT '0' NOT NULL","AFTER workSpaceDef");

		if(!$this->isColExist(USER_TABLE,"LoginDenied")) $this->addCol(USER_TABLE,"LoginDenied","TINYINT(1) DEFAULT '0' NOT NULL");
		if(!$this->isColExist(USER_TABLE,"UseSalt")) $this->addCol(USER_TABLE,"UseSalt","TINYINT(1) DEFAULT '0' NOT NULL");

		if($this->isColExist(USER_TABLE,"workSpace")){
			$this->changeColTyp(USER_TABLE,"workSpace","VARCHAR(255)");
			$DB_WE->query("UPDATE " . USER_TABLE . " SET workSpace='' WHERE workSpace='0';");
		}
		if($this->isColExist(USER_TABLE,"IsFolder")){
			$DB_WE->query("SELECT ID FROM " . USER_TABLE . " WHERE Type=1");
			while($DB_WE->next_record()) $db123->query("UPDATE " . USER_TABLE . " SET IsFolder=1 WHERE ID=".abs($DB_WE->f("ID")));
		}
		$this->fix_icon();

		return true;
	}

	function updateCustomers(){
		global $DB_WE;

		if(defined("CUSTOMER_TABLE")){
			if(weModuleInfo::isModuleInstalled("customer")){
				if(!$this->isTabExist(CUSTOMER_ADMIN_TABLE)){
					$cols=array(
					  "Name"=>"VARCHAR(255) NOT NULL",
					  "Value"=>"TEXT NOT NULL"
				  );

				  $this->addTable(CUSTOMER_ADMIN_TABLE,$cols);

				  $DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('FieldAdds','');");
				  $DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('SortView','');");
				  $DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('Prefs','');");

				  include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_modules/customer/"."weCustomerSettings.php");
				  $settings=new weCustomerSettings();
				  $settings->customer=new weCustomer();
				  $fields=$settings->customer->getFieldsDbProperties();
				  $_keys = array_keys($fields);
				  foreach($_keys as $name){
					  if(!$settings->customer->isProtected($name) && !$settings->customer->isProperty($name)){
						  $settings->FieldAdds[$name]["type"]="input";
						  $settings->FieldAdds[$name]["default"]="";
					  }
				  }
				  $settings->save();
			  }

			}

			if(!$this->isColExist(CUSTOMER_TABLE,"ParentID")) $this->addCol(CUSTOMER_TABLE,"ParentID","BINGINT DEFAULT '0' NOT NULL");
			if(!$this->isColExist(CUSTOMER_TABLE,"Path")) $this->addCol(CUSTOMER_TABLE,"Path","VARCHAR(255) DEFAULT '' NOT NULL");
			if(!$this->isColExist(CUSTOMER_TABLE,"IsFolder")) $this->addCol(CUSTOMER_TABLE,"IsFolder","TINYINT(1) DEFAULT '0' NOT NULL");
			if(!$this->isColExist(CUSTOMER_TABLE,"Icon")) $this->addCol(CUSTOMER_TABLE,"Icon","VARCHAR(255) DEFAULT 'customer.gif' NOT NULL");
			if(!$this->isColExist(CUSTOMER_TABLE,"Text")) $this->addCol(CUSTOMER_TABLE,"Text","VARCHAR(255) DEFAULT '' NOT NULL");

			if(!$this->isColExist(CUSTOMER_TABLE,"Username")) $this->addCol(CUSTOMER_TABLE,"Username","VARCHAR(255) DEFAULT '' NOT NULL");
			if(!$this->isColExist(CUSTOMER_TABLE,"Password")) $this->addCol(CUSTOMER_TABLE,"Password","VARCHAR(32) DEFAULT '' NOT NULL");
			if(!$this->isColExist(CUSTOMER_TABLE,"Forename")) $this->addCol(CUSTOMER_TABLE,"Forename","VARCHAR(255) DEFAULT '' NOT NULL");
			if(!$this->isColExist(CUSTOMER_TABLE,"Surname")) $this->addCol(CUSTOMER_TABLE,"Surname","VARCHAR(255) DEFAULT '' NOT NULL");


			if(!$this->isColExist(CUSTOMER_TABLE,"LoginDenied")) $this->addCol(CUSTOMER_TABLE,"LoginDenied","TINYINT DEFAULT '0' NOT NULL");
			if(!$this->isColExist(CUSTOMER_TABLE,"MemberSince")){
				$this->addCol(CUSTOMER_TABLE,"MemberSince","int(10) NOT NULL default 0");
				$DB_WE->query("UPDATE " . CUSTOMER_ADMIN_TABLE . " SET MemberSince='".time()."';");
			}
			else $this->changeColTyp(CUSTOMER_TABLE,"MemberSince","int(10) NOT NULL default 0");

			if(!$this->isColExist(CUSTOMER_TABLE,"LastLogin")) $this->addCol(CUSTOMER_TABLE,"LastLogin","int(10) NOT NULL default 0",' AFTER MemberSince ');
			else $this->changeColTyp(CUSTOMER_TABLE,"LastLogin","int(10) NOT NULL default 0");

			if(!$this->isColExist(CUSTOMER_TABLE,"LastAccess")) $this->addCol(CUSTOMER_TABLE,"LastAccess","int(10) NOT NULL default 0",' AFTER LastLogin ');
			else $this->changeColTyp(CUSTOMER_TABLE,"LastAccess","int(10) NOT NULL default 0");

			if(!$this->isColExist(CUSTOMER_TABLE,"AutoLoginDenied")) $this->addCol(CUSTOMER_TABLE,"AutoLoginDenied","tinyint(1) NOT NULL default '0'", " AFTER LastAccess ");
			if(!$this->isColExist(CUSTOMER_TABLE,"AutoLogin")) $this->addCol(CUSTOMER_TABLE,"AutoLogin","tinyint(1) NOT NULL default '0'", " AFTER AutoLoginDenied ");

			if($this->isColExist(CUSTOMER_TABLE,"Anrede_Anrede")) $this->changeColTyp(CUSTOMER_TABLE,"Anrede_Anrede","enum('','Herr','Frau') NOT NULL");

			if($this->isColExist(CUSTOMER_TABLE,"Newsletter_Ok")) $this->changeColTyp(CUSTOMER_TABLE,"Newsletter_Ok","enum('','ja','0','1','2') NOT NULL");
			if($this->isColExist(CUSTOMER_TABLE,"Newsletter_HTMLNewsletter")) $this->changeColTyp(CUSTOMER_TABLE,"Newsletter_HTMLNewsletter","enum('','ja','0','1','2') NOT NULL");

		}
		return true;
	}

	function updateScheduler(){
		if(defined("SCHEDULE_TABLE")){
			if(!$this->isColExist(SCHEDULE_TABLE,"Schedpro")) $this->addCol(SCHEDULE_TABLE,"Schedpro","longtext DEFAULT ''");
			if(!$this->isColExist(SCHEDULE_TABLE,"Type")) $this->addCol(SCHEDULE_TABLE,"Type","TINYINT(3) DEFAULT '0' NOT NULL");
			if(!$this->isColExist(SCHEDULE_TABLE,"Active")) $this->addCol(SCHEDULE_TABLE,"Active","TINYINT(1) DEFAULT '1'");

			check_and_convert_to_sched_pro();
		}
		return true;
	}

	function updateNewsletter(){
		if(defined("NEWSLETTER_LOG_TABLE")){
				if(!$this->isColExist(NEWSLETTER_LOG_TABLE,"Param")) $this->addCol(NEWSLETTER_LOG_TABLE,"Param","VARCHAR(255) DEFAULT ''");
		}
		if(defined("NEWSLETTER_BLOCK_TABLE")){
				if(!$this->isColExist(NEWSLETTER_BLOCK_TABLE,"Pack")) $this->addCol(NEWSLETTER_BLOCK_TABLE,"Pack","TINYINT(1) DEFAULT '0'");
		}
		return true;
	}

	function updateShop(){
		if(defined("SHOP_TABLE")){
			if($this->isColExist(SHOP_TABLE,"Price")) $this->changeColTyp(SHOP_TABLE,"Price","VARCHAR(20)");
			if($this->isColExist(SHOP_TABLE,"IntQuantity")) $this->changeColTyp(SHOP_TABLE,"IntQuantity"," float ");

			if(!$this->isColExist(SHOP_TABLE,'DateConfirmation')) $this->addCol(SHOP_TABLE,'DateConfirmation','datetime default NULL',' AFTER DateOrder ');
			if(!$this->isColExist(SHOP_TABLE,'DateCustomA')) $this->addCol(SHOP_TABLE,'DateCustomA','datetime default NULL',' AFTER DateOrder ');
			if(!$this->isColExist(SHOP_TABLE,'DateCustomB')) $this->addCol(SHOP_TABLE,'DateCustomB','datetime default NULL',' AFTER DateCustomA ');
			if(!$this->isColExist(SHOP_TABLE,'DateCustomC')) $this->addCol(SHOP_TABLE,'DateCustomC','datetime default NULL',' AFTER DateCustomB ');

			if(!$this->isColExist(SHOP_TABLE,'DateCustomD')) $this->addCol(SHOP_TABLE,'DateCustomD','datetime default NULL',' AFTER DateShipping ');
			if(!$this->isColExist(SHOP_TABLE,'DateCustomE')) $this->addCol(SHOP_TABLE,'DateCustomE','datetime default NULL',' AFTER DateCustomD ');

			if(!$this->isColExist(SHOP_TABLE,'DateCustomF')) $this->addCol(SHOP_TABLE,'DateCustomF','datetime default NULL',' AFTER DatePayment ');
			if(!$this->isColExist(SHOP_TABLE,'DateCustomG')) $this->addCol(SHOP_TABLE,'DateCustomG','datetime default NULL',' AFTER DateCustomF ');
			if(!$this->isColExist(SHOP_TABLE,'DateCancellation')) $this->addCol(SHOP_TABLE,'DateCancellation','datetime default NULL',' AFTER DateCustomG ');
			if(!$this->isColExist(SHOP_TABLE,'DateCustomH')) $this->addCol(SHOP_TABLE,'DateCustomH','datetime default NULL',' AFTER DateCancellation ');
			if(!$this->isColExist(SHOP_TABLE,'DateCustomI')) $this->addCol(SHOP_TABLE,'DateCustomI','datetime default NULL',' AFTER DateCustomH ');
			if(!$this->isColExist(SHOP_TABLE,'DateCustomJ')) $this->addCol(SHOP_TABLE,'DateCustomJ','datetime default NULL',' AFTER DateCustomI ');
			if(!$this->isColExist(SHOP_TABLE,'DateFinished')) $this->addCol(SHOP_TABLE,'DateFinished','datetime default NULL',' AFTER DateCustomJ ');

			if(!$this->isColExist(SHOP_TABLE,'MailOrder')) $this->addCol(SHOP_TABLE,'MailOrder','datetime default NULL',' AFTER DateFinished ');
			if(!$this->isColExist(SHOP_TABLE,'MailConfirmation')) $this->addCol(SHOP_TABLE,'MailConfirmation','datetime default NULL',' AFTER MailOrder ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomA')) $this->addCol(SHOP_TABLE,'MailCustomA','datetime default NULL',' AFTER MailConfirmation ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomB')) $this->addCol(SHOP_TABLE,'MailCustomB','datetime default NULL',' AFTER MailCustomA ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomC')) $this->addCol(SHOP_TABLE,'MailCustomC','datetime default NULL',' AFTER MailCustomB ');
			if(!$this->isColExist(SHOP_TABLE,'MailShipping')) $this->addCol(SHOP_TABLE,'MailShipping','datetime default NULL',' AFTER MailCustomC ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomD')) $this->addCol(SHOP_TABLE,'MailCustomD','datetime default NULL',' AFTER MailShipping ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomE')) $this->addCol(SHOP_TABLE,'MailCustomE','datetime default NULL',' AFTER MailCustomD ');
			if(!$this->isColExist(SHOP_TABLE,'MailPayment')) $this->addCol(SHOP_TABLE,'MailPayment','datetime default NULL',' AFTER MailCustomE ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomF')) $this->addCol(SHOP_TABLE,'MailCustomF','datetime default NULL',' AFTER MailPayment ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomG')) $this->addCol(SHOP_TABLE,'MailCustomG','datetime default NULL',' AFTER MailCustomF ');
			if(!$this->isColExist(SHOP_TABLE,'MailCancellation')) $this->addCol(SHOP_TABLE,'MailCancellation','datetime default NULL',' AFTER MailCustomG ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomH')) $this->addCol(SHOP_TABLE,'MailCustomH','datetime default NULL',' AFTER MailCancellation ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomI')) $this->addCol(SHOP_TABLE,'MailCustomI','datetime default NULL',' AFTER MailCustomH ');
			if(!$this->isColExist(SHOP_TABLE,'MailCustomJ')) $this->addCol(SHOP_TABLE,'MailCustomJ','datetime default NULL',' AFTER MailCustomI ');
			if(!$this->isColExist(SHOP_TABLE,'MailFinished')) $this->addCol(SHOP_TABLE,'MailFinished','datetime default NULL',' AFTER MailCustomJ ');
		}
		return true;
	}

	function updateObject(){
		if(defined("OBJECT_TABLE")){
			if(!$this->isColExist(OBJECT_TABLE,'DefaultUrl')) $this->addCol(OBJECT_TABLE,'DefaultUrl',"varchar(255) NOT NULL default ''", ' AFTER  DefaultKeywords ');
			if(!$this->isColExist(OBJECT_TABLE,'DefaultUrlfield0')) $this->addCol(OBJECT_TABLE,'DefaultUrlfield0',"varchar(255) NOT NULL default ''", ' AFTER  DefaultUrl ');
			if(!$this->isColExist(OBJECT_TABLE,'DefaultUrlfield1')) $this->addCol(OBJECT_TABLE,'DefaultUrlfield1',"varchar(255) NOT NULL default ''", ' AFTER  DefaultUrlfield0 ');
			if(!$this->isColExist(OBJECT_TABLE,'DefaultUrlfield2')) $this->addCol(OBJECT_TABLE,'DefaultUrlfield2',"varchar(255) NOT NULL default ''", ' AFTER  DefaultUrlfield1 ');
			if(!$this->isColExist(OBJECT_TABLE,'DefaultUrlfield3')) $this->addCol(OBJECT_TABLE,'DefaultUrlfield3',"varchar(255) NOT NULL default ''", ' AFTER  DefaultUrlfield2 ');
			if(!$this->isColExist(OBJECT_TABLE,'DefaultTriggerID')) $this->addCol(OBJECT_TABLE,'DefaultTriggerID',"bigint(20) NOT NULL default 0", ' AFTER  DefaultUrlfield3 ');
		}
	}
	function updateObjectFiles(){
		if(defined("OBJECT_FILES_TABLE")){
			if(!$this->isColExist(OBJECT_FILES_TABLE,'Url')) $this->addCol(OBJECT_FILES_TABLE,'Url',"varchar(255) NOT NULL default ''", ' AFTER Path ');
			if(!$this->isColExist(OBJECT_FILES_TABLE,'TriggerID')) $this->addCol(OBJECT_FILES_TABLE,'TriggerID',"bigint NOT NULL default '0'", ' AFTER Url ');
		}
	}

	function updateObjectFilesX() {
		if(defined('OBJECT_X_TABLE')){
			$_db = new DB_WE();

			$_table = OBJECT_FILES_TABLE;
			if($this->isColExist($_table,'Url')){
				$this->changeColTyp($_table,'Url','VARCHAR(255) NOT NULL');
			} else {
				$this->addCol($_table,'Url','VARCHAR(255) NOT NULL',' AFTER Path ');
			}
			if($this->isColExist($_table,'TriggerID')){
				$this->changeColTyp($_table,'TriggerID',"bigint NOT NULL default '0'");
			} else {
				$this->addCol($_table,'TriggerID',"bigint NOT NULL default '0'",' AFTER Url ');
			}
			if($this->isColExist($_table,'IsSearchable')){
				$this->changeColTyp($_table,'IsSearchable','TINYINT(1) DEFAULT 1');
			} else {
				$this->addCol($_table,'IsSearchable','TINYINT(1) DEFAULT 1',' AFTER Published ');
			}
			if($this->isColExist($_table,'Charset')){
				$this->changeColTyp($_table,'Charset','VARCHAR(64) DEFAULT NULL');
			} else {
				$this->addCol($_table,'Charset','VARCHAR(64) DEFAULT NULL',' AFTER IsSearchable ');
			}
			if($this->isColExist($_table,'Language')){
				$this->changeColTyp($_table,'Language','VARCHAR(5) DEFAULT NULL');
			} else {
				$this->addCol($_table,'Language','VARCHAR(5) DEFAULT NULL',' AFTER Charset ');
			}
			if($this->isColExist($_table,'WebUserID')){
				$this->changeColTyp($_table,'WebUserID','BIGINT(20) NOT NULL');
			} else {
				$this->addCol($_table,'WebUserID','BIGINT(20) NOT NULL',' AFTER Language ');
			}

			$_maxid = f('SELECT MAX(ID) as MaxTID FROM ' . OBJECT_TABLE . ';','MaxTID',$_db);
			$_maxid++;
			for($i=1;$i<$_maxid;$i++) {
				$_table = OBJECT_X_TABLE . $i;
				if ($this->isTabExist($_table)) {
					if($this->isColExist($_table,'OF_Url')){
						$this->changeColTyp($_table,'OF_Url','VARCHAR(255) NOT NULL');
					} else {
						$this->addCol($_table,'OF_Url','VARCHAR(255) NOT NULL','  AFTER OF_Path  ');
					}
					if($this->isColExist($_table,'OF_TriggerID')){
						$this->changeColTyp($_table,'OF_TriggerID','BIGINT(20) NOT NULL DEFAULT 0');
					} else {
						$this->addCol($_table,'OF_TriggerID','BIGINT(20) NOT NULL DEFAULT 0','  AFTER OF_Url  ');
					}
					if($this->isColExist($_table,'OF_IsSearchable')){
						$this->changeColTyp($_table,'OF_IsSearchable','TINYINT(1) DEFAULT 1');
					} else {
						$this->addCol($_table,'OF_IsSearchable','TINYINT(1) DEFAULT 1',' AFTER OF_Published ');
					}
					if($this->isColExist($_table,'OF_Charset')){
						$this->changeColTyp($_table,'OF_Charset','VARCHAR(64) NOT NULL');
					} else {
						$this->addCol($_table,'OF_Charset','VARCHAR(64) NOT NULL',' AFTER OF_IsSearchable ');
					}
					if($this->isColExist($_table,'OF_WebUserID')){
						$this->changeColTyp($_table,'OF_WebUserID','BIGINT(20) NOT NULL');
					} else {
						$this->addCol($_table,'OF_WebUserID','BIGINT(20) NOT NULL',' AFTER OF_Charset ');
					}
					if($this->isColExist($_table,'OF_Language')){
						$this->changeColTyp($_table,'OF_Language','VARCHAR(5) DEFAULT NULL');
					} else {
						$this->addCol($_table,'OF_Language','VARCHAR(5) DEFAULT NULL',' AFTER OF_WebUserID ');
					}
					//add indices to all objects
					$this->updateUnindexedCols($_table,'object_%');

					if(!$this->hasIndex($_table, 'OF_WebUserID')){
						$this->addIndex($_table,'OF_WebUserID','OF_WebUserID');
					}
					if(!$this->hasIndex($_table, 'published')){
						$this->addIndex($_table,'published','OF_ID,OF_Published,OF_IsSearchable');
					}
					if(!$this->hasIndex($_table, 'OF_IsSearchable')){
						$this->addIndex($_table,'OF_IsSearchable','OF_IsSearchable');
					}
				}
			}
		}
		return true;
	}



	function updateNavigation(){
		if(!$this->isColExist(NAVIGATION_TABLE,"Charset")){
			$this->addCol(NAVIGATION_TABLE,'`Charset`','varchar(255) NOT NULL default ""');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"Attributes")){
			$this->addCol(NAVIGATION_TABLE,'`Attributes`','text NOT NULL');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"FolderSelection")){
			$this->addCol(NAVIGATION_TABLE,'`FolderSelection`','varchar(32) NOT NULL default ""');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"FolderWsID")){
			$this->addCol(NAVIGATION_TABLE,'`FolderWsID`','bigint(20) NOT NULL default "0"');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"FolderParameter")){
			$this->addCol(NAVIGATION_TABLE,'`FolderParameter`','varchar(255) NOT NULL default ""');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"FolderUrl")){
			$this->addCol(NAVIGATION_TABLE,'`FolderUrl`','varchar(255) NOT NULL default ""');
		}

		if(!$this->isColExist(NAVIGATION_TABLE,"LimitAccess")){
			$this->addCol(NAVIGATION_TABLE,'`LimitAccess`','tinyint(4) NOT NULL default 0');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"AllCustomers")){
			$this->addCol(NAVIGATION_TABLE,'`AllCustomers`','tinyint(4) NOT NULL default 0');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"ApplyFilter")){
			$this->addCol(NAVIGATION_TABLE,'`ApplyFilter`','tinyint(4) NOT NULL default 0');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"Customers")){
			$this->addCol(NAVIGATION_TABLE,'`Customers`','text NOT NULL');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"CustomerFilter")){
			$this->addCol(NAVIGATION_TABLE,'`CustomerFilter`','text NOT NULL');
		}

		if(!$this->isColExist(NAVIGATION_TABLE,"Published")){
			$this->addCol(NAVIGATION_TABLE,'Published','int(11) NOT NULL DEFAULT "1"',' AFTER Path ');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"Display")){
			$this->addCol(NAVIGATION_TABLE,'Display','varchar(255) NOT NULL ',' AFTER Text ');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"CurrentOnUrlPar")){
			$this->addCol(NAVIGATION_TABLE,'CurrentOnUrlPar','tinyint(1) NOT NULL DEFAULT 0',' AFTER Text ');
		}
		if(!$this->isColExist(NAVIGATION_TABLE,"CurrentOnAnker")){
			$this->addCol(NAVIGATION_TABLE,'CurrentOnAnker','tinyint(1) NOT NULL DEFAULT 0',' AFTER CurrentOnUrlPar ');
		}


	}

	function updateVoting(){
		if(defined('VOTING_TABLE')){
			if(!$this->isColExist(VOTING_TABLE,'QASetAdditions')){
				$this->addCol(VOTING_TABLE,'QASetAdditions','text',' AFTER QASet ');
			}
			if(!$this->isColExist(VOTING_TABLE,'IsRequired')){
				$this->addCol(VOTING_TABLE,'IsRequired','tinyint(1) NOT NULL DEFAULT 0',' AFTER QASetAdditions ');
			}
			if(!$this->isColExist(VOTING_TABLE,'AllowFreeText')){
				$this->addCol(VOTING_TABLE,'AllowFreeText','tinyint(1) NOT NULL DEFAULT 0',' AFTER IsRequired ');
			}
			if(!$this->isColExist(VOTING_TABLE,'AllowImages')){
				$this->addCol(VOTING_TABLE,'AllowImages','tinyint(1) NOT NULL DEFAULT 0',' AFTER AllowFreeText ');
			}
			if(!$this->isColExist(VOTING_TABLE,'AllowMedia')){
				$this->addCol(VOTING_TABLE,'AllowMedia','tinyint(1) NOT NULL DEFAULT 0',' AFTER AllowImages ');
			}
			if(!$this->isColExist(VOTING_TABLE,'AllowSuccessor')){
				$this->addCol(VOTING_TABLE,'AllowSuccessor','tinyint(1) NOT NULL DEFAULT 0',' AFTER AllowMedia ');
			}
			if(!$this->isColExist(VOTING_TABLE,'AllowSuccessors')){
				$this->addCol(VOTING_TABLE,'AllowSuccessors','tinyint(1) NOT NULL DEFAULT 0',' AFTER AllowSuccessor ');
			}
			if(!$this->isColExist(VOTING_TABLE,'Successor')){
				$this->addCol(VOTING_TABLE,'Successor','bigint(20) unsigned NOT NULL DEFAULT 0',' AFTER AllowSuccessors ');
			}
			if(!$this->isColExist(VOTING_TABLE,'FallbackUserID')){
				$this->addCol(VOTING_TABLE,'FallbackUserID','tinyint(1) NOT NULL DEFAULT 0',' AFTER UserAgent ');
			}

			if(!$this->isColExist(VOTING_LOG_TABLE,'votingsession')){
				$this->addCol(VOTING_LOG_TABLE,'votingsession','tinyint(1) NOT NULL DEFAULT 0',' AFTER id ');
			}
			if(!$this->isColExist(VOTING_LOG_TABLE,'userid')){
				$this->addCol(VOTING_LOG_TABLE,'userid','bigint(20) NOT NULL DEFAULT 0',' AFTER agent ');
			}
			if(!$this->isColExist(VOTING_LOG_TABLE,'answer')){
				$this->addCol(VOTING_LOG_TABLE,'answer','varchar(255) NOT NULL',' AFTER status ');
			}
			if(!$this->isColExist(VOTING_LOG_TABLE,'answertext')){
				$this->addCol(VOTING_LOG_TABLE,'answertext','text NOT NULL',' AFTER answer ');
			}
			if(!$this->isColExist(VOTING_LOG_TABLE,'successor')){
				$this->addCol(VOTING_LOG_TABLE,'successor','bigint(20) unsigned NOT NULL DEFAULT 0',' AFTER answertext ');
			}
			if(!$this->isColExist(VOTING_LOG_TABLE,'additionalfields')){
				$this->addCol(VOTING_LOG_TABLE,'additionalfields','text NOT NULL',' AFTER successor ');
			}
		}
	}

	function updateVersions(){
		if(defined("VERSIONS_TABLE")){
			if($this->isColExist(VERSIONS_TABLE,'DocType')) $this->changeColTyp(VERSIONS_TABLE,'DocType','varchar(64) NOT NULL');
			if(!$this->isColExist(VERSIONS_TABLE,'MasterTemplateID')) $this->addCol(VERSIONS_TABLE,'MasterTemplateID',"bigint(20) NOT NULL default '0'",' AFTER ExtraTemplates ');
		}
	}
	function updateWorkflow(){
		if(defined('WORKFLOW_STEP_TABLE')){
			if($this->isColExist(WORKFLOW_STEP_TABLE,'Worktime')) $this->changeColTyp(WORKFLOW_STEP_TABLE,'Worktime','float NOT NULL default 0');
		}
		if(defined('WORKFLOW_TABLE')){
			if(!$this->isColExist(WORKFLOW_TABLE,'DocType')) $this->changeColTyp(WORKFLOW_TABLE,'DocType',"varchar(255) NOT NULL default '0'");
			if(!$this->isColExist(WORKFLOW_TABLE,'ObjectFileFolders')) $this->addCol(WORKFLOW_TABLE,'ObjectFileFolders',"varchar(255) NOT NULL default ''",' AFTER Objects ');
			if(!$this->isColExist(WORKFLOW_TABLE,'EmailPath')) $this->addCol(WORKFLOW_TABLE,'EmailPath','tinyint(1) NOT NULL DEFAULT 0',' AFTER Status ');
			if(!$this->isColExist(WORKFLOW_TABLE,'LastStepAutoPublish')) $this->addCol(WORKFLOW_TABLE,'LastStepAutoPublish','tinyint(1) NOT NULL DEFAULT 0',' AFTER EmailPath ');
		}
	}

	function updateLock(){
		if(!$this->isTabExist(LOCK_TABLE)){
			$cols=array(
				"ID"=>"bigint(20) NOT NULL default '0'",
				"sessionID"=>"varchar(64) NOT NULL default ''",
				"lockTime"=>"datetime NOT NULL",
				"tbl"=>"varchar(32) NOT NULL default ''"
				);
			$keys=array(
				"PRIMARY KEY"=>"(ID,tbl)",
				"KEY UserID"=>"(UserID,sessionID)",
				"KEY lockTime"=>"(lockTime)"
				);
			$this->addTable(LOCK_TABLE,$cols,$keys);	
		}
		if(!$this->isColExist(LOCK_TABLE,'sessionID'))  $this->addCol(LOCK_TABLE,'sessionID',"varchar(64) NOT NULL default ''",' AFTER UserID ');
		if($this->isColExist(LOCK_TABLE,'lock')) $this->changeColName(LOCK_TABLE,'lock','lockTime');
		if(!$this->isColExist(LOCK_TABLE,'lockTime'))  $this->addCol(LOCK_TABLE,'lockTime',"datetime NOT NULL",' AFTER sessionID ');
	}

	function updateTableKeys(){
		if (isset($_SESSION['weBackupVars']['tablekeys']) && is_array($_SESSION['weBackupVars']['tablekeys'])) {
			$myarray= $_SESSION['weBackupVars']['tablekeys'];
			foreach($myarray as $k => $v){
				if (is_array($v)){
					foreach($v as $tabkey){
						if(!weDBUtil::isKeyExist($k,$tabkey)) {
							weDBUtil::addKey($k,$tabkey);
						}
					}
				}
			}
		}
		return true;
	}
	function updateLangLink(){
		if(!$this->isTabExist(LANGLINK_TABLE)){
			$cols=array(
				"ID"=>"int(11) NOT NULL AUTO_INCREMENT",
				"DID"=>"DID int(11) NOT NULL default '0'",
				"DLocale"=>"varchar(5) NOT NULL default ''",
				"IsFolder"=>"tinyint(1) NOT NULL default '0'",
				"IsObject"=>"tinyint(1) NOT NULL default '0'",
				"LDID"=>"int(11) NOT NULL default '0'",
				"Locale"=>" varchar(5) NOT NULL default ''",
				"DocumentTable"=>"enum('tblFile','tblObjectFile','tblDocTypes') NOT NUL"
				);
			$keys=array(
				"PRIMARY KEY"=>"(ID)",
				"KEY DID"=>"(DID,Locale(5))"
				);
			$this->addTable(LOCK_TABLE,$cols,$keys);				
		}
		if(!$this->isColExist(LANGLINK_TABLE,'DLocale'))  $this->addCol(LANGLINK_TABLE,'DLocale',"varchar(5) NOT NULL default ''",' AFTER DID ');
	}

	function doUpdate(){
		$this->updateTables();
		$this->updateUsers();
		$this->updateShop();
		$this->updateNewsletter();
		$this->updateObjectFilesX();
		$this->updateNavigation();
		$this->updateScheduler();
		$this->updateVoting();
		$this->updateVersions();
		$this->updateWorkflow();
		$this->updateLock();
		$this->updateLangLink();
		$this->updateTableKeys();
	}

}
