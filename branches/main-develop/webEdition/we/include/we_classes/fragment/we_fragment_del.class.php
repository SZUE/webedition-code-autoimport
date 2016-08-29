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
class we_fragment_del extends we_fragment_base{
	private $db;
	private $table;

	function __construct($name, $table){
		$this->db = new DB_WE();
		$this->table = $table;
		parent::__construct($name, 1, 0);
	}

	function init(){
		if(empty($_SESSION['weS']['fragDel']['todel'])){
			return;
		}
		$filesToDel = implode(',', array_map('intval', explode(',', trim($_SESSION['weS']['fragDel']['todel'], ','))));
		$this->db->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE ID IN (' . $filesToDel . ') ORDER BY IsFolder, LENGTH(Path) DESC');
		$this->alldata = $this->db->getAll(true);
	}

	function doTask(){
		$p = addslashes(we_base_util::shortenPath(id_to_path($this->data, $this->table, $this->db), 70));
		$GLOBALS['we_folder_not_del'] = [];
		$currentID = we_base_request::_(we_base_request::INT, 'currentID', 0);
		$currentParents = [];
		we_readParents($currentID, $currentParents, $this->table);

		we_base_delete::deleteEntry($this->data, $this->table, false);
		if(($GLOBALS['we_folder_not_del'])){
			$_SESSION['weS']['fragDel']['we_not_deleted_entries'][] = $GLOBALS['we_folder_not_del'][0];
		}
		if($this->data == $currentID){
			$_SESSION['weS']['fragDel']['we_go_seem_start'] = true;
		}
		$percent = round((100 / count($this->alldata)) * (1 + $this->currentTask));
		echo we_html_element::jsElement('parent.delmain.setProgressText("pb1","' . sprintf(g_l('delete', '[delete_entry]'), $p) . '");parent.delmain.setProgress(' . $percent . ');');
	}

	function finish(){
		$alert = (($_SESSION['weS']['fragDel']['we_not_deleted_entries']) ?
				we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[folder_not_empty]'), implode("\n", $_SESSION['weS']['fragDel']['we_not_deleted_entries']) . "\n"), we_message_reporting::WE_MESSAGE_ERROR) :
				we_message_reporting::getShowMessageCall(g_l('alert', '[delete_ok]'), we_message_reporting::WE_MESSAGE_NOTICE));
		echo we_html_element::jsElement($alert . (($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE && $_SESSION['weS']['fragDel']['we_go_seem_start']) ? 'top.opener.top.we_cmd("start_multi_editor");' : '') . 'top.close();');
		unset($_SESSION['weS']['fragDel']);
	}

}
