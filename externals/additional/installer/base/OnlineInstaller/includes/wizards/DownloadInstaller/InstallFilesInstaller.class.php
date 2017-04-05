<?php
/**
 * $Id: InstallFilesInstaller.class.php 13539 2017-03-12 11:39:19Z mokraemer $
 */

class InstallFilesInstaller extends leStep{
	var $EnabledButtons = array("reload");
	var $ProgressBarVisible = true;

	function execute(&$Template = ''){
		$this->liveUpdateHttpResponse = $this->getLiveUpdateHttpResponse();

		return LE_STEP_NEXT;
	}

}
