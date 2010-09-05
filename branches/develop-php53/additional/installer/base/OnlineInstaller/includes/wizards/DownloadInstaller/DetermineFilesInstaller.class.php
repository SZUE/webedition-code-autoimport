<?php
class DetermineFilesInstaller extends leStep {

	var $EnabledButtons = array("reload");

	var $ProgressBarVisible = true;


	function execute(&$Template) {

		// start the session on server
		if (!isset($_REQUEST["liveUpdateSession"]) || $_REQUEST["liveUpdateSession"] == "") {

			// use other template
			$Template->UseOnlineInstallerTemplate = false;

			if (!isset($_REQUEST["update_cmd"])) {
				$_REQUEST["update_cmd"] = "downloadInstaller";

			}

			if (!isset($_REQUEST["detail"])) {
				$_REQUEST["detail"] = "determineInstallerFiles";

			}

			$SessionForm = liveUpdateHttp::getServerSessionForm();
			$Template->Output = $SessionForm;

		} else {
			$this->liveUpdateHttpResponse = $this->getLiveUpdateHttpResponse();
			return LE_STEP_NEXT;

		}

	}

}