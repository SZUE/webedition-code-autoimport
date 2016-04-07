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

$weShippingControl = we_shop_shippingControl::getShippingControl();

switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case 'newShipping':
		$weShipping = we_shop_shipping::getNewEmptyShipping();
		break;

	case 'editShipping':
		$weShipping = $weShippingControl->getShippingById(we_base_request::_(we_base_request::STRING, 'weShippingId'));
		break;

	case 'deleteShipping':
		$weShippingControl->delete(we_base_request::_(we_base_request::STRING, 'weShippingId'));
		break;

	case 'saveShipping':
		$weShippingControl->setByRequest($_REQUEST); //FIXME: bad this is unchecked!!!
		if($weShippingControl->save()){
			$jsMessage = g_l('modules_shop', '[shipping][save_success]');
			$jsMessageType = we_message_reporting::WE_MESSAGE_NOTICE;
		} else {
			$jsMessage = g_l('modules_shop', '[shipping][save_error]');
			$jsMessageType = we_message_reporting::WE_MESSAGE_ERROR;
		}
		if(($sid = we_base_request::_(we_base_request::STRING, 'weShippingId')) !== false){
			$weShipping = $weShippingControl->getShippingById($sid);
		}
		break;
}

echo we_html_element::jsElement('
function closeOnEscape() {
	return true;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd(){
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "save":
			we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
			break;
		case "close":
			window.close();
			break;
		case "delete":
			if (confirm("' . g_l('modules_shop', '[delete][shipping]') . '")) {
				var we_cmd_field = document.getElementById("we_cmd_field");
				we_cmd_field.value = "deleteShipping";
				we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");

			}
			break;
		case "newEntry":
			document.location = "' . $_SERVER['SCRIPT_NAME'] . '?we_cmd[0]=newShipping";
			break;
		case "addShippingCostTableRow":
			addShippingCostTableRow();
			break;
		case "deleteShippingCostTableRow":
			deleteShippingCostTableRow(args[1]);
			break;
		default :
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
			break;
	}
}

// this is for new entries.
var entryPosition = 0;

function addShippingCostTableRow() {

	tbl = document.getElementById("shippingCostTableEntries");

	entryId = "New" + "" + entryPosition++;

	theNewRow = document.createElement("TR");
	theNewRow.setAttribute("id", "weShippingId_" + entryId);

	var cell1 = document.createElement("TD");
	cell1.innerHTML=\'<input class="wetextinput" type="text" name="weShipping_cartValue[]" size="24" />\';
			var cell2 = document.createElement("TD");
	var cell3 = document.createElement("TD");
	cell3.innerHTML=\'<input class="wetextinput" type="text" name="weShipping_shipping[]" size="24" />\';
	var cell4 = document.createElement("TD");
	var cell5 = document.createElement("TD");

	var tmp=\'' . addslashes(we_html_button::create_button(we_html_button::TRASH, "we_cmd('deleteShippingCostTableRow', 'weShippingId_#####placeHolder#####');")) . '\';

cell5.innerHTML=tmp.replace("#####placeHolder#####",entryId);
	theNewRow.appendChild(cell1);
	theNewRow.appendChild(cell2);
	theNewRow.appendChild(cell3);
	theNewRow.appendChild(cell4);
	theNewRow.appendChild(cell5);

	// append new row
	tbl.appendChild(theNewRow);
}

function deleteShippingCostTableRow(rowId) {
	tbl = document.getElementById("shippingCostTable");
	tableRows = tbl.rows;

	for (i=0;i<tableRows.length;i++) {
		if(rowId == tableRows[i].id) {
			tbl.deleteRow(i);
		}
	}
}

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
}' .
		(isset($jsMessage) ? we_message_reporting::getShowMessageCall($jsMessage, $jsMessageType) : '')
) .
 '</head>';
?>

<body class="weDialogBody" onload="window.focus();">
	<form name="we_form">
		<input type="hidden" id="we_cmd_field" name="we_cmd[0]" value="saveShipping" />';
		<?php
// show shippingControl
// first show fields: country, vat, isNet?

		$customerTableFields = $DB_WE->metadata(CUSTOMER_TABLE);
		$selectFieldsCtl = $selectFieldsVat = $selectFieldsTbl = array();
		foreach($customerTableFields as $tblField){
			$selectFieldsTbl[$tblField['name']] = $tblField['name'];
		}
		$shopVats = we_shop_vats::getAllShopVATs();
		foreach($shopVats as $id => $shopVat){ //Fix #9625 use shopVat->Id as key instead of the sorted array $id!
			$selectFieldsVat[$shopVat->id] = $shopVat->vat . '% - ' . $shopVat->getNaturalizedText() . ' (' . $shopVat->territory . ')';
		}
// selectBox with all existing shippings
// select menu with all available shipping costs
		foreach($weShippingControl->shippings as $key => $shipping){
			$selectFieldsCtl[$key] = $shipping->text;
		}

		$parts = array(
			array(
				'headline' => g_l('modules_shop', '[vat_country][stateField]'),
				'space' => 200,
				'html' => we_class::htmlSelect('stateField', $selectFieldsTbl, 1, $weShippingControl->stateField, false, array(), 'value', 280),
				'noline' => 1
			),
			array(
				'headline' => g_l('modules_shop', '[mwst]'),
				'space' => 200,
				'html' => we_class::htmlSelect('vatId', $selectFieldsVat, 1, $weShippingControl->vatId, false, array(), 'value', 280),
				'noline' => 1
			),
			array(
				'headline' => g_l('modules_shop', '[shipping][prices_are_net]'),
				'space' => 200,
				'html' => we_class::htmlSelect('isNet', array(1 => g_l('global', '[true]'), 0 => g_l('global', '[false]')), 1, $weShippingControl->isNet, false, array(), 'value', 280)
			),
			array(
				'headline' => g_l('modules_shop', '[shipping][insert_packaging]'),
				'space' => 200,
				'html' => '<table class="default defaultfont">
	<tr>
		<td>' . we_class::htmlSelect('editShipping', $selectFieldsCtl, 4, we_base_request::_(we_base_request::RAW, 'weShippingId', ''), false, array('onchange' => 'document.location=\'' . $_SERVER['SCRIPT_NAME'] . '?we_cmd[0]=editShipping&weShippingId=\' + this.options[this.selectedIndex].value;'), 'value', 280) . '</td>
		<td style="width:10px;"></td>
		<td style="vertical-align:top">'
				. we_html_button::create_button('new_entry', 'javascript:we_cmd(\'newEntry\');') .
				'<div style="margin:5px;"></div>' .
				we_html_button::create_button(we_html_button::DELETE, 'javascript:we_cmd(\'delete\')') .
				'</td>
	</tr>
	</table>'
			)
		);


// if a shipping should be edited, show it in a form

		if(isset($weShipping)){ // show the shipping which must be edited
			$parts[] = array(
				'headline' => g_l('modules_shop', '[shipping][name]'),
				'space' => 200,
				'html' => we_class::htmlTextInput('weShipping_text', 24, $weShipping->text) . we_html_tools::hidden('weShippingId', $weShipping->id),
				'noline' => 1
			);
			$parts[] = array(
				'headline' => g_l('modules_shop', '[shipping][countries]'),
				'space' => 200,
				'html' => we_class::htmlTextArea('weShipping_countries', 4, 21, implode("\n", $weShipping->countries)),
				'noline' => 1
			);
			// foreach ...
			// form table with every value -> cost entry
			if($weShipping->shipping){

				$tblPart = '';
				for($i = 0; $i < count($weShipping->shipping); $i++){

					$tblRowName = 'weShippingId_' . $i;

					$tblPart .= '
			<tr id="' . $tblRowName . '">
				<td>' . we_class::htmlTextInput('weShipping_cartValue[]', 24, $weShipping->cartValue[$i], '', 'onkeypress="return WE().util.IsDigit(event);"') . '</td>
				<td></td>
				<td>' . we_class::htmlTextInput('weShipping_shipping[]', 20, $weShipping->shipping[$i], '', 'onkeypress="return WE().util.IsDigit(event);"') . '</td>
				<td></td>
				<td>' . we_html_button::create_button(we_html_button::TRASH, "we_cmd('deleteShippingCostTableRow','" . $tblRowName . "');") . '</td>
			</tr>';
				}
			}

			$parts[] = array(
				'headline' => g_l('modules_shop', '[shipping][costs]'),
				'space' => 200,
				'html' =>
				'<table style="width:100%" class="default defaultfont" id="shippingCostTable">
		<tr>
			<td><b>' . g_l('modules_shop', '[shipping][order_value]') . '</b></td>
			<td style="width:10px;"></td>
			<td><b>' . g_l('modules_shop', '[shipping][shipping_costs]') . '</b></td>
			<td style="width:10px"></td>
		</tr>
		<tbody id="shippingCostTableEntries">
	' . $tblPart . '
		</tbody>
	</table>' .
				we_html_button::create_button(we_html_button::PLUS, 'javascript:we_cmd(\'addShippingCostTableRow\',\'12\');'),
				'noline' => 1
			);
			$parts[] = array(
				'headline' => 'Standard',
				'space' => 200,
				'html' => we_class::htmlSelect('weShipping_default', array(1 => g_l('global', '[true]'), 0 => g_l('global', '[false]')), 1, $weShipping->default),
				'noline' => 1
			);
		}

		echo we_html_multiIconBox::getHTML('weShipping', $parts, 30, we_html_button::position_yes_no_cancel(
						we_html_button::create_button(we_html_button::SAVE, 'javascript:we_cmd(\'save\');'), '', we_html_button::create_button(we_html_button::CLOSE, 'javascript:we_cmd(\'close\');')
				), -1, '', '', false, g_l('modules_shop', '[shipping][shipping_package]')
		);
		?>
	</form>
</body></html>