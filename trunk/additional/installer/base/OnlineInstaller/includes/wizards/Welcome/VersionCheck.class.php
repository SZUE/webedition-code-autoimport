<?php
class VersionCheck extends leStep {

	var $AutoContinue = 10;


	function prepare() {
		global $LU_Version;
		$_SESSION["le_installerVersion"] = $LU_Version;
	}


	function execute(&$Template = '') {

		$Success = true;
		$VersionState = true;
		
		$VersionImage = leLayout::getRequirementStateImage();
		if ( !(isset($_SESSION["le_testSession"]) && $_SESSION["le_testSession"] == "test-session" ) ) {
			$Success = false;
			$VersionImage = leLayout::getRequirementStateImage(false);
			$Template->addError($this->Language['installerVersionFailed']);

		}

		$Content = <<<EOF
{$this->Language['content']}<br />
<table id="requirementsLog">
<tr>
	<td>&middot; {$this->Language['installerVersion']}</td>
	<td align="right">{$VersionImage}</td>
</tr>
</table>
EOF;

		$this->setHeadline($this->Language['headline']);

		$this->setContent($Content);

		if ($Success) {
			return LE_STEP_NEXT;

		} else {
			$this->setContent($this->Language['failureMessage']);
			return LE_STEP_FATAL_ERROR;

		}

	}

}