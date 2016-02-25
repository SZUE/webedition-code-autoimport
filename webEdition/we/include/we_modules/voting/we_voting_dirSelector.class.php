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

	function printHeaderHeadlines(){
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

	protected function printHeaderTable($extra = '', $append = false){
		$makefolderState = permissionhandler::hasPerm("NEW_VOTING");
		return parent::printHeaderTable('<td>' .
				we_html_element::jsElement('makefolderState=' . intval($makefolderState) . ';') .
				we_html_button::create_button('fa:btn_new_dir,fa-plus,fa-lg fa-folder', "javascript:if(makefolderState){top.drawNewFolder();}", true, 0, 0, "", "", $makefolderState ? false : true) .
				'</td>');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
			we_html_element::jsScript(JS_DIR . 'selectors/votingdir_selector.js');
	}

	protected function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->db->next_record()){
			$ret.= 'top.addEntry(' . $this->db->f("ID") . ',"' . $this->db->f("Text") . '",' . $this->db->f("IsFolder") . ',"' . $this->db->f("Path") . '");';
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
		$txt = rawurldecode(we_base_request::_(we_base_request::STRING, 'we_FolderText_tmp', ''));

		if(!$txt){
			echo we_message_reporting::getShowMessageCall(g_l('modules_voting', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->we_new($this->table, $this->dir, $txt);
			$this->db->query('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($folder->Path) . '"');
			if($this->db->next_record()){
				echo we_message_reporting::getShowMessageCall(g_l('modules_voting', '[folder_path_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(we_voting_voting::filenameNotValid($folder->Text)){
				echo we_message_reporting::getShowMessageCall(g_l('modules_voting', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$folder->we_save();
				echo 'var ref;
if(top.opener.top.content.makeNewEntry){
	ref = top.opener.top.content;
	ref.treeData.makeNewEntry({id:' . $folder->ID . ',parentid:' . $folder->ParentID . ',text:"' . $txt . '",open:1,contenttype:"folder",table:"' . $this->table . '",published:1});
}
' . ($this->canSelectDir ?
					'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
' : '');
			}
		}

		echo
		$this->printCmdAddEntriesHTML() .
		$this->printCMDWriteAndFillSelectorHTML() .
		'top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
	}

	function query(){
		$this->db->query('SELECT ' . $this->db->escape($this->fields) . ' FROM ' .
			$this->db->escape($this->table) .
			' WHERE IsFolder=1 AND ParentID=' . intval($this->dir) . ' ' . self::getUserExtraQuery($this->table));
	}

	static function getUserExtraQuery($table, $useCreatorID = true){
		$userExtraSQL = ' AND ((1 ' . we_users_util::makeOwnersSql(false) . ') ';

		if(get_ws($table)){
			$userExtraSQL .= getWsQueryForSelector($table);
		} else if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!permissionhandler::hasPerm("ADMINISTRATOR"))){
			$wsQuery = "";
			$ac = we_users_util::getAllowedClasses($this->db);
			foreach($ac as $cid){
				$path = id_to_path($cid, OBJECT_TABLE);
				$wsQuery .= ' Path LIKE "' . $path . '/%" OR Path="' . $path . '" OR ';
			}
			if($wsQuery){
				$userExtraSQL .= ' AND (' . substr($wsQuery, 0, strlen($wsQuery) - 3) . ')';
			}
		} else {
			$userExtraSQL.=' OR RestrictOwners=0 ';
		}
		return $userExtraSQL . ')';
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
			echo we_message_reporting::getShowMessageCall(g_l('modules_voting', '[folder_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->Filename = $txt;
			$folder->Path = $folder->getPath();
			$this->db->query("SELECT ID,Text FROM " . $this->db->escape($this->table) . ' WHERE Path="' . $folder->Path . '" AND ID!=' . $this->we_editDirID);
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('modules_voting', '[folder_exists]'), $folder->Path);
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(preg_match('/[%/\\"\']/', $folder->Text)){
				$we_responseText = g_l('modules_voting', '[wrongtext]');
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(f("SELECT Text FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->we_editDirID), "", $this->db) != $txt){
				$folder->we_save();
				echo 'var ref;
if(top.opener.top.content.treeData){
	ref = top.opener.top.content;
	ref.treeData.updateEntry({id:' . $folder->ID . ',text:"' . $txt . '",parentid:"' . $folder->ParentID . '"});
}
' . ($this->canSelectDir ?
					'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
' : '');
			}
		}

		echo
		$this->printCmdAddEntriesHTML() .
		$this->printCMDWriteAndFillSelectorHTML() .
		'top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
	}

	protected function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
g_l.newFolder="' . g_l('modules_voting', '[newFolder]') . '";
');
	}

	function printHTML($what = we_selector_file::FRAMESET, $withPreview = false){
		parent::printHTML($what, $withPreview);
	}

}
