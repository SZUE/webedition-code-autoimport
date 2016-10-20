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
class we_versions_fragment extends we_fragment_base{

	public function __construct($name, $initdata = ''){
		parent::__construct($name, 1, 0, [], $initdata);
	}

	function doTask(){
		we_versions_version::todo($this->data);
		$this->updateProgressBar();
	}

	function updateProgressBar(){
		$percent = round((100 / count($this->alldata)) * (1 + $this->currentTask));
		echo we_html_element::jsElement(
			'if(parent.wizbusy.document.getElementById("progr")){parent.wizbusy.document.getElementById("progr").style.display="";};parent.wizbusy.setProgressText("pb1",(parent.wizbusy.document.getElementById("progr") ? "' . addslashes(
				we_base_util::shortenPath($this->data["path"] . " - " . g_l('versions', '[version]') . " " . $this->data["version"], 33)) . '" : "' . "test" . addslashes(
				we_base_util::shortenPath($this->data["path"] . " - " . g_l('versions', '[version]') . " " . $this->data["version"], 60)) . '") );parent.wizbusy.setProgress("",' . $percent . ');');
	}

	function finish(){
		if(!empty($_SESSION['weS']['versions']['logResetIds'])){
			$versionslog = new we_versions_log();
			$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logResetIds'], we_versions_log::VERSIONS_RESET);
		}
		unset($_SESSION['weS']['versions']['logResetIds']);
		$responseText = we_base_request::_(we_base_request::STRING, 'responseText', "");
		switch(we_base_request::_(we_base_request::STRING, 'type')){
			case 'delete_versions':
				$responseText = g_l('versions', '[deleteDateVersionsOK]');
				break;
			case 'reset_versions':
				$responseText = g_l('versions', '[resetAllVersionsOK]');
		}
		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement(we_message_reporting::getShowMessageCall(
					addslashes($responseText ?: ""), we_message_reporting::WE_MESSAGE_NOTICE) . '

			// reload current document => reload all open Editors on demand

			var _usedEditors =  WE().layout.weEditorFrameController.getEditorsInUse();
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
			top.opener.we_cmd("load", top.opener.top.treeData.table ,0);

			top.close();
		'), we_html_element::htmlBody());
	}

	static function printHeader(){
		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', ' ');
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding documents
	 *
	 * @return array
	 * @param string $btype "rebuild_all" or "rebuild_filter"
	 * @param string $categories csv value of category IDs
	 * @param boolean $catAnd true if AND should be used for more than one categories (default=OR)
	 * @param string $doctypes csv value of doctypeIDs
	 * @param string $folders csv value of directory IDs
	 * @param boolean $maintable if the main table should be rebuilded
	 * @param boolean $tmptable if the tmp table should be rebuilded
	 * @param int $templateID ID of a template (All documents of this template should be rebuilded)
	 */
	public static function getDocuments($type = 'delete_versions', $version){
		switch($type){
			case "delete_versions" :
				return self::getDocumentsDelete($version);
			case "reset_versions" :
				return self::getDocumentsReset($version);
		}
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding all documents and templates (Called from getDocuments())
	 *
	 * @return array
	 * @param boolean $maintable if the main table should be rebuilded
	 * @param boolean $tmptable if the tmp table should be rebuilded
	 */
	private static function getDocumentsDelete($version){
		$data = [];
		if(permissionhandler::hasPerm("ADMINISTRATOR")){

			$GLOBALS['DB_WE']->query($_SESSION['weS']['versions']['query']);
			while($GLOBALS['DB_WE']->next_record()){

				$data[] = ["ID" => $GLOBALS['DB_WE']->f("ID"),
					"documentID" => $GLOBALS['DB_WE']->f("documentID"),
					"type" => "version_delete",
					"version" => $GLOBALS['DB_WE']->f("version"),
					"timestamp" => $GLOBALS['DB_WE']->f("timestamp"),
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"table" => $GLOBALS['DB_WE']->f("documentTable"),
					"contenttype" => $GLOBALS['DB_WE']->f("ContentType"),
					"text" => $GLOBALS['DB_WE']->f("Text")
				];
			}
			unset($_SESSION['weS']['versions']['query']);
		}
		return $data;
	}

	private static function getDocumentsReset($version){
		$data = [];
		if(permissionhandler::hasPerm("ADMINISTRATOR")){

			$GLOBALS['DB_WE']->query($_SESSION['weS']['versions']['query']);
			while($GLOBALS['DB_WE']->next_record()){

				$data[] = ["ID" => $GLOBALS['DB_WE']->f("ID"),
					"documentID" => $GLOBALS['DB_WE']->f("documentID"),
					"type" => "version_reset",
					"version" => $GLOBALS['DB_WE']->f("version"),
					"timestamp" => $GLOBALS['DB_WE']->f("timestamp"),
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"table" => $GLOBALS['DB_WE']->f("documentTable"),
					"contenttype" => $GLOBALS['DB_WE']->f("ContentType"),
					"text" => $GLOBALS['DB_WE']->f("Text")
				];
			}
			unset($_SESSION['weS']['versions']['query']);
		}
		return $data;
	}

}
