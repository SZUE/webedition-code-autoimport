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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_users_selector extends we_selector_multiple{

	function __construct($id, $table = USER_TABLE, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $sessionID = '', $rootDirID = 0, $filter = '', $multiple = true){

		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $rootDirID, $multiple, $filter);
		$this->title = g_l('fileselector', '[userSelector][title]');

		//FIXME: fix userSelector String
		//$GLOBALS["l_fileselector"]["filename"] = ($this->filter == "group") ? g_l('fileselector',"[groupname]") : g_l('fileselector',"[username]");
	}

	function setDefaultDirAndID($setLastDir){
		$this->dir = $setLastDir ? (isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$this->table]) : 0 ) : 0;
		$foo = getHash('SELECT IsFolder,Text,Path FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), $this->db);
		if(isset($foo['IsFolder']) && $foo['IsFolder'] && $this->dir){
			$this->values = array(
				'ParentID' => $this->dir,
				'Text' => $foo['Text'],
				'Path' => $foo['Path'],
				'IsFolder' => 1);
			$this->path = $foo['Path'];
			$this->id = $this->dir;
		} else {
			$this->dir = 0;
			$this->values = array(
				'ParentID' => 0,
				'Text' => '',
				'Path' => '',
				'IsFolder' => 1);
			$this->path = '';
			$this->id = 0;
		}
	}

	function printHTML($what = we_selector_file::FRAMESET){
		switch($what){
			case we_selector_file::HEADER:
				$this->printHeaderHTML();
				break;
			case we_selector_file::FOOTER:
				$this->printFooterHTML();
				break;
			case we_selector_file::BODY:
				$this->printBodyHTML();
				break;
			case we_selector_multiple::SETDIR:
				$this->printSetDirHTML();
				break;
			case we_selector_file::CMD:
				$this->printCmdHTML();
				break;
			case we_selector_file::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

	function getFsQueryString($what){
		return $_SERVER['SCRIPT_NAME'] . "?what=$what&table=" . $this->table . "&id=" . $this->id . "&order=" . $this->order . "&filter=" . $this->filter;
	}

	function query(){
		switch($this->filter){
			case 'group':
				$q = ' AND IsFolder=1 ';
				break;
			case 'noalias':
				$q = ' AND Alias=0 ';
				break;
			default:
				$q = '';
		}
		//if(!$_SESSION["perms"]["ADMINISTRATOR"]){
		//	$upid = f("SELECT ParentID FROM ".$this->table." WHERE ID='".$_SESSION["user"]["ID"]."'","ParentID",$this->db);
		//	if($upid) $upath = f("SELECT Path FROM ".$this->table." WHERE ID='".$upid."'","Path",$this->db);
		//	else $upath = "/";
		//}else{
		$upath = '';
		//}
		$this->db->query('SELECT ' . $this->db->escape($this->fields) . ' FROM ' .
			$this->db->escape($this->table) .
			' WHERE ParentID=' . intval($this->dir) .
			($upath ? ' AND Path LIKE "' . $this->db->escape($upath) . '%" ' : '') .
			$q . ($this->order ? (' ORDER BY ' . $this->db->escape($this->order)) : ''));
	}

	function printFramesetJSFunctionQueryString(){
		return we_html_element::jsElement('
function queryString(what,id,o){
	if(!o) o=top.order;
	return \'' . $_SERVER["SCRIPT_NAME"] . '?what=\'+what+\'&table=' . $this->table . '&id=\'+id+"&order="+o+"&filter=' . $this->filter . '";
}');
	}

	function printFramesetJSsetDir(){
		return we_html_element::jsElement('
function setDir(id){' .
				($this->filter == "user" ? '
	currentDir = id;
	top.fscmd.location.replace(top.queryString(' . we_selector_file::CMD . ',id));' : '
	top.fscmd.location.replace(top.queryString(' . we_selector_multiple::SETDIR . ',id));'
				) . '
}');
	}

	function printSetDirHTML(){
		$js = 'top.clearEntries();' .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.fsheader.' . (intval($this->dir) == intval($this->rootDirID) ? 'disable' : 'enable') . 'RootDirButs();';

		if(permissionhandler::hasPerm("ADMINISTRATOR")){
			$go = true;
		} else {
			$rootID = f('SELECT ParentID FROM ' . $this->table . ' WHERE ID=' . intval($_SESSION["user"]["ID"]), 'ParentID', $this->db);
			$rootPath = f('SELECT Path FROM ' . $this->table . ' WHERE ID=' . intval($rootID), 'Path', $this->db);
			$go = (f('SELECT 1 FROM ' . $this->table . ' WHERE ID=' . intval($this->dir) . ' AND Path LIKE "' . $rootPath . '%"', '', $this->db) == '1');
		}
		if($go){
			if($this->id == 0){
				$this->path = '/';
			}
			$js.= 'top.currentPath = "' . $this->path . '";
top.currentID = "' . $this->id . '";
top.fsfooter.document.we_form.fname.value = "' . $this->values["Text"] . '";';
		}
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
		$js.= 'top.currentDir = "' . $this->dir . '";
top.parentID = "' . $this->values["ParentID"] . '";';
		echo we_html_element::jsElement($js);
	}

	function printFramesetSelectFileHTML(){
		return we_html_element::jsElement('
function selectFile(id){
	if(id){
		e=top.getEntry(id);' .
				($this->filter == "user" ? '
			if(!e.isFolder){' : ''
				) . '
				if( top.fsfooter.document.we_form.fname.value != e.text &&
					top.fsfooter.document.we_form.fname.value.indexOf(e.text+",") == -1 &&
					top.fsfooter.document.we_form.fname.value.indexOf(","+e.text+",") == -1 &&
					top.fsfooter.document.we_form.fname.value.indexOf(","+e.text+",") == -1 ){

					top.fsfooter.document.we_form.fname.value =  top.fsfooter.document.we_form.fname.value ?
						(top.fsfooter.document.we_form.fname.value + "," + e.text) :
						e.text;
				}
				top.fsbody.document.getElementById("line_"+id).style.backgroundColor="#DFE9F5";
				currentPath = e.path;
				currentID = id;' .
				($this->filter == "user" ? '
			}' : ''
				) . '

	}else{
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
	}
}');
	}

	function printFooterTable(){
		$cancel_button = we_html_button::create_button("cancel", "javascript:top.exit_close();");
		$yes_button = we_html_button::create_button("ok", "javascript:press_ok_button();");
		$buttons = we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button);
		return '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td colspan="5"><img src="' . IMAGE_DIR . 'umr_h_small.gif" width="100%" height="2" border="0" /></td>
	</tr>
	<tr>
		<td colspan="5">' . we_html_tools::getPixel(5, 5) . '</td>
	</tr>
	<tr>
		<td></td>
		<td class="defaultfont">
			<b>' . g_l('fileselector', "[name]") . '</b>
		</td>
		<td></td>
		<td class="defaultfont" align="left">' . we_html_tools::htmlTextInput("fname", 24, isset($this->values["Text"])?$this->values["Text"]:'', "", 'style="width:100%" readonly="readonly"') . '
		</td>
		<td></td>
	</tr>
	<tr>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
		<td width="70">' . we_html_tools::getPixel(70, 5) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
		<td>' . we_html_tools::getPixel(5, 5) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
	</tr>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="right">' . $buttons . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
	</tr>
</table>';
	}

	function printFooterJSDef(){
		return we_html_element::jsElement("
function press_ok_button() {
	if(document.we_form.fname.value==''&&top.currentType!='group'){
		top.exit_close();
	}else{
		top.exit_open();
	};
}");
	}

}
