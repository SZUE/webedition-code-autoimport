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

var hot = false;
var get_focus = 1;
var activ_tab = 1;
var scrollToVal = 0;

WE().util.loadConsts(document, "g_l.shop");
var viewData = WE().util.getDynamicVar(document, 'loadVarShop_view', 'data-viewData');

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}


function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "new_shop":
			top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=editor";
			break;
		case "delete_shop":
			if (!top.content.right && top.content.editor.edbody.hot && top.content.editor.edbody.hot) {
				top.we_showMessage(WE().consts.g_l.shop.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			WE().util.showConfirm(window, "", WE().consts.g_l.shop.del_shop, ["delete_shop_order"]);
			break;
		case "delete_shop_order":
			top.content.editor.edbody.deleteorder();
			break;
		case "new_article":
			if (top.content.right && top.content.editor.edbody.hot && top.content.editor.edbody.hot) {
				top.content.editor.edbody.neuerartikel();
			} else {
				top.we_showMessage(WE().consts.g_l.shop.no_order_there, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			break;
		case "pref_shop":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true, false);
			break;
		case "edit_shop_vats":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true, false);
			break;
		case "edit_shop_shipping":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, false, true, false);
			break;
		case "edit_shop_status":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], WE().consts.size.dialog.medium, WE().consts.size.dialog.medium, true, true, true, false);
			break;
		case "edit_shop_vat_country":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], WE().consts.size.dialog.medium, WE().consts.size.dialog.medium, true, true, true, false);
			break;
		case "payment_val":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], WE().consts.size.dialog.small, WE().consts.size.dialog.medium, true, false, true, false);
			break;
		case "edit_shop_categories":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true, false);
			break;
		case "revenue_view":
			//FIXME: this is not correct; document doesnt work like this
			if (viewData.isDocument) {
				top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=editor&top=1&typ=document";
			} else if (viewData.isObject) {
				top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=editor&top=1&typ=object&ViewClass=" + viewData.classID;
			} else {
				top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=editor&top=1&typ=document";
			}
			break;
		case "year":
			top.content.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&year=" + args[1];
			break;

		default:
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}
parent.document.title = viewData.title;