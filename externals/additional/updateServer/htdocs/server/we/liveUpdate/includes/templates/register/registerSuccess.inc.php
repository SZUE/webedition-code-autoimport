<?php
/**
 * This template is used, when the registration form of a demo version is
 * requested. This contains mainly a input field for the serial
 */

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Encoding'] = 'true';
$liveUpdateResponse['EncodedCode'] = updateUtil::encodeCode('<?php
$we_button = new we_button();
$okButton = $we_button->create_button("ok", "javascript:top.opener.top.we_cmd(\'logout\')");

$content = \'
<form name="we_form">
	<div class="contentBlock">
	' . addslashes($GLOBALS['lang']['register']['registerSuccess']) . '
	<br />
	\' . $okButton . \'
	</div>
</form>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['register']['headline']) . '", $content);
?>
');

?>