/* global WE, top, entries */

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
'use strict';

function doClick(id, ct) {
	if (top.fspreview && top.fspreview.document.body) {
		top.fspreview.document.body.innerHTML = "";
	}
	if (ct == 1) {
		if (top.metaKeys.doubleClick) {
			setDir(id);
			window.setTimeout(function () {
				top.metaKeys.doubleClick = false;
			}, 400);
		}
	} else if (top.getEntry(id).contentType != WE().consts.contentTypes.FOLDER || (top.fileSelect.options.canSelectDir)) {
		if (top.fileSelect.options.multiple) {
			if (top.metaKeys.shift) {
				var oldid = top.fileSelect.data.currentID;
				var currendPos = top.getPositionByID(id);
				var firstSelected = top.getFirstSelected();

				if (currendPos > firstSelected) {
					top.selectFilesFrom(firstSelected, currendPos);
				} else if (currendPos < firstSelected) {
					top.selectFilesFrom(currendPos, firstSelected);
				} else {
					top.selectFile(id);
				}
				top.fileSelect.data.currentID = oldid;

			} else if (!top.metaKeys.ctrl) {
				top.selectFile(id);
			} else if (top.isFileSelected(id)) {
				top.unselectFile(id);
			} else {
				top.selectFile(id);
			}
		} else {
			top.selectFile(id);
		}
	} else {
		showPreview(id);

	}
	if (top.metaKeys.ctrl) {
		top.metaKeys.ctrl = 0;
	}
	if (top.metaKeys.shift) {
		top.metaKeys.shift = 0;
	}
}

function previewFolder(id) {
	window.alert(id);
}

function setDir(id) {
	showPreview(id);
	if (top.fspreview && top.fspreview.document.body) {
		top.fspreview.document.body.innerHTML = "";
	}
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.SETDIR, id));
	var e = top.getEntry(id);
	top.document.getElementById('fspath').innerHTML = e.path;
}

function selectFile(id) {
	var a = top.document.getElementsByName("fname")[0];
	if (id) {
		var e = top.getEntry(id);
		top.document.getElementById('fspath').innerHTML = e.path;
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
		top.fileSelect.data.currentType = e.contentType;
		showPreview(id);
	} else {
		a.value = "";
		top.fileSelect.data.currentPath = "";
		top.fileSelect.data.we_editDirID = 0;
	}
}

function addEntry(ID, text, extension, isFolder, path, modDate, contentType, published, title) {
	entries.push({
		ID: ID,
		text: text,
		isFolder: isFolder,
		path: path,
		modDate: modDate,
		contentType: contentType,
		extension: extension,
		published: published,
		title: title,
	});
}

function setFilter(ct) {
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.CMD, top.fileSelect.data.currentDir, "", "", ct));
}

function showPreview(id) {
	if (top.fspreview) {
		top.fspreview.location.replace(top.queryString(WE().consts.selectors.PREVIEW, id));
	}
}

function reloadDir() {
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.CMD, top.fileSelect.data.currentDir));
}

function newFile() {
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_fileupload_editor&we_cmd[1]=" + top.fileSelect.data.currentType + "&we_cmd[3]=selector&we_cmd[4]=" + top.fileSelect.data.currentDir + "&we_cmd[5]=1&we_cmd[7]=selector_insertFromUploader";
	new (WE().util.jsWindow)(window, url, "we_fileupload_editor", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true, true);
}

function newCollection() {
	var url = "we_cmd.php?we_cmd[0]=editNewCollection&we_cmd[1]=write_back_to_selector&fixedpid=" + top.currentDir;
	new (WE().util.jsWindow)(window, url, "we_newICollection", WE().consts.size.dialog.small, WE().consts.size.dialogSmall, true, false, true);
}

function writeBodyDocument(d) {
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
									'<td class="filename"><input type="hidden" name="we_FolderText" value="' + WE().consts.g_l.fileselector.new_folder_name + '" /><input onMouseDown="top.metaKeys.inputClick=true" name="we_FolderText_tmp" type="text" value="' + WE().consts.g_l.fileselector.new_folder_name + '" class="wetextinput" /></td>' +
									'<td class="selector title">' + WE().consts.g_l.fileselector.folder + '</td>' +
									'<td class="selector moddate">' + WE().consts.g_l.fileselector.date_format + '</td>' +
									'</tr>' :
									'');
	for (var i = 0; i < entries.length; i++) {
		var onclick = ' onclick="return selectorOnClick(event,' + entries[i].ID + ');"';
		var ondblclick = ' onDblClick="return selectorOnDblClick(' + entries[i].ID + ');"';
		body += '<tr id="line_' + entries[i].ID + '" class="' + ((entries[i].ID == top.fileSelect.data.currentID) ? 'selected' : '') + '" ' + ((top.fileSelect.data.we_editDirID || top.fileSelect.data.makeNewFolder) ? '' : onclick) + (entries[i].isFolder ? ondblclick : '') + '>' +
						'<td class="selector selectoricon">' + WE().util.getTreeIcon(entries[i].contentType, false) + '</td>' +
						'<td class="selector filename"' + (entries[i].published === 0 && entries[i].isFolder === 0 ? ' style="color: red;"' : "") + ' title="' + entries[i].text + '">' +
						(top.fileSelect.data.we_editDirID == entries[i].ID ?
										'<input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="top.metaKeys.inputClick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
										'<div class="cutText">' + entries[i].text + '</div><div class="extension">' + entries[i].extension + '</div>'
										) +
						'</td>' +
						'<td class="selector title" title="' + (top.fileSelect.options.useID ? entries[i].ID : entries[i].title) + '"><div class="cutText">' + (top.fileSelect.options.useID ? entries[i].ID : entries[i].title) + '</div></td>' +
						'<td class="selector moddate">' + entries[i].modDate + '</td>' +
						'</tr>';
	}
	d.innerHTML = '<form name="we_form" target="fscmd" method="post" action="' + top.fileSelect.options.formtarget + '" onsubmit="document.we_form.we_FolderText.value=document.we_form.we_FolderText_tmp.value;return true;">' + body + '</table></form>';
	if (top.fileSelect.data.makeNewFolder || top.fileSelect.data.we_editDirID) {
		top.fsbody.document.we_form.we_FolderText_tmp.focus();
		top.fsbody.document.we_form.we_FolderText_tmp.select();
	}
}

function writeBody(d) {
	writeBodyDocument(d);
}

function queryString(what, id, o, we_editDirID, filter) {
	if (!o) {
		o = top.fileSelect.data.order;
	}
	if (!filter) {
		filter = top.fileSelect.data.currentType;
	}
	return top.fileSelect.options.formtarget + 'what=' + what + '&rootDirID=' + top.fileSelect.options.rootDirID + '&open_doc=' + top.fileSelect.options.open_doc + '&table=' + top.fileSelect.options.table + '&id=' + id + (o ? ("&order=" + o) : "") + (we_editDirID ? ("&we_editDirID=" + we_editDirID) : "") + (filter ? ("&filter=" + filter) : "");
}
