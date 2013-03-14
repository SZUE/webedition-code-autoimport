<?php

class installerBase {


	/**
	 * @return array
	 */
	function getInstallationStepNames() {
		
		error_log("Overwrite this:");
		error_log(__FILE__ . ": " . __CLASS__ . "::" . __FUNCTION__);
		
		return array(
		);

	}



	/**
	 * returns progress of current installer
	 *
	 * @return integer
	 */
	function getInstallerProgressPercent() {
		
		error_log("Overwrite this:");
		error_log(__FILE__ . ": " . __CLASS__ . "::" . __FUNCTION__);

		return 0;
		
	}

	/**
	 * returns next step according to installation step names
	 *
	 * @param mixed $currentStep
	 * @return string
	 */
	function getNextUpdateDetail($currentStep=false) {

		if (!$currentStep) {
			$currentStep = $_REQUEST['detail'];

		}

		$steps = $this->getInstallationStepNames();
		for ($i=0;$i<sizeof($steps);$i++) {
			if ($currentStep == $steps[$i] && isset($steps[($i+1)])) {
				return $steps[($i+1)];

			}

		}
		return "";//$steps[$i];

	}


}
?>