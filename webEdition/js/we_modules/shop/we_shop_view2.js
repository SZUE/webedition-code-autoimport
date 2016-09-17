/* global WE */

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
var bid = 0;
var cid = 0;

function SendMail(was) {
	document.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&bid=" + bid + "&SendMail=" + was;
}
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_order_properties&");

	var wind;
	switch (args[0]) {
		case "edit_shipping_cost":
			wind = new (WE().util.jsWindow)(this, url + "&bid=" + bid, args[0], -1, -1, 545, 205, true, true, true, false);
			break;

		case "edit_shop_cart_custom_field":
			wind = new (WE().util.jsWindow)(this, url + "&bid=" + bid + "&cartfieldname=" + (args[1] ? args[1] : ''), args[0], -1, -1, 545, 300, true, true, true, false);
			break;

		case "edit_order_customer":
			wind = new (WE().util.jsWindow)(this, url + "&bid=" + bid, "edit_order_customer", -1, -1, 545, 600, true, true, true, false);
			break;
		case "customer_edit":
			top.document.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=customer&pnt=show_frameset&sid=' + cid;
			break;
		case "add_new_article":
			wind = new (WE().util.jsWindow)(this, url + "&bid=" + bid, "add_new_article", -1, -1, 650, 600, true, false, true, false);
			break;
	}
}

function neuerartikel() {
	we_cmd("add_new_article");
}

function deleteorder() {
	top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&deletethisorder=1&bid=" + bid;
	top.content.treeData.deleteEntry(bid);
}
