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
class we_navigation_dirSelector extends we_selector_directory{

	function __construct($id, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $we_editDirID = '', $FolderText = ''){
		parent::__construct($id, NAVIGATION_TABLE, stripslashes($JSIDName), stripslashes($JSTextName), $JSCommand, $order, '', $we_editDirID, $FolderText);
		$this->title = g_l('fileselector', '[navigationDirSelector][title]');
		$this->userCanMakeNewFolder = true;
		$this->fields.=',Charset';
	}

	function printHeaderHeadlines(){
		return '
<table class="headerLines" style="width:550px;">
<colgroup><col style="width:25px;"/><col style="width:200px;"/><col style="width:300px;"/></colgroup>
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector"colspan="2"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('navigation', '[name]') . '</a></th>
	</tr>
</table>';
	}

	protected function printFooterTable(){
		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, 'javascript:top.exit_close();');
		$yes_button = we_html_button::create_button(we_html_button::OK, "javascript:press_ok_button();");
		return '
<table id="footer">
	<tr>
		<td class="defaultfont description">' . g_l('navigation', '[name]') . '</td>
		<td class="defaultfont" style="text-align:left"><div id="showDiv" style="width:100%; height:2.2ex; background-color: #dce6f2; border: #AAAAAA solid 1px;"></div><div style="display:none;">' . we_html_tools::htmlTextInput('fname', 24, $this->values['Text'], '', 'style="width:100%" readonly="readonly"') . '</div></td>
	</tr>
</table><div id="footerButtons">' . we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button) . '</div>';
	}

	protected function printHeaderTable($extra = '', $append = false){
		$makefolderState = permissionhandler::hasPerm("EDIT_NAVIGATION");
		return parent::printHeaderTable('<td>' .
				we_html_element::jsElement('makefolderState=' . intval($makefolderState) . ';') .
				we_html_button::create_button('fa:btn_new_dir,fa-plus,fa-lg fa-folder', "javascript:if(makefolderState){top.drawNewFolder();}", true, 0, 0, "", "", $makefolderState ? false : true) .
				'</td>');
	}

	protected function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->db->next_record()){
			$text = $this->db->f('Text');
			$charset = $this->db->f('Charset');
			if(function_exists('mb_convert_encoding') && $charset){
				$text = mb_convert_encoding($this->db->f('Text'), 'HTML-ENTITIES', $charset);
			}
			$ret.='top.addEntry(' . $this->db->f('ID') . ',"' . $text . '",' . $this->db->f('IsFolder') . ',"' . $this->db->f('Path') . '");';
		}
		return $ret;
	}

	function printCreateFolderHTML(){
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = rawurldecode(we_base_request::_(we_base_request::FILE, 'we_FolderText_tmp', ''));

		$js = 'top.clearEntries();
top.makeNewFolder=false;';

		if(!$txt){
			$js.=we_message_reporting::getShowMessageCall(g_l('navigation', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->we_new($this->table, $this->dir, $txt);
			if(f('SELECT 1 FROM ' . $this->table . ' WHERE Path="' . $folder->Path . '"', '', $this->db)){
				$js.=we_message_reporting::getShowMessageCall(g_l('navigation', '[folder_path_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(we_navigation_navigation::filenameNotValid($folder->Text)){
				$js.=we_message_reporting::getShowMessageCall(g_l('navigation', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$folder->we_save();
				$js.='
if(top.opener.top.treeData.makeNewEntry){
	top.opener.top.treeData.makeNewEntry({id:' . $folder->ID . ',parentid:' . $folder->ParentID . ',text:"' . $txt . '",open:1,contenttype:"folder",table:"' . $this->table . '",published:0,order:0});
}' .
					($this->canSelectDir ?
						'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";' :
						'');
			}
		}

		echo we_html_tools::getHtmlTop() .
		we_html_element::jsElement(
			$js .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.selectFile(top.currentID);'
		) .
		'</head><body></body></html>';
	}

	function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->table . ' WHERE IsFolder=1 AND ParentID=' . intval($this->dir) . ' ' . getWsQueryForSelector(NAVIGATION_TABLE) . ' ORDER BY Ordn, (text REGEXP "^[0-9]") DESC,ABS(text),Text');
	}

	function printDoRenameFolderHTML(){
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;

		$js = 'top.clearEntries();
top.makeNewFolder=false;';
		if(!$txt){
			$js.=we_message_reporting::getShowMessageCall(g_l('navigation', '[folder_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->Filename = $txt;
			$folder->Path = $folder->getPath();
			$this->db->query('SELECT ID,Text FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($folder->Path) . '" AND ID!=' . intval($this->we_editDirID));
			if($this->db->next_record()){
				$js.=we_message_reporting::getShowMessageCall(sprintf(g_l('navigation', '[folder_exists]'), $folder->Path), we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(strpbrk($folder->Text, '%/\\"\'') !== false){
				$js.=we_message_reporting::getShowMessageCall(g_l('navigation', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editDirID), "Text", $this->db) != $txt){
				$folder->we_save();
				$js.='var ref;
if(top.opener.top.treeData.updateEntry){
	ref = top.opener.top;
	ref.treeData.updateEntry({id:' . $folder->ID . ',text:"' . $txt . '",parentid:"' . $folder->ParentID . '"});
}' . ($this->canSelectDir ?
						'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
' :
						''
					);
			}
		}

		echo we_html_tools::getHtmlTop() .
		we_html_element::jsElement(
			$js .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.selectFile(top.currentID);'
		) .
		'</head><body></body></html>';
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() . we_html_element::jsScript(JS_DIR . 'selectors/naviagationDir_selector.js');
	}

	protected function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
g_l.newFolder="' . g_l('navigation', '[newFolder]') . '";
');
	}

	function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		parent::printHTML($what, false);
	}

}
