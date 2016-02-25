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
class rpcGetItemsFromDBCmd extends we_rpc_cmd{

	function execute(){
		$resp = new we_rpc_response();

		$IDs = we_base_request::_(we_base_request::INTLISTA, 'we_cmd', array(), 'id');
		if(empty($IDs)){
			$resp->setData("error", array("Missing field id"));
			return $resp;
		}

		$collection = new we_collection();
		$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 'transaction');
		if($transaction && $we_dt = isset($_SESSION['weS']['we_data'][$transaction]) ? $_SESSION['weS']['we_data'][$transaction] : ''){
			$collection->we_initSessDat($we_dt);
		} else if(($collectionID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'collection'))){
			$collection->initByID($collectionID);
		} else {
			$resp->setData("error", array("no collection error"));
			return $resp;
		}

		$full = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'full');
		$recursive = we_base_request::_(we_base_request::BOOL, 'we_cmd', true, 'recursive');
		$resp->setData("itemsArray", $collection->getValidItemsFromIDs($IDs, $full, $recursive));

		return $resp;
	}

}