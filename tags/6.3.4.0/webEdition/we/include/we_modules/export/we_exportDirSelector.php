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
class we_exportDirSelector extends we_dirSelector{

	var $fields = "ID,ParentID,Text,Path,IsFolder,Icon";

	function __construct($id, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $we_editDirID = "", $FolderText = ""){

		parent::__construct($id, EXPORT_TABLE, $JSIDName, $JSTextName, $JSCommand, $order, "", $we_editDirID, $FolderText);
		$this->title = g_l('fileselector', '[exportDirSelector][title]');
		$this->userCanMakeNewFolder = true;
	}

	function printHeaderHeadlines(){
		print '			<table border="0" cellpadding="0" cellspacing="0" width="550">
				<tr>
					<td>' . we_html_tools::getPixel(25, 14) . '</td>
					<td class="selector"colspan="2"><b><a href="#" onclick="javascript:top.orderIt(\'IsFolder DESC, Text\');">' . g_l('export', "[name]") . '</a></b></td>
				</tr>
				<tr>
					<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
					<td width="200">' . we_html_tools::getPixel(200, 1) . '</td>
					<td width="300">' . we_html_tools::getPixel(300, 1) . '</td>
				</tr>
			</table>
';
	}

	function printFooterTable(){
		print '<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="5"><img src="' . IMAGE_DIR . 'umr_h_small.gif" width="100%" height="2" border="0" /></td>
				</tr>
				<tr>
					<td colspan="5">' . we_html_tools::getPixel(5, 5) . '</td>
				</tr>';
		$cancel_button = we_button::create_button("cancel", "javascript:top.exit_close();");
		$yes_button = we_button::create_button("ok", "javascript:press_ok_button();");
		$buttons = we_button::position_yes_no_cancel(
				$yes_button, null, $cancel_button);
		print '
				<tr>
					<td></td>
					<td class="defaultfont">
						<b>' . g_l('export', "[name]") . '</b>
					</td>
					<td></td>
					<td class="defaultfont" align="left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '
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

	function printHeaderTableExtraCols(){
		print '                <td width="10">' . we_html_tools::getPixel(10, 10) . '</td><td width="40">
';
		$makefolderState = we_hasPerm("NEW_EXPORT");
		print we_html_element::jsElement('makefolderState=' . $makefolderState . ';');
		print we_button::create_button("image:btn_new_dir", "javascript:if(makefolderState==1){top.drawNewFolder();}", true, -1, 22, "", "", $makefolderState ? false : true);
		print '               </td>
';
	}

	function printFramesetJSFunctioWriteBody(){
		$htmltop = preg_replace("/[[:cntrl:]]/", "", trim(str_replace("'", "\\'", we_html_tools::getHtmlTop())));
		$htmltop = str_replace('script', "scr' + 'ipt", $htmltop);
		?>

		function writeBody(d){
		d.open();
		//d.writeln('<?php print $htmltop; ?>'); Geht nicht im IE
		d.writeln('<?php print we_html_element::htmlDocType(); ?><html><head><title>webEdition</title><meta http-equiv="expires" content="0"><meta http-equiv="pragma" content="no-cache"><?php echo we_html_tools::htmlMetaCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);?><meta http-equiv="imagetoolbar" content="no"><meta name="generator" content="webEdition">');
				d.writeln('<?php print STYLESHEET_SCRIPT; ?>');
				d.writeln('</head>');
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
				d.writeln('document.onclick = weonclick;');
				d.writeln('function weonclick(e){');
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
			d.writeln('<body bgcolor="white" LINK="#000000" ALINK="#000000" VLINK="#000000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">');
				d.writeln('<form name="we_form" target="fscmd" action="<?php print $_SERVER["SCRIPT_NAME"]; ?>">');
					if(top.we_editDirID){
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
							d.writeln('<td align="center"><img src="<?php print ICON_DIR.we_base_ContentTypes::FOLDER_ICON; ?>" width="16" height="18" border="0" /></td>');
							d.writeln('<td><input type="hidden" name="we_FolderText" value="<?php print g_l('export', "[newFolder]") ?>" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="<?php print g_l('export', "[newFolder]") ?>"  class="wetextinput" onBlur="this.className=\'wetextinput\';" onFocus="this.className=\'wetextinputselected\'" style="width:100%" /></td>');
							d.writeln('</tr>');
						}
						for(i=0;i < entries.length; i++){
						var onclick = ' onClick="weonclick(<?php echo (we_base_browserDetect::isIE() ? "this" : "event") ?>);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick('+entries[i].ID+',0);}else{top.wasdblclick=0;}\',300);return true"';
						var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick('+entries[i].ID+',1);return true;"';
						d.writeln('<tr id="line_'+entries[i].ID+'" style="' + ((entries[i].ID == top.currentID && (!makeNewFolder) )  ? 'background-color:#DFE9F5;' : '')+'cursor:pointer;'+((we_editDirID != entries[i].ID) ? '' : '' )+'"'+((we_editDirID || makeNewFolder) ? '' : onclick)+ (entries[i].isFolder ? ondblclick : '') + ' >');
													 d.writeln('<td class="selector" width="25" align="center">');
								d.writeln('<img src="<?php print ICON_DIR; ?>'+entries[i].icon+'" width="16" height="18" border="0" />');
								d.writeln('</td>');
							if(we_editDirID == entries[i].ID){
							d.writeln('<td class="selector">');
								d.writeln('<input type="hidden" name="we_FolderText" value="'+entries[i].text+'" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="'+entries[i].text+'" class="wetextinput" onBlur="this.className=\'wetextinput\';" onFocus="this.className=\'wetextinputselected\'" style="width:100%" />');
								}else{
								d.writeln('<td class="selector" style="" >');
								d.writeln(cutText(entries[i].text,24));
								}
								d.writeln('</td>');
							d.writeln('</tr><tr><td colspan="3"><?php print we_html_tools::getPixel(2, 1); ?></td></tr>');
						}
						d.writeln('<tr>');
							d.writeln('<td width="25"><?php print we_html_tools::getPixel(25, 2) ?></td>');
							d.writeln('<td><?php print we_html_tools::getPixel(200, 2) ?></td>');
							d.writeln('</tr>');
						d.writeln('</table></form>');
				if(makeNewFolder || top.we_editDirID){
				d.writeln('<scr'+'ipt type="text/javascript">document.we_form.we_FolderText_tmp.focus();document.we_form.we_FolderText_tmp.select();</scr'+'ipt>');
			}
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

		function entry(ID,icon,text,isFolder,path){
		this.ID=ID;
		this.icon=icon;
		this.text=text;
		this.isFolder=isFolder;
		this.path=path;
		}

		<?php
	}

	function printFramesetJSFunctionAddEntry(){
		?>

		function addEntry(ID,icon,text,isFolder,path){
		entries[entries.length] = new entry(ID,icon,text,isFolder,path);
		}

		<?php
	}

	function printFramesetJSFunctionAddEntries(){
		while($this->next_record()) {
			print 'addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '");' . "\n";
		}
	}

	function printCmdAddEntriesHTML(){
		$this->query();
		while($this->next_record()) {
			print 'top.addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '");' . "\n";
		}
	}

	function printCreateFolderHTML(){
		we_html_tools::htmlTop();
		we_html_tools::protect();

		print '<script>
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = '';
		if(isset($_REQUEST['we_FolderText_tmp'])){
			$txt = rawurldecode($_REQUEST['we_FolderText_tmp']);
		}
		if($txt == ""){
			print we_message_reporting::getShowMessageCall(g_l('export', "[wrongtext]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$folder = new we_folder();
			$folder->we_new();
			$folder->setParentID($this->dir);
			$folder->Table = $this->table;
			$folder->Icon = we_base_ContentTypes::FOLDER_ICON;
			$folder->Text = $txt;
			$folder->Path = $folder->getPath();
			$this->db->query("SELECT ID FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($folder->Path) . "'");
			if($this->db->next_record()){
				print we_message_reporting::getShowMessageCall(g_l('export', "[folder_path_exists]"), we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(weExport::filenameNotValid($folder->Text)){
					print we_message_reporting::getShowMessageCall(g_l('export', "[wrongtext]"), we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					$folder->we_save();
					print 'var ref;
if(top.opener.top.content.makeNewEntry){
	ref = top.opener.top.content;
	ref.makeNewEntry("'.we_base_ContentTypes::FOLDER_ICON.'",' . $folder->ID . ',"' . $folder->ParentID . '","' . $txt . '",1,"folder","' . $this->table . '",1);
}
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

	function query(){
		$this->db->query("SELECT " . $this->db->escape($this->fields) . " FROM " .
			$this->db->escape($this->table) .
			" WHERE IsFolder=1 AND ParentID='" . (is_null($this->dir) ? $this->dir : $this->db->affected_rows()) . "'");
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
			print we_message_reporting::getShowMessageCall(g_l('export', "[folder_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->Filename = $txt;
			$folder->Path = $folder->getPath();
			$this->db->query("SELECT ID,Text FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($folder->Path) . "' AND ID != " . intval($this->we_editDirID));
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('export', "[folder_exists]"), $folder->Path);
				print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(preg_match('/[%/\\"\']/', $folder->Text)){
					$we_responseText = g_l('export', "[wrongtext]");
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					if(f("SELECT Text FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->we_editDirID), "Text", $this->db) != $txt){
						$folder->we_save();
						print 'var ref;
if(top.opener.top.content.updateEntry){
	ref = top.opener.top.content;
	ref.updateEntry(' . $folder->ID . ',"' . $txt . '","' . $folder->ParentID . '");
}
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

}
