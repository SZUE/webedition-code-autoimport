/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 8967 $
 * $Author: mokraemer $
 * $Date: 2015-01-13 12:56:41 +0100 (Di, 13. Jan 2015) $
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

function SendMail(was) {
	document.location = dirs.SCRIPT_NAME + "?pnt=edbody&bid=" + bid + "&SendMail=" + was;
}
function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = dirs.WE_SHOP_MODULE_DIR + "edit_shop_properties.php?";

	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURIComponent(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	switch (arguments[0]) {

		case "edit_shipping_cost":
			var wind = new jsWindow(url + "&bid=" + bid, "edit_shipping_cost", -1, -1, 545, 205, true, true, true, false);
			break;

		case "edit_shop_cart_custom_field":
			var wind = new jsWindow(url + "&bid=" + bid + "&cartfieldname=" + (arguments[1] ? arguments[1] : ''), "edit_shop_cart_custom_field", -1, -1, 545, 300, true, true, true, false);
			break;

		case "edit_order_customer":
			var wind = new jsWindow(url + "&bid=" + bid, "edit_order_customer", -1, -1, 545, 600, true, true, true, false);
			break;
		case "customer_edit":
			top.document.location = dirs.WE_MODULES_DIR + 'show_frameset.php?mod=customer&sid=' + cid;
			break;
		case "add_new_article":
			var wind = new jsWindow(url + "&bid=" + bid, "add_new_article", -1, -1, 650, 600, true, false, true, false);
			break;
	}
}

function neuerartikel() {
	we_cmd("add_new_article");
}

function deleteorder() {
	top.content.editor.location = dirs.WE_SHOP_MODULE_DIR + "edit_shop_frameset.php?pnt=edbody&deletethisorder=1&bid=" + bid;
	top.content.deleteEntry(bid);
}
