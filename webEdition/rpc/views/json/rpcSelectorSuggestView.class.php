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
class rpcSelectorSuggestView extends we_rpc_genericJSONView{

	function getResponse(we_rpc_response $response){
		header('Content-type: text/plain');
		$suggests = $response->getData('data');
		$ret = [];
		if(is_array($suggests)){
			foreach($suggests as $sug){
				$short = strrchr($sug['Path'], '/');
				$ret[] = [
					'ID' => $sug['ID'],
					'label' => ($short !== $sug['Path'] ? '/...' : '') . $short,
					'value' => $sug['Path'],
					'contenttype' => (isset($sug['ContentType']) ? $sug['ContentType'] : (isset($sug['IsFolder']) && $sug['IsFolder'] ? we_base_ContentTypes::FOLDER : ''))
				];
			}
		}
		return parent::getResponse($ret);
	}

}
