<?php
/**
 * This template is shown to confirm an update repeat
 */

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", $_SERVER["PHP_SELF"] . "?update_cmd=community&detail=deauthenticateVerified");
$backButton = $we_button->create_button("back", $_SERVER["PHP_SELF"] . "?section=community");


$content = \'
	' . $GLOBALS['lang']['community']['confirmDeauthentication'] . '
	<br />
	<div class="messageDiv">
	' . $GLOBALS['lang']['community']['confirmDeauthenticationInfo'] . '
	</div>
	<table>
	<tr><td width="120px">\' . $backButton . \'</td><td>\' . $nextButton . \'</td></tr>
	</table>
\';
	
print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['community']['headline']) . '", $content);
?>';

?>