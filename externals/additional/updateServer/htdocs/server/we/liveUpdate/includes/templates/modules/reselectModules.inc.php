<?php

$modules = array();
foreach ($GLOBALS['updateServerTemplateData']['clientDesiredModules'] as $moduleKey) {
	
	$modules[$moduleKey] = $GLOBALS['updateServerTemplateData']['existingModules'][$moduleKey]['text'];
}
asort($modules);

$moduleStr = "<ul>\n<li>" . implode("</li>\n<li>", $modules) . "</li>\n</ul>";

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$back_button = $we_button->create_button("back", "?' . updateUtil::getCommonHrefParameters('modules', 'selectModules') . '");

$content = \'
<div class="errorDiv">
' . $GLOBALS['lang']['modules']['reselectModules'] . '
' . $moduleStr . '
\' . $back_button . \'
</div>
\';


print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['modules']['headline']) . '", $content);
?>';


?>