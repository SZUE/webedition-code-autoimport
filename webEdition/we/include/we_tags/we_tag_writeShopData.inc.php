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
	if(!$name && ($foo = attributFehltError($attribs, 'shopname', __FUNCTION__))){
		return $foo;
	}

	$shopname = weTag_getAttribute('shopname', $attribs, '', we_base_request::STRING) ?: $name;
	$pricename = weTag_getAttribute('pricename', $attribs, '', we_base_request::STRING);
	$shipping = weTag_getAttribute('shipping', $attribs, '', we_base_request::FLOAT);
	$shippingIsNet = weTag_getAttribute('shippingisnet', $attribs, false, we_base_request::BOOL);
	$shippingVatRate = weTag_getAttribute('shippingvatrate', $attribs, 0, we_base_request::FLOAT);
	$netprices = weTag_getAttribute('netprices', $attribs, true, we_base_request::BOOL);
	$useVat = weTag_getAttribute('usevat', $attribs, false, we_base_request::BOOL);
	$customPrefix = weTag_getAttribute('customPrefix', $attribs, '', we_base_request::STRING);
	$customPostfix = weTag_getAttribute('customPostfix', $attribs, '', we_base_request::STRING);

	$customer = (isset($_SESSION['webuser']) ? $_SESSION['webuser'] : false);
	unset($customer['Password'], $customer['_Password'], $customer['ID'], $customer['Username'], $customer['LoginDenied'], $customer['MemberSince'], $customer['LastLogin'], $customer['LastAccess'], $customer['AutoLoginDenied'], $customer['AutoLogin'], $customer['ModifyDate'], $customer['ModifiedBy'], $customer['Path'], $customer['Newsletter_Ok'], $customer['registered'], $customer['AutoLoginID']
	);

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

	//first insert essential order data
	$DB_WE->query('INSERT INTO ' . SHOP_ORDER_TABLE . ' SET ' . we_database_base::arraySetter([
			'shopname' => $shopname,
			'customerID' => intval($_SESSION['webuser']['ID']),
			'customerData' => we_serialize($customer, SERIALIZE_JSON, false, 5, true),
			'customFields' => $cartFields ? we_serialize($cartFields, SERIALIZE_JSON, false, 0, true) : sql_function('NULL'),
			'pricesNet' => $netprices,
			'priceName' => $pricename,
			'shippingCost' => $shipping, //we prefill this
			'shippingNet' => $shippingIsNet,
			'shippingVat' => $shippingVatRate,
			'calcVat' => $useVat ? $calcVat : 0,
	]));

	$orderID = $DB_WE->getInsertId();
	$basket->setOrderID($orderID);

	if($customPostfix || $customPrefix){ //update customOrderNo
		$DB_WE->query('UPDATE ' . SHOP_ORDER_TABLE . ' SET ' . we_database_base::arraySetter([
				'customOrderNo' => $customPrefix . $orderID . $customPostfix
			]) . ' WHERE ID=' . $orderID);
	}

	$totPrice = 0;
	$categoryMode = we_shop_category::isCategoryMode();

	foreach($shoppingItems as $shoppingItem){
		$preis = we_base_util::std_numberformat((isset($shoppingItem['serial']['we_' . $pricename])) ? $shoppingItem['serial']['we_' . $pricename] : $shoppingItem['serial'][$pricename]);
		$totPrice += $preis * $shoppingItem['quantity'];

		// foreach article we must determine the correct tax-rate
		if($categoryMode){
			$wedocCategory = $shoppingItem['serial'][we_listview_base::PROPPREFIX . 'CATEGORY'];
			$billingCountry = we_shop_category::getCountryFromCustomer(false, $_SESSION['webuser']);
			$catId = !empty($shoppingItem['serial'][WE_SHOP_CATEGORY_FIELD_NAME]) ? $shoppingItem['serial'][WE_SHOP_CATEGORY_FIELD_NAME] : 0;

			$shopVat = we_shop_category::getShopVatByIdAndCountry($catId, $wedocCategory, $billingCountry, true);
			$shopCategory = $catId;
		} else {
			$vatId = isset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME]) ? $shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME] : 0;
			$shopVat = we_shop_vats::getVatRateForSite($vatId, true, false);
			$shopCategory = 0;
		}

		$dat = $shoppingItem['serial'];

		$docid = intval(isset($dat['OF_ID']) ? $dat['OF_ID'] : $dat['ID']);
		$pub = intval(empty($dat['we_wedoc_Published']) ? $dat['WE_Published'] : $dat['we_wedoc_Published']);
		$type = (!empty($dat['we_wedoc_ContentType'] && $dat['we_wedoc_ContentType'] == we_base_ContentTypes::OBJECT_FILE) ? 'object' : 'document');
		$variant = $dat['WE_VARIANT'];

		$orderDocID = f('SELECT ID FROM ' . SHOP_ORDER_DOCUMENT_TABLE . ' WHERE DocID=' . $docid . ' AND type="' . $type . '" AND variant="' . $DB_WE->escape($variant) . '" AND Published=FROM_UNIXTIME(' . $pub . ')');
		if(!$orderDocID){
			$data = $dat;
			unset($data['we_shoptitle'], $data['we_shopdescription'], $data['we_sacf'], $data['shopvat'], $data['shopcategory'], $data['WE_VARIANT']);
			//add document first
			$DB_WE->query('INSERT INTO ' . SHOP_ORDER_DOCUMENT_TABLE . ' SET ' . we_database_base::arraySetter([
					'DocID' => $docid,
					'type' => $type,
					'variant' => $variant,
					'Published' => sql_function('FROM_UNIXTIME(' . $pub . ')'),
					'title' => strip_tags($dat['we_shoptitle']),
					'description' => strip_tags($dat['we_shopdescription']),
					'CategoryID' => $shopCategory ?: 0,
					'SerializedData' => we_serialize($data, SERIALIZE_JSON, false, 5, true)
			]));
			$orderDocID = $DB_WE->getInsertId();
		}

		if(!$DB_WE->query('INSERT INTO ' . SHOP_ORDER_ITEM_TABLE . ' SET ' . we_database_base::arraySetter([
					'orderID' => $orderID,
					'orderDocID' => $orderDocID,
					'quantity' => abs($shoppingItem['quantity']),
					'Price' => $preis,
					'Vat' => ($shopVat !== false ? $shopVat : sql_function('NULL')),
					'customFields' => $shoppingItem['serial'][WE_SHOP_ARTICLE_CUSTOM_FIELD] ? we_serialize($shoppingItem['serial'][WE_SHOP_ARTICLE_CUSTOM_FIELD], SERIALIZE_JSON, false, 0, true) : sql_function('NULL'),
			]))){

			t_e('error during write shop data contents of basket', $shoppingItems);
			echo 'Data Insert Failed';
			return;
		}
	}

	if($shipping === ''){// we have to change shipping costs
		$weShippingControl = we_shop_shippingControl::getShippingControl();

		$DB_WE->query('UPDATE ' . SHOP_ORDER_TABLE . ' SET ' . we_database_base::arraySetter([
				'shippingCost' => floatval($weShippingControl->getShippingCostByOrderValue($totPrice, $customer)),
				'shippingNet' => (bool) $weShippingControl->isNet,
				'shippingVat' => floatval($weShippingControl->vatRate),
			]) . ' WHERE ID=' . $orderID
		);
	}


	$doc = we_getDocForTag('top');
	$lang = substr($doc->Language, 0, 2);
	$weShopStatusMails = we_shop_statusMails::getShopStatusMails();
	$weShopStatusMails->checkAutoMailAndSend('Order', $orderID, $customer, $lang);
}
