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
//FIXME: remove this file almost complete; at least all DB queries. Replace by Update-Script calls on DB-Files.
class we_updater{

	static function replayUpdateDB(){
		if(file_exists(WE_INCLUDES_PATH . 'dbQueries.inc.php')){
			$data = @unserialize(@gzinflate(weFile::load(WE_INCLUDES_PATH . 'dbQueries.inc.php')));
			$lf = new liveUpdateFunctions();
			//disable sql-error handling!
			$GLOBALS['we']['errorhandler']['sql'] = false;
			foreach($data as $content){
				$queries = explode("/* query separator */", $content);
				foreach($queries as $query){
					$success &= $lf->executeUpdateQuery($query, $GLOBALS['DB_WE']);
				}
			}
			$GLOBALS['we']['errorhandler']['sql'] = false;
		}
	}

	static function updateTables(){
		global $DB_WE;
		$db2 = new DB_WE();
		$tables = $db2->table_names();
		$hasOwnertable = false;
		foreach($tables as $t){
			// old Version of small User Module
			if($t["table_name"] == TBL_PREFIX . 'tblOwner'){
				$hasOwnertable = true;
				break;
			}
		}
		if(!$GLOBALS['DB_WE']->isColExist(FILE_TABLE, "CreatorID"))
			$GLOBALS['DB_WE']->addCol(FILE_TABLE, "CreatorID", "BIGINT DEFAULT '0' NOT NULL");
		if(!$GLOBALS['DB_WE']->isColExist(FILE_TABLE, "ModifierID"))
			$GLOBALS['DB_WE']->addCol(FILE_TABLE, "ModifierID", "BIGINT DEFAULT '0' NOT NULL");
		if(!$GLOBALS['DB_WE']->isColExist(FILE_TABLE, "WebUserID"))
			$GLOBALS['DB_WE']->addCol(FILE_TABLE, "WebUserID", "BIGINT DEFAULT '0' NOT NULL");
		if($hasOwnertable){
			$DB_WE->query('SELECT * FROM ' . TBL_PREFIX . 'tblOwner');
			while($DB_WE->next_record()) {
				$table = $DB_WE->f("DocumentTable");
				if($table == TEMPLATES_TABLE || $table == FILE_TABLE){
					$id = $DB_WE->f("fileID");
					if($table && $id){
						$Owners = ($DB_WE->f("OwnerID") && ($DB_WE->f("OwnerID") != $DB_WE->f("CreatorID"))) ? ("," . $DB_WE->f("OwnerID") . ",") : "";
						$CreatorID = $DB_WE->f("CreatorID") ? $DB_WE->f("CreatorID") : $_SESSION["user"]["ID"];
						$ModifierID = $DB_WE->f("ModifierID") ? $DB_WE->f("ModifierID") : $_SESSION["user"]["ID"];
						$db2->query("UPDATE " . $db2->escape($table) . " SET CreatorID=" . intval($CreatorID) . " , ModifierID=" . intval($ModifierID) . " , Owners='" . $db2->escape($Owners) . "' WHERE ID=" . intval($id));
						$db2->query('DELETE FROM ' . TBL_PREFIX . ' WHERE fileID=' . intval($id));
						@set_time_limit(30);
					}
				}
			}
			$DB_WE->query('DROP TABLE ' . TBL_PREFIX . 'tblOwner');
		}

		$GLOBALS['DB_WE']->addCol(INDEX_TABLE, 'Language', "varchar(5) default NULL");
		$GLOBALS['DB_WE']->changeColType(INDEX_TABLE, "Workspace", " varchar(1000) NOT NULL default '' ");


		$GLOBALS['DB_WE']->addCol(FILE_TABLE, "Owners", "VARCHAR(255)  DEFAULT ''");
		$GLOBALS['DB_WE']->addCol(FILE_TABLE, "RestrictOwners", "TINYINT(1)  DEFAULT ''");
		$GLOBALS['DB_WE']->addCol(FILE_TABLE, "OwnersReadOnly", "TEXT DEFAULT ''");

		if($GLOBALS['DB_WE']->isColExist(FILE_TABLE, "IsFolder"))
			$GLOBALS['DB_WE']->changeColType(FILE_TABLE, "IsFolder", "tinyint(1) NOT NULL default '0'");
		if($GLOBALS['DB_WE']->isColExist(FILE_TABLE, "IsDynamic"))
			$GLOBALS['DB_WE']->changeColType(FILE_TABLE, "IsDynamic", "tinyint(1) NOT NULL default '0'");
		if($GLOBALS['DB_WE']->isColExist(FILE_TABLE, "DocType"))
			$GLOBALS['DB_WE']->changeColType(FILE_TABLE, "IsFolder", "varchar(64) NOT NULL default ''");

		$GLOBALS['DB_WE']->addCol(CATEGORY_TABLE, "IsFolder", "TINYINT(1) DEFAULT 0");
		$GLOBALS['DB_WE']->addCol(CATEGORY_TABLE, "ParentID", "BIGINT(20) DEFAULT 0");
		$GLOBALS['DB_WE']->addCol(CATEGORY_TABLE, "Text", "VARCHAR(64) DEFAULT ''");
		$GLOBALS['DB_WE']->addCol(CATEGORY_TABLE, "Path", "VARCHAR(255)  DEFAULT ''");
		$GLOBALS['DB_WE']->addCol(CATEGORY_TABLE, "Icon", "VARCHAR(64) DEFAULT 'cat.gif'");

		$DB_WE->query('UPDATE ' . CATEGORY_TABLE . ' SET Text=Category WHERE Text=""');
		$DB_WE->query('UPDATE ' . CATEGORY_TABLE . ' SET Path=CONCAT("/",Category) WHERE Path=""');

		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "seem_start_file", "INT");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "seem_start_type", "VARCHAR(10) DEFAULT ''");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "seem_start_weapp", "VARCHAR(255) DEFAULT ''", ' AFTER seem_start_type ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "phpOnOff", "TINYINT(1) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorSizeOpt", "TINYINT( 1 ) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorWidth", "INT( 11 ) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorHeight", "INT( 11 ) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "debug_normal", "TINYINT( 1 ) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "debug_seem", "TINYINT( 1 ) DEFAULT '0' NOT NULL");

		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "xhtml_show_wrong", "TINYINT(1) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "xhtml_show_wrong_text", "TINYINT(2) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "xhtml_show_wrong_js", "TINYINT(2) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "xhtml_show_wrong_error_log", "TINYINT(2) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "default_tree_count", "smallint unsigned DEFAULT '0' NOT NULL");

		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorMode", "  varchar(64) NOT NULL DEFAULT 'textarea'", ' AFTER  specify_jeditor_colors ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorLinenumbers", " tinyint(1) NOT NULL default '1'", ' AFTER editorMode ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorCodecompletion", " tinyint(1) NOT NULL default '0'", ' AFTER editorLinenumbers ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorTooltips", " tinyint(1) NOT NULL default '1'", ' AFTER editorCodecompletion ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorTooltipFont", " tinyint(1) NOT NULL default '0'", ' AFTER editorTooltips ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorTooltipFontname", "  varchar(255) NOT NULL default 'none'", ' AFTER editorTooltipFont ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorTooltipFontsize", " int(2) NOT NULL default '-1'", ' AFTER editorTooltipFontname ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "editorDocuintegration", " tinyint(1) NOT NULL default '1'", ' AFTER editorTooltipFontsize ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "BackendCharset", " varchar(22) NOT NULL default ''", ' AFTER Language ');
		$GLOBALS['DB_WE']->addCol(PREFS_TABLE, "juploadPath", " text ", ' AFTER use_jupload ');

		if($GLOBALS['DB_WE']->isColExist(DOC_TYPES_TABLE, "DocType"))
			$GLOBALS['DB_WE']->changeColType(DOC_TYPES_TABLE, "DocType", " varchar(64) NOT NULL default '' ");

		$GLOBALS['DB_WE']->changeColType(ERROR_LOG_TABLE, "ID", "int(11) NOT NULL auto_increment");
		$GLOBALS['DB_WE']->addCol(ERROR_LOG_TABLE, "Type", " enum('Error','Warning','Parse error','Notice','Core error','Core warning','Compile error','Compile warning','User error','User warning','User notice','Deprecated notice','User deprecated notice','unknown Error') NOT NULL ", ' AFTER ID ');
		$GLOBALS['DB_WE']->addCol(ERROR_LOG_TABLE, "Function", " varchar(255) NOT NULL default ''", ' AFTER Type ');
		$GLOBALS['DB_WE']->addCol(ERROR_LOG_TABLE, "File", " varchar(255) NOT NULL default ''", ' AFTER Function ');
		$GLOBALS['DB_WE']->addCol(ERROR_LOG_TABLE, "Line", " int(11) NOT NULL", ' AFTER File ');
		$GLOBALS['DB_WE']->addCol(ERROR_LOG_TABLE, "Backtrace", "text NOT NULL", ' AFTER Text ');
		$GLOBALS['DB_WE']->changeColType(ERROR_LOG_TABLE, "Date", "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

		if($GLOBALS['DB_WE']->isColExist(FAILED_LOGINS_TABLE, "ID"))
			$GLOBALS['DB_WE']->changeColType(FAILED_LOGINS_TABLE, "ID", "bigint(20) NOT NULL AUTO_INCREMENT");
		if($GLOBALS['DB_WE']->isColExist(FAILED_LOGINS_TABLE, "IP"))
			$GLOBALS['DB_WE']->changeColType(FAILED_LOGINS_TABLE, "IP", " varchar(40) NOT NULL");
		if($GLOBALS['DB_WE']->isColExist(FAILED_LOGINS_TABLE, "LoginDate"))
			$GLOBALS['DB_WE']->changeColType(FAILED_LOGINS_TABLE, "LoginDate", " timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

		if($GLOBALS['DB_WE']->isColExist(LINK_TABLE, "DocumentTable"))
			$GLOBALS['DB_WE']->changeColType(LINK_TABLE, "DocumentTable", " enum('tblFile','tblTemplates') NOT NULL ");

		if(defined('GLOSSARY_TABLE')){
			$GLOBALS['DB_WE']->changeColType(GLOSSARY_TABLE, "`Type`", " enum('abbreviation','acronym','foreignword','link','textreplacement') NOT NULL default 'abbreviation'");
			$GLOBALS['DB_WE']->changeColType(GLOSSARY_TABLE, "`Icon`", " enum('folder.gif','prog.gif') NOT NULL ");
		}
		$GLOBALS['DB_WE']->addCol(THUMBNAILS_TABLE, "Fitinside", " smallint(5) unsigned NOT NULL default '0' ", ' AFTER Interlace ');
		$GLOBALS['DB_WE']->changeColType(HISTORY_TABLE, "ContentType", "enum('image/*','text/html','text/webedition','text/weTmpl','text/js','text/css','text/htaccess','text/plain','folder','class_folder','application/x-shockwave-flash','video/quicktime','application/*','text/xml','object','objectFile') NOT NULL");
	}

	static function convertPerms(){
		global $DB_WE;
		if(!($GLOBALS['DB_WE']->isColExist(USER_TABLE, "Permissions") && $GLOBALS['DB_WE']->getColTyp(USER_TABLE, "Permissions") != "text")){
			return;
		}
		$GLOBALS['DB_WE']->changeColType(USER_TABLE, 'Permissions', 'TEXT');
		$db_tmp = new DB_WE();
		$DB_WE->query('SELECT ID,username,Permissions FROM ' . USER_TABLE);
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
		$db->query('SELECT ID,username,ParentID,Path FROM ' . USER_TABLE);
		while($db->next_record()) {
			@set_time_limit(30);
			$id = $db->f('ID');
			$pid = $db->f('ParentID');
			$path = '/' . $db->f("username");
			while($pid > 0) {
				$db2->query('SELECT username,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($pid));
				if($db2->next_record()){
					$path = '/' . $db2->f("username") . $path;
					$pid = $db2->f("ParentID");
				} else{
					$pid = 0;
				}
			}
			if($db->f('Path') != $path){
				$db2->query('UPDATE ' . USER_TABLE . " SET Path='" . $db2->escape($path) . "' WHERE ID=" . intval($id));
			}
		}
	}

	static function fix_icon(){
		$db = new DB_WE();
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='user_alias.gif' WHERE Type=2");
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='usergroup.gif' WHERE Type=1");
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='user.gif' WHERE Type NOT IN(1,2)");
	}

	static function fix_icon_small(){
		$db = new DB_WE();
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='usergroup.gif' WHERE IsFolder=1");
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='user.gif' WHERE IsFolder=0");
	}

	static function fix_text(){
		$db = new DB_WE();
		$db->query('UPDATE ' . USER_TABLE . ' SET Text=username');
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

	static function updateUsers(){
		global $DB_WE;
		$db123 = new DB_WE();
		if(!$GLOBALS['DB_WE']->isTabExist(USER_TABLE)){
			return;
		}
		self::convertPerms();
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Path", "VARCHAR(255)  DEFAULT ''", "AFTER ID");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "ParentID", "BIGINT(20) DEFAULT '0' NOT NULL", "AFTER ID");

		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Icon", "VARCHAR(64)  DEFAULT ''", "AFTER Permissions");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "IsFolder", "TINYINT(1) DEFAULT '0' NOT NULL", "AFTER Permissions");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Text", "VARCHAR(255)  DEFAULT ''", "AFTER Permissions");

		$GLOBALS['DB_WE']->changeColType(USER_TABLE, "First", "VARCHAR(255)");
		$GLOBALS['DB_WE']->changeColType(USER_TABLE, "Second", "VARCHAR(255)");
		$GLOBALS['DB_WE']->changeColType(USER_TABLE, "username", "VARCHAR(255) NOT NULL");
		$GLOBALS['DB_WE']->changeColType(USER_TABLE, "workSpace", "VARCHAR(1000)");


		self::fix_path();
		self::fix_text();
		self::fix_icon_small();

		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Salutation", "VARCHAR(32) DEFAULT ''", "AFTER ParentID");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Type", "TINYINT(4) DEFAULT '0' NOT NULL", "AFTER ParentID");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Address", "VARCHAR(255) DEFAULT ''", "AFTER Second");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "HouseNo", "VARCHAR(32) DEFAULT ''", "AFTER Address");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "PLZ", "VARCHAR(32) DEFAULT ''", "AFTER HouseNo");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "City", "VARCHAR(255) DEFAULT ''", "AFTER PLZ");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "State", "VARCHAR(255) DEFAULT ''", "AFTER City");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Country", "VARCHAR(255) DEFAULT ''", "AFTER State");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Tel_preselection", "VARCHAR(32) DEFAULT ''", "AFTER Country");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Telephone", "VARCHAR(64) DEFAULT ''", "AFTER Tel_preselection");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Fax_preselection", "VARCHAR(32) DEFAULT ''", "AFTER Telephone");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Fax", "VARCHAR(64) DEFAULT ''", "AFTER Fax_preselection");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Handy", "VARCHAR(64) DEFAULT ''", "AFTER Fax");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Email", "VARCHAR(255) DEFAULT ''", "AFTER Handy");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Description", "TEXT DEFAULT ''", "AFTER Email");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "workSpaceTmp", "VARCHAR(1000) DEFAULT ''", "AFTER workSpace");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "workSpaceDef", "VARCHAR(1000) DEFAULT ''", "AFTER workSpaceTmp");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "ParentPerms", "TINYINT DEFAULT '0' NOT NULL", "AFTER passwd");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "ParentWs", "TINYINT DEFAULT '0' NOT NULL", "AFTER workSpaceDef");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "ParentWst", "TINYINT DEFAULT '0' NOT NULL", "AFTER ParentWs");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "Alias", "BIGINT DEFAULT '0' NOT NULL");

		$GLOBALS['DB_WE']->addCol(USER_TABLE, "workSpaceObj", "VARCHAR(1000) DEFAULT ''", "AFTER workSpace");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "ParentWso", "TINYINT DEFAULT '0' NOT NULL", "AFTER workSpaceDef");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "workSpaceNav", "VARCHAR(1000) DEFAULT ''", "AFTER workSpace");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "ParentWsn", "TINYINT DEFAULT '0' NOT NULL", "AFTER workSpaceDef");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "workSpaceNwl", "VARCHAR(1000) DEFAULT ''", "AFTER workSpace");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "ParentWsnl", "TINYINT DEFAULT '0' NOT NULL", "AFTER workSpaceDef");

		$GLOBALS['DB_WE']->addCol(USER_TABLE, "LoginDenied", "TINYINT(1) DEFAULT '0' NOT NULL");
		$GLOBALS['DB_WE']->addCol(USER_TABLE, "UseSalt", "TINYINT(1) DEFAULT '0' NOT NULL");

		if($GLOBALS['DB_WE']->isColExist(USER_TABLE, "workSpace")){
			$GLOBALS['DB_WE']->changeColType(USER_TABLE, "workSpace", "VARCHAR(1000)");
			$DB_WE->query("UPDATE " . USER_TABLE . " SET workSpace='' WHERE workSpace='0';");
		}
		if($GLOBALS['DB_WE']->isColExist(USER_TABLE, "IsFolder")){
			$DB_WE->query("SELECT ID FROM " . USER_TABLE . " WHERE Type=1");
			while($DB_WE->next_record())
				$db123->query("UPDATE " . USER_TABLE . " SET IsFolder=1 WHERE ID=" . intval($DB_WE->f("ID")));
		}
		self::fix_icon();

		$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET BackendCharset="ISO-8859-1" WHERE (Language NOT LIKE "%_UTF-8%" AND Language!="") AND BackendCharset=""');
		$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET BackendCharset="UTF-8",Language=REPLACE(Language,"_UTF-8","") WHERE (Language LIKE "%_UTF-8%") AND BackendCharset=""');
		$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET BackendCharset="UTF-8",Language="Deutsch" WHERE Language="" AND BackendCharset=""');


		return true;
	}

	static function updateCustomers(){
		global $DB_WE;

		if(defined("CUSTOMER_TABLE")){
			if(weModuleInfo::isModuleInstalled("customer")){
				if(!$GLOBALS['DB_WE']->isTabExist(CUSTOMER_ADMIN_TABLE)){
					$cols = array(
						"Name" => "VARCHAR(255) NOT NULL",
						"Value" => "TEXT NOT NULL"
					);

					$GLOBALS['DB_WE']->addTable(CUSTOMER_ADMIN_TABLE, $cols);

					$DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('FieldAdds','');");
					$DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('SortView','');");
					$DB_WE->query("INSERT INTO " . CUSTOMER_ADMIN_TABLE . "(Name,Value) VALUES('Prefs','');");

					include(WE_MODULES_PATH . 'customer/weCustomerSettings.php');
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

			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "ParentID", "BINGINT DEFAULT '0' NOT NULL");
			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "Path", "VARCHAR(255) DEFAULT '' NOT NULL");
			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "IsFolder", "TINYINT(1) DEFAULT '0' NOT NULL");
			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "Icon", "VARCHAR(255) DEFAULT 'customer.gif' NOT NULL");
			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "Text", "VARCHAR(255) DEFAULT '' NOT NULL");

			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "Username", "VARCHAR(255) DEFAULT '' NOT NULL");
			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "Password", "VARCHAR(32) DEFAULT '' NOT NULL");
			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "Forename", "VARCHAR(255) DEFAULT '' NOT NULL");
			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "Surname", "VARCHAR(255) DEFAULT '' NOT NULL");


			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "LoginDenied", "TINYINT DEFAULT '0' NOT NULL");
			if(!$GLOBALS['DB_WE']->isColExist(CUSTOMER_TABLE, "MemberSince")){
				$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "MemberSince", "int(10) NOT NULL default 0");
				$DB_WE->query("UPDATE " . CUSTOMER_ADMIN_TABLE . " SET MemberSince='" . time() . "';");
			} else{
				$GLOBALS['DB_WE']->changeColType(CUSTOMER_TABLE, "MemberSince", "int(10) NOT NULL default 0");
			}

			if(!$GLOBALS['DB_WE']->isColExist(CUSTOMER_TABLE, "LastLogin"))
				$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "LastLogin", "int(10) NOT NULL default 0", ' AFTER MemberSince ');
			else
				$GLOBALS['DB_WE']->changeColType(CUSTOMER_TABLE, "LastLogin", "int(10) NOT NULL default 0");

			if(!$GLOBALS['DB_WE']->isColExist(CUSTOMER_TABLE, "LastAccess"))
				$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "LastAccess", "int(10) NOT NULL default 0", ' AFTER LastLogin ');
			else
				$GLOBALS['DB_WE']->changeColType(CUSTOMER_TABLE, "LastAccess", "int(10) NOT NULL default 0");

			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "AutoLoginDenied", "tinyint(1) NOT NULL default '0'", " AFTER LastAccess ");
			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "AutoLogin", "tinyint(1) NOT NULL default '0'", " AFTER AutoLoginDenied ");

			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "ModifyDate", "bigint(20) unsigned NOT NULL default '0'", " AFTER AutoLogin ");
			$GLOBALS['DB_WE']->addCol(CUSTOMER_TABLE, "ModifiedBy", "enum('','backend','frontend','external') NOT NULL default''", " AFTER ModifyDate ");

			$GLOBALS['DB_WE']->changeColType(CUSTOMER_TABLE, "Anrede_Anrede", "enum('','Herr','Frau') NOT NULL");

			$GLOBALS['DB_WE']->changeColType(CUSTOMER_TABLE, "Newsletter_Ok", "enum('','ja','0','1','2') NOT NULL");
			$GLOBALS['DB_WE']->changeColType(CUSTOMER_TABLE, "Newsletter_HTMLNewsletter", "enum('','ja','0','1','2') NOT NULL");
		}
		return true;
	}

	static function updateScheduler(){
		if(defined("SCHEDULE_TABLE")){
			$GLOBALS['DB_WE']->addCol(SCHEDULE_TABLE, "Schedpro", "longtext DEFAULT ''");
			$GLOBALS['DB_WE']->addCol(SCHEDULE_TABLE, "Type", "TINYINT(3) DEFAULT '0' NOT NULL");
			$GLOBALS['DB_WE']->addCol(SCHEDULE_TABLE, "Active", "TINYINT(1) DEFAULT '1'");
			$GLOBALS['DB_WE']->addCol(SCHEDULE_TABLE, "lockedUntil", "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");

			we_schedpro::check_and_convert_to_sched_pro();
		}
		return true;
	}

	static function updateNewsletter(){
		if(defined("NEWSLETTER_LOG_TABLE")){
			$GLOBALS['DB_WE']->addCol(NEWSLETTER_LOG_TABLE, "Param", "VARCHAR(255) DEFAULT ''");
		}
		if(defined("NEWSLETTER_BLOCK_TABLE")){
			$GLOBALS['DB_WE']->addCol(NEWSLETTER_BLOCK_TABLE, "Pack", "TINYINT(1) DEFAULT '0'");
		}
		return true;
	}

	static function updateShop(){
		if(defined("SHOP_TABLE")){
			$GLOBALS['DB_WE']->changeColType(SHOP_TABLE, "Price", "VARCHAR(20)");
			$GLOBALS['DB_WE']->changeColType(SHOP_TABLE, "IntQuantity", " float ");

			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateConfirmation', 'datetime default NULL', ' AFTER DateOrder ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomA', 'datetime default NULL', ' AFTER DateOrder ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomB', 'datetime default NULL', ' AFTER DateCustomA ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomC', 'datetime default NULL', ' AFTER DateCustomB ');

			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomD', 'datetime default NULL', ' AFTER DateShipping ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomE', 'datetime default NULL', ' AFTER DateCustomD ');

			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomF', 'datetime default NULL', ' AFTER DatePayment ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomG', 'datetime default NULL', ' AFTER DateCustomF ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCancellation', 'datetime default NULL', ' AFTER DateCustomG ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomH', 'datetime default NULL', ' AFTER DateCancellation ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomI', 'datetime default NULL', ' AFTER DateCustomH ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateCustomJ', 'datetime default NULL', ' AFTER DateCustomI ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'DateFinished', 'datetime default NULL', ' AFTER DateCustomJ ');

			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailOrder', 'datetime default NULL', ' AFTER DateFinished ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailConfirmation', 'datetime default NULL', ' AFTER MailOrder ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomA', 'datetime default NULL', ' AFTER MailConfirmation ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomB', 'datetime default NULL', ' AFTER MailCustomA ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomC', 'datetime default NULL', ' AFTER MailCustomB ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailShipping', 'datetime default NULL', ' AFTER MailCustomC ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomD', 'datetime default NULL', ' AFTER MailShipping ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomE', 'datetime default NULL', ' AFTER MailCustomD ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailPayment', 'datetime default NULL', ' AFTER MailCustomE ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomF', 'datetime default NULL', ' AFTER MailPayment ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomG', 'datetime default NULL', ' AFTER MailCustomF ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCancellation', 'datetime default NULL', ' AFTER MailCustomG ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomH', 'datetime default NULL', ' AFTER MailCancellation ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomI', 'datetime default NULL', ' AFTER MailCustomH ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailCustomJ', 'datetime default NULL', ' AFTER MailCustomI ');
			$GLOBALS['DB_WE']->addCol(SHOP_TABLE, 'MailFinished', 'datetime default NULL', ' AFTER MailCustomJ ');
		}
		return true;
	}

	static function updateObject(){
		if(defined("OBJECT_TABLE")){
			$GLOBALS['DB_WE']->addCol(OBJECT_TABLE, 'DefaultUrl', "varchar(255) NOT NULL default ''", ' AFTER  DefaultKeywords ');
			$GLOBALS['DB_WE']->addCol(OBJECT_TABLE, 'DefaultUrlfield0', "varchar(255) NOT NULL default ''", ' AFTER  DefaultUrl ');
			$GLOBALS['DB_WE']->addCol(OBJECT_TABLE, 'DefaultUrlfield1', "varchar(255) NOT NULL default ''", ' AFTER  DefaultUrlfield0 ');
			$GLOBALS['DB_WE']->addCol(OBJECT_TABLE, 'DefaultUrlfield2', "varchar(255) NOT NULL default ''", ' AFTER  DefaultUrlfield1 ');
			$GLOBALS['DB_WE']->addCol(OBJECT_TABLE, 'DefaultUrlfield3', "varchar(255) NOT NULL default ''", ' AFTER  DefaultUrlfield2 ');
			$GLOBALS['DB_WE']->addCol(OBJECT_TABLE, 'DefaultTriggerID', "bigint(20) NOT NULL default 0", ' AFTER  DefaultUrlfield3 ');
			$GLOBALS['DB_WE']->changeColType(OBJECT_TABLE, "Workspaces", " varchar(1000) NOT NULL default '' ");
			$GLOBALS['DB_WE']->changeColType(OBJECT_TABLE, "DefaultWorkspaces", " varchar(1000) NOT NULL default '' ");
		}
	}

	static function updateObjectFiles(){
		if(defined("OBJECT_FILES_TABLE")){
			$GLOBALS['DB_WE']->addCol(OBJECT_FILES_TABLE, 'Url', "varchar(255) NOT NULL default ''", ' AFTER Path ');
			$GLOBALS['DB_WE']->addCol(OBJECT_FILES_TABLE, 'TriggerID', "bigint NOT NULL default '0'", ' AFTER Url ');
			$GLOBALS['DB_WE']->changeColType(OBJECT_FILES_TABLE, "Workspaces", " varchar(1000) NOT NULL default '' ");
			$GLOBALS['DB_WE']->changeColType(OBJECT_FILES_TABLE, "ExtraWorkspaces", " varchar(1000) NOT NULL default '' ");
			$GLOBALS['DB_WE']->changeColType(OBJECT_FILES_TABLE, "ExtraWorkspacesSelected", " varchar(1000) NOT NULL default '' ");
		}
	}

	static function updateObjectFilesX(){
		if(defined('OBJECT_X_TABLE')){
			$_db = new DB_WE();

			$_table = OBJECT_FILES_TABLE;
			if($GLOBALS['DB_WE']->isColExist($_table, 'Url')){
				$GLOBALS['DB_WE']->changeColType($_table, 'Url', 'VARCHAR(255) NOT NULL');
			} else{
				$GLOBALS['DB_WE']->addCol($_table, 'Url', 'VARCHAR(255) NOT NULL', ' AFTER Path ');
			}
			if($GLOBALS['DB_WE']->isColExist($_table, 'TriggerID')){
				$GLOBALS['DB_WE']->changeColType($_table, 'TriggerID', "bigint NOT NULL default '0'");
			} else{
				$GLOBALS['DB_WE']->addCol($_table, 'TriggerID', "bigint NOT NULL default '0'", ' AFTER Url ');
			}
			if($GLOBALS['DB_WE']->isColExist($_table, 'IsSearchable')){
				$GLOBALS['DB_WE']->changeColType($_table, 'IsSearchable', 'TINYINT(1) DEFAULT 1');
			} else{
				$GLOBALS['DB_WE']->addCol($_table, 'IsSearchable', 'TINYINT(1) DEFAULT 1', ' AFTER Published ');
			}
			if($GLOBALS['DB_WE']->isColExist($_table, 'Charset')){
				$GLOBALS['DB_WE']->changeColType($_table, 'Charset', 'VARCHAR(64) DEFAULT NULL');
			} else{
				$GLOBALS['DB_WE']->addCol($_table, 'Charset', 'VARCHAR(64) DEFAULT NULL', ' AFTER IsSearchable ');
			}
			if($GLOBALS['DB_WE']->isColExist($_table, 'Language')){
				$GLOBALS['DB_WE']->changeColType($_table, 'Language', 'VARCHAR(5) DEFAULT NULL');
			} else{
				$GLOBALS['DB_WE']->addCol($_table, 'Language', 'VARCHAR(5) DEFAULT NULL', ' AFTER Charset ');
			}
			if($GLOBALS['DB_WE']->isColExist($_table, 'WebUserID')){
				$GLOBALS['DB_WE']->changeColType($_table, 'WebUserID', 'BIGINT(20) NOT NULL');
			} else{
				$GLOBALS['DB_WE']->addCol($_table, 'WebUserID', 'BIGINT(20) NOT NULL', ' AFTER Language ');
			}

			$_maxid = f('SELECT MAX(ID) as MaxTID FROM ' . OBJECT_TABLE, 'MaxTID', $_db) + 1;
			for($i = 1; $i < $_maxid; $i++){
				$_table = OBJECT_X_TABLE . $i;
				if($GLOBALS['DB_WE']->isTabExist($_table)){
					if($GLOBALS['DB_WE']->isColExist($_table, 'OF_Url')){
						$GLOBALS['DB_WE']->changeColType($_table, 'OF_Url', 'VARCHAR(255) NOT NULL');
					} else{
						$GLOBALS['DB_WE']->addCol($_table, 'OF_Url', 'VARCHAR(255) NOT NULL', '  AFTER OF_Path  ');
					}
					if($GLOBALS['DB_WE']->isColExist($_table, 'OF_TriggerID')){
						$GLOBALS['DB_WE']->changeColType($_table, 'OF_TriggerID', 'BIGINT(20) NOT NULL DEFAULT 0');
					} else{
						$GLOBALS['DB_WE']->addCol($_table, 'OF_TriggerID', 'BIGINT(20) NOT NULL DEFAULT 0', '  AFTER OF_Url  ');
					}
					if($GLOBALS['DB_WE']->isColExist($_table, 'OF_IsSearchable')){
						$GLOBALS['DB_WE']->changeColType($_table, 'OF_IsSearchable', 'TINYINT(1) DEFAULT 1');
					} else{
						$GLOBALS['DB_WE']->addCol($_table, 'OF_IsSearchable', 'TINYINT(1) DEFAULT 1', ' AFTER OF_Published ');
					}
					if($GLOBALS['DB_WE']->isColExist($_table, 'OF_Charset')){
						$GLOBALS['DB_WE']->changeColType($_table, 'OF_Charset', 'VARCHAR(64) NOT NULL');
					} else{
						$GLOBALS['DB_WE']->addCol($_table, 'OF_Charset', 'VARCHAR(64) NOT NULL', ' AFTER OF_IsSearchable ');
					}
					if($GLOBALS['DB_WE']->isColExist($_table, 'OF_WebUserID')){
						$GLOBALS['DB_WE']->changeColType($_table, 'OF_WebUserID', 'BIGINT(20) NOT NULL');
					} else{
						$GLOBALS['DB_WE']->addCol($_table, 'OF_WebUserID', 'BIGINT(20) NOT NULL', ' AFTER OF_Charset ');
					}
					if($GLOBALS['DB_WE']->isColExist($_table, 'OF_Language')){
						$GLOBALS['DB_WE']->changeColType($_table, 'OF_Language', 'VARCHAR(5) DEFAULT NULL');
					} else{
						$GLOBALS['DB_WE']->addCol($_table, 'OF_Language', 'VARCHAR(5) DEFAULT NULL', ' AFTER OF_WebUserID ');
					}
					//add indices to all objects
					self::updateUnindexedCols($_table, 'object_%');
					$key = 'KEY OF_WebUserID (OF_WebUserID)';
					if(!$GLOBALS['DB_WE']->isKeyExistAtAll($_table, $key)){
						$GLOBALS['DB_WE']->addKey($_table, $key);
					}
					$key = 'KEY published (OF_ID,OF_Published,OF_IsSearchable)';
					if(!$GLOBALS['DB_WE']->isKeyExistAtAll($_table, $key)){
						$GLOBALS['DB_WE']->addKey($_table, $key);
					}
					$key = 'KEY OF_IsSearchable (OF_IsSearchable)';
					if(!$GLOBALS['DB_WE']->isKeyExistAtAll($_table, $key)){
						$GLOBALS['DB_WE']->addKey($_table, $key);
					}
				}
			}
		}
		return true;
	}

	static function updateNavigation(){
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'Charset', 'varchar(255) NOT NULL default ""');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'Attributes', 'text NOT NULL');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'FolderSelection', 'enum("docLink","objLink","urlLink") NOT NULL default ""');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'FolderWsID', 'bigint(20) NOT NULL default "0"');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'FolderParameter', 'varchar(255) NOT NULL default ""');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'FolderUrl', 'varchar(255) NOT NULL default ""');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'LimitAccess', 'tinyint(4) NOT NULL default 0');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'AllCustomers', 'tinyint(4) NOT NULL default 0');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'ApplyFilter', 'tinyint(4) NOT NULL default 0');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'Customers', 'text NOT NULL');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'CustomerFilter', 'text NOT NULL');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'Published', 'int(11) NOT NULL DEFAULT "1"', ' AFTER Path ');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'Display', 'varchar(255) NOT NULL ', ' AFTER Text ');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'CurrentOnUrlPar', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER Text ');
		$GLOBALS['DB_WE']->addCol(NAVIGATION_TABLE, 'CurrentOnAnker', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER CurrentOnUrlPar ');
		$GLOBALS['DB_WE']->changeColType(NAVIGATION_TABLE, "Icon", " enum('folder.gif','link.gif') NOT NULL ");
		$GLOBALS['DB_WE']->changeColType(NAVIGATION_TABLE, "Selection", " enum('dynamic','nodynamic','static') NOT NULL ");
		$GLOBALS['DB_WE']->changeColType(NAVIGATION_TABLE, "FolderSelection", " enum('docLink','objLink','urlLink') NOT NULL ");
	}

	static function updateVoting(){
		if(defined('VOTING_TABLE')){
			$GLOBALS['DB_WE']->addCol(VOTING_TABLE, 'QASetAdditions', 'text', ' AFTER QASet ');
			$GLOBALS['DB_WE']->addCol(VOTING_TABLE, 'IsRequired', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER QASetAdditions ');
			$GLOBALS['DB_WE']->addCol(VOTING_TABLE, 'AllowFreeText', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER IsRequired ');
			$GLOBALS['DB_WE']->addCol(VOTING_TABLE, 'AllowImages', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER AllowFreeText ');
			$GLOBALS['DB_WE']->addCol(VOTING_TABLE, 'AllowMedia', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER AllowImages ');
			$GLOBALS['DB_WE']->addCol(VOTING_TABLE, 'AllowSuccessor', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER AllowMedia ');
			$GLOBALS['DB_WE']->addCol(VOTING_TABLE, 'AllowSuccessors', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER AllowSuccessor ');
			$GLOBALS['DB_WE']->addCol(VOTING_TABLE, 'Successor', 'bigint(20) unsigned NOT NULL DEFAULT 0', ' AFTER AllowSuccessors ');
			$GLOBALS['DB_WE']->addCol(VOTING_TABLE, 'FallbackUserID', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER UserAgent ');
			$GLOBALS['DB_WE']->addCol(VOTING_LOG_TABLE, 'votingsession', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER id ');
			$GLOBALS['DB_WE']->addCol(VOTING_LOG_TABLE, 'userid', 'bigint(20) NOT NULL DEFAULT 0', ' AFTER agent ');
			$GLOBALS['DB_WE']->addCol(VOTING_LOG_TABLE, 'answer', 'varchar(255) NOT NULL', ' AFTER status ');
			$GLOBALS['DB_WE']->addCol(VOTING_LOG_TABLE, 'answertext', 'text NOT NULL', ' AFTER answer ');
			$GLOBALS['DB_WE']->addCol(VOTING_LOG_TABLE, 'successor', 'bigint(20) unsigned NOT NULL DEFAULT 0', ' AFTER answertext ');
			$GLOBALS['DB_WE']->addCol(VOTING_LOG_TABLE, 'additionalfields', 'text NOT NULL', ' AFTER successor ');
			//this looks weird but means just :\"question inside the table
			$GLOBALS['DB_WE']->query('UPDATE ' . VOTING_TABLE . ' SET
			QASet=REPLACE(QASet,\'\\\\"\',\'"\'),
			QASetAdditions=REPLACE(QASetAdditions,\'\\\\"\',\'"\'),
			Scores=REPLACE(Scores,\'\\\\"\',\'"\'),
			Revote=REPLACE(Revote,\'\\\\"\',\'"\'),
			RevoteUserAgent=REPLACE(RevoteUserAgent,\'\\\\"\',\'"\'),
			LogData=REPLACE(LogData,\'\\\\"\',\'"\'),
			BlackList=REPLACE(BlackList,\'\\\\"\',\'"\')
			WHERE QASet LIKE \'%:\\\\\\\"question%\'');
		}
	}

	static function updateVersions(){
		if(defined("VERSIONS_TABLE")){
			$GLOBALS['DB_WE']->changeColType(VERSIONS_TABLE, 'DocType', 'varchar(64) NOT NULL');
			$GLOBALS['DB_WE']->addCol(VERSIONS_TABLE, 'MasterTemplateID', "bigint(20) NOT NULL default '0'", ' AFTER ExtraTemplates ');
			$GLOBALS['DB_WE']->changeColType(VERSIONS_TABLE, "documentElements", "blob");
			$GLOBALS['DB_WE']->changeColType(VERSIONS_TABLE, "documentScheduler", "blob");
			$GLOBALS['DB_WE']->changeColType(VERSIONS_TABLE, "documentCustomFilter", "blob");
			$GLOBALS['DB_WE']->changeColType(VERSIONS_TABLE, "Workspaces", " varchar(1000) NOT NULL ");
			$GLOBALS['DB_WE']->changeColType(VERSIONS_TABLE, "ExtraWorkspaces", " varchar(1000) NOT NULL ");
			$GLOBALS['DB_WE']->changeColType(VERSIONS_TABLE, "ExtraWorkspacesSelected", " varchar(1000) NOT NULL ");
		}
	}

	static function updateWorkflow(){
		if(defined('WORKFLOW_STEP_TABLE')){
			if($GLOBALS['DB_WE']->isColExist(WORKFLOW_STEP_TABLE, 'Worktime'))
				$GLOBALS['DB_WE']->changeColType(WORKFLOW_STEP_TABLE, 'Worktime', 'float NOT NULL default 0');
		}
		if(defined('WORKFLOW_TABLE')){
			if($GLOBALS['DB_WE']->isColExist(WORKFLOW_TABLE, 'DocType')){
				$GLOBALS['DB_WE']->changeColType(WORKFLOW_TABLE, 'DocType', "varchar(255) NOT NULL default ''");
			} else{
				$GLOBALS['DB_WE']->addCol(WORKFLOW_TABLE, 'DocType', "varchar(255) NOT NULL default ''", ' AFTER Folders ');
			}
			$GLOBALS['DB_WE']->addCol(WORKFLOW_TABLE, 'ObjectFileFolders', "varchar(255) NOT NULL default ''", ' AFTER Objects ');
			$GLOBALS['DB_WE']->addCol(WORKFLOW_TABLE, 'EmailPath', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER Status ');
			$GLOBALS['DB_WE']->addCol(WORKFLOW_TABLE, 'LastStepAutoPublish', 'tinyint(1) NOT NULL DEFAULT 0', ' AFTER EmailPath ');
		}
		if(defined('MESSAGES_TABLE')){
			$GLOBALS['DB_WE']->changeColType(MESSAGES_TABLE, 'seenStatus', " tinyint(4) unsigned NOT NULL default '0'");
		}
		if(defined('MSG_TODO_TABLE')){
			$GLOBALS['DB_WE']->changeColType(MSG_TODO_TABLE, 'seenStatus', " tinyint(3) unsigned NOT NULL default '0'");
		}
	}

	static function updateLock(){
		if(!$GLOBALS['DB_WE']->isTabExist(LOCK_TABLE)){
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
			$GLOBALS['DB_WE']->addTable(LOCK_TABLE, $cols, $keys);
		}
		$GLOBALS['DB_WE']->addCol(LOCK_TABLE, 'sessionID', "varchar(64) NOT NULL default ''", ' AFTER UserID ');
		if($GLOBALS['DB_WE']->isColExist(LOCK_TABLE, 'lock') && !$GLOBALS['DB_WE']->isColExist(LOCK_TABLE, 'lockTime'))
			$GLOBALS['DB_WE']->renameCol(LOCK_TABLE, 'lock', 'lockTime');
		if($GLOBALS['DB_WE']->isColExist(LOCK_TABLE, 'lock'))
			$GLOBALS['DB_WE']->delCol(LOCK_TABLE, 'lock');
		$GLOBALS['DB_WE']->addCol(LOCK_TABLE, 'lockTime', "datetime NOT NULL", ' AFTER sessionID ');
		return true;
	}

	static function updateTableKeys(){
		//FIXME: remove this unsafe code
		if(isset($_SESSION['weS']['weBackupVars']['tablekeys']) && is_array($_SESSION['weS']['weBackupVars']['tablekeys'])){
			$myarray = $_SESSION['weS']['weBackupVars']['tablekeys'];
			foreach($myarray as $table => $v){
				if(is_array($v)){
					foreach($v as $tabkey){
						if(!$GLOBALS['DB_WE']->isKeyExist($table, $tabkey)){
							if(($key = $GLOBALS['DB_WE']->isKeyExistAtAll($table, $tabkey))){
								$GLOBALS['DB_WE']->delKey($table, $key);
							}
							$GLOBALS['DB_WE']->addKey($table, $tabkey);
						}
					}
				}
			}
		}
		return true;
	}

	private static function updateLangLink(){
		if(!$GLOBALS['DB_WE']->isTabExist(LANGLINK_TABLE)){
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
				"UNIQUE KEY DID" => "(DID,DLocale,IsObject,IsFolder,Locale,DocumentTable)",
				"UNIQUE KEY DLocale" => "(DLocale,IsFolder,IsObject,LDID,Locale,DocumentTable)"
			);
			$GLOBALS['DB_WE']->addTable(LANGLINK_TABLE, $cols, $keys);
		}
		$GLOBALS['DB_WE']->addCol(LANGLINK_TABLE, 'DLocale', "varchar(5) NOT NULL default ''", ' AFTER DID ');

		if((!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DLocale` (`DLocale`,`IsFolder`,`IsObject`,`LDID`,`Locale`,`DocumentTable`)"))
			|| (!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DID` (`DID`,`DLocale`,`IsObject`,`IsFolder`,`Locale`,`DocumentTable`)"))){
			//no unique def. found
			$db = $GLOBALS['DB_WE'];
			if($db->query('CREATE TEMPORARY TABLE tmpLangLink LIKE ' . LANGLINK_TABLE)){

				// copy links from documents or document-folders to tmpLangLink only if DID and DLocale are consistent with Language in tblFile
				$db->query("INSERT INTO tmpLangLink SELECT " . LANGLINK_TABLE . ".* FROM " . LANGLINK_TABLE . ", " . FILE_TABLE . " WHERE " . LANGLINK_TABLE . ".DID = " . FILE_TABLE . ".ID AND " . LANGLINK_TABLE . ".DLocale = " . FILE_TABLE . ".Language AND " . LANGLINK_TABLE . ".IsObject = 0 AND " . LANGLINK_TABLE . ".DocumentTable = 'tblFile'");

				// copy links from objects or object-folders to tmpLangLink only if DID and DLocale are consistent with Language in tblObjectFiles
				$db->query("INSERT INTO tmpLangLink SELECT " . LANGLINK_TABLE . ".* FROM " . LANGLINK_TABLE . ", " . OBJECT_FILES_TABLE . " WHERE " . LANGLINK_TABLE . ".DID = " . OBJECT_FILES_TABLE . ".ID AND " . LANGLINK_TABLE . ".DLocale = " . OBJECT_FILES_TABLE . ".Language AND " . LANGLINK_TABLE . ".IsObject = 1");

				// copy links from doctypes to tmpLangLink only if DID and DLocale are consistent with Language in tblFile
				$db->query("INSERT INTO tmpLangLink SELECT " . LANGLINK_TABLE . ".* FROM " . LANGLINK_TABLE . ", " . DOC_TYPES_TABLE . " WHERE " . LANGLINK_TABLE . ".DID = " . DOC_TYPES_TABLE . ".ID AND " . LANGLINK_TABLE . ".DLocale = " . DOC_TYPES_TABLE . ".Language AND " . LANGLINK_TABLE . ".DocumentTable = 'tblDocTypes'");

				$db->query('TRUNCATE ' . LANGLINK_TABLE);
				if(!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DID` (`DID`,`DLocale`,`IsObject`,`IsFolder`,`Locale`,`DocumentTable`)")){
					if($GLOBALS['DB_WE']->isKeyExistAtAll(LANGLINK_TABLE, "UNIQUE KEY `DID` (`DID`,`DLocale`,`IsObject`,`IsFolder`,`Locale`,`DocumentTable`)")){
						$GLOBALS['DB_WE']->delKey(LANGLINK_TABLE, 'DID');
					}
					$GLOBALS['DB_WE']->addKey(LANGLINK_TABLE, 'UNIQUE KEY DID (DID,DLocale,IsObject,IsFolder,Locale,DocumentTable)');
				}
				if(!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DLocale` (`DLocale`,`IsFolder`,`IsObject`,`LDID`,`Locale`,`DocumentTable`)")){
					if($GLOBALS['DB_WE']->isKeyExistAtAll(LANGLINK_TABLE, "UNIQUE KEY `DLocale` (`DLocale`,`IsFolder`,`IsObject`,`LDID`,`Locale`,`DocumentTable`)")){
						$GLOBALS['DB_WE']->delKey(LANGLINK_TABLE, 'DLocale');
					}
					$GLOBALS['DB_WE']->addKey(LANGLINK_TABLE, 'UNIQUE KEY DLocale (DLocale,IsFolder,IsObject,LDID,Locale,DocumentTable)');
				}

				// copy links from documents, document-folders and object-folders (to documents) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query("INSERT IGNORE INTO " . LANGLINK_TABLE . " SELECT tmpLangLink.* FROM tmpLangLink, " . FILE_TABLE . " WHERE tmpLangLink.LDID = " . FILE_TABLE . ".ID AND tmpLangLink.Locale = " . FILE_TABLE . ".Language AND tmpLangLink.IsObject = 0 AND tmpLangLink.DocumentTable = 'tblFile' ORDER BY tmpLangLink.ID DESC");

				// copy links from objects (to objects) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query("INSERT IGNORE INTO " . LANGLINK_TABLE . " SELECT tmpLangLink.* FROM tmpLangLink, " . OBJECT_FILES_TABLE . " WHERE tmpLangLink.LDID = " . OBJECT_FILES_TABLE . ".ID AND tmpLangLink.Locale = " . OBJECT_FILES_TABLE . ".Language AND tmpLangLink.IsObject = 1 ORDER BY tmpLangLink.ID DESC");

				// copy links from doctypes (to doctypes) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query("INSERT IGNORE INTO " . LANGLINK_TABLE . " SELECT tmpLangLink.* FROM tmpLangLink, " . DOC_TYPES_TABLE . " WHERE tmpLangLink.LDID = " . DOC_TYPES_TABLE . ".ID AND tmpLangLink.Locale = " . DOC_TYPES_TABLE . ".Language AND tmpLangLink.DocumentTable = 'tblDocTypes' ORDER BY tmpLangLink.ID DESC");
			} else{
				t_e('no rights to create temp-table');
			}
		}
	}

	static function convertTemporaryDoc(){
		if($GLOBALS['DB_WE']->isColExist(TEMPORARY_DOC_TABLE, 'ID')){
			$GLOBALS['DB_WE']->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE Active=0');
			$GLOBALS['DB_WE']->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocTable="tblFile" WHERE DocTable  LIKE "%tblFile"');
			$GLOBALS['DB_WE']->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocTable="tblObjectFiles" WHERE DocTable LIKE "%tblObjectFiles"');
			$GLOBALS['DB_WE']->delCol(TEMPORARY_DOC_TABLE, 'ID');
			$GLOBALS['DB_WE']->delKey(TEMPORARY_DOC_TABLE, 'PRIMARY');
			$GLOBALS['DB_WE']->addKey(TEMPORARY_DOC_TABLE, 'PRIMARY KEY ( `DocumentID` , `DocTable` , `Active` )');
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
		$del = self::getAllIDFromQuery('SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND DID NOT IN(SELECT ID FROM ' . FILE_TABLE . ')');
		$del = array_merge($del, self::getAllIDFromQuery('SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblTemplates" AND DID NOT IN(SELECT ID FROM ' . TEMPLATES_TABLE . ')'));

		if(!empty($del)){
			$db->query('DELETE FROM ' . LINK_TABLE . ' WHERE CID IN (' . implode(',', $del) . ')');
		}

		$del = self::getAllIDFromQuery('SELECT ID FROM ' . CONTENT_TABLE . ' WHERE ID NOT IN (SELECT CID FROM ' . LINK_TABLE . ')');
		if(!empty($del)){
			$db->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID IN (' . implode(',', $del) . ')');
		}

		if(defined('SCHEDULE_TABLE')){
			$db->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE ClassName != "we_objectFile" AND DID NOT IN (SELECT ID FROM ' . FILE_TABLE . ')');

			if(defined('OBJECT_FILES_TABLE')){
				$db->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE ClassName = "we_objectFile" AND DID NOT IN (SELECT ID FROM ' . OBJECT_FILES_TABLE . ')');
			}
		}
		//FIXME: clean customerfilter
		//FIXME: clean history
		//FIXME: clean inconsistent objects
	}

	static function updateGlossar(){
		//FIXME: remove after 7.0
		foreach($GLOBALS['weFrontendLanguages'] as $lang){
			$cache = new weGlossaryCache($lang);
			$cache->write();
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
		self::updateGlossar();
	}

}
