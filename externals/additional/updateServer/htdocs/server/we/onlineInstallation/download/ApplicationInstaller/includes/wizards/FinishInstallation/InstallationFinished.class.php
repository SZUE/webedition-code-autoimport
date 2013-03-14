<?php

	class InstallationFinished extends leStep {

		//var $EnabledButtons = array('next');


		function execute(&$Template = '') {


			unset($GLOBALS['leApplicationList']['webEdition']);
			
			$PostContent = "";
			$JSString = "";
			if(sizeof($GLOBALS['leApplicationList']) < 0) { // temporarily disabled (should never be smaller than zero)

				$Options = array();
				$JSString = 'var information = new Array();' . "\n";

				foreach($GLOBALS['leApplicationList'] as $Key => $Value) {
					$Options[$Key] = $Value['Name'];
					$JSString .= 'information["' . $Key . '"] = new Array();' . "\n";
					$JSString .= 'information["' . $Key . '"]["Name"] = "' . $Value['Name'] . '";' . "\n";
					$JSString .= 'information["' . $Key . '"]["Description"] = "' . $Value['Description'] . '";' . "\n";

				}
				$temp = $Options;

				$Name = 'changeApplication';
				$Value = array_shift($temp);

				$Attributes = array(
					'onchange'	=> 'switchInformation(this.value)',
					'id'		=> 'changeApplication',
					'style'		=> 'width: 293px;',
					'disabled'	=> 'disabled',
				);

				$Application = $GLOBALS['leApplicationList'][$Value];

				$Select = leSelect::get($Name, $Options, $Value, $Attributes);

				$TrueJs	=	'top.document.getElementById(\'changeApplication\').disabled=false;'
						.	'top.leForm.setInputField(\'leWizard\', \'DownloadInstaller\');'
						.	'top.leForm.setInputField(\'leStep\', \'DetermineFilesInstaller\');';

				$FalseJs	=	'top.document.getElementById(\'changeApplication\').disabled=true;'
							.	'top.leForm.setInputField(\'leWizard\', \'FinishInstallation\');'
							.	'top.leForm.setInputField(\'leStep\', \'CleanUp\');';
				
				$Name = 'nextApplicaton';
				$Value = 1;
				$Attributes = array(
					"onClick"	=> "top.leForm.evalCheckBox(this, '" . $TrueJs . "', '" . $FalseJs . "');",
				);
				$Text = $this->Language["installMore"];
				$Checked = false;
				$nextApplicaton = leCheckbox::get($Name, $Value, $Attributes, $Text, $Checked);

				$PostContent = <<<EOF
<p>
{$this->Language['additional_software']}<br />
<br />
{$nextApplicaton}<br />
<b>{$this->Language['choose_software']}:</b><br />
{$Select}
</p>
<h1 id="leInfoHeadline">
{$Application['Name']}
</h1>
<p id="leInfoDescription">
{$Application['Description']}
</p>
EOF;
				$Template->addJavascript('top.leForm.setInputField("leWizard", "DownloadInstaller");');
				$Template->addJavascript('top.leForm.setInputField("leStep", "DetermineFilesInstaller");');

			}

			$this->setHeadline($this->Language['headline']);

			$Button = leButton::get("openWebEdition", $this->Language['login_webEdition'], "javascript:window.open('/webEdition/index.php', 'webEdition');", 150, 22, "", false, false);
									
			$Content = <<<EOF
{$this->Language['content']}<br />
<div align="center" class="defaultfont">
{$Button}
</div>
<div style="margin-top:20px;">Diese webEdition Version wurde ermöglicht durch die Arbeit des gemeinnützigen webEdition e.V. Unterstützen Sie die kostenlose und freiwillige Arbeit der Vereins- und Community-Mitglieder. 
<br>Ermöglichen Sie durch Ihre Spende, dass:<ul>
<li>der webEdition e.V. professionelle Entwickler einstellen kann</li>
<li>die Beseitigung von Fehlern sowie die Entwicklung<br>
neuer Features beschleunigt wird</li>
<li>die Weiterentwicklung von webEdition langfristig<br>
gesichert wird</li></ul><form target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                  <input type="hidden" name="cmd" value="_s-xclick">
                  <input type="hidden" name="hosted_button_id" value="BERPPPT588RAE">
                  <input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online spenden – mit PayPal.">
                  <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
                </form></div>
{$PostContent}
EOF;
			$this->setContent($Content);

			$Javascript = <<<EOF
{$JSString}
top.openWebEdition_mouse_event = "";
top.openWebEdition_enabled = true;
top.switchInformation = function(val) {
	top.document.getElementById('leInfoHeadline').innerHTML = information[val]['Name'];
	top.document.getElementById('leInfoDescription').innerHTML = information[val]['Description'];

}
EOF;
			$Template->addJavascript($Javascript);

			return LE_STEP_NEXT;

		}


		function check(&$Template = '') {

			if(isset($_REQUEST['changeApplication']) && array_key_exists($_REQUEST['changeApplication'], $GLOBALS['leApplicationList'])) {
				$_SESSION['leApplication'] = $_REQUEST['changeApplication'];


			}
			return true;

		}


	}

?>