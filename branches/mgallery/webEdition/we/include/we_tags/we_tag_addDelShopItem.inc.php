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
function we_tag_addDelShopItem($attribs){
	if(!defined('SHOP_TABLE')){
		echo modulFehltError('Shop', __FUNCTION__);
		return;
	}
	if(($foo = attributFehltError($attribs, 'shopname', __FUNCTION__))){
		return $foo;
	}

	$shopname = weTag_getAttribute('shopname', $attribs);
	$floatquantities = weTag_getAttribute('floatquantities', $attribs, false, true);

	we_base_moduleInfo::isActive('shop');

	$floatfilter = new Zend_Filter_LocalizedToNormalized();//FIXME: no local set, this won't work if server settings not correct or not match document-settings
	if((isset($_REQUEST['shopname']) && $_REQUEST['shopname'] == $shopname) || !isset($_REQUEST['shopname']) || !($_REQUEST['shopname'])){
		if(isset($_REQUEST['shop_cart_id']) && is_array($_REQUEST['shop_cart_id'])){
			if(we_base_request::_(we_base_request::INT, 't', 0) > (isset($_SESSION['tb']) ? $_SESSION['tb'] : 0 )){
				foreach($_REQUEST['shop_cart_id'] as $cart_id => $cart_amount){
					$GLOBALS[$shopname]->Set_Cart_Item(filterXss($cart_id), $floatquantities ? $floatfilter->filter($cart_amount) : intval($cart_amount));
					$_SESSION[$shopname . '_save'] = $GLOBALS[$shopname]->getCartProperties();
				}
			}
		} else if(isset($_REQUEST['shop_anzahl_und_id']) && is_array($_REQUEST['shop_anzahl_und_id'])){
			if(we_base_request::_(we_base_request::INT, 't', 0) > (isset($_SESSION['tb']) ? $_SESSION['tb'] : 0 )){
				$shop_articleid_variant = $shop_anzahl = '';
				foreach($_REQUEST['shop_anzahl_und_id'] as $shop_articleid_variant => $shop_anzahl){
					$articleInfo = explode('_', filterXss($shop_articleid_variant));
					$GLOBALS[$shopname]->Set_Item(intval($articleInfo[0]), ($floatquantities ? $floatfilter->filter($shop_anzahl) : intval($shop_anzahl)), $articleInfo[1], (isset($articleInfo[2]) ? $articleInfo[2] : ''));
					$_SESSION[$shopname . '_save'] = $GLOBALS[$shopname]->getCartProperties();
					unset($articleInfo);
				}
				$_SESSION['tb'] = we_base_request::_(we_base_request::INT, 't', 0);
			}
		} else if(($artID = we_base_request::_(we_base_request::INT, 'shop_artikelid')) && isset($_REQUEST['shop_anzahl']) && $_REQUEST['shop_anzahl'] != 0){
			if(we_base_request::_(we_base_request::INT, 't', 0) > (isset($_SESSION['tb']) ? $_SESSION['tb'] : 0)){
				$GLOBALS[$shopname]->Add_Item($artID, ($floatquantities ? $floatfilter->filter($_REQUEST['shop_anzahl']) : we_base_request::_(we_base_request::INT, 'shop_anzahl', 0)), filterXss($_REQUEST['type']), we_base_request::_(we_base_request::RAW, WE_SHOP_VARIANT_REQUEST, ''), ( ( isset($_REQUEST[WE_SHOP_ARTICLE_CUSTOM_FIELD]) && is_array($_REQUEST[WE_SHOP_ARTICLE_CUSTOM_FIELD]) ) ? filterXss($_REQUEST[WE_SHOP_ARTICLE_CUSTOM_FIELD]) : array()));
				$_SESSION[$shopname . '_save'] = $GLOBALS[$shopname]->getCartProperties();
				$_SESSION['tb'] = we_base_request::_(we_base_request::INT, 't');
			}
		} else if(($artID = we_base_request::_(we_base_request::INT, 'del_shop_artikelid'))){
			if(we_base_request::_(we_base_request::INT, 't', 0) > (isset($_SESSION['tb']) ? $_SESSION['tb'] : 0 )){
				$GLOBALS[$shopname]->Del_Item($artID, filterXss($_REQUEST['type']), (isset($_REQUEST[WE_SHOP_VARIANT_REQUEST]) ? filterXss($_REQUEST[WE_SHOP_VARIANT_REQUEST]) : ''), ( ( isset($_REQUEST[WE_SHOP_ARTICLE_CUSTOM_FIELD]) && is_array($_REQUEST[WE_SHOP_ARTICLE_CUSTOM_FIELD]) ) ? filterXss($_REQUEST[WE_SHOP_ARTICLE_CUSTOM_FIELD]) : array()));
				$_SESSION[$shopname . '_save'] = $GLOBALS[$shopname]->getCartProperties();
				$_SESSION['tb'] = we_base_request::_(we_base_request::INT, 't');
			}
		}
	}
}
