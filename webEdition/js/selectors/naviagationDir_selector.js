/* global top, WE, fileSelect */

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

function selectFile(id) {
	var a = top.document.getElementsByName("fname")[0];
	if (id) {
		e = getEntry(id);
		if (a.value != e.text &&
						a.value.indexOf(e.text + ",") == -1 &&
						a.value.indexOf("," + e.text + ",") == -1 &&
						a.value.indexOf("," + e.text + ",") == -1) {

			a.value = a.value ?
							(a.value + "," + e.text) :
							e.text;

			var show = top.document.getElementById("showDiv");
			if (show) {
				show.innerHTML = top.document.getElementsByName("fname")[0].value;
			}

		}
		if (top.fsbody.document.getElementById("line_" + id)) {
			top.fsbody.document.getElementById("line_" + id).classList.add("selected");
		}
		top.fileSelect.data.currentPath = e.path;
		top.fileSelect.data.currentID = id;
		top.fileSelect.data.we_editDirID = 0;
	} else {
		top.document.getElementsByName("fname")[0].value = "";
		top.fileSelect.data.currentPath = "";
		top.fileSelect.data.we_editDirID = 0;
	}
}

function addEntry(ID, text, isFolder, path) {
	entries.push({
		"ID": ID,
		"text": text,
		"isFolder": isFolder,
		"path": path,
		contentType: (isFolder ? 'folder' : 'we/navigation')
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
									'<td class="selectoricon">' + WE().util.getTreeIcon('folder', false) + '</td>' +
									'<td><input type="hidden" name="we_FolderText" value="' + WE().consts.g_l.fileselector.newFolderNavigation + '" />' +
									'<input onMouseDown="window.inputklick=true" name="we_FolderText_tmp" type="text" value="' + WE().consts.g_l.fileselector.newFolderNavigation + '"  class="wetextinput" /></td>' +
									'</tr>' :
									'');
	for (i = 0; i < entries.length; i++) {
		var onclick = ' onclick="return selectorOnClick(event,' + entries[i].ID + ');"';
		var ondblclick = ' onDblClick="return selectorOnDblClick(' + entries[i].ID + ');"';
		body += '<tr id="line_' + entries[i].ID + '" class="' + ((entries[i].ID == top.fileSelect.data.currentID && (!top.fileSelect.data.makeNewFolder)) ? 'selected' : '') + '"' + ((top.fileSelect.data.we_editDirID || top.fileSelect.data.makeNewFolder) ? '' : onclick) + (entries[i].isFolder ? ondblclick : '') + '>' +
						'<td class="selector selectoricon">' + WE().util.getTreeIcon((entries[i].isFolder ? 'folder' : 'we/navigation'), false) + '</td>' +
						(top.fileSelect.data.we_editDirID == entries[i].ID ?
										'<td class="selector"><input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="window.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
										'<td class="selector" style="" >' + entries[i].text
										) +
						'</td></tr>';
	}

	d.innerHTML = '<form name="we_form" target="fscmd" method="post" action="' + top.fileSelect.options.formtarget + '" />' + body + '</table></form>';
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

function elementSelected() {
	return true;
}
