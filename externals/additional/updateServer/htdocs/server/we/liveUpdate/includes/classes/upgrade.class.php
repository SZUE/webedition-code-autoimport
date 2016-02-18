<?php

class upgrade{

	/**
	 * gathers all changes needed for an update and returns assoziative array
	 *
	 * @return array
	 */
	static function getChangesForUpdate(){

		// which modules are installed/licensed
		//$installedModules = $domainInformation['registeredModules'];
		// query for all selected modules
		$modulesQuery = '';
		/*
		  $modulesQuery = ' AND ( module = "" OR ';
		  foreach ($GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'] as $module) {
		  $modulesQuery .= 'module="' . $module . '" OR ';

		  }
		  foreach ($installedModules as $module) {
		  $modulesQuery .= ' module = "' . $module . '" OR ';

		  }
		  $modulesQuery .= '0 )';
		 */
		$sysLngQuery = ' AND (language="" OR language="' . $_SESSION['clientSyslng'] . '") ';

		// query for all needed changes - software
		// DON'T use content here.
		$query = 'SELECT * FROM ' . SOFTWARE_TABLE . '  WHERE
				version <= ' . $_SESSION['clientTargetVersionNumber'] . '
				AND (type="system")
				' . $modulesQuery . '
				' . $sysLngQuery . '
				ORDER BY version DESC
		';

		$languagePart = 'AND ( ';
		foreach($_SESSION['clientInstalledLanguages'] as $language){
			$languagePart .= 'language="' . $language . '" OR ';
		}
		$languagePart .= ' 0 )';

		// query for needed changes language
		$languageQuery = '
			SELECT *
			FROM ' . SOFTWARE_LANGUAGE_TABLE . '
			WHERE
				version <= ' . $_SESSION['clientTargetVersionNumber'] . '
				AND (type="system")
				' . $modulesQuery . '
				' . $languagePart . '
				ORDER BY version DESC
		';

		return updateUtil::getChangesArrayByQueries(array($query, $languageQuery));
	}

	/**
	 * @return string
	 */
	function getNoUpdateForLanguagesResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/upgrade/noUpgradeForLanguages.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * upgrade possible response
	 *
	 * @return string
	 */
	function getUpgradePossibleResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/upgrade/upgradePossible.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * during upgrade copy this folder to /webEdition6
	 * and write configuration files
	 *
	 * @return string
	 */
	static function getCopyFilesResponse(){

		$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail(installer::getNextUpdateDetail()), installer::getNextUpdateDetail());
		$versionnumber = updateUtilBase::version2number($_SESSION['clientTargetVersion']);
		$zf_version = update::getZFversion($versionnumber);
		$SubVersions = $_SESSION['SubVersions'];
		$subversion = $SubVersions[$versionnumber];
		$version_type = update::getOnlyVersionType($versionnumber);
		$version_type_version = update::getOnlyVersionTypeVersion($versionnumber);
		$version_branch = update::getOnlyVersionBranch($versionnumber);
		$we_version = updateUtil::getReplaceCode('we_version', array($_SESSION['clientTargetVersion'], $version_type, $zf_version, $subversion, $version_type_version, $version_branch));


		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

' . updateUtil::getOverwriteClassesCode() . '

$success = true;

// prepare files

// we_installed_modules
/*
$we_installed_modules = \'' . updateUtil::encodeCode(modules::getCodeForInstalledModules()) . '\';
if (!$liveUpdateFnc->filePutContent(LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/we_installed_modules.inc' . $_SESSION['clientExtension'] . '", $liveUpdateFnc->decodeCode($we_installed_modules))) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesInstalledModulesError']) . '
}
*/
// we_active_integrated_modules
/*
$we_active_integrated_modules = \'' . updateUtil::encodeCode(modules::getCodeForActiveIntegratedModules()) . '\';
if (!$liveUpdateFnc->filePutContent(LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/we_active_integrated_modules.inc' . $_SESSION['clientExtension'] . '", $liveUpdateFnc->decodeCode($we_active_integrated_modules))) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesInstalledModulesError']) . '
}
*/
// we_version
if (!$liveUpdateFnc->filePutContent( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/we_version' . $_SESSION['clientExtension'] . '", $liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($we_version['replace']) . '") )) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesVersionError']) . '
}

// we_conf.inc
$confContent = $liveUpdateFnc->getFileContent(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/conf/we_conf.inc' . $_SESSION['clientExtension'] . '");
if (!$liveUpdateFnc->filePutContent( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/conf/we_conf.inc' . $_SESSION['clientExtension'] . '", $confContent)) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesConfError']) . '
}

// we_conf_global.inc
$confGlobalContent = $liveUpdateFnc->getFileContent(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/conf/we_conf_global.inc' . $_SESSION['clientExtension'] . '");
if (!$liveUpdateFnc->filePutContent( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/conf/we_conf_global.inc' . $_SESSION['clientExtension'] . '", $confGlobalContent)) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesConfError']) . '
}

// now make some folders

// fragments
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/fragments")) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'fragments')) . '
}
// tmp
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/tmp")) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'tmp')) . '
}
// versions
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/versions")) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'versions')) . '
}

// custom_tags #1
// moved to getFinishInstallationResponse because rename() will fail on Windows Servers if target already exists
/*
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/weTagWizard/we_tags/custom_tags")) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'weTagWizard/custom_tags')) . '
}
*/

// custom_tags #2
// moved to getFinishInstallationResponse because rename() will fail on Windows Servers if target already exists
/*
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/we_tags/custom_tags")) {
	$success = false;
	' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'custom_tags')) . '
}
*/

// copy installer - needed for confirm window
$liveUpdaterFiles = array();
$liveUpdateFnc->getFilesOfDir($liveUpdaterFiles, LIVEUPDATE_SOFTWARE_DIR . "/webEdition/liveUpdate/updateClient");

foreach ($liveUpdaterFiles as $liveUpdateFile) {

	$newPath = str_replace(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/liveUpdate/updateClient", LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/liveUpdate/updateClient", $liveUpdateFile);

	$liveUpdateFnc->checkMakeDir(dirname($newPath));
	if (!copy($liveUpdateFile, $newPath)) {
		' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesFileError'], '$newPath')) . '
	}
}

// move tmpFolder to /webEdition6
if ($success && rename(LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition", LIVEUPDATE_SOFTWARE_DIR . "/webEdition6")) {
	$message = "<div>' . $GLOBALS['lang']['upgrade']['copyFilesSuccess'] . '</div>";
	?>' . installer::getProceedNextCommandResponsePart($nextUrl, installer::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
} else {
	$success = false;
	' . installer::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesError']) . '
}

?>';

		return updateUtil::getResponseString($retArray);
	}

	/**
	 * @return string
	 */
	static function getExecutePatchesResponse(){

		$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail(installer::getNextUpdateDetail()), installer::getNextUpdateDetail());

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php
' . updateUtil::getOverwriteClassesCode() . '

$success = true;

if (!$success) {

	$message = "<div>' . $GLOBALS['lang']['upgrade']['executePatchesDatabase'] . '<br /><ul>$errorDetail</ul></div>";
	' . installer::getErrorMessageResponsePart('', '$message') . '
} else {
	?>' . installer::getProceedNextCommandResponsePart($nextUrl, installer::getInstallerProgressPercent(), '<div>' . sprintf($GLOBALS['lang']['installer']['amountPatchesExecuted'], 3) . '</div>') . '<?php
}

?>';
		return updateUtil::getResponseString($retArray);
	}

	/**
	 * Response to finish installation, prepares webEdition 5 folder and renames
	 * them both
	 *
	 * @return string
	 */
	function getFinishInstallationResponse(){
		$versionnumber = updateUtilBase::version2number($_SESSION['clientTargetVersion']);
		$zf_version = update::getZFversion($versionnumber);

		$SubVersions = $_SESSION['SubVersions'];
		$subversion = $SubVersions[$versionnumber];
		$version_type = update::getOnlyVersionType($versionnumber);
		$version_type_version = update::getOnlyVersionTypeVersion($versionnumber);
		$version_branch = update::getOnlyVersionBranch($versionnumber);
		$we_version = updateUtil::getReplaceCode('we_version', array($_SESSION['clientTargetVersion'], $version_type, $zf_version, $subversion, $version_type_version, $version_branch));


		// folder name for old webEdition folder (i.e. "webEdition5" for version 5.x or webEdition5light for light version)
		$we_versionDirName = static::getVersionDirName();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

' . updateUtil::getOverwriteClassesCode() . '

$success = true;

// rename finish installation
if($success) {
	$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
	$liveUpdateFnc->deleteDir($filesDir);

}

// we -> we5 (move old (ugpraded) webEdition folder to $we_versionDirName)
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '")) {
	if ( !rename(LIVEUPDATE_SOFTWARE_DIR . "/webEdition", LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '")) {
		$success = false;
		' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'webEdition')) . '
	}
}

// we6 -> we (move new downloaded webEdition folder to the right place)
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition")) {
	if (!rename(LIVEUPDATE_SOFTWARE_DIR . "/webEdition6", LIVEUPDATE_SOFTWARE_DIR . "/webEdition")) {
		$success = false;
		' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'webEdition6')) . '
	}
}

// now move backupFolder
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we_backup") && file_exists(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we_backup")) {
	if (!rename(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we_backup", LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we_backup")) {
		$success = false;
		' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'we_backup')) . '
	}
}

// move custom_tags #1
if ($success && file_exists(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we/include/we_tags/custom_tags")) {
	if ( !rename(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we/include/we_tags/custom_tags", LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_tags/custom_tags")) {
		$success = false;
		' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'custom_tags')) . '
	}
}

// movie custom_tags #2
if ($success && file_exists(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we/include/weTagWizard/we_tags/custom_tags")) {
	if ( !rename(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we/include/weTagWizard/we_tags/custom_tags", LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/weTagWizard/we_tags/custom_tags")) {
		$success = false;
		' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'custom_tags')) . '
	}
}

// custom_tags #1
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/weTagWizard/we_tags/custom_tags")) {
	if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/weTagWizard/we_tags/custom_tags")) {
		$success = false;
		' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'weTagWizard/custom_tags')) . '
	}
}

// custom_tags #2
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_tags/custom_tags")) {
	if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_tags/custom_tags")) {
		$success = false;
		' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'custom_tags')) . '
	}
}

// last part move site folder
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/site") && file_exists(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/site")) {
	if (!rename(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/site", LIVEUPDATE_SOFTWARE_DIR . "/webEdition/site")) {
		$success = false;
		' . installer::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'site')) . '
	}
}

// last add welcome message to tblnotepad -> this table should be empty.
if ($success) {
	$tmpDB = new DB_WE();
	$tmpDB->Halt_On_Error = "no";
	$_query = "
		INSERT INTO " . TBL_PREFIX . "tblwidgetnotepad
				(WidgetName, UserID, CreationDate, Title, Text, Priority, Valid, ValidFrom, ValidUntil)
		VALUES	(
				\"' . $GLOBALS['lang']['upgrade']['notepad_category'] . '\", 1, NOW(), \"' . $GLOBALS['lang']['upgrade']['notepad_headline'] . '\", \"' . $GLOBALS['lang']['upgrade']['notepad_text'] . '\", \"low\", \"always\", NOW(), NOW()
				)
	";
	$tmpDB->query($_query);
}
>>>>>>> .r11302

<<<<<<< .mine
// last add welcome message to tblnotepad -> this table should be empty.
if ($success) {
	$tmpDB = new DB_WE();
	$tmpDB->Halt_On_Error = "no";
	$_query = "INSERT INTO " . TBL_PREFIX . "tblwidgetnotepad
				(WidgetName, UserID, CreationDate, Title, Text, Priority, Valid, ValidFrom, ValidUntil)
		VALUES	(
				\"' . $GLOBALS['lang']['upgrade']['notepad_category'] . '\", 1, NOW(), \"' . $GLOBALS['lang']['upgrade']['notepad_headline'] . '\", \"' . $GLOBALS['lang']['upgrade']['notepad_text'] . '\", \"low\", \"always\", NOW(), NOW()
				)
	";
	$tmpDB->query($_query);
}

// insert into log
if ($success) {
	$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage']['upgrade']['finished'] . '", "' . $_SESSION['clientTargetVersion'] . '", 0);
	?>' . upgrade::getFinishUpgradeResponsePart("<div>" . $GLOBALS['lang']['upgrade']['finished'] . "</div>") . '<?php
} else {
	' . installer::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['finishInstallationError']) . '
}
?>';
		return updateUtil::getResponseString($retArray);
	}

	/**
	 * returns response to finish the installation
	 *
	 * @param string $message
	 * @param string $progress
	 * @return string
	 */
	function getFinishUpgradeResponsePart($message, $jsMessage = '', $progress = 100){

		if(!$jsMessage){
			$jsMessage = strip_tags($message);
		}

		return '<script>
top.frames["updatecontent"].setProgressBar("' . $progress . '");
top.frames["updatecontent"].appendMessageLog("' . $message . '\n");
window.open(\'?' . updateUtil::getCommonHrefParameters('upgrade', 'finishUpgradePopUp') . '\', \'finishInstallationPopUp' . session_id() . '\', \'dependent=yes,height=250,width=600,menubar=no,location=no,resizable=no,status=no,toolbar=no,scrollbars=no\');
//			alert("' . $jsMessage . '");
		</script>';
	}

	function getFinishUpgradePopUpResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/upgrade/finishUpgradePopUp.inc.php');
		//error_log( updateUtil::getResponseString($ret) );
		return updateUtil::getResponseString($ret);
	}

	// identify upgraded webEdition version for renaming old webEdition/ folder (i.e. to "webEdition4/")
	function getVersionDirName(){
		$_versionDirName = "webEdition5";
		if(isset($_SESSION["clientWE_LIGHT"]) && $_SESSION["clientWE_LIGHT"]){
			$_versionDirName = "webEdition5light";
		} else if(substr($_SESSION['clientVersionNumber'], 0, 1) == "5"){
			$_versionDirName = "webEdition5";
		} else {
			// nothing special
		}
		return $_versionDirName;
	}

}
