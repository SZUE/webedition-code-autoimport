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
function we_tag_ifShopPayVat($attribs){
	$namefrom = weTag_getAttribute("customerfrom", $attribs, '', we_base_request::STRING);
	$usefallback = weTag_getAttribute("usefallback", $attribs);
	$weShopVatRule = we_shop_vatRule::getShopVatRule();

	if(we_tag('ifRegisteredUser', array(), '')){
		$customer = $_SESSION['webuser'];
	} elseif(isset($GLOBALS[$namefrom]) && $GLOBALS[$namefrom]){
		$cus = new we_customer_customertag($GLOBALS[$namefrom]);
		$customerarray = $cus->getDBRecord();
		unset($cus);
		$customer = ($customerarray ? : false);
	} elseif(isset($GLOBALS[$namefrom]) && $GLOBALS[$namefrom]){
		$cus = new we_customer_customertag($GLOBALS[$namefrom]);
		$customerarray = $cus->getDBRecord();
		unset($cus);
		$customer = ($customerarray ? : false);
	} else {
		$customer = false;
	}

	$country = '';
	if(!$customer && $usefallback){
		$country = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_location"', '', $GLOBALS['DB_WE'], -1) ? : '';
	}

	return $weShopVatRule->executeVatRule($customer, $country);
}
