/* global WE, top */

/**
 * webEdition CMS
 *
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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function we_cmd_users(args, url) {
	switch (args[0]) {
		case "we_users_selector":
			if (WE().util.hasPerm('NEW_USER') || WE().util.hasPerm('NEW_GROUP') || WE().util.hasPerm('SAVE_USER') || WE().util.hasPerm('SAVE_GROUP') || WE().util.hasPerm('DELETE_USER') || WE().util.hasPerm('DELETE_GROUP')) {
				new (WE().util.jsWindow)(this, url, "browse_users", -1, -1, 500, 300, true, false, true);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
			break;
		case "users_edit":
		case "users_edit_ifthere":
			if (WE().util.hasPerm('NEW_USER') || WE().util.hasPerm('NEW_GROUP') || WE().util.hasPerm('SAVE_USER') || WE().util.hasPerm('SAVE_GROUP') || WE().util.hasPerm('DELETE_USER') || WE().util.hasPerm('DELETE_GROUP')) {
				new (WE().util.jsWindow)(this, url, "edit_module", -1, -1, 970, 760, true, true, true, true);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
			break;
		case "new_user":
			if (WE().util.hasPerm('NEW_USER')) {
				showNewWindow(args);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
			break;
		case "save_user":
			if (WE().util.hasPerm('SAVE_USER')) {
				showNewWindow(args);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
			break;
		case "new_group":
			if (WE().util.hasPerm('NEW_GROUP')) {
				showNewWindow(args);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
			break;
		case "new_alias":
			if (WE().util.hasPerm('NEW_USER')) {
				showNewWindow(args);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
			break;
		case "exit_users":
			showNewWindow(args);
			break;
		case "delete_user":
			if (WE().util.hasPerm('DELETE_USER')) {
				showNewWindow(args);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
			break;
		case "new_organization":
			if (WE().util.hasPerm('NEW_USER')) {
				showNewWindow(args);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
			break;
		case "doctypes":
			new (WE().util.jsWindow)(this, url, "doctypes", -1, -1, 720, 670, true, true, true);
			break;
		case "users_unlock":
			top.YAHOO.util.Connect.asyncRequest('GET', url, {success: function () {
				}, failure: function () {
				}});
			break;
		case "users_add_owner":
			top._EditorFrame.setEditorIsHot(true);
			top.setScrollTo();
			args[1] = args[1].allIDs.join(',');
		case "users_del_owner":
		case "users_del_all_owners":
		case "users_del_user":
		case "users_add_user":
			if (args[0] === "object_del_all_users" && args[3]) {
				url += '#f' + args[3];
			}
			if (!WE().util.we_sbmtFrm(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url)) {
				url += "&we_transaction=" + args[2];
				we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url, args[0]);
			}
			break;
		case "chooseAddress":
			new (WE().util.jsWindow)(this, url, "chooseAddress", -1, -1, 400, 590, true, true, true, true);
			break;
		case "users_changeR":
			we_repl(window.load, url, args[0]);
			break;
		default:
			return false;
	}
	return true;
}

function showNewWindow(args) {
	var wind = WE().util.jsWindow.prototype.find('edit_module');
	if (wind) {
		wind.content.we_cmd(args[0]);
		wind.focus();
	}
}