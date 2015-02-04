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
var name_ord = 0;
var type_ord = 0;
var date_ord = 0;
var size_ord = 0;

function addOption(txt, id) {
	var a = document.we_form.elements.lookin;
	a.options[a.options.length] = new Option(txt, id);
	a.selectedIndex = (a.options.length > 0 ? a.options.length - 1 : 0);
}

function openFile() {
	url = "we_sselector_uploadFile.php?pat=" + top.currentDir;
	new jsWindow(url, "we_fsuploadImage", -1, -1, 450, 360, true, false, true);
}

function reorder(name) {
	var order = 0;
	switch (name) {
		case "name":
			if (name_ord == 1) {
				order = 10;
				name_ord = 0;
			} else {
				order = 11;
				name_ord = 1;
			}
			break;
		case "type":
			if (type_ord == 1) {
				order = 20;
				type_ord = 0;
			} else {
				order = 21;
				type_ord = 1;
			}
			break;
		case "date":
			if (date_ord == 1) {
				order = 30;
				date_ord = 0;
			} else {
				order = 31;
				date_ord = 1;
			}
			break;
		case "size":
			if (size_ord == 1) {
				order = 40;
				size_ord = 0;
			} else {
				order = 41;
				size_ord = 1;
			}
			break;
	}
	top.fscmd.reorderDir(top.currentDir, order);
}

function setLookin() {
	var dirs = new Array();
	var foo = new Array();
	var a = document.we_form.elements.lookin;
	var c = 0;

	a.options.length = 0;
	foo = top.currentDir.split("/");
	for (j = 0; j < foo.length; j++) {
		if (foo[j] != "") {
			dirs[c] = foo[j];
			c++;
		}
	}
	foo = top.rootDir.split("/");
	root = "/";
	for (j = 0; j < foo.length; j++) {
		if (foo[j] != "") {
			root = foo[j];
		}
	}

	addOption(root, "/");
	for (i = 0; i < dirs.length; i++) {
		if (a.options[i].value == "/") {
			addOption(dirs[i], a.options[i].value + dirs[i]);
		} else {
			addOption(dirs[i], a.options[i].value + "/" + dirs[i]);
		}
	}

}