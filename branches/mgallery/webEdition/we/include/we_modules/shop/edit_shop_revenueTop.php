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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
$protect = we_base_moduleInfo::isActive('shop') && we_users_util::canEditModule('shop') ? null : array(false);
we_html_tools::protect($protect);

$selectedYear = we_base_request::_(we_base_request::INT, 'ViewYear', date('Y'));
$selectedMonth = we_base_request::_(we_base_request::INT, 'ViewMonth', 1);
$orderBy = we_base_request::_(we_base_request::STRING, 'orderBy', 'IntOrderID');
$actPage = we_base_request::_(we_base_request::INT, 'actPage', 0);

function orderBy($a, $b){
	static $ord = null;
	static $desc = false;
	if($ord === null){
		$ord = we_base_request::_(we_base_request::RAW, "orderBy");
		$desc = we_base_request::_(we_base_request::BOOL, "orderDesc");
	}
	$ret = ($a[$ord] >= $b[$ord]);
	return ($desc ? !$ret : $ret);
}

function getTitleLink($text, $orderKey){
	$desc = we_base_request::_(we_base_request::BOOL, "orderDesc");
	$_href = $_SERVER['SCRIPT_NAME'] .
			'?ViewYear=' . $GLOBALS['selectedYear'] .
			'&ViewMonth=' . $GLOBALS['selectedMonth'] .
			'&orderBy=' . $orderKey .
			'&actPage=' . $GLOBALS['actPage'] .
			( ($GLOBALS['orderBy'] == $orderKey && !$desc) ? '&orderDesc=true' : '' );

	return '<a href="' . $_href . '">' . $text . '</a>' . ($GLOBALS['orderBy'] == $orderKey ? ' <i class="fa fa-sort-' . ($desc ? 'desc' : 'asc') . ' fa-lg"></i>' : '<i class="fa fa-sort fa-lg"></i>');
}

function getPagerLink(){
	return $_SERVER['SCRIPT_NAME'] .
			'?ViewYear=' . $GLOBALS['selectedYear'] .
			'&ViewMonth=' . $GLOBALS['selectedMonth'] .
			'&orderBy=' . $GLOBALS['orderBy'] .
			(we_base_request::_(we_base_request::BOOL, "orderDesc") ? '&orderDesc=true' : '' );
}

function yearSelect($select_name){
	$yearStart = 2001;
	$yearNow = date('Y');
	$opts = array();

	while($yearNow > $yearStart){
		$opts[$yearNow] = $yearNow;
		$yearNow--;
	}
	return we_class::htmlSelect($select_name, $opts, 1, we_base_request::_(we_base_request::INT, $select_name, ''), false, array('id' => $select_name));
}

function monthSelect($select_name, $selectedMonth){
	$opts = g_l('modules_shop', '[month]');
	$opts[-1] = '-';
	ksort($opts, SORT_NUMERIC);
	return we_class::htmlSelect($select_name, $opts, 1, $selectedMonth, false, array('id' => $select_name));
}

$mon = we_base_request::_(we_base_request::INT, 'ViewMonth');
echo we_html_tools::getHtmlTop('', '', '', STYLESHEET .
		we_html_element::jsElement('
	function we_submitDateform() {
		elem = document.forms[0];
		elem.submit();
	}

	function setHeaderTitle() {
		pre = "";
		post = "' . ($mon > 0 ? g_l('modules_shop', '[month][' . $mon . ']') . ' ' : '') . we_base_request::_(we_base_request::INT, 'ViewYear') . '";
		if(parent.edheader && parent.edheader.weTabs.setTitlePath) {
			parent.edheader.weTabs.setTitlePath(post,pre);
		} else {
				setTimeout(setHeaderTitle,100);
		}
	}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

		switch (args[0]) {
			case "openOrder": //TODO: check this adress: mit oder ohne tree? Bisher: left
				if(top.content.doClick) {
					top.content.doClick(args[1], args[2], args[3]);//TODO: check this adress
				}
				break;
			default: // not needed yet
				top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
		}
	}')) . '
<body class="weEditorBody" onload="self.focus(); setHeaderTitle();" onunload="">
<form>';

// get some preferences!
$feldnamen = explode('|', f('SELECT pref_value from ' . SETTINGS_TABLE. ' WHERE tool="shop" AND pref_name="shop_pref"'));
$waehr = "&nbsp;" . oldHtmlspecialchars($feldnamen[0]);
$numberformat = $feldnamen[2];
$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
$defaultVat = $feldnamen[1] ? : 0;

if(!isset($nrOfPage)){

	$nrOfPage = isset($feldnamen[4]) ? $feldnamen[4] : 20;
}
if($nrOfPage === "default"){
	$nrOfPage = 20;
}

$parts = array(
// get header of total revenue of a year
	array(
		'headline' => '<label for="ViewYear">' . g_l('modules_shop', '[selectYear]') . '</label>',
		'html' => yearSelect("ViewYear"),
		'space' => 150,
		'noline' => 1
	),
	array(
		'headline' => '<label for="ViewMonth">' . g_l('modules_shop', '[selectMonth]') . '</label>',
		'html' => monthSelect("ViewMonth", $selectedMonth),
		'space' => 150,
		'noline' => 1
	),
	array(
		'headline' => we_html_button::create_button(we_html_button::SELECT, "javascript:we_submitDateform();"),
		'html' => '',
		'space' => 150
	)
);

// get queries for revenue and article list.
$queryCondtion = 'YEAR(DateOrder)=' . $selectedYear . ($selectedMonth > 0 ? ' AND MONTH(DateOrder)=' . $selectedMonth : '');
//$queryCondtion = 'date_format(DateOrder,"%Y") = ' . $selectedYear . ($selectedMonth > 0 ? ' AND date_format(DateOrder,"%c") = ' . $selectedMonth : '');

$query = ' FROM ' . SHOP_TABLE . '	WHERE ' . $queryCondtion;
if(($maxRows = f('SELECT COUNT(1) ' . $query, '', $DB_WE))){
	$total = $payed = $unpayed = $canceled = 0;

	$amountOrders = f('SELECT COUNT(distinct IntOrderID) ' . $query, '', $DB_WE);
	//$unpayedOrders = f('SELECT COUNT(distinct IntOrderID) ' . $query . ' AND ISNULL(DatePayment)', '', $DB_WE);
	//$payedOrders = $amountOrders - $unpayedOrders;
	$editedOrders = f('SELECT COUNT(distinct IntOrderID) ' . $query . ' AND !ISNULL(DateShipping)', '', $DB_WE);

	//get table entries
	$orderRows = array();
	$DB_WE->query('SELECT strSerial,strSerialOrder,IntOrderID,IntCustomerID,IntArticleID,IntQuantity,DatePayment,DateOrder,DateCancellation,DATE_FORMAT(DateOrder, "%d.%m.%Y") AS formatDateOrder, DATE_FORMAT(DatePayment, "%d.%m.%Y") AS formatDatePayment, DATE_FORMAT(DateCancellation, "%d.%m.%Y") AS formatDateCancellation, Price ' . $query . ' ORDER BY ' . we_base_request::_(we_base_request::STRING, 'orderBy', 'IntOrderID') . ' LIMIT ' . ($actPage * $nrOfPage) . ',' . $nrOfPage);
	while($DB_WE->next_record()){

		// for the articlelist, we need also all these article, so save them in array
		// initialize all data saved for an article
		$shopArticleObject = we_unserialize($DB_WE->f('strSerial'));
		$orderData = we_unserialize($DB_WE->f('strSerialOrder'));


		$netPrice = $orderData['we_shopPriceIsNet'] ? $DB_WE->f('Price') : ($DB_WE->f('Price') / (1 + (floatval($shopArticleObject['shopvat']) / 100)));
		$grosPrice = $orderData['we_shopPriceIsNet'] ? ($DB_WE->f('Price') * (1 + (floatval($shopArticleObject['shopvat']) / 100))) : $DB_WE->f('Price');

		$priceToShow = $orderData['we_shopCalcVat'] ? $grosPrice : $netPrice;
		$articleSum = $DB_WE->f('IntQuantity') * $priceToShow;


		$orderRows[] = array(
			'articleArray' => $shopArticleObject,
			// save all data in array
			'IntOrderID' => $DB_WE->f('IntOrderID'), // also for ordering
			'IntCustomerID' => $DB_WE->f('IntCustomerID'),
			'IntArticleID' => $DB_WE->f('IntArticleID'), // also for ordering
			'IntQuantity' => $DB_WE->f('IntQuantity'),
			'articleSum' => $articleSum,
			'DatePayment' => $DB_WE->f('DatePayment'),
			'DateOrder' => $DB_WE->f('DateOrder'),
			'DateCancellation' => $DB_WE->f('DateCancellation'),
			'formatDateOrder' => $DB_WE->f('formatDateOrder'), // also for ordering
			'formatDatePayment' => $DB_WE->f('formatDatePayment'), // also for ordering
			'formatDateCancellation' => $DB_WE->f('formatDateCancellation'), // also for ordering
			'Price' => $priceToShow, // also for ordering
			WE_SHOP_TITLE_FIELD_NAME => (isset($shopArticleObject[WE_SHOP_TITLE_FIELD_NAME]) ? $shopArticleObject[WE_SHOP_TITLE_FIELD_NAME] : $shopArticleObject['we_' . WE_SHOP_TITLE_FIELD_NAME]), // also for ordering
			'orderArray' => $orderData,
		);
	}


	// first of all calculate complete revenue of this year -> important check vats as well.
	$cur = 0;
	while($maxRows > $cur){
		$DB_WE->query('SELECT strSerial,strSerialOrder,(Price*IntQuantity) AS actPrice,(!ISNULL(DatePayment) && DatePayment>0) AS payed, (!ISNULL(DateCancellation) && DateCancellation>0) AS canceled  ' . $query . ' LIMIT ' . $cur . ',1000');
		$cur+=1000;
		while($DB_WE->next_record()){

			// for the articlelist, we need also all these article, so sve them in array
			// initialize all data saved for an article
			$shopArticleObject = we_unserialize($DB_WE->f('strSerial'));
			$orderData = we_unserialize($DB_WE->f('strSerialOrder'));

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
						if($DB_WE->f('canceled') == 0){ // #7896 but not, if order is canceled
							$articleVatArray[$articleVat] += ($actPrice * $articleVat / 100);
						}
						$actPrice += ($actPrice * $articleVat / 100);
					} else {
						if($DB_WE->f('canceled') == 0){ // #7896 but not, if order is canceled
							$articleVatArray[$articleVat] += ($actPrice * $articleVat / (100 + $articleVat));
						}
					}
				}
			}

			if($DB_WE->f('canceled') > 0){ //#7896
				$canceled += $actPrice;
			} else {
				$total += $actPrice;
				if($DB_WE->f('payed') > 0){
					$payed += $actPrice;
				} else {
					$unpayed += $actPrice;
				}
			}
		}
	}
	// generate vat table
	$vatTable = '';
	if(isset($articleVatArray)){
		ksort($articleVatArray);
		$vatTable .= '
			<tr>
				<td colspan="7" class="shopContentfontR" style="padding-top:10px;">' . g_l('modules_shop', '[includedVat]') . ':</td>
			</tr>';
		foreach($articleVatArray as $_vat => $_amount){
			$vatTable .= '
				<tr>
					<td colspan="5"></td>
					<td class="shopContentfontR">' . $_vat . '&nbsp;%</td>
					<td class="shopContentfontR">' . we_base_util::formatNumber($_amount) . $waehr . '</td>
				</tr>' . "\n";
		}
	}

	$parts[] = array(
		'html' => '
			<table class="defaultfont" width="680">
			<tr>
				<th>' . g_l('modules_shop', '[anual]') . '</th>
				<th>' . ($selectedMonth ? g_l('modules_shop', '[monat]') : '' ) . '</th>
				<th>' . g_l('modules_shop', '[anzahl]') . '</th>
				<th>' . g_l('modules_shop', '[unbearb]') . '</th>
				<th>' . g_l('modules_shop', '[schonbezahlt]') . '</th>
				<th>' . g_l('modules_shop', '[unbezahlt]') . '</th>
				<th class="shopContentfontR">' . g_l('modules_shop', '[umsatzgesamt]') . '</th>
			</tr>' . "\n" . '
			<tr class="shopContentfont">
				<td>' . $selectedYear . '</td>
				<td>' . ($selectedMonth > 0 ? $selectedMonth : '' ) . '</td>
				<td>' . $amountOrders . '</td>
				<td class="npshopContentfontR">' . ($amountOrders - $editedOrders) . '</td>
				<td>' . we_base_util::formatNumber($payed) . $waehr . '</td>
				<td class="npshopContentfontR">' . we_base_util::formatNumber($unpayed) . $waehr . '</td>
				<td class="shopContentfontR">' . we_base_util::formatNumber($total) . $waehr . '</td>
			</tr>' . "\n" .
		$vatTable . '</table>' . "\n",
		'space' => 0
	);

	$headline = array(
		array("dat" => getTitleLink(g_l('modules_shop', '[bestellung]'), 'IntOrderID')),
		array("dat" => g_l('modules_shop', '[ArtName]')), // 'shoptitle'
		array("dat" => g_l('modules_shop', '[anzahl]')),
		array("dat" => getTitleLink(g_l('modules_shop', '[artPrice]'), 'Price')),
		array("dat" => g_l('modules_shop', '[Gesamt]')),
		array("dat" => getTitleLink(g_l('modules_shop', '[artOrdD]'), 'DateOrder')),
		array("dat" => getTitleLink(g_l('modules_shop', '[ArtID]'), 'IntArticleID')),
		array("dat" => getTitleLink(g_l('modules_shop', '[artPay]'), 'DatePayment')),
	);
	$content = array();

	// we need functionalitty to order these

	/* if(isset(REQUEST['orderBy']) && REQUEST['orderBy']){
	  usort($orderRows, 'orderBy');
	  } */

	foreach($orderRows as $orderRow){

		$orderData = $orderRow['orderArray'];
		$articleData = $orderRow['articleArray'];

		$variantStr = '';
		if(!empty($articleData['WE_VARIANT'])){
			$variantStr = '<br /><strong>' . g_l('modules_shop', '[variant]') . ': ' . $articleData['WE_VARIANT'] . '</strong>';
		}

		$customFields = '';
		if(!empty($articleData[WE_SHOP_ARTICLE_CUSTOM_FIELD])){
			$customFields = we_html_element::htmlBr();
			foreach($articleData[WE_SHOP_ARTICLE_CUSTOM_FIELD] as $key => $val){
				$customFields .= $key . '=' . $val . we_html_element::htmlBr();
			}
		}

		$content[] = array(
			array('dat' => '<a href="javascript:we_cmd(\'openOrder\',' . $orderRow['IntOrderID'] . ',\'shop\',\'' . SHOP_TABLE . '\');">' . $orderRow['IntOrderID'] . '</a>'),
			array('dat' => $orderRow[WE_SHOP_TITLE_FIELD_NAME] . '<span class="small">' . $variantStr . ' ' . $customFields . '</span>'),
			array('dat' => $orderRow['IntQuantity']),
			array('dat' => we_base_util::formatNumber($orderRow['Price']) . $waehr),
			array('dat' => we_base_util::formatNumber($orderRow['articleSum']) . $waehr),
			array('dat' => $orderRow['formatDateOrder']),
			array('dat' => $orderRow['IntArticleID']),
			array('dat' => ($orderRow['DatePayment'] ? $orderRow['formatDatePayment'] : ( $orderRow['DateCancellation'] ? '<span class="npshopContentfontR">' . g_l('modules_shop', '[artCanceled]') . '</span>' : '<span class="npshopContentfontR">' . g_l('modules_shop', '[artNPay]') . '</span>'))),
		);
	}

	$parts[] = array(
		'html' => we_html_tools::htmlDialogBorder3(670, 100, $content, $headline),
		'space' => 0,
		'noline' => true
	);

	$parts[] = array(
		'html' => we_shop_pager::getStandardPagerHTML(getPagerLink(), $actPage, $nrOfPage, $maxRows),
		'space' => 0
	);
} else {
	$parts[] = array(
		'html' => g_l('modules_shop', '[NoRevenue]') . ' (' . ($selectedMonth > 0 ? g_l('modules_shop', '[month][' . $selectedMonth . ']') . ' ' : '') . $selectedYear . ')',
		'space' => 0
	);
}

echo we_html_multiIconBox::getHTML('revenues', $parts, 30, '', -1, '', '', false, sprintf(g_l('tabs', '[module][revenueTotal]'), $selectedYear));
?>
</form>
</body>
</html>
