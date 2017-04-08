<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source we_users_util::can redistribute it and/or modify
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
class we_voting_dirSelector extends we_selector_directory{

	function __construct($id, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $we_editDirID = "", $FolderText = ""){
		parent::__construct($id, VOTING_TABLE, stripslashes($JSIDName), stripslashes($JSTextName), $JSCommand, $order, "", $we_editDirID, $FolderText);
		$this->title = g_l('fileselector', '[votingDirSelector][title]');
		$this->userCanMakeNewFolder = true;
	}

	protected function printHeaderHeadlines(){
		return '
<table class="headerLines" style="width:550px;">
<colgroup><col style="width:25px;"/><col style="width:200px;"/><col style="width:300px;"/></colgroup>
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector"colspan="2"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('modules_voting', '[name]') . '</a></th>
	</tr>
</table>';
	}

	protected function printFooterTable(){
		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.exit_close();");
		$yes_button = we_html_button::create_button(we_html_button::OK, "javascript:press_ok_button();");
		return '
<table id="footer">
	<tr>
		<td class="defaultfont">' . g_l('modules_voting', '[name]') . '</td>
		<td class="defaultfont" style="text-align:left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '</td>
	</tr>
</table><div id="footerButtons">' . we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button) . '</div>';
	}

	protected function printHeaderTable(we_base_jsCmd $weCmd, $extra = '', $append = false){
		$makefolderState = we_base_permission::hasPerm("NEW_VOTING");
		$weCmd->addCmd('updateSelectData', ['makefolderState' => $makefolderState]);
		return parent::printHeaderTable($weCmd, '<td>' .
				we_html_button::create_button('fa:btn_new_dir,fa-plus,fa-lg fa-folder', "javascript:if(top.fileSelect.data.makefolderState){top.drawNewFolder();}", '', 0, 0, "", "", $makefolderState ? false : true) .
				'</td>');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
			we_html_element::jsScript(JS_DIR . 'selectors/votingdir_selector.js');
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
		$weCmd->addCmd('writeBody');
	}

	protected function printCreateFolderHTML(){
		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');
		$weCmd->addCmd('updateSelectData', ['makeNewFolder' => false]);

		$this->FolderText = rawurldecode($this->FolderText);
		$txt = rawurldecode(we_base_request::_(we_base_request::FILE, 'we_FolderText_tmp', ''));

		if(!$txt){
			$weCmd->addMsg(g_l('modules_voting', '[wrongtext]'), we_base_util::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->we_new($this->table, $this->dir, $txt);
			$this->db->query('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($folder->Path) . '"');
			if($this->db->next_record()){
				$weCmd->addMsg(g_l('modules_voting', '[folder_path_exists]'), we_base_util::WE_MESSAGE_ERROR);
			} elseif(we_voting_voting::filenameNotValid($folder->Text)){
				$weCmd->addMsg(g_l('modules_voting', '[wrongtext]'), we_base_util::WE_MESSAGE_ERROR);
			} else {
				$folder->we_save();
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
					'contenttype' => we_base_ContentTypes::FOLDER,
					'table' => $this->table,
					'published' => 1
				]);
			}
		}

		$this->printCmdAddEntriesHTML($weCmd);
		$this->setWriteSelectorData($weCmd);

		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds(), we_html_element::htmlBody());
	}

	protected function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' .
			$this->db->escape($this->table) .
			' WHERE IsFolder=1 AND ParentID=' . intval($this->dir) . ' ' . self::getUserExtraQuery($this->table));
	}

	static function getUserExtraQuery($table, $useCreatorID = true){
		$userExtraSQL = ' AND ((1 ' . we_users_util::makeOwnersSql(false) . ') ';

		if(get_ws($table)){
			$userExtraSQL .= self::getWsQuery($table);
		} else if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!we_base_permission::hasPerm("ADMINISTRATOR"))){
			$wsQuery =[];
			$ac = we_users_util::getAllowedClasses($this->db);
			$paths = id_to_path($ac, OBJECT_TABLE);
			foreach($paths as $path){
				$wsQuery []= ' Path LIKE "' . $path . '/%"';
				$wsQuery []= 'Path="' . $path . '" OR ';
			}
			if($wsQuery){
				$userExtraSQL .= ' AND (' . implode(',',$wsQuery) . ')';
			}
		} else {
			$userExtraSQL .= ' OR RestrictOwners=0 ';
		}
		return $userExtraSQL . ')';
	}

	protected function printDoRenameFolderHTML(){
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');

		$weCmd->addCmd('updateSelectData', [
			'makeNewFolder' => false
		]);
		if(!$txt){
			$weCmd->addMsg(g_l('modules_voting', '[folder_empty]'), we_base_util::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->Filename = $txt;
			$folder->Path = $folder->getPath();
			$exists = f('SELECT 1 FROM ' . $this->db->escape(VOTING_TABLE) . ' WHERE Path="' . $folder->Path . '" AND ID!=' . $this->we_editDirID . ' LIMIT 1', '', $this->db);
			if($exists){
				$weCmd->addMsg(sprintf(g_l('modules_voting', '[folder_exists]'), $folder->Path), we_base_util::WE_MESSAGE_ERROR);
			} elseif(preg_match('/[%/\\"\']/', $folder->Text)){
				$weCmd->addMsg(g_l('modules_voting', '[wrongtext]'), we_base_util::WE_MESSAGE_ERROR);
			} elseif(f('SELECT Text FROM ' . $this->db->escape(VOTING_TABLE) . ' WHERE ID=' . intval($this->we_editDirID), "", $this->db) != $txt){
				$folder->we_save();
				$weCmd->addCmd('updateTreeEntry', ['id' => $folder->ID, 'text' => $txt, 'parentid' => $folder->ParentID]);
				if($this->canSelectDir){
					$weCmd->addCmd('updateSelectData', [
						'currentPath' => $folder->Path,
						'currentID' => $folder->ID,
						'currentText' => $folder->Text
					]);
				}
			}
		}
		$this->printCmdAddEntriesHTML($weCmd);
		$this->setWriteSelectorData($weCmd);

		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds(), we_html_element::htmlBody());
	}

	public function printHTML($what = we_selector_file::FRAMESET, $withPreview = false){
		parent::printHTML($what, $withPreview);
	}

}
