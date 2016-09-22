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
class we_rebuild_fragment extends we_fragment_base{

	function doTask(){
		$this->updateProgressBar();
		we_rebuild_base::rebuild($this->data);
	}

	protected function updateTaskPerFragment(){
		switch($this->alldata[$this->currentTask]['cn']){
			case 'we_folder':
				$this->taskPerFragment = max(20, $this->taskPerFragment);
				break;
			case 'we_template':
				$this->taskPerFragment = max(10, $this->taskPerFragment);
				break;
		}
		$type = $this->alldata[$this->currentTask]['cn'];
		for($i = 0; $i < $this->taskPerFragment; $i++){
			if(!isset($this->alldata[$i + $this->currentTask]) || $type != $this->alldata[$i + $this->currentTask]['cn']){
				$this->taskPerFragment = max($i, 1);
				return;
			}
		}
	}

	function updateProgressBar(){
		$percent = round((100 / count($this->alldata)) * (1 + $this->currentTask));
		echo we_html_element::jsElement('if(parent.wizbusy.document.getElementById("progr")){
	parent.wizbusy.document.getElementById("progr").style.display="";
};
parent.wizbusy.setProgressText("pb1",(parent.wizbusy.document.getElementById("progr") ? "' . addslashes(we_base_util::shortenPath($this->data["path"], 33)) . '" : "' . g_l('rebuild', '[savingDocument]') . addslashes(we_base_util::shortenPath($this->data["path"], 60)) . '") );
parent.wizbusy.setProgress("",' . $percent . ');');
		flush();
	}

	function finish(){
		$responseText = we_base_request::_(we_base_request::STRING, 'responseText', '');
		$cmd = new we_base_jsCmd();
		$cmd->addCmd('msg', ['msg' => addslashes($responseText ? : g_l('rebuild', '[finished]')), 'prio' => we_message_reporting::WE_MESSAGE_NOTICE]);
		$cmd->addCmd('close');
		echo $cmd->getCmds();
	}

	function printBodyTag(array $attributes = []){
		//note we need to subtract this, since it was already added in constructor loop.
		//$this->currentTask = $this->currentTask - $this->taskPerFragment + 1;
		parent::printBodyTag($attributes);
	}

}
