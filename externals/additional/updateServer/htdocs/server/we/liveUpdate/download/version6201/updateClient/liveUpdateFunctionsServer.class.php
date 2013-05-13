<?php
//version6201
//code aus 6201
class liveUpdateFunctionsServer extends liveUpdateFunctions {

	/**
	 * moves $source file to new $destination
	 *
	 * @param string $source
	 * @param string $destination
	 * @return boolean
	 */
	function moveFile($source, $destination) {


		if ($this->checkMakeDir(dirname($destination))) {

			$this->deleteFile($destination);
			if (rename($source, $destination)) {
				return true;
			} else {
				return false;
			}

		} else {
			return false;
		}
	}
	
	/**
	 * @param string $file
	 * @return boolean true if the file is not existent after this call
	 */
	function deleteFile($file) {
		if(file_exists($file)){
			return @unlink($file);
		}else{
			return true;
		}
	}
	
	/**
	 * Reads filecontent in a string and returns it
	 *
	 * @param string $filePath
	 * @return string
	 */
	function getFileContent($filePath) {

		$content = '';
		$fh = fopen($filePath, 'rb');
		if ($fh) {
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
	function filePutContent($filePath, $newContent) {

		if ($this->checkMakeDir( dirname($filePath) )) {
			$fh = fopen($filePath, 'wb');
			if ($fh) {
				fwrite($fh, $newContent, strlen($newContent));
				fclose($fh);
				if(!chmod($filePath, 0755)) {
					return false;
					
				}
				return true;

			}

		}
		return false;
	}
	
	/**
	 * returns array with key information of a table by tablename
	 *
	 * @param string $tableName
	 * @return array
	 */
	function getKeysFromTable($tableName) {
		$db = new DB_WE();
		$keysOfTable = array();
		$db->query("SHOW INDEX FROM $tableName");
		while ($db->next_record()) {
			if ($db->f('Key_name') == 'PRIMARY') {
				$indexType = 'PRIMARY';
			} else if ( $db->f('Comment') == 'FULLTEXT' || $db->f('Index_type') == 'FULLTEXT' ) {// this also depends from mysqlVersion
				$indexType = 'FULLTEXT';
			} else if ( $db->f('Non_unique') == '0' ) {
				$indexType = 'UNIQUE';
			} else {
				$indexType = 'INDEX';
			}

			if (!isset($keysOfTable[$db->f('Key_name')]) || !in_array($indexType, $keysOfTable[$db->f('Key_name')])) {
				$keysOfTable[$db->f('Key_name')]['index'] = $indexType;
			}
			$keysOfTable[$db->f('Key_name')][$db->f('Seq_in_index')]=$db->f('Column_name').($db->f('Sub_part')?'('.$db->f('Sub_part').')':'');
		}

		return $keysOfTable;
	}
	
	/**
    * expects array from getFieldsOfTable and returns generated queries to
    * alter these fields
    *
    * @param array $fields
    * @param string $tableName
    * @param boolean $newField
    * @return unknown
    */
   function getAlterTableForFields($fields, $tableName, $isNew=false) {

       $queries = array();

       foreach ($fields as $fieldName => $fieldInfo) {

           $extra = '';
           $default = '';

           $null = (strtoupper($fieldInfo['Null']) == "YES"?' NULL':' NOT NULL');

           if (($fieldInfo['Default']) != "") {
						 $default ='DEFAULT '.(($fieldInfo['Default']) == 'CURRENT_TIMESTAMP'?'CURRENT_TIMESTAMP':'\'' . $fieldInfo['Default'] . '\'');
           } else {
               if (strtoupper($fieldInfo['Null']) == "YES") {
                   $default = ' DEFAULT NULL';
               }
           }
           $extra = strtoupper($fieldInfo['Extra']);
		   			//note: auto_increment cols must have an index!
					if( strpos($extra,'AUTO_INCREMENT') !== false){
						$keyfound=false;
						$Currentkeys = $this->getKeysFromTable($tableName);
						foreach ($Currentkeys as $ckeys){
							foreach ($ckeys as $k){
								if (stripos($k,$fieldName)!==false){$keyfound=true;}
							}
						}
						if (!$keyfound){
							$extra .= ' FIRST, ADD INDEX _temp ('.$fieldInfo['Field'].')';
						}
					}

           if ($isNew) {
				//Bug #4431, siehe unten
			  	$queries[] = "ALTER TABLE `$tableName` ADD `" . $fieldInfo['Field'] . '` ' . $fieldInfo['Type'] . " $null $default $extra";;
           } else {
				//Bug #4431
			   // das  mysql_real_escape_string bei $fieldInfo['Type'] f�hrt f�r enum dazu, das die ' escaped werden und ein Syntaxfehler entsteht (nicht abgeschlossene Zeichenkette
			   $queries[] = "ALTER TABLE `$tableName` CHANGE `" . $fieldInfo['Field'] . '` `' . $fieldInfo['Field'] . '` ' .$fieldInfo['Type'] . " $null $default $extra";
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
	function getAlterTableForKeys($fields, $tableName, $isNew) {
		$queries = array();

		foreach ($fields as $key => $indexes) {
			//escape all index fields
			array_walk($indexes,'addslashes');

			$type=$indexes['index'];
			$mysl='`';
			if($type=='PRIMARY'){
				$key='KEY';
				$mysl='';
			}
			//index is not needed any more and disturbs implode
			unset($indexes['index']);
			$myindexes=array();
			foreach ($indexes as $index){
				if (strpos($index,'(') === false){
					$myindexes[] = '`'.$index.'`';
				} else {
					$myindexes[] = '`'.str_replace('(','`(',$index);
				}
			}
			$queries[] = 'ALTER TABLE `'.$tableName.'` '.($isNew?'':' DROP '.($type=='PRIMARY'?$type:'INDEX').' '.$mysl.$key.$mysl.' , ').' ADD ' . $type. ' '.$mysl.$key.$mysl . ' ('.implode(',',$myindexes).')';
		}
		return $queries;
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
	function executeQueriesInFiles($path) {

		if ($this->isInsertQueriesFile($path)) {
			$success = true;
			$queryArray = file($path);
			if ($queryArray) {
				foreach ($queryArray as $query) {
					if (trim($query)) {
						$success &= $this->executeUpdateQuery($query);
					}
				}
			}

		} else {
			$content = $this->getFileContent($path);
			$queries = explode("/* query separator */",$content);
			//$success = $this->executeUpdateQuery($content);
			$success = true;
			foreach($queries as $query) {
				$success &= $this->executeUpdateQuery($query);
			}
			
		}
		return $success;
	}
	
	/**
	 * updates the database with given dump.
	 *
	 * @param string $query
	 */
		function executeUpdateQuery($query) {

		$db = new DB_WE();

		// when executing a create statement, try to create table,
		// change fields when needed.

		$query = trim($query);

		if (strpos($query,'###INSTALLONLY###')!==false){// potenzielles Sicherheitsproblem, nur im LiveUpdate nicht ausf�hren
			return true;
		}

		$query=str_replace('###UPDATEONLY###', '', $query);
		if (LIVEUPDATE_TABLE_PREFIX && strpos($query,'###TBLPREFIX###')===false) {

			$query = preg_replace("/^INSERT INTO /", "INSERT INTO " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^INSERT IGNORE INTO /", "INSERT IGNORE INTO " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^CREATE TABLE /", "CREATE TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^DELETE FROM /", "DELETE FROM " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^ALTER TABLE /", "ALTER TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^RENAME TABLE /", "RENAME TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^TRUNCATE TABLE /", "TRUNCATE TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^DROP TABLE /", "DROP TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			
			$query = @str_replace(LIVEUPDATE_TABLE_PREFIX.'`', '`'.LIVEUPDATE_TABLE_PREFIX, $query);
		}
		$query=str_replace('###TBLPREFIX###', LIVEUPDATE_TABLE_PREFIX, $query);
		

		// second, we need to check if there is a collation
		if (defined("DB_CHARSET") && DB_CHARSET != "" && defined("DB_COLLATION") && DB_COLLATION != "") {
			if(eregi("^CREATE TABLE ", $query)) {
				$Charset = DB_CHARSET;
				$Collation = DB_COLLATION;
				if($Charset == 'UTF-8'){//#4661
					$Charset='utf8';
				}
				if($Collation == 'UTF-8'){//#4661
					$Collation='utf8_general_ci';
				}
				$query = preg_replace("/;$/", " CHARACTER SET " . $Charset . " COLLATE " . $Collation . ";", $query, 1);
			}

		}

		if ($db->query($query) ) {
			return true;
		} else {

			switch ($db->Errno) {

				case '1050': // this table already exists

					// the table already exists,
					// make tmptable and check these tables ...
					$namePattern = "/CREATE TABLE (\w+) \(/";
					preg_match($namePattern, $query, $matches);

					if ($matches[1]) {

						// get name of table and build name of temptable

						// realname of the new table
						$tableName = $matches[1];

						// tmpname - this table is to compare the incoming dump
						// with existing table
						$tmpName = '__we_delete_update_temp_table__';

						$db->query("DROP TABLE IF EXISTS $tmpName;"); // delete table if already exists

						// create temptable
						$tmpQuery = preg_replace($namePattern, "CREATE TABLE $tmpName (", $query);
						$db->query(trim($tmpQuery));

						// get information from existing and new table
						$origTable = $this->getFieldsOfTable($tableName);
						$newTable = $this->getFieldsOfTable($tmpName);

						// get keys from existing and new table
						$origTableKeys = $this->getKeysFromTable($tableName);
						$newTableKeys = $this->getKeysFromTable($tmpName);


						// determine changed and new fields.
						$changeFields = array(); // array with changed fields
						$addFields = array(); // array with new fields

						foreach ($newTable as $fieldName => $newField) {

							if (isset($origTable[$fieldName])) { // field exists
								if ( !($newField['Type'] == $origTable[$fieldName]['Type'] && $newField['Null'] == $origTable[$fieldName]['Null'] && $newField['Default'] == $origTable[$fieldName]['Default'] && $newField['Extra'] == $origTable[$fieldName]['Extra']) ) {
									$changeFields[$fieldName] = $newField;
								}
							} else { // field does not exist
								$addFields[$fieldName] = $newField;
							}
						}

						// determine new keys
						// moved down after change and addfields

						// get all queries to add/change fields, keys
						$alterQueries = array();

						// get all queries to change existing fields
						if (sizeof($changeFields)) {
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForFields($changeFields, $tableName));
						}
						if (sizeof($addFields)) {
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForFields($addFields, $tableName, true));
						}

						//new position to determine new keys
						$addKeys = array();
						$changedKeys = array();
						foreach ($newTableKeys as $keyName => $indexes) {

							if (isset($origTableKeys[$keyName])) {
								//index-type changed
								if($origTableKeys[$keyName]['index'] != $indexes['index']){
									$changedKeys[$keyName] = $indexes;
									continue;
								}

								for ($i=1;$i<sizeof($indexes);$i++) {
									if (!in_array($indexes[$i], $origTableKeys[$keyName])) {
										$changedKeys[$keyName] = $indexes;
										break;
									}
								}
							} else {
								$addKeys[$keyName] = $indexes;
							}
						}

						// get all queries to change existing keys
						if (sizeof($addKeys)) {
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForKeys($addKeys, $tableName, true));
						}

						if (sizeof($changedKeys)) {
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForKeys($changedKeys, $tableName, false));
						}

						//clean-up, if there is still a temporary index - make sure this is the first statement, since new temp might be created
						if (isset($origTableKeys['_temp'])) {
							$alterQueries = array_merge(array('ALTER TABLE `'.$tableName.'` DROP INDEX _temp'),$alterQueries);
						}

						if (sizeof($alterQueries)) {
							// execute all queries
							$success = true;
							foreach ($alterQueries as $_query) {

								if ($db->query(trim($_query))) {
									$this->QueryLog['success'][] = $_query;
								} else {
									$this->QueryLog['error'][] = $db->Errno . ' ' . $db->Error . "\n-- $_query --";
									$success = false;
								}
							}
							if ($success) {
								$this->QueryLog['tableChanged'][] = $tableName . "\n<!-- $query -->";
							}
							$SearchTempTableKeys = $this->getKeysFromTable($tableName);
							if (isset($SearchTempTableKeys['_temp'])) {
								$db->query(trim('ALTER TABLE `'.$tableName.'` DROP INDEX _temp'));
							}

						} else {
							$this->QueryLog['tableExists'][] = $tableName;
						}

						$db->query("DROP TABLE $tmpName");
					}
				break;
				case '1062':
					$this->QueryLog['entryExists'][] = $db->Errno . ' ' . $db->Error . "\n<!-- $query -->";
				break;
				default:
					$this->QueryLog['error'][] = $db->Errno . ' ' . $db->Error . "\n-- $query --";
					return false;
				break;
			}
			return false;
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
	function replaceCode($filePath, $replace, $needle='') {

		// decode parameters
		$needle = $this->decodeCode($needle);
		$replace = $this->decodeCode($replace);

		if (file_exists($filePath)) {
			$oldContent = $this->getFileContent($filePath);
			$replace = $this->checkReplaceDocRoot($replace);
			if ($needle) {
				/*This version is used in OnlineInstaller! which one is correct?
				$newneedle= preg_quote($needle, '~');
				$newContent = preg_replace('~'.$newneedle.'~', $replace, $oldContent);
				*/
				$newContent = ereg_replace($needle, $replace, $oldContent);

			} else {
				$newContent = $replace;
			}

			if (!$this->filePutContent($filePath, $newContent)) {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}
	function executePatch($path) {

		if (file_exists($path)) {

			$code = $this->getFileContent($path);
			$patchSuccess = eval('?>' .$code);
			if ($patchSuccess === false) {
				return false;
			} else {
				return true;
			}
		}
		return true;
	}

}

