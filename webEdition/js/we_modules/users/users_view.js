/* global top, WE */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
WE().util.loadConsts(document, "g_l.users");

var usersData = WE().util.getDynamicVar(document, 'loadVarUsersView', 'data-users');

var frameset = usersData.frameset;
var cgroup = usersData.cgroup;
parent.document.title = usersData.modTitle;

var loaded = 0;
var hot = false;

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);

	if (args[0] === 'usetHot') {
		usetHot();
		return;
	}
	if (hot && args[0] !== "save_user") {
		if (confirm(WE().consts.g_l.users.view.save_changed_user)) {
			args[0] = "save_user";
		} else {
			top.content.usetHot();
		}
	}
	switch (args[0]) {
		case "exit_users":
			if (!hot) {
				top.opener.top.we_cmd("exit_modules");
			}
			break;
		case "new_user":
			top.content.editor.edbody.focus();
			if (!saveBeforeNextCmd(args)) {
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=new_user&cgroup=" + cgroup;
			}
			break;
		case "check_user_display":
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=check_user_display&uid=" + args[1];
			break;
		case "display_user":
			top.content.editor.edbody.focus();
			if (!saveBeforeNextCmd(args)) {
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=display_user&uid=" + args[1];
			}
			break;
		case "new_group":
			if (!saveBeforeNextCmd(args)) {
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=new_group&cgroup=" + cgroup;
			}
			break;
		case "new_alias":
			if (!saveBeforeNextCmd(args)) {
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=new_alias&cgroup=" + cgroup;
			}
			break;
		case "save_user":
			if (top.content.editor.edbody.document.we_form) {
				top.content.editor.edbody.document.we_form.cmd.value = "save_user";
				if (top.content.editor.edbody.we_submitForm("cmd", WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd")) {
					top.content.usetHot();
				}
			}
			break;
		case "delete_user":
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=delete_user";
			break;
		case "show_search":
			var keyword = top.content.we_form_treefooter.keyword.value;
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=search&search=1&keyword=" + keyword, "search", -1, -1, 580, 400, true, true, true, false);
			break;
		case "updateTitle":
			top.content.editor.edheader.document.getElementById("titlePath").innerText = args[1];
			break;
		case "setCgroup":
			cgroup = args[1];
			break;
		case 'loadUsersContent':
			var home = args[1].home !== undefined ? "&home=1" : "";
			top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=edheader" + home;
			top.content.editor.edbody.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=edbody" + home + (args[1].oldtab !== undefined ? '&oldtab=' + args[1].oldtab : '');
			top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=edfooter" + home;
			break;
		default:
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function saveBeforeNextCmd(args) {
	if (hot && top.content.editor.edbody.document.we_form.cmd) {
		if (confirm(WE().consts.g_l.users.view.save_changed_user)) {
			top.content.editor.edbody.document.we_form.cmd.value = "save_user";
			top.content.editor.edbody.document.we_form.sd.value = 1;
		} else {
			top.content.usetHot();
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
		}
		if (args[1]) {
			top.content.editor.edbody.document.we_form.uid.value = args[1];
		}
		if (args[2]) {
			top.content.editor.edbody.document.we_form.ctype.value = args[2];
		}
		if (args[3]) {
			top.content.editor.edbody.document.we_form.ctable.value = args[3];
		}
		top.content.editor.edbody.we_submitForm("cmd", WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd");
		return true;
	}
	return false;

}

function setHot() {
	hot = true;
}

function usetHot() {
	hot = false;
}