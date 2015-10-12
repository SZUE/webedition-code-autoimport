/* global top, WE */

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

function doClick(id, ct) {
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout(function () {
				wasdblclick = false;
			}, 400);
		}
	} else if (top.shiftpressed) {
		var oldid = top.currentID;
		var currendPos = getPositionByID(id);
		var firstSelected = getFirstSelected();

		if (currendPos > firstSelected) {
			selectFilesFrom(firstSelected, currendPos);
		} else if (currendPos < firstSelected) {
			selectFilesFrom(currendPos, firstSelected);
		} else {
			selectFile(id);
		}
		top.currentID = oldid;

	} else if (!top.ctrlpressed) {
		selectFile(id);
	} else if (isFileSelected(id)) {
		unselectFile(id);
	} else {
		selectFile(id);
	}
	if (top.ctrlpressed) {
		top.ctrlpressed = 0;
	}
	if (top.shiftpressed) {
		top.shiftpressed = 0;
	}
}

function setDir(id) {
	e = getEntry(id);
	if (id === 0) {
		e.text = "";
	}
	top.currentID = id;
	top.currentDir = id;
	top.currentPath = e.path;
	top.document.getElementsByName("fname")[0].value = e.text;
	if (id) {
		top.enableDelBut();
	}
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.CMD, id));
}

function selectFile(id) {
	var a=top.document.getElementsByName("fname")[0];
	if (id) {
		e = getEntry(id);

		if (a.value != e.text &&
						a.value.indexOf(e.text + ",") == -1 &&
						a.value.indexOf("," + e.text + ",") == -1 &&
						a.value.indexOf("," + e.text + ",") == -1) {

			a.value = a.value ?
							(a.value + "," + e.text) :
							e.text;
		}
		if (top.fsbody.document.getElementById("line_" + id)){
			top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
		}
		top.currentPath = e.path;
		top.currentID = id;
		if (id){
			top.enableDelBut();
			}
		top.we_editDelID = 0;
	} else {
		a.value = "";
		top.currentPath = "";
		top.we_editDelID = 0;
	}
}

function unselectAllFiles() {
	for (var i = 0; i < entries.length; i++) {
		top.fsbody.document.getElementById("line_" + entries[i].ID).style.backgroundColor = "white";
	}
	top.document.getElementsByName("fname")[0].value = "";
	top.disableDelBut();
}

function deleteEntry() {
	if (confirm(g_l.deleteQuestion)) {
		var todel = "";
		var docIsOpen = false;
		for (var i = 0; i < entries.length; i++) {
			if (isFileSelected(entries[i].ID)) {
				todel += entries[i].ID + ",";
				if (options.seemForOpenDelSelector && entries[i].ID == options.seemForOpenDelSelector) {
					docIsOpen = true;
				}
			}
		}
		if (todel) {
			todel = "," + todel;
		}

		top.fscmd.location.replace(top.queryString(consts.DEL, top.currentID) + "&todel=" + encodeURI(todel));
		top.disableDelBut();

		if (docIsOpen) {
			top.opener.top.we_cmd("close_all_documents");
			top.opener.top.we_cmd("start_multi_editor");
		}
	}
}