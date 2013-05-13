<?php

class languages extends languagesBase {

	/**
	 * returns form to choose Languages
	 *
	 * @return string
	 */
	function getLanguagesFormResponse() {

		// at least already installed languages can be reinstalled
		$GLOBALS['updateServerTemplateData']['installAbleLanguages'] = update::getPossibleLanguagesArray();
		$GLOBALS['updateServerTemplateData']['installAbleBetaLanguages'] = update::getPossibleBetaLanguagesArray();

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/languagesForm.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	function getRegisterLanguagesResponse($systemLanguage = '', $extraLanguages = array()) {

		$_SESSION['clientSyslng'] = $systemLanguage;
		$_SESSION['clientDesiredLanguages'] = array_merge(array($systemLanguage), $extraLanguages);

		$ret = array (
			'Type' => 'eval',
			'Code' => '<?php return true; ?>',
		);
		return updateUtil::getResponseString($ret);

	}

}

?>