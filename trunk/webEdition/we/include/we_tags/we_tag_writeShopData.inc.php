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
we_base_moduleInfo::isActive(we_base_moduleInfo::SHOP);

/**
 * This function writes the shop data (order) to the database
 *
 * @param          $attribs array
 *
 * @return         void
 */
function we_tag_writeShopData(array $attribs){

	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	if(($foo = attributFehltError($attribs, 'pricename', __FUNCTION__))){
		return $foo;
	}
	if(!$name){
		if(($foo = attributFehltError($attribs, 'shopname', __FUNCTION__))){
			return $foo;
		}
	}

	$shopname = weTag_getAttribute('shopname', $attribs, '', we_base_request::STRING)? : $name;
	$pricename = weTag_getAttribute('pricename', $attribs, '', we_base_request::STRING);
	$shipping = weTag_getAttribute('shipping', $attribs, '', we_base_request::FLOAT);
	$shippingIsNet = weTag_getAttribute('shippingisnet', $attribs, false, we_base_request::BOOL);
	$shippingVatRate = weTag_getAttribute('shippingvatrate', $attribs, 0, we_base_request::FLOAT);
	$netprices = weTag_getAttribute('netprices', $attribs, true, we_base_request::BOOL);
	$useVat = weTag_getAttribute('usevat', $attribs, false, we_base_request::BOOL);

	$customer = (isset($_SESSION['webuser']) ? $_SESSION['webuser'] : false);
	unset($customer['Password'], $customer['_Password']);

	if($useVat){
		$weShopVatRule = we_shop_vatRule::getShopVatRule();
		$calcVat = $weShopVatRule->executeVatRule($customer);
	}

	// Check for Shop being set
	if(!isset($GLOBALS[$shopname])){
		return;
	}
	$basket = $GLOBALS[$shopname];
	$shoppingItems = $basket->getShoppingItems();
	$cartFields = $basket->getCartFields();

	if(empty($shoppingItems)){
		return;
	}

	$DB_WE = $GLOBALS['DB_WE'];

	$DB_WE->lock(array(
		SHOP_TABLE => 'write',
		ERROR_LOG_TABLE => 'write',
		WE_SHOP_VAT_TABLE => 'read',
		CATEGORY_TABLE => 'read',
		SETTINGS_TABLE => 'read'
	));
	$orderID = intval(f('SELECT MAX(IntOrderID) FROM ' . SHOP_TABLE, '', $DB_WE)) + 1;

	$totPrice = 0;
	$articleCount = 0;
	$first = false;

	foreach($shoppingItems as $shoppingItem){
		$preis = we_base_util::std_numberformat((isset($shoppingItem['serial']['we_' . $pricename])) ? $shoppingItem['serial']['we_' . $pricename] : $shoppingItem['serial'][$pricename]);
		$totPrice += $preis * $shoppingItem['quantity'];

		// foreach article we must determine the correct tax-rate
		if(we_shop_category::isCategoryMode()){
			$wedocCategory = ((isset($shoppingItem['serial']['we_wedoc_Category'])) ? $shoppingItem['serial']['we_wedoc_Category'] : $shoppingItem['serial']['wedoc_Category']);
			$billingCountry = we_shop_category::getCountryFromCustomer(false, $_SESSION['webuser']);
			$catId = !empty($shoppingItem['serial'][WE_SHOP_CATEGORY_FIELD_NAME]) ? $shoppingItem['serial'][WE_SHOP_CATEGORY_FIELD_NAME] : 0;

			$shopVat = we_shop_category::getShopVatByIdAndCountry($catId, $wedocCategory, $billingCountry, true);
			$shopCategory = we_shop_category::getShopCatFieldByID($catId, $wedocCategory, 'ID');
		} else {
			$vatId = isset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME]) ? $shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME] : 0;
			$shopVat = we_shop_vats::getVatRateForSite($vatId, true, false);
			$shopCategory = 0;
		}

		if($shopVat !== false){ // has selected or standard shop rate
			$shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME] = $shopVat;
		} else { // could not find any shoprates, remove field if necessary
			if(isset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME])){
				unset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME]);
			}
		}
		$shoppingItem['serial'][WE_SHOP_CATEGORY_FIELD_NAME] = $shopCategory ? : 0;

		if(!$DB_WE->query('INSERT INTO ' . SHOP_TABLE . ' SET ' .
				we_database_base::arraySetter(array(
					'IntArticleID' => intval($shoppingItem['id']),
					'IntQuantity' => abs($shoppingItem['quantity']),
					'Price' => $preis,
					'IntOrderID' => $orderID,
					'IntCustomerID' => intval($_SESSION['webuser']['ID']),
					'DateOrder' => sql_function('NOW()'),
					'DateShipping' => 0,
					'Datepayment' => 0,
					'strSerial' => we_serialize($shoppingItem['serial'], SERIALIZE_JSON),
					'shopname' => $shopname
			)))){

			$DB_WE->unlock();
			t_e('error during write shop data contents of basket', $shoppingItems);
			echo 'Data Insert Failed';
			return;
		}

		if(!$first){
			//all critical data is set, unlock tables again
			$first = true;
			$DB_WE->unlock();
		}
		$articleCount++;
	}
	$basket->setOrderID($orderID);

	// second part: add cart fields to table order.
	//{
	// add shopcartfields to table
	$weShippingControl = we_shop_shippingControl::getShippingControl();

	$cartField = array(
		WE_SHOP_CART_CUSTOM_FIELD => $cartFields, // add custom cart fields to article
		WE_SHOP_PRICE_IS_NET_NAME => $netprices, // add netprice flag to article
		WE_SHOP_CART_CUSTOMER_FIELD => $customer, // add netprice flag to article
		WE_SHOP_PRICENAME => $pricename,
		WE_SHOP_SHIPPING => ($shipping === '' ?
			array(
			'costs' => $weShippingControl->getShippingCostByOrderValue($totPrice, $customer),
			'isNet' => $weShippingControl->isNet,
			'vatRate' => $weShippingControl->vatRate
			) :
			array(
			'costs' => floatval(str_replace(',', '.', $shipping)),
			'isNet' => $shippingIsNet,
			'vatRate' => $shippingVatRate
			)),
	);


	if($useVat){
		$cartField[WE_SHOP_CALC_VAT] = $calcVat; // add flag to shop, if vats shall be used
	}

	if(!$DB_WE->query('UPDATE ' . SHOP_TABLE . ' SET strSerialOrder="' . $DB_WE->escape(we_serialize($cartField, SERIALIZE_JSON)) . '" WHERE intOrderID=' . intval($orderID))){
		return;
	}
	//}
	$doc = we_getDocForTag('top');
	$lang = substr($doc->Language, 0, 2);
	$weShopStatusMails = we_shop_statusMails::getShopStatusMails();
	$weShopStatusMails->checkAutoMailAndSend('Order', $orderID, $customer, $lang);
}
