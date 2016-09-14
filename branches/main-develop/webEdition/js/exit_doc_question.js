/* global WE, top */

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

var editorSave = WE().util.getDynamicVar(document, 'loadVarExit_doc_question', 'data-editorSave');
var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrame(editorSave.editorFrameId);

function pressed_cancel() {
	window_closed();
	self.close();
}

function window_closed() {
	_EditorFrame.EditorExitDocQuestionDialog = false;
}

// functions for keyBoard Listener
function applyOnEnter() {
	pressed_yes();
}

// functions for keyBoard Listener
function closeOnEscape() {
	pressed_cancel();
}

function pressed_yes() {
	//FIXME: eval in timeout
	//FIXME:we_save_document(XX) xx will be we_cmd[6]!
	_EditorFrame.getDocumentReference().frames.editFooter.we_save_document("WE().layout.weEditorFrameController.closeDocument('" + editorSave.editorFrameId + "');" + (editorSave.nextCmd ? "top.setTimeout('" + editorSave.nextCmd + "', 1000);" : ""));
	window_closed();
	self.close();
}

function pressed_no() {
	_EditorFrame.setEditorIsHot(false);
	WE().layout.weEditorFrameController.closeDocument(editorSave.editorFrameId);
	if (editorSave.nextCmd) {
		//FIXME: eval
		opener.top.setTimeout(editorSave.nextCmd, 1000);
	}
	window_closed();
	self.close();
}
