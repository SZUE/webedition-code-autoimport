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
function we_tag_shopVat($attribs){
	
	if(!we_shop_category::isCategoryMode()){
		$name = WE_SHOP_VAT_FIELD_NAME;

		// in webEdition - EditMode
		$allVats = we_shop_vats::getAllShopVATs();
		$values = array();

		$standardVal = '';
		$standardId = 0;

		foreach($allVats as $id => $shopVat){
			$values[$id] = $shopVat->vat . ' - ' . $shopVat->getNaturalizedText() . ' (' . $shopVat->territory . ')';
			if($shopVat->standard){

				$standardId = $id;
				$standardVal = $shopVat->vat;
			}
		}

		$attribs['name'] = WE_SHOP_VAT_FIELD_NAME;
		$val = oldHtmlspecialchars(isset($GLOBALS['we_doc']->elements[$name]['dat']) ? $GLOBALS['we_doc']->getElement($name) : $standardId);

		// use a defined name for this
		if($GLOBALS['we_editmode']){

			/* 		switch($type){
			  default: */
			$fieldname = 'we_' . $GLOBALS['we_doc']->Name . '_attrib[' . $name . ']';
			return $GLOBALS['we_doc']->htmlSelect($fieldname, $values, 1, $val);
			//}
		}
		return (isset($allVats[$val]) ? $allVats[$val]->vat : $standardVal);
	} else {
		$field = weTag_getAttribute('field', $attribs, 'vat', we_base_request::STRING);
		//$id = weTag_getAttribute('id', $attribs, false, we_base_request::INT);
		$shopcategoryid = weTag_getAttribute('shopcategoryid', $attribs, false, we_base_request::INT);
		$wedoccategories = weTag_getAttribute('wedoccategories', $attribs, '', we_base_request::STRING);
		$customerid = weTag_getAttribute('customerid', $attribs, false, we_base_request::INT);
		$country = weTag_getAttribute('country', $attribs, false, we_base_request::STRING);
		$iso = $field === 'country_iso';

		$fieldMap = array(
			'id' => 'id',
			'vat' => 'vat',
			'name' => 'text',
			'country' => 'territory',
			'country_iso' => 'territory',
			'is_standard' => 'standard',
			'is_fallback_to_standard' => 'is_vat_fallback_to_standard',
			'is_fallback_to_prefs' => 'is_vat_fallback_to_prefs',
			'is_country_fallback_to_prefs' => 'is_country_fallback_to_prefs'
		);
		$field = isset($fieldMap[$field]) ? $fieldMap[$field] : 'vat';

		$countryArr = we_shop_category::getCountryFromCustomer(true, null, $customerid, true);
		$countryArr['country'] = $country ? : $countryArr['country']; //country delivered as attribute has highest priority

		if($field === 'is_country_fallback_to_prefs'){
			return $countryArr['country'] && $countryArr['isFallback'] ? 1 : 0;
		}

		$weShopVatRule = we_shop_vatRule::getShopVatRule();
		if(!$weShopVatRule->executeVatRule($countryArr['customer'], $countryArr['country'])){
			return $field === 'vat' ? 0 : '';
		}

		$catId = $shopcategoryid ? : ($GLOBALS['we_doc']->getElement(WE_SHOP_CATEGORY_FIELD_NAME) ? : 0);
		$wedocCategory = $wedoccategories ? : $GLOBALS['we_doc']->Category;
		$vat = we_shop_category::getShopVatByIdAndCountry($catId, $wedocCategory, $countryArr['country'], false, ($field === 'is_vat_fallback_to_standard'), ($field === 'is_vat_fallback_to_prefs'));

		switch($field){
			case 'is_vat_fallback_to_standard':
			case 'is_vat_fallback_to_prefs':
				return $vat === true ? 1 : 0;
			case 'standard':
				return $vat->$field ? 1 : 0;
			case 'text':
				return $vat->getNaturalizedText();
			case 'territory':
				if(!$iso){
					$lang = array_search($GLOBALS['WE_LANGUAGE'], getWELangs());
					return $translation = Zend_Locale::getTranslation($vat->$field, 'territory', $lang) ? : $vat->$field;
				}
				return $vat->$field;
			default: 
				return $vat->$field;
		}
	}
}
