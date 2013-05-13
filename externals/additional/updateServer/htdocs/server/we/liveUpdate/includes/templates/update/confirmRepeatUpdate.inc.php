<?php
/**
 * This template is shown to confirm an update repeat
 */

$ReqOK = update::checkRequirements($ReqOut,$_SESSION['clientPcreVersion'],$_SESSION['clientPhpExtensions'],$_SESSION['clientPhpVersion'],$_SESSION['clientMySQLVersion']);

$clientVersionComplete = update::getFormattedVersionStringFromWeVersion(true,false);

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
	$diskquotawarning = "";
}

$we_button = new we_button();
$nextButton = $we_button->create_button("next", "' . installer::getConfirmInstallationWindow() . '");

$ReqOK = '.$ReqOK.';
if (!$ReqOK) {$nextButton = "";}

$content = \'
<form name="we_form">
	' . updateUtil::getCommonFormFields('update', 'startRepeatUpdate') . '
	' . sprintf($GLOBALS['lang']['update']['confirmRepeatUpdateText'], $clientVersionComplete) . $ReqOut.'
	<br />
	<div class="messageDiv">
	' . $GLOBALS['lang']['update']['confirmRepeatUpdateMessage'] . '
	</div>\'.$diskquotawarning.\'
	\' . $nextButton . \'</form><div style="margin-top:20px;">'.$GLOBALS['lang']['update']['spenden'].'<form target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                  <input type="hidden" name="cmd" value="_s-xclick">
                  <input type="hidden" name="hosted_button_id" value="BERPPPT588RAE">
                  <input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
                  <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
                </form</div>
\';
	
print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['update']['headline']) . '", $content);
?>';

?>