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
if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/includes/proxysettings.inc.php')){
	include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/includes/proxysettings.inc.php');
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_autoload.inc.php');

if(is_dir($_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/updateClient')){
	we_base_file::deleteLocalFolder($_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/updateClient', true);
}

/*
 * Include all needed files
 */
require_once('includes/includes.inc.php');
we_html_tools::protect();

/*
 * Deal with update_cmd
 */
if(isset($_REQUEST['update_cmd'])){

	/*
	 * Gather all needed Variables for the update-Request
	 */
	$parameters = [];
	foreach($LU_ParameterNames as $parameterName){
		if(isset($_REQUEST[$parameterName])){
			$parameters[$parameterName] = $_REQUEST[$parameterName];
		}
	}

	/*
	 * For command checkConnection, it is not needed to create a session on the
	 * server. Therefore treat this command in a special way.
	 */
	if($_REQUEST['update_cmd'] === 'checkConnection'){

		$response = liveUpdateHttp::getHttpResponse(LIVEUPDATE_SERVER, LIVEUPDATE_SERVER_SCRIPT, $parameters);
		$liveUpdateResponse = new liveUpdateResponse();

		echo ($liveUpdateResponse->initByHttpResponse($response) ?
			liveUpdateFrames::htmlConnectionSuccess($liveUpdateResponse->isError() ? $liveUpdateResponse->getField('Message') : '') :
			liveUpdateFrames::htmlConnectionError()
		);

		exit();
	}
	/*
	 * Before an update_cmd is submitted to the server, there must be an
	 * existing session on the server. $_REQUEST[liveUpdateSession] contains
	 * the session_id of the server. If this id is missing, create a new
	 * session on the server.
	 */
	if(empty($_REQUEST['liveUpdateSession'])){

		/*
		 * exit after submitting the form
		 */
		echo liveUpdateHttp::getServerSessionForm();
		exit;
	}
	/*
	 * $_REQUEST['liveUpdateSession'] exists => Session on server is up
	 * prepare all needed variables to submit to the updateServer
	 * These are stored in $LU_ParameterNames
	 */

	// add all other request parameters to the request
	$reqVars = [];
	foreach($_REQUEST as $key => $value){
		if(!isset($parameters[$key]) && !in_array($key, $LU_IgnoreRequestParameters) && !array_key_exists($key, $_COOKIE)){
			$reqVars[$key] = $value;
		}
	}
	$parameters['reqArray'] = base64_encode(serialize($reqVars));

	$response = liveUpdateHttp::getHttpResponse(LIVEUPDATE_SERVER, LIVEUPDATE_SERVER_SCRIPT, $parameters);


	/*
	 * There is a response from the Update-Server.
	 */
	if($response){

		$liveUpdateResponse = new liveUpdateResponse();

		echo ($liveUpdateResponse->initByHttpResponse($response) ?
			$liveUpdateResponse->getOutput() :
			liveUpdateFrames::htmlConnectionError());
	} else {
		/*
		 * No response from the update-server. Error message
		 */
		echo liveUpdateFrames::htmlConnectionError();
	}
} else {
	/*
	 * No update_cmd exists, show normal frameset
	 */
	$updateFrames = new liveUpdateFrames();
	echo $updateFrames->getFrame();
}
