<?php
/**
 * This template is shown to confirm an update repeat
 */

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

//error_log(print_r($_REQUEST,true));

$we_button = new we_button();
//$retryButton = $we_button->create_button("back", $_SERVER[\'PHP_SELF\'] . "?update_cmd=community&detail=reauthenticateForm");
$retryButton = $we_button->create_button("back", "javascript:document.we_form.submit();");

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/lib/we/core/autoload.php");
$communityRegistration = new we_net_Community();
if(isset($_REQUEST["clientUID"])) {
	$communityRegistration->uid = $_REQUEST["clientUID"];
}
if(isset($_REQUEST["clientPassword"])) {
	$communityRegistration->password = $_REQUEST["clientPassword"];
}
$authenticated = $communityRegistration->authenticate();

if($authenticated === true) {	
	$content = \'
	' . $GLOBALS['lang']['community']['authenticationSuccess'] . '
	<br />
	<div class="messageDiv">
	' . $GLOBALS['lang']['community']['authenticationHint'] . '
	</div>
	\';
} else {
	$content = \'
	<form name="we_form">
	' . updateUtil::getCommonFormFields('community', 'authenticateForm') . '
	' . $GLOBALS['lang']['community']['authenticationFailure'] . '
	<input type="hidden" name="clientUID" value="\'.$communityRegistration->uid.\'" />
	<div class="messageDiv">
	' . $GLOBALS['lang']['community']['noSuchAccount'] . '
	</div><br />
	\'.$retryButton.\'
	</form>\';
}

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['community']['headline']) . '", $content);
?>';

?>