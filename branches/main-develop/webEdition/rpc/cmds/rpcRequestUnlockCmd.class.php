<?php

/**
 * webEdition CMS
 *
 * $Rev: 13615 $
 * $Author: mokraemer $
 * $Date: 2017-03-20 18:04:19 +0100 (Mo, 20. Mär 2017) $
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
class rpcRequestUnlockCmd extends we_rpc_cmd{

	function execute(){
		$resp = new we_rpc_response();
		$docID = we_base_request::_(we_base_request::INT, 'doc');
		$table = we_base_request::_(we_base_request::TABLE, 'table');
		switch(we_base_request::_(we_base_request::STRING, 'type')){
			case 'request':
				$force = we_base_permission::hasPerm('ADMINISTRATOR') ? we_base_request::_(we_base_request::INT, 'time') : 0;
				$GLOBALS['DB_WE']->query('UPDATE ' . LOCK_TABLE . ' SET ' .
					we_database_base::arraySetter([
						'releaseRequestID' => $_SESSION['user']['ID'],
						'releaseRequestText' => we_base_request::_(we_base_request::STRING, 'text'),
						'releaseRequestForce' => sql_function($force ? 'NOW()+INTERVAL ' . $force . ' MINUTE' : 'NULL')
					]) .
					' WHERE UserID!=' . intval($_SESSION['user']['ID']) . ' AND tbl="' . $table . '" AND ID=' . $docID);
				break;
		}
		return $resp;
	}

}
