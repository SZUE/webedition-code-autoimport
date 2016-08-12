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
define('SHOP_TABLE', TBL_PREFIX . 'tblOrders');
define('SHOP_ORDER_TABLE', TBL_PREFIX . 'tblOrder');
define('SHOP_ORDER_DATES_TABLE', TBL_PREFIX . 'tblOrderDates');
define('SHOP_ORDER_DOCUMENT_TABLE', TBL_PREFIX . 'tblOrderItemDocument');
define('SHOP_ORDER_ITEM_TABLE', TBL_PREFIX . 'tblOrderItem');
define('WE_SHOP_VAT_TABLE', TBL_PREFIX . 'tblshopvats');
define('WE_SHOP_MODULE_DIR', WE_MODULES_DIR . 'shop/');

// name of request array for shopping items
define('WE_SHOP_ARTICLE_CUSTOM_FIELD', 'we_sacf');
define('WE_SHOP_CART_CUSTOM_FIELD', 'we_sscf');
define('WE_SHOP_CART_CUSTOMER_FIELD', 'we_shopCustomer');
define('WE_SHOP_TITLE_FIELD_NAME', 'shoptitle');
define('WE_SHOP_DESCRIPTION_FIELD_NAME', 'shopdescription');

define('WE_SHOP_VAT_FIELD_NAME', 'shopvat'); // due to the names of old fields (shoptitle, shopdescription) - we must name shopvat
define('WE_SHOP_CATEGORY_FIELD_NAME', 'shopcategory');
define('WE_SHOP_PRICE_IS_NET_NAME', 'we_shopPriceIsNet');
define('WE_SHOP_PRICENAME', 'we_shopPricename');
define('WE_SHOP_SHIPPING', 'we_shopPriceShipping');
define('WE_SHOP_CALC_VAT', 'we_shopCalcVat');

we_base_request::registerTables(array(
	'SHOP_TABLE' => SHOP_TABLE,
	'WE_SHOP_VAT_TABLE' => WE_SHOP_VAT_TABLE,
	'SHOP_ORDER_TABLE' => SHOP_ORDER_TABLE,
	'SHOP_ORDER_DATES_TABLE' => SHOP_ORDER_DATES_TABLE,
	'SHOP_ORDER_DOCUMENT_TABLE' => SHOP_ORDER_DOCUMENT_TABLE,
	'SHOP_ORDER_ITEM_TABLE' => SHOP_ORDER_ITEM_TABLE,
));
