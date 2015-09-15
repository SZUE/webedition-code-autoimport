/**
 * webEdition CMS
 *
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

var regular_logout = false;
var widthBeforeDeleteMode = 0;
var widthBeforeDeleteModeSidebar = 0;
var cockpitFrame;
var we_mediaReferences = {};


/**
 * setting is built like the unix file system privileges with the 3 options
 * see notices, see warnings, see errors
 *
 * 1 => see Notices
 * 2 => see Warnings
 * 4 => see Errors
 *
 * @param message string
 * @param prio integer one of the values 1,2,4
 * @param win object reference to the calling window
 */
function showMessage(message, prio, win) {
	if (!win) {
		win = this.window;
	}
	// default is error, to avoid missing messages
	prio = prio ? prio : WE_MESSAGE_ERROR;

	// always show in console !
	messageConsole.addMessage(prio, message);

	if (prio & messageSettings) { // show it, if you should

		// the used vars are in file JS_DIR . "weJsStrings.php";
		switch (prio) {
			// Notice
			case WE_MESSAGE_NOTICE:
				win.alert(message_reporting.notice + ":\n" + message);
				break;

				// Warning
			case WE_MESSAGE_WARNING:
				win.alert(message_reporting.warning + ":\n" + message);
				break;

				// Error
			case WE_MESSAGE_ERROR:
				win.alert(message_reporting.error + ":\n" + message);
				break;
		}
	}
}

function weDummy(o) { // AJAX Requests
	// dummy
}


// new functions
function doClickDirect(id, ct, table, fenster) {
	if (!fenster) {
		fenster = window;
	}
	//  the actual position is the top-window, maybe the first window was closed
	if (!fenster.top.opener || fenster.top.opener.isLoginScreen || fenster.top.opener.closed) {
		top.weEditorFrameController.openDocument(table, id, ct);

	} else {
		//  If a include-file is edited and another link is chosen, it will appear on the main window. And the pop-up will be closed.
		top.we_showMessage(g_l.open_link_in_SEEM_edit_include, WE_MESSAGE_WARNING, window);
		top.opener.top.doClickDirect(id, ct, table, top.opener);
		// clean session
		// get the EditorFrame - this is important due to edit_include_mode!!!!
		var _ActiveEditor = top.weEditorFrameController.getActiveEditorFrame();
		if (_ActiveEditor) {
			trans = _ActiveEditor.getEditorTransaction();
			if (trans) {
				top.we_cmd('users_unlock', _ActiveEditor.getEditorDocumentId(), userID, _ActiveEditor.getEditorEditorTable(), trans);
			}
		}
		top.close();
	}
}

function setTreeArrow(direction) {
	try {
		var arrImg = self.document.getElementById("arrowImg");
		if (direction === "right") {
			arrImg.classList.remove("fa-caret-left");
			self.document.getElementById("incBaum").style.backgroundColor = "gray";
			self.document.getElementById("decBaum").style.backgroundColor = "gray";
		} else {
			arrImg.classList.remove("fa-caret-right");
			self.document.getElementById("incBaum").style.backgroundColor = "";
			self.document.getElementById("decBaum").style.backgroundColor = "";
		}
		arrImg.classList.add("fa-caret-" + direction);
	} catch (e) {
		// Nothing
	}
}

function doClickWithParameters(id, ct, table, parameters) {
	top.weEditorFrameController.openDocument(table, id, ct, '', '', '', '', '', parameters);

}

function doExtClick(url) {
	// split url in url and parameters !!!
	var parameters = "";
	if ((_position = url.indexOf("?")) != -1) {
		parameters = url.substring(_position);
		url = url.substring(0, _position);
	}

	top.weEditorFrameController.openDocument('', '', '', '', '', url, '', '', parameters);
}

function weSetCookie(name, value, expires, path, domain) {
	var doc = self.document;
	doc.cookie = name + "=" + encodeURI(value) +
					((expires === null) ? "" : "; expires=" + expires.toGMTString()) +
					((path === null) ? "" : "; path=" + path) +
					((domain === null) ? "" : "; domain=" + domain);
}

function treeResized() {
	var treeWidth = getTreeWidth();
	if (treeWidth <= size.tree.hidden) {
		setTreeArrow("right");
	} else {
		setTreeArrow("left");
		storeTreeWidth(treeWidth);
	}
}

var oldTreeWidth = size.tree.defaultWidth;
function toggleTree() {
	var tfd = self.document.getElementById("treeFrameDiv");
	var w = top.getTreeWidth();

	if (tfd.style.display == "none") {
		oldTreeWidth = (oldTreeWidth < size.tree.min ? size.tree.defaultWidth : oldTreeWidth);
		setTreeWidth(oldTreeWidth);
		tfd.style.display = "block";
		setTreeArrow("left");
		storeTreeWidth(oldTreeWidth);
	} else {
		tfd.style.display = "none";
		oldTreeWidth = w;
		setTreeWidth(size.tree.hidden);
		setTreeArrow("right");
	}
}

function treeOut() {
	if (getTreeWidth() <= size.tree.min) {
		toggleTree();
	}
}

function getTreeWidth() {
	var w = self.document.getElementById("bframeDiv").style.width;
	return w.substr(0, w.length - 2);
}

function incTree() {
	var w = parseInt(getTreeWidth());
	if ((w > size.tree.min) && (w < size.tree.max)) {
		w += size.tree.step;
		setTreeWidth(w);
	}
	if (w >= size.tree.max) {
		w = size.tree.max;
		setTreeWidth(w);
		self.document.getElementById("incBaum").style.backgroundColor = "grey";
	} else {

	}
}

function decTree() {
	var w = parseInt(getTreeWidth());
	w -= size.tree.step;
	if (w > size.tree.min) {
		setTreeWidth(w);
		self.document.getElementById("incBaum").style.backgroundColor = "";
	}
	if (w <= size.tree.min && ((w + size.tree.step) >= size.tree.min)) {
		toggleTree();
	}
}


function getSidebarWidth() {
	var obj = self.document.getElementById("sidebarDiv");
	if (obj === undefined || obj === null) {
		return 0;
	}
	var w = obj.style.left;
	return w.substr(0, w.length - 2);
}

function setSidebarWidth() {
	var obj = self.document.getElementById("sidebarDiv");
	if (obj !== undefined && obj !== null) {
		obj.style.left = top.w + "px";
	}
}

function setTreeWidth(w) {
	self.document.getElementById("bframeDiv").style.width = w + "px";
	self.document.getElementById("bm_content_frameDiv").style.left = w + "px";
	if (w > size.tree.hidden) {
		storeTreeWidth(w);
	}
}

function storeTreeWidth(w) {
	var ablauf = new Date();
	var newTime = ablauf.getTime() + 30758400000;
	ablauf.setTime(newTime);
	weSetCookie("treewidth_main", w, ablauf, "/");
}

function focusise() {
	setTimeout(function () {
		self.makefocus.focus();
		self.makefocus = null;
	}, 200);
}

function we_repl(target, url) {
	if (target) {
		try {
			// use 2 loadframes to avoid missing cmds
			if (target.name === "load" || target.name === "load2") {
				if (top.lastUsedLoadFrame === target.name) {
					target = (target.name === "load" ?
									self.load2 :
									self.load);
				}
				top.lastUsedLoadFrame = target.name;
			}
		} catch (e) {
			// Nothing
		}
		if (target.location === undefined) {
			target.src = url;
		} else {
			target.location.replace(url);
		}
	}
}

function submit_we_form(formlocation, target, url) {
	try {
		if (formlocation) {
			if (formlocation.we_submitForm) {
				formlocation.we_submitForm(target.name, url);
				return true;
			}
			if (formlocation.contentWindow.we_submitForm) {
				formlocation.contentWindow.we_submitForm(target.name, url);
				return true;
			}
		}
	} catch (e) {
	}
	return false;
}

function we_sbmtFrm(target, url, source) {
	if (source === undefined) {
		source = top.weEditorFrameController.getVisibleEditorFrame();
	}
	return submit_we_form(source, target, url);

}

function we_setEditorWasLoaded(flag) {
	// imi: console.log("we_setEditorWasLoaded: " + flag);
	//flag = true; //uncomment to keep first weEditorWasLoaded=true for the rest of the session
	self.weEditorWasLoaded = flag;
}

// use this to submit a cmd with post (if you have much data, which is to long for the url);  // not testet very much!!!
function doPostCmd(cmds, target) {
	var doc = self.postframe.document;
	if (doc.forms[0]) {
		doc.body.removeChild(doc.forms[0]);
	}
	var formElement = doc.createElement("FORM");
	formElement.action = '/webEdition/we_cmd.php';
	formElement.method = "post";
	formElement.target = target;

	var hiddens = [];
	for (var i = 0; i < cmds.length; i++) {
		var hid = doc.createElement("INPUT");
		hid.name = "we_cmd[" + i + "]";
		hid.value = cmds[i];
		formElement.appendChild(hid);
	}
	doc.body.appendChild(formElement);
	formElement.submit();
}

function doSave(url, trans, cmd) {
	_EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction(trans);
	// _EditorFrame.setEditorIsHot(false);
	if (_EditorFrame.getEditorAutoRebuild())
		url += "&we_cmd[8]=1";
	if (!we_sbmtFrm(self.load, url)) {
		url += "&we_transaction=" + trans;
		we_repl(self.load, url, cmd);
	}
}

function doPublish(url, trans, cmd) {
	if (!we_sbmtFrm(self.load, url)) {
		url += "&we_transaction=" + trans;
		we_repl(self.load, url, cmd);
	}
}

function openWindow(url, ref, x, y, w, h, scrollbars, menues) {
	new jsWindow(url, ref, x, y, w, h, true, scrollbars, menues);
}


function openBrowser(url) {
	if (!url) {
		url = "/";
	}
	try {
		browserwind = window.open("/webEdition/openBrowser.php?url=" + encodeURI(url), "browser", "menubar=yes,resizable=yes,scrollbars=yes,location=yes,status=yes,toolbar=yes");
	} catch (e) {
		top.we_showMessage(g_l.browser_crashed, WE_MESSAGE_ERROR, window);
	}
}

function start() {
	self.Tree = self;
	self.Vtabs = self;
	self.TreeInfo = self;
	if (tables.table_to_load) {
		we_cmd("load", tables.table_to_load);
	}
}

function hasPermDelete(eTable, isFolder) {
	if (eTable === "") {
		return false;
	}
	if (wePerms.ADMINISTRATOR) {
		return true;
	}
	switch (eTable) {
		case tables.FILE_TABLE:
			return (isFolder ? wePerms.DELETE_DOC_FOLDER : wePerms.DELETE_DOCUMENT);
		case tables.TEMPLATES_TABLE:
			return (isFolder ? wePerms.DELETE_TEMP_FOLDER : wePerms.DELETE_TEMPLATE);
		case tables.OBJECT_FILES_TABLE:
			return (isFolder ? wePerms.DELETE_OBJECTFILE : wePerms.DELETE_OBJECTFILE);
		case tables.OBJECT_TABLE:
			return (isFolder ? false : wePerms.DELETE_OBJECT);
		default:
			return false;
	}
}

function toggleBusy(w) { //=> removed since no header animation anymore
	return;/*
	 if (w == busy || firstLoad == false) {
	 return;
	 }
	 if (self.header) {
	 if (self.header.toggleBusy) {
	 busy = w;
	 self.header.toggleBusy(w);
	 return;
	 }
	 }
	 setTimeout("toggleBusy(" + w + ");", 300);*/
}


function doUnloadSEEM(whichWindow) {
	// unlock all open documents
	var _usedEditors = top.weEditorFrameController.getEditorsInUse();

	var docIds = "";
	var docTables = "";

	for (var frameId in _usedEditors) {
		if (_usedEditors[frameId].EditorType != "cockpit") {
			docIds += _usedEditors[frameId].getEditorDocumentId() + ",";
			docTables += _usedEditors[frameId].getEditorEditorTable() + ",";
		}
	}

	if (docIds) {
		top.we_cmd('users_unlock', docIds, userID, docTables);
		if (top.opener) {
			top.opener.focus();

		}
	}
	//  close the SEEM-edit-include when exists
	if (top.edit_include) {
		top.edit_include.close();
	}
	try {
		if (jsWindow_count) {
			for (i = 0; i < jsWindow_count; i++) {
				eval("jsWindow" + i + "Object.close()");
			}
		}
		if (browserwind) {
			browserwind.close();
		}
	} catch (e) {

	}

	//  only when no SEEM-edit-include window is closed

	if (whichWindow != "include") {
		if (opener) {
			opener.location.replace('/webEdition/we_loggingOut.php');
		}
	}
}

function doUnloadNormal(whichWindow) {
	var tinyDialog;
	if (!regular_logout) {

		if (window.tinyMceDialog !== undefined && window.tinyMceDialog !== null) {
			tinyDialog = tinyMceDialog;
			try {
				tinyDialog.close();
			} catch (err) {
			}
		}

		if (tinyMceSecondaryDialog !== undefined && tinyMceSecondaryDialog !== null) {
			tinyDialog = tinyMceSecondaryDialog;
			try {
				tinyDialog.close();
			} catch (err) {
			}
		}

		try {
			if (jsWindow_count) {
				for (i = 0; i < jsWindow_count; i++) {
					eval("jsWindow" + i + "Object.close()");
				}
			}
			if (browserwind) {
				browserwind.close();
			}
		} catch (e) {
		}
		if (whichWindow != "include") { 	// only when no SEEM-edit-include window is closed
			// FIXME: closing-actions for SEEM
			var logoutpopup;
			if (top.opener) {
				if (specialUnload) {
					top.opener.location.replace('/webEdition/we_loggingOut.php?isopener=1');
					top.opener.focus();
				} else {
					top.opener.history.back();
					logoutpopup = window.open('/webEdition/we_loggingOut.php?isopener=0', "webEdition", "width=350,height=70,toolbar=no,menubar=no,directories=no,location=no,resizable=no,status=no,scrollbars=no,top=300,left=500");
					if (logoutpopup) {
						logoutpopup.focus();
					}
				}
			} else {
				logoutpopup = window.open('/webEdition/we_loggingOut.php?isopener=0', "webEdition", "width=350,height=70,toolbar=no,menubar=no,directories=no,location=no,resizable=no,status=no,scrollbars=no,top=300,left=500");
				if (logoutpopup) {
					logoutpopup.focus();
				}
			}
		}
	}

}


function doUnload(whichWindow) { // triggered when webEdition-window is closed
	if (SEEMODE) {
		doUnloadSEEM(whichWindow);
	} else {
		doUnloadNormal(whichWindow);
	}
}

function we_openMediaReference(id) {
	id = id ? id : 0;

	if (window.we_mediaReferences && window.we_mediaReferences['id_' + id]) {
		var ref = window.we_mediaReferences['id_' + id];
		switch (ref.type) {
			case 'module':
				top.we_cmd(ref.mod + '_edit_ifthere', ref.id);
				break;
			case 'cat':
				top.we_cmd('editCat', ref.id);
				break;
			default:
				if (ref.isTempPossible && ref.referencedIn == 'main' && ref.isModified) {
					top.we_showMessage('Der Link wurde bei einer unveröffentlichten Änderung entfernt: Er existiert nur noch in der veröffentlichten Version!', WE_MESSAGE_ERROR, window);
				} else {
					top.weEditorFrameController.openDocument(ref.table, ref.id, ref.ct);
				}
		}
	}
}

function we_cmd_base(args, url) {
	switch (args[0]) {
		case "exit_modules":
			if (jsWindow_count) {
				for (i = 0; i < jsWindow_count; i++) {
					eval("if(jsWindow" + i + "Object.ref=='edit_module') jsWindow" + i + "Object.close()");
				}
			}
			break;
		case "openFirstStepsWizardMasterTemplate":
		case "openFirstStepsWizardDetailTemplates":
			new jsWindow(url, "we_firststepswizard", -1, -1, 1024, 768, true, true, true);
			break;
		case "openUnpublishedObjects":
			we_cmd("tool_weSearch_edit", "", "", 7, 3);
			break;
		case "openUnpublishedPages":
			we_cmd("tool_weSearch_edit", "", "", 4, 3);
			break;
		case "we_selector_category":
			new jsWindow(url, "we_cateditor", -1, -1, size.catSelect.width, size.catSelect.height, true, true, true, true);
			break;
		case "openSidebar":
			top.weSidebar.open("default");
			break;
		case "loadSidebarDocument":
			top.weSidebarContent.location.href = url;
			break;
		case "versions_preview":
			new jsWindow(url, "version_preview", -1, -1, 1000, 750, true, false, true, false);
			break;
		case "versions_wizard":
			new jsWindow(url, "versions_wizard", -1, -1, 600, 620, true, false, true);
			break;
		case "versioning_log":
			new jsWindow(url, "versioning_log", -1, -1, 600, 500, true, false, true);
			break;

		case "delete_single_document_question":
			var cType = top.weEditorFrameController.getActiveEditorFrame().getEditorContentType();
			var eTable = top.weEditorFrameController.getActiveEditorFrame().getEditorEditorTable();
			var path = top.weEditorFrameController.getActiveEditorFrame().getEditorDocumentPath();

			toggleBusy(1);
			if (weEditorFrameController.getActiveDocumentReference()) {
				if (!hasPermDelete(eTable, (cType === "folder"))) {
					top.we_showMessage(g_l.no_perms_action, WE_MESSAGE_ERROR, window);
				} else if (window.confirm(g_l.delete_single_confirm_delete + "\n" + path)) {
					url2 = url.replace(/we_cmd\[0\]=delete_single_document_question/g, "we_cmd[0]=delete_single_document");
					submit_we_form(top.weEditorFrameController.getActiveDocumentReference().frames.editFooter, self.load, url2 + "&we_cmd[2]=" + top.weEditorFrameController.getActiveEditorFrame().getEditorEditorTable());
				}
			} else {
				top.we_showMessage(g_l.no_document_opened, WE_MESSAGE_ERROR, window);
			}
			break;
		case "delete_single_document":
			var cType = top.weEditorFrameController.getActiveEditorFrame().getEditorContentType();
			var eTable = top.weEditorFrameController.getActiveEditorFrame().getEditorEditorTable();

			toggleBusy(1);
			if (weEditorFrameController.getActiveDocumentReference()) {
				if (!hasPermDelete(eTable, (cType === "folder"))) {
					top.we_showMessage(g_l.no_perms_action, WE_MESSAGE_ERROR, window);
				} else {
					submit_we_form(top.weEditorFrameController.getActiveDocumentReference().editFooter, self.load, url + "&we_cmd[2]=" + top.weEditorFrameController.getActiveEditorFrame().getEditorEditorTable());
				}
			} else {
				top.we_showMessage(g_l.no_document_opened, WE_MESSAGE_ERROR, window);
			}
			break;
		case "do_delete":
			toggleBusy(1);
			submit_we_form(self.treeheader, self.load, url);
			break;
		case "move_single_document":
			toggleBusy(1);
			submit_we_form(top.weEditorFrameController.getActiveDocumentReference().editFooter, self.load, url);
			break;
		case "do_move":
			toggleBusy(1);
			submit_we_form(self.treeheader, self.load, url);
			break;
		case "do_addToCollection":
			toggleBusy(1);
			submit_we_form(self.treeheader, self.load, url);
			break;
		case "change_passwd":
			new jsWindow(url, "we_change_passwd", -1, -1, 250, 220, true, false, true, false);
			break;
		case "update":
			new jsWindow("/webEdition/liveUpdate/liveUpdate.php?active=update", "we_update_" + sess_id, -1, -1, 600, 500, true, true, true);
			break;
		case "upgrade":
			new jsWindow("/webEdition/liveUpdate/liveUpdate.php?active=upgrade", "we_update_" + sess_id, -1, -1, 600, 500, true, true, true);
			break;
		case "languageinstallation":
			new jsWindow("/webEdition/liveUpdate/liveUpdate.php?active=languages", "we_update_" + sess_id, -1, -1, 600, 500, true, true, true);
			break;
		case "del":
			we_cmd('delete', 1, args[2]);
			treeData.setstate(treeData.tree_states.select);
			top.treeData.unselectnode();
			top.drawTree();
			break;
		case "mv":
			we_cmd('move', 1, args[2]);
			treeData.setstate(treeData.tree_states.selectitem);
			top.treeData.unselectnode();
			top.drawTree();
			break;//add_to_collection
		case "tocollection":
			we_cmd('addToCollection', 1, args[2]);
			treeData.setstate(treeData.tree_states.select);
			top.treeData.unselectnode();
			top.drawTree();
			break;
		case "changeLanguageRecursive":
		case "changeTriggerIDRecursive":
			we_repl(self.load, url, args[0]);
			break;
		case "logout":
			we_repl(self.load, url, args[0]);
			break;
		case "dologout":
			// before the command 'logout' is executed, ask if unsaved changes should be saved
			if (top.weEditorFrameController.doLogoutMultiEditor()) {
				regular_logout = true;
				we_cmd('logout');
			}
			break;
		case "exit_multi_doc_question":
			new jsWindow(url, "exit_multi_doc_question", -1, -1, 500, 300, true, false, true);
			break;
		case "loadFolder":
		case "closeFolder":
			we_repl(self.load, url, args[0]);
			break;
		case "reload_editfooter":
			we_repl(top.weEditorFrameController.getActiveDocumentReference().frames.editFooter, url, args[0]);
			break;
		case "rebuild":
			new jsWindow(url, "rebuild", -1, 0, 609, 645, true, false, true);
			break;
		case "openPreferences":
			new jsWindow(url, "preferences", -1, -1, 540, 670, true, true, true, true);
			break;
		case "editCat":
			we_cmd("we_selector_category", 0, tables.CATEGORY_TABLE, "", "", "", "", "", 1);
			break;
		case "editThumbs":
			new jsWindow(url, "thumbnails", -1, -1, 500, 550, true, true, true);
			break;
		case "editMetadataFields":
			new jsWindow(url, "metadatafields", -1, -1, 500, 550, true, true, true);
			break;
		case "doctypes":
			new jsWindow(url, "doctypes", -1, -1, 800, 670, true, true, true);
			break;
		case "info":
			new jsWindow(url, "info", -1, -1, 432, 360, true, false, true);
			break;
		case "webEdition_online":
			new jsWindow("http://www.webedition.org/", "webEditionOnline", -1, -1, 960, 700, true, true, true, true);
			break;
		case "snippet_shop":
			alert("Es gibt noch keine URL für die Snippets Seite");
			break;
		case "help_modules":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo) {
						break;
					}
				}
				wind.focus();
			}
			url = "http://help.webedition.org/index.php?language=" + helpLang;
			new jsWindow(url, "help", -1, -1, 800, 600, true, false, true, true);
			break;
		case "info_modules":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo) {
						break;
					}
				}
				wind.focus();
			}
			url = "/webEdition/we_cmd.php?we_cmd[0]=info";
			new jsWindow(url, "info", -1, -1, 432, 350, true, false, true);
			break;
		case "help_tools":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='tool_window' || jsWindow" + k + "Object.ref=='tool_window_navigation' || jsWindow" + k + "Object.ref=='tool_window_weSearch'){ fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo) {
						break;
					}
				}
				wind.focus();
			}
			url = "http://help.webedition.org/index.php?language=" + helpLang;
			new jsWindow(url, "help", -1, -1, 800, 600, true, false, true, true);
			break;
		case "info_tools":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='tool_window' || jsWindow" + k + "Object.ref=='tool_window_navigation' || jsWindow" + k + "Object.ref=='tool_window_weSearch'){ fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo) {
						break;
					}
				}
				wind.focus();
			}
			url = "/webEdition/we_cmd.php?we_cmd[0]=info";
			new jsWindow(url, "info", -1, -1, 432, 350, true, false, true);
			break;
		case "help":
			url = "http://help.webedition.org/index.php?language=" + helpLang;
			new jsWindow(url, "help", -1, -1, 720, 600, true, false, true, true);
			break;
		case "help_forum":
			new jsWindow("http://forum.webedition.org", "help_forum", -1, -1, 960, 700, true, true, true, true);
			break;
		case "help_bugtracker":
			new jsWindow("http://qa.webedition.org/tracker/", "help_bugtracker", -1, -1, 960, 700, true, true, true, true);
			break;
		case "help_changelog":
			new jsWindow("http://www.webedition.org/de/webedition-cms/versionshistorie/webedition-6/", "help_changelog", -1, -1, 960, 700, true, true, true, true);
			break;
		case "we_customer_selector":
		case "we_selector_file":
			new jsWindow(url, "we_fileselector", -1, -1, size.windowSelect.width, size.windowSelect.height, true, true, true, true);
			break;
		case "we_selector_directory":
			new jsWindow(url, "we_fileselector", -1, -1, size.windowDirSelect.width, size.windowDirSelect.height, true, true, true, true);
			break;
		case "we_selector_image":
		case "we_selector_document":
			new jsWindow(url, "we_fileselector", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
			break;
		case "setTab":
			if (self.Vtabs && self.Vtabs.setTab && (window.treeData !== undefined)) {
				self.Vtabs.setTab(args[1]);
				treeData.table = args[1];
			} else {
				setTimeout('we_cmd("setTab","' + args[1] + '")', 500);
			}
			break;
		case "showLoadInfo":
			we_repl(self.Tree, url, args[0]);
			break;
		case "update_image":
		case "update_file":
		case "copyDocument":
		case "insert_entry_at_list":
		case "delete_list":
		case "down_entry_at_list":
		case "up_entry_at_list":
		case "down_link_at_list":
		case "up_link_at_list":
		case "add_entry_to_list":
		case "add_link_to_linklist":
		case "change_link":
		case "change_linklist":
		case "delete_linklist":
		case "insert_link_at_linklist":
		case "change_doc_type":
		case "doctype_changed":
		case "remove_image":
		case "delete_link":
		case "delete_cat":
		case "add_cat":
		case "delete_all_cats":
		case "schedule_add":
		case "schedule_del":
		case "schedule_add_schedcat":
		case "schedule_delete_all_schedcats":
		case "schedule_delete_schedcat":
		case "template_changed":
		case "add_navi":
		case "delete_navi":
		case "delete_all_navi":
			// set Editor hot
			_EditorFrame = top.weEditorFrameController.getActiveEditorFrame();
			_EditorFrame.setEditorIsHot(true);
			//no break;
		case "reload_editpage":
		case "wrap_on_off":
		case "restore_defaults":
		case "do_add_thumbnails":
		case "del_thumb":
		case "resizeImage":
		case "rotateImage":
		case "doImage_convertGIF":
		case "doImage_convertPNG":
		case "doImage_convertJPEG":
		case "doImage_crop":
		case "revert_published":

			// get editor root frame of active tab
			var _currentEditorRootFrame = top.weEditorFrameController.getActiveDocumentReference();
			// get visible frame for displaying editor page
			var _visibleEditorFrame = top.weEditorFrameController.getVisibleEditorFrame();
			// if cmd equals "reload_editpage" and there are parameters, attach them to the url
			if (args[0] === "reload_editpage" && _currentEditorRootFrame.parameters) {
				url += _currentEditorRootFrame.parameters;
			}

			// attach necessary parameters if available
			if (args[0] === "reload_editpage" && args[1]) {
				url += '#f' + args[1];
			} else if (args[0] === "remove_image" && args[2]) {
				url += '#f' + args[2];
			}

			// focus visible editor frame
			if (_visibleEditorFrame) {
				_visibleEditorFrame.focus();
			}

			if (_currentEditorRootFrame) {
				if (!we_sbmtFrm(_visibleEditorFrame, url, _visibleEditorFrame)) {
					if (args[0] !== "update_image") {
						// add we_transaction, if not set
						if (!args[2]) {
							args[2] = top.weEditorFrameController.getActiveEditorFrame().getEditorTransaction();
						}
						url += "&we_transaction=" + args[2];
					}
					we_repl(_visibleEditorFrame, url, args[0]);
				}
			}

			break;
		case "edit_document_with_parameters":
		case "edit_document":
			toggleBusy(1);
			try {
				if ((window.treeData !== undefined) && treeData) {
					treeData.unselectnode();
					if (args[1]) {
						treeData.selection_table = args[1];
					}
					if (args[2]) {
						treeData.selection = args[2];
					}
					if (treeData.selection_table === treeData.table) {
						treeData.selectnode(treeData.selection);
					}
				}
			} catch (e) {
			}

			if ((nextWindow = top.weEditorFrameController.getFreeWindow())) {
				_nextContent = nextWindow.getDocumentReference();
				// activate tab and set state to loading
				top.weMultiTabs.addTab(nextWindow.getFrameId(), nextWindow.getFrameId(), nextWindow.getFrameId());
				// use Editor Frame
				nextWindow.initEditorFrameData(
								{
									"EditorType": "model",
									"EditorEditorTable": args[1],
									"EditorDocumentId": args[2],
									"EditorContentType": args[3]
								}
				);
				// set Window Active and show it
				top.weEditorFrameController.setActiveEditorFrame(nextWindow.FrameId);
				top.weEditorFrameController.toggleFrames();
				if (_nextContent.frames && _nextContent.frames[1]) {
					if (!we_sbmtFrm(_nextContent, url)) {
						we_repl(_nextContent, url + "&frameId=" + nextWindow.getFrameId());
					}
				} else {
					we_repl(_nextContent, url + "&frameId=" + nextWindow.getFrameId());
				}
			} else {
				alert(g_l.no_editor_left);
			}
			break;
		case "open_extern_document":
		case "new_document":
			if ((nextWindow = top.weEditorFrameController.getFreeWindow())) {
				_nextContent = nextWindow.getDocumentReference();
				// activate tab and set it status loading ...
				top.weMultiTabs.addTab(nextWindow.getFrameId(), nextWindow.getFrameId(), nextWindow.getFrameId());
				nextWindow.updateEditorTab();
				// set Window Active and show it
				top.weEditorFrameController.setActiveEditorFrame(nextWindow.getFrameId());
				top.weEditorFrameController.toggleFrames();
				// load new document editor
				we_repl(_nextContent, url + "&frameId=" + nextWindow.getFrameId());
			} else {
				alert(g_l.no_editor_left);
			}
			break;
		case "close_document":
			if (args[1]) { // close special tab
				top.weEditorFrameController.closeDocument(args[1]);
			} else if ((_currentEditor = top.weEditorFrameController.getActiveEditorFrame())) {
				// close active tab
				top.weEditorFrameController.closeDocument(_currentEditor.getFrameId());
			}
			break;
		case "close_all_documents":
			top.weEditorFrameController.closeAllDocuments();
			break;
		case "close_all_but_active_document":

			activeId = null;
			if (args[1]) {
				activeId = args[1];
			}
			top.weEditorFrameController.closeAllButActiveDocument(activeId);
			break;
		case "open_url_in_editor":
			we_repl(self.load, url, args[0]);
			break;
		case "publish":
		case "unpublish":
			toggleBusy(1);
			doPublish(url, args[1], args[0]);
			break;
		case "save_document":
			var _EditorFrame = top.weEditorFrameController.getActiveEditorFrame();
			if (_EditorFrame && _EditorFrame.getEditorFrameWindow().frames && _EditorFrame.getEditorFrameWindow().frames[1]) {
				_EditorFrame.getEditorFrameWindow().frames[1].focus();
			}

			toggleBusy(1);
			if (!args[1]) {
				args[1] = _EditorFrame.getEditorTransaction();
			}

			doSave(url, args[1], args[0]);
			break;
		case "we_selector_delete":
			new jsWindow(url, "we_del_selector", -1, -1, size.windowDelSelect.width, size.windowDelSelect.height, true, true, true, true);
			break;
		case "browse":
			openBrowser();
			break;
		case "home":
			if (top.treeData) {
				top.treeData.unselectnode();
			}
			top.weEditorFrameController.openDocument('', '', '', 'open_cockpit');
			break;
		case "browse_server":
			new jsWindow(url, "browse_server", -1, -1, 840, 400, true, false, true);
			break;
		case "make_backup":
			new jsWindow(url, "export_backup", -1, -1, 680, 600, true, true, true);
			break;
		case "recover_backup":
			new jsWindow(url, "recover_backup", -1, -1, 680, 600, true, true, true);
			break;
		case "import_docs":
			new jsWindow(url, "import_docs", -1, -1, 480, 390, true, false, true);
			break;
		case "import":
			new jsWindow(url, "import", -1, -1, 600, 620, true, false, true);
			break;
		case "import_files":
			new jsWindow(url, "import_files", -1, -1, 600, 620, true, false, true);
			break;
		case "export":
			new jsWindow(url, "export", -1, -1, 600, 540, true, false, true);
			break;
		case "copyWeDocumentCustomerFilter":
			new jsWindow(url, "copyWeDocumentCustomerFilter", -1, -1, 400, 115, true, true, true);
			break;
		case "copyFolder":
			new jsWindow(url, "copyfolder", -1, -1, 550, 320, true, true, true);
			break;
		case "del_frag":
			new jsWindow("/webEdition/delFrag.php?currentID=" + args[1], "we_del", -1, -1, 600, 130, true, true, true);
			break;
		case "open_wysiwyg_window":
			if (top.weEditorFrameController.getActiveDocumentReference()) {
				top.weEditorFrameController.getActiveDocumentReference().openedWithWE = false;
			}
			var wyw = args[2];
			wyw = Math.max((wyw ? wyw : 800));
			var wyh = args[3];
			wyh = Math.max((wyh ? wyh : 600));
			if (window.screen) {
				var screen_height = ((screen.height - 50) > screen.availHeight) ? screen.height - 50 : screen.availHeight;
				screen_height = screen_height - 40;
				var screen_width = screen.availWidth - 10;
				wyw = Math.min(screen_width, wyw);
				wyh = Math.min(screen_height, wyh);
			}
			// set new width & height
			url = url.replace(/we_cmd\[2\]=[^&]+/, 'we_cmd[2]=' + wyw);
			url = url.replace(/we_cmd\[3\]=[^&]+/, 'we_cmd[3]=' + (wyh - args[10]));
			new jsWindow(url, "we_wysiwygWin", -1, -1, Math.max(220, wyw + (document.all ? 0 : ((navigator.userAgent.toLowerCase().indexOf('safari') > -1) ? 20 : 4))), Math.max(100, wyh + 60), true, false, true);
			//doPostCmd(args,"we_wysiwygWin");
			break;
		case "not_installed_modules":
			we_repl(self.load, url, args[0]);
			break;
		case "start_multi_editor":
			we_repl(self.load, url, args[0]);
			break;
		case "customValidationService":
			new jsWindow(url, "we_customizeValidation", -1, -1, 700, 700, true, false, true);
			break;
		case "edit_home":
			if (args[1] === 'add') {
				self.load.location = '/webEdition/we/include/we_widgets/cmd.php?we_cmd[0]=' + args[1] + '&we_cmd[1]=' + args[2] + '&we_cmd[2]=' + args[3];
			}
			break;
		case "edit_navi":
			new jsWindow(url, "we_navieditor", -1, -1, 400, 360, true, true, true, true);
			break;
		case "initPlugin":
			weplugin_wait = new jsWindow("/webEdition/editors/content/eplugin/weplugin_wait.php?callback=" + args[1], "weplugin_wait", -1, -1, 300, 100, true, false, true);
			break;
		case "edit_settings_newsletter":
			new jsWindow(dirs.WE_MODULES_DIR + "newsletter/edit_newsletter_frameset.php?pnt=newsletter_settings", "newsletter_settings", -1, -1, 600, 750, true, false, true);
			break;
		case "edit_settings_customer":
			new jsWindow(dirs.WE_MODULES_DIR + "customer/edit_customer_frameset.php?pnt=settings", "customer_settings", -1, -1, 520, 300, true, false, true);
			break;
		case "edit_settings_shop":
			new jsWindow(dirs.WE_SHOP_MODULE_DIR + "edit_shop_pref.php", "shoppref", -1, -1, 470, 600, true, false, true);
			break;
		case "edit_settings_messaging":
			new jsWindow(dirs.WE_MESSAGING_MODULE_DIR + "messaging_settings.php?mode=1", "messaging_settings", -1, -1, 280, 200, true, false, true);
			break;
		case "edit_settings_spellchecker":
			we_cmd("spellchecker_edit");
			break;
		case "edit_settings_banner":
			we_cmd("banner_default");
			break;
		case "edit_settings_editor":
			if (top.plugin.editSettings) {
				top.plugin.editSettings();
			} else {
				we_cmd("initPlugin", "top.plugin.editSettings()");
			}
			break;
		case "edit_settings_glossary":
			we_cmd("glossary_settings");
			break;
		case "sysinfo":
			new jsWindow("/webEdition/we_cmd.php?we_cmd[0]=sysinfo", "we_sysinfo", -1, -1, 720, 660, true, false, true);
			break;
		case "showerrorlog":
			new jsWindow("/webEdition/errorlog.php", "we_errorlog", -1, -1, 920, 660, true, false, true);
			break;
		case "view_backuplog":
			new jsWindow("/webEdition/we_cmd.php?we_cmd[0]=backupLog", "we_backuplog", -1, -1, 720, 660, true, false, true);
			break;
		case "show_message_console":
			new jsWindow("/webEdition/we/include/jsMessageConsole/messageConsole.php", "we_jsMessageConsole", -1, -1, 600, 500, true, false, true, false);
			break;
		case "remove_from_editor_plugin":
			if (args[1] && top.plugin && top.plugin.remove) {
				top.plugin.remove(args[1]);
			}
			break;
		case "new":
			if (SEEMODE) {
				top.weEditorFrameController.openDocument(args[1], args[2], args[3], "", args[4], "", args[5]);

			} else {
				treeData.unselectnode();
				if (args[5] !== undefined) {
					top.weEditorFrameController.openDocument(args[1], args[2], args[3], "", args[4], "", args[5]);
				} else {
					top.weEditorFrameController.openDocument(args[1], args[2], args[3], "", args[4]);
				}
			}
			break;
		case "load":
			if (SEEMODE) {
				//	toggleBusy(1);
			} else {
				if (self.Tree) {
					if (self.Tree.setScrollY) {
						self.Tree.setScrollY();
					}
				}
				var tbl_prefix = tables.TBL_PREFIX,
								table = (args[1] !== undefined && args[1]) ? args[1] : 'tblFile';
				we_cmd("setTab", (tbl_prefix !== '' && table.indexOf(tbl_prefix) !== 0 ? tbl_prefix + table : table));
				//toggleBusy(1);
				we_repl(self.load, url, args[0]);
			}
			break;
		case "exit_delete":
		case "exit_move":
		case "exit_addToCollection":
			deleteMode = false;
			if (SEEMODE) {
			} else {
				treeData.setstate(treeData.tree_states.edit);
				drawTree();

				self.document.getElementById("bm_treeheaderDiv").style.height = "1px";
				self.document.getElementById("bm_mainDiv").style.top = "1px";
				top.setTreeWidth(widthBeforeDeleteMode);
				top.setSidebarWidth(widthBeforeDeleteModeSidebar);
			}
			break;
		case "delete":
			if (SEEMODE) {
				if (top.deleteMode != args[1]) {
					top.deleteMode = args[1];
				}
				if (args[2] != 1) {
					we_repl(top.weEditorFrameController.getActiveDocumentReference(), url, args[0]);
				}
			} else {
				if (top.deleteMode != args[1]) {
					top.deleteMode = args[1];
				}
				if (!top.deleteMode && treeData.state == treeData.tree_states.select) {
					treeData.setstate(treeData.tree_states.edit);
					drawTree();
				}
				self.document.getElementById("bm_treeheaderDiv").style.height = "150px";
				self.document.getElementById("bm_mainDiv").style.top = "150px";

				var width = top.getTreeWidth();

				widthBeforeDeleteMode = width;

				if (width < size.tree.deleteWidth) {
					top.setTreeWidth(size.tree.deleteWidth);
				}
				top.storeTreeWidth(widthBeforeDeleteMode);

				var widthSidebar = top.getSidebarWidth();

				widthBeforeDeleteModeSidebar = widthSidebar;

				if (args[2] != 1) {
					we_repl(self.treeheader, url, args[0]);
				}
			}
			break;
		case "move":
			if (SEEMODE) {
				if (top.deleteMode != args[1]) {
					top.deleteMode = args[1];
				}
				if (args[2] != 1) {
					we_repl(top.weEditorFrameController.getActiveDocumentReference(), url, args[0]);
				}
			} else {
				if (top.deleteMode != args[1]) {
					top.deleteMode = args[1];
				}
				if (!top.deleteMode && treeData.state == treeData.tree_states.selectitem) {
					treeData.setstate(treeData.tree_states.edit);
					drawTree();
				}
				self.document.getElementById("bm_treeheaderDiv").style.height = "160px";
				self.document.getElementById("bm_mainDiv").style.top = "160px";

				var width = top.getTreeWidth();

				widthBeforeDeleteMode = width;

				if (width < size.tree.moveWidth) {
					top.setTreeWidth(size.tree.moveWidth);
				}
				top.storeTreeWidth(widthBeforeDeleteMode);

				var widthSidebar = top.getSidebarWidth();

				widthBeforeDeleteModeSidebar = widthSidebar;

				if (args[2] != 1) {
					we_repl(self.treeheader, url, args[0]);
				}
			}
			break;
		case "addToCollection":
			if (SEEMODE) {
				//
			} else {
				if (top.deleteMode != args[1]) {
					top.deleteMode = args[1];
				}
				if (!top.deleteMode && treeData.state == treeData.tree_states.select) {
					treeData.setstate(treeData.tree_states.edit);
					drawTree();
				}
				self.document.getElementById("bm_treeheaderDiv").style.height = "205px";
				self.document.getElementById("bm_mainDiv").style.top = "205px";

				var width = top.getTreeWidth();
				widthBeforeDeleteMode = width;
				if (width < size.tree.moveWidth) {
					top.setTreeWidth(size.tree.moveWidth);
				}
				top.storeTreeWidth(widthBeforeDeleteMode);

				var widthSidebar = top.getSidebarWidth();
				widthBeforeDeleteModeSidebar = widthSidebar;

				if (args[2] != 1) {
					we_repl(self.treeheader, url, args[0]);
				}
			}
			break;
		case "reset_home":
			var _currEditor = top.weEditorFrameController.getActiveEditorFrame();
			if (_currEditor && _currEditor.getEditorType() === "cockpit") {
				if (confirm(g_l.cockpit_reset_settings)) {
					//FIXME: currently this doesn't work
					top.weEditorFrameController.getActiveDocumentReference().location = '/webEdition/we/include/we_widgets/cmd.php?we_cmd[0]=' + args[0];
					if ((window.treeData !== undefined) && treeData) {
						treeData.unselectnode();
					}
				}
			} else {
				top.we_showMessage(g_l.cockpit_not_activated, WE_MESSAGE_NOTICE, window);
			}
			break;

		case "new_widget_sct":
		case "new_widget_rss":
		case "new_widget_msg":
		case "new_widget_usr":
		case "new_widget_mfd":
		case "new_widget_upb":
		case "new_widget_mdc":
		case "new_widget_pad":
		case "new_widget_shp":
		case "new_widget_fdl":
			if (top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().quickstart) {
				top.weEditorFrameController.getActiveDocumentReference().createWidget(args[0].substr(args[0].length - 3), 1, 1);
			}
			else {
				top.we_showMessage(g_l.cockpit_not_activated, WE_MESSAGE_ERROR, window);
			}
			break;
		case "open_document":
			we_cmd("load", tables.FILE_TABLE);
			url = "/webEdition/we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[2]=" + tables.FILE_TABLE + "&we_cmd[5]=" + encodeURIComponent("opener.top.weEditorFrameController.openDocument(table,currentID,currentType)") + "&we_cmd[9]=1";
			new jsWindow(url, "we_dirChooser", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
			break;
		case "open_collection":
			we_cmd("load", tables.VFILE_TABLE);
			url = "/webEdition/we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[2]=" + tables.VFILE_TABLE + "&we_cmd[5]=" + encodeURIComponent("opener.top.weEditorFrameController.openDocument(table,currentID,currentType)") + "&we_cmd[9]=1";
			new jsWindow(url, "we_dirChooser", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
			break;
		case "edit_new_collection":
			url = "/webEdition/we_cmd.php?we_cmd[0]=editNewCollection&we_cmd[1]=" + args[1] + "&we_cmd[2]=" + args[2] + "&fixedpid=" + args[3] + "&fixedremtable=" + args[4];
			new jsWindow(url, "weNewCollection", -1, -1, 590, 560, true, true, true, true);
			break;
		case "help_documentation":
			new jsWindow("http://documentation.webedition.org/wiki/" + docuLang + "/", "help_documentation", -1, -1, 960, 700, true, true, true, true);
			break;

		case "help_tagreference":
			new jsWindow("http://tags.webedition.org/" + docuLang + "/", "help_tagreference", -1, -1, 960, 700, true, true, true, true);
			break;
		case "help_demo":
			new jsWindow("http://demo.webedition.org/" + docuLang + "/", "help_demo", -1, -1, 960, 700, true, true, true, true);
			break;
		case "open_tagreference":
			var docupath = "http://tags.webedition.org/" + docuLang + "/" + args[1];
			new jsWindow(docupath, "we_tagreference", -1, -1, 1024, 768, true, true, true);
			break;
		case "open_template":
			we_cmd("load", tables.TEMPLATES_TABLE);
			url = "/webEdition/we_cmd.php?we_cmd[0]=we_selector_document&we_cmd[8]=" + contentTypes.TEMPLATE + "&we_cmd[2]=" + tables.TEMPLATES_TABLE + "&we_cmd[5]=" + encodeURIComponent("opener.top.weEditorFrameController.openDocument(table,currentID,currentType)") + "&we_cmd[9]=1";
			new jsWindow(url, "we_dirChooser", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
			break;
		case "switch_edit_page":
			// get editor root frame of active tab
			var _currentEditorRootFrame = top.weEditorFrameController.getActiveDocumentReference();
			// get visible frame for displaying editor page
			var _visibleEditorFrame = top.weEditorFrameController.getVisibleEditorFrame();
			// frame where the form should be sent from
			var _sendFromFrame = _visibleEditorFrame;
			// set flag to true if active frame is frame nr 2 (frame for displaying editor page 1 with content editor)
			var _isEditpageContent = _visibleEditorFrame === _currentEditorRootFrame.frames[2];
			//var _isEditpageContent = _visibleEditorFrame == _currentEditorRootFrame.document.getElementsByTagName("div")[2].getElementsByTagName("iframe")[0];

			// if we switch from we_base_constants::WE_EDITPAGE_CONTENT to another page
			if (_isEditpageContent && args[1] !== constants.WE_EDITPAGE_CONTENT) {
				// clean body to avoid flickering
				try {
					_currentEditorRootFrame.frames[1].document.body.innerHTML = "";
				} catch (e) {
					//can be caused by not loaded content
				}
				// switch to normal frame
				top.weEditorFrameController.switchToNonContentEditor();
				// set var to new active editor frame
				_visibleEditorFrame = _currentEditorRootFrame.frames[1];
				//_visibleEditorFrame = _currentEditorRootFrame.document.getElementsByTagName("div")[1].getElementsByTagName("iframe")[0];

				// set flag to false
				_isEditpageContent = false;
				// if we switch to we_base_constants::WE_EDITPAGE_CONTENT from another page
			} else if (!_isEditpageContent && args[1] === constants.WE_EDITPAGE_CONTENT) {
				// switch to content editor frame
				top.weEditorFrameController.switchToContentEditor();
				// set var to new active editor frame
				_visibleEditorFrame = _currentEditorRootFrame.frames[2];
				//_visibleEditorFrame = _currentEditorRootFrame.document.getElementsByTagName("div")[2].getElementsByTagName("iframe")[0];
				// set flag to false
				_isEditpageContent = true;
			}

			// frame where the form should be sent to
			var _sendToFrame = _visibleEditorFrame;
			// get active transaction
			var _we_activeTransaction = top.weEditorFrameController.getActiveEditorFrame().getEditorTransaction();
			// if there are parameters, attach them to the url
			if (_currentEditorRootFrame.parameters) {
				url += _currentEditorRootFrame.parameters;
			}

			// focus the frame
			if (_sendToFrame) {
				_sendToFrame.focus();
			}
			// if visible frame equals to editpage content and there is already content loaded
			if (_isEditpageContent && _visibleEditorFrame.weIsTextEditor !== undefined && _currentEditorRootFrame.frames[2].location !== "about:blank") {
				// tell the backend the right edit page nr and break (don't send the form)
				//YAHOO.util.Connect.setForm(_sendFromFrame.document.we_form);
				YAHOO.util.Connect.asyncRequest('POST', "/webEdition/rpc/rpc.php", setPageNrCallback, 'protocol=json&cmd=SetPageNr&transaction=' + _we_activeTransaction + "&editPageNr=" + args[1]);
				if (_visibleEditorFrame.reloadContent === false) {
					break;
				}
				_visibleEditorFrame.reloadContent = false;
			}

			if (_currentEditorRootFrame) {

				if (!we_sbmtFrm(_sendToFrame, url, _sendFromFrame)) {
					// add we_transaction, if not set
					if (!args[2]) {
						args[2] = _we_activeTransaction;
					}
					url += "&we_transaction=" + args[2];
					we_repl(_sendToFrame, url, args[0]);
				}
			}

			break;
		default:
			return false;
	}
	return true;
}
