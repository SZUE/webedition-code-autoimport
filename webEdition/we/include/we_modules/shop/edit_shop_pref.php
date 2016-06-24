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

function prepareFieldname($str){
	return (strpos($str, '_') ?
			substr_replace($str, '/', strpos($str, '_'), 1) :
			$str);
}

we_html_tools::protect();
echo we_html_tools::getHtmlTop() .
 STYLESHEET;


$ignoreFields = explode(',', we_shop_shop::ignoredEditFields);

$customerTableFields = $DB_WE->metadata(CUSTOMER_TABLE);
$selectFields['-'] = '-';
foreach($customerTableFields as $tblField){
	if(!in_array($tblField['name'], $ignoreFields)){
		$selectFields[$tblField['name']] = $tblField['name'];
	}
}

if(($format = we_base_request::_(we_base_request::RAW, "format"))){ //	save data in arrays ..
	$settings = array(
		'shop_location' => we_base_request::_(we_base_request::STRING, 'shoplocation'),
		'category_mode' => we_base_request::_(we_base_request::INT, 'categorymode'),
	);

	foreach($settings as $dbField => $value){
		$DB_WE->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'tool' => "shop",
				'pref_name' => $dbField,
				'pref_value' => $value
		)));
	}

	$DB_WE->query('REPLACE ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter(array(
			'tool' => 'shop',
			'pref_name' => "shop_pref",
			'pref_value' => we_base_request::_(we_base_request::STRING, "waehr") . '|' . we_base_request::_(we_base_request::STRING, "mwst") . '|' . $format . '|' . we_base_request::_(we_base_request::STRING, "classID", 0) . '|' . we_base_request::_(we_base_request::STRING, "pag")
	)));

	$fields['customerFields'] = we_base_request::_(we_base_request::STRING, 'orderfields', []);
	$fields['orderCustomerFields'] = we_base_request::_(we_base_request::STRING, 'ordercustomerfields', []);

	// check if field exists
	$DB_WE->query('REPLACE ' . SETTINGS_TABLE . ' SET ' . we_database_base::arraySetter(array(
			'tool' => 'shop',
			'pref_name' => 'edit_shop_properties',
			'pref_value' => we_serialize($fields, SERIALIZE_JSON)
	)));

	$CLFields['stateField'] = we_base_request::_(we_base_request::RAW, 'stateField', '-');
	$CLFields['stateFieldIsISO'] = we_base_request::_(we_base_request::STRING, 'stateFieldIsISO', 0);
	$CLFields['languageField'] = we_base_request::_(we_base_request::STRING, 'languageField', '-');
	$CLFields['languageFieldIsISO'] = we_base_request::_(we_base_request::RAW, 'languageFieldIsISO', 0);

	// check if field exists
	$DB_WE->query('REPLACE ' . SETTINGS_TABLE . ' SET tool="shop",pref_name="shop_CountryLanguage", pref_value="' . $DB_WE->escape(we_serialize($CLFields)) . '"');
	// Update Country Field in weShopVatRule
	$weShopVatRule = we_shop_vatRule::getShopVatRule();
	$weShopVatRule->stateField = $CLFields['stateField'];
	$weShopVatRule->stateFieldIsISO = $CLFields['stateFieldIsISO'];
	$weShopVatRule->save();
	// Update Language Field in weShopStatusMails
	$weShopStatusMails = we_shop_statusMails::getShopStatusMails();
	$weShopStatusMails->LanguageData['languageField'] = $CLFields['languageField'];
	$weShopStatusMails->LanguageData['languageFieldIsISO'] = $CLFields['languageFieldIsISO'];
	$weShopStatusMails->save();

	//	Close window when finished
	echo we_html_element::jsElement('self.close();');
	exit;
}

$shoplocation = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_location"');
$categorymode = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="category_mode"');

$CLFields = we_unserialize(f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_CountryLanguage"'), array(
	'stateField' => '-',
	'stateFieldIsISO' => 0,
	'languageField' => '-',
	'languageFieldIsISO' => 0
	));


//	generate html-output table
$htmlTable = new we_html_table(array('class' => 'default withBigSpace', 'width' => 410), 10, 3);


//	NumberFormat - currency and taxes
$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE. ' WHERE tool="shop" AND pref_name="shop_pref"'));

$fe = (isset($feldnamen[3]) ? explode(',', $feldnamen[3]) : []);

if(!isset($feldnamen[4])){
	$feldnamen[4] = '-';
}

$row = 0;
//we_html_tools::htmlSelectCountry('weShopVatCountry', '', 1, [], false, array('id' => 'weShopVatCountry'), 200)

$htmlTable->setCol($row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[shopcats][use_shopCats]'));
$htmlTable->setCol($row, 1, array('style' => 'width:10px;'));
$yesno = array(0 => 'false', 1 => 'true');
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('categorymode', $yesno, 1, $categorymode, false, array("id" => "categorymode", "onchange" => "document.getElementById('shop_holders_location').style.display = (this.value == 1 ? '' : 'none'); document.getElementById('shop_holders_location_br').style.display = (this.value == 1 ? '' : 'none');")));
$htmlTable->setRow($row, array('id' => 'shop_holders_location_br', 'style' => 'display:' . ($categorymode ? '' : 'none')));

$htmlTable->setRow($row, array('id' => 'shop_holders_location', 'style' => 'display:' . ($categorymode ? '' : 'none')));
$htmlTable->setCol($row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[shopcats][shopHolderCountry]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelectCountry('shoplocation', '', 1, array($shoplocation), false, array('id' => 'shoplocation'), 280));

$htmlTable->setCol($row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[waehrung]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlTextInput('waehr', 6, $feldnamen[0]));

$htmlTable->setCol($row, 0, array('class' => 'defaultfont', 'style' => 'vertical-align:top'), g_l('modules_shop', '[mwst]'));
$htmlTable->setCol($row++, 2, array('class' => 'defaultfont', 'style' => 'padding-bottom:5px;'), we_html_tools::htmlTextInput('mwst', 6, $feldnamen[1]) . '&nbsp;%');
$htmlTable->setCol($row++, 0, array('colspan' => 3, 'class' => 'small'), we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[mwst_expl]'), we_html_tools::TYPE_INFO, "400", false, 45));

$list = array('german' => 'german', 'english' => 'english', 'french' => 'french', 'swiss' => 'swiss');
$htmlTable->setCol($row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[format]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('format', $list, 1, $feldnamen[2]));


$pager = array('default' => '-', 5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40, 45 => 45, 50 => 50);

$htmlTable->setCol($row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[pageMod]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('pag', $pager, 1, $feldnamen[4]));


if(defined('OBJECT_TABLE')){
	$htmlTable->setCol($row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[classID]'));
	$htmlTable->setColContent($row++, 2, we_html_tools::htmlTextInput('classID', 100, (isset($feldnamen[3]) ? $feldnamen[3] : ''), '', '', 'text', 280) . '<br/><span class="small">&nbsp;' . g_l('modules_shop', '[classIDext]') . ' </span>');
}

// look for all available fields in tblCustomer
$DB_WE->query('SHOW FIELDS FROM ' . CUSTOMER_TABLE);

$extraIgnore = explode(',', we_shop_shop::ignoredExtraShowFields);
$showFields = [];

while($DB_WE->next_record()){
	if(!in_array($DB_WE->f('Field'), $ignoreFields)){
		$showFields[$DB_WE->f('Field')] = prepareFieldname($DB_WE->f('Field'));
	}
}
asort($showFields);
$orderFields = $showFields;
foreach($extraIgnore as $cur){
	unset($showFields[$cur]);
}


//	get the already selected fields ...
$entry = f('SELECT pref_value FROM ' . SETTINGS_TABLE. ' WHERE tool="shop" AND pref_name="edit_shop_properties"');

// ...
if(($fields = we_unserialize($entry))){
	// we have an array with following syntax:
	// array ( 'customerFields' => array('fieldname ...',...)
	//         'orderCustomerFields' => array('fieldname', ...) )
} else {
	t_e('unsupported Shop-Settings found');
}

$htmlTable->setCol($row, 0, array('class' => 'defaultfont', 'style' => 'vertical-align:top'), g_l('modules_shop', '[preferences][customerFields]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('orderfields[]', $showFields, (count($showFields) > 5 ? 5 : count($showFields)), implode(',', $fields['customerFields']), true, [], 'value', 280));

$htmlTable->setCol($row, 0, array('class' => 'defaultfont', 'style' => 'vertical-align:top'), g_l('modules_shop', '[preferences][orderCustomerFields]'));
$htmlTable->setColContent($row++, 2, we_html_tools::htmlSelect('ordercustomerfields[]', $orderFields, min(count($orderFields), 5), implode(',', $fields['orderCustomerFields']), true, [], 'value', 280));

$htmlTable->setCol($row, 0, array('class' => 'defaultfont', 'style' => 'vertical-align:top'), g_l('modules_shop', '[preferences][CountryField]'));

$countrySelect = we_class::htmlSelect('stateField', $selectFields, 1, $CLFields['stateField']);
$countrySelectISO = we_html_forms::checkboxWithHidden($CLFields['stateFieldIsISO'], 'stateFieldIsISO', g_l('modules_shop', '[preferences][ISO-Kodiert]'), false, "defaultfont");

$htmlTable->setCol($row, 0, array('class' => 'defaultfont', 'style' => 'vertical-align:top'), g_l('modules_shop', '[preferences][LanguageField]'));
$languageSelect = we_class::htmlSelect('languageField', $selectFields, 1, $CLFields['languageField']);
$languageSelectISO = we_html_forms::checkboxWithHidden($CLFields['languageFieldIsISO'], 'languageFieldIsISO', g_l('modules_shop', '[preferences][ISO-Kodiert]'), false, "defaultfont");
$htmlTable->setColContent($row++, 2, $languageSelect . '<br/>' . $languageSelectISO);


$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, 'javascript:if(document.getElementById("categorymode").value == 1 && document.getElementById("shoplocation").value === ""){' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[preferences][save_alert]'), we_message_reporting::WE_MESSAGE_ERROR) . '}else{document.we_form.submit();}'), '', we_html_button::create_button(we_html_button::CANCEL, 'javascript:self.close();'));

echo '
	</head>
	<body class="weDialogBody" onload="self.focus();">
	<form name="we_form" method="post" style="margin-left:8px; margin-top:16px;">
	' . we_html_tools::htmlDialogLayout($htmlTable->getHtml(), g_l('modules_shop', '[pref]'), $buttons) . '</form>
 	</body></html>';
