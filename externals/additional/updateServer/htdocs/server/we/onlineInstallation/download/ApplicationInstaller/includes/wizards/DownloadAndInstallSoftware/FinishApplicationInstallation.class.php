<?php

	class FinishApplicationInstallation extends leStep {

		var $EnabledButtons = array("next");

		var $ProgressBarVisible = true;

		var $AutoContinue = 5;


		function execute(&$Template = '') {

			$this->setHeadline($this->Language['headline']);

			$this->setContent($this->Language['content']);

			return LE_STEP_NEXT;

		}

	}

?>