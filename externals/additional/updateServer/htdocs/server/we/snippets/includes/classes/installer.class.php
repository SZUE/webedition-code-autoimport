<?php

class installer extends installerBase {

	var $LanguageIndex = "installer";

	/**
	 * @return array
	 */
	function getInstallationStepNames() {

		return array(
			'determineFiles',
			'downloadFiles',
			
		);

	}


	/**
	 * returns progress of current installer
	 *
	 * @return integer
	 */
	function getInstallerProgressPercent() {

		// current position
		if (!isset($_REQUEST['position'])) {
			$_REQUEST['position'] = 0;
		}

		// all steps are:

		// - installation steps
		// - all downloads/files per step
		// - queryfiles/queries per step
		// - all files to prepare/prepareFiles per step


		$installationStepsTotal = 0;

		// each step
		$installationSteps = $this->getInstallationStepNames();
		$installationStepsTotal = sizeof($installationSteps);

		// downloads
		$dlSteps = floor(sizeof($_SESSION['clientChanges']['allChanges'])/100);
		$installationStepsTotal += $dlSteps;

		$currentStep = 0;

		switch ($_REQUEST['detail']) {

			case 'determineFiles':
				$currentStep = 1;
			break;
			case 'downloadFiles':
				$currentStep = 2;
				$currentStep += ($_REQUEST['position'] / sizeof($_SESSION['clientChanges']['allChanges'])) * $dlSteps;
			break;
		}

		return number_format(($currentStep/$installationStepsTotal * 100), 0);
		
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
					$nextUrl = '?' . updateUtil::getCommonHrefParameters( $this->getNextUpdateDetail(), true );
					
					// :IMPORTANT:
					return updateUtil::getResponseString(installer::_getDownloadFilesMergeResponse($fileArray, $nextUrl, installer::getInstallerProgressPercent(), $Paths[$Position], $Part));
				
				} else {
					$Position++;
					$nextUrl = '?' . updateUtil::getCommonHrefParameters( $_REQUEST['detail'], false ) . "&position=" . $Position;
					
					// :IMPORTANT:
					return updateUtil::getResponseString(installer::_getDownloadFilesMergeResponse($fileArray, $nextUrl, installer::getInstallerProgressPercent(), $Paths[$Position-1], $Part));
					
				}

				
			} else {
				$Part += 1;
				$nextUrl = '?' . updateUtil::getCommonHrefParameters( $_REQUEST['detail'], false ) . "&part=" . $Part . "&position=" . $Position;

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
				$nextUrl = '?' . updateUtil::getCommonHrefParameters( $this->getNextUpdateDetail(), true );
	
			} else {
				$nextUrl = '?' . updateUtil::getCommonHrefParameters( $_REQUEST['detail'], false ) . "&position=$Position";
	
			}

			// :IMPORTANT:
			return updateUtil::getResponseString(installer::_getDownloadFilesResponse($fileArray, $nextUrl, installer::getInstallerProgressPercent()));

		}

		
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
	function getProceedNextCommandResponsePart($nextUrl, $progress) {

		$activateStep = '';

		if ( strpos($nextUrl, "leStep=" . $_REQUEST["nextLeStep"]) ) {
			$activateStep = '
				top.leWizardStatus.update("' . ($_REQUEST["nextLeWizard"]) . '", "' . ($_REQUEST["nextLeStep"]) . '");
			';

		}

		return '<script type="text/JavaScript">
			top.decreaseSpeed = false;
			top.nextUrl = "' . $nextUrl . '";
			top.leWizardProgress.set("' . $progress . '");
			' . $activateStep . '
			window.setTimeout("top.leWizardForm.proceedUrl();", 500);
		</script>';
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
			$headline = "<br /><strong class=\'errorText\'>" . $GLOBALS['lang'][$this->LanguageIndex][$_REQUEST['detail'] . 'Error'] . '</strong>';
		}

		if ($message) {
			$message .= '<br />\\\n';
		}

		$errorMessage = '"<div class=\'errorDiv\'>"
				. "' . $headline . '<br />\\\n"
				. "' . $message . '"
				. ($GLOBALS["liveUpdateError"]["errorString"] ?	"' . $GLOBALS['lang']['installer']['errorMessage'] . ': <code class=\'errorText\'>" . $GLOBALS["liveUpdateError"]["errorString"] . "</code><br />\\\n"
				.												"' . $GLOBALS['lang']['installer']['errorIn'] . ': <code class=\'errorText\'>" . $GLOBALS["liveUpdateError"]["errorFile"] . "</code><br />\\\n"
				. 												"' . $GLOBALS['lang']['installer']['errorLine'] . ': <code class=\'errorText\'>" . $GLOBALS["liveUpdateError"]["errorLine"] . "</code>\\\n"
															   : "")
				. "</div>\\\n"';

		return $errorMessage;
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

		$errorMessage = ' . $this->getErrorMessage($headline, $message) . ';

		print \'
			<script type="text/javascript">
				top.leWizardContent.appendErrorText("\' . $errorMessage . \'");
				alert("\' . strip_tags($errorMessage) . \'");
			</script>\';
		';
	}

	function getUpdateDetailPosition() {

		$currentStep = $_REQUEST['detail'];

		$steps = $this->getInstallationStepNames();

		for ($i=0; $i<sizeof($steps); $i++) {

			if ($currentStep == $steps[$i]) {
				return $i;
			}
		}
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
			?>' . $this->getProceedNextCommandResponsePart($nextUrl, $progress) . '<?php

		} else {
			' . $this->getErrorMessageResponsePart() . '

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
			?>' . $this->getProceedNextCommandResponsePart($nextUrl, $progress) . '<?php

		} else {
			' . $this->getErrorMessageResponsePart() . '

		}
?>';
		
		return $retArray;

	}
	
}

?>