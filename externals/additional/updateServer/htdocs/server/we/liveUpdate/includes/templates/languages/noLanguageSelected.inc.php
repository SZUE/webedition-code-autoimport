<?php

// build response array
$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();

$backButton = $we_button->create_button("back", "javascript:document.we_form.submit();");

$content = \'
<form name="we_form">
' . updateUtil::getCommonFormFields('languages', 'selectLanguages') . '

' . $GLOBALS['lang']['languages']['noLanguageSelectedText'] . '
<br />
<br />
\' . $backButton . \'
</form>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['languages']['headline']) . '", $content);
?>';

?>