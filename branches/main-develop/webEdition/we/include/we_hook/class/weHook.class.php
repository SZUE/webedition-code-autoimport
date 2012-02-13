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
include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/conf/we_conf_global.inc.php");

/**
 * class to handle hooks in webEdition and in applications
 */
class weHook{

	protected $action;
	protected $appName;
	protected $param;
	private $file = '';
	private $func;
	private $errStr = '';

	function __construct($action, $appName='', $param=array()){
		if(!(defined('EXECUTE_HOOKS') && EXECUTE_HOOKS)){
			return;
		}
		$this->action = $action;
		$this->appName = $appName;
		$this->param = $param;
		$this->param['hookHandler'] = $this;
		$this->findHookFile();
		$this->func = 'weCustomHook_' . ($appName != '' ? $appName . '_' : '') . $action;
	}

	function executeHook(){
		if(!(defined('EXECUTE_HOOKS') && EXECUTE_HOOKS)){
			return true;
		}

		if($this->action != '' && is_array($this->param) && $this->file != ''){
			include_once($this->file);
			if(function_exists($this->func)){
				$f = $this->func;
				$f($this->param);
				return ($this->errStr == '');
			}
		}
		return true;
	}

	/**
	 * get custom hook file
	 *
	 * @param string $action
	 * @param string $appName
	 *
	 * return string
	 */
	function findHookFile(){
		$hookFile = '';

		if($this->appName != ''){
			$filename = 'weCustomHook_' . $this->appName . '_' . $this->action . '.inc.php';
			// look in app folder
			$hookFile = WE_TOOLS_DIR . $this->appName . '/hook/custom_hooks/' . $filename;
		} else{
			$filename = 'weCustomHook_' . $this->action . '.inc.php';
			// look in we_hook/custom_hooks folder
			$hookFile = WEBEDITION_INCLUDES_DIR . 'we_hook/custom_hooks/' . $filename;
			//no more check for sample hooks - they are overwritten on update
		}
		if(file_exists($hookFile) && is_readable($hookFile)){
			$this->file = $hookFile;
		}
	}

	function setErrorString($str){
		$this->errStr = $str;
	}

	function getErrorString(){
		return $this->errStr;
	}

}