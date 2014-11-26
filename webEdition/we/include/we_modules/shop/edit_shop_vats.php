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

switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case 'saveVat':
		$province = we_base_request::_(we_base_request::STRING, 'weShopVatProvince');
		$territory = we_base_request::_(we_base_request::STRING, 'weShopVatCountry') . ($province ? '-' . $province : '');

		$weShopVat = new we_shop_vat(we_base_request::_(we_base_request::INT, 'weShopVatId'), we_base_request::_(we_base_request::STRING, 'weShopVatText'), we_base_request::_(we_base_request::RAW, 'weShopVatVat'), we_base_request::_(we_base_request::RAW, 'weShopVatStandard'), $territory, we_base_request::_(we_base_request::STRING, 'weShopVatTextProvince'));

		if(($newId = we_shop_vats::saveWeShopVAT($weShopVat))){
			$weShopVat->id = $newId;
			unset($newId);
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
$jsFunction = '
var isGecko = ' . (we_base_browserDetect::isGecko() ? 'true' : 'false') . ';

' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? 'document.addEventListener("keyup",doKeyDown,true);' : 'document.onkeydown = doKeyDown;') . '

function doKeyDown(e) {
	var key;

' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? 'key = e.keyCode;' : 'key = event.keyCode;') . '

	switch (key) {
		case 27:
			top.close();
			break;	}
}

function IsDigit(e) {
	var key;

' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? 'key = e.charCode;' : 'key = event.keyCode;') . '
	return ( (key == 46) || ((key >= 48) && (key <= 57)) || (key == 0) || (key == 13)  || (key == 8) || (key <= 63235 && key >= 63232) || (key == 63272));
}


function changeFormTextField(theId, newVal) {
	document.getElementById(theId).value = newVal;
}

function changeFormSelect(theId, newVal) {

	elem = document.getElementById(theId);

	for (i=0; i<elem.options.length; i++) {
		if ( elem.options[i].value == newVal ) {
			elem.selectedIndex = i;
		}
	}
}


function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd(){

	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?";
	for(var i = 0; i < arguments.length; i++){
			url += "we_cmd["+i+"]="+encodeURI(arguments[i]);
			if(i < (arguments.length - 1)){
					url += "&";
			}
	}

	switch (arguments[0]) {

		case "save":
			we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
		break;

		case "close":
			window.close();
		break;

		case "edit":

			elem = document.getElementById("editShopVatForm");
			if (elem.style.display == "none") {
				elem.style.display = "";
			}

			if (theVat = allVats["vat_" + arguments[1]]) {
				changeFormTextField("weShopVatId", theVat["id"]);
				changeFormTextField("weShopVatText", theVat["text"]);
				changeFormTextField("weShopVatVat", theVat["vat"]);
				changeFormSelect("weShopVatStandard", theVat["standard"]);
				changeFormSelect("weShopVatCountry", theVat["country"]);
				changeFormTextField("weShopVatProvince", theVat["province"]);
				changeFormTextField("weShopVatTextProvince", theVat["textProvince"]);
			}
		break;

		case "delete":
			if (confirm("' . g_l('modules_shop', '[vat][js_confirm_delete]') . '")) {
				document.location = "' . $_SERVER['SCRIPT_NAME'] . '?we_cmd[0]=deleteVat&weShopVatId=" + arguments[1];
			}
		break;

		case "addVat":
			elem = document.getElementById("editShopVatForm");
			if (elem.style.display == "none") {
				elem.style.display = "";
			}
			if (theVat = allVats["vat_0"]) {
				changeFormTextField("weShopVatId", theVat["id"]);
				changeFormTextField("weShopVatText", theVat["text"]);
				changeFormTextField("weShopVatVat", theVat["vat"]);
				changeFormSelect("weShopVatStandard", theVat["standard"]);
				changeFormSelect("weShopVatCountry", theVat["country"]);
				changeFormTextField("weShopVatProvince", theVat["province"]);
				changeFormTextField("weShopVatTextProvince", theVat["textProvince"]);
			}

		break;

		default :
	break;
	}
}

function we_submitForm(url){

		var f = self.document.we_form;

	f.action = url;
	f.method = "post";

	f.submit();
}';


// at top of page show a table with all actual vats
$allVats = we_shop_vats::getAllShopVATs();

$parts = array();
$vatJavaScript = '';
$vatTable = '';

$vatJavaScript = '
	var allVats = new Object();
	allVats["vat_0"] = {"id":"0","text":"' . g_l('modules_shop', '[vat][new_vat_name]') . '","vat":"19","standard":"0","country":"DE","province":"", "textProvince":""};';

if(!empty($allVats)){

	$vatTable = '
	<div style="height:300px; width: 550px; padding-right: 40px; overflow-y:scroll">
		<table class="defaultfont" width="100%">
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
			<td>' . we_html_button::create_button('image:btn_edit_edit', 'javascript:we_cmd(\'edit\',\'' . $_weShopVat->id . '\');') . '</td>
			<td>' . we_html_button::create_button('image:btn_function_trash', 'javascript:we_cmd(\'delete\',\'' . $_weShopVat->id . '\');') . '</td>
		</tr>';
		unset($_weShopVat);
	}

	$vatTable .= '</table>
	</div>';
}

$plusBut = we_html_button::create_button('image:btn_function_plus', 'javascript:we_cmd(\'addVat\')');

echo we_html_element::jsElement(
		$vatJavaScript .
		$jsFunction .
		(isset($jsMessage) ? we_message_reporting::getShowMessageCall($jsMessage, $jsMessageType) : '')) . "
	</head>
<body class=\"weDialogBody\" onload='window.focus();'>";

$parts[] = array(
	'space' => 0,
	'html' => $vatTable
);
$parts[] = array(
	'space' => 0,
	'html' => $plusBut
);

// formular to edit the vats
$selPredefinedNames = we_html_tools::htmlSelect(
				'sel_predefinedNames', array_merge(array('---'), we_shop_vat::getPredefinedNames()), 1, 0, false, array('onchange' => "var elem=document.getElementById('weShopVatText');elem.value=this.options[this.selectedIndex].text;this.selectedIndex=0")
);

$formVat = '
<form name="we_form" method="post">
<input type="hidden" name="weShopVatId" id="weShopVatId" value="' . $weShopVat->id . '" />
<input type="hidden" name="we_cmd[0]" value="saveVat" />
<table class="defaultfont" id="editShopVatForm" style="display:none;">
<tr>
	<td colspan="2"><strong>' . g_l('modules_shop', '[vat][vat_edit_form_headline]') . '</strong></td>
</tr>
<tr>
	<td height="10"></td>
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
	<td>
		<input size="3" style="width:30px" class="wetextinput" type="text" id="weShopVatProvince" name="weShopVatProvince" value="" />:
		<input style="width:160px" class="wetextinput" type="text" id="weShopVatTextProvince" name="weShopVatTextProvince" value="" />
	</td>
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
	<td width="100">&nbsp;</td>
	<td>' . we_html_button::create_button('save', 'javascript:we_cmd(\'save\');') . '</td>
</tr>
</table>
</form>
';

$parts[] = array(
	'html' => $formVat,
	'space' => 0
);

echo we_html_multiIconBox::getHTML(
		'weShopVates', "100%", $parts, 30, we_html_button::position_yes_no_cancel(
				'', '', we_html_button::create_button('close', 'javascript:we_cmd(\'close\');')
		), -1, '', '', false, g_l('modules_shop', '[vat][vat_edit_form_headline_box]'), "", ''
) . '
</body></html>';
