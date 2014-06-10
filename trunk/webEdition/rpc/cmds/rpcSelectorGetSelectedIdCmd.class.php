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
class rpcSelectorGetSelectedIdCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();

		if(!($search = weRequest('string', 'we_cmd', '', 1)) || !($table = weRequest('table', 'we_cmd', false, 2))){
			exit();
		}

		$selectorSuggest = new we_selector_query();
		$contentTypes = isset($_REQUEST['we_cmd'][3]) ? explode(",", $_REQUEST['we_cmd'][3]) : null;
		$selectorSuggest->queryTable($search, $table, $contentTypes);
		$resp->setData("data", $selectorSuggest->getResult());

		return $resp;
	}

}
