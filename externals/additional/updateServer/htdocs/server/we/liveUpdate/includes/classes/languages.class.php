<?php

class languages extends languagesBase{

	/**
	 * @return string
	 */
	static function getSelectLanguagesResponse(){

		// at least already installed languages can be reinstalled
		$installAbleLanguages = $_SESSION['clientInstalledLanguages'];

		// get all possible languages
		$versionLngs = update::getVersionsLanguageArray(false);
		if(!is_array($installAbleLanguages)){
			$installAbleLanguages = unserialize(urldecode(base64_decode($installAbleLanguages)));
		}
		if(isset($versionLngs[$_SESSION['clientVersionNumber']])){
			$versionLngCount = sizeof($versionLngs[$_SESSION['clientVersionNumber']]);
			for($i = 0; $i < $versionLngCount; $i++){
				if(!in_array($versionLngs[$_SESSION['clientVersionNumber']][$i], $installAbleLanguages)){
					$installAbleLanguages[] = $versionLngs[$_SESSION['clientVersionNumber']][$i];
				}
			}
		}

		// languages not existing for this version
		$missingLanguages = array();
		$allLanguages = languages::getExistingLanguages();
		foreach($allLanguages as $lang){
			if(!in_array($lang, $installAbleLanguages)){
					$missingLanguages[] = $lang;
			}
		}

		$GLOBALS['updateServerTemplateData']['installAbleLanguages'] = $installAbleLanguages;
		$GLOBALS['updateServerTemplateData']['missingLanguages'] = $missingLanguages;
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/selectLanguages.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * @return string
	 */
	function getNoLanguageSelectedResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/noLanguageSelected.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * @return string
	 */
	function getConfirmLanguagesResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/confirmLanguages.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * @return array
	 */
	static function getChangesForUpdate(){
		if(!is_array($_SESSION['clientDesiredLanguages'])){
			$_SESSION['clientDesiredLanguages'] = unserialize(urldecode(base64_decode($_SESSION['clientDesiredLanguages'])));
		}

		return updateUtil::getChangesArrayByQueries([
				'SELECT changes,version,detail FROM ' . SOFTWARE_LANGUAGE_TABLE . ' WHERE (version<=' . $_SESSION['clientVersionNumber'] . ') ' .
				($_SESSION['clientDesiredLanguages'] ? ' AND language IN("' . implode('","', $_SESSION['clientDesiredLanguages']) . '")' : ' AND 0 ') .
				' ORDER BY version DESC'
		]);
	}

	/**
	 * @return string
	 */
	static function getGetChangesResponse(){
		return installer::getGetChangesResponse();
	}

	/**
	 * Response to finish installation, deletes not needed files and updates
	 * installed_modules file
	 *
	 * @return string
	 */
	function getFinishInstallationResponse(){

		$message = '<ul>';
		for($i = 0; $i < sizeof($_SESSION['clientDesiredLanguages']); $i++){
			$message .= "<li>" . $_SESSION['clientDesiredLanguages'][$i] . "</li>";
		}
		$message .= '</ul>';

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

' . updateUtil::getOverwriteClassesCode() . '

$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
$liveUpdateFnc->deleteDir($filesDir);

$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage']['languages']['finished'] . $message . '", "' . $_SESSION['clientVersion'] . '", 0);

?>' . installer::getFinishInstallationResponsePart("<div>" . $GLOBALS['lang']['languages']['finished'] . "\\n" . $message . "</div>");
		return updateUtil::getResponseString($retArray);
	}

}
