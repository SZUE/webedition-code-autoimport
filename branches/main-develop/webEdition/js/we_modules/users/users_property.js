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
var loaded = false;
function we_submitForm(target, url) {
	var f = self.document.we_form;

	ok = true;

	if (f.input_pass) {
		if (f.oldtab.value == 0) {
			if (f.input_pass.value.length < 4 && f.input_pass.value.length !== 0) {
				WE().util.showMessage(WE().consts.g_l.navigation.users.password_alert, WE().consts.message.WE_MESSAGE_ERROR, this);
				return false;
			}
			if (f.input_pass.value !== "") {
				var clearPass = f.input_pass.value;
				f.input_pass.value = "";
				f[f.obj_name.value + "_clearpasswd"].value = clearPass;
			}
		}
	}

	if (ok) {
		f.target = target;
		f.action = url;
		f.method = "post";
		f.submit();
	}
	return true;
}

function switchPage(page) {
	document.we_form.tab.value = page;
	return we_submitForm(self.name, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=edbody");
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_users_selector":
			new (WE().util.jsWindow)(this, url, "browse_users", -1, -1, 500, 300, true, false, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(this, url, "we_fileselector", -1, -1, WE().consts.size.windowDirSelect.width, WE().consts.size.windowDirSelect.height, true, true, true, true);
			break;
		case "select_seem_start":
			myWindStr = "WE().util.jsWindow.prototype.find(\'preferences\').wind";
			top.opener.top.we_cmd("we_selector_document", myWind.document.forms[0].elements.seem_start_file.value, WE().consts.tables.FILE_TABLE, myWindStr + ".document.forms[0].elements.seem_start_file.value", myWindStr + ".document.forms[0].elements.seem_start_file_name.value", "", "", "", WE().consts.contentTypes.WEDOCUMENT, 1);
			break;
		case "openNavigationDirselector":
		case "openNewsletterDirselector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
			if (args[0] === "openNewsletterDirselector") {
				args[0] = "we_newsletter_dirSelector";//FIXME
			} else {
				args[0] = "we_navigation_dirSelector";
			}
			for (var i = 0; i < args.length; i++) {
				url += "we_cmd[]=" + encodeURI(args[i]);
				if (i < (args.length - 1)) {
					url += "&";
				}
			}
			new (WE().util.jsWindow)(this, url, "we_navigation_dirselector", -1, -1, 600, 400, true, true, true);
			break;
		default:
			top.content.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}