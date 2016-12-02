<?php

class HintAboutOnlineInstallation extends leStep{
	var $EnabledButtons = array('back');

	function execute(&$Template = ''){

		// If binary installer
		if(file_exists(LE_INSTALLER_PATH . "/OnlineInstaller.log.php") && is_file(LE_INSTALLER_PATH . "/OnlineInstaller.log.php")){
			$this->EnabledButtons = array();
		}

		$this->setHeadline($this->Language['headline']);

		$Attributes = array(
			"onClick" => "top.leForm.evalCheckBox(this, 'top.leButton.enable(\'next\');', 'top.leButton.disable(\'next\');');",
		);
		$AcceptConnection = leCheckbox::get('acceptConnection', 1, $Attributes, $this->Language["labelAccept"], false);

		if(isset($_SESSION['leChangedMod'])){
			$Template->addError(sprintf($this->Language['chmod_hint'], $_SESSION['leChangedMod']));
		}

		$Content = <<<EOF
{$this->Language['content']}<br />
<br />
{$AcceptConnection}
EOF;

		$this->setContent($Content);

		return LE_STEP_NEXT;
	}

}
