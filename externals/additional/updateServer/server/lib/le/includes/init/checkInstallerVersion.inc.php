<?php
/**
 * $Id: checkInstallerVersion.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
/**
 * check online installer version
 * it has to be at least 2.7.0.0 or newer
 */
if(isset($clientRequestVars['le_installer_version'])){
	$_SESSION['le_installer_version'] = $clientRequestVars['le_installer_version'];
}

if(isset($_SESSION['le_installer_version'])){
	$currentVersion = str_replace(".", "", $_SESSION['le_installer_version']);
	if($currentVersion < str_replace(".", "", MIN_INSTALLER_V)){
		print notificationBase::getInstallerVersionCheckResponse();
		exit;
	}
} else {
	print notificationBase::getInstallerVersionCheckResponse();
	exit;
}
