/* global WE, top, self*/

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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';

var doc = WE().util.getDynamicVar(document, 'loadVarEditor_footer', 'data-doc');
var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction(doc.we_transaction);

function we_submitForm(target, url) {
	var f = window.document.we_form;
	if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
	return true;
}

function put_in_workflow(table) {
	if (_EditorFrame.getEditorIsHot()) {
		WE().util.showConfirm(window, "", WE().consts.g_l.alert.in_wf_warning[table], ['save_document', '', '', '', '', 0, 0, 1]);
	} else {
		top.we_cmd('workflow_isIn', doc.we_transaction, (doc.makeSameDocCheck && _EditorFrame.getEditorMakeSameDoc() ? 1 : 0));
	}
}

function pass_workflow() {
	we_cmd('workflow_pass', doc.we_transaction);
}

function workflow_finish() {
	we_cmd('workflow_finish', doc.we_transaction);
}

function decline_workflow() {
	we_cmd('workflow_decline', doc.we_transaction);
}

function editFile() {
	if (top.plugin.editFile) {
		top.plugin.editFile();
	} else {
		we_cmd("initPlugin", "top.plugin.editFile();");
	}
}

function editSource() {
	if (top.plugin.editSource) {
		top.plugin.editSource(doc.editFilename, doc.contentType);
	} else {
		we_cmd("initPlugin", "top.plugin.editSource('" + doc.editFilename + "','" + doc.contentType + "')");
	}
}

function setTemplate() {
	window.document.we_form.autoRebuild.checked = (_EditorFrame.getEditorAutoRebuild() ? true : false);
	window.document.we_form.makeNewDoc.checked = (_EditorFrame.getEditorMakeNewDoc() ? true : false);
}

function setTextDocument(hasCtrl, value) {
	if (window.document.we_form && window.document.we_form.makeSameDoc) {
		if (hasCtrl) {
			window.document.we_form.makeSameDoc.checked = value;
			_EditorFrame.setEditorMakeSameDoc(value);
		} else if (doc.ID) {
			window.document.we_form.makeSameDoc.checked = false;
		} else if (_EditorFrame.getEditorMakeSameDoc()) {
			window.document.we_form.makeSameDoc.checked = true;
		} else {
			window.document.we_form.makeSameDoc.checked = false;
		}
	}
}

function setPath() {
	WE().layout.we_setPath(_EditorFrame, doc.Path, doc.Text, doc.ID, doc.classname);
}

function saveReload() {
	window.location = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=load_edit_footer&we_transaction=' + doc.we_transaction;
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "glossary_check":
			new (WE().util.jsWindow)(window, url, "glossary_check", WE().consts.size.dialog.medium, WE().consts.size.dialog.smaller, true, false, true);
			break;
		case "save_document":
			args[1] = doc.we_transaction;
			args[2] = 0;

			if (doc.isTemplate) {
				if (doc.isFolder) {
					args[3] = 1;
					args[4] = (doc.makeSameDocCheck && _EditorFrame.getEditorMakeSameDoc() ? 1 : 0);
				} else {
					args[3] = 0;
				}
			} else {
				args[3] = 1;
				args[4] = (doc.makeSameDocCheck && _EditorFrame.getEditorMakeSameDoc() ? 1 : 0);
			}
			top.we_cmd.apply(window, args);
			break;
		case "object_obj_search":
			top.we_cmd("object_obj_search", doc.we_transaction, document.we_form.obj_search.value, document.we_form.obj_searchField[document.we_form.obj_searchField.selectedIndex].value);
			break;
		default:
			if (top.we_cmd) {
				top.we_cmd.apply(window, Array.prototype.slice.call(arguments));
			}
	}
}

function we_footerLoaded() {
	if (doc.isTemplate && !doc.isFolder) {
		setTemplate();
	}
	switch (doc.ctrlElem) {
		case 1:
			setTextDocument(true, true);
			break;
		case 2:
			setTextDocument(true, false);
			break;
		case 3:
			setTextDocument(false);
			break;
	}
	setPath();
}

function we_save_document(nextCmd) {
	var countSaveLoop = 0;
	try {
		var contentEditor = WE().layout.weEditorFrameController.getVisibleEditorFrame();
		if (contentEditor && contentEditor.fields_are_valid && !contentEditor.fields_are_valid()) {
			return;

		}
	} catch (e) {
		// Nothing
	}
	nextCmd = nextCmd ? nextCmd : "";
	/*if (_EditorFrame.getEditorPublishWhenSave() && doc._showGlossaryCheck) {
	 we_cmd('glossary_check', '', doc.we_transaction);
	 } else */{
		//if window.parent.frames[1].$ is not existent, the frame was not loaded
		var acStatus = window.parent.frames[1].$ ? WE().layout.weSuggest.checkRequired(window.parent.frames[1]) : {'running': false, 'valid': true};

		if (countSaveLoop > 10 || !acStatus.valid) {
			top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			countSaveLoop = 0;
		} else if (acStatus.running) {
			countSaveLoop++;
			window.setTimeout(we_save_document, 100, nextCmd);
		} else {
			countSaveLoop = 0;
			if (doc.weCanSave) {
				var we_cmd_args = ["save_document", "", "", "", "", doc.pass_publish ? window.btoa(JSON.stringify(doc.pass_publish)) : '', nextCmd];
				if (doc.isBinary) {
					WE().layout.checkFileUpload(we_cmd_args);
				} else {
					we_cmd.apply(window, we_cmd_args);
				}
				if (doc.reloadOnSave) {
					window.setTimeout(saveReload, 1500);
				}
			}
		}
	}
}

var we_editor_footer = {
	timeout: null,
	evtCounter: 0,
	dragEnter: function () {
		++this.evtCounter;
		this.scrollDownEditorContent();
	},
	dragLeave: function () {
		if (--this.evtCounter === 0) {
			clearTimeout(this.timeout);
		}
	},
	scrollDownEditorContent: function () {
		_EditorFrame.getContentEditor().scrollBy(0, 10);
		if (this.evtCounter) {
			this.timeout = setTimeout(we_editor_footer.scrollDownEditorContent, 66);
		}
	}
};
