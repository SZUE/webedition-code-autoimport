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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//FIXME: remove this file almost complete; at least all DB queries. Replace by Update-Script calls on DB-Files.
class we_updater{

	static function replayUpdateDB($specFile = ''){
		include_once(WEBEDITION_PATH . 'liveUpdate/conf/conf.inc.php');
		include_once(WEBEDITION_PATH . 'liveUpdate/classes/liveUpdateFunctions.class.php');
		$lf = new liveUpdateFunctions();
		$GLOBALS['we']['errorhandler']['sql'] = false;
		if($specFile){
			$lf->executeQueriesInFiles(LIVEUPDATE_CLIENT_DOCUMENT_DIR . 'sqldumps/' . $specFile);
		} else {
			$d = dir(LIVEUPDATE_CLIENT_DOCUMENT_DIR . 'sqldumps');
			while(false !== ($entry = $d->read())){
				if(substr($entry, -4) === '.sql'){
					$lf->executeQueriesInFiles(LIVEUPDATE_CLIENT_DOCUMENT_DIR . 'sqldumps/' . $entry);
				}
			}
			$d->close();
		}
		$GLOBALS['we']['errorhandler']['sql'] = true;
		if(($entries = $lf->getQueryLog('error'))){
			t_e('Errors while updating tables', $entries);
		}
	}

	static function updateTables($DB_WE = null){
		$db2 = new DB_WE();
		$tables = $db2->table_names(TBL_PREFIX . 'tblOwner');
		$DB_WE = $DB_WE ? : new DB_WE(); //old code calls without object

		if(!empty($tables)){
			$DB_WE->query('SELECT * FROM ' . TBL_PREFIX . 'tblOwner');
			while($DB_WE->next_record()){
				$table = $DB_WE->f('DocumentTable');
				if($table == TEMPLATES_TABLE || $table == FILE_TABLE){
					$id = $DB_WE->f('fileID');
					if($table && $id){
						$Owners = ($DB_WE->f("OwnerID") && ($DB_WE->f("OwnerID") != $DB_WE->f("CreatorID"))) ? ("," . $DB_WE->f("OwnerID") . ",") : "";
						$CreatorID = $DB_WE->f("CreatorID") ? : $_SESSION["user"]["ID"];
						$ModifierID = $DB_WE->f("ModifierID") ? : $_SESSION["user"]["ID"];
						$db2->query('UPDATE ' . $db2->escape($table) . " SET CreatorID=" . intval($CreatorID) . " , ModifierID=" . intval($ModifierID) . " , Owners='" . $db2->escape($Owners) . "' WHERE ID=" . intval($id));
						$db2->query('DELETE FROM ' . TBL_PREFIX . ' WHERE fileID=' . intval($id));
						update_time_limit(30);
					}
				}
			}
			$DB_WE->query('DROP TABLE ' . TBL_PREFIX . 'tblOwner');
		}

		$DB_WE->query('UPDATE ' . CATEGORY_TABLE . ' SET Text=Category WHERE Text=""');
		$DB_WE->query('UPDATE ' . CATEGORY_TABLE . ' SET Path=CONCAT("/",Category) WHERE Path=""');

		$DB_WE->query('DROP TABLE IF EXISTS ' . PREFS_TABLE . '_old');
		if(count(getHash('SELECT * FROM ' . PREFS_TABLE . ' LIMIT 1', null, MYSQL_ASSOC)) > 3){
			//make a backup
			$DB_WE->query('CREATE TABLE ' . PREFS_TABLE . '_old LIKE ' . PREFS_TABLE);
			$DB_WE->query('INSERT INTO ' . PREFS_TABLE . '_old SELECT * FROM ' . PREFS_TABLE);

			$DB_WE->query('DELETE FROM ' . PREFS_TABLE . ' WHERE userID=0');
			$DB_WE->query('SELECT * FROM ' . PREFS_TABLE . ' LIMIT 1');
			$queries = $DB_WE->getAll();
			$keys = array_keys($queries[0]);
			foreach($keys as $key){
				switch($key){
					case 'userID':
					case 'key':
					case 'value':
						continue;
					default:
						$GLOBALS['DB_WE']->delCol(PREFS_TABLE, $key);
				}
			}

			$GLOBALS['DB_WE']->query('DELETE FROM ' . PREFS_TABLE . ' WHERE `key`=""');
			foreach($queries as $q){
				we_users_user::writePrefs($q['userID'], $GLOBALS['DB_WE'], $q);
			}
		}
	}

	static function fix_user($db){
		//FIXME: since this is done ever and ever, remove this after 6.4.3
		$db2 = new DB_WE();
		$db->query('SELECT ID,username,ParentID,Path FROM ' . USER_TABLE);
		while($db->next_record()){
			update_time_limit(30);
			$id = $db->f('ID');
			$pid = $db->f('ParentID');
			$path = '/' . $db->f("username");
			while($pid > 0){
				$db2->query('SELECT username,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($pid));
				if($db2->next_record()){
					$path = '/' . $db2->f("username") . $path;
					$pid = $db2->f("ParentID");
				} else {
					$pid = 0;
				}
			}
			if($db->f('Path') != $path){
				$db2->query('UPDATE ' . USER_TABLE . " SET Path='" . $db2->escape($path) . "' WHERE ID=" . intval($id));
			}
		}
		$db->query('UPDATE ' . USER_TABLE . ' SET Text=username');
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='user_alias.gif' WHERE Type=" . we_users_user::TYPE_ALIAS);
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='usergroup.gif' WHERE Type=" . we_users_user::TYPE_USER_GROUP);
		$db->query('UPDATE ' . USER_TABLE . " SET Icon='user.gif' WHERE Type=" . we_users_user::TYPE_USER);
	}

	static function updateUnindexedCols($tab, $col){
		global $DB_WE;
		$DB_WE->query('SHOW COLUMNS FROM ' . $DB_WE->escape($tab) . " LIKE '" . $DB_WE->escape($col) . "'");
		$query = array();
		while($DB_WE->next_record()){
			if(!$DB_WE->f('Key')){
				$query[] = 'ADD INDEX (' . $DB_WE->f('Field') . ')';
			}
		}
		if(!empty($query)){
			$DB_WE->query('ALTER TABLE ' . $DB_WE->escape($tab) . ' ' . implode(', ', $query));
		}
	}

	static function updateUsers($DB_WE){
		$DB_WE = $DB_WE? : new DB_WE();
		self::fix_user($DB_WE);

		$DB_WE->query('SELECT DISTINCT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND (value NOT LIKE "%_UTF-8%" OR value!="") AND userID IN (SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="BackendCharset" AND value="")');
		$users = $DB_WE->getAll(true);
		if($users){
			$DB_WE->query('UPDATE ' . PREFS_TABLE . ' SET value="ISO-8859-1" WHERE `key`="BackendCharset" AND userID IN (' . implode(',', $users) . ')');
		}
		$DB_WE->query('SELECT DISTINCT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND (value LIKE "%_UTF-8%") AND userID IN (SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="BackendCharset" AND value="")');
		$users = $DB_WE->getAll(true);
		if($users){
			$DB_WE->query('UPDATE ' . PREFS_TABLE . ' SET value="UTF-8" WHERE `key`="BackendCharset" AND userID IN (' . implode(',', $users) . ')');
			$DB_WE->query('UPDATE ' . PREFS_TABLE . ' SET value=REPLACE(value,"_UTF-8","") WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
		}
		$DB_WE->query('SELECT DISTINCT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND value="" AND userID IN (SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="BackendCharset" AND value="")');
		$users = $DB_WE->getAll(true);
		if($users){
			$DB_WE->query('UPDATE ' . PREFS_TABLE . ' SET value="UTF-8" WHERE `key`="BackendCharset" AND userID IN (' . implode(',', $users) . ')');
			$DB_WE->query('UPDATE ' . PREFS_TABLE . ' SET value="Deutsch" WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
			//$_SESSION['prefs'] = we_user::readPrefs($_SESSION['user']['ID'], $GLOBALS['DB_WE']);
		}
		$DB_WE->query('SELECT DISTINCT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND value=""');
		$users = $DB_WE->getAll(true);
		if($users){
			$DB_WE->query('UPDATE ' . PREFS_TABLE . ' SET value="Deutsch" WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
		}
		$_SESSION['prefs'] = we_users_user::readPrefs($_SESSION['user']['ID'], $DB_WE);


		return true;
	}

	static function updateScheduler(){
		if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
			we_schedpro::check_and_convert_to_sched_pro();
		}
		return true;
	}

	static function updateObjectFilesX(){
		if(defined('OBJECT_X_TABLE')){
			$_db = new DB_WE();
			//correct folder properties
			$_db->query('UPDATE ' . OBJECT_FILES_TABLE . ' f SET IsClassFolder=IF(ParentID=0,1,0)');

			//all files should have a tableid
			$_db->query('UPDATE ' . OBJECT_FILES_TABLE . ' f SET TableID=(SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path=f.Path) WHERE IsClassFolder=1 AND TableID=0');
			$_db->query('UPDATE ' . OBJECT_FILES_TABLE . ' f SET TableID=(SELECT ID FROM ' . OBJECT_TABLE . ' WHERE f.Path LIKE CONCAT(Path,"/%") ) WHERE IsClassFolder=0 AND IsFolder=1 AND TableID=0');

			//all files without a tableID can be deleted
			$_db->query('DELETE FROM ' . OBJECT_FILES_TABLE . ' WHERE TableID=0');


			$_db->query('SHOW TABLES LIKE "' . str_replace('_', '', OBJECT_X_TABLE) . '\_%"'); //note: _% ignores _, so escaping _ with \_ does the job
			$allTab = $_db->getAll(true);
			foreach($allTab as $_table){
				if($_table == OBJECT_FILES_TABLE){
					continue;
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_Url')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_Url', 'VARCHAR(255) NOT NULL');
				} else {
					$GLOBALS['DB_WE']->addCol($_table, 'OF_Url', 'VARCHAR(255) NOT NULL', '  AFTER OF_Path  ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_TriggerID')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_TriggerID', 'BIGINT(20) NOT NULL DEFAULT 0');
				} else {
					$GLOBALS['DB_WE']->addCol($_table, 'OF_TriggerID', 'BIGINT(20) NOT NULL DEFAULT 0', '  AFTER OF_Url  ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_IsSearchable')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_IsSearchable', 'TINYINT(1) DEFAULT 1');
				} else {
					$GLOBALS['DB_WE']->addCol($_table, 'OF_IsSearchable', 'TINYINT(1) DEFAULT 1', ' AFTER OF_Published ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_Charset')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_Charset', 'VARCHAR(64) NOT NULL');
				} else {
					$GLOBALS['DB_WE']->addCol($_table, 'OF_Charset', 'VARCHAR(64) NOT NULL', ' AFTER OF_IsSearchable ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_WebUserID')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_WebUserID', 'BIGINT(20) NOT NULL');
				} else {
					$GLOBALS['DB_WE']->addCol($_table, 'OF_WebUserID', 'BIGINT(20) NOT NULL', ' AFTER OF_Charset ');
				}
				if($GLOBALS['DB_WE']->isColExist($_table, 'OF_Language')){
					$GLOBALS['DB_WE']->changeColType($_table, 'OF_Language', 'VARCHAR(5) DEFAULT NULL');
				} else {
					$GLOBALS['DB_WE']->addCol($_table, 'OF_Language', 'VARCHAR(5) DEFAULT NULL', ' AFTER OF_WebUserID ');
				}
				//remove old indices from all objects
				self::updateUnindexedCols($_table, we_object::QUERY_PREFIX . '%');
				if($GLOBALS['DB_WE']->isKeyExistAtAll($_table, 'OF_WebUserID')){
					$GLOBALS['DB_WE']->delKey($_table, 'OF_WebUserID');
				}
				if($GLOBALS['DB_WE']->isKeyExistAtAll($_table, 'published')){
					$GLOBALS['DB_WE']->delKey($_table, 'published');
				}
				if($GLOBALS['DB_WE']->isKeyExistAtAll($_table, 'OF_IsSearchable')){
					$GLOBALS['DB_WE']->delKey($_table, 'OF_IsSearchable');
				}

				if($GLOBALS['DB_WE']->isColExist($_table, 'ID')){
					$key = 'UNIQUE KEY OF_ID (OF_ID)';
					if(!$GLOBALS['DB_WE']->isKeyExistAtAll($_table, 'OF_ID')){
						$GLOBALS['DB_WE']->query('DELETE FROM ' . $_table . ' WHERE OF_ID=0');
						$GLOBALS['DB_WE']->addKey($_table, $key);
						if(!$GLOBALS['DB_WE']->isKeyExistAtAll($_table, 'OF_ID')){
							//we have duplicates in this table - we must clean up
							//should we add an index first?
							$GLOBALS['DB_WE']->query('SELECT DISTINCT o.ID FROM ' . $_table . ' o JOIN ' . $_table . ' oo ON o.OF_ID=oo.OF_ID WHERE o.ID<oo.ID');
							$ids = $GLOBALS['DB_WE']->getAll(true);
							$GLOBALS['DB_WE']->query('DELETE FROM ' . $_table . ' WHERE ID IN(' . implode(',', $ids) . ')');
							//retry to add key
							$GLOBALS['DB_WE']->addKey($_table, $key);
						}
						$GLOBALS['DB_WE']->query('REPLACE INTO ' . $_table . ' SET OF_ID=0');
					}
					//remove col id, set OF_ID=primary
					$GLOBALS['DB_WE']->delCol($_table, 'ID');
					$GLOBALS['DB_WE']->addKey($_table, 'PRIMARY KEY (OF_ID)');
					//no need for this index
					$GLOBALS['DB_WE']->delKey($_table, 'OF_ID');
				}
			}
		}
		return true;
	}

	static function updateVoting(){
		if(defined('VOTING_TABLE')){
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

	private static function updateLangLink(){
		if((!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DLocale` (`DLocale`,`IsFolder`,`IsObject`,`LDID`,`Locale`,`DocumentTable`)")) || (!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DID` (`DID`,`DLocale`,`IsObject`,`IsFolder`,`Locale`,`DocumentTable`)"))){
			//no unique def. found
			$db = $GLOBALS['DB_WE'];
			if($db->query('CREATE TEMPORARY TABLE tmpLangLink LIKE ' . LANGLINK_TABLE)){

				// copy links from documents or document-folders to tmpLangLink only if DID and DLocale are consistent with Language in tblFile
				$db->query("INSERT INTO tmpLangLink SELECT " . LANGLINK_TABLE . ".* FROM " . LANGLINK_TABLE . ", " . FILE_TABLE . " WHERE " . LANGLINK_TABLE . ".DID = " . FILE_TABLE . ".ID AND " . LANGLINK_TABLE . ".DLocale = " . FILE_TABLE . ".Language AND " . LANGLINK_TABLE . ".IsObject = 0 AND " . LANGLINK_TABLE . ".DocumentTable = 'tblFile'");

				// copy links from objects or object-folders to tmpLangLink only if DID and DLocale are consistent with Language in tblObjectFiles
				$db->query("INSERT INTO tmpLangLink SELECT " . LANGLINK_TABLE . ".* FROM " . LANGLINK_TABLE . ", " . OBJECT_FILES_TABLE . " WHERE " . LANGLINK_TABLE . ".DID = " . OBJECT_FILES_TABLE . ".ID AND " . LANGLINK_TABLE . ".DLocale = " . OBJECT_FILES_TABLE . ".Language AND " . LANGLINK_TABLE . ".IsObject = 1");

				// copy links from doctypes to tmpLangLink only if DID and DLocale are consistent with Language in tblFile
				$db->query('INSERT INTO tmpLangLink SELECT ' . LANGLINK_TABLE . ".* FROM " . LANGLINK_TABLE . ", " . DOC_TYPES_TABLE . " WHERE " . LANGLINK_TABLE . ".DID = " . DOC_TYPES_TABLE . ".ID AND " . LANGLINK_TABLE . ".DLocale = " . DOC_TYPES_TABLE . ".Language AND " . LANGLINK_TABLE . ".DocumentTable = 'tblDocTypes'");

				$db->query('TRUNCATE ' . LANGLINK_TABLE);
				if(!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DID` (`DID`,`DLocale`,`IsObject`,`IsFolder`,`Locale`,`DocumentTable`)")){
					if($GLOBALS['DB_WE']->isKeyExistAtAll(LANGLINK_TABLE, "DID")){
						$GLOBALS['DB_WE']->delKey(LANGLINK_TABLE, 'DID');
					}
					$GLOBALS['DB_WE']->addKey(LANGLINK_TABLE, 'UNIQUE KEY DID (DID,DLocale,IsObject,IsFolder,Locale,DocumentTable)');
				}
				if(!$GLOBALS['DB_WE']->isKeyExist(LANGLINK_TABLE, "UNIQUE KEY `DLocale` (`DLocale`,`IsFolder`,`IsObject`,`LDID`,`Locale`,`DocumentTable`)")){
					if($GLOBALS['DB_WE']->isKeyExistAtAll(LANGLINK_TABLE, "DLocale")){
						$GLOBALS['DB_WE']->delKey(LANGLINK_TABLE, 'DLocale');
					}
					$GLOBALS['DB_WE']->addKey(LANGLINK_TABLE, 'UNIQUE KEY DLocale (DLocale,IsFolder,IsObject,LDID,Locale,DocumentTable)');
				}

				// copy links from documents, document-folders and object-folders (to documents) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query('INSERT IGNORE INTO ' . LANGLINK_TABLE . ' SELECT tmpLangLink.* FROM tmpLangLink, ' . FILE_TABLE . " WHERE tmpLangLink.LDID = " . FILE_TABLE . ".ID AND tmpLangLink.Locale = " . FILE_TABLE . ".Language AND tmpLangLink.IsObject=0 AND tmpLangLink.DocumentTable = 'tblFile' ORDER BY tmpLangLink.ID DESC");

				// copy links from objects (to objects) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query('INSERT IGNORE INTO ' . LANGLINK_TABLE . " SELECT tmpLangLink.* FROM tmpLangLink, " . OBJECT_FILES_TABLE . " WHERE tmpLangLink.LDID = " . OBJECT_FILES_TABLE . ".ID AND tmpLangLink.Locale = " . OBJECT_FILES_TABLE . ".Language AND tmpLangLink.IsObject = 1 ORDER BY tmpLangLink.ID DESC");

				// copy links from doctypes (to doctypes) back to tblLangLink only if LDID and Locale are consistent with Language in tblFile
				$db->query('INSERT IGNORE INTO ' . LANGLINK_TABLE . " SELECT tmpLangLink.* FROM tmpLangLink, " . DOC_TYPES_TABLE . " WHERE tmpLangLink.LDID = " . DOC_TYPES_TABLE . ".ID AND tmpLangLink.Locale = " . DOC_TYPES_TABLE . ".Language AND tmpLangLink.DocumentTable = 'tblDocTypes' ORDER BY tmpLangLink.ID DESC");
			} else {
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

	static function fixInconsistentTables(){
		$db = $GLOBALS['DB_WE'];
		$db->query('SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND DID NOT IN(SELECT ID FROM ' . FILE_TABLE . ')
UNION
SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblTemplates" AND DID NOT IN(SELECT ID FROM ' . TEMPLATES_TABLE . ')', true);
		$del = $db->getAll(true);

		if($del){
			$db->query('DELETE FROM ' . LINK_TABLE . ' WHERE CID IN (' . implode(',', $del) . ')');
		}

		$db->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID NOT IN (SELECT CID FROM ' . LINK_TABLE . ')');

		if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
			$db->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE ClassName != "we_objectFile" AND DID NOT IN (SELECT ID FROM ' . FILE_TABLE . ')');

			if(defined('OBJECT_FILES_TABLE')){
				$db->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE ClassName = "we_objectFile" AND DID NOT IN (SELECT ID FROM ' . OBJECT_FILES_TABLE . ')');
			}
		}
		//FIXME: clean customerfilter
		//FIXME: clean inconsistent objects
	}

	static function updateGlossar(){
		//FIXME: remove after 7.0
		if(defined('GLOSSARY_TABLE')){
			foreach($GLOBALS['weFrontendLanguages'] as $lang){
				$cache = new we_glossary_cache($lang);
				$cache->write();
			}
		}
	}

	static function updateCats(){
		$db = $GLOBALS['DB_WE'];
		if($db->isColExist(CATEGORY_TABLE, 'Catfields') && f('SELECT COUNT(1) FROM ' . CATEGORY_TABLE . ' WHERE Title=""') == f('SELECT COUNT(1) FROM ' . CATEGORY_TABLE)){
			$db->query('SELECT ID,Catfields FROM ' . CATEGORY_TABLE . ' WHERE Catfields!=""');
			$udb = new DB_WE();
			while($db->next_record()){
				$data = unserialize($db->f('Catfields'));
				$udb->query('UPDATE ' . CATEGORY_TABLE . ' SET ' . we_database_base::arraySetter(array(
							'Title' => $data['default']['Title'],
							'Description' => $data['default']['Description'],
						)) . ' WHERE ID=' . $db->f('ID'));
			}
			$db->delCol(CATEGORY_TABLE, 'Catfields');
		}
	}

	static function fixHistory($db = null){
		$db = $db? : new DB_WE();
		if($db->isColExist(HISTORY_TABLE, 'ID')){
			$db->query('SELECT h1.ID FROM ' . HISTORY_TABLE . ' h1 LEFT JOIN ' . HISTORY_TABLE . ' h2 ON h1.DID=h2.DID AND h1.DocumentTable=h2.DocumentTable AND h1.ModDate=h2.ModDate WHERE h1.ID<h2.ID');
			$tmp = $db->getAll(true);
			if($tmp){
				$db->query('DELETE FROM ' . HISTORY_TABLE . ' WHERE ID IN (' . implode(',', $tmp) . ')');
			}
			$db->delCol(HISTORY_TABLE, 'ID');
			if($db->isKeyExistAtAll(HISTORY_TABLE, 'DID')){
				$db->delKey(HISTORY_TABLE, 'DID');
			}
			self::replayUpdateDB('tblhistory.sql');
		}
		if(f('SELECT COUNT(1) c FROM ' . HISTORY_TABLE . ' GROUP BY UID HAVING c>' . we_history::MAX . ' LIMIT 1')){
			$db->query('DELETE FROM ' . HISTORY_TABLE . ' WHERE ModDate="0000-00-00 00:00:00"');
			$db->query('RENAME TABLE ' . HISTORY_TABLE . ' TO old' . HISTORY_TABLE);
			//create clean table
			self::replayUpdateDB('tblhistory.sql');
			$db->query('INSERT IGNORE INTO ' . HISTORY_TABLE . ' (DID,DocumentTable,ContentType,ModDate,UserName,UID) SELECT DID,DocumentTable,ContentType,MAX(ModDate),UserName,UID FROM old' . HISTORY_TABLE . ' GROUP BY UID,DID,DocumentTable');
			$db->query('SELECT UID,COUNT(1) c FROM ' . HISTORY_TABLE . ' GROUP BY UID HAVING c>' . we_history::MAX);
			$all = $db->getAllFirst(false);
			foreach($all as $uid => $cnt){
				$db->query('DELETE FROM ' . HISTORY_TABLE . ' WHERE UID=' . $uid . ' ORDER BY ModDate DESC LIMIT ' . ($cnt - we_history::MAX));
			}
			$db->query('DROP TABLE old' . HISTORY_TABLE);
		}
	}

	private static function meassure($name){
		static $last = 0;
		static $times = array();
		$last = $last? : microtime(true);
		if($name == -1){
			t_e('notice', 'time for updates', $times);
			return;
		}
		$now = microtime(true);
		$times[] = $name . ': ' . ($now - $last);
		$last = $now;
	}

	public function doUpdate(){
		$db = new DB_WE();
		self::meassure('start');
		self::replayUpdateDB();
		self::meassure('replayUpdateDB');

		self::updateTables($db);
		self::meassure('updateTables');
		self::updateUsers($db);
		self::meassure('updateUsers');
		self::updateObjectFilesX();
		self::meassure('updateObjectFilesX');
		self::updateScheduler();
		self::meassure('updateScheduler');
		self::updateVoting();
		self::meassure('updateVoting');
		self::convertTemporaryDoc();
		self::meassure('convertTemporaryDoc');
		self::updateLangLink();
		self::meassure('updateLangLink');
		self::fixInconsistentTables();
		self::meassure('fixInconsistentTables');
		self::updateGlossar();
		self::meassure('updateGlossar');
		self::updateCats();
		self::meassure('updateCats');
		self::fixHistory();
		self::meassure('fixHistory');
		self::replayUpdateDB();
		self::meassure('replayUpdateDB');
	}

}
