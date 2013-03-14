<?php
/**
 * This template is shown, when something is not available at the moment
 */

// old code:
$versions = $GLOBALS['updateServerTemplateData']['possibleVersions'];

$versionList = '<select class="wetextinput" name="clientTargetVersionNumber">';
foreach ($versions as $key => $value) {
	if($key<6007){
		$versionList .= '<option value="' . $key . '">' . $value . '</option>';
	}
}
$versionList .= '</select>';

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "' . installer::getConfirmInstallationWindow() . '", true, "100", "22","", "", false);

$confirmCheckbox = we_forms::checkboxWithHidden(false, "confirmUpgrade", "'.$GLOBALS['lang']['upgrade']['confirmUpgradeWarningCheckbox'].'", false, "defaultfont","toggleNextButton();");

if( defined("PCRE_VERSION") ) {
$pcreV = PCRE_VERSION;
} else {$pcreV="";}


$content = \'
<script type="text/javascript">
function toggleNextButton() {
	if(document.getElementById("nextButton").style.display == "") {
		document.getElementById("nextButton").style.display = "none";
	} else {
		document.getElementById("nextButton").style.display = "";
	}
}
</script>
<form name="we_form">
	' . $GLOBALS['lang']['upgrade']['upgradePossibleText'] . '
	<br />
	<br />
	' . $GLOBALS['lang']['upgrade']['upgradeToVersion'] . '
	
	' . $versionList . '
	<div class="messageDiv">
		' . updateUtil::getCommonFormFields('upgrade', 'startUpgrade') . '
		' . $GLOBALS['lang']['upgrade']['confirmUpgradeWarning'] . '
	</div><b>'.$GLOBALS['lang']['upgrade']['confirmUpgradeWarningTitle'].'</b><br />
	\' . $confirmCheckbox . \' <br /><div id="nextButton" style="display:none;"> \' . $nextButton . \'</div>';
	if(!isset($_SESSION['clientPhpExtensions'])){
		$liveUpdateResponse['Code'] .='
		<input type="hidden" name="clientPhpVersion" value="\'.phpversion(). \'" />
		<input type="hidden" name="clientPcreVersion" value="\'.$pcreV. \'" />
		<input type="hidden" name="clientPhpExtensions" value="\'.base64_encode(serialize(get_loaded_extensions())). \'" />
		<input type="hidden" name="clientMySQLVersion" value="\'.getMysqlVer(false). \'" />';	
	}
	$liveUpdateResponse['Code'] .='

</form>
\';
	
print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['upgrade']['headline']) . '", $content);
?>';

?><?php
/**
 * This template is shown, when something is not available at the moment
 */
/*
$versions = $GLOBALS['updateServerTemplateData']['possibleVersions'];

$versionList = '<select class="wetextinput" name="clientTargetVersionNumber">';
foreach ($versions as $key => $value) {
	
	$versionList .= '<option value="' . $key . '">' . $value . '</option>';
}
$versionList .= '</select>';

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "' . installer::getConfirmInstallationWindow() . '");

$content = \'
<form name="we_form">
	' . $GLOBALS['lang']['upgrade']['upgradePossibleText'] . '
	<br />
	<br />
	' . $GLOBALS['lang']['upgrade']['upgradeToVersion'] . '
	
	' . $versionList . '
	<div class="messageDiv">
		' . updateUtil::getCommonFormFields('upgrade', 'startUpgrade') . '
		' . $GLOBALS['lang']['upgrade']['confirmUpgradeWarning'] . '
	</div>
	\' . $nextButton . \'
</form>
\';
	
print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['upgrade']['headline']) . '", $content);
?>';
*/
?>