<?php

// get names of the modules
$installModules = array();
foreach ($GLOBALS['updateServerTemplateData']['clientDesiredModules'] as $moduleKey) {
	$installModules[$moduleKey] = $GLOBALS['updateServerTemplateData']['existingModules'][$moduleKey]['text'];
}
asort($installModules);


$modulesStr = '<ul>
';

foreach ($installModules as $module) {
	$modulesStr .= "<li>$module</li>\n";
}
$modulesStr .= '</ul>';

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "' . installer::getConfirmInstallationWindow() . '");

$content = \'
<form name="we_form">
' . updateUtil::getCommonFormFields('modules', 'startUpdate') . '
' . $GLOBALS['lang']['modules']['textConfirmModules'] . '
<br />' . $modulesStr . '
\' . $nextButton . \'
</form>\';


print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['modules']['headline']) . '", $content);
?>';


?>