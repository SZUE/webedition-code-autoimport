<?php

/**
 * webEdition CMS
 *
 * $Rev: 3541 $
 * $Author: mokraemer $
 * $Date: 2011-12-11 21:18:24 +0100 (So, 11. Dez 2011) $
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

class copyFolderFinishFrag extends copyFolderFrag{

	function init(){
		if(isset($_SESSION["WE_CREATE_TEMPLATE"])){
			$this->alldata = array();
			foreach($_SESSION["WE_CREATE_TEMPLATE"] as $id){
				array_push($this->alldata, $id);
			}
			unset($_SESSION["WE_CREATE_TEMPLATE"]);
		}
	}

	function doTask(){
		if($this->correctTemplate()){

			$pbText = sprintf(
				g_l('copyFolder', "[correctTemplate]"), basename(id_to_path($this->data, TEMPLATES_TABLE)));

			print we_htmlElement::jsElement(
				'parent.document.getElementById("pbTd").style.display="block";parent.setProgress(' . ((int) ((100 / count(
					$this->alldata)) * ($this->currentTask + 1))) . ');parent.setProgressText("pbar1","' . addslashes(
					$pbText) . '");');
			flush();
		} else{
			exit("Error correctiing Template with id: " . $this->data);
		}
	}

	function correctTemplate(){
		$templ = new we_template();
		;
		$templ->initByID($this->data, TEMPLATES_TABLE);
		$content = $templ->elements["data"]["dat"];

		if(preg_match_all('/##WEPATH##([^ ]+) ###WEPATH###/i', $content, $regs, PREG_SET_ORDER)){
			for($i = 0; $i < sizeof($regs); $i++){
				$path = $regs[$i][1];
				$id = $this->getID($path, $GLOBALS['DB_WE']);
				$content = str_replace('##WEPATH##' . $path . ' ###WEPATH###', $id, $content);
			}
		}
		$templ->elements["data"]["dat"] = $content;
		return $templ->we_save();
	}

	function finish(){

		if(isset($_SESSION["WE_CREATE_TEMPLATE"])){
			unset($_SESSION["WE_CREATE_TEMPLATE"]);
		}
		print we_htmlElement::jsElement(
			'top.opener.top.we_cmd("load","' . FILE_TABLE . '");' . we_message_reporting::getShowMessageCall(
				g_l('copyFolder', "[copy_success]"), WE_MESSAGE_NOTICE) . 'top.close();');
	}

	function printHeader(){
		we_html_tools::htmlTop(g_l('copyFolder', "[headline]"));
		print STYLESHEET;
	}

}
