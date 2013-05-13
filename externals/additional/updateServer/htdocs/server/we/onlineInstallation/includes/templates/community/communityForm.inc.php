<?php

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = '
<?php

// Community
$Table = \'<table class="leContentTable">\';


$Output = "<table class=\'leContentTable\'>\n";
$_choiceNotYetOnClickJs ="document.getElementById(\'le_communityChoice_already_Form\').style.display = \'none\';\n";
//error_log($_SESSION["le_communityChoice"]);
if(!isset($_SESSION["le_communityChoice"]) || $_SESSION["le_communityChoice"] == "" || $_SESSION["le_communityChoice"] == "notYet") {
	$_choiceNotYetSelected = true;
	$_choiceNotYetRadioButtonDisable = " ";
} else {
	$_choiceNotYetSelected = false;
	$_choiceNotYetRadioButtonDisable = "display:none";
}
$_choiceNotYetRadio = leCheckbox::get(
						"le_communityChoice",
						"notYet",
						array(
							"onClick"	=> str_replace("\n", "", str_replace("\r\n", "\n", $_choiceNotYetOnClickJs)),
							"id"		=> "le_communityChoice_notYet",
						),
						"",
						$_choiceNotYetSelected,
						"radio"
					);
//$_choiceNotYetButton = leButton::get("le_communityChoice_notYet_button", $this->Language["button"]["enterData"], "#", "150", "22", "document.getElementById(\'le_communityRegistration_Form\').style.display = \'\';");
$Output	.=	\'<tr>\'
		.	\'<td class="defaultfont" width="20px">\' . $_choiceNotYetRadio . \'</td>\'
		.	\'<td class="defaultfont"><label for="le_communityChoice_notYet">\' . $this->Language["choice"]["notRegisteredYet"] . \'</label></td>\'
		.	\'</tr>\';
/*$Output	.=	\'<tr id="le_communityChoice_notYet_Form" style="\' . $_choiceNotYetRadioButtonDisable . \';">\'
		.	\'<td class="defaultfont" width="20px"></td>\'
		.	\'<td class="defaultfont">\'.$_choiceNotYetButton.\'</td>\'
		.	\'</tr>\';*/

$_choiceAlreadyOnClickJs = "document.getElementById(\'le_communityChoice_already_Form\').style.display = \'\';\n";
//error_log($_SESSION["le_communityChoice"]);
if(isset($_SESSION["le_communityChoice"]) && $_SESSION["le_communityChoice"] == "Already") {
	$_choiceAlreadySelected = true;
	$_choiceAlreadyRadioButtonDisable = "";
} else {
	$_choiceAlreadySelected = false;
	$_choiceAlreadyRadioButtonDisable = "display:none;";
}
// username/e-mail address
$name = \'le_communityChoice_Already_Email\';
$value = isset($_SESSION[\'le_communityChoice_Already_Email\']) ? $_SESSION[\'le_communityChoice_Already_Email\'] : "";
$attribs = array(
	\'size\'	=> \'40\',
	\'style\'	=> \'width: 250px\',
);
$type = "text";
$email_input = leInput::get($name, $value, $attribs, $type);
$email_help = leLayout::getHelp($this->Language["help"]["email"]);
// username/e-mail address
$name = \'le_communityChoice_Already_Password\';
$value = isset($_SESSION[\'le_communityChoice_Already_Password\']) ? $_SESSION[\'le_communityChoice_Already_Password\'] : "";
$attribs = array(
	\'size\'	=> \'40\',
	\'style\'	=> \'width: 250px\',
);
$type = "password";
$password_input = leInput::get($name, $value, $attribs, $type);
$password_help = leLayout::getHelp($this->Language["help"]["password"]);
// html code:
$_choiceAlreadyForm = \'<b>\'.$this->Language["input"]["email"].\':</b> \'.$email_help.\'<br />\'
	.$email_input.\'<br />\'
	.\'<b>\'.$this->Language["input"]["password"].\': </b> \'.$password_help.\'<br />\'
	.$password_input.\'<br />\';
$_choiceAlreadyRadio = leCheckbox::get(
						"le_communityChoice",
						"Already",
						array(
							"onClick"	=> str_replace("\n", "", str_replace("\r\n", "\n", $_choiceAlreadyOnClickJs)),
							"id"		=> "le_communityChoice_Already",
						),
						"",
						$_choiceAlreadySelected,
						"radio"
					);

$Output	.=	\'<tr>\'
		.	\'<td class="defaultfont" width="20px">\' . $_choiceAlreadyRadio . \'</td>\'
		.	\'<td class="defaultfont"><label for="le_communityChoice_Already">\' . $this->Language["choice"]["alreadyRegistered"] . \'</label></td>\'
		.	\'</tr>\';
$Output	.=	\'<tr id="le_communityChoice_already_Form" style="\'.$_choiceAlreadyRadioButtonDisable.\'">\'
		.	\'<td class="defaultfont" width="20px"></td>\'
		.	\'<td class="defaultfont">\'.$_choiceAlreadyForm.\'</td>\'
		.	\'</tr>\';

$_choiceSkipOnClickJs = "document.getElementById(\'le_communityChoice_already_Form\').style.display = \'none\';\n";

//error_log($_SESSION["le_communityChoice"]);
if(isset($_SESSION["le_communityChoice"]) && $_SESSION["le_communityChoice"] == "skip") {
	$_choiceSkipSelected = true;
} else {
	$_choiceSkipSelected = false;
}
$_choiceSkipRadio = leCheckbox::get(
						"le_communityChoice",
						"skip",
						array(
							"onClick"	=> str_replace("\n", "", str_replace("\r\n", "\n", $_choiceSkipOnClickJs)),
							"id"		=> "le_defaultLanguage_skip",
						),
						"",
						$_choiceSkipSelected,
						"radio"
					);

$Output	.=	\'<tr>\'
		.	\'<td class="defaultfont" width="20px">\' . $_choiceSkipRadio . \'</td>\'
		.	\'<td class="defaultfont"><label for="le_defaultLanguage_skip">\' . $this->Language["choice"]["skip"] . \'</label></td>\'
		.	\'</tr>\'
		.	\'</table>\';

$Registration = \'
	<div id="le_communityRegistration_Form" name="le_communityRegistration_Form" style="border:1px solid #555555; position:absolute; top:61px; left:45px; z-index:10000; padding:10px; height:300px; width:528px; background-color:#ffffff; display:none;">
		registration form for new community members
\';
$Registration .= leButton::get("le_communityRegistration_FormClose", $this->Language["button"]["hideForm"], "#", "150", "22", "document.getElementById(\'le_communityRegistration_Form\').style.display = \'none\';");

$Registration .= \'	</div>
\';

$Content = <<<EOF
{$this->Language[\'content\']}
{$Output}{$Registration}
<br />
{$this->Language[\'privacy\']}<br />
EOF;

	$this->setHeadline($this->Language[\'headline\']);

	$this->setContent($Content);

	return LE_STEP_NEXT;

?>';

?>