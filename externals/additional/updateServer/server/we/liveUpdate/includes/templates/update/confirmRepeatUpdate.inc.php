<?php
/**
 * $Id: confirmRepeatUpdate.inc.php 13564 2017-03-13 17:13:40Z mokraemer $
 */
/**
 * This template is shown to confirm an update repeat
 */
$ReqOK = updateUpdate::checkRequirements($ReqOut, $_SESSION['clientPcreVersion'], $_SESSION['clientPhpExtensions'], $_SESSION['clientPhpVersion'], $_SESSION['clientMySQLVersion']);

$clientVersionComplete = updateUpdate::getFormattedVersionStringFromWeVersion(true, false);

$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['update']['headline'],
	'Header' => '',
	'Content' => '
<form name="we_form">
	' . updateUtilUpdate::getCommonFormFields('update', 'startRepeatUpdate') . '
	' . sprintf($GLOBALS['lang']['update']['confirmRepeatUpdateText'], $clientVersionComplete) . $ReqOut . '
	<br />
	<div class="messageDiv">
	' . $GLOBALS['lang']['update']['confirmRepeatUpdateMessage'] . '
	</div>
' . ($ReqOK ? '<button type="button" class="weBtn" onclick="' . installerUpdate::getConfirmInstallationWindow() . '">' . $GLOBALS['lang']['button']['next'] . ' <i class="fa fa-lg fa-step-forward"></i></button>' : '') . '
</form><div style="margin-top:20px;">' . $GLOBALS['lang']['update']['spenden'] . '
<form target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="BERPPPT588RAE">
	<input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
	<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form</div>'
];
