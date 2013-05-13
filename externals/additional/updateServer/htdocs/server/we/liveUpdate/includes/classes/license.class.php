<?php

class license extends licenseBase {


	/**
	 * checks, if installed modules are licensed, and
	 * if licensed modules are installed
	 *
	 * @return boolean
	 */
	function areInstalledModulesLicensed($domainId) {

		$domainInfo = license::getRegisteredDomainInformationById($domainId);
		
		$licensedModules = $domainInfo['registeredModules'];
		for ($i=0; $i<sizeof($_SESSION['clientInstalledModules']); $i++) {
			if (		$_SESSION['clientInstalledModules'][$i] != "customerpro" // Customer Pro doesn't exists since version 5
					&&	!in_array($_SESSION['clientInstalledModules'][$i], $licensedModules)
					&&	!in_array($_SESSION['clientInstalledModules'][$i], $GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'])) {
				// installed NOT licensed
				return false;

			}

		}

		for ($i=0; $i<sizeof($licensedModules); $i++) {
			if (		$licensedModules[$i] != "customerpro" // Customer Pro doesn't exists since version 5
					&&	!in_array($licensedModules[$i], $_SESSION['clientInstalledModules'])
					&&	!in_array($licensedModules[$i], $GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'])) {
				// licensed NOT installed
				return false;

			}

		}
		return true;

	}


	function hasEnoughLicensesForUpgrade() {
		
		$serial = license::getSerialByUid($_SESSION['clientUid']);
		$serialInformation = license::getSerialInformation($serial);
		
		if ($serialInformation['upgrades']['version5'] > $serialInformation['installedUpgrades']['version5']) {
			return true;

		} else {
			// check if there was already an installation of version 5 on this domain
			$domainInformation = license::getRegisteredDomainInformationById($_SESSION['clientInstalledTableId']);
			if (in_array('version5', $domainInformation['registeredUpgrades'])) {
				return true;
				
			}
		}
		
		return false;
	}
}
?>