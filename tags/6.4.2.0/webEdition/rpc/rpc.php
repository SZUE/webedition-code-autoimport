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
we_html_tools::protect();
$protocol = we_base_request::_(we_base_request::STRING, 'protocol', 'json');
define('RPC_DIR', str_replace('\\', '/', dirname(__FILE__)) . '/');
define('RPC_URL', str_replace($_SERVER['DOCUMENT_ROOT'], '', RPC_DIR));

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . RPC_DIR);

require('base/rpcCmdShell.class.php');

function dieWithError($text, $protocol){
	switch($protocol){
		case 'json':
			$resp = new rpcResponse();
			$resp->setStatus(false);
			$resp->setData('data', $text);
			$errorView = new rpcJsonView();
			echo $errorView->getResponse($resp);
			exit;
		case 'text':
			$resp = new rpcResponse();
			$resp->setStatus(false);
			$resp->setData('data', $text);
			$errorView = new rpcView();
			echo $errorView->getResponse($resp);
			exit;
		default:
			die($text);
	}
}

if(!we_base_request::_(we_base_request::RAW, 'cmd')){
	dieWithError('The Request is not well formed!', $protocol);
}

//FIXME: !!this is not safe at all
$_shell = new rpcCmdShell($_REQUEST, $protocol);

if($_shell->getStatus() == rpcCmd::STATUS_OK){
	$_shell->executeCommand();
	echo $_shell->getResponse();
} else { // there was an error in initializing the command
	dieWithError($_shell->getErrorOut(), $protocol);
}

unset($_shell);
