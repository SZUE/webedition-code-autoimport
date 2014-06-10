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
require_once(WE_MODULES_PATH . 'shop/we_conf_shop.inc.php');

/**
 * This function writes the shop data (order) to the database
 *
 * @param          $attribs array
 *
 * @return         void
 */
function we_tag_writeShopData($attribs){

	$name = weTag_getAttribute('name', $attribs);
	if(($foo = attributFehltError($attribs, 'pricename', __FUNCTION__))){
		return $foo;
	}
	if(!$name){
		if(($foo = attributFehltError($attribs, 'shopname', __FUNCTION__))){
			return $foo;
		}
	}

	$shopname = weTag_getAttribute('shopname', $attribs);
	$shopname = $shopname ? $shopname : $name;
	$pricename = weTag_getAttribute('pricename', $attribs);
	$shipping = floatval(str_replace(',', '.', weTag_getAttribute('shipping', $attribs)));
	$shippingIsNet = weTag_getAttribute('shippingisnet', $attribs, false, true);
	$shippingVatRate = weTag_getAttribute('shippingvatrate', $attribs);
	$netprices = weTag_getAttribute('netprices', $attribs, true, true);
	$useVat = weTag_getAttribute('usevat', $attribs, false, true);

	$_customer = (isset($_SESSION['webuser']) ? $_SESSION['webuser'] : false);

	if($useVat){
		$weShopVatRule = we_shop_vatRule::getShopVatRule();
		$calcVat = $weShopVatRule->executeVatRule($_customer);
	}


	// Check for Shop being set
	if(isset($GLOBALS[$shopname])){

		$basket = $GLOBALS[$shopname];

		$shoppingItems = $basket->getShoppingItems();
		$cartFields = $basket->getCartFields();

		if(empty($shoppingItems)){
			return;
		}

		$DB_WE = $GLOBALS['DB_WE'];

		$DB_WE->lock(array(SHOP_TABLE => 'write', ERROR_LOG_TABLE => 'write', WE_SHOP_VAT_TABLE => 'read'));
		$orderID = intval(f('SELECT MAX(IntOrderID) AS max FROM ' . SHOP_TABLE, 'max', $DB_WE)) + 1;

		$totPrice = 0;

		$articleCount = 0;

		foreach($shoppingItems as $shoppingItem){
			$preis = ((isset($shoppingItem['serial']['we_' . $pricename])) ? $shoppingItem['serial']['we_' . $pricename] : $shoppingItem['serial'][$pricename]);

			$preis = we_base_util::std_numberformat($preis);

			$totPrice += $preis * $shoppingItem['quantity'];


			// foreach article we must determine the correct tax-rate
			$vatId = isset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME]) ? $shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME] : 0;
			$shopVat = we_shop_vats::getVatRateForSite($vatId, true, false);
			if($shopVat){ // has selected or standard shop rate
				$shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME] = $shopVat;
			} else { // could not find any shoprates, remove field if necessary
				if(isset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME])){
					unset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME]);
				}
			}

			if(!$DB_WE->query('INSERT INTO ' . SHOP_TABLE . ' SET ' .
					we_database_base::arraySetter((array(
						'IntArticleID' => intval($shoppingItem['id']),
						'IntQuantity' => abs($shoppingItem['quantity']),
						'Price' => $preis,
						'IntOrderID' => $orderID,
						'IntCustomerID' => intval($_SESSION['webuser']['ID']),
						'DateOrder' => sql_function('NOW()'),
						'DateShipping' => 0,
						'Datepayment' => 0,
						'strSerial' => serialize($shoppingItem['serial']),
				))))){

				echo 'Data Insert Failed';
				return;
			}

			if(isset($GLOBALS['weEconda'])){
				$GLOBALS['weEconda']['emosBasket'] .= "
                    if(typeof emosBasketPageArray == 'undefined') var emosBasketPageArray = new Array();
                    emosBasketPageArray[$articleCount] = new Array();
                    emosBasketPageArray[$articleCount][0]='" . $shoppingItem['id'] . "';
                    emosBasketPageArray[$articleCount][1]='" . rawurlencode($shoppingItem['serial'][WE_SHOP_TITLE_FIELD_NAME]) . "';
                    emosBasketPageArray[$articleCount][2]='$preis';
                    emosBasketPageArray[$articleCount][3]='';
                    emosBasketPageArray[$articleCount][4]='" . $shoppingItem['quantity'] . "';
                    emosBasketPageArray[$articleCount][5]='NULL';
                    emosBasketPageArray[$articleCount][6]='NULL';
                    emosBasketPageArray[$articleCount][7]='NULL';";
			}
			$articleCount++;
		}
		//all critical data is set, unlock tables again
		$DB_WE->unlock();
		$basket->setOrderID($orderID);

		// second part: add cart fields to table order.
		//{
		// add shopcartfields to table
		$weShippingControl = we_shop_shippingControl::getShippingControl();

		$cartField = array(
			WE_SHOP_CART_CUSTOM_FIELD => $cartFields, // add custom cart fields to article
			WE_SHOP_PRICE_IS_NET_NAME => $netprices, // add netprice flag to article
			WE_SHOP_CART_CUSTOMER_FIELD => $_customer, // add netprice flag to article
			WE_SHOP_PRICENAME => $pricename,
			WE_SHOP_SHIPPING => ($shipping == '' ?
				array(
				'costs' => $weShippingControl->getShippingCostByOrderValue($totPrice, $_customer),
				'isNet' => $weShippingControl->isNet,
				'vatRate' => $weShippingControl->vatRate
				) :
				array(
				'costs' => $shipping,
				'isNet' => $shippingIsNet,
				'vatRate' => $shippingVatRate
				)),
		);


		if($useVat){
			$cartField[WE_SHOP_CALC_VAT] = $calcVat; // add flag to shop, if vats shall be used
		}

		if(!$DB_WE->query('UPDATE ' . SHOP_TABLE . ' set strSerialOrder="' . $DB_WE->escape(serialize($cartField)) . '" WHERE intOrderID="' . $orderID . '"')){
			echo "Data Insert Failed";
			return;
		}
		//}
		if(isset($GLOBALS['weEconda'])){
			/*
			 * first get the prefs for country, city, address by shop default settings and shop payment settings
			 */
			$shopDefaultPrefs = @unserialize(f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLanguage"'));
			if(is_array($shopDefaultPrefs)){ // check for array
				$fieldCountry = $shopDefaultPrefs['stateField'];
				$emosBillingCountry = $_SESSION['webuser'][$fieldCountry];
			}
			$shopPaymentPrefs = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="payment_details"'));
			if(isset($shopPaymentPrefs[2]) && isset($shopPaymentPrefs[3]) && isset($shopPaymentPrefs[4])){
				$emosBillingCity = substr($_SESSION['webuser'][$shopPaymentPrefs[3]], 0, 1) . "/" . substr($_SESSION['webuser'][$shopPaymentPrefs[3]], 0, 2) . "/" . $_SESSION['webuser'][$shopPaymentPrefs[4]] . "/" . $_SESSION['webuser'][$shopPaymentPrefs[3]];
				//$emosBillingStreet = $_SESSION['webuser'][$shopPaymentPrefs[2]];
			}
			$GLOBALS['weEconda']['emosBilling'] .= "
                if(typeof emosBillingPageArray == 'undefined') var emosBillingPageArray = new Array();
                emosBillingPageArray [0]='" . $orderID . "';
                emosBillingPageArray [1]='" . md5($_SESSION["webuser"]["ID"]) . "';
                emosBillingPageArray [2]='" . rawurlencode($emosBillingCountry) . "/" . rawurlencode($emosBillingCity) . "';
                emosBillingPageArray [3]='" . $totPrice . "';
                			";
		}
		$doc = we_getDocForTag('top');
		$lang = substr($doc->Language, 0, 2);
		$weShopStatusMails = we_shop_statusMails::getShopStatusMails();
		$weShopStatusMails->checkAutoMailAndSend('Order', $orderID, $_customer, $lang);
	}

	return;
}
