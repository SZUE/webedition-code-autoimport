<?php

	$leInstallerSteps = array();
	
	// Show only if not redirected from binary installer
	// If binary installer
	if(		!file_exists(LE_INSTALLER_PATH . "/OnlineInstaller.log.php")
		&&	!is_file(LE_INSTALLER_PATH . "/OnlineInstaller.log.php")) {
		$leInstallerSteps[] = 'Welcome';
		
	}
	
	$leInstallerSteps[] = 'HintAboutOnlineInstallation';
	//$leInstallerSteps[] = 'VersionCheck';
	
	// if more than one application could be installed and no default application is set
	// then show screen to choose the application
	if(sizeof( $GLOBALS['leApplicationList']) > 1 && !defined("LE_DEFAULT_APPLICATION")) {
		$leInstallerSteps[] = 'ChooseApplication';

	}

	$leInstallerSteps[] = 'ProxyServer';
	$leInstallerSteps[] = 'ConnectionCheck';
	$leInstallerSteps[] = 'SessionAndCookieTest';

?>