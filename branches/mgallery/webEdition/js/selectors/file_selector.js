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

function applyOnEnter(evt) {
	_elemName = "target";
	if (evt.srcElement !== undefined) { // IE
		_elemName = "srcElement";
	}

	if (!(evt[_elemName].tagName == "SELECT" ||
					(evt[_elemName].tagName == "INPUT" && evt[_elemName].name != "fname")
					)) {
		top.press_ok_button();
		return true;
	}

}
function closeOnEscape() {
	top.exit_close();

}
function in_array(needle, haystack) {
	for (var i = 0; i < haystack.length; i++) {
		if (haystack[i] == needle) {
			return true;
		}
	}
	return false;
}

function orderIt(o) {
	order = o + (order == o ? " DESC" : "");
	top.fscmd.location.replace(top.queryString(top.queryType.CMD, top.currentDir, order));
}

function goBackDir() {
	setDir(parentID);
}

function getEntry(id) {
	for (var i = 0; i < top.entries.length; i++) {
		if (top.entries[i].ID == id) {
			return top.entries[i];
		}
	}
	return {
		"ID": 0,
		"icon": "",
		"text": "/",
		"isFolder": 1,
		"path": "/"
	};
}

function clearEntries() {
	entries = [];
}

function exit_close() {
	if (top.opener.top.opener && top.opener.top.opener.top.toggleBusy) {
		top.opener.top.opener.top.toggleBusy();
	} else if (top.opener.top.toggleBusy) {
		top.opener.top.toggleBusy();
	}
	self.close();
}

function doClick(id, ct) {
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout("wasdblclick=0;", 400);
		}
	} else if (top.options.multiple) {
		if (top.shiftpressed) {
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
		} else if (!top.ctrlpressed) {
			selectFile(id);
		} else if (isFileSelected(id)) {
			unselectFile(id);
		} else {
			selectFile(id);
		}
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
	currentID = id;
	currentDir = id;
	currentPath = e.path;
	currentText = e.text;
	top.fsfooter.document.we_form.fname.value = e.text;
	top.fscmd.location.replace(top.queryString(top.queryType.CMD, id));
}

function setRootDir() {
	setDir(options.rootDirID);
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

function addEntry(ID, icon, text, isFolder, path) {
	entries.push({
		"ID": ID,
		"icon": icon,
		"text": text,
		"isFolder": isFolder,
		"path": path
	});
}

function writeBody(d) {
	var body = '<table>';
	for (i = 0; i < entries.length; i++) {
		var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true;"';
		var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
		body += '<tr' + ((entries[i].ID == top.currentID) ? ' style="background-color:#DFE9F5;cursor:pointer;"' : '') + ' id="line_' + entries[i].ID + '" style="cursor:pointer;"' + onclick + (entries[i].isFolder ? ondblclick : '') + ' >' +
						'<td class="selector" width="25" align="center">' +
						'<img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + entries[i].icon + '"/>' +
						'</td>' +
						'<td class="selector filename"  title="' + entries[i].text + '"><div class="cutText">' + entries[i].text + '</div></td>' +
						'</tr>'
	}
	body += '</table>';
	d.innerHTML = body;
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


function queryString(what, id, o) {
	if (!o) {
		o = top.order;
	}
	return options.formtarget + '?what=' + what + '&table=' + options.table + '&id=' + id + "&order=" + o + "&filter=" + currentType;
}

var allIDs = "";
var allPaths = "";
var allTexts = "";
var allIsFolder = "";

function fillIDs() {
	allIDs = ",";
	allPaths = ",";
	allTexts = ",";
	allIsFolder = ",";

	for (var i = 0; i < entries.length; i++) {
		if (isFileSelected(entries[i].ID)) {
			allIDs += (entries[i].ID + ",");
			allPaths += (entries[i].path + ",");
			allTexts += (entries[i].text + ",");
			allIsFolder += (entries[i].isFolder + ",");
		}
	}
	if (currentID != "") {
		if (allIDs.indexOf("," + currentID + ",") == -1) {
			allIDs += (currentID + ",");
		}
	}
	if (currentPath != "") {
		if (allPaths.indexOf("," + currentPath + ",") == -1) {
			allPaths += (currentPath + ",");
			allTexts += (we_makeTextFromPath(currentPath) + ",");
		}
	}

	if (allIDs == ",") {
		allIDs = "";
	}
	if (allPaths == ",") {
		allPaths = "";
	}
	if (allTexts == ",") {
		allTexts = "";
	}

	if (allIsFolder == ",") {
		allIsFolder = "";
	}
}

function we_makeTextFromPath(path) {
	position = path.lastIndexOf("/");
	if (position > -1 && position < path.length) {
		return path.substring(position + 1);
	}
	return "";
}

var ctrlpressed = false;
var shiftpressed = false;
var wasdblclick = false;
var inputklick = false;
var tout = null;
function weonclick(e) {
	if (document.all) {
		if (e.ctrlKey || e.altKey) {
			ctrlpressed = true;
		}
		if (e.shiftKey) {
			shiftpressed = true;
		}
	} else {
		if (e.altKey || e.metaKey || e.ctrlKey) {
			ctrlpressed = true;
		}
		if (e.shiftKey) {
			shiftpressed = true;
		}
	}
	if (top.options.multiple) {
		if ((self.shiftpressed == false) && (self.ctrlpressed == false)) {
			top.unselectAllFiles();
		}
	} else {
		top.unselectAllFiles();
	}
}

function press_ok_button() {
	if(top.fsfooter.document.we_form.fname.value==""){
		top.exit_close();
	}else{
		top.exit_open();
	};
}
function disableDelBut(){
	switch_button_state("delete", "delete_enabled", "disabled");
}
function enableDelBut(){
	switch_button_state("delete", "delete_enabled", "enabled");
}