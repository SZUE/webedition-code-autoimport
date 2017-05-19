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
'use strict';
WE().util.loadConsts(document, "g_l.users");

var usersData = WE().util.getDynamicVar(document, 'loadVarUsersView', 'data-users');

parent.document.title = usersData.modTitle;

var loaded = 0;
var hot = false;

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);

	if (args[0] === 'usetHot') {
		usetHot();
		return;
	}
	if (hot && args[0] !== "save_user") {
		if (window.confirm(WE().consts.g_l.users.view.save_changed_user)) {
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
			if (!WE().util.hasPerm('NEW_USER')) {
				WE().util.showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			top.content.editor.edbody.focus();
			if (!saveBeforeNextCmd(args)) {
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=new_user&cgroup=" + usersData.cgroup;
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
			if (!WE().util.hasPerm('NEW_GROUP')) {
				WE().util.showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			if (!saveBeforeNextCmd(args)) {
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=new_group&cgroup=" + usersData.cgroup;
			}
			break;
		case "new_alias":
			if (!WE().util.hasPerm('NEW_USER')) {
				WE().util.showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			if (!saveBeforeNextCmd(args)) {
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=new_alias&cgroup=" + usersData.cgroup;
			}
			break;
		case "save_user":
			if (!WE().util.hasPerm('SAVE_USER')) {
				WE().util.showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			if (top.content.editor.edbody.document.we_form) {
				top.content.editor.edbody.document.we_form.cmd.value = "save_user";
				if (top.content.editor.edbody.we_submitForm("cmd", WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd")) {
					top.content.usetHot();
				}
			}
			break;
		case "delete_user":
			if (!WE().util.hasPerm('DELETE_USER')) {
				WE().util.showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			WE().util.showConfirm(window, "", WE().util.sprintf(WE().consts.g_l.users.view.delete_alert[usersData.Type], usersData.Text), ["delete_user_do"]);
			break;
		case "delete_user_do":
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=cmd&cmd=delete_user_do";
			break;
		case "show_search":
			var keyword = top.content.we_form_treefooter.keyword.value;
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=search&search=1&keyword=" + keyword, "search", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true, false);
			break;
		case "setUserData":
			usersData.cgroup = args[1];
			usersData.Type = args[2];
			usersData.Text = args[3];
			break;
		case 'loadUsersContent':
			var home = args[1].home !== undefined ? "&home=1" : "";
			top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=edheader" + home;
			top.content.editor.edbody.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=edbody" + home + (args[1].oldtab !== undefined ? '&oldtab=' + args[1].oldtab : '');
			top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=edfooter" + home;
			break;
		default:
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function saveBeforeNextCmd(args) {
	if (hot && top.content.editor.edbody.document.we_form.cmd) {
		if (window.confirm(WE().consts.g_l.users.view.save_changed_user)) {
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