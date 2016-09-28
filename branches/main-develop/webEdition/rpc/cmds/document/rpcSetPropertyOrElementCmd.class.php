<?php

/**
 * webEdition CMS
 *
 * $Rev: 12330 $
 * $Author: mokraemer $
 * $Date: 2016-06-24 20:15:32 +0200 (Fr, 24 Jun 2016) $
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
class rpcSetPropertyOrElementCmd extends we_rpc_cmd{

	function execute(){
		$resp = new we_rpc_response();

		$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'id');
		$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 'table');
		$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 'transaction');
		$name = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'name');
		$type = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'type');
		$key = we_base_request::_(we_base_request::STRING, 'we_cmd', 'dat', 'key');
		$value = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'value');

		if(!table || !(id || transaction)){
			$resp->setData('error', ['cannot load wedoc']);
			return $resp;
		}
		if(!name){
			$resp->setData('error', ['no field name defined']);
			return $resp;
		}

		switch($table){
			case FILE_TABLE:
				$GLOBALS['we_doc'] = new we_webEditionDocument();
				break;
			case OBJECT_FILES_TABLE:
				//$GLOBALS['we_doc'] = new we_objectFile;
				//break;
			case VFILE_TABLE:
				//$GLOBALS['we_doc'] = new we_collection();
				//break;
			default:
				$resp->setData('error', ['not implemented']);
				return $resp;
		}

		$isFromSessDat = false;
		if($transaction && (isset($_SESSION['weS']['we_data'][$transaction]))){
			$GLOBALS['we_doc']->we_initSessDat($_SESSION['weS']['we_data'][$transaction]);
			$isFromSessDat = true;
		} else if($id){
			$GLOBALS['we_doc']->initByID($id);
		} else {
			$resp->setData('error', ['cannot load wedoc']);
			return $resp;
		}
		$GLOBALS['we_doc']->setElement($name, $value, $type, $key);

		if($isFromSessDat ){
			$ret = $GLOBALS['we_doc']->saveInSession($_SESSION['weS']['we_data'][$transaction]);
			$resp->setData('data', $ret ? 'successfully saved to session' : 'failure when saving to session');
		} else {
			$ret = $GLOBALS['we_doc']->save(false, true); // FIXME: why is $ret = -1?
			$resp->setData('data', $ret ? 'successfully saved to db' : 'failure when saving to db');

		}

		return $resp;
	}

}
