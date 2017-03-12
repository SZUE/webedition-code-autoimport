<?php
/**
 * $Id$
 */

abstract class updateBase{

	static function getZFversion($versionnumber){
		$zf_version = "1.5.1";
		if($versionnumber >= 6007){
			$zf_version = "1.8.4";
		}
		if($versionnumber >= 6009){
			$zf_version = "1.10.4";
		}
		if($versionnumber >= 6100){
			$zf_version = "1.10.6";
		}
		if($versionnumber >= 6103){
			$zf_version = "1.11.6";
		}
		if($versionnumber >= 6201){
			$zf_version = "1.11.7";
		}
		if($versionnumber >= 6261){
			$zf_version = "1.11.11";
		}
		if($versionnumber >= 6351){
			$zf_version = "1.12.1";
		}
		if($versionnumber >= 6360){
			$zf_version = "1.12.3";
		}
		return $zf_version;
	}

	/**
	 * returns array assigning all languages that exists
	 *
	 * @return array
	 */
	static function getPossibleLanguagesArray(){
		$GLOBALS['DB_WE']->query('SELECT DISTINCT (language) FROM ' . SOFTWARE_LANGUAGE_TABLE . ' WHERE version>=7000 ORDER BY language ASC');

		return $GLOBALS['DB_WE']->getAll(true);
	}

	/**
	 * returns array of all possible versions to update according to the
	 * installed languages
	 *
	 * @return array
	 */
	static function getPossibleVersionsArray(){
		$langVersions = update::getVersionsLanguageArray(true, 0, isset($_SESSION['testUpdate']) ? $_SESSION['testUpdate'] : false);

		$possibleVersions = [];

		foreach($langVersions as $version => $lngArray){
			if($version > $_SESSION['clientVersionNumber'] && count($lngArray) == count($_SESSION['clientInstalledLanguages'])){
				$possibleVersions[$version] = updateUtil::number2version($version);
			} elseif($version > $_SESSION['clientVersionNumber']){
				$possibleVersions[$version] = updateUtil::number2version($version);
			}
		}
		return $possibleVersions;
	}

	static function getAlphaBetaVersions(){
		$AlphaBetaVersions = [];

		$GLOBALS['DB_WE']->query('SELECT v.version,v.svnrevision,v.type,v.typeversion,v.branch,l.language,0 AS isbeta FROM ' . VERSION_TABLE . ' v JOIN ' . SOFTWARE_LANGUAGE_TABLE . ' l ON v.version=l.version ORDER BY v.version DESC,l.language');

		while($GLOBALS['DB_WE']->next_record()){
			$AlphaBetaVersions[$GLOBALS['DB_WE']->f('version')] = $GLOBALS['DB_WE']->getRecord();
		}

		return $AlphaBetaVersions;
	}

	/**
	 * returns associative array with all versions where isLive is not true (betas)
	 * version exists
	 *
	 * @return array
	 */
	static function getSubVersions(){
		$GLOBALS['DB_WE']->query('SELECT version,svnrevision FROM ' . VERSION_TABLE . ' WHERE 1 ORDER BY version DESC');
		return $GLOBALS['DB_WE']->getAllFirst(false);
	}

	/**
	 * returns gets the subversion to a version
	 * version exists
	 *
	 * @return string
	 */
	static function getSubVersion($version){
		$h = $GLOBALS['DB_WE']->getHash('SELECT svnrevision FROM ' . VERSION_TABLE . ' WHERE version=' . intval($version) . ' LIMIT 1');
		return empty($h['svnrevision']) ? 0 : $h['svnrevision'];
	}

	static function getVersionNames(){
		$GLOBALS['DB_WE']->query('SELECT version, versname FROM ' . VERSION_TABLE . ' WHERE 1 ORDER BY version DESC');

		return $GLOBALS['DB_WE']->getAllFirst(false);
	}

	/**
	 * returns the name to a version
	 * version exists
	 *
	 * @return string
	 */
	static function getVersionName($version){
		$h = $GLOBALS['DB_WE']->getHash('SELECT versname FROM ' . VERSION_TABLE . ' WHERE version=' . $version . ' LIMIT 1');
		return $h['versname'];
	}

	/**
	 * returns gets the type to a version
	 * version exists
	 *
	 * @return string
	 */
	static function getVersionType($version){
		$row = $GLOBALS['DB_WE']->getHash('SELECT type,typeversion FROM ' . VERSION_TABLE . ' WHERE version=' . $version);

		return $row['type'] . (($row['typeversion'] == 0) ? '' : $row['typeversion']);
	}

	static function getOnlyVersionType($version){
		$h = $GLOBALS['DB_WE']->getHash('SELECT type FROM ' . VERSION_TABLE . ' WHERE version=' . $version . ' LIMIT 1');
		return $h['type'];
	}

	static function getOnlyVersionTypeVersion($version){
		$h = $GLOBALS['DB_WE']->getHash('SELECT typeversion FROM ' . VERSION_TABLE . ' WHERE version=' . intval($version) . ' LIMIT 1');
		return $h['typeversion'];
	}

	static function getOnlyVersionBranch($version){
		$h = $GLOBALS['DB_WE']->getHash('SELECT branch FROM ' . VERSION_TABLE . ' WHERE version=' . $version . ' LIMIT 1');
		return $h['branch'];
	}

	/**
	 * returns associative array assigning a version to all languages that
	 * version exists
	 *
	 * @return array
	 */
	static function getVersionsLanguageArray($installedLanguagesOnly = true, $vers = 0, $useBeta = false){
		//error_log(print_r(urldecode(base64_decode($_SESSION['clientInstalledLanguages'])),1));
		if(isset($_SESSION['clientInstalledLanguages']) && !is_array($_SESSION['clientInstalledLanguages'])){
			//$_SESSION['clientInstalledLanguages'] = unserialize(urldecode(($_SESSION['clientInstalledLanguages'])));
			if(unserialize(urldecode(($_SESSION['clientInstalledLanguages'])))){
				$_SESSION['clientInstalledLanguages'] = unserialize(urldecode(($_SESSION['clientInstalledLanguages'])));
			} else if(!is_array($_SESSION['clientInstalledLanguages'])){
				//if(!is_array($_SESSION['clientInstalledLanguages'])) {
				$_SESSION['clientInstalledLanguages'] = unserialize(urldecode(base64_decode($_SESSION['clientInstalledLanguages'])));
			}
		}

		$GLOBALS['DB_WE']->query('SELECT l.version,l.language FROM ' . VERSION_TABLE . ' v JOIN ' . SOFTWARE_LANGUAGE_TABLE . ' l ON v.version=l.version WHERE 1 ' . ($installedLanguagesOnly ? ' AND l.language IN ("' . implode('", "', $_SESSION['clientInstalledLanguages']) . '")' : '') . ' ' . ($vers ? ' AND l.version=' . $vers : '') .
				($useBeta ? '' : ' AND v.islive=1') .
				' ORDER BY l.version DESC,l.language');
		//AND v.branch="' . $_SESSION['clientVersionBranch'] . '" is not set in installer
		$langs = [];
		foreach($GLOBALS['DB_WE']->getAll(false) as $rec){
			$langs[$rec['version']] = isset($langs[$rec['version']]) ? $langs[$rec['version']] : [];
			if(!in_array($rec['language'], $langs[$rec['version']])){
				$langs[$rec['version']][] = $rec['language'];
			}
		}

		return $langs;
	}

}
