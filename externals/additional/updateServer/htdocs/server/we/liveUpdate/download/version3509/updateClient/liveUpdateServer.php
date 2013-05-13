<?php

/**
 * This file is downloaded from client and executed there. Be careful with it.
 */

if(file_exists('../../updateinclude/proxysettings.php')){
	include_once('../../updateinclude/proxysettings.php');
}

/*
 * Include all needed files on client
 */
	require_once('../includes/includes.inc.php');
	
	// and new updated classes
	require_once('liveUpdateFunctionsServer.class.php');
	require_once('liveUpdateResponseServer.class.php');
	
	protect();
	

/*
 * Deal with update_cmd
 */
if (isset($_REQUEST['update_cmd'])) {
	
	/*
	 * Gather all needed Variables for the update-Request
	 */
	$parameters = array();
	
	foreach ($LU_ParameterNames as $parameterName) {
		
		if (isset($_REQUEST[$parameterName])) {
			$parameters[$parameterName] = $_REQUEST[$parameterName];
		}
	}
	
	
	
	// this is flag to check if a response was received!
	$response = false;
	
	/*
	 * $_REQUEST['liveUpdateSession'] exists => Session on server is up
	 * prepare all needed variables to submit to the updateServer
	 * These are stored in $LU_ParameterNames
	 */
	
	// add all other request parameters to the request
	$reqVars = array();
	foreach ($_REQUEST as $key => $value) {
		if (!isset($parameters[$key]) && !in_array($key, $LU_IgnoreRequestParameters)) {
			$reqVars[$key] = $value;
		}
	}
	$parameters['reqArray'] = serialize($reqVars);
	
	$response = liveUpdateHttp::getHttpResponse(LIVEUPDATE_SERVER, LIVEUPDATE_SERVER_SCRIPT, $parameters);
	
	/*
	 * There is a response from the Update-Server.
	 */
	if ($response) {
		
		$liveUpdateResponse = new liveUpdateResponseServer();
		
		if ($liveUpdateResponse->initByHttpResponse($response)) {
			
			print $liveUpdateResponse->getOutput();

		} else {
			print liveUpdateFrames::htmlConnectionError();
		}
		
	} else {
		/*
		 * No response from the update-server. Error message
		 */
		print 'Fehler 1';
	}
	
} else {
	
	print 'Fehler 2';
}
?>