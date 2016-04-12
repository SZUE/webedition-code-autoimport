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
class rpcChangeDocTypeView extends we_rpc_view{

	function getResponse($response){
		$_elems = "";
		$_i = 0;

		foreach($response->getData("elements") as $_element => $_property){
			$_elems .=($_i > 0 ? ", " : "") . "
		" . $_i . ':{
			elem: ' . $_element . ',
			props: {';
			$_loop = 0;
			foreach($_property as $_propertyName => $_propertyValue){
				$_elems .= ($_loop > 0 ? ", " : "") . "
				" . $_loop . ':{
					prop:"' . $_propertyName . '",
					val: "' . $_propertyValue . '"
				}';
				$_loop++;
			}
			$_elems .= '
			}
		}';
			$_i++;
		}

		$json = <<<HTS1
{
	elems: { $_elems
	}
}
HTS1;
		return $json;
	}

}
