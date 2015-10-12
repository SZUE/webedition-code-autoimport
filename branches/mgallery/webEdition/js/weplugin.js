/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

var code;
var to;
var isLodaed = false;
var c = 0;
var wait_count = 0;
var wait_retry = 40;

function setIsLoaded(flag) {
	self.isLoaded = flag;
}

function editSettings() {
	if (self.isLoaded) {
		document.WePlugin.editSettings();
	}
}

function editSource(filename, ct, charset) {
	var _EditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();
	var source = "###EDITORPLUGIN:EMPTYSTRING###";
	if (_EditorFrame.getContentEditor().getSource) {
		source = _EditorFrame.getContentEditor().getSource();
		document.we_form.acceptCharset = _EditorFrame.getContentEditor().getCharset();
	}

	document.we_form.elements['we_cmd[0]'].value = "editSource";
	document.we_form.elements['we_cmd[1]'].value = filename;
	document.we_form.elements['we_cmd[2]'].value = _EditorFrame.getEditorTransaction();
	document.we_form.elements['we_cmd[3]'].value = ct;
	document.we_form.elements['we_cmd[4]'].value = source;

	document.we_form.submit();
}

function editFile() {
	var _EditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();
	document.we_form.elements['we_cmd[0]'].value = "editFile";
	document.we_form.elements['we_cmd[1]'].value = _EditorFrame.getEditorTransaction();
	document.we_form.submit();
}

function setSource(trans) {
	var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction(trans);
	if (_EditorFrame) {
		_EditorFrame.setEditorIsHot(true);
		var source = (self.isLoaded) ? document.WePlugin.getSource(trans).replace(/\r?\n?$/, "") : "";
		if (_EditorFrame && _EditorFrame.getContentEditor().setSource) {
			_EditorFrame.getContentEditor().setSource(source);
		} else {
			document.we_form.elements['we_cmd[0]'].value = "setSource";
			document.we_form.elements['we_cmd[1]'].value = trans;
			document.we_form.elements['we_cmd[2]'].value = source;

			document.we_form.submit();
		}
	}
}

function setFile(source, trans) {
	document.we_form.elements['we_cmd[0]'].value = "setFile";
	document.we_form.elements['we_cmd[1]'].value = trans;
	document.we_form.elements['we_cmd[2]'].value = source;
	document.we_form.submit();
}



function reloadContentFrame(trans) {
	document.we_form.elements['we_cmd[0]'].value = "reloadContentFrame";
	document.we_form.elements['we_cmd[1]'].value = trans;
	document.we_form.submit();
}

function remove(transaction) {
	if (self.isLoaded && (typeof document.WePlugin.removeDocument == "function")) {
		document.WePlugin.removeDocument(transaction);
	} else {
		self.isLoaded = false;
	}
}

function isInEditor(transaction) {
	if (self.isLoaded && transaction !== null && (typeof document.WePlugin.inEditor == "function")) {
		return document.WePlugin.inEditor(transaction);
	}
	return false;
}

function getDocCount() {
	if (self.isLoaded) {
		return document.WePlugin.getDocCount();
	}
	return 1;
}

function pingPlugin() {
	if (document.WePlugin && self.isLoaded) {
		c++;
		if (document.WePlugin.hasMessages) {
			if (document.WePlugin.hasMessages()) {
				var messages = document.WePlugin.getMessages();
				eval("" + messages);

			}
		}

	}
	//to = window.setTimeout(pingPlugin(), 1000);
}

function initPlugin() {
	top.opener.top.plugin.location = "/webEdition/editors/content/eplugin/weplugin.php";
	self.focus();
	checkPlugin();
}

function nojava() {
	alert(g_l.no_java);
	top.opener.top.plugin.location = "about:blank";
	self.close();
}

function checkPlugin() {
	if (top.opener.top.plugin.isLoaded && top.opener.top.plugin.document.WePlugin !== undefined) {
		if (top.opener.top.plugin.document.WePlugin.isLive !== undefined) {
			if (callBack) {
				eval("top.opener." + callBack);
			}
			self.close();
		} else {
			nojava();
		}
	} else {
		wait_count++;
		if (wait_count < wait_retry) {
			setTimeout(checkPlugin, 1000);
		} else {
			nojava();
		}
	}
}
