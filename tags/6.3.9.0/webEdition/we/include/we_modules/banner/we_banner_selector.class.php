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
class we_banner_selector extends we_selector_multiple{

	function __construct($id, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = ""){
		parent::__construct($id, BANNER_TABLE, $JSIDName, $JSTextName, $JSCommand, $order);
		$this->title = g_l('fileselector', '[bannerSelector][title]');
	}

	protected function printFramesetJSDoClickFn(){
		return we_html_element::jsElement('
function doClick(id,ct){
	if(ct==1){
		if(wasdblclick){
			setDir(id);
			setTimeout("wasdblclick=0;",400);
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
}');
	}

	function printHeaderHeadlines(){
		return '
<table border="0" cellpadding="0" cellspacing="0" width="550">
	<tr>
		<td>' . we_html_tools::getPixel(25, 14) . '</td>
		<td class="selector"colspan="2"><b><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('modules_banner', '[name]') . '</a></b></td>
	</tr>
	<tr>
		<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
		<td width="200">' . we_html_tools::getPixel(200, 1) . '</td>
		<td width="300">' . we_html_tools::getPixel(300, 1) . '</td>
	</tr>
</table>';
	}

	function printFramesetJSsetDir(){

		return we_html_element::jsElement('
function setDir(id){
	top.fscmd.location.replace(top.queryString(' . we_selector_multiple::SETDIR . ',id));
}');
	}

	function printSetDirHTML(){

		print we_html_element::jsElement('
top.clearEntries();' .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() . '
top.fsheader.' . (intval($this->dir) == 0 ? 'disable' : 'enable') . 'RootDirButs();
top.currentDir = "' . $this->dir . '";
top.parentID = "' . $this->values["ParentID"] . '";');
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
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
			case we_selector_multiple::SETDIR:
				$this->printSetDirHTML();
				break;
			case we_selector_file::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

}
