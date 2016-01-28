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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

/**
 * base class for rpc commands
 *
 * @package none
 * @abstract
 */
class rpcCmd{

	const STATUS_OK = 0;
	const STATUS_NO_PERMISSION = 1;
	const STATUS_NOT_ALLOWED_VIEW = 2;
	const STATUS_LOGIN_FAILED = 3;
	const STATUS_REQUEST_MALFORMED = 4;
	const STATUS_NO_CMD = 5;
	const STATUS_NO_CIEW = 6;
	const STATUS_NO_SESSION = 7;
	const STATUS_NO_VIEW = 8;

	protected $CmdShell;
	protected $ExtraViews = array();
	protected $Permissions = array();
	protected $Status = self::STATUS_OK;
	protected $Parameters = array();

	function executeRpcCmd($shell){
		$this->checkSession();
		$this->checkParameters();

		foreach($this->Permissions as $perm){
			if(!permissionhandler::hasPerm($perm)){
				$this->Status = self::STATUS_NO_PERMISSION;
			}
		}

		$this->CmdShell = $shell;
	}

	function execute(){
		return new rpcResponse();
	}

	function checkSession(){
		if(!isset($_SESSION['user']['ID']) || !$_SESSION['user']['ID']){
			$this->Status = self::STATUS_NO_SESSION;
			return false;
		}

		return true;
	}

	function checkParameters(){
		foreach($this->Parameters as $par){
			if(we_base_request::_(we_base_request::STRING, $par) === false){
				$this->Status = self::STATUS_REQUEST_MALFORMED;
				return false;
			}
		}
		return true;
	}

	public function getStatus(){
		return $this->Status;
	}
}
