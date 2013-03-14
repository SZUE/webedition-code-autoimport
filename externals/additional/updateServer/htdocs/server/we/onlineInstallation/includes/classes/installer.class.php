<?php

class installer extends installerBase {

	var $LanguageIndex = "installer";


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

		$appendMessageLogJs = "appendText";

		if ( !strpos($nextUrl, $_REQUEST['detail']) ) {
			$NextUpdateDetail = $this->getNextUpdateDetail();
			if(key_exists($NextUpdateDetail, $GLOBALS['lang'][$this->LanguageIndex])) {
				$message .= "<h1>" . $GLOBALS['lang'][$this->LanguageIndex][$NextUpdateDetail] . "</h1>";

			}

		}

		if ( strpos($nextUrl, "leStep=" . $_REQUEST["nextLeStep"]) ) {
			$activateStep = '
				top.leStatus.update("leStatus", "' . ($_REQUEST["nextLeWizard"]) . '", "' . ($_REQUEST["nextLeStep"]) . '");
			';

		}

		if ( $this->getUpdateDetailPosition() === 0) {
			$appendMessageLogJs = "replaceText";

		}

		return '<script type="text/JavaScript">
			top.decreaseSpeed = false;
			top.nextUrl = "' . $nextUrl . '";
			top.leProgressBar.set("leProgress", "' . $progress . '");
			top.leContent.' . $appendMessageLogJs . '("' . $message . '\n");
			' . $activateStep . '
			window.setTimeout("top.leForm.proceedUrl();", 500);
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
				top.leContent.appendErrorText("\' . $errorMessage . \'");
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
			$message = "<ul>";
			foreach ($successFiles as $path) {
				$text = basename($path);
				$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;
				$message .= "<li>$text</li>";

			}
			$endFile = ' . ($_REQUEST['position'] + sizeof($filesArray)) . ';
			$maxFile = ' . sizeof($_SESSION['clientChanges']['allChanges']) . ';

			$message	.=	"</ul>"
						.	"<p>" . sprintf("' . $GLOBALS['lang']['installer']['amountFilesDownloaded'] . '", $endFile, $maxFile) . "</p>";
			?>' . $this->getProceedNextCommandResponsePart($nextUrl, $progress, '<?php print $message; ?>') . '<?php

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
			$message = "<ul>";
			foreach ($successFiles as $path) {
				$text = basename($path);
				$text = (strlen($text) > 40) ? substr($text, (strlen($text) -40)) : $text;
				$message .= "<li>$text</li>";

			}
			$endFile = ' . ($_REQUEST['position'] + sizeof($filesArray)) . ';
			$maxFile = ' . sizeof($_SESSION['clientChanges']['allChanges']) . ';

			$message	.=	"</ul>"
						.	"<p>" . sprintf("' . $GLOBALS['lang']['installer']['amountFilesDownloaded'] . '", $endFile, $maxFile) . "</p>";
			?>' . $this->getProceedNextCommandResponsePart($nextUrl, $progress, '<?php print $message; ?>') . '<?php

		} else {
			' . $this->getErrorMessageResponsePart() . '

		}

?>';

		return $retArray;

	}

}

?>