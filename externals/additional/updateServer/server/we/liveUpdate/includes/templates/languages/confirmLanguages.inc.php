<?php
/**
 * $Id$
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
' . updateUtil::getCommonFormFields('languages', 'startUpdate') . '
' . $GLOBALS['lang']['languages']['confirmInstallation'] . '
	<ul>
' . $desiredLanguagesStr . '
	</ul>
<button type="button" class="weBtn" onclick="document.we_form.submit();">' . $GLOBALS['lang']['button']['next'] . '</button>
</form>'
];
