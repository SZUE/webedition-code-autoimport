<?php
/**
 * This template is shown, when something is not available at the moment
 */
$tmp_registerBeforeUpgradeMessage = "";
if (isset($_SESSION["clientWE_LIGHT"]) && $_SESSION["clientWE_LIGHT"]) {
	$tmp_registerBeforeUpgradeMessage = $GLOBALS['lang']['upgrade']['registerBeforeUpgrade_we5light'];
} else if(substr(intval($_SESSION['clientVersionNumber']),0,1) == "4") {
	$tmp_registerBeforeUpgradeMessage = $GLOBALS['lang']['upgrade']['registerBeforeUpgrade_we4'];
} else {
	$tmp_registerBeforeUpgradeMessage = $GLOBALS['lang']['upgrade']['registerBeforeUpgrade'];
}

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "javascript:top.opener.top.we_cmd(\'update\');");

$content = \'
<div class="errorDiv">
	' . $tmp_registerBeforeUpgradeMessage . '
	<br />
	<br />
	\' . $nextButton . \'
</div>
\';
	
print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['upgrade']['headline']) . '", $content);
?>';

?>