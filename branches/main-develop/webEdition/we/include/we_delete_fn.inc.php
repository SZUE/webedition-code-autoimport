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
include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_inc_min.inc.php');

$notprotect = isset($GLOBALS["NOT_PROTECT"]) && $GLOBALS["NOT_PROTECT"] && (!isset($_REQUEST["NOT_PROTECT"]));

if(!$notprotect){
	we_html_tools::protect();
}

function deleteTreeEntries($dontDeleteClassFolders = false){
	return '
		var obj = top.treeData;
		var cont = new top.container();
		for(var i=1;i<=obj.len;i++){
			if(obj[i].checked!=1 ' . ($dontDeleteClassFolders ? ' || obj[i].parentid==0' : '') . '){
				if(obj[i].parentid != 0){
					if(!parentChecked(obj[i].parentid)){
						cont.add(obj[i]);
					}
				}else{
					cont.add(obj[i]);
				}
			}
		}
		top.treeData = cont;
		top.drawTree();

		function parentChecked(start){
			var obj = top.treeData;
			for(var i=1;i<=obj.len;i++){
				if(obj[i].id == start){
					if(obj[i].checked==1) return true;
					else if(obj[i].parentid != 0) parentChecked(obj[i].parentid);
				}
			}

			return false;
		}';
}

function checkDeleteEntry($id, $table){
	if($table == FILE_TABLE || (defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE))
		return true;
	$row = getHash("SELECT IsFolder FROM " . $GLOBALS['DB_WE']->escape($table) . " WHERE  ID=" . intval($id), $GLOBALS['DB_WE']);
	if(isset($row["IsFolder"]) && $row["IsFolder"]){
		return checkDeleteFolder($id, $table);
	} else{
		return checkDeleteFile($id, $table);
	}
}

function checkDeleteFolder($id, $table){
	if($table == FILE_TABLE || (defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE))
		return true;

	$DB_WE = new DB_WE();
	$DB_WE->query("SELECT * FROM $table WHERE ParentID=" . intval($id));
	while($DB_WE->next_record()) {
		if(!checkDeleteEntry($DB_WE->f("ID"), $table)){
			return false;
		}
	}
	return true;
}

function checkDeleteFile($id, $table, $path = ""){
	if($table == FILE_TABLE || (defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE))
		return true;

	if(defined("OBJECT_TABLE") && $table == OBJECT_TABLE){
		if(ObjectUsedByObjectFile($id, false)){
			return false;
		}
	} else
	if($table == TEMPLATES_TABLE){
		$arr = getTemplAndDocIDsOfTemplate($id, false, false, true);
		if(count($arr["documentIDs"]) > 0){
			return false;
		}
	}
	return true;
}

function makeAlertDelFolderNotEmpty($folders){
	$txt = "";
	for($i = 0; $i < sizeof($folders); $i++){
		$txt .= $folders[$i] . "\\n";
	}
	return sprintf(g_l('alert', "[folder_not_empty]"), $txt);
}

function deleteFolder($id, $table, $path = "", $delR = true){

	$isTemplateFolder = ($table == TEMPLATES_TABLE);

	$DB_WE = new DB_WE();
	$path = $path ? $path : f("SELECT Path FROM $table WHERE ID=" . intval($id), "Path", $DB_WE);

	if($delR){ // recursive delete
		$DB_WE->query("SELECT * FROM $table WHERE ParentID=" . intval($id));
		while($DB_WE->next_record()) {
			deleteEntry($DB_WE->f("ID"), $table);
		}
	}

	// do not delete class folder if class still exists!!!
	if(defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE){
		if(f("SELECT IsClassFolder FROM $table WHERE ID=$id", "IsClassFolder", $DB_WE)){ // it is a class folder
			if(f("SELECT Path FROM " . OBJECT_TABLE . " WHERE Path='" . $DB_WE->escape($path) . "'", "Path", $DB_WE)){ // class still exists
				return;
			}
		}
	}
	$DB_WE->query("DELETE FROM $table WHERE ID=" . intval($id));

	deleteContentFromDB($id, $table);
	if(substr($path, 0, 3) == "/.."){
		return;
	}
	$file = ((!$isTemplateFolder) ? $_SERVER['DOCUMENT_ROOT'] : TEMPLATE_DIR) . $path;
	if($table == TEMPLATES_TABLE || $table == FILE_TABLE){
		if(!we_util_File::deleteLocalFolder($file)){
			if(is_array($GLOBALS["we_folder_not_del"])){
				array_push($GLOBALS["we_folder_not_del"], $file);
			}
		}
	}
	if($table == FILE_TABLE){
		$file = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($path, 1);
		we_util_File::deleteLocalFolder($file, 1);
	}
	if(defined("OBJECT_TABLE") && defined("OBJECT_FILES_TABLE") && $table == OBJECT_TABLE){
		$ofID = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='" . $DB_WE->escape($path) . "'", "ID", $DB_WE);
		if($ofID){
			deleteEntry($ofID, OBJECT_FILES_TABLE);
		}
	}
}

function isColExistForDelete($tab, $col){
	$DB_WE = new DB_WE();
	$DB_WE->query('SHOW COLUMNS FROM ' . $tab . " LIKE '$col'");
	return $DB_WE->next_record();
}

function deleteFile($id, $table, $path = "", $contentType = ""){
	$DB_WE = new DB_WE();

	$isTemplateFile = ($table == TEMPLATES_TABLE);

	$path = $path ? $path : f("SELECT Path FROM $table WHERE ID=" . intval($id), "Path", $DB_WE);
	deleteContentFromDB($id, $table);

	$file = ((!$isTemplateFile) ? $_SERVER['DOCUMENT_ROOT'] : TEMPLATE_DIR) . $path;

	if($table == TEMPLATES_TABLE){
		$file = preg_replace('/\.tmpl$/i', '.php', $file);
	}

	if($table == TEMPLATES_TABLE || $table == FILE_TABLE)
		we_util_File::deleteLocalFile($file);
	if($table == FILE_TABLE){
		$file = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($path, 1);
		we_util_File::deleteLocalFile($file);
	}
	we_temporaryDocument::delete($id, $table, $DB_WE);

	if($table == FILE_TABLE){
		$DB_WE->query("UPDATE " . CONTENT_TABLE . " SET BDID=0 WHERE BDID=" . intval($id));
		$DB_WE->query("DELETE FROM " . INDEX_TABLE . " WHERE DID=" . intval($id));

		if(in_array("schedule", $GLOBALS['_we_active_integrated_modules'])){ //	Delete entries from schedule as well
			$DB_WE->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($id) . ' AND ClassName !="we_objectFile"');
		}
		$DB_WE->query('DELETE FROM ' . NAVIGATION_TABLE . ' WHERE Selection="static" AND SelectionType="docLink" AND LinkID=' . intval($id));
	}

	if(defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE){
		$DB_WE->query("DELETE FROM " . INDEX_TABLE . " WHERE OID=" . intval($id));
		$tableID = f("SELECT TableID FROM " . OBJECT_FILES_TABLE . " WHERE ID=" . intval($id), "TableID", $DB_WE);
		if($tableID != "" || $tableID == "0"){
			$DB_WE->query("DELETE FROM " . OBJECT_X_TABLE . $tableID . " WHERE OF_ID=" . intval($id));
			//Bug 2892
			$q = "SELECT ID FROM " . OBJECT_TABLE . " ";
			$DB_WE->query($q);
			$foo = $DB_WE->getAll();
			foreach($foo as $testclass){
				if(isColExistForDelete(OBJECT_X_TABLE . $testclass['ID'], "object_" . $tableID)){

					//das l�schen in der DB wirkt sich nicht auf die Objekte aus, die noch nicht publiziert sind
					$qtest = "SELECT OF_ID FROM " . OBJECT_X_TABLE . $testclass['ID'] . " WHERE object_" . $tableID . "= " . intval($id);
					$DB_WE->query($qtest);
					$foos = $DB_WE->getAll();
					foreach($foos as $affectedobjects){
						$obj = new we_objectFile();
						$obj->initByID($affectedobjects['OF_ID'], OBJECT_FILES_TABLE);

						$obj->getContentDataFromTemporaryDocs($affectedobjects['OF_ID']);
						$oldModDate = $obj->ModDate;
						$obj->setElement("we_object_" . $tableID, "0");
						$obj->we_save(0, 1);
						if($obj->Published != 0 && $obj->Published == $oldModDate){
							$obj->we_publish(0, 1, 1);
						}
					}

					$q = "UPDATE " . OBJECT_X_TABLE . $testclass['ID'] . " SET object_" . $tableID . "='0' WHERE object_" . $tableID . "= " . intval($id);
					$DB_WE->query($q);
				}
			}
		}
		if(in_array("schedule", $GLOBALS['_we_active_integrated_modules'])){ //	Delete entries from schedule as well
			$DB_WE->query(
				'DELETE FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($id) . ' AND ClassName="we_objectFile"');
		}
	}
	$DB_WE->query("DELETE FROM $table WHERE ID=$id");
	if(defined("OBJECT_TABLE") && $table == OBJECT_TABLE){
		$ofID = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='" . $DB_WE->escape($path) . "'", "ID", $DB_WE);
		if($ofID){
			deleteEntry($ofID, OBJECT_FILES_TABLE);
		}
		$DB_WE->query("DROP TABLE IF EXISTS " . OBJECT_X_TABLE . $id);
	}
	if($contentType == "image/*"){
		deleteThumbsByImageID($id);
	}
}

function deleteThumbsByImageID($id){
	$thumbsdir = we_thumbnail::getThumbDirectory(true);
	$dir_obj = @dir($thumbsdir);
	$filestodelete = array();
	if($dir_obj){
		while(false !== ($entry = $dir_obj->read())) {
			if($entry != '.' && $entry != '..' && substr($entry, 0, strlen($id) + 1) == $id . "_"){
				array_push($filestodelete, $thumbsdir . "/" . $entry);
			}
		}
	}
	$previewDir = $_SERVER['DOCUMENT_ROOT'] . "/webEdition/preview";
	$dir_obj = @dir($previewDir);
	if($dir_obj){
		while(false !== ($entry = $dir_obj->read())) {
			if($entry != '.' && $entry != '..' && (substr($entry, 0, strlen($id) + 1) == $id . "_" || substr(
					$entry, 0, strlen($id) + 1) == $id . ".")){
				array_push($filestodelete, $previewDir . "/" . $entry);
			}
		}
	}
	foreach($filestodelete as $p){
		we_util_File::deleteLocalFile($p);
	}
}

function deleteThumbsByThumbID($id){
	$thumbsdir = we_thumbnail::getThumbDirectory(true);
	$dir_obj = @dir($thumbsdir);
	$filestodelete = array();
	if($dir_obj){
		while(false !== ($entry = $dir_obj->read())) {
			if($entry != '.' && $entry != '..' && preg_match('|^[0-9]+_' . $id . '_(.+)|', $entry)){
				array_push($filestodelete, $thumbsdir . "/" . $entry);
			}
		}
		foreach($filestodelete as $p){
			we_util_File::deleteLocalFile($p);
		}
	}
}

function checkIfRestrictUserIsAllowed($id, $table = FILE_TABLE){

	$DB_WE = new DB_WE();
	$row = getHash("SELECT CreatorID,RestrictOwners,Owners,OwnersReadOnly FROM " . $DB_WE->escape($table) . " WHERE ID=" . intval($id), $DB_WE);
	if((isset($row["CreatorID"]) && $_SESSION["user"]["ID"] == $row["CreatorID"]) || $_SESSION["perms"]["ADMINISTRATOR"]){ //	Owner or admin
		return true;
	}

	if($row["RestrictOwners"]){ //	check which user - group has permission
		$userArray = makeArrayFromCSV($row["Owners"]);

		$_allowedGroup = false;

		//	check if usergroup is allowed
		foreach($_SESSION['user']['groups'] as $nr => $_userGroup){
			if(in_array($_userGroup, $userArray)){
				$_allowedGroup = true;
				break;
			}
		}
		if(!in_array($_SESSION["user"]["ID"], $userArray) && !$_allowedGroup){ //	user is no allowed user.
			return false;
		}

		//	user belongs to owners of document, check if he has only read access !!!


		if($row["OwnersReadOnly"]){

			$arr = unserialize($row["OwnersReadOnly"]);
			if(is_array($arr)){

				if(isset($arr[$_SESSION["user"]["ID"]]) && $arr[$_SESSION["user"]["ID"]]){ //	if user is readonly user -> no delete
					return false;
				} else{ //	user NOT readonly and in restricted -> delete allowed
					if(in_array($_SESSION["user"]["ID"], $userArray)){
						return true;
					}
				}
				//	check if group has rights to delete
				foreach($_SESSION['user']['groups'] as $nr => $_userGroup){ //	user is directly in first group
					if(isset($arr[$_userGroup]) && $arr[$_userGroup]){ //	group not allowed
						return false;
					} else{
						if(in_array($_userGroup, $userArray)){ //	group is NOT readonly and in restricted -> delete allowed
							return true;
						}
					}
				}
			}
		}
	}
	return true;
}

function deleteEntry($id, $table, $delR = true, $skipHook=0){

	$DB_WE = new DB_WE();
	if(defined("WORKFLOW_TABLE") && ($table == FILE_TABLE || (defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE))){
		if(weWorkflowUtility::inWorkflow($id, $table))
			weWorkflowUtility::removeDocFromWorkflow($id, $table, $_SESSION["user"]["ID"], g_l('modules_workflow', '[doc_deleted]'));
	}
	if($id){
		$row = getHash("SELECT Path,IsFolder,ContentType FROM " . $DB_WE->escape($table) . " WHERE ID=" . intval($id), $DB_WE);
		$version = new weVersions();
		$object = weContentProvider::getInstance($row['ContentType'], $id, $table);
		if(in_array($row['ContentType'], $version->contentTypes)){
			$version_exists = $version->getLastEntry($id, $table);
			if(empty($version_exists)){
				$version->saveVersion($object);
			}

			$version->setVersionOnDelete($id, $table, $row['ContentType'], $DB_WE);
		}
		/* hook */
		if($skipHook == 0){
			$hook = new weHook('delete', '', array($object));
			$hook->executeHook();
		}

		we_temporaryDocument::delete($id, $table, $DB_WE);

		@set_time_limit(30);
		if(sizeof($row)){
			if($row["IsFolder"]){
				deleteFolder($id, $table, $row["Path"], $delR);
			} else{
				deleteFile($id, $table, $row["Path"], $row["ContentType"]);
			}
		}
		$GLOBALS['deletedItems'][] = $id;
	}
}
