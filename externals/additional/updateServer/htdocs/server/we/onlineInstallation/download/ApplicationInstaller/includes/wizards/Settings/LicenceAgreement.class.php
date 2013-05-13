<?php

	class LicenceAgreement extends leStep {

		//var $EnabledButtons = array("next");
		var $EnabledButtons = array("back");
		//var $EnabledButtons = array("next","back");
		
		function execute(&$Template = '') {

			if(!$this->CheckFailed && !isset($_REQUEST['backStep'])) {
				$Template->addJavascript("top.leEffect.switchTheme('5F8A1F', '007abd', '" . LE_APPLICATION_INSTALLER_URL . "', 'webEdition Installer');");

			}

			$LicenceFile = LE_APPLICATION_INSTALLER_PATH . "/includes/language/licenceagreement/". $_SESSION['leInstallerLanguage'] . ".txt";

			$this->setHeadline($this->Language['headline']);

			$Name = 'acceptAgreement';
			$Value = 1;
			$Attributes = array(
				"onClick"	=> "top.leForm.evalCheckBox(this, 'top.leButton.enable(\'next\');', 'top.leButton.disable(\'next\');');",
				"id" => "acceptAgreement",
			);
			$Text = $this->Language["labelAccept"];
			$Checked = (isset($_SESSION['acceptAgreement']) && $_SESSION['acceptAgreement']) ? true : false;
			$AcceptConnection = leCheckbox::get($Name, $Value, $Attributes, $Text, $Checked);

			$Licence = "";
			if(file_exists($LicenceFile) && is_file($LicenceFile)) {
				$Licence = nl2br(implode("", file($LicenceFile)));

			}
			
			if($Checked) {
				$Template->addJavascript("top.leButton.enable('next');");
				$Template->addJavascript("document.getElementById('acceptAgreement').checked = true;");
			}

			$Content = <<<EOF
<div id="licenceAgreementDiv">
	{$Licence}
</div>
<br />
{$AcceptConnection}
EOF;

			$this->setContent($Content);

			return LE_STEP_NEXT;

		}
		
		
		function check(&$Tempalte = '') {
			
			if(isset($_REQUEST['acceptAgreement'])) {
				$_SESSION['acceptAgreement'] = true;
				return true;
				
			}
			return false;
			
		}

	}

?>