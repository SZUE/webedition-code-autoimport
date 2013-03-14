<?php
/**
 * This template is shown, before installation starts
 */

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$okButton = $we_button->create_button("ok", "javascript:top.opener.document.we_form.submit();self.close();");
$backupButton = $we_button->create_button("backup", "javascript:top.opener.top.opener.top.we_cmd(\'make_backup\');");
$cancelButton = $we_button->create_button("cancel", "javascript:self.close();");

$content = \'
<div class="messageDiv">
	' . $GLOBALS['lang']['installer']['confirmInstallation'] . '
</div>
\' . $we_button->position_yes_no_cancel($okButton, $backupButton, $cancelButton) . \'
\';

print liveUpdateTemplates::getHtml("' . $GLOBALS['lang']['installer']['headlineConfirmInstallation'] . '", $content);

?>';

?>