<?php

/**
 * $Id: languagesUpdate.class.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
class languagesUpdate extends languagesBase{

	/**
	 * @return string
	 */
	static function getSelectLanguagesResponse(){

		// at least already installed languages can be reinstalled
		$installAbleLanguages = $_SESSION['clientInstalledLanguages'];

		// get all possible languages
		$vers = $_SESSION['clientVersionNumber'];
		$versionLngs = updateUpdate::getVersionsLanguageArray(false, $vers, true);
		if(!is_array($installAbleLanguages)){
			$installAbleLanguages = unserialize(urldecode(base64_decode($installAbleLanguages)));
		}
		if(isset($versionLngs[$vers])){
			foreach($versionLngs[$vers] as $cur){
				if(!in_array($cur, $installAbleLanguages)){
					$installAbleLanguages[] = $cur;
				}
			}
		}

		// languages not existing for this version
		$missingLanguages = array();
		$allLanguages = self::getExistingLanguages($vers >= LANGUAGELIMIT);
		foreach($allLanguages as $lang){
			if(!in_array($lang, $installAbleLanguages)){
				$missingLanguages[] = $lang;
			}
		}

		$GLOBALS['updateServerTemplateData']['installAbleLanguages'] = $installAbleLanguages;
		$GLOBALS['updateServerTemplateData']['missingLanguages'] = $missingLanguages;
		return updateUtilUpdate::getResponseString(updateUtilUpdate::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/selectLanguages.inc.php'));
	}

	/**
	 * @return string
	 */
	static function getNoLanguageSelectedResponse(){
		$ret = updateUtilUpdate::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/noLanguageSelected.inc.php');
		return updateUtilUpdate::getResponseString($ret);
	}

	/**
	 * @return string
	 */
	static function getConfirmLanguagesResponse(){
		$ret = updateUtilUpdate::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/confirmLanguages.inc.php');
		return updateUtilUpdate::getResponseString($ret);
	}

	/**
	 * @return array
	 */
	static function getChangesForUpdate(){
		if(!is_array($_SESSION['clientDesiredLanguages'])){
			$_SESSION['clientDesiredLanguages'] = unserialize(urldecode(base64_decode($_SESSION['clientDesiredLanguages'])));
		}

		return updateUtilUpdate::getChangesArrayByQueries([
				'SELECT changes,version,detail FROM ' . SOFTWARE_LANGUAGE_TABLE . ' WHERE (version<=' . $_SESSION['clientVersionNumber'] . ') ' .
				($_SESSION['clientDesiredLanguages'] ? ' AND language IN("' . implode('","', $_SESSION['clientDesiredLanguages']) . '")' : ' AND 0 ') .
				' ORDER BY version DESC'
		]);
	}

	/**
	 * @return string
	 */
	static function getGetChangesResponse(){
		return installerUpdate::getGetChangesResponse();
	}

	/**
	 * Response to finish installation, deletes not needed files and updates
	 * installed_modules file
	 *
	 * @return string
	 */
	static function getFinishInstallationResponse(){
		$message = '<ul>';
		foreach($_SESSION['clientDesiredLanguages'] as $cur){
			$message .= "<li>" . $cur . "</li>";
		}
		$message .= '</ul>';

		$retArray = [
			'Type' => 'eval',
			'Code' => '<?php
' . updateUtilUpdate::getOverwriteClassesCode() . '
$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
$liveUpdateFnc->deleteDir($filesDir);

$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['lang']['languages']['finished'] . $message . '", "' . $_SESSION['clientVersion'] . '", 0);

?>' . installerUpdate::getFinishInstallationResponsePart("<div>" . $GLOBALS['lang']['languages']['finished'] . "\\n" . $message . "</div>")
		];
		return updateUtilUpdate::getResponseString($retArray);
	}

}
