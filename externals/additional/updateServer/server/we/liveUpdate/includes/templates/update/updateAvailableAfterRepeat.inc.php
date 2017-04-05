<?php
/**
 * $Id: updateAvailableAfterRepeat.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
/**
 * This template is shown, when there is an update available. But the SVN-REvision ist not high enough It is possible
 * to start an updaterepeat.
 */
//client version: text and version string
$clientVersionComplete = updateUpdate::getFormattedVersionStringFromWeVersion(true, false);
$clientVersionText = addslashes($GLOBALS['lang']['update']['installedVersion']) . ':<br />' . $clientVersionComplete . '.<br />';

//maxBranchVersion: text + version string
if(isset($_SESSION['clientVersionBranch']) && $_SESSION['clientVersionBranch'] != '' && isset($_SESSION['testUpdate']) && $_SESSION['testUpdate']){
	$maxBranchVersionComplete = updateUpdate::getFormattedVersionString(updateUpdate::getMaxVersionNumberForBranch($_SESSION['clientVersionBranch']), true, false);
	$maxBranchVersionText = addslashes($GLOBALS['lang']['update']['newestVersionSameBranch']) . ':<br/> ' . $maxBranchVersionComplete . '.<br/>';
} else {
	$maxBranchVersionText = '';
}

//maxVersion: text + version string
$maxVersionComplete = updateUpdate::getFormattedVersionString($GLOBALS['updateServerTemplateData']['maxVersionNumber']['version'], true, false);
$maxVersionText = addslashes($GLOBALS['lang']['update']['newestVersion']) . ':<br/> ' . $maxVersionComplete . '.';

//error_log('getUpdateAvailableResponseAfterRepeat2');
//error_log($GLOBALS['updateServerTemplateData']['maxVersionNumber']['version']);
$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

if( defined("PCRE_VERSION") ) {
$pcreV = PCRE_VERSION;
} else {$pcreV="";}


$content = \'
<form name="we_form">
	' . updateUtilUpdate::getCommonFormFields('update', 'confirmRepeatUpdate') . '
	<input type="hidden" name="clientTargetVersionNumber" value="' . $_SESSION['clientVersionNumber'] . '" />
	<input type="hidden" name="clientPhpVersion" value="\'.phpversion(). \'" />
	<input type="hidden" name="clientPcreVersion" value="\'.$pcreV. \'" />
	<input type="hidden" name="clientPhpExtensions" value="\'.base64_encode(serialize(get_loaded_extensions())). \'" />
	<input type="hidden" name="clientMySQLVersion" value="\'.getMysqlVer(false). \'" />

	' . addslashes($GLOBALS['lang']['update']['suggestCurrentVersion']) . '<br /><br />' .
	$clientVersionText .
	$maxBranchVersionText .
	$maxVersionText;

if($_SESSION['clientVersionNumber'] > $GLOBALS['updateServerTemplateData']['maxVersionNumber']['version']){
	$liveUpdateResponse['Code'] .= '
<div class="messageDiv">
		' . $GLOBALS['lang']['update']['repeatUpdateNotPossible'] . '

	</div>';
} else {
	$liveUpdateResponse['Code'] .= ' <br/>

	<div class="messageDiv">
		' . $GLOBALS['lang']['update']['repeatUpdateNeeded'] . '
	<button type="button" class="weBtn" onclick="document.we_form.submit();">' . $GLOBALS['lang']['button']['next'] . ' <i class="fa fa-lg fa-step-forward"></i></button>
	</div>';
}
$liveUpdateResponse['Code'] .= '
</form>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['update']['headline']) . '", $content);
?>';

