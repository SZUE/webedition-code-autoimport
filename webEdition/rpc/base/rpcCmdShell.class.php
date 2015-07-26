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
require_once('base/rpcCmd.class.php');
require_once('base/rpcResponse.class.php');
require_once('base/rpcView.class.php');
require_once('base/rpcJsonView.class.php');

class rpcCmdShell{
	protected $Protocol;
	protected $Cmd;
	protected $View;
	protected $Response;
	protected $Status = rpcCmd::STATUS_OK;

	public function __construct(&$cmd, $protocol){

		$this->Protocol = $protocol;
		$this->Cmd = $this->createCmd($cmd);

		if(($view = we_base_request::_(we_base_request::STRING, 'view'))){
			if(!$this->isViewAllowed($view)){
				$this->Status = rpcCmd::STATUS_NOT_ALLOWED_VIEW;
			}
		} else {
			$cmd['view'] = $this->CmdName;
		}
		if($this->Status == rpcCmd::STATUS_OK){
			$this->View = $this->getView($cmd);
		}
	}

	private function createCmd(&$cmd){
		$this->CmdName = $cmd['cmd'];
		$_classname = 'rpc' . $cmd['cmd'] . 'Cmd';

		$_namespace = '/' . (isset($cmd['cns']) ? $cmd['cns'] . '/' : '');

		$_cmdfile = (isset($cmd['tool']) && we_tool_lookup::isTool($cmd['tool']) ?
				we_tool_lookup::getCmdInclude($_namespace, $cmd['tool'], $this->CmdName) :
				'cmds' . $_namespace . $_classname . '.class.php');

		if(include_once($_cmdfile)){
			$_obj = new $_classname($this);
			$_obj->executeRpcCmd($this);

			$this->Status = $_obj->getStatus();

			return $_obj;
		}
		$this->Status = rpcCmd::STATUS_NO_CMD;

		return null;
	}

	function getView(&$cmd){
		$_classname = 'rpc' . $cmd["view"] . 'View';
		$namespace = '/' . (isset($cmd['vns']) ? $cmd['vns'] . '/' : (isset($cmd['cns']) ? $cmd['cns'] . '/' : ''));

		$_viewfile = (isset($cmd['tool']) && we_tool_lookup::isTool($cmd['tool']) ?
				we_tool_lookup::getViewInclude($this->Protocol, $namespace, $cmd['tool'], $cmd["view"]) :
				'views/' . $this->Protocol . $namespace . $_classname . '.class.php');
		if(@include_once($_viewfile)){

			$_obj = new $_classname();
			$_obj->setCmdShell($this);

			return $_obj;
		}
		require_once('views/json/rpcGenericJSONView.class.php');
		$_obj = new rpcGenericJSONView();
		$_obj->setCmdShell($this);
		return $_obj;
	}

	function setView(&$cmd){
		$this->View = $this->getView($cmd);
	}

	function isViewAllowed($view){
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
		$_View = $this->getView($cmd);
		return $_View->getResponse($this->executeInternalCmd($cmd));
	}

	function getErrorOut(){
		switch($this->Status){
			case rpcCmd::STATUS_NO_CMD :
				return 'ERROR: No command defined!';
			case rpcCmd::STATUS_NO_VIEW :
				return 'ERROR: No view defined!';
			case rpcCmd::STATUS_NO_SESSION :
				return 'ERROR: No session exists!';
			default:
				return 'ERROR';
		}
	}

	function getStatus(){
		return $this->Status;
	}

}
