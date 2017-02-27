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
class rpcGetRssCmd extends we_rpc_cmd{

	function execute(){
		//close session, we don't need it anymore
		session_write_close();

		list($title, $sRssOut) = we_widget_rss::getRSSContent(we_base_request::_(we_base_request::URL, 'we_cmd', '', 0), we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::INT, 'we_cmd', '', 2), we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), we_base_request::_(we_base_request::STRING, 'we_cmd', '', 4));

		if(strlen($title) > 50){
			$title = substr($title, 0, 50) . '&hellip;';
		}

		$resp = new we_rpc_response();
		$resp->setData('data', $sRssOut);
		$resp->setData('titel', $title);
		$resp->setData('widgetType', "rss");
		$resp->setData('widgetId', we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 5));

		return $resp;
	}

}
