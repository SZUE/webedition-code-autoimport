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
/* global WE, top */
'use strict';

var editorSave = WE().util.getDynamicVar(document, 'loadVarEditor_save', 'data-editorSave');

var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction(editorSave.we_transaction);
var _EditorFrameDocumentRef = _EditorFrame.getDocumentReference();
var isEditInclude = false;
var weWindow = top;
var i;

while (1) {
	if (!weWindow.top.opener || weWindow.treeData) {
		break;
	} else {
		isEditInclude = true;
		weWindow = weWindow.opener.top;
	}
}

if (editorSave.we_editor_save) {//called from we_editor_save.inc.php

	if (editorSave.isHot) {
		_EditorFrame.setEditorIsHot(true);
	}

	if (editorSave.wasSaved) {
		// DOC was saved, mark open tabs to reload if necessary
		// was saved - not hot anymore

		_EditorFrame.setEditorIsHot(false);
		if (_EditorFrame.getContentFrame().refreshContentCompare !== undefined) {
			_EditorFrame.getContentFrame().refreshContentCompare();
		}
		if (editorSave.reloadEditors) {
			WE().layout.reloadUsedEditors(editorSave.reloadEditors);
		}
		if (!editorSave.isSEEMode) {
			WE().layout.makeNewDoc(_EditorFrame, editorSave.ContentType, editorSave.docID, editorSave.saveTmpl, !editorSave.isClose);
		}
	}

	for (i = 0; i < editorSave.we_JavaScript.length; i++) {
		we_cmd.apply(window, editorSave.we_JavaScript[i]);
	}

	window.focus();
	var showAlert = false;
	var contentEditor = WE().layout.weEditorFrameController.getVisibleEditorFrame();

// enable navigation box if doc has been published
	if (editorSave.wasPublished) {
		try {
			if (_EditorFrame && _EditorFrame.getEditorIsInUse() && contentEditor) {
				WE().layout.button.switch_button_state(contentEditor.document, "add", "enabled");
			}
		} catch (e) {
		}
	}

	if (editorSave.isSEEMode && !editorSave.showAlert) {
		//	Confirm Box or alert in seeMode
		if (editorSave.isPublished) {
			//	edit include and pulish then close window and reload
			if (isEditInclude) {
				showAlert = true;
			}
			if (editorSave.docHasPreview && editorSave.EditPageNr != WE().consts.tabs.PREVIEW) {
				if (!showAlert) { //	alert or confirm
					if (window.confirm(editorSave.we_responseText + "\n\n" + WE().consts.g_l.alert.confirm_change_to_preview)) {
						_EditorFrameDocumentRef.frames.editHeader.we_cmd('switch_edit_page', WE().consts.tabs.PREVIEW, editorSave.we_transaction);
					} else {
						_EditorFrameDocumentRef.frames.editHeader.we_cmd('switch_edit_page', editorSave.EditPageNr, editorSave.we_transaction);
					}
				} else {
					top.we_showMessage(editorSave.we_responseText, editorSave.we_responseTextType, window);
				}
			} else {
				//	alert when in preview mode
				top.we_showMessage(editorSave.we_responseText, editorSave.we_responseTextType, window);
				_EditorFrameDocumentRef.frames.editHeader.we_cmd('switch_edit_page', editorSave.EditPageNr, editorSave.we_transaction);

				for (i = 0; i < editorSave.we_responseJS.length; i++) {
					we_cmd.apply(window, editorSave.we_responseJS[i]);
				}
				for (i = 0; i < editorSave.we_cmd5.length; i++) {
					we_cmd.apply(window, editorSave.we_cmd5[i]);
				}
			}
			if (editorSave.isPublished) {
				if (isEditInclude) {
					top.we_showMessage(WE().consts.g_l.alert.changed_include, WE().consts.message.WE_MESSAGE_NOTICE, window);
					weWindow.top.we_cmd("reload_editpage");
					weWindow.edit_include.close();
					top.close();
				}
			}
		}
	} else {
		top.we_showMessage(editorSave.we_responseText, editorSave.we_responseTextType, window);
		for (i = 0; i < editorSave.we_responseJS.length; i++) {
			we_cmd.apply(window, editorSave.we_responseJS[i]);
		}

		for (i = 0; i < editorSave.we_cmd5.length; i++) {
			we_cmd.apply(window, editorSave.we_cmd5[i]);
		}
	}
} else {//called from we_editor_publish.inc.php
	for (i = 0; i < editorSave.we_responseJS.length; i++) {
		we_cmd.apply(window, editorSave.we_responseJS[i]);
	}
	for (i = 0; i < editorSave.we_cmd5.length; i++) {
		we_cmd.apply(window, editorSave.we_cmd5[i]);
	}
	for (i = 0; i < editorSave.we_JavaScript.length; i++) {
		we_cmd.apply(window, editorSave.we_JavaScript[i]);
	}
	top.we_showMessage(editorSave.we_responseText, editorSave.we_responseTextType, window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);
	var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction(editorSave.we_transaction);

	switch (args[0]) {
		case 'setEditorDocumentId':
			_EditorFrame.setEditorDocumentId(args[1]);
			break;
		case "we_setPath":
			WE().layout.we_setPath(_EditorFrame, args[1], args[2], args[3], args[4]);
			break;
		case 'unsetHot':
			_EditorFrame.setEditorIsHot(false);
			break;
		case 'addHistory':
			WE().layout.weNavigationHistory.addDocToHistory(args[1], args[2], args[3]);
			break;
		case 'rebuildTemplates':
			new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_filter&templateID=' + args[1] + '&responseText=' + args[2], 'resave', WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, 0, true);
			break;
		case 'updateVTab':
			if (top.treeData.table == args[1]) {
				we_cmd('loadVTab', top.treeData.table, 0);
			}
			break;
		case 'updateMenu':
			document.getElementById("nav").parentNode.innerHTML = args[1];
			break;
		default:
			top.we_cmd.apply(window, args);
	}
}