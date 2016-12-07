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

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var i;

	switch (args[0]) {
		case "switchPage":
			document.we_form.cmd.value = args[0];
			document.we_form.tabnr.value = args[1];
			submitForm();
			break;
		case "we_voting_dirSelector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
			args[0] = "we_voting_dirSelector";
			for (i = 0; i < args.length; i++) {
				url += "we_cmd[]=" + encodeURI(args[i]);
				if (i < (args.length - 1)) {
					url += "&";
				}
			}
			new (WE().util.jsWindow)(window, url, "we_votingSelector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(window, url, "browse_server", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "we_users_selector":
			new (WE().util.jsWindow)(window, url, "browse_users", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true);
			break;
		case "users_add_owner":
			var owners = args[1].allPaths;
			var isfolders = args[2].allIsFolder;

			for (i = 0; i < owners.length; i++) {
				if (owners[i] !== "") {
					owners_label.addItem();
					owners_label.setItem(0, (owners_label.itemCount - 1), WE().util.getTreeIcon(isfolders[i] == 1 ? WE().consts.contentTypes.FOLDER : "we/user") + " " + owners[i]);
					owners_label.showVariant(0);
				}
			}
			break;
		case "export_csv":
			var oldcmd = document.we_form.cmd.value;
			var oldpnt = document.we_form.pnt.value;
			document.we_form.question_name.value = question_edit.name;
			document.we_form.answers_name.value = answers_edit.name;
			document.we_form.variant_count.value = answers_edit.variantCount;
			document.we_form.item_count.value = answers_edit.itemCount;
			document.we_form.cmd.value = args[0];
			document.we_form.pnt.value = args[0];
			new (WE().util.jsWindow)(window, "", "export_csv", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			submitForm("export_csv");
			document.we_form.cmd.value = oldcmd;
			document.we_form.pnt.value = oldpnt;
			break;
		case "exportGroup_csv":
			oldcmd = document.we_form.cmd.value;
			oldpnt = document.we_form.pnt.value;
			document.we_form.cmd.value = args[0];
			document.we_form.pnt.value = args[0];
			new (WE().util.jsWindow)(window, "", "exportGroup_csv", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			submitForm("exportGroup_csv");
			document.we_form.cmd.value = oldcmd;
			document.we_form.pnt.value = oldpnt;
			break;

		case  "reset_ipdata":
			WE().util.showConfirm(window, "", WE().consts.g_l.voting.delete_ipdata_question, ["reset_ipdata_do"]);
			break;
		case "reset_ipdata_do":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=reset_ipdata";
			new (WE().util.jsWindow)(window, url, "reset_ipdata", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			var t = document.getElementById("ip_mem_size");
			setVisible("delete_ip_data", false);
			t.innerHTML = "0";
			break;
		case "delete_log":
			WE().util.showConfirm(window, "", WE().consts.g_l.voting.delete_log_question, ["delete_log_do"]);
			break;
		case "delete_log_do":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=delete_log";
			new (WE().util.jsWindow)(window, url, "delete_log", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			break;
		case "show_log":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=" + args[0];
			new (WE().util.jsWindow)(window, url, "show_log", WE().consts.size.dialog.medium, WE().consts.size.dialog.small, true, true, true);
			break;
		default:
			top.content.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function submitForm(target, action, method, form) {
	var f = form ? window.document.forms[form] : window.document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting");
	f.method = (method ? method : "post");

	f.submit();
}