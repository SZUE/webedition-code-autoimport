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
abstract class we_base_delete{

	public static function checkDeleteEntry($id, $table){
		if($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE)){
			return true;
		}
		return (f('SELECT IsFolder FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE  ID=' . intval($id)) ?
				self::checkDeleteFolder($id, $table) :
				self::checkDeleteFile($id, $table));
	}

	private static function checkDeleteFolder($id, $table){
		if($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE)){
			return true;
		}

		$DB_WE = $GLOBALS['DB_WE'];
		$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($id));
		while($DB_WE->next_record()){
			if(!self::checkDeleteEntry($DB_WE->f('ID'), $table)){
				return false;
			}
		}
		return true;
	}

	private static function checkDeleteFile($id, $table){
		switch($table){
			case FILE_TABLE:
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				return true;
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				return !(we_object::isUsedByObjectFile($id));
			case TEMPLATES_TABLE:
				$arr = we_rebuild_base::getTemplAndDocIDsOfTemplate($id, false, false, true);
				return (empty($arr["documentIDs"]));
		}
		return true;
	}

	private static function deleteFolder($id, $table, $path = '', $delR = true, we_database_base $DB_WE = null){
		$isTemplateFolder = ($table == TEMPLATES_TABLE);

		$DB_WE = ($DB_WE ? : new DB_WE());
		$path = $path ? : f('SELECT Path FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), '', $DB_WE);

		if($delR){ // recursive delete
			$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($id));
			$toDeleteArray = array();
			while($DB_WE->next_record()){
				$toDeleteArray[] = $DB_WE->f('ID');
			}
			foreach($toDeleteArray as $toDelete){
				self::deleteEntry($toDelete, $table, true, 0, $DB_WE);
			}
		}

		// do not delete class folder if class still exists!!!
		if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE){
			if(f('SELECT IsClassFolder FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), '', $DB_WE)){ // it is a class folder
				if(f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '"', '', $DB_WE)){ // class still exists
					return;
				}
			}
		}
		// Fast Fix for deleting entries from tblLangLink: #5840
		if($DB_WE->query('DELETE FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id))){
			$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $DB_WE->escape($table) . '" AND IsObject=' . ($table == FILE_TABLE ? 0 : 1) . ' AND IsFolder=1 AND DID=' . intval($id));
		}

		self::deleteContentFromDB($id, $table, $DB_WE);
		if(substr($path, 0, 3) === '/..'){
			return;
		}
		$file = ((!$isTemplateFolder) ? $_SERVER['DOCUMENT_ROOT'] : TEMPLATES_PATH) . $path;
		if($table == TEMPLATES_TABLE || $table == FILE_TABLE){
			if(!we_base_file::deleteLocalFolder($file)){
				if(isset($GLOBALS['we_folder_not_del']) && is_array($GLOBALS['we_folder_not_del'])){
					$GLOBALS['we_folder_not_del'][] = $file;
				}
			}
		}
		switch($table){
			case FILE_TABLE:
				$file = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($path, 1);
				we_base_file::deleteLocalFolder($file, 1);
				break;
			case (defined('OBJECT_TABLE') && defined('OBJECT_FILES_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				if(($ofID = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '"', 'ID', $DB_WE))){
					self::deleteEntry($ofID, OBJECT_FILES_TABLE, true, 0, $DB_WE);
				}
				break;
		}
	}

	private static function deleteFile($id, $table, $path = '', $contentType = '', we_database_base $DB_WE = null){
		$DB_WE = $DB_WE ? : new DB_WE();

		$isTemplateFile = ($table == TEMPLATES_TABLE);

		$path = $path ? : f('SELECT Path FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), 'Path', $DB_WE);
		self::deleteContentFromDB($id, $table);

		$file = ((!$isTemplateFile) ? $_SERVER['DOCUMENT_ROOT'] : TEMPLATES_PATH) . $path;

		switch($table){
			case FILE_TABLE:
				we_base_file::deleteLocalFile($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($path, 1));
			//no break
			case TEMPLATES_TABLE:
				we_base_file::deleteLocalFile(preg_replace('/\.tmpl$/i', '.php', $file));
				break;
		}

		we_temporaryDocument::delete($id, $table, $DB_WE);

		switch($table){
			case FILE_TABLE:
				$DB_WE->query('UPDATE ' . CONTENT_TABLE . ' c JOIN '.LINK_TABLE.' l ON c.ID=l.CID SET BDID=0 WHERE l.Type IN ("href","img") AND c.BDID=' . intval($id));
				$DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE DID=' . intval($id));

				if(we_base_moduleInfo::isActive('schedule')){ //	Delete entries from schedule as well
					$DB_WE->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($id) . ' AND ClassName!="we_objectFile"');
				}

				$DB_WE->query('DELETE FROM ' . NAVIGATION_TABLE . ' WHERE Selection="static" AND SelectionType="' . we_navigation_navigation::STPYE_DOCLINK . '" AND LinkID=' . intval($id));

				// Fast Fix for deleting entries from tblLangLink: #5840
				$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblFile" AND IsObject=0 AND IsFolder=0 AND DID=' . intval($id));
				$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblFile" AND LDID=' . intval($id));
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE OID=' . intval($id));
				$tableID = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsClassFolder=0 AND ID=' . intval($id), '', $DB_WE);
				if($tableID){
					$DB_WE->query('DELETE FROM ' . OBJECT_X_TABLE . intval($tableID) . ' WHERE OF_ID=' . intval($id));
					$DB_WE->query('DELETE FROM ' . NAVIGATION_TABLE . ' WHERE Selection="static" AND SelectionType="' . we_navigation_navigation::STPYE_OBJLINK . '" AND LinkID=' . intval($id));
					//Bug 2892
					$DB_WE->query('SELECT ID FROM ' . OBJECT_TABLE);
					$foo = $DB_WE->getAll(true);
					foreach($foo as $testclassID){
						if($DB_WE->isColExist(OBJECT_X_TABLE . $testclassID, we_object::QUERY_PREFIX . $tableID)){

							//das loeschen in der DB wirkt sich nicht auf die Objekte aus, die noch nicht publiziert sind
							$DB_WE->query('SELECT OF_ID FROM ' . OBJECT_X_TABLE . intval($testclassID) . ' WHERE ' . we_object::QUERY_PREFIX . $tableID . '= ' . intval($id));
							$foos = $DB_WE->getAll(true);
							foreach($foos as $affectedobjectsID){
								$obj = new we_objectFile();
								$obj->initByID($affectedobjectsID, OBJECT_FILES_TABLE);

								$obj->getContentDataFromTemporaryDocs($affectedobjectsID);
								$oldModDate = $obj->ModDate;
								$obj->setElement('we_object_' . $tableID, 0);
								$obj->we_save(0, 1);
								if($obj->Published != 0 && $obj->Published == $oldModDate){
									$obj->we_publish(0, 1, 1);
								}
							}
							$DB_WE->query('UPDATE ' . OBJECT_X_TABLE . intval($testclassID) . ' SET ' . we_object::QUERY_PREFIX . $tableID . '=0 WHERE ' . we_object::QUERY_PREFIX . $tableID . '=' . intval($id));
						}
					}
					// Fast Fix for deleting entries from tblLangLink: #5840
					$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblObjectFile" AND DID=' . intval($id));
					$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblObjectFile" AND LDID=' . intval($id));
				}
				if(we_base_moduleInfo::isActive('schedule')){ //	Delete entries from schedule as well
					$DB_WE->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($id) . ' AND ClassName="we_objectFile"');
				}
				break;
		}

		$DB_WE->query('DELETE FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id));
		if(defined('OBJECT_TABLE') && $table == OBJECT_TABLE){
			$ofID = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '"', '', $DB_WE);
			if($ofID){
				self::deleteEntry($ofID, OBJECT_FILES_TABLE, true, 0, $DB_WE);
			}
			$DB_WE->query('DROP TABLE IF EXISTS ' . OBJECT_X_TABLE . intval($id));
		}
		if($contentType == we_base_ContentTypes::IMAGE){
			we_thumbnail::deleteByImageID($id);
		}
	}

	public static function deleteEntry($id, $table, $delR = true, $skipHook = false, we_database_base $DB_WE = null){
		$DB_WE = ($DB_WE ? : new DB_WE());
		if(defined('WORKFLOW_TABLE') && ($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE))){
			if(we_workflow_utility::inWorkflow($id, $table)){
				we_workflow_utility::removeDocFromWorkflow($id, $table, $_SESSION['user']['ID'], g_l('modules_workflow', '[doc_deleted]'));
			}
		}
		if($id){
			if($table != USER_TABLE && ( (defined('CUSTOMER_TABLE') && $table != CUSTOMER_TABLE) || !defined('CUSTOMER_TABLE'))){
				$row = getHash('SELECT Path,IsFolder,ContentType FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
			} else {
				$row = getHash('SELECT Path,IsFolder FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
				$row['ContentType']='internalModule';
			}
			if(!$row){
				$GLOBALS['deletedItems'][] = $id;
				return;
			}
			$ct = we_versions_version::getContentTypesVersioning();
			//no need to init doc, if no version is needed or hook is executed
			if(in_array($row['ContentType'], $ct) || !$skipHook){
				$object = we_exim_contentProvider::getInstance($row['ContentType'], $id, $table);
			}
			if(in_array($row['ContentType'], $ct)){
				$version = new we_versions_version();
				if(!we_versions_version::versionExists($id, $table)){
					$version->saveVersion($object);
				}

				$version->setVersionOnDelete($id, $table, $row['ContentType'], $DB_WE);
			}
			/* hook */
			if(!$skipHook){
				$hook = new weHook('delete', '', array($object));
				$hook->executeHook();
			}

			we_temporaryDocument::delete($id, $table, $DB_WE);

			update_time_limit(30);
			if($row){
				if($row['IsFolder']){
					self::deleteFolder($id, $table, $row['Path'], $delR, $DB_WE);
				} else {
					self::deleteFile($id, $table, $row['Path'], $row['ContentType'], $DB_WE);
				}
			}
			$GLOBALS['deletedItems'][] = $id;
		}
	}

	/**
	 * @internal
	 * @param type $id
	 * @param type $table
	 * @return bool true on success, or if not in DB
	 */
	public static function deleteContentFromDB($id, $table, we_database_base $DB_WE = null){
		$DB_WE = $DB_WE ? : new DB_WE();

		if(!f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '" LIMIT 1', '', $DB_WE)){
			return true;
		}

		$DB_WE->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID IN (
		SELECT CID FROM ' . LINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '")');
		return $DB_WE->query('DELETE FROM ' . LINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '"');
	}

}
