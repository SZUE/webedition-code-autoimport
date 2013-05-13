<?php
/**
 * This template is shown to confirm an update repeat
 */

$input_uid = '<input class="wetextinput" size="24" type="text" name="clientUID" id="clientUID" value="\'.(isset($_REQUEST["clientUID"]) ? $_REQUEST["clientUID"] : "").\'" />';
$input_password = '<input class="wetextinput" size="24" type="password" name="clientPassword" id="clientPassword" />';

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$backButton = $we_button->create_button("back", $_SERVER["PHP_SELF"] . "?section=community");
$nextButton = $we_button->create_button("next", "javascript:document.we_form.submit();");

$content = \'
<form name="we_form">
	<div class="defaultfont">
		' . $GLOBALS['lang']['community']['authentication'] . '
		' . updateUtil::getCommonFormFields('community', 'authenticate') . '
	</div>
	<br />
	<div class="messageDiv">
	' . $GLOBALS['lang']['community']['authenticationHint'] . '
	</div>
	<br />
	<table class="defaultfont">
		<tr>
			<td width="120px">' . $GLOBALS['lang']['community']['uid'] . '</td><td>'.$input_uid.'</td>
		</tr>
		<tr>
			<td width="120px">' . $GLOBALS['lang']['community']['password'] . '</td><td>'.$input_password.'</td>
		</tr>
		<tr>
			<td colspan="2"><br />
				<input type="hidden" name="update_cmd" value="community" />
				<input type="hidden" name="detail" value="reauthenticate" />
				<div style="float:left; width:120px;">\' . $backButton . \'</div>
				<div style="float:left;">\' . $nextButton . \'</div>
			</td>
		</tr>
	</table>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['community']['headline']) . '", $content);
?>';

?>