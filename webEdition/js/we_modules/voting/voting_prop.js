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
			new (WE().util.jsWindow)(this, url, "we_votingSelector", -1, -1, 600, 350, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(this, url, "browse_server", -1, -1, 840, 400, true, false, true);
			break;
		case "we_users_selector":
			new (WE().util.jsWindow)(this, url, "browse_users", -1, -1, 500, 300, true, false, true);
			break;
		case "users_add_owner":
			var owners = args[1];
			var isfolders = args[2];

			var own_arr = owners.split(",");
			var isfolders_arr = isfolders.split(",");
			for (i = 0; i < own_arr.length; i++) {
				if (own_arr[i] !== "") {
					owners_label.addItem();
					owners_label.setItem(0, (owners_label.itemCount - 1), WE().util.getTreeIcon(isfolders_arr[i] == 1 ? "folder" : "we/user") + " " + own_arr[i]);
					owners_label.showVariant(0);
				}
			}
			break;
		case "export_csv":
			oldcmd = document.we_form.cmd.value;
			oldpnt = document.we_form.pnt.value;
			document.we_form.question_name.value = question_edit.name;
			document.we_form.answers_name.value = answers_edit.name;
			document.we_form.variant_count.value = answers_edit.variantCount;
			document.we_form.item_count.value = answers_edit.itemCount;
			document.we_form.cmd.value = args[0];
			document.we_form.pnt.value = args[0];
			new (WE().util.jsWindow)(window, "", "export_csv", -1, -1, 420, 250, true, false, true);
			submitForm("export_csv");
			document.we_form.cmd.value = oldcmd;
			document.we_form.pnt.value = oldpnt;
			break;
		case "exportGroup_csv":
			oldcmd = document.we_form.cmd.value;
			oldpnt = document.we_form.pnt.value;
			document.we_form.cmd.value = args[0];
			document.we_form.pnt.value = args[0];
			new (WE().util.jsWindow)(window, "", "exportGroup_csv", -1, -1, 420, 250, true, false, true);
			submitForm("exportGroup_csv");
			document.we_form.cmd.value = oldcmd;
			document.we_form.pnt.value = oldpnt;
			break;

		case "reset_ipdata":
			if (confirm(WE().consts.g_l.voting.delete_ipdata_question)) {
				url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=" + args[0];
				new (WE().util.jsWindow)(this, url, args[0], -1, -1, 420, 230, true, false, true);
				var t = document.getElementById("ip_mem_size");
				setVisible("delete_ip_data", false);
				t.innerHTML = "0";
			}
			break;
		case "delete_log":
			if (confirm(WE().consts.g_l.voting.delete_log_question)) {
				url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=" + args[0];
				new (WE().util.jsWindow)(this, url, args[0], -1, -1, 420, 230, true, false, true);
			}
			break;
		case "show_log":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=" + args[0];
			new (WE().util.jsWindow)(this, url, args[0], -1, -1, 810, 600, true, true, true);
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