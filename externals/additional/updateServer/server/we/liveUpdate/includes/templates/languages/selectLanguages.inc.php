<?php
/**
 * $Id: selectLanguages.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */

/**
 * returns html checkbox for webEdition
 *
 * @param sring $name
 * @param string $value
 * @param string $text
 * @return string
 */
function getCheckBox($name, $value, $text, $beta = false){
	if(!is_array($_SESSION['clientInstalledLanguages'])){
		$_SESSION['clientInstalledLanguages'] = unserialize(urldecode(base64_decode($_SESSION['clientInstalledLanguages'])));
	}

	return '<input type="checkbox" id="' . $name . '" name="' . $name . '" value="' . $value . '"/><label for="' . $name . '">' .
		(in_array($value, $_SESSION['clientInstalledLanguages']) ? '<i>' . $text . '</i>' : $text) .
		'</label>';
}

$missingLanguages = $GLOBALS['updateServerTemplateData']['missingLanguages'];

// selectBoxes for languages
$installAbleLanguages = $GLOBALS['updateServerTemplateData']['installAbleLanguages'];

if($_SESSION['clientVersionNumber'] >= LANGUAGELIMIT){
	foreach($installAbleLanguages as $lk => $lv){
		if(strpos($lv, '_UTF-8') === false){
			$newinstallAbleLanguages[] = $lv;
		}
	}
} else {
	$newinstallAbleLanguages = $installAbleLanguages;
}
$installLanguages = '';
for($i = 0; $i < count($newinstallAbleLanguages); $i++){
	$installLanguages .= '<div>' . getCheckBox('lng_' . $newinstallAbleLanguages[$i], $newinstallAbleLanguages[$i], $newinstallAbleLanguages[$i], false) . '</div>';
}

// missingLanguages
$missingStr = '';
if(!empty($missingLanguages)){
	foreach($missingLanguages as $cur){
		$missingStr .= "<li>" . $cur . "</li>";
	}
}

$liveUpdateResponse = [
	'Type' => 'template',
	'Headline' => $GLOBALS['lang']['languages']['headline'],
	'Header' => '',
	'Content' => '
<form name="we_form">
' . updateUtilUpdate::getCommonFormFields('languages', 'confirmLanguages') . '

' . $GLOBALS['lang']['languages']['installLamguages'] . '<br/>

' . $installLanguages . '

</form>
<table class="defaultfont" width="100%">
<tr>
	<td>' . $GLOBALS['lang']['languages']['installLanguages'] . '</td>
	<td><button type="button" class="weBtn" onclick="document.we_form.submit();">' . $GLOBALS['lang']['button']['next'] . ' <i class="fa fa-lg fa-step-forward"></i></button></td>
</tr>
</table>
' . ($missingStr ? '<div class="messageDiv">' .
	$GLOBALS['lang']['languages']['languagesNotReady'] . '<ul>' . $missingStr .
	'</ul></div>' : '')
];
