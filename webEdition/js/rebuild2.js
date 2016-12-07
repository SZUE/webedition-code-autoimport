/*
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
/* global top, WE */
function handle_event(what) {
	f = document.we_form;
	switch (what) {
		case "previous":
			f.step.value = 0;
			f.target = "wizbody";
			break;
		case "next":
			if (document._errorMessage !== undefined && document._errorMessage !== "") {
				top.we_showMessage(WE().consts.g_l.rebuild.noFieldsChecked, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			} else {
				top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "disabled");
				top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "disabled");
				top.wizbusy.showRefreshButton();
				f.step.value = 2;
				f.target = "wizcmd";
			}
			break;
	}
	f.submit();
}
function we_cmd() {
	f = document.we_form;
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var cats,i, folders;

	switch (args[0]) {
		case "we_selector_directory":
			new (WE().util.jsWindow)(window, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(window, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "add_cat":
			var catsToAdd = args[1].allIDs;
			cats = makeArrayFromCSV(f.categories.value);
			for (i = 0; i < catsToAdd.length; i++) {
				if (!WE().util.in_array(catsToAdd[i], cats)) {
					cats.push(catsToAdd[i]);
				}
			}
			f.categories.value = cats.join(",");
			f.step.value = 1;
			f.submit();
			break;
		case "del_cat":
			var catToDel = args[1];
			cats = makeArrayFromCSV(f.categories.value);
			var newcats = [];
			for (i = 0; i < cats.length; i++) {
				if (cats[i] != catToDel) {
					newcats.push(cats[i]);
				}
			}

			f.categories.value = newcats.join(",");
			f.step.value = 1;
			f.submit();
			break;
		case "del_all_cats":
			f.categories.value = "";
			f.step.value = 1;
			f.submit();
			break;
		case "add_folder":
			var foldersToAdd = args[1].allIDs;
			folders = makeArrayFromCSV(f[WE().session.rebuild.folders].value);
			for (i = 0; i < foldersToAdd.length; i++) {
				if (!WE().util.in_array(foldersToAdd[i], folders)) {
					folders.push(foldersToAdd[i]);
				}
			}
			f[WE().session.rebuild.folders].value = folders.join(",");
			f.step.value = 1;
			f.submit();
			break;
		case "del_folder":
			var folderToDel = args[1];
			folders = makeArrayFromCSV(f[WE().session.rebuild.folders].value);
			var newfolders = [];
			for (i = 0; i < folders.length; i++) {
				if (folders[i] != folderToDel) {
					newfolders.push(folders[i]);
				}
			}
			f[WE().session.rebuild.folders].value = newfolders.join(",");
			f.step.value = 1;
			f.submit();
			break;
		case "del_all_folders":
			f[WE().session.rebuild.folders].value = "";
			f.step.value = 1;
			f.submit();
			break;
		case "toggle_all_fields":
			var _elem = document.we_form.elements;
			var checked = 0;
			for (i = 0; i < _elem.length; i++) {
				if (_elem[i].name.substring(0, 7) == "_field[") {
					_elem[i].checked = !_elem[i].checked;
					if (_elem[i].checked) {
						checked++;
					}
				}
			}
			document._errorMessage = (checked > 0 ? "" : WE().consts.g_l.rebuild.noFieldsChecked);
			break;
		default:
			opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}
function checkForError() {
	var _elem = document.we_form.elements;
	var _elemLength = _elem.length;
	var _fieldsChecked = false;
	for (var i = 0; i < _elemLength; i++) {
		if (_elem[i].name.substring(0, 7) == "_field[") {
			if (_elem[i].checked) {
				_fieldsChecked = true;
				break;
			}
		}
	}
	document._errorMessage = (_fieldsChecked === false ?
					WE().consts.g_l.rebuild.noFieldsChecked :
					"");
}
function makeArrayFromCSV(csv) {
	if (csv.length && csv.substring(0, 1) === ",") {
		csv = csv.substring(1, csv.length);
	}
	if (csv.length && csv.substring(csv.length - 1, csv.length) === ",") {
		csv = csv.substring(0, csv.length - 1);
	}
	return (csv.length === 0 ?
					[] :
					csv.split(/,/)
					);

}
function set_button_state() {
	if (top.wizbusy) {
		top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "enabled");
		top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "enabled");
	} else {
		setTimeout(set_button_state, 300);
	}
}
set_button_state();