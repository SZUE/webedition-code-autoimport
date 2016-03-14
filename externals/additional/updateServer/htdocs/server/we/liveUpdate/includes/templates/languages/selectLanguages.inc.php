<?php

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
for($i = 0; $i < sizeof($newinstallAbleLanguages); $i++){

	$installLanguages .= getCheckBox('lng_' . $newinstallAbleLanguages[$i], $newinstallAbleLanguages[$i], $newinstallAbleLanguages[$i], false);
}

// missingLanguages
$missingStr = '';
if(!empty($missingLanguages)){

	$missingStr = "<ul>";
	for($i = 0; $i < sizeof($missingLanguages); $i++){

		$missingStr .= "<li>" . $missingLanguages[$i] . "</li>";
	}
	$missingStr .= "</ul>";
}

if($missingStr){
	$missingStr = '<div class="messageDiv">' .
		$GLOBALS['lang']['languages']['languagesNotReady'] . $missingStr .
		'</div>';
}

// build response array
$liveUpdateResponse['Type'] = 'eval';
$liveUpdateResponse['Code'] = '<?php

$we_button = new we_button();

$submitButton = $we_button->create_button("next", "javascript:document.we_form.submit();");

$content = \'
<form name="we_form">
' . updateUtil::getCommonFormFields('languages', 'confirmLanguages') . '

' . $GLOBALS['lang']['languages']['installLamguages'] . '

' . $installLanguages . '

</form>
<table class="defaultfont" width="100%">
<tr>
	<td>' . $GLOBALS['lang']['languages']['installLanguages'] . '</td>
	<td>\' . $submitButton . \'</td>
</tr>
</table>

' . $missingStr . '
\';

print liveUpdateTemplates::getHtml("' . addslashes($GLOBALS['lang']['languages']['headline']) . '", $content);
?>';

