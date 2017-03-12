<?php
/**
 * $Header$
 */

class leLayout{

	static function getRequirementStateImage($State = true){
		return ($State ?
			"<img src=\"" . LE_ONLINE_INSTALLER_URL . "/img/leLayout/requirementOk.gif\" />" :
			"<img src=\"" . LE_ONLINE_INSTALLER_URL . "/img/leLayout/requirementFailure.gif\" />");
	}

	static function getWarningStateImage($State = true){
		return ($State ?
			"<img src=\"" . LE_ONLINE_INSTALLER_URL . "/img/leLayout/requirementOk.gif\" />" :
			"<img src=\"" . LE_ONLINE_INSTALLER_URL . "/img/leLayout/alert_tiny.gif\" />");
	}

	static function getHelp($text = ""){
		return '<a tabindex="1000" href="javascript://" onclick="alert(\'' . $text . '\');" style="text-decoration: none; cursor: help;">[?]</a>';
	}

}
