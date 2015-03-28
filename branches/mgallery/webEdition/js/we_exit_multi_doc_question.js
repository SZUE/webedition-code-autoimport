/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 9455 $
 * $Author: mokraemer $
 * $Date: 2015-03-02 19:17:04 +0100 (Mo, 02. Mär 2015) $
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

function setHotDocuments() {
	var allHotDocuments = top.opener.top.weEditorFrameController.getEditorsInUse();
	var liStr = "";
	var _hotDocumentsOfCt = new Object();
	for (frameId in allHotDocuments) {
		if (allHotDocuments[frameId].getEditorIsHot()) {
			if (!_hotDocumentsOfCt[allHotDocuments[frameId].getEditorContentType()]) {
				_hotDocumentsOfCt[allHotDocuments[frameId].getEditorContentType()] = new Array();
			}
			_hotDocumentsOfCt[allHotDocuments[frameId].getEditorContentType()].push(allHotDocuments[frameId]);
		}
	}

	for (ct in _hotDocumentsOfCt) {
		var liCtElem = document.createElement("li");
		liCtElem.innerHTML = ctLngs[ct];

		var ulCtElem = document.createElement("ul");
		for (var i = 0; i < _hotDocumentsOfCt[ct].length; i++) {

			var liPathElem = document.createElement("li");

			if (_hotDocumentsOfCt[ct][i].getEditorDocumentText()) {
				liPathElem.innerHTML = _hotDocumentsOfCt[ct][i].getEditorDocumentPath();
			} else {
				liPathElem.innerHTML = "<em>" + g_l.untitled + "</em>";
			}

			ulCtElem.appendChild(liPathElem);
		}
		liCtElem.appendChild(ulCtElem);
		document.getElementById("ulHotDocuments").appendChild(liCtElem);
	}
}

function yes_cmd_pressed() {
	var allHotDocuments = top.opener.top.weEditorFrameController.getEditorsInUse();
	for (frameId in allHotDocuments) {
		if (allHotDocuments[frameId].getEditorIsHot()) {
			allHotDocuments[frameId].setEditorIsHot(false);
		}
	}
	top.opener.top.we_cmd(nextCmd);
	self.close();
}
