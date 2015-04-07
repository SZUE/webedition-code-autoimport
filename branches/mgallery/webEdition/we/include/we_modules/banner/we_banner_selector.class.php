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
class we_banner_selector extends we_selector_file{

	function __construct($id, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = ""){
		parent::__construct($id, BANNER_TABLE, $JSIDName, $JSTextName, $JSCommand, $order);
		$this->title = g_l('fileselector', '[bannerSelector][title]');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .we_html_element::jsScript(JS_DIR . 'selectors/banner_selector.js');
	}

	function printHeaderHeadlines(){
		return '
<table class="headerLines" width="550">
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector"colspan="2"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('modules_banner', '[name]') . '</a></th>
	</tr>
	<tr>
		<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
		<td width="200">' . we_html_tools::getPixel(200, 1) . '</td>
		<td width="300">' . we_html_tools::getPixel(300, 1) . '</td>
	</tr>
</table>';
	}

	function printSetDirHTML(){

		echo we_html_element::jsElement('
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
			case self::SETDIR:
				$this->printSetDirHTML();
				break;
			case self::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

}
