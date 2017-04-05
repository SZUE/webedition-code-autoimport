<?php
/**
 * $Id: WriteApplicationConfiguration.class.php 13540 2017-03-12 11:48:37Z mokraemer $
 */

class WriteApplicationConfiguration extends leStep{
	var $EnabledButtons = array("reload");
	var $ProgressBarVisible = true;

	function execute(&$Template = ''){

		$this->liveUpdateHttpResponse = $this->getLiveUpdateHttpResponse();

		return LE_STEP_NEXT;
	}

}
