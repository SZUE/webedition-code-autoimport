<?php

class updateUtil extends updateUtilBase{

	/**
	 * @param string $detail
	 * @param boolean $nextWizardStep
	 * @param mixed $liveUpdateSession
	 * @return string
	 */
	static function getCommonHrefParameters($detail, $nextWizardStep = false, $liveUpdateSession = false){

		$paraStr = "leWizard=" . $_REQUEST["leWizard"] .
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
	static function getReplaceCode($name, $replacements = array()){

		require(LIVEUPDATE_SERVER_DIR . "/includes/extras/replaceCode.inc.php");

		$ret = array(
			'path' => sprintf($replaceCode[$name]['path'][updateUtil::getNearestVersion($replaceCode[$name]['path'], $_SESSION['clientTargetVersionNumber'])], $_SESSION['clientExtension']),
			'needle' => (isset($replaceCode[$name]['needle']) ?
				$replaceCode[$name]['needle'][updateUtil::getNearestVersion($replaceCode[$name]['needle'], $_SESSION['clientTargetVersionNumber'])] :
				'')
		);

		if(isset($replaceCode[$name]['replace'])){
			$replace = $replaceCode[$name]['replace'][updateUtil::getNearestVersion($replaceCode[$name]['replace'], $_SESSION['clientTargetVersionNumber'])];
			$ret['replace'] = ($replacements ?
					vsprintf($replace, $replacements) :
					$replace);
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
	static function getChangesArrayByQueries($queryArray){
		$changes = array(
			'files' => array(),
			'queries' => array(),
			'patches' => array(),
			'allChanges' => array(),
		);

		foreach($queryArray as $query){
			$GLOBALS['DB_WE']->query($query);
			while($GLOBALS['DB_WE']->next_record()){
				$row = $GLOBALS['DB_WE']->getRecord();
				$changesDb = explode(',', $row['changes']);
				$pathPrefix = '../../files/we/version' . $row['version'] . '/';

				$clientPathPrefix = '/tmp/';

				switch($row['detail']){
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

				foreach($changesDb as $change){
					if(!isset($changes[$row['detail']]['LE_INSTALLER_TEMP_PATH . "/' . $clientPathPrefix . trim($change) . '"'])){
						$changes[$row['detail']]['LE_INSTALLER_TEMP_PATH . "/' . $clientPathPrefix . trim($change) . '"'] = $pathPrefix . trim($change);
						$changes['allChanges']['LE_INSTALLER_TEMP_PATH . "/' . $clientPathPrefix . trim($change) . '"'] = $pathPrefix . trim($change);
					}
				}
			}
		}
		return $changes;
	}

	static function getLastSnapShot($targetVersionNumber){
		$row = $GLOBALS['DB_WE']->getHash('SELECT version FROM ' . VERSION_TABLE . " WHERE isSnapshot='1' AND version <= '" . $targetVersionNumber . "' ORDER BY version DESC ");

		if($row){
			return $row['version'];
		}
		return 6000;
	}

}
