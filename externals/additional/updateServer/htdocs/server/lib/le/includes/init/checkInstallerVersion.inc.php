<?php

/**
 * check online installer version
 * it has to be at least 2.7.0.0 or newer 
 */
if ( isset($clientRequestVars['le_installer_version']) ) {
	$_SESSION['le_installer_version'] = $clientRequestVars['le_installer_version'];
}

if(isset($_SESSION['le_installer_version'])) {
	$minVersion = "2700";
	$currentVersion = str_replace(".","",$_SESSION['le_installer_version']);
	if($currentVersion < $minVersion) {
		print notification::getInstallerVersionCheckResponse();
		exit;
	}
} else {
	print notification::getInstallerVersionCheckResponse();
	exit;
}
?>