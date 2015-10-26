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
require_once(WE_INCLUDES_PATH . 'we_tools/weSearch/conf/define.conf.php');

class rpcGetMouseOverDivsCmd extends rpcCmd{

	function execute(){

		$resp = new rpcResponse();

		$whichsearch = we_base_request::_(we_base_request::STRING, 'whichsearch', '');
		$setView = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'setView' . $whichsearch);
		$anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'anzahl' . $whichsearch);
		$searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'searchstart' . $whichsearch);

		$GLOBALS['we_cmd_obj'] = $_SESSION['weSearch_session'];
		$sview = new we_search_view();

		switch($setView){
			case we_search_view::VIEW_ICONS:
				$content = $sview->searchProperties($whichsearch);

				$x = $searchstart + $anzahl;
				if($x > count($content)){
					$x = $x - ($x - count($content));
				}
				$code = we_search_view::makeMouseOverDivs($x, $content, $whichsearch);
				break;
			default:
			case we_search_view::VIEW_LIST:
				$code = "";
		}

		$resp->setData("data", $code);

		return $resp;
	}

}
