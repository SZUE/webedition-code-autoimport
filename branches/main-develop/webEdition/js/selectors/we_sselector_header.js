/* global WE, top */

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
WE().util.loadConsts("g_l.fileselector");

var name_ord = 0;
var type_ord = 0;
var date_ord = 0;
var size_ord = 0;
var dirsel = 1;
var scrollToVal = 0;
var allentries = [];


function addOptionh(txt, id) {
	var a = document.getElementById('lookin');
	a.options[a.options.length] = new Option(txt, id);
	a.selectedIndex = (a.options.length > 0 ? a.options.length - 1 : 0);
}

function openFile() {
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_fileupload_editor&we_cmd[2]=0&we_cmd[3]=sselector&we_cmd[6]=" + top.fileSelect.data.currentDir + "&we_cmd[7]=1&we_cmd[8]=sselector";
	new (WE().util.jsWindow)(window, url, "we_fileupload_editor", -1, -1, 500, 550, true, true, true, true);
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
	top.fscmd.reorderDir(top.fileSelect.data.currentDir, order);
}

function setLookin() {
	var dirs = [];
	var foo = [];
	var a = document.getElementById('lookin');
	var c = 0;

	a.options.length = 0;
	foo = top.fileSelect.data.currentDir.split("/");
	for (j = 0; j < foo.length; j++) {
		if (foo[j] !== "") {
			dirs[c] = foo[j];
			c++;
		}
	}
	foo = top.rootDir.split("/");
	root = "/";
	for (j = 0; j < foo.length; j++) {
		if (foo[j] !== "") {
			root = foo[j];
		}
	}

	addOptionh(root, "/");
	for (i = 0; i < dirs.length; i++) {
		if (a.options[i].value == "/") {
			addOptionh(dirs[i], a.options[i].value + dirs[i]);
		} else {
			addOptionh(dirs[i], a.options[i].value + "/" + dirs[i]);
		}
	}

}

function closeOnEscape() {
	return true;
}


function addOption(txt, id) {
	var a = document.getElementsByName("filter")[0];
	a.options[a.options.length] = new Option(txt, id);
	a.selectedIndex = 0;
}

function editFile() {
	if (!top.dirsel) {
		var a = document.getElementsByName("fname")[0];
		if ((top.fileSelect.data.currentID !== "") && (a.value !== "")) {
			if (a.value != top.currentName) {
				top.fileSelect.data.currentID = top.sitepath + top.rootDir + top.fileSelect.data.currentDir + "/" + a.value;
			}
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=selectorEdit&id=" + top.fileSelect.data.currentID;
			new (WE().util.jsWindow)(window, url, "we_fseditFile", -1, -1, 600, 500, true, false, true, true);
		} else {
			top.we_showMessage(WE().consts.g_l.fileselector.edit_file_nok, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	} else {
		top.we_showMessage(g_l.edit_file_is_folder, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}