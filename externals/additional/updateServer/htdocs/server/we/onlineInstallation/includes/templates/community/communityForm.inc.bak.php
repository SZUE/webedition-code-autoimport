<?php
/*
 * checks existing community registration (e-mail address and password)
 * returns true if there is such a community member and logs current installation to database
 * returns false if the member is not registered yet and does not create an installation log entry
 */
$Output =	'<table class="leContentTable">';
$_choiceNotYetOnClickJs =   "document.getElementById(***le_communityChoice_notYet_Form***).style.display = ******;"
							."document.getElementById(***le_communityChoice_already_Form***).style.display = ***none***;";
if(!isset($_SESSION["le_community"]) || $_SESSION["le_community"] == "notYet") {
	$_choiceNotYetSelected = true;
	$_choiceNotYetRadioButtonDisable = " ";
} else {
	$_choiceNotYetSelected = false;
	$_choiceNotYetRadioButtonDisable = "display:none";
}
$_choiceNotYetRadio = 'leCheckbox::get(
						"le_communityChoice",
						"notYet",
						array(
							"onClick"	=> "' . str_replace("\n", "", str_replace("\r\n", "\n", $_choiceNotYetOnClickJs)) . '",
							"id"		=> "le_communityChoice_notYet",
						),
						"",
						"' . $_choiceNotYetSelected . '",
						"radio"
					)';

$_choiceNotYetButton = $GLOBALS["lang"]["community"]["button"]["enterData"];
//$_choiceNotYetButton = 'leButton::get("le_communityChoice_notYet_button", "' . $GLOBALS["lang"]["community"]["button"]["enterData"] . '", "", "100", "22", "", false, true)';
//$_choiceNotYetButton = 'leButton::get("openWebEdition", $this->Language[\'openWebEdition\'], "javascript:top.location.replace(\'/webEdition/index.php\');", 150, 22, "", false, false);';
$_choiceNotYetButton = str_replace('"', "###", $_choiceNotYetButton);
$_choiceNotYetButton = str_replace("'", "***", $_choiceNotYetButton);
$_choiceNotYetRadio = str_replace('"', "###", $_choiceNotYetRadio);
$_choiceNotYetRadio = str_replace("'", "***", $_choiceNotYetRadio);
$Output	.=	'<tr>'
		.	'<td class="defaultfont" width="10%">### . ' . $_choiceNotYetRadio . ' . ###</td>'
		.	'<td class="defaultfont" width="90%"><label for="le_communityChoice_notYet">' . $GLOBALS["lang"]["community"]["choice"]["notRegisteredYet"] . '</label></td>'
		.	'</tr>';
$Output	.=	'<tr id="le_communityChoice_notYet_Form" style="' . $_choiceNotYetRadioButtonDisable . ';">'
		.	'<td class="defaultfont" width="10%"></td>'
		.	'<td class="defaultfont" width="90%">'.$_choiceNotYetButton.'</td>'
		.	'</tr>';

$_choiceAlreadyOnClickJs =   "document.getElementById(***le_communityChoice_notYet_Form***).style.display = ***none***;"
							."document.getElementById(***le_communityChoice_already_Form***).style.display = ******;";
if(isset($_SESSION["le_community"]) && $_SESSION["le_community"] == "Already") {
	$_choiceAlreadySelected = true;
	$_choiceAlreadyRadioButtonDisable = "";
} else {
	$_choiceAlreadySelected = false;
	$_choiceAlreadyRadioButtonDisable = "display:none;";
}
$_choiceAlreadyRadio = 'leCheckbox::get(
						"le_communityChoice",
						"Already",
						array(
							"onClick"	=> "' . str_replace("\n", "", str_replace("\r\n", "\n", $_choiceAlreadyOnClickJs)) . '",
							"id"		=> "le_communityChoice_Already",
						),
						"",
						"' . $_choiceAlreadySelected . '",
						"radio"
					)';

$_choiceAlreadyRadio = str_replace('"', "###", $_choiceAlreadyRadio);
$_choiceAlreadyRadio = str_replace("'", "***", $_choiceAlreadyRadio);
$Output	.=	'<tr>'
		.	'<td class="defaultfont" width="10%">### . ' . $_choiceAlreadyRadio . ' . ###</td>'
		.	'<td class="defaultfont" width="90%"><label for="le_communityChoice_Already">' . $GLOBALS["lang"]["community"]["choice"]["alreadyRegistered"] . '</label></td>'
		.	'</tr>';
$Output	.=	'<tr id="le_communityChoice_already_Form" style="'.$_choiceAlreadyRadioButtonDisable.'">'
		.	'<td class="defaultfont" width="10%"></td>'
		.	'<td class="defaultfont" width="90%">FORM</td>'
		.	'</tr>';

$_choiceSkipOnClickJs =   "document.getElementById(***le_communityChoice_notYet_Form***).style.display = ***none***;"
							."document.getElementById(***le_communityChoice_already_Form***).style.display = ***none***;";

if(isset($_SESSION["le_community"]) && $_SESSION["le_community"] == "skip") {
	$_choiceSkipSelected = true;
} else {
	$_choiceSkipSelected = false;
}
$_choiceSkipRadio = 'leCheckbox::get(
						"le_communityChoice",
						"notYet",
						array(
							"onClick"	=> "' . str_replace("\n", "", str_replace("\r\n", "\n", $_choiceSkipOnClickJs)) . '",
							"id"		=> "le_defaultLanguage_skip",
						),
						"",
						"' . $_choiceSkipSelected . '",
						"radio"
					)';

$_choiceSkipRadio = str_replace('"', "###", $_choiceSkipRadio);
$_choiceSkipRadio = str_replace("'", "***", $_choiceSkipRadio);
$Output	.=	'<tr>'
		.	'<td class="defaultfont" width="10%">### . ' . $_choiceSkipRadio . ' . ###</td>'
		.	'<td class="defaultfont" width="60%" colspan="2"><label for="le_defaultLanguage_skip">' . $GLOBALS["lang"]["community"]["choice"]["skip"] . '</label></td>'
		.	'</tr>';

//$_choiceJoinCheckbox = "I am already a member<br />";
//$_choiceJoinButton = "";
//$_choiceJoin = $_choiceJoinCheckbox.$_choiceJoinButton;
//
//$_choiceSkip = "no!<br />";

//$Output .= "<br />".$_choiceNotYet.$_choiceJoin.$_choiceSkip;
 
//$Output .= "<hr />";


/*
$installAbleLanguages = $GLOBALS['updateServerTemplateData']['installAbleLanguages'];
if(isset($_SESSION['clientSyslng']) && !in_array($_SESSION['clientSyslng'], $installAbleLanguages)) {
	unset($_SESSION['clientSyslng']);

}

if(!isset($_SESSION['clientSyslng'])) {
	if($_SESSION['clientLng'] == "de" && in_array("Deutsch", $installAbleLanguages)) {
		$_SESSION['clientSyslng'] = "Deutsch";
		
	} elseif(in_array("English", $installAbleLanguages)) {
		$_SESSION['clientSyslng'] = "English";
		
	}
	
}

for ($i=0; $i<sizeof($installAbleLanguages); $i++) {

	$onClickJs	=	"
					for(i=0; i<" . sizeof($installAbleLanguages) . "; i++) {
						document.getElementById('le_extraLanguages['+i+']').disabled=false;
					}
					document.getElementById('le_extraLanguages[" . $i . "]').disabled = this.checked;";

	$Selected	=	(		isset($_SESSION['clientSyslng'])
						&&	$_SESSION['clientSyslng'] == $installAbleLanguages[$i]
					?
						true
					:
						(		!isset($_SESSION['clientSyslng'])
							&&	$i == 0
						?
							true
						:
							false
						)
					);

	$DefaultRadio = 'leCheckbox::get(
						"le_defaultLanguage",
						"' . $installAbleLanguages[$i] . '",
						array(
							"onClick"	=> "' . str_replace("\n", "", str_replace("\r\n", "\n", $onClickJs)) . '",
							"id"		=> "le_defaultLanguage_' . $i . '",
						),
						"",
						"' . $Selected . '",
						"radio"
					)';
	$DefaultRadio = str_replace('"', "###", $DefaultRadio);
	$DefaultRadio = str_replace("'", "***", $DefaultRadio);

	$Disabled =	(		isset($_SESSION['clientSyslng'])
					&&	$_SESSION['clientSyslng'] == $installAbleLanguages[$i]
				?
					"array('disabled'=>'disabled')"
				:	(		!isset($_SESSION['clientSyslng'])
						&&	$i == 0
					?
						"array('disabled'=>'disabled')"
					:
						"array()"
					)
				);

	$Selected	=	(		isset($_SESSION['clientDesiredLanguages'])
						&&	in_array($installAbleLanguages[$i], $_SESSION['clientDesiredLanguages'])
						&&	$installAbleLanguages[$i] != $_SESSION['clientSyslng']
					?
						true
					:
						false
					);

	$AdditionalCheckbox = 'leCheckbox::get(
								"le_extraLanguages[' . $i . ']",
								"' . $installAbleLanguages[$i] . '",
								' . $Disabled . ',
								"",
								"' . $Selected . '"
							)';
	$AdditionalCheckbox = str_replace('"', "###", $AdditionalCheckbox);
	$AdditionalCheckbox = str_replace("'", "***", $AdditionalCheckbox);

	$Output	.=	'<tr>'
			.	'<td><label for="le_defaultLanguage_' . $i . '">' . $installAbleLanguages[$i] . '</label></td>'
			.	'<td align="center">### . ' . $DefaultRadio . ' . ###</td>'
			.	'<td align="center">### . ' . $AdditionalCheckbox . ' . ###</td>'
			.	'</tr>';

}
*/
$Output .= '</table>';

$Output = '"' . str_replace('###', '"',  str_replace('***', "'", str_replace('"', '\"', $Output))) . '"';

$Code = <<<CODE
<?php

\$this->setHeadline(\$this->Language['headline']);
\$this->setContent(\$this->Language['content'] . {$Output});
return LE_STEP_NEXT;

?>
CODE;

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = $Code;

?>