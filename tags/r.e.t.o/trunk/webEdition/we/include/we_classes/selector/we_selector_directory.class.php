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
class we_selector_directory extends we_selector_multiple{
	const NEWFOLDER = 7;
	const RENAMEFOLDER = 9;
	const DORENAMEFOLDER = 10;
	const PREVIEW = 11;

	protected $userCanMakeNewFolder = true;
	protected $userCanRenameFolder = true;
	protected $we_editDirID = "";
	protected $FolderText = '';

	function __construct($id, $table = "", $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $sessionID = "", $we_editDirID = 0, $FolderText = "", $rootDirID = 0, $multiple = 0, $filter = ''){
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $rootDirID, $multiple, $filter);
		switch($this->table){
			case FILE_TABLE:
			case TEMPLATES_TABLE:
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$this->fields.= ',ModDate,RestrictOwners,Owners,OwnersReadOnly,CreatorID';
				break;
			default:
		}
		$this->title = g_l('fileselector', '[dirSelector][title]');
		$this->userCanMakeNewFolder = $this->userCanMakeNewDir();
		$this->userCanRenameFolder = $this->userCanRenameFolder();
		$this->we_editDirID = $we_editDirID;
		$this->FolderText = $FolderText;
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
			case we_selector_file::CMD:
				$this->printCmdHTML();
				break;
			case self::SETDIR:
				$this->printSetDirHTML();
				break;
			case self::NEWFOLDER:
				$this->printNewFolderHTML();
				break;
			case self::CREATEFOLDER:
				$this->printCreateFolderHTML();
				break;
			case self::RENAMEFOLDER:
				$this->printRenameFolderHTML();
				break;
			case self::DORENAMEFOLDER:
				$this->printDoRenameFolderHTML();
				break;
			case self::PREVIEW:
				$this->printPreviewHTML();
				break;
			case self::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

	protected function printCmdHTML(){

		echo we_html_element::jsElement('
top.clearEntries();' .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			(intval($this->dir) == intval($this->rootDirID) ?
				'top.fsheader.disableRootDirButs();' :
				'top.fsheader.enableRootDirButs();') .
			'top.currentPath = "' . $this->path . '";
top.parentID = "' . $this->values["ParentID"] . '";
');
	}

	function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($this->dir) . ' AND((1' . we_users_util::makeOwnersSql() . ') ' .
			getWsQueryForSelector($this->table) . ')' . ($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : ''));
	}

	protected function setDefaultDirAndID($setLastDir){
		$this->dir = $setLastDir ? (isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$this->table]) : 0 ) : 0;
		$ws = get_ws($this->table, true);
		if($ws && strpos($ws, ("," . $this->dir . ",")) !== true){
			$this->dir = "";
		}
		$this->id = $this->dir;
		if($this->rootDirID){
			if(!in_parentID($this->dir, $this->rootDirID, $this->table, $this->db)){
				$this->dir = $this->rootDirID;
				$this->id = $this->rootDirID;
			}
		}
		$this->path = '';

		$this->values = array(
			'ParentID' => 0,
			'Text' => '/',
			'Path' => '/',
			'IsFolder' => 1,
			'ModDate' => 0,
			'RestrictOwners' => 0,
			'Owners' => '',
			'OwnersReadOnly' => '',
			'CreatorID' => 0);
	}

	protected function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . "?what=$what&rootDirID=" . $this->rootDirID . "&table=" . $this->table . "&id=" . $this->id . "&order=" . $this->order . (isset($this->open_doc) ? ("&open_doc=" . $this->open_doc) : "");
	}

	protected function printFramesetJSFunctions(){
		return parent::printFramesetJSFunctions() . we_html_element::jsElement('
function drawNewFolder(){
	unselectAllFiles();
	top.fscmd.location.replace(top.queryString(' . self::NEWFOLDER . ',currentDir));
}
function RenameFolder(id){
	unselectAllFiles();
	top.fscmd.location.replace(top.queryString(' . self::RENAMEFOLDER . ',currentDir,"",id));
}');
	}

	protected function printFramesetJSFunctioWriteBody(){
		return we_html_element::jsElement('
function writeBody(d){
	d.open();' .
				self::makeWriteDoc(we_html_tools::getHtmlTop('', '', '4Trans', true) . STYLESHEET_SCRIPT . we_html_element::jsElement('
var ctrlpressed=false;
var shiftpressed=false;
var inputklick=false;
var wasdblclick=false;
var tout=null;
function submitFolderMods(){
//	document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value); document.we_form.submit();
}
document.onclick = weonclick;
function weonclick(e){
	if(top.fspreview.document.body){
		top.fspreview.document.body.innerHTML = "";
	}
#	if(makeNewFolder ||  we_editDirID){
		if(!inputklick){
			document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);document.we_form.submit();
		}else{
			inputklick=false;
		}
#	}else{
		inputklick=false;
		if(document.all){
			if(event.ctrlKey || event.altKey){ ctrlpressed=true;}
			if(event.shiftKey){ shiftpressed=true;}
		}else{
			if(e.altKey || e.metaKey || e.ctrlKey){ ctrlpressed=true;}
			if(e.shiftKey){ shiftpressed=true;}
		}' . ($this->multiple ? '
		if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}' : '
		top.unselectAllFiles();') . '
#	}
}') . '<style type="text/css">
body{
	background-color:white;
	margin:0px;
}
a:link,a:visited,a:hover,a:active
{color:#000;}
</style>
</head>
<body #\'+((makeNewFolder || top.we_editDirID) ? #\' onload="document.we_form.we_FolderText_tmp.focus();document.we_form.we_FolderText_tmp.select();"#\' : "")+#\'>
<form name="we_form" target="fscmd" action="' . $_SERVER["SCRIPT_NAME"] . '" onsubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">
#if(we_editDirID){
	<input type="hidden" name="what" value="' . self::DORENAMEFOLDER . '" />
	<input type="hidden" name="we_editDirID" value="#\' + top.we_editDirID + #\'" />
#}else{
	<input type="hidden" name="what" value="' . self::CREATEFOLDER . '" />
#}
	<input type="hidden" name="order" value="#\'+top.order+#\'" />
	<input type="hidden" name="rootDirID" value="' . $this->rootDirID . '" />
	<input type="hidden" name="table" value="' . $this->table . '" />
	<input type="hidden" name="id" value="#\'+top.currentDir+#\'" />
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
#if(makeNewFolder){
	<tr style="background-color:#DFE9F5;">
		<td align="center"><img src="' . TREE_ICON_DIR . we_base_ContentTypes::FOLDER_ICON . '" width="16" height="18" border="0" /></td>
		<td><input type="hidden" name="we_FolderText" value="' . g_l('fileselector', '[new_folder_name]') . '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' . g_l('fileselector', '[new_folder_name]') . '" class="wetextinput" style="width:100%" /></td>
		<td class="selector">' . date(g_l('date', '[format][default]')) . '</td>
	</tr>
#}
#for(i=0;i < entries.length; i++){
#	var onclick = #\' onclick="weonclick(' . (we_base_browserDetect::isIE() ? "this" : "event") . ');tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(#\'+entries[i].ID+#\',0);}else{top.wasdblclick=0;}\',300);return true"#\';
#	var ondblclick = #\' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(#\'+entries[i].ID+#\',1);return true;"#\';
	<tr id="line_#\'+entries[i].ID+#\'" style="#\' + ((entries[i].ID == top.currentID && (!makeNewFolder) )  ? "background-color:#DFE9F5;" : "")+#\'cursor:pointer;" #\'+((we_editDirID || makeNewFolder) ? "" : onclick)+ (entries[i].isFolder ? ondblclick : "") + #\'>
		<td class="selector" align="center"><img src="' . TREE_ICON_DIR . '#\'+entries[i].icon+#\'" width="16" height="18" border="0" /></td>
#if(we_editDirID == entries[i].ID){
		<td class="selector"><input type="hidden" name="we_FolderText" value="#\'+entries[i].text+#\'" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="#\'+entries[i].text+#\'" class="wetextinput" style="width:100%" />
#}else{
		<td class="selector" style="" title="#\'+entries[i].text+#\'">
			#\'+cutText(entries[i].text,30)+#\'
#}
		</td>
		<td class="selector">#\'+entries[i].modDate+#\'</td>
	</tr>
	<tr><td colspan="3">' . we_html_tools::getPixel(2, 1) . '</td></tr>
#}
	<tr>
		<td width="25">' . we_html_tools::getPixel(25, 2) . '</td>
		<td width="200">' . we_html_tools::getPixel(200, 2) . '</td>
		<td>' . we_html_tools::getPixel(300, 2) . '</td>
	</tr>
</table></form>
</body>') . '
d.close();
}');
	}

	protected function printFramesetJSFunctionQueryString(){
		return we_html_element::jsElement('
		function queryString(what,id,o,we_editDirID){
		if(!o) o=top.order;
		if(!we_editDirID) we_editDirID="";
		return \'' . $_SERVER["SCRIPT_NAME"] . '?what=\'+what+\'&rootDirID=' .
				$this->rootDirID . (isset($this->open_doc) ?
					"&open_doc=" . $this->open_doc : '') .
				'&table=' . $this->table . '&id=\'+id+(o ? ("&order="+o) : "")+(we_editDirID ? ("&we_editDirID="+we_editDirID) : "");
		}');
	}

	protected function printFramesetJSFunctionEntry(){
		return we_html_element::jsElement('
function entry(ID,icon,text,isFolder,path,modDate){
	this.ID=ID;
	this.icon=icon;
	this.text=text;
	this.isFolder=isFolder;
	this.path=path;
	this.modDate=modDate;
}');
	}

	protected function printFramesetJSFunctionAddEntry(){
		return we_html_element::jsElement('
function addEntry(ID,icon,text,isFolder,path,modDate){
	entries[entries.length] = new entry(ID,icon,text,isFolder,path,modDate);
}');
	}

	protected function printFramesetJSFunctionAddEntries(){
		$ret = '';
		while($this->next_record()){
			$ret.='addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . addcslashes($this->f("Text"), '"') . '",' . $this->f("IsFolder") . ',"' . addcslashes($this->f("Path"), '"') . '","' . date(g_l('date', '[format][default]'), (is_numeric($this->f("ModDate")) ? $this->f("ModDate") : 0)) . '");';
		}
		return we_html_element::jsElement($ret);
	}

	function cmdAddEntriesHTML(){
		$this->query();
		$ret = '';
		while($this->next_record()){
			$ret.='top.addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '","' . date(g_l('date', '[format][default]'), (is_numeric($this->f("ModDate")) ? $this->f("ModDate") : 0)) . '");';
		}
		$ret.='top.fsheader.' . ($this->userCanMakeNewDir() ? 'enable' : 'disable') . 'NewFolderBut();';
		return $ret;
		return $ret;
	}

	function printHeaderHeadlines(){
		return '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>' . we_html_tools::getPixel(25, 14) . '</td>
		<td class="selector"><b><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[filename]') . '</a></b></td>
		<td class="selector"><b><a href="#" onclick="javascript:top.orderIt(\'ModDate\');">' . g_l('fileselector', '[modified]') . '</a></b></td>
	</tr>
	<tr>
		<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
		<td width="200">' . we_html_tools::getPixel(200, 1) . '</td>
		<td>' . we_html_tools::getPixel(300, 1) . '</td>
	</tr>
</table>';
	}

	protected function printHeaderJSDef(){
		return parent::printHeaderJSDef() .
			'var makefolderState = ' . ($this->userCanMakeNewFolder ? 1 : 0) . ';';
	}

	protected function printHeaderJS(){
		return parent::printHeaderJS() .
			we_html_button::create_state_changer(false) . '
function disableNewFolderBut(){
	btn_new_dir_enabled = switch_button_state("btn_new_dir", "new_directory_enabled", "disabled", "image");
	makefolderState = 0;
}
function enableNewFolderBut(){

	btn_new_dir_enabled = switch_button_state("btn_new_dir", "new_directory_enabled", "enabled", "image");
	makefolderState = 1;
}';
	}

	protected function userCanSeeDir($showAll = false){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		if(!$showAll){
			if(!in_workspace(intval($this->dir), get_ws($this->table, false, true), $this->table, $this->db)){
				return false;
			}
		}
		return we_users_util::userIsOwnerCreatorOfParentDir($this->dir, $this->table);
	}

	protected function userCanRenameFolder(){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		if(!$this->userHasRenameFolderPerms()){
			return false;
		}
		return true;
	}

	protected function userCanMakeNewDir(){
		if(defined('OBJECT_FILES_TABLE') && ($this->table == OBJECT_FILES_TABLE) && (!$this->dir)){
			return false;
		}
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		if(!$this->userCanSeeDir() || !$this->userHasFolderPerms()){
			return false;
		}
		return true;
	}

	protected function userHasRenameFolderPerms(){
		switch($this->table){
			case FILE_TABLE:
				if(!permissionhandler::hasPerm("CHANGE_DOC_FOLDER_PATH")){
					return false;
				}
				break;
		}
		return true;
	}

	protected function userHasFolderPerms(){

		switch($this->table){
			case FILE_TABLE:
				if(!permissionhandler::hasPerm("NEW_DOC_FOLDER")){
					return false;
				}
				break;
			case TEMPLATES_TABLE:
				if(!permissionhandler::hasPerm("NEW_TEMP_FOLDER")){
					return false;
				}
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				if(!permissionhandler::hasPerm("NEW_OBJECTFILE_FOLDER")){
					return false;
				}
				break;
		}
		return true;
	}

	protected function printFramesetRootDirFn(){
		return we_html_element::jsElement('
function setRootDir(){
	setDir(' . intval($this->rootDirID) . ');
}');
	}

	protected function printCMDWriteAndFillSelectorHTML(){
		$pid = $this->dir;
		$out = '';
		$c = 0;
		while($pid != 0){
			$c++;
			$this->db->query("SELECT ID,Text,ParentID FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($pid));
			if($this->db->next_record()){
				$out = 'top.fsheader.addOption("' . $this->db->f("Text") . '",' . $this->db->f("ID") . ');' . $out;
			}
			$pid = $this->db->f("ParentID");
			if($c > 500){
				$pid = 0;
			}
			if($this->rootDirID){
				if($this->db->f("ID") == $this->rootDirID){
					$pid = 0;
				}
			}
		}
		return '
top.writeBody(top.fsbody.document);
top.fsheader.clearOptions();' .
			($this->rootDirID ? '' : '
top.fsheader.addOption("/",0);') .
			$out . '
top.fsheader.selectIt();';
	}

	protected function printHeaderTable(){
		return '
<table border="0" cellpadding="0" cellspacing="0" width="100%">' .
			$this->printHeaderTableSpaceRow() . '
	<tr valign="middle">
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="70" class="defaultfont"><b>' . g_l('fileselector', '[lookin]') . '</b></td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td><select name="lookin" class="weSelect" size="1" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%">' .
			$this->printHeaderOptions() . '
			</select>' .
			((!defined('OBJECT_TABLE')) || $this->table != OBJECT_TABLE ? '
		</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="40">' . we_html_button::create_button("root_dir", "javascript:if(rootDirButsState){top.setRootDir();}", true, 0, 0, "", "", $this->dir == intval($this->rootDirID), false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="40">' . we_html_button::create_button("image:btn_fs_back", "javascript:if(rootDirButsState){top.goBackDir();}", true, 0, 0, "", "", $this->dir == intval($this->rootDirID), false) . '</td>' .
				$this->printHeaderTableExtraCols() :
				''
			) . '
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
	</tr>' .
			$this->printHeaderTableSpaceRow() . '
</table>';
	}

	protected function printHeaderOptions(){
		$pid = $this->dir;
		$out = '';
		$c = 0;
		$z = 0;
		while($pid != 0){
			++$c;
			$data = getHash('SELECT ID,Text,ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($pid), $this->db);
			if($data){
				$out = '<option value="' . $data['ID'] . '"' . (($z == 0) ? ' selected="selected"' : '') . '>' . $data['Text'] . '</option>' . $out;
				$z++;
			}
			$pid = $data['ParentID'];
			if($c > 500){
				$pid = 0;
			}
			if($this->rootDirID && $data['ID'] == $this->rootDirID){
				$pid = 0;
			}
		}
		return ($this->rootDirID ? '' : '<option value="0">/</option>') . $out;
	}

	protected function printHeaderTableExtraCols(){
		return '<td width="10">' . we_html_tools::getPixel(10, 10) . '</td><td width="40">' .
			we_html_button::create_button("image:btn_new_dir", "javascript:top.drawNewFolder();", true, 0, 0, '', '', !$this->userCanMakeNewDir(), false) .
			'</td>';
	}

	protected function printHeaderTableSpaceRow(){
		return '<tr><td colspan="11">' . we_html_tools::getPixel(5, 10) . '</td></tr>';
	}

	protected function printFramesetJSDoClickFn(){
		return we_html_element::jsElement('
function showPreview(id) {
	if(top.fspreview) {
		top.fspreview.location.replace(top.queryString(' . self::PREVIEW . ',id));
	}
}

function doClick(id,ct){
	if(top.fspreview.document.body){
		top.fspreview.document.body.innerHTML = "";
	}
	if(ct==1){
		if(wasdblclick){
			setDir(id);
			setTimeout("wasdblclick=0;",400);
		}
	}else{
		if(top.currentID == id && (!fsbody.ctrlpressed)){' .
				($this->userCanRenameFolder ? 'top.RenameFolder(id);' : 'selectFile(id);') . '

		}else{' .
				($this->multiple ? '
				if(fsbody.shiftpressed){
					var oldid = currentID;
					var currendPos = getPositionByID(id);
					var firstSelected = getFirstSelected();

					if(currendPos > firstSelected){
						selectFilesFrom(firstSelected,currendPos);
					}else if(currendPos < firstSelected){
						selectFilesFrom(currendPos,firstSelected);
					}else{
						selectFile(id);
					}
					currentID = oldid;

				}else if(!fsbody.ctrlpressed){
					selectFile(id);
				}else{
					if (isFileSelected(id)) {
						unselectFile(id);
					}else{' : '') . '

					selectFile(id);' .
				($this->multiple ? '
					}
				}' : '') . '
		}
	}
	if(fsbody.ctrlpressed){
		fsbody.ctrlpressed = 0;
	}
	if(fsbody.shiftpressed){
		fsbody.shiftpressed = 0;
	}
}');
	}

	protected function printFramesetJSsetDir(){
		return we_html_element::jsElement('
function setDir(id){
	showPreview(id);
	if(top.fspreview.document.body){
		top.fspreview.document.body.innerHTML = "";
	}
	top.fscmd.location.replace(top.queryString(' . we_selector_multiple::SETDIR . ',id));
	e = getEntry(id);
	fspath.document.body.innerHTML = e.path;
}');
	}

	function printSetDirHTML(){
		echo '<script type="text/javascript"><!--
top.clearEntries();' .
		$this->printCmdAddEntriesHTML() .
		$this->printCMDWriteAndFillSelectorHTML() .
		'top.fsheader.' . (intval($this->dir) == intval($this->rootDirID) ? 'disable' : 'enable') . 'RootDirButs();';
		if(in_workspace(intval($this->dir), get_ws($this->table, false, true), $this->table, $this->db)){
			if($this->id == 0){
				$this->path = '/';
			}
			echo '
top.unselectAllFiles();
top.currentPath = "' . $this->path . '";
top.currentID = "' . $this->id . '";
top.fsfooter.document.we_form.fname.value = "' . (($this->id == 0) ? '/' : $this->values["Text"]) . '";';
		}
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
		echo '
top.currentDir = "' . $this->dir . '";
top.parentID = "' . $this->values["ParentID"] . '";
//-->
</script>';
	}

	function printFramesetSelectFileHTML(){
		return we_html_element::jsElement('
function selectFile(id){
	if(id){
		showPreview(id);
		e = getEntry(id);
		if( top.fsfooter.document.we_form.fname.value != e.text &&
			top.fsfooter.document.we_form.fname.value.indexOf(e.text+",") == -1 &&
			top.fsfooter.document.we_form.fname.value.indexOf(","+e.text+",") == -1 &&
			top.fsfooter.document.we_form.fname.value.indexOf(","+e.text+",") == -1 ){

			top.fsfooter.document.we_form.fname.value =  top.fsfooter.document.we_form.fname.value ?
				(top.fsfooter.document.we_form.fname.value + "," + e.text) :
				e.text;
		}
		if(top.fsbody.document.getElementById("line_"+id)) top.fsbody.document.getElementById("line_"+id).style.backgroundColor="#DFE9F5";
		currentPath = e.path;
		currentID = id;

		we_editDirID = 0;
	}else{
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
		we_editDirID = 0;
	}
}');
	}

	function printNewFolderHTML(){
		echo '<script type="text/javascript"><!--
top.clearEntries();
top.makeNewFolder = 1;' .
		$this->printCmdAddEntriesHTML() .
		$this->printCMDWriteAndFillSelectorHTML() . '
top.makeNewFolder = 0;
//-->
</script>';
	}

	function printCreateFolderHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop() .
		'<script type="text/javascript"><!--
top.clearEntries();';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if(!$txt){
			echo we_message_reporting::getShowMessageCall(g_l('weEditor', '[folder][filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
			//}elseif(strpos($txt,".")!==false){ entfernt fuer #4333
			//print we_message_reporting::getShowMessageCall(g_l('weEditor',"[folder][we_filename_notAllowed]"), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(substr($txt, -1) === '.'){ // neue Version f�r 4333 testet auf "." am ende, analog zu i_filenameNotAllowed in we_root
			echo we_message_reporting::getShowMessageCall(g_l('weEditor', '[folder][we_filename_notAllowed]'), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(preg_match('-[<>?":|\\/*]-', $txt)){ // Test auf andere verbotene Zeichen
			echo we_message_reporting::getShowMessageCall(g_l('weEditor', '[folder][we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(we_base_request::_(we_base_request::INT, 'id', 0) == 0 && strtolower($txt) === 'webedition'){
			echo we_message_reporting::getShowMessageCall(g_l('weEditor', '[folder][we_filename_notAllowed]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = (defined('OBJECT_FILES_TABLE') && $this->table == OBJECT_FILES_TABLE ? //4076
					new we_class_folder() :
					new we_folder());

			$folder->we_new();
			$folder->setParentID($this->dir);
			$folder->Table = $this->table;
			$folder->Text = $txt;
			$folder->CreationDate = time();
			$folder->ModDate = time();
			$folder->Filename = $txt;
			$folder->Published = time();
			$folder->Path = $folder->getPath();
			$folder->CreatorID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : '';
			$folder->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : '';
			$this->db->query('SELECT ID FROM ' . $this->table . ' WHERE Path="' . $folder->Path . '"');
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
if(top.opener.top.makeNewEntry){
	ref = top.opener.top;
}else if(top.opener.top.opener){
	ref = top.opener.top.opener.top;
}
ref.makeNewEntry("' . $folder->Icon . '",' . $folder->ID . ',"' . $folder->ParentID . '","' . $txt . '",1,"' . $folder->ContentType . '","' . $this->table . '");' .
					($this->canSelectDir ? '
top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.fsfooter.document.we_form.fname.value = "' . $folder->Text . '";' : '');
				}
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

	protected function getFrameset(){
		return '<frameset rows="67,*,65,20,0" border="0">
	<frame src="' . $this->getFsQueryString(we_selector_file::HEADER) . '" name="fsheader" noresize scrolling="no">
	<frameset cols="605,*" border="1">
		<frame src="' . $this->getFsQueryString(we_selector_file::BODY) . '" name="fsbody" noresize scrolling="auto">
		<frame src="' . $this->getFsQueryString(self::PREVIEW) . '" name="fspreview" noresize scrolling="no"' . ((!we_base_browserDetect::isGecko()) ? ' style="border-left:1px solid black"' : '') . '>
	</frameset>
	<frame src="' . $this->getFsQueryString(we_selector_file::FOOTER) . '"  name="fsfooter" noresize scrolling="no">
	<frame src="' . HTML_DIR . 'gray2.html"  name="fspath" noresize scrolling="no">
    <frame src="about:blank"  name="fscmd" noresize scrolling="no">
</frameset>
<body>
</body>
</html>';
	}

	function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
var makeNewFolder=0;
var we_editDirID="";
var old=0;');
	}

	function printRenameFolderHTML(){
		if(we_users_util::userIsOwnerCreatorOfParentDir($this->we_editDirID, $this->table) && in_workspace($this->we_editDirID, get_ws($this->table, false, true), $this->table, $this->db)){
			echo '<script type="text/javascript"><!--
top.clearEntries();
top.we_editDirID=' . $this->we_editDirID . ';' .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.we_editDirID = "";
//-->
</script>';
		}
	}

	function printDoRenameFolderHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop() .
		'<script type="text/javascript"><!--
top.clearEntries();';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if($txt === ''){
			echo we_message_reporting::getShowMessageCall(g_l('weEditor', '[folder][filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$folder = (defined('OBJECT_FILES_TABLE') && $this->table == OBJECT_FILES_TABLE ? //4076
					new we_class_folder() :
					new we_folder());

			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->ModDate = time();
			$folder->Filename = $txt;
			$folder->Published = time();
			$folder->Path = $folder->getPath();
			$folder->ModifierID = isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : '';
			$this->db->query('SELECT ID,Text FROM ' . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($folder->Path) . "' AND ID != " . intval($this->we_editDirID));
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('weEditor', '[folder][response_path_exists]'), $folder->Path);
				echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				if(preg_match('-[<>?":|\\/*]-', $folder->Filename)){
					$we_responseText = sprintf(g_l('weEditor', '[folder][we_filename_notValid]'), $folder->Path);
					echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else if(in_workspace($this->we_editDirID, get_ws($this->table, false, true), $this->table, $this->db)){
					if(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editDirID), 'Text', $this->db) != $txt){
						$folder->we_save();
						echo 'var ref;
if(top.opener.top.makeNewEntry) ref = top.opener.top;
else if(top.opener.top.opener) ref = top.opener.top.opener.top;
ref.updateEntry(' . $folder->ID . ',"' . $txt . '","' . $folder->ParentID . '","' . $this->table . '");' .
						($this->canSelectDir ? '
top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.fsfooter.document.we_form.fname.value = "' . $folder->Text . '";
' : '');
					}
				}
			}
		}


		print
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() . '
top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
	}

	function printPreviewHTML(){
		if(!$this->id){
			return;
		}
		$data = getHash('SELECT * FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->id), $this->db);
		if($data){
			$result = array(
				'Text' => $data['Text'],
				'Path' => $data['Path'],
				'ContentType' => isset($data['ContentType']) ? $data['ContentType'] : '',
				'Type' => isset($data['Type']) ? $data['Type'] : '',
				'CreationDate' => isset($data['CreationDate']) ? $data['CreationDate'] : '',
				'ModDate' => isset($data['ModDate']) ? $data['ModDate'] : '',
				'Filename' => isset($data['Filename']) ? $data['Filename'] : '',
				'Extension' => isset($data['Extension']) ? $data['Extension'] : '',
				'MasterTemplateID' => isset($data['MasterTemplateID']) ? $data['MasterTemplateID'] : '',
				'IncludedTemplates' => isset($data['IncludedTemplates']) ? $data['IncludedTemplates'] : '',
				'ClassName' => isset($data['ClassName']) ? $data['ClassName'] : '',
				'Templates' => isset($data['Templates']) ? $data['Templates'] : '',
			);
		}
		$path = $data ? $data['Path'] : '';

		$out = we_html_tools::getHtmlTop() .
			STYLESHEET . we_html_element::cssElement('
	body {
		margin:0px;
		padding:0px;
		background-color:#FFFFFF;
	}
	td {
		font-size: 10px;
		padding: 3px 6px;
		vertical-align:top;
	}
	td.image {
		vertical-align:middle;
		padding: 0px;
	}
	td.info {
		padding: 0px;
	}
	.headline {
		padding:3px 6px;
		background-color:#BABBBA;
		font-weight:bold;
		border-top:0px solid black;
		border-bottom:0px solid black;
	}
	.odd {
		padding:3px 6px;
		background-color:#FFFFFF;
	}
	.even {
		padding:3px 6px;
		background-color:#F2F2F1;
	}
') . we_html_element::jsElement('
	function setInfoSize() {
		infoSize = document.body.clientHeight;
		if(infoElem=document.getElementById("info")) {
			infoElem.style.height = document.body.clientHeight - (prieviewpic = document.getElementById("previewpic") ? 160 : 0 )+"px";
		}
	}
	function openToEdit(tab,id,contentType){
		if(top.opener && top.opener.top.weEditorFrameController) {
			top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		} else if(top.opener.top.opener && top.opener.top.opener.top.weEditorFrameController) {
			top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		} else if(top.opener.top.opener.top.opener && top.opener.top.opener.top.opener.top.weEditorFrameController) {
			top.opener.top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		}
	}
	var weCountWriteBC = 0;
	function weWriteBreadCrumb(BreadCrumb){
		if(top.fspath && top.fspath.document && top.fspath.document.body){
			top.fspath.document.body.innerHTML = BreadCrumb;
		}else if(weCountWriteBC<10){
			setTimeout(\'weWriteBreadCrumb("' . $path . '")\',100);
		}
		weCountWriteBC++;
	}') .
			'</head>
<body class="defaultfont" onresize="setInfoSize()" onload="setTimeout(\'setInfoSize()\',50);weWriteBreadCrumb(\'' . $path . '\');">';
		if(isset($result['ContentType']) && $result['ContentType']){
			if($this->table == FILE_TABLE){
				if($result['ContentType'] == we_base_ContentTypes::FOLDER){
					$query = $this->db->query('SELECT ID,Text,IsFolder FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->id));
					$folderFolders = array();
					$folderFiles = array();
					while($this->db->next_record()){
						if($this->db->f('IsFolder')){
							$folderFolders[$this->db->f('ID')] = $this->db->f('Text');
						} else {
							$folderFiles[$this->db->f('ID')] = $this->db->f('Text');
						}
					}
				} else {
					$query = $this->db->query('SELECT l.Name,c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DID=' . intval($this->id) . ' AND l.DocumentTable!="tblTemplates"');
					$metainfos = $this->db->getAllFirst(false);
				}
			}

			$fs = file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path']) ? filesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']) : 0;

			$filesize = we_base_file::getHumanFileSize($fs);
			$next = 0;
			$previewDefauts = "
<tr><td class='info' width='100%'>
	<div style='overflow:auto; height:100%' id='info'>
	<table cellpadding='0' cellspacing='0' width='100%'>
		<tr><td colspan='2' class='headline'>" . g_l('weClass', '[tab_properties]') . "</td></tr>
		<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td title=\"" . $result['Path'] . "\" width='10'>" . g_l('fileselector', '[name]') . ": </td><td>
			<div style='margin-right:14px'>" . $result['Text'] . "</div></td></tr>
		<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td width='10'>ID: </td><td>
			<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'><div style='float:left; vertical-align:baseline; margin-right:4px;'><img src='" . TREE_ICON_DIR . "bearbeiten.gif' border='0' vspace='0' hspace='0'></div></a>
			<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'><div>" . $this->id . "</div></a>
		</td></tr>";
			if($result['CreationDate']){
				$previewDefauts .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[created]') . ": </td><td>" . date(g_l('date', '[format][default]'), $result['CreationDate']) . "</td></tr>";
			}
			if($result['ModDate']){
				$previewDefauts .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[modified]') . ": </td><td>" . date(g_l('date', '[format][default]'), $result['ModDate']) . "</td></tr>";
			}
			$previewDefauts .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[type]') . ": </td><td>" . (g_l('contentTypes', '[' . $result['ContentType'] . ']', true) !== false ? g_l('contentTypes', '[' . $result['ContentType'] . ']') : $result['ContentType']) . "</td></tr>";

			$out .= '<table cellpadding="0" cellspacing="0" height="100%" width="100%">';
			switch($result['ContentType']){
				case we_base_ContentTypes::IMAGE:
					if(file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path'])){
						$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']);
						if($imagesize[0] > 150 || $imagesize[1] > 150){
							$extension = substr($result['Extension'], 1);
							$thumbpath = WE_THUMB_PREVIEW_DIR . $this->id . '.' . $extension;
							$created = filemtime($_SERVER['DOCUMENT_ROOT'] . $result['Path']);
							if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $thumbpath) || ($created > filemtime($_SERVER['DOCUMENT_ROOT'] . $thumbpath))){
								//create thumb
								we_base_imageEdit::edit_image($_SERVER['DOCUMENT_ROOT'] . $result['Path'], $extension, $_SERVER['DOCUMENT_ROOT'] . $thumbpath, null, 150, 200);
							}
						} else {
							$thumbpath = $result['Path'];
						}

						$out .= "<tr><td valign='middle' class='image' height='160' align='center' bgcolor='#EDEEED'><a href='" . $result['Path'] . "' target='_blank' align='center'><img src='" . $thumbpath . "' border='0' id='previewpic'></a></td></tr>" .
							$previewDefauts . "
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[width]') . " x " . g_l('weClass', '[height]') . ": </td><td>" . $imagesize[0] . " x " . $imagesize[1] . " px </td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[filesize]') . ": </td><td>" . $filesize . "</td></tr>";

						$next = 0;
						$out .= "
<tr><td colspan='2' class='headline'>" . g_l('weClass', '[metainfo]') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Title]') . ": </td><td>" . (isset($metainfos['Title']) ? $metainfos['Title'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Description]') . ": </td><td>" . (isset($metainfos['Description']) ? $metainfos['Description'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Keywords]') . ": </td><td>" . (isset($metainfos['Keywords']) ? $metainfos['Keywords'] : '') . "</td></tr>";

						$next = 0;
						$out .= "
<tr><td colspan='2' class='headline'>" . g_l('weClass', '[attribs]') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Title]') . ": </td><td>" . (isset($metainfos['Title']) ? $metainfos['Title'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[name]') . ": </td><td>" . (isset($metainfos['name']) ? $metainfos['name'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[alt]') . ": </td><td>" . (isset($metainfos['alt']) ? $metainfos['alt'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[width]') . " x " . g_l('weClass', '[height]') . ": </td><td>" . (isset($metainfos['width']) ? $metainfos['width'] : '') . " x " . (isset($metainfos['height']) ? $metainfos['height'] : '') . " px </td></tr>";
					}
					break;
				case "folder":
					$out .= $previewDefauts;
					if(isset($folderFolders) && is_array($folderFolders) && count($folderFolders)){
						$next = 0;
						$out .= "<tr><td colspan='2' class='headline'>" . g_l('fileselector', '[folders]') . "</td></tr>";
						foreach($folderFolders as $fId => $fxVal){
							$out .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . $fId . ": </td><td>" . $fxVal . "</td></tr>";
						}
					}
					if(isset($folderFiles) && is_array($folderFiles) && count($folderFiles)){
						$next = 0;
						$out .= "<tr><td colspan='2' class='headline'>" . g_l('fileselector', '[files]') . "</td></tr>";
						foreach($folderFiles as $fId => $fxVal){
							$out .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . $fId . ": </td><td>" . $fxVal . "</td></tr>";
						}
					}
					break;
				case we_base_ContentTypes::TEMPLATE:
					$out .= $previewDefauts;
					if(isset($result['MasterTemplateID']) && !empty($result['MasterTemplateID'])){
						$mastertemppath = f("SELECT Text, Path FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($result['MasterTemplateID']), "Path", $this->db);
						$next = 0;
						$out .= "
<tr><td colspan='2' class='headline'>" . g_l('weClass', '[master_template]') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>ID:</td><td>" . $result['MasterTemplateID'] . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[path]') . ":</td><td>" . $mastertemppath . "</td></tr>";
					}
					break;
				case we_base_ContentTypes::WEDOCUMENT:
					$out .= $previewDefauts . "
<tr><td colspan='2' class='headline'>" . g_l('weClass', '[metainfo]') . "</td></tr>";
					$next = 0;
					$out .= "
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Title]') . ":</td><td>" . (isset($metainfos['Title']) ? $metainfos['Title'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Charset]') . ":</td><td>" . (isset($metainfos['Charset']) ? $metainfos['Charset'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Keywords]') . ":</td><td>" . (isset($metainfos['Keywords']) ? $metainfos['Keywords'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Description]') . ":</td><td>" . (isset($metainfos['Description']) ? $metainfos['Description'] : '') . "</td></tr>";
					break;
				case we_base_ContentTypes::HTML:
				case we_base_ContentTypes::CSS:
				case we_base_ContentTypes::JS:
				case we_base_ContentTypes::APPLICATION:
					$out .= $previewDefauts;
					$out .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[filesize]') . ":</td><td>" . $filesize . "</td></tr>";
					break;
				case "object":
				case "objectFile":
				default:
					$out .= $previewDefauts;
					break;
			}
			$out .= '</table></div></td></tr></table>';
		}
		$out .= '</body></html>';
		echo $out;
	}

}
