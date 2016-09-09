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
class we_banner_dirSelector extends we_selector_directory{

	function __construct($id, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $we_editDirID = 0, $FolderText = ''){
		parent::__construct($id, BANNER_TABLE, $JSIDName, $JSTextName, $JSCommand, $order, '', $we_editDirID, $FolderText);
		$this->fields = 'ID,ParentID,Text,Path,IsFolder';
		$this->title = g_l('fileselector', '[bannerDirSelector][title]');
		$this->userCanMakeNewFolder = true;
	}

	protected function printHeaderHeadlines(){
		return '
<table class="headerLines" style="width:550px;">
<colgroup><col style="width:25px;"/><col style="width:200px;"/><col style="width:300px;"/></colgroup>
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector" colspan="2"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('modules_banner', '[name]') . '</a></th>
	</tr>
</table>';
	}

	protected function printFooterTable(){
		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.exit_close();");
		$yes_button = we_html_button::create_button(we_html_button::OK, "javascript:press_ok_button();");
		return '
<table id="footer">
	<tr>
		<td class="defaultfont description">' . g_l('modules_banner', '[name]') . '</td>
		<td class="defaultfont" style="text-align:left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '</td>
	</tr>
</table><div id="footerButtons">' . we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button) . '</div>';
	}

	protected function printHeaderTable($extra = '', $append = false){
		$makefolderState = permissionhandler::hasPerm("NEW_BANNER");
		return parent::printHeaderTable('<td>' .
				we_base_jsCmd::singleCmd('updateSelectData', [
					'makefolderState' => $makefolderState
					]
				) .
				we_html_button::create_button('fa:btn_new_bannergroup,fa-plus,fa-lg fa-folder', "javascript:if(top.fileSelect.data.makefolderState){top.drawNewFolder();}", true, 0, 0, "", "", $makefolderState ? false : true) .
				'</td>');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
			we_html_element::jsScript(JS_DIR . 'selectors/bannerdir_selector.js');
	}

	protected function printCmdAddEntriesHTML(we_base_jsCmd $weCmd){
		$entries = [];
		$this->query();
		while($this->db->next_record()){
			$entries[] = [
				$this->db->f("ID"),
				$this->db->f("Text"),
				$this->db->f("IsFolder"),
				$this->db->f("Path")
			];
		}
		$weCmd->addCmd('addEntries', $entries);
	}

	protected function printCreateFolderHTML(){
		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');

		$js = '';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if(!$txt){
			$weCmd->addCmd('msg', ['msg' => g_l('modules_banner', '[group_empty]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} else {
			$folder = new we_folder();
			$folder->we_new($this->table, $this->dir, $txt);
			$this->db->query('SELECT ID FROM ' . $this->table . ' WHERE Path="' . $this->db->escape($folder->Path) . '"');
			if($this->db->next_record()){
				$weCmd->addCmd('msg', ['msg' => sprintf(g_l('modules_banner', '[group_path_exists]'), $folder->Path), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
			} else if(preg_match('|[%/\\"\']|', $folder->Text)){
				$weCmd->addCmd('msg', ['msg' => g_l('modules_banner', '[wrongtext]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
			} else {
				$folder->we_save();
				$weCmd->addCmd('updateSelectData', [
					'makeNewFolder' => false
				]);
				if($this->canSelectDir){
					$weCmd->addCmd('updateSelectData', [
						'currentID' => $folder->ID,
						'currentPath' => $folder->Path,
						'currentText' => $folder->Text
					]);
				}
				$weCmd->addCmd('makeNewTreeEntry', [
					'id' => $folder->ID,
					'parentid' => $folder->ParentID,
					'text' => $txt,
					'open' => 1,
					'contenttype' => 'folder',
					'table' => $this->table
				]);
			}
		}

		$js.=$this->printCmdAddEntriesHTML($weCmd) .
			'top.selectFile(top.fileSelect.data.currentID);';
		$this->setWriteSelectorData($weCmd);
		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds() .
			we_html_element::jsElement($js), we_html_element::htmlBody());
	}

	protected function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->table . ' WHERE IsFolder=1 AND ParentID=' . intval($this->dir));
	}

	protected function printDoRenameFolderHTML(){
		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');

		$js = '
top.fileSelect.data.makeNewFolder=false;';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if(!$txt){
			$weCmd->addCmd('msg', ['msg' => g_l('modules_banner', '[group_empty]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} else {
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->Filename = $txt;
			$folder->Path = $folder->getPath();
			$this->db->query('SELECT ID,Text FROM ' . $this->table . ' WHERE Path="' . $this->db->escape($folder->Path) . '" AND ID!=' . intval($this->we_editDirID));
			if($this->db->next_record()){
				$weCmd->addCmd('msg', ['msg' => sprintf(g_l('modules_banner', '[group_path_exists]')), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
			} else {
				if(preg_match('/[%/\\"\']/', $folder->Text)){
					$weCmd->addCmd('msg', ['msg' => g_l('modules_banner', '[wrongtext]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
				} else {
					if(f('SELECT Text FROM ' . $this->table . ' WHERE ID=' . intval($this->we_editDirID), 'Text', $this->db) != $txt){
						$folder->we_save();
						$weCmd->addCmd('updateTreeEntry', ['id' => $folder->ID, 'parentid' => $folder->ParentID, 'text' => $txt]);
						if($this->canSelectDir){
							$weCmd->addCmd('updateSelectData', [
								'currentPath' => $folder->Path,
								'currentID' => $folder->ID,
								'currentText' => $folder->Text
							]);
						}
						$js.= ($this->canSelectDir ? '
top.document.getElementsByName("fname")[0].value = top.fileSelect.data.currentText;
' : '');
					}
				}
			}
		}
		$js.=$this->printCmdAddEntriesHTML($weCmd) . '
top.selectFile(top.fileSelect.data.currentID);';
		$this->setWriteSelectorData($weCmd);
		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds() . we_html_element::jsElement($js), we_html_element::htmlBody());
	}

	public function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		parent::printHTML($what, false);
	}

}
