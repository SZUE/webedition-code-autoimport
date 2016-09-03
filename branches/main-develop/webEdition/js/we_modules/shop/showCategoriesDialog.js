/* global WE, top */

/**
 * webEdition CMS
 *
 * $Rev: 12708 $
 * $Author: mokraemer $
 * $Date: 2016-09-01 14:22:39 +0200 (Do, 01. Sep 2016) $
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
var hot = 0;

function addListeners() {
	for (var i = 1; i < document.we_form.elements.length; i++) {
		document.we_form.elements[i].addEventListener("change", function () {
			hot = 1;
		});
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

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "close":
			if (hot) {
				new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=exitQuestion", "we_exit_doc_question", -1, -1, 380, 130, true, false, true);
			} else {
				window.close();
			}
			break;
		case "save":
			document.we_form["we_cmd[0]"].value = "saveShopCatRels";
			document.we_form.onsaveclose.value = 1;
			we_submitForm(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_shop_categories");
			break;
		case "save_notclose":
			document.we_form["we_cmd[0]"].value = "saveShopCatRels";
			we_submitForm(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edit_shop_categories");
			break;
		default:
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function we_switch_active_by_id(id) {
	try {
		document.getElementById("destPrincipleRow_" + id).style.display =
						document.getElementById("defCountryRow_" + id).style.display =
						(document.getElementById("check_weShopCatIsActive[" + id + "]").checked) ? "" : "none";

		document.getElementById("countriesRow_" + id).style.display =
						document.getElementById("check_weShopCatIsActive[" + id + "]").checked &&
						(document.getElementById("taxPrinciple_tmp[" + id + "]").value == 1) ? "" : "none";
	} catch (e) {
	}
}

function we_switch_principle_by_id(id, obj, isShopCatsDir) {
	try {
		var active = isShopCatsDir ? true : document.getElementById("check_weShopCatIsActive[" + id + "]").checked;

		document.getElementById("taxPrinciple_tmp[" + id + "]").value = obj.value;
		document.getElementById("countriesRow_" + id).style.display =
						(active && obj.value == 1) ? "" : "none";
	} catch (e) {
	}
}
