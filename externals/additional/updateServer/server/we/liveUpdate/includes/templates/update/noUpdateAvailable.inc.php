<?php
/**
 * $Id$
 */
/**
 * This template is shown, when there is no update available. It is possible
 * to start an updaterepeat.
 */
//what's this?
$branchAvailText = (isset($GLOBALS['updateServerTemplateData']['maxVersionNumber']['branch']) && isset($_SESSION['testUpdate']) && $_SESSION['testUpdate'] ?
	'|' . $GLOBALS['updateServerTemplateData']['maxVersionNumber']['branch'] : '');


//client version: text and version string
$clientVersionComplete = update::getFormattedVersionStringFromWeVersion(true, false);
$clientVersionText = addslashes($GLOBALS['lang']['update']['installedVersion']) . ':<br />' . $clientVersionComplete . '.<br />';

//newest version in branch: text and version string
if(isset($_SESSION['clientVersionBranch']) && $_SESSION['clientVersionBranch'] != '' && isset($_SESSION['testUpdate']) && $_SESSION['testUpdate']){
	$maxBranchVersion = update::getMaxVersionNumberForBranch($_SESSION['clientVersionBranch']);
	$maxBranchVersionComplete = update::getFormattedVersionString($maxBranchVersion, true, true);
	$maxBranchVersionText = addslashes($GLOBALS['lang']['update']['newestVersionSameBranch']) . ':<br />' . $maxBranchVersionComplete . '.<br/>';
} else {
	$maxBranchVersionText = '';
}

//neweset version: text and versions string
$maxVersionComplete = update::getFormattedVersionString($GLOBALS['updateServerTemplateData']['maxVersionNumber']['version']);
$maxVersionText = addslashes($GLOBALS['lang']['update']['newestVersion']) . ':<br/>' . $maxVersionComplete . '.<br/>';

$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['update']['headline'],
	'Header' => '',
	'Content' => '
<form name="we_form">
	' . updateUtil::getCommonFormFields('update', 'confirmRepeatUpdate') . '
	<input type="hidden" name="clientTargetVersionNumber" value="' . $_SESSION['clientVersionNumber'] . '" />
	' . addslashes($GLOBALS['lang']['update']['noUpdateNeeded']) . '<br /><br />' .
	$clientVersionText .
	$maxBranchVersionText .
	$maxVersionText . '<br />
<div class="messageDiv">' .
	(updateUtilBase::version2number($_SESSION['clientVersionNumber']) > $GLOBALS['updateServerTemplateData']['maxVersionNumber']['version'] ?
	$GLOBALS['lang']['update']['repeatUpdateNotPossible'] :
	$GLOBALS['lang']['update']['repeatUpdatePossible'] . '<button type="button" class="weBtn" onclick="document.we_form.submit();">' . $GLOBALS['lang']['button']['next'] . ' <i class="fa fa-lg fa-step-forward"></i></button>') . '
</div></form>'
];
