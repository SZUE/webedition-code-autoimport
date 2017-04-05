<?php
/**
 * $Id: finishUpgradePopUp.inc.php 13552 2017-03-13 01:44:53Z mokraemer $
 */
$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang'][$_SESSION['update_cmd']]['headline'],
	'Header' => '<script>
	window.onunload = function () {
		top.opener.top.opener.top.we_cmd("dologout");
	}
</script>',
	'Content' => '
<div class="messageDiv">
	' . $GLOBALS['lang'][$_SESSION['update_cmd']]['finished'] . '
	<br />
	<br />
	' . $GLOBALS['lang']['upgrade']['finished_note'] . '
</div>
<button type="button" class="weBtn" onclick="top.opener.top.opener.top.we_cmd(\'dologout\');self.close();"><i class="fa fa-lg fa-check fa-ok"></i> ' . $GLOBALS['lang']['button']['ok'] . '</button>'
];

