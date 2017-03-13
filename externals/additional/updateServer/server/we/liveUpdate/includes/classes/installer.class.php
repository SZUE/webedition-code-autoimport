<?php

/**
 * $Id$
 */
class installer extends installerBase{

	/**
	 * @return array
	 */
	static function getInstallationStepNames(){
		return ['downloadInstaller',
			'getChanges',
			'downloadChanges',
			//'prepareChanges',
			'updateDatabase',
			'copyFiles',
			'executePatches',
			'finishInstallation'
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
		$installationSteps = self::getInstallationStepNames();
		$installationStepsTotal = count($installationSteps);

		// downloads
		$dlSteps = floor(count($_SESSION['clientChanges']['allChanges']) / 100);
		$installationStepsTotal += $dlSteps;
		// queries
		$querySteps = count($_SESSION['clientChanges']['queries']) / EXECUTE_QUERIES_PER_STEP;
		$installationStepsTotal += $querySteps;
		// prepare files
		/* 		$prepareSteps = count($_SESSION['clientChanges']['allChanges']) / PREPARE_FILES_PER_STEP;
		  $installationStepsTotal += $prepareSteps;
		 */
		$prepareSteps = 0;
		$currentStep = 0;

		switch($_REQUEST['detail']){
			default:
				$currentStep = 0;
				break;
			case 'downloadInstaller':
				$currentStep = 1;
				break;
			case 'getChanges':
				$currentStep = 2;
				break;
			case 'downloadChanges':
				$currentStep = 3 + ($_REQUEST['position'] / count($_SESSION['clientChanges']['allChanges'])) * $dlSteps;
				break;
			/* case 'prepareChanges':
			  $currentStep = 4 + $dlSteps + ($_REQUEST['position'] / PREPARE_FILES_PER_STEP);
			  break; */
			case 'updateDatabase':
				$currentStep = 5 + $dlSteps + $prepareSteps + ($_REQUEST['position'] / EXECUTE_QUERIES_PER_STEP);
				break;
			case 'copyFiles':
				$currentStep = $installationStepsTotal - 2;
				break;
			case 'executePatches':
				$currentStep = $installationStepsTotal - 1;
				break;
			case 'finishInstallation':
				return 100;
		}
		return number_format(($currentStep / $installationStepsTotal * 100), 0);
	}

	/**
	 * returns url to the screen to confirm the installation
	 *
	 * @return string
	 */
	static function getConfirmInstallationWindow(){
		return "window.open(\'?" . updateUtil::getCommonHrefParameters('installer', 'confirmInstallation') . "\', \'confirmUpdate" . time() . "\', \'dependent=yes,height=250,width=600,menubar=no,location=no,resizable=no,status=no,toolbar=no,scrollbars=no\')";
	}

	/**
	 * returns next step according to installation step names
	 *
	 * @param mixed $currentStep
	 * @return string
	 */
	static function getNextUpdateDetail($currentStep = false){
		if(!$currentStep){
			$currentStep = $_REQUEST['detail'];
		}
		$steps = static::getInstallationStepNames();
		foreach($steps as $i => $step){
			if($currentStep == $step){
				return $steps[($i + 1)];
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
	static function getCommandNameForDetail($detail){

		$cmd = [
			'update' => ['getChanges', 'finishInstallation'],
			'modules' => ['getChanges', 'finishInstallation'],
			'languages' => ['getChanges', 'finishInstallation'],
			'upgrade' => ['getChanges', 'copyFiles', 'executePatches', 'finishInstallation'],
		];

		if(in_array($detail, $cmd[$_SESSION['update_cmd']])){
			return $_SESSION['update_cmd'];
		}
		return 'installer';
	}

	/**
	 * @return string
	 */
	static function getJsFunctions(){
		return '<script><!--
var decreaseSpeed = 1; // is set false, when script was successful, otherwise decrease speed
var nextUrl = "?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail('downloadInstaller'), 'downloadInstaller') . '";

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
	messageLog.scrollTop = 1000000;
}

function activateLiInstallerStep(stepId) {
	document.getElementById(stepId).className = "activeStep";
}

function finishLiInstallerStep(stepId) {
	document.getElementById(stepId).className = "finishedStep";
}
//-->
</script>';
	}

	/**
	 * returns screen to confirm installation
	 *
	 * @return string
	 */
	static function getConfirmInstallationResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/installer/confirmInstallation.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * @return string
	 */
	static function getInstallationScreenResponse(){
		$GLOBALS['updateServerTemplateData']['installationSteps'] = self::getInstallationStepNames();

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/installer/initialInstallerScreen.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * returns order for client to install the new installer.
	 * rest of installation is done with these files then.
	 *
	 * @return string
	 */
	static function getDownloadInstallerResponse(){

		$files = self::getInstallerFilesArray();

		$nextStep = self::getNextUpdateDetail();
		$nextUrl = self::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail($nextStep), $nextStep);

		$retArray = self::_getDownloadFilesResponse($files, $nextUrl);

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
	static function getDownloadFilesResponse($filesArray, $nextUrl, $progress = 0, $parts = []){
		$endFile = ((empty($_REQUEST['position']) ? 0 : $_REQUEST['position']) + count($filesArray));
		$maxFile = count(empty($_SESSION['clientChanges']['allChanges']) ? [] : $_SESSION['clientChanges']['allChanges']);
		return [
			'Type' => 'SaveFiles',
			'Files' => $filesArray,
			'Next' => self::getProceedNextCommandResponse($nextUrl, $progress),
			'SuccessText' => sprintf($GLOBALS['lang']['installer']['amountFilesDownloaded'], $endFile, $maxFile),
			'Parts' => $parts,
			'Error' => [
				'path' => self::getErrorMessageResponse('', '__PATH__'),
				'write' => self::getErrorMessageResponse('', '__PATH__', 'notWritableError')
			]
		];
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
	static function _getDownloadFilesResponse($filesArray, $nextUrl, $progress = 0){

		// prepare $filesArray (path => encodedContent) for the client
		$writeFilesCode = '
			$files = array(';

		foreach($filesArray as $path => $content){
			$writeFilesCode .= '
				"' . $path . '" => "' . $content . '",';
		}
		$writeFilesCode .= ');';

		return [
			'Type' => 'eval',
			'Code' => '<?php
' . updateUtil::getOverwriteClassesCode() . '

' . $writeFilesCode . '

$success = true; // all files fine
$successFiles = array(); // successfully saved files

foreach ($files as $path => $content) {
	if ($success) {

		$testPath = ltrim($path, "/");
		$testPath = strpos($testPath, "tmp/files") === 0 ? "/". ltrim(str_replace("tmp/files", "", $testPath), "/") : false;

		if(!$liveUpdateFnc->filePutContent( LIVEUPDATE_CLIENT_DOCUMENT_DIR.$path, $liveUpdateFnc->decodeCode($content))) {
			$success = false;
			' . self::getErrorMessageResponsePart('', '$path') . '
		} else if($testPath && method_exists($liveUpdateFnc, "checkMakeFileWritable") && !$liveUpdateFnc->checkMakeFileWritable($testPath)) {
			$success = false;
			' . self::getErrorMessageResponsePart('', '$testPath', 'notWritableError') . '
		} else {
			$successFiles[] = $path;
		}
	}
}

if ($success) {
			$endFile = ' . ((empty($_REQUEST['position']) ? 0 : $_REQUEST['position']) + count($filesArray)) . ';
			$maxFile = ' . count(empty($_SESSION['clientChanges']['allChanges']) ? [] : $_SESSION['clientChanges']['allChanges']) . ';

	$message=sprintf("' . $GLOBALS['lang']['installer']['amountFilesDownloaded'] . '", $endFile, $maxFile) . "<br/>";
	/*foreach ($successFiles as $path) {

				$text = basename($path);
				$text = substr($text, -40);

		$message .= "<div>&hellip;$text</div>";
	}*/

	?>' . self::getProceedNextCommandResponsePart($nextUrl, $progress, '<?php print $message; ?>') . '<?php

}
?>'];
	}

	/**
	 * This response updates the installer screen and triggers next step
	 *
	 * @return string
	 */
	static function getGetChangesResponse($nextUrl = ''){
		$nextUrl = '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail(self::getNextUpdateDetail()), self::getNextUpdateDetail());

		$message = '<div>' . sprintf($GLOBALS['lang']['installer']['downloadFilesTotal'], count($_SESSION['clientChanges']['allChanges'])) . '<br />' .
			count($_SESSION['clientChanges']['files']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesFiles'] . '<br />' .
			count($_SESSION['clientChanges']['queries']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesQueries'] . '<br />' .
			count($_SESSION['clientChanges']['patches']) . ' ' . $GLOBALS['lang']['installer']['downloadFilesPatches'] . '<br /></div>';

		$progress = self::getInstallerProgressPercent();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php
' . updateUtil::getOverwriteClassesCode() . '
$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
$liveUpdateFnc->deleteDir($filesDir);

?>' . self::getProceedNextCommandResponsePart($nextUrl, $progress, $message);

		return updateUtil::getResponseString($retArray);
	}

	/**
	 * returns code for response to update database
	 *
	 * @return string
	 */
	static function getUpdateDatabaseResponse(){
		if(!isset($_REQUEST['position'])){
			$_REQUEST['position'] = 0;
		}

		$repeatUrl = self::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail']) . '&position=' . ($_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP']);
		$nextUrl = self::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail(self::getNextUpdateDetail()), self::getNextUpdateDetail());
		$progress = self::getInstallerProgressPercent();

		return updateUtil::getResponseString([
				'Type' => 'DBUpdate',
				'From' => $_REQUEST['position'],
				'To' => ($_REQUEST['position'] + $_SESSION['EXECUTE_QUERIES_PER_STEP']),
				'ClientVersion' => $_SESSION['clientVersion'],
				'Next' => self::getProceedNextCommandResponse($nextUrl, $progress),
				'Repeat' => self::getProceedNextCommandResponse($repeatUrl, $progress),
				'Lang' => [
					'notice' => $GLOBALS['lang']['installer']['updateDatabaseNotice'],
					'tableExists' => $GLOBALS['luSystemLanguage']['installer']['tableExists'],
					'tableChanged' => $GLOBALS['luSystemLanguage']['installer']['tableChanged'],
					'entryExists' => $GLOBALS['luSystemLanguage']['installer']['entryAlreadyExists'],
					'error' => $GLOBALS['luSystemLanguage']['installer']['errorExecutingQuery']
				]
		]);
	}

	/**
	 * returns response to copy new files to correct location
	 *
	 * @return string
	 */
	static function getCopyFilesResponse(){
		$nextUrl = self::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail(self::getNextUpdateDetail()), self::getNextUpdateDetail());
		return updateUtil::getResponseString([
				'Type' => 'CopyFiles',
				'Message' => sprintf($GLOBALS['lang']['installer']['amountFilesCopied'], count($_SESSION['clientChanges']['files'])),
				'Next' => self::getProceedNextCommandResponse($nextUrl, self::getInstallerProgressPercent()),
				'Error' => self::getErrorMessageResponse()
		]);
	}

	/**
	 * returns response to execute all patches
	 *
	 * @return string
	 */
	static function getExecutePatchesResponse(){

		$nextUrl = self::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail(self::getNextUpdateDetail()), self::getNextUpdateDetail());
		$repeatUrl = self::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail']);

		return updateUtil::getResponseString([
				'Type' => 'ExecutePatches',
				'Next' => self::getProceedNextCommandResponse($nextUrl, self::getInstallerProgressPercent()),
				'Repeat' => self::getProceedNextCommandResponse($repeatUrl, self::getInstallerProgressPercent()),
				'Message' => sprintf($GLOBALS['lang']['installer']['amountPatchesExecuted'], count($_SESSION['clientChanges']['patches'])),
				'Error' => self::getErrorMessageResponse('__HEAD__', '__MSG__'),
				]
		);
	}

	/*	 * ********************************************************
	  /* Needed php Scripts and functions
	  /********************************************************* */


	/*	 * ********************************************************
	  /* Check and Downloading the installer
	  /********************************************************* */

	/**
	 * returns paths to needed installer for download, regarding the version of
	 * client
	 *
	 * @return array
	 */
	static function getInstallerFilesArray(){

		$availableInstallers = [];

		$d = dir(LIVEUPDATE_SERVER_DOWNLOAD_DIR);
		while($entry = $d->read()){
			switch($entry){
				case '.':
				case '..':
					continue;
				default:
					$availableInstallers[str_replace('version', '', $entry)] = $entry;
			}
		}
		$d->close();
		//bug #6305: bei 6.2.7 (ev. auch anderen) wird bein Nachinstallieren von Sprachen nicht $_SESSION['clientTargetVersionNumber'] gesetzt
		//dann findet er auch nicht das downzuloadende Installer-Vereichnis und alles kommt leer an
		$suchInstallerVersion = (!empty($_SESSION['clientTargetVersionNumber']) ?
			$_SESSION['clientTargetVersionNumber'] :
			$_SESSION['clientVersionNumber']
			);

		$installerVersionDir = $availableInstallers[updateUtil::getNearestVersion($availableInstallers, $suchInstallerVersion)];
		$installerDir = LIVEUPDATE_SERVER_DOWNLOAD_DIR . '/' . $installerVersionDir;

		$fileArray = $liveUpdaterFiles = [];
		updateUtilBase::getFilesOfDir($installerDir . '/updateClient', $liveUpdaterFiles);
		foreach($liveUpdaterFiles as $file){
			//filename is evaled, therefore it looks wired
			$fileArray[str_replace($installerDir, "", $file)] = updateUtil::getFileContentEncoded($file);
		}

		return $fileArray;
	}

	/**
	 * returns url for the downloaded installer
	 *
	 * @return string
	 */
	static function getUpdateClientUrl(){
		return dirname($_SESSION['clientUpdateUrl']) . '/updateClient/liveUpdateServer.php';
	}

	static function getProceedNextCommandResponse($nextUrl, $progress){

		$message = '';
		if(!strpos($nextUrl, $_REQUEST['detail'])){
			$NextUpdateDetail = static::getNextUpdateDetail();
			if(key_exists($NextUpdateDetail, $GLOBALS['lang']['installer'])){
				$message .= '<br /><strong>' . $GLOBALS['lang']['installer'][$NextUpdateDetail] . '</strong><br/>';
			}
			$nextStep = [$_REQUEST['detail'], $NextUpdateDetail];
		} else {
			$nextStep = '';
		}

		return [
			'message' => $message,
			'progress' => $progress,
			'nextUrl' => $nextUrl,
			'nextStep' => $nextStep,
		];
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
	static function getProceedNextCommandResponsePart($nextUrl, $progress, $message = ''){
		if(!strpos($nextUrl, $_REQUEST['detail'])){
			$NextUpdateDetail = static::getNextUpdateDetail();
			if(key_exists($NextUpdateDetail, $GLOBALS['lang']['installer'])){
				$message .= '<br /><strong>' . $GLOBALS['lang']['installer'][$NextUpdateDetail] . '</strong><br/>';
			}

			$activateStep = '
			top.frames.updatecontent.finishLiInstallerStep("' . $_REQUEST['detail'] . '");
			top.frames.updatecontent.activateLiInstallerStep("' . $NextUpdateDetail . '");';
		} else {
			$activateStep = '';
		}

		return '<script>
top.frames.updatecontent.decreaseSpeed = false;
top.frames.updatecontent.nextUrl = "' . $nextUrl . '"+(top.frames.updatecontent.param?top.frames.updatecontent.param:"");
top.frames.updatecontent.setProgressBar("' . $progress . '");
top.frames.updatecontent.appendMessageLog("' . str_replace(["\n", "\r"], '', $message) . '\n");
' . $activateStep . '
window.setTimeout("top.frames.updatecontent.proceedUrl();", 20);
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
	static function getErrorMessageResponsePart($headline = '', $message = '', $type = ''){
		return '
$errorMessage = ' . self::getErrorMessage($headline, $message, $type) . ';

$liveUpdateFnc->insertUpdateLogEntry($errorMessage, "' . (isset($_SESSION['clientTargetVersion']) ? $_SESSION['clientTargetVersion'] : $_SESSION['clientVersion']) . '", 1);

print \'
<script>
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
	static function getFinishInstallationResponsePart($message, $jsMessage = '', $progress = 100){

		if(!$jsMessage){
			$jsMessage = strip_tags($message);
		}
		update::updateLogFinish(1);
		return '<script>
top.frames["updatecontent"].setProgressBar("' . $progress . '");
top.frames["updatecontent"].appendMessageLog("' . $message . '\n");
window.open(\'?' . updateUtil::getCommonHrefParameters('installer', 'finishInstallationPopUp') . '\', \'finishInstallationPopUp' . session_id() . '\', \'dependent=yes,height=250,width=600,menubar=no,location=no,resizable=no,status=no,toolbar=no,scrollbars=no\');
//			alert("' . $jsMessage . '");
		</script>';
	}

	static function getFinishInstallationPopUpResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/installer/finishInstallationPopUp.inc.php');
		return updateUtil::getResponseString($ret);
	}

	static function getErrorMessageResponse($headline = '', $message = '', $type = ''){
		switch($type){
			case "notWritableError":
				$headline = $GLOBALS['luSystemLanguage']['installer']['fileNotWritableError'];
		}
		return [
			'headline' => $headline ?: '<br /><strong class="errorText">' . $GLOBALS['luSystemLanguage']['installer'][$_REQUEST['detail'] . 'Error'] . '</strong>',
			'message' => $message . ($message ? '' : '<br/>'),
			'lang' => [
				'errorMessage' => $GLOBALS['luSystemLanguage']['installer']['errorMessage'],
				'errorIn' => $GLOBALS['luSystemLanguage']['installer']['errorIn'],
				'errorLine' => $GLOBALS['luSystemLanguage']['installer']['errorLine']
			]
		];
	}

	/**
	 * returns string for eval Response to generate output for a php error
	 * appends php error, when necessayr
	 *
	 * @param string $headline
	 * @return string
	 */
	static function getErrorMessage($headline = '', $message = '', $type = ''){

		$headline = !$headline ? "<br /><strong class=\'errorText\'>" . $GLOBALS['luSystemLanguage']['installer'][$_REQUEST['detail'] . 'Error'] . '</strong>' : $headline;
		$message .= $message ? '<br />\\\n' : '';

		switch($type){
			case "notWritableError":
				$errorMessage = '"<div class=\'errorDiv\'>" . "' . sprintf($GLOBALS['luSystemLanguage']['installer']['fileNotWritableError'], $message) . '" . "</div>"';
				break;

			default:
				$errorMessage = '"<div class=\'errorDiv\'>"
						. "' . $headline . '<br />"
						. "' . $message . '"
						. ($GLOBALS["liveUpdateError"]["errorString"] ?	"' . $GLOBALS['luSystemLanguage']['installer']['errorMessage'] . ': <code class=\'errorText\'>" . $GLOBALS["liveUpdateError"]["errorString"] . "</code><br />"
						.												"' . $GLOBALS['luSystemLanguage']['installer']['errorIn'] . ': <code class=\'errorText\'>" . $GLOBALS["liveUpdateError"]["errorFile"] . "</code><br />"
						. 												"' . $GLOBALS['luSystemLanguage']['installer']['errorLine'] . ': <code class=\'errorText\'>" . $GLOBALS["liveUpdateError"]["errorLine"] . "</code>"
																	   : "")
						. "</div>"';
		}

		return $errorMessage;
	}

	static function getDownloadChangesResponse(){

		// current position
		if(!isset($_REQUEST['position'])){
			$_REQUEST['position'] = 0;
		}

		$Paths = array_keys($_SESSION['clientChanges']['allChanges']);

		$fileArray = [];
		$Position = $_REQUEST['position'];

		$Content = updateUtil::getFileContent($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);
		$FileSize = strlen($Content);

		// If file is too large to transfer in one request, split it!
		// when first part(s) are transfered do the next part until complete
		// file is transfered

		if((!empty($_REQUEST['part'])) || $FileSize > $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024){

			// Check which part have to be transfered
			$Part = empty($_REQUEST['part']) ? 0 : intval($_REQUEST['part']);

			// get offset and length of the substr from the file
			$Start = ($Part * $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024);
			$Length = ($_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024);

			// filename on the client
//			$Index = $Paths[$Position] . ".part" . $Part;
			// value of the part -> must be base64_encoded
			$Value = substr($Content, $Start, $Length);

			$fileArray[$Paths[$Position] . 'part' . $Part] = $Value;

			if($Start + $Length >= $FileSize){//last step
				if($Position >= count($_SESSION['clientChanges']['allChanges'])){
					$nextUrl = self::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail(self::getNextUpdateDetail()), self::getNextUpdateDetail());

					// :IMPORTANT:
					return updateUtil::getResponseString(self::getDownloadFilesResponse($fileArray, $nextUrl, self::getInstallerProgressPercent(), ['Name' => $Paths[$Position],
								'Count' => $Part]));
				}
				$Position++;
				$nextUrl = self::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail']) . "&position=" . $Position;

				// :IMPORTANT:
				return updateUtil::getResponseString(self::getDownloadFilesResponse($fileArray, $nextUrl, self::getInstallerProgressPercent(), ['Name' => $Paths[$Position - 1],
							'Count' => $Part]));
			}
			$Part += 1;
			$nextUrl = self::getUpdateClientUrl() . '?' . updateUtil::getCommonHrefParameters(self::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail']) . "&part=" . $Part . "&position=" . $Position;

			// :IMPORTANT:
			return updateUtil::getResponseString(self::getDownloadFilesResponse($fileArray, $nextUrl, self::getInstallerProgressPercent()));


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

				$fileArray[$Paths[$Position]] = updateUtil::getFileContent($_SESSION['clientChanges']['allChanges'][$Paths[$Position]]);
				$Position++;
			} else {
				break;
			}
		} while($ResponseSize < $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] * 1024);

		$nextUrl = self::getUpdateClientUrl() . '?' . ($Position >= count($_SESSION['clientChanges']['allChanges']) ?
			updateUtil::getCommonHrefParameters(self::getCommandNameForDetail(self::getNextUpdateDetail()), self::getNextUpdateDetail()) :
			updateUtil::getCommonHrefParameters(self::getCommandNameForDetail($_REQUEST['detail']), $_REQUEST['detail']) . "&position=$Position"
			);


		// :IMPORTANT:
		return updateUtil::getResponseString(self::getDownloadFilesResponse($fileArray, $nextUrl, self::getInstallerProgressPercent()));
	}

}
