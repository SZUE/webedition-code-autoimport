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
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . WEBEDITION_PATH . '/rpc/');

function dieWithError($text, $protocol){
	switch($protocol){
		case 'json':
			$resp = new we_rpc_response();
			$resp->setStatus(false);
			$resp->setData('data', $text);
			$errorView = new we_rpc_jsonView('', $protocol);
			echo $errorView->getResponse($resp);
			exit;
		case 'text':
			$resp = new we_rpc_response();
			$resp->setStatus(false);
			$resp->setData('data', $text);
			$errorView = new we_rpc_view('', $protocol);
			echo $errorView->getResponse($resp);
			exit;
		default:
			die($text);
	}
}

if(!we_base_request::_(we_base_request::RAW, 'cmd')){
	dieWithError('The Request is not well formed!', $protocol);
}

//FIXME: !this is not safe at all
$shell = new we_rpc_cmdShell($_REQUEST, $protocol);

if($shell->getStatus() == we_rpc_cmd::STATUS_OK){
	$shell->executeCommand();
	echo $shell->getResponse();
} else { // there was an error in initializing the command
	dieWithError($shell->getErrorOut(), $protocol);
}

unset($shell);
