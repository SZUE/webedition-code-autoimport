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
class we_selector_delete extends we_selector_file{

	function __construct($id, $table = FILE_TABLE){
		parent::__construct($id, $table);
		$this->title = g_l('fileselector', '[delSelector][title]');
	}

	function printHTML($what = we_selector_file::FRAMESET){
		switch($what){
			case self::HEADER:
				$this->printHeaderHTML();
				break;
			case self::FOOTER:
				$this->printFooterHTML();
				break;
			case self::BODY:
				$this->printBodyHTML();
				break;
			case self::CMD:
				$this->printCmdHTML();
				break;
			case self::DEL:
				$this->printDoDelEntryHTML();
				break;
			case self::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

	protected function printFramesetJSFunctions(){
		$tmp = (isset($_SESSION['weS']['seemForOpenDelSelector']['ID']) ? $_SESSION['weS']['seemForOpenDelSelector']['ID'] : 0);
		unset($_SESSION['weS']['seemForOpenDelSelector']['ID']);

		return parent::printFramesetJSFunctions() . we_html_element::jsElement('
function deleteEntry(){
	if(confirm(\'' . g_l('fileselector', '[deleteQuestion]') . '\')){
		var todel = "";
		var docIsOpen = false;
		for	(var i=0;i < entries.length; i++){
			if(isFileSelected(entries[i].ID)){
				todel += entries[i].ID + ",";' .
						($tmp ? '
						if(entries[i].ID=="' . $_SESSION['weS']['seemForOpenDelSelector']['ID'] . '") {
							docIsOpen = true;
						}' : '') . '
			}
		}
		if (todel) {
			todel = "," + todel;
		}

		top.fscmd.location.replace(top.queryString(' . self::DEL . ',top.currentID)+"&todel="+encodeURI(todel));
		top.fsfooter.disableDelBut();

		if(docIsOpen) {
			top.opener.top.we_cmd("close_all_documents");
			top.opener.top.we_cmd("start_multi_editor");
		}
	}
}');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() . we_html_element::jsScript(JS_DIR . 'selectors/delete_selector.js');
	}

	protected function printCmdHTML(){
		echo we_html_element::jsElement('
top.clearEntries();' .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				(intval($this->dir) == 0 ? '
top.fsheader.disableRootDirButs();
top.fsfooter.disableDelBut();' : '
top.fsheader.enableRootDirButs();
top.fsfooter.enableDelBut();') . '
top.currentPath = "' . $this->path . '";
top.parentID = "' . $this->values["ParentID"] . '";');
	}

	function renameChildrenPath($id, we_database_base $db = null){
		$db = $db? : new DB_WE();
		$parentPath = f('SELECT Path FROM ' . $db->escape($this->table) . ' WHERE ID=' . intval($id), "", $db);
		$db->query('UPDATE ' . $db->escape($this->table) . ' SET Path=CONCAT("' . $parentPath . '/",Text) WHERE ParentID=' . intval($id));
		$db->query('SELECT ID FROM ' . $db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($id));
		$all = $db->getAll(true);
		foreach($all as $id){
			$this->renameChildrenPath($id, $db);
		}
	}

	function printDoDelEntryHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop();
		if(($del = we_base_request::_(we_base_request::RAW, "todel"))){
			$_SESSION['weS']['todel'] = $del;
			echo we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
top.opener.top.we_cmd("del_frag", "' . $del . '");
top.close();');
		}
		echo '</head><body></body></html>';
	}

	protected function printFooterTable(){
		if($this->values["Text"] === "/"){
			$this->values["Text"] = "";
		}
		$okBut = we_html_button::create_button("delete", "javascript:if(document.we_form.fname.value==''){top.exit_close();}else{top.deleteEntry();}", true, 100, 22, "", "", true, false);

		$cancelbut = we_html_button::create_button("cancel", "javascript:top.exit_close();");
		$buttons = ($okBut ? we_html_button::position_yes_no_cancel($okBut, null, $cancelbut) : $cancelbut);

		return '
<table class="footer">
	<tr>
		<td class="defaultfont">
			<b>' . g_l('fileselector', '[filename]') . '</b>
		</td>
		<td></td>
		<td class="defaultfont" align="left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '
		</td>
	</tr>
	<tr>
		<td width="70"></td>
		<td width="10"></td>
		<td></td>
	</tr>
</table><div id="footerButtons">' . $buttons . '</div>';
	}

	function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->dir) . ' AND((1' . we_users_util::makeOwnersSql() . ')' .
				getWsQueryForSelector($this->table, false) . ')' . ($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : '')
		);
	}

}
