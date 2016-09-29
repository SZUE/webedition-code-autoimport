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
abstract class we_updater{

	static function replayUpdateDB($specFile = ''){
		//FIXME: even in update, only execute queries on enabled modules
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

	public static function updateUsers($db = null){ //FIXME: remove after 6.5 from 6360/update6300.php
		$db = $db ?: new DB_WE();
		$db->delTable(PREFS_TABLE . '_old');
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
			}
			$db->query('SELECT DISTINCT userID FROM ' . PREFS_TABLE . ' WHERE `key`="Language" AND value=""');
			$users = $db->getAll(true);
			if($users){
				$db->query('UPDATE ' . PREFS_TABLE . ' SET value="Deutsch" WHERE `key`="Language" AND userID IN (' . implode(',', $users) . ')');
				$_SESSION['prefs'] = we_users_user::readPrefs($_SESSION['user']['ID'], $db);
			}
		}
		return true;
	}

	private static function updateObjectFilesX(we_database_base $db = null){
		//FIXME: this takes long, so try to remove this
		if(defined('OBJECT_X_TABLE')){
			//this is from 6.3.9
			$db = $db ?: new DB_WE();

			//change old tables to have different prim key
			$tables = $db->getAllq('SELECT ID FROM ' . OBJECT_TABLE, true);
			foreach($tables as $table){
				if($db->isColExist(OBJECT_X_TABLE . $table, 'ID')){
					$db->delCol(OBJECT_X_TABLE . $table, 'ID');
					//we need to set the key, if sth. is present 2 times, this is an error.
					$db->addKey(OBJECT_X_TABLE . $table, 'PRIMARY KEY (OF_ID)', true);
				}

				//remove old OF_ cols
				if($db->isColExist(OBJECT_X_TABLE . $table, 'OF_ParentID')){
					//remove dummy entry
					$db->query('DELETE FROM ' . OBJECT_X_TABLE . $table . ' WHERE OF_ID=0');
					$db->changeColType(OBJECT_X_TABLE . $table, 'OF_ID', 'INT unsigned NOT NULL');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_ParentID');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_Text');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_Path');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_Url');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_TriggerID');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_Workspaces');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_ExtraWorkspaces');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_ExtraWorkspacesSelected');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_Templates');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_ExtraTemplates');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_Category');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_Published');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_IsSearchable');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_Charset');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_WebUserID');
					$db->delCol(OBJECT_X_TABLE . $table, 'OF_Language');
					$db->delKey(OBJECT_X_TABLE . $table, 'published');
					if($db->isKeyExist(OBJECT_X_TABLE . $table, 'Published')){
						$db->delKey(OBJECT_X_TABLE . $table, 'Published');
					}
					$db->query('SHOW COLUMNS FROM ' . OBJECT_X_TABLE . $table . ' WHERE Field LIKE "checkbox_%" OR Field LIKE "img_%" OR Field LIKE "flashmovie_%" OR Field LIKE "binary_%" OR Field LIKE "country_%" OR Field LIKE "language_%" OR Field LIKE "collection_%" OR Field LIKE "object_%" OR Field LIKE "shopVat_%" OR Field LIKE "date_%"');
					while($db->next_record()){
						$field = $db->f('Field');
						list($type) = explode('_', $field);
						$default = $db->f('Default');
						switch($type){
							case we_objectFile::TYPE_DATE:
								$db->changeColType(OBJECT_X_TABLE . $table, $field, ' INT unsigned NOT NULL ');
								break;
							case we_objectFile::TYPE_COUNTRY:
							case we_objectFile::TYPE_LANGUAGE:
								$db->changeColType(OBJECT_X_TABLE . $table, $field, ' CHAR(2) NOT NULL ');
								break;
							case we_objectFile::TYPE_IMG:
							case we_objectFile::TYPE_FLASHMOVIE:
							case we_objectFile::TYPE_QUICKTIME:
							case we_objectFile::TYPE_BINARY:
							case we_objectFile::TYPE_COLLECTION:
								$db->changeColType(OBJECT_X_TABLE . $table, $field, ' INT unsigned DEFAULT "0" NOT NULL ');
								break;
							case we_objectFile::TYPE_CHECKBOX:
								$db->changeColType(OBJECT_X_TABLE . $table, $field, ' TINYINT unsigned DEFAULT "' . $default . '" NOT NULL ');
								break;
							case we_objectFile::TYPE_OBJECT:
								$db->changeColType(OBJECT_X_TABLE . $table, $field, ' INT unsigned DEFAULT "0" NOT NULL ');
								break;
							case we_objectFile::TYPE_SHOPVAT:
								$db->changeColType(OBJECT_X_TABLE . $table, $field, ' decimal(4,2) default NOT NULL');
								break;
						}
					}
				}
			}
			if(!f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE TableID=0 LIMIT 1')){
				return;
			}
			//correct folder properties
			$db->query('UPDATE ' . OBJECT_FILES_TABLE . ' of SET IsClassFolder=IF(ParentID=0,1,0)');

			//all files should have a tableid
			$db->query('UPDATE ' . OBJECT_FILES_TABLE . ' of SET TableID=(SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path=of.Path) WHERE IsClassFolder=1 AND TableID=0');
			$db->query('UPDATE ' . OBJECT_FILES_TABLE . ' of SET TableID=(SELECT ID FROM ' . OBJECT_TABLE . ' WHERE of.Path LIKE CONCAT(Path,"/%") ) WHERE IsClassFolder=0 AND IsFolder=1 AND TableID=0');

			//all files without a tableID can be deleted
			$db->query('DELETE FROM ' . OBJECT_FILES_TABLE . ' WHERE TableID=0');
		}
		return true;
	}

	private static function upgradeTblLink($db){
		//added in 7.0
		if(f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE nHash=x\'00000000000000000000000000000000\' LIMIT 1')){
			if(version_compare("5.5.3", we_database_base::getMysqlVer(false)) > 1){
				//md5 is binary in mysql <5.5.3
				$db->query('UPDATE ' . LINK_TABLE . ' SET nHash=md5(Name)');
			} else {
				$db->query('UPDATE ' . LINK_TABLE . ' SET nHash=unhex(md5(Name))');
			}
			$db->delKey(LINK_TABLE, 'PRIMARY');
			$db->addKey(LINK_TABLE, 'PRIMARY KEY (DID,DocumentTable,nHash)');
		}

		if(!$db->getPrimaryKeys(LINK_TABLE)){
			$db->addKey(LINK_TABLE, 'INDEX tmpHash(nHash)');
			//unique is not set, we have to make updates
			$db->query('CREATE TABLE IF NOT EXISTS WE_tmp(
  DID int(11) unsigned NOT NULL,
  CID int(11) unsigned NOT NULL,
  DocumentTable tinytext,
	nHash binary(16) NOT NULL,
  KEY DID (DID,DocumentTable(5))
)');
			$db->query('TRUNCATE WE_tmp');
			$db->query('INSERT INTO WE_tmp (SELECT DID,MAX(CID),DocumentTable,nHash FROM ' . LINK_TABLE . ' group by nHash,DID having count(1)>1 )');
			$db->query('DELETE FROM ' . LINK_TABLE . ' WHERE (DID,DocumentTable,nHash) IN (SELECT DID,DocumentTable,nHash FROM WE_tmp) AND CID NOT IN (SELECT CID FROM WE_tmp)');
			//finally delete key
			$db->delKey(LINK_TABLE, 'tmpHash');
			$db->addKey(LINK_TABLE, 'PRIMARY KEY (DID,DocumentTable,nHash)');
			$db->delTable('WE_tmp');
		}
	}

	/* 	private function correctTblFile(we_database_base $db){
	  //added in 7.0.1
	  if(!$db->isKeyExist(FILE_TABLE, 'ParentID', array('ParentID', 'Filename', 'Extension'), 'UNIQUE KEY')){
	  if($db->isKeyExistAtAll(FILE_TABLE, 'Path')){
	  $db->delKey(FILE_TABLE, 'Path');
	  }
	  //we need an temporary key
	  $db->addKey(FILE_TABLE, 'KEY Path(Path)');
	  //first cleanup really invalid entries
	  $db->query('DELETE FROM ' . FILE_TABLE . ' WHERE CreationDate=0 AND NOT EXISTS (SELECT * FROM ' . LINK_TABLE . ' WHERE DID=ID AND DocumentTable="tblFile")');
	  $safeDel = $db->getAllq('SELECT f1.ID FROM ' . FILE_TABLE . ' f1 JOIN ' . FILE_TABLE . ' f2 ON f1.Path=f2.Path WHERE f1.ID!=f2.ID AND NOT EXISTS (SELECT * FROM ' . LINK_TABLE . ' WHERE DID=f1.ID AND DocumentTable="tblFile") AND EXISTS (SELECT * FROM ' . LINK_TABLE . ' WHERE DID=f2.ID AND DocumentTable="tblFile")', true);
	  if($safeDel){
	  $db->query('DELETE FROM ' . FILE_TABLE . ' WHERE ID IN (' . implode(',', $safeDel) . ')');
	  }

	  $ids = $db->getAllq('SELECT f1.ID FROM ' . FILE_TABLE . ' f1 JOIN ' . FILE_TABLE . ' f2 ON f1.Path=f2.Path WHERE f1.ID!=f2.ID AND NOT EXISTS (SELECT * FROM ' . LINK_TABLE . ' WHERE DID=f1.ID AND DocumentTable="tblFile") AND EXISTS (SELECT * FROM ' . LINK_TABLE . ' WHERE DID=f2.ID AND DocumentTable="tblFile")');
	  if($ids){
	  $db->query('DELETE FROM ' . FILE_TABLE . ' WHERE ID IN (' . implode(',', $ids) . ')');
	  }

	  //check if we still have bad entries
	  $problems = $db->getAllq('SELECT f1.ID AS fail,f1.Path AS `Name1`,f2.ID as other,f2.Path AS `Name2`,BINARY f1.Path=BINARY f2.Path AS `binEq`,EXISTS (SELECT * FROM ' . LINK_TABLE . ' WHERE DID=f1.ID AND DocumentTable="tblFile") AS origOk,EXISTS (SELECT * FROM ' . LINK_TABLE . ' WHERE DID=f2.ID AND DocumentTable="tblFile") AS otherOk,f2.ContentType,f1.CreationDate AS `create1`,f2.CreationDate AS `create2`,f1.ModDate AS `mod1`,f2.ModDate AS `mod2` FROM ' . FILE_TABLE . ' f1 JOIN ' . FILE_TABLE . ' f2 ON f1.Path=f2.Path WHERE f1.ID<f2.ID');

	  if($problems){
	  t_e('we can\'t upgrade table due to file-list', $problems);
	  } else {
	  //finally add a new unique key, del temp index
	  $db->addKey(FILE_TABLE, 'UNIQUE KEY ParentID(ParentID,Filename,Extension)');
	  }
	  }
	  }
	 */

	public static function fixInconsistentTables(we_database_base $db = null){//from backup
		$db = $db ?: $GLOBALS['DB_WE'];
		$db->query('SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND DID NOT IN(SELECT ID FROM ' . FILE_TABLE . ')
UNION
SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblTemplates" AND DID NOT IN(SELECT ID FROM ' . TEMPLATES_TABLE . ')
UNION
SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND Type="href" AND Name LIKE "%_intPath"
UNION
SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND Type="txt" AND Name="RollOverPath"
UNION
SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND Type="object" AND Name LIKE "%_path"
', true);

		if(($del = $db->getAll(true))){
			$db->query('DELETE FROM ' . LINK_TABLE . ' WHERE CID IN (' . implode(',', $del) . ')');
		}
		self::upgradeTblLink($db);
		//self::correctTblFile($db);

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

	private static function updateCats(we_database_base $db = null){
		$db = $db ?: $GLOBALS['DB_WE'];

		if($db->isColExist(CATEGORY_TABLE, 'Catfields')){
			if(f('SELECT COUNT(1) FROM ' . CATEGORY_TABLE . ' WHERE Title=""') == f('SELECT COUNT(1) FROM ' . CATEGORY_TABLE)){

				$db->query('SELECT ID,Catfields FROM ' . CATEGORY_TABLE . ' WHERE Catfields!="" AND Title="" AND Description=""');
				$udb = new DB_WE();
				while($db->next_record()){
					$data = we_unserialize($db->f('Catfields'));
					if($data){
						$udb->query('UPDATE ' . CATEGORY_TABLE . ' SET ' . we_database_base::arraySetter(['Title' => $data['default']['Title'],
								'Description' => $data['default']['Description'],
							]) . ' WHERE ID=' . $db->f('ID'));
					}
				}
			}
			$db->delCol(CATEGORY_TABLE, 'Catfields');
		}
	}

	public static function fixHistory(we_database_base $db = null){ //called from 6370/update6370.php
		return;
	}

	public static function meassure($name){
		static $last = 0;
		static $times = [];
		$last = $last ?: microtime(true);
		if($name == -1){
			t_e('notice', 'time for updates', $times);
			return;
		}
		$now = microtime(true);
		$times[] = $name . ': ' . round($now - $last, 3);
		$last = $now;
	}

	public static function removeObsoleteFiles($path = ''){
		$path = $path ?: WEBEDITION_PATH . 'liveUpdate/includes/';
		if(!is_file($path . 'del.files')){
			return true;
		}
		if(($all = file($path . 'del.files', FILE_IGNORE_NEW_LINES))){
			$delFiles = [];
			foreach($all as $cur){
				$recursive = false;
				if($cur{0} === '!'){
					$cur = substr($cur, 1);
					$recursive = true;
				}
				if(file_exists(WEBEDITION_PATH . $cur)){
					if(is_file(WEBEDITION_PATH . $cur)){
						$delFiles[] = $cur;
						unlink(WEBEDITION_PATH . $cur);
					} elseif(is_dir(WEBEDITION_PATH . $cur)){
						$delFiles[] = 'Folder: ' . $cur;
						we_base_file::deleteLocalFolder(WEBEDITION_PATH . $cur, $recursive);
					}
				}
			}
		}
		unlink($path . 'del.files');
		file_put_contents($path . 'deleted.files', ($all ? "Deleted Files: " . count($delFiles) . "\n\n" . implode("\n", $delFiles) : "File del.files empty"));

		return true;
	}

	public static function updateContentTable(we_database_base $db){
		//we only add hashes here, nothing more
		if(!f('SELECT COUNT(1) FROM ' . CONTENT_TABLE . ' WHERE Dat IS NOT NULL AND dHash=x\'00000000000000000000000000000000\'')){
			return;
		}

		if(version_compare("5.5.3", we_database_base::getMysqlVer(false)) > 1){
			//md5 is binary in mysql <5.5.3
			$db->query('UPDATE ' . CONTENT_TABLE . ' SET dHash=md5(Dat) WHERE Dat IS NOT NULL AND dHash=x\'00000000000000000000000000000000\'');
		} else {
			$db->query('UPDATE ' . CONTENT_TABLE . ' SET dHash=unhex(md5(Dat)) WHERE Dat IS NOT NULL AND dHash=x\'00000000000000000000000000000000\'');
		}
		return;
//the latter will be enabled in future releases
		//FIXME: change tabledefinition of content table
		define('CONTENT_TABLEx', CONTENT_TABLE . 'XX');
		define('LINK_TABLEx', LINK_TABLE . 'xx');

		//eleminate duplicates

		$db->query('CREATE TABLE IF NOT EXISTS WE_tmp (
  `ID` int(10) unsigned NOT NULL,
  `hash` binary(16) NOT NULL,
  `BDID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY (`hash`),
  KEY(`BDID`)
	)');
		$db->query('TRUNCATE WE_tmp');

		$db->query('INSERT INTO WE_tmp (ID,hash,BDID) SELECT ID,hash,BDID FROM ' . CONTENT_TABLEx . ' WHERE 1 GROUP BY hash,BDID,Dat HAVING COUNT(1)>1 ');
		//check if we have clashes
		if(f('SELECT 1 FROM WE_tmp GROUP BY hash,BDID HAVING COUNT(1)>1')){
			//we can't reduce, this will be complex if the time is limited
			return;
		}

		//we must change update - this will take too long!
		return;

		//this will not work due to indices on tblLink
		$db->query('UPDATE LOW_PRIORITY ' . LINK_TABLEx . ' l JOIN ' . CONTENT_TABLEx . ' c ON l.CID=c.ID JOIN WE_tmp t ON c.hash=t.hash SET l.CID=t.ID');
		$db->query('DELETE FROM ' . CONTENT_TABLEx . ' WHERE ID NOT IN (SELECT CID FROM ' . LINK_TABLEx . ')');
		$db->delTable('WE_tmp');
	}

	private static function updateDateInContent(we_database_base $db){
		$db->query('UPDATE ' . CONTENT_TABLE . ' SET dHash=x\'00\',BDID=Dat,Dat=NULL WHERE ID IN (SELECT CID FROM ' . LINK_TABLE . ' WHERE DocumentTable="tblFile" AND type="date") AND Dat IS NOT NULL AND dHash!=x\'00\'');
	}

	private static function updateVersionsTable(we_database_base $db){
		if(!f('SELECT 1 FROM ' . VERSIONS_TABLE . ' WHERE binaryPath LIKE "' . WEBEDITION_DIR . '%" LIMIT 1')){
			return;
		}
		$db->query('UPDATE ' . VERSIONS_TABLE . ' SET binaryPath=REPLACE(binaryPath,"' . VERSION_DIR . '","") WHERE binaryPath LIKE "' . WEBEDITION_DIR . '%"');
	}

	private static function cleanUnreferencedVersions(we_database_base $db){
		$all = [];
		$d = dir(rtrim($_SERVER['DOCUMENT_ROOT'] . VERSION_DIR, '/'));
		while($d && false !== ($entry = $d->read())){
			switch($entry){
				case '.':
				case '..':
					break;
				default:
					$all[] = $entry;
			}
		}

		$db->addTable('tmp', ['b' => 'varchar(255) NOT NULL'], ['KEY b (b)'], 'MYISAM', true);
		$db->query('INSERT INTO tmp VALUES ("' . implode('"),("', $all) . '")');
		//we add a limit since this file might not be executed to the end
		$db->query('SELECT b FROM tmp LEFT JOIN ' . VERSIONS_TABLE . ' ON b=binaryPath WHERE ID IS NULL LIMIT 1000');
		$all = $db->getAll(true);
		foreach($all as $cur){
			if($cur && $cur != '/'){
				we_base_file::delete($_SERVER['DOCUMENT_ROOT'] . VERSION_DIR . $cur);
			}
		}
		$db->delTable('tmp', true);
	}

	public static function updateGlossar(){
		if(defined('WE_GLOSSARY_MODULE_PATH') && is_dir(WE_GLOSSARY_MODULE_PATH . 'cache')){
			foreach(glob(WE_GLOSSARY_MODULE_PATH . 'cache/cache_*' . '.php') as $file){
				$name = str_replace('cache_', '', basename($file, '.php'));
				rename($file, we_glossary_cache::cacheIdToFilename($name));
			}
			we_base_file::deleteLocalFolder(WE_GLOSSARY_MODULE_PATH . 'cache', true, true);
		}
	}

	public static function updateCustomerFilters(we_database_base $db){
		$db->query("SELECT ID,CustomerFilter,WhiteList,BlackList,Customers FROM " . NAVIGATION_TABLE . " WHERE CustomerFilter LIKE 'a:%{i:%'");
		$all = $db->getAll();
		foreach($all as $a){
			$db->query('UPDATE ' . NAVIGATION_TABLE . ' SET ' . we_database_base::arraySetter([
					'CustomerFilter' => we_serialize(we_unserialize($a['CustomerFilter']), SERIALIZE_JSON),
					'WhiteList' => trim($a['WhiteList'], ','),
					'BlackList' => trim($a['BlackList'], ','),
					'Customers' => trim($a['Customers'], ','),
				]) . ' WHERE ID=' . $a['ID']);
		}
		if(defined('CUSTOMER_FILTER_TABLE')){
			$db->query("SELECT modelId,filter,whiteList,blackList,specificCustomers FROM " . CUSTOMER_FILTER_TABLE . " WHERE filter LIKE 'a:%{i:%'");
			$all = $db->getAll();
			foreach($all as $a){
				$db->query('UPDATE ' . CUSTOMER_FILTER_TABLE . ' SET ' . we_database_base::arraySetter([
						'filter' => we_serialize(we_unserialize($a['filter']), SERIALIZE_JSON),
						'whiteList' => trim($a['whiteList'], ','),
						'blackList' => trim($a['blackList'], ','),
						'specificCustomers' => trim($a['specificCustomers'], ','),
					]) . ' WHERE modelId=' . $a['modelId']);
			}
		}
	}

	private static function updateShop(we_database_base $db){
		//convert 1st gen values
		if(($zw = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="weShopStatusMails" AND pref_value LIKE "%weShopStatusMails%"', '', $db))){
			$zw = we_unserialize(
				strtr($zw, [
				'O:17:"weShopStatusMails":' => 'O:19:"we_shop_statusMails":',
				'O:17:"weshopstatusmails":' => 'O:19:"we_shop_statusMails":',
				])
			);
			$db->query('UPDATE ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter([
					'pref_value' => we_serialize((array) $zw, SERIALIZE_JSON)
				]) . ' WHERE tool="shop" AND pref_name="weShopStatusMails"');
		}

		if(($zw = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="weShopVatRule" AND pref_value LIKE "%weShopVatRule%"', '', $db))){
			$zw = we_unserialize(
				strtr($zw, [
				'O:13:"weShopVatRule":' => 'O:15:"we_shop_vatRule":',
				'O:13:"weshopvatrule":' => 'O:15:"we_shop_vatRule":'
				])
			);
			$db->query('UPDATE ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter([
					'pref_value' => we_serialize((array) $zw, SERIALIZE_JSON)
				]) . ' WHERE tool="shop" AND pref_name="weShopVatRule"');
		}
//convert 2nd gen values
		if(($zw = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="weShopStatusMails" AND pref_value LIKE "%we_shop_statusMails%"', '', $db))){
			$db->query('UPDATE ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter([
					'pref_value' => we_serialize((array) we_unserialize($zw), SERIALIZE_JSON)
				]) . ' WHERE tool="shop" AND pref_name="weShopStatusMails"');
		}

		if(($zw = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="weShopVatRule" AND pref_value LIKE "%we_shop_vatRule%"', '', $db))){
			$db->query('UPDATE ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter([
					'pref_value' => we_serialize((array) we_unserialize($zw), SERIALIZE_JSON)
				]) . ' WHERE tool="shop" AND pref_name="weShopVatRule"');
		}
	}

	private static function updateSetting(we_database_base $db){
		$items = $db->getAllq('SELECT * FROM ' . SETTINGS_TABLE . ' WHERE pref_value LIKE "a:%"');
		foreach($items as $item){
			$db->query('UPDATE ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter([
					'pref_value' => we_serialize(we_unserialize($item['pref_value']), SERIALIZE_JSON)
				]) . ' WHERE tool="' . $item['tool'] . '" AND pref_name="' . $item['pref_name'] . '"');
		}
	}

	private static function updateShop2(we_database_base $db, $pos = 0){
		if(!$db->isTabExist(SHOP_TABLE)){
			return;
		}
		$db = new DB_WE();
		$db2 = $GLOBALS['DB_WE'];
		$max = f('SELECT COUNT(DISTINCT IntOrderID) FROM ' . SHOP_TABLE);
		$maxStep = 150;

		if($pos > $max){//finished
			$db->delTable(SHOP_TABLE . '_old');
			$db->query('RENAME TABLE ' . SHOP_TABLE . ' TO ' . SHOP_TABLE . '_old');
			return false;
		}
		if($pos == 0){
			//clean new tables
			$db->query('TRUNCATE TABLE ' . SHOP_ORDER_DATES_TABLE);
			$db->query('TRUNCATE TABLE ' . SHOP_ORDER_DOCUMENT_TABLE);
			$db->query('TRUNCATE TABLE ' . SHOP_ORDER_ITEM_TABLE);
			$db->query('TRUNCATE TABLE ' . SHOP_ORDER_TABLE);
			//make sure we have at least the last Order in the new table

			$db->query('INSERT IGNORE INTO ' . SHOP_ORDER_TABLE . ' SET ID=(SELECT MAX(IntOrderID) FROM ' . SHOP_TABLE . ')');
		}
		//prefill as much as possible

		$ids = implode(',', $db->getAllq('SELECT IntOrderID FROM ' . SHOP_TABLE . ' GROUP BY IntOrderID LIMIT ' . $pos . ',' . $maxStep, true));

		$db->query('REPLACE INTO ' . SHOP_ORDER_TABLE . ' (ID,shopname,customerID,DateOrder,DateConfirmation,DateShipping,DatePayment,DateCancellation,DateFinished) (SELECT IntOrderID,shopname,IntCustomerID,DateOrder,DateConfirmation,DateShipping,DatePayment,DateCancellation,DateFinished FROM ' . SHOP_TABLE . ' WHERE IntOrderID IN(' . $ids . ') GROUP BY IntOrderID)');

		//fill in dates
		foreach(['MailConfirmation', 'MailShipping', 'MailPayment', 'MailCancellation', 'MailFinished',
		'DateCustomA', 'DateCustomB', 'DateCustomC', 'DateCustomD', 'DateCustomE', 'DateCustomF', 'DateCustomG', 'DateCustomH', 'DateCustomI', 'DateCustomJ',
		'MailCustomA', 'MailCustomB', 'MailCustomC', 'MailCustomD', 'MailCustomE', 'MailCustomF', 'MailCustomG', 'MailCustomH', 'MailCustomI', 'MailCustomJ'] as $date){
			$db->query('REPLACE INTO ' . SHOP_ORDER_DATES_TABLE . ' (ID,type,date) (SELECT IntOrderID,"' . $date . '",' . $date . ' FROM ' . SHOP_TABLE . ' WHERE ' . $date . ' IS NOT NULL AND IntOrderID IN(' . $ids . ') GROUP BY IntOrderID)');
		}

		//fill the rest of the order itself
		$db->query('SELECT IntOrderID,strSerialOrder FROM ' . SHOP_TABLE . ' WHERE IntOrderID IN(' . $ids . ') GROUP BY IntOrderID');

		while($db->next_record(MYSQL_ASSOC)){
			$dat = we_unserialize($db->f('strSerialOrder'));

			$customer = $dat['we_shopCustomer'];
			unset($customer['Password'], $customer['_Password'], $customer['ID'], $customer['Username'], $customer['LoginDenied'], $customer['MemberSince'], $customer['LastLogin'], $customer['LastAccess'], $customer['AutoLoginDenied'], $customer['AutoLogin'], $customer['ModifyDate'], $customer['ModifiedBy'], $customer['Path'], $customer['Newsletter_Ok'], $customer['registered'], $customer['AutoLoginID']
			);
			$db2->query('UPDATE ' . SHOP_ORDER_TABLE . ' SET ' . we_database_base::arraySetter([
					'pricesNet' => intval($dat['we_shopPriceIsNet']),
					'priceName' => $dat['we_shopPricename'],
					'shippingCost' => $dat['we_shopPriceShipping']['costs'],
					'shippingNet' => $dat['we_shopPriceShipping']['isNet'],
					'shippingVat' => $dat['we_shopPriceShipping']['vatRate'],
					'calcVat' => empty($dat['we_shopCalcVat']) ? 1 : $dat['we_shopCalcVat'],
					'customFields' => $dat['we_sscf'] ? we_serialize($dat['we_sscf'], SERIALIZE_JSON, false, 0, true) : sql_function('NULL'),
					'customerData' => we_serialize($customer, SERIALIZE_JSON, false, 5, true),
				]) . ' WHERE ID=' . $db->f('IntOrderID'));
		}

		//fill in order items
		$db->query('SELECT IntOrderID,Price,IntQuantity,strSerial FROM ' . SHOP_TABLE . ' WHERE IntOrderID IN (' . $ids . ')');
		while($db->next_record(MYSQL_ASSOC)){
			$tmp = we_unserialize($db->f('strSerial'));
			$dat = array_filter($tmp, function($k){
				return !is_numeric($k);
			}, ARRAY_FILTER_USE_KEY);
			$docid = intval(isset($dat['OF_ID']) ? $dat['OF_ID'] : $dat['ID']);
			$pub = intval(empty($dat['we_wedoc_Published']) ? $dat['WE_Published'] : $dat['we_wedoc_Published']);
			$type = (!empty($dat['we_wedoc_ContentType'] && $dat['we_wedoc_ContentType'] == we_base_ContentTypes::OBJECT_FILE) ? 'object' : 'document');
			$variant = $dat['WE_VARIANT'];

			$id = f('SELECT ID FROM ' . SHOP_ORDER_DOCUMENT_TABLE . ' WHERE DocID=' . $docid . ' AND type="' . $type . '" AND variant="' . $db->escape($variant) . '" AND Published=FROM_UNIXTIME(' . $pub . ')');
			if(!$id){
				$data = $dat;
				unset($data['we_shoptitle'], $data['we_shopdescription'], $data['we_sacf'], $data['shopvat'], $data['shopcategory'], $data['WE_VARIANT']);
				//add document first
				$db2->query('REPLACE INTO ' . SHOP_ORDER_DOCUMENT_TABLE . ' SET ' . we_database_base::arraySetter([
						'DocID' => $docid,
						'type' => $type,
						'variant' => $variant,
						'Published' => sql_function('FROM_UNIXTIME(' . $pub . ')'),
						'title' => strip_tags($dat['we_shoptitle']),
						'description' => strip_tags($dat['we_shopdescription']),
						'CategoryID' => empty($dat['shopcategory']) ? 0 : intval($dat['shopcategory']),
						'SerializedData' => we_serialize($data, SERIALIZE_JSON, false, 5, true)
				]));
				$id = $db2->getInsertId();
			}

			$db2->query('INSERT INTO ' . SHOP_ORDER_ITEM_TABLE . ' SET ' . we_database_base::arraySetter([
					'orderID' => $db->f('IntOrderID'),
					'orderDocID' => $id,
					'quantity' => $db->f('IntQuantity'),
					'Price' => $db->f('Price'),
					'customFields' => $dat['we_sacf'] ? we_serialize($dat['we_sacf'], SERIALIZE_JSON, false, 0, true) : sql_function('NULL'),
					'Vat' => isset($dat['shopvat']) ? $dat['shopvat'] : sql_function('NULL'),
			]));
		}
		return ['text' => 'Shop ' . $pos . ' / ' . $max, 'pos' => ($pos + $maxStep)];
		//what about variants?! how do they apply?
		//old not used: IntPayment_Type
		//FIXME strSerial may contain we_wedoc_ & OF_... & may contain numeric entries
		/* 	 */
	}

	public static function doUpdate($what = '', $pos = 0){
		$db = new DB_WE();
		self::meassure('start');
		//if we are in liveupdate, initial db updates already triggered
		if($what == 'internal'){
			self::replayUpdateDB();
			self::meassure('replayUpdateDB');
			$what = 'all';
		}
		$pos = intval($pos);
		switch($what){
			default:
			case '':
				self::updateUsers($db);
				self::meassure('updateUsers');
				if(defined('OBJECT_X_TABLE')){
					self::updateObjectFilesX($db);
					self::meassure('updateObjectFilesX');
				}
				self::fixInconsistentTables($db);
				self::meassure('fixInconsistentTables');
				if(defined('WE_GLOSSARY_MODULE_PATH')){
					self::updateGlossar();
					self::meassure('updateGlossar');
				}
				self::updateCats($db);
				self::meassure('updateCats');
				/* self::fixHistory();
				  self::meassure('fixHistory'); */
				self::updateContentTable($db);
				self::meassure('updateContent');
				self::updateDateInContent($db);
				self::meassure('updateContentDate');
				self::updateVersionsTable($db);
				self::meassure('versions');
				self::cleanUnreferencedVersions($db);
				self::meassure('fixVersions');
				self::updateCustomerFilters($db);
				self::meassure('customerFilter');
				self::updateSetting($db);
				self::meassure('setting');
				if(defined('SHOP_ORDER_TABLE')){
					self::updateShop($db);
					self::meassure('shop');
				}
			case 'shop':
				$what = 'shop';
				if(defined('SHOP_TABLE')){
					$ret = self::updateShop2($db, $pos);
					if($ret){
						self::meassure(-1);
						return array_merge($ret, ['what' => $what]);
					}
					self::meassure('shop');
				}

				self::replayUpdateDB();
				self::meassure('replayUpdateDB');
				self::meassure(-1);
				self::removeObsoleteFiles();
		}
	}

}
