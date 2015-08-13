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

		if(!($search = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)) || !($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', false, 2))){
			exit();
		}

		$selectorSuggest = new we_selector_query();
		$contentTypes = explode(',', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
		$selectorSuggest->queryTable($search, $table, $contentTypes);
		$resp->setData('data', $selectorSuggest->getResult());
		return $resp;
	}

}
