<?php

	class PrepareApplicationInstallation extends leStep {

		var $EnabledButtons = array("reload");

		var $ProgressBarVisible = true;


		function execute(&$Template = '') {

			if (!isset($_REQUEST["update_cmd"])) {
				$_REQUEST["update_cmd"] = "installApplication";

			}

			if (!isset($_REQUEST["detail"])) {
				$_REQUEST["detail"] = "prepareApplicationInstallation";

			}

			$this->liveUpdateHttpResponse = $this->getLiveUpdateHttpResponse();

			return LE_STEP_NEXT;

		}

	}

?>