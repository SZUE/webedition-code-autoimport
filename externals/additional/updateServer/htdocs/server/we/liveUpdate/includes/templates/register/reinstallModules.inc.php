<?php
/**
 * This template is used, when the registration form of a demo version is
 * requested. This contains mainly a input field for the serial
 */

$reinstallModules = $GLOBALS['updateServerTemplateData']['reinstallModules'];
$existingModules = $GLOBALS['updateServerTemplateData']['existingModules'];

$orderedModules = array();
foreach ($reinstallModules as $moduleKey) {
	$orderedModules[$moduleKey] = $existingModules[$moduleKey]['text'];
}
asort($orderedModules);

$moduleString = '
<ul>';
foreach ($orderedModules as $key => $moduleName) {
	
	$moduleString .= '
	<li>' . $moduleName . '</li>';
}
$moduleString .= '
</ul>';

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Encoding'] = 'true';
$liveUpdateResponse['EncodedCode'] = updateUtil::encodeCode('<?php
$we_button = new we_button();
$nextButton = $we_button->create_button("next", "' . installer::getConfirmInstallationWindow() . '");

$content = \'
<form name="we_form">
' . updateUtil::getCommonFormFields('modules', 'startUpdate') . '
<div class="messageDiv">
' . $GLOBALS['lang']['register']['reInstallModules'] . '
' . $moduleString . '
\' . $nextButton . \'
</div>
</form>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['register']['headline']) . '", $content);
?>
');

?>