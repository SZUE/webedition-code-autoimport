<?php

/**
 * webEdition CMS
 *
 * $Rev: 6489 $
 * $Author: mokraemer $
 * $Date: 2013-08-19 15:19:40 +0200 (Mon, 19 Aug 2013) $
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
// widget Shop

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
//if(!isset($aCols)){
	$aCols = explode(';', $aProps[3]);
//}
$sTypeBinary = $aCols[0];
$bTypeDoc = (bool) $sTypeBinary{0};
$bTypeTpl = (bool) $sTypeBinary{1};
$bTypeObj = (bool) $sTypeBinary{2};
$bTypeCls = (bool) $sTypeBinary{3};

$iDate = intval($aCols[1]);
$sRevenueTarget = intval($aCols[2]);

switch($iDate){
	default:
		break;
	case 0 : //heute 
		$queryShopDateCondtion = '(DATE(DateOrder) = DATE(CURDATE()))';
		$timestampCustomer = '(MemberSince >= UNIX_TIMESTAMP(NOW()))';
		$interval = g_l('cockpit', '[today]');
		break;
	case 1 : //diese woche
		$queryShopDateCondtion = '(WEEK(DateOrder) = WEEK(CURDATE()) AND YEAR(DateOrder) = YEAR(CURDATE()))';
		$timestampCustomer = '(MemberSince>=UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 7 DAY)))';
		$interval = g_l('cockpit', '[this_week]');
		break;
	case 2 : //letzte woche
		$queryShopDateCondtion = '(WEEK(DateOrder) = WEEK(CURDATE())-1 AND YEAR(DateOrder) = YEAR(CURDATE()))';
		$timestampCustomer = '(MemberSince>=UNIX_TIMESTAMP(NOW()-INTERVAL 7 DAY))';
		$interval = g_l('cockpit', '[last_week]');
		break;
	case 3 : //dieser monat
		$queryShopDateCondtion = '(YEAR(DateOrder) = YEAR(CURDATE()) AND MONTH(DateOrder) = MONTH(CURDATE()))';
		$timestampCustomer = '(MemberSince>=UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 1 MONTH)))';
		$interval = g_l('cockpit', '[this_month]');
		break;
	case 4 : //letzter monat
		$queryShopDateCondtion = '(YEAR(DateOrder) = YEAR(CURDATE()) AND MONTH(DateOrder) = MONTH(CURDATE())-1)';
		$timestampCustomer = '(MemberSince>=UNIX_TIMESTAMP(NOW()-INTERVAL 1 MONTH))';
		$interval = g_l('cockpit', '[last_month]');
		break;
	case 5 : //dieses jahr
		$queryShopDateCondtion = '(YEAR(DateOrder) = YEAR(CURDATE()))';
		$timestampCustomer = '(MemberSince>=UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 1 YEAR)))';
		$interval = g_l('cockpit', '[this_year]');
		break;
	case 6 : //letztes jahr
		$queryShopDateCondtion = '(YEAR(DateOrder) = YEAR(CURDATE()) - 1)';
		$timestampCustomer = '(MemberSince>=UNIX_TIMESTAMP(NOW()-INTERVAL 1 YEAR))';
		$interval = g_l('cockpit', '[last_year]');
		break;
}

// get some preferences!
$feldnamen = explode('|', f('SELECT strFelder from ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname = "shop_pref"', 'strFelder', $DB_WE));
$currency = oldHtmlspecialchars($feldnamen[0]);
$numberformat = $feldnamen[2];
$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
$defaultVat = !empty($feldnamen[1]) ? ($feldnamen[1]) : 0;

if(defined("WE_SHOP_MODULE_DIR") && we_hasPerm("CAN_SEE_SHOP")){
	$queryShop = ' FROM ' . SHOP_TABLE . '	WHERE ' . $queryShopDateCondtion;
	
	$total = $payed = $unpayed = 0;
	if(($maxRows = f('SELECT COUNT(1) AS a ' . $queryShop, 'a', $DB_WE))){

		$amountOrders = f('SELECT COUNT(distinct IntOrderID) AS a ' . $queryShop, 'a', $DB_WE);
		$amountArticles = f('SELECT COUNT(IntID) AS b ' . $queryShop, 'b', $DB_WE);
		
		// first of all calculate complete revenue of this year -> important check vats as well.
		$cur = 0;
		while($maxRows > $cur) {
			$DB_WE->query('SELECT strSerial,strSerialOrder,(Price*IntQuantity) AS actPrice,(!ISNULL(DatePayment) && DatePayment>0) AS payed ' . $queryShop . ' LIMIT ' . $cur . ',1000');
			$cur+=1000;
			while($DB_WE->next_record()) {
	
				// for the articlelist, we need also all these article, so sve them in array
				// initialize all data saved for an article
				$shopArticleObject = @unserialize($DB_WE->f('strSerial'));
				$serialOrder = $DB_WE->f('strSerialOrder');
				$orderData = ($serialOrder ? @unserialize($serialOrder) : array());
	
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
	
					if($articleVat > 0){
						if(!isset($articleVatArray[$articleVat])){ // avoid notices
							$articleVatArray[$articleVat] = 0;
						}
	
						// calculate vats to prices if neccessary
						if($pricesAreNet){
							$articleVatArray[$articleVat] += ($actPrice * $articleVat / 100);
							$actPrice += ($actPrice * $articleVat / 100);
						} else{
							$articleVatArray[$articleVat] += ($actPrice * $articleVat / (100 + $articleVat));
						}
					}
				}
				$total += $actPrice;
	
				if($DB_WE->f('payed') == 0){
					$payed += $actPrice;
				} else{
					$unpayed += $actPrice;
				}
			}
		}
	}
}

if(defined("CUSTOMER_TABLE") && we_hasPerm("CAN_SEE_CUSTOMER")){
	$queryCustomer = ' FROM ' . CUSTOMER_TABLE . '	WHERE ' . $timestampCustomer;
	
	if(($maxRowsCustomer = f('SELECT COUNT(1) AS a ' . $queryCustomer, 'a', $DB_WE))){
		$amountCustomers = f('SELECT COUNT(distinct Username) AS a ' . $queryCustomer, 'a', $DB_WE);
	}
}

$shopDashboard ="<script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['gauge']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Ziel in ".$currency."', ".we_util_Strings::formatNumber($total)."],
        ]);

        var options = {
          width: 300, height: 170,
          max: ".($sRevenueTarget*2).",
          redFrom: 0, redTo: ".($sRevenueTarget*0.9).",
          yellowFrom: ".($sRevenueTarget*0.9).", yellowTo: ".($sRevenueTarget*1.1).",
          greenFrom: ".($sRevenueTarget*1.1).", greenTo: ".($sRevenueTarget*2).",
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>";


$shopDashboard .= '<div style="width:60%;float:left;">';

//$shopDashboard .= '<table cellspacing="0" cellpadding="0" border="0">';

$shopDashboardTable = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 1, 3);

//1. row
//$shopDashboardTable->addRow();
$shopDashboardTable->setCol(0, 0, array("class" => "middlefont"), we_html_element::htmlB(g_l('cockpit','[shop_dashboard][cnt_order]').we_html_tools::getPixel(5, 1)));
$shopDashboardTable->setCol(0, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(0, 2, array("class" => "middlefont","align"=>"right"), we_html_element::htmlB(($amountOrders > 0 ? $amountOrders : 0)));

//2. row
$shopDashboardTable->addRow();
$shopDashboardTable->setCol(1, 0, array("class" => "middlefont"), g_l('cockpit','[shop_dashboard][cnt_articles]').we_html_tools::getPixel(5, 1));
$shopDashboardTable->setCol(1, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(1, 2, array("class" => "middlefont","align"=>"right"),($amountArticles > 0 ? $amountArticles : 0));

//3. row
$shopDashboardTable->addRow();
$shopDashboardTable->setCol(2, 0, array("class" => "middlefont"), g_l('cockpit','[shop_dashboard][articles_order]'));
$shopDashboardTable->setCol(2, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(2, 2, array("class" => "middlefont","align"=>"right"), we_util_Strings::formatNumber($amountArticles/$amountOrders,$numberformat));

//4. row
$shopDashboardTable->addRow();
$shopDashboardTable->setCol(3, 0, array("class" => "middlefont"), "&nbsp;");
$shopDashboardTable->setCol(3, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(3, 2, array("class" => "middlefont"), "&nbsp;");

//5. row
$shopDashboardTable->addRow();
$shopDashboardTable->setCol(4, 0, array("class" => "middlefont"), we_html_element::htmlB(g_l('cockpit','[shop_dashboard][revenue]')));
$shopDashboardTable->setCol(4, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(4, 2, array("class" => "middlefont","align"=>"right"), we_html_element::htmlB(we_util_Strings::formatNumber($total,$numberformat). '&nbsp;'. $currency));

//6. row
$shopDashboardTable->addRow();
$shopDashboardTable->setCol(5, 0, array("class" => "middlefont","style"=>"color:green;"), g_l('cockpit','[shop_dashboard][payed]'));
$shopDashboardTable->setCol(5, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(5, 2, array("class" => "middlefont","align"=>"right","style"=>"color:green;"), we_util_Strings::formatNumber($payed,$numberformat). '&nbsp;'. $currency);

//7. row
$shopDashboardTable->addRow();
$shopDashboardTable->setCol(6, 0, array("class" => "middlefont","style"=>"color:red;"), g_l('cockpit','[shop_dashboard][unpayed]'));
$shopDashboardTable->setCol(6, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(6, 2, array("class" => "middlefont","align"=>"right","style"=>"color:red;"), we_util_Strings::formatNumber($unpayed,$numberformat). '&nbsp;'. $currency);

//8. row
$shopDashboardTable->addRow();
$shopDashboardTable->setCol(7, 0, array("class" => "middlefont"), g_l('cockpit','[shop_dashboard][order_value_order]'));
$shopDashboardTable->setCol(7, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(7, 2, array("class" => "middlefont","align"=>"right"), we_util_Strings::formatNumber($total/$amountOrders,$numberformat). '&nbsp;'. $currency);

//9. row
$shopDashboardTable->addRow();
$shopDashboardTable->setCol(8, 0, array("class" => "middlefont"), "&nbsp;");
$shopDashboardTable->setCol(8, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(8, 2, array("class" => "middlefont"), "&nbsp;");

//10. row
$shopDashboardTable->addRow();
$shopDashboardTable->setCol(9, 0, array("class" => "middlefont"), we_html_element::htmlB(g_l('cockpit','[shop_dashboard][cnt_new_customer]')));
$shopDashboardTable->setCol(9, 1, array(), we_html_tools::getPixel(10, 1));
$shopDashboardTable->setCol(9, 2, array("class" => "middlefont","align"=>"right"), we_html_element::htmlB(($amountCustomers > 0 ? $amountCustomers : 0)));

$shopDashboard .= $shopDashboardTable->getHtml();

$shopDashboard .= '</div>';
$shopDashboard .= '<div style="width:40%;float:right;"><b>'.g_l('cockpit','[shop_dashboard][revenue_target]').'&nbsp;'.we_util_Strings::formatNumber($sRevenueTarget,$numberformat) .'&nbsp;'. $currency.'</b>';
$shopDashboard .= we_html_element::htmlDiv(array("id" => "chart_div"),'');
$shopDashboard .= '</div><br style="clear:both;"/>';