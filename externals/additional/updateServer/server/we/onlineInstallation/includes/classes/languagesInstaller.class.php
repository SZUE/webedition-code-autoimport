<?php
/**
 * $Id: languagesInstaller.class.php 13561 2017-03-13 13:40:03Z mokraemer $
 */

class languagesInstaller extends languagesBase{

	/**
	 * returns form to choose Languages
	 *
	 * @return string
	 */
	static function getLanguagesFormResponse(){

		// at least already installed languages can be reinstalled
		$GLOBALS['updateServerTemplateData']['installAbleLanguages'] = updateInstaller::getPossibleLanguagesArray();

		$ret = updateUtilInstaller::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/languages/languagesForm.inc.php');
		return updateUtilInstaller::getResponseString($ret);
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
		return updateUtilInstaller::getResponseString($ret);
	}

}
