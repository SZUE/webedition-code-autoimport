<?php

class installationLogBase {

	function getWeId() {
		if(isset($_SESSION['clientUid'])) {
			$serial = license::getSerialByUid($_SESSION['clientUid']);
			return license::getStockTableIdBySerial($serial);
		} else {
			return 0;
		}

	}


	function insertRegistrationEntry($notice='') {

		$priority = 0;

		if ($notice != 'ok') {
			$priority = 1;

		}

		$weId = license::getStockTableIdBySerial(license::formatSerial($GLOBALS['clientRequestVars']['clientSerial']));
		installationLog::insertLogEntry($_SESSION['clientDomain'], $weId, $_SERVER['REMOTE_ADDR'], $_REQUEST['update_cmd'], $_REQUEST['detail'], $_SESSION['clientVersionNumber'], 0, $notice, $priority);

	}


	function insertModulesEntry() {
		if(sizeof($_SESSION['clientDesiredModules'])>0) {
			$notice = implode(', ', $_SESSION['clientDesiredModules']);
			installationLog::insertLogEntry($_SESSION['clientDomain'], installationLog::getWeId(), $_SERVER['REMOTE_ADDR'], $_REQUEST['update_cmd'], $_REQUEST['detail'], $_SESSION['clientVersionNumber'], 0, $notice, 0);
			
		}

	}


	function insertLanguagesEntry() {
		$notice = implode(', ', $_SESSION['clientDesiredLanguages']);
		installationLog::insertLogEntry($_SESSION['clientDomain'], installationLog::getWeId(), $_SERVER['REMOTE_ADDR'], $_REQUEST['update_cmd'], $_REQUEST['detail'], $_SESSION['clientVersionNumber'], 0, $notice, 0);
		
	}

	function insertLogEntry($domain='', $weId=0, $ip='', $updateCmd='', $detail='', $startVersion='', $endVersion='', $notice='', $priority='') {
		
		/*
		global $DB_Register;
		$query = "
			INSERT INTO " . INSTALLATIONLOG_TABLE . "
			(date, weID, domain, ip, updateCmd, detail, startVersion, endVersion, notice, priority)
			VALUES (NOW(), $weId, \"$domain\", \"$ip\", \"$updateCmd\", \"$detail\", $startVersion, $endVersion, \"$notice\", $priority)
		";
		$DB_Register->query($query);
		*/
		
		// 2007-06-19 15:31:08
		//$date = date("Y-m-d h:i:s");
		// log via soap:
		/*
		$data = array(
			'date' => $date,
			'weID' => $weId,
			'domain' => $domain,
			'ip' => $ip,
			'command' => $updateCmd,
			'detail' => $detail,
			'startVersion' => $startVersion,
			'endVersion' => $endVersion,
			'notice' => $notice,
			'priority' => $priority,
			'product' => 'webEdition 6'
		);
		$request = new soapRequest();
		$result = $request->GetData("logInstallation",$data);
		*/
	}

}
?>