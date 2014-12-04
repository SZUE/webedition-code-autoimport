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
require_once (WE_INCLUDES_PATH . 'we_tags/we_tag_category.inc.php');

function we_tag_shopCategory($attribs){
	$id = weTag_getAttribute('id', $attribs, false, we_base_request::INT);
	$fromdoc = weTag_getAttribute('fromdoc', $attribs, false, we_base_request::BOOL);
	$showpath = weTag_getAttribute('showpath', $attribs, false, we_base_request::BOOL);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '/', we_base_request::FILE);
	$onlyindir = we_shop_category::getShopCatsDir(true);
	$show = weTag_getAttribute('show', $attribs, 'category', we_base_request::STRING);
	$field = weTag_getAttribute(($show === 'vat' ? 'vatfield' : 'catfield'), $attribs, '', we_base_request::STRING);
	$customerid = weTag_getAttribute('customerid', $attribs, false, we_base_request::INT);
	$country = weTag_getAttribute('country', $attribs, false, we_base_request::STRING);
	$dosave = weTag_getAttribute('dosave', $attribs, true, we_base_request::BOOL);

	$attribs['onlyindir'] = $onlyindir;
	$attribs['fromTag'] = 'shopcategory';

	if($GLOBALS['we_editmode'] && !($id || $fromdoc) && $show !== 'vat'){
		$attribs['_name_orig'] = WE_SHOP_CATEGORY_FIELD_NAME;
		$attribs['field'] = 'PATH';
		$attribs['showpath'] = true;

		return we_tag_category($attribs);
	}

	$ret = '';
	if($GLOBALS['we_editmode']){
		$attribs['field'] = 'ID';
		$catIDs = explode(',', trim(we_tag_category($attribs), ','));
		if(count($catIDs) && !$show === 'vat' && $dosave){
			$ret .= we_html_element::htmlHidden(array('name' => 'we_' . $GLOBALS['we_doc']->Name . '_category[we_shopCategory]', 'value' => $catIDs[0]));
		}
	}

	if($show === 'vat'){
		$countryArr = we_shop_category::getCountryFromCustomer(true, null, $customerid, true);
		$countryArr['country'] = $country ? : $countryArr['country']; //country delivered as attribute has highest priority
		if($field === 'is_prefs_country'){
			return $countryArr['country'] && $countryArr['isFallback'] ? 1 : 0;
		}

		$weShopVatRule = we_shop_vatRule::getShopVatRule();
		if(!$weShopVatRule->executeVatRule($countryArr['customer'], $countryArr['country'])){
			return $field === 'vat' ? 0 : '';
		}

		$catId = $id ? : $GLOBALS['we_doc']->getElement(WE_SHOP_CATEGORY_FIELD_NAME);
		$vat = we_shop_category::getVatByIdAndCountry($catId, $countryArr['country'], false, ($field === 'is_default_vat'), ($field === 'is_prefs_rate'));

		switch($field){
			case 'is_default_vat':
			case 'is_prefs_rate':
				$ret .= $vat === true ? 1 : 0;
				break;
			default: 
				$ret = $vat->$field;
		}
	} else {
		$ret .= we_shop_category::getFieldFromIDs($GLOBALS['we_doc']->getElement(WE_SHOP_CATEGORY_FIELD_NAME), $field, false, 0, $onlyindir, false, false, ',', $showpath, $rootdir);
	}

	return $ret;
}
