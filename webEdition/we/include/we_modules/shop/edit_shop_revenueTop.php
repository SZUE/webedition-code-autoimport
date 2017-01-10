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
$protect = we_base_moduleInfo::isActive(we_base_moduleInfo::SHOP) && we_users_util::canEditModule(we_base_moduleInfo::SHOP) ? null : [false];
we_html_tools::protect($protect);

$selectedYear = we_base_request::_(we_base_request::INT, 'ViewYear', date('Y'));
$selectedMonth = we_base_request::_(we_base_request::INT, 'ViewMonth', 1);
$orderBy = we_base_request::_(we_base_request::STRING, 'orderBy', 'ID');
$orderDesc = we_base_request::_(we_base_request::BOOL, "orderDesc");
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
	$href = $_SERVER['SCRIPT_NAME'] .
		'?ViewYear=' . $GLOBALS['selectedYear'] .
		'&ViewMonth=' . $GLOBALS['selectedMonth'] .
		'&orderBy=' . $orderKey .
		'&actPage=' . $GLOBALS['actPage'] .
		( ($GLOBALS['orderBy'] == $orderKey && !$desc) ? '&orderDesc=true' : '' );

	return '<span onclick="document.location=\'' . $href . '\';">' . $text . '</span>' . ($GLOBALS['orderBy'] == $orderKey ? ' <i class="fa fa-sort-' . ($desc ? 'desc' : 'asc') . ' fa-lg"></i>' : '<i class="fa fa-sort fa-lg"></i>');
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
	$opts = [];

	while($yearNow > $yearStart){
		$opts[$yearNow] = $yearNow;
		$yearNow--;
	}
	return we_html_tools::htmlSelect($select_name, $opts, 1, we_base_request::_(we_base_request::INT, $select_name, ''), false, ['id' => $select_name]);
}

function monthSelect($select_name, $selectedMonth){
	$opts = g_l('modules_shop', '[month]');
	$opts[0] = '-';
	ksort($opts, SORT_NUMERIC);
	return we_html_tools::htmlSelect($select_name, $opts, 1, $selectedMonth, false, ['id' => $select_name]);
}

$mon = we_base_request::_(we_base_request::INT, 'ViewMonth');

// get some preferences!
$feldnamen = explode('|', f('SELECT pref_value from ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"'));
$waehr = "&nbsp;" . oldHtmlspecialchars($feldnamen[0]);
$numberformat = $feldnamen[2];
$classid = (isset($feldnamen[3]) ? $feldnamen[3] : '');
$defaultVat = $feldnamen[1] ?: 0;

if(!isset($nrOfPage)){

	$nrOfPage = isset($feldnamen[4]) ? $feldnamen[4] : 20;
}
if($nrOfPage === "default"){
	$nrOfPage = 20;
}

$parts = [
	[
		'headline' => '<label for="ViewYear">' . g_l('modules_shop', '[selectYear]') . '</label>',
		'html' => yearSelect("ViewYear"),
		'space' => we_html_multiIconBox::SPACE_MED2,
		'noline' => 1
	], [
		'headline' => '<label for="ViewMonth">' . g_l('modules_shop', '[selectMonth]') . '</label>',
		'html' => monthSelect("ViewMonth", $selectedMonth),
		'space' => we_html_multiIconBox::SPACE_MED2,
		'noline' => 1
	], [
		'headline' => we_html_button::create_button(we_html_button::SELECT, "javascript:we_submitDateform();"),
		'html' => '',
		'space' => we_html_multiIconBox::SPACE_MED2
	]
];

$to = ($selectedMonth ?
	$selectedYear . '-' . ($selectedMonth + 1) :
	($selectedYear + 1) . '-01');

$queryShopDateCondtion = '(o.DateOrder BETWEEN "' . $selectedYear . '-' . ($selectedMonth ?: '01') . '-01" AND "' . $to . '-01")';

if(($maxRows = f('SELECT COUNT(1) FROM ' . SHOP_ORDER_TABLE . ' o JOIN ' . SHOP_ORDER_ITEM_TABLE . ' oi ON o.ID=oi.orderID WHERE ' . $queryShopDateCondtion, '', $DB_WE))){


	$amountOrders = f('SELECT COUNT(1) FROM ' . SHOP_ORDER_TABLE . ' o WHERE ' . $queryShopDateCondtion, '', $DB_WE);
	$editedOrders = f('SELECT COUNT(1) FROM ' . SHOP_ORDER_TABLE . ' o WHERE ' . $queryShopDateCondtion . ' AND !ISNULL(o.DateShipping)', '', $DB_WE);

	$query = 'SELECT
SUM((oi.Price*oi.quantity*IF(o.pricesNet&&o.calcVat&&IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . '),(1+IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . ')/100),1))) AS brutto,
SUM((oi.Price*oi.quantity*IF(o.pricesNet,1,(1/(1+IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . ')/100))))) AS netto
FROM ' . SHOP_ORDER_TABLE . ' o JOIN ' . SHOP_ORDER_ITEM_TABLE . ' oi ON o.ID=oi.orderID WHERE ' . $queryShopDateCondtion;


	$payed = getHash($query . ' AND o.DatePayment IS NOT NULL AND o.DateCancellation IS NULL');
	//$canceled = f($query . ' AND o.DatePayment IS NULL AND o.DateCancellation IS NOT NULL');
	$unpayed = getHash($query . ' AND o.DatePayment IS NULL AND o.DateCancellation IS NULL');

	$articleVatArray = $DB_WE->getAllFirstq('SELECT oi.Vat, SUM((IF(o.pricesNet,oi.Price*oi.quantity*(IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . ')/100), (oi.Price*oi.quantity)-( (oi.Price*oi.quantity)/(1+ (IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . ')/100) )) ))) FROM tblOrder o JOIN tblOrderItem oi ON o.ID=oi.orderID WHERE ' . $queryShopDateCondtion . ' AND o.DateCancellation IS NULL AND o.calcVat AND IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . ')>0 GROUP BY oi.Vat', false);


	// generate vat table
	if(!empty($articleVatArray)){
		$vatTable = '
			<tr>
				<td colspan="7" class="shopContentfontR" style="padding-top:10px;">' . g_l('modules_shop', '[plusVat]') . ':</td>
			</tr>';
		foreach($articleVatArray as $vat => $amount){
			$vatTable .= '
				<tr>
					<td colspan="5"></td>
					<td class="shopContentfontR">' . $vat . '&nbsp;%</td>
					<td class="shopContentfontR">' . we_base_util::formatNumber($amount) . $waehr . '</td>
				</tr>';
		}
	} else {
		$vatTable = '';
	}

	$parts[] = [
		'html' => '
			<table class="defaultfont" style="width:680px;">
			<tr>
				<th>' . g_l('modules_shop', '[anual]') . '</th>
				<th>' . ($selectedMonth ? g_l('modules_shop', '[monat]') : '' ) . '</th>
				<th>' . g_l('modules_shop', '[anzahl]') . '</th>
				<th>' . g_l('modules_shop', '[unbearb]') . '</th>
				<th>' . g_l('modules_shop', '[schonbezahlt]') . '</th>
				<th>' . g_l('modules_shop', '[unbezahlt]') . '</th>
				<th class="shopContentfontR">' . g_l('modules_shop', '[umsatzgesamt]') . '</th>
			</tr>' . '
			<tr class="shopContentfont">
				<td>' . $selectedYear . '</td>
				<td>' . ($selectedMonth > 0 ? $selectedMonth : '' ) . '</td>
				<td>' . $amountOrders . '</td>
				<td class="defaultfont shopNotPayed">' . ($amountOrders - $editedOrders) . '</td>
				<td>' . we_base_util::formatNumber($payed['netto']) . $waehr . '</td>
				<td class="defaultfont shopNotPayed">' . we_base_util::formatNumber($unpayed['netto']) . $waehr .'</td>
				<td class="shopContentfontR">' . we_base_util::formatNumber($payed['netto'] + $unpayed['netto']) . $waehr . '</td>
			</tr>' .
		$vatTable .
		'<tr><td colspan="7"><hr style="color:black"/></td></tr>'.
		'<tr style="border-top:2px solid black" class="shopContentfont">
				<td colspan="4"></td>
				<td>' . we_base_util::formatNumber($payed['brutto']) . $waehr . '</td>
				<td class="defaultfont shopNotPayed">' . we_base_util::formatNumber($unpayed['brutto']) . $waehr .'</td>
				<td class="shopContentfontR">' . we_base_util::formatNumber($payed['brutto'] + $unpayed['brutto']) . $waehr .'</td>
			</tr>'.
		'</table>',
	];

	$headline = [
		['dat' => getTitleLink(g_l('modules_shop', '[bestellung]'), 'o.ID')],
		['dat' => g_l('modules_shop', '[ArtName]')], // 'shoptitle'
		['dat' => g_l('modules_shop', '[anzahl]')],
		['dat' => getTitleLink(g_l('modules_shop', '[artPrice]'), 'Price')],
		['dat' => g_l('modules_shop', '[Gesamt]')],
		['dat' => getTitleLink(g_l('modules_shop', '[artOrdD]'), 'DateOrder')],
		['dat' => getTitleLink(g_l('modules_shop', '[ArtID]'), 'orderDocID')],
		['dat' => getTitleLink(g_l('modules_shop', '[artPay]'), 'DatePayment')],
	];
	$content = [];

	$DB_WE->query('SELECT o.ID,
	@price:=(oi.Price*IF(o.pricesNet&&o.calcVat&&IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . '),(1+IFNULL(oi.Vat,' . (isset($defaultVat) ? $defaultVat : 0) . ')/100),1)) AS priceToShow,
	(oi.quantity*@price) AS articleSum,
	oi.quantity,
	oi.customFields,
	od.DocID,
	od.title,
	od.variant,
	DATE_FORMAT(DateOrder, "%d.%m.%Y") AS formatDateOrder,
	DATE_FORMAT(DatePayment, "%d.%m.%Y") AS formatDatePayment,
	DateCancellation IS NOT NULL AS isCancelled
FROM ' . SHOP_ORDER_TABLE . ' o JOIN ' . SHOP_ORDER_ITEM_TABLE . ' oi ON o.ID=oi.orderID JOIN ' . SHOP_ORDER_DOCUMENT_TABLE . ' od ON oi.orderDocID=od.ID
WHERE ' .
		$queryShopDateCondtion . '
ORDER BY ' . we_base_request::_(we_base_request::STRING, 'orderBy', 'o.ID') . ($orderDesc ? ' DESC' : '') . ' LIMIT ' . ($actPage * $nrOfPage) . ',' . $nrOfPage);

	while($DB_WE->next_record(MYSQL_ASSOC)){
		$hash = $DB_WE->getRecord();

		$variantStr = ($hash['variant'] ? '<br /><strong>' . g_l('modules_shop', '[variant]') . ': ' . $hash['variant'] . '</strong>' : '');

		if($hash['customFields']){
			$cf = we_unserialize($hash['customFields']);
			$customFields = we_html_element::htmlBr();
			foreach($cf as $key => $val){
				$customFields .= $key . '=' . $val . we_html_element::htmlBr();
			}
		} else {
			$customFields = '';
		}

		$content[] = [
			['dat' => '<a href="javascript:we_cmd(\'openOrder\',' . $hash['ID'] . ',\'shop\',\'' . SHOP_ORDER_TABLE . '\');">' . $hash['ID'] . '</a>'],
			['dat' => $hash['title'] . '<span class="small">' . $variantStr . ' ' . $customFields . '</span>'],
			['dat' => $hash['quantity']],
			['dat' => we_base_util::formatNumber($hash['priceToShow']) . $waehr],
			['dat' => we_base_util::formatNumber($hash['articleSum']) . $waehr],
			['dat' => $hash['formatDateOrder']],
			['dat' => $hash['DocID']],
			['dat' => ($hash['formatDatePayment'] ?: ( $hash['isCancelled'] ? '<span class="defaultfont shopNotPayed">' . g_l('modules_shop', '[artCanceled]') . '</span>' : '<span class="defaultfont shopNotPayed">' . g_l('modules_shop', '[artNPay]') . '</span>'))],
		];
	}


	$parts[] = ['html' => we_html_tools::htmlDialogBorder3(670, $content, $headline),
		'noline' => true
	];

	$parts[] = ['html' => we_shop_pager::getStandardPagerHTML(getPagerLink(), $actPage, $nrOfPage, $maxRows),
	];
} else {
	$parts[] = ['html' => g_l('modules_shop', '[NoRevenue]') . ' (' . ($selectedMonth > 0 ? g_l('modules_shop', '[month][' . $selectedMonth . ']') . ' ' : '') . $selectedYear . ')',
	];
}

echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(WE_JS_MODULES_DIR . 'shop/edit_shop_revenueTop.js'), we_html_element::htmlBody(['class' => "weEditorBody",
		'onload' => "self.focus(); setHeaderTitle('" . ($mon > 0 ? g_l('modules_shop', '[month][' . $mon . ']') . ' ' : '') . we_base_request::_(we_base_request::INT, 'ViewYear') . "');"], '<form>' . we_html_multiIconBox::getHTML('revenues', $parts, 30, '', -1, '', '', false, sprintf(g_l('tabs', '[module][revenueTotal]'), $selectedYear)) . '</form>'));

