<?php

class update extends updateBase{

	static function updateLogStart(){
		$GLOBALS['DB_WE']->query('INSERT INTO ' . UPDATELOG_TABLE . ' SET date=NOW(),' .
			we_database_base::arraySetter([
				"installedVersion" => updateUtil::version2number($_SESSION['clientVersion']),
				'installedSvnRevision' => (isset($_SESSION['clientSubVersion']) ? $_SESSION['clientSubVersion'] : ''),
				'installedVersionBranch' => (isset($_SESSION['clientVersionBranch']) ? $_SESSION['clientVersionBranch'] : ''),
				'clientPhpVersion' => (isset($_SESSION['clientPhpVersion']) ? $_SESSION['clientPhpVersion'] : ''),
				'clientPhpExtensions' => (isset($_SESSION['clientPhpExtensions']) ? $_SESSION['clientPhpExtensions'] : ''),
				'clientPcreVersion' => (isset($_SESSION['clientPcreVersion']) ? $_SESSION['clientPcreVersion'] : ''),
				'clientMySqlVersion' => (isset($_SESSION['clientMySQLVersion']) ? $_SESSION['clientMySQLVersion'] : ''),
				'clientServerSoftware' => (isset($_SESSION['clientServerSoftware']) ? $_SESSION['clientServerSoftware'] : ''),
				'clientEncoding' => (isset($_SESSION['clientEncoding']) ? $_SESSION['clientEncoding'] : ''),
				'clientSysLng' => (isset($_SESSION['clientSyslng']) ? $_SESSION['clientSyslng'] : ''),
				'clientLng' => (isset($_SESSION['clientLng']) ? $_SESSION['clientLng'] : ''),
				'clientExtension' => (isset($_SESSION['clientExtension']) ? $_SESSION['clientExtension'] : ''),
				'clientDomain' => (isset($_SESSION['clientDomain']) ? base64_encode($_SESSION['clientDomain']) : ''),
				'installedLanguages' => (isset($_SESSION['clientInstalledLanguages']) ? implode(',', $_SESSION['clientInstalledLanguages']) : ''),
				'installedModules' => (isset($_SESSION['clientInstalledModules']) ? implode(',', $_SESSION['clientInstalledModules']) : ''),
				'installedAppMeta' => (isset($_SESSION['clientInstalledAppMeta']) ? print_r($_SESSION['clientInstalledAppMeta'], true) : ''),
				'installedDbCharset' => (isset($_SESSION['clientDBcharset']) ? $_SESSION['clientDBcharset'] : ''),
				'installedDbCollation' => (isset($_SESSION['clientDBcollation']) ? $_SESSION['clientDBcollation'] : ''),
				'testUpdate' => (isset($_SESSION['testUpdate']) ? $_SESSION['testUpdate'] : '')
			])
		);
		$_SESSION['db_log_id'] = $GLOBALS['DB_WE']->getInsertId();
	}

	static function updateLogAvail($verarray){
		$GLOBALS['DB_WE']->query('UPDATE ' . UPDATELOG_TABLE . ' SET ' .
			we_database_base::arraySetter([
				'installedSvnRevisionDB' => (empty($verarray['svnrevisionDB']) ? '' : $verarray['svnrevisionDB']),
				'newestVersion' => $verarray['version'],
				'newestVersionStatus' => $verarray['type'],
				'newestSvnRevision' => $verarray['svnrevision'],
				'newestVersionBranch' => (empty($verarray['versionBranch']) ? '' : $verarray['versionBranch'])
			]) .
			' WHERE id=' . intval($_SESSION['db_log_id']));
	}

	static function updateLogTarget(){
		$version = $_SESSION['clientTargetVersionNumber'];

		$GLOBALS['DB_WE']->query('UPDATE ' . UPDATELOG_TABLE . ' SET ' .
			we_database_base::arraySetter([
				'updatedVersion' => $version,
				'updatedVersionName' => update::getVersionName($version),
				'updatedVersionStatus' => update::getVersionType($version),
				'updatedSvnRevision' => update::getSubVersion($version),
				'updatedVersionBranch' => update::getOnlyVersionBranch($version),
				'success' => 1,
			]) .
			'WHERE id=' . intval($_SESSION['db_log_id']));
	}

	static function updateLogFinish($success){
		$GLOBALS['DB_WE']->query('UPDATE ' . UPDATELOG_TABLE . ' SET success=' . intval($success) . ' WHERE id=' . intval($_SESSION['db_log_id']));
	}

	static function checkRequirements(&$output, $pcreV, $phpextensionsstring, $phpV, $mysqlV = ''){
		$phpversionOK = true;
		$phpversionOkForV640 = true;
		$mysqlversionOK = true;
		$pcreversionOK = true;
		$phpExtensionsDetectable = true;
		$phpExtensionsOK = true;
		$sdkDbOK = true;
		$mbstringAvailable = true;
		$gdlibAvailable = true;
		$exifAvailable = true;
		$phpextensions = explode(',', $phpextensionsstring);
		foreach($phpextensions as &$extens){
			$extens = strtolower($extens);
		}
		$phpextensionsMissing = array();
		$phpextensionsMin = array('ctype', 'date', 'dom', 'filter', 'iconv', 'libxml', 'mysqli', 'pcre', 'Reflection', 'session', 'SimpleXML', 'SPL', 'standard', 'tokenizer', 'xml', 'zlib');

		if(count($phpextensions) > 3){
			foreach($phpextensionsMin as $exten){
				if(!in_array(strtolower($exten), $phpextensions, true)){
					$phpextensionsMissing[] = $exten;
				}
			}
			if($_SESSION['clientTargetVersionNumber'] > 6440){
				if(!in_array(strtolower('mysql'), $phpextensions, true) && !in_array('mysqli', $phpextensions, true)){
					$phpextensionsMissing[] = 'mysqli';
				}
			} else {
				if(!in_array('mysqli', $phpextensions, true)){
					$phpextensionsMissing[] = 'mysqli';
				}
			}
			/* if(!(in_array(strtolower('PDO'), $phpextensions) && in_array(strtolower('pdo_mysql'), $phpextensions))){//sp�ter ODER mysqli
			  $sdkDbOK = false;
			  } */
			if(!in_array(strtolower('mbstring'), $phpextensions)){
				$mbstringAvailable = false;
			}
			if(!in_array(strtolower('gd'), $phpextensions)){
				$gdlibAvailable = false;
			}
			if(!in_array(strtolower('exif'), $phpextensions)){
				$exifAvailable = false;
			}
		} else {
			$phpExtensionsDetectable = false;
		}

		if($phpV != '' && version_compare($phpV, '5.2.4', '<')){
			$phpversionOK = false;
		} else if($phpV != '' && version_compare($phpV, '5.3.7', '<') && $_SESSION['clientTargetVersionNumber'] > 6390){
			$phpversionOkForV640 = false;
		}

		if($mysqlV != '' && version_compare($mysqlV, 4, '<') && $_SESSION['clientTargetVersionNumber'] < 6200){
			$mysqlversionOK = false;
			$DBtext = $GLOBALS['lang']['update']['ReqWarnungMySQL4'];
		}
		if($mysqlV != '' && version_compare($mysqlV, 5, '<') && $_SESSION['clientTargetVersionNumber'] >= 6200){
			$mysqlversionOK = false;
			$DBtext = $GLOBALS['lang']['update']['ReqWarnungMySQL5'];
		}

		if(!empty($phpextensionsMissing)){
			$phpExtensionsOK = false;
		}
		if($pcreV != '' && version_compare($pcreV, 7, '<')){
			$pcreversionOK = false;
		}
		if($sdkDbOK && $mysqlversionOK && $phpExtensionsOK && $pcreversionOK && $mbstringAvailable && $gdlibAvailable && $exifAvailable && $phpversionOK && $phpversionOkForV640 && $phpExtensionsDetectable){
			$output = '';
			return 1;
		}
		$output = '<div class="messageDiv">';
		if(!$phpExtensionsOK || !$phpversionOK || $phpversionOkForV640 || !$mysqlversionOK){
			$output .='<p><b>' . $GLOBALS['lang']['update']['ReqWarnung'] . '</b></p><p>' . $GLOBALS['lang']['update']['ReqWarnungText'] . '</p><ul>';
		} else {
			$output .='<ul>';
		}
		if(!$mysqlversionOK){
			$output .= '<li><b>' . $GLOBALS['lang']['update']['ReqWarnungKritisch'] . '</b>' . $DBtext . '</li>';
		}
		if(!$phpversionOK){
			$output .= '<li><b>' . $GLOBALS['lang']['update']['ReqWarnungKritisch'] . '</b>' . $GLOBALS['lang']['update']['ReqWarnungPHPversion'] . '<b>' . $phpV . '</b></li>';
		}

		if(!$phpversionOkForV640){
			$output .= '<li><b>' . $GLOBALS['lang']['update']['ReqWarnungKritisch'] . '</b>' . $GLOBALS['lang']['update']['ReqWarnungPHPversionForV640'] . '<b>' . $phpV . '</b></li>';
		}

		if(!$phpExtensionsOK){
			$output .= '<li><b>' . $GLOBALS['lang']['update']['ReqWarnungKritisch'] . '</b>' . $GLOBALS['lang']['update']['ReqWarnungPHPextension'] . '<b>' . implode(',', $phpextensionsMissing) . '</b></li>';
		}
		if(!$phpExtensionsDetectable){
			$output .= '<li><b>' . $GLOBALS['lang']['update']['ReqWarnungHinweis'] . '</b>' . $GLOBALS['lang']['update']['ReqWarnungPHPextensionND'] . '<b>' . $phpextensionsstring . '</b></li>';
		}
		if(!$pcreversionOK){
			$output .= '<li>' . $GLOBALS['lang']['update']['ReqWarnungHinweis'] . $GLOBALS['lang']['update']['ReqWarnungPCREold1'] . $pcreV . $GLOBALS['lang']['update']['ReqWarnungPCREold2'] . '</li>';
		}
		if(!$sdkDbOK){
			$output .= '<li>' . $GLOBALS['lang']['update']['ReqWarnungHinweis'] . $GLOBALS['lang']['update']['ReqWarnungSDKdb'] . '</li>';
		}
		if(!$mbstringAvailable){
			$output .= '<li>' . $GLOBALS['lang']['update']['ReqWarnungHinweis'] . $GLOBALS['lang']['update']['ReqWarnungMbstring'] . '</li>';
		}
		if(!$gdlibAvailable){
			$output .= '<li>' . $GLOBALS['lang']['update']['ReqWarnungHinweis'] . $GLOBALS['lang']['update']['ReqWarnungGdlib'] . '</li>';
		}
		if(!$exifAvailable){
			$output .= '<li>' . $GLOBALS['lang']['update']['ReqWarnungHinweis'] . $GLOBALS['lang']['update']['ReqWarnungExif'] . '</li>';
		}
		if($_SESSION['clientVersionNumber'] < 6100){
			$output .= '<li>' . $GLOBALS['lang']['update']['ReqWarnungNoCheck'] . '</li>';
		}

		$output .= '</ul></div>';

		return ($phpExtensionsOK && $phpversionOK && $phpversionOkForV640 && $mysqlversionOK ?
				1 : 0);
	}

	/**
	 * returns if there is a new version available
	 *
	 * @return boolean
	 */
	static function checkForUpdate(){
		$row = $GLOBALS['DB_WE']->getHash('SELECT MAX(version) AS maxVersion FROM ' . VERSION_TABLE . (isset($_SESSION['testUpdate']) ? '' : ' WHERE type="release"'));

		return ($row['maxVersion'] > $_SESSION['clientVersionNumber'] ?
				$row['maxVersion'] :
				false);
	}

	static function getMaxVersionNumber(){
		$row = $GLOBALS['DB_WE']->getHash('SELECT MAX(version) AS maxVersion FROM ' . VERSION_TABLE . (isset($_SESSION['testUpdate']) ? '' : ' WHERE type="release"'));

		return $GLOBALS['DB_WE']->getHash('SELECT version, svnrevision,type,typeversion,branch,versname FROM ' . VERSION_TABLE .
				(isset($_SESSION['testUpdate']) ? ' WHERE version=' . $row['maxVersion'] : ' WHERE type="release" AND version=' . $row['maxVersion']));
	}

	static function getMaxVersionNumberForBranch($branch){
		$row = $GLOBALS['DB_WE']->getHash('SELECT MAX(version) AS maxVersion FROM `' . VERSION_TABLE . '` WHERE ' . (isset($_SESSION['testUpdate']) ?
				" `branch`='" . $branch . "'" :
				" type='release' AND `branch`='" . $branch . "'"));

		return intval($row['maxVersion']);
	}

	static function getMaxVersionFieldsForBranch($branch){
		$maxVersion = self::getMaxVersionNumberForBranch($branch);

		return $GLOBALS['DB_WE']->getHash('SELECT version, svnrevision,type,typeversion,branch,versname FROM `' . VERSION_TABLE . '` WHERE ' . (isset($_SESSION['testUpdate']) ?
					" `version`='" . $maxVersion . "' AND `branch`='" . $branch . "'" :
					" type='release' AND `version`='" . $maxVersion . "' AND `branch`='" . $branch . "'"));
	}

	static function getFormattedVersionStringFromWeVersion($showBranch = false, $showBranchIfTrunk = false){
		$versionArray = array(
			'version' => $_SESSION['clientVersionNumber'],
			'versname' => empty($_SESSION['clientVersionName']) ? '' : $_SESSION['clientVersionName'],
			'svnrevision' => (empty($_SESSION['clientSubVersion']) || $_SESSION['clientSubVersion'] == '0000' ) ? 'n.n.' : $_SESSION['clientSubVersion'],
			'type' => $_SESSION['clientVersionSupp'],
			'typeversion' => $_SESSION['clientVersionSuppVersion'],
			'branch' => $_SESSION['clientVersionBranch'],
		);

		return static::getFormattedVersionString(0, $showBranch, $showBranchIfTrunk, $versionArray);
	}

	static function getFormattedVersionString($versionnumber, $showBranch = false, $showBranchIfTrunk = false, $versionArray = array()){
		if($versionnumber != 0){
			$versionArray = $GLOBALS['DB_WE']->getHash('SELECT version,versname,svnrevision,type,typeversion,branch FROM ' . VERSION_TABLE . ' WHERE version = ' . $versionnumber);
		}

		if(count($versionArray) > 0){
			$version = updateUtilBase::number2version($versionArray['version']);
			$versionname = $versionArray['versname'] ? $versionArray['versname'] : $version;
			$svnrevision = $versionArray['svnrevision'];
			$type = $versionArray['type'] ? ' ' . $GLOBALS['lang']['update'][$versionArray['type']] : '';
			$typeversion = $type && $versionArray['typeversion'] != 0 ? ' ' . $versionArray['typeversion'] : '';
			$branch = !$showBranch ? '' : ((!$showBranchIfTrunk && $versionArray['branch'] == 'trunk') ? '' : '|' . $versionArray['branch']);

			return $versionname . ' (' . $version . $type . $typeversion . ', SVN-Revision: ' . $svnrevision . $branch . ')';
		}

		return '';
	}

	/**
	 * gathers all changes needed for an update and returns assoziative array
	 *
	 * @return array
	 */
	static function getChangesForUpdate(){

		// which modules are installed/licensed
		//$installedModules = $domainInformation['registeredModules'];
		// query for versions
		$versionQuery = '';
		if($_SESSION['clientVersionNumber'] == $_SESSION['clientTargetVersionNumber']){ // repeat Update
			$startversion = updateUtil::getLastSnapShot($_SESSION['clientTargetVersionNumber']);
			$versionQuery = '(version >= ' . $startversion . ' AND version <= ' . $_SESSION['clientTargetVersionNumber'] . ' )';
		} else { // normal update
			$versionQuery = '(version > ' . $_SESSION['clientVersionNumber'] . ' AND version <= ' . $_SESSION['clientTargetVersionNumber'] . ')';
		}

		// get systemlanguage only
		$clientSyslng = ($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT ?
				str_replace('_UTF-8', '', $_SESSION['clientSyslng']) :
				$_SESSION['clientSyslng']
			);

		if(!is_array($_SESSION["clientInstalledLanguages"])){
			$_SESSION["clientInstalledLanguages"] = unserialize(urldecode(base64_decode(print_r($_SESSION["clientInstalledLanguages"], true))));
		}
		//error_log(print_r($_SESSION["clientInstalledLanguages"],true));

		$theLanguages = $_SESSION['clientInstalledLanguages'];
		if($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT && $_SESSION['clientVersionNumber'] < LANGUAGELIMIT){
			foreach($theLanguages as &$lvalue){
				$lvalue = str_replace('_UTF-8', '', $lvalue);
			}
			$theLanguages = array_unique($theLanguages);
		}

		return updateUtil::getChangesArrayByQueries([
				// query for all needed changes - software
				'SELECT changes,version,detail FROM ' . SOFTWARE_TABLE . ' WHERE ' . $versionQuery . ' ORDER BY version DESC',
// query for needed changes language
				'SELECT changes,version,detail FROM ' . SOFTWARE_LANGUAGE_TABLE . ' WHERE ' . $versionQuery .
				($theLanguages ? ' AND language IN("' . implode('","', $theLanguages) . '")' : ' AND 0 ') .
				' ORDER BY version DESC'
		]);
	}

	/**
	 * returns response with data to produce screen when no new version is
	 * available
	 *
	 * @return string
	 */
	static function getNoUpdateAvailableResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/update/noUpdateAvailable.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * returns response with data to produce screen, if there is a new version
	 * available, which cannot be installed due to installed languages
	 *
	 * @return string
	 */
	static function getNoUpdateForLanguagesResponse(){
		return updateUtil::getResponseString(updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/update/noUpdateForLanguages.inc.php'));
	}

	/**
	 * @return string
	 */
	static function getUpdateAvailableResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/update/updateAvailable.inc.php');
		return updateUtil::getResponseString($ret);
	}

	static function getUpdateAvailableAfterRepeatResponse(){//error_log('getUpdateAvailableAfterRepeatResponse');
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/update/updateAvailableAfterRepeat.inc.php');
		return updateUtil::getResponseString($ret);
	}

	static function getConfirmUpdateResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/update/confirmUpdate.inc.php');
		return updateUtil::getResponseString($ret);
	}

	static function getConfirmRepeatUpdateResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/update/confirmRepeatUpdate.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/*
	 * responses during update process
	 */

	static function getGetChangesResponse(){
		return installer::getGetChangesResponse();
	}

	/**
	 * Response to finish installation, deletes not needed files and writes
	 * version number
	 *
	 * @return string
	 */
	static function getFinishInstallationResponse(){
		//error_log('getFinishInstallationResponse'); taucht nicht im Log des Servers auf
		$versionnumber = updateUtilBase::version2number($_SESSION['clientTargetVersion']);
		$zf_version = update::getZFversion($versionnumber);
		$SubVersions = $_SESSION['SubVersions'];
		$subversion = $SubVersions[$versionnumber];
		$version_name = update::getVersionName($versionnumber);
		$version_type = update::getOnlyVersionType($versionnumber);
		$version_type_version = update::getOnlyVersionTypeVersion($versionnumber);

		$branch = update::getOnlyVersionBranch($versionnumber);

		$branchText = ($branch != 'trunk' ? '|' . $branch : '');

		$AlphaBetaVersions = update::getAlphaBetaVersions();
		$loginfo = ' ' . $_SESSION['clientTargetVersion'] . ' ' . $GLOBALS['lang']['update'][$AlphaBetaVersions[$versionnumber]['type']] . ($AlphaBetaVersions[$versionnumber]['typeversion'] ? $AlphaBetaVersions[$versionnumber]['typeversion'] : '') . ' (SVN-Revision: ' . $SubVersions[$versionnumber] . $branchText . ')';


		$we_version = updateUtil::getReplaceCode('we_version', [$_SESSION['clientTargetVersion'], $version_type, $zf_version, $subversion, $version_type_version, $branch, $version_name]);

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

' . updateUtil::getOverwriteClassesCode() . '

$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
$liveUpdateFnc->deleteDir($filesDir);

//FIXME: remove the following create code
if (	$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $we_version['path'] . '", "' . updateUtil::encodeCode($we_version['replace']) . '", "' . updateUtil::encodeCode($we_version['needle']) . '") &&
		$liveUpdateFnc->checkMakeDir( LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we_backup", 0770 ) &&
		$liveUpdateFnc->checkMakeDir( LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we_backup/download", 0770 )
	) {

	$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage']['update']['finished'] . $loginfo . '", "' . $_SESSION['clientTargetVersion'] . '", 0);

	?>' . installer::getFinishInstallationResponsePart("<div>" . $GLOBALS['lang']['update']['finished'] . "</div>") . '<?php

} else {
	' . installer::getErrorMessageResponsePart() . '
}
?>';
		//static::updateLogFinish(1);
		return updateUtil::getResponseString($retArray);
	}

}
