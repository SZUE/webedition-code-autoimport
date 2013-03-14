<?php
/**
 * This template is shown to confirm an update repeat
 */

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/lib/we/core/autoload.php");
$communityRegistration = new we_net_Community();
$deauthenticated = $communityRegistration->deauthenticate();

if($deauthenticated === true) {	
	$content = \'
	' . $GLOBALS['lang']['community']['deauthenticationSuccess'] . '
	<br />
	<div class="messageDiv">
	' . $GLOBALS['lang']['community']['deauthenticationHint'] . '
	</div>
	\';
} else {
	$content = \'
	' . $GLOBALS['lang']['community']['deauthenticationFailure'] . '
	\';
}

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['community']['headline']) . '", $content);
?>';

?>