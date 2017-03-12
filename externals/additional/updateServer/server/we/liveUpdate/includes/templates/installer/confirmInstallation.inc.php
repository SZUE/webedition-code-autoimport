<?php
/**
 * $Id$
 */
/**
 * This template is shown, before installation starts
 */
$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['installer']['headlineConfirmInstallation'],
	'Header' => '',
	'Content' => '<div class="messageDiv">
	' . $GLOBALS['lang']['installer']['confirmInstallation'] . '
</div>
<div style="float:right;">
	<button type="button" class="weBtn" onclick="top.opener.document.we_form.submit();self.close();">' . $GLOBALS['lang']['button']['ok'] . '</button>
	<button type="button" class="weBtn" onclick="top.opener.top.opener.top.we_cmd(\'make_backup\');">' . $GLOBALS['lang']['button']['backup'] . '</button>
	<button type="button" class="weBtn" onclick="self.close();">' . $GLOBALS['lang']['button']['cancel'] . '</button>
</div>'
];
