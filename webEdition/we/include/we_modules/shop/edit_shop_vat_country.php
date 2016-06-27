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

echo we_html_tools::getHtmlTop() .
 STYLESHEET;

$jsFunction = '
function we_submitForm(url){
var f = self.document.we_form;
	if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	f.action = url;
	f.method = "post";

	f.submit();
	return true;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd(){
var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "close":
			window.close();
			break;
		case "save":
			we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
			break;
		default:
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}';



// initialise the vatRuleObject
if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'saveVatRule'){

	// initialise the vatRule by request
	$weShopVatRule = we_shop_vatRule::initByRequest();
	$weShopVatRule->save();
} else {
	$weShopVatRule = we_shop_vatRule::getShopVatRule();
}

// array with all rules

$customerTableFields = $DB_WE->metadata(CUSTOMER_TABLE);
foreach($customerTableFields as $tblField){
	$selectFields[$tblField['name']] = $tblField['name'];
}

// default value f�r mwst
$defaultInput = we_class::htmlSelect('defaultValue', array('true' => 'true', 'false' => 'false'), 1, $weShopVatRule->defaultValue);
// select field containing land
$countrySelect = we_class::htmlSelect('stateField', $selectFields, 1, $weShopVatRule->stateField);
$countrySelectISO = we_html_forms::checkboxWithHidden($weShopVatRule->stateFieldIsISO, 'stateFieldIsISO', g_l('modules_shop', '[preferences][ISO-Kodiert]'), false, "defaultfont");
// states which must always pay vat

$textAreaLiableStates = we_class::htmlTextArea('liableToVat', 3, 30, implode("\n", $weShopVatRule->liableToVat));
// states which must never pay vat

$textAreaNotLiableStates = we_class::htmlTextArea('notLiableToVat', 3, 30, implode("\n", $weShopVatRule->notLiableToVat));
// states which must only pay under certain circumstances
// if we make more rules possible - adjust here
$actCondition = $weShopVatRule->conditionalRules[0];

$conditionTextarea = we_class::htmlTextArea('conditionalStates[]', 3, 30, implode("\n", $actCondition['states']));
$conditionField = we_class::htmlSelect('conditionalCustomerField[]', $selectFields, 1, $actCondition['customerField']);
$conditionSelect = we_class::htmlSelect('conditionalCondition[]', array('is_empty' => g_l('modules_shop', '[vat_country][condition_is_empty]'), 'is_set' => g_l('modules_shop', '[vat_country][condition_is_set]')), 1, $actCondition['condition']);
$conditionReturn = we_class::htmlSelect('conditionalReturn[]', array('false' => 'false', 'true' => 'true'), 1, $actCondition['returnValue']);

$parts = array(
	array(
		'headline' => g_l('modules_shop', '[vat_country][defaultReturn]'),
		'space' => we_html_multiIconBox::SPACE_BIG,
		'html' => $defaultInput,
		'noline' => 1
	),
	array(
		'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[vat_country][defaultReturn_desc]'), we_html_tools::TYPE_INFO, 600),
	),
	array(
		'headline' => g_l('modules_shop', '[vat_country][stateField]') . ':',
		'space' => we_html_multiIconBox::SPACE_BIG,
		'html' => $countrySelect . $countrySelectISO,
		'noline' => 1
	),
	array(
		'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[vat_country][stateField_desc]'), we_html_tools::TYPE_INFO, 600),
	),
	array(
		'headline' => g_l('modules_shop', '[vat_country][statesLiableToVat]') . ':',
		'space' => we_html_multiIconBox::SPACE_BIG,
		'html' => $textAreaLiableStates,
		'noline' => 1
	),
	array(
		'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[vat_country][statesLiableToVat_desc]'), we_html_tools::TYPE_INFO, 600),
	),
	array(
		'headline' => g_l('modules_shop', '[vat_country][statesNotLiableToVat]') . ':',
		'space' => we_html_multiIconBox::SPACE_BIG,
		'html' => $textAreaNotLiableStates,
		'noline' => 1
	),
	array(
		'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[vat_country][statesNotLiableToVat_desc]'), we_html_tools::TYPE_INFO, 600),
	),
	array(
		'headline' => g_l('modules_shop', '[vat_country][statesSpecialRules]') . ':',
		'space' => we_html_multiIconBox::SPACE_BIG,
		'html' => $conditionTextarea,
		'noline' => 1
	),
	array(
		'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[vat_country][statesSpecialRules_desc]'), we_html_tools::TYPE_INFO, 600),
		'noline' => 1
	),
	array(
		'headline' => g_l('modules_shop', '[vat_country][statesSpecialRules_condition]'),
		'space' => we_html_multiIconBox::SPACE_BIG,
		'html' => $conditionField . ' ' . $conditionSelect,
		'noline' => 1
	),
	array(
		'headline' => g_l('modules_shop', '[vat_country][statesSpecialRules_result]'),
		'space' => we_html_multiIconBox::SPACE_BIG,
		'html' => $conditionReturn
	)
);

echo we_html_element::jsElement($jsFunction);
?></head>
<body class="weDialogBody" onload="window.focus();">
	<form name="we_form" method="post">
		<input type="hidden" name="we_cmd[0]" value="saveVatRule" />
<?= we_html_multiIconBox::getHTML('weShopCountryVat', $parts, 30, we_html_button::position_yes_no_cancel(
		we_html_button::create_button(we_html_button::SAVE, 'javascript:we_cmd(\'save\');'), '', we_html_button::create_button(we_html_button::CANCEL, 'javascript:we_cmd(\'close\');')
	), -1, '', '', false, g_l('modules_shop', '[vat_country][box_headline]'), '', 741
);
?>
	</form>
</body>
</html>
