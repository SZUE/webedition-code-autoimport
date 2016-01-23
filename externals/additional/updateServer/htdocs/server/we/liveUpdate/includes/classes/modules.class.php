<?php

class modules extends modulesBase{

	/**
	 * returns response with screen to select modules
	 *
	 * @return string
	 */
	function getSelectModulesResponse(){

		$serial = license::getSerialByUid();
		$serialInformation = license::getSerialInformation($serial);

		$existingModules = modules::getExistingModules();
		$installAbleModules = array();

		// which modules can be installed?
		foreach($serialInformation['modules'] as $moduleKey => $amount){
			if(in_array($moduleKey, $_SESSION['clientInstalledModules']) || ($amount > $serialInformation['installedModules'][$moduleKey])){
				$installAbleModules[$moduleKey] = $existingModules[$moduleKey]['text'];
			}
		}

		if(sizeof($installAbleModules)){
			$GLOBALS['updateServerTemplateData']['installAbleModules'] = $installAbleModules;
			$GLOBALS['updateServerTemplateData']['existingModules'] = $existingModules;

			$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/modules/selectModules.inc.php');
			return updateUtil::getResponseString($ret);
		} else {
			$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/modules/noInstallableModules.inc.php');
			return updateUtil::getResponseString($ret);
		}
	}

	/**
	 * gathers all changes needed for an update and returns assoziative array
	 *
	 * @return array
	 */
	function getChangesForUpdate(){

		$contentQuery = '';
		if(!$_SESSION['clientContent']){
			$contentQuery .= ' AND (type="system") ';
		}

		// get systemlanguage only
		$sysLngQuery = ' AND (language="" OR language="' . $_SESSION['clientSyslng'] . '") ';

		$modulesQuery = ' AND ( ';
		foreach($_SESSION['clientDesiredModules'] as $module){
			if(in_array($module, $_SESSION['clientInstalledModules'])){
				$modulesQuery .= ' (module = "' . $module . '" AND type="system") OR ';
			} else {
				$modulesQuery .= ' module = "' . $module . '" OR ';
			}
		}
		$modulesQuery .= ' 0 )';

		$query = '
			SELECT *
			FROM ' . SOFTWARE_TABLE . '
			WHERE
				(version <= ' . $_SESSION['clientVersionNumber'] . ')
				' . $contentQuery . '
				' . $modulesQuery . '
				' . $sysLngQuery . '
			ORDER BY version DESC
		';


		$languagePart = 'AND ( ';
		foreach($_SESSION['clientInstalledLanguages'] as $language){
			$languagePart .= 'language="' . $language . '" OR ';
		}
		$languagePart .= ' 0 )';

		// query for needed changes language
		$languageQuery = '
			SELECT *
			FROM ' . SOFTWARE_LANGUAGE_TABLE . '
			WHERE
				(version <= ' . $_SESSION['clientVersionNumber'] . ')
				' . $contentQuery . '
				' . $modulesQuery . '
				' . $languagePart . '
			ORDER BY version DESC
		';

		return updateUtil::getChangesArrayByQueries(array($query, $languageQuery));
	}

	function getNoModulesSelectedResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/modules/noModulesSelected.inc.php');
		return updateUtil::getResponseString($ret);
	}

	function getReselectModulesResponse($desiredModules = array()){

		$GLOBALS['updateServerTemplateData']['existingModules'] = modules::getExistingModules();
		$GLOBALS['updateServerTemplateData']['clientDesiredModules'] = $desiredModules;

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/modules/reselectModules.inc.php');
		return updateUtil::getResponseString($ret);
	}

	function getConfirmModulesResponse(){

		$GLOBALS['updateServerTemplateData']['clientDesiredModules'] = $_SESSION['clientDesiredModules'];
		$GLOBALS['updateServerTemplateData']['existingModules'] = modules::getExistingModules();

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/modules/confirmModules.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * @return string
	 */
	function getGetChangesResponse(){
		return installer::getGetChangesResponse();
	}

	/**
	 * Response to finish installation, deletes not needed files and updates
	 * installed_modules file
	 *
	 * @return string
	 */
	function getFinishInstallationResponse(){

		$existingModules = modules::getExistingModules();

		$modules_path = "LIVEUPDATE_SOFTWARE_DIR . '/webEdition/we/include/we_installed_modules.inc" . $_SESSION['clientExtension'] . "'";
		// end of installed modules

		// if usermodule is installed, create new user
		$message = '<ul>';
		for($i = 0; $i < sizeof($_SESSION['clientDesiredModules']); $i++){
			$message .= "<li>" . $existingModules[$_SESSION['clientDesiredModules'][$i]]['text'] . "</li>";
		}
		$message .= '</ul>';

		$newContent = modules::getCodeForInstalledModules();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

' . updateUtil::getOverwriteClassesCode() . '

$modulesPath = ' . $modules_path . ';
$modulesContent = "' . updateUtil::encodeCode($newContent) . '";

$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
$liveUpdateFnc->deleteDir($filesDir);

if ($liveUpdateFnc->filePutContent($modulesPath, $liveUpdateFnc->decodeCode($modulesContent))) {

	$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage']['modules']['finished'] . $message . '", "' . $_SESSION['clientVersion'] . '", 0);

	?>' . installer::getFinishInstallationResponsePart("<div>" . $GLOBALS['lang']['modules']['finished'] . "\\n" . $message . "</div>") . '<?php

} else {
	' . installer::getErrorMessageResponsePart() . '
}
?>';

		return updateUtil::getResponseString($retArray);
	}

}
