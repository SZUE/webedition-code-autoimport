<?php
/**
 * $Id$
 */

class languages extends languagesBase{

	/**
	 * returns form to choose Languages
	 *
	 * @return string
	 */
	static function getLanguagesFormResponse(){

		// at least already installed languages can be reinstalled
		$GLOBALS['updateServerTemplateData']['installAbleLanguages'] = update::getPossibleLanguagesArray();

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/languagesForm.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	static function getRegisterLanguagesResponse($systemLanguage = '', $extraLanguages = array()){

		$_SESSION['clientSyslng'] = $systemLanguage;
		$_SESSION['clientDesiredLanguages'] = array_merge(array($systemLanguage), $extraLanguages);

		$ret = array(
			'Type' => 'eval',
			'Code' => '<?php return true; ?>',
		);
		return updateUtil::getResponseString($ret);
	}

}
