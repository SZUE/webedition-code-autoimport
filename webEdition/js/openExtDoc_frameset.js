/* global WE, top */

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
var extdoc = WE().util.getDynamicVar(document, 'loadVarExtDoc', 'data-extdoc');
var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrame(window.name);
_EditorFrame.initEditorFrameData(extdoc.frameData);

function checkDocument() {
	var loc = null;

	try {
		loc = top.extDocContent.location;
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

		_EditorFrame.initEditorFrameData({
			EditorType: "none_webedition",
			EditorContentType: "none_webedition",
			EditorDocumentText: "Unknown",
			EditorDocumentPath: "Unknown"
		});

		top.extDocHeader.location = "about:blank";
		top.extDocFooter.location = WE().consts.dirs.WEBEDITION_DIR + "we/include/we_seem/we_SEEM_openExtDoc_footer.php";
	}
}
