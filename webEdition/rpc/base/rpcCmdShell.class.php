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
 * @package    webEdition_rpc
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

require_once("base/rpcCmd.class.php");
require_once("base/rpcResponse.class.php");
require_once("base/rpcView.class.php");
require_once("base/rpcJsonView.class.php");


class rpcCmdShell {

	var $Protocol = array();

	var $Cmd;

	var $View;

	var $Response;

	var $Status = rpcCmd::STATUS_OK;

	function __construct(&$cmd,$protocol) {

		$this->Protocol = $protocol;
		$this->Cmd = $this->createCmd($cmd);

		if(isset($_REQUEST['view'])) {

			if(!$this->isViewAllowed($_REQUEST['view'])) {

				$this->Status = rpcCmd::STATUS_NOT_ALLOWED_VIEW;

			}

		} else {

			$cmd['view'] = $this->CmdName;

		}

		if($this->Status == rpcCmd::STATUS_OK) {

			$this->View = $this->getView($cmd);

		}

	}

	function createCmd(&$cmd) {

		$this->CmdName = $cmd['cmd'];

		$_classname = 'rpc' . $cmd['cmd'] . 'Cmd';

		if (isset($cmd['cns'])) {
			$_namespace = "/{$cmd['cns']}/";
		} else {
			$_namespace = '/';
		}

		if(isset($cmd['tool']) && weToolLookup::isTool($cmd['tool'])) {
			$_cmdfile = weToolLookup::getCmdInclude($_namespace,$cmd['tool'],$this->CmdName);
		} else {
			$_cmdfile = 'cmds' . $_namespace . $_classname . '.class.php';
		}
		if (include_once($_cmdfile)) {

			$_obj = new $_classname($this);
			$_obj->rpcCmd($this);

			$this->Status = $_obj->Status;

			return $_obj;

		} else {

			$this->Status = rpcCmd::STATUS_NO_CMD;

		}

		return null;

	}

	function getView(&$cmd) {

		$_classname = "rpc{$cmd["view"]}View";


		if (isset($cmd['vns'])) {
			$namespace = "/{$cmd['vns']}/";
		} else if (isset($cmd['cns'])) {
			$namespace = "/{$cmd['cns']}/";
		} else {
			$namespace = '/';
		}

		if(isset($cmd['tool']) && weToolLookup::isTool($cmd['tool'])) {
			$_viewfile = weToolLookup::getViewInclude($this->Protocol,$namespace,$cmd['tool'],$cmd["view"]);
		} else {
			$_viewfile = 'views/' . $this->Protocol . $namespace . $_classname . '.class.php';
		}
		if ( @include_once($_viewfile) ) {

			$_obj  = new $_classname();
			$_obj->setCmdShell($this);

			return $_obj;

		}else {
			include_once('views/json/rpcGenericJSONView.class.php');
			$_obj  = new rpcGenericJSONView();
			$_obj->setCmdShell($this);
			return $_obj;

		}

		return null;
	}


	function setView(&$cmd) {

		$this->View = $this->getView($cmd);
	}

	function isViewAllowed($view) {

		if ($view == $this->CmdName) {
			return true;
		}

		if (count($this->Cmd->ExtraViews)){
			return in_array($view,$this->Cmd->ExtraViews);
		}

		return false;
	}


	function executeCommand(){
         $this->Response = $this->Cmd->execute();
	}


	function getResponse(){
		return $this->View->getResponse($this->Response);
	}

	function executeInternalCmd(&$cmd) {

		$cmd = $this->createCmd($cmd);
		return $cmd->execute();
	}

	function getInternalView($cmd) {

		$_View = $this->getView($cmd);
		return $_View->getResponse($this->executeInternalCmd($cmd));
	}

	function getErrorOut() {

		switch ($this->Status) {

			case rpcCmd::STATUS_NO_CMD :
				return 'ERROR: No command defined!';
			break;

			case rpcCmd::STATUS_NO_VIEW :
				return 'ERROR: No view defined!';
			break;

			case rpcCmd::STATUS_NO_SESSION :
				return 'ERROR: No session exists!';
			break;

		}

		return 'ERROR';
	}

	function getStatus() {
		return $this->Status;
	}

}
