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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$notprotect = isset($GLOBALS['NOT_PROTECT']) && $GLOBALS['NOT_PROTECT'] && (!isset($_REQUEST['NOT_PROTECT']));

if(!$notprotect){
	we_html_tools::protect();
}

function deleteTreeEntries($dontDeleteClassFolders = false){
	return weTree::deleteTreeEntries($dontDeleteClassFolders);
}

function checkDeleteEntry($id, $table){
	if($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE)){
		return true;
	}
	return (f('SELECT IsFolder FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE  ID=' . intval($id))?
			checkDeleteFolder($id, $table) :
			checkDeleteFile($id, $table));
}

function checkDeleteFolder($id, $table){
	if($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE)){
		return true;
	}

	$DB_WE = $GLOBALS['DB_WE'];
	$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($id));
	while($DB_WE->next_record()){
		if(!checkDeleteEntry($DB_WE->f('ID'), $table)){
			return false;
		}
	}
	return true;
}

function checkDeleteFile($id, $table){
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

function makeAlertDelFolderNotEmpty($folders){
	return sprintf(g_l('alert', '[folder_not_empty]'), implode("\n", $folders) . "\n");
}

function deleteFolder($id, $table, $path = '', $delR = true, we_database_base $DB_WE = null){
	$isTemplateFolder = ($table == TEMPLATES_TABLE);

	$DB_WE = ($DB_WE ? $DB_WE : new DB_WE());
	$path = $path ? $path : f('SELECT Path FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), 'Path', $DB_WE);

	if($delR){ // recursive delete
		$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($id));
		$toDeleteArray = array();
		while($DB_WE->next_record()){
			$toDeleteArray[] = $DB_WE->f('ID');
		}
		foreach($toDeleteArray as $toDelete){
			deleteEntry($toDelete, $table, true, 0, $DB_WE);
		}
	}

	// do not delete class folder if class still exists!!!
	if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE){
		if(f('SELECT IsClassFolder FROM ' . $table . ' WHERE ID=' . intval($id), 'IsClassFolder', $DB_WE)){ // it is a class folder
			if(f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '"', 'Path', $DB_WE)){ // class still exists
				return;
			}
		}
	}
	// Fast Fix for deleting entries from tblLangLink: #5840
	if($DB_WE->query('DELETE FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id))){
		$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $DB_WE->escape($table) . '" AND IsObject=' . ($table == FILE_TABLE ? 0 : 1) . ' AND IsFolder=1 AND DID=' . intval($id));
	}

	deleteContentFromDB($id, $table, $DB_WE);
	if(substr($path, 0, 3) == '/..'){
		return;
	}
	$file = ((!$isTemplateFolder) ? $_SERVER['DOCUMENT_ROOT'] : TEMPLATES_PATH) . $path;
	if($table == TEMPLATES_TABLE || $table == FILE_TABLE){
		if(!we_util_File::deleteLocalFolder($file)){
			if(isset($GLOBALS['we_folder_not_del']) && is_array($GLOBALS['we_folder_not_del'])){
				$GLOBALS['we_folder_not_del'][] = $file;
			}
		}
	}
	switch($table){
		case FILE_TABLE:
			$file = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($path, 1);
			we_util_File::deleteLocalFolder($file, 1);
			break;
		case (defined('OBJECT_TABLE') && defined('OBJECT_FILES_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
			if(($ofID = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '"', 'ID', $DB_WE))){
				deleteEntry($ofID, OBJECT_FILES_TABLE, true, 0, $DB_WE);
			}
			break;
	}
}

function deleteFile($id, $table, $path = '', $contentType = '', we_database_base $DB_WE = null){
	$DB_WE = $DB_WE ? $DB_WE : new DB_WE();

	$isTemplateFile = ($table == TEMPLATES_TABLE);

	$path = $path ? $path : f('SELECT Path FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), 'Path', $DB_WE);
	deleteContentFromDB($id, $table);

	$file = ((!$isTemplateFile) ? $_SERVER['DOCUMENT_ROOT'] : TEMPLATES_PATH) . $path;

	switch($table){
		case FILE_TABLE:
			we_util_File::deleteLocalFile($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($path, 1));
		//no break
		case TEMPLATES_TABLE:
			we_util_File::deleteLocalFile(preg_replace('/\.tmpl$/i', '.php', $file));
			break;
	}

	we_temporaryDocument::delete($id, $table, $DB_WE);

	switch($table){
		case FILE_TABLE:
			$DB_WE->query('UPDATE ' . CONTENT_TABLE . ' SET BDID=0 WHERE BDID=' . intval($id));
			$DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE DID=' . intval($id));

			if(we_base_moduleInfo::isActive('schedule')){ //	Delete entries from schedule as well
				$DB_WE->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($id) . ' AND ClassName !="we_objectFile"');
			}

			$DB_WE->query('DELETE FROM ' . NAVIGATION_TABLE . ' WHERE Selection="static" AND SelectionType="docLink" AND LinkID=' . intval($id));

			// Fast Fix for deleting entries from tblLangLink: #5840
			$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable = "tblFile" AND IsObject = 0 AND IsFolder = 0 AND DID = ' . intval($id));
			$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable = "tblFile" AND LDID = ' . intval($id));
			break;
		case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
			$DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE OID=' . intval($id));
			$tableID = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id), 'TableID', $DB_WE);
			if(!empty($tableID)){
				$DB_WE->query('DELETE FROM ' . OBJECT_X_TABLE . $tableID . ' WHERE OF_ID=' . intval($id));
				//Bug 2892
				$DB_WE->query('SELECT ID FROM ' . OBJECT_TABLE);
				$foo = $DB_WE->getAll(true);
				foreach($foo as $testclassID){
					if($DB_WE->isColExist(OBJECT_X_TABLE . $testclassID, we_object::QUERY_PREFIX . $tableID)){

						//das loeschen in der DB wirkt sich nicht auf die Objekte aus, die noch nicht publiziert sind
						$DB_WE->query('SELECT OF_ID FROM ' . OBJECT_X_TABLE . $testclassID . ' WHERE ' . we_object::QUERY_PREFIX . $tableID . '= ' . intval($id));
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
						$DB_WE->query('UPDATE ' . OBJECT_X_TABLE . $testclassID . ' SET ' . we_object::QUERY_PREFIX . $tableID . '=0 WHERE ' . we_object::QUERY_PREFIX . $tableID . '= ' . intval($id));
					}
				}
				// Fast Fix for deleting entries from tblLangLink: #5840
				$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable = "tblObjectFile" AND DID = ' . intval($id));
				$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable = "tblObjectFile" AND LDID = ' . intval($id));
			}
			if(we_base_moduleInfo::isActive('schedule')){ //	Delete entries from schedule as well
				$DB_WE->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($id) . ' AND ClassName="we_objectFile"');
			}
			break;
	}

	$DB_WE->query('DELETE FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id));
	if(defined('OBJECT_TABLE') && $table == OBJECT_TABLE){
		$ofID = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '"', 'ID', $DB_WE);
		if($ofID){
			deleteEntry($ofID, OBJECT_FILES_TABLE, true, 0, $DB_WE);
		}
		$DB_WE->query('DROP TABLE IF EXISTS ' . OBJECT_X_TABLE . intval($id));
	}
	if($contentType == we_base_ContentTypes::IMAGE){
		we_thumbnail::deleteByImageID($id);
	}
}

function deleteThumbsByImageID($id){
	we_thumbnail::deleteByImageID($id);
}

function deleteThumbsByThumbID($id){
	we_thumbnail::deleteByThumbID($id);
}

function deleteEntry($id, $table, $delR = true, $skipHook = 0, we_database_base $DB_WE = null){

	$DB_WE = ($DB_WE ? $DB_WE : new DB_WE());
	if(defined('WORKFLOW_TABLE') && ($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE))){
		if(we_workflow_utility::inWorkflow($id, $table)){
			we_workflow_utility::removeDocFromWorkflow($id, $table, $_SESSION['user']['ID'], g_l('modules_workflow', '[doc_deleted]'));
		}
	}
	if($id){
		$row = getHash('SELECT Path,IsFolder,ContentType FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
		if(!$row){
			$GLOBALS['deletedItems'][] = $id;
			return;
		}
		$ct = weVersions::getContentTypesVersioning();
		//no need to init doc, if no version is needed or hook is executed
		if(in_array($row['ContentType'], $ct) || !$skipHook){
			$object = weContentProvider::getInstance($row['ContentType'], $id, $table);
		}
		if(in_array($row['ContentType'], $ct)){
			$version = new weVersions();
			$version_exists = $version->getLastEntry($id, $table);
			if(empty($version_exists)){
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
		if(!empty($row)){
			if($row['IsFolder']){
				deleteFolder($id, $table, $row['Path'], $delR, $DB_WE);
			} else {
				deleteFile($id, $table, $row['Path'], $row['ContentType'], $DB_WE);
			}
		}
		$GLOBALS['deletedItems'][] = $id;
	}
}
