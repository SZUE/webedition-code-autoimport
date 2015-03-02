/**
 * webEdition CMS
 *
 * $Rev: 9089 $
 * $Author: mokraemer $
 * $Date: 2015-01-21 16:07:44 +0100 (Mi, 21. Jan 2015) $
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
function doClick(id, ct) {
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout("wasdblclick=0;", 400);
		}
	} else {
		if (options.multiple) {
			if (fsbody.shiftpressed) {
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

			} else if (!fsbody.ctrlpressed) {

				selectFile(id);
			} else {
				if (isFileSelected(id)) {
					unselectFile(id);
				} else {
					selectFile(id);
				}
			}
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

function selectFile(id) {
	if (id) {
		e = getEntry(id);

		if (
						top.fsfooter.document.we_form.fname.value != e.text &&
						top.fsfooter.document.we_form.fname.value.indexOf(e.text + ",") == -1 &&
						top.fsfooter.document.we_form.fname.value.indexOf("," + e.text + ",") == -1 &&
						top.fsfooter.document.we_form.fname.value.indexOf("," + e.text + ",") == -1) {

			top.fsfooter.document.we_form.fname.value = top.fsfooter.document.we_form.fname.value ?
							(top.fsfooter.document.we_form.fname.value + "," + e.text) :
							e.text;
		}
		top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
		currentPath = e.path;
		currentID = id;
	} else {
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
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
		if (elem = top.fsbody.document.getElementById("line_" + entries[i].ID)) {
			elem.style.backgroundColor = "white";
		}
	}
	top.fsfooter.document.we_form.fname.value = "";
}