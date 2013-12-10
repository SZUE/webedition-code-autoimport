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
 * @package    webEdition_rpc
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
if(!isset($_REQUEST['protocol'])){
	$_REQUEST['protocol'] = 'json';
}

define('RPC_DIR', str_replace('\\', '/', dirname(__FILE__)) . '/');
define('RPC_URL', str_replace($_SERVER['DOCUMENT_ROOT'], '', RPC_DIR));

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . RPC_DIR);

//define('NO_SESS',1);
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

require('base/rpcCmdShell.class.php');

we_html_tools::protect();

function dieWithError($text){
	switch($_REQUEST['protocol']){
		case 'json':
			$resp = new rpcResponse();
			$resp->setStatus(false);
			$resp->setData('data', $text);
			$errorView = new rpcJsonView();
			print $errorView->getResponse($resp);
			exit;
		case 'text':
			$resp = new rpcResponse();
			$resp->setStatus(false);
			$resp->setData('data', $text);
			$errorView = new rpcView();
			print $errorView->getResponse($resp);
			exit;
		default:
			die($text);
	}
}

if(!isset($_REQUEST['cmd'])){
	dieWithError('The Request is not well formed!');
}

$_shell = new rpcCmdShell($_REQUEST, $_REQUEST['protocol']);

if($_shell->getStatus() == rpcCmd::STATUS_OK){
	$_shell->executeCommand();
	print $_shell->getResponse();
} else { // there was an error in initializing the command
	dieWithError($_shell->getErrorOut());
}

unset($_shell);
