<?php
/**
 * $Id: updateAvailable.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */

/**
 * This template is shown, when there is no update available. It is possible
 * to start an updaterepeat.
 */
$selectList = '';

if(version_compare($_SESSION['clientPhpVersion'], "4.3.0") == -1){
	$maxVersionNumber = "5099";
} elseif(version_compare($_SESSION['clientPhpVersion'], "5.6.0") == -1){
	$maxVersionNumber = "7071";
}
$shownversions = [];
foreach($GLOBALS['updateServerTemplateData']['possibleVersions'] as $number => $version){
	if(isset($maxVersionNumber) && $number > $maxVersionNumber){

	} else {
		$selectList .= '<option value="' . $number . '">' . $version . '</option>';
		$shownversions[] = $number;
	}
}
$confirmUpdateHint = '';
foreach($shownversions as $number){
	$confirmUpdateHint .= empty($GLOBALS['lang']['upgrade']['confirmUpdateHint'][$number]) ? '' : $GLOBALS['lang']['upgrade']['confirmUpdateHint'][$number];
}

//client version: text and version string
$clientVersionComplete = updateUpdate::getFormattedVersionStringFromWeVersion(true, false);
$clientVersionText = addslashes($GLOBALS['lang']['update']['installedVersion']) . ':<br />' . $clientVersionComplete . '.<br />';

//maxBranchVersion: text + version string
if(isset($_SESSION['clientVersionBranch']) && isset($_SESSION['testUpdate']) && $_SESSION['testUpdate']){
	$maxBranchVersionComplete = updateUpdate::getFormattedVersionString(updateUpdate::getMaxVersionNumberForBranch($_SESSION['clientVersionBranch']), true, false);
	$maxBranchVersionText = addslashes($GLOBALS['lang']['update']['newestVersionSameBranch']) . ':<br/> ' . $maxBranchVersionComplete . '.<br/>';
} else {
	$maxBranchVersionText = '';
}

if(isset($_SESSION['testUpdate']) && $_SESSION['testUpdate']){
	$selectsize = count($GLOBALS['updateServerTemplateData']['possibleVersions']);
} else {
	$selectsize = 1;
}

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$pcreV = (defined("PCRE_VERSION")?PCRE_VERSION:"");

$content = \'
<script>
function toggleNextButton() {
	if (document.getElementById("clientTargetVersionNumber").selectedIndex==-1){
		alert("' . $GLOBALS['lang']['upgrade']['pleaseSelectVersion'] . '");
	}
	if(document.getElementById("nextButton").style.display == "") {
		document.getElementById("nextButton").style.display = "none";
	} else {
		document.getElementById("nextButton").style.display = "";
	}

}
function checkSelectIsMainDevel(elem){
	if(elem.selectedIndex!==-1 && elem.options[elem.selectedIndex].text.search(/main-develop/i)>0 ){
	if(!confirm("' . $GLOBALS['lang']['update']['confirmMainDevel'] . '")){
	elem.selectedIndex=-1;
	}
	}
}

</script>
<form name="we_form">
	' . updateUtilUpdate::getCommonFormFields('update', 'confirmUpdate') .
	$GLOBALS['lang']['update']['updateAvailableText'] . '<br /><br />' .
		$clientVersionText .
		$maxBranchVersionText .
		'<br />
	' . $GLOBALS['lang']['update']['updatetoVersion'] . '<br/>
	<select id="clientTargetVersionNumber" name="clientTargetVersionNumber" size="' . $selectsize . '" onchange="checkSelectIsMainDevel(this);">
		' . $selectList . '
	</select>';
if(!isset($_SESSION['clientPhpExtensions'])){
	$liveUpdateResponse['Code'] .= '
		<input type="hidden" name="clientPhpVersion" value="\'.phpversion(). \'" />
		<input type="hidden" name="clientPcreVersion" value="\'.$pcreV. \'" />
		<input type="hidden" name="clientPhpExtensions" value="\'.base64_encode(serialize(get_loaded_extensions())). \'" />
		<input type="hidden" name="clientMySQLVersion" value="\'.getMysqlVer(false). \'" />';
}

$liveUpdateResponse['Code'] .= '<br />
	<div class="messageDiv">
		' . $GLOBALS['lang']['upgrade']['confirmUpdateWarning'] . $confirmUpdateHint . $GLOBALS['lang']['upgrade']['confirmUpdateWarningEnd'] . '
	</div><b>' . $GLOBALS['lang']['upgrade']['confirmUpdateWarningTitle'] . '</b><br />
	<input type="checkbox" id="confirmUpgrade" name="confirmUpgrade" value="1" onclick="toggleNextButton();"/><label for="confirmUpgrade">' . $GLOBALS['lang']['upgrade']['confirmUpgradeWarningCheckbox'] . '</label> <br /><div id="nextButton" style="display:none;"><button type="button" class="weBtn" onclick="document.we_form.submit();">' . $GLOBALS['lang']['button']['next'] . ' <i class="fa fa-lg fa-step-forward"></i></button><br /></div>
	' . $GLOBALS['lang']['update']['suggestCurrentVersion'] . '
	<br />
	<div class="messageDiv">
		' . addslashes($GLOBALS['lang']['update']['repeatUpdatePossible']) . '
		<button type="button" class="weBtn" onclick="document.location=\\\'?' . updateUtilUpdate::getCommonHrefParameters('update', 'confirmRepeatUpdate') . '&clientTargetVersionNumber=' . $_SESSION['clientVersionNumber'] . '\\\'">' . $GLOBALS['lang']['button']['next'] . ' <i class="fa fa-lg fa-step-forward"></i></button>
	</div>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['update']['headline']) . '", $content);
?>';

