<?php

class installerBase{

	/**
	 * @return array
	 */
	static function getInstallationStepNames(){

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
	static function getInstallerProgressPercent(){

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
	static function getNextUpdateDetail($currentStep = false){

		if(!$currentStep){
			$currentStep = $_REQUEST['detail'];
		}

		$steps = static::getInstallationStepNames();
		foreach($steps as $i=>$step){
			if($currentStep == $step && isset($steps[($i + 1)])){
				return $steps[($i + 1)];
			}
		}
		return ""; //$steps[$i];
	}

}
