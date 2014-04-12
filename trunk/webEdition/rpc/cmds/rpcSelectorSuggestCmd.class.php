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
class rpcSelectorSuggestCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();

		if(!isset($_REQUEST['we_cmd'][1]) || !isset($_REQUEST['we_cmd'][2])){
			exit();
		}

		$selectorSuggest = new we_selector_query();
		$contentTypes = isset($_REQUEST['we_cmd'][3]) ? explode(",", $_REQUEST['we_cmd'][3]) : null;
		if(isset($_REQUEST['we_cmd'][4]) && !empty($_REQUEST['we_cmd'][4]) && isset($_REQUEST['we_cmd'][5]) && !empty($_REQUEST['we_cmd'][5])){
			if($_REQUEST['we_cmd'][2] == "tblTemplates" && $_REQUEST['we_cmd'][4] == we_base_ContentTypes::TEMPLATE){
				$selectorSuggest->addCondition(array("AND", "<>", "ID", $_REQUEST['we_cmd'][5]));
			}
		}
		$selectorSuggest->search($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2], $contentTypes, "", weRequest('raw','we_cmd','',6));
		$resp->setData("data", $selectorSuggest->getResult());

		return $resp;
	}

}
