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
var unlock = false;
var scrollToVal = 0;
var editorScrollPosTop = 0;
var editorScrollPosLeft = 0;
var weAutoCompetionFields = [];
var openedInEditor = true;
//	SEEM
//	With this var we can see, if the document is opened via webEdition
//	or just opened in the bm_content Frame, p.ex javascript location.replace or reload or sthg..
//	we must check, if the tab is switched ... etc.
var openedWithWE = true;
var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrame(window.name);


function we_cmd() {
	if (!unlock) {
		//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);

		if (top.we_cmd) {
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
		}
	}
}

function closeAllModalWindows() {
	try {
		var _editor1 = self.frames[1];
		var _editor2 = self.frames[2];
		WE().util.jsWindow.prototype.closeAll(_editor1);
		WE().util.jsWindow.prototype.closeAll(_editor2);
	} catch (e) {

	}
}

function setOpenedWithWE(val) {
	openedWithWE = val;
}

function checkDocument() {
	loc = null;
	try {
		loc = editor.location;
	} catch (e) {
	}

	_EditorFrame.setEditorIsHot(false);

	if (loc) {	//	Page is on webEdition-Server, open it with matching command
		// close existing editor, it was closed very hard
		WE().layout.weEditorFrameController.closeDocument(_EditorFrame.getFrameId());

		// build command for this location
		top.we_cmd("open_url_in_editor", loc);

	} else {	//	Page is not known - replace top and bottom frame of editor
		//	Fill upper and lower Frame with white
		//	If the document is editable with webedition, it will be replaced
		//	Location not known - empty top and footer

		//	close window, when in seeMode include window.
		if (SEEM_edit_include) {
			WE().util.showMessage(WE().consts.g_l.main.close_include, WE().consts.message.WE_MESSAGE_ERROR, window);
			top.close();
		} else {
			_EditorFrame.initEditorFrameData({
				"EditorType": "none_webedition",
				"EditorContentType": "none_webedition",
				"EditorDocumentText": "Unknown",
				"EditorDocumentPath": "Unknown"
			});

			editHeader.location = "about:blank";
			editFooter.location = WE().consts.dirs.WE_INCLUDES_DIR + "we_seem/we_SEEM_openExtDoc_footer.php' ?>";

		}
	}
}

function doUnload() {
	try {
		closeAllModalWindows();
		if (USERACCESS) {
			if (!unlock && (!top.opener || top.opener.win)) {	//	login to super easy edit mode
				unlock = true;
			}
		}
	} catch (e) {
	}
}


function edit_framesetStart(Text, Path, Table, Id, Transaction, ContentType, Parameters) {
	_EditorFrame.initEditorFrameData({
		EditorType: "model",
		EditorDocumentText: Text,
		EditorDocumentPath: Path,
		EditorEditorTable: Table,
		EditorDocumentId: Id,
		EditorTransaction: Transaction,
		EditorContentType: ContentType,
		EditorDocumentParameters: Parameters
	});
	if (!_EditorFrame.EditorDocumentId) {
		if (top.treeData && top.treeData.table != _EditorFrame.EditorEditorTable) {
			top.we_cmd('load', _EditorFrame.EditorEditorTable);
		}
	}

	if (top.treeData && (top.treeData.state == top.treeData.tree_states["select"] || top.treeData.state == top.treeData.tree_states["selectitem"])) {
		top.we_cmd("exit_delete");
	}

}

function setOnload() {
	if (top.edit_include) {
		top.edit_include.close();
	}
	if (openedWithWE === false) {
		checkDocument();
	}
	openedWithWE=false;
}