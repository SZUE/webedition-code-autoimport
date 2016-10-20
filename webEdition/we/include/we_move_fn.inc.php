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
	return moveItem($targetDirectoryID, [$id], $table, $notMovedItems);
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
		$parentID = intval($targetDirectoryID);
		$newPath = f('SELECT Path FROM ' . $DB_WE->escape($table) . ' WHERE IsFolder=1 AND ID=' . $parentID, '', $DB_WE);
		if(!$newPath){
			return false;
		}
	} else {
		$newPath = '';
		$parentID = 0;
	}

	$allIds = implode(',', $ids);
	unset($ids);

	//move folders first
	$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($table) . ' WHERE IsFolder=1 AND ID IN (' . $allIds . ') AND ParentID NOT IN(' . $allIds . ') ' .
		(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE ? ' AND IsClassFolder=0' : '')
		. ' ORDER BY Path');
	$ids = $DB_WE->getAll(true);
	foreach($ids as $id){
		$folder = (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE ? //4076
				new we_class_folder() :
				new we_folder());
		$folder->initByID($id, $table);
		$folder->ParentID = $targetDirectoryID;
		$folder->Path = $folder->getPath();
		if(!$folder->save()){
			$notMovedItems[] = ['ID' => $folder->ID,
				'Text' => $folder->Text,
				'Path' => $folder->Path,
				'ContentType' => $folder->ContentType
				];
		}
	}
	//if folders are unable to move, we must stop here.
	if(!empty($notMovedItems)){
		return false;
	}

//continue with single files
	$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($table) . ' WHERE IsFolder=0 AND ID IN (' . $allIds . ') AND ParentID NOT IN(' . $allIds . ') ' .
		(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE ? ' AND IsClassFolder=0' : '')
		. ' ORDER BY Path');
	$ids = $DB_WE->getAll(true);

	switch($table){
		case TEMPLATES_TABLE:
			// bugfix 0001643
			foreach($ids as $id){
				$template = new we_template();
				$template->initByID($id, TEMPLATES_TABLE);
				$template->ParentID = $targetDirectoryID;
				if(!$template->save()){
					$notMovedItems[] = ['ID' => $template->ID,
						'Text' => $template->Text,
						'Path' => $template->Path,
						'ContentType' => $template->ContentType
						];
				}
			}
			break;
		case FILE_TABLE:
			foreach($ids as $id){
				// get information about the document which has to be moved
				$row = getHash('SELECT Text,Path,Published,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), $DB_WE);
				$fileName = $row['Text'];
				$oldPath = $row['Path'];
				$isPublished = ($row['Published'] ? true : false);
				if(
				// move document file
					(file_exists($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $oldPath) && !we_base_file::moveFile($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $oldPath, $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $newPath . '/' . $fileName)) ||
					// move published document file
					(($isPublished) && file_exists($_SERVER['DOCUMENT_ROOT'] . $oldPath) && !we_base_file::moveFile($_SERVER['DOCUMENT_ROOT'] . $oldPath, $_SERVER['DOCUMENT_ROOT'] . $newPath . '/' . $fileName))){
					$notMovedItems[] = ['ID' => $id,
						'Text' => $fileName,
						'Path' => $oldPath,
						'ContentType' => $row['ContentType']
						];
					continue;
				}

				if(we_versions_version::CheckPreferencesCtypes($row['ContentType'])){
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
				$DB_WE->query('UPDATE ' . FILE_TABLE . ' SET ' . we_database_base::arraySetter(['ParentID' => intval($parentID),
						'Path' => $newPath . '/' . $fileName
						]) . ' WHERE ID=' . intval($id));
			}
			break;

		// move Objects
		case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
			foreach($ids as $id){

//FIME: check no classfolder (top level element is moved)
				// get information about the object which has to be moved
				$row = getHash('SELECT TableID,Path,Text,ContentType FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id), $DB_WE);

				$oldPath = $row['Path'];
				$fileName = $row['Text'];


				if(we_versions_version::CheckPreferencesCtypes($row['ContentType'])){
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
				//$DB_WE->query('UPDATE ' . OBJECT_X_TABLE . intval($row['TableID']) . ' SET OF_ParentID=' . intval($parentID) . ', OF_Path="' . $DB_WE->escape($newPath . '/' . $fileName) . '" WHERE OF_ID=' . intval($id));
			}
			break;
	}


	return empty($notMovedItems);
}
