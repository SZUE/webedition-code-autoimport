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
/* * Class for external JS commands
 * dynamic generated content is handled as a command dynamic paramters can be passed.
 * we_processCmd.js is responsible for the correct commands to be executed
 */
class we_base_jsCmd{
	private $cmds = [];
	private $cmdData = [];

	public function addCmd($cmd, $data = ''){
		$this->cmds[] = $cmd;
		$this->cmdData[] = $data ? setDynamicVar($data) : '';
	}

	public function getCmds(){
		if(empty($this->cmds)){
			return '';
		}
		$attrs = ['id' => 'loadVarCmd', 'data-cmds' => implode(',', $this->cmds)];
		foreach($this->cmdData as $pos => $cur){
			if($cur){
				$attrs['data-cmd' . $pos] = $cur;
			}
		}

		return we_html_element::jsScript(JS_DIR . 'we_processCmd.js', '', $attrs);
	}

	public static function singleCmd($cmd, $data){
		$tmp = new self();
		$tmp->addCmd($cmd, $data);
		return $tmp->getCmds();
	}

}
