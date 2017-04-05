<?php
/**
 * $Id: PrepareApplicationInstallation.class.php 13540 2017-03-12 11:48:37Z mokraemer $
 */

class PrepareApplicationInstallation extends leStep{
	var $EnabledButtons = array("reload");
	var $ProgressBarVisible = true;

	function execute(&$Template = ''){

		if(!isset($_REQUEST["update_cmd"])){
			$_REQUEST["update_cmd"] = "installApplication";
		}

		if(!isset($_REQUEST["detail"])){
			$_REQUEST["detail"] = "prepareApplicationInstallation";
		}

		$this->liveUpdateHttpResponse = $this->getLiveUpdateHttpResponse();

		return LE_STEP_NEXT;
	}

}
