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
		return (f('SELECT IsFolder FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE ID=' . intval($id)) ?
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
				return (empty($arr['documentIDs']));
		}
		return true;
	}

	private static function deleteFolder($id, $table, $path = '', $delR = true, we_database_base $DB_WE = null){
		$isTemplateFolder = ($table == TEMPLATES_TABLE);

		$DB_WE = ($DB_WE ?: new DB_WE());
		$path = $path ?: f('SELECT Path FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), '', $DB_WE);
		if(!$path){
			return false;
		}

		if($delR){ // recursive delete
			$toDeleteArray = $DB_WE->getAllq('SELECT ID FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($id), true);
			foreach($toDeleteArray as $toDelete){
				self::deleteEntry($toDelete, $table, true, 0, $DB_WE);
			}
		}

		// do not delete class folder if class still exists!
		if(defined('OBJECT_FILES_TABLE') && $table === OBJECT_FILES_TABLE){
			if(f('SELECT IsClassFolder FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), '', $DB_WE) || // it is a class folder
				f('SELECT 1 FROM ' . OBJECT_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '"', '', $DB_WE)){ // class still exists
				return;
			}
		}
		// Fast Fix for deleting entries from tblLangLink: #5840
		if($DB_WE->query('DELETE FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id))){
			$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '" AND IsObject=' . ($table == FILE_TABLE ? 0 : 1) . ' AND IsFolder=1 AND DID=' . intval($id));
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
		$DB_WE = $DB_WE ?: new DB_WE();

		$isTemplateFile = ($table == TEMPLATES_TABLE);

		$path = $path ?: f('SELECT Path FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), '', $DB_WE);
		self::deleteContentFromDB($id, $table);

		$file = ((!$isTemplateFile) ? $_SERVER['DOCUMENT_ROOT'] : TEMPLATES_PATH) . $path;

		switch($table){
			case FILE_TABLE:
				we_base_file::deleteLocalFile($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . trim($path, '/'));
			//no break
			case TEMPLATES_TABLE:
				we_base_file::deleteLocalFile(preg_replace('/\.tmpl$/i', '.php', $file));
				$DB_WE->query('DELETE FROM ' . CAPTCHADEF_TABLE . ' WHERE ID=' . $id);
				break;
		}

		we_temporaryDocument::delete($id, $table, $DB_WE);

		$DB_WE->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE (ID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '") OR (remObj=' . intval($id) . ' AND remTable="' . $DB_WE->escape(stripTblPrefix($table)) . '")');

		switch($table){
			case FILE_TABLE:
				$DB_WE->query('UPDATE ' . CONTENT_TABLE . ' c SET c.BDID=0 WHERE c.Type IN ("href","img") AND c.BDID=' . intval($id));
				$DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID=0 AND ID=' . intval($id));

				if(defined('SCHEDULE_TABLE')){ //	Delete entries from schedule as well
					$DB_WE->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($id) . ' AND ClassName!="we_objectFile"');
				}
				if(defined('CUSTOMER_FILTER_TABLE')){
					$DB_WE->query('DELETE FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelTable="tblFile" AND modelId=' . intval($id));
				}

				$DB_WE->query('DELETE FROM ' . NAVIGATION_TABLE . ' WHERE Selection="' . we_navigation_navigation::SELECTION_STATIC . '" AND SelectionType="' . we_navigation_navigation::STYPE_DOCLINK . '" AND LinkID=' . intval($id));

				// Fast Fix for deleting entries from tblLangLink: #5840
				$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblFile" AND IsObject=0 AND IsFolder=0 AND DID=' . intval($id));
				$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblFile" AND LDID=' . intval($id));
				break;
			case (defined('VFILE_TABLE') ? VFILE_TABLE : 'VFILE_TABLE'):
				$DB_WE->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($id) . ' AND DocumentTable="' . stripTblPrefix(VFILE_TABLE) . '" AND type IN ("collection","archive")'); //FIXME: delete OR archive
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID>0 AND ID=' . intval($id));
				$tableID = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsClassFolder=0 AND ID=' . intval($id), '', $DB_WE);
				if($tableID){
					$DB_WE->query('DELETE FROM ' . OBJECT_X_TABLE . intval($tableID) . ' WHERE OF_ID=' . intval($id));
					$DB_WE->query('DELETE FROM ' . NAVIGATION_TABLE . ' WHERE Selection="' . we_navigation_navigation::SELECTION_STATIC . '" AND SelectionType="' . we_navigation_navigation::STYPE_OBJLINK . '" AND LinkID=' . intval($id));
					//Bug 2892
					$foo = $DB_WE->getAllq('SELECT ID FROM ' . OBJECT_TABLE, true);
					foreach($foo as $testclassID){
						if($DB_WE->isColExist(OBJECT_X_TABLE . $testclassID, we_object::QUERY_PREFIX . $tableID)){

							//das loeschen in der DB wirkt sich nicht auf die Objekte aus, die noch nicht publiziert sind
							$foos = $DB_WE->getAllq('SELECT OF_ID FROM ' . OBJECT_X_TABLE . intval($testclassID) . ' WHERE ' . we_object::QUERY_PREFIX . $tableID . '=' . intval($id), true);
							foreach($foos as $affectedobjectsID){
								$obj = new we_objectFile();
								$obj->initByID($affectedobjectsID, OBJECT_FILES_TABLE);

								$obj->getContentDataFromTemporaryDocs($affectedobjectsID);
								$oldModDate = $obj->ModDate;
								$obj->setElement('we_object_' . $tableID, 0);
								$obj->we_save(false, true);
								if($obj->Published != 0 && $obj->Published == $oldModDate){
									$obj->we_publish(false, true, true);
								}
							}
							$DB_WE->query('UPDATE ' . OBJECT_X_TABLE . intval($testclassID) . ' SET ' . we_object::QUERY_PREFIX . $tableID . '=0 WHERE ' . we_object::QUERY_PREFIX . $tableID . '=' . intval($id));
						}
					}
					// Fast Fix for deleting entries from tblLangLink: #5840
					$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblObjectFiles" AND DID=' . intval($id));
					$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblObjectFiles" AND LDID=' . intval($id));
				}
				if(defined('SCHEDULE_TABLE')){ //	Delete entries from schedule as well
					$DB_WE->query('DELETE FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($id) . ' AND ClassName="we_objectFile"');
				}
				if(defined('CUSTOMER_FILTER_TABLE')){
					$DB_WE->query('DELETE FROM ' . CUSTOMER_FILTER_TABLE . ' WHERE modelTable="tblObjectFiles" AND modelId=' . intval($id));
				}

				break;
		}

		$DB_WE->query('DELETE FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id));
		if(defined('OBJECT_TABLE') && $table == OBJECT_TABLE){
			$ofID = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $DB_WE->escape($path) . '"', '', $DB_WE);
			if($ofID){
				self::deleteEntry($ofID, OBJECT_FILES_TABLE, true, 0, $DB_WE);
			}
			$DB_WE->delTable(OBJECT_X_TABLE . intval($id));
		}
		if($contentType === we_base_ContentTypes::IMAGE){
			we_thumbnail::deleteByImageID($id);
		}
	}

	public static function deleteEntry($id, $table, $delR = true, $skipHook = false, we_database_base $DB_WE = null){
		switch($table){
			case defined('FILE_TABLE') ? FILE_TABLE : 'FILE_TABLE':
			case defined('TEMPLATES_TABLE') ? TEMPLATES_TABLE : 'TEMPLATES_TABLE':
			case defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE':
			case defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE':
			case defined('VFILE_TABLE') ? VFILE_TABLE : 'VFILE_TABLE':
				break;
			default:
				t_e('unable to delete files from this table', $table);
				die('unsupported delete');
		}

		$DB_WE = ($DB_WE ?: new DB_WE());
		if(defined('WORKFLOW_TABLE') && ($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE))){
			if(we_workflow_utility::inWorkflow($id, $table, $GLOBALS['DB_WE'])){
				we_workflow_utility::removeDocFromWorkflow($id, $table, $_SESSION['user']['ID'], g_l('modules_workflow', '[doc_deleted]'));
			}
		}

		if($id){
			$row = getHash('SELECT Path,IsFolder,ContentType FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
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
				$hook = new we_hook_base('delete', '', [$object]);
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
		$DB_WE = $DB_WE ?: new DB_WE();

		if(!f('SELECT 1 FROM ' . CONTENT_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '" LIMIT 1', '', $DB_WE)){
			return true;
		}

		return $DB_WE->query('DELETE FROM ' . CONTENT_TABLE . ' c WHERE c.DID=' . intval($id) . ' AND c.DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '"');
	}

	private static function getHasPerm($idInfos, $table){
		if(we_base_permission::hasPerm('ADMINISTRATOR')){
			return true;
		}
		switch($table){
			case FILE_TABLE:
				return (
					($idInfos['IsFolder'] && we_base_permission::hasPerm('DELETE_DOC_FOLDER') && !$idInfos['hasFiles']) ||
					(!$idInfos['IsFolder'] && we_base_permission::hasPerm('DELETE_DOCUMENT')) ||
					($idInfos['IsFolder'] && we_base_permission::hasPerm('DELETE_DOC_FOLDER') && $idInfos['hasFiles'] && we_base_permission::hasPerm('DELETE_DOCUMENT'))
					);
			case TEMPLATES_TABLE:
				return (
					($idInfos['IsFolder'] && we_base_permission::hasPerm('DELETE_TEMP_FOLDER') && !$idInfos['hasFiles']) ||
					(!$idInfos['IsFolder'] && we_base_permission::hasPerm('DELETE_TEMPLATE')) ||
					($idInfos['IsFolder'] && we_base_permission::hasPerm('DELETE_TEMP_FOLDER') && $idInfos['hasFiles'] && we_base_permission::hasPerm('DELETE_TEMPLATE'))
					);

			case OBJECT_FILES_TABLE:
				return (we_base_permission::hasPerm('DELETE_OBJECTFILE'));

			case OBJECT_TABLE:
				return ($idInfos['IsFolder'] && we_base_permission::hasPerm('DELETE_OBJECT'));
			default:
				return false;
		}
	}

	private static function checkFilePerm($selectedItems, $table){
		foreach($selectedItems as $selectedItem){
			if(!we_base_permission::checkIfRestrictUserIsAllowed($selectedItem, $table, $GLOBALS['DB_WE'])){
				return we_base_permission::USER_RESTRICTED;
			}

			if(!we_base_delete::checkDeleteEntry($selectedItem, $table)){
				return 0;
			}
		}


		foreach($selectedItems as $selectedItem){

			if($table == FILE_TABLE){
				$users = we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $selectedItem);
				if($users){
					return we_base_permission::WORKSPACE_HAS_USERS;
				}

				// check if childrenfolders are workspaces
				$childs = [];

				pushChilds($childs, $selectedItem, $table, 1, $GLOBALS['DB_WE']);
				$users = [];
				foreach($childs as $ch){
					$users = array_merge($users, we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $childs));
				}
				$users = array_unique($users);

				if($users){
					$retVal = -4;
					break;
				}
			}

			if($table == TEMPLATES_TABLE){
				$users = we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $selectedItem, "workSpaceTmp");
				if($users){
					return we_base_permission::WORKSPACE_HAS_USERS;
				}

				// check if childrenfolders are workspaces
				$childs = [];

				pushChilds($childs, $selectedItem, $table, 1, $GLOBALS['DB_WE']);
				$users = [];
				foreach($childs as $ch){
					$users = array_merge($users, we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $childs, "workSpaceTmp"));
				}
				$users = array_unique($users);

				if($users){
					$retVal = -4;
					break;
				}
			}

			if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE){

				$users = we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $selectedItem, "workSpaceObj");
				if($users){
					return we_base_permission::WORKSPACE_HAS_USERS;
				}

				$childs = [];

				pushChilds($childs, $selectedItem, $table, 1, $GLOBALS['DB_WE']);
				$users = we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $childs, "workSpaceObj");

				if($users){
					$retVal = -4;
					break;
				}
			}
			if(defined('OBJECT_FILES_TABLE') && $table == FILE_TABLE){
				$objects = self::getObjectsForDocWorkspace($selectedItem, $GLOBALS['DB_WE']);
				if($objects){
					$retVal = -3;
					break;
				}

				$childs = [];

				pushChilds($childs, $selectedItem, $table, 1, $GLOBALS['DB_WE']);
				$objects = self::getObjectsForDocWorkspace($childs, $GLOBALS['DB_WE']);

				if($objects){
					$retVal = -5;
					break;
				}
			}
		}
		return 1;
	}

	private static function getObjectsForDocWorkspace($id, we_database_base $db){
	$ids = (is_array($id)) ? $id : [$id];

	if(!defined('OBJECT_FILES_TABLE')){
		return [];
	}

	$where = [];
	foreach($ids as $id){
		$where[] = 'FIND_IN_SET(' . $id . ',Workspaces)';
	}

	$db->query('SELECT ID,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ' . implode(' OR ', $where));
	return $db->getAllFirst(false);
}


	public static function getDialog(){
$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2);
$wfchk = defined('WORKFLOW_TABLE') && ($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE)) ?
	we_base_request::_(we_base_request::BOOL, 'we_cmd', 0, 3) :
	1;
$wecmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$jsCmd = new we_base_jsCmd();

if(!$wfchk){
	if(($selectedItems = we_base_request::_(we_base_request::INTLISTA, 'sel', []))){
		$found = false;
		foreach($selectedItems as $selectedItem){
			if(we_workflow_utility::inWorkflow($selectedItem, $table)){
				$found = true;
				break;
			}
		}
	} else {
		$jsCmd->addMsg(g_l('alert', '[nothing_to_delete]'), we_base_util::WE_MESSAGE_WARNING);
	}
	$wfchk_html = we_html_element::htmlBody(['onload' => ($selectedItems ? 'confirmDel(' . intval($found) . ')' : '')], '<form name="we_form" method="post">' . we_html_element::htmlHidden("sel", implode(',', $selectedItems)) . "</form>");
} elseif(in_array($wecmd0, ["do_delete", 'delete_single_document'])){
	if(($selectedItems = we_base_request::_(we_base_request::INTLISTA, "sel", []))){
		//	look which documents must be deleted.
		$idInfos = [
			'IsFolder' => 0,
			'Path' => '',
			'hasFiles' => 0
		];
		if($selectedItems && ($table == FILE_TABLE || $table == TEMPLATES_TABLE)){
			$idInfos = getHash('SELECT IsFolder,Path FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE ID=' . intval($selectedItems[0]));
			if(!$idInfos){
				t_e('ID ' . $selectedItems[0] . ' not present in table ' . $table);
				return;
			} elseif($idInfos['IsFolder']){
				$idInfos['hasFiles'] = f('SELECT ID FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE ParentID=' . intval($selectedItems[0]) . ' AND IsFolder=0 AND Path LIKE "' . $GLOBALS['DB_WE']->escape($idInfos['Path']) . '%"') > 0 ? 1 : 0;
			}
		}
		$hasPerm = self::getHasPerm($idInfos, $table);
		unset($idInfos);
		$retVal = !$hasPerm ? we_base_permission::NO_PERMISSION : self::checkFilePerm($selectedItems, $table);

		switch($retVal){
			case we_base_permission::NO_PERMISSION:
				$jsCmd->addMsg(g_l('alert', '[no_perms_action]'), we_base_util::WE_MESSAGE_ERROR);
				break;
			case -5: //	not allowed to delete workspace
				$objList = '';
				foreach($objects as $val){
					$objList .= '- ' . $val . '\n';
				}
				$jsCmd->addMsg(sprintf(g_l('alert', '[delete_workspace_object_r]'), id_to_path($selectedItem, $table), $objList), we_base_util::WE_MESSAGE_ERROR);
				break;
			case -4: //	not allowed to delete workspace
				$usrList = '';
				foreach($users as $val){
					$usrList .= '- ' . $val . '\n';
				}
				$jsCmd->addMsg(sprintf(g_l('alert', '[delete_workspace_user_r]'), id_to_path($selectedItem, $table), $usrList), we_base_util::WE_MESSAGE_ERROR);
				break;
			case -3: //	not allowed to delete workspace
				$objList = '';
				foreach($objects as $val){
					$objList .= "- " . $val . '\n';
				}
				$jsCmd->addMsg(sprintf(g_l('alert', '[delete_workspace_object]'), id_to_path($selectedItem, $table), $objList), we_base_util::WE_MESSAGE_ERROR);
				break;
			case we_base_permission::WORKSPACE_HAS_USERS: //	not allowed to delete workspace
				$usrList = '';
				foreach($users as $val){
					$usrList .= '- ' . $val . '\n';
				}
				$jsCmd->addMsg(sprintf(g_l('alert', '[delete_workspace_user]'), id_to_path($selectedItem, $table), $usrList), we_base_util::WE_MESSAGE_ERROR);
				break;
			case we_base_permission::USER_RESTRICTED: //	not allowed to delete document
				$jsCmd->addMsg(sprintf(g_l('alert', '[noRightsToDelete]'), id_to_path($selectedItem, $table)), we_base_util::WE_MESSAGE_ERROR);
				break;
			default:
				if($retVal){ //	user may delete -> delete files !
					$GLOBALS["we_folder_not_del"] = [];

					$deletedItems = [];

					foreach($selectedItems as $sel){
						we_base_delete::deleteEntry($sel, $table, true, false, $GLOBALS['DB_WE']);
					}

					if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	only update tree when in normal mode
						$jsCmd->addCmd(deleteTreeEntries, defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE);
					}

					if(!empty($deletedItems)){
						$deleted_objects = [];

						if(defined('OBJECT_TABLE') && $table == OBJECT_TABLE){ // close all open objects, if a class is deleted
							$localDeletedItems = [];

							// if its deleted and not selected, it must be an object
							foreach($deletedItems as $cur){
								if(in_array($cur, $selectedItems)){
									$localDeletedItems[] = $cur;
								} else {
									$deleted_objects[] = $cur; // deleted objects when classes are deleted
								}
							}
							$deletedItems = $localDeletedItems;
						}

						if(defined('CUSTOMER_TABLE')){ // delete the customerfilters
							we_customer_documentFilter::deleteModel($deletedItems, $table);
							if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_TABLE){
								if(!empty($deleted_objects)){
									we_customer_documentFilter::deleteModel($deleted_objects, OBJECT_FILES_TABLE);
								}
							}
						}

						we_base_history::deleteFromHistory(
							$deletedItems, $table);
						if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_TABLE){
							if(!empty($deleted_objects)){
								we_base_history::deleteFromHistory(
									$deleted_objects, OBJECT_FILES_TABLE);
							}
						}

						$jsCmd->addCmd('closeDeletedDocuments', ',' . implode(",", $deletedItems) . ',', ',' . implode(",", $deleted_objects) . ',');
					}

					if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	different messages in normal or seeMode
						if(!empty($GLOBALS['we_folder_not_del'])){
							$_SESSION['weS']['delete_files_nok'] = [];
							$_SESSION['weS']['delete_files_info'] = str_replace('\n', '', sprintf(g_l('alert', '[folder_not_empty]'), ''));
							foreach($GLOBALS["we_folder_not_del"] as $datafile){
								$_SESSION['weS']['delete_files_nok'][] = [
									'ContentType' => we_base_ContentTypes::FOLDER,
									"path" => $datafile
								];
							}
							$jsCmd->addCmd('delInfo');
						} else {
							$jsCmd->addMsg(g_l('alert', '[delete_ok]'), we_base_util::WE_MESSAGE_NOTICE);
						}
					}
				} else {
					switch($table){
						case TEMPLATES_TABLE:
							$jsCmd->addMsg(g_l('alert', '[deleteTempl_notok_used]'), we_base_util::WE_MESSAGE_ERROR);
							break;
						case OBJECT_TABLE:
							$jsCmd->addMsg(g_l('alert', '[deleteClass_notok_used]'), we_base_util::WE_MESSAGE_ERROR);
							break;
						default:
							$jsCmd->addMsg(g_l('alert', '[delete_notok]'), we_base_util::WE_MESSAGE_ERROR);
					}
				}
		}
	} else {
		$jsCmd->addMsg(g_l('alert', '[nothing_to_delete]'), we_base_util::WE_MESSAGE_WARNING);
	}

	//exit;
}

//	in seeMode return to startDocument ...

if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
	if($retVal){
		$jsCmd->addMsg(g_l('alert', '[delete_single][return_to_start]'), we_base_util::WE_MESSAGE_NOTICE);
		//	document deleted -> go to seeMode startPage
		$jsCmd->addCmd('we_cmd', ['start_multi_editor']);
	} else {
		$jsCmd->addMsg(g_l('alert', '[delete_single][no_delete]'), we_base_util::WE_MESSAGE_ERROR);
	}
	$body = we_html_element::htmlBody();
} elseif(!$wfchk && $wecmd0 != "delete"){
	$body = $wfchk_html;
} elseif($wecmd0 === "do_delete"){
	$body = we_html_element::htmlBody();
} else {
	$body = we_html_element::htmlBody(['class' => "weTreeHeader"], '<div>
<h1 class="big" style="padding:0px;margin:0px;">' . oldHtmlspecialchars(g_l('newFile', '[title_delete]')) . '</h1>
<p class="small"><span class="middlefont">' . g_l('newFile', '[delete_text]') . '</span></p>
<div>' . we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:WE().util.showConfirm(window, '', '" . g_l('alert', '[delete]') . "',['do_delete','','" . $table . "']);"), "", we_html_button::create_button('quit_delete', "javascript:we_cmd('exit_delete','','" . $table . "')"), 10, "left") . '</div></div><form name="we_form" method="post">' . we_html_element::htmlHidden('sel', '') . '</form>');
}

echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'delete.js', 'init();', ['id' => 'loadVarDelete', 'data-deleteData' => setDynamicVar([
			'table' => $table,
			'wecmd0' => $wecmd0
	])]) . $jsCmd->getCmds(), $body);


	}

	public static function getDelInfo(){
		if(isset($_SESSION['weS']['delete_files_nok']) && is_array($_SESSION['weS']['delete_files_nok'])){
			$table = new we_html_table(['style' => 'margin:10px;', 'class' => 'defaultfont default'], 1, 2);
			foreach($_SESSION['weS']['delete_files_nok'] as $i => $data){
				$table->setCol($i, 0, ['style' => 'padding-top:2px;', 'class' => 'selectoricon', 'data-contenttype' => (isset($data["ContentType"]) ? $data["ContentType"] : '')]);
				$table->setCol($i, 1, null, str_replace($_SERVER['DOCUMENT_ROOT'], "", $data["path"]));
				$table->addRow();
			}
			unset($_SESSION['weS']['delete_files_nok']);
		}

		$parts = [
			["headline" => we_html_tools::htmlAlertAttentionBox($_SESSION['weS']['delete_files_info'], we_html_tools::TYPE_ALERT, 500),
				"html" => "",
				'space' => we_html_multiIconBox::SPACE_SMALL,
				'noline' => 1
			],
			["headline" => "",
				"html" => we_html_element::htmlDiv(['class' => "blockWrapper", 'style' => "width: 475px; height: 350px; border:1px #dce6f2 solid;"], $table->getHtml()),
				'space' => we_html_multiIconBox::SPACE_SMALL
			],
		];
		unset($_SESSION['weS']['delete_files_info']);

		$buttons = new we_html_table(['class' => 'default defaultfont', 'style' => "text-align:right"], 1, 1);
		$buttons->setCol(0, 0, null, we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();"));
		echo we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(['class' => "weDialogBody", 'onload' => "setIconOfDocClass(document,'selectoricon');"], we_html_multiIconBox::getHTML("", $parts, 30, $buttons->getHtml())
			)
		);
	}

}
