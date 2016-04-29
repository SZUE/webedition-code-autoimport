/* global top, WE */

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

function setHotDocuments() {
	var allHotDocuments = WE().layout.weEditorFrameController.getEditorsInUse();
	var _hotDocumentsOfCt = {};
	var ct;
	for (var frameId in allHotDocuments) {
		ct = allHotDocuments[frameId].getEditorContentType();
		if (!_hotDocumentsOfCt[ct]) {
			_hotDocumentsOfCt[ct] = [];
		}
		_hotDocumentsOfCt[ct].push(allHotDocuments[frameId]);
	}

	for (var ct in _hotDocumentsOfCt) {
		var liCtElem = document.createElement("li");
		liCtElem.innerHTML = ctLngs[ct];

		var ulCtElem = document.createElement("ul");
		for (var i = 0; i < _hotDocumentsOfCt[ct].length; i++) {

			var liPathElem = document.createElement("li");

			if (_hotDocumentsOfCt[ct][i].getEditorDocumentText()) {
				liPathElem.innerHTML = _hotDocumentsOfCt[ct][i].getEditorDocumentPath();
			} else {
				liPathElem.innerHTML = "<em>" + WE().consts.g_l.main.untitled + "</em>";
			}

			ulCtElem.appendChild(liPathElem);
		}
		liCtElem.appendChild(ulCtElem);
		document.getElementById("ulHotDocuments").appendChild(liCtElem);
	}
}

function yes_cmd_pressed() {
	var allHotDocuments = WE().layout.weEditorFrameController.getEditorsInUse();
	for (var frameId in allHotDocuments) {
		if (allHotDocuments[frameId].getEditorIsHot()) {
			allHotDocuments[frameId].setEditorIsHot(false);
		}
	}
	top.opener.top.we_cmd(nextCmd);
	self.close();
}
