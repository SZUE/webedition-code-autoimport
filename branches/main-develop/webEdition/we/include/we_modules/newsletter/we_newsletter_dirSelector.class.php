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
class we_newsletter_dirSelector extends we_selector_directory{
	var $fields = "ID,ParentID,Text,Path,IsFolder";

	function __construct($id, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $sessionID = "", $we_editDirID = "", $FolderText = "", $rootDirID = 0, $multiple = 0){
		$table = NEWSLETTER_TABLE;
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, 0, $we_editDirID, $FolderText, $rootDirID, $multiple);
	}

	protected function printCreateFolderHTML(){
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		$folder = new we_folder();
		$folder->we_new($this->table, $this->dir, $txt);
		if(!($msg = $folder->checkFieldsOnSave())){
			$folder->we_save();
		}

		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');

		if($msg){
			$weCmd->addCmd('updateSelectData', [
				'makeNewFolder' => false,
			]);
			$weCmd->addMsg($msg, we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$weCmd->addCmd('updateSelectData', [
				'makeNewFolder' => false,
			]);
			if($this->canSelectDir){
				$weCmd->addCmd('updateSelectData', [
					'currentPath' => $folder->Path,
					'currentID' => $folder->ID,
					'currentText' => $folder->Text
				]);
			}
			$weCmd->addCmd('makeNewTreeEntry', [
				'id' => $folder->ID,
				'parentid' => $folder->ParentID,
				'text' => $txt,
				'open' => 1,
				'contenttype' => $folder->ContentType,
				'table' => $this->table,
				'published' => 1
			]);
		}
		$this->printCmdAddEntriesHTML($weCmd);
		$weCmd->addCmd('setButtons', [['NewFolderBut' ,$this->userCanMakeNewDir()]]);

		$this->setWriteSelectorData($weCmd);
		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds(), we_html_element::htmlBody());
	}

	protected function printDoRenameFolderHTML(){
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		$folder = new we_folder();
		$folder->initByID($this->we_editDirID, $this->table);
		$folder->Text = $txt;
		$folder->ModDate = time();
		$folder->Filename = $txt;
		$folder->Published = time();
		$folder->Path = $folder->getPath();
		$folder->ModifierID = isset($_SESSION['user']["ID"]) ? $_SESSION['user']["ID"] : "";
		if(!($msg = $folder->checkFieldsOnSave())){
			$folder->we_save();
		}
		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');
		if($msg){
			$weCmd->addMsg($msg, we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$weCmd->addCmd('updateTreeEntry', ['id' => $folder->ID, 'text' => $txt, 'parentid' => $folder->ParentID]);
			if($this->canSelectDir){
				$weCmd->addCmd('updateSelectData', [
					'currentPath' => $folder->Path,
					'currentID' => $folder->ID,
					'currentText' => $folder->Text
				]);
			}
		}

		$weCmd->addCmd('setButtons', [['NewFolderBut' ,$this->userCanMakeNewDir()]]);
		$weCmd->addCmd('updateSelectData', ['makeNewFolder' => false]);

		$this->printCmdAddEntriesHTML($weCmd);
		$this->setWriteSelectorData($weCmd);
		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds(), we_html_element::htmlBody());
	}

	protected function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($this->dir) .
			self::getWsQuery(NEWSLETTER_TABLE) .
			($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : '')
		);
	}

	protected function printCmdAddEntriesHTML(we_base_jsCmd $weCmd){
		$entries = [];
		$this->query();
		while($this->db->next_record()){
			$entries[] = [
				intval($this->db->f('ID')),
				$this->db->f('Text'),
				intval($this->db->f('IsFolder')),
				$this->db->f('Path')
			];
		}
		$weCmd->addCmd('addEntries', $entries);
		$weCmd->addCmd('setButtons', [['NewFolderBut' ,$this->userCanMakeNewDir()]]);
		$weCmd->addCmd('writeBody');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
			we_html_element::jsScript(JS_DIR . 'selectors/newsletterdir_selector.js');
	}

	protected function printHeaderHeadlines(){
		return '
<table class="headerLines" style="width:550px;">
<colgroup><col style="width:25px;"/><col style="width:200px;"/><col style="width:300px;"/></colgroup>
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector"colspan="2"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[filename]') . '</a></th>
	</tr>
</table>';
	}

	protected function userCanSeeDir($showAll = false){
		return true;
	}

	protected function userCanRenameFolder(){
		return permissionhandler::hasPerm('EDIT_NEWSLETTER');
	}

	protected function userCanMakeNewDir(){
		return permissionhandler::hasPerm('NEW_NEWSLETTER');
	}

	protected function userHasRenameFolderPerms(){
		return permissionhandler::hasPerm('EDIT_NEWSLETTER');
	}

	protected function userHasFolderPerms(){
		return permissionhandler::hasPerm('NEW_NEWSLETTER');
	}

	public function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		parent::printHTML($what, false);
	}

}
