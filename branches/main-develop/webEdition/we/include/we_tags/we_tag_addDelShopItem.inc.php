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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_addDelShopItem($attribs, $content) {
	if (!defined("SHOP_TABLE")) {
		print modulFehltError('Shop', '"adddelShopitem"');
		return;
	}
	if (($foo = attributFehltError($attribs, "shopname", "addDelShopItem"))) {
		return $foo;
	}

	$shopname = we_getTagAttribute("shopname", $attribs);
	$floatquantities = we_getTagAttribute("floatquantities", $attribs, '', true);
	$floatquantities = empty($floatquantities) ? 'false' : $floatquantities;

	include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_modules/shop/we_conf_shop.inc.php");

	$floatfilter = new Zend_Filter_LocalizedToNormalized();
	if ((isset($_REQUEST["shopname"]) && $_REQUEST["shopname"] == $shopname) || !isset($_REQUEST["shopname"]) || $_REQUEST["shopname"] == "") {
		if (isset($_REQUEST["shop_cart_id"]) && is_array($_REQUEST["shop_cart_id"])) {
			if ($_REQUEST["t"] > (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 )) {
				if ($_REQUEST["t"] != (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 )) {
					foreach ($_REQUEST["shop_cart_id"] as $cart_id => $cart_amount) {
						$GLOBALS[$shopname]->Set_Cart_Item($cart_id, $floatquantities ? $floatfilter->filter($cart_amount) : $cart_amount);
						$_SESSION[$shopname . '_save'] = $GLOBALS[$shopname]->getCartProperties();
					}
				}
			}
		} else if (isset($_REQUEST["shop_anzahl_und_id"]) && is_array($_REQUEST["shop_anzahl_und_id"])) {
			if ($_REQUEST["t"] > (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 )) {
				if ($_REQUEST["t"] != (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 )) {
					//	reset the Array
					reset($_REQUEST["shop_anzahl_und_id"]);
					while (list($shop_articleid_variant, $shop_anzahl) = each($_REQUEST["shop_anzahl_und_id"])) {
						$articleInfo = explode("_", $shop_articleid_variant);
						$shop_artikelid = $articleInfo[0];
						$shop_artikeltype = $articleInfo[1];
						$shop_variant = (isset($articleInfo[2]) ? $articleInfo[2] : "");
						$GLOBALS[$shopname]->Set_Item($shop_artikelid, $floatquantities ? $floatfilter->filter($shop_anzahl) : $shop_anzahl, $shop_artikeltype, $shop_variant);
						$_SESSION[$shopname . '_save'] = $GLOBALS[$shopname]->getCartProperties();
						unset($articleInfo);
					}
				}
				$_SESSION["tb"] = $_REQUEST["t"];
			}
		} else if (isset($_REQUEST["shop_artikelid"]) && $_REQUEST["shop_artikelid"] != "" && isset($_REQUEST["shop_anzahl"]) && $_REQUEST["shop_anzahl"] != "0") {
			if ($_REQUEST["t"] > (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0)) {
				if ($_REQUEST["t"] != (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0)) {
					$GLOBALS[$shopname]->Add_Item($_REQUEST["shop_artikelid"], $floatquantities ? $floatfilter->filter($_REQUEST["shop_anzahl"]) : $_REQUEST["shop_anzahl"], $_REQUEST["type"], (isset($_REQUEST["' . WE_SHOP_VARIANT_REQUEST . '"]) ? $_REQUEST["' . WE_SHOP_VARIANT_REQUEST . '"] : ""), ( ( isset($_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"]) && is_array($_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"]) ) ? $_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"] : array()));
					$_SESSION[$shopname . '_save'] = $GLOBALS[$shopname]->getCartProperties();
				}
				$_SESSION["tb"] = $_REQUEST["t"];
			}
		} else if (isset($_REQUEST["del_shop_artikelid"]) && $_REQUEST["del_shop_artikelid"] != "") {
			if ($_REQUEST["t"] > (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 )) {
				if ($_REQUEST["t"] != (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 )) {
					$GLOBALS[$shopname]->Del_Item($_REQUEST["del_shop_artikelid"], $_REQUEST["type"], (isset($_REQUEST["' . WE_SHOP_VARIANT_REQUEST . '"]) ? $_REQUEST["' . WE_SHOP_VARIANT_REQUEST . '"] : ""), ( ( isset($_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"]) && is_array($_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"]) ) ? $_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"] : array()));
					$_SESSION[$shopname . '_save'] = $GLOBALS[$shopname]->getCartProperties();
				}
				$_SESSION["tb"] = $_REQUEST["t"];
			}
		}
	}
}
