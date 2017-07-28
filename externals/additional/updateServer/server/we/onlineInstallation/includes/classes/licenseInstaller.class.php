<?php

/**
 * $Id: licenseInstaller.class.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
class licenseInstaller extends licenseBase{

	/**
	 * returns form to choose version
	 *
	 * @return string
	 */
	static function getVersionFormResponse(){

		$AvailableVersions = updateInstaller::getVersionsLanguageArray(false,0,!empty($_SESSION['testUpdate']));
		//$NotLiveVersions = update::getNotLiveVersions();
		$SubVersions = updateInstaller::getSubVersions();
		$AlphaBetaVersions = updateInstaller::getAlphaBetaVersions();
		$VersionNames = updateInstaller::getVersionNames();

		$MatchingVersions = $VersionsMissingLanguage = array();
		foreach(array_keys($AvailableVersions) as $Version){
			$MatchingVersions[$Version] = updateUtilInstaller::number2version($Version);
		}
		unset($MatchingVersions['betaLanguages']);
		$GLOBALS['updateServerTemplateData']['AvailableVersions'] = $AvailableVersions;
		$GLOBALS['updateServerTemplateData']['MatchingVersions'] = $MatchingVersions;
		$GLOBALS['updateServerTemplateData']['VersionsMissingLanguage'] = $VersionsMissingLanguage;
		//$GLOBALS['updateServerTemplateData']['NotLiveVersions'] = $NotLiveVersions;
		$GLOBALS['updateServerTemplateData']['SubVersions'] = $SubVersions;
		$GLOBALS['updateServerTemplateData']['VersionNames'] = $VersionNames;
		$GLOBALS['updateServerTemplateData']['AlphaBetaVersions'] = $AlphaBetaVersions;
		$_SESSION['SubVersions'] = $SubVersions;
		$_SESSION['AlphaBetaVersions'] = $AlphaBetaVersions;

		$ret = updateUtilInstaller::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/license/versionForm.inc.php');
		return updateUtilInstaller::getResponseString($ret);
	}

	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	static function getRegisterVersionResponse($version){

		$_SESSION['clientTargetVersionNumber'] = $version;
		$_SESSION['clientTargetSubVersionNumber'] = updateInstaller::getSubVersion($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionType'] = updateInstaller::getVersionType($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionBranch'] = updateInstaller::getOnlyVersionBranch($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersion'] = updateUtilInstaller::number2version($version);

		$_SESSION['clientVersionNumber'] = $_SESSION['clientTargetVersionNumber'];
		$_SESSION['clientVersion'] = $_SESSION['clientTargetVersion'];

		$ret = array(
			'Type' => 'eval',
			'Code' => '<?php return true; ?>',
		);
		return updateUtilInstaller::getResponseString($ret);
	}

}
