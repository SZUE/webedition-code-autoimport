<?php
/**
 * $Id$
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

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$refreshButton = $we_button->create_button("image:btn_function_reload", "javascript:proceedUrl();");

' . updateUtil::getOverwriteClassesCode() . '

$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage'][$_SESSION['update_cmd']]['start'] . '", "' . (isset($_SESSION['clientTargetVersion']) ? $_SESSION['clientTargetVersion'] : $_SESSION['clientVersion']) . '", 0);

$content = \'
<table border="0" class="defaultfont" cellpadding="0" cellspacing="0">
<tr>
	<td id="tdInstallerSteps" valign="top" rowspan="2">
		<ul id="ulInstallerSteps">
			' . $stepList . '
		</ul>
	</td>
	<td valign="top" id="tdMessageLog" colspan="2">
		<div id="messageLog">
			<strong>' . $GLOBALS['lang']['installer'][$firstStep] . '</strong>
		</div>
	</td>
</tr>
<tr>
	<td id="tdProgressBar">
		' . progressBar::getProgressBarHtml() . '
	</td>
	<td align="right" id="tdRefreshButton">\' . $refreshButton . \'</td>
</tr>
</table>
<script>
	activateLiInstallerStep("' . $firstStep . '");
</script>
<script>
	proceedUrl();
</script>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['installer']['headline']) . '", $content, "' . addslashes(progressBar::getProgressBarJs() . installer::getJsFunctions() ) . '", "", 550, 500);

?>';
