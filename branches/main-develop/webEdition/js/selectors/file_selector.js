/* global top, WE */

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
var fileSelect = WE().util.getDynamicVar(document, 'loadVarSelectors', 'data-selector');
WE().util.loadConsts(document, "g_l.fileselector");
WE().util.loadConsts(document, "selectors");

var entries = [];
var clickCount = 0;
var mk = null;
var allIDs = "";
var allPaths = "";
var allTexts = "";
var allIsFolder = "";
var ctrlpressed = false;
var shiftpressed = false;
var wasdblclick = false;
var inputklick = false;
var tout = null;

function applyOnEnter(evt) {
	_elemName = "target";
	if (evt.srcElement !== undefined) { // IE
		_elemName = "srcElement";
	}

	if (!(evt[_elemName].tagName === "SELECT" ||
					(evt[_elemName].tagName === "INPUT" && evt[_elemName].name !== "fname")
					)) {
		top.press_ok_button();
		return true;
	}

}
function closeOnEscape() {
	top.exit_close();
}

function orderIt(o) {
	top.fileSelect.data.order = o + (top.fileSelect.data.order === o ? " DESC" : "");
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.CMD, top.fileSelect.data.currentDir, top.fileSelect.data.order));
}

function goBackDir() {
	setDir(top.fileSelect.data.parentID);
}

function getEntry(id) {
	for (var i = 0; i < top.entries.length; i++) {
		if (top.entries[i].ID == id) {
			return top.entries[i];
		}
	}
	return {
		"ID": 0,
		"icon": "",
		"text": "/",
		"isFolder": 1,
		"path": "/"
	};
}

function clearEntries() {
	entries = [];
}

function exit_close() {
	window.close();
}

function doClick(id, ct) {
	if (ct == 1) {
		if (wasdblclick) {
			setDir(id);
			setTimeout(function () {
				wasdblclick = false;
			}, 400);
		}
	} else if (top.fileSelect.options.multiple) {
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
		} else if (isFileSelected(id)) {
			unselectFile(id);
		} else {
			selectFile(id);
		}
	} else {
		selectFile(id);

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
	top.fileSelect.data.currentID = id;
	top.fileSelect.data.currentDir = id;
	top.fileSelect.data.currentPath = e.path;
	top.fileSelect.data.currentText = e.text;
	top.document.getElementsByName("fname")[0].value = e.text;
	top.fscmd.location.replace(top.queryString(WE().consts.selectors.CMD, id));
}

function setRootDir() {
	setDir(top.fileSelect.options.rootDirID);
}

function selectFile(id) {
	var a = top.document.getElementsByName("fname")[0];
	if (id) {
		e = getEntry(id);

		if (a.value != e.text &&
						a.value.indexOf(e.text + ",") === -1 &&
						a.value.indexOf("," + e.text + ",") === -1 &&
						a.value.indexOf("," + e.text + ",") === -1) {

			a.value = a.value ? (a.value + "," + e.text) : e.text;
		}
		top.fsbody.document.getElementById("line_" + id).classList.add("selected");
		top.fileSelect.data.currentPath = e.path;
		top.fileSelect.data.currentID = id;
	} else {
		a.value = "";
		top.fileSelect.data.currentPath = "";
	}
}

function addEntry(id, txt, folder, pth, ct) {
	entries.push({
		ID: id,
		text: txt,
		isFolder: folder,
		path: pth,
		contentType: ct
	});
}

function writeBody(d) {
	var body = '<table class="selector">';
	for (i = 0; i < entries.length; i++) {
		var onclick = ' onclick="return selectorOnClick(event,' + entries[i].ID + ');"';
		var ondblclick = ' onDblClick="return selectorOnDblClick(' + entries[i].ID + ');"';
		body += '<tr' + ((entries[i].ID == top.fileSelect.data.currentID) ? ' class="selected"' : '') + ' id="line_' + entries[i].ID + '"' + onclick + (entries[i].isFolder ? ondblclick : '') + ' >' +
						'<td class="selector selectoricon">' + WE().util.getTreeIcon(entries[i].contentType, false) + '</td>' +
						'<td class="selector filename"  title="' + entries[i].text + '"><div class="cutText">' + entries[i].text + '</div></td>' +
						'</tr>';
	}
	body += '</table>';
	d.innerHTML = body;
}

function getFirstSelected() {
	for (var i = 0; i < entries.length; i++) {
		if (top.fsbody.document.getElementById("line_" + entries[i].ID).classList.contains("selected")) {
			return i;
		}
	}
	return -1;
}

function unselectFile(id) {
	e = getEntry(id);
	top.fsbody.document.getElementById("line_" + id).style.classList.remove("selected");

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

function getPositionByID(id) {
	for (var i = 0; i < entries.length; i++) {
		if (entries[i].ID == id) {
			return i;
		}
	}
	return -1;
}

function isFileSelected(id) {
	return (top.fsbody.document.getElementById("line_" + id).classList.contains("selected"));
}

function unselectAllFiles() {
	for (var i = 0; i < entries.length; i++) {
		if ((elem = top.fsbody.document.getElementById("line_" + entries[i].ID))) {
			elem.classList.remove("selected");
		}
	}
	top.document.getElementsByName("fname")[0].value = "";
	top.DelBut(false);
}

function queryString(what, id, o) {
	if (!o) {
		o = top.fileSelect.data.order;
	}
	return top.fileSelect.options.formtarget + 'what=' + what + '&table=' + top.fileSelect.options.table + '&id=' + id + "&order=" + o + "&filter=" + top.fileSelect.data.currentType;
}

function fillIDs(asArray) {
	allIDs = [];
	allPaths = [];
	allTexts = [];
	allIsFolder = [];

	for (var i = 0; i < entries.length; i++) {
		if (isFileSelected(entries[i].ID)) {
			allIDs.push(entries[i].ID);
			allPaths.push(entries[i].path);
			allTexts.push(entries[i].text);
			allIsFolder.push(entries[i].isFolder);
		}
	}
	if (top.fileSelect.data.currentID !== "" && allIDs.indexOf(top.fileSelect.data.currentID) === -1) {
		allIDs.push(top.fileSelect.data.currentID);
	}
	if (top.fileSelect.data.currentPath !== "" && allPaths.indexOf(top.fileSelect.data.currentPath) === -1) {
		allPaths.push(top.fileSelect.data.currentPath);
		allTexts.push(we_makeTextFromPath(top.fileSelect.data.currentPath));
	}

	if (!asArray) {
		allIDs = allIDs.join(',');
		allPaths = allPaths.join(',');
		allTexts = allTexts.join(',');
		allIsFolder = allIsFolder.join(',');
		//keep old behaviour
		if (allIDs) {
			allIDs = "," + allIDs + ",";
			allPaths = "," + allPaths + ",";
			allTexts = "," + allTexts + ",";
			allIsFolder = "," + allIsFolder + ",";
		}
	}
}

function we_makeTextFromPath(path) {
	position = path.lastIndexOf("/");
	if (position > -1 && position < path.length) {
		return path.substring(position + 1);
	}
	return "";
}

function weonclick(e) {
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
		if ((window.shiftpressed === false) && (window.ctrlpressed === false)) {
			top.unselectAllFiles();
		}
	} else {
		top.unselectAllFiles();
	}
}

function elementSelected() {
	return top.document.getElementsByName("fname")[0].value !== "";
}

function press_ok_button() {
	if (elementSelected()) {
		top.exit_open();
	} else {
		top.exit_close();
	}
}

function DelBut(enable) {
	if (enable) {
		WE().layout.button.switch_button_state(document, "delete", "enabled");
		if (top.fileSelect.options.userCanEditCat) {
			WE().layout.button.switch_button_state(document, "btn_function_trash", "enabled");
			top.fileSelect.data.changeCatState = true;
		}
	} else {
		WE().layout.button.switch_button_state(document, "delete", "disabled");
		WE().layout.button.switch_button_state(document, "btn_function_trash", "disabled");
		top.fileSelect.data.changeCatState = false;
	}
}

function startFrameset() {
	top.document.getElementById('fspath').innerHTML = (top.fileSelect.data.startPath === '' ? '/' : top.fileSelect.data.startPath);
}

function RootDirButs(enable) {
	if (enable) {
		WE().layout.button.switch_button_state(document, "root_dir", "enabled");
		WE().layout.button.switch_button_state(document, "btn_fs_back", "enabled");
	} else {
		WE().layout.button.switch_button_state(document, "root_dir", "disabled");
		WE().layout.button.switch_button_state(document, "btn_fs_back", "disabled");
	}
	top.fileSelect.data.rootDirButsState = enable;
}
function NewFolderBut(enable) {
	if (enable) {
		WE().layout.button.switch_button_state(document, "btn_new_dir", "enabled");
	} else {
		WE().layout.button.switch_button_state(document, "btn_new_dir", "disabled");
	}
	top.fileSelect.data.makefolderState = enable;
}

function NewBut(enable) {
	if (enable) {
		if (top.fileSelect.options.userCanEditCat) {
			WE().layout.button.switch_button_state(document, "btn_new_dir", "enabled");
			WE().layout.button.switch_button_state(document, "btn_add_cat", "enabled");
		} else {
			WE().layout.button.switch_button_state(document, "btn_new_dir", "disabled");
			WE().layout.button.switch_button_state(document, "btn_add_cat", "disabled");
		}
	}
}

function NewFileBut(enable) {
	if (enable) {
		WE().layout.button.switch_button_state(document, "btn_add_file", "enabled");
	} else {
		WE().layout.button.switch_button_state(document, "btn_add_file", "disabled");
	}
	top.fileSelect.data.newFileState = enable;
}

function clearOptions() {
	var a = top.document.getElementById("lookin");
	while (a.options.length) {
		a.options.remove(0);
	}
}
function addOption(txt, id) {
	var a = top.document.getElementById("lookin");
	a.options[a.options.length] = new Option(txt, id);
	a.selectedIndex = (a.options.length > 0 ?
					a.options.length - 1 :
					0);

}
function selectIt() {
	var a = top.document.getElementById("lookin");
	a.selectedIndex = a.options.length - 1;
}

function setview(view) {
	top.fileSelect.options.view = view;
	var zoom = top.document.getElementsByName("zoom")[0];
	switch (view) {
		case 'list':
			zoom.value = 100;
			if (zoom.onchange) {
				zoom.onchange();
			}
			zoom.disabled = true;
			zoom.style.display = "none";
			break;
		case 'icons':
			zoom.disabled = false;
			zoom.style.display = "inline";
			break;
	}
	top.document.getElementById('list').style.display = (view === 'list' ? "none" : "table-cell");
	top.document.getElementById('icons').style.display = (view === 'icons' ? "none" : "table-cell");

	top.writeBody(top.fsbody.document.body);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);
	var i, ref;
	switch (args[0]) {
		case 'clearEntries':
			top.clearEntries();
			break;
		case 'addEntries':
			for (i = 0; i < args[1].length; i++) {
				top.addEntry.apply(this, args[1][i]);
			}
			break;
		case 'writeOptions':
			top.clearOptions();
			for (i = 0; i < args[1].length; i++) {
				top.addOption.apply(this, args[1][i]);
			}
			top.selectIt();
			break;
		case 'writeBody':
			if (top.fsbody.document.body) {
				top.writeBody(top.fsbody.document.body);
				top.selectFile(top.fileSelect.data.currentID);
			}
			break;
		case 'updateTreeEntry':
			ref = (top.opener.top.treeData ? top.opener.top : (top.opener.top.opener.top.treeData ? top.opener.top.opener.top : null));
			if (ref) {
				ref.treeData.updateEntry(args[1]);
			}
			break;
		case 'makeNewTreeEntry':
			ref = (top.opener.top.treeData ? top.opener.top : (top.opener.top.opener.top.treeData ? top.opener.top.opener.top : null));
			if (ref) {
				ref.treeData.makeNewEntry(args[1]);
			}
			if (top.fileSelect.options.canSelectDir) {
				top.document.getElementsByName("fname")[0].value = top.fileSelect.data.currentText;
			}
			break;
		case 'updateSelectData':
			var obj = args[1];
			for (i in obj) {
				top.fileSelect.data[i] = obj[i];
				if (obj === 'currentText') {
					top.document.getElementsByName("fname")[0].value = top.fileSelect.data.currentText;
				}
			}
			break;
		case 'setButtons':
			for (i = 0; i < args[1].length; i++) {
				switch (args[1][i][0]) {
					case 'NewFolderBut':
						NewFolderBut(args[1][i][1]);
						break;
					case 'NewFileBut':
						NewFileBut(args[1][i][1]);
						break;
					case 'RootDirButs':
						RootDirButs(args[1][i][1]);
						break;
					case 'DelBut':
						DelBut(args[1][i][1]);
				}
			}
			break;
		case 'setLookinDir':
			top.setDir(top.document.getElementById("lookin").value);
			break;
		case 'updateCatChooserButton':
			top.frames.fsvalues.document.we_form.elements.FolderID.value = args[1];
			top.frames.fsvalues.document.we_form.elements.FolderIDPath.value = args[2];
			break;
		case 'newCatSuccess':
			top.hot = 1; // this is hot for category edit!!

			if (top.fileSelect.data.currentID) {
				top.DelBut(false);
				top.showPref(top.fileSelect.data.currentID);
			}
			break;
		case 'unselectAllFiles':
			top.unselectAllFiles();
			break;
		case 'postRenameCat':
			top.hot = true; // this is hot for category edit!!
			if (top.fileSelect.data.currentID) {
				top.DelBut(true);
				top.showPref(top.fileSelect.data.currentID);
			}
			break;
		default:
			opener.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function selectorOnClick(event, id) {
	weonclick(event);
	tout = setTimeout(function () {
		if (!top.wasdblclick) {
			top.doClick(id, 0);
		} else {
			top.wasdblclick = false;
		}
	}, 300);
	return true;
}

function selectorOnDblClick(id) {
	top.wasdblclick = true;
	clearTimeout(tout);
	top.doClick(id, 1);
	return true;
}

function exit_open() {
	if (top.fileSelect.data.JSIDName) {
		opener.document.we_form.elements[top.fileSelect.data.JSIDName].value = top.fileSelect.data.currentID;
	}
	if (top.fileSelect.data.JSTextName) {
		opener.document.we_form.elements[top.fileSelect.data.JSTextName].value = top.fileSelect.data.currentID ? top.fileSelect.data.currentPath : "";

		if ((opener.parent !== undefined) && (opener.parent.frames.editHeader !== undefined)) {
			if (top.fileSelect.data.currentType != "") {
				switch (top.fileSelect.data.currentType) {
					case "noalias":
						setTabsCurPath = "@" + top.fileSelect.data.currentText;
						break;
					default:
						setTabsCurPath = top.fileSelect.data.currentPath;
				}
				if (getEntry(top.fileSelect.data.currentID).isFolder) {
					opener.parent.frames.editHeader.weTabs.setTitlePath("", setTabsCurPath);
				} else {
					opener.parent.frames.editHeader.weTabs.setTitlePath(setTabsCurPath);
				}
			}
		}
		if (opener.YAHOO !== undefined && opener.YAHOO.autocoml !== undefined) {
			var val = opener.document.we_form.elements[top.fileSelect.data.JSTextName].id
			opener.YAHOO.autocoml.selectorSetValid(val);
		}
	}
	if (top.fileSelect.data.JSCommand) {
		if (top.fileSelect.data.JSCommand.indexOf(".") > 0) {
			eval(top.fileSelect.data.JSCommand);
		} else {
			fillIDs(true);
			var tmp = top.fileSelect.data.JSCommand.split(',');
			tmp.splice(1, 0, top.fileSelect.data);
			opener.we_cmd.apply(opener, tmp);
		}
	}

	window.close();
}

window.focus();