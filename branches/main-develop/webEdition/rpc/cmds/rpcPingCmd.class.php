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

class rpcPingCmd extends we_rpc_cmd{

	function execute(){
		$resp = new we_rpc_response();
		$relRequest = we_users_user::updateActiveUser();
		if($relRequest){
			$resp->setData('release', $relRequest);
		}

		list($num, $usr) = we_users_online::getUsers();

		$resp->setData('users', $usr);
		$resp->setData('num_users', $num);

		$aDat = we_unserialize(we_base_preferences::getUserPref('cockpit_dat')); // array as saved in the prefs
		foreach($aDat as $d){
			if($d){
				foreach($d as $aProps){
					if($aProps[0] === 'mfd'){
						$widget = new we_widget_mfd();
						$resp->setData('mfd_data', $widget->getLastModified());
					}
				}
			}
		}
		return $resp;
	}

}
