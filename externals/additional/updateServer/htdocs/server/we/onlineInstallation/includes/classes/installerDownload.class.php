<?php

class installerDownload extends installer {

	var $LanguageIndex = "installerDownload";

	/**
	 * @return array
	 */
	function getInstallationStepNames() {

		return array(
			'determineInstallerFiles',
			'downloadInstallerFiles',
			'prepareInstallerFiles',
			'copyInstallerFiles',
		);

	}


	/**
	 * returns progress of current installer
	 *
	 * @return integer
	 */
	function getInstallerProgressPercent() {

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

		// prepare files
		$prepareSteps = sizeof($_SESSION['clientChanges']['allChanges'])/PREPARE_FILES_PER_STEP;
		$installationStepsTotal += $prepareSteps;

		$currentStep = 0;

		switch ($_REQUEST['detail']) {

			case 'getInstallerFiles':
				$currentStep = 1;
			break;
			case 'downloadInstallerFiles':
				$currentStep = 2;
				$currentStep += ($_REQUEST['position'] / sizeof($_SESSION['clientChanges']['allChanges'])) * $dlSteps;
			break;
			case 'prepareInstallerFiles':
				$currentStep = 3 + $dlSteps;
				$currentStep += ($_REQUEST['position']/PREPARE_FILES_PER_STEP);
			break;
			case 'copyInstallerFiles':
				return 100;
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
					return updateUtil::getResponseString(self::_getDownloadFilesMergeResponse($fileArray, $nextUrl, self::getInstallerProgressPercent(), $Paths[$Position], $Part));
				
				} else {
					$Position++;
					$nextUrl = '?' . updateUtil::getCommonHrefParameters( $_REQUEST['detail'], false ) . "&position=" . $Position;
					
					// :IMPORTANT:
					return updateUtil::getResponseString(self::_getDownloadFilesMergeResponse($fileArray, $nextUrl, self::getInstallerProgressPercent(), $Paths[$Position-1], $Part));
					
				}

				
			} else {
				$Part += 1;
				$nextUrl = '?' . updateUtil::getCommonHrefParameters( $_REQUEST['detail'], false ) . "&part=" . $Part . "&position=" . $Position;

				// :IMPORTANT:
				return updateUtil::getResponseString(self::_getDownloadFilesResponse($fileArray, $nextUrl, self::getInstallerProgressPercent()));

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
			return updateUtil::getResponseString(self::_getDownloadFilesResponse($fileArray, $nextUrl, self::getInstallerProgressPercent()));

		}

		
	}



	/**
	 * @return array
	 */
	function getInstallerFiles() {

		$files = array();
		$dir = LIVEUPDATE_SERVER_DOWNLOAD_DIR;

		$clientDir = "";
		updateUtil::getFilesOfDir($dir, $files);

		// build array for downloading
		$retFiles = array();
		$clientPathPrefix = "/tmp/files/";

		for ($i=0; $i<sizeof($files); $i++) {
			$relPath = str_replace($dir, "", $files[$i]);
			$retFiles["files"]['LE_INSTALLER_TEMP_PATH . "' . $clientPathPrefix . trim($relPath) . '"'] = LIVEUPDATE_SERVER_DOWNLOAD_DIR . trim($relPath);
			$retFiles["allChanges"]['LE_INSTALLER_TEMP_PATH . "' . $clientPathPrefix . trim($relPath) . '"'] = LIVEUPDATE_SERVER_DOWNLOAD_DIR . trim($relPath);

		}
		return $retFiles;

	}


	function getGetInstallerFilesResponse() {

		$nextUrl = '?' . updateUtil::getCommonHrefParameters( $this->getNextUpdateDetail(), true );

		$message	=	'<h1>' . $GLOBALS['lang'][$this->LanguageIndex][$_REQUEST["detail"]] . '</h1>'
					.	'<p>' . sprintf($GLOBALS['lang']['installer']['downloadFilesTotal'], sizeof($_SESSION['clientChanges']['allChanges'])) . '</p>';

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
	 * returns response for prepare the downloaded files
	 * - overwrite doc_root, change extension
	 * - rename (extension)
	 *
	 * @return string
	 */
	function getPrepareInstallerFilesResponse() {

		if (!isset($_REQUEST['position'])) {
			$_REQUEST['position'] = 0;
		}

		$repeatUrl = '?' . updateUtil::getCommonHrefParameters($_REQUEST['detail']) . '&position=' . ($_REQUEST['position']+$_SESSION['PREPARE_FILES_PER_STEP']);
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

		for ($i=' . $_REQUEST["position"] . ',$j=0; $i<sizeof($allFiles) && $success && $j < ' . $_SESSION['PREPARE_FILES_PER_STEP'] . '; $i++,$j++) {
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
			$endFile = ' . sizeof($_SESSION['clientChanges']['allChanges']) . ';
			$maxFile = ' . sizeof($_SESSION['clientChanges']['allChanges']) . ';

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
	function getCopyFilesResponse() {

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

		for ($i=0;$success && $i<sizeof($allFiles);$i++) {
			$text = basename($allFiles[$i]);
			$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;
			$message .= "<li>$text</li>";
			$success = $liveUpdateFnc->moveFile($allFiles[$i], LE_INSTALLER_PATH . substr($allFiles[$i], $preLength));

		}
		$message .= "</ul>";

		if ($success) {
			$endFile = ' . sizeof($_SESSION['clientChanges']['allChanges']) . ';
			$maxFile = ' . sizeof($_SESSION['clientChanges']['allChanges']) . ';

			$message .= "<p>" . sprintf(\'' . $GLOBALS['lang']['installer']['amountFilesCopied'] . '\', $endFile, $maxFile) . "</p>";

			?>' . $this->getProceedNextCommandResponsePart($nextUrl, $this->getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

		} else {

			' . $this->getErrorMessageResponsePart('', $GLOBALS['lang']['installer']['errorMoveFile']) . '
		}
		?>';
		
		return updateUtil::getResponseString($retArray);

	}

}

?>