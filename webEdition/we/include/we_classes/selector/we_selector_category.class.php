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
class we_selector_category extends we_selector_multiple{

	const CREATE_CAT = 7;
	const DO_RENAME_CAT = 9;
	const DO_RENAME_ENTRY = 10;
	const PROPERTIES = 12;
	const CHANGE_CAT = 13;

	private $we_editCatID = '';
	private $EntryText = '';
	private $noChoose = false;

	function __construct($id, $table = FILE_TABLE, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $we_editCatID = '', $EntryText = '', $rootDirID = 0, $noChoose = false){
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $rootDirID);
		$this->title = g_l('fileselector', '[catSelector][title]');

		$this->we_editCatID = $we_editCatID;
		$this->EntryText = $EntryText;
		$this->noChoose = $noChoose;
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
			case self::CREATEFOLDER:
				$this->printCreateEntryHTML(1);
				break;
			case self::DO_RENAME_ENTRY:
				$this->printDoRenameEntryHTML();
				break;
			case self::CREATE_CAT:
				$this->printCreateEntryHTML(0);
				break;
			case self::DO_RENAME_CAT:
				$this->printDoRenameEntryHTML();
				break;
			case self::DEL:
				$this->printDoDelEntryHTML();
				break;
			case self::PROPERTIES:
				$this->printPropertiesHTML();
				break;
			case self::CHANGE_CAT:
				$this->printchangeCatHTML();
				break;
			case self::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

	protected function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . "?what=$what&table=" . $this->table . "&id=" . $this->id . "&order=" . $this->order . "&noChoose=" . $this->noChoose;
	}

	protected function printHeaderTable(){
		return '
<table border="0" cellpadding="0" cellspacing="0" width="100%">' .
				$this->printHeaderTableSpaceRow() . '
	<tr valign="middle">
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="70" class="defaultfont"><b>' . g_l('fileselector', '[lookin]') . '</b></td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td><select name="lookin" class="weSelect" size="1" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%">' . $this->printHeaderOptions() . '</select></td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="40">' . we_html_button::create_button("root_dir", "javascript:top.setRootDir();", true, 0, 0, '', '', $this->dir == intval($this->rootDirID), false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="40">' . we_html_button::create_button("image:btn_fs_back", "javascript:top.goBackDir();", true, 0, 0, '', '', $this->dir == intval($this->rootDirID), false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>' .
				($this->userCanEditCat() ?
						'<td width="40">' . we_html_button::create_button("image:btn_new_dir", 'javascript:top.drawNewFolder();', true, 0, 0, '', '', false, false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="38">' . we_html_button::create_button("image:btn_add_cat", 'javascript:top.drawNewCat();', true, 0, 0, '', '', false, false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>' : '') .
				($this->userCanEditCat() ?
						'<td width="27">' . we_html_button::create_button("image:btn_function_trash", 'javascript:if(changeCatState==1){top.deleteEntry();}', true, 27, 22, '', '', false, false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>' : '') .
				'</tr>' .
				$this->printHeaderTableSpaceRow() . '
</table>';
	}

	protected function printHeaderTableSpaceRow(){
		return '<tr><td colspan="15">' . we_html_tools::getPixel(5, 10) . '</td></tr>';
	}

	function userCanEditCat(){
		return permissionhandler::hasPerm('EDIT_KATEGORIE');
	}

	function userCanChangeCat(){
		return ($this->userCanEditCat() && $this->id > 0);
	}

	protected function printHeaderJSDef(){
		return 'var changeCatState=' . ($this->userCanChangeCat() ? 1 : 0) . ';';
	}

	protected function printHeaderJS(){
		return we_html_button::create_state_changer(false) . '

function disableRootDirButs(){
	root_dir_enabled = switch_button_state("root_dir", "root_dir_enabled", "disabled");
	btn_fs_back_enabled = switch_button_state("btn_fs_back", "back_enabled", "disabled", "image");
	rootDirButsState = 0;
}
function enableRootDirButs(){
	root_dir_enabled = switch_button_state("root_dir", "root_dir_enabled", "enabled");
	btn_fs_back_enabled = switch_button_state("btn_fs_back", "back_enabled", "enabled", "image");
	rootDirButsState = 1;
}

function disableNewBut(){
	btn_new_dir_enabled = switch_button_state("btn_new_dir", "new_directory_enabled", "disabled", "image");
	btn_add_cat_enabled = switch_button_state("btn_add_cat", "newCategorie_enabled", "disabled", "image");
}
function enableNewBut(){' .
				($this->userCanEditCat() ? '
	btn_new_dir_enabled = switch_button_state("btn_new_dir", "new_directory_enabled", "enabled", "image");
	btn_add_cat_enabled = switch_button_state("btn_add_cat", "newCategorie_enabled", "enabled", "image");' : '') . '
}
function disableDelBut(){
	btn_function_trash_enabled = switch_button_state("btn_function_trash", "btn_function_trash_enabled", "disabled", "image");
	changeCatState = 0;
}
function enableDelBut(){
' . ($this->userCanEditCat() ? '
	btn_function_trash_enabled = switch_button_state("btn_function_trash", "btn_function_trash_enabled", "enabled", "image");
	changeCatState = 1;
' : '') . '
}';
	}

	function getExitClose(){
		return we_html_element::jsElement('	function exit_close(){' .
						(!$this->noChoose ? '		if(hot){
			opener.setScrollTo();opener.top.we_cmd("reload_editpage");
		}' : '') .
						'		self.close();
	}');
	}

	protected function printFramesetJSFunctioWriteBody(){
		ob_start();
		?><script type="text/javascript"><!--
					function writeBody(d) {
				d.open();
		<?php
		echo self::makeWriteDoc(we_html_tools::getHtmlTop('', '', '4Trans', true) . STYLESHEET_SCRIPT . we_html_element::jsElement('
var ctrlpressed=false;
var shiftpressed=false;
var inputklick=false;
var wasdblclick=false;
var tout=null;
document.onclick = weonclick;
function weonclick(e){
#	if(makeNewFolder || makeNewCat || we_editCatID){
if(!inputklick){' . (we_base_browserDetect::isIE() && $GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? '
document.we_form.we_EntryText.value=escape(document.we_form.we_EntryText_tmp.value);document.we_form.submit();' : '
document.we_form.we_EntryText.value=document.we_form.we_EntryText_tmp.value;document.we_form.submit();') . '
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
}
if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}
#	}
}') . '</head>
<body bgcolor="white" LINK="#000000" ALINK="#000000" VLINK="#000000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"#\'+((makeNewFolder || makeNewCat || we_editCatID) ? #\' onload="document.we_form.we_EntryText_tmp.focus();document.we_form.we_EntryText_tmp.select();"#\' : "")+#\'>
');
		?>

		<?php if(we_base_browserDetect::isIE() && substr($GLOBALS["WE_LANGUAGE"], -5) !== "UTF-8"){ ?>
					d.writeln('<form name="we_form" target="fscmd" action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" onsubmit="document.we_form.we_EntryText.value=escape(document.we_form.we_EntryText_tmp.value);return true;">');

		<?php } else { ?>
					d.writeln('<form name="we_form" target="fscmd" action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" onsubmit="document.we_form.we_EntryText.value=document.we_form.we_EntryText_tmp.value;return true;">');

		<?php } ?>
				if (top.we_editCatID) {
					d.writeln('<input type="hidden" name="what" value="<?php echo self::DO_RENAME_ENTRY; ?>" />');
					d.writeln('<input type="hidden" name="we_editCatID" value="' + top.we_editCatID + '" />');
				} else {
					if (makeNewFolder) {
						d.writeln('<input type="hidden" name="what" value="<?php echo self::CREATEFOLDER; ?>" />');
					} else {
						d.writeln('<input type="hidden" name="what" value="<?php echo self::CREATE_CAT; ?>" />');
					}
				}
				d.writeln('<input type="hidden" name="order" value="' + top.order + '" />');
				d.writeln('<input type="hidden" name="rootDirID" value="<?php echo $this->rootDirID; ?>" />');
				d.writeln('<input type="hidden" name="table" value="<?php echo $this->table; ?>" />');
				d.writeln('<input type="hidden" name="id" value="' + top.currentDir + '" />');
				d.writeln('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
				if (makeNewFolder) {
					d.writeln('<tr style="background-color:#DFE9F5;">');
					d.writeln('<td align="center"><img src="<?php echo TREE_ICON_DIR . we_base_ContentTypes::FOLDER_ICON; ?>" width="16" height="18" border="0" /></td>');
					d.writeln('<td><input type="hidden" name="we_EntryText" value="<?php echo g_l('fileselector', '[new_folder_name]'); ?>" /><input onMouseDown="self.inputklick=true" name="we_EntryText_tmp" type="text" value="<?php echo g_l('fileselector', '[new_folder_name]') ?>" class="wetextinput" style="width:100%" /></td>');
					d.writeln('</tr>');
				} else if (makeNewCat) {
					d.writeln('<tr style="background-color:#DFE9F5;">');
					d.writeln('<td align="center"><img src="<?php echo TREE_ICON_DIR ?>cat.gif" width="16" height="18" border="0" /></td>');
					d.writeln('<td><input type="hidden" name="we_EntryText" value="<?php echo g_l('fileselector', '[new_cat_name]'); ?>" /><input onMouseDown="self.inputklick=true" name="we_EntryText_tmp" type="text" value="<?php echo g_l('fileselector', '[new_cat_name]') ?>" class="wetextinput" style="width:100%" /></td>');
					d.writeln('</tr>');
				}
				for (i = 0; i < entries.length; i++) {
					var onclick = ' onclick="weonclick(<?php echo (we_base_browserDetect::isIE() ? "this" : "event") ?>);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true;"';
					var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
					d.writeln('<tr id="line_' + entries[i].ID + '" style="cursor:pointer;' + ((we_editCatID != entries[i].ID) ? '' : '') + '"' + ((we_editCatID || makeNewFolder || makeNewCat) ? '' : onclick) + (entries[i].isFolder ? ondblclick : '') + ' >');
					d.writeln('<td class="selector" width="25" align="center">');
					if (we_editCatID == entries[i].ID) {
						d.writeln('<img src="<?php echo TREE_ICON_DIR; ?>' + entries[i].icon + '" width="16" height="18" border="0" />');
						d.writeln('</td>');
						d.writeln('<td class="selector">');
						d.writeln('<input type="hidden" name="we_EntryText" value="' + entries[i].text + '" /><input onMouseDown="self.inputklick=true" name="we_EntryText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />');
					} else {
						d.writeln('<img src="<?php echo TREE_ICON_DIR; ?>' + entries[i].icon + '" width="16" height="18" border="0" />');
						d.writeln('</td>');
						d.writeln('<td class="selector"' + (we_editCatID ? '' : '') + ' title="' + entries[i].text + '">');
						d.writeln(cutText(entries[i].text, 80));
					}
					d.writeln('</td>');
					d.writeln('</tr><tr><td colspan="2"><?php echo we_html_tools::getPixel(2, 1); ?></td></tr>');
				}
				d.writeln('<tr>');
				d.writeln('<td width="25"><?php echo we_html_tools::getPixel(25, 2) ?></td>');
				d.writeln('<td><?php echo we_html_tools::getPixel(150, 2) ?></td>');
				d.writeln('</tr>');
				d.writeln('</table></form>');
				d.writeln('</body>');
				d.close();
			}
			//-->
		</script>
		<?php
		return ob_get_clean();
	}

	protected function printFramesetJSFunctionQueryString(){
		return we_html_element::jsElement('
		function queryString(what,id,o,we_editCatID){
		if(!o) o=top.order;
		if(!we_editCatID) we_editCatID="";
		return \'' . $_SERVER["SCRIPT_NAME"] . '?what=\'+what+\'&rootDirID=' . $this->rootDirID . '&table=' . $this->table . '&id=\'+id+(o ? ("&order="+o) : "")+(we_editCatID ? ("&we_editCatID="+we_editCatID) : "");
		}');
	}

	protected function printFramesetJSFunctions(){
		return parent::printFramesetJSFunctions() . we_html_element::jsElement('
function drawNewFolder(){
	unselectAllFiles();
	top.makeNewFolder=true;
	top.writeBody(top.fsbody.document);
	top.makeNewFolder=false;
}
function drawNewCat(){
	unselectAllFiles();
	top.makeNewCat=true;
	top.writeBody(top.fsbody.document);
	top.makeNewCat=false;
}
function deleteEntry(){
	if(confirm(\'' . g_l('fileselector', '[deleteQuestion]') . '\')){
		var todel = "";
		for	(var i=0;i < entries.length; i++){
			if(isFileSelected(entries[i].ID)){
				todel += entries[i].ID + ",";
			}
		}
		if (todel) {
			todel = "," + todel;
		}
		top.fscmd.location.replace(top.queryString(' . self::DEL . ',top.currentID)+"&todel="+encodeURI(todel));
		if(top.fsvalues) top.fsvalues.location.replace(top.queryString(' . self::PROPERTIES . ',0));
		top.fsheader.disableDelBut();
	}

}
function RenameEntry(id){
	top.we_editCatID=id;
	top.writeBody(top.fsbody.document);
	selectFile(id);
	top.we_editCatID=0;
}');
	}

	function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
var makeNewFolder=0;
var hot=0; // this is hot for category edit!!
var makeNewCat=false;
var we_editCatID="";
var old=0;');
	}

	function printCreateEntryHTML($what = 0){
		echo we_html_tools::getHtmlTop();
		$js = 'top.clearEntries();';
		$this->EntryText = rawurldecode($this->EntryText);
		$txt = $this->EntryText;
		if(empty($txt)){
			$js.=($what == 1 ?
							we_message_reporting::getShowMessageCall(g_l('weEditor', '[folder][filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR) :
							we_message_reporting::getShowMessageCall(g_l('weEditor', '[category][filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
		} else if(strpos($txt, ',') !== false){
			$js.=we_message_reporting::getShowMessageCall(g_l('weEditor', '[category][name_komma]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$txt = trim($txt);
			$parentPath = (!intval($this->dir)) ? '' : f('SELECT Path FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), 'Path', $this->db);
			$Path = $parentPath . '/' . $txt;

			if(f('SELECT 1 FROM ' . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($Path) . "' LIMIT 1", '', $this->db) === '1'){
				$js.=we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', ($what == 1 ? '[folder][response_path_exists]' : '[category][response_path_exists]')), $Path), we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				if(preg_match('|[\\\'"<>/]|', $txt)){
					$js.= we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][we_filename_notValid]'), $Path), we_message_reporting::WE_MESSAGE_ERROR);
				} else {
					$this->db->query('INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter(array(
								'Category' => $txt,
								'ParentID' => intval($this->dir),
								'Text' => $txt,
								'Path' => $Path,
								'IsFolder' => intval($what),
								'Icon' => (($what == 1) ? we_base_ContentTypes::FOLDER_ICON : 'cat.gif'),
					)));
					$folderID = $this->db->getInsertId();
					$js.='top.currentPath = "' . $Path . '";
top.currentID = "' . $folderID . '";
top.hot = 1; // this is hot for category edit!!

if(top.currentID){
	top.fsheader.enableDelBut();
	top.showPref(top.currentID);
}';
				}
			}
		}

		echo we_html_element::jsElement(
				$js .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				'top.makeNewFolder = 0;
top.selectFile(top.currentID);') .
		'</head><body></body></html>';
	}

	function printHeaderHeadlines(){
		echo '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="35%" class="selector" style="padding-left:10px;"><b><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[catname]') . '</a></b></td>
		<td width="65%" class="selector" style="padding-left:10px;"><b>' . g_l('button', '[properties][value]') . '</b></td>
	</tr>
	<tr>
		<td width="35%"></td>
		<td width="65%"></td>
	</tr>
</table>';
	}

	function printDoRenameEntryHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop();
		$what = f('SELECT IsFolder FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($this->we_editCatID), '', $this->db);
		$js = 'top.clearEntries();';
		$this->EntryText = rawurldecode($this->EntryText);
		$txt = $this->EntryText;
		if(empty($txt)){
			$js.=we_message_reporting::getShowMessageCall(g_l('weEditor', ($what == 1 ? '[folder]' : '[category]') . '[filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else if(strpos($txt, ',') !== false){
			$js.=we_message_reporting::getShowMessageCall(g_l('weEditor', '[category][name_komma]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$parentPath = (!intval($this->dir)) ? '' : f('SELECT Path FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), 'Path', $this->db);
			$Path = $parentPath . '/' . $txt;
			if(f('SELECT 1 FROM ' . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($Path) . "' AND ID!=" . intval($this->we_editCatID) . ' LIMIT 1', '', $this->db)){
				$js.=we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', ($what == 1 ? '[folder]' : '[category]') . '[response_path_exists]'), $Path), we_message_reporting::WE_MESSAGE_ERROR);
			} elseif(preg_match('|[\'"<>/]|', $txt)){
				$js.=we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][we_filename_notValid]'), $Path), we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				if(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editCatID), 'Text', $this->db) != $txt){
					$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter(array(
								'Category' => $txt,
								'ParentID' => intval($this->dir),
								'Text' => $txt,
								'Path' => $Path,
							)) .
							' WHERE ID=' . intval($this->we_editCatID));
					if(f('SELECT IsFolder FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editCatID), '', $this->db)){
						$this->renameChildrenPath($this->we_editCatID);
					}
					$js.='top.currentPath = "' . $Path . '";
top.hot = 1; // this is hot for category edit!!
top.currentID = "' . $this->we_editCatID . '";
if(top.currentID){
	top.fsheader.enableDelBut();
	top.showPref(top.currentID);
}';
				}
			}
		}

		echo we_html_element::jsElement(
				$js .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				'top.fsfooter.document.we_form.fname.value = "";
top.selectFile(' . $this->we_editCatID . ');top.makeNewFolder = 0;') .
		'</head><body></body></html>';
	}

	protected function printFramesetJSDoClickFn(){
		return we_html_element::jsElement('
function doClick(id,ct){
	if(ct==1){
		if(wasdblclick){
			setDir(id);
			setTimeout("wasdblclick=0;",400);
		}else if(top.currentID == id){' .
						(permissionhandler::hasPerm("EDIT_KATEGORIE") ? '
				top.RenameEntry(id);' : '') . '
		}
	}else{
		if(top.currentID == id && (!fsbody.ctrlpressed)){' .
						(permissionhandler::hasPerm("EDIT_KATEGORIE") ? '
				top.RenameEntry(id);' : '') . '

		}else{
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
				hidePref(id);
			}else if(!fsbody.ctrlpressed){
				showPref(id);
				selectFile(id);
			}else{
				hidePref(id);
				if (isFileSelected(id)) {
					unselectFile(id);
				}else{
					selectFile(id);
				}
			}
		}
	}
	if(fsbody.ctrlpressed){
		fsbody.ctrlpressed = 0;
	}
	if(fsbody.shiftpressed){
		fsbody.shiftpressed = 0;
	}
}

function showPref(id) {
	if(self.fsvalues) self.fsvalues.location = "' . $this->getFsQueryString(self::PROPERTIES) . '&catid="+id;
}

function hidePref() {
	if(self.fsvalues) self.fsvalues.location = "' . $this->getFsQueryString(self::PROPERTIES) . '";
}');
	}

	protected function printCmdHTML(){
		echo we_html_element::jsElement('
top.clearEntries();' .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				(intval($this->dir) == 0 ? '
top.fsheader.disableRootDirButs();
top.fsheader.disableDelBut();' : '
top.fsheader.enableRootDirButs();
top.fsheader.enableDelBut();' ) . '
top.currentPath = "' . $this->path . '";
top.parentID = "' . $this->values["ParentID"] . '";');
	}

	function printFramesetSelectFileHTML(){
		return we_html_element::jsElement('
function unselectFile(id){
	e = getEntry(id);
	top.fsbody.document.getElementById("line_"+id).style.backgroundColor="white";

	var foo = top.fsfooter.document.we_form.fname.value.split(/,/);

	for (var i=0; i < foo.length; i++) {
		if (foo[i] == e.text) {
			foo[i] = "";
			break;
		}
	}
	var str = "";
	for (var i=0; i < foo.length; i++) {
		if(foo[i]){
			str += foo[i]+",";
		}
	}
	str = str.replace(/(.*),$/,"$1");
	top.fsfooter.document.we_form.fname.value = str;
}

function selectFilesFrom(from,to){
	unselectAllFiles();
	for	(var i=from;i <= to; i++){
		selectFile(entries[i].ID);
	}
}

function getFirstSelected(){
	for	(var i=0;i < entries.length; i++){
		if(top.fsbody.document.getElementById("line_"+entries[i].ID).style.backgroundColor!="white"){
			return i;
		}
	}
	return -1;
}

function getPositionByID(id){
	for	(var i=0;i < entries.length; i++){
		if(entries[i].ID == id){
			return i;
		}
	}
	return -1;
}
function isFileSelected(id){
	return (top.fsbody.document.getElementById("line_"+id).style.backgroundColor && (top.fsbody.document.getElementById("line_"+id).style.backgroundColor!="white"));
}

function unselectAllFiles(){
	for	(var i=0;i < entries.length; i++){
		top.fsbody.document.getElementById("line_"+entries[i].ID).style.backgroundColor="white";
	}
	top.fsfooter.document.we_form.fname.value = "";
	top.fsheader.disableDelBut()
}

function selectFile(id){
	if(id){
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
		if(id) top.fsheader.enableDelBut();
		we_editCatID = 0;
	}else{
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
		we_editCatID = 0;
	}
}');
	}

	protected function printFramesetJSsetDir(){
		return we_html_element::jsElement('
function setDir(id){
	e = getEntry(id);
	if(id==0) e.text="";
	currentID = id;
	currentDir = id;
	currentPath = e.path;
	top.fsfooter.document.we_form.fname.value = e.text;
	if(id) top.fsheader.enableDelBut();
	top.fscmd.location.replace(top.queryString(' . we_selector_file::CMD . ',id));
}');
	}

	function renameChildrenPath($id, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		$db->query('SELECT ID,IsFolder FROM ' . CATEGORY_TABLE . ' WHERE ParentID=' . intval($id));
		$updates = $db->getAllFirst(false);
		$path = f('SELECT Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($id));
		foreach($updates as $curId => $IsFolder){
			$db->query('UPDATE ' . CATEGORY_TABLE . ' SET Path=CONCAT("' . $path . '","/",Text) WHERE ID=' . $curId);
			if($IsFolder){
				$this->renameChildrenPath($curId, $db);
			}
		}
	}

	function CatInUse($id, $IsDir, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		if($IsDir){
			return $this->DirInUse($id, $db);
		}
		if(f('SELECT 1  FROM ' . FILE_TABLE . ' WHERE FIND_IN_SET(' . intval($id) . ',Category) OR FIND_IN_SET(' . intval($id) . ',temp_category) LIMIT 1', '', $db)){
			return true;
		}
		if(defined('OBJECT_TABLE') && f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE FIND_IN_SET(' . intval($id) . ',Category) LIMIT 1', '', $db)){
			return true;
		}

		return false;
	}

	function DirInUse($id, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		if($this->CatInUse($id, 0, $db)){
			return true;
		}

		$db->query('SELECT ID,IsFolder FROM ' . $db->escape($this->table) . ' WHERE ParentID=' . intval($id));
		while($db->next_record()){
			if($this->CatInUse($db->f("ID"), $db->f("IsFolder"))){
				return true;
			}
		}
		return false;
	}

	function printDoDelEntryHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop();

		if(($catsToDel = we_base_request::_(we_base_request::INTLISTA, 'todel',array()))){
			$finalDelete = array();
			$catlistNotDeleted = "";
			$changeToParent = false;
			foreach($catsToDel as $id){
				$IsDir = f('SELECT IsFolder FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->id), "", $this->db);
				if($this->CatInUse($id, $IsDir)){
					$catlistNotDeleted .= id_to_path($id, CATEGORY_TABLE) . "\\n";
				} else {
					$finalDelete[] = array('id' => $id, 'IsDir' => $IsDir);
				}
			}
			if(!empty($finalDelete)){
				foreach($finalDelete as $foo){
					if($foo['IsDir']){
						$this->delDir($foo['id']);
					} else {
						$this->delEntry($foo['id']);
					}
					if($this->dir == $foo['id']){
						$changeToParent = true;
					}
				}
			}
			if($catlistNotDeleted){
				echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('fileselector', '[cat_in_use]') . '\n\n' . $catlistNotDeleted, we_message_reporting::WE_MESSAGE_ERROR)
				);
			}
			if($changeToParent){
				$this->dir = $this->values['ParentID'];
			}
			$this->id = $this->dir;

			echo we_html_element::jsElement(
					'top.clearEntries();' .
					$this->printCmdAddEntriesHTML() .
					$this->printCMDWriteAndFillSelectorHTML() .
					'top.makeNewFolder = 0;
top.currentPath = "' . ($this->id ? f('SELECT Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($this->id), '', $this->db) : '') . '";
top.currentID = "' . $this->id . '";
top.selectFile(' . $this->id . ');
if(top.currentID && top.fsfooter.document.we_form.fname.value != ""){
	top.fsheader.enableDelBut();
}');
		}
		echo '</head><body></body></html>';

		return;
		/*
		  $IsDir = f('SELECT IsFolder FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->id), '', $this->db);
		  if($this->CatInUse($this->id, $IsDir)){

		  print we_html_element::jsElement(
		  we_message_reporting::getShowMessageCall(g_l('fileselector', '[cat_in_use]') . '\n\n' . $catlistNotDeleted, we_message_reporting::WE_MESSAGE_ERROR)
		  );
		  } else {
		  if($IsDir){
		  $this->delDir($this->id);
		  } else {
		  $this->delEntry($this->id);
		  }
		  if($this->dir && ($this->dir == $this->id)){
		  $this->dir = $this->values["ParentDir"];
		  }
		  $this->id = $this->dir;

		  if($this->id){
		  list($Path, $Text) = getHash('SELECT Path,Text FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($this->id), $this->db, MYSQL_NUM);
		  } else {
		  $Path = '';
		  $Text = '';
		  }
		  print we_html_element::jsElement(
		  'top.clearEntries();' .
		  $this->printCmdAddEntriesHTML() .
		  $this->printCMDWriteAndFillSelectorHTML() . '
		  top.makeNewFolder = 0;
		  top.currentPath = "' . $Path . '";
		  top.currentID = "' . $this->id . '";
		  top.fsfooter.document.we_form.fname.value = "' . $Text . '";
		  if(top.currentID && top.fsfooter.document.we_form.fname.value != ""){
		  top.fsheader.enableDelBut();
		  }');
		  }

		  print '</head><body></body></html>';
		 *
		 */
	}

	function delDir($id){
		$this->db->query('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($id));
		$entries = $this->db->getAll(true);
		foreach($entries as $entry){
			$this->delDir($entry);
		}
		$this->db->query('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=0 AND ParentID=' . intval($id));
		$entries = $this->db->getAll(true);
		$entries[] = $id;
		$this->delEntry($entries);
	}

	function delEntry($id){
		$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ID IN (' . (is_array($id) ? implode(',', $id) : intval($id)) . ')');
	}

	protected function printFooterTable(){
		if($this->values['Text'] === '/'){
			$this->values['Text'] = '';
		}
		$csp = $this->noChoose ? 4 : 5;

		$okBut = (!$this->noChoose ? we_html_button::create_button('ok', 'javascript:press_ok_button();') : '');
		$cancelbut = we_html_button::create_button('close', 'javascript:top.exit_close();');
		$buttons = ($okBut ? we_html_button::position_yes_no_cancel($okBut, null, $cancelbut) : $cancelbut);
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
		<td class="defaultfont"><b>' . g_l('fileselector', '[catname]') . '</b></td>
		<td></td>
		<td class="defaultfont" align="left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '</td>
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

	protected function getFrameset(){
		$isMainChooser = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'openCatselector' && !(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 3) || we_base_request::_(we_base_request::JS, 'we_cmd', false, 5));
		return '<frameset rows="67,*,65,0" border="0">
	<frame src="' . $this->getFsQueryString(we_selector_file::HEADER) . '" name="fsheader" noresize scrolling="no">
' . ($isMainChooser ? '<frameset cols="35%,65%" border="0">' : '') . '
    	<frame src="' . $this->getFsQueryString(we_selector_file::BODY) . '" name="fsbody" scrolling="auto">
' . ($isMainChooser ? '<frame src="' . $this->getFsQueryString(self::PROPERTIES) . '" name="fsvalues"  scrolling="auto"></frameset>' : '') . '
    <frame src="' . $this->getFsQueryString(we_selector_file::FOOTER) . '"  name="fsfooter" noresize scrolling="no">
    <frame src="about:blank"  name="fscmd" noresize scrolling="no">
</frameset>
<body>
</body>
</html>';
	}

	function printChangeCatHTML(){
		if(!($catId = we_base_request::_(we_base_request::INT, "catid"))){
			return;
		}
		$db = $GLOBALS['DB_WE'];
		$result = getHash('SELECT Category,Title,Description,ParentID,Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . $catId, $db);
		$title = we_base_request::_(we_base_request::STRING, "catTitle", $result["Title"]);
		$description = we_base_request::_(we_base_request::RAW, "catDescription", $result["Description"]);
		$path = $result['Path'];
		$parentid = we_base_request::_(we_base_request::INT, 'FolderID', $result['ParentID']);
		$category = we_base_request::_(we_base_request::STRING, 'Category', $result['Category']);

		$targetPath = id_to_path($parentid, CATEGORY_TABLE);

		$js = '';
		if(preg_match('|^' . preg_quote($path, '|') . '|', $targetPath) || preg_match('|^' . preg_quote($path, '|') . '/|', $targetPath)){
			// Verschieben nicht m�glich
			$parentid = $result['ParentID'];

			if($parentid == 0){
				$parentPath = '/';
				$path = '/' . $category;
			} else {
				$tmp = explode('/', $path);
				array_pop($tmp);
				$parentPath = implode('/', $tmp);
				$path = $parentPath . '/' . $category;
			}
			$js = "top.frames['fsvalues'].document.we_form.elements['FolderID'].value = '" . $parentid . "';top.frames['fsvalues'].document.we_form.elements['FolderIDPath'].value = '" . $parentPath . "';";
		} else {
			$path = ($parentid ? $targetPath : '') . '/' . $category;
		}
		$updateok = $db->query('UPDATE ' . CATEGORY_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'Category' => $category,
					'Text' => $category,
					'Path' => $path,
					'ParentID' => $parentid,
					'Title' => $title,
					'Description' => $description
				)) . ' WHERE ID=' . $catId);
		if($updateok){
			$this->renameChildrenPath($catId);
		}
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop() .
		we_html_element::jsElement($js . 'top.setDir(top.fsheader.document.we_form.elements[\'lookin\'].value);' .
				($updateok ? we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][response_save_ok]'), $category), we_message_reporting::WE_MESSAGE_NOTICE) : we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][response_save_notok]'), $category), we_message_reporting::WE_MESSAGE_ERROR) )
		) .
		'</head><body></body></html>';
	}

	function printPropertiesHTML(){
		$showPrefs = we_base_request::_(we_base_request::INT, 'catid', 0);

		$path = $title = '';
		$variant = isset($_SESSION['weS']["we_catVariant"]) ? $_SESSION['weS']["we_catVariant"] : "default";
		$_SESSION['weS']["we_catVariant"] = $variant;
		$description = "";
		if($showPrefs){
			$db = new DB_WE();
			$result = getHash('SELECT ID,Category,Title,Description,Path,ParentID FROM ' . CATEGORY_TABLE . ' WHERE ID=' . $showPrefs, $db);

			$path = ($result["ParentID"] ?
							(f('SELECT Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($result["ParentID"]), '', $db)? :
									'/'
							) :
							'/');

			$parentId = $result ? $result["ParentID"] : 0;
			$category = $result ? $result["Category"] : '';
			$catID = $result ? intval($result["ID"]) : 0;
			$title = $result ? $result['Title'] : '';
			$description = $result ? $result["Description"] : '';

			$dir_chooser = we_html_button::create_button('select', "javascript:we_cmd('openSelector', document.we_form.elements['FolderID'].value, '" . CATEGORY_TABLE . "', 'document.we_form.elements[\\'FolderID\\'].value', 'document.we_form.elements[\\'FolderIDPath\\'].value', '', '', '', '1', '', 'false', 1)");

			$yuiSuggest = &weSuggest::getInstance();
			$yuiSuggest->setAcId('Doc');
			$yuiSuggest->setTable(CATEGORY_TABLE);
			$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$yuiSuggest->setInput('FolderIDPath', $path);
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(false);
			$yuiSuggest->setResult('FolderID', $parentId);
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setWidth(250);
			$yuiSuggest->setSelectButton($dir_chooser, 10);
			$yuiSuggest->setContainerWidth(350);

			$table = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0), 4, 3);

			$table->setCol(0, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), '<b>' . g_l('weClass', '[category]') . '</b>');
			$table->setCol(0, 1, array("colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), we_html_tools::htmlTextInput("Category", 50, $category, "", ' id="category"', "text", 360));

			$table->setCol(1, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), "<b>ID</b>");
			$table->setCol(1, 1, array("colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), $catID);

			$table->setCol(2, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), '<b>' . g_l('weClass', '[dir]') . '</b>');
			$table->setCol(2, 1, array("style" => "width:240px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), $yuiSuggest->getHTML());

			$table->setCol(3, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), "<b>" . g_l('global', '[title]') . "</b>");
			$table->setCol(3, 1, array("colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), we_html_tools::htmlTextInput("catTitle", 50, $title, "", '', "text", 360));

			$ta = we_html_tools::htmlFormElementTable(we_html_forms::weTextarea("catDescription", $description, array("bgcolor" => "white", "inlineedit" => "true", "wysiwyg" => "true", "width" => 450, "height" => 130), true, 'autobr', true, "", true, true, true, false, ""), "<b>" . g_l('global', '[description]') . "</b>", "left", "defaultfont", "", "", "", "", "", 0);
			$saveBut = we_html_button::create_button("save", "javascript:weWysiwygSetHiddenText();we_checkName();");
		}

		we_html_tools::protect();

		echo we_html_tools::getHtmlTop() .
		STYLESHEET . we_html_element::jsScript(JS_DIR . 'we_textarea.js') . we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
function we_cmd(){
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	switch (arguments[0]){
		case "openSelector":
			new jsWindow(url,"we_selector",-1,-1,' . self::WINDOW_SELECTOR_WIDTH . ',' . self::WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
			break;
		default:
			for(var i = 0; i < arguments.length; i++){
				args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
			}
			eval(\'parent.we_cmd(\'+args+\')\');
	}
}
function we_checkName() {
	var regExp = /\'|"|>|<|\\\|\\//;
	if(regExp.test(document.getElementById("category").value)) {' .
				we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][we_filename_notValid]'), $path), we_message_reporting::WE_MESSAGE_ERROR) . '
	} else {
		document.we_form.submit();
	}
}') .
		weSuggest::getYuiFiles() .
		'</head><body class="defaultfont" style="margin:0px;padding: 15px 0 0 10px;background-image:url(' . IMAGE_DIR . 'backgrounds/aquaBackgroundLineLeft.gif);">
' . ($showPrefs ? '
	<form onsubmit="weWysiwygSetHiddenText();"; action="' . $_SERVER["SCRIPT_NAME"] . '" name="we_form" method="post" target="fscmd"><input type="hidden" name="what" value="' . self::CHANGE_CAT . '" /><input type="hidden" name="catid" value="' . we_base_request::_(we_base_request::INT, 'catid', 0) . '" />
		' . $table->getHtml() . "<br/>" . $ta . "<br/>" . $saveBut . '
	</div>' : '' ) .
		(isset($yuiSuggest) ?
				$yuiSuggest->getYuiCss() .
				$yuiSuggest->getYuiJs() : '') .
		'</body></html>';
	}

}
