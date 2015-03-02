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

function drawNewFolder() {
	unselectAllFiles();
	top.fscmd.location.replace(top.queryString(queryType.NEWFOLDER, currentDir));
}

function RenameFolder(id) {
	unselectAllFiles();
	top.fscmd.location.replace(top.queryString(queryType.RENAMEFOLDER, currentDir, "", id));
}

function showPreview(id) {
	if (top.fspreview) {
		top.fspreview.location.replace(top.queryString(queryType.PREVIEW, id));
	}
}

function doClick(id, ct) {
	if (top.fspreview.document.body) {
		top.fspreview.document.body.innerHTML = "";
	}
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout("wasdblclick=0;", 400);
		}
	} else {
		if (top.currentID == id && (!fsbody.ctrlpressed)) {
			if (options.userCanRenameFolder) {
				top.RenameFolder(id);
			} else {
				selectFile(id);
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
	}
	if (fsbody.ctrlpressed) {
		fsbody.ctrlpressed = 0;
	}
	if (fsbody.shiftpressed) {
		fsbody.shiftpressed = 0;
	}
}

function setDir(id) {
	showPreview(id);
	if (top.fspreview.document.body) {
		top.fspreview.document.body.innerHTML = "";
	}
	top.fscmd.location.replace(top.queryString(queryType.SETDIR, id));
	e = getEntry(id);
	fspath.document.body.innerHTML = e.path;
}

function selectFile(id) {
	if (id) {
		showPreview(id);
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

		we_editDirID = 0;
	} else {
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
		we_editDirID = 0;
	}
}

function entry(ID, icon, text, isFolder, path, modDate) {
	this.ID = ID;
	this.icon = icon;
	this.text = text;
	this.isFolder = isFolder;
	this.path = path;
	this.modDate = modDate;
}

function addEntry(ID, icon, text, isFolder, path, modDate) {
	entries[entries.length] = new entry(ID, icon, text, isFolder, path, modDate);
}