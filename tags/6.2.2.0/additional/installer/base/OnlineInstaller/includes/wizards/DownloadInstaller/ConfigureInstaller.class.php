<?php
class ConfigureInstaller extends leStep {

	var $AutoContinue = 3;

	var $EnabledButtons = array('next');


	function execute(&$Template) {

		// update the steps on the left side.
		// ulInstallerSteps
		$newLi = leStatus::get($GLOBALS['OnlineInstaller'], 'leStatus', $this->Wizard->Name, $this->Name, false);
		$newLi = str_replace("\n", "", $newLi);

		$javaScript = "top.document.getElementById(\"leStatus\").innerHTML=\"" . addslashes($newLi) . "\";";
		$Template->addJavascript($javaScript);

		$this->setHeadline($this->Language['headline']);

		$this->setContent($this->Language['content']);

		return LE_STEP_NEXT;

	}

}