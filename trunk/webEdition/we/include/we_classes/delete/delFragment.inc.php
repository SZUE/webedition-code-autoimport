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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
include_once(WE_INCLUDES_PATH . 'we_delete_fn.inc.php');

class delFragment extends taskFragment{

	var $db;
	var $table;

	function __construct($name, $taskPerFragment, $pause, $table){
		$this->db = new DB_WE();
		$this->table = $table;
		parent::__construct($name, $taskPerFragment, $pause);
	}

	function init(){
		if(isset($_SESSION["todel"]) && $_SESSION["todel"]){
			$filesToDel = makeArrayFromCSV($_SESSION["todel"]);
			$this->alldata = array();
			foreach($filesToDel as $id){
				if(f('SELECT IsFolder FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'IsFolder', $this->db)){
					we_readChilds($id, $this->alldata, FILE_TABLE, false);
				}
			}

			foreach($filesToDel as $id){
				array_push($this->alldata, $id);
			}

			$_SESSION["we_not_deleted_entries"] = array();
			$_SESSION["we_go_seem_start"] = false;
		}
	}

	function doTask(){
		$p = addslashes(shortenPath(id_to_path($this->data, $this->table, $this->db), 70));
		$GLOBALS["we_folder_not_del"] = array();
		$currentID = (isset($_REQUEST["currentID"]) && $_REQUEST["currentID"]) ? $_REQUEST["currentID"] : 0;
		$currentParents = array();
		we_readParents($currentID, $currentParents, $this->table);

		deleteEntry($this->data, $this->table, false);
		if(count($GLOBALS["we_folder_not_del"]) > 0){
			array_push($_SESSION["we_not_deleted_entries"], $GLOBALS["we_folder_not_del"][0]);
		}
		if($this->data == $currentID){
			$_SESSION["we_go_seem_start"] = true;
		}
		$percent = round((100 / count($this->alldata)) * (1 + $this->currentTask));
		print we_html_element::jsElement('parent.delmain.setProgressText("pb1","' . sprintf(g_l('delete', "[delete_entry]"), $p) . '");parent.delmain.setProgress(' . $percent . ');');
	}

	function finish(){
		unset($_SESSION["todel"]);
		if(count($_SESSION["we_not_deleted_entries"])){
			$alert = we_message_reporting::getShowMessageCall(makeAlertDelFolderNotEmpty($_SESSION["we_not_deleted_entries"]), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$alert = we_message_reporting::getShowMessageCall(g_l('alert', "[delete_ok]"), we_message_reporting::WE_MESSAGE_NOTICE);
		}
		unset($_SESSION["we_not_deleted_entries"]);
		print we_html_element::jsElement($alert . (($_SESSION["we_mode"] == "seem" && $_SESSION["we_go_seem_start"]) ? 'top.opener.top.we_cmd("start_multi_editor");' : '') . 'top.close();');
		unset($_SESSION["we_go_seem_start"]);
	}

	function printHeader(){
		we_html_tools::protect();
		print we_html_tools::htmlTop() . "</head>";
	}

}
