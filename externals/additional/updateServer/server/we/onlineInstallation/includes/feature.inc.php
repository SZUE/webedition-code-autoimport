<?php
/**
 * $Id: feature.inc.php 13564 2017-03-13 17:13:40Z mokraemer $
 */
// execute command
switch($_REQUEST['detail']){

	// get form with all possible languages
	case 'languagesForm':
		print languagesInstaller::getLanguagesFormResponse();
		break;


	// save chosen languages in session
	case 'registerLanguages':
		$defaultLanguage = $clientRequestVars['le_defaultLanguage'];
		$extraLanguages = array();

		if(isset($clientRequestVars['le_extraLanguages'])){
			$extraLanguages = $clientRequestVars['le_extraLanguages'];
		}

		print languagesInstaller::getRegisterLanguagesResponse($defaultLanguage, $extraLanguages);
		break;


	// get form with all versions
	case 'versionForm':

		print licenseInstaller::getVersionFormResponse();
		break;


	// save the version in session
	case 'registerVersion':
		print licenseInstaller::getRegisterVersionResponse($clientRequestVars['le_version']);
		break;
}

