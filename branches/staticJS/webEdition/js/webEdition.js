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
				win.alert(we_string_message_reporting_notice + ":\n" + message);
				break;

				// Warning
			case WE_MESSAGE_WARNING:
				win.alert(we_string_message_reporting_warning + ":\n" + message);
				break;

				// Error
			case WE_MESSAGE_ERROR:
				win.alert(we_string_message_reporting_error + ":\n" + message);
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
	if (!fenster.top.opener || /*fenster.top.opener.win || FIXME: what is win??*/ fenster.top.opener.closed) {
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

var oldTreeWidth = size.tree.default;
function toggleTree() {
	var tfd = self.rframe.document.getElementById("treeFrameDiv");
	var w = top.getTreeWidth();

	if (tfd.style.display == "none") {
		oldTreeWidth = (oldTreeWidth < size.tree.min ? size.tree.default : oldTreeWidth);
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

function getTreeWidth() {
	var w = self.rframe.document.getElementById("bframeDiv").style.width;
	return w.substr(0, w.length - 2);
}

function getSidebarWidth() {
	var obj = self.rframe.document.getElementById("sidebarDiv");
	if (obj === undefined || obj === null) {
		return 0;
	}
	var w = obj.style.left;
	return w.substr(0, w.length - 2);
}

function setSidebarWidth() {
	var obj = self.rframe.document.getElementById("sidebarDiv");
	if (obj !== undefined && obj !== null) {
		obj.style.left = w + "px";
	}
}

function setTreeWidth(w) {
	self.rframe.document.getElementById("bframeDiv").style.width = w + "px";
	self.rframe.document.getElementById("bm_content_frameDiv").style.left = w + "px";
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
	setTimeout("self.makefocus.focus();self.makefocus=null;", 200);
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
	if (typeof (source) === "undefined") {
		source = top.weEditorFrameController.getVisibleEditorFrame();
	}
	return submit_we_form(source, target, url);

}

function we_sbmtFrmC(target, url) {
	return submit_we_form(top.weEditorFrameController.getActiveDocumentReference(), target, url);
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

	var hiddens = new Array();
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
	self.Tree = self.rframe;
	self.Vtabs = self.rframe;
	self.TreeInfo = self.rframe;
	if (table_to_load) {
		we_cmd("load", table_to_load);
	}
}