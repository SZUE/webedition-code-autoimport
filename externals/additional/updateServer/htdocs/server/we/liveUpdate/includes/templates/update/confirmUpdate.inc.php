<?php
/**
 * This template is shown, when there is no update available. It is possible
 * to start an updaterepeat.
 */

// target mit versname versehen

$source = str_replace(".","",$_SESSION['clientVersion']);
$target = str_replace(".","",$_SESSION['clientTargetVersion']);

$clientVersionComplete = update::getFormattedVersionStringFromWeVersion(true,false);
$clientTargetVersionComplete = update::getFormattedVersionString($target, true, false);

$ReqOK = update::checkRequirements($ReqOut,$_SESSION['clientPcreVersion'],$_SESSION['clientPhpExtensions'],$_SESSION['clientPhpVersion'],$_SESSION['clientMySQLVersion']);
//$ReqOK = update::checkRequirements($ReqOut,'','','','','');
if ($source < 5100 && $target >= 5100) {
	$meldung = $GLOBALS['lang']['update']['we51Notification'];
} else {
	$meldung = "";
}
if ($source<6300 && $target>= 6300){
	$weiterwarnung = '<div class="messageDiv"  style="color:#ff0000;">' . $GLOBALS['lang']['update']['confirmUpdateWarning6300'].'</div>';
} else {
	$weiterwarnung = '<div class="messageDiv">' . $GLOBALS['lang']['update']['confirmUpdateVersionDetails']. $meldung . '</div>';
}
$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "' . installer::getConfirmInstallationWindow() . '");


$ReqOK = '.$ReqOK.';
if (!$ReqOK) {$nextButton = "";}

$content = \'
<form name="we_form">
	' . updateUtil::getCommonFormFields('update', 'startUpdate') . '
	' . sprintf($GLOBALS['lang']['update']['confirmUpdateText'], $clientVersionComplete, $clientTargetVersionComplete) . $ReqOut.'
	<br />
	<br />
	\' .  $nextButton  . \'
	</form>' . $weiterwarnung . '<div style="margin-top:20px;">'.$GLOBALS['lang']['update']['spenden'].'<form target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                  <input type="hidden" name="cmd" value="_s-xclick">
                  <input type="hidden" name="hosted_button_id" value="BERPPPT588RAE">
                  <input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
                  <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
                </form</div>
\';
	
print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['update']['headline']) . '", $content);
?>';

?>