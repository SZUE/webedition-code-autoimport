<?php
/**
 * $Id: confirmLanguages.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
$desiredLanguagesStr = '';

foreach($_SESSION['clientDesiredLanguages'] as $lng){
	$desiredLanguagesStr .= "	<li>$lng</li>";
}
$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['languages']['headline'],
	'Header' => '',
	'Content' => '
<form name="we_form">
' . updateUtilUpdate::getCommonFormFields('languages', 'startUpdate') . '
' . $GLOBALS['lang']['languages']['confirmInstallation'] . '
	<ul>
' . $desiredLanguagesStr . '
	</ul>
<button type="button" class="weBtn" onclick="document.we_form.submit();">' . $GLOBALS['lang']['button']['next'] . ' <i class="fa fa-lg fa-step-forward"></i></button>
</form>'
];
