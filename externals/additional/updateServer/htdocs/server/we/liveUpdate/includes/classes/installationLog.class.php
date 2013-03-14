<?php

class installationLog extends installationLogBase {


	function insertUpgradeEntry() {
		installationLog::insertUpdateEntry();

	}


	function insertUpdateEntry() {
		installationLog::insertLogEntry($_SESSION['clientDomain'], installationLog::getWeId(), $_SERVER['REMOTE_ADDR'], $_REQUEST['update_cmd'], $_REQUEST['detail'], $_SESSION['clientVersionNumber'], $_SESSION['clientTargetVersionNumber'], '', 0);

	}

}

?>