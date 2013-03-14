<?php
/**
 * This template is shown, when there is no update available. It is possible
 * to start an updaterepeat.
 */

//what's this?
if(isset($GLOBALS['updateServerTemplateData']['maxVersionNumber']['branch']) && isset($_SESSION['testUpdate']) &&  $_SESSION['testUpdate']) {
	$branchAvailText= '|'.$GLOBALS['updateServerTemplateData']['maxVersionNumber']['branch'];
} else {$branchAvailText='';}

//client version: text and version string
$clientVersionComplete = update::getFormattedVersionStringFromWeVersion(true,false);
$clientVersionText = addslashes($GLOBALS['lang']['update']['installedVersion']) . ':<br />' . $clientVersionComplete . '.<br />';

//newest version in branch: text and version string
if (isset($_SESSION['clientVersionBranch']) && $_SESSION['clientVersionBranch']!='' && isset($_SESSION['testUpdate']) &&  $_SESSION['testUpdate']){
	$maxBranchVersion = update::getMaxVersionNumberForBranch($_SESSION['clientVersionBranch']);
	$maxBranchVersionComplete = update::getFormattedVersionString($maxBranchVersion, true, true);
	$maxBranchVersionText = addslashes($GLOBALS['lang']['update']['newestVersionSameBranch']) . ':<br />' . $maxBranchVersionComplete . '.<br/>';
} else {$maxBranchVersionText='';}

//neweset version: text and versions string
$maxVersionComplete = update::getFormattedVersionString($GLOBALS['updateServerTemplateData']['maxVersionNumber']['version']);
$maxVersionText = addslashes($GLOBALS['lang']['update']['newestVersion']) . ':<br/>'. $maxVersionComplete . '.<br/>';

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "javascript:document.we_form.submit()");


$content = \'
<form name="we_form">
	' . updateUtil::getCommonFormFields('update', 'confirmRepeatUpdate') . '
	<input type="hidden" name="clientTargetVersionNumber" value="' . $_SESSION['clientVersionNumber'] . '" />
	' . addslashes($GLOBALS['lang']['update']['noUpdateNeeded']) . '<br /><br />' .
	$clientVersionText .
	$maxBranchVersionText .
	$maxVersionText . '<br />';
if (updateUtilBase::version2number($_SESSION['clientVersionNumber']) > $GLOBALS['updateServerTemplateData']['maxVersionNumber']['version']){
$liveUpdateResponse['Code'] .= '
<div class="messageDiv">
		' . $GLOBALS['lang']['update']['repeatUpdateNotPossible'] . '
		
	</div>';
} else {
$liveUpdateResponse['Code'] .= '

	<div class="messageDiv">
		' . $GLOBALS['lang']['update']['repeatUpdatePossible'] . '
		\' . $nextButton . \'
	</div>';

}	
$liveUpdateResponse['Code'] .= '	
</form>
\';
	
print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['update']['headline']) . '", $content);
?>';

?>