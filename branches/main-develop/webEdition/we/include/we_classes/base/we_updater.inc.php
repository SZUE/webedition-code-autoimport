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
class we_updater{

	static function updateTables(){
		global $DB_WE;
		$db2 = new DB_WE();
		$tables = $db2->table_names();
		$hasOwnertable = false;
		foreach($tables as $t){
			// old Version of small User Module
			if($t["table_name"] == "tblOwner"){
				$hasOwnertable = true;
				break;
			}
		}
		if(!self::isColExist(FILE_TABLE, "CreatorID"))
			self::addCol(FILE_TABLE, "CreatorID", "BIGINT DEFAULT '0' NOT NULL");
		if(!self::isColExist(FILE_TABLE, "ModifierID"))
			self::addCol(FILE_TABLE, "ModifierID", "BIGINT DEFAULT '0' NOT NULL");
		if(!self::isColExist(FILE_TABLE, "WebUserID"))
			self::addCol(FILE_TABLE, "WebUserID", "BIGINT DEFAULT '0' NOT NULL");
		if($hasOwnertable){
			$DB_WE->query("SELECT * FROM tblOwner");
			while($DB_WE->next_record()) {
				$table = $DB_WE->f("DocumentTable");
				if($table == TEMPLATES_TABLE || $table == FILE_TABLE){
					$id = $DB_WE->f("fileID");
					if($table && $id){
						$Owners = ($DB_WE->f("OwnerID") && ($DB_WE->f("OwnerID") != $DB_WE->f("CreatorID"))) ? ("," . $DB_WE->f("OwnerID") . ",") : "";
						$CreatorID = $DB_WE->f("CreatorID") ? $DB_WE->f("CreatorID") : $_SESSION["user"]["ID"];
						$ModifierID = $DB_WE->f("ModifierID") ? $DB_WE->f("ModifierID") : $_SESSION["user"]["ID"];
						$db2->query("UPDATE " . $db2->escape($table) . " SET CreatorID=" . intval($CreatorID) . " , ModifierID=" . intval($ModifierID) . " , Owners='" . $db2->escape($Owners) . "' WHERE ID=" . intval($id));
						$db2->query("DELETE FROM tblOwner WHERE fileID=" . intval($id));
						@set_time_limit(30);
					}
				}
			}
			$DB_WE->query("DROP TABLE tblOwner");
		}

		self::addCol(INDEX_TABLE, 'Language', "varchar(5) default NULL");


		self::addCol(FILE_TABLE, "Owners", "VARCHAR(255)  DEFAULT ''");
		self::addCol(FILE_TABLE, "RestrictOwners", "TINYINT(1)  DEFAULT ''");
		self::addCol(FILE_TABLE, "OwnersReadOnly", "TEXT DEFAULT ''");

		if(self::isColExist(FILE_TABLE, "IsFolder"))
			self::changeColTyp(FILE_TABLE, "IsFolder", "tinyint(1) NOT NULL default '0'");
		if(self::isColExist(FILE_TABLE, "IsDynamic"))
			self::changeColTyp(FILE_TABLE, "IsDynamic", "tinyint(1) NOT NULL default '0'");
		if(self::isColExist(FILE_TABLE, "DocType"))
			self::changeColTyp(FILE_TABLE, "IsFolder", "varchar(64) NOT NULL default ''");

		self::addCol(CATEGORY_TABLE, "IsFolder", "TINYINT(1) DEFAULT 0");
		self::addCol(CATEGORY_TABLE, "ParentID", "BIGINT(20) DEFAULT 0");
		self::addCol(CATEGORY_TABLE, "Text", "VARCHAR(64) DEFAULT ''");
		self::addCol(CATEGORY_TABLE, "Path", "VARCHAR(255)  DEFAULT ''");
		self::addCol(CATEGORY_TABLE, "Icon", "VARCHAR(64) DEFAULT 'cat.gif'");
		$DB_WE->query("SELECT * FROM " . CATEGORY_TABLE);
		while($DB_WE->next_record()) {
			if(($DB_WE->f("Text") == ""))
				$db2->query("UPDATE " . CATEGORY_TABLE . " SET Text='" . $db2->escape($DB_WE->f("Category")) . "' WHERE ID=" . intval($DB_WE->f("ID")));
			if(($DB_WE->f("Path") == ""))
				$db2->query("UPDATE " . CATEGORY_TABLE . " SET Path='/" . $db2->escape($DB_WE->f("Category")) . "' WHERE ID=" . intval($DB_WE->f("ID")));
		}

		self::addCol(PREFS_TABLE, "seem_start_file", "INT");
		self::addCol(PREFS_TABLE, "seem_start_type", "VARCHAR(10) DEFAULT ''");
		self::addCol(PREFS_TABLE, "seem_start_weapp", "VARCHAR(255) DEFAULT ''", ' AFTER seem_start_type ');
		self::addCol(PREFS_TABLE, "phpOnOff", "TINYINT(1) DEFAULT '0' NOT NULL");
		self::addCol(PREFS_TABLE, "editorSizeOpt", "TINYINT( 1 ) DEFAULT '0' NOT NULL");
		self::addCol(PREFS_TABLE, "editorWidth", "INT( 11 ) DEFAULT '0' NOT NULL");
		self::addCol(PREFS_TABLE, "editorHeight", "INT( 11 ) DEFAULT '0' NOT NULL");
		self::addCol(PREFS_TABLE, "debug_normal", "TINYINT( 1 ) DEFAULT '0' NOT NULL");
		self::addCol(PREFS_TABLE, "debug_seem", "TINYINT( 1 ) DEFAULT '0' NOT NULL");

		self::addCol(PREFS_TABLE, "xhtml_show_wrong", "TINYINT(1) DEFAULT '0' NOT NULL");
		self::addCol(PREFS_TABLE, "xhtml_show_wrong_text", "TINYINT(2) DEFAULT '0' NOT NULL");
		self::addCol(PREFS_TABLE, "xhtml_show_wrong_js", "TINYINT(2) DEFAULT '0' NOT NULL");
		self::addCol(PREFS_TABLE, "xhtml_show_wrong_error_log", "TINYINT(2) DEFAULT '0' NOT NULL");
		self::addCol(PREFS_TABLE, "default_tree_count", "smallint unsigned DEFAULT '0' NOT NULL");

		self::addCol(PREFS_TABLE, "editorMode", "  varchar(64) NOT NULL DEFAULT 'textarea'", ' AFTER  specify_jeditor_colors ');
		self::addCol(PREFS_TABLE, "editorLinenumbers", " tinyint(1) NOT NULL default '1'", ' AFTER editorMode ');
		self::addCol(PREFS_TABLE, "editorCodecompletion", " tinyint(1) NOT NULL default '0'", ' AFTER editorLinenumbers ');
		self::addCol(PREFS_TABLE, "editorTooltips", " tinyint(1) NOT NULL default '1'", ' AFTER editorCodecompletion ');
		self::addCol(PREFS_TABLE, "editorTooltipFont", " tinyint(1) NOT NULL default '0'", ' AFTER editorTooltips ');
		self::addCol(PREFS_TABLE, "editorTooltipFontname", "  varchar(255) NOT NULL default 'none'", ' AFTER editorTooltipFont ');
		self::addCol(PREFS_TABLE, "editorTooltipFontsize", " int(2) NOT NULL default '-1'", ' AFTER editorTooltipFontname ');
		self::addCol(PREFS_TABLE, "editorDocuintegration", " tinyint(1) NOT NULL default '1'", ' AFTER editorTooltipFontsize ');

		if(self::isColExist(DOC_TYPES_TABLE, "DocType"))
			self::changeColTyp(DOC_TYPES_TABLE, "DocType", " varchar(64) NOT NULL default '' ");

		self::changeColTyp(ERROR_LOG_TABLE, "ID", "int(11) NOT NULL auto_increment");
		self::addCol(ERROR_LOG_TABLE, "Type", " enum('Error','Warning','Parse error','Notice','Core error','Core warning','Compile error','Compile warning','User error','User warning','User notice','Deprecated notice','User deprecated notice','unknown Error') NOT NULL ", ' AFTER ID ');
		self::addCol(ERROR_LOG_TABLE, "Function", " varchar(255) NOT NULL default ''", ' AFTER Type ');
		self::addCol(ERROR_LOG_TABLE, "File", " varchar(255) NOT NULL default ''", ' AFTER Function ');
		self::addCol(ERROR_LOG_TABLE, "Line", " int(11) NOT NULL", ' AFTER File ');
		self::addCol(ERROR_LOG_TABLE, "Backtrace", "text NOT NULL", ' AFTER Text ');
		self::changeColTyp(ERROR_LOG_TABLE, "Date", "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

		if(self::isColExist(FAILED_LOGINS_TABLE, "ID"))
			self::changeColTyp(FAILED_LOGINS_TABLE, "ID", "bigint(20) NOT NULL AUTO_INCREMENT");
		if(self::isColExist(FAILED_LOGINS_TABLE, "IP"))
			self::changeColTyp(FAILED_LOGINS_TABLE, "IP", " varchar(40) NOT NULL");
		if(self::isColExist(FAILED_LOGINS_TABLE, "LoginDate"))
			self::changeColTyp(FAILED_LOGINS_TABLE, "LoginDate", " timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

		if(self::isColExist(LINK_TABLE, "DocumentTable"))
			self::changeColTyp(LINK_TABLE, "DocumentTable", " enum('tblFile','tblTemplates') NOT NULL ");

		if(defined('GLOSSARY_TABLE')){
			self::changeColTyp(GLOSSARY_TABLE, "`Type`", " enum('abbreviation','acronym','foreignword','link','textreplacement') NOT NULL default 'abbreviation'");
			self::changeColTyp(GLOSSARY_TABLE, "`Icon`", " enum('folder.gif','prog.gif') NOT NULL ");
		}
		self::addCol(THUMBNAILS_TABLE, "Fitinside", " smallint(5) unsigned NOT NULL default '0' ", ' AFTER Interlace ');
	}

	static function convertPerms(){
		global $DB_WE;
		if(!(self::isColExist(USER_TABLE, "Permissions") && self::getColTyp(USER_TABLE, "Permissions") != "text")){
			return;
		}
		self::changeColTyp(USER_TABLE, "Permissions", "TEXT");
		$db_tmp = new DB_WE();
		$DB_WE->query("SELECT ID,username,Permissions from " . USER_TABLE);
		while($DB_WE->next_record()) {
			$perms_slot = array();
			$pstr = $DB_WE->f("Permissions");
			$perms_slot["ADMINISTRATOR"] = $pstr["0"];
			$perms_slot["PUBLISH"] = $pstr["1"];
			if(count($perms_slot) > 0){
				$db_tmp->query("UPDATE " . USER_TABLE . " SET Permissions='" . $db_tmp->escape(serialize($perms_slot)) . "' WHERE ID=" . intval($DB_WE->f("ID")));
			}
		}
	}

	static function fix_path(){
		$db = new DB_WE();
		$db2 = new DB_WE();
		$db->query("SELECT ID,username,ParentID FROM " . USER_TABLE);
		while($db->next_record()) {
			@set_time_limit(30);
			$id = $db->f("ID");
			$pid = $db->f("ParentID");
			$path = "/" . $db->f("username");
			while($pid > 0) {
				$db2->query("SELECT username,ParentID FROM " . USER_TABLE . " WHERE ID=" . intval($pid));
				if($db2->next_record()){
					$path = "/" . $db2->f("username") . $path;
					$pid = $db2->f("ParentID");
				} else{
					$pid = 0;
				}
			}
			$db2->query("UPDATE " . USER_TABLE . " SET Path='" . $db2->escape($path) . "' WHERE ID=" . intval($id));
		}
	}

	static function fix_icon(){
		$db = new DB_WE();
		$db->query("UPDATE " . USER_TABLE . " SET Icon='user_alias.gif' WHERE Type=2");
		$db->query("UPDATE " . USER_TABLE . " SET Icon='usergroup.gif' WHERE Type=1");
		$db->query("UPDATE " . USER_TABLE . " SET Icon='user.gif' WHERE Type NOT IN(1,2)");
	}

	static function fix_icon_small(){
		$db = new DB_WE();
		$db->query("UPDATE " . USER_TABLE . " SET Icon='usergroup.gif' WHERE IsFolder=1");
		$db->query("UPDATE " . USER_TABLE . " SET Icon='user.gif' WHERE IsFolder=0");
	}

	static function fix_text(){
		$db = new DB_WE();
		$db2 = new DB_WE();
		$db->query("SELECT ID,username FROM " . USER_TABLE);
		while($db->next_record()) {
			@set_time_limit(30);
			$id = $db->f("ID");
			$text = $db->f("username");
			$db2->query("UPDATE " . USER_TABLE . " SET Text='" . $db2->escape($text) . "' WHERE ID=" . intval($id));
		}
	}

	static function isColExist($tab, $col){
		$DB_WE = $GLOBALS['DB_WE'];
		$DB_WE->query("SHOW COLUMNS FROM " . $DB_WE->escape($tab) . " LIKE '" . $DB_WE->escape(trim($col, '`')) . "';");
		return ($DB_WE->next_record());
	}

	static function hasIndex($tab, $index){
		$GLOBALS['DB_WE']->query('SHOW INDEX FROM ' . $GLOBALS['DB_WE']->escape($tab) . ' WHERE Key_name = "' . $index . '"');
		return $GLOBALS['DB_WE']->next_record();
	}

	static function updateUnindexedCols($tab, $col){
		global $DB_WE;
		$DB_WE->query("SHOW COLUMNS FROM " . $DB_WE->escape($tab) . " LIKE '" . $DB_WE->escape($col) . "'");
		$query = array();
		while($DB_WE->next_record()) {
			if($DB_WE->f('Key') == ''){
				$query[] = 'ADD INDEX (' . $DB_WE->f('Field') . ')';
			}
		}
		if(count($query) > 0){
			$DB_WE->query('ALTER TABLE ' . $DB_WE->escape($tab) . ' ' . implode(', ', $query));
		}
	}

	static function isTabExist($tab){
		global $DB_WE;
		$DB_WE->query("SHOW TABLES LIKE '" . $DB_WE->escape($tab) . "';");
		return ($DB_WE->next_record());
	}

	static function addTable($tab, $cols, $keys = array()){
		global $DB_WE;

		if(!is_array($cols))
			return;
		if(!count($cols))
			return;
		$cols_sql = array();
		$key_sql = array();
		foreach($cols as $name => $type){
			$cols_sql[] = $name . " " . $type;
		}
		foreach($keys as $name => $type){
			$key_sql[] = $name . " " . $type;
		}
		$sql_array = array_merge($cols_sql, $key_sql);

		$DB_WE->query("CREATE TABLE " . $DB_WE->escape($tab) . " (" . implode(",", $sql_array) . ") ENGINE = MYISAM");
	}

	static function addCol($tab, $col, $typ, $pos = ""){
		global $DB_WE;
		if(self::isColExist($tab, $col)){
			return;
		}
		$DB_WE->query("ALTER TABLE " . $DB_WE->escape($tab) . " ADD " . $col . " " . $typ . " " . (($pos != "") ? " " . $pos : "") . ";");
	}

	static function addIndex($tab, $name, $def){
		$GLOBALS['DB_WE']->query('ALTER TABLE ' . $GLOBALS['DB_WE']->escape($tab) . ' ADD INDEX ' . $name . ' (' . $def . ')');
	}

	static function changeColTyp($tab, $col, $newtyp){
		global $DB_WE;
		$DB_WE->query("ALTER TABLE " . $DB_WE->escape($tab) . " CHANGE " . $col . " " . $col . " " . $newtyp . ";");
	}

	static function changeColName($tab, $oldcol, $newcol){
		global $DB_WE;
		$DB_WE->query("ALTER TABLE " . $DB_WE->escape($tab) . " CHANGE `" . $oldcol . "` `" . $newcol . "` ;");
	}

	static function getColTyp($tab, $col){
		global $DB_WE;
		$DB_WE->query("SHOW COLUMNS FROM " . $DB_WE->escape($tab) . " LIKE '" . $col . "';");
		if($DB_WE->next_record())
			return $DB_WE->f("Type"); else
			return "";
	}

	static function delCol($tab, $col){
		global $DB_WE;
		$DB_WE->query("ALTER TABLE " . $DB_WE->escape($tab) . " DROP `" . $col . "` ;");
	}

	static function updateUsers(){
		global $DB_WE;
		$db123 = new DB_WE();
		if(!self::isTabExist(USER_TABLE))
			return;
		self::convertPerms();
		self::addCol(USER_TABLE, "Path", "VARCHAR(255)  DEFAULT ''", "AFTER ID");
		self::addCol(USER_TABLE, "ParentID", "BIGINT(20) DEFAULT '0' NOT NULL", "AFTER ID");

		self::addCol(USER_TABLE, "Icon", "VARCHAR(64)  DEFAULT ''", "AFTER Permissions");
		self::addCol(USER_TABLE, "IsFolder", "TINYINT(1) DEFAULT '0' NOT NULL", "AFTER Permissions");
		self::addCol(USER_TABLE, "Text", "VARCHAR(255)  DEFAULT ''", "AFTER Permissions");

		self::changeColTyp(USER_TABLE, "First", "VARCHAR(255)");
		self::changeColTyp(USER_TABLE, "Second", "VARCHAR(255)");
		self::changeColTyp(USER_TABLE, "username", "VARCHAR(255) NOT NULL");
		self::changeColTyp(USER_TABLE, "workSpace", "VARCHAR(1000)");


		self::fix_path();
		self::fix_text();
		self::fix_icon_small();

		self::addCol(USER_TABLE, "Salutation", "VARCHAR(32) DEFAULT ''", "AFTER ParentID");
		self::addCol(USER_TABLE, "Type", "TINYINT(4) DEFAULT '0' NOT NULL", "AFTER ParentID");
		self::addCol(USER_TABLE, "Address", "VARCHAR(255) DEFAULT ''", "AFTER Second");
		self::addCol(USER_TABLE, "HouseNo", "VARCHAR(32) DEFAULT ''", "AFTER Address");
		self::addCol(USER_TABLE, "PLZ", "VARCHAR(32) DEFAULT ''", "AFTER HouseNo");
		self::addCol(USER_TABLE, "City", "VARCHAR(255) DEFAULT ''", "AFTER PLZ");
		self::addCol(USER_TABLE, "State", "VARCHAR(255) DEFAULT ''", "AFTER City");
		self::addCol(USER_TABLE, "Country", "VARCHAR(255) DEFAULT ''", "AFTER State");
		self::addCol(USER_TABLE, "Tel_preselection", "VARCHAR(32) DEFAULT ''", "AFTER Country");
		self::addCol(USER_TABLE, "Telephone", "VARCHAR(64) DEFAULT ''", "AFTER Tel_preselection");
		self::addCol(USER_TABLE, "Fax_preselection", "VARCHAR(32) DEFAULT ''", "AFTER Telephone");
		self::addCol(USER_TABLE, "Fax", "VARCHAR(64) DEFAULT ''", "AFTER Fax_preselection");
		self::addCol(USER_TABLE, "Handy", "VARCHAR(64) DEFAULT ''", "AFTER Fax");
		self::addCol(USER_TABLE, "Email", "VARCHAR(255) DEFAULT ''", "AFTER Handy");
		self::addCol(USER_TABLE, "Description", "TEXT DEFAULT ''", "AFTER Email");
		self::addCol(USER_TABLE, "workSpaceTmp", "VARCHAR(1000) DEFAULT ''", "AFTER workSpace");
		self::addCol(USER_TABLE, "workSpaceDef", "VARCHAR(1000) DEFAULT ''", "AFTER workSpaceTmp");
		self::addCol(USER_TABLE, "ParentPerms", "TINYINT DEFAULT '0' NOT NULL", "AFTER passwd");
		self::addCol(USER_TABLE, "ParentWs", "TINYINT DEFAULT '0' NOT NULL", "AFTER workSpaceDef");
		self::addCol(USER_TABLE, "ParentWst", "TINYINT DEFAULT '0' NOT NULL", "AFTER ParentWs");
		self::addCol(USER_TABLE, "Alias", "BIGINT DEFAULT '0' NOT NULL");

		self::addCol(USER_TABLE, "workSpaceObj", "VARCHAR(1000) DEFAULT ''", "AFTER workSpace");
		self::addCol(USER_TABLE, "ParentWso", "TINYINT DEFAULT '0' NOT NULL", "AFTER workSpaceDef");
		self::addCol(USER_TABLE, "workSpaceNav", "VARCHAR(1000) DEFAULT ''", "AFTER workSpace");
		self::addCol(USER_TABLE, "ParentWsn", "TINYINT DEFAULT '0' NOT NULL", "AFTER workSpaceDef");
		self::addCol(USER_TABLE, "workSpaceNwl", "VARCHAR(1000) DEFAULT ''", "AFTER workSpace");
		self::addCol(USER_TABLE, "ParentWsnl", "TINYINT DEFAULT '0' NOT NULL", "AFTER workSpaceDef");

		self::addCol(USER_TABLE, "LoginDenied", "TINYINT(1) DEFAULT '0' NOT NULL");
		self::addCol(USER_TABLE, "UseSalt", "TINYINT(1) DEFAULT '0' NOT NULL");

		if(self::isColExist(USER_TABLE, "workSpace")){
			self::changeColTyp(USER_TABLE, "workSpace", "VARCHAR(1000)");
			$DB_WE->query("UPDATE " . USER_TABLE . " SET workSpace='' WHERE workSpace='0';");
		}
		if(self::isColExist(USER_TABLE, "IsFolder")){
			$DB_WE->query("SELECT ID FROM " . USER_TABLE . " WHERE Type=1");
			while($DB_WE->next_record())
				$db123->query("UPDATE " . USER_TABLE . " SET IsFolder=1 WHERE ID=" . intval($DB_WE->f("ID")));
		}
		self::fix_icon();

		return true;
	}

	static function updateCustomers(){
		global $DB_WE;

		if(defined("CUSTOMER_TABLE")){
			if(weModuleInfo::isModuleInstalled("customer")){
				if(!self::isTabExist(CUSTOMER_ADMIN_TABLE)){
					$cols = array(
						"Name" => "VARCHAR(255) NOT NULL",
						"Value" => "TEXT NOT NULL"
					);

					self::addTable(CUSTOMER_ADMIN_TABLE, $cols);

					$DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('FieldAdds','');");
					$DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('SortView','');");
					$DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('Prefs','');");

					include($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_modules/customer/weCustomerSettings.php");
					$settings = new weCustomerSettings();
					$settings->customer = new weCustomer();
					$fields = $settings->customer->getFieldsDbProperties();
					$_keys = array_keys($fields);
					foreach($_keys as $name){
						if(!$settings->customer->isProtected($name) && !$settings->customer->isProperty($name)){
							$settings->FieldAdds[$name]["type"] = "input";
							$settings->FieldAdds[$name]["default"] = "";
						}
					}
					$settings->save();
				}
			}

			self::addCol(CUSTOMER_TABLE, "ParentID", "BINGINT DEFAULT '0' NOT NULL");
			self::addCol(CUSTOMER_TABLE, "Path", "VARCHAR(255) DEFAULT '' NOT NULL");
			self::addCol(CUSTOMER_TABLE, "IsFolder", "TINYINT(1) DEFAULT '0' NOT NULL");
			self::addCol(CUSTOMER_TABLE, "Icon", "VARCHAR(255) DEFAULT 'customer.gif' NOT NULL");
			self::addCol(CUSTOMER_TABLE, "Text", "VARCHAR(255) DEFAULT '' NOT NULL");

			self::addCol(CUSTOMER_TABLE, "Username", "VARCHAR(255) DEFAULT '' NOT NULL");
			self::addCol(CUSTOMER_TABLE, "Password", "VARCHAR(32) DEFAULT '' NOT NULL");
			self::addCol(CUSTOMER_TABLE, "Forename", "VARCHAR(255) DEFAULT '' NOT NULL");
			self::addCol(CUSTOMER_TABLE, "Surname", "VARCHAR(255) DEFAULT '' NOT NULL");


			self::addCol(CUSTOMER_TABLE, "LoginDenied", "TINYINT DEFAULT '0' NOT NULL");
			if(!self::isColExist(CUSTOMER_TABLE, "MemberSince")){
				self::addCol(CUSTOMER_TABLE, "MemberSince", "int(10) NOT NULL default 0");
				$DB_WE->query("UPDATE " . CUSTOMER_ADMIN_TABLE . " SET MemberSince='" . time() . "';");
			} else{
				self::changeColTyp(CUSTOMER_TABLE, "MemberSince", "int(10) NOT NULL default 0");
			}

			if(!self::isColExist(CUSTOMER_TABLE, "LastLogin"))
				self::addCol(CUSTOMER_TABLE, "LastLogin", "int(10) NOT NULL default 0", ' AFTER MemberSince ');
			else
				self::changeColTyp(CUSTOMER_TABLE, "LastLogin", "int(10) NOT NULL default 0");

			if(!self::isColExist(CUSTOMER_TABLE, "LastAccess"))
				self::addCol(CUSTOMER_TABLE, "LastAccess", "int(10) NOT NULL default 0", ' AFTER LastLogin ');
			else
				self::changeColTyp(CUSTOMER_TABLE, "LastAccess", "int(10) NOT NULL default 0");

			self::addCol(CUSTOMER_TABLE, "AutoLoginDenied", "tinyint(1) NOT NULL default '0'", " AFTER LastAccess ");
			self::addCol(CUSTOMER_TABLE, "AutoLogin", "tinyint(1) NOT NULL default '0'", " AFTER AutoLoginDenied ");

			self::addCol(CUSTOMER_TABLE, "ModifyDate", "bigint(20) unsigned NOT NULL default '0'", " AFTER AutoLogin ");
			self::addCol(CUSTOMER_TABLE, "ModifiedBy", "enum('','backend','frontend','external') NOT NULL default''", " AFTER ModifyDate ");

			self::changeColTyp(CUSTOMER_TABLE, "Anrede_Anrede", "enum('','Herr','Frau') NOT NULL");

			self::changeColTyp(CUSTOMER_TABLE, "Newsletter_Ok", "enum('','ja','0','1','2') NOT NULL");
			self::changeColTyp(CUSTOMER_TABLE, "Newsletter_HTMLNewsletter", "enum('','ja','0','1','2') NOT NULL");
		}
		return true;
	}

	static function updateScheduler(){
		if(defined("SCHEDULE_TABLE")){
			self::addCol(SCHEDULE_TABLE, "Schedpro", "longtext DEFAULT ''");
			self::addCol(SCHEDULE_TABLE, "Type", "TINYINT(3) DEFAULT '0' NOT NULL");
			self::addCol(SCHEDULE_TABLE, "Active", "TINYINT(1) DEFAULT '1'");
			self::addCol(SCHEDULE_TABLE, "lockedUntil", "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

			we_schedpro::check_and_convert_to_sched_pro();
		}
		return true;
	}

	static function updateNewsletter(){
		if(defined("NEWSLETTER_LOG_TABLE")){
			self::addCol(NEWSLETTER_LOG_TABLE, "Param", "VARCHAR(255) DEFAULT ''");
		}
		if(defined("NEWSLETTER_BLOCK_TABLE")){
			self::addCol(NEWSLETTER_BLOCK_TABLE, "Pack", "TINYINT(1) DEFAULT '0'");
		}
		return true;
	}

	static function updateShop(){
		if(defined("SHOP_TABLE")){
			self::changeColTyp(SHOP_TABLE, "Price", "VARCHAR(20)");
			self::changeColTyp(SHOP_TABLE, "IntQuantity", " float ");

			self::addCol(SHOP_TABLE, 'DateConfirmation', 'datetime default NULL', ' AFTER DateOrder ');
			self::addCol(SHOP_TABLE, 'DateCustomA', 'datetime default NULL', ' AFTER DateOrder ');
			self::addCol(SHOP_TABLE, 'DateCustomB', 'datetime default NULL', ' AFTER DateCustomA ');
			self::addCol(SHOP_TABLE, 'DateCustomC', 'datetime default NULL', ' AFTER DateCustomB ');

			self::addCol(SHOP_TABLE, 'DateCustomD', 'datetime default NULL', ' AFTER DateShipping ');
			self::addCol(SHOP_TABLE, 'DateCustomE', 'datetime default NULL', ' AFTER DateCustomD ');

			self::addCol(SHOP_TABLE, 'DateCustomF', 'datetime default NULL', ' AFTER DatePayment ');
			self::addCol(SHOP_TABLE, 'DateCustomG', 'datetime default NULL', ' AFTER DateCustomF ');
			self::addCol(SHOP_TABLE, 'DateCancellation', 'datetime default NULL', ' AFTER DateCustomG ');
			self::addCol(SHOP_TABLE, 'DateCustomH', 'datetime default NULL', ' AFTER DateCancellation ');
			self::addCol(SHOP_TABLE, 'DateCustomI', 'datetime default NULL', ' AFTER DateCustomH ');
			self::addCol(SHOP_TABLE, 'DateCustomJ', 'datetime default NULL', ' AFTER DateCustomI ');
			self::addCol(SHOP_TABLE, 'DateFinished', 'datetime default NULL', ' AFTER DateCustomJ ');

			self::addCol(SHOP_TABLE, 'MailOrder', 'datetime default NULL', ' AFTER DateFinished ');
			self::addCol(SHOP_TABLE, 'MailConfirmation', 'datetime default NULL', ' AFTER MailOrder ');
			self::addCol(SHOP_TABLE, 'MailCustomA', 'datetime default NULL', ' AFTER MailConfirmation ');
			self::addCol(SHOP_TABLE, 'MailCustomB', 'datetime default NULL', ' AFTER MailCustomA ');
			self::addCol(SHOP_TABLE, 'MailCustomC', 'datetime default NULL', ' AFTER MailCustomB ');
			self::addCol(SHOP_TABLE, 'MailShipping', 'datetime default NULL', ' AFTER MailCustomC ');
			self::addCol(SHOP_TABLE, 'MailCustomD', 'datetime default NULL', ' AFTER MailShipping ');
			self::addCol(SHOP_TABLE, 'MailCustomE', 'datetime default NULL', ' AFTER MailCustomD ');
			self::addCol(SHOP_TABLE, 'MailPayment', 'datetime default NULL', ' AFTER MailCustomE ');
			self::addCol(SHOP_TABLE, 'MailCustomF', 'datetime default NULL', ' AFTER MailPayment ');
			self::addCol(SHOP_TABLE, 'MailCustomG', 'datetime default NULL', ' AFTER MailCustomF ');
			self::addCol(SHOP_TABLE, 'MailCancellation', 'datetime default NULL', ' AFTER MailCustomG ');
			self::addCol(SHOP_TABLE, 'MailCustomH', 'datetime default NULL', ' AFTER MailCancellation ');
			self::addCol(SHOP_TABLE, 'MailCustomI', 'datetime default NULL', ' AFTER MailCustomH ');
			self::addCol(SHOP_TABLE, 'MailCustomJ', 'datetime default NULL', ' AFTER MailCustomI ');
			self::addCol(SHOP_TABLE, 'MailFinished', 'datetime default NULL', ' AFTER MailCustomJ ');
		}
		return true;
	}

	static function updateObject(){
		if(defined("OBJECT_TABLE")){
			self::addCol(OBJECT_TABLE, 'DefaultUrl', "varchar(255) NOT NULL default ''", ' AFTER  DefaultKeywords ');
			self::addCol(OBJECT_TABLE, 'DefaultUrlfield0', "varchar(255) NOT NULL default ''", ' AFTER  DefaultUrl ');
			self::addCol(OBJECT_TABLE, 'DefaultUrlfield1', "varchar(255) NOT NULL default ''", ' AFTER  DefaultUrlfield0 ');
			self::addCol(OBJECT_TABLE, 'DefaultUrlfield2', "varchar(255) NOT NULL default ''", ' AFTER  DefaultUrlfield1 ');
			self::addCol(OBJECT_TABLE, 'DefaultUrlfield3', "varchar(255) NOT NULL default ''", ' AFTER  DefaultUrlfield2 ');
			self::addCol(OBJECT_TABLE, 'DefaultTriggerID', "bigint(20) NOT NULL default 0", ' AFTER  DefaultUrlfield3 ');
		}
	}

	static function updateObjectFiles(){
		if(defined("OBJECT_FILES_TABLE")){
			self::addCol(OBJECT_FILES_TABLE, 'Url', "varchar(255) NOT NULL default ''", ' AFTER Path ');
			self::addCol(OBJECT_FILES_TABLE, 'TriggerID', "bigint NOT NULL default '0'", ' AFTER Url ');
		}
	}

	static function updateObjectFilesX(){
		if(defined('OBJECT_X_TABLE')){
			$_db = new DB_WE();

			$_table = OBJECT_FILES_TABLE;
			if(self::isColExist($_table, 'Url')){
				self::changeColTyp($_table, 'Url', 'VARCHAR(255) NOT NULL');
			} else{
				self::addCol($_table, 'Url', 'VARCHAR(255) NOT NULL', ' AFTER Path ');
			}
			if(self::isColExist($_table, 'TriggerID')){
				self::changeColTyp($_table, 'TriggerID', "bigint NOT NULL default '0'");
			} else{
				self::addCol($_table, 'TriggerID', "bigint NOT NULL default '0'", ' AFTER Url ');
			}
			if(self::isColExist($_table, 'IsSearchable')){
				self::changeColTyp($_table, 'IsSearchable', 'TINYINT(1) DEFAULT 1');
			} else{
				self::addCol($_table, 'IsSearchable', 'TINYINT(1) DEFAULT 1', ' AFTER Published ');
			}
			if(self::isColExist($_table, 'Charset')){
				self::changeColTyp($_table, 'Charset', 'VARCHAR(64) DEFAULT NULL');
			} else{
				self::addCol($_table, 'Charset', 'VARCHAR(64) DEFAULT NULL', ' AFTER IsSearchable ');
			}
			if(self::isColExist($_table, 'Language')){
				self::changeColTyp($_table, 'Language', 'VARCHAR(5) DEFAULT NULL');
			} else{
				self::addCol($_table, 'Language', 'VARCHAR(5) DEFAULT NULL', ' AFTER Charset ');
			}
			if(self::isColExist($_table, 'WebUserID')){
				self::changeColTyp($_table, 'WebUserID', 'BIGINT(20) NOT NULL');
			} else{
				self::addCol($_table, 'WebUserID', 'BIGINT(20) NOT NULL', ' AFTER Language ');
			}

			$_maxid = f('SELECT MAX(ID) as MaxTID FROM ' . OBJECT_TABLE . ';', 'MaxTID', $_db) + 1;
			for($i = 1; $i < $_maxid; $i++){
				$_table = OBJECT_X_TABLE . $i;
				if(self::isTabExist($_table)){
					if(self::isColExist($_table, 'OF_Url')){
						self::changeColTyp($_table, 'OF_Url', 'VARCHAR(255) NOT NULL');
					} else{
						self::addCol($_table, 'OF_Url', 'VARCHAR(255) NOT NULL', '  AFTER OF_Path  ');
					}
					if(self::isColExist($_table, 'OF_TriggerID')){
						self::changeColTyp($_table, 'OF_TriggerID', 'BIGINT(20) NOT NULL DEFAULT 0');
					} else{
						self::addCol($_table, 'OF_TriggerID', 'BIGINT(20) NOT NULL DEFAULT 0', '  AFTER OF_Url  ');
					}
					if(self::isColExist($_table, 'OF_IsSearchable')){
						self::changeColTyp($_table, 'OF_IsSearchable', 'TINYINT(1) DEFAULT 1');
					} else{
						self::addCol($_table, 'OF_IsSearchable', 'TINYINT(1) DEFAULT 1', ' AFTER OF_Published ');
					}
					if(self::isColExist($_table, 'OF_Charset')){
						self::changeColTyp($_table, 'OF_Charset', 'VARCHAR(64) NOT NULL');
					} else{
						self::addCol($_table, 'OF_Charset', 'VARCHAR(64) NOT NULL', ' AFTER OF_IsSearchable ');
					}
					if(self::isColExist($_table, 'OF_WebUserID')){
						self::changeColTyp($_table, 'OF_WebUserID', 'BIGINT(20) NOT NULL');
					} else{
						self::addCol($_table, 'OF_WebUserID', 'BIGINT(20) NOT NULL', ' AFTER OF_Charset ');
					}
					if(self::isColExist($_table, 'OF_Language')){
						self::changeColTyp($_table, 'OF_Language', 'VARCHAR(5) DEFAULT NULL');
					} else{
						self::addCol($_table, 'OF_Language', 'VARCHAR(5) DEFAULT NULL', ' AFTER OF_WebUserID ');
					}
					//add indices to all objects
					self::updateUnindexedCols($_table, 'object_%');

					if(!self::hasIndex($_table, 'OF_WebUserID')){
						self::addIndex($_table, 'OF_WebUserID', 'OF_WebUserID');
					}
					if(!self::hasIndex($_table, 'published')){
						self::addIndex($_table, 'published', 'OF_ID,OF_Published,OF_IsSearchable');
					}
					if(!self::hasIndex($_table, 'OF_IsSearchable')){
						self::addIndex($_table, 'OF_IsSearchable', 'OF_IsSearchable');
					}
				}
			}
		}
		return true;
	}

	static function updateNavigation(){
		self::addCol(NAVIGATION_TABLE, '`Charset`', 'varchar(255) NOT NULL default ""');
		self::addCol(NAVIGATION_TABLE, '`Attributes`', 'text NOT NULL');
		self::addCol(NAVIGATION_TABLE, '`FolderSelection`', 'enum("docLink","objLink","urlLink") NOT NULL default ""');
		self::addCol(NAVIGATION_TABLE, '`FolderWsID`', 'bigint(20) NOT NULL default "0"');
		self::addCol(NAVIGATION_TABLE, '`FolderParameter`', 'varchar(255) NOT NULL default ""');
		self::addCol(NAVIGATION_TABLE, '`FolderUrl`', 'varchar(255) NOT NULL default ""');
		self::addCol(NAVIGATION_TABLE, '`LimitAccess`', 'tinyint(4) NOT NULL default 0');
		self::addCol(NAVIGATION_TABLE, '`AllCustomers`', 'tinyint(4) NOT NULL default 0');
		self::addCol(NAVIGATION_TABLE, '`ApplyFilter`', 'tinyint(4) NOT NULL default 0');
		self::addCol(NAVIGATION_TABLE, '`Customers`', 'text NOT NULL');
		self::addCol(NAVIGATION_TABLE, '`CustomerFilter`', 'text NOT NULL');
		self::addCol(NAVIGATION_TABLE, 'Published', 'int(11) NOT NULL DEFAULT "1"', ' AFTER Path ');
		self::addCol(NAVIGATION_TABLE, 'Display', 'varchar(255) NOT NULL ', ' AFTER Text ');
		self::addCol(NAVIGATION_TABLE, 'CurrentOnUrlPar', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER Text ');
		self::addCol(NAVIGATION_TABLE, 'CurrentOnAnker', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER CurrentOnUrlPar ');
	}

	static function updateVoting(){
		if(defined('VOTING_TABLE')){
			self::addCol(VOTING_TABLE, 'QASetAdditions', 'text', ' AFTER QASet ');
			self::addCol(VOTING_TABLE, 'IsRequired', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER QASetAdditions ');
			self::addCol(VOTING_TABLE, 'AllowFreeText', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER IsRequired ');
			self::addCol(VOTING_TABLE, 'AllowImages', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER AllowFreeText ');
			self::addCol(VOTING_TABLE, 'AllowMedia', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER AllowImages ');
			self::addCol(VOTING_TABLE, 'AllowSuccessor', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER AllowMedia ');
			self::addCol(VOTING_TABLE, 'AllowSuccessors', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER AllowSuccessor ');
			self::addCol(VOTING_TABLE, 'Successor', 'bigint(20) unsigned NOT NULL DEFAULT 0', ' AFTER AllowSuccessors ');
			self::addCol(VOTING_TABLE, 'FallbackUserID', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER UserAgent ');
			self::addCol(VOTING_LOG_TABLE, 'votingsession', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER id ');
			self::addCol(VOTING_LOG_TABLE, 'userid', 'bigint(20) NOT NULL DEFAULT 0', ' AFTER agent ');
			self::addCol(VOTING_LOG_TABLE, 'answer', 'varchar(255) NOT NULL', ' AFTER status ');
			self::addCol(VOTING_LOG_TABLE, 'answertext', 'text NOT NULL', ' AFTER answer ');
			self::addCol(VOTING_LOG_TABLE, 'successor', 'bigint(20) unsigned NOT NULL DEFAULT 0', ' AFTER answertext ');
			self::addCol(VOTING_LOG_TABLE, 'additionalfields', 'text NOT NULL', ' AFTER successor ');
		}
	}

	static function updateVersions(){
		if(defined("VERSIONS_TABLE")){
			self::changeColTyp(VERSIONS_TABLE, 'DocType', 'varchar(64) NOT NULL');
			self::addCol(VERSIONS_TABLE, 'MasterTemplateID', "bigint(20) NOT NULL default '0'", ' AFTER ExtraTemplates ');
			self::changeColTyp(VERSIONS_TABLE, "documentElements", "blob");
			self::changeColTyp(VERSIONS_TABLE, "documentScheduler", "blob");
			self::changeColTyp(VERSIONS_TABLE, "documentCustomFilter", "blob");
		}
	}

	static function updateWorkflow(){
		if(defined('WORKFLOW_STEP_TABLE')){
			if(self::isColExist(WORKFLOW_STEP_TABLE, 'Worktime'))
				self::changeColTyp(WORKFLOW_STEP_TABLE, 'Worktime', 'float NOT NULL default 0');
		}
		if(defined('WORKFLOW_TABLE')){
			if(!self::isColExist(WORKFLOW_TABLE, 'DocType'))
				self::changeColTyp(WORKFLOW_TABLE, 'DocType', "varchar(255) NOT NULL default '0'");
			self::addCol(WORKFLOW_TABLE, 'ObjectFileFolders', "varchar(255) NOT NULL default ''", ' AFTER Objects ');
			self::addCol(WORKFLOW_TABLE, 'EmailPath', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER Status ');
			self::addCol(WORKFLOW_TABLE, 'LastStepAutoPublish', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER EmailPath ');
		}
	}

	static function updateLock(){
		if(!self::isTabExist(LOCK_TABLE)){
			$cols = array(
				"ID" => "bigint(20) NOT NULL default '0'",
				"sessionID" => "varchar(64) NOT NULL default ''",
				"lockTime" => "datetime NOT NULL",
				"tbl" => "varchar(32) NOT NULL default ''"
			);
			$keys = array(
				"PRIMARY KEY" => "(ID,tbl)",
				"KEY UserID" => "(UserID,sessionID)",
				"KEY lockTime" => "(lockTime)"
			);
			self::addTable(LOCK_TABLE, $cols, $keys);
		}
		self::addCol(LOCK_TABLE, 'sessionID', "varchar(64) NOT NULL default ''", ' AFTER UserID ');
		if(self::isColExist(LOCK_TABLE, 'lock') && !self::isColExist(LOCK_TABLE, 'lockTime'))
			self::changeColName(LOCK_TABLE, 'lock', 'lockTime');
		if(self::isColExist(LOCK_TABLE, 'lock'))
			self::delCol(LOCK_TABLE, 'lock');
		self::addCol(LOCK_TABLE, 'lockTime', "datetime NOT NULL", ' AFTER sessionID ');
		return true;
	}

	static function updateTableKeys(){
		if(isset($_SESSION['weBackupVars']['tablekeys']) && is_array($_SESSION['weBackupVars']['tablekeys'])){
			$myarray = $_SESSION['weBackupVars']['tablekeys'];
			foreach($myarray as $table => $v){
				if(is_array($v)){
					foreach($v as $tabkey){
						if(!weDBUtil::isKeyExist($table, $tabkey)){
							if(($key = weDBUtil::isKeyExistAtAll($table, $tabkey))){
								weDBUtil::delKey($table, $key);
							}
							weDBUtil::addKey($table, $tabkey);
						}
					}
				}
			}
		}
		return true;
	}

	private static function updateLangLink(){
		if(!self::isTabExist(LANGLINK_TABLE)){
			$cols = array(
				"ID" => "int(11) NOT NULL AUTO_INCREMENT",
				"DID" => "DID int(11) NOT NULL default '0'",
				"DLocale" => "varchar(5) NOT NULL default ''",
				"IsFolder" => "tinyint(1) NOT NULL default '0'",
				"IsObject" => "tinyint(1) NOT NULL default '0'",
				"LDID" => "int(11) NOT NULL default '0'",
				"Locale" => " varchar(5) NOT NULL default ''",
				"DocumentTable" => "enum('tblFile','tblObjectFile','tblDocTypes') NOT NUL"
			);
			$keys = array(
				"PRIMARY KEY" => "(ID)",
				"UNIQUE KEY DID" => "(DID,DocumentTable,DLocale,Locale,IsFolder,IsObject)",
				"UNIQUE KEY DLocale" => "(DLocale,LDID,Locale,DocumentTable,IsFolder,IsObject)"
			);
			self::addTable(LANGLINK_TABLE, $cols, $keys);
		}
		self::addCol(LANGLINK_TABLE, 'DLocale', "varchar(5) NOT NULL default ''", ' AFTER DID ');

		if(!weDBUtil::isUniqueKeyExist(LANGLINK_TABLE, 'DLocale')){
			//no unique def. found
			$db=$GLOBALS['DB_WE'];
			if($db->query('CREATE TEMPORARY TABLE tmpLangLink LIKE '.LANGLINK_TABLE)){

				// copy links from documents or document-folders to tmpLangLink only if DID and DLocale are consistent with Language in tblFile
				$db->query("INSERT INTO tmpLangLink SELECT ".LANGLINK_TABLE.".* FROM ".LANGLINK_TABLE.", ".FILE_TABLE." WHERE ".LANGLINK_TABLE.".DID = ".FILE_TABLE.".ID AND ".LANGLINK_TABLE.".DLocale = ".FILE_TABLE.".Language AND ".LANGLINK_TABLE.".IsObject = 0 AND ".LANGLINK_TABLE.".DocumentTable = 'tblFile'");

				// copy links from objects or object-folders to tmpLangLink only if DID and DLocale are consistent with Language in tblObjectFiles
				$db->query("INSERT INTO tmpLangLink SELECT ".LANGLINK_TABLE.".* FROM ".LANGLINK_TABLE.", ".OBJECT_FILES_TABLE." WHERE ".LANGLINK_TABLE.".DID = ".OBJECT_FILES_TABLE.".ID AND ".LANGLINK_TABLE.".DLocale = ".OBJECT_FILES_TABLE.".Language AND ".LANGLINK_TABLE.".IsObject = 1");

				// copy links from doctypes to tmpLangLink only if DID and DLocale are consistent with Language in tblFile
				$db->query("INSERT INTO tmpLangLink SELECT ".LANGLINK_TABLE.".* FROM ".LANGLINK_TABLE.", ".DOC_TYPES_TABLE." WHERE ".LANGLINK_TABLE.".DID = ".DOC_TYPES_TABLE.".ID AND ".LANGLINK_TABLE.".DLocale = ".DOC_TYPES_TABLE.".Language AND ".LANGLINK_TABLE.".DocumentTable = 'tblDocTypes'");

				$db->query('TRUNCATE '.LANGLINK_TABLE);
				if(!weDBUtil::isUniqueKeyExist(LANGLINK_TABLE,'DID')){
					weDBUtil::addKey(LANGLINK_TABLE,'UNIQUE KEY DID (DID,DocumentTable,DLocale,Locale,IsFolder,IsObject)');
				}
				if(!weDBUtil::isUniqueKeyExist(LANGLINK_TABLE,'DLocale')){
					weDBUtil::addKey(LANGLINK_TABLE,'UNIQUE KEY DLocale (DLocale,LDID,Locale,DocumentTable,IsFolder,IsObject)');
				}

				// copy links from documents, document-folders and object-folders (to documents) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query("INSERT IGNORE INTO ".LANGLINK_TABLE." SELECT tmpLangLink.* FROM tmpLangLink, ".FILE_TABLE." WHERE tmpLangLink.LDID = ".FILE_TABLE.".ID AND tmpLangLink.Locale = ".FILE_TABLE.".Language AND tmpLangLink.IsObject = 0 AND tmpLangLink.DocumentTable = 'tblFile' ORDER BY tmpLangLink.ID DESC");

				// copy links from objects (to objects) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query("INSERT IGNORE INTO ".LANGLINK_TABLE." SELECT tmpLangLink.* FROM tmpLangLink, ".OBJECT_FILES_TABLE." WHERE tmpLangLink.LDID = ".OBJECT_FILES_TABLE.".ID AND tmpLangLink.Locale = ".OBJECT_FILES_TABLE.".Language AND tmpLangLink.IsObject = 1 ORDER BY tmpLangLink.ID DESC");

				// copy links from doctypes (to doctypes) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query("INSERT IGNORE INTO ".LANGLINK_TABLE." SELECT tmpLangLink.* FROM tmpLangLink, ".DOC_TYPES_TABLE." WHERE tmpLangLink.LDID = ".DOC_TYPES_TABLE.".ID AND tmpLangLink.Locale = ".DOC_TYPES_TABLE.".Language AND tmpLangLink.DocumentTable = 'tblDocTypes' ORDER BY tmpLangLink.ID DESC");

			}else{
				t_e('no rights to create temp-table');
			}
		}
	}

	static function convertTemporaryDoc(){
		if(self::isColExist(TEMPORARY_DOC_TABLE, 'ID')){
			$GLOBALS['DB_WE']->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE Active=0');
			$GLOBALS['DB_WE']->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocTable="tblFile" WHERE DocTable  LIKE "%tblFile"');
			$GLOBALS['DB_WE']->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocTable="tblObjectFiles" WHERE DocTable LIKE "%tblObjectFiles"');
			self::delCol(TEMPORARY_DOC_TABLE, 'ID');
			$GLOBALS['DB_WE']->query('ALTER IGNORE TABLE ' . TEMPORARY_DOC_TABLE . '  DROP PRIMARY KEY ');
			$GLOBALS['DB_WE']->query('ALTER IGNORE TABLE ' . TEMPORARY_DOC_TABLE . ' ADD PRIMARY KEY ( `DocumentID` , `DocTable` , `Active` )');
		}
	}

	private static function getAllIDFromQuery($sql){
		$db = $GLOBALS['DB_WE'];
		$db->query($sql);
		$ret = array();
		while($db->next_record()) {
			$ret[] = $db->f(0);
		}
		return $ret;
	}

	static function fixInconsistentTables(){
		$db = $GLOBALS['DB_WE'];
		$del = array();
		$del = array_merge($del, self::getAllIDFromQuery('SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND DID NOT IN(SELECT ID FROM ' . FILE_TABLE . ')'));
		$del = array_merge($del, self::getAllIDFromQuery('SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblTemplates" AND DID NOT IN(SELECT ID FROM ' . TEMPLATES_TABLE . ')'));

		if(count($del)){
			$db->query('DELETE FROM ' . LINK_TABLE . ' WHERE CID IN (' . implode(',', $del) . ')');
		}

		$del = array_merge($del, self::getAllIDFromQuery('SELECT ID FROM ' . CONTENT_TABLE . ' WHERE ID NOT IN (SELECT CID FROM ' . LINK_TABLE . ')'));
		if(count($del)){
			$db->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID IN (' . implode(',', $del) . ')');
		}
	}

	function doUpdate(){
		self::updateTables();
		self::updateUsers();
		self::updateShop();
		self::updateNewsletter();
		self::updateObjectFilesX();
		self::updateNavigation();
		self::updateScheduler();
		self::updateVoting();
		self::updateVersions();
		self::updateWorkflow();
		self::updateLock();
		self::convertTemporaryDoc();
		self::updateTableKeys();
		self::updateLangLink();
		self::fixInconsistentTables();
	}

}
