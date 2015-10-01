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
			if (hasPerm('NEW_USER') || hasPerm('NEW_GROUP') || hasPerm('SAVE_USER') || hasPerm('SAVE_GROUP') || hasPerm('DELETE_USER') || hasPerm('DELETE_GROUP')) {
				new jsWindow(url, "browse_users", -1, -1, 500, 300, true, false, true);
			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE_MESSAGE_ERROR, window);
			}
			break;
		case "users_edit":
		case "users_edit_ifthere":
			if (hasPerm('NEW_USER') || hasPerm('NEW_GROUP') || hasPerm('SAVE_USER') || hasPerm('SAVE_GROUP') || hasPerm('DELETE_USER') || hasPerm('DELETE_GROUP')) {
				new jsWindow(url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE_MESSAGE_ERROR, window);
			}
			break;
		case "new_user":
			if (hasPerm('NEW_USER')) {
				showNewWindow(args);
			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE_MESSAGE_ERROR, window);
			}
			break;
		case "save_user":
			if (hasPerm('SAVE_USER')) {
				showNewWindow(args);
			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE_MESSAGE_ERROR, window);
			}
			break;
		case "new_group":
			if (hasPerm('NEW_GROUP')) {
				showNewWindow(args);
			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE_MESSAGE_ERROR, window);
			}
			break;
		case "new_alias":
			if (hasPerm('NEW_USER')) {
				showNewWindow(args);
			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE_MESSAGE_ERROR, window);
			}
			break;
		case "exit_users":
			showNewWindow(args);
			break;
		case "delete_user":
			if (hasPerm('DELETE_USER')) {
				showNewWindow(args);
			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE_MESSAGE_ERROR, window);
			}
			break;
		case "new_organization":
			if (hasPerm('NEW_USER')) {
				showNewWindow(args);
			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE_MESSAGE_ERROR, window);
			}
			break;
		case "doctypes":
			new jsWindow(url, "doctypes", -1, -1, 720, 670, true, true, true);
			break;
		case "users_unlock":
			top.YAHOO.util.Connect.asyncRequest('GET', url, {success:  function(){}, failure:  function(){}});
//		we_repl(self.load,url,args[0]);
			break;
		case "users_add_owner":
		case "users_del_owner":
		case "users_del_all_owners":
		case "users_del_user":
		case "users_add_user":
			if (args[0] === "object_del_all_users" && args[3]) {
				url += '#f' + args[3];
			}
			if (!we_sbmtFrm(top.weEditorFrameController.getActiveDocumentReference().frames[1], url)) {
				url += "&we_transaction=" + args[2];
				we_repl(top.weEditorFrameController.getActiveDocumentReference().frames[1], url, args[0]);
			}
			break;
		case "chooseAddress":
			new jsWindow(url, "chooseAddress", -1, -1, 400, 590, true, true, true, true);
			break;
		case "users_changeR":
			we_repl(self.load, url, args[0]);
			break;
		default:
			return false;
	}
	return true;
}

function showNewWindow(args) {
	var fo = false;
	if (jsWindow_count) {
		for (var k = jsWindow_count - 1; k > -1; k--) {
			eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + args[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
			if (fo)
				break;
		}
		if (wind)
			wind.focus();
	}

}