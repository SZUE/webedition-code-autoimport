<?php
/**
 * $Id: Welcome.class.php 13539 2017-03-12 11:39:19Z mokraemer $
 */

class Welcome extends leStep{
	var $EnabledButtons = array("next");

	function execute(&$Template = ''){

		$PostContent = "";
		$AvailableLanguages = leOnlineInstaller::getAvailableLanguages();
		if(count($AvailableLanguages) > 1){


			$Name = 'changeApplication';
			$Options = array();
			foreach($AvailableLanguages as $Language){
				$Options[$Language] = $this->Language['language_' . $Language];
			}
			$Value = $_SESSION['leInstallerLanguage'];

			$Attributes = array(
				'onchange' => 'document.location.href=\'' . LE_INSTALLER_ADAPTER_URL . '?' . (isset($_REQUEST['debug']) ? 'debug=' . $_REQUEST['debug'] . '&' : '') . 'leInstallerLanguage=\'+this.value;',
				'style' => 'width: 293px;',
			);

			$Select = leSelect::get($Name, $Options, $Value, $Attributes);

			$PostContent = <<<EOF
<b>{$this->Language['choose_language']}:</b><br />
{$Select}<br/>{$this->Language['ISO_language']}
EOF;
		}

		$this->setHeadline($this->Language['headline']);

		$Content = <<<EOF
{$this->Language['content']}<br />
<br />

{$PostContent}
EOF;

		$this->setContent($Content);

		return LE_STEP_NEXT;
	}

}
