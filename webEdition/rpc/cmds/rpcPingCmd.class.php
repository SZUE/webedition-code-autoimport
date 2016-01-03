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
we_base_moduleInfo::isActive(we_base_moduleInfo::USERS);

class rpcPingCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();
		we_users_user::updateActiveUser();

		if(defined('MESSAGING_SYSTEM')){
			$messaging = new we_messaging_messaging($we_transaction);
			$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
			$messaging->add_msgobj('we_message', 1);
			$messaging->add_msgobj('we_todo', 1);

			$resp->setData('newmsg_count', $messaging->used_msgobjs['we_message']->get_newmsg_count());
			$resp->setData('newtodo_count', $messaging->used_msgobjs['we_todo']->get_newmsg_count());
		}

		$users_online = new we_users_online();

		$resp->setData('users', $users_online->getUsers());
		$resp->setData('num_users', $users_online->getNumUsers());

		$aDat = we_unserialize(we_base_preferences::getUserPref('cockpit_dat')); // array as saved in the prefs
		foreach($aDat as $d){
			foreach($d as $aProps){
				if($aProps[0] === 'mfd'){
					$lastModified = include(WE_INCLUDES_PATH . 'we_widgets/mod/mfd.inc.php');
					$resp->setData('mfd_data', $lastModified);
				}
			}
		}
		return $resp;
	}

}
