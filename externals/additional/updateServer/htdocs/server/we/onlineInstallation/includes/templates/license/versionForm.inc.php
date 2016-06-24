<?php
$Output = '<br /><br />';

$AvailableVersions = $GLOBALS['updateServerTemplateData']['AvailableVersions'];
$MatchingVersions = $GLOBALS['updateServerTemplateData']['MatchingVersions'];
$VersionsMissingLanguage = $GLOBALS['updateServerTemplateData']['VersionsMissingLanguage'];
//$NotLiveVersions = $GLOBALS['updateServerTemplateData']['NotLiveVersions'];
$SubVersions = $GLOBALS['updateServerTemplateData']['SubVersions'];
$VersionNames = $GLOBALS['updateServerTemplateData']['VersionNames'];
$AlphaBetaVersions = $GLOBALS['updateServerTemplateData']['AlphaBetaVersions'];

if(!empty($VersionsMissingLanguage) && !empty($MatchingVersions)){
	$temp0 = $VersionsMissingLanguage;
	$temp1 = $MatchingVersions;
	$Missing = array_shift($temp0);
	$Available = array_shift($temp1);
	if(!is_numeric($Missing)){
		$Missing = 0;
	}
	if($Missing > $Available){
		if(isset($_SESSION['testUpdate']) && empty($NotLiveVersions)){
			$Output .= '### . $this->Language[\'noNotLiveVersion\'] . ###<br /><br />';
			;
		} else {
			$Output .= '### . sprintf($this->Language[\'missingTranslations\'], ###' . $Missing . '###, ###' . $Available . '###) . ###<br /><br />';
		}
	} else {
		if(isset($_SESSION['testUpdate']) && empty($NotLiveVersions)){
			$Output .= '### . $this->Language[\'noNotLiveVersion\'] . ###<br /><br />';
		} else {
			$Output .= '### . $this->Language[\'highestVersionRecommended\'] . ###<br /><br />';
		}
	}
} else if(!empty($MatchingVersions)){
	$Output .= '### . $this->Language[\'highestVersionRecommended\'] . ###<br /><br />';
}
if(!empty($MatchingVersions)){
	$ModMatchingVersions = $MatchingVersions;
	$_SESSION['MatchingVersions'] = $MatchingVersions;
	foreach($ModMatchingVersions as $key => &$value){
		if(isset($_SESSION['testUpdate']) && $_SESSION['testUpdate']){
			$branchText = '|' . $AlphaBetaVersions[$key]['branch'];
		} else {
			$branchText = '';
		}
		$value = ($VersionNames[$key] ? $VersionNames[$key] : $value) . ' (' . $value . ' ' . $GLOBALS['lang']['installer'][$AlphaBetaVersions[$key]['type']] . ($AlphaBetaVersions[$key]['typeversion'] ? ' ' . $AlphaBetaVersions[$key]['typeversion'] : '') . ', SVN-Revision:' . $SubVersions[$key] . $branchText . ')';
	}
	$flippedVers = array_flip($MatchingVersions);

	$VersionSelectBox = 'leSelect::get(
									"le_version",
									unserialize(***' . serialize($ModMatchingVersions) . '***),
									"' . (isset($_SESSION['clientInstalledVersion']) && key_exists($_SESSION['clientInstalledVersion'], $MatchingVersions) ?
			$_SESSION['clientInstalledVersion'] :
			array_shift($flippedVers)
		) . '",
									array(
										"style" => "width:293px",
									)
								)';
	$VersionSelectBox = str_replace(['"', "'"], ["###", "***"], $VersionSelectBox);

	$Output .= '<strong>### . $this->Language[\'version\'] . ###</strong><br />'
		. '### . ' . $VersionSelectBox . ' . ###<br /><br />';
	$ReturnValue = "LE_STEP_NEXT";
} else {
	$ReturnValue = "LE_STEP_FATAL_ERROR";
	$Output .= '### . $this->Language[\'cannotInstallWebEdition\'] . ###';
}


$Output = '"' . str_replace('###', '"', str_replace('***', "'", str_replace('"', '\"', $Output))) . '"';

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = <<<CODE
<?php

\$this->setHeadline(\$this->Language['headline']);
\$this->setContent(\$this->Language['content'] . {$Output});
return {$ReturnValue};

?>
CODE;
