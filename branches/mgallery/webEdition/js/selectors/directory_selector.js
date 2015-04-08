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
	document.getElementById('fspath').innerHTML = e.path;
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


function addEntry(ID, icon, text, isFolder, path, modDate) {
	entries.push({
		"ID": ID,
		"icon": icon,
		"text": text,
		"isFolder": isFolder,
		"path": path,
		"modDate": modDate
	});
}

function writeBody(d) {
	var body = (we_editDirID ?
					'<input type="hidden" name="what" value="' + consts.DORENAMEFOLDER + '" />' +
					'<input type="hidden" name="we_editDirID" value="' + we_editDirID + '" />'
					:
					'<input type="hidden" name="what" value="' + consts.CREATEFOLDER + '" />'
					) +
					'<input type="hidden" name="order" value="' + order + '" />' +
					'<input type="hidden" name="rootDirID" value="' + options.rootDirID + '" />' +
					'<input type="hidden" name="table" value="' + options.table + '" />' +
					'<input type="hidden" name="id" value="' + currentDir + '" />' +
					'<table class="selector">' +
					(makeNewFolder ?
									'<tr style="background-color:#DFE9F5;">' +
									'<td align="center"><img class="treeIcon" src="' + dirs.TREE_ICON_DIR + consts.FOLDER_ICON + '"/></td>' +
									'<td><input type="hidden" name="we_FolderText" value="' + g_l.new_folder_name + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + g_l.new_folder_name + '" class="wetextinput" style="width:100%" /></td>' +
									'<td class="selector moddate">' + g_l.date_format + '</td>' +
									'</tr>' :
									'');

	for (i = 0; i < entries.length; i++) {
		var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(wasdblclick==0){doClick(' + entries[i].ID + ',0);}else{wasdblclick=0;}\',300);return true"';
		var ondblclick = ' onDblClick="wasdblclick=1;clearTimeout(tout);doClick(' + entries[i].ID + ',1);return true;"';
		body += '<tr id="line_' + entries[i].ID + '" style="' + ((entries[i].ID == currentID && (!makeNewFolder)) ? "background-color:#DFE9F5;" : "") + 'cursor:pointer;" ' + ((we_editDirID || makeNewFolder) ? "" : onclick) + (entries[i].isFolder ? ondblclick : "") + '>' +
						'<td class="treeIcon"><img class="treeIcon" src="' + dirs.TREE_ICON_DIR + (entries[i].icon ? entries[i].icon : consts.FOLDER_ICON) + '"/></td>' +
						(we_editDirID == entries[i].ID ?
										'<td class="selector treeIcon"><input type="hidden" name="we_FolderText" value="' + entries[i].text + '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
										'<td class="selector cutText directory" style="" title="' + entries[i].text + '">' + entries[i].text
										) +
						'</td><td class="selector moddate">' + entries[i].modDate + '</td>' +
						'</tr>';
	}
	d.innerHTML = '<form name="we_form" target="fscmd" action="' + options.formtarget + '" onsubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">' + body + '</table></form>';
	if (makeNewFolder || we_editDirID) {
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
	return options.formtarget + '?what=' + what + '&rootDirID=' + options.rootDirID + '&open_doc="+options.open_doc+"&table=' + options.table + '&id=' + id + (o ? ("&order=" + o) : "") + (we_editDirID ? ("&we_editDirID=" + we_editDirID) : "");
}

var ctrlpressed = false;
var shiftpressed = false;
var inputklick = false;
var wasdblclick = false;
var tout = null;
function weonclick(e) {
	if (top.fspreview.document.body) {
		top.fspreview.document.body.innerHTML = "";
	}
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