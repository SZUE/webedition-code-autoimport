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
	private static $count = 0;
	//for debug only
	private static $traces = [];
	private static $active = null;

	public function __construct(){
		self::$traces[] = getBacktrace(['getBacktrace'])[0];
		self::$count++;
		self::$active = $this;
	}

	public function addCmd($cmd, $data = ''){
		$values = func_get_args();
		if(count($values) <= 2){
			$this->cmds[] = $cmd;
			$this->cmdData[] = $data !== '' ? setDynamicVar($data) : '';
		} else {
			//all data is called as we_cmd(cmd,p1,p2,p3)
			$this->cmds[] = 'we_cmd';
			$this->cmdData[] = setDynamicVar($values);
		}
	}

	public function addMsg($message, $priority){
		$this->cmd[] = 'msg';
		$this->cmdData[] = setDynamicVar(['msg' => $message, 'prio' => $priority]);
	}

	public function getCmds(){
		self::$active = null;
		if(empty($this->cmds)){
			self::$count--;
			return '';
		}
		$attrs = ['id' => 'loadVarCmd', 'data-cmds' => implode(',', $this->cmds)];
		foreach($this->cmdData as $pos => $cur){
			if($cur){
				$attrs['data-cmd' . $pos] = $cur;
			}
		}

		$this->cmds = $this->cmdData = [];
		self::$traces[] = getBacktrace(['getBacktrace'])[0];
		if(self::$count > 1){
			t_e('possible JS error will arrise', self::$traces);
		}

		return we_html_element::jsScript(JS_DIR . 'we_processCmd.js', '', $attrs);
	}

	/* all args passed to addCmd
	 */

	public static function singleCmd(){
		//FIXME: active is temporary
		if(self::$active){
			//make sure we change this!
			self::$count++;
		}
		$tmp = self::$active ?: new self();
		$values = func_get_args();
		call_user_func_array([$tmp, 'addCmd'], $values);
		//FIXME: this is not safe at all!
		return $tmp->getCmds();
	}

}
