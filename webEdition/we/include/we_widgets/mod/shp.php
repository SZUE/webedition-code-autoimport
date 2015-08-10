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
// widget Shop

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

$isRefresh = true;
if(!isset($aCols[5])){
	$aCols = explode(';', $aProps[3]);
	$isRefresh = false;
}

$sKPIs = $aCols[0] ? : array();
$bOrders = isset($sKPIs[0]) && $sKPIs[0] ? true : false;
$bCustomer = isset($sKPIs[1]) && $sKPIs[1] ? true : false;
$bAverageOrder = isset($sKPIs[2]) && $sKPIs[2] ? true : false;
$bTarget = isset($sKPIs[3]) && $sKPIs[3] ? true : false;

$iDate = intval($aCols[1]);
$sRevenueTarget = intval($aCols[2]);

switch($iDate){
	default:
	case 0 : //heute
		$queryShopDateCondtion = '(DATE(DateOrder) = DATE(CURDATE()))';
		$timestampCustomer = '(MemberSince >= UNIX_TIMESTAMP(NOW()))';
		$interval = g_l('cockpit', '[today]');
		break;
	case 1 : //diese woche
		$queryShopDateCondtion = '(WEEK(DateOrder,1) = WEEK(NOW(),1) AND YEAR(DateOrder) = YEAR(NOW()))';
		$timestampCustomer = '(WEEK(FROM_UNIXTIME(MemberSince),1) = WEEK(NOW(),1) AND YEAR(FROM_UNIXTIME(MemberSince)) = YEAR(NOW()))';
		$interval = g_l('cockpit', '[this_week]');
		break;
	case 2 : //letzte woche
		$queryShopDateCondtion = '(WEEK(DateOrder) = WEEK(CURDATE())-1 AND YEAR(DateOrder) = YEAR(CURDATE()))';
		$timestampCustomer = '(WEEK(FROM_UNIXTIME(MemberSince),1) = WEEK(NOW()-INTERVAL 7 DAY,1) AND (YEAR(FROM_UNIXTIME(MemberSince)) = YEAR(NOW()-INTERVAL 7 DAY)))';
		$interval = g_l('cockpit', '[last_week]');
		break;
	case 3 : //dieser monat
		$queryShopDateCondtion = '(YEAR(DateOrder) = YEAR(CURDATE()) AND MONTH(DateOrder) = MONTH(CURDATE()))';
		$timestampCustomer = '(MONTH(FROM_UNIXTIME(MemberSince)) = MONTH(NOW()) AND YEAR(FROM_UNIXTIME(MemberSince)) = YEAR(NOW()))';
		$interval = g_l('cockpit', '[this_month]');
		break;
	case 4 : //letzter monat
		$queryShopDateCondtion = '(YEAR(DateOrder) = YEAR(NOW()-INTERVAL 1 MONTH) AND MONTH(DateOrder) = MONTH(NOW()-INTERVAL 1 MONTH))';
		$timestampCustomer = '(MONTH(FROM_UNIXTIME(MemberSince)) = MONTH(NOW()-INTERVAL 1 MONTH) AND YEAR(FROM_UNIXTIME(MemberSince)) = YEAR(NOW()-INTERVAL 1 MONTH))';
		$interval = g_l('cockpit', '[last_month]');
		break;
	case 5 : //dieses jahr
		$queryShopDateCondtion = '(YEAR(DateOrder) = YEAR(CURDATE()))';
		$timestampCustomer = '(YEAR(FROM_UNIXTIME(MemberSince)) = YEAR(NOW()))';
		$interval = g_l('cockpit', '[this_year]');
		break;
	case 6 : //letztes jahr
		$queryShopDateCondtion = '(YEAR(DateOrder) = YEAR(CURDATE()) - 1)';
		$timestampCustomer = '(YEAR(FROM_UNIXTIME(MemberSince)) = (YEAR(NOW())-1))';
		$interval = g_l('cockpit', '[last_year]');
		break;
}

// get some preferences!
$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"'));
$currency = oldHtmlspecialchars($feldnamen[0]);
$numberformat = $feldnamen[2];
$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
$defaultVat = ($feldnamen[1] ? : 0);

$amountCustomers = $amountOrders = $amountArticles = $amountCanceledOrders = $canceled = 0;

if(defined('WE_SHOP_MODULE_DIR') && (permissionhandler::hasPerm("NEW_SHOP_ARTICLE") || permissionhandler::hasPerm("DELETE_SHOP_ARTICLE") || permissionhandler::hasPerm("EDIT_SHOP_ORDER") || permissionhandler::hasPerm("DELETE_SHOP_ORDER") || permissionhandler::hasPerm("EDIT_SHOP_PREFS"))){
	$queryShop = ' FROM ' . SHOP_TABLE . '	WHERE ' . $queryShopDateCondtion;

	$total = $payed = $unpayed = $timestampDatePayment = 0;
	if(($maxRows = f('SELECT COUNT(1) ' . $queryShop))){

		$amountOrders = f('SELECT COUNT(distinct IntOrderID) ' . $queryShop);
		$amountCanceledOrders = f('SELECT COUNT(distinct IntOrderID) ' . $queryShop . 'AND DateCancellation != 0');
		$amountArticles = f('SELECT COUNT(IntID) ' . $queryShop);

		// first of all calculate complete revenue of this year -> important check vats as well.
		$cur = 0;
		while($maxRows > $cur){
			$DB_WE->query('SELECT strSerial,strSerialOrder,(Price*IntQuantity) AS actPrice,UNIX_TIMESTAMP(DatePayment) AS payed, UNIX_TIMESTAMP(DateCancellation) AS canceled ' . $queryShop . ' LIMIT ' . $cur . ',1000');
			$cur+=1000;
			while($DB_WE->next_record()){

				// for the articlelist, we need also all these article, so save them in array
				// initialize all data saved for an article
				$shopArticleObject = unserialize($DB_WE->f('strSerial'));
				$serialOrder = $DB_WE->f('strSerialOrder');
				$orderData = ($serialOrder ? unserialize($serialOrder) : array());

				// all data from strSerialOrders
				// first unserialize order-data
				// ********************************************************************************
				// now get information about complete order
				// - pay VAT?
				// - prices are net?
				// prices are net?
				$pricesAreNet = (isset($orderData[WE_SHOP_PRICE_IS_NET_NAME]) ? $orderData[WE_SHOP_PRICE_IS_NET_NAME] : true);

				// must calculate vat?
				$calcVat = (isset($orderData[WE_SHOP_CALC_VAT]) ? $orderData[WE_SHOP_CALC_VAT] : true);

				//
				// no get information about complete order
				// ********************************************************************************
				// now calculate prices: without vat first
				$actPrice = $DB_WE->f('actPrice');
				// now calculate vats to prices !!!
				if($calcVat){ // vat must be payed for this order
					// now determine VAT
					$articleVat = (isset($shopArticleObject[WE_SHOP_VAT_FIELD_NAME]) ?
							$shopArticleObject[WE_SHOP_VAT_FIELD_NAME] :
							(isset($defaultVat) ? $defaultVat : 0)
						);

					if($articleVat){
						if(!isset($articleVatArray[$articleVat])){ // avoid notices
							$articleVatArray[$articleVat] = 0;
						}

						// calculate vats to prices if neccessary
						if($pricesAreNet){
							$articleVatArray[$articleVat] += ($actPrice * $articleVat / 100);
							$actPrice += ($actPrice * $articleVat / 100);
						} else {
							$articleVatArray[$articleVat] += ($actPrice * $articleVat / (100 + $articleVat));
						}
					}
				}
				$total += $actPrice;

				switch(true){
					case ($DB_WE->f('payed')):
						$payed += $actPrice;
						break;
					case ($DB_WE->f('canceled')):
						$canceled += $actPrice;
						break;
					default:
						$unpayed += $actPrice;
				}
			}
		}
	}
}

if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm("EDIT_CUSTOMER") || permissionhandler::hasPerm("NEW_CUSTOMER"))){
	$queryCustomer = ' FROM ' . CUSTOMER_TABLE . '	WHERE ' . $timestampCustomer;

	if(($maxRowsCustomer = f('SELECT COUNT(1) ' . $queryCustomer))){
		$amountCustomers = f('SELECT COUNT(distinct Username) ' . $queryCustomer);
	}
}

$shopDashboardTable = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 1, 3);
$i = 0;
if($bOrders){
	//1. row
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont"), we_html_element::htmlB(g_l('cockpit', '[shop_dashboard][cnt_order]') . we_html_tools::getPixel(5, 1)));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right"), we_html_element::htmlB(($amountOrders > 0 ? $amountOrders : 0)));
	$i++;

	//2. row
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont","style"=>"color:red;"), g_l('cockpit','[shop_dashboard][canceled_order]').we_html_tools::getPixel(5, 1));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont","align"=>"right","style"=>"color:red;"),($amountCanceledOrders > 0 ? $amountCanceledOrders : 0));
	$i++;

	//3. row
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont"), g_l('cockpit', '[shop_dashboard][cnt_articles]') . we_html_tools::getPixel(5, 1));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right"), ($amountArticles > 0 ? $amountArticles : 0));
	$i++;

	//4. row
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont"), g_l('cockpit', '[shop_dashboard][articles_order]'));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right"), we_util_Strings::formatNumber(($amountArticles > 0 ? ($amountArticles / $amountOrders) : 0), $numberformat));
	$i++;

	//need some space
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont"), "&nbsp;");
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont"), "&nbsp;");
	$i++;
}

if($bAverageOrder){
	//order volume
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont"), we_html_element::htmlB(g_l('cockpit', '[shop_dashboard][order_volume]')));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right"), we_html_element::htmlB(we_util_Strings::formatNumber($total, $numberformat) . '&nbsp;' . $currency));
	$i++;
	
	//canceled volume
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont","style"=>"color:red;"), g_l('cockpit', '[shop_dashboard][canceled]'));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right", "style" => "color:red;"), we_util_Strings::formatNumber($canceled, $numberformat) . '&nbsp;' . $currency);
	$i++;
	
	//revenue
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont"), we_html_element::htmlB(g_l('cockpit', '[shop_dashboard][revenue]')));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right"), we_html_element::htmlB(we_util_Strings::formatNumber(($total-$canceled), $numberformat) . '&nbsp;' . $currency));
	$i++;

	//payed volume
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont", "style" => "color:green;"), g_l('cockpit', '[shop_dashboard][payed]'));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right", "style" => "color:green;"), we_util_Strings::formatNumber($payed, $numberformat) . '&nbsp;' . $currency);
	$i++;

	//unpayed volume
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont", "style" => "color:red;"), g_l('cockpit', '[shop_dashboard][unpayed]'));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right", "style" => "color:red;"), we_util_Strings::formatNumber($unpayed, $numberformat) . '&nbsp;' . $currency);
	$i++;

	//volume per order
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont"), g_l('cockpit', '[shop_dashboard][order_value_order]'));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right"), we_util_Strings::formatNumber(($amountOrders > 0 ? ($total / $amountOrders) : 0), $numberformat) . '&nbsp;' . $currency);
	$i++;

	//need some space
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont"), "&nbsp;");
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont"), "&nbsp;");
	$i++;
}

if($bCustomer){
	//new customer
	$shopDashboardTable->addRow();
	$shopDashboardTable->setCol($i, 0, array("class" => "middlefont"), we_html_element::htmlB(g_l('cockpit', '[shop_dashboard][cnt_new_customer]')));
	$shopDashboardTable->setCol($i, 1, array(), we_html_tools::getPixel(10, 1));
	$shopDashboardTable->setCol($i, 2, array("class" => "middlefont", "align" => "right"), we_html_element::htmlB(($amountCustomers > 0 ? $amountCustomers : 0)));
	$i++;
}

$shopDashboard = '<div style="width:60%;float:left;">' .
	$shopDashboardTable->getHtml() .
	'</div>'
	. '<div style="width:40%;float:right;">' . ($bTarget ? '<b>' . g_l('cockpit', '[shop_dashboard][revenue_target]') . '&nbsp;' . we_util_Strings::formatNumber($sRevenueTarget, $numberformat) . '&nbsp;' . $currency . '</b><br/>' : '') .
	'<canvas id="'.$newSCurrId . '_chart_div" width="160" height="160"></canvas>' .
	'</div><br style="clear:both;"/>';
	
if($bTarget){
	$shopDashboard .= "<script type='text/javascript' src='" . WE_INCLUDES_DIR . "we_widgets/dlg/shp/js/excanvas.js'></script>
		<script type='text/javascript' src='" . WE_INCLUDES_DIR . "we_widgets/dlg/shp/js/gauge.min.js'></script>
		<script type='text/javascript'>
			// Helper to execute a function after the window is loaded
			// see http://www.google.com/search?q=addLoadEvent
			function addLoadEvent(func) {
				var oldonload = window.onload;
				if (typeof window.onload != 'function') {
					window.onload = func;
				} else {
					window.onload = function() {
						if (oldonload) {
							oldonload();
						}
						func();
					}
				}
			}

			addLoadEvent( function() {
				var options;
				var widgetDoc = typeof widgetFrame !== 'undefined' ? widgetFrame.document : " . ($isRefresh ? 'parent.document' : 'document') . ";

				// Draw the gauge using custom settings
				options = {
					value: " . we_util_Strings::formatNumber(($total-$canceled)) . ",
					label: 'Ziel in " . $currency . "',
					unitsLabel: ' " . $currency . "',
					min: 0,
					max: " . ($sRevenueTarget * 2) . ",
					minorTicks: 5, // small ticks inside each major tick
					greenFrom: " . ($sRevenueTarget * 1.1) . ",
					greenTo: " . ($sRevenueTarget * 2) . ",
					yellowFrom: " . ($sRevenueTarget * 0.9) . ",
					yellowTo: " . ($sRevenueTarget * 1.1) . ",
					redFrom: 0,
					redTo: " . ($sRevenueTarget * 0.9) . "
				};

				new Gauge(widgetDoc.getElementById('".$newSCurrId . "_chart_div'), options );
			});

		</script>";
}