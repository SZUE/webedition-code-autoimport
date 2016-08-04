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
$protect = we_base_moduleInfo::isActive(we_base_moduleInfo::SHOP) && we_users_util::canEditModule(we_base_moduleInfo::SHOP) ? null : array(false);
we_html_tools::protect($protect);

echo we_html_tools::getHtmlTop();

if(($fname = we_base_request::_(we_base_request::STRING, "fieldForname"))){ //	save data in arrays ..
	$DB_WE->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter(array(
			'pref_value' => $fname . "|" . we_base_request::_(we_base_request::STRING, "fieldSurname") . "|" . we_base_request::_(we_base_request::STRING, "fieldStreet") . "|" . we_base_request::_(we_base_request::STRING, "fieldZip") . "|" . we_base_request::_(we_base_request::STRING, "fieldCity") . "|" . we_base_request::_(we_base_request::STRING, "lc") . "|" . we_base_request::_(we_base_request::STRING, "ppB") . "|" . we_base_request::_(we_base_request::STRING, "psb") . "|" . we_base_request::_(we_base_request::STRING, "lcS") . "|" . we_base_request::_(we_base_request::STRING, "spAID") . "|" . we_base_request::_(we_base_request::STRING, "spB") . "|" . we_base_request::_(we_base_request::STRING, "spC") . "|" . we_base_request::_(we_base_request::STRING, "spD") . "|" . we_base_request::_(we_base_request::STRING, "spCo") . "|" . we_base_request::_(we_base_request::STRING, "spPS") . "|" . we_base_request::_(we_base_request::STRING, "spcmdP") . "|" . we_base_request::_(we_base_request::STRING, "spconfP") . "|" . we_base_request::_(we_base_request::STRING, "spdesc") . "|" . we_base_request::_(we_base_request::STRING, "fieldEmail"),
			'tool' => 'shop',
			'pref_name' => 'payment_details',
	)));


	//	Close window when finished
	echo we_html_element::jsElement('self.close();');
	exit;
}

//	NumberFormat - currency and taxes
$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="payment_details"'));

for($i = 0; $i <= 18; $i++){
	$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
}

$row = 0;
//hier
$Parts = [];

if(defined('CUSTOMER_TABLE')){
	$htmlTable = new we_html_table(array('class' => 'default withSpace', 'width' => "100%"), 4, 3);
	$htmlTable->setCol($row++, 0, array('colspan' => 4, 'class' => 'defaultfont'), g_l('modules_shop', '[FormFieldsTxt]'));

	$custfields = [];


	$DB_WE->query("SHOW FIELDS FROM " . CUSTOMER_TABLE);
	while($DB_WE->next_record()){
		$custfields[$DB_WE->f("Field")] = $DB_WE->f("Field");
	}

	$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[fieldForname]'));
	$htmlTable->setCol($row, 1, array('style' => 'width:10px;'));
	$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('fieldForname', $custfields, 1, $feldnamen[0]));

	$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[fieldSurname]'));
	$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('fieldSurname', $custfields, 1, $feldnamen[1]));

	$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[fieldStreet]'));
	$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('fieldStreet', $custfields, 1, $feldnamen[2]));

	$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[fieldZip]'));
	$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('fieldZip', $custfields, 1, $feldnamen[3]));

	$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[fieldCity]'));
	$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('fieldCity', $custfields, 1, $feldnamen[4]));


	$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[fieldEmail]'));
	$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('fieldEmail', array_merge(array(""), $custfields), 1, $feldnamen[18]));

	$Parts[] = array("html" => $htmlTable->getHtml());
}

// PayPal
$htmlTable = new we_html_table(array('class' => 'default withSpace', 'width' => "100%"), 4, 3);
$row = 0;
$htmlTable->setCol($row++, 0, array('class' => 'weDialogHeadline', 'colspan' => 4), g_l('modules_shop', '[paypal][name]'));

$list1 = array("AI" => "Anguilla", "AR" => "Argentina", "AU" => "Australia", "AT" => "Austria", "BE" => "Belgium", "BR" => "Brazil", "CA" => "Canada", "CL" => "Chile", "CN" => "China", "CR" => "Costa Rica", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DO" => "Dominican Rep.", "EC" => "Equador", "EE" => "Estonia", "FI" => "Finland", "FR" => "France", "DE" => "Deutschland", "GR" => "Greece", "HK" => "Hong Kong");
$list2 = array("HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "IE" => "Ireland", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "LV" => "Latvia", "LT" => "Lithuania", "LU" => "Luxemburg", "MY" => "Malaysia", "MT" => "Malta", "MX" => "Mexico");
$list3 = array("NL" => "Netherlands", "NZ" => "New Zealand", "NO" => "Norway", "PL" => "Poland", "PT" => "Portugal", "SG" => "Singapore", "SK" => "Slovakia", "ZA" => "South Afrika", "KR" => "South Korea", "ES" => "Spain", "SE" => "Sweden", "CH" => "Switzerland", "TW" => "Taiwan", "TH" => "Thailand", "TR" => "Turkey", "GB" => "United Kingdom", "United States" => "US", "Uruguay" => "UY", "Venezuela" => "VE");
$list = array_merge($list1, $list2, $list3);

$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[lc]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('lc', $list, 1, $feldnamen[5]) . '<span class="small">&nbsp;' . g_l('modules_shop', '[paypalLcTxt]') . ' </span>');

$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[paypalbusiness]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlTextInput("ppB", 30, $feldnamen[6], "", "", "text", 128) . '<span class="small">&nbsp;' . g_l('modules_shop', '[paypalbTxt]') . ' </span>');

$paypalPV = array("default" => "PayPal-Shop", "test" => "Sandbox (Test) ");
$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[paypalSB]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('psb', $paypalPV, 1, $feldnamen[7]) . '<span class="small">&nbsp;' . g_l('modules_shop', '[paypalSBTxt]') . ' </span>');

$Parts[] = array("html" => $htmlTable->getHtml());

// saferpay
$htmlTable = new we_html_table(array('class' => 'default withSpace', 'width' => "100%"), 10, 3);
$row = 0;
$htmlTable->setCol($row++, 0, array('class' => 'weDialogHeadline', 'colspan' => 4), g_l('modules_shop', '[saferpay]'));

$saferPayLang = array("en" => "english", "de" => "deutsch", "fr" => "francais", "it" => "italiano");
$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[saferpayTermLang]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('lcS', $saferPayLang, 1, $feldnamen[8]) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpayLcTxt]') . ' </span>');

$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[saferpayID]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlTextInput("spAID", 30, $feldnamen[9], "", "", "text", 128) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpayIDTxt]') . ' </span>');

$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[saferpaybusiness]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlTextInput("spB", 30, $feldnamen[10], "", "", "text", 128) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpaybTxt]') . ' </span>');

$saferPayCollect = array("no" => g_l('modules_shop', '[saferpayNo]'), "yes" => g_l('modules_shop', '[saferpayYes]'));
$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[saferpayAllowCollect]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('spC', $saferPayCollect, 1, $feldnamen[11]) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpayAllowCollectTxt]') . ' </span>');

$saferPayDelivery = array("no" => g_l('modules_shop', '[saferpayNo]'), "yes" => g_l('modules_shop', '[saferpayYes]'));
$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[saferpayDelivery]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('spD', $saferPayDelivery, 1, $feldnamen[12]) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpayDeliveryTxt]') . ' </span>');

$saferPayConfirm = array("no" => g_l('modules_shop', '[saferpayNo]'), "yes" => g_l('modules_shop', '[saferpayYes]'));
$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[saferpayUnotify]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('spCo', $saferPayConfirm, 1, $feldnamen[13]) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpayUnotifyTxt]') . ' </span>');

$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[saferpayProviderset]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlTextInput("spPS", 30, $feldnamen[14], "", "", "text", 128) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpayProvidersetTxt]') . ' </span>');

$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[saferpayCMDPath]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlTextInput("spcmdP", 30, $feldnamen[15], "", "", "text", 128) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpayCMDPathTxt]') . ' </span>');

$htmlTable->setCol($row, 0, ['class' => 'defaultfont'], g_l('modules_shop', '[saferpayconfPath]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlTextInput("spconfP", 30, $feldnamen[16], "", "", "text", 128) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpayconfPathTxt]') . ' </span>');

$htmlTable->setCol($row, 0, array('class' => 'defaultfont', 'style' => 'padding-bottom:20px;'), g_l('modules_shop', '[saferpaydesc]'));
$htmlTable->setColContent($row++, 2, we_class::htmlTextArea("spdesc", 2, 30, $feldnamen[17]) . '<span class="small">&nbsp;' . g_l('modules_shop', '[saferpaydescTxt]') . ' </span>');

$Parts[] = array("html" => $htmlTable->getHtml());

$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, "javascript:document.we_form.submit();"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
);

echo '
</head>
<body class="weDialogBody" onload="self.focus();">


<form name="we_form" method="post" style="margin-top:16px;">
' . we_html_multiIconBox::getHTML('', $Parts, 30, $buttons, -1, '', '', false, g_l('modules_shop', '[paymentP]'), '', '', 'auto') . '</form>
</body></html>';
