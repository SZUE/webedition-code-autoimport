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

	function printCreateFolderHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop() .
		'<script type="text/javascript"><!--
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if(!$txt){
			echo we_message_reporting::getShowMessageCall(g_l('weEditor', '[folder][filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->we_new();
			$folder->setParentID($this->dir);
			$folder->Table = $this->table;
			$folder->Text = $txt;
			$folder->CreationDate = time();
			$folder->ModDate = time();
			$folder->Filename = $txt;
			$folder->Published = time();
			$folder->Path = $folder->getPath();
			$folder->CreatorID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
			$folder->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
			$this->db->query("SELECT ID FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($folder->Path) . "'");
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('weEditor', '[folder][response_path_exists]'), $folder->Path);
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(preg_match('-[<>?":|\\/*]-', $folder->Filename)){
				$we_responseText = sprintf(g_l('weEditor', '[folder][we_filename_notValid]'), $folder->Path);
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$folder->we_save();
				echo 'var ref;
if(top.opener.top.content.makeNewEntry) ref = top.opener.top.content;
else if(top.opener.top.opener) ref = top.opener.top.opener.top;
ref.makeNewEntry(' . $folder->ID . ',"' . $folder->ParentID . '","' . $txt . '",1,"' . $folder->ContentType . '","' . $this->table . '");
';
				if($this->canSelectDir){
					echo 'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
';
				}
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

	function printDoRenameFolderHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop() .
		'<script type="text/javascript"><!--
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if(!$txt){
			echo we_message_reporting::getShowMessageCall(g_l('weEditor', '[folder][filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->ModDate = time();
			$folder->Filename = $txt;
			$folder->Published = time();
			$folder->Path = $folder->getPath();
			$folder->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
			$this->db->query("SELECT ID,Text FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($folder->Path) . "' AND ID != " . intval($this->we_editDirID));
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('weEditor', '[folder][response_path_exists]'), $folder->Path);
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(preg_match('-[<>?":|\\/*]-', $folder->Filename)){
				$we_responseText = sprintf(g_l('weEditor', '[folder][we_filename_notValid]'), $folder->Path);
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editDirID), "Text", $this->db) != $txt){
				$folder->we_save();
				echo 'var ref;
if(top.opener.top.content.makeNewEntry){
	ref = top.opener.top.content;
}else if(top.opener.top.opener){
	ref = top.opener.top.opener.top;
}
';
				echo 'ref.updateEntry({id:' . $folder->ID . ',text:"' . $txt . '",parentid:"' . $folder->ParentID . '"});';
				if($this->canSelectDir){
					echo 'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
';
				}
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
		$this->db->query('SELECT ' . $this->db->escape($this->fields) . ' FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($this->dir) .
			getWsQueryForSelector(NEWSLETTER_TABLE) .
			($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : '')
		);
	}

	protected function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->db->next_record()){
			$ret.='top.addEntry(' . $this->db->f("ID") . ',"' . $this->db->f("Text") . '",' . $this->db->f("IsFolder") . ',"' . $this->db->f("Path") . '");';
		}

		$ret.=' function startFrameset(){'.($this->userCanMakeNewDir() ?
				'top.enableNewFolderBut();' :
				'top.disableNewFolderBut();').'}';
		return $ret;
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
			we_html_element::jsScript(JS_DIR . 'selectors/newsletterdir_selector.js');
	}

	function printHeaderHeadlines(){
		return '
<table class="headerLines" width="550">
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector"colspan="2"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[filename]') . '</a></th>
	</tr>
	<tr>
		<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
		<td width="200">' . we_html_tools::getPixel(200, 1) . '</td>
		<td width="300">' . we_html_tools::getPixel(300, 1) . '</td>
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

	function printHTML($what = we_selector_file::FRAMESET){
		parent::printHTML($what, false);
	}

}
