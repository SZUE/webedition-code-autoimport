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
 * @package    webEdition_rpc
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

class rpcPingCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();

		if($_SESSION["user"]["ID"]){
			$GLOBALS['DB_WE']->query('UPDATE ' . USER_TABLE . ' SET Ping=UNIX_TIMESTAMP(NOW()) WHERE ID=' . intval($_SESSION["user"]["ID"]));
			$GLOBALS['DB_WE']->query('UPDATE ' . LOCK_TABLE . ' SET lockTime=DATE_ADD( NOW( ) , INTERVAL ' . (PING_TIME + PING_TOLERANZ) . ' SECOND) WHERE UserID=' . intval($_SESSION["user"]["ID"]) . ' AND sessionID="' . session_id() . '"');
		}

		if(defined("MESSAGING_SYSTEM")){
			include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_session.inc.php");
			$messaging = new we_messaging($we_transaction);
			$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
			$messaging->add_msgobj('we_message', 1);
			$messaging->add_msgobj('we_todo', 1);

			$resp->setData("newmsg_count", $messaging->used_msgobjs['we_message']->get_newmsg_count());
			$resp->setData("newtodo_count", $messaging->used_msgobjs['we_todo']->get_newmsg_count());
		}

		$users_online = new usersOnline();

		$resp->setData("users", $users_online->getUsers());
		$resp->setData("num_users", $users_online->getNumUsers());
		return $resp;
	}
}