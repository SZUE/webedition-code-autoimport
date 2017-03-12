<?php
/**
 * $Id$
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
	// FIXME: eliminate [beta] or re-implement beta in db
	$betas = defined('LANGUAGES_BETA') ? explode(',', LANGUAGES_BETA) : array();
	$beta = in_array($text, $betas) ? true : $beta;

	if(!is_array($_SESSION['clientInstalledLanguages'])){
		$_SESSION['clientInstalledLanguages'] = unserialize(urldecode(base64_decode($_SESSION['clientInstalledLanguages'])));
	}

	if(in_array($value, $_SESSION['clientInstalledLanguages'])){

		$text = "<i>$text</i>";
	}
	if($beta == true){
		$text = "$text <font color='red'>[beta]</font>";
		//error_log("found beta language ".$text.".");
	}
	return '\' . we_forms::checkbox("' . $value . '", false, "' . $name . '", "' . $text . '", false, "defaultfont") . \'';
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
' . updateUtil::getCommonFormFields('languages', 'confirmLanguages') . '

' . $GLOBALS['lang']['languages']['installLamguages'] . '<br/>

' . $installLanguages . '

</form>
<table class="defaultfont" width="100%">
<tr>
	<td>' . $GLOBALS['lang']['languages']['installLanguages'] . '</td>
	<td><button type="button" class="weBtn" onclick="document.we_form.submit();">' . $GLOBALS['lang']['button']['next'] . '</button></td>
</tr>
</table>
' . ($missingStr ? '<div class="messageDiv">' .
	$GLOBALS['lang']['languages']['languagesNotReady'] . '<ul>' . $missingStr .
	'</ul></div>' : '')
];
