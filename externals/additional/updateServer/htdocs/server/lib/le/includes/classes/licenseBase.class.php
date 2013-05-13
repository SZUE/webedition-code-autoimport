<?php
/**
 * base class for all issues concerning the license
 *
 */
class licenseBase {

	/**
	 * returns licensee by weId
	 * This is written when software is registered
	 *
	 * @param integer $id
	 * @return string
	 */
	function getLicensee($id) {

		global $DB_Register;

		$query = '
			SELECT strFirstName, strLastName, strCompany
			FROM ' . CUSTOMER_TABLE . '
			WHERE weID=' . $id . '
		';

		$res =& $DB_Register->query($query);
		$row = $res->fetchRow();

		if (!empty($row['strCompany'])) {
			return $row['strCompany'];

		} else {
			return $row['strLastName'] . ' ' . $row['strFirstName'];

		}

	}

	/**
	 * returns stocktable id by serial
	 *
	 * @param serial $serial
	 * @return integer
	 */
	function getStockTableIdBySerial($serial) {
		global $DB_Register;

		$query = '
			SELECT id
			FROM ' . STOCK_TABLE . '
			WHERE strKey LIKE BINARY "' . addslashes($serial) . '"
		';

		$res =& $DB_Register->query($query);
		$row = $res->fetchRow();

		return $row['id'];

	}


	/**
	 * Transforms a via request transmitted serial in another form
	 * if needed
	 *
	 * @param string $serial
	 * @return string
	 */
	function formatSerial($serial) {
		return str_replace('-', '', $serial);

	}

	/**
	 * This function checks, if a registration with the submitted serial is
	 * allowed, or if there are any problems with this serial.
	 *
	 * @param string $serial
	 * @return state
	 */
	function checkSerialState($serial) {

		/* *************************************************************** */
		/* Strato serials are only allowed in a certain IP-Range
		/* *************************************************************** */
		if(substr($serial,0,6) == "STRATO"){
			if(!updateUtil::isStratoIp($_SERVER['REMOTE_ADDR'])){
				return 'noStratoIp';
			}
		}
		
		/* *************************************************************** */
		/* Wirtualna Polska serials are only allowed in a certain IP-Range
		/* *************************************************************** */
		if(substr($serial,0,7) == "WPOLSKA"){
			if(!updateUtil::isWpolskaIp($_SERVER['REMOTE_ADDR'])){
				return 'noWpolskaIp';
			}
		}

		// get all information about this serial
		$serialInfo = license::getSerialInformation($serial);
		
		if ($serialInfo['stockTableId']) { // serial exists?

			if( $serialInfo['upgrades']['version5'] ){	//	version 5 is bought ?
				
				// version5 is bought, check if its ok to install on this domain
				$domainInfo = license::getRegisteredDomainInformation($_SESSION['clientDomain'], $serialInfo['stockTableId']);

				if ($domainInfo['id']) { // there is already a webEdition version4 on this server
					
					// check if the needed updates are available
					if ((in_array('version3', $domainInfo['registeredUpgrades']) || $serialInfo['upgrades']['version3'] > $serialInfo['installedUpgrades']['version3']) &&
						(in_array('version4', $domainInfo['registeredUpgrades']) || $serialInfo['upgrades']['version4'] > $serialInfo['installedUpgrades']['version4']) &&
						(in_array('version5', $domainInfo['registeredUpgrades']) || $serialInfo['upgrades']['version5'] > $serialInfo['installedUpgrades']['version5'])	) {

						// check if all installed modules are licensed
						$GLOBALS['missingModuleLicenses'] = license::getMissingModuleLicenses($_SESSION['clientInstalledModules'], $domainInfo['registeredModules'], $serialInfo['modules'], $serialInfo['installedModules']);
						
						if (sizeof($GLOBALS['missingModuleLicenses'])) {
							return 'missingModuleLicenses';
						} else {
							return 'ok';
						}

					} else if ( // this was an upgrade of a weClassic version
							($serialInfo['installedWeClassic'] || $serialInfo['installedWeClassic'] > $serialInfo['weClassic']) &&
							(in_array('version5', $domainInfo['registeredUpgrades']) || $serialInfo['upgrades']['version5'] > $serialInfo['installedUpgrades']['version5'])
							
						) {
							$GLOBALS['missingModuleLicenses'] = license::getMissingModuleLicenses($_SESSION['clientInstalledModules'], $domainInfo['registeredModules'], $serialInfo['modules'], $serialInfo['installedModules']);
							if (sizeof($GLOBALS['missingModuleLicenses'])) {
								return 'missingModuleLicenses';
								
							} else {
								$GLOBALS['thisWasClassicInstallation'] = true;
								return 'ok';
								
							}
					} else {
						return 'notEnoughVersions';
						
					}

				} else { // this is first installation of webEdition 5 on this server
					
					// check if all desired is allowed
					if ($serialInfo['upgrades']['version5'] > $serialInfo['installedUpgrades']['version5'] &&
						$serialInfo['upgrades']['version4'] > $serialInfo['installedUpgrades']['version4'] &&
						$serialInfo['upgrades']['version3'] > $serialInfo['installedUpgrades']['version3'] &&
						$serialInfo['basis'] > $serialInfo['installedBasis']  ) {

						// check if all installed modules are licensed
						$GLOBALS['missingModuleLicenses'] = license::getMissingModuleLicenses($_SESSION['clientInstalledModules'], $domainInfo['registeredModules'], $serialInfo['modules'], $serialInfo['installedModules']);

						if (sizeof($GLOBALS['missingModuleLicenses'])) {
							return 'missingModuleLicenses';
						} else {
							return 'ok';
						}

					} else if ( // this was an upgrade of a weClassic version
							( $serialInfo['weClassic'] > $serialInfo['installedWeClassic'] ) &&
							( $serialInfo['upgrades']['version5'] > $serialInfo['installedUpgrades']['version5'] )
							
						) {
							$GLOBALS['missingModuleLicenses'] = license::getMissingModuleLicenses($_SESSION['clientInstalledModules'], $domainInfo['registeredModules'], $serialInfo['modules'], $serialInfo['installedModules']);
							if (sizeof($GLOBALS['missingModuleLicenses'])) {
								return 'missingModuleLicenses';
								
							} else {
								$GLOBALS['thisWasClassicInstallation'] = true;
								return 'ok';
								
							}
					} else {
						return 'notEnoughVersions';
					}
				}

			} else {
				return 'noVersion5';
			}

		}

		return 'serialNotExist';

	}

	/**
	 * Returns an associative array with information about the domain saved in
	 * INSTALLATION_TABLE
	 *
	 * @param string $domain
	 * @param integer $stocktableId
	 * @return array
	 */
	function getRegisteredDomainInformation($domain, $stocktableId) {

		global $DB_Register;

		$orText = '';

		$domainInfo = array();

		$domainInfo['id'] = 0;
		$domainInfo['registeredModules'] = array();
		$domainInfo['registeredUpgrades'] = array();

		if(substr($domain, 0, 4) == "www."){
			$orText = " OR strDomainname LIKE \"" . substr($domain, 4) . "\"";
		} else {
			$orText = " OR strDomainname LIKE \"www." . $domain . "\"";
		}

		$query = "
			SELECT *
			FROM " . INSTALLATION_TABLE . "
			WHERE
				( strDomainname LIKE \"$domain\" $orText ) AND weID =\"$stocktableId\"
		";

		$res =& $DB_Register->query($query);
		if ($row = $res->fetchRow()) {

			// id
			$domainInfo['id'] = $row['id'];

			// modules
			$moduleInformation = modules::getExistingModules();
			foreach ($moduleInformation as $moduleKey => $moduleInfo) {

				if ($row[$moduleInfo['INSTALLATION_TABLE_field']]) {
					if(!in_array($moduleKey, $GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'])) {
						$domainInfo['registeredModules'][] = $moduleKey;

					}
					//$domainInfo['registeredModules'][] = $moduleKey;

				}


			}


			// updates
			if ($row['intVersion3']) {
				$domainInfo['registeredUpgrades'][] = 'version3';
			}
			if ($row['intVersion4']) {
				$domainInfo['registeredUpgrades'][] = 'version4';
			}
			if ($row['intVersion5']) {
				$domainInfo['registeredUpgrades'][] = 'version5';
			}
			return $domainInfo;
		}
		return $domainInfo;
	}

	/**
	 * @param integer $installationTableId
	 * @return array
	 */
	function getRegisteredDomainInformationById( $installationTableId ){

		global $DB_Register;

		$domainInfo = array();

		$domainInfo['id'] = 0;
		$domainInfo['registeredModules'] = array();
		$domainInfo['registeredUpgrades'] = array();

		// installed modules
		$query = "
			SELECT *
			FROM " . INSTALLATION_TABLE . "
			WHERE
				id = \"$installationTableId\"
		";

		$res =& $DB_Register->query($query);

		if ($row = $res->fetchRow()) {

			// id
			$domainInfo['id'] = $row['id'];

			// existing modules
			$moduleInformation = modules::getExistingModules();

			foreach ($moduleInformation as $moduleKey => $moduleInfo) {

				if ($row[$moduleInfo['INSTALLATION_TABLE_field']]) {
					if(!in_array($moduleKey, $GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'])) {
						$domainInfo['registeredModules'][] = $moduleKey;

					}
					//$domainInfo['registeredModules'][] = $moduleKey;

				}


			}

			// updates
			if ($row['intVersion3']) {
				$domainInfo['registeredUpgrades'][] = 'version3';
			}
			if ($row['intVersion4']) {
				$domainInfo['registeredUpgrades'][] = 'version4';
			}
			if ($row['intVersion5']) {
				$domainInfo['registeredUpgrades'][] = 'version5';
			}
			return $domainInfo;
		}
		return $domainInfo;
	}

	/**
	 * Gathers all infomration about the stock of the given serial, including
	 * - amount of bought software, updates and modules
	 * - amount of already installed software, updates and modules
	 *
	 * @param string $serial
	 * @return array
	 */
	function getSerialInformation($serial){

		global $DB_Register;

		// serialinformation contains all information about this serial
		$serialInformation = array();
		$serialInformation['stockTableId'] = 0;

		// baseVersions
		$serialInformation['basis'] = 0;
		$serialInformation['installedBasis'] = 0;
		
		// WE_CLASSIC !!!!!!!!
		$serialInformation['weClassic'] = 0;
		$serialInformation['installedWeClassic'] = 0;
		
		// modules (associative)
		$serialInformation['modules'] = array();
		$serialInformation['installedModules'] = array();

		// higher versions (associative)
		$serialInformation['upgrades'] = array();
		$serialInformation['installedUpgrades'] = array();


		$query = '
			SELECT *
			FROM ' . STOCK_TABLE . '
			WHERE strKey LIKE BINARY "' . addslashes($serial) . '"
		';

		$res =& $DB_Register->query($query);

		if ( $row = $res->fetchRow() ) {

			// take stock
			$serialInformation['stockTableId'] = $row['id'];

			// basis
			$serialInformation['basis'] = $row['intTyp'];
			$serialInformation['installedBasis'] = 0;
			
			// WE_CLASSIC !!!!!!!!
			$serialInformation['weClassic'] = $row['intWeClassic'];

			// upgrades
			$serialInformation['upgrades'] = array();
			$serialInformation['upgrades']['version3'] = $row['intVersion3'];
			$serialInformation['upgrades']['version4'] = $row['intVersion4'];
			$serialInformation['upgrades']['version5'] = $row['intVersion5'];

			$serialInformation['installedUpgrades'] = array();
			$serialInformation['installedUpgrades']['version3'] = 0;
			$serialInformation['installedUpgrades']['version4'] = 0;
			$serialInformation['installedUpgrades']['version5'] = 0;

			// add modules
			$existingModules = modules::getExistingModules();

			$serialInformation['modules'] = array();
			$serialInformation['installedModules'] = array();

			$installedModulesQuery = '';

			foreach ($existingModules as $moduleKey => $moduleInformation) {

				if(in_array($moduleKey, $GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'])) {
					$serialInformation['modules'][$moduleKey] = 1;
					$serialInformation['installedModules'][$moduleKey] = 0;

				} else {
					$serialInformation['modules'][$moduleKey] = $row[$moduleInformation['STOCK_TABLE_field']];
					$serialInformation['installedModules'][$moduleKey] = 0;
					$installedModulesQuery .= ", SUM(" . $moduleInformation["INSTALLATION_TABLE_field"] . ") AS installed" . $moduleKey;

				}

			}

			// fill already used versions
			$installedQuery = '
				SELECT SUM(intWeClassic) as installedWeClassic, SUM(intVersion3) as installedVersion3, SUM(intVersion4) as installedVersion4, SUM(intVersion5) as installedVersion5, COUNT(id) as installedBasis ' . $installedModulesQuery . '
				FROM ' . INSTALLATION_TABLE . '
				WHERE weID = ' . $serialInformation['stockTableId'] . '
				GROUP BY weID
			';

			$res =& $DB_Register->query($installedQuery);

			if ($row = $res->fetchRow()) {

				// installed basis
				$serialInformation['installedBasis'] = $row['installedBasis'];

				// WE_CLASSIC !!!!!!!!
				$serialInformation['installedWeClassic'] = $row['installedWeClassic'];
				
				// upgrades
				$serialInformation['installedUpgrades']['version3'] = $row['installedVersion3'];
				$serialInformation['installedUpgrades']['version4'] = $row['installedVersion4'];
				$serialInformation['installedUpgrades']['version5'] = $row['installedVersion5'];

				// installed modules
				foreach ($existingModules as $moduleKey => $moduleInformation) {
					$serialInformation['installedModules'][$moduleKey] = $row['installed' . $moduleKey];

				}

			}

		}
		return $serialInformation;

	}


	/**
	 * Checks, if there exists already an entry for this installation in the
	 * INSTALLATION_TABLE and returns occasionally its id. This function is
	 * needed to check, if an update, moduleinstallation, etc is allowed, or a
	 * re-registration is necessary
	 *
	 * @param string $domain
	 * @param string $field
	 * @param integer $stocktableId
	 * @return boolean
	 */
	function checkDomain($domain, $stocktableId) {
		global $DB_Register;

		$orText = '';

		if(substr($domain, 0, 4) == "www."){
			$orText = " OR strDomainname LIKE \"" . substr($domain, 4) . "\"";

		} else {
			$orText = " OR strDomainname LIKE \"www." . $domain . "\"";

		}

		$versionCond = 'intVersion3=1 AND intVersion4=1 AND intVersion5=1 AND';
		$versionCond = ''; // bugfix install 5 with already installed version 4

		$query = "
			SELECT id
			FROM " . INSTALLATION_TABLE . "
			WHERE
				$versionCond
				( strDomainname LIKE \"$domain\" $orText ) AND weID =\"$stocktableId\"
		";

		$res =& $DB_Register->query($query);
		if ($row = $res->fetchRow()) {
			return $row['id'];

		}

		return 0;

	}

	/**
	 * returns id of domain in INSTALLATION_TABLE by domainname and uid
	 *
	 * @param string $domain
	 * @param integer $stocktableId
	 * @param boolean $checkVersion4
	 * @return boolean
	 */
	function getDomainId($domain, $uid, $checkVersion5=true){
		global $DB_Register;

		$orText = '';

		if(substr($domain, 0, 4) == "www."){
			$orText = " OR strDomainname LIKE \"" . substr($domain, 4) . "\"";

		} else {
			$orText = " OR strDomainname LIKE \"www." . $domain . "\"";

		}
		
		if ($_SESSION['clientWE_CLASSIC']) { // this is only possible when upgrading we4 => we5
			$query = "
			SELECT id
			FROM " . INSTALLATION_TABLE . "
			WHERE 
				intWeClassic=1 AND intVersion3=0 AND intVersion4=0
				AND ( strDomainname LIKE \"$domain\" $orText ) AND lifeUpdate =\"$uid\"
			";
			
		} else if(isset($_SESSION["clientWE_LIGHT"]) && $_SESSION["clientWE_LIGHT"]) {
			/*
			 * we5light:	intVersion5light = 1
			 * we5 upgrade:	intVersion5light = 1, intVersion5=1
			 */
			if($_REQUEST['update_cmd'] == "upgrade") {
				//error_log("upgrade we5light -> we5");
				//$checkVersion5 = true;
				$query = "
				SELECT id
				FROM " . INSTALLATION_TABLE . "
				WHERE
					(
						(intVersion5light=1 AND intVersion3=1 AND intVersion4=1 " . ($checkVersion5 ? 'AND intVersion5=1' : '') . ")
						OR
						(intVersion5light=1 " . ($checkVersion5 ? 'AND intVersion5=1' : '') . ")
					)
					AND ( strDomainname LIKE \"$domain\" $orText ) AND lifeUpdate =\"$uid\"
				";
			} else {
				//error_log("update we5light");
				$query = "
				SELECT id
				FROM " . INSTALLATION_TABLE . "
				WHERE 
					intVersion5light=1 " . ($checkVersion5 ? 'AND intVersion5=1' : '') . "
					AND ( strDomainname LIKE \"$domain\" $orText ) AND lifeUpdate =\"$uid\"
				";
			}
			
		} else { // domain is valid, if weClassic and we5 are registered
			$query = "
				SELECT id
				FROM " . INSTALLATION_TABLE . "
				WHERE
					(
						(intVersion3=1 AND intVersion4=1 " . ($checkVersion5 ? 'AND intVersion5=1' : '') . ")
						OR
						(intWeClassic=1 " . ($checkVersion5 ? 'AND intVersion5=1' : '') . ")
						OR
						(intVersion5light=1 " . ($checkVersion5 ? 'AND intVersion5=1' : '') . ")
					)
					AND ( strDomainname LIKE \"$domain\" $orText ) AND lifeUpdate =\"$uid\"
			";
		}

		$res =& $DB_Register->query($query);
		if ($row = $res->fetchRow()) {
			return $row['id'];

		}
		return 0;

	}


	/**
	 * @param string $uid
	 * @return string
	 */
	function getSerialByUid($uid='') {
		global $DB_Register;

		if (!$uid) {
			$uid = $_SESSION['clientUid'];

		}

		$query = '
			SELECT strKey
			FROM ' . STOCK_TABLE . ',' . INSTALLATION_TABLE . '
			WHERE ' . STOCK_TABLE . '.id=weID AND lifeUpdate LIKE BINARY "' . $uid . '"
		';

		$res =& $DB_Register->query($query);
		if ($row = $res->fetchRow()) {
			return $row['strKey'];

		}

		return '';

	}

	/**
	 * enters registration information to database
	 *
	 * @param string $uid
	 * @param integer $stockTableId
	 * @param integer $domainId
	 * @return boolean
	 */
	function insertRegistration($uid, $stockTableId, $domainId = 0) {

		global $DB_Register;

		// always new entry for localhost installations
		$isLocalhost = updateUtil::isLocalhost($_SESSION['clientDomain']);
		
		// don't forget modules
		$allModules = modules::getExistingModules();
		
		if ($isLocalhost || !$domainId) { // insert new entry

			$queryModuleFields = '';
			$queryModuleValues = '';
			// query for modules

			foreach ($allModules as $moduleKey => $moduleInformation) {

				$queryModuleFields .= ", " . $moduleInformation["INSTALLATION_TABLE_field"];
				if (in_array($moduleKey, $_SESSION['clientInstalledModules'])) {
					$queryModuleValues .= ', 1';
				} else {
					$queryModuleValues .= ', 0';
				}
			}

			foreach ($GLOBALS['MODULES_FREE_OF_CHARGE_DOMAINFIELDS'] as $moduleKey => $queryModuleField) {

				$queryModuleFields .= ", " . $queryModuleField;
				$queryModuleValues .= ', 0';
			}

			// new entry -> only insert on domain installed modules
			
			if ( isset($GLOBALS['thisWasClassicInstallation']) && $GLOBALS['thisWasClassicInstallation'] ) { // weClassic
				$query = "
					INSERT INTO " . INSTALLATION_TABLE . "
					(weID, lifeUpdate, strDomainname, strIp, dateDate, intWeClassic, intVersion3, intVersion4, intVersion5 $queryModuleFields)
					VALUES('$stockTableId', '$uid', '" . $_SESSION['clientDomain'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', now(), 1, 0, 0, 1 " . $queryModuleValues . ")
				";
				
			} else {
				$query = "
					INSERT INTO " . INSTALLATION_TABLE . "
					(weID, lifeUpdate, strDomainname, strIp, dateDate, intWeClassic, intVersion3, intVersion4, intVersion5 $queryModuleFields)
					VALUES('$stockTableId', '$uid', '" . $_SESSION['clientDomain'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', now(), 0, 1, 1, 1 " . $queryModuleValues . ")
				";
				
			}
			

		} else { // update existing entry

			// update entry -> update installed modules to domain

			$queryModules = '';
			// query for modules
			foreach ($allModules as $moduleKey => $moduleInformation) {

				if (in_array($moduleKey, $_SESSION['clientInstalledModules'])) {
					$queryModules .= ', ' . $moduleInformation["INSTALLATION_TABLE_field"] . '=1';
				}
			}

			foreach ($GLOBALS['MODULES_FREE_OF_CHARGE_DOMAINFIELDS'] as $moduleKey => $queryModuleField) {

				$queryModules .= ', ' . $queryModuleField . '=0';
			}
			
			if ( isset($GLOBALS['thisWasClassicInstallation']) && $GLOBALS['thisWasClassicInstallation'] ) { // weClassic
				$query = "
					UPDATE " . INSTALLATION_TABLE . "
					SET lifeUpdate='$uid', strIp='" . $_SERVER['REMOTE_ADDR'] . "', dateDate=NOW(), intWeClassic=1, intVersion3=0, intVersion4=0, intVersion5=1" . $queryModules . "
					WHERE id=\"$domainId\"
				";
				
			} else {
				$query = "
					UPDATE " . INSTALLATION_TABLE . "
					SET lifeUpdate='$uid', strIp='" . $_SERVER['REMOTE_ADDR'] . "', dateDate=NOW(), intWeClassic=0, intVersion3=1, intVersion4=1, intVersion5=1" . $queryModules . "
					WHERE id=\"$domainId\"
				";
				
			}
			
		}

		if ($res =& $DB_Register->query($query)) {
			return true;
		} else {
			return false;
		}
	}


	function insertUpgradeInformation($domainId) {
		global $DB_Register;

		$queryModules = '';
		// query for modules
		foreach ($GLOBALS['MODULES_FREE_OF_CHARGE_DOMAINFIELDS'] as $moduleKey => $queryModuleField) {

			$queryModules .= ', ' . $queryModuleField . '=0';
		}

		$query = "
			UPDATE " . INSTALLATION_TABLE . "
			SET intVersion5=1
			$queryModules
			WHERE id=\"$domainId\"
		";

		if ($res =& $DB_Register->query($query)) {
			return true;

		} else {
			return false;

		}

	}


	function insertNewModules($desiredModules) {
		global $DB_Register;

		if (sizeof($desiredModules)) {

			$tblDomainId = license::getDomainId($_SESSION['clientDomain'], $_SESSION['clientUid']);

			$existingModules = modules::getExistingModules();

			$moduleQuery = '';
			// query for modules
			for ($i=0; $i<sizeof($desiredModules); $i++) {

				if ($i) {
					$moduleQuery .= ',';
				}
				$moduleQuery .= $existingModules[$desiredModules[$i]]["INSTALLATION_TABLE_field"] . '=1';
			}

			$query = "
				UPDATE " . INSTALLATION_TABLE . "
				SET $moduleQuery
				WHERE id=\"$tblDomainId\"
			";

			if ($res =& $DB_Register->query($query)) {
				return true;

			} else {
				return false;

			}

		}

	}


	/**
	 * checks if license has enough modules left.
	 *
	 * @param array $desiredModules
	 * @return boolean
	 */
	function hasLicensesForDesiredModules($desiredModules) {

		// already installed
		$serial = license::getSerialByUid();
		$serialInformation = license::getSerialInformation($serial);

		$stockTableId = license::getStockTableIdBySerial($serial);
		$registeredInformation = license::getRegisteredDomainInformationById( $_SESSION['clientInstalledTableId'] );

		foreach ($desiredModules as $moduleKey) {
			// registered on domain || already installed || module available
			if (  !(in_array($moduleKey, $registeredInformation['registeredModules']) || in_array($moduleKey, $_SESSION['clientInstalledModules']) || ($serialInformation['modules'][$moduleKey] > $serialInformation['installedModules'][$moduleKey]))  ) {
				return false;

			}

		}
		return true;

	}


	function getMissingModuleLicenses($modulesOnDomain, $licensedOnDomain, $modulesTotal, $modulesInUse) {

		$notLicensedModules = array();
		$moduleInformation = modules::getExistingModules();

		// customerpro does not exist anymore as extra module
		foreach ($modulesOnDomain as $module) {
			if (  !(in_array($module, $licensedOnDomain) || $modulesTotal[$module] > $modulesInUse[$module]) && $module != 'customerpro' ) {
				$notLicensedModules[$module] = $moduleInformation[$module]['text'];
			}

		}
		return $notLicensedModules;

	}

}
?>