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
class we_shopDirSelector extends we_multiSelector{

	var $fields = "ID,ParentID,Text,Path,IsFolder,Icon";

	function __construct($id, $JSIDName="", $JSTextName="", $JSCommand="", $order=""){

		parent::__construct($id, BANNER_TABLE, $JSIDName, $JSTextName, $JSCommand, $order);
	}

	function printFramesetJSDoClickFn(){
		?>
		function doClick(id,ct){
		if(ct==1){
		if(wasdblclick){
		setDir(id);
		setTimeout('wasdblclick=0;',400);
		}
		}else{
		e=top.getEntry(id);
		if(e.isFolder){
		if(top.currentID == id){
		top.RenameFolder(id);
		}
		}else{
		selectFile(id);
		}
		}
		}
		<?php
	}

	function printHeaderHeadlines(){
		print '			<table border="0" cellpadding="0" cellspacing="0" width="550">
				<tr>
					<td>' . we_html_tools::getPixel(25, 14) . '</td>
					<td class="selector"colspan="2"><b><a href="#" onclick="javascript:top.orderIt(\'IsFolder DESC, Text\');">' . g_l('modules_banner', '[name]') . '</a></b></td>
				</tr>
				<tr>
					<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
					<td width="200">' . we_html_tools::getPixel(200, 1) . '</td>
					<td width="300">' . we_html_tools::getPixel(300, 1) . '</td>
				</tr>
			</table>
';
	}

	function printFramesetJSsetDir(){
		?>
		function setDir(id){
		top.fscmd.location.replace(top.queryString(<?php print we_multiSelector::SETDIR; ?>,id));
		}


		<?php
	}

	function printSetDirHTML(){
		print '<script>
top.clearEntries();
';
		$this->printCmdAddEntriesHTML();
		$this->printCMDWriteAndFillSelectorHTML();

		if(intval($this->dir) == 0){
			print 'top.fsheader.disableRootDirButs();
';
		} else{
			print 'top.fsheader.enableRootDirButs();
';
		}
		print 'top.currentDir = "' . $this->dir . '";
top.parentID = "' . $this->values["ParentID"] . '";
</script>
';
		$GLOBALS["we_fs_lastDir"][$this->table] = $this->dir;
	}

	function printHTML($what=we_fileselector::FRAMESET){
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
			case we_multiSelector::SETDIR:
				$this->printSetDirHTML();
				break;
			case we_fileselector::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

}
?>