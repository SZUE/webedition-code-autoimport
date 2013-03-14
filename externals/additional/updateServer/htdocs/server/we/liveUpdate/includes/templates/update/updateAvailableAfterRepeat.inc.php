<?php
/**
 * This template is shown, when there is an update available. But the SVN-REvision ist not high enough It is possible
 * to start an updaterepeat.
 */

//client version: text and version string
$clientVersionComplete = update::getFormattedVersionStringFromWeVersion(true,false);
$clientVersionText = addslashes($GLOBALS['lang']['update']['installedVersion']) . ':<br />' . $clientVersionComplete . '.<br />';

//maxBranchVersion: text + version string
if (isset($_SESSION['clientVersionBranch']) && $_SESSION['clientVersionBranch']!='' && isset($_SESSION['testUpdate']) &&  $_SESSION['testUpdate']){
	$maxBranchVersionComplete = update::getFormattedVersionString(update::getMaxVersionNumberForBranch($_SESSION['clientVersionBranch']), true, false);
	$maxBranchVersionText = addslashes($GLOBALS['lang']['update']['newestVersionSameBranch']) . ':<br/> '. $maxBranchVersionComplete . '.<br/>';
} else {$maxBranchVersionText='';}

//maxVersion: text + version string
$maxVersionComplete = update::getFormattedVersionString($GLOBALS['updateServerTemplateData']['maxVersionNumber']['version'], true, false);
$maxVersionText = addslashes($GLOBALS['lang']['update']['newestVersion']).':<br/> '. $maxVersionComplete . '.';

//error_log('getUpdateAvailableResponseAfterRepeat2');
//error_log($GLOBALS['updateServerTemplateData']['maxVersionNumber']['version']);
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
	$diskquotawarning = "'.$GLOBALS['lang']['upgrade']['repeatUpdateDiskquotaWarning1'].'".$testdiskquota."'.$GLOBALS['lang']['upgrade']['repeatUpdateDiskquotaWarning2'].'";
} else {
	$diskquotawarning = "'.$GLOBALS['lang']['upgrade']['confirmUpdateDiskquotaWarning0'].'";
}

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "javascript:document.we_form.submit()");

if( defined("PCRE_VERSION") ) {
$pcreV = PCRE_VERSION;
} else {$pcreV="";}


$content = \'
<form name="we_form">
	' . updateUtil::getCommonFormFields('update', 'confirmRepeatUpdate') . '
	<input type="hidden" name="clientTargetVersionNumber" value="' . $_SESSION['clientVersionNumber'] . '" />
	<input type="hidden" name="clientPhpVersion" value="\'.phpversion(). \'" />
	<input type="hidden" name="clientPcreVersion" value="\'.$pcreV. \'" />
	<input type="hidden" name="clientPhpExtensions" value="\'.base64_encode(serialize(get_loaded_extensions())). \'" />
	<input type="hidden" name="clientMySQLVersion" value="\'.getMysqlVer(false). \'" />

	' . addslashes($GLOBALS['lang']['update']['suggestCurrentVersion']) . '<br /><br />' .
	$clientVersionText .
	$maxBranchVersionText .
	$maxVersionText;

if ($_SESSION['clientVersionNumber'] > $GLOBALS['updateServerTemplateData']['maxVersionNumber']['version']){
$liveUpdateResponse['Code'] .= '
<div class="messageDiv">
		' . $GLOBALS['lang']['update']['repeatUpdateNotPossible'] . '

	</div>';
} else {
$liveUpdateResponse['Code'] .= ' <br/>\'.$diskquotawarning.\'<br/>

	<div class="messageDiv">
		' . $GLOBALS['lang']['update']['repeatUpdateNeeded'] . '
		\' . $nextButton . \'
	</div>';

}
$liveUpdateResponse['Code'] .= '
</form>
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['update']['headline']) . '", $content);
?>';

?>