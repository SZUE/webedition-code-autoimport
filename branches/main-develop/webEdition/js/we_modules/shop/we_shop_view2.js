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
'use strict';

var shopData = WE().util.getDynamicVar(document, 'loadVarShop', 'data-shop');

function SendMail(was) {
	document.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&bid=" + shopData.bid + "&SendMail=" + was;
}
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_order_properties&");

	var wind;
	switch (args[0]) {
		case "edit_shipping_cost":
			wind = new (WE().util.jsWindow)(caller, url + "&bid=" + shopData.bid, args[0], WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, true, true, false);
			break;

		case "edit_shop_cart_custom_field":
			wind = new (WE().util.jsWindow)(caller, url + "&bid=" + shopData.bid + "&cartfieldname=" + (args[1] ? args[1] : ''), args[0], WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true, false);
			break;

		case "edit_order_customer":
			wind = new (WE().util.jsWindow)(caller, url + "&bid=" + shopData.bid, "edit_order_customer", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true, false);
			break;
		case "customer_edit":
			top.document.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=customer&pnt=show_frameset&sid=' + shopData.cid;
			break;
		case "add_new_article":
			wind = new (WE().util.jsWindow)(caller, url + "&bid=" + shopData.bid, "add_new_article", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true, false);
			break;
		case "doClickShopOrder":
			top.opener.top.content.doClick(args[1], "shop", WE().consts.tables.SHOP_ORDER_TABLE);
			break;
		case 'showDeleteOrder':
			top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&deletethisorder=1&bid=" + args[1];
			break;
		default:
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function neuerartikel() {
	we_cmd("add_new_article");
}

function deleteorder() {
	top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&deletethisorder=1&bid=" + shopData.bid;
	top.content.treeData.deleteEntry(shopData.bid);
}

function we_submit() {
	var elem = document.getElementById("cartfieldname");

	if (elem && elem.value) {
		document.we_form.submit();
	} else {
		WE().util.showMessage(WE().consts.g_l.shop.field_empty_js_alert, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function CalendarChanged(field, oldval, val) {
	if (oldval != val) {
		document.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&bid=" + shopData.bid + "&" + field + "=" + val;
	}
}

function selectArticle(articleInfo) {
	document.location = "?we_cmd[0]=" + shopData.cmd0 + "&bid=" + shopData.bid + "&page=" + shopData.page + "&searchArticle=" + shopData.searchArticle + "&add_article=" + articleInfo;
}

function switchEntriesPage(pageNum) {
	document.location = "?we_cmd[0]=" + shopData.cmd0 + "&bid=" + shopData.bid + "&searchArticle=" + shopData.searchArticle + "&page=" + pageNum;
}

function searchArticles() {
	var field = document.getElementById("searchArticle");
	document.location = "?we_cmd[0]=" + shopData.cmd0 + "&bid=" + shopData.bid + "&searchArticle=" + field.value;
}