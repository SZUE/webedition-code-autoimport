<?php

class installApplication extends installer{
	var $LanguageIndex = "installApplication";

	/**
	 * @return array
	 */
	function getInstallationStepNames(){

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
	function getInstallerProgressPercent(){

		// all steps are:
		// - installation steps
		// - all downloads/files per step
		// - queryfiles/queries per step
		// - all files to prepare/prepareFiles per step

		if($_REQUEST['detail'] == 'prepareApplicationInstallation'){
			return 1;
		}

		$installationStepsTotal = 0;

		// each step
		$installationSteps = $this->getInstallationStepNames();
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
				$currentStep = 4 + $dlSteps;
				$currentStep += ($_REQUEST['position'] / EXECUTE_QUERIES_PER_STEP);
				break;

			case 'prepareApplicationFiles':
				$currentStep = 5 + $dlSteps + $querySteps;
				$currentStep += ($_REQUEST['position'] / PREPARE_FILES_PER_STEP);
				break;

			case 'copyApplicationFiles':
				$currentStep = $installationStepsTotal - 1;
				break;

			case 'writeApplicationConfiguration':
				return 100;
				break;
		}

		return number_format(($currentStep / $installationStepsTotal * 100), 0);
	}

	function getDownloadChangesResponse(){

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

			$fileArray[$Paths[$Position] . ".part" . $Part] = $Value;

			if($Start + $Length >= $FileSize){
				if($Position >= sizeof($_SESSION['clientChanges']['allChanges'])){
					$nextUrl = '?' . updateUtil::getCommonHrefParameters($this->getNextUpdateDetail(), true);

					// :IMPORTANT:
					return updateUtil::getResponseString(installApplication::_getDownloadFilesMergeResponse($fileArray, $nextUrl, installApplication::getInstallerProgressPercent(), $Paths[$Position], $Part));
				} else {
					$Position++;
					$nextUrl = '?' . updateUtil::getCommonHrefParameters($_REQUEST['detail'], false) . "&position=" . $Position;

					// :IMPORTANT:
					return updateUtil::getResponseString(installApplication::_getDownloadFilesMergeResponse($fileArray, $nextUrl, installApplication::getInstallerProgressPercent(), $Paths[$Position - 1], $Part));
				}
			} else {
				$Part += 1;
				$nextUrl = '?' . updateUtil::getCommonHrefParameters($_REQUEST['detail'], false) . "&part=" . $Part . "&position=" . $Position;

				// :IMPORTANT:
				return updateUtil::getResponseString(installApplication::_getDownloadFilesResponse($fileArray, $nextUrl, installApplication::getInstallerProgressPercent()));
			}

			// Only whole files	with max. $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] kbytes per step
		} else {

			$ResponseSize = 0;
			do{

				if($Position >= sizeof($Paths)){
					break;
				}
				if(!is_readable($_SESSION['clientChanges']['allChanges'][$Paths[$Position]])){
					//error_log('ERROR: file '.$_SESSION['clientChanges']['allChanges'][$Paths[$Position]].' not readable');
				}
				$FileSize = @filesize($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);

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
				$nextUrl = '?' . updateUtil::getCommonHrefParameters($this->getNextUpdateDetail(), true);
			} else {
				$nextUrl = '?' . updateUtil::getCommonHrefParameters($_REQUEST['detail'], false) . "&position=$Position";
			}

			// :IMPORTANT:
			return updateUtil::getResponseString(installApplication::_getDownloadFilesResponse($fileArray, $nextUrl, installApplication::getInstallerProgressPercent()));
		}
	}

	/**
	 * gathers all changes needed for an update and returns assoziative array
	 *
	 * @return array
	 */
	function getApplicationFiles(){

		$contentQuery = '';
		if(!$_SESSION['clientContent']){
			$contentQuery .= ' AND (type="system") ';
		}

		// query for versions
		$startversion = updateUtil::getLastSnapShot($_SESSION['clientTargetVersionNumber']);
		$versionQuery = '( version >= ' . $startversion . ' AND version <= ' . $_SESSION['clientTargetVersionNumber'] . ' )';


		// query for all selected modules
		$modulesQuery = 'AND (module = "" OR ';
		foreach($GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'] as $module){
			$modulesQuery .= 'module="' . $module . '" OR ';
		}
		if(isset($_SESSION['clientDesiredModules']) || empty($_SESSION['clientDesiredModules'])){
			$_SESSION['clientDesiredModules'] = array();
		}
		foreach($_SESSION['clientDesiredModules'] as $module){
			$modulesQuery .= 'module="' . $module . '" OR ';
		}
		$modulesQuery .= ' 0 )';

		// get systemlanguage only
		if($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT){
			$clientSyslng = str_replace('_UTF-8', '', $_SESSION['clientSyslng']);
		} else {
			$clientSyslng = $_SESSION['clientSyslng'];
		}
		$sysLngQuery = ' AND (language="" OR language="' . $clientSyslng . '") ';

		// query for all needed changes - software
		// DON'T use content here.
		$query = '
			SELECT *
			FROM ' . SOFTWARE_TABLE . '
			WHERE
				' . $versionQuery . '
				' . $contentQuery . '
				' . $modulesQuery . '
				' . $sysLngQuery . '
				AND (detail != "patches")
				ORDER BY version DESC
		';

		$languagePart = 'AND ( ';

		if($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT){
			foreach($_SESSION['clientDesiredLanguages'] as &$language){
				$language = str_replace('_UTF-8', '', $language);
			}
			array_unique($_SESSION['clientDesiredLanguages']);
		}
		foreach($_SESSION['clientDesiredLanguages'] as $language){
			$languagePart .= 'language="' . $language . '" OR ';
		}
		$languagePart .= ' 0 )';

		// query for needed changes language
		$languageQuery = '
			SELECT *
			FROM ' . SOFTWARE_LANGUAGE_TABLE . '
			WHERE
				' . $versionQuery . '
				' . $contentQuery . '
				' . $modulesQuery . '
				' . $languagePart . '
				AND (detail != "patches")
				ORDER BY version DESC
		';

		return updateUtil::getChangesArrayByQueries(array($query, $languageQuery));
	}

	function getPrepareApplicationInstallationResponse(){

		$nextUrl = '?' . updateUtil::getCommonHrefParameters($this->getNextUpdateDetail(), true);

		// generate code to drop existing webEdition tables
		require( LIVEUPDATE_SERVER_DIR . "/includes/extras/webEditionTables.inc.php");

		$dropTablesCode = '$dropQueries = array();' . "\n";
		foreach($GLOBALS["allTables"] as $table){
			$dropTablesCode .= '$dropQueries[] = "DROP TABLE IF EXISTS ' . $table . '";' . "\n";
		}
		$dropTablesCode .= '
			if (!$liveUpdateFnc->executeDropQueries($dropQueries)) {
			' . $this->getErrorMessageResponsePart('', '<h1 class=\"error\">' . $GLOBALS['lang']['installer']['tableNotDrop'] . '</h1>') . '
			exit;
		}
		';

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$message = "<h1>' . $GLOBALS['lang'][$this->LanguageIndex][$_REQUEST["detail"]] . '</h1>";

		$success = true;

		// move webEdition if needed:
		if (isset($_SESSION["le_verifyWebEdition"])) { // move webEdition
			$newFolder .= $_SESSION["le_installationDirectory"] . "/webEdition_backup_" . time();
			do {
				$newFolder = $_SESSION["le_installationDirectory"] . "/webEdition_backup_" . time();

			} while (is_dir($newFolder));

			if (file_exists($_SESSION["le_installationDirectory"] . "/webEdition")) {
				if (!rename($_SESSION["le_installationDirectory"] . "/webEdition", $newFolder)) {
					' . $this->getErrorMessageResponsePart() . '
					exit;

				}

			}

		}

		if ($_SESSION["le_db_overwrite"]) { // overwrite webEdition tables
			' . $dropTablesCode . '

		}

		?>' . $this->getProceedNextCommandResponsePart($nextUrl, $this->getInstallerProgressPercent(), '<?php print $message ?>') . '<?php
		';
		return updateUtil::getResponseString($retArray);
	}

	/**
	 * This response updates the installer screen and triggers next step
	 *
	 * @return string
	 */
	function getApplicationFilesResponse($nextUrl = ''){

		$nextUrl = '?' . updateUtil::getCommonHrefParameters($this->getNextUpdateDetail(), true);

		$message = '<p>'
			. sprintf($GLOBALS['lang']['installer']['downloadFilesTotal'], sizeof($_SESSION['clientChanges']['allChanges']))
			. '</p>'
			. '<ul>'
			. '<li>' . sizeof($_SESSION['clientChanges']['files']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesFiles'] . '</li>'
			. '<li>' . sizeof($_SESSION['clientChanges']['queries']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesQueries'] . '</li>'
			. '</ul>';

		$progress = $this->getInstallerProgressPercent();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '
		$filesDir = LE_INSTALLER_TEMP_PATH;
		$liveUpdateFnc->deleteDir($filesDir);

		?>' . $this->getProceedNextCommandResponsePart($nextUrl, $progress, $message);

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
		$nextUrl = '?' . updateUtil::getCommonHrefParameters($this->getNextUpdateDetail(), true);

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
				$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;
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
					$message .= "<h1 class=\'notice\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['lang']['installer']['errorExecutingQuery'] . ' </h1>";
				}
				if(isset($msg)) {
					//error_log(print_r($msg,true));
				}
			}

		}

		$endFile = min(sizeof($allFiles), ' . ($_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP']) . ');
		$maxFile = sizeof($allFiles);

		$message	.=	"</ul>"
					.	"<p>" . sprintf("' . $GLOBALS['lang']['installer']['amountDatabaseQueries'] . '", $endFile, $maxFile) . "</p>";

		if ( sizeof($allFiles) > ' . ( $_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP'] ) . ' ) { // continue with DB steps
			?>' . $this->getProceedNextCommandResponsePart($repeatUrl, $this->getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

		} else { // proceed to next step.
			?>' . $this->getProceedNextCommandResponsePart($nextUrl, $this->getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

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
		$nextUrl = '?' . updateUtil::getCommonHrefParameters($this->getNextUpdateDetail(), true);

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
			$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;

			$message .= "<li>$text</li>";

			if ($liveUpdateFnc->isPhpFile($allFiles[$i])) {
				$success = $liveUpdateFnc->filePutContent($allFiles[$i], $liveUpdateFnc->preparePhpCode($content, ".php", "' . $_SESSION['clientExtension'] . '"));
				if ($success) {
					$success = rename($allFiles[$i], $liveUpdateFnc->replaceExtensionInContent($allFiles[$i], ".php", "' . $_SESSION['clientExtension'] . '"));

				}
			}
		}
		$message .= "</ul>";

		if (!$success) {
			' . $this->getErrorMessageResponsePart() . '

		} else {
			$endFile = min(sizeof($allFiles), ' . ($_REQUEST["position"] + $_SESSION['PREPARE_FILES_PER_STEP']) . ');
			$maxFile = sizeof($allFiles);

			$message .= "<p>" . sprintf(\'' . $GLOBALS['lang']['installer']['amountFilesPrepared'] . '\', $endFile, $maxFile) . "</p>";

			if ( sizeof($allFiles) >= (' . $_SESSION['PREPARE_FILES_PER_STEP'] . ' + ' . $_REQUEST["position"] . ') ) {

				?>' . $this->getProceedNextCommandResponsePart($repeatUrl, $this->getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
			} else {

				?>' . $this->getProceedNextCommandResponsePart($nextUrl, $this->getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
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

		$nextUrl = '?' . updateUtil::getCommonHrefParameters($this->getNextUpdateDetail(), true);

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
			$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;
			$message .= "<li>$text</li>";

			//$success = $liveUpdateFnc->moveFile($allFiles[$i], $_SESSION["le_installationDirectory"] . substr($allFiles[$i], $preLength));

		}
		$message .= "</ul>";

		$success = rename($filesDir."webEdition", $_SESSION["le_installationDirectory"]."/webEdition");

		if ($success) {
			$endFile = sizeof($allFiles);
			$maxFile = sizeof($allFiles);

			$message .= "<p>" . sprintf(\'' . $GLOBALS['lang']['installer']['amountFilesCopied'] . '\', $endFile, $maxFile) . "</p>";

			?>' . $this->getProceedNextCommandResponsePart($nextUrl, $this->getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

		} else {

			' . $this->getErrorMessageResponsePart('', $GLOBALS['lang']['installer']['errorMoveFile']) . '
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

		$nextUrl = '?' . updateUtil::getCommonHrefParameters($this->getNextUpdateDetail(), true);

		// 1st step: missing files
		$replaceVersionDemo = updateUtil::getReplaceCode('we_version_demo');
		// folder we_conf, we_conf.inc.php we_conf_global.inc.php
		$replaceWeConfDemo = updateUtil::getReplaceCode('we_conf_demo');
		$replaceWeConfGlobalDemo = updateUtil::getReplaceCode('we_conf_global_demo');
		$replaceWeActiveModules = updateUtil::getReplaceCode('we_activeModules');

		// we_installed_modules
		$we_installed_modules = updateUtil::encodeCode(modules::getCodeForInstalledModules());

		// we_active_integrated_modules
		$we_active_integrated_modules = updateUtil::encodeCode(modules::getCodeForActiveIntegratedModules());

		// proxy settings
		$replaceProxySettings = updateUtil::getReplaceCode('we_proxysettings');

		// 2nd overwrite some stuff
		$webEdition_demo = updateUtil::getReplaceCode('webEdition_demo');
		$menu1_demo = updateUtil::getReplaceCode('menu1_demo');
		$menu2_demo = updateUtil::getReplaceCode('menu2_demo');
		$editor_demo = updateUtil::getReplaceCode('templateSaveCode_demo');


		$reinstallModules = array();
		$_SESSION['clientInstalledModules'] = $_SESSION['clientDesiredModules'];
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
		if (
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we_backup", 0777 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we_backup/tmp", 0777 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we_backup/download", 0777 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/templates", 0777 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/tmp", 0777 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/versions", 0777 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/include/we_tags/custom_tags", 0777 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/we/include/weTagWizard/we_tags/custom_tags", 0777 ) ||
			!$liveUpdateFnc->checkMakeDir( $_SESSION["le_installationDirectory"] . "/webEdition/site", 0777 ) ||

			!$liveUpdateFnc->filePutContent( $_SESSION["le_installationDirectory"] . "' . $replaceVersionDemo['path'] . '", $liveUpdateFnc->preparePhpCode( sprintf($liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceVersionDemo['replace']) . '"), "' . $version . '", "' . $version_type . '", "' . $zf_version . '", "' . $subversion . '", "' . $version_type_version . '", "' . $version_branch . '", "' . $version_name . '"), ".php","' . $_SESSION['clientExtension'] . '"))  ||
			!$liveUpdateFnc->filePutContent( $_SESSION["le_installationDirectory"] . "' . $replaceWeConfDemo['path'] . '", $liveUpdateFnc->preparePhpCode( sprintf($liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceWeConfDemo['replace']) . '"), $_SESSION["le_db_host"], $_SESSION["le_db_database"], base64_encode($_SESSION["le_db_user"]), base64_encode($_SESSION["le_db_password"]), $_SESSION["le_db_connect"], $_SESSION["le_db_prefix"], $_SESSION["le_db_charset"], $_SESSION["le_db_collation"], "' . $licenceName . '", "' . $_SESSION['clientSyslngNEW'] . '", "' . $_SESSION['client_backend_charset'] . '"), ".php","' . $_SESSION['clientExtension'] . '"))  ||
			!$liveUpdateFnc->filePutContent( $_SESSION["le_installationDirectory"] . "' . $replaceWeConfGlobalDemo['path'] . '", $liveUpdateFnc->preparePhpCode( sprintf($liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceWeConfGlobalDemo['replace']) . '"), "' . $_SESSION['client_default_charset'] . '", $_SESSION["le_db_charset"]), ".php","' . $_SESSION['clientExtension'] . '")) ||

			!$liveUpdateFnc->filePutContent($_SESSION["le_installationDirectory"] . "/webEdition/we/include/conf/we_active_integrated_modules.inc' . $_SESSION['clientExtension'] . '", $liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceWeActiveModules['replace']) . '")) ||


			(isset($_SESSION["le_proxy_use"]) && $_SESSION["le_proxy_use"] ? !$liveUpdateFnc->filePutContent($_SESSION["le_installationDirectory"] . "' . $replaceProxySettings['path'] . '", $liveUpdateFnc->preparePhpCode( sprintf($liveUpdateFnc->decodeCode("' . updateUtil::encodeCode($replaceProxySettings['replace']) . '"), $_SESSION["le_proxy_host"], $_SESSION["le_proxy_port"], $_SESSION["le_proxy_username"], $_SESSION["le_proxy_password"])), ".php","' . $_SESSION['clientExtension'] . '") : false)  ||

			0
			) {
			' . $this->getErrorMessageResponsePart('', $GLOBALS['lang']['installer']['errorMoveFile']) . '
			exit;

		}
			';

		// Insert log --> Languages
		installationLog::insertLanguagesEntry();

		// Insert log --> Modules
		installationLog::insertModulesEntry();

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
		$query = sprintf("' . $tblUserQuery['replace'] . '", $_SESSION[\'le_db_prefix\'], $userText, $_SESSION["le_login_user"], $_SESSION["le_login_pass"]);

		if (!$leDB->query($query)) {
			' . $this->getErrorMessageResponsePart('', $GLOBALS['lang'][$this->LanguageIndex]['dbNotInsertUser']) . '
			exit;

		}';

		if($_SESSION['clientTargetVersionNumber'] >= LANGUAGELIMIT){
			if(strpos($_SESSION['clientSyslng'], 'UTF-8') !== false){
				$backendCH = 'UTF-8';
			} else {
				$backendCH = 'ISO-8859-1';
			}
			$backendCH = 'UTF-8';
			$retArray['Code'] .= '$query = sprintf("' . $tblPrefsQuery['replace'] . '", $_SESSION[\'le_db_prefix\'], "' . $_SESSION['clientSyslngNEW'] . '", "' . $backendCH . '");';
		} else {
			$retArray['Code'] .= '$query = sprintf("' . $tblPrefsQuery['replace'] . '", $_SESSION[\'le_db_prefix\'], "' . $_SESSION['clientSyslngNEW'] . '", "' . $GLOBALS["lang"]["installApplication"]["rss_feed_url"] . '");';
		}
		$retArray['Code'] .= '
		if (!$leDB->query($query)) {
			$query = "INSERT INTO " . $_SESSION[\'le_db_prefix\'] . "tblPrefs (userID,`key`,value) VALUES (\'1\',\'Language\',\'' . $_SESSION['clientSyslngNEW'] . '\');";
			if (!$leDB->query($query)) {
				' . $this->getErrorMessageResponsePart('', $GLOBALS['lang'][$this->LanguageIndex]['dbNotInsertPrefs']) . '
				exit;
			}
			$query = "INSERT INTO " . $_SESSION[\'le_db_prefix\'] . "tblPrefs (userID,`key`,value) VALUES (\'1\',\'BackendCharset\',\'' . $backendCH . '\');";
			if (!$leDB->query($query)) {
				' . $this->getErrorMessageResponsePart('', $GLOBALS['lang'][$this->LanguageIndex]['dbNotInsertPrefs']) . '
				exit;
			}
		}' .
			/*
			  if("' . $_SESSION['clientSyslng'] . '" == "Deutsch" || "' . $_SESSION['clientSyslng'] . '" == "Deutsch_UTF-8") {
			  $query = "INSERT INTO " . $_SESSION[\'le_db_prefix\'] . "tblwidgetnotepad VALUES (1, \'Sonstiges\', 1, \'2007-06-04\', \'Willkommen bei webEdition 5\', ' . htmlentities('\'Das Cockpit ist eine der Neuerungen in Version 5. Sie können im Cockpit-Menü verschiedene Widgets auswählen. Jedes Widget ist über die obere Leiste \"Eigenschaften\" konfigurierbar und kann frei positioniert werden.\'') . ', \'low\', \'always\', \'2007-06-04\', \'2007-06-04\');";

			  } else {
			  $query = "INSERT INTO " . $_SESSION[\'le_db_prefix\'] . "tblwidgetnotepad VALUES (1, \'Miscellaneous\', 1, \'2007-06-04\', \'Welcome to webEdition 5\', ' . htmlentities('\'One of the new features in version 5 is the cockpit. You can select several widgets in the cockpit menu. Each widget can be adjusted and positioned in the title bar.\'') . ', \'low\', \'always\', \'2007-06-04\', \'2007-06-04\');";

			  }

			  if (!$leDB->query($query)) {
			  ' . $this->getErrorMessageResponsePart('', $GLOBALS['lang'][$this->LanguageIndex]['dbNotInsertPrefs']) . '
			  exit;
			  }
			 */
			'?>' . $this->getProceedNextCommandResponsePart($nextUrl, $this->getInstallerProgressPercent(), "<p>" . $GLOBALS['lang'][$this->LanguageIndex]['finished'] . "</p>") . '<?php

		?>';

		return updateUtil::getResponseString($retArray);
	}

}