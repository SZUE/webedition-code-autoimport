<?php

class updateUtil extends updateUtilBase {

	/**
	 * @param string $detail
	 * @param boolean $nextWizardStep
	 * @param mixed $liveUpdateSession
	 * @return string
	 */
	function getCommonHrefParameters($detail, $nextWizardStep=false, $liveUpdateSession=false) {

		$paraStr =
			"leWizard=" . $_REQUEST["leWizard"] .
			"&leStep=" . ($nextWizardStep ? $_REQUEST["nextLeStep"] : $_REQUEST["leStep"] ) .
			"&update_cmd=" . $_REQUEST["update_cmd"];

		return "$paraStr&liveUpdateSession=" . ($liveUpdateSession ? $liveUpdateSession : session_id()) . "&detail=$detail";

	}

	/**
	 * returns array with search and replace string by name
	 *
	 * @param string $name
	 * @return array
	 */
	function getReplaceCode($name, $replacements=array()) {

		require(LIVEUPDATE_SERVER_DIR . "/includes/extras/replaceCode.inc.php");

		$ret = array();

		$ret['path'] = sprintf($replaceCode[$name]['path'][updateUtil::getNearestVersion($replaceCode[$name]['path'], $_SESSION['clientTargetVersionNumber'])], $_SESSION['clientExtension']);

		if (isset($replaceCode[$name]['needle'])) {
			$ret['needle'] = $replaceCode[$name]['needle'][updateUtil::getNearestVersion($replaceCode[$name]['needle'], $_SESSION['clientTargetVersionNumber'])];

		} else {
			$ret['needle'] = '';

		}

		if (isset($replaceCode[$name]['replace'])) {
			$replace = $replaceCode[$name]['replace'][updateUtil::getNearestVersion($replaceCode[$name]['replace'], $_SESSION['clientTargetVersionNumber'])];
			if (sizeof($replacements)) {
				$replace = vsprintf($replace, $replacements);

			}
			$ret['replace'] = $replace;
		}
		return $ret;

	}

	/**
	 * this function returns an array with all files (files, patches, queries)
	 * the client needs for the update, module installation or language installation
	 *
	 * @param array $query
	 * @return array
	 */
	function getChangesArrayByQueries($queryArray) {
		global $DB_Versioning;

		$changes['files'] = array();
		$changes['queries'] = array();
		$changes['patches'] = array();

		$changes['allChanges'] = array();

		foreach ($queryArray as $query) {
			$res =& $DB_Versioning->query($query);
			while ($row = $res->fetchRow()) {

				$changesDb = explode(',', $row['changes']);
				$pathPrefix = '../../files/we/version' . $row['version'] . '/';

				$clientPathPrefix = '/tmp/';

				switch ($row['detail']) {
					case 'files':
						$pathPrefix .= 'files/' . $_SESSION['clientEncoding'] . '/';
						$clientPathPrefix .= 'files/';
						break;

					case 'patches':
						$pathPrefix .= 'patches/';
						$clientPathPrefix .= 'patches/';
						break;

					case 'queries':
						$pathPrefix .= 'queries/';
						$clientPathPrefix .= 'queries/';
						break;

				}

				foreach ($changesDb as $change) {
					if (!isset($changes[$row['detail']]['LE_INSTALLER_TEMP_PATH . "/' . $clientPathPrefix . trim($change) . '"'])) {
						$changes[$row['detail']]['LE_INSTALLER_TEMP_PATH . "/' . $clientPathPrefix . trim($change) . '"'] = $pathPrefix . trim($change);
						$changes['allChanges']['LE_INSTALLER_TEMP_PATH . "/' . $clientPathPrefix . trim($change) . '"'] = $pathPrefix . trim($change);

					}

				}

			}

		}
		return $changes;

	}
	function getLastSnapShot($targetVersionNumber) {
		global $DB_Versioning;
		$query = "SELECT * FROM " . VERSION_TABLE . " WHERE isSnapshot='1' AND version <= '".$targetVersionNumber."' ORDER BY version DESC ";
		$res =& $DB_Versioning->query($query);
		$anzahl = & $DB_Versioning->numCols();
		if($anzahl > 0) {
			$row = $res->fetchRow(); 
			return 	$row['version'];
		} else {
			return 6000;
		}
	}

}

?>