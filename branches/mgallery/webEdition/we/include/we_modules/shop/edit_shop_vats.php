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

$saveSuccess = false;
$onsaveClose = we_base_request::_(we_base_request::BOOL, 'onsaveclose', false);
switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case 'saveVat':
		$province = we_base_request::_(we_base_request::STRING, 'weShopVatProvince');
		$territory = we_base_request::_(we_base_request::STRING, 'weShopVatCountry') . ($province ? '-' . $province : '');

		$weShopVat = new we_shop_vat(we_base_request::_(we_base_request::INT, 'weShopVatId'), we_base_request::_(we_base_request::STRING, 'weShopVatText'), we_base_request::_(we_base_request::FLOAT, 'weShopVatVat'), we_base_request::_(we_base_request::FLOAT, 'weShopVatStandard'), $territory, we_base_request::_(we_base_request::STRING, 'weShopVatTextProvince'));

		if(($newId = we_shop_vats::saveWeShopVAT($weShopVat))){
			$weShopVat->id = $newId;
			unset($newId);
			$saveSuccess = true;
			$jsMessage = g_l('modules_shop', '[vat][save_success]');
			$jsMessageType = we_message_reporting::WE_MESSAGE_NOTICE;
		} else {
			$jsMessage = g_l('modules_shop', '[vat][save_error]');
			$jsMessageType = we_message_reporting::WE_MESSAGE_ERROR;
		}

		break;

	case 'deleteVat':
		if(we_shop_vats::deleteVatById(we_base_request::_(we_base_request::INT, 'weShopVatId'))){
			$jsMessage = g_l('modules_shop', '[vat][delete_success]');
			$jsMessageType = we_message_reporting::WE_MESSAGE_NOTICE;
		} else {
			$jsMessage = g_l('modules_shop', '[vat][delete_error]');
			$jsMessageType = we_message_reporting::WE_MESSAGE_ERROR;
		}
		break;
}


if(!isset($weShopVat)){
	$weShopVat = new we_shop_vat(0, g_l('modules_shop', '[vat][new_vat_name]'), 19, 0);
}

// at top of page show a table with all actual vats
$allVats = we_shop_vats::getAllShopVATs();

$vatJavaScript = '
top.WE().consts.g_l.shop.vat_confirm_delete="' . g_l('modules_shop', '[vat][js_confirm_delete]') . '";
var SCRIPT_NAME="' . $_SERVER['SCRIPT_NAME'] . '";
var allVats = {
	vat_0: {id:0,text:"' . g_l('modules_shop', '[vat][new_vat_name]') . '",vat:19,standard:0,country:"DE",province:"",textProvince:""}
};';
if($allVats){
	$vatTable = '
	<div style="height:300px; width: 550px; padding-right: 40px; overflow:auto;">
		<table class="defaultfont">
		<tr>
			<td><strong>Id</strong></td>
			<td><strong>' . g_l('modules_shop', '[vat][vat_form_name]') . '</strong></td>
			<td><strong>' . g_l('modules_shop', '[vat][vat_form_vat]') . '</strong></td>
			<td><strong>' . g_l('modules_shop', '[vat][vat_form_StateRegion]') . '</strong></td>
			<td><strong>ISO</strong></td>
			<td><strong>' . g_l('modules_shop', '[vat][vat_form_standard]') . '</strong></td>
		</tr>';

	foreach($allVats as $_weShopVat){
		$vatJavaScript .='
		allVats["vat_' . $_weShopVat->id . '"] = {"id":"' . $_weShopVat->id . '","text":"' . $_weShopVat->getNaturalizedText() . '", "vat":"' . $_weShopVat->vat . '", "standard":"' . ($_weShopVat->standard ? 1 : 0) . '", "territory":"' . $_weShopVat->territory . '", "country":"' . $_weShopVat->country . '", "province":"' . $_weShopVat->province . '", "textProvince":"' . $_weShopVat->textProvince . '"};';

		$vatTable .= '
		<tr>
			<td>' . $_weShopVat->id . '</td>
			<td>' . oldHtmlspecialchars($_weShopVat->getNaturalizedText()) . '</td>
			<td>' . $_weShopVat->vat . '%</td>
			<td>' . $_weShopVat->textTerritory . '</td>
			<td>' . $_weShopVat->territory . '</td>
			<td>' . g_l('global', ($_weShopVat->standard ? '[yes]' : '[no]')) . '</td>
			<td>' . we_html_button::create_button(we_html_button::EDIT, 'javascript:we_cmd(\'edit\',\'' . $_weShopVat->id . '\');') . '</td>
			<td>' . we_html_button::create_button(we_html_button::TRASH, 'javascript:we_cmd(\'delete\',\'' . $_weShopVat->id . '\');') . '</td>
		</tr>';
		unset($_weShopVat);
	}

	$vatTable .= '</table>
	</div>';
} else {
	$vatTable = '';
}

$plusBut = we_html_button::create_button(we_html_button::PLUS, 'javascript:we_cmd(\'addVat\')');

// formular to edit the vats
$selPredefinedNames = we_html_tools::htmlSelect(
		'sel_predefinedNames', array_merge(array('---'), we_shop_vat::getPredefinedNames()), 1, 0, false, array('onchange' => "var elem=document.getElementById('weShopVatText');elem.value=this.options[this.selectedIndex].text;this.selectedIndex=0")
);

$formVat = '
<form name="we_form" method="post">
<input type="hidden" name="weShopVatId" id="weShopVatId" value="' . $weShopVat->id . '" />
<input type="hidden" name="onsaveclose" value="0" />
<input type="hidden" name="we_cmd[0]" value="saveVat" />
<table class="defaultfont" id="editShopVatForm" style="display:none;">
<tr>
	<td colspan="2"><strong>' . g_l('modules_shop', '[vat][vat_edit_form_headline]') . '</strong></td>
</tr>
<tr>
	<td width="100">' . g_l('modules_shop', '[vat][vat_form_name]') . ':</td>
	<td><input class="wetextinput" type="text" id="weShopVatText" name="weShopVatText" value="' . $weShopVat->text . '" />' . $selPredefinedNames . '</td>
</tr>
<tr>
	<td>' . g_l('modules_shop', '[vat][vat_form_vat]') . ':</td>
	<td><input class="wetextinput" type="text" id="weShopVatVat" name="weShopVatVat" value="' . $weShopVat->vat . '" onkeypress="return IsDigit(event);" />%</td>
</tr>

<tr>
	<td>' . g_l('modules_shop', '[vat][vat_edit_form_state]') . ':</td>
	<td>' . we_html_tools::htmlSelectCountry('weShopVatCountry', '', 1, array(), false, array('id' => 'weShopVatCountry'), 200) . '</td>
</tr>
<tr>
	<td>' . g_l('modules_shop', '[vat][vat_edit_form_province]') . ':</td>
	<td>(-<input style="width:6em" class="wetextinput" type="text" id="weShopVatProvince" name="weShopVatProvince" value="" />)</td>
</tr>

<tr>
	<td>' . g_l('modules_shop', '[vat][vat_form_standard]') . ':</td>
	<td><select id="weShopVatStandard" name="weShopVatStandard">
			<option value="1"' . ($weShopVat->standard ? ' selected="selected"' : '') . '>' . g_l('modules_shop', '[vat][vat_edit_form_yes]') . '</option>
			<option value="0"' . ($weShopVat->standard ? '' : ' selected="selected"') . '>' . g_l('modules_shop', '[vat][vat_edit_form_no]') . '</option>
		</select>
	</td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td>' . we_html_button::create_button(we_html_button::SAVE, 'javascript:we_cmd(\'save_notclose\');') . '</td>
</tr>
</table>
</form>';

$parts = array(
	array(
		'space' => 0,
		'html' => $vatTable
	),
	array(
		'space' => 0,
		'html' => $plusBut
	),
	array(
		'html' => $formVat,
		'space' => 0
	),
);

echo we_html_element::jsElement(
	$vatJavaScript .
	(isset($jsMessage) ? we_message_reporting::getShowMessageCall($jsMessage, $jsMessageType) . ($saveSuccess && $onsaveClose ? 'window.close()' : '') : '')) . we_html_element::jsScript(JS_DIR . 'we_modules/shop/edit_shop_vats.js') . "
	</head>
<body class=\"weDialogBody\" onload='window.focus();addListeners();'>" .
 we_html_multiIconBox::getHTML('weShopVates', $parts, 30, we_html_button::position_yes_no_cancel(
		'', '', we_html_button::create_button(we_html_button::CLOSE, 'javascript:we_cmd(\'close\');')
	), -1, '', '', false, g_l('modules_shop', '[vat][vat_edit_form_headline_box]'), "", ''
) . '
</body></html>';
