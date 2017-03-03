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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * This class contains all functions needed for the update process
 * TBD if we divide this class in several classes
 */
class liveUpdateFunctions{
	var $QueryLog = [
		'success' => [],
		'tableChanged' => [],
		'error' => [],
		'entryExists' => [],
		'tableExists' => [], //needed from server functions
	];

	/*
	 * Functions for updatelog
	 */

	function insertUpdateLogEntry($action, $version, $errorCode){
		$GLOBALS['DB_WE']->query('INSERT INTO ' . UPDATE_LOG_TABLE . ' SET ' . we_database_base::arraySetter([
				'aktion' => $action,
				'versionsnummer' => $version,
				'error' => $errorCode
		]));
	}

	/**
	 * @param string $type
	 * @param string $premessage
	 * @param integer $errorCode
	 * @param string $version
	 */
	function insertQueryLogEntries($type, $premessage, $errorCode, $version){
		// insert notices first
		if(isset($this->QueryLog[$type])){
			$message = $premessage;

			foreach($this->QueryLog[$type] as $noticeMessage){
				$message .= "<br />$noticeMessage\n";
			}
			$this->insertUpdateLogEntry($message, $version, $errorCode);
		}
	}

	/**
	 * Decode encoded strings submit from liveupdater
	 *
	 * @param string $string
	 * @return string
	 */
	function decodeCode($string){
		$string = base64_decode($string);
		if($string && $string[0] === 'x'){
			$str = gzuncompress($string);
			return ($str === false ? $string : $str);
		}
		return $string;
	}

	/**
	 * prepares given php-code
	 * - replaces doc_root
	 * - edits extension of all containing files
	 *
	 * @return string
	 */
	function preparePhpCode($content, $needle, $replace){
		return $this->checkReplaceDocRoot($content);
	}

	/**
	 * replaces extension of content
	 *
	 * @param unknown_type $content
	 * @param unknown_type $replace
	 * @param unknown_type $needle
	 * @return unknown
	 */
	function replaceExtensionInContent($content, $needle, $replace){
		return $content;
	}

	function replaceDocRootNeeded(){
		return (!(isset($_SERVER['DOCUMENT' . '_ROOT']) && $_SERVER['DOCUMENT' . '_ROOT'] == LIVEUPDATE_SOFTWARE_DIR));
	}

	/**
	 * checks if document root exists, and replaces $_SERVER['DOCMENT_ROOT'] in
	 * $content if needed
	 *
	 * @param string $content
	 * @return string
	 */
	function checkReplaceDocRoot($content){
		//replaces any count of escaped docroot-strings
		return ($this->replaceDocRootNeeded() ?
			preg_replace('-\$(_SERVER|GLOBALS)\[([\\\"\']+)DOCUMENT' . '_ROOT([\\\"\']+)\]-', '${2}' . LIVEUPDATE_SOFTWARE_DIR . '${3}', $content) :
			$content);
	}

	/**
	 * fills given array with all files in given dir
	 *
	 * @param array $allFiles
	 */
	function getFilesOfDir(&$allFiles, $baseDir){
		if(file_exists($baseDir)){
			$dh = opendir($baseDir);
			while(($entry = readdir($dh))){
				if($entry != "" && $entry != "." && $entry != ".."){
					$entry = $baseDir . "/" . $entry;
					if(!is_dir($entry)){
						$allFiles[] = $entry;
					}

					if(is_dir($entry)){
						$this->getFilesOfDir($allFiles, $entry);
					}
				}
			}
			closedir($dh);
		}
	}

	/**
	 * deletes $dir and all files/dirs inside
	 *
	 * @param string $dir
	 */
	function deleteDir($dir){
		if(strpos($dir, './') !== false){
			return true;
		}

		if(!file_exists($dir)){
			return true;
		}

		$dh = opendir($dir);
		if($dh){
			while(($entry = readdir($dh))){
				if($entry != '' && $entry != "." && $entry != '..'){
					$entry = $dir . '/' . $entry;
					if(is_dir($entry)){
						$this->deleteDir($entry);
					} else {
						$this->deleteFile($entry);
					}
				}
			}
			closedir($dh);
			return rmdir($dir);
		}
		return true;
	}

	/**
	 * Reads filecontent in a string and returns it
	 *
	 * @param string $filePath
	 * @return string
	 */
	function getFileContent($filePath){
		$content = '';
		$fh = fopen($filePath, 'rb');
		if($fh){
			$content = fread($fh, filesize($filePath));
			fclose($fh);
		}

		return $content;
	}

	/**
	 * writes filecontent in a file
	 *
	 * @param string $filePath
	 * @param string $newContent
	 * @return boolean
	 */
	function filePutContent($filePath, $newContent){
		if($this->checkMakeDir(dirname($filePath)) && $this->checkMakeFileWritable($filePath)){
			$fh = fopen($filePath, 'wb');
			if($fh){
				fwrite($fh, $newContent, strlen($newContent));
				fclose($fh);
				//if we write a php file, invalidate cache if used.
				if(substr($filePath, -4) === '.php' && function_exists('opcache_invalidate')){
					opcache_invalidate($filePath, true);
				}
				if(!chmod($filePath, 0444)){
					return false;
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * This function checks if a given file is writable and tries to chmod() if necessary
	 *
	 * @param string $filePath
	 * @return boolean
	 */
	function checkMakeFileWritable($filePath = '', $mod = 0750){
		$filePath = LIVEUPDATE_SOFTWARE_DIR . $filePath;

		if(file_exists($filePath) && !is_writable($filePath)){
			if(!chmod($filePath, $mod)){
				return false;
			}
		}

		return true;
	}

	/**
	 * This function checks if given dir exists, if not tries to create it
	 *
	 * @param string $dirPath
	 * @return boolean
	 */
	function checkMakeDir($dirPath, $mod = 0755){
		// open_base_dir - seperate document-root from rest
		$dirPath = rtrim(str_replace(['///', '//'], '/', $dirPath), '/');

		$dir = (defined('LIVEUPDATE_SOFTWARE_DIR') ? LIVEUPDATE_SOFTWARE_DIR : WEBEDITION_PATH);
		if(strpos($dirPath, $dir) === 0){
			$preDir = $dir;
			$dir = substr($dirPath, strlen($dir));
		} else {
			$preDir = '';
			$dir = $dirPath;
		}

		$pathArray = explode('/', $dir);
		$path = $preDir;

		foreach($pathArray as $subPath){
			$path .= $subPath;
			if($subPath && !is_dir($path)){
				if(!(file_exists($path) || mkdir($path, $mod))){
					return false;
				}
			}
			$path .= '/';
		}

		if(!is_writable($dirPath)){
			if(!chmod($dirPath, $mod)){
				return false;
			}
		}
		return true;
	}

	/**
	 * @param string $file
	 * @return boolean true if the file is not existent after this call
	 */
	function deleteFile($file){
		return (file_exists($file) ? @unlink($file) : true);
	}

	/**
	 * moves $source file to new $destination
	 *
	 * @param string $source
	 * @param string $destination
	 * @return boolean false if move was not successful
	 */
	function moveFile($source, $destination){

		if($source == $destination){
			return true;
		}
		if(filesize($source) == 0){//assume error, add warning, keep file!
			$this->QueryLog['error'][] = 'File ' . $source . ' was empty, not overwriting!';
			//keep going
			return true;
		}

		if($this->checkMakeDir(dirname($destination))){
			if($this->deleteFile($destination)){
				if(!isset($_SESSION['weS']['moveOk'])){
					touch($source . 'x');
					$_SESSION['weS']['moveOk'] = @rename($source . 'x', $destination . 'x');
					$this->deleteFile($destination . 'x');
					$this->deleteFile($source . 'x');
					$this->insertUpdateLogEntry('Using ' . ($_SESSION['weS']['moveOk'] ? 'move' : 'copy') . ' for installation', WE_VERSION, 0);
				}
				if(substr($destination, -4) === '.php' && function_exists('opcache_invalidate')){
					opcache_invalidate($destination, true);
				}

				if($_SESSION['weS']['moveOk']){
					return rename($source, $destination);
				}
				//rename seems to have problems - we do it old school way: copy, on success delete
				if(copy($source, $destination)){
					$this->deleteFile($source);
					//should we handle file deletion?
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * returns if selected file is a php file or not, important also check html files
	 *
	 * @param string $path
	 * @return boolean
	 */
	function isPhpFile($path){

		$pattern = "/\.([^\..]+)$/";
		$matches = [];
		if(preg_match($pattern, $path, $matches)){
			switch(strtolower($matches[1])){
				case 'jpg':
				case 'gif':
				case 'jpeg':
				case 'sql':
					return false;
			}
		}
		return true;
	}

	/**
	 * This file searchs $needle in given file and replaces it with $replace
	 * If needle is empty the whole file is overwritten. Also
	 * $_SERVER[DOCUMENT_ROOT] is replaced if necessary
	 *
	 * @param string $filePath
	 * @param string $replace
	 * @param string $needle
	 * @return boolean
	 */
	function replaceCode($filePath, $replace, $needle = ''){
		if(strpos($filePath, 'we/include/we_version') !== false || !$this->replaceDocRootNeeded()){
			return true;
		}

		if(file_exists($filePath)){
			$needle = $this->decodeCode($needle);
			$replace = $this->decodeCode($replace);
			$oldContent = $this->getFileContent($filePath);
			$replace = $this->checkReplaceDocRoot($replace);
			$newContent = ($needle ? preg_replace('/' . preg_quote($needle) . '/', $replace, $oldContent) : $replace );

			return ($this->filePutContent($filePath, $newContent));
		}
		return false;
	}

	/*
	 * Functions for patches
	 */

	/**
	 * executes patch.
	 *
	 * @param string $path
	 * @return boolean
	 */
	function executePatch($path){
		include_once($path);
		return true;
	}

	/*
	 * Functions for manipulating database
	 */

	/**
	 * returns array with all columns of given tablename
	 *
	 * @param string $tableName
	 * @return array
	 */
	function getFieldsOfTable($tableName, we_database_base $db){

		$fieldsOfTable = [];
		$db->query('DESCRIBE ' . $db->escape($tableName));

		while($db->next_record()){
			$fieldsOfTable[$db->f('Field')] = [
				'Field' => $db->f('Field'),
				'Type' => $db->f('Type'),
				'Null' => $db->f('Null'),
				'Key' => $db->f('Key'),
				'Default' => $db->f('Default'),
				'Extra' => $db->f('Extra')
			];
		}
		return $fieldsOfTable;
	}

	/**
	 * returns array with key information of a table by tablename
	 *
	 * @param string $tableName
	 * @return array
	 */
	function getKeysFromTable($tableName, $lowerKeys = false){
		$db = new DB_WE();
		$keysOfTable = [];
		$db->query('SHOW INDEX FROM ' . $db->escape($tableName));
		while($db->next_record()){
			if($db->f('Key_name') === 'PRIMARY'){
				$indexType = 'PRIMARY';
			} else if($db->f('Comment') === 'FULLTEXT' || $db->f('Index_type') === 'FULLTEXT'){// this also depends on mysqlVersion
				$indexType = 'FULLTEXT';
			} else if($db->f('Non_unique') == 0){
				$indexType = 'UNIQUE';
			} else {
				$indexType = 'INDEX';
			}

			$key = $lowerKeys ? strtolower($db->f('Key_name')) : $db->f('Key_name');

			if(!isset($keysOfTable[$key]) || !in_array($indexType, $keysOfTable[$key])){
				$keysOfTable[$key]['index'] = $indexType;
			}
			$keysOfTable[$key][$db->f('Seq_in_index')] = $db->f('Column_name') . ($db->f('Sub_part') ? '(' . $db->f('Sub_part') . ')' : '');
		}

		return $keysOfTable;
	}

	/**
	 * expects array from getFieldsOfTable and returns generated queries to
	 * alter these fields
	 *
	 * @param array $fields
	 * @param string $tableName
	 * @param boolean $isNew
	 * @return unknown
	 */
	function getAlterTableForFields($fields, $tableName, $isNew = false){
		$queries = [];

		foreach($fields as $fieldName => $fieldInfo){

			$default = '';

			$null = (strtoupper($fieldInfo['Null']) === 'YES' ? ' NULL' : ' NOT NULL');

			if(($fieldInfo['Default']) != ""){
				$default = 'DEFAULT ' . (($fieldInfo['Default']) === 'CURRENT_TIMESTAMP' ? 'CURRENT_TIMESTAMP' : '\'' . $fieldInfo['Default'] . '\'');
			} elseif(strtoupper($fieldInfo['Null']) === "YES"){
				$default = ' DEFAULT NULL';
			}
			$extra = strtoupper($fieldInfo['Extra']);
			$pos = ($fieldInfo['last'] === 'FIRST' ? $fieldInfo['last'] : 'AFTER ' . $fieldInfo['last']);
			$extra .= ' ' . $pos;
			//note: auto_increment cols must have an index!
			if(strpos($extra, 'AUTO_INCREMENT') !== false){
				$keyfound = false;
				$Currentkeys = $this->getKeysFromTable($tableName);
				foreach($Currentkeys as $ckeys){
					foreach($ckeys as $k){
						if(stripos($k, $fieldName) !== false){
							$keyfound = true;
						}
					}
				}
				if(!$keyfound){
					$extra .= ', ADD INDEX _temp (' . $fieldInfo['Field'] . ')';
				}
			}

			if($isNew){
				$queries[] = 'ALTER TABLE `' . $tableName . '` ADD `' . $fieldInfo['Field'] . '` ' . $fieldInfo['Type'] . " $null $default $extra";
			} else {
				$queries[] = 'ALTER TABLE `' . $tableName . '` MODIFY `' . $fieldInfo['Field'] . '` ' . $fieldInfo['Type'] . " $null $default $extra";
			}
		}
		return $queries;
	}

	/**
	 * returns array with queries to update keys of table
	 *
	 * @param array $fields
	 * @param string $tableName
	 * @param boolean $isNew
	 * @return array
	 */
	function getAlterTableForKeys($fields, $tableName, $isNew){
		$queries = [];

		foreach($fields as $key => $indexes){
			//escape all index fields
			$indexes = array_map('addslashes', $indexes);

			$type = $indexes['index'];
			$mysl = '`';
			if($type === 'PRIMARY'){
				$key = 'KEY';
				$mysl = '';
			}
			//index is not needed any more and disturbs implode
			unset($indexes['index']);
			$myindexes = [];
			foreach($indexes as $index){
				if(strpos($index, '(') === false){
					$myindexes[] = '`' . $index . '`';
				} else {
					$myindexes[] = '`' . str_replace('(', '`(', $index);
				}
			}
			$queries[] = 'ALTER TABLE ' . $tableName . ' ' . ($isNew ? '' : ' DROP ' . ($type === 'PRIMARY' ? $type : 'INDEX') . ' ' . $mysl . $key . $mysl . ' , ') . ' ADD ' . $type . ' ' . $mysl . $key . $mysl . ' (' . implode(',', $myindexes) . ')';
		}
		return $queries;
	}

	/**
	 * @deprecated since version now
	 * @param string $path
	 * @return boolean
	 */
	function isInsertQueriesFile($path){
		//	return preg_match("/^(.){3}_insert_(.*).sql/", basename($path));
	}

	/**
	 * executes all queries in a single file
	 * - there is one query, if create-statement
	 * - many queris, if insert statements
	 *
	 *
	 * @param string $path
	 * @return boolean
	 */
	function executeQueriesInFiles($path){
		static $db = null;
		static $defaultEngine = '';
		$db = $db ?: new DB_WE();
		if(!$defaultEngine){
			$db->query('show variables LIKE "default_storage_engine"');
			$db->next_record();
			$defaultEngine = $db->f('Value');
			if(!in_array(strtolower($defaultEngine), ['myisam', 'aria'])){
				$defaultEngine = 'myisam';
			}
		}

		$queries = explode("/* query separator */", str_replace("ENGINE=MyISAM", 'ENGINE=' . $defaultEngine, $this->getFileContent($path)));
		$success = true;
		foreach($queries as $query){
			$success &= $this->executeUpdateQuery($query, $db);
		}

		return $success;
	}

	/**
	 * updates the database with given dump.
	 *
	 * @param string $query
	 */
	function executeUpdateQuery($query, we_database_base $db = null){
		$db = ($db ?: new DB_WE());

		// when executing a create statement, try to create table,
		// change fields when needed.


		if(strpos($query, '###INSTALLONLY###') !== false){// potenzielles Sicherheitsproblem, nur im LiveUpdate nicht ausf�hren
			return true;
		}

		$query = str_replace(['###TBLPREFIX###', '###UPDATEONLY###'], [LIVEUPDATE_TABLE_PREFIX, ''], trim($query));
		$matches = [];
		if(preg_match('/###UPDATEDROPCOL\(([^,]*),([^)]*)\)###/', $query, $matches)){
			$query = ($db->isColExist($matches[2], $matches[1]) ? 'ALTER TABLE ' . $db->escape($matches[2]) . ' DROP COLUMN ' . $db->escape($matches[1]) : '');
		}
		if(preg_match('/###ONCOL\(([^,]*),([^)]*)\)(.+);###/', $query, $matches)){
			$query = ($db->isColExist($matches[2], $matches[1]) ? $matches[3] : '');
		}
		//handle if key is not set, should be used after table def. so handling code, e.g. truncate, copy... can be put here
		if(preg_match('/###ONKEYFAILED\(([^,]+),([^)]+)\)([^#]+)###/', $query, $matches)){
			$keys = $this->getKeysFromTable($matches[2]);
			$query = (!isset($keys[$matches[1]]) ? $matches[3] : '');
		}
		if(preg_match('/###UPDATEDROPKEY\(([^,]+),([^)]+)\)###/', $query, $matches)){
			$db->query('SHOW KEYS FROM ' . $db->escape($matches[2]) . ' WHERE Key_name="' . $matches[1] . '"');
			$query = ($db->num_rows() ? 'ALTER TABLE ' . $db->escape($matches[2]) . ' DROP KEY ' . $db->escape($matches[1]) : '');
		}
		if(preg_match('/###ONTAB\(([^)]*)\)(.+);###/', $query, $matches)){
			$query = ($db->isTabExist($matches[1]) ? $matches[2] : '');
		}


		// second, we need to check if there is a collation
		$Charset = we_database_base::getCharset();
		$Collation = we_database_base::getCollation();
		if($Charset != '' && $Collation != ''){
			if(stripos($query, 'CREATE TABLE ') === 0){
				if(strtoupper($Charset) === 'UTF-8'){//#4661
					$Charset = 'utf8';
				}
				if(strtoupper($Collation) === 'UTF-8'){//#4661
					$Collation = 'utf8_general_ci';
				}
				$query = preg_replace('/;$/', ' CHARACTER SET ' . $Charset . ' COLLATE ' . $Collation . ';', $query, 1);
			}
		}

		$tabExists = false;
		if(preg_match('/CREATE TABLE (\w+) \(/', $query, $matches)){
			if($db->isTabExist($matches[1])){//tab exists
				$db->Errno = 1050;
				$tabExists = true;
			}
		}


		if(!$query || (!$tabExists && $db->query($query))){
			return true;
		}

		switch($db->Errno){
			case 1050: // this table already exists
				// the table already exists,
				// make tmptable and check these tables
				$namePattern = '/CREATE TABLE (\w+) \(/';
				preg_match($namePattern, $query, $matches);
				if($matches[1]){

					// get name of table and build name of temptable
					// realname of the new table
					$tableName = $matches[1];

					// tmpname - this table is to compare the incoming dump
					// with existing table
					$tmpName = '__we_delete_update_temp_table__';
					$backupName = trim($tableName, '`') . '_backup';

					$db->query('DROP TABLE IF EXISTS ' . $db->escape($tmpName)); // delete table if already exists
					$db->query('DROP TABLE IF EXISTS ' . $db->escape($backupName)); // delete table if already exists
					$db->query('SHOW CREATE TABLE ' . $db->escape($tableName));
					list(, $orgTable) = ($db->next_record() ? $db->Record : ['', '']);
					$orgTable = preg_replace($namePattern, 'CREATE TABLE ' . $db->escape($backupName) . ' (', $orgTable);

					// create temptable
					$tmpQuery = preg_replace($namePattern, 'CREATE TEMPORARY TABLE ' . $db->escape($tmpName) . ' (', $query);
					$db->query(trim($tmpQuery));

					// get information from existing and new table
					$origTable = $this->getFieldsOfTable($tableName, $db);
					$newTable = $this->getFieldsOfTable($tmpName, $db);
					if(empty($newTable)){
						$this->QueryLog['error'][] = 'Update of table ' . $tableName . " failed (create temporary table was not successfull)\n-- $query --";
						break;
					}

					// get keys from existing and new table
					$origTableKeys = $this->getKeysFromTable($tableName, true);
					$newTableKeys = $this->getKeysFromTable($tmpName);

					// determine changed and new fields.
					$changeFields = []; // array with changed fields
					$addFields = []; // array with new fields
					$lastField = 'FIRST';
					foreach($newTable as $fieldName => $newField){
						$newField['last'] = $lastField;
						if(isset($origTable[$fieldName])){ // field exists
							if(!($newField['Type'] == $origTable[$fieldName]['Type'] && $newField['Null'] == $origTable[$fieldName]['Null'] && $newField['Default'] == $origTable[$fieldName]['Default'] && $newField['Extra'] == $origTable[$fieldName]['Extra'])){
								$changeFields[$fieldName] = $newField;
							}
						} else { // field does not exist
							$addFields[$fieldName] = $newField;
						}
						$lastField = $fieldName;
					}

					// determine new keys
					// moved down after change and addfields
					// get all queries to add/change fields, keys
					$alterQueries = [];

					// get all queries to change existing fields
					if($changeFields){
						$alterQueries = array_merge($alterQueries, $this->getAlterTableForFields($changeFields, $tableName));
					}
					if($addFields){
						$alterQueries = array_merge($alterQueries, $this->getAlterTableForFields($addFields, $tableName, true));
					}

					//new position to determine new keys
					$addKeys = [];
					$changedKeys = [];
					foreach($newTableKeys as $keyName => $indexes){
						$lkeyName = strtolower($keyName);
						if(isset($origTableKeys[$lkeyName])){
							//index-type changed
							if($origTableKeys[$lkeyName]['index'] != $indexes['index']){
								$changedKeys[$keyName] = $indexes;
								continue;
							}

							for($i = 1; $i < count($indexes); $i++){
								if(!in_array($indexes[$i], $origTableKeys[$lkeyName])){
									$changedKeys[$keyName] = $indexes;
									break;
								}
							}
						} else {
							$addKeys[$keyName] = $indexes;
						}
					}

					// get all queries to change existing keys
					if(!empty($addKeys)){
						$alterQueries = array_merge($alterQueries, $this->getAlterTableForKeys($addKeys, $tableName, true));
					}

					if(!empty($changedKeys)){
						$alterQueries = array_merge($alterQueries, $this->getAlterTableForKeys($changedKeys, $tableName, false));
					}

					//clean-up, if there is still a temporary index - make sure this is the first statement, since new temp might be created
					if(isset($origTableKeys['_temp'])){
						$alterQueries = array_merge(['ALTER TABLE `' . $tableName . '` DROP INDEX _temp'], $alterQueries);
					}
					if($alterQueries){
						// execute all queries
						$success = true;
						$duplicate = false;
						foreach($alterQueries as $query){
							if(!trim($query)){
								continue;
							}
							if($db->query(trim($query))){
								$this->QueryLog['success'][] = $query;
							} else {
								//unknown why mysql don't show correct error
								if($db->Errno == 1062 || $db->Errno == 0){
									$duplicate = true;
									$this->QueryLog['tableChanged'][] = $tableName;
								} else {
									$this->QueryLog['error'][] = $db->Errno . ' ' . urlencode($db->Error) . "\n-- $query --";
								}
								$success = false;
							}
						}
						if($success){
							$this->QueryLog['tableChanged'][] = $tableName . "\n<!-- $query -->";
						} else if($duplicate){
							if($db->query('RENAME TABLE ' . $db->escape($tableName) . ' TO ' . $db->escape($backupName))){
								$db->query($orgTable);
								$db->lock([$tableName => 'write', $backupName => 'read']);
								foreach($alterQueries as $query){
									if(trim($query) && !$db->query(trim($query))){
										$this->QueryLog['error'][] = $db->Errno . ' ' . urlencode($db->Error) . "\n-- $query --";
									}
								}
								$db->query('INSERT IGNORE INTO ' . $db->escape($tableName) . ' SELECT * FROM ' . $db->escape($backupName));
								$db->unlock();
							}
						}
						$SearchTempTableKeys = $this->getKeysFromTable($tableName);
						if(isset($SearchTempTableKeys['_temp'])){
							$db->query(trim('ALTER TABLE ' . $db->escape($tableName) . ' DROP INDEX _temp'));
						}
					} else {
						//$this->QueryLog['tableExists'][] = $tableName;
					}

					$db->query('DROP TABLE IF EXISTS ' . $db->escape($tmpName));
				}
				break;
			case 1062:
				$this->QueryLog['entryExists'][] = $db->Errno . ' ' . urlencode($db->Error) . "\n<!-- $query -->";
				return false;
			case 0:
			case 1065:
				//ignore empty queries
				return true;
			default:
				$this->QueryLog['error'][] = $db->Errno . ' ' . urlencode($db->Error) . "\n-- $query --";
				return false;
		}

		return true;
	}

	/**
	 * returns log array for db-queries
	 * @return array
	 */
	function getQueryLog($specific = ''){
		return ($specific ? $this->QueryLog[$specific] : $this->QueryLog);
	}

	/**
	 * resets query log, this is done after each query file.
	 */
	function clearQueryLog(){
		foreach($this->QueryLog as &$cur){
			$cur = [];
		}
	}

	/**
	 * returns array with all installed languages
	 *
	 * @return array
	 */
	public static function getInstalledLanguages(){
		clearstatcache();

		//	Get all installed Languages
		$installedLanguages = [];
		//	Look which languages are installed
		$language_directory = dir($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language');

		while(false !== ($entry = $language_directory->read())){
			if($entry != '.' && $entry != '..'){
				if(is_dir($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $entry)){
					$installedLanguages[] = $entry;
				}
			}
		}
		$language_directory->close();

		return $installedLanguages;
	}

	function removeObsoleteFiles($path){
		if(is_file($path . 'del.files')){
			$all = [];
			if(($all = file($path . 'del.files', FILE_IGNORE_NEW_LINES))){
				$delFiles = [];
				foreach($all as $cur){
					$recursive = false;
					if($cur{0} === '!'){
						$cur = substr($cur, 1);
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
		}

		return true;
	}

	function removeDirOnlineInstaller(){
		if(is_dir($_SERVER['DOCUMENT_ROOT'] . '/OnlineInstaller')){
			we_base_file::deleteLocalFolder($_SERVER['DOCUMENT_ROOT'] . '/OnlineInstaller', true);
		}

		return true;
	}

	static function weUpdaterDoUpdate($what = '', $pos = ''){
		if(method_exists('we_updater', 'doUpdate')){
			we_updater::doUpdate();
		}

		return true;
	}

	/**
	 * This file sets another errorhandler - to make specific error-messages
	 *
	 * @param integer $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param integer $errline
	 * @param string $errcontext
	 */
	static function liveUpdateErrorHandler($errno, $errstr, $errfile, $errline, $errcontext){

		$GLOBALS['liveUpdateError'] = [
			"errorNr" => $errno,
			"errorString" => $errstr,
			"errorFile" => $errfile,
			"errorLine" => $errline,
		];
		if(function_exists('error_handler')){
			if(strpos($errstr, 'MYSQL-ERROR') !== 0){
				//don't handle mysql errors, they're handled by updatelog - since some of them are "wanted"
				//log errors to system log, if we have one.
				error_handler($errno, $errstr, $errfile, $errline, $errcontext);
			}
		}
	}

}
