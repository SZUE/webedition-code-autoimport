<?php 

class liveUpdateFunctionsServer extends liveUpdateFunctions {
	
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
	
	       $null = '';
	       $extra = '';
	       $default = '';
	
	       if (strtoupper($fieldInfo['Null']) == "YES") {
	           $null = ' NULL';
	       } else {
	           $null = ' NOT NULL';
	       }
	
	       if (($fieldInfo['Default']) != "") {
	           $default = ' default \'' . $fieldInfo['Default'] . '\'';
	       } else {
	           if (strtoupper($fieldInfo['Null']) == "YES") {
	               $default = ' default NULL';
	           }
	       }
	       $extra = strtoupper($fieldInfo['Extra']);
	
	       if ($isNew) {
	
	           $queries[] = "ALTER TABLE $tableName ADD " . $fieldInfo['Field'] . " " . $fieldInfo['Type'] . " $null $default $extra";
	       } else {
	
	           $queries[] = "ALTER TABLE $tableName CHANGE " . $fieldInfo['Field'] . " " . $fieldInfo['Field'] . " " . $fieldInfo['Type'] . " $null $default $extra";
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
	function getAlterTableForKeys($fields, $tableName, $isNew=true) {

		$queries = array();

		foreach ($fields as $key => $indexes) {

			for ($i=0; $i<sizeof($indexes); $i++) {
				
				$index = '';
				switch ($indexes[$i]) {
					case 'PRIMARY':
						$index = 'PRIMARY KEY';
					break;
					default:
						$index = strtoupper($indexes[$i]);
					break;
				}
				
				$queries[] = "ALTER TABLE $tableName ADD " . $index . " ($key)";
			}
		}
		return $queries;
	}
	
	/**
	 * replaces extension of content
	 *
	 * @param unknown_type $content
	 * @param unknown_type $replace
	 * @param unknown_type $needle
	 * @return unknown
	 */
	function replaceExtensionInContent($content, $needle, $replace) {
		
		$content = str_replace($needle, $replace, $content);
		return $content;
	}
	
	/**
	 * prepares given php-code
	 * - replaces doc_root
	 * - edits extension of all containing files
	 *
	 * @return string
	 */
	function preparePhpCode($content, $needle, $replace) {
		
		$content = $this->replaceExtensionInContent($content, $needle, $replace);
		$content = $this->checkReplaceDocRoot($content);
		
		return $content;
	}
	
	/**
	 * checks if document root exists, and replaces $_SERVER['DOCMENT_ROOT'] in
	 * $content if needed
	 *
	 * @param string $content
	 * @return string
	 */
	function checkReplaceDocRoot($content) {
		
		if (!(isset($_SERVER['DOCUMENT_' . 'ROOT']) && $_SERVER['DOCUMENT_' . 'ROOT'] == LIVEUPDATE_SOFTWARE_DIR) ) {
			
			$content = str_replace('$_SERVER[\'DOCUMENT_' . 'ROOT\']', '"' . LIVEUPDATE_SOFTWARE_DIR . '"', $content);
			$content = str_replace('$_SERVER["DOCUMENT_' . 'ROOT"]', '"' . LIVEUPDATE_SOFTWARE_DIR . '"', $content);
			$content = str_replace('$GLOBALS[\'DOCUMENT_' . 'ROOT\']', '"' . LIVEUPDATE_SOFTWARE_DIR . '"', $content);
			$content = str_replace('$GLOBALS["DOCUMENT_' . 'ROOT"]', '"' . LIVEUPDATE_SOFTWARE_DIR . '"', $content);
		}
		return $content;
	}
	
	/**
	 * returns if selected file is a php file or not
	 *
	 * @param string $path
	 * @return boolean
	 */
	function isPhpFile($path) {
		
		$pattern = "/\.([^\..]+)$/";
		
		if (preg_match($pattern, $path, $matches)) {
			
			$ext = strtolower($matches[1]);
			
			if ( ($ext == 'jpg' || $ext == 'gif' || $ext == 'jpeg' || $ext == 'sql') ) {
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
	function checkMakeDir($dirPath, $mod=0755) {
		
		// open_base_dir - seperate document-root from rest
		if (strpos($dirPath, LIVEUPDATE_SOFTWARE_DIR) === 0) {
			$preDir = LIVEUPDATE_SOFTWARE_DIR;
			$dir = substr($dirPath, strlen(LIVEUPDATE_SOFTWARE_DIR));
		} else {
			$preDir = '';
			$dir = $dirPath;
		}
		
		$pathArray = explode('/', $dir);
		$path = $preDir;
		
		for($i=0;$i<sizeof($pathArray); $i++){
			
			$path .= $pathArray[$i];
			if($pathArray[$i] != "" && !is_dir($path)){
				
				if( !(file_exists($path) || mkdir($path, $mod)) ){
					return false;
				}
			}
			$path .= "/";
		}
		
		return true;
	}
	
	function insertUpdateLogEntry($action, $version, $errorCode) {

		global $DB_WE;
		
		$query = 
			"INSERT INTO " . UPDATE_LOG_TABLE . "
			(datum, aktion, versionsnummer, error)
			VALUES (NOW(), \"" . addslashes($action) . "\", \"$version\", $errorCode);";
		
		$DB_WE->query($query);
	}
	
	/**
	 * updates the database with given dump.
	 *
	 * @param string $query
	 */
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

		// first of all we need to check if there is a tblPrefix
		if (LIVEUPDATE_TABLE_PREFIX) {

			$query = preg_replace("/^INSERT INTO /", "INSERT INTO " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^CREATE TABLE /", "CREATE TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
		}

		// second, we need to check if there is a collation
		if (defined("DB_CHARSET") && DB_CHARSET != "" && defined("DB_COLLATION") && DB_COLLATION != "") {
			if(eregi("^CREATE TABLE ", $query)) {
				$Charset = DB_CHARSET;
				$Collation = DB_COLLATION;
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
						$addKeys = array();
						foreach ($newTableKeys as $keyName => $indexes) {

							if (isset($origTableKeys[$keyName])) {

								for ($i=0;$i<sizeof($indexes);$i++) {
									if (!in_array($indexes[$i], $origTableKeys[$keyName])) {
										$addKeys[$keyName][] = $indexes[$i];
									}
								}
							} else {
								$addKeys[$keyName] = $indexes;
							}
						}

						// get all queries to add/change fields, keys
						$alterQueries = array();

						// get all queries to change existing fields
						if (sizeof($addFields)) {
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForFields($addFields, $tableName, true));
						}
						
						// get all queries to change existing keys
						if (sizeof($addKeys)) {
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForKeys($addKeys, $tableName, true));
						}
						
						if (sizeof($changeFields)) {
							$alterQueries = array_merge($alterQueries, $this->getAlterTableForFields($changeFields, $tableName));
						}

						if (sizeof($alterQueries)) {
							// execute all queries
							$success = true;
							foreach ($alterQueries as $_query) {

								if ($db->query(trim($_query))) {
									$this->QueryLog['success'][] = $_query;
								} else {
									$this->QueryLog['error'][] = $db->Errno . ' ' . $db->Error . "\n<!-- $_query -->";
									$success = false;
								}
							}
							if ($success) {
								$this->QueryLog['tableChanged'][] = $tableName . "\n<!--$query-->";
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
					$this->QueryLog['error'][] = $db->Errno . ' ' . $db->Error . "\n<!-- $query -->";
					return false;
				break;
			}
			return false;
		}
		return true;
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
			
			$indexType = '';
			
			if ($db->f('Key_name') == 'PRIMARY') {
				$indexType = 'PRIMARY';
			} else if ( $db->f('Comment') == 'FULLTEXT' || $db->f('Index_type') == 'FULLTEXT' ) {// this also depends from mysqlVersion
				$indexType = 'FULLTEXT';
			} else if ( $db->f('Non_unique') == '0' ) {
				$indexType = 'UNIQUE';
			} else {
				$indexType = 'INDEX';
			}
			
			if (!isset($keysOfTable[$db->f('Column_name')]) || !in_array($indexType, $keysOfTable[$db->f('Column_name')])) {
				$keysOfTable[$db->f('Column_name')][] = $indexType;
			}
		}
		
		return $keysOfTable;
	}
}

?>