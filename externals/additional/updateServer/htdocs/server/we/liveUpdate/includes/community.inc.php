<?php

// 1st check if software is installed correct
if ( !(isset($_SESSION['clientInstalledTableId']) &&  $_SESSION['clientInstalledTableId']) ) {
	
	$_SESSION['clientInstalledTableId'] = license::getDomainId($_SESSION['clientDomain'], $_SESSION['clientUid']);
}

// execute command
if (isset($_SESSION['clientInstalledTableId'])) {
	
	switch ($_REQUEST['detail']) {
		
		case 'deauthenticate':
			print community::getConfirmDeauthenticationWindow();
			break;
		case 'deauthenticateVerified':
			print community::getDeauthenticationWindow();
			break;
			
		case 'authenticateForm':
			print community::getAuthenticationFormWindow();
			break;
		case 'authenticate':
			print community::getAuthenticationWindow();
			break;
			
		case 'reauthenticateForm':
			print community::getReauthenticationFormWindow();
			break;
		case 'reauthenticate':
			print community::getReauthenticationWindow();
			break;
			
		default:
			print notification::getNotAvailableAtTheMomentResponse();
			break;
	}
	
} else {
	
}



?>