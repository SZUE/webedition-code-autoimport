<?php

class update extends updateBase{

	static function updateLogStart(){
		$GLOBALS['DB_WE']->query('INSERT INTO ' . UPDATELOG_TABLE . ' (date) VALUES (NOW())');
		$_SESSION['db_log_id'] = $GLOBALS['DB_WE']->getInsertId();
		$setvalues = "";
		$setvalues = "installedVersion = '" . updateUtil::version2number($_SESSION['clientVersion']) . "'";
		if(isset($_SESSION['clientSubVersion'])){
			$setvalues .= ", installedSvnRevision = '" . $_SESSION['clientSubVersion'] . "'";
		}
		if(isset($_SESSION['clientVersionBranch'])){
			$setvalues .= ", installedVersionBranch = '" . $_SESSION['clientVersionBranch'] . "'";
		}
		if(isset($_SESSION['clientPhpVersion'])){
			$setvalues .= ", clientPhpVersion = '" . $_SESSION['clientPhpVersion'] . "'";
		}
		if(isset($_SESSION['clientPhpExtensions'])){
			$setvalues .= ", clientPhpExtensions = '" . $_SESSION['clientPhpExtensions'] . "'";
		}
		if(isset($_SESSION['clientPcreVersion'])){
			$setvalues .= ", clientPcreVersion = '" . $_SESSION['clientPcreVersion'] . "'";
		}
		if(isset($_SESSION['clientMySQLVersion'])){
			$setvalues .= ", clientMySqlVersion = '" . $_SESSION['clientMySQLVersion'] . "'";
		}
		if(isset($_SESSION['clientServerSoftware'])){
			$setvalues .= ", clientServerSoftware = '" . $_SESSION['clientServerSoftware'] . "'";
		}
		if(isset($_SESSION['clientEncoding'])){
			$setvalues .= ", clientEncoding = '" . $_SESSION['clientEncoding'] . "'";
		}
		if(isset($_SESSION['clientSyslng'])){
			$setvalues .= ", clientSysLng = '" . $_SESSION['clientSyslng'] . "'";
		}
		if(isset($_SESSION['clientLng'])){
			$setvalues .= ", clientLng = '" . $_SESSION['clientLng'] . "'";
		}
		if(isset($_SESSION['clientExtension'])){
			$setvalues .= ", clientExtension = '" . $_SESSION['clientExtension'] . "'";
		}
		if(isset($_SESSION['clientDomain'])){
			$setvalues .= ", clientDomain = '" . base64_encode($_SESSION['clientDomain']) . "'";
		}
		if(isset($_SESSION['clientInstalledLanguages'])){
			$setvalues .= ", installedLanguages = '" . implode(',', $_SESSION['clientInstalledLanguages']) . "'";
		}
		if(isset($_SESSION['clientInstalledModules'])){
			$setvalues .= ", installedModules = '" . implode(',', $_SESSION['clientInstalledModules']) . "'";
		}
		if(isset($_SESSION['clientInstalledAppMeta'])){
			$setvalues .= ", installedAppMeta = '" . print_r($_SESSION['clientInstalledAppMeta'], true) . "'";
		}
		if(isset($_SESSION['clientInstalledAppTOC'])){
			$setvalues .= ", installedAppTOC = '" . $_SESSION['clientInstalledAppTOC'] . "'";
		}

		if(isset($_SESSION['clientDBcharset'])){
			$setvalues .= ", installedDbCharset = '" . $_SESSION['clientDBcharset'] . "'";
		}
		if(isset($_SESSION['clientDBcollation'])){
			$setvalues .= ", installedDbCollation = '" . $_SESSION['clientDBcollation'] . "'";
		}
		if(isset($_SESSION['testUpdate'])){
			$setvalues .= ", testUpdate = '" . $_SESSION['testUpdate'] . "'";
		}
		$query = "UPDATE " . UPDATELOG_TABLE . " SET " . $setvalues . " WHERE id = '" . $_SESSION['db_log_id'] . "' ;";

		$GLOBALS['DB_WE']->query($query);
	}

	static function updateLogAvail($verarray){

		$setvalues = "";
		$setvalues = "installedSvnRevisionDB = '" . $verarray['svnrevisionDB'] . "', newestVersion = '" . $verarray['version'] . "', newestVersionStatus = '" . $verarray['type'] . "', newestSvnRevision = '" . $verarray['svnrevision'] . "', newestVersionBranch = '" . $verarray['versionBranch'] . "' ";
		$query = "UPDATE " . UPDATELOG_TABLE . " SET " . $setvalues . " WHERE id = '" . $_SESSION['db_log_id'] . "' ;";
		$GLOBALS['DB_WE']->query($query);
	}

	static function updateLogTarget(){
		$version = $_SESSION['clientTargetVersionNumber'];
		$versionname = update::getVersionName($version);
		$svnrevision = update::getSubVersion($version);
		$versiontype = update::getVersionType($version);
		$versionbranch = update::getOnlyVersionBranch($version);
		$setvalues = "";
		$setvalues = "updatedVersion = '" . $version . "', updatedVersionName = '" . $versionname . "', updatedVersionStatus = '" . $versiontype . "', updatedSvnRevision = '" . $svnrevision . "', updatedVersionBranch = '" . $versionbranch . "', success = '1' ";
		$query = "UPDATE " . UPDATELOG_TABLE . " SET " . $setvalues . " WHERE id = '" . $_SESSION['db_log_id'] . "' ;";
		$GLOBALS['DB_WE']->query($query);
	}

	static function updateLogFinish($success){

		$setvalues = "success = '" . $success . "' ";
		$query = "UPDATE " . UPDATELOG_TABLE . " SET " . $setvalues . " WHERE id = '" . $_SESSION['db_log_id'] . "' ;";
		$GLOBALS['DB_WE']->query($query);
	}

	function checkRequirements(&$output, $pcreV, $phpextensionsstring, $phpV, $mysqlV = ''){
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
		$liveCondition = ' WHERE islive=1';
		if(isset($_SESSION['testUpdate'])){
			$liveCondition = '';
		}

		$row = $GLOBALS['DB_WE']->getHash('SELECT MAX(version) AS maxVersion FROM ' . VERSION_TABLE . $liveCondition);


		if($row['maxVersion'] > $_SESSION['clientVersionNumber']){
			return $row['maxVersion'];
		}
		return false;
	}

	static function getMaxVersionNumber(){
		$liveCondition = ' WHERE islive=1';
		if(isset($_SESSION['testUpdate'])){
			$liveCondition = '';
		}

		$row = $GLOBALS['DB_WE']->getHash('SELECT MAX(version) AS maxVersion FROM ' . VERSION_TABLE . $liveCondition);

		$maxVersion = $row['maxVersion'];
		$liveCondition = ' WHERE islive=1 AND version=' . $maxVersion;
		if(isset($_SESSION['testUpdate'])){
			$liveCondition = ' WHERE version=' . $maxVersion;
		}
		return $GLOBALS['DB_WE']->getHash('SELECT version, svnrevision,type,typeversion,branch,versname FROM ' . VERSION_TABLE . $liveCondition);


		//return $row['maxVersion'];
	}

	static function getMaxVersionNumberForBranch($branch){
		$liveCondition = " WHERE `islive`=1 AND `branch`='" . $branch . "'";
		if(isset($_SESSION['testUpdate'])){
			$liveCondition = " WHERE `branch`='" . $branch . "'";
		}

		$row = $GLOBALS['DB_WE']->getHash('SELECT MAX(version) AS maxVersion FROM `' . VERSION_TABLE . '`' . $liveCondition);

		return intval($row['maxVersion']);
	}

	static function getMaxVersionFieldsForBranch($branch){

		$liveCondition = " WHERE `islive`=1 AND `branch`='" . $branch . "'";
		if(isset($_SESSION['testUpdate'])){
			$liveCondition = " WHERE `branch`='" . $branch . "'";
		}

		$row = $GLOBALS['DB_WE']->getHash('SELECT MAX(version) AS maxVersion FROM `' . VERSION_TABLE . '`' . $liveCondition);

		$maxVersion = $row['maxVersion'];
		$liveCondition = " WHERE `islive`=1 AND `version`='" . $maxVersion . "' AND `branch`='" . $branch . "'";
		if(isset($_SESSION['testUpdate'])){
			$liveCondition = " WHERE `version`='" . $maxVersion . "' AND `branch`='" . $branch . "'";
		}
		return $GLOBALS['DB_WE']->getHash('SELECT version, svnrevision,type,typeversion,branch,versname FROM `' . VERSION_TABLE . '`' . $liveCondition);
	}

	static function getFormattedVersionStringFromWeVersion($showBranch = false, $showBranchIfTrunk = false){
		$versionArray = array(
			'version' => $_SESSION['clientVersionNumber'],
			'versname' => $_SESSION['clientVersionName'],
			'svnrevision' => ($_SESSION['clientSubVersion'] == '0000' || $_SESSION['clientSubVersion'] == '') ? 'n.n.' : $_SESSION['clientSubVersion'],
			'type' => $_SESSION['clientVersionSupp'],
			'typeversion' => $_SESSION['clientVersionSuppVersion'],
			'branch' => $_SESSION['clientVersionBranch'],
		);

		return static::getFormattedVersionString(0, $showBranch, $showBranchIfTrunk, $versionArray);
	}

	static function getFormattedVersionString($versionnumber, $showBranch = false, $showBranchIfTrunk = false, $versionArray = array()){
		if($versionnumber != 0){
			$query = '
				SELECT version, versname, svnrevision, type, typeversion, branch
				FROM ' . VERSION_TABLE . '
				WHERE version = ' . $versionnumber . '
			';
			$versionArray = $GLOBALS['DB_WE']->getHash($query);
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
			$versionQuery = '( version >= ' . $startversion . ' AND version <= ' . $_SESSION['clientTargetVersionNumber'] . ' )';
		} else { // normal update
			$versionQuery = '(version > ' . $_SESSION['clientVersionNumber'] . ' AND version <= ' . $_SESSION['clientTargetVersionNumber'] . ')';
		}

		// query for all selected modules
		$modulesQuery = '';

		// get systemlanguage only
		$clientSyslng = ($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT ?
				str_replace('_UTF-8', '', $_SESSION['clientSyslng']) :
				$_SESSION['clientSyslng']
			);

		$sysLngQuery = ' AND (language="" OR language="' . $clientSyslng . '") ';

		// query for all needed changes - software
		// DON'T use content here.
		$query = '
			SELECT *
			FROM ' . SOFTWARE_TABLE . '
			WHERE
				' . $versionQuery . '
				AND (type="system")
				' . $modulesQuery . '
				' . $sysLngQuery . '
				ORDER BY version DESC
		';

		$languagePart = 'AND ( ';
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

		foreach($theLanguages as $language){
			$languagePart .= 'language="' . $language . '" OR ';
		}
		$languagePart .= ' 0 )';

		// query for needed changes language
		$languageQuery = '
			SELECT *
			FROM ' . SOFTWARE_LANGUAGE_TABLE . '
			WHERE
				' . $versionQuery . '
				AND (type="system")
				' . $modulesQuery . '
				' . $languagePart . '
				ORDER BY version DESC
		';

		return updateUtil::getChangesArrayByQueries(array($query, $languageQuery));
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
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/update/noUpdateForLanguages.inc.php');
		return updateUtil::getResponseString($ret);
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

	function getGetChangesResponse(){
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

		if($branch != 'trunk'){
			$branchText = '|' . $branch;
		} else {
			$branchText = '';
		}
		$AlphaBetaVersions = update::getAlphaBetaVersions();
		$loginfo = ' ' . $_SESSION['clientTargetVersion'] . ' ' . $GLOBALS['lang']['update'][$AlphaBetaVersions[$versionnumber]['type']] . ($AlphaBetaVersions[$versionnumber]['typeversion'] ? $AlphaBetaVersions[$versionnumber]['typeversion'] : '') . ' (SVN-Revision: ' . $SubVersions[$versionnumber] . $branchText . ')';


		$we_version = updateUtil::getReplaceCode('we_version', array($_SESSION['clientTargetVersion'], $version_type, $zf_version, $subversion, $version_type_version, $branch, $version_name));

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

' . updateUtil::getOverwriteClassesCode() . '

$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
$liveUpdateFnc->deleteDir($filesDir);

//FIXME: remove the following create code
if (	$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $we_version['path'] . '", "' . updateUtil::encodeCode($we_version['replace']) . '", "' . updateUtil::encodeCode($we_version['needle']) . '") &&
		$liveUpdateFnc->checkMakeDir( LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we_backup", 0770 ) &&
		$liveUpdateFnc->checkMakeDir( LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we_backup/tmp", 0770 ) &&
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
