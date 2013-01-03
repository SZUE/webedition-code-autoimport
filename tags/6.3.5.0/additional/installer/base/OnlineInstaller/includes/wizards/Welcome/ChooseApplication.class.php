<?php
class ChooseApplication extends leStep {

	var $EnabledButtons = array('next', 'back');


	function execute(&$Template) {

		$Options = array();
		$JSString = 'var information = new Array();' . "\n";
		foreach($GLOBALS['leApplicationList'] as $Key => $Value) {
			$Options[$Key] = $Value['Name'];
			$JSString .= 'information["' . $Key . '"] = new Array();' . "\n";
			$JSString .= 'information["' . $Key . '"]["Name"] = "' . $Value['Name'] . '";' . "\n";
			$JSString .= 'information["' . $Key . '"]["Description"] = "' . $Value['Description'] . '";' . "\n";
			$JSString .= 'information["' . $Key . '"]["Longdescription"] = "' . $Value['Longdescription'] . '";' . "\n";
			
		}
		$temp = $Options;

		$Name = 'changeApplication';
		$Value = isset($_SESSION['leApplication']) ? $_SESSION['leApplication'] : array_shift($temp);

		$Attributes = array(
			'onchange'	=> 'switchInformation(this.value)',
			'id'		=> 'changeApplication',
			'style'		=> 'width: 293px',
		);

		$Application = $GLOBALS['leApplicationList'][$Value];

		$Select = leSelect::get($Name, $Options, $Value, $Attributes);

		$this->setHeadline($this->Language['headline']);

		$Content = <<<EOF
{$this->Language['content']}<br />
<br />

<b>{$this->Language['select_application']}:</b><br />
{$Select}<br /><br />

<h1 id="leInfoHeadline">
{$Application['Name']}
</h1>
<p id="leInfoDescription">
{$Application['Description']}
</p>
<p id="leLongDescription">
{$Application['Longdescription']}
</p>
EOF;
		$this->setContent($Content);

		$Javascript = <<<EOF
{$JSString}
top.switchInformation = function(val) {
	top.document.getElementById('leInfoHeadline').innerHTML = information[val]['Name'];
	top.document.getElementById('leInfoDescription').innerHTML = information[val]['Description'];
	top.document.getElementById('leLongDescription').innerHTML = information[val]['Longdescription'];
}
EOF;
		$Template->addJavascript($Javascript);

		return LE_STEP_NEXT;

	}


	function check() {

		if(isset($_REQUEST['changeApplication']) && array_key_exists($_REQUEST['changeApplication'], $GLOBALS['leApplicationList'])) {
			$_SESSION['leApplication'] = $_REQUEST['changeApplication'];


		}
		return true;

	}

}