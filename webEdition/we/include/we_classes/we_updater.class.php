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
define('LINK_TABLE', TBL_PREFIX . 'tblLink');

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

	private static function updateObjectFilesX(we_database_base $db = null, array $progress = []){
		if(!defined('OBJECT_X_TABLE')){
			return false;
		}
		//this is from 6.3.9
		$db = $db ?: new DB_WE();
		$tmpDB = new DB_WE();
		$init = $progress = ($progress ?: [
			'pos' => 0,
			'maxID' => 0,
			'max' => f('SELECT COUNT(1) FROM ' . OBJECT_TABLE . ' WHERE DefaultValues LIKE "a:%"')
		]);

		$maxStep = 15;

		if(!$progress['max'] || ($progress['pos'] > $progress['max'])){//finished
			$db->delCol(OBJECT_TABLE, 'strOrder');
			if(!f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE TableID=0 LIMIT 1')){
				return false;
			}
			//correct folder properties
			$db->query('UPDATE ' . OBJECT_FILES_TABLE . ' of SET IsClassFolder=IF(ParentID=0,1,0)');

			//all files should have a tableid
			$db->query('UPDATE ' . OBJECT_FILES_TABLE . ' of SET TableID=(SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path=of.Path) WHERE IsClassFolder=1 AND TableID=0');
			$db->query('UPDATE ' . OBJECT_FILES_TABLE . ' of SET TableID=(SELECT ID FROM ' . OBJECT_TABLE . ' WHERE of.Path LIKE CONCAT(Path,"/%") ) WHERE IsClassFolder=0 AND IsFolder=1 AND TableID=0');

			//all files without a tableID can be deleted
			$db->query('DELETE FROM ' . OBJECT_FILES_TABLE . ' WHERE TableID=0');
			return false;
		}

		$db->query('SELECT ID,DefaultValues,UsersReadOnly FROM ' . OBJECT_TABLE . ' WHERE DefaultValues LIKE "a:%" AND ID>' . intval($init['maxID']) . ' ORDER BY ID LIMIT ' . $maxStep);
		while($db->next_record(MYSQL_ASSOC)){
			$data = we_unserialize($db->f('DefaultValues'));
			$users = we_unserialize($db->f('UsersReadOnly'));
			foreach($data as &$d){
				if(is_array($d)){
					$d = array_filter($d);
					unset($d['intPath']);
				}
			}
			$tmpDB->query('UPDATE ' . OBJECT_TABLE . ' SET ' . we_database_base::arraySetter([
						'DefaultValues' => we_serialize($data, SERIALIZE_JSON),
						'UsersReadOnly' => implode(',', $users)
					]) . ' WHERE ID=' . $db->f('ID'));
		}
		if($db->f('ID')){
			$progress['maxID'] = $db->f('ID');
		}
		//make sure we always progress
		$progress['pos'] += ($db->num_rows() ?: 1);

		//change old tables to have different prim key
		$tables = $db->getAllq('SELECT ID FROM ' . OBJECT_TABLE . ' AND ID>' . intval($init['maxID']) . ' ORDER BY ID LIMIT ' . $maxStep, true);
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
				if($db->isKeyExistAtAll(OBJECT_X_TABLE . $table, 'published')){
					$db->delKey(OBJECT_X_TABLE . $table, 'published');
				}
				if($db->isKeyExistAtAll(OBJECT_X_TABLE . $table, 'Published')){
					$db->delKey(OBJECT_X_TABLE . $table, 'Published');
				}
				$db->query('SHOW COLUMNS FROM ' . OBJECT_X_TABLE . $table . ' WHERE Field LIKE "checkbox_%" OR Field LIKE "img_%" OR Field LIKE "flashmovie_%" OR Field LIKE "binary_%" OR Field LIKE "country_%" OR Field LIKE "language_%" OR Field LIKE "collection_%" OR Field LIKE "object_%" OR Field LIKE "shopVat_%" OR Field LIKE "date_%" OR Field LIKE "link_%" OR Field LIKE "href_%"');
				$entries = $db->getAll();
				$changes = [];
				foreach($entries as $entry){
					$field = $entry['Field'];
					list($type) = explode('_', $field);
					$default = $entry['Default'];
					switch($type){
						case we_objectFile::TYPE_DATE:
							$changes[$field] = ' INT unsigned NOT NULL ';
							break;
						case we_objectFile::TYPE_COUNTRY:
						case we_objectFile::TYPE_LANGUAGE:
							$changes[$field] = ' CHAR(2) NOT NULL ';
							break;
						case we_objectFile::TYPE_LINK:
						case we_objectFile::TYPE_HREF:
							$changes[$field] = ' TINYTEXT NOT NULL ';
							break;
						case we_objectFile::TYPE_IMG:
						case we_objectFile::TYPE_FLASHMOVIE:
						case we_objectFile::TYPE_QUICKTIME:
						case we_objectFile::TYPE_BINARY:
						case we_objectFile::TYPE_COLLECTION:
							$changes[$field] = ' INT unsigned DEFAULT "0" NOT NULL ';
							break;
						case we_objectFile::TYPE_CHECKBOX:
							$changes[$field] = ' TINYINT unsigned DEFAULT "' . $default . '" NOT NULL ';
							break;
						case we_objectFile::TYPE_OBJECT:
							$changes[$field] = ' INT unsigned DEFAULT "0" NOT NULL ';
							break;
						case we_objectFile::TYPE_SHOPVAT:
							$changes[$field] = ' decimal(4,2) default NOT NULL';
							break;
					}
				}
				if($changes){
					$query = '';
					foreach($changes as $field => $change){
						$query .= ($query ? ',' : '') . ' MODIFY `' . $field . '` ' . $change;
					}
					$db->query('ALTER TABLE `' . OBJECT_X_TABLE . $table . '` ' . $query);
					$db->query('OPTIMIZE TABLE `' . OBJECT_X_TABLE . $table . '`');
				}

				if(($sort = f('SELECT strOrder FROM ' . OBJECT_TABLE . ' WHERE ID=' . $table))){
					$ctable = OBJECT_X_TABLE . $table;
					$tableInfo = $db->metadata($ctable, we_database_base::META_NAME);
					$sort = [];
					$i = 0;
					foreach($tableInfo as $name){
						list($type, $name) = explode('_', $name, 2);
						switch($type){
							case 'OF':
							case 'variant':
								break;
							default:
								$sort[$name] = $sort[$i];
								$i++;
						}
					}
					asort($sort, SORT_NUMERIC);
					$last = 'OF_ID';
					foreach(array_keys($sort) as $value){
						$db->moveCol($ctable, $value, $last);
						$last = $value;
					}

					$db->query('UPDATE ' . OBJECT_TABLE . ' SET strOrder="" WHERE ID=' . $table);
				}
			}
		}
		return array_merge($progress, ['text' => 'Classes ' . $progress['pos'] . ' / ' . $progress['max']]);
	}

	private static function upgradeTblFileLink(we_database_base $db){
		//added in 7.1
		if($db->isColExist(FILELINK_TABLE, 'element')){
			if(version_compare("5.5.3", we_database_base::getMysqlVer(false)) > 1){
				//md5 is binary in mysql <5.5.3
				$db->query('UPDATE ' . FILELINK_TABLE . ' SET nHash=md5(element) WHERE element!=""');
			} else {
				$db->query('UPDATE ' . FILELINK_TABLE . ' SET nHash=unhex(md5(element)) WHERE element!=""');
			}
			$db->delCol(FILELINK_TABLE, 'element');
			$db->delKey(FILELINK_TABLE, 'PRIMARY');
			$db->addKey(FILELINK_TABLE, 'PRIMARY KEY (ID,DocumentTable,`type`,remObj,nHash,`position`,isTemp)');
		}
	}

	private static function upgradeTblLink(we_database_base $db){
		//added in 7.0
		if(f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE nHash=x\'00000000000000000000000000000000\' LIMIT 1')){
			$db->query('UPDATE ' . LINK_TABLE . ' SET nHash=unhex(md5(Name))');
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
			$db->query('INSERT INTO WE_tmp (SELECT DID,MAX(CID),DocumentTable,nHash FROM ' . LINK_TABLE . ' group by nHash,DID having COUNT(1)>1 )');
			$db->query('DELETE FROM ' . LINK_TABLE . ' WHERE (DID,DocumentTable,nHash) IN (SELECT DID,DocumentTable,nHash FROM WE_tmp) AND CID NOT IN (SELECT CID FROM WE_tmp)');
			//finally delete key
			$db->delKey(LINK_TABLE, 'tmpHash');
			$db->addKey(LINK_TABLE, 'PRIMARY KEY (DID,DocumentTable,nHash)');
			$db->delTable('WE_tmp');
		}
	}

	public static function fixInconsistentTables(we_database_base $db = null){//from backup
		$db = $db ?: $GLOBALS['DB_WE'];

		if($db->isTabExist(LINK_TABLE)){
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
			$db->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID NOT IN (SELECT CID FROM ' . LINK_TABLE . ')');
		}

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

		$db->query('DELETE FROM ' . CAPTCHADEF_TABLE . ' WHERE ID NOT IN (SELECT ID FROM ' . TEMPLATES_TABLE . ')');
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

	public static function updateContentTable(we_database_base $db, array $progress = []){
		$init = $progress = ($progress ?: [
			'pos' => 0,
			'maxID' => 0,
			'max' => f('SELECT COUNT(1) FROM ' . CONTENT_TABLE . ' WHERE nHash=x\'00000000000000000000000000000000\'')
		]);
		$maxStep = 100;

		//FIXME!!!! LINK_TABLE
		if(!$progress['max'] || ($progress['pos'] > $progress['max'])){//finished
			$db->addKey(CONTENT_TABLE, 'UNIQUE KEY prim(DID,DocumentTable,nHash)');
			return false;
		}

		$tmp = ' FROM ' . LINK_TABLE . ' l WHERE l.CID=c.ID';
		$db->query('UPDATE ' . CONTENT_TABLE . ' c SET c.DID=(SELECT l.DID' . $tmp . '),c.Type=(SELECT l.Type' . $tmp . '),c.Name=(SELECT l.Name' . $tmp . '),c.nHash=(SELECT l.nHash' . $tmp . '),c.DocumentTable=(SELECT l.DocumentTable' . $tmp . ') WHERE c.ID>' . $init['maxID'] . ' ORDER BY c.ID LIMIT ' . $maxStep);

		$progress['maxID'] = f('SELECT MIN(ID) FROM ' . CONTENT_TABLE . ' WHERE nHash=x\'00000000000000000000000000000000\'');
		$progress['pos'] = max($progress['pos'] + $maxStep, $progress['max']);

		return array_merge($progress, ['text' => 'Content ' . $progress['pos'] . ' / ' . $progress['max']]);
	}

	private static function updateDateInContent(we_database_base $db){
		$db->query('UPDATE ' . CONTENT_TABLE . ' SET BDID=Dat,Dat=NULL WHERE DocumentTable="tblFile" AND type="date" AND Dat IS NOT NULL');
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
		if(defined('WE_GLOSSARY_MODULE_PATH')){

		}
	}

	public static function updateCustomer(we_database_base $db){
		$order = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="EditSort"');
		if(!$order){
			return;
		}
		$order = makeArrayFromCSV($order);
		$meta = $db->metadata(CUSTOMER_TABLE);
		$defaultCols = ['ID', 'Password', 'Forename', 'Surname', 'LoginDenied', 'MemberSince', 'LastLogin', 'LastAccess', 'AutoLogin', 'AutoLoginDenied', 'ModifyDate',
			'ModifiedBy', 'Username',];
		$last = 'FIRST';
		foreach($defaultCols as $col){
			$db->moveCol(CUSTOMER_TABLE, $col, $last);
			$last = $col;
		}


		$newOrder = [];
		foreach($meta as $i => $col){
			if(!in_array($col, $defaultCols)){
				$newOrder[$col] = $order[$i];
			}
		}
		asort($newOrder, SORT_NUMERIC);
		//now we have the defined order
		//sort by groups again
		$groups = [
			'' => []
		];
		foreach(array_keys($newOrder) as $col){
			$tmp = explode('_', $col, 2);
			if(empty($tmp[1])){
				array_unshift($tmp, '');
			}
			$groups[$tmp[0]][] = $tmp[1];
		}
		$last = 'Username';
		foreach($groups as $group => $entries){
			foreach($entries as $entry){
				$name = ($group === '' ? '' : $group . '_') . $entry;
				$db->moveCol(CUSTOMER_TABLE, $name, $last);
				$last = $name;
			}
		}

		$db->query('DELETE FROM ' . SETTINGS_TABLE . ' WHERE tool="webadmin" AND pref_name="EditSort"');
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

	private static function updateShop2(we_database_base $db2, array $progress = []){
		if(!$db2->isTabExist(SHOP_TABLE)){
			return;
		}
		$db = new DB_WE();
		$init = $progress = ($progress ?: [
			'pos' => 0,
			'maxID' => 0,
			'max' => f('SELECT COUNT(DISTINCT IntOrderID) FROM ' . SHOP_TABLE)
		]);

		$maxStep = 150;

		if(!$progress['max'] || ($progress['pos'] > $progress['max'])){//finished
			$db->delTable(SHOP_TABLE . '_old');
			$db->query('RENAME TABLE ' . SHOP_TABLE . ' TO ' . SHOP_TABLE . '_old');
			return false;
		}
		if($init['pos'] == 0){
			//clean new tables
			$db->query('TRUNCATE TABLE ' . SHOP_ORDER_DATES_TABLE);
			$db->query('TRUNCATE TABLE ' . SHOP_ORDER_DOCUMENT_TABLE);
			$db->query('TRUNCATE TABLE ' . SHOP_ORDER_ITEM_TABLE);
			$db->query('TRUNCATE TABLE ' . SHOP_ORDER_TABLE);

			//make sure we have at least the last Order in the new table
			$db->query('INSERT IGNORE INTO ' . SHOP_ORDER_TABLE . ' SET ID=(SELECT MAX(IntOrderID) FROM ' . SHOP_TABLE . ')');
		}
		//prefill as much as possible

		$idAr = $db->getAllq('SELECT IntOrderID FROM ' . SHOP_TABLE . ' WHERE ID>' . intval($progress['maxID']) . ' GROUP BY IntOrderID LIMIT ' . $maxStep, true);

		$progress['pos'] += count($idAr) ?: 1;
		$progress['maxID'] = end($idAr);

		$ids = implode(',', $idAr);
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
		return array_merge($progress, ['text' => 'Shop ' . $progress['pos'] . ' / ' . $progress['max']]);
		//what about variants?! how do they apply?
		//old not used: IntPayment_Type
		//strSerial may contain we_wedoc_ & OF_... & may contain numeric entries
		/* 	 */
	}

	public static function doUpdate($what = '', array $progress = []){
		$db = new DB_WE();
		self::meassure('start');
		//if we are in liveupdate, initial db updates already triggered
		if($what == 'internal'){
			self::replayUpdateDB();
			self::meassure('replayUpdateDB');
			$what = 'all';
		}

		switch($what){
			default:
			case '':
				self::fixInconsistentTables($db);
				self::meassure('fixInconsistentTables');
				if(defined('WE_GLOSSARY_MODULE_PATH')){
					self::updateGlossar();
					self::meassure('updateGlossar');
				}
				self::upgradeTblFileLink($db);
				self::meassure('updateFileLink');
				self::updateCats($db);
				self::meassure('updateCats');
				self::updateDateInContent($db);
				self::meassure('updateContentDate');
				self::updateVersionsTable($db);
				self::meassure('versions');
				self::cleanUnreferencedVersions($db);
				self::meassure('fixVersions');
				self::updateCustomerFilters($db);
				self::meassure('customerFilter');
				self::updateCustomer($db);
				self::meassure('customer');
				self::updateSetting($db);
				self::meassure('setting');
			case 'content':
				$ret = self::updateContentTable($db, $progress);
				if($ret){
					self::meassure(-1);
					return array_merge($ret, ['what' => 'content']);
				}
				self::meassure('updateContent');
				break;
			case 'object':
				if(defined('OBJECT_X_TABLE')){
					$ret = self::updateObjectFilesX($db, $progress);
					if($ret){
						self::meassure(-1);
						return array_merge($ret, ['what' => 'object']);
					}
					self::meassure('updateObjectFilesX');
				}

				if(defined('SHOP_ORDER_TABLE')){
					self::updateShop($db);
					self::meassure('shop');
				}
			case 'shop':
				if(defined('SHOP_TABLE')){
					$ret = self::updateShop2($db, $progress);
					if($ret){
						self::meassure(-1);
						return array_merge($ret, ['what' => 'shop']);
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
