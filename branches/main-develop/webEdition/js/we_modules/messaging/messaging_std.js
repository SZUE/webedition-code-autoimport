/* global WE,chec,loadData,drawTree,check */

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
'use strict';

var sel_color = "#697ace",
	default_color = "#000000",
	entries_selected,
	delta_sel = [],
	addrbook_sel,
	current_sel,
	transaction;

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "setVars":
			addrbook_sel = args[1];
			current_sel = args[2];
			transaction = args[3];
			break;
		default:
			window.parent.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

// Highlighting-Stuff start
function selectEntryHandler(id) {
	if (window.parent.multi_select === false) {
		//unselect all selected entries
		for (var j = 0; j < window.parent.entries_selected.length; j++) {
			highlight_Elem(window.parent.entries_selected[j], default_color);
		}
		window.parent.entries_selected = [];
		doSelectMessage(id, '');
		return;
	}
	if (window.parent.entries_selected.indexOf(id) !== -1) {
		unSelectMessage(id, '');
	} else {
		doSelectMessage(id, '');
	}

}

function doSelectMessage(id, doc) {
	if (id == -1) {
		return;
	}

	var highlight_color = sel_color;

	var entries_selected = entries_selected.concat([String(id)]);
	highlight_Elem(id, highlight_color, doc);

}

function highlight_Elem(id, color, fr) {
	var el;
	if (fr === '') {
		el = document.getElementById(id);

	} else if (fr.document.getElementById(id)) {
		el = fr.document.getElementById(id);
	}
	if (el) {
		el.style.color = color;
	}
}

function highlight_TR(id, color) {
	for (var i = 0; i <= 3; i++) {
		document.getElementById("td_" + id + "_" + i).style.backgroundColor = color;
	}
}

function unSelectMessage(id, doc) {
	entries_selected = array_rm_elem(entries_selected, id, -1);
	highlight_Elem(id, default_color, '');
}

//Highlighting-Stuff end


function array_two_dim_search(needle, haystack, offset) {
	for (var i = 0; i < haystack.length; i++) {
		if (needle == haystack[i][offset]) {
			return i;
		}
	}

	return -1;
}

function user_array_search(needle, haystack, offset, type) {
	for (var i = 0; i < haystack.length; i++) {
		if (haystack[i][0] != type) {
			continue;
		}

		if (needle == haystack[i][offset]) {
			return i;
		}
	}

	return -1;
}

function array_rm_elem(arr, elem, tdim_off) {
	var arr1, arr2;
	var index = -1;

	// Locate elem in arr
	index = (tdim_off < 0 ?
		arr.indexOf(elem) :
		array_two_dim_search(elem, arr, tdim_off));


	// Delete entry from entries_selected
	if (index != -1) {
		arr1 = arr.slice(0, index);
		arr2 = arr.slice(index + 1, arr.length);
		return arr1.concat(arr2);
	}

	return arr;
}

function get_sel_elems(sel_box) {
	var i;
	var arr_sel = [];

	for (i = 0; i < sel_box.length; i++) {
		if (sel_box.options[i].selected === true) {
			arr_sel = arr_sel.concat([String(sel_box.options[i].value)]);
		}
	}

	return arr_sel;
}

function close_win(name) {
	WE().util.jsWindow.prototype.closeByName(name);
}


function init_check() {
	for (var i = 0; i < opener.current_sel.length; i++) {
		if (opener.current_sel[i][0] !== 'we_message') {
			continue;
		}
		check(opener.current_sel[i][1] + '&' + opener.current_sel[i][2]);
	}
}

function start() {
	loadData();
	drawTree();
	window.focus();
}

function init() {
	var i, opt;

	for (i = 0; i < current_sel.length; i++) {
		opt = new Option(current_sel[i][2], current_sel[i][1], false, false);
		document.usel.usel_currentsel.options[document.usel.usel_currentsel.length] = opt;
	}

	for (i = 0; i < addrbook_sel.length; i++) {
		opt = new Option(addrbook_sel[i][2], addrbook_sel[i][1], false, false);
		document.usel.usel_addrbook.options[document.usel.usel_addrbook.length] = opt;
	}
}

function save_addrbook() {
	var submit_str = "";
	var i, j;

	if (addrbook_sel.length > 0) {
		for (i = 0; i < addrbook_sel.length; i++) {
			for (j = 0; j < addrbook_sel[i].length; j++) {
				submit_str += encodeURI(addrbook_sel[i][j]) + ',';
			}
			submit_str = submit_str.substr(0, submit_str.length - 1);

			submit_str += "\t";
		}

		submit_str = submit_str.substr(0, submit_str.length - 1);
	}
	document.addrbook_data.addrbook_arr.value = submit_str;
	document.addrbook_data.submit();
}

function dump_entries(u_type) {
	var i;
	var new_arr = current_sel;
	var pos;

	for (i = 0; i < current_sel.length; i++) {
		if (current_sel[i][0] == u_type) {
			pos = array_two_dim_search(current_sel[i][1], new_arr, 1);
			val = document.usel.usel_currentsel.options[pos].value;
			document.usel.usel_currentsel.options[pos] = null;
			new_arr = array_rm_elem(new_arr, val, 1);
		}
	}

	current_sel = new_arr;
}

function delta_sel_add(user_type) {
	var i;
	var opt;
	var tarr;
	var len = delta_sel.length;

	dump_entries(user_type);

	for (i = 0; i < len; i++) {
		tarr = delta_sel[i].split(',');

		if (current_sel.indexOf(String(tarr[0])) !== -1) {
			continue;
		}

		current_sel = current_sel.concat([[user_type, tarr[0].toString(), tarr[1].toString()]]);
		opt = new Option(tarr[1], tarr[0], false, false);
		document.usel.usel_currentsel.options[document.usel.usel_currentsel.length] = opt;
	}
}

function rm_sel_user() {
	var sel_elems = get_sel_elems(document.usel.usel_currentsel);
	var i;
	var pos = -1;
	var val;

	for (i = 0; i < sel_elems.length; i++) {
		pos = array_two_dim_search(sel_elems[i], current_sel, 1);
		val = document.usel.usel_currentsel.options[pos].value;
		document.usel.usel_currentsel.options[pos] = null;
		current_sel = array_rm_elem(current_sel, val, 1);
	}
}

function rm_addrbook_entry() {
	var sel_elems = get_sel_elems(document.usel.usel_addrbook);
	var i;
	var pos = -1;
	var val;
	for (i = 0; i < sel_elems.length; i++) {
		pos = array_two_dim_search(sel_elems[i], addrbook_sel, 1);
		val = document.usel.usel_addrbook.options[pos].value;
		document.usel.usel_addrbook.options[pos] = null;
		addrbook_sel = array_rm_elem(addrbook_sel, val, 1);
	}
}

function add_toaddr() {
	var sel_elems = get_sel_elems(document.usel.usel_currentsel);
	var i, curr_offset, addrbook_sel;

	for (i = 0; i < sel_elems.length; i++) {
		curr_offset = array_two_dim_search(String(sel_elems[i]), current_sel, 1);
		if (array_two_dim_search(String(sel_elems[i]), addrbook_sel, 1) != -1) {
			continue;
		}

		addrbook_sel = addrbook_sel.concat([current_sel[curr_offset]]);
		opt = new Option(current_sel[curr_offset][2], current_sel[curr_offset][1], false, false);
		document.usel.usel_addrbook.options[document.usel.usel_addrbook.length] = opt;
	}
}

function add_addr2sel() {
	var i,
		addr_offset,
		opt,
		current_sel,
		sel_elems = get_sel_elems(document.usel.usel_addrbook),
		len = sel_elems.length;

	for (i = 0; i < len; i++) {
		addr_offset = array_two_dim_search(String(sel_elems[i]), addrbook_sel, 1);
		if (array_two_dim_search(String(sel_elems[i]), current_sel, 1) != -1) {
			continue;
		}

		current_sel = current_sel.concat([addrbook_sel[addr_offset]]);
		opt = new Option(addrbook_sel[addr_offset][2], addrbook_sel[addr_offset][1], false, false);
		document.usel.usel_currentsel.options[document.usel.usel_currentsel.length] = opt;
	}
}

function ok() {
	window.opener.rcpt_sel = current_sel;
	window.opener.update_rcpts();
	window.close();
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function browse_users_window() {
	new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "messaging_usel_browse_frameset.php?we_transaction=" + transaction, "messaging_usel_browse", WE().consts.size.dialog.smaller, WE().consts.size.dialog.smaller, true, false, true, false);
}