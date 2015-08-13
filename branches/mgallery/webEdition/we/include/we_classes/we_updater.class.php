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
abstract class we_updater{

	private static function replayUpdateDB($specFile = ''){
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

//FIXME: remove - only present due to calls of patches
	public static function updateTables($db = null){
		return;
	}

	/* private static function updateUnindexedCols($tab, $col){
	  $db = $GLOBALS['DB_WE'];
	  $db->query('SHOW COLUMNS FROM ' . $db->escape($tab) . " LIKE '" . $db->escape($col) . "'");
	  $query = array();
	  while($db->next_record()){
	  if(!$db->f('Key')){
	  $query[] = 'ADD INDEX (' . $db->f('Field') . ')';
	  }
	  }
	  if(!empty($query)){
	  $db->query('ALTER TABLE ' . $db->escape($tab) . ' ' . implode(', ', $query));
	  }
	  } */

	public static function updateUsers($db = null){ //FIXME: remove after 6.5 from 6360/update6300.php
		$db = $db? : new DB_WE();
		$db->query('DROP TABLE IF EXISTS ' . PREFS_TABLE . '_old');
		if(count(getHash('SELECT * FROM ' . PREFS_TABLE . ' LIMIT 1', null, MYSQL_ASSOC)) > 3){
			//make a backup
			$db->query('CREATE TABLE ' . PREFS_TABLE . '_old LIKE ' . PREFS_TABLE);
			$db->query('INSERT INTO ' . PREFS_TABLE . '_old SELECT * FROM ' . PREFS_TABLE);

			$db->query('DELETE FROM ' . PREFS_TABLE . ' WHERE userID=0');
			$db->query('SELECT * FROM ' . PREFS_TABLE . ' LIMIT 1');
			$queries = $db->getAll();
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

			$db->query('DELETE FROM ' . PREFS_TABLE . ' WHERE `key`=""');
			foreach($queries as $q){
				we_users_user::writePrefs($q['userID'], $db, $q);
			}
		}

		$db->query('SELECT DISTINCT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND (value NOT LIKE "%_UTF-8%" OR value!="") AND userID IN (SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="BackendCharset" AND value="")');
		$users = $db->getAll(true);
		if($users){
			$db->query('UPDATE ' . PREFS_TABLE . ' SET value="ISO-8859-1" WHERE `key`="BackendCharset" AND userID IN (' . implode(',', $users) . ')');
		}
		$db->query('SELECT DISTINCT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND (value LIKE "%_UTF-8%") AND userID IN (SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="BackendCharset" AND value="")');
		$users = $db->getAll(true);
		if($users){
			$db->query('UPDATE ' . PREFS_TABLE . ' SET value="UTF-8" WHERE `key`="BackendCharset" AND userID IN (' . implode(',', $users) . ')');
			$db->query('UPDATE ' . PREFS_TABLE . ' SET value=REPLACE(value,"_UTF-8","") WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
		}
		$db->query('SELECT DISTINCT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND value="" AND userID IN (SELECT userID FROM ' . PREFS_TABLE . ' WHERE `key`="BackendCharset" AND value="")');
		$users = $db->getAll(true);
		if($users){
			$db->query('UPDATE ' . PREFS_TABLE . ' SET value="UTF-8" WHERE `key`="BackendCharset" AND userID IN (' . implode(',', $users) . ')');
			$db->query('UPDATE ' . PREFS_TABLE . ' SET value="Deutsch" WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
			//$_SESSION['prefs'] = we_user::readPrefs($_SESSION['user']['ID'], $GLOBALS['DB_WE']);
		}
		$db->query('SELECT DISTINCT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND value=""');
		$users = $db->getAll(true);
		if($users){
			$db->query('UPDATE ' . PREFS_TABLE . ' SET value="Deutsch" WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
			$_SESSION['prefs'] = we_users_user::readPrefs($_SESSION['user']['ID'], $db);
		}

		return true;
	}

	private static function updateObjectFilesX(){
		//FIXME: this takes long, so try to remove this
		if(defined('OBJECT_X_TABLE')){
			//this is from 6.3.9
			$_db = new DB_WE();
			//correct folder properties
			$_db->query('UPDATE ' . OBJECT_FILES_TABLE . ' f SET IsClassFolder=IF(ParentID=0,1,0)');

			//all files should have a tableid
			$_db->query('UPDATE ' . OBJECT_FILES_TABLE . ' f SET TableID=(SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path=f.Path) WHERE IsClassFolder=1 AND TableID=0');
			$_db->query('UPDATE ' . OBJECT_FILES_TABLE . ' f SET TableID=(SELECT ID FROM ' . OBJECT_TABLE . ' WHERE f.Path LIKE CONCAT(Path,"/%") ) WHERE IsClassFolder=0 AND IsFolder=1 AND TableID=0');

			//all files without a tableID can be deleted
			$_db->query('DELETE FROM ' . OBJECT_FILES_TABLE . ' WHERE TableID=0');
		}
		return true;
	}

	/* private static function updateVoting(){
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
	  } */



	/*
	  private static function convertTemporaryDoc(){
	  if($GLOBALS['DB_WE']->isColExist(TEMPORARY_DOC_TABLE, 'ID')){
	  $GLOBALS['DB_WE']->query('DELETE FROM ' . TEMPORARY_DOC_TABLE . ' WHERE Active=0');
	  $GLOBALS['DB_WE']->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocTable="tblFile" WHERE DocTable LIKE "%tblFile"');
	  $GLOBALS['DB_WE']->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocTable="tblObjectFiles" WHERE DocTable LIKE "%tblObjectFiles"');
	  $GLOBALS['DB_WE']->delCol(TEMPORARY_DOC_TABLE, 'ID');
	  $GLOBALS['DB_WE']->delKey(TEMPORARY_DOC_TABLE, 'PRIMARY');
	  $GLOBALS['DB_WE']->addKey(TEMPORARY_DOC_TABLE, 'PRIMARY KEY ( `DocumentID` , `DocTable` , `Active` )');
	  }
	  } */

	public static function fixInconsistentTables(){//from backup
		$db = $GLOBALS['DB_WE'];
		$db->query('SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND DID NOT IN(SELECT ID FROM ' . FILE_TABLE . ')
UNION
SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblTemplates" AND DID NOT IN(SELECT ID FROM ' . TEMPLATES_TABLE . ')', true);

		if(($del = $db->getAll(true))){
			$db->query('DELETE FROM ' . LINK_TABLE . ' WHERE CID IN (' . implode(',', $del) . ')');
		}

		$db->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID NOT IN (SELECT CID FROM ' . LINK_TABLE . ')');

		//FIXME: this has to be integrated in we_delete code!
		if(defined('SCHEDULE_TABLE')){
			$db->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE ClassName!="we_objectFile" AND DID NOT IN (SELECT ID FROM ' . FILE_TABLE . ')');

			if(defined('OBJECT_FILES_TABLE')){
				$db->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE ClassName="we_objectFile" AND DID NOT IN (SELECT ID FROM ' . OBJECT_FILES_TABLE . ')');
			}
		}
		//clean customerfilter
		if(defined('CUSTOMER_FILTER_TABLE')){
			$db->query('DELETE FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelTable="tblFile" AND modelId NOT IN (SELECT ID FROM ' . FILE_TABLE . ')');
			$db->query('DELETE FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelTable="tblObjectFiles" AND modelId NOT IN (SELECT ID FROM ' . OBJECT_FILES_TABLE . ')');
		}
		//FIXME: clean inconsistent objects
	}

	public static function updateGlossar(){//from 6340/update6340.php
		//FIXME: remove after 7.0
		if(defined('GLOSSARY_TABLE')){
			foreach($GLOBALS['weFrontendLanguages'] as $lang){
				$cache = new we_glossary_cache($lang);
				$cache->write();
			}
		}
	}

	private static function updateCats(){
		$db = $GLOBALS['DB_WE'];
		//FIXME the next 2 queries are old, remove them after 6.5
		$db->query('UPDATE ' . CATEGORY_TABLE . ' SET Text=Category WHERE Text=""');
		$db->query('UPDATE ' . CATEGORY_TABLE . ' SET Path=CONCAT("/",Category) WHERE Path=""');

		if($db->isColExist(CATEGORY_TABLE, 'Catfields')){
			if(f('SELECT COUNT(1) FROM ' . CATEGORY_TABLE . ' WHERE Title=""') == f('SELECT COUNT(1) FROM ' . CATEGORY_TABLE)){

				$db->query('SELECT ID,Catfields FROM ' . CATEGORY_TABLE . ' WHERE Catfields!="" AND Title="" AND Description=""');
				$udb = new DB_WE();
				while($db->next_record()){
					$data = we_unserialize($db->f('Catfields'));
					if($data){
						$udb->query('UPDATE ' . CATEGORY_TABLE . ' SET ' . we_database_base::arraySetter(array(
								'Title' => $data['default']['Title'],
								'Description' => $data['default']['Description'],
							)) . ' WHERE ID=' . $db->f('ID'));
					}
				}
			}
			$db->delCol(CATEGORY_TABLE, 'Catfields');
		}
	}

	public static function fixHistory($db = null){ //called from 6370/update6370.php
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
		$times[] = $name . ': ' . round($now - $last, 3);
		$last = $now;
	}

	public static function removeObsoleteFiles($path){
		$path = $path ? : WEBEDITION_PATH . 'liveUpdate/includes/';
		if(is_file($path . 'del.files')){
			if(($all = file($path . 'del.files', FILE_IGNORE_NEW_LINES))){
				$delFiles = array();
				foreach($all as $cur){
					if(file_exists(WEBEDITION_PATH . $cur)){
						if(is_file(WEBEDITION_PATH . $cur)){
							$delFiles[] = $cur;
							unlink(WEBEDITION_PATH . $cur);
						} elseif(is_dir(WEBEDITION_PATH . $cur)){
							$delFiles[] = 'Folder: ' . $cur;
							we_base_file::deleteLocalFolder(WEBEDITION_PATH . $cur, false);
						}
					}
				}
			}
			unlink($path . 'del.files');
			file_put_contents($path . 'deleted.files', ($all ? "Deleted Files: " . count($delFiles) . "\n\n" . implode("\n", $delFiles) : "File del.files empty"));
		}

		return true;
	}

	public static function doUpdate(){
		$db = new DB_WE();
		self::meassure('start');
		self::replayUpdateDB();
		self::meassure('replayUpdateDB');

		self::updateUsers($db);
		self::meassure('updateUsers');
		self::updateObjectFilesX();
		self::meassure('updateObjectFilesX');
		/*
		  self::updateVoting();
		  self::meassure('updateVoting');
		  self::convertTemporaryDoc();
		  self::meassure('convertTemporaryDoc');
		 * */
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
		self::meassure(-1);
		self::removeObsoleteFiles();
	}

}
