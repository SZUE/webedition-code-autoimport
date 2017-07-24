<?php
/**
 * $Id: initialInstallerScreen.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
/**
 * This template is shown, when any form of installation should start.
 */
// get steps
$stepList = '';
$firstStep = '';
foreach($GLOBALS['updateServerTemplateData']['installationSteps'] as $installationStep){

	if(!$firstStep){
		$firstStep = $installationStep;
	}
	$stepList .= '
	<li class="upcomingStep" id="' . $installationStep . '">' . $GLOBALS['lang']['installer'][$installationStep] . '</li>';
}

$liveUpdateResponse = [
	'Type' => 'template',
	'headline' => $GLOBALS['lang']['installer']['headline'],
	'Header' => progressBarUpdate::getProgressBarJs() . installerUpdate::getJsFunctions(),
	'Content' => '
<table border="0" class="defaultfont" cellpadding="0" cellspacing="0">
<tr>
	<td id="tdInstallerSteps" valign="top" rowspan="2">
		<ul id="ulInstallerSteps">' . $stepList . '</ul>
	</td>
	<td valign="top" id="tdMessageLog" colspan="2">
		<div id="messageLog"><strong>' . $GLOBALS['lang']['installer'][$firstStep] . '</strong></div>
	</td>
</tr>
<tr>
	<td id="tdProgressBar">' . progressBarUpdate::getProgressBarHtml() . '</td>
	<td align="right" id="tdRefreshButton"><button type="button" class="weBtn" onclick="proceedUrl();"><i class="fa fa-lg fa-refresh"></i> ' . $GLOBALS['lang']['button']['refresh'] . '</button></td>
</tr>
</table>
<script>
	activateLiInstallerStep("' . $firstStep . '");
	proceedUrl();
</script>'
];
