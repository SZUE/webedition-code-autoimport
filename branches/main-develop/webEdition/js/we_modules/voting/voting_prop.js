/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 10647 $
 * $Author: mokraemer $
 * $Date: 2015-10-21 22:02:31 +0200 (Mi, 21. Okt 2015) $
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
	var args = [];
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		args.push(arguments[i]);
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (arguments[0]) {
		case "switchPage":
			document.we_form.cmd.value = arguments[0];
			document.we_form.tabnr.value = arguments[1];
			submitForm();
			break;
		case "we_voting_dirSelector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_voting_dirSelector&";
			for (var i = 1; i < arguments.length; i++) {
				url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
				if (i < (arguments.length - 1)) {
					url += "&";
				}
			}
			new (WE().util.jsWindow)(window, url, "we_votingSelector", -1, -1, 600, 350, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(window, url, "browse_server", -1, -1, 840, 400, true, false, true);
			break;
		case "we_users_selector":
			new (WE().util.jsWindow)(window, url, "browse_users", -1, -1, 500, 300, true, false, true);
			break;
		case "users_add_owner":
			var owners = arguments[1];
			var isfolders = arguments[2];

			var own_arr = owners.split(",");
			var isfolders_arr = isfolders.split(",");
			for (var i = 0; i < own_arr.length; i++) {
				if (own_arr[i] != "") {
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
			document.we_form.cmd.value = arguments[0];
			document.we_form.pnt.value = arguments[0];
			new (WE().util.jsWindow)(window, "", "export_csv", -1, -1, 420, 250, true, false, true);
			submitForm("export_csv");
			document.we_form.cmd.value = oldcmd;
			document.we_form.pnt.value = oldpnt;
			break;
		case "exportGroup_csv":
			oldcmd = document.we_form.cmd.value;
			oldpnt = document.we_form.pnt.value;
			document.we_form.cmd.value = arguments[0];
			document.we_form.pnt.value = arguments[0];
			new (WE().util.jsWindow)(window, "", "exportGroup_csv", -1, -1, 420, 250, true, false, true);
			submitForm("exportGroup_csv");
			document.we_form.cmd.value = oldcmd;
			document.we_form.pnt.value = oldpnt;
			break;

		case "reset_ipdata":
			if (confirm(WE().consts.g_l.voting.delete_ipdata_question)) {
				url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=" + arguments[0];
				new (WE().util.jsWindow)(window, url, arguments[0], -1, -1, 420, 230, true, false, true);
				var t = document.getElementById("ip_mem_size");
				setVisible("delete_ip_data", false);
				t.innerHTML = "0";
			}
			break;
		case "delete_log":
			if (confirm(WE().consts.g_l.voting.delete_log_question)) {
				url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=" + arguments[0];
				new (WE().util.jsWindow)(window, url, arguments[0], -1, -1, 420, 230, true, false, true);
			}
			break;
		case "show_log":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=" + arguments[0];
			new (WE().util.jsWindow)(window, url, arguments[0], -1, -1, 810, 600, true, true, true);
			break;
			break;
		default:
			top.content.we_cmd.apply(this, args);
	}
}