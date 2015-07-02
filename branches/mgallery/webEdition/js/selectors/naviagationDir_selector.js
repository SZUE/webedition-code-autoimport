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
			top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
		}
		top.currentPath = e.path;
		top.currentID = id;
		top.we_editDirID = 0;
	} else {
		top.document.getElementsByName("fname")[0].value = "";
		top.currentPath = "";
		top.we_editDirID = 0;
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
	var body = (top.we_editDirID ?
					'<input type="hidden" name="what" value="' + top.consts.DORENAMEFOLDER + '" />' +
					'<input type="hidden" name="we_editDirID" value="' + top.we_editDirID + '" />' :
					'<input type="hidden" name="what" value="' + top.consts.CREATEFOLDER + '" />'
					) +
					'<input type="hidden" name="order" value="' + top.order + '" />' +
					'<input type="hidden" name="rootDirID" value="' + top.options.rootDirID + '" />' +
					'<input type="hidden" name="table" value="' + top.options.table + '" />' +
					'<input type="hidden" name="id" value="' + top.currentDir + '" />' +
					'<table class="default" width="100%">' +
					(makeNewFolder ?
									'<tr style="background-color:#DFE9F5;">' +
									'<td class="selectoricon">' + getTreeIcon('folder', false) + '</td>' +
									'<td><input type="hidden" name="we_FolderText" value="' + g_l.newFolder + '" />' +
									'<input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + g_l.newFolder + '"  class="wetextinput" style="width:100%" /></td>' +
									'</tr>' :
									'');
	for (i = 0; i < entries.length; i++) {
		var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(!top.wasdblclick){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=false;}\',300);return true"';
		var ondblclick = ' onDblClick="top.wasdblclick=true;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
		body += '<tr id="line_' + entries[i].ID + '" style="' + ((entries[i].ID == top.currentID && (!makeNewFolder)) ? 'background-color:#DFE9F5;' : '') + 'cursor:pointer;' + ((we_editDirID != entries[i].ID) ? '' : '') + '"' + ((we_editDirID || makeNewFolder) ? '' : onclick) + (entries[i].isFolder ? ondblclick : '') + '>' +
						'<td class="selector selectoricon">' + getTreeIcon((entries[i].isFolder ? 'folder' : 'we/navigation'), false) + '</td>' +
						(we_editDirID == entries[i].ID ?
										'<td class="selector"><input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
										'<td class="selector" style="" >' + entries[i].text
										) +
						'</td></tr>';
	}

	d.innerHTML = '<form name="we_form" target="fscmd" method="post" action="' + top.options.formtarget + '" />' + body + '</table></form>';
	if (makeNewFolder || top.we_editDirID) {
		top.fsbody.document.we_form.we_FolderText_tmp.focus();
		top.fsbody.document.we_form.we_FolderText_tmp.select();
	}
}

function queryString(what, id, o, we_editDirID) {
	if (!o) {
		o = top.order;
	}
	if (!we_editDirID) {
		we_editDirID = "";
	}
	return options.formtarget + 'what=' + what + '&rootDirID=' + options.rootDirID + '&open_doc=' + options.open_doc + '&table=' + options.table + '&id=' + id + (o ? ("&order=" + o) : "") + (we_editDirID ? ("&we_editDirID=" + we_editDirID) : "");
}

function weonclick(e) {
	if (top.makeNewFolder || top.we_editDirID) {
		if (!inputklick) {
			top.makeNewFolder = top.we_editDirID = false;
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
		if (top.options.multiple) {
			if (!self.shiftpressed && !self.ctrlpresseds) {
				top.unselectAllFiles();
			}
		} else {
			top.unselectAllFiles();
		}
	}
}