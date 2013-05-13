<?php
/**
 * This template is used, when the registration form of a demo version is
 * requested. This contains mainly a input field for the serial
 */

$newsletterForm = register::getNewsletterHtml();

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '
<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "javascript:submitRegistration();");

$content = \'
<form name="we_form">
	<div class="defaultfont">
		' . addslashes($GLOBALS['lang']['register']['errorWithSerial']) . '
		<div class="errorDiv">
			' . $GLOBALS['updateServerTemplateData']['licenceError'] . '
		</div>

		' . addslashes($GLOBALS['lang']['register']['insertSerial']) . '<br />
		' . updateUtil::getCommonFormFields('register', 'register') . '

		<table cellpadding="0" cellspacing="0" class="defaultfont">
		<tr>
			<td><label for="clientSerial">' . $GLOBALS['lang']['register']['serial'] . ':</label></td>
			<td width="10px"></td>
			<td> <input class="wetextinput" size="24" type="text" name="clientSerial" id="clientSerial" value="' . addslashes($GLOBALS['clientRequestVars']['clientSerial']) . '" /></td>
			<td width="10px"></td>
			<td> \' . $nextButton . \'</td>
		</tr>
		</table>
		' . $newsletterForm . '
	</div>
</form>
\'
;

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['register']['headline']) . '", $content);
?>';

?>