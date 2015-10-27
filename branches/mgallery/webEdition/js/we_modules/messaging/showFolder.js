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


sel_color = "#006DB8";
sel_text_color = "#ffffff";
default_text_color = "#000000";
default_color = "#ffffff";

passed_dls = [];

function showContent(id) {
	top.content.editor.edbody.messaging_msg_view.location = WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "messaging_message_view.php?id=" + id + "&we_transaction=" + transaction;
}

function check(elem, groupSel) {
	var j;

	var id = parseInt(elem.match(/\d+/));

	if (top.content.multi_select === false) {

		//de-select all selected entries
		for (j = 0; j < parent.entries_selected.length; j++) {
			highlight_TR(parent.entries_selected[j], default_color, default_text_color);
		}

		parent.entries_selected = [];
		doSelectMessage(id);
	} else if (WE().util.in_array(id, parent.entries_selected)) {
		unSelectMessage(id);
	} else {
		doSelectMessage(id);
	}
}

function doSelectMessage(id) {
	if (id == -1) {
		return;
	}
	showContent(id);

	if (parent.entries_selected.length > 0) {
		parent.entries_selected.push(String(id));
	} else {
		parent.entries_selected = [String(id)];
	}

	parent.parent.last_entry_selected = id;

	if (document.getElementsByName("read_0").length) {
		document.getElementsByName("read_0")[0].classList.remove("msgUnRead");
		document.getElementsByName("read_0")[0].classList.add("msgRead");
	}
	highlight_TR(id, sel_color, sel_text_color);
}

function highlight_TR(id, color, text_color) {
	var i;

	for (i = 0; i <= 3; i++) {
		switch (i) {
			case 0:
			case 2:
				if (document.getElementById("td_" + id + "_link_" + i)) {
					document.getElementById("td_" + id + "_link_" + i).style.color = text_color;
				}
				if (document.getElementById("td_" + id + "_" + i)) {
					document.getElementById("td_" + id + "_" + i).style.color = text_color;
				}
				break;
			default:
				if (i != 1 || (top.content.viewclass != "todo")) {
					if (document.getElementById("td_" + id + "_" + i)) {
						document.getElementById("td_" + id + "_" + i).style.color = text_color;
					}
				}
		}
		if (document.getElementById("td_" + id + "_" + i)) {
			document.getElementById("td_" + id + "_" + i).style.backgroundColor = color;
		}
	}
}

function unSelectMessage(id) {
	highlight_TR(id, default_color, default_text_color);

	parent.entries_selected = array_rm_elem(parent.entries_selected, id, -1);

	if (parent.entries_selected.length === 0) {
		top.content.editor.edbody.messaging_msg_view.location = "about:blank";
	} else {
		showContent(parent.entries_selected[parent.entries_selected.length - 1]);
	}
}

function newMessage(username) {
	new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + 'messaging_newmessage.php?we_transaction=' + transaction + '&mode=u_' + encodeURI(username), 'messaging_new_message', -1, -1, 670, 530, true, false, true, false);
}