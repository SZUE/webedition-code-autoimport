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

function we_submitForm(target, url) {
	var f = self.document.we_form;
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
}

function put_in_workflow() {
	if (_EditorFrame.getEditorIsHot()) {
		if (confirm(g_l.in_wf_warning)) {
			we_cmd('save_document', '', '', '', '', 0, 0, 1);
		}
	} else {
		top.we_cmd('workflow_isIn', we_transaction, (doc.makeSameDocCheck && _EditorFrame.getEditorMakeSameDoc() ? 1 : 0));
	}
}

function pass_workflow() {
	we_cmd('workflow_pass', we_transaction);
}

function workflow_finish() {
	we_cmd('workflow_finish', we_transaction);
}

function decline_workflow() {
	we_cmd('workflow_decline', we_transaction);
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
		we_cmd("initPlugin", "top.plugin.editSource(\"" + doc.editFilename + "\",\"" + doc.contentType + "\")");
	}
}

function setTemplate() {
	if (_EditorFrame.getEditorAutoRebuild()) {
		self.document.we_form.autoRebuild.checked = true;
	} else {
		self.document.we_form.autoRebuild.checked = false;
	}
	if (_EditorFrame.getEditorMakeNewDoc()) {
		self.document.we_form.makeNewDoc.checked = true;
	} else {
		self.document.we_form.makeNewDoc.checked = false;
	}
}

function setTextDocument(hasCtrl, value) {
	if (self.document.we_form && self.document.we_form.makeSameDoc) {
		if (hasCtrl) {
			self.document.we_form.makeSameDoc.checked = value;
			_EditorFrame.setEditorMakeSameDoc(value);
		} else if (doc.ID) {
			self.document.we_form.makeSameDoc.checked = false;
		} else if (_EditorFrame.getEditorMakeSameDoc()) {
			self.document.we_form.makeSameDoc.checked = true;
		} else {
			self.document.we_form.makeSameDoc.checked = false;
		}
	}
}

function setPath() {
	try {
		top._EditorFrame.getDocumentReference().frames.editHeader.we_setPath(doc.Path, doc.Text, doc.ID);
	} catch (e) {
	}
}

function saveReload() {
	self.location = '/webEdition/we_cmd.php?we_cmd[0]=load_edit_footer&we_transaction=' + we_transaction;
}

function we_cmd() {
	var url = '/webEdition/we_cmd.php?';
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (arguments[0]) {
		case "glossary_check":
			new jsWindow(url, "glossary_check", -1, -1, 730, 400, true, false, true);
			return;
		case "save_document":
			if (doc.isTemplate) {
				if (doc.isFolder) {
					top.we_cmd("save_document", we_transaction, 0, 1, "", "", arguments[6] ? arguments[6] : "", arguments[7] ? arguments[7] : "");
				} else {
					top.we_cmd("save_document", we_transaction, 0, 0, "", arguments[5] ? arguments[5] : "", arguments[6] ? arguments[6] : "", arguments[7] ? arguments[7] : "");
				}
			} else {
				top.we_cmd("save_document", we_transaction, 0, 1, (doc.makeSameDocCheck && _EditorFrame.getEditorMakeSameDoc() ? 1 : 0), arguments[5] ? arguments[5] : "", arguments[6] ? arguments[6] : "", arguments[7] ? arguments[7] : "");
			}
			return;
		case "object_obj_search":
			top.we_cmd("object_obj_search", we_transaction, document.we_form.obj_search.value, document.we_form.obj_searchField[document.we_form.obj_searchField.selectedIndex].value);
			return;
	}
	var args = [];
	for (i = 0; i < arguments.length; i++)
	{
		args.push(arguments[i]);
	}
	if (top.we_cmd) {
		top.we_cmd.apply(this, args);
	}
}


function we_save_document() {
	var countSaveLoop = 0;
	try {
		var contentEditor = top.weEditorFrameController.getVisibleEditorFrame();
		if (contentEditor && contentEditor.fields_are_valid && !contentEditor.fields_are_valid()) {
			return;

		}
	}
	catch (e) {
		// Nothing
	}

	if (_EditorFrame.getEditorPublishWhenSave() && _showGlossaryCheck) {
		we_cmd('glossary_check', '', we_transaction);
	} else {
		acStatus = '';
		invalidAcFields = false;
		try {
			if (parent && parent.frames[1] && parent.frames[1].YAHOO && parent.frames[1].YAHOO.autocoml) {
				acStatus = parent.frames[1].YAHOO.autocoml.checkACFields();
			}
		}
		catch (e) {
			// Nothing
		}
		acStatusType = typeof acStatus;
		if (parent && parent.weAutoCompetionFields && parent.weAutoCompetionFields.length > 0) {
			for (i = 0; i < parent.weAutoCompetionFields.length; i++) {
				if (parent.weAutoCompetionFields[i] && parent.weAutoCompetionFields[i].id && !parent.weAutoCompetionFields[i].valid) {
					invalidAcFields = true;
					break;
				}
			}
		}
		if (countSaveLoop > 10) {
			top.we_showMessage(g_l.save_error_fields_value_not_valid, WE_MESSAGE_ERROR, window);
			countSaveLoop = 0;
		} else if (acStatusType.toLowerCase() == 'object' && acStatus.running) {
			countSaveLoop++;
			setTimeout(we_save_document, 100);
		} else if (invalidAcFields) {
			top.we_showMessage(g_l.save_error_fields_value_not_valid, WE_MESSAGE_ERROR, window);
			countSaveLoop = 0;
		} else {
			countSaveLoop = 0;
			generatedSaveDoc();
		}
	}
}