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
function we_tag_ifShopVat(array $attribs){
	if(!we_shop_category::isCategoryMode()){
		if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
			echo $foo;
			return false;
		}
		$id = weTag_getAttribute('id', $attribs, -1, we_base_request::INT);

		$vatId = (isset($GLOBALS['lv']) && $GLOBALS['lv']->f(WE_SHOP_VAT_FIELD_NAME) ?
						$GLOBALS['lv']->f(WE_SHOP_VAT_FIELD_NAME) :
						$GLOBALS['we_doc']->getElement(WE_SHOP_VAT_FIELD_NAME));


		if(!$vatId){
			$shopVat = we_shop_vats::getStandardShopVat();
			if($shopVat){
				$vatId = $shopVat->id;
			}
		}

		return ($id == $vatId);
	} else {
		$match = intval(weTag_getAttribute('match', $attribs, 0, we_base_request::INT));
		$field = weTag_getAttribute('field', $attribs, 'id', we_base_request::STRING);
		$lvOnly = weTag_getAttribute('lvOnly', $attribs, false, we_base_request::BOOL);

		if(isset($GLOBALS['lv'])){
			$catID = $GLOBALS['lv']->f(WE_SHOP_CATEGORY_FIELD_NAME) ? : 0;
			$wedocCategory = $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'CATEGORY') ? : '';
		} elseif(!$lvOnly) {
			$catID = $GLOBALS['we_doc']->getElement(WE_SHOP_CATEGORY_FIELD_NAME) ? : 0;
			$wedocCategory = $GLOBALS['we_doc']->Category ? : '';
		} else {
			return false;
		}
		$attribs['shopcategoryid'] = $catID;
		$attribs['wedoccategories'] = $wedocCategory;
		$vatField = we_tag('shopVat', $attribs);

		switch ($field){
			case 'id':
				return $match === 0 ? boolval($vatField) : $match === intval($vatField);
			default:
				return boolval($vatField);
		}
	}
}
