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
'use strict';
var fileSelect = WE().util.getDynamicVar(document, 'loadVarSelectors', 'data-selector');
WE().util.loadConsts(document, "g_l.fileselector");
WE().util.loadConsts(document, "selectors");

var entries = [];
var clickCount = 0;
var mk = null;
top.metaKeys = {
	ctrl: false,
	shift: false,
	doubleClick: false,
	inputClick: false,
	doubleTout: null
};

function applyOnEnter(evt) {
	var _elemName = "target";
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
		ID: 0,
		icon: "",
		text: "/",
		isFolder: 1,
		path: "/"
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
		if (top.metaKeys.doubleClick) {
			setDir(id);
			window.setTimeout(function () {
				top.metaKeys.doubleClick = false;
			}, 400);
		}
	} else if (top.fileSelect.options.multiple) {
		if (top.metaKeys.shift) {
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
		} else if (!top.metaKeys.ctrl) {
			selectFile(id);
		} else if (isFileSelected(id)) {
			unselectFile(id);
		} else {
			selectFile(id);
		}
	} else {
		selectFile(id);

	}
	if (top.metaKeys.ctrl) {
		top.metaKeys.ctrl = 0;
	}
	if (top.metaKeys.shift) {
		top.metaKeys.shift = 0;
	}
}

function setDir(id) {
	var e = getEntry(id);
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
		var e = getEntry(id);

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

function addEntry(ID, text, isFolder, path, ct) {
	entries.push({
		ID: ID,
		text: text,
		isFolder: isFolder,
		path: path,
		contentType: ct
	});
}

function writeBody(d) {
	var body = '<table class="selector">';
	for (var i = 0; i < entries.length; i++) {
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
	var e = getEntry(id);
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
	var elem;
	for (var i = 0; i < entries.length; i++) {
		if ((elem = top.fsbody.document.getElementById("line_" + entries[i].ID))) {
			elem.classList.remove("selected");
		}
	}
	top.document.getElementsByName("fname")[0].value = "";
	top.delBut(false);
}

function queryString(what, id, o) {
	if (!o) {
		o = top.fileSelect.data.order;
	}
	return top.fileSelect.options.formtarget + '&what=' + what + '&table=' + top.fileSelect.options.table + '&id=' + id + "&order=" + o + "&filter=" + top.fileSelect.data.currentType;
}

function fillIDs() {
	top.fileSelect.data.allIDs = [];
	top.fileSelect.data.allPaths = [];
	top.fileSelect.data.allTexts = [];
	top.fileSelect.data.allIsFolder = [];

	for (var i = 0; i < entries.length; i++) {
		if (isFileSelected(entries[i].ID)) {
			top.fileSelect.data.allIDs.push(entries[i].ID);
			top.fileSelect.data.allPaths.push(entries[i].path);
			top.fileSelect.data.allTexts.push(entries[i].text);
			top.fileSelect.data.allIsFolder.push(entries[i].isFolder);
		}
	}
	if (top.fileSelect.data.currentID !== "" && top.fileSelect.data.allIDs.indexOf(top.fileSelect.data.currentID) === -1) {
		top.fileSelect.data.allIDs.push(top.fileSelect.data.currentID);
	}
	if (top.fileSelect.data.currentPath !== "" && top.fileSelect.data.allPaths.indexOf(top.fileSelect.data.currentPath) === -1) {
		top.fileSelect.data.allPaths.push(top.fileSelect.data.currentPath);
		top.fileSelect.data.allTexts.push(we_makeTextFromPath(top.fileSelect.data.currentPath));
	}
}

function we_makeTextFromPath(path) {
	var position = path.lastIndexOf("/");
	if (position > -1 && position < path.length) {
		return path.substring(position + 1);
	}
	return "";
}

function weonclick(e) {
	if (document.all) {
		if (e.ctrlKey || e.altKey) {
			top.metaKeys.ctrl = true;
		}
		if (e.shiftKey) {
			top.metaKeys.shift = true;
		}
	} else {
		if (e.altKey || e.metaKey || e.ctrlKey) {
			top.metaKeys.ctrl = true;
		}
		if (e.shiftKey) {
			top.metaKeys.shift = true;
		}
	}
	if (top.fileSelect.options.multiple) {
		if ((top.metaKeys.shift === false) && (top.metaKeys.ctrl === false)) {
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

function delBut(enable) {
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

function rootDirButs(enable) {
	if (enable) {
		WE().layout.button.switch_button_state(document, "root_dir", "enabled");
		WE().layout.button.switch_button_state(document, "btn_fs_back", "enabled");
	} else {
		WE().layout.button.switch_button_state(document, "root_dir", "disabled");
		WE().layout.button.switch_button_state(document, "btn_fs_back", "disabled");
	}
	top.fileSelect.data.rootDirButsState = enable;
}
function newFolderBut(enable) {
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

function newFileBut(enable) {
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
	a.options[a.options.length] = new window.Option(txt, id);
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
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var i, ref;
	switch (args[0]) {
		case 'clearEntries':
			top.clearEntries();
			break;
		case 'addEntries':
			for (i = 0; i < args[1].length; i++) {
				top.addEntry.apply(window, args[1][i]);
			}
			break;
		case 'writeOptions':
			top.clearOptions();
			for (i = 0; i < args[1].length; i++) {
				top.addOption.apply(window, args[1][i]);
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
						newFolderBut(args[1][i][1]);
						break;
					case 'NewFileBut':
						newFileBut(args[1][i][1]);
						break;
					case 'RootDirButs':
						rootDirButs(args[1][i][1]);
						break;
					case 'DelBut':
						delBut(args[1][i][1]);
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
			top.hot = true; // this is hot for category edit!!

			if (top.fileSelect.data.currentID) {
				top.delBut(false);
				top.showPref(top.fileSelect.data.currentID);
			}
			break;
		case 'unselectAllFiles':
			top.unselectAllFiles();
			break;
		case 'postRenameCat':
			top.hot = true; // this is hot for category edit!!
			if (top.fileSelect.data.currentID) {
				top.delBut(true);
				top.showPref(top.fileSelect.data.currentID);
			}
			break;
		case 'selector_insertFromUploader':
			var importedDoc = args[1];
			top.reloadDir();
			top.unselectAllFiles();
			top.addEntry(importedDoc.currentID, importedDoc.currentText, false, importedDoc.currentPath, importedDoc.currentType);
			top.doClick(importedDoc.currentID);
			window.setTimeout(top.selectFile, 200, importedDoc.currentID);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		default:
			window.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function selectorOnClick(event, id) {
	weonclick(event);
	top.metaKeys.doubleTout = setTimeout(function () {
		if (!top.metaKeys.doubleClick) {
			top.doClick(id, 0);
		} else {
			top.metaKeys.doubleClick = false;
		}
	}, 300);
	return true;
}

function selectorOnDblClick(id) {
	top.metaKeys.doubleClick = true;
	window.clearTimeout(top.metaKeys.doubleTout);
	top.doClick(id, 1);
	return true;
}

function exit_open() {
	var setTabsCurPath;
	var suggestID = '';
	if (top.fileSelect.data.JSIDName) {
		var elemID = window.opener.document.we_form.elements[top.fileSelect.data.JSIDName];
		elemID.value = top.fileSelect.data.currentID;
		suggestID = elemID.id.search('yuiAcResult') === 0 ? elemID.id.substr(11) : suggestID;
	}
	if (top.fileSelect.data.JSTextName) {
		var elemText = window.opener.document.we_form.elements[top.fileSelect.data.JSTextName];
		elemText.value = top.fileSelect.data.currentID ? top.fileSelect.data.currentPath : "";
		suggestID = suggestID ? suggestID : (elemText.id.search('yuiAcInput') === 0 ? elemText.id.substr(10) : suggestID);

		if ((opener.parent !== undefined) && (opener.parent.frames.editHeader !== undefined)) {
			if (top.fileSelect.data.currentType !== "") {
				switch (top.fileSelect.data.currentType) {
					case "noalias":
						setTabsCurPath = "@" + top.fileSelect.data.currentText;
						break;
					default:
						setTabsCurPath = top.fileSelect.data.currentPath;
				}
				if (getEntry(top.fileSelect.data.currentID).isFolder) {
					window.opener.parent.frames.editHeader.weTabs.setTitlePath("", setTabsCurPath);
				} else {
					window.opener.parent.frames.editHeader.weTabs.setTitlePath(setTabsCurPath);
				}
			}
		}
		WE().layout.weSuggest.checkRequired(opener, opener.document.we_form.elements[top.fileSelect.data.JSTextName].id);
	}

	// if selector is used in combination with suggestor and we know sugestorID we call sugestors postporcess
	if(suggestID){
		window.opener.we_cmd('we_suggest_postprocessSelection', top.fileSelect.data, suggestID);
	}

	// if not in combination with suggestor or if using custom selector callback: no onSelect is called automatically
	if (top.fileSelect.data.JSCommand) {
		fillIDs();
		if (top.fileSelect.data.JSCommand.indexOf(".") > 0) {
			eval(top.fileSelect.data.JSCommand);
			WE().t_e("old JS Command found", top.fileSelect.data.JSCommand);
		} else {
			var tmp = top.fileSelect.data.JSCommand.split(',');
			tmp.splice(1, 0, top.fileSelect.data);
			window.opener.we_cmd.apply(opener, tmp);
		}
	}

	window.close();
}

window.focus();