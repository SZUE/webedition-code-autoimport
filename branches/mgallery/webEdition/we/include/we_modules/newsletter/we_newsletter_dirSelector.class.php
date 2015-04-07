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

	var $fields = "ID,ParentID,Text,Path,IsFolder,Icon";

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
			} else {
				if(preg_match('-[<>?":|\\/*]-', $folder->Filename)){
					$we_responseText = sprintf(g_l('weEditor', '[folder][we_filename_notValid]'), $folder->Path);
					echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else {
					$folder->we_save();
					echo 'var ref;
if(top.opener.top.content.makeNewEntry) ref = top.opener.top.content;
else if(top.opener.top.opener) ref = top.opener.top.opener.top;
ref.makeNewEntry("' . $folder->Icon . '",' . $folder->ID . ',"' . $folder->ParentID . '","' . $txt . '",1,"' . $folder->ContentType . '","' . $this->table . '");
';
					if($this->canSelectDir){
						echo 'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.fsfooter.document.we_form.fname.value = "' . $folder->Text . '";
';
					}
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
			} else {
				if(preg_match('-[<>?":|\\/*]-', $folder->Filename)){
					$we_responseText = sprintf(g_l('weEditor', '[folder][we_filename_notValid]'), $folder->Path);
					echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else {
					if(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editDirID), "Text", $this->db) != $txt){
						$folder->we_save();
						echo 'var ref;
if(top.opener.top.content.makeNewEntry) ref = top.opener.top.content;
else if(top.opener.top.opener) ref = top.opener.top.opener.top;
';
						echo 'ref.updateEntry(' . $folder->ID . ',"' . $txt . '","' . $folder->ParentID . '","' . $this->table . '");
';
						if($this->canSelectDir){
							echo 'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.fsfooter.document.we_form.fname.value = "' . $folder->Text . '";
';
						}
					}
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

	protected function printFramesetJSFunctionAddEntries(){
		$ret = '';
		while($this->next_record()){
			$ret.='addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . addcslashes($this->f("Text"), '"') . '",' . $this->f("IsFolder") . ',"' . addcslashes($this->f("Path"), '"') . '");';
		}
		return we_html_element::jsElement($ret);
	}

	protected function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->next_record()){
			$ret.='top.addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '");' . "\n";
		}
		$ret.=($this->userCanMakeNewDir() ?
						'top.fsheader.enableNewFolderBut();' :
						'top.fsheader.disableNewFolderBut();');
		return $ret;
	}

	protected function printFramesetJSFunctionEntry(){
		return we_html_element::jsElement('
		function addEntry(ID,icon,text,isFolder,path){
		entries[entries.length] = new entry(ID,icon,text,isFolder,path);
		}
		function entry(ID,icon,text,isFolder,path){
		this.ID=ID;
		this.icon=icon;
		this.text=text;
		this.isFolder=isFolder;
		this.path=path;
		}');
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

	protected function getWriteBodyHead(){
		return we_html_element::jsElement('
var ctrlpressed=false;
var shiftpressed=false;
var inputklick=false;
var wasdblclick=false;
var tout=null;
function weonclick(e){
if(top.makeNewFolder ||  top.we_editDirID){
if(!inputklick){
top.makeNewFolder =  top.we_editDirID=false;
document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);
document.we_form.submit();
}else{
inputklick=false;
}
	}else{
inputklick=false;
if(document.all){
if(e.ctrlKey || e.altKey){ ctrlpressed=true;}
if(e.shiftKey){ shiftpressed=true;}
}else{
if(e.altKey || e.metaKey || e.ctrlKey){ ctrlpressed=true;}
if(e.shiftKey){ shiftpressed=true;}
}
if(top.options.multiple){
if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}
}else{
top.unselectAllFiles();
}
	}');
	}

	protected function printFramesetJSFunctioWriteBody(){
		ob_start();
		?><script type="text/javascript"><!--
					function writeBody(d) {
						var body = (top.we_editDirID ?
										'<input type="hidden" name="what" value="' + top.consts.DORENAMEFOLDER + '" />' +
										'<input type="hidden" name="we_editDirID" value="' + top.we_editDirID + '" />' :
										'<input type="hidden" name="what" value="' + top.consts.CREATEFOLDER + '" />'
										) +
										'<input type="hidden" name="order" value="' + top.order + '" />' +
										'<input type="hidden" name="rootDirID" value="' + top.options.rootDirID + '" />' +
										'<input type="hidden" name="table" value="' + top.options.table + '" />' +
										'<input type="hidden" name="id" value="' + top.currentDir + '" />' +
										'<table class="selector">' +
										(makeNewFolder ?
														'<tr style="background-color:#DFE9F5;">' +
														'<td align="center"><img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + top.consts.FOLDER_ICON + '"/></td>' +
														'<td><input type="hidden" name="we_FolderText" value="<?php echo g_l('fileselector', '[new_folder_name]') ?>" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="<?php echo g_l('fileselector', '[new_folder_name]') ?>"  class="wetextinput" style="width:100%" /></td>' +
														'</tr>' :
														'');
						for (i = 0; i < entries.length; i++) {
							var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true"';
							var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
							body += '<tr id="line_' + entries[i].ID + '" style="' + ((entries[i].ID == top.currentID && (!makeNewFolder)) ? 'background-color:#DFE9F5;' : '') + 'cursor:pointer;' + ((we_editDirID != entries[i].ID) ? '' : '') + '"' + ((we_editDirID || makeNewFolder) ? '' : onclick) + (entries[i].isFolder ? ondblclick : '') + ' >' +
											'<td class="selector" width="25" align="center">' +
											'<img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + entries[i].icon + '"/>' +
											'</td>' +
											(we_editDirID == entries[i].ID ?
															'<td class="selector"><input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
															'<td class="selector filename" style="" ><div class="cutText">' + entries[i].text + '</div>'
															) +
											'</td></tr>';
						}
						d.innerHTML = '<form name="we_form" target="fscmd" action="' + top.options.formtarget + '" onsubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">' + body + '</table></form>';

						if (makeNewFolder || top.we_editDirID) {
							top.fsbody.document.we_form.we_FolderText_tmp.focus();
							top.fsbody.document.we_form.we_FolderText_tmp.select();
						}
					}
					//-->
		</script>
		<?php
		return ob_get_clean();
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

}
