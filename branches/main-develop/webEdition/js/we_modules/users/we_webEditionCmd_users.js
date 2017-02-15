/* global WE, top, we_cmd_modules */

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
'use strict';

we_cmd_modules.users = function (args, url, caller) {
	switch (args[0]) {
		case "we_users_selector":
			if (WE().util.hasPerm('NEW_USER') || WE().util.hasPerm('NEW_GROUP') || WE().util.hasPerm('SAVE_USER') || WE().util.hasPerm('SAVE_GROUP') || WE().util.hasPerm('DELETE_USER') || WE().util.hasPerm('DELETE_GROUP')) {
				new (WE().util.jsWindow)(caller, url, "browse_users", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
			break;
		case "users_edit":
		case "users_edit_ifthere":
			if (WE().util.hasPerm('NEW_USER') || WE().util.hasPerm('NEW_GROUP') || WE().util.hasPerm('SAVE_USER') || WE().util.hasPerm('SAVE_GROUP') || WE().util.hasPerm('DELETE_USER') || WE().util.hasPerm('DELETE_GROUP')) {
				new (WE().util.jsWindow)(caller, url, "edit_module", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
			break;
		case "new_user":
			if (WE().util.hasPerm('NEW_USER')) {
				WE().layout.pushCmdToModule(args);
				return true;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
			break;
		case "save_user":
			if (WE().util.hasPerm('SAVE_USER')) {
				WE().layout.pushCmdToModule(args);
				return true;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
			break;
		case "new_group":
			if (WE().util.hasPerm('NEW_GROUP')) {
				WE().layout.pushCmdToModule(args);
				return true;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
			break;
		case "new_alias":
			if (WE().util.hasPerm('NEW_USER')) {
				WE().layout.pushCmdToModule(args);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
			break;
		case "exit_users":
			WE().layout.pushCmdToModule(args);
			break;
		case "delete_user":
			if (WE().util.hasPerm('DELETE_USER')) {
				WE().layout.pushCmdToModule(args);
				break;
			}
			top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
			break;
		case "doctypes":
			new (WE().util.jsWindow)(caller, url, "doctypes",  WE().consts.size.dialog.medium, WE().consts.size.dialog.medium, true, true, true);
			break;
		case "users_unlock":
			WE().util.rpc(url);
			break;
		case "users_add_owner":
			top._EditorFrame.setEditorIsHot(true);
			top.setScrollTo();
			args[1] = args[1].allIDs.join(',');
			/*falls through*/
		case "users_del_owner":
		case "users_del_all_owners":
		case "users_del_user":
		case "users_add_user":
			if (args[0] === "object_del_all_users" && args[3]) {
				url += '#f' + args[3];
			}
			if (!WE().util.we_sbmtFrm(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url)) {
				url += "&we_transaction=" + args[2];
				window.we_repl(WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1], url);
			}
			break;
		case "chooseAddress":
			new (WE().util.jsWindow)(caller, url, "chooseAddress", WE().consts.size.dialog.smaller, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "users_changeR":
			window.we_repl(window.load, url);
			break;
		default:
			return false;
	}
	return true;
};
