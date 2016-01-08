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
function we_tag_ifField(array $attribs){
	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);

	switch($type){
		case 'shopCategory':
			$attribs['lvOnly'] = true;
			return we_shop_category::isCategoryMode() ? we_tag('ifShopCategory', $attribs) : false;
		case 'shopVat':
			if(we_shop_category::isCategoryMode()){
				$attribs['lvOnly'] = true;
				return we_tag('ifShopVat', $attribs);
			}
			break;
		case 'float':
		case 'int':
			$attribs['type'] = 'text';//Bug #4815
	}

	if(($foo = attributFehltError($attribs, array('name' => false, 'match' => true), __FUNCTION__))){
		print($foo);
		return false;
	}
	$attribs['name'] = $attribs['_name_orig'];

	$realvalue = we_tag('field', $attribs);
	$match = weTag_getAttribute('match', $attribs, '', we_base_request::STRING);

	switch(weTag_getAttribute('operator', $attribs, 'equal', we_base_request::STRING)){
		default:
		case 'equal':
			return $realvalue == $match;
		case 'less':
			return intval($realvalue) < intval($match);
		case 'less|equal':
			return intval($realvalue) <= intval($match);
		case 'greater':
			return intval($realvalue) > intval($match);
		case 'greater|equal':
			return intval($realvalue) >= intval($match);
		case 'contains':
			return (strpos($realvalue, $match) !== false);
		case 'isin':
			return (strpos(',' . $match . ',', ',' . $realvalue . ',') !== false);
	}
}
