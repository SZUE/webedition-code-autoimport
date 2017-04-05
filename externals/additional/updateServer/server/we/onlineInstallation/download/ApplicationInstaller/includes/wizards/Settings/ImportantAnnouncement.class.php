<?php
/**
 * $Id: ImportantAnnouncement.class.php 13540 2017-03-12 11:48:37Z mokraemer $
 */

class ImportantAnnouncement extends leStep{
	var $EnabledButtons = array("next");

	function execute(&$Template = ''){

		if(!isset($_REQUEST['backStep'])){
			$Template->addJavascript("top.leEffect.switchTheme('5F8A1F', '007abd', '" . LE_APPLICATION_INSTALLER_URL . "', 'webEdition Installer');");
		}
		$this->setHeadline($this->Language['headline']);

		$this->setContent($this->Language["content"]);

		return LE_STEP_NEXT;
	}

	function check(&$Tempalte = ''){

		return true;
	}

}
