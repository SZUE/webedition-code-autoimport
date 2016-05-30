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

	var $fields = 'ID,ParentID,Text,Path,IsFolder';

	function __construct($id, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $we_editDirID = 0, $FolderText = ''){
		parent::__construct($id, BANNER_TABLE, $JSIDName, $JSTextName, $JSCommand, $order, '', $we_editDirID, $FolderText);
		$this->title = g_l('fileselector', '[bannerDirSelector][title]');
		$this->userCanMakeNewFolder = true;
	}

	function printHeaderHeadlines(){
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
						we_html_element::jsElement('makefolderState=' . intval($makefolderState) . ';') .
						we_html_button::create_button('fa:btn_new_bannergroup,fa-plus,fa-lg fa-folder', "javascript:if(makefolderState){top.drawNewFolder();}", true, 0, 0, "", "", $makefolderState ? false : true) .
						'</td>');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
				we_html_element::jsScript(JS_DIR . 'selectors/bannerdir_selector.js');
	}

	protected function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->db->next_record()){
			$ret.='top.addEntry(' . $this->db->f("ID") . ',"' . $this->db->f("Text") . '",' . $this->db->f("IsFolder") . ',"' . $this->db->f("Path") . '");';
		}
		return $ret;
	}

	function printCreateFolderHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop() .
		'<script><!--
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if(!$txt){
			echo we_message_reporting::getShowMessageCall(g_l('modules_banner', '[group_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->we_new($this->table, $this->dir, $txt);
			$this->db->query('SELECT ID FROM ' . $this->table . ' WHERE Path="' . $this->db->escape($folder->Path) . '"');
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('modules_banner', '[group_path_exists]'), $folder->Path);
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else if(preg_match('|[%/\\"\']|', $folder->Text)){
				$we_responseText = g_l('modules_banner', '[wrongtext]');
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$folder->we_save();
				echo 'var ref;
if(top.opener.top.content.makeNewEntry){
	ref = top.opener.top.content;
	ref.treeData.makeNewEntry({id:' . $folder->ID . ',parentid:' . $folder->ParentID . ',text:"' . $txt . '",open:1,contenttype:"folder",table:"' . $this->table . '"});
}
' .
				($this->canSelectDir ?
						'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
' : '');
			}
		}


		echo $this->printCmdAddEntriesHTML() .
		$this->printCMDWriteAndFillSelectorHTML() .
		'top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
	}

	function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->table . ' WHERE IsFolder=1 AND ParentID=' . intval($this->dir));
	}

	function printDoRenameFolderHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop() .
		'<script><!--
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if(!$txt){
			echo we_message_reporting::getShowMessageCall(g_l('modules_banner', '[group_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->Filename = $txt;
			$folder->Path = $folder->getPath();
			$this->db->query('SELECT ID,Text FROM ' . $this->table . ' WHERE Path="' . $this->db->escape($folder->Path) . '" AND ID!=' . intval($this->we_editDirID));
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('modules_banner', '[group_path_exists]'), $folder->Path);
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				if(preg_match('/[%/\\"\']/', $folder->Text)){
					$we_responseText = g_l('modules_banner', '[wrongtext]');
					echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else {
					if(f('SELECT Text FROM ' . $this->table . ' WHERE ID=' . intval($this->we_editDirID), 'Text', $this->db) != $txt){
						$folder->we_save();
						echo 'var ref;
if(top.opener.top.content.treeData.updateEntry){
	ref = top.opener.top.content;
	ref.treeData.updateEntry({id:' . $folder->ID . ',parentid:"' . $folder->ParentID . '",text:"' . $txt . '"});
}
' . ($this->canSelectDir ? '
top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
' : '');
					}
				}
			}
		}

		echo $this->printCmdAddEntriesHTML() .
		$this->printCMDWriteAndFillSelectorHTML() . '
top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
	}

	protected function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
g_l.newbannergroup="' . g_l('modules_banner', '[newbannergroup]') . '";
');
	}

	function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		parent::printHTML($what, false);
	}

}
