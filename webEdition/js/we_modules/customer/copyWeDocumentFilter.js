/* global WE */

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
'use strict';

var filter = WE().util.getDynamicVar(document, 'loadVarFilter', 'data-filter');



function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "closeChilds":
			var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse(),
							_openChilds = args[1];
			for (var i = 0; i < _openChilds.length; i++) {
				_usedEditors[_openChilds[i]].setEditorIsHot(false);
				WE().layout.weEditorFrameController.closeDocument(_openChilds[i]);
			}
			document.getElementById("iframeCopyWeDocumentCustomerFilter").src = filter.redirect;
			break;
		case "abortClose":
			window.close();
			break;
		default:
			window.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function checkForOpenChilds() {
	var
					_openChilds = [],
					_usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();

	for (var frameId in _usedEditors) {
		// table muss FILE_TABLE sein
		if (_usedEditors[frameId].getEditorEditorTable() === filter.table) {
			if (filter.allChilds[_usedEditors[frameId].getEditorDocumentId()] && filter.allChilds[_usedEditors[frameId].getEditorDocumentId()] === _usedEditors[frameId].getEditorContentType()) {
				_openChilds.push(frameId);
			}
		}
	}

	if (_openChilds.length) {
		WE().util.showConfirm(window, "", filter.question, ["closeChilds", _openChilds], ["abortClose"]);
		return;
	}
	document.getElementById("iframeCopyWeDocumentCustomerFilter").src = filter.redirect;
}