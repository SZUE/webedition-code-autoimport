<?php

class updateUtil extends updateUtilBase {

	/**
	 * @param string $detail
	 * @param boolean $nextWizardStep
	 * @param mixed $liveUpdateSession
	 * @return string
	 */
	function getCommonHrefParameters($detail, $nextWizardStep=false, $liveUpdateSession=false) {

		$paraStr	=	"leWizard=" . $_REQUEST["leWizard"]
					.	"&leStep=" . ($nextWizardStep ? $_REQUEST["nextLeStep"] : $_REQUEST["leStep"] )
					.	"&update_cmd=" . $_REQUEST["update_cmd"]
					.	"&detail=" . $detail;
			
		if(isset($_SESSION['we_cmd'][0])) {
			$paraStr .= "&we_cmd[0]=" . $_SESSION['we_cmd'][0];
			
		}
			
		if($liveUpdateSession) {
			$paraStr .= "&liveUpdateSession=" . $liveUpdateSession;
			
		} else {
			$paraStr .= "&liveUpdateSession=" .  session_id();
			
		}

		return $paraStr;

	}

}

?>