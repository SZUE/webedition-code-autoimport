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

function unselectFile(id) {
	e = getEntry(id);
	top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "white";

	var foo = top.fsfooter.document.we_form.fname.value.split(/,/);

	for (var i = 0; i < foo.length; i++) {
		if (foo[i] == e.text) {
			foo[i] = "";
			break;
		}
	}
	var str = "";
	for (var i = 0; i < foo.length; i++) {
		if (foo[i]) {
			str += foo[i] + ",";
		}
	}
	str = str.replace(/(.*),$/, "$1");
	top.fsfooter.document.we_form.fname.value = str;
}

function selectFilesFrom(from, to) {
	unselectAllFiles();
	for (var i = from; i <= to; i++) {
		selectFile(entries[i].ID);
	}
}

function getFirstSelected() {
	for (var i = 0; i < entries.length; i++) {
		if (top.fsbody.document.getElementById("line_" + entries[i].ID).style.backgroundColor != "white") {
			return i;
		}
	}
	return -1;
}

function getPositionByID(id) {
	for (var i = 0; i < entries.length; i++) {
		if (entries[i].ID == id) {
			return i;
		}
	}
	return -1;
}
function isFileSelected(id) {
	return (top.fsbody.document.getElementById("line_" + id).style.backgroundColor && (top.fsbody.document.getElementById("line_" + id).style.backgroundColor != "white"));
}

function unselectAllFiles() {
	for (var i = 0; i < entries.length; i++) {
		top.fsbody.document.getElementById("line_" + entries[i].ID).style.backgroundColor = "white";
	}
	top.fsfooter.document.we_form.fname.value = "";
	top.fsheader.disableDelBut()
}

function selectFile(id) {
	if (id) {
		e = getEntry(id);

		if (top.fsfooter.document.we_form.fname.value != e.text &&
						top.fsfooter.document.we_form.fname.value.indexOf(e.text + ",") == -1 &&
						top.fsfooter.document.we_form.fname.value.indexOf("," + e.text + ",") == -1 &&
						top.fsfooter.document.we_form.fname.value.indexOf("," + e.text + ",") == -1) {

			top.fsfooter.document.we_form.fname.value = top.fsfooter.document.we_form.fname.value ?
							(top.fsfooter.document.we_form.fname.value + "," + e.text) :
							e.text;
		}
		if (top.fsbody.document.getElementById("line_" + id))
			top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
		currentPath = e.path;
		currentID = id;
		if (id)
			top.fsheader.enableDelBut();
		we_editCatID = 0;
	} else {
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
		we_editCatID = 0;
	}
}

function exit_close() {
	if (!noChoose && hot) {
		opener.setScrollTo();
		opener.top.we_cmd("reload_editpage");
	}
	self.close();
}

function doClick(id, ct) {
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout("wasdblclick=0;", 400);
		} else if (top.currentID == id) {
			if (perms.EDIT_KATEGORIE) {
				top.RenameEntry(id);
			}
		}
	} else if (top.currentID == id && (!fsbody.ctrlpressed)) {
		if (perms.EDIT_KATEGORIE) {
			top.RenameEntry(id);
		}

	} else if (fsbody.shiftpressed) {
		var oldid = currentID;
		var currendPos = getPositionByID(id);
		var firstSelected = getFirstSelected();
		if (currendPos > firstSelected) {
			selectFilesFrom(firstSelected, currendPos);
		} else if (currendPos < firstSelected) {
			selectFilesFrom(currendPos, firstSelected);
		} else {
			selectFile(id);
		}
		currentID = oldid;
		hidePref(id);
	} else if (!fsbody.ctrlpressed) {
		showPref(id);
		selectFile(id);
	} else {
		hidePref(id);
		if (isFileSelected(id)) {
			unselectFile(id);
		} else {
			selectFile(id);
		}
	}

	if (fsbody.ctrlpressed) {
		fsbody.ctrlpressed = 0;
	}

	if (fsbody.shiftpressed) {
		fsbody.shiftpressed = 0;
	}
}

function setDir(id) {
	e = getEntry(id);
	if (id == 0) {
		e.text = "";
	}
	currentID = id;
	currentDir = id;
	currentPath = e.path;
	top.fsfooter.document.we_form.fname.value = e.text;
	if (id) {
		top.fsheader.enableDelBut();
	}
	top.fscmd.location.replace(top.queryString(queryType.CMD, id));
}

function drawNewFolder() {
	unselectAllFiles();
	top.makeNewFolder = true;
	top.writeBody(top.fsbody.document.body);
	top.makeNewFolder = false;
}
function drawNewCat() {
	unselectAllFiles();
	top.makeNewCat = true;
	top.writeBody(top.fsbody.document.body);
	top.makeNewCat = false;
}
function deleteEntry() {
	if (confirm(g_l.deleteQuestion)) {
		var todel = "";
		for (var i = 0; i < entries.length; i++) {
			if (isFileSelected(entries[i].ID)) {
				todel += entries[i].ID + ",";
			}
		}
		if (todel) {
			todel = "," + todel;
		}
		top.fscmd.location.replace(top.queryString(queryType.DEL, top.currentID) + "&todel=" + encodeURI(todel));
		if (top.fsvalues)
			top.fsvalues.location.replace(top.queryString(queryType.PROPERTIES, 0));
		top.fsheader.disableDelBut();
	}

}
function RenameEntry(id) {
	top.we_editCatID = id;
	top.writeBody(top.fsbody.document.body);
	selectFile(id);
	top.we_editCatID = 0;
}