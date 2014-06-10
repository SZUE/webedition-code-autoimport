<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class versionFragment extends taskFragment{

	function __construct($name, $taskPerFragment, $pause = 0, $bodyAttributes = "", $initdata = ""){
		parent::__construct($name, $taskPerFragment, $pause, $bodyAttributes, $initdata);
	}

	function doTask(){
		we_version::todo($this->data);
		$this->updateProgressBar();
	}

	function updateProgressBar(){
		$percent = round((100 / count($this->alldata)) * (1 + $this->currentTask));
		print we_html_element::jsElement(
				'if(parent.wizbusy.document.getElementById("progr")){parent.wizbusy.document.getElementById("progr").style.display="";};parent.wizbusy.setProgressText("pb1",(parent.wizbusy.document.getElementById("progr") ? "' . addslashes(
					we_util_Strings::shortenPath($this->data["path"] . " - " . g_l('versions', '[version]') . " " . $this->data["version"], 33)) . '" : "' . "test" . addslashes(
					we_util_Strings::shortenPath($this->data["path"] . " - " . g_l('versions', '[version]') . " " . $this->data["version"], 60)) . '") );parent.wizbusy.setProgress(' . $percent . ');');
	}

	function finish(){
		if(!empty($_SESSION['weS']['versions']['logResetIds'])){
			$versionslog = new versionsLog();
			$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logResetIds'], versionsLog::VERSIONS_RESET);
		}
		unset($_SESSION['weS']['versions']['logResetIds']);
		$responseText = weRequest('string', "responseText", "");
		echo we_html_tools::getHtmlTop();
		switch(weRequest('string', 'type')){
			case "delete_versions":
				$responseText = g_l('versions', '[deleteDateVersionsOK]');
				break;
			case "reset_versions":
				$responseText = g_l('versions', '[resetAllVersionsOK]');
		}
		echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(
				addslashes($responseText ? $responseText : ""), we_message_reporting::WE_MESSAGE_NOTICE) . '

			// reload current document => reload all open Editors on demand

			var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
			for (frameId in _usedEditors) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);

				} else {
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
				}
			}
			_multiEditorreload = true;

			//reload tree
			top.opener.we_cmd("load", top.opener.treeData.table ,0);

			top.close();
		') .
		'</head></html>';
	}

	function printHeader(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop() . '</head>';
	}

	function printBodyTag($attributes = ""){

	}

	function printFooter(){
		$this->printJSReload();
	}

}
