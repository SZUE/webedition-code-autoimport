<?php

class license extends licenseBase {

	/**
	 * returns form to choose version
	 *
	 * @return string
	 */
	function getVersionFormResponse() {

		$AvailableVersions = update::getVersionsLanguageArray(false);
		$NotLiveVersions = update::getNotLiveVersions();
		$SubVersions = update::getSubVersions();
		$AlphaBetaVersions = update::getAlphaBetaVersions();
                $VersionNames = update::getVersionNames();

		$MatchingVersions = array();
		$VersionsMissingLanguage = array();
		foreach ($AvailableVersions as $Version => $Languages) {
			//$MissingLanguages = array_diff($_SESSION['clientDesiredLanguages'], $Languages);
			/*if(sizeof($MissingLanguages) == 0) {
				$MatchingVersions[$Version] = updateUtil::number2version($Version);
			} else {
				$VersionsMissingLanguage[$Version] = updateUtil::number2version($Version);
			}
			*/
			$MatchingVersions[$Version] = updateUtil::number2version($Version);
		}
		unset($MatchingVersions['betaLanguages']);
		$GLOBALS['updateServerTemplateData']['AvailableVersions'] = $AvailableVersions;
		$GLOBALS['updateServerTemplateData']['MatchingVersions'] = $MatchingVersions;
		$GLOBALS['updateServerTemplateData']['VersionsMissingLanguage'] = $VersionsMissingLanguage;
		$GLOBALS['updateServerTemplateData']['NotLiveVersions'] = $NotLiveVersions;
		$GLOBALS['updateServerTemplateData']['SubVersions'] = $SubVersions;
                $GLOBALS['updateServerTemplateData']['VersionNames'] = $VersionNames;
		$GLOBALS['updateServerTemplateData']['AlphaBetaVersions'] = $AlphaBetaVersions;
		$_SESSION['SubVersions']  = $SubVersions;
		$_SESSION['AlphaBetaVersions']  = $AlphaBetaVersions;

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/license/versionForm.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	function getRegisterVersionResponse($version) {

		$_SESSION['clientTargetVersionNumber'] = $version;
		$_SESSION['clientTargetSubVersionNumber'] = update::getSubVersion($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionType'] = update::getVersionType($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionBranch'] = update::getOnlyVersionBranch($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersion'] = updateUtil::number2version($version);

		$_SESSION['clientVersionNumber'] = $_SESSION['clientTargetVersionNumber'];
		$_SESSION['clientVersion'] = $_SESSION['clientTargetVersion'];

		$ret = array (
			'Type' => 'eval',
			'Code' => '<?php return true; ?>',
		);
		return updateUtil::getResponseString($ret);

	}

}

?>