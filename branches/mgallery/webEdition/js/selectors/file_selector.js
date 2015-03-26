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
		top.fsfooter.press_ok_button();
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
	top.fscmd.location.replace(top.queryString(queryType.CMD, top.currentDir, order));
}

function goBackDir() {
	setDir(parentID);
}

function getEntry(id) {
	for (var i = 0; i < entries.length; i++) {
		if (entries[i].ID == id) {
			return entries[i];
		}
	}
	return new entry(0, "", "/", 1, "/");
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
	} else {
		selectFile(id);
	}
}

function setDir(id) {
	e = getEntry(id);
	currentID = id;
	currentDir = id;
	currentPath = e.path;
	currentText = e.text;
	top.fsfooter.document.we_form.fname.value = e.text;
	top.fscmd.location.replace(top.queryString(queryType.CMD, id));
}

function setRootDir() {
	setDir(options.rootDirID);
}

function selectFile(id) {
	e = getEntry(id);
	top.fsfooter.document.we_form.fname.value = e.text;
	currentText = e.text;
	currentPath = e.path;
	currentID = id;
}

function entry(ID, icon, text, isFolder, path) {
	this.ID = ID;
	this.icon = icon;
	this.text = text;
	this.isFolder = isFolder;
	this.path = path;
}

function addEntry(ID,icon,text,isFolder,path){
	entries[entries.length] = new entry(ID,icon,text,isFolder,path);
}
