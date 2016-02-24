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
		'<script><!--
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		$folder = new we_folder();
		$folder->we_new($this->table, $this->dir, $txt);
		if(($msg = $folder->checkFieldsOnSave())){
			echo we_message_reporting::getShowMessageCall($msg, we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder->we_save();
			echo 'var ref;
if(top.opener.top.content.makeNewEntry){
	ref = top.opener.top.content;
	ref.treeData.makeNewEntry({id:' . $folder->ID . ',parentid:' . $folder->ParentID . ',text:"' . $txt . '",open:1,contenttype:"' . $folder->ContentType . '",table:"' . $this->table . '",published:1});
}
' .
			($this->canSelectDir ?
					'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
' : '');
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
		'<script><!--
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		$folder = new we_folder();
		$folder->initByID($this->we_editDirID, $this->table);
		$folder->Text = $txt;
		$folder->ModDate = time();
		$folder->Filename = $txt;
		$folder->Published = time();
		$folder->Path = $folder->getPath();
		$folder->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
		if(($msg = $folder->checkFieldsOnSave())){
			echo we_message_reporting::getShowMessageCall($msg, we_message_reporting::WE_MESSAGE_ERROR);
		} else {

			$folder->we_save();
			echo 'var ref;
if(top.opener.top.content.makeNewEntry){
	ref = top.opener.top;
}else if(top.opener.top.opener){
	ref = top.opener.top.opener.top;
}
ref.treeData.updateEntry({id:' . $folder->ID . ',text:"' . $txt . '",parentid:"' . $folder->ParentID . '"});';
			if($this->canSelectDir){
				echo 'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
';
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

		$ret.=' function startFrameset(){' . ($this->userCanMakeNewDir() ?
						'top.enableNewFolderBut();' :
						'top.disableNewFolderBut();') . '}';
		return $ret;
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
				we_html_element::jsScript(JS_DIR . 'selectors/newsletterdir_selector.js');
	}

	function printHeaderHeadlines(){
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

	function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		parent::printHTML($what, false);
	}

}
