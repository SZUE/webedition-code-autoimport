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

function unselectFile(id) {
	e = getEntry(id);
	top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "white";

	var foo = top.document.getElementsByName("fname")[0].value.split(/,/);

	for (var i = 0; i < foo.length; i++) {
		if (foo[i] == e.text) {
			foo[i] = "";
			break;
		}
	}
	var str = "";
	for (i = 0; i < foo.length; i++) {
		if (foo[i]) {
			str += foo[i] + ",";
		}
	}
	str = str.replace(/(.*),$/, "$1");
	top.document.getElementsByName("fname")[0].value = str;
}

function selectFilesFrom(from, to) {
	unselectAllFiles();
	for (var i = from; i <= to; i++) {
		selectFile(entries[i].ID);
	}
}

function getFirstSelected() {
	for (var i = 0; i < entries.length; i++) {
		if (top.fsbody.document.getElementById("line_" + entries[i].ID).style.backgroundColor != "white") {
			return i;
		}
	}
	return -1;
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
		top.fsbody.document.getElementById("line_" + entries[i].ID).style.backgroundColor = "white";
	}
	top.document.getElementsByName("fname")[0].value = "";
	top.disableDelBut();
}

function selectFile(id) {
	if (id) {
		e = getEntry(id);
		var a=top.document.getElementsByName("fname")[0];
		if (a.value != e.text &&
						a.value.indexOf(e.text + ",") == -1 &&
						a.value.indexOf("," + e.text + ",") == -1 &&
						a.value.indexOf("," + e.text + ",") == -1) {

			a.value = a.value ?
							(a.value + "," + e.text) :
							e.text;
		}
		if (top.fsbody.document.getElementById("line_" + id))
			top.fsbody.document.getElementById("line_" + id).style.backgroundColor = "#DFE9F5";
		currentPath = e.path;
		currentID = id;
		if (id) {
			top.enableDelBut();
		}
		if (id !== we_editCatID) {
			we_editCatID = 0;
		}
	} else {
		top.document.getElementsByName("fname")[0].value = "";
		currentPath = "";
		we_editCatID = 0;
	}
}

function exit_close() {
	if (!noChoose && hot) {
		opener.setScrollTo();
		opener.top.we_cmd("reload_editpage");
	}
	self.close();
}

function doClick(id, ct) {
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout(function () {
				wasdblclick = false;
			}, 400);
		} else if (top.currentID == id) {
			if (perms.EDIT_KATEGORIE) {
				top.RenameEntry(id);
			}
		}
	} else if (top.currentID == id && (!top.ctrlpressed)) {
		if (perms.EDIT_KATEGORIE) {
			top.RenameEntry(id);
		}

	} else if (top.shiftpressed) {
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
		hidePref(id);
	} else if (!top.ctrlpressed) {
		showPref(id);
		selectFile(id);
	} else {
		hidePref(id);
		if (isFileSelected(id)) {
			unselectFile(id);
		} else {
			selectFile(id);
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
	if (id === 0) {
		e.text = "";
	}
	currentID = id;
	currentDir = id;
	currentPath = e.path;
	top.document.getElementsByName("fname")[0].value = e.text;
	if (id) {
		top.enableDelBut();
	}
	top.fscmd.location.replace(top.queryString(top.queryType.CMD, id));
}

function drawNewFolder() {
	unselectAllFiles();
	top.makeNewFolder = true;
	top.writeBody(top.fsbody.document.body);
}
function drawNewCat() {
	unselectAllFiles();
	top.makeNewCat = true;
	top.writeBody(top.fsbody.document.body);
}
function deleteEntry() {
	if (confirm(g_l.deleteQuestion)) {
		var todel = "";
		for (var i = 0; i < entries.length; i++) {
			if (isFileSelected(entries[i].ID)) {
				todel += entries[i].ID + ",";
			}
		}
		if (todel) {
			todel = "," + todel;
		}
		top.fscmd.location.replace(top.queryString(top.queryType.DEL, top.currentID) + "&todel=" + encodeURI(todel));
		if (top.fsvalues)
			top.fsvalues.location.replace(top.queryString(top.queryType.PROPERTIES, 0));
		top.disableDelBut();
	}

}
function RenameEntry(id) {
	top.we_editCatID = id;
	top.writeBody(top.fsbody.document.body);
	selectFile(id);
}

function showPref(id) {
	if (self.fsvalues)
		self.fsvalues.location = top.queryString(top.queryType.PROPERTIES) + "&catid=" + id;
}

function hidePref() {
	if (self.fsvalues)
		self.fsvalues.location = top.queryString(top.queryType.PROPERTIES);
}

function writeBody(d) {
	var body = (options.needIEEscape ?
					'<form name="we_form" target="fscmd" method="post" action="' + options.formtarget + '" onsubmit="document.we_form.we_EntryText.value=escape(document.we_form.we_EntryText_tmp.value);return true;">' :
					'<form name="we_form" target="fscmd" method="post" action="' + options.formtarget + '" onsubmit="document.we_form.we_EntryText.value=document.we_form.we_EntryText_tmp.value;return true;">'
					) +
					(we_editCatID ?
									'<input type="hidden" name="what" value="' + top.queryType.DO_RENAME_ENTRY + '" />' +
									'<input type="hidden" name="we_editCatID" value="' + top.we_editCatID + '" />' :
									(makeNewFolder ?
													'<input type="hidden" name="what" value="' + top.queryType.CREATEFOLDER + '" />' :
													'<input type="hidden" name="what" value="' + top.queryType.CREATE_CAT + '" />'
													)) +
					'<input type="hidden" name="order" value="' + top.order + '" />' +
					'<input type="hidden" name="rootDirID" value="' + options.rootDirID + '" />' +
					'<input type="hidden" name="table" value="' + options.table + '" />' +
					'<input type="hidden" name="id" value="' + top.currentDir + '" />' +
					'<table class="selector">' +
					(makeNewFolder ?
									'<tr style="background-color:#DFE9F5;">' +
									'<td align="center"><img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + top.consts.FOLDER_ICON + '"/></td>' +
									'<td><input type="hidden" name="we_EntryText" value="' + g_l.new_folder_name + '" /><input onMouseDown="self.inputklick=true" name="we_EntryText_tmp" type="text" value="' + g_l.new_folder_name + '" class="wetextinput" style="width:100%" /></td>' +
									'</tr>' :
									(makeNewCat ?
													'<tr style="background-color:#DFE9F5;">' +
													'<td align="center"><img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + 'cat.gif"/></td>' +
													'<td><input type="hidden" name="we_EntryText" value="' + g_l.new_cat_name + '" /><input onMouseDown="self.inputklick=true" name="we_EntryText_tmp" type="text" value="' + g_l.new_cat_name + '" class="wetextinput" style="width:35%" /></td>' +
													'</tr>' :
													'')
									);

	for (i = 0; i < entries.length; i++) {
		var onclick = ' onclick="weonclick(event);tout=setTimeout(\'if(!top.wasdblclick){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=false;}\',300);return true;"';
		var ondblclick = ' onDblClick="top.wasdblclick=true;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
		body += '<tr id="line_' + entries[i].ID + '" style="cursor:pointer;' + ((we_editCatID != entries[i].ID) ? '' : '') + '"' + ((we_editCatID || makeNewFolder || makeNewCat) ? '' : onclick) + (entries[i].isFolder ? ondblclick : '') + ' >' +
						'<td class="selector" width="25" align="center">' +
						'<img class="treeIcon" src="' + top.dirs.TREE_ICON_DIR + entries[i].icon + '"/></td>' +
						(we_editCatID == entries[i].ID ?
										'<td class="selector"><input type="hidden" name="we_EntryText" value="' + entries[i].text + '" /><input onMouseDown="self.inputklick=true" name="we_EntryText_tmp" type="text" value="' + entries[i].text + '" class="wetextinput" style="width:100%" />' :
										'<td class="selector filename"' + (we_editCatID ? '' : '') + ' title="' + entries[i].text + '"><div class="cutText">' + entries[i].text + '</div>'
										) +
						'</td></tr>';
	}
	d.innerHTML = body + '</table></form>';
	if (makeNewFolder || makeNewCat || we_editCatID) {
		top.fsbody.document.we_form.we_EntryText_tmp.focus();
		top.fsbody.document.we_form.we_EntryText_tmp.select();
	}
}

function queryString(what, id, o, we_editCatID) {
	if (!o) {
		o = top.order;
	}
	if (!we_editCatID) {
		we_editCatID = "";
	}
	return options.formtarget + 'what=' + what + '&rootDirID=' + options.rootDirID + '&table=' + options.table + '&id=' + id + (o ? ("&order=" + o) : "") + (we_editCatID ? ("&we_editCatID=" + we_editCatID) : "");
}

function weonclick(e) {
	if (top.makeNewFolder || top.makeNewCat || top.we_editCatID) {
		if (!inputklick) {
			if (parent.options.needIEEscape) {
				document.we_form.we_EntryText.value = escape(top.fsbody.document.we_form.we_EntryText_tmp.value);
			} else {
				document.we_form.we_EntryText.value = top.fsbody.document.we_form.we_EntryText_tmp.value;
			}
			top.makeNewFolder = top.makeNewCat = top.we_editCatID = false;
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
		if (!self.shiftpressed && !self.ctrlpressed) {
			top.unselectAllFiles();
		}
	}
}