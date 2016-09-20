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
class rpcSelectorGetSelectedIdCmd extends we_rpc_cmd{

	function execute(){
		$response = new we_rpc_response();

		if(!($search = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)) || !($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', false, 2))){
			exit();
		}

		$selectorSuggest = new we_selector_query();
		$contentTypes = explode(',', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3));
		$selectorSuggest->queryTable($search, $table, $contentTypes);
		$suggests = $selectorSuggest->getResult();
		if(is_array($suggests) && isset($suggests[0]['ID'])){
			$response->setStatus(true);
			$response->setData('data', [
				'id' => we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4),
				'value' => $suggests[0]['ID'],
				'contentType' => (isset($suggests[0]['ContentType']) ? $suggests[0]['ContentType'] : '')
			]);
			return $response;
		}

		$response->setStatus(false);

		if(strpos(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3), ',')){
			switch(we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2)){
				case FILE_TABLE:
					$msg = g_l('weSelectorSuggest', '[no_document]');
					break;
				case TEMPLATES_TABLE:
					$msg = g_l('weSelectorSuggest', '[no_template]');
					break;
				case OBJECT_TABLE:
					$msg = g_l('weSelectorSuggest', '[no_class]');
					break;
				case OBJECT_FILES_TABLE:
					$msg = g_l('weSelectorSuggest', '[no_class]');
					break;
				default:
					$msg = g_l('weSelectorSuggest', '[no_result]');
					break;
			}
		} else {
			$msg = g_l('weSelectorSuggest', '[no_folder]');
		}
		$response->setData('data', [
			'msg' => $msg,
			'nr' => we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2)
		]);

		return $response;
	}

}
