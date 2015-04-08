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
		if (top.options.multiple) {
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
	} else {
		showPreview(id);

	}
	if (top.ctrlpressed) {
		top.ctrlpressed = 0;
	}
	if (top.shiftpressed) {
		top.shiftpressed = 0;
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
	top.fscmd.location.replace(top.queryString(top.queryType.SETDIR, id));
	e = getEntry(id);
	top.document.getElementById('fspath').innerHTML = e.path;
}

function selectFile(id) {
	fname = top.fsfooter.document.getElementsByName("fname");
	if (id) {
		e = getEntry(id);
		top.document.getElementById('fspath').innerHTML = e.path;
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

function addEntry(ID, icon, text, extension, isFolder, path, modDate, contentType, published, title) {
	entries.push({
		"ID": ID,
		"icon": icon,
		"text": text,
		"extension": extension,
		"isFolder": isFolder,
		"path": path,
		"modDate": modDate,
		"contentType": contentType,
		"published": published,
		"title": title
	});
}

function setFilter(ct) {
	top.fscmd.location.replace(top.queryString(top.queryType.CMD, top.currentDir, "", "", ct));
}

function showPreview(id) {
	if (top.fspreview) {
		top.fspreview.location.replace(top.queryString(top.queryType.PREVIEW, id));
	}
}

function reloadDir() {
	top.fscmd.location.replace(top.queryString(top.queryType.CMD, top.currentDir));
}

function newFile() {
	url = "we_fs_uploadFile.php?pid=" + top.currentDir + "&tab=" + top.table + "&ct=" + currentType;
	new jsWindow(url, "we_fsuploadFile", -1, -1, 450, 660, true, false, true);
}

function writeBodyDocument(d) {
	var body = (we_editDirID ?
					'<input type="hidden" name="what" value="' + top.consts.DORENAMEFOLDER + '" />' +
					'<input type="hidden" name="we_editDirID" value="' + top.we_editDirID + '" />' :
					'<input type="hidden" name="what" value="' + top.consts.CREATEFOLDER + '" />'
					) +
					'<input type="hidden" name="order" value="' + top.order + '" />' +
					'<input type="hidden" name="rootDirID" value="' + top.options.rootDirID + '" />' +
					'<input type="hidden" name="table" value="' + top.options.table + '" />' +
					'<input type="hidden" name="id" value="' + top.currentDir + '" />' +
					'<table class="selector">' +
					(makeNewFolder ?
									'<tr>' +
									'<td class="treeIcon"><img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + top.consts.FOLDER_ICON + '"></td>' +
									'<td class="filename"><input type="hidden" name="we_FolderText" value="' + g_l.new_folder_name + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + g_l.new_folder_name + '" class="wetextinput" style="width:100%" /></td>' +
									'<td class="selector title">' + g_l.folder + '</td>' +
									'<td class="selector moddate">' + g_l.date_format + '</td>' +
									'</tr>' :
									'');
	for (i = 0; i < entries.length; i++) {
		var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true"';
		var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
		body += '<tr' + ((entries[i].ID == top.currentID) ? ' style="background-color:#DFE9F5;cursor:pointer;"' : "") + ' id="line_' + entries[i].ID + '" style="cursor:pointer;" ' + ((we_editDirID || makeNewFolder) ? "" : onclick) + (entries[i].isFolder ? ondblclick : "") + '>' +
						'<td class="selector treeIcon" align="center"><img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + entries[i].icon + '" /></td>' +
						'<td class="selector filename"' + (entries[i].published == 0 && entries[i].isFolder == 0 ? ' style="color: red;"' : "") + ' title="' + entries[i].text + '">' +
						(we_editDirID == entries[i].ID ?
										'<input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
										'<div class="cutText">' + entries[i].text + '</div><div class="extension">' + entries[i].extension + '</div>'
										) +
						'</td>' +
						'<td class="selector title" title="' + eval(options.col2js) + '"><div class="cutText">' + eval(options.col2js) + '</div></td>' +
						'<td class="selector moddate">' + entries[i].modDate + '</td>' +
						'</tr>';
	}
	d.innerHTML = '<form name="we_form" target="fscmd" action="' + top.options.formtarget + '" onsubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">' + body + '</table></form>';
	if (makeNewFolder || top.we_editDirID) {
		top.fsbody.document.we_form.we_FolderText_tmp.focus();
		top.fsbody.document.we_form.we_FolderText_tmp.select();
	}
}

function writeBody(d) {
	writeBodyDocument(d);
}

function queryString(what, id, o, we_editDirID, filter) {
	if (!o) {
		o = top.order;
	}
	if (!we_editDirID) {
		we_editDirID = "";
	}
	if (!filter) {
		filter = currentType;
	}
	return options.formtarget + '?what=' + what + '&rootDirID=' + options.rootDirID + '&open_doc="+options.open_doc+"&table=' + options.table + '&id=' + id + (o ? ("&order=" + o) : "") + (we_editDirID ? ("&we_editDirID=" + we_editDirID) : "") + (filter ? ("&filter=" + filter) : "");
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
			if ((self.shiftpressed == false) && (self.ctrlpressed == false)) {
				top.unselectAllFiles();
			}
		} else {
			top.unselectAllFiles();
		}
	}
}