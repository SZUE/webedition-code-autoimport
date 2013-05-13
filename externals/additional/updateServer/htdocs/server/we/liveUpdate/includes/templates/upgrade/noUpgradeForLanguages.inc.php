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

$liveUpdateResponse['Type'] = 'template';
$liveUpdateResponse['headline'] = $GLOBALS['lang']['upgrade']['headline'];
$liveUpdateResponse['Content'] = '
<div class="errorDiv">
	' . $GLOBALS['lang']['upgrade']['noUpgradeForLanguages'] . '
	' . $preventLngsList . '
</div>
';

?>