<?php

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Encoding'] = 'true';
$liveUpdateResponse['EncodedCode'] = updateUtil::encodeCode('<?php

$errorMessage =	"
<div class=\"errorDiv\">
	<strong>' . $GLOBALS["luSystemLanguage"]["register"]['registrationError'] . '</strong>
	<br />
	' . $GLOBALS["luSystemLanguage"]["installer"]['errorMessage'] . ': <code class=\"errorText\">" . $GLOBALS["liveUpdateError"]["errorString"] . "</code><br />
	' . $GLOBALS["luSystemLanguage"]["installer"]['errorIn'] . ': <code class=\"errorText\">" . $GLOBALS["liveUpdateError"]["errorFile"] . "</code><br />
	' . $GLOBALS["luSystemLanguage"]["installer"]['errorLine'] . ': <code class=\"errorText\">" . $GLOBALS["liveUpdateError"]["errorLine"] . "</code>
</div>";

$liveUpdateFnc = new liveUpdateFunctionsServer();
$liveUpdateFnc->insertUpdateLogEntry($errorMessage, "' . $_SESSION['clientVersion'] . '", 1);

$content = "' . addslashes(addslashes($GLOBALS['lang']['register']['registerError'])) . '
	$errorMessage
";

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['register']['headline']) . '", $content);
?>
');