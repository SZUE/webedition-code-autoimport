<?php
/**
 * $Id: upgradeUpdate.class.php 13561 2017-03-13 13:40:03Z mokraemer $
 */

class upgradeUpdate{

	/**
	 * gathers all changes needed for an update and returns assoziative array
	 *
	 * @return array
	 */
	static function getChangesForUpdate(){

		return updateUtilUpdate::getChangesArrayByQueries([
				// query for all needed changes - software
				'SELECT changes,version,detail FROM ' . SOFTWARE_TABLE . '  WHERE version<=' . $_SESSION['clientTargetVersionNumber'] . ' ORDER BY version DESC',
				// query for needed changes language
				' SELECT changes,version,detail FROM ' . SOFTWARE_LANGUAGE_TABLE . ' WHERE version <= ' . $_SESSION['clientTargetVersionNumber'] .
				($_SESSION['clientInstalledLanguages'] ? ' AND language IN("' . implode('","', $_SESSION['clientInstalledLanguages']) . '")' : ' AND 0 ') .
				' ORDER BY version DESC'
		]);
	}

	/**
	 * @return string
	 */
	function getNoUpdateForLanguagesResponse(){
		$ret = updateUtilUpdate::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/upgrade/noUpgradeForLanguages.inc.php');
		return updateUtilUpdate::getResponseString($ret);
	}

	/**
	 * upgrade possible response
	 *
	 * @return string
	 */
	function getUpgradePossibleResponse(){
		$ret = updateUtilUpdate::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/upgrade/upgradePossible.inc.php');
		return updateUtilUpdate::getResponseString($ret);
	}

	/**
	 * during upgrade copy this folder to /webEdition6
	 * and write configuration files
	 *
	 * @return string
	 */
	static function getCopyFilesResponse(){

		$nextUrl = installerUpdate::getUpdateClientUrl() . '?' . updateUtilUpdate::getCommonHrefParameters(installerUpdate::getCommandNameForDetail(installerUpdate::getNextUpdateDetail()), installerUpdate::getNextUpdateDetail());
		$versionnumber = updateUtilBase::version2number($_SESSION['clientTargetVersion']);
		$zf_version = updateUpdate::getZFversion($versionnumber);
		$SubVersions = $_SESSION['SubVersions'];
		$subversion = $SubVersions[$versionnumber];
		$version_type = updateUpdate::getOnlyVersionType($versionnumber);
		$version_type_version = updateUpdate::getOnlyVersionTypeVersion($versionnumber);
		$version_branch = updateUpdate::getOnlyVersionBranch($versionnumber);
		$we_version = updateUtilUpdate::getReplaceCode('we_version', array($_SESSION['clientTargetVersion'], $version_type, $zf_version, $subversion, $version_type_version, $version_branch));


		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

' . updateUtilUpdate::getOverwriteClassesCode() . '

$success = true;

// we_version
if (!$liveUpdateFnc->filePutContent( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/we_version.php", $liveUpdateFnc->decodeCode("' . updateUtilUpdate::encodeCode($we_version['replace']) . '") )) {
	$success = false;
	' . installerUpdate::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesVersionError']) . '
}

// we_conf.inc
$confContent = $liveUpdateFnc->getFileContent(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/conf/we_conf.inc.php");
if (!$liveUpdateFnc->filePutContent( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/conf/we_conf.inc.php", $confContent)) {
	$success = false;
	' . installerUpdate::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesConfError']) . '
}

// we_conf_global.inc
$confGlobalContent = $liveUpdateFnc->getFileContent(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/conf/we_conf_global.inc.php");
if (!$liveUpdateFnc->filePutContent( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/conf/we_conf_global.inc.php", $confGlobalContent)) {
	$success = false;
	' . installerUpdate::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesConfError']) . '
}

// now make some folders

// fragments
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/fragments")) {
	$success = false;
	' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'fragments')) . '
}
// tmp
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/tmp")) {
	$success = false;
	' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'tmp')) . '
}
// versions
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/versions")) {
	$success = false;
	' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'versions')) . '
}

// custom_tags #1
// moved to getFinishInstallationResponse because rename() will fail on Windows Servers if target already exists
/*
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/weTagWizard/we_tags/custom_tags")) {
	$success = false;
	' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'weTagWizard/custom_tags')) . '
}
*/

// custom_tags #2
// moved to getFinishInstallationResponse because rename() will fail on Windows Servers if target already exists
/*
if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/we/include/we_tags/custom_tags")) {
	$success = false;
	' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'custom_tags')) . '
}
*/

// copy installer - needed for confirm window
$liveUpdaterFiles = array();
$liveUpdateFnc->getFilesOfDir($liveUpdaterFiles, LIVEUPDATE_SOFTWARE_DIR . "/webEdition/liveUpdate/updateClient");

foreach ($liveUpdaterFiles as $liveUpdateFile) {

	$newPath = str_replace(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/liveUpdate/updateClient", LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition/liveUpdate/updateClient", $liveUpdateFile);

	$liveUpdateFnc->checkMakeDir(dirname($newPath));
	if (!copy($liveUpdateFile, $newPath)) {
		' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesFileError'], '$newPath')) . '
	}
}

// move tmpFolder to /webEdition6
if ($success && rename(LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/webEdition", LIVEUPDATE_SOFTWARE_DIR . "/webEdition6")) {
	$message = "<div>' . $GLOBALS['lang']['upgrade']['copyFilesSuccess'] . '</div>";
	?>' . installerUpdate::getProceedNextCommandResponsePart($nextUrl, installerUpdate::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
} else {
	$success = false;
	' . installerUpdate::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['copyFilesError']) . '
}

?>';

		return updateUtilUpdate::getResponseString($retArray);
	}

	/**
	 * @return string
	 */
	static function getExecutePatchesResponse(){

		$nextUrl = installerUpdate::getUpdateClientUrl() . '?' . updateUtilUpdate::getCommonHrefParameters(installerUpdate::getCommandNameForDetail(installerUpdate::getNextUpdateDetail()), installerUpdate::getNextUpdateDetail());

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php
' . updateUtilUpdate::getOverwriteClassesCode() . '

$success = true;

if (!$success) {

	$message = "<div>' . $GLOBALS['lang']['upgrade']['executePatchesDatabase'] . '<br /><ul>$errorDetail</ul></div>";
	' . installerUpdate::getErrorMessageResponsePart('', '$message') . '
} else {
	?>' . installerUpdate::getProceedNextCommandResponsePart($nextUrl, installerUpdate::getInstallerProgressPercent(), '<div>' . sprintf($GLOBALS['lang']['installer']['amountPatchesExecuted'], 3) . '</div>') . '<?php
}

?>';
		return updateUtilUpdate::getResponseString($retArray);
	}

	/**
	 * Response to finish installation, prepares webEdition 5 folder and renames
	 * them both
	 *
	 * @return string
	 */
	static function getFinishInstallationResponse(){
		$versionnumber = updateUtilBase::version2number($_SESSION['clientTargetVersion']);
		$zf_version = updateUpdate::getZFversion($versionnumber);

		$SubVersions = $_SESSION['SubVersions'];
		$subversion = $SubVersions[$versionnumber];
		$version_type = updateUpdate::getOnlyVersionType($versionnumber);
		$version_type_version = updateUpdate::getOnlyVersionTypeVersion($versionnumber);
		$version_branch = updateUpdate::getOnlyVersionBranch($versionnumber);
		$we_version = updateUtilUpdate::getReplaceCode('we_version', array($_SESSION['clientTargetVersion'], $version_type, $zf_version, $subversion, $version_type_version, $version_branch));


		// folder name for old webEdition folder (i.e. "webEdition5" for version 5.x or webEdition5light for light version)
		$we_versionDirName = static::getVersionDirName();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

' . updateUtilUpdate::getOverwriteClassesCode() . '

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
		' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'webEdition')) . '
	}
}

// we6 -> we (move new downloaded webEdition folder to the right place)
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition")) {
	if (!rename(LIVEUPDATE_SOFTWARE_DIR . "/webEdition6", LIVEUPDATE_SOFTWARE_DIR . "/webEdition")) {
		$success = false;
		' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'webEdition6')) . '
	}
}

// now move backupFolder
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we_backup") && file_exists(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we_backup")) {
	if (!rename(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we_backup", LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we_backup")) {
		$success = false;
		' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'we_backup')) . '
	}
}

// move custom_tags #1
if ($success && file_exists(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we/include/we_tags/custom_tags")) {
	if ( !rename(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we/include/we_tags/custom_tags", LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_tags/custom_tags")) {
		$success = false;
		' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'custom_tags')) . '
	}
}

// movie custom_tags #2
if ($success && file_exists(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we/include/weTagWizard/we_tags/custom_tags")) {
	if ( !rename(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/we/include/weTagWizard/we_tags/custom_tags", LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/weTagWizard/we_tags/custom_tags")) {
		$success = false;
		' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'custom_tags')) . '
	}
}

// custom_tags #1
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/weTagWizard/we_tags/custom_tags")) {
	if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/weTagWizard/we_tags/custom_tags")) {
		$success = false;
		' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'weTagWizard/custom_tags')) . '
	}
}

// custom_tags #2
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_tags/custom_tags")) {
	if (!$liveUpdateFnc->checkMakeDir( LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_tags/custom_tags")) {
		$success = false;
		' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesDirectoryError'], 'custom_tags')) . '
	}
}

// last part move site folder
if ($success && !file_exists(LIVEUPDATE_SOFTWARE_DIR . "/webEdition/site") && file_exists(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/site")) {
	if (!rename(LIVEUPDATE_SOFTWARE_DIR . "/' . $we_versionDirName . '/site", LIVEUPDATE_SOFTWARE_DIR . "/webEdition/site")) {
		$success = false;
		' . installerUpdate::getErrorMessageResponsePart('', sprintf($GLOBALS['lang']['upgrade']['copyFilesMoveDirectoryError'], 'site')) . '
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

// insert into log
if ($success) {
	$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage']['upgrade']['finished'] . '", "' . $_SESSION['clientTargetVersion'] . '", 0);
	?>' . upgradeUpdate::getFinishUpgradeResponsePart("<div>" . $GLOBALS['lang']['upgrade']['finished'] . "</div>") . '<?php
} else {
	' . installerUpdate::getErrorMessageResponsePart('', $GLOBALS['lang']['upgrade']['finishInstallationError']) . '
}
?>';
		return updateUtilUpdate::getResponseString($retArray);
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
window.open(\'?' . updateUtilUpdate::getCommonHrefParameters('upgrade', 'finishUpgradePopUp') . '\', \'finishInstallationPopUp' . session_id() . '\', \'dependent=yes,height=250,width=600,menubar=no,location=no,resizable=no,status=no,toolbar=no,scrollbars=no\');
//			alert("' . $jsMessage . '");
		</script>';
	}

	function getFinishUpgradePopUpResponse(){
		$ret = updateUtilUpdate::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/upgrade/finishUpgradePopUp.inc.php');
		//error_log( updateUtil::getResponseString($ret) );
		return updateUtilUpdate::getResponseString($ret);
	}

	// identify upgraded webEdition version for renaming old webEdition/ folder (i.e. to "webEdition4/")
	function getVersionDirName(){
		$_versionDirName = "webEdition5";
		if(substr($_SESSION['clientVersionNumber'], 0, 1) == "5"){
			$_versionDirName = "webEdition5";
		} else {
			// nothing special
		}
		return $_versionDirName;
	}

}
