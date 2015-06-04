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

echo we_html_tools::getHtmlTop() .
 STYLESHEET;

/// config
$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"'));

$waehr = '&nbsp;' . oldHtmlspecialchars($feldnamen[0]);
$numberformat = $feldnamen[2];
$mwst = ($feldnamen[1]) ? (($feldnamen[1] / 100) + 1) : "";
$year = abs(substr($_REQUEST["mid"], -4));
$month = abs(str_replace($year, "", $_REQUEST["mid"]));

$bezahlt = $unbezahlt = $r = $f = 0;


$DB_WE->query('SELECT IntOrderID, Price, IntQuantity, DateShipping,DatePayment FROM ' . SHOP_TABLE . ' WHERE DateOrder>="' . $year . (($month < 10) ? "0" . $month : $month) . '01000000" AND DateOrder<="' . $year . (($month < 10) ? '0' . $month : $month) . date('t', mktime(0, 0, 0, $month, 1, $year)) . '000000" ORDER BY IntOrderID');
while($DB_WE->next_record()){
	if($DB_WE->f('DatePayment') != 0){
		if(!isset($bezahlt)){
			$bezahlt = 0;
		}
		$bezahlt += ($DB_WE->f("IntQuantity") * $DB_WE->f("Price")); //bezahlt
	} else {
		if(!isset($unbezahlt)){
			$unbezahlt = 0;
		}
		$unbezahlt += ($DB_WE->f("IntQuantity") * $DB_WE->f("Price")); //unbezahlt
	}

	if(isset($orderid) ? $DB_WE->f("IntOrderID") != $orderid : $DB_WE->f("IntOrderID")){
		if($DB_WE->f("DateShipping") != 0){
			$r++; //bearbeitet
		} else {
			$f++; //unbearbeitet
		}
	}
	$orderid = $DB_WE->f("IntOrderID");
}

$mwst = max($mwst ,1);
$info = g_l('modules_shop', '[anzahl]') . ": <b>" . ($f + $r) . "</b><br/>" . g_l('modules_shop', '[unbearb]') . ": " . (($f) ? $f : "0");
$stat = g_l('modules_shop', '[umsatzgesamt]') . ": <b>" . we_util_Strings::formatNumber(($bezahlt + $unbezahlt) * $mwst) . " $waehr </b><br/><br/>" . g_l('modules_shop', '[schonbezahlt]') . ": " . we_util_Strings::formatNumber($bezahlt * $mwst) . " $waehr <br/>" . g_l('modules_shop', '[unbezahlt]') . ": " . we_util_Strings::formatNumber($unbezahlt * $mwst) . " $waehr";
echo we_html_element::jsScript(JS_DIR . 'windows.js');
?>
</head>

<body class="weEditorBody" onunload="doUnload()"><?php
	$parts = array(
		array(
			"headline" => g_l('modules_shop', '[month][' . $month . ']') . " " . $year,
			"html" => $info,
			"space" => 170
		),
		array(
			"headline" => g_l('modules_shop', '[stat]'),
			"html" => $stat,
			"space" => 170
		)
	);

	echo we_html_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('tabs', '[module][overview]'));
	?></body></html>