/* global WE, top */

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

// this is for new entries.
var entryPosition = 0;

function closeOnEscape() {
	return true;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "save":
			we_submitForm(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_shop_shipping");
			break;
		case "close":
			window.close();
			break;
		case "delete":
			if (confirm(WE().consts.g_l.shop.delete_shipping)) {
				var we_cmd_field = document.getElementById("we_cmd_field");
				we_cmd_field.value = "deleteShipping";
				we_submitForm(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_shop_shipping");

			}
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
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
			break;
	}
}

function deleteShippingCostTableRow(rowId) {
	tbl = document.getElementById("shippingCostTable");
	tableRows = tbl.rows;

	for (i = 0; i < tableRows.length; i++) {
		if (rowId == tableRows[i].id) {
			tbl.deleteRow(i);
		}
	}
}

function we_submitForm(url) {
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