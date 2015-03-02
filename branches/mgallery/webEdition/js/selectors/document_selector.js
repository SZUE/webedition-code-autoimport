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
	if (top.fspreview.document.body) {
		top.fspreview.document.body.innerHTML = "";
	}
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout("wasdblclick=0;", 400);
		}
	} else if (getEntry(id).contentType != "folder" || (option.canSelectDir)) {
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
			} else if (isFileSelected(id)) {
				unselectFile(id);
			} else {
				selectFile(id);

			}
		} else {
			selectFile(id);
		}
	} else {
		showPreview(id);

	}
	if (fsbody.ctrlpressed) {
		fsbody.ctrlpressed = 0;
	}
	if (fsbody.shiftpressed) {
		fsbody.shiftpressed = 0;
	}
}

function previewFolder(id) {
	alert(id);
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
	fname = top.fsfooter.document.getElementsByName("fname");
	if (id) {
		e = getEntry(id);
		fspath.document.body.innerHTML = e.path;
		if (fname && fname[0].value != e.text &&
						fname[0].value.indexOf(e.text + ",") == -1 &&
						fname[0].value.indexOf("," + e.text + ",") == -1 &&
						fname[0].value.indexOf("," + e.text + ",") == -1) {
			fname[0].value = top.fsfooter.document.we_form.fname.value ?
							(fname[0].value + "," + e.text) :
							e.text;
		}

		if (top.fsbody.document.getElementById("line_" + id))
			top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
		currentPath = e.path;
		currentID = id;
		we_editDirID = 0;
		currentType = e.contentType;

		showPreview(id);
	} else {
		fname[0].value = "";
		currentPath = "";
		we_editDirID = 0;
	}
}

function entry(ID, icon, text, isFolder, path, modDate, contentType, published, title) {
	this.ID = ID;
	this.icon = icon;
	this.text = text;
	this.isFolder = isFolder;
	this.path = path;
	this.modDate = modDate;
	this.contentType = contentType;
	this.published = published;
	this.title = title;
}

function addEntry(ID, icon, text, isFolder, path, modDate, contentType, published, title) {
	entries[entries.length] = new entry(ID, icon, text, isFolder, path, modDate, contentType, published, title);
}

function setFilter(ct) {
	top.fscmd.location.replace(top.queryString(queryType.CMD, top.currentDir, "", "", ct));
}

function showPreview(id) {
	if (top.fspreview) {
		top.fspreview.location.replace(top.queryString(queryType.PREVIEW, id));
	}
}

function reloadDir() {
	top.fscmd.location.replace(top.queryString(queryType.CMD,top.currentDir));
}

function newFile() {
	url="we_fs_uploadFile.php?pid="+top.currentDir+"&tab="+top.table+"&ct="+currentType;
	new jsWindow(url,"we_fsuploadFile",-1,-1,450,660,true,false,true);
}