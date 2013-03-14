<?php
/**
 * This template is shown, when there is no update available. It is possible
 * to start an updaterepeat.
 */

$selectList = '';

if(version_compare(phpversion(), "4.3.0") == -1) {
	$maxVersionNumber = "5099";
}
$shownversions = array();
foreach ($GLOBALS['updateServerTemplateData']['possibleVersions'] as $number => $version) {
	if(isset($maxVersionNumber) && $number > $maxVersionNumber) {} else {
		$selectList .= '<option value="' . $number . '">' . $version . '</option>';
		$shownversions[] = $number;
	}
}
$confirmUpdateHint='';
foreach ($shownversions as $number) {
	$confirmUpdateHint .= $GLOBALS['lang']['upgrade']['confirmUpdateHint'][$number];
}

//client version: text and version string
$clientVersionComplete = update::getFormattedVersionStringFromWeVersion(true, false);
$clientVersionText = addslashes($GLOBALS['lang']['update']['installedVersion']) . ':<br />' . $clientVersionComplete . '.<br />';

//maxBranchVersion: text + version string
if (isset($_SESSION['clientVersionBranch']) && isset($_SESSION['testUpdate']) &&  $_SESSION['testUpdate']){
	$maxBranchVersionComplete = update::getFormattedVersionString(update::getMaxVersionNumberForBranch($_SESSION['clientVersionBranch']), true, false);
	$maxBranchVersionText = addslashes($GLOBALS['lang']['update']['newestVersionSameBranch']) . ':<br/> '. $maxBranchVersionComplete . '.<br/>';
} else {$maxBranchVersionText='';}

if (isset($_SESSION['testUpdate']) && $_SESSION['testUpdate']){
	$selectsize=count($GLOBALS['updateServerTemplateData']['possibleVersions']);
} else {
	$selectsize=1;
}

$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

function checkfreequota(&$AnzMB){
	$kB= str_repeat("0",1024);
	$MB= str_repeat($kB,1024);
	$removedir = false;

	if(!is_dir(LIVEUPDATE_CLIENT_DOCUMENT_DIR . "tmp")){
		mkdir(LIVEUPDATE_CLIENT_DOCUMENT_DIR . "tmp");
		$removedir = true;
	}
	$testfilename = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "tmp/testquota.txt";

	if (file_exists($testfilename)){
		unlink($testfilename);
	}
	$allesOK=true;
	for ($i=1;$i<=$AnzMB;$i++){
		if( !file_put_contents ( $testfilename ,$MB, FILE_APPEND) ) {
			$allesOK=false;
			break;
		}
	}
	$AnzMB=$i-1;
	unlink($testfilename);
	if($removedir){
		rmdir(LIVEUPDATE_CLIENT_DOCUMENT_DIR . "tmp");
	}
	return $allesOK;
}

$testdiskquota = 100;
if (!checkfreequota($testdiskquota)){
	$diskquotawarning = "'.$GLOBALS['lang']['upgrade']['confirmUpdateDiskquotaWarning1'].'".$testdiskquota."'.$GLOBALS['lang']['upgrade']['confirmUpdateDiskquotaWarning2'].'";
} else {
	$diskquotawarning = "'.$GLOBALS['lang']['upgrade']['confirmUpdateDiskquotaWarning0'].'";
}
$we_button = new we_button();
$nextButton = $we_button->create_button("next", "javascript:document.we_form.submit()", true, "100", "22","", "", false);
$repeatUpdateButton = $we_button->create_button("next", "?' . updateUtil::getCommonHrefParameters('update', 'confirmRepeatUpdate') . '&clientTargetVersionNumber=' . $_SESSION['clientVersionNumber'] . '");

$confirmCheckbox = we_forms::checkboxWithHidden(false, "confirmUpgrade", "'.$GLOBALS['lang']['upgrade']['confirmUpdateWarningCheckbox'].'", false, "defaultfont","toggleNextButton();");

if( defined("PCRE_VERSION") ) {
$pcreV = PCRE_VERSION;
} else {$pcreV="";}


$content = \'
<script type="text/javascript">
function toggleNextButton() {
	if (document.getElementById("clientTargetVersionNumber").selectedIndex==-1){
		alert("'.$GLOBALS['lang']['upgrade']['pleaseSelectVersion'].'");
	}
	if(document.getElementById("nextButton").style.display == "") {
		document.getElementById("nextButton").style.display = "none";
	} else {
		document.getElementById("nextButton").style.display = "";
	}

}
</script>
<form name="we_form">

	' . updateUtil::getCommonFormFields('update', 'confirmUpdate') . '

	' . $GLOBALS['lang']['update']['updateAvailableText'] . '<br /><br />' .
	$clientVersionText .
	$maxBranchVersionText . 
	'<br />
	' . $GLOBALS['lang']['update']['updatetoVersion'] . '<br/>
	<select  id="clientTargetVersionNumber" name="clientTargetVersionNumber" size="'.$selectsize.'" >
		' . $selectList . '
	</select>';
	if(!isset($_SESSION['clientPhpExtensions'])){
		$liveUpdateResponse['Code'] .='
		<input type="hidden" name="clientPhpVersion" value="\'.phpversion(). \'" />
		<input type="hidden" name="clientPcreVersion" value="\'.$pcreV. \'" />
		<input type="hidden" name="clientPhpExtensions" value="\'.base64_encode(serialize(get_loaded_extensions())). \'" />
		<input type="hidden" name="clientMySQLVersion" value="\'.getMysqlVer(false). \'" />';

	}

	$liveUpdateResponse['Code'] .='<br />\'.$diskquotawarning. \'
	<div class="messageDiv">

		' . $GLOBALS['lang']['upgrade']['confirmUpdateWarning'] . $confirmUpdateHint . $GLOBALS['lang']['upgrade']['confirmUpdateWarningEnd'] . '
	</div><b>'.$GLOBALS['lang']['upgrade']['confirmUpdateWarningTitle'].'</b><br />
	\' . $confirmCheckbox . \' <br /><div id="nextButton" style="display:none;"> \' . $nextButton . \'<br /></div>

	' . $GLOBALS['lang']['update']['suggestCurrentVersion'] . '

	<br />
	<div class="messageDiv">
		' . addslashes($GLOBALS['lang']['update']['repeatUpdatePossible']) . '
		\' . $repeatUpdateButton . \'
	</div>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['update']['headline']) . '", $content);
?>';

?>