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

	protected function init(){
		if(isset($_SESSION['weS']['WE_CREATE_TEMPLATE'])){
			$this->alldata = [];
			foreach($_SESSION['weS']['WE_CREATE_TEMPLATE'] as $id){
				$this->alldata[] = $id;
			}
			unset($_SESSION['weS']['WE_CREATE_TEMPLATE']);
		}
	}

	protected function doTask(){
		if(!$this->correctTemplate()){
			t_e("Error correcting Template with id: " . $this->data);
			exit("Error correcting Template with id: " . $this->data);
		}
	}

	protected function updateProgressBar(we_base_jsCmd $jsCmd){
		$jsCmd->addCmd('setProgress', [
			'progress' => ((int) ((100 / count($this->alldata)) * (1 + $this->currentTask))),
			'name' => 'pbar1',
			'text' => sprintf(g_l('copyFolder', '[correctTemplate]'), basename(id_to_path($this->data, TEMPLATES_TABLE)))
		]);
	}

	private function correctTemplate(){
		$templ = new we_template();

		$templ->initByID($this->data, TEMPLATES_TABLE);
		$content = $templ->getElement('data');
		$regs = [];
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

	protected function finish(we_base_jsCmd $jsCmd){
		if(isset($_SESSION['weS']['WE_CREATE_TEMPLATE'])){
			unset($_SESSION['weS']['WE_CREATE_TEMPLATE']);
		}
		$jsCmd->addCmd('we_cmd', ['load', FILE_TABLE]);
		$jsCmd->addMsg(g_l('copyFolder', '[copy_success]'), we_base_util::WE_MESSAGE_NOTICE);
		$jsCmd->addCmd('close');
	}

}
