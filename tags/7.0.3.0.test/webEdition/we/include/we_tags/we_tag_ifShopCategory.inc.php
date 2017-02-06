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
function we_tag_ifShopCategory(array $attribs){
	$field = weTag_getAttribute('field', $attribs, 'id', we_base_request::STRING);
	$match = intval(weTag_getAttribute('match', $attribs, false, we_base_request::INT));
	$ignorefallbacks = weTag_getAttribute('ignorefallbacks', $attribs, false, we_base_request::BOOL);
	$lvOnly = weTag_getAttribute('lvOnly', $attribs, false, we_base_request::BOOL);

	if(isset($GLOBALS['lv'])){
		$catID = $GLOBALS['lv']->f(WE_SHOP_CATEGORY_FIELD_NAME) ? : 0;
		$wedocCategory = $GLOBALS['lv']->f('wedoc_Category') ? : '';
	} elseif(!$lvOnly) {
		$catID = $GLOBALS['we_doc']->getElement(WE_SHOP_CATEGORY_FIELD_NAME) ? : 0;
		$wedocCategory = $GLOBALS['we_doc']->Category ? : '';
	} else {
		return false;
	}

	$validArr = we_shop_category::checkGetValidID($catID, $wedocCategory, true, true);
	$validID = intval($validArr['id']);

	switch($field){
		case 'is_from doc_object':
			return boolval($validArr['state'] === we_shop_category::IS_CAT_FALLBACK_TO_WEDOCCAT) || boolval($validArr['state'] === we_shop_category::IS_CAT_FALLBACK_TO_WEDOCCAT_AND_ACTIVE);
		case 'is_fallback_to_standard':
			return boolval($validArr['state'] === we_shop_category::IS_CAT_FALLBACK_TO_STANDARD);
		case 'is_fallback_to_active':
			return boolval($validArr['state'] === we_shop_category::IS_CAT_FALLBACK_TO_ACTIVE) || boolval($validArr['state'] === we_shop_category::IS_CAT_FALLBACK_TO_WEDOCCAT_AND_ACTIVE);
		case 'is_destinationprinciple':
			return boolval(we_shop_category::getShopCatFieldByID($validID, $wedocCategory = '', 'DestPrinciple'));
		case 'id':
			if(!$match){
				return $ignorefallbacks ? boolval($catID) : boolval($validID);
			} else {
				return $ignorefallbacks ? $catID === $match : $validID === $match;
			}
		default:
			return false;
	}
}
