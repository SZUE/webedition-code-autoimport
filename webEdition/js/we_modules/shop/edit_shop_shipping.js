/* global WE, top */
'use strict';

/**
 * webEdition CMS
 *
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

var shopping = WE().util.getDynamicVar(document, 'loadVarShopping', 'data-shopping');

// this is for new entries.
var entryPosition = 0;

function closeOnEscape() {
	return true;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "save":
			we_submitForm(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_shop_shipping");
			break;
		case "close":
			window.close();
			break;
		case "delete":
			WE().util.showConfirm(window, "", WE().consts.g_l.shop.delete_shipping, ["delete_shipping"]);
			break;
		case "delete_shipping":
			var we_cmd_field = document.getElementById("we_cmd_field");
			we_cmd_field.value = "deleteShipping";
			we_submitForm(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_shop_shipping");
			break;
		case "newEntry":
			document.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_shop_shipping&we_cmd[0]=newShipping";
			break;
		case "addShippingCostTableRow":
			addShippingCostTableRow();
			break;
		case "deleteShippingCostTableRow":
			deleteShippingCostTableRow(args[1]);
			break;
		default :
			top.opener.top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
			break;
	}
}

function deleteShippingCostTableRow(rowId) {
	var tbl = document.getElementById("shippingCostTable");
	var tableRows = tbl.rows;

	for (var i = 0; i < tableRows.length; i++) {
		if (rowId == tableRows[i].id) {
			tbl.deleteRow(i);
		}
	}
}

function we_submitForm(url) {
	var f = window.document.we_form;
	if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	f.action = url;
	f.method = "post";

	f.submit();
	return true;
}

function addShippingCostTableRow() {
	var tbl = document.getElementById("shippingCostTableEntries");
	var entryId = "New" + "" + entryPosition++;

	var theNewRow = document.createElement("TR");
	theNewRow.setAttribute("id", "weShippingId_" + entryId);

	var cell1 = document.createElement("TD");
	cell1.innerHTML = '<input class="wetextinput" type="text" name="weShipping_cartValue[]" size="24" />';
	var cell2 = document.createElement("TD");
	var cell3 = document.createElement("TD");
	cell3.innerHTML = '<input class="wetextinput" type="text" name="weShipping_shipping[]" size="24" />';
	var cell4 = document.createElement("TD");
	var cell5 = document.createElement("TD");


	cell5.innerHTML = shopping.trashButton.replace("#####placeHolder#####", entryId);
	theNewRow.appendChild(cell1);
	theNewRow.appendChild(cell2);
	theNewRow.appendChild(cell3);
	theNewRow.appendChild(cell4);
	theNewRow.appendChild(cell5);

	// append new row
	tbl.appendChild(theNewRow);
}