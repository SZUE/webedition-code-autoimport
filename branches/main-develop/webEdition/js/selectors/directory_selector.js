/* global top, WE, fileSelect, entries */

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
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.NEWFOLDER, top.fileSelect.data.currentDir));
}

function RenameFolder(id) {
	unselectAllFiles();
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.RENAMEFOLDER, top.fileSelect.data.currentDir, "", id));
}

function showPreview(id) {
	if (top.fspreview) {
		top.fspreview.location.replace(top.queryString(WE().consts.selectors.PREVIEW, id));
	}
}

function doClick(id, ct) {
	if (top.fspreview && top.fspreview.document.body) {
		top.fspreview.document.body.innerHTML = "";
	}
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout(function () {
				wasdblclick = false;
			}, 400);
		}
	} else {
		if (top.fileSelect.data.currentID == id && (!top.ctrlpressed)) {
			if (top.fileSelect.options.userCanRenameFolder) {
				top.RenameFolder(id);
			} else {
				selectFile(id);
			}

		} else {
			if (top.fileSelect.options.multiple) {
				if (top.shiftpressed) {
					var oldid = top.fileSelect.data.currentID;
					var currendPos = getPositionByID(id);
					var firstSelected = getFirstSelected();

					if (currendPos > firstSelected) {
						selectFilesFrom(firstSelected, currendPos);
					} else if (currendPos < firstSelected) {
						selectFilesFrom(currendPos, firstSelected);
					} else {
						selectFile(id);
					}
					top.fileSelect.data.currentID = oldid;

				} else if (!top.ctrlpressed) {
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
	if (top.ctrlpressed) {
		top.ctrlpressed = 0;
	}
	if (top.shiftpressed) {
		top.shiftpressed = 0;
	}
}

function setDir(id) {
	e = getEntry(id);
	showPreview(id);
	if (top.fspreview && top.fspreview.document.body) {
		top.fspreview.document.body.innerHTML = "";
	}
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.SETDIR, id));
	top.document.getElementById('fspath').innerHTML = e.path;
}

function selectFile(id) {
	var a = top.document.getElementsByName("fname")[0];
	if (id) {
		showPreview(id);
		e = getEntry(id);
		if (a.value != e.text &&
						a.value.indexOf(e.text + ",") === -1 &&
						a.value.indexOf("," + e.text + ",") === -1 &&
						a.value.indexOf("," + e.text + ",") === -1) {

			a.value = a.value ?
							(a.value + "," + e.text) :
							e.text;
		}
		if (top.fsbody.document.getElementById("line_" + id)) {
			top.fsbody.document.getElementById("line_" + id).classList.add("selected");
		}
		top.fileSelect.data.currentPath = e.path;
		top.fileSelect.data.currentID = id;
		top.fileSelect.data.we_editDirID = 0;
	} else {
		a.value = "";
		top.fileSelect.data.currentPath = "";
		top.fileSelect.data.we_editDirID = 0;
	}
}

function elementSelected() {
	return true;
}

function addEntry(id, txt, folder, pth, moddte, ct) {
	entries.push({
		ID: id,
		text: txt,
		isFolder: folder,
		path: pth,
		modDate: moddte,
		contentType: ct
	});
}

function writeBody(d) {
	var body = (top.fileSelect.data.we_editDirID ?
					'<input type="hidden" name="what" value="' + WE().consts.selectors.DORENAMEFOLDER + '" />' +
					'<input type="hidden" name="we_editDirID" value="' + top.fileSelect.data.we_editDirID + '" />' :
					'<input type="hidden" name="what" value="' + WE().consts.selectors.CREATEFOLDER + '" />'
					) +
					'<input type="hidden" name="order" value="' + top.fileSelect.data.order + '" />' +
					'<input type="hidden" name="rootDirID" value="' + top.fileSelect.options.rootDirID + '" />' +
					'<input type="hidden" name="table" value="' + top.fileSelect.options.table + '" />' +
					'<input type="hidden" name="id" value="' + top.fileSelect.data.currentDir + '" />' +
					'<table class="selector">' +
					(top.fileSelect.data.makeNewFolder ?
									'<tr class="newEntry">' +
									'<td class="selectoricon">' + WE().util.getTreeIcon(WE().consts.contentTypes.FOLDER, false) + '</td>' +
									'<td><input type="hidden" name="we_FolderText" value="' + WE().consts.g_l.fileselector.new_folder_name + '" /><input onMouseDown="window.inputklick=true" name="we_FolderText_tmp" type="text" value="' + WE().consts.g_l.fileselector.new_folder_name + '" class="wetextinput" /></td>' +
									'<td class="selector moddate">' + WE().consts.g_l.fileselector.date_format + '</td>' +
									'</tr>' :
									'');

	for (i = 0; i < entries.length; i++) {
		var onclick = ' onclick="return selectorOnClick(event,' + entries[i].ID + ');"';
		var ondblclick = ' onDblClick="return selectorOnDblClick(' + entries[i].ID + ');"';
		body += '<tr id="line_' + entries[i].ID + '" class="' + ((entries[i].ID == top.fileSelect.data.currentID && (!top.fileSelect.data.makeNewFolder)) ? 'selected' : '') + '" ' + ((top.fileSelect.data.we_editDirID || top.fileSelect.data.makeNewFolder) ? '' : onclick) + (entries[i].isFolder ? ondblclick : '') + '>' +
						'<td class="treeIcon">' + WE().util.getTreeIcon(entries[i].contentType, false) + '</td>' +
						(top.fileSelect.data.we_editDirID == entries[i].ID ?
										'<td class="selector"><input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="window.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
										'<td class="selector cutText directory" title="' + entries[i].text + '">' + entries[i].text
										) +
						'</td><td class="selector moddate">' + entries[i].modDate + '</td>' +
						'</tr>';
	}
	d.innerHTML = '<form name="we_form" target="fscmd" method="post" action="' + top.fileSelect.options.formtarget + '" onsubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">' + body + '</table></form>';
	if (top.fileSelect.data.makeNewFolder || top.fileSelect.data.we_editDirID) {
		top.fsbody.document.we_form.we_FolderText_tmp.focus();
		top.fsbody.document.we_form.we_FolderText_tmp.select();
	}
}

function queryString(what, id, o, we_editDirID) {
	if (!o) {
		o = top.fileSelect.data.order;
	}
	return top.fileSelect.options.formtarget + 'what=' + what + '&rootDirID=' + top.fileSelect.options.rootDirID + '&open_doc=' + top.fileSelect.options.open_doc + '&table=' + top.fileSelect.options.table + '&id=' + id + (o ? ("&order=" + o) : "") + (we_editDirID ? ("&we_editDirID=" + we_editDirID) : "");
}

function weonclick(e) {
	if (top.fspreview && top.fspreview.document.body) {
		top.fspreview.document.body.innerHTML = "";
	}
	if (top.fileSelect.data.makeNewFolder || top.fileSelect.data.we_editDirID) {
		if (!inputklick) {
			top.fileSelect.data.makeNewFolder = top.fileSelect.data.we_editDirID = false;
			document.we_form.we_FolderText.value = escape(document.we_form.we_FolderText_tmp.value);
			document.we_form.submit();
		} else {
			inputklick = false;
		}
	} else {
		inputklick = false;
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
		if (top.fileSelect.options.multiple) {
			if (!window.shiftpressed && !window.ctrlpressed) {
				top.unselectAllFiles();
			}
		} else {
			top.unselectAllFiles();
		}
	}
}