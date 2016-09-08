/* global WE */

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

var sel_color = "#697ace";
var default_color = "#000000";

// Highlighting-Stuff start
function selectEntryHandler(id) {
	if (parent.multi_select === false) {
		//unselect all selected entries
		for (var j = 0; j < parent.entries_selected.length; j++) {
			highlight_Elem(parent.entries_selected[j], default_color);
		}
		parent.entries_selected = [];
		doSelectMessage(id, '');
		return;
	}
	if (WE().util.in_array(id, parent.entries_selected)) {
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

	entries_selected = entries_selected.concat([String(id)]);
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
	self.focus();
}