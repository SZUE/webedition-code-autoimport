<?php

class languages extends languagesBase {

	/**
	 * @return string
	 */
	function getSelectLanguagesResponse() {

		// at least already installed languages can be reinstalled
		$installAbleLanguages = $_SESSION['clientInstalledLanguages'];

		// get all possible languages
		$versionLngs = update::getVersionsLanguageArray(false);
		if(!is_array($installAbleLanguages)) {
			$installAbleLanguages = unserialize(urldecode(base64_decode($installAbleLanguages)));
		}
		if (isset($versionLngs[$_SESSION['clientVersionNumber']])) {
			$installableBetaLanguages = $versionLngs[$_SESSION['clientVersionNumber']]["betaLanguages"];
			unset($versionLngs[$_SESSION['clientVersionNumber']]["betaLanguages"]);
			$versionLngCount = sizeof($versionLngs[$_SESSION['clientVersionNumber']]);
			for ($i=0;$i<$versionLngCount;$i++) {
				if (!in_array($versionLngs[$_SESSION['clientVersionNumber']][$i], $installAbleLanguages)) {
					$installAbleLanguages[] = $versionLngs[$_SESSION['clientVersionNumber']][$i];
				}

			}

		}
		
		// languages not existing for this version
		$missingLanguages = array();
		$missingBetaLanguages = array();
		$allLanguages = languages::getExistingLanguages();
		for ($i=0;$i<sizeof($allLanguages); $i++) {
			if (!in_array($allLanguages[$i], $installAbleLanguages)) {
				if(in_array($allLanguages[$i],$installableBetaLanguages)) {
					$missingBetaLanguages[] = $allLanguages[$i];
				} else {
					$missingLanguages[] = $allLanguages[$i];
				}

			}

		}
		error_log(print_r($missingBetaLanguages,1));
		
		$GLOBALS['updateServerTemplateData']['installAbleLanguages'] = $installAbleLanguages;
		$GLOBALS['updateServerTemplateData']['missingLanguages'] = $missingLanguages;
		$GLOBALS['updateServerTemplateData']['installableBetaLanguages'] = $installableBetaLanguages;
		$GLOBALS['updateServerTemplateData']['missingBetaLanguages'] = $missingBetaLanguages;
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/selectLanguages.inc.php');
		return updateUtil::getResponseString($ret);

	}

	/**
	 * @return string
	 */
	function getNoLanguageSelectedResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/noLanguageSelected.inc.php');
		return updateUtil::getResponseString($ret);

	}

	/**
	 * @return string
	 */
	function getConfirmLanguagesResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/confirmLanguages.inc.php');
		return updateUtil::getResponseString($ret);

	}

	/**
	 * @return array
	 */
	function getChangesForUpdate() {

		$lngPart = '';

		// query for all installed modules
		$modulesQuery = ' AND ( module = "" OR ';
		foreach ($GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'] as $module) {
			$modulesQuery .= 'module="' . $module . '" OR ';

		}
		foreach ($_SESSION['clientInstalledModules'] as $module) {
			$modulesQuery .= 'module = "' . $module . '" OR  ';
			
		}
		$modulesQuery .= '0 )';
		if(!is_array($_SESSION['clientDesiredLanguages'])) {
			$_SESSION['clientDesiredLanguages'] = unserialize(urldecode(base64_decode($_SESSION['clientDesiredLanguages'])));
		}
		//error_log(print_r($_SESSION['clientDesiredLanguages'],true));
		// desired languages
		$languagePart = 'AND ( ';
		foreach ($_SESSION['clientDesiredLanguages'] as $language) {
			$languagePart .= 'language="' . $language . '" OR ';
		}
		$languagePart .= ' 0 )';

		$query = '
			SELECT *
			FROM ' . SOFTWARE_LANGUAGE_TABLE . '
			WHERE
				(version <= ' . $_SESSION['clientVersionNumber'] . ')
				AND type="system"
				' . $modulesQuery . '
				' . $languagePart . '
			ORDER BY version DESC
		';

		return updateUtil::getChangesArrayByQueries(array($query));

	}

	/**
	 * @return string
	 */
	function getGetChangesResponse() {
		return installer::getGetChangesResponse();

	}

	/**
	 * Response to finish installation, deletes not needed files and updates
	 * installed_modules file
	 *
	 * @return string
	 */
	function getFinishInstallationResponse() {

		$message = '<ul>';
		for ($i=0; $i<sizeof($_SESSION['clientDesiredLanguages']);$i++) {
			$message .= "<li>" . $_SESSION['clientDesiredLanguages'][$i] . "</li>\\n";
		}
		$message .= '</ul>';

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
		$liveUpdateFnc->deleteDir($filesDir);

		$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage']['languages']['finished'] . $message . '", "' . $_SESSION['clientVersion'] . '", 0);

		?>' . installer::getFinishInstallationResponsePart("<div>" . $GLOBALS['lang']['languages']['finished'] . "\\n" . $message . "</div>") . '<?php
		?>';
		return updateUtil::getResponseString($retArray);

	}

}

?>