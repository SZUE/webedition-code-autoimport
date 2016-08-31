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
class we_rpc_genericJSONView extends we_rpc_view{

	function getResponse($response){
		//always sent utf8 data as expected by json
		header('Content-Type: application/json; charset=UTF-8');
		$ret = json_encode($response, JSON_UNESCAPED_UNICODE);
		if($ret){
			return $ret;
		}

		$json = new Services_JSON(Services_JSON::SERVICES_JSON_USE_NO_CHARSET_CONVERSION);
		return utf8_encode($json->encode($response, false));
	}

}
