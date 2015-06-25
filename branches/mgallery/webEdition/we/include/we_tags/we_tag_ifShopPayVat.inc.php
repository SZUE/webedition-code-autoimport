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
	} elseif(!empty($GLOBALS[$namefrom])){
		$customer = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($GLOBALS[$namefrom]));
		$customer = $customer ? array_merge($customer, we_customer_customer::getEncryptedFields()) : array();
	} else {
		$customer = false;
	}

	$country = (!$customer && $usefallback ?
			(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_location"', '', $GLOBALS['DB_WE'], -1) ? :
				'') :
			'');

	return $weShopVatRule->executeVatRule($customer, $country);
}
