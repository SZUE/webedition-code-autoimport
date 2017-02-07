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
 * @abstract This class executes commands and provides views depending on requesting client.
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_rpc_cmdShell{
	protected $Protocol;
	protected $Cmd;
	protected $View;
	protected $Response;
	protected $Status = we_rpc_cmd::STATUS_OK;

	public function __construct($protocol){
		$this->Protocol = $protocol;
		$this->Cmd = $this->createCmd();

		if(($view = we_base_request::_(we_base_request::STRING, 'view'))){
			if(!$this->isViewAllowed($view)){
				$this->Status = we_rpc_cmd::STATUS_NOT_ALLOWED_VIEW;
			}
		} else {
			$view = $this->CmdName;
		}
		if($this->Status == we_rpc_cmd::STATUS_OK){
			$this->View = $this->getView($view);
		}
	}

	private function createCmd(){
		$this->CmdName = we_base_request::_(we_base_request::STRING, 'cmd');
		$classname = 'rpc' . $this->CmdName . 'Cmd';

		$namespace = we_base_request::_(we_base_request::STRING, 'cns', '');
		$namespace = '/' . ($namespace ? $namespace . '/' : '');

		$tool = we_base_request::_(we_base_request::STRING, 'tool');

		$cmdfile = ($tool && we_tool_lookup::isTool($tool) ?
			we_tool_lookup::getCmdInclude($namespace, $tool, $this->CmdName) :
			'cmds' . $namespace . $classname . '.class.php');

		if($cmdfile && include_once($cmdfile)){
			$obj = new $classname($this);
			$obj->executeRpcCmd($this);

			$this->Status = $obj->getStatus();

			return $obj;
		}
		$this->Status = we_rpc_cmd::STATUS_NO_CMD;

		return null;
	}

	function getView($view){
		$classname = 'rpc' . $view . 'View';
		$tool = we_base_request::_(we_base_request::STRING, 'tool');
		$vns = we_base_request::_(we_base_request::STRING, 'vns');
		$cns = we_base_request::_(we_base_request::STRING, 'cns');
		$namespace = '/' . ($vns ? $vns . '/' : ($cns ? $cns . '/' : ''));

		$viewfile = ($tool && we_tool_lookup::isTool($tool) ?
			we_tool_lookup::getViewInclude($this->Protocol, $namespace, $tool, $view) :
			'views/' . $this->Protocol . $namespace . $classname . '.class.php');
		if($viewfile && include_once($viewfile)){
			return new $classname($this, $this->Protocol);
		}
		return new we_rpc_genericJSONView($this, $this->Protocol);
	}

	private function isViewAllowed($view){
		if($view == $this->CmdName){
			return true;
		}

		if($this->Cmd->ExtraViews){
			return in_array($view, $this->Cmd->ExtraViews);
		}

		return false;
	}

	function executeCommand(){
		$this->Response = $this->Cmd->execute();
	}

	function getResponse(){
		return $this->View->getResponse($this->Response);
	}

	function executeInternalCmd(&$cmd){
		$cmd = $this->createCmd($cmd);
		return $cmd->execute();
	}

	function getInternalView($cmd){
		$View = $this->getView($cmd);
		return $View->getResponse($this->executeInternalCmd($cmd));
	}

	public function getErrorOut(){
		switch($this->Status){
			case we_rpc_cmd::STATUS_NO_CMD:
				return 'ERROR: No command defined!';
			case we_rpc_cmd::STATUS_NO_VIEW:
				return 'ERROR: No view defined!';
			case we_rpc_cmd::STATUS_NO_SESSION:
				return 'ERROR: No session exists!';
			default:
				return 'ERROR';
		}
	}

	function getStatus(){
		return $this->Status;
	}

}
