<?php
/**
 * This file is the template for the update screen, if there are updates
 * available which cannot be installed - due to installed langugages
 * 
 * This file can use the following variables
 * - possibleVersions -> array with versions matching to the system
 * - availableVersions -> array with the maximal versions foreach existing
 *   language
 * - maxVersionNumber
 */

$installedLngList = '';
	foreach ($_SESSION['clientInstalledLanguages'] as $lng) {
		
		$installedLngList .= "\t<li>$lng</li>\n";
	}


$preventLngsList = '';
	foreach ($updateServerTemplateData['availableVersions'] as $lng => $version) {
	
		if ( updateUtil::version2number($version) == $_SESSION['clientVersionNumber'] ) {
			$preventLngsList .= "
		<li>$lng (-> $version)</li>";
		}
	}
	foreach ($_SESSION['clientInstalledLanguages'] as $lng) {
			
		if (!isset($updateServerTemplateData['availableVersions'][$lng])) {
			$preventLngsList .= "
	<li>$lng</li>";
		}
	}
//$preventLngsList .='<li>'.print_r($_SESSION['clientInstalledLanguages'],1).'</li>';
$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "javascript:document.we_form.submit()");

$content = \'
<form name="we_form">
	' . updateUtil::getCommonFormFields('update', 'confirmRepeatUpdate') . '
	<input type="hidden" name="clientTargetVersionNumber" value="' . $_SESSION['clientVersionNumber'] . '" />
<div class="errorDiv">
	' . addslashes(sprintf($GLOBALS['lang']['update']['noUpdateForLanguagesText'], $_SESSION['clientVersion'])) . '
	<br />
	<br />
	' . addslashes($GLOBALS['lang']['update']['installedLanguages']) . '
	<ul>
	' . $installedLngList . '
	</ul>
	' . addslashes($GLOBALS['lang']['update']['updatePreventingLanguages']) . '
	<ul>
	' . $preventLngsList . '
	</ul>
</div>
<div class="messageDiv">
	' . addslashes($GLOBALS['lang']['update']['repeatUpdatePossible']) . '
	\' . $nextButton . \'
</div>
</form>
\';
	
print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['update']['headline']) . '", $content);
?>';

?>