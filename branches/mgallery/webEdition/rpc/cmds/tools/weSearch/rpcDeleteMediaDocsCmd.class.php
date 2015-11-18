<?php

/**
 * webEdition CMS
 *
 * $Rev: 7998 $
 * $Author: mokraemer $
 * $Date: 2014-07-31 13:45:21 +0200 (Do, 31 Jul 2014) $
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
class rpcDeleteMediaDocsCmd extends rpcCmd{

	function execute(){
		we_html_tools::protect();

		if(!permissionhandler::hasPerm('ADMINISTRATOR') && !permissionhandler::hasPerm('DELETE_DOCUMENT')){
			//return 'no perms';
		}

		$selectedItems = array();
		$csv = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
		if($csv){
			$allDocs = explode(",", $csv);
			foreach($allDocs as $v){
				$teile = explode("_", $v, 2);
				$selectedItems[$teile[1]][] = intval($teile[0]);
			}
		}

		$resp = new rpcResponse();
		$resp->setData("data", $this->delete($selectedItems[FILE_TABLE]), JSON_F);

		return $resp;
	}

	function delete($selectedItems = array()){
		if(!$selectedItems){
			return array('message' => g_l('alert', '[nothing_to_delete]'), 'rewriteMain' => false);
		}

		$db = $GLOBALS['DB_WE'];

		// check if docs are media documents and not protected
		$db->query('SELECT ID, ContentType, IsProtected, ParentID, Path FROM ' . FILE_TABLE . ' WHERE ID IN (' . (implode(',', $selectedItems)) . ')');
		$docsToDelete = array_filter($db->getAllFirst(true), function($var){
			return !$var[1] && in_array($var[0], array(
					we_base_ContentTypes::IMAGE,
					we_base_ContentTypes::AUDIO,
					we_base_ContentTypes::VIDEO,
					we_base_ContentTypes::FLASH,
					we_base_ContentTypes::QUICKTIME,
					we_base_ContentTypes::APPLICATION,)
				);
		});

		// check if docs are used
		if($docsToDelete){
			$db->query('SELECT remObj FROM ' . FILELINK_TABLE . ' WHERE type="media" AND remTable="' . stripTblPrefix(FILE_TABLE) . '" AND remObj IN (' . implode(',', array_keys($docsToDelete)) . ') AND position=0');
			foreach($db->getAll(true) as $v){
				unset($docsToDelete[$v]);
			}
		}

		// check owner restrictions and try to delete
		foreach($docsToDelete as $k => $v){
			if(permissionhandler::checkIfRestrictUserIsAllowed($k, FILE_TABLE, $db)){
				we_base_delete::deleteEntry($k, FILE_TABLE);
			}
		}

		if($GLOBALS['deletedItems']){
			if(defined('CUSTOMER_TABLE')){
				we_customer_documentFilter::deleteModel($GLOBALS['deletedItems'], FILE_TABLE);
			}
			we_history::deleteFromHistory($GLOBALS['deletedItems'], FILE_TABLE);

			return array('message' => g_l('alert', '[delete_ok]'), 'rewriteMain' => true, 'deletedItems' => $GLOBALS['deletedItems']);
		}
		return array('message' => g_l('alert', '[nothing_to_delete]'), 'rewriteMain' => false);
	}

}
