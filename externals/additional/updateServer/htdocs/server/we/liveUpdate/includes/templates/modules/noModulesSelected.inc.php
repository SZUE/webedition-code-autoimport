<?php

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$back_button = $we_button->create_button("back", "?' . updateUtil::getCommonHrefParameters('modules', 'selectModules') . '");

$content = \'
' . $GLOBALS['lang']['modules']['noModulesSelected'] . '
<br />
<br />
\' . $back_button . \'
\';


print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['modules']['headline']) . '", $content);
?>';


?>