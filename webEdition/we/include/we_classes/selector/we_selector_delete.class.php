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

	function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		switch($what){
			case self::DEL:
				$this->printDoDelEntryHTML();
				break;
			default:
				parent::printHTML($what);
		}
	}

	protected function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
g_l.deleteQuestion="' . g_l('fileselector', '[deleteQuestion]') . '";
options.seemForOpenDelSelector=' . intval(isset($_SESSION['weS']['seemForOpenDelSelector']['ID']) ? $_SESSION['weS']['seemForOpenDelSelector']['ID'] : 0) . ';
consts.DEL=' . self::DEL . ';
');
		unset($_SESSION['weS']['seemForOpenDelSelector']['ID']);
	}

	protected function printCmdHTML($morejs = ''){
		parent::printCmdHTML((intval($this->dir) ? 'top.enableDelBut();' : 'top.disableDelBut();') . $morejs);
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
		$js = '';
		if(($del = we_base_request::_(we_base_request::INT, "todel"))){
			$_SESSION['weS']['fragDel'] = [
				'todel' => $del,
				'we_not_deleted_entries' => [],
				'we_go_seem_start' => false,
			];
			$js = we_html_element::jsElement('
top.opener.top.we_cmd("del_frag", "' . $del . '");
top.close();');
		}
		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', $js, we_html_element::htmlBody());
	}

	protected function printFooterTable(){
		if($this->values["Text"] === "/"){
			$this->values["Text"] = "";
		}
		$okBut = we_html_button::create_button(we_html_button::DELETE, "javascript:if(document.we_form.fname.value==''){top.exit_close();}else{top.deleteEntry();}", true, 100, 22, "", "", true, false);

		$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.exit_close();");

		return '
<table id="footer">
	<tr>
		<td class="defaultfont description">' . g_l('fileselector', '[filename]') . '</td>
		<td class="defaultfont" style="text-align:left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", 'style="width:100%" readonly="readonly"') . '</td>
	</tr>
</table><div id="footerButtons">' . ($okBut ? we_html_button::position_yes_no_cancel($okBut, null, $cancelbut) : $cancelbut) . '</div>';
	}

	function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->dir) . ' AND((1' . we_users_util::makeOwnersSql() . ')' .
			getWsQueryForSelector($this->table, false) . ')' . ($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : '')
		);
	}

}
