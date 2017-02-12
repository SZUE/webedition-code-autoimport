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
abstract class we_base_move{

	private static function checkMoveItem($DB_WE, $targetDirectoryID, $id, $table, &$items2move){
		// check if entry is a folder
		$row = getHash('SELECT Path,Text,IsFolder FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
		if(!$row /* || $row['IsFolder'] */){
			return we_base_file::ERROR_NO_SUCH_FILE;
		}

		if($row['IsFolder']){
			$targetDir = $targetDirectoryID ? f('SELECT Path FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($targetDirectoryID)) : '/';
			if(strpos($targetDir, $row['Path'] . '/') === 0){
				return we_base_file::ERROR_SAME_PARENT;
			}
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
				return we_base_file::ERROR_DUPLICATE_NAME;
			}

			if(defined('OBJECT_TABLE') && $table == OBJECT_FILES_TABLE){
				// check if class directory is the same
				if(substr($DB_WE->f('Path'), 0, strlen($rootdir) + 1) != $rootdir . '/'){
					return we_objectFile::ERROR_NOT_SAME_CLASS;
				}
			}
		}
		return 1;
	}

	private static function moveItems($targetDirectoryID, array $ids, $table, &$notMovedItems){
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
							we_versions_version::updateLastVersionPath($DB_WE, $id, $table, $parentID, $newPath . '/' . $fileName);
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
							we_versions_version::updateLastVersionPath($DB_WE, $id, $table, $parentID, $newPath . '/' . $fileName);
						}
					}

					// update table
					$DB_WE->query('UPDATE ' . $DB_WE->escape($table) . ' SET ParentID=' . intval($parentID) . ', Path="' . $DB_WE->escape($newPath . '/' . $fileName) . '" WHERE ID=' . intval($id));
				}
				break;
		}


		return empty($notMovedItems);
	}

	public static function getDialog(){
		$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2);

		if(($table == TEMPLATES_TABLE && !we_base_permission::hasPerm("MOVE_TEMPLATE")) ||
			($table == FILE_TABLE && !we_base_permission::hasPerm("MOVE_DOCUMENT")) ||
			(defined('OBJECT_TABLE') && $table == OBJECT_TABLE && !we_base_permission::hasPerm("MOVE_OBJECTFILES"))){
			we_base_permission::noPermDialog(g_l('alert', '[no_perms]'));
		}
		$jsCmd = new we_base_jsCmd();

		$weSuggest = & weSuggest::getInstance();
		$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);

		switch($cmd0){
			case 'do_move':
			case 'move_single_document':
				$db = new DB_WE();
				if(($targetDirectroy = we_base_request::_(we_base_request::INT, 'we_target')) === false){
					$jsCmd->addMsg(g_l('alert', '[move_no_dir]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}
				if(!($selectedItems = we_base_request::_(we_base_request::INTLISTA, 'sel', []))){
					$jsCmd->addMsg(g_l('alert', '[nothing_to_move]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				// list of all item names which should be moved
				$items2move = [];

				// list of the selected items
				$retVal = 1;
				foreach($selectedItems as $selectedItem){

					// check if user is allowed to move this item
					if(!we_base_permission::checkIfRestrictUserIsAllowed($selectedItem, $table, $db)){
						$retVal = -1;
						break;
					}

					// check if item could be moved to the target directory
					switch(self::checkMoveItem($db, $targetDirectroy, $selectedItem, $table, $items2move)){
						default :
						case 1 :
							break;
						case we_base_file::ERROR_NO_SUCH_FILE:
							$message = 'File not found';
							$retVal = 0;
							break;
						case we_base_file::ERROR_SAME_PARENT:
							$message = g_l('weEditor', '[folder_save_nok_parent_same]');
							$retVal = 0;
							break;
						case we_base_file::ERROR_DUPLICATE_NAME:
							$message = g_l('alert', '[move_duplicate]');
							$retVal = 0;
							break;
						case we_objectFile::ERROR_NOT_SAME_CLASS :
							$message = g_l('alert', '[move_onlysametype]');
							$retVal = 0;
							break;
					}
				}

				if($retVal == -1){ //	not allowed to move document
					$jsCmd->addMsg(sprintf(g_l('alert', '[noRightsToMove]'), id_to_path($selectedItem, $table)), we_message_reporting::WE_MESSAGE_ERROR);
				} elseif($retVal){ //	move files !
					$notMovedItems = [];
					self::moveItems($targetDirectroy, $selectedItems, $table, $notMovedItems);

					if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	only update tree when in normal mode
						$jsCmd->addCmd('moveTreeEntries', $table == (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'));
					}

					if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	different messages in normal or seeMode
						if($notMovedItems){
							$_SESSION['weS']['move_files_nok'] = [];
							foreach($notMovedItems as $item){
								$_SESSION['weS']['move_files_nok'][] = [
									'ContentType' => $item['ContentType'],
									'path' => $item['Path']
								];
							}
							$jsCmd->addCmd('moveInfo');
						} else {
							$jsCmd->addMsg(g_l('alert', '[move_ok]'), we_message_reporting::WE_MESSAGE_NOTICE);
						}
					}
				} else {
					$jsCmd->addMsg($message, we_message_reporting::WE_MESSAGE_ERROR);
				}
		}

//	in seeMode return to startDocument ...


		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
			$js = ($retVal ? //	document moved -> go to seeMode startPage
				we_message_reporting::getShowMessageCall(g_l('alert', '[move_single][return_to_start]'), we_message_reporting::WE_MESSAGE_NOTICE) . ";top.we_cmd('start_multi_editor');" :
				we_message_reporting::getShowMessageCall(g_l('alert', '[move_single][no_delete]'), we_message_reporting::WE_MESSAGE_ERROR));

			echo we_html_tools::getHtmlTop('', '', '', $jsCmd->getCmds() . we_html_element::jsElement($js));
			exit();
		}

		switch($table){
			case TEMPLATES_TABLE:
				$type = g_l('global', '[templates]');
				break;
			case defined('OBJECT_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE':
				$type = g_l('global', '[objects]');
				break;
			default:
				$type = g_l('global', '[documents]');
				break;
		}

		if($cmd0 === 'do_move'){
			$body = we_html_element::htmlBody();
		} else {


			$ws_Id = get_def_ws($table) ?: 0;
			$ws_path = ($ws_Id ? id_to_path($ws_Id, $table) : '/');
			$textname = 'we_targetname';
			$idname = 'we_target';

			$weSuggest->setAcId('Dir');
			$weSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$weSuggest->setInput($textname, $ws_path);
			$weSuggest->setMaxResults(4);
			$weSuggest->setRequired(true);
			$weSuggest->setResult(trim($idname), $ws_Id);
			$weSuggest->setSelector(weSuggest::DirSelector);
			$weSuggest->setTable($table);
			$weSuggest->setWidth(250);
			$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',top.treeheader.document.we_form.elements.' . $idname . '.value,'" . $table . "','" . $idname . "','" . $textname . "','','',0)"), 10);

			$weAcSelector = $weSuggest->getHTML();


			$body = we_html_element::htmlBody(['class' => "weTreeHeaderMove"], '<form name="we_form" method="post" onsubmit="return false">
<div>
<h1 class="big" style="padding:0px;margin:0px;">' . oldHtmlspecialchars(g_l('newFile', '[title_move]')) . '</h1>
<p class="small"><span class="middlefont" style="padding-right:5px;padding-bottom:10px;">' . g_l('newFile', '[move_text]') . '</span>
			<p style="margin:0px 0px 10px 0px;padding:0px;">' . $weAcSelector . '</p></p>
<div>' . we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:press_ok_move('" . $type . "');"), '', we_html_button::create_button('quit_move', "javascript:we_cmd('exit_move','','" . $table . "')"), 10, "left") . '</div></div>' . we_html_element::htmlHidden("sel", "") .
					'</form>');
		}

		echo we_html_tools::getHtmlTop('', '', '', $jsCmd->getCmds() .
			we_html_element::jsScript(JS_DIR . 'move.js', "initMove('" . $table . "');"), $body);
	}

	public static function getMoveInfo(){
		if(isset($_SESSION['weS']['move_files_nok']) && is_array($_SESSION['weS']['move_files_nok'])){
			$table = new we_html_table(['style' => 'margin:10px;', "class" => "default defaultfont"], 1, 2);
			foreach($_SESSION['weS']['move_files_nok'] as $i => $data){
				$table->addRow();
				$table->setCol($i, 0, ['style' => 'padding-top:2px;'], (isset($data['ContentType']) ? we_html_element::jsElement('document.write(WE().util.getTreeIcon("' . $data['ContentType'] . '"))') : ''));
				$table->setCol($i, 1, null, str_replace($_SERVER['DOCUMENT_ROOT'], "", $data["path"]));
			}
		}

		$parts = [
			["headline" => we_html_tools::htmlAlertAttentionBox(str_replace("\\n", '', sprintf(g_l('alert', '[move_of_files_failed]'), "")), we_html_tools::TYPE_ALERT, 500),
				"html" => "",
				'space' => we_html_multiIconBox::SPACE_SMALL,
				'noline' => 1],
			["headline" => "",
				"html" => we_html_element::htmlDiv(['class' => "blockWrapper", 'style' => "width: 475px; height: 350px; border:1px #dce6f2 solid;"], $table->getHtml()),
				'space' => we_html_multiIconBox::SPACE_SMALL],
		];

		$buttons = new we_html_table(['style' => "text-align:right", "class" => "default defaultfont"], 1, 1);
		$buttons->setCol(0, 0, null, we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();"));
		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', '', we_html_element::htmlBody(['class' => "weDialogBody"], we_html_multiIconBox::getHTML("", $parts, 30, $buttons->getHtml())
			)
		);
	}

}
