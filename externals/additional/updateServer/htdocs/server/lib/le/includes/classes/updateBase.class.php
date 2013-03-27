<?php

class updateBase {


	function getZFversion($versionnumber){
		$zf_version= "1.5.1";
		if ($versionnumber >= 6007) $zf_version= "1.8.4";
		if ($versionnumber >= 6009) $zf_version= "1.10.4";
		if ($versionnumber >= 6100) $zf_version= "1.10.6";
		if ($versionnumber >= 6103) $zf_version= "1.11.6";
		if ($versionnumber >= 6201) $zf_version= "1.11.7";
		if ($versionnumber >= 6261) $zf_version= "1.11.11";
		if ($versionnumber >= 6351) $zf_version= "1.12.1";
		if ($versionnumber >= 6360) $zf_version= "1.12.3";
		return $zf_version;
	}

	/**
	 * returns array assigning all languages that exists
	 *
	 * @return array
	 */
	function getPossibleLanguagesArray() {
		global $DB_Versioning;

		$liveCondition = ' AND islive=1';
		if (isset($_SESSION['testUpdate'])) {
			$liveCondition = '';

		}

		//$query = 'SELECT distinct(language), isbeta  f�hrt zu dopplten betasprachen

		$query = '
			SELECT distinct(language)
			FROM ' . VERSION_TABLE . '
			WHERE version >= 1590
				' . $liveCondition . '
			ORDER BY language ASC
		';

		$versionLanguages = array();

		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$versionLanguages[] = $row['language'];

		}
		return $versionLanguages;

	}

	function getPossibleBetaLanguagesArray() {
		global $DB_Versioning;

		$liveCondition = ' AND islive=1';
		if (isset($_SESSION['testUpdate'])) {
			$liveCondition = '';

		}

		$query = '
			SELECT distinct(language), isbeta
			FROM ' . VERSION_TABLE . '
			WHERE version >= 1590
				' . $liveCondition . '
			ORDER BY language ASC
		';

		$versionLanguages = array();

		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			if ($row['isbeta']==1){
				$versionLanguages[] = $row['language'];
			}
			

		}
		return $versionLanguages;
	}

	/**
	 * returns array of all possible versions to update according to the
	 * installed languages
	 *
	 * @return array
	 */
	function getPossibleVersionsArray() {

		$langVersions = update::getVersionsLanguageArray(1,0);

		$possibleVersions = array();

		foreach ($langVersions as $version => $lngArray) {
			if(isset($_SESSION["clientWE_LIGHT"]) && $_SESSION["clientWE_LIGHT"]) {
				if ($version >= $_SESSION['clientVersionNumber'] && sizeof($lngArray) == sizeof($_SESSION['clientInstalledLanguages'])) {
					$possibleVersions[$version] = updateUtil::number2version($version);

				}
			} else {
			
				if ($version > $_SESSION['clientVersionNumber'] && sizeof($lngArray) == sizeof($_SESSION['clientInstalledLanguages'])) {
					$possibleVersions[$version] = updateUtil::number2version($version);

				} else {
					if($version > $_SESSION['clientVersionNumber']){
						$possibleVersions[$version] = updateUtil::number2version($version);
					}
					
				}
			}

		}
		return $possibleVersions;

	}


	/**
	 * returns associative array with all versions where isLive is not true (betas)
	 * version exists
	 *
	 * @return array
	 */
	function getNotLiveVersions() {
		global $DB_Versioning;
		$query = '
			SELECT version, svnrevision, language, isbeta
			FROM ' . VERSION_TABLE . '
			WHERE version >= 6000 AND islive!=1 ORDER BY version DESC, language
		';
		$NotLiveVersions = array();
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$NotLiveVersions[$row['version']][] = $row['language'];
		}
		
		return $NotLiveVersions;
		
	}

	function getAlphaBetaVersions() {
		global $DB_Versioning;
		$query = '
			SELECT version, svnrevision,type,typeversion, branch,language, isbeta
			FROM ' . VERSION_TABLE . '
			WHERE version >= 6000 ORDER BY version DESC, language
		';
		$AlphaBetaVersions = array();
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$AlphaBetaVersions[$row['version']] = $row;
		}
		
		return $AlphaBetaVersions;
		
	}

	/**
	 * returns associative array with all versions where isLive is not true (betas)
	 * version exists
	 *
	 * @return array
	 */
	function getSubVersions() {
		global $DB_Versioning;
		$query = '
			SELECT version, svnrevision
			FROM ' . VERSION_TABLE . '
			WHERE version >= 6000 ORDER BY version DESC, language
		';
		$SubVersions = array();
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$SubVersions[$row['version']] = $row['svnrevision'];
		}
		
		return $SubVersions;
		
	}

	/**
	 * returns gets the subversion to a version
	 * version exists
	 *
	 * @return string
	 */
	function getSubVersion($version) {
		global $DB_Versioning;
		$query = '
			SELECT  svnrevision
			FROM ' . VERSION_TABLE . '
			WHERE version = '.$version.'  
		';
		$SubVersion='';
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$SubVersion = $row['svnrevision'];
		}
		
		return $SubVersion;
		
	}

	function getVersionNames() {
		global $DB_Versioning;
		$query = '
			SELECT version, versname
			FROM ' . VERSION_TABLE . '
			WHERE version >= 6000 ORDER BY version DESC, language
		';
		$VersionNames = array();
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$VersionNames[$row['version']] = $row['versname'];
		}

		return $VersionNames;
	}

	/**
	 * returns the name to a version
	 * version exists
	 *
	 * @return string
	 */
	function getVersionName($version) {
		global $DB_Versioning;
		$query = '
			SELECT  versname
			FROM ' . VERSION_TABLE . '
			WHERE version = '.$version.'  
		';
		$VersionName='';
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$VersionName = $row['versname'] ? $row['versname'] : '';
		}
		
		return $VersionName;
	}

	/**
	 * returns gets the type to a version
	 * version exists
	 *
	 * @return string
	 */
	function getVersionType($version) {
		global $DB_Versioning;
		$query = '
			SELECT  type, typeversion
			FROM ' . VERSION_TABLE . '
			WHERE version = '.$version.'  
		';
		$VersionType='';
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$VersionType = $row['type']. (($row['typeversion']==0)? '': $row['typeversion']);
		}
		
		return $VersionType;
		
	}

	function getOnlyVersionType($version) {
		global $DB_Versioning;
		$query = '
			SELECT  type, typeversion
			FROM ' . VERSION_TABLE . '
			WHERE version = '.$version.'  
		';
		$VersionType='';
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$VersionType = $row['type'];
		}
		
		return $VersionType;
		
	}

	function getOnlyVersionTypeVersion($version) {
		global $DB_Versioning;
		$query = '
			SELECT  type, typeversion
			FROM ' . VERSION_TABLE . '
			WHERE version = '.$version.'  
		';
		$VersionType='';
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$VersionType = $row['typeversion'];
		}
		
		return $VersionType;
		
	}

	function getOnlyVersionBranch($version) {
		global $DB_Versioning;
		$query = '
			SELECT  branch, typeversion
			FROM ' . VERSION_TABLE . '
			WHERE version = '.$version.'  
		';
		$VersionType='';
		
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			$VersionType = $row['branch'];
		}
		
		return $VersionType;
		
	}


	/**
	 * returns associative array assigning a version to all languages that
	 * version exists
	 *
	 * @return array
	 */
	function getVersionsLanguageArray($installedLanguagesOnly = true, $showBeta=true) {
		global $DB_Versioning;

		//error_log(print_r(urldecode(base64_decode($_SESSION['clientInstalledLanguages'])),1));
		if(isset($_SESSION['clientInstalledLanguages']) && !is_array($_SESSION['clientInstalledLanguages'])) {
			//$_SESSION['clientInstalledLanguages'] = @unserialize(urldecode(($_SESSION['clientInstalledLanguages'])));
			if(@unserialize(urldecode(($_SESSION['clientInstalledLanguages'])))) {
				$_SESSION['clientInstalledLanguages'] = @unserialize(urldecode(($_SESSION['clientInstalledLanguages'])));
			} else if(!is_array($_SESSION['clientInstalledLanguages'])){
				//if(!is_array($_SESSION['clientInstalledLanguages'])) {
				$_SESSION['clientInstalledLanguages'] = @unserialize(urldecode(base64_decode($_SESSION['clientInstalledLanguages'])));
			}
		}
		
		$liveCondition = ' AND islive=1';
		if (isset($_SESSION['testUpdate'])) {
			$liveCondition = '';

		}
		
		$languageQuery = '';
		if ($installedLanguagesOnly) {
			$languageQuery = ' AND language IN ("' . implode('", "', $_SESSION['clientInstalledLanguages']) . '")';

		}

		$query = '
			SELECT version, language, isbeta
			FROM ' . VERSION_TABLE . '
			WHERE version >= 6000
					' . $languageQuery . '
					' . $liveCondition . '
			ORDER BY version DESC, language
		';

		$versionLanguages = array();
		$versionLanguages["betaLanguages"] = array();
		$res =& $DB_Versioning->query($query);

		while ($res->fetchInto($row)) {
			
			if($row["isbeta"] == "1") {
				if($showBeta){
					$versionLanguages[$row['version']]["betaLanguages"][] = $row['language'];
				} else {$versionLanguages[$row['version']][] = $row['language'];}
				
			} else {
				$versionLanguages[$row['version']][] = $row['language'];
			}
		}
		
		return $versionLanguages;

	}

}

?>