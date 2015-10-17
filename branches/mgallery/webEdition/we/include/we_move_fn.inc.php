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
$notprotect = !empty($GLOBALS["NOT_PROTECT"]) && (!isset($_REQUEST["NOT_PROTECT"]));

if(!$notprotect){
	we_html_tools::protect();
}

function moveTreeEntries($dontMoveClassFolders = false){//FIXME: check if js function is duplicate in tree.js
	return 'var obj = top.treeData;
var cont = new top.container();
for(var i=1;i<=obj.len;i++){
	if(obj[i].checked!=1 ' . ($dontMoveClassFolders ? ' || obj[i].parentid==0' : '') . '){
		if(obj[i].parentid != 0){
			if(!top.treeData.parentChecked(obj[i].parentid)){
				cont.add(obj[i]);
			}
		}else{
			cont.add(obj[i]);
		}
	}
}
top.treeData = cont;
top.drawTree();
';
}

function checkMoveItem($DB_WE, $targetDirectoryID, $id, $table, &$items2move){
	// check if entry is a folder
	$row = getHash('SELECT Path,Text,IsFolder FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
	if(!$row /* || $row['IsFolder'] */){
		return -1;
	}

	$text = $row['Text'];
	$temp = explode('/', $row['Path']);
	$rootdir = (count($temp) < 2 ? '/' : '/' . $temp[1]);

	// add the item to the item names which could be moved
	$items2move[] = $text;

	$DB_WE->query('SELECT Text,Path FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($targetDirectoryID));
	while($DB_WE->next_record()){
		// check if there is a item with the same name in the target directory
		if(in_array($DB_WE->f('Text'), $items2move)){
			return -2;
		}

		if(defined('OBJECT_TABLE') && $table == OBJECT_FILES_TABLE){
			// check if class directory is the same
			if(substr($DB_WE->f('Path'), 0, strlen($rootdir) + 1) != $rootdir . '/'){
				return -3;
			}
		}
	}
	return 1;
}

function moveItem($targetDirectoryID, $id, $table, &$notMovedItems){
	if(!$id){
		return false;
	}
	return moveItem($targetDirectoryID, array($id), $table, $notMovedItems);
}

function moveItems($targetDirectoryID, array $ids, $table, &$notMovedItems){
	if(!$ids){
		return false;
	}
	$DB_WE = new DB_WE();

	// get information about the target directory
	if(defined('OBJECT_TABLE') && $table == OBJECT_TABLE && !$targetDirectoryID){
		return false;
	}
	if($targetDirectoryID){
		$row = getHash('SELECT IsFolder,Path,ID FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($targetDirectoryID), $DB_WE);
		if(!$row /* || !$row["IsFolder"] */){
			return false;
		}
		$newPath = $row['Path'];
		$parentID = $row['ID'];
	} else {
		$newPath = '';
		$parentID = 0;
	}

	foreach($ids as $id){
		// move Templates
		switch($table){
			case TEMPLATES_TABLE:
				// bugfix 0001643
				$_template = new we_template();
				$_template->initByID($id, $_template->Table);
				$_template->ParentID = $targetDirectoryID;
				if(!$_template->save()){
					$notMovedItems[] = array(
						'ID' => $_template->ID,
						'Text' => $_template->Text,
						'Path' => $_template->Path,
						'ContentType' => $_template->ContentType
					);
				}
				continue;
			// move documents
			case FILE_TABLE:
				// get information about the document which has to be moved
				$row = getHash('SELECT Text,Path,Published,IsFolder,ContentType FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
				$fileName = $row['Text'];
				$oldPath = $row['Path'];
				$isPublished = ($row['Published'] ? true : false);
				$isFolder = ($row['IsFolder'] ? true : false);
				if(!$row ||
					/* $isFolder || */
					// move document file
					(file_exists($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $oldPath) && !we_base_file::moveFile($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $oldPath, $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $newPath . '/' . $fileName)) ||
					// move published document file
					(($isPublished || $isFolder) && file_exists($_SERVER['DOCUMENT_ROOT'] . $oldPath) && !we_base_file::moveFile($_SERVER['DOCUMENT_ROOT'] . $oldPath, $_SERVER['DOCUMENT_ROOT'] . $newPath . '/' . $fileName))){
					$notMovedItems[] = array('ID' => $id, 'Text' => $fileName, 'Path' => $oldPath, 'ContentType' => $row['ContentType']);
					continue;
				}

				if(!$isFolder && we_versions_version::CheckPreferencesCtypes($row['ContentType'])){
					$version = new we_versions_version();
					if(!we_versions_version::versionExists($id, $table)){
						$object = we_exim_contentProvider::getInstance($row['ContentType'], $id, $table);
						$object->Path = $newPath . '/' . $fileName;
						$object->ParentID = $parentID;
						$version->saveVersion($object);
					} else {
						we_versions_version::updateLastVersionPath($id, $table, $parentID, $newPath . '/' . $fileName);
					}
				}

				// update table
				$DB_WE->query('UPDATE ' . $DB_WE->escape($table) . ' SET ' . we_database_base::arraySetter(array(
						'ParentID' => intval($parentID),
						'Path' => $newPath . '/' . $fileName
					)) . ' WHERE ID=' . intval($id));

				continue;

			// move Objects
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
//FIME: check no classfolder (top level element is moved)
				// get information about the object which has to be moved
				$row = getHash('SELECT TableID,Path,Text,IsFolder,IsClassFolder,ContentType FROM ' . OBJECT_FILES_TABLE . ' WHERE IsClassFolder=0 AND ID=' . intval($id), $DB_WE);

				if(!$row || $row['IsClassFolder']){
					$notMovedItems[] = array(
						'ID' => $id,
						'Text' => ($row ? $row['Text'] : ''),
						'Path' => ($row ? $row['Path'] : ''),
						'ContentType' => ($row ? $row['ContentType'] : '')
					);
					continue;
				}
				$tableID = $row['TableID'];
				$oldPath = $row['Path'];
				$fileName = $row['Text'];
				$isFolder = $row['IsFolder'] == 1;


				if(!$isFolder && we_versions_version::CheckPreferencesCtypes($row['ContentType'])){
					$version = new we_versions_version();
					if(!we_versions_version::versionExists($id, $table)){
						$object = we_exim_contentProvider::getInstance($row['ContentType'], $id, $table);
						$object->Path = $newPath . '/' . $fileName;
						$object->ParentID = $parentID;
						$version->saveVersion($object);
					} else {
						we_versions_version::updateLastVersionPath($id, $table, $parentID, $newPath . '/' . $fileName);
					}
				}

				// update table
				$DB_WE->query('UPDATE ' . $DB_WE->escape($table) . ' SET ParentID=' . intval($parentID) . ', Path="' . $DB_WE->escape($newPath . '/' . $fileName) . '" WHERE ID=' . intval($id));
				$DB_WE->query('UPDATE ' . OBJECT_X_TABLE . intval($tableID) . ' SET OF_ParentID=' . intval($parentID) . ', OF_Path="' . $DB_WE->escape($newPath . '/' . $fileName) . '" WHERE OF_ID=' . intval($id));

				continue;
		}
	}
	return empty($notMovedItems);
}
