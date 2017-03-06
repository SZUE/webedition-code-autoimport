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
		$this->fields = 'ID,ParentID,Text,Path,IsFolder,IF(IsFolder,"folder","we/banner") AS ContentType';
		parent::__construct($id, BANNER_TABLE, $JSIDName, $JSTextName, $JSCommand, $order);
		$this->title = g_l('fileselector', '[bannerSelector][title]');
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() . we_html_element::jsScript(JS_DIR . 'selectors/banner_selector.js');
	}

	protected function printHeaderHeadlines(){
		return '
<table class="headerLines" style="width:550px;">
<colgroup><col style="width:25px;"/><col style="width:200px;"/><col style="width:300px;"/></colgroup>
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector"colspan="2"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('modules_banner', '[name]') . '</a></th>
	</tr>
</table>';
	}

	function printSetDirHTML(we_base_jsCmd $weCmd){
		$weCmd->addCmd('clearEntries');
		$weCmd->addCmd('updateSelectData', [
			'currentDir' => $this->dir,
			'parentID' => $this->values["ParentID"]
		]);
		$this->printCmdAddEntriesHTML($weCmd);
		$this->setSelectorData($weCmd);

		$weCmd->addCmd('setButtons', [['RootDirButs', intval($this->dir) !== 0]]);
		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds(), we_html_element::htmlBody());
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
	}

	public function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		switch($what){
			case self::SETDIR:
				$jsCmd = new we_base_jsCmd();
				$this->printSetDirHTML($jsCmd);
				break;
			default:
				parent::printHTML($what);
		}
	}

}
