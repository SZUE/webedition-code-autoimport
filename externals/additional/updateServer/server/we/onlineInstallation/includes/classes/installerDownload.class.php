<?php
/**
 * $Id$
 */

class installerDownload extends installerInstaller{

	static $LanguageIndex = "installerDownload";

	/**
	 * @return array
	 */
	static function getInstallationStepNames(){

		return ['determineInstallerFiles',
			'downloadInstallerFiles',
			'prepareInstallerFiles',
			'copyInstallerFiles',
			];
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

		// each step
		$installationSteps = static::getInstallationStepNames();
		$installationStepsTotal = count($installationSteps);

		// downloads
		$dlSteps = floor(count($_SESSION['clientChanges']['allChanges']) / 100);
		$installationStepsTotal += $dlSteps;

		// prepare files
		$prepareSteps = count($_SESSION['clientChanges']['allChanges']) / PREPARE_FILES_PER_STEP;
		$installationStepsTotal += $prepareSteps;

		$currentStep = 0;

		switch($_REQUEST['detail']){

			case 'getInstallerFiles':
				$currentStep = 1;
				break;
			case 'downloadInstallerFiles':
				$currentStep = 2;
				$currentStep += ($_REQUEST['position'] / count($_SESSION['clientChanges']['allChanges'])) * $dlSteps;
				break;
			case 'prepareInstallerFiles':
				$currentStep = 3 + $dlSteps;
				$currentStep += ($_REQUEST['position'] / PREPARE_FILES_PER_STEP);
				break;
			case 'copyInstallerFiles':
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

		$Content = updateUtilInstaller::getFileContent($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);
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
			$Value = updateUtilInstaller::encodeCode(substr($Content, $Start, $Length));

			$fileArray[$Paths[$Position] . ".'part" . $Part . "'"] = $Value;

			if($Start + $Length >= $FileSize){
				if($Position >= count($_SESSION['clientChanges']['allChanges'])){
					$nextUrl = '?' . updateUtilInstaller::getCommonHrefParameters(static::getNextUpdateDetail(), true);

					// :IMPORTANT:
					return updateUtilInstaller::getResponseString(static::_getDownloadFilesMergeResponse($fileArray, $nextUrl, static::getInstallerProgressPercent(), $Paths[$Position], $Part));
				}
				$Position++;
				$nextUrl = '?' . updateUtilInstaller::getCommonHrefParameters($_REQUEST['detail'], false) . "&position=" . $Position;

				// :IMPORTANT:
				return updateUtilInstaller::getResponseString(static::_getDownloadFilesMergeResponse($fileArray, $nextUrl, static::getInstallerProgressPercent(), $Paths[$Position - 1], $Part));
			}
			$Part += 1;
			$nextUrl = '?' . updateUtilInstaller::getCommonHrefParameters($_REQUEST['detail'], false) . "&part=" . $Part . "&position=" . $Position;

			// :IMPORTANT:
			return updateUtilInstaller::getResponseString(static::_getDownloadFilesResponse($fileArray, $nextUrl, static::getInstallerProgressPercent()));


			// Only whole files	with max. $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] kbytes per step
		}

		$ResponseSize = 0;
		do{

			if($Position >= count($Paths)){
				break;
			}

			$FileSize = filesize($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);

			// response + size of next file < max size for response
			if($ResponseSize + $FileSize < $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024){
				$ResponseSize += $FileSize;

				$fileArray[$Paths[$Position]] = updateUtilInstaller::getFileContentEncoded($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);
				$Position++;
			} else {
				break;
			}
		}while($ResponseSize < $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024);

		$nextUrl = ($Position >= count($_SESSION['clientChanges']['allChanges']) ?
						'?' . updateUtilInstaller::getCommonHrefParameters(static::getNextUpdateDetail(), true) :
						'?' . updateUtilInstaller::getCommonHrefParameters($_REQUEST['detail'], false) . "&position=$Position"
				);

		// :IMPORTANT:
		return updateUtilInstaller::getResponseString(static::_getDownloadFilesResponse($fileArray, $nextUrl, static::getInstallerProgressPercent()));
	}

	/**
	 * @return array
	 */
	function getInstallerFiles(){

		$files = array();
		$dir = LIVEUPDATE_SERVER_DOWNLOAD_DIR;

		$clientDir = "";
		updateUtilInstaller::getFilesOfDir($dir, $files);

		// build array for downloading
		$retFiles = array();
		$clientPathPrefix = "/tmp/files/";

		for($i = 0; $i < count($files); $i++){
			$relPath = str_replace($dir, "", $files[$i]);
			$retFiles["files"]['LE_INSTALLER_TEMP_PATH . "' . $clientPathPrefix . trim($relPath) . '"'] = LIVEUPDATE_SERVER_DOWNLOAD_DIR . trim($relPath);
			$retFiles["allChanges"]['LE_INSTALLER_TEMP_PATH . "' . $clientPathPrefix . trim($relPath) . '"'] = LIVEUPDATE_SERVER_DOWNLOAD_DIR . trim($relPath);
		}
		return $retFiles;
	}

	function getGetInstallerFilesResponse(){

		$nextUrl = '?' . updateUtilInstaller::getCommonHrefParameters(static::getNextUpdateDetail(), true);

		$message = '<h1>' . $GLOBALS['lang'][self::$LanguageIndex][$_REQUEST["detail"]] . '</h1>'
				. '<p>' . sprintf($GLOBALS['lang']['installer']['downloadFilesTotal'], count($_SESSION['clientChanges']['allChanges'])) . '</p>';

		$progress = self::getInstallerProgressPercent();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtilInstaller::getOverwriteClassesCode() . '
		$filesDir = LE_INSTALLER_TEMP_PATH;
		$liveUpdateFnc->deleteDir($filesDir);

		?>' . static::getProceedNextCommandResponsePart($nextUrl, $progress, $message);

		return updateUtilInstaller::getResponseString($retArray);
	}

	/**
	 * returns response for prepare the downloaded files
	 * - overwrite doc_root, change extension
	 * - rename (extension)
	 *
	 * @return string
	 */
	function getPrepareInstallerFilesResponse(){

		if(!isset($_REQUEST['position'])){
			$_REQUEST['position'] = 0;
		}

		$repeatUrl = '?' . updateUtilInstaller::getCommonHrefParameters($_REQUEST['detail']) . '&position=' . ($_REQUEST['position'] + $_SESSION['PREPARE_FILES_PER_STEP']);
		$nextUrl = '?' . updateUtilInstaller::getCommonHrefParameters(static::getNextUpdateDetail(), true);

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtilInstaller::getOverwriteClassesCode() . '

		$filesDir = LE_INSTALLER_TEMP_PATH . "/tmp/files/";

		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $filesDir);
		sort($allFiles);

		$message = "<ul>";
		$success = true;

		for ($i=' . $_REQUEST["position"] . ',$j=0; $i<count($allFiles) && $success && $j < ' . $_SESSION['PREPARE_FILES_PER_STEP'] . '; $i++,$j++) {
			$content = $liveUpdateFnc->getFileContent($allFiles[$i]);

			$text = substr(basename($allFiles[$i]), -40);
			$message .= "<li>$text</li>";

			if ($liveUpdateFnc->isPhpFile($allFiles[$i])) {
				$success = $liveUpdateFnc->filePutContent($allFiles[$i], $liveUpdateFnc->preparePhpCode($content, ".php", "' . $_SESSION['clientExtension'] . '"));

			}

		}
		$message .= "</ul>";

		if (!$success) {
			' . static::getErrorMessageResponsePart() . '

		} else {
			$endFile = ' . count($_SESSION['clientChanges']['allChanges']) . ';
			$maxFile = ' . count($_SESSION['clientChanges']['allChanges']) . ';

			$message .= "<p>" . sprintf(\'' . $GLOBALS['lang']['installer']['amountFilesPrepared'] . '\', $endFile, $maxFile) . "</p>";
			if ( count($allFiles) >= (' . $_SESSION['PREPARE_FILES_PER_STEP'] . ' + ' . $_REQUEST["position"] . ') ) {
				?>' . static::getProceedNextCommandResponsePart($repeatUrl, self::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

			} else {
				?>' . static::getProceedNextCommandResponsePart($nextUrl, self::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php

			}

		}
		?>';

		return updateUtilInstaller::getResponseString($retArray);
	}

	/**
	 * returns response to copy new files to correct location
	 *
	 * @return string
	 */
	static function getCopyFilesResponse(){

		$nextUrl = '?' . updateUtilInstaller::getCommonHrefParameters(static::getNextUpdateDetail(), true);

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtilInstaller::getOverwriteClassesCode() . '

		$filesDir = LE_INSTALLER_TEMP_PATH . "/tmp/files/";
		$preLength = strlen($filesDir);

		$success = true;

		$message = "<ul>";

		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $filesDir);

		for ($i=0;$success && $i<count($allFiles);$i++) {
			$text = substr(basename($allFiles[$i]), -40);
			$message .= "<li>$text</li>";
			$success = $liveUpdateFnc->moveFile($allFiles[$i], LE_INSTALLER_PATH . substr($allFiles[$i], $preLength));

		}
		$message .= "</ul>";

		if ($success) {
			$endFile = ' . count($_SESSION['clientChanges']['allChanges']) . ';
			$maxFile = ' . count($_SESSION['clientChanges']['allChanges']) . ';

			$message .= "<p>" . sprintf(\'' . $GLOBALS['lang']['installer']['amountFilesCopied'] . '\', $endFile, $maxFile) . "</p>";

			?>' . static::getProceedNextCommandResponsePart($nextUrl, self::getInstallerProgressPercent(), '<?php print $message; ?>') . '<?php
		} else {
			' . static::getErrorMessageResponsePart('', $GLOBALS['lang']['installer']['errorMoveFile']) . '
		}
		?>';

		return updateUtilInstaller::getResponseString($retArray);
	}

}
