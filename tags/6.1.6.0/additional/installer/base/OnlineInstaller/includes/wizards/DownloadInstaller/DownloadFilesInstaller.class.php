<?php
class DownloadFilesInstaller extends leStep {

	var $EnabledButtons = array("reload");

	var $ProgressBarVisible = true;


	function execute(&$Template) {

		$this->liveUpdateHttpResponse = $this->getLiveUpdateHttpResponse();

		return LE_STEP_NEXT;

	}

}