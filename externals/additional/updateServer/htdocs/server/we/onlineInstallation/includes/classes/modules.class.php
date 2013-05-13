<?php

class modules extends modulesBase {

	/**
	 * returns form to choose modules
	 *
	 * @return string
	 */
	function getModulesFormResponse() {

//		if(!isset($_SESSION['clientSerial'])) {
//			$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/modules/modulesNoSerial.inc.php');
//			return updateUtil::getResponseString($ret);
//
//		}
		if(!isset($_SESSION['clientSerial'])) $_SESSION['clientSerial'] = "";
		$serialInformation = license::getSerialInformation($_SESSION['clientSerial']);

		$existingModules = modules::getExistingModules(true, $_SESSION['clientSyslng']);
		$installAbleModules = array();

		// which modules can be installed?
		$_SESSION['availableModules'] = array();
		$_SESSION['availableModules'] = $existingModules;
//		error_log(print_r($existingModules,true));
//		error_log(print_r($_SESSION['availableModules'],true));
//		foreach ($serialInformation['modules'] as $moduleKey => $amount) {
//			if ($amount > $serialInformation['installedModules'][$moduleKey]) {
//				$installAbleModules[$moduleKey] = $existingModules[$moduleKey]['text'];
//
//			}
//
//		}
//		error_log(print_r($installAbleModules,true));
		foreach($existingModules as $_name => $_module) {
			$installAbleModules[$_name] = $_module["text"];
		}
//		error_log(print_r($installAbleModules,true));
		if (sizeof($installAbleModules)) {

			$temp = license::getRegisteredDomainInformation($_SESSION['clientDomain'], $serialInformation['stockTableId']);
			$GLOBALS['updateServerTemplateData']['registeredModules'] = $temp['registeredModules'];
			
			$GLOBALS['updateServerTemplateData']['installAbleModules'] = $installAbleModules;
			$GLOBALS['updateServerTemplateData']['existingModules'] = $existingModules;

			$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/modules/modulesForm.inc.php');
			return updateUtil::getResponseString($ret);

		} else {
			$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/modules/modulesNoInstallable.inc.php');
			return updateUtil::getResponseString($ret);

		}

	}


	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	function getRegisterModulesResponse($Modules = array()) {

		$_SESSION['clientDesiredModules'] = array_keys($Modules);
		
		$ret = array (
			'Type' => 'eval',
			'Code' => '<?php return true; ?>',
		);
		return updateUtil::getResponseString($ret);

	}

}
?>