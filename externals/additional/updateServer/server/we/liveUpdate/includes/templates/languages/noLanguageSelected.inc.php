<?php
/**
 * $Id: noLanguageSelected.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
// build response array
$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['languages']['headline'],
	'Header' => '',
	'Content' => '
<form name="we_form">
' . updateUtilUpdate::getCommonFormFields('languages', 'selectLanguages') . '

' . $GLOBALS['lang']['languages']['noLanguageSelectedText'] . '
<br />
<br />
<button type="button" class="weBtn" onclick="document.we_form.submit();"><i class="fa fa-lg fa-step-backward"></i> ' . $GLOBALS['lang']['button']['back'] . '</button>
</form>'
];

