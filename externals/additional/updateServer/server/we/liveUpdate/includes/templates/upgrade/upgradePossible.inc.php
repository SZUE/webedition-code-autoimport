<?php
/**
 * $Id$
 */
/**
 * This template is shown, when something is not available at the moment
 */
// old code:
$versions = $GLOBALS['updateServerTemplateData']['possibleVersions'];

$versionList = '<select class="wetextinput" name="clientTargetVersionNumber">';
foreach($versions as $key => $value){
	if($key < 6007){
		$versionList .= '<option value="' . $key . '">' . $value . '</option>';
	}
}
$versionList .= '</select>';

$liveUpdateResponse = [
	'Type' => 'eval',
	'Code' => '<?php

if( defined("PCRE_VERSION") ) {
$pcreV = PCRE_VERSION;
} else {$pcreV="";}


$content = \'
<script>
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
		' . updateUtilUpdate::getCommonFormFields('upgrade', 'startUpgrade') . '
		' . $GLOBALS['lang']['upgrade']['confirmUpgradeWarning'] . '
	</div><b>' . $GLOBALS['lang']['upgrade']['confirmUpgradeWarningTitle'] . '</b><br />
	<input type="checkbox" id="confirmUpgrade" name="confirmUpgrade" value="1" onclick="toggleNextButton();"/><label for="confirmUpgrade">' . $GLOBALS['lang']['upgrade']['confirmUpgradeWarningCheckbox'] . '</label> <br /><div id="nextButton" style="display:none;"><button type="button" class="weBtn" onclick="' . installerUpdate::getConfirmInstallationWindow() . '"><i class="fa fa-lg fa-step-forward"></i>' . $GLOBALS['lang']['button']['next'] . '</button></div>' .
	(!isset($_SESSION['clientPhpExtensions']) ? '
		<input type="hidden" name="clientPhpVersion" value="\'.phpversion(). \'" />
		<input type="hidden" name="clientPcreVersion" value="\'.$pcreV. \'" />
		<input type="hidden" name="clientPhpExtensions" value="\'.base64_encode(serialize(get_loaded_extensions())). \'" />
		<input type="hidden" name="clientMySQLVersion" value="\'.getMysqlVer(false). \'" />' : ''
	) . '

</form>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['upgrade']['headline']) . '", $content);
?>'];
