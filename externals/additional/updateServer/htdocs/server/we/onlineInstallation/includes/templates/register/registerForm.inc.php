<?php

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = '
<?php

	// Register
	$Name = "le_register";
	$Value = 1;
	$Attributes = array(
		"onClick"	=> "enableRegistration(this.checked);",
	);
	$Text = $this->Language[\'labelRegister\'];
	$Checked = isset($_SESSION["le_register"]) && $_SESSION["le_register"] ? true : false;
	$Register = leCheckbox::get($Name, $Value, $Attributes, $Text, $Checked);

	// Serialnumber
	$Name = "le_serial";
	$Value = isset($_SESSION["le_serial"]) ? $_SESSION["le_serial"] : "";
	$Attributes = array(
		"size"		=> "40",
		"style"		=> "width: 293px",
	);
	if(!isset($_SESSION["le_register"]) || $_SESSION["le_register"] == false) {
		$Attributes["disabled"] = "disabled";
	}
	$Value = isset($_SESSION["le_serial"]) ? $_SESSION["le_serial"] : "";
	$Serial = leInput::get($Name, $Value, $Attributes);

	$Content = <<<EOF
{$this->Language[\'content\']}<br />
<br />

{$Register}
<br />

<b>{$this->Language[\'serial\']}:</b><br />
{$Serial}<br />
EOF;

	$this->setHeadline($this->Language[\'headline\']);

	$this->setContent($Content);

	return LE_STEP_NEXT;

?>';

?>