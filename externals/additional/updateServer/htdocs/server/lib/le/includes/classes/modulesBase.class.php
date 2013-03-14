<?php

class modulesBase {


	/**
	 * returns array with all existing modules and some information about the modules
	 *
	 * @return array
	 */
	function getExistingModules($forceSelect = false, $language = "") {
		global $DB_Versioning;

		if ( !isset($_SESSION['existingModules']) || $forceSelect) {

			if($language == "") {
				$language = $_SESSION['clientSyslng'];

			}
			$_SESSION['existingModules'] = array();

			$query = "
				SELECT PK_moduleKey, tblField_domains, tblField_we, grade, basismodule, dependent, strText
				FROM " . MODULES_INFORMATION . ", " . MODULES_TRANSLATIONS . "
				WHERE intVersion <= " . $_SESSION['clientVersionNumber'] . " AND intLive = 1 AND strLanguage LIKE \"" . $language . "\"
					AND PK_moduleKey = strModuleKey
			";

			$res =& $DB_Versioning->query($query);

			while ( $row = $res->fetchRow() ) {
				$_SESSION['existingModules'][$row['PK_moduleKey']] = array(
					'text' => $row['strText'],
					'grade' => $row['grade'],
					'STOCK_TABLE_field' => $row['tblField_we'],
					'INSTALLATION_TABLE_field' => $row['tblField_domains']
				);

				// occasionally add information about basis module for promodules
				// and information about module dependencies
				if($row['grade'] == "pro"){	//	ProModule - add basismodule
					$_SESSION['existingModules'][$row["PK_moduleKey"]]["basismodule"] = $row["basismodule"];

				}

				if($row['dependent'] != ""){
					$_SESSION['existingModules'][$row["PK_moduleKey"]]["dependent"] = $row["dependent"];

				}

			}

		}
		return $_SESSION['existingModules'];

	}


	/**
	 * checks if allowed module combination is allowed.
	 *
	 * @param array $desiredModules
	 * @return boolean
	 */
	function isDesiredModuleCombinationAllowed($desiredModules) {

		$existingModules = modules::getExistingModules();

		foreach ($desiredModules as $moduleKey) {
			$module = $existingModules[$moduleKey];

			// base module to desired promodule
			if ($module['grade'] == 'pro') {
				if ( !(in_array($module['basismodule'], $desiredModules) || in_array($module['basismodule'], $_SESSION['clientInstalledModules']))  ) {
					return false;

				}

			}

			// all modules this module depends from
			if (isset($module['dependent'])) {
				$depModules = explode(',', $module['dependent']);
				for ($i=0; $i<sizeof($depModules); $i++) {
					if ( !(in_array($depModules[$i], $desiredModules) || in_array($depModules[$i], $_SESSION['clientInstalledModules']))  ) {
						return false;

					}

				}

			}

		}
		return true;

	}

	/**
	 * returns code for installed modules
	 *
	 * @return string
	 */
	function getCodeForInstalledModules() {

		$existingModules = modules::getExistingModules();

		$installedModules = array();
		$installedProModules = array();

		// write all modules in we_installed modulesveUpdateServer.php?liveUpdateSession=ad6d6fc29eaeb51
		$allModules = $_SESSION['clientInstalledModules'];

		if (isset($_SESSION['clientDesiredModules'])) {
			$allModules = array_merge($allModules, $_SESSION['clientDesiredModules']);

		}
		$allModules = array_unique($allModules);

		foreach ($allModules as $moduleKey) {
			if( !in_array($moduleKey, $GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'])
				&& $moduleKey != 'customerpro'
				) {
				
				if ($existingModules[$moduleKey]['grade'] == 'pro') {
					$installedProModules[] = $moduleKey;
					
				// user management is integrated module now!
				// intern it is still handled as module
				// busers is now treated like a normal grade module
				// in module selection dialog it must be located in normal modules
				} else if ($moduleKey == "busers") {
					$installedProModules[] = $moduleKey;
					
				} else {
					$installedModules[] = $moduleKey;
	
				}
				
			}

		}

		$modules_content = '';
		$pro_modules_content = '';

		foreach ($installedModules as $moduleKey) {
			$modules_content .= "\$_we_installed_modules[] = \"$moduleKey\";\n";

		}

		foreach ($installedProModules as $moduleKey) {
			$pro_modules_content .= "\$_pro_modules[] = \"$moduleKey\";\n";

		}
		$newContent = '<?php

$_we_installed_modules = array();

' . $modules_content . '

$_pro_modules = array();

' . $pro_modules_content . '
?>';
		return $newContent;

	}

	/**
	 * returns code for installed modules
	 *
	 * @return string
	 */
	function getCodeForActiveIntegratedModules() {

		// write all active integrated modules
		$Content	=	'<?php' . "\n"
					.	'' . "\n"
					.	'$_we_active_integrated_modules = array();' . "\n";
		foreach ($GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'] as $moduleKey) {
			$_we_active_integrated_modules[] = $moduleKey;
			$Content .= '$_we_active_integrated_modules[] = "' . $moduleKey . '";' . "\n";

		}
		$Content	.=	'' . "\n"
					.	'?>';

		return $Content;

	}

}
?>