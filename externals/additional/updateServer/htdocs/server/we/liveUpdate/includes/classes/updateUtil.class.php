<?php

class updateUtil extends updateUtilBase{

	/**
	 * returns always needed string for response
	 *
	 * @param string $update_cmd
	 * @param string $detail
	 * @param string $liveUpdateSession
	 * @return string
	 */
	static function getCommonHrefParameters($update_cmd, $detail, $liveUpdateSession = false){

		return "liveUpdateSession=" . ($liveUpdateSession ? $liveUpdateSession : session_id()) . "&update_cmd=$update_cmd&detail=$detail";
	}

	/**
	 * returns array with search and replace string by name
	 *
	 * @param string $name
	 * @return array
	 */
	static function getReplaceCode($name, $replacements = array()){
		global $replaceCode;

		$ret = array();
		$ret['path'] = sprintf($replaceCode[$name]['path'][updateUtil::getNearestVersion($replaceCode[$name]['path'], $_SESSION['clientVersionNumber'])], $_SESSION['clientExtension']);

		if(isset($replaceCode[$name]['needle'])){
			$ret['needle'] = $replaceCode[$name]['needle'][updateUtil::getNearestVersion($replaceCode[$name]['needle'], $_SESSION['clientVersionNumber'])];
		} else {
			$ret['needle'] = '';
		}

		if(isset($replaceCode[$name]['replace'])){
			$replace = $replaceCode[$name]['replace'][updateUtil::getNearestVersion($replaceCode[$name]['replace'], $_SESSION['clientVersionNumber'])];

			if(!empty($replacements)){
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
	static function getChangesArrayByQueries(array $queryArray){
		$changes = [
			'files' => [],
			'queries' => [],
			'patches' => [],
			'allChanges' => []
		];

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
					if(!isset($changes[$row['detail']]['LIVEUPDATE_CLIENT_DOCUMENT_DIR . "' . $clientPathPrefix . trim($change) . '"'])){
						$changes[$row['detail']]['LIVEUPDATE_CLIENT_DOCUMENT_DIR . "' . $clientPathPrefix . trim($change) . '"'] = $pathPrefix . trim($change);
						$changes['allChanges']['LIVEUPDATE_CLIENT_DOCUMENT_DIR . "' . $clientPathPrefix . trim($change) . '"'] = $pathPrefix . trim($change);
					}
				}
			}
		}
		return $changes;
	}

	static function getLastSnapShot($targetVersionNumber){
		$row = $GLOBALS['DB_WE']->getHash("SELECT version FROM " . VERSION_TABLE . " WHERE isSnapshot=1 AND version<=" . intval($targetVersionNumber) . " ORDER BY version DESC LIMIT 1");
		return ($row ? $row['version'] : 6000);
	}

}
