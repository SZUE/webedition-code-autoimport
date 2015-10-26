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
class we_fragment_copyFolderFinish extends we_fragment_copyFolder{

	function init(){
		if(isset($_SESSION['weS']['WE_CREATE_TEMPLATE'])){
			$this->alldata = array();
			foreach($_SESSION['weS']['WE_CREATE_TEMPLATE'] as $id){
				$this->alldata[] = $id;
			}
			unset($_SESSION['weS']['WE_CREATE_TEMPLATE']);
		}
	}

	function doTask(){
		if($this->correctTemplate()){

			$pbText = sprintf(
				g_l('copyFolder', '[correctTemplate]'), basename(id_to_path($this->data, TEMPLATES_TABLE)));

			echo we_html_element::jsElement(
				'parent.document.getElementById("pbTd").style.display="block";parent.setProgress(' . ((int) ((100 / count(
					$this->alldata)) * ($this->currentTask + 1))) . ');parent.setProgressText("pbar1","' . addslashes(
					$pbText) . '");');
			flush();
		} else {
			exit("Error correcting Template with id: " . $this->data);
		}
	}

	private function correctTemplate(){
		$templ = new we_template();

		$templ->initByID($this->data, TEMPLATES_TABLE);
		$content = $templ->getElement("data");

		if(preg_match_all('/##WEPATH##([^ ]+) ###WEPATH###/i', $content, $regs, PREG_SET_ORDER)){
			foreach($regs as $cur){
				$path = $cur[1];
				$id = path_to_id($path, FILE_TABLE, $GLOBALS['DB_WE']);
				$content = str_replace('##WEPATH##' . $path . ' ###WEPATH###', $id, $content);
			}
		}
		$templ->elements["data"]["dat"] = $content;
		return $templ->we_save();
	}

	function finish(){
		if(isset($_SESSION['weS']['WE_CREATE_TEMPLATE'])){
			unset($_SESSION['weS']['WE_CREATE_TEMPLATE']);
		}
		echo we_html_element::jsElement(
			'top.opener.top.we_cmd("load","' . FILE_TABLE . '");
				WE().util.showMessage(WE().consts.g_l.main.folder_copy_success, WE().consts.message.WE_MESSAGE_NOTICE, window);
				top.close();');
	}

	function printHeader(){
		echo we_html_tools::getHtmlTop(g_l('copyFolder', '[headline]'), STYLESHEET);
	}

}
