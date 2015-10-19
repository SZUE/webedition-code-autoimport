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
function new_message(mode) {
	if (mode == "re" && (top.content.editor.edbody.last_entry_selected == -1)) {
		return;
	}
	new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "messaging_newmessage.php?we_transaction=" + transaction + "&mode=" + mode, "messaging_new_message", -1, -1, 670, 530, true, false, true, false);
}

function copy_messages() {
	if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
		top.content.cmd.location = WE().consts.dirs.WE_MODULES_DIR + "show.php?mod=messaging&pnt=cmd&we_transaction=" + transaction + "&mcmd=copy_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
	}
}

function cut_messages() {
	if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
		top.content.cmd.location = WE().consts.dirs.WE_MODULES_DIR + "show.php?mod=messaging&pnt=cmd&we_transaction=" + transaction + "&mcmd=cut_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
	}
}

function paste_messages() {
	if (top.content.editor.edbody.entries_selected) {
		top.content.cmd.location = WE().consts.dirs.WE_MODULES_DIR + "show.php?mod=messaging&pnt=cmd&we_transaction=" + transaction + "&mcmd=paste_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
	}
}

function delete_messages(isTodo) {
	if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
		c = confirm((isTodo ? WE().consts.g_l.messaging.q_rm_todos : WE().consts.g_l.messaging.q_rm_messages));
		if (c === false) {
			return;
		}
		top.content.cmd.location = WE().consts.dirs.WE_MODULES_DIR + "show.php?mod=messaging&pnt=cmd&we_transaction=" + transaction + "&mcmd=delete_msg&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
	}
}

function refresh() {
	top.content.update_messaging();
}

function launch_todo() {
	if (top.content.editor.edbody.entries_selected) {
		top.content.cmd.location = WE().consts.dirs.WE_MODULES_DIR + "show.php?mod=messaging&pnt=cmd&mcmd=launch&mode=todo&we_transaction=" + transaction + "";
	}
}

function new_todo() {
	new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "todo_edit_todo.php?we_transaction=" + transaction + "&mode=new", "messaging_new_todo", -1, -1, 690, 520, true, false, true, false);
}

function forward_todo() {
	if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
		new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "todo_edit_todo.php?we_transaction=" + transaction + "&mode=forward", "messaging_new_todo", -1, -1, 705, 600, true, true, true, false);
	}
}

function reject_todo() {
	if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
		new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "todo_edit_todo.php?we_transaction=" + transaction + "&mode=reject", "messaging_new_todo", -1, -1, 690, 600, true, false, true, false);
	}
}

function update_todo() {
	if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
		new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "todo_update_todo.php?we_transaction=" + transaction + "&mode=reject", "messaging_new_todo", -1, -1, 705, 600, true, true, true, false);
	}
}

function launch_msg() {
	if (top.content.editor.edbody.entries_selected) {
		top.content.cmd.location = WE().consts.dirs.WE_MODULES_DIR + "show.php?mod=messaging&pnt=cmd&mcmd=launch&mode=message&we_transaction=" + transaction;
	}
}