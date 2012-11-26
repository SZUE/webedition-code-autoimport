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
class we_dirSelector extends we_multiSelector{

	const NEWFOLDER = 7;
	const RENAMEFOLDER = 9;
	const DORENAMEFOLDER = 10;
	const PREVIEW = 11;

	var $fields = "ID,ParentID,Text,Path,IsFolder,Icon,ModDate,RestrictOwners,Owners,OwnersReadOnly,CreatorID";
	var $userCanMakeNewFolder = true;
	var $userCanRenameFolder = true;
	var $we_editDirID = "";
	var $FolderText = "";
	var $rootDirID = 0;

	function __construct($id, $table = "", $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $sessionID = "", $we_editDirID = "", $FolderText = "", $rootDirID = 0, $multiple = 0){
		if($table == ""){
			$table = FILE_TABLE;
		}
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $rootDirID, $multiple);
		$this->title = g_l('fileselector', '[dirSelector][title]');
		$this->userCanMakeNewFolder = $this->userCanMakeNewDir();
		$this->userCanRenameFolder = $this->userCanRenameFolder();
		$this->we_editDirID = $we_editDirID;
		$this->FolderText = $FolderText;
		$this->rootDirID = $rootDirID;
	}

	function printHTML($what = we_fileselector::FRAMESET){
		switch($what){
			case we_fileselector::HEADER:
				$this->printHeaderHTML();
				break;
			case we_fileselector::FOOTER:
				$this->printFooterHTML();
				break;
			case we_fileselector::BODY:
				$this->printBodyHTML();
				break;
			case we_fileselector::CMD:
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

	function printCmdHTML(){
		print '<script><!--
top.clearEntries();';
		$this->printCmdAddEntriesHTML();
		$this->printCMDWriteAndFillSelectorHTML();

		print (intval($this->dir) == intval($this->rootDirID) ?
				'top.fsheader.disableRootDirButs();' :
				'top.fsheader.enableRootDirButs();') .
			'top.currentPath = "' . $this->path . '";
top.parentID = "' . $this->values["ParentID"] . '";
//-->
</script>';
	}

	function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($this->dir) . ' AND((1' . makeOwnersSql() . ') ' .
			getWsQueryForSelector($this->table) . ')' . ($this->order ? (' ORDER BY ' . $this->order) : ''));
	}

	function setDefaultDirAndID($setLastDir){
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
		$this->path = "";

		$this->values = array("ParentID" => 0,
			"Text" => "/",
			"Path" => "/",
			"IsFolder" => 1,
			"ModDate" => 0,
			"RestrictOwners" => 0,
			"Owners" => "",
			"OwnersReadOnly" => "",
			"CreatorID" => 0);
	}

	function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . "?what=$what&rootDirID=" . $this->rootDirID . "&table=" . $this->table . "&id=" . $this->id . "&order=" . $this->order . (isset($this->open_doc) ? ("&open_doc=" . $this->open_doc) : "");
	}

	function printFramesetJSFunctions(){
		parent::printFramesetJSFunctions();
		?>

		function drawNewFolder(){
		unselectAllFiles();
		top.fscmd.location.replace(top.queryString(<?php print self::NEWFOLDER; ?>,currentDir));
		}
		function RenameFolder(id){
		unselectAllFiles();
		top.fscmd.location.replace(top.queryString(<?php print self::RENAMEFOLDER; ?>,currentDir,'',id));
		}

		<?php
	}

	function printFramesetJSFunctioWriteBody(){
		$htmltop = preg_replace("/[[:cntrl:]]/", "", trim(str_replace("'", "\\'", we_html_tools::getHtmlTop())));
		$htmltop = str_replace('script', "scr' + 'ipt", $htmltop);
		?>
		function writeBody(d){
		d.open();
		//d.writeln('<?php print $htmltop; ?>');
		d.writeln('<?php print we_html_element::htmlDocType(); ?><html><head><title>webEdition</title><meta http-equiv="expires" content="0"><meta http-equiv="pragma" content="no-cache"><?php echo we_html_tools::htmlMetaCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']); ?><meta http-equiv="imagetoolbar" content="no"><meta name="generator" content="webEdition">');
				d.writeln('<?php print STYLESHEET_SCRIPT; ?>');
				d.writeln('<scr'+'ipt>');

				<?php print $this->getJS_attachKeyListener(); ?>

				//from we_showMessage.js
				d.writeln('var WE_MESSAGE_INFO = -1;');
				d.writeln('var WE_MESSAGE_FRONTEND = -2;');
				d.writeln('var WE_MESSAGE_NOTICE = 1;');
				d.writeln('var WE_MESSAGE_WARNING = 2;');
				d.writeln('var WE_MESSAGE_ERROR = 4;');
				d.writeln('function we_showMessage (message, prio, win) {');
				d.writeln('if (win.top.showMessage != null) {');
				d.writeln('win.top.showMessage(message, prio, win);');
				d.writeln('} else if (win.top.opener) {');
				d.writeln('if (win.top.opener.top.showMessage != null) {');
				d.writeln('win.top.opener.top.showMessage(message, prio, win);');
				d.writeln('} else if (win.top.opener.top.opener.top.showMessage != null) {');
				d.writeln('win.top.opener.top.opener.top.showMessage(message, prio, win);');
				d.writeln('} else if (win.top.opener.top.opener.top.opener.top.showMessage != null) {');
				d.writeln('win.top.opener.top.opener.top.showMessage(message, prio, win);');
				d.writeln('}');
				d.writeln('} else { // there is no webEdition window open, just show the alert');
				d.writeln('if (!win) {');
				d.writeln('win = window;');
				d.writeln('}');
				d.writeln('win.alert(message);');
				d.writeln('}');
				d.writeln('}');

				d.writeln('var ctrlpressed=false');
				d.writeln('var shiftpressed=false');
				d.writeln('var inputklick=false');
				d.writeln('var wasdblclick=false');
				d.writeln('var tout=null');
				d.writeln('function submitFolderMods(){');
				//	d.writeln('document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value); document.we_form.submit();');
				d.writeln('}');
				d.writeln('document.onclick = weonclick;');
				d.writeln('function weonclick(e){');
				d.writeln('top.fspreview.document.body.innerHTML = "";');
				if(makeNewFolder ||  we_editDirID){
				d.writeln('if(!inputklick){');
				d.writeln('document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);document.we_form.submit();');
				d.writeln('}else{  ');
				d.writeln('inputklick=false;');
				d.writeln('}  ');
				}else{
				d.writeln('inputklick=false;');
				d.writeln('if(document.all){');
				d.writeln('if(event.ctrlKey || event.altKey){ ctrlpressed=true;}');
				d.writeln('if(event.shiftKey){ shiftpressed=true;}');
				d.writeln('}else{  ');
				d.writeln('if(e.altKey || e.metaKey || e.ctrlKey){ ctrlpressed=true;}');
				d.writeln('if(e.shiftKey){ shiftpressed=true;}');
				d.writeln('}');
				<?php if($this->multiple){ ?>
					d.writeln('if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}');
				<?php } else{ ?>
					d.writeln('top.unselectAllFiles();');
				<?php } ?>
				}
				d.writeln('}');
				d.writeln('</scr'+'ipt>');
			d.writeln('</head>');
		d.writeln('<body bgcolor="white" LINK="#000000" ALINK="#000000" VLINK="#000000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"'+((makeNewFolder || top.we_editDirID) ? '  onload="document.we_form.we_FolderText_tmp.focus();document.we_form.we_FolderText_tmp.select();"' : '')+'>');
										 d.writeln('<form name="we_form" target="fscmd" action="<?php print $_SERVER["SCRIPT_NAME"]; ?>" onSubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">');

				if(we_editDirID){
				d.writeln('<input type="hidden" name="what" value="<?php print self::DORENAMEFOLDER; ?>" />');
				d.writeln('<input type="hidden" name="we_editDirID" value="'+top.we_editDirID+'" />');
				}else{
				d.writeln('<input type="hidden" name="what" value="<?php print self::CREATEFOLDER; ?>" />');
				}
				d.writeln('<input type="hidden" name="order" value="'+top.order+'" />');
				d.writeln('<input type="hidden" name="rootDirID" value="<?php print $this->rootDirID; ?>" />');
				d.writeln('<input type="hidden" name="table" value="<?php print $this->table; ?>" />');
				d.writeln('<input type="hidden" name="id" value="'+top.currentDir+'" />');
				d.writeln('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
					if(makeNewFolder){
					d.writeln('<tr style="background-color:#DFE9F5;">');
						d.writeln('<td align="center"><img src="<?php print ICON_DIR ?>folder.gif" width="16" height="18" border="0" /></td>');
						d.writeln('<td><input type="hidden" name="we_FolderText" value="<?php print g_l('fileselector', "[new_folder_name]"); ?>" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="<?php print g_l('fileselector', "[new_folder_name]"); ?>" class="wetextinput" onBlur="submitFolderMods(); this.className=\'wetextinput\';" onFocus="this.className=\'wetextinputselected\'" style="width:100%" /></td>');
						d.writeln('<td class="selector"><?php print date(g_l('date', '[format][default]')) ?></td>');
						d.writeln('</tr>');
					}
					for(i=0;i < entries.length; i++){
					var onclick = ' onClick="weonclick(<?php echo (we_base_browserDetect::isIE() ? "this" : "event") ?>);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick('+entries[i].ID+',0);}else{top.wasdblclick=0;}\',300);return true"';
					var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick('+entries[i].ID+',1);return true;"';
					d.writeln('<tr id="line_'+entries[i].ID+'" style="' + ((entries[i].ID == top.currentID && (!makeNewFolder) )  ? 'background-color:#DFE9F5;' : '')+'cursor:pointer;'+((we_editDirID != entries[i].ID) ? '' : '' )+'"'+((we_editDirID || makeNewFolder) ? '' : onclick)+ (entries[i].isFolder ? ondblclick : '') + '>');
												 d.writeln('<td class="selector" align="center">');
							d.writeln('<img src="<?php print ICON_DIR; ?>'+entries[i].icon+'" width="16" height="18" border="0" />');
							d.writeln('</td>');
						if(we_editDirID == entries[i].ID){
						d.writeln('<td class="selector">');
							d.writeln('<input type="hidden" name="we_FolderText" value="'+entries[i].text+'" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="'+entries[i].text+'" class="wetextinput" onBlur="submitFolderMods(); this.className=\'wetextinput\';" onFocus="this.className=\'wetextinputselected\'" style="width:100%" />');
							}else{
							d.writeln('<td class="selector" style="" title="'+entries[i].text+'">');
							d.writeln(cutText(entries[i].text,30));
							}
							d.writeln('</td>');
						d.writeln('<td class="selector">');
							d.writeln(entries[i].modDate);
							d.writeln('</td>');
						d.writeln('</tr><tr><td colspan="3"><?php print we_html_tools::getPixel(2, 1); ?></td></tr>');
					}
					d.writeln('<tr>');
						d.writeln('<td width="25"><?php print we_html_tools::getPixel(25, 2) ?></td>');
						d.writeln('<td width="200"><?php print we_html_tools::getPixel(200, 2) ?></td>');
						d.writeln('<td><?php print we_html_tools::getPixel(300, 2) ?></td>');
						d.writeln('</tr>');
					d.writeln('</table></form>');
			d.writeln('</body>');
		d.close();
		}

		<?php
	}

	function printFramesetJSFunctionQueryString(){
		?>

		function queryString(what,id,o,we_editDirID){
		if(!o) o=top.order;
		if(!we_editDirID) we_editDirID="";
		return '<?php print $_SERVER["SCRIPT_NAME"]; ?>?what='+what+'&rootDirID=<?php
		print $this->rootDirID;
		if(isset($this->open_doc)){
			print "&open_doc=" . $this->open_doc;
		}
		?>&table=<?php print $this->table; ?>&id='+id+(o ? ("&order="+o) : "")+(we_editDirID ? ("&we_editDirID="+we_editDirID) : "");
		}

		<?php
	}

	function printFramesetJSFunctionEntry(){
		?>

		function entry(ID,icon,text,isFolder,path,modDate){
		this.ID=ID;
		this.icon=icon;
		this.text=text;
		this.isFolder=isFolder;
		this.path=path;
		this.modDate=modDate;
		}

		<?php
	}

	function printFramesetJSFunctionAddEntry(){
		?>

		function addEntry(ID,icon,text,isFolder,path,modDate){
		entries[entries.length] = new entry(ID,icon,text,isFolder,path,modDate);
		}

		<?php
	}

	function printFramesetJSFunctionAddEntries(){
		while($this->next_record()) {
			print 'addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '","' . date(g_l('date', '[format][default]'), (is_numeric($this->f("ModDate")) ? $this->f("ModDate") : 0)) . '");' . "\n";
		}
	}

	function printCmdAddEntriesHTML(){
		$this->query();
		while($this->next_record()) {
			print 'top.addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '","' . date(g_l('date', '[format][default]'), (is_numeric($this->f("ModDate")) ? $this->f("ModDate") : 0)) . '");' . "\n";
		}
		if($this->userCanMakeNewDir()){
			print 'top.fsheader.enableNewFolderBut();' . "\n";
		} else{
			print 'top.fsheader.disableNewFolderBut();' . "\n";
		}
	}

	function printHeaderHeadlines(){
		print '			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>' . we_html_tools::getPixel(25, 14) . '</td>
					<td class="selector"><b><a href="#" onclick="javascript:top.orderIt(\'IsFolder DESC, Text\');">' . g_l('fileselector', "[filename]") . '</a></b></td>
					<td class="selector"><b><a href="#" onclick="javascript:top.orderIt(\'IsFolder DESC, ModDate\');">' . g_l('fileselector', "[modified]") . '</a></b></td>
				</tr>
				<tr>
					<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
					<td width="200">' . we_html_tools::getPixel(200, 1) . '</td>
					<td>' . we_html_tools::getPixel(300, 1) . '</td>
				</tr>
			</table>
';
	}

	function printHeaderJSDef(){
		we_fileselector::printHeaderJSDef();
		print 'var makefolderState = ' . ($this->userCanMakeNewFolder ? 1 : 0) . ';
';
	}

	function printHeaderJS(){
		we_fileselector::printHeaderJS();
		print we_button::create_state_changer(false) . '
function disableNewFolderBut(){

	btn_new_dir_enabled = switch_button_state("btn_new_dir", "new_directory_enabled", "disabled", "image");
	makefolderState = 0;
}
function enableNewFolderBut(){

	btn_new_dir_enabled = switch_button_state("btn_new_dir", "new_directory_enabled", "enabled", "image");
	makefolderState = 1;
}';
	}

	function userCanSeeDir($showAll = false){
		if($_SESSION["perms"]["ADMINISTRATOR"])
			return true;
		if(!$showAll){
			if(!in_workspace(intval($this->dir), get_ws($this->table), $this->table, $this->db)){
				return false;
			}
		}
		if(!userIsOwnerCreatorOfParentDir($this->dir, $this->table)){
			return false;
		}

		return true;
	}

	function userCanRenameFolder(){

		if($_SESSION["perms"]["ADMINISTRATOR"]){
			return true;
		}
		if(!$this->userHasRenameFolderPerms()){

			return false;
		}
		return true;
	}

	function userCanMakeNewDir(){
		if(defined("OBJECT_FILES_TABLE") && ($this->table == OBJECT_FILES_TABLE) && (!$this->dir)){
			return false;
		}
		if($_SESSION["perms"]["ADMINISTRATOR"])
			return true;
		if(!$this->userCanSeeDir())
			return false;
		if(!$this->userHasFolderPerms()){
			return false;
		}
		return true;
	}

	function userHasRenameFolderPerms(){

		switch($this->table){
			case FILE_TABLE:
				if(!we_hasPerm("CHANGE_DOC_FOLDER_PATH")){
					return false;
				}
				break;
		}
		return true;
	}

	function userHasFolderPerms(){

		switch($this->table){
			case FILE_TABLE:
				if(!we_hasPerm("NEW_DOC_FOLDER")){
					return false;
				}
				break;
			case TEMPLATES_TABLE:
				if(!we_hasPerm("NEW_TEMP_FOLDER")){
					return false;
				}
				break;
			default:
				if(defined("OBJECT_FILES_TABLE")){
					switch($this->table){
						case OBJECT_FILES_TABLE:
							if(!we_hasPerm("NEW_OBJECTFILE_FOLDER")){
								return false;
							}
							break;
					}
				}
		}
		return true;
	}

	function printFramesetRootDirFn(){
		print 'function setRootDir(){
	setDir(' . intval($this->rootDirID) . ');
}
';
	}

	function printCMDWriteAndFillSelectorHTML(){
		print '
			top.writeBody(top.fsbody.document);
			top.fsheader.clearOptions();';

		$pid = $this->dir;
		$out = "";
		$c = 0;
		while($pid != 0) {
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
		if(!$this->rootDirID){
			$out = 'top.fsheader.addOption("/",0);' . $out;
		}
		print $out . 'top.fsheader.selectIt();';
	}

	function printHeaderTable(){
		print '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
		$this->printHeaderTableSpaceRow();
		print '				<tr valign="middle">
					<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
					<td width="70" class="defaultfont"><b>' . g_l('fileselector', "[lookin]") . '</b></td>
					<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
					<td>
					<select name="lookin" class="weSelect" size="1" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%">
';
		$this->printHeaderOptions();

		print '</select>
';
		if((!defined("OBJECT_TABLE")) || $this->table != OBJECT_TABLE){

			print '</td>
					<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
					<td width="40">
						' . we_button::create_button("root_dir", "javascript:if(rootDirButsState){top.setRootDir();}", true, -1, 22, "", "", $this->dir == intval($this->rootDirID), false) . '
					</td>
					<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
					<td width="40">
						' . we_button::create_button("image:btn_fs_back", "javascript:if(rootDirButsState){top.goBackDir();}", true, -1, 22, "", "", $this->dir == intval($this->rootDirID), false) . '
					</td>';
			$this->printHeaderTableExtraCols();
		}
		print '<td width="10">' . we_html_tools::getPixel(10, 29) . '</td></tr>';
		$this->printHeaderTableSpaceRow();

		print '</table>';
	}

	function printHeaderOptions(){
		$pid = $this->dir;
		$out = "";
		$c = 0;
		$z = 0;
		while($pid != 0) {
			$c++;
			$this->db->query("SELECT ID,Text,ParentID FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($pid));
			if($this->db->next_record()){
				$out = '<option value="' . $this->db->f("ID") . '"' . (($z == 0) ? ' selected' : '') . '>' . $this->db->f("Text") . '</options>' . "\n" . $out;
				$z++;
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
		if(!$this->rootDirID){
			$out = '<option value="0">/</option>' . $out . "\n";
		}
		print $out;
	}

	function printHeaderTableExtraCols(){
		print '                <td width="10">' . we_html_tools::getPixel(10, 10) . '</td><td width="40">
';
		$makefolderState = $this->userCanMakeNewDir() ? 1 : 0;

		print we_button::create_button("image:btn_new_dir", "javascript:top.drawNewFolder();", true, -1, 22, "", "", !$this->userCanMakeNewDir(), false);
		print '               </td>
';
	}

	function printHeaderTableSpaceRow(){
		print '				<tr>
					<td colspan="11">' . we_html_tools::getPixel(5, 10) . '</td>
				</tr>
';
	}

	function printFramesetJSDoClickFn(){
		?>

		function showPreview(id) {
		if(top.fspreview) {
		top.fspreview.location.replace(top.queryString(<?php print self::PREVIEW; ?>,id));
		}
		}

		function doClick(id,ct){
		top.fspreview.document.body.innerHTML = "";
		if(ct==1){
		if(wasdblclick){
		setDir(id);
		setTimeout('wasdblclick=0;',400);
		}
		}else{
		if(top.currentID == id && (!fsbody.ctrlpressed)){
		<?php
		print $this->userCanRenameFolder ? 'top.RenameFolder(id);' : 'selectFile(id);';
		?>

		}else{
		<?php if($this->multiple){ ?>
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
			}else{

		<?php } ?>

		selectFile(id);

		<?php if($this->multiple){ ?>

			}
			}

		<?php } ?>

		}
		}
		if(fsbody.ctrlpressed){
		fsbody.ctrlpressed = 0;
		}
		if(fsbody.shiftpressed){
		fsbody.shiftpressed = 0;
		}
		}

		<?php
	}

	function printFramesetJSsetDir(){
		?>
		function setDir(id){
		showPreview(id);
		top.fspreview.document.body.innerHTML = "";
		top.fscmd.location.replace(top.queryString(<?php print we_multiSelector::SETDIR; ?>,id));
		e = getEntry(id);
		fspath.document.body.innerHTML = e.path;
		}


		<?php
	}

	function printSetDirHTML(){
		print '<script>
top.clearEntries();
';
		$this->printCmdAddEntriesHTML();
		$this->printCMDWriteAndFillSelectorHTML();

		if(intval($this->dir) == intval($this->rootDirID)){
			print 'top.fsheader.disableRootDirButs();
';
		} else{
			print 'top.fsheader.enableRootDirButs();
';
		}
		if(in_workspace(intval($this->dir), get_ws($this->table), $this->table, $this->db)){
			if($this->id == 0)
				$this->path = "/";
			print 'top.unselectAllFiles();top.currentPath = "' . $this->path . '";
top.currentID = "' . $this->id . '";
top.fsfooter.document.we_form.fname.value = "' . (($this->id == 0) ? "/" : $this->values["Text"]) . '";
';
		}
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
		print 'top.currentDir = "' . $this->dir . '";
top.parentID = "' . $this->values["ParentID"] . '";
</script>
';
	}

	function printFramesetSelectFileHTML(){
		?>

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
		}

		<?php
	}

	function printNewFolderHTML(){
		print '<script>
top.clearEntries();
top.makeNewFolder=1;
';
		$this->printCmdAddEntriesHTML();
		$this->printCMDWriteAndFillSelectorHTML();

		print 'top.makeNewFolder = 0;
</script>
';
	}

	function printCreateFolderHTML(){
		we_html_tools::htmlTop();
		we_html_tools::protect();

		print '<script>
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if($txt == ""){
			print we_message_reporting::getShowMessageCall(g_l('weEditor', "[folder][filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
			//}elseif(strpos($txt,".")!==false){ entfernt fuer #4333
			//print we_message_reporting::getShowMessageCall(g_l('weEditor',"[folder][we_filename_notAllowed]"), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(substr($txt, -1) == '.'){ // neue Version f�r 4333 testet auf "." am ende, analog zu i_filenameNotAllowed in we_root
			print we_message_reporting::getShowMessageCall(g_l('weEditor', "[folder][we_filename_notAllowed]"), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(preg_match('/[^a-z0-9\._\-]/i', $txt)){ // Test auf andere verbotene Zeichen
			print we_message_reporting::getShowMessageCall(g_l('weEditor', "[folder][we_filename_notValid]"), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif($_REQUEST['id'] == 0 && strtolower($txt) == "webedition"){
			print we_message_reporting::getShowMessageCall(g_l('weEditor', "[folder][we_filename_notAllowed]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			if(defined('OBJECT_FILES_TABLE') && $this->table == OBJECT_FILES_TABLE){ //4076
				$folder = new we_class_folder();
			} else{
				$folder = new we_folder();
			}

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
			$this->db->query("SELECT ID FROM " . $this->table . " WHERE Path='" . $folder->Path . "'");
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('weEditor', "[folder][response_path_exists]"), $folder->Path);
				print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(preg_match('/[^a-z0-9\._\-]/i', $folder->Filename)){
					$we_responseText = sprintf(g_l('weEditor', "[folder][we_filename_notValid]"), $folder->Path);
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					$folder->we_save();
					print 'var ref;
if(top.opener.top.makeNewEntry) ref = top.opener.top;
else if(top.opener.top.opener) ref = top.opener.top.opener.top;
ref.makeNewEntry("' . $folder->Icon . '",' . $folder->ID . ',"' . $folder->ParentID . '","' . $txt . '",1,"' . $folder->ContentType . '","' . $this->table . '");
';
					if($this->canSelectDir){
						print 'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.fsfooter.document.we_form.fname.value = "' . $folder->Text . '";
';
					}
				}
			}
		}


		$this->printCmdAddEntriesHTML();
		$this->printCMDWriteAndFillSelectorHTML();

		print 'top.makeNewFolder = 0;
top.selectFile(top.currentID);
</script>
';
		print '</head><body></body></html>';
	}

	function getFrameset(){
		$out = '<frameset rows="67,*,65,20,0" border="0">
	<frame src="' . $this->getFsQueryString(we_fileselector::HEADER) . '" name="fsheader" noresize scrolling="no">
	<frameset cols="605,*" border="1">
		<frame src="' . $this->getFsQueryString(we_fileselector::BODY) . '" name="fsbody" noresize scrolling="auto">
		<frame src="' . $this->getFsQueryString(self::PREVIEW) . '" name="fspreview" noresize scrolling="no"' . ((!we_base_browserDetect::isGecko()) ? ' style="border-left:1px solid black"' : '') . '>
	</frameset>
	<frame src="' . $this->getFsQueryString(we_fileselector::FOOTER) . '"  name="fsfooter" noresize scrolling="no">
	<frame src="' . HTML_DIR . 'gray2.html"  name="fspath" noresize scrolling="no">
    <frame src="' . HTML_DIR . 'white.html"  name="fscmd" noresize scrolling="no">
</frameset>
<body>
</body>
</html>
';
		return $out;
	}

	function getFramesetJavaScriptDef(){
		$def = we_fileselector::getFramesetJavaScriptDef();
		$def .= 'var makeNewFolder=0;
var we_editDirID="";
var old=0;
';
		return $def;
	}

	function printRenameFolderHTML(){
		if(userIsOwnerCreatorOfParentDir($this->we_editDirID, $this->table) && in_workspace($this->we_editDirID, get_ws($this->table), $this->table, $this->db)){
			print '<script>
top.clearEntries();
top.we_editDirID=' . $this->we_editDirID . ';
';
			$this->printCmdAddEntriesHTML();
			$this->printCMDWriteAndFillSelectorHTML();

			print 'top.we_editDirID = "";
</script>
';
		}
	}

	function printDoRenameFolderHTML(){
		we_html_tools::htmlTop();
		we_html_tools::protect();

		print '<script>
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if($txt == ""){
			print we_message_reporting::getShowMessageCall(g_l('weEditor', "[folder][filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			if(defined('OBJECT_FILES_TABLE') && $this->table == OBJECT_FILES_TABLE){ //4076
				$folder = new we_class_folder();
			} else{
				$folder = new we_folder();
			}

			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->ModDate = time();
			$folder->Filename = $txt;
			$folder->Published = time();
			$folder->Path = $folder->getPath();
			$folder->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
			$this->db->query("SELECT ID,Text FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($folder->Path) . "' AND ID != " . intval($this->we_editDirID));
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('weEditor', "[folder][response_path_exists]"), $folder->Path);
				print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(preg_match('/[^a-z0-9\._\-]/i', $folder->Filename)){
					$we_responseText = sprintf(g_l('weEditor', "[folder][we_filename_notValid]"), $folder->Path);
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else if(in_workspace($this->we_editDirID, get_ws($this->table), $this->table, $this->db)){
					if(f("SELECT Text FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->we_editDirID), "Text", $this->db) != $txt){
						$folder->we_save();
						print 'var ref;
if(top.opener.top.makeNewEntry) ref = top.opener.top;
else if(top.opener.top.opener) ref = top.opener.top.opener.top;
';
						print 'ref.updateEntry(' . $folder->ID . ',"' . $txt . '","' . $folder->ParentID . '","' . $this->table . '");
';
						if($this->canSelectDir){
							print 'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.fsfooter.document.we_form.fname.value = "' . $folder->Text . '";
';
						}
					}
				}
			}
		}


		$this->printCmdAddEntriesHTML();
		$this->printCMDWriteAndFillSelectorHTML();

		print 'top.makeNewFolder = 0;
top.selectFile(top.currentID);
</script>
';
		print '</head><body></body></html>';
	}

	function printPreviewHTML(){
		if($this->id){
			$query = $this->db->query("SELECT * FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->id));
			while($this->db->next_record()) {
				$result['Text'] = $this->db->f('Text');
				$result['Path'] = $this->db->f('Path');
				$result['ContentType'] = $this->db->f('ContentType');
				$result['Type'] = $this->db->f('Type');
				$result['CreationDate'] = $this->db->f('CreationDate');
				$result['ModDate'] = $this->db->f('ModDate');
				$result['Filename'] = $this->db->f('Filename');
				$result['Extension'] = $this->db->f('Extension');
				$result['MasterTemplateID'] = $this->db->f('MasterTemplateID');
				$result['IncludedTemplates'] = $this->db->f('IncludedTemplates');
				$result['ClassName'] = $this->db->f('ClassName');
				$result['Templates'] = $this->db->f('Templates');
			}
			$path = f("SELECT Text, Path FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->id), "Path", $this->db);
			$out = we_html_tools::getHtmlTop() . '
' . STYLESHEET . '
<style type="text/css">
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
</style>
<script tyle="text/javascript">
	function setInfoSize() {
		infoSize = document.body.clientHeight;
		if(infoElem=document.getElementById("info")) {
			infoElem.style.height = document.body.clientHeight - (prieviewpic = document.getElementById("previewpic") ? 160 : 0 );
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
	setTimeout(\'weWriteBreadCrumb("' . $path . '")\',100);
	function weWriteBreadCrumb(BreadCrumb){
		if(top.fspath && top.fspath.document && top.fspath.document.body) top.fspath.document.body.innerHTML = BreadCrumb;
		else if(weCountWriteBC<10) setTimeout(\'weWriteBreadCrumb("' . $path . '")\',100);
		weCountWriteBC++;
	}
</script>
</head>
<body bgcolor="white" class="defaultfont" onresize="setInfoSize()" onload="setTimeout(\'setInfoSize()\',50)">
					';
			if(isset($result['ContentType']) && !empty($result['ContentType'])){
				if($this->table == FILE_TABLE && $result['ContentType'] != "folder"){
					$query = $this->db->query("SELECT a.Name, b.Dat FROM " . LINK_TABLE . " a LEFT JOIN " . CONTENT_TABLE . " b on (a.CID = b.ID) WHERE a.DID=" . intval($this->id) . " AND NOT a.DocumentTable='tblTemplates'");
					while($this->db->next_record()) {
						$metainfos[$this->db->f('Name')] = $this->db->f('Dat');
					}
				} elseif($this->table == FILE_TABLE && $result['ContentType'] = "folder"){
					$query = $this->db->query("SELECT ID, Text, IsFolder FROM " . $this->db->escape($this->table) . " WHERE ParentID=" . intval($this->id));
					$folderFolders = array();
					$folderFiles = array();
					while($this->db->next_record()) {
						$this->db->f('IsFolder') ? $folderFolders[$this->db->f('ID')] = $this->db->f('Text') : $folderFiles[$this->db->f('ID')] = $this->db->f('Text');
					}
				}

				$fs = file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path']) ? filesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']) : 0;

				$filesize = $fs < 1000 ? $fs . ' byte' : ($fs < 1024000 ? round(($fs / 1024), 2) . ' kb' : round(($fs / (1024 * 1024)), 2) . ' mb');
				$nextrowclass = "odd";
				$previewDefauts = "<tr><td class='info' width='100%'>";
				$previewDefauts .= "<div style='overflow:auto; height:100%' id='info'><table cellpadding='0' cellspacing='0' width='100%'>";

				$previewDefauts .= "<tr><td colspan='2' class='headline'>" . g_l('weClass', "[tab_properties]") . "</td></tr>";
				$previewDefauts .= "<tr class='odd'><td title=\"" . $result['Path'] . "\" width='10'>" . g_l('fileselector', "[name]") . ": </td><td>";
				$previewDefauts .= "<div style='margin-right:14px'>" . $result['Text'] . "</div></td></tr>";
				$previewDefauts .= "<tr class='even'><td width='10'>ID: </td><td>";
				$previewDefauts .= "<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'><div style='float:left; vertical-align:baseline; margin-right:4px;'><img src='" . ICON_DIR . "bearbeiten.gif' border='0' vspace='0' hspace='0'></div></a>";
				$previewDefauts .= "<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'><div>" . $this->id . "</div></a></td></tr>";
				if($result['CreationDate']){
					$previewDefauts .= "<tr class='odd'><td class='odd'>" . g_l('fileselector', "[created]") . ": </td><td>" . date(g_l('date', '[format][default]'), $result['CreationDate']) . "</td></tr>";
					$nextrowclass = "even";
				} else{
					$nextrowclass = "odd";
				}
				if($result['ModDate']){
					$previewDefauts .= "<tr class='$nextrowclass'><td>" . g_l('fileselector', "[modified]") . ": </td><td>" . date(g_l('date', '[format][default]'), $result['ModDate']) . "</td></tr>";
					$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
				} else{
					$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
				}
				$previewDefauts .= "<tr class='$nextrowclass'><td>" . g_l('fileselector', "[type]") . ": </td><td>" . (g_l('contentTypes', '[' . $result['ContentType'] . ']', true) !== false ? g_l('contentTypes', '[' . $result['ContentType'] . ']') : $result['ContentType']) . "</td></tr>";

				$out .= "\t<table cellpadding='0' cellspacing='0' height='100%' width='100%'>\n";
				switch($result['ContentType']){
					case "image/*":
						if(file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path'])){
							$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']);
							if($imagesize[0] > 150 || $imagesize[1] > 150){
								$extension = substr($result['Extension'], 1);
								$thumbpath = WE_THUMB_PREVIEW_DIR . $this->id . '.' . $extension;
								$created = filemtime($_SERVER['DOCUMENT_ROOT'] . $result['Path']);
								if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $thumbpath) || ($created > filemtime($_SERVER['DOCUMENT_ROOT'] . $thumbpath))){
									$thumb = we_image_edit::edit_image($_SERVER['DOCUMENT_ROOT'] . $result['Path'], $extension, $_SERVER['DOCUMENT_ROOT'] . $thumbpath, null, 150, 200);
								}
							} else{
								$thumbpath = $result['Path'];
							}

							$out .= "<tr><td valign='middle' class='image' height='160' align='center' bgcolor='#EDEEED'><a href='" . getServerUrl(true) . $result['Path'] . "' target='_blank' align='center'><img src='$thumbpath' border='0' id='previewpic'></a></td></tr>" .
								$previewDefauts;

							$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[width]") . " x " . g_l('weClass', "[height]") . ": </td><td>" . $imagesize[0] . " x " . $imagesize[1] . " px </td></tr>";
							$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('fileselector', "[filesize]") . ": </td><td>" . $filesize . "</td></tr>";

							$out .= "<tr><td colspan='2' class='headline'>" . g_l('weClass', "[metainfo]") . "</td></tr>";
							$nextrowclass = "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[Title]") . ": </td><td>" . (isset($metainfos['Title']) ? $metainfos['Title'] : '') . "</td></tr>";
							$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[Description]") . ": </td><td>" . (isset($metainfos['Description']) ? $metainfos['Description'] : '') . "</td></tr>";
							$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[Keywords]") . ": </td><td>" . (isset($metainfos['Keywords']) ? $metainfos['Keywords'] : '') . "</td></tr>";

							$out .= "<tr><td colspan='2' class='headline'>" . g_l('weClass', "[attribs]") . "</td></tr>";
							$nextrowclass = "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[Title]") . ": </td><td>" . (isset($metainfos['Title']) ? $metainfos['Title'] : '') . "</td></tr>";
							$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[name]") . ": </td><td>" . (isset($metainfos['name']) ? $metainfos['name'] : '') . "</td></tr>";
							$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[alt]") . ": </td><td>" . (isset($metainfos['alt']) ? $metainfos['alt'] : '') . "</td></tr>";
							$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[width]") . " x " . g_l('weClass', "[height]") . ": </td><td>" . (isset($metainfos['width']) ? $metainfos['width'] : '') . " x " . (isset($metainfos['height']) ? $metainfos['height'] : '') . " px </td></tr>";
						}
						break;
					case "folder":
						$out .= $previewDefauts;
						if(isset($folderFolders) && is_array($folderFolders) && count($folderFolders)){
							$out .= "<tr><td colspan='2' class='headline'>" . g_l('fileselector', "[folders]") . "</td></tr>";
							$nextrowclass = "odd";
							foreach($folderFolders as $fId => $fxVal){
								$out .= "<tr class='$nextrowclass'><td>" . $fId . ": </td><td>" . $fxVal . "</td></tr>";
								$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							}
						}
						if(isset($folderFiles) && is_array($folderFiles) && count($folderFiles)){
							$out .= "<tr><td colspan='2' class='headline'>" . g_l('fileselector', "[files]") . "</td></tr>";
							$nextrowclass = "odd";
							foreach($folderFiles as $fId => $fxVal){
								$out .= "<tr class='$nextrowclass'><td>" . $fId . ": </td><td>" . $fxVal . "</td></tr>";
								$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							}
						}
						break;
					case "text/weTmpl":
						$out .= $previewDefauts;
						if(isset($result['MasterTemplateID']) && !empty($result['MasterTemplateID'])){
							$mastertemppath = f("SELECT Text, Path FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($result['MasterTemplateID']), "Path", $this->db);
							$out .= "<tr><td colspan='2' class='headline'>" . g_l('weClass', "[master_template]") . "</td></tr>";
							$nextrowclass = "odd";
							$out .= "<tr class='$nextrowclass'><td>ID:</td><td>" . $result['MasterTemplateID'] . "</td></tr>";
							$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
							$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[path]") . ":</td><td>" . $mastertemppath . "</td></tr>";
						}
						break;
					case "text/webedition":
						$out .= $previewDefauts;
						$out .= "<tr><td colspan='2' class='headline'>" . g_l('weClass', "[metainfo]") . "</td></tr>";
						$nextrowclass = "odd";
						$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[Title]") . ":</td><td>" . (isset($metainfos['Title']) ? $metainfos['Title'] : '') . "</td></tr>";
						$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
						$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[Charset]") . ":</td><td>" . (isset($metainfos['Charset']) ? $metainfos['Charset'] : '') . "</td></tr>";
						$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
						$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[Keywords]") . ":</td><td>" . (isset($metainfos['Keywords']) ? $metainfos['Keywords'] : '') . "</td></tr>";
						$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
						$out .= "<tr class='$nextrowclass'><td>" . g_l('weClass', "[Description]") . ":</td><td>" . (isset($metainfos['Description']) ? $metainfos['Description'] : '') . "</td></tr>";
						break;
					case "text/html":
						$out .= $previewDefauts;
						$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
						$out .= "<tr class='$nextrowclass'><td>" . g_l('fileselector', "[filesize]") . ":</td><td>" . $filesize . "</td></tr>";
						break;
					case "text/css":
						$out .= $previewDefauts;
						$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
						$out .= "<tr class='$nextrowclass'><td>" . g_l('fileselector', "[filesize]") . ":</td><td>" . $filesize . "</td></tr>";
						break;
					case "text/js":
						$out .= $previewDefauts;
						$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
						$out .= "<tr class='$nextrowclass'><td>" . g_l('fileselector', "[filesize]") . ":</td><td>" . $filesize . "</td></tr>";
						break;
					case "application/*":
						$out .= $previewDefauts;
						$nextrowclass = $nextrowclass == "odd" ? "even" : "odd";
						$out .= "<tr class='$nextrowclass'><td>" . g_l('fileselector', "[filesize]") . ":</td><td>" . $filesize . "</td></tr>";
						break;
					case "object":
						$out .= $previewDefauts;
						break;
					case "objectFile":
						$out .= $previewDefauts;
						break;
					default:
						$out .= $previewDefauts;
				}
				$out .= "</table></div></td></tr>\t</table>\n";
			}
			$out .= "</body>\n</html>";
			echo $out;
		}
	}

}

