<?php
// execute command
switch($_REQUEST['detail']){

	// get form with all possible languages
	case 'languagesForm':
		print languages::getLanguagesFormResponse();
		break;


	// save chosen languages in session
	case 'registerLanguages':
		$defaultLanguage = $clientRequestVars['le_defaultLanguage'];
		$extraLanguages = array();

		if(isset($clientRequestVars['le_extraLanguages'])){
			$extraLanguages = $clientRequestVars['le_extraLanguages'];
		}

		print languages::getRegisterLanguagesResponse($defaultLanguage, $extraLanguages);
		break;


	// get form with all versions
	case 'versionForm':

		print license::getVersionFormResponse();
		break;


	// save the version in session
	case 'registerVersion':
		print license::getRegisterVersionResponse($clientRequestVars['le_version']);
		break;


	// get form with all snippets
	case 'snippetsForm':
		print snippets::getSnippetsFormResponse();
		break;
}

