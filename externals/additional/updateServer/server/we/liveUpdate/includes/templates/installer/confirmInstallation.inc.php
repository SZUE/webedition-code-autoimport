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
	<button type="button" class="weBtn" onclick="top.opener.document.we_form.submit();self.close();"><i class="fa fa-lg fa-check fa-ok"></i> ' . $GLOBALS['lang']['button']['ok'] . '</button>
	<button type="button" class="weBtn" onclick="top.opener.top.opener.top.we_cmd(\'make_backup\');"><i class="fa fa-lg fa-save"></i> ' . $GLOBALS['lang']['button']['backup'] . '</button>
	<button type="button" class="weBtn" onclick="self.close();"><i class="fa fa-lg fa-ban fa-cancel"></i> ' . $GLOBALS['lang']['button']['cancel'] . '</button>
</div>'
];