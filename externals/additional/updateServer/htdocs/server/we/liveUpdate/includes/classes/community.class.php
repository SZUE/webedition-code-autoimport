<?php

class community extends communityBase {

	/**
	 * returns url to the screen to confirm the installation
	 *
	 * @return string
	 */
	function getConfirmDeauthenticationWindow() {
		//return 'javascript:window.open(\'?' . updateUtil::getCommonHrefParameters('installer', 'confirmInstallation') . '\', \'confirmUpdate' . time() . '\', \'dependent=yes,height=250,width=600,menubar=no,location=no,resizable=no,status=no,toolbar=no,scrollbars=no\')';
		//return 'javascript:window.open(\'?' . updateUtil::getCommonHrefParameters('community', 'confirmDeauthentication') . '\', \'confirmUpdate' . time() . '\', \'dependent=yes,height=250,width=600,menubar=no,location=no,resizable=no,status=no,toolbar=no,scrollbars=no\')';
		//return 'javascript:alert("huhu");';
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/confirmDeauthentication.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	function getDeauthenticationWindow() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/deauthenticate.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	function getAuthenticationFormWindow() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/authenticateForm.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	function getAuthenticationWindow() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/reauthenticate.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	function getReauthenticationFormWindow() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/reauthenticateForm.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	function getReauthenticationWindow() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/authenticate.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	function getNotAvailableAtTheMomentResponse() {
		//$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/upgradeMaintenance.inc.php');
		//return updateUtil::getResponseString($ret);
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/maintenance.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	/**
	 * returns next step according to installation step names
	 *
	 * @param mixed $currentStep
	 * @return string
	 */
	function getNextUpdateDetail($currentStep=false) {
		if (!$currentStep) {
			$currentStep = $_REQUEST['detail'];
		}
		$steps = installer::getInstallationStepNames();
		for ($i=0;$i<sizeof($steps);$i++) {
			if ($currentStep == $steps[$i]) {
				return $steps[($i+1)];

			}

		}
		return $steps[$i];

	}


	/**
	 * returns the name of the command (installer) at the used detail
	 * returns installer as default. This function is controller, which steps
	 * are done from installer or form update_cmd itself
	 * uses session['update_cmd']
	 *
	 * @param unknown_type $detail
	 * @return unknown
	 */
	function getCommandNameForDetail($detail) {

		$cmd['update'] = array('getChanges', 'finishInstallation');
		$cmd['modules'] = array('getChanges', 'finishInstallation');
		$cmd['languages'] = array('getChanges', 'finishInstallation');
		$cmd['upgrade'] = array('getChanges', 'copyFiles', 'executePatches', 'finishInstallation');

		if ( in_array($detail, $cmd[$_SESSION['update_cmd']]) ) {
			return $_SESSION['update_cmd'];

		} else {
			return 'installer';

		}

	}


	/**
	 * @return string
	 */
	function getJsFunctions() {

		return '<script type="text/javascript">

	var decreaseSpeed = 1; // is set false, when script was successful, otherwise decrease speed
	var nextUrl = "?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail('downloadInstaller'), 'downloadInstaller') . '";

	function proceedUrl() {
		if (!decreaseSpeed) {
			top.frames["updateload"].document.location = nextUrl;
		} else {
			top.frames["updateload"].document.location = nextUrl + "&decreaseSpeed=1";
		}
		decreaseSpeed = 1;
	}

	function appendMessageLog(newText) {

		var messageLog = document.getElementById("messageLog");
		messageLog.innerHTML += "\n" + newText;
		messageLog.scrollTop = 100000;
	}

	function activateLiInstallerStep(stepId) {
		document.getElementById(stepId).className = "activeStep";
	}

	function finishLiInstallerStep(stepId) {
		document.getElementById(stepId).className = "finishedStep";
	}

</script>';
	}

	/**
	 * returns screen to confirm installation
	 *
	 * @return string
	 */
	function getConfirmInstallationResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/installer/confirmInstallation.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * @return string
	 */
	function getInstallationScreenResponse() {
		$GLOBALS['updateServerTemplateData']['installationSteps'] = installer::getInstallationStepNames();

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/installer/initialInstallerScreen.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * returns order for client to install the new installer.
	 * rest of installation is done with these files then.
	 *
	 * @return string
	 */
	function getDownloadInstallerResponse() {

		$files = installer::getInstallerFilesArray();

		$nextStep = installer::getNextUpdateDetail();
		$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters( installer::getCommandNameForDetail($nextStep), $nextStep);

		$retArray = installer::_getDownloadFilesResponse($files, $nextUrl);

		return updateUtil::getResponseString($retArray);

	}


	/**
	 * returns response string with orders for the client to download the files
	 * in the filesArray and the nextCmd, $nextDetail to proceed
	 *
	 * @param array $filesArray
	 * @param string $nextCmd
	 * @param string $nextDetail
	 * @return array
	 */
	function _getDownloadFilesResponse($filesArray, $nextUrl, $progress=0) {

		// prepare $filesArray (path => encodedContent) for the client
		$writeFilesCode = '
			$files = array();';

		foreach ($filesArray as $path => $content) {
			$writeFilesCode .= '
				$files[' . $path . '] = "' . $content . '";';

		}

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

	' . updateUtil::getOverwriteClassesCode() . '

	' . $writeFilesCode . '

		$success = true; // all files fine
		$successFiles = array(); // successfully saved files

		foreach ($files as $path => $content) {

			if ($success) {

				if ($liveUpdateFnc->filePutContent( $path, $liveUpdateFnc->decodeCode($content) ) ) {
					$successFiles[] = $path;
				} else {
					$errorFile = $path;
					$success = false;
				}
			}
		}

		if ($success) {

			$message = "";
			foreach ($successFiles as $path) {

				$text = basename($path);
				$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;

				$message .= "<div> ...$text</div>";
			}

			?>' . installer::getProceedNextCommandResponsePart($nextUrl, $progress, '<?php print $message; ?>') . '<?php

		} else {

			' . installer::getErrorMessageResponsePart() . '
		}
?>';

		return $retArray;

	}

	/**
	 * returns response string with orders for the client to download the files
	 * in the filesArray and the nextCmd, $nextDetail to proceed
	 *
	 * @param array $filesArray
	 * @param string $nextCmd
	 * @param string $nextDetail
	 * @return array
	 */
	function _getDownloadFilesMergeResponse($filesArray, $nextUrl, $progress=0, $Realname, $numberOfParts) {

		// prepare $filesArray (path => encodedContent) for the client
		$writeFilesCode = '
			$files = array();';

		foreach ($filesArray as $path => $content) {

			$writeFilesCode .= '
				$files[' . $path . '] = "' . $content . '";';
		}

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

	' . updateUtil::getOverwriteClassesCode() . '

	' . $writeFilesCode . '

		$success = true; // all files fine
		$successFiles = array(); // successfully saved files

		foreach ($files as $path => $content) {
			if ($success) {
				if (!$liveUpdateFnc->filePutContent( $path, $liveUpdateFnc->decodeCode($content) ) ) {
					$errorFile = $path;
					$success = false;

				}

			}

		}
		
		$Content = "";
		for ($i = 0; $i <= ' . $numberOfParts . '; $i++) {
			$Content .= $liveUpdateFnc->getFileContent(' . $Realname . '."part" . $i);

		}
		
		if ($liveUpdateFnc->filePutContent( ' . $Realname . ', $Content ) ) {
			$successFiles[] = $path;
			
		}
		for ($i = 0; $i <= ' . $numberOfParts . '; $i++) {
			$liveUpdateFnc->deleteFile(' . $Realname . '."part" . $i);

		}


		if ($success) {

			$message = "";
			foreach ($successFiles as $path) {

				$text = basename(' . $Realname . ');
				$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;

				$message .= "<div> ...$text</div>";
			}
			?>' . installer::getProceedNextCommandResponsePart($nextUrl, $progress) . '<?php

		} else {
			' . installer::getErrorMessageResponsePart() . '

		}
?>';
		
		return $retArray;

	}


	/**
	 * This response updates the installer screen and triggers next step
	 *
	 * @return string
	 */
	function getGetChangesResponse($nextUrl='' ) {

		$nextUrl = '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail(installer::getNextUpdateDetail()), installer::getNextUpdateDetail());

		$message =	'<div>' . sprintf($GLOBALS['lang']['installer']['downloadFilesTotal'], sizeof($_SESSION['clientChanges']['allChanges'])) . '<br />' .
					sizeof($_SESSION['clientChanges']['files']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesFiles'] . '<br />' .
					sizeof($_SESSION['clientChanges']['queries']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesQueries'] . '<br />' .
					sizeof($_SESSION['clientChanges']['patches']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesPatches'] . '<br /></div>';

		$progress = installer::getInstallerProgressPercent();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '
		$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
		$liveUpdateFnc->deleteDir($filesDir);

		?>' . installer::getProceedNextCommandResponsePart($nextUrl, $progress, $message);

		return updateUtil::getResponseString($retArray);

	}


	/**
	 * returns code for response to update database
	 *
	 * @return string
	 */
	function getUpdateDatabaseResponse() {

		if (!isset($_REQUEST['position'])) {
			$_REQUEST['position'] = 0;
		}

		$repeatUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail']) . '&position=' . ($_REQUEST['position']+$_SESSION['EXECUTE_QUERIES_PER_STEP']);
		$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail(installer::getNextUpdateDetail()), installer::getNextUpdateDetail());

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$queryDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/queries/";

		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $queryDir);
		sort($allFiles);

		$message = "";

		for ($i=' . $_REQUEST['position'] . '; $i<sizeof($allFiles) && $i<' . ($_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP']) . '; $i++) {

			// execute queries in each file
			if ($liveUpdateFnc->executeQueriesInFiles($allFiles[$i])) {

				$text = basename($allFiles[$i]);
				$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;

				$message .= "<div>...$text</div>";

			} else {

				$msg = $liveUpdateFnc->getQueryLog();
				$fileName = basename($allFiles[$i]);

				if ($msg["tableExists"]) {
					$message .= "<div class=\'messageDiv\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['luSystemLanguage']['installer']['tableExists'] . '</div>";
					$liveUpdateFnc->insertQueryLogEntries("tableExists", $fileName . ": ' . $GLOBALS['luSystemLanguage']['installer']['tableExists'] . '", 2, "' . $_SESSION['clientVersion'] . '");
				}

				if ($msg["tableChanged"]) {
					$message .= "<div class=\'messageDiv\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['luSystemLanguage']['installer']['tableChanged'] . '</div>";
					$liveUpdateFnc->insertQueryLogEntries("tableChanged", $fileName . ": ' . $GLOBALS['luSystemLanguage']['installer']['tableChanged'] . '", 2, "' . $_SESSION['clientVersion'] . '");
				}

				if ($msg["entryExists"]) {
					$message .= "<div class=\'messageDiv\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['luSystemLanguage']['installer']['entryAlreadyExists'] . '</div>";
					$liveUpdateFnc->insertQueryLogEntries("entryExists", $fileName . ": ' . $GLOBALS['luSystemLanguage']['installer']['entryAlreadyExists'] . '", 2, "' . $_SESSION['clientVersion'] . '");
				}

				if ($msg["error"]) {
					$message .= "<div class=\'errorDiv\'>' . $GLOBALS['lang']['installer']['updateDatabaseNotice'] . '<br />$fileName: ' . $GLOBALS['luSystemLanguage']['installer']['errorExecutingQuery'] . '</div>";
					$liveUpdateFnc->insertQueryLogEntries("error", $fileName . ": ' . $GLOBALS['luSystemLanguage']['installer']['errorExecutingQuery'] . '", 1, "' . $_SESSION['clientVersion'] . '");
				}

				$liveUpdateFnc->clearQueryLog();
			}
		}

		if ( sizeof($allFiles) > ' . ( $_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP'] ) . ' ) { // continue with DB steps

			?>' . installer::getProceedNextCommandResponsePart($repeatUrl, installer::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

		} else { // proceed to next step.

			?>' . installer::getProceedNextCommandResponsePart($nextUrl, installer::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
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
	function getPrepareChangesResponse() {

		if (!isset($_REQUEST['position'])) {
			$_REQUEST['position'] = 0;
		}

		$repeatUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail']) . '&position=' . ($_REQUEST['position'] + $_SESSION['PREPARE_FILES_PER_STEP']);
		$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail(installer::getNextUpdateDetail()), installer::getNextUpdateDetail());

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/";

		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $filesDir);
		sort($allFiles);

		$message = "";
		$success = true;

		for ( $i=' . $_REQUEST["position"] . ',$j=0; $i<sizeof($allFiles) && $success && $j < ' . $_SESSION['PREPARE_FILES_PER_STEP'] . '; $i++,$j++) {

			$content = $liveUpdateFnc->getFileContent($allFiles[$i]);

			$text = basename($allFiles[$i]);
			$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;

			$message .= "<div>...$text</div>";

			if ($liveUpdateFnc->isPhpFile($allFiles[$i])) {
				$success = $liveUpdateFnc->filePutContent($allFiles[$i], $liveUpdateFnc->preparePhpCode($content, ".php", "' . $_SESSION['clientExtension'] . '"));
				if ($success) {
					$success = rename($allFiles[$i], $liveUpdateFnc->replaceExtensionInContent($allFiles[$i], ".php", "' . $_SESSION['clientExtension'] . '"));
				}
			}
		}

		if (!$success) {

			' . installer::getErrorMessageResponsePart() . '

		} else {

			if ( sizeof($allFiles) >= (' . $_SESSION['PREPARE_FILES_PER_STEP'] . ' + ' . $_REQUEST["position"] . ') ) {

				?>' . installer::getProceedNextCommandResponsePart($repeatUrl,installer::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
			} else {

				?>' . installer::getProceedNextCommandResponsePart($nextUrl, installer::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
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
	function getCopyFilesResponse() {

		$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail(installer::getNextUpdateDetail()), installer::getNextUpdateDetail());

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/";
		$preLength = strlen($filesDir);

		$success = true;

		$message = "";

		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $filesDir);

		for ($i=0;$success && $i<sizeof($allFiles);$i++) {

			$text = basename($allFiles[$i]);
			$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;
			$message .= "<div>...$text</div>";

			$success = $liveUpdateFnc->moveFile($allFiles[$i], LIVEUPDATE_SOFTWARE_DIR . substr($allFiles[$i], $preLength));
		}

		if ($success) {

			$message = "<div>" . $message . "</div>";
			$message .= "<div>' . sprintf($GLOBALS['lang']['installer']['amountFilesCopied'], sizeof($_SESSION['clientChanges']['files'])) . '</div>";

			?>' . installer::getProceedNextCommandResponsePart($nextUrl, installer::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

		} else {

			' . installer::getErrorMessageResponsePart() . '
		}
		?>';
		return updateUtil::getResponseString($retArray);

	}


	/**
	 * returns response to execute all patches
	 *
	 * @return string
	 */
	function getExecutePatchesResponse() {

		$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail(installer::getNextUpdateDetail()), installer::getNextUpdateDetail());

		$retArray['Type'] = 'eval';
		//$retArray['Type'] = 'executePatches';		
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '

		$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/patches/";

		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $filesDir);

		$success = true;
		$message = "";

		for ($i=0; $success && $i<sizeof($allFiles); $i++) {

			$message .= basename($allFiles[$i]) . "<br />";
			$success = $liveUpdateFnc->executePatch($allFiles[$i]);
			if (!$success) {
				$errorFile = basename($allFiles[$i]);
			}
		}

		if ($success) {

			$message = "<div>" . $message . "</div>";
			$message .= "<div>' . sprintf($GLOBALS['lang']['installer']['amountPatchesExecuted'], sizeof($_SESSION['clientChanges']['patches'])) . '</div>";
			?>' . installer::getProceedNextCommandResponsePart($nextUrl, installer::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

		} else {
			' . installer::getErrorMessageResponsePart(stripslashes('$errorFile'), '{$GLOBALS["errorDetail"]}') . '
		}
		?>';

		return updateUtil::getResponseString($retArray);

	}


	/**********************************************************
	/* Needed php Scripts and functions
	/**********************************************************/


	/**********************************************************
	/* Check and Downloading the installer
	/**********************************************************/

	/**
	 * returns paths to needed installer for download, regarding the version of
	 * client
	 *
	 * @return array
	 */
	function getInstallerFilesArray() {

		$availableInstallers = array();

		$d = dir(LIVEUPDATE_SERVER_DOWNLOAD_DIR);
		while ($entry = $d->read()) {

			if ( !($entry == '.' || $entry == '..') ) {
				$availableInstallers[str_replace('version', '', $entry)] = $entry;
			}
		}
		$d->close();

		$installerVersionDir = $availableInstallers[updateUtil::getNearestVersion($availableInstallers, $_SESSION['clientTargetVersionNumber'])];
		$installerDir = LIVEUPDATE_SERVER_DOWNLOAD_DIR . '/' . $installerVersionDir;

		$fileArray["LIVEUPDATE_CLIENT_DOCUMENT_DIR . '/updateClient/liveUpdateServer" . $_SESSION['clientExtension'] . "'"] = updateUtil::getFileContentEncoded($installerDir . '/updateClient/liveUpdateServer.php', true);
		$fileArray["LIVEUPDATE_CLIENT_DOCUMENT_DIR . '/updateClient/liveUpdateFunctionsServer.class" . $_SESSION['clientExtension'] . "'"] = updateUtil::getFileContentEncoded($installerDir . '/updateClient/liveUpdateFunctionsServer.class.php', true);
		$fileArray["LIVEUPDATE_CLIENT_DOCUMENT_DIR . '/updateClient/liveUpdateResponseServer.class" . $_SESSION['clientExtension'] . "'"] = updateUtil::getFileContentEncoded($installerDir . '/updateClient/liveUpdateResponseServer.class.php', true);

		return $fileArray;

	}


	/**
	 * returns url for the downloaded installer
	 *
	 * @return string
	 */
	function getUpdateClientUrl() {
		return dirname($_SESSION['clientUpdateUrl']) . '/updateClient/liveUpdateServer' . $_SESSION['clientExtension'];

	}


	/**
	 * returns response part, with javascript orders to
	 * - append message to messageDiv
	 * - setProgressBar
	 * - setNextUrl
	 *
	 * @param string $nextUrl
	 * @param integer $progress
	 * @param string $message
	 * @return string
	 */
	function getProceedNextCommandResponsePart($nextUrl, $progress, $message='') {

		$activateStep = '';
		if ( !strpos($nextUrl, $_REQUEST['detail']) ) {

			$NextUpdateDetail = installer::getNextUpdateDetail();
			if(key_exists($NextUpdateDetail, $GLOBALS['lang']['installer'])) {
				$message .= "<br /><strong>" . $GLOBALS['lang']['installer'][$NextUpdateDetail] . "</strong>";
				
			}
			
			$activateStep = '
			top.frames["updatecontent"].finishLiInstallerStep("' . $_REQUEST['detail'] . '");
			top.frames["updatecontent"].activateLiInstallerStep("' . installer::getNextUpdateDetail() . '");';
		}

		return '<script type="text/javascript">
			top.frames["updatecontent"].decreaseSpeed = false;
			top.frames["updatecontent"].nextUrl = "' . $nextUrl . '";
			top.frames["updatecontent"].setProgressBar("' . $progress . '");
			top.frames["updatecontent"].appendMessageLog("' . $message . '\n");
			' . $activateStep . '
			window.setTimeout("top.frames[\"updatecontent\"].proceedUrl();", 50);
		</script>';

	}


	/**
	 * This errormessage is added to each step dealing with files. It generates needed
	 * code for updateing errorlog, javascript-message and errorBox
	 * - download
	 * - prepare
	 * - copy
	 * - pathces
	 *
	 * @param string $headline
	 * @param string $message
	 * @return string
	 */
	function getErrorMessageResponsePart($headline='', $message='') {

		return '

		$errorMessage = ' . installer::getErrorMessage($headline, $message) . ';

		$liveUpdateFnc->insertUpdateLogEntry($errorMessage, "' . (isset($_SESSION['clientTargetVersion']) ? $_SESSION['clientTargetVersion'] : $_SESSION['clientVersion']) . '", 1);

		print \'
			<script type="text/javascript">
				top.frames["updatecontent"].appendMessageLog("\' . $errorMessage . \'");
				alert("\' . strip_tags($errorMessage) . \'");
			</script>\';
		';

	}


	/**
	 * returns response to finish the installation
	 *
	 * @param string $message
	 * @param string $progress
	 * @return string
	 */
	function getFinishInstallationResponsePart($message, $jsMessage='', $progress=100) {

		if (!$jsMessage) {
			$jsMessage = strip_tags($message);
		}

		return '<script type="text/javascript">
			top.frames["updatecontent"].setProgressBar("' . $progress . '");
			top.frames["updatecontent"].appendMessageLog("' . $message . '\n");
			window.open(\'?' . updateUtil::getCommonHrefParameters('installer', 'finishInstallationPopUp') . '\', \'finishInstallationPopUp' . session_id() . '\', \'dependent=yes,height=250,width=600,menubar=no,location=no,resizable=no,status=no,toolbar=no,scrollbars=no\');
//			alert("' . $jsMessage . '");
		</script>';

	}


	function getFinishInstallationPopUpResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/installer/finishInstallationPopUp.inc.php');
		return updateUtil::getResponseString($ret);

	}

	/**
	 * returns string for eval Response to generate output for a php error
	 * appends php error, when necessayr
	 *
	 * @param string $headline
	 * @return string
	 */
	function getErrorMessage($headline='', $message='') {

		if (!$headline) {
			$headline = "<br /><strong class=\'errorText\'>" . $GLOBALS['luSystemLanguage']['installer'][$_REQUEST['detail'] . 'Error'] . '</strong>';

		}

		if ($message) {
			$message .= '<br />\\\n';

		}

		$errorMessage = '"<div class=\'errorDiv\'>"
				. "' . $headline . '<br />\\\n"
				. "' . $message . '"
				. ($GLOBALS["liveUpdateError"]["errorString"] ?	"' . $GLOBALS['luSystemLanguage']['installer']['errorMessage'] . ': <code class=\'errorText\'>" . $GLOBALS["liveUpdateError"]["errorString"] . "</code><br />\\\n"
				.												"' . $GLOBALS['luSystemLanguage']['installer']['errorIn'] . ': <code class=\'errorText\'>" . $GLOBALS["liveUpdateError"]["errorFile"] . "</code><br />\\\n"
				. 												"' . $GLOBALS['luSystemLanguage']['installer']['errorLine'] . ': <code class=\'errorText\'>" . $GLOBALS["liveUpdateError"]["errorLine"] . "</code>\\\n"
															   : "")
				. "</div>\\\n"';

		return $errorMessage;

	}
	
	
	function getDownloadChangesResponse() {

		// current position
		if (!isset($_REQUEST['position'])) {
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
		
		if(		(isset($_REQUEST['part']) && $_REQUEST['part'] > 0)
			||	$FileSize > $_SESSION['DOWNLOAD_KBYTES_PER_STEP']*1024) {
				
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
			
			if($Start + $Length >= $FileSize) {
				if($Position >= sizeof($_SESSION['clientChanges']['allChanges'])) {
					$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail(installer::getNextUpdateDetail()), installer::getNextUpdateDetail() );
			
					// :IMPORTANT:
					return updateUtil::getResponseString(installer::_getDownloadFilesMergeResponse($fileArray, $nextUrl, installer::getInstallerProgressPercent(), $Paths[$Position], $Part));
				
				} else {
					$Position++;
					$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail'] ) . "&position=" . $Position;
					
					// :IMPORTANT:
					return updateUtil::getResponseString(installer::_getDownloadFilesMergeResponse($fileArray, $nextUrl, installer::getInstallerProgressPercent(), $Paths[$Position-1], $Part));
					
				}

				
			} else {
				$Part += 1;
				$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail'] ) . "&part=" . $Part . "&position=" . $Position;
				
				// :IMPORTANT:
				return updateUtil::getResponseString(installer::_getDownloadFilesResponse($fileArray, $nextUrl, installer::getInstallerProgressPercent()));

			}
			
		// Only whole files	with max. $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] kbytes per step
		} else {
			
			$ResponseSize = 0;
			do {
				
				if($Position >= sizeof($Paths)) {
					break;
					
				}
				
				$FileSize = filesize($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);
				
				// response + size of next file < max size for response
				if( $ResponseSize + $FileSize < $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024 ) {
					$ResponseSize += $FileSize;
					
					$fileArray[$Paths[$Position]] = updateUtil::getFileContentEncoded($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);
					$Position++;
					
				} else {
					break;
					
				}
				
			} while ( $ResponseSize < $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024 );

			if ( $Position >= sizeof($_SESSION['clientChanges']['allChanges']) ) {
				$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail(installer::getNextUpdateDetail()), installer::getNextUpdateDetail() );
		
			} else {
				$nextUrl = installer::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(installer::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail'] ) . "&position=$Position";
			
			}
	
			// :IMPORTANT:
			return updateUtil::getResponseString(installer::_getDownloadFilesResponse($fileArray, $nextUrl, installer::getInstallerProgressPercent()));
			
		}

		
	}

}
?>