<?php
/**
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_modules/shop/we_conf_shop.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_classes/we_util.inc.php');

/**
 * This function writes the shop data (order) to the database
 *
 * @param          $attribs array
 *
 * @return         void
 */
function we_tag_writeShopData($attribs,$content) {
	global $DB_WE;

	$name = we_getTagAttribute('name',$attribs);
	if(($foo = attributFehltError($attribs,'pricename','writeShopData'))){
		return $foo;
	}
	if(!$name){
		if(($foo = attributFehltError($attribs,'shopname','writeShopData'))){
			return $foo;
		}
	}

	$shopname = we_getTagAttribute('shopname',$attribs);
	$shopname = $shopname ? $shopname : $name;
	$pricename = we_getTagAttribute('pricename',$attribs);
	$shipping = we_getTagAttribute('shipping',$attribs);
	$shippingIsNet = we_getTagAttribute('shippingisnet',$attribs);
	$shippingVatRate = we_getTagAttribute('shippingvatrate',$attribs);


	$netprices = we_getTagAttribute('netprices',$attribs,'true', true, true);

	$useVat = we_getTagAttribute('usevat',$attribs,'true', true);

	if (isset($_SESSION['webuser'])) {
		$_customer = $_SESSION['webuser'];
	} else {
		$_customer = false;
	}

	if ($useVat) {
		require_once(WE_SHOP_MODULE_DIR . 'weShopVatRule.class.php');

		$weShopVatRule = weShopVatRule::getShopVatRule();
		$calcVat = $weShopVatRule->executeVatRule($_customer);
	}
	require_once(WE_SHOP_MODULE_DIR . 'weShopVats.class.php');


	// Check for Shop being set
	if (isset($GLOBALS[$shopname])) {

		$basket = $GLOBALS[$shopname];

		$shoppingItems = $basket->getShoppingItems();
		$cartFields = $basket->getCartFields();

		if (sizeof($shoppingItems) == 0) {
			return;
		}

		$DB_WE = !isset($GLOBALS['DB_WE']) ? new DB_WE : $GLOBALS['DB_WE'];

		$DB_WE->lock(array(SHOP_TABLE=>'write',ERROR_LOG_TABLE=>'write',WE_SHOP_VAT_TABLE=>'read'));
		$orderID = abs(f("SELECT MAX(IntOrderID) AS max FROM " . SHOP_TABLE,'max',$DB_WE))+1;

		$totPrice = 0;

		if(defined("WE_ECONDA_STAT") && defined("WE_ECONDA_PATH") && WE_ECONDA_STAT  && WE_ECONDA_PATH !="" && !$GLOBALS["we_doc"]->InWebEdition){
			$_GLOBALS['weEconda'] = array('emosBasket'=>"");
			$GLOBALS['weEconda']  = array('emosBilling'=>"");
		}
		$articleCount = 0;
		foreach ($shoppingItems as $shoppingItem) {

			$preis = ((isset($shoppingItem['serial']["we_".$pricename])) ? $shoppingItem['serial']["we_".$pricename] : $shoppingItem['serial'][$pricename]);

			$preis = we_util::std_numberformat($preis);

			$totPrice += $preis * $shoppingItem['quantity'];

			$additionalFields = array();

			// add shopcartfields to table
			$cartField[WE_SHOP_CART_CUSTOM_FIELD] = $cartFields; // add custom cart fields to article
			$cartField[WE_SHOP_PRICE_IS_NET_NAME] = $netprices; // add netprice flag to article

			if ($useVat) {
				$cartField[WE_SHOP_CALC_VAT] = $calcVat; // add flag to shop, if vats shall be used
			}

			// foreach article we must determine the correct tax-rate
			$vatId = isset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME]) ? $shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME] : 0;
			$shopVat = weShopVats::getVatRateForSite($vatId, true, false);
			if ($shopVat) { // has selected or standard shop rate
				$shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME] = $shopVat;
			} else { // could not find any shoprates, remove field if necessary
				if (isset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME])) {
					unset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME]);
				}
			}

			$sql = "INSERT INTO " . SHOP_TABLE . " (intOrderID, IntArticleID, IntQuantity, Price, IntCustomerID, DateOrder, DateShipping, DatePayment, strSerial) ";
			$sql .= "VALUES (" . $orderID . ", " . abs($shoppingItem['id']) . ", '" . abs($shoppingItem['quantity']) . "', '".$DB_WE->escape($preis)."' , " . abs($_SESSION["webuser"]["ID"]) . ", now(), '00000000000000', '00000000000000', '" . $DB_WE->escape(serialize($shoppingItem['serial'])) . "')";

			if (!$DB_WE->query($sql)) {
				echo "Data Insert Failed";
				return;
			}

			if (isset($_GLOBALS['weEconda'])){
				$_GLOBALS['weEconda']['emosBasket'] .= "
if(typeof emosBasketPageArray == 'undefined') var emosBasketPageArray = new Array();
emosBasketPageArray[$articleCount] = new Array();
emosBasketPageArray[$articleCount][0]='" . $shoppingItem['id'] . "';
emosBasketPageArray[$articleCount][1]='" . rawurlencode($shoppingItem['serial']['shoptitle']) . "';
emosBasketPageArray[$articleCount][2]='$preis';
emosBasketPageArray[$articleCount][3]='';
emosBasketPageArray[$articleCount][4]='".$shoppingItem['quantity']."';
emosBasketPageArray[$articleCount][5]='NULL';
emosBasketPageArray[$articleCount][6]='NULL';
emosBasketPageArray[$articleCount][7]='NULL';
";
			}
			$articleCount++;
		}
		//all critical data is set, unlock tables again
		$DB_WE->unlock();
		$basket->setOrderID($orderID);
		
		// second part: add cart fields to table order.
		//{
			// add shopcartfields to table
			$cartField[WE_SHOP_CART_CUSTOM_FIELD] = $cartFields; // add custom cart fields to article
			$cartField[WE_SHOP_PRICE_IS_NET_NAME] = $netprices; // add netprice flag to article
			$cartField[WE_SHOP_CART_CUSTOMER_FIELD] = $_customer; // add netprice flag to article

			require_once(WE_SHOP_MODULE_DIR . 'weShippingControl.class.php');
			$weShippingControl = weShippingControl::getShippingControl();

			if ($shipping==''){
			$cartField[WE_SHOP_SHIPPING] = array(
				'costs'   => $weShippingControl->getShippingCostByOrderValue($totPrice, $_customer),
				'isNet'   => $weShippingControl->isNet,
				'vatRate' => $weShippingControl->vatRate
			);
			} else {
				$cartField[WE_SHOP_SHIPPING] = array(
					'costs'   => $shipping,
					'isNet'   => $shippingIsNet,
					'vatRate' => $shippingVatRate
			    );

			}

			if ($useVat) {
				$cartField[WE_SHOP_CALC_VAT] = $calcVat; // add flag to shop, if vats shall be used
			}

			$cartSql = '
				UPDATE ' . SHOP_TABLE . '
				set strSerialOrder=\'' . $DB_WE->escape(serialize($cartField)) . '\'
				WHERE intOrderID="' . $orderID . '"
			';

			if (!$DB_WE->query($cartSql)) {
				echo "Data Insert Failed";
				return;
			}
		//}
		if (isset($_GLOBALS['weEconda'])){
			$GLOBALS['weEconda']['emosBilling'] .= "
if(typeof emosBillingPageArray == 'undefined') var emosBillingPageArray = new Array();
emosBillingPageArray [0]='".$orderID."';
emosBillingPageArray [1]='".md5($_SESSION["webuser"]["ID"])."';
emosBillingPageArray [2]='".rawurlencode($_SESSION["webuser"]["Contact_Country"])."/".rawurlencode($_SESSION["webuser"]["Contact_Address2"])."/".rawurlencode($_SESSION["webuser"]["Contact_Address1"])."';
emosBillingPageArray [3]='".$totPrice."';
			";
		}
		$doc = we_getDocForTag('top');
		$lang=substr($doc->Language,0,2);
		require_once(WE_SHOP_MODULE_DIR . 'weShopStatusMails.class.php');
		$weShopStatusMails = weShopStatusMails::getShopStatusMails();
		$weShopStatusMails->checkAutoMailAndSend('Order',$orderID,$_customer,$lang);
	}

	return;
}
