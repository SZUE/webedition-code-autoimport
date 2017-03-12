<?php
/**
 * $Id$
 */
if(isset($_REQUEST["update_cmd"]) && $_REQUEST["update_cmd"] == "startSession"){
	/*
	 * Start the session on the server and store alwayws needed data here
	 */

	// destroy existing session
	@session_start();
	$_SESSION = array();
	session_destroy();

	// start new session
	if(isset($_REQUEST["clientDomain"])){
		session_set_cookie_params(0, '/', $_REQUEST["clientDomain"]);
	}
	@session_start();

	foreach($_REQUEST as $varName => $varValue){

		if(strpos($varName, 'client') === 0){

			$varValue = urldecode($varValue);
			if(preg_match('|^[asO]:\d+:|', $varValue)){
				$varArray = unserialize(($varValue));
			} elseif(preg_match('|^[asO]:\d+:|', base64_decode($varValue))){
				$varArray = unserialize(base64_decode($varValue));
			} else {
				$varArray = '';
			}
			if(is_array($varArray)){
				$_SESSION[$varName] = $varArray;
			} else {
				$_SESSION[$varName] = $varValue;
			}
		}
	}

	// prepare some more variables for update
	if(isset($_SESSION['clientVersion'])){ // version as string and number
		$_SESSION['clientVersionNumber'] = updateUtilBase::version2number($_SESSION['clientVersion']);
	}

	if(!isset($_SESSION['clientEncoding'])){
		// remove folder from path - if software is encoded
		$_SESSION['clientEncoding'] = 'none';
	}

	if(!isset($_SESSION['clientWE_CLASSIC'])){
		$_SESSION['clientWE_CLASSIC'] = false;
	}

	if(isset($_SESSION['clientUpdateUrl'])){

		// THIS IS FOR OnlineInstaller
		$le_online_installer_req = "";
		if(isset($_REQUEST["clientLeWizard"])){
			$le_online_installer_req = "&leWizard=" . $_REQUEST["clientLeWizard"] .
				"&leStep=" . $_REQUEST["clientLeStep"];

			// THIS IS FOR Wizards
		} elseif(isset($_REQUEST["we_cmd"][0])){
			$le_online_installer_req = "&we_cmd[0]=" . $_REQUEST["we_cmd"][0] .
				"&leWizard=" . $_REQUEST["next_cmd"] .
				"&leStep=" . $_REQUEST["detail"];

			$_SESSION['we_cmd'][0] = $_REQUEST["we_cmd"][0];
		}
		if(isset($_REQUEST['clientSessionName']) && isset($_REQUEST['clientSessionID'])){
			$clientsession = '&' . $_REQUEST['clientSessionName'] . '=' . $_REQUEST['clientSessionID'];
		} else {
			$clientsession = '';
		}

		// recall script on client with session_id as parameter
		print "
<script>
	document.location = '" . $_SESSION['clientUpdateUrl'] . "?update_cmd=" . $_REQUEST['next_cmd'] . "&detail=" . $_REQUEST['detail'] . $clientsession . "&liveUpdateSession=" . session_id() . "&$le_online_installer_req';
</script>";
	} else {
		print 'clientUpdateUrl is not known';
	}
	exit;
} else if(!empty($_REQUEST['liveUpdateSession'])){
	// restart existing session
	if(isset($_REQUEST["clientDomain"])){
		session_set_cookie_params(0, '/', $_REQUEST["clientDomain"]);
	}
	session_id($_REQUEST["liveUpdateSession"]);
	@session_start();

	if(!isset($_SESSION['clientUpdateUrl'])){ // session is dead
		$_REQUEST['update_cmd'] = 'notification';
		$_REQUEST['detail'] = 'lostSession';
	}
} else {

	// no update_cmd set - perhaps just to test updateserver
	if(!( isset($_REQUEST['update_cmd']) && $_REQUEST['update_cmd'] == 'checkConnection' )){

		// errormessage ? session is dead!
	}
}

