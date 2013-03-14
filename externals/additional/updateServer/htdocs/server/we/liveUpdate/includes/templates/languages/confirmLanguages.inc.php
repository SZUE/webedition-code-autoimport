<?php

$desiredLanguagesStr = "<ul>";

foreach ($_SESSION['clientDesiredLanguages'] as $lng) {
	$desiredLanguagesStr .= "	<li>$lng</li>";
}
$desiredLanguagesStr .= "</ul>";

// build response array
$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();

$submitButton = $we_button->create_button("next", "javascript:document.we_form.submit();");

$content = \'
<form name="we_form">
' . updateUtil::getCommonFormFields('languages', 'startUpdate') . '
' . $GLOBALS['lang']['languages']['confirmInstallation'] . '
' . $desiredLanguagesStr . '
</form>
\' . $submitButton . \'
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['languages']['headline']) . '", $content);
?>';

?>