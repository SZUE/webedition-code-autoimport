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
we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT);

class we_export_dirSelector extends we_selector_directory{

	function __construct($id, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $we_editDirID = "", $FolderText = ""){
		parent::__construct($id, EXPORT_TABLE, $JSIDName, $JSTextName, $JSCommand, $order, '', $we_editDirID, $FolderText);
		$this->title = g_l('fileselector', '[exportDirSelector][title]');
		$this->userCanMakeNewFolder = true;
	}

	protected function printHeaderHeadlines(){
		return '
<table class="headerLines" style="width:550px;">
<colgroup><col style="width:25px;"/><col style="width:200px;"/><col style="width:300px;"/></colgroup>
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector"colspan="2"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('export', '[name]') . '</a></th>
	</tr>
</table>';
	}

	protected function printFooterTable(){
		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.exit_close();");
		$yes_button = we_html_button::create_button(we_html_button::OK, "javascript:press_ok_button();");
		return '
<table id="footer">
	<tr>
		<td class="defaultfont description">' . g_l('export', '[name]') . '</td>
		<td class="defaultfont" style="text-align:left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '</td>
	</tr>
</table><div id="footerButtons">' . we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button) . '</div>';
	}

	protected function printHeaderTable(we_base_jsCmd $weCmd, $extra = '', $append = false){
		$makefolderState = we_base_permission::hasPerm("NEW_EXPORT");
		return parent::printHeaderTable($weCmd, '<td>' .
				we_base_jsCmd::singleCmd('updateSelectData', [
					'makefolderState' => $makefolderState
				]) .
				we_html_button::create_button('fa:btn_new_dir,fa-plus,fa-lg fa-folder', "javascript:if(top.fileSelect.data.makefolderState){top.drawNewFolder();}", '', 0, 0, "", "", $makefolderState ? false : true) .
				'</td>');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
			we_html_element::jsScript(JS_DIR . 'selectors/exportdir_selector.js');
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
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = rawurldecode(we_base_request::_(we_base_request::FILE, 'we_FolderText_tmp', ''));

		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');

		$weCmd->addCmd('updateSelectData', [
			'makeNewFolder' => false
			]
		);
		if(!$txt){
			$weCmd->addMsg(g_l('export', '[wrongtext]'), we_base_util::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->we_new($this->table, $this->dir, $txt);
			if(f('SELECT 1 FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($folder->Path) . '"', '', $this->db)){
				$weCmd->addMsg(g_l('export', '[folder_path_exists]'), we_base_util::WE_MESSAGE_ERROR);
			} elseif(we_export_export::filenameNotValid($folder->Text)){
				$weCmd->addMsg(g_l('export', '[wrongtext]'), we_base_util::WE_MESSAGE_ERROR);
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
					'table' => $this->table
				]);
			}
		}
		$this->printCmdAddEntriesHTML($weCmd);
		$this->setWriteSelectorData($weCmd);

		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds(), we_html_element::htmlBody());
	}

	protected function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval(is_null($this->dir) ? $this->dir : $this->db->affected_rows()));
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
			$weCmd->addMsg(g_l('export', '[folder_empty]'), we_base_util::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->Filename = $txt;
			$folder->Path = $folder->getPath();
			$this->db->query('SELECT ID,Text FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($folder->Path) . '" AND ID!=' . intval($this->we_editDirID));
			if($this->db->next_record()){
				$weCmd->addMsg(sprintf(g_l('export', '[folder_exists]'), $folder->Path), we_base_util::WE_MESSAGE_ERROR);
			} else {
				if(preg_match('/[%/\\"\']/', $folder->Text)){
					$weCmd->addMsg(g_l('export', '[wrongtext]'), we_base_util::WE_MESSAGE_ERROR);
				} else {
					if(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editDirID), '', $this->db) != $txt){
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
			}
		}
		$this->printCmdAddEntriesHTML($weCmd);
		$this->setWriteSelectorData($weCmd);

		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds(), we_html_element::htmlBody());
	}

	public function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		parent::printHTML($what, false);
	}

}
