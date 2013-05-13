<?php

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$okButton = $we_button->create_button("ok", "javascript:top.opener.top.opener.top.we_cmd(\'dologout\');self.close();");

$header = \'<script type="text/javascript">
	
	window.onunload = function () {
		top.opener.top.opener.top.we_cmd("dologout");
	}
	
</script>\';

$content = \'
<div class="messageDiv">
	' . $GLOBALS['lang'][$_SESSION['update_cmd']]['finished'] . '
	<br />
	<br />
	' . $GLOBALS['lang']['installer']['finished'] . '
</div>
\' . $okButton . \'
\';

print liveUpdateTemplates::getHtml("' . $GLOBALS['lang'][$_SESSION['update_cmd']]['headline'] . '", $content, $header);

?>';

?>