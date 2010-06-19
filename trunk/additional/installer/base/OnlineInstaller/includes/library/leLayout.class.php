<?php
class leLayout {

	function getRequirementStateImage($State = true) {

		if ($State) {
			return "<img src=\"" . LE_ONLINE_INSTALLER_URL . "/img/leLayout/requirementOk.gif\" />";

		} else {
			return "<img src=\"" . LE_ONLINE_INSTALLER_URL . "/img/leLayout/requirementFailure.gif\" />";

		}
	}
	function getWarningStateImage($State = true) {

		if ($State) {
			return "<img src=\"" . LE_ONLINE_INSTALLER_URL . "/img/leLayout/requirementOk.gif\" />";

		} else {
			return "<img src=\"" . LE_ONLINE_INSTALLER_URL . "/img/leLayout/alert_tiny.gif\" />";

		}
	}

	function getHelp($text = "") {
		return '<a tabindex="1000" href="javascript://" onclick="alert(\''.$text.'\');" style="text-decoration: none; cursor: help;">[?]</a>';

	}

}