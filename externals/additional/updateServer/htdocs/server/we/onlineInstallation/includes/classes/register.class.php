<?php

class register extends registerBase {

	/**
	 * returns form to register webedition
	 *
	 * @return string
	 */
	function getRegisterFormResponse() {

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/registerForm.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	function getRegisterResponse($serial) {

		$_SESSION['clientSerial'] = $serial;

		$ret = array (
			'Type' => 'eval',
			'Code' => '<?php return true; ?>',
		);
		return updateUtil::getResponseString($ret);

	}


	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	function getDontRegisterResponse() {

		if(isset($_SESSION['clientSerial'])) unset($_SESSION['clientSerial']);

		$ret = array (
			'Type' => 'eval',
			'Code' => '<?php return true; ?>',
		);
		return updateUtil::getResponseString($ret);

	}


	/**
	 * returns form with error to register webedition
	 *
	 * @return string
	 */
	function getRegisterFormErrorResponse($serialstate) {

		$GLOBALS['updateServerTemplateData']['licenceError'] = $GLOBALS['lang']['license']['undefinedError'] . ': <code>' . $serialstate . '</code>';

		if (file_exists(SHARED_TEMPLATE_DIR . '/license/' . $serialstate . '.inc.php')) {
			$GLOBALS['updateServerTemplateData']['licenceError'] = updateUtil::getTemplateContentForResponse(SHARED_TEMPLATE_DIR . '/license/' . $serialstate . '.inc.php');

		}

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/registerFormError.inc.php');
		return updateUtil::getResponseString($ret);

	}

}

?>