<?php

class modules extends modulesBase{

	

	/**
	 * Response to finish installation, deletes not needed files and updates
	 * installed_modules file
	 *
	 * @return string
	 */
	function getFinishInstallationResponse(){

		$existingModules = modules::getExistingModules();

		$modules_path = "LIVEUPDATE_SOFTWARE_DIR . '/webEdition/we/include/we_installed_modules.inc" . $_SESSION['clientExtension'] . "'";
		// end of installed modules
		// if usermodule is installed, create new user
		$message = '<ul>';
		for($i = 0; $i < sizeof($_SESSION['clientDesiredModules']); $i++){
			$message .= "<li>" . $existingModules[$_SESSION['clientDesiredModules'][$i]]['text'] . "</li>";
		}
		$message .= '</ul>';

		$newContent = modules::getCodeForInstalledModules();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

' . updateUtil::getOverwriteClassesCode() . '

$modulesPath = ' . $modules_path . ';
$modulesContent = "' . updateUtil::encodeCode($newContent) . '";

$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp";
$liveUpdateFnc->deleteDir($filesDir);

if ($liveUpdateFnc->filePutContent($modulesPath, $liveUpdateFnc->decodeCode($modulesContent))) {

	$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage']['modules']['finished'] . $message . '", "' . $_SESSION['clientVersion'] . '", 0);

	?>' . installer::getFinishInstallationResponsePart("<div>" . $GLOBALS['lang']['modules']['finished'] . "\\n" . $message . "</div>") . '<?php

} else {
	' . installer::getErrorMessageResponsePart() . '
}
?>';

		return updateUtil::getResponseString($retArray);
	}

}
