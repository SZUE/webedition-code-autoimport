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

var hot = 0;
var get_focus = 1;
var activ_tab = 1;
var scrollToVal = 0;
var isDocument = 0;
var isObject = 0;
var classID = 0;


function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}


function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "new_shop":
			top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=editor";
			break;
		case "delete_shop":
			if (top.content.right && top.content.editor.edbody.hot && top.content.editor.edbody.hot === 1) {
				if (confirm(WE().consts.g_l.shop.del_shop)) {
					top.content.editor.edbody.deleteorder();
				}
			} else {
				top.we_showMessage(WE().consts.g_l.shop.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, this);
			}
			break;
		case "new_article":
			if (top.content.right && top.content.editor.edbody.hot && top.content.editor.edbody.hot === 1) {
				top.content.editor.edbody.neuerartikel();
			} else {
				top.we_showMessage(WE().consts.g_l.shop.no_order_there, WE().consts.message.WE_MESSAGE_ERROR, this);
			}
			break;
		case "pref_shop":
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 470, 600, true, true, true, false);
			break;
		case "edit_shop_vats":
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 500, 450, true, false, true, false);
			break;
		case "edit_shop_shipping":
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 700, 600, true, false, true, false);
			break;
		case "edit_shop_status":
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 700, 780, true, true, true, false);
			break;
		case "edit_shop_vat_country":
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 700, 780, true, true, true, false);
			break;
		case "payment_val":
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 520, 720, true, false, true, false);
			break;
		case "edit_shop_categories":
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=" + args[0], args[0], -1, -1, 500, 450, true, false, true, false);
			break;
		case "revenue_view":
			//FIXME: this is not correct; document doesnt work like this
			if (isDocument) {
				top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=editor&top=1&typ=document";
			} else
			if (isObject) {
				top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=editor&top=1&typ=object&ViewClass=" + classID;
			} else {
				top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=editor&top=1&typ=document";
			}
			break;
		case "year":
			top.content.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&year=" + args[1];
			break;

		default:
			top.opener.top.we_cmd.apply(this, arguments);
	}
}
