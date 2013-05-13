<?php

class installationLog extends installationLogBase {


	function insertRegistrationEntry($notice='') {

		$priority = 0;
		$notice = 'registration';

		$weId = license::getStockTableIdBySerial(license::formatSerial($_SESSION['clientSerial']));
		installationLog::insertLogEntry($_SESSION['clientDomain'], $weId, $_SERVER['REMOTE_ADDR'], $_REQUEST['update_cmd'], $_REQUEST['detail'], $_SESSION['clientTargetVersionNumber'], $_SESSION['clientTargetVersionNumber'], $notice, $priority);

	}


	function insertInstallationEntry($isDemo = true) {
		
		if($isDemo) {
			$weId = '0';
			$notice = 'installation demo';
			
		} else {
			$weId = license::getStockTableIdBySerial(license::formatSerial($_SESSION['clientSerial']));
			$notice = 'installation';
			
		}
		installationLog::insertLogEntry($_SESSION['clientDomain'], $weId, $_SERVER['REMOTE_ADDR'], $_REQUEST['update_cmd'], $_REQUEST['detail'], 0, $_SESSION['clientTargetVersionNumber'], $notice, 0);

	}

}

?>