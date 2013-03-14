<?php
/**
 * This template is used, when the registration form of a demo version is
 * requested. This contains mainly a input field for the serial
 */

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '
<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "javascript:document.we_form.submit();");

$content = \'
<form name="we_form">
	<div class="errorDiv">
		' . $GLOBALS['lang']['register']['repeatRegistration'] . '.
		' . updateUtil::getCommonFormFields('register', 'repeatRegistration') . '
		<br />
		<br />
		<table cellpadding="0" cellspacing="0" class="defaultfont">
		<tr>
			<td><label for="clientSerial">' . $GLOBALS['lang']['register']['serial'] . ':</label></td>
			<td width="10px"></td>
			<td> <input class="wetextinput" size="24" type="text" name="clientSerial" id="clientSerial" /></td>
			<td width="10px"></td>
			<td> \' . $nextButton . \'</td>
		</tr>
	
		</table>
	</div>
	
</form>
\'
;

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['register']['headline']) . '", $content);
?>';

?>