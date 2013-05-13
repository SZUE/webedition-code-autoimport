<?php
class liveUpdateFunctionsServer extends liveUpdateFunctions {
	
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

			if ($queryArray = file($path)) {

				foreach ($queryArray as $query) {

					if (trim($query)) {
						if (!$this->executeUpdateQuery($query)) {
							$success = false;
						}
					}
				}
			}

		} else {
			$content = $this->getFileContent($path);
			$queries = explode("/* query separator */",$content);
			//$success = $this->executeUpdateQuery($content);
			$success = true;
			foreach($queries as $query) {
				$success = $this->executeUpdateQuery($query);
				if(!$success) $success = false;
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

		// first of all we need to check if there is a tblPrefix
		if (LIVEUPDATE_TABLE_PREFIX) {

			$query = preg_replace("/^INSERT INTO /", "INSERT INTO " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^CREATE TABLE /", "CREATE TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^DELETE FROM /", "DELETE FROM " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^ALTER TABLE /", "ALTER TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^RENAME TABLE /", "RENAME TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^TRUNCATE TABLE /", "TRUNCATE TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			$query = preg_replace("/^DROP TABLE /", "DROP TABLE " . LIVEUPDATE_TABLE_PREFIX, $query, 1);
			
			$query = @str_replace(LIVEUPDATE_TABLE_PREFIX.'`', '`'.LIVEUPDATE_TABLE_PREFIX, $query);
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
}

?>