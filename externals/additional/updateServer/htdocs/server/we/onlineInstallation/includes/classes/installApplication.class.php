<?php

class installApplication extends installer{
	static $LanguageIndex = "installApplication";

	/**
	 * @return array
	 */
	static function getInstallationStepNames(){

		return array(
			'prepareApplicationInstallation',
			'determineApplicationFiles',
			'downloadApplicationFiles',
			'updateApplicationDatabase',
			'prepareApplicationFiles',
			'copyApplicationFiles',
			'writeApplicationConfiguration',
		);
	}

	/**
	 * returns progress of current installer
	 *
	 * @return integer
	 */
	static function getInstallerProgressPercent(){

		// all steps are:
		// - installation steps
		// - all downloads/files per step
		// - queryfiles/queries per step
		// - all files to prepare/prepareFiles per step

		if($_REQUEST['detail'] == 'prepareApplicationInstallation'){
			return 1;
		}

		// each step
		$installationSteps = static::getInstallationStepNames();
		$installationStepsTotal = sizeof($installationSteps);

		// downloads
		$dlSteps = floor(sizeof($_SESSION['clientChanges']['allChanges']) / 100);
		$installationStepsTotal += $dlSteps;
		// queries
		$querySteps = sizeof($_SESSION['clientChanges']['queries']) / EXECUTE_QUERIES_PER_STEP;
		$installationStepsTotal += $querySteps;
		// prepare files
		$prepareSteps = sizeof($_SESSION['clientChanges']['allChanges']) / PREPARE_FILES_PER_STEP;
		$installationStepsTotal += $prepareSteps;

		$currentStep = 0;

		switch($_REQUEST['detail']){

			case 'determineApplicationFiles':
				$currentStep = 2;
				break;

			case 'downloadApplicationFiles':
				$currentStep = 3;
				$currentStep += ($_REQUEST['position'] / sizeof($_SESSION['clientChanges']['allChanges'])) * $dlSteps;
				break;

			case 'updateApplicationDatabase':
				$currentStep = 4 + $dlSteps + ($_REQUEST['position'] / EXECUTE_QUERIES_PER_STEP);
				break;

			case 'prepareApplicationFiles':
				$currentStep = 5 + $dlSteps + $querySteps + ($_REQUEST['position'] / PREPARE_FILES_PER_STEP);
				break;

			case 'copyApplicationFiles':
				$currentStep = $installationStepsTotal - 1;
				break;

			case 'writeApplicationConfiguration':
				return 100;
		}

		return number_format(($currentStep / $installationStepsTotal * 100), 0);
	}

	static function getDownloadChangesResponse(){

		// current position
		if(!isset($_REQUEST['position'])){
			$_REQUEST['position'] = 0;
		}

		$Paths = array_keys($_SESSION['clientChanges']['allChanges']);

		$fileArray = array();
		$Position = $_REQUEST['position'];

		$Content = updateUtil::getFileContent($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);
		$FileSize = strlen($Content);

		// If file is too large to transfer in one request, split it!
		// when first part(s) are transfered do the next part until complete
		// file is transfered
		if((isset($_REQUEST['part']) && $_REQUEST['part'] > 0) || $FileSize > $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024){

			// Check which part have to be transfered
			$Part = isset($_REQUEST['part']) ? $_REQUEST['part'] : 0;

			// get offset and length of the substr from the file
			$Start = ($Part * $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024);
			$Length = ($_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024);

			// filename on the client
			$Index = $Paths[$Position] . ".part" . $Part;

			// value of the part -> must be base64_encoded
			$Value = updateUtil::encodeCode(substr($Content, $Start, $Length));

			$fileArray[$Paths[$Position] . ".'part" . $Part . "'"] = $Value;

			if($Start + $Length >= $FileSize){
				if($Position >= sizeof($_SESSION['clientChanges']['allChanges'])){
					$nextUrl = '?' . updateUtil::getCommonHrefParameters(static::getNextUpdateDetail(), true);

					// :IMPORTANT:
					return updateUtil::getResponseString(installApplication::_getDownloadFilesMergeResponse($fileArray, $nextUrl, installApplication::getInstallerProgressPercent(), $Paths[$Position], $Part));
				}
				$Position++;
				$nextUrl = '?' . updateUtil::getCommonHrefParameters($_REQUEST['detail'], false) . "&position=" . $Position;

				// :IMPORTANT:
				return updateUtil::getResponseString(installApplication::_getDownloadFilesMergeResponse($fileArray, $nextUrl, installApplication::getInstallerProgressPercent(), $Paths[$Position - 1], $Part));
			}
			$Part += 1;
			$nextUrl = '?' . updateUtil::getCommonHrefParameters($_REQUEST['detail'], false) . "&part=" . $Part . "&position=" . $Position;

			// :IMPORTANT:
			return updateUtil::getResponseString(installApplication::_getDownloadFilesResponse($fileArray, $nextUrl, installApplication::getInstallerProgressPercent()));


			// Only whole files	with max. $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] kbytes per step
		}

		$ResponseSize = 0;
		do{

			if($Position >= sizeof($Paths)){
				break;
			}
			if(!is_readable($_SESSION['clientChanges']['allChanges'][$Paths[$Position]])){
				//error_log('ERROR: file '.$_SESSION['clientChanges']['allChanges'][$Paths[$Position]].' not readable');
			}
			$FileSize = filesize($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);

			// response + size of next file < max size for response
			if($ResponseSize + $FileSize < $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024){
				$ResponseSize += $FileSize;

				$fileArray[$Paths[$Position]] = updateUtil::getFileContentEncoded($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);
				$Position++;
			} else {
				break;
			}
		} while($ResponseSize < $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024);

		if($Position >= sizeof($_SESSION['clientChanges']['allChanges'])){
			$nextUrl = '?' . updateUtil::getCommonHrefParameters(static::getNextUpdateDetail(), true);
		} else {
			$nextUrl = '?' . updateUtil::getCommonHrefParameters($_REQUEST['detail'], false) . "&position=$Position";
		}

		// :IMPORTANT:
		return updateUtil::getResponseString(installApplication::_getDownloadFilesResponse($fileArray, $nextUrl, installApplication::getInstallerProgressPercent()));
	}

	/**
	 * gathers all changes needed for an update and returns assoziative array
	 *
	 * @return array
	 */
	function getApplicationFiles(){
		// query for versions
		$startversion = updateUtil::getLastSnapShot($_SESSION['clientTargetVersionNumber']);

		// get systemlanguage only
		if($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT){
			$clientSyslng = str_replace('_UTF-8', '', $_SESSION['clientSyslng']);
		} else {
			$clientSyslng = $_SESSION['clientSyslng'];
		}

		if($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT){
			foreach($_SESSION['clientDesiredLanguages'] as &$language){
				$language = str_replace('_UTF-8', '', $language);
			}
		}
		array_unique($_SESSION['clientDesiredLanguages']);

		return updateUtil::getChangesArrayByQueries([
				// query for all needed changes - software
				'SELECT changes,version,detail FROM ' . SOFTWARE_TABLE . ' WHERE (version>=' . $startversion . ' AND version<=' . $_SESSION['clientTargetVersionNumber'] . ' ) AND (detail!="patches") ORDER BY version DESC',
				// query for needed changes language
				'SELECT changes,version,detail FROM ' . SOFTWARE_LANGUAGE_TABLE . ' WHERE (version>=' . $startversion . ' AND version<=' . $_SESSION['clientTargetVersionNumber'] . ' ) ' .
				($_SESSION['clientDesiredLanguages'] ? ' AND language IN("' . implode('","', $_SESSION['clientDesiredLanguages']) . '")' : ' AND 0 ') .
				' AND (detail!="patches") ORDER BY version DESC'
		]);
	}

	function getPrepareApplicationInstallationResponse(){

		$nextUrl = '?' . updateUtil::getCommonHrefParameters(static::getNextUpdateDetail(), true);

		// generate code to drop existing webEdition tables
		require( LIVEUPDATE_SERVER_DIR . "/includes/extras/webEditionTables.inc.php");

		$dropTablesCode = '$dropQueries = array();';
		foreach($GLOBALS["allTables"] as $table){
			$dropTablesCode .= '$dropQueries[] = "DROP TABLE IF EXISTS ' . $table . '";' . "\n";
		}
		$dropTablesCode .= '
			if (!$liveUpdateFnc->executeDropQueries($dropQueries)) {
			' . static::getErrorMessageResponsePart('', '<h1 class=\"error\">' . $GLOBALS['lang']['installer']['tableNotDrop'] . '</h1>') . '
			exit;
		}
		';

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$message = "<h1>' . $GLOBALS['lang'][self::$LanguageIndex][$_REQUEST["detail"]] . '</h1>";

		$success = true;

		// move webEdition if needed:
		if (isset($_SESSION["le_verifyWebEdition"])) { // move webEdition
			$newFolder .= $_SESSION["le_installationDirectory"] . "/webEdition_backup_" . time();
			do {
				$newFolder = $_SESSION["le_installationDirectory"] . "/webEdition_backup_" . time();

			} while (is_dir($newFolder));

			if (file_exists($_SESSION["le_installationDirectory"] . "/webEdition")) {
				if (!rename($_SESSION["le_installationDirectory"] . "/webEdition", $newFolder)) {
					' . static::getErrorMessageResponsePart() . '
					exit;

				}

			}

		}

		if ($_SESSION["le_db_overwrite"]) { // overwrite webEdition tables
			' . $dropTablesCode . '

		}

		?>' . static::getProceedNextCommandResponsePart($nextUrl, self::getInstallerProgressPercent(), '<?php print $message ?>') . '<?php
		';
		return updateUtil::getResponseString($retArray);
	}

	/**
	 * This response updates the installer screen and triggers next step
	 *
	 * @return string
	 */
	function getApplicationFilesResponse($nextUrl = ''){

		$nextUrl = '?' . updateUtil::getCommonHrefParameters(static::getNextUpdateDetail(), true);

		$message = '<p>'
			. sprintf($GLOBALS['lang']['installer']['downloadFilesTotal'], sizeof($_SESSION['clientChanges']['allChanges']))
			. '</p>'
			. '<ul>'
			. '<li>' . sizeof($_SESSION['clientChanges']['files']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesFiles'] . '</li>'
			. '<li>' . sizeof($_SESSION['clientChanges']['queries']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesQueries'] . '</li>'
			. '</ul>';

		$progress = self::getInstallerProgressPercent();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '
		$filesDir = LE_INSTALLER_TEMP_PATH;
		$liveUpdateFnc->deleteDir($filesDir);

		?>' . static::getProceedNextCommandResponsePart($nextUrl, $progress, $message);

		return updateUtil::getResponseString($retArray);
	}

	/**
	 * returns code for response to update database
	 *
	 * @return string
	 */
	function getUpdateApplicationDatabaseResponse(){

		if(!isset($_REQUEST['position'])){
			$_REQUEST['position'] = 0;
		}

		$repeatUrl = '?' . updateUtil::getCommonHrefParameters($_REQUEST['detail']) . '&position=' . ($_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP']);
		$nextUrl = '?' . updateUtil::getCommonHrefParameters(static::getNextUpdateDetail(), true);

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$queryDir = LE_INSTALLER_TEMP_PATH . "/tmp/queries/";

		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $queryDir);
		sort($allFiles);

		$message = "<ul>";

		$liveUpdateFnc->clearQueryLog();
		for ($i=' . $_REQUEST['position'] . '; $i<sizeof($allFiles) && $i<' . ($_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP']) . '; $i++) {

			// execute queries in each file
			if ($liveUpdateFnc->executeQueriesInFiles($allFiles[$i])) {
				$text = basename($allFiles[$i]);
				$text = substr($text, -40);
				$message .= "<li>$text</li>";

			} else {

				$msg = $liveUpdateFnc->getQueryLog();
				$fileName = basename($allFiles[$i]);
				if ($msg["tableExists"]) {
					$message .= "<h1 class=\'notice\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['lang']['installer']['tableExists'] . '</h1>";

				}

				if ($msg["tableReCreated"]) {
					$message .= "<h1 class=\'notice\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['lang']['installer']['tableReCreated'] . '</h1>";

				}

				if ($msg["tableChanged"]) {
					$message .= "<h1 class=\'notice\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['lang']['installer']['tableChanged'] . '</h1>";

				}

				if ($msg["entryExists"]) {
					$message .= "<h1 class=\'notice\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['lang']['installer']['entryAlreadyExists'] . '</h1>";

				}

				if ($msg["error"]) {
					$message .= "<h1 class=\'error\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['lang']['installer']['errorExecutingQuery'] . ' </h1>";
				}
				if(isset($msg["error"])) {
					error_log(print_r($msg["error"],true));
				}
			}

		}

		$endFile = min(sizeof($allFiles), ' . ($_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP']) . ');
		$maxFile = sizeof($allFiles);

		$message	.=	"</ul>"
					.	"<p>" . sprintf("' . $GLOBALS['lang']['installer']['amountDatabaseQueries'] . '", $endFile, $maxFile) . "</p>";

		if ( sizeof($allFiles) > ' . ( $_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP'] ) . ' ) { // continue with DB steps
			?>' . static::getProceedNextCommandResponsePart($repeatUrl, self::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

		} else { // proceed to next step.
			?>' . static::getProceedNextCommandResponsePart($nextUrl, self::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

		}
		';

		return updateUtil::getResponseString($retArray);
	}

	/**
	 * returns response for prepare the downloaded files
	 * - overwrite doc_root, change extension
	 * - rename (extension)
	 *
	 * @return string
	 */
	function prepareApplicationFilesResponse(){

		if(!isset($_REQUEST['position'])){
			$_REQUEST['position'] = 0;
		}

		$repeatUrl = '?' . updateUtil::getCommonHrefParameters($_REQUEST['detail']) . '&position=' . ($_REQUEST['position'] + $_SESSION['PREPARE_FILES_PER_STEP']);
		$nextUrl = '?' . updateUtil::getCommonHrefParameters(static::getNextUpdateDetail(), true);

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$filesDir = LE_INSTALLER_TEMP_PATH . "/tmp/files/";

		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $filesDir);
		sort($allFiles);

		$message = "<ul>";
		$success = true;

		for ( $i=' . $_REQUEST["position"] . ',$j=0; $i<sizeof($allFiles) && $success && $j < ' . $_SESSION['PREPARE_FILES_PER_STEP'] . '; $i++,$j++) {

			$content = $liveUpdateFnc->getFileContent($allFiles[$i]);

			$text = basename($allFiles[$i]);
			$text = substr($text, -40);

			$message .= "<li>$text</li>";

			if ($liveUpdateFnc->isPhpFile($allFiles[$i])) {
				$success = $liveUpdateFnc->filePutContent($allFiles[$i], $liveUpdateFnc->preparePhpCode($content, ".php", "' . $_SESSION['clientExtension'] . '"));
				if ($success) {
					$success = rename($allFiles[$i], $allFiles[$i]);

				}
			}
		}
		$message .= "</ul>";

		if (!$success) {
			' . static::getErrorMessageResponsePart() . '

		} else {
			$endFile = min(sizeof($allFiles), ' . ($_REQUEST["position"] + $_SESSION['PREPARE_FILES_PER_STEP']) . ');
			$maxFile = sizeof($allFiles);

			$message .= "<p>" . sprintf(\'' . $GLOBALS['lang']['installer']['amountFilesPrepared'] . '\', $endFile, $maxFile) . "</p>";

			if ( sizeof($allFiles) >= (' . $_SESSION['PREPARE_FILES_PER_STEP'] . ' + ' . $_REQUEST["position"] . ') ) {

				?>' . static::getProceedNextCommandResponsePart($repeatUrl, self::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
			} else {

				?>' . static::getProceedNextCommandResponsePart($nextUrl, self::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
			}
		}
		?>';

		return updateUtil::getResponseString($retArray);
	}

	/**
	 * returns response to copy new files to correct location
	 *
	 * @return string
	 */
	function getCopyApplicationFilesResponse(){

		$nextUrl = '?' . updateUtil::getCommonHrefParameters(static::getNextUpdateDetail(), true);

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$filesDir = LE_INSTALLER_TEMP_PATH . "/tmp/files/";
		$preLength = strlen($filesDir);

		$success = true;

		$message = "<ul>";
		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $filesDir);

		for ($i=0; $success && $i<sizeof($allFiles); $i++) {
			$text = basename($allFiles[$i]);
			$text = substr($text, -40);
			$message .= "<li>$text</li>";

			//$success = $liveUpdateFnc->moveFile($allFiles[$i], $_SESSION["le_installationDirectory"] . substr($allFiles[$i], $preLength));

		}
		$message .= "</ul>";
		$docRoot = isset($_SESSION["le_documentRoot"]) ? $_SESSION["le_documentRoot"] : $_SERVER["DOCUMENT_ROOT"];
		$success = rename($filesDir."webEdition", $docRoot ."/webEdition");

		if ($success) {
			$endFile = sizeof($allFiles);
			$maxFile = sizeof($allFiles);

			$message .= "<p>" . sprintf(\'' . $GLOBALS['lang']['installer']['amountFilesCopied'] . '\', $endFile, $maxFile) . "</p>";

			?>' . static::getProceedNextCommandResponsePart($nextUrl, self::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

		} else {
			' . static::getErrorMessageResponsePart('', $GLOBALS['lang']['installer']['errorMoveFile']) . '
		}
		?>';

		return updateUtil::getResponseString($retArray);
	}

	/**
	 * Response to finish installation, deletes not needed files and writes
	 * version number
	 * configures the following files:
	 * - we_version.inc.php
	 * - we_conf.inc.php
	 * - we_global_conf.inc.php
	 * - we_installed_modules.inc.php
	 * - webEdition.php
	 * - we_menu.inc.php
	 * - liveUpdate/conf.inc.php
	 * creates some needed folders
	 * - we_backup
	 * - we_backup/tmp
	 * - we_backup/download
	 * - we/templates
	 * - we/tmp
	 * - webEdition/site
	 * inserts user in database
	 *
	 * @return string
	 */
	function getWriteApplicationConfigurationResponse(){

		$nextUrl = '?' . updateUtil::getCommonHrefParameters(static::getNextUpdateDetail(), true);

		// 1st step: missing files
		$replaceVersionDemo = updateUtil::getReplaceCode('we_version_demo');
		// folder we_conf, we_conf.inc.php we_conf_global.inc.php
		$replaceWeConfDemo = updateUtil::getReplaceCode('we_conf_demo');
		$replaceWeConfGlobalDemo = updateUtil::getReplaceCode('we_conf_global_demo', [$_SESSION['we_charset']]);
		$replaceWeActiveModules = updateUtil::getReplaceCode('we_activeModules');

		// proxy settings
		$replaceProxySettings = updateUtil::getReplaceCode('we_proxysettings');

		$_SESSION['clientInstalledModules'] = 'Install';
		$licenceName = "GPL";
		$version = $_SESSION['clientTargetVersion'];

		$versionnumber = updateUtilBase::version2number($version);
		$zf_version = update::getZFversion($versionnumber);
		$SubVersions = $_SESSION['SubVersions'];
		$subversion = $SubVersions[$versionnumber];
		$version_type = update::getOnlyVersionType($versionnumber);
		$version_type_version = update::getOnlyVersionTypeVersion($versionnumber);
		$version_branch = update::getOnlyVersionBranch($versionnumber);
		$version_name = update::getVersionName($versionnumber); //imi

		if(stristr($_SESSION['clientSyslng'], 'UTF-8')){
			$_SESSION['client_default_charset'] = 'UTF-8';
			$_SESSION['client_backend_charset'] = 'UTF-8';
		} else {
			$_SESSION['client_default_charset'] = '';
			$_SESSION['client_backend_charset'] = 'ISO-8859-1';
		}
		$_SESSION['clientSyslngNEW'] = $_SESSION['clientSyslng'];
		if($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT){
			$_SESSION['clientSyslngNEW'] = str_replace('_UTF-8', '', $_SESSION['clientSyslngNEW']);
		}
		$ReplaceCode = '
			// replaceCode and make needed directories
//FIXME: remove the following create code
		if (
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we_backup", 0770 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we_backup/tmp", 0770 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we_backup/download", 0770 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/templates", 0770 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/tmp", 0770 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/versions", 0770 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/include/we_tags/custom_tags", 0770 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/include/weTagWizard/we_tags/custom_tags", 0770 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/site", 0770 ) ||

			!$liveUpdateFnc->filePutContent( $_SESSION["le_installationDirectory"] . "' . $replaceVersionDemo['path'] . '", $liveUpdateFnc->preparePhpCode( sprintf($liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceVersionDemo['replace']) . '"), "' . $version . '", "' . $version_type . '", "' . $zf_version . '", "' . $subversion . '", "' . $version_type_version . '", "' . $version_branch . '", "' . $version_name . '"), ".php","' . $_SESSION['clientExtension'] . '"))  ||
			!$liveUpdateFnc->filePutContent( $_SESSION["le_installationDirectory"] . "' . $replaceWeConfDemo['path'] . '", $liveUpdateFnc->preparePhpCode( sprintf($liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceWeConfDemo['replace']) . '"), $_SESSION["le_db_host"], $_SESSION["le_db_database"], base64_encode($_SESSION["le_db_user"]), base64_encode($_SESSION["le_db_password"]), $_SESSION["le_db_connect"], $_SESSION["le_db_prefix"], $_SESSION["we_db_charset"], $_SESSION["we_db_collation"], "' . $licenceName . '", "' . $_SESSION['clientSyslngNEW'] . '", "' . $_SESSION['client_backend_charset'] . '"), ".php","' . $_SESSION['clientExtension'] . '"))  ||
			!$liveUpdateFnc->filePutContent( $_SESSION["le_installationDirectory"] . "' . $replaceWeConfGlobalDemo['path'] . '", $liveUpdateFnc->preparePhpCode( sprintf($liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceWeConfGlobalDemo['replace']) . '"), "' . $_SESSION['client_default_charset'] . '", $_SESSION["we_db_charset"]), ".php","' . $_SESSION['clientExtension'] . '")) ||

			!$liveUpdateFnc->filePutContent($_SESSION["le_installationDirectory"] . "/webEdition/we/include/conf/we_active_integrated_modules.inc' . $_SESSION['clientExtension'] . '", $liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceWeActiveModules['replace']) . '")) ||

			(isset($_SESSION["le_proxy_use"]) && $_SESSION["le_proxy_use"] ? !$liveUpdateFnc->filePutContent($_SESSION["le_installationDirectory"] . "' . $replaceProxySettings['path'] . '", $liveUpdateFnc->preparePhpCode( sprintf($liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceProxySettings['replace']) . '"), $_SESSION["le_proxy_host"], $_SESSION["le_proxy_port"], $_SESSION["le_proxy_username"], $_SESSION["le_proxy_password"])), ".php","' . $_SESSION['clientExtension'] . '") : false)  ||

			0
			) {
			' . static::getErrorMessageResponsePart('', $GLOBALS['lang']['installer']['errorMoveFile']) . '
			exit;

		}
			';


		$tblPrefsQuery = updateUtil::getReplaceCode('insert_tblPrefs');
		$tblUserQuery = updateUtil::getReplaceCode('insert_tblUser');

		// here we must do somestuff
		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		// delete all downloaded files
		$filesDir = LE_INSTALLER_TEMP_PATH . "/tmp";
		$liveUpdateFnc->deleteDir($filesDir);

		' . $ReplaceCode . '

		// query to insert user in webEdition

		$leDB = new le_MySQL_DB();
		$userText = strip_tags($_SESSION["le_login_user"]);
		$userText = str_replace("\'","",$userText);
		$userText = str_replace(\'"\',"",$userText);
		if("' . $tblUserQuery['path'] . '"){
			$docRoot = isset($_SESSION["le_documentRoot"]) ? $_SESSION["le_documentRoot"] : $_SERVER["DOCUMENT_ROOT"];
			include_once($docRoot."' . $tblUserQuery['path'] . '");
			' . $tblUserQuery['needle'] . ';
		}
		$query = sprintf("' . $tblUserQuery['replace'] . '", $_SESSION[\'le_db_prefix\'], $userText, $_SESSION["le_login_user"], $_SESSION["le_login_pass"]);

		if (!$leDB->query($query)) {
			' . static::getErrorMessageResponsePart('', $GLOBALS['lang'][self::$LanguageIndex]['dbNotInsertUser']) . '
			exit;

		}';

		if($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT){
			$backendCH = 'UTF-8';
			$retArray['Code'] .= '$query = sprintf("' . $tblPrefsQuery['replace'] . '", $_SESSION[\'le_db_prefix\'], "' . $_SESSION['clientSyslngNEW'] . '", "' . $backendCH . '");';
		} else {
			$retArray['Code'] .= '$query = sprintf("' . $tblPrefsQuery['replace'] . '", $_SESSION[\'le_db_prefix\'], "' . $_SESSION['clientSyslngNEW'] . '", "' . $GLOBALS["lang"]["installApplication"]["rss_feed_url"] . '");';
		}
		$retArray['Code'] .= '
		if (!$leDB->query($query)) {
			if (!$leDB->query("INSERT INTO " . $_SESSION[\'le_db_prefix\'] . "tblPrefs (userID,`key`,value) VALUES (\'1\',\'Language\',\'' . $_SESSION['clientSyslngNEW'] . '\');")) {
				' . static::getErrorMessageResponsePart('', $GLOBALS['lang'][self::$LanguageIndex]['dbNotInsertPrefs']) . '
				exit;
			}
			if (!$leDB->query("INSERT INTO " . $_SESSION[\'le_db_prefix\'] . "tblPrefs (userID,`key`,value) VALUES (\'1\',\'BackendCharset\',\'' . $backendCH . '\')")) {
				' . static::getErrorMessageResponsePart('', $GLOBALS['lang'][self::$LanguageIndex]['dbNotInsertPrefs']) . '
				exit;
			}
		}' .
			'?>' . static::getProceedNextCommandResponsePart($nextUrl, self::getInstallerProgressPercent(), "<p>" . $GLOBALS['lang'][self::$LanguageIndex]['finished'] . "</p>") . '<?php

		?>';

		return updateUtil::getResponseString($retArray);
	}

}
